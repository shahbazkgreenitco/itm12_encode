<?php

namespace App\Http\Controllers;

use App\Exports\Accessories\AccessoriesExport;
use App\Exports\Accessories\AccessoryInfoExport;
use App\Exports\Accessories\CustomFieldsAccessories;
use App\Helpers\Common as CommonHelper;
use App\Http\Controllers\Controller;
use App\Imports\Accessories\AccessoryImport;
use App\Mail\Accessories\AccessoryAddNotification;
use App\Mail\Accessories\AccessoryCheckinNotification;
use App\Mail\Accessories\AccessoryCheckoutNotification;
use App\Mail\Accessories\AccessoryScrap;
use App\Mail\Accessories\AccessoryScrapRevert;
use App\Mail\Accessories\AccessoryThreshouldNotification;
use App\Mail\Accessories\ThreshouldNotification;
use App\Models\Accessory;
use App\Models\AccessoryPurchase;
use App\Models\AccessoryUser;
use App\Models\Actionlog;
use App\Models\Category;
use App\Models\Company;
use App\Models\Currency;
use App\Models\CustomField;
use App\Models\CustomFieldset;
use App\Models\Department;
use App\Models\Device;
use App\Models\Location;
use App\Models\Manufacture;
use App\Models\Place;
use App\Models\Purchase;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\Threshold;
use App\Models\ThresholdAlertSettings;
use App\Models\ThresholdSettings;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Log;
use Maatwebsite\Excel\Facades\Excel;
// use Image;
use Mail;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Validator;
use Spatie\LaravelPdf\Facades\Pdf as PDF;
use App\Models\AccessoryCache;
use App\Models\AccessoryRecord;

class AccessoryController extends Controller
{

    public function getIndex(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('AccessoriesRead') || ! config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $accessoryfilter = null;
        if (isset($request->q)) {
            $accessoryfilter = $request->q;
        }
        $compObj = Company::select("id", "name as text");
        // get filter value from dashboard filter
        $location   = "null";
        $department = isset($request->department) ? $request->department : "null";
        if ($request->location != null && $request->location != "null") {
            $location = $request->location;
        } else if ($request->city != null && $request->city != "null") {
            $id       = Location::whereIn('city_id', [$request->city])->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        } else if ($request->states != null && $request->states != "null") {
            $id       = Location::whereIn('state_id', [$request->states])->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        } else if ($request->zone != null && $request->zone != "null") {
            $id       = Location::whereIn('zone', [$request->zone])->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        } else if ($request->country != null && $request->country != "null") {
            $id       = Location::whereIn('country_id', [$request->country])->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        }
        $type = isset($request->type) ? $request->type : "null";
        if (isset($request->cat_name)) {
            $catId  = Category::where('name', $request->cat_name)->select('id')->first();
            $cat_id = isset($catId->id) ? $catId->id : "null";
        } elseif (isset($request->category)) {
            $cat_id = $request->category;
        } else {
            $cat_id = "null";
        }
        if (! Auth::user()->isSuperUser() && ! Settings::getSettings()->full_multiple_companies_support) {
            $compObj->where("id", "=", Auth::user()->company_id);
        }
        $companies    = $compObj->get()->toArray();
        $manufacturer = Manufacture::get();

        $categories             = Category::whereNull("deleted_at")->select("id", "name as text")->where("category_type", "accessory")->get()->toArray();
        $currencies             = Currency::getCurrencies();
        $vd                     = new stdClass();
        $vd->places             = Place::selectOptions();
        $vd->internal_places    = Place::select('id', 'place as text')->orderBy('place')->get();
        $vd->location           = Location::select("id", "name")->get();
        $vd->assignedForOptions = Accessory::getAssignedForOptions();
        $companyFieldset        = CustomFieldset::where('id', Settings::first()->accessories_custom_fieldset_id)->first();
        $requestable_enabled    = config('app.requestable_enabled');
        return view("accessories.index")->with(compact("accessoryfilter", "companies", "categories", "currencies", "location", "companyFieldset", "department", "requestable_enabled", "manufacturer", "cat_id", "type"))->with('vd', $vd)->with('request', $request);
    }

    public function ajaxIndex(Request $request)
    {
        $req = $request->all();

        $fields = [
            '1' => 'a.name',
            '2' => 'loc.name',
            '3' => 'a.qty',
            '4' => 'remaining',
            '5' => 'a.scrap_qty',
            '6' => 'a.accessory_thresholds',
            '7' => 'a.purchase_date',
            '8' => 'a.updated_at',
        ];

        $db = DB::table('accessories as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin(DB::raw('(SELECT accessory_id, count(id) as tot_assigns FROM `accessories_users` group by accessory_id) au'), function ($j) {
            $j->on('a.id', '=', 'au.accessory_id');
        });
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('places as pla', 'pla.id', '=', 'a.internal_place_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'a.manufacturer_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');

        $db->select('a.unique_tag as unique_tag', 'a.id', 'a.name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'pla.place as place_name', 'cat.name as cat_name', 'a.qty', 'a.order_number', 'a.purchase_currency', 'a.company_id', 'a.scrap_qty', 'dep.name as department', 'a.image', 'mnu.attachment', 'cat.image_thumbnail as cat_img', 'a.accessory_thresholds', 'cat.id as category_id');
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'));
        // $db->addSelect(DB::raw('case when au.tot_assigns is not null then (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)) ) when a.scrap_qty > 0 then (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0)) else COALESCE(a.qty, 0) end as remaining'));
        $db->addSelect(DB::raw('CASE WHEN au.tot_assigns IS NOT NULL THEN (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0))) WHEN a.scrap_qty > 0 THEN (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0)) ELSE COALESCE(a.qty, 0) END as remaining'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('concat("AC", a.id) as batch_no'));
        } else {
            $db->addSelect(DB::raw('concat("A", a.id) as batch_no'));
        }

        if (isset($req["showDeletedAccessories"]) && $req["showDeletedAccessories"] == "true") {
            $db->whereNotNull('a.deleted_at');
        } else {
            $db->whereNull('a.deleted_at');
        }

        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings       = Settings::getSettings();
        $permitted_loc  = explode(",", $loc_previllage);
        if ($settings->location_config == 1) {
            if (empty($loc_previllage)) {
                $db->where('a.location_id', '=', 0);
            } else {
                $db->whereIn('a.location_id', $permitted_loc);
            }
        }
        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts           = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }
        $db->where("a.company_id", "=", Auth::user()->company_id);
        $location_filter = isset($req['location']) ? $req['location'] : $request->input("location", null);
        if ($location_filter != null && $location_filter != 'null') {
            $db->whereIn("a.location_id", explode(",", $location_filter));
        }
        $department_filter = isset($req['department']) ? $req['department'] : $request->input("department", null);
        if ($department_filter != null && $department_filter != 'null') {
            $db->whereIn("a.department_id", explode(",", $department_filter));
        }

        $cat_id_filter = isset($req['cat_id']) ? $req['cat_id'] : $request->input("cat_id", null);
        if ($cat_id_filter != null && $cat_id_filter != 'null') {
            $db->whereIn("a.category_id", explode(",", $cat_id_filter));
        }

        $type_filter = isset($req['type']) ? $req['type'] : $request->input("type", null);
        if ($type_filter !== null && $type_filter !== 'null') {
            if ($type_filter == "Available") {
                $db->whereRaw('(a.qty - (IFNULL(au.tot_assigns, 0) + a.scrap_qty)) > 0');
            } elseif ($type_filter == "In use") {
                $db->whereRaw('IFNULL(au.tot_assigns, 0) > 0');
            } elseif ($type_filter == "In Scrap") {
                $db->where('a.scrap_qty', '>', 0);
            }
        }

        if (isset($request->q) && $request->q != null) {
            $ids = array_map('intval', explode(',', trim(base64_decode($request->q), '"')));
            $db->whereIn('a.id', $ids);
        }

        $return['recordsTotal']    = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["filters"])) {
            $filters = $req["filters"];
            $db->where(function ($query) use ($filters) {
                if (isset($filters["location"]) && ! empty($filters["location"]) && $filters['location'] != "null") {
                    $query->whereIn("a.location_id", $filters['location']);
                }
                if (isset($filters["purchase_reference"]) && ! empty($filters["purchase_reference"]) && $filters['purchase_reference'] != "null") {
                    $query->whereIn("a.invoice_id", $filters['purchase_reference']);
                }
                if (isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                    $query->whereIn("a.internal_place_id", $filters['internal_place']);
                }
                if (isset($filters["categories"]) && ! empty($filters["categories"]) && $filters['categories'] != "null") {
                    $query->whereIn("a.category_id", $filters["categories"]);
                }

                if (isset($filters["asset_department"]) && ! empty($filters["asset_department"]) && $filters['asset_department'] != "null") {
                    $query->whereIn("a.department_id", $filters["asset_department"]);
                }
                if (isset($filters["assigned_user"]) && $filters['assigned_user'] && $filters['assigned_user'] != "null") {
                    $subQuery1 = AccessoryUser::where("assigned_for", 1)->whereIn('assigned_to', $filters['assigned_user'])->pluck('accessory_id');
                    $query->whereIn("a.id", $subQuery1);
                }
                if (isset($filters["assigned_place"]) && $filters['assigned_place'] && $filters['assigned_place'] != "null") {
                    $subQuery2 = AccessoryUser::where("assigned_for", 2)->whereIn('assigned_to', $filters['assigned_place'])->pluck('accessory_id');
                    $query->whereIn("a.id", $subQuery2);
                }
                if (isset($filters["device_assigned"]) && $filters['device_assigned'] && $filters['device_assigned'] != "null") {
                    $subQuery3 = AccessoryUser::where("assigned_for", 3)->whereIn('assigned_to', $filters["device_assigned"])->pluck('accessory_id');
                    $query->whereIn("a.id", $subQuery3);
                }
                if (isset($filters["available_accessories"]) && $filters["available_accessories"] && $filters["available_accessories"] != "null") {
                    if (isset($filters["available_accessories"]) && $filters["available_accessories"] && $filters["available_accessories"] != "null") {
                        $query->where(function ($query) use ($filters) {
                            if ($filters["available_accessories"] == 1) {
                                $query->whereRaw('CASE
                                    WHEN au.tot_assigns IS NOT NULL THEN
                                        (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)))
                                    WHEN a.scrap_qty > 0 THEN
                                        (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0))
                                    ELSE
                                        COALESCE(a.qty, 0)
                                    END >= 1');
                            } elseif ($filters["available_accessories"] == 2) {
                                $query->whereRaw('CASE
                                WHEN au.tot_assigns IS NOT NULL THEN
                                    (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)))
                                WHEN a.scrap_qty > 0 THEN
                                    (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0))
                                ELSE
                                    COALESCE(a.qty, 0)
                                END = 0');
                            }
                        });
                    }
                }
            });
            $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.updated_at'];
            if (isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2) {
                if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                    $daterange = explode(" - ", $filters["date_range"]);
                    $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                    $to_date   = date("Y-m-d H:i:s", strtotime($daterange[1]));
                    if ($from_date && $to_date) {
                        $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                        $db->whereRaw($whereStr);
                    }
                }
            }
            $is_searching = true;
        }

        if (isset($req["location"]) && $req['location'] && $req['location'] != "null") {
            $array = explode(",", $req['location']);
            $db->whereIn("a.location_id", $array);
        }
        // $return['recordsTotal'] = $db->count();
        // $return['recordsFiltered'] = $return['recordsTotal'];
        $return['recordsFiltered'] = $db->count();

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('(concat("AC", a.id) like "%%%1$s%%"  or a.unique_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or pla.place like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.scrap_qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or (case when au.tot_assigns is not null then (a.qty - au.tot_assigns) else a.qty end) like "%%%1$s%%" or FORMAT(a.purchase_cost, 2) like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when au.tot_assigns is not null then (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)) ) when a.scrap_qty > 0 then (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0)) else COALESCE(a.qty, 0) end) like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('(concat("A", a.id) like "%%%1$s%%"  or a.unique_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or pla.place like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.scrap_qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or (case when au.tot_assigns is not null then (a.qty - au.tot_assigns) else a.qty end) like "%%%1$s%%" or FORMAT(a.purchase_cost, 2) like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when au.tot_assigns is not null then (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)) ) when a.scrap_qty > 0 then (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0)) else COALESCE(a.qty, 0) end) like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        // if( isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
        //     $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
        // }

        if (isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
            $colIndex = (int) $req['order'][0]['column'];
            $dir      = $req['order'][0]['dir'];
            // Check if it's the updated_at display column
            $colData = $req['columns'][$colIndex]['data'] ?? '';
            if ($colData == 'a.last_updated_at' || $colData == 'a.last_updated_at_raw') {
                // Force raw datetime ordering
                $db->orderBy('a.updated_at', $dir);
            } else {
                $db->orderBy($fields[$colIndex], $dir);
            }
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }

        $db->skip($skip);
        $db->take($take);

        $data           = $db->get();
        $return['data'] = [];
        foreach ($data as $d) {
            if ($d->category_id) {
                $cat_threshold = Threshold::where('cat_id', $d->category_id)->first();
            }
            $catThreshold       = isset($cat_threshold->threshold) ? $cat_threshold->threshold : 0;
            $d->catthreshold    = $catThreshold;
            $currency_format    = Currency::getCurrencyByCode($d->purchase_currency);
            $d->currency_symbol = $currency_format['symbol'] ?? '';
            $d->accessories_img = (
                ($d->image != "" && file_exists(public_path('uploads/accessories/' . $d->image))) ? url("uploads/accessories") . "/" . $d->image : (($d->attachment != "" && file_exists(public_path('uploads/manufacturers/' . $d->attachment))) ? url("uploads/manufacturers") . "/" . $d->attachment : (($d->cat_img != "" && file_exists(public_path('uploads/category/' . $d->cat_img))) ? url("uploads/category") . "/" . $d->cat_img : null))
            );
            $return['data'][] = ['a' => $d];
        }

        return response()->json($return);
    }

    public function accessoriesExport(Request $request)
    {
        $req    = $request->all();
        $return = [
            "draw" => date('is'),
        ];
        if (! Auth::user()->hasPermissionTo('AccessoriesDownload') || ! config("services.assets.enabled")) {
            $return["status"] = "danger";
            $return["msg"]    = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $fields = [
            '1' => 'a.name',
            '2' => 'loc.name',
            '3' => 'a.qty',
            '4' => 'remaining',
            '5' => 'a.scrap_qty',
            '6' => 'a.purchase_date',
            '7' => 'a.purchase_cost',
            '8' => 'a.order_number',
        ];

        $db = DB::table('accessories as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin(DB::raw('(SELECT accessory_id, count(id) as tot_assigns FROM `accessories_users` group by accessory_id) au'), function ($j) {
            $j->on('a.id', '=', 'au.accessory_id');
        });
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('places as p', 'p.id', '=', 'a.internal_place_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('manufacturers as manu', 'manu.id', '=', 'a.manufacturer_id');
        $db->leftJoin('suppliers as sup', 'sup.id', '=', 'a.supplier_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');

        $db->select('a.*', 'cmp.name as cmp_name', 'loc.name as loc_name', 'cat.name as cat_name', 'manu.name as manu_name', 'sup.name as sup_name', 'dep.name as department', 'p.place as internal_place');
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('concat("AC", a.id) as acc_batch_no'));
        } else {
            $db->addSelect(DB::raw('concat("A", a.id) as acc_batch_no'));
        }
        $db->addSelect(DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        $db->addSelect(DB::raw('case when au.tot_assigns is not null then (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)) ) when a.scrap_qty > 0 then (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0)) else COALESCE(a.qty, 0) end as remaining'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y %h:%i %p") as purchase_date'));

        // if( isset($req["showDeletedAccessories"]) && $req["showDeletedAccessories"] == "true" ) {
        //     $db->whereNotNull('a.deleted_at');
        // }
        // else {
        //     $db->whereNull('a.deleted_at');
        // }

        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings       = Settings::getSettings();
        $permitted_loc  = explode(",", $loc_previllage);
        if ($settings->location_config == 1) {
            if (empty($loc_previllage)) {
                $db->where('a.location_id', '=', 0);
            } else {
                $db->whereIn('a.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts           = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }
        $db->where("a.company_id", "=", Auth::user()->company_id);
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters         = json_decode($request_filters);
            $req             = [];
            if (isset($filters->showDeletedAccessories) && $filters->showDeletedAccessories == true) {
                $db->whereNotNull('a.deleted_at');
            } else {
                $db->whereNull('a.deleted_at');
            }
            if (isset($filters->dashboard_filters)) {
                $req["dashboard_filters"] = (array) $filters->dashboard_filters;
                $location_filter          = isset($req["dashboard_filters"]['location']) ? $req["dashboard_filters"]['location'] : "null";
                if ($location_filter != null && $location_filter != 'null') {
                    $db->whereIn("a.location_id", explode(",", $location_filter));
                }
                $department_filter = isset($req["dashboard_filters"]['department']) ? $req["dashboard_filters"]['department'] : "null";
                if ($department_filter != null && $department_filter != 'null') {
                    $db->whereIn("a.department_id", explode(",", $department_filter));
                }
                $cat_id_filter = isset($req["dashboard_filters"]['cat_id']) ? $req["dashboard_filters"]['cat_id'] : "null";
                if ($cat_id_filter != null && $cat_id_filter != 'null') {
                    $db->whereIn("a.category_id", explode(",", $cat_id_filter));
                }
                $type_filter = isset($req["dashboard_filters"]['type']) ? $req["dashboard_filters"]['type'] : "null";
                if ($type_filter !== null && $type_filter !== 'null') {
                    if ($type_filter == "Available") {
                        $db->whereRaw('(a.qty - (IFNULL(au.tot_assigns, 0) + a.scrap_qty)) > 0');
                    } elseif ($type_filter == "In use") {
                        $db->whereRaw('IFNULL(au.tot_assigns, 0) > 0');
                    } elseif ($type_filter == "In Scrap") {
                        $db->where('a.scrap_qty', '>', 0);
                    }
                }
            }
            if (isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if (isset($filters->other_filters)) {
                $req["filters"] = (array) $filters->other_filters;
            }

            if (isset($filters->q)) {
                $ids = array_map('intval', explode(',', trim(base64_decode($filters->q), '"')));
                $db->whereIn('a.id', $ids);
            }
            if (isset($req["filters"])) {
                $filters = $req["filters"];
                if (! empty($filters["location"]) && $filters["location"] != "null") {
                    $db->whereIn("a.location_id", $filters["location"]);
                }
                if (isset($filters["purchase_reference"]) && ! empty($filters["purchase_reference"]) && $filters['purchase_reference'] != "null") {
                    $db->whereIn("a.invoice_id", $filters['purchase_reference']);
                }
                if (isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                    $db->whereIn("a.internal_place_id", $filters['internal_place']);
                }
                if (isset($filters["categories"]) && $filters['categories'] && $filters['categories'] != "null") {
                    $db->whereIn("a.category_id", $filters['categories']);
                }
                if (isset($filters["asset_department"]) && $filters['asset_department'] && $filters['asset_department'] != "null") {
                    $db->whereIn("a.department_id", $filters['asset_department']);
                }
                if (isset($filters["assigned_user"]) && $filters['assigned_user'] && $filters['assigned_user'] != "null") {
                    $subQuery1 = AccessoryUser::where("assigned_for", 1)->whereIn('assigned_to', $filters['assigned_user'])->pluck('accessory_id');
                    $db->whereIn("a.id", $subQuery1);
                }
                if (isset($filters["assigned_place"]) && $filters['assigned_place'] && $filters['assigned_place'] != "null") {
                    $subQuery2 = AccessoryUser::where("assigned_for", 2)->whereIn('assigned_to', $filters['assigned_place'])->pluck('accessory_id');
                    $db->whereIn("a.id", $subQuery2);
                }
                if (isset($filters["device_assigned"]) && $filters['device_assigned'] && $filters['device_assigned'] != "null") {
                    $subQuery3 = AccessoryUser::where("assigned_for", 3)->whereIn('assigned_to', $filters["device_assigned"])->pluck('accessory_id');
                    $db->whereIn("a.id", $subQuery3);
                }
                $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.updated_at'];
                if (isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2) {
                    if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                        $daterange = explode(" - ", $filters["date_range"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date   = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if ($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $db->whereRaw($whereStr);
                        }
                    }
                }
                if (isset($filters["available_accessories"]) && $filters["available_accessories"] && $filters["available_accessories"] != "null") {
                    if (isset($filters["available_accessories"]) && $filters["available_accessories"] && $filters["available_accessories"] != "null") {
                        $db->where(function ($query) use ($filters) {
                            if ($filters["available_accessories"] == 1) {
                                $query->whereRaw('CASE
                                    WHEN au.tot_assigns IS NOT NULL THEN
                                        (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)))
                                    WHEN a.scrap_qty > 0 THEN
                                        (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0))
                                    ELSE
                                        COALESCE(a.qty, 0)
                                    END >= 1');
                            } elseif ($filters["available_accessories"] == 2) {
                                $query->whereRaw('CASE
                                WHEN au.tot_assigns IS NOT NULL THEN
                                    (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)))
                                WHEN a.scrap_qty > 0 THEN
                                    (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0))
                                ELSE
                                    COALESCE(a.qty, 0)
                                END = 0');
                            }
                        });
                    }
                }
                $is_searching = true;

                if (isset($req["search"]) && $search_key = trim($req["search"])) {
                    if (config("app.client") == "etherealmachines") {
                        $whereStr = sprintf('((concat("AC", a.id)) like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or (case when au.tot_assigns is not null then (a.qty - au.tot_assigns) else a.qty end) like "%%%1$s%%" or FORMAT(a.purchase_cost, 2) like "%%%1$s%%")', $search_key);
                    } else {
                        $whereStr = sprintf('((concat("A", a.id)) like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or (case when au.tot_assigns is not null then (a.qty - au.tot_assigns) else a.qty end) like "%%%1$s%%" or FORMAT(a.purchase_cost, 2) like "%%%1$s%%")', $search_key);
                    }
                    $db->whereRaw($whereStr);
                    $return['recordsFiltered'] = $db->count();
                }
            }
        }
        $data          = $db->get();
        $lineArray     = [];
        $keys          = ['Batch No', 'Unique Tag', 'Company', 'Accessory Name', 'Department', 'Accessory Category', 'Manufacture', 'Supplier', 'Location', 'Internal Place', 'Total Qty', 'Threshold Qty', 'Available Qty', 'Scrap Qty', 'Purchase Date', 'Purchase Cost', 'Purchase Currency', 'Order Number', 'Notes'];
        $settings      = Settings::first();
        $customFields  = [];
        $prefixed_code = [];
        if ($settings->accessories_custom_fieldset_id != "") {
            $customFieldset = CustomFieldset::find($settings->accessories_custom_fieldset_id);
            if (! empty($customFieldset->fields)) {
                foreach ($customFieldset->fields as $f) {
                    array_push($keys, $f->name);
                }
            }
        }
        foreach ($data as $key => $val) {
            $line = [
                $val->acc_batch_no,
                $val->unique_tag,
                $val->cmp_name,
                $val->name,
                $val->department,
                $val->cat_name,
                $val->manu_name,
                $val->sup_name,
                $val->loc_name,
                $val->internal_place,
                $val->qty,
                $val->accessory_thresholds,
                $val->remaining,
                $val->scrap_qty,
                $val->purchase_date,
                $val->purchase_cost_format,
                $val->purchase_currency,
                $val->order_number,
                $val->notes,
            ];
            if ($settings->accessories_custom_fieldset_id) {
                if (! empty($customFieldset)) {
                    $customaCol   = json_decode(json_encode($val), true);
                    $fieldData    = CommonHelper::getCustomData($customFieldset->fields, $customaCol);
                    $dataFieldset = [];
                    foreach ($fieldData as $k => $f) {
                        $col_name                = ucwords(str_replace(['_itm_', '_'], ' ', $k));
                        $dataFieldset[$col_name] = $f;
                    }
                    $line = array_merge($line, array_values($dataFieldset));
                }
            }
            $accessory = Accessory::find($val->id);
            if (isset($accessory->category_id) && $accessory->category->customFieldset && count($accessory->category->customFieldset->fields)) {
                foreach ($accessory->category->customFieldset->fields as $field) {
                    $con = '_itm_' . '' . str_replace(' ', '_', strtolower($field->name));
                    if (! in_array(ucwords($field->name), $keys)) {
                        array_push($keys, ucwords($field->name));
                    }
                    $modelCusValue = CommonHelper::getCustomDataFormate($field, $accessory->$con);
                    array_push($line, $modelCusValue);
                }
            }
            $lineArray[] = $line;
        }
        $result = json_decode(json_encode($lineArray, true), true);
        return Excel::download(new AccessoriesExport($result, $keys), 'Accessories.xlsx');
    }

    public function accessoriesExportPDF(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.accessory_fields.Unable_to_export_the_Details')];
        $vd     = [];
        if (! Auth::user()->hasPermissionTo('AccessoriesDownload') || ! config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $db = DB::table('accessories as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin(DB::raw('(SELECT accessory_id, count(id) as tot_assigns FROM `accessories_users` group by accessory_id) au'), function ($j) {
            $j->on('a.id', '=', 'au.accessory_id');
        });
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');

        $db->select('a.id', 'a.unique_tag', 'a.name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'cat.name as cat_name', 'a.qty', 'a.order_number', 'a.purchase_currency', 'a.company_id', 'a.scrap_qty', 'a.accessory_thresholds');
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when au.tot_assigns is not null then (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)) ) when a.scrap_qty > 0 then (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0)) else COALESCE(a.qty, 0) end as remaining'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('concat("AC", a.id) as batch_no'));
        } else {
            $db->addSelect(DB::raw('concat("A", a.id) as batch_no'));
        }

        // if( isset($request["showDeletedAccessories"]) && $request["showDeletedAccessories"] == "true" ) {
        //     $db->whereNotNull('a.deleted_at');
        // }
        // else {
        //     $db->whereNull('a.deleted_at');
        // }

        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings       = Settings::getSettings();
        $permitted_loc  = explode(",", $loc_previllage);
        if ($settings->location_config == 1) {
            if (empty($loc_previllage)) {
                $db->where('a.location_id', '=', 0);
            } else {
                $db->whereIn('a.location_id', $permitted_loc);
            }
        }

        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings       = Settings::getSettings();
        $permitted_loc  = explode(",", $loc_previllage);
        if ($settings->location_config == 1) {
            if (empty($loc_previllage)) {
                $db->where('a.location_id', '=', 0);
            } else {
                $db->whereIn('a.location_id', $permitted_loc);
            }
        }
        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts           = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters         = json_decode($request_filters);
            $req             = [];
            if (isset($filters->showDeletedAccessories) && $filters->showDeletedAccessories == true) {
                $db->whereNotNull('a.deleted_at');
            } else {
                $db->whereNull('a.deleted_at');
            }

            if (isset($filters->dashboard_filters)) {
                $req["dashboard_filters"] = (array) $filters->dashboard_filters;
                $location_filter          = isset($req["dashboard_filters"]['location']) ? $req["dashboard_filters"]['location'] : "null";
                if ($location_filter != null && $location_filter != 'null') {
                    $db->whereIn("a.location_id", explode(",", $location_filter));
                }
                $department_filter = isset($req["dashboard_filters"]['department']) ? $req["dashboard_filters"]['department'] : "null";
                if ($department_filter != null && $department_filter != 'null') {
                    $db->whereIn("a.department_id", explode(",", $department_filter));
                }
                $cat_id_filter = isset($req["dashboard_filters"]['cat_id']) ? $req["dashboard_filters"]['cat_id'] : "null";
                if ($cat_id_filter != null && $cat_id_filter != 'null') {
                    $db->whereIn("a.category_id", explode(",", $cat_id_filter));
                }
                $type_filter = isset($req["dashboard_filters"]['type']) ? $req["dashboard_filters"]['type'] : "null";
                if ($type_filter !== null && $type_filter !== 'null') {
                    if ($type_filter == "Available") {
                        $db->whereRaw('(a.qty - (IFNULL(au.tot_assigns, 0) + a.scrap_qty)) > 0');
                    } elseif ($type_filter == "In use") {
                        $db->whereRaw('IFNULL(au.tot_assigns, 0) > 0');
                    } elseif ($type_filter == "In Scrap") {
                        $db->where('a.scrap_qty', '>', 0);
                    }
                }
            }
            if (isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if (isset($filters->other_filters)) {
                $req["filters"] = (array) $filters->other_filters;
            }

            if (isset($filters->q)) {
                $ids = array_map('intval', explode(',', trim(base64_decode($filters->q), '"')));
                $db->whereIn('a.id', $ids);
            }

            // if ($request->filled('q')) {
            //     $ids = array_map('intval', explode(',', trim(base64_decode($request->q), '"')));
            //     dd($ids);
            //     $db->whereIn('a.id', $ids);
            // }
            if (isset($req["filters"])) {
                $filters = $req["filters"];
                if (! empty($filters["location"]) && $filters["location"] != "null") {
                    $db->whereIn("a.location_id", $filters["location"]);
                }
                if (isset($filters["purchase_reference"]) && ! empty($filters["purchase_reference"]) && $filters['purchase_reference'] != "null") {
                    $db->whereIn("a.invoice_id", $filters['purchase_reference']);
                }
                if (isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                    $db->whereIn("a.internal_place_id", $filters['internal_place']);
                }
                if (isset($filters["categories"]) && $filters['categories'] && $filters['categories'] != "null") {
                    $db->whereIn("a.category_id", $filters['categories']);
                }
                if (isset($filters["asset_department"]) && $filters['asset_department'] && $filters['asset_department'] != "null") {
                    $db->whereIn("a.department_id", $filters['asset_department']);
                }
                if (isset($filters["assigned_user"]) && $filters['assigned_user'] && $filters['assigned_user'] != "null") {
                    $subQuery1 = AccessoryUser::where("assigned_for", 1)->whereIn('assigned_to', $filters['assigned_user'])->pluck('accessory_id');
                    $db->whereIn("a.id", $subQuery1);
                }
                if (isset($filters["assigned_place"]) && $filters['assigned_place'] && $filters['assigned_place'] != "null") {
                    $subQuery2 = AccessoryUser::where("assigned_for", 2)->whereIn('assigned_to', $filters['assigned_place'])->pluck('accessory_id');
                    $db->whereIn("a.id", $subQuery2);
                }
                if (isset($filters["device_assigned"]) && $filters['device_assigned'] && $filters['device_assigned'] != "null") {
                    $subQuery3 = AccessoryUser::where("assigned_for", 3)->whereIn('assigned_to', $filters["device_assigned"])->pluck('accessory_id');
                    $db->whereIn("a.id", $subQuery3);
                }
                $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.updated_at'];
                if (isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2) {
                    if (isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2) {
                        if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                            $daterange = explode(" - ", $filters["date_range"]);
                            $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                            $to_date   = date("Y-m-d H:i:s", strtotime($daterange[1]));
                            if ($from_date && $to_date) {
                                $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                                $db->whereRaw($whereStr);
                            }
                        }
                    }
                }
                if (isset($filters["available_accessories"]) && $filters["available_accessories"] && $filters["available_accessories"] != "null") {
                    if (isset($filters["available_accessories"]) && $filters["available_accessories"] && $filters["available_accessories"] != "null") {
                        $db->where(function ($query) use ($filters) {
                            if ($filters["available_accessories"] == 1) {
                                $query->whereRaw('CASE
                                    WHEN au.tot_assigns IS NOT NULL THEN
                                        (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)))
                                    WHEN a.scrap_qty > 0 THEN
                                        (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0))
                                    ELSE
                                        COALESCE(a.qty, 0)
                                    END >= 1');
                            } elseif ($filters["available_accessories"] == 2) {
                                $query->whereRaw('CASE
                                WHEN au.tot_assigns IS NOT NULL THEN
                                    (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)))
                                WHEN a.scrap_qty > 0 THEN
                                    (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0))
                                ELSE
                                    COALESCE(a.qty, 0)
                                END = 0');
                            }
                        });
                    }
                }
                $is_searching = true;

                if (isset($req["search"]) && $search_key = trim($req["search"])) {
                    if (config("app.client") == "etherealmachines") {
                        $whereStr = sprintf('((concat("AC", a.id)) like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or (case when au.tot_assigns is not null then (a.qty - au.tot_assigns) else a.qty end) like "%%%1$s%%" or FORMAT(a.purchase_cost, 2) like "%%%1$s%%")', $search_key);
                    } else {
                        $whereStr = sprintf('((concat("A", a.id)) like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or (case when au.tot_assigns is not null then (a.qty - au.tot_assigns) else a.qty end) like "%%%1$s%%" or FORMAT(a.purchase_cost, 2) like "%%%1$s%%")', $search_key);
                    }
                    $db->whereRaw($whereStr);
                    $return['recordsFiltered'] = $db->count();
                }
            }
        }

        $data = $db->get();
        $vd['records'] = $data;
        return PDF::view('accessories.for_export', $vd)->landscape()->format('a4')->name('accessories.pdf')->download();
    }

    public function ajaxAddAccessory(Request $request)
    {
        $appSettings = Settings::first();
        $return      = [
            "status" => "danger",
            "msg"    => trans('content.accessory_fields.Unable_to_add_given_Accessory'),
        ];
        if (! Auth::user()->hasPermissionTo('AccessoriesAdd') || ! config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only("unique_tag", "name", "company_id", "category_id", "location_id", "internal_place_id", "qty", "order_number", "purchase_date", "purchase_currency", "purchase_cost", "manufacturer_id", "supplier_id", "invoice_id", "notes", "department_id", "image", "accessory_thresholds", "thresholds_alerts", "requestable_accessory", "reorder_limits");

        $validate = Validator::make($data, [
            'unique_tag'        => ['nullable', 'clean_text_only', 'max:100', Rule::unique('accessories', 'unique_tag')->where(function ($query) use ($request) {
                return $query->where('location_id', $request->location_id);
            })],
            "name"              => "required|min:3|max:255|clean_text_only",
            "company_id"        => "required|integer",
            "location_id"       => "required|integer",
            "category_id"       => "required|integer",
            "qty"               => "required|integer|min:0",
            "supplier_id"       => "nullable|integer|exists:suppliers,id",
            "invoice_id"        => "nullable|integer|exists:purchases,id",
            "notes"             => "nullable|clean_text_only|max:2000",
            "department_id"     => "nullable|integer|exists:departments,id",
            "internal_place_id" => "nullable|integer|exists:places,id",
            'image'             => 'image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'image.max'   => 'Image must not be greater than 2 MB.',
            'image.mimes' => 'Only JPEG, JPG, and PNG images are allowed.',
        ]);

        $data_fields   = $request->input("fields", null);
        $custom_fields = $custom_fields_val = [];
        $accessory     = Category::find($data['category_id']);
        if ($accessory && $accessory->customFieldset && count($accessory->customFieldset->fields)) {
            foreach ($accessory->customFieldset->fields as $f) {
                $col_name         = $f->nameToColumn();
                $formatType       = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name]                       = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[]                       = $col_name;
                $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
            }
        }
        if (Settings::first()->accessories_custom_fieldset_id != "") {
            $customFieldset = CustomFieldset::where('id', Settings::first()->accessories_custom_fieldset_id)->first();
            foreach ($customFieldset->fields as $f) {
                $col_name         = $f->nameToColumn();
                $formatType       = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name]                       = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[]                       = $col_name;
                $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
            }
        }

        if ($validate->fails()) {
            $v             = $validate->errors()->toArray();
            $e             = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        if (Auth::user()->isSuperUser()) {
            $data["company_id"] = empty($data['company_id']) ? Auth::user()->company_id : $data['company_id'];
        } else {
            $data["company_id"] = $appSettings->full_multiple_companies_support == 0 ? (empty($data['company_id']) ? Auth::user()->company_id : $data['company_id']) : Auth::user()->company_id;
        }

        $data["unique_tag"]            = ! empty($data["unique_tag"]) ? $data["unique_tag"] : null;
        $data["qty"]                   = (int) $data["qty"];
        $data["purchase_currency"]     = $data["purchase_currency"] ? $data["purchase_currency"] : null;
        $data["purchase_cost"]         = (float) $data["purchase_cost"];
        $data["purchase_date"]         = CommonHelper::getDateAs($data["purchase_date"], "Y-m-d", "d/m/Y");
        $data["user_id"]               = Auth::user()->id;
        $data["thresholds_alerts"]     = isset($data["thresholds_alerts"]) ? $data["thresholds_alerts"] : 0;
        $data["accessory_thresholds"]  = (int) $data["accessory_thresholds"];
        $data["requestable_accessory"] = isset($data["requestable_accessory"]) ? $data["requestable_accessory"] : 0;
        $data["reorder_limits"]        = isset($data["reorder_limits"]) ? $data["reorder_limits"] : 0;
        // $data["accessories_custom_fields"] = json_encode($custom_fields_val);
        $acc = Accessory::create($data);
        if (count($custom_fields_val)) {
            foreach ($custom_fields_val as $key => $f) {
                $acc->$key = isset($f) ? $f : null;
            }
        }
        $acc->image = null;
        if ($request->file('image')) {
            $uploaded_img = $request->file('image');
            // $accessoryImage = str_random(12) . str_random(12) . '.' . $uploaded_img->getClientOriginalExtension();
            $accessoryImage = Str::random(12) . Str::random(12) . '.' . $uploaded_img->getClientOriginalExtension();
            $path           = public_path("uploads/accessories/" . $accessoryImage);
            // Image::make($uploaded_img->getRealPath())->resize(300, null, function($constraint) {
            //     $constraint->aspectRatio();
            //     $constraint->upsize();
            // })->save($path);
            $manager = new ImageManager(new Driver());
            $image   = $manager->read($uploaded_img->getRealPath());
            $image->scale(width: 300);
            $image->save($path);
            $acc->image = $accessoryImage;
        } elseif ($request->input("forAction") == "clone" && $request->input("clone_img") != "" && ! $request->input("delete_img", 0)) {
            $old_path = public_path('/uploads/accessories/' . $request->input("clone_img"));
            if (file_exists($old_path)) {
                $cloneImgArr    = explode(".", $request->input("clone_img"));
                $accessoryImage = Str::random(12) . Str::random(12) . "." . last($cloneImgArr);
                $path           = public_path("uploads/accessories/" . $accessoryImage);
                // Image::make($old_path)->resize(300, null, function($constraint) {
                //     $constraint->aspectRatio();
                //     $constraint->upsize();
                // })->save($path);
                $manager = new ImageManager(new Driver());
                $image   = $manager->read($old_path);
                $image->scale(width: 300);
                $image->save($path);
                $acc->image = $accessoryImage;
            }
        }
        if ($acc) {
            if (! $acc->unique_tag) {
                $acc->generateUniqueTag();
            }
            if (config("app.client") == "etherealmachines") {
                $acc->batch_no = 'AC' . $acc->id;
            } else {
                $acc->batch_no = 'A' . $acc->id;
            }
            $acc->save();
            Actionlog::accessoryAdded($acc, Auth::id());
            // add entry in Accessory purchase
            $accPurchase                 = new AccessoryPurchase();
            $accPurchase->batch_no       = $acc->id;
            $accPurchase->po_no          = ! empty($acc->order_number) ? $acc->order_number : null;
            $accPurchase->purchase_date  = ! empty($acc->purchase_date) ? $acc->purchase_date : date('Y-m-d');
            $accPurchase->exp_date       = ! empty($acc->purchase_date) ? $acc->purchase_date : date('Y-m-d');
            $accPurchase->currency       = ! empty($acc->currency) ? $acc->currency : $appSettings->default_currency;
            $accPurchase->purchase_price = ! empty($acc->purchase_cost) ? $acc->purchase_cost : 0.00;
            $accPurchase->qty            = ! empty($acc->qty) ? $acc->qty : 0;
            $accPurchase->purchase_by    = ! empty($acc->supplier_id) ? $acc->supplier_id : null;
            $accPurchase->save();
            $user = Auth::user();
            if (Settings::first()->alerts_enabled == 1) {
                try {
                    $alertnotify = CommonHelper::getGlobalAlertEmail();
                    if (config('mail.service_enabled') && ! empty($alertnotify)) {
                        foreach ($alertnotify as $email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                Mail::to($email)->queue(new AccessoryAddNotification($acc, $user));
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            if (! empty($request->threshold_alert_users)) {
                foreach ($request->threshold_alert_users as $userId) {
                    ThresholdAlertSettings::create([
                        'asset_id'   => $acc->id,
                        'asset_type' => 2,
                        'user_id'    => $userId,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            $return["accessory_id"] = $acc->id;
            $return["msg"]          = trans('content.accessory_fields.Accessory_added_successfully');
            $return["status"]       = "success";
            Log::info("ajaxAddAccessory id:" . $acc->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
        }

        return response()->json($return);
    }

    public function deleteAccessory(Request $request, $id)
    {
        $return = [
            "status" => "danger",
            "msg"    => trans('content.accessory_fields.Unable_to_delete_chosen_Accessory'),
        ];
        if (! Auth::user()->hasPermissionTo('AccessoriesDelete') || ! config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }

        $acc = Accessory::where("id", "=", $id)->first();
        if (! $acc || ! $acc->exists) {
            $return["msg"] = trans('content.accessory_fields.already_deleted_data');
            return response()->json($return);
        }

        $record = Accessory::where('id', '=', $id)->withCount(['accessories_user'])->first();
        if ($record->accessories_user_count) {
            return response()->json(['status' => 'error', 'msg' => trans('content.accessory_fields.This_accessory_is_checked_out')]);
        }

        if ($record->asset_log_count) {
            return response()->json(['status' => 'error', 'msg' => trans('content.accessory_fields.Some_accessory_is_attached')]);
        }

        if (! Auth::user()->isSuperUser() && ! Company::checkUserAccess($acc)) {
            // $return["status"] = 'error';
            $return["section"] = 'accessory-delete';
            $return["msg"]     = Auth::user()->company_id == null ? trans('content.accessory_fields.update_company_name') : trans('content.multiple_company_access');
            return response()->json($return);
        }

        $check_outs = $acc->usersCount();
        if ($check_outs > 0) {
            $return["msg"] = trans('content.accessory_fields.Currently_this_accessory_used_by') . $check_outs . trans('content.accessory_fields.Please_check');
            return response()->json($return);
        }

        $place_counts = $acc->placesCount();
        if ($place_counts > 0) {
            $return["msg"] = trans('content.accessory_fields.Currently_this_accessory_used_by') . $place_counts . trans('content.accessory_fields.of_places');
            return response()->json($return);
        }

        $device_counts = $acc->devicesCount();
        if ($device_counts > 0) {
            $return["msg"] = trans('content.accessory_fields.Currently_this_accessory_used_by') . $device_counts . trans('content.accessory_fields.of_devices');
            return response()->json($return);
        }

        $accery_cat_id = $acc->category_id;
        $acc->delete();
        Actionlog::accessoryDeleted($acc, Auth::id());
        $this->notifyCategoryThresould($accery_cat_id);
        if ($acc->trashed()) {
            $return["status"] = "success";
            $return["msg"]    = $acc->name . ' ' . trans('content.accessory_fields.has_deleted_successfully');
            Log::info("deleteAccessory id:" . $acc->id . " ud:" . Auth::user()->id . " : " . json_encode($request->all()));
        }

        return response()->json($return);
    }

    public function restoreAccessory(Request $request, $id)
    {
        $return = ["status" => "failure", "msg" => "Unable to restore the Accessory"];
        if (! Auth::user()->hasPermissionTo('AccessoriesRestore') || ! config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $accessory = Accessory::withTrashed()->where("id", "=", $id)->first();
        if (! $accessory->exists) {
            return response()->json($return);
        }
        $accessoryNotDelete = Accessory::where('location_id', $accessory->location_id)->where("unique_tag", $accessory->unique_tag)->first();
        if (isset($accessoryNotDelete->exists)) {
            $return["status"] = 'error';
            $return["msg"]    = trans('content.accessory_fields.already_restored_data');
            return response()->json($return);
        }

        if (! Auth::user()->isSuperUser() && ! Company::checkUserAccess($accessory)) {
            $return["status"] = 'error';
            $return["msg"]    = Auth::user()->company_id == null ? trans('content.device_fields.update_company_name') : trans('content.device_fields.multiple_company_access');
            return response()->json($return);
        }
        $accessory->restore();
        Actionlog::accessoryRestored($accessory, Auth::user()->id);
        $return["msg"]    = trans('content.accessory_fields.accessory_restored');
        $return["status"] = "success";
        return response()->json($return);
    }

    public function editAccessory(Request $request, $id)
    {
        $return = ['status' => 'fail', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('AccessoriesEdit') || ! config("services.assets.enabled")) {
            return response()->json($return);
        }
        $return = [
            "status" => "danger",
            "msg"    => trans('content.accessory_fields.Unable_to_update_given_Accessory'),
        ];

        $acc = Accessory::where("id", "=", $id)->first();
        if (! $acc->exists) {
            $return["msg"] = trans('content.accessory_fields.Given_accessory_is_not_found');
            return response()->json($return);
        }

        if (! Auth::user()->isSuperUser() && ! Company::checkUserAccess($acc)) {
            // $return["status"] = 'error';
            $return["section"] = 'accessory-checkout';
            $return["msg"]     = Auth::user()->company_id == null ? trans('content.accessory_fields.update_company_name') : trans('content.multiple_company_access');
            return response()->json($return);
        }

        $data = $request->only("name", "company_id", "category_id", "location_id", "internal_place_id", "qty", "order_number", "purchase_date", "purchase_cost", "purchase_currency", "manufacturer_id", "supplier_id", "invoice_id", "notes", "department_id", "image", "thresholds_alerts", "accessory_thresholds", "requestable_accessory", "reorder_limits", "unique_tag");

        $data_fields   = $request->input("fields", null);
        $custom_fields = $custom_fields_val = [];
        $category      = Category::find($data['category_id']);
        if ($category && $category->customFieldset && count($category->customFieldset->fields)) {
            foreach ($category->customFieldset->fields as $f) {
                $col_name         = $f->nameToColumn();
                $formatType       = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name]                       = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[]                       = $col_name;
                $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
            }
        }
        $globalCustomFieldObj = Settings::first();
        if (! empty($globalCustomFieldObj) && $globalCustomFieldObj->accessories_custom_fieldset_id != "") {
            $customFieldset = CustomFieldset::where('id', $globalCustomFieldObj->accessories_custom_fieldset_id)->first();
            if (! empty($customFieldset)) {
                foreach ($customFieldset->fields as $f) {
                    $col_name         = $f->nameToColumn();
                    $formatType       = CommonHelper::convertFormatToRegex($f->format);
                    $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                    // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                    $data[$col_name]                       = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                    $custom_fields[]                       = $col_name;
                    $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
                }
            }
        }

        $validate = Validator::make($data, [
            'unique_tag'        => ['nullable', 'clean_text_only', 'max:100', Rule::unique('accessories', 'unique_tag')->where(function ($query) use ($request) {
                return $query->where('location_id', $request->location_id);
            })->ignore($request->id)],
            "name"              => "required|min:3|max:255|clean_text_only",
            "company_id"        => "required|integer",
            "location_id"       => "required|integer",
            "category_id"       => "required|integer",
            "qty"               => "required|integer|min:0",
            "supplier_id"       => "nullable|integer|exists:suppliers,id",
            "invoice_id"        => "nullable|integer|exists:purchases,id",
            "notes"             => "nullable|clean_text_only|max:2000",
            "department_id"     => "nullable|integer|exists:departments,id",
            "internal_place_id" => "nullable|integer|exists:places,id",
            'image'             => 'image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'image.max'   => 'Image must not be greater than 2 MB.',
            'image.mimes' => 'Only JPEG, JPG, and PNG images are allowed.',
        ]);

        if ($validate->fails()) {
            $v             = $validate->errors()->toArray();
            $e             = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $data["qty"]      = (int) $data["qty"];
        $totalPurchaseQty = AccessoryPurchase::where('batch_no', $acc->id)->sum('qty') ?? 0;
        $inScrap          = $acc->scrap_qty;
        $inUseCount       = $acc->usersCount() + $acc->placesCount() + $acc->devicesCount() + $inScrap;
        if ($inUseCount >= $totalPurchaseQty) {
            if ($data['qty'] < $inUseCount) {
                return response()->json(['status' => 'error', 'msg' => $inUseCount . trans('content.accessory_fields.of_these_accessories')]);
            }
        }

        if ($totalPurchaseQty >= $inUseCount) {
            if ($data['qty'] < $totalPurchaseQty) {
                return response()->json(['status' => 'error', 'msg' => $totalPurchaseQty . trans('content.accessory_fields.purchase_accessory_qty')]);
            }
        }

        $data["internal_place_id"]         = ! empty($data["internal_place_id"]) ? $data["internal_place_id"] : null;
        $data["purchase_currency"]         = $data["purchase_currency"] ? $data["purchase_currency"] : null;
        $data["purchase_cost"]             = (float) $data["purchase_cost"];
        $data["purchase_date"]             = CommonHelper::getDateAs($data["purchase_date"], "Y-m-d", "d/m/Y");
        $data["user_id"]                   = Auth::user()->id;
        $data["accessories_custom_fields"] = json_encode($custom_fields_val);
        $data["department_id"]             = isset($data["department_id"]) ? $data["department_id"] : null;
        $data["accessories_custom_fields"] = null;
        $data["thresholds_alerts"]         = isset($data["thresholds_alerts"]) ? $data["thresholds_alerts"] : 0;
        $data["accessory_thresholds"]      = (int) $data["accessory_thresholds"];
        $data["requestable_accessory"]     = isset($data["requestable_accessory"]) ? $data["requestable_accessory"] : 0;
        $data["reorder_limits"]            = isset($data["reorder_limits"]) ? $data["reorder_limits"] : 0;
        // $data_custom = json_decode($data["accessories_custom_fields"], TRUE);
        // $var = json_decode($acc->accessories_custom_fields);
        // if(!empty($var)){
        //     foreach($data_custom as $key=>$d) {
        //         $var->$key = $d;
        //     }
        //     $data["accessories_custom_fields"] = json_encode($var);
        // } else {
        //     $data["accessories_custom_fields"] = json_encode($custom_fields_val);
        // }
        unset($data['qty']);
       $for_log_comparison = $acc->AccessorydataForCache();
        $acc->fill($data);
        if (count($custom_fields_val)) {
            foreach ($custom_fields_val as $key => $f) {
                $acc->$key = isset($f) ? $f : null;
            }
        }

        $imagedata = Accessory::find($id)->image;
        if ($request->input("delete_img", false) || $request->image != null) {
            if ($imagedata != null) {
                $exits1 = File::exists(public_path('/uploads/accessories/' . $imagedata));
                if ($exits1) {
                    File::delete(public_path('/uploads/accessories/' . $imagedata));
                    Accessory::where('id', $id)->update(['image' => null]);
                }
            }
        }
        if ($request->image) {
            $uploaded_img = $request->image;
            $image1       = Str::random(12) . Str::random(12) . '.' . $uploaded_img->getClientOriginalExtension();
            $path         = public_path("uploads/accessories/" . $image1);
            $manager = new ImageManager(new Driver());
            $image   = $manager->read($uploaded_img->getRealPath());
            $image->scale(width: 300);
            $image->save($path);
            $acc->image = $image1;
        }
        if ($acc->save()) {

            $isUpdated = false;
            $updatedFields = [];

            $accessoryArray = $acc->AccessorydataForCache();
            $comparisonArray = $for_log_comparison;

            foreach ($accessoryArray as $key => $value) {
                if (isset($comparisonArray[$key]) && $comparisonArray[$key] != $value) {
                    $isUpdated = true;
                    $updatedFields[] = $key;
                }
            }

            if ($isUpdated) {
                Actionlog::accessoryEdited($acc, $for_log_comparison, Auth::user()->id);

                Log::info('Accessory Updated Fields', $updatedFields);
            }
            ThresholdAlertSettings::where('asset_id', $acc->id)->where('asset_type', 2)->delete();
            if (! empty($request->threshold_alert_users)) {
                $data = [];

                foreach ($request->threshold_alert_users as $userId) {
                    $data[] = [
                        'asset_id'   => $acc->id,
                        'asset_type' => 2,
                        'user_id'    => $userId,
                        'updated_by' => Auth::user()->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                ThresholdAlertSettings::insert($data);
            }

            $this->notifyCategoryThresould($acc->category_id);
            $return["msg"]    = trans('content.accessory_fields.Accessory_has_updated_successfully');
            $return["status"] = "success";
            Log::info("editAccessory id:" . $acc->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
        }

        return response()->json($return);
    }

    public function getAccessory(Request $request, $id)
    {
        $return = ["status" => "failure"];
        $acc    = Accessory::where("id", $id)->first();

        if (! $acc->exists) {
            $return["msg"] = trans('content.accessory_fields.Given_accessory');
            return response()->json($return);
        }

        $acc->purchase_date = CommonHelper::getDateAs($acc->purchase_date, "d-m-Y", "Y-m-d");
        $acc->purchase_cost = number_format($acc->purchase_cost, 2, ".", "");

        $accessory                                     = [];
        $accessory["data"]                             = $acc->toArray();
        $accessory["dropdown"]                         = [];
        $accessory["custom_fields"]['all_fields']      = [];
        $accessory["custom_fields"]['required_fields'] = [];
        $accessory["custom_fields"]['html']            = "";

        if ($acc->department_id) {
            $department                          = Department::where("id", $acc->department_id)->select("id", "name as text")->first();
            $accessory["dropdown"]["department"] = $department && $department->exists ? $department->toArray() : null;
        }
        if ($acc->location_id) {
            $getLocation                       = Location::where("id", $acc->location_id)->select("id", "name as text")->first();
            $accessory["dropdown"]["location"] = $getLocation && $getLocation->exists ? $getLocation->toArray() : null;
        }
        if ($acc->internal_place_id) {
            $getInternalPlace                        = Location::where("id", $acc->internal_place_id)->select("id", "name as text")->first();
            $accessory["dropdown"]["internal_place"] = $getInternalPlace && $getInternalPlace->exists ? $getInternalPlace->toArray() : null;
        }
        if ($acc->manufacturer_id) {
            $getManufacture                        = Manufacture::where("id", $acc->manufacturer_id)->select("id", "name as text")->first();
            $accessory["dropdown"]["manufacturer"] = $getManufacture && $getManufacture->exists ? $getManufacture->toArray() : null;
        }
        if ($acc->supplier_id) {
            $getSupplier                       = Supplier::where("id", $acc->supplier_id)->select("id", "name as text")->first();
            $accessory["dropdown"]["supplier"] = $getSupplier && $getSupplier->exists ? $getSupplier->toArray() : null;
        }
        if ($acc->invoice_id) {
            $getInvoice                       = Purchase::where("id", $acc->invoice_id)->select("id", DB::raw('concat_ws(" - ", invoice_no, date_format(invoice_date, "%d/%m/%Y")) as text'))->first();
            $accessory["dropdown"]["invoice"] = $getInvoice && $getInvoice->exists ? $getInvoice->toArray() : null;
        }
        $thresholdUserEmails = ThresholdAlertSettings::where('threshold_alert_settings.asset_id', $id)->where('threshold_alert_settings.asset_type', 2)
            ->join('users', 'users.id', '=', 'threshold_alert_settings.user_id')
            ->select('users.id', 'users.email as text')
            ->get()
            ->toArray();
        $accessory["dropdown"]["thresholdUserEmails"] = ! empty($thresholdUserEmails) ? $thresholdUserEmails : null;
        if ($acc->category_id) {
            $getAccessory                       = Category::where("id", $acc->category_id)->select("id", "name as text")->first();
            $accessory["dropdown"]["accessory"] = $getAccessory && $getAccessory->exists ? $getAccessory->toArray() : null;

            // load custom fields
            if ($acc->category->customFieldset && count($acc->category->customFieldset->fields)) {
                // $accessory["custom_fields"] = CommonHelper::formCustomFieldsLicence($acc->category->customFieldset->fields, $accessory["data"]['accessories_custom_fields']);
                $accessory["custom_fields"] = CommonHelper::formCustomFields($acc->category->customFieldset->fields, $accessory["data"]);
            }
        } else {
            if (Settings::first()->accessories_custom_fieldset_id != null) {
                $fieldsetObj = CustomFieldset::where('id', Settings::first()->accessories_custom_fieldset_id)->first();
                if (! empty($fieldsetObj)) {
                    $accessory1 = CommonHelper::formCustomFields($fieldsetObj->fields, $accessory["data"]);
                    if (isset($accessory1['all_fields'][0])) {
                        array_push($accessory["custom_fields"]['all_fields'], $accessory1['all_fields'][0]);
                    }
                    if (isset($accessory1['required_fields'][0])) {
                        array_push($accessory["custom_fields"]['required_fields'], $accessory1['required_fields'][0]);
                    }
                    $accessory["custom_fields"]['html'] .= $accessory1['html'];
                }
            }
        }

        $return["status"]    = "success";
        $return["accessory"] = $accessory;
        return response()->json($return);
    }

    public function viewInfo(Request $request, $id)
    {
        // dd($request);
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('AccessoriesView') || ! config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $accessory = Accessory::leftjoin('departments as dep', 'dep.id', 'accessories.department_id')->select('accessories.*', 'dep.name as department')->where("accessories.id", "=", $id)->first();
        if (! $accessory || ! $accessory->exists) {
            return redirect("accessories")->with("status", "Accessory not found");
        }
        $userLocationAccessCheck = User::where('id', Auth::user()->id)->where('permitted_locations', 'like', '%' . $accessory->location_id . '%')->first();
        $settings                = Settings::getSettings();
        if ($settings->location_config == 1 && empty($userLocationAccessCheck)) {
            $return["msg"] = "You don't have Accessory location access";
            return redirect('dashboard')->with("msg", $return);
        }
        $companies  = Company::select("id", "name as text")->get()->toArray();
        $categories = Category::whereNull("deleted_at")->select("id", "name as text")->where("category_type", "accessory")->get()->toArray();
        $vd         = new stdClass;
        $vd->places = Place::selectOptions();

        $isCheckinCall = false;
        if ($request->checkin && is_numeric($request->checkin)) {
            // $holder = $accessory->users()->wherePivot('id', $request->checkin)->first();
            $get_au_record = DB::table('accessories_users as au')
                ->leftJoin('users as u', 'u.id', '=', 'au.assigned_to')
                ->leftJoin('assets as a', 'a.id', '=', 'au.assigned_to')
                ->leftJoin('places as p', 'p.id', '=', 'au.assigned_to')
                ->select(DB::raw('u.username as fullname'))
                ->addSelect(DB::raw('case when au.assigned_for = 1 then u.username when au.assigned_for = 2 then p.place when au.assigned_for = 3 then a.asset_tag end as target_name'))
                ->addSelect(DB::raw('case when au.assigned_for = 1 then "User" when au.assigned_for = 2 then "Place" when au.assigned_for = 3 then "Device" end as target_type'))
                ->where('au.id', $request->checkin)->limit(1)->get();
            if (count($get_au_record)) {
                $au_record     = $get_au_record[0];
                $isCheckinCall = [
                    "username"   => $au_record->fullname,
                    "checkoutTo" => $au_record->target_name, // ($au_record->device_id > 0 ? $au_record->asset_tag : $au_record->fullname),
                    "id"         => $request->checkin,
                ];
            }
        }

        $currencies          = Currency::getCurrencies();
        $companyFieldset     = CustomFieldset::where('id', Settings::first()->accessories_custom_fieldset_id)->first();
        $requestable_enabled = config('app.requestable_enabled');
        return view("accessories.info")->with(compact("accessory", "companies", "categories", "isCheckinCall", "currencies", "companyFieldset", "requestable_enabled"))->with("vd", $vd);
    }

    public function addPurchaseAccessory(Request $request)
    {
        if (! Auth::user()->hasPermissionTo('PurchaseAdd') || ! config("services.assets.enabled")) {
            $return = ['status' => 'error', 'section' => 'consumable-add', 'msg' => trans('content.user_fields.Permission_denied')];
            return response()->json($return);
        }
        $appSettings = Settings::first();
        if (empty(Auth::user()->company_id)) {
            return response()->json(['status' => 'error', 'section' => 'purchase-add', 'msg' => 'You are not allowed for this process !! Please update your company ID and try again !!']);
        }

        try {
            DB::beginTransaction();
            $objAccessoryPurchase = new AccessoryPurchase();
            $rules                = [
                'po_number'     => 'nullable|clean_text_only',
                'invoice_no'    => 'nullable|string',
                'batch_no'      => 'required|integer',
                'purchase_date' => 'nullable|date_format:d/m/Y',
                'exp_date'      => 'nullable|date_format:d/m/Y|after_or_equal:purchase_date',
                'currency'      => 'nullable',
                'bill_amount'   => 'nullable|numeric',
                'qty'           => 'required|integer|min:1',
                "purchase_by"   => "nullable|integer|exists:suppliers,id",
                'attachment'    => 'sometimes|mimes:png,gif,jpg,jpeg,doc,docx,pdf,txt,zip,rar,eml,msg,mbox,pst,xlsx,xls|max:2000',

            ];
            $messages = [
                'qty.integer'             => 'Quantity must be an Integer',
                'qty.required'            => 'Quantity field is required',
                'exp_date.after_or_equal' => 'The exp date must be after or equal to the purchase date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                foreach ($validator->errors()->getMessages() as $key => $value) {
                    $return['status'] = 'error';
                    $return['msg']    = $value;
                }
                return response()->json($return);
            } else {
                $data      = $request->validate($rules);
                $accessory = Accessory::where('id', $data['batch_no'])->first();

                $objAccessoryPurchase->batch_no       = $data['batch_no'];
                $objAccessoryPurchase->po_no          = isset($data['po_number']) ? $data['po_number'] : null;
                $objAccessoryPurchase->invoice_no     = isset($data['invoice_no']) ? $data['invoice_no'] : null;
                $objAccessoryPurchase->purchase_date  = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d/m/Y") : null;
                $objAccessoryPurchase->exp_date       = isset($request->exp_date) ? CommonHelper::getDateAs($request->exp_date, "Y-m-d", "d/m/Y") : null;
                $objAccessoryPurchase->currency       = empty($request->currency) ? $appSettings->default_currency : $request->currency;
                $objAccessoryPurchase->purchase_price = $data['bill_amount'];
                $objAccessoryPurchase->qty            = $data['qty'];
                $objAccessoryPurchase->purchase_by    = isset($data['purchase_by']) ? $data['purchase_by'] : null;

                if (isset($data['attachment']) && $data['attachment'] != null) {
                    $filePath        = date("Y") . '/' . date('m') . '/' . date('d');
                    $checkFolderPath = CommonHelper::attachmentFolderStructure('accessory', $filePath);
                    if (! $checkFolderPath) {
                        return $this->fail(500, " Directory not found", '');
                    }
                    $path = Storage::disk("documents")->putFile('accessory/' . $filePath, $data['attachment']);
                }
                $objAccessoryPurchase->attachment = isset($path) ? $path : null;
                if ($objAccessoryPurchase->save()) {
                    $qty           = $accessory->qty + $objAccessoryPurchase->qty;
                    $purchase_cost = $accessory->purchase_cost + $objAccessoryPurchase->purchase_price;
                    $accessory->update([
                        'qty'           => $qty,
                        'purchase_cost' => $purchase_cost,
                    ]);
                }
                DB::commit();
                $return["msg"]    = trans('content.accessory_fields.purchase_accessory_added_successfully');
                $return["status"] = "success";
                Log::info("purchase Acc store id:" . $objAccessoryPurchase->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
                return response()->json($return);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("addPurchaseAccessory : " . $e->getMessage());
        }
    }

    public function ajaxPurchaseAccessoryIndex(Request $request)
    {
        $req    = $request->all();
        $return = [];

        $fields = [
            'a.acc_batch_no'         => 'accessory_purchases.batch_no',
            'a.purchase_date_on'     => 'accessory_purchases.purchase_date',
            'a.po_no'                => 'accessory_purchases.po_no',
            'a.supplier_name'        => 'sup.name',
            'a.exp_date_on'          => 'accessory_purchases.exp_date',
            'a.qty'                  => 'accessory_purchases.qty',
            'a.purchase_cost_format' => 'accessory_purchases.purchase_price',
            'a.loc_name'             => 'loc.name',
            'a.dep_name'             => 'dep.name',
            'a.last_updated_at'      => 'accessory_purchases.updated_at',
        ];

        $db = AccessoryPurchase::leftJoin('accessories as a', 'a.id', 'accessory_purchases.batch_no');
        $db->leftJoin('companies as cmp', 'cmp.id', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', 'a.location_id');
        $db->leftJoin('departments as dep', 'dep.id', 'a.department_id');
        $db->leftJoin('suppliers as sup', 'sup.id', 'accessory_purchases.purchase_by');
        $db->where('accessory_purchases.batch_no', $request->accessory_id);
        $db->select(
            'accessory_purchases.batch_no',
            'accessory_purchases.po_no',
            'accessory_purchases.qty',
            'accessory_purchases.attachment',
            'accessory_purchases.id as accessory_purchases_id',
            'accessory_purchases.currency',
            'accessory_purchases.purchase_price',
            'sup.name as supplier_name',
            'loc.name as loc_name',
            'dep.name as dep_name'
        );
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","AC",a.id) else "" end as acc_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","A",a.id) else "" end as acc_batch_no'));
        }
        $db->addSelect(DB::raw('DATE_FORMAT(accessory_purchases.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(accessory_purchases.exp_date, "%d %b %Y") as exp_date_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(accessory_purchases.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        $db->addSelect(DB::raw('case when accessory_purchases.purchase_price then FORMAT(accessory_purchases.purchase_price, 2) else "0.00" end as purchase_cost_format'));

        $return['recordsTotal']    = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('((case when a.id is not null then concat_ws("","AC",a.id) else "" end) like "%%%1$s%%" or loc.name like "%%%1$s%%"  or dep.name like "%%%1$s%%" or sup.name like "%%%1$s%%" or accessory_purchases.qty like "%%%1$s%%" or accessory_purchases.po_no like "%%%1$s%%" or DATE_FORMAT(accessory_purchases.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(accessory_purchases.exp_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or accessory_purchases.purchase_price like "%%%1$s%%" or DATE_FORMAT(accessory_purchases.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('((case when a.id is not null then concat_ws("","A",a.id) else "" end) like "%%%1$s%%" or loc.name like "%%%1$s%%"  or dep.name like "%%%1$s%%" or sup.name like "%%%1$s%%" or accessory_purchases.qty like "%%%1$s%%" or accessory_purchases.po_no like "%%%1$s%%" or DATE_FORMAT(accessory_purchases.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(accessory_purchases.exp_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or accessory_purchases.purchase_price like "%%%1$s%%" or DATE_FORMAT(accessory_purchases.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ["asc", "desc"])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }
        $db->skip($skip);
        $db->take($take);

        $data           = $db->get();
        $return['data'] = [];
        foreach ($data as $d) {
            $symbol                  = Currency::getSymbolByCode($d->currency);
            $d->purchase_cost_format = $symbol . ' ' . $d->purchase_cost_format;
            $return['data'][]        = ['a' => $d];
        }

        return response()->json($return);
    }

    public function deletePurchaseAccessory(Request $request)
    {
        $return = ["status" => "error", "msg" => trans('content.accessory_fields.unable_to_delete_purchase_accessory')];
        if (! Auth::user()->hasPermissionTo('PurchaseDelete') || ! config("services.assets.enabled")) {
            $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
            return response()->json($return);
        }
        $rules = [
            "id" => "sometimes|integer|min:1",
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $v             = $validator->errors()->toArray();
            $e             = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $objAccessoryPurchase = AccessoryPurchase::where('id', $request->id)->first();
        if ($objAccessoryPurchase == null) {
            return response()->json($return);
        }
        $accessory = Accessory::where('id', $objAccessoryPurchase->batch_no)->withCount('users', 'places', 'devices')->first();
        if ($accessory->qty > 0) {
            $qtyAll = ($accessory->qty - ($accessory->users_count + $accessory->devices_count + $accessory->places_count + $accessory->scrap_qty)) - $objAccessoryPurchase->qty;
            if ($qtyAll >= 1) {
                $accessory->update([
                    'qty' => $qtyAll + $accessory->users_count + $accessory->devices_count + $accessory->places_count + $accessory->scrap_qty,
                ]);
                if ($objAccessoryPurchase->attachment != null) {
                    Storage::disk("documents")->delete($objAccessoryPurchase->attachment);
                }
                $objAccessoryPurchase->delete();
            } else {
                if (($accessory->users_count + $accessory->devices_count + $accessory->places_count + $accessory->scrap_qty) > 0) {
                    $return = ['status' => 'error', 'msg' => $accessory->users_count + $accessory->devices_count + $accessory->places_count + $accessory->scrap_qty . ' ' . trans('content.accessory_fields.of_these_accessories_delete')];
                } else {
                    $return = ['status' => 'error', 'msg' => trans("content.accessory_fields.delete_purchase_accessories_insufficient")];
                }
                return response()->json($return);
            }
        }
        $return["status"] = "success";
        $return["msg"]    = trans('content.accessory_fields.purchase_accessory_delete_successfully');
        return response()->json($return);
    }

    public function viewAccessoryPurchaseAttachments(Request $request, $id)
    {
        try {
            $ap = AccessoryPurchase::find($id);
            if (
                ! $ap ||
                empty($ap->attachment) ||
                ! Storage::disk('documents')->exists($ap->attachment)
            ) {
                throw new \Exception("Invalid File");
            }
            $path = Storage::disk('documents')->path($ap->attachment);
            return response()->file($path);
        } catch (\Exception $e) {
            Log::error("viewAccessoryPurchaseAttachments: " . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'File not found',
            ], 404);
        }
    }

    public function getPurchaseAccessoryDetail($id, Request $request)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('PurchaseEdit') || ! config("services.assets.enabled")) {
            return response()->json($return);
        }

        $record = AccessoryPurchase::where('id', $id)->first();

        if (empty($record)) {
            return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);
        }

        $accessory = Accessory::where('id', $record->batch_no)->first();
        if (! Company::checkUserAccess($accessory)) {
            return response()->json(['status' => 'error', 'msg' => 'Insufficient permission for this purchase !!']);
        }

        $record->purchase_date = CommonHelper::getDateAs($record->purchase_date, "d/m/Y", "Y-m-d");
        $record->exp_date      = CommonHelper::getDateAs($record->exp_date, "d/m/Y", "Y-m-d");
        $dev["dropdown"]       = [];

        if ($record->purchase_by) {
            $getSupplier                    = Supplier::where("id", $record->purchase_by)->select("id", "name as text")->first();
            $dev["dropdown"]["purchase_by"] = $getSupplier && $getSupplier->exists ? $getSupplier->toArray() : null;
        }
        if ($record->currency) {
            $getCurrency = Currency::getCurrencies();
            $currencyKey = $record->currency;
            if (array_key_exists($currencyKey, $getCurrency)) {
                $currencyData     = $getCurrency[$currencyKey];
                $concatenatedText = $currencyData['name'] . ' ' . $currencyData['symbol'];
                $filteredCurrency = [
                    "id"   => $currencyKey,
                    "text" => $concatenatedText,
                ];
                $dev["dropdown"]["currency"] = $filteredCurrency;
            } else {
                $dev["dropdown"]["currency"] = null;
            }
        }
        $record->dev      = $dev;
        $return['status'] = 'success';
        $return['data'][] = $record;
        return response()->json($return);
    }

    public function updatePurchaseAccessory(Request $request, $id)
    {
        if (! Auth::user()->hasPermissionTo('PurchaseEdit') || ! config("services.assets.enabled")) {
            $return = ['status' => 'error', 'section' => 'accessory-edit', 'msg' => trans('content.user_fields.Permission_denied')];
            return response()->json($return);
        }
        $appSettings = Settings::first();
        if (empty(Auth::user()->company_id)) {
            return response()->json(['status' => 'error', 'section' => 'accessory-edit', 'msg' => 'You are not allowed for this process !! Please update your company ID and try again !!']);
        }

        try {
            DB::beginTransaction();
            $objAccessoryPurchase = AccessoryPurchase::where('id', $id)->first();
            $rules                = [
                'po_number'      => 'nullable|clean_text_only',
                'invoice_no'     => 'nullable|string',
                'batch_no'       => 'required|integer',
                'purchase_date'  => 'nullable|date_format:d/m/Y',
                'exp_date'       => 'nullable|date_format:d/m/Y|after_or_equal:purchase_date',
                'currency'       => 'nullable',
                'bill_amount'    => 'nullable|numeric',
                'qty'            => 'required|integer|min:1',
                "purchase_by"    => "nullable|integer|exists:suppliers,id",
                'attachment'     => 'sometimes|mimes:png,gif,jpg,jpeg,doc,docx,pdf,txt,zip,rar,eml,msg,mbox,pst,xlsx,xls|max:2000',
                'delete_img_pur' => 'nullable',

            ];
            $messages = [
                'qty.integer'             => 'Quantity must be an Integer',
                'qty.required'            => 'Quantity field is required',
                'exp_date.after_or_equal' => 'The exp date must be after or equal to the purchase date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                foreach ($validator->errors()->getMessages() as $key => $value) {
                    $return['status'] = 'error';
                    $return['msg']    = $value;
                }
                return response()->json($return);
            } else {
                $data                                = $request->validate($rules);
                $accessory                           = Accessory::where('id', $data['batch_no'])->withCount('users', 'places', 'devices')->first();
                $objAccessoryPurchase->batch_no      = $data['batch_no'];
                $objAccessoryPurchase->po_no         = isset($data['po_number']) ? $data['po_number'] : null;
                $objAccessoryPurchase->invoice_no    = isset($data['invoice_no']) ? $data['invoice_no'] : null;
                $objAccessoryPurchase->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d/m/Y") : null;
                $objAccessoryPurchase->exp_date      = isset($request->exp_date) ? CommonHelper::getDateAs($request->exp_date, "Y-m-d", "d/m/Y") : null;
                $objAccessoryPurchase->currency      = empty($request->currency) ? $appSettings->default_currency : $request->currency;
                // $objAccessoryPurchase->purchase_price = $data['bill_amount'];
                if ($objAccessoryPurchase->qty > $data['qty']) {
                    $qtyTotal = (($accessory->qty - ($accessory->users_count + $accessory->devices_count + $accessory->places_count + $accessory->scrap_qty)) - ($objAccessoryPurchase->qty - $data['qty']));
                    if ($qtyTotal < 0) {
                        $return = ['status' => 'error', 'msg' => $accessory->users_count + $accessory->devices_count + $accessory->places_count + $accessory->scrap_qty . ' ' . trans('content.accessory_fields.of_these_accessories')];
                        return response()->json($return);
                    }
                    $qty                       = ($accessory->qty - $objAccessoryPurchase->qty) + $data['qty'];
                    $objAccessoryPurchase->qty = $data['qty'];
                } else {
                    $qty                       = ($accessory->qty - $objAccessoryPurchase->qty) + $data['qty'];
                    $objAccessoryPurchase->qty = $data['qty'];
                }
                if ($objAccessoryPurchase->purchase_price > $data['bill_amount']) {
                    $purchase_cost                        = ($accessory->purchase_cost - $objAccessoryPurchase->purchase_price) + $data['bill_amount'];
                    $objAccessoryPurchase->purchase_price = $data['bill_amount'];
                } else {
                    $purchase_cost                        = ($accessory->purchase_cost - $objAccessoryPurchase->purchase_price) + $data['bill_amount'];
                    $objAccessoryPurchase->purchase_price = $data['bill_amount'];
                }
                $objAccessoryPurchase->purchase_by = isset($data['purchase_by']) ? $data['purchase_by'] : null;

                if (isset($data['delete_img_pur']) && $data['delete_img_pur'] == 1 || isset($data['attachment']) && $data['attachment'] != null) {
                    if ($objAccessoryPurchase->attachment != null) {
                        $exits1 = File::exists(public_path('/uploads/documents/' . $objAccessoryPurchase->attachment));
                        if ($exits1) {
                            File::delete(public_path('/uploads/documents/' . $objAccessoryPurchase->attachment));
                            $objAccessoryPurchase->attachment = null;
                        }
                    }
                }

                if (isset($data['attachment']) && $data['attachment'] != null) {
                    $filePath        = date("Y") . '/' . date('m') . '/' . date('d');
                    $checkFolderPath = CommonHelper::attachmentFolderStructure('accessory', $filePath, 'documents');
                    // dd($checkFolderPath);
                    // if (!$checkFolderPath) {
                    //     return $this->fail(500, " Directory not found", '');
                    // }
                    if (! $checkFolderPath) {

                        return response()->json([
                            'status'  => false,
                            'message' => 'Directory not found',
                        ], 500);
                    }

                    $path                             = Storage::disk("documents")->putFile('accessory/' . $filePath, $data['attachment']);
                    $objAccessoryPurchase->attachment = isset($path) ? $path : null;
                }
                if ($objAccessoryPurchase->save()) {
                    $accessory->update([
                        'qty'           => $qty,
                        'purchase_cost' => $purchase_cost,
                    ]);
                }
                DB::commit();
                $return["msg"]    = trans('content.accessory_fields.purchase_accessory_updated_successfully');
                $return["status"] = "success";
                Log::info("purchase acc update store id:" . $objAccessoryPurchase->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
                return response()->json($return);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("addPurchaseAccessory : " . $e->getMessage());
        }
    }

    public function viewInfoTab(Request $request, $id)
    {
        // dd($id);
        $accessory      = Accessory::leftjoin('departments as dep', 'dep.id', 'accessories.department_id')->select('accessories.*', 'dep.name as department')->where("accessories.id", "=", $id)->first();
        $customFieldset = [];
        if (Settings::first()->accessories_custom_fieldset_id != null) {
            $fieldsetObj = CustomFieldset::where('id', Settings::first()->accessories_custom_fieldset_id)->first();
            if (! empty($fieldsetObj)) {
                $customFieldset = CommonHelper::getCustomData($fieldsetObj->fields, json_decode($accessory->accessories_custom_fields, true));
            }
        }
        $path = null;

        if ($accessory->exists) {
            $available_qty   = $accessory->availableQty();
            $currency_symbol = Currency::getSymbolByCode($accessory->purchase_currency);

            if ($accessory->image != null && file_exists(public_path('uploads/accessories/' . $accessory->image))) {
                $path = asset('uploads/accessories/' . $accessory->image);
            } else {
                $cat_img_path = Category::where('id', $accessory->category_id)->first();
                if ($cat_img_path != null) {
                    if ($cat_img_path->image_thumbnail != null && file_exists(public_path('uploads/category/' . $cat_img_path->image_thumbnail))) {
                        $path = asset('uploads/category/' . $cat_img_path->image_thumbnail);
                    }
                }

                $man_img_path = Manufacture::where('id', $accessory->manufacturer_id)->first();
                if ($man_img_path != null) {
                    if ($man_img_path->attachment != null && file_exists(public_path('uploads/manufacturers/' . $man_img_path->attachment))) {
                        $path = asset('uploads/manufacturers/' . $man_img_path->attachment);
                    }
                }
            }
            return view("accessories.info_tab")->with(compact("path", "accessory", "available_qty", "currency_symbol", "customFieldset"));
        }
    }

    public function ajaxUsers(Request $request)
    {
        $req    = $request->all();
        $return = [
            "draw" => date('is'),
        ];

        $fields = [
            '1' => 'target_name',
            '2' => 'target_type',
            '3' => 'au.expected_checkin',
            '4' => 'al.note',
        ];

        $db = DB::table('accessories_users as au');
        $db->leftJoin('accessories as ac', 'ac.id', '=', 'au.accessory_id');
        $db->leftJoin('categories as c', 'c.id', '=', 'ac.category_id');
        $db->leftJoin('users as u', 'u.id', '=', 'au.assigned_to');
        $db->leftJoin('users as actioner', 'actioner.id', '=', 'au.user_id');
        $db->leftJoin('assets as a', 'a.id', '=', 'au.assigned_to');
        $db->leftJoin('places as p', 'p.id', '=', 'au.assigned_to');
        $db->leftJoin('asset_logs as al', function ($q) {
            $q->on('al.accessory_id', '=', 'au.accessory_id');
            $q->on('al.id', '=', 'au.device_id');
            $q->where('al.asset_type', '=', 'accessory');
            $q->where('al.action_type', '=', 'checkout');
        });
        $db->addSelect(DB::raw('DISTINCT case when u.displayName is not null then u.displayName else concat(u.first_name, " ", u.last_name) end as fullname'), 'ac.name', 'al.note', 'u.id', 'al.assigned_to_type');
        $db->addSelect(DB::raw('case  when actioner.displayName is not null then actioner.displayName else concat(actioner.first_name, " ", actioner.last_name) end as actioner_name'));
        $db->addSelect(DB::raw('case when au.assigned_for = 1 then concat( case when u.displayName is not null then u.displayName else concat(u.first_name, " ", u.last_name)  end, " - ", u.username) when au.assigned_for = 2 then p.place when au.assigned_for = 3 then a.asset_tag end as target_name'));
        $db->addSelect(DB::raw('case when au.assigned_for = 1 then "User" when au.assigned_for = 2 then "Place" when au.assigned_for = 3 then "Device" end as target_type'));
        $db->addSelect(DB::raw('case when au.assigned_for = 1 then u.id when au.assigned_for = 2 then p.id when au.assigned_for = 3 then a.id end as target_id'));
        $db->addSelect('u.id as user_id', 'au.id', 'c.name as cat_name', 'a.id as device_id', 'a.asset_tag', DB::raw('concat(u.first_name, " ", u.last_name) as full_name'));
        $db->addSelect('au.created_at as acc_created_date');
        $db->addSelect(DB::raw('case when au.expected_checkin is not null then DATE_FORMAT(au.expected_checkin, "%d %b %Y") else "" end as expected_checkin_format'));

        $db->where("au.accessory_id", "=", $req["accessory_id"]);

        $return['recordsTotal']    = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $whereStr = sprintf('((case when au.assigned_for = 1 then "User" when au.assigned_for = 2 then "Place" when au.assigned_for = 3 then "Device" end) like  "%%%1$s%%" or (case when au.assigned_for = 1 then concat(case when u.displayName is not null then u.displayName else concat(u.first_name, " ", u.last_name) end, " - ", u.username) when au.assigned_for = 2 then p.place when au.assigned_for = 3 then a.asset_tag end) like  "%%%1$s%%" or DATE_FORMAT(au.expected_checkin, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" )', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
            $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }
        $db->skip($skip);
        $db->take($take);

        $data           = $db->get();
        $return['data'] = [];
        foreach ($data as $d) {
            $return['data'][] = ['a' => $d];
        }

        return response()->json($return);
    }

    public function ajaxHistory(Request $request)
    {
        $req    = $request->all();
        $return = [
            "draw" => date('is'),
        ];

        $fields = [
            '0' => 'al.created_at',
            '1' => 'adm_full_name',
            '2' => 'action_type',
            '3' => 'target_name',
            '4' => 'asset_tag',
            '5' => 'note',
        ];

        $db = DB::table('asset_logs as al');
        $db->leftJoin('users as u', 'u.id', '=', 'al.checkedout_to');
        $db->leftJoin('users as adm', 'adm.id', '=', 'al.user_id');
        $db->leftJoin('assets as a', 'a.id', '=', 'al.checkedout_to');
        $db->leftJoin('places as p', 'p.id', '=', 'al.checkedout_to');
        $db->leftJoin('accessory_records as ar', 'ar.accessory_log_id', '=', 'al.id'); // add this 

        $db->addSelect(DB::raw('case when al.assigned_for = 1 then concat_ws(" ", u.first_name, u.last_name, "-", u.username) when al.assigned_for = 2 then p.place when al.assigned_for = 3 then a.asset_tag end as target_name'));
        $db->addSelect(DB::raw('case when al.assigned_for = 1 then "User" when al.assigned_for = 2 then "Place" when al.assigned_for = 3 then "Device" end as target_type'));

        $db->where('al.asset_type', '=', 'accessory');
        $db->whereNull('al.filename');
        if (isset($req["accessory_id"]) && $req["accessory_id"]) {
            // $db->where('al.accessory_id', '=', $req["accessory_id"]);
            $db->where('al.asset_id', '=', $req["accessory_id"]);
        }
        // $db->addSelect('al.id', 'al.action_type', 'al.note', 'adm.id as adminuserid', 'a.asset_tag', 'a.id as device_id', 'u.id as user_id');
        $db->addSelect('al.id', 'al.asset_id', 'al.action_type', 'al.note', 'ar.id as accessory_record_id', 'al.interact_id', 'al.interact_type', 'al.interact_module', 'adm.id as adminuserid', 'a.asset_tag', 'a.id as device_id', 'u.id as user_id');
        $db->addSelect(DB::raw('concat(adm.first_name, " ", adm.last_name) as adm_full_name'));
        $db->addSelect(DB::raw('DATE_FORMAT(al.created_at, "%d %b %Y %h:%i %p") as created_at_format'));

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $whereStr = sprintf('(al.action_type like "%%%1$s%%" or al.note like "%%%1$s%%" or case when al.assigned_for = 1 then concat_ws(" ", u.first_name, u.last_name, "-", u.username) when al.assigned_for = 2 then p.place when al.assigned_for = 3 then a.asset_tag end like "%%%1$s%%" or concat(adm.first_name, " ", adm.last_name) like "%%%1$s%%" or case when al.assigned_for = 1 then "User" when al.assigned_for = 2 then "Place" when al.assigned_for = 3 then "Device" end like "%%%1$s%%" or DATE_FORMAT(al.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
            $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }
        $db->skip($skip);
        $db->take($take);

        $data           = $db->get();
        $return['data'] = [];
        foreach ($data as $d) {
            $return['data'][] = ['a' => $d];
        }
        return response()->json($return);
    }

    public function checkin(Request $request)
    {
        $return = [
            "status" => "danger",
            "msg" => trans('content.accessory_fields.Unable_to_checkin_given_accessory'),
        ];
        if (! Auth::user()->hasPermissionTo('AccessoriesCheckin') || ! config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data     = $request->only("id", "note", "scrap_qty");
        $validate = Validator::make($data, [
            "id"        => "required|integer|min:1",
            "scrap_qty" => "nullable|integer|min:0",
            "note"      => [
                Rule::requiredIf(fn() => config('app.client') === 'knightfrank'),
                'nullable',
                'clean_text_only',
                'max:255',
            ],
        ]);

        if ($validate->fails()) {
            $v             = $validate->errors()->toArray();
            $e             = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $id     = $data["id"];
        $accUsr = AccessoryUser::find($request->id);
        if (! $accUsr) {
            $return["msg"] = trans('content.accessory_fields.Invalid_Access');
            return response()->json($return);
        }

        $acc = Accessory::where("id", "=", $accUsr->accessory_id)->first();
        if (! $acc) {
            $return["msg"] = trans('content.accessory_fields.Accessory_currently');
            return response()->json($return);
        }

        if (! Auth::user()->isSuperUser() && ! Company::checkUserAccess($acc)) {
            // $return["status"] = 'error';
            $return["section"] = 'accessory-checkin';
            $return["msg"]     = Auth::user()->company_id == null ? trans('content.accessory_fields.update_company_name') : trans('content.multiple_company_access');
            return response()->json($return);
        }

        /* know checkin form user (or) place (or) device */
        $checkin_from = $accUsr->assigned_for;
        // $accessory_checkout_id = $accUsr->device_id;

        if (! $accUsr->delete()) {
            $return["msg"] = trans('content.accessory_fields.Unable_to_checkin_back_the_accessory');
            return response()->json($return);
        }

        if ($request->scrap_qty > 0) {
            $acc->scrap_qty = $acc->scrap_qty ? $acc->scrap_qty + 1 : 1;
            $acc->save();
        }

        $user   = "";
        $place  = "";
        $device = "";
        if ($checkin_from == 1) {
            $user = User::find($accUsr->assigned_to);
        } elseif ($checkin_from == 2) {
            $place = Place::find($accUsr->assigned_to);
        } elseif ($checkin_from == 3) {
            $device = Device::find($accUsr->assigned_to);
        }

        $log = Actionlog::accessoryCheckin([
            'asset_id'         => $acc->id,
            'accessory_id'     => $acc->id,
            'checkedout_to'    => $accUsr->assigned_to,
            'assigned_for'     => $accUsr->assigned_for,
            'assigned_to_type' => $checkin_from,
            'location_id'      => $accUsr->assigned_for == 3 ? optional($device)->location_id : ($accUsr->assigned_for == 2 ? optional($place)->location_id : optional($user)->location_id),
            'note'             => e($request->note),
            'user_id'          => Auth::user()->id,
            'in_out_id'        => $accUsr->device_id,
        ]);
        //To update the in_out_id for checkout entry
        DB::table('asset_logs')->where('id', $accUsr->device_id)->update(['in_out_id' => $log->id]);

        /* mail */

        if (Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        if (config('mail.service_enabled') && $acc->isNeedCheckinMail() && $user && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            if (isset($alertnotify) && is_array($alertnotify)) {
                foreach ($alertnotify as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $cc_emails[] = $email;
                    }
                }
                Mail::to($user->email)->cc($cc_emails)->queue(new AccessoryCheckinNotification($acc, $log, $user));
            } else {
                Mail::to($user->email)->queue(new AccessoryCheckinNotification($acc, $log, $user));
            }
        }

        $return["status"] = "success";
        $return["msg"]    = trans('content.accessory_fields.Accessory_has_been_checked_successfully');
        return response()->json($return);
    }

    public function expectedCheckin(Request $request, $id)
    {
        try {
            $expectedCheckin = CommonHelper::getDateAs($request->expected_checkin, 'Y-m-d', 'd/m/Y');

            DB::table('accessories_users')->where('id', $id)->update([
                'expected_checkin' => $expectedCheckin,
                'updated_at'       => now(),
            ]);

            $return["status"] = "success";
            $return["msg"]    = trans('content.accessory_fields.Expected_checkin_date_updated_successfully');
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error("updateexpectedcheckindate : " . $e->getMessage());
        }
    }

    public function checkout(Request $request, $technician = null)
    {
        $return = [
            "status" => "danger",
            "msg"    => trans('content.accessory_fields.Unable_to_checkout_this_accessory'),
        ];
        if (! Auth::user()->hasPermissionTo('AccessoriesCheckout') && $technician == null || ! config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only("id", "assigned_to", "assigned_for", "assigned_place", "expected_checkin", "device_id", "note");
        if (! isset($data["assigned_to"])) {
            $data["assigned_to"] = null;
        }
        if (! isset($data["device_id"])) {
            $data["device_id"] = null;
        }
        $validate = Validator::make($data, [
            "id"               => [
                "required",
                Rule::exists("accessories")->where(function ($q) {
                    $q->whereNull("deleted_at");
                }),
            ],
            "assigned_for"     => "required|integer|min:1|max:3",
            "assigned_to"      => [
                "nullable",
                "required_if:assigned_for,1",
                Rule::exists("users", "id")->where(function ($q) {}),
            ],
            "assigned_place"   => [
                "nullable",
                "required_if:assigned_for,2",
                Rule::exists("places", "id"),
            ],
            "device_id"        => [
                "nullable",
                "required_if:assigned_for,3",
                Rule::exists("assets", "id")->where(function ($q) {
                    $q->whereNull("deleted_at");
                }),
            ],
            "expected_checkin" => "nullable|date_format:d/m/Y",
            "note"             => [
                Rule::requiredIf(fn() => config('app.client') === 'knightfrank'),
                'nullable',
                'clean_text_only',
                'max:255',
            ],
        ]);

        if ($validate->fails()) {
            $v             = $validate->errors()->toArray();
            $e             = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $id                 = $data["id"];
        $target_checkout_to = $request->assigned_for;
        $acc                = Accessory::find($id);

        if (! Auth::user()->isSuperUser() && ! Company::checkUserAccess($acc)) {
            $return["section"] = 'accessory-checkout';
            $return["msg"]     = Auth::user()->company_id == null ? trans('content.accessory_fields.update_company_name') : trans('content.multiple_company_access');
            return response()->json($return);
        }

        if ($acc->availableQty() < 1) {
            $return["msg"] = trans('content.accessory_fields.This_accessory_quantity');
            return response()->json($return);
        }

        $target_assigned    = 0;
        $target_location_id = 0;

        $attach_data = [
            "accessory_id"     => $acc->id,
            "expected_checkin" => CommonHelper::getDateAs($request->expected_checkin, 'Y-m-d', 'd/m/Y'),
            "user_id"          => Auth::user()->id,
            "assigned_for"     => $target_checkout_to,
        ];
        $target_assigned_name = "";
        if ($target_checkout_to == 3) {
            $device               = Device::find($request->device_id);
            $target_assigned_name = $device->name;
            // if($device->status->sold == 1 ){
            //     $return["msg"] = trans('content.accessory_fields.Chosen_Device_is_in_sold_status');
            //     return response()->json($return);
            // }
            if ($device->status->stolen_item == 1) {
                $return["msg"] = trans('content.accessory_fields.Chosen_Device_is_in_lost');
                return response()->json($return);
            }
            $target_assigned            = $request->device_id;
            $target_location_id         = $device->rtd_location_id;
            $attach_data["assigned_to"] = $target_assigned;
            $user                       = User::find($device->assigned_to);
        } elseif ($target_checkout_to == 2) {
            $place                      = Place::find($request->assigned_place);
            $target_assigned_name       = $place->place;
            $target_assigned            = $request->assigned_place;
            $target_location_id         = $place->location_id;
            $attach_data["assigned_to"] = $target_assigned;
        } elseif ($target_checkout_to == 1) {
            $user                 = User::find($request->assigned_to);
            $target_assigned_name = ! empty($user->displayName) ? $user->displayName : trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            if (empty($user)) {
                $return["msg"] = trans('content.accessory_fields.Chosen_User_is_not_found');
                return response()->json($return);
            }
            if ($user->checkoutBasicClearance()) {
                $return["msg"] = trans('content.accessory_fields.Chosen_User_is_not_in_Active_Status');
                return response()->json($return);
            }
            if ($user->checkLastWorkingDate()) {
                $return["msg"] = trans('content.accessory_fields.This_users_last_working_date');
                return response()->json($return);
            }
            $target_assigned            = $request->assigned_to;
            $target_location_id         = $user->location_id;
            $attach_data["assigned_to"] = $target_assigned;
            // $acc->users()->attach($acc->id, $attach_data);

        }
        $acc->touch();

        // trigger checkout
        $log = Actionlog::accessoryCheckout($acc->id, $target_checkout_to, $target_assigned, $target_location_id, Auth::id(), $request->note);
        $log->asset_type       = "accessory";
        $log->checkedout_to    = $target_assigned;
        $log->location_id      = $target_location_id;
        $log->assigned_to_type = $target_checkout_to;
        $log->assigned_for     = $target_checkout_to;
        $log->accessory_id     = $acc->id;
        $log->note             = $request->note;
        $log->user_id          = Auth::user()->id;
        $log->action_type      = "checkout";
        $log->save();
        $attach_data["device_id"] = $log->id;
        $attach_data["ticket_id"] = $request->ticket_id ?? null;

        if ($target_checkout_to == 3) {
            $acc->devices()->attach($acc->id, $attach_data);
            $target_chkout_to = "Device";
        } elseif ($target_checkout_to == 2) {
            $acc->places()->attach($acc->id, $attach_data);
            $target_chkout_to = "Place";
        } elseif ($target_checkout_to == 1) {
            $acc->users()->attach($acc->id, $attach_data);
            $target_chkout_to = "User";
        }

        /* mail */
        $alertnotify = null;
        if (Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }

        // if(config('mail.service_enabled')) {
        //     if(($acc->requireAcceptance() || $acc->getEula())) {
        //         if($target_checkout_to == 1 && $user && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
        //             if($alertnotify) {
        //                 Mail::to($user->email)->cc($alertnotify)->queue(new AccessoryCheckoutNotification($acc, $log, $user));
        //             }
        //             else {
        //                 Mail::to($user->email)->queue(new AccessoryCheckoutNotification($acc, $log, $user));
        //             }
        //         }
        //     }
        // }
        if (config('mail.service_enabled')) {
            if (($acc->requireAcceptance() || $acc->getEula())) {
                if (($target_checkout_to == 1 && $user) || ($target_checkout_to == 3 && $user) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    if ($alertnotify) {
                        Mail::to($user->email)->cc($alertnotify)->queue(new AccessoryCheckoutNotification($acc, $log, $user, $target_chkout_to, $target_assigned_name));
                    } else {
                        Mail::to($user->email)->queue(new AccessoryCheckoutNotification($acc, $log, $user, $target_chkout_to, $target_assigned_name));
                    }
                }
            }
        }

        $this->notifythresholdAlert($acc->id, $acc->category_id);
        $return["status"] = "success";
        $return["msg"]    = trans('content.accessory_fields.Accessory_has_been_checked_out_successfully');
        Log::info("Acc checkout id:" . $acc->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
        return response()->json($return);
    }

    public function scrapAccessory(Request $request)
    {
        $return = [
            "status" => "danger",
            "msg"    => 'Unable To Scrap',
        ];
        $data = $request->only("id", "scrap_qty");

        $validate = Validator::make($data, [
            "scrap_qty" => [
                "required",
                "numeric",
            ],
        ]);

        if ($validate->fails()) {
            $v             = $validate->errors()->toArray();
            $e             = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $id            = $data["id"];
        $acc           = Accessory::find($id);
        $available_qty = $acc->availableQty();

        $scrapQty     = $data["scrap_qty"];
        $availableQty = $acc->availableQty();

        if ($scrapQty > $availableQty) {
            return response()->json([
                'success' => false,
                'msg'     => "Scrap quantity cannot be more than available quantity ($availableQty).",
            ]);
        }
        $newScrapQty    = (int) $data['scrap_qty'];
        $acc->scrap_qty = (int) $acc->scrap_qty + $newScrapQty;
        $acc->save();
        $log = Actionlog::accessoryScrap($acc, Auth::id(), "Scrap quantity: {$newScrapQty}");
        $admin = Auth::user()->email;
        try {
            if (config('mail.service_enabled') && filter_var($admin, FILTER_VALIDATE_EMAIL)) {
                $alertnotify      = Settings::first()->alerts_enabled == 1 ? CommonHelper::getGlobalAlertEmail() : [];
                $validAlertEmails = array_filter($alertnotify, function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });

                if ($admin) {
                    Mail::to($admin)->cc($validAlertEmails)->queue(new AccessoryScrap($log, $acc->company_id));
                } elseif (! empty($validAlertEmails)) {
                    Mail::to($validAlertEmails)->queue(new AccessoryScrap($log, $acc->company_id));
                }
            }
        } catch (\Exception $e) {
            Log::error('Acc Scrap Email Failed: ' . $e->getMessage());
        }

        $return["status"] = "success";
        $return["msg"]    = "Accessory has been scrapped successfully.";
        return response()->json($return);
    }

    public function revertScrap(Request $request)
    {
        $return = [
            "status" => "danger",
            "msg"    => 'Unable To Scrap',
        ];
        $data = $request->only("id", "scrap_qty");

        $validate = Validator::make($data, [
            "scrap_qty" => [
                "required",
                "numeric",
            ],
        ]);

        if ($validate->fails()) {
            $v             = $validate->errors()->toArray();
            $e             = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $id            = $data["id"];
        $acc           = Accessory::find($id);
        $available_qty = $acc->availableQty();

        $scrapQty = $data["scrap_qty"];
        if ($scrapQty > $acc->scrap_qty) {
            return response()->json([
                'success' => false,
                'msg'     => "Revert scrap quantity cannot be more than available quantity ($scrapQty).",
            ]);
        }
        $newScrapQty    = (int) $data['scrap_qty'];
        $acc->scrap_qty = (int) $acc->scrap_qty - $newScrapQty;
        $acc->save();

        $log = Actionlog::accessoryRestoreScrap($acc, Auth::id(),"Revert scrap quantity: {$newScrapQty}");
        $admin = Auth::user()->email;
        try {
            if (config('mail.service_enabled') && filter_var($admin, FILTER_VALIDATE_EMAIL)) {
                $alertnotify      = Settings::first()->alerts_enabled == 1 ? CommonHelper::getGlobalAlertEmail() : [];
                $validAlertEmails = array_filter($alertnotify, function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });

                if ($admin) {
                    Mail::to($admin)->cc($validAlertEmails)->queue(new AccessoryScrapRevert($log));
                } elseif (! empty($validAlertEmails)) {
                    Mail::to($validAlertEmails)->queue(new AccessoryScrapRevert($log));
                }
            }
        } catch (\Exception $e) {
            Log::error('Acc Scrap Revert Email Failed: ' . $e->getMessage());
        }

        $return["status"] = "success";
        $return["msg"]    = "Accessory has been  revert scrapped successfully.";
        return response()->json($return);
    }

    public function acceptCheckout(Request $request, $id)
    {
        $log = Actionlog::where("id", "=", $id)->first();
        if (! $log || ! $log->exists) {
            return redirect("dashboard")->with("status", "Invalid Access");
        }

        if (Auth::user()->id != $log->checkedout_to) {
            return redirect("dashboard")->with("status", "Invalid Access");
        }

        $item = false;
        if ($log->accessory_id && $log->asset_type == "accessory") {
            $item = Accessory::where("id", "=", $log->accessory_id)->first();
        }

        if (! $item || ! $item->exists) {
            return redirect("dashboard")->with("status", "Accessory does not exist.");
        }

        if ($log->accepted_id) {
            return redirect("dashboard")->with("status", "Accessory has been already accepted.");
        }

        // needs to redirect requestable page if item present but permission no 
        if ($request->method() == "POST") {

            if (! $request->has("confirm")) {
                return view("accessories.checkout_confirm")->withLog($log)->withAccessory($item)->withStatus("Please choose correct option");
            }

            $confirmation = $request->input("confirm");

            $confLog               = new ActionLog();
            $confLog->asset_id     = null;
            $confLog->accessory_id = $log->accessory_id;
            $confLog->asset_type   = "accessory";

            $confirmation_msg = trans('content.accessory_fields.accepted');
            $notification_msg = trans('content.accessory_fields.You_have_successfully_accepted_the_accessory');
            if (! $confirmation) {
                $confirmation_msg = trans('content.accessory_fields.declined');
                $notification_msg = trans('content.accessory_fields.You_have_successfully_declined_the_accessory');
            }

            $confLog->checkedout_to = $log->checkedout_to;
            $confLog->user_id       = Auth::user()->id;
            $confLog->accepted_at   = date("Y-m-d h:i:s");
            $confLog->action_type   = $confirmation_msg;
            $confLog->note          = e($request->note);
            $confLog->save();

            // update log 
            $log->accepted_id = $confLog->id;
            $log->save();

            return redirect("dashboard")->with("status", $notification_msg);
        }

        return view("accessories.checkout_confirm")->withLog($log)->withAccessory($item);
    }

    public function notifythresholdAlert($acc_id, $category_id)
    {

        $thresholdSettings = ThresholdSettings::first();
        if (empty($thresholdSettings) || ($thresholdSettings->threshold_enabled === null) || ($thresholdSettings->alerts_enabled === null)) {
            Log::info('Threshold settings are not configured.');
            return;
        }

        //Get accessory Details By Id
        $accDetails         = Accessory::where('id', $acc_id)->first();
        $accThreshold       = $accDetails->accessory_thresholds;
        $totAccThreshold    = $accDetails->qty;
        $total_chkout_acc   = Accessory::getCheckoutAccTotalById($acc_id)[0]->total_checkouts;
        $availableSingleAcc = $totAccThreshold - $total_chkout_acc;

        //Get Details By Category
        $availableAccessory = $thresouldCatValue = 0;
        if (! empty($category_id)) {
            $thresouldDetail   = Threshold::where('cat_id', $category_id)->first();
            $thresouldCatValue = ! empty($thresouldDetail) ? $thresouldDetail->threshold : 0;
        }
        $alertmail           = [];
        $thresholdUserEmails = ThresholdAlertSettings::where('threshold_alert_settings.asset_id', $acc_id)
            ->where('threshold_alert_settings.asset_type', 2)
            ->join('users', 'users.id', '=', 'threshold_alert_settings.user_id')
            ->whereNotNull('users.email')
            ->pluck('users.email')
            ->toArray();
        if (! empty($thresholdUserEmails)) {
            $alertmail = array_merge($alertmail, $thresholdUserEmails);
        }

        if (config('mail.service_enabled') && ! empty($alertmail) && ($accThreshold > 0) && ($availableSingleAcc <= $accThreshold)) {
            Mail::to($alertmail)->send(new AccessoryThreshouldNotification($accDetails, $accThreshold, $availableSingleAcc, $totAccThreshold));
        } elseif (config('mail.service_enabled') && ($thresouldCatValue > 0) && ($availableSingleAcc <= $thresouldCatValue && $thresouldDetail->alerts_enabled == 1)) {
            $categoryAlertsUsers = [];
            if ($thresholdSettings->send_alerts == 0) {
                $categoryAlertsUsers = array_merge($categoryAlertsUsers, CommonHelper::getGlobalAlertEmail());
            } else {
                $categoryAlertsUsers = array_merge($categoryAlertsUsers, explode(',', $thresholdSettings->email));
            }
            $catDetail = Category::find($category_id);
            Mail::to($categoryAlertsUsers)->send(new ThreshouldNotification($catDetail, $thresouldCatValue, $availableSingleAcc));
            $threshold = Threshold::where('cat_id', $catDetail->id)->first();
            if ($threshold) {
                $threshold->notify_count       = $threshold->notify_count + 1;
                $threshold->last_notified_date = date('Y-m-d');
                $threshold->save();
            }
        }
    }

    public function notifyCategoryThresould($category_id)
    {
        $alertnotify = [];
        if (Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }

        $alertmail         = null;
        $thresholdSettings = ThresholdSettings::first();
        $thresouldDtl      = Threshold::where('cat_id', '=', $category_id)->first();

        $total_accessories        = Accessory::getCatAccessoriesTotal($category_id)[0]->total_accessories;
        $total_chkout_accessories = Accessory::getCheckoutAccessoriesTotalByCat($category_id)[0]->total_checkouts;

        $availableAccessories = $total_accessories - $total_chkout_accessories;

        if (empty($thresouldDtl)) {
            return;
        }

        if ($thresholdSettings->threshold_enabled && $thresholdSettings->alerts_enabled && $thresouldDtl->alerts_enabled) {
            if ($thresholdSettings->send_alerts == 0) {
                if (Settings::first()->alerts_enabled == 1) {
                    $alertmail = CommonHelper::getGlobalAlertEmail();
                }
            }
            if ($thresholdSettings->send_alerts == 1) {
                $alertmail = $thresholdSettings->email;
            }
        }
        if (! empty($alertmail) && $thresouldDtl->threshold > 0) {
            $catDetail = Category::find($category_id);
            if ($availableAccessories < $thresouldDtl->threshold) {
                if (config('mail.service_enabled')) {
                    Mail::to($alertmail)->cc($alertnotify)->queue(new ThreshouldNotification($catDetail, $thresouldDtl->threshold, $availableAccessories));
                    $thresouldDtl->notify_count       = $thresouldDtl->notify_count + 1;
                    $thresouldDtl->last_notified_date = date('Y-m-d');
                }
                $thresouldDtl->save();
            }
        }
    }

    public function exportAccessory($accessory_id, Request $request)
    {

        $return = ['status' => 'danger', 'msg' => trans('content.accessory_fields.Unable_to_export_the_Details')];
        if (! Auth::user()->hasPermissionTo('AccessoriesDownload') || ! config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $db = DB::table('accessories_users as au');
        $db->leftJoin('users as u', 'u.id', '=', 'au.assigned_to');
        $db->leftJoin('asset_logs as al', function ($q) {
            $q->on('al.accessory_id', '=', 'au.accessory_id')
                ->on('al.id', '=', 'au.device_id')
                ->where('al.asset_type', '=', 'accessory')
                ->where('al.action_type', '=', 'checkout');
        });
        $db->leftJoin('users as actioner', 'actioner.id', '=', 'au.user_id');
        $db->leftJoin('assets as a', 'a.id', '=', 'au.assigned_to');
        $db->leftJoin('places as p', 'p.id', '=', 'au.assigned_to');
        $db->leftJoin('accessories as ac', 'ac.id', '=', 'au.accessory_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'ac.company_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'ac.category_id');

        $db->select(DB::raw('u.username as fullname'));
        $db->select(DB::raw('concat_ws(" ", actioner.first_name, actioner.last_name) as actioner_name'));
        $db->addSelect('ac.name as name', 'cat.name as category_name', 'cmp.name as company_name');
        $db->addSelect(DB::raw('case when au.assigned_for = 1 then concat_ws(" ", u.first_name, u.last_name, "-", u.username) when au.assigned_for = 2 then p.place when au.assigned_for = 3 then a.asset_tag end as target_name'));
        $db->addSelect(DB::raw('case when au.assigned_for = 1 then "User" when au.assigned_for = 2 then "Place" when au.assigned_for = 3 then "Device" end as target_type'));
        $db->addSelect('u.id as user_id', 'au.id', 'a.id as device_id', 'a.asset_tag', DB::raw('concat(u.first_name, " ", u.last_name) as full_name'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('concat("AC", ac.id) as tag'));
        } else {
            $db->addSelect(DB::raw('concat("A", ac.id) as tag'));
        }
        $db->addSelect(DB::raw('case when au.expected_checkin is not null then DATE_FORMAT(au.expected_checkin, "%d %b %Y") else "" end as expected_checkin_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(ac.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('al.note as notes'));
        $db->where("au.accessory_id", "=", $accessory_id);

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters         = json_decode($request_filters);
            if (isset($filters->search) && $search_key = trim($filters->search)) {
                $whereStr = sprintf('((case when au.assigned_for = 1 then "User" when au.assigned_for = 2 then "Place" when au.assigned_for = 3 then "Device" end) like  "%%%1$s%%" or (case when au.assigned_for = 1 then concat_ws(" ", u.first_name, u.last_name, "-", u.username) when au.assigned_for = 2 then p.place when au.assigned_for = 3 then a.asset_tag end) like  "%%%1$s%%" or DATE_FORMAT(au.expected_checkin, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" )', $search_key);
                $db->whereRaw($whereStr);
                $return['recordsFiltered'] = $db->count();
            }
        }

        $return['recordsTotal']    = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        $records = $db->get();

        $data = [];
        foreach ($records as $r) {
            $data[] = [
                $r->tag,
                $r->name,
                $r->category_name,
                $r->company_name,
                $r->target_type,
                $r->target_name,
                $r->purchase_date_on,
                $r->expected_checkin_format,
                $r->notes,
            ];
        }

        return Excel::download(new AccessoryInfoExport($data), 'Accessory Checkout Details.xlsx');
    }

    public function pdfexportAccessory($accessory_id, Request $request)
    {

        $return = ['status' => 'danger', 'msg' => trans('content.accessory_fields.Unable_to_export_the_Details')];
        $vd     = [];
        if (! Auth::user()->hasPermissionTo('AccessoriesRead') || ! config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $db = DB::table('accessories_users as au');
        $db->leftJoin('users as u', 'u.id', '=', 'au.assigned_to');
        $db->leftJoin('users as actioner', 'actioner.id', '=', 'au.user_id');
        $db->leftJoin('assets as a', 'a.id', '=', 'au.assigned_to');
        $db->leftJoin('places as p', 'p.id', '=', 'au.assigned_to');
        $db->leftJoin('accessories as ac', 'ac.id', '=', 'au.accessory_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'ac.company_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'ac.category_id');

        $db->select(DB::raw('u.username as fullname'));
        $db->select(DB::raw('concat_ws(" ", actioner.first_name, actioner.last_name) as actioner_name'));
        $db->addSelect('ac.name as name', 'cat.name as category_name', 'cmp.name as company_name');
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('concat("AC", ac.id) as tag'));
        } else {
            $db->addSelect(DB::raw('concat("A", ac.id) as tag'));
        }
        $db->addSelect(DB::raw('case when au.assigned_for = 1 then concat_ws(" ", u.first_name, u.last_name, "-", u.username) when au.assigned_for = 2 then p.place when au.assigned_for = 3 then a.asset_tag end as target_name'));
        $db->addSelect(DB::raw('case when au.assigned_for = 1 then "User" when au.assigned_for = 2 then "Place" when au.assigned_for = 3 then "Device" end as target_type'));
        $db->addSelect('u.id as user_id', 'au.id', 'a.id as device_id', 'a.asset_tag', DB::raw('concat(u.first_name, " ", u.last_name) as full_name'));
        $db->addSelect(DB::raw('case when au.expected_checkin is not null then DATE_FORMAT(au.expected_checkin, "%d %b %Y") else "" end as expected_checkin_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(ac.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->where("au.accessory_id", "=", $accessory_id);

        $return['recordsTotal']    = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters         = json_decode($request_filters);
            if (isset($filters->search) && $search_key = trim($filters->search)) {
                $whereStr = sprintf('((case when au.assigned_for = 1 then "User" when au.assigned_for = 2 then "Place" when au.assigned_for = 3 then "Device" end) like  "%%%1$s%%" or (case when au.assigned_for = 1 then concat_ws(" ", u.first_name, u.last_name, "-", u.username) when au.assigned_for = 2 then p.place when au.assigned_for = 3 then a.asset_tag end) like  "%%%1$s%%" or DATE_FORMAT(au.expected_checkin, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" )', $search_key);
                $db->whereRaw($whereStr);
                $return['recordsFiltered'] = $db->count();
            }
        }

        $data = $db->get();
        $vd['records'] = $data;
        return PDF::view('accessories.for_pdf_export', $vd)->format('a4')->name('Accessory Checkout Details.pdf')->download();
    }

    public function accessoryImport(Request $request)
    {
        $return = ["msg" => "Unable to import the given accessory", "status" => "danger"];
        if (! Auth::user()->hasPermissionTo('AccessoriesDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $settings = Settings::first();
        if ($request->isMethod('post') && !$request->import_file) {
            $return["msg"] = trans('content.download_format.error_accessory_upload');
            $request->session()->flash("msg", $return);
        } else {
        if (! $request->isMethod('post')) {
            return view("accessories.import")->with("settings", $settings);
        }
            if ($request->isMethod('post') && $request->import_file) {
                $path = $request->file('import_file')->getRealPath();
            $import = new AccessoryImport($request);
                Excel::import($import, $request->import_file);
            $response = $import->data;
                // return view("accessories.import")->with("settings", $settings)->with(['success' => $response['success'], 'fail' => $response['fail'], 'fail_msgs' => $response['fail_msgs'] ]);
                return redirect()->back()->with("settings", $settings)->with(['success' => $response['success'], 'fail' => $response['fail'], 'fail_msgs' => $response['fail_msgs']]);
            }
        }
        return view("accessories.import")->with("settings", $settings);
    }

    public function downloadCustomFieldsCodeAccessories(Request $request)
    {

        $customFieldset = CustomFieldset::where('id', Settings::first()->accessories_custom_fieldset_id)->first();
        $fields         = [];
        if (! empty($customFieldset)) {
            if (count($customFieldset->fields) > 0) {
                foreach ($customFieldset->fields as $f) {
                    $col_name = $f->name;
                    $fields[] = CustomField::where('name', $f->name)->first();
                }
            }
        }

        $result = json_decode(json_encode($fields, true), true);
        return Excel::download(new CustomFieldsAccessories($result), 'AccessoriesCustomFields.xlsx');
    }
    public function ajaxRequestableAccessory(Request $request)
    {
        $req = $request->all();

        $fields = [
            '1' => 'a.batch_no',
            '2' => 'name',
            '3' => 'cat.name',
            '4' => 'loc.name',
            '5' => 'qty',
            '6' => 'remaining',
        ];

        $db = DB::table('accessories as a');
        $db->leftJoin(DB::raw('(SELECT accessory_id, count(id) as tot_assigns FROM `accessories_users` group by accessory_id) au'), function ($j) {
            $j->on('a.id', '=', 'au.accessory_id');
        });
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->leftJoin(DB::raw('(SELECT MAX(id) as id, accessory_id FROM asset_logs WHERE asset_type = "accessory" AND user_id = ' . Auth::id() . ' AND accepted_id IS NULL GROUP BY accessory_id) al'), 'al.accessory_id', '=', 'a.id');
        $db->where("a.requestable_accessory", "=", 1);
        $db->whereNull('a.deleted_at');
        $db->select('a.id', 'al.id as requested_id', 'a.name', 'loc.name as loc_name', 'cat.name as cat_name', 'a.qty', 'dep.name as department', 'a.image');
        $db->addSelect(DB::raw('case when au.tot_assigns is not null then (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)) ) when a.scrap_qty > 0 then (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0)) else COALESCE(a.qty, 0) end as remaining'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('concat("AC", a.id) as batch_no'));
        } else {
            $db->addSelect(DB::raw('concat("A", a.id) as batch_no'));
        }
        $db->havingRaw('remaining > 0');
        $db->groupBy('a.id', 'a.name', 'loc.name', 'cat.name', 'a.qty', 'dep.name', 'a.image', 'al.id', 'au.tot_assigns', 'a.scrap_qty');

        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings       = Settings::getSettings();
        $permitted_loc  = explode(",", $loc_previllage);
        if ($settings->location_config == 1) {
            if (empty($loc_previllage)) {
                $db->where('a.location_id', '=', 0);
            } else {
                $db->whereIn('a.location_id', $permitted_loc);
            }
        }
        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts           = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }

        $return['recordsTotal']    = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('(concat("AC", a.id) like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%"  or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or (case when au.tot_assigns is not null then (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)) ) when a.scrap_qty > 0 then (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0)) else COALESCE(a.qty, 0) end) like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('(concat("A", a.id) like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%"  or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or (case when au.tot_assigns is not null then (COALESCE(a.qty, 0) - (COALESCE(a.scrap_qty, 0) + COALESCE(au.tot_assigns, 0)) ) when a.scrap_qty > 0 then (COALESCE(a.qty, 0) - COALESCE(a.scrap_qty, 0)) else COALESCE(a.qty, 0) end) like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
            $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }

        $db->skip($skip);
        $db->take($take);

        $data           = $db->get();
        $return['data'] = [];
        foreach ($data as $key => $d) {
            $return['data'][] = ['a' => $d];
        }
        return response()->json($return);
    }

    public function requestAccessory(Request $request, $id)
    {
        $return       = [];
        $appSettings  = Settings::first();
        $objAccessory = Accessory::where('id', '=', $id)->where('requestable_accessory', '=', 1)->whereNull('deleted_at')->first();
        $user         = Auth::user();

        if (! $objAccessory) {
            return response()->json(['status' => 'error', 'msg' => 'Device not found !!']);
        }
        $logaction               = new Actionlog();
        $logaction->accessory_id = $objAccessory->id;
        $logaction->asset_type   = 'accessory';
        $logaction->created_at   = $logaction->requested_at   = date("Y-m-d h:i:s");
        $logaction->location_id  = $user->location_id ? $user->location_id : null;
        $logaction->user_id      = $user->id;
        $logaction->action_type  = 'requested';
        $logaction->save();

        $settings = Settings::getSettings();
        return response()->json(['status' => 'success', 'section' => 'approve-request', 'msg' => 'Accessory requested successfully']);
    }

    public function getAccessoryForDropDown(Request $request)
    {
        $return = [];
        try {
            $search = $request->input("search", "");
            $page   = $request->input("page", 1);
            $skip   = (($page * 10) - 10);

            $db = DB::table("accessories as a");
            if (config("app.client") == "etherealmachines") {
                $db->select("a.id", DB::raw("concat_ws('-', concat('AC', a.id), a.name) as text"));
            } else {
                $db->select("a.id", DB::raw("concat_ws('-', concat('A', a.id), a.name) as text"));
            }
            $db->whereNull('a.deleted_at');
            if ($search) {
                $db->whereRaw("a.name like '%" . $search . "%')");
            }
            $count = $db->count();
            $db->skip($skip)->take(10);
            $result               = $db->get();
            $return["pagination"] = ["more" => ($count - ($page * 20)) > 0 ? true : false];
            $return["results"]    = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error("getAccForDropDown: " . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getAccessoryChangeInfo(Request $request, $id)
    {
        $accessoryCacheObj = AccessoryCache::select('accessory_caches.*','cat.name as category_name','cat_old.name as category_name_old','l.name as location_name','l_old.name as location_name_old','d.name as department_name','d_old.name as department_name_old','s.name as supplier_name','s_old.name as supplier_name_old','m.name as manufacturer_name','m_old.name as manufacturer_name_old','companies.name as company_name','c_old.name as company_name_old','purchases.invoice_no as purchase_name','p_old.invoice_no as purchase_name_old','place.place as place_name','place_old.place as place_name_old')
            ->leftJoin('asset_logs as al', 'al.id', '=', 'accessory_caches.accessory_log_id')
            ->leftJoin('accessory_records as ar', 'ar.id', '=', 'accessory_caches.accessory_record_id')
            ->leftJoin('categories as cat', 'cat.id', '=', 'ar.category_id')
            ->leftJoin('categories as cat_old', 'cat_old.id', '=', 'accessory_caches.category_id')
            ->leftJoin('locations as l', 'l.id', '=', 'ar.location_id')
            ->leftJoin('locations as l_old', 'l_old.id', '=', 'accessory_caches.location_id')
            ->leftJoin('departments as d', 'd.id', '=', 'ar.department_id')
            ->leftJoin('departments as d_old', 'd_old.id', '=', 'accessory_caches.department_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'ar.supplier_id')
            ->leftJoin('suppliers as s_old', 's_old.id', '=', 'accessory_caches.supplier_id')
            ->leftJoin('manufacturers as m', 'm.id', '=', 'ar.manufacturer_id')
            ->leftJoin('manufacturers as m_old', 'm_old.id', '=', 'accessory_caches.manufacturer_id')
            ->leftJoin('companies', 'companies.id', '=', 'ar.company_id')
            ->leftJoin('companies as c_old', 'c_old.id', '=', 'accessory_caches.company_id')
            ->leftJoin('purchases', 'purchases.id', '=', 'ar.invoice_id')
            ->leftJoin('purchases as p_old', 'p_old.id', '=', 'accessory_caches.invoice_id')
            ->leftJoin('places as place', 'place.id', '=', 'ar.internal_place_id')
            ->leftJoin('places as place_old', 'place_old.id', '=', 'accessory_caches.internal_place_id')
            ->where('accessory_caches.accessory_record_id', $id)
            ->first();

        if (!$accessoryCacheObj) { return response()->json([ 'success' => false, 'message' => 'History not found.']);}
        $accessoryRecord = AccessoryRecord::where('accessory_log_id',$accessoryCacheObj->accessory_log_id)->first();
        $customFieldChanges = [];

        if (Settings::first()->accessories_custom_fieldset_id &&$accessoryRecord) {
            $customFieldset = CustomFieldset::find(Settings::first()->accessories_custom_fieldset_id);
            if ($customFieldset) {
                foreach ($customFieldset->fields as $field) {
                    $column = $field->nameToColumn();
                    $oldValue = $accessoryCacheObj->{$column} ?? null;
                    $newValue = $accessoryRecord->{$column} ?? null;
                    if ($oldValue != $newValue) {
                        $customFieldChanges[] = [
                            'name'   => $field->name,
                            'column' => $column,
                            'old'    => $oldValue,
                            'new'    => $newValue,
                        ];
                    }
                }
            }
        }
        return response()->json(['success' => true,'old' => $accessoryCacheObj,'new' => $accessoryRecord,'customFieldChanges' => $customFieldChanges,]);
    }

}

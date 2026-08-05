<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse as TraitsApiResponse;
use App\Exports\Consumables\ConsumableInfoExport;
use App\Exports\Consumables\ConsumableExport;
use App\Exports\CustomFields;
use App\Imports\Consumables\ConsumableImport;
use App\Models\Category;
use App\Models\ConsumableCustomField;
use App\Models\CustomFieldset;
use App\Models\ThresholdAlertSettings;
use App\Models\CustomField;
use App\Models\Manufacture;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;
use Validator;
use Auth;
use DateTime;
use App\Models\Department;
use App\Models\Consumable;
use App\Models\ConsumableUser;
use App\Models\Actionlog;
use App\Models\Location;
use App\Models\ConsumableHistory;
use App\Models\User;
use App\Models\Company;
use App\Models\Purchase;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\ThresholdSettings;
use App\Models\Threshold;
use App\Models\Place;
use App\Models\Device;
use App\Helpers\Common as CommonHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\Consumables\ConsumableCheckout;
use App\Mail\Consumables\ConsumableCheckin;
use App\Mail\Consumables\ConsumableScrap;
use App\Mail\Consumables\ConsumableScrapRevert;
use App\Mail\Consumables\ConsumableAddNotification;
use App\Mail\Consumables\ThreshouldNotification;
use App\Mail\Consumables\ConsumableThreshouldNotification;
use App\Models\ConsumablePurchase;
use App\Models\Currency;
use App\Models\Procurement\Unit;
use Mail;
use Log;
// use PDF;
use Spatie\LaravelPdf\Facades\Pdf as PDF;
use Intervention\Image\Laravel\Facades\Image;
use stdClass;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Storage;

class ConsumableController extends Controller
{
    use TraitsApiResponse;

    public function index(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.permission_denied')];
        if (!Auth::user()->hasPermissionTo('ConsumableRead') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $purchaseFilter = null;
        if (isset($request->q)) {
            $purchaseFilter = $request->q;
        }
        $vd = new stdClass();
        $companyId = CommonHelper::getSelectedCompanyIds();
        // $companyIds = CommonHelper::getAccessibleCompanyIds();
        $vd->assignedForOptions = Consumable::getAssignedForOptions();
        $vd->places = Place::selectOptions();
        $vd->location = Location::select("id", "name")->whereIn('company_id', $companyId)->get();
        $location = "null";
        $department = isset($request->department) ? $request->department : "null";
        if ($request->location != null && $request->location != "null") {
            $location = $request->location;
        } else if ($request->city != null && $request->city != "null") {
            $id = Location::whereIn('city_id', array($request->city))->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        } else if ($request->states != null && $request->states != "null") {
            $id = Location::whereIn('state_id', array($request->states))->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        } else if ($request->zone != null && $request->zone != "null") {
            $id = Location::whereIn('zone', array($request->zone))->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        } else if ($request->country != null && $request->country != "null") {
            $id = Location::whereIn('country_id', array($request->country))->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        }
        $type = isset($request->type) ? $request->type : "null";
        if (isset($request->cat_name)) {
            $catId = Category::where('name', $request->cat_name)->where('category_type', 'consumable')->select('id')->first();
            $cat_id = isset($catId->id) ? $catId->id : "null";
        } elseif (isset($request->category)) {
            $cat_id = $request->category;
        } else {
            $cat_id = "null";
        }
        $companyFieldset = CustomFieldset::where('id', Settings::first()->consumable_custom_fieldset_id)->first();
        $categories = Category::whereNull("deleted_at")->select("id", "name as text")->where("category_type", "like", "consumable")->get()->toArray();
        $units = Unit::select("id", "name as text")
            ->where("name", "Piece")
            ->get()
            ->toArray();
        $requestable_enabled = config('app.requestable_enabled');
        $companies = Company::select("id", "name as text")->whereNull('deleted_at')->get()->toArray();
        return view("consumables.index")->with([
            "location" => $location,
            "department" => $department,
            'cat_id' => $cat_id,
            'type' => $type,
            "companyFieldset" => $companyFieldset,
            "categories" => $categories,
            "vd" => $vd,
            "units" => $units,
            "requestable_enabled" => $requestable_enabled,
            "purchaseFilter" => $purchaseFilter,
            "companies" => $companies
        ]);
    }

    public function ajaxIndex(Request $request) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            '1' => 'a.name',
            '2' => 'loc.name',
            '3' => 'a.qty',
            '4' => 'a.qty',
            '5' => 'a.consumable_thresholds',
            '6' => 'a.purchase_date',
            '7' => 'a.updated_at'
        );

        $db = DB::table('consumables as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('places as p', 'p.id', '=', 'a.internal_place_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'a.manufacturer_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(assigned_to) as tot_assigns FROM `consumables_users` group by consumable_id) cu'), function ($j) {
            $j->on('a.id', '=', 'cu.consumable_id');
        });

        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');
        $db->select('a.id', 'a.name', 'a.notes', 'a.currency', 'a.company_id', 'cmp.name as cmp_name', 'loc.name as loc_name', 'cat.name as cat_name', 'a.qty', 'a.order_number', 'a.purchase_cost', 'dep.name as department', 'a.image', 'mnu.attachment', 'cat.image_thumbnail as cat_img', 'a.consumable_thresholds', 'unique_tag', 'cat.id as category_id', 'p.place as internal_place', 'loc.branch_code as loc_branch_code', 'a.scrap_qty');
        // $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y %h:%i %p") as purchase_date_on'));
        // $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","CNS",a.id) else "" end as con_batch_no'));  
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(['a.unique_tag', DB::raw("CONCAT_WS('', 'CN', a.id) as con_batch_id")]);
        } else {
            $db->addSelect(['a.unique_tag', DB::raw("CONCAT_WS('', 'CNS', a.id) as con_batch_id")]);
        }

        $dateFormatSql = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(a.purchase_date, '{$dateFormatSql}') as purchase_date_on"));

        $dateTimeFormatSql = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(a.updated_at, '{$dateTimeFormatSql}') as last_updated_at"));

        $db->addSelect(DB::raw('case when a.purchase_cost then FORMAT(a.purchase_cost, 2) else "0.00" end as purchase_cost_format'));
        $db->addSelect(DB::raw('COALESCE(cu.tot_assigns, 0) as tot_assigns'));
        if (isset($req["showDeletedConsumables"]) && $req["showDeletedConsumables"] == "true") {
            $db->whereNotNull('a.deleted_at');
        } else {
            $db->whereNull('a.deleted_at');
        }

        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
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
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);
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
                $db->whereRaw('a.qty > IFNULL(cu.tot_assigns, 0)');
            } elseif ($type_filter == "In use") {
                $db->whereRaw('IFNULL(cu.tot_assigns, 0) > 0');
            }
        }

        if (isset($request->q) && $request->q != null) {
            $decoded = array_map('intval', explode(',', trim(base64_decode($request->q), '"')));
            $db->whereIn('a.id', $decoded);
        }
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["filters"])) {
            $filters = $req["filters"];
            $db->where(function ($query) use ($filters) {
                if (isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                    $query->where("a.location_id", "=", (int) $filters['location']);
                }
                if (isset($filters["purchase_reference"]) && !empty($filters["purchase_reference"]) && $filters['purchase_reference'] != "null") {
                    $query->whereIn("a.invoice_id", $filters['purchase_reference']);
                }
                if (isset($filters["categories"]) && $filters['categories'] && $filters['categories'] != "null") {
                    $query->whereIn("a.category_id", $filters['categories']);
                }
                if (isset($filters["asset_department"]) && $filters['asset_department'] && $filters['asset_department'] != "null") {
                    $query->whereIn("a.department_id", $filters['asset_department']);
                }
            });
            $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.updated_at'];
            if (isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 7) {
                if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                    $daterange = explode(" - ", $filters["date_range"]);
                    $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                    $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                    if ($from_date && $to_date) {
                        $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                        $db->whereRaw($whereStr);
                    }
                }
            }
            $is_searching = true;
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('((case when a.id is not null then concat_ws("","CN",a.id) else "" end) like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or p.place like "%%%1$s%%"  or loc.branch_code like "%%%1$s%%" or p.place like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('((case when a.id is not null then concat_ws("","CNS",a.id) else "" end) like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or p.place like "%%%1$s%%"  or loc.branch_code like "%%%1$s%%" or p.place like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        // if( isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
        //     $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
        // }

        if (isset($req["order"][0]["column"]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
            $col = (string) $req["order"][0]["column"];
            $dir = $req["order"][0]["dir"];

            if ($col === '4') {
                // Avail column: qty - assigned - scrap
                $db->orderByRaw('(a.qty - COALESCE(cu.tot_assigns, 0) - COALESCE(a.scrap_qty, 0)) ' . $dir);
            } elseif ($col === '5') {
                // Scrap column
                $db->orderBy('a.scrap_qty', $dir);
            } elseif ($col === '6') {
                //Threshhold
                $db->orderBy('a.consumable_thresholds', $dir);
            } elseif ($col === '8') {
                // Updated_On column in the table header
                $db->orderBy('a.updated_at', $dir);
            } elseif (isset($fields[$col])) {
                $db->orderBy($fields[$col], $dir);
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

        $data = $db->get();
        $return['data'] = array();
        foreach ($data as $d) {
            if ($d->category_id) {
                $cat_threshold = Threshold::where('cat_id', $d->category_id)->first();
            }
            $catThreshold = isset($cat_threshold->threshold) ? $cat_threshold->threshold : 0;
            $d->catthreshold = $catThreshold;
            $allocated = Consumable::where('id', $d->id)->withCount(['users', 'places', 'device'])->first();
            $allocated_user_qty = $allocated->users_count ?? 0;
            $allocated_place_qty = $allocated->places_count ?? 0;
            $allocated_device_qty = $allocated->device_count ?? 0;
            $allocated_qty = $allocated_user_qty + $allocated_place_qty + $allocated_device_qty;
            $d->available_qty = $d->qty - $allocated_qty - $d->scrap_qty;
            $d->allocated_qty = $allocated_qty;
            $d->consumable_img = (
                ($d->image != "" && file_exists(storage_path('app/public/uploads/consumable/' . $d->image))) ? url("storage/uploads/consumable") . "/" . $d->image :
                (($d->attachment != "" && file_exists(public_path('uploads/manufacturers/' . $d->attachment))) ? url("uploads/manufacturers") . "/" . $d->attachment :
                    (($d->cat_img != "" && file_exists(public_path('uploads/category/' . $d->cat_img))) ? url("uploads/category") . "/" . $d->cat_img : null))
            );
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    public function consumablesExport(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('ConsumableDownload') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $req = $request->all();
        $return = array(
            // "draw" => date('is')
        );

        $db = DB::table('consumables as a');
        $db->leftJoin('consumable_custom_field as ccf', 'ccf.consumable_id', '=', 'a.id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('places as p', 'p.id', '=', 'a.internal_place_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('manufacturers as manu', 'manu.id', '=', 'a.manufacturer_id');
        $db->leftJoin('suppliers as sup', 'sup.id', '=', 'a.supplier_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(assigned_to) as tot_assigns FROM `consumables_users` group by consumable_id) cu'), function ($j) {
            $j->on('a.id', '=', 'cu.consumable_id');
        });
        $db->select('a.*', 'cmp.name as cmp_name', 'loc.name as loc_name', 'cat.name as cat_name', 'manu.name as manu_name', 'sup.name as sup_name', 'dep.name as department', 'p.place as internal_place', 'loc.branch_code as loc_branch_code', 'a.scrap_qty', 'ccf.*');
        // $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y %h:%i %p") as purchase_date_on'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","CN",a.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","CNS",a.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw("a.unique_tag as unique_tag"));
        $dateTimeFormatSql = CommonHelper::mysqlDateTimeFormat('datetime', 'excel');
        $dateFormatSql = CommonHelper::mysqlDateTimeFormat('date', 'excel');
        $db->addSelect(DB::raw("DATE_FORMAT(a.purchase_date, '{$dateFormatSql}') as purchase_date_on"));
        $db->addSelect(DB::raw("DATE_FORMAT(a.created_at, '{$dateTimeFormatSql}') as created_at"));
        $db->addSelect(DB::raw("DATE_FORMAT(a.updated_at, '{$dateTimeFormatSql}') as last_updated_at"));
        $db->addSelect(DB::raw('case when a.purchase_cost then FORMAT(a.purchase_cost, 2) else "0.00" end as purchase_cost_format'));
        $db->addSelect(DB::raw('COALESCE(cu.tot_assigns, 0) as tot_assigns'));
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);

        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
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
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if (isset($filters->showDeletedConsumables) && $filters->showDeletedConsumables == true) {
                $db->whereNotNull('a.deleted_at');
            } else {
                $db->whereNull('a.deleted_at');
            }
            $return['recordsTotal'] = $db->count();
            $return['recordsFiltered'] = $return['recordsTotal'];
            if (isset($filters->dashboard_filters)) {
                $req["dashboard_filters"] = (array) $filters->dashboard_filters;
                $location_filter = isset($req["dashboard_filters"]['location']) ? $req["dashboard_filters"]['location'] : "null";
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
                        $db->whereRaw('a.qty > IFNULL(cu.tot_assigns, 0)');
                    } elseif ($type_filter == "In use") {
                        $db->whereRaw('IFNULL(cu.tot_assigns, 0) > 0');
                    }
                }
            }
            if (isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if (isset($filters->other_filters)) {
                $req["filters"] = (array) $filters->other_filters;
            }
            if (isset($filters->over_all_purchase_filter)) {
                $ids = array_map('intval', explode(',', trim(base64_decode($filters->over_all_purchase_filter), '"')));
                $db->whereIn('a.id', $ids);
            }
            if (isset($req["filters"])) {
                $filters = $req["filters"];
                $db->where(function ($query) use ($filters) {
                    if (isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                        $query->where("a.location_id", "=", (int) $filters['location']);
                    }
                    if (isset($filters["purchase_reference"]) && !empty($filters["purchase_reference"]) && $filters['purchase_reference'] != "null") {
                        $query->whereIn("a.invoice_id", $filters['purchase_reference']);
                    }
                    if (isset($filters["categories"]) && $filters['categories'] && $filters['categories'] != "null") {
                        $query->whereIn("a.category_id", $filters['categories']);
                    }
                    if (isset($filters["asset_department"]) && $filters['asset_department'] && $filters['asset_department'] != "null") {
                        $query->whereIn("a.department_id", $filters['asset_department']);
                    }
                });
                $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.updated_at'];
                if (isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2) {
                    if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                        $daterange = explode(" - ", $filters["date_range"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if ($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $db->whereRaw($whereStr);
                        }
                    }
                }
                $is_searching = true;
            }
            if (isset($req["search"]) && $search_key = trim($req["search"])) {
                if (config("app.client") == "etherealmachines") {
                    $whereStr = sprintf('((case when a.id is not null then concat_ws("","CN",a.id) else "" end) like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or p.place like "%%%1$s%%"  or loc.branch_code like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                } else {
                    $whereStr = sprintf('((case when a.id is not null then concat_ws("","CNS",a.id) else "" end) like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or p.place like "%%%1$s%%"  or loc.branch_code like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                }
                $db->whereRaw($whereStr);
                $return['recordsFiltered'] = $db->count();
            }
        }

        $data = $db->get();
        $lineArray = [];
        $keys = ["Batch Code", "Unique Tag", "Consumable Name", "Company", "Category", "Department", "Manufacture", "Location", "Internal Place", "Supplier", "Order Number", "Purchase Date", "Purchase Currency", "Purchase Cost", "Total", "Issued Quantity", "Scrap Qty", "Threshold", "Remaining", "Notes", "Created At", "Updated At"];
        $settings = Settings::first();
        $customFields = [];
        $prefixed_code = [];
        if ($settings->consumable_custom_fieldset_id != "") {
            $customFieldset = CustomFieldset::find($settings->consumable_custom_fieldset_id);
            if (!empty($customFieldset->fields)) {
                foreach ($customFieldset->fields as $f) {
                    array_push($keys, $f->name);
                }
            }
        }
        foreach ($data as $key => $val) {
            $allocated = Consumable::where('id', $val->id)->withCount(['users', 'places', 'device'])->first();
            $allocated_user_qty = $allocated->users_count ?? 0;
            $allocated_place_qty = $allocated->places_count ?? 0;
            $allocated_device_qty = $allocated->device_count ?? 0;
            $allocated_qty = $allocated_user_qty + $allocated_place_qty + $allocated_device_qty;
            $available_qty = $val->qty - $allocated_qty - $val->scrap_qty;

            $line = [
                $val->con_batch_no,
                $val->unique_tag,
                $val->name,
                $val->cmp_name,
                $val->cat_name,
                $val->department,
                $val->manu_name,
                $val->loc_name,
                $val->internal_place,
                $val->sup_name,
                $val->order_number,
                $val->purchase_date_on,
                $val->currency,
                $val->purchase_cost_format,
                $val->qty,
                $val->tot_assigns,
                $val->scrap_qty,
                $val->consumable_thresholds,
                $available_qty,
                $val->notes,
                $val->created_at,
                $val->last_updated_at
            ];
            if ($settings->consumable_custom_fieldset_id) {
                if (!empty($customFieldset)) {
                    $customaCol = json_decode(json_encode($val), true);
                    $fieldData = CommonHelper::getCustomData($customFieldset->fields, $customaCol);
                    $dataFieldset = [];
                    foreach ($fieldData as $k => $f) {
                        $col_name = ucwords(str_replace(['_itm_', '_'], ' ', $k));
                        $dataFieldset[$col_name] = $f;
                    }
                    $line = array_merge($line, array_values($dataFieldset));

                }
            }
            $consumable = Consumable::find($val->id);
            if (isset($consumable->category_id) && isset($consumable->category->customFieldset) && count($consumable->category->customFieldset->fields)) {
                foreach ($consumable->category->customFieldset->fields as $field) {
                    $con = '_itm_' . '' . str_replace(' ', '_', strtolower($field->name));
                    if (!in_array(ucwords($field->name), $keys)) {
                        array_push($keys, ucwords($field->name));
                    }
                    $modelCusValue = CommonHelper::getCustomDataFormate($field, $consumable->$con);
                    array_push($line, $modelCusValue);
                }
            }
            $lineArray[] = $line;
        }
        $result = json_decode(json_encode($lineArray, true), true);
        return Excel::download(new ConsumableExport($result, $keys), 'ConsumableExport.xlsx');
    }

    public function consumablesExportPDF(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Unable_to_export_the_details')];
        if (!Auth::user()->hasPermissionTo('ConsumableDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = [];

        $db = DB::table('consumables as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');

        $db->leftJoin(DB::raw('(SELECT consumable_id, count(assigned_to) as tot_assigns FROM `consumables_users` group by consumable_id) cu'), function ($j) {
            $j->on('a.id', '=', 'cu.consumable_id');
        });

        $db->select('a.id', 'a.name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'cat.name as cat_name', 'a.qty', 'a.scrap_qty', 'a.order_number', 'a.consumable_thresholds', 'a.currency', 'a.unique_tag');
        // $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y %h:%i %p") as purchase_date_on'));
        // $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","CNS",a.id) else "" end as con_batch_no'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw("CASE WHEN a.id IS NOT NULL THEN CONCAT_WS('', 'CN', a.id) ELSE '' END as con_batch_no"));
        } else {
            $db->addSelect(DB::raw("CASE WHEN a.id IS NOT NULL THEN CONCAT_WS('', 'CNS', a.id) ELSE '' END as con_batch_no"));
        }
        $dateFormatSql = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(a.purchase_date, '{$dateFormatSql}') as purchase_date_on"));
        $db->addSelect(DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when cu.tot_assigns is not null then (COALESCE(a.qty, 0) - COALESCE(cu.tot_assigns, 0)) else COALESCE(a.qty, 0) end as remaining'));

        if (isset($request["showDeletedConsumables"]) && $request["showDeletedConsumables"] == "true") {
            $db->whereNotNull('a.deleted_at');
        } else {
            $db->whereNull('a.deleted_at');
        }

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);

        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if ($settings->location_config == 1) {
            if (empty($loc_previllage)) {
                $db->where('a.location_id', '=', 0);
            } else {
                $db->whereIn('a.location_id', $permitted_loc);
            }
        }
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if (isset($filters->showDeletedConsumables) && $filters->showDeletedConsumables == true) {
                $db->whereNotNull('a.deleted_at');
            } else {
                $db->whereNull('a.deleted_at');
            }
            $return['recordsTotal'] = $db->count();
            $return['recordsFiltered'] = $return['recordsTotal'];
            if (isset($filters->dashboard_filters)) {
                $req["dashboard_filters"] = (array) $filters->dashboard_filters;
                $location_filter = isset($req["dashboard_filters"]['location']) ? $req["dashboard_filters"]['location'] : "null";
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
                        $db->whereRaw('a.qty > IFNULL(cu.tot_assigns, 0)');
                    } elseif ($type_filter == "In use") {
                        $db->whereRaw('IFNULL(cu.tot_assigns, 0) > 0');
                    }
                }
            }
            if (isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if (isset($filters->other_filters)) {
                $req["filters"] = (array) $filters->other_filters;
            }
            if (isset($req["filters"])) {
                $filters = $req["filters"];
                $db->where(function ($query) use ($filters) {
                    if (isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                        $query->where("a.location_id", "=", (int) $filters['location']);
                    }
                    if (isset($filters["purchase_reference"]) && !empty($filters["purchase_reference"]) && $filters['purchase_reference'] != "null") {
                        $query->whereIn("a.invoice_id", $filters['purchase_reference']);
                    }
                    if (isset($filters["categories"]) && $filters['categories'] && $filters['categories'] != "null") {
                        $query->whereIn("a.category_id", $filters['categories']);
                    }
                    if (isset($filters["asset_department"]) && $filters['asset_department'] && $filters['asset_department'] != "null") {
                        $query->whereIn("a.department_id", $filters['asset_department']);
                    }
                });
                $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.updated_at'];
                if (isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2) {
                    if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                        $daterange = explode(" - ", $filters["date_range"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if ($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $db->whereRaw($whereStr);
                        }
                    }
                }
                $is_searching = true;
            }
            if (isset($req["search"]) && $search_key = trim($req["search"])) {
                if (config("app.client") == "etherealmachines") {
                    $whereStr = sprintf('((case when a.id is not null then concat_ws("","CN",a.id) else "" end) like "%%%1$s%%" or a.name like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                } else {
                    $whereStr = sprintf('((case when a.id is not null then concat_ws("","CNS",a.id) else "" end) like "%%%1$s%%" or a.name like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                }
                $db->whereRaw($whereStr);
                $return['recordsFiltered'] = $db->count();
            }
        }
        $data = $db->get();
        $properties = [];
        $properties['format'] = 'A4';

        $vd["records"] = $data;
        // $pdf = PDF::loadView('consumables.for_export', $vd, [], $properties);
        // return $pdf->download('consumables.pdf');
        return PDF::view('consumables.for_export', $vd)->landscape()->format('a4')->name('consumables.pdf')->download();
    }

    public function store(Request $request) {
        if (!Auth::user()->hasPermissionTo('ConsumableAdd') || !config("services.assets.enabled")) {
            $return = ['status' => 'error', 'section' => 'consumable-add', 'msg' => trans('consumables.controller.Permission_denied')];
            return response()->json($return);
        }
        $appSettings = Settings::first();
        if (empty(Auth::user()->company_id))
            return response()->json(['status' => 'error', 'section' => 'consumable-add', 'msg' => trans('consumables.controller.you_are_not_allowed')]);

        // $companyId = CommonHelper::getAccessibleCompanyIds();
        // if (!in_array($request->company_id, $companyId)) {
        //     $msg = "You don't have access to this company.";
        //     return response()->json(["msg" => $msg]);
        // }

        $objConsumable = new Consumable;
        $rules = [
            'unique_tag' => ['nullable', 'clean_text_only', 'max:100', Rule::unique('consumables', 'unique_tag')->where(function ($query) use ($request) {
                return $query->where('location_id', $request->location_id); }),],
            'company_id' => 'required|integer',
            'name' => 'required|clean_text_only|min:3|max:255',
            'category_id' => 'required|integer',
            'location_id' => 'required|integer',
            'order_number' => 'nullable|clean_text_only',
            'purchase_date' => 'nullable|date_format:d/m/Y',
            'currency' => 'nullable',
            'purchase_cost' => 'nullable|numeric',
            'qty' => 'required|integer|min:0',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            "supplier_id" => "nullable|integer|exists:suppliers,id",
            "invoice_id" => "nullable|integer|exists:purchases,id",
            "notes" => "nullable|clean_text_only|max:2000",
            "department_id" => "nullable|integer|exists:departments,id",
            "internal_place" => "nullable|integer|exists:places,id",
            'image' => 'sometimes|mimes:jpeg,bmp,png,jpg',
            "units" => "nullable|integer|exists:procure_units,id",
            "consumable_thresholds" => 'integer|min:0',
        ];
        $messages = [
            'qty.integer' => 'Quantity must be an Integer',
            'qty.required' => 'Quantity field is required',
            'consumable_thresholds.integer' => 'Threshold must be an Integer',
            // 'consumable_thresholds.required' => 'Threshold field is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            //return back()->withInput()->withErrors($validator->messages());
            return array('status' => 'error', 'errors' => $validator->errors()->getMessages());
        } else {
            $data = $request->validate($rules);
            $data['internal_place_id'] = isset($request->internal_place) && !empty($request->internal_place) ? $request->internal_place : null;
            $data_fields = $request->input("fields", null);
            $custom_fields = $custom_fields_val = [];
            $consumable = Category::find($data['category_id']);
            if ($consumable && $consumable->customFieldset && count($consumable->customFieldset->fields)) {
                foreach ($consumable->customFieldset->fields as $f) {
                    $col_name = $f->nameToColumn();
                    $formatType = CommonHelper::convertFormatToRegex($f->format);
                    $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                    // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                    $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                    $custom_fields[] = $col_name;
                    $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
                }
            }
            if (Settings::first()->consumable_custom_fieldset_id != "") {
                $customFieldset = CustomFieldset::where('id', Settings::first()->consumable_custom_fieldset_id)->first();
                if (!empty($customFieldset) && $customFieldset->fields->isNotEmpty()) {
                    foreach ($customFieldset->fields as $f) {
                        $col_name = $f->nameToColumn();
                        $formatType = CommonHelper::convertFormatToRegex($f->format);
                        $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                        // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                        $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                        $custom_fields[] = $col_name;
                        $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
                    }
                }
            }

            $objConsumable->company_id = $appSettings->full_multiple_companies_support == 0 ? (empty($data['company_id']) ? Auth::user()->company_id : $data['company_id']) : Auth::user()->company_id;
            $objConsumable->name = $data['name'];
            $objConsumable->category_id = $data['category_id'];
            $objConsumable->manufacturer_id = $data['manufacturer_id'] ?? null;
            $objConsumable->location_id = $data['location_id'];
            $objConsumable->order_number = $data['order_number'];
            $objConsumable->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d/m/Y") : null;
            $objConsumable->currency = empty($request->currency) ? $appSettings->default_currency : $request->currency;
            $objConsumable->purchase_cost = $data['purchase_cost'];
            $objConsumable->qty = $data['qty'];
            $objConsumable->internal_place_id = $data['internal_place_id'];
            $objConsumable->supplier_id = $request->supplier_id;
            $objConsumable->invoice_id = $request->invoice_id;
            $objConsumable->consumable_thresholds = $request->consumable_thresholds ?? 0;
            $objConsumable->reorder_limits = $request->reorder_limits ?? 0;
            $objConsumable->thresholds_alerts = isset($request->thresholds_alerts) ? $request->thresholds_alerts : 0;
            $objConsumable->notes = trim($request->notes);
            $objConsumable->consumable_custom_fields = null;
            $objConsumable->department_id = $request->department_id;
            $objConsumable->units = $request->unit;
            $objConsumable->requestable = isset($request->requestable_consumables) ? $request->requestable_consumables : 0;

            $objConsumable->image = null;
            if ($request->image) {
                $uploaded_img = $request->image;
                $consumableImage = Str::random(12) . Str::random(12) . '.' . $uploaded_img->getClientOriginalExtension();
                $checkFolderPath = CommonHelper::attachmentFolderStructure('consumable_uploads', 'consumable');
                if (!$checkFolderPath) {
                    return $this->fail(500, " Directory not found", '');
                }
                $directoryPath = storage_path("app/public/uploads/consumable");
                if (!File::exists($directoryPath)) {
                    File::makeDirectory($directoryPath, 0777, true, true);
                }
                $path = $directoryPath . '/' . $consumableImage;
                Image::read($uploaded_img->getRealPath())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $objConsumable->image = $consumableImage;
            } elseif ($request->input("clone_img") != "") {
                if (isset($request->delete_img) == false) {
                    $old_path = storage_path('app/public/uploads/consumable/' . $request->input("clone_img"));
                    if (file_exists($old_path)) {
                        $cloneImgArr = explode(".", $request->input("clone_img"));
                        $consumableImage = Str::random(12) . Str::random(12) . "." . last($cloneImgArr);
                        $checkFolderPath = CommonHelper::attachmentFolderStructure('consumable_uploads', 'consumable');
                        if (!$checkFolderPath) {
                            return $this->fail(500, " Directory not found", '');
                        }
                        $path = storage_path("app/public/uploads/consumable/" . $consumableImage);
                        Image::read($old_path)->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })->save($path);
                        $objConsumable->image = $consumableImage;
                        $objConsumable->updated_at = now();
                        $objConsumable->save();
                    }
                }
            }

            $objConsumable->unique_tag = $data['unique_tag'];
            if ($objConsumable->save()) {
                if ($data["unique_tag"] == "") {
                    $objConsumable->generateUniqueTag();
                }

                if (count($custom_fields_val)) {
                    $consumableCustomField = [
                        'consumable_id' => $objConsumable->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    foreach ($custom_fields_val as $key => $f) {
                        if (is_array($f)) {
                            $consumableCustomField[$key] = json_encode($f);
                        } else {
                            $consumableCustomField[$key] = $f ?? null;
                        }
                    }

                    ConsumableCustomField::insert($consumableCustomField);
                }

                $history = new ConsumableHistory();
                $history->consumable_id = $objConsumable->id;
                $history->name = $objConsumable->name;
                $history->category_id = $objConsumable->category_id;
                $history->location_id = $objConsumable->location_id;
                $history->internal_place_id = $objConsumable->internal_place_id;
                $history->user_id = Auth::id();
                $history->qty = $objConsumable->qty;
                $history->requestable = $objConsumable->requestable;
                $history->purchase_date = $objConsumable->purchase_date;
                $history->currency = $objConsumable->currency;
                $history->purchase_cost = $objConsumable->purchase_cost;
                $history->order_number = $objConsumable->order_number;
                $history->company_id = $objConsumable->company_id;
                $history->manufacturer_id = $objConsumable->manufacturer_id;
                $history->supplier_id = $objConsumable->supplier_id;
                $history->invoice_id = $objConsumable->invoice_id;
                $history->notes = $objConsumable->notes;
                $history->consumable_custom_fields = $objConsumable->consumable_custom_fields;
                $history->department_id = $objConsumable->department_id;
                $history->image = $objConsumable->image;
                $history->units = $objConsumable->units;
                $history->consumable_thresholds = $objConsumable->consumable_thresholds ?? 0;
                $history->thresholds_alerts = $objConsumable->thresholds_alerts;
                $history->reorder_limits = $objConsumable->reorder_limits;
                $history->unique_tag = $objConsumable->unique_tag;
                $history->scrap_qty = $objConsumable->scrap_qty ?? 0;
                $history->changed_by = Auth::id();
                $history->change_type = 'Add';
                $history->save();

                // add entry in consumable purchase 
                $objConsumablePurchase = new ConsumablePurchase();
                $objConsumablePurchase->batch_no = $objConsumable->id;
                $objConsumablePurchase->po_no = $objConsumable->order_number != null ? $objConsumable->order_number : null;
                $objConsumablePurchase->purchase_date = $objConsumable->purchase_date != null ? $objConsumable->purchase_date : date('Y-m-d');
                $objConsumablePurchase->currency = $objConsumable->currency != null ? $objConsumable->currency : $appSettings->default_currency;
                $objConsumablePurchase->purchase_price = $objConsumable->purchase_cost != null ? $objConsumable->purchase_cost : 0.00;
                $objConsumablePurchase->qty = $objConsumable->qty != null ? $objConsumable->qty : 0;
                $objConsumablePurchase->purchase_by = $objConsumable->supplier_id != null ? $objConsumable->supplier_id : null;
                $objConsumablePurchase->save();

                if (!empty($request->threshold_alert_users)) {
                    foreach ($request->threshold_alert_users as $userId) {
                        ThresholdAlertSettings::create([
                            'asset_id' => $objConsumable->id,
                            'asset_type' => 4,
                            'user_id' => $userId,
                            'updated_by' => Auth::user()->id
                        ]);
                    }
                }

                //log this add
                $logaction = new Actionlog();
                $logaction->consumable_id = $objConsumable->id;
                $logaction->action_type = 'New Add';
                $logaction->asset_type = 'consumable';
                $logaction->user_id = Auth::user()->id;
                $logaction->note = $objConsumable->notes;
                $logaction->save();

                $user = Auth::user();
                if (Settings::first()->alerts_enabled == 1) {
                    try {
                        $alertnotify = CommonHelper::getGlobalAlertEmail();
                        if (config('mail.service_enabled') && is_array($alertnotify)) {
                            foreach ($alertnotify as $email) {
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($email)->queue(new ConsumableAddNotification($objConsumable, $user));
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
                $msg["msg"] = trans('consumables.controller.Consumable_added_successfully');
                $msg["status"] = "success";
                Log::info("Con store id:" . $objConsumable->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
                return array($msg);
            }
        }
    }

    public function addPurchaseConsumable(Request $request) {
        if (!Auth::user()->hasPermissionTo('PurchaseAdd') || !config("services.assets.enabled")) {
            $return = ['status' => 'error', 'section' => 'consumable-add', 'msg' => trans('consumables.controller.Permission_denied')];
            return response()->json($return);
        }
        $appSettings = Settings::first();
        if (empty(Auth::user()->company_id))
            return response()->json(['status' => 'error', 'section' => 'purchase-add', 'msg' => 'You are not allowed for this process !! Please update your company ID and try again !!']);

        try {
            DB::beginTransaction();
            $objConsumablePurchase = new ConsumablePurchase();
            $rules = [
                'po_number' => 'nullable|clean_text_only',
                'invoice_no' => 'nullable|clean_text_only',
                'batch_no' => 'required|integer',
                'purchase_date' => 'nullable|date_format:d/m/Y',
                'received_date' => 'nullable|date_format:d/m/Y|after_or_equal:purchase_date',
                'exp_date' => 'nullable|date_format:d/m/Y|after_or_equal:purchase_date',
                'currency' => 'nullable',
                'bill_amount' => 'nullable|numeric',
                'qty' => 'required|integer|min:1',
                "purchase_by" => "nullable|integer|exists:suppliers,id",
                'attachment' => 'sometimes|mimes:png,gif,jpg,jpeg,doc,docx,pdf,txt,zip,rar,eml,msg,mbox,pst,xlsx,xls|max:2000',

            ];
            $messages = [
                'qty.integer' => 'Quantity must be an Integer',
                'qty.required' => 'Quantity field is required',
                'received_date.after_or_equal' => 'The received date must be after or equal to the purchase date.',
                'exp_date.after_or_equal' => 'The exp date must be after or equal to the purchase date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                foreach ($validator->errors()->getMessages() as $key => $value) {
                    $return['status'] = 'error';
                    $return['msg'] = $value;
                }
                return response()->json($return);
            } else {
                $data = $request->validate($rules);
                $consumable = Consumable::where('id', $data['batch_no'])->WithCount('consumableusers')->first();

                $objConsumablePurchase->batch_no = $data['batch_no'];
                $objConsumablePurchase->po_no = isset($data['po_number']) ? $data['po_number'] : null;
                $objConsumablePurchase->invoice_no = isset($data['invoice_no']) ? $data['invoice_no'] : null;
                $objConsumablePurchase->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d/m/Y") : null;
                $objConsumablePurchase->exp_date = isset($request->exp_date) ? CommonHelper::getDateAs($request->exp_date, "Y-m-d", "d/m/Y") : null;
                $objConsumablePurchase->received_date = isset($request->received_date) ? CommonHelper::getDateAs($request->received_date, "Y-m-d", "d/m/Y") : null;
                $objConsumablePurchase->currency = empty($request->currency) ? $appSettings->default_currency : $request->currency;
                $objConsumablePurchase->purchase_price = $data['bill_amount'];
                $objConsumablePurchase->qty = $data['qty'];
                $objConsumablePurchase->purchase_by = isset($data['purchase_by']) ? $data['purchase_by'] : null;

                if (isset($data['attachment']) && $data['attachment'] != null) {
                    $filePath = date("Y") . '/' . date('m') . '/' . date('d');
                    $checkFolderPath = CommonHelper::attachmentFolderStructure('storage_documents', $filePath);
                    if (!$checkFolderPath) {
                        return $this->fail(500, " Directory not found", '');
                    }
                    $path = Storage::disk("storage_documents")->putFile('consumable/' . $filePath, $data['attachment']);
                }
                $objConsumablePurchase->attachment = isset($path) ? $path : null;
                if ($objConsumablePurchase->save()) {
                    $qty = $consumable->qty + $objConsumablePurchase->qty;
                    $purchase_cost = $consumable->purchase_cost + $objConsumablePurchase->purchase_price;
                    $consumable->update([
                        'qty' => $qty,
                        'purchase_cost' => $purchase_cost,
                    ]);
                }
                DB::commit();
                $return["msg"] = trans('consumables.controller.purchase_consumable_added_successfully');
                $return["status"] = "success";
                return response()->json($return);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("addPurchaseConsumable : " . $e->getMessage());
        }
    }

    public function ajaxPurchaseConsumableIndex(Request $request) {
        $req = $request->all();

        $fields = array(
            'a.con_batch_no' => 'consumable_purchases.batch_no',
            'a.purchase_date_on' => 'consumable_purchases.purchase_date',
            'a.po_no' => 'consumable_purchases.po_no',
            'a.supplier_name' => 'sup.name',
            'a.received_date_on' => 'consumable_purchases.received_date',
            'a.exp_date_on' => 'consumable_purchases.exp_date',
            'a.qty' => 'consumable_purchases.qty',
            'a.purchase_cost_format' => 'consumable_purchases.purchase_price',
            'a.loc_name' => 'loc.name',
            'a.dep_name' => 'dep.name',
            'a.last_updated_at' => 'consumable_purchases.updated_at',
        );

        $db = ConsumablePurchase::leftJoin('consumables as a', 'a.id', 'consumable_purchases.batch_no');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->leftJoin('suppliers as sup', 'sup.id', 'consumable_purchases.purchase_by');
        $db->where('consumable_purchases.batch_no', $request->consumable_id);
        $db->select(
            'consumable_purchases.batch_no',
            'consumable_purchases.po_no',
            'consumable_purchases.qty',
            'consumable_purchases.attachment',
            'consumable_purchases.id as consumable_purchases_id',
            'consumable_purchases.currency',
            'consumable_purchases.purchase_price',
            'sup.name as supplier_name',
            'loc.name as loc_name',
            'dep.name as dep_name'
        );
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw("case when a.unique_tag is null then case when a.id is not null then concat_ws('', 'cn', a.id) else '' end else a.unique_tag end as con_batch_no"));
        } else {
            $db->addSelect(DB::raw("case when a.unique_tag is null then case when a.id is not null then concat_ws('', 'cns', a.id) else '' end else a.unique_tag end as con_batch_no"));
        }
        $dateFormatSql = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $dateTimeFormatSql = CommonHelper::mysqlDateTimeFormat('datetime', 'display');

        $db->addSelect(DB::raw("DATE_FORMAT(consumable_purchases.purchase_date, '{$dateFormatSql}') as purchase_date_on"));
        $db->addSelect(DB::raw("DATE_FORMAT(consumable_purchases.exp_date, '{$dateFormatSql}') as exp_date_on"));
        $db->addSelect(DB::raw("DATE_FORMAT(consumable_purchases.received_date, '{$dateFormatSql}') as received_date_on"));
        $db->addSelect(DB::raw("DATE_FORMAT(consumable_purchases.updated_at, '{$dateTimeFormatSql}') as last_updated_at"));
        $db->addSelect(DB::raw('case when consumable_purchases.purchase_price then FORMAT(consumable_purchases.purchase_price, 2) else "0.00" end as purchase_cost_format'));

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('((case when a.id is not null then concat_ws("","CN",a.id) else "" end) like "%%%1$s%%" or loc.name like "%%%1$s%%"  or dep.name like "%%%1$s%%" or sup.name like "%%%1$s%%" or consumable_purchases.qty like "%%%1$s%%" or consumable_purchases.po_no like "%%%1$s%%" or DATE_FORMAT(consumable_purchases.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(consumable_purchases.exp_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(consumable_purchases.received_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or consumable_purchases.purchase_price like "%%%1$s%%" or DATE_FORMAT(consumable_purchases.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('((case when a.id is not null then concat_ws("","CNS",a.id) else "" end) like "%%%1$s%%" or loc.name like "%%%1$s%%"  or dep.name like "%%%1$s%%" or sup.name like "%%%1$s%%" or consumable_purchases.qty like "%%%1$s%%" or consumable_purchases.po_no like "%%%1$s%%" or DATE_FORMAT(consumable_purchases.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(consumable_purchases.exp_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(consumable_purchases.received_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or consumable_purchases.purchase_price like "%%%1$s%%" or DATE_FORMAT(consumable_purchases.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
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

        $data = $db->get();
        $return['data'] = array();
        foreach ($data as $d) {
            $symbol = Currency::getSymbolByCode($d->currency);
            $d->purchase_cost_format = $symbol . ' ' . $d->purchase_cost_format;
            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }

    public function deletePurchaseConsumable(Request $request) {
        $return = array("status" => "error", "msg" => "Unable to delete the purchase consumable");
        if (!Auth::user()->hasPermissionTo('PurchaseDelete') || !config("services.assets.enabled")) {
            $return = ['status' => 'error', 'msg' => trans('consumables.controller.Permission_denied')];
            return response()->json($return);
        }
        $rules = [
            "id" => "sometimes|integer|min:1",
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $objConsumablePurchase = ConsumablePurchase::where('id', $request->id)->first();
        if ($objConsumablePurchase == null) {
            return response()->json($return);
        }

        $consumable = Consumable::where('id', $objConsumablePurchase->batch_no)->withCount('users', 'places', 'device')->first();
        if ($consumable->qty > 0) {
            $qtyAll = ($consumable->qty - ($consumable->users_count + $consumable->device_count + $consumable->places_count + $consumable->scrap_qty)) - $objConsumablePurchase->qty;
            if ($qtyAll >= 0) {
                $consumable->update([
                    'qty' => $qtyAll + $consumable->users_count + $consumable->device_count + $consumable->places_count + $consumable->scrap_qty,
                    'purchase_cost' => $consumable->purchase_cost - $objConsumablePurchase->purchase_price,
                ]);
                if ($objConsumablePurchase->attachment != null) {
                    Storage::disk("storage_documents")->delete($objConsumablePurchase->attachment);
                }
                $objConsumablePurchase->delete();
            } else {
                if (($consumable->users_count + $consumable->device_count + $consumable->places_count + $consumable->scrap_qty) > 0) {
                    $return = ['status' => 'error', 'msg' => $consumable->users_count + $consumable->device_count + $consumable->places_count + $consumable->scrap_qty . ' ' . trans('consumables.controller.of_these_consumable_delete')];
                } else {
                    $return = ['status' => 'error', 'msg' => trans('consumables.controller.unable_to_delete_purchase_consumable')];
                }
                return response()->json($return);
            }
        }
        $return["status"] = "success";
        $return["msg"] = trans('consumables.controller.purchase_consumable_delete_successfully');
        return response()->json($return);
    }

    public function viewConsumablePurchaseAttachments(Request $request, $id) {
        try {
            $cp = ConsumablePurchase::find($id);
            if (!$cp || !Storage::disk('storage_documents')->exists($cp->attachment)) {
                throw new \Exception("Invalid File");
            }
            // $path = Storage::disk('documents')->getAdapter()->getPathPrefix();
            $path = Storage::disk('storage_documents')->path('');
            $path .= $cp->attachment;
            return response()->file($path);
        } catch (\Exception $e) {
            Log::error("viewPurchaseAttachments: " . $e->getMessage());
        }
    }

    public function getPurchaseConsumableDetail($id, Request $request) {
        $return = ['status' => 'error', 'msg' => trans('consumables.controller.permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseEdit') || !config("services.assets.enabled")) {
            return response()->json($return);
        }

        $record = ConsumablePurchase::where('id', $id)->first();

        if (empty($record)) {
            return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);
        }

        $consumable = Consumable::where('id', $record->batch_no)->first();
        if (!Company::checkUserAccess($consumable)) {
            return response()->json(['status' => 'error', 'msg' => 'Insufficient permission for this purchase !!']);
        }

        $record->purchase_date = CommonHelper::getDateAs($record->purchase_date, "d/m/Y", "Y-m-d");
        $record->exp_date = CommonHelper::getDateAs($record->exp_date, "d/m/Y", "Y-m-d");
        $record->received_date = CommonHelper::getDateAs($record->received_date, "d/m/Y", "Y-m-d");
        $dev["dropdown"] = array();

        if ($record->purchase_by) {
            $getSupplier = Supplier::where("id", $record->purchase_by)->select("id", "name as text")->first();
            $dev["dropdown"]["purchase_by"] = $getSupplier && $getSupplier->exists ? $getSupplier->toArray() : null;
        }
        if ($record->currency) {
            $getCurrency = Currency::getCurrencies();
            $currencyKey = $record->currency;
            if (array_key_exists($currencyKey, $getCurrency)) {
                $currencyData = $getCurrency[$currencyKey];
                $concatenatedText = $currencyData['name'] . ' ' . $currencyData['symbol'];
                $filteredCurrency = [
                    "id" => $currencyKey,
                    "text" => $concatenatedText,
                ];
                $dev["dropdown"]["currency"] = $filteredCurrency;
            } else {
                $dev["dropdown"]["currency"] = null;
            }
        }
        $record->dev = $dev;
        $return['status'] = 'success';
        $return['data'][] = $record;
        return response()->json($return);
    }

    public function updatePurchaseConsumable(Request $request, $id)
    {
        if (!Auth::user()->hasPermissionTo('PurchaseEdit') || !config("services.assets.enabled")) {
            $return = ['status' => 'error', 'section' => 'consumable-edit', 'msg' => trans('consumables.controller.permission_denied')];
            return response()->json($return);
        }
        $appSettings = Settings::first();
        if (empty(Auth::user()->company_id))
            return response()->json(['status' => 'error', 'section' => 'purchase-edit', 'msg' => 'You are not allowed for this process !! Please update your company ID and try again !!']);

        try {
            DB::beginTransaction();
            $objConsumablePurchase = ConsumablePurchase::where('id', $id)->first();
            $rules = [
                'po_number' => 'nullable|clean_text_only',
                'invoice_no' => 'nullable|clean_text_only',
                'batch_no' => 'required|integer',
                'purchase_date' => 'nullable|date_format:d/m/Y',
                'received_date' => 'nullable|date_format:d/m/Y|after_or_equal:purchase_date',
                'exp_date' => 'nullable|date_format:d/m/Y|after_or_equal:purchase_date',
                'currency' => 'nullable',
                'bill_amount' => 'nullable|numeric',
                'qty' => 'required|integer|min:1',
                "purchase_by" => "nullable|integer|exists:suppliers,id",
                'attachment' => 'sometimes|mimes:png,gif,jpg,jpeg,doc,docx,pdf,txt,zip,rar,eml,msg,mbox,pst,xlsx,xls|max:2000',
                'delete_img_pur' => 'nullable',

            ];
            $messages = [
                'qty.integer' => 'Quantity must be an Integer',
                'qty.required' => 'Quantity field is required',
                'received_date.after_or_equal' => 'The Received date must be after or equal to the purchase date.',
                'exp_date.after_or_equal' => 'The exp date must be after or equal to the purchase date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                foreach ($validator->errors()->getMessages() as $key => $value) {
                    $return['status'] = 'error';
                    $return['msg'] = $value;
                }
                return response()->json($return);
            } else {
                $data = $request->validate($rules);
                $consumable = Consumable::where('id', $data['batch_no'])->withCount('users', 'places', 'device')->first();
                $objConsumablePurchase->batch_no = $data['batch_no'];
                $objConsumablePurchase->po_no = isset($data['po_number']) ? $data['po_number'] : null;
                $objConsumablePurchase->invoice_no = isset($data['invoice_no']) ? $data['invoice_no'] : null;
                $objConsumablePurchase->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d/m/Y") : null;
                $objConsumablePurchase->exp_date = isset($request->exp_date) ? CommonHelper::getDateAs($request->exp_date, "Y-m-d", "d/m/Y") : null;
                $objConsumablePurchase->received_date = isset($request->received_date) ? CommonHelper::getDateAs($request->received_date, "Y-m-d", "d/m/Y") : null;
                $objConsumablePurchase->currency = empty($request->currency) ? $appSettings->default_currency : $request->currency;
                if ($objConsumablePurchase->qty > $data['qty']) {
                    $qtyTotal = (($consumable->qty - ($consumable->users_count + $consumable->device_count + $consumable->places_count + $consumable->scrap_qty)) - ($objConsumablePurchase->qty - $data['qty']));
                    if ($qtyTotal < 0) {
                        $return = ['status' => 'error', 'msg' => $consumable->users_count + $consumable->device_count + $consumable->places_count + $consumable->scrap_qty . ' ' . trans('consumables.controller.of_these_consumables')];
                        return response()->json($return);
                    }
                    $qty = ($consumable->qty - $objConsumablePurchase->qty) + $data['qty'];
                    $objConsumablePurchase->qty = $data['qty'];
                } else {
                    $qty = ($consumable->qty - $objConsumablePurchase->qty) + $data['qty'];
                    $objConsumablePurchase->qty = $data['qty'];

                }
                $purchase_cost = ($consumable->purchase_cost - $objConsumablePurchase->purchase_price) + $data['bill_amount'];
                $objConsumablePurchase->purchase_price = $data['bill_amount'];
                $objConsumablePurchase->purchase_by = isset($data['purchase_by']) ? $data['purchase_by'] : null;

                if (isset($data['delete_img_pur']) && $data['delete_img_pur'] == 1 || isset($data['attachment']) && $data['attachment'] != null) {
                    if ($objConsumablePurchase->attachment != null) {
                        $exits1 = File::exists(public_path('/uploads/documents/' . $objConsumablePurchase->attachment));
                        if ($exits1) {
                            File::delete(public_path('/uploads/documents/' . $objConsumablePurchase->attachment));
                            $objConsumablePurchase->attachment = null;
                        }
                    }
                }

                if (isset($data['attachment']) && $data['attachment'] != null) {
                    $filePath = date("Y") . '/' . date('m') . '/' . date('d');
                    $checkFolderPath = CommonHelper::attachmentFolderStructure('storage_documents', $filePath);
                    if (!$checkFolderPath) {
                        return $this->fail(500, " Directory not found", '');
                    }
                    $path = Storage::disk("storage_documents")->putFile('consumable/' . $filePath, $data['attachment']);
                    $objConsumablePurchase->attachment = isset($path) ? $path : null;
                }
                if ($objConsumablePurchase->save()) {
                    $consumable->update([
                        'qty' => $qty,
                        'purchase_cost' => $purchase_cost,
                    ]);
                }
                DB::commit();
                $return["msg"] = trans('consumables.controller.purchase_consumable_updated_successfully');
                $return["status"] = "success";
                Log::info("purchase Con store id:" . $objConsumablePurchase->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
                return response()->json($return);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("updatePurchaseConsumable : " . $e->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        if (!Auth::user()->hasPermissionTo('ConsumableEdit') || !config("services.assets.enabled")) {
            $return = ['status' => 'error', 'section' => 'consumable-update', 'msg' => trans('consumables.controller.permission_denied')];
            return response()->json($return);
        }
        $appSettings = Settings::first();
        // if(empty(Auth::user()->company_id))
        // return response()->json(['status' => 'error', 'section'=>'consumable-add' , 'msg' => 'You are not allowed for this process !! Please update your company ID and try again !!']);
        $objConsumable = Consumable::withCount(['users', 'places', 'device'])->find($id);
        $assignedConsumable = $objConsumable->users_count + $objConsumable->device_count + $objConsumable->places_count;
        // $companyId = CommonHelper::getAccessibleCompanyIds();
        $data['company_id'] = ($assignedConsumable > 0) ? $objConsumable->company_id : $request->company_id;
        // if (!in_array($data['company_id'], $companyId)) {
        //     $msg = "You don't have access to this company.";
        //     return response()->json(["msg" => $msg]);
        // }

        $allocated_qty = (optional($objConsumable)->users_count ?? 0) + (optional($objConsumable)->places_count ?? 0) + (optional($objConsumable)->devices_count ?? 0) + (optional($objConsumable)->scrap_qty ?? 0);

        // if( !Auth::user()->isSuperUser() && Auth::user()->company_id != $request->company_id ) {
        //     $return["status"] = 'error';
        //     $return["section"] = 'consumable-update';
        //     $return["msg"] = Auth::user()->company_id == null ? trans('consumables.controller.update_company_name') : trans('consumables.controller.multiple_company_access');
        //     return response()->json($return);
        // }

        // if($appSettings->full_multiple_companies_support){// check consumable company and user company is same 
        //     if($objConsumable->company_id != Auth::user()->company_id){
        //         return response()->json(['status' => 'error', 'msg' => 'Insufficient Permission for this consumable !!']);
        //     }
        // }

        $rules = [
            'unique_tag' => ['nullable', 'clean_text_only', 'max:100', Rule::unique('consumables', 'unique_tag')->where(function ($query) use ($request) {
                return $query->where('location_id', $request->location_id); })->ignore($request->id),],
            'company_id' => 'required|integer',
            'name' => 'required|clean_text_only|min:3|max:255',
            'category_id' => 'required|integer',
            'location_id' => 'required|integer',
            'order_number' => 'nullable|clean_text_only',
            'purchase_date' => 'nullable|date_format:d/m/Y',
            'currency' => 'nullable',
            'purchase_cost' => 'nullable|numeric',
            'qty' => 'required|integer|min:0',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            "supplier_id" => "nullable|integer|exists:suppliers,id",
            "invoice_id" => "nullable|integer|exists:purchases,id",
            "notes" => "nullable|clean_text_only|max:2000",
            "department_id" => "nullable|integer|exists:departments,id",
            "internal_place" => "nullable|integer|exists:places,id",
            'image' => 'sometimes|mimes:jpeg,bmp,png,jpg',
            "consumable_thresholds" => 'integer|min:0',
        ];
        $messages = [
            'qty.integer' => 'Quantity must be an Integer',
            'qty.required' => 'Quantity field is required',
            'consumable_thresholds.integer' => 'Threshold must be an Integer',
            // 'consumable_thresholds.required' => 'Threshold field is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            //return back()->withInput()->withErrors($validator->messages());
            return array('status' => 'error', 'errors' => $validator->errors()->getMessages());
        } else {
            $data = $request->validate($rules);
            $data_fields = $request->input("fields", null);
            $custom_fields = $custom_fields_val = [];
            $consumable = Category::find($data['category_id']);
            if ($consumable && $consumable->customFieldset && count($consumable->customFieldset->fields)) {
                foreach ($consumable->customFieldset->fields as $f) {
                    $col_name = $f->nameToColumn();
                    $formatType = CommonHelper::convertFormatToRegex($f->format);
                    $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                    // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                    $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                    $custom_fields[] = $col_name;
                    $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
                }
            }
            if (Settings::first()->consumable_custom_fieldset_id != "") {
                $customFieldset = CustomFieldset::where('id', Settings::first()->consumable_custom_fieldset_id)->first();
                if (!empty($customFieldset) && $customFieldset->fields->isNotEmpty()) {
                    foreach ($customFieldset->fields as $f) {
                        $col_name = $f->nameToColumn();
                        $formatType = CommonHelper::convertFormatToRegex($f->format);
                        $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                        // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                        $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : Null;
                        $custom_fields[] = $col_name;
                        $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
                    }
                }
            }
            // if ($request->consumable_thresholds > $request->qty) {
            //     return response()->json([
            //         'status'  => 'error',
            //         'section' => 'consumable-delete',
            //         'msg'     => "The threshold may not be greater than the available quantity {$objConsumable->qty}."
            //     ]);
            // }
            $totalPurchaseQty = ConsumablePurchase::where('batch_no', $objConsumable->id)->sum('qty') ?? 0;
            if ($allocated_qty >= $totalPurchaseQty) {
                if ($data['qty'] < $allocated_qty) {
                    return response()->json(['status' => 'error', 'section' => 'consumable-delete', 'msg' => $allocated_qty . trans('consumables.controller.users_are_using_this_consumable')]);
                }
            }

            if ($totalPurchaseQty >= $allocated_qty) {
                if ($data['qty'] < $totalPurchaseQty) {
                    return response()->json(['status' => 'error', 'section' => 'consumable-delete', 'msg' => $totalPurchaseQty . trans('consumables.controller.purchase_consumable_qty')]);
                }
            }

            $data['internal_place_id'] = isset($request->internal_place) && !empty($request->internal_place) ? $request->internal_place : null;
            // $objConsumabletry= json_encode($custom_fields_val);
            // $data_custom =  json_decode( $objConsumabletry, TRUE);
            // $var = json_decode($objConsumable->consumable_custom_fields);
            // if(!empty($var)) {
            //     foreach($data_custom as $key=>$d) {
            //         $var->$key = $d;
            //     }
            //     $objConsumable->consumable_custom_fields = json_encode($var);
            // } else {
            //     $objConsumable->consumable_custom_fields = json_encode($custom_fields_val);
            // }
            $objConsumable->company_id = $appSettings->full_multiple_companies_support == 0 ? (empty($data['company_id']) ? Auth::user()->company_id : $data['company_id']) : Auth::user()->company_id;
            $objConsumable->name = $request->name;
            $objConsumable->category_id = $request->category_id;
            $objConsumable->manufacturer_id = $request->manufacturer_id;
            $objConsumable->location_id = $request->location_id;
            $objConsumable->internal_place_id = $data['internal_place_id'];
            $objConsumable->order_number = $request->order_number;
            $objConsumable->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d/m/Y") : null;
            $objConsumable->currency = empty($request->currency) ? $appSettings->default_currency : $request->currency;
            $objConsumable->purchase_cost = $request->purchase_cost;
            // $objConsumable->qty             = $request->qty;
            $objConsumable->units = $request->unit;
            $objConsumable->supplier_id = $request->supplier_id;
            $objConsumable->invoice_id = $request->invoice_id;
            $objConsumable->notes = trim($request->notes);
            $objConsumable->department_id = $request->department_id;
            $objConsumable->consumable_custom_fields = null;
            $objConsumable->consumable_thresholds = $request->consumable_thresholds ?? 0;
            $objConsumable->thresholds_alerts = isset($request->thresholds_alerts) ? $request->thresholds_alerts : 0;
            $objConsumable->requestable = isset($request->requestable_consumables) ? $request->requestable_consumables : 0;
            $objConsumable->reorder_limits = $request->reorder_limits ?? 0;
            unset($data["unique_tag"]);
            if (count($custom_fields_val)) {
                $consumableCustomField = [
                    'updated_at' => now(),
                ];

                foreach ($custom_fields_val as $key => $f) {
                    if (is_array($f)) {
                        $consumableCustomField[$key] = json_encode($f);
                    } else {
                        $consumableCustomField[$key] = $f ?? null;
                }
            }
                ConsumableCustomField::where('consumable_id', $id)->update($consumableCustomField);
            }
            // $imagedata = Consumable::find($id)->image;
            // if ($request->input("delete_img", false) || $request->image != null) {
            //     if ($imagedata != null) {
            //         $exits1 = File::exists(public_path('/uploads/consumable/' . $imagedata));
            //         if ($exits1) {
            //             File::delete(public_path('/uploads/consumable/' . $imagedata));
            //             Consumable::where('id', $id)->update(['image' => null]);
            //         }
            //     }
            // }
            if ($request->image) {
                $uploaded_img = $request->image;
                $image1 = Str::random(12) . Str::random(12) . '.' . $uploaded_img->getClientOriginalExtension();
                $checkFolderPath = CommonHelper::attachmentFolderStructure('consumable_uploads', 'consumable');
                if (!$checkFolderPath) {
                    return $this->fail(500, " Directory not found", '');
                }
                $path = storage_path("app/public/uploads/consumable/$image1");
                Image::read($uploaded_img->getRealPath())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $objConsumable->image = $image1;
            }

            if ($objConsumable->save()) {
                ThresholdAlertSettings::where('asset_id', $objConsumable->id)->where('asset_type', 4)->delete();
                if (!empty($request->threshold_alert_users)) {
                    $data = [];

                    foreach ($request->threshold_alert_users as $userId) {
                        $data[] = [
                            'asset_id' => $objConsumable->id,
                            'asset_type' => 4,
                            'user_id' => $userId,
                            'updated_by' => Auth::user()->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    ThresholdAlertSettings::insert($data);
                }

                $history = new ConsumableHistory();
                $history->consumable_id = $objConsumable->id;
                $history->name = $objConsumable->name;
                $history->category_id = $objConsumable->category_id;
                $history->location_id = $objConsumable->location_id;
                $history->internal_place_id = $objConsumable->internal_place_id;
                $history->user_id = Auth::id();
                $history->qty = $objConsumable->qty;
                $history->requestable = $objConsumable->requestable;
                $history->purchase_date = $objConsumable->purchase_date;
                $history->currency = $objConsumable->currency;
                $history->purchase_cost = $objConsumable->purchase_cost;
                $history->order_number = $objConsumable->order_number;
                $history->company_id = $objConsumable->company_id;
                $history->manufacturer_id = $objConsumable->manufacturer_id;
                $history->supplier_id = $objConsumable->supplier_id;
                $history->invoice_id = $objConsumable->invoice_id;
                $history->notes = $objConsumable->notes;
                $history->consumable_custom_fields = $objConsumable->consumable_custom_fields;
                $history->department_id = $objConsumable->department_id;
                $history->image = $objConsumable->image;
                $history->units = $objConsumable->units;
                $history->consumable_thresholds = $objConsumable->consumable_thresholds ?? 0;
                $history->thresholds_alerts = $objConsumable->thresholds_alerts;
                $history->reorder_limits = $objConsumable->reorder_limits;
                $history->unique_tag = $objConsumable->unique_tag;
                $history->scrap_qty = $objConsumable->scrap_qty ?? 0;
                $history->changed_by = Auth::id();
                $history->change_type = 'Update';
                $history->save();

                $msg["msg"] = trans('consumables.controller.consumable_update_successfully');
                $msg["status"] = "success";
                Log::info("Con update id:" . $objConsumable->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
                return array($msg);
            }
        }
    }

    public function consumableCheckoutUsers($id)
    {
        if (!Auth::user()->hasPermissionTo('ConsumableCheckout') || !config("services.assets.enabled")) {
            $return = ['status' => 'error', 'section' => 'request-device', 'msg' => trans('consumables.controller.permission_denied')];
            return response()->json($return);
        }
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        $record = Consumable::where('id', '=', $id)->first();

        if (!in_array($record->company_id, $companyIds)) {
            $return["status"] = 'error';
            $return["section"] = 'request-device';
            $return["msg"] = "You don't have access to this company.";
            return response()->json($return);
        }

        $users = User::where('company_id', '=', $record->company_id)->get();

        return $users;

    }
    public function show($id)
    {
        $appSettings = Settings::first();
        $consumable = array();
        $record = Consumable::where('id', '=', $id)->with('customField')->withCount('users', 'places', 'device')->first();
        $totalAssignedQty = $record->users_count + $record->places_count + $record->device_count;
        $record->purchase_date = CommonHelper::getDateAs($record->purchase_date, "d-m-Y", "Y-m-d");
        $record->purchase_cost = number_format($record->purchase_cost, 2, '.', '');

        // if($appSettings->full_multiple_companies_support && $record->company_id != Auth::user()->company_id) {
        //     return response()->json(['status' => 'error', 'msg' => trans('consumables.controller.Insufficient_Permission_for_this_consumable')]);
        // }

        $consumable["data"] = $record;
        $consumable["data"]["totalAssignedQty"] = $totalAssignedQty;
        if ($record->image != null) {
            $path = file_exists(storage_path('app/public/uploads/consumable/' . $record->image)) == true ? $record->image : null;
            $consumable["data"]['image'] = $path;
            // $consumable['data']['image_url'] = asset('storage/uploads/consumable/' . $record->image);
        }
        $consumable["dropdown"] = array();
        $consumable["custom_fields"]['all_fields'] = array();
        $consumable["custom_fields"]['required_fields'] = array();
        $consumable["custom_fields"]['html'] = "";
        if ($record->supplier_id) {
            $getSupplier = Supplier::where("id", "=", $record->supplier_id)->select("id", "name as text")->first();
            $consumable["dropdown"]["supplier"] = $getSupplier ? $getSupplier->toArray() : null;
        }
        /* get department */
        if ($record->department_id) {
            $department = Department::where("id", $record->department_id)->select("id", "name as text")->first();
            $consumable["dropdown"]["department"] = $department ? $department->toArray(): [];
        }
        if ($record->location_id) {
            $location = Location::where("id", $record->location_id)->select("id", "name as text")->first();
            $consumable["dropdown"]["location"] = $location ? $location->toArray() : null;
        }
        /*get internal place */
        if ($record->internal_place_id) {
            $getPlace = Place::where("id", $record->internal_place_id)->select("id", "place as text")->first();
            $consumable["dropdown"]["internal_place"] = $getPlace ? $getPlace->toArray() : null;
        }
        if ($record->units) {
            $unit = Unit::where("id", $record->units)->select("id", "name as text")->first();
            $consumable["dropdown"]["unit"] = $unit ? $unit->toArray() : null;
        }
        if ($record->company_id) {
            $company = Company::where("id", $record->company_id)->select("id", "name as text")->first();
            $consumable["dropdown"]["company"] = $company ? $company->toArray() : null;
        }
        if ($record->manufacturer_id) {
            $manufacturer = Manufacture::where("id", $record->manufacturer_id)->select("id", "name as text")->first();
            $consumable["dropdown"]["manufacturer"] = $manufacturer ? $manufacturer->toArray() : null;
        }
        if ($record->invoice_id) {
            $getInvoice = Purchase::where("id", "=", $record->invoice_id)->select("id", DB::raw('concat_ws(" - ", invoice_no, date_format(invoice_date, "%d/%m/%Y")) as text'))->first();
            $consumable["dropdown"]["invoice"] = $getInvoice ? $getInvoice->toArray() : null;
        }
        if ($record->category_id) {
            $getConsumable = Category::where("id", $record->category_id)->select("id", "name as text")->first();
            $consumable["dropdown"]["consumable"] = $getConsumable ? $getConsumable->toArray() : null;

            // load custom fields
            if (isset($record->category->customFieldset) && count($record->category->customFieldset->fields)) {
                $consumable["custom_fields"] = CommonHelper::formCustomFields($record->category->customFieldset->fields, $consumable["data"], 'consumable');
            }
        } else {
            if (Settings::first()->consumable_custom_fieldset_id != null) {
                $fieldsetObj = CustomFieldset::where('id', Settings::first()->consumable_custom_fieldset_id)->first();
                if (!empty($fieldsetObj)) {
                    $consumable1 = CommonHelper::formCustomFields($fieldsetObj->fields, $consumable["data"], 'consumable');
                    if (isset($consumable1['all_fields'][0])) {
                        array_push($consumable["custom_fields"]['all_fields'], $consumable1['all_fields'][0]);
                    }
                    if (isset($consumable1['required_fields'][0])) {
                        array_push($consumable["custom_fields"]['required_fields'], $consumable1['required_fields'][0]);
                    }
                    $consumable["custom_fields"]['html'] .= $consumable1['html'];
                }
            }
        }
        $thresholdUserEmails = ThresholdAlertSettings::where('threshold_alert_settings.asset_id', $id)->where('threshold_alert_settings.asset_type', 4)
            ->join('users', 'users.id', '=', 'threshold_alert_settings.user_id')
            ->select('users.id', 'users.username as text')
            ->get()
            ->toArray();
        $consumable["dropdown"]["thresholdUserEmails"] = !empty($thresholdUserEmails) ? $thresholdUserEmails : null;

        // if(Settings::first()->consumable_custom_fieldset_id != null) {
        //     $fieldsetObj = CustomFieldset::where('id', Settings::first()->consumable_custom_fieldset_id)->first();
        //     if(!empty($fieldsetObj)) {
        //         $consumable1 = CommonHelper::formCustomFieldsLicence($fieldsetObj->fields, $consumable["data"]['consumable_custom_fields']);
        //     }
        //     if(isset($consumable1['all_fields'][0])) {
        //         $consumable["custom_fields"]['all_fields'] = isset($consumable["custom_fields"]['all_fields']) ? $consumable["custom_fields"]['all_fields'] : [];
        //         array_push($consumable["custom_fields"]['all_fields'], $consumable1['all_fields']);
        //     }
        //     if(isset($consumable1['required_fields'][0])) {
        //         $consumable["custom_fields"]['required_fields'] = $consumable["custom_fields"]['required_fields'] ?? [];
        //         array_push($consumable["custom_fields"]['required_fields'], $consumable1['required_fields']);
        //     }
        //     $consumable["custom_fields"]['html'] .= $consumable1['html'];
        // }

        return $consumable;
    }

    // user belongs to same company as consumable can delete the consumable
    public function deleteConsumable($id, Request $request)
    {
        if (!Auth::user()->hasPermissionTo('ConsumableDelete') || !config("services.assets.enabled")) {
            $return = ['status' => 'error', 'section' => 'consumable-delete', 'msg' => trans('consumables.controller.permission_denied')];
            return response()->json($return);
        }
        $appSettings = Settings::first();

        $record = Consumable::withCount('users', 'consumableusers', 'places', 'device')->where('id', '=', $id)->first();
        $total_assigned_qty = (optional($record)->users_count ?? 0) + (optional($record)->places_count ?? 0) + (optional($record)->devices_count ?? 0) + (optional($record)->scrap_qty ?? 0);
        if (empty($record))
            return response()->json(['status' => 'error', 'msg' => trans('consumables.controller.already_deleted_data')]);

        $companyId = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($record->company_id, $companyId)) {
            $return["msg"] = "You don't have access to this company.";
            $return["status"] = 'error';
            $return["section"] = 'consumable-delete';
            return response()->json($return);
        }

        if ($total_assigned_qty > 0)
            return response()->json(['status' => 'error', 'msg' => trans('consumables.controller.Currently_this_consumable_is_used_by', ['users_count' => $total_assigned_qty])]);

        if ($record->consumablelogs_count)
            return response()->json(['status' => 'error', 'msg' => trans('consumables.controller.Some_Consumables_are_associated')]);

        if ($record->consumableusers_count)
            return response()->json(['status' => 'error', 'msg' => trans('consumables.controller.Some_Consumables_are_Checked')]);

        $record->delete();
        $logaction = new Actionlog();
        $logaction->consumable_id = $id;
        $logaction->action_type = 'Delete';
        $logaction->asset_type = 'consumable';
        $logaction->user_id = Auth::user()->id;
        $logaction->note = '';
        $logaction->save();
        Log::info("deleteConsumable id:" . $record->id . " uid:" . Auth::user()->id);
        return response()->json(['status' => 'success', 'msg' => trans('consumables.controller.Consumable_has_deleted_successfully')]);

    }

    public function restoreConsumable(Request $request, $id)
    {
        $return = array("status" => "failure", "msg" => "Unable to restore the Consumable");
        if (!Auth::user()->hasPermissionTo('ConsumableRestore') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.permission_denied');
            return response()->json($return);
        }
        $consumable = Consumable::withTrashed()->where("id", "=", $id)->first();
        if (!$consumable->exists) {
            return response()->json($return);
        }
        $companyId = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($consumable->company_id, $companyId)) {
            $return["status"] = 'error';
            $return["msg"] = "You don't have access to this company.";
            return response()->json($return);
        }
        $consumableNotDelete = Consumable::where('location_id', $consumable->location_id)->where("unique_tag", $consumable->unique_tag)->first();
        if (isset($consumableNotDelete->exists)) {
            $return["status"] = 'error';
            $return["msg"] = trans('consumables.controller.already_restored_data');
            return response()->json($return);
        }

        $consumable->restore();
        $logaction = new Actionlog();
        $logaction->consumable_id = $consumable->id;
        $logaction->action_type = 'Restore';
        $logaction->asset_type = 'consumable';
        $logaction->user_id = Auth::user()->id;
        $logaction->note = '';
        $logaction->save();
        $return["msg"] = trans('consumables.controller.consumables_restored');
        $return["status"] = "success";
        return response()->json($return);
    }

    public function checkout($id, Request $request, $technician = null)
    {
        if (!Auth::user()->hasPermissionTo('ConsumableCheckout') && $technician == null || !config("services.assets.enabled")) {
            $return = ['status' => 'error', 'section' => 'checkout', 'msg' => trans('consumables.controller.permission_denied')];
            return response()->json($return);
        }
        $data = $request->only("assigned_to", "assigned_for", "assigned_place", "device_id", "notes");
        if (!isset($data["assigned_to"])) {
            $data["assigned_to"] = null;
        }
        if (!isset($data["device_id"])) {
            $data["device_id"] = null;
        }
        $validate = Validator::make($data, [
            "assigned_for" => "required|integer|min:1|max:3",
            "assigned_to" => [
                "nullable",
                "required_if:assigned_for,1",
                Rule::exists("users", "id")->where(function ($q) {
                    $q->whereNull("deleted_at");
                })
            ],
            "assigned_place" => [
                "nullable",
                "required_if:assigned_for,2",
                Rule::exists("places", "id")->where(function ($q) {
                    $q->whereNull("deleted_at");
                })
            ],
            "device_id" => [
                "nullable",
                "required_if:assigned_for,3",
                Rule::exists("assets", "id")->where(function ($q) {
                    $q->whereNull("deleted_at");
                })
            ],
            "notes" => "clean_text_only : true",
        ]);

        if ($validate->fails()) {
            $v = $validate->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        if ($id) {
            $consumable = Consumable::where('id', '=', $id)->withCount(['users', 'device', 'places'])->with('category')->first();
            if (empty($consumable)) {
                return response()->json(['status' => 'error', 'section' => 'checkout', 'msg' => trans('consumables.controller.Some_problem_in_system')]);
            }
        }

        if (!in_array($consumable->company_id, $companyIds)) {
            $return["status"] = 'error';
            $return["section"] = 'consumable-checkout';
            $return["msg"] = "You don't have access to this company.";
            return response()->json($return);
        }

        $target_checkout_to = $request->assigned_for;
        $totalAssigned = (optional($consumable)->users_count ?? 0) + (optional($consumable)->places_count ?? 0) + (optional($consumable)->device_count ?? 0) + (optional($consumable)->scrap_qty ?? 0);
        $available_qty = $consumable->qty - $totalAssigned;
        if ($available_qty <= 0) {
            $return["status"] = 'error';
            $return["section"] = 'checkout';
            $return["msg"] = 'Quantity is not available';
            return response()->json($return);
        }
        if ($consumable->qty > $totalAssigned) {
            $target_assigned = $target_location_id = 0;
            $attach_data = [
                "consumable_id" => $consumable->id,
                "user_id" => Auth::user()->id,
                "assigned_for" => $target_checkout_to,
                'ticket_id' => $request->ticket_id ?? null
            ];
            $target_assigned_name = $assignedToEmail = "";
            if ($target_checkout_to == 3) {
                $device = Device::find($request->device_id);
                if (empty($device)) {
                    $return["status"] = 'error';
                    $return["section"] = 'checkout';
                    $return["msg"] = trans('consuambles.controller.choosen_device_not_found');
                    return response()->json($return);
                }
                if ($device->status->sold == 1) {
                    $return["msg"] = trans('consuambles.controller.Chosen_Device_is_in_sold_status');
                    return response()->json($return);
                }
                if ($device->status->stolen_item == 1) {
                    $return["msg"] = trans('consuambles.controller.Chosen_Device_is_in_lost');
                    return response()->json($return);
                }
                $target_assigned = $request->device_id;
                $target_location_id = $device->rtd_location_id;
                $target_assigned_name = $device->asset_tag;
                $attach_data["assigned_to"] = $target_assigned;
                if ($device->assigned_for == 1 && !empty($device->assigned_to)) {
                    $assignedToEmail = User::find($device->assigned_to);
                }
                $target_chkout_to = "Device";
            } elseif ($target_checkout_to == 2) {
                $place = Place::find($request->assigned_place);
                if (empty($place)) {
                    $return["status"] = 'error';
                    $return["section"] = 'checkout';
                    $return["msg"] = trans('consuambles.controller.place_not_found');
                    return response()->json($return);
                }
                $target_assigned_name = $place->place;
                $target_assigned = $request->assigned_place;
                $target_location_id = $place->location_id;
                $attach_data["assigned_to"] = $target_assigned;
                $target_chkout_to = "Place";
                $location = Location::find($request->assigned_place);
                if (!empty($location) && !empty($location->location_user_id)) {
                    $assignedToEmail = User::find($location->location_user_id);
                }
            } elseif ($target_checkout_to == 1) {
                $assignedToEmail = User::find($request->assigned_to);
                if (empty($assignedToEmail)) {
                    $return["status"] = 'error';
                    $return["section"] = 'checkout';
                    $return["msg"] = trans('consuambles.controller.Chosen_User_is_not_found');
                    return response()->json($return);
                }
                if ($assignedToEmail->checkoutBasicClearance()) {
                    $return["status"] = 'error';
                    $return["section"] = 'checkout';
                    $return["msg"] = trans('consuambles.controller.sChosen_User_is_not_in_Active_Status');
                    return response()->json($return);
                }
                if ($assignedToEmail->checkLastWorkingDate()) {
                    $return["status"] = 'error';
                    $return["section"] = 'checkout';
                    $return["msg"] = trans('consuambles.controller.This_users_last_working_date');
                    return response()->json($return);
                }
                $target_assigned_name = !empty($assignedToEmail->displayName) ? $assignedToEmail->displayName : trim(($assignedToEmail->first_name ?? '') . ' ' . ($assignedToEmail->last_name ?? ''));
                $target_assigned = $request->assigned_to;
                $target_location_id = $assignedToEmail->location_id;
                $attach_data["assigned_to"] = $target_assigned;
                $target_chkout_to = "User";
            }

            $consumable->touch();
            $log = new Actionlog();
            $log->asset_type = "consumable";
            $log->checkedout_to = $target_assigned;
            $log->location_id = $target_location_id;
            $log->assigned_to_type = $target_checkout_to;
            $log->assigned_for = $target_checkout_to;
            $log->consumable_id = $consumable->id;
            $log->note = $request->note;
            $log->user_id = Auth::user()->id;
            $log->action_type = "checkout";
            $log->save();
            $attach_data["asset_logs_id"] = $log->id;
            $attach_data["ticket_id"] = $request->ticket_id ?? null;

            if ($target_checkout_to == 3) {
                $consumable->device()->attach($consumable->id, $attach_data);
            } elseif ($target_checkout_to == 2) {
                $consumable->places()->attach($consumable->id, $attach_data);
            } elseif ($target_checkout_to == 1) {
                $consumable->users()->attach($consumable->id, $attach_data);
            }
            if (empty($assignedToEmail)) {
                $assignedToEmail = Auth::user();
            }
            /* mail */
            try {
                $alertnotify = (Settings::first()->alerts_enabled == 1) ? CommonHelper::getGlobalAlertEmail() : [];
                $eula = $consumable->getEula();

                if (config('mail.service_enabled') && $eula && $assignedToEmail && filter_var($assignedToEmail->email, FILTER_VALIDATE_EMAIL) && $technician == null) {
                    $ccList = array_filter(array_diff($alertnotify, [$assignedToEmail]), function ($email) {
                        return filter_var($email, FILTER_VALIDATE_EMAIL);
                    });
                    Mail::to($assignedToEmail->email)->cc($ccList)->send(new ConsumableCheckout($consumable, $assignedToEmail, $target_assigned_name, $log, $eula));
                }
            } catch (\Exception $e) {
                Log::error('Checkout Email Error: ' . $e->getMessage());
            }
            $this->notifythresholdAlert($consumable->id, $consumable->category_id);

            Log::info("Con checkout id:" . $consumable->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json(['status' => 'success', 'section' => 'checkout', 'msg' => trans('consumables.controller.Consumable_checked_out_successfully_to') . ' ' . ($target_assigned_name), 'id' => $consumable->id]);
        } else {
            return response()->json(['status' => 'error', 'section' => 'checkout', 'msg' => trans('consumables.controller.Insufficient_consumable')]);
        }

        //    return $consumable->toJson();
    }

    // public function history($id){
    //     $return = [];
    //     if($id)
    //         $consumable = Consumable::where('id','=',$id)->withCount(['assetlog'])->first();
    //     if(empty($consumable))
    //         return response()->json(['status' => 'error', 'section'=>'history' , 'msg' => 'Some problem in system!!']);

    //     if($consumable->assetlog_count == 0){
    //         return response()->json(['status' => 'error', 'section'=>'history' , 'msg' => 'No history found for this Consumable!!']);
    //     }else{

    //         $consumable->purchase_date = CommonHelper::getDateAs($consumable->purchase_date, "d  M  Y", "Y-m-d");

    //         $consumable->purchase_cost = number_format ( $consumable->purchase_cost, 2 , '.' ,',' );
    //         // $consumable->order_number  = 

    //         foreach( $consumable->assetlog as $history ){
    //             $consumed_by_user = User::where('id','=',$history->checkedout_to)->withTrashed()->first();
    //                 $history->consumed_by = $consumed_by_user->first_name.' '.$consumed_by_user->last_name;
    //             $allocated_by_user = User::where('id','=',$history->user_id)->withTrashed()->first();
    //                 $history->allocated_by = $allocated_by_user->first_name.' '.$allocated_by_user->last_name;

    //                 $return['data'][] = array('a' => $history);
    //         }


    //     }

    //     return response()->json($return);
    // }

    public function history($id, Request $request)
    {
        $req = $request->all();
        $return = array();
        $fields = array(
            '0' => 'al.created_at',
            '1' => 'adminUserName',
            '2' => 'action_type',
            '3' => 'target_type',
            '4' => 'target_name',
            '5' => 'al.note',
            '6' => 'a.ticket_id'
        );

        $db = DB::table('asset_logs as al');
        $db->leftJoin('users as u', 'u.id', '=', 'al.checkedout_to');
        $db->leftJoin('users as adminuser', 'adminuser.id', '=', 'al.user_id');
        $db->leftJoin('assets as a', 'a.id', '=', 'al.checkedout_to');
        $db->leftJoin('places as p', 'p.id', '=', 'al.checkedout_to');
        $db->leftJoin('consumable_histories as ch', 'ch.consumable_id', '=', 'al.asset_id');
        $db->addSelect(DB::raw('case when al.assigned_for = 1 then concat_ws(" ", COALESCE(u.displayName, CONCAT(u.first_name, " ", u.last_name)), "-", u.username) when al.assigned_for = 2 then p.place when al.assigned_for = 3 then a.asset_tag end as target_name'));
        $db->addSelect(DB::raw('case when al.assigned_for = 1 then "User" when al.assigned_for = 2 then "Place" when al.assigned_for = 3 then "Device" end as target_type'));
        $db->addSelect('al.id', 'al.action_type', 'al.assigned_for', 'al.note', 'al.ticket_id', 'adminuser.id as adminuserid', 'a.asset_tag', 'a.id as device_id', 'u.id as user_id');
        $db->addSelect(DB::raw('COALESCE(adminuser.displayName, CONCAT(adminuser.first_name, " ", adminuser.last_name)) as adminUserName'));
        $dateTimeFormatSql = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(al.created_at, '{$dateTimeFormatSql}') as created_at"));
        $db->where('al.asset_type', '=', 'consumable');
        $db->whereNull('al.filename');
        $db->where('al.consumable_id', $id);

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $whereStr = sprintf('(concat(adminuser.first_name, " ", adminuser.last_name) like "%%%1$s%%" or al.action_type like "%%%1$s%%" or concat(u.first_name, " ", u.last_name) like "%%%1$s%%" or DATE_FORMAT(al.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or al.note like "%%%1$s%%" or al.ticket_id like "%%%1$s%%")', $search_key);
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

        $data = $db->get();
        $return['data'] = array();
        foreach ($data as $d) {
            // $allocated_qty = Consumable::where('id','=',$d->id)->withCount(['users'])->first()->users_count;
            // $d->available_qty = $d->qty - $allocated_qty;
            // $d->allocated_qty = $allocated_qty;
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    public function getConsumableActivity(Request $request)
    {
        $req = $request->all();
        $return = array();
        $fields = array(
            '0' => 'change_type',
            '1' => 'unique_tag',
            '2' => 'cat_name',
            '3' => 'loc_name',
            '4' => 'internal_place',
            '5' => 'order_number',
            '6' => 'purchase_cost',
            '7' => 'qty',
            '8' => 'created_at',
        );

        $db = DB::table('consumable_histories as a');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('places as p', 'p.id', '=', 'a.internal_place_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'a.manufacturer_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');

        $db->select('a.id', 'a.name', 'a.notes', 'a.currency', 'loc.name as loc_name', 'cat.name as cat_name', 'a.qty', 'a.order_number', 'a.purchase_cost', 'dep.name as department', 'a.image', 'mnu.attachment', 'cat.image_thumbnail as cat_img', 'a.consumable_thresholds', 'unique_tag', 'cat.id as category_id', 'p.place as internal_place', 'loc.branch_code as loc_branch_code', 'a.scrap_qty', 'a.change_type');
        $dateTimeFormatSql = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(a.created_at, '{$dateTimeFormatSql}') as created_at"));
        $db->where('a.consumable_id', $request->consumable_id);

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $whereStr = sprintf('(a.change_type like "%%%1$s%%" or DATE_FORMAT(a.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or loc.name like "%%%1$s%%" or p.place like "%%%1$s%%" or a.order_number like "%%%1$s%%" or a.qty like "%%%1$s%%")', $search_key);
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

        $data = $db->get();
        $return['data'] = array();
        foreach ($data as $d) {
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    public function revokeConsumable(Request $request)
    {
        $return = [
            'status' => 'error',
            'msg' => trans('consumables.controller.Unable_to_revoke'),
            'section' => 'consumable-checkout'
        ];
        if (!Auth::user()->hasPermissionTo('ConsumableCheckin') || !config("services.assets.enabled")) {
            $return['msg'] = trans('consumables.controller.permission_denied');
            return response()->json($return);
        }
        try {
            $idInPivot = $request->id;
            $user = Auth::user();
            if ($idInPivot != null) {
                $cons_user_record = ConsumableUser::findOrFail($idInPivot);
                $consumable = Consumable::findOrFail($cons_user_record->consumable_id);
            }

            if (!$user->isSuperUser() && $user->company_id != $consumable->company_id) {
                return response()->json($return);
            }

            $assigned_user = null;
            if (!$cons_user_record->delete()) {
                return response()->json($return);
            }
            if ($request->scrap_qty > 0) {
                $consumable->scrap_qty = $consumable->scrap_qty ? $consumable->scrap_qty + 1 : 1;
                $consumable->save();
            }

            $log = new Actionlog();

            $user = $place = $device = "";
            if ($cons_user_record->assigned_for == "1") {
                $user = User::withTrashed()->find($cons_user_record->assigned_to);
                $assigned_user = $user;
                $log->note = !empty($request->note) ? $request->note : 'revoked from ' . $user->first_name . ' ' . $user->last_name . ' (' . $user->username . ')';
            } elseif ($cons_user_record->assigned_for == "2") {
                $place = Place::find($cons_user_record->assigned_to);
                $assigned_user = $place;
                $log->note = !empty($request->note) ? $request->note : 'revoked from place: ' . $place->place;
            } elseif ($cons_user_record->assigned_for == "3") {
                $device = Device::find($cons_user_record->assigned_to);
                $assigned_user = $device;
                $log->note = !empty($request->note) ? $request->note : 'revoked from device: ' . $device->asset_tag;
            }

            // $log = new Actionlog();
            $log->consumable_id = $consumable->id;
            $log->action_type = 'revoked from';
            $log->checkedout_to = (int) $cons_user_record->assigned_to;
            $log->asset_type = 'consumable';
            $log->location_id = $cons_user_record->assigned_for == 3 ? ($device->rtd_location_id ?? null) : (
                $cons_user_record->assigned_for == 2 ? ($place->location_id ?? null) : (
                    $cons_user_record->assigned_for == 1 ? ($user->location_id ?? null) : null
                )
            );
            $log->user_id = Auth::user()->id;
            $log->assigned_for = (int) $cons_user_record->assigned_for;
            $log->assigned_to_type = (int) $cons_user_record->assigned_for;
            $log->in_out_id = $cons_user_record->asset_logs_id;
            $log->save();
            //To update the in_out_id for checkout entry
            $logEntry = Actionlog::find($log->in_out_id);
            if ($logEntry) {
                DB::table('asset_logs')->where('id', $cons_user_record->asset_logs_id)->update([
                    'created_at' => $logEntry->created_at,
                    'in_out_id' => $log->id
                ]);
            }

            try {
                if (config('mail.service_enabled') && filter_var($assigned_user->email, FILTER_VALIDATE_EMAIL)) {
                    $alertnotify = Settings::first()->alerts_enabled == 1 ? CommonHelper::getGlobalAlertEmail() : [];
                    $validAlertEmails = array_filter($alertnotify, function ($email) {
                        return filter_var($email, FILTER_VALIDATE_EMAIL);
                    });
                    $toEmail = null;
                    if ($cons_user_record->assigned_for == "1" && isset($assigned_user->email) && filter_var($assigned_user->email, FILTER_VALIDATE_EMAIL)) {
                        $toEmail = $assigned_user->email;
                    }
                    $ccEmails = array_diff($validAlertEmails, [$toEmail]);
                    if ($toEmail) {
                        Mail::to($toEmail)->cc($ccEmails)->queue(new ConsumableCheckin($log));
                    } elseif (!empty($ccEmails)) {
                        Mail::to($ccEmails)->queue(new ConsumableCheckin($log));
                    }
                }
            } catch (\Exception $e) {
                Log::error('Consumable Revoke Email Failed: ' . $e->getMessage());
            }

            $return['status'] = 'success';
            $return['section'] = 'checkout';
            $return['msg'] = trans('consumables.controller.Consumable_revoked_successfully_from');
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error('revoke error @ consume revoke');
            Log::error($e->getMessage());
            return response()->json($return);
        }

        // $record = DB::table('consumables_users')->where('id', '=', $idInPivot)->get();//->delete();

        // // return $record;
        // if(!empty($record))
        // if(!empty($record[0]))
        // {

        //     $consumable = Consumable::where('id','=',$record[0]->consumable_id)->first();
        //     if( !Auth::user()->isSuperUser() && Auth::user()->company_id != $request->company_id ) {
        //         $return["status"] = 'error';
        //         $return["section"] = 'consumable-checkout';
        //         $return["msg"] = Auth::user()->company_id == null ? "Please update your company name on your profile then try again." : "Multiple Company access is not enabled. You can add item for your company only.";
        //         return response()->json($return);
        //     }

        //     $asigned_usr = User::where('id','=',$record[0]->assigned_to)->withTrashed()->first();

        //     DB::table('consumables_users')->where('id', '=', $idInPivot)->delete();

        //     $logaction = new Actionlog();
        //     $logaction->consumable_id = $record[0]->consumable_id;
        //     $logaction->action_type = 'revoked from';
        //     $logaction->checkedout_to = $record[0]->assigned_to;
        //     $logaction->asset_type = 'consumable';
        //     $logaction->location_id = $asigned_usr->location_id;
        //     $logaction->user_id = Auth::user()->id;
        //     $logaction->note = 'revoked from '.$asigned_usr->first_name.' '.$asigned_usr->last_name.'('.$asigned_usr->username.')' ;
        //     $logaction->save();

        //     if(config('mail.service_enabled') && filter_var($asigned_usr->email, FILTER_VALIDATE_EMAIL)) {
        //         $alertnotify = Settings::first()->alerts_enabled == 1 && filter_var(Settings::first()->alert_email, FILTER_VALIDATE_EMAIL) ? Settings::first()->alert_email : null;
        //         if($alertnotify) {
        //             Mail::to($asigned_usr->email)->cc($alertnotify)->send(new ConsumableRevoke($logaction) );
        //         }
        //         else {
        //             Mail::to($asigned_usr->email)->send(new ConsumableRevoke ($logaction));
        //         }
        //     }

        //     return response()->json(['status' => 'success', 'section'=>'checkout' , 'msg' => 'Consumable revoked successfully from '.($asigned_usr->first_name.' '.$asigned_usr->last_name )]);

        // }
        // else{
        //     return response()->json(['status' => 'error', 'section'=>'revoke-consumable' , 'msg' => 'System is unable to revoke the consumable !!']);
        // }
    }

    public function consumableUsers($consumable_id, Request $request)
    {
        $req = $request->all();
        $fields = array(
            '1' => 'target_type',
            '2' => 'target_name',
            '3' => 'created_at',
            '4' => 'actioner_name',
            '5' => 'ticket_id',
            // '5' => 'a.purchase_date', 
            // '6' => 'a.purchase_cost',
            // '7' => 'a.order_number'
        );

        $db = DB::table('consumables_users as cu');
        $db->leftJoin('users as enduser', 'enduser.id', '=', 'cu.assigned_to');
        $db->leftJoin('users as actioner', 'actioner.id', '=', 'cu.user_id');
        $db->leftJoin('assets as a', 'a.id', '=', 'cu.assigned_to');
        $db->leftJoin('places as p', 'p.id', '=', 'cu.assigned_to');
        $db->leftJoin('asset_logs as al', function ($q) {
            $q->on('al.consumable_id', '=', 'cu.consumable_id');
            $q->on('al.id', '=', 'cu.asset_logs_id');
            $q->where('al.asset_type', '=', 'consumable');
            $q->where('al.action_type', '=', 'checkout');
        });
        $db->addSelect(DB::raw('DISTINCT case when enduser.displayName is not null then enduser.displayName else concat(enduser.first_name, " ", enduser.last_name) end as fullname'), 'al.note', 'enduser.id', 'al.assigned_to_type', 'cu.ticket_id', 'cu.id as cuid');
        $db->addSelect(DB::raw('case  when actioner.displayName is not null then actioner.displayName else concat(actioner.first_name, " ", actioner.last_name) end as actioner_name'));
        $db->addSelect(DB::raw('case when cu.assigned_for = 1 then concat( case when enduser.displayName is not null then enduser.displayName else concat(enduser.first_name, " ", enduser.last_name)  end, " - ", enduser.username) when cu.assigned_for = 2 then p.place when cu.assigned_for = 3 then a.asset_tag end as target_name'));
        $db->addSelect(DB::raw('case when cu.assigned_for = 1 then "User" when cu.assigned_for = 2 then "Place" when cu.assigned_for = 3 then "Device" end as target_type'));
        $db->addSelect('enduser.id as user_id', 'cu.id', 'a.id as device_id', 'a.asset_tag', DB::raw('concat(enduser.first_name, " ", enduser.last_name) as full_name'));

        $dateTimeFormatSql = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(cu.created_at, '{$dateTimeFormatSql}') as created_at"));
        $db->where("cu.consumable_id", "=", $consumable_id);

        // if( isset($req["showDeletedConsumables"]) && $req["showDeletedConsumables"] == "true" ) {
        //     $db->whereNotNull('a.deleted_at');
        // }
        // else {
        //     $db->whereNull('a.deleted_at');
        // }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $whereStr = sprintf('(concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or enduser.displayname like "%%%1$s%%" or al.note like "%%%1$s%%" or actioner.displayname like "%%%1$s%%" or concat(actioner.first_name, " ", actioner.last_name) like "%%%1$s%%" or case when cu.assigned_for = 1 then concat(coalesce(enduser.displayname, concat(enduser.first_name, " ", enduser.last_name)), " - ", enduser.username) when cu.assigned_for = 2 then p.place when cu.assigned_for = 3 then a.asset_tag end like "%%%1$s%%" or case when cu.assigned_for = 1 then "user" when cu.assigned_for = 2 then "place" when cu.assigned_for = 3 then "device" end like "%%%1$s%%" or a.asset_tag like "%%%1$s%%" or DATE_FORMAT(cu.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or cu.ticket_id like "%%%1$s%%")', $search_key);
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

        $data = $db->get();
        $return['data'] = array();
        foreach ($data as $d) {
            // $allocated_qty = Consumable::where('id','=',$d->id)->withCount(['users'])->first()->users_count;
            // $d->available_qty = $d->qty - $allocated_qty;
            // $d->allocated_qty = $allocated_qty;
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }
    public function infosummery($consumable_id)
    {
        $consumable = Consumable::where('id', '=', $consumable_id)->first();
        if (empty($consumable))
            return redirect('consumables');

        $path = null;
        if ($consumable->image != null && file_exists(storage_path('app/public/uploads/consumable/' . $consumable->image))) {
                $path = asset('storage/uploads/consumable/' . $consumable->image);
        } else {
            $cat_img_path = Category::where('id', $consumable->category_id)->first();
            if ($cat_img_path != null) {
                if ($cat_img_path->image_thumbnail != null && file_exists(public_path('uploads/category/' . $cat_img_path->image_thumbnail))) {
                    $path = asset('uploads/category/' . $cat_img_path->image_thumbnail);
                }
            }

            $man_img_path = Manufacture::where('id', $consumable->manufacturer_id)->first();
            if ($man_img_path != null) {
                if ($man_img_path->attachment != null && file_exists(public_path('uploads/manufacturers/' . $man_img_path->attachment))) {
                    $path = asset('uploads/manufacturers/' . $man_img_path->attachment);
                }
            }
        }

        return view("consumables.basic-info")->with('consumable', $consumable)->with('path', $path);
    }
    public function consumableInfo(Request $request, $consumable_id)
    {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.permission_denied')];
        if (!Auth::user()->hasPermissionTo('ConsumableView') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = new stdClass();
        $companyId = CommonHelper::getSelectedCompanyIds();
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        $vd->assignedForOptions = Consumable::getAssignedForOptions();
        $vd->places = Place::selectOptions();
        $companyFieldset = CustomFieldset::where('id', Settings::first()->consumable_custom_fieldset_id)->first();
        $currencies = Currency::getCurrencies();
        $consumable = Consumable::where('id', '=', $consumable_id)->first();
        $categories = Category::whereNull("deleted_at")->select("id", "name as text")->where("category_type", "like", "consumable")->get()->toArray();
        $path = null;
        if (!in_array($consumable->company_id, $companyIds)) {
            return redirect('consumables')->withMsg(['status' => 'error', 'msg' => "You don't have access to this company."]);
        }
        $companies = Company::select("id", "name as text")->whereNull('deleted_at')->get()->toArray();
        if ($consumable != null) {
            if ($consumable->image != null && file_exists(storage_path('app/public/uploads/consumable/' . $consumable->image))) {
                $path = asset('storage/uploads/consumable/' . $consumable->image);
            } else {
                $cat_img_path = Category::where('id', $consumable->category_id)->first();
                if ($cat_img_path != null) {
                    if ($cat_img_path->image_thumbnail != null && file_exists(public_path('uploads/category/' . $cat_img_path->image_thumbnail))) {
                        $path = asset('uploads/category/' . $cat_img_path->image_thumbnail);
                    }
                }

                $man_img_path = Manufacture::where('id', $consumable->manufacturer_id)->first();
                if ($man_img_path != null) {
                    if ($man_img_path->attachment != null && file_exists(public_path('uploads/manufacturers/' . $man_img_path->attachment))) {
                        $path = asset('uploads/manufacturers/' . $man_img_path->attachment);
                    }
                }
            }
            $isCheckinCall = false;
            if ($request->open && is_numeric($request->open) && $request->popup == "revoke") {
                $get_cu_record = DB::table('consumables_users as cu')
                    ->leftJoin('users as u', 'u.id', '=', 'cu.assigned_to')
                    ->leftJoin('assets as a', 'a.id', '=', 'cu.assigned_to')
                    ->leftJoin('places as p', 'p.id', '=', 'cu.assigned_to')
                    ->select(DB::raw('u.username as fullname'))
                    ->addSelect(DB::raw('case when cu.assigned_for = 1 then u.username when cu.assigned_for = 2 then p.place when cu.assigned_for = 3 then a.asset_tag end as target_name'))
                    ->addSelect(DB::raw('case when cu.assigned_for = 1 then "User" when cu.assigned_for = 2 then "Place" when cu.assigned_for = 3 then "Device" end as target_type'))
                    ->where('cu.id', $request->open)->limit(1)->get();
                if (count($get_cu_record) > 0) {
                    $cu_record = $get_cu_record[0];
                    $isCheckinCall = [
                        "username" => $cu_record->fullname,
                        "checkoutTo" => $cu_record->target_name, // ($cu_record->device_id > 0 ? $cu_record->asset_tag : $cu_record->fullname),
                        "id" => $request->open
                    ];
                }
            }
        }
        if (empty($consumable))
            return redirect('consumables');

        $requestable_enabled = config('app.requestable_enabled');
        return view("consumables.info")
            ->with('consumable', $consumable)->with('companyFieldset', $companyFieldset)->with("categories", $categories)->with('requestable_enabled', $requestable_enabled)->with("path", $path)->with("currencies", $currencies)->with("vd", $vd)->with('isCheckinCall', $isCheckinCall)->with("companies", $companies);
    }

    public function acceptCheckout(Request $request, $id)
    {
        $log = Actionlog::where("id", "=", $id)->first();

        // echo '<pre>';print_r($log);        die;
        if (!$log || !$log->exists) {
            return redirect("dashboard")->with("status", trans('consumables.controller.Invalid_Access'));
        }

        if (Auth::user()->id != $log->checkedout_to) {
            return redirect("dashboard")->with("status", trans('consumables.controller.Invalid_Access'));
        }

        $item = false;
        if ($log->consumable_id && $log->asset_type == "consumable") {
            $item = Consumable::where("id", "=", $log->consumable_id)->first();
        }
        // echo '<pre>';print_r($item);        die;
        if (!$item || !$item->exists) {
            return redirect("dashboard")->with("status", trans('consumables.controller.Consumable_does_not_exist'));
        }

        if ($log->accepted_id) {
            return redirect("dashboard")->with("status", trans('consumables.controller.Consumable_has_been_already_accepted'));
        }

        // needs to redirect requestable page if item present but permission no 
        if ($request->method() == "POST") {

            if (!$request->has("confirm")) {
                return view("consumables.checkout_confirm")->withLog($log)->withConsumable($item)->withStatus(trans('consumables.controller.Please_choose_correct_option'));
            }

            $confirmation = $request->input("confirm");

            $confLog = new ActionLog();
            $confLog->asset_id = NULL;
            $confLog->consumable_id = $log->consumable_id;
            $confLog->asset_type = "consumable";

            $confirmation_msg = trans('consumables.controller.accepted');
            $notification_msg = trans('consumables.controller.You_have_successfully_accepted_the_consumable');
            if (!$confirmation) {
                $confirmation_msg = trans('consumables.controller.declined');
                $notification_msg = trans('consumables.controller.You_have_successfully_declined_the_consumable');
            }

            $confLog->checkedout_to = $log->checkedout_to;
            $confLog->user_id = Auth::user()->id;
            $confLog->accepted_at = date("Y-m-d h:i:s");
            $confLog->action_type = $confirmation_msg;
            $confLog->note = e($request->note);
            $confLog->save();

            // update log 
            $log->accepted_id = $confLog->id;
            $log->save();

            return redirect("dashboard")->with("status", $notification_msg);
        }

        return view("consumables.checkout_confirm")->withLog($log)->withConsumable($item);
    }

    public function exportConsumables($consumable_id, Request $request)
    {

        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Unable_to_export_the_Details')];
        if (!Auth::user()->hasPermissionTo('ConsumableDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $req = $request->all();

        $db = DB::table('consumables_users as cu');
        $db->leftJoin('users as enduser', 'enduser.id', '=', 'cu.assigned_to');
        $db->leftJoin('users as adminuser', 'adminuser.id', '=', 'cu.user_id');
        $db->Join('consumables as c', 'c.id', '=', 'cu.consumable_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'c.company_id');
        $db->leftJoin('manufacturers as manu', 'manu.id', '=', 'c.manufacturer_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');
        $db->leftJoin('assets as a', 'a.id', '=', 'cu.assigned_to');
        $db->leftJoin('places as p', 'p.id', '=', 'cu.assigned_to');
        $db->leftJoin('asset_logs as al', function ($q) {
            $q->on('al.consumable_id', '=', 'cu.consumable_id');
            $q->on('al.id', '=', 'cu.asset_logs_id');
            $q->where('al.asset_type', '=', 'consumable');
            $q->where('al.action_type', '=', 'checkout');
        });
        $db->select('cu.id', 'c.name', 'c.unique_tag', 'enduser.id as enduserid', 'cmp.name as cmp_name', 'manu.name as manu_name', 'cat.name as cat_name', 'c.purchase_cost', 'cu.ticket_id');
        $dateTimeFormatSql = CommonHelper::mysqlDateTimeFormat('datetime', 'excel');
        $db->addSelect(DB::raw("DATE_FORMAT(cu.created_at, '{$dateTimeFormatSql}') as 	created_at"));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CN",c.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CNS",c.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('case when cu.assigned_for = 1 then concat( case when enduser.displayName is not null then enduser.displayName else concat(enduser.first_name, " ", enduser.last_name)  end, " - ", enduser.username) when cu.assigned_for = 2 then p.place when cu.assigned_for = 3 then a.asset_tag end as target_name'));
        $db->addSelect(DB::raw('case when cu.assigned_for = 1 then "User" when cu.assigned_for = 2 then "Place" when cu.assigned_for = 3 then "Device" end as target_type'));
        $db->addSelect(DB::raw('case  when adminuser.displayName is not null then adminuser.displayName else concat(adminuser.first_name, " ", adminuser.last_name) end as endUserName'));
        $db->addSelect(DB::raw('case when c.purchase_cost then FORMAT(c.purchase_cost, 2) else "0.00" end as purchase_cost_format'));
        $db->where('cu.consumable_id', '=', $consumable_id);
        $db->whereNull('c.deleted_at');
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if (isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if (isset($req["search"]) && $search_key = trim($req["search"])) {
                $whereStr = sprintf('(concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or enduser.displayname like "%%%1$s%%" or case when adminuser.displayname is not null then adminuser.displayname else concat(adminuser.first_name, " ", adminuser.last_name) end like "%%%1$s%%" or case when cu.assigned_for = 1 then concat(case when enduser.displayname is not null then enduser.displayname else concat(enduser.first_name, " ", enduser.last_name) end, " - ", enduser.username) when cu.assigned_for = 2 then p.place when cu.assigned_for = 3 then a.asset_tag end like "%%%1$s%%" or case when cu.assigned_for = 1 then "user" when cu.assigned_for = 2 then "place" when cu.assigned_for = 3 then "device" end like "%%%1$s%%" or cu.ticket_id like "%%%1$s%%" or date_format(cu.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                $db->whereRaw($whereStr);
                $return['recordsFiltered'] = $db->count();
            }
        }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        $records = $db->get();

        $data = [];
        foreach ($records as $r) {
            $data[] = [
                $r->con_batch_no,
                $r->unique_tag,
                $r->name,
                $r->cmp_name,
                $r->cat_name,
                $r->manu_name,
                $r->target_type,
                $r->target_name,
                $r->endUserName,
                $r->created_at,
                $r->purchase_cost_format,
                $r->ticket_id,
            ];
        }

        return Excel::download(new ConsumableInfoExport($data), 'Consumables Checkout Details.xlsx');
    }

    public function exportPDFConsumables($consumable_id, Request $request)
    {

        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Unable_to_export_the_Details')];
        if (!Auth::user()->hasPermissionTo('ConsumableDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = [];

        $db = DB::table('consumables_users as cu');
        $db->leftJoin('users as enduser', 'enduser.id', '=', 'cu.assigned_to');
        $db->leftJoin('users as adminuser', 'adminuser.id', '=', 'cu.user_id');
        $db->Join('consumables as c', 'c.id', '=', 'cu.consumable_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'c.company_id');
        $db->leftJoin('manufacturers as manu', 'manu.id', '=', 'c.manufacturer_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');
        $db->leftJoin('assets as a', 'a.id', '=', 'cu.assigned_to');
        $db->leftJoin('places as p', 'p.id', '=', 'cu.assigned_to');
        $db->leftJoin('asset_logs as al', function ($q) {
            $q->on('al.consumable_id', '=', 'cu.consumable_id');
            $q->on('al.id', '=', 'cu.asset_logs_id');
            $q->where('al.asset_type', '=', 'consumable');
            $q->where('al.action_type', '=', 'checkout');
        });

        $db->select('cu.id', 'c.name', 'c.unique_tag', 'enduser.id as enduserid', 'cmp.name as cmp_name', 'manu.name as manu_name', 'cat.name as cat_name', 'c.purchase_cost', 'cu.ticket_id');
        $db->addSelect(DB::raw('DATE_FORMAT(cu.created_at, "%d %b %Y %h:%i %p") as 	created_at'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CN",c.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CNS",c.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('case when cu.assigned_for = 1 then concat( case when enduser.displayName is not null then enduser.displayName else concat(enduser.first_name, " ", enduser.last_name)  end, " - ", enduser.username) when cu.assigned_for = 2 then p.place when cu.assigned_for = 3 then a.asset_tag end as target_name'));
        $db->addSelect(DB::raw('case when cu.assigned_for = 1 then "User" when cu.assigned_for = 2 then "Place" when cu.assigned_for = 3 then "Device" end as target_type'));
        $db->addSelect(DB::raw('case  when adminuser.displayName is not null then adminuser.displayName else concat(adminuser.first_name, " ", adminuser.last_name) end as endUserName'));
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('case when c.purchase_cost then FORMAT(c.purchase_cost, 2) else "0.00" end as purchase_cost_format'));
        $db->where('cu.consumable_id', '=', $consumable_id);
        $db->whereNull('c.deleted_at');

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if (isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if (isset($req["search"]) && $search_key = trim($req["search"])) {
                $whereStr = sprintf('(concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or enduser.displayname like "%%%1$s%%" or case when adminuser.displayname is not null then adminuser.displayname else concat(adminuser.first_name, " ", adminuser.last_name) end like "%%%1$s%%" or case when cu.assigned_for = 1 then concat(case when enduser.displayname is not null then enduser.displayname else concat(enduser.first_name, " ", enduser.last_name) end, " - ", enduser.username) when cu.assigned_for = 2 then p.place when cu.assigned_for = 3 then a.asset_tag end like "%%%1$s%%" or case when cu.assigned_for = 1 then "user" when cu.assigned_for = 2 then "place" when cu.assigned_for = 3 then "device" end like "%%%1$s%%" or cu.ticket_id like "%%%1$s%%" or date_format(cu.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                $db->whereRaw($whereStr);
                $return['recordsFiltered'] = $db->count();
            }
        }

        $data = $db->get();

        $properties = [];
        $properties['format'] = 'A4';

        $vd["records"] = $data;
        // $pdf = PDF::loadView('consumables.for_export_con', $vd, [], $properties);
        // return $pdf->download('Consumables Checkout Details.pdf');
        return PDF::view('consumables.for_export_con', $vd)->landscape()->format('a4')->name('Consumables Checkout Details.pdf')->download();
    }

    public function consumablesImport(Request $request)
    {
        $return = ["msg" => "Unable to import the given consumables", "status" => "danger"];
        if (!config("services.assets.enabled")) {
            $return = ['status' => 'danger', 'msg' => trans('consumables.controller.permission_denied')];
            return redirect('dashboard')->with("msg", $return);
        }
        $settings = Settings::first();
        if ($request->isMethod('post') && !$request->import_file) {
            $return["msg"] = trans('consuambles.controller.error_place_upload');
            $request->session()->flash("msg", $return);
        } else {
            if (!$request->isMethod('post')) {
                return view("consumables.import")->with("settings", $settings);
            }
            if ($request->isMethod('post') && $request->import_file) {
                $path = $request->file('import_file')->getRealPath();
                $import = new ConsumableImport($request);
                Excel::import($import, $request->import_file);
                $response = $import->data;
                // return view("consumables.import")->with("settings", $settings)->with(['success' => $response['success'] ?? "", 'fail' => $response['fail'] ?? "", 'fail_msgs' => $response['fail_msgs'] ?? [] ]);
                return redirect()->back()->with([
                    'settings' => $settings,
                    'success' => $response['success'] ?? 0,
                    'fail' => $response['fail'] ?? 0,
                    'fail_msgs' => $response['fail_msgs'] ?? []
                ]);
            }
        }
        return view("consumables.import")->with("settings", $settings);
    }
    public function downloadCustomFieldsCodeConsumable(Request $request)
    {
        $customFieldset = CustomFieldset::where('id', Settings::first()->consumable_custom_fieldset_id)->first();
        $fields = [];
        if (!empty($customFieldset)) {
            if (count($customFieldset->fields) > 0) {
                foreach ($customFieldset->fields as $f) {
                    $col_name = $f->name;
                    $fields[] = CustomField::where('name', $f->name)->first();
                }
            }
        }

        $result = json_decode(json_encode($fields, true), true);
        return Excel::download(new CustomFields($result), 'ConsumableCustomFields.xlsx');
    }

    public function notifyCategoryThresould($category_id)
    {
        $alertnotify = [];
        if (Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        $alertmail = null;
        $ThresholdSettings = ThresholdSettings::first();
        $thresouldDtl = Threshold::where('cat_id', '=', $category_id)->first();
        $total_consumable = Consumable::getCatConsumableTotal($category_id)[0]->total_consumables;
        $total_chkout_consumables = Consumable::getCheckoutConsumableTotalByCat($category_id)[0]->total_checkouts;
        $total_scrap_qty_consumables = Consumable::getCatConsumableTotalScrap($category_id)[0]->total_scrap_qty_consumables;
        $availableConsumables = $total_consumable - ($total_chkout_consumables + ($total_scrap_qty_consumables ?? 0));

        if (empty($thresouldDtl))
            return;
        if ($ThresholdSettings->threshold_enabled && $ThresholdSettings->alerts_enabled && $thresouldDtl->alerts_enabled) {
            if ($ThresholdSettings->send_alerts == 0) {
                if (Settings::first()->alerts_enabled == 1)
                    $alertmail = CommonHelper::getGlobalAlertEmail();
            }
            if ($ThresholdSettings->send_alerts == 1) {
                $alertmail = $ThresholdSettings->email;
            }
        }
        if (!empty($alertmail) && $thresouldDtl->threshold >= 0) {
            $catDetail = Category::find($category_id);
            if ($availableConsumables < $thresouldDtl->threshold) {
                if (config('mail.service_enabled')) {
                    Mail::to($alertmail)->cc($alertnotify)->send(new ThreshouldNotification($catDetail, $thresouldDtl->threshold, $availableConsumables));
                    $thresouldDtl->notify_count = $thresouldDtl->notify_count + 1;
                    $thresouldDtl->last_notified_date = date('Y-m-d');
                }
                $thresouldDtl->save();
            }
        }
    }

    public function notifythresholdAlert($cons_id, $category_id)
    {
        $thresholdSettings = ThresholdSettings::first();
        if (empty($thresholdSettings) || ($thresholdSettings->threshold_enabled === null) || ($thresholdSettings->alerts_enabled === null)) {
            Log::info('Threshold settings are not configured.');
            return;
        }
        $alertmail = [];
        //Get Details By Item
        $consumableDetails = Consumable::where('id', $cons_id)->first();
        $singleConsThreshold = $consumableDetails->consumable_thresholds;
        $totSingleConsThreshold = $consumableDetails->qty;
        $total_single_chkout_consumables = Consumable::getCheckoutConsumableTotalById($cons_id)[0]->total_checkouts;
        $availableSingleConsumables = $totSingleConsThreshold - $total_single_chkout_consumables;

        //Get Details By Category
        $thresouldCatValue = 0;
        if (!empty($category_id)) {
            $thresouldDetail = Threshold::where('cat_id', $category_id)->first();
            $thresouldCatValue = !empty($thresouldDetail) ? $thresouldDetail->threshold : 0;
        }
        $thresholdUserEmails = ThresholdAlertSettings::where('threshold_alert_settings.asset_id', $cons_id)
            ->where('threshold_alert_settings.asset_type', 4)
            ->join('users', 'users.id', '=', 'threshold_alert_settings.user_id')
            ->pluck('users.email')
            ->toArray();
        if (!empty($thresholdUserEmails)) {
            $alertmail = array_merge($alertmail, $thresholdUserEmails);
        }

        if (config('mail.service_enabled') && !empty($alertmail) && ($singleConsThreshold > 0) && ($availableSingleConsumables <= $singleConsThreshold)) {
            Mail::to($alertmail)->send(new ConsumableThreshouldNotification($consumableDetails, $singleConsThreshold, $availableSingleConsumables, $totSingleConsThreshold));
        } elseif (config('mail.service_enabled') && ($thresouldCatValue > 0) && ($availableSingleConsumables <= $thresouldCatValue) && $thresouldDetail->alerts_enabled == 1) {
            $categoryAlertsUsers = [];
            if ($thresholdSettings->send_alerts == 0) {
                // send_alerts = 0 = Global alert setting fetch
                $categoryAlertsUsers = array_merge($categoryAlertsUsers, CommonHelper::getGlobalAlertEmail());
            } else {
                // send_alerts = 1 = Entered email alert
                $categoryAlertsUsers = array_merge($categoryAlertsUsers, explode(',', $thresholdSettings->email));
            }
            $catDetail = Category::find($category_id);
            Mail::to($categoryAlertsUsers)->send(new ThreshouldNotification($catDetail, $thresouldCatValue, $availableSingleConsumables));
            $threshold = Threshold::where('cat_id', $catDetail->id)->first();
            if ($threshold) {
                $threshold->notify_count = $threshold->notify_count + 1;
                $threshold->last_notified_date = date('Y-m-d');
                $threshold->save();
            }
        }
    }

    public function getUnitsByAjax(Request $request)
    {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);
        $take = 10;

        if (isset($request->start) && isset($request->length)) {
            $skip = (int) $request->start;
            $take = (int) $request->length;
        }

        $db = Unit::select("id", "name as text");
        if ($search) {
            if (is_array($search)) {
                $db->where("name", "like", "%" . $search['value'] . "%");
            } else {
                $db->where("name", "like", "%" . $search . "%");
            }
        }
        $return['recordsFiltered'] = $db->count();
        $count = $db->count();
        if (isset($request->start) && isset($request->length)) {
            $db->skip($skip)->take($take);
        } else {
            $db->skip($skip)->take(20);
        }
        $return['recordsTotal'] = $db->count();
        $result = $db->get();
        $return["draw"] = date('is');

        $return["pagination"] = array("more" => ($count - ($page * 10)) > 0 ? true : false);
        $return["data"] = count($result) ? $result->toArray() : [];
        $return["results"] = count($result) ? $result->toArray() : [];
        $return['data'] = array();
        foreach ($result as $d) {
            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }

    public function ajaxRequestableConsumable(Request $request)
    {
        $req = $request->all();

        $fields = array(
            '1' => 'a.name',
            '2' => 'con_batch_no',
            '3' => 'cat.name',
            '4' => 'loc.name',
            '5' => 'a.qty',
        );

        $db = DB::table('consumables as a');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('asset_logs as al', function ($q) {
            $q->on('al.consumable_id', '=', 'a.id');
            $q->where('al.action_type', 'like', 'requested');
            $q->where('al.asset_type', '=', 'consumable');
            $q->where('al.user_id', '=', Auth::user()->id);
            $q->whereNull('al.accepted_id');
        });
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(id) as used_seats FROM `consumables_users` where (assigned_to is not null or user_id is not null) group by consumable_id) as lu2'), function ($j) {
            $j->on('a.id', '=', 'lu2.consumable_id');
        });
        $db->select('a.id', 'al.id as requested_id', 'a.name', 'loc.name as loc_name', 'cat.name as cat_name', 'a.qty', 'a.order_number', 'a.requestable', 'a.image');
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","CN",a.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","CNS",a.id) else "" end as con_batch_no'));
        }
        $db->where("a.requestable", "=", 1);
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);
        $db->addSelect(DB::raw('case when lu2.used_seats is not null then (a.qty - lu2.used_seats) else a.qty end as remaining'));
        $db->havingRaw('remaining > 0');

        // $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        // $settings = Settings::getSettings();
        // $permitted_loc = explode(",", $loc_previllage);
        // if($settings->location_config == 1) {
        //     if(empty($loc_previllage)) {
        //         $db->where('a.location_id', '=', 0);
        //     } else {
        //         $db->whereIn('a.location_id', $permitted_loc);
        //     }
        // }
        // // check asset dept permission
        // if ($settings->department_config == 1) {
        //     $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
        //     $asset_depts = explode(",", $asset_dept_permission);
        //     if (empty($asset_dept_permission)) {
        //         $db->where('a.department_id', '=', 0);
        //     } else {
        //         $db->whereIn('a.department_id', $asset_depts);
        //     }
        // }

        // if(!Auth::user()->isSuperUser()) {
        //     if(Settings::first()->full_multiple_companies_support != 1)
        //         $db->where("a.company_id", "=", Auth::user()->company_id);
        // }
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["location"]) && $req['location'] && $req['location'] != "null") {
            $array = explode(",", $req['location']);
            $db->whereIn("a.location_id", $array);
            $return['recordsTotal'] = $db->count();
            $return['recordsFiltered'] = $return['recordsTotal'];
        }
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];
        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('((case when a.id is not null then concat_ws("","CN",a.id) else "" end) like "%%%1$s%%" or a.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('((case when a.id is not null then concat_ws("","CNS",a.id) else "" end) like "%%%1$s%%" or a.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%")', $search_key);
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

        $data = $db->get();
        $return['data'] = array();
        foreach ($data as $key => $d) {
            $consumable = Consumable::withCount(['users', 'places', 'device'])->find($d->id);
            $allocated_qty = (optional($consumable)->users_count ?? 0) + (optional($consumable)->places_count ?? 0) + (optional($consumable)->devices_count ?? 0) + (optional($consumable)->scrap_qty ?? 0);
            $d->available_qty = $d->qty - $allocated_qty;
            $d->allocated_qty = $allocated_qty;
            if ($d->available_qty <= 0) {
                unset($data[$key]);
                continue;
            }

            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }

    public function requestConsumable(Request $request, $id)
    {
        $return = [];
        $appSettings = Settings::first();
        $objConsumable = Consumable::where('id', '=', $id)->where('requestable', '=', 1)->whereNull('deleted_at')->first();
        $user = Auth::user();
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        if (!$objConsumable) {
            return response()->json(['status' => 'error', 'msg' => 'Device not found !!']);
        }

        if (!in_array($objConsumable->company_id, $companyIds)) {
            $return["status"] = 'error';
            $return["section"] = 'request-device';
            $return["msg"] = "You don't have access to this company.";
            return response()->json($return);
        }

        $logaction = new Actionlog();
        $logaction->consumable_id = $objConsumable->id;
        $logaction->asset_type = 'consumable';
        $logaction->created_at = $logaction->requested_at = date("Y-m-d h:i:s");
        $logaction->location_id = $user->location_id ? $user->location_id : null;
        $logaction->user_id = $user->id;
        $logaction->action_type = 'requested';
        $logaction->save();

        $settings = Settings::getSettings();
        return response()->json(['status' => 'success', 'section' => 'approve-request', 'msg' => 'Consumable requested successfully']);
    }

    public function scrapConsumable(Request $request)
    {
        $return = array(
            "status" => "danger",
            "msg" => trans('consumables.controller.unable_to_scrap_this_consumable')
        );
        if (!Auth::user()->hasPermissionTo('ConsumableScrap') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.permission_denied');
            return response()->json($return);
        }
        $data = $request->only("id", "scrap_qty");

        $validate = Validator::make($data, [
            "scrap_qty" => [
                "required",
                "numeric",
            ],
        ]);

        if ($validate->fails()) {
            $v = $validate->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $id = $data["id"];
        $con = Consumable::find($id);

        $allocated = Consumable::where('id', $id)->withCount(['users', 'places', 'device'])->first();
        $allocated_user_qty = $allocated->users_count;
        $allocated_place_qty = $allocated->places_count;
        $allocated_device_qty = $allocated->device_count;
        $total_checkouts = $allocated_user_qty + $allocated_place_qty + $allocated_device_qty;
        $availableQty = $con->qty - $total_checkouts - $con->scrap_qty;

        $scrapQty = $data["scrap_qty"];
        if ($scrapQty > $availableQty) {
            return response()->json([
                'success' => false,
                'msg' => "Scrap quantity cannot be more than available quantity ($availableQty)."
            ]);
        }
        $newScrapQty = (int) $data['scrap_qty'];
        $con->scrap_qty = (int) $con->scrap_qty + $newScrapQty;
        $con->save();
        $log = new Actionlog();
        $log->asset_type = "consumable";
        $log->consumable_id = $con->id;
        $log->user_id = Auth::user()->id;
        $log->action_type = "Scrap";
        $log->note = "The scrap quantity is $newScrapQty.";
        $log->save();

        $admin = Auth::user()->email;
        try {
            if (config('mail.service_enabled') && filter_var($admin, FILTER_VALIDATE_EMAIL)) {
                $alertnotify = Settings::first()->alerts_enabled == 1 ? CommonHelper::getGlobalAlertEmail() : [];
                $validAlertEmails = array_filter($alertnotify, function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });

                if ($admin) {
                    Mail::to($admin)->cc($validAlertEmails)->queue(new ConsumableScrap($log, $con));
                } elseif (!empty($validAlertEmails)) {
                    Mail::to($validAlertEmails)->queue(new ConsumableScrap($log, $con));
                }
            }
        } catch (\Exception $e) {
            Log::error('Consumable Scrap Email Failed: ' . $e->getMessage());
        }

        $return["status"] = "success";
        $return["msg"] = "Consumable has been scrapped successfully.";
        return response()->json($return);
    }

    public function revertScrapConsumable(Request $request)
    {
        $return = array(
            "status" => "danger",
            "msg" => trans('consumables.controller.unable_to_scrap_revert_this_consumable')
        );
        if (!Auth::user()->hasPermissionTo('ConsumableRevertScrap') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.permission_denied');
            return response()->json($return);
        }
        $data = $request->only("id", "scrap_qty");

        $validate = Validator::make($data, [
            "scrap_qty" => [
                "required",
                "numeric",
            ],
        ]);

        if ($validate->fails()) {
            $v = $validate->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $id = $data["id"];
        $con = Consumable::find($id);

        $scrapQty = $data["scrap_qty"];
        if ($scrapQty > $con->scrap_qty) {
            return response()->json([
                'success' => false,
                'msg' => "Reverted scrap quantity cannot be more than scrap quantity ($scrapQty)."
            ]);
        }
        $newScrapQty = (int) $data['scrap_qty'];
        $con->scrap_qty = (int) $con->scrap_qty - $newScrapQty;
        $con->save();

        $log = new Actionlog();
        $log->asset_type = "consumable";
        $log->consumable_id = $con->id;
        $log->user_id = Auth::user()->id;
        $log->action_type = "Revert Scrap";
        $log->note = "The revert scrap quantity is $newScrapQty.";
        $log->save();

        $admin = Auth::user()->email;
        try {
            if (config('mail.service_enabled') && filter_var($admin, FILTER_VALIDATE_EMAIL)) {
                $alertnotify = Settings::first()->alerts_enabled == 1 ? CommonHelper::getGlobalAlertEmail() : [];
                $validAlertEmails = array_filter($alertnotify, function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });

                if ($admin) {
                    Mail::to($admin)->cc($validAlertEmails)->queue(new ConsumableScrapRevert($log, $con));
                } elseif (!empty($validAlertEmails)) {
                    Mail::to($validAlertEmails)->queue(new ConsumableScrapRevert($log, $con));
                }
            }
        } catch (\Exception $e) {
            Log::error('Consumable Scrap Revert Email Failed: ' . $e->getMessage());
        }

        $return["status"] = "success";
        $return["msg"] = "Consumable has been reverted from scrap successfully.";
        return response()->json($return);
    }

    public function getConsumableForDropDown(Request $request)
    {
        $return = array();
        try {
            $search = $request->input("search", "");
            $page = $request->input("page", 1);
            $skip = (($page * 10) - 10);

            $db = DB::table("consumables as a");
            if (config("app.client") == "etherealmachines") {
                $db->select("a.id", DB::raw("concat_ws('-', concat('CN', a.id), a.name) as text"));
            } else {
                $db->select("a.id", DB::raw("concat_ws('-', concat('CNS', a.id), a.name) as text"));
            }
            $db->whereNull('a.deleted_at');
            if ($search) {
                $db->whereRaw("a.name like '%" . $search . "%')");
            }
            $count = $db->count();
            $db->skip($skip)->take(10);
            $result = $db->get();
            $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
            $return["results"] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error("getConForDropDown: " . $e->getMessage());
        }
        return response()->json($return);
    }

    public function attachmentDelete(Request $request, $id)
    {
        $imagedata = Consumable::find($id)->image;
        if ($imagedata != null) {
            $fileExists = File::exists(storage_path("app/public/uploads/consumable/$imagedata"));
            if ($fileExists) {
                File::delete(storage_path("app/public/uploads/consumable/$imagedata"));
                Consumable::where('id', $id)->update(['image' => null]);
                    $return["msg"] = trans('consumables.controller.consumable_attachment_deleted_success');
                $return["status"] = "success";
            } else {
                $return["msg"] = trans('consumables.controller.consumable_attachment_delete_failed');
                $return["status"] = "success";
            }

            return response()->json($return);
        }
    }
}

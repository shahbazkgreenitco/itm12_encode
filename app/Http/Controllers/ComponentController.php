<?php

namespace App\Http\Controllers;

use App\Exports\CustomFieldsAccessories;
use App\Exports\NetworkInventory\ComponentDetails;
use App\Http\Controllers\Controller;
use App\Imports\ComponentImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Base;
use App\Models\Model;
use App\Models\Location;
use App\Models\Place;
use App\Models\Purchase;
use App\Models\Company;
use App\Models\Category;
use App\Models\Currency;
use App\Models\User;
use App\Models\CustomField;
use App\Models\Settings;
use App\Models\Component;
use App\Models\Device;
use App\Models\Supplier;
use App\Models\Manufacture;
use App\Models\Department;
use App\Models\Actionlog;
use App\Models\ThresholdSettings;
use App\Models\Threshold;
use App\Models\CustomFieldset;
use App\Helpers\Common as CommonHelper;
use App\Mail\ComponentCheckoutNotification;
use App\Mail\ComponentCheckinNotification;
use Mail;
use Validator;
use Auth;
use DB;
use Log;
use PDF;
use Image;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use App\Mail\ThreshouldNotification;
use App\Models\UserDetails;

class ComponentController extends Controller {

    public function getIndex(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('ComponentRead') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $purchaseFilter = null;
        if (isset($request->q)) {
            $purchaseFilter = $request->q;
        }
        // $companyId = CommonHelper::getAccessibleCompanyIds();
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $companies = Company::select("id", "name as text")->whereNull('deleted_at')->get()->toArray();
        $categories = Category::select("id", "name as text")->where("category_type","like","component")->get()->toArray();
        $location = Location::select('id', 'name as text')->whereIn("company_id", $companyIds)->get();
        $internalPlaces = Place::select('places.id', DB::raw('CONCAT(locations.name, " - ", places.place) as text'))
                                ->join('locations', 'places.location_id', 'locations.id')
                                ->whereIn("locations.company_id", $companyIds)
                                ->orderBy('places.place')->get();
        $location_filter = $category_id = "null";
        $department_filter = isset($request->department) ? $request->department : "null";
        if( $request->location != null && $request->location != "null"){
            $location_filter = $request->location;
        } else if($request->city != null && $request->city != "null") {
            $id = Location::whereIn('city_id',array($request->city))->pluck('id')->toArray();
            $location_filter = empty($id) ? 0 : implode(',', $id);
        } else if($request->states != null && $request->states != "null") {
            $id = Location::whereIn('state_id',array($request->states))->pluck('id')->toArray();
            $location_filter = empty($id) ? 0 : implode(',', $id);
        } else if($request->zone != null && $request->zone != "null") {
            $id = Location::whereIn('zone',array($request->zone))->pluck('id')->toArray();
            $location_filter = empty($id) ? 0 : implode(',', $id);
        } else if($request->country != null && $request->country != "null") {
            $id = Location::whereIn('country_id',array($request->country))->pluck('id')->toArray();
            $location_filter = empty($id) ? 0 : implode(',', $id);
        }
        if(isset($request->category) && $request->category != "null"){
            $category_id = explode(',', $request->category);
        }
        $currencies = Currency::getCurrencies();
        $sort_fields = [
            ["id"=>1,"text"=>"Tag"],
            ["id"=>2,"text"=>"Component Name"],
            ["id"=>3,"text"=>"Component Serial"],
            ["id"=>4,"text"=>"Category"],
            ["id"=>5,"text"=>"Location"],
            ["id"=>6,"text"=>"Status"],
            ["id"=>7,"text"=>"Checkout Device"],
            ["id"=>8,"text"=>"Checkout Date"],
            ["id"=>9,"text"=>"Purchase Cost"],
            ["id"=>10,"text"=>"Updated On"]
        ];
        $component = new Component;
        $companyFieldset = CustomFieldset::where('id', Settings::first()->component_custom_fieldset_id)->first();
        $requestable_enabled = config('app.requestable_enabled');
        return view("components.index")->with("sort_fields",$sort_fields)->with('companyFieldset', $companyFieldset)->with('category_id',$category_id)->with(compact("companies", "categories", "currencies", "component", "location", "location_filter", "department_filter", "requestable_enabled","purchaseFilter", "internalPlaces"));
    }

    public function ajaxIndex(Request $request) {
        $return = ["total"=>0,"filtered"=>0,"data"=>[]];
        $req = $request->all();

        $fields = array(
            '1' => 'a.unique_tag',
            '2' => 'a.name',
            '3' => 'a.serial',
            '4' => 'cat.name', 
            '5' => 'loc.name',
            '6' => 'status_name',
            '7' => 'chkout_dev.asset_tag',
            '8' => 'a.checked_out_at',
            '9' => 'a.purchase_cost',
            '10' => 'a.updated_at'
        );
        
        $db = DB::table('components as a');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'a.manufacturer_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('suppliers as sup', 'sup.id', '=', 'a.supplier_id');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');
        $db->leftJoin('assets as par_dev', 'par_dev.id', '=', 'a.parent_device');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->leftJoin('assets as chkout_dev', 'chkout_dev.id', '=', 'a.checked_out_to');
        $db->leftJoin('users as chkout_user', 'chkout_user.id', '=', 'a.checked_out_to');
        $db->leftJoin('places as pla', 'pla.id', '=', 'a.internal_place_id');

        $db->select('a.purchase_currency','a.id', 'a.notes', 'mnu.name as manu_name','mnu.attachment','cat.image_thumbnail as cat_img', 'cmp.name as cmp_name', 'loc.name as loc_name', 'par_dev.serial as par_dev_tag' , 'cat.name as cat_name', 'a.name', 'a.serial', 'a.unique_tag', 'a.company_id', 'a.checked_out_to', 'a.origin_from','a.parent_device', 'a.purchase_cost', 'pur.invoice_no','a.checked_out_at','dep.name as department', 'pla.place as internal_place_name', 'a.image');
        $db->addSelect(DB::raw('CASE WHEN a.deleted_at IS NULL THEN 0 ELSE 1 END as is_deleted'));
        $db->addSelect(DB::raw('chkout_dev.asset_tag as chkout_dev_tag'));
        $db->addSelect(DB::raw("case when a.checked_out_to is not null and a.checked_out_to > 0 then 2 when a.status = 4 then 2 when a.status = 1 then 1 else 0 end as check_action"));
        $db->addSelect(DB::raw('DATE_FORMAT(a.checked_out_at, "%d %b %Y %h:%i %p") as last_checkout_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.expected_checkin_at, "%d %b %Y") as expected_checkin_at'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y %h:%i %p") as purchase_date'));
        $db->addSelect(DB::raw('case when a.status = 1 then "Usable" when a.status = 2 then "Lost" when a.status = 0 then "Repair" when a.status = 3 then "Scrap" when a.status = 4 then "Deployed" end as status_name'));
        $db->addSelect(DB::raw('case when a.origin_from = 1 then "Purchase" when a.origin_from = 2 then "Existing Device" when a.origin_from = 3 then "Others" when a.origin_from = 4 then "Via Network" end as origin_from_name'));
        $db->addSelect(DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when a.checked_out_for = 1 then concat(chkout_user.first_name, " ", chkout_user.last_name) when a.checked_out_for = 2 then chkout_dev.asset_tag else null end as checked_out_name'));
        $db->addSelect(DB::raw('a.checked_out_for'));
        
        if (isset($req['showDeletedComponents']) && $req['showDeletedComponents'] === 'true') {
            $db->whereNotNull('a.deleted_at');
        } else {
            $db->whereNull('a.deleted_at');
        }
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
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
        if($location_filter != null && $location_filter != 'null') {
            $db->whereIn("a.location_id", explode(",", $location_filter));
        }
        $category_id_filter = isset($req['category_id']) ? $req['category_id'] : $request->input("category_id", null);
        if($category_id_filter != null && $category_id_filter != 'null') {
            $db->whereIn("a.category_id", $category_id_filter);
        }
        $department_filter = isset($req['department']) ? $req['department'] : $request->input("department", null);
        if( $department_filter != null && $department_filter != 'null') {
            $db->whereIn("a.department_id", explode(",", $department_filter));
        }

        if (isset($request->q) && $request->q != null) {
            $decoded = array_map('intval', explode(',', trim(base64_decode($request->q), '"')));
            $db->whereIn('a.id', $decoded);
        }

        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];

        $is_searching = false;
        if(isset($req["filters"])) {
            $filters = $req["filters"];

            $filter_cond = "";
            if(isset($filters["companies"]) && $filters['companies'] && $filters['companies'] != "null") {
                $db->where("a.company_id", (int) $filters['companies']);
            }
            if(isset($filters["condition"]) && $filters['condition'] && $filters['condition'] != "null") {
                $db->whereIn("a.status", $filters['condition']);
            }
            if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                $db->whereIn("a.location_id", $filters['location']);
            }    
            if(isset($filters["purchase_reference"]) && !empty($filters["purchase_reference"]) && $filters['purchase_reference'] != "null") {
                $db->whereIn("a.invoice_id", $filters['purchase_reference']);
            }
            if(isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                $db->whereIn("a.internal_place_id", $filters['internal_place']);
            }
            if(isset($filters["categories"]) && $filters['categories'] && $filters['categories'] != "null") {
                $db->whereIn("a.category_id", $filters['categories']);
            }
            if(isset($filters["assigned_device"]) && $filters['assigned_device'] && $filters['assigned_device'] != "null") {
                $db->whereIn("a.checked_out_to", $filters['assigned_device']);
            }
            if(isset($filters["assigned_user"]) && $filters['assigned_user'] && $filters['assigned_user'] != "null") {
                $db->whereIn("a.checked_out_to", $filters['assigned_user']);
            }
            if(isset($filters["origin_info"]) && $filters['origin_info'] && $filters['origin_info'] != "null") {
                $db->whereIn("a.origin_from", $filters['origin_info']);
            }
            if(isset($filters["asset_department"]) && $filters['asset_department'] && $filters['asset_department'] != "null") {
                $db->whereIn("a.department_id", $filters['asset_department']);
            }
            $based_on_possible = ['1'=>'a.checked_out_at', '2'=>'a.expected_checkin_at','3'=>'a.purchase_date', '4'=>'a.updated_at'];
            if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 4 ) {
                if(isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                    $daterange = explode(" - ", $filters["date_range"]);
                    $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                    $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                    if($from_date && $to_date) {
                        $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                        $db->whereRaw($whereStr);
                    }
                }
            }
           
            $is_searching = true;
           
        }

        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
            $whereStr = sprintf('(a.unique_tag like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or a.serial like "%%%1$s%%" or mnu.name like "%%%1$s%%" or pur.invoice_no like "%%%1$s%%" or pla.place like "%%%1$s%%" or cmp.name like "%%%1$s%%" or chkout_dev.asset_tag like "%%%1$s%%" or loc.name like "%%%1$s%%" or (case when a.status = 1 then "Usable" when a.status = 2 then "Lost" when a.status = 0 then "Repair" when a.status = 3 then "Scrap" when a.status = 4 then "Deployed" end) like "%%%1$s%%" or (case when a.origin_from = 1 then "Purchase" when a.origin_from = 2 then "Existing Device" when a.origin_from = 3 then "Others" when a.origin_from = 4 then "Via Network" end) like "%%%1$s%%" or
            FORMAT(a.purchase_cost, 2) like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or DATE_FORMAT(a.checked_out_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.expected_checkin_at, "%%d %%b %%Y") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['filtered'] = $db->count();
        }

        if($is_searching) {
            $return['filtered'] = $db->count();
        }

        if( isset($req["order"]["id"]) && isset($fields[$req["order"]["id"]]) && in_array($req["order"]["dir"], [1,2]) ) {
            $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
            $db->orderBy($fields[$req["order"]["id"]], $dir);
        }

        $page = $request->input("page", 1);
        $take = $request->input("size", 10);
        $skip = ($page * $take) - $take;
        if($return["filtered"] < $skip) {
            $page = 1;
            $skip = 0;
        }

        $db->skip($skip);
        $db->take($take);
        $return["data"] = array();

        foreach ($db->get() as $item) {
            $currency_format = Currency::getCurrencyByCode($item->purchase_currency);
            if (is_array($currency_format) && isset($currency_format['symbol'])) {
                $item->purchase_currency = $currency_format['symbol'];
            } else {
                $item->purchase_currency = $item->purchase_currency; // Keep original if invalid
            }
            $componentPath = public_path('uploads/component/' . $item->image);
            $categoryPath = public_path('uploads/category/' . $item->cat_img);
           
            $item->component_img = null;
            if (!empty($item->image) && file_exists($componentPath)) {
                $item->component_img = url('uploads/component/' . $item->image);
            } elseif (!empty($item->cat_img) && file_exists($categoryPath)) {
                $item->component_img = url('uploads/category/' . $item->cat_img);
            }
            $return['data'][] = array('a' => $item);
        }
        $return["page"] = $page;
        return response()->json($return);
    }

    public function componentExport(Request $request) {

        $return = ['status' => 'danger', 'msg' => trans('content.component_fields.Unable_to_export_the_Component')];
        if(! Auth::user()->hasPermissionTo('ComponentDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $db = DB::table('components as a');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'a.manufacturer_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('places as p', 'p.id', '=', 'a.internal_place_id');
        $db->leftJoin('suppliers as sup', 'sup.id', '=', 'a.supplier_id');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');
        $db->leftJoin('itm_network_inventory_basic as b', 'b.id', '=', 'a.parent_device');
        // $db->leftJoin('assets as par_dev', 'par_dev.id', '=', 'a.parent_device');
        $db->leftJoin('assets as chkout_dev', 'chkout_dev.id', '=', 'a.checked_out_to');
        $db->leftJoin('users as user', 'user.id', '=', 'chkout_dev.assigned_to');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->select('a.*', 'mnu.name as manu_name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'cat.name as cat_name', 'pur.invoice_no','a.checked_out_at','sup.name as sup_name','dep.name as department','p.place as internal_place');
        $db->addSelect(DB::raw('chkout_dev.asset_tag as chkout_dev_tag'));
        $db->addSelect(DB::raw('b.ComputerName as par_dev_tag'));
        $db->addSelect(DB::raw("case when a.checked_out_to is not null and a.checked_out_to > 0 then 2 when a.status = 4 then 2 when a.status = 1 then 1 else 0 end as check_action"));
        $db->addSelect(DB::raw('DATE_FORMAT(a.checked_out_at, "%d %b %Y %h:%i %p") as last_checkout_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.expected_checkin_at, "%d %b %Y %h:%i %p") as expected_checkin_at'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        $db->addSelect(DB::raw('case when a.status = 1 then "Usable" when a.status = 2 then "Lost" when a.status = 0 then "Repair" when a.status = 3 then "Scrap" when a.status = 4 then "Deployed" end as status_name'));
        $db->addSelect(DB::raw('case when a.origin_from = 1 then "Purchase" when a.origin_from = 2 then "Existing Device" when a.origin_from = 3 then "Others" when a.origin_from = 4 then "Via Network" end as origin_from_name'));
        $db->addSelect(DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when a.checked_out_for  = 1 then "User" when a.checked_out_for = 2 then "Device" end as checkout_for'));
        $db->addSelect(DB::raw('case when a.checked_out_for = 1 then user.username when a.checked_out_for = 2 then chkout_dev.asset_tag end as checked_out_name'));
        // $db->addSelect(DB::raw('case when chkout_dev.assigned_for = 1 then concat(user.first_name, " ", user.last_name) else "" end as user_name'));

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);
        if (isset($request['showDeletedComponents']) && $request['showDeletedComponents'] === 'true') {
            $db->whereNotNull('a.deleted_at');
        } else {
            $db->whereNull('a.deleted_at');
        }

        $settings = Settings::getSettings();
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
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

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);

            if(isset($filters->over_all_purchase_filter)) {
                $ids = array_map('intval', explode(',', trim(base64_decode($filters->over_all_purchase_filter), '"')));
                $db->whereIn('a.id', $ids);
            }

            $req = [];
            if(isset($filters->dashboard_filters)){
                $req["dashboard_filters"] = (array) $filters->dashboard_filters;
                $location_filter = isset($req["dashboard_filters"]['location']) ? $req["dashboard_filters"]['location'] : "null";
                if( $location_filter != null && $location_filter != 'null') {
                    $db->whereIn("a.location_id", explode(",", $location_filter));
                }
                $category_id_filter = isset($req["dashboard_filters"]['category_id']) ? $req["dashboard_filters"]['category_id'] : "null";
                if($category_id_filter != null && $category_id_filter != 'null') {
                    $db->whereIn("a.category_id", $category_id_filter);
                }
                $department_filter = isset($req["dashboard_filters"]['department']) ? $req["dashboard_filters"]['department'] : "null";
                if($department_filter != null && $department_filter != 'null') {
                    $db->whereIn("a.department_id", explode(",", $department_filter));
                }
            }
            if(isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if(isset($filters->other_filters)){
                $req["filters"] = (array) $filters->other_filters;
            }
            if (isset($filters->showDeletedComponents) && $filters->showDeletedComponents === true) {
                $db->whereNotNull('a.deleted_at');
            } else {
                $db->whereNull('a.deleted_at');
            }
            if(isset($req["filters"])) {
                $filters = $req["filters"];

                if(isset($filters["companies"]) && $filters['companies'] && $filters['companies'] != "null") {
                    $db->where("a.company_id", "=", (int) $filters['companies']);
                }
                if(isset($filters["condition"]) && $filters['condition'] && $filters['condition'] != "null") {
                    $db->where("a.status",  $filters['condition']);
                }
                if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                    $db->whereIn("a.location_id", $filters['location']);
                }
                if(isset($filters["purchase_reference"]) && !empty($filters["purchase_reference"]) && $filters['purchase_reference'] != "null") {
                    $db->whereIn("a.invoice_id", $filters['purchase_reference']);
                }
                if(isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                    $db->whereIn("a.internal_place_id", $filters['internal_place']);
                }
                if(isset($filters["categories"]) && $filters['categories'] && $filters['categories'] != "null") {
                    $db->whereIn("a.category_id", $filters['categories']);
                }
                if(isset($filters["assigned_device"]) && $filters['assigned_device'] && $filters['assigned_device'] != "null") {
                    $db->whereIn("a.checked_out_to", $filters['assigned_device']);
                }
                if(isset($filters["assigned_user"]) && $filters['assigned_user'] && $filters['assigned_user'] != "null") {
                    $db->whereIn("a.checked_out_to", $filters['assigned_user']);
                }
                if(isset($filters["origin_info"]) && $filters['origin_info'] && $filters['origin_info'] != "null") {
                    $db->whereIn("a.origin_from", $filters['origin_info']);
                }
                if(isset($filters["asset_department"]) && $filters['asset_department'] && $filters['asset_department'] != "null") {
                    $db->whereIn("a.department_id", $filters['asset_department']);
                }
                $based_on_possible = ['1'=>'a.checked_out_at', '2'=>'a.expected_checkin_at', '3'=>'a.purchase_date', '4'=>'a.updated_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 4 ) {
                    if(isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                        $daterange = explode(" - ", $filters["date_range"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $db->whereRaw($whereStr);
                        }
                    }
                }

                if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                    $whereStr = sprintf('(a.unique_tag like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or mnu.name like "%%%1$s%%" or pur.invoice_no like "%%%1$s%%" or cmp.name like "%%%1$s%%" or chkout_dev.asset_tag like "%%%1$s%%" or loc.name like "%%%1$s%%" or (case when a.status = 1 then "Usable" when a.status = 2 then "Lost" when a.status = 0 then "Repair" when a.status = 3 then "Scrap" when a.status = 4 then "Deployed" end) like "%%%1$s%%" or (case when a.origin_from = 1 then "Purchase" when a.origin_from = 2 then "Existing Device" when a.origin_from = 3 then "Others" when a.origin_from = 4 then "Via Network" end) like "%%%1$s%%" or FORMAT(a.purchase_cost, 2) like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or DATE_FORMAT(a.checked_out_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.expected_checkin_at, "%%d %%b %%Y") like "%%%1$s%%")', $search_key);
                    $db->whereRaw($whereStr);

                }
            }
        }
        $records = $db->get();
        $lineArray = [];
        $keys = ['Component Tag','Component Name','Company','Department','Category','Location','Internal Place','Status','Serial','Supplier','Order Number','Purchase Currency','Purchase Cost','Notes','Checkout For','Checkout To','Checkout Date','Expected Checkin Date'];
        $settings = Settings::first();
        $customFields = [];
        $prefixed_code = [];
        if($settings->component_custom_fieldset_id != "") {
            $customFieldset = CustomFieldset::find($settings->component_custom_fieldset_id);
            if(!empty($customFieldset->fields)) {
                foreach ($customFieldset->fields as $f) {
                    array_push($keys, $f->name);
                }
            }
        }
        foreach($records as $key => $r) {
            $data = [
                $r->unique_tag,
                $r->name,
                $r->cmp_name,
                $r->department,
                $r->cat_name,
                $r->loc_name,
                $r->internal_place,
                $r->status_name,
                $r->serial,
                $r->sup_name,
                $r->order_number,
                $r->purchase_currency,
                $r->purchase_cost,
                $r->notes,
                $r->checkout_for,
                $r->checked_out_name,
                $r->last_checkout_on,
                $r->expected_checkin_at
            ];
            if ($settings->component_custom_fieldset_id) {
                if(!empty($customFieldset)) {
                    $customaCol = json_decode(json_encode($r), true);
                    $fieldData  = CommonHelper::getCustomData($customFieldset->fields, $customaCol);
                    $dataFieldset = [];
                    foreach ($fieldData  as $k => $f) {
                        $col_name = ucwords(str_replace(['_itm_', '_'], ' ', $k));
                        $dataFieldset[$col_name] = $f;
                    }

                    $data = array_merge($data, array_values($dataFieldset)); 
                }
            }
            
            $component = Component::withTrashed()->find($r->id);
            if (isset($component) && !empty($component) && $component->category_id && $component->category->customFieldset && count($component->category->customFieldset->fields)) {
               foreach ($component->category->customFieldset->fields as $field) {
                    $con = '_itm_'.''.str_replace(' ', '_', strtolower($field->name));
                    if (!in_array(ucwords($field->name), $keys)) {
                        array_push($keys, ucwords($field->name));
                    }
                    $modelCusValue = CommonHelper::getCustomDataFormate($field,$component->$con);
                    array_push($data, $modelCusValue);
                }
            }
            $lineArray[] = $data;
        }
        $data = json_decode(json_encode($lineArray, true),true);
        return Excel::download(new ComponentDetails($data, $keys), 'ComponentDetails.xlsx');
    }

    public function componentExportPDF(Request $request) {
        
        $return = ['status' => 'danger', 'msg' => trans('content.component_fields.Unable_to_export_the_details')];
        if(! Auth::user()->hasPermissionTo('ComponentDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = [];
        $db = DB::table('components as a');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'a.manufacturer_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('suppliers as sup', 'sup.id', '=', 'a.supplier_id');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');
        $db->leftJoin('itm_network_inventory_basic as b', 'b.id', '=', 'a.parent_device');
        // $db->leftJoin('assets as par_dev', 'par_dev.id', '=', 'a.parent_device');
        $db->leftJoin('assets as chkout_dev', 'chkout_dev.id', '=', 'a.checked_out_to');
        $db->leftJoin('users as user', 'user.id', '=', 'chkout_dev.assigned_to');

        $db->select('a.id', 'mnu.name as manu_name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'cat.name as cat_name', 'a.name', 'a.serial', 'a.unique_tag', 'a.company_id', 'a.checked_out_to', 'a.origin_from', 'a.parent_device', 'a.purchase_cost', 'pur.invoice_no','a.checked_out_at');
        $db->addSelect(DB::raw('chkout_dev.asset_tag as chkout_dev_tag'));
        $db->addSelect(DB::raw('b.ComputerName as par_dev_tag'));
        $db->addSelect(DB::raw("case when a.checked_out_to is not null and a.checked_out_to > 0 then 2 when a.status = 4 then 2 when a.status = 1 then 1 else 0 end as check_action"));
        $db->addSelect(DB::raw('DATE_FORMAT(a.checked_out_at, "%d %b %Y %h:%i %p") as last_checkout_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.expected_checkin_at, "%d %b %Y %h:%i %p") as expected_checkin_at'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        $db->addSelect(DB::raw('case when a.status = 1 then "Usable" when a.status = 2 then "Lost" when a.status = 0 then "Repair" when a.status = 3 then "Scrap" when a.status = 4 then "Deployed" end as status_name'));
        $db->addSelect(DB::raw('case when a.origin_from = 1 then "Purchase" when a.origin_from = 2 then "Existing Device" when a.origin_from = 3 then "Others" when a.origin_from = 4 then "Via Network" end as origin_from_name'));
        $db->addSelect(DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when chkout_dev.assigned_for = 1 then concat(user.first_name, " ", user.last_name) else "" end as user_name'));
        $db->addSelect(DB::raw('case when a.checked_out_for  = 1 then "User" when a.checked_out_for = 2 then "Device" end as checkout_for'));
        $db->addSelect(DB::raw('case when a.checked_out_for = 1 then user.username when a.checked_out_for = 2 then chkout_dev.asset_tag end as checked_out_name'));

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
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

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if(isset($filters->dashboard_filters)){
                $req["dashboard_filters"] = (array) $filters->dashboard_filters;
                $location_filter = isset($req["dashboard_filters"]['location']) ? $req["dashboard_filters"]['location'] : "null";
                if( $location_filter != null && $location_filter != 'null') {
                    $db->whereIn("a.location_id", explode(",", $location_filter));
                }
                $category_id_filter = isset($req["dashboard_filters"]['category_id']) ? $req["dashboard_filters"]['category_id'] : "null";
                if($category_id_filter != null && $category_id_filter != 'null') {
                    $db->whereIn("a.category_id", $category_id_filter);
                }
                $department_filter = isset($req["dashboard_filters"]['department']) ? $req["dashboard_filters"]['department'] : "null";
                if($department_filter != null && $department_filter != 'null') {
                    $db->whereIn("a.department_id", explode(",", $department_filter));
                }
            }
            if(isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if(isset($filters->other_filters)){
                $req["filters"] = (array) $filters->other_filters;
            } 
            if (isset($filters->showDeletedComponents) && $filters->showDeletedComponents === true) {
                $db->whereNotNull('a.deleted_at');
            } else {
                $db->whereNull('a.deleted_at');
            }
            if(isset($req["filters"])) {
                $filters = $req["filters"];
    
                if(isset($filters["companies"]) && $filters['companies'] && $filters['companies'] != "null") {
                    $db->where("a.company_id", "=", (int) $filters['companies']);
                }
                if(isset($filters["condition"]) && $filters['condition'] && $filters['condition'] != "null") {
                    $db->where("a.status",  $filters['condition']);
                }
                if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                    $db->whereIn("a.location_id", $filters['location']);
                }
                if(isset($filters["purchase_reference"]) && !empty($filters["purchase_reference"]) && $filters['purchase_reference'] != "null") {
                    $db->whereIn("a.invoice_id", $filters['purchase_reference']);
                }
                if(isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                    $db->whereIn("a.internal_place_id", $filters['internal_place']);
                }
                if(isset($filters["categories"]) && $filters['categories'] && $filters['categories'] != "null") {
                    $db->whereIn("a.category_id", $filters['categories']);
                }
                if(isset($filters["assigned_device"]) && $filters['assigned_device'] && $filters['assigned_device'] != "null") {
                    $db->whereIn("a.checked_out_to", $filters['assigned_device']);
                }
                if(isset($filters["assigned_user"]) && $filters['assigned_user'] && $filters['assigned_user'] != "null") {
                    $db->whereIn("a.checked_out_to", $filters['assigned_user']);
                }
                if(isset($filters["origin_info"]) && $filters['origin_info'] && $filters['origin_info'] != "null") {
                    $db->whereIn("a.origin_from", $filters['origin_info']);
                }
                if (isset($filters["asset_department"]) && $filters['asset_department'] && $filters['asset_department'] != "null") {
                    $db->whereIn("a.department_id", $filters['asset_department']);
                }
                $based_on_possible = ['1'=>'a.checked_out_at', '2'=>'a.expected_checkin_at', '3'=>'a.purchase_date', '4'=>'a.updated_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 4 ) {
                    if(isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                        $daterange = explode(" - ", $filters["date_range"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $db->whereRaw($whereStr);
                        }
                    }
                }
                if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                    $whereStr = sprintf('(a.unique_tag like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or mnu.name like "%%%1$s%%" or pur.invoice_no like "%%%1$s%%" or cmp.name like "%%%1$s%%" or chkout_dev.asset_tag like "%%%1$s%%" or loc.name like "%%%1$s%%" or (case when a.status = 1 then "Usable" when a.status = 2 then "Lost" when a.status = 0 then "Repair" when a.status = 3 then "Scrap" when a.status = 4 then "Deployed" end) like "%%%1$s%%" or (case when a.origin_from = 1 then "Purchase" when a.origin_from = 2 then "Existing Device" when a.origin_from = 3 then "Others" when a.origin_from = 4 then "Via Network" end) like "%%%1$s%%" or FORMAT(a.purchase_cost, 2) like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or DATE_FORMAT(a.checked_out_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.expected_checkin_at, "%%d %%b %%Y") like "%%%1$s%%")', $search_key);
                    $db->whereRaw($whereStr);
                    
                }
            }
        }
        $records = $db->get();
    
        $data = $db->get();
        $properties = [];
        $properties['format'] = 'A4-L';
        
        $vd["records"] = $data;
        $pdf = PDF::loadView('components.for_export', $vd, [], $properties);
        return $pdf->download('components.pdf');
    }

    public function getComponent(Request $request, $id, $forAction) {
        $return = array("status"=>"failure", "msg"=>trans('content.component_fields.Unable_to_open_component_for_edit'));
        $component = Component::find($id);

        if(! $component || !in_array($forAction, ["edit","clone"])) {
            return response()->json($return);
        }

        $rd = [];
        $rd['data'] = $component->toArray();
        $rd['dropdown'] = [];
        $rd["custom_fields"]['all_fields'] = array();
        $rd["custom_fields"]['required_fields'] = array();
        $rd["custom_fields"]['html'] = "";

        /* get category */
        if($component->category_id) {
            $getCategory = Category::where("id", $component->category_id)->select("id", "name as text")->first();
            $rd["dropdown"]["category"] = $getCategory ? $getCategory->toArray() : null;
        }
        /* get department */
        if($component->department_id) {
            $department = Department::where("id", $component->department_id)->select("id", "name as text")->first();
            $rd["dropdown"]["department"] = $department->exists ? $department->toArray() : null;
        }

        /* get location */
        if($component->location_id) {
            $getLocation = Location::where("id", $component->location_id)->select("id", "name as text")->first();
            if(!empty($getLocation))
            $rd["dropdown"]["location"] = $getLocation && $getLocation->exists ? $getLocation->toArray() : null;
        }

        /*get internal place*/
        if ($component->internal_place_id) {
            $getPlace = Place::where("id", $component->internal_place_id)->select("id", "place as text")->first();
            if (!empty($getPlace))
                $rd["dropdown"]["internal_place"] = $getPlace && $getPlace->exists ? $getPlace->toArray() : null;
        }
        /* get supplier */
        if($component->supplier_id) {
            $getSupplier = Supplier::where("id", $component->supplier_id)->select("id", "name as text")->first();
            $rd["dropdown"]["supplier"] = $getSupplier && $getSupplier->exists ? $getSupplier->toArray() : null;
        }

        /* get purchase reference */
        if($component->invoice_id) {
            $getPurchase = Purchase::where("id", $component->invoice_id)->select("id", DB::raw('concat_ws(" - ", invoice_no, date_format(invoice_date, "%d/%m/%Y")) as text'))->first();
            $rd["dropdown"]["invoice"] = $getPurchase && $getPurchase->exists ? $getPurchase->toArray() : null;
        }

        /* get parent device */
        if($component->parent_device) {
            $getDevice = Device::where("id", $component->parent_device)->select("id", "asset_tag as text")->first();
            $rd["dropdown"]["parent_device"] = $getDevice && $getDevice->exists ? $getDevice->toArray() : null;
        }
        if($component->category_id) {
            $getComponent = Category::where("id", $component->category_id)->select("id", "name as text")->first();
            $rd["dropdown"]["category"] = $getComponent && $getComponent->exists ? $getComponent->toArray() : null;

            // load custom fields
            if(isset($component->category->customFieldset) && count($component->category->customFieldset->fields)) {
                // $rd["custom_fields"] = CommonHelper::formCustomFieldsLicence($component->category->customFieldset->fields, $rd["data"]['component_custom_fields']);
                $rd["custom_fields"] = CommonHelper::formCustomFields($component->category->customFieldset->fields, $rd["data"]);
            }
        }else{
            $component1 = ['all_fields' => [], 'required_fields' => [], 'html' => ''];  
            if(Settings::first()->component_custom_fieldset_id != null) {
                $fieldsetObj = CustomFieldset::where('id', Settings::first()->component_custom_fieldset_id)->first();
                if(!empty($fieldsetObj)) {
                    $component1 = CommonHelper::formCustomFields($fieldsetObj->fields, $rd["data"]);
                if(isset($component1['all_fields'][0])) {
                        array_push($rd["custom_fields"]['all_fields'], $component1['all_fields'][0]);
                }
                if(isset($component1['required_fields'][0])) {
                        array_push($rd["custom_fields"]['required_fields'], $component1['required_fields'][0]);
                }
                $rd["custom_fields"]['html'] .= $component1['html'];
                }
            }
        }

        $rd['data']['purchase_date'] = CommonHelper::getDateAs($component->purchase_date, "d/m/Y", "Y-m-d");

        if($forAction == "clone") {
            $rd["data"]["unique_tag"] = "";
            $rd["data"]["serial"] = "";
            if($rd["data"]["status"] == 4) {
                $rd["data"]["status"] = 1;
            }
        }

        $return['status'] = 'success';
        $return['msg'] = '';
        $return['component'] = $rd;
        return $return;
    }

    public function deleteComponent(Request $request) {
        $return = array("status"=>"failure", "msg"=>trans('content.component_fields.Unable_to_delete_component'));
        if(! Auth::user()->hasPermissionTo('ComponentDelete') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        $component = Component::find($request->id);

        if(!in_array($component->company_id, $companyIds)) {
            $return["msg"] = trans('content.component_fields.permission_denied_to_delete_the_component');
            return response()->json($return);
        }
        if(! $component) {
            return response()->json($return);
        }
        $companyId = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($component->company_id, $companyId)) {
            $msg = "You don't have access to this company.";
            return response()->json(["msg" => $msg]);
        }  
        if($component->checked_out_to) {
            $return["msg"] = trans('content.component_fields.component_get_checkout');
            return response()->json($return);
        }

        $component->delete();
        if ($component->delete()) {
            $log = new Actionlog();
            $log->asset_type = "component";
            $log->location_id = $component->location_id;
            $log->asset_id = $component->id;
            $log->note = $component->note;
            $log->user_id = Auth::user()->id;
            $log->action_type = "Delete";
            $log->save();
        }
        $return["status"] = "success";
        $return["msg"] = trans('content.component_fields.component_has_deleted_successfully');
        Log::info("deleteComponent id:" . $component->id." uid:" . Auth::user()->id . " : " . json_encode($request->all()));
        return response()->json($return);
    }

    public function restoreComponent(Request $request, $id) {
        $return = array("status" => "failure", "msg" => "Unable to restore component");

        if (!Auth::user()->hasPermissionTo('ComponentRestore') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        $component = Component::withTrashed()->where("id", $id)->first();
        if (!$component || !$component->exists) {
            return response()->json($return);
        }
        if (empty($component->deleted_at)) {
            return response()->json([
                'status'  => "success",
                'msg' => 'Component is already restored'
            ]);
        }
        $companyId = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($component->company_id, $companyId)) {
            $return["status"] = 'error';
            $return["msg"] = "You don't have access to this company.";
            return response()->json($return);
        }
        if ($component->restore()) {
            Log::info("restoreComponent id:" . $component->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));

            $log = new Actionlog();
            $log->asset_type = "component";
            $log->location_id = $component->location_id;
            $log->asset_id = $component->id;
            $log->user_id = Auth::user()->id;
            $log->action_type = "Restore";
            $log->save();

            $return["msg"] = trans('content.component_fields.component_has_restored_successfully');
            $return["status"] = "success";
        }
        return response()->json($return);
    }

    public function checkin(Request $request) {
        $return = array("status"=>"failure", "msg"=>trans('content.component_fields.Unable_to_checkout_component'));
        if(! Auth::user()->hasPermissionTo('ComponentCheckin') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only("id", "checked_in_at", "note");
        $rules = [
            'id' => 'required|integer|min:1|exists:components,id',
            'checked_in_at' => 'nullable|date_format:d/m/Y h:i A',
            'note' => 'nullable|clean_text_only|max:255'
        ];

        $validator = Validator::make($data, $rules, []);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        $component = Component::find($request->id);
        if(! $component) {
            return response()->json($return);
        }
        if( !in_array($component->company_id, $companyIds) ) {
            $return["status"] = 'error';
            $return["section"] = 'component-checkin';
            $return["msg"] = "You don't have access to this company.";
            return response()->json($return);
        }

        $user = $device = $target_assigned_name = "";
        $checked_in_from = $component->checked_out_for;

        if($checked_in_from == 1) {
            $user = User::find($component->checked_out_to);
            $target_assigned_name = !empty($user->displayName) ? $user->displayName  : trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));        
            
        }
        elseif($checked_in_from == 2) {
            $device = Device::find($component->checked_out_to);
            $target_assigned_name = $device->asset_tag;
            $user = User::find($device->assigned_to);
        }
        $log = new Actionlog();
        $log->asset_type = "component";
        $log->checkedout_to = $component->checked_out_to;
        $log->location_id = $checked_in_from == 3 ? $device->location_id : ($checked_in_from == 1 ? $user->location_id : null)  ;
        $log->assigned_for = $checked_in_from;
        $log->assigned_to_type = $checked_in_from;
        $log->asset_id = $component->id;
        $log->note = $request->note;
        $log->user_id = Auth::user()->id;
        $log->action_type = "checkin from";
        $log->save();

        $component->checked_in_at = $request->checked_in_at ? CommonHelper::getDateAs($request->checked_in_at, "Y-m-d H:i:s", "d/m/Y h:i A") : null;
        $component->checked_out_at = null;
        $component->checked_out_to = null;
        $component->expected_checkin_at = null;
        $component->status = 1;
        $component->updator_id = Auth::user()->id;
        $component->checked_out_for = 0;  
        try {
            $alertnotify = (Settings::first()->alerts_enabled == 1) ? CommonHelper::getGlobalAlertEmail() : [];
            $recipientEmail = (isset($user) && !empty($user->email)) ? $user->email : null;
            if( config('mail.service_enabled')  && $recipientEmail != null) {
                $checked_in_fromName=$checked_in_from == 1 ? "User" : ($checked_in_from == 2 ? "Device" : null);
                if(!empty($alertnotify) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($recipientEmail)->cc($alertnotify)->queue(new ComponentCheckinNotification($data["checked_in_at"], $target_assigned_name,  $component, $log, $checked_in_fromName, $user));
                    Mail::to($recipientEmail)->cc($alertnotify)->queue(new ComponentCheckinNotification($data["checked_in_at"], $target_assigned_name,  $component, $log, $checked_in_fromName, $user));
                }
                else {
                    Mail::to($recipientEmail)->queue(new ComponentCheckinNotification($data["checked_in_at"], $target_assigned_name,  $component, $log, $checked_in_fromName, $user));
                    Mail::to($recipientEmail)->queue(new ComponentCheckinNotification($data["checked_in_at"], $target_assigned_name,  $component, $log, $checked_in_fromName, $user));
                }
            }
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            Log::error("checkin component mailConfig", ['error' => $e->getMessage()]);
        }

        if($component->save()) {
            $return["status"] = "success";
            $return["msg"] = trans('content.component_fields.Component_checked_in_successfully');
            Log::info("checkin component id:" . $component->id." uid:" . Auth::user()->id . " : " . json_encode($request->all()));
        }

        return response()->json($return);
    }

    public function getComponentCheckinData(Request $request, $id){
        $return = array("status"=>"failure", "msg"=>trans('content.component_fields.Unable_to_checkout_component'));
        if(! Auth::user()->hasPermissionTo('ComponentCheckin')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $component = Component::find($id);
        if(! $component) {
            return response()->json($return);
        }
        $checked_out_for = $component->checked_out_for;
        $checked_out_to = $component->checked_out_to;
        
        $chckinfo = '';
        $checkout_details = [];
        if($checked_out_for == 1){
            $chckinfo=User::find($checked_out_to);
            if($chckinfo){
                $checkout_details=[
                    'type'=>'User',
                    'name' => !empty($chckinfo->displayName) ? $chckinfo->displayName : trim(($chckinfo->first_name ?? '') . ' ' . ($chckinfo->last_name ?? '')),
                    'email' => $chckinfo->email,
                    'id' => $chckinfo->id
                ];
            }
        } elseif($checked_out_for == 2){
            $chckinfo=Device::find($checked_out_to);
            if($chckinfo){
                $checkout_details=[
                    'type'=>'Device',
                    'name'=>"$chckinfo->asset_tag - $chckinfo->name",
                    'asset_tag'=>$chckinfo->asset_tag,
                    'id'=>$chckinfo->id   
                ];
            }
        }
        if($chckinfo) {
            $return["status"] = "success";
            $return["msg"] = "Component checkout details retrieved successfully";
            $return["component"] = ['checkout_details' => $checkout_details];
        } else {
            $return["msg"] = "Component checkout information not found";
        }
        return response()->json($return);
    }

    public function checkout(Request $request) {
        $return = array("status"=>"failure", "msg"=>trans('content.component_fields.Unable_to_checkout_component'));
        if(! Auth::user()->hasPermissionTo('ComponentCheckout') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only("id",'assigned_to','assigned_for','device_id', "checked_out_at", "expected_checkin_at","note");
        $rules = [
            "id" => [
                "required", 
                Rule::exists("components")->where(function($q) {
                    $q->whereNull("deleted_at");
                })
            ],
            "assigned_for" => "required|integer|min:1|max:2",
            "assigned_to" => [
                "nullable",
                "required_if:assigned_for,1",
                Rule::exists("users", "id"),
            ],
             "device_id" => [
                "nullable",
                "required_if:assigned_for,2",
                Rule::exists("assets", "id")->where(function($q) {
                    $q->whereNull("deleted_at");
                })
            ],
            'checked_out_at' => 'nullable|date_format:d/m/Y h:i A',
            'expected_checkin_at' => 'nullable|date_format:d/m/Y h:i A',
            'note' => 'nullable|clean_text_only|max:255'
        ];

        $validator = Validator::make($data, $rules, []);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        $component = Component::find($request->id);
        if(! $component) {
            return response()->json($return);
        }
        if( !in_array($component->company_id, $companyIds) ) {
            $return["status"] = 'error';
            $return["section"] = 'component-checkout';
            $return["msg"] = "You don't have access to this company.";
            return response()->json($return);
        }

        $checked_out_at = CommonHelper::getDateAs($request->checked_out_at, "Y-m-d H:i:s", "d/m/Y h:i A");
        if(! $checked_out_at) {
            $checked_out_at = (new Carbon(config('app.timezone')))->format('Y-m-d H:i:s');
        }

        $expected_checkin_at = isset($request->expected_checkin_at) ? CommonHelper::getDateAs($request->expected_checkin_at, "Y-m-d H:i:s", "d/m/Y h:i A") : null;

        $target_assigned_name = $target_chkout_to='';
        $target_assigned = $target_location_id = 0;
        $target_checkout_to = $request->assigned_for;
        if ($target_checkout_to == 2){
            $device = Device::find($request->device_id);
            $target_assigned =$request->device_id;
            $target_assigned_name=$device->asset_tag;
            $target_chkout_to='Device';

            if($device->status->sold == 1 ){
                $return["msg"] = trans('content.component_fields.chosen_device_is_in_sold_status');
                return response()->json($return);
            }
            if($device->status->stolen_item == 1){
                $return["msg"] = trans('content.component_fields.chosen_device_is_in_lost');
                return response()->json($return);
            }


            $component->checked_out_to = $request->device_id;
            $component->checked_out_for = $target_checkout_to;
            $target_location_id = $device->rtd_location_id;
            $user = User::find($device->assigned_to);
            
        } else if($target_checkout_to == 1){
            $user = User::find($request->assigned_to);
            $target_chkout_to ='User';
            $target_assigned_name = !empty($user->displayName) ? $user->displayName  : trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));               
            if(empty($user)){
                $return["msg"] = 'Chosen User is not found';
                return response()->json($return);
            }
            
            if($user->checkoutBasicClearance()){
                $return["msg"] = trans('content.accessory_fields.Chosen_User_is_not_in_Active_Status');
                return response()->json($return);
            }

            if($user->checkLastWorkingDate()){
                $return["msg"] = trans('content.accessory_fields.This_users_last_working_date');
                return response()->json($return);
            }
           
            $component->checked_out_to = $request->assigned_to;
            $target_assigned = $request->assigned_to;
            $target_location_id = $user->location_id;
            $component->checked_out_for = $target_checkout_to;
        }
        $component->checked_out_at = $checked_out_at;
        $component->checked_in_at = null;
        $component->status = 4;
        $component->expected_checkin_at = $request->expected_checkin_at ? CommonHelper::getDateAs($request->expected_checkin_at, "Y-m-d H:i:s", "d/m/Y h:i A") : null;
        $component->updator_id = Auth::user()->id;
        
        $log = new Actionlog();
        $log->asset_type = "component";
        $log->checkedout_to = $target_assigned;
        $log->location_id = $target_location_id;
        $log->assigned_for = $target_checkout_to;
        $log->assigned_to_type = $target_checkout_to;
        $log->asset_id = $component->id;
        $log->expected_checkin = $expected_checkin_at;
        $log->note = $request->note;
        $log->user_id = Auth::user()->id;
        $log->action_type = "checkout";
        $log->save();

        try {
            $alertnotify = (Settings::first()->alerts_enabled == 1) ? CommonHelper::getGlobalAlertEmail() : [];
            $eula = $component->getEula();
            $recipientEmail = (isset($user) && !empty($user->email)) ? $user->email : null;
            if( config('mail.service_enabled') && $eula && $recipientEmail != null) {
                if(!empty($alertnotify) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {    
                    Mail::to($recipientEmail)->cc($alertnotify)->send(new ComponentCheckoutNotification($component, $user, $log, $eula, $target_chkout_to, $target_assigned_name));
                }
                else {
                    Mail::to($recipientEmail)->send(new ComponentCheckoutNotification($component, $user, $log, $eula, $target_chkout_to, $target_assigned_name));
                }
            }
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
        }

        if($component->save()) {
            if (!empty($component->category_id)) {
                $this->notifyCategoryThresould($component->category_id);
            }
            $return["status"] = "success";
            $return["msg"] = trans('content.component_fields.Component_checked_out_successfully');
            Log::info("checkout component id:" . $component->id." uid:" . Auth::user()->id . " : " . json_encode($request->all()));
        }

        return response()->json($return);
    }

    public function editComponent(Request $request, $id) {
        $appSettings = Settings::first();
        $return = array("status"=>"failure", "msg"=>trans('content.component_fields.Unable_to_edit_component'));
        if(! Auth::user()->hasPermissionTo('ComponentEdit') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $component = Component::find($id);

        if(! $component) {
            return response()->json($return);
        }

        $data = $request->only("name", "category_id", "company_id", "location_id", "unique_tag", "status", "serial", "origin_from", "parent_device", "invoice_id", "purchase_date", "supplier_id", "order_number", "purchase_currency", "purchase_cost", "notes","component_custom_fields","department_id","requestable_component","internal_place_id");

        // $companyId = CommonHelper::getAccessibleCompanyIds();
        $data['company_id'] =  ($component->status != 4) ? $data['company_id'] : $component->company_id;
        // if (!in_array($data['company_id'], $companyId)) {
        //     $msg = "You don't have access to this company.";
        //     return response()->json(["msg" => $msg]);
        // }
        
        $rules = [
            'unique_tag' => ['nullable', 'clean_text_only', 'max:100', Rule::unique("components")->ignore($id)],
            'category_id'          => 'required|integer|min:1|exists:categories,id',
            'status'         => 'required|integer|min:0|max:4',
            'origin_from'         => 'nullable|integer|min:1|max:4',
            'company_id'        => 'required|integer|min:1|exists:companies,id',
            'location_id'   => 'required|integer|min:1|exists:locations,id',
            'invoice_id'       => 'nullable|integer|min:1|exists:purchases,id',
            'supplier_id'       => 'nullable|integer|min:1|exists:suppliers,id',
            'parent_device'        => 'nullable|integer|min:1|exists:assets,id',
            'internal_place_id'   => 'nullable|integer|min:1|exists:places,id',
            'name'              => 'nullable|clean_text_only|max:100',
            'serial'            => 'required|clean_text_only|max:255',
            'purchase_date'     => 'nullable|date_format:d/m/Y',
            'order_number'      => 'nullable|clean_text_only|max:100',
            'purchase_cost'     => 'nullable|numeric',
            'purchase_currency' => 'nullable|clean_text_only|max:3',
            'notes'             => 'nullable|clean_text_only|max:2000',
            'department_id' => 'nullable|integer|exists:departments,id',
        ];

        $messages = [ 
            'category_id.required' => 'Please select Category',
            'company_id.exists' => 'Please select Company'
        ];

        $data_fields = $request->input("fields", null);
        $custom_fields = $custom_fields_val= [];
        $component_data = Category::find($data['category_id']);
        if($component_data && $component_data->customFieldset && count($component_data->customFieldset->fields)) {
            foreach($component_data->customFieldset->fields as $f){
                $col_name = $f->nameToColumn();
                $formatType = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[] = $col_name;
                $custom_fields_val[$f->nameToColumn()] = $data[$col_name];

            }
        }
        if(Settings::first()->component_custom_fieldset_id != "") {
            $customFieldset = CustomFieldset::where('id', Settings::first()->component_custom_fieldset_id)->first();
            foreach($customFieldset->fields as $f) {
                $col_name = $f->nameToColumn();
                $formatType = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : Null;
                $custom_fields[] = $col_name;
                $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
            }
        }
        if ($component->status == 4) {
            unset($rules['status']);
            unset($data['status']);
        }
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        if($data["origin_from"] || $request->origin_from == 1 ) {
            $data["parent_device"] = null;
            Component::where('id', $id)->update([
                'parent_device' => null,
            ]);
        }
        elseif($data["origin_from"] || $request->origin_from == 2) {
            $data["invoice_id"] = null;
            Component::where('id', $id)->update([
                'purchase_date' => null,
                'invoice_id' => null,
            ]);
        }
        elseif(($data["origin_from"] || $request->origin_from == 3) || (($data["origin_from"] || $request->origin_from == 4))) {
            Component::where('id', $id)->update([
                'purchase_date' => "",
                'invoice_id' => "",
                'parent_device' => "",
            ]);
        }
        else {
            $data["parent_device"] = null;
            $data["invoice_id"] = null;
            Component::where('id', $id)->update([
                'purchase_date' => null,
                'invoice_id' => null,
                'parent_device' => null,
            ]);
        }

        $data["component_custom_fields"] = null;
        // $data_custom = json_decode($data["component_custom_fields"], true);
        // $var = json_decode($component->component_custom_fields);
        // $data["department_id"] = isset($data["department_id"]) ? $data["department_id"] : null;
        // if(!empty($var)) {
        //     foreach($data_custom as $key=>$d) {
        //         $var->$key = $d;
        //     }
        //     $data["component_custom_fields"] = json_encode($var);
        // } else {
        //     $data["component_custom_fields"] = json_encode($custom_fields_val);
        // }
        $component->fill($data);
        $component->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d/m/Y") : null;
        $component->purchase_cost = (float) $request->purchase_cost;
        $component->updator_id = Auth::user()->id;
        $component->invoice_id = $request->invoice_id;
        $component->department_id = $request->department_id ?? null;
        $component->requestable_component = isset($request->requestable_component) ? $request->requestable_component : 0;

        if (count($custom_fields_val)) {
            foreach ($custom_fields_val as $key => $f) {
                 $component->$key  = isset($f) ? $f : null;
            }
        }
        $imagedata = Component::find($id)->image;
        if ( $request->input("delete_img", false) ||  $request->image != null) {
            if($imagedata != null) {
                $exits1=File::exists(public_path('/uploads/component/' . $imagedata));
                    if( $exits1 ) {
                        File::delete(public_path('/uploads/component/' . $imagedata));
                        Component::where('id', $id)->update(['image' => null]);
                    }
            }
        }
        if($request->image) {
            $uploaded_img = $request->image;
            $deviceImage = str_random(12) . str_random(12) . '.' . $uploaded_img->getClientOriginalExtension();
            $path = public_path("uploads/component/" . $deviceImage);
            Image::make($uploaded_img->getRealPath())->resize(300, null, function($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($path);
            $component->image  = $deviceImage;
        }

        if($component->save()) {
            $log = new Actionlog();
            $log->asset_type = "component";
            $log->location_id = $component->location_id;
            $log->asset_id = $component->id;
            $log->note = $component->note;
            $log->user_id = Auth::user()->id;
            $log->action_type = "Changes";
            $log->save();
            if ($data["unique_tag"] == "" || !$data["unique_tag"]) {
                $component->generateUniqueTag();
            }
            $return["component_id"] = $component->id;
            $return["status"] = "success";
            $return["msg"] = trans('content.component_fields.Component_has_updated_successfully');
            Log::info("editComponent id:" . $component->id." uid:" . Auth::user()->id . " : " . json_encode($request->all()));
        }

        return response()->json($return);
    }

    public function addComponent(Request $request) {
        $return = ['status'=>'fail', 'msg'=>trans('content.licenses_fields.Unable_to_get_the_record')];
        if(! Auth::user()->hasPermissionTo('ComponentAdd') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $appSettings = Settings::first();
        $return = array(
            "status" => "danger",
            "msg" => trans('content.component_fields.Unable_to_add_given_component')
        );

        $data = $request->only("name", "category_id", "company_id", "location_id", "status", "unique_tag", "serial", "checked_out_to", "checked_out_to_user","assigned_for","origin_from", "parent_device", "invoice_id", "purchase_date", "supplier_id", "order_number", "purchase_currency", "purchase_cost", "notes","component_custom_fields","department_id","image","requestable_component","internal_place_id");
        // $companyId = CommonHelper::getAccessibleCompanyIds();
        // if (!in_array($data['company_id'], $companyId)) {
        //     $msg = "You don't have access to this company.";
        //     return response()->json(["msg" => $msg]);
        // }
        
        $rules = [
            'unique_tag'         => 'nullable|clean_text_only|max:100|unique:components,unique_tag',
            'category_id'          => 'required|integer|min:1|exists:categories,id',
            'status'         => 'required|integer|min:0|max:4',
            'origin_from'         => 'nullable|integer|min:1|max:4',
            'company_id'        => 'required|integer|min:1|exists:companies,id',
            'location_id'   => 'required|integer|min:1|exists:locations,id',
            'invoice_id'       => 'nullable|integer|min:1|exists:purchases,id',
            'supplier_id'       => 'nullable|integer|min:1|exists:suppliers,id',
            'parent_device'        => 'nullable|integer|min:1|exists:assets,id',
            'name'              => 'nullable|clean_text_only|max:100',
            'serial'            => 'required|clean_text_only|max:255',
            'purchase_date'     => 'nullable|date_format:d/m/Y',
            'order_number'      => 'nullable|clean_text_only|max:100',
            'purchase_cost'     => 'nullable|numeric',
            'purchase_currency' => 'nullable|clean_text_only|max:3',
            'notes'             => 'nullable|clean_text_only|max:2000',
            'checked_out_to'    => 'nullable|integer|min:1|exists:assets,id',
            'checked_out_to_user'    => 'nullable|integer|min:1|exists:users,id',
            'department_id'     => 'nullable|integer|exists:departments,id',
            'image'             => 'sometimes|mimes:jpeg,jpg,png',
            'internal_place_id'   => 'nullable|integer|min:1|exists:places,id',
        ];
        $data_fields = $request->input("fields", null);
        $custom_fields = $custom_fields_val= [];
        $component = Category::find($data['category_id']);
        if($component && $component->customFieldset && count($component->customFieldset->fields)) {
            foreach($component->customFieldset->fields as $f){
                $col_name = $f->nameToColumn();
                $formatType = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[] = $col_name;
                $custom_fields_val[$f->nameToColumn()] = $data[$col_name];

            }
        }
        $settings = Settings::first();
        if (!empty($settings) && $settings->component_custom_fieldset_id != null) {
            $customFieldset = CustomFieldset::where('id', $settings->component_custom_fieldset_id)->first();
            if ($customFieldset) {
                foreach ($customFieldset->fields as $f) {
                    $col_name = $f->nameToColumn();
                    $formatType = CommonHelper::convertFormatToRegex($f->format);
                    $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                    // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                    $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                    $custom_fields[] = $col_name;
                    $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
                }
            } else {
                \Log::warning("CustomFieldset not found for ID: " . $settings->component_custom_fieldset_id);
            }
        } else {
            \Log::warning("Settings not found or component_custom_fieldset_id is empty.");
        }

        $messages = [ 
            'category_id.required' => 'Please select Category',
            'company_id.exists' => 'Please select Company'
        ];

        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        if($data["origin_from"] == 1) {
            $data["parent_device"] = null;
        }
        elseif($data["origin_from"] == 2) {
            $data["invoice_id"] = null;
        }
        else {
            $data["parent_device"] = null;
            $data["invoice_id"] = null;
        }

        $data["component_custom_fields"] = null;
        $component = new Component;
        $component->fill($data);
        if (count($custom_fields_val)) {
            foreach ($custom_fields_val as $key => $f) {
                 $component->$key  = isset($f) ? $f : null;
            }
        }
        if ($data['status'] == "4") {
            // checkout data
            $target_assigned_name = $target_chkout_to = '';
            $target_assigned = $target_location_id = 0;
            $target_checkout_to = $data['assigned_for'];
            $checked_out_at = (new Carbon(config('app.timezone')))->format('Y-m-d H:i:s');
            if ($target_checkout_to == 2) {
                if (isset($data["checked_out_to"])) {
                    $device = Device::find($data["checked_out_to"]);
                    $target_assigned = $data["checked_out_to"];
                    $target_assigned_name = $device->asset_tag;
                    $target_chkout_to = 'Device';
                    if($device->status->sold == 1 ) {
                        $return["msg"] = trans('content.component_fields.chosen_device_is_in_sold_status');
                        return response()->json($return);
                    }
                    if($device->status->stolen_item == 1) {
                        $return["msg"] = trans('content.component_fields.chosen_device_is_in_lost');
                        return response()->json($return);
                    }
                    $component->checked_out_to = $data['checked_out_to'];
                    $component->checked_out_for = $target_checkout_to;
                    $target_location_id = $device->rtd_location_id;
                    $user = User::find($device->assigned_to);
                }
            } else if ($target_checkout_to == 1) {
                $user = User::find($data['checked_out_to_user']);
                $target_chkout_to = 'User';
                $target_assigned_name = $user->getGuranteedNameText();
                if(empty($user)) {
                    $return["msg"] = 'Chosen User is not found';
                    return response()->json($return);
                }

                if($user->checkoutBasicClearance()) {
                    $return["msg"] = trans('content.accessory_fields.Chosen_User_is_not_in_Active_Status');
                    return response()->json($return);
                }

                if($user->checkLastWorkingDate()) {
                    $return["msg"] = trans('content.accessory_fields.This_users_last_working_date');
                    return response()->json($return);
                }

                $target_location_id = $user->location_id;
                $target_assigned = $data['checked_out_to_user'];
                $component->checked_out_to = $data['checked_out_to_user'];
                $component->checked_out_for = $data['assigned_for'];
            }
            $component->checked_out_at = $checked_out_at;
        }
        // checkout data end
        $component->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d/m/Y") : null;
        $component->purchase_cost = (float) $request->purchase_cost;
        $component->creator_id = $component->updator_id = Auth::user()->id;
        $component->requestable_component = isset($request->requestable_component) ? $request->requestable_component : 0;
        $component->updator_id = Auth::user()->id;
        $component->image = null;
        if($request->image) {
            $uploaded_img = $request->image;
            $componentImage = str_random(12) . str_random(12) . '.' . $uploaded_img->getClientOriginalExtension();
            $path = public_path("uploads/component/" . $componentImage);
            Image::make($uploaded_img->getRealPath())->resize(300, null, function($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($path);
            $component->image  = $componentImage;
        } elseif($request->input("forAction") == "clone" && $request->input("clone_img") != "" && !$request->input("delete_img", 0) ) {
            $old_path = public_path('/uploads/component/' . $request->input("clone_img"));
            if( file_exists($old_path) ) {
                $cloneImgArr = explode(".", $request->input("clone_img"));
                $componentImage = str_random(12) . str_random(12) . "." . last($cloneImgArr);
                $path = public_path("uploads/component/" . $componentImage);
                Image::make($old_path)->resize(300, null, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $component->image  = $componentImage;
            }
        }

        $component->company_id = empty($data['company_id']) ? Auth::user()->company_id : $data['company_id'] ;

        if($component->save()) {
            if (isset($data["checked_out_to"]) || isset($data["checked_out_to_user"])) {
                $log = new Actionlog();
                $log->asset_type = "component";
                $log->checkedout_to = $target_assigned;
                $log->location_id = $target_location_id;
                $log->assigned_for = $target_checkout_to;
                $log->assigned_to_type = $target_checkout_to;
                $log->asset_id = $component->id;
                $log->note = $component->note;
                $log->user_id = Auth::user()->id;
                $log->action_type = "checkout";
                $log->save();
                try {
                    $alertnotify = (Settings::first()->alerts_enabled == 1) ? CommonHelper::getGlobalAlertEmail() : [];
                    $eula = $component->getEula();
                    $recipientEmail = isset($user) ? $user->email : null;

                    if( config('mail.service_enabled') && $eula && $recipientEmail != null) {
                        if(!empty($alertnotify) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($recipientEmail)->cc($alertnotify)->send(new ComponentCheckoutNotification($component, $user, $log, $eula, $target_chkout_to, $target_assigned_name));
                        }
                        else {
                            Mail::to($recipientEmail)->send(new ComponentCheckoutNotification($component, $user, $log, $eula, $target_chkout_to, $target_assigned_name));
                        }
                    }
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            } else {
                $log = new Actionlog();
                $log->asset_type = "component";
                $log->location_id = $component->location_id;
                $log->asset_id = $component->id;
                $log->note = $component->note;
                $log->user_id = Auth::user()->id;
                $log->action_type = "New Add";
                $log->save();
            }

            if ($data["unique_tag"] == "" || !$data["unique_tag"]) {
                $component->generateUniqueTag();
            }
            $return["component_id"] = $component->id;
            $return["status"] = "success";
            $return["msg"] = trans('content.component_fields.New_Component_added_successfully');
            Log::info("addComponent id:" . $component->id." uid:" . Auth::user()->id . " : " . json_encode($request->all()));
        }
        
        return response()->json($return);
    }
   
    public function viewInfo(Request $request, $id) {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('ComponentView') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $component = Component::where("id", "=", $id)->first();
        if(!$component) {
            return redirect("components")->with("status", "component not found");
        }  
        $isCheckinCall = ($request->checkin == true && $component->checked_out_to);      
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($component->company_id, $companyIds)) {
            $return["msg"] = "You don't have access to this company.";
            return redirect('dashboard')->with("msg", $return);
        }
        $companies = Company::select("id", "name as text")->whereNull('deleted_at')->get()->toArray();
        $department = Department::select("id", "name as text")->whereIn('id',$companyIds)->whereNull('deleted_at')->get()->toArray();
        $categories = Category::select("id", "name as text")->where("category_type","like","component")->get()->toArray();
        $currencies = Currency::getCurrencies();
        $companyFieldset = CustomFieldset::where('id', Settings::first()->component_custom_fieldset_id)->first();
        $requestable_enabled = config('app.requestable_enabled');
        return view("components.info")->with(compact("component","companies","categories","currencies","isCheckinCall","companyFieldset","requestable_enabled","department"));
       
    }

    public function viewInfoTab(Request $request, $id) {
        $component = Component::where("id", "=", $id)->first();
        if($component->exists) {
                $chckinfo = '';
                $checkout_details = [];
                $checked_out_for = $component->checked_out_for;
                $checked_out_to = $component->checked_out_to;  
                if($checked_out_for == 1){
                    $chckinfo = User::find($checked_out_to);
                    if($chckinfo){
                        $checkout_details = [
                            'type'=>'User',
                            'name' => !empty($chckinfo->displayName) ? $chckinfo->displayName : trim(($chckinfo->first_name ?? '') . ' ' . ($chckinfo->last_name ?? '')),
                            'email' => $chckinfo->email,
                            'id' => $chckinfo->id
                        ];
                    }
                } elseif($checked_out_for == 2){
                    $chckinfo = Device::find($checked_out_to);
                    if($chckinfo){
                        $checkout_details = [
                            'type'=>'Device',
                            'name'=>$chckinfo->asset_tag. (!empty($chckinfo->name) ? ' - ' . $chckinfo->name : ''),
                            'asset_tag'=>$chckinfo->asset_tag,
                            'id'=>$chckinfo->id   
                        ];
                    }
                }
                $checkout_details = (object) $checkout_details;
                if ($component->image && file_exists(public_path('uploads/component/' . $component->image))) {
                        $path = asset('uploads/component/' . $component->image);
                    } else {
                        $path = null;
                    }
            return view("components.info_tab",compact('path'))->with(compact("component","checkout_details"));
        }
    }

    public function ajaxUsers(Request $request) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );
        
        $fields = array(
            '0' => 'l.created_at',
            '0' => 'l.created_at',
            '1' => 'adm_full_name',
            '2' => 'target_name',
            '3' => 'dev_user_name',
            '4' => 'checked_in_at',
            '5' => 'note',
            '4' => 'checked_in_at',
            '5' => 'note',
        );
    
        $db = DB::table('asset_logs as l')
            ->leftJoin('assets as dev', 'dev.id', '=', 'l.checkedout_to')
            ->leftJoin('users as u', 'u.id', '=', 'l.checkedout_to')
            ->leftJoin('users as dev_user', 'dev.assigned_to', '=', 'dev_user.id') 
            ->leftJoin('components as cm', 'cm.id', '=', 'l.asset_id')
            ->leftJoin('users as adm', 'adm.id', '=', 'l.user_id');
            
        $db->select(DB::raw('l.note as note'))
            ->addSelect(DB::raw('dev.asset_tag as chkout_dev_tag, dev.user_id as assigned_to'))
            ->addSelect(DB::raw('DATE_FORMAT(l.created_at, "%d %b %Y %h:%i %p") as created_at'))
            ->addSelect(DB::raw('case when l.expected_checkin then DATE_FORMAT(l.expected_checkin, "%d %b %Y %h:%i %p") end as checked_in_at'))
            ->addSelect(DB::raw('coalesce(adm.displayName, concat(adm.first_name, " ", adm.last_name)) as adm_full_name'))
            ->addSelect(DB::raw('case when l.assigned_for = 1 then "User"  when l.assigned_for = 2 then "Device" end as dev_user_name'))
            ->addSelect(DB::raw('case when l.assigned_for = 1 then concat_ws(" ", coalesce(u.displayName, concat(u.first_name, " ", u.last_name)), "-", u.username) when l.assigned_for = 2 then dev.asset_tag end as target_name'));

        $db->where("l.asset_id", "=", $req["component_id"]);
        $db->where('l.asset_type', '=', 'component');   
        $db->where('l.action_type', '=', 'checkout');
        
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];
        
        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
            $whereStr = sprintf('(l.note like "%%%1$s%%" or dev.asset_tag like "%%%1$s%%" or DATE_FORMAT(l.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when l.expected_checkin then DATE_FORMAT(l.expected_checkin, "%%d %%b %%Y %%h:%%i %%p") end) like "%%%1$s%%" or concat(adm.first_name, " ", adm.last_name) like "%%%1$s%%" or concat(dev_user.first_name, " ", dev_user.last_name) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }
        
        if( isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
            $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
        }
        
        $skip = 0;
        $take = 10;
        if( isset($req["start"]) && isset($req["length"]) ) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }
        $db->skip($skip);
        $db->take($take);
        
        $data = $db->get();
        $return['data'] = array();
        foreach($data as $d) {
            $return['data'][] = array('a' => $d);
        }
        
        return response()->json($return);
        
    }

    public function ajaxHistory(Request $request) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            '0' => 'adm_full_name',
            '1' => 'c.action_type',
            '2' => 'chkout_dev_tag',
            '3' => 'target_name',
            '4' => 'c.note',
            '5' => 'c.created_at',

        );

        $db = DB::table('asset_logs as c');
        $db->leftJoin('assets as chkout_dev', 'chkout_dev.id', '=', 'c.checkedout_to');
        $db->leftJoin('users as adm', 'adm.id', '=', 'c.user_id');
        $db->leftJoin('users as u', 'u.id', '=', 'c.checkedout_to');


        $db->select('c.id', 'c.action_type as action_type', 'c.note as note');
        $db->addSelect(DB::raw('chkout_dev.asset_tag as chkout_dev_tag'));
        $db->addSelect(DB::raw('DATE_FORMAT(c.created_at, "%d %b %Y %h:%i %p") as created_at'));
        $db->addSelect(DB::raw('concat(adm.first_name, " ", adm.last_name) as adm_full_name'));
        $db->addSelect(DB::raw('case when c.assigned_for = 1 then concat_ws(" ", u.first_name, u.last_name, "-", u.username) when c.assigned_for = 2 then chkout_dev.asset_tag end as target_name'));
        $db->addSelect(DB::raw('case when c.assigned_for = 1 then "User"  when c.assigned_for = 2 then "Device" end as target_type'));

        $db->where("c.asset_id", "=", $req["component_id"]);
        $db->where('c.asset_type', '=', 'component');

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $whereStr = sprintf('(chkout_dev.asset_tag like "%%%1$s%%" or c.note like "%%%1$s%%" or c.action_type like "%%%1$s%%" or concat(adm.first_name, " ", adm.last_name) like "%%%1$s%%" or DATE_FORMAT(c.created_at, "%%d %%b %%Y") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req["order"][0]["dir"]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
            $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int)$req["start"];
            $take = (int)$req["length"];
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

    public function componentImport(Request $request) {
        $return = ["msg"=>"Unable to import the given component", "status"=>"danger"];
        if(!config("services.assets.enabled")) {
            $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
            return redirect('dashboard')->with("msg", $return);
        }
        $settings = Settings::first();
        if( $request->isMethod('post') && !$request->import_file ) {
            $return["msg"] = trans('content.download_format.error_place_upload');
            $request->session()->flash("msg", $return);
        } else {
            if(! $request->isMethod('post')) {
                return view("components.import_blade")->with("settings", $settings);
            }
            if ( $request->isMethod('post') && $request->import_file ) {
                $path = $request->file('import_file')->getRealPath();
                $import = new ComponentImport($request);
                Excel::import($import, $request->import_file);
                $response = $import->data;
                return view("components.import_blade")->with("settings", $settings)->with(['success' => $response['success'], 'fail' => $response['fail'], 'fail_msgs' => $response['fail_msgs'] ]);
            }
        }
        return view("components.import_blade")->with("settings", $settings);
    }

    public function downloadCustomFieldsCodeComponents(Request $request) {

        $customFieldset = CustomFieldset::where('id', Settings::first()->component_custom_fieldset_id)->first();
        $fields = [];
        if(!empty($customFieldset)) {
            if(count($customFieldset->fields) > 0) {
                foreach ($customFieldset->fields as $f) {
                    $col_name = $f->name;
                    $fields[] = CustomField::where('name', $f->name)->first();
                }
            }
        }

        $result = json_decode(json_encode($fields, true),true);
        return Excel::download(new CustomFieldsAccessories($result), 'ComponentsCustomFields.xlsx');
    }

    public function notifyCategoryThresould($category_id){
        $alertnotify = [];
        if(Settings::first()->alerts_enabled == 1){
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        $alertmail = null;
        $ThresholdSettings = ThresholdSettings::first();
        $thresouldDtl = Threshold::where('cat_id','=',$category_id)->first();

        $total_consumable = Component::getCatComponentTotal($category_id)[0]->total_component;
        // $availableConsumables = Component::getCheckoutComponentTotalByCat($category_id)[0]->total_checkouts;
        $availableConsumables = Component::getAvailableComponentTotalByCat($category_id)[0]->total_available;
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
            if ($availableConsumables <= $thresouldDtl->threshold ){
                if (config('mail.service_enabled')) {
                    Mail::to($alertmail)->cc($alertnotify)->send(new ThreshouldNotification ($catDetail,$thresouldDtl->threshold,$availableConsumables) );
                    $thresouldDtl->notify_count = $thresouldDtl->notify_count + 1 ;
                    $thresouldDtl->last_notified_date = date('Y-m-d');
                }
                $thresouldDtl->save();
            }
        }
    }
    public function ajaxRequestableComponent(Request $request) {
        $return = ["total"=>0,"filtered"=>0,"data"=>[]];
        $req = $request->all();

        $fields = array(
            '1' => 'a.unique_tag',
            '2' => 'a.name',
            '3' => 'cat.name', 
            '4' => 'loc.name',
            '5' => 'status_name',
        );
        
        $db = DB::table('components as a');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('assets as chkout_dev', 'chkout_dev.id', '=', 'a.checked_out_to');
        $db->leftJoin('asset_logs as al', function($q) {
            $q->on('al.asset_id', '=', 'a.id');
            $q->where('al.action_type', 'like', 'requested');
            $q->where('al.asset_type', '=', 'component');
            $q->where('al.user_id', '=', Auth::user()->id);
            $q->whereNull('al.accepted_id');
        });
        $db->where("a.requestable_component", 1);
        $db->where("a.status", 1);
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);

        $db->select('a.id', 'a.notes','al.id as requested_id', 'loc.name as loc_name', 'cat.name as cat_name', 'a.name', 'a.serial', 'a.unique_tag', 'a.image');
        $db->addSelect(DB::raw('chkout_dev.asset_tag as chkout_dev_tag'));
        $db->addSelect(DB::raw("case when a.checked_out_to is not null and a.checked_out_to > 0 then 2 when a.status = 4 then 2 when a.status = 1 then 1 else 0 end as check_action"));
        $db->addSelect(DB::raw('case when a.status = 1 then "Usable" when a.status = 2 then "Lost" when a.status = 0 then "Repair" when a.status = 3 then "Scrap" when a.status = 4 then "Deployed" end as status_name'));
        $db->addSelect(DB::raw('case when a.origin_from = 1 then "Purchase" when a.origin_from = 2 then "Existing Device" when a.origin_from = 3 then "Others" when a.origin_from = 4 then "Via Network" end as origin_from_name'));
        $db->whereNull('a.deleted_at');
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

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];
         $is_searching = false;
        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
            $whereStr = sprintf('(a.unique_tag like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or (case when a.status = 1 then "Usable" when a.status = 2 then "Lost" when a.status = 0 then "Repair" when a.status = 3 then "Scrap" when a.status = 4 then "Deployed" end) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }
        
        if($is_searching) {
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order"]["id"]) && isset($fields[$req["order"]["id"]]) && in_array($req["order"]["dir"], [1,2]) ) {
            $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
            $db->orderBy($fields[$req["order"]["id"]], $dir);
        }

        $page = $request->input("page", 1);
        $take = $request->input("size", 10);
        $skip = ($page * $take) - $take;
        if($return["filtered"] < $skip) {
            $page = 1;
            $skip = 0;
        }

        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return["page"] = $page;
        foreach ($data as $key => $d) {
        
            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }
    public function requestComponent(Request $request,$id){
        $return         = [];
        $appSettings    = Settings::first();
        $objComponent      = Component::where('id', '=', $id)->where('requestable_component', '=', 1)->whereNull('deleted_at')->first();
        $user           = Auth::user();

        if (! $objComponent) {
            return response()->json(['status' => 'error', 'msg' => 'Device not found !!']);
        }

        $logaction = new Actionlog();
        $logaction->asset_id  = $objComponent->id;
        $logaction->asset_type = 'component';
        $logaction->created_at = $logaction->requested_at = date("Y-m-d h:i:s");
        $logaction->location_id = $user->location_id ? $user->location_id : null;
        $logaction->user_id = $user->id;
        $logaction->action_type = 'requested';
        $logaction->save();

         $settings = Settings::getSettings();
        return response()->json(['status' => 'success', 'section'=>'approve-request', 'msg' =>'Component requested successfully']);
    }
    public function getComponentForDropDown(Request $request) {
        $return = array();
        try {
            $search = $request->input("search", "");
            $page = $request->input("page", 1);
            $skip = (($page * 10) - 10);

            $db = DB::table("components as a");
            if (config("app.client") == "etherealmachines") {
                $db->select("a.id",DB::raw("concat_ws('-', concat('CM', a.id), a.name) as text"));
            } else {
                $db->select("a.id",DB::raw("concat_ws('-', concat('CMP', a.id), a.name) as text"));
            }
            $db->whereNull('a.deleted_at');
            if($search) {
                $db->whereRaw("a.name like '%" . $search . "%')");
            }
            $count = $db->count();
            $db->skip($skip)->take(10);
            $result = $db->get();
            $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
            $return["results"] = count($result) ? $result->toArray() : [];
        }
        catch(\Exception $e) {
            Log::error("getAccForDropDown: " . $e->getMessage());
        }
        return response()->json($return);
    }

    public function qrcode(Request $request, $id) {
        $size = CommonHelper::getBarcodeDimensions(Settings::getSettings()->barcode_type);
        $path = url("component/info/" . $id);
        $barcode = new \Com\Tecnick\Barcode\Barcode();
        $barcode_obj =  $barcode->getBarcodeObj(Settings::getSettings()->barcode_type, $path, $size['height'], $size['width'], 'black', array(-1, -1, -1, -1));
        return $barcode_obj->getPngData();
    }

    public function printBarcode(Request $request, $option, $ids, $user_info = 1)
    {
        $return = array("status" => "danger", "msg" => "Component not found");
        if( ! Auth::user()->hasPermissionTo('ComponentPrintLabel') ) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $settings = Settings::getSettings();
        $data_string = base64_decode($ids);
      
        // data collection to be passed to view portion
        $view_data = array(
            "qr_text" => $settings->qr_text,
            "components" => array(),
            "componentscount" => 0
        );

        // check the asset list found or not
        if(!$ids || !$data_string) {
            return redirect("components")->with("error", "Please select any component to print");
        }

        // check the barcode is enabled on settings
        if($settings->qr_code != '1') {
            return redirect("components")->with("error", "Barcode is disabled.");
        }

        $data = explode(",", $data_string);

        foreach($data as $k => $v) {
            $data[$k] = trim($v);
        }

        $view_data["components"] = Component::find($data);
        $view_data["componentscount"] = count($view_data["components"]);
        $view_data["option"] = $option;
        $view_data["user_info"] = $user_info;

        return view('components/print_barcode3')->with("data", $view_data);
    }

    public function printBarcodeOneCol(Request $request,$option, $ids, $user_info = 1) 
    {
        $return = array("status" => "danger", "msg" => "Component not found");
        if( ! Auth::user()->hasPermissionTo('ComponentPrintLabel') ) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $settings = Settings::getSettings();
        $data_string = base64_decode($ids);

        // data collection to be passed to view portion
        $view_data = array(
            "qr_text" => $settings->qr_text,
            "components" => array(),
            "componentscount" => 0
        );

        // check the asset list found or not
        if(!$ids || !$data_string) {
            return redirect("components")->with("error", "Please select any component to print");
        }

        // check the barcode is enabled on settings
        if($settings->qr_code != '1') {
            return redirect("components")->with("error", "Barcode is disabled.");
        }

        $data = explode(",", $data_string);

        foreach($data as $k => $v) {
            $data[$k] = trim($v);
        }

        $view_data["components"] = Component::find($data);
        $view_data["componentscount"] = count($view_data["components"]);
        $view_data["option"] = $option;
        $view_data["user_info"] = $user_info;

        return view('components/print_barcode_one_col')->with("data", $view_data);
    }

    public function printBarcodeTwoCol(Request $request,$option, $ids, $user_info = 1)
    {
        $return = array("status" => "danger", "msg" => "Component not found");
        if( ! Auth::user()->hasPermissionTo('ComponentPrintLabel') ) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $settings = Settings::getSettings();
        $data_string = base64_decode($ids);

        // data collection to be passed to view portion
        $view_data = array(
            "qr_text" => $settings->qr_text,
            "components" => array(),
            "componentscount" => 0
        );

        // check the asset list found or not
        if(!$ids || !$data_string) {
            return redirect("components")->with("error", "Please select any component to print");
        }

        // check the barcode is enabled on settings
        if($settings->qr_code != '1') {
            return redirect("components")->with("error", "Barcode is disabled.");
        }

        $data = explode(",", $data_string);

        foreach($data as $k => $v) {
            $data[$k] = trim($v);
        }

        $view_data["components"] = Component::find($data);
        $view_data["componentscount"] = count($view_data["components"]);
        $view_data["option"] = $option;
        $view_data["user_info"] = $user_info;

        return view('components/print_barcode2')->with("data", $view_data);
    }
    public function printVerticalCol(Request $request, $option, $ids, $user_info = 1)
    {
        $return = array("status" => "danger", "msg" => "Component not found");
        if( ! Auth::user()->hasPermissionTo('ComponentPrintLabel') ) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $settings = Settings::getSettings();
        $data_string = base64_decode($ids);
        $view_data = array(
            "qr_text" => $settings->qr_text,
            "components" => array(),
            "componentscount" => 0
        );

        if(!$ids || !$data_string) {
            return redirect("components")->with("error", "Please select any component to print");
        }

        if($settings->qr_code != '1') {
            return redirect("components")->with("error", "Barcode is disabled.");
        }

        $data = explode(",", $data_string);

        foreach($data as $k => $v) {
            $data[$k] = trim($v);
        }

        $view_data["components"] = Component::find($data);
        $view_data["componentscount"] = count($view_data["components"]);
        $view_data["option"] = $option;
        $view_data["user_info"] = $user_info;

        return view('components/print_vertical_barcode')->with("data", $view_data);
    }
}

<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Consumable;
use App\Models\Location;
use App\Models\Company;
use App\Models\Manufacture;
use App\Models\Department;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\Procurement\AccountType;
use Auth;
use DB;
use PDF;
use Log;
use Carbon\Carbon;
use App\Helpers\Common as CommonHelper;
use stdClass;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Reports\CategoryWise\CategoryWiseConsumables;
use App\Exports\Reports\DeletedReport\DeletedConsumablesReport;
use App\Exports\Reports\LocationReport\LocationWiseConsumablesReport;
use App\Exports\Reports\ConsumableReport;
use App\Exports\Reports\ConsumableActivityReport;
use App\Exports\Reports\DailyDistributionExport;
use App\Exports\Reports\IndividualMaterialExport;
use App\Exports\Reports\MonthWiseExport;
use App\Exports\Reports\ConsumablesPurchaseExpire;
use App\Exports\Reports\ThresholdLevel;
use App\Models\ConsumablePurchase;
use App\Models\ConsumableUser;
use DateTime;

class ConsumableController extends Controller {
   
    public function getConsumable(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('CategoryReport') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = new stdClass();
        $vd->location = Location::select('id', 'name as text')->orderBy('name')->get(); 
        $vd->category = Category::select('id', 'name as text')->orderBy('name')->get(); 
        $vd->company = Company::select('id', 'name as text')->orderBy('name')->get();
        $vd->account = AccountType::select('id', 'name as text')->orderBy('name')->get();  
        $vd->manufacturer = Manufacture::select('id', 'name as text')->orderBy('name')->get();
        $vd->departments = Department::select("id","name as text")->where('asset_department', 1)->orderBy("name")->get();
        $vd->suppliers = Supplier::select('id', 'name as text')->orderBy('name')->get();
        $sort_fields = [
            ["id"=>1,"text"=>"Batch No."],
            ["id"=>2,"text"=>"Unique Tag"],
            ["id"=>3,"text"=>"Consumable Name"],
            ["id"=>4,"text"=>"Category"],
            ["id"=>5,"text"=>"Account type"],
            ["id"=>6,"text"=>"Company"],
            ["id"=>7,"text"=>"Location"],
            ["id"=>8,"text"=>"Total"],
            ["id"=>9,"text"=>"Available"],
           
        ];
        return view("reports.categories.consumable")->with("sort_fields", $sort_fields)->with("vd", $vd);
    }

    public function downloadCategoryWiseConsumables(Request $request) {

        $return = ['status' => 'danger', 'msg' => 'Unable to export the Consumables'];
        if(! Auth::user()->hasPermissionTo('CategoryReportDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $db = DB::table('consumables as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(id) as tot_assigns FROM `consumables_users` group by consumable_id) au'), function($j) {
            $j->on('a.id', '=', 'au.consumable_id');
        });
        $db->leftJoin('procure_account_types as pr', 'cat.account_type_id', '=', 'pr.id');
        $db->where('cat.category_type', '=', 'consumable');

        $db->select('a.id', 'a.unique_tag as unique_tag','a.name as con_name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'cat.name as cat_name', 'a.qty as qty','cat.name as name','pr.name as account_type');
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","CN",a.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","CNS",a.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('case when au.tot_assigns is not null then (a.qty - au.tot_assigns) else a.qty end as remaining'));
        $db->wherenull('a.deleted_at');

        // check asset location permission
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

            if(isset($filters->search)) {
                $req["filters"] = (array) $filters->other_filters;
                $req["search"] = $filters->search;
            }

            if(isset($req["filters"])) {
                $filters = $req["filters"];

                if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                    $db->where("a.location_id", "=", (int) $filters['location']);
                }
                if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                    $db->where("a.category_id", "=", (int) $filters['category']);
                    $db->where('cat.category_type', '=', 'consumable');
                }
                if(isset($filters["account"]) && $filters['account'] && $filters['account'] != "null") {
                    $db->where("cat.account_type_id", "=", (int) $filters['account']);
                }
                if(isset($filters["company"]) && $filters['company'] && $filters['company'] != "null") {
                    $db->where("a.company_id", "=", (int) $filters['company']);
                }
                if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                    $db->where("a.manufacturer_id", "=", (int) $filters['manufacturer']);
                }
                if(isset($filters["departments"]) && $filters['departments'] && $filters['departments'] != "null") {
                    $db->where("a.department_id", "=", (int) $filters['departments']);
                }
                if(isset($filters["suppliers"]) && $filters['suppliers'] && $filters['suppliers'] != "null") {
                    $db->where("a.supplier_id", "=", (int) $filters['suppliers']);
                }
            }

            if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                if (config("app.client") == "etherealmachines") {
                    $whereStr = sprintf('((case when a.id is not null then concat_ws("","CN",a.id) else "" end) like "%%%1$s%%" or a.unique_tag like "%%%1$s%%"  or a.name like "%%%1$s%%" or pr.name like "%%%1$s%%" or a.unique_tag like "%%%1$s%%"  or cmp.name like "%%%1$s%%"  or loc.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or cat.name like "%%%1$s%%")', $search_key);
                } else {
                    $whereStr = sprintf('((case when a.id is not null then concat_ws("","CNS",a.id) else "" end) like "%%%1$s%%" or a.unique_tag like "%%%1$s%%"  or a.name like "%%%1$s%%" or pr.name like "%%%1$s%%" or a.unique_tag like "%%%1$s%%"  or cmp.name like "%%%1$s%%"  or loc.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or cat.name like "%%%1$s%%")', $search_key);
                }
                $db->whereRaw($whereStr);
            }
        }

        $records = $db->get();
        $result = json_decode(json_encode($records, true),true);
        return Excel::download(new CategoryWiseConsumables($result), 'Category_Wise_Consumables_Report.xlsx');
    }

    public function ajaxConsumableList(Request $request) {
        $return = ["total"=>0,"filtered"=>0,"data"=>[]];
        $req = $request->all();

        $fields = array(
            '1'  => 'con_name',
            '2'  => 'unique_tag',
            '3'  => 'name',
            '4'  => 'account_type',
            '5'  => 'cmp_name',
            '6'  => 'loc_name',
            '7'  => 'qty',
            '8'  => 'remaining',
        );
        
        $db = DB::table('consumables as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(id) as tot_assigns FROM `consumables_users` group by consumable_id) au'), function($j) {
            $j->on('a.id', '=', 'au.consumable_id');
        });
        $db->leftJoin('procure_account_types as pr', 'cat.account_type_id', '=', 'pr.id');
        $db->where('cat.category_type', '=', 'consumable');

        $db->select('a.id', 'a.unique_tag as unique_tag','a.name as con_name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'cat.name as cat_name', 'a.qty as qty','cat.name as name','pr.name as account_type');
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","CN",a.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","CNS",a.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('case when au.tot_assigns is not null then (a.qty - au.tot_assigns) else a.qty end as remaining'));
        $db->wherenull('a.deleted_at');

        if( isset($req['category_id']) && $category_id = trim($req['category_id']) ){
            $db->where("cat.id", "=", $category_id);
        }
        // check asset location permission
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

        $return['total'] = $db->count();
        $return['filtered'] = $return['total']; 

        $is_searching = false;
        if(isset($req["filters"])) {
            $filters = $req["filters"];

            $filter_cond = "";
            if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                $db->where("a.location_id", "=", (int) $filters['location']);
            }
            if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                $db->where("a.category_id", "=", (int) $filters['category']);
                $db->where('cat.category_type', '=', 'consumable');
            }
            if(isset($filters["account"]) && $filters['account'] && $filters['account'] != "null") {
                $db->where("cat.account_type_id", "=", (int) $filters['account']);
            }
            if(isset($filters["company"]) && $filters['company'] && $filters['company'] != "null") {
                $db->where("a.company_id", "=", (int) $filters['company']);
            }
            if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                $db->where("a.manufacturer_id", "=", (int) $filters['manufacturer']);
            }
            if(isset($filters["departments"]) && $filters['departments'] && $filters['departments'] != "null") {
                $db->where("a.department_id", "=", (int) $filters['departments']);
            }
            if(isset($filters["suppliers"]) && $filters['suppliers'] && $filters['suppliers'] != "null") {
                $db->where("a.supplier_id", "=", (int) $filters['suppliers']);
            }
           
            $is_searching = true;
           
        }
       
        if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
            $whereStr = sprintf('(a.name like "%%%1$s%%" or pr.name like "%%%1$s%%" or unique_tag like "%%%1$s%%" or cmp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.qty like "%%%1$s%%" or (case when au.tot_assigns is not null then (a.qty - au.tot_assigns) else a.qty end) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $is_searching = true;
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

        $return["data"] = $db->get();
        $return["page"] = $page;
       
       
        return response()->json($return);
    }
    // consumables report page
    
    public function getIndex(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('ConsumableReport') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        // $vd = new stdClass();
        // $vd->category = Category::select('id', 'name as text')->orderBy('name')->get(); 
        // $vd->manufacturer = Manufacture::select('id', 'name as text')->orderBy('name')->get(); 
        // $vd->department = Department::select('id', 'name as text')->orderBy('name')->get(); 

        $sort_fields = [
            "consumables" => [
                
                ["id"=>1,"text"=>"Batch No"],
                ["id"=>2,"text"=>"Unique Tag"],
                ["id"=>3,"text"=>"Consumable Name"],
                ["id"=>4,"text"=>"Category"],
                ["id"=>5,"text"=>"Company"],
                ["id"=>6,"text"=>"Manufacturer"],
                ["id"=>7,"text"=>"Location"],
                ["id"=>8,"text"=>"Checkout User"],
                ["id"=>9,"text"=>"Checkout UserFullName"],
                ["id"=>10,"text"=>"Purchase Date"],
                ["id"=>11,"text"=>"Ticket ID"],
                ["id"=>12,"text"=>"Assigned By"]
            ],
        ];

        $tbl_fields = [
            "consumables" => ["col1" => "id","col2" => "unique_tag", "col3" => "con_name", "col4" => "cat_name","col5" => "cmp_name","col6" => "manu_name","col7" => "loc_name" ,"col8" => "checkout_user" ,"col9" => "full_name","col10" => "purchase_cost"],
        ];
        return view("reports.consumables")->with("sort_fields", $sort_fields)->with("tbl_fields", $tbl_fields);
    }

    public function ajaxIndex(Request $request) {
        $return = ["total"=>0,"filtered"=>0,"data"=>[]];
        $req = $request->all();

        $fields = array(
            '1'  => 'con_name',
            '2'  => 'unique_tag',
            '3'  => 'cat_name',
            '4'  => 'cmp_name',
            '5'  => 'manu_name',
            '6'  => 'loc_name',
            '7'  => 'checkout_user',
            '8'  => 'full_name',
            '9'  => 'c.created_at',
            '10'  => 'c.purchase_date',
            '11'  => 'cu.ticket_id',
            '12'  => 'requestor_full_name'
        );


        $db = DB::table('consumables_users as cu');
        $db->leftJoin('users as enduser', 'enduser.id', '=', 'cu.assigned_to');
        $db->leftJoin('users as adminuser', 'adminuser.id', '=', 'cu.user_id');
        $db->Join('consumables as c', 'c.id', '=', 'cu.consumable_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'c.company_id');
        $db->leftJoin('manufacturers as manu', 'manu.id', '=', 'c.manufacturer_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');
        $db->leftJoin('departments as dept', 'dept.id', '=', 'c.department_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');
        $db->leftJoin('users as au', function ($join) {
            $join->on('au.id', '=', 'cu.assigned_to')->where('cu.assigned_for', '=', 1);
        });
        $db->leftJoin('places as ap', function ($join) {
            $join->on('ap.id', '=', 'cu.assigned_to')->where('cu.assigned_for', '=', 2);
        });
        $db->leftJoin('assets as aa', function ($join) {
            $join->on('aa.id', '=', 'cu.assigned_to')->where('cu.assigned_for', '=', 3);
        });
        $db->select('cu.id', 'c.id as tag', 'c.name as con_name', 'c.unique_tag as unique_tag', 'enduser.id as enduserid', 'enduser.username as checkout_user','cmp.name as cmp_name', 'manu.name as manu_name', 'cat.name as cat_name', 'c.purchase_cost', 'dept.name as dept_name', 'loc.name as loc_name', 'cu.ticket_id');
        $db->addSelect(DB::raw('CONCAT(adminuser.first_name, " ", adminuser.last_name) as requestor_full_name'));
        $db->addSelect(DB::raw('CASE WHEN cu.assigned_for = 1 THEN concat(enduser.first_name, " ", enduser.last_name) ELSE "" END as full_name'));
        $db->addSelect(DB::raw('DATE_FORMAT(cu.created_at, "%d %b %Y") as created_date_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('CASE WHEN cu.assigned_for = 1 THEN "User" WHEN cu.assigned_for = 2 THEN "Place" WHEN cu.assigned_for = 3 THEN "Device" ELSE "-" END as assigned_for'));
        $db->addSelect(DB::raw('CASE WHEN cu.assigned_for = 1 THEN CONCAT_WS(" ", au.first_name, au.last_name, "-", au.username) WHEN cu.assigned_for = 2 THEN ap.place WHEN cu.assigned_for = 3 THEN aa.asset_tag ELSE "-" END as assigned_to_name'));
        $db->whereNull('c.deleted_at');

        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }

        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];
        $is_searching = false;

        if(isset($req["filters"])) {
            $filters = $req["filters"];

            if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                $db->where("cat.id", "=", (int) $filters['category']);
            }
            if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                $db->where("manu.id", "=", (int) $filters['manufacturer']);
            }
            if(isset($filters["user"]) && $filters['user'] && $filters['user'] != "null") {
                $db->where("enduser.id", "=", (int) $filters['user']);
            }
            if(isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                $db->where("dept.id", "=", (int) $filters['department']);
            }
            $based_on_possible = ['1'=>'c.purchase_date', '2'=>'cu.created_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
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
        if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('(loc.name like "%%%1$s%%" or concat("CN", c.id) like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or cmp.name like "%%%1$s%%" or dept.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or manu.name like "%%%1$s%%" or enduser.username like "%%%1$s%%" or concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or case when c.purchase_cost then FORMAT(c.purchase_cost, 2) else "0.00" end like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%y") like "%%%1$s%%" or DATE_FORMAT(cu.created_at, "%%d %%b %%y") like "%%%1$s%%" or DATE_FORMAT(c.created_at, "%%d %%b %%y") like "%%%1$s%%" or cu.ticket_id like "%%%1$s%%" or CONCAT(adminuser.first_name, " ", adminuser.last_name) like "%%%1$s%%" or CONCAT(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or case when cu.assigned_for = 1 then "user" when cu.assigned_for = 2 then "place" when cu.assigned_for = 3 then "device" else "-" end like "%%%1$s%%" or case when cu.assigned_for = 1 then concat_ws(" ", au.first_name, au.last_name, "-", au.username) when cu.assigned_for = 2 then ap.place when cu.assigned_for = 3 then aa.asset_tag else "-" end like "%%%1$s%%")', strtolower($search_key));
            } else {
                $whereStr = sprintf('(loc.name like "%%%1$s%%" or concat("CNS", c.id) like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or cmp.name like "%%%1$s%%" or dept.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or manu.name like "%%%1$s%%" or enduser.username like "%%%1$s%%" or concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or case when c.purchase_cost then FORMAT(c.purchase_cost, 2) else "0.00" end like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%y") like "%%%1$s%%" or DATE_FORMAT(cu.created_at, "%%d %%b %%y") like "%%%1$s%%" or DATE_FORMAT(c.created_at, "%%d %%b %%y") like "%%%1$s%%" or cu.ticket_id like "%%%1$s%%" or CONCAT(adminuser.first_name, " ", adminuser.last_name) like "%%%1$s%%" or CONCAT(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or case when cu.assigned_for = 1 then "user" when cu.assigned_for = 2 then "place" when cu.assigned_for = 3 then "device" else "-" end like "%%%1$s%%" or case when cu.assigned_for = 1 then concat_ws(" ", au.first_name, au.last_name, "-", au.username) when cu.assigned_for = 2 then ap.place when cu.assigned_for = 3 then aa.asset_tag else "-" end like "%%%1$s%%")', strtolower($search_key));
            }
            $db->whereRaw($whereStr);
            $is_searching = true;
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

        $return["data"] = $db->get();
        $return["page"] = $page;
       
        return response()->json($return);
    }

    public function downloadReport(Request $request) {
        $req = $request->all();

        $return = ['status' => 'danger', 'msg' => 'Unable to export the Consumables'];
        if(! Auth::user()->hasPermissionTo('ConsumableReportDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }

        $db = DB::table('consumables_users as cu');
        $db->leftJoin('users as enduser', 'enduser.id', '=', 'cu.assigned_to');
        $db->leftJoin('users as adminuser', 'adminuser.id', '=', 'cu.user_id');
        $db->Join('consumables as c', 'c.id', '=', 'cu.consumable_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'c.company_id');
        $db->leftJoin('manufacturers as manu', 'manu.id', '=', 'c.manufacturer_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');
        $db->leftJoin('departments as dept', 'dept.id', '=', 'c.department_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');

        $db->leftJoin('users as au', function ($join) {
            $join->on('au.id', '=', 'cu.assigned_to')->where('cu.assigned_for', '=', 1);
        });
        $db->leftJoin('places as ap', function ($join) {
            $join->on('ap.id', '=', 'cu.assigned_to')->where('cu.assigned_for', '=', 2);
        });
        $db->leftJoin('assets as aa', function ($join) {
            $join->on('aa.id', '=', 'cu.assigned_to')->where('cu.assigned_for', '=', 3);
        });
        $db->select('cu.id','c.id as tag','c.name as con_name', 'c.unique_tag as unique_tag','enduser.id as enduserid','enduser.username as checkout_user','cmp.name as cmp_name','manu.name as manu_name', 'cat.name as cat_name','c.purchase_cost','loc.name as loc_name','dept.name as dept_name','cu.ticket_id');
        $db->addSelect(DB::raw('DATE_FORMAT(cu.created_at, "%d %b %Y %h:%i %p") as 	created_at'));
        $db->addSelect(DB::raw('CASE WHEN cu.assigned_for = 1 THEN concat(enduser.first_name, " ", enduser.last_name) ELSE "" END as full_name'));
        $db->addSelect(DB::raw('concat(adminuser.first_name, " ", adminuser.last_name) as requestor_full_name'));
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CN",c.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CNS",c.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('DATE_FORMAT(cu.created_at, "%d %b %Y") as created_date_on'));
        $db->addSelect(DB::raw('CASE WHEN cu.assigned_for = 1 THEN "User" WHEN cu.assigned_for = 2 THEN "Place" WHEN cu.assigned_for = 3 THEN "Device" ELSE "-" END as assigned_for'));
        $db->addSelect(DB::raw('CASE WHEN cu.assigned_for = 1 THEN CONCAT_WS(" ", au.first_name, au.last_name, "-", au.username) WHEN cu.assigned_for = 2 THEN ap.place WHEN cu.assigned_for = 3 THEN aa.asset_tag ELSE "-" END as assigned_to_name'));
        //$db->addSelect(DB::raw('case when c.purchase_cost then FORMAT(c.purchase_cost, 2) else "0.00" end as purchase_cost_format'));
        //$db->where('cu.consumable_id','=',$consumable_id);
        $db->whereNull('c.deleted_at');

        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if(isset($filters->search)) {
                //$req["filters"] = (array) $filters->other_filters;
                $req["search"] = $filters->search;
            }
            if(isset($filters->other_filters)) {
                $req["filters"] = (array) $filters->other_filters;
            }

            if(isset($req["filters"])) {
                $filters = $req["filters"];

                if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                    $db->where("cat.id", "=", (int) $filters['category']);
                }
                if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                    $db->where("manu.id", "=", (int) $filters['manufacturer']);
                }
                if(isset($filters["user"]) && $filters['user'] && $filters['user'] != "null") {
                    $db->where("enduser.id", "=", (int) $filters['user']);
                }
                if(isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                    $db->where("dept.id", "=", (int) $filters['department']);
                }
                $based_on_possible = ['1'=>'c.purchase_date', '2'=>'cu.created_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
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


            }

            if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                if (config("app.client") == "etherealmachines") {
                    $whereStr = sprintf('(loc.name like "%%%1$s%%" or concat("CN",c.id) like "%%%1$s%%" or unique_tag like "%%%1$s%%" or  c.name like "%%%1$s%%" or cmp.name like "%%%1$s%%" or dept.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or manu.name like "%%%1$s%%" or enduser.username like "%%%1$s%%" or concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or (case when c.purchase_cost then FORMAT(c.purchase_cost, 2) else "0.00" end) like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(cu.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(c.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or cu.ticket_id like "%%%1$s%%" or case when cu.assigned_for = 1 then "user" when cu.assigned_for = 2 then "place" when cu.assigned_for = 3 then "device" else "-" end like "%%%1$s%%" or case when cu.assigned_for = 1 then concat_ws(" ", au.first_name, au.last_name, "-", au.username) when cu.assigned_for = 2 then ap.place when cu.assigned_for = 3 then aa.asset_tag else "-" end like "%%%1$s%%")', $search_key);
                } else {
                    $whereStr = sprintf('(loc.name like "%%%1$s%%" or concat("CNS",c.id) like "%%%1$s%%" or unique_tag like "%%%1$s%%" or  c.name like "%%%1$s%%" or cmp.name like "%%%1$s%%" or dept.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or manu.name like "%%%1$s%%" or enduser.username like "%%%1$s%%" or concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or (case when c.purchase_cost then FORMAT(c.purchase_cost, 2) else "0.00" end) like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(cu.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(c.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or cu.ticket_id like "%%%1$s%%" or case when cu.assigned_for = 1 then "user" when cu.assigned_for = 2 then "place" when cu.assigned_for = 3 then "device" else "-" end like "%%%1$s%%" or case when cu.assigned_for = 1 then concat_ws(" ", au.first_name, au.last_name, "-", au.username) when cu.assigned_for = 2 then ap.place when cu.assigned_for = 3 then aa.asset_tag else "-" end like "%%%1$s%%")', $search_key);
                }
                $db->whereRaw($whereStr);
                $is_searching = true;
            }

        }

        $records = $db->get();
        $result = json_decode(json_encode($records, true),true);
        return Excel::download(new ConsumableReport($result), 'Consumable_Report.xlsx');
    }

    public function downloadReportPDF(Request $request) {

        $return = ['status' => 'danger', 'msg' => 'Unable to export the details'];
        if(! Auth::user()->hasPermissionTo('ConsumableReportDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.Permission_denied');
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
        $db->leftJoin('departments as dept', 'dept.id', '=', 'enduser.department_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');
        
        $db->leftJoin('users as au', function ($join) {
            $join->on('au.id', '=', 'cu.assigned_to')->where('cu.assigned_for', '=', 1);
        });
        $db->leftJoin('places as ap', function ($join) {
            $join->on('ap.id', '=', 'cu.assigned_to')->where('cu.assigned_for', '=', 2);
        });
        $db->leftJoin('assets as aa', function ($join) {
            $join->on('aa.id', '=', 'cu.assigned_to')->where('cu.assigned_for', '=', 3);
        });

        $db->select('cu.id','c.id as tag','c.name as con_name','c.unique_tag as unique_tag','enduser.id as enduserid','enduser.username as checkout_user','cmp.name as cmp_name','manu.name as manu_name', 'cat.name as cat_name','c.purchase_cost','loc.name as loc_name','dept.name as dept_name','cu.ticket_id');
        $db->addSelect(DB::raw('DATE_FORMAT(cu.created_at, "%d %b %Y %h:%i %p") as 	created_at'));
        $db->addSelect(DB::raw('concat(enduser.first_name, " ", enduser.last_name) as full_name'));
        $db->addSelect(DB::raw('concat(adminuser.first_name, " ", adminuser.last_name) as requestor_full_name'));
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CN",c.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CNS",c.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('DATE_FORMAT(cu.created_at, "%d %b %Y") as created_date_on'));
        $db->addSelect(DB::raw('CASE WHEN cu.assigned_for = 1 THEN "User" WHEN cu.assigned_for = 2 THEN "Place" WHEN cu.assigned_for = 3 THEN "Device" ELSE "-" END as assigned_for'));
        $db->addSelect(DB::raw('CASE WHEN cu.assigned_for = 1 THEN CONCAT_WS(" ", au.first_name, au.last_name, "-", au.username) WHEN cu.assigned_for = 2 THEN ap.place WHEN cu.assigned_for = 3 THEN aa.asset_tag ELSE "-" END as assigned_to_name'));
        //$db->addSelect(DB::raw('case when c.purchase_cost then FORMAT(c.purchase_cost, 2) else "0.00" end as purchase_cost_format'));
        //$db->where('cu.consumable_id','=',$consumable_id);
        $db->whereNull('c.deleted_at');

        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if(isset($filters->search)) {
                //$req["filters"] = (array) $filters->other_filters;
                $req["search"] = $filters->search;
            }
            if(isset($filters->other_filters)) {
                $req["filters"] = (array) $filters->other_filters;
            }

            if(isset($req["filters"])) {
                $filters = $req["filters"];

                if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                    $db->where("cat.id", "=", (int) $filters['category']);
                }
                if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                    $db->where("manu.id", "=", (int) $filters['manufacturer']);
                }
                if(isset($filters["user"]) && $filters['user'] && $filters['user'] != "null") {
                    $db->where("enduser.id", "=", (int) $filters['user']);
                }
                if(isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                    $db->where("dept.id", "=", (int) $filters['department']);
                }
                $based_on_possible = ['1'=>'c.purchase_date', '2'=>'cu.created_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
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
            }

            if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                if (config("app.client") == "etherealmachines") {
                    $whereStr = sprintf('(loc.name like "%%%1$s%%" or concat("CN",c.id) like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or cmp.name like "%%%1$s%%" or dept.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or manu.name like "%%%1$s%%" or enduser.username like "%%%1$s%%" or concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or (case when c.purchase_cost then FORMAT(c.purchase_cost, 2) else "0.00" end) like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(cu.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(c.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or cu.ticket_id like "%%%1$s%%" or case when cu.assigned_for = 1 then "user" when cu.assigned_for = 2 then "place" when cu.assigned_for = 3 then "device" else "-" end like "%%%1$s%%" or case when cu.assigned_for = 1 then concat_ws(" ", au.first_name, au.last_name, "-", au.username) when cu.assigned_for = 2 then ap.place when cu.assigned_for = 3 then aa.asset_tag else "-" end like "%%%1$s%%")', $search_key);
                } else {
                    $whereStr = sprintf('(loc.name like "%%%1$s%%" or concat("CNS",c.id) like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or cmp.name like "%%%1$s%%" or dept.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or manu.name like "%%%1$s%%" or enduser.username like "%%%1$s%%" or concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or (case when c.purchase_cost then FORMAT(c.purchase_cost, 2) else "0.00" end) like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(cu.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(c.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or cu.ticket_id like "%%%1$s%%" or case when cu.assigned_for = 1 then "user" when cu.assigned_for = 2 then "place" when cu.assigned_for = 3 then "device" else "-" end like "%%%1$s%%" or case when cu.assigned_for = 1 then concat_ws(" ", au.first_name, au.last_name, "-", au.username) when cu.assigned_for = 2 then ap.place when cu.assigned_for = 3 then aa.asset_tag else "-" end like "%%%1$s%%")', $search_key);
                }
                $db->whereRaw($whereStr);
                $is_searching = true;
            }
        }

        $data = $db->get();
        $properties = [];
        $properties['format'] = 'A4-L';

        $vd["records"] = $data;
        $pdf = PDF::loadView('reports.for_export_con', $vd, [], $properties);
        return $pdf->download('ConsumableReport.pdf');

    }

    // Consumable Location Wise report
    public function getConsumableLocationWise(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('LocationReport') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = new stdClass();
        $vd->category = Category::select('id', 'name as text')->orderBy('name')->get();  
        $vd->manufacturer = Manufacture::select('id', 'name as text')->orderBy('name')->get();
        $vd->suppliers = Supplier::select('id', 'name as text')->orderBy('name')->get();

        $sort_fields = [
            "consumable" => [
                ["id"=>1,"text"=>"Batch No"],
                ["id"=>2,"text"=>"Unique Tag"],
                ["id"=>3,"text"=>"Consumable Name"],
                ["id"=>4,"text"=>"Category"],
                ["id"=>5,"text"=>"Location"],
                ["id"=>6,"text"=>"Total Consumable"],
                ["id"=>7,"text"=>"Total Available"],
                ["id"=>8,"text"=>"Total Used"],
            ],
        ];

        $tbl_fields = [

            "consumable" => ["col1" => "id", "col2" => "unique_tag", "col3" => "name","col4" => "cat_name","col5" => "loc_name" ,"col6" => "qty" ,"col7" => "remaining","col8" => "tot_assigns"],
        ];
        return view("reports.consumable-loc-wise")->with("sort_fields", $sort_fields)->with("tbl_fields", $tbl_fields)->with("vd",$vd);
    }

    public function ajaxConsumableLocationWise(Request $request) {
        $return = ["total"=>0,"filtered"=>0,"data"=>[]];
        $req = $request->all();

        $fields = array(
            '1'  => 'id',
            '2'  => 'unique_tag',
            '3'  => 'name',
            '4'  => 'cat_name',
            '5'  => 'loc_name',
            '6'  => 'qty',
            '7'  => 'remaining',
            '8'  => 'tot_assigns'
        );

        $db = DB::table('consumables as c');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'c.company_id');
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(assigned_to) as tot_assigns FROM `consumables_users` group by consumable_id) cu'), function($j) {
            $j->on('c.id', '=', 'cu.consumable_id');
        });
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');

        $db->select('c.id', 'c.name as name', 'c.unique_tag as unique_tag','cmp.name as cmp_name','loc.name as loc_name','c.qty', 'cat.name as cat_name', 'c.qty', 'c.order_number','cu.tot_assigns as tot_assigns');
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('FORMAT(c.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end as remaining'));

        $db->whereNotNull('c.location_id');
        $db->whereNull('c.deleted_at');

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("c.company_id", $companyIds);

        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if ($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }

        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];

        $is_searching = false;

        if(isset($req["filters"])) {
            $filters = $req["filters"];

            if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                $db->whereIn("cat.id", $filters['category']);
            }
            if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                $db->whereIn("loc.id", $filters['location']);
            }
            if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                $db->where("c.manufacturer_id", "=", (int) $filters['manufacturer']);
            }
            if(isset($filters["departments"]) && $filters['departments'] && $filters['departments'] != "null") {
                $db->where("c.department_id", "=", (int) $filters['departments']);
            }
            if(isset($filters["suppliers"]) && $filters['suppliers'] && $filters['suppliers'] != "null") {
                $db->where("c.supplier_id", "=", (int) $filters['suppliers']);
            }
            
            $is_searching = true;

        }
        if( isset($req["search"]) && $search_key = trim($req["search"])) {
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('(c.id like "%%%1$s%%" or concat("CN",c.id) like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('(c.id like "%%%1$s%%" or concat("CNS",c.id) like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $is_searching = true;
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

        $return["data"] = $db->get();
        $return["page"] = $page;
       
        return response()->json($return);
    }

    public function downloadConsumableLocationWise(Request $request) {

        $req = $request->all();

        $return = ['status' => 'danger', 'msg' => 'Unable to export the Consumables'];
        if(! Auth::user()->hasPermissionTo('LocationReportDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $db = DB::table('consumables as c');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'c.company_id');
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(assigned_to) as tot_assigns FROM `consumables_users` group by consumable_id) cu'), function($j) {
            $j->on('c.id', '=', 'cu.consumable_id');
        });
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');
        $db->leftJoin('places as p', 'p.id', '=', 'c.internal_place_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');

        $db->select('c.id', 'c.unique_tag as unique_tag', 'c.name as name', 'cmp.name as cmp_name','loc.name as loc_name','c.qty', 'cat.name as cat_name', 'c.qty', 'c.order_number','cu.tot_assigns as tot_assigns','p.place as internal_place');
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CN",c.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CNS",c.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(c.created_at, "%d %b %Y") as c_created_at'));
        $db->addSelect(DB::raw('DATE_FORMAT(c.updated_at, "%d %b %Y") as c_updated_at'));
        $db->addSelect(DB::raw('FORMAT(c.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end as remaining'));

        $db->whereNull('c.deleted_at');
        $db->whereNotNull('c.location_id');
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("c.company_id", $companyIds);

        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if ($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }

        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if(isset($filters->search)) {
                //$req["filters"] = (array) $filters->other_filters;
                $req["search"] = $filters->search;
            }
            if(isset($filters->other_filters)) {
                $req["filters"] = (array) $filters->other_filters;
            }

            if(isset($req["filters"])) {
                $filters = $req["filters"];

                if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                    $db->whereIn("cat.id", $filters['category']);
                }
                if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                    $db->whereIn("loc.id", $filters['location']);
                }
                if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                    $db->where("c.manufacturer_id", "=", (int) $filters['manufacturer']);
                }
                if(isset($filters["departments"]) && $filters['departments'] && $filters['departments'] != "null") {
                    $db->where("c.department_id", "=", (int) $filters['departments']);
                }
                if(isset($filters["suppliers"]) && $filters['suppliers'] && $filters['suppliers'] != "null") {
                    $db->where("c.supplier_id", "=", (int) $filters['suppliers']);
                }
            }

            if( isset($req["search"]) && $search_key = trim($req["search"])) {
                if (config("app.client") == "etherealmachines") {
                    $whereStr = sprintf('(c.id like "%%%1$s%%" or concat("CN",c.id) like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%")', $search_key);
                } else {
                    $whereStr = sprintf('(c.id like "%%%1$s%%" or concat("CNS",c.id) like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%")', $search_key);
                }
                $db->whereRaw($whereStr);
            }
        }
        $records = $db->get();
        $result = json_decode(json_encode($records, true),true);
        return Excel::download(new LocationWiseConsumablesReport($result), 'Location_Wise_Consumables_Report.xlsx');

    }

    public function downloadConsumableLocationWisePDF(Request $request) {

        $return = ['status' => 'danger', 'msg' => 'Unable to export the details'];
        $vd = [];
        
        $db = DB::table('consumables as c');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'c.company_id');
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(assigned_to) as tot_assigns FROM `consumables_users` group by consumable_id) cu'), function($j) {
            $j->on('c.id', '=', 'cu.consumable_id');
        });
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');

        $db->select('c.id', 'c.name as name', 'cmp.name as cmp_name','loc.name as loc_name','c.qty', 'cat.name as cat_name', 'c.qty', 'c.order_number','cu.tot_assigns as tot_assigns');
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('FORMAT(c.purchase_cost, 2) as purchase_cost_format'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CN",c.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CNS",c.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end as remaining'));

        $db->whereNull('c.deleted_at');
        $db->whereNotNull('c.location_id');

        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if ($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }
        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if(isset($filters->search)) {
                //$req["filters"] = (array) $filters->other_filters;
                $req["search"] = $filters->search;
            }
            if(isset($filters->other_filters)) {
                $req["filters"] = (array) $filters->other_filters;
            }

            if(isset($req["filters"])) {
                $filters = $req["filters"];
    
                if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                    $db->whereIn("cat.id", $filters['category']);
                }
                if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                    $db->whereIn("loc.id", $filters['location']);
                }
            }

            if( isset($req["search"]) && $search_key = trim($req["search"])) {
                if (config("app.client") == "etherealmachines") {
                    $whereStr = sprintf('((case when c.id is not null then concat_ws("","CN",c.id) else "" end) like "%%%1$s%%" or  c.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%")', $search_key);
                } else {
                    $whereStr = sprintf('((case when c.id is not null then concat_ws("","CNS",c.id) else "" end) like "%%%1$s%%" or  c.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%")', $search_key);
                }
                $db->whereRaw($whereStr);
            }
        }

        $data = $db->get();
        $properties = [];
        $properties['format'] = 'A4-L';
        
        $vd["records"] = $data;
        $pdf = PDF::loadView('reports.for_export_con_loc', $vd, [], $properties);
        return $pdf->download('ConsumableLocationWise.pdf');
    }
    
    // Deleted Consumable report
    public function getDeletedConsumables(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('DeletedAssetsReport') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = new stdClass();
        $vd->category = Category::select('id', 'name as text')->orderBy('name')->get();  
        $vd->manufacturer = Manufacture::select('id', 'name as text')->orderBy('name')->get();
        $sort_fields = [
            "consumable" => [
                ["id"=>1,"text"=>"Batch No"],
                ["id"=>2,"text"=>"Unique Tag"],
                ["id"=>3,"text"=>"Consumable Name"],
                ["id"=>4,"text"=>"Category"],
                ["id"=>5,"text"=>"Manufacturer"],
                ["id"=>6,"text"=>"Location"],
                ["id"=>7,"text"=>"Total Consumable"],
                ["id"=>8,"text"=>"Purchase Reference"],
                ["id"=>9,"text"=>"Purchase Cost"],
                ["id"=>10,"text"=>"Purchase Date"],
                ["id"=>11,"text"=>"Deleted At"],
            ],
        ];

        $tbl_fields = [

            "consumable" => ["col1" => "id", "col2" => "name", "col3" => "cat_name","col4" => "manu_name","col5" => "loc_name" ,"col6" => "qty" ,"col7" => "purchase_invoice","col8" => "purchase_cost_format","col9" => "purchase_date_on","col10" => "deleted_at_format"]
        ];
        return view("reports.deleted-consumables")->with("sort_fields", $sort_fields)->with("tbl_fields", $tbl_fields)->with("vd",$vd);
    }

    public function ajaxDeletedConsumables(Request $request) {	
        $return = ["total"=>0,"filtered"=>0,"data"=>[]];	
        $req = $request->all();	
        $fields = array(	
            '1'  => 'id',	
            '2'  => 'unique_tag',	
            '3'  => 'name',	
            '4'  => 'cat_name',	
            '5'  => 'manu_name',	
            '6'  => 'loc_name',	
            '7'  => 'qty',	
            '8'  => 'purchase_invoice',	
            '9'  => 'purchase_cost_format',	
            '10'  => 'purchase_date_on',	
            '11' => 'deleted_at_format'	
        );	
        $db = DB::table('consumables as c');	
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(assigned_to) as tot_assigns FROM `consumables_users` group by consumable_id) cu'), function($j) {	
            $j->on('c.id', '=', 'cu.consumable_id');	
        });	
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'c.company_id');	
        $db->leftJoin('manufacturers as manu', 'manu.id', '=', 'c.manufacturer_id');	
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');	
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');	
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'c.invoice_id');	
        $db->select('c.id', 'c.unique_tag as unique_tag', 'c.name as name', 'cmp.name as cmp_name','loc.name as loc_name','c.qty', 'cat.name as cat_name', 'c.qty', 'c.order_number','cu.tot_assigns as tot_assigns','manu.name as manu_name','cmp.name as cmp_name','pur.invoice_no as purchase_invoice');	
        
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('concat("CN",c.id) as tag'));
        } else {
            $db->addSelect(DB::raw('concat("CNS",c.id) as tag'));
        }
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));	
        $db->addSelect(DB::raw('DATE_FORMAT(c.deleted_at, "%d %b %Y %h:%i %p") as deleted_at_format'));	
        $db->addSelect(DB::raw('FORMAT(c.purchase_cost, 2) as purchase_cost_format'));	
        $db->addSelect(DB::raw('case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end as remaining'));	
        $db->whereNotNull('c.deleted_at');
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("c.company_id", $companyIds);

        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }

        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];	

        $is_searching = false;
        if(isset($req["filters"])) {
            $filters = $req["filters"];
            if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                $db->whereIn("cat.id", $filters['category']);
            }
            if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                $db->whereIn("loc.id", $filters['location']);
            }
            if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                $db->whereIn("c.manufacturer_id", $filters['manufacturer']);
            }
            $is_searching = true;
        }

        if( isset($req["search"]) && $search_key = trim($req["search"])) {	
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('(CONCAT("CN", c.id) like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or FORMAT(c.purchase_cost, 2) like "%%%1$s%%" or pur.invoice_no like "%%%1$s%%" or manu.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%Y") like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%" or DATE_FORMAT(c.deleted_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);	
            } else {
                $whereStr = sprintf('(CONCAT("CNS", c.id) like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or FORMAT(c.purchase_cost, 2) like "%%%1$s%%" or pur.invoice_no like "%%%1$s%%" or manu.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%Y") like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%" or DATE_FORMAT(c.deleted_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);	
            }
            $db->whereRaw($whereStr);	
            $return['filtered'] = $db->count();	
        }
        if($is_searching == true){
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
        $return["data"] = $db->get();	
        $return["page"] = $page;	
       	
        return response()->json($return);	
    }

    public function downloadDeletedConsumables(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('DeletedAssetsReportDownload') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $req = $request->all();	
        	
        $db = DB::table('consumables as c');	
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(assigned_to) as tot_assigns FROM `consumables_users` group by consumable_id) cu'), function($j) {	
            $j->on('c.id', '=', 'cu.consumable_id');	
        });	
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'c.company_id');	
        $db->leftJoin('manufacturers as manu', 'manu.id', '=', 'c.manufacturer_id');	
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');	
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');	
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'c.invoice_id');	
        $db->select('c.id', 'c.unique_tag as unique_tag','c.name as name', 'cmp.name as cmp_name','loc.name as loc_name','c.qty', 'cat.name as cat_name', 'c.qty', 'c.order_number','cu.tot_assigns as tot_assigns','manu.name as manu_name','cmp.name as cmp_name','pur.invoice_no as purchase_invoice');	
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('concat("CN",c.id) as tag'));
        } else {
            $db->addSelect(DB::raw('concat("CNS",c.id) as tag'));
        }
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));	
        $db->addSelect(DB::raw('DATE_FORMAT(c.deleted_at, "%d %b %Y %h:%i %p") as deleted_at_format'));	
        $db->addSelect(DB::raw('FORMAT(c.purchase_cost, 2) as purchase_cost_format'));	
        $db->addSelect(DB::raw('case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end as remaining'));	
        $db->whereNotNull('c.deleted_at');

        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("c.company_id", $companyIds);

        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];
        if($req){
            $request_filters = base64_decode($req['search']);
            $filters = json_decode($request_filters);
            $is_searching = false;
            if(isset($filters->other_filters)) {
                $filter = $filters->other_filters;
                if(isset($filter->category) && $filter->category && $filter->category != "null") {
                    $db->whereIn("cat.id", $filter->category);
                }
                if(isset($filter->location) && $filter->location && $filter->location != "null") {
                    $db->whereIn("loc.id", $filter->location);
                }
                if(isset($filter->manufacturer) && $filter->manufacturer && $filter->manufacturer != "null") {
                    $db->whereIn("c.manufacturer_id", $filter->manufacturer);
                }
                $is_searching = true;
            }
            if( isset($filters->search) && $search_key = trim($filters->search)) {
                if (config("app.client") == "etherealmachines") {
                    $whereStr = sprintf('(c.id like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or  c.name like "%%%1$s%%" or FORMAT(c.purchase_cost, 2) like "%%%1$s%%" or pur.invoice_no like "%%%1$s%%" or manu.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%Y") like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%" or DATE_FORMAT(c.deleted_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or concat("CN",c.id) like "%%%1$s%%")', $search_key);	
                } else {
                    $whereStr = sprintf('(c.id like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or  c.name like "%%%1$s%%" or FORMAT(c.purchase_cost, 2) like "%%%1$s%%" or pur.invoice_no like "%%%1$s%%" or manu.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%Y") like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%" or DATE_FORMAT(c.deleted_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or concat("CNS",c.id) like "%%%1$s%%")', $search_key);	
                }
                $db->whereRaw($whereStr);	
                $return['filtered'] = $db->count();	
            }
            if($is_searching == true){
                $return['filtered'] = $db->count();
            }
        }
        $records = $db->get();
        $result = json_decode(json_encode($records, true),true);
        return Excel::download(new DeletedConsumablesReport($result), 'Deleted_Consumables_Report.xlsx');
    }

    public function downloadDeletedConsumablesPDF(Request $request) {

        $return = ['status' => 'danger', 'msg' => 'Unable to export the details'];
        if(! Auth::user()->hasPermissionTo('DeletedAssetsReportDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('consumables.controller.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = [];
        $req = $request->all();
        $db = DB::table('consumables as c');
        $db->leftJoin(DB::raw('(SELECT consumable_id, count(assigned_to) as tot_assigns FROM `consumables_users` group by consumable_id) cu'), function($j) {
            $j->on('c.id', '=', 'cu.consumable_id');
        });
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'c.company_id');
        $db->leftJoin('manufacturers as manu', 'manu.id', '=', 'c.manufacturer_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'c.invoice_id');

        $db->select('c.id', 'c.name as name', 'cmp.name as cmp_name','loc.name as loc_name','c.qty', 'cat.name as cat_name', 'c.qty', 'c.order_number','cu.tot_assigns as tot_assigns','manu.name as manu_name','cmp.name as cmp_name','pur.invoice_no as purchase_invoice','cu.ticket_id');
        $db->addSelect(DB::raw('DATE_FORMAT(c.purchase_date, "%d %b %Y") as purchase_date_on'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('concat("CN",c.id) as tag'));
        } else {
            $db->addSelect(DB::raw('concat("CNS",c.id) as tag'));
        }
        $db->addSelect(DB::raw('DATE_FORMAT(c.deleted_at, "%d %b %Y %h:%i %p") as deleted_at_format'));
        $db->addSelect(DB::raw('FORMAT(c.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end as remaining'));
        $db->whereNotNull('c.deleted_at');
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("c.company_id", $companyIds);

        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }
        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];

        if($req){
            $request_filters = base64_decode($req['search']);
            $filters = json_decode($request_filters);
            $is_searching = false;
            if(isset($filters->other_filters)) {
                $filter = $filters->other_filters;
                if(isset($filter->category) && $filter->category && $filter->category != "null") {
                    $db->whereIn("cat.id", $filter->category);
                }
                if(isset($filter->location) && $filter->location && $filter->location != "null") {
                    $db->whereIn("loc.id", $filter->location);
                }
                if(isset($filter->manufacturer) && $filter->manufacturer && $filter->manufacturer != "null") {
                    $db->whereIn("c.manufacturer_id", $filter->manufacturer);
                }
                $is_searching = true;
            }
            if( isset($filters->search) && $search_key = trim($filters->search)) {
                if (config("app.client") == "etherealmachines") {
                    $whereStr = sprintf('(c.id like "%%%1$s%%" or  c.name like "%%%1$s%%" or FORMAT(c.purchase_cost, 2) like "%%%1$s%%" or pur.invoice_no like "%%%1$s%%" or manu.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%Y") like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%" or DATE_FORMAT(c.deleted_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or concat("CN",c.id) like "%%%1$s%%")', $search_key);
                } else {
                    $whereStr = sprintf('(c.id like "%%%1$s%%" or  c.name like "%%%1$s%%" or FORMAT(c.purchase_cost, 2) like "%%%1$s%%" or pur.invoice_no like "%%%1$s%%" or manu.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or cu.tot_assigns  like "%%%1$s%%" or c.qty like "%%%1$s%%" or DATE_FORMAT(c.purchase_date, "%%d %%b %%Y") like "%%%1$s%%" or (case when cu.tot_assigns is not null then (COALESCE(c.qty, 0) -  COALESCE(cu.tot_assigns, 0)) else COALESCE(c.qty, 0) end) like "%%%1$s%%" or DATE_FORMAT(c.deleted_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or concat("CNS",c.id) like "%%%1$s%%")', $search_key);
                }
                $db->whereRaw($whereStr);	
                $return['filtered'] = $db->count();	
            }
            if($is_searching == true){
                $return['filtered'] = $db->count();
            }
        }


        $data = $db->get();
        $properties = [];
        $properties['format'] = 'A4-L';
        
        $vd["records"] = $data;
        $pdf = PDF::loadView('reports.for_export_del_con', $vd, [], $properties);
        return $pdf->download('Deleted Consumables.pdf');
    }

    public function getConsumableActivity(Request $request) {
       $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('ConsumableReport') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        return view("reports.consumable-activity");
    }

    public function ajaxConsumableActivities(Request $request) {
        $req = $request->all();

        $fields = array(
            '0' => 'con_batch_no',
            '1' => 'unique_tag',
            '2' => 'action_type',
            '3' => 'name',
            '4' => 'category_name',
            '5' => 'location_name',
            '6' => 'branch_code',
            '7' => 'place',
            '8' => 'assigned_for',
            '9' => 'assigned_to',
            '10' => 'assigned_by',
            '11' => 'a.created_at',
        );
        
        $db = DB::table('asset_logs as a')
        ->where('a.asset_type', 'Consumable')
        ->whereIn('a.action_type', ['checkout', 'New Add']);
        $db->leftJoin('consumables as cons', function($q) {
            $q->on('cons.id', '=', 'a.consumable_id');
        });
        $db->leftJoin('users as admin', 'admin.id', '=', 'a.user_id');
        $db->leftJoin('categories as cat', function($q) {
            $q->on('cat.id', '=', 'cons.category_id');
            $q->where('cons.category_id', '<>', 0);
        });
        $db->leftJoin('locations as loc', function($q) {
            $q->on('cons.location_id', '=', 'loc.id');
        });
     
        $db->leftJoin('places as place_loc', function($q) {
            $q->on('cons.internal_place_id', '=', 'place_loc.id');
        });

        $db->leftJoin('users as au', function ($join) {
            $join->on('au.id', '=', 'a.checkedout_to')->where('a.assigned_for', '=', 1);
        });
        $db->leftJoin('places as ap', function ($join) {
            $join->on('ap.id', '=', 'a.checkedout_to')->where('a.assigned_for', '=', 2);
        });
        $db->leftJoin('assets as aa', function ($join) {
            $join->on('aa.id', '=', 'a.checkedout_to')->where('a.assigned_for', '=', 3);
        });
       $db->where('a.asset_type', 'Consumable');
       $db->whereIn('a.action_type', ['checkout', 'New Add']);
       $db->whereNotNull('a.consumable_id');

        $db->select('cons.id as consumable_id','a.id', 'a.action_type','a.note','cat.name as category_name','cons.unique_tag','loc.name as location_name','place_loc.place','loc.branch_code','cons.name','a.action_type as asset_type_text');
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when cons.id is not null then concat_ws("","CN",cons.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when cons.id is not null then concat_ws("","CNS",cons.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('concat(admin.first_name, " ", admin.last_name) as assigned_by'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.created_at, "%d %b %Y %h:%i %p") as created_at_on'));
        $db->addSelect([
            DB::raw('DATE_FORMAT(a.created_at, "%d %b %Y") as created_date'),
            DB::raw('DATE_FORMAT(a.created_at, "%h:%i %p") as created_time'),
        ]);
        $db->addSelect(DB::raw('CASE WHEN a.assigned_for = 1 THEN "User" WHEN a.assigned_for = 2 THEN "Place" WHEN a.assigned_for = 3 THEN "Device" ELSE "-" END as assigned_for'));
        $db->addSelect(DB::raw('CASE WHEN a.assigned_for = 1 THEN CONCAT_WS(" ", au.first_name, au.last_name, "-", au.username) WHEN a.assigned_for = 2 THEN ap.place WHEN a.assigned_for = 3 THEN aa.asset_tag ELSE "-" END as assigned_to'));
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("cons.company_id", $companyIds);
        $loc_previllage = Auth::user()->permitted_locations;
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where(function ($query) {
                    $query->orWhere('cons.location_id', '=', 0);
                });
            } else {
                $db->where(function ($query) use($permitted_loc) {
                    $query->orWhereIn('cons.location_id', $permitted_loc);
                });
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where(function ($query) {
                    $query->orWhere('cons.department_id', '=', 0);
                });
            } else {
                $db->where(function ($query) use($asset_depts) {
                    $query->orWhereIn('cons.department_id', $asset_depts);
                });
            }
        }
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];
    
        if (isset($req["search"]["value"])) {$search_key = trim($req["search"]["value"]);
            if (!empty($search_key)) {
                if (config("app.client") == "etherealmachines") {
                    $whereStr = sprintf('DATE_FORMAT(a.created_at, "%%d %%b %%Y") LIKE "%%%1$s%%" 
                    OR DATE_FORMAT(a.created_at, "%%h:%%i %%p") LIKE "%%%1$s%%"
                    OR a.note LIKE "%%%1$s%%" OR loc.name LIKE "%%%1$s%%" OR a.action_type LIKE "%%%1$s%%"
                    OR cat.name LIKE "%%%1$s%%" OR place_loc.place LIKE "%%%1$s%%" 
                    OR cons.unique_tag LIKE "%%%1$s%%" OR loc.branch_code LIKE "%%%1$s%%" 
                    OR cons.name LIKE "%%%1$s%%" OR CONCAT("CN", cons.id) LIKE "%%%1$s%%" 
                    OR CONCAT(admin.first_name, " ", admin.last_name) LIKE "%%%1$s%%"
                    OR ( CASE WHEN a.assigned_for = 1 THEN "user" WHEN a.assigned_for = 2 THEN "place" WHEN a.assigned_for = 3 THEN "device" END) LIKE "%%%1$s%%"
                    OR ( CASE WHEN a.assigned_for = 1 THEN CONCAT_WS(" ", au.first_name, au.last_name, "-", au.username) WHEN a.assigned_for = 2 THEN ap.place WHEN a.assigned_for = 3 THEN aa.asset_tag ELSE "-" END) LIKE "%%%1$s%%"', $search_key);
                } else {
                    $whereStr = sprintf('DATE_FORMAT(a.created_at, "%%d %%b %%Y") LIKE "%%%1$s%%" 
                    OR DATE_FORMAT(a.created_at, "%%h:%%i %%p") LIKE "%%%1$s%%"
                    OR a.note LIKE "%%%1$s%%" OR loc.name LIKE "%%%1$s%%" OR a.action_type LIKE "%%%1$s%%"
                    OR cat.name LIKE "%%%1$s%%" OR place_loc.place LIKE "%%%1$s%%" 
                    OR cons.unique_tag LIKE "%%%1$s%%" OR loc.branch_code LIKE "%%%1$s%%" 
                    OR cons.name LIKE "%%%1$s%%" OR CONCAT("CNS", cons.id) LIKE "%%%1$s%%" 
                    OR CONCAT(admin.first_name, " ", admin.last_name) LIKE "%%%1$s%%"
                    OR ( CASE WHEN a.assigned_for = 1 THEN "user" WHEN a.assigned_for = 2 THEN "place" WHEN a.assigned_for = 3 THEN "device" END) LIKE "%%%1$s%%"
                    OR ( CASE WHEN a.assigned_for = 1 THEN CONCAT_WS(" ", au.first_name, au.last_name, "-", au.username) WHEN a.assigned_for = 2 THEN ap.place WHEN a.assigned_for = 3 THEN aa.asset_tag ELSE "-" END) LIKE "%%%1$s%%"', $search_key);
                }
                $db->where(function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                });
                $return['recordsFiltered'] = $db->count();
            }
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
            $d->note = mb_strimwidth(strip_tags($d->note), 0, 100, '...');
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    public function downloadConsumableActivityReport(Request $request) {
       $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
       if(! Auth::user()->hasPermissionTo('ConsumableReportDownload') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
       }
       $now = new Carbon(config('app.timezone'));
       $db = DB::table('asset_logs as a')
        ->where('a.asset_type', 'Consumable')
        ->whereIn('a.action_type', ['checkout', 'New Add']);
        $db->leftJoin('consumables as cons', function($q) {
            $q->on('cons.id', '=', 'a.consumable_id');
        });
        $db->leftJoin('users as admin', 'admin.id', '=', 'a.user_id');
        $db->leftJoin('categories as cat', function($q) {
            $q->on('cat.id', '=', 'cons.category_id');
            $q->where('cons.category_id', '<>', 0);
        });
        $db->leftJoin('locations as loc', function($q) {
            $q->on('cons.location_id', '=', 'loc.id');
        });
     
        $db->leftJoin('places as place_loc', function($q) {
            $q->on('cons.internal_place_id', '=', 'place_loc.id');
        });
        $db->leftJoin('users as user', function ($join) {
            $join->on('user.id', '=', 'a.checkedout_to')->where('a.assigned_for', '=', 1);
        });
        $db->leftJoin('places as ap', function ($join) {
            $join->on('ap.id', '=', 'a.checkedout_to')->where('a.assigned_for', '=', 2);
        });
        $db->leftJoin('assets as aa', function ($join) {
            $join->on('aa.id', '=', 'a.checkedout_to')->where('a.assigned_for', '=', 3);
        });
       $db->where('a.asset_type', 'Consumable');
       $db->whereIn('a.action_type', ['checkout', 'New Add']);
       $db->whereNotNull('a.consumable_id');

        $db->select('cons.id as consumable_id','a.id', 'a.action_type','a.note','cat.name as category_name','cons.unique_tag','loc.name as location_name','place_loc.place','loc.branch_code','cons.name','a.action_type as asset_type_text');
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when cons.id is not null then concat_ws("","CN",cons.id) else "" end as con_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when cons.id is not null then concat_ws("","CNS",cons.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('concat(admin.first_name, " ", admin.last_name) as assigned_by'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.created_at, "%d %b %Y %h:%i %p") as created_at_on'));
        $db->addSelect([
            DB::raw('DATE_FORMAT(a.created_at, "%d %b %Y") as created_date'),
            DB::raw('DATE_FORMAT(a.created_at, "%h:%i %p") as created_time'),
        ]);
        $db->addSelect(DB::raw('CASE WHEN a.assigned_for = 1 THEN "User" WHEN a.assigned_for = 2 THEN "Place" WHEN a.assigned_for = 3 THEN "Device" ELSE "-" END as assigned_for'));
        $db->addSelect(DB::raw('CASE WHEN a.assigned_for = 1 THEN CONCAT_WS(" ", user.first_name, user.last_name, "-", user.username) WHEN a.assigned_for = 2 THEN ap.place WHEN a.assigned_for = 3 THEN aa.asset_tag ELSE "-" END as assigned_to'));

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("cons.company_id", $companyIds);

        $loc_previllage = Auth::user()->permitted_locations;
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where(function ($query) {
                    $query->orWhere('cons.location_id', '=', 0);
                });
            } else {
                $db->where(function ($query) use($permitted_loc) {
                    $query->orWhereIn('cons.location_id', $permitted_loc);
                });
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where(function ($query) {
                    $query->orWhere('cons.department_id', '=', 0);
                });
            } else {
                $db->where(function ($query) use($asset_depts) {
                    $query->orWhereIn('cons.department_id', $asset_depts);
                });
            }
        }
        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if(isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if (isset($req["search"])) {$search_key = trim($req["search"]);
                if (!empty($search_key)) {
                    if (config("app.client") == "etherealmachines") {
                        $whereStr = sprintf('DATE_FORMAT(a.created_at, "%%d %%b %%Y") LIKE "%%%1$s%%"
                        OR DATE_FORMAT(a.created_at, "%%h:%%i %%p") LIKE "%%%1$s%%"
                        OR a.note LIKE "%%%1$s%%" OR loc.name LIKE "%%%1$s%%" OR a.action_type LIKE "%%%1$s%%"
                        OR cat.name LIKE "%%%1$s%%" OR place_loc.place LIKE "%%%1$s%%"
                        OR cons.unique_tag LIKE "%%%1$s%%" OR loc.branch_code LIKE "%%%1$s%%"
                        OR cons.name LIKE "%%%1$s%%" OR CONCAT("CN", cons.id) LIKE "%%%1$s%%" 
                        OR CONCAT(admin.first_name, " ", admin.last_name) LIKE "%%%1$s%%"
                        OR ( CASE WHEN a.assigned_for = 1 THEN "user" WHEN a.assigned_for = 2 THEN "place" WHEN a.assigned_for = 3 THEN "device" END) LIKE "%%%1$s%%"
                        OR ( CASE WHEN a.assigned_for = 1 THEN CONCAT_WS(" ", user.first_name, user.last_name, "-", user.username) WHEN a.assigned_for = 2 THEN ap.place WHEN a.assigned_for = 3 THEN aa.asset_tag ELSE "-" END) LIKE "%%%1$s%%"', $search_key);
                    } else {
                        $whereStr = sprintf('DATE_FORMAT(a.created_at, "%%d %%b %%Y") LIKE "%%%1$s%%"
                        OR DATE_FORMAT(a.created_at, "%%h:%%i %%p") LIKE "%%%1$s%%"
                        OR a.note LIKE "%%%1$s%%" OR loc.name LIKE "%%%1$s%%" OR a.action_type LIKE "%%%1$s%%"
                        OR cat.name LIKE "%%%1$s%%" OR place_loc.place LIKE "%%%1$s%%"
                        OR cons.unique_tag LIKE "%%%1$s%%" OR loc.branch_code LIKE "%%%1$s%%"
                        OR cons.name LIKE "%%%1$s%%" OR CONCAT("CNS", cons.id) LIKE "%%%1$s%%" 
                        OR CONCAT(admin.first_name, " ", admin.last_name) LIKE "%%%1$s%%"
                        OR ( CASE WHEN a.assigned_for = 1 THEN "user" WHEN a.assigned_for = 2 THEN "place" WHEN a.assigned_for = 3 THEN "device" END) LIKE "%%%1$s%%"
                        OR ( CASE WHEN a.assigned_for = 1 THEN CONCAT_WS(" ", user.first_name, user.last_name, "-", user.username) WHEN a.assigned_for = 2 THEN ap.place WHEN a.assigned_for = 3 THEN aa.asset_tag ELSE "-" END) LIKE "%%%1$s%%"', $search_key);
                    }
                    $db->where(function ($q) use ($whereStr) {
                        $q->whereRaw($whereStr);
                    });
                }
            }
        }
        $data = $db->get();
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'batch_code'      => $item->con_batch_no,
                'tag'             => $item->unique_tag,
                'action'          => $item->action_type,
                'name'            => $item->name,
                'category'        => $item->category_name,
                'location'        => $item->location_name,
                'location_code'   => $item->branch_code,
                'inernal_place'   => $item->place,
                'assigned_by'     => $item->assigned_by,
                'assigned_for'     => $item->assigned_for,
                'assigned_to'     => $item->assigned_to,
                'date'            => $item->created_date,
                'time'            => $item->created_time,

            ];
        }
        return Excel::download(new ConsumableActivityReport($result), 'ConsumableActivityReport' . $now->format('dmY') . '.xlsx');
    }

    public function individualMaterial(Request $request){
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('IndividualMaterialRead') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $sort_fields = [
            ['id'=>'1', 'text' => 'Username'],
            ['id'=>'2', 'text' => 'PS No.'],
            ['id'=>'3', 'text' => 'Department'],
            ['id'=>'4', 'text' => 'Cost Code'],
            ['id'=>'5', 'text' => 'Item Name'],
            ['id'=>'6', 'text' => 'Issue Date'],
            ['id'=>'7', 'text' => 'Location'],
        ];
        return view('reports.consumable.individual_material', compact('sort_fields'));
    }

    public function ajaxIndividualMaterial(Request $request){
        if(! Auth::user()->hasPermissionTo('IndividualMaterialRead') || !config("services.assets.enabled")) {
            $return['msg'] = trans('consumables.controller.Permission_denied');
            $return['status'] = "Success";
            $return["page"] = $page;
            return response()->json($return);
        }
        $return = [];
        $req = $request->All();
        try {
            $fields = [
                '1'=>'u.username',
                '2'=>'u.employee_num',
                '3'=>'c.department_id',
                '4'=>'ud.baseCostCode',
                '5'=>'c.name',
                '6'=>'cu.created_at',
                '7' => 'loc.name',
            ];
            // strict mode
            config()->set('database.connections.mysql.strict', false);
            DB::reconnect();

            $db = DB::table('consumables_users as cu')
                ->select('c.id','c.name as name','u.username','u.employee_num','dep.name as dep_name','c.qty',DB::raw('COUNT(cu.id) as assigned_count'),'loc.name as loc_name','tkt_custom.field_values',
                    DB::raw('DATE_FORMAT(cu.created_at, "%d %b %Y %h:%i %p") as created_at_format'),
                    DB::raw('ud.baseCostCode'),
                    DB::raw('DATE_FORMAT(cu.created_at, "%Y-%m-%d %H:%i") as group_minute')
                )
                ->addSelect(DB::raw('CONCAT(u.first_name, " ", u.last_name) as full_name'))
                ->leftJoin('consumables as c', 'c.id', 'cu.consumable_id')
                ->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id')
                // ->leftJoin('tkt_tickets as tkt', 'tkt.department_id', '=', 'c.department_id')
                ->leftJoin('departments as dep', 'dep.id', '=', 'c.department_id')
                ->leftJoin('users as u', 'u.id', '=', 'cu.assigned_to')
                ->leftJoin('user_details as ud', 'u.id', 'ud.user_id')
                ->leftJoin('tkt_requested_custom_form as tkt_custom', 'tkt_custom.request_id', '=', 'cu.ticket_id')
                ->whereNotNull('u.username')
                ->where('cu.assigned_for', 1)
                ->whereNull('c.deleted_at');

            // Apply department permission
            $settings = Settings::getSettings();
            if ($settings->department_config == 1) {
                $asset_dept_permission = Auth::user()->asset_departments_id;
                $asset_depts = explode(",", $asset_dept_permission);
                if (empty($asset_dept_permission)) {
                    $db->where('c.department_id', '=', 0);
                } else {
                    $db->whereIn('c.department_id', $asset_depts);
                }
            }

            $db->groupBy('c.id','c.name','u.username','u.employee_num','dep.name','c.qty','ud.baseCostCode','loc.name',DB::raw('DATE_FORMAT(cu.created_at, "%Y-%m-%d %H:%i")'));
            $grouped = clone $db;
            $return['filtered'] = $grouped->get()->count();
            $return['total'] = $return['filtered'];
            if(isset($req['filters']) && !empty($req['filters'])){
                if(isset($req['filters']['department'])){
                    $db->where('c.department_id', $req['filters']['department']);
                }
                if(isset($req['filters']['location'])){
                    $db->where('c.location_id', $req['filters']['location']);
                }
                if(isset($req['filters']['category'])){
                    $db->where('c.category_id', $req['filters']['category']);
                }
                if(isset($req['filters']['assigned_to'])){
                    $db->where('cu.assigned_to', $req['filters']['assigned_to']);
                }

                if(isset($req['filters']['based_on']) && isset($req['filters']["dateRange"]) && $req['filters']["dateRange"] && $req['filters']["dateRange"] != "null") {
                    $based_on_possible = ['1' => 'cu.created_at'];
                    $daterange = explode(" - ", $req["filters"]["dateRange"]);
                    $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                    $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                    if($from_date && $to_date) {
                        $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$req['filters']['based_on']], $from_date, $to_date);
                        $db->whereRaw($whereStr);
                    }
                }
                $return['filtered'] = count($db->get());
            }
            if (isset($req["search"])) {$search_key = trim($req["search"]);
                if (!empty($search_key)) {
                    $whereStr = sprintf('DATE_FORMAT(cu.created_at, "%%d %%b %%Y") LIKE "%%%1$s%%"
                    OR u.first_name LIKE "%%%1$s%%" OR dep.name LIKE "%%%1$s%%" OR cu.assigned_to LIKE "%%%1$s%%"
                    OR u.username LIKE "%%%1$s%%" OR u.employee_num LIKE "%%%1$s%%" OR ud.baseCostCode LIKE "%%%1$s%%"
                    OR concat_ws(" ", u.first_name, u.last_name) like "%%%1$s%%"
                    OR loc.name LIKE "%%%1$s%%"
                    OR c.name LIKE "%%%1$s%%" OR ud.user_id LIKE "%%%1$s%%"', $search_key);
                    $db->where(function ($q) use ($whereStr) {
                        $q->whereRaw($whereStr);
                    });
                    $return['filtered'] = count($db->get());
                }
            }
            if( isset($req["order"]["id"]) && isset($fields[$req["order"]["id"]]) && in_array($req["order"]["dir"], [1,2]) ) {
                $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
                $db->orderBy($fields[$req["order"]["id"]], $dir);
            }
            // Pagination
            $page = $request->input("page", 1);
            $take = $request->input("size", 10);
            $skip = ($page - 1) * $take;
            if ($return["filtered"] < $skip) {
                $page = 1;
                $skip = 0;
            }
            $db->skip($skip)->take($take);
            $return['data'] = $db->get();
            foreach ($return['data'] as $key => $value) {
                $value->internal_location = json_decode($value->field_values, true)['internal_location'] ?? null;
            }
            Config()->set('database.connections.mysql.strict', true);
            DB::reconnect();
            $return['msg'] = "Fetch data";
            $return['status'] = "Success";
            $return["page"] = $page;
            return response()->json($return);

        } catch (\Exception $ex) {
            Log::error("ajaxIndividualMaterial: " . $ex->getMessage());
            Config()->set('database.connections.mysql.strict', true);
            DB::reconnect();
            return response()->json($return);
        }
    }

    public function exportIndividualMaterial(Request $request){
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('IndividualMaterialDownload') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $return = [];
        $req = $request->All();

        $fields = [
            '1'=>'u.username',
            '2'=>'u.employee_num',
            '3'=>'c.department_id',
            '4'=>'ud.baseCostCode',
            '5'=>'c.name',
            '6'=>'cu.created_at',
            '7' => 'loc.name',
        ];
        // strict mode
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        $db = DB::table('consumables_users as cu')
            ->select('c.id','c.name as name','u.username','u.employee_num','dep.name as dep_name','c.qty',DB::raw('COUNT(cu.id) as assigned_count'),'loc.name as loc_name','tkt_custom.field_values',
                DB::raw('DATE_FORMAT(cu.created_at, "%d %b %Y %h:%i %p") as created_at_format'),
                DB::raw('ud.baseCostCode'),
                DB::raw('DATE_FORMAT(cu.created_at, "%Y-%m-%d %H:%i") as group_minute')
            )
            ->addSelect(DB::raw('CONCAT(u.first_name, " ", u.last_name) as full_name'))
            ->leftJoin('consumables as c', 'c.id', 'cu.consumable_id')
            ->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id')
            // ->leftJoin('tkt_tickets as tkt', 'tkt.department_id', '=', 'c.department_id')
            ->leftJoin('departments as dep', 'dep.id', '=', 'c.department_id')
            ->leftJoin('users as u', 'u.id', '=', 'cu.assigned_to')
            ->leftJoin('user_details as ud', 'u.id', 'ud.user_id')
            ->leftJoin('tkt_requested_custom_form as tkt_custom', 'tkt_custom.request_id', '=', 'cu.ticket_id')
            ->whereNotNull('u.username')
            ->where('cu.assigned_for', 1)
            ->whereNull('c.deleted_at');

        // Apply department permission
        $settings = Settings::getSettings();
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id;
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }

        $db->groupBy('c.id','c.name','u.username','u.employee_num','dep.name','c.qty','ud.baseCostCode','loc.name',DB::raw('DATE_FORMAT(cu.created_at, "%Y-%m-%d %H:%i")'));
        $grouped = clone $db;


        if($request->q) {
            $request_filters = base64_decode($request->q);
            $req = json_decode($request_filters, true);
            if(isset($req['other_filters']['department'])){
                $db->where('c.department_id', $req['other_filters']['department']);
            }
            if(isset($req['other_filters']['location'])){
                $db->where('c.location_id', $req['other_filters']['location']);
            }
            if(isset($req['other_filters']['category'])){
                $db->where('c.category_id', $req['other_filters']['category']);
            }
            if(isset($req['other_filters']['assigned_to'])){
                $db->where('cu.assigned_to', $req['other_filters']['assigned_to']);
            }

            if(isset($req['other_filters']['based_on']) && isset($req['other_filters']["dateRange"]) && $req['other_filters']["dateRange"] && $req['other_filters']["dateRange"] != "null") {
                $based_on_possible = ['1' => 'cu.created_at'];
                $daterange = explode(" - ", $req["other_filters"]["dateRange"]);
                $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                if($from_date && $to_date) {
                    $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$req['other_filters']['based_on']], $from_date, $to_date);
                    $db->whereRaw($whereStr);
                }
            }
            if (isset($req["search"])) {
                $search_key = trim($req["search"]);
                if (!empty($search_key)) {
                    $whereStr = sprintf('DATE_FORMAT(cu.created_at, "%%d %%b %%Y") LIKE "%%%1$s%%"
                    OR u.first_name LIKE "%%%1$s%%" OR dep.name LIKE "%%%1$s%%" OR cu.assigned_to LIKE "%%%1$s%%"
                    OR u.username LIKE "%%%1$s%%" OR u.employee_num LIKE "%%%1$s%%" OR ud.baseCostCode LIKE "%%%1$s%%"
                    OR concat_ws(" ", u.first_name, u.last_name) like "%%%1$s%%"
                    OR loc.name LIKE "%%%1$s%%"
                    OR c.name LIKE "%%%1$s%%" OR ud.user_id LIKE "%%%1$s%%"', $search_key);
                    $db->where(function ($q) use ($whereStr) {
                        $q->whereRaw($whereStr);
                    });
                }
            }
            $return['filtered'] = count($db->get());
        }
        if( isset($req["order"]["id"]) && isset($fields[$req["order"]["id"]]) && in_array($req["order"]["dir"], [1,2]) ) {
            $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
            $db->orderBy($fields[$req["order"]["id"]], $dir);
        }

        $return['data'] = $db->get();
        Config()->set('database.connections.mysql.strict', true);
        DB::reconnect();
        $data = [];
        foreach($return['data'] as $record){
            $data[] = [
                $record->full_name,
                $record->employee_num,
                $record->dep_name,
                $record->loc_name,
                json_decode($record->field_values, true)['internal_location'] ?? null,
                $record->baseCostCode,
                $record->name,
                $record->created_at_format,
                $record->assigned_count,
            ];
        }
        $result = json_decode(json_encode($data, true),true);
        return Excel::download(new IndividualMaterialExport($result), 'individual-material.xlsx');

    }

    public function itemThresholdLevel(Request $request){
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('IndividualMaterialRead') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $sort_fields = [
            ['id'=>'1', 'text' => 'Item Name'],
            ['id'=>'2', 'text' => 'Location'],
            ['id'=>'3', 'text' => 'Category'],
            ['id'=>'4', 'text' => 'Item Code'],
            ['id'=>'5', 'text' => 'Threshold'],
        ];
        $department = Department::select('id', 'name as text')->orderBy('name')->get();
        return view('reports.consumable.item_threshold_level', compact('sort_fields', 'department'));
    }

    public function ajaxItemThresholdLevel(Request $request){

        $req = $request->all();
        $fields = [
            '1'=>'a.name',
            '2'=>'loc.name',
            '3'=>'cat.name',
            '4'=>'a.unique_tag',
            '5'=>'a.consumable_thresholds',
        ];
        $return = [];
        $db = DB::table('consumables as a');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('departments as dep','dep.id', '=','a.department_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->select('a.id', 'a.name',  'loc.name as loc_name', 'cat.name as cat_name', 'a.qty', 'a.consumable_thresholds','a.unique_tag');
        $db->whereNull('a.deleted_at');
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("c.company_id", $companyIds);

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
        $return['filtered'] = $db->count();
        $return['total'] = $return['filtered'];
        if(isset($req['filters']) && !empty($req['filters'])){

            if(isset($req['filters']['location'])){
                $db->where('a.location_id', $req['filters']['location']);
            }
            if(isset($req['filters']['department'])) {
                $db->where('a.department_id', $req['filters']['department']);
            }
            if(isset($req['filters']['category'])){
                $db->where('a.category_id', $req['filters']['category']);
            }
            $return['filtered'] = count($db->get());
        }
        if (isset($req["search"])) {$search_key = trim($req["search"]);
            if (!empty($search_key)) {
                $whereStr = sprintf('a.name LIKE "%%%1$s%%" OR loc.name LIKE "%%%1$s%%" OR cat.name LIKE "%%%1$s%%"
                OR a.unique_tag LIKE "%%%1$s%%" OR a.consumable_thresholds LIKE "%%%1$s%%"', $search_key);
                $db->where(function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                });
                $return['filtered'] = count($db->get());
            }
        }
        if( isset($req["order"]["id"]) && isset($fields[$req["order"]["id"]]) && in_array($req["order"]["dir"], [1,2]) ) {
            $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
            $db->orderBy($fields[$req["order"]["id"]], $dir);
        }
        // Pagination
        $page = $request->input("page", 1);
        $take = $request->input("size", 10);
        $skip = ($page - 1) * $take;
        if ($return["filtered"] < $skip) {
            $page = 1;
            $skip = 0;
        }
        $db->skip($skip)->take($take);
        $return['data'] = $db->get();
        foreach($return['data'] as $key=>$data){
            $consumable = Consumable::where('id','=',$data->id)->withCount(['users', 'device','places'])->with('category')->first();
            $totalAssigned = $consumable->users_count + $consumable->places_count + $consumable->device_count;
            $return['data'][$key]->current_stock = $data->qty - $totalAssigned;
        }
        $return['msg'] = "Fetch data";
        $return['status'] = "Success";
        $return["page"] = $page;
        return response()->json($return);
    }

    public function exportItemThresholdLevel(Request $request){
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(!config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $req = $request->all();
        $return = [];
        $fields = [
            '1'=>'a.name',
            '2'=>'loc.name',
            '3'=>'cat.name',
            '4'=>'a.unique_tag',
            '5'=>'a.consumable_thresholds',
        ];
        $db = DB::table('consumables as a');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->select('a.id', 'a.name',  'loc.name as loc_name', 'cat.name as cat_name', 'a.qty', 'a.consumable_thresholds','a.unique_tag');
        $db->whereNull('a.deleted_at');
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("c.company_id", $companyIds);

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
            $req = json_decode($request_filters, true);

            if(isset($req['other_filters']['location'])) {
                $db->where('a.location_id', $req['other_filters']['location']);
            }
            if(isset($req['other_filters']['department'])) {
                $db->where('a.department_id', $req['other_filters']['department']);
            }
            if(isset($req['other_filters']['category'])) {
                $db->where('a.category_id', $req['other_filters']['category']);
            }
            $return['filtered'] = count($db->get());
        }
        if (isset($req["search"])) {$search_key = trim($req["search"]);
            if (!empty($search_key)) {
                $whereStr = sprintf('a.name LIKE "%%%1$s%%" OR loc.name LIKE "%%%1$s%%" OR cat.name LIKE "%%%1$s%%"
                 OR a.unique_tag LIKE "%%%1$s%%" OR a.consumable_thresholds LIKE "%%%1$s%%"', $search_key);
                $db->where(function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                });
                $return['filtered'] = count($db->get());
            }
        }
        if( isset($req["order"]["id"]) && isset($fields[$req["order"]["id"]]) && in_array($req["order"]["dir"], [1,2]) ) {
            $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
            $db->orderBy($fields[$req["order"]["id"]], $dir);
        }
        $return['data'] = $db->get();
        $record = [];
        foreach($return['data'] as $key=>$data){
            $consumable = Consumable::where('id','=',$data->id)->withCount(['users', 'device','places'])->with('category')->first();
            $totalAssigned = $consumable->users_count + $consumable->places_count + $consumable->device_count;
            $current_stock = $data->qty - $totalAssigned;
            $record[] = [
                $data->name,
                $data->loc_name,
                $data->cat_name,
                $data->unique_tag,
                $data->consumable_thresholds,
                $current_stock
            ];
        }
        $result = json_decode(json_encode($record, true),true);
        return Excel::download(new ThresholdLevel($result), 'Item-Threshold-level.xlsx');

    }

    public function monthWiseConsumable(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('MonthWiseConsumableRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $sort_fields = [
            ['id'=>'1', 'text' => 'Item Name'],
            ['id'=>'2', 'text' => 'Location'],
            ['id'=>'3', 'text' => 'Category'],
            // ['id'=>'4', 'text' => 'Month Opening Stock'],
            // ['id'=>'5', 'text' => 'Release during the month'],
            // ['id'=>'6', 'text' => 'New Stock in the month'],
            // ['id'=>'7', 'text' => 'Closing Stock'],
            ['id'=>'8', 'text' => 'Threshold'],
        ];
        $department = Department::select('id', 'name as text')->orderBy('name')->get();
        $current_year = date("Y");
        $current_month = date("n");
        return view('reports.consumable.index', compact('sort_fields', 'current_year', 'current_month','department'));
    }

    public function ajaxMonthWiseConsumable(Request $request){
        $return = ['status' => 'danger', 'msg' => 'Unable to load report'];

        $fields = [
            '1'=>'c.name',
            '2'=>'loc.name',
            '3'=>'cat.name',
            '4'=>'',
            '5'=>'',
            '6'=>'',
            '7'=>'',
            '8'=>'c.consumable_thresholds',
        ];
        $req = $request->all();
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();
        $year  = $request->input('filters.filter_by_year', now()->year);
        $month = $request->input('filters.filter_by_month', now()->month);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate   = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        $prevMonthEndDate = Carbon::createFromDate($year, $month, 1)->subMonth()->endOfMonth()->toDateString();
        // $dates = collect(Carbon::parse($startDate)->daysUntil($endDate))->map->day->toArray();
        $db = DB::table('consumables as c');
        // $db->leftJoin('departments as dep','dep.id', '=', 'c.department_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');
        // $db->leftJoin('consumables_users as cu', 'c.id', '=', 'cu.consumable_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');
        $db->select('c.id', 'c.name as desc', 'cat.name as cat_name', 'loc.name as loc_name', 'c.qty','c.consumable_thresholds as threshold', 'c.unique_tag');
        $db->whereNull('c.deleted_at');
        // $db->where('cu.assigned_for', 1);

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("c.company_id", $companyIds);
        // $db->whereBetween('cu.created_at', [$startDate, $endDate]);

        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }
        $db->groupBy('c.id');
        $return['total'] = count($db->get());
        $return['filtered'] = $return['total'];

        if(isset($req['filters']) && !empty($req['filters'])){
            if(isset($req['filters']['location'])){
                $db->where('c.location_id', $req['filters']['location']);
            }
            if(isset($req['filters']['department'])){
                $db->where('c.department_id', $req['filters']['department']);
            }
            if(isset($req['filters']['category'])){
                $db->where('c.category_id', $req['filters']['category']);
            }
            $return['filtered'] = count($db->get());
        }
        if (isset($req["search"])) {
            $search_key = trim($req["search"]);
            if(!empty($search_key)) {
                $whereStr = sprintf('c.consumable_thresholds LIKE "%%%1$s%%"
                OR loc.name LIKE "%%%1$s%%" OR cat.name LIKE "%%%1$s%%"
                OR c.name LIKE "%%%1$s%%"', $search_key);
                $db->where(function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                });
                $return['filtered'] = count($db->get());
            }
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

        $return["data"] = $db->get();
        $return["page"] = $page;
        $openingStock = (clone $db)
            ->leftJoin('consumables_users as cu', function ($join) use ($startDate, $endDate) {
                $join->on('c.id', '=', 'cu.consumable_id')
                    ->whereBetween('cu.created_at', [$startDate, $endDate]);
            })
            ->select('c.name', 'c.qty','c.id', DB::raw('COUNT(cu.id) as opening'))->groupBy('c.name', 'c.qty')->get()->mapWithKeys(function ($item) {
                return [$item->name => [
                    'qty' => $item->qty,
                    'opening' => $item->opening,
                ]];
            })->toArray();
        config()->set('database.connections.mysql.strict', true);
        DB::reconnect();
        foreach ($return["data"] as $key => $output) {
            $purchaseCount = ConsumablePurchase::select(DB::raw('SUM(qty) as purchase'))->where('batch_no', $output->id)->whereBetween('created_at', [$startDate, $endDate])->value('purchase');
            $allpurchaseCountQtn = ConsumablePurchase::select(DB::raw('SUM(qty) as purchase'))->where('batch_no', $output->id)->whereDate('created_at',  '<', $prevMonthEndDate)->value('purchase');
            // $allocated_qty = Consumable::where('id', $output->id)->withCount(['users', 'device', 'places'])->value('users_count');
            $allocated_qty = Consumable::where('id', $output->id)->withCount([
                'users' => fn($q) => $q->whereDate('consumables_users.created_at', '<', $prevMonthEndDate),
                // 'device as device_count' => fn($q) => $q->whereDate('consumables_users.created_at', '<', $endDate),
                // 'places as places_count' => fn($q) => $q->whereDate('consumables_users.created_at', '<', $endDate),
            ])->value('users_count');
            $totalRelease = ConsumableUser::where('consumable_id', $output->id)->whereBetween('created_at', [$startDate, $endDate])->count();
            $totalReleasePreMonth = ConsumableUser::where('consumable_id', $output->id)->whereDate('created_at', '<', $prevMonthEndDate)->count();
            $closing_stock = ($allpurchaseCountQtn ?? 0) - ($totalReleasePreMonth ?? 0 + $purchaseCount ?? 0);
            $return["data"][$key]->closing_stock = ($closing_stock + $purchaseCount) - ($totalRelease ?? 0);
            $return["data"][$key]->opening_stock = $closing_stock;
            $return["data"][$key]->new_stock = $purchaseCount;
            $return["data"][$key]->total_release = $totalRelease ?? 0;
        }
        return response()->json($return);

    }

    public function exportMonthWiseConsumable(Request $request){
        $return = ['status' => 'danger', 'msg' => 'Unable to load report'];
        if(! Auth::user()->hasPermissionTo('MonthWiseConsumableDownload')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $fields = [
            '1'=>'c.name',
            '2'=>'loc.name',
            '3'=>'cat.name',
            '4'=>'',
            '5'=>'',
            '6'=>'',
            '7'=>'',
            '8'=>'c.consumable_thresholds',
        ];
        $req = $request->all();
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();
        if($request->q) {
            $request_filters = base64_decode($request->q);
            $req = json_decode($request_filters, true);
            $year = $req['other_filters']['filter_by_year'] ?? date('Y');
            $month = $req['other_filters']['filter_by_month'] ?? date('n');
            $monthName = date("F", mktime(0, 0, 0, $month, 1));
            $startDate = date('Y-m-01', strtotime("$year-$month-01"));
            $endDate = date('Y-m-t', strtotime("$year-$month-01"));
            $prevMonthEndDate = Carbon::createFromDate($year, $month, 1)->subMonth()->endOfMonth()->toDateString();
        }
        // $dates = collect(Carbon::parse($startDate)->daysUntil($endDate))->map->day->toArray();
        $db = DB::table('consumables as c');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'c.location_id');
        // $db->leftJoin('departments as dep','dep.id', '=', 'c.department_id');
        // $db->leftJoin('consumables_users as cu', 'c.id', '=', 'cu.consumable_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'c.category_id');
        $db->select('c.id', 'c.name as desc','loc.name as loc_name', 'cat.name as cat_name', 'c.qty','c.consumable_thresholds as threshold', 'c.unique_tag');
        $db->whereNull('c.deleted_at');
        // $db->where('cu.assigned_for', 1);

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("c.company_id", $companyIds);
        // $db->whereBetween('cu.created_at', [$startDate, $endDate]);


        // check asset location permission
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('c.location_id', '=', 0);
            } else {
                $db->whereIn('c.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('c.department_id', '=', 0);
            } else {
                $db->whereIn('c.department_id', $asset_depts);
            }
        }
        $db->groupBy('c.id');
        if($request->q) {
            if(isset($req['other_filters']) && !empty($req['other_filters'])){
                if(isset($req['other_filters']['location'])){
                    $db->where('c.location_id', $req['other_filters']['location']);
                }
                if(isset($req['other_filters']['department'])){
                    $db->where('c.department_id', $req['other_filters']['department']);
                }
                if(isset($req['other_filters']['category'])){
                    $db->where('c.category_id', $req['other_filters']['category']);
                }
            }
            if (isset($req["search"])) {
                $search_key = trim($req["search"]);
                if(!empty($search_key)) {
                    $whereStr = sprintf('c.consumable_thresholds LIKE "%%%1$s%%"
                    OR loc.name LIKE "%%%1$s%%" OR cat.name LIKE "%%%1$s%%"
                    OR c.name LIKE "%%%1$s%%"', $search_key);
                    $db->where(function ($q) use ($whereStr) {
                        $q->whereRaw($whereStr);
                    });
                }
            }
        }

        if( isset($req["order"]["id"]) && isset($fields[$req["order"]["id"]]) && in_array($req["order"]["dir"], [1,2]) ) {
            $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
            $db->orderBy($fields[$req["order"]["id"]], $dir);
        }

        $return["data"] = $db->get();
        $openingStock = (clone $db)
            ->leftJoin('consumables_users as cu', function ($join) use ($startDate, $endDate) {
                $join->on('c.id', '=', 'cu.consumable_id')
                    ->whereBetween('cu.created_at', [$startDate, $endDate]);
            })
            ->select('c.name', 'c.qty','c.id', DB::raw('COUNT(cu.id) as opening'))->groupBy('c.name', 'c.qty')->get()->mapWithKeys(function ($item) {
                return [$item->name => [
                    'qty' => $item->qty,
                    'opening' => $item->opening,
                ]];
            })->toArray();
        config()->set('database.connections.mysql.strict', true);
        DB::reconnect();
        foreach ($return["data"] as $key => $output) {
            $purchaseCount = ConsumablePurchase::select(DB::raw('SUM(qty) as purchase'))->where('batch_no', $output->id)->whereBetween('created_at', [$startDate, $endDate])->value('purchase');
            $allpurchaseCountQtn = ConsumablePurchase::select(DB::raw('SUM(qty) as purchase'))->where('batch_no', $output->id)->whereDate('created_at',  '<', $prevMonthEndDate)->value('purchase');
            // $allocated_qty = Consumable::where('id', $output->id)->withCount(['users', 'device', 'places'])->value('users_count');
            $allocated_qty = Consumable::where('id', $output->id)->withCount([
                'users' => fn($q) => $q->whereDate('consumables_users.created_at', '<', $prevMonthEndDate),
                // 'device as device_count' => fn($q) => $q->whereDate('consumables_users.created_at', '<', $endDate),
                // 'places as places_count' => fn($q) => $q->whereDate('consumables_users.created_at', '<', $endDate),
            ])->value('users_count');
            $totalRelease = ConsumableUser::where('consumable_id', $output->id)->whereBetween('created_at', [$startDate, $endDate])->count();
            $totalReleasePreMonth = ConsumableUser::where('consumable_id', $output->id)->whereDate('created_at', '<', $prevMonthEndDate)->count();
            $closing_stock = ($allpurchaseCountQtn ?? 0) - ($totalReleasePreMonth ?? 0 + $purchaseCount ?? 0);
            $return["data"][$key]->closing_stock = ($closing_stock + $purchaseCount) - ($totalRelease ?? 0);
            $return["data"][$key]->opening_stock = $closing_stock;
            $return["data"][$key]->new_stock = $purchaseCount;
            $return["data"][$key]->total_release = $totalRelease ?? 0;
        }
        $data = [];
        foreach($return["data"] as $return){
            $data[] = [
                $return->loc_name,
                $return->cat_name,
                $return->desc,
                $return->unique_tag,
                $return->opening_stock,
                $return->total_release,
                $return->new_stock,
                $return->closing_stock,
                $return->threshold,
            ];
        }
        $result = json_decode(json_encode($data, true),true);
        $filename = "Month-Wise-{$monthName}-{$year}.xlsx";
        $month_year = "{$monthName}-{$year}";
        return Excel::download(new MonthWiseExport($result, $month_year), $filename);
    }

    public function dailyDistribution(Request $request){
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('DailyDistributionRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $sort_fields = [
            ['id'=>'1', 'text' => 'Item Name'],
            ['id'=>'2', 'text' => 'Location'],
            ['id'=>'3', 'text' => 'Category'],
            ['id'=>'4', 'text' => 'Item Code'],
            ['id'=>'5', 'text' => 'Threshold'],
        ];
        $current_year = date("Y");
        $current_month = date("n");
        $department = Department::select('id', 'name as text')->orderBy('name')->get();
        return view('reports.consumable.daily_distribution', compact('sort_fields','current_year' ,'current_month','department'));
    }

    public function ajaxDailyDistribution(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('DailyDistributionRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        try {
            config()->set('database.connections.mysql.strict', false);
            DB::reconnect();
            $year  = $request->input('filters.filter_by_year', now()->year);
            $month = $request->input('filters.filter_by_month', now()->month);

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
            $endDate   = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

            $dates = collect(Carbon::parse($startDate)->daysUntil($endDate))->map->day->toArray();

            // Auth + Settings
            $settings       = Settings::getSettings();
            $user           = Auth::user();
            $permittedLoc   = array_filter(explode(',', $user->permitted_locations));
            $permittedDepts = array_filter(explode(',', $user->asset_departments_id));

            // Apply base filters
            $applyFilters = function ($query) use ($settings, $user, $permittedLoc, $permittedDepts, $request) {
                if ($settings->location_config == 1) {
                    $query->whereIn('c.location_id', $permittedLoc ?: [0]);
                }
                if ($settings->department_config == 1) {
                    $query->whereIn('c.department_id', $permittedDepts ?: [0]);
                }
                if ($filters = $request->input('filters')) {
                    if (!empty($filters['location'])) {
                        $query->where('c.location_id', $filters['location']);
                    }
                    if (!empty($filters['department'])) {
                        $query->where('c.department_id', $filters['department']);
                    }
                    if (!empty($filters['category'])) {
                        $query->where('c.category_id', $filters['category']);
                    }
                    if (!empty($filters['assigned_to'])) {
                        $query->where('cu.user_id', $filters['assigned_to']);
                    }
                }
                if ($search = trim($request->input('search'))) {
                    if (config("app.client") == "etherealmachines") {
                        $whereStr = sprintf('((case when c.id is not null then concat_ws("","CN",c.id) else "" end) like "%%%1$s%%" or c.name like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or loc.name like "%%%1$s%%")', $search);
                    } else {
                        $whereStr = sprintf('((case when c.id is not null then concat_ws("","CNS",c.id) else "" end) like "%%%1$s%%" or c.name like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or loc.name like "%%%1$s%%")', $search);
                    }
                    $query->whereRaw($whereStr);
                }
            };

            // Base Query
            $baseQuery = DB::table('consumables_users as cu')->leftJoin('consumables as c', 'c.id', '=', 'cu.consumable_id')->leftJoin('locations as loc', 'c.location_id', 'loc.id')->where('cu.assigned_for', 1)->whereNull('c.deleted_at')->whereBetween('cu.created_at', [$startDate, $endDate]);
            $totalRecords = (clone $baseQuery)->select('c.name')->groupBy('c.id')->get()->count();
            $applyFilters($baseQuery);
            $filterRecords = (clone $baseQuery)->select('c.name')->groupBy('c.id')->get()->count();
            $page = (int) $request->input('page', 1);
            $take = (int) $request->input('size', 10);
            $skip = ($page - 1) * $take;
            $paginatedItems = (clone $baseQuery)->select('c.id')->groupBy('c.name','c.unique_tag','c.id','loc.name')->skip($skip)->take($take)->pluck('c.id')->toArray();
            $openingStock = (clone $baseQuery)
            ->select('c.name','c.qty','c.id','c.unique_tag', DB::raw('COUNT(cu.id) as opening'),'loc.name as loc_name')->groupBy('c.name','c.unique_tag','c.id','loc.name')->whereNull('c.deleted_at')->whereIn('c.id', $paginatedItems)->get()
                ->mapWithKeys(fn($item) => [
                $item->id => [
                    'qty'     => $item->qty,
                    'opening' => $item->opening,
                    'name'      => $item->name,
                    'loc_name' => $item->loc_name,
                    'unique_tag' => $item->unique_tag,
                    'id' => $item->id,
                    ]
                ])->toArray();
            $datewiseQuery = DB::table('consumables_users as cu')->leftJoin('consumables as c', 'c.id', '=', 'cu.consumable_id')->leftJoin('locations as loc', 'c.location_id', 'loc.id')->where('cu.assigned_for', 1)->whereNull('c.deleted_at')->whereBetween('cu.created_at', [$startDate, $endDate])->whereIn('c.id', $paginatedItems);

            $applyFilters($datewiseQuery);
            $datewise = $datewiseQuery->select('c.id',DB::raw('DATE_FORMAT(cu.created_at, "%e") as date'),DB::raw('COUNT(cu.id) as count'))
                ->groupBy('c.id', 'loc.name', DB::raw('DATE_FORMAT(cu.created_at, "%Y-%m-%d")'))->get();

            // Pivot Data
            $pivotData = [];
            foreach ($datewise as $row) {
                $pivotData[$row->id][$row->date] = $row->count;
            }
            foreach ($paginatedItems as $item) {
                if (!isset($pivotData[$item])) {
                    $pivotData[$item] = array_fill_keys($dates, 0);
                }
            }

            // Pre-fetch allocated quantities (avoid N+1)
            $allocatedCounts = Consumable::withCount(['users', 'device', 'places'])
                ->whereIn('id', array_column($openingStock, 'id'))
                ->get()
                ->mapWithKeys(fn($c) => [$c->id => ($c->users_count + $c->device_count + $c->places_count)])
                ->toArray();

            // Final result
            if(empty($paginatedItems)){
                $finalResult = [];
                $row = [
                    'con_id' => '',
                    'item_name' => '',
                    'unique_tag' => '',
                    'loc_name' => '',
                    'opening_stock' =>  0
                ];
                $totalDistributed = 0;
                foreach ($dates as $date) {
                    $daily = $dayData[$date] ?? 0;
                    $row['day_' . $date] = $daily;
                }
                $row['total_distribution'] = $totalDistributed;
                $row['new_purchase'] = 0;
                $row['closing_stock'] =  0;
                $row['opening_stock'] = 0;
                $finalResult[] = $row;
            } else {
                foreach ($paginatedItems as $key => $item) {
                $itemId         = $openingStock[$item]['id'] ?? null;
                $allocated_qty  = $allocatedCounts[$itemId] ?? 0;
                $dayData        = $pivotData[$item] ?? array_fill_keys($dates, 0);
                $totalDistributed = array_sum($dayData);
                $purchaseCount = ConsumablePurchase::where('batch_no', $itemId)->whereBetween('created_at', [$startDate, $endDate])->sum('qty');
                $finalResult[] = array_merge([
                    'item_name'      => $openingStock[$item]['name'] ?? null,
                    'unique_tag' => $openingStock[$item]['unique_tag'] ?? null,
                    'con_id' => $openingStock[$item]['id'] ?? null,
                    'loc_name' => $openingStock[$item]['loc_name'] ?? null,
                    'opening_stock'  => ($openingStock[$item]['qty'] ?? 0) - ($allocated_qty - $totalDistributed),
                ],
                    collect($dates)->mapWithKeys(fn($d) => ['day_' . $d => $dayData[$d] ?? 0])->toArray(),
                    [
                        'total_distribution' => $totalDistributed,
                        'new_purchase'       => $purchaseCount ?? 0,
                        'closing_stock'      => ($openingStock[$item]['qty'] ?? 0) - $allocated_qty + $purchaseCount ?? 0,
                    ]
                );
                }
            }
            config()->set('database.connections.mysql.strict', true);
            DB::reconnect();
            return response()->json([
                'data'     => $finalResult,
                'status'   => 'success',
                'msg'      => 'Fetched daily distribution',
                'total'    => $totalRecords,
                'filtered' => $filterRecords,
                'page'     => $page,
            ]);
        } catch (\Exception $ex) {
            config()->set('database.connections.mysql.strict', true);
            DB::reconnect();
            Log::error("ajaxDailyDistribution: " . $ex->getMessage());
            return response()->json($return);
        }

    }

    public function exportDailyDistribution(Request $request) {

        if(! Auth::user()->hasPermissionTo('DailyDistributionDownload')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $year = $request['filters']['filter_by_year'] ?? date('Y');
        $month = $request['filters']['filter_by_month'] ?? date('n');
        $startDate = date('Y-m-01', strtotime("$year-$month-01"));
        $endDate = date('Y-m-t', strtotime("$year-$month-01"));
        $req = $request->all();
        if($request->q) {
            $request_filters = base64_decode($request->q);
            $req = json_decode($request_filters, true);
            $year = $req['other_filters']['filter_by_year'] ?? date('Y');
            $month = $req['other_filters']['filter_by_month'] ?? date('n');
            $startDate = date('Y-m-01', strtotime("$year-$month-01"));
            $endDate = date('Y-m-t', strtotime("$year-$month-01"));
        }
        $monthName = date("F", mktime(0, 0, 0, $month, 1));
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        $dates = [];
        $start = new DateTime($startDate);
        $end   = new DateTime($endDate);
        while ($start <= $end) {
            $dates[] = $start->format('j');
            $start->modify('+1 day');
        }
        $settings = Settings::getSettings();
        $user = Auth::user();
        $permittedLoc   = array_filter(explode(',', $user->permitted_locations));
        $permittedDepts = array_filter(explode(',', $user->asset_departments_id));
        $baseQuery = DB::table('consumables_users as cu')
            ->leftJoin('consumables as c', 'c.id', '=', 'cu.consumable_id')
            ->leftJoin('locations as loc', 'loc.id', 'c.location_id')
            ->where('cu.assigned_for', 1)
            ->whereNull('c.deleted_at')
            ->whereBetween('cu.created_at', [$startDate, $endDate]);
        if ($settings->location_config == 1) {
            $baseQuery->whereIn('c.location_id', $permittedLoc ?: [0]);
        }

        if ($settings->department_config == 1) {
            $baseQuery->whereIn('c.department_id', $permittedDepts ?: [0]);
        }
        if(!empty($req['other_filters'])) {
            if (!empty($req['other_filters']['location'])) {
                $baseQuery->where('c.location_id', $req['other_filters']['location']);
            }
            if (!empty($req['other_filters']['department'])) {
                $baseQuery->where('c.department_id', $req['other_filters']['department']);
            }
            if (!empty($req['other_filters']['category'])) {
                $baseQuery->where('c.category_id', $req['other_filters']['category']);
            }
            if (!empty($req['other_filters']['assigned_to'])) {
                $baseQuery->where('cu.user_id', $req['other_filters']['assigned_to']);
            }
        }

        if (!empty($req['search'])) {
            $search = trim($req['search']);
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('((case when c.id is not null then concat_ws("","CN",c.id) else "" end) like "%%%1$s%%" or c.name like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or loc.name like "%%%1$s%%")', $search);
            } else {
                $whereStr = sprintf('((case when c.id is not null then concat_ws("","CNS",c.id) else "" end) like "%%%1$s%%" or c.name like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or loc.name like "%%%1$s%%")', $search);
            }
            $baseQuery->whereRaw($whereStr);
        }
        $items = (clone $baseQuery)->select('c.id')->groupBy('c.name','c.unique_tag','c.id','loc.name')->pluck('c.id')->toArray();
        $openingStock = (clone $baseQuery)
            ->select('c.name','c.qty','c.id','c.unique_tag', DB::raw('COUNT(cu.id) as opening'),'loc.name as loc_name')->groupBy('c.name','c.unique_tag','c.id','loc.name')->whereNull('c.deleted_at')->whereIn('c.id', $items)->get()
            ->mapWithKeys(fn($item) => [
                $item->id => [
                    'qty'     => $item->qty,
                    'opening' => $item->opening,
                    'name'      => $item->name,
                    'loc_name' => $item->loc_name,
                    'unique_tag' => $item->unique_tag,
                    'id' => $item->id,
                ]
            ])->toArray();

        $datewiseQuery = DB::table('consumables_users as cu')->leftJoin('consumables as c', 'c.id', '=', 'cu.consumable_id')->leftJoin('locations as loc', 'c.location_id', 'loc.id')->where('cu.assigned_for', 1)->whereNull('c.deleted_at')->whereBetween('cu.created_at', [$startDate, $endDate])->whereIn('c.id', $items);
        $datewise = $datewiseQuery->select('c.id',DB::raw('DATE_FORMAT(cu.created_at, "%e") as date'),DB::raw('COUNT(cu.id) as count'))
            ->groupBy('c.id', 'loc.name', DB::raw('DATE_FORMAT(cu.created_at, "%Y-%m-%d")'))->get();

        // Pivot Data
        $pivotData = [];
        foreach ($datewise as $row) {
            $pivotData[$row->id][$row->date] = $row->count;
        }
        foreach ($items as $item) {
            if (!isset($pivotData[$item])) {
                $pivotData[$item] = array_fill_keys($dates, 0);
            }
        }

        // Pre-fetch allocated quantities (avoid N+1)
        $allocatedCounts = Consumable::withCount(['users', 'device', 'places'])
            ->whereIn('id', array_column($openingStock, 'id'))
            ->get()
            ->mapWithKeys(fn($c) => [$c->id => ($c->users_count + $c->device_count + $c->places_count)])
            ->toArray();

        $finalResult = [];
        if(!empty($items)) {
            foreach ($items as $key => $item) {
                $itemId        = $openingStock[$item]['id'] ?? null;
                $allocatedQty  = $allocatedCounts[$itemId] ?? 0;
                $dayData       = $pivotData[$item] ?? array_fill_keys($dates, 0);

                $totalDistributed = array_sum($dayData);

                $purchaseCount = ConsumablePurchase::where('batch_no', $itemId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('qty');

                $finalResult[] = array_merge(
                    [
                        'con_batch_id' => isset($openingStock[$item]['id'])
                        ? (
                            config('app.client') === 'etherealmachines'
                                ? 'CN' . $openingStock[$item]['id']
                                : 'CNS' . $openingStock[$item]['id']
                        )
                        : null,
                        'item_name'      => $openingStock[$item]['name'] ?? null,
                        'unique_tag' => $openingStock[$item]['unique_tag'] ?? null,
                        'loc_name' => $openingStock[$item]['loc_name'] ?? null,
                        'opening_stock' => ($openingStock[$item]['qty'] ?? 0) - ($allocatedQty - $totalDistributed),
                    ],
                    collect($dates)->mapWithKeys(fn($d) => ['day_' . $d => $dayData[$d] ?? 0])->toArray(),
                    [
                        'total_distribution' => $totalDistributed,
                        'new_purchase'       => $purchaseCount,
                        'closing_stock'      => ($openingStock[$item]['qty'] ?? 0) - $allocatedQty + $purchaseCount,
                    ]
                );
            }
        } else {
            $row = [
                'con_batch_id' => '',
                'item_name' => '',
                'unique_tag' => '',
                'loc_name' => '',
                'opening_stock' =>  0
            ];
            $totalDistributed = 0;
            foreach ($dates as $date) {
                $daily = $dayData[$date] ?? 0;
                $row['day_' . $date] = $daily;
            }
            $row['total_distribution'] = $totalDistributed;
            $row['new_purchase'] = 0;
            $row['closing_stock'] =  0;
            $row['opening_stock'] = 0;
            $finalResult[] = $row;
        }
        config()->set('database.connections.mysql.strict', true);
        DB::reconnect();

        $filename   = "Daily-Distribution-{$monthName}-{$year}.xlsx";
        $month_year = "{$monthName}-{$year}";
        return Excel::download(new DailyDistributionExport($finalResult, $month_year),$filename);
    }

    public function purchaseExpireReport(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('consumables.controller.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('ConsumablePurchaseExpireReport')) {
            return redirect('dashboard')->with("msg", $return);
        }
        return view("reports.consumable.purchase_expiry");
    }

    public function ajaxPurchaseExpireIndex(Request $request) {
        $fields = [
            0 => 'consu_name',
            1 => 'unique_tag',
            2 => 'batch',
            3 => 'po_no',
            4 => 'purchase_date',
            5 => 'category',
            6 => 'department',
            7 => 'purchase_from',
            8 => 'location',
            9 => 'qty',
            10 => 'expire_date',
            11 => 'received_date',
            12 => 'updated_at_formatted',
        ];

        $draw   = intval($request->draw);
        $start  = intval($request->start);
        $length = intval($request->length);

        $today = Carbon::today();
        $next7Days = Carbon::today()->addDays(7);

        $db = ConsumablePurchase::query()
            ->leftJoin('consumables as a', 'a.id', '=', 'consumable_purchases.batch_no')
            ->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id')
            ->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id')
            ->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id')
            ->leftJoin('suppliers as sup', 'sup.id', '=', 'consumable_purchases.purchase_by')
            ->leftJoin('manufacturers as manu', 'manu.id', '=', 'a.manufacturer_id')
            ->whereNotNull('consumable_purchases.exp_date')
            ->where(function ($q) use ($today, $next7Days) {
                $q->whereDate('consumable_purchases.exp_date', '<', $today)
                    ->orWhereBetween('consumable_purchases.exp_date', [$today, $next7Days]);
            })
            ->select('a.name as consu_name','a.unique_tag','cat.name as category', 'manu.name as manufacturer', 'dep.name as department','loc.name as location', 'consumable_purchases.qty as qty', 'sup.name as purchase_from', 'consumable_purchases.po_no as po_no',
                DB::raw('DATE_FORMAT(consumable_purchases.exp_date, "%d %b %Y") as expire_date'),
                DB::raw('DATE_FORMAT(consumable_purchases.purchase_date, "%d %b %Y") as purchase_date'),
                DB::raw('DATE_FORMAT(consumable_purchases.received_date, "%d %b %Y") as received_date'),
                DB::raw('DATE_FORMAT(consumable_purchases.updated_at, "%d %b %Y %h:%i %p") as updated_at_formatted')
            );
            $companyIds = CommonHelper::getSelectedCompanyIds();
            $db->whereIn("a.company_id", $companyIds);
        $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","' . (config("app.client") == "etherealmachines" ? 'CN' : 'CNS') . '",a.id) else "" end as batch'));


        $totalRecords = (clone $db)->count();

        if (!empty($request->search['value'])) {
            $search_key = $request->search['value'];
            $whereStr = sprintf('a.name LIKE "%%%1$s%%" OR a.unique_tag LIKE "%%%1$s%%" OR cat.name LIKE "%%%1$s%%" OR manu.name LIKE "%%%1$s%%" OR dep.name LIKE "%%%1$s%%" OR loc.name LIKE "%%%1$s%%" OR sup.name LIKE "%%%1$s%%" OR consumable_purchases.po_no LIKE "%%%1$s%%" OR consumable_purchases.qty LIKE "%%%1$s%%" OR DATE_FORMAT(consumable_purchases.exp_date, "%%d %%b %%Y") LIKE "%%%1$s%%" OR DATE_FORMAT(consumable_purchases.purchase_date, "%%d %%b %%Y") LIKE "%%%1$s%%" OR DATE_FORMAT(consumable_purchases.received_date, "%%d %%b %%Y") LIKE "%%%1$s%%" OR DATE_FORMAT(consumable_purchases.updated_at, "%%d %%b %%Y %%h:%%i %%p") LIKE "%%%1$s%%" OR (CASE WHEN a.id IS NOT NULL THEN CONCAT_WS("","' . (config("app.client") == "etherealmachines" ? 'CN' : 'CNS') . '",a.id) ELSE "" END) LIKE "%%%1$s%%"', $search_key);
            $db->where(function ($q) use ($whereStr) {
                $q->whereRaw($whereStr);
            });
        }
        $filters = $request->other_filters ;
        if (!empty($filters)) {
            if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                $db->where("a.location_id", "=", (int)$filters['location']);
            }

            if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                $db->where("a.category_id", "=", (int)$filters['category']);
            }

            if(isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                $db->where("a.department_id", "=", (int)$filters['department']);
            }

            $based_on_possible = ['1'=>'consumable_purchases.purchase_date','2'=>'consumable_purchases.exp_date','3'=>'consumable_purchases.received_date','4'=>'consumable_purchases.updated_at'];

            if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 4 ) {
                if(isset($filters["dateRange"]) && $filters["dateRange"] && $filters["dateRange"] != "null") {
                    $daterange = explode(" - ", $filters["dateRange"]);
                    $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                    $to_date   = date("Y-m-d H:i:s", strtotime($daterange[1]));
                    if($from_date && $to_date)
                        $db->whereRaw(sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date));
                }
            }
        }

        $filteredRecords = (clone $db)->count();
        if ($request->has('order.0.column') && isset($fields[$request->input('order.0.column')]) && in_array($request->input('order.0.dir'), ['asc','desc'])) {
            $db->orderBy($fields[$request->input('order.0.column')], $request->input('order.0.dir'));
        }
        $data = $db->skip($start)->take($length)->get();
        return response()->json([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ]);
    }

    public function purchaseExpireExport(Request $request) {
        $today = Carbon::today();
        $next7Days = Carbon::today()->addDays(7);

        $db = ConsumablePurchase::query()
            ->leftJoin('consumables as a', 'a.id', '=', 'consumable_purchases.batch_no')
            ->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id')
            ->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id')
            ->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id')
            ->leftJoin('suppliers as sup', 'sup.id', '=', 'consumable_purchases.purchase_by')
            ->leftJoin('manufacturers as manu', 'manu.id', '=', 'a.manufacturer_id')
            ->whereNotNull('consumable_purchases.exp_date')
            ->where(function ($q) use ($today, $next7Days) {
                $q->whereDate('consumable_purchases.exp_date', '<', $today)
                    ->orWhereBetween('consumable_purchases.exp_date', [$today, $next7Days]);
            });
            $companyIds = CommonHelper::getSelectedCompanyIds();
            $db->whereIn("a.company_id", $companyIds);
        $db->select(DB::raw('case when a.id is not null then concat_ws("","' . (config("app.client") == "etherealmachines" ? 'CN' : 'CNS') . '",a.id) else "" end as batch'));

        $db->addSelect('a.unique_tag','a.name as consu_name','cat.name as category', 'manu.name as manufacturer', 'dep.name as department','loc.name as location', 'consumable_purchases.qty as qty', 'sup.name as purchase_from', 'consumable_purchases.po_no as po_no',
            DB::raw('DATE_FORMAT(consumable_purchases.purchase_date, "%d %b %Y") as purchase_date'),
            DB::raw('DATE_FORMAT(consumable_purchases.received_date, "%d %b %Y") as received_date'),
            DB::raw('DATE_FORMAT(consumable_purchases.exp_date, "%d %b %Y") as expire_date'),
            DB::raw('DATE_FORMAT(consumable_purchases.updated_at, "%d %b %Y %h:%i %p") as updated_at_formatted')
        );

        $totalRecords = (clone $db)->count();
        $request_filters = base64_decode($request->q);
        $decodeDdFilters = json_decode($request_filters);
        $filters = $decodeDdFilters->filter;

        if (!empty($decodeDdFilters->search)) {
            $search_key = $decodeDdFilters->search;
            $whereStr = sprintf('a.name LIKE "%%%1$s%%" OR a.unique_tag LIKE "%%%1$s%%" OR cat.name LIKE "%%%1$s%%" OR manu.name LIKE "%%%1$s%%" OR dep.name LIKE "%%%1$s%%" OR loc.name LIKE "%%%1$s%%" OR sup.name LIKE "%%%1$s%%" OR consumable_purchases.po_no LIKE "%%%1$s%%" OR consumable_purchases.qty LIKE "%%%1$s%%" OR DATE_FORMAT(consumable_purchases.exp_date, "%%d %%b %%Y") LIKE "%%%1$s%%" OR DATE_FORMAT(consumable_purchases.purchase_date, "%%d %%b %%Y") LIKE "%%%1$s%%" OR DATE_FORMAT(consumable_purchases.received_date, "%%d %%b %%Y") LIKE "%%%1$s%%" OR DATE_FORMAT(consumable_purchases.updated_at, "%%d %%b %%Y %%h:%%i %%p") LIKE "%%%1$s%%" OR (CASE WHEN a.id IS NOT NULL THEN CONCAT_WS("","' . (config("app.client") == "etherealmachines" ? 'CN' : 'CNS') . '",a.id) ELSE "" END) LIKE "%%%1$s%%"', $search_key);
            $db->where(function ($q) use ($whereStr) {
                $q->whereRaw($whereStr);
            });
        }

        if (!empty($filters)) {
            if(isset($filters->location) && $filters->location && $filters->location != "null") {
                $db->where("a.location_id", "=", (int)$filters->location);
            }

            if(isset($filters->category) && $filters->category && $filters->category != "null") {
                $db->where("a.category_id", "=", (int)$filters->category);
            }

            if(isset($filters->department) && $filters->department && $filters->department != "null") {
                $db->where("a.department_id", "=", (int)$filters->department);
            }

            $based_on_possible = ['1'=>'consumable_purchases.purchase_date','2'=>'consumable_purchases.exp_date','3'=>'consumable_purchases.received_date','4'=>'consumable_purchases.updated_at'];
            if(isset($filters->based_on) && $filters->based_on && $filters->based_on != "null" && $filters->based_on >= 1 && $filters->based_on <= 4 ) {
                if(isset($filters->dateRange) && $filters->dateRange && $filters->dateRange != "null") {
                    $daterange = explode(" - ", $filters->dateRange);
                    $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                    $to_date   = date("Y-m-d H:i:s", strtotime($daterange[1]));
                    if($from_date && $to_date)
                        $db->whereRaw(sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters->based_on], $from_date, $to_date));
                }
            }
        }

        $filteredRecords = (clone $db)->count();
        $data = $db->get();
        $result = json_decode(json_encode($data, true),true);
        return Excel::download(new ConsumablesPurchaseExpire($result), 'Category_Wise_Consumables_Report.xlsx');
    }
}

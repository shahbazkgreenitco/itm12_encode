<?php

namespace App\Http\Controllers\Device\NI;

use App\Exports\MicrosoftOfficeDetails;
use App\Exports\NetworkInventory\LicenseReport;
use App\Exports\OperatingSystemDetails;
use App\Http\Controllers\Controller;
use App\Models\Assets;
use App\Exports\NetworkInventory\InventoryDetails;
use App\Exports\NetworkInventory\DownloadInstalledProgrammesReport;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\Company;
use App\Models\Category;
use App\Models\Component;
use App\Models\Device;
use App\Models\NetworkInventory\ScanRegister;
use App\Models\NetworkInventory\Basic;
use App\Models\NetworkInventory\PhysicalMemory;
use App\Models\NetworkInventory\DiskDrive;
use App\Models\NetworkInventory\Product;
use App\Models\NetworkInventory\Volume;
use App\Models\NetworkInventory\VideoController;
use App\Models\NetworkInventory\SoundDevice;
use App\Models\NetworkInventory\ChangeLog;
use App\Models\NetworkInventory\ChangeCache;
use App\Models\NetworkInventory\UserAccount;
use App\Models\NetworkInventory\AgentData;
use App\Models\NetworkInventory\AgentErrorLog;
use App\Models\NetworkInventory\RegPvtEnterprise;
use App\Models\NetworkInventory\TrackedDevice;
use App\Models\NetworkInventory\MappedLocation;
use App\Models\NetworkInventory\AgentRunSchedule;
use App\Models\NetworkInventory\NetworkAdapter;
use App\Models\NetworkInventory\Monitor;
use App\Models\Place;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;
use DB;
use Log;
use Spatie\LaravelPdf\Facades\Pdf as PDF;
use stdClass;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Helpers\Common as CommonHelper;
use App\Helpers\Ni\AllData;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;

class NetworkInventoryController extends Controller {
    
    // public function MapList() {
    //     $places = place::where("location_id")->get();
    //     return view("network_inventories.mapped-ips")->with(compact("basic", "places"));
    // }

    // public function downloadDeviceReport(Request $request, $id) {
      
    //     $return = ['status' => 'danger', 'msg' => trans('content.network_inv_fields.Unable_to_export_the_Devices')];
    //     if(! Auth::user()->hasPermissionTo('NIDownload') || ! config("services.network_inventory.enabled")) {
    //         $return["msg"] = trans('content.user_fields.Permission_denied');
    //         return redirect('dashboard')->with("msg", $return);
    //     }
    //     $db = DB::table('itm_network_inventory_basic as a');
    //     $db->leftJoin('itm_network_inventory_products as pr', 'pr.basic_id', '=', 'a.id');

    //     $db->select('pr.Caption as caption', 'pr.Version as version', 'pr.InstalledDate','pr.Publisher as publisher');
    //     $db->addSelect(DB::raw('DATE_FORMAT(pr.InstalledDate, "%d %m %Y") as installed_date'));
    //     if($request->q) {
    //         if( $search_key = trim($request->q) ) {
    //             $whereStr = sprintf('(pr.Caption like "%%%1$s%%" or pr.Version like "%%%1$s%%" or pr.Publisher like "%%%1$s%%" or DATE_FORMAT(pr.InstalledDate, "%%d/%%m/%%Y") like "%%%1$s%%")', $search_key);
    //             $db->whereRaw($whereStr);
    //         }
    //     }
        
    //     $records = $db->where('a.id','=',$id)->get();
        
    //     $data = [];
    //     foreach($records as $r) {
    //         $data[] = [
    //             $r->caption,
    //             $r->version,
    //             $r->installed_date,
    //             $r->publisher
    //         ];
    //     }

    //     return Excel::download(new DownloadInstalledProgrammesReport($data), 'InstalledProgrammes.xlsx');
    // }

    public function exportInventoryDetails(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('NIDownload') || ! config("services.network_inventory.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $company_id = implode(',', $companyIds);

        $select_fields = "select cmp.name as cmp_name, assets.asset_tag,assets.rdp_status, assets.assigned_to, assets.notes, DATE_FORMAT(assets.purchase_date, '%d %b %Y %h:%i %p') as purchase_date_on, DATE_FORMAT(assets.last_checkout, '%d %b %Y %h:%i %p') as last_checkout_on, concat(u.first_name, ' ', u.last_name) as full_name, case when assets.device_occure_type = 1 then 'Project Device' when assets.device_occure_type = 2 then 'Rental' when assets.device_occure_type = 3 then 'Customer Owned' else 'Purchase Device' end as device_occure_type_name, case when assets.high_pririty = 1 then 'High' else 'Low' end as priority, b.ComputerName, b.ComputerManufacturer, b.ComputerModel, b.BIOSSerialNumber, b.ComputerDomain, b.OSCaption,b.OSSerialNumber, b.OSVersion, b.ActiveMACAddress, b.IPv4, b.IPv6, DATE_FORMAT(b.created_at, '%d %b %Y %h:%i %p') as created_at_format, DATE_FORMAT(b.updated_at, '%d %b %Y %h:%i %p') as updated_at_format, b.id AS basic_id,b.is_AD as b_is_AD, assets.asset_tag, assets.id as asset_id, b.HddSize, b.RamSize, b.ProcessorName, b.OSArchitecture, case when status.deployable <> 0 and status.archived = 0 and assets.assigned_to <> '' and assets.assigned_to > 0 then 'Deployed' else status.name end as status, loc.name as loc_name,p.place as place, cat.name as cat_name, pur.invoice_no as pur_invoice, concat(own.first_name, ' ', own.last_name) as asset_owner, pldev.place as stock_place, pldevloc.name as pldevloc_name,ua2.Caption, DATE_FORMAT(LastLogonDate.LastLogon, '%d %b %Y %h:%i %p') as LastLogon_format, b.SoftwareLicensingProduct, ni_loc.name as ni_detected_location_name, b.agentVersion";

        $count_fields = "select count(*) as tot";

        $loc = $dep = '';
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $loc = " and assets.rtd_location_id = 0 ";
            } else {
                $loc = " and assets.rtd_location_id IN(".$loc_previllage.") ";
            }
        }
        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            if (empty($asset_dept_permission)) {
                $dep = " and assets.department_id = 0";
            } else {
                $dep = " and assets.department_id IN(".$asset_dept_permission.") ";
            }
        }
        // $query = "FROM itm_network_inventory_basic as b left join assets on b.BIOSSerialNumber like assets.serial and assets.deleted_at is null where b.is_dupe is null and b.deleted_at is null";
        $query = "FROM itm_network_inventory_basic as b left join assets on b.device_id = assets.id 
                    left join users on users.id = assets.assigned_to 
                    left join companies as cmp on cmp.id=assets.company_id 
                    left join status_labels as status on status.id=assets.status_id 
                    left join locations as loc on loc.id=assets.rtd_location_id
                    left join locations as ni_loc on ni_loc.id = assets.ni_detected_location
                    left join places as p on p.id=assets.internal_place_id 
                    left join purchases as pur on pur.id=assets.invoice_id                    
                    left join users as own on own.id=assets.asset_owner 
                    left join models as mdl on mdl.id=assets.model_id 
                    left join places as pldev on pldev.id=assets.stock_place 
                    left join locations as pldevloc on pldevloc.id=pldev.location_id
                    left join users as u on u.id=assets.assigned_to and assets.assigned_for = 1
                    left join categories as cat on cat.id=mdl.category_id and mdl.category_id <> 0 
                    left JOIN (SELECT basic_id, MAX(LastLogon) LastLogon FROM itm_network_user_accounts GROUP BY basic_id) LastLogonDate ON b.id = LastLogonDate.basic_id left JOIN itm_network_user_accounts ua2 on (b.id = ua2.basic_id AND LastLogonDate.LastLogon = ua2.LastLogon)
                    where assets.deleted_at is null and assets.company_id in ($company_id) AND b.is_dupe is null and b.deleted_at is null $loc $dep";

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if(isset($filters->search)) {
                $req["search"] = $filters->search;
            }

            if(isset($filters->other_filters)){
                $req["filters"] = (array) $filters->other_filters;
            }

            if(isset($filters->dashboard_filters)){
                $req["dashboard_filters"] = (array) $filters->dashboard_filters;
                $syncdate = isset($req["dashboard_filters"]['sync_date']) ? $req["dashboard_filters"]['sync_date'] : null;
                $today = now()->startOfDay();
                $yesterday = now()->subDay()->startOfDay();
                if (!empty($syncdate) && $syncdate != 'null') {
                    if ($syncdate == $today->toDateString()) {
                        $query .= " AND DATE(b.updated_at) != '" . $today->toDateString() . "'";
                    } elseif ($syncdate == $yesterday->toDateString()) {
                        $query .= " AND DATE(b.updated_at) != '" . $yesterday->toDateString() . "'";
                    } else {
                        $syncdate1 = date("Y-m-d", strtotime($syncdate));
                        $today1 = $today->toDateString();
                        $query .= " AND (DATE(b.updated_at) < '" . $syncdate1 . "' 
                                    OR DATE(b.updated_at) > '" . $today1 . "')";
                    }
                }
                $location_filter = isset($req["dashboard_filters"]['location']) ? $req["dashboard_filters"]['location'] : null;
                if (!empty($location_filter) && $location_filter != 'null') {
                    $location_ids = implode(",", array_map('intval', explode(",", $location_filter)));
                    $query .= " AND assets.rtd_location_id IN ($location_ids)";
                }

                $department_filter = isset($req["dashboard_filters"]['department']) ? $req["dashboard_filters"]['department'] : null;
                if (!empty($department_filter) && $department_filter != 'null') {
                    $department_ids = implode(",", array_map('intval', explode(",", $department_filter)));
                    $query .= " AND assets.department_id IN ($department_ids)";
                }

                $assign_user_location = isset($req["dashboard_filters"]['assign_user_location']) ? $req["dashboard_filters"]['assign_user_location'] : null;
                if (!empty($assign_user_location) && $assign_user_location != 'null') {
                    $assign_user_location_ids = implode(",", array_map('intval', explode(",", $assign_user_location)));
                    $query .= " AND assets.status_id = 6 AND assets.assigned_for = 1 AND users.location_id IN ($assign_user_location_ids)";
                }

                $model_id_filter = isset($req["dashboard_filters"]['model_id']) ? $req["dashboard_filters"]['model_id'] : null;
                if ($model_id_filter != null && $model_id_filter != 'null') {
                    $query .= " AND assets.model_id IN ($model_id_filter)";
                }
            }

            $where = "";
            if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                $where = 'AND (assets.asset_tag like "%'. $search_key .'%" or b.ComputerName like "%'. $search_key .'%" or b.ComputerManufacturer like "%'. $search_key .'%" or b.ComputerModel like "%'. $search_key .'%" or b.BIOSSerialNumber like "%'. $search_key .'%" or b.ComputerDomain like "%'. $search_key .'%" or b.OSCaption like "%'. $search_key .'%" or b.ActiveMACAddress like "%'. $search_key .'%" or b.IPv4 like "%'. $search_key .'%" or b.HddSize like "%'. $search_key .'%" or DATE_FORMAT(b.updated_at, "%d %b %Y %h:%i %p") like "%'. $search_key .'%" or b.RamSize like "%'. $search_key .'%" or b.ProcessorName like "%'. $search_key .'%" or b.ComputerSystemType like "%'. $search_key .'%" or ni_loc.name like "%'. $search_key .'%")';
            }


            $where_arr = [];
            if(isset($req["filters"])) {
                $filters = $req["filters"];

                $filter_cond = "";
                if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                    $where_arr[] = "b.ComputerManufacturer like '". $filters['manufacturer'] . "'";
                }
                if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                    $where_arr[] = "cat.id like '". $filters['category'] . "'";
                }
                if(isset($filters["model"]) && $filters['model'] && $filters['model'] != "null") {
                    $where_arr[] = "b.ComputerModel like '". $filters['model'] . "'";
                }
                if(isset($filters["os"]) && $filters['os'] && $filters['os'] != "null") {
                    $where_arr[] = "b.OSCaption like '". $filters['os'] . "'";
                }
                if(isset($filters["version"]) && $filters['version'] && $filters['version'] != "null") {
                    $where_arr[] = "b.OSVersion like '". $filters['version'] . "'";
                }
                if(isset($filters["processor_name"]) && $filters['processor_name'] && $filters['processor_name'] != "null") {
                    $where_arr[] = "b.ProcessorName like '". $filters['processor_name'] . "'";
                }
                if(isset($filters["computer_system_type"]) && $filters['computer_system_type'] && $filters['computer_system_type'] != "null") {
                    $where_arr[] = "b.ComputerSystemType like '". $filters['computer_system_type'] . "'";
                }
                if(isset($filters["platform"]) && $filters['platform'] && $filters['platform'] != "null") {
                    $where_arr[] = "b.platform = ". $filters['platform'] . "";
                }
                if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                    $where_arr[] = "assets.rtd_location_id = ". $filters['location'] . "";
                }
                if (isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                    $where_arr[] = "assets.internal_place_id IN (" . implode(',', $filters['internal_place']) . ")";               
                }
                /*if(isset($filters["program"]) && $filters['program'] && $filters['program'] != "null") {
                    $where_arr[] = "itm.Caption like '". $filters['program'] . "'";
                }*/
                if(isset($filters["hdd"]) && $filters['hdd'] && $filters['hdd'] != "null") {
                    $cond = "=";
                    if((isset($filters["hdd_cond"]) && $filters['hdd'] && $filters['hdd'] != "null")) {
                        switch($filters["hdd_cond"]) {
                            case 1:
                                $cond = ">";
                                break;
                            case 3:
                                $cond = "<";
                                break;
                            default:
                                $cond = "=";
                        }
                    }
                    $where_arr[] = "b.HddSize " . $cond . " ". $filters['hdd'] . "";
                }
                if(isset($filters["ram"]) && $filters['ram'] && $filters['ram'] != "null") {
                    $cond = "=";
                    if((isset($filters["ram_cond"]) && $filters['ram'] && $filters['ram'] != "null")) {
                        switch($filters["ram_cond"]) {
                            case 1:
                                $cond = ">";
                                break;
                            case 3:
                                $cond = "<";
                                break;
                            default:
                                $cond = "=";
                        }
                    }
                    $where_arr[] = "b.RamSize " . $cond . " ". $filters['ram'] . "";
                }
                if(isset($filters["genuine_status"]) && $filters['genuine_status'] && $filters['genuine_status'] != "null") {
                    $whereStr = sprintf('(b.SoftwareLicensingProduct like "%%%1$s%%")', '\"GenuineStatus\":\"'.$filters["genuine_status"]);
                    $where_arr[] = $whereStr;
                }
                if(isset($filters["license_status"]) && $filters['license_status']!= "" && $filters['license_status'] != "null") {
                    $whereStr = sprintf('(b.SoftwareLicensingProduct like "%%%1$s%%")', '\"LicenseStatus\":\"'.$filters["license_status"]);
                    $where_arr[] = $whereStr;
                }
                $based_on_possible = ['1'=>'b.created_at', '2'=>'b.updated_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
                    if(isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                        $daterange = explode(" - ", $filters["date_range"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $where_arr[] =  $whereStr;
                        }
                    }
                }
                if(isset($filters["ad_nonad_agent"]) && $filters['ad_nonad_agent']!= "" && $filters['ad_nonad_agent'] != "null"){
                    $where_arr[] = "b.is_AD = ". $filters['ad_nonad_agent']. "";
                }

                if(count($where_arr)) {
                    $condition = implode(" and ", $where_arr);
                    $where .= $where ?  (" and " . $condition) : (" and " . $condition);
                }
            }

            $get_count = DB::select($count_fields . " " . $query);

            $return['recordsTotal'] = $get_count[0]->tot;
            $return['recordsFiltered'] = $return['recordsTotal'];

            if($where) {
                $get_count = DB::select($count_fields . " " . $query . " " . $where);
                $return['recordsFiltered'] = $get_count[0]->tot;
            }

            $order = "";
            /*if( isset($req["order"][0]["column"]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
                $order = "order by " . $fields[$req["order"][0]["column"]] . " " . $req["order"][0]["dir"];
            }*/

            $data_query = $select_fields . " " . $query . " " . $where . " " . $order;

            $records = DB::select($data_query);

            $data = [];
            foreach($records as $k => $r) {
                $stock_place = (!$r->assigned_to && $r->stock_place) ? implode(', ', [$r->stock_place, $r->pldevloc_name]) : '';
                $chkout_date = $r->assigned_to ? $r->last_checkout_on : '';
                $softwareLicensingProduct = (isset($r->SoftwareLicensingProduct) && $r->SoftwareLicensingProduct != "") ? json_decode($r->SoftwareLicensingProduct,true) : [];
                $licenseData = [];
                if(!empty($softwareLicensingProduct)) {
                    foreach ($softwareLicensingProduct as $license) {
                        if (substr($license['Name'], 0, 7) === "Windows") {
                            $licenseData = $license;
                        }
                    }
                }
                $softwareLicensingProduct = $licenseData;
                $genuineStatus = (isset($softwareLicensingProduct['LicenseStatus']) && $softwareLicensingProduct['LicenseStatus'] != 0) ? "Active" : "Inactive";
                $licenseStatus = "";
                if(isset($softwareLicensingProduct['LicenseStatus'])) {
                    switch($softwareLicensingProduct['LicenseStatus']) {
                        case 0:
                            $licenseStatus = "Unlicensed";
                            break;
                        case 1:
                            $licenseStatus = "Licensed";
                            break;
                        case 2:
                            $licenseStatus = "OOBGrace";
                            break;
                        case 3:
                            $licenseStatus = "OOTGrace";
                            break;
                        case 4:
                            $licenseStatus = "NonGenuineGrace";
                            break;
                        case 5:
                            $licenseStatus = "Notification";
                            break;
                        case 6:
                            $licenseStatus = "ExtendedGrace";
                            break;
                        default:
                            $licenseStatus = "";
                            break;
                    }
                }
                $productKey = isset($softwareLicensingProduct['PartialProductKey']) ? $softwareLicensingProduct['PartialProductKey'] : '';
                $productKeyID = isset($softwareLicensingProduct['ProductKeyID']) ? $softwareLicensingProduct['ProductKeyID'] : '';
                $disk_drives = DiskDrive::where("basic_id", "=", $r->basic_id)->count();
                // $LastLogon_account = $r->LastLogon_format != "" ? $r->Caption : "";
                $data[] = [
                    $r->cmp_name,
                    $r->ComputerName,
                    $r->asset_tag,
                    $r->ComputerDomain,
                    $r->ComputerManufacturer,
                    $r->ComputerModel,
                    $r->BIOSSerialNumber,
                    $r->OSCaption,
                    $r->OSSerialNumber,
                    $r->OSVersion,
                    $r->HddSize,
                    $disk_drives,
                    $r->RamSize,
                    $r->ProcessorName,
                    $r->OSArchitecture,
                    $r->status,
                    $r->loc_name,
                    $r->place,
                    $stock_place,
                    $r->cat_name,
                    $r->notes,
                    $r->purchase_date_on,
                    $r->pur_invoice,
                    $chkout_date,
                    $r->full_name,
                    $r->device_occure_type_name,
                    $r->asset_owner,
                    $r->priority,
                    $r->IPv4,
                    $r->IPv6,
                    $r->ActiveMACAddress,
                    $r->created_at_format,
                    $r->updated_at_format,
                    $r->ni_detected_location_name,
                    $r->Caption,
                    $r->LastLogon_format,
                    $genuineStatus,
                    $licenseStatus,
                    $productKey,
                    $productKeyID,
                    ($r->b_is_AD == 1 ? 'AD' : 'Non AD'),
                    $r->agentVersion,
                ];
                if(config('services.rdp.enabled') == 1) {
                    array_push($data[$k], ($r->rdp_status == 1) ? "Enabled" : "Disabled" );
                }
            }
            $key = ['Company','Device Name','Device Tag','Domain','Manufacturer','Model','Serial','OS Caption','OS SerialNumber','OS Version','Hard Disk Size','Hard Disk Count','RAM Size','Processor','OS Architecture','Status','Location','Internal Place','Stock Place','Category','Notes','Purchase Date','Purchase Invoice','Checkout Date','Assigned User','Device Type','Asset Owner','Asset Classification','IPv4','IPv6','MAC','Created Date','Updated Date','Last Network Location','LastLogon User Account','LastLogon Date','License Activated','License Status','License ProductKey','License ProductKeyID','IS AD/Non AD','Agent Version'];
            if(config('services.rdp.enabled') == 1) {
                array_push($key, "RDP Status");
            }

            return Excel::download(new InventoryDetails($data, $key), 'InventoryDetails.xlsx');
        }
    }



    public function downloadPdfFormat(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('NIDownload') || ! config("services.network_inventory.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $company_id = implode(',', $companyIds);
        $loc = $dep = '';
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        // if($settings->location_config == 1) {
        //     if(empty($loc_previllage)) {
        //         $loc = " and assets.rtd_location_id = 0 ";
        //     } else {
        //         $loc = " and assets.rtd_location_id IN(".$loc_previllage.") ";
        //     }
        // }
        // // check asset dept permission
        // if ($settings->department_config == 1) {
        //     $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
        //     if (empty($asset_dept_permission)) {
        //         $dep = " and assets.department_id = 0";
        //     } else {
        //         $dep = " and assets.department_id IN(".$asset_dept_permission.") ";
        //     }
        // }

        $select_fields = "select assets.asset_tag, b.ComputerName, b.ComputerManufacturer, b.ComputerModel, b.BIOSSerialNumber, b.ComputerDomain, b.OSCaption, b.ActiveMACAddress, b.IPv4, DATE_FORMAT(b.updated_at, '%d %b %Y %h:%i %p') as updated_at_format, b.id AS basic_id, assets.asset_tag, assets.id as asset_id, b.HddSize, b.RamSize";

        $count_fields = "select count(*) as tot";

        $query = "FROM itm_network_inventory_basic as b left join assets on b.BIOSSerialNumber like assets.serial left join users on users.id = assets.assigned_to and assets.deleted_at is null left join models as mdl on mdl.id=assets.model_id  left join categories as cat on cat.id=mdl.category_id and mdl.category_id <> 0 where b.is_dupe is null and assets.company_id in ($company_id) and b.deleted_at is null $loc $dep";

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            
            if(isset($filters->search)) {
                $req["search"] = $filters->search;
            }

            if(isset($filters->other_filters)){
                $req["filters"] = (array) $filters->other_filters;
            }

            
            if(isset($filters->dashboard_filters)){
                $req["dashboard_filters"] = (array) $filters->dashboard_filters;
                $syncdate = isset($req["dashboard_filters"]['sync_date']) ? $req["dashboard_filters"]['sync_date'] : null;
                $today = now()->startOfDay();
                $yesterday = now()->subDay()->startOfDay();
                if (!empty($syncdate) && $syncdate != 'null') {
                    if ($syncdate == $today->toDateString()) {
                        $query .= " AND DATE(b.updated_at) != '" . $today->toDateString() . "'";
                    } elseif ($syncdate == $yesterday->toDateString()) {
                        $query .= " AND DATE(b.updated_at) != '" . $yesterday->toDateString() . "'";
                    } else {
                        $syncdate1 = date("Y-m-d", strtotime($syncdate));
                        $today1 = $today->toDateString();
                        $query .= " AND (DATE(b.updated_at) < '" . $syncdate1 . "' 
                                    OR DATE(b.updated_at) > '" . $today1 . "')";
                    }
                }
                $location_filter = isset($req["dashboard_filters"]['location']) ? $req["dashboard_filters"]['location'] : null;
                if (!empty($location_filter) && $location_filter != 'null') {
                    $location_ids = implode(",", array_map('intval', explode(",", $location_filter)));
                    $query .= " AND assets.rtd_location_id IN ($location_ids)";
                }

                $department_filter = isset($req["dashboard_filters"]['department']) ? $req["dashboard_filters"]['department'] : null;
                if (!empty($department_filter) && $department_filter != 'null') {
                    $department_ids = implode(",", array_map('intval', explode(",", $department_filter)));
                    $query .= " AND assets.department_id IN ($department_ids)";
                }

                $assign_user_location = isset($req["dashboard_filters"]['assign_user_location']) ? $req["dashboard_filters"]['assign_user_location'] : null;
                if (!empty($assign_user_location) && $assign_user_location != 'null') {
                    $assign_user_location_ids = implode(",", array_map('intval', explode(",", $assign_user_location)));
                    $query .= " AND assets.status_id = 6 AND assets.assigned_for = 1 AND users.location_id IN ($assign_user_location_ids)";
                }

                $model_id_filter = isset($req["dashboard_filters"]['model_id']) ? $req["dashboard_filters"]['model_id'] : null;
                if ($model_id_filter != null && $model_id_filter != 'null') {
                    $query .= " AND assets.model_id IN ($model_id_filter)";
                }
            }

            $where = "";
            if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                $where = 'AND (assets.asset_tag like "%'. $search_key .'%" or b.ComputerName like "%'. $search_key .'%" or itmdd.SerialNumber like "%'. $search_key .'%" or itmpm.SerialNumber like "%'. $search_key .'%" or b.ComputerManufacturer like "%'. $search_key .'%" or b.ComputerModel like "%'. $search_key .'%" or b.BIOSSerialNumber like "%'. $search_key .'%" or b.ComputerDomain like "%'. $search_key .'%" or b.OSCaption like "%'. $search_key .'%" or b.ActiveMACAddress like "%'. $search_key .'%" or b.IPv4 like "%'. $search_key .'%" or b.HddSize like "%'. $search_key .'%" or DATE_FORMAT(b.updated_at, "%d %b %Y %h:%i %p") like "%'. $search_key .'%" or b.RamSize like "%'. $search_key .'%" or b.ProcessorName like "%'. $search_key .'%" or b.ComputerSystemType like "%'. $search_key .'%")';
            }

            $where_arr = [];
            if(isset($req["filters"])) {
                $filters = $req["filters"];
    
                $filter_cond = "";
                if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                    $where_arr[] = "b.ComputerManufacturer like '". $filters['manufacturer'] . "'";
                }
                if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                    $where_arr[] = "cat.id like '". $filters['category'] . "'";
                }
                if(isset($filters["model"]) && $filters['model'] && $filters['model'] != "null") {
                    $where_arr[] = "b.ComputerModel like '". $filters['model'] . "'";
                }
                if (isset($filters['location']) && $filters['location'] && $filters['location'] !== "null") {
                    $where_arr[] = "assets.rtd_location_id like '". $filters['location'] . "'";
                }
                if (isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                    $where_arr[] = "assets.internal_place_id IN (" . implode(',', $filters['internal_place']) . ")";               
                }
                if(isset($filters["os"]) && $filters['os'] && $filters['os'] != "null") {
                    $where_arr[] = "b.OSCaption like '". $filters['os'] . "'";
                }
                if(isset($filters["version"]) && $filters['version'] && $filters['version'] != "null") {
                    $where_arr[] = "b.OSVersion like '". $filters['version'] . "'";
                }
                if(isset($filters["processor_name"]) && $filters['processor_name'] && $filters['processor_name'] != "null") {
                    $where_arr[] = "b.ProcessorName like '". $filters['processor_name'] . "'";
                }
                if(isset($filters["computer_system_type"]) && $filters['computer_system_type'] && $filters['computer_system_type'] != "null") {
                    $where_arr[] = "b.ComputerSystemType like '". $filters['computer_system_type'] . "'";
                }
                if(isset($filters["platform"]) && $filters['platform'] && $filters['platform'] != "null") {
                    $where_arr[] = "b.platform = ". $filters['platform'] . "";
                }
                if(isset($filters["program"]) && $filters['program'] && $filters['program'] != "null") {
                    $where_arr[] = "itm.Caption like '". $filters['program'] . "'";
                }
                if(isset($filters["hdd"]) && $filters['hdd'] && $filters['hdd'] != "null") {
                    $cond = "=";
                    if((isset($filters["hdd_cond"]) && $filters['hdd'] && $filters['hdd'] != "null")) {
                        switch($filters["hdd_cond"]) {
                            case 1:
                                $cond = ">"; 
                                break;
                            case 3:
                                $cond = "<"; 
                                break;
                            default:
                                $cond = "="; 
                        }
                    }
                    $where_arr[] = "b.HddSize " . $cond . " ". $filters['hdd'] . "";
                }
                if(isset($filters["ram"]) && $filters['ram'] && $filters['ram'] != "null") {
                    $cond = "=";
                    if((isset($filters["ram_cond"]) && $filters['ram'] && $filters['ram'] != "null")) {
                        switch($filters["ram_cond"]) {
                            case 1:
                                $cond = ">"; 
                                break;
                            case 3:
                                $cond = "<"; 
                                break;
                            default:
                                $cond = "="; 
                        }
                    }
                    $where_arr[] = "b.RamSize " . $cond . " ". $filters['ram'] . "";
                }
                if(isset($filters["genuine_status"]) && $filters['genuine_status'] && $filters['genuine_status'] != "null") {
                    $whereStr = sprintf('(b.SoftwareLicensingProduct like "%%%1$s%%")', '\"GenuineStatus\":\"'.$filters["genuine_status"]);
                    $where_arr[] = $whereStr;
                }
                if(isset($filters["license_status"]) && $filters['license_status']!= "" && $filters['license_status'] != "null") {
                    $whereStr = sprintf('(b.SoftwareLicensingProduct like "%%%1$s%%")', '\"LicenseStatus\":\"'.$filters["license_status"]);
                    $where_arr[] = $whereStr;
                }
                $based_on_possible = ['1'=>'b.created_at', '2'=>'b.updated_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
                    if(isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                        $daterange = explode(" - ", $filters["date_range"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $where_arr[] =  $whereStr;
                        }
                    }
                }
                if(isset($filters["ad_nonad_agent"]) && $filters['ad_nonad_agent']!= "" && $filters['ad_nonad_agent'] != "null"){
                    $where_arr[] = "b.is_AD = ". $filters['ad_nonad_agent']. "";
                }
    
                if(count($where_arr)) {
                    $condition = implode(" and ", $where_arr);
                    $where .= $where ?  (" and " . $condition) : (" and " . $condition);
                }
            }
            
            $get_count = DB::select($count_fields . " " . $query);
    
            $return['recordsTotal'] = $get_count[0]->tot;
            $return['recordsFiltered'] = $return['recordsTotal'];
    
            if($where) {
                $get_count = DB::select($count_fields . " " . $query . " " . $where);
                $return['recordsFiltered'] = $get_count[0]->tot;
            }
    
            $order = "";
            /*if( isset($req["order"][0]["column"]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
                $order = "order by " . $fields[$req["order"][0]["column"]] . " " . $req["order"][0]["dir"];
            }*/
    
            $data_query = $select_fields . " " . $query . " " . $where . " " . $order;
    
            $records = DB::select($data_query);

            $properties = [];
            $properties['format'] = 'A4-L';

            $vd["records"] = $records;
            return PDF::view('devices/NI/for_export', $vd)->landscape()->format('a4')->name('network_inventory.pdf')->download();
        }
    }



    public function getList(Request $request) {
        
        /* -- */
        // $lifetime_end = Carbon::createFromFormat('Y-m-d H:i:s', '2019-08-23 12:00:00', config('app.timezone'))->addDays(90);
        // $present_time = Carbon::now(config('app.timezone'));
        // if( $lifetime_end->lessThan($present_time) ) {
        //     return response()->json(["msg" => "Sorry for Inconveince. Please contact admin"]);
        // }
        /* -- */
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('NIRead') || ! config("services.network_inventory.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $model_id = null;
        $location = isset($request->location) ? $request->location : null;
        $department = isset($request->department) ? $request->department : null;
        $assign_user_location = isset($request->assign_user_location) ? $request->assign_user_location : "null";

        if(isset($request->model) && $request->model != "null"){
            $model_id = $request->model;
        }
        if(isset($request->category) && $request->category != "null"){
            $catId = explode(',',  $request->category);
            $model_id = Model::whereIn('category_id',$catId)->pluck('id')->toArray();
            $model_id = empty($model_id) ? "0" : implode(',',  $model_id);
        }
        if (isset($request->mdl_name)){
            $modelId = Model::where('name', $request->mdl_name)->pluck('id')->toArray();
            $model_id = empty($modelId) ? "0" : implode(',',  $modelId); 
        }
        $syncdate = isset($request->date) ? $request->date : null;
        $vd = new stdClass();
        $vd->manufacturers = Basic::manufacturerOptions();
        $vd->models = Basic::modelOptions();
        $vd->oss = Basic::osOptions();
        $vd->version = Basic::versionOptions();
        $vd->computerSystemTypes = Basic::computerSystemTypeOptions();
        $vd->processorNames = Basic::processorNameOptions();
        $vd->internal_places = Place::select('id', 'place as text')->orderBy('place')->get();
        return view("devices.NI.get_list")->with("vd", $vd)->with("location", $location)->with("department", $department)->with("syncdate", $syncdate)->with('model_id', $model_id)->with('assign_user_location', $assign_user_location);
    }



//     public function getLicense() {
//         $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
//         if(! Auth::user()->hasPermissionTo('NILicenseRead') || ! config("services.network_inventory.enabled")) {
//             return redirect('dashboard')->with("msg", $return);
//         }
//         $vd = new stdClass();
//         $vd->manufacturers = Basic::manufacturerOptions();
//         $vd->models = Basic::modelOptions();
//         $vd->sw_names = Product::captionOptions();
//         $vd->sw_versions = Product::versionOptions();
//         $vd->sw_publishers = Product::publisherOptions();
//         return view("network_inventory.license")->with("vd", $vd);
//     }

//     public function getChanges() {
//         $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
//         if(! Auth::user()->hasPermissionTo('NIChangeDetailsRead') || ! config("services.network_inventory.enabled")) {
//             return redirect('dashboard')->with("msg", $return);
//         }
//         $vd = new stdClass();
//         $vd->manufacturers = Basic::manufacturerOptions();
//         $vd->models = Basic::modelOptions();
//         $vd->vendors = ChangeLog::manufacturerOptions();
//         $vd->change_item = [1 => "Disk Drive", 2=>'Internal Memory',3=>'Software'];
//         $vd->change_type = [1 => "New", 2=>'Change',3=>'Missing'];        
//         return view("network_inventory.changelist")->with("vd", $vd);
//     }

   public function ajaxList(Request $request) {
        $req = $request->all();
        $return = array(
            // "draw" => date('is')
        );

        $fields = array(
            '0' => 'assets.asset_tag',
            '1' => 'b.ComputerName',
            '2' => 'b.ComputerDomain',
            '3' => 'b.OSCaption',
            '4' => 'b.HddSize',
            '6' => 'b.IPv4',
            '7' => 'b.updated_at'
        );

        $db = Basic::from('itm_network_inventory_basic as b')->select('assets.asset_tag', 'b.ComputerName', 'b.ComputerManufacturer', 'b.ComputerModel', 'b.BIOSSerialNumber', 'b.ComputerDomain', 'b.OSCaption', 'b.ActiveMACAddress', 'b.IPv4','b.IPv6', DB::raw('DATE_FORMAT(b.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'),  'b.id AS basic_id', 'assets.asset_tag', 'assets.id as asset_id', 'b.HddSize', 'b.RamSize', 'b.is_virtual', 'b.ProcessorName', 'b.ComputerSystemType', 'b.SoftwareLicensingProduct','ni_loc.name as ni_detected_location_name', 'is_AD', 'agentVersion')
            ->leftjoin('assets', 'b.device_id', '=', 'assets.id')
            ->leftjoin('users','users.id','=','assets.assigned_to')
            ->leftJoin('models as mdl', 'mdl.id', '=', 'assets.model_id')
            ->leftJoin('locations as ni_loc', 'ni_loc.id', '=', 'assets.ni_detected_location')
            ->leftJoin('categories as cat', function($q) {
                $q->on('cat.id', '=', 'mdl.category_id');
                $q->where('mdl.category_id', '<>', 0);
            })
            // ->leftJoin('itm_network_inventory_products as itm', 'itm.basic_id', '=', 'b.id')
            // ->leftJoin('itm_network_user_accounts as ua', 'b.id', '=', 'ua.basic_id')
            ->whereNull('b.is_dupe')
            ->whereNull('assets.deleted_at');
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn('assets.company_id', $companyIds);

        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        // if($settings->location_config == 1) {
        //     if(empty($loc_previllage)) {
        //         $db->where('assets.rtd_location_id', '=', 0);
        //     } else {
        //         $db->whereIn('assets.rtd_location_id', $permitted_loc);
        //     }
        // }

        // check asset dept permission
        // if ($settings->department_config == 1) {
        //     $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
        //     $asset_depts = explode(",", $asset_dept_permission);
        //     if (empty($asset_dept_permission)) {
        //         $db->where('assets.department_id', '=', 0);
        //     } else {
        //         $db->whereIn('assets.department_id', $asset_depts);
        //     }
        // }

        $location_filter = $request->input("location", null);
        if(!empty($location_filter) && $location_filter != 'null') {
            $db->whereIn("assets.rtd_location_id", explode(",", $location_filter));
        }
        $department_filter = $request->input("department", null);
        if(!empty($department_filter) && $department_filter != 'null') {
            $db->whereIn("assets.department_id", explode(",", $department_filter));
        }
        $model_id_filter = $request->input("model_id", null);
        if($model_id_filter != null && $model_id_filter != 'null') { 
            $db->whereIn("assets.model_id", explode(",", $model_id_filter));
        }

        $assign_user_location = $request->input("assign_user_location", null);
        if($assign_user_location != null && $assign_user_location != 'null') { 
            $db->where('assets.status_id',6)->where('assets.assigned_for',1)->whereIn("users.location_id", explode(",", $assign_user_location));
        }
        $syncdate = $request->input("sync_date", null);
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        if (!empty($syncdate) && $syncdate != 'null') {
            if ($syncdate == $today->toDateString()) {
                $db->whereDate('b.updated_at', '!=', $today);
            } elseif ($syncdate == $yesterday->toDateString()) {
                $db->whereDate('b.updated_at', '!=', $yesterday);
            } else {
                $syncdate1 = date("Y-m-d", strtotime($syncdate));
                $today1 = date("Y-m-d", strtotime($today));
                $db->where(function($query) use ($syncdate1, $today1) {
                    $query->whereDate('b.updated_at', '<', $syncdate1)
                          ->orWhereDate('b.updated_at', '>', $today1);
                });
            }
        }
        $is_searching = false;
        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
            $whereStr = sprintf('(assets.asset_tag like "%%%1$s%%" or b.ComputerName like "%%%1$s%%" or b.ComputerManufacturer like "%%%1$s%%" or b.ComputerModel like "%%%1$s%%" or b.BIOSSerialNumber like "%%%1$s%%" or b.ComputerDomain like "%%%1$s%%" or b.OSCaption like "%%%1$s%%" or b.ActiveMACAddress like "%%%1$s%%" or b.IPv4 like "%%%1$s%%" or b.HddSize like "%%%1$s%%" or DATE_FORMAT(b.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or b.RamSize like "%%%1$s%%" or b.ProcessorName like "%%%1$s%%" or b.ComputerSystemType like "%%%1$s%%" or ni_loc.name like "%%%1$s%%" or b.agentVersion like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $is_searching = true;
        }

        if(isset($req["filters"])) {
            $filters = $req["filters"];

            if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                $db->where("b.ComputerManufacturer", "like", $filters['manufacturer']);
            }
            if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                $db->where("cat.id", "like", $filters['category']);
            }
            if(isset($filters["model"]) && $filters['model'] && $filters['model'] != "null") {
                $db->where("b.ComputerModel", "like", $filters['model']);
            }
            if(isset($filters["os"]) && $filters['os'] && $filters['os'] != "null") {
                $db->where("b.OSCaption", "like", $filters['os']);
            }
            if(isset($filters["version"]) && $filters['version'] && $filters['version'] != "null") {
                $db->where("b.OSVersion", "like", $filters['version']);
            }
            if(isset($filters["processor_name"]) && $filters['processor_name'] && $filters['processor_name'] != "null") {
                $db->where("b.ProcessorName", "like", $filters['processor_name']);
            }
            if(isset($filters["computer_system_type"]) && $filters['computer_system_type'] && $filters['computer_system_type'] != "null") {
                $db->where("b.ComputerSystemType", "like", $filters['computer_system_type']);
            }
            if(isset($filters["platform"]) && $filters['platform'] && $filters['platform'] != "null") {
                $db->where("b.platform", "like", $filters['platform']);
            }
            if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                $db->where("assets.rtd_location_id", "=", (int) $filters['location']);
            }
            if (isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                $db->whereIn("assets.internal_place_id", $filters['internal_place']);
            }
            /*if(isset($filters["program"]) && $filters['program'] && $filters['program'] != "null") {
                $db->where("itm.Caption", "like", $filters['program']);
            }*/
            if(isset($filters["hdd"]) && $filters['hdd'] && $filters['hdd'] != "null") {
                $hddSize = (float) $filters['hdd'];
                $cond = "=";
                if((isset($filters["hdd_cond"]) && $filters['hdd'] && $filters['hdd'] != "null")) {
                    switch($filters["hdd_cond"]) {
                        case 1:
                            $cond = ">";
                            break;
                        case 3:
                            $cond = "<";
                            break;
                        default:
                            $cond = "=";
                    }
                }
                if ($cond === "=") {
                    $db->whereBetween("b.HddSize", [$hddSize - 0.01, $hddSize + 0.01]);
                } else {
                    $db->where("b.HddSize", $cond, $filters['hdd']);
                }
            }
            if(isset($filters["ram"]) && $filters['ram'] && $filters['ram'] != "null") {
                $cond = "=";
                if((isset($filters["ram_cond"]) && $filters['ram'] && $filters['ram'] != "null")) {
                    switch($filters["ram_cond"]) {
                        case 1:
                            $cond = ">"; 
                            break;
                        case 3:
                            $cond = "<"; 
                            break;
                        default:
                            $cond = "="; 
                    }
                }
                $db->where("b.RamSize", $cond, $filters['ram']);
            }
            if(isset($filters["genuine_status"]) && $filters['genuine_status'] && $filters['genuine_status'] != "null") {
                $whereStr = sprintf('(b.SoftwareLicensingProduct like "%%%1$s%%")', '\"GenuineStatus\":\"'.$filters["genuine_status"]);
                $db->whereRaw($whereStr);
            }
            if(isset($filters["license_status"]) && $filters['license_status']!= "" && $filters['license_status'] != "null") {
                $whereStr = sprintf('(b.SoftwareLicensingProduct like "%%%1$s%%")', '\"LicenseStatus\":\"'.$filters["license_status"]);
                $db->whereRaw($whereStr);
            }
            $based_on_possible = ['1'=>'b.created_at', '2'=>'b.updated_at'];
            if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
                if(isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                    $daterange = explode(" - ", $filters["date_range"]);
                    $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                    $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                    if($from_date && $to_date) {
                        $whereStr = sprintf(
                            'DATE(%1$s) BETWEEN "%2$s" AND "%3$s"',
                            $based_on_possible[$filters['based_on']],
                            $from_date,
                            $to_date
                        );      
                        $db->whereRaw($whereStr);
                    }
                }
            }

            if (isset($filters["ad_nonad_agent"]) && $filters['ad_nonad_agent'] !== "" && $filters['ad_nonad_agent'] !== "null") {
                $db->where("b.is_AD", "=",(int) $filters['ad_nonad_agent']);
            }
        }
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if($is_searching) {
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order"][0]["column"]) && array_key_exists($req["order"][0]["column"], $fields) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
            $dir = $req["order"][0]["dir"];
            $db->orderBy($fields[$req["order"][0]["column"]], $dir);
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
        $current_user_id = Auth::user()->id;
        foreach($data as $d) {
        //            $tmp_db_query = DB::select('select count(id) as tot, sum(case when (find_in_set("'. $current_user_id . '", notified) = 0 or find_in_set("'. $current_user_id . '", notified) is null) then 1 else 0 end) as not_notified from itm_network_change_logs where basic_id = "' . $d->basic_id .'"');
        //            $d->pending_notifices = $tmp_db_query[0]->tot && $tmp_db_query[0]->not_notified ? 1 : 0;
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }


    public function ajaxPrograms(Request $request, $id) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            '0'  => 'caption',
            '1'  => 'version',
            '2'  => 'publisher',
            '3'  => 'pr.InstalledDate'
        );

        $db = DB::table('itm_network_inventory_basic as a');
        $db->join('itm_network_inventory_products as pr', 'pr.basic_id', '=', 'a.id');
       
        $db->select('pr.Caption as caption', 'pr.Version as version', 'pr.InstalledDate','pr.Publisher as publisher');
        $db->addSelect(DB::raw('DATE_FORMAT(pr.InstalledDate, "%d/%m/%Y") as installed_date'));
       

        $return['recordsTotal'] = $db->where('a.id','=',$id)->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        
        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
            $whereStr = sprintf('(pr.Caption like "%%%1$s%%" or pr.Version like "%%%1$s%%" or pr.Publisher like "%%%1$s%%" or DATE_FORMAT(pr.InstalledDate, "%%d/%%m/%%Y") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->where('a.id','=',$id)->count();
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
        
        $data = $db->where('a.id','=',$id)->get();
      
        $return['data'] = array();
        foreach($data as $d) {
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    // public function ajaxChangeList(Request $request, $id) {
    //     $req = $request->all();
    //     $return = array(
    //         "draw" => date('is')
    //     );

    //     $fields = array(
    //         '0'  => 'created_at',
    //         '1'  => 'change_lbl',
    //         '2'  => 'change_item',
    //         '3'  => 'caption',
    //         // '4'  => 'caption',
    //     );

    //     $db = Basic::from('itm_network_change_logs as cl');
    //     // $db->join('itm_network_change_logs as cl', 'cl.basic_id', '=', 'a.id');
       
    //     $db->select('cl.SerialNumber as SerialNumber','cl.Caption as caption','cl.id as cl_id', 'cl.created_at');
    //     $db->addSelect(DB::raw('DATE_FORMAT(cl.created_at, "%d %b %Y %h:%i %p") as updated_at_format'));
    //     $db->addSelect(DB::raw('case when cl.cl_type = 1 then "New" when cl.cl_type = 2 then "Change" when cl.cl_type = 3 then "Missing" end as change_lbl'));
    //     $db->addSelect(DB::raw('case when cl.cl_item = 1 then "Disk Drive" when cl.cl_item = 2 then "Internal Memory" when cl.cl_item = 3 then "Software" end as change_item'));

    //     $db->where('cl.basic_id','=',$id);

    //     $is_searching = false;
    //     if(isset($req["filters"])) {
    //         $filters = $req["filters"];

    //         if(isset($filters["change_item"]) && $filters['change_item'] && $filters['change_item'] != "null") {
    //             $db->where('cl.cl_item',$filters['change_item']);
    //         }
    //         if(isset($filters["change_type"]) && $filters['change_type'] && $filters['change_type'] != "null") {
    //             $db->where("cl.cl_type",$filters['change_type']);
    //         }
    //         $is_searching = true;
    //     }
        
    //     if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
    //         $whereStr = sprintf('(cl.SerialNumber like "%%%1$s%%" or cl.Caption like "%%%1$s%%" or (case when cl.cl_item = 1 then "Disk Drive" when cl.cl_item = 2 then "Internal Memory" when cl.cl_item = 3 then "Software" else "" end) like "%%%1$s%%" or (case when cl.cl_type = 1 then "Newly Added" when cl.cl_type = 2 then "Changes Detected" when cl.cl_type = 3 then "Missing" else "" end) like "%%%1$s%%" or DATE_FORMAT(cl.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
    //         $db->whereRaw($whereStr);
    //         $return['recordsFiltered'] = $db->count();
    //     }

    //     if( isset($req["order"][0]["column"]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
    //         if(isset($fields[$req["order"][0]["column"]])) {
    //             $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
    //         }
    //     }

    //     $return['recordsTotal'] = $db->count();
    //     $return['recordsFiltered'] = $return['recordsTotal'];

    //     $skip = 0;
    //     $take = 10;
    //     if( isset($req["start"]) && isset($req["length"]) ) {
    //         $skip = (int) $req["start"];
    //         $take = (int) $req["length"];
    //     }
    //     $db->skip($skip);
    //     $db->take($take);
        
    //     $data = $db->get();

    //     $return['data'] = array();
    //     foreach($data as $d) {
    //         $return['data'][] = array('a' => $d);
    //     }

    //     return response()->json($return);
    // }

    // public function ajaxLicense(Request $request) {
    //     $req = $request->all();
    //     $return = array(
    //         "draw" => date('is')
    //     );

    //     $fields = array(
    //         '0' => 'assets.asset_tag',
    //         '1' => 'b.BIOSSerialNumber',
    //         '2' => 'b.ComputerManufacturer',
    //         '3' => 'b.ComputerModel',
    //         '4' => 'loc.name',
    //         '5' => 'sw.Caption',
    //         '6' => 'sw.Version',
    //         '7' => 'sw.Publisher',
    //         '8' => 'sw.installedDate'
    //     );
    //     $db = Basic::from('itm_network_inventory_basic as b')->select('assets.asset_tag', 'b.ComputerName', 'b.ComputerManufacturer', 'b.ComputerModel', 'b.BIOSSerialNumber',  DB::raw('DATE_FORMAT(b.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'),  'b.id AS basic_id', 'assets.asset_tag', 'assets.id as asset_id','loc.name as loc_name', 'sw.Caption as sw_name', 'sw.Version as sw_version', 'sw.Publisher', DB::raw('DATE_FORMAT(sw.installedDate, "%d %b %Y %h:%i %p") as install_date'))
    //         ->leftjoin('assets', 'b.device_id', '=', 'assets.id')
    //         ->leftjoin('itm_network_inventory_products as sw', 'b.id', '=', 'sw.basic_id')
    //         ->leftjoin('locations as loc', 'assets.rtd_location_id', '=', 'loc.id')
    //         ->whereNull('b.is_dupe')
    //         ->whereNull('b.deleted_at');

    //     $is_searching = false;
    //     if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
    //         $whereStr = sprintf('(assets.asset_tag like "%%%1$s%%" or b.ComputerName like "%%%1$s%%" or b.ComputerManufacturer like "%%%1$s%%" or b.ComputerModel like "%%%1$s%%" or b.BIOSSerialNumber like "%%%1$s%%" or loc.name like "%%%1$s%%" or sw.Caption like "%%%1$s%%" or sw.Version like "%%%1$s%%" or sw.Publisher like "%%%1$s%%"  or DATE_FORMAT(sw.installedDate, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
    //         $db->whereRaw($whereStr);
    //         $is_searching = true;
    //     }
    //     if(isset($req["filters"])) {
    //         $filters = $req["filters"];

    //         if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
    //             $db->whereIn("b.ComputerManufacturer", $filters['manufacturer']);
    //         }
    //         if(isset($filters["model"]) && $filters['model'] && $filters['model'] != "null") {
    //             $db->whereIn("b.ComputerModel", $filters['model']);
    //         }
    //         if(isset($filters["sw_name"]) && $filters['sw_name'] && $filters['sw_name'] != "null") {
    //             $db->whereIn("sw.Caption", $filters['sw_name']);
    //         }
    //         if(isset($filters["publisher"]) && $filters['publisher'] && $filters['publisher'] != "null") {
    //             $db->whereIn("sw.Publisher", $filters['publisher']);
    //         }
    //         if(isset($filters["sw_version"]) && $filters['sw_version'] && $filters['sw_version'] != "null") {
    //             $db->whereIn("sw.Version", $filters['sw_version']);
    //         }
    //         if(isset($filters["platform"]) && $filters['platform'] && $filters['platform'] != "null") {
    //             $db->whereIn("b.platform", $filters['platform']);
    //         }
    //     }
    //     $return['recordsTotal'] = $db->count();
    //     $return['recordsFiltered'] = $return['recordsTotal'];

    //     if($is_searching) {
    //         $return['recordsFiltered'] = $db->count();
    //     }

    //     if( isset($req["order"][0]["column"]) && array_key_exists($req["order"][0]["column"], $fields) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
    //         $dir = $req["order"][0]["dir"];
    //         $db->orderBy($fields[$req["order"][0]["column"]], $dir);
    //     }

    //     $skip = 0;
    //     $take = 10;
    //     if( isset($req["start"]) && isset($req["length"]) ) {
    //         $skip = (int) $req["start"];
    //         $take = (int) $req["length"];
    //     }
    //     $db->skip($skip);
    //     $db->take($take);

    //     $data = $db->get();
    //     $return['data'] = array();
    //     foreach($data as $d) {
    //         $return['data'][] = array('a' => $d);
    //     }

    //     return response()->json($return);
    // }

//     public function exportajaxLicense(Request $request) {
//         $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
//         if(! Auth::user()->hasPermissionTo('NILicenseDownload') || ! config("services.network_inventory.enabled")) {
//             return redirect('dashboard')->with("msg", $return);
//         }
//         $select_fields = "select b.ComputerName, assets.asset_tag, b.BIOSSerialNumber, b.ComputerManufacturer, b.ComputerModel, loc.name as loc_name, sw.Caption as sw_name, sw.Version as sw_version, sw.Publisher, DATE_FORMAT(sw.installedDate, '%d %b %Y') as install_date";

//         $count_fields = "select count(*) as tot";

//         $query = "FROM itm_network_inventory_basic as b 
//         left join assets on b.BIOSSerialNumber like assets.serial
//         left join itm_network_inventory_products as sw on b.id = sw.basic_id
//         left join locations as loc on assets.rtd_location_id = loc.id where b.is_dupe is null and b.deleted_at is null";

//         $where = "";
//         if($request->q) {
//             $request_filters = base64_decode($request->q);
//             $filters = json_decode($request_filters);
//             $req = [];

//             if(isset($filters->search)) {
//                 $req["search"]["value"] = $filters->search;
//             }

//             if(isset($filters->other_filters)){
//                 $req["filters"] = (array) $filters->other_filters;
//             }


//             if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
//                 $where = 'AND (assets.asset_tag like "%'. $search_key .'%" or b.ComputerName like "%'. $search_key .'%" or b.ComputerManufacturer like "%'. $search_key .'%" or b.ComputerModel like "%'. $search_key .'%" or b.BIOSSerialNumber like "%'. $search_key .'%" or loc.name like "%'. $search_key .'%" or sw.Caption like "%'. $search_key .'%" or sw.Version like "%'. $search_key .'%" or sw.Publisher like "%'. $search_key .'%" or DATE_FORMAT(sw.installedDate,"%d %b %Y") like "%'. $search_key .'%")';
//             }


//             $where_arr = [];
//             if(isset($req["filters"])) {
//                 $filters = $req["filters"];

//                 $filter_cond = "";
//                 if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
//                     $where_arr[] = "b.ComputerManufacturer like '". $filters['manufacturer'] . "'";
//                 }
//                 if(isset($filters["model"]) && $filters['model'] && $filters['model'] != "null") {
//                     $where_arr[] = "b.ComputerModel like '". $filters['model'] . "'";
//                 }
//                 if(isset($filters["sw_name"]) && $filters['sw_name'] && $filters['sw_name'] != "null") {
//                     $text = "";
//                     foreach($filters['sw_name'] as $soft) {
//                         if($text == "") {
//                             $text = "'$soft";
//                         } else {
//                             $text = $text ."', '".$soft;
//                         }
//                     }
//                     $sw_name = implode(",", $filters['sw_name']);
//                     $where_arr[] = "sw.Caption IN(". $text ."')";
//                 }
//                 if(isset($filters["publisher"]) && $filters['publisher'] && $filters['publisher'] != "null") {
//                     $where_arr[] = "sw.Publisher like '". $filters['publisher'] . "'";
//                 }
//                 if(isset($filters["sw_version"]) && $filters['sw_version'] && $filters['sw_version'] != "null") {
//                     $where_arr[] = "sw.Version like '". $filters['sw_version'] . "'";
//                 }
//                 if(isset($filters["platform"]) && $filters['platform'] && $filters['platform'] != "null") {
//                     $where_arr[] = "b.platform = ". $filters['platform'] . "";
//                 }

//                 if(count($where_arr)) {
//                     $condition = implode(" and ", $where_arr);
//                     $where .= $where ?  (" and " . $condition) : (" and " . $condition);
//                 }
//             }


//             $get_count = DB::select($count_fields . " " . $query);
//         }
//         $return['recordsTotal'] = $get_count[0]->tot;
//         $return['recordsFiltered'] = $return['recordsTotal'];


//         if($where) {
//             $get_count = DB::select($count_fields . " " . $query . " " . $where);
//             $return['recordsFiltered'] = $get_count[0]->tot;
//         }

//         $order = "";
//         /*if( isset($req["order"][0]["column"]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
//             $order = "order by " . $fields[$req["order"][0]["column"]] . " " . $req["order"][0]["dir"];
//         }*/

//         $data_query = $select_fields . " " . $query . " " . $where . " " . $order;

//         $records = DB::select($data_query);
       

//         /*if($get_count[0]->tot > 10000) {
//             $return = ["status"=>"danger", "msg"=>trans('content.network_inv_fields.license_records')];
//             $request->session()->flash("msg", $return);
//             return redirect("devices/network_inventory/license_list");
//         }*/
//         $data = collect($records)->map(function($x){ return (array) $x; })->toArray();
//         // return (new FastExcel($this->licenceGenerator($data)))->download('LicenseDetails.xlsx');  
//         return (new FastExcel($this->licenceGenerator($data)))->download('LicenseDetails.xlsx', function($licence) {
//             return [
//                 'Device Name' => $licence['ComputerName'],
//                 'Device Tag' => $licence['asset_tag'],
//                 'Serial No' => $licence['BIOSSerialNumber'],
//                 'Manufacturer' => $licence['ComputerManufacturer'],
//                 'Device Modal' => $licence['ComputerModel'],
//                 'Location Name' => $licence['loc_name'],
//                 'License Name' => $licence['sw_name'],
//                 'Version' => $licence['sw_version'],
//                 'Publisher' => $licence['Publisher'],
//                 'Installation Date' => $licence['install_date'],
//             ];
//         }); 
//     }

//     public function licenceGenerator($data) {
//         foreach ($data as $licence) {
//             yield $licence;
//         }
//     }

//     public function ajaxLicensePDF(Request $request) {

//         $return = ['status' => 'danger', 'msg' => trans('content.network_inv_fields.Unable_to_export_the_details')];
//         $vd = [];

//         $select_fields = "select assets.asset_tag, b.ComputerName, b.ComputerManufacturer, b.ComputerModel, b.BIOSSerialNumber, DATE_FORMAT(b.updated_at, '%d %b %Y') as updated_at_format, b.id AS basic_id, assets.asset_tag, assets.id as asset_id, loc.name as loc_name, sw.Caption as sw_name, sw.Version as sw_version, sw.Publisher, DATE_FORMAT(sw.installedDate, '%d %b %Y') as install_date";

//         $count_fields = "select count(*) as tot";

//         $query = "FROM itm_network_inventory_basic as b 
//         left join assets on b.BIOSSerialNumber like assets.serial
//         left join itm_network_inventory_products as sw on b.id = sw.basic_id
//         left join locations as loc on assets.rtd_location_id = loc.id where b.is_dupe is null and b.deleted_at is null";

//         if($request->q) {
//             $request_filters = base64_decode($request->q);
//             $filters = json_decode($request_filters);
//             $req = [];
            
//             if(isset($filters->search)) {
//                 $req["search"] = $filters->search;
//             }

//             if(isset($filters->other_filters)){
//                 $req["filters"] = (array) $filters->other_filters;
//             }

//             $where = "";
//             if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
//                 $where = 'AND (assets.asset_tag like "%'. $search_key .'%" or b.ComputerName like "%'. $search_key .'%" or b.ComputerManufacturer like "%'. $search_key .'%" or b.ComputerModel like "%'. $search_key .'%" or b.BIOSSerialNumber like "%'. $search_key .'%" or loc.name like "%'. $search_key .'%" or sw.Caption like "%'. $search_key .'%" or sw.Version like "%'. $search_key .'%" or sw.Publisher like "%'. $search_key .'%" or DATE_FORMAT(sw.installedDate,"%d %b %Y") like "%'. $search_key .'%")';
//             }
            

//             $where_arr = [];
//             if(isset($req["filters"])) {
//                 $filters = $req["filters"];
    
//                 $filter_cond = "";
//                 if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
//                     $where_arr[] = "b.ComputerManufacturer like '". $filters['manufacturer'] . "'";
//                 }
//                 if(isset($filters["model"]) && $filters['model'] && $filters['model'] != "null") {
//                     $where_arr[] = "b.ComputerModel like '". $filters['model'] . "'";
//                 }
//                 if(isset($filters["sw_name"]) && $filters['sw_name'] && $filters['sw_name'] != "null") {
//                     $where_arr[] = "sw.Caption like '". $filters['sw_name'] . "'";
//                 }
//                 if(isset($filters["publisher"]) && $filters['publisher'] && $filters['publisher'] != "null") {
//                     $where_arr[] = "sw.Publisher like '". $filters['publisher'] . "'";
//                 }
//                 if(isset($filters["sw_version"]) && $filters['sw_version'] && $filters['sw_version'] != "null") {
//                     $where_arr[] = "sw.Version like '". $filters['sw_version'] . "'";
//                 }
//                 if(isset($filters["platform"]) && $filters['platform'] && $filters['platform'] != "null") {
//                     $where_arr[] = "b.platform = ". $filters['platform'] . "";
//                 }
    
//                 if(count($where_arr)) {
//                     $condition = implode(" and ", $where_arr);
//                     $where .= $where ?  (" and " . $condition) : (" and " . $condition);
//                 }
//             }

//         }
            
//             $get_count = DB::select($count_fields . " " . $query);
    
//             $return['recordsTotal'] = $get_count[0]->tot;
//             $return['recordsFiltered'] = $return['recordsTotal'];
    
//             if($where) {
//                 $get_count = DB::select($count_fields . " " . $query . " " . $where);
//                 $return['recordsFiltered'] = $get_count[0]->tot;
//             }
    
//             $order = "";
//             if( isset($req["order"][0]["column"]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
//                 $order = "order by " . $fields[$req["order"][0]["column"]] . " " . $req["order"][0]["dir"];
//             }
    
//             $data_query = $select_fields . " " . $query . " " . $where . " " . $order;
    
//             $records = DB::select($data_query);

//             $properties = [];
//             $properties['format'] = 'Legal-L';

//             $vd["records"] = $records;
//             $pdf = PDF::loadView('network_inventory.for_export', $vd, [], $properties);
//             return $pdf->download('License detail.pdf');
        
//     }

//     public function ajaxChanges(Request $request) {
//         $req = $request->all();
//         $return = array(
//             "draw" => date('is')
//         );

//         $fields = array(
//             '0' => 'assets.asset_tag',
//             '1' => 'b.BIOSSerialNumber',
//             '2' => 'b.ComputerManufacturer',
//             '3' => 'b.ComputerModel',
//             '4' => 'change_lbl',
//             '5' => 'change_item',
//             '6' => 'cl.Caption',
//             '7' => 'cl.SerialNumber',
//             '8' => 'cl.Manufacturer',
//             '9' => 'memory_size',
//             '10' => 'cl.MemoryType',
//             '11' => 'cl.updated_at'
//         );
//         $devices = ChangeLog::from('itm_network_change_logs as cl')->select('assets.asset_tag', 'b.ComputerName', 'b.ComputerManufacturer','b.ComputerModel', 'b.BIOSSerialNumber', 'b.id AS basic_id', 'assets.asset_tag', 'assets.id as asset_id', 'cl.Caption', 'cl.Manufacturer', 'cl.SerialNumber','cl.SerialNumber', 'cl.MemoryType', DB::raw('case when cl.cl_item = 1 then cl.Size when cl.cl_item = 2 then cl.Capacity end as memory_size'), DB::raw('case when cl.cl_type = 1 then "New" when cl.cl_type = 2 then "Change" when cl.cl_type = 3 then "Missing" end as change_lbl '),
//             DB::raw('case when cl.cl_item = 1 then "Disk Drive" when cl.cl_item = 2 then "Internal Memory" when cl.cl_item = 3 then "Software" end as change_item'), DB::raw('DATE_FORMAT(cl.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'))
//             ->leftjoin('itm_network_inventory_basic as b', 'b.id', 'cl.basic_id')
//             ->leftjoin('assets', 'assets.serial', 'b.BIOSSerialNumber');


//         $select_fields = "select assets.asset_tag, b.ComputerName, b.ComputerManufacturer, b.ComputerModel, b.BIOSSerialNumber,  b.id AS basic_id, assets.asset_tag, assets.id as asset_id, cl.Caption, cl.Manufacturer, cl.SerialNumber, cl.MemoryType, case when cl.cl_item = 1 then cl.Size when cl.cl_item = 2 then cl.Capacity end as memory_size, case when cl.cl_type = 1 then 'New' when cl.cl_type = 2 then 'Change' when cl.cl_type = 3 then 'Missing' end as change_lbl,
//         case when cl.cl_item = 1 then 'Disk Drive' when cl.cl_item = 2 then 'Internal Memory' when cl.cl_item = 3 then 'Software' end as change_item, DATE_FORMAT(cl.updated_at, '%d %b %Y %h:%i %p') as updated_at_format";

//         $count_fields = "select count(*) as tot";

//         $query = "FROM itm_network_change_logs as cl 
//         join itm_network_inventory_basic as b on cl.basic_id = b.id and b.deleted_at is null
//         left join assets on b.BIOSSerialNumber like assets.serial";
//         //left join locations as loc on assets.rtd_location_id = loc.id where b.is_dupe is null";
//         $return['recordsTotal'] = $devices->count();

//         $where = "";
//         if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
//             $devices->where('assets.asset_tag', 'like', '%'.$search_key.'%')->orWhere('b.ComputerName', 'like', '%'.$search_key.'%')->orWhere('b.ComputerManufacturer', 'like', '%'.$search_key.'%')->orWhere('b.ComputerModel', 'like', '%'.$search_key.'%')->orWhere('b.BIOSSerialNumber', 'like', '%'.$search_key.'%')->orWhere('cl.Caption', 'like', 'like', '%'.$search_key.'%')->orWhere('cl.SerialNumber', 'like', 'like', '%'.$search_key.'%')->orWhere('cl.Manufacturer', 'like', 'like', '%'.$search_key.'%')->orWhere('cl.MemoryType', 'like', 'like', '%'.$search_key.'%')->orWhereRaw('DATE_FORMAT(cl.updated_at, "%d %b %Y %h:%i %p") like "%'. $search_key .'%"')->orWhereRaw(('case when cl.cl_type = 1 then "New" when cl.cl_type = 2 then "Change" when cl.cl_type = 3 then "Missing" else "" end'), 'like', '%'. $search_key .'%')->orWhereRaw(('case when cl.cl_item = 1 then "Disk Drive" when cl.cl_item = 2 then "Internal Memory" when cl.cl_item = 3 then "Software" else "" end'), 'like', '%'. $search_key .'%');
//             $where = 'AND (assets.asset_tag like "%'. $search_key .'%" or b.ComputerName like "%'. $search_key .'%" or b.ComputerManufacturer like "%'. $search_key .'%" or b.ComputerModel like "%'. $search_key .'%"  or b.BIOSSerialNumber like "%'. $search_key .'%" or (case when cl.cl_type = 1 then "New" when cl.cl_type = 2 then "Change" when cl.cl_type = 3 then "Missing" else "" end) like "%'. $search_key .'%" or (case when cl.cl_item = 1 then "Disk Drive" when cl.cl_item = 2 then "Internal Memory" when cl.cl_item = 3 then "Software" else "" end) like "%'. $search_key .'%" or cl.Caption like "%'. $search_key .'%" or cl.SerialNumber like "%'. $search_key .'%" or cl.Manufacturer like "%'. $search_key .'%"  or (case when cl.cl_item = 1 then cl.Size when cl.cl_item = 2 then cl.Capacity else "" end) like "%'. $search_key .'%" or cl.MemoryType like "%'. $search_key .'%" or  DATE_FORMAT(cl.updated_at, "%d %b %Y %h:%i %p") like "%'. $search_key .'%")';
//         }

//         $where_arr = [];
//         if(isset($req["filters"])) {
//             $filters = $req["filters"];

//             $filter_cond = "";
//             if(isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
//                 $devices->where("b.ComputerManufacturer", "like",$filters['manufacturer']);
//             }
//             if(isset($filters["model"]) && $filters['model'] && $filters['model'] != "null") {
//                 $devices->where("b.ComputerModel", "like",$filters['model']);
//             }
//             if(isset($filters["vendor"]) && $filters['vendor'] && $filters['vendor'] != "null") {
//                 $devices->where("cl.Manufacturer", "like",$filters['vendor']);
//             }
//             if(isset($filters["change_item"]) && $filters['change_item'] && $filters['change_item'] != "null") {
//                 $devices->where('cl.cl_item', $filters['change_item']);
//             }
//             if(isset($filters["change_type"]) && $filters['change_type'] && $filters['change_type'] != "null") {
//                 $devices->where("cl.cl_type", $filters['change_type']);
//             }
//             if(isset($filters["memory_size"]) && $filters['memory_size'] && $filters['memory_size'] != "null") {
//                 $cond = "=";
//                 if((isset($filters["memory_size_cond"]) && $filters['memory_size'] && $filters['memory_size'] != "null")) {
//                     switch($filters["memory_size_cond"]) {
//                         case 1:
//                             $cond = ">";
//                             break;
//                         case 3:
//                             $cond = "<";
//                             break;
//                         default:
//                             $cond = "=";
//                     }
//                 }
//                 // $devices->where("case when cl.cl_item = 1 then cl.Size when cl.cl_item = 2 then cl.Capacity end) " . $cond . " ". $filters['memory_size'] . "");
//                 $devices->where("cl.Capacity", $cond, $filters['memory_size']);
//             }

//             if(count($where_arr)) {
//                 $condition = implode(" and ", $where_arr);
//                 $where .= $where ?  (" and " . $condition) : (" and " . $condition);
//             }
//         }

//         // $get_count = DB::select($count_fields . " " . $query);

// //        $return['recordsTotal'] = $devices->count();
//         $return['recordsFiltered'] = $devices->count();

//         if($where) {
//             // $get_count = DB::select($count_fields . " " . $query . " " . $where);
//             $return['recordsFiltered'] = $devices->count();
//         }

//         $order = "";
//         if( isset($req["order"][0]["column"]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
// //            $order = "order by " . $fields[$req["order"][0]["column"]] . " " . $req["order"][0]["dir"];
//             $devices->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
//         }

//         $skip = 0;
//         $take = 10;
//         if( isset($req["start"]) && isset($req["length"]) ) {
//             $skip = (int) $req["start"];
//             $take = (int) $req["length"];
//         }
// //        $limit = " limit " . $skip . "," . $take;
//         $devices->limit($take)->skip($skip);
// //        $data_query = $select_fields . " " . $query . " " . $where . " " . $order . " " . $limit;

//         $data = $devices->get();
//         $return['data'] = array();
//         foreach($data as $d) {
//             $return['data'][] = array('a' => $d);
//         }

//         return response()->json($return);
//     }

//     public function getInfo($id) {
//         $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
//         if(! Auth::user()->hasPermissionTo('NIView') || ! config("services.network_inventory.enabled")) {
//             return redirect('dashboard')->with("msg", $return);
//         }
//         $basic = Basic::find($id);
//         if(!$basic) {
//             return redirect("/")->with("error", "Device does not exist");
//         }

//         $installed_programmes = Product::where("basic_id", "=", $id)->get();
//         $physical_memories = PhysicalMemory::where("basic_id", "=", $id)->get();
//         $video_controllers = VideoController::where("basic_id", "=", $id)->get();
//         $audio_controllers = SoundDevice::where("basic_id", "=", $id)->get();
//         $disk_drives = DiskDrive::where("basic_id", "=", $id)->get();
//         $volumes = Volume::where("basic_id", "=", $id)->get();
//         $monitors = Monitor::where('basic_id', $id)->get();
//         $user_accounts = UserAccount::where("basic_id", "=", $id)->get();
//         $softwareLicensingProduct = (isset($basic->SoftwareLicensingProduct) && $basic->SoftwareLicensingProduct != "") ? json_decode($basic->SoftwareLicensingProduct,true)[0] : [];

//         $device = Device::where('serial','=',($basic->BIOSSerialNumber))->select("id","serial")->first();

//         $vd = new stdClass();
//         $vd->change_item = [1=>"Disk Drive", 2=>'Internal Memory', 3=>'Software'];
//         $vd->change_type = [1=>"New", 2=>'Change', 3=>'Missing'];

//         return view("network_inventory.get_info")->with(compact("basic", "installed_programmes", "physical_memories", "video_controllers", "audio_controllers", "disk_drives", "volumes", "user_accounts", "device", "monitors", "softwareLicensingProduct"))->with("vd", $vd);
//     }

//     public function getChangesInfo($id) {
//         $basic = Basic::find($id);
//         $vd = new stdClass;
//         $vd->changeLogs = ChangeLog::where("basic_id", "=", $id)->orderBy("updated_at", "desc")->get();
//         ChangeLog::markAsNotified($id, Auth::user()->id);
//         return view("network_inventory.changes_info")->with("vd", $vd);
//     }

//     public function getChangeMoreInfo($id) {
//         $vd = new stdClass;
//         $vd->changeLog = ChangeLog::find($id);
//         return view("network_inventory.change_more_info")->with("vd", $vd);
//     }

//     /* Agent - Update the network hosts platform & ports */
//     public function updateRegister(Request $request) {
//         $all = $request->all();
//         Log::error(date("Y-m-d H:i:s"));
//         Log::error(print_r($all, true));

//         $data = $request->only('hosts');

//         $return = ['status'=>'failure', 'msg'=>trans('content.network_inv_fields.Could_not_done_the_update')];

//         if(!is_array($data) || !count($data)) {
//             $return['msg'] = trans('content.network_inv_fields.no_data_has_been_received');
//             return response()->json($return);
//         }

//         $now = new Carbon(config('app.timezone'));

//         foreach($data['hosts'] as $d) {
//             try {
//                 ScanRegister::where('ipv4', '=', $d['ip'])->update([
//                     'ports' => $d['ports'],
//                     'platform' => $d['platform'],
//                     'last_active_at' => $now->format('Y-m-d H:i:s')
//                 ]);
//             }
//             catch(\Exception $e) {
//                 $return['msg'] = trans('content.network_inv_fields.some_data_does_not');
//                 return response()->json($return);
//             }
//         }

//         $return['status'] = 'success';
//         $return['msg'] = '';
//         return response()->json($return);
//     }

//     /* remote validation */
//     public function detectExistingSystem(Request $request) {
//         try {
//             $serial = trim( urldecode($request->serial) );
//             $ip = trim( urldecode($request->ip) );
//             $mac = trim( urldecode($request->mac) );
//             $device = Device::where('serial', 'like', $serial)->first();
//             if($device) {
//                 $url = url('device/info', $device->id);
//                 $str = sprintf("Serial already exists with another device <a href='%s'>%s</a>. If you like to map with it, please <a href='#' id='map-with-exist-device' data-id='%s' data-ip='%s' data-mac='%s'>Click here</a>.", $url, $device->asset_tag, $device->id, $request->ip, $request->mac);
//                 return response()->json($str);
//             }
//         }
//         catch(\Exception $e) {
//             Log::error("detectExistingSystem: " . $e->getMessage());
//         }

//         return response()->json("true");
//     }

//     /* map a host based on serial after user's accept */
//     public function mapHostBySerial(Request $request) {
//         $return = ["status" => "failure", "msg" => trans('content.network_inv_fields.unable_to_map')];
//         try {
//             $device = Device::findOrFail($request->id);
//             $device->ip = $request->ip;
//             $device->mac = $request->mac;

//             if($device->save()) {
//                 $return['status'] = "success";
//                 $return['msg'] = trans('content.network_inv_fields.host/device');
//             }
//         }
//         catch(\Exception $e) {
//             Log::error("mapHostBySerial: " . $e->getMessage());
//         }
//         return response()->json($return);
//     }

//     /* special url for java agent */
//     public function storeScanRangeInformation(Request $request) {
//         $all = $request->all();
//         $headers = collect($request->header())->transform(function ($item) {
//             return $item[0];
//         });
//         Log::error("storeScanRangeInformation: " . date("Y-m-d H:i:s"));
//         Log::error(print_r($all, true));
//         // Log::error(print_r($headers, true));
//         Log::error("----------------------storeScanRangeInformation--------------------------");
//         // return response()->json(["status" => "success"]);

//         $return = ['status'=>'failure', 'msg'=>trans('content.network_inv_fields.could_not_done')];
//         if(!is_array($all) || !count($all)) {
//             $return['msg'] = trans('content.network_inv_fields.no_data_has_been_received_for_process');
//             return response()->json($return);
//         }

//         $now = new Carbon(config('app.timezone'));
//         $total = 0;
//         foreach($all as $d) {
//             if(!isset($d['Ip'])) {
// 				continue;
//             }

//             try {
//                 foreach($d['Data'] as $k=>$v) {
//                     $d['Data'][$k] = ($v == "" || $v == null || $v == "null" || $v == "None") ? null : trim($v);
//                 }

//                 if(!isset($d['Data']["mac"]) || $d['Data']["mac"] == "" || $d['Data']["mac"] == null || $d['Data']["mac"] == "null" || $d['Data']["mac"] == "None") {
//                     $d['Data']["mac"] = null;
//                 }

//                 $mac = CommonHelper::sanitizeMacAddress($d['Data']["mac"]);
//                 $register = ScanRegister::where('ipv4', 'like', $d['Ip'])->first();

//                 $data = [];
//                 $data['device_type'] = $d['Data']['devicetype'];
//                 $data['os'] = $d['Data']['os'];
//                 $data['hostname'] = $d['Data']['host'];
//                 $data['manufacturer'] = $d['Data']['manufacturer'];
//                 $data['others'] = trim($d['Data']['others1'] . " " . $d['Data']['other2']);
//                 $data["last_active_at"] =  $now->format('Y-m-d H:i:s');
                
//                 if($register && $register->id) {
//                     if($mac && $register->mac != $mac) {
//                         $data['mac'] = $mac;
//                     }

//                     $register->fill($data);
//                     $register->save();
//                 }
//                 else {
//                     $data['mac'] = $mac;
//                     $data['ipv4'] = $d['Ip'];
//                     $register = ScanRegister::create($data);
//                 }

//                 // Log::error("Hey Device is: ");
//                 // Log::error(print_r($data, true));
//                 $total++;
//             }
//             catch(\Exception $e) {
//                 Log::error($e->getMessage());
//                 continue;
//             }
//         }
        
//         return response()->json(["status" => "success", "msg" => trans('content.network_inv_fields.total_received') . $total++]);
//     }

//     /* Agent - Register the scanned the network active IPs */
//     public function scanRegister(Request $request) {
//         $all = $request->all();
//         // Log::error(date("Y-m-d H:i:s"));
//         Log::info("scanRegister: " . print_r($all, true));

//         $data = $request->only('hosts');

//         $return = ['status'=>'failure', 'msg'=>trans('content.network_inv_fields.Could_not_done_the_update')];

//         if(!is_array($data) || !count($data)) {
//             $return['msg'] = trans('content.network_inv_fields.no_data_has_been_received_for_process');
//             return response()->json($return);
//         }

//         $need_to_scan = [];

//         $now = new Carbon(config('app.timezone'));

//         foreach($data['hosts'] as $d) {
//             if(!isset($d['ipv4'])) {
// 				continue;
// 			}
			
//             if(!isset($d["mac"]) || $d["mac"] == "" || $d["mac"] == null || $d["mac"] == "null" || $d["mac"] == "None") {
//                 $d["mac"] = null;
//             }

//             foreach($d as $k=>$v) {
//                 $d[$k] = ($v == "" || $v == null || $v == "null" || $v == "None") ? null : trim($v);
//             }

//             $d["mac"] = CommonHelper::sanitizeMacAddress($d["mac"]);

//             $d["last_active_at"] =  $now->format('Y-m-d H:i:s');
//             $register = ScanRegister::updateOrCreate(['ipv4' => $d['ipv4']], $d);
            
//             // if($register->agent_look_at == null || $register->agent_look_at == "") {
//                 $need_to_scan[] = $d['ipv4'];
//             // }
//         }

//         $return['status'] = 'success';
//         $return['ips'] = $need_to_scan;
//         $return['msg'] = trans('content.network_inv_fields.Updation_done_successfully');

//         return response()->json($return);
//     }

//     /* Agent - ask for date and time for its' next execution */
//     public function getNextSchedule(Request $request) {
//         $return = ["status" => "fail"];

//         if(config('services.network_inventory.agent_token') !== trim($request->agentToken)) {
//             return response()->json($return);
//         }

//         $interval_hrs = 12;
//         $increase_mins = 10;
//         $targetTime = false;

//         if($request->BIOSSerialNumber) {
//             $data = $request->only('BIOSSerialNumber');
//             $validator = Validator::make($data, [
//                 'BIOSSerialNumber' => 'nullable|string|max:255'
//             ], []);

//             if($validator->fails()) {
//                 $v = $validator->errors()->toArray();
//                 $e = array_shift($v);
//                 $return["msg"] = $e[0];
//                 return response()->json($return);
//             }

//             $getLastData = AgentData::where('BIOSSerialNumber', 'like', $request->BIOSSerialNumber)->select('created_at')->orderBy('id', 'desc')->limit(1)->get();
//             if(count($getLastData)) {
//                 $minTime = Carbon::now(config('app.timezone'))->subHours($interval_hrs);
//                 $targetTime = new Carbon($getLastData[0]->created_at, config('app.timezone'));
//                 if($targetTime->lessThan($minTime)) {
//                     $targetTime = $minTime;
//                 }
//             }
//         }

//         $getNextSchedule = AgentRunSchedule::getNextSchedule($interval_hrs, $increase_mins, $targetTime);

//         if($getNextSchedule) {
//             $return["status"] = "success";
//             // $return["next_schedule"] = $getNextSchedule->format('Y-m-d H:i:00');
//             $return["next_schedule"] = Carbon::now(config('app.timezone'))->addMinutes(1)->format('Y-m-d H:i:00');
//         }

//         return response()->json($return);
//     }

//     /* Agent send info of snmp inventory */
//     public function receiveDataBySnmp(Request $request) {
//         $return = ['status'=>'False'];
//         try {
//             $now = new Carbon(config('app.timezone'));
//             $all = $request->all();

//             $dir_sep = DIRECTORY_SEPARATOR;
//             $ni_folder_name = "ni" . $dir_sep . $now->format("ymd");
//             $storage_path = storage_path($ni_folder_name);
//             if(! is_dir($storage_path)) {
//                 Storage::makeDirectory($ni_folder_name);
//             }
//             $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//             $file_stored = Storage::put($ni_folder_name . $dir_sep . $file_name, json_encode($all));

//             $agent_data = AgentData::create([
//                 'file_path' => $ni_folder_name . $dir_sep . $file_name,
//                 'ipv4' => $request->IPv4,
//                 'company_id' => $request->company,
//                 'protocol' => 'snmp'
//             ]);

//             $return['status'] = 'True';
//         }
//         catch(\Exception $e) {
//             $return['status'] = 'False';
//         }

//         return response()->json($return);
//     }

//     /* Receive Telnet Data */
//     public function receiveTelnetDataByAgent(Request $request) {
//         $return = ['status'=>'False'];
//         try {
//             if($request->serialnumber == null || $request->serialnumber == "") {
//                 return response()->json($return);
//             }

//             $now = new Carbon(config('app.timezone'));
//             $all = $request->all();

//             $dir_sep = DIRECTORY_SEPARATOR;
//             $ni_folder_name = "ni" . $dir_sep . $now->format("ymd");
//             $storage_path = storage_path($ni_folder_name);
//             if(! is_dir($storage_path)) {
//                 Storage::makeDirectory($ni_folder_name);
//             }
//             $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//             $file_stored = Storage::put($ni_folder_name . $dir_sep . $file_name, json_encode($all));

//             $agent_data = AgentData::create([
//                 'file_path' => $ni_folder_name . $dir_sep . $file_name,
//                 'BIOSSerialNumber' => $request->serialnumber,
//                 'ipv4' => $request->IPv4,
//                 'company_id' => $request->company,
//                 'protocol' => 'telnet'
//             ]);

//             $return['status'] = 'True';
//         }
//         catch(\Exception $e) {
//             $return['status'] = 'False';
//         }

//         return response()->json($return);
//     }

//     /* Agent send info */
//     public function receiveDataByAgent(Request $request) {
        
//         $return = ["status" => "fail"];
//         $now = new Carbon(config('app.timezone'));

//         $dir_sep = DIRECTORY_SEPARATOR;
//         $ni_folder_name = "ni" . $dir_sep . $now->format("ymd");
//         $ni_incomplete_folder_name = "ni_data_incomplete" . $dir_sep . $now->format("ymd");
//         $ni_new_serial_and_duplicate_mac = "ni_data_duplicate" . $dir_sep . $now->format("ymd");
//         $all = $request->all();
//         $post_keys = array_keys($all);
//         // Log::error('----------------receiveDataByAgent----------------');

//         $agent_token = (string) config('services.network_inventory.agent_token');

//         if( $agent_token != trim((string) $request->agentToken) && $agent_token != trim((string) $request->agent_token)) {
//             $storage_path = storage_path($ni_incomplete_folder_name);
//             if(! is_dir($storage_path)) {
//                 Storage::makeDirectory($ni_incomplete_folder_name);
//             }

//             $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//             $file_stored = Storage::put($ni_incomplete_folder_name . $dir_sep . $file_name, json_encode($all));
//             return response()->json($return);
//         }

//         $firstSuperUser = User::getFirstSuperUser();
//         Auth::loginUsingId($firstSuperUser->id);

//         $allData = $request->has('allData') ? new AllData($request->allData) : null;

//         $storage_path = storage_path($ni_folder_name);
//         if(! is_dir($storage_path)) {
//             Storage::makeDirectory($ni_folder_name);
//         }
//         $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//         $file_stored = Storage::put($ni_folder_name . $dir_sep . $file_name, json_encode($all));

//         $agent_data = AgentData::create([
//             'file_path' => $ni_folder_name . $dir_sep . $file_name
//         ]);

//         $dataParams = ["ComputerName","ComputerManufacturer","ComputerModel","ComputerDomain","ComputerWorkgroup","ComputerSystemType","OSCaption","OSManufacturer","OSSerialNumber","OSVersion","OSServicePack","OSArchitecture","OSSystemDrive","OSSystemDirectory","OSKey","ProcessorName","ProcessorManufacturer","ProcessorArchitecture","ProcessorFamily","ProcessorProcessorId","ProcessorNumberOfCores", "ActiveMACAddress", "BIOSSMBIOSBIOSVersion", "BIOSManufacturer", "BIOSReleaseDate", "IPv4", "platform","company","SoftwareLicensingProduct"];

//         $data = $allData ? $allData->getValues($dataParams) :  $request->only("ComputerName","ComputerManufacturer","ComputerModel","ComputerDomain","ComputerWorkgroup","ComputerSystemType","OSCaption","OSManufacturer","OSSerialNumber","OSVersion","OSServicePack","OSArchitecture","OSSystemDrive","OSSystemDirectory","OSKey","ProcessorName","ProcessorManufacturer","ProcessorArchitecture","ProcessorFamily","ProcessorProcessorId","ProcessorNumberOfCores", "ActiveMACAddress", "BIOSSMBIOSBIOSVersion", "BIOSManufacturer", "BIOSReleaseDate", "IPv4", "platform","company","SoftwareLicensingProduct");
//         $sanitizedMacAddress = CommonHelper::sanitizeMacAddress($request->ActiveMACAddress);
//         $isInvalidSerial = CommonHelper::isInvalidSerial($request->BIOSSerialNumber);
//         $data["ActiveMACAddress"] = $sanitizedMacAddress;
//         $data["IPv4"] = $request->IPv4;
//         $data["SoftwareLicensingProduct"] = (isset($data['SoftwareLicensingProduct']) && $data['SoftwareLicensingProduct'] != "") ? json_encode($data['SoftwareLicensingProduct']) : NULL;

//         if($data['ComputerName'] == $request->IPv4) {
//             $data['IPv4'] = "";
//         }

//         /* not required to allow if both serial and mac fields are empty */
//         if(($request->BIOSSerialNumber == null || $request->BIOSSerialNumber == "null" || $request->BIOSSerialNumber == "") && ($request->ActiveMACAddress == null || $request->ActiveMACAddress == "null" || $request->ActiveMACAddress == "")) {
//             return response()->json($return);
//         }

//         $agent_data->BIOSSerialNumber = trim($request->BIOSSerialNumber);
//         $agent_data->ActiveMACAddress = trim($request->ActiveMACAddress);
//         $agent_data->ipv4 = $data["IPv4"];
//         $agent_data->ComputerName = $data['ComputerName'];
//         $agent_data->company_id = $data['company'] && $data['company'] > 0 ? $data['company'] : Company::whereNull('deleted_at')->first()->id;
//         $agent_data->save();

//         $exists = [];
//         if($isInvalidSerial == false && $sanitizedMacAddress == "") {
//             // store error log & store file in ni_error folder
//             $storage_path = storage_path($ni_new_serial_and_duplicate_mac);
//             if(! is_dir($storage_path)) {
//                 Storage::makeDirectory($ni_new_serial_and_duplicate_mac);
//             }

//             $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//             $file_stored = Storage::put($ni_new_serial_and_duplicate_mac . $dir_sep . $file_name, json_encode($all));
//             return response()->json($return);
//         }
//         else if($isInvalidSerial == false && $sanitizedMacAddress != "") {
//             $existsSerialCount = Basic::where("BIOSSerialNumber", "=", $request->BIOSSerialNumber)->whereNull('is_dupe')->count();
//             if($existsSerialCount == 1) {
//                 $exists = Basic::where("BIOSSerialNumber", "=", $request->BIOSSerialNumber)->whereNull('is_dupe')->select("id")->get();
//             } else {
//                 $existsMacCount = Basic::where("ActiveMACAddress", "=", $sanitizedMacAddress)->whereNull('is_dupe')->count();
//                 if($existsMacCount == 1) {
//                     $diskDrivesData = $allData ? $allData->DiskDrive : $request->DiskDrive;
//                     if($diskDrivesData && count($diskDrivesData) == 1) {
//                         foreach($diskDrivesData as $dd) {
//                             if(! isset($dd["SerialNumber"])) {
//                                 $dd["SerialNumber"] = "";
//                             }
//                             if(! isset($dd["Size"])) {
//                                 $dd["Size"] = 0;
//                             }

//                             $dd["SerialNumber"] = str_replace("-", "", $dd["SerialNumber"]);
//                             $existsDrive = DiskDrive::where("SerialNumber", "like", $dd["SerialNumber"])->first();
//                             if(!empty($existsDrive)) {
//                                 // $changeThere = $existsDrive->isChangeThere($dd);
//                                 // if(!$changeThere) {
//                                     $exists = Basic::where("ActiveMACAddress", "=", $sanitizedMacAddress)->whereNull('is_dupe')->select("id")->get();
//                                 // }
//                             } else {
//                                 // store error log & store file in ni_error folder
//                                 $storage_path = storage_path($ni_new_serial_and_duplicate_mac);
//                                 if(! is_dir($storage_path)) {
//                                     Storage::makeDirectory($ni_new_serial_and_duplicate_mac);
//                                 }

//                                 $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//                                 $file_stored = Storage::put($ni_new_serial_and_duplicate_mac . $dir_sep . $file_name, json_encode($all));
//                                 return response()->json($return);
//                             }
//                         }
//                     }
//                 } else if ($existsMacCount > 1 ) {
//                     // store error log & store file in ni_error folder
//                     $storage_path = storage_path($ni_new_serial_and_duplicate_mac);
//                     if(! is_dir($storage_path)) {
//                         Storage::makeDirectory($ni_new_serial_and_duplicate_mac);
//                     }

//                     $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//                     $file_stored = Storage::put($ni_new_serial_and_duplicate_mac . $dir_sep . $file_name, json_encode($all));
//                     return response()->json($return);
//                 }
//             }
//         }
//         elseif( $isInvalidSerial == true && $sanitizedMacAddress != "" &&  $sanitizedMacAddress != null & $sanitizedMacAddress != "null" ) {
//             $existsMacCount = Basic::where("ActiveMACAddress", "=", $sanitizedMacAddress)->whereNull('is_dupe')->count();
//             if($existsMacCount == 1) {
//                 $exists = Basic::where("ActiveMACAddress", "=", $sanitizedMacAddress)->whereNull('is_dupe')->select("id")->get();
//             } else if($existsMacCount > 1) {
//                 // store error log & store file in ni_error folder
//                 $storage_path = storage_path($ni_new_serial_and_duplicate_mac);
//                 if(! is_dir($storage_path)) {
//                     Storage::makeDirectory($ni_new_serial_and_duplicate_mac);
//                 }

//                 $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//                 $file_stored = Storage::put($ni_new_serial_and_duplicate_mac . $dir_sep . $file_name, json_encode($all));
//                 return response()->json($return);
//             }
//         }
//         elseif($isInvalidSerial == true && ($sanitizedMacAddress == "" || $sanitizedMacAddress == null || $sanitizedMacAddress == "null")) {
//             $storage_path = storage_path($ni_new_serial_and_duplicate_mac);
//             if(! is_dir($storage_path)) {
//                 Storage::makeDirectory($ni_new_serial_and_duplicate_mac);
//             }

//             $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//             $file_stored = Storage::put($ni_new_serial_and_duplicate_mac . $dir_sep . $file_name, json_encode($all));
//             return response()->json($return);
//         }
//         elseif( $isInvalidSerial == false) {
//             $exists = Basic::where("BIOSSerialNumber", "=", $request->BIOSSerialNumber)->whereNull('is_dupe')->select("id")->limit(1)->get();
//         }

//         $basic_id = null;

//         if(count($exists)) {
//             $basic_id = $exists[0]->id;
//             $basic = Basic::find($basic_id);
//             $asset = Assets::where('serial', $request->BIOSSerialNumber)->first();
//             // Basic::where("BIOSSerialNumber", "like", $request->BIOSSerialNumber)->update($data);
//             if($isInvalidSerial) {
//                 unset($data["BIOSSerialNumber"]);
//             }

//             if(!empty($asset)) {
//                 $asset->name = $data['ComputerName'];
//                 $asset->ip = $data["IPv4"];
//                 $asset->mac = $data["ActiveMACAddress"];
//                 $asset->save();
//                 $data['device_id'] = $asset->id;
//             }

//             $basic->update($data);
//         }

//         /*if(! count($exists)) {
//             try {
//                 $exists = NetworkAdapter::where("PhysicalAddress", "like", $sanitizedMacAddress)->select("basic_id")->limit(1)->get();
//                 if(count($exists)) {
//                     $basic_id = $exists[0]->basic_id;
//                     $basic = Basic::find($basic_id);
//                     $asset = Assets::where('serial', $request->BIOSSerialNumber)->first();
//                     if($isInvalidSerial) {
//                         unset($data["BIOSSerialNumber"]);
//                     }

//                     if(!empty($asset)) {
//                         $asset->name = $data['ComputerName'];
//                         $asset->save();
//                         $data['device_id'] = $asset->id;
//                     }

//                     $basic->update($data);
//                 }
//             }
//             catch(\Exception $e) {
//                 Log::error("Error at get basic by mac - procedure 2 : " . $sanitizedMacAddress);
//                 Log::error($e->getMessage());
//             }
//         }*/

//         if(! $basic_id) {
//             $basic = new Basic;
//             $basic->fill($data);

//             $basic->BIOSSerialNumber = $isInvalidSerial ? Basic::generateDummySerial() : $request->BIOSSerialNumber;
//             // $basic->BIOSSerialNumber = $request->BIOSSerialNumber;
//             $basic->save();
//             $basic_id = $basic->id;
//         }

//         $basic->company = $data['company'] && $data['company'] > 0 ? $data['company'] : Company::whereNull('deleted_at')->first()->id;

//         if(isset($data['IPv4']) && $data['IPv4'] && $basic->validate_ip($data['IPv4'])) {
//             $scanRegisterData = [];
//             $scanRegisterData['agent_look_at'] = $now->format('Y-m-d H:i:s');
//             $scanRegisterData['company_id'] = isset($data["company"]) && $data["company"] ? $data["company"] : Company::whereNull('deleted_at')->first()->id;
//             $scanRegisterData['hostname'] = $data['ComputerName'];
//             $scanRegisterData['os'] = $data['OSCaption'];
//             $scanRegisterData['mac'] = $sanitizedMacAddress;
//             ScanRegister::where('ipv4', 'like', $data['IPv4'])->update($scanRegisterData);
//         }

//         /* collect */
//         $monitors = $allData ? $allData->monitors : $request->get('monitors');
//         // Log::error($post_keys);

//         $calc_tot_ram_size = 0;
//         $physicalMemoryChangeDetect = new stdClass();
//         $physicalMemoryChangeDetect->physical_memory_id = [];
//         $physicalMemories = $allData ? $allData->PhysicalMemory : $request->PhysicalMemory;
//         if($physicalMemories && count($physicalMemories)) {
//             try{              
//                 foreach($physicalMemories as $pm) {
//                     $tmp_capacity = isset($pm["Capacity"]) && $pm["Capacity"] ? $pm["Capacity"] : 0;
//                     if(! isset($pm["SerialNumber"])) {
//                         $pm["SerialNumber"] = "";
//                     }
//                     $pm["SerialNumber"] = str_replace("-", "", $pm["SerialNumber"]);
//                     $pm["Capacity"] = CommonHelper::byteToGb($tmp_capacity);

//                     $exists = PhysicalMemory::where("SerialNumber", "like", $pm["SerialNumber"])->where("DeviceLocator", "like", $pm["DeviceLocator"])->where("basic_id", "=", $basic_id)->first();
//                     if($exists) {
//                         $physicalMemoryChangeDetect->physical_memory_id[] = $exists->id;

//                         $changeThere = $exists->isChangeThere($pm);
//                         if($changeThere) {
//                             /* change cache - cache old data */
//                             $changeCache = $exists->dataForCache();
//                         }

//                         PhysicalMemory::where("SerialNumber", "like", $pm["SerialNumber"])->where("DeviceLocator", "like", $pm["DeviceLocator"])->where("basic_id", "=", $basic_id)->update($pm);

//                         if($changeThere) {
//                             /* change log - add change detected disk */
//                             $changeLog = (array) $pm;
//                             $changeLog["basic_id"] = $basic_id;
//                             $changeLog["cl_item"] = 2;
//                             $changeLog["cl_type"] = 2;
//                             $changeLog = ChangeLog::create($changeLog);

//                             $changeCache["cl_item"] = 2;
//                             $changeCache["cl_id"] = $changeLog->id;
//                             ChangeCache::create($changeCache);
//                         }
//                     }
//                     else {
//                         $pmObj = new PhysicalMemory;
//                         $pmObj->basic_id = $basic_id;
//                         $pmObj->fill((array) $pm);
//                         $pmObj->save();

//                         $physicalMemoryChangeDetect->physical_memory_id[] = $pmObj->id;

//                         /* change log - add newly detected disk */
//                         $changeLog = (array) $pm;
//                         $changeLog["basic_id"] = $basic_id;
//                         $changeLog["cl_item"] = 2;
//                         $changeLog["cl_type"] = 1;
//                         ChangeLog::create($changeLog);
//                     }

//                     $calc_tot_ram_size += $pm["Capacity"];
//                 }

//                 /* check for missing memory drives */
//                 $get_missing_memories = PhysicalMemory::where("basic_id", "=", $basic_id)->whereNotIn("id", $physicalMemoryChangeDetect->physical_memory_id)->get();
//                 if(count($get_missing_memories)) {
//                     foreach($get_missing_memories as $missing_memory) {
//                         /* change log - missing detected disk */
//                         $changeLog = $missing_memory->dataForCache();
//                         $changeLog["basic_id"] = $basic_id;
//                         $changeLog["cl_item"] = 2;
//                         $changeLog["cl_type"] = 3;
//                         $changeLog = ChangeLog::create($changeLog);
//                     }
//                     PhysicalMemory::where("basic_id", "=", $basic_id)->whereNotIn("id", $physicalMemoryChangeDetect->physical_memory_id)->delete();
//                 }

//                 $basic->touch();
//             }
//             catch(\Exception $e) {
//                 Log::error($e->getMessage());
//             }
//         }

//         $calc_tot_hdd_size = 0;
//         $diskDriveChangeDetect = new stdClass();
//         $diskDriveChangeDetect->serial = [];

//         $diskDrives = $allData ? $allData->DiskDrive : $request->DiskDrive;
//         if($diskDrives && count($diskDrives)) {
//             try{
//                 foreach($diskDrives as $dd) {
//                     foreach($dd as $k=>$v) {
//                         $dd[$k] = trim((string) $v);
//                     }

//                     if(! isset($dd["SerialNumber"])) {
//                         $dd["SerialNumber"] = "";
//                     }
//                     if(! isset($dd["Size"])) {
//                         $dd["Size"] = 0;
//                     }
                    
//                     $dd["SerialNumber"] = str_replace("-", "", $dd["SerialNumber"]);
//                     $dd["Size"] = CommonHelper::byteToGb($dd["Size"]);
//                     $diskDriveChangeDetect->serial[] = $dd["SerialNumber"];

//                     $exists = DiskDrive::where("SerialNumber", "like", $dd["SerialNumber"])->where("basic_id", "=", $basic_id)->first();
//                     if($exists) {
//                         $changeThere = $exists->isChangeThere($dd);
//                         if($changeThere) {
//                             /* change cache - cache old data */
//                             $changeCache = $exists->dataForCache();
//                         }

//                         DiskDrive::where("SerialNumber", "like", $dd["SerialNumber"])->where("basic_id", "=", $basic_id)->update($dd);

//                         if($changeThere) {
//                             /* change log - add change detected disk */
//                             $changeLog = (array) $dd;
//                             $changeLog["basic_id"] = $basic_id;
//                             $changeLog["cl_item"] = 1;
//                             $changeLog["cl_type"] = 2;
//                             $changeLog = ChangeLog::create($changeLog);

//                             $changeCache["cl_item"] = 1;
//                             $changeCache["cl_id"] = $changeLog->id;
//                             ChangeCache::create($changeCache);
//                         }
//                     }
//                     else {
//                         $ddObj = new DiskDrive;
//                         $ddObj->basic_id = $basic_id;
//                         $ddObj->fill((array) $dd);
//                         $ddObj->save();

//                         /* change log - add newly detected disk */
//                         $changeLog = (array) $dd;
//                         $changeLog["basic_id"] = $basic_id;
//                         $changeLog["cl_item"] = 1;
//                         $changeLog["cl_type"] = 1;
//                         ChangeLog::create($changeLog);
//                     }
//                     $calc_tot_hdd_size += $dd["Size"];
//                 }

//                 /* check for missing disk drives */
//                 $get_missing_disks = DiskDrive::where("basic_id", "=", $basic_id)->whereNotIn("SerialNumber", $diskDriveChangeDetect->serial)->get();
//                 if(count($get_missing_disks)) {
//                     foreach($get_missing_disks as $missing_disk) {
//                         /* change log - missing detected disk */
//                         $changeLog = $missing_disk->dataForCache();
//                         $changeLog["basic_id"] = $basic_id;
//                         $changeLog["cl_item"] = 1;
//                         $changeLog["cl_type"] = 3;
//                         $changeLog = ChangeLog::create($changeLog);
//                     }
//                     DiskDrive::where("basic_id", "=", $basic_id)->whereNotIn("SerialNumber", $diskDriveChangeDetect->serial)->delete();
//                 }

//                 $basic->touch();
//             }
//             catch(\Exception $e) {
//                 Log::error($e->getMessage());
//             }
//         }

//         $volumes = $allData ? $allData->Volume : $request->Volume;
//         if($volumes && count($volumes)) {
//             try{              
//                 foreach($volumes as $volume) {
//                     foreach($volume as $k=>$v) {
//                         $volume[$k] = trim((string) $v);
//                     }

//                     try {
// 						$volume["AvailableFreeSpace"] = isset($volume["AvailableFreeSpace"]) ? CommonHelper::byteToGb($volume["AvailableFreeSpace"]) : 0;
// 						$volume["TotalSize"] = isset($volume["TotalSize"]) ? CommonHelper::byteToGb($volume["TotalSize"]) : 0;
// 					}
// 					catch(\Exception $e) {
// 						$volume["AvailableFreeSpace"] = 0;
// 						$volume["TotalSize"] = 0;
//                     }
                    
//                     $exists = Volume::where("Name", "like", $volume["Name"])->where("basic_id", "=", $basic_id)->count();
//                     if($exists) {
//                         Volume::where("Name", "like", $volume["Name"])->where("basic_id", "=", $basic_id)->update($volume);
//                     }
//                     else {
//                         $volumeObj = new Volume;
//                         $volumeObj->basic_id = $basic_id;
//                         $volumeObj->fill((array) $volume);
//                         $volumeObj->save();
//                     }
//                 }
//                 $basic->touch();
//             }
//             catch(\Exception $e) {
//                 Log::error($e->getMessage());
//             }
//         }

//         if($monitors && count($monitors)) {
//             try {
//                 foreach($monitors as $monitor) {
//                     foreach($monitor as $k=>$v) {
//                         $monitor[$k] = trim((string) $v);
//                     }
                    
//                     $existing_serial = 'SerialNumber';

//                     if(!$monitor[$existing_serial] || $monitor[$existing_serial] == '0') {
//                         continue;
//                     }

//                     $monitor_serial = $monitor[$existing_serial];
//                     $monitor_data = [];
//                     $monitor_data['basic_id'] = $basic_id;
//                     $monitor_data['monitor_name'] = $monitor['MonitorName'];

//                     unset($monitor[$existing_serial]);
//                     unset($monitor["MonitorName"]);
//                     $monitor_data['others'] = json_encode($monitor);

//                     $monitor_obj = Monitor::updateOrCreate(['serial' => $monitor_serial], $monitor_data);

//                     if( $monitor_obj && !$monitor_obj->component_id ) {
//                         $component = Component::where('serial', 'like', $monitor_serial)->first();
//                         if($component) {
//                             $monitor_obj->component_id = $component->id;
//                             $monitor_obj->save();

//                             $component->monitor_id = $monitor_obj->id;
//                             $component->save();
//                         }
//                         else {
//                             $component = new Component();
//                             $component->category_id = Category::getMonitor()->id;
//                             $component->status = 1;
//                             $component->company_id = 1;
//                             $component->location_id = null;
//                             $component->serial = $monitor_obj->serial;
//                             $component->name = $monitor_obj->monitor_name;
//                             $component->monitor_id = $monitor_obj->id;
//                             $component->save();
//                             $component->generateUniqueTag();

//                             $monitor_obj->component_id = $component->id;
//                             $monitor_obj->save();
//                         }
//                     }
//                 }
//             }
//             catch(\Exception $e) {
//                 Log::error("AgentData Monitor: " . $e->getMessage());
//             }
//         }

//         $softwareChangeDetect = new stdClass();
//         $softwareChangeDetect->captions = [];
//         $softwares = [];
//         if($request->pkg) {
//             $parse_pkg = json_decode($request->pkg);
//             foreach($parse_pkg as $p) {
//                 $softwares[] = (array) $p;
//             }
//         }
//         else {
//             $softwares = $allData ? $allData->InstalledSoftware : $request->InstalledSoftware;
//         }

//         if($softwares && count($softwares)) {
//             try {              
//                 foreach($softwares as $s) {
//                     try {
//                         foreach($s as $k=>$v) {
//                             $s[$k] = trim((string) $v);
//                         }

//                         if( !isset($s["Caption"]) || $s["Caption"] == "" || $s["Caption"] == null || $s["Caption"] == "null") {
//                             continue;
//                         }
//                         $softwareChangeDetect->captions[] = $s["Caption"];

//                         try {
//                             $exists = Product::where("Caption", "like", $s["Caption"])->where("basic_id", "=", $basic_id)->first();
//                         }
//                         catch(\Exception $e) {
//                             continue;
//                         }
//                         if(! $exists) {
//                             $proData = (array) $s;
//                             $proData["InstalledDate"] = isset($s['installDate']) && $s['installDate'] ? CommonHelper::getDateAs($s['installDate'], "Y-m-d H:i:s", "Ymd") : null;
//                             unset($proData["installDate"]);

//                             $productObj = new Product;
//                             $productObj->basic_id = $basic_id;
//                             $productObj->InstalledDate = isset($s['installDate']) && $s['installDate'] ? CommonHelper::getDateAs($s['installDate'], "Y-m-d H:i:s", "Ymd") : null;
//                             unset($s['installDate']);
//                             $productObj->fill((array) $s);
//                             $productObj->save();

//                             /* change log - add newly detected license */
//                             $changeLog = $proData;
//                             $changeLog["basic_id"] = $basic_id;
//                             $changeLog["cl_item"] = 3;
//                             $changeLog["cl_type"] = 1;
//                             ChangeLog::create($changeLog);
//                         }
//                         else {
//                             $proData = (array) $s;
//                             $proData["InstalledDate"] = isset($s['installDate']) && $s['installDate'] ? CommonHelper::getDateAs($s['installDate'], "Y-m-d H:i:s", "Ymd") : null;
//                             unset($proData['installDate']);

//                             $changeThere = $exists->isChangeThere($proData);
//                             if($changeThere) {
//                                 /* change cache - cache old data */
//                                 $changeCache = $exists->dataForCache();
//                             }

//                             Product::where("Caption", "like", $s["Caption"])->where("basic_id", "=", $basic_id)->update($proData);

//                             if($changeThere) {
//                                 /* change log - add change detected license */
//                                 $changeLog = $proData;
//                                 $changeLog["basic_id"] = $basic_id;
//                                 $changeLog["cl_item"] = 3;
//                                 $changeLog["cl_type"] = 2;
//                                 $changeLog = ChangeLog::create($changeLog);

//                                 $changeCache["cl_item"] = 3;
//                                 $changeCache["cl_id"] = $changeLog->id;
//                                 ChangeCache::create($changeCache);
//                             }
//                         }
//                     }
//                     catch(\Exception $e) {
//                         Log::error($e->getMessage());
//                     }
//                 }

//                 /* check for missing softwares */
//                 $get_missing_softwares = Product::where("basic_id", "=", $basic_id)->whereNotIn("Caption", $softwareChangeDetect->captions)->get();
//                 if(count($get_missing_softwares)) {
//                     foreach($get_missing_softwares as $missing_sw) {
//                         try {
//                             /* change log - missing software */
//                             $changeLog = $missing_sw->dataForCache();
//                             $changeLog["basic_id"] = $basic_id;
//                             $changeLog["cl_item"] = 3;
//                             $changeLog["cl_type"] = 3;
//                             $changeLog = ChangeLog::create($changeLog);
//                         }
//                         catch(\Exception $e) {
//                             Log::error($e->getMessage());
//                         }
//                     }
//                     Product::where("basic_id", "=", $basic_id)->whereNotIn("Caption", $softwareChangeDetect->captions)->delete();
//                 }

//                 $basic->touch();
//             }
//             catch(\Exception $e) {
//                 Log::error($e->getMessage());
//             }
//         }

//         $videoControllers = $allData ? $allData->VideoController : $request->VideoController;
//         if($videoControllers && count($videoControllers)) {
//             try{              
//                 foreach($videoControllers as $s) {
//                     foreach($s as $k=>$v) {
//                         $s[$k] = trim((string) $v);
//                     }
//                     $exists = VideoController::where("Caption", "like", $s["Caption"])->where("DriverVersion", "like", $s["DriverVersion"])->where("basic_id", "=", $basic_id)->count();
//                     if($exists) {
//                         VideoController::where("Caption", "like", $s["Caption"])->where("DriverVersion", "like", $s["DriverVersion"])->where("basic_id", "=", $basic_id)->update($s);
//                     }
//                     else {
//                         $videoObj = new VideoController;
//                         $videoObj->basic_id = $basic_id;
//                         $videoObj->fill((array) $s);
//                         $videoObj->save();
//                     }
//                 }
//                 $basic->touch();
//             }
//             catch(\Exception $e) {
//                 Log::error($e->getMessage());
//             }
//         }

//         $soundDevices = $allData ? $allData->SoundDevice : $request->SoundDevice;
//         if($soundDevices && count($soundDevices)) {
//             try{              
//                 foreach($soundDevices as $s) {
//                     foreach($s as $k=>$v) {
//                         $s[$k] = trim((string) $v);
//                     }
//                     $exists = SoundDevice::where("Name", "like", $s["Name"])->where("basic_id", "=", $basic_id)->count();
//                     if($exists) {
//                         SoundDevice::where("Name", "like", $s["Name"])->where("basic_id", "=", $basic_id)->update($s);
//                     }
//                     else {
//                         $soundObj = new SoundDevice;
//                         $soundObj->basic_id = $basic_id;
//                         $soundObj->fill((array) $s);
//                         $soundObj->save();
//                     }
//                 }
//                 $basic->touch();
//             }
//             catch(\Exception $e) {
//                 Log::error($e->getMessage());
//             }
//         }

//         $userAccounts = $allData ? $allData->UserAccount : $request->UserAccount;
//         if($userAccounts && count($userAccounts)) {
//             try {
//                 foreach($userAccounts as $ua) {
//                     try {
//                         if(! isset($ua["SID"])) {
//                             continue;
//                         }

//                         foreach($ua as $k=>$v) {
//                             $ua[$k] = trim((string) $v);
//                         }

//                         $exists = UserAccount::where("basic_id", "=", $basic_id)->where("SID", "like", $ua["SID"])->first();
//                         if(! $exists) {
//                             $uaData = (array) $ua;
//                             $ua["LastLogon"] = isset($uaData['LastLogon']) && $uaData['LastLogon'] ? CommonHelper::getDateAs(explode(".", $uaData['LastLogon'])[0], "Y-m-d H:i:s", "YmdHis") : null;

//                             $uaObj = new UserAccount();
//                             $uaObj->basic_id = $basic_id;
//                             $uaObj->fill((array) $ua);
//                             $uaObj->save();
//                         }
//                         else {
//                             $uaData = (array) $ua;
//                             $ua["LastLogon"] = isset($uaData['LastLogon']) && $uaData['LastLogon'] ? CommonHelper::getDateAs(explode(".", $uaData['LastLogon'])[0], "Y-m-d H:i:s", "YmdHis") : null;

//                             UserAccount::where("basic_id", "=", $basic_id)->where("SID", "like", $ua["SID"])->update($ua);
//                         }
//                     }
//                     catch(\Exception $e) {
//                         Log::error($e->getMessage());
//                     }
//                 }
//                 $basic->touch();
//             }
//             catch(\Exception $e) {
//                 Log::error($e->getMessage());
//             }
//         }

//         /* network adapters of a system */
//         $networkAdapters = $allData ? $allData->nw_adapters : $request->nw_adapters;
//         if($networkAdapters && count($networkAdapters)) {
//             try {
//                 foreach($networkAdapters as $nwa) {
//                     $nwa["PhysicalAddress"] = CommonHelper::sanitizeMacAddress($nwa["PhysicalAddress"]);
//                     if(! $nwa["PhysicalAddress"]) {
//                         continue;
//                     }

//                     foreach($nwa as $k=>$v) {
//                         $nwa[$k] = trim((string) $v);
//                     }

//                     $exists = NetworkAdapter::where("basic_id", "=", $basic_id)->where("PhysicalAddress", "like", $nwa["PhysicalAddress"])->first();
//                     if(! $exists) {
//                         $nwaObj = new NetworkAdapter();
//                         $nwaObj->basic_id = $basic_id;
//                         $nwaObj->fill((array) $nwa);
//                         $nwaObj->save();
//                     }
//                     else {
//                         NetworkAdapter::where("basic_id", "=", $basic_id)->where("PhysicalAddress", "like", $nwa["PhysicalAddress"])->update($nwa);
//                     }
//                 }
//                 $basic->touch();
//             }
//             catch(\Exception $e) {
//                 Log::error($e->getMessage());
//             }
//         }

//         /* update hdd and ram size */
//         $basic->HddSize = $calc_tot_hdd_size ? round($calc_tot_hdd_size, 2) : $calc_tot_hdd_size;
//         $basic->RamSize = $calc_tot_ram_size ? round($calc_tot_ram_size,2) : $calc_tot_ram_size;
//         $basic->save();

//         /* check for device already exists */
//         try {
//             // if($basic->IPv4 && $basic->validate_ip($basic->IPv4)) {
//             if(isset($data['IPv4']) && $data['IPv4'] && $basic->validate_ip($data['IPv4'])) {
//                 $get_existing_device = Device::where('serial', 'like', $basic->BIOSSerialNumber)->get();
//                 if($get_existing_device && count($get_existing_device)) {
//                     $existing_device = $get_existing_device[0];
//                     $existing_device->ip = $data['IPv4'];
//                     $existing_device->mac = $basic->ActiveMACAddress;

//                     $mapped_locs = MappedLocation::get();
//                     if(count($mapped_locs)) {
//                         foreach($mapped_locs as $ml) {
//                             if($ml->check_ip_in_range($data['IPv4'])) {
//                                 $td = [
//                                     "device_id" => $existing_device->id,
//                                     "map_loc_id" => $ml->id,
//                                     "ip" => $data['IPv4'],
//                                     "location_id" => $ml->location_id,
//                                     "place_id" => $ml->place_id
//                                 ];
//                                 $existing_device->ni_detected_location = $ml->location_id;
//                                 TrackedDevice::create($td);
//                                 break;
//                             }
//                         }
//                     }

//                     $existing_device->save();
//                 }
//             }
//         }
//         catch(\Exception $e) {
//             Log::error($e->getMessage());
//         }

//         $return["msg"] = trans('content.network_inv_fields.we_received_configuration');
//         $return["status"] = "success";
//         return response()->json($return);
//     }

//     public function getothers() {
//         $vd = new stdClass();
//         $vd->manufacturers = Basic::manufacturerOptions();
//         $vd->models = Basic::modelOptions();
//         $vd->vendors = ChangeLog::manufacturerOptions();
//         return view("network_inventory.otherlist")->with("vd", $vd);
//     }

//     /* To Receive the Error Log from Agent */
//     public function receiveErrorLog(Request $request) {
//         $return = ['status' => 'False'];
//         $error_log = null;
//         try {
//             $now = new Carbon(config('app.timezone'));
//             $data = $request->only('agent_version', 'incident_at', 'error_msg', 'error_code', 'ip', 'mac', 'name', 'errors');
//             $mac = strpos($request->mac, ":") === true ? str_replace(":", "", $request->mac) : $request->mac;
//             try {
//                 $date = CommonHelper::getDateAs($data['incident_at'], "Y-m-d H:i:s", "YmdHis");
//                 $data["incident_at"] = $date ? $date : $now->format("Y-m-d H:i:s");
//                 $data['mac'] = $mac;
//                 $error_log = AgentErrorLog::create($data);
//             }
//             catch(\Exception $e) {
//                 $dir_sep = DIRECTORY_SEPARATOR;
//                 $ni_folder_name = "ni_errors" . $dir_sep . $now->format("ymd");
//                 $storage_path = storage_path($ni_folder_name);
//                 if(! is_dir($storage_path)) {
//                     Storage::makeDirectory($ni_folder_name);
//                 }
//                 $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//                 $file_stored = Storage::put($ni_folder_name . $dir_sep . $file_name, $data['error_msg']);
//                 $error_log = AgentErrorLog::create([
//                     'incident_at' => $now->format('Y-m-d H:i:s'),
//                     'file_path' => $ni_folder_name . $dir_sep . $file_name,
//                     'error_code' => $request->error_code,
//                     'ip' => $request->ip,
//                     'mac' => $mac,
//                     'name' => $request->name,
//                     'errors' => $request->errors
//                 ]);
//             }

//             if($error_log) {
//                 $return['status'] = 'True';
//             }
//         }
//         catch(\Exception $e) {
//             $return['status'] = 'False';
//         }

//         return response()->json($return);
//     }

//     /* To Download OS Detail */
//     public function downloadosdetail(Request $request) {

//         $select_fields ="select b.OSCaption as name, count(b.id) as tot";

//         $count_fields = "select count(b.id) as tot";

//         $query = "FROM itm_network_inventory_basic as b where b.is_dupe is null and b.deleted_at is null and b.OSCaption is not null  GROUP BY b.OSCaption order by tot desc";

//             $get_count = DB::select($count_fields . " " . $query);
    
//             $return['recordsTotal'] = $get_count[0]->tot;
//             $return['recordsFiltered'] = $return['recordsTotal'];
        
//             $where = "";
//             if($where) {
//                 $get_count = DB::select($count_fields . " " . $query . " " . $where);
//                 $return['recordsFiltered'] = $get_count[0]->tot;
//             }
    
//             $data_query = $select_fields . " " . $query . " " . $where;
    
//             $records = DB::select($data_query);
      
//             $data = [];
//             foreach($records as $r) {

//                 $data[] = [
//                     $r->name,
//                     $r->tot
//                 ];
//             }

//         return Excel::download(new OperatingSystemDetails($data), 'OperatingSystemDetails.xlsx');
//     }

//     /* To Download Microsoft Office Detail */
//     public function downloadMicrosoftOfficedetail(Request $request) {

//         $select_fields = 'select prod.Caption as name, count(prod.basic_id) as tot';
        
//         $count_fields = "select count(prod.basic_id) as tot";

//         $query ='FROM itm_network_inventory_products as prod left join itm_network_inventory_basic as basic on basic.id = prod.basic_id and basic.is_dupe is null and basic.deleted_at is null where prod.Caption in ("Microsoft Office Enterprise 2007", "Microsoft Office Standard 2013", "Microsoft Office Standard 2010", "Microsoft Office Professional Plus 2010", "Microsoft Office Professional Plus 2013", "Microsoft Office", "Microsoft Office Professional Plus 2016", "Microsoft Office 365 - en-us", "Microsoft Office Professional Plus 2007", "Microsoft Office Home and Business 2013 - en-us", "Microsoft Office Home and Business 2010", "Microsoft Office Single Image 2010", "Microsoft Office Professional Plus 2016 - en-us", "Microsoft Office Professional Plus 2019 - en-us", "Microsoft Office Standard 2007", "Microsoft Office Home and Student 2016 - en-us", "Microsoft Office 2010", "Microsoft Office Professional 2016 - en-us", "Microsoft Office Standard 2016") group by prod.Caption order by tot desc';

//             $get_count = DB::select($count_fields . " " . $query);
    
//             $return['recordsTotal'] = $get_count[0]->tot;
//             $return['recordsFiltered'] = $return['recordsTotal'];
        
//             $where = "";
//             if($where) {
//                 $get_count = DB::select($count_fields . " " . $query . " " . $where);
//                 $return['recordsFiltered'] = $get_count[0]->tot;
//             }
    
//             $data_query = $select_fields . " " . $query . " " . $where;
    
//             $records = DB::select($data_query);
      
//             $data = [];
//             foreach($records as $r) {

//                 $data[] = [
//                     $r->name,
//                     $r->tot
//                 ];
//             }

//         return Excel::download(new MicrosoftOfficeDetails($data), 'MicrosoftOfficeDetails.xlsx');
//     }

//     public function getMacErrorLogFile(Request $request) {
//         $return = ["status" => "fail"];
//         $now = new Carbon(config('app.timezone'));

//         $dir_sep = DIRECTORY_SEPARATOR;
//         $ni_mac_errors_folder_name = "ni_mac_errors" . $dir_sep . $now->format("ymd");
//         $all = $request->all();

//         $agent_token = (string) config('services.network_inventory.agent_token');

//         if( $agent_token != trim((string) $request->agentToken) && $agent_token != trim((string) $request->agent_token)) {
//             return response()->json($return);
//         }

//         $storage_path = storage_path($ni_mac_errors_folder_name);
//         if(! is_dir($storage_path)) {
//             Storage::makeDirectory($ni_mac_errors_folder_name);
//         }
//         $file_name = strtolower('f' . $now->format('His_') . Str::random(5) . '.txt');
//         $file_stored = Storage::put($ni_mac_errors_folder_name . $dir_sep, $request->file('error_log'));

//         $return = ["status" => "success", "msg" => "Error Log file uploaded successfully"];
//         return response()->json($return);
//     }
}

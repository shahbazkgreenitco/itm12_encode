<?php

namespace App\Http\Controllers;

use App\Exports\Device\RfidLogExport;
use App\Exports\PatchManagement\PatchDeviceExportList;
use App\Exports\AssetSummaryReport;
use App\Exports\Device\DeviceExport;
use App\Exports\ExportDevicesForImport;
use App\Exports\MicrosoftOfficeDetailsMS;
use App\Exports\OperatingSystemDetailsMS;
use App\Exports\PendingAssetExport;
use App\Helpers\Excel\TextValueHandler;
use App\Helpers\Common as CommonHelper;
use App\Http\Controllers\Controller;
use App\Imports\Device\DevicesImport;
use App\Jobs\ComplianceApplicationFetchStatus;
use App\Mail\Asset\TransferUpdateNotification;
use App\Mail\AssetOwnerMail;
use App\Mail\DeleteDevice;
use App\Mail\DeviceAddNotification;
use App\Mail\DeviceCheckinNotification;
use App\Mail\DeviceCheckoutAcceptRemainder;
use App\Mail\DeviceRequestNotification;
use App\Mail\RequestableNotifyUsers;
use App\Mail\ResaleDevice;
use App\Mail\ThreshouldNotification;
use App\Mail\UserAssetCheckoutPendingMail;
use App\Models\Device\DeviceSetting;
use App\Models\Device\DeviceSez;
use App\Models\Device\PatchManagementGroup;
use App\Models\Device\PatchManagementGroupDevice;
use App\Models\Device\SoftwareDeploymentPolicy;
use App\Models\LiveMonitor\LiveMonitorDevice;
use App\Models\NetworkInventory\Basic;
use App\Models\NetworkInventory\Product;
use App\Models\NetworkInventory\RdpConnectHistory;
use App\Models\NetworkInventory\ScanRegister;
use App\Models\NetworkInventory\TrackedDevice;
use App\Models\PatchManagement\PatchManagerRequestDevice;
use App\Models\PatchManagement\PatchManagerRequestDeviceLog;
use App\Models\PatchManagement\PatchMaster;
use App\Models\PatchManagement\SystemUpdatePatchMaster;
use App\Models\ProjectManagement\Project;
use App\Models\Rdp\Meshcentral;
use App\Models\Rdp\Power;
use App\Models\RFID\BlockedRfidTag;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceCron;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceStatus;
use App\Models\Accessory;
use App\Models\Actionlog;
use App\Models\AssetAllocationType;
use App\Models\AssetDispose;
use App\Models\AssetExpense;
use App\Models\AssetSummary;
use App\Models\AssetType;
use App\Models\Base;
use App\Models\BillingUnit;
use App\Models\BlockedIP;
use App\Models\BulkActions;
use App\Models\Category;
use App\Models\CategoryWiseAssetSummary;
use App\Models\Company;
use App\Models\Component;
use App\Models\Consumable;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CustomField;
use App\Models\CustomFieldset;
use App\Models\Department;
use App\Models\Device;
use App\Models\DeviceAudit;
use App\Models\DeviceBillingCheckinCheckout;
use App\Models\DeviceItemDispose;
use App\Models\DeviceLog;
use App\Models\DeviceMaintenance;
use App\Models\DeviceRfid;
use App\Models\InteractCache;
use App\Models\InteractRecord;
use App\Models\Label;
use App\Models\Lease;
use App\Models\License;
use App\Models\Location;
use App\Models\Manufacture;
use App\Models\Model;
use App\Models\NetworkDevice;
use App\Models\NotificationConfig;
use App\Models\Place;
use App\Models\Purchase;
use App\Models\RequestableNotifyUser;
use App\Models\RfidScanLog;
use App\Models\Setting;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\Threshold;
use App\Models\ThresholdSettings;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Config;
use DateTime;
use DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Log;
use Mail;
use PDF;
use stdClass;
use Storage;
use Validator;

class DeviceController extends Controller
{
    public function getIndex(Request $request, $status = 0)
    {
        /* -- */
        // $lifetime_end = Carbon::createFromFormat('Y-m-d H:i:s', '2019-08-23 12:00:00', config('app.timezone'))->addDays(90);
        // $present_time = Carbon::now(config('app.timezone'));
        // if( $lifetime_end->lessThan($present_time) ) {
        //     return response()->json(["msg" => "Sorry for Inconvenience. Please contact admin"]);
        // }
        /* -- */
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('DeviceRead') || !config('services.assets.enabled')) {
            return redirect('dashboard')->with('msg', $return);
        }
        $data = isset($request->data) ? json_decode(base64_decode($request->data), true) : null;
        $patch_device_ids = isset($data['patch_device_ids']) ? $data['patch_device_ids'] : null;
        $patch_filter = isset($data['patch_filter']) && $data['patch_filter'] == 1 ? $data['patch_filter'] : null;
        $purchaseFilter = null;
        if (isset($request->q)) {
            $purchaseFilter = $request->q;
        }
        $compObj = Company::select('id', 'name as text');
        $location = $model_id = 'null';
        if ($request->location != null && $request->location != 'null') {
            $location = $request->location;
        } else if ($request->city != null && $request->city != 'null') {
            $id = Location::whereIn('city_id', array($request->city))->pluck('id')->toArray();
            $location = empty($id) ? '0' : implode(',', $id);
        } else if ($request->states != null && $request->states != 'null') {
            $id = Location::whereIn('state_id', array($request->states))->pluck('id')->toArray();
            $location = empty($id) ? '0' : implode(',', $id);
        } else if ($request->zone != null && $request->zone != 'null') {
            $id = Location::whereIn('zone', array($request->zone))->pluck('id')->toArray();
            $location = empty($id) ? '0' : implode(',', $id);
        } else if ($request->country != null && $request->country != 'null') {
            $id = Location::whereIn('country_id', array($request->country))->pluck('id')->toArray();
            $location = empty($id) ? '0' : implode(',', $id);
        }
        $department = isset($request->department) ? $request->department : 'null';
        $createdFrom = isset($request->createdFrom) ? $request->createdFrom : 'mull';
        $createdTo = isset($request->createdTo) ? $request->createdTo : 'null';
        $added_from = isset($request->added_from) ? $request->added_from : 'null';
        $place = isset($request->place) ? $request->place : 'null';
        $place_type = isset($request->place_type) ? $request->place_type : 'null';
        $dep_name = isset($request->dep_name) ? $request->dep_name : 'null';
        $assign_user_location = isset($request->assign_user_location) ? $request->assign_user_location : 'null';
        $assign_user_internalplace = isset($request->internalplace) ? $request->internalplace : 'null';
        $req_status = isset($request->status) ? $request->status : 'null';
        $req_year = isset($request->year) ? $request->year : 'null';
        if (isset($request->category) && $request->category != 'null') {
            $catId = explode(',', $request->category);
            $model_id = Model::whereIn('category_id', $catId)->pluck('id');
            $model_id = $model_id->isEmpty() ? [0] : $model_id;
        }

        if (isset($request->model) && $request->model != 'null') {
            $model_id = explode(',', $request->model);
        }

        if (isset($request->cat_name)) {
            $catId = Category::where('name', $request->cat_name)->where('category_type', 'asset')->pluck('id')->toArray();
            $model_id = Model::whereIn('category_id', $catId)->pluck('id');
            $model_id = $model_id->isEmpty() ? [0] : $model_id;
        }

        if (isset($request->mdl_name)) {
            $model_id = Model::where('name', $request->mdl_name)->pluck('id');
            $model_id = $model_id->isEmpty() ? [0] : $model_id;
        }
        if (!Auth::user()->isSuperUser() && !Settings::getSettings()->full_multiple_companies_support) {
            $compObj->where('id', '=', Auth::user()->company_id);
        }
        $companies = $compObj->get()->toArray();
        // $companies = Company::select("id", "name as text")->get()->toArray();
        $labels = Label::whereNull('deleted_at')->wherenull('sold')->orderBy('name')->select('id', 'name as text')->get()->toArray();
        $filterLabels = Label::whereNull('deleted_at')->orderBy('name')->select('id', 'name as text')->get()->toArray();
        $deployedLabel = Label::getDeployedLabel()->id;
        $firstDeployable = Label::getFirstDeployable();
        $sort_fields = [
            ['id' => 1, 'text' => 'Device Tag'],
            ['id' => 2, 'text' => 'Model Name'],
            ['id' => 3, 'text' => 'Location Name'],
            ['id' => 4, 'text' => 'Status'],
            ['id' => 5, 'text' => 'Assigned To'],
            ['id' => 6, 'text' => 'Checkout Date'],
            ['id' => 7, 'text' => 'Updated On'],
            ['id' => 8, 'text' => 'Purchase Date']
        ];
        $currencies = Currency::getCurrencies();

        /* view data */
        $vd = new stdClass();
        $vd->manufacturers = Manufacture::select('id', 'name as text')->orderBy('name')->get();
        $vd->models = Model::select('id', 'name as text')->orderBy('name')->get();
        $vd->categoires = Category::where('category_type', '=', 'asset')->select('id', 'name as text')->orderBy('name')->get();
        // $vd->categoires = Category::select('id', 'name as text')->orderBy('name')->get();
        $vd->locations = Location::select('id', 'name as text')->orderBy('name')->get();
        $vd->internal_places = Place::select('places.id', DB::raw('CONCAT(locations.name, " - ", places.place) as text'))
            ->leftJoin('locations', 'places.location_id', 'locations.id')
            ->orderBy('places.place')
            ->get();
        $vd->project = Project::select('id', 'name as text')->orderBy('name')->get();
        $vd->status = AssetAllocationType::select('id', 'name as text')->orderBy('name')->get();
        $vd->places = Place::selectOptions();
        $vd->assetType = AssetType::select('id', 'name as text')->orderBy('name')->get();
        $vd->assignedForOptions = Device::getAssignedForOptions();
        $vd->allocationType = AssetAllocationType::select('id', 'name as text')->orderBy('name')->get();
        $device_custom_field = Setting::select('custom_fieldset_id')->first();
        $deviceMain = new AssetExpense();
        $vd->customField = null;
        if ($device_custom_field && !empty($device_custom_field)) {
            $vd->customField = DB::table('custom_field_custom_fieldset')
                ->leftJoin('custom_fields as cf', 'cf.id', '=', 'custom_field_custom_fieldset.custom_field_id')
                ->where('custom_field_custom_fieldset.custom_fieldset_id', $device_custom_field->custom_fieldset_id)
                ->select('custom_field_custom_fieldset.custom_field_id as id', 'cf.name as text')
                ->orderBy('cf.name')
                ->get();
        }
        $companyFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
        $rfids = DeviceRfid::distinct('device_rfid')->get()->pluck('device_rfid');
        $settings = Settings::select('cost_earned')->first();

        return view('devices.index')->withStatus($status)->withStatusLabels($labels)->withCompanies($companies)->with('filterLabels', $filterLabels)->with('sort_fields', $sort_fields)->withCurrencies($currencies)->with('deployedLabel', $deployedLabel)->with('firstDeployable', $firstDeployable)->with('vd', $vd)->with('companyFieldset', $companyFieldset)->with('location', $location)->with('department', $department)->with('rfids', $rfids)->with('createdFrom', $createdFrom)->with('createdTo', $createdTo)->with('settings', $settings)->with('added_from', $added_from)->with('deviceMain', $deviceMain)->with('filterModel', $model_id)->with('filterPlace', $place)->with('place_type', $place_type)->with('dep_name', $dep_name)->with('req_year', $req_year)->with('req_status', $req_status)->with('assign_user_location', $assign_user_location)->with('purchaseFilter', $purchaseFilter)->with('patch_device_ids', $patch_device_ids)->with('patch_filter', $patch_filter)->with('assign_user_internalplace', $assign_user_internalplace);
    }

    public function ajaxIndex(Request $request)
    {
        $return = ["total" => 0, "filtered" => 0, "data" => []];
        $req = $request->all();
        $found_custom_keys = [];
        $custom_fields_code = CustomField::getAllFieldsAsCode();
        if (count($custom_fields_code)) {
            foreach ($custom_fields_code as $fck) {
                $prefixed_code = CustomField::getPrefixedFieldCode($fck);
                $found_custom_keys[] = $prefixed_code;
            }
        }

        $fields = array(
            '1' => 'a.asset_tag',
            '2' => 'mdl_name',
            '3' => 'loc_name',
            '4' => 'lbl_name',
            '5' => 'checkout',
            '6' => 'last_checkout',
            '7' => 'a.updated_at',
            '8' => 'a.purchase_date'
        );

        $db = DB::table('assets as a');
        $db->leftJoin('models as mdl', 'mdl.id', '=', 'a.model_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'mdl.manufacturer_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.rtd_location_id');
        $db->leftJoin('places as pla', 'pla.id', '=', 'a.internal_place_id');
        $db->leftJoin('users as u', 'u.id', '=', 'a.assigned_to');
        $db->leftJoin('users as own', 'own.id', '=', 'a.asset_owner');
        $db->leftJoin('lease_agreements as lease', 'lease.id', '=', 'a.lease_id');
        $db->leftJoin('projects as pr', 'pr.id', '=', 'a.last_checkout_project');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');
        $db->leftJoin('assets_types as at', 'at.id', '=', 'a.asset_type_id');
        $db->leftJoin('itm_network_inventory_basic as itm', 'itm.device_id', '=', 'a.id');
        $db->leftJoin('itm_network_inventory_basic_azure as itm_azure', 'itm_azure.device_id', '=', 'a.id');
        $db->leftJoin('places as pldev', function ($q) {
            $q->on('pldev.id', '=', 'a.stock_place');
        });
        $db->leftJoin('transfer_items as ti', function ($q) {
            $q->on('ti.device_id', '=', 'a.id');
            $q->where('ti.transfer_status', '=', '1');
        });
        $db->leftJoin('places as p', function ($q) {
            $q->on('p.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '2');
        });
        $db->leftJoin('transfers as t', 't.id', '=', 'ti.transfer_id');
        $db->leftJoin('locations as pldevloc', 'pldevloc.id', '=', 'pldev.location_id');
        $db->leftJoin('locations as ploc', 'ploc.id', '=', 'p.location_id');
        $db->leftJoin('locations as loc_from', 'loc_from.id', '=', 't.transfer_from');
        $db->leftJoin('locations as loc_to', 'loc_to.id', '=', 't.transfer_to');
        $db->leftJoin('locations as ni_loc', 'ni_loc.id', '=', 'a.ni_detected_location');
        // if( isset($req['assigned_to_dept']) && $assigned_to_dept = trim($req['assigned_to_dept']) ){
        $db->leftJoin('departments as dept', 'dept.id', '=', 'u.department_id');
        // }

        $db->leftJoin('status_labels as lbl', 'lbl.id', '=', 'a.status_id');
        $db->leftJoin('categories as cat', function ($q) {
            $q->on('cat.id', '=', 'mdl.category_id');
            $q->where('mdl.category_id', '<>', 0);
        });
        $db->leftJoin('asset_logs as al', function ($join) {
            $join->on('a.chkout_log_id', '=', 'al.id');
            $join->on('a.status_id', '=', DB::raw('6'));
        });
        $db->leftJoin('gate_pass_items as gpi', function ($join) {
            $join->on('gpi.item_id', '=', 'a.id')
                ->where('gpi.item_type', '=', 1);
        });

        $db->leftJoin('itm_asset_allocation_type as ata', 'ata.id', '=', 'al.allocation_type_id');
        $db->select(DB::raw('(CASE WHEN a.assigned_to IS NOT NULL THEN (SELECT COUNT(*) FROM assets WHERE assigned_to = a.assigned_to)ELSE 0 END) as assigned_count'), 'a.id', 'a.asset_tag', 'itm.id as itm_id', 'itm.is_AD as itm_is_AD', 'itm_azure.id as itm_azure_id', 'a.status_id', 'a.uuid', 'a.order_number', 'mdl.name as mdl_name', 'mnu.name as manu_name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'cat.name as cat_name', 'a.serial', 'a.company_id', 'a.assigned_to', 'a.added_from', 'a.ip', 'a.mac', 'a.high_pririty', 'a.assigned_for', 'a.device_occure_type', 'lbl.sold as sold', 'lbl.deployed as deployed', 'lbl.stolen_item as stolen_item', 'itm.is_dupe', 'itm.is_virtual', 'a.accepted', 'a.warranty_months', 'a.invoice_id', 'a.warranty_status', 'loc_from.name as transfer_from', 'loc_to.name as transfer_to', 'a.asset_type_id', 'u.employee_num', 'a.device_rfid', 'a.department_id', 'a.rdp_status', 'a.node_id', 't.batch_code', 'a.ni_detected_location', 'itm.agentVersion', 'gpi.gate_pass_id as gatepass_id');
        $db->addSelect(DB::raw('case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end as project_details'));
        $db->addSelect(DB::raw('case when u.employee_num is not null and u.employee_num !="" then concat(" (", u.employee_num ,")") else u.employee_num end as employee_number'));
        $db->addSelect('u.avatar as profile_img');
        $db->addSelect(DB::raw('case when u.displayName is not null then u.displayName else concat(u.first_name, " ", u.last_name) end as full_name'));
        $db->addSelect(DB::raw('case when ti.transfer_status = 1 then "Under Transfer"  else "" end as device_transfer_status'));
        // $db->addSelect(DB::raw('concat(own.first_name, " ", own.last_name) as asset_owner'));
        $db->addSelect(DB::raw('case when own.displayName is not null then own.displayName else concat(own.first_name, " ", own.last_name) end as asset_owner'));
        $db->addSelect(DB::raw("case when a.assigned_to is not null and a.assigned_to > 0 then 2 when lbl.deployable = 1 and lbl.archived = 0 then 1 else 0 end as check_action"));
        $db->addSelect(DB::raw('case when u.job_type in (1,2) then u.ex_user_company else cmp.name end as cmp_name'));
        $db->addSelect(DB::raw("case when a.name is not null then a.name when a.name is null then '' end as name"));
        $db->addSelect(DB::raw('DATE_FORMAT(a.last_checkout, "%d %b %Y %h:%i %p") as last_checkout_on'));
        $dateTimeFormatSqlDate = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(a.last_checkout, '{$dateTimeFormatSqlDate}') as last_checkout_date"));
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y %h:%i %p") as device_purchase_date'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y") as dev_purchase_date'));
        $db->addSelect(DB::raw('DATE_FORMAT(pur.invoice_date, "%d %b %Y") as inv_date'));
        $db->addSelect(DB::raw('case when a.device_occure_type = 1 then DATE_FORMAT(lease.end_date, "%d %b %Y ") else "" end as end_date'));
        $dateTimeFormatSql = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(a.updated_at, '{$dateTimeFormatSql}') as last_updated_at"));
        $db->addSelect(DB::raw('DATE_FORMAT(a.warranty_start_date, "%d %b %Y") as warranty_start_date'));
        $dateTimeFormatSqlwarrenty = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(a.warrenty_end_date, '{$dateTimeFormatSqlwarrenty}') as warranty_end_date"));
        $db->addSelect(DB::raw('DATE_FORMAT(a.deleted_at, "%d %b %Y %h:%i %p") as last_deleted_at'));
        $db->addSelect(DB::raw('case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end as lbl_name'));
        $db->addSelect('a.image', 'mdl.image_thumbnail', 'p.place', 'ploc.name as place_loc', 'mnu.attachment', 'cat.image_thumbnail as cat_img');
        $db->addSelect('pldev.place as stock_place', 'pldevloc.name as pldevloc_name');

        $db->addSelect(DB::raw('case when a.added_from = 1 then "Manually Added" when a.added_from = 2 then "Via Network" when a.added_from = 3 then "Via Azure" else "" end as added_from_text'));
        $db->addSelect(DB::raw('case when a.high_pririty = 1 then "High Priority" else "" end as high_pririty_text'));
        $db->addSelect(DB::raw('case when a.stock_place is not null and a.assigned_for is null then "Stock Place" else "" end as stock_place_text'));
        $db->addSelect(DB::raw('case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" when a.device_occure_type = 0 then "Purchase Device" else "" end as device_occure_type_name'));
        $db->addSelect(DB::raw('case when a.assigned_for = 1 then "Checkout to User" when a.assigned_for = 2 then "Checkout to Place" else "" end as checkout_text'));
        $db->addSelect(DB::raw('case when a.assigned_for = 1 then "User" when a.assigned_for = 2 then "Place" else stock_place end as checkout_to_text'));
        $db->addSelect(DB::raw('case when a.assigned_for = 1 then concat(u.first_name, " ", u.last_name) when a.assigned_for = 2 then p.place else "" end as checkout'));

        $db->addSelect(DB::raw('case when ((a.warranty_status = 1 or a.warranty_status = 2) and a.calc_warranty_expire_date is not null) then DATE_FORMAT(a.calc_warranty_expire_date, "%d %b %Y") else "" end as consolidated_warrenty_end_date'));
        $db->addSelect(DB::raw('case when a.warranty_status = 1 then "Expired" when a.warranty_status = 2 then "Not Expired" when a.warranty_status = 3 then "Not Applicable" when a.warranty_status = 4 then "Warranty End Date and OEM Date not matching" when a.warranty_status = 5 then "Purchase Date and OEM Date not matching" when a.warranty_status = 6 then "Invoice Date and OEM Date not matching" else "" end as warranty_status_text'));
        $db->addSelect(DB::raw("CASE WHEN lbl.stolen_item = 1 THEN 1 WHEN lbl.id = 4 OR lbl.deployed = 1 OR lbl.pending = 1 OR lbl.archived = 1 OR ti.transfer_status = 1 THEN 0 ELSE 1 END AS dispose_allowed"));
        $db->addSelect('ni_loc.name as ni_detected_location_name');

        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if ($settings->location_config == 1) {
            if (empty($loc_previllage)) {
                $db->where('a.rtd_location_id', '=', 0);
            } else {
                $db->whereIn('a.rtd_location_id', $permitted_loc);
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

        if (isset($req['assigned_to_dept']) && $assigned_to_dept = trim($req['assigned_to_dept'])) {
            $db->where("u.department_id", "=", $assigned_to_dept);
        }

        if (isset($req["showDeletedDevices"]) && $req["showDeletedDevices"] == "true") {
            $db->whereNotNull('a.deleted_at');
        } else {
            $db->whereNull('a.deleted_at');
        }

        if (isset($req["status"]) && $req["status"]) {
            $db->where("a.status_id", "=", $req["status"]);
        }

        if (isset($req["audit"]) && $req["audit"]) {
            $lowerAudit = strtolower($req["audit"]);
            if ($lowerAudit != 'total') {
                $db->where("a.accepted", "=", (string) $lowerAudit);
                $db->where("a.assigned_for", "=", 1);
                $db->whereNotNull("a.assigned_to");
            } else {
                $db->where("a.assigned_for", "=", 1);
                $db->whereNotNull("a.assigned_to");
            }
        }

        if (isset($req['patch_filter']) && $req['patch_filter'] == 1) {
            $patch_device_ids = explode(',', $req['patch_device_ids']);
            $db->whereIn("a.id", $patch_device_ids);
        }

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn('a.company_id', $companyIds);
        // if( isset($req['company_id']) && $company_id = trim($req['company_id']) ){
        //     $db->where("a.company_id", "=", $company_id);
        // }

        $model_filter = isset($req['filter_model']) ? $req['filter_model'] : $request->input("filter_model", null);
        if ($model_filter != null && $model_filter != 'null') {
            $db->whereIn("a.model_id", $model_filter);
        }
        $filter_dep_name = isset($req['filter_dep_name']) ? $req['filter_dep_name'] : $request->input("filter_dep_name", null);
        if ($filter_dep_name != null && $filter_dep_name != 'null') {
            $db->where("dept.name", $filter_dep_name);
            $db->where("a.assigned_for", "=", 1);
            $db->whereNotNull("a.assigned_to");
        }
        $filter_place_type = isset($req['filter_place_type']) ? $req['filter_place_type'] : $request->input("filter_place_type", null);
        $place_filter = isset($req['filter_place']) ? $req['filter_place'] : $request->input("filter_place", null);
        if ($place_filter != null && $place_filter != 'null' && $filter_place_type != null && $filter_place_type != 'null') {
            if ($filter_place_type == 'checkout_place') {
                $db->where("a.assigned_for", 2);
                $db->where("a.status_id", 6);
                $db->where("p.place", $place_filter);
            } else if ($filter_place_type == 'stock_place') {
                $db->where("a.status_id", '!=', 6);
                $db->where("pldev.place", $place_filter);
            } else if ($filter_place_type == 'amc_expired_place') {
                $db->where("p.place", $place_filter);
                $now = Carbon::now(config('app.timezone'))->Format('Y-m-d');
                $db->where("a.amc_expire_date", $now);
            }
        }
        $location_filter = isset($req['location']) ? $req['location'] : $request->input("location", null);
        if ($location_filter != null && $location_filter != 'null') {
            $db->whereIn("a.rtd_location_id", explode(",", $location_filter));
        }

        $internalplace_filter = isset($req['internalplace']) ? $req['internalplace'] : $request->input("internalplace", null);
        if ($internalplace_filter != null && $internalplace_filter != 'null') {
            $db->whereIn("a.internal_place_id", explode(",", $internalplace_filter));
        }
        $assign_user_location_filter = isset($req['assign_user_location']) ? $req['assign_user_location'] : $request->input("assign_user_location", null);
        if ($assign_user_location_filter != null && $assign_user_location_filter != 'null') {
            $db->where(function ($query) use ($assign_user_location_filter) {
                $query->where("a.assigned_for", 1)->where("a.status_id", 6)->whereIn("u.location_id", explode(",", $assign_user_location_filter));
            });
        }
        $assign_user_internalplace_filter = isset($req['assign_user_internalplace']) ? $req['assign_user_internalplace'] : $request->input("assign_user_internalplace", null);
        if ($assign_user_internalplace_filter != null && $assign_user_internalplace_filter != 'null') {
            $db->where(function ($query) use ($assign_user_internalplace_filter) {
                $query->where("a.assigned_for", 1)->where("a.status_id", 6)->whereIn("u.internal_place_id", explode(",", $assign_user_internalplace_filter));
            });
        }

        $department_filter = isset($req['department']) ? $req['department'] : $request->input("department", null);
        if ($department_filter != null && $department_filter != 'null') {
            $db->whereIn("a.department_id", explode(",", $department_filter));
        }
        $added_from_filter = isset($req['added_from']) ? $req['added_from'] : $request->input("added_from", null);
        if ($added_from_filter != null && $added_from_filter != 'null') {
            if ($added_from_filter == 1) {
                $db->whereNull("itm.device_id")
                    ->whereNull("itm_azure.device_id");
            } elseif ($added_from_filter == 2) {
                $db->whereNotNull("itm.device_id");
            } elseif ($added_from_filter == 3) {
                $db->whereNotNull("itm_azure.device_id");
            }
        }

        if (isset($req["req_status"]) && $req["req_status"] && $req["req_status"] != "null") {
            $db->where('a.status_id', $req["req_status"]);
        }

        if (isset($req["req_year"]) && $req["req_year"] && $req["req_year"] != "null") {
            $year_range = explode('-', $req["req_year"]);
            if (!empty($year_range) && isset($year_range[1]) && $year_range != "null") {
                $startDate = Carbon::createFromDate($year_range[0], 4, 1)->startOfDay();
                $endDate = Carbon::createFromDate($year_range[1], 3, 31)->endOfDay();
                $db->whereBetween('a.purchase_date', [$startDate, $endDate]);
            } else {
                $db->whereYear('a.purchase_date', $req["req_year"]);
            }
        }

        if (isset($req["createdFrom"]) && $req["createdFrom"] && $req["createdFrom"] != "null" && isset($req["createdTo"]) && $req["createdTo"] && $req["createdTo"] != "null") {
            $from_date = CommonHelper::getDateAs($req["createdFrom"], "Y-m-d", "d/m/Y");
            $to_date = CommonHelper::getDateAs($req["createdTo"], "Y-m-d", "d/m/Y");
            if ($from_date && $to_date) {
                $db->wherebetween('a.created_at', [$from_date, $to_date]);
            }
        }

        if (isset($request->q) && $request->q != null) {
            $decoded = array_map('intval', explode(',', trim(base64_decode($request->q), '"')));
            $db->whereIn('a.id', $decoded);
        }

        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];

        $is_searching = false;
        if (isset($req["filters"])) {
            $filters = $req["filters"];

            $db->where(function ($query) use ($filters) {
                if (isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                    $query->where("mdl.manufacturer_id", "=", (int) $filters['manufacturer']);
                }
                if (isset($filters["rdp_status"])) {
                    $query->where("a.rdp_status", "=", (int) $filters['rdp_status']);
                    if ($filters['rdp_status'] == 1) {
                        $query->whereNotNull("a.node_id");
                    } else {
                        $query->whereNull("a.node_id");
                    }
                }
                if (isset($filters["model"]) && $filters['model'] && $filters['model'] != "null") {
                    $query->whereIn("a.model_id", $filters['model']);
                }
                if (isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                    $query->whereIn("mdl.category_id", $filters['category']);
                }
                if (isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                    $query->whereIn("a.rtd_location_id", $filters['location']);
                }
                if (isset($filters["user_location"]) && $filters['user_location'] && $filters['user_location'] != "null") {
                    $query->where("a.assigned_for", 1);
                    $query->where("a.status_id", 6);
                    $query->whereIn("u.location_id", $filters['user_location']);
                }
                if (isset($filters["user_internalplace"]) && $filters['user_internalplace'] && $filters['user_internalplace'] != "null") {
                    $query->where("a.assigned_for", 1);
                    $query->where("a.status_id", 6);
                    $query->whereIn("u.internal_place_id", $filters['user_internalplace']);
                }
                if (isset($filters["user_base_location"]) && $filters['user_base_location'] && $filters['user_base_location'] != "null") {
                    // $query->where("a.assigned_to", "=", 1);
                    $query->where("a.status_id", 6);
                    $query->whereIn("u.base_location_id", $filters['user_base_location']);
                }
                if (isset($filters["assigned_user"]) && $filters['assigned_user'] && $filters['assigned_user'] != "null") {
                    $query->where("a.assigned_to", "=", (int) $filters['assigned_user']);
                    $query->where("a.assigned_for", "=", 1);
                }
                if (isset($filters["purchase_reference"]) && !empty($filters["purchase_reference"]) && $filters['purchase_reference'] != "null") {
                    $query->whereIn("a.invoice_id", $filters['purchase_reference']);
                }
                if (isset($filters["stock_location"]) && $filters['stock_location'] && $filters['stock_location'] != "null") {
                    $query->whereIn("pldevloc.id", $filters['stock_location']);
                    $query->where("a.assigned_for", "=", null);
                }
                if (isset($filters["assigned_place"]) && $filters['assigned_place'] && $filters['assigned_place'] != "null") {
                    $query->whereIn("a.assigned_to", $filters['assigned_place']);
                    $query->where("a.assigned_for", "=", 2);
                }
                if (isset($filters["stock_place"]) && $filters['stock_place'] && $filters['stock_place'] != "null") {
                    if ($filters['stock_place'] == "empty_stock_place") {
                        $query->where("a.stock_place", "=", null);
                        $query->where("a.assigned_for", "=", null);
                    } else {
                        $query->whereIn("a.stock_place", $filters['stock_place']);
                        $query->where("a.assigned_for", "=", null);
                    }
                }
                if (isset($filters["asset_type_id"]) && $filters['asset_type_id'] && $filters['asset_type_id'] != "null") {
                    $query->whereIn("a.asset_type_id", $filters['asset_type_id']);
                }
                if (isset($filters["device_occure_type"]) && $filters['device_occure_type'] && $filters['device_occure_type'] != "null") {
                    $query->whereIn("a.device_occure_type", $filters['device_occure_type']);
                }
                if (isset($filters["last_checkout_project"]) && $filters['last_checkout_project'] && $filters['last_checkout_project'] != "null") {
                    $query->where("a.last_checkout_project", "=", (int) $filters['last_checkout_project']);
                }
                if (isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                    $query->where("u.department_id", "=", (int) $filters['department']);
                }
                if (isset($filters["asset_owner"]) && $filters['asset_owner'] && $filters['asset_owner'] != "null") {
                    $query->where("a.asset_owner", "=", (int) $filters['asset_owner']);
                }
                if (isset($filters["device_assigned_to"]) && $filters['device_assigned_to'] && $filters['device_assigned_to'] != "null") {
                    $query->where("a.assigned_for", "=", (int) $filters['device_assigned_to']);
                }
                if (isset($filters["added_from"]) && $filters['added_from'] && $filters['added_from'] != "null") {
                    $query->where("a.added_from", "=", (int) $filters['added_from']);
                }
                if (isset($filters["detected_on"]) && $filters['detected_on'] && $filters['detected_on'] != "null") {
                    if ($filters["detected_on"] == 1) {
                        $query->whereNull("itm.device_id")->whereNull("itm_azure.device_id");
                    } else if ($filters["detected_on"] == 2) {
                        $query->whereNotNull("itm.device_id");
                    } else if ($filters["detected_on"] == 3) {
                        $query->whereNotNull("itm_azure.device_id");
                    } else if ($filters["detected_on"] == 4) {
                        $query->whereNotNull("a.node_id");
                    }
                }
                if (isset($filters["filter_by_device_with_rfid"]) && $filters['filter_by_device_with_rfid'] && $filters['filter_by_device_with_rfid'] != "null") {
                    $rfidDevices = DeviceRfid::groupBy('device_id')->pluck('device_id')->toArray();
                    if ($filters["filter_by_device_with_rfid"] == 1) {
                        $query->whereIn("a.id", $rfidDevices);
                    } else {
                        $query->whereNotIn("a.id", $rfidDevices);
                    }
                }
                if (isset($filters["allocation_type"]) && $filters['allocation_type'] && $filters['allocation_type'] != "null") {
                    $query->where("ata.id", "=", (int) $filters['allocation_type']);
                }
                if (isset($filters["audit_confirmation"]) && $filters['audit_confirmation'] && $filters['audit_confirmation'] != "null") {
                    $query->where("a.accepted", "=", (string) $filters['audit_confirmation']);
                    $query->where("a.assigned_for", "=", 1);
                    $query->whereNotNull("a.assigned_to");
                }
                if (isset($filters["asset_tag"]) && $filters['asset_tag'] && $filters['asset_tag'] != "null") {
                    $query->whereIn("a.id", $filters['asset_tag']);
                }
                if (isset($filters["rfid"]) && $filters['rfid'] && $filters['rfid'] != "null") {
                    $ids = DeviceRfid::where('device_rfid', $filters['rfid'])->pluck('device_id')->toArray();
                    $query->whereIn("a.id", $ids);
                }
                if (isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                    $query->whereIn("a.internal_place_id", $filters['internal_place']);
                }
                if (isset($filters["asset_department"]) && $filters['asset_department'] && $filters['asset_department'] != "null") {
                    $query->whereIn("a.department_id", $filters['asset_department']);
                }
                if (isset($filters["sez_device"]) && $filters['sez_device'] != "null") {
                    $query->where("a.sez_device", (int) $filters["sez_device"]);
                }
                if (isset($filters["high_priority_device"]) && $filters['high_priority_device'] != "null") {
                    $query->where("a.high_pririty", (int) $filters['high_priority_device']);
                }
                if (isset($filters["requestable"]) && $filters['requestable'] != "null") {
                    $query->where("a.requestable", (int) $filters["requestable"]);
                }
                if (isset($filters["under_transfer_device"]) && $filters['under_transfer_device'] != "null") {
                    if ($filters["under_transfer_device"] == "1") {
                        $query->whereExists(function ($q) {
                            $q->select(DB::raw(1))
                                ->from('transfer_items as ti')
                                ->whereRaw('ti.device_id = a.id')
                                ->where('ti.transfer_status', '=', 1);
                        });
                    } else {
                        $query->whereNotExists(function ($q) {
                            $q->select(DB::raw(1))
                                ->from('transfer_items as ti')
                                ->whereRaw('ti.device_id = a.id')
                                ->where('ti.transfer_status', '=', 1);
                        });
                    }
                }
                $now = Carbon::now(config('app.timezone'))->startOfDay()->format('Y-m-d');
                if (isset($filters["warranty_status"]) && $filters['warranty_status'] !== "" && $filters['warranty_status'] !== "null") {
                    switch ($filters["warranty_status"]) {
                        case 1:
                            $query->where("a.warranty_status", 2)->whereNotNull("a.calc_warranty_expire_date")->whereDate("a.calc_warranty_expire_date", ">=", $now);
                            break;
                        case 2:
                            $query->where("a.warranty_status", 1)->whereNotNull("a.calc_warranty_expire_date")->whereDate("a.calc_warranty_expire_date", "<", $now);
                            break;
                        case 3:
                            $query->where("a.warranty_status", 3);
                            break;
                        case 4:
                            $query->where("a.warranty_status", 4);
                            break;
                        case 5:
                            $query->where("a.warranty_status", 5);
                            break;
                        case 6:
                            $query->where("a.warranty_status", 6);
                            break;
                    }
                }

                if (isset($filters["custom_field"]) && $filters['custom_field'] && $filters['custom_field'] != "null") {
                    $customField = CustomField::find($filters["custom_field"]);
                    $dev_name = '_itm_' . '' . str_replace(' ', '_', strtolower($customField->name));
                    $device_custom_field = Device::select($dev_name . " as text")->where($dev_name, '!=', null)->get()->toArray();
                    if (isset($filters["custom_field_value"]) && $filters['custom_field_value'] && $filters['custom_field_value'] != "null") {
                        $query->whereIn($dev_name, $filters["custom_field_value"]);
                    }
                }

                $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.last_checkout', '3' => 'a.expected_checkin', '4' => 'a.amc_expire_date', '5' => 'a.warranty_start_date', '6' => 'a.warrenty_end_date', '7' => 'a.created_at', '8' => 'u.doj', '9' => 'a.calc_warranty_expire_date'];
                if (isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 9) {
                    if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                        $daterange = explode(" - ", $filters["date_range"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if ($from_date && $to_date) {
                            if ($filters['based_on'] == 3 || $filters['based_on'] == 8) {
                                $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s" and a.status_id = 6)', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            } else {
                                $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            }
                            $query->whereRaw($whereStr);
                        }
                    }
                }

                if (isset($filters["mapped_location"]) && $filters['mapped_location'] && $filters['mapped_location'] != "null") {
                    $query->whereIn("a.ni_detected_location", $filters['mapped_location']);
                }
                if (isset($filters['device_size']) && $filters['device_size'] != "null") {
                    $size = $filters['device_size'];
                    if (isset($filters["condition_by_user_assign"]) && $filters["condition_by_user_assign"] != "null") {
                        if ($filters['condition_by_user_assign'] == 1) {
                            $query->whereRaw('CASE WHEN a.assigned_to IS NOT NULL and a.assigned_for = 1 THEN (SELECT COUNT(*) FROM assets WHERE assigned_to = a.assigned_to and assigned_for = 1) ELSE 0 END > ?', [$filters['device_size']]);
                        } elseif ($filters['condition_by_user_assign'] == 2) {
                            $query->whereRaw('CASE WHEN a.assigned_to IS NOT NULL and a.assigned_for = 1 THEN (SELECT COUNT(*) FROM assets WHERE assigned_to = a.assigned_to and assigned_for = 1) ELSE 0 END < ?', [$filters['device_size']]);
                        } else {
                            $query->whereRaw('CASE WHEN a.assigned_to IS NOT NULL and a.assigned_for = 1 THEN (SELECT COUNT(*) FROM assets WHERE assigned_to = a.assigned_to and assigned_for = 1) ELSE 0 END = ?', [$filters['device_size']]);
                        }
                    }
                    $query->where(function ($s) use ($size) {
                        $s->havingRaw('CASE WHEN a.assigned_to IS NOT NULL and a.assigned_for = 1 THEN (SELECT COUNT(*) FROM assets WHERE assigned_to = a.assigned_to and assigned_for = 1) ELSE 0 END = ?', [$size]);
                    });
                }
            });

            $is_searching = true;
        }

        if (isset($req["search"]) && $search_key = addslashes(trim($req["search"]["value"]))) {
            $whereStr = sprintf('(a.asset_tag like "%%%1$s%%" or a.product_number like "%%%1$s%%" or (case when a.assigned_for = 1 then u.employee_num else "" end) like "%%%1$s%%" or a.uuid like "%%%1$s%%" or a.notes like "%%%1$s%%" or a.ip like "%%%1$s%%" or a.order_number like "%%%1$s%%" or a.mac like "%%%1$s%%" or (case when a.added_from = 1 then "Manually Added" when a.added_from = 2 then "Via Network" when a.added_from = 3 then "Via Azure" else "" end) like "%%%1$s%%" or (case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" when a.device_occure_type = 0 then "Purchase Device" else "" end) like "%%%1$s%%" or (case when a.high_pririty = 1 then "High Priority" else "" end) like "%%%1$s%%" or (case when a.stock_place is not null and a.assigned_for is null then "Stock Place" else "" end) like "%%%1$s%%" or (case when a.assigned_for = 1 then "Checkout to User" when a.assigned_for = 2 then "Checkout to Place" else "" end ) like "%%%1$s%%" or at.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or (case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end) like "%%%1$s%%" or a.name like "%%%1$s%%" or mdl.name like "%%%1$s%%" or mnu.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or pla.place like "%%%1$s%%" or cmp.name like "%%%1$s%%" or ploc.name like "%%%1$s%%" or (case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end) like "%%%1$s%%" or (a.assigned_for = 1 and u.first_name like "%%%1$s%%") or (a.assigned_for = 2 and p.place like "%%%1$s%%") or (a.assigned_for = 1 and u.last_name like "%%%1$s%%") or concat(u.first_name, " ", u.last_name) like "%%%1$s%%" or a.serial like "%%%1$s%%" or ni_loc.name like "%%%1$s%%" or concat(own.first_name, " ", own.last_name) like "%%%1$s%%" or own.employee_num like "%%%1$s%%" or DATE_FORMAT(a.last_checkout, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.calc_warranty_expire_date, "%%d %%b %%Y") like "%%%1$s%%"', $search_key);
            foreach ($found_custom_keys as $keys) {
                $whereStr .= "OR a." . $keys . " like '%$search_key%'";
            }
            $whereStr .= ")";
            $db->whereRaw($whereStr);
            $is_searching = true;
        }

        if ($is_searching) {
            $return['filtered'] = $db->count();
        }

        if (isset($req["order"]["id"]) && isset($fields[$req["order"]["id"]]) && in_array($req["order"]["dir"], [1, 2])) {
            $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
            $db->orderBy($fields[$req["order"]["id"]], $dir);
        }

        $page = $request->input("page", 1);
        $take = $request->input("size", 10);
        $skip = ($page * $take) - $take;

        // if (isset($filters['device_size']) && $filters['device_size'] != "null") {
        //     $data = $db->get()->toArray(); 
        //     if (!empty($data) && isset($filters['device_size']) && $filters['device_size'] != "null") {
        //         $data = array_filter($data, function ($item) use ($filters) {
        //             if (isset($filters["condition_by_user_assign"]) && $filters["condition_by_user_assign"] != "null") {
        //                 if ($filters['condition_by_user_assign'] == 1) {
        //                     return $item->assigned_count > $filters['device_size'] && $item->assigned_for == 1;
        //                 } elseif ($filters['condition_by_user_assign'] == 2) {
        //                     return $item->assigned_count < $filters['device_size'] && $item->assigned_for == 1;
        //                 } else {
        //                     return $item->assigned_count == $filters['device_size'] && $item->assigned_for == 1;
        //                 }
        //             }
        //             return $item->assigned_count == $filters['device_size'] && $item->assigned_for == 1;
        //         });
        //         $data = array_values($data);

        //         $return['filtered'] = count($data);
        //         if ($return['filtered'] < $skip) {
        //             $page = 1;
        //             $skip = 0;
        //         }
        //     }
        //     $return["data"] = $data;
        // } else {
        if ($return["filtered"] < $skip) {
            $page = 1;
            $skip = 0;
        }

        $db->skip($skip);
        $db->take($take);

        $return["data"] = $db->get();
        // }
        $return["page"] = $page;

        $return["profile_imgs"] = [];
        foreach ($return["data"] as $v) {
            $v->device_img = (
                ($v->image != "" && file_exists(public_path('uploads/devices/' . $v->image))) ? url("uploads/devices") . "/" . $v->image : (($v->image_thumbnail != "" && file_exists(public_path('uploads/models/' . $v->image_thumbnail))) ? url("uploads/models") . "/" . $v->image_thumbnail : (($v->attachment != "" && file_exists(public_path('uploads/manufacturers/' . $v->attachment))) ? url("uploads/manufacturers") . "/" . $v->attachment : (($v->cat_img != "" && file_exists(public_path('uploads/category/' . $v->cat_img))) ? url("uploads/category") . "/" . $v->cat_img : null)))
            );
            if (isset($return["profile_imgs"][$v->assigned_to]) || !$v->assigned_to) {
                continue;
            }
            $tmp_u = User::find($v->assigned_to);
            if ($tmp_u) {
                $return["profile_imgs"][$v->assigned_to] = $tmp_u->getProfileImg();
            }
        }
        return response()->json($return);
    }

    public function ajaxPatchManagementListByDevice(Request $request)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PatchManagerRead') || !config('services.patch_management.enabled')) {
            return response()->json($return);
        }
        $req = $request->all();
        $countTab = $this->ajaxPatchManagementListByDeviceCount($request);
        $ni = Basic::where('device_id', $request->device_id)->first();
        $return['operating_system'] = $ni->OSCaption ?? '';
        if ($req['tab'] != 'system_update_patch') {
            $fields = array(
                'a.caption' => 'caption',
                'a.version' => 'version',
                'a.publisher' => 'publisher',
                'a' => 'pm_severity',
            );

            $db = DB::table('itm_network_inventory_basic as a')
                ->leftJoin('itm_network_inventory_products as pr', 'pr.basic_id', '=', 'a.id')
                ->leftJoin('patch_masters as pm', function ($join) {
                    $join
                        ->on('pm.patch_name', '=', 'pr.Caption')
                        ->whereRaw('pm.version > pr.Version');
                })
                ->leftJoin('agent_os_support as os', 'os.id', '=', 'pm.os');
            $db->select('pr.Caption as caption', 'pr.Publisher as publisher', 'pr.Version as version', 'pr.basic_id as basic_id', 'pr.id as software_id', 'pm.id as patch_id', 'pm.version as patch_version', 'pm.patch_name as pm_name', 'pm.patch_description as pm_description', 'os.os_name as pm_os');
            $db->addSelect(DB::raw('case when pm.version is not null then "Available" else "Updated" end as pm_status'));
            $db->addSelect(DB::raw('case when pm.severity = 1 then "Critical" when pm.severity = 2 then "Important" when pm.severity = 3 then "Moderate" when pm.severity = 4 then "Low" else "Unrated" end as pm_severity'));
            // $db->addSelect(DB::raw('case when pm.os = 2 then "Linux" else "Windows" end as pm_os'));
            $db->addSelect(DB::raw('case when pm.reboot_required = 1 then "Yes" else "No" end as pm_reboot_required'));
            $db->addSelect(DB::raw('case when pm.approved_status = 1 then "Yes" else "No" end as pm_approved_status'));
            $db->whereNull('a.is_dupe');
            $db->whereNull('a.deleted_at');
            $db->where('a.device_id', $req['device_id']);

            if ($req['tab'] == 'all_patch') {
                $pendingPatch = (clone $db)
                    ->whereRaw('? LIKE CONCAT("%", os.os_name, "%")', [$ni->OSCaption])
                    ->whereNotNull('pm.id');
                $completedPatch = (clone $db)
                    ->whereNotNull('pr.id')
                    ->whereNull('pm.id');
                $db = $pendingPatch->unionAll($completedPatch);
            } elseif ($req['tab'] == 'pending_patch') {
                $db
                    ->whereRaw('? LIKE CONCAT("%", os.os_name, "%")', [$ni->OSCaption])
                    ->whereNotNull('pm.id');
            } elseif ($req['tab'] == 'completed_patch') {
                $db->whereNotNull('pr.id')->whereNull('pm.id');
            }
            $data = $db->get();
            if ($req['tab'] == 'missing_patch') {
                $return['data'] = [];
                $deviceCaptions = $data->pluck('caption')->toArray();
                // $patchListing = PatchMaster::all();
                $patchListing = PatchMaster::select('patch_masters.*', 'os.os_name as pm_os')
                    ->leftJoin('agent_os_support as os', 'patch_masters.os', '=', 'os.id')
                    ->whereRaw('? LIKE CONCAT("%", os.os_name, "%")', [$ni->OSCaption])
                    ->get();
                foreach ($patchListing as $patch) {
                    // if (!in_array($patch->patch_name, $deviceCaptions)) {
                    if (!in_array(strtolower($patch->patch_name), array_map('strtolower', $deviceCaptions))) {
                        $logList = PatchManagerRequestDeviceLog::where('device_id', $req['device_id'])->where('log_patch_id', $patch->id)->where('log_system_update_type', 0)->select('status_id', 'message', 'created_at')->get();
                        $d = [
                            'caption' => $patch->patch_name,
                            'version' => $patch->version,
                            'publisher' => $patch->patch_publisher ?? null,
                            'basic_id' => null,
                            'software_id' => null,
                            'patch_id' => $patch->id ?? null,
                            'patch_version' => $patch->version ?? null,
                            'pm_name' => $patch->patch_name ?? null,
                            'pm_description' => $patch->patch_description ?? null,
                            'pm_status' => 'Missing',
                            'pm_severity' => $patch->severity == 4 ? 'Low' : ($patch->severity == 2 ? 'Important' : ($patch->severity == 1 ? 'Critical' : ($patch->severity == 3 ? 'Moderate' : 'Unrated'))),
                            // 'pm_os' => $patch->os == 2 ? 'Linux' : 'Windows',
                            'pm_os' => $patch->pm_os,
                            'pm_reboot_required' => $patch->reboot_required == 1 ? 'Yes' : 'No',
                            'pm_approved_status' => $patch->approved_status == 1 ? 'Yes' : 'No',
                            'log_list' => $logList,
                        ];
                        $return['data'][] = ['a' => $d];
                    }
                }
                $total = count($return['data']);
                $return['recordsTotal'] = $total;
                $return['recordsFiltered'] = $return['recordsTotal'];
                if (isset($req['search']) && $search_key = trim($req['search'])) {
                    $return['data'] = array_filter($return['data'], function ($item) use ($search_key) {
                        return stripos($item['a']['caption'], $search_key) !== false || stripos($item['a']['version'], $search_key) !== false || stripos($item['a']['publisher'], $search_key) !== false;
                    });
                    $return['recordsFiltered'] = count($return['data']);
                }

                if (isset($req['other_filters'])) {
                    $filters = $req['other_filters'];
                    $return['data'] = array_filter($return['data'], function ($item) use ($filters) {
                        $pm_status_match = $pm_severity_match = $pm_publisher_match = true;

                        if (isset($filters['patch_status']) && $filters['patch_status'] !== 'null' && $filters['patch_status'] !== null) {
                            $pm_status_match = stripos($item['a']['pm_status'], $filters['patch_status']) !== false;
                        }
                        if (isset($filters['publisher']) && $filters['publisher'] !== 'null' && $filters['publisher'] !== null) {
                            $pm_publisher_match = stripos($item['a']['publisher'], $filters['publisher']) !== false;
                        }
                        if (isset($filters['severity']) && $filters['severity'] !== 'null' && $filters['severity'] !== null) {
                            $pm_severity_match = stripos($item['a']['pm_severity'], $filters['severity']) !== false;
                        }
                        return $pm_status_match && $pm_severity_match && $pm_publisher_match;
                    });
                    $return['recordsFiltered'] = count($return['data']);
                }

                if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ['asc', 'desc'])) {
                    usort($return['data'], function ($a, $b) use ($fields, $req) {
                        $col = $fields[$req['sorted_column_name']];
                        return $req['sorted_direction'] === 'asc' ? strcmp($a['a'][$col], $b['a'][$col]) : strcmp($b['a'][$col], $a['a'][$col]);
                    });
                }

                $skip = $req['start'] ?? 0;
                $take = $req['length'] ?? 10;
                $paginatedData = array_slice($return['data'], $skip, $take);

                $return['data'] = array_values($paginatedData);
                // $return['recordsFiltered'] = count($return['data']);
                // $return['recordsTotal'] = $total;
            } else {
                $return['data'] = [];
                foreach ($data as $d) {
                    $letestLogs = null;
                    $logList = PatchManagerRequestDeviceLog::where('device_id', $req['device_id'])->where('log_patch_id', $d->patch_id)->where('log_system_update_type', 0)->select('status_id', 'message', 'created_at')->get();
                    if (!$logList->isEmpty()) {
                        $letestLogs = PatchManagerRequestDeviceLog::where('device_id', $req['device_id'])->where('log_patch_id', $d->patch_id)->where('log_system_update_type', 0)->orderByDesc('created_at')->select('status_id', 'message', 'created_at')->first();
                    }
                    $da = [
                        'caption' => $d->caption,
                        'version' => $d->version,
                        'publisher' => $d->publisher ?? null,
                        'basic_id' => $d->basic_id,
                        'software_id' => $d->software_id,
                        'patch_id' => $d->patch_id ?? null,
                        'patch_version' => $d->patch_version ?? null,
                        'pm_name' => $d->pm_name ?? null,
                        'pm_description' => $d->pm_description ?? null,
                        'pm_status' => $letestLogs != null ? ($letestLogs->status_id == 1 ? 'Initiated' : ($letestLogs->status_id == 2 ? 'Processing' : ($letestLogs->status_id == 3 ? 'Incomplete' : 'Updated'))) : $d->pm_status,
                        'pm_severity' => $d->pm_severity,
                        'pm_os' => $d->pm_os,
                        'pm_reboot_required' => $d->pm_reboot_required,
                        'pm_approved_status' => $d->pm_approved_status,
                        'log_list' => $logList,
                    ];
                    $return['data'][] = ['a' => $da];
                }
                $total = count($return['data']);
                $return['recordsTotal'] = $total;
                $return['recordsFiltered'] = $return['recordsTotal'];
                if (isset($req['search']) && $search_key = trim($req['search'])) {
                    $return['data'] = array_filter($return['data'], function ($item) use ($search_key) {
                        return stripos($item['a']['caption'], $search_key) !== false || stripos($item['a']['version'], $search_key) !== false || stripos($item['a']['publisher'], $search_key) !== false;
                    });
                    $return['recordsFiltered'] = count($return['data']);
                }

                if (isset($req['other_filters'])) {
                    $filters = $req['other_filters'];
                    $return['data'] = array_filter($return['data'], function ($item) use ($filters) {
                        $pm_status_match = $pm_severity_match = $pm_publisher_match = true;

                        if (isset($filters['patch_status']) && $filters['patch_status'] !== 'null' && $filters['patch_status'] !== null) {
                            $pm_status_match = stripos($item['a']['pm_status'], $filters['patch_status']) !== false;
                        }
                        if (isset($filters['publisher']) && $filters['publisher'] !== 'null' && $filters['publisher'] !== null) {
                            $pm_publisher_match = stripos($item['a']['publisher'], $filters['publisher']) !== false;
                        }
                        if (isset($filters['severity']) && $filters['severity'] !== 'null' && $filters['severity'] !== null) {
                            $pm_severity_match = stripos($item['a']['pm_severity'], $filters['severity']) !== false;
                        }
                        return $pm_status_match && $pm_severity_match && $pm_publisher_match;
                    });
                    $return['recordsFiltered'] = count($return['data']);
                }

                if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ['asc', 'desc'])) {
                    usort($return['data'], function ($a, $b) use ($fields, $req) {
                        $col = $fields[$req['sorted_column_name']];
                        return $req['sorted_direction'] === 'asc' ? strcmp($a['a'][$col], $b['a'][$col]) : strcmp($b['a'][$col], $a['a'][$col]);
                    });
                }

                $skip = $req['start'] ?? 0;
                $take = $req['length'] ?? 10;
                $paginatedData = array_slice($return['data'], $skip, $take);

                $return['data'] = array_values($paginatedData);
            }
        } else {
            $return['data'] = [];
            $fields = array(
                'a.caption' => 'caption',
                'a.version' => 'version',
                'a.publisher' => 'publisher',
                'a' => 'pm_severity',
            );
            $patchSystemListing = SystemUpdatePatchMaster::where('device_id', $req['device_id'])->get();
            foreach ($patchSystemListing as $patch) {
                $logList = PatchManagerRequestDeviceLog::where('device_id', $req['device_id'])->where('log_patch_id', $patch->id)->where('log_system_update_type', 1)->select('status_id', 'message', 'created_at')->get();
                $d = [
                    'caption' => $patch->name,
                    'version' => $patch->patch_version,
                    'publisher' => $patch->patch_publisher ?? null,
                    'basic_id' => null,
                    'software_id' => null,
                    'patch_id' => $patch->id ?? null,
                    'patch_version' => $patch->patch_version ?? null,
                    'pm_name' => $patch->name ?? null,
                    'pm_description' => $patch->description ?? null,
                    'pm_status' => ($patch->patch_status == 4) ? 'Updated' : (($patch->patch_status == 1) ? 'Initiated' : (($patch->patch_status == 2) ? 'Processing' : (($patch->patch_status == 3) ? 'Incomplete' : 'Available'))),
                    'pm_severity' => $patch->patch_severity == 4 ? 'Low' : ($patch->patch_severity == 2 ? 'Important' : ($patch->patch_severity == 1 ? 'Critical' : ($patch->patch_severity == 3 ? 'Moderate' : 'Unrated'))),
                    'pm_os' => null,
                    'pm_reboot_required' => null,
                    'pm_approved_status' => null,
                    'log_list' => $logList,
                ];
                $return['data'][] = ['a' => $d];
            }
            $total = count($return['data']);
            $return['recordsTotal'] = $total;
            $return['recordsFiltered'] = $return['recordsTotal'];
            if (isset($req['search']) && $search_key = trim($req['search'])) {
                $return['data'] = array_filter($return['data'], function ($item) use ($search_key) {
                    return stripos($item['a']['caption'], $search_key) !== false || stripos($item['a']['version'], $search_key) !== false || stripos($item['a']['publisher'], $search_key) !== false;
                });
                $return['recordsFiltered'] = count($return['data']);
            }

            if (isset($req['other_filters'])) {
                $filters = $req['other_filters'];
                $return['data'] = array_filter($return['data'], function ($item) use ($filters) {
                    $pm_status_match = $pm_severity_match = $pm_publisher_match = true;

                    if (isset($filters['patch_status']) && $filters['patch_status'] !== 'null' && $filters['patch_status'] !== null) {
                        $pm_status_match = stripos($item['a']['pm_status'], $filters['patch_status']) !== false;
                    }

                    if (isset($filters['publisher']) && $filters['publisher'] !== 'null' && $filters['publisher'] !== null) {
                        $pm_publisher_match = stripos($item['a']['publisher'], $filters['publisher']) !== false;
                    }

                    if (isset($filters['severity']) && $filters['severity'] !== 'null' && $filters['severity'] !== null) {
                        $pm_severity_match = stripos($item['a']['pm_severity'], $filters['severity']) !== false;
                    }
                    return $pm_status_match && $pm_severity_match && $pm_publisher_match;
                });
                $return['recordsFiltered'] = count($return['data']);
            }

            // if (isset($req['order'][0]['column']) && isset($fields[$req['order'][0]['column']]) && in_array($req['order'][0]['dir'], ['asc', 'desc'])) {
            //     usort($return['data'], function ($a, $b) use ($fields, $req) {
            //         $col = $fields[$req['order'][0]['column']];
            //         return $req['order'][0]['dir'] === 'asc' ? strcmp($a['a'][$col], $b['a'][$col]) : strcmp($b['a'][$col], $a['a'][$col]);
            //     });
            // }

            if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ['asc', 'desc'])) {
                usort($return['data'], function ($a, $b) use ($fields, $req) {
                    $col = $fields[$req['sorted_column_name']];
                    return $req['sorted_direction'] === 'asc' ? strcmp($a['a'][$col], $b['a'][$col]) : strcmp($b['a'][$col], $a['a'][$col]);
                });
            }

            $skip = $req['start'] ?? 0;
            $take = $req['length'] ?? 10;
            $paginatedData = array_slice($return['data'], $skip, $take);

            $return['data'] = array_values($paginatedData);
            // $return['recordsFiltered'] = count($return['data']);
            // $return['recordsTotal'] = $total;
        }
        $return['tabCount'] = $countTab;
        $return['requested_count'] = PatchManagerRequestDevice::where('device_id', $request->device_id)->where('status_id', '!=', 4)->count();
        return response()->json($return);
    }

    public function ajaxPatchManagementListByDeviceCount(Request $request)
    {
        $req = $request->all();
        $count = [];
        $deviceId = $req['device_id'];
        $ni = Basic::where('device_id', $deviceId)->first();
        $return['operating_system'] = $ni->OSCaption ?? '';
        $patchCounts = DB::table('itm_network_inventory_basic as a')
            ->leftJoin('itm_network_inventory_products as pr', 'pr.basic_id', '=', 'a.id')
            ->leftJoin('patch_masters as pm', function ($join) {
                $join
                    ->on('pm.patch_name', '=', 'pr.Caption')
                    ->whereRaw('pm.version > pr.Version');
            })
            ->leftJoin('agent_os_support as os', 'pm.os', '=', 'os.id')
            ->whereNull('a.is_dupe')
            ->whereNull('a.deleted_at')
            ->where('a.device_id', $deviceId);
        $pendingPatch = (clone $patchCounts)
            ->whereRaw('? LIKE CONCAT("%", os.os_name, "%")', [$ni->OSCaption])
            ->selectRaw('COUNT(CASE WHEN pm.id IS NOT NULL THEN 1 END) AS pending_patch')
            ->first();
        $completedPatch = (clone $patchCounts)
            ->selectRaw('COUNT(CASE WHEN pr.id IS NOT NULL AND pm.id IS NULL THEN 1 END) AS completed_patch')
            ->first();

        $missingPatchCount = 0;
        $deviceCaptions = DB::table('itm_network_inventory_basic as a')
            ->leftJoin('itm_network_inventory_products as pr', 'pr.basic_id', '=', 'a.id')
            ->whereNull('a.is_dupe')
            ->whereNull('a.deleted_at')
            ->where('a.device_id', $deviceId)
            ->pluck('pr.Caption')
            ->toArray();
        if (!empty($deviceCaptions)) {
            // $missingList = PatchMaster::get('patch_name');
            $missingList = PatchMaster::select('patch_masters.patch_name')
                ->leftJoin('agent_os_support as os', 'patch_masters.os', '=', 'os.id')
                ->whereRaw('? LIKE CONCAT("%", os.os_name, "%")', [$ni->OSCaption])
                ->get();
            foreach ($missingList as $key => $value) {
                if (!in_array(strtolower($value->patch_name), array_map('strtolower', $deviceCaptions))) {
                    $missingPatchCount++;
                }
            }
        } else {
            // $missingPatchCount = PatchMaster::count();
            $missingPatchCount = PatchMaster::leftJoin('agent_os_support as os', 'pm.os', '=', 'os.id')
                ->whereRaw('? LIKE CONCAT("%", os.os_name, "%")', [$ni->OSCaption])
                ->count();
        }

        $systemUpdatePatchCount = SystemUpdatePatchMaster::where('device_id', $deviceId)->count();

        $count = [
            'pending_patch' => $pendingPatch->pending_patch ?? 0,
            'completed_patch' => $completedPatch->completed_patch ?? 0,
            'missing_patch' => $missingPatchCount,
            'system_update_patch' => $systemUpdatePatchCount,
            'all_patch' => ($pendingPatch->pending_patch ?? 0) + ($completedPatch->completed_patch ?? 0),
        ];

        return $count;
    }

    public function ajaxPatchManagementListByDeviceExport(Request $request)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PatchManagerDownload') || !config('services.patch_management.enabled')) {
            return response()->json($return);
        }
        $req = $request->all();
        if (isset($req['q'])) {
            $data = base64_decode($req['q']);
            $filter = json_decode($data);
            $ni = Basic::where('device_id', $filter->device_id)->first();
            $return['operating_system'] = $ni->OSCaption ?? '';
            if ($filter->tab != 'system_update_patch') {
                $fields = array(
                    '1' => 'pr.Caption',
                    '2' => 'pr.Version',
                    '3' => 'pm.severity',
                );

                $db = DB::table('itm_network_inventory_basic as a')
                    ->leftJoin('itm_network_inventory_products as pr', 'pr.basic_id', '=', 'a.id')
                    ->leftJoin('patch_masters as pm', function ($join) {
                        $join
                            ->on('pm.patch_name', '=', 'pr.Caption')
                            ->whereRaw('pm.version > pr.Version');
                    })
                    ->leftJoin('agent_os_support as os', 'os.id', '=', 'pm.os');
                $db->select('pr.Caption as caption', 'pr.Version as version', 'pr.basic_id as basic_id', 'pr.id as software_id', 'pm.id as patch_id', 'pm.version as patch_version', 'pm.patch_name as pm_name', 'pm.patch_description as pm_description', 'os.os_name as pm_os');
                $db->addSelect(DB::raw('case when pm.version is not null then concat("Patch Available: ", pm.patch_name," ", pm.version) else "Updated" end as pm_status'));
                $db->addSelect(DB::raw('case when pm.severity = 1 then "Critical" when pm.severity = 2 then "Important" when pm.severity = 3 then "Moderate" when pm.severity = 4 then "Low" else "Unrated" end as pm_severity'));
                // $db->addSelect(DB::raw('case when pm.os = 2 then "Linux" else "Windows" end as pm_os'));
                $db->addSelect(DB::raw('case when pm.reboot_required = 1 then "Yes" else "No" end as pm_reboot_required'));
                $db->addSelect(DB::raw('case when pm.approved_status = 1 then "Yes" else "No" end as pm_approved_status'));
                $db->whereNull('a.is_dupe');
                $db->whereNull('a.deleted_at');
                $db->where('a.device_id', $filter->device_id);

                if ($filter->tab == 'all_patch') {
                    $pendingPatch = (clone $db)
                        ->whereRaw('? LIKE CONCAT("%", os.os_name, "%")', [$ni->OSCaption])
                        ->whereNotNull('pm.id');
                    $completedPatch = (clone $db)
                        ->whereNotNull('pr.id')
                        ->whereNull('pm.id');
                    $db = $pendingPatch->unionAll($completedPatch);
                } elseif ($filter->tab == 'pending_patch') {
                    $db->whereRaw('? LIKE CONCAT("%", os.os_name, "%")', [$ni->OSCaption]);
                    $db->whereNotNull('pm.id');
                } elseif ($filter->tab == 'completed_patch') {
                    $db->whereNotNull('pr.id')->whereNull('pm.id');
                }
                $data = $db->get();

                if ($filter->tab == 'missing_patch') {
                    $return['data'] = [];
                    $fields = array(
                        '1' => 'caption',
                        '2' => 'version',
                    );
                    $deviceCaptions = $data->pluck('caption')->toArray();
                    // $patchListing = PatchMaster::all();
                    $patchListing = PatchMaster::select('patch_masters.*', 'os.os_name as pm_os')
                        ->leftJoin('agent_os_support as os', 'patch_masters.os', '=', 'os.id')
                        ->whereRaw('? LIKE CONCAT("%", os.os_name, "%")', [$ni->OSCaption])
                        ->get();

                    foreach ($patchListing as $patch) {
                        // if (!in_array($patch->patch_name, $deviceCaptions)) {
                        if (!in_array(strtolower($patch->patch_name), array_map('strtolower', $deviceCaptions))) {
                            $d = [
                                'pm_status' => 'Missing: ' . $patch->patch_name . ' ' . $patch->version,
                                'caption' => $patch->patch_name,
                                'version' => $patch->version,
                                // 'pm_severity' => $patch->patch_severity == 4 ? 'Low' : ($patch->patch_severity == 2 ? 'Important' : ($patch->patch_severity == 1 ? 'Critical' : ($patch->patch_severity == 3 ? 'Moderate' : 'Unrated'))),
                                'pm_severity' => $patch->severity == 4 ? 'Low' : ($patch->severity == 2 ? 'Important' : ($patch->severity == 1 ? 'Critical' : ($patch->severity == 3 ? 'Moderate' : 'Unrated'))),
                            ];
                            $return['data'][] = $d;
                        }
                    }

                    if (isset($filter->search) && $search_key = trim($filter->search)) {
                        $return['data'] = array_filter($return['data'], function ($item) use ($search_key) {
                            return stripos($item['caption'], $search_key) !== false || stripos($item['version'], $search_key) !== false;
                        });
                    }
                    if (isset($filter->other_filters)) {
                        $filters = $filter->other_filters;
                        $return['data'] = array_filter($return['data'], function ($item) use ($filters) {
                            $pm_status_match = true;
                            $pm_severity_match = true;
                            if (isset($filters->patch_status) && $filters->patch_status !== 'null' && $filters->patch_status !== null) {
                                $pm_status_match = stripos($item['pm_status'], $filters->patch_status) !== false;
                            }

                            if (isset($filters->severity) && $filters->severity !== 'null' && $filters->severity !== null) {
                                $pm_severity_match = stripos($item['pm_severity'], $filters->severity) !== false;
                            }
                            return $pm_status_match && $pm_severity_match;
                        });
                    }
                } else {
                    $return['data'] = [];
                    foreach ($data as $d) {
                        $letestLogs = null;
                        $logList = PatchManagerRequestDeviceLog::where('device_id', $filter->device_id)->where('log_patch_id', $d->patch_id)->where('log_system_update_type', 0)->select('status_id', 'message', 'created_at')->get();
                        if (!$logList->isEmpty()) {
                            $letestLogs = PatchManagerRequestDeviceLog::where('device_id', $filter->device_id)->where('log_patch_id', $d->patch_id)->where('log_system_update_type', 0)->orderByDesc('created_at')->select('status_id', 'message', 'created_at')->first();
                        }
                        $a = [
                            'pm_status' => $letestLogs != null ? ($letestLogs->status_id == 1 ? 'Initiated' : ($letestLogs->status_id == 2 ? 'Processing' : ($letestLogs->status_id == 3 ? 'Incomplete' : 'Updated'))) : $d->pm_status,
                            'caption' => $d->caption,
                            'version' => $d->version,
                            'pm_severity' => $d->pm_severity,
                        ];
                        $return['data'][] = $a;
                    }
                    if (isset($filter->search) && $search_key = trim($filter->search)) {
                        $return['data'] = array_filter($return['data'], function ($item) use ($search_key) {
                            return stripos($item['caption'], $search_key) !== false || stripos($item['version'], $search_key) !== false;
                        });
                    }
                    if (isset($filter->other_filters)) {
                        $filters = $filter->other_filters;
                        $return['data'] = array_filter($return['data'], function ($item) use ($filters) {
                            $pm_status_match = true;
                            $pm_severity_match = true;
                            if (isset($filters->patch_status) && $filters->patch_status !== 'null' && $filters->patch_status !== null) {
                                $pm_status_match = stripos($item['pm_status'], $filters->patch_status) !== false;
                            }

                            if (isset($filters->severity) && $filters->severity !== 'null' && $filters->severity !== null) {
                                $pm_severity_match = stripos($item['pm_severity'], $filters->severity) !== false;
                            }
                            return $pm_status_match && $pm_severity_match;
                        });
                    }
                }
            } else {
                $return['data'] = [];
                $fields = array(
                    '1' => 'caption',
                    '2' => 'version',
                );
                $patchSystemListing = SystemUpdatePatchMaster::where('device_id', $filter->device_id)->get();

                foreach ($patchSystemListing as $patch) {
                    $d = [
                        'pm_status' => ($patch->patch_status == 4) ? 'Updated' : (($patch->patch_status == 1) ? 'Initiated' : (($patch->patch_status == 2) ? 'Processing' : (($patch->patch_status == 3) ? 'Incomplete' : ('System Patch Available: ' . $patch->name . ' ' . $patch->patch_version)))),
                        'caption' => $patch->name,
                        'version' => $patch->patch_version,
                        'pm_severity' => $patch->patch_severity == 4 ? 'Low' : ($patch->patch_severity == 2 ? 'Important' : ($patch->patch_severity == 1 ? 'Critical' : ($patch->patch_severity == 3 ? 'Moderate' : 'Unrated'))),
                    ];
                    $return['data'][] = $d;
                }

                if (isset($filter->search) && $search_key = trim($filter->search)) {
                    $return['data'] = array_filter($return['data'], function ($item) use ($search_key) {
                        return stripos($item['caption'], $search_key) !== false || stripos($item['version'], $search_key) !== false;
                    });
                }
                if (isset($filter->other_filters)) {
                    $filters = $filter->other_filters;
                    $return['data'] = array_filter($return['data'], function ($item) use ($filters) {
                        $pm_status_match = true;
                        $pm_severity_match = true;
                        if (isset($filters->patch_status) && $filters->patch_status !== 'null' && $filters->patch_status !== null) {
                            $pm_status_match = stripos($item['pm_status'], $filters->patch_status) !== false;
                        }

                        if (isset($filters->severity) && $filters->severity !== 'null' && $filters->severity !== null) {
                            $pm_severity_match = stripos($item['pm_severity'], $filters->severity) !== false;
                        }
                        return $pm_status_match && $pm_severity_match;
                    });
                }
            }
        }
        return Excel::download(new PatchDeviceExportList($return['data']), 'Patch Device List.xlsx');
    }

    public function ajaxRequestableDevice(Request $request)
    {
        $req = $request->all();
        $return = array(
            'draw' => date('is')
        );

        $fields = array(
            '1' => 'a.asset_tag',
            '2' => 'mdl_name',
            '3' => 'manu_name',
            '4' => 'a.serial',
            '5' => 'loc_name',
            '6' => 'lbl_name'
        );

        $db = DB::table('assets as a');
        $db->join('models as mdl', 'mdl.id', '=', 'a.model_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'mdl.manufacturer_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.rtd_location_id');
        $db->leftJoin('users as u', 'u.id', '=', 'a.assigned_to');
        $db->whereNotExists(function ($query) {
            $query
                ->select(DB::raw(1))
                ->from('transfer_items as ti')
                ->whereRaw('ti.device_id = a.id')
                ->where('ti.transfer_status', '=', 1);
        });
        $db->join('status_labels as lbl', function ($q) {
            $q->on('lbl.id', '=', 'a.status_id');
            $q->where(function ($subQ) {
                $subQ
                    ->where('lbl.deployable', '=', 1)
                    ->orWhere('lbl.deployed', '=', 1);
            });
            $q->where('lbl.archived', '=', 0);
        });
        $db->leftJoin('asset_logs as al', function ($q) {
            $q->on('al.asset_id', '=', 'a.id');
            $q->where('al.action_type', 'like', 'requested');
            $q->where('al.asset_type', '=', 'hardware');
            $q->where('al.user_id', '=', Auth::user()->id);
            $q->whereNull('al.accepted_id');
        });
        $db->leftJoin('categories as cat', function ($q) {
            $q->on('cat.id', '=', 'mdl.category_id');
            $q->where('mdl.category_id', '<>', 0);
        });
        $db->select('a.id', 'al.id as requested_id', 'a.asset_tag', 'a.uuid', 'mdl.name as mdl_name', 'mnu.name as manu_name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'a.name', 'a.serial', 'a.assigned_to', 'lbl.id as status_id');
        $db->addSelect('a.image', 'mdl.image_thumbnail', 'mnu.attachment', 'cat.image_thumbnail as cat_img');
        $db->addSelect(DB::raw('DATE_FORMAT(a.last_checkout, "%d %b %Y %h:%i %p") as last_checkout_on'));
        $db->addSelect(DB::raw('case when a.assigned_to is not null and a.assigned_to > 0 then "Deployed" else lbl.name end as lbl_name'));

        $db->where('a.requestable', '=', 1);
        $db->where('a.company_id', '=', Auth::user()->company_id);
        // $db->whereRaw("(a.assigned_to is null or a.assigned_to = 0)");
        $db->whereNull('a.deleted_at');

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(a.asset_tag like "%%%1$s%%" or mdl.name like "%%%1$s%%" or mnu.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or (case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to is not null and a.assigned_to > 0 then "Deployed" else lbl.name end) like "%%%1$s%%" or a.serial like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['order'][0]['column']) && isset($fields[$req['order'][0]['column']]) && in_array($req['order'][0]['dir'], ['asc', 'desc'])) {
            $db->orderBy($fields[$req['order'][0]['column']], $req['order'][0]['dir']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
        }
        $db->skip($skip);
        $db->take($take);

        $return['data'] = $db->get();

        $return['profile_imgs'] = [];
        foreach ($return['data'] as $v) {
            $v->device_img = (
                ($v->image != '' && file_exists(public_path('uploads/devices/' . $v->image)))
                ? url('uploads/devices') . '/' . $v->image
                : (($v->image_thumbnail != '' && file_exists(public_path('uploads/models/' . $v->image_thumbnail)))
                    ? url('uploads/models') . '/' . $v->image_thumbnail
                    : (($v->attachment != '' && file_exists(public_path('uploads/manufacturers/' . $v->attachment)))
                        ? url('uploads/manufacturers') . '/' . $v->attachment
                        : (($v->cat_img != '' && file_exists(public_path('uploads/category/' . $v->cat_img))) ? url('uploads/category') . '/' . $v->cat_img : null)))
            );
            if (isset($return['profile_imgs'][$v->assigned_to]) || !$v->assigned_to) {
                continue;
            }
            $tmp_u = User::find($v->assigned_to);
            if ($tmp_u) {
                $return['profile_imgs'][$v->assigned_to] = $tmp_u->getProfileImg();
            }
        }

        return response()->json($return);
    }

    // function to make request for device from user screen
    public function requestDevice(Request $request, $id)
    {
        $return = [];
        $appSettings = Settings::first();
        $objDevice = Device::where('id', '=', $id)->where('requestable', '=', 1)->whereNull('deleted_at')->first();
        $user = Auth::user();

        if (!$objDevice) {
            return response()->json(['status' => 'error', 'msg' => 'Device not found !!']);
        }

        if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($objDevice)) {
            $return['status'] = 'error';
            $return['section'] = 'request-device';
            $return['msg'] = Auth::user()->company_id == null ? trans('content.device_fields.update_company_name') : trans('content.device_fields.multiple_company_access');
            return response()->json($return);
        }

        $logaction = new Actionlog();
        $logaction->asset_id = $objDevice->id;
        $logaction->asset_type = 'hardware';
        $logaction->created_at = $logaction->requested_at = date('Y-m-d h:i:s');
        $logaction->location_id = $user->location_id ? $user->location_id : null;
        $logaction->user_id = $user->id;
        $logaction->action_type = 'requested';
        $logaction->save();

        $alertnotify = null;
        if (Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        if (config('mail.service_enabled') && $user && !empty($user) && $user->email != null && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($user->email)->cc($alertnotify)->queue(new DeviceRequestNotification($user, $objDevice, $logaction->requested_at));
        }
        return response()->json(['status' => 'success', 'section' => 'approve-request', 'msg' => trans('content.device_fields.device+has_requested')]);
    }

    public function deviceExportPDF(Request $request)
    {
        $return = ['msg' => 'Unable to export report', 'status' => 'danger'];
        $vd = [];

        $set = $get_custom_fields = '';
        $setting = Settings::getSettings()->custom_fieldset_id ? Settings::getSettings()->custom_fieldset_id : '';
        if ($setting) {
            $set = 'where cfcf.custom_fieldset_id = ' . $setting;
            $get_custom_fields = DB::select("SELECT cfcf.custom_fieldset_id, CONCAT('a._itm_', replace(lcase(cf.NAME), ' ', '_')) AS cus_field FROM custom_fields AS cf
            JOIN custom_field_custom_fieldset AS cfcf ON cf.id = cfcf.custom_field_id $set");
        }
        $coll_field_sets = [];
        $field_sets = [];
        $coll_custom_fields = [];
        $custom_fields = [];
        $custom_field_names = [];
        $field_based_fieldset_id = [];
        if ($get_custom_fields) {
            foreach ($get_custom_fields as $cf) {
                $coll_field_sets[] = $cf->custom_fieldset_id;
                $coll_custom_fields[] = $cf->cus_field;
                if (!isset($field_based_fieldsets_ids[$cf->cus_field])) {
                    $field_based_fieldsets_ids[$cf->cus_field] = [$cf->custom_fieldset_id];
                } else {
                    $field_based_fieldsets_ids[$cf->cus_field][] = $cf->custom_fieldset_id;
                }
            }

            $field_sets = array_unique($coll_field_sets);
            $custom_fields = array_unique($coll_custom_fields);

            foreach ($custom_fields as $cf) {
                $n1 = str_replace('a._itm_', ' ', $cf);
                $n2 = str_replace('_', ' ', $n1);
                $custom_field_names[$cf] = ucwords($n2);
            }
        }

        $vd['fields'] = [];
        foreach ($custom_field_names as $fo) {
            $vd['fields'][] = $fo;
        }

        $db = DB::table('assets as a');
        $db->leftJoin('models as mdl', 'mdl.id', '=', 'a.model_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'mdl.manufacturer_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.rtd_location_id');
        // $db->leftJoin('users as u', 'u.id', '=', 'a.assigned_to');
        $db->leftJoin('users as u', function ($q) {
            $q->on('u.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '1');
        });

        $db->leftJoin('users as own', 'own.id', '=', 'a.asset_owner');
        $db->leftJoin('lease_agreements as lease', 'lease.id', '=', 'a.lease_id');
        $db->leftJoin('projects as pr', 'pr.id', '=', 'a.last_checkout_project');
        $db->leftJoin('status_labels as lbl', 'lbl.id', '=', 'a.status_id');
        $db->leftJoin('categories as cat', function ($q) {
            $q->on('cat.id', '=', 'mdl.category_id');
            $q->where('mdl.category_id', '<>', 0);
        });
        $db->leftJoin('procure_account_types as acc_typ', 'acc_typ.id', '=', 'cat.account_type_id');
        $db->leftJoin('places as pldev', function ($q) {
            $q->on('pldev.id', '=', 'a.stock_place');
        });
        $db->leftJoin('places as p', function ($q) {
            $q->on('p.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '2');
        });
        $db->leftJoin('locations as pldevloc', 'pldevloc.id', '=', 'pldev.location_id');
        $db->leftJoin('locations as ploc', 'ploc.id', '=', 'p.location_id');
        $db->leftJoin('suppliers as amc_supp', 'a.amc_supplier_id', '=', 'amc_supp.id');
        $db->leftJoin('assets_types as at', 'a.asset_type_id', '=', 'at.id');

        $db->select(
            'a.id',
            'a.asset_tag',
            'a.uuid',
            'mdl.name as mdl_name',
            'mdl.manufacturer_id',
            'mnu.name as manu_name',
            'cmp.name as cmp_name',
            'loc.name as loc_name',
            'a.purchase_cost',
            'cat.name as cat_name',
            'a.name',
            'a.serial',
            'a.notes',
            'a.order_number',
            'a.warranty_months',
            'mdl.fieldset_id',
            'amc_supp.name as amc_supp_name',
            'a.ip',
            'a.mac'
        );
        $db->addSelect(DB::raw('trim(concat_ws(" ", mdl.name, mdl.modelno)) as model_full_name'));
        $db->addSelect(DB::raw('case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end as last_checkout_project'));
        $db->addSelect(DB::raw('concat(u.first_name, " ", u.last_name) as full_name'));
        $db->addSelect(DB::raw('concat(own.first_name, " ", own.last_name) as asset_owner'));
        $db->addSelect(DB::raw('case when u.job_type in (1,2) then u.ex_user_company else null end as ex_user_company'));
        $db->addSelect(DB::raw("case when lbl.deployable <> 0 and a.assigned_to <> '' and a.assigned_to > 0 then 2 when lbl.deployable <> 0 then 1 else 0 end as check_action"));
        $db->addSelect(DB::raw('case when a.device_occure_type = 1 then DATE_FORMAT(lease.end_date, "%d %b %Y ") else "" end as lease_end_date'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.last_checkout, "%d %b %Y %h:%i %p") as last_checkout_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.expected_checkin, "%d %b %Y %h:%i %p") as expected_checkin_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y %h:%i %p") as purchase_date_on'));
        $db->addSelect(DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end as lbl_name'));
        $db->addSelect(DB::raw('case when acc_typ.name is not null then acc_typ.name else "" end as acc_type_name'));
        $db->addSelect('p.place', 'ploc.name as place_loc', 'pldev.place as stock_place', 'pldevloc.name as pldevloc_name', 'a.assigned_for', 'a.assigned_to');
        $db->addSelect(DB::raw('case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" else "Purchase Device" end as device_occure_type_name'));
        $db->addSelect(DB::raw("@wed:=case when (dayname(a.purchase_date) is not null and a.warranty_months is not null) then DATE_ADD(a.purchase_date, INTERVAL a.warranty_months month) else null end as wed, date_format(@wed, '%d/%m/%Y') as wed_format, IF(@wed < CURRENT_TIMESTAMP, 'Expired', '') as 'is_warranty_expired'"));
        $db->addSelect(DB::raw("case when (dayname(a.amc_expire_date) is not null) then date_format(a.amc_expire_date, '%d/%m/%Y') else null end as amc_ed_format, IF((dayname(a.amc_expire_date) is not null) and a.amc_expire_date < CURRENT_TIMESTAMP, 'Expired', '') as 'is_amc_expired'"));

        if (is_array($coll_custom_fields) and count($coll_custom_fields)) {
            foreach ($coll_custom_fields as $coll_custom_field) {
                $clm_name = substr($coll_custom_field, 2);
                if (Schema::hasColumn('assets', $clm_name)) {
                    $db->addSelect($coll_custom_field);
                }
            }
        }

        if (isset($req['showDeletedDevices']) && $req['showDeletedDevices'] == 'true') {
            $db->whereNotNull('a.deleted_at');
        } else {
            $db->whereNull('a.deleted_at');
        }

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if (isset($filters->search)) {
                $req['filters'] = (array) $filters->other_filters;
                $req['search'] = $filters->search;
            }

            if (isset($req['filters'])) {
                $filters = $req['filters'];

                if (isset($filters['manufacturer']) && $filters['manufacturer'] && $filters['manufacturer'] != 'null') {
                    $db->where('mdl.manufacturer_id', '=', (int) $filters['manufacturer']);
                }
                if (isset($filters['model']) && $filters['model'] && $filters['model'] != 'null') {
                    $db->where('a.model_id', '=', (int) $filters['model']);
                }
                if (isset($filters['category']) && $filters['category'] && $filters['category'] != 'null') {
                    $db->where('mdl.category_id', '=', (int) $filters['category']);
                }
                if (isset($filters['location']) && $filters['location'] && $filters['location'] != 'null') {
                    $db->where('a.rtd_location_id', '=', (int) $filters['location']);
                }
                if (isset($filters['assigned_user']) && $filters['assigned_user'] && $filters['assigned_user'] != 'null') {
                    $db->where('a.assigned_to', '=', (int) $filters['assigned_user']);
                    $db->where('a.assigned_for', '=', 1);
                }
                if (isset($filters['assigned_place']) && $filters['assigned_place'] && $filters['assigned_place'] != 'null') {
                    $db->where('a.assigned_to', '=', (int) $filters['assigned_place']);
                    $db->where('a.assigned_for', '=', 2);
                }
                if (isset($filters['stock_place']) && $filters['stock_place'] && $filters['stock_place'] != 'null') {
                    $db->where('a.stock_place', '=', (int) $filters['stock_place']);
                    $db->where('a.assigned_for', '=', null);
                }
                if (isset($filters['asset_type_id']) && $filters['asset_type_id'] && $filters['asset_type_id'] != 'null') {
                    $db->where('a.asset_type_id', '=', (int) $filters['asset_type_id']);
                }
                if (isset($filters['device_occure_type']) && $filters['device_occure_type'] && $filters['device_occure_type'] != 'null') {
                    $db->where('a.device_occure_type', '=', (int) $filters['device_occure_type']);
                }
                if (isset($filters['last_checkout_project']) && $filters['last_checkout_project'] && $filters['last_checkout_project'] != 'null') {
                    $db->where('a.last_checkout_project', '=', (int) $filters['last_checkout_project']);
                }
                if (isset($filters['department']) && $filters['department'] && $filters['department'] != 'null') {
                    $db->where('u.department_id', '=', (int) $filters['department']);
                }
                if (isset($filters['asset_owner']) && $filters['asset_owner'] && $filters['asset_owner'] != 'null') {
                    $db->where('a.asset_owner', '=', (int) $filters['asset_owner']);
                }
                if (isset($filters['device_assigned_to']) && $filters['device_assigned_to'] && $filters['device_assigned_to'] != 'null') {
                    $db->where('a.assigned_for', '=', (int) $filters['device_assigned_to']);
                }

                $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.last_checkout', '3' => 'a.expected_checkin', '4' => 'a.amc_expire_date'];
                if (isset($filters['based_on']) && $filters['based_on'] && $filters['based_on'] != 'null' && $filters['based_on'] >= 1 && $filters['based_on'] <= 4) {
                    if (isset($filters['from_date']) && $filters['from_date'] && $filters['from_date'] != 'null') {
                        $from_date = CommonHelper::getDateAs($filters['from_date'], 'Y-m-d', 'd/m/Y');
                        $to_date = CommonHelper::getDateAs($filters['to_date'], 'Y-m-d', 'd/m/Y');
                        if ($from_date && $to_date) {
                            if ($filters['based_on'] == 3) {
                                $whereStr = sprintf('(date(%1$s) >= "%2$s" and date(%1$s) <= "%3$s" and a.status_id = 6)', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            } else {
                                $whereStr = sprintf('(date(%1$s) >= "%2$s" and date(%1$s) <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            }
                            $db->whereRaw($whereStr);
                        }
                    }
                }
            }
        }
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']) && $search_key = trim($req['search'])) {
            $whereStr = sprintf('(a.asset_tag like "%%%1$s%%" or a.product_number like "%%%1$s%%" or mdl.name like "%%%1$s%%" or mnu.name like "%%%1$s%%" or pr.name like "%%%1$s%%" or cmp.name like "%%%1$s%%"or a.last_checkout_project like "%%%1$s%%" or loc.name like "%%%1$s%%" or (case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end) like "%%%1$s%%" or concat(own.first_name, " ", own.last_name) like "%%%1$s%%" or a.serial like "%%%1$s%%" or (case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end) like "%%%1$s%%" or DATE_FORMAT(a.last_checkout, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['order'][0]['column']) && isset($fields[$req['order'][0]['column']]) && in_array($req['order'][0]['dir'], ['asc', 'desc'])) {
            $db->orderBy($fields[$req['order'][0]['column']], $req['order'][0]['dir']);
        }

        $data = $db->get();

        $report_file_name = 'Devices-' . date('dMY') . '.pdf';
        $properties = [];
        $properties['format'] = 'Legal-L';
        $vd['data'] = $data;

        // $stock_place = (!$data->assigned_to && $data->stock_place) ? implode(', ', [$data->stock_place, $data->pldevloc_name]) : '';
        // $chkout_place = $data->assigned_for == 2 ? implode(', ', [$data->place, $data->place_loc]) : '';
        // $chkout_date = $data->assigned_to ? $data->last_checkout_on : '';

        $pdf = PDF::loadView('devices.for_export', $vd, [], $properties);
        return $pdf->download($report_file_name);
    }

    public function getDevice(Request $request, $id, $forAction)
    {
        $return = array('status' => 'failure', 'msg' => 'Unable to open device for edit.');
        $device = Device::where('id', '=', $id)->first();
        $unit = BillingUnit::where('asset_id', $id)->first();

        $under_transfer = DB::table('transfer_items')->where('device_id', $device->id)->where('transfer_status', 1)->first();
        if (!empty($under_transfer)) {
            $return['status'] = 'failure';
            $return['msg'] = trans('content.device_fields.device_is_under_transfer');
            return response()->json($return);
        }

        if (!$device || !$device->exists) {
            return response()->json($return);
        }

        $device->purchase_date = CommonHelper::getDateAs($device->purchase_date, 'd-m-Y', 'Y-m-d');
        $device->amc_expire_date = CommonHelper::getDateAs($device->amc_expire_date, 'd-m-Y', 'Y-m-d');
        $device->warrenty_end_date = CommonHelper::getDateAs($device->warrenty_end_date, 'd-m-Y', 'Y-m-d');
        $device->warranty_start_date = CommonHelper::getDateAs($device->warranty_start_date, 'd-m-Y', 'Y-m-d');
        $device->purchase_cost = number_format($device->purchase_cost, 2, '.', '');

        $dev = array();
        $dev['data'] = $device->toArray();
        if ($unit != null) {
            $dev['data1'] = $unit->toArray();
        }
        $dev['dropdown'] = array();
        $dev['custom_fields']['all_fields'] = array();
        $dev['custom_fields']['required_fields'] = array();
        $dev['custom_fields']['html'] = '';
        if ($device->model_id) {
            $getModel = Model::where('id', $device->model_id)->select('id', 'name as text')->first();
            $dev['dropdown']['model'] = $getModel && $getModel->exists ? $getModel->toArray() : null;
            if ($device->model->customFieldset && isset($device->model->customFieldset->fields) && count($device->model->customFieldset->fields) && $device->model->category->customFieldset && isset($device->model->category->customFieldset->fields) && count($device->model->category->customFieldset->fields)) {
                $modelFields = $device->model->customFieldset->fields ?? collect();
                $categoryFields = $device->model->category->customFieldset->fields ?? collect();
                $allFields = $modelFields->merge($categoryFields);
                $dev['custom_fields'] = CommonHelper::formCustomFields(
                    $allFields,
                    $dev['data'],
                );
            } else if ($device->model->customFieldset && isset($device->model->customFieldset->fields) && count($device->model->customFieldset->fields)) {
                $dev['custom_fields'] = CommonHelper::formCustomFields($device->model->customFieldset->fields, $dev['data']);
            } else if ($device->model->category->customFieldset && isset($device->model->category->customFieldset->fields) && count($device->model->category->customFieldset->fields)) {
                $dev['custom_fields'] = CommonHelper::formCustomFields($device->model->category->customFieldset->fields, $dev['data']);
            }
        } else {
            if (Settings::first()->custom_fieldset_id != null) {
                $fieldsetObj = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
                if (!empty($fieldsetObj)) {
                    $devices1 = CommonHelper::formCustomFields($fieldsetObj->fields, $dev['data']);
                    if (isset($devices1['all_fields'][0])) {
                        array_push($dev['custom_fields']['all_fields'], $devices1['all_fields'][0]);
                    }
                    if (isset($devices1['required_fields'][0])) {
                        array_push($dev['custom_fields']['required_fields'], $devices1['required_fields'][0]);
                    }
                    $dev['custom_fields']['html'] .= $devices1['html'];
                }
            }
        }
        if ($device->invoice_id) {
            $getDevice = Purchase::where('id', $device->invoice_id)->select('id', DB::raw('concat_ws(" - ", invoice_no, date_format(invoice_date, "%d/%m/%Y")) as text'))->first();
            $dev['dropdown']['invoice'] = $getDevice && $getDevice->exists ? $getDevice->toArray() : null;
        }

        if ($device->status_id == 6) {
            $getAllocation_type = Actionlog::where('asset_id', $id)
                ->where('action_type', 'Checkout')
                ->latest()
                ->value('allocation_type_id');

            $dev['data']['allocation_type'] = $getAllocation_type ?? null;
        }

        if ($device->supplier_id) {
            $getSupplier = Supplier::where('id', $device->supplier_id)->select('id', 'name as text')->first();
            $dev['dropdown']['supplier'] = $getSupplier && $getSupplier->exists ? $getSupplier->toArray() : null;
        }
        if ($device->asset_owner) {
            $getUser = User::where('id', $device->asset_owner)->select('id', DB::raw('concat(first_name, " ", last_name, "") as text'))->first();
            $dev['dropdown']['asset_owner'] = $getUser && $getUser->exists ? $getUser->toArray() : null;
        }
        if ($device->amc_supplier_id) {
            $getSupplier = Supplier::where('id', $device->amc_supplier_id)->select('id', 'name as text')->first();
            $dev['dropdown']['amc_supplier'] = $getSupplier && $getSupplier->exists ? $getSupplier->toArray() : null;
        }
        if ($device->rtd_location_id) {
            $getLocation = Location::where('id', $device->rtd_location_id)->select('id', 'name as text')->first();
            if (!empty($getLocation))
                $dev['dropdown']['location'] = $getLocation && $getLocation->exists ? $getLocation->toArray() : null;
        }
        if ($device->internal_place_id) {
            $getPlace = Place::leftJoin('locations', 'places.location_id', '=', 'locations.id')->where('places.id', $device->internal_place_id)->whereNull('places.deleted_at')->select('places.id', DB::raw('CONCAT(locations.name, " - ", places.place) as text'))->first();
            if (!empty($getPlace))
                $dev['dropdown']['internal_place'] = $getPlace && $getPlace->exists ? $getPlace->toArray() : null;
        }
        if ($device->stock_place) {
            $getStockPlace = Place::leftJoin('locations', 'places.location_id', '=', 'locations.id')->where('places.id', $device->stock_place)->whereNull('places.deleted_at')->select('places.id', DB::raw('CONCAT(locations.name, " - ", places.place) as text'))->first();
            if (!empty($getStockPlace))
                $dev['dropdown']['stock_place'] = $getStockPlace && $getStockPlace->exists ? $getStockPlace->toArray() : null;
        }
        if ($device->department_id) {
            $getDepartment = Department::where('id', $device->department_id)->where('asset_department', 1)->select('id', 'name as text')->first();
            if (!empty($getDepartment))
                $dev['dropdown']['department'] = $getDepartment && $getDepartment->exists ? $getDepartment->toArray() : null;
        }

        if (!empty($device->deviceRfids)) {
            foreach ($device->deviceRfids as $a) {
                $dev['dropdown']['device_rfid'][] = $a->device_rfid;
            }
        }
        if (!empty($device->in_antenna)) {
            $antenna = explode(',', $device->in_antenna);
            foreach ($antenna as $a) {
                $dev['dropdown']['in_antenna'][] = $a;
            }
        }
        if (!empty($device->out_antenna)) {
            $antenna = explode(',', $device->out_antenna);
            foreach ($antenna as $a) {
                $dev['dropdown']['out_antenna'][] = $a;
            }
        }
        if ($device->lease_id) {
            $lease = DB::table('lease_agreements as l');
            $lease->join('suppliers as s', 'l.leaser', '=', 's.id');
            $lease->select('l.id', DB::raw('concat_ws(" - ", concat("#", l.id), concat(s.name, " (", date_format(l.start_date, "%b %Y"), " - ", date_format(l.end_date, "%b %Y"), ")")) as text'));
            $lease->where('l.id', '=', $device->lease_id);
            $getLease = $lease->first();
            $dev['dropdown']['lease_id'] = $getLease ? $getLease : null;
        }

        if(isset($device->last_checkout_project)) {
            $project = Project::select('id', 'name as text')->find($device->last_checkout_project);
            $dev['dropdown']['project'] = $project;
        }

        if(isset($dev['data']['allocation_type'])) {
            $allocationType = AssetAllocationType::select('id', 'name as text')->find($dev['data']['allocation_type']);
            $dev['dropdown']['allocation'] = $allocationType;
        }

        if ($forAction == 'clone') {
            $dev['incrementedid'] = Device::getNextId();
            $dev['data']['serial'] = '';
        }

        $dev['data']['serial_no_match'] = 0;
        if ($device->serial) {
            $ni_serial = Basic::where('BIOSSerialNumber', $device->serial)->first();
            if ($ni_serial != null) {
                $dev['data']['serial_no_match'] = 1;
            }
        }
        if($device->expected_checkin) {
            $dev['data']['expected_checkin'] = Carbon::parse($device->expected_checkin)->format('d/m/Y');
        }
        $return['status'] = 'success';
        $return['msg'] = '';
        $return['device'] = $dev;
        return $return;
    }

    public function addDevice(Request $request, $type = null)
    {
        $appSettings = Settings::first();
        $return = array(
            'status' => 'danger',
            'msg' => 'Unable to add given Device'
        );
        if (!Auth::user()->hasPermissionTo('DeviceAdd') || !config('services.assets.enabled')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $devicecount = Device::whereNotIn('status_id', [7])->count();
        if ($devicecount > config('services.assets.asset_limit')) {
            $return['msg'] = "You don't have permission to insert more than " . config('services.assets.asset_limit') . ' devices. Please contact Admin';
            return response()->json($return);
        }
        try {
            $data = $request->only('asset_tag', 'uuid', 'name', 'company_id', 'model_id', 'asset_owner', 'location_id', 'status_id', 'supplier_id', 'serial', 'product_number', 'order_number', 'purchase_date', 'purchase_cost', 'warranty_months', 'notes', 'rtd_location_id', 'internal_place_id', 'requestable', 'high_pririty', 'image', 'forAction', 'clone_img', 'delete_img', 'purchase_currency', 'amc_supplier_id', 'amc_expire_date', 'warranty_start_date', 'warrenty_end_date', 'invoice_id', 'lease_id', 'device_occure_type', 'stock_place', 'ip', 'mac', 'asset_type_id', 'scanner_id', 'in_antenna', 'out_antenna', 'block_device_movement', 'department_id');

            $rules = [
                'asset_tag' => 'nullable|string|max:100|unique:assets,asset_tag|clean_text_only',
                'model_id' => 'required|integer|min:1|exists:models,id',
                'asset_owner' => 'sometimes|nullable|integer|min:1|exists:users,id',
                'status_id' => 'required|integer|min:1|exists:status_labels,id',
                'company_id' => 'required|integer|min:1|exists:companies,id',
                'rtd_location_id' => 'required|integer|min:1|exists:locations,id',  // default location
                'internal_place_id' => 'nullable|integer|min:1|exists:places,id',
                'invoice_id' => 'nullable|integer|min:1|exists:purchases,id',
                'supplier_id' => 'nullable|integer|min:1|exists:suppliers,id',
                'amc_supplier_id' => 'nullable|integer|min:1|exists:suppliers,id',
                'amc_expire_date' => 'nullable|date_format:d/m/Y',
                'warranty_start_date' => 'nullable|date_format:d/m/Y',
                'warrenty_end_date' => 'nullable|date_format:d/m/Y',
                'name' => 'nullable|alpha_space|max:100|clean_text_only',
                'uuid' => 'nullable|string|max:100|clean_text_only',
                'product_number' => 'nullable|string|max:255|not_regex:/[\$]/|clean_text_only_with_hash',
                'serial' => 'required|string|max:255|not_regex:/[#\$]/|unique:assets,serial|clean_text_only',
                'purchase_date' => 'nullable|date_format:d/m/Y',
                'order_number' => 'nullable|string|max:100|clean_text_only',
                'purchase_cost' => 'nullable|numeric|clean_text_only',
                'purchase_currency' => 'nullable|string|max:3',
                'warranty_months' => 'nullable|integer|min:0',
                'notes' => 'nullable|string|max:2000|clean_text_only',
                'sez_device' => 'sometimes|nullable|integer|min:0|max:1',
                'requestable' => 'sometimes|nullable|integer|min:0|max:1',
                'high_pririty' => 'sometimes|nullable|integer|min:0|max:1',
                'image' => 'sometimes|mimes:jpeg,bmp,png',
                'forAction' => 'nullable|string|max:20|clean_text_only',
                'clone_img' => 'nullable|string|max:150|clean_text_only',
                'delete_img' => 'sometimes|nullable|string|min:0|max:1',
                'device_occure_type' => 'nullable|integer|min:0|max:3',
                'lease_id' => 'nullable|integer|min:1|exists:lease_agreements,id',
                'stock_place' => 'nullable|integer|min:1|exists:places,id',
                'ip' => 'nullable|string|max:50|clean_text_only',
                'mac' => 'nullable|string|max:60|clean_text_only',
                'asset_type_id' => 'nullable|integer|min:1|exists:assets_types,id',
                'device_rfid' => 'nullable',
                'department_id' => 'nullable|integer|min:1|exists:departments,id',
            ];

            $messages = [
                'model_id.required' => 'Please Select Model',
                'company_id.exists' => 'Please select company'
            ];

            $data_fields = $request->input('fields', null);
            $custom_fields = $custom_fields_sez = [];
            $model = Model::find($data['model_id']);
            if ($model && $model->customFieldset && count($model->customFieldset->fields)) {
                foreach ($model->customFieldset->fields as $f) {
                    $col_name = $f->nameToColumn();
                    $formatType = CommonHelper::convertFormatToRegex($f->format);
                    $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                    // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                    $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                    $custom_fields[] = $col_name;
                }
            }
            if ($model && $model->category->customFieldset && count($model->category->customFieldset->fields)) {
                foreach ($model->category->customFieldset->fields as $f) {
                    $col_name = $f->nameToColumn();
                    $formatType = CommonHelper::convertFormatToRegex($f->format);
                    $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                    // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                    $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                    $custom_fields[] = $col_name;
                }
            }
            if (Settings::first()->custom_fieldset_id != '') {
                $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
                if (!empty($customFieldset)) {
                    foreach ($customFieldset->fields as $f) {
                        $col_name = $f->nameToColumn();
                        $formatType = CommonHelper::convertFormatToRegex($f->format);
                        $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                        // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                        $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                        $custom_fields[] = $col_name;
                    }
                }
            }

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return['msg'] = $e[0];
                return response()->json($return);
            }

            unset($data['forAction']);
            unset($data['clone_img']);
            unset($data['delete_img']);

            $dummy_tag = '';
            if ($data['asset_tag'] == '' || !$data['asset_tag']) {
                $dummy_tag = sha1(time());
                $data['asset_tag'] = substr($dummy_tag, 0, 98);
            }
            if (isset($request->ip)) {
                $ipObj = BlockedIP::select('ip')->where('ip', $request->ip)->first();
            }
            if (isset($request->mac)) {
                $macObj = BlockedIP::select('mac')->where('mac', $request->mac)->first();
            }
            if (!empty($ipObj) || !empty($macObj)) {
                $return['msg'] = trans('content.device_fields.this_ip_mac_is_already_block');
                return response()->json($return);
            }
            if (isset($request->device_rfid) && !empty($request->device_rfid)) {
                foreach ($request->device_rfid as $a) {
                    $obj = DeviceRfid::where('device_rfid', $a)->count();
                    if ($obj > 0) {
                        $return['msg'] = $a . ' : RFID Tag is already in use.';
                        return response()->json($return);
                    }
                }
            }
            $device = new Device;
            $device->fill($data);
            $device->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, 'Y-m-d', 'd/m/Y') : null;
            $device->purchase_cost = (float) $request->purchase_cost;
            $device->warranty_months = $request->warranty_months > 0 ? $request->warranty_months : null;
            $device->amc_expire_date = $request->amc_expire_date ? CommonHelper::getDateAs($request->amc_expire_date, 'Y-m-d', 'd/m/Y') : null;
            $device->warranty_start_date = $request->warranty_start_date ? CommonHelper::getDateAs($request->warranty_start_date, 'Y-m-d', 'd/m/Y') : null;
            $device->warrenty_end_date = $request->warrenty_end_date ? CommonHelper::getDateAs($request->warrenty_end_date, 'Y-m-d', 'd/m/Y') : null;
            $device->requestable = $request->input('requestable', 0);
            $device->sez_device = $request->input('sez_device', 0);
            $device->high_pririty = $request->input('high_pririty', 0);
            $device->user_id = Auth::user()->id;
            $device->archived = '0';
            $device->physical = '1';
            $device->depreciate = '0';
            $device->assigned_to = null;
            $device->department_id = $request->input('department_id', null) ? $request->input('department_id') : null;
            $device->lease_id = $request->input('device_occure_type', null) ? $request->input('lease_id') : null;
            if (isset($request->device_rfid)) {
                $device->device_rfid = (isset($request->device_rfid) && !empty($request->device_rfid)) ? implode(',', $request->device_rfid) : null;
            }
            if (isset($request->scanner_id)) {
                $device->scanner_id = $request->scanner_id;
            }
            if (isset($request->in_antenna)) {
                $device->in_antenna = isset($request->in_antenna) && !empty($request->in_antenna) ? implode(',', $request->in_antenna) : null;
            }
            if (isset($request->out_antenna)) {
                $device->out_antenna = isset($request->out_antenna) && !empty($request->out_antenna) ? implode(',', $request->out_antenna) : null;
            }
            if (isset($request->block_device_movement)) {
                $device->block_device_movement = isset($request->block_device_movement) ? $request->block_device_movement : null;
            }

            if (count($custom_fields)) {
                foreach ($custom_fields as $f) {
                    $device->{$f} = isset($data[$f]) ? $data[$f] : null;
                }
            }

            // company id validation needs
            if (Auth::user()->isSuperUser()) {
                $device->company_id = empty($data['company_id']) ? Auth::user()->company_id : $data['company_id'];
            } else {
                $device->company_id = $appSettings->full_multiple_companies_support == 0 ? (empty($data['company_id']) ? Auth::user()->company_id : $data['company_id']) : Auth::user()->company_id;
            }

            // upload device photo
            $device->image = null;
            if ($request->image) {
                $uploaded_img = $request->image;
                $deviceImage = Str::random(12) . Str::random(12) . '.' . $uploaded_img->getClientOriginalExtension();
                $checkFolderPath = CommonHelper::attachmentFolderStructure('uploads', 'devices');
                if (!$checkFolderPath) {
                    return $this->fail(500, ' Directory not found', '');
                }
                $path = public_path('uploads/devices/' . $deviceImage);
                $manager = new ImageManager(new Driver());
                $image   = $manager->read($uploaded_img->getRealPath());
                $image->scale(width: 300);
                $image->save($path);
                $device->image = $deviceImage;
            } elseif ($request->input('forAction') == 'clone' && $request->input('clone_img') != '' && !$request->input('delete_img', 0)) {
                $old_path = public_path('/uploads/devices/' . $request->input('clone_img'));
                if (file_exists($old_path)) {
                    $cloneImgArr = explode('.', $request->input('clone_img'));
                    $deviceImage = Str::random(12) . Str::random(12) . '.' . last($cloneImgArr);
                    $checkFolderPath = CommonHelper::attachmentFolderStructure('uploads', 'devices');
                    if (!$checkFolderPath) {
                        return $this->fail(500, ' Directory not found', '');
                    }
                    $path = public_path('uploads/devices/' . $deviceImage);
                    $manager = new ImageManager(new Driver());
                    $image   = $manager->read($old_path);
                    $image->scale(width: 300);
                    $image->save($path);
                    $device->image = $deviceImage;
                }
            }

            $device->warranty_status = $device->updateWarrantyStatus();
            $device->calc_warranty_expire_date = $device->updateWarrantyExpireDate();
            if ($device->save()) {
                if (in_array(config('app.client'), ['rolepermission', 'knightfrank', 'rashmi'])) {
                    $newStatus = $device->status_id;
                    if (!empty($newStatus)) {
                        CommonHelper::updateStatusCounts($oldStatus = null, $newStatus, $device);
                    }
                }
                if (isset($request->device_rfid)) {
                    foreach ($request->device_rfid as $id) {
                        if (!DeviceRfid::where('device_rfid', $id)->exists()) {
                            $deviceRfidTag = new DeviceRfid();
                            $rfid_array = [
                                'device_id' => $device->id,
                                'device_rfid' => $id,
                            ];
                            $deviceRfidTag->fill($rfid_array);
                            if (!$deviceRfidTag->save()) {
                                $return['msg'] = 'Unable to add device RFID';
                                return response()->json($return);
                            }
                        }
                    }
                }
                $return['device_id'] = $device->id;
                $user = Auth::user();
                if (Settings::first()->alerts_enabled == 1) {
                    try {
                        $alertnotify = CommonHelper::getGlobalAlertEmail();
                        if (config('mail.service_enabled') && !empty($alertnotify)) {
                            foreach ($alertnotify as $email) {
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($email)->queue(new DeviceAddNotification($device, $user));
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }

                if (Settings::first()->cost_earned == 1) {
                    $billingunit = BillingUnit::create([
                        'rate_hr' => $request->rate_hr,
                        'rate_day' => $request->rate_day,
                        'rate_week' => $request->rate_week,
                        'rate_month' => $request->rate_month,
                        'rate_quarterly' => $request->rate_quarterly,
                        'rate_half_yearly' => $request->rate_half_yearly,
                        'rate_yearly' => $request->rate_yearly,
                        'asset_id' => $device->id,
                    ]);
                    $billingunit->save();
                }

                if ($dummy_tag) {
                    $device->asset_tag = $device->getNextId();
                    $device->save();
                }
                if (isset($request->sez_device) && $request->sez_device != null) {
                    $deviceSez = new DeviceSez();
                    $deviceSez->device_id = $device->id;
                    $deviceSez->save();
                }
                // to update data as per AzureDeviceMapping
                if (isset($device->serial) && isset($request->added_from) && $request->added_from == 3) {
                    $updateAzureDeviceId = NetworkDevice::where('BIOSSerialNumber', $device->serial)->first();
                    if (!empty($updateAzureDeviceId)) {
                        $updateAzureDeviceId->device_id = $device->id;
                        if (!$updateAzureDeviceId->save()) {
                            Log::error('AzureDeviceMapping error: Unable to update device id: ' . $device->serial);
                        }
                        $device->added_from = 3;
                        $device->save();
                    } else {
                        Log::error("AzureDeviceMapping error: NetworkDevice not found for serial {$device->serial}");
                    }
                }

                // to update data as per NSDeviceMapping
                if (isset($device->mac) && isset($request->added_from) && $request->added_from == 4) {
                    $updateNs = ScanRegister::where('mac', $device->mac)->first();
                    if (!empty($updateNs)) {
                        $device->added_from = 4;
                        $device->save();
                    } else {
                        Log::error("NsDeviceMapping error: NetworkDevice not found for mac {$device->mac}");
                        $return['msg'] = "NetworkDevice not found for mac {$device->mac}";
                        return response()->json($return);
                    }
                }

                $return['status'] = 'success';
                $return['device_status'] = $device->status_id;
                if ($type == 'clone') {
                    $return['msg'] = trans('content.device_fields.new_device_clone', ['asset_tag' => $device->asset_tag]);
                } else {
                    $return['msg'] = trans('content.device_fields.new_device_create', ['asset_tag' => $device->asset_tag]);
                }
                $getGroup = PatchManagementGroup::where('auto_added_new_device', 1)->select('id', 'auto_added_new_device')->get();
                foreach ($getGroup as $key => $value) {
                    // Add Devices to the Group
                    PatchManagementGroupDevice::insert([
                        'group_id' => $value->id,
                        'device_id' => $device->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                Actionlog::deviceAdded($device, Auth::user()->id);
                if (config('app.socket_enabled')) {
                    CommonHelper::sendDeviceCountToSocket();
                }
                // checkout process
                if ($device->status_id == Label::getDeployedLabel()->id) {
                    $checkout_data = [];
                    $checkout_data['id'] = $device->id;
                    $checkout_data['name'] = $request->name;
                    $checkout_data['assigned_for'] = $request->assigned_for;

                    if ($request->assigned_for == '2') {
                        $assignedTarget = Place::where('id', '=', $request->assigned_place)->first();
                        $checkout_data['assigned_place'] = $request->assigned_place;
                    } else {
                        if (!Auth::user()->checkoutBasicClearance()) {
                            $assignedTarget = User::where('id', '=', $request->assigned_to)->first();
                            $checkout_data['assigned_to'] = $request->assigned_to;
                        }
                    }

                    if (empty($assignedTarget) || !$assignedTarget->exists) {
                        $systemChosenStatus = Label::getFirstDeployable(1);
                        $device->status_id = $systemChosenStatus->id;
                        $device->save();
                        $return['msg'] = $request->assigned_for == '2' ? 'New Device (' . $device->asset_tag . ') Created Successfully. But choosen place is not available to checkout.' : 'New Device (' . $device->asset_tag . ') Created Successfully. But choosen user is not available to checkout.';
                        return response()->json($return);
                    }
                    if ($request->assigned_for != '2') {
                        if ($assignedTarget->checkoutBasicClearance()) {
                            $systemChosenStatus = Label::getFirstDeployable(1);
                            $device->status_id = $systemChosenStatus->id;
                            $device->save();
                            $return['msg'] = 'New Device (' . $device->asset_tag . ') Created Successfully. But Chosen User is not in Active Status. So unable to checkout';
                            return response()->json($return);
                        }

                        if ($assignedTarget->checkLastWorkingDate()) {
                            $systemChosenStatus = Label::getFirstDeployable(1);
                            $device->status_id = $systemChosenStatus->id;
                            $device->save();
                            $return['msg'] = 'New Device (' . $device->asset_tag . ") Created Successfully. This user's last working date has already completed. Hence unable to check out. !!!";
                            return response()->json($return);
                        }
                    }

                    $checkout_data['checkout_at'] = date('Y-m-d H:i:s');
                    $checkout_data['expected_checkin'] = null;
                    $checkout_data['note'] = $request->input('notes');
                    $checkout_data['allocation_type_id'] = isset($request->allocation_type) ? $request->allocation_type : null;
                    $checkout_data['last_checkout_project'] = isset($request->project_name) ? $request->project_name : null;
                    // trigger checkout
                    if ($device->checkout($checkout_data, $assignedTarget)) {
                        $return['status'] = 'success';
                        $return['msg'] = 'New Device (' . $device->asset_tag . ') added and checked out successfully';
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('addDevice error: uid:' . Auth::user()->id . ' - ' . $e->getMessage());
        }
        Log::info('addDevice success: uid:' . Auth::user()->id . ' - ' . json_encode($request->all()));
        return response()->json($return);
    }

    /* get options of bulk checkout options */
    public function getBulkChkoutOptions(Request $request)
    {
        $return = new stdClass;
        $return->status = 'success';
        $return->departments = Department::getPlainOptions();
        $return->locations = Location::getPlainOptions();
        return response()->json($return);
    }

    public function sendBulkChkoutAcceptReminder(Request $request)
    {
        try {
            $return = ['status' => 'fail', 'msg' => 'Unable to send the reminder', 'send_reminder' => 0];
            if (!Auth::user()->hasPermissionTo('DeviceBulkSendReminder') || !config('services.assets.enabled')) {
                $return['msg'] = trans('content.user_fields.Permission_denied');
                return response()->json($return);
            }
            if (!config('mail.service_enabled')) {
                throw new \Exception(trans('content.device_fields.mail_Service_not_enanbled'));
            }

            $data = $request->only('send_to');
            $validate = Validator::make($data, [
                'send_to' => 'required|integer|min:1|max:3'
            ]);

            if ($validate->fails()) {
                $v = $validate->errors()->toArray();
                $e = array_shift($v);
                $return['msg'] = $e[0];
                return response()->json($return);
            }

            $datetime = Carbon::now(config('app.timezone'))->subMinutes(5);

            $getDevices = Device::where('status_id', '=', Label::getDeployedLabel()->id)
                ->where('assigned_for', '=', 1)
                ->whereNotNull('chkout_log_id')
                ->where(function ($q) {
                    $q->whereNull('accepted')->orWhere('accepted', '!=', 'accepted');
                })
                ->where(function ($q) use ($datetime) {
                    $q->whereNull('accept_link_send_at')->orWhere('accept_link_send_at', '<', $datetime->format('Y-m-d H:i:s'));
                });

            /*if( $request->send_to == 1 ) {
                send to All (Who not yet confirm)
                $getDevices->where(function($q) {
                    $q->whereNull('accepted')->orWhere('accepted', '!=', 'accepted');
                })
                ->where(function($q) use($datetime) {
                    $q->whereNull('accept_link_send_at')->orWhere('accept_link_send_at', '<', $datetime->format('Y-m-d H:i:s'));
                });
            }*/
            if ($request->send_to == 2) {
                /* send to departments */
                if (!is_array($request->departments) || !count($request->departments)) {
                    $return['msg'] = trans('content.device_fields.choose_department');
                    return response()->json($return);
                }

                $getDevices->withAssignedUser()->whereIn('users.department_id', $request->departments);
            } elseif ($request->send_to == 3) {
                /* send to locations */
                if (!is_array($request->locations) || !count($request->locations)) {
                    $return['msg'] = trans('content.device_fields.choose_locations');
                    return response()->json($return);
                }

                $getDevices->withAssignedUser()->whereIn('users.location_id', $request->locations);
            }

            $devices = $getDevices->orderBy('last_checkout')->get();
            if (!count($devices)) {
                throw new \Exception('No Checkout found to send reminder');
            }

            $globalAlerts = CommonHelper::getGlobalAlertEmail() ?? [];
            foreach ($devices as $device) {
                $log = $device->chkoutLog;
                $user = $device->user;
                if ($log && $user && $user->email && $user->activated == 1) {
                    // $cc = array_unique(array_filter(array_merge($globalAlerts, [$user->email]), function ($email) use ($user) {
                    //     return filter_var($email, FILTER_VALIDATE_EMAIL) && $email !== $user->email;
                    // }));
                    Mail::to($user->email)->queue(new DeviceCheckoutAcceptRemainder($device, $log, $user));
                    $return['send_reminder'] = $return['send_reminder'] + 1;
                    $device->accept_link_send_at = Carbon::now(config('app.timezone'));
                    $device->timestamps = false;
                    $device->save();
                }
            }
            Log::info('sendBulkChkoutAcceptReminder uid: ' . Auth::user()->id);
            $return['status'] = 'success';
            $return['msg'] = 'Accept Remainder have been sent to ' . $return['send_reminder'] . ' number of user(s)';
        } catch (\Exception $e) {
            Log::error('sendBulkChkoutAcceptReminder Error uid: ' . Auth::user()->id . ' - ' . $e->getMessage());
            $return['msg'] = $e->getMessage();
        }

        return response()->json($return);
    }

    public function sendChkoutAcceptReminder(Request $request)
    {
        try {
            $return = ['status' => 'fail', 'msg' => 'Unable to send the reminder'];
            if (!Auth::user()->hasPermissionTo('DeviceSendReminder') || !config('services.assets.enabled')) {
                $return['msg'] = trans('content.user_fields.Permission_denied');
                return response()->json($return);
            }
            if (!config('mail.service_enabled')) {
                throw new \Exception(trans('content.device_fields.mail_Service_not_enanbled'));
            }

            $device = Device::findOrFail($request->id);
            if ($device->assigned_for != 1) {
                throw new \Exception(trans('content.device_fields.device_not_checked'));
            }

            $log = $device->chkoutLog;
            $user = $device->user;

            if (!$user || !$user->email) {
                $return['msg'] = 'Checkout user email address not found';
                return response()->json($return);
                // throw new \Exception(trans('content.device_fields.unable_get_the_user'));
            }

            // $alertnotify = CommonHelper::getGlobalAlertEmail();
            $mail = Mail::to($user->email);
            // if (!empty($alertnotify)) {
            //     $mail->cc($alertnotify);
            // }
            $mail->queue(new DeviceCheckoutAcceptRemainder($device, $log, $user));

            $device->accept_link_send_at = Carbon::now(config('app.timezone'));
            $device->timestamps = false;
            $device->save();

            $return['msg'] = trans('content.device_fields.remainder_mail');
            $return['status'] = 'success';
            Log::info('sendChkoutAcceptReminder id:' . $device->id . ' uid:' . Auth::user()->id . ' - ' . $user->email);
        } catch (\Exception $e) {
            Log::error('sendChkoutAcceptReminder error id:' . $request->id . ' uid:' . Auth::user()->id);
            $return['msg'] = $e->getMessage();
        }

        return response()->json($return);
    }

    public function autoincrement_asset()
    {
        $return = array('status' => 'failure', 'msg' => 'No autoincrement device tag');
        $tag = Device::getNextId();
        if ($tag) {
            $return['incrementedid'] = $tag;
            $return['msg'] = '';
            $return['status'] = 'success';
        }
        return response()->json($return);
    }

    public function editDevice($id, Request $request)
    {
        $return = array(
            'status' => 'failure',
            'msg' => 'Unable to edit Device'
        );
        if (!Auth::user()->hasPermissionTo('DeviceEdit') || !config('services.assets.enabled')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $assetObj = Device::where('id', '=', $id)->first();
        $device = Device::where('id', '=', $id)->first();

        if (!$device->exists) {
            return response()->json($return);
        }

        if (isset($request->ip)) {
            $ipObj = BlockedIP::select('ip')->where('ip', $request->ip)->first();
        }
        if (isset($request->mac)) {
            $macObj = BlockedIP::select('mac')->where('mac', $request->mac)->first();
        }
        if (!empty($ipObj) || !empty($macObj)) {
            $return['msg'] = trans('content.device_fields.this_ip_mac_is_already_block');
            return response()->json($return);
        }

        if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($device)) {
            // $return["status"] = 'error';
            $return['section'] = 'device-edit';
            $return['msg'] = Auth::user()->company_id == null ? trans('content.device_fields.update_company_name') : trans('content.device_fields.multiple_company_access');
            return response()->json($return);
        }

        $system_deployed_status = Label::getDeployedLabel()->id;

        $data = $request->only('asset_tag', 'uuid', 'name', 'company_id', 'model_id', 'asset_owner', 'location_id', 'status_id', 'supplier_id', 'serial', 'product_number', 'order_number', 'purchase_date', 'purchase_cost', 'warranty_months', 'notes', 'rtd_location_id', 'internal_place_id', 'requestable', 'high_pririty', 'live_monitor', 'image', 'delete_img', 'purchase_currency', 'amc_supplier_id', 'amc_expire_date', 'invoice_id', 'stock_place', 'lease_id', 'device_occure_type', 'warranty_start_date', 'warrenty_end_date', 'ip', 'mac', 'asset_type_id', 'scanner_id', 'in_antenna', 'out_antenna', 'device_rfid', 'block_device_movement', 'department_id', 'expected_checkin');

        $rules = [
            'asset_tag' => ['required', 'string', 'clean_text_only', 'max:100', Rule::unique('assets')->ignore($id)],
            'model_id' => 'required|integer|min:1|exists:models,id',
            'asset_owner' => 'sometimes|nullable|integer|min:1|exists:users,id',
            'status_id' => 'required|integer|min:1|exists:status_labels,id',
            'company_id' => 'required|integer|min:1|exists:companies,id',
            'rtd_location_id' => 'required|integer|min:1|exists:locations,id',  // default location
            'internal_place_id' => 'nullable|integer|min:1|exists:places,id',
            'invoice_id' => 'nullable|integer|min:1|exists:purchases,id',
            'supplier_id' => 'nullable|integer|min:1|exists:suppliers,id',
            'amc_supplier_id' => 'nullable|integer|min:1|exists:suppliers,id',
            'amc_expire_date' => 'nullable|date_format:d/m/Y',
            'warranty_start_date' => 'nullable|date_format:d/m/Y',
            'warrenty_end_date' => 'nullable|date_format:d/m/Y',
            'name' => 'nullable|max:100|clean_text_only',
            'uuid' => 'nullable|string|max:100|clean_text_only',
            'product_number' => ['nullable', 'string', 'max:255', 'clean_text_only_with_hash', 'not_regex:/[\$]/'],
            'serial' => ['required', 'string', 'max:255', 'clean_text_only', 'not_regex:/[#\$]/'],
            'purchase_date' => 'nullable|date_format:d/m/Y',
            'order_number' => 'nullable|string|max:100|clean_text_only',
            'purchase_cost' => 'nullable|numeric|clean_text_only',
            'purchase_currency' => 'nullable|string|max:3',
            'warranty_months' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:2000|clean_text_only',
            'requestable' => 'sometimes|nullable|integer|min:0|max:1',
            'sez_device' => 'sometimes|nullable|integer|min:0|max:1',
            'high_pririty' => 'sometimes|nullable|integer|min:0|max:1',
            'live_monitor' => 'sometimes|nullable|integer|min:0|max:1',
            'delete_img' => 'sometimes|nullable|integer|min:0|max:1',
            'image' => 'sometimes|mimes:jpeg,bmp,png,gif',
            'stock_place' => 'nullable|integer|min:1|exists:places,id',
            'device_occure_type' => 'nullable|integer|min:0|max:3|clean_text_only',
            'lease_id' => 'nullable|integer|min:1|exists:lease_agreements,id',
            'ip' => 'nullable|string|max:50|clean_text_only',
            'mac' => 'nullable|string|max:60|clean_text_only',
            'asset_type_id' => 'nullable|integer|min:1|exists:assets_types,id',
            'device_rfid' => 'nullable',
            'department_id' => 'nullable|integer|min:1|exists:departments,id',
            'expected_checkin' => 'nullable|date_format:d/m/Y'
        ];

        $messages = [
            'model_id.required' => 'Please Select Model',
            'company_id.exists' => 'Please select company'
        ];

        $transferItemCount = TransferItem::where('device_id', $assetObj->id)->where('transfer_status', 1)->count();
        if ($transferItemCount >= 1) {
            if ($assetObj->rtd_location_id != $data['rtd_location_id'] || $assetObj->status_id != $data['status_id']) {
                $return['msg'] = trans('content.device_fields.unable_to_change_location_status');
                return response()->json($return);
            }
        }

        $data_fields = $request->input('fields', null);
        $custom_fields = $custom_fields_sez = [];
        $model = Model::find($data['model_id']);
        if ($model && $model->customFieldset && count($model->customFieldset->fields)) {
            foreach ($model->customFieldset->fields as $f) {
                $col_name = $f->nameToColumn();
                $formatType = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[] = $col_name;
            }
        }

        if ($model && $model->category->customFieldset && count($model->category->customFieldset->fields)) {
            foreach ($model->category->customFieldset->fields as $f) {
                $col_name = $f->nameToColumn();
                $formatType = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[] = $col_name;
            }
        }

        if (Settings::first()->custom_fieldset_id != '') {
            $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
            if (!empty($customFieldset)) {
                foreach ($customFieldset->fields as $f) {
                    $col_name = $f->nameToColumn();
                    $formatType = CommonHelper::convertFormatToRegex($f->format);
                    $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                    // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                    $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                    $custom_fields[] = $col_name;
                }
            }
        }

        unset($data['delete_img']);

        if ($device->status_id == $system_deployed_status) {
            unset($rules['status_id']);
            unset($data['status_id']);
        }

        if (($device->status_id == 4) && (isset($data['status_id']) && $data['status_id'] != 4) && (!empty($request->temp_id))) {
            $devExpense = AssetExpense::where('asset_id', $id)->where('temp_id', $request->temp_id)->first();
            if (!empty($devExpense)) {
                $devExpense->update(['temp_id' => null]);
            }
        }

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }
        $live_monitor_device = LiveMonitorDevice::where('device_id', $id)->first();
        if (isset($request->live_monitor)) {
            if ($live_monitor_device != null) {
                $live_monitor_device->added_by = Auth::user()->id;
                $live_monitor_device->save();
            } else {
                $live_monitor_device = new LiveMonitorDevice();
                $live_monitor_device->device_id = $id;
                $live_monitor_device->added_by = Auth::user()->id;
                $live_monitor_device->save();
            }
        } else {
            if (config('services.live_monitor.enabled') && auth()->user()->can('LiveMonitorAdd') && $live_monitor_device != null) {
                $live_monitor_device->delete();
            }
        }

        if (isset($request->device_rfid)) {
            foreach ($request->device_rfid as $rfid) {
                $deviceRfidTag = DeviceRfid::where('device_rfid', $rfid)->count();
                if ($deviceRfidTag > 0) {
                    $deviceTagFind = DeviceRfid::where('device_rfid', $rfid)->where('device_id', $id)->count();
                    if ($deviceTagFind <= 0) {
                        $return['msg'] = $rfid . ' : RFID Tag is already in use.';
                        return response()->json($return);
                    }
                }
            }
        }

        if (isset($data['status_id']) && $data['status_id'] == $system_deployed_status) {
            $return['msg'] = trans('content.device_fields.not_allowed');
            return response()->json($return);
        }
        $customFieldsCacheData = $customFieldsRecordData = [];
        if (Settings::first()->custom_fieldset_id != '') {
            $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
            if (!empty($customFieldset)) {
                $customFieldsCacheData = CommonHelper::interactedData($customFieldset->fields, $assetObj);
            }
        }
        $for_log_comparison = $device->dataForCache();
        unset($data['serial']);
        $device->fill($data);
        $device->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, 'Y-m-d', 'd/m/Y') : null;
        $device->warranty_start_date = $request->warranty_start_date ? CommonHelper::getDateAs($request->warranty_start_date, 'Y-m-d', 'd/m/Y') : null;
        $device->warrenty_end_date = $request->warrenty_end_date ? CommonHelper::getDateAs($request->warrenty_end_date, 'Y-m-d', 'd/m/Y') : null;
        $device->purchase_cost = (float) $request->purchase_cost;
        $device->warranty_months = $request->warranty_months > 0 ? $request->warranty_months : null;
        $device->amc_expire_date = $request->amc_expire_date ? CommonHelper::getDateAs($request->amc_expire_date, 'Y-m-d', 'd/m/Y') : null;
        $device->requestable = $request->input('requestable', 0);
        $device->sez_device = $request->input('sez_device', 0);
        if (config('services.live_monitor.enabled') && auth()->user()->can('LiveMonitorAdd') && $live_monitor_device != null) {
            $device->live_monitor = $request->input('live_monitor', 0);
        }
        $device->high_pririty = $request->input('high_pririty', 0);
        $device->user_id = Auth::user()->id;
        $device->archived = '0';
        $device->physical = '1';
        $device->depreciate = '0';
        $device->asset_owner = $request->input('asset_owner');
        $device->department_id = $request->department_id;
        $device->invoice_id = $request->invoice_id;
        $device->notes = $request->notes;
        $existingRfid = DeviceRfid::where('device_id', $device->id)->count();
        if ($existingRfid > 0 && !isset($request->device_rfid)) {
            $deleteRfid = DeviceRfid::where('device_id', $device->id)->delete();
        }
        if (isset($request->device_rfid)) {
            $newRfids = $request->device_rfid ?? [];
            $getRfid = DeviceRfid::where('device_id', $device->id)->pluck('device_rfid')->toArray() ?? [];
            $insertRfids = array_diff($newRfids, $getRfid);
            $deleteRfids = array_diff($getRfid, $newRfids);
            if (!empty($deleteRfids)) {
                DeviceRfid::where('device_id', $device->id)->whereIn('device_rfid', $deleteRfids)->delete();
            }
            foreach ($insertRfids as $rfid) {
                $deviceRfidTag = new DeviceRfid();
                $rfid_array = [
                    'device_id' => $device->id,
                    'device_rfid' => $rfid,
                ];
                $deviceRfidTag->fill($rfid_array);
                if (!$deviceRfidTag->save()) {
                    $return['msg'] = 'Unable to add device RFID';
                    return response()->json($return);
                }
            }
        }
        if (isset($request->scanner_id)) {
            $device->scanner_id = $request->scanner_id;
        }

        $device->in_antenna = !empty($request->in_antenna) ? implode(',', $request->in_antenna) : null;
        $device->out_antenna = !empty($request->out_antenna) ? implode(',', $request->out_antenna) : null;
        $device->block_device_movement = !empty($request->block_device_movement) ? $request->block_device_movement : null;

        if (count($custom_fields)) {
            foreach ($custom_fields as $f) {
                $device->{$f} = isset($data[$f]) ? $data[$f] : null;
            }
        }

        $imagedata = Device::find($id)->image;
        if ($request->input('delete_img', false) || $request->image != null) {
            if ($imagedata != null) {
                $exits1 = File::exists(public_path('/uploads/devices/' . $imagedata));
                if ($exits1) {
                    File::delete(public_path('/uploads/devices/' . $imagedata));
                    Device::where('id', $id)->update(['image' => null]);
                }
            }
        }
        if ($request->image) {
            $uploaded_img = $request->image;
            $deviceImage = Str::random(12) . Str::random(12) . '.' . $uploaded_img->getClientOriginalExtension();
            $checkFolderPath = CommonHelper::attachmentFolderStructure('uploads', 'devices');
            if (!$checkFolderPath) {
                return $this->fail(500, ' Directory not found', '');
            }
            $path = public_path('uploads/devices/' . $deviceImage);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($uploaded_img->getRealPath());
            $image->scale(width: 300);
            $image->save($path);
            $device->image = $deviceImage;
        }
        $device->warranty_status = $device->updateWarrantyStatus();
        $device->calc_warranty_expire_date = $device->updateWarrantyExpireDate();
        $device->expected_checkin = isset($data['expected_checkin']) ? CommonHelper::getDateAs($data['expected_checkin'], 'Y-m-d', 'd/m/Y') : null;
        $device->last_checkout_project = $request->project_name ?? null;
        if (Settings::first()->custom_fieldset_id != '') {
            $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
            if (!empty($customFieldset)) {
                $customFieldsRecordData = CommonHelper::interactedData($customFieldset->fields, $device);
            }
        }
        if (!empty($request->allocation_type)) {
            $latestId = Actionlog::where('asset_id', $id)->where('action_type', 'Checkout')->latest()->select('id', 'allocation_type_id')->first();

            if (!empty($latestId) && $latestId->allocation_type_id != $request->allocation_type) {
                Actionlog::where('id', $latestId->id)->update(['allocation_type_id' => $request->allocation_type]);
            }
        }

        if ($device->save()) {
            if (in_array(config('app.client'), ['rolepermission', 'knightfrank', 'rashmi'])) {
                $oldStatus = $assetObj->status_id;
                $newStatus = $device->status_id;
                if ($oldStatus != $newStatus) {
                    CommonHelper::updateStatusCounts($oldStatus, $newStatus, $device);
                } else if ($assetObj->model_id != $device->model_id) {
                    CommonHelper::updateStatusCounts($oldStatus, $newStatus, $device, null, $assetObj->model_id);
                }
            }
            $isUpdated = false;
            $updatedFields = [];
            $deviceArray = $device->toArray();
            $comparisonArray = $for_log_comparison;
            foreach ($deviceArray as $key => $value) {
                if (isset($comparisonArray[$key]) && $comparisonArray[$key] != $value) {
                    $isUpdated = true;
                    $updatedFields[] = $key;
                }
            }
            if ($isUpdated) {
                Actionlog::deviceEdited($device, $for_log_comparison, Auth::user()->id, $customFieldsCacheData, $customFieldsRecordData);
            }
            $this->notifyCategoryThresould($device->model_id);
            $deviceSez = DeviceSez::where('device_id', $id)->first();
            if (isset($request->sez_device) && $request->sez_device == 1) {
                if (empty($deviceSez)) {
                    $deviceSez = new deviceSez();
                    $deviceSez->device_id = $device->id;
                    $deviceSez->save();
                }
            } else {
                if (!empty($deviceSez)) {
                    $deviceSez->delete();
                }
            }

            $return['status'] = 'success';
            $return['msg'] = trans('content.device_fields.device_changes');
        }

        if (Settings::first()->cost_earned == 1) {
            $billingunit = BillingUnit::updateOrCreate([
                'asset_id' => $id,
            ], [
                'rate_hr' => $request->rate_hr,
                'rate_day' => $request->rate_day,
                'rate_week' => $request->rate_week,
                'rate_month' => $request->rate_month,
                'rate_quarterly' => $request->rate_quarterly,
                'rate_half_yearly' => $request->rate_half_yearly,
                'rate_yearly' => $request->rate_yearly,
            ]);
        }

        if (config('app.socket_enabled')) {
            CommonHelper::sendDeviceCountToSocket();
        }

        // Mail trigger asset is mapped to asset owner
        if ($assetObj->asset_owner != $device->asset_owner) {
            $asset_owner_mail = User::where('id', $device->asset_owner)->select('id', 'email', DB::raw('concat(first_name, " ", last_name, "") as asset_owner_name'))->first();
            $log = $device->chkoutLog;
            try {
                if (config('mail.service_enabled') && $asset_owner_mail && $asset_owner_mail->email != '' && $asset_owner_mail->email && filter_var($asset_owner_mail->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($asset_owner_mail->email)->queue(new AssetOwnerMail($device, $log, $asset_owner_mail));
                }
            } catch (\Exception $ex) {
                Log::error('editDevice assetOwner Mail: ' . $ex->getMessage());
            }
        }

        return response()->json($return);
    }

    public function getAssignTo(Request $request)
    {
        $result = array('result' => false);
        $data = $request->only('company_id', 'location_id', 'assign_to');
        $company_id = trim($data['company_id']);
        $location_id = trim($data['location_id']);
        $assign_to = trim($data['assign_to']);

        if ($company_id && $location_id && in_array($assign_to, array('User', 'Department'))) {
            $result['result'] = true;
            if ($assign_to == 'User') {
                $objUser = new User();
                $getUsers = $objUser->getUsersToOpts(array(array('company_id', '=', $company_id)));
                $result['users'] = $getUsers;
            } else {
                $objDepartment = new Department();
                $getDepartments = $objDepartment->getDepartmentOpts(array(
                    array('location_id', '=', $location_id)
                ));
                $result['departments'] = $getDepartments;
            }
        }

        return response()->json($result);
    }

    public function deleteDevice(Request $request, $id)
    {
        $return = array('status' => 'failure', 'msg' => 'Unable to delete the Device');
        if (!Auth::user()->hasPermissionTo('DeviceDelete') || !config('services.assets.enabled')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $device = Device::where('id', '=', $id)->first();
        if (!$device || !$device->exists) {
            return response()->json($return);
        }

        $under_transfer = DB::table('transfer_items')->where('device_id', $device->id)->where('transfer_status', 1)->first();
        if (!empty($under_transfer)) {
            $return['status'] = 'failure';
            $return['msg'] = trans('content.device_fields.device_is_under_transfer');
            return response()->json($return);
        }

        if ($device->assigned_to) {
            $return['msg'] = trans('content.device_fields.device_checkout');
            return response()->json($return);
        }

        $record = Device::where('id', '=', $id)->withCount(['asset_maintenance', 'cm_relevant_device', 'component', 'itm_network_device_track', 'license_seat', 'acc_checkout_device', 'tkt_ticket', 'tkt_scheduled_maintenance'])->first();
        if ($record->asset_maintenance_count)
            return response()->json(['status' => 'error', 'msg' => 'This asset is given for device maintenance. Please unlink them and try again !!']);
        if ($record->cm_relevant_device_count)
            return response()->json(['status' => 'error', 'msg' => 'This asset is given for some changes. Please unlink them and try again !!']);
        if ($record->component_count)
            return response()->json(['status' => 'error', 'msg' => 'This asset is Checked out by a component. Please unlink them and try again !!']);
        /*if($record->itm_network_device_track_count)
            return response()->json(['status' => 'error', 'msg' => 'Some records are attached with this assets. Please unlink them and try again !!']);*/
        if ($record->acc_checkout_device_count)
            return response()->json(['status' => 'error', 'msg' => 'This asset is Checked out by an Accessory. Please unlink them and try again !!']);
        if ($record->license_seat_count)
            return response()->json(['status' => 'error', 'msg' => 'This asset is Checked out by some Licenses. Please unlink them and try again !!']);
        if ($record->tkt_ticket_count)
            return response()->json(['status' => 'error', 'msg' => 'Some tickets are attached with this assets. Please unlink them and try again !!']);
        /*if($record->tkt_scheduled_maintenance_count)
            return response()->json(['status' => 'error', 'msg' => 'This asset is allocated to schedule maintenance. Please unlink them and try again !!']);*/

        if (AssetDispose::where('asset_id', $id)->where('is_dispose', 1)->exists())
            return response()->json(['status' => 'error', 'msg' => 'This device has been permanently deleted!']);
        if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($device)) {
            $return['section'] = 'device-delete';
            $return['msg'] = Auth::user()->company_id == null ? trans('content.device_fields.update_company_name') : trans('content.device_fields.multiple_company_access');
            return response()->json($return);
        }

        $device->assigned_to = null;
        $thisdeviceModelId = $device->model_id;
        $device->delete();
        if ($device->delete()) {
            DB::table('itm_network_inventory_basic')
                ->where('BIOSSerialNumber', $record->serial)
                ->update(['deleted_at' => now()]);
            if (in_array(config('app.client'), ['rolepermission', 'knightfrank', 'rashmi'])) {
                $deviceOld = Device::withTrashed()->where('id', '=', $id)->first();
                $oldStatus = $deviceOld->status_id;
                if (!empty($oldStatus)) {
                    CommonHelper::updateStatusCounts($deviceOld->status_id, null, $deviceOld, 'deleted');
                }
            }
        }
        $getGroup = PatchManagementGroup::get();
        foreach ($getGroup as $key => $value) {
            PatchManagementGroupDevice::where('group_id', $value->id)->where('device_id', $id)->delete();
            $count = PatchManagementGroupDevice::where('group_id', $value->id)->count();
            if ($count == 0) {
                $value->delete();
            }
        }

        Actionlog::deviceDeleted($device, Auth::user()->id);
        $this->notifyCategoryThresould($thisdeviceModelId);

        if (config('app.socket_enabled')) {
            CommonHelper::sendDeviceCountToSocket();
        }
        $user = Auth::user();
        if (Settings::first()->alerts_enabled == 1) {
            try {
                $alertnotify = CommonHelper::getGlobalAlertEmail();
                if (config('mail.service_enabled') && !empty($alertnotify)) {
                    foreach ($alertnotify as $email) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($email)->queue(new DeleteDevice($device, $user));
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        Log::info('deleteDevice id:' . $device->id . ' user_id:' . Auth::user()->id);
        $return['status'] = 'success';
        $return['msg'] = trans('content.device_fields.deleted_devices');

        return response()->json($return);
    }

    public function deleteBulkDevices(Request $request)
    {
        $tags = ['ITM2607', 'ITM2605'];

        foreach ($tags as $tag) {
            $getDevice = Device::where('asset_tag', 'like', $tag)->get();
            if (!count($getDevice)) {
                echo sprintf('<br>%s is not found', $tag);
                continue;
            }

            $device = $getDevice[0];
            if (!$device || !$device->exists) {
                echo sprintf('<br>%s is not found', $tag);
                continue;
            }

            if ($device->assigned_to) {
                echo sprintf('<br>%s is checked out', $tag);
                continue;
            }

            if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($device)) {
                echo sprintf('<br>%s company access insuff', $tag);
                continue;
            }

            $device->assigned_to = null;
            $device->delete();
            Actionlog::deviceDeleted($device, Auth::user()->id);
            echo sprintf('<br>%s deleted', $tag);
        }
    }

    public function checkout(Request $request)
    {
        $return = array(
            'status' => 'failure',
            'msg' => 'Unable to checkout given device'
        );
        if (!Auth::user()->hasPermissionTo('DeviceCheckoutCheckin') || !config('services.assets.enabled')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only('id', 'name', 'assigned_for', 'assigned_to', 'assigned_place', 'checkout_at', 'expected_checkin', 'note', 'request_id', 'last_checkout_project', 'allocation_type_id', 'billable', 'rate', 'rate_cost');
        $validate = Validator::make($data, [
            'id' => 'required|integer|min:1',
            'name' => 'nullable|string|clean_text_only',
            'assigned_for' => 'required|integer',
            'assigned_to' => 'nullable|integer',
            'assigned_place' => 'nullable|integer',
            'last_checkout_project' => 'nullable|integer|min:1|exists:projects,id',
            'checkout_at' => 'nullable|date_format:d/m/Y',
            'expected_checkin' => 'nullable|date_format:d/m/Y',
            // "note" => "nullable|string|alpha_space|max:255",
            'note' => [
                Rule::requiredIf(fn() => config('app.client') === 'knightfrank'),
                'nullable',
                'clean_text_only',
                'alpha_space',
                'max:255',
            ],
            'allocation_type_id' => 'nullable|integer',
            'rate' => 'nullable|string|clean_text_only',
            'rate_cost' => 'nullable|string|clean_text_only',
        ]);

        if ($validate->fails()) {
            $v = $validate->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }

        $id = $data['id'];
        $dev = Device::where('id', '=', $id)->first();

        $transferItems = TransferItem::where('device_id', $id)->where('transfer_status', 1)->first();
        if (!empty($transferItems)) {
            $return['status'] = 'failure';
            $return['msg'] = trans('content.device_fields.device_under_transfer');
            return response()->json($return);
        }

        if (!$dev || !$dev->exists) {
            $return['msg'] = trans('content.device_fields.choosen_device_not');
            return response()->json($return);
        }

        if ($dev->assigned_for != null) {
            $return['status'] = 'failure';
            $return['msg'] = trans('content.device_fields.already_checkout');
            return response()->json($return);
        }

        $pendingLabel = Label::getPendingLabel()->id;
        if ($dev->status->sold == 1 || $dev->status->stolen_item == 1 || $dev->status_id == $pendingLabel) {
            $return['status'] = 'failure';
            $return['msg'] = trans('content.device_fields.device_invalid');
            return response()->json($return);
        }

        if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($dev)) {
            $return['section'] = 'device-checkout';
            $return['msg'] = Auth::user()->company_id == null ? trans('content.device_fields.update_company_name') : trans('content.device_fields.multiple_company_access');
            return response()->json($return);
        }

        /* check target either user or place */
        if ($request->assigned_for == '2') {
            $assignedTarget = Place::where('id', '=', $data['assigned_place'])->first();
        } else {
            if (!Auth::user()->checkoutBasicClearance()) {
                $assignedTarget = User::where('id', '=', $data['assigned_to'])->first();
            }
        }

        if (empty($assignedTarget) || !$assignedTarget->exists) {
            $return['msg'] = $request->assigned_for == '2' ? trans('content.device_fields.place_not_found') : trans('content.device_fields.user_not_found');
            return response()->json($return);
        }

        if ($request->assigned_for != '2') {
            if ($assignedTarget->checkoutBasicClearance()) {
                $return['msg'] = trans('content.device_fields.Choose_user_not');
                return response()->json($return);
            }

            /* allow only users location matched */
            if (config('app.client') == 'safari' && $assignedTarget->location_id != $dev->rtd_location_id) {
                $return['msg'] = trans('content.device_fields.user_location_not_match');
                return response()->json($return);
            }

            if ($assignedTarget->checkLastWorkingDate()) {
                $return['msg'] = trans('content.device_fields.last_working_date');
                return response()->json($return);
            }
            // if($assignedTarget->last_working_date && $request->checkout_at && $request->expected_checkin){
            //     if($request->checkout_at >= $assignedTarget->last_working_date) {
            //         $return["msg"] = "Checkout date must be less than user's last working date.";
            //         return response()->json($return);
            //     }
            //     if($request->expected_checkin >= $assignedTarget->last_working_date) {
            //         $return["msg"] = "Checkin Date must be less than user's last working date";
            //         return response()->json($return);
            //     }
            // }
        }

        try {
            if ($request->expected_checkin) {
                $checkout_at = Carbon::createFromFormat('d/m/Y', $request->checkout_at);
                $expected_checkin = Carbon::createFromFormat('d/m/Y', $request->expected_checkin);
                if ($expected_checkin->lt($checkout_at)) {
                    $return['msg'] = 'Expected Checkin date must be after the Checkout Date';
                    return response()->json($return);
                }

                $request->expected_checkin = $expected_checkin->format('d/m/Y');
            } else {
                $request->expected_checkin = Carbon::now(config('app.timezone'))->format('d/m/Y');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($return);
        }

        // admin permission need to check

        // trigger checkout
        if ($dev->checkout($data, $assignedTarget)) {
            $this->notifyCategoryThresould($dev->model_id);
            // stock place will be null on checkout
            $dev->stock_place = null;
            $dev->save();
            $return['project_id'] = $dev->last_checkout_project;
            $return['status'] = 'success';
            $return['msg'] = trans('content.device_fields.checked_out');
        }

        if (config('app.socket_enabled')) {
            CommonHelper::sendDeviceCountToSocket();
        }
        /** send push notification */
        $u = User::find($dev->assigned_to);
        $notify_people = [];
        $assignedByUser = User::find(Auth::user()->id);
        $notification = NotificationConfig::first();
        if ($notification->device_checkout == 1 && $request->assigned_for == '1') {
            $assignedUser = User::find($u->id);
            if (!empty($assignedUser)) {
                array_push($notify_people, $assignedUser->id);
                $notificationText = 'Device ' . $dev->asset_tag . ' is allocated to you.';
                $u->device_id = $id;
                $u->module_type = 'device';
                $data = [
                    'title' => $notificationText,
                    'data' => $u,
                    'notify' => $notify_people,
                ];

                $sendNotifications = CommonHelper::sendPushNotification($data);
                if ($sendNotifications != false) {
                    $response = json_decode($sendNotifications);
                    if (isset($response->failure) && $response->failure == 1) {
                        Log::error('device checkout push notification:' . $u->id . ' error ' . json_encode($response));
                    }
                }
            }
        }

        return response()->json($return);
    }

    public function resaleDevice(Request $request)
    {
        $appSettings = Settings::first();
        $return = array(
            'status' => 'danger',
            'msg' => 'Unable to dispose given device'
        );
        if (!Auth::user()->hasPermissionTo('DeviceResale') || !config('services.assets.enabled')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only('id', 'sold_by', 'sold_at', 'sold_value_format', 'sold_value', 'resale_notes', 'reference_no', 'org_name', 'vendor', 'asset_status', 'sold_by_option', 'accessories', 'components', 'licenses', 'consumables');
        $validate = Validator::make($data, [
            'asset_status' => ['required'],
            'sold_value_format' => 'nullable|string|max:6',
            'sold_at' => 'nullable|date_format:d/m/Y',
            'resale_notes' => 'nullable|string|max:2000|clean_text_only',
            'reference_no' => 'nullable|string|clean_text_only',
            'org_name' => 'nullable|string|clean_text_only',
            'vendor' => 'nullable|string|clean_text_only',
            'sold_by' => 'required_if:asset_status,==,sold',
            'sold_value' => 'required_if:asset_status,==,sold'
        ]);

        if ($validate->fails()) {
            $v = $validate->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }

        $id = $data['id'];
        $dev = Device::where('id', '=', $id)->withCount(['asset_maintenance', 'cm_relevant_device', 'component', 'itm_network_device_track', 'license_seat', 'acc_checkout_device', 'tkt_ticket', 'tkt_scheduled_maintenance'])->first();
        if ($dev->status_id == 7) {
            $return['msg'] = 'This Device is already disposed';
            return response()->json($return);
        }
        if ($dev->status_id == 6) {
            $return['msg'] = 'This Device is deployed so you cannot dispose this device';
            return response()->json($return);
        }
        $under_transfer = DB::table('transfer_items')->where('device_id', $dev->id)->where('transfer_status', 1)->first();
        if (!empty($under_transfer)) {
            $return['status'] = 'failure';
            $return['msg'] = trans('content.device_fields.device_is_under_transfer');
            return response()->json($return);
        }
        $soldLabel = Label::getSoldLabel()->id;
        $dev->sold_value = $data['sold_value'];
        $dev->sold_value_format = $data['sold_value_format'] ?? null;
        $dev->sold_by = ($data['sold_by_option'] == 'current_user') ? auth()->id() : ($data['sold_by'] ?? 0);
        $dev->sold_at = $request->sold_at ? CommonHelper::getDateAs($request->sold_at, 'Y-m-d', 'd/m/Y') : null;
        $dev->stock_place = null;
        $dev->resale_notes = $data['resale_notes'];
        if (!$dev || !$dev->exists) {
            return response()->json($return);
        }
        // if($dev->assigned_to) {
        //     $return["msg"] = trans('content.device_fields.device_checkout');
        //     return response()->json($return);
        // }
        $dispose = new AssetDispose();
        $dispose->asset_id = $id;
        $dispose->dispose_type = $data['asset_status'] ?? null;
        $dispose->dispose_price = $data['sold_value'] ?? null;
        $dispose->currency_format = $data['sold_value_format'] ?? null;
        $dispose->dispose_at = !empty($data['sold_at']) ? Carbon::createFromFormat('d/m/Y', $data['sold_at'])->format('Y-m-d') . ' ' . Carbon::now()->format('H:i:s') : null;
        $dispose->vendor_org_name = !empty($data['org_name']) ? $data['org_name'] : (!empty($data['vendor']) ? $data['vendor'] : null);
        $dispose->dispose_by = ($data['sold_by_option'] == 'current_user') ? auth()->id() : ($data['sold_by'] ?? 0);
        $dispose->updated_by = auth()->id();
        $dispose->note = $data['resale_notes'] ?? null;
        $dispose->ref_no = $data['reference_no'] ?? null;
        $dispose->asset_tag = $dev->asset_tag ?? null;
        $dispose->model_id = $dev->model_id ?? null;
        $dispose->serial = $dev->serial ?? null;

        if ($dispose->save()) {
            // Accessories
            if (isset($data['accessories'])) {
                foreach ($data['accessories'] as $index => $value) {
                    $parts = explode('_', $index);
                    $accessoryId = $parts[0] ?? null;
                    $accessoryUserId = $parts[1] ?? null;
                    if (!$accessoryId) {
                        continue;
                    }
                    $accessory = Accessory::find($accessoryId);
                    if (!$accessory) {
                        continue;
                    }
                    $deviceItemDispose = new DeviceItemDispose();
                    $deviceItemDispose->asset_id = $id;
                    $deviceItemDispose->device_dispose_id = $dispose->id;
                    $deviceItemDispose->asset_type = 'accessory';
                    $deviceItemDispose->item_id = $accessory->id;
                    $deviceItemDispose->item_user_id = $accessoryUserId;
                    $deviceItemDispose->item_name = $accessory->name;
                    $deviceItemDispose->is_dispose = $value;
                    $deviceItemDispose->save();
                }
            }

            if (isset($data['consumables'])) {
                foreach ($data['consumables'] as $index => $value) {
                    $parts = explode('_', $index);
                    $consumablesId = $parts[0] ?? null;
                    $consumablesIdUserId = $parts[1] ?? null;
                    if (!$consumablesId) {
                        continue;
                    }
                    $consumables = Consumable::find($consumablesId);
                    if (!$consumables) {
                        continue;
                    }
                    $deviceItemDispose = new DeviceItemDispose();
                    $deviceItemDispose->asset_id = $id;
                    $deviceItemDispose->device_dispose_id = $dispose->id;
                    $deviceItemDispose->asset_type = 'consumables';
                    $deviceItemDispose->item_id = $consumables->id;
                    $deviceItemDispose->item_user_id = $consumablesIdUserId;
                    $deviceItemDispose->item_name = $consumables->name;
                    $deviceItemDispose->is_dispose = $value;
                    $deviceItemDispose->save();
                }
            }
            // Components
            if (isset($data['components'])) {
                foreach ($data['components'] as $index => $value) {
                    $parts = explode('_', $index);
                    $componentId = $parts[0] ?? null;
                    if (!$componentId) {
                        continue;
                    }

                    $component = Component::find($componentId);
                    if (!$component) {
                        continue;
                    }
                    $deviceItemDispose = new DeviceItemDispose();
                    $deviceItemDispose->asset_id = $id;
                    $deviceItemDispose->device_dispose_id = $dispose->id;
                    $deviceItemDispose->asset_type = 'component';
                    $deviceItemDispose->item_id = $component->id;
                    $deviceItemDispose->item_name = $component->unique_tag;
                    $deviceItemDispose->is_dispose = $value;
                    $deviceItemDispose->save();
                }
            }

            // Licenses
            if (isset($data['licenses'])) {
                foreach ($data['licenses'] as $index => $value) {
                    $parts = explode('_', $index);
                    $licenseId = $parts[0] ?? null;
                    $licSeatId = $parts[1] ?? null;
                    if (!$licenseId) {
                        continue;
                    }

                    $license = License::find($licenseId);
                    if (!$license) {
                        continue;
                    }
                    $deviceItemDispose = new DeviceItemDispose();
                    $deviceItemDispose->asset_id = $id;
                    $deviceItemDispose->device_dispose_id = $dispose->id;
                    $deviceItemDispose->asset_type = 'license';
                    $deviceItemDispose->item_id = $licenseId;
                    $deviceItemDispose->item_user_id = $licSeatId;
                    $deviceItemDispose->item_name = $license->name;
                    $deviceItemDispose->is_dispose = $value;
                    $deviceItemDispose->save();
                }
            }
            $dev->sold_with_status = $dev->status_id;
            $dev->status_id = $soldLabel;
            if ($dev->save()) {
                Actionlog::deviceSold($dev, auth()->id());
                if (in_array(config('app.client'), ['rolepermission', 'knightfrank', 'rashmi'])) {
                    if (!empty($dev->sold_with_status) || !empty($dev->status_id)) {
                        CommonHelper::updateStatusCounts($dev->sold_with_status, $dev->status_id, $dev);
                    }
                }
            }
            $return['status'] = 'success';
            $return['msg'] = 'Device is Disposed Successfully.';
            if (config('app.socket_enabled')) {
                CommonHelper::sendDeviceCountToSocket();
            }
            $user = Auth::user();
            $toUserEmail = $user->email ?? $soldByUser->email ?? null;
            if (Settings::first()->alerts_enabled == 1) {
                try {
                    $alertnotify = CommonHelper::getGlobalAlertEmail();
                    $soldByUser = User::find($dispose->dispose_by);
                    if ($user->id != $dispose->dispose_by && !empty($user->email)) {
                        array_push($alertnotify, $user->email);
                        $alertnotify = array_unique($alertnotify);
                    }
                    $soldByEmail = $soldByUser->email ?? null;
                    $soldByName = $soldByUser->displayName ?? trim(($soldByUser->first_name ?? '') . ' ' . ($soldByUser->last_name ?? '')) ?: null;
                    if (config('mail.service_enabled') && isset($dev, $user, $dispose, $soldByName) && $dev && $user && $dispose && $soldByName) {
                        $mail = new ResaleDevice($dev, $user, $dispose, $soldByName);
                        !empty($alertnotify) ? Mail::to($toUserEmail)->cc($alertnotify)->queue($mail) : Mail::to($toUserEmail)->queue($mail);
                    }
                } catch (\Exception $e) {
                    Log::error('disposeDevice Mail Error: ' . $e->getMessage());
                }
            }
        }
        return response()->json($return);
    }

    public function checkin(Request $request)
    {
        $return = array(
            'status' => 'danger',
            'msg' => 'Unable to checkin given device'
        );
        if (!Auth::user()->hasPermissionTo('DeviceCheckoutCheckin') || !config('services.assets.enabled')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only('id', 'status_id', 'checkin_at', 'note', 'stock_place', 'checkin_attachment');
        $validate = Validator::make($data, [
            'id' => 'required|integer|min:1',
            'status_id' => 'required|integer',
            'checkin_at' => 'nullable|date_format:d/m/Y',
            'note' => 'nullable|string|max:255|clean_text_only',
            'note' => [
                'nullable',
                'clean_text_only',
                'alpha_space',
                'max:255',
                Rule::requiredIf(config('app.client') === 'knightfrank'),
            ],
            'stock_place' => 'nullable|integer|min:1|exists:places,id',
            'checkin_attachment' => 'sometimes|mimes:jpeg,jpg,png',
        ]);

        if ($validate->fails()) {
            $v = $validate->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }

        $id = $data['id'];
        $dev = Device::where('id', '=', $id)->first();
        $oldStatus = $dev->status_id;
        if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($dev)) {
            // $return["status"] = 'error';
            $return['section'] = 'device-checkin';
            $return['msg'] = Auth::user()->company_id == null ? trans('content.device_fields.update_company_name') : trans('content.device_fields.multiple_company_access');
            return response()->json($return);
        }

        if (!$dev->exists) {
            $return['msg'] = trans('content.device_fields.choosen_device_not_found');
            return response()->json($return);
        }

        // needs to check current user access

        if (!$dev->assigned_to) {
            $return['msg'] = trans('content.device_fields.choosen_device_already_check');
            return response()->json($return);
        }

        $assigned_for = $dev->assigned_for;
        $assigned_to = $dev->assigned_to;
        $last_checkout_project = $dev->last_checkout_project;

        if ($assigned_for != 2) {
            $assignedUser = User::where('id', '=', $dev->assigned_to)->first();
        }

        $checkout = $dev->last_checkout;
        $dev->status_id = $data['status_id'];
        $dev->assigned_to = null;
        $dev->assigned_for = null;
        $dev->accepted = null;
        $dev->expected_checkin = null;
        $dev->last_checkout = null;
        $dev->last_checkout_project = null;
        $dev->stock_place = $data['stock_place'];

        if ($dev->save()) {
            try {
                $rnu = new RequestableNotifyUser();

                if ($rnu->where('notified_device', $id)->exists()) {
                    $notifiedUsers = $rnu->where('notified_device', $id)->get();
                    if ($notifiedUsers->count() > 0) {
                        foreach ($notifiedUsers as $key => $value) {
                            $userId = $value->notified_user;
                            $user = User::find($userId);
                            if (!empty($user) && $user->email) {
                                $alertnotify = [];
                                if (Settings::first()->alerts_enabled == 1) {
                                    $alertnotify = CommonHelper::getGlobalAlertEmail();
                                }
                                if (config('mail.service_enabled') && $user && !empty($user) && $user->email != null && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($user->email)->cc($alertnotify)->queue(new RequestableNotifyUsers($user, $dev, $value->created_at));
                                }
                                // befor delete the user send the notification who is request for this device
                                $rnu->where('notified_user', $user->id)->where('notified_device', $id)->delete();
                                Log::info("Deleted notification requests for device ID: {$id}");
                            }
                        }
                    }
                } else {
                    Log::info("Failed to find notification requests for device ID: {$id}");
                }
            } catch (\Exception $e) {
                Log::error("Failed to delete notification requests for device ID: {$id}", [
                    'error' => $e->getMessage()
                ]);
            }

            if (in_array(config('app.client'), ['rolepermission', 'knightfrank', 'rashmi'])) {
                $newStatus = $dev->status_id;
                if (!empty($oldStatus) || !empty($newStatus)) {
                    CommonHelper::updateStatusCounts($oldStatus, $newStatus, $dev);
                }
            }
            $actionlog = DeviceBillingCheckinCheckout::where('asset_id', $dev->id)->where('action_type', 'Checkout')->whereNull('checkin_at')->first();
            if (Settings::first()->cost_earned == 1) {
                if ($actionlog && $actionlog->rate != null) {
                    $checkoutTime = Carbon::parse($checkout);
                    $date = Carbon::createFromFormat('d/m/Y H:i:s', $data['checkin_at'] . ' ' . date('H:i:s'));
                    $checkinTime = isset($data['checkin_at']) && $data['checkin_at'] ? Carbon::parse($date) : Carbon::parse($dev->updated_at);
                    $totalDuration = $checkinTime->diffInHours($checkoutTime);
                    switch ($actionlog->rate) {
                        case 'Rate Hr':
                            $cost = $totalDuration * $actionlog->rate_cost;
                            break;
                        case 'Rate Day':
                            $cost = (($totalDuration * 0.042) * $actionlog->rate_cost);
                            break;
                        case 'Rate Week':
                            $cost = (($totalDuration * 0.006) * $actionlog->rate_cost);
                            break;
                        case 'Rate Month':
                            $cost = (($totalDuration * 0.00137) * $actionlog->rate_cost);
                            break;
                        case 'Rate Quarterly':
                            $cost = (($totalDuration * 0.000456) * $actionlog->rate_cost);
                            break;
                        case 'Rate Half Yearly':
                            $cost = (($totalDuration * 0.000228) * $actionlog->rate_cost);
                            break;
                        case 'Rate Yearly':
                            $cost = (($totalDuration * 0.000114) * $actionlog->rate_cost);
                            break;
                        default:
                            $cost = null;
                            break;
                    }
                }
            }

            $date = isset($data['checkin_at']) && $data['checkin_at'] ? $data['checkin_at'] . ' ' . date('H:i:s') : Carbon::now();
            $billingunit = DB::table('checkin_checkout_logs')->where('asset_id', $id)->whereNull('checkin_at')->update(['checkin_at' => $date, 'cost' => isset($cost) ? $cost : null]);
            $checkinAttachment = null;
            if ($request->file('checkin_attachment')) {
                $uploaded_img = $request->file('checkin_attachment');
                $attachment = Str::random(12) . Str::random(12) . '.' . $uploaded_img->getClientOriginalExtension();
                $path = public_path('uploads/documents/' . $attachment);
                // Image::make($uploaded_img->getRealPath())->resize(300, null, function ($constraint) {
                //     $constraint->aspectRatio();
                //     $constraint->upsize();
                // })->save($path);
                $manager = new ImageManager(new Driver());
                $image = $manager->read($uploaded_img->getRealPath());
                $image->scaleDown(width: 300);
                $image->save($path);
                $checkinAttachment = $attachment;
            }
            $log = Actionlog::deviceCheckin([
                'checkedout_to' => $assigned_to,
                'project_id' => $last_checkout_project,
                'asset_id' => $id,
                'note' => $data['note'],
                'user_id' => Auth::user()->id,
                'assigned_for' => $assigned_for,
                'checkin_attachment' => $checkinAttachment,
                'in_out_id' => $dev->chkout_log_id,
            ], $request->input('checkin_at', ''));

            $logEntry = Actionlog::find($dev->chkout_log_id);
            if (!empty($logEntry)) {
                $originalCreatedAt = $logEntry->created_at;
                DB::table('asset_logs')->where('id', $dev->chkout_log_id)->update(['created_at' => $originalCreatedAt, 'in_out_id' => $log->id]);
            }

            $dev->chkin_log_id = $log->id;
            $dev->save();

            $settings = Settings::first();
            $alertnotify = (Settings::first()->alerts_enabled == 1) ? CommonHelper::getGlobalAlertEmail() : [];
            $notification = NotificationConfig::first();
            if ($notification->device_checkin == 1) {
                if ($assigned_for != 2 && config('mail.service_enabled') && $dev->checkinEmail()) {
                    if (isset($assignedUser) && $assignedUser->email) {
                        if (!empty($alertnotify)) {
                            Mail::to($assignedUser->email)->cc($alertnotify)->queue(new DeviceCheckinNotification($dev, $log, $assignedUser));
                        } else {
                            Mail::to($assignedUser->email)->queue(new DeviceCheckinNotification($dev, $log, $assignedUser));
                        }
                    } elseif ($alertnotify) {
                        Mail::to($alertnotify)->queue(new DeviceCheckinNotification($dev, $log, $assignedUser));
                    }
                }
            }

            /** send push notification */
            $notify_people = [];
            $assignedByUser = User::find(Auth::user()->id);
            if ($notification->device_checkin == 1 && $assigned_for != 2) {
                if (!empty($assignedUser)) {
                    array_push($notify_people, $assignedUser->id);
                    $notificationText = 'Device ' . $dev->asset_tag . ' is unallocated to you.';
                    $assignedUser->device_id = $id;
                    $assignedUser->module_type = 'device';
                    $data = [
                        'title' => $notificationText,
                        'data' => $assignedUser,
                        'notify' => $notify_people,
                    ];

                    $sendNotifications = CommonHelper::sendPushNotification($data);
                    if ($sendNotifications != false) {
                        $response = json_decode($sendNotifications);
                        if (isset($response->failure) && $response->failure == 1) {
                            Log::error('device checkout push notification:' . $assignedByUser->id . ' error ' . json_encode($response));
                        }
                    }
                }
            }
            if (config('app.socket_enabled')) {
                CommonHelper::sendDeviceCountToSocket();
            }

            $return['msg'] = trans('content.device_fields.Device_checked_in_successfully');
            $return['status'] = 'success';
        }

        return response()->json($return);
    }

    /* get model by manufacturer id based */
    public function getModelByManufacturer(Request $request, $manuf_id)
    {
        $return = array();
        try {
            $db = DB::table('models')->select('id', DB::raw("CONCAT_WS('', name, NULLIF(CONCAT('/', modelno), '/')) as text"));
            $db->whereNull('deleted_at');
            $db->where('manufacturer_id', '=', $manuf_id);
            $count = $db->count();
            $result = $db->get();
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
        }
        return response()->json($return);
    }

    /* get model by Category id based */
    public function getModelByCategory(Request $request)
    {
        // return $request;
        $return = array();
        try {
            $search = $request->input('term', '') ? $request->input('term', '') : $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);
            $db = DB::table('models')->select('models.id as id', DB::raw("CONCAT_WS('', models.name, NULLIF(CONCAT('/', models.modelno), '/')) as text"));
            $db->leftJoin('categories as c', 'c.id', '=', 'models.category_id');
            $db->whereNull('models.deleted_at');
            $db->where('c.category_type', 'asset');
            if (isset($request->cat_id) && $request->cat_id != null) {
                if (is_array($request->cat_id)) {
                    $db->whereIn('category_id', $request->cat_id);
                } else {
                    $db->where('category_id', $request->cat_id);
                }
            }
            if ($search) {
                // $db->whereRaw("name like '%" . $search . "%'");
                $db->whereRaw("CONCAT_WS('', models.name, NULLIF(CONCAT('/', models.modelno), '/')) like '%" . $search . "%'");
            }

            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
        }
        return response()->json($return);
    }

    /* get device by model */
    public function getDeviceByModel(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('term', '') ? $request->input('term', '') : $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);
            $db = DB::table('assets as a');
            $db->leftJoin('status_labels as s', 'a.status_id', '=', 's.id');
            $db->select('a.id', 'a.asset_tag', 'a.name as asset_name', 'a.serial', DB::raw("concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) as text"));
            $db->whereNull('a.deleted_at');
            $db->whereNull('s.sold');
            $db->whereNull('s.stolen_item');
            if (isset($request->model_id)) {
                $db->where('a.model_id', '=', $request->model_id);
            }
            if ($search) {
                $whereStr = sprintf('(a.asset_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or concat_ws("-",a.asset_tag,concat_ws("" ,a.name)) like "%%%1$s%%")', $search);
                $db->whereRaw($whereStr);
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
        }
        return response()->json($return);
    }

    public function getDeviceByCategory(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('term', '') ? $request->input('term', '') : $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);
            $db = DB::table('assets as a');
            $db->leftJoin('status_labels as s', 'a.status_id', '=', 's.id');
            $db->leftJoin('models as m', 'm.id', '=', 'a.model_id');
            $db->leftJoin('categories as c', 'c.id', '=', 'm.category_id');
            $db->select('a.id', 'a.asset_tag', 'a.name as asset_name', 'a.serial', 'c.name', DB::raw("concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) as text"));
            $db->whereNull('a.deleted_at');
            $db->whereNull('s.sold');
            $db->whereNull('s.stolen_item');
            $db->where('c.id', '=', $request->category_id);
            if ($search) {
                $whereStr = sprintf('(a.asset_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or concat_ws("-",a.asset_tag,concat_ws("" ,a.name)) like "%%%1$s%%")', $search);
                $db->whereRaw($whereStr);
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error('getDeviceByCategory Error: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getModelByQuery(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('search', '');
            $term = $request->input('term', '');
            if ($term != null) {
                $search = $term;
            }
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);

            $db = DB::table('models')->select('models.id as id', DB::raw("CONCAT_WS('', models.name, NULLIF(CONCAT('/', models.modelno), '/')) as text"));
            $db->leftJoin('categories as c', 'c.id', '=', 'models.category_id');
            $db->whereNull('models.deleted_at');
            $db->where('c.category_type', 'asset');
            if ($search) {
                $db->whereRaw("CONCAT_WS('', models.name, NULLIF(CONCAT('/', models.modelno), '/')) like '%" . $search . "%'");
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error('getModelByQuery Error: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getDeviceByQuery(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);

            $db = DB::table('assets as a');
            $db->leftJoin('models as mdl', 'a.model_id', '=', 'mdl.id');

            if (!Auth::user()->isSuperUser()) {
                $db->where('a.company_id', '=', Auth::user()->company_id);
            }

            $db->select('a.id', 'a.asset_tag', 'mdl.name', 'mdl.modelno', DB::raw("concat_ws('-', a.asset_tag, concat_ws(' ', mdl.name, mdl.modelno)) as text"));
            $db->whereNull('a.deleted_at');
            if ($search) {
                $db->whereRaw("(concat_ws('-', a.asset_tag, concat_ws(' ', mdl.name, mdl.modelno)) like '%" . $search . "%' or a.asset_tag like '%" . $search . "%' or mdl.name like '%" . $search . "%' or mdl.modelno like '%" . $search . "%')");
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
        }
        return response()->json($return);
    }

    public function getDeviceByStatus(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);

            $db = DB::table('assets as a');
            $db->leftJoin('models as mdl', 'a.model_id', '=', 'mdl.id');
            $db->leftJoin('status_labels as s', 'a.status_id', '=', 's.id');

            if (!Auth::user()->isSuperUser()) {
                $db->where('a.company_id', '=', Auth::user()->company_id);
            }

            $db->select('a.id', 'a.asset_tag', 'mdl.name', 'mdl.modelno', DB::raw("concat_ws('-', a.asset_tag, concat_ws(' ', mdl.name, mdl.modelno)) as text"));
            $db->whereNull('a.deleted_at');
            $db->whereNull('s.sold');
            $db->whereNull('s.stolen_item');
            if ($search) {
                $db->whereRaw("(concat_ws('-', a.asset_tag, concat_ws(' ', mdl.name, mdl.modelno)) like '%" . $search . "%' or a.asset_tag like '%" . $search . "%' or mdl.name like '%" . $search . "%' or mdl.modelno like '%" . $search . "%')");
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
        }
        return response()->json($return);
    }

    public function restoresoldDevice(Request $request, $id)
    {
        $return = array('status' => 'failure', 'msg' => 'Unable to restore the Desposed Device');

        $device = Device::withTrashed()->where('id', '=', $id)->first();
        $oldStatus = $device->status_id;
        if (!$device->exists) {
            return response()->json($return);
        }

        if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($device)) {
            $return['section'] = 'device-checkout';
            $return['msg'] = Auth::user()->company_id == null ? trans('content.device_fields.update_company_name') : trans('content.device_fields.multiple_company_access');
            return response()->json($return);
        }

        if ($device && $device->status_id == 7) {
            $device->status_id = $device->sold_with_status == null ? 1 : $device->sold_with_status;
            $device->sold_with_status = null;
            $device->restore();
            Actionlog::restoreSoldDevice($device, Auth::user()->id);
            if (config('app.socket_enabled')) {
                CommonHelper::sendDeviceCountToSocket();
            }
            $newStatus = $device->status_id;
            if (in_array(config('app.client'), ['rolepermission', 'knightfrank', 'rashmi'])) {
                if (!empty($oldStatus) || !empty($newStatus)) {
                    CommonHelper::updateStatusCounts($oldStatus, $newStatus, $device);
                }
            }
            $return['msg'] = trans('content.device_fields.sold_out');
            $return['status'] = 'success';
        } else {
            $return['msg'] = trans('device.device_fields.already_sold_out');
            $return['status'] = 'error';
        }

        return response()->json($return);
    }

    public function restoreDevice(Request $request, $id)
    {
        $return = array('status' => 'failure', 'msg' => 'Unable to restore the Device');
        if (!Auth::user()->hasPermissionTo('DeviceRestore') || !config('services.assets.enabled')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $devicecount = Device::whereNotIn('status_id', [7])->count();
        if ($devicecount > config('services.assets.asset_limit')) {
            $return['msg'] = "You don't have permission to insert more than " . config('services.assets.asset_limit') . ' devices. Please contact Admin';
            return response()->json($return);
        }
        $device = Device::withTrashed()->where('id', '=', $id)->first();
        if (!$device->exists) {
            return response()->json($return);
        }

        if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($device)) {
            // $return["status"] = 'error';
            $return['section'] = 'device-checkout';
            $return['msg'] = Auth::user()->company_id == null ? trans('content.device_fields.update_company_name') : trans('content.device_fields.multiple_company_access');
            return response()->json($return);
        }

        // if(! Company::checkUserAccess($device)) {
        //     $return["msg"] = "Insufficient Permission to restore the device.";
        //     return response()->json($return);
        // }

        if ($device->restore()) {
            if (in_array(config('app.client'), ['rolepermission', 'knightfrank', 'rashmi'])) {
                $deviceNew = Device::where('id', $id)->first();
                $newStatus = $deviceNew->status_id;
                if (!empty($newStatus)) {
                    CommonHelper::updateStatusCounts(null, $newStatus, $deviceNew);
                }
            }
        }
        $getGroup = PatchManagementGroup::where('auto_added_new_device', 1)->select('id', 'auto_added_new_device')->get();
        foreach ($getGroup as $key => $value) {
            // Add Devices to the Group
            PatchManagementGroupDevice::insert([
                'group_id' => $value->id,
                'device_id' => $device->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        Actionlog::deviceRestored($device, Auth::user()->id);
        if (config('app.socket_enabled')) {
            CommonHelper::sendDeviceCountToSocket();
        }
        $return['msg'] = trans('content.device_fields.device_restored');
        $return['status'] = 'success';
        return response()->json($return);
    }

    public function acceptCheckout(Request $request, $id)
    {
        $log = DeviceLog::where('id', '=', $id)->first();
        if (!$log->exists) {
            return redirect('dashboard')->with('status', 'Invalid Access');
        }

        if (Auth::user()->id != $log->checkedout_to) {
            return redirect('dashboard')->with('status', 'Invalid Access');
        }

        $item = false;
        if ($log->asset_id && $log->asset_type == 'hardware') {
            $item = Device::where('id', '=', $log->asset_id)->first();
        }

        if (!$item->exists) {
            return redirect('dashboard')->with('status', 'Device does not exist.');
        }

        // needs to redirect requestable page if item present but permission no
        if ($request->method() == 'POST') {
            if ($log->accepted_id) {
                return redirect('dashboard')->with('status', 'Device has been already accepted.');
            }

            if (!$request->has('confirm')) {
                return view('devices.checkout_confirm')->withLog($log)->withDevice($item)->withStatus('Please choose correct option');
            }

            $confirmation = $request->input('confirm');

            $confLog = new ActionLog();
            $confLog->asset_id = $log->asset_id;
            $confLog->accessory_id = null;
            $confLog->asset_type = 'hardware';

            $confirmation_msg = 'accepted';
            $notification_msg = 'You have successfully accepted this device';
            if (!$confirmation) {
                $item->assigned_to = null;
                $item->save();
                $confirmation_msg = 'declined';
                $notification_msg = 'You have successfully declined this device';
            }

            $confLog->checkedout_to = $log->checkedout_to;
            $confLog->user_id = Auth::user()->id;
            $confLog->accepted_at = date('Y-m-d h:i:s');
            $confLog->action_type = $confirmation_msg;
            $confLog->save();

            // update log
            $log->accepted_id = $confLog->id;
            $log->save();

            return redirect('dashboard')->with('status', $notification_msg);
        }

        return view('devices.checkout_confirm')->withLog($log)->withDevice($item);
    }

    public function viewInfo(Request $request, $id)
    {
        $return = array('status' => 'danger', 'msg' => 'Device not found');
        if (!Auth::user()->hasPermissionTo('DeviceView') || !config('services.assets.enabled')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with('msg', $return);
        }
        $device = Device::with('location.country')->where('id', '=', $id)->first();
        $transfer_status = DB::table('transfer_items')->where('device_id', $id)->value('transfer_status');
        if (!$device || !$device->exists) {
            return redirect('devices')->with('status', 'Device not found');
        }
        $userLocationAccessCheck = User::where('id', Auth::user()->id)->where('permitted_locations', 'like', '%' . $device->rtd_location_id . '%')->first();
        $settings = Settings::getSettings();
        if ($settings->location_config == 1 && empty($userLocationAccessCheck)) {
            $return['msg'] = "You don't have device location access";
            return redirect('dashboard')->with('msg', $return);
        }
        $companies = Company::select('id', 'name as text')->get()->toArray();
        $labels = Label::whereNull('deleted_at')->wherenull('sold')->orderBy('name')->select('id', 'name as text')->get()->toArray();
        $deviceMain = new AssetExpense();
        $deviceCountry = Country::where('country_code', '=', optional($device->location)->country)->first();
        $currencies = Currency::getCurrencies();
        $countryName = optional($deviceCountry)->name;
        $deviceStatus = Device::select('status_id')->where('id', $id)->first();
        $network = Basic::where('BIOSSerialNumber', '=', ($device->serial))->whereNull('is_dupe')->whereNull('is_virtual')->select('id', 'BIOSSerialNumber', 'agentVersion', 'is_AD')->first();
        $getRdpData = [];
        $itmAgentInstalled = false;
        if (!empty($network->id)) {
            $getRdpData = Basic::getRdpData($network->id);
            $product = Product::where('basic_id', $network->id)->where('Caption', 'ITM Agent')->first();
            $itmAgentInstalled = empty($product) ? false : true;
        }
        $getDevice = Device::where('assets.id', $device->id)
            ->leftJoin('models as mdl', 'mdl.id', '=', 'assets.model_id')
            ->select('assets.id', DB::raw("concat_ws(' ', assets.asset_tag, '(', mdl.name, mdl.modelno, ')') as text"))
            ->first();

        $dropdown = ['device' => $getDevice->toArray()];
        $audit_device = ['device_id' => $getDevice->toArray()];
        $isCheckinCall = ($request->checkin == true && $device->assigned_to);

        $deployedLabel = Label::getDeployedLabel()->id;
        $firstDeployable = Label::getFirstDeployable();
        $vd = new stdClass;
        $vd->places = Place::selectOptions();
        $vd->project = Project::select('id', 'name as text')->orderBy('name')->get();
        $vd->status = AssetAllocationType::select('id', 'name as text')->orderBy('name')->get();
        $vd->assetType = AssetType::select('id', 'name as text')->orderBy('name')->get();
        $vd->assignedForOptions = Device::getAssignedForOptions();
        $vd->ratingsOptions = DeviceAudit::getRatingsOptions();
        $vd->internal_places = Place::select('id', 'place as text')->orderBy('place')->get();
        $policies = SoftwareDeploymentPolicy::select('id', 'policy')->get();
        $vd->trackedLocations = [];
        if ($device->trackedDevices->count()) {
            $tracks = TrackedDevice::where('device_id', $device->id)->distinct()->orderBy('created_at', 'desc')->get();
            foreach ($tracks as $td) {
                $vd->trackedLocations[] = [
                    'at' => CommonHelper::getDateAs($td->created_at, 'd/m/Y h:i a', 'Y-m-d H:i:s'),
                    'msg' => $td->formAsMessage()
                ];
            }
        }
        $vd->scheduleMaintenanceStatus = ScheduleMaintenanceStatus::where('is_enabled', 1)->get();
        return view('devices.info')
            ->with(compact('device', 'companies', 'deviceMain', 'dropdown', 'audit_device', 'network', 'itmAgentInstalled'))
            ->with('statusLabels', $labels)
            ->with('isCheckinCall', $isCheckinCall)
            ->with('locationName', optional($device->location)->name)
            ->with('countryName', $countryName)
            ->with('currencies', $currencies)
            ->with('deployedLabel', $deployedLabel)
            ->with('firstDeployable', $firstDeployable)
            ->with('settings', $settings)
            ->with('deviceStatus', $deviceStatus)
            ->with('getRdpData', $getRdpData)
            ->with('policies', $policies)
            // ->with("rdpAllData", $rdpAllData)
            // ->with("rdpPower",  $rdpPower)
            ->with('vd', $vd)
            ->with($this->buildInfoTabData($id))
            ->with('transfer_status', $transfer_status);
    }

    public function viewInfoTab(Request $request, $id)
    {
        $data = $this->buildInfoTabData($id);

        $return = array_merge(
            $data['return'], ['html' => view('devices.info_tab', $data)->render()]
        );

        return response()->json($return);
    }

    public function ajaxHistory(Request $request)
    {
        $req = $request->all();
        $return = array(
            'draw' => $req['draw']
        );

        $fields = array(
            'id' => 'al.id',
            'date' => 'al.updated_at',
            'admin' => 'adm_full_name',
            'action_type' => 'action_type',
            'user_place' => 'full_name',
            'project_name' => 'project_name',
            'checkinout_reason' => 'checkinout_reason',
            'notes' => 'note'
        );

        $db = DB::table('asset_logs as al');
        $db->leftJoin('users as u', 'u.id', '=', 'al.checkedout_to');
        $db->leftJoin('users as adm', 'adm.id', '=', 'al.user_id');
        // $db->leftJoin('users as dis_by', 'dis_by.id', '=', 'al.user_id');
        $db->leftJoin('checkout_acceptance_histories as cah', 'cah.chkout_acceptance_log_id', '=', 'al.id');
        $db->leftJoin('places as p', function ($q) {
            $q->on('p.id', '=', 'al.checkedout_to');
            $q->where('al.assigned_for', '=', 2);
        });
        $db->leftJoin('locations as place_loc', function ($q) {
            $q->on('place_loc.id', '=', 'p.location_id');
            $q->where('al.assigned_for', '=', 2);
        });

        $db->leftJoin('locations as stock_loc', function ($q) {
            $q->on('stock_loc.id', '=', 'p.location_id');
            $q->where('al.assigned_for', '=', 2);
        });
        $db->where('al.asset_type', '=', 'hardware');
        $db->whereNull('al.filename');
        if (isset($req['device_id']) && $req['device_id']) {
            $db->where('al.asset_id', '=', $req['device_id']);
        }
        $db->leftJoin('projects as pr', 'pr.id', '=', 'al.project_id');
        $db->select('al.id', DB::raw('CONCAT(UPPER(SUBSTRING(al.action_type, 1, 1)), LOWER(SUBSTRING(al.action_type, 2))) as action_type'), 'al.interact_id', 'al.interact_type', 'al.interact_module', 'al.assigned_for', 'al.note', 'u.id as enduserid', 'adm.id as adminuserid', 'pr.name as project_name', 'u.username', 'cah.accepted_via');
        $db->addSelect(DB::raw('case when al.assigned_for = 2 then concat(p.place, " - ", place_loc.name) else concat(u.first_name, " ", u.last_name) end as full_name'));
        $db->addSelect(DB::raw('concat(adm.first_name, " ", adm.last_name) as adm_full_name'));
        // $db->addSelect(DB::raw('concat(dis_by.first_name, " ", dis_by.last_name) as dis_by_full_name'));
        $mysqlDateTimeFormat = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(al.updated_at, '{$mysqlDateTimeFormat}') as created_at_format"));
        $db->leftJoin('asset_inout_reason as aior', 'aior.id', '=', 'al.reason_id');
        $db->addSelect('aior.name as checkinout_reason');

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(pr.name like "%%%1$s%%" or al.action_type like "%%%1$s%%" or al.note like "%%%1$s%%" or case when al.assigned_for = 2 then concat(p.place, " - ", place_loc.name) else concat(u.first_name, " ", u.last_name) end like "%%%1$s%%" or concat(adm.first_name, " ", adm.last_name) like "%%%1$s%%" or DATE_FORMAT(al.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['sorted_column_name']) && array_key_exists($req['sorted_column_name'], $fields) && in_array($req['sorted_direction'], ["asc", "desc"])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        } else {
            $db->orderBy('al.id', 'desc');
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
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

    public function ajaxAssignedAccessories(Request $request)
    {
        $req = $request->all();
        $return = array(
            'draw' => $req['draw']
        );

        $fields = array(
            'accessory' => 'acc.name',
            'category' => 'cat.name',
            'cost' => 'acc.purchase_cost',
            'order_number' => 'acc.order_number',
            'checkout_date' => 'au.created_at',
            'checkin_date' => 'au.expected_checkin'
        );

        $db = DB::table('accessories as acc');
        $db->leftJoin('accessories_users as au', 'au.accessory_id', '=', 'acc.id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'acc.category_id');
        $db->select('au.id', 'acc.id as accessory_id', 'acc.name', 'cat.name as cat_name', 'acc.order_number', 'acc.batch_no', 'acc.purchase_cost as cost');
        $mysqlDateTimeFormat = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $mysqlDateFormat = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(au.created_at, '{$mysqlDateTimeFormat}') as checkout_at"));
        if (config('app.client') == 'etherealmachines') {
            $db->addSelect(DB::raw('concat("AC",acc.id) as acc_tag'));
        } else {
            $db->addSelect(DB::raw('concat("A",acc.id) as acc_tag'));
        }
        $db->addSelect(DB::raw("DATE_FORMAT(au.expected_checkin, '{$mysqlDateFormat}') as expected_checkin_at"));

        if (isset($req['device_id']) && $req['device_id']) {
            $db->where('au.assigned_to', '=', $req['device_id']);
        } else {
            $db->whereNull('acc.id');
        }
        $db->where('assigned_for', '=', 3);

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            if (config('app.client') == 'etherealmachines') {
                $whereStr = sprintf('(concat("AC",acc.id) like "%%%1$s%%" or acc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or acc.order_number like "%%%1$s%%" or DATE_FORMAT(au.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(au.expected_checkin, "%%d %%b %%Y") like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('(concat("A",acc.id) like "%%%1$s%%" or acc.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or acc.order_number like "%%%1$s%%" or DATE_FORMAT(au.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(au.expected_checkin, "%%d %%b %%Y") like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['sorted_column_name']) && array_key_exists($req['sorted_column_name'], $fields) && in_array($req['sorted_direction'], ["asc", "desc"])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
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

    public function ajaxAssignedComponents(Request $request)
    {
        $req = $request->all();
        $return = array(
            'draw' => $req['draw']
        );

        $fields = array(
            'component' => 'c.name',
            'category' => 'ca.name',
            'checkout_date' => 'c.checked_out_at',
            'checkin_date' => 'c.expected_checkin_at',
            'cost' => 'cost'
        );

        $db = DB::table('components as c');
        $db->leftJoin('categories as ca', 'ca.id', '=', 'c.category_id');

        $db->select('c.id', 'c.id as component_id', 'c.name', 'ca.name as co_cat_name', 'c.checked_out_to', 'c.unique_tag', 'c.purchase_cost as cost');
        $mysqlDateTimeFormat = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $mysqlDateFormat = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(c.checked_out_at, '{$mysqlDateTimeFormat}') as checkout_date"));
        $db->addSelect(DB::raw("DATE_FORMAT(c.expected_checkin_at, '{$mysqlDateFormat}') as checkin_date"));

        if (isset($req['device_id']) && $req['device_id']) {
            $db->where('c.checked_out_to', '=', $req['device_id']);
        }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(c.name like "%%%1$s%%" or c.unique_tag like "%%%1$s%%" or ca.name like "%%%1$s%%" or DATE_FORMAT(c.checked_out_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(c.expected_checkin_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['sorted_column_name']) && array_key_exists($req['sorted_column_name'], $fields) && in_array($req['sorted_direction'], ["asc", "desc"])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
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

    public function ajaxAssignedTickets(Request $request)
    {
        $req = $request->all();
        $return = array(
            'draw' => date('is')
        );

        $fields = array(
            '0' => 't.id',
            '1' => 't.subject',
            '2' => 'status',
            '3' => 'created_by_name',
            '4' => 'assigned_to_name',
            '5' => 't.created_at',
            '6' => 't.updated_at',
            '7' => 'resolved_at_format',
            '8' => 'closed_at_format',
        );

        $db = DB::table('tkt_tickets as t');
        $db->leftJoin('tkt_statuses as s', 't.status_id', '=', 's.id');
        $db->leftJoin('users as u', 't.creator_id', '=', 'u.id');  // ticket raiser
        $db->leftJoin('users as cu', 't.created_by', '=', 'cu.id');  // who actually created ticket
        $db->leftJoin('users as ta', 't.assigned_to', '=', 'ta.id');  // to whom ticket getting assigned

        $db->select('t.id', 't.subject', 's.name as status');
        $db->addSelect(DB::raw('concat(u.first_name, " ", u.last_name, " @ ", u.username) as creator_name'));
        $db->addSelect(DB::raw('concat(cu.first_name, " ", cu.last_name, " @ ", cu.username) as created_by_name'));
        $db->addSelect(DB::raw('case when t.assigned_to is not null then concat(ta.first_name, " ", ta.last_name, " @ ", ta.username) else "" end as assigned_to_name'));
        $db->addSelect(DB::raw('DATE_FORMAT(t.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(t.created_at, "%d %b %Y %h:%i %p") as created_at_format'));
        $db->addSelect(DB::raw('case when t.status_id in (5,6) then DATE_FORMAT(t.resolved_at, "%d %b %Y %h:%i %p") else "" end as resolved_at_format'));
        $db->addSelect(DB::raw('case when t.status_id = 6 then DATE_FORMAT(t.closed_at, "%d %b %Y %h:%i %p") else "" end as closed_at_format'));

        if (isset($req['device_id']) && $req['device_id']) {
            $db->where('t.device_id', '=', $req['device_id']);
        }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(t.id like "%%%1$s%%" or t.subject like "%%%1$s%%" or s.name like "%%%1$s%%" or concat_ws(" ", u.first_name, u.last_name, "@", u.username) like "%%%1$s%%" or concat_ws(" ", cu.first_name, cu.last_name, "@", cu.username) like "%%%1$s%%" or concat_ws(" ", ta.first_name, ta.last_name, "@", ta.username) like "%%%1$s%%" or (case when t.status_id in (5,6) then DATE_FORMAT(t.resolved_at, "%%d %%b %%Y %%h:%%i %%p") else "" end) like "%%%1$s%%" or (case when t.assigned_to is not null then concat(ta.first_name, " ", ta.last_name, " @ ", ta.username) else "" end) like "%%%1$s%%" or (case when t.status_id = 6 then DATE_FORMAT(t.closed_at, "%%d %%b %%Y %%h:%%i %%p") else "" end) like "%%%1$s%%" or DATE_FORMAT(t.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(t.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(t.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['order'][0]['column']) && isset($fields[$req['order'][0]['column']]) && in_array($req['order'][0]['dir'], ['asc', 'desc'])) {
            $db->orderBy($fields[$req['order'][0]['column']], $req['order'][0]['dir']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
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

    public function ajaxAssignedLicenses(Request $request)
    {
        $req = $request->all();
        $return = array(
            'draw' => $req['draw']
        );

        $fields = array(
            'license' => 'l.name',
            'serial_no' => 'serial_no',
            'cost' => 'cost'
        );

        $db = DB::table('license_seats as ls');
        $db->join('licenses as l', function ($q) {
            $q->on('l.id', '=', 'ls.license_id');
            $q->whereNull('l.deleted_at');
        });
        if (isset($req['device_id']) && $req['device_id']) {
            $db->where('ls.asset_id', '=', $req['device_id']);
        }
        $db->whereNull('ls.deleted_at');
        $db->select('l.name', 'ls.id', 'l.serial', 'ls.license_id', 'ls.cost');
        $db->addSelect(DB::raw('concat("LIC", l.id) as lic_batch_no'));
        $db->addSelect(DB::raw('case when ls.serial is not null then ls.serial when ls.serial is null then l.serial else "" end as serial_no'));

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(l.name like "%%%1$s%%" or (case when ls.serial is not null then ls.serial when ls.serial is null then l.serial else "" end) like "%%%1$s%%" or concat("LIC", l.id) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['sorted_column_name']) && array_key_exists($req['sorted_column_name'], $fields) && in_array($req['sorted_direction'], ["asc", "desc"])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
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

    public function ajaxAssignedConsumable(Request $request)
    {
        $req = $request->all();

        $return = array(
            'draw' => $req['draw']
        );

        $fields = array(
            'consumable' => 'con.name',
            'category' => 'cat.name',
            'batch' => 'con.id',
            'checkout_date' => 'cu.created_at',
            'cost' => 'con.purchase_cost',
        );

        $db = DB::table('consumables as con');
        $db->leftJoin('consumables_users as cu', 'cu.consumable_id', '=', 'con.id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'con.category_id');
        $db->select('cu.id', 'con.id as consumable_id', 'con.name', 'cat.name as cat_name', 'con.purchase_cost as cost');
        $db->addSelect(DB::raw('DATE_FORMAT(cu.created_at, "%d %b %Y %h:%i %p") as checkout_at'));
        if (config('app.client') == 'etherealmachines') {
            $db->addSelect(DB::raw('concat("CN",con.id) as con_tag'));
        } else {
            $db->addSelect(DB::raw('concat("CNS",con.id) as con_tag'));
        }
        $mysqlDateFormat = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(cu.created_at, '{$mysqlDateFormat}') as checkout_at"));

        if (isset($req['device_id']) && $req['device_id']) {
            $db->where('cu.assigned_to', '=', $req['device_id']);
        } else {
            $db->whereNull('con.id');
        }
        $db->where('assigned_for', '=', 3);

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            if (config('app.client') == 'etherealmachines') {
                $whereStr = sprintf('(concat("CN",con.id) like "%%%1$s%%" or con.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or DATE_FORMAT(cu.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('(concat("CNS",con.id) like "%%%1$s%%" or con.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or DATE_FORMAT(cu.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['sorted_column_name']) && array_key_exists($req['sorted_column_name'], $fields) && in_array($req['sorted_direction'], ["asc", "desc"])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
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

    public function qrcode(Request $request, $id)
    {
        $size = CommonHelper::getBarcodeDimensions(Settings::getSettings()->barcode_type);
        $path = url('device/info/' . $id);
        if (config('app.client') == 'tradekings') {
            $path = url('device_public/' . $id);
        }
        $barcode = new \Com\Tecnick\Barcode\Barcode();
        $barcode_obj = $barcode->getBarcodeObj(Settings::getSettings()->barcode_type, $path, $size['height'], $size['width'], 'black', array(-1, -1, -1, -1));
        return $barcode_obj->getPngData();
    }

    public function printBarcode(Request $request, $option, $ids, $user_info = 1)
    {
        $return = array('status' => 'danger', 'msg' => 'Device not found');
        if (!Auth::user()->hasPermissionTo('DevicePrintLabel')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with('msg', $return);
        }
        $settings = Settings::getSettings();
        $data_string = base64_decode($ids);

        // data collection to be passed to view portion
        $view_data = array(
            'qr_text' => $settings->qr_text,
            'devices' => array(),
            'devicescount' => 0
        );

        // check the asset list found or not
        if (!$ids || !$data_string) {
            return redirect('devices')->with('error', 'Please select any device to print');
        }

        // check the barcode is enabled on settings
        if ($settings->qr_code != '1') {
            return redirect('devices')->with('error', 'Barcode is disabled.');
        }

        $data = explode(',', $data_string);

        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }

        $view_data['devices'] = Device::find($data);
        $view_data['devicescount'] = count($view_data['devices']);
        $view_data['option'] = $option;
        $view_data['user_info'] = $user_info;

        if (config('app.client') == 'tbsl') {
            return view('devices/clients/tbsl/print_barcode3')->with('data', $view_data);
        } elseif (config('app.client') == 'icicibank') {
            return view('devices/clients/icicibank/print_barcode3')->with('data', $view_data);
        } elseif (config('app.client') == 'airasia') {
            return view('devices/clients/airasia/print_barcode3')->with('data', $view_data);
        } elseif (config('app.client') == 'capacite') {
            return view('devices/clients/capacite/print_barcode3')->with('data', $view_data);
        } elseif (config('app.client') == 'ltsct') {
            return view('devices/clients/ltsct/print_barcode3')->with('data', $view_data);
        } elseif (config('app.client') == 'safari') {
            return view('devices/clients/safari/print_barcode3')->with('data', $view_data);
        } elseif (config('app.client') == 'knightfrank') {
            return view('devices/clients/knightfrank/print_barcode3')->with('data', $view_data);
        } elseif (config('app.client') == 'shyammetalics') {
            return view('devices/clients/shyammetalics/print_barcode3')->with('data', $view_data);
        } elseif (config('app.client') == 'mgmotor') {
            return view('devices/clients/mgmotor/print_barcode3')->with('data', $view_data);
        }
        return view('devices/print_barcode3')->with('data', $view_data);
    }

    public function printBarcodeOneCol(Request $request, $option, $ids, $user_info = 1)
    {
        $return = array('status' => 'danger', 'msg' => 'Device not found');
        if (!Auth::user()->hasPermissionTo('DevicePrintLabel')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with('msg', $return);
        }
        $settings = Settings::getSettings();
        $data_string = base64_decode($ids);

        // data collection to be passed to view portion
        $view_data = array(
            'qr_text' => $settings->qr_text,
            'devices' => array(),
            'devicescount' => 0
        );

        // check the asset list found or not
        if (!$ids || !$data_string) {
            return redirect('devices')->with('error', 'Please select any device to print');
        }

        // check the barcode is enabled on settings
        if ($settings->qr_code != '1') {
            return redirect('devices')->with('error', 'Barcode is disabled.');
        }

        $data = explode(',', $data_string);

        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }

        $view_data['devices'] = Device::find($data);
        $view_data['devicescount'] = count($view_data['devices']);
        $view_data['option'] = $option;
        $view_data['user_info'] = $user_info;
        if (config('app.client') == 'tbsl') {
            return view('devices/clients/tbsl/print_barcode_one_col')->with('data', $view_data);
        } elseif (config('app.client') == 'icicibank') {
            return view('devices/clients/icicibank/print_barcode_one_col')->with('data', $view_data);
        } elseif (config('app.client') == 'airasia') {
            return view('devices/clients/airasia/print_barcode_one_col')->with('data', $view_data);
        } elseif (config('app.client') == 'knightfrank') {
            return view('devices/clients/knightfrank/print_barcode_one_col')->with('data', $view_data);
        } elseif (config('app.client') == 'shyammetalics') {
            return view('devices/clients/shyammetalics/print_barcode_one_col')->with('data', $view_data);
        } elseif (config('app.client') == 'mgmotor') {
            return view('devices/clients/mgmotor/print_barcode_one_col')->with('data', $view_data);
        }
        return view('devices/print_barcode_one_col')->with('data', $view_data);
    }

    public function printBarcodeTwoCol(Request $request, $option, $ids, $user_info = 1)
    {
        $return = array('status' => 'danger', 'msg' => 'Device not found');
        if (!Auth::user()->hasPermissionTo('DevicePrintLabel')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with('msg', $return);
        }
        $settings = Settings::getSettings();
        $data_string = base64_decode($ids);

        // data collection to be passed to view portion
        $view_data = array(
            'qr_text' => $settings->qr_text,
            'devices' => array(),
            'devicescount' => 0
        );

        // check the asset list found or not
        if (!$ids || !$data_string) {
            return redirect('devices')->with('error', 'Please select any device to print');
        }

        // check the barcode is enabled on settings
        if ($settings->qr_code != '1') {
            return redirect('devices')->with('error', 'Barcode is disabled.');
        }

        $data = explode(',', $data_string);

        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }

        $view_data['devices'] = Device::find($data);
        $view_data['devicescount'] = count($view_data['devices']);
        $view_data['option'] = $option;
        $view_data['user_info'] = $user_info;

        if (config('app.client') == 'tbsl') {
            return view('devices/clients/tbsl/print_barcode2')->with('data', $view_data);
        } elseif (config('app.client') == 'icicibank') {
            return view('devices/clients/icicibank/print_barcode2')->with('data', $view_data);
        } elseif (config('app.client') == 'airasia') {
            return view('devices/clients/airasia/print_barcode2')->with('data', $view_data);
        } elseif (config('app.client') == 'knightfrank') {
            return view('devices/clients/knightfrank/print_barcode2')->with('data', $view_data);
        } elseif (config('app.client') == 'shyammetalics') {
            return view('devices/clients/shyammetalics/print_barcode2')->with('data', $view_data);
        } elseif (config('app.client') == 'mgmotor') {
            return view('devices/clients/mgmotor/print_barcode2')->with('data', $view_data);
        }
        return view('devices/print_barcode2')->with('data', $view_data);
    }

    public function printVerticalCol(Request $request, $option, $ids, $user_info = 1)
    {
        $return = array('status' => 'danger', 'msg' => 'Device not found');
        if (!Auth::user()->hasPermissionTo('DevicePrintLabel')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with('msg', $return);
        }
        $settings = Settings::getSettings();
        $data_string = base64_decode($ids);
        $view_data = array(
            'qr_text' => $settings->qr_text,
            'devices' => array(),
            'devicescount' => 0
        );

        if (!$ids || !$data_string) {
            return redirect('devices')->with('error', 'Please select any device to print');
        }

        if ($settings->qr_code != '1') {
            return redirect('devices')->with('error', 'Barcode is disabled.');
        }

        $data = explode(',', $data_string);

        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }

        $view_data['devices'] = Device::find($data);
        $view_data['devicescount'] = count($view_data['devices']);
        $view_data['option'] = $option;
        $view_data['user_info'] = $user_info;

        if (config('app.client') == 'tbsl') {
            return view('devices/clients/tbsl/print_vertical_barcode')->with('data', $view_data);
        }
        if (config('app.client') == 'safari') {
            return view('devices/clients/safari/print_vertical_barcode')->with('data', $view_data);
        }
        if (config('app.client') == 'ltsct') {
            return view('devices/clients/ltsct/print_vertical_barcode')->with('data', $view_data);
        }
        if (config('app.client') == 'iciciback') {
            return view('devices/clients/icicibank/print_vertical_barcode')->with('data', $view_data);
        }
        if (config('app.client') == 'capacite') {
            return view('devices/clients/capacite/print_vertical_barcode')->with('data', $view_data);
        }
        if (config('app.client') == 'airasia') {
            return view('devices/clients/airasia/print_vertical_barcode')->with('data', $view_data);
        }
        if (config('app.client') == 'knightfrank') {
            return view('devices/clients/knightfrank/print_vertical_barcode')->with('data', $view_data);
        }
        if (config('app.client') == 'mgmotor') {
            return view('devices/clients/mgmotor/print_vertical_barcode')->with('data', $view_data);
        }
        return view('devices/print_vertical_barcode')->with('data', $view_data);
    }

    public function requestables(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('RequestableDevicesRead') || !config('services.assets.enabled')) {
            return redirect('dashboard')->with('msg', $return);
        }
        $requestable_enabled = config('app.requestable_enabled');
        return view('devices/requestable')->with('requestable_enabled', $requestable_enabled);
    }

    // to reject the user device request
    public function rejectReq(Request $request)
    {
        $return = ['status' => 'error', 'msg' => 'Request not found'];
        $reflog = Actionlog::find($request->id);
        if (!$reflog) {
            return response()->json($return);
        }

        $device = Device::find($reflog->asset_id);
        if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($device)) {
            $return['section'] = 'reject-device-request';
            $return['msg'] = Auth::user()->company_id == null ? trans('content.device_fields.update_company_name') : trans('content.device_fields.multiple_company_access');
            return response()->json($return);
        }

        $logaction = new Actionlog;
        $logaction->asset_id = $reflog->asset_id;
        $logaction->location_id = $reflog->location_id;
        $logaction->asset_type = 'hardware';
        $logaction->action_type = 'declined';
        $logaction->user_id = Auth::user()->id;
        $logaction->save();

        $reflog->accepted_id = $logaction->id;
        $reflog->save();

        return response()->json(['status' => 'success', 'msg' => 'Request rejected successfully !!']);
    }

    // item requests list for admin user side
    public function ajaxDevReqs(Request $request)
    {
        $req = $request->all();
        $return = array(
            'draw' => date('is')
        );

        $fields = array(
            '1' => 'full_name',
            '2' => 'u.username',
            '3' => 'loc_name',
            '4' => 'al.created_at'
        );

        $db = DB::table('asset_logs as al');
        $db->leftJoin('users as u', 'u.id', '=', 'al.user_id');
        $db->leftJoin('locations as l', 'al.location_id', '=', 'l.id');
        $db->where('al.asset_type', '=', $request->asset_type);
        $db->where('al.action_type', '=', 'requested');
        $db->whereNull('al.accepted_id');
        $db->whereNull('al.deleted_at');

        $db->select('al.id', 'u.id as user_id', 'u.username', 'l.name as loc_name');
        $db->addSelect(DB::raw('concat(u.first_name, " ", u.last_name) as full_name'));
        $db->addSelect(DB::raw('DATE_FORMAT(al.created_at, "%d %b %Y %h:%i %p") as created_at_format'));

        if ($request->asset_type == 'hardware') {
            $db->join('assets as item', 'al.asset_id', '=', 'item.id');
            $db->join('status_labels as lbl', 'item.status_id', '=', 'lbl.id');
            $db->where('al.asset_id', '=', $request->device_id);
            $db->addSelect('item.id as item_id', 'item.name as item_name', 'item.asset_tag');
            $db->addSelect(DB::raw('case when lbl.deployable = 1 and lbl.archived = 0 and item.assigned_to is not null and item.assigned_to > 0 then 2 when lbl.deployable = 1 and lbl.archived = 0 and (item.assigned_to is null or item.assigned_to = 0) then 1 else 0 end as check_action'));
        }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(l.name like "%%%1$s%%" or u.username like "%%%1$s%%" or al.action_type like "%%%1$s%%" or al.note like "%%%1$s%%" or concat(u.first_name, " ", u.last_name) like "%%%1$s%%"  or DATE_FORMAT(al.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['order'][0]['column']) && isset($fields[$req['order'][0]['column']]) && in_array($req['order'][0]['dir'], ['asc', 'desc'])) {
            $db->orderBy($fields[$req['order'][0]['column']], $req['order'][0]['dir']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
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

    public function notifyCategoryThresould($model_id)
    {  // cat 7 data card
        // $cat = Category::withCount('assets')->where('id','=',$category_id)->first();
        // count all devices with same model which are not ready to deploy
        // $readyToDeployCountModel = Device::where([
        //     ['status_id','=',1],
        //     ['model_id','=',$model_id]
        // ])->count();
        // return;

        $alertnotify = null;
        if (Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }

        $alertmail = null;
        $ThresholdSettings = ThresholdSettings::first();
        $thresholdcat = Model::find($model_id)->category_id;
        $thresouldDtl = Threshold::where('cat_id', '=', $thresholdcat)->first();
        if (empty($thresouldDtl))
            return;
        // echo '<pre>',print_r($ThresholdSettings);
        if ($ThresholdSettings->threshold_enabled && $ThresholdSettings->alerts_enabled && $thresouldDtl->alerts_enabled) {
            if ($ThresholdSettings->send_alerts == 0) {
                if (Settings::first()->alerts_enabled == 1)
                    $alertmail = CommonHelper::getGlobalAlertEmail();
            }
            if ($ThresholdSettings->send_alerts == 1) {
                $alertmail = $ThresholdSettings->email;
            }
        }
        // var_dump($alertmail) ;die;
        if (!empty($alertmail) && $thresouldDtl->threshold > 0) {
            $catDetail = Category::find($thresholdcat);

            // $deployableCatDeviceCount
            // $catDevicedtl = Category::with([
            //     'assets' => function($query){
            //         $query->where('assets.status_id',1);
            //         // $query->join('status_labels as lbl', function($q) {
            //         //     $q->on('lbl.id', '=', 'assets.status_id');
            //         //     $q->where('lbl.deployable', '=', 1);
            //         //     $q->where('lbl.archived', '=', 0);
            //         //     // $q->where('assets.user_id', '=', null);
            //         // });
            //     }
            // ])->where('id','=',$thresholdcat)->get();
            $db = DB::table('categories as cat');
            $db->leftJoin('models as mdl', 'cat.id', '=', 'mdl.category_id');
            $db->leftJoin('assets as device', 'mdl.id', '=', 'device.model_id');
            $db->join('status_labels as lbl', function ($q) {
                $q->on('lbl.id', '=', 'device.status_id');
                $q->where('lbl.deployable', '=', 1);
                $q->where('lbl.archived', '=', 0);
            });
            // $db->select('device.assigned_to','device.id','mdl.category_id','mdl.id as modelid');
            $db->addSelect(DB::raw('count(device.id) as device_count'));
            $db->whereNull('device.assigned_to');
            // $db->addSelect('device.user_id');

            $db->whereNull('device.deleted_at');
            $db->where('cat.id', '=', $thresholdcat);
            $deployableCatDeviceCount = $db->count();
            // echo '<pre>';print_r($thresouldDtl->threshold);die;

            if (!empty($thresouldDtl)) {  // threshould value check
                // echo 	$deployableCatDeviceCount;
                if ($deployableCatDeviceCount < $thresouldDtl->threshold) {
                    // if ( $deployableCatDeviceCount > $thresouldDtl->threshold ){
                    // trigger mail
                    // echo 'mail triggering';
                    if (config('mail.service_enabled')) {
                        Mail::to($alertmail)->cc($alertnotify)->send(new ThreshouldNotification($catDetail, $thresouldDtl->threshold, $deployableCatDeviceCount));
                        $thresouldDtl->notify_count = $thresouldDtl->notify_count + 1;
                        $thresouldDtl->last_notified_date = date('Y-m-d');
                    }
                    $thresouldDtl->save();
                }
            }
        }
    }

    public function getDeviceForDropDown(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);

            $db = DB::table('assets as a');
            $db->leftJoin('models as mdl', 'a.model_id', '=', 'mdl.id');
            $db->leftJoin('status_labels as s', 'a.status_id', '=', 's.id');

            $db->select('a.id', 'a.asset_tag', 'a.name as asset_name', 'mdl.name', 'mdl.modelno', 'a.serial', DB::raw("concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) as text"));
            $db->whereNull('a.deleted_at');
            $db->whereNull('s.sold');
            $db->whereNull('s.stolen_item');
            if (!Auth::user()->hasPermissionTo('DeviceRead')) {
                $db->where('a.assigned_to', Auth::user()->id);
            }
            if ($search) {
                $db->whereRaw("(concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) like '%" . $search . "%' or a.asset_tag like '%" . $search . "%'  or a.name like '%" . $search . "%' or mdl.name like '%" . $search . "%' or mdl.modelno like '%" . $search . "%'  or a.serial like '%" . $search . "%')");
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error('getDeviceForDropDown: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getDeviceForCheckoutDropDown(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);

            $db = DB::table('assets as a');
            $db->leftJoin('models as mdl', 'a.model_id', '=', 'mdl.id');
            $db->leftJoin('status_labels as s', 'a.status_id', '=', 's.id');
            $db->select('a.id', 'a.asset_tag', 'a.name as asset_name', 'mdl.name', 'mdl.modelno', 'a.serial', DB::raw("concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) as text"));
            if (!Auth::user()->hasPermissionTo('DeviceRead')) {
                $db->where('a.assigned_to', Auth::user()->id);
            }
            $db->whereNotIn('a.status_id', [5, 4, 3, 7]);
            $db->whereNull('a.deleted_at');
            if ($search) {
                $db->whereRaw("(concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) like '%" . $search . "%' or a.asset_tag like '%" . $search . "%'  or a.name like '%" . $search . "%' or mdl.name like '%" . $search . "%' or mdl.modelno like '%" . $search . "%'  or a.serial like '%" . $search . "%')");
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();
            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error('getDeviceForCheckoutDropDown: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getDeviceForAllTicketDropDown(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);

            $db = DB::table('assets as a');
            $db->leftJoin('models as mdl', 'a.model_id', '=', 'mdl.id');
            $db->leftJoin('status_labels as s', 'a.status_id', '=', 's.id');

            $db->select('a.id', 'a.asset_tag', 'a.name as asset_name', 'mdl.name', 'mdl.modelno', 'a.serial', DB::raw("concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) as text"));
            $db->whereNull('a.deleted_at');
            $db->whereNull('s.sold');
            $db->whereNull('s.stolen_item');
            if ($search) {
                $db->whereRaw("(concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) like '%" . $search . "%' or a.asset_tag like '%" . $search . "%'  or a.name like '%" . $search . "%' or mdl.name like '%" . $search . "%' or mdl.modelno like '%" . $search . "%'  or a.serial like '%" . $search . "%')");
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return response()->json($return);
    }

    /* download operating system based dashboard */

    public function downloadbasedOS(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => 'Unable to export the Operating System Based OS'];

        $db = DB::table('itm_network_inventory_basic as itm');
        $db->leftJoin('assets as a', 'a.serial', '=', 'itm.BIOSSerialNumber');
        $db->leftJoin('models as mdl', 'mdl.id', '=', 'a.model_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'mdl.manufacturer_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.rtd_location_id');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');
        // $db->leftJoin('users as u', 'u.id', '=', 'a.assigned_to');
        $db->leftJoin('users as u', function ($q) {
            $q->on('u.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '1');
        });
        $db->leftJoin('locations as assigned_user_loc', function ($q) {
            $q->on('assigned_user_loc.id', '=', 'u.location_id');
            $q->where('a.assigned_for', '=', '1');
        });

        $db->leftJoin('users as own', 'own.id', '=', 'a.asset_owner');
        $db->leftJoin('lease_agreements as lease', 'lease.id', '=', 'a.lease_id');
        $db->leftJoin('projects as pr', 'pr.id', '=', 'a.last_checkout_project');
        $db->leftJoin('status_labels as lbl', 'lbl.id', '=', 'a.status_id');
        $db->leftJoin('categories as cat', function ($q) {
            $q->on('cat.id', '=', 'mdl.category_id');
            $q->where('mdl.category_id', '<>', 0);
        });
        $db->leftJoin('procure_account_types as acc_typ', 'acc_typ.id', '=', 'cat.account_type_id');
        $db->leftJoin('places as pldev', function ($q) {
            $q->on('pldev.id', '=', 'a.stock_place');
        });
        $db->leftJoin('places as p', function ($q) {
            $q->on('p.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '2');
        });
        $db->leftJoin('departments as dept', 'dept.id', '=', 'u.department_id');
        $db->leftJoin('locations as pldevloc', 'pldevloc.id', '=', 'pldev.location_id');
        $db->leftJoin('locations as ploc', 'ploc.id', '=', 'p.location_id');
        $db->leftJoin('suppliers as amc_supp', 'a.amc_supplier_id', '=', 'amc_supp.id');

        $db->select('a.id', 'a.asset_tag', 'a.uuid', 'mdl.name as mdl_name', 'mdl.manufacturer_id', 'mnu.name as manu_name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'dept.name', 'a.purchase_cost', 'cat.name as cat_name', 'a.name', 'a.serial', 'a.notes', 'a.order_number', 'a.warranty_months', 'mdl.fieldset_id', 'amc_supp.name as amc_supp_name', 'a.ip', 'a.mac', 'pur.invoice_no as pur_invoice', 'assigned_user_loc.name as assigned_user_loc_name', 'itm.ComputerName', 'itm.ComputerManufacturer', 'itm.ComputerModel', 'itm.BIOSSerialNumber', 'itm.ComputerDomain', 'itm.OSCaption', 'itm.ActiveMACAddress', 'itm.IPv4', 'itm.id as basic_id', 'itm.HddSize', 'itm.RamSize', 'itm.ProcessorName', 'itm.ComputerSystemType');
        $db->addSelect(DB::raw('trim(concat_ws(" ", mdl.name, mdl.modelno)) as mdl_full_name'));
        $db->addSelect(DB::raw('case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end as last_checkout_project'));
        $db->addSelect(DB::raw('concat(u.first_name, " ", u.last_name) as full_name'));
        $db->addSelect(DB::raw('case when a.assigned_for = 1 then "User" when a.assigned_for = 2 then "Place" when a.stock_place is not null then "Stock Place" else "" end as checkout_to_text'));
        $db->addSelect(DB::raw('case when a.assigned_for = 1 then concat(u.first_name, " ", u.last_name) when a.assigned_for = 2 then p.place when a.stock_place is not null then pldev.place else "" end as checkout'));
        $db->addSelect('u.username as ckout_username', 'u.email as ckout_email', 'u.employee_num as ckout_emp_num');
        $db->addSelect(DB::raw('concat(own.first_name, " ", own.last_name) as asset_owner'));
        $db->addSelect(DB::raw('case when u.job_type in (1,2) then u.ex_user_company else null end as ex_user_company'));
        $db->addSelect(DB::raw("case when lbl.deployable <> 0 and a.assigned_to <> '' and a.assigned_to > 0 then 2 when lbl.deployable <> 0 then 1 else 0 end as check_action"));
        $db->addSelect(DB::raw('case when a.device_occure_type = 1 then DATE_FORMAT(lease.end_date, "%d %b %Y ") else "" end as lease_end_date'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.last_checkout, "%d %b %Y %h:%i %p") as last_checkout_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.expected_checkin, "%d %b %Y %h:%i %p") as expected_checkin_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(itm.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
        $db->addSelect(DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end as lbl_name'));
        $db->addSelect(DB::raw('case when acc_typ.name is not null then acc_typ.name else "" end as acc_type_name'));
        $db->addSelect('p.place', 'ploc.name as place_loc', 'pldev.place as stock_place', 'pldevloc.name as pldevloc_name', 'a.assigned_for', 'a.assigned_to');
        $db->addSelect(DB::raw('case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" else "Purchase Device" end as device_occure_type_name'));
        $device_os = $request->os;
        if ($device_os != null) {
            $db->where('itm.OSCaption', '=', (string) $device_os);
        }

        $records = $db->get();
        $data = [];
        foreach ($records as $r) {
            $data[] = [
                $r->asset_tag,
                $r->ComputerName,
                $r->ComputerDomain,
                $r->ComputerManufacturer,
                $r->ComputerModel,
                $r->BIOSSerialNumber,
                $r->OSCaption,
                $r->HddSize,
                $r->RamSize,
                $r->ProcessorName,
                $r->ComputerSystemType,
                $r->IPv4,
                $r->ActiveMACAddress,
                $r->checkout_to_text,
                $r->checkout,
                $r->ckout_email,
                $r->updated_at_format
            ];
        }

        $result = json_decode(json_encode($data, true), true);
        return Excel::download(new OperatingSystemDetailsMS($result), 'Operating System Details.xlsx');
    }

    /* download microsoft based dashboard */

    public function downloadbasedMSOffice(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => 'Unable to export the Operating System Based OS'];

        $db = DB::table('itm_network_inventory_basic as itm');
        $db->leftJoin('assets as a', 'a.serial', '=', 'itm.BIOSSerialNumber');
        $db->leftJoin('itm_network_inventory_products as sw', 'itm.id', '=', 'sw.basic_id');
        $db->leftJoin('models as mdl', 'mdl.id', '=', 'a.model_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'mdl.manufacturer_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.rtd_location_id');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');
        // $db->leftJoin('users as u', 'u.id', '=', 'a.assigned_to');
        $db->leftJoin('users as u', function ($q) {
            $q->on('u.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '1');
        });
        $db->leftJoin('locations as assigned_user_loc', function ($q) {
            $q->on('assigned_user_loc.id', '=', 'u.location_id');
            $q->where('a.assigned_for', '=', '1');
        });

        $db->leftJoin('users as own', 'own.id', '=', 'a.asset_owner');
        $db->leftJoin('lease_agreements as lease', 'lease.id', '=', 'a.lease_id');
        $db->leftJoin('projects as pr', 'pr.id', '=', 'a.last_checkout_project');
        $db->leftJoin('status_labels as lbl', 'lbl.id', '=', 'a.status_id');
        $db->leftJoin('categories as cat', function ($q) {
            $q->on('cat.id', '=', 'mdl.category_id');
            $q->where('mdl.category_id', '<>', 0);
        });
        $db->leftJoin('procure_account_types as acc_typ', 'acc_typ.id', '=', 'cat.account_type_id');
        $db->leftJoin('places as pldev', function ($q) {
            $q->on('pldev.id', '=', 'a.stock_place');
        });
        $db->leftJoin('places as p', function ($q) {
            $q->on('p.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '2');
        });
        $db->leftJoin('departments as dept', 'dept.id', '=', 'u.department_id');
        $db->leftJoin('locations as pldevloc', 'pldevloc.id', '=', 'pldev.location_id');
        $db->leftJoin('locations as ploc', 'ploc.id', '=', 'p.location_id');
        $db->leftJoin('suppliers as amc_supp', 'a.amc_supplier_id', '=', 'amc_supp.id');

        $db->select('a.id', 'a.asset_tag', 'a.uuid', 'mdl.name as mdl_name', 'mdl.manufacturer_id', 'mnu.name as manu_name', 'cmp.name as cmp_name', 'loc.name as loc_name', 'dept.name', 'a.purchase_cost', 'cat.name as cat_name', 'a.name', 'a.serial', 'a.notes', 'a.order_number', 'a.warranty_months', 'mdl.fieldset_id', 'amc_supp.name as amc_supp_name', 'a.ip', 'a.mac', 'pur.invoice_no as pur_invoice', 'assigned_user_loc.name as assigned_user_loc_name', 'itm.ComputerName', 'itm.ComputerManufacturer', 'itm.ComputerModel', 'itm.BIOSSerialNumber', 'itm.ComputerDomain', 'itm.OSCaption', 'itm.ActiveMACAddress', 'itm.IPv4', 'itm.id as basic_id', 'itm.HddSize', 'itm.RamSize', 'itm.ProcessorName', 'itm.ComputerSystemType', 'sw.Caption as sw_name');
        $db->addSelect(DB::raw('trim(concat_ws(" ", mdl.name, mdl.modelno)) as mdl_full_name'));
        $db->addSelect(DB::raw('case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end as last_checkout_project'));
        $db->addSelect(DB::raw('concat(u.first_name, " ", u.last_name) as full_name'));
        $db->addSelect(DB::raw('case when a.assigned_for = 1 then "User" when a.assigned_for = 2 then "Place" when a.stock_place is not null then "Stock Place" else "" end as checkout_to_text'));
        $db->addSelect(DB::raw('case when a.assigned_for = 1 then concat(u.first_name, " ", u.last_name) when a.assigned_for = 2 then p.place when a.stock_place is not null then pldev.place else "" end as checkout'));
        $db->addSelect('u.username as ckout_username', 'u.email as ckout_email', 'u.employee_num as ckout_emp_num');
        $db->addSelect(DB::raw('concat(own.first_name, " ", own.last_name) as asset_owner'));
        $db->addSelect(DB::raw('case when u.job_type in (1,2) then u.ex_user_company else null end as ex_user_company'));
        $db->addSelect(DB::raw("case when lbl.deployable <> 0 and a.assigned_to <> '' and a.assigned_to > 0 then 2 when lbl.deployable <> 0 then 1 else 0 end as check_action"));
        $db->addSelect(DB::raw('case when a.device_occure_type = 1 then DATE_FORMAT(lease.end_date, "%d %b %Y ") else "" end as lease_end_date'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.last_checkout, "%d %b %Y %h:%i %p") as last_checkout_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.expected_checkin, "%d %b %Y %h:%i %p") as expected_checkin_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(itm.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
        $db->addSelect(DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'));
        $db->addSelect(DB::raw('case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end as lbl_name'));
        $db->addSelect(DB::raw('case when acc_typ.name is not null then acc_typ.name else "" end as acc_type_name'));
        $db->addSelect('p.place', 'ploc.name as place_loc', 'pldev.place as stock_place', 'pldevloc.name as pldevloc_name', 'a.assigned_for', 'a.assigned_to');
        $db->addSelect(DB::raw('case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" else "Purchase Device" end as device_occure_type_name'));
        $ms_office = $request->ms_office;
        if ($ms_office != null) {
            $db->where('sw.Caption', '=', (string) $ms_office);
        }

        $records = $db->get();
        $data = [];
        foreach ($records as $r) {
            $data[] = [
                $r->asset_tag,
                $r->ComputerName,
                $r->ComputerDomain,
                $r->ComputerManufacturer,
                $r->ComputerModel,
                $r->BIOSSerialNumber,
                $r->OSCaption,
                $r->HddSize,
                $r->RamSize,
                $r->ProcessorName,
                $r->ComputerSystemType,
                $r->IPv4,
                $r->ActiveMACAddress,
                $r->sw_name,
                $r->checkout_to_text,
                $r->checkout,
                $r->ckout_email,
                $r->updated_at_format
            ];
        }

        $result = json_decode(json_encode($data, true), true);
        return Excel::download(new MicrosoftOfficeDetailsMS($result), 'Microsoft Office Details.xlsx');
    }

    /* Device Import With Downloading the data */
    public function deviceImport(Request $request)
    {
        $return = ['msg' => 'Unable to import the given device list', 'status' => 'danger'];
        if (!Auth::user()->hasPermissionTo('DeviceImport') || !config('services.assets.enabled')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with('msg', $return);
        }
        if ($request->isMethod('post') && !$request->import_file) {
            $return['msg'] = trans('content.device_fields.upload_the_file');
            $request->session()->flash('msg', $return);
        } else {
            if (!$request->isMethod('post')) {
                $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
                return view('devices.import')->with('customFieldset', $customFieldset);
            }

            if ($request->isMethod('post') && $request->import_file) {
                $path = $request->file('import_file')->getRealPath();
                $currentUser = Auth::user()->id;
                $currentAuthUser = Auth::user();

                $non_deployed_labels = Label::getAllNonDeployedLabels();
                $all_companies = Company::getAllCompany();
                $exception_break = false;
                $tot_insert_records = 0;
                try {
                    $import = new DevicesImport($request);
                    Excel::import($import, $request->import_file);
                    $response = $import->data ?? ['success'=> 0, 'fail'=> 0, 'fail_msgs' => [],
                    ];
                    return redirect()->back()->with(['success'=> $response['success'],'fail'=> $response['fail'], 'fail_msgs' => $response['fail_msgs'],
                    ]);
                } catch (\Throwable $e) {
                    $return['msg'] = $e->getMessage();
                    $return['status'] = 'danger';
                    return redirect()->back()->with('msg', $return);
                }
            }
        }
        return view('devices.import');
    }
    public function ajaxAddRdpConnectHistory(Request $request)
    {
        $return = ['status' => 'fail', 'msg' => trans('content.rdp.history_store_fail')];
        $input = $request->all();
        $user_id = Auth::user()->id;
        try {
            $rdpHistory = new RdpConnectHistory();
            $rdpHistory->device_id = $input['device_id'];
            $rdpHistory->user_id = $user_id;

            if (!$rdpHistory->save()) {
                return response()->json($return);
            }
            $return['status'] = 'success';
            $return['msg'] = trans('content.rdp.history_store_success');
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error('ajaxAddRdpConnectHistory Error: ' . $e->getMessage());
            return response()->json($return);
        }
    }

    public function ajaxRdpHistory(Request $request)
    {
        $req = $request->all();
        $user_id = Auth::user()->id;
        $return = array(
            'draw' => date('is')
        );

        $fields = array(
            '0' => 'u.username',
            '1' => 'rdp.created_at',
        );
        try {
            $db = RdpConnectHistory::from('itm_rdp_connect_history as rdp')->select('rdp.*', 'u.username')->where('device_id', $req['device_id'])->leftjoin('users as u', 'u.id', 'rdp.user_id');

            $return['recordsTotal'] = $db->count();
            $return['recordsFiltered'] = $return['recordsTotal'];

            if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
                $whereStr = sprintf('(u.username like "%%%1$s%%" )', $search_key);
                $db->whereRaw($whereStr);
                $return['recordsFiltered'] = $db->count();
            }

            if (isset($req['order'][0]['column']) && isset($fields[$req['order'][0]['column']]) && in_array($req['order'][0]['dir'], ['asc', 'desc'])) {
                $db->orderBy($fields[$req['order'][0]['column']], $req['order'][0]['dir']);
            }

            $skip = 0;
            $take = 10;
            if (isset($req['start']) && isset($req['length'])) {
                $skip = (int) $req['start'];
                $take = (int) $req['length'];
            }
            $db->skip($skip);
            $db->take($take);

            $data = $db->get()->toArray();
            $return['data'] = $data;
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error('ajaxRdpHistory Error: ' . $e->getMessage());
            return response()->json($return);
        }
    }

    public function ajaxUpdateRdpStatus(Request $request)
    {
        $return = ['status' => 'fail', 'msg' => trans('content.rdp.status_update_fail')];
        $req = $request->all();
        try {
            DB::beginTransaction();
            $deviceObj = Device::find($req['device_id']);
            $deviceObj->rdp_status = $req['rdp_status'];

            if (!$deviceObj->save()) {
                $return['msg'] = trans('content.rdp.status_update_fail');
                return response()->json($return);
            }
            $getRdpData = [];
            $network = Basic::where('BIOSSerialNumber', '=', $deviceObj->serial)
                ->whereNull('is_dupe')
                ->whereNull('is_virtual')
                ->select('id', 'BIOSSerialNumber')
                ->first();

            if (!empty($network->id)) {
                $getRdpData = Basic::getRdpData($network->id);
            }
            DB::commit();
            $return['status'] = 'success';
            $return['msg'] = trans('content.rdp.status_update_success');
            $return['rdp_count'] = !empty($network) && is_countable($getRdpData) ? count($getRdpData) : 0;

            $return['rdp_status'] = $deviceObj->rdp_status;
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return response()->json(['status' => 'fail', 'msg' => $e->getMessage()]);
        }
    }

    public function getChangeInfo(Request $request, $id)
    {
        $interactCacheObj = InteractCache::select(
            'interacted_caches.*',
            'm.id as model_id',
            'm_old.id as model_old_id',
            'm.name as model_name',
            'm_old.name as model_name_old',
            'cat.id as cat_id',
            'cat_old.id as cat_old_id',
            'cat.name as cat_name',
            'cat_old.name as cat_old_name',
            'man.id as man_id',
            'man_old.id as man_old_id',
            'man.name as man_name',
            'man_old.name as man_old_name',
            'status.name as status_name',
            'status_old.name as status_name_old',
            'l.name as location_name',
            'l_old.name as location_name_old',
            'd.name as department_name',
            'd_old.name as department_name_old',
            's.name as supplier_name',
            's_old.name as supplier_name_old',
            'companies.name as company_name',
            'c_old.name as company_name_old',
            'assets_types.name as assets_types_name',
            'asset_type_old.name as assets_types_name_old',
            'purchases.invoice_no as purchase_name',
            'p_old.invoice_no as purchase_name_old',
            'amc_supp.name as amc_supplier_name',
            'amc_supp_old.name as amc_supplier_name_old',
            'place.place as place_name',
            'place_old.place as place_old_name',
            'a.ip as ip',
            'interacted_caches.ip as old_ip',
            'a.mac as mac',
            'interacted_caches.mac as old_mac',
            'a.id as asset_id',
            'pldev.place as oldstockplace',
            'pa.place as newstockplace',
            'a.asset_owner as asset_owner_new',
            'interacted_caches.asset_owner as asset_owner_old',
            DB::raw("CONCAT(new_owner.first_name, ' ', new_owner.last_name) as asset_owner_new_name"),
            DB::raw("CONCAT(old_owner.first_name, ' ', old_owner.last_name) as asset_owner_old_name")
            )
            ->addSelect(DB::raw('case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" when a.device_occure_type = 0 then "Purchase Device" else "" end as device_occure_type_new'))
            ->addSelect(DB::raw('case when interacted_caches.device_occure_type = 1 then "Project Device" when interacted_caches.device_occure_type = 2 then "Rental" when interacted_caches.device_occure_type = 3 then "Customer Owned" when interacted_caches.device_occure_type = 0 then "Purchase Device" else "" end as device_occure_type_old'))
            ->leftjoin('asset_logs as al', 'al.id', 'interacted_caches.interact_log_id')
            ->leftjoin('interacted_records as ir', 'ir.id', 'interacted_caches.interact_record_id')
            ->leftjoin('assets as a', 'a.id', 'al.asset_id')
            ->leftjoin('models as m', 'm.id', 'a.model_id')
            ->leftjoin('categories as cat', 'cat.id', 'm.category_id')
            ->leftjoin('manufacturers as man', 'man.id', 'm.manufacturer_id')
            ->leftjoin('models as m_old', 'm_old.id', 'interacted_caches.model_id')
            ->leftjoin('categories as cat_old', 'cat_old.id', 'm_old.category_id')
            ->leftjoin('manufacturers as man_old', 'man_old.id', 'm_old.manufacturer_id')
            ->leftjoin('status_labels as status', 'status.id', 'ir.status_id')
            ->leftjoin('status_labels as status_old', 'status_old.id', 'interacted_caches.status_id')
            ->leftjoin('departments as d', 'd.id', 'a.department_id')
            ->leftjoin('departments as d_old', 'd_old.id', 'interacted_caches.department_id')
            ->leftjoin('locations as l', 'l.id', 'a.rtd_location_id')
            ->leftjoin('locations as l_old', 'l_old.id', 'interacted_caches.rtd_location_id')
            ->leftjoin('suppliers as s', 's.id', 'a.supplier_id')
            ->leftjoin('suppliers as s_old', 's_old.id', 'interacted_caches.supplier_id')
            ->leftjoin('companies', 'companies.id', 'a.company_id')
            ->leftjoin('companies as c_old', 'c_old.id', 'interacted_caches.company_id')
            ->leftjoin('assets_types', 'assets_types.id', 'a.asset_type_id')
            ->leftjoin('assets_types as asset_type_old', 'asset_type_old.id', 'interacted_caches.asset_type_id')
            ->leftjoin('users as new_owner', 'new_owner.id', 'a.asset_owner')
            ->leftjoin('users as old_owner', 'old_owner.id', 'interacted_caches.asset_owner')
            ->leftjoin('purchases', 'purchases.id', 'a.invoice_id')
            ->leftjoin('purchases as p_old', 'p_old.id', 'interacted_caches.invoice_id')
            ->leftJoin('suppliers as amc_supp', 'a.amc_supplier_id', '=', 'amc_supp.id')
            ->leftjoin('suppliers as amc_supp_old', 'interacted_caches.amc_supplier_id', 'amc_supp_old.id')
            ->leftjoin('places as place', 'place.id', 'a.internal_place_id')
            ->leftjoin('places as place_old', 'place_old.id', 'interacted_caches.internal_place_id')
            ->leftjoin('places as pldev', 'pldev.id', 'interacted_caches.stock_place')
            ->leftjoin('places as pa', 'pa.id', 'a.stock_place')
            ->where('interact_record_id', $id)
            ->first();
        $customFieldsCache = $customFieldsRecord = null;
        $interactedCaches = InteractCache::where('interact_record_id', $id)->first();
        $interactRecord = InteractRecord::where('interact_log_id', $interactedCaches->interact_log_id)->first();
        if (Settings::first()->custom_fieldset_id != null) {
            $fieldsetObj = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
            if (!empty($fieldsetObj)) {
                $customFieldsCache = CommonHelper::getCustomData($fieldsetObj->fields, $interactedCaches);
                $customFieldsRecord = CommonHelper::getCustomData($fieldsetObj->fields, $interactRecord);
            }
        }
        $html = view('devices.change-info', [
            'interactCacheObj' => $interactCacheObj,
            'customFieldsCache' => $customFieldsCache,
            'customFieldsRecord' => $customFieldsRecord,
            'interactRecord' => $interactRecord,
        ])->render();

        return response()->json([
            'status' => true,
            'html'   => $html,
        ]);
    }

    public function deviceExportExcel(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('DeviceDownload') || !config('services.assets.enabled')) {
            return redirect('dashboard')->with('msg', $return);
        }
        $req = $request->all();
        $return = array(
            // "draw" => date('is')
        );
        $objDeviceSettings = DeviceSetting::first();
        $device_export_column = isset($objDeviceSettings->device_export_column) && $objDeviceSettings->device_export_column != null ? explode(',', $objDeviceSettings->device_export_column) : [];
        $export_column_empty = false;
        if (empty($device_export_column)) {
            $export_column_empty = true;
        }

        if (in_array('Custom Fields', $device_export_column) || $export_column_empty == true) {
            $set = $get_custom_fields = '';
            $setting = Settings::getSettings()->custom_fieldset_id ? Settings::getSettings()->custom_fieldset_id : '';
            if ($setting) {
                $set = 'where cfcf.custom_fieldset_id = ' . $setting;
                $get_custom_fields = DB::select("SELECT cfcf.custom_fieldset_id, CONCAT('a._itm_', replace(lcase(cf.NAME), ' ', '_')) AS cus_field FROM custom_fields AS cf
                JOIN custom_field_custom_fieldset AS cfcf ON cf.id = cfcf.custom_field_id $set");
            }

            $coll_field_sets = [];
            $field_sets = [];
            $coll_custom_fields = [];
            $custom_fields = [];
            $custom_field_names = [];
            $field_based_fieldsets_ids = [];
            if ($get_custom_fields) {
                foreach ($get_custom_fields as $cf) {
                    $coll_field_sets[] = $cf->custom_fieldset_id;
                    $coll_custom_fields[] = $cf->cus_field;
                    if (!isset($field_based_fieldsets_ids[$cf->cus_field])) {
                        $field_based_fieldsets_ids[$cf->cus_field] = [$cf->custom_fieldset_id];
                    } else {
                        $field_based_fieldsets_ids[$cf->cus_field][] = $cf->custom_fieldset_id;
                    }
                }

                $field_sets = array_unique($coll_field_sets);
                $custom_fields = array_unique($coll_custom_fields);

                foreach ($custom_fields as $cf) {
                    $n1 = str_replace('a._itm_', ' ', $cf);
                    $n2 = str_replace('_', ' ', $n1);
                    $custom_field_names[$cf] = ucwords($n2);
                }
            }
        }

        $db = DB::table('assets as a');
        $db->leftJoin('asset_logs as al', 'al.id', '=', 'a.chkout_acceptance_log_id');
        $db->leftJoin('models as mdl', 'mdl.id', '=', 'a.model_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'mdl.manufacturer_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.rtd_location_id');
        $db->leftJoin('locations as ni_loc', 'ni_loc.id', '=', 'a.ni_detected_location');
        $db->leftJoin('places as pla', 'pla.id', '=', 'a.internal_place_id');
        $db->leftJoin('locations as locByNi', 'locByNi.id', '=', 'a.ni_detected_location');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');
        // $db->leftJoin('users as u', 'u.id', '=', 'a.assigned_to');
        $db->leftJoin('users as u', function ($q) {
            $q->on('u.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '1');
        });
        $db->leftJoin('locations as assigned_user_loc', function ($q) {
            $q->on('assigned_user_loc.id', '=', 'u.location_id');
            $q->where('a.assigned_for', '=', '1');
        });
        $db->leftJoin('places as assigned_user_place', function ($q) {
            $q->on('assigned_user_place.id', '=', 'u.internal_place_id');
            $q->where('a.assigned_for', '=', '1');
        });

        $db->leftJoin('users as own', 'own.id', '=', 'a.asset_owner');
        $db->leftJoin('lease_agreements as lease', 'lease.id', '=', 'a.lease_id');
        $db->leftJoin('projects as pr', 'pr.id', '=', 'a.last_checkout_project');
        $db->leftJoin('status_labels as lbl', 'lbl.id', '=', 'a.status_id');
        $db->leftJoin('categories as cat', function ($q) {
            $q->on('cat.id', '=', 'mdl.category_id');
            $q->where('mdl.category_id', '<>', 0);
        });
        $db->leftJoin('procure_account_types as acc_typ', 'acc_typ.id', '=', 'cat.account_type_id');
        $db->leftJoin('places as pldev', function ($q) {
            $q->on('pldev.id', '=', 'a.stock_place');
        });
        $db->leftJoin('places as p', function ($q) {
            $q->on('p.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '2');
        });
        $db->leftJoin('users as manager', 'manager.id', '=', 'u.manager_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->leftJoin('departments as dept', 'dept.id', '=', 'u.department_id');
        $db->leftJoin('locations as pldevloc', 'pldevloc.id', '=', 'pldev.location_id');
        $db->leftJoin('locations as ploc', 'ploc.id', '=', 'p.location_id');
        $db->leftJoin('suppliers as s', 'a.supplier_id', '=', 's.id');
        $db->leftJoin('suppliers as amc_supp', 'a.amc_supplier_id', '=', 'amc_supp.id');
        $db->leftJoin('assets_types as at', 'a.asset_type_id', '=', 'at.id');
        $db->leftJoin('asset_logs as al1', function ($join) {
            $join->on('a.chkout_log_id', '=', 'al1.id');
            $join->on('a.status_id', '=', DB::raw('6'));
        });
        $db->leftJoin('itm_asset_allocation_type as ata', 'ata.id', '=', 'al1.allocation_type_id');
        $db->leftJoin('itm_network_inventory_basic as itm', 'itm.device_id', '=', 'a.id');
        $db->leftJoin('itm_network_inventory_basic_azure as itm_azure', 'itm_azure.device_id', '=', 'a.id');

        if ($export_column_empty == true) {
            $db->select(
                DB::raw('(SELECT COUNT(*) FROM assets WHERE assigned_to = a.assigned_to) as assigned_count'),
                'a.assigned_for',
                'cmp.name as cmp_name',
                'a.id',
                'a.name',
                'a.asset_tag',
                'a.serial',
                'mnu.name as manu_name',
                'mdl.name as mdl_full_name',
                'cat.name as cat_name',
                DB::raw('case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end as lbl_name'),
                'loc.name as loc_name',
                'pla.place as internal_place',
                DB::raw('DATE_FORMAT(a.purchase_date, "%Y-%m-%d") as purchase_date_on'),
                'a.purchase_currency',
                DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'),
                'pur.invoice_no as pur_invoice',
                DB::raw('DATE_FORMAT(a.ship_date, "%Y-%m-%d") as ship_date'),
                DB::raw('DATE_FORMAT(a.warranty_start_date, "%Y-%m-%d") as warranty_start_date'),
                DB::raw('DATE_FORMAT(a.warrenty_end_date, "%Y-%m-%d") as warranty_end_date'),
                'a.warranty_months',
                'a.notes',
                'a.order_number',
                DB::raw('concat(own.first_name, " ", own.last_name) as asset_owner'),
                'a.ip',
                'a.mac',
                DB::raw('case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" else "Purchase Device" end as device_occure_type_name'),
                'pldevloc.name AS stock_location',
                DB::raw('case when a.stock_place is not null and pldev.place is not null then concat(pldev.place, " ", pldevloc.name, "") else null end as stock_place'),
                'locByNi.name as locByNiName',
                's.name as supplier_name',
                DB::raw('case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end as last_checkout_project'),
                DB::raw('case when ((a.warranty_status = 1 or a.warranty_status = 2) and a.calc_warranty_expire_date is not null) then DATE_FORMAT(a.calc_warranty_expire_date, "%Y-%m-%d") else "" end as consolidated_warrenty_end_date'),
                DB::raw('case when a.warranty_status = 1 then "Expired" when a.warranty_status = 2 then "Not Expired" when a.warranty_status = 3 then "Not Applicable" when a.warranty_status = 4 then "Warranty End Date and OEM Date not matching" when a.warranty_status = 5 then "Purchase Date and OEM Date not matching" when a.warranty_status = 6 then "Invoice Date and OEM Date not matching" else "" end as warranty_status_text'),
                DB::raw('case when acc_typ.name is not null then acc_typ.name else "" end as acc_type_name'),
                'a.uuid',
                DB::raw('case when a.assigned_to is not null then DATE_FORMAT(a.last_checkout, "%Y-%m-%d") else "" end as chkout_date'),
                'a.accepted',
                DB::raw("case when (a.chkout_acceptance_log_id is not null and a.accepted = 'accepted') then date_format(al.created_at, '%Y-%m-%d') else '' end as chkout_acceptance_date"),
                DB::raw('case when a.assigned_for = 1 then "User" when a.assigned_for = 2 then "Place" end as user_or_place'),
                DB::raw("CASE WHEN a.status_id = 6 AND a.assigned_for = 1 THEN concat(u.first_name, ' ', u.last_name) WHEN a.status_id = 6 AND a.assigned_for = 2 THEN concat(p.place, ' ', ploc.name, '') WHEN a.status_id != 6 THEN lbl.name ELSE NULL END as user_place_status_text"),
                DB::raw('concat(u.first_name, " ", u.last_name) as full_name'),
                'u.username as ckout_username',
                'u.email as ckout_email',
                'u.employee_num as ckout_emp_num',
                'assigned_user_loc.name as assigned_user_loc_name',
                'assigned_user_place.place as assigned_user_place_name',
                'u.doj as assigned_user_doj',
                DB::raw('case when u.job_type in (1,2) then u.ex_user_company else null end as ex_user_company'),
                DB::raw('case when a.assigned_for = 2 then concat(p.place, " ", ploc.name, "") else null end as chkout_place'),
                DB::raw('DATE_FORMAT(a.expected_checkin, "%Y-%m-%d") as expected_checkin_on'),
                DB::raw('case when a.device_occure_type = 1 then DATE_FORMAT(lease.end_date, "%Y-%m-%d") else "" end as lease_end_date'),
                'amc_supp.name as amc_supp_name',
                DB::raw("case when (dayname(a.amc_expire_date) is not null) then date_format(a.amc_expire_date, '%Y-%m-%d') else null end as amc_ed_format, IF((dayname(a.amc_expire_date) is not null) and a.amc_expire_date < CURRENT_TIMESTAMP, 'Expired', '') as 'is_amc_expired'"),
                'a.manufacturer_warranty_data',
                'at.name as asset_type_name',
                'ata.name as allocation_type',
                'u.business_unit',
                'u.delivery_unit',
                'dept.name as user_department',
                'manager.email',
                DB::raw('case when a.requestable = 1 then "Yes" else "No" end as requestable'),
                DB::raw('case when a.high_pririty = 1 then "Yes" else "No" end as high_pririty'),
                DB::raw('case when a.sez_device = 1 then "Yes" else "No" end as sez_device'),
                DB::raw('DATE_FORMAT(itm.updated_at, "%Y-%m-%d") as ni_last_updated_at'),
                DB::raw('DATE_FORMAT(a.created_at, "%Y-%m-%d") as installed_at_format'),
                'itm_azure.azureADDeviceId',
                'itm_azure.lastSyncDateTime',
                'itm_azure.complianceState',
                DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as updated_at'),
                'dep.name as department',
                'itm.HddSize',
                'itm.RamSize',
                'itm.ProcessorName',
                'itm.OSCaption',
                DB::raw('case when itm.id is not null then "Yes" else "No" end as ni_exits'),
                DB::raw('case when itm_azure.id is not null then "Yes" else "No" end as azure_exits'),
                DB::raw('case when a.rdp_status = 1 AND a.node_id is not null then "Yes" else "No" end as rdp_enable'),
                DB::raw("CASE WHEN a.device_rfid IS NULL OR a.device_rfid = '' THEN ''ELSE REPLACE(REPLACE(REPLACE(JSON_EXTRACT(a.device_rfid, '\$'), '[', ''), ']', ''), '\"', '') END as device_rfids")
            );
        } else {
            $selects = [];
            $selects[] = DB::raw('(SELECT COUNT(*) FROM assets WHERE assigned_to = a.assigned_to) as assigned_count');
            $selects[] = 'a.assigned_for';
            $db->select(
                DB::raw('(SELECT COUNT(*) FROM assets WHERE assigned_to = a.assigned_to) as assigned_count'),
                'a.assigned_for',
            );
            if (in_array('Company', $device_export_column)) {
                $selects[] = 'cmp.name as cmp_name';
            }
            $selects[] = 'a.id';
            if (in_array('Device Name', $device_export_column)) {
                $selects[] = 'a.name';
            }
            if (in_array('Device Tag', $device_export_column)) {
                $selects[] = 'a.asset_tag';
            }
            if (in_array('Serial', $device_export_column)) {
                $selects[] = 'a.serial';
            }
            if (in_array('Manufacture', $device_export_column)) {
                $selects[] = 'mnu.name as manu_name';
            }
            if (in_array('Model Name', $device_export_column)) {
                $selects[] = 'mdl.name as mdl_full_name';
            }
            if (in_array('Category', $device_export_column)) {
                $selects[] = 'cat.name as cat_name';
            }
            if (in_array('Status', $device_export_column)) {
                $selects[] = DB::raw('case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end as lbl_name');
            }
            if (in_array('Location', $device_export_column)) {
                $selects[] = 'loc.name as loc_name';
            }
            if (in_array('Internal Place', $device_export_column)) {
                $selects[] = 'pla.place as internal_place';
            }
            if (in_array('Purchase Date', $device_export_column)) {
                $selects[] = DB::raw('DATE_FORMAT(a.purchase_date, "%Y-%m-%d") as purchase_date_on');
            }
            if (in_array('Purchase Currency', $device_export_column)) {
                $selects[] = 'a.purchase_currency';
            }
            if (in_array('Purchase Cost', $device_export_column)) {
                $selects[] = DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format');
            }
            if (in_array('Purchase Invoice', $device_export_column)) {
                $selects[] = 'pur.invoice_no as pur_invoice';
            }
            if (in_array('Ship Date', $device_export_column)) {
                $selects[] = DB::raw('DATE_FORMAT(a.ship_date, "%Y-%m-%d") as ship_date');
            }
            if (in_array('Warranty Start', $device_export_column)) {
                $selects[] = DB::raw('DATE_FORMAT(a.warranty_start_date, "%Y-%m-%d") as warranty_start_date');
            }
            if (in_array('Warranty End', $device_export_column)) {
                $selects[] = DB::raw('DATE_FORMAT(a.warrenty_end_date, "%Y-%m-%d") as warranty_end_date');
            }
            if (in_array('Warranty(Months)', $device_export_column)) {
                $selects[] = 'a.warranty_months';
            }
            if (in_array('Notes', $device_export_column)) {
                $selects[] = 'a.notes';
            }

            if (in_array('Order Number', $device_export_column)) {
                $selects[] = 'a.order_number';
            }
            if (in_array('Asset Owner', $device_export_column)) {
                $selects[] = DB::raw('concat(own.first_name, " ", own.last_name) as asset_owner');
            }
            if (in_array('IP', $device_export_column)) {
                $selects[] = 'a.ip';
            }

            if (in_array('MAC', $device_export_column)) {
                $selects[] = 'a.mac';
            }
            if (in_array('Device From', $device_export_column)) {
                $selects[] = DB::raw('case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" else "Purchase Device" end as device_occure_type_name');
            }
            if (in_array('Stock Location', $device_export_column)) {
                $selects[] = DB::raw('pldevloc.name AS stock_location');
            }
            if (in_array('Stock Place', $device_export_column)) {
                $selects[] = DB::raw('case when a.stock_place is not null and pldev.place is not null then concat(pldev.place, " ", pldevloc.name, "") else null end as stock_place');
            }
            if (in_array('Last Network Location', $device_export_column)) {
                $selects[] = 'locByNi.name as locByNiName';
            }
            if (in_array('Supplier', $device_export_column)) {
                $selects[] = 's.name as supplier_name';
            }
            if (in_array('Project Name', $device_export_column)) {
                $selects[] = DB::raw('case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end as last_checkout_project');
            }
            if (in_array('Warranty Expire Date', $device_export_column)) {
                $selects[] = DB::raw('case when ((a.warranty_status = 1 or a.warranty_status = 2) and a.calc_warranty_expire_date is not null) then DATE_FORMAT(a.calc_warranty_expire_date, "%Y-%m-%d") else "" end as consolidated_warrenty_end_date');
            }
            if (in_array('Warranty Status', $device_export_column)) {
                $selects[] = DB::raw('case when a.warranty_status = 1 then "Expired" when a.warranty_status = 2 then "Not Expired" when a.warranty_status = 3 then "Not Applicable" when a.warranty_status = 4 then "Warranty End Date and OEM Date not matching" when a.warranty_status = 5 then "Purchase Date and OEM Date not matching" when a.warranty_status = 6 then "Invoice Date and OEM Date not matching" else "" end as warranty_status_text');
            }
            if (in_array('Account Type', $device_export_column)) {
                $selects[] = DB::raw('case when acc_typ.name is not null then acc_typ.name else "" end as acc_type_name');
            }
            if (in_array('UUID', $device_export_column)) {
                $selects[] = 'a.uuid';
            }
            if (in_array('Checkout Date', $device_export_column)) {
                $selects[] = DB::raw('case when a.assigned_to is not null then DATE_FORMAT(a.last_checkout, "%Y-%m-%d") else "" end as chkout_date');
            }
            if (in_array('Checkout Status', $device_export_column)) {
                $selects[] = 'a.accepted';
            }
            if (in_array('Checkout Accepted Date', $device_export_column)) {
                $selects[] = DB::raw("case when (a.chkout_acceptance_log_id is not null and a.accepted = 'accepted') then date_format(al.created_at, '%Y-%m-%d') else '' end as chkout_acceptance_date");
            }
            if (in_array('User/Place', $device_export_column)) {
                $selects[] = DB::raw('case when a.assigned_for = 1 then "User" when a.assigned_for = 2 then "Place" end as user_or_place');
            }
            if (in_array('Username/Placename/Status', $device_export_column)) {
                $selects[] = DB::raw("CASE WHEN a.status_id = 6 AND a.assigned_for = 1 THEN concat(u.first_name, ' ', u.last_name) WHEN a.status_id = 6 AND a.assigned_for = 2 THEN concat(p.place, ' ', ploc.name, '') WHEN a.status_id != 6 
                THEN lbl.name ELSE NULL END as user_place_status_text");
            }
            if (in_array('Assigned User', $device_export_column)) {
                $selects[] = DB::raw('concat(u.first_name, " ", u.last_name) as full_name');
            }
            if (in_array('Assigned Username', $device_export_column)) {
                $selects[] = 'u.username as ckout_username';
            }
            if (in_array('Assigned User Email', $device_export_column)) {
                $selects[] = 'u.email as ckout_email';
            }
            if (in_array('Assigned User Emp. Code', $device_export_column)) {
                $selects[] = 'u.employee_num as ckout_emp_num';
            }
            if (in_array('Assigned User Location', $device_export_column)) {
                $selects[] = 'assigned_user_loc.name as assigned_user_loc_name';
            }
            if (in_array('Assigned User Place', $device_export_column)) {
                $selects[] = 'assigned_user_loc.name as assigned_user_place_name';
            }
            if (in_array('Assigned User DOJ', $device_export_column)) {
                $selects[] = 'u.doj as assigned_user_doj';
            }
            if (in_array('External User Company', $device_export_column)) {
                $selects[] = DB::raw('case when u.job_type in (1,2) then u.ex_user_company else null end as ex_user_company');
            }
            if (in_array('Assigned Place', $device_export_column)) {
                $selects[] = DB::raw('case when a.assigned_for = 2 then concat(p.place, " ", ploc.name, "") else null end as chkout_place');
            }
            if (in_array('Expected Checkin Date', $device_export_column)) {
                $selects[] = DB::raw('DATE_FORMAT(a.expected_checkin, "%Y-%m-%d") as expected_checkin_on');
            }
            if (in_array('Contract End Date', $device_export_column)) {
                $selects[] = DB::raw('case when a.device_occure_type = 1 then DATE_FORMAT(lease.end_date, "%Y-%m-%d") else "" end as lease_end_date');
            }
            if (in_array('AMC Supplier', $device_export_column)) {
                $selects[] = 'amc_supp.name as amc_supp_name';
            }
            if (in_array('AMC Expire Date', $device_export_column)) {
                $selects[] = DB::raw("case when (dayname(a.amc_expire_date) is not null) then date_format(a.amc_expire_date, '%Y-%m-%d') else null end as amc_ed_format");
            }
            if (in_array('AMC Expire Status', $device_export_column)) {
                $selects[] = DB::raw("IF((dayname(a.amc_expire_date) is not null) and a.amc_expire_date < CURRENT_TIMESTAMP, 'Expired', '') as is_amc_expired");
            }
            if (in_array('OEM Warranty', $device_export_column)) {
                $selects[] = 'a.manufacturer_warranty_data';
            }
            if (in_array('Device Type', $device_export_column)) {
                $selects[] = 'at.name as asset_type_name';
            }
            if (in_array('Allocation Type', $device_export_column)) {
                $selects[] = 'ata.name as allocation_type';
            }
            if (in_array('Business Unit', $device_export_column)) {
                $selects[] = 'u.business_unit';
            }

            if (in_array('Delivery Unit', $device_export_column)) {
                $selects[] = 'u.delivery_unit';
            }
            if (in_array('User Department', $device_export_column)) {
                $selects[] = 'dept.name as user_department';
            }
            if (in_array('Manager', $device_export_column)) {
                $selects[] = 'manager.email';
            }
            if (in_array('Requestable', $device_export_column)) {
                $selects[] = DB::raw('case when a.requestable = 1 then "Yes" else "No" end as requestable');
            }
            if (in_array('High Priority', $device_export_column)) {
                $selects[] = DB::raw('case when a.high_pririty = 1 then "Yes" else "No" end as high_pririty');
            }
            if (in_array('Sez Device', $device_export_column)) {
                $selects[] = DB::raw('case when a.sez_device = 1 then "Yes" else "No" end as sez_device');
            }
            if (in_array('Last NI Scan Date', $device_export_column)) {
                $selects[] = DB::raw('DATE_FORMAT(itm.updated_at, "%Y-%m-%d") as ni_last_updated_at');
            }
            if (in_array('Installed Date', $device_export_column)) {
                $selects[] = DB::raw('DATE_FORMAT(a.created_at, "%Y-%m-%d") as installed_at_format');
            }
            if (in_array('Azure AD Device Id', $device_export_column)) {
                $selects[] = 'itm_azure.azureADDeviceId';
            }
            if (in_array('Last Sync Date Time', $device_export_column)) {
                $selects[] = 'itm_azure.lastSyncDateTime';
            }
            if (in_array('Compliance State', $device_export_column)) {
                $selects[] = 'itm_azure.complianceState';
            }
            if (in_array('Updated At', $device_export_column)) {
                $selects[] = DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as updated_at');
            }
            if (in_array('Department', $device_export_column)) {
                $selects[] = 'dep.name as department';
            }
            if (in_array('HddSize', $device_export_column)) {
                $selects[] = 'itm.HddSize';
            }
            if (in_array('RAMSize', $device_export_column)) {
                $selects[] = 'itm.RamSize';
            }
            if (in_array('Processor', $device_export_column)) {
                $selects[] = 'itm.ProcessorName';
            }
            if (in_array('OSCaption', $device_export_column)) {
                $selects[] = 'itm.OSCaption';
            }
            if (in_array('NI Exist', $device_export_column)) {
                $selects[] = DB::raw('case when itm.id is not null then "Yes" else "No" end as ni_exits');
            }
            if (in_array('Azure Exist', $device_export_column)) {
                $selects[] = DB::raw('case when itm_azure.id is not null then "Yes" else "No" end as azure_exits');
            }
            if (in_array('RDP Enable', $device_export_column)) {
                $selects[] = DB::raw('case when a.rdp_status = 1 AND a.node_id is not null then "Yes" else "No" end as rdp_enable');
            }
            if (in_array('Device RFID', $device_export_column)) {
                $selects[] = DB::raw("
                    CASE 
                        WHEN a.device_rfid IS NULL OR a.device_rfid = '' THEN ''
                        ELSE REPLACE(REPLACE(REPLACE(JSON_EXTRACT(a.device_rfid, '\$'), '[', ''), ']', ''), '\"', '')
                    END as device_rfids
                ");
            }
            $db->select($selects);
        }
        $loc_previllage = Auth::user()->permitted_locations;  // getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(',', $loc_previllage);
        if ($settings->location_config == 1) {
            if (empty($loc_previllage)) {
                $db->where('a.rtd_location_id', '=', 0);
            } else {
                $db->whereIn('a.rtd_location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id;  // check from user's table
            $asset_depts = explode(',', $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }
        if (in_array('Custom Fields', $device_export_column) || $export_column_empty == true) {
            if (is_array($coll_custom_fields) and count($coll_custom_fields)) {
                foreach ($coll_custom_fields as $coll_custom_field) {
                    $clm_name = substr($coll_custom_field, 2);
                    if (Schema::hasColumn('assets', $clm_name)) {
                        $db->addSelect($coll_custom_field);
                    }
                }
            }
        }

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if (isset($filters->dashboard_filters)) {
                $req['dashboard_filters'] = (array) $filters->dashboard_filters;
                if (isset($req['dashboard_filters']['assigned_to_dept']) && $assigned_to_dept = trim($req['dashboard_filters']['assigned_to_dept'])) {
                    $db->where('u.department_id', '=', $assigned_to_dept);
                }

                if (isset($req['dashboard_filters']['patch_filter']) && $req['dashboard_filters']['patch_filter'] == 1) {
                    $patch_device_ids = explode(',', $req['dashboard_filters']['patch_device_ids']);
                    $db->whereIn('a.id', $patch_device_ids);
                }

                if (isset($req['dashboard_filters']['status']) && $req['dashboard_filters']['status']) {
                    $db->where('a.status_id', '=', $req['dashboard_filters']['status']);
                }

                if (isset($req['dashboard_filters']['audit']) && $req['dashboard_filters']['audit']) {
                    $db->where('a.accepted', '=', (string) $req['dashboard_filters']['audit']);
                    $db->where('a.assigned_for', '=', 1);
                    $db->whereNotNull('a.assigned_to');
                }

                $db->where('a.company_id', '=', Auth::user()->company_id);

                $model_filter = isset($req['dashboard_filters']['filter_model']) ? $req['dashboard_filters']['filter_model'] : 'null';
                if ($model_filter != null && $model_filter != 'null') {
                    $db->whereIn('a.model_id', $model_filter);
                }
                $filter_dep_name = isset($req['dashboard_filters']['filter_dep_name']) ? $req['dashboard_filters']['filter_dep_name'] : 'null';
                if ($filter_dep_name != null && $filter_dep_name != 'null') {
                    $db->where('dept.name', $filter_dep_name);
                    $db->where('a.assigned_for', '=', 1);
                    $db->whereNotNull('a.assigned_to');
                }
                $filter_place_type = isset($req['dashboard_filters']['filter_place_type']) ? $req['dashboard_filters']['filter_place_type'] : 'null';
                $place_filter = isset($req['dashboard_filters']['filter_place']) ? $req['dashboard_filters']['filter_place'] : 'null';
                if ($place_filter != null && $place_filter != 'null' && $filter_place_type != null && $filter_place_type != 'null') {
                    if ($filter_place_type == 'checkout_place') {
                        $db->where('a.assigned_for', 2);
                        $db->where('a.status_id', 6);
                        $db->where('p.place', $place_filter);
                    } else if ($filter_place_type == 'stock_place') {
                        $db->where('a.status_id', '!=', 6);
                        $db->where('pldev.place', $place_filter);
                    } else if ($filter_place_type == 'amc_expired_place') {
                        $db->where('p.place', $place_filter);
                        $now = Carbon::now(config('app.timezone'))->Format('Y-m-d');
                        $db->where('a.amc_expire_date', $now);
                    }
                }
                $location_filter = isset($req['dashboard_filters']['location']) ? $req['dashboard_filters']['location'] : 'null';
                if ($location_filter != null && $location_filter != 'null') {
                    $db->whereIn('a.rtd_location_id', explode(',', $location_filter));
                }
                $assign_user_location_filter = isset($req['dashboard_filters']['assign_user_location']) ? $req['dashboard_filters']['assign_user_location'] : 'null';
                if ($assign_user_location_filter != null && $assign_user_location_filter != 'null') {
                    if ($assign_user_location_filter != null && $assign_user_location_filter != 'null') {
                        $db->where(function ($query) use ($assign_user_location_filter) {
                            $query->where('a.assigned_for', 1)->where('a.status_id', 6)->whereIn('u.location_id', explode(',', $assign_user_location_filter));
                        });
                    }
                }
                $assign_user_internalplace_filter = isset($req['dashboard_filters']['assign_user_internalplace']) ? $req['dashboard_filters']['assign_user_internalplace'] : 'null';
                if ($assign_user_internalplace_filter != null && $assign_user_internalplace_filter != 'null') {
                    if ($assign_user_internalplace_filter != null && $assign_user_internalplace_filter != 'null') {
                        $db->where(function ($query) use ($assign_user_internalplace_filter) {
                            $query->where('a.assigned_for', 1)->where('a.status_id', 6)->whereIn('u.internal_place_id', explode(',', $assign_user_internalplace_filter));
                        });
                    }
                }
                $department_filter = isset($req['dashboard_filters']['department']) ? $req['dashboard_filters']['department'] : 'null';
                if ($department_filter != null && $department_filter != 'null') {
                    $db->whereIn('a.department_id', explode(',', $department_filter));
                }
                $added_from_filter = isset($req['dashboard_filters']['added_from']) ? $req['dashboard_filters']['added_from'] : 'null';
                if ($added_from_filter != null && $added_from_filter != 'null') {
                    if ($added_from_filter == 1) {
                        $db
                            ->whereNull('itm.device_id')
                            ->whereNull('itm_azure.device_id');
                    } elseif ($added_from_filter == 2) {
                        $db->whereNotNull('itm.device_id');
                    } elseif ($added_from_filter == 3) {
                        $db->whereNotNull('itm_azure.device_id');
                    }
                }

                if (isset($req['dashboard_filters']['createdFrom']) && $req['dashboard_filters']['createdFrom'] && $req['dashboard_filters']['createdFrom'] != 'null' && isset($req['dashboard_filters']['createdTo']) && $req['dashboard_filters']['createdTo'] && $req['dashboard_filters']['createdTo'] != 'null') {
                    $from_date = CommonHelper::getDateAs($req['createdFrom'], 'Y-m-d', 'd/m/Y');
                    $to_date = CommonHelper::getDateAs($req['createdTo'], 'Y-m-d', 'd/m/Y');
                    if ($from_date && $to_date) {
                        $db->wherebetween('a.created_at', [$from_date, $to_date]);
                    }
                }
                if (isset($req['dashboard_filters']['req_status']) && $req['dashboard_filters']['req_status'] && $req['dashboard_filters']['req_status'] != 'null') {
                    $db->where('a.status_id', $req['dashboard_filters']['req_status']);
                }

                if (isset($req['dashboard_filters']['req_year']) && $req['dashboard_filters']['req_year'] && $req['dashboard_filters']['req_year'] != 'null') {
                    $year_range = explode('-', $req['dashboard_filters']['req_year']);
                    if (!empty($year_range) && isset($year_range[1]) && $year_range != 'null') {
                        $startDate = Carbon::createFromDate($year_range[0], 4, 1)->startOfDay();
                        $endDate = Carbon::createFromDate($year_range[1], 3, 31)->endOfDay();
                        $db->whereBetween('a.purchase_date', [$startDate, $endDate]);
                    } else {
                        $db->whereYear('a.purchase_date', $req['dashboard_filters']['req_year']);
                    }
                }
            }

            if (isset($filters->status_id)) {
                $req['status_id'] = $filters->status_id;
            }
            if (isset($filters->search)) {
                $req['search'] = $filters->search;
            }
            if (isset($filters->other_filters)) {
                $req['filters'] = (array) $filters->other_filters;
            }
            if (isset($filters->showDeletedDevices)) {
                $req['showDeletedDevices'] = $filters->showDeletedDevices;
            }
            if (isset($filters->print_label_id) && !empty($filters->print_label_id)) {
                $db->whereIn('a.id', $filters->print_label_id);
            }

            if (isset($filters->over_all_purchase_filter)) {
                $ids = array_map('intval', explode(',', trim(base64_decode($filters->over_all_purchase_filter), '"')));
                $db->whereIn('a.id', $ids);
            }

            if (isset($req['filters'])) {
                $filters = $req['filters'];

                $db->where(function ($query) use ($filters) {
                    if (isset($filters['manufacturer']) && $filters['manufacturer'] && $filters['manufacturer'] != 'null') {
                        $query->where('mdl.manufacturer_id', '=', (int) $filters['manufacturer']);
                    }
                    if (isset($filters['model']) && $filters['model'] && $filters['model'] != 'null') {
                        $query->whereIn('a.model_id', $filters['model']);
                    }
                    if (isset($filters['category']) && $filters['category'] && $filters['category'] != 'null') {
                        $query->whereIn('mdl.category_id', $filters['category']);
                    }
                    if (isset($filters['location']) && $filters['location'] && $filters['location'] != 'null') {
                        $query->whereIn('a.rtd_location_id', $filters['location']);
                    }
                    if (isset($filters['assigned_user']) && $filters['assigned_user'] && $filters['assigned_user'] != 'null') {
                        $query->where('a.assigned_to', '=', (int) $filters['assigned_user']);
                        $query->where('a.assigned_for', '=', 1);
                    }
                    if (isset($filters['purchase_reference']) && !empty($filters['purchase_reference']) && $filters['purchase_reference'] != 'null') {
                        $query->whereIn('a.invoice_id', $filters['purchase_reference']);
                    }
                    if (isset($filters['stock_location']) && $filters['stock_location'] && $filters['stock_location'] != 'null') {
                        $query->whereIn('pldevloc.id', $filters['stock_location']);
                        $query->where('a.assigned_for', '=', null);
                    }
                    if (isset($filters['assigned_place']) && $filters['assigned_place'] && $filters['assigned_place'] != 'null') {
                        $query->whereIn('a.assigned_to', $filters['assigned_place']);
                        $query->where('a.assigned_for', '=', 2);
                    }
                    if (isset($filters['stock_place']) && $filters['stock_place'] && $filters['stock_place'] != 'null') {
                        if ($filters['stock_place'] == 'empty_stock_place') {
                            $query->where('a.stock_place', '=', null);
                            $query->where('a.assigned_for', '=', null);
                        } else {
                            $query->whereIn('a.stock_place', $filters['stock_place']);
                            $query->where('a.assigned_for', '=', null);
                        }
                    }
                    if (isset($filters['asset_type_id']) && $filters['asset_type_id'] && $filters['asset_type_id'] != 'null') {
                        $query->whereIn('a.asset_type_id', $filters['asset_type_id']);
                    }
                    if (isset($filters['device_occure_type']) && $filters['device_occure_type'] && $filters['device_occure_type'] != 'null') {
                        $query->whereIn('a.device_occure_type', $filters['device_occure_type']);
                    }
                    if (isset($filters['last_checkout_project']) && $filters['last_checkout_project'] && $filters['last_checkout_project'] != 'null') {
                        $query->where('a.last_checkout_project', '=', (int) $filters['last_checkout_project']);
                    }
                    if (isset($filters['department']) && $filters['department'] && $filters['department'] != 'null') {
                        $query->where('u.department_id', '=', (int) $filters['department']);
                    }
                    if (isset($filters['asset_owner']) && $filters['asset_owner'] && $filters['asset_owner'] != 'null') {
                        $query->where('a.asset_owner', '=', (int) $filters['asset_owner']);
                    }
                    if (isset($filters['device_assigned_to']) && $filters['device_assigned_to'] && $filters['device_assigned_to'] != 'null') {
                        $query->where('a.assigned_for', '=', (int) $filters['device_assigned_to']);
                    }
                    if (isset($filters['added_from']) && $filters['added_from'] && $filters['added_from'] != 'null') {
                        $query->where('a.added_from', '=', (int) $filters['added_from']);
                    }
                    if (isset($filters['detected_on']) && $filters['detected_on'] && $filters['detected_on'] != 'null') {
                        if ($filters['detected_on'] == 1) {
                            $query->whereNull('itm.device_id')->whereNull('itm_azure.device_id');
                        } else if ($filters['detected_on'] == 2) {
                            $query->whereNotNull('itm.device_id');
                        } else if ($filters['detected_on'] == 3) {
                            $query->whereNotNull('itm_azure.device_id');
                        }
                    }
                    if (isset($filters['allocation_type']) && $filters['allocation_type'] && $filters['allocation_type'] != 'null') {
                        $query->where('ata.id', '=', (int) $filters['allocation_type']);
                    }
                    if (isset($filters['audit_confirmation']) && $filters['audit_confirmation'] && $filters['audit_confirmation'] != 'null') {
                        $query->where('a.accepted', '=', (string) $filters['audit_confirmation']);
                        $query->where('a.assigned_for', '=', 1);
                        $query->whereNotNull('a.assigned_to');
                    }
                    if (isset($filters['asset_tag']) && $filters['asset_tag'] && $filters['asset_tag'] != 'null') {
                        $query->whereIn('a.id', $filters['asset_tag']);
                    }
                    if (isset($filters['user_location']) && $filters['user_location'] && $filters['user_location'] != 'null') {
                        $query->where('a.status_id', 6);
                        $query->where('a.assigned_for', 1);
                        $query->whereIn('u.location_id', $filters['user_location']);
                    }
                    if (isset($filters['user_internalplace']) && $filters['user_internalplace'] && $filters['user_internalplace'] != 'null') {
                        $query->where('a.status_id', 6);
                        $query->where('a.assigned_for', 1);
                        $query->whereIn('u.internal_place_id', $filters['user_internalplace']);
                    }
                    if (isset($filters['user_base_location']) && $filters['user_base_location'] && $filters['user_base_location'] != 'null') {
                        $query->where('a.status_id', 6);
                        $query->whereIn('u.base_location_id', $filters['user_base_location']);
                    }
                    if (isset($filters['asset_department']) && $filters['asset_department'] && $filters['asset_department'] != 'null') {
                        $query->whereIn('a.department_id', $filters['asset_department']);
                    }
                    if (isset($filters['sez_device']) && $filters['sez_device'] != 'null') {
                        $query->where('a.sez_device', (int) $filters['sez_device']);
                    }
                    if (isset($filters['high_priority_device']) && $filters['high_priority_device'] != 'null') {
                        $query->where('a.high_pririty', (int) $filters['high_priority_device']);
                    }
                    if (isset($filters['requestable']) && $filters['requestable'] != 'null') {
                        $query->where('a.requestable', (int) $filters['requestable']);
                    }
                    if (isset($filters['under_transfer_device']) && $filters['under_transfer_device'] != 'null') {
                        if ($filters['under_transfer_device'] == '1') {
                            $query->whereExists(function ($q) {
                                $q
                                    ->select(DB::raw(1))
                                    ->from('transfer_items as ti')
                                    ->whereRaw('ti.device_id = a.id')
                                    ->where('ti.transfer_status', '=', 1);
                            });
                        } else {
                            $query->whereNotExists(function ($q) {
                                $q
                                    ->select(DB::raw(1))
                                    ->from('transfer_items as ti')
                                    ->whereRaw('ti.device_id = a.id')
                                    ->where('ti.transfer_status', '=', 1);
                            });
                        }
                    }

                    $now = Carbon::now(config('app.timezone'))->Format('Y-m-d');
                    if (isset($filters['warranty_status']) && $filters['warranty_status'] && $filters['warranty_status'] != 'null') {
                        if ($filters['warranty_status'] == 1) {
                            $query->where('a.calc_warranty_expire_date', '>=', $now);
                        } elseif ($filters['warranty_status'] == 2) {
                            $query->whereRaw("(a.warranty_status = 2 or a.calc_warranty_expire_date < '" . $now . "')");
                        } elseif ($filters['warranty_status'] == 3) {
                            $query->where('a.warranty_status', '=', 3);
                        }
                    }

                    $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.last_checkout', '3' => 'a.expected_checkin', '4' => 'a.amc_expire_date', '5' => 'a.warranty_start_date', '6' => 'a.warrenty_end_date', '7' => 'a.created_at', '8' => 'u.doj', '9' => 'a.calc_warranty_expire_date'];
                    if (isset($filters['based_on']) && $filters['based_on'] && $filters['based_on'] != 'null' && $filters['based_on'] >= 1 && $filters['based_on'] <= 9) {
                        if (isset($filters['date_range']) && $filters['date_range'] && $filters['date_range'] != 'null') {
                            $daterange = explode(' - ', $filters['date_range']);
                            $from_date = date('Y-m-d H:i:s', strtotime($daterange[0]));
                            $to_date = date('Y-m-d H:i:s', strtotime($daterange[1]));
                            if ($from_date && $to_date) {
                                if ($filters['based_on'] == 3 || $filters['based_on'] == 8) {
                                    $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s" and a.status_id = 6)', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                                } else {
                                    $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                                }
                                $query->whereRaw($whereStr);
                            }
                        }
                    }

                    if (isset($filters['internal_place']) && $filters['internal_place'] && $filters['internal_place'] != 'null') {
                        $query->whereIn('a.internal_place_id', $filters['internal_place']);
                    }
                    if (isset($filters['filter_by_device_with_rfid']) && $filters['filter_by_device_with_rfid'] && $filters['filter_by_device_with_rfid'] != 'null') {
                        $rfidDevices = DeviceRfid::groupBy('device_id')->pluck('device_id')->toArray();
                        if ($filters['filter_by_device_with_rfid'] == 1) {
                            $query->whereIn('a.id', $rfidDevices);
                        } else {
                            $query->whereNotIn('a.id', $rfidDevices);
                        }
                    }

                    if (isset($filters['custom_field']) && $filters['custom_field'] && $filters['custom_field'] != 'null') {
                        $customField = CustomField::find($filters['custom_field']);
                        $dev_name = '_itm_' . '' . str_replace(' ', '_', strtolower($customField->name));
                        $device_custom_field = Device::select($dev_name . ' as text')->where($dev_name, '!=', null)->get()->toArray();
                        if (isset($filters['custom_field_value']) && $filters['custom_field_value'] && $filters['custom_field_value'] != 'null') {
                            $query->whereIn($dev_name, $filters['custom_field_value']);
                        }
                    }

                    if (isset($filters['mapped_location']) && $filters['mapped_location'] && $filters['mapped_location'] != 'null') {
                        $query->whereIn('a.ni_detected_location', $filters['mapped_location']);
                    }
                });
            }

            if (isset($req['status_id']) && $req['status_id']) {
                $db->where('a.status_id', '=', $req['status_id']);
            }

            if (isset($req['search']) && $search_key = trim($req['search'])) {
                $whereStr = sprintf('(a.asset_tag like "%%%1$s%%" or a.product_number like "%%%1$s%%" or (a.assigned_for = 1 and u.employee_num like "%%%1$s%%") or a.uuid like "%%%1$s%%" or a.ip like "%%%1$s%%" or a.order_number like "%%%1$s%%" or a.mac like "%%%1$s%%" or (case when a.added_from = 1 then "Manually Added" when a.added_from = 2 then "Via Network" when a.added_from = 3 then "Via Azure" else "" end) like "%%%1$s%%" or (case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" when a.device_occure_type = 0 then "Purchase Device" else "" end) like "%%%1$s%%" or (case when a.high_pririty = 1 then "High Priority" else "" end) like "%%%1$s%%" or (case when a.stock_place is not null and a.assigned_for is null then "Stock Place" else "" end) like "%%%1$s%%" or (case when a.assigned_for = 1 then "Checkeout to User" when a.assigned_for = 2 then "Checkout to Place" else "" end ) like "%%%1$s%%" or cat.name like "%%%1$s%%" or (case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end) like "%%%1$s%%" or a.name like "%%%1$s%%" or mdl.name like "%%%1$s%%" or mnu.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or pla.place like "%%%1$s%%" or cmp.name like "%%%1$s%%" or ploc.name like "%%%1$s%%" or (case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end) like "%%%1$s%%" or (a.assigned_for = 1 and u.first_name like "%%%1$s%%") or (a.assigned_for = 2 and p.place like "%%%1$s%%") or (a.assigned_for = 1 and u.last_name like "%%%1$s%%") or concat(u.first_name, " ", u.last_name) like "%%%1$s%%" or a.serial like "%%%1$s%%" or ni_loc.name like "%%%1$s%%" or concat(own.first_name, " ", own.last_name) like "%%%1$s%%" or DATE_FORMAT(a.last_checkout, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.calc_warranty_expire_date, "%%d %%b %%Y") like "%%%1$s%%")', $search_key);
                $db->whereRaw($whereStr);
            }
        }

        if (isset($req['showDeletedDevices']) && $req['showDeletedDevices'] == 'true') {
            $db->whereNotNull('a.deleted_at');
        } else {
            $db->whereNull('a.deleted_at');
        }

        if ($export_column_empty == true) {
            $title_row = ['Company', 'Device Name', 'Device Tag', 'Serial', 'Manufacture', 'Model Name', 'Category', 'Status', 'Location', 'Internal Place', 'Purchase Date', 'Purchase Currency', 'Purchase Cost', 'Purchase Invoice', 'Ship Date', 'Warranty Start', 'Warranty End', 'Warranty(Months)', 'Notes', 'Order Number', 'Asset Owner', 'IP', 'MAC', 'Device From', 'Stock Location', 'Stock Place', 'Last Network Location', 'Supplier', 'Project Name', 'Warranty Expire Date', 'Warranty Status', 'Account Type', 'UUID', 'Checkout Date', 'Checkout Status', 'Checkout Accepted Date', 'User/Place', 'Username/Placename/Status', 'Assigned User', 'Assigned Username', 'Assigned User Email', 'Assigned User Emp. Code', 'Assigned User Location', 'Assigned User Place', 'Assigned User DOJ', 'External User Company', 'Assigned Place', 'Expected Checkin Date', 'Contract End Date', 'AMC Supplier', 'AMC Expire Date', 'AMC Expire Status', 'OEM Warranty', 'Device Type', 'Allocation Type', 'Business Unit', 'Delivery Unit', 'User Department', 'Manager', 'Requestable', 'High Priority', 'Sez Device', 'Last NI Scan Date', 'Installed Date', 'Azure AD Device Id', 'Last Sync Date Time', 'Compliance State', 'Updated At', 'Department', 'HddSize', 'RAMSize', 'Processor', 'OSCaption', 'NI Exist', 'Azure Exist', 'RDP Enable', 'Device RFID'];
        } else {
            $title_row = $device_export_column;
            $title_row = array_filter($title_row, function ($item) {
                return $item !== 'Custom Fields';
            });
            $title_row = array_values($title_row);
        }
        // foreach($custom_fields as $cf) {
        //     $title_row[] = $custom_field_names[$cf];
        // }

        if (in_array('Custom Fields', $device_export_column) || $export_column_empty == true) {
            if ($settings->custom_fieldset_id != '') {
                $customFieldset = CustomFieldset::find($settings->custom_fieldset_id);
                if (!empty($customFieldset->fields)) {
                    foreach ($customFieldset->fields as $f) {
                        $col_name = ucwords(str_replace(['_itm_', '_'], ' ', $f->name));
                        array_push($title_row, $col_name);
                    }
                }
            }
        }
        if (isset($filters['device_size']) && $filters['device_size'] != 'null') {
            $data = $db->get()->toArray();
            if (!empty($data) && isset($filters['device_size']) && $filters['device_size'] != 'null') {
                $data = array_filter($data, function ($item) use ($filters) {
                    if (isset($filters['condition_by_user_assign']) && $filters['condition_by_user_assign'] != 'null') {
                        if ($filters['condition_by_user_assign'] == 1) {
                            return $item->assigned_count > $filters['device_size'] && $item->assigned_for == 1;
                        } elseif ($filters['condition_by_user_assign'] == 2) {
                            return $item->assigned_count < $filters['device_size'] && $item->assigned_for == 1;
                        } else {
                            return $item->assigned_count == $filters['device_size'] && $item->assigned_for == 1;
                        }
                    }
                    return $item->assigned_count == $filters['device_size'] && $item->assigned_for == 1;
                });
                $data = array_values($data);
            }
            $results = $data;
        } else {
            $results = $db->get();
        }
        $lineArray = [];
        foreach ($results as $key => $val) {
            $valArray = json_decode(json_encode($val), true);
            if (in_array('Custom Fields', $device_export_column) || $export_column_empty == true) {
                foreach ($valArray as $k => $v) {
                    if (strpos($k, '_itm_') === 0) {
                        unset($valArray[$k]);
                    }
                }

                if ($settings->custom_fieldset_id) {
                    $customFieldset = CustomFieldset::find($settings->custom_fieldset_id);
                    if (!empty($customFieldset)) {
                        $customaCol = json_decode(json_encode($val), true);
                        $fieldData = CommonHelper::getCustomData($customFieldset->fields, $customaCol);
                        $dataFieldset = [];
                        foreach ($fieldData as $k => $f) {
                            $dataFieldset[$k] = $f;
                        }
                        $valArray = array_merge($valArray, $dataFieldset);
                    }
                }
            }
            $lineArray[] = $valArray;
        }
        $result = json_decode(json_encode($lineArray, true), true);
        return Excel::download(new DeviceExport($result, $title_row), 'Device.xlsx');
    }

    public function deviceExportExcelForType(Request $request, $type)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('DeviceDownload') || !config('services.assets.enabled')) {
            return redirect('dashboard')->with('msg', $return);
        }
        $req = $request->all();
        $return = array(
            // "draw" => date('is')
        );
        $set = $get_custom_fields = '';
        $setting = Settings::getSettings()->custom_fieldset_id ? Settings::getSettings()->custom_fieldset_id : '';
        if ($setting) {
            $set = 'where cfcf.custom_fieldset_id = ' . $setting;
            $get_custom_fields = DB::select("SELECT cfcf.custom_fieldset_id, CONCAT('a._itm_', replace(lcase(cf.NAME), ' ', '_')) AS cus_field FROM custom_fields AS cf
            JOIN custom_field_custom_fieldset AS cfcf ON cf.id = cfcf.custom_field_id $set");
        }

        $coll_field_sets = [];
        $field_sets = [];
        $coll_custom_fields = [];
        $custom_fields = [];
        $custom_field_names = [];
        $field_based_fieldsets_ids = [];
        if ($get_custom_fields) {
            foreach ($get_custom_fields as $cf) {
                $coll_field_sets[] = $cf->custom_fieldset_id;
                $coll_custom_fields[] = $cf->cus_field;
                if (!isset($field_based_fieldsets_ids[$cf->cus_field])) {
                    $field_based_fieldsets_ids[$cf->cus_field] = [$cf->custom_fieldset_id];
                } else {
                    $field_based_fieldsets_ids[$cf->cus_field][] = $cf->custom_fieldset_id;
                }
            }

            $field_sets = array_unique($coll_field_sets);
            $custom_fields = array_unique($coll_custom_fields);

            foreach ($custom_fields as $cf) {
                $n1 = str_replace('a._itm_', ' ', $cf);
                $n2 = str_replace('_', ' ', $n1);
                $custom_field_names[$cf] = ucwords($n2);
            }
        }

        $db = DB::table('assets as a');
        $db->leftJoin('asset_logs as al', 'al.id', '=', 'a.chkout_acceptance_log_id');
        $db->leftJoin('models as mdl', 'mdl.id', '=', 'a.model_id');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'mdl.manufacturer_id');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.rtd_location_id');
        $db->leftJoin('locations as ni_loc', 'ni_loc.id', '=', 'a.ni_detected_location');
        $db->leftJoin('places as pla', 'pla.id', '=', 'a.internal_place_id');
        $db->leftJoin('locations as locByNi', 'locByNi.id', '=', 'a.ni_detected_location');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');
        // $db->leftJoin('users as u', 'u.id', '=', 'a.assigned_to');
        $db->leftJoin('users as u', function ($q) {
            $q->on('u.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '1');
        });
        $db->leftJoin('locations as assigned_user_loc', function ($q) {
            $q->on('assigned_user_loc.id', '=', 'u.location_id');
            $q->where('a.assigned_for', '=', '1');
        });

        $db->leftJoin('users as own', 'own.id', '=', 'a.asset_owner');
        $db->leftJoin('lease_agreements as lease', 'lease.id', '=', 'a.lease_id');
        $db->leftJoin('projects as pr', 'pr.id', '=', 'a.last_checkout_project');
        $db->leftJoin('status_labels as lbl', 'lbl.id', '=', 'a.status_id');
        $db->leftJoin('categories as cat', function ($q) {
            $q->on('cat.id', '=', 'mdl.category_id');
            $q->where('mdl.category_id', '<>', 0);
        });
        $db->leftJoin('procure_account_types as acc_typ', 'acc_typ.id', '=', 'cat.account_type_id');
        $db->leftJoin('places as pldev', function ($q) {
            $q->on('pldev.id', '=', 'a.stock_place');
        });
        $db->leftJoin('places as p', function ($q) {
            $q->on('p.id', '=', 'a.assigned_to');
            $q->where('a.assigned_for', '=', '2');
        });
        $db->leftJoin('users as manager', 'manager.id', '=', 'u.manager_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->leftJoin('departments as dept', 'dept.id', '=', 'u.department_id');
        $db->leftJoin('locations as pldevloc', 'pldevloc.id', '=', 'pldev.location_id');
        $db->leftJoin('locations as ploc', 'ploc.id', '=', 'p.location_id');
        $db->leftJoin('suppliers as s', 'a.supplier_id', '=', 's.id');
        $db->leftJoin('suppliers as amc_supp', 'a.amc_supplier_id', '=', 'amc_supp.id');
        $db->leftJoin('assets_types as at', 'a.asset_type_id', '=', 'at.id');
        $db->leftJoin('asset_logs as al1', function ($join) {
            $join->on('a.chkout_log_id', '=', 'al1.id');
            $join->on('a.status_id', '=', DB::raw('6'));
        });
        $db->leftJoin('itm_asset_allocation_type as ata', 'ata.id', '=', 'al1.allocation_type_id');
        $db->leftJoin('itm_network_inventory_basic as itm', 'itm.device_id', '=', 'a.id');
        $db->leftJoin('itm_network_inventory_basic_azure as itm_azure', 'itm_azure.device_id', '=', 'a.id');

        $db->select(
            DB::raw('(SELECT COUNT(*) FROM assets WHERE assigned_to = a.assigned_to) as assigned_count'),
            'a.assigned_for',
            'cmp.name as cmp_name',
            'a.id',
            'a.name',
            'a.asset_tag',
            'a.serial',
            'a.product_number',
            'mnu.name as manu_name',
            DB::raw('trim(concat_ws(" ", mdl.name, mdl.modelno)) as mdl_full_name'),
            'cat.name as cat_name',
            DB::raw('case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end as lbl_name'),
            'loc.name as loc_name',
            'pla.place as internal_place',
            DB::raw('DATE_FORMAT(a.purchase_date, "%Y-%m-%d") as purchase_date_on'),
            'a.purchase_currency',
            DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'),
            'pur.invoice_no as pur_invoice',
            DB::raw('DATE_FORMAT(a.warranty_start_date, "%Y-%m-%d") as warranty_start_date'),
            DB::raw('DATE_FORMAT(a.warrenty_end_date, "%Y-%m-%d") as warranty_end_date'),
            'a.warranty_months',
            'a.notes',
            'a.order_number',
            'own.username as asset_owner',
            'a.ip',
            'a.mac',
            DB::raw('case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" else "Purchase Device" end as device_occure_type_name'),
            DB::raw('case when a.stock_place is not null and pldev.place is not null then pldev.place else null end as stock_place'),
            'locByNi.name as locByNiName',
            's.name as supplier_name',
            DB::raw('case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end as last_checkout_project'),
            DB::raw('case when ((a.warranty_status = 1 or a.warranty_status = 2) and a.calc_warranty_expire_date is not null) then DATE_FORMAT(a.calc_warranty_expire_date, "%Y-%m-%d") else "" end as consolidated_warrenty_end_date'),
            DB::raw('case when a.warranty_status = 1 then "Expired" when a.warranty_status = 2 then "Not Expired" when a.warranty_status = 3 then "Not Applicable" when a.warranty_status = 4 then "Warranty End Date and OEM Date not matching" when a.warranty_status = 5 then "Purchase Date and OEM Date not matching" when a.warranty_status = 6 then "Invoice Date and OEM Date not matching" else "" end as warranty_status_text'),
            DB::raw('case when acc_typ.name is not null then acc_typ.name else "" end as acc_type_name'),
            'a.uuid',
            DB::raw('case when a.assigned_to is not null then DATE_FORMAT(a.last_checkout, "%Y-%m-%d") else "" end as chkout_date'),
            'a.accepted',
            DB::raw("case when (a.chkout_acceptance_log_id is not null and a.accepted = 'accepted') then date_format(al.created_at, '%Y-%m-%d') else '' end as chkout_acceptance_date"),
            DB::raw('case when a.assigned_for = 1 then "User" when a.assigned_for = 2 then "Place" end as user_or_place'),
            DB::raw('concat(u.first_name, " ", u.last_name) as full_name'),
            'u.username as ckout_username',
            'u.email as ckout_email',
            'u.employee_num as ckout_emp_num',
            'assigned_user_loc.name as assigned_user_loc_name',
            DB::raw('case when u.job_type in (1,2) then u.ex_user_company else null end as ex_user_company'),
            DB::raw('case when a.assigned_for = 2 then concat(p.place, " ", ploc.name, "") else null end as chkout_place'),
            DB::raw('DATE_FORMAT(a.expected_checkin, "%Y-%m-%d") as expected_checkin_on'),
            DB::raw('case when a.device_occure_type = 1 then DATE_FORMAT(lease.end_date, "%Y-%m-%d") else "" end as lease_end_date'),
            'amc_supp.name as amc_supp_name',
            DB::raw("case when (dayname(a.amc_expire_date) is not null) then date_format(a.amc_expire_date, '%Y-%m-%d') else null end as amc_ed_format, IF((dayname(a.amc_expire_date) is not null) and a.amc_expire_date < CURRENT_TIMESTAMP, 'Expired', '') as 'is_amc_expired'"),
            'a.manufacturer_warranty_data',
            'at.name as asset_type_name',
            'ata.name as allocation_type',
            'u.business_unit',
            'u.delivery_unit',
            'dept.name as user_department',
            'manager.email',
            DB::raw('case when a.requestable = 1 then "Yes" else "No" end as requestable'),
            DB::raw('case when a.high_pririty = 1 then "Yes" else "No" end as high_pririty'),
            DB::raw('case when a.sez_device = 1 then "Yes" else "No" end as sez_device'),
            DB::raw('DATE_FORMAT(itm.updated_at, "%Y-%m-%d") as ni_last_updated_at'),
            DB::raw('DATE_FORMAT(a.created_at, "%Y-%m-%d") as installed_at_format'),
            DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as updated_at'),
            'dep.name as department',
            'itm.HddSize',
            'itm.RamSize',
            'itm.ProcessorName',
            'itm.OSCaption',
            DB::raw('case when itm.id is not null then "Yes" else "No" end as ni_exits'),
            DB::raw('case when itm_azure.id is not null then "Yes" else "No" end as azure_exits'),
            DB::raw('case when a.rdp_status = 1 AND a.node_id is not null then "Yes" else "No" end as rdp_enable')
        );

        $loc_previllage = Auth::user()->permitted_locations;  // getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(',', $loc_previllage);
        if ($settings->location_config == 1) {
            if (empty($loc_previllage)) {
                $db->where('a.rtd_location_id', '=', 0);
            } else {
                $db->whereIn('a.rtd_location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id;  // check from user's table
            $asset_depts = explode(',', $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }

        if (is_array($coll_custom_fields) and count($coll_custom_fields)) {
            foreach ($coll_custom_fields as $coll_custom_field) {
                $clm_name = substr($coll_custom_field, 2);
                if (Schema::hasColumn('assets', $clm_name)) {
                    $db->addSelect($coll_custom_field);
                }
            }
        }
        $db->where('a.company_id', '=', Auth::user()->company_id);
        if (isset($req['showDeletedDevices']) && $req['showDeletedDevices'] == 'true') {
            $db->whereNotNull('a.deleted_at');
        } else {
            $db->whereNull('a.deleted_at');
        }

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if (isset($filters->status_id)) {
                $req['status_id'] = $filters->status_id;
            }

            if (isset($filters->search)) {
                $req['search'] = $filters->search;
            }
            if (isset($filters->other_filters)) {
                $req['filters'] = (array) $filters->other_filters;
            }

            if (isset($filters->print_label_id) && !empty($filters->print_label_id)) {
                $db->whereIn('a.id', $filters->print_label_id);
            }

            if (isset($req['filters'])) {
                $filters = $req['filters'];

                $db->where(function ($query) use ($filters) {
                    if (isset($filters['manufacturer']) && $filters['manufacturer'] && $filters['manufacturer'] != 'null') {
                        $query->where('mdl.manufacturer_id', '=', (int) $filters['manufacturer']);
                    }
                    if (isset($filters['model']) && $filters['model'] && $filters['model'] != 'null') {
                        $query->whereIn('a.model_id', $filters['model']);
                    }
                    if (isset($filters['category']) && $filters['category'] && $filters['category'] != 'null') {
                        $query->whereIn('mdl.category_id', $filters['category']);
                    }
                    if (isset($filters['location']) && $filters['location'] && $filters['location'] != 'null') {
                        $query->whereIn('a.rtd_location_id', $filters['location']);
                    }
                    if (isset($filters['assigned_user']) && $filters['assigned_user'] && $filters['assigned_user'] != 'null') {
                        $query->where('a.assigned_to', '=', (int) $filters['assigned_user']);
                        $query->where('a.assigned_for', '=', 1);
                    }
                    if (isset($filters['purchase_reference']) && !empty($filters['purchase_reference']) && $filters['purchase_reference'] != 'null') {
                        $query->whereIn('a.invoice_id', $filters['purchase_reference']);
                    }
                    if (isset($filters['stock_location']) && $filters['stock_location'] && $filters['stock_location'] != 'null') {
                        $query->whereIn('pldevloc.id', $filters['stock_location']);
                        $query->where('a.assigned_for', '=', null);
                    }
                    if (isset($filters['assigned_place']) && $filters['assigned_place'] && $filters['assigned_place'] != 'null') {
                        $query->whereIn('a.assigned_to', $filters['assigned_place']);
                        $query->where('a.assigned_for', '=', 2);
                    }
                    if (isset($filters['stock_place']) && $filters['stock_place'] && $filters['stock_place'] != 'null') {
                        if ($filters['stock_place'] == 'empty_stock_place') {
                            $query->where('a.stock_place', '=', null);
                            $query->where('a.assigned_for', '=', null);
                        } else {
                            $query->whereIn('a.stock_place', $filters['stock_place']);
                            $query->where('a.assigned_for', '=', null);
                        }
                    }
                    if (isset($filters['asset_type_id']) && $filters['asset_type_id'] && $filters['asset_type_id'] != 'null') {
                        $query->whereIn('a.asset_type_id', $filters['asset_type_id']);
                    }
                    if (isset($filters['device_occure_type']) && $filters['device_occure_type'] && $filters['device_occure_type'] != 'null') {
                        $query->whereIn('a.device_occure_type', $filters['device_occure_type']);
                    }
                    if (isset($filters['last_checkout_project']) && $filters['last_checkout_project'] && $filters['last_checkout_project'] != 'null') {
                        $query->where('a.last_checkout_project', '=', (int) $filters['last_checkout_project']);
                    }
                    if (isset($filters['department']) && $filters['department'] && $filters['department'] != 'null') {
                        $query->where('u.department_id', '=', (int) $filters['department']);
                    }
                    if (isset($filters['asset_owner']) && $filters['asset_owner'] && $filters['asset_owner'] != 'null') {
                        $query->where('a.asset_owner', '=', (int) $filters['asset_owner']);
                    }
                    if (isset($filters['device_assigned_to']) && $filters['device_assigned_to'] && $filters['device_assigned_to'] != 'null') {
                        $query->where('a.assigned_for', '=', (int) $filters['device_assigned_to']);
                    }
                    if (isset($filters['added_from']) && $filters['added_from'] && $filters['added_from'] != 'null') {
                        $query->where('a.added_from', '=', (int) $filters['added_from']);
                    }
                    if (isset($filters['detected_on']) && $filters['detected_on'] && $filters['detected_on'] != 'null') {
                        if ($filters['detected_on'] == 1) {
                            $query->whereNull('itm.device_id')->whereNull('itm_azure.device_id');
                        } else if ($filters['detected_on'] == 2) {
                            $query->whereNotNull('itm.device_id');
                        } else if ($filters['detected_on'] == 3) {
                            $query->whereNotNull('itm_azure.device_id');
                        }
                    }
                    if (isset($filters['allocation_type']) && $filters['allocation_type'] && $filters['allocation_type'] != 'null') {
                        $query->where('ata.id', '=', (int) $filters['allocation_type']);
                    }
                    if (isset($filters['audit_confirmation']) && $filters['audit_confirmation'] && $filters['audit_confirmation'] != 'null') {
                        $query->where('a.accepted', '=', (string) $filters['audit_confirmation']);
                        $query->where('a.assigned_for', '=', 1);
                        $query->whereNotNull('a.assigned_to');
                    }
                    if (isset($filters['asset_tag']) && $filters['asset_tag'] && $filters['asset_tag'] != 'null') {
                        $query->whereIn('a.id', $filters['asset_tag']);
                    }
                    if (isset($filters['user_location']) && $filters['user_location'] && $filters['user_location'] != 'null') {
                        $query->where('a.status_id', 6);
                        $query->whereIn('u.location_id', $filters['user_location']);
                    }
                    if (isset($filters['user_base_location']) && $filters['user_base_location'] && $filters['user_base_location'] != 'null') {
                        $query->where('a.status_id', 6);
                        $query->whereIn('u.base_location_id', $filters['user_base_location']);
                    }
                    if (isset($filters['asset_department']) && $filters['asset_department'] && $filters['asset_department'] != 'null') {
                        $query->whereIn('a.department_id', $filters['asset_department']);
                    }
                    if (isset($filters['sez_device']) && $filters['sez_device'] != 'null') {
                        $query->where('a.sez_device', (int) $filters['sez_device']);
                    }
                    if (isset($filters['high_priority_device']) && $filters['high_priority_device'] != 'null') {
                        $query->where('a.high_pririty', (int) $filters['high_priority_device']);
                    }
                    if (isset($filters['requestable']) && $filters['requestable'] != 'null') {
                        $query->where('a.requestable', (int) $filters['requestable']);
                    }
                    if (isset($filters['under_transfer_device']) && $filters['under_transfer_device'] != 'null') {
                        if ($filters['under_transfer_device'] == '1') {
                            $query->whereExists(function ($q) {
                                $q
                                    ->select(DB::raw(1))
                                    ->from('transfer_items as ti')
                                    ->whereRaw('ti.device_id = a.id')
                                    ->where('ti.transfer_status', '=', 1);
                            });
                        } else {
                            $query->whereNotExists(function ($q) {
                                $q
                                    ->select(DB::raw(1))
                                    ->from('transfer_items as ti')
                                    ->whereRaw('ti.device_id = a.id')
                                    ->where('ti.transfer_status', '=', 1);
                            });
                        }
                    }

                    $now = Carbon::now(config('app.timezone'))->Format('Y-m-d');
                    if (isset($filters['warranty_status']) && $filters['warranty_status'] && $filters['warranty_status'] != 'null') {
                        if ($filters['warranty_status'] == 1) {
                            $query->where('a.calc_warranty_expire_date', '>=', $now);
                        } elseif ($filters['warranty_status'] == 2) {
                            $query->whereRaw("(a.warranty_status = 2 or a.calc_warranty_expire_date < '" . $now . "')");
                        } elseif ($filters['warranty_status'] == 3) {
                            $query->where('a.warranty_status', '=', 3);
                        }
                    }

                    $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.last_checkout', '3' => 'a.expected_checkin', '4' => 'a.amc_expire_date', '5' => 'a.warranty_start_date', '6' => 'a.warrenty_end_date', '7' => 'a.created_at', '9' => 'a.calc_warranty_expire_date'];
                    if (isset($filters['based_on']) && $filters['based_on'] && $filters['based_on'] != 'null' && $filters['based_on'] >= 1 && $filters['based_on'] <= 9) {
                        if (isset($filters['date_range']) && $filters['date_range'] && $filters['date_range'] != 'null') {
                            $daterange = explode(' - ', $filters['date_range']);
                            $from_date = date('Y-m-d H:i:s', strtotime($daterange[0]));
                            $to_date = date('Y-m-d H:i:s', strtotime($daterange[1]));
                            if ($from_date && $to_date) {
                                if ($filters['based_on'] == 3) {
                                    $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s" and a.status_id = 6)', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                                } else {
                                    $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                                }
                                $query->whereRaw($whereStr);
                            }
                        }
                    }

                    if (isset($filters['internal_place']) && $filters['internal_place'] && $filters['internal_place'] != 'null') {
                        $query->whereIn('a.internal_place_id', $filters['internal_place']);
                    }

                    if (isset($filters['custom_field']) && $filters['custom_field'] && $filters['custom_field'] != 'null') {
                        $customField = CustomField::find($filters['custom_field']);
                        $dev_name = '_itm_' . '' . str_replace(' ', '_', strtolower($customField->name));
                        $device_custom_field = Device::select($dev_name . ' as text')->where($dev_name, '!=', null)->get()->toArray();
                        if (isset($filters['custom_field_value']) && $filters['custom_field_value'] && $filters['custom_field_value'] != 'null') {
                            $query->whereIn($dev_name, $filters['custom_field_value']);
                        }
                    }
                });
            }

            if (isset($req['status_id']) && $req['status_id']) {
                $db->where('a.status_id', '=', $req['status_id']);
            }

            if (isset($req['search']) && $search_key = trim($req['search'])) {
                $whereStr = sprintf('(a.asset_tag like "%%%1$s%%" or (a.assigned_for = 1 and u.employee_num like "%%%1$s%%") or a.uuid like "%%%1$s%%" or a.product_number like "%%%1$s%%" or a.ip like "%%%1$s%%" or a.order_number like "%%%1$s%%" or a.mac like "%%%1$s%%" or ni_loc.name like "%%%1$s%%" or (case when a.added_from = 1 then "Manually Added" when a.added_from = 2 then "Via Network" when a.added_from = 3 then "Via Azure" else "" end) like "%%%1$s%%" or (case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" when a.device_occure_type = 0 then "Purchase Device" else "" end) like "%%%1$s%%" or (case when a.high_pririty = 1 then "High Priority" else "" end) like "%%%1$s%%" or (case when a.stock_place is not null and a.assigned_for is null then "Stock Place" else "" end) like "%%%1$s%%" or (case when a.assigned_for = 1 then "Checkeout to User" when a.assigned_for = 2 then "Checkout to Place" else "" end ) like "%%%1$s%%" or cat.name like "%%%1$s%%" or (case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end) like "%%%1$s%%" or a.name like "%%%1$s%%" or mdl.name like "%%%1$s%%" or mnu.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or pla.place like "%%%1$s%%" or cmp.name like "%%%1$s%%" or ploc.name like "%%%1$s%%" or (case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end) like "%%%1$s%%" or (a.assigned_for = 1 and u.first_name like "%%%1$s%%") or (a.assigned_for = 2 and p.place like "%%%1$s%%") or (a.assigned_for = 1 and u.last_name like "%%%1$s%%") or concat(u.first_name, " ", u.last_name) like "%%%1$s%%" or a.serial like "%%%1$s%%" or concat(own.first_name, " ", own.last_name) like "%%%1$s%%" or DATE_FORMAT(a.last_checkout, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(a.calc_warranty_expire_date, "%%d %%b %%Y") like "%%%1$s%%")', $search_key);
                $db->whereRaw($whereStr);
            }
        }

        if (isset($filters['device_size']) && $filters['device_size'] != 'null') {
            $data = $db->get()->toArray();
            if (!empty($data) && isset($filters['device_size']) && $filters['device_size'] != 'null') {
                $data = array_filter($data, function ($item) use ($filters) {
                    if (isset($filters['condition_by_user_assign']) && $filters['condition_by_user_assign'] != 'null') {
                        if ($filters['condition_by_user_assign'] == 1) {
                            return $item->assigned_count > $filters['device_size'] && $item->assigned_for == 1;
                        } elseif ($filters['condition_by_user_assign'] == 2) {
                            return $item->assigned_count < $filters['device_size'] && $item->assigned_for == 1;
                        } else {
                            return $item->assigned_count == $filters['device_size'] && $item->assigned_for == 1;
                        }
                    }
                    return $item->assigned_count == $filters['device_size'] && $item->assigned_for == 1;
                });
                $data = array_values($data);
            }
            $results = $data;
        } else {
            $results = $db->get();
        }
        if ($type == 'bulk-update') {
            $title_row = ['Company', 'Name', 'Asset Tag', 'Serial', 'Product Number', 'Manufacture', 'Model', 'Category', 'Status', 'Department', 'Location', 'Internal Place', 'Purchase Date', 'Purchase Currency', 'Purchase Cost', 'Purchase Reference', 'Uuid', 'Warranty Start Date', 'Warranty End Date', 'Warranty Months', 'Amc Expire Date', 'Amc  Supplier', 'Notes', 'Order Number', 'Asset Owner', 'Supplier', 'IP', 'MAC', 'Device Type', 'Device From', 'Stock Place', 'Requestable', 'High Priority', 'SEZ Device'];
            $file_name = 'DownloadDeviceForUpdate.xlsx';
        } else {
            $title_row = ['Company', 'Device Name', 'Device Tag', 'Serial', 'Product Number', 'Manufacture', 'Model', 'Category', 'Status', 'Department', 'Location', 'Internal Place', 'Purchase Date', 'Purchase Cost', 'Purchase Reference', 'Uuid', 'Warranty Start Date', 'Warranty End Date', 'Warranty Months', 'Amc Expire Date', 'Amc  Supplier', 'Notes', 'Order Number', 'Asset Owner', 'Supplier', 'IP', 'MAC', 'Device Type', 'Device From', 'Stock Place', 'Requestable', 'High Priority', 'SEZ Device'];
            $file_name = 'DownloadDeviceForImport.xlsx';
        }

        $data = [];
        foreach ($custom_fields as $cf) {
            $title_row[] = $custom_field_names[$cf];
        }
        if ($type == 'bulk-update') {
            foreach ($results as $key => $value) {
                $data[] = [
                    $value->cmp_name,
                    $value->name,
                    $value->asset_tag,
                    $value->serial,
                    $value->product_number,
                    $value->manu_name,
                    $value->mdl_full_name,
                    $value->cat_name,
                    $value->lbl_name,
                    $value->department,
                    $value->loc_name,
                    $value->internal_place,
                    $value->purchase_date_on != null ? Carbon::parse($value->purchase_date_on)->format('m/d/Y') : null,
                    $value->purchase_currency,
                    $value->purchase_cost_format,
                    $value->pur_invoice,
                    $value->uuid,
                    $value->warranty_start_date != null ? Carbon::parse($value->warranty_start_date)->format('m/d/Y') : null,
                    $value->warranty_end_date != null ? Carbon::parse($value->warranty_end_date)->format('m/d/Y') : null,
                    $value->warranty_months,
                    $value->amc_ed_format != null ? Carbon::parse($value->amc_ed_format)->format('m/d/Y') : null,
                    $value->amc_supp_name,
                    $value->notes,
                    $value->order_number,
                    $value->asset_owner,
                    $value->supplier_name,
                    $value->ip,
                    $value->mac,
                    $value->asset_type_name,
                    $value->device_occure_type_name,
                    $value->stock_place,
                    $value->requestable == 'Yes' ? 'Yes' : 'No',
                    $value->high_pririty == 'Yes' ? 'Yes' : 'No',
                    $value->sez_device == 'Yes' ? 'Yes' : 'No',
                ];
            }
        } else {
            foreach ($results as $key => $value) {
                $data[] = [
                    $value->cmp_name,
                    $value->name,
                    $value->asset_tag,
                    $value->serial,
                    $value->product_number,
                    $value->manu_name,
                    $value->mdl_full_name,
                    $value->cat_name,
                    $value->lbl_name,
                    $value->department,
                    $value->loc_name,
                    $value->internal_place,
                    $value->purchase_date_on != null ? Carbon::parse($value->purchase_date_on)->format('m/d/Y') : null,
                    $value->purchase_cost_format,
                    $value->pur_invoice,
                    $value->uuid,
                    $value->warranty_start_date != null ? Carbon::parse($value->warranty_start_date)->format('m/d/Y') : null,
                    $value->warranty_end_date != null ? Carbon::parse($value->warranty_end_date)->format('m/d/Y') : null,
                    $value->warranty_months,
                    $value->amc_ed_format != null ? Carbon::parse($value->amc_ed_format)->format('m/d/Y') : null,
                    $value->amc_supp_name,
                    $value->notes,
                    $value->order_number,
                    $value->asset_owner,
                    $value->supplier_name,
                    $value->ip,
                    $value->mac,
                    $value->asset_type_name,
                    $value->device_occure_type_name,
                    $value->stock_place,
                    $value->requestable == 'Yes' ? '1' : '0',
                    $value->high_pririty == 'Yes' ? '1' : '0',
                    $value->sez_device == 'Yes' ? '1' : '0',
                ];
            }
        }
        return Excel::download(new ExportDevicesForImport($data, $title_row), $file_name);
    }

    public function getUserDeviceForDropDown(Request $request)
    {
        $return = ['status' => 'fail', 'msg' => 'Data not fetch'];
        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);
            $data = $request->only('user_id', 'email');
            if (isset($request->problemManagementApiCall)) {
                $field = filter_var($request->input('user_id'), FILTER_VALIDATE_EMAIL) ? 'email' : 'user_id';

                if ($field == 'user_id') {
                    $rules = [
                        'user_id' => 'required|integer|exists:users,id'
                    ];
                    $user_id = $data['user_id'] = $request->input('user_id', Auth::user()->id);
                } else if ($field == 'email') {
                    $rules = [
                        'user_id' => 'required|string|email|max:255|exists:users,email',
                    ];
                    $email = $request->input('user_id');
                }
                $messages = [
                    'user_id.required' => 'Enter id or email address of user',
                    'email.required' => 'Enter id or email address of user'
                ];

                $validator = Validator::make($data, $rules, $messages);
                if ($validator->fails()) {
                    $v = $validator->errors()->toArray();
                    $e = array_shift($v);
                    $return['msg'] = $e[0];
                    return response()->json($return);
                }
            }

            $db = DB::table('assets as a');
            $db->leftJoin('models as mdl', 'a.model_id', '=', 'mdl.id');
            $db->leftJoin('status_labels as s', 'a.status_id', '=', 's.id');

            $db->select('a.id', 'a.asset_tag', 'a.name as asset_name', 'mdl.name', 'mdl.modelno', 'a.serial', DB::raw("CASE WHEN a.asset_tag IS NOT NULL AND a.asset_tag != '' AND a.name IS NOT NULL AND a.name != '' THEN CONCAT(a.asset_tag, '-', a.name) ELSE COALESCE(a.asset_tag, a.name) END as text"));
            $db->whereNull('a.deleted_at');
            $db->whereNull('s.sold');
            $db->whereNull('s.stolen_item');
            if (isset($request->problemManagementApiCall) && $request->problemManagementApiCall != 'true') {
                if (isset($user_id) && $user_id != '') {
                    $db->where('a.assigned_for', 1)->where('a.assigned_to', $user_id);
                } elseif (isset($email) && $email != '') {
                    $userObj = User::where('email', $email)->where('activated', 1)->first();
                    if (empty($userObj)) {
                        $return['msg'] = 'User email address not found or deactivated';
                        return response()->json($return);
                    }
                    $db->where('a.assigned_for', 1)->where('a.assigned_to', $userObj->id);
                }
            }

            if ($search) {
                $db->whereRaw("(concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) like '%" . $search . "%' or a.asset_tag like '%" . $search . "%'  or a.name like '%" . $search . "%' or mdl.name like '%" . $search . "%' or mdl.modelno like '%" . $search . "%'  or a.serial like '%" . $search . "%')");
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();
            $return['status'] = 'success';
            $return['msg'] = 'Data fetch successfully';
            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error('getUserDeviceForDropDown: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function rfidLogs(Request $request, $status = 0)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        // if(! Auth::user()->hasPermissionTo('DeviceRead')) {
        //     return redirect('dashboard')->with("msg", $return);
        // }

        $sort_fields = [
            ['id' => 1, 'text' => 'Tag'],
            ['id' => 2, 'text' => 'Device Tag'],
            ['id' => 3, 'text' => 'Scanned At']
        ];
        return view('devices.rfid_index')->with(compact('sort_fields'));
    }

    public function jxRfidLogs(Request $request)
    {
        $return = ['total' => 0, 'filtered' => 0, 'data' => []];
        $req = $request->all();

        $fields = array(
            '1' => 'tag',
            '2' => 'a.asset_tag',
            '3' => 'scanned_at'
        );

        $db = RfidScanLog::leftJoin('assets as a', 'a.id', '=', 'rfid_scan_log.device_id');

        $db->select('tag', 'a.asset_tag', 'rfid_scan_log.id');
        $db->addSelect(DB::raw('DATE_FORMAT(scanned_at, "%d %b %Y %h:%i %p") as scanned_at_format'));

        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];

        $is_searching = false;
        if (isset($req['filters'])) {
            $filters = $req['filters'];

            $db->where(function ($query) use ($filters) {
                if (isset($filters['manufacturer']) && $filters['manufacturer'] && $filters['manufacturer'] != 'null') {
                    $query->where('mdl.manufacturer_id', '=', (int) $filters['manufacturer']);
                }
                if (isset($filters['model']) && $filters['model'] && $filters['model'] != 'null') {
                    $query->whereIn('a.model_id', $filters['model']);
                }
                if (isset($filters['category']) && $filters['category'] && $filters['category'] != 'null') {
                    $query->whereIn('mdl.category_id', $filters['category']);
                }
                if (isset($filters['location']) && $filters['location'] && $filters['location'] != 'null') {
                    $query->whereIn('a.rtd_location_id', $filters['location']);
                }
                if (isset($filters['user_location']) && $filters['user_location'] && $filters['user_location'] != 'null') {
                    // $query->where("a.assigned_to", "=", 1);
                    $query->where('a.status_id', 6);
                    $query->whereIn('u.location_id', $filters['user_location']);
                }
                if (isset($filters['user_base_location']) && $filters['user_base_location'] && $filters['user_base_location'] != 'null') {
                    // $query->where("a.assigned_to", "=", 1);
                    $query->where('a.status_id', 6);
                    $query->whereIn('u.base_location_id', $filters['user_base_location']);
                }
                if (isset($filters['assigned_user']) && $filters['assigned_user'] && $filters['assigned_user'] != 'null') {
                    $query->where('a.assigned_to', '=', (int) $filters['assigned_user']);
                    $query->where('a.assigned_for', '=', 1);
                }
                if (isset($filters['assigned_place']) && $filters['assigned_place'] && $filters['assigned_place'] != 'null') {
                    $query->whereIn('a.assigned_to', $filters['assigned_place']);
                    $query->where('a.assigned_for', '=', 2);
                }
                if (isset($filters['stock_place']) && $filters['stock_place'] && $filters['stock_place'] != 'null') {
                    if ($filters['stock_place'] == 'empty_stock_place') {
                        $query->where('a.stock_place', '=', null);
                        $query->where('a.assigned_for', '=', null);
                    } else {
                        $query->whereIn('a.stock_place', $filters['stock_place']);
                        $query->where('a.assigned_for', '=', null);
                    }
                }
                if (isset($filters['asset_type_id']) && $filters['asset_type_id'] && $filters['asset_type_id'] != 'null') {
                    $query->whereIn('a.asset_type_id', $filters['asset_type_id']);
                }
                if (isset($filters['device_occure_type']) && $filters['device_occure_type'] && $filters['device_occure_type'] != 'null') {
                    $query->whereIn('a.device_occure_type', $filters['device_occure_type']);
                }
                if (isset($filters['last_checkout_project']) && $filters['last_checkout_project'] && $filters['last_checkout_project'] != 'null') {
                    $query->where('a.last_checkout_project', '=', (int) $filters['last_checkout_project']);
                }
                if (isset($filters['department']) && $filters['department'] && $filters['department'] != 'null') {
                    $query->where('u.department_id', '=', (int) $filters['department']);
                }
                if (isset($filters['asset_owner']) && $filters['asset_owner'] && $filters['asset_owner'] != 'null') {
                    $query->where('a.asset_owner', '=', (int) $filters['asset_owner']);
                }
                if (isset($filters['device_assigned_to']) && $filters['device_assigned_to'] && $filters['device_assigned_to'] != 'null') {
                    $query->where('a.assigned_for', '=', (int) $filters['device_assigned_to']);
                }
                if (isset($filters['added_from']) && $filters['added_from'] && $filters['added_from'] != 'null') {
                    $query->where('a.added_from', '=', (int) $filters['added_from']);
                }
                if (isset($filters['allocation_type']) && $filters['allocation_type'] && $filters['allocation_type'] != 'null') {
                    $query->where('ata.id', '=', (int) $filters['allocation_type']);
                }
                if (isset($filters['audit_confirmation']) && $filters['audit_confirmation'] && $filters['audit_confirmation'] != 'null') {
                    $query->where('a.accepted', '=', (string) $filters['audit_confirmation']);
                    $query->where('a.assigned_for', '=', 1);
                    $query->whereNotNull('a.assigned_to');
                }
                if (isset($filters['asset_tag']) && $filters['asset_tag'] && $filters['asset_tag'] != 'null') {
                    $query->whereIn('a.id', $filters['asset_tag']);
                }
                if (isset($filters['rfid']) && $filters['rfid'] && $filters['rfid'] != 'null') {
                    $query->whereIn('a.device_rfid', $filters['rfid']);
                }

                $now = Carbon::now(config('app.timezone'))->Format('Y-m-d');
                if (isset($filters['warranty_status']) && $filters['warranty_status'] && $filters['warranty_status'] != 'null') {
                    if ($filters['warranty_status'] == 1) {
                        $query->where('a.calc_warranty_expire_date', '>=', $now);
                    } elseif ($filters['warranty_status'] == 2) {
                        $query->whereRaw("(a.warranty_status = 2 or a.calc_warranty_expire_date < '" . $now . "')");
                    } elseif ($filters['warranty_status'] == 3) {
                        $query->where('a.warranty_status', '=', 3);
                    }
                }

                $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.last_checkout', '3' => '', '4' => 'a.amc_expire_date', '5' => 'a.warranty_start_date', '6' => 'a.warrenty_end_date'];
                if (isset($filters['based_on']) && $filters['based_on'] && $filters['based_on'] != 'null' && $filters['based_on'] >= 1 && $filters['based_on'] <= 6) {
                    if (isset($filters['from_date']) && $filters['from_date'] && $filters['from_date'] != 'null') {
                        $from_date = CommonHelper::getDateAs($filters['from_date'], 'Y-m-d', 'd/m/Y');
                        $to_date = CommonHelper::getDateAs($filters['to_date'], 'Y-m-d', 'd/m/Y');
                        if ($from_date && $to_date) {
                            $whereStr = sprintf('(date(%1$s) >= "%2$s" and date(%1$s) <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $query->whereRaw($whereStr);
                        }
                    }
                }
            });

            $is_searching = true;
        }

        if (isset($req['search']) && $search_key = trim($req['search'])) {
            $whereStr = sprintf('(a.asset_tag like "%%%1$s%%" or tag like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $is_searching = true;
        }

        if ($is_searching) {
            $return['filtered'] = $db->count();
        }

        if (isset($req['order']['id']) && isset($fields[$req['order']['id']]) && in_array($req['order']['dir'], [1, 2])) {
            $dir = $req['order']['dir'] == 1 ? 'asc' : 'desc';
            $db->orderBy($fields[$req['order']['id']], $dir);
        }

        $page = $request->input('page', 1);
        $take = $request->input('size', 10);
        $skip = ($page * $take) - $take;
        if ($return['filtered'] < $skip) {
            $page = 1;
            $skip = 0;
        }

        $db->skip($skip);
        $db->take($take);

        $return['data'] = $db->get();
        foreach ($return['data'] as $d) {
            $d->is_blocked = BlockedRfidTag::isBlocked($d->tag) == true ? true : false;
        }
        $return['page'] = $page;

        return response()->json($return);
    }

    public function exportScanLogs(Request $request)
    {
        $return = ['total' => 0, 'filtered' => 0, 'data' => []];
        $req = $request->all();
        $fields = array(
            '1' => 'tag',
            '2' => 'a.asset_tag',
            '3' => 'scanned_at'
        );

        $db = RfidScanLog::leftJoin('assets as a', 'a.id', '=', 'rfid_scan_log.device_id');

        $db->select('rfid_scan_log.id', 'tag', 'a.asset_tag');
        $db->addSelect(DB::raw('DATE_FORMAT(scanned_at, "%d %b %Y %h:%i %p") as scanned_at_format'));

        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];

        $is_searching = false;
        if (isset($req['filters'])) {
            $filters = $req['filters'];

            $db->where(function ($query) use ($filters) {
                if (isset($filters['manufacturer']) && $filters['manufacturer'] && $filters['manufacturer'] != 'null') {
                    $query->where('mdl.manufacturer_id', '=', (int) $filters['manufacturer']);
                }
                if (isset($filters['model']) && $filters['model'] && $filters['model'] != 'null') {
                    $query->whereIn('a.model_id', $filters['model']);
                }
                if (isset($filters['category']) && $filters['category'] && $filters['category'] != 'null') {
                    $query->whereIn('mdl.category_id', $filters['category']);
                }
                if (isset($filters['location']) && $filters['location'] && $filters['location'] != 'null') {
                    $query->whereIn('a.rtd_location_id', $filters['location']);
                }
                if (isset($filters['user_location']) && $filters['user_location'] && $filters['user_location'] != 'null') {
                    // $query->where("a.assigned_to", "=", 1);
                    $query->where('a.status_id', 6);
                    $query->whereIn('u.location_id', $filters['user_location']);
                }
                if (isset($filters['user_base_location']) && $filters['user_base_location'] && $filters['user_base_location'] != 'null') {
                    // $query->where("a.assigned_to", "=", 1);
                    $query->where('a.status_id', 6);
                    $query->whereIn('u.base_location_id', $filters['user_base_location']);
                }
                if (isset($filters['assigned_user']) && $filters['assigned_user'] && $filters['assigned_user'] != 'null') {
                    $query->where('a.assigned_to', '=', (int) $filters['assigned_user']);
                    $query->where('a.assigned_for', '=', 1);
                }
                if (isset($filters['assigned_place']) && $filters['assigned_place'] && $filters['assigned_place'] != 'null') {
                    $query->whereIn('a.assigned_to', $filters['assigned_place']);
                    $query->where('a.assigned_for', '=', 2);
                }
                if (isset($filters['stock_place']) && $filters['stock_place'] && $filters['stock_place'] != 'null') {
                    if ($filters['stock_place'] == 'empty_stock_place') {
                        $query->where('a.stock_place', '=', null);
                        $query->where('a.assigned_for', '=', null);
                    } else {
                        $query->whereIn('a.stock_place', $filters['stock_place']);
                        $query->where('a.assigned_for', '=', null);
                    }
                }
                if (isset($filters['asset_type_id']) && $filters['asset_type_id'] && $filters['asset_type_id'] != 'null') {
                    $query->whereIn('a.asset_type_id', $filters['asset_type_id']);
                }
                if (isset($filters['device_occure_type']) && $filters['device_occure_type'] && $filters['device_occure_type'] != 'null') {
                    $query->whereIn('a.device_occure_type', $filters['device_occure_type']);
                }
                if (isset($filters['last_checkout_project']) && $filters['last_checkout_project'] && $filters['last_checkout_project'] != 'null') {
                    $query->where('a.last_checkout_project', '=', (int) $filters['last_checkout_project']);
                }
                if (isset($filters['department']) && $filters['department'] && $filters['department'] != 'null') {
                    $query->where('u.department_id', '=', (int) $filters['department']);
                }
                if (isset($filters['asset_owner']) && $filters['asset_owner'] && $filters['asset_owner'] != 'null') {
                    $query->where('a.asset_owner', '=', (int) $filters['asset_owner']);
                }
                if (isset($filters['device_assigned_to']) && $filters['device_assigned_to'] && $filters['device_assigned_to'] != 'null') {
                    $query->where('a.assigned_for', '=', (int) $filters['device_assigned_to']);
                }
                if (isset($filters['added_from']) && $filters['added_from'] && $filters['added_from'] != 'null') {
                    $query->where('a.added_from', '=', (int) $filters['added_from']);
                }
                if (isset($filters['allocation_type']) && $filters['allocation_type'] && $filters['allocation_type'] != 'null') {
                    $query->where('ata.id', '=', (int) $filters['allocation_type']);
                }
                if (isset($filters['audit_confirmation']) && $filters['audit_confirmation'] && $filters['audit_confirmation'] != 'null') {
                    $query->where('a.accepted', '=', (string) $filters['audit_confirmation']);
                    $query->where('a.assigned_for', '=', 1);
                    $query->whereNotNull('a.assigned_to');
                }
                if (isset($filters['asset_tag']) && $filters['asset_tag'] && $filters['asset_tag'] != 'null') {
                    $query->whereIn('a.id', $filters['asset_tag']);
                }
                if (isset($filters['rfid']) && $filters['rfid'] && $filters['rfid'] != 'null') {
                    $query->whereIn('a.device_rfid', $filters['rfid']);
                }

                $now = Carbon::now(config('app.timezone'))->Format('Y-m-d');
                if (isset($filters['warranty_status']) && $filters['warranty_status'] && $filters['warranty_status'] != 'null') {
                    if ($filters['warranty_status'] == 1) {
                        $query->where('a.calc_warranty_expire_date', '>=', $now);
                    } elseif ($filters['warranty_status'] == 2) {
                        $query->whereRaw("(a.warranty_status = 2 or a.calc_warranty_expire_date < '" . $now . "')");
                    } elseif ($filters['warranty_status'] == 3) {
                        $query->where('a.warranty_status', '=', 3);
                    }
                }

                $based_on_possible = ['1' => 'a.purchase_date', '2' => 'a.last_checkout', '3' => '', '4' => 'a.amc_expire_date', '5' => 'a.warranty_start_date', '6' => 'a.warrenty_end_date'];
                if (isset($filters['based_on']) && $filters['based_on'] && $filters['based_on'] != 'null' && $filters['based_on'] >= 1 && $filters['based_on'] <= 6) {
                    if (isset($filters['from_date']) && $filters['from_date'] && $filters['from_date'] != 'null') {
                        $from_date = CommonHelper::getDateAs($filters['from_date'], 'Y-m-d', 'd/m/Y');
                        $to_date = CommonHelper::getDateAs($filters['to_date'], 'Y-m-d', 'd/m/Y');
                        if ($from_date && $to_date) {
                            $whereStr = sprintf('(date(%1$s) >= "%2$s" and date(%1$s) <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $query->whereRaw($whereStr);
                        }
                    }
                }
            });

            $is_searching = true;
        }

        if (isset($req['search']) && $search_key = trim($req['search'])) {
            $whereStr = sprintf('(a.asset_tag like "%%%1$s%%" or tag like "%%%1$s%%" )', $search_key);
            $db->whereRaw($whereStr);
            $is_searching = true;
        }

        if ($is_searching) {
            $return['filtered'] = $db->count();
        }

        if (isset($req['order']['id']) && isset($fields[$req['order']['id']]) && in_array($req['order']['dir'], [1, 2])) {
            $dir = $req['order']['dir'] == 1 ? 'asc' : 'desc';
            $db->orderBy($fields[$req['order']['id']], $dir);
        }

        $return['data'] = $db->get();

        foreach ($return['data'] as $r) {
            $r->is_blocked = BlockedRfidTag::isBlocked($r->tag) == true ? 'Yes' : 'No';
            $data[] = [
                $r->id,
                $r->tag,
                $r->asset_tag,
                $r->scanned_at_format,
                $r->is_blocked,
            ];
        }

        $result = json_decode(json_encode($data, true), true);
        return Excel::download(new RfidLogExport($result), 'RFID ScanLog.xlsx');
    }

    public function assignDevice(Request $request)
    {
        try {
            $return = ['status' => 'fail', 'msg' => 'Unable to assign device'];
            $rules = [
                'device_id' => 'required|exists:assets,id',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return['msg'] = $e[0];
                return response()->json($return);
            }
            // DB::beginTransaction();
            $log = RfidScanLog::find($request->logid);

            // check existing tag assigned
            $check = DeviceRfid::where([
                'device_id' => $request->device_id,
                'device_rfid' => $log->tag,
            ])->count();
            if ($check != 0) {
                $return['msg'] = 'Tag already assigned to device.';
                return response()->json($return);
            }

            $data = [
                'device_rfid' => $log->tag,
                'device_id' => $request->device_id,
            ];
            $deviceTagUpdate = new DeviceRfid();
            $deviceTagUpdate->fill($data);
            if (!$deviceTagUpdate->save()) {
                return response()->json($return);
            }
            $updateLogs = RfidScanLog::where('tag', $log->tag);
            $updateLogs->device_id = $request->device_id;
            if (!$updateLogs->save()) {
                return response()->json($return);
            }
            // DB::commit();
            $return = ['status' => 'success', 'msg' => 'Device assigned successfully'];
            return response()->json($return);
        } catch (\Exception $e) {
            // DB::rollback();
            Log::error('assignDevice : ' . $e->getMessage());
            return response()->json($return);
        }
    }

    public function getModelCatByDevice(Request $request, $id)
    {
        $return = array();
        try {
            $db = DB::table('assets as a');
            $db->leftJoin('models as mdl', 'a.model_id', '=', 'mdl.id');
            $db->leftJoin('categories as c', 'mdl.category_id', '=', 'c.id');
            $db->select('a.id', 'a.asset_tag', 'a.name as asset_name', 'a.serial', 'mdl.id as model_id', 'mdl.name as model_name', 'mdl.category_id', 'c.name as category_name', DB::raw("concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) as text"));
            $db->where('a.id', $id);
            $db->whereNull('mdl.deleted_at');
            $return = $db->first();
        } catch (\Exception $e) {
            Log::error('getModelCatByDevice: ' . $e->getMessage());
            return response()->json($return);
        }
        return response()->json($return);
    }

    public function blockTags(Request $request, $tag)
    {
        try {
            $return = ['status' => 'danger', 'msg' => 'Unable to update tag'];
            DB::beginTransaction();
            if (BlockedRfidTag::isBlocked($tag) == true) {
                $blockTag = BlockedRfidTag::where('tag', $tag)->delete();
            } else {
                $blockTag = new BlockedRfidTag();
                $blockTag->fill(['tag' => $tag]);
                if (!$blockTag->save()) {
                    $request->session()->flash('msg', $return);
                    return redirect('rfid-logs');
                }
            }
            DB::commit();
            $return = ['status' => 'success', 'msg' => 'Tag update successfully'];
            $request->session()->flash('msg', $return);
            return redirect('rfid-logs');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('blockTags : ' . $e->getMessage());
            $request->session()->flash('msg', $return);
            return redirect('rfid-logs');
        }
    }

    public function viewSezInfoTab(Request $request, $id)
    {
        $return = [];
        try {
            $device = DeviceSez::where('device_id', $id)->first();
            if (Settings::first()->sez_custom_fieldset_id != null) {
                $customFieldset = CustomFieldset::where('id', Settings::first()->sez_custom_fieldset_id)->first();
                $prefixed_code = [];

                foreach ($customFieldset->fields as $f) {
                    $prefixed = $f->nameToColumn();
                    array_push($prefixed_code, $prefixed);
                }
            }
            if ($device->exists) {
                $return['html'] = view('devices.sez_tab')->with(compact('device', 'prefixed_code'))->render();
            }
        } catch (\Exception $e) {
            Log::error('viewSezInfoTab: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function ajaxGetSez(Request $request, $id)
    {
        $return = array('status' => 'failure', 'msg' => 'Unable to open device for edit.');
        $device = DeviceSez::where('device_id', $id)->first();
        if (!$device || !$device->exists) {
            return response()->json($return);
        }

        $objSez = array();
        $objSez['data'] = $device->toArray();
        $objSez['dropdown'] = array();
        $objSez['custom_fields']['all_fields'] = array();
        $objSez['custom_fields']['required_fields'] = array();
        $objSez['custom_fields']['html'] = '';

        if (Settings::first()->sez_custom_fieldset_id != null) {
            $fieldsetObj = CustomFieldset::where('id', Settings::first()->sez_custom_fieldset_id)->first();
            if (!empty($fieldsetObj)) {
                $devices1 = CommonHelper::formCustomFieldsSez($fieldsetObj->fields, $objSez['data']);
                if (isset($devices1['all_fields'][0])) {
                    array_push($objSez['custom_fields']['all_fields'], $devices1['all_fields'][0]);
                }
                if (isset($devices1['required_fields'][0])) {
                    array_push($objSez['custom_fields']['required_fields'], $devices1['required_fields'][0]);
                }
                $objSez['custom_fields']['html'] .= $devices1['html'];
            }
        }
        $return['status'] = 'success';
        $return['msg'] = 'Device SEZ data fetched successfully';
        $return['data'] = $objSez;
        return $return;
    }

    public function ajaxEditSez(Request $request, $id)
    {
        $return = array('status' => 'failure', 'msg' => 'Unable to edit Device');

        $device = DeviceSez::where('device_id', $id)->first();
        if (!$device->exists) {
            return response()->json($return);
        }
        $rules = [];
        $messages = [];
        $data_fields = $request->input('fields', null);
        $custom_fields = [];
        if (Settings::first()->sez_custom_fieldset_id != '') {
            $customFieldset = CustomFieldset::where('id', Settings::first()->sez_custom_fieldset_id)->first();
            foreach ($customFieldset->fields as $f) {
                $col_name = $f->nameToColumn();
                $formatType = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[] = $col_name;
            }
        }
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }

        $device->fill($data);
        if (count($custom_fields)) {
            foreach ($custom_fields as $f) {
                $device->{$f} = isset($data[$f]) ? $data[$f] : null;
            }
        }
        if ($device->save()) {
            $return['status'] = 'success';
            $return['msg'] = trans('content.device_fields.device_changes');
        }
        return response()->json($return);
    }

    public function getCostByAjax(Request $request, $type_val, $id)
    {
        $return = [
            'status' => 'fail',
            'msg' => 'Unable to fetch cost'
        ];
        try {
            $db = DB::table('billing_unit as a');
            $db->where('a.asset_id', $id);
            if ($type_val == 'Rate Hr') {
                $db->addselect('a.rate_hr');
            } else if ($type_val == 'Rate Day') {
                $db->addselect('a.rate_day');
            } else if ($type_val == 'Rate Week') {
                $db->addselect('a.rate_week');
            } else if ($type_val == 'Rate Month') {
                $db->addselect('a.rate_month');
            } else if ($type_val == 'Rate Quarterly') {
                $db->addselect('a.rate_quarterly');
            } else if ($type_val == 'Rate Half Yearly') {
                $db->addselect('a.rate_half_yearly');
            } else if ($type_val == 'Rate Yearly') {
                $db->addselect('a.rate_yearly');
            } else {
                $type_val = null;
            }

            if ($type_val == null) {
                $cost = null;
            } else {
                $cost = $db->first();
            }
            if ($cost && $cost != null) {
                $return = $cost;
            }
        } catch (\Exception $e) {
            Log::error('getCostByAjax: ' . $e->getMessage());
            return response()->json($return);
        }
        return response()->json($return);
    }

    public function ajaxCostHistory(Request $request, $device_id)
    {
        $req = $request->all();
        $return = array(
            'draw' => date('is')
        );

        $fields = array(
            '0' => 'al.action_type',
            '1' => 'al.updated_at',
            '2' => 'al.checkin_at',
            '3' => 'al.rate',
            '4' => 'al.rate_cost',
            '5' => 'al.cost',
        );
        $db = DB::table('checkin_checkout_logs as al');
        $db->where('al.asset_id', $device_id);
        $db->select('al.id', 'al.action_type', 'al.rate', 'al.rate_cost', 'al.cost', 'al.checkin_at', 'a.purchase_currency');
        $db->addSelect(DB::raw('DATE_FORMAT(al.updated_at, "%d %b %Y %h:%i %p") as created_at_format'));
        $db->leftJoin('assets as a', 'a.id', '=', 'al.asset_id');
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(al.action_type like "%%%1$s%%" or al.rate_cost like "%%%1$s%%" or al.rate like "%%%1$s%%" or DATE_FORMAT(al.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['order'][0]['column']) && isset($fields[$req['order'][0]['column']]) && in_array($req['order'][0]['dir'], ['asc', 'desc'])) {
            $db->orderBy($fields[$req['order'][0]['column']], $req['order'][0]['dir']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
        }
        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return['data'] = array();
        foreach ($data as $d) {
            if ($d->purchase_currency || $d->purchase_currency != null) {
                $currency = Currency::getCurrencyByCode($d->purchase_currency, true);
                $d->rate_cost = $currency . ' ' . $d->rate_cost;
                $d->cost = isset($d->cost) ? $currency . ' ' . $d->cost : 0;
            }
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    public function ajaxGatepass(Request $request, $device_id)
    {
        $req = $request->all();

        $fields = array(
            '0' => 'gatepass',
            '1' => 'fromlocation',
            '2' => 'status',
            '3' => 'username',
            '4' => 'serial',
        );
        $db = DB::table('gate_passes as ga');
        $db->leftJoin('gate_pass_items as it', 'ga.id', '=', 'it.gate_pass_id');
        $db->leftJoin('assets as a', 'a.id', '=', 'it.item_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'ga.location_id');
        $db->leftJoin('locations as toloc', 'toloc.id', '=', 'ga.moved_to_location');
        $db->leftJoin('users as u', 'u.id', '=', 'ga.moved_with_id');
        $db->where('it.item_type', 1);
        $db->where('it.item_id', $device_id);
        $db->select('a.id', 'a.name', 'loc.name as fromlocation', 'toloc.name as tolocation', 'u.username', DB::raw('CONCAT("#GP", ga.id) as gatepass'), 'a.serial', DB::raw("CASE WHEN ga.status = 1 THEN 'In Transit' WHEN ga.status = 2 THEN 'Completed' WHEN ga.status = 3 THEN 'Pending' WHEN ga.status = 4 THEN 'Cancelled'ELSE ''END as status"));
        $db->addSelect(DB::raw('DATE_FORMAT(ga.created_at, "%d %b %Y %h:%i %p") as created_at_format'));
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(a.serial like "%%%1$s%%" or toloc.name like "%%%1$s%%" or ga.id like "%%%1$s%%" or DATE_FORMAT(ga.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['order'][0]['column']) && isset($fields[$req['order'][0]['column']]) && in_array($req['order'][0]['dir'], ['asc', 'desc'])) {
            $db->orderBy($fields[$req['order'][0]['column']], $req['order'][0]['dir']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
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

    public function getAccountData(Request $request, $device_id)
    {
        $return = [];

        // get total expense of device
        $total_cost = AssetExpense::select(
            DB::raw('IFNULL(SUM(case when asset_expenses.expense_type = 1 then asset_expenses.cost else 0 end) + 
        SUM(case when asset_expenses.expense_type = 2 then asset_expenses.cost else 0 end) + SUM(case when asset_expenses.expense_type = 3 then asset_expenses.cost else 0 end) +
        SUM(case when asset_expenses.expense_type = 4 then asset_expenses.cost else 0 end) + SUM(case when asset_expenses.expense_type = 5 then asset_expenses.cost else 0 end), 0) AS total_cost'),
            DB::raw('IFNULL(SUM(case when asset_expenses.expense_type = 1 then asset_expenses.cost end), 0) as maintenance_cost'),
            DB::raw('IFNULL(SUM(case when asset_expenses.expense_type = 2 then asset_expenses.cost end), 0) as repair_cost'),
            DB::raw('IFNULL(SUM(case when asset_expenses.expense_type = 3 then asset_expenses.cost end), 0) as upgrade_cost'),
            DB::raw('IFNULL(SUM(case when asset_expenses.expense_type = 4 then asset_expenses.cost end), 0) as misc_cost'),
            DB::raw('IFNULL(SUM(case when asset_expenses.expense_type = 5 then asset_expenses.cost end), 0) as audit_cost')
        )
            ->where('asset_id', $device_id)
            ->whereNull('asset_expenses.temp_id')
            ->first();

        $return['total_cost'] = $total_cost;

        // get schedule maintenance cost of device
        $schedule_cost = ScheduleMaintenanceCron::select(DB::raw('case when SUM(IFNULL(t.cost, 0)) is null then 0 else SUM(IFNULL(t.cost, 0)) end as schedule_cost'))
            ->leftJoin('schedule_maintenance_tasks as t', 'schedule_maintenance.id', 't.plan_id')
            ->where('schedule_maintenance.status', 3)
            ->where('t.status', 1)
            ->where('schedule_maintenance.device_id', $device_id)
            ->first();
        $return['total_schedule_cost'] = $schedule_cost;

        // get total income of device
        $total_earning = DeviceBillingCheckinCheckout::select(DB::raw('SUM(IFNULL(cost, 0)) as cost_earned'))->where('asset_id', $device_id)->first();
        $return['total_earning'] = $total_earning;

        // to get the purchase cost
        $total_purchase_cost = Device::select(DB::raw('SUM(IFNULL(purchase_cost, 0)) as purchase_cost'))->where('id', $device_id)->first();
        $return['total_purchase_cost'] = $total_purchase_cost;

        // Total Asset Type Cost
        $sumComponentPurchaseCost = DB::table('components as c')
            ->where('c.checked_out_to', '=', $device_id)
            ->sum('c.purchase_cost');

        $accData = DB::table('accessories as acc')
            ->leftJoin('accessories_users as au', 'au.accessory_id', '=', 'acc.id')
            ->where('au.assigned_to', '=', $device_id)
            ->where('au.assigned_for', '=', 3)
            ->selectRaw('
                acc.name,
                acc.purchase_cost,
                acc.qty as total_qty,
                COUNT(au.id) as assigned_qty,
                (acc.purchase_cost / acc.qty) as unit_price,
                (COUNT(au.id) * (acc.purchase_cost / acc.qty)) as total_assigned_cost
            ')
            ->groupBy('acc.id', 'acc.name', 'acc.purchase_cost', 'acc.qty')
            ->get();
        $sumAccPurchaseCost = $accData->sum('total_assigned_cost');

        $conData = DB::table('consumables as con')
            ->leftJoin('consumables_users as cu', 'cu.consumable_id', '=', 'con.id')
            ->where('cu.assigned_to', '=', $device_id)
            ->where('cu.assigned_for', '=', 3)
            ->selectRaw('
                con.name,
                con.purchase_cost,
                con.qty as total_qty,
                COUNT(cu.id) as assigned_qty,
                (con.purchase_cost / CAST(con.qty AS DECIMAL(13,4))) as unit_price,
                (COUNT(cu.id) * (con.purchase_cost / CAST(con.qty AS DECIMAL(13,4)))) as total_assigned_cost
            ')
            ->groupBy('con.id', 'con.name', 'con.purchase_cost', 'con.qty')
            ->get();
        $sumConsumablePurchaseCost = $conData->sum('total_assigned_cost');

        $licData = DB::table('license_seats as ls')
            ->join('licenses as l', function ($q) {
                $q->on('l.id', '=', 'ls.license_id')->whereNull('l.deleted_at');
            })
            ->where('ls.asset_id', '=', $device_id)
            ->whereNull('ls.deleted_at')
            ->selectRaw('
                l.id,
                l.name,
                l.seats,
                l.purchase_cost,
                COUNT(ls.id) as assigned_seats,
                (l.purchase_cost / l.seats) as unit_price,
                (COUNT(ls.id) * (l.purchase_cost / l.seats)) as total_assigned_cost
            ')
            ->groupBy('l.id', 'l.name', 'l.seats', 'l.purchase_cost')
            ->get();
        $totalLicenseCost = $licData->sum('total_assigned_cost');

        $total_asset_cost = $sumComponentPurchaseCost + $sumAccPurchaseCost + $sumConsumablePurchaseCost + $totalLicenseCost + $total_cost->total_cost + $schedule_cost->schedule_cost + $total_purchase_cost->purchase_cost;
        $return['total_asset_value'] = $total_asset_cost;

        $return['total_expense'] = $total_cost->total_cost + $schedule_cost->schedule_cost;

        return response()->json($return);
    }

    public function getDeviceRateDropDown(Request $request, $device_id)
    {
        $return = [
            'status' => 'fail',
            'msg' => 'Unable to fetch unit'
        ];
        try {
            $asset = Device::find($device_id);
            if (!$asset) {
                $return = [
                    'status' => 'fail',
                    'msg' => 'Device not found'
                ];
                return response()->json($return);
            }
            $unit = BillingUnit::select('rate_hr as Rate Hr', 'rate_day as Rate Day', 'rate_week as Rate Week', 'rate_month as Rate Month', 'rate_quarterly as Rate Quarterly', 'rate_half_yearly as Rate Half Yearly', 'rate_yearly as Rate Yearly')->where('asset_id', $device_id)->first();
            if ($unit && $unit != null) {
                $return = $unit;
            }
        } catch (\Exception $e) {
            Log::error('getDropDown: ' . $e->getMessage());
            return response()->json($return);
        }
        return response()->json($return);
    }

    public function ajaxAddTransfer(Request $request)
    {
        try {
            $return = ['status' => 'failure', 'msg' => 'Unable to add the transfer'];
            if (!Auth::user()->hasPermissionTo('TransferAdd') || !config('services.assets.enabled')) {
                $return['msg'] = trans('content.user_fields.you_have_not_permission');
                return response()->json($return);
            }

            $data = $request->all();
            $validate = Validator::make($data, [
                'transfer_to' => 'required|integer|exists:locations,id',
                'internal_place' => 'nullable|integer|exists:places,id',
                'expected_received_date' => 'required|date_format:d/m/Y',
                'responsible_user' => 'required|integer|exists:users,id',
            ]);

            if ($validate->fails()) {
                $v = $validate->errors()->toArray();
                $e = array_shift($v);
                $return['msg'] = $e[0];
                return response()->json($return);
            }

            $ids = $data['ids'];

            if (empty($ids)) {
                $return['msg'] = trans('content.device_fields.Please_select_atleast_one_device');
                return response()->json($return);
            }

            $device_ids = explode(',', $ids);
            $randomString = Str::random(8) . Carbon::now()->timestamp;
            $prefixed_code = [];
            $responsibleUserObj = User::where('id', $request->responsible_user)->first();

            if (!empty($responsibleUserObj) && !$responsibleUserObj->email && !$responsibleUserObj->phone) {
                $return['msg'] = trans('content.device_fields.responsible_user_email_or_mobile_not_found');
                return response()->json($return);
            }

            foreach ($device_ids as $dev_id) {
                $dev = Device::findOrFail($dev_id);
                $isDeployable = Label::where('id', $dev->status_id)
                    ->where('deployable', 1)
                    ->first();

                if (!in_array($dev->status_id, [2, 3]) && empty($isDeployable)) {
                    $statusData = Label::where('id', $dev->status_id)->first();
                    $return['msg'] = trans('content.device_fields.you_cannot_transfer_this_status_device') . ' ' . ($statusData->name ?? '') . '';
                    return response()->json($return);
                }
                array_push($prefixed_code, $dev->rtd_location_id);
                if ($dev->assigned_to) {
                    $return['msg'] = trans('content.device_fields.device_assigned');
                    return response()->json($return);
                }
                if ($dev->rtd_location_id == $request->transfer_to) {
                    $return['msg'] = trans('content.device_fields.form_and_to_should_be_diffenent');
                    return response()->json($return);
                }
                $transfer_log = TransferItem::where('device_id', $dev_id)->where('transfer_status', 1)->first();
                if ($transfer_log) {
                    $return['msg'] = trans('content.device_fields.already_transfer');
                    return response()->json($return);
                }
                if (count(array_unique($prefixed_code)) > 1) {
                    $return['msg'] = trans('content.device_fields.device_belongs_to_same_location');
                    return response()->json($return);
                }
            }

            $transfer = new Transfer;
            $transfer->transfer_from = $dev->rtd_location_id;
            $transfer->transfer_to = $request->transfer_to;
            $transfer->internal_place = $request->internal_place;
            $transfer->responsible_user = $request->responsible_user;
            $transfer->status = 1;
            $transfer->notes = $data['notes'];
            $transfer->batch_code = $randomString;
            $transfer->expected_received_date = $request->expected_received_date ? CommonHelper::getDateAs($request->expected_received_date, 'Y-m-d', 'd/m/Y') : null;
            $transfer->created_by = Auth::user()->id;
            if (isset($request->cc_users) && $request->cc_users != 'null') {
                $transfer->cc_users = $request->cc_users;
            }
            if ($transfer->save()) {
                $id = $transfer->id;  // Get Auto increment inserted ID
                // Now generate batch_code based on client + ID
                if (config('app.client') == 'rashmi') {
                    // Format: RG-TR-001 (padded to 3 digits — adjust if needed)
                    $batchCode = 'RG-TR-' . str_pad($id, 3, '0', STR_PAD_LEFT);
                    $transfer->batch_code = $batchCode;
                    $transfer->save();
                }
                $return['status'] = 'success';
                $return['msg'] = 'Device Transfer Initiated successfully.';
            }
            // To store history
            $newRecord = $transfer->replicate();
            $newRecord->setTable('transfer_history');
            $newRecord->transfer_id = $transfer->id;
            $newRecord->save();

            foreach ($device_ids as $dev_id) {
                $id = $dev_id;
                $dev = Device::findOrFail($id);
                $transfer_item = new TransferItem;
                $transfer_item->transfer_id = $transfer->id;
                $transfer_item->device_id = $dev->id;
                $transfer_item->transfer_status = 1;
                if ($transfer_item->save()) {
                    $result['status'] = 'success';
                    $result['msg'] = 'Device transferred successfully';
                    $result['id'] = $dev->id;
                    $result['asset_tag'] = $dev->asset_tag;
                    $return['data'][] = $result;
                    $return['batch_code'] = $transfer->id != null ? $transfer->batch_code : null;
                    $return['transfer_id'] = $transfer->id;
                }
                Actionlog::transferDevice($dev, Auth::user()->id, $request->transfer_to, $request->responsible_user, 'Under Transfer / Batch Code: ' . $transfer->batch_code);
            }

            /** send mail notification */
            $ccUsers = $request->cc_users;
            $usersCCArray = explode(',', $ccUsers);
            array_push($usersCCArray, Auth::user()->id);
            if (!empty($responsibleUserObj)) {
                $ccUserEmail = [];
                $settings = Settings::getSettings();
                $globalAlertEmails = CommonHelper::getGlobalAlertEmail();
                if ($settings->alerts_enabled && $globalAlertEmails) {
                    if (is_array($globalAlertEmails)) {
                        $ccUserEmail = array_merge($ccUserEmail, $globalAlertEmails);
                    } else {
                        $ccUserEmail[] = $globalAlertEmails;
                    }
                }
                foreach ($usersCCArray as $u) {
                    $user = User::where('id', $u)->where('activated', 1)->first();
                    if (!empty($user) && $user->email != '' && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        array_push($ccUserEmail, $user->email);
                    }
                }
                $ccUseremail = array_unique($ccUserEmail);
                $responsiableEmail = $responsibleUserObj->email;
                $ccUseremail = array_filter($ccUseremail, function ($email) use ($responsiableEmail) {
                    return $email !== $responsiableEmail;
                });
                $ccUserEmail = array_values($ccUseremail);
                if (config('mail.service_enabled') && $responsibleUserObj && $responsibleUserObj->email && filter_var($responsibleUserObj->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        Log::info($responsibleUserObj->email . ' - CC: ' . json_encode($ccUserEmail));
                        Mail::to($responsibleUserObj->email)->cc($ccUserEmail)->send(new TransferUpdateNotification($transfer, $responsibleUserObj, 'new added'));
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }
            /** end send mail notification */

            /** send push notification */
            array_push($usersCCArray, $request->responsible_user);
            if (!empty($usersCCArray)) {
                foreach ($device_ids as $dev_id) {
                    $notificationText = 'Device Transfer Initiated For ' . $transfer->batch_code . '';
                    $transfer->device_id = $dev_id;
                    $transfer->module_type = 'device';
                    $data = [
                        'title' => $notificationText,
                        'data' => $transfer,
                        'notify' => array_unique($usersCCArray),
                    ];

                    $sendNotifications = CommonHelper::sendPushNotification($data);
                    if ($sendNotifications != false) {
                        $response = json_decode($sendNotifications);
                        if (isset($response->failure) && $response->failure == 1) {
                            Log::error('device transfer notification:' . $transfer->batch_code . ' error ' . json_encode($response));
                        }
                    }
                }
            }

            // send whatsapp notification
            if (!empty($usersCCArray)) {
                $data = [
                    'title' => 'Device Transfer Initiated',
                    'notify' => array_unique($usersCCArray),
                ];
                CommonHelper::sendWhatsappNotification($data);
            }

            return response()->json($return);
        } catch (\Exception $e) {
            Log::error('ajaxTransferAdd: ' . $e->getMessage());
            return response()->json($return);
        }
    }

    public function getWarrantyDate(Request $request, $id)
    {
        $return = [];
        try {
            $return['warranty'] = Commonhelper::getWarrantyDate($id);
            $return['warranty']['percent'] = 0;
            if (!empty($return['warranty']['start_date']) && !empty($return['warranty']['end_date'])) {
                $start_date = strtotime($return['warranty']['start_date']);
                $end_date = strtotime($return['warranty']['end_date']);
                $today_date = time();

                $total = $end_date - $start_date;
                if ($total > 0) {
                    $part = $today_date - $start_date;
                    $return['warranty']['percent'] = ($part / $total) * 100 > 100 ? 100 : (($part / $total) * 100 < 0 ? 0 : round(($part / $total) * 100));
                } elseif ($start_date == $end_date) {
                    $return['warranty']['percent'] = $today_date >= $end_date ? 100 : 0;
                } else {
                    $return['warranty']['percent'] = 0;
                }

                $return['warranty']['start_date'] = CommonHelper::displayDateTime($return['warranty']['start_date'], 'date', 'display');
                $return['warranty']['end_date'] = CommonHelper::displayDateTime($return['warranty']['end_date'], 'date', 'display');
            }
        } catch (\Exception $e) {
            Log::error('getWarrantyDate: ' . $e->getMessage());
            return $return;
        }
        return $return;
    }

    public function getCustomFieldValue(Request $request, $id)
    {
        $return = [];
        try {
            $customField = CustomField::find($id);
            $dev_name = '_itm_' . '' . str_replace(' ', '_', strtolower($customField->name));
            $data = Device::select($dev_name . ' as id', $dev_name . ' as text')->where($dev_name, '!=', null)->distinct($dev_name)->get()->toArray();

            if ($customField->preDefinedOptions != null && $customField->preDefinedOptions == 1) {
                foreach ($data as $value) {
                    $locations = Location::select('id', 'name as text')->where('id', $value['text'])->first();
                    if ($locations) {
                        $return[] = [
                            'id' => $locations->id,
                            'text' => $locations->text,
                        ];
                    }
                }
            } else if ($customField->preDefinedOptions != null && $customField->preDefinedOptions == 2) {
                foreach ($data as $value) {
                    $users = User::select('id', DB::raw('concat(first_name, " ", last_name, " (", username, ")") as text'))->where('id', $value['text'])->whereNull('deleted_at')->first();
                    if ($users) {
                        $return[] = [
                            'id' => $users->id,
                            'text' => $users->text,
                        ];
                    }
                }
            } else {
                $return = $data;
            }

            // $dev_name = '_itm_'.''.str_replace(' ', '_', strtolower($customField->name));
            // $return = Device::select($dev_name." as text")->where($dev_name, '!=', null)->distinct($dev_name)->get()->toArray();
        } catch (\Exception $e) {
            Log::error('getCustomFieldValue: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getConfigurationIndex(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('DeviceConfiguration') || !config('services.assets.enabled')) {
            return redirect('dashboard')->with('msg', $return);
        }
        $settings = Settings::first();
        $objDeviceSettings = DeviceSetting::first();
        $columns = ['Company', 'Device Name', 'Device Tag', 'Serial', 'Manufacture', 'Model Name', 'Category', 'Status', 'Location', 'Internal Place', 'Purchase Date', 'Purchase Currency', 'Purchase Cost', 'Purchase Invoice', 'Ship Date', 'Warranty Start', 'Warranty End', 'Warranty(Months)', 'Notes', 'Order Number', 'Asset Owner', 'IP', 'MAC', 'Device From', 'Stock Location', 'Stock Place', 'Last Network Location', 'Supplier', 'Project Name', 'Warranty Expire Date', 'Warranty Status', 'Account Type', 'UUID', 'Checkout Date', 'Checkout Status', 'Checkout Accepted Date', 'User/Place', 'Username/Placename/Status', 'Assigned User', 'Assigned Username', 'Assigned User Email', 'Assigned User Emp. Code', 'Assigned User Location', 'Assigned User Place', 'Assigned User DOJ', 'External User Company', 'Assigned Place', 'Expected Checkin Date', 'Contract End Date', 'AMC Supplier', 'AMC Expire Date', 'AMC Expire Status', 'OEM Warranty', 'Device Type', 'Allocation Type', 'Business Unit', 'Delivery Unit', 'User Department', 'Manager', 'Requestable', 'High Priority', 'Sez Device', 'Last NI Scan Date', 'Installed Date', 'Azure AD Device Id', 'Last Sync Date Time', 'Compliance State', 'Updated At', 'Department', 'HddSize', 'RAMSize', 'Processor', 'OSCaption', 'NI Exist', 'Azure Exist', 'RDP Enable', 'Device RFID', 'Custom Fields'];
        $device_export_column = isset($objDeviceSettings->device_export_column) && $objDeviceSettings->device_export_column != null ? explode(',', $objDeviceSettings->device_export_column) : [];
        $applicationArray = explode(',', $settings->compliance_application);
        return view('devices.configuration')->with([
            'settings' => $settings,
            'applicationArray' => $applicationArray,
            'device_column' => $columns,
            'export_column' => $device_export_column,
        ]);
    }

    public function saveConfigurationChanges(Request $request)
    {
        $return = ['status' => 'failure', 'msg' => 'Unable to update settings'];
        try {
            if (!Auth::user()->hasPermissionTo('DeviceConfiguration') || !config('services.assets.enabled')) {
                $return['msg'] = trans('content.user_fields.Permission_denied');
                return response()->json($return);
            }
            $data = $request->all();
            $rules = [
                'compliance_dashboard' => 'nullable|boolean',
                'compliance_application' => 'nullable',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return['msg'] = $e[0];
                return response()->json($return);
            }

            $objSettings = Settings::first();
            $objSettings->compliance_dashboard = $request->compliance_dashboard == '1' ? 1 : 0;
            $objSettings->bitLocker_enabled = $request->bitLocker == '1' ? 1 : 0;
            $objSettings->greenit_agent_enabled = $request->greenit_agent == '1' ? 1 : 0;
            // $objSettings->admin_rights_enabled = $request->admin_rights == '1' ? 1 : 0;
            // $objSettings->usb_access_enabled = $request->usb_access == '1' ? 1 : 0;
            $objSettings->compliance_application = (isset($request->compliance_application) && !empty($request->compliance_application)) ? implode(',', $request->compliance_application) : null;

            if ($objSettings->save()) {
                // if ((isset($data['compliance_application']) && !empty($data['compliance_application'])) || (in_array($objSettings->greenit_agent_enabled, [0,1])) ) {
                //     ComplianceApplicationFetchStatus::dispatch($data, $objSettings->greenit_agent_enabled);
                // }
                $return['msg'] = 'Configuration has been updated successfully!';
                $return['status'] = 'success';
            }
        } catch (\Exception $e) {
            Log::error('saveConfigurationChanges:' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function updateColumnExports(Request $request)
    {
        $return = ['status' => 'fail', 'msg' => 'Unable to update role.'];
        try {
            if (!Auth::user()->hasPermissionTo('DeviceConfiguration') || !config('services.assets.enabled')) {
                $return['msg'] = trans('content.user_fields.Permission_denied');
                return response()->json($return);
            }
            $req = $request->all();
            $objDeviceSettings = DeviceSetting::first();
            $device_export_column = null;
            if (isset($req['column_export'])) {
                $device_export_column = array_keys($req['column_export']);
                $device_export_column = implode(',', $device_export_column);
            }
            $objDeviceSettings->device_export_column = $device_export_column;
            $objDeviceSettings->save();
            $return = [
                'status' => 'success',
                'msg' => 'Column Export Updated',
            ];
            Log::info('updateColumnExports uid:' . Auth::user()->id);
        } catch (Exception $e) {
            Log::error('updateColumnExports() error : ' . $e->getMessage());
        }
        return response()->json($return);
    }

    /* get category by manufacture id based */
    public function getCategoryByManufacturer(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('term', '') ?: $request->input('search', '');
            $manufacturerId = $request->manufacturer_id;
            $db = DB::table('models')
                ->select('categories.id as id', 'categories.name as text')
                ->join('categories', 'models.category_id', '=', 'categories.id')
                ->whereNull('models.deleted_at')
                ->where('models.manufacturer_id', '=', $manufacturerId);
            if ($search) {
                $db->where('categories.name', 'like', '%' . $search . '%');
            }
            $count = $db->count();
            $result = $db->get();
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error('getCategoryByManufacturer: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getNIDeviceByModel(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('term', '') ? $request->input('term', '') : $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);
            $db = DB::table('assets as a');
            $db->rightJoin('itm_network_inventory_basic as b', 'a.id', '=', 'b.device_id');
            $db->leftJoin('status_labels as s', 'a.status_id', '=', 's.id');
            $db->select('a.id', 'a.asset_tag', 'a.name as asset_name', 'a.serial', DB::raw("concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) as text"));
            $db->whereNull('a.deleted_at');
            $db->whereNull('s.sold');
            $db->whereNull('s.stolen_item');
            $db->where('a.model_id', '=', $request->model_id);
            if ($search) {
                $whereStr = sprintf('(a.asset_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or concat_ws("-",a.asset_tag,concat_ws("" ,a.name)) like "%%%1$s%%")', $search);
                $db->whereRaw($whereStr);
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error('getNIDeviceByModel: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    /* get inventory network device */
    public function getNIDeviceByQuery(Request $request)
    {
        $return = array();
        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);

            $db = DB::table('assets as a');
            $db->rightJoin('itm_network_inventory_basic as b', 'a.id', '=', 'b.device_id');
            $db->leftJoin('models as mdl', 'a.model_id', '=', 'mdl.id');

            if (!Auth::user()->isSuperUser()) {
                $db->where('a.company_id', '=', Auth::user()->company_id);
            }

            $db->select('a.id', 'a.asset_tag', 'mdl.name', 'mdl.modelno', DB::raw("concat_ws('-', a.asset_tag, concat_ws(' ', mdl.name, mdl.modelno)) as text"));
            $db->whereNull('a.deleted_at');
            if ($search) {
                $db->whereRaw("(concat_ws('-', a.asset_tag, concat_ws(' ', mdl.name, mdl.modelno)) like '%" . $search . "%' or a.asset_tag like '%" . $search . "%' or mdl.name like '%" . $search . "%' or mdl.modelno like '%" . $search . "%')");
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::error('getNIDeviceByQuery: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getRdpPower(Request $request, $id)
    {
        $database = config('database.connections.sqlite.database');

        $device = Device::where('id', $id)->select('node_id')->first();
        $data = [];
        if ($device->node_id != null) {
            $node = 'node//' . $device->node_id;
            $power = Power::where('nodeid', $node)->whereBetween('time', [Carbon::now()->subDays(6), Carbon::now()])->orderBy('time', 'asc')->get();
            $date = $start_date = $end_date = $current_key = '';
            $last_index = 0;
            foreach ($power as $key => $p) {
                $doc = json_decode($p->doc);
                $d = Carbon::parse($p->time)->format('Y-m-d');
                if ($date != $d) {
                    $start_date = $end_date = '';
                    $date = $d;
                    $last_index = 1;
                }
                // set start time for active
                if ($doc->power == 1 && $start_date == '') {
                    $start_date = $p->time;
                }

                if ($doc->power == 1 && $end_date != '') {
                    $current = Carbon::parse($p->time);
                    $difference = $current->diffInMinutes($end_date);
                    // get end date for active time
                    if ($difference > 5) {
                        $x = $y = [];
                        // ignore last inactive time array
                        if ($last_index == 0) {
                            if (!empty($k = array_key_last($data))) {
                                $x = [
                                    'inactiveEndHour' => Carbon::parse($start_date)->format('H'),
                                    'inactiveEndMinute' => Carbon::parse($start_date)->format('i'),
                                ];
                                $y = $data[$k][array_key_last($data[$k])];
                                $data[$k][array_key_last($data[$k])] = array_merge_recursive($x, $y);
                            }
                        } else {
                            $last_index = 0;
                        }

                        $data[$date][] = [
                            'startHour' => Carbon::parse($start_date)->format('H'),
                            'startMinute' => Carbon::parse($start_date)->format('i'),
                            'endHour' => Carbon::parse($end_date)->format('H'),
                            'endMinute' => Carbon::parse($end_date)->format('i'),
                        ];

                        $current_key = $key;
                        $start_date = $p->time;
                        $end_date = '';
                    }
                }
                if ($doc->power == 0) {
                    $end_date = $p->time;
                }
            }
        }
        $st_date = Carbon::now()->subDays(6);
        $ed_date = Carbon::now();
        $j = 0;
        for ($i = $st_date; $i < $ed_date; $i = $i->addDay()) {
            if (!array_key_exists($i->format('Y-m-d'), $data)) {
                // insert the key at specific position and not at the end of an array
                $data = array_slice($data, 0, $j, true) + array($i->format('Y-m-d') => []) + array_slice($data, $j, count($data) - $j, true);
            }
            $j++;
        }

        return response()->json(array_reverse($data));
    }

    public function updateAssetTag(Request $request)
    {
        $return = ['status' => 'failure', 'msg' => 'Unable To Update The Tag'];
        $settings = DeviceSetting::first();

        $separators = [
            0 => '',
            1 => '-',
            2 => '_',
            3 => '/',
        ];

        $tagData = isset($settings['asset_tag_data']) ? json_decode($settings['asset_tag_data'], true) : null;

        $separator = (!empty($tagData) && isset($tagData['asset_tag_saperator']) && isset($separators[$tagData['asset_tag_saperator']])) ? $separators[$tagData['asset_tag_saperator']] : '';

        $devices = Device::all();

        foreach ($devices as $device) {
            $for_log_comparison = $device->dataForCache();
            $result = DB::table('assets as a')
                ->select('a.id', 'a.name as hostname', 'loc.name as location', 'dept.name as deptname', DB::raw("DATE_FORMAT(purchase_date, '%m%d%Y') as purchase_date"), 'invoice_id')
                ->leftJoin('locations as loc', 'loc.id', '=', 'a.rtd_location_id')
                ->leftJoin('departments as dept', 'dept.id', '=', 'a.department_id')
                ->where('a.id', $device->id)
                ->first();

            $assetTag = Settings::getSettings()->auto_increment_prefix . $result->id;

            // Asset Tag Type 2: Using Hostname
            if (!empty($tagData) && isset($tagData['asset_tag_type']) && $tagData['asset_tag_type'] == 2) {
                if (!empty($result->hostname)) {
                    if (Device::where('asset_tag', 'like', $result->hostname)->exists()) {
                        $assetTag = $assetTag . $separator . $result->hostname;
                    } else {
                        $assetTag = !empty($result->hostname) ? $result->hostname : $assetTag;
                    }
                }
            } elseif (!empty($tagData) && isset($tagData['asset_tag_type']) && $tagData['asset_tag_type'] == 3) {
                $tagParts = [];
                // Add Location
                if (!empty($result->location) && $tagData['asset_tag_location'] == 1) {
                    $tagParts[] = $separator . $result->location;
                }
                // Add Department
                if (!empty($result->deptname) && $tagData['asset_tag_department'] == 1) {
                    $tagParts[] = $separator . $result->deptname;
                }
                // Add Purchase Date or Invoice Date
                $invoiceDate = null;
                if ($result->invoice_id) {
                    $invoiceDate = Purchase::where('id', $result->invoice_id)
                        ->select(DB::raw("DATE_FORMAT(invoice_date, '%m/%d/%Y') as invoice_date"))
                        ->value('invoice_date');
                }
                $usedDate = false;
                if (!empty($tagData) && $tagData['asset_tag_pur_date'] == 1) {
                    if (!empty($result->purchase_date)) {
                        $tagParts[] = $separator . substr($result->purchase_date, 0, 2) . $separator . substr($result->purchase_date, 2, 2) . $separator . substr($result->purchase_date, 4);
                        $usedDate = true;
                    } elseif (!empty($invoiceDate)) {
                        $tagParts[] = $separator . substr($invoiceDate, 0, 2) . $separator . substr($invoiceDate, 2, 2) . $separator . substr($invoiceDate, 4);
                        $usedDate = true;
                    }
                }
                $currentDate = Carbon::now();
                $currentMonth = $currentDate->format('m');
                $currentYear = $currentDate->format('Y');
                // Add Current Month and Year if no date is used
                if (!$usedDate) {
                    if (!empty($currentMonth) && !empty($tagData) && $tagData['asset_tag_month'] == 1) {
                        $tagParts[] = $separator . $currentMonth;
                    }
                    if (!empty($currentYear) && !empty($tagData) && $tagData['asset_tag_year'] == 1) {
                        $tagParts[] = $separator . $currentYear;
                    }
                }
                $assetTag .= implode('', $tagParts);
            }
            $pre_tag = $device->asset_tag;
            $device->asset_tag = $assetTag;
            if ($device->save()) {
                if ($device->asset_tag != $pre_tag) {
                    Actionlog::deviceEdited($device, $for_log_comparison, Auth::user()->id);
                }
                $return['status'] = 'success';
                $return['msg'] = trans('content.device_fields.device_changes');
            }
        }
        return response()->json($return);
    }

    public function ajaxAssetSummary(Request $request)
    {
        $req = $request->all();
        if (!empty($req['filters'])) {
            $filters = $req['filters'];
            if (isset($filters['filter_by_category']) && $filters['filter_by_category'] != 'null') {
                $db = DB::table('category_wise_asset_summaries as as');
                $columns = Schema::getColumnListing('category_wise_asset_summaries');
                $excludeColumns = ['id', 'category_wise_id', 'created_at'];
            } else {
                $db = DB::table('asset_summary as as');
                $columns = Schema::getColumnListing('asset_summary');
                $excludeColumns = ['id', 'created_at'];
            }
        } else {
            $db = DB::table('asset_summary as as');
            $columns = Schema::getColumnListing('asset_summary');
            $excludeColumns = ['id', 'created_at'];
        }

        $columns = array_values(array_diff($columns, $excludeColumns));
        $fields = [];
        foreach ($columns as $col) {
            $key = $col === 'date' ? 'summary_date' : $col;
            $fields[$key] = 'as.' . $col;
        }
        $db->select(array_merge($columns, [
            DB::raw("DATE_FORMAT(as.date, '%d %b %Y') as summary_date"),
            DB::raw('as.date as date'),
            DB::raw("DATE_FORMAT(as.created_at, '%d %b %Y %H:%i %p') as created_at"),
            DB::raw("DATE_FORMAT(as.updated_at, '%d %b %Y %H:%i %p') as updated_at"),
        ]));

        if (isset($filters['filter_by_category']) && $filters['filter_by_category'] != 'null') {
            $db->where('as.category_wise_id', $filters['filter_by_category']);
            $totalRecords = $db->count();
        } else {
            $totalRecords = $db->count();
        }

        if (!empty($req['search'])) {
            $searchKey = trim($req['search']);
            $db->where(function ($query) use ($columns, $searchKey) {
                foreach ($columns as $col) {
                    $colSafe = "`$col`";
                    $query->orWhereRaw("DATE_FORMAT({$colSafe}, '%d %b %Y') LIKE ?", ["%{$searchKey}%"])->orWhereRaw("{$colSafe} LIKE ?", ["%{$searchKey}%"]);
                }
            });
        }

        $basedOnMapping = [
            '1' => 'created_at',
            '2' => 'updated_at',
        ];

        if (!empty($req['filters'])) {
            $filters = $req['filters'];
            if (!empty($filters['based_on']) && isset($basedOnMapping[$filters['based_on']])) {
                $basedOnColumn = $basedOnMapping[$filters['based_on']];

                if (!empty($filters['date_range'])) {
                    [$startDate, $endDate] = explode(' - ', $filters['date_range']);
                    $startDate = date('Y-m-d', strtotime($startDate));
                    $endDate = date('Y-m-d', strtotime($endDate));
                    $db->whereBetween('date', [$startDate, $endDate]);
                }
            }
        } else {
            $db->where('as.date', '>=', Carbon::now()->subDays(30)->format('Y-m-d'));
        }
        $filteredRecords = $db->count();

        if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ['asc', 'desc'])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        } else {
            $db->orderBy('as.date', 'desc');
        }
        // if (!empty($req["order"])) {
        //     $orderColumnIndex = $req["order"][0]["column"];
        //     $orderDir = $req["order"][0]["dir"];
        //     if (isset($columns[$orderColumnIndex])) {
        //         $db->orderBy($columns[$orderColumnIndex], $orderDir);
        //     }
        // }

        $skip = $req['start'] ?? 0;
        $take = $req['length'] ?? 10;
        $db->skip((int) $skip)->take((int) $take);

        $data = $db->get();
        // array_unshift($columns, 'summary_date');
        array_splice($columns, 1, 0, 'summary_date');
        $return = [
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'columns' => $columns,
            'data' => $data
        ];

        return response()->json($return);
    }

    public function ajaxBalanceSummary(Request $request)
    {
        $filters = $request->input('filters', []);
        if (isset($filters['filter_by_category']) && $filters['filter_by_category'] != 'null') {
            $yesterdayData = CategoryWiseAssetSummary::first();
        } else {
            $yesterdayData = AssetSummary::first();
        }
        $yesterday = $yesterdayData->date ?? null;
        $today = now()->toDateString();
        if (!empty($filters['based_on']) && $filters['based_on'] != 'null') {
            $dateRange = $filters['date_range'] ?? null;
            if ($dateRange) {
                [$startDate, $endDate] = explode(' - ', $dateRange);
                $yesterday = Carbon::parse($startDate)->toDateString();
                $today = Carbon::parse($endDate)->toDateString();
                $yesterdayliveDate = Carbon::yesterday()->toDateString();
                if ($yesterday == $today && $yesterdayliveDate == $yesterday && $yesterdayliveDate == $today) {
                    $dayBeforeYesterday = Carbon::yesterday()->subDay();
                    $yesterday = $dayBeforeYesterday->toDateString();
                    $today = Carbon::parse($endDate)->toDateString();
                } else if ($yesterday == $today) {
                    $yesterday = Carbon::yesterday()->toDateString();
                    $today = Carbon::parse($endDate)->toDateString();
                }
            }
        }

        if (isset($filters['filter_by_category']) && $filters['filter_by_category'] != 'null') {
            $columns = Schema::getColumnListing('category_wise_asset_summaries');
            $yesterdayData = DB::table('category_wise_asset_summaries as as');
            $todayData = DB::table('category_wise_asset_summaries as as');
            $excludeColumns = ['id', 'category_wise_id', 'created_at', 'updated_at'];
        } else {
            $columns = Schema::getColumnListing('asset_summary');
            $yesterdayData = DB::table('asset_summary as as');
            $todayData = DB::table('asset_summary as as');
            $excludeColumns = ['id', 'created_at', 'updated_at'];
        }
        $columns = array_values(array_diff($columns, $excludeColumns));
        $yesterdayData = $yesterdayData->select(array_merge($columns, [
            DB::raw("DATE_FORMAT(as.date, '%d %b %Y') as date"),
        ]));
        if (isset($filters['filter_by_category']) && $filters['filter_by_category'] != 'null') {
            $yesterdayData = $yesterdayData->where('as.category_wise_id', $filters['filter_by_category']);
        }
        $yesterdayData = $yesterdayData
            ->whereDate('as.date', $yesterday)
            ->first();

        $todayData = $todayData->select(array_merge($columns, [
            DB::raw("DATE_FORMAT(as.date, '%d %b %Y') as date"),
            DB::raw("DATE_FORMAT(as.created_at, '%d %b %Y %H:%i:%s') as created_at"),
            DB::raw("DATE_FORMAT(as.updated_at, '%d %b %Y %H:%i:%s') as updated_at"),
        ]));
        if (isset($filters['filter_by_category']) && $filters['filter_by_category'] != 'null') {
            $todayData = $todayData->where('as.category_wise_id', $filters['filter_by_category']);
        }
        $todayData = $todayData
            ->whereDate('as.date', $today)
            ->first();

        $allData = [
            array_merge(
                ['label' => 'Opening Balance'],
                $yesterdayData ? array_intersect_key((array) $yesterdayData, array_flip($columns)) : array_fill_keys($columns, '-')
            ),
            array_merge(
                ['label' => 'Closing Balance'],
                $todayData ? array_intersect_key((array) $todayData, array_flip($columns)) : array_fill_keys($columns, '-')
            )
        ];

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        if (!empty($request['sorted_column_name']) && !empty($request['sorted_direction'])) {
            $column = $request['sorted_column_name'] ?? 'date';
            $direction = strtolower($request['sorted_direction']) === 'desc' ? 'desc' : 'asc';

            usort($allData, function ($a, $b) use ($column, $direction) {
                $valueA = $a[$column] ?? null;
                $valueB = $b[$column] ?? null;
                if ($valueA === null && $valueB === null)
                    return 0;
                if ($valueA === null)
                    return $direction === 'asc' ? -1 : 1;
                if ($valueB === null)
                    return $direction === 'asc' ? 1 : -1;
                if ($column === 'date') {
                    $timeA = strtotime($valueA);
                    $timeB = strtotime($valueB);
                    return $direction === 'asc' ? $timeA <=> $timeB : $timeB <=> $timeA;
                }
                if (is_numeric($valueA) && is_numeric($valueB)) {
                    return $direction === 'asc' ? $valueA <=> $valueB : $valueB <=> $valueA;
                }
                return $direction === 'asc' ? strcmp($valueA, $valueB) : strcmp($valueB, $valueA);
            });
        }
        $data = array_slice($allData, $start, $length);

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => count($allData),
            'recordsFiltered' => count($allData),
            'columns' => $columns,
            'data' => $data,
        ]);
    }

    public function exportSummary(Request $request)
    {
        $req = $request->all();
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if (isset($filters->other_filters)) {
                $req['filters'] = (array) $filters->other_filters;
            }
            if (!empty($req['filters'])) {
                $filters = $req['filters'];
                if (isset($filters['filter_by_category']) && $filters['filter_by_category'] != 'null') {
                    $db = DB::table('category_wise_asset_summaries as as');
                    $columns = Schema::getColumnListing('category_wise_asset_summaries');
                    $excludeColumns = ['id', 'category_wise_id', 'created_at'];
                } else {
                    $db = DB::table('asset_summary as as');
                    $columns = Schema::getColumnListing('asset_summary');
                    $excludeColumns = ['id', 'created_at'];
                }
            }
        } else {
            $db = DB::table('asset_summary as as');
            $columns = Schema::getColumnListing('asset_summary');
            $excludeColumns = ['id', 'created_at'];
        }
        $columns = array_values(array_diff($columns, $excludeColumns));
        $formattedcol = array_map(function ($column) {
            return Str::of($column)->replace('_', ' ')->title();
        }, $columns);
        $db->select(array_merge($columns, [
            DB::raw("DATE_FORMAT(as.date, '%d %b %Y') as date"),
            DB::raw("DATE_FORMAT(as.created_at, '%d %b %Y %H:%i:%s') as created_at"),
            DB::raw("DATE_FORMAT(as.updated_at, '%d %b %Y %H:%i:%s') as updated_at"),
        ]));

        $totalRecords = $db->count();
        $basedOnMapping = [
            '1' => 'created_at',
            '2' => 'updated_at',
        ];

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if (isset($filters->search)) {
                $req['search'] = $filters->search;
            }
            if (isset($filters->other_filters)) {
                $req['filters'] = (array) $filters->other_filters;
            }
            if (!empty($req['filters'])) {
                $filters = $req['filters'];
                if (!empty($filters['based_on']) && isset($basedOnMapping[$filters['based_on']])) {
                    $basedOnColumn = $basedOnMapping[$filters['based_on']];
                    if (!empty($filters['date_range'])) {
                        [$startDate, $endDate] = explode(' - ', $filters['date_range']);
                        $startDate = date('Y-m-d', strtotime($startDate));
                        $endDate = date('Y-m-d', strtotime($endDate));
                        $db->whereBetween('date', [$startDate, $endDate]);
                    }
                } else {
                    $db->where('as.date', '>=', Carbon::now()->subDays(30)->format('Y-m-d'));
                }
                if (isset($filters['filter_by_category']) && $filters['filter_by_category'] != 'null') {
                    $db->where('as.category_wise_id', $filters['filter_by_category']);
                }
            }
            if (!empty($req['search'])) {
                $searchKey = trim($req['search']);

                $db->where(function ($query) use ($columns, $searchKey) {
                    foreach ($columns as $col) {
                        $colSafe = "`$col`";
                        $query->orWhereRaw("DATE_FORMAT({$colSafe}, '%d %b %Y') LIKE ?", ["%{$searchKey}%"])->orWhereRaw("{$colSafe} LIKE ?", ["%{$searchKey}%"]);
                    }
                });
            }
        }

        $filteredRecords = $db->count();

        if (!empty($req['order'])) {
            $orderColumnIndex = $req['order'][0]['column'];
            $orderDir = $req['order'][0]['dir'];
            if (isset($columns[$orderColumnIndex])) {
                $db->orderBy($columns[$orderColumnIndex], $orderDir);
            }
        }

        $data = $db->get();
        foreach ($data as $key => $value) {
            unset($value->created_at);
        }
        $result = json_decode(json_encode($data, true), true);
        return Excel::download(new AssetSummaryReport($result, $formattedcol), 'AssetSummary.xlsx');
    }

    public function getDeviceItems(Request $request, $id)
    {
        $skip = (int) $request->get('start', 0);
        $take = (int) $request->get('length', 10);

        // Components
        $componentsQuery = DB::table('components')->select('id', 'name', 'unique_tag')->where('checked_out_to', $id);

        $componentsTotal = $componentsQuery->count();
        $components = $componentsQuery->skip($skip)->take($take)->get();

        // Accessories
        $accessoriesQuery = DB::table('accessories_users as au')
            ->leftJoin('accessories as acc', 'au.accessory_id', '=', 'acc.id')
            ->select('au.id as acc_user_id', 'acc.name as name', 'acc.id as accessory_id');
        if (config('app.client') == 'etherealmachines') {
            $accessoriesQuery->addSelect(DB::raw('case when acc.id is not null then concat_ws("","AC",acc.id) else "" end as acc_batch_no'));
        } else {
            $accessoriesQuery->addSelect(DB::raw('case when acc.id is not null then concat_ws("","A",acc.id) else "" end as acc_batch_no'));
        }
        $accessoriesQuery
            ->where('au.assigned_to', $id)
            ->where('au.assigned_for', 3);

        $accessoriesTotal = $accessoriesQuery->count();
        $accessories = $accessoriesQuery->skip($skip)->take($take)->get();
        // Licenses
        $licensesQuery = DB::table('license_seats as ls')
            ->join('licenses as l', 'l.id', '=', 'ls.license_id')
            ->whereNull('l.deleted_at')
            ->whereNull('ls.deleted_at')
            ->where('ls.asset_id', $id)
            ->select('l.id', 'l.name', 'ls.id as lic_seat_id', DB::raw('CASE WHEN ls.id IS NOT NULL THEN CONCAT_WS("", "LIC", l.id) ELSE "" END AS batch_no'));

        $licensesTotal = $licensesQuery->count();
        $licenses = $licensesQuery->skip($skip)->take($take)->get();

        // Consumables
        $consumablesQuery = DB::table('consumables_users as cu')
            ->join('consumables as cns', 'cns.id', '=', 'cu.consumable_id')
            ->whereNull('cns.deleted_at')
            ->where('cu.assigned_to', $id)
            ->where('cu.assigned_for', 3)
            ->select('cns.id as cns_id', 'cns.name', 'cu.id as cu_id', 'cns.unique_tag as unique_tag');

        $consumablesTotal = $consumablesQuery->count();
        $consumables = $consumablesQuery->skip($skip)->take($take)->get();

        return response()->json([
            'status' => 'success',
            'components' => [
                'data' => $components,
                'recordsTotal' => $componentsTotal
            ],
            'accessories' => [
                'data' => $accessories,
                'recordsTotal' => $accessoriesTotal
            ],
            'licenses' => [
                'data' => $licenses,
                'recordsTotal' => $licensesTotal
            ],
            'consumables' => [
                'data' => $consumables,
                'recordsTotal' => $consumablesTotal
            ],
        ]);
    }

    public function showReport()
    {
        // Optional filters from request (sent by External dashboard)
        $filters = (array) request()->input('filters', []);
        // Date-only range parsing
        $from = $to = $location_id = $category_id = null;
        if (!empty($filters['date_range'])) {
            $dr = str_replace('/', '-', trim($filters['date_range']));
            $parts = explode(' - ', $dr);

            // try {
            if (count($parts) === 2) {
                // "DD-MM-YYYY - DD-MM-YYYY"
                $selected = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', trim($parts[0]));
                $from = $selected->copy()->subDay()->startOfDay();
                $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', trim($parts[1]))->endOfDay();
            } else if (count($parts) === 1 && !empty($parts[0])) {
                // Single day selected (e.g., "DD-MM-YYYY"): opening = previous day, closing = selected day
                $selected = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', trim($parts[0]));
                $from = $selected->copy()->subDay()->startOfDay();
                $to = $selected->copy()->endOfDay();
            }
            // } catch (\Exception $e) {
            //     // ignore parsing errors; fallback below
            // }
        }
        if (!$from || !$to) {
            // Fallback to last 30 days if not provided
            $to = \Carbon\Carbon::now();
            $from = \Carbon\Carbon::now()->subDays(30)->startOfDay();
        }

        if (isset($filters['filter_by_locations']) && $filters['filter_by_locations'] != 'null') {
            $location_id = $filters['filter_by_locations'];
        }
        if (isset($filters['filter_by_category']) && $filters['filter_by_category'] != 'null') {
            $category_id = $filters['filter_by_category'];
        }

        // Dynamic example: “Returned by employees leaving”
        $returnedByEmployees = \DB::table('asset_logs')
            ->Join('assets as as', 'asset_logs.asset_id', '=', 'as.id')
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            ->leftJoin('itm_asset_allocation_type', 'asset_logs.allocation_type_id', '=', 'itm_asset_allocation_type.id')
            ->leftJoin('interacted_records as ir', 'asset_logs.id', '=', 'ir.interact_log_id')
            ->leftJoin('status_labels as sl', 'ir.status_id', '=', 'sl.id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->where('asset_logs.asset_type', 'hardware')
            ->where('asset_logs.action_type', 'Checkin')
            ->where('asset_logs.assigned_for', 1)
            // ->where('sl.name', 'Ready to Deploy')
            ->where('as.deleted_at', null)
            ->where(function ($q) {
                $q
                    ->where('itm_asset_allocation_type.name', '!=', 'Temporary')
                    ->orWhereNull('asset_logs.allocation_type_id');
            })
            ->whereBetween('asset_logs.created_at', [$from, $to])
            ->distinct('asset_logs.asset_id')
            ->count();

        // New Laptops delivered by vendors
        $newLaptopsDeliveredByVendors = \DB::table('asset_logs')
            ->Join('assets as as', 'asset_logs.asset_id', '=', 'as.id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            // ->leftJoin('status_labels as sl', 'sl.id', '=', 'ic.status_id')
            ->where('asset_logs.asset_type', 'hardware')
            ->where('asset_logs.action_type', 'New Add')
            ->where('as.deleted_at', null)
            // ->where('sl.name', 'Ready to Deploy')
            ->whereBetween('asset_logs.created_at', [$from, $to])
            ->distinct('asset_logs.asset_id')
            ->count();

        // Returned in replacement cases //need to remove this currently dont want this
        $returnedInReplacementCases = \DB::table('asset_logs')
            ->Join('assets as as', 'asset_logs.asset_id', '=', 'as.id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            ->leftJoin('itm_asset_allocation_type', 'asset_logs.allocation_type_id', '=', 'itm_asset_allocation_type.id')
            // ->leftJoin('interacted_caches as ic', 'ic.interact_log_id', '=', 'asset_logs.id')
            // ->leftJoin('status_labels as sl', 'sl.id', '=', 'ic.status_id')
            ->where('asset_logs.asset_type', 'hardware')
            ->where('asset_logs.action_type', 'Checkin')
            // ->where('sl.name', 'Ready to Deploy')
            ->where('itm_asset_allocation_type.name', 'Replacement')
            ->where('as.deleted_at', null)
            ->whereBetween('asset_logs.created_at', [$from, $to])
            ->distinct('asset_logs.asset_id')
            ->count();

        // Returned Temporary Laptop
        $returnedTemporaryLaptop = \DB::table('asset_logs as checkin')
            ->join('asset_logs as checkout', 'checkout.id', '=', 'checkin.in_out_id')
            ->Join('assets as as', 'checkin.asset_id', '=', 'as.id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            ->leftJoin('itm_asset_allocation_type as at', 'checkout.allocation_type_id', '=', 'at.id')
            // ->leftJoin('interacted_caches as ic', 'ic.interact_log_id', '=', 'asset_logs.id')
            // ->leftJoin('status_labels as sl', 'sl.id', '=', 'ic.status_id')
            ->where('checkin.asset_type', 'hardware')
            ->where('checkin.action_type', 'Checkin')
            ->where('as.deleted_at', null)
            // ->where('sl.name', 'Ready to Deploy')
            ->where('checkout.action_type', 'Checkout')
            ->where('at.name', 'Temporary')
            ->whereBetween('checkin.created_at', [$from, $to])
            ->distinct('checkin.asset_id')
            ->count();

        // Issued to new joinees
        $issuedToNewJoinees = \DB::table('asset_logs as al')
            ->Join('assets as as', 'al.asset_id', '=', 'as.id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            ->where('al.asset_type', 'hardware')
            ->where('al.action_type', 'Checkout')
            ->where('al.assigned_for', 1)
            ->whereBetween('al.created_at', [$from, $to])
            ->whereNotExists(function ($query) {
                $query
                    ->select(\DB::raw(1))
                    ->from('asset_logs as al2')
                    ->whereColumn('al2.checkedout_to', 'al.checkedout_to')
                    ->where('al2.action_type', 'Checkout')
                    ->where('al2.asset_type', 'hardware')
                    ->where('al2.assigned_for', 1)
                    ->whereColumn('al2.id', '!=', 'al.id');  // exclude same row
            })
            // ->where('as.status_id',6) //status = Deployed from assets table
            ->where('as.deleted_at', null)
            ->distinct('al.asset_id')
            ->count();

        // Temporary laptop assigned
        $temporaryLaptopAssigned = \DB::table('asset_logs')
            ->Join('assets as as', 'asset_logs.asset_id', '=', 'as.id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            ->leftJoin('itm_asset_allocation_type', 'asset_logs.allocation_type_id', '=', 'itm_asset_allocation_type.id')
            // ->leftJoin('interacted_caches as ic', 'ic.interact_log_id', '=', 'asset_logs.id')
            // ->leftJoin('status_labels as sl', 'sl.id', '=', 'ic.status_id')
            ->where('asset_logs.asset_type', 'hardware')
            ->where('asset_logs.action_type', 'Checkout')
            ->where('as.deleted_at', null)
            // ->where('sl.name', 'Ready to Deploy')
            ->where('itm_asset_allocation_type.name', 'Temporary')
            ->whereBetween('asset_logs.created_at', [$from, $to])
            ->distinct('asset_logs.asset_id')
            ->count();

        // Disposed off
        $disposedOff = \DB::table('asset_logs')
            ->Join('assets as as', 'asset_logs.asset_id', '=', 'as.id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            // ->leftJoin('itm_asset_allocation_type', 'asset_logs.allocation_type_id', '=', 'itm_asset_allocation_type.id')
            // ->leftJoin('users as u', 'u.id', '=', 'asset_logs.checkedout_to')
            ->where('asset_logs.asset_type', 'hardware')
            ->where('asset_logs.action_type', 'Changes')  // Dispose
            ->where('as.status_id', 3)  // 3 = Scrap & 7 = Sold
            ->where('as.deleted_at', null)
            // ->where('itm_asset_allocation_type.name', 'Dispose')
            ->whereBetween('asset_logs.created_at', [$from, $to])
            ->distinct('asset_logs.asset_id')
            ->count();

        // INTERNAL SCENARIOS

        // Laptops sent for in house repairs //need confirmation on this point
        $laptopsSentForInHouseRepairs = \DB::table('interacted_records as ir')
            ->Join('assets as as', 'ir.serial', '=', 'as.serial')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            ->leftJoin('interacted_caches as ic', 'ir.id', '=', 'ic.interact_record_id')
            ->leftJoin('status_labels as sl_ir', 'ir.status_id', '=', 'sl_ir.id')
            ->leftJoin('status_labels as sl_ic', 'ic.status_id', '=', 'sl_ic.id')
            ->where('sl_ir.name', 'Repair Inhouse')
            ->where('sl_ic.name', 'Ready to Deploy')
            ->whereBetween('ir.created_at', [$from, $to])
            ->where('as.deleted_at', null)
            ->distinct('ir.serial')
            ->count();

        // Sent to repairs to vendors
        $laptopsSentToRepairsToVendors = \DB::table('interacted_records as ir')
            ->leftJoin('assets as as', 'ir.serial', '=', 'as.serial')
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            // ->leftJoin('interacted_caches as ic', 'ir.id', '=', 'ic.interact_log_id')
            ->leftJoin('status_labels as sl_ir', 'ir.status_id', '=', 'sl_ir.id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            // ->leftJoin('status_labels as sl_ic', 'sl_ic.id', '=', 'ic.status_id')
            ->where('sl_ir.name', 'Out for Repair')
            // ->where('sl_ic.name', 'Ready to Deploy')
            ->where('as.deleted_at', null)  // is it required to check deleted device ?
            ->whereBetween('ir.created_at', [$from, $to])
            ->distinct('ir.serial')
            ->count();

        // Returned after repairs by vendor
        $returnedAfterRepairsByVendor = \DB::table('interacted_records as ir')
            ->Join('assets as as', 'ir.serial', '=', 'as.serial')
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            ->Join('interacted_caches as ic', 'ir.id', '=', 'ic.interact_record_id')
            ->Join('status_labels as sl_ir', 'sl_ir.id', '=', 'ir.status_id')
            ->Join('status_labels as sl_ic', 'sl_ic.id', '=', 'ic.status_id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->where('sl_ir.name', 'Ready to Deploy')
            ->where('sl_ic.name', 'Out for Repair')
            ->where('as.deleted_at', null)
            ->whereBetween('ir.created_at', [$from, $to])
            ->distinct('ir.serial')
            ->count();

        // $returnedAfterRepairsByVendor = \DB::table('interacted_caches as ic')
        //     ->Join('assets as as', 'ic.serial', '=', 'as.serial')
        //     //->Join('interacted_caches as ic', 'ir.id', '=', 'ic.interact_log_id')
        //     //->Join('status_labels as sl_ir', 'sl_ir.id', '=', 'ir.status_id')
        //     ->Join('status_labels as sl_ic', 'sl_ic.id', '=', 'ic.status_id')
        //     //->where('sl_ir.name', 'Ready to Deploy')
        //     ->where('sl_ic.name', 'Out for Repair')
        //     ->where('as.deleted_at', null)
        //     ->whereBetween('ic.created_at', [$from, $to])
        //     ->distinct('ic.serial')
        //     ->count();
        // dd($returnedAfterRepairsByVendor);

        // Laptop repaired in house and made good
        $laptopsRepairedInHouseAndMadeGood = \DB::table('interacted_records as ir')
            ->Join('assets as as', 'ir.serial', '=', 'as.serial')
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            ->leftJoin('interacted_caches as ic', 'ir.id', '=', 'ic.interact_record_id')
            ->leftJoin('status_labels as sl_ir', 'sl_ir.id', '=', 'ir.status_id')
            ->leftJoin('status_labels as sl_ic', 'sl_ic.id', '=', 'ic.status_id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->where('sl_ir.name', 'Ready to Deploy')
            ->where('sl_ic.name', 'Repair Inhouse')
            ->where('as.deleted_at', null)
            ->whereBetween('ir.created_at', [$from, $to])
            ->distinct('ir.serial')
            ->count();

        // Laptops found irreparable and made unsable
        $laptopsFoundIrreparableAndMadeUnusable = \DB::table('interacted_records as ir')
            ->Join('assets as as', 'ir.serial', '=', 'as.serial')
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            ->leftJoin('interacted_caches as ic', 'ir.id', '=', 'ic.interact_record_id')
            ->leftJoin('status_labels as sl_ir', 'sl_ir.id', '=', 'ir.status_id')
            ->leftJoin('status_labels as sl_ic', 'sl_ic.id', '=', 'ic.status_id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->where('sl_ir.name', 'Scrap')
            ->where('sl_ic.name', 'Repair Inhouse')
            ->where('as.deleted_at', null)
            ->whereBetween('ir.created_at', [$from, $to])
            ->distinct('ir.serial')
            ->count();

        // Not repairable by Vendor
        $laptopsNotRepairableByVendor = \DB::table('interacted_records as ir')
            ->Join('assets as as', 'ir.serial', '=', 'as.serial')
            ->when(!empty($location_id) && $location_id !== 'null', function ($q) use ($location_id) {
                return $q->where('as.rtd_location_id', $location_id);
            })
            ->leftJoin('interacted_caches as ic', 'ir.id', '=', 'ic.interact_record_id')
            ->leftJoin('status_labels as sl_ir', 'sl_ir.id', '=', 'ir.status_id')
            ->leftJoin('status_labels as sl_ic', 'sl_ic.id', '=', 'ic.status_id')
            ->Join('models as m', 'as.model_id', '=', 'm.id')
            ->Join('categories as c', 'm.category_id', '=', 'c.id')
            ->when(!empty($category_id) && $category_id !== 'null', function ($q) use ($category_id) {
                return $q->where('c.id', $category_id);
            })
            ->where('sl_ir.name', 'Repair Inhouse')
            ->where('sl_ic.name', 'Out for Repair')
            ->where('as.deleted_at', null)
            ->whereBetween('ir.created_at', [$from, $to])
            ->distinct('ir.serial')
            ->count();

        // Opening / Closing dates
        $openingDate = $from->format('d-m-Y');
        $closingDate = $to->format('d-m-Y');

        // Normalize opening date to Y-m-d for DB lookup
        $dbOpeningDate = \Carbon\Carbon::createFromFormat('d-m-Y', $openingDate)->format('Y-m-d');

        // Fetch opening-balance data from asset_summary
        $openingSummary = \DB::table('asset_summary')
            ->where('date', $dbOpeningDate)
            ->select(
                'ready_to_deploy as usable',
                'repair_inhouse as repairs',
                'out_for_repair as vendor',
                'scrap as unusable'
            )
            ->first();

        // Fallback to zero if no record found
        $obUsable = $openingSummary ? (int) $openingSummary->usable : 0;
        $obRepairs = $openingSummary ? (int) $openingSummary->repairs : 0;
        $obVendor = $openingSummary ? (int) $openingSummary->vendor : 0;
        $obUnusable = $openingSummary ? (int) $openingSummary->unusable : 0;
        $obTotal = $obUsable + $obRepairs + $obVendor + $obUnusable;

        // Payload keys aligned to the dashboard cells
        $report = [
            // Opening Balance
            'ob_date' => $openingDate,
            'ob_usable' => $obUsable,
            'ob_repairs' => $obRepairs,
            'ob_vendor' => $obVendor,
            'ob_unusable' => $obUnusable,
            'ob_total' => $obTotal,
            // External
            'ext_emp_usable' => (int) $returnedByEmployees,
            'ext_emp_total' => (int) $returnedByEmployees,
            'ext_new_usable' => $newLaptopsDeliveredByVendors,
            'ext_new_total' => $newLaptopsDeliveredByVendors,
            'ext_replacement_usable' => $returnedInReplacementCases,
            'ext_replacement_total' => $returnedInReplacementCases,
            'ext_return_temp_usable' => $returnedTemporaryLaptop,
            'ext_return_temp_total' => $returnedTemporaryLaptop,
            // 'ext_issued_replacement_usable' => '- '.$issuedToReplacementCases,
            // 'ext_issued_replacement_total' => '- '.$issuedToReplacementCases,
            'ext_new_joiners_usable' => $issuedToNewJoinees > 0 ? '- ' . $issuedToNewJoinees : $issuedToNewJoinees,
            'ext_new_joiners_total' => $issuedToNewJoinees > 0 ? '- ' . $issuedToNewJoinees : $issuedToNewJoinees,
            'ext_temp_assigned_usable' => $temporaryLaptopAssigned > 0 ? '- ' . $temporaryLaptopAssigned : $temporaryLaptopAssigned,
            'ext_temp_assigned_total' => $temporaryLaptopAssigned > 0 ? '- ' . $temporaryLaptopAssigned : $temporaryLaptopAssigned,
            'ext_disposed_unusable' => $disposedOff > 0 ? '- ' . $disposedOff : $disposedOff,
            'ext_disposed_total' => $disposedOff > 0 ? '- ' . $disposedOff : $disposedOff,
            $sa_usable = (int) $returnedByEmployees + (int) $newLaptopsDeliveredByVendors + (int) $returnedInReplacementCases + (int) $returnedTemporaryLaptop - (int) $issuedToNewJoinees - (int) $temporaryLaptopAssigned,
            $sa_repairs = 0,
            $sa_vendors = 0,
            $sa_unusable = -(int) $disposedOff,
            $sa_total = $sa_usable + $sa_repairs + $sa_vendors + $sa_unusable,
            // Sub Total A
            'sa_usable' => $sa_usable,
            'sa_repairs' => 0,
            'sa_vendor' => 0,
            'sa_unusable' => $sa_unusable,
            'sa_total' => $sa_total,
            // Internal
            'int_sent_inhouse_usable' => $laptopsSentForInHouseRepairs > 0 ? '- ' . (int) $laptopsSentForInHouseRepairs : (int) $laptopsSentForInHouseRepairs,
            'int_sent_inhouse_repairs' => (int) $laptopsSentForInHouseRepairs,
            'int_sent_inhouse_total' => (int) $laptopsSentForInHouseRepairs - (int) $laptopsSentForInHouseRepairs,
            'int_sent_vendors_vendor' => (int) $laptopsSentToRepairsToVendors,
            'int_sent_vendors_total' => (int) $laptopsSentToRepairsToVendors,
            'int_returned_vendor_usable' => (int) $returnedAfterRepairsByVendor,
            'int_returned_vendor_vendor' => $returnedAfterRepairsByVendor > 0 ? '- ' . (int) $returnedAfterRepairsByVendor : (int) $returnedAfterRepairsByVendor,
            'int_returned_vendor_total' => (int) $returnedAfterRepairsByVendor - (int) $returnedAfterRepairsByVendor,
            'int_repaired_inhouse_usable' => (int) $laptopsRepairedInHouseAndMadeGood,
            'int_repaired_inhouse_repairs' => $laptopsRepairedInHouseAndMadeGood > 0 ? '- ' . (int) $laptopsRepairedInHouseAndMadeGood : (int) $laptopsRepairedInHouseAndMadeGood,
            'int_repaired_inhouse_total' => (int) $laptopsRepairedInHouseAndMadeGood - (int) $laptopsRepairedInHouseAndMadeGood,
            'int_irreparable_repairs' => $laptopsFoundIrreparableAndMadeUnusable > 0 ? '- ' . (int) $laptopsFoundIrreparableAndMadeUnusable : (int) $laptopsFoundIrreparableAndMadeUnusable,
            'int_irreparable_unusable' => (int) $laptopsFoundIrreparableAndMadeUnusable,
            'int_irreparable_total' => (int) $laptopsFoundIrreparableAndMadeUnusable - (int) $laptopsFoundIrreparableAndMadeUnusable,
            'int_not_repairable_vendor' => (int) $laptopsNotRepairableByVendor,
            'int_not_repairable_unusable' => $laptopsNotRepairableByVendor > 0 ? '- ' . (int) $laptopsNotRepairableByVendor : (int) $laptopsNotRepairableByVendor,
            'int_not_repairable_total' => (int) $laptopsNotRepairableByVendor - (int) $laptopsNotRepairableByVendor,
            $sb_usable = -(int) $laptopsSentForInHouseRepairs + (int) $returnedAfterRepairsByVendor + (int) $laptopsRepairedInHouseAndMadeGood,
            $sb_repairs = (int) $laptopsSentForInHouseRepairs - (int) $laptopsRepairedInHouseAndMadeGood - (int) $laptopsFoundIrreparableAndMadeUnusable + (int) $laptopsNotRepairableByVendor,
            $sb_vendor = (int) $laptopsSentToRepairsToVendors - (int) $returnedAfterRepairsByVendor - (int) $laptopsNotRepairableByVendor,
            $sb_unusable = (int) $laptopsFoundIrreparableAndMadeUnusable,
            $sb_total = $sb_usable + $sb_repairs + $sb_vendor + $sb_unusable,
            // Sub Total B
            'sb_usable' => $sb_usable,
            'sb_repairs' => $sb_repairs,
            'sb_vendor' => $sb_vendor,
            'sb_unusable' => $sb_unusable,
            'sb_total' => $sb_total,
            // Closing Balance
            'cb_date' => $closingDate,
            'cb_usable' => $obUsable + $sa_usable + $sb_usable,
            'cb_repairs' => $obRepairs + $sa_repairs + $sb_repairs,
            'cb_vendor' => $obVendor + $sa_vendors + $sb_vendor,
            'cb_unusable' => $obUnusable + $sa_unusable + $sb_unusable,
            'cb_total' => $obTotal + $sa_total + $sb_total,
        ];

        return response()->json($report);
    }

    public function requestNotifyDevice(Request $request, $id)
    {
        try {
            $return = [];
            $objDevice = Device::where('id', $id)->where('requestable', 1)->first();
            $user = Auth::user();

            if (!$objDevice) {
                return response()->json(['status' => 'error', 'msg' => 'Device not found !!']);
            }

            if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($objDevice)) {
                $return['status'] = 'error';
                $return['section'] = 'request-device';
                $return['msg'] = Auth::user()->company_id == null ? trans('content.device_fields.update_company_name') : trans('content.device_fields.multiple_company_access');
                return response()->json($return);
            }

            $db = new RequestableNotifyUser();
            if ($db->whereRaw("notified_user= $user->id and notified_device= $objDevice->id")->exists()) {
                return response()->json(['status' => 'error', 'msg' => 'You have already requested a notification for this device']);
            }

            $db->notified_user = $user->id;
            $db->notified_device = $objDevice->id;
            $db->save();

            return response()->json(['status' => 'success', 'section' => 'approve-request', 'msg' => trans('content.requestable_device_fields.request_notify')]);
        } catch (\Throwable $th) {
            log::error('deviceNotifyRequest' . $e->getMessage());
            return response()->json(['status' => 'error', 'msg' => 'Somthing went wrong !!']);
        }
    }

    public function sendAcceptanceMail()
    {
        try {
            $users = User::select('id', 'email', 'username')->where('activated', 1)->whereNotNull('email')->withCount(['devices' => function ($q) {
                $q->where('accepted', 'pending');
            }])->with(['devices' => function ($q) {
                $q->where('accepted', 'pending');
            }])->orderBy('id', 'desc')->get();
            foreach ($users as $user) {
                if ($user->devices_count > 0) {
                    if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        $devicesWithLogs = $user->devices->filter(function ($device) {
                            return !empty($device->chkoutLog);
                        });
                        if ($devicesWithLogs->count() > 0) {
                            $fileName = "User_Pending_Asset_Report_{$user->id}_" . time() . '.xlsx';
                            // Export to Excel
                            Excel::store(new PendingAssetExport($user->devices), $fileName, 'user_pending_asset');
                            Mail::to($user->email)->queue(new UserAssetCheckoutPendingMail($user, $fileName));
                        }
                    } else {
                        Log::warning("Invalid email skipped: {$user->email} (User ID: {$user->id})");
                    }
                }
            }
            return response()->json(['status' => 'success', 'msg' => 'Mail successfully sent to users!']);
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'msg' => 'Something went wrong while sending mail.' . $e->getMessage()]);
        }
    }


    public function getReasonOptions(Request $request)
    {
        $search = $request->input('search');
        $actionType = $request->input('action_type');

        $db = DB::table('asset_inout_reason as air')
            ->select('air.id', 'air.name')
            ->where('air.status', 1)
            ->whereNull('air.deleted_at');
        // Filter by action_type (1 = checkout, 2 = checkin)
        if (!empty($actionType)) {
            $db->where('air.action_type', $actionType);
        }

        if (!empty($search)) {
            $db->where('air.name', 'like', '%' . $search . '%');
        }

        $results = $db->orderBy('air.name')->get();

        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name
            ];
        }

        return response()->json(['results' => $data]);
    }

    private function buildInfoTabData($id) {
        $return = [];
        try {
            $device = Device::where('id', '=', $id)->first();
            if ($device->exists) {
                $transfer_items = $device->transitems();
                $warrantyByFeed = $device->getWarrantyByFeed();
                $warrantyByEndDate = $device->getWarrantyByEndDate();
                $warrantyEndDate = $device->getWarrantyEndDate();
                $warrentyInfo = $device->getLiveWarrentyInfo();
                $warrantyExpiredStatus = $device->getWarrantyExpiredStatus();
                $purchase_currency = Currency::getSymbolByCode($device->purchase_currency);
                $sold_value_format = Currency::getSymbolByCode($device->sold_value_format);
                $dispose = AssetDispose::where('asset_id', $id)->latest('updated_at')->first();
                $checkOutPlaceNote = null;
                if (config('app.client') == 'mgmotor' && isset($device) && $device->assigned_to != null && $device->chkout_log_id != null) {
                    $logNote = DB::table('asset_logs')->where('asset_logs.id', $device->chkout_log_id)->select('note')->first();
                    $checkOutPlaceNote = $logNote->note;
                }
                $dispose_data = null;
                if ($dispose != null) {
                    if ($dispose->dispose_type == 3) {
                        $dispose_data = AssetDispose::with(['vendorName:id,name', 'updatedBy:id,first_name,last_name', 'disposedBy:id,first_name,last_name'])->where('id', $dispose->id)->first();
                    } else {
                        $dispose_data = AssetDispose::with(['updatedBy:id,first_name,last_name', 'disposedBy:id,first_name,last_name'])->where('id', $dispose->id)->first();
                    }
                }

                $companyFieldset = [];
                $fieldsetObj = null;
                $globalDeviceCustomFieldObj = Settings::first();
                if (!empty($globalDeviceCustomFieldObj) && $globalDeviceCustomFieldObj->custom_fieldset_id != null) {
                    $fieldsetObj = CustomFieldset::where('id', $globalDeviceCustomFieldObj->custom_fieldset_id)->first();
                    if (!empty($fieldsetObj)) {
                        $companyFieldset = CommonHelper::getCustomData($fieldsetObj->fields, $device);
                    }
                }

                $checkoutReasonName = "";
                if($device->assigned_to != null) {
                    $checkoutReasonName = ActionLog::where('asset_logs.id', $device->chkout_log_id)
                    ->leftJoin('asset_inout_reason as aior', 'aior.id', '=', 'asset_logs.reason_id')
                    ->value('aior.name') ?? '';
                }

                $company = null;
                if($device->company_id != null) {
                    $company = DB::table('companies')->where('id', $device->company_id)->select('logo')->first();
                }
                $return["company_logo"] = !empty($company) && !empty($company->logo) ? url("/") . "/" . $company->logo : (!empty(CommonHelper::CompLogo()) ? CommonHelper::CompLogo() : Settings::getSettings()->logo_thumbnail);
                
                $return['device_img'] = null;
                if (!empty($device->image)) {
                    $return['device_img'] = url('uploads/devices/' . $device->image);
                } elseif (!empty($device->model?->image_thumbnail)) {
                    $return['device_img'] = url('uploads/models/' . $device->model->image_thumbnail);
                } elseif (!empty($device->model?->manufacturer?->attachment)) {
                    $return['device_img'] = url('uploads/manufacturers/' . $device->model->manufacturer->attachment);
                } elseif (!empty($device->model?->category?->image_thumbnail)) {
                    $return['device_img'] = url('uploads/category/' . $device->model->category->image_thumbnail);
                }

                $return['qr'] = url('qrcode/' . $device->id);
                $return['map_loc'] = [];
                try {
                    if ($device->niDetectedLocation) {
                        $return['map_loc'] = $device->niDetectedLocation->getValuesForMap();
                    } elseif ($device->location) {
                        $return['map_loc'] = $device->location->getValuesForMap();
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return compact('device', 'warrantyEndDate', 'warrentyInfo', 'warrantyByFeed', 'warrantyByEndDate', 'warrantyExpiredStatus', 'purchase_currency', 'sold_value_format', 'transfer_items', 'companyFieldset', 'fieldsetObj', 'dispose_data', 'checkOutPlaceNote', 'checkoutReasonName', 'return');
    }
}

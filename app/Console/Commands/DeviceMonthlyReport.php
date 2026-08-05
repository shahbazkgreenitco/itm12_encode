<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device\DeviceSetting;
use App\Models\Settings;
use DB;
use App\Helpers\Common as CommonHelper;
use Illuminate\Support\Facades\Schema;
use App\Models\CustomFieldset;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DeviceImportStore;
use App\Mail\Asset\DeviceMonthlyNotification;
use Carbon\Carbon;
use Mail;


class DeviceMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DeviceMonthlyReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates and sends the monthly device report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $objDeviceSettings = DeviceSetting::first();
        $settings = Settings::getSettings();
        $globalAlertEmails = CommonHelper::getGlobalAlertEmail();
        $emails = explode(',', $objDeviceSettings->device_report_email) ?? [];
        if (!config('mail.service_enabled') || empty($emails) || !array_filter($emails, function($mail) {return filter_var($mail, FILTER_VALIDATE_EMAIL);})|| !$settings->alerts_enabled || empty($globalAlertEmails) || !array_filter($globalAlertEmails, function($email) {return filter_var($email, FILTER_VALIDATE_EMAIL);})) {
            return;
        }
        if(!empty($emails)){
            $device_export_column = isset($objDeviceSettings->device_export_column) && $objDeviceSettings->device_export_column != null ? explode(',', $objDeviceSettings->device_export_column) : [];
            $export_column_empty = false;
            if(empty($device_export_column)) {
                $export_column_empty = true;
            }

            if(in_array("Custom Fields", $device_export_column) ||  $export_column_empty == true ) {
                $set = $get_custom_fields = '';
                $setting = Settings::getSettings()->custom_fieldset_id ? Settings::getSettings()->custom_fieldset_id : '';
                if($setting) {
                    $set = "where cfcf.custom_fieldset_id = " . $setting;
                    $get_custom_fields = DB::select("SELECT cfcf.custom_fieldset_id, CONCAT('a._itm_', replace(lcase(cf.NAME), ' ', '_')) AS cus_field FROM custom_fields AS cf
                    JOIN custom_field_custom_fieldset AS cfcf ON cf.id = cfcf.custom_field_id $set");
                }

                $coll_field_sets = $field_sets = $coll_custom_fields = $custom_fields = $custom_field_names = $field_based_fieldsets_ids = [];
                
                if($get_custom_fields) {
                    foreach($get_custom_fields as $cf) {
                        $coll_field_sets[] = $cf->custom_fieldset_id;
                        $coll_custom_fields[] = $cf->cus_field;
                        if(! isset($field_based_fieldsets_ids[$cf->cus_field])) {
                            $field_based_fieldsets_ids[$cf->cus_field] = [$cf->custom_fieldset_id];
                        }
                        else {
                            $field_based_fieldsets_ids[$cf->cus_field][] = $cf->custom_fieldset_id;
                        }
                    }

                    $field_sets = array_unique($coll_field_sets);
                    $custom_fields = array_unique($coll_custom_fields);

                    foreach($custom_fields as $cf) {
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
            $db->leftJoin('places as pla', 'pla.id', '=', 'a.internal_place_id');
            $db->leftJoin('locations as locByNi', 'locByNi.id', '=', 'a.ni_detected_location');
            $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');
            // $db->leftJoin('users as u', 'u.id', '=', 'a.assigned_to');
            $db->leftJoin('users as u', function($q) {
                $q->on('u.id', '=', 'a.assigned_to');
                $q->where('a.assigned_for', '=', '1');
            });
            $db->leftJoin('locations as assigned_user_loc', function($q) {
                $q->on('assigned_user_loc.id', '=', 'u.location_id');
                $q->where('a.assigned_for', '=', '1');
            });
            $db->leftJoin('places as assigned_user_place', function($q) {
                $q->on('assigned_user_place.id', '=', 'u.internal_place_id');
                $q->where('a.assigned_for', '=', '1');
            });

            $db->leftJoin('users as own', 'own.id', '=', 'a.asset_owner');
            $db->leftJoin('lease_agreements as lease', 'lease.id', '=', 'a.lease_id');
            $db->leftJoin('projects as pr', 'pr.id', '=', 'a.last_checkout_project');
            $db->leftJoin('status_labels as lbl', 'lbl.id', '=', 'a.status_id');
            $db->leftJoin('categories as cat', function($q) {
                $q->on('cat.id', '=', 'mdl.category_id');
                $q->where('mdl.category_id', '<>', 0);
            });
            $db->leftJoin('procure_account_types as acc_typ', 'acc_typ.id', '=', 'cat.account_type_id');
            $db->leftJoin('places as pldev', function($q) {
                $q->on('pldev.id', '=', 'a.stock_place');
            });
            $db->leftJoin('places as p', function($q) {
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
            $db->leftJoin('asset_logs as al1', function($join) {
                $join->on('a.chkout_log_id', '=', 'al1.id');
                $join->on('a.status_id', '=', DB::raw('6'));
            });
            $db->leftJoin('itm_asset_allocation_type as ata', 'ata.id', '=', 'al1.allocation_type_id');
            $db->leftJoin('itm_network_inventory_basic as itm', 'itm.device_id', '=', 'a.id');
            $db->leftJoin('itm_network_inventory_basic_azure as itm_azure', 'itm_azure.device_id', '=', 'a.id');
            if( $export_column_empty == true) {
                $db->select('cmp.name as cmp_name','a.name','a.asset_tag','a.serial','mnu.name as manu_name',DB::raw('trim(concat_ws(" ", mdl.name, mdl.modelno)) as mdl_full_name'),'cat.name as cat_name',DB::raw('case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end as lbl_name'), 'loc.name as loc_name','pla.place as internal_place', DB::raw('DATE_FORMAT(a.purchase_date, "%Y-%m-%d") as purchase_date_on'),'a.purchase_currency',DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format'),'pur.invoice_no as pur_invoice',DB::raw('DATE_FORMAT(a.ship_date, "%Y-%m-%d") as ship_date'),DB::raw('DATE_FORMAT(a.warranty_start_date, "%Y-%m-%d") as warranty_start_date'),DB::raw('DATE_FORMAT(a.warrenty_end_date, "%Y-%m-%d") as warranty_end_date'),'a.warranty_months','a.notes','a.order_number',DB::raw('concat(own.first_name, " ", own.last_name) as asset_owner'),'a.ip', 'a.mac',DB::raw('case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" else "Purchase Device" end as device_occure_type_name'), 'pldevloc.name AS stock_location', DB::raw('case when a.stock_place is not null and pldev.place is not null then concat(pldev.place, " ", pldevloc.name, "") else null end as stock_place'),'locByNi.name as locByNiName','s.name as supplier_name',
                    DB::raw('case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end as last_checkout_project'),DB::raw('case when ((a.warranty_status = 1 or a.warranty_status = 2) and a.calc_warranty_expire_date is not null) then DATE_FORMAT(a.calc_warranty_expire_date, "%Y-%m-%d") else "" end as consolidated_warrenty_end_date'),DB::raw('case when a.warranty_status = 1 then "Expired" when a.warranty_status = 2 then "Not Expired" when a.warranty_status = 3 then "Not Applicable" when a.warranty_status = 4 then "Warranty End Date and OEM Date not matching" when a.warranty_status = 5 then "Purchase Date and OEM Date not matching" when a.warranty_status = 6 then "Invoice Date and OEM Date not matching" else "" end as warranty_status_text'),DB::raw('case when acc_typ.name is not null then acc_typ.name else "" end as acc_type_name'),'a.uuid', DB::raw('case when a.assigned_to is not null then DATE_FORMAT(a.last_checkout, "%Y-%m-%d") else "" end as chkout_date'), 'a.accepted',DB::raw("case when (a.chkout_acceptance_log_id is not null and a.accepted = 'accepted') then date_format(al.created_at, '%Y-%m-%d') else '' end as chkout_acceptance_date"),DB::raw('case when a.assigned_for = 1 then "User" when a.assigned_for = 2 then "Place" end as user_or_place'), DB::raw("CASE WHEN a.status_id = 6 AND a.assigned_for = 1 THEN concat(u.first_name, ' ', u.last_name) WHEN a.status_id = 6 AND a.assigned_for = 2 THEN concat(p.place, ' ', ploc.name, '') WHEN a.status_id != 6 THEN lbl.name ELSE NULL END as user_place_status_text"), DB::raw('concat(u.first_name, " ", u.last_name) as full_name'),'u.username as ckout_username','u.email as ckout_email','u.employee_num as ckout_emp_num','assigned_user_loc.name as assigned_user_loc_name','assigned_user_place.place as assigned_user_place_name','u.doj as assigned_user_doj',DB::raw('case when u.job_type in (1,2) then u.ex_user_company else null end as ex_user_company'), DB::raw('case when a.assigned_for = 2 then concat(p.place, " ", ploc.name, "") else null end as chkout_place'),
                    DB::raw('DATE_FORMAT(a.expected_checkin, "%Y-%m-%d") as expected_checkin_on'),DB::raw('case when a.device_occure_type = 1 then DATE_FORMAT(lease.end_date, "%Y-%m-%d") else "" end as lease_end_date'),'amc_supp.name as amc_supp_name',DB::raw("case when (dayname(a.amc_expire_date) is not null) then date_format(a.amc_expire_date, '%Y-%m-%d') else null end as amc_ed_format, IF((dayname(a.amc_expire_date) is not null) and a.amc_expire_date < CURRENT_TIMESTAMP, 'Expired', '') as 'is_amc_expired'"),'a.manufacturer_warranty_data','at.name as asset_type_name', 'ata.name as allocation_type','u.business_unit','u.delivery_unit','dept.name as user_department','manager.email',DB::raw('case when a.requestable = 1 then "Yes" else "No" end as requestable'),DB::raw('case when a.high_pririty = 1 then "Yes" else "No" end as high_pririty'),DB::raw('case when a.sez_device = 1 then "Yes" else "No" end as sez_device'),DB::raw('DATE_FORMAT(itm.updated_at, "%Y-%m-%d") as ni_last_updated_at'),DB::raw('DATE_FORMAT(a.created_at, "%Y-%m-%d") as installed_at_format'),'itm_azure.azureADDeviceId','itm_azure.lastSyncDateTime','itm_azure.complianceState',DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as updated_at'), 'dep.name as department','itm.HddSize','itm.RamSize','itm.ProcessorName','itm.OSCaption',DB::raw('case when itm.id is not null then "Yes" else "No" end as ni_exits'),DB::raw('case when itm_azure.id is not null then "Yes" else "No" end as azure_exits'),DB::raw('case when a.rdp_status = 1 AND a.node_id is not null then "Yes" else "No" end as rdp_enable'),DB::raw("
                    CASE 
                        WHEN a.device_rfid IS NULL OR a.device_rfid = '' THEN ''
                        ELSE REPLACE(REPLACE(REPLACE(JSON_EXTRACT(a.device_rfid, '$'), '[', ''), ']', ''), '\"', '')
                    END as device_rfids
                "));

            } else {
                $selects = [];
                if(in_array("Company", $device_export_column)) {
                    $selects[] = 'cmp.name as cmp_name';
                }
                if(in_array("Device Name", $device_export_column)) {
                    $selects[] = 'a.name';
                }
                if(in_array("Device Tag", $device_export_column)) {
                    $selects[] = 'a.asset_tag';
                }
                if(in_array("Serial", $device_export_column)) {
                    $selects[] = 'a.serial';
                }
                if(in_array("Manufacture", $device_export_column)) {
                    $selects[] = 'mnu.name as manu_name';
                }
                if(in_array("Model Name", $device_export_column)) {
                    $selects[] = DB::raw('trim(concat_ws(" ", mdl.name, mdl.modelno)) as mdl_full_name');
                }
                if(in_array("Category", $device_export_column)) {
                    $selects[] = 'cat.name as cat_name';
                }
                if(in_array("Status", $device_export_column)) {
                    $selects[] = DB::raw('case when lbl.deployable <> 0 and lbl.archived = 0 and a.assigned_to <> "" and a.assigned_to > 0 then "Deployed" else lbl.name end as lbl_name');
                }
                if(in_array("Location", $device_export_column)) {
                    $selects[] = 'loc.name as loc_name';
                }
                if(in_array("Internal Place",$device_export_column)) {
                    $selects[] = 'pla.place as internal_place';
                }
                if(in_array("Purchase Date", $device_export_column)) {
                    $selects[] = DB::raw('DATE_FORMAT(a.purchase_date, "%Y-%m-%d") as purchase_date_on');
                }
                if(in_array("Purchase Currency", $device_export_column)) {
                $selects[] = 'a.purchase_currency';
                }
                if(in_array("Purchase Cost", $device_export_column)) {
                    $selects[] = DB::raw('FORMAT(a.purchase_cost, 2) as purchase_cost_format');
                }
                if(in_array("Purchase Invoice", $device_export_column)) {
                    $selects[] = 'pur.invoice_no as pur_invoice';
                }
                if(in_array("Ship Date", $device_export_column)) {
                    $selects[] = DB::raw('DATE_FORMAT(a.ship_date, "%Y-%m-%d") as ship_date');
                }
                if(in_array("Warranty Start", $device_export_column)) {
                    $selects[] = DB::raw('DATE_FORMAT(a.warranty_start_date, "%Y-%m-%d") as warranty_start_date');
                }
                if(in_array("Warranty End", $device_export_column)) {
                    $selects[] = DB::raw('DATE_FORMAT(a.warrenty_end_date, "%Y-%m-%d") as warranty_end_date');
                }
                if(in_array("Warranty(Months)", $device_export_column)) {
                    $selects[] = 'a.warranty_months';
                }
                if(in_array("Notes", $device_export_column)) {
                    $selects[] = 'a.notes';
                }

                if(in_array("Order Number", $device_export_column)) {
                    $selects[] = 'a.order_number';
                }
                if(in_array("Asset Owner",$device_export_column)) {
                    $selects[] = DB::raw('concat(own.first_name, " ", own.last_name) as asset_owner');
                }
                if(in_array("IP", $device_export_column)) {
                    $selects[] = 'a.ip';
                }

                if(in_array("MAC", $device_export_column)) {
                    $selects[] = 'a.mac';
                }
                if(in_array("Device From", $device_export_column)) {
                    $selects[] = DB::raw('case when a.device_occure_type = 1 then "Project Device" when a.device_occure_type = 2 then "Rental" when a.device_occure_type = 3 then "Customer Owned" else "Purchase Device" end as device_occure_type_name');
                }
                if (in_array("Stock Location", $device_export_column)) {
                    $selects[] = DB::raw('pldevloc.name AS stock_location');
                }
                if(in_array("Stock Place", $device_export_column)) {
                    $selects[] = DB::raw('case when a.stock_place is not null and pldev.place is not null then concat(pldev.place, " ", pldevloc.name, "") else null end as stock_place');
                }
                if(in_array("Last Network Location", $device_export_column)) {
                    $selects[] = 'locByNi.name as locByNiName';
                }
                if(in_array("Supplier", $device_export_column)) {
                    $selects[] = 's.name as supplier_name';
                }
                if(in_array("Project Name",$device_export_column)) {
                    $selects[] = DB::raw('case when pr.project_no is not null then concat(pr.name, " " ,pr.project_no) else pr.name end as last_checkout_project');
                }
                if(in_array("Warranty Expire Date",$device_export_column)) {
                    $selects[] = DB::raw('case when ((a.warranty_status = 1 or a.warranty_status = 2) and a.calc_warranty_expire_date is not null) then DATE_FORMAT(a.calc_warranty_expire_date, "%Y-%m-%d") else "" end as consolidated_warrenty_end_date');
                }
                if(in_array("Warranty Status", $device_export_column)) {
                    $selects[] = DB::raw('case when a.warranty_status = 1 then "Expired" when a.warranty_status = 2 then "Not Expired" when a.warranty_status = 3 then "Not Applicable" when a.warranty_status = 4 then "Warranty End Date and OEM Date not matching" when a.warranty_status = 5 then "Purchase Date and OEM Date not matching" when a.warranty_status = 6 then "Invoice Date and OEM Date not matching" else "" end as warranty_status_text');
                }
                if(in_array("Account Type", $device_export_column)) {
                    $selects[] = DB::raw('case when acc_typ.name is not null then acc_typ.name else "" end as acc_type_name');
                }
                if(in_array("UUID", $device_export_column)) {
                    $selects[] = 'a.uuid';
                }
                if(in_array("Checkout Date", $device_export_column)) {
                    $selects[] = DB::raw('case when a.assigned_to is not null then DATE_FORMAT(a.last_checkout, "%Y-%m-%d") else "" end as chkout_date');
                }
                if(in_array("Checkout Status", $device_export_column)) {
                    $selects[] = 'a.accepted';
                }
                if(in_array("Checkout Accepted Date", $device_export_column)) {
                    $selects[] = DB::raw("case when (a.chkout_acceptance_log_id is not null and a.accepted = 'accepted') then date_format(al.created_at, '%Y-%m-%d') else '' end as chkout_acceptance_date");
                }
                if(in_array("User/Place", $device_export_column)) {
                    $selects[] = DB::raw('case when a.assigned_for = 1 then "User" when a.assigned_for = 2 then "Place" end as user_or_place');
                }
                if(in_array("Username/Placename/Status", $device_export_column)) {
                    $selects[] = DB::raw("CASE WHEN a.status_id = 6 AND a.assigned_for = 1 THEN concat(u.first_name, ' ', u.last_name) WHEN a.status_id = 6 AND a.assigned_for = 2 THEN concat(p.place, ' ', ploc.name, '') WHEN a.status_id != 6 
                    THEN lbl.name ELSE NULL END as user_place_status_text");
                }
                if(in_array("Assigned User", $device_export_column)) {
                    $selects[] = DB::raw('concat(u.first_name, " ", u.last_name) as full_name');
                }
                if(in_array("Assigned Username", $device_export_column)) {
                    $selects[] = 'u.username as ckout_username';
                }
                if(in_array("Assigned User Email", $device_export_column)) {
                    $selects[] = 'u.email as ckout_email';
                }
                if(in_array("Assigned User Emp. Code", $device_export_column)) {
                    $selects[] = 'u.employee_num as ckout_emp_num';
                }
                if(in_array("Assigned User Location", $device_export_column)) {
                $selects[] = 'assigned_user_loc.name as assigned_user_loc_name';
                }
                if(in_array("Assigned User Place", $device_export_column)) {
                    $selects[] = 'assigned_user_loc.name as assigned_user_place_name';
                }
                if(in_array("Assigned User DOJ", $device_export_column)) {
                    $selects[] = 'u.doj as assigned_user_doj';
                }
                if(in_array("External User Company", $device_export_column)) {
                    $selects[] = DB::raw('case when u.job_type in (1,2) then u.ex_user_company else null end as ex_user_company');
                }
                if(in_array("Assigned Place", $device_export_column)) {
                    $selects[] = DB::raw('case when a.assigned_for = 2 then concat(p.place, " ", ploc.name, "") else null end as chkout_place');
                }
                if(in_array("Expected Checkin Date", $device_export_column)) {
                    $selects[] = DB::raw('DATE_FORMAT(a.expected_checkin, "%Y-%m-%d") as expected_checkin_on');
                }
                if(in_array("Contract End Date", $device_export_column)) {
                    $selects[] = DB::raw('case when a.device_occure_type = 1 then DATE_FORMAT(lease.end_date, "%Y-%m-%d") else "" end as lease_end_date');
                }
                if(in_array("AMC Supplier", $device_export_column)) {
                    $selects[] = 'amc_supp.name as amc_supp_name';
                }
                if(in_array("AMC Expire Date", $device_export_column)) {
                    $selects[] = DB::raw("case when (dayname(a.amc_expire_date) is not null) then date_format(a.amc_expire_date, '%Y-%m-%d') else null end as amc_ed_format");
                }
                if(in_array("AMC Expire Status", $device_export_column)) {
                    $selects[] = DB::raw("IF((dayname(a.amc_expire_date) is not null) and a.amc_expire_date < CURRENT_TIMESTAMP, 'Expired', '') as is_amc_expired");
                }
                if(in_array("OEM Warranty", $device_export_column)) {
                    $selects[] = 'a.manufacturer_warranty_data';
                }
                if(in_array("Device Type", $device_export_column)) {
                    $selects[] = 'at.name as asset_type_name';
                }
                if(in_array("Allocation Type", $device_export_column)) {
                    $selects[] = 'ata.name as allocation_type';
                }
                if(in_array("Business Unit", $device_export_column)) {
                    $selects[] = 'u.business_unit';
                }

                if(in_array("Delivery Unit", $device_export_column)) {
                    $selects[] = 'u.delivery_unit';
                }
                if(in_array("User Department", $device_export_column)) {
                    $selects[] = 'dept.name as user_department';
                }
                if(in_array("Manager",$device_export_column)) {
                $selects[] = 'manager.email';
                }
                if(in_array("Requestable", $device_export_column)) {
                    $selects[] = DB::raw('case when a.requestable = 1 then "Yes" else "No" end as requestable');
                }
                if(in_array("High Priority", $device_export_column)) {
                    $selects[] = DB::raw('case when a.high_pririty = 1 then "Yes" else "No" end as high_pririty');
                }
                if(in_array("Sez Device", $device_export_column)) {
                    $selects[] = DB::raw('case when a.sez_device = 1 then "Yes" else "No" end as sez_device');
                }
                if(in_array("Last NI Scan Date", $device_export_column)) {
                    $selects[] = DB::raw('DATE_FORMAT(itm.updated_at, "%Y-%m-%d") as ni_last_updated_at');
                }
                if(in_array("Installed Date", $device_export_column)) {
                    $selects[] = DB::raw('DATE_FORMAT(a.created_at, "%Y-%m-%d") as installed_at_format');
                }
                if(in_array("Azure AD Device Id", $device_export_column)) {
                    $selects[] = 'itm_azure.azureADDeviceId';
                }
                if(in_array("Last Sync Date Time", $device_export_column)) {
                    $selects[] = 'itm_azure.lastSyncDateTime';
                }
                if(in_array("Compliance State", $device_export_column)) {
                    $selects[] = 'itm_azure.complianceState';
                }
                if(in_array("Updated At", $device_export_column)) {
                    $selects[] = DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as updated_at');
                }
                if(in_array("Department", $device_export_column)) {
                    $selects[] = 'dep.name as department';
                }
                if(in_array("HddSize", $device_export_column)) {
                    $selects[] = 'itm.HddSize';
                }
                if(in_array("RAMSize", $device_export_column)) {
                    $selects[] = 'itm.RamSize';
                }
                if(in_array("Processor", $device_export_column)) {
                    $selects[] = 'itm.ProcessorName';
                }
                if(in_array("OSCaption", $device_export_column)) {
                    $selects[] = 'itm.OSCaption';
                }
                if(in_array("NI Exist", $device_export_column)) {
                    $selects[] = DB::raw('case when itm.id is not null then "Yes" else "No" end as ni_exits');
                }
                if(in_array("Azure Exist", $device_export_column)) {
                    $selects[] = DB::raw('case when itm_azure.id is not null then "Yes" else "No" end as azure_exits');
                }
                if(in_array("RDP Enable", $device_export_column)) {
                    $selects[] = DB::raw('case when a.rdp_status = 1 AND a.node_id is not null then "Yes" else "No" end as rdp_enable');
                }
                if (in_array("Device RFID", $device_export_column)) {
                    $selects[] = DB::raw("
                        CASE 
                            WHEN a.device_rfid IS NULL OR a.device_rfid = '' THEN ''
                            ELSE REPLACE(REPLACE(REPLACE(JSON_EXTRACT(a.device_rfid, '$'), '[', ''), ']', ''), '\"', '')
                        END as device_rfids
                    ");
                }
                $db->select($selects);
            }
            $db->whereNull('a.deleted_at');

            if(in_array("Custom Fields",$device_export_column) ||  $export_column_empty == true ) {
                if(is_array($coll_custom_fields) and count($coll_custom_fields)) {
                    foreach($coll_custom_fields as $coll_custom_field) {
                        $clm_name = substr($coll_custom_field, 2);
                        if (Schema::hasColumn('assets', $clm_name)) {
                            $db->addSelect($coll_custom_field);
                        }
                    }
                }
            }

            if( $export_column_empty == true ) {
                $title_row = ["Company","Device Name","Device Tag","Serial","Manufacture","Model Name","Category","Status","Location","Internal Place","Purchase Date","Purchase Currency","Purchase Cost","Purchase Invoice","Ship Date","Warranty Start","Warranty End","Warranty(Months)","Notes","Order Number","Asset Owner","IP","MAC","Device From","Stock Location","Stock Place","Last Network Location","Supplier","Project Name","Warranty Expire Date","Warranty Status","Account Type","UUID","Checkout Date","Checkout Status","Checkout Accepted Date","User/Place","Username/Placename/Status","Assigned User","Assigned Username","Assigned User Email","Assigned User Emp. Code","Assigned User Location","Assigned User Place","Assigned User DOJ","External User Company","Assigned Place","Expected Checkin Date","Contract End Date","AMC Supplier","AMC Expire Date","AMC Expire Status","OEM Warranty","Device Type","Allocation Type","Business Unit","Delivery Unit","User Department","Manager","Requestable","High Priority","Sez Device","Last NI Scan Date","Installed Date","Azure AD Device Id","Last Sync Date Time","Compliance State","Updated At","Department",'HddSize','RAMSize','Processor','OSCaption','NI Exist','Azure Exist','RDP Enable','Device RFID','Custom Fields'];
            } else {
                $title_row = $device_export_column;
                $title_row = array_filter($title_row, function ($item) {
                    return $item !== 'Custom Fields';
                });
                $title_row = array_values($title_row);
            }
            if(in_array("Custom Fields",$device_export_column) ||  $export_column_empty == true ) {
                if($settings->custom_fieldset_id != "") {
                    $customFieldset = CustomFieldset::find($settings->custom_fieldset_id);
                    if(!empty($customFieldset->fields)) {
                        foreach ($customFieldset->fields as $f) {
                            $col_name = ucwords(str_replace(['_itm_', '_'], ' ', $f->name));
                            array_push($title_row, $col_name);
                        }
                    }
                }
            }
            $results =  $db->get();
            $lineArray = [];
            foreach($results as $key=>$val) {
                $valArray = json_decode(json_encode($val), true);
                if(in_array("Custom Fields", $device_export_column) ||  $export_column_empty == true ) {
                    foreach ($valArray as $k => $v) {
                        if (strpos($k, "_itm_") === 0) {
                            unset($valArray[$k]);
                        }
                    }

                    if ($settings->custom_fieldset_id) {
                        $customFieldset = CustomFieldset::find($settings->custom_fieldset_id);
                        if(!empty($customFieldset)) {
                            $customaCol = json_decode(json_encode($val), true);
                            $fieldData  = CommonHelper::getCustomData($customFieldset->fields, $customaCol);
                            $dataFieldset = [];
                            foreach ($fieldData  as $k => $f) {
                                $dataFieldset[$k] = $f;
                            }
                            $valArray = array_merge($valArray, $dataFieldset); 
                        }
                    }
                }
                $lineArray[] = $valArray;
            }
            $data = json_decode(json_encode($lineArray, true),true);
            $tot = count($data);
            $now = new Carbon(config('app.timezone'));
            if (!empty($data)) {
                $file_name = 'DeviceReport_' . $now->format('dmY_His') . '_' .'.xlsx';
                Excel::store(new DeviceImportStore($data, $title_row), $file_name, 'device_monthly_report');
                Mail::to($emails)->cc($globalAlertEmails)->queue(new DeviceMonthlyNotification($tot, $file_name));
                $this->info("Device Report sent to: " . implode(', ', $emails));
            }
        }
    }
}
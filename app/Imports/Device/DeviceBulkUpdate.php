<?php

namespace App\Imports\Device;

use App\Helpers\Common as CommonHelper;
use App\Models\Actionlog;
use App\Models\AssetType;
use App\Models\BulkActions;
use App\Models\Category;
use App\Models\Company;
use App\Models\CustomField;
use App\Models\Device;
use App\Models\Label;
use App\Models\Location;
use App\Models\Place;
use App\Models\Manufacture;
use App\Models\Department;
use App\Models\Model;
use App\Models\Settings;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Currency;
use App\Models\CustomFieldset;
use App\Models\TransferItem;
use App\Models\NetworkInventory\Basic;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Log;
use Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;


class DeviceBulkUpdate implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
{
    use Importable, SkipsErrors;
    public $data, $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $return = ["msg"=>"Unable to update the given device list", "status"=>"danger"];
        try {  
            $success = 0;
            $fail = 0;
            $fail_msgs = [];
            $data = [];
            $exception_break = false;
            $tot_insert_records = 0;
            $invalied_key = false;
            $currencies = Currency::getCurrencies();

            $required_keys = ['company', 'name', 'asset_tag', 'serial', 'product_number', 'manufacture', 'model', 'category', 'status', 'department', 'location', 'internal_place', 'purchase_date', 'purchase_currency', 'purchase_cost', 'purchase_reference', 'uuid', 'warranty_start_date','warranty_end_date', 'warranty_months', 'amc_expire_date', 'amc_supplier', 'notes', 'order_number', 'asset_owner', 'supplier', 'ip', 'mac', 'device_type', 'device_from', 'stock_place', 'requestable', 'high_priority', 'sez_device'];
            $custom_fields_code = CustomField::getAllFieldsAsCode();
            $all_companies = Company::getAllCompany();
            // $companyId = CommonHelper::getAccessibleCompanyIds();
            // $non_deployed_labels = Label::getAllNonDeployedLabelsExceptSold();
            $not_non_deployed_labels = Label::getAllNotNonDeployedLabelsExceptSold();
            foreach($collection as $key => $row) {
                $invalied_key = false;
                $collected_keys_tot = 0;
                $custom_keys_tot = 0;

                $found_custom_keys = $data_to_insert = $customValue = $data_to_insert_c = $validation_rules_c = [];
                $customFieldsError = $customFields = null;
                $row = CommonHelper::importColumnValidate($row);
                foreach($row as $trim_k => $trim_v) {
                    if(! $trim_k) {
						continue;
					}
                    if(in_array($trim_k, $required_keys)) {
                        $collected_keys_tot++;
                    }
                    elseif(in_array($trim_k, $custom_fields_code)) {
                        $found_custom_keys[] = $trim_k;
                        $custom_keys_tot++;
                    }
                    else {
                        $invalied_key = true;
                        break;
                    }

                    if(in_array($trim_k, ['purchase_date'])) {
                        continue;
                    }
                    if(in_array($trim_k, ['warranty_start_date'])) {
                        continue;
                    }
                    if(in_array($trim_k, ['warranty_end_date'])) {
                        continue;
                    }
					$value = strip_tags(trim($trim_v));
                    $row->{$trim_k} = $value;
					
					if( is_numeric($value) && stripos((string) $value, "e+") > 0 ) {
						$fail++;
						$fail_msgs[] = "The record : '" . $row['asset_tag'] . "' has an invalid '{$trim_k}' value.";
						continue;
					}
                }

                if($invalied_key || count($required_keys) != $collected_keys_tot) {
                    $return["msg"] = trans('content.device_fields.invalid_column');
                    Session::flash('msg', $return);
                    continue;
                }

                if(count($found_custom_keys)) {
                    foreach($found_custom_keys as $fck) {
                        $cv = null;
                        $prefixed_code = CustomField::getPrefixedFieldCode($fck);
                        $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
                        if(!empty($customFieldset)) {
                            foreach ($customFieldset->fields as $f) {
                                if($fck == strtolower(str_replace(' ', '_', $f->name))){
                                    $formatType = CommonHelper::convertFormatToRegex($f->format);
                                    $validation_rules_c[$prefixed_code] = $f->pivot->required == 1 ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                                    $cv = null;
                                    $data_to_insert_c[$prefixed_code] = null;
                                    if($f->element == 'dropdown'){
                                        if($f->option_type == 2 && $f->custom_options != null){
                                            if($row[$fck] != '' || $row[$fck] != null) {
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = null;
                                                $custom_options_array = json_decode($f->custom_options, true);
                                                if(in_array($cv, $custom_options_array)){
                                                    $data_to_insert_c[$prefixed_code] = strval($cv);
                                                }
                                                else {
                                                    $customFieldsError = true;
                                                    $customFields = "The record for serial '" . $row['serial'] . "' has not updated due to invalid " . $fck . ".";
                                                }
                                            }
                                        }else {
                                            if($row[$fck] != '' || $row[$fck] != null) {
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = null;
                                                if($f->preDefinedOptions == 1){
                                                    $getCustomDropDown = Location::where("name", $cv)->where('deleted_at', null)->first();
                                                }elseif($f->preDefinedOptions == 2){
                                                    $getCustomDropDown = User::where("username", $cv)->where('activated', 1)->where('deleted_at', null)->first();
                                                }elseif($f->preDefinedOptions == 18){
                                                    $getCustomDropDown = Department::where('name',$cv)->where('deleted_at', null)->first();
                                                }
                                                if($getCustomDropDown != null){
                                                    $data_to_insert_c[$prefixed_code] = strval($getCustomDropDown->id);
                                                }else {
                                                    $customFieldsError = true;
                                                    $customFields = "The record for serial '" . $row['serial'] . "' has not updated due to invalid " . $fck . ".";
                                                }
    
                                            }
                                        }
                                    }elseif($f->element == 'datetime'){ 
                                        if($row[$fck] != '' || $row[$fck] != null) {
                                            if(is_numeric($row[$fck])){
                                                $row[$fck] = floatval($row[$fck]);  
                                            }
                                            if (gettype($row[$fck]) == "string" && strtotime($row[$fck])) {
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = Carbon::parse($row[$fck])->format('Y-m-d\TH:i');
                                                
                                            }elseif (gettype($row[$fck]) != "string") {
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$fck])->format('Y-m-d\TH:i');
                                            }else{
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = $cv;
                                            }
                                        }
                                    }elseif($f->element == 'time'){
                                        if($row[$fck] != '' || $row[$fck] != null) {
                                            if(is_numeric($row[$fck])){
                                                $row[$fck] = floatval($row[$fck]);  
                                            }
                                            if (gettype($row[$fck]) == "string" && strtotime($row[$fck])) {
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = Carbon::parse($row[$fck])->format('H:i');
                                            }elseif (gettype($row[$fck]) != "string") {
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$fck])->format('H:i');
                                            }else{
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = $cv;
                                            }  
                                        }
                                    }elseif($f->element == 'date'){
                                        if ($row[$fck] != '' || $row[$fck] != null) {
                                            if(is_numeric($row[$fck])){
                                                $row[$fck] = intval($row[$fck]);  
                                            }
                                            
                                            if (gettype($row[$fck]) == "string" && strtotime($row[$fck])) {
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = Carbon::parse($row[$fck])->format('Y-m-d');
                                            }elseif (gettype($row[$fck]) != "string") {
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$fck])->format('Y-m-d');
                                            }else{
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = $cv;
                                            }   
                                        }
                                    }elseif($f->element != 'date' && $f->element != 'datetime' && $f->element != 'time'){
                                        if($row[$fck] != '' || $row[$fck] != null) {
                                            $cv = $row[$fck];
                                            $data_to_insert_c[$prefixed_code] = $cv;
                                        }
                                    }
                                }
                            }
                        }
                        $dataCk['device_'.$key][$fck] = $cv;
                    }
                    $customValue[] = $dataCk;
                }

                if(trim($row['serial']) == "" || $row['serial'] == NULL) {
                    $fail++;
                    $fail_msgs[] = "Serial Number field is empty. Please fill in the data.";
                    $data[] = [
                        $row['company'],
                        $row['name'],
                        $row['asset_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['status'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['purchase_reference'],
                        $row['uuid'],
                        $row['warranty_start_date'],
                        $row['warranty_end_date'],
                        $row['warranty_months'],
                        $row['amc_expire_date'],
                        $row['amc_supplier'],
                        $row['notes'],
                        $row['order_number'],
                        $row['asset_owner'],
                        $row['supplier'],
                        $row['ip'],
                        $row['mac'],
                        $row['device_type'],
                        $row['device_from'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        'Serial Number field is empty. Please fill in the data.'
                    ];
                    continue;
                }

                $validate = Validator::make(['serial' => $row['serial']], [
                    'serial' => 'required'
                ]);

                if($validate->fails()) {
                    $fail++;
                    $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not found.";
                    $data[] = [
                        $row['company'],
                        $row['name'],
                        $row['asset_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['status'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['purchase_reference'],
                        $row['uuid'],
                        $row['warranty_start_date'],
                        $row['warranty_end_date'],
                        $row['warranty_months'],
                        $row['amc_expire_date'],
                        $row['amc_supplier'],
                        $row['notes'],
                        $row['order_number'],
                        $row['asset_owner'],
                        $row['supplier'],
                        $row['ip'],
                        $row['mac'],
                        $row['device_type'],
                        $row['device_from'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        'The record for serial "' . $row['serial'] . '" has not found.'
                    ];
                    continue;
                }

                $device_col = Device::where("serial", "like", $row['serial'])->first();
               
                if (empty($device_col)) {
                    
                    $fail++;
                    $fail_msgs[] = "The record not found for the serial no: '" . $row['serial'] . "'";
                    $data[] = [
                        $row['company'],
                        $row['name'],
                        $row['asset_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['status'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['purchase_reference'],
                        $row['uuid'],
                        $row['warranty_start_date'],
                        $row['warranty_end_date'],
                        $row['warranty_months'],
                        $row['amc_expire_date'],
                        $row['amc_supplier'],
                        $row['notes'],
                        $row['order_number'],
                        $row['asset_owner'],
                        $row['supplier'],
                        $row['ip'],
                        $row['mac'],
                        $row['device_type'],
                        $row['device_from'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        'The record not found for the serial no: "' . $row['serial'] . '" has not found.'
                    ];
                    continue;
                }

                if($row['company']) {
                    $company_id = null;
                    foreach($all_companies as $a_company) {
                        if( strtolower($row['company']) == $a_company->name ) {
                            $company_id = $a_company->id;
                            break;
                        }
                    }
                    if(!empty($company_id)) {
                        $companyPermission = null;
                        // if (!in_array($company_id, $companyId)) {
                        //     $companyPermission = "The record for serial '" . $row['serial'] . "' has not updated due to permission denied to import for other company value";
                        // }
                        if( $device_col->status_id == 6 && $company_id != $device_col->company_id) {
                            $companyPermission = "The record for serial '" . $row['serial'] . "' has not updated due to. company cannot be changed when the asset status is 'Deployed'.";
                        }
                        if ($company_id != $device_col->company_id && empty($row['location'])) {
                            $companyPermission = "The record for serial '" . $row['serial'] . "' has not been updated because when the company changes, location is required.";
                        }
                        if ($company_id != $device_col->company_id && !empty($row['location'])) {
                            foreach ($device_col->getAttributes() as $key => $value) {
                                if (str_starts_with($key, '_itm')) {
                                    $device_col->$key = null;
                                }
                            }
                            $device_col->department_id = null;
                            $device_col->rtd_location_id = null;
                            $device_col->internal_place_id = null;
                            $device_col->stock_place = null;
                            $device_col->lease_id = null;
                            $device_col->asset_owner = null;
                        }
                        if($companyPermission != null ) {
                            $fail++;
                            $fail_msgs[] = $companyPermission;
                            $data[] = [
                                $row['company'],
                                $row['name'],
                                $row['asset_tag'],
                                $row['serial'],
                                $row['product_number'],
                                $row['manufacture'],
                                $row['model'],
                                $row['category'],
                                $row['status'],
                                $row['department'],
                                $row['location'],
                                $row['internal_place'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['purchase_reference'],
                                $row['uuid'],
                                $row['warranty_start_date'],
                                $row['warranty_end_date'],
                                $row['warranty_months'],
                                $row['amc_expire_date'],
                                $row['amc_supplier'],
                                $row['notes'],
                                $row['order_number'],
                                $row['asset_owner'],
                                $row['supplier'],
                                $row['ip'],
                                $row['mac'],
                                $row['device_type'],
                                $row['device_from'],
                                $row['stock_place'],
                                $row['requestable'],
                                $row['high_priority'],
                                $row['sez_device'],
                                'fail',
                                $companyPermission,
                            ];
                            continue;
                        }
                        $device_col->company_id = $company_id;
                    } else {
                        $fail++;
                        $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid Company value.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record for serial '" . $row['serial'] . "' has not updated due to invalid Company value."
                        ];
                        continue;
                    }
                }

                $oldStatus = $device_col->status_id;
                $oldModel = $device_col->model_id;

                $dev_transfer = TransferItem::where('device_id', $device_col->id)->where('transfer_status', 1)->first();
                if(!empty($dev_transfer)) {
                    $fail++;
                    $fail_msgs[] = "'" . $row['serial'] . "'Selected Device is Under Transfer can't be updated.'";
                    $data[] = [
                        $row['company'],
                        $row['name'],
                        $row['asset_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['status'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['purchase_reference'],
                        $row['uuid'],
                        $row['warranty_start_date'],
                        $row['warranty_end_date'],
                        $row['warranty_months'],
                        $row['amc_expire_date'],
                        $row['amc_supplier'],
                        $row['notes'],
                        $row['order_number'],
                        $row['asset_owner'],
                        $row['supplier'],
                        $row['ip'],
                        $row['mac'],
                        $row['device_type'],
                        $row['device_from'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail', 
                        "'" . $row['serial'] . "'Selected Device is Under Transfer can't be updated.'"
                    ];
                    continue;
                }

                $device_asset_tag = Device::where("asset_tag", $row['asset_tag'])->first();
                $customFieldsCacheData = $customFieldsRecordData = [];
                if(Settings::first()->custom_fieldset_id != "") {
                    $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
                    if (!empty($customFieldset)) {
                        $customFieldsCacheData = CommonHelper::interactedData($customFieldset->fields, $device_col);
                    }
                }
                $for_log_comparison = $device_col->dataForCache();
                if ($device_asset_tag != null){
                    if ($device_asset_tag->serial != $device_col->serial) {
                        $fail++;
                        $fail_msgs[] = 'The asset tag has already been taken: this serial no '. $row['serial'].'';
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            'The asset tag has already been taken: this serial no '. $row['serial'].''
                        ];
                        continue;
                    }
                }
                $device = $device_col;
                $amc_expire_date = $row['amc_expire_date'];
                if($row['amc_expire_date']) {
                    try {
                        if(gettype($row['amc_expire_date']) == "string" && strtotime($row['amc_expire_date'])) {
                            $device->amc_expire_date = Carbon::parse($row['amc_expire_date'])->format('Y-m-d');
                            $row['amc_expire_date'] = $device->amc_expire_date;
                        } elseif(gettype($row['amc_expire_date']) != "string") {
                            $device->amc_expire_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['amc_expire_date'])->format('Y-m-d');
                            $row['amc_expire_date'] = $device->amc_expire_date;
                        } else {
                            $fail++;
                            $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid AMC Expire date format. Date can be empty or valid Excel Date Format.";
                            $data[] = [
                                $row['company'],
                                $row['name'],
                                $row['asset_tag'],
                                $row['serial'],
                                $row['product_number'],
                                $row['manufacture'],
                                $row['model'],
                                $row['category'],
                                $row['status'],
                                $row['department'],
                                $row['location'],
                                $row['internal_place'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['purchase_reference'],
                                $row['uuid'],
                                $row['warranty_start_date'],
                                $row['warranty_end_date'],
                                $row['warranty_months'],
                                $row['amc_expire_date'],
                                $row['amc_supplier'],
                                $row['notes'],
                                $row['order_number'],
                                $row['asset_owner'],
                                $row['supplier'],
                                $row['ip'],
                                $row['mac'],
                                $row['device_type'],
                                $row['device_from'],
                                $row['stock_place'],
                                $row['requestable'],
                                $row['high_priority'],
                                $row['sez_device'],
                                'fail',
                                "The record : '" . $row['serial'] . "' has not updated due to invalid AMC Expire date format. Date can be empty or valid Excel Date Format."
                            ];
                            continue;
                        }
                        if($device->amc_expire_date) {
                            $amcDate = Carbon::parse($device->amc_expire_date);
                            $purchaseDate = null;
                            if($row['purchase_date']) {
                                if(gettype($row['purchase_date']) == "string" && strtotime($row['purchase_date'])) {
                                    $purchaseDate = Carbon::parse($row['purchase_date']);
                                } elseif(gettype($row['purchase_date']) != "string") {
                                    $purchaseDate = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['purchase_date'])->format('Y-m-d'));
                                }
                            } elseif($device_col->purchase_date) {
                                $purchaseDate = Carbon::parse($device_col->purchase_date);
                            }
                            $warrantyDate = null;
                            if($row['warranty_start_date']) {
                                if(gettype($row['warranty_start_date']) == "string" && strtotime($row['warranty_start_date'])) {
                                    $warrantyDate = Carbon::parse($row['warranty_start_date']);
                                } elseif(gettype($row['warranty_start_date']) != "string") {
                                    $warrantyDate = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['warranty_start_date'])->format('Y-m-d'));
                                }
                            } elseif($device_col->warranty_start_date) {
                                $warrantyDate = Carbon::parse($device_col->warranty_start_date);
                            }

                            if (($purchaseDate && $amcDate <= $purchaseDate) || ($warrantyDate && $amcDate <= $warrantyDate)) {
                                $fail++;
                                $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated because AMC Expire Date must be greater than Purchase Date and Warranty Start Date.";
                                $data[] = [
                                    $row['company'],
                                    $row['name'],
                                    $row['asset_tag'],
                                    $row['serial'],
                                    $row['product_number'],
                                    $row['manufacture'],
                                    $row['model'],
                                    $row['category'],
                                    $row['status'],
                                    $row['department'],
                                    $row['location'],
                                    $row['internal_place'],
                                    $row['purchase_date'],
                                    $row['purchase_currency'],
                                    $row['purchase_cost'],
                                    $row['purchase_reference'],
                                    $row['uuid'],
                                    $row['warranty_start_date'],
                                    $row['warranty_end_date'],
                                    $row['warranty_months'],
                                    $row['amc_expire_date'],
                                    $row['amc_supplier'],
                                    $row['notes'],
                                    $row['order_number'],
                                    $row['asset_owner'],
                                    $row['supplier'],
                                    $row['ip'],
                                    $row['mac'],
                                    $row['device_type'],
                                    $row['device_from'],
                                    $row['stock_place'],
                                    $row['requestable'],
                                    $row['high_priority'],
                                    $row['sez_device'],
                                    'fail',
                                    "The record : '" . $row['serial'] . "' has not updated because AMC Expire Date must be greater than Purchase Date and Warranty Start Date."
                                ];
                                continue;
                            }
                        }
                    }
                    catch(\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid AMC Expire date format. Date can be empty or valid Excel Date Format.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record : '" . $row['serial'] . "' has not updated due to invalid AMC Expire date format. Date can be empty or valid Excel Date Format."
                        ];
                        continue;
                    }
                }
                $purchase_date = $row['purchase_date'];
                if($row['purchase_date']) {
                    try {
                        if(gettype($row['purchase_date']) == "string" && strtotime($row['purchase_date'])) {
                            $device->purchase_date = Carbon::parse($row['purchase_date'])->format('Y-m-d');
                            $row['purchase_date'] = $device->purchase_date;
                        } elseif(gettype($row['purchase_date']) != "string") {
                            $device->purchase_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['purchase_date'])->format('Y-m-d');
                            $row['purchase_date'] = $device->purchase_date;
                        } else {
                            $fail++;
                            $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid purchase date format. Date can be empty or valid Excel Date Format.";
                            $data[] = [
                                $row['company'],
                                $row['name'],
                                $row['asset_tag'],
                                $row['serial'],
                                $row['product_number'],
                                $row['manufacture'],
                                $row['model'],
                                $row['category'],
                                $row['status'],
                                $row['department'],
                                $row['location'],
                                $row['internal_place'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['purchase_reference'],
                                $row['uuid'],
                                $row['warranty_start_date'],
                                $row['warranty_end_date'],
                                $row['warranty_months'],
                                $row['amc_expire_date'],
                                $row['amc_supplier'],
                                $row['notes'],
                                $row['order_number'],
                                $row['asset_owner'],
                                $row['supplier'],
                                $row['ip'],
                                $row['mac'],
                                $row['device_type'],
                                $row['device_from'],
                                $row['stock_place'],
                                $row['requestable'],
                                $row['high_priority'],
                                $row['sez_device'],
                                'fail',
                                "The record : '" . $row['serial'] . "' has not updated due to invalid purchase date format. Date can be empty or valid Excel Date Format."
                            ];
                            continue;
                        }
                    }
                    catch(\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid purchase date format. Date can be empty or valid Excel Date Format.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record : '" . $row['serial'] . "' has not updated due to invalid purchase date format. Date can be empty or valid Excel Date Format."
                        ];
                        continue;
                    }
                }
                $warranty_start_date = $row['warranty_start_date'];
                if($row['warranty_start_date']) {
                    try {
                        if(gettype($row['warranty_start_date']) == "string" && strtotime($row['warranty_start_date'])) {
                            $device->warranty_start_date = Carbon::parse($row['warranty_start_date'])->format('Y-m-d');
                            $row['warranty_start_date'] = $device->warranty_start_date;
                        } elseif(gettype($row['warranty_start_date']) != "string") {
                            $device->warranty_start_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['warranty_start_date'])->format('Y-m-d');
                            $row['warranty_start_date'] = $device->warranty_start_date;
                        } else {
                            $fail++;
                            $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid warranty start date format. Date can be empty or valid Excel Date Format.";
                            $data[] = [
                                $row['company'],
                                $row['name'],
                                $row['asset_tag'],
                                $row['serial'],
                                $row['product_number'],
                                $row['manufacture'],
                                $row['model'],
                                $row['category'],
                                $row['status'],
                                $row['department'],
                                $row['location'],
                                $row['internal_place'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['purchase_reference'],
                                $row['uuid'],
                                $row['warranty_start_date'],
                                $row['warranty_end_date'],
                                $row['warranty_months'],
                                $row['amc_expire_date'],
                                $row['amc_supplier'],
                                $row['notes'],
                                $row['order_number'],
                                $row['asset_owner'],
                                $row['supplier'],
                                $row['ip'],
                                $row['mac'],
                                $row['device_type'],
                                $row['device_from'],
                                $row['stock_place'],
                                $row['requestable'],
                                $row['high_priority'],
                                $row['sez_device'],
                                'fail',
                                "The record : '" . $row['serial'] . "' has not updated due to invalid warranty start date format. Date can be empty or valid Excel Date Format."
                            ];
                            continue;
                        }
                    }
                    catch(\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid warranty start date format. Date can be empty or valid Excel Date Format.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record : '" . $row['serial'] . "' has not updated due to invalid warranty date start format. Date can be empty or valid Excel Date Format."
                        ];
                        continue;
                    }
                }
                $warranty_end_date = $row['warranty_end_date'];
                if($row['warranty_end_date']) {
                    try {
                        if(gettype($row['warranty_end_date']) == "string" && strtotime($row['warranty_end_date'])) {
                            $device->warrenty_end_date = Carbon::parse($row['warranty_end_date'])->format('Y-m-d');
                            $row['warranty_end_date'] = $device->warrenty_end_date;
                        } elseif(gettype($row['warranty_end_date']) != "string") {
                            $device->warrenty_end_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['warranty_end_date'])->format('Y-m-d');
                            $row['warranty_end_date'] = $device->warrenty_end_date;
                        } else {
                            $fail++;
                            $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid warranty end date format. Date can be empty or valid Excel Date Format.";
                            $data[] = [
                                $row['company'],
                                $row['name'],
                                $row['asset_tag'],
                                $row['serial'],
                                $row['product_number'],
                                $row['manufacture'],
                                $row['model'],
                                $row['category'],
                                $row['status'],
                                $row['department'],
                                $row['location'],
                                $row['internal_place'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['purchase_reference'],
                                $row['uuid'],
                                $row['warranty_start_date'],
                                $row['warranty_end_date'],
                                $row['warranty_months'],
                                $row['amc_expire_date'],
                                $row['amc_supplier'],
                                $row['notes'],
                                $row['order_number'],
                                $row['asset_owner'],
                                $row['supplier'],
                                $row['ip'],
                                $row['mac'],
                                $row['device_type'],
                                $row['device_from'],
                                $row['stock_place'],
                                $row['requestable'],
                                $row['high_priority'],
                                $row['sez_device'],
                                'fail',
                                "The record : '" . $row['serial'] . "' has not updated due to invalid warranty end date format. Date can be empty or valid Excel Date Format."
                            ];
                            continue;
                        }
                    }
                    catch(\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid warranty end date format. Date can be empty or valid Excel Date Format.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record : '" . $row['serial'] . "' has not updated due to invalid warranty end date format. Date can be empty or valid Excel Date Format."
                        ];
                        continue;
                    }
                }

                if (isset($row['purchase_cost']) && $row['purchase_cost'] !== '') {
                    $value = str_replace(',', '', trim($row['purchase_cost']));
                    if (preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
                        $row['purchase_cost'] = (float) $value;
                    } else {
                        $fail++;
                        $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid purchase cost format.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record : '" . $row['serial'] . "' has not updated due to invalid purchase cost format."
                        ];
                        continue;
                    }
                }
                /* custom fields data capture */
                // if(count($found_custom_keys)) {
                //     foreach($found_custom_keys as $fck) {
                //         $cv = null;
                //         $prefixed_code = CustomField::getPrefixedFieldCode($fck);
                //         $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
                //         if(!empty($customFieldset)) {
                //             foreach ($customFieldset->fields as $f) {
                //                 if($fck == strtolower(str_replace(' ', '_', $f->name))){
                //                     $formatType = CommonHelper::convertFormatToRegex($f->format);
                //                     $validation_rules_c[$prefixed_code] = $f->pivot->required == 1 ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                //                     $cv = null;
                //                     $data_to_insert_c[$prefixed_code] = null;
                //                     if($f->element == 'dropdown'){
                //                         if($f->option_type == 2 && $f->custom_options != null){
                //                             if($row[$fck] != '' || $row[$fck] != null) {
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = null;
                //                                 $custom_options_array = json_decode($f->custom_options, true);
                //                                 if(in_array($cv, $custom_options_array)){
                //                                     $data_to_insert_c[$prefixed_code] = strval($cv);
                //                                 }
                //                                 else {
                //                                     $customFieldsError = true;
                //                                     $customFields = "The record for serial '" . $row['serial'] . "' has not updated due to invalid " . $fck . ".";
                //                                 }
                //                             }
                //                         }else {
                //                             if($row[$fck] != '' || $row[$fck] != null) {
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = null;
                //                                 if($f->preDefinedOptions == 1){
                //                                     $getCustomDropDown = Location::where("name", $cv)->where('deleted_at', null)->first();
                //                                 }elseif($f->preDefinedOptions == 2){
                //                                     $getCustomDropDown = User::where("username", $cv)->where('activated', 1)->where('deleted_at', null)->first();
                //                                 }elseif($f->preDefinedOptions == 18){
                //                                     $getCustomDropDown = Department::where('name',$cv)->where('deleted_at', null)->first();
                //                                 }
                //                                 if($getCustomDropDown != null){
                //                                     $data_to_insert_c[$prefixed_code] = strval($getCustomDropDown->id);
                //                                 }else {
                //                                     $customFieldsError = true;
                //                                     $customFields = "The record for serial '" . $row['serial'] . "' has not updated due to invalid " . $fck . ".";
                //                                 }
    
                //                             }
                //                             // if($f->preDefinedOptions == 1){
                //                             //     if($row[$fck] != '' || $row[$fck] != null) {
                //                             //         $cv = $row[$fck];
                //                             //         $getLocation = Location::where("name", $cv)->where('deleted_at', null)->first();
                //                             //         $data_to_insert_c[$prefixed_code] = null;
                //                             //         if($getLocation != null){
                //                             //             $data_to_insert_c[$prefixed_code] = strval($getLocation->id);
                //                             //         }
                //                             //         else {
                //                             //             $customFieldsError = true;
                //                             //             $customFields = $text_msg_until_completed_rows . "Invalid ".$fck." is found at Excel Row" . ($key + 1);
                //                             //         }
    
                //                             //     }
                //                             // }elseif($f->preDefinedOptions == 2){
                //                             //     if($row[$fck] != '' || $row[$fck] != null) {
                //                             //         $cv = $row[$fck];
                //                             //         $getUser = User::where("username", $cv)->where('activated', 1)->where('deleted_at', null)->first();
                //                             //         $data_to_insert_c[$prefixed_code] = null;
                //                             //         if($getUser != null){
                //                             //             $data_to_insert_c[$prefixed_code] = strval($getUser->id);
                //                             //         }
                //                             //         else {
                //                             //             $customFieldsError = true;
                //                             //             $customFields = $text_msg_until_completed_rows . "Invalid ".$fck." is found at Excel Row" . ($key + 1);
                //                             //         }
    
                //                             //     }
                //                             // }     
                //                         }
                //                     }elseif($f->element == 'datetime'){ 
                //                         if($row[$fck] != '' || $row[$fck] != null) {
                //                             if(is_numeric($row[$fck])){
                //                                 $row[$fck] = floatval($row[$fck]);  
                //                             }
                //                             if (gettype($row[$fck]) == "string" && strtotime($row[$fck])) {
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = Carbon::parse($row[$fck])->format('Y-m-d\TH:i');
                                                
                //                             }elseif (gettype($row[$fck]) != "string") {
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$fck])->format('Y-m-d\TH:i');
                //                             }else{
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = $cv;
                //                             }
                //                         }
                //                     }elseif($f->element == 'time'){
                //                         if($row[$fck] != '' || $row[$fck] != null) {
                //                             if(is_numeric($row[$fck])){
                //                                 $row[$fck] = floatval($row[$fck]);  
                //                             }
                //                             if (gettype($row[$fck]) == "string" && strtotime($row[$fck])) {
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = Carbon::parse($row[$fck])->format('H:i');
                //                             }elseif (gettype($row[$fck]) != "string") {
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$fck])->format('H:i');
                //                             }else{
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = $cv;
                //                             }  
                //                         }
                //                     }elseif($f->element == 'date'){
                //                         if ($row[$fck] != '' || $row[$fck] != null) {
                //                             if(is_numeric($row[$fck])){
                //                                 $row[$fck] = intval($row[$fck]);  
                //                             }
                                            
                //                             if (gettype($row[$fck]) == "string" && strtotime($row[$fck])) {
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = Carbon::parse($row[$fck])->format('Y-m-d');
                //                             }elseif (gettype($row[$fck]) != "string") {
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$fck])->format('Y-m-d');
                //                             }else{
                //                                 $cv = $row[$fck];
                //                                 $data_to_insert_c[$prefixed_code] = $cv;
                //                             }   
                //                         }
                //                     }elseif($f->element != 'date' && $f->element != 'datetime' && $f->element != 'time'){
                //                         if($row[$fck] != '' || $row[$fck] != null) {
                //                             $cv = $row[$fck];
                //                             $data_to_insert_c[$prefixed_code] = $cv;
                //                         }
                //                     }
                //                 }
                //             }
                //         }
                //         $dataCk['device_'.$key][$fck] = $cv;
                //         Log::info($row['serial']);
                //     }
                //     $customValue[] = $dataCk;
                // }

                if( $customFieldsError != null && $customFields != null ){
                    if($customFieldsError == true) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $customFields;
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $customFields,
                        ];
                        continue;
                    }
                    $customFieldsError = $customFields = null;  
                }
                $validation_rules = [
                    'company_id' => 'nullable|integer|min:1',
                    'name' => 'nullable|clean_text_only|string|max:100',
                    'asset_tag' => 'nullable|clean_text_only|string|max:100',
                    'manufacture' => 'nullable|clean_text_only|string|min:1|max:255',
                    'category' => 'nullable|clean_text_only|string|min:1|max:255',
                    'model' => 'nullable|clean_text_only|string|min:1|max:255',
                    'status_id' => 'nullable|integer|min:1',
                    'rtd_location_id' => 'nullable|integer|min:1',
                    'internal_place_id' => 'nullable|integer|min:1|exists:places,id',
                    'uuid' => 'nullable|clean_text_only|string|max:100',
                    'serial' => 'nullable|clean_text_only|string|max:255',
                    'product_number' => 'nullable|clean_text_only_with_hash|string|max:255',
                    'purchase_date' => 'nullable|date_format:Y-m-d',
                    'warranty_start_date' => 'nullable|date_format:Y-m-d',
                    'warranty_end_date' => 'nullable|date_format:Y-m-d',
                    // 'amc_expire_date' => 'nullable|date_format:Y-m-d',
                    'asset_owner' => 'nullable|clean_text_only|string',
                    'supplier_id' => 'nullable|clean_text_only|integer',
                    'order_number' => 'nullable|clean_text_only|string|max:100',
                    'purchase_cost' => 'nullable|clean_text_only',
                    'warranty_months' => 'nullable|integer|min:0',
                    'notes' => 'nullable|clean_text_only|string|max:2000',
                    //'requestable' => 'sometimes|nullable|integer|min:0|max:1',
                    "ip" => "nullable|clean_text_only|string|max:50",
                    "mac" => "nullable|clean_text_only|string|max:60",
                    "device_type" => "nullable|clean_text_only|string",
                    "device_occure_type" => "nullable|integer|min:0|max:2",
                    'stock_place'       => 'nullable|clean_text_only|string',
                ];
                $data_to_insert = (array) $row;
                if(!empty($validation_rules_c)){
                    $validation_rules = array_merge($validation_rules,$validation_rules_c);
                }
                if(!empty($data_to_insert_c)){
                    $data_to_insert = array_merge($data_to_insert,$data_to_insert_c);
                }
                $validate = Validator::make($data_to_insert, $validation_rules);
                if($validate->fails()) {
                    $errors = $validate->errors(); // Retrieve the error messages
                    $errorDetails = [];
                    foreach ($errors->messages() as $field => $messages) {
                        $errorDetails[] = "Field: $field, Error: " . implode(', ', $messages);
                    }
                    $fail++;
                    // $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid values detected.";
                    $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid values. Details: " . implode('; ', $errorDetails);
                    $data[] = [
                        $row['company'],
                        $row['name'],
                        $row['asset_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['status'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['purchase_reference'],
                        $row['uuid'],
                        $row['warranty_start_date'],
                        $row['warranty_end_date'],
                        $row['warranty_months'],
                        $row['amc_expire_date'],
                        $row['amc_supplier'],
                        $row['notes'],
                        $row['order_number'],
                        $row['asset_owner'],
                        $row['supplier'],
                        $row['ip'],
                        $row['mac'],
                        $row['device_type'],
                        $row['device_from'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        "The record : '" . $row['serial'] . "' has not updated due to invalid values. Details: " . implode('; ', $errorDetails),
                    ];  
                    Log::info("Device bulk update validation errors: " . json_encode($errors->messages()));
                    continue;
                }
                // $row['amc_expire_date'] = $amc_expire_date;
                $row['purchase_date'] = $purchase_date;
                $row['warranty_start_date'] = $warranty_start_date;
                $row['warranty_end_date'] = $warranty_end_date;

                if($row['name']) {
                    $device->name = strtolower($row['name']) != "null" ? $row['name'] : null;
                }

                if($row['asset_tag']) {
                    $device->asset_tag = strtolower($row['asset_tag']) != "null" ? $row['asset_tag'] : $device->asset_tag;
                }

                if($row['serial'] != "null") {
                    $device->serial = strtolower($row['serial']) ? $device->serial : $device->serial;
                }

                if($row['product_number']) {
                    $device->product_number = strtolower($row['product_number']) != "null" ? $row['product_number'] : $device->product_number;
                }

                if($row['serial'] == "null") {
                    $fail++;
                    $fail_msgs[] = 'Please provide the serial number or leave it blank';
                    $data[] = [
                        $row['company'],
                        $row['name'],
                        $row['asset_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['status'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['purchase_reference'],
                        $row['uuid'],
                        $row['warranty_start_date'],
                        $row['warranty_end_date'],
                        $row['warranty_months'],
                        $row['amc_expire_date'],
                        $row['amc_supplier'],
                        $row['notes'],
                        $row['order_number'],
                        $row['asset_owner'],
                        $row['supplier'],
                        $row['ip'],
                        $row['mac'],
                        $row['device_type'],
                        $row['device_from'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        "Please provide the serial number"
                    ];
                    continue;
                }

                //check for manufacture and if not create new
                if ($row['manufacture']) {
                    $thisManufactureId  = optional( Manufacture::where('name', $row['manufacture'])->first() )->id;
                    if(empty($thisManufactureId))
                    {
                        $newManufac = new Manufacture;
                        $newManufac->name = $row['manufacture'];
                        $newManufac->user_id = Auth::user()->id;
                        $newManufac->save();
                        $thisManufactureId = $newManufac->id;
                    }
                }

                //check for category and if not create new
                if ($row['category']) {
                    // $thisCategoryId = optional( Category::where('name', $row['category'])->first())->id;
                    $thisCategoryId = optional( Category::where('name', $row['category'] )->where('category_type','asset')->first() )->id;
                    if(empty($thisCategoryId))
                    {
                        $newCategory = new Category;
                        $newCategory->name = $row['category'];
                        $newCategory->user_id = Auth::user()->id;
                        $newCategory->save();
                        $thisCategoryId = $newCategory->id;
                    }
                }

                //check for model and if not create new
                if (!empty($row['model'])) {
                    $model_id = null;
                    $getModel = Model::where('id', $device_col->model_id)->first();
                    if ($row['category'] && $row['manufacture']) {
                        $model_id = optional(Model::where([
                            [ 'name', 'like', $row['model'] ],
                            [ 'manufacturer_id','=', $thisManufactureId ],
                            [ 'category_id','=',  $thisCategoryId ]
                        ])->first())->id;
                        if (empty($model_id)) {
                            // insert model
                            $newModel = new Model;
                            $newModel->name = $row['model'];
                            $newModel->manufacturer_id = $thisManufactureId;
                            $newModel->category_id = $thisCategoryId;
                            $newModel->user_id = Auth::user()->id;
                            $newModel->save();
                            $device->model_id = $newModel->id;
                        } else {
                            $device->model_id = $model_id;
                        }
                    } else if ($row['manufacture']) {
                        $model_id = optional(Model::where([
                            [ 'name', 'like', $row['model'] ],
                            [ 'manufacturer_id','=', $thisManufactureId ],
                            [ 'category_id','=', $getModel->category_id ]
                        ])->first())->id;
                        if (empty($model_id)) {
                            // insert model
                            $newModel = new Model;
                            $newModel->name = $row['model'];
                            $newModel->manufacturer_id = $thisManufactureId;
                            $newModel->category_id = $getModel->category_id;
                            $newModel->user_id = Auth::user()->id;
                            $newModel->save();
                            $device->model_id = $newModel->id;
                        } else {
                            $device->model_id = $model_id;
                        }
                    } else if ($row['category']) {
                        $model_id = optional(Model::where([
                            [ 'name', 'like', $row['model'] ],
                            [ 'manufacturer_id','=', $getModel->manufacturer_id ],
                            [ 'category_id','=', $thisCategoryId ]
                        ])->first())->id;
                        if (empty($model_id)) {
                            // insert model
                            $newModel = new Model;
                            $newModel->name = $row['model'];
                            $newModel->manufacturer_id = $getModel->manufacturer_id;
                            $newModel->category_id = $thisCategoryId;
                            $newModel->user_id = Auth::user()->id;
                            $newModel->save();
                            $device->model_id = $newModel->id;
                        } else {
                            $device->model_id = $model_id;
                        }
                    } else {
                        $model = Model::where('name', $row['model'])->first();
                        if ($model) {
                            $device->model_id = $model->id;
                        } else {
                              // insert model
                              $newModel = new Model;
                              $newModel->name = $row['model'];
                              $newModel->manufacturer_id = $getModel->manufacturer_id;
                              $newModel->category_id = $getModel->category_id;
                              $newModel->user_id = Auth::user()->id;
                              $newModel->save();
                              $device->model_id = $newModel->id;
                        }
                    }
                } else {
                    $getModel = Model::where('id', $device_col->model_id)->first();
                    if ($row['category'] && $row['manufacture']) {
                        $model_id = optional(Model::where([
                            [ 'name', '=', $getModel->name ],
                            [ 'manufacturer_id', '=', $thisManufactureId ],
                            [ 'category_id', '=',  $thisCategoryId ]
                        ])->first())->id;

                        if (empty($model_id)) {
                            // insert model
                            $newModel = new Model;
                            $newModel->name = $getModel->name;
                            $newModel->manufacturer_id = $thisManufactureId;
                            $newModel->category_id = $thisCategoryId;
                            $newModel->user_id = Auth::user()->id;
                            $newModel->save();
                            $device->model_id = $newModel->id;
                        } else {
                            $device->model_id = $model_id;
                        }
                    } else if($row['category']) {
                        $model_id = optional(Model::where([
                            [ 'name', '=', $getModel->name ],
                            [ 'category_id', '=', $thisCategoryId ]
                        ])->first())->id;
                        if (empty($model_id)) {
                            // insert model
                            $newModel = new Model;
                            $newModel->name = $getModel->name;
                            $newModel->manufacturer_id = $getModel->manufacturer_id;
                            $newModel->category_id = $thisCategoryId;
                            $newModel->user_id = Auth::user()->id;
                            $newModel->save();
                            $device->model_id = $newModel->id;
                        } else {
                            $device->model_id = $model_id;
                        }
                    } else if($row['manufacture']) {
                        $model_id = optional(Model::where([
                            [ 'name', '=', $getModel->name ],
                            [ 'manufacturer_id', '=', $thisManufactureId ]
                        ])->first())->id;
                        if (empty($model_id)) {
                            // insert model
                            $newModel = new Model;
                            $newModel->name = $getModel->name;
                            $newModel->manufacturer_id = $thisManufactureId;
                            $newModel->category_id = $getModel->category_id;
                            $newModel->user_id = Auth::user()->id;
                            $newModel->save();
                            $device->model_id = $newModel->id;
                        } else {
                            $device->model_id = $model_id;
                        }
                    }
                }

                if ($row['status']) {
                    $status_id = null;
                    foreach($not_non_deployed_labels as $dep_labels) {
                        if( strtolower($row['status']) == strtolower($dep_labels->name) ) {
                            $status_id = $dep_labels->id;
                            break;
                        }
                    }
                    if(!empty($status_id)) {
                        if($device_col->status_id != 6 && $status_id != 6){
                            $device->status_id = $status_id;
                        } else if($device_col->status_id != 6 && $status_id == 6 || $device_col->status_id == 6 && $status_id != 6) { 
                            $fail++;
                            $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated if the deployed status changes to another status or another status changes to the deployed status.";
                            $data[] = [
                                $row['company'],
                                $row['name'],
                                $row['asset_tag'],
                                $row['serial'],
                                $row['product_number'],
                                $row['manufacture'],
                                $row['model'],
                                $row['category'],
                                $row['status'],
                                $row['department'],
                                $row['location'],
                                $row['internal_place'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['purchase_reference'],
                                $row['uuid'],
                                $row['warranty_start_date'],
                                $row['warranty_end_date'],
                                $row['warranty_months'],
                                $row['amc_expire_date'],
                                $row['amc_supplier'],
                                $row['notes'],
                                $row['order_number'],
                                $row['asset_owner'],
                                $row['supplier'],
                                $row['ip'],
                                $row['mac'],
                                $row['device_type'],
                                $row['device_from'],
                                $row['stock_place'],
                                $row['requestable'],
                                $row['high_priority'],
                                $row['sez_device'],
                                'fail',
                                "The record for serial '" . $row['serial'] . "' has not updated if the deployed status changes to another status or another status changes to the deployed status."
                            ];
                            continue;
                        }
                    } else {
                        $fail++;
                        $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid Status Name";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record for serial '" . $row['serial'] . "' has not updated due to invalid Status Name"
                        ];
                        continue;
                    }
                }

                if($row['department']) {
                    $department = Department::where('name', $row['department'])->where('company_id', $device->company_id)->first();
                    if(!empty($department)) {
                        $device->department_id = $department->id;
                    } else {
                        $fail++;
                        $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid Department value or it does not belong to this company .";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record for serial '" . $row['serial'] . "' has not updated due to invalid Department value or it does not belong to this company."
                        ];
                        continue;
                    }
                }
                if($row['uuid']) {
                    $device->uuid = strtolower($row['uuid']) != "null" ? $row['uuid'] : null;
                }
                
                if($row['purchase_currency']) {
                    $upper_currency = strtoupper($row['purchase_currency']);
                    if($upper_currency == "NULL") {
                        $device->purchase_currency = null;
                    }
                    elseif(! array_key_exists($upper_currency, $currencies)) {
                        $fail++;
                        $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid purchase currency code.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record : '" . $row['serial'] . "' has not updated due to invalid purchase currency code."
                        ];
                        continue;
                    }
                    $device->purchase_currency = $upper_currency;
                }

                if($row['purchase_cost']) {
                    $device->purchase_cost = strtolower($row['purchase_cost']) != "null" ? ((float) $row['purchase_cost']) : 0;
                }
                if($row['order_number']) {
                    $device->order_number = strtolower($row['order_number']) != "null" ? $row['order_number'] : null;
                }

                /* stock place */
                if ($row['location']) {
                    $locationObj = Location::where('name', 'like', $row['location'])->where('company_id', $device->company_id)->first();
                    if (!empty($locationObj)) {
                        $device->rtd_location_id = $locationObj->id;
                        if ($row['stock_place']) {
                            if ($row['stock_place'] == "null") {
                                $device->stock_place = null;
                            } else {
                                $place = Place::where('place', $row['stock_place'])->where('location_id',$device->rtd_location_id)->where('company_id', $device->company_id)->first();
                                if (!empty($place) && $device->rtd_location_id == $place->location_id) {
                                    $device->stock_place = $place->id;
                                } else {
                                    $fail++;
                                    $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid stock place value or it does not belong to this company.";
                                    $data[] = [
                                        $row['company'],
                                        $row['name'],
                                        $row['asset_tag'],
                                        $row['serial'],
                                        $row['product_number'],
                                        $row['manufacture'],
                                        $row['model'],
                                        $row['category'],
                                        $row['status'],
                                        $row['department'],
                                        $row['location'],
                                        $row['internal_place'],
                                        $row['purchase_date'],
                                        $row['purchase_currency'],
                                        $row['purchase_cost'],
                                        $row['purchase_reference'],
                                        $row['uuid'],
                                        $row['warranty_start_date'],
                                        $row['warranty_end_date'],
                                        $row['warranty_months'],
                                        $row['amc_expire_date'],
                                        $row['amc_supplier'],
                                        $row['notes'],
                                        $row['order_number'],
                                        $row['asset_owner'],
                                        $row['supplier'],
                                        $row['ip'],
                                        $row['mac'],
                                        $row['device_type'],
                                        $row['device_from'],
                                        $row['stock_place'],
                                        $row['requestable'],
                                        $row['high_priority'],
                                        $row['sez_device'],
                                        'fail',
                                        "The record for serial '" . $row['serial'] . "' has not updated due to invalid stock place value or it does not belong to this company."
                                    ];
                                    continue;
                                }
                            }
                        }
                    } else {
                        $fail++;
                        $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid location name or it does not belong to this company.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record for serial: '" . $row['serial'] . "' has not updated due to invalid location name or it does not belong to this company."
                        ];
                        continue;
                    }
                } else {
                    if ($row['stock_place']) {
                        if ($row['stock_place'] == "null") {
                            $device->stock_place = null;
                        } else {
                            $place = Place::where('place', $row['stock_place'])->where('location_id',$device_col->rtd_location_id)->where('company_id', $device->company_id)->first();
                            if (!empty($place) && $device->rtd_location_id == $place->location_id) {
                                $device->stock_place = $place->id;
                            } else {
                                $fail++;
                                $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid stock place value or it does not belong to this company.";
                                $data[] = [
                                    $row['company'],
                                    $row['name'],
                                    $row['asset_tag'],
                                    $row['serial'],
                                    $row['product_number'],
                                    $row['manufacture'],
                                    $row['model'],
                                    $row['category'],
                                    $row['status'],
                                    $row['department'],
                                    $row['location'],
                                    $row['internal_place'],
                                    $row['purchase_date'],
                                    $row['purchase_currency'],
                                    $row['purchase_cost'],
                                    $row['purchase_reference'],
                                    $row['uuid'],
                                    $row['warranty_start_date'],
                                    $row['warranty_end_date'],
                                    $row['warranty_months'],
                                    $row['amc_expire_date'],
                                    $row['amc_supplier'],
                                    $row['notes'],
                                    $row['order_number'],
                                    $row['asset_owner'],
                                    $row['supplier'],
                                    $row['ip'],
                                    $row['mac'],
                                    $row['device_type'],
                                    $row['device_from'],
                                    $row['stock_place'],
                                    $row['requestable'],
                                    $row['high_priority'],
                                    $row['sez_device'],
                                    'fail',
                                    "The record for serial '" . $row['serial'] . "' has not updated due to invalid stock place value or it does not belong to this company."
                                ];
                                continue;
                            }
                        }
                    }
                }

                if($row['requestable']) {
                    $device->requestable = strtolower($row['requestable']) == "yes" ? 1 : 0;
                }

                if ($row['sez_device']) {
                    $device->sez_device = strtolower($row['sez_device']) == "yes" ? 1 : 0;
                }

                if($row['high_priority']) {
                    $device->high_pririty = strtolower($row['high_priority']) == "yes" ? 1 : 0;
                }

                if($row['warranty_months']) {
                    $device->warranty_months = strtolower($row['warranty_months']) != "null" ? ((int) $row['warranty_months']) : null;
                }

                /* amc supplier ID */
                if ($row['amc_supplier']) {
                    try {
                        if ($row['amc_supplier'] != "") {
                            $try_loc = Supplier::where('name', 'like', $row['amc_supplier'])->first();
                            if ($try_loc) {
                                $device->amc_supplier_id = $try_loc->id;
                            } else {
                                $fail++;
                                $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid AMC Supplier Value";
                                $data[] = [
                                    $row['company'],
                                    $row['name'],
                                    $row['asset_tag'],
                                    $row['serial'],
                                    $row['product_number'],
                                    $row['manufacture'],
                                    $row['model'],
                                    $row['category'],
                                    $row['status'],
                                    $row['department'],
                                    $row['location'],
                                    $row['internal_place'],
                                    $row['purchase_date'],
                                    $row['purchase_currency'],
                                    $row['purchase_cost'],
                                    $row['purchase_reference'],
                                    $row['uuid'],
                                    $row['warranty_start_date'],
                                    $row['warranty_end_date'],
                                    $row['warranty_months'],
                                    $row['amc_expire_date'],
                                    $row['amc_supplier'],
                                    $row['notes'],
                                    $row['order_number'],
                                    $row['asset_owner'],
                                    $row['supplier'],
                                    $row['ip'],
                                    $row['mac'],
                                    $row['device_type'],
                                    $row['device_from'],
                                    $row['stock_place'],
                                    $row['requestable'],
                                    $row['high_priority'],
                                    $row['sez_device'],
                                    'fail',
                                    "The record : '" . $row['serial'] . "' has not updated due to invalid AMC Supplier."
                                ];
                                continue;
                            }
                        }
                    }
                    catch(\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "The record : '" . $row['serial'] . "' has not updated due to invalid AMC Supplier";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record : '" . $row['serial'] . "' has not updated due to invalid AMC Supplier"
                        ];
                        continue;
                    }
                }

                if( $row['notes']) {
                    $device->notes = strtolower( $row['notes']) != "null" ? $row['notes'] : null;
                }
                // if($row['asset_owner']) {
                //     $thisOwnerId  = optional( User::where('username', 'like', $row['asset_owner'])->first() )->id;
                //     $device->asset_owner = $thisOwnerId ? $thisOwnerId : null;
                // }
                if ($row['asset_owner']) {
                    if ($row['asset_owner'] == "null") {
                        $device->asset_owner = null;
                    } else {
                        $assetOwner = User::where('username', 'like', $row['asset_owner'])->where('company_id', $device->company_id)->first();
                        if (!empty($assetOwner)) {
                            $device->asset_owner = $assetOwner->id;
                        } else {
                            $fail++;
                            $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid asset owner or it does not belong to this company.";
                            $data[] = [
                                $row['company'],
                                $row['name'],
                                $row['asset_tag'],
                                $row['serial'],
                                $row['product_number'],
                                $row['manufacture'],
                                $row['model'],
                                $row['category'],
                                $row['status'],
                                $row['department'],
                                $row['location'],
                                $row['internal_place'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['purchase_reference'],
                                $row['uuid'],
                                $row['warranty_start_date'],
                                $row['warranty_end_date'],
                                $row['warranty_months'],
                                $row['amc_expire_date'],
                                $row['amc_supplier'],
                                $row['notes'],
                                $row['order_number'],
                                $row['asset_owner'],
                                $row['supplier'],
                                $row['ip'],
                                $row['mac'],
                                $row['device_type'],
                                $row['device_from'],
                                $row['stock_place'],
                                $row['requestable'],
                                $row['high_priority'],
                                $row['sez_device'],
                                'fail',
                                "The record for serial '" . $row['serial'] . "' has not updated due to invalid asset owner or it does not belong to this company."
                            ];
                            continue;
                        }
                    }
                }
                if($row['ip']) {
                    $device->ip = strtolower($row['ip']) != "null" ? $row['ip'] : null;
                }
                if($row['mac']) {
                    $device->mac = strtolower($row['mac']) != "null" ? $row['mac'] : null;
                }
                if($row['device_type']) {
                    $thisAssetType = AssetType::where('name', 'like', $row['device_type'])->first();
                    if(!empty($thisAssetType)) {
                        $device->asset_type_id = $thisAssetType->id;
                    } else {
                        $fail++;
                        $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid device type value.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record for serial '" . $row['serial'] . "' has not updated due to invalid device type value."
                        ];
                        continue;
                    }
                }

                if ($row['location']) {
                    $locationObj = Location::where('name', 'like', $row['location'])->where('company_id', $device->company_id)->first();
                    if (!empty($locationObj)) {
                        $device->rtd_location_id = $locationObj->id;
                        if ($row['internal_place']) {
                            if ($row['internal_place'] == "null") {
                                $device->internal_place_id = null;
                            } else {
                                $place = Place::where('place', $row['internal_place'])->where('location_id',$device->rtd_location_id)->where('company_id', $device->company_id)->first();
                                if (!empty($place) && $device->rtd_location_id == $place->location_id) {
                                    $device->internal_place_id = $place->id;
                                } else {
                                    $fail++;
                                    $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid internal place value or it does not belong to this company.";
                                    $data[] = [
                                        $row['company'],
                                        $row['name'],
                                        $row['asset_tag'],
                                        $row['serial'],
                                        $row['product_number'],
                                        $row['manufacture'],
                                        $row['model'],
                                        $row['category'],
                                        $row['status'],
                                        $row['department'],
                                        $row['location'],
                                        $row['internal_place'],
                                        $row['purchase_date'],
                                        $row['purchase_currency'],
                                        $row['purchase_cost'],
                                        $row['purchase_reference'],
                                        $row['uuid'],
                                        $row['warranty_start_date'],
                                        $row['warranty_end_date'],
                                        $row['warranty_months'],
                                        $row['amc_expire_date'],
                                        $row['amc_supplier'],
                                        $row['notes'],
                                        $row['order_number'],
                                        $row['asset_owner'],
                                        $row['supplier'],
                                        $row['ip'],
                                        $row['mac'],
                                        $row['device_type'],
                                        $row['device_from'],
                                        $row['stock_place'],
                                        $row['requestable'],
                                        $row['high_priority'],
                                        $row['sez_device'],
                                        'fail',
                                        "The record for serial '" . $row['serial'] . "' has not updated due to invalid internal place value or it does not belong to this company."
                                    ];
                                    continue;
                                }
                            }
                        }
                    } else {
                        $fail++;
                        $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid location name or it does not belong to this company.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record for serial: '" . $row['serial'] . "' has not updated due to invalid location name or it does not belong to this company."
                        ];
                        continue;
                    }
                } else {
                    if ($row['internal_place']) {
                        if ($row['internal_place'] == "null") {
                            $device->internal_place_id = null;
                        } else {
                            $place = Place::where('place', $row['internal_place'])->where('location_id',$device_col->rtd_location_id)->where('company_id', $device->company_id)->first();
                            if (!empty($place) && $device->rtd_location_id == $place->location_id) {
                                $device->internal_place_id = $place->id;
                            } else {
                                $fail++;
                                $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid internal place value or it does not belong to this company.";
                                $data[] = [
                                    $row['company'],
                                    $row['name'],
                                    $row['asset_tag'],
                                    $row['serial'],
                                    $row['product_number'],
                                    $row['manufacture'],
                                    $row['model'],
                                    $row['category'],
                                    $row['status'],
                                    $row['department'],
                                    $row['location'],
                                    $row['internal_place'],
                                    $row['purchase_date'],
                                    $row['purchase_currency'],
                                    $row['purchase_cost'],
                                    $row['purchase_reference'],
                                    $row['uuid'],
                                    $row['warranty_start_date'],
                                    $row['warranty_end_date'],
                                    $row['warranty_months'],
                                    $row['amc_expire_date'],
                                    $row['amc_supplier'],
                                    $row['notes'],
                                    $row['order_number'],
                                    $row['asset_owner'],
                                    $row['supplier'],
                                    $row['ip'],
                                    $row['mac'],
                                    $row['device_type'],
                                    $row['device_from'],
                                    $row['stock_place'],
                                    $row['requestable'],
                                    $row['high_priority'],
                                    $row['sez_device'],
                                    'fail',
                                    "The record for serial '" . $row['serial'] . "' has not updated due to invalid internal place value or it does not belong to this company."
                                ];
                                continue;
                            }
                        }
                    }
                }

                /* Supplier */
                if ($row['supplier']) {
                    if ($row['supplier'] == "null") {
                        $device->supplier_id = null;
                    } else {
                        $supplier = Supplier::where('name', trim($row['supplier']))->first();
                        if (!empty($supplier)) {
                            $device->supplier_id = $supplier->id;
                        } else {
                            $fail++;
                            $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid supplier name.";
                            $data[] = [
                                $row['company'],
                                $row['name'],
                                $row['asset_tag'],
                                $row['serial'],
                                $row['product_number'],
                                $row['manufacture'],
                                $row['model'],
                                $row['category'],
                                $row['status'],
                                $row['department'],
                                $row['location'],
                                $row['internal_place'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['purchase_reference'],
                                $row['uuid'],
                                $row['warranty_start_date'],
                                $row['warranty_end_date'],
                                $row['warranty_months'],
                                $row['amc_expire_date'],
                                $row['amc_supplier'],
                                $row['notes'],
                                $row['order_number'],
                                $row['asset_owner'],
                                $row['supplier'],
                                $row['ip'],
                                $row['mac'],
                                $row['device_type'],
                                $row['device_from'],
                                $row['stock_place'],
                                $row['requestable'],
                                $row['high_priority'],
                                $row['sez_device'],
                                'fail',
                                "The record for serial '" . $row['serial'] . "' has not updated due to invalid supplier name."
                            ];
                            continue;
                        }
                    }
                }

                /* purchase_reference */
                if ($row['purchase_reference']) {
                    if ($row['purchase_reference'] == "null") {
                        $device->invoice_id = null;
                    } else {
                        $purchase_reference = Purchase::where('invoice_no', $row['purchase_reference'])->where('company_id', $device->company_id)->first();
                        if (!empty($purchase_reference)) {
                            $device->invoice_id = $purchase_reference->id;
                        } else {
                            $fail++;
                            $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid purchase reference name or it does not belong to this company.";
                            $data[] = [
                                $row['company'],
                                $row['name'],
                                $row['asset_tag'],
                                $row['serial'],
                                $row['product_number'],
                                $row['manufacture'],
                                $row['model'],
                                $row['category'],
                                $row['status'],
                                $row['department'],
                                $row['location'],
                                $row['internal_place'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['purchase_reference'],
                                $row['uuid'],
                                $row['warranty_start_date'],
                                $row['warranty_end_date'],
                                $row['warranty_months'],
                                $row['amc_expire_date'],
                                $row['amc_supplier'],
                                $row['notes'],
                                $row['order_number'],
                                $row['asset_owner'],
                                $row['supplier'],
                                $row['ip'],
                                $row['mac'],
                                $row['device_type'],
                                $row['device_from'],
                                $row['stock_place'],
                                $row['requestable'],
                                $row['high_priority'],
                                $row['sez_device'],
                                'fail',
                                "The record for serial '" . $row['serial'] . "' has not updated due to invalid purchase reference or it does not belong to this company."
                            ];
                            continue;
                        }
                    }
                }

                if ($row['device_from']) {
                    if (in_array($row['device_from'], ["Purchase Device", "Project Device", "Rental", "Customer Owned"])) {
                        $types = Device::getDeviceFrom();
                        foreach ($types as $k => $type) {
                            if ($type['text'] == $row['device_from']) {
                                $device->device_occure_type = $type['id'];
                                break;
                            }
                        }
                    } else {
                        $fail++;
                        $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not updated due to invalid device from value.";
                        $data[] = [
                            $row['company'],
                            $row['name'],
                            $row['asset_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['status'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['purchase_reference'],
                            $row['uuid'],
                            $row['warranty_start_date'],
                            $row['warranty_end_date'],
                            $row['warranty_months'],
                            $row['amc_expire_date'],
                            $row['amc_supplier'],
                            $row['notes'],
                            $row['order_number'],
                            $row['asset_owner'],
                            $row['supplier'],
                            $row['ip'],
                            $row['mac'],
                            $row['device_type'],
                            $row['device_from'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            "The record for serial: '" . $row['serial'] . "' has not updated due to invalid device from value."
                        ];
                        continue;
                    }
                }

                /* custom fields data capture */
                if( count($found_custom_keys) ) {
                    foreach($found_custom_keys as $fck) {
                        $prefixed_code = CustomField::getPrefixedFieldCode($fck);
                        $device->{$prefixed_code} = $data_to_insert[$prefixed_code] !="" ? $data_to_insert[$prefixed_code]: null;
                    }
                }
                $device->warranty_status = $device->updateWarrantyStatus();
                $device->calc_warranty_expire_date = $device->updateWarrantyExpireDate();

                if($device->save()) {
                    $basic = Basic::where('device_id',$device->id)->first();
                    if ($basic) {
                        $basic->timestamps = false;
                        $basic->company = $device->company_id;
                        $basic->save();
                    }
                    if(in_array(config('app.client'), ["rolepermission", "knightfrank", "rashmi"])) {
                        if(!empty($oldStatus) && !empty($device->status_id) && $oldStatus != $device->status_id){
                            CommonHelper::updateStatusCounts($oldStatus, $device->status_id, $device);
                        }else if($oldModel != $device->model_id) {
                            CommonHelper::updateStatusCounts($oldStatus, $device->status_id, $device, null, $oldModel);
                        }
                    }
                    if(Settings::first()->custom_fieldset_id != "") {
                        $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
                        if (!empty($customFieldset)) {
                            $customFieldsRecordData = CommonHelper::interactedData($customFieldset->fields, $device);
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
                    if($isUpdated){
                        Actionlog::deviceEdited($device, $for_log_comparison, Auth::user()->id,$customFieldsCacheData,$customFieldsRecordData);
                    }
                    $success++;
                    $succ_msgs[] = "The record : '" . $row['serial'] . "' has Updated Successfully";
                    $data[] = [
                        $row['company'],
                        $row['name'],
                        $row['asset_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['status'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['purchase_reference'],
                        $row['uuid'],
                        $row['warranty_start_date'],
                        $row['warranty_end_date'],
                        $row['warranty_months'],
                        $row['amc_expire_date'],
                        $row['amc_supplier'],
                        $row['notes'],
                        $row['order_number'],
                        $row['asset_owner'],
                        $row['supplier'],
                        $row['ip'],
                        $row['mac'],
                        $row['device_type'],
                        $row['device_from'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'Success',
                        "The record : '" . $row['serial'] . "' has Updated Successfully"
                    ];
                } else {
                    $fail++;
                    $fail_msgs[] = "The record : '" . $row['serial'] . "' not updated.";
                    $data[] = [
                        $row['company'],
                        $row['name'],
                        $row['asset_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['status'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['purchase_reference'],
                        $row['uuid'],
                        $row['warranty_start_date'],
                        $row['warranty_end_date'],
                        $row['warranty_months'],
                        $row['amc_expire_date'],
                        $row['amc_supplier'],
                        $row['notes'],
                        $row['order_number'],
                        $row['asset_owner'],
                        $row['supplier'],
                        $row['ip'],
                        $row['mac'],
                        $row['device_type'],
                        $row['device_from'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        "The record : '" . $row['serial'] . "' not updated."
                    ];
                    continue;
                }
            }
        }
        catch(\Exception $e) {
            Log::error("device bulk update error: " . $e->getMessage());
        }

        $file_name = $this->request->file('import_file');
        $given_file_original_name = preg_replace('@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName());
        if (!empty($data)) {
            $i = 0;
            foreach ($data as &$row) {
                if(!empty($customValue)){ 
                    foreach ($customValue as $key => $value) {
                        $d = $value['device_'.$i];
                        foreach ( $d as $k => $dv) {
                            foreach ($found_custom_keys as $ck) {
                                if($ck == $k){
                                    $removedElements = array_splice($row, -2);
                                    $prefixed_code = CustomField::getPrefixedFieldCode($ck);
                                    $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
                                    if(!empty($customFieldset)) {
                                        foreach ($customFieldset->fields as $f) {
                                            if($ck == strtolower(str_replace(' ', '_', $f->name))){
                                                if($f->element == 'datetime'){
                                                    if ($dv != null && gettype($dv) != "string") {
                                                        $dv = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dv)->format('m/d/Y H:i');
                                                        $dataArray = array_merge($row, array($dv));
                                                    }else{
                                                        $dataArray = array_merge($row, array($dv));
                                                    }
                                                }elseif($f->element == 'time'){
                                                    if ($dv != null && gettype($dv) != "string") {
                                                        $dv = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dv)->format('h:i A');
                                                        $dataArray = array_merge($row, array($dv));
                                                    }else{
                                                        $dataArray = array_merge($row, array($dv));
                                                    }
                                                }elseif($f->element == 'date'){
                                                        if ($dv != null && gettype($dv) != "string") {
                                                            $dv = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dv)->format('n/j/Y');
                                                            $dataArray = array_merge($row, array($dv));
                                                        }else{
                                                            $dataArray = array_merge($row, array($dv));
                                                        }
                                                }elseif($f->element != 'date' && $f->element != 'datetime' && $f->element != 'time'){
                                                    if($dv != null) {
                                                        $dataArray = array_merge($row, array($dv));
                                                    }else{
                                                        $dataArray = array_merge($row, array($dv));
                                                    }
                                                }
                                                $row = array_merge($dataArray,$removedElements);
                                            }
                                        }  
                                    }
                                }
                            }
                        }
                    }
                };
                if (isset($row[12]) && gettype($row[12]) != 'string' && $row[12] !== null) {
                    $row[12] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[12])->format('n/j/Y');
                }
                if (isset($row[17]) && gettype($row[17]) != 'string' && $row[17] !== null) {
                    $row[17] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[17])->format('n/j/Y');
                }
                if (isset($row[18]) && gettype($row[18]) != 'string' && $row[18] !== null) {
                    $row[18] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[18])->format('n/j/Y');
                }
                if (isset($row[20]) && $row[20] !== null) {
                    if (is_numeric($row[20])) {$row[20] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[20])->format('m/d/Y');
                    }else {
                        $row[20] = date('m/d/Y', strtotime($row[20]));
                    }
                }
                $i++;
            }
            unset($row);
        }
        $defaultKkeys = array('Company', 'Name','Asset Tag','Serial','Product Number', 'Manufacture', 'Model', 'Category', 'Status', 'Department','Location','Internal Place','Purchase Date','Purchase Currency','Purchase Cost','Purchase Reference','Uuid','Warranty Start Date','Warranty End Date','Warranty Months','Amc Expire Date', 'Amc  Supplier', 'Notes','Order Number','Asset Owner','Supplier','IP','MAC','Device Type','Device From', 'Stock Place','Requestable','High Priority', 'SEZ Device');
        if(!empty($customValue) && count($found_custom_keys) > 0) {
            foreach($found_custom_keys as $d) {
                $cf= ucwords(str_replace('_', ' ', $d));
                $defaultKkeys[] = $cf;
            }
        }
        $succ_fail = array('Success/Fail' ,'Message');
        $keys = array_merge($defaultKkeys, $succ_fail);
        $name = 'DeviceUpdateFormat_'.date('dmYHis').'.xlsx';
        $doc_path = Excel::store(new DeviceImportStore($data, $keys), $name, 'bulk_documents');

        $log = new BulkActions();
        $log->action_type = 5;
        $log->module_id = 1;
        $log->created_at = date("Y-m-d H:i:s");
        $log->doc_path = $name;
        $log->doc_name = $given_file_original_name;
        $log->tot_success = $success;
        $log->tot_failure = $fail;
        $log->user_id = Auth::user()->id;
        $log->save();

        if (config('app.socket_enabled')) {
            CommonHelper::sendDeviceCountToSocket();
        }

        $return['fail'] = $fail;
        $return['success'] = $success;
        $return['fail_msgs'] = $fail_msgs;
        $this->data = $return;
    }

    public function model(array $row)
    {
        $invalied_key = false;
        $required_keys = ['company', 'name', 'asset_tag', 'serial', 'product_number', 'manufacture', 'model', 'category', 'status', 'department', 'location', 'internal_place', 'purchase_date', 'purchase_currency', 'purchase_cost', 'purchase_reference', 'uuid', 'warranty_start_date', 'warranty_end_date', 'warranty_months', 'amc_expire_date', 'amc_supplier', 'notes', 'order_number', 'asset_owner', 'supplier', 'ip', 'mac', 'device_type', 'device_from', 'stock_place', 'requestable', 'high_priority', 'sez_device'];
        $custom_fields_code = CustomField::getAllFieldsAsCode();

        // Log::info(json_encode($row));

    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}

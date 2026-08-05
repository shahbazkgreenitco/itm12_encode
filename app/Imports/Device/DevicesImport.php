<?php

namespace App\Imports\Device;

use App\Helpers\Common as CommonHelper;
use App\Models\Actionlog;
use App\Models\AssetAllocationType;
use App\Models\AssetType;
use App\Models\BulkActions;
use App\Models\Category;
use App\Models\Company;
use App\Models\CustomField;
use App\Models\CustomFieldset;
use App\Models\Device;
use App\Models\Label;
use App\Models\Location;
use App\Models\Manufacture;
use App\Models\Department;
use App\Models\Model;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Place;
use App\Models\BlockedIP;
use App\Models\Currency;
use App\Models\Device\PatchManagementGroup;
use App\Models\Device\PatchManagementGroupDevice;
use App\Models\Lease;
use App\Models\AssetInoutReason;
use Illuminate\Validation\Rule;
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
use Carbon\Carbon;
class DevicesImport implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
{
    use Importable, SkipsErrors;
    public $data, $request, $custom_fields_code;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->custom_fields_code = [];
        $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
        if(!empty($customFieldset)) {
            foreach ($customFieldset->fields as $f) {
                $fields = CustomField::where('name', $f->name)->first();
                if(!empty($fields)) {
                    $this->custom_fields_code[] = preg_replace("/[^a-zA-Z0-9]/","_",strtolower($f["name"]));
                }
            }
        }
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        // dd($collection);
        // $this->data = collect();
        /*$dyname = str_random(16);
        $file_name = $this->request->file('import_file');
        $given_file_original_name = preg_replace('@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName());
        $doc_path = Excel::store($collection, $given_file_original_name, 'bulk_documents');*/

        $devicecount = Device::whereNotIn('status_id', [7])->count();
        $tot_count = $devicecount + count($collection);
        if ($devicecount > config("services.assets.asset_limit") || $tot_count > config("services.assets.asset_limit") ) {
            $fail_msgs = ["You don't have permission to insert more than " . config('services.assets.asset_limit') . " devices. Please contact Admin"];
            $return['fail'] = 1;
            $return['success'] = 0;
            $return['fail_msgs'] = $fail_msgs;
            $this->data = $return;
            return;
        }
        $return = ["msg"=>"Unable to import the given device list", "status"=>"danger"];
        $success = 0;
        $fail = 0;
        $fail_msgs = [];
        $data = [];
        // $custom_fields_val = $found_custom_keys = [];
        $exception_break = false;
        $tot_insert_records = 0;
        $invalied_key = false;
        $currencies = Currency::getCurrencies();
        $currentUser = Auth::user()->id;
        $currentAuthUser = Auth::user();
        $all_companies = Company::getAllCompany();
        // $companyId = CommonHelper::getAccessibleCompanyIds();
        $required_keys = ['company', 'device_name', 'device_tag', 'serial', 'product_number', 'manufacture', 'model', 'category', 'device_status','checkout_for','checkout_to', 'checkout_reason', 'expected_checkin','allocation_type', 'department', 'location', 'internal_place', 'purchase_date' ,'currency_format','purchase_cost', 'purchase_reference', 'uuid', 'warranty_start_date','warranty_end_date', 'warranty_months', 'amc_expire_date', 'amc_supplier', 'notes', 'order_number','asset_owner', 'supplier', 'ip', 'mac', 'device_type','device_from','contract_reference', 'stock_place', 'requestable', 'high_priority', 'sez_device'];
        // $custom_fields_code = CustomField::getAllFieldsAsCode();
        $non_deployed_labels = Label::getAllNonDeployedLabels();
        $not_null_deployed_labels = Label::getAllNotNullDeployedLabels();
        $device_from_values = ["Purchase Device", "Project Device", "Rental", "Customer Owned"];
        $checkout_reasons = AssetInoutReason::where(['action_type' => 2, 'status' => 1])->get()->pluck('id', 'name')->toArray();
        foreach ($collection as $key => $row) {
            $collected_keys_tot = 0;
            $custom_keys_tot = 0;
            $invalid_value_at_field = false;
            $row = CommonHelper::importColumnValidate($row);
            $found_custom_keys = $data_to_insert = $customValue = $data_to_insert_c = $validation_rules_c = [];
            $customFieldsError = $customFields = null;
            foreach($row as $trim_k=>$trim_v)
            {
                if(! $trim_k) {
                    continue;
                }
                if(in_array($trim_k, $required_keys)) {
                    $collected_keys_tot++;
                }
                elseif(in_array($trim_k, $this->custom_fields_code)) {
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

                if(in_array($trim_k, ['amc_expire_date'])) {
                    continue;
                }
                if(in_array($trim_k, ['expected_checkin'])) {
                    continue;
                }
                $value = strip_tags(trim($trim_v));
                $row[$trim_k] = $value;

                if( is_numeric($value) && stripos((string) $value, "e+") > 0 ) {
                    $exception_break = true;
                    $return["msg"] = "The record no: '" . ($key + 1) . "' has an invalid '{$trim_k}' value.";
                    Session::flash('msg', $return);
                    $invalid_value_at_field = true;
                    break;
                }
            }

            if($invalid_value_at_field) {
                break;
            }

            if($invalied_key || count($required_keys) != $collected_keys_tot) {
                // dd([
                //     'Invalid Key'        => $invalied_key,
                //     'Required Count'     => count($required_keys),
                //     'Collected Count'    => $collected_keys_tot,
                //     'Excel Headers'      => array_keys($row->toArray()),
                //     'Expected Headers'   => $required_keys,
                // ]);
                $exception_break = true;
                $return["msg"] = trans('content.device_fields.invalid_column');
                Session::flash('msg', $return);
                continue;
            }

            $text_msg_until_completed_rows = "Device(s) imported " . ($key) . " records.";
            // $data_to_insert = [];
            /* custom fields data capture */
            if(count($found_custom_keys)) {
                foreach($found_custom_keys as $fck) {
                    $cv = null;
                    $prefixed_code = CustomField::getPrefixedFieldCode($fck);
                    $customFieldset = CustomFieldset::where('id', Settings::first()->custom_fieldset_id)->first();
                    if(!empty($customFieldset)) {
                        foreach ($customFieldset->fields as $f) {
                            $this->custom_fields_code[$prefixed_code] = (string)$row[$fck];
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
                                                $customFields = $text_msg_until_completed_rows . "Invalid ".$fck." is found at Excel Row" . ($key + 1);
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
                                                $customFields = $text_msg_until_completed_rows . "Invalid ".$fck." is found at Excel Row" . ($key + 1);
                                            }

                                        }
                                        // if($f->preDefinedOptions == 1){
                                        //     if($row[$fck] != '' || $row[$fck] != null) {
                                        //         $cv = $row[$fck];
                                        //         $getLocation = Location::where("name", $cv)->where('deleted_at', null)->first();
                                        //         $data_to_insert_c[$prefixed_code] = null;
                                        //         if($getLocation != null){
                                        //             $data_to_insert_c[$prefixed_code] = strval($getLocation->id);
                                        //         }
                                        //         else {
                                        //             $customFieldsError = true;
                                        //             $customFields = $text_msg_until_completed_rows . "Invalid ".$fck." is found at Excel Row" . ($key + 1);
                                        //         }

                                        //     }
                                        // }elseif($f->preDefinedOptions == 2){
                                        //     if($row[$fck] != '' || $row[$fck] != null) {
                                        //         $cv = $row[$fck];
                                        //         $getUser = User::where("username", $cv)->where('activated', 1)->where('deleted_at', null)->first();
                                        //         $data_to_insert_c[$prefixed_code] = null;
                                        //         if($getUser != null){
                                        //             $data_to_insert_c[$prefixed_code] = strval($getUser->id);
                                        //         }
                                        //         else {
                                        //             $customFieldsError = true;
                                        //             $customFields = $text_msg_until_completed_rows . "Invalid ".$fck." is found at Excel Row" . ($key + 1);
                                        //         }

                                        //     }
                                        // }     
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
            if( $customFieldsError != null && $customFields != null ){
                if($customFieldsError == true) {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $customFields;
                    $data[] = [
                        $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
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

            $dummy_tag = "";
            if(! $row['device_tag']) {
                $dummy_tag = sha1(time());
                $data_to_insert["asset_tag"] = substr($dummy_tag, 0, 98);
            } else {
                $data_to_insert["asset_tag"] = (string) $row['device_tag'];
            }
            $data_to_insert['serial'] = (string) $row['serial'];
            $data_to_insert['product_number'] = (string)  $row['product_number'];
            $data_to_insert['name'] = (string) $row['device_name'];

            /* Company ID */
            $data_to_insert['company_id'] = null;
            if(! $row['company']) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Company Name is required at Excel Row " . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Company Name is required at Excel Row" . ($key + 1)
                ];
                continue;
            }

            foreach($all_companies as $a_company) {
                if( strtolower($row['company']) == $a_company->name ) {
                    $data_to_insert['company_id'] = $a_company->id;
                    break;
                }
            }

            if(! $data_to_insert['company_id']) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Company Name is found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Company Name is found at Excel Row" . ($key + 1)
                ];
                continue;
            }


            // if(!in_array($data_to_insert['company_id'], $companyId)) {
            //     $exception_break = true;
            //     $fail++;
            //     $fail_msgs[] = $text_msg_until_completed_rows . "Permission denied to import for another company at Excel Row " . ($key + 1);
            //     $data[] = [
            //         $row['company'],
            //         $row['device_name'],
            //         $row['device_tag'],
            //         $row['serial'],
            //         $row['product_number'],
            //         $row['manufacture'],
            //         $row['model'],
            //         $row['category'],
            //         $row['device_status'],
            //         $row['checkout_for'],
            //         $row['checkout_to'],
            //         $row['expected_checkin'],
            //         $row['allocation_type'],
            //         $row['department'],
            //         $row['location'],
            //         $row['internal_place'],
            //         $row['purchase_date'],
            //         $row['currency_format'],
            //         $row['purchase_cost'],
            //         $row['purchase_reference'],
            //         $row['uuid'],
            //         $row['warranty_start_date'],
            //         $row['warranty_end_date'],
            //         $row['warranty_months'],
            //         $row['amc_expire_date'],
            //         $row['amc_supplier'],
            //         $row['notes'],
            //         $row['order_number'],
            //         $row['asset_owner'],
            //         $row['supplier'],
            //         $row['ip'],
            //         $row['mac'],
            //         $row['device_type'],
            //         $row['device_from'],
            //         $row['contract_reference'],
            //         $row['stock_place'],
            //         $row['requestable'],
            //         $row['high_priority'],
            //         $row['sez_device'],
            //         'fail',
            //         $text_msg_until_completed_rows . "Permission denied to import for another company at Excel Row " . ($key + 1),
            //     ];
            //     continue;
            // }




            /* purchase currency format*/
            $data_to_insert['purchase_currency'] = null;
            try {
                if($row['currency_format']) {
                    $upper_currency = strtoupper($row['currency_format']);
                    if($upper_currency == "NULL") {
                        $data_to_insert['purchase_currency'] = null;
                    } elseif(! array_key_exists($upper_currency, $currencies)) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Value For Currency Format found at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid Value For Currency Format found at Excel Row" . ($key + 1)
                        ];
                        continue;
                    } else {
                        $data_to_insert['purchase_currency'] = $upper_currency;
                    }
                }
            } catch(\Exception $e) {
                Log::error("currency_format Error: " . $e->getMessage());
                $data_to_insert['purchase_currency'] = null;
            }

            $data_to_insert['purchase_cost'] = 0;
            if($row['purchase_cost']) {
                $data_to_insert['purchase_cost'] = $row['purchase_cost'];
            }

            $data_to_insert['purchase_date'] = null;
            if (!empty($row['purchase_date'])) {
                if (gettype($row['purchase_date']) == "string" && strtotime($row['purchase_date'])) {
                    $data_to_insert['purchase_date'] = Carbon::parse($row['purchase_date'])->format('Y-m-d');
                } elseif (gettype($row['purchase_date']) != "string") {
                    $data_to_insert['purchase_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['purchase_date'])->format('Y-m-d');
                } else {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Value For Purchase Date found at Excel Row" . ($key + 1);
                    $data[] = [
                        $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        $text_msg_until_completed_rows . "Invalid Value For Purchase Date found at Excel Row" . ($key + 1)
                    ];
                    continue;
                }
            } 

            $data_to_insert['warranty_start_date'] = null;
            try {
                if($row['warranty_start_date'] != null){
                    if (gettype($row['warranty_start_date']) == "string" && strtotime($row['warranty_start_date'])) {
                        $data_to_insert['warranty_start_date'] = Carbon::parse($row['warranty_start_date'])->format('Y-m-d');
                    } elseif (gettype($row['warranty_start_date']) != "string") {
                        $data_to_insert['warranty_start_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['warranty_start_date'])->format('Y-m-d');
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Value For Warranty Start Date found at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid Value For Warranty Start Date found at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }

                // $data_to_insert['warranty_start_date'] = ($row['warranty_start_date'] && gettype($row['warranty_start_date']) != "string") ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['warranty_start_date'])->format('Y-m-d') : null;
            }
            catch(\Exception $e) {
                $data_to_insert['warranty_start_date'] = null;
            }

            $data_to_insert['warrenty_end_date'] = null;
            try {
                if($row['warranty_end_date'] != null){
                    if (gettype($row['warranty_end_date']) == "string" && strtotime($row['warranty_end_date'])) {
                        $data_to_insert['warrenty_end_date'] = Carbon::parse($row['warranty_end_date'])->format('Y-m-d');
                    } elseif (gettype($row['warranty_end_date']) != "string") {
                        $data_to_insert['warrenty_end_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['warranty_end_date'])->format('Y-m-d');
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Value For Warranty End Date found at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid Value For Warranty Start End found at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
                // $data_to_insert['warrenty_end_date'] = ($row['warranty_end_date'] && gettype($row['warranty_end_date']) != "string") ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['warranty_end_date'])->format('Y-m-d') : null;
            }
            catch(\Exception $e) {
                $data_to_insert['warrenty_end_date'] = null;
            }

            $data_to_insert['amc_expire_date'] = null;
            try {
                if($row['amc_expire_date'] != null){
                    if (gettype($row['amc_expire_date']) == "string" && strtotime($row['amc_expire_date'])) {
                        $data_to_insert['amc_expire_date'] = Carbon::parse($row['amc_expire_date'])->format('Y-m-d');
                    } elseif (gettype($row['amc_expire_date']) != "string") {
                        $data_to_insert['amc_expire_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['amc_expire_date'])->format('Y-m-d');
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Value For AMC Expire Date found at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid Value For AMC Expire Date found at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
            if ($data_to_insert['amc_expire_date']) {
                $amcDate = Carbon::parse($data_to_insert['amc_expire_date']);
                $purchaseDate = $data_to_insert['purchase_date'] ? Carbon::parse($data_to_insert['purchase_date']) : null;
                $warrantyDate = $data_to_insert['warranty_start_date'] ? Carbon::parse($data_to_insert['warranty_start_date']) : null;

                if (($purchaseDate && $amcDate <= $purchaseDate) || ($warrantyDate && $amcDate <= $warrantyDate)) {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "AMC Expire Date must be greater than Purchase Date and Warranty Start Date at Excel Row" . ($key + 1);
                    $data[] = [
                        $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        $text_msg_until_completed_rows . "AMC Expire Date must be greater than Purchase Date and Warranty Start Date at Excel Row" . ($key + 1)
                    ];
                    continue;
                }
            }
                // $data_to_insert['amc_expire_date'] = ($row['amc_expire_date'] && gettype($row['amc_expire_date']) != "string") ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['amc_expire_date'])->format('Y-m-d') : null;
            }
            catch(\Exception $e) {
                $data_to_insert['amc_expire_date'] = null;
            }

            if(isset($row['ip'])) {
                $ipObj = BlockedIP::select('ip')->where('ip', $row['ip'])->first();
            }
            if(isset($row['mac'])) {
                $macObj = BlockedIP::select('mac')->where('mac', $row['mac'])->first();
            }
            if( !empty($ipObj) || !empty($macObj) ) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "IP/Mac address is Blocked" . ($key + 1);
                $data[] = [
                    $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "IP/Mac address is Blocked" . ($key + 1)
                ];
                continue;
            }
            //To check the sez checkbox value
            $data_to_insert['sez_device'] = null;
            if ($row['sez_device'] != "") {
                if ($row['sez_device'] !== "1" && $row['sez_device'] !== "0") {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Value For Sez Device found at Excel Row" . ($key + 1);
                    $data[] = [
                        $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        $text_msg_until_completed_rows . "Invalid Value For Sez Device found at Excel Row" . ($key + 1)
                    ];
                    continue;
                }
            }

            //To check the high priority checkbox value
            $data_to_insert['high_pririty'] = null;
            if ($row['high_priority'] != "") {
                if ($row['high_priority'] !== "1" && $row['high_priority'] !== "0") {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Value For High Pririty found at Excel Row" . ($key + 1);
                    $data[] = [
                        $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        $text_msg_until_completed_rows . "Invalid Value For High Pririty found at Excel Row" . ($key + 1)
                    ];
                    continue;
                }
            }

            //To check the requestable checkbox value
            $data_to_insert['requestable'] = null;
            if ($row['requestable'] != "") {
                if ($row['requestable'] !== "1" && $row['requestable'] !== "0") {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Value For Requestable found at Excel Row" . ($key + 1);
                    $data[] = [
                        $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        $text_msg_until_completed_rows . "Invalid Value For Requestable found at Excel Row" . ($key + 1)
                    ];
                    continue;
                }
            }
        
            $data_to_insert['uuid'] = isset($row['uuid']) ? $row['uuid'] : null;
            $data_to_insert['order_number'] = $row['order_number'];
            $data_to_insert['warranty_months'] = $row['warranty_months'] ? preg_replace("/[^0-9]/", "", $row['warranty_months']) : null;
            $data_to_insert['notes'] = $row['notes'];
            $data_to_insert['requestable'] = (isset($row['requestable']) && $row['requestable'] == 1) ? 1 : 0;
            $data_to_insert['manufacture'] =  $row['manufacture'];
            $data_to_insert['category'] =  $row['category'];
            $data_to_insert['model'] = $row['model'];
            $data_to_insert['asset_owner'] = $row['asset_owner'];
            $data_to_insert['ip'] = $row['ip'];
            $data_to_insert['mac'] = $row['mac'];
            $data_to_insert['high_pririty'] = (isset($row['high_priority']) && $row['high_priority'] == 1) ? 1 : 0;
            $data_to_insert['sez_device'] = (isset($row['sez_device']) && $row['sez_device'] == 1) ? 1 : 0;

            // /* custom fields data capture */
            // if(count($found_custom_keys)) {
            //     foreach($found_custom_keys as $fck) {
            //         $prefixed_code = CustomField::getPrefixedFieldCode($fck);
            //         $data_to_insert[$prefixed_code] = (string) $row[$fck];
            //         $validation_rules[$prefixed_code] = "nullable|string|max:2000";
            //     }
            // }

            /* department ID */
            $data_to_insert['department_id'] = null;
            if ($row['department'] != null) {
                try {
                    $try_dep = Department::where('name', 'like', $row['department'])->where('company_id', $data_to_insert['company_id'])->first();
                    $data_to_insert['department_id'] = $try_dep->id;
                }
                catch(\Exception $e) {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Department found or it does not belong to this company at Excel Row" . ($key + 1);
                    $data[] = [
                        $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        $text_msg_until_completed_rows . "Invalid Department found or it does not belong to this company at Excel Row" . ($key + 1)
                    ];
                    continue;
                }
            }

            /* Location ID */
            $data_to_insert['rtd_location_id'] = null;
            try {
                $try_loc = Location::where('name', 'like', $row['location'])->where('company_id', $data_to_insert['company_id'])->first();
                $data_to_insert['rtd_location_id'] = $try_loc->id;
            }
            catch(\Exception $e) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Location found or it does not belong to this company at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Location found or it does not belong to this company at Excel Row" . ($key + 1)
                ];
                continue;

            }

            /* Internal Place */
            $data_to_insert['internal_place_id'] = null;
            try {
                if ($row['internal_place'] != "") {
                    $place = Place::where('place', $row['internal_place'])->where('location_id', $data_to_insert['rtd_location_id'])->where('company_id', $data_to_insert['company_id'])->first();
                    if ($place) {
                        $data_to_insert['internal_place_id'] = $place->id;
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Internal Place found or it does not belong to this company at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid Internal Place found or it does not belong to this company at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
            }
            catch(\Exception $e) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Internal Place found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Internal Place found at Excel Row" . ($key + 1)
                ];
                continue;

            }

            /* Supplier */
            $data_to_insert['supplier_id'] = null;
            try {
                if ($row['supplier'] != "") {
                    $supplier = Supplier::where('name', $row['supplier'])->first();
                    if ($supplier) {
                        $data_to_insert['supplier_id'] = $supplier->id;
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Supplier Name found at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid Supplier Name found at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
            }
            catch(\Exception $e) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Supplier Name found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Internal Place found at Excel Row" . ($key + 1)
                ];
                continue;
            }

            /* purchase_reference */
            $data_to_insert['invoice_id'] = null;
            try {
                if ($row['purchase_reference'] != "") {
                    $purchase_reference = Purchase::where('invoice_no', $row['purchase_reference'])->where('company_id', $data_to_insert['company_id'])->first();
                    if ($purchase_reference) {
                        $data_to_insert['invoice_id'] = $purchase_reference->id;
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid purchase reference found or it does not belong to this company at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid purchase reference found or it does not belong to this company at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
            }
            catch(\Exception $e) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid purchase reference  found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                     'fail',
                     $text_msg_until_completed_rows . "Invalid purchase reference  found at Excel Row" . ($key + 1)
                 ];
                 continue;
             }

            /* amc supplier ID */
            $data_to_insert['amc_supplier_id'] = null;
            try {
                if ($row['amc_supplier'] != "") {
                    $try_loc = Supplier::where('name', 'like', $row['amc_supplier'])->first();
                    if ($try_loc) {
                        $data_to_insert['amc_supplier_id'] = $try_loc->id;
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Supplier found at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid Supplier found at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
            }
            catch(\Exception $e) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Supplier found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Supplier found at Excel Row" . ($key + 1)
                ];
                continue;

            }

            /* Device Type(asset_type_id) */
            $data_to_insert['asset_type_id'] = null;
            try {
                if($row['device_type'] != "") {
                    $try_asst_type = AssetType::where('name', 'like', $row['device_type'])->first();
                    // if(!empty($try_asst_type)) {
                        $data_to_insert['asset_type_id'] = $try_asst_type->id;
                    // }
                }
            }
            catch(\Exception $e) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Device Type found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Device Type found at Excel Row" . ($key + 1)
                ];
                continue;

            }

            /* Device Stock Place */
            $data_to_insert['stock_place'] = null;
            try {
                if ($row['stock_place'] != "") {
                    $place = Place::where('place', $row['stock_place'])->where('location_id', $data_to_insert['rtd_location_id'])->where('company_id', $data_to_insert['company_id'])->first();
                    if ($place) {
                        $data_to_insert['stock_place'] = $place->id;
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Stock Place found or it does not belong to this company at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid Stock Place found or it does not belong to this company at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
            }
            catch(\Exception $e) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Stock Place found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Stock Place found at Excel Row" . ($key + 1)
                ];
                continue;
            }

            /* Device From(device_occure_type) */
            $data_to_insert['device_occure_type'] = null;
            // if(! $row['device_from']) {
            //     $exception_break = true;
            //     $fail++;
            //     $fail_msgs[] = $text_msg_until_completed_rows . "Device From is required at Excel Row" . ($key + 1);
            //     $data[] = [
            //         $row['company'],
            //         $row['device_name'],
            //         $row['device_tag'],
            //         $row['serial'],
            //         $row['manufacture'],
            //         $row['model'],
            //         $row['category'],
            //         $row['status'],
            //         $row['department'],
            //         $row['location'],
            //         $row['internal_place'],
            //         $row['purchase_date'],
            //         $row['purchase_cost'],
            //         $row['purchase_reference'],
            //         $row['uuid'],
            //         $row['warranty_start_date'],
            //         $row['warrenty_end_date'],
            //         $row['warranty_months'],
            //         $row['amc_expire_date'],
            //         $row['amc_supplier'],
            //         $row['notes'],
            //         $row['order_number'],
            //         $row['asset_owner'],
            //         $row['supplier'],
            //         $row['ip'],
            //         $row['mac'],
            //         $row['device_type'],
            //         $row['device_from'],
            //         $row['stock_place'],
            //         $row['requestable'],
            //         $row['high_priority'],
            //         $row['sez_device'],
            //         //$record->{$fck},
            //         'fail',
            //         $text_msg_until_completed_rows . "Device From is required at Excel Row" . ($key + 1)
            //     ];
            //     continue;
            // }
            foreach($device_from_values as $key => $value) {
                if( trim($row['device_from']) == $value ) {
                    $data_to_insert['device_occure_type'] = $key;
                    break;
                } else{
                    $data_to_insert['device_occure_type'] = 0;
                }
            }

            if($data_to_insert['device_occure_type'] === "") {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Device From Value is found at Excel Row " . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Device From Value is found at Excel Row " . ($key + 1)
                ];
                continue;
            }

            /* Status ID */
            $data_to_insert['status_id'] = null;
            if(! $row['device_status']) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Status Value is required at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Status Value is required at Excel Row" . ($key + 1)
                ];
                continue;
            }

            foreach($not_null_deployed_labels as $dep_labels) {
                if( strtolower($row['device_status']) == strtolower($dep_labels->name) ) {
                    $data_to_insert['status_id'] = $dep_labels->id;
                    break;
                }
            }

            if(! $data_to_insert['status_id']) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Status Value is found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    //$record->{$fck},
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Status Value is found at Excel Row" . ($key + 1)
                ];
                continue;
            }

          if($data_to_insert['status_id'] == 6){
            $validateAssignedFor = Validator::make($row->toArray(), [
                'checkout_for' => 'required|in:User,Place',
            ]);
            if($validateAssignedFor->fails()) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid or Empty Assigned For is found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Status Value is found at Excel Row" . ($key + 1)
                ];
                continue;
            }
            $validateAssignedTo = Validator::make($row->toArray(), [
                'checkout_to' => 'required|string'
            ]);
            if($validateAssignedTo->fails()) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid or Empty Assigned To is found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Status Value is found at Excel Row" . ($key + 1)
                ];
                continue;
            }
            $validateAllocationType = Validator::make($row->toArray(), [
                'allocation_type' => 'required'
            ]);
            if($validateAllocationType->fails()) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid or Empty Allocation Type is found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid Allocation Type is found at Excel Row" . ($key + 1)
                ];
                continue;
            }
            if (!empty($row['expected_checkin'])) {
                try {
                    $expectedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expected_checkin']);
                    $expectedDate = Carbon::parse($expectedDate)->startOfDay();
                } catch (\Exception $e) {
                    $expectedDate = null;
                }
            
                if (!$expectedDate || $expectedDate->lt(Carbon::today())) {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Expected Checkin Date found at Excel Row " . ($key + 1);
            
                    $data[] = [
                        $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                        'fail',
                        $text_msg_until_completed_rows . "Invalid or Past Expected Checkin Date found at Excel Row " . ($key + 1)
                    ];
                    continue;
                }
            
                $data_to_insert['expected_checkin'] = $expectedDate->format('Y-m-d');
            }
            
            if($row['checkout_for'] == 'User' || $row['checkout_for'] == 'Place'){
                $data_to_insert['assigned_for'] = $row['checkout_for'] == 'User' ? 1:2;
                if($row['checkout_for'] == 'User'){
                    $thisOwnerId  = User::where('username', 'like', $row['checkout_to'])->where('company_id', $data_to_insert['company_id'])->first();
                    if($thisOwnerId != null) {
                        $user = User::find($thisOwnerId->id);
                        if($user->checkoutBasicClearance() || $user->checkLastWorkingDate()){
                            $exception_break = true;
                            $fail++;
                            $fail_msgs[] = $text_msg_until_completed_rows . "Chosen user is not in active status or This user's last working date has already completed is found at Excel Row" . ($key + 1);
                            $data[] = [
                                $row['company'],
                                $row['device_name'],
                                $row['device_tag'],
                                $row['serial'],
                                $row['product_number'],
                                $row['manufacture'],
                                $row['model'],
                                $row['category'],
                                $row['device_status'],
                                $row['checkout_for'],
                                $row['checkout_to'],
                                $row['checkout_reason'],
                                $row['expected_checkin'],
                                $row['allocation_type'],
                                $row['department'],
                                $row['location'],
                                $row['internal_place'],
                                $row['purchase_date'],
                                $row['currency_format'],
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
                                $row['contract_reference'],
                                $row['stock_place'],
                                $row['requestable'],
                                $row['high_priority'],
                                $row['sez_device'],
                                'fail',
                                $text_msg_until_completed_rows . "Chosen user is not in active status or This user's last working date has already completed is found at Excel Row" . ($key + 1)
                            ];
                            continue;
                        } else {
                            $data_to_insert['assigned_to'] = $thisOwnerId->id;
                            $data_to_insert['stock_place'] = null;
                        }
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Choose User is not found or it does not belong to this company at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Choose User is not found or it does not belong to this company at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
                
                if($row['checkout_for'] == 'Place'){
                    $thisPlaceId  = Place::where('place', 'like', $row['checkout_to'])->where('company_id', $data_to_insert['company_id'])->first();
                    if($thisPlaceId != null) {
                        $data_to_insert['assigned_to'] = $thisPlaceId->id;
                        $data_to_insert['stock_place'] = null;
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Please provide the correct place for the device or it does not belong to this company at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Please provide the correct place name for the device or it does not belong to this company at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
                
                if($row['allocation_type']){
                    $allocationType = AssetAllocationType::where('name', 'like', $row['allocation_type'])->first();
                    if($allocationType == null) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Please provide the correct allocation type for the device at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Please provide the correct allocation type for the device at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
                $data_to_insert['accepted'] = null;
                $data_to_insert['chkin_log_id'] = null; 
            }
          }

            $validation_rules = [
                'company_id' => 'required|integer|min:1',
                'serial' => 'required|clean_text_only|string|min:4|max:255|not_regex:/[#\$]/|unique:assets,serial',
                'product_number' => 'nullable|string|max:255|not_regex:/[\$]/|clean_text_only_with_hash',
                'name' => 'nullable|clean_text_only|string|max:100',
                'asset_tag' => 'required|string|max:100|unique:assets,asset_tag',
                'manufacture' => 'required|clean_text_only|string|min:1|max:255',
                'category' => 'required|clean_text_only|string|min:1|max:255',
                'model' => 'required|clean_text_only|string|min:1|max:255',
                'status_id' => 'required|integer|min:1',
                'rtd_location_id' => 'required|integer|min:1',
                'internal_place_id' => 'nullable|integer|min:1|exists:places,id',
                'uuid' => 'nullable|clean_text_only|string|max:100',
                'purchase_date' => 'nullable|date_format:Y-m-d',
                'warranty_start_date' => 'nullable|date_format:Y-m-d',
                'warrenty_end_date' => 'nullable|date_format:Y-m-d',
                'expected_checkin' => 'nullable|date_format:Y-m-d',
                'asset_owner' => 'nullable|clean_text_only|string',
                'supplier_id' => 'nullable|integer',
                'order_number' => 'nullable|clean_text_only|string|max:100',
                'purchase_cost' => 'nullable|numeric',
                'warranty_months' => 'nullable|integer|min:0',
                'notes' => 'nullable|clean_text_only|string|max:2000',
                'requestable' => 'sometimes|nullable|integer|min:0|max:1',
                // 'mac' => ['nullable', 'regex:/^([0-9A-Fa-f]{2}[:-]?){5}([0-9A-Fa-f]{2})$/'],
                'mac' => ['nullable', 'regex:/^[0-9A-Fa-f]+$/'],
                "ip" => "nullable|ip", 
                "device_type" => "nullable|string",
                "device_occure_type" => "nullable|integer|min:0|max:3",
                'stock_place'       => 'nullable|integer|min:1|exists:places,id',
                'amc_expire_date' => 'nullable|date_format:Y-m-d',
                'device_from' => 'nullable|string|in:Purchase Device, Project Device, Rental, Customer Owned'
            ];

            if(!empty($validation_rules_c)){
                $validation_rules = array_merge($validation_rules,$validation_rules_c);
            }
            if(!empty($data_to_insert_c)){
                $data_to_insert = array_merge($data_to_insert,$data_to_insert_c);
            }

            /* Data Validation */
            $validate = Validator::make($data_to_insert, $validation_rules, [
                'model.required' => 'Provided model ('. $row['model'] .') not found at Excel Row ' . ($key + 1) . $text_msg_until_completed_rows,
                'status_id.required' => 'Provided status ('. $row['device_status'] .') not found at Excel Row ' . ($key + 1) . $text_msg_until_completed_rows,
                'company_id.required' => 'Company is required at Excel Row ' . ($key + 1) . $text_msg_until_completed_rows,
                'rtd_location_id.required' => 'Provided location ('. $row['location'] . ') not found at Excel Row ' . ($key + 1) . $text_msg_until_completed_rows,
                'serial.required' => 'The serial field is required.',
                'serial.string' => 'The serial field must be a string.',
                'serial.min' => 'The serial field must be at least 4 characters.',
                'serial.max' => 'The serial field may not be greater than 255 characters.',
                'serial.not_regex' => 'The serial field must not contain the characters # or $.',
                'serial.unique' => 'The serial has already been taken.',
                'product_number.string' => 'The product number field must be a string.',
                'product_number.max' => 'The product number field may not be greater than 255 characters.',
                'product_number.not_regex' => 'The product number field must not contain the characters $.',
                'product_number.unique' => 'The product number has already been taken.',
            ]);

            if($validate->fails()) {
                $exception_break = true;
                $v = $validate->errors()->toArray();
                $e = array_shift($v);
                foreach($e as $value){
                    $err = $value;
                }
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Error:" . $err;
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Error:" . $err
                ];
                continue;
            }
           
            /* take care for multiple same names */
            $thisManufactureId  = optional( Manufacture::where('name', 'like', $row['manufacture'])->first() )->id;
            if(empty($thisManufactureId))
            {
                $newManufac = new Manufacture;
                $newManufac->name = $row['manufacture'];
                $newManufac->user_id = Auth::user()->id;
                $newManufac->save();
                $thisManufactureId = $newManufac->id;
            }

            // $thisOwnerId  = optional( User::where('username', 'like', $row['asset_owner'])->first() )->id;
            // $data_to_insert['asset_owner'] = $thisOwnerId ? $thisOwnerId : null;

            $data_to_insert['asset_owner'] = null;
            try {
                if ($row['asset_owner'] != "") {
                    $thisOwnerId = optional( User::where('username', 'like', $row['asset_owner'])->where('company_id', $data_to_insert['company_id'])->first() )->id;
                    if ($thisOwnerId) {
                        $data_to_insert['asset_owner'] = $thisOwnerId;
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid asset owner found or it does not belong to this company at Excel  Row" . ($key + 1);
                        $data[] = [
                        $row['company'],
                        $row['device_name'],
                        $row['device_tag'],
                        $row['serial'],
                        $row['product_number'],
                        $row['manufacture'],
                        $row['model'],
                        $row['category'],
                        $row['device_status'],
                        $row['checkout_for'],
                        $row['checkout_to'],
                        $row['checkout_reason'],
                        $row['expected_checkin'],
                        $row['allocation_type'],
                        $row['department'],
                        $row['location'],
                        $row['internal_place'],
                        $row['purchase_date'],
                        $row['currency_format'],
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
                        $row['contract_reference'],
                        $row['stock_place'],
                        $row['requestable'],
                        $row['high_priority'],
                        $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid asset owner found or it does not belong to this company at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
            }
            catch(\Exception $e) {
                $exception_break = true;
                $fail++;
                $fail_msgs[] = $text_msg_until_completed_rows . "Invalid asset owner found at Excel Row" . ($key + 1);
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'fail',
                    $text_msg_until_completed_rows . "Invalid asset owner found at Excel Row" . ($key + 1)
                ];
                continue;
            }

            $data_to_insert['lease_id'] = null;
            try {
                if ($row['contract_reference'] != "" && $row['device_from'] == "Project Device") {
                    // $thisOwner = Lease::find($row['contract_reference'])->id;
                    $thisOwner = optional( Lease::where('id', $row['contract_reference'])->where('company_id', $data_to_insert['company_id'])->first())->id;
                    if ($thisOwner) {
                        $data_to_insert['lease_id'] = $thisOwner;
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Contract Reference found or it does not belong to this company at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['company'],
                            $row['device_name'],
                            $row['device_tag'],
                            $row['serial'],
                            $row['product_number'],
                            $row['manufacture'],
                            $row['model'],
                            $row['category'],
                            $row['device_status'],
                            $row['checkout_for'],
                            $row['checkout_to'],
                            $row['checkout_reason'],
                            $row['expected_checkin'],
                            $row['allocation_type'],
                            $row['department'],
                            $row['location'],
                            $row['internal_place'],
                            $row['purchase_date'],
                            $row['currency_format'],
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
                            $row['contract_reference'],
                            $row['stock_place'],
                            $row['requestable'],
                            $row['high_priority'],
                            $row['sez_device'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid Contract Reference found or it does not belong to this company at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
            }
            catch(\Exception $e) {
                continue;
            }

            /* take care for multipple same names */
            // $thisCategoryId = optional( Category::where('name', 'like', $row['category'] )->first() )->id;
            $thisCategoryId = optional( Category::where('name', 'like', $row['category'] )->where('category_type','asset')->first() )->id;
            if(empty($thisCategoryId))
            {
                $newCategory = new Category;
                $newCategory->name = $row['category'];
                $newCategory->user_id = Auth::user()->id;
                $newCategory->save();
                $thisCategoryId = $newCategory->id;
            }

            /* take care for multipple same names */
            $data_to_insert['model_id'] = optional( Model::where([
                [ 'name', 'like', $row['model'] ],
                [ 'manufacturer_id','=', $thisManufactureId ],
                [ 'category_id','=',  $thisCategoryId ]
            ])->first() )->id;

            if(empty($data_to_insert['model_id'] ))
            {
                // insert model
                $newModel = new Model;
                $newModel->name = $row['model'];
                $newModel->manufacturer_id = $thisManufactureId;
                $newModel->category_id = $thisCategoryId;
                $newModel->user_id = Auth::user()->id;
                $newModel->save();
                $data_to_insert['model_id'] = $newModel->id;
            }

            $data_to_insert['user_id'] = $currentUser;
            $data_to_insert['purchase_date'] = $data_to_insert['purchase_date'];
            $data_to_insert['warranty_start_date'] = $data_to_insert['warranty_start_date'];
            $data_to_insert['warrenty_end_date'] = $data_to_insert['warrenty_end_date'];
            $data_to_insert['purchase_cost'] = (float) $data_to_insert['purchase_cost'];
            // $data_to_insert['purchase_currency'] = Settings::first()->default_currency;
            $data_to_insert['warranty_months'] = $data_to_insert['warranty_months'] > 0 ? $data_to_insert['warranty_months'] : null;
            $data_to_insert['requestable'] = $data_to_insert['requestable'];
            $data_to_insert['physical'] = 1;
            $data_to_insert['archived'] = 0;
            $data_to_insert['depreciate'] = 0;
            unset($data_to_insert['manufacture']);
            unset($data_to_insert['category']);
            unset($data_to_insert['model']);

            $newInst = Device::create($data_to_insert);
            if($dummy_tag) {
                $newInst->asset_tag = $newInst->fixDeviceTag();
            }

            /* custom fields data capture */
            if( count($found_custom_keys) ) {
                foreach($found_custom_keys as $fck) {
                    $prefixed_code = CustomField::getPrefixedFieldCode($fck);
                    $newInst->{$prefixed_code} = $data_to_insert[$prefixed_code] != "" ? $data_to_insert[$prefixed_code] : null;
                }
            }

            $newStatus = $data_to_insert['status_id'];
            if(in_array(config('app.client'), ["rolepermission", "knightfrank", "rashmi"])) {
                if(!empty($newStatus)){
                    CommonHelper::updateStatusCounts($oldStatus = null, $newStatus, $newInst);
                }
            }
            if($newInst->save()) {
                $newInst->warranty_status = $newInst->updateWarrantyStatus();
                $newInst->calc_warranty_expire_date = $newInst->updateWarrantyExpireDate();
                $data[] = [
                    $row['company'],
                    $row['device_name'],
                    $row['device_tag'],
                    $row['serial'],
                    $row['product_number'],
                    $row['manufacture'],
                    $row['model'],
                    $row['category'],
                    $row['device_status'],
                    $row['checkout_for'],
                    $row['checkout_to'],
                    $row['checkout_reason'],
                    $row['expected_checkin'],
                    $row['allocation_type'],
                    $row['department'],
                    $row['location'],
                    $row['internal_place'],
                    $row['purchase_date'],
                    $row['currency_format'],
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
                    $row['contract_reference'],
                    $row['stock_place'],
                    $row['requestable'],
                    $row['high_priority'],
                    $row['sez_device'],
                    'success',
                    "Device(s) Imported Successfully."
                ];
                $success++;
                if($data_to_insert['status_id'] == 6){
                    $allocationType = AssetAllocationType::where('name', 'like', $row['allocation_type'])->first();
                    $log = new Actionlog();
                    $log->asset_id = $newInst->id;
                    $log->asset_type = "hardware";
                    $log->assigned_for = $newInst->assigned_for;
                    $log->checkedout_to = $newInst->assigned_to;
                    $log->reason_id = $checkout_reasons[$row['checkout_reason']] ?? null;
                    $log->interact_id = 10;
                    $log->interact_type = "i4";
                    $log->interact_module = "m1";
                    $log->project_id = $newInst->last_checkout_project;
                    $log->user_id = Auth::user()->id;
                    $log->action_type = "checkout";
                    $log->note = $newInst->notes;
                    $log->access_code = sha1(time());
                    $log->allocation_type_id = $allocationType->id;
                    $log->save();
                    $newInst->chkout_log_id = $log->id;
                    $newInst->last_checkout = $newInst->created_at;
                    $newInst->accepted = "pending";
                }
                $newInst->save();
            }
            $getGroup = PatchManagementGroup::where('auto_added_new_device', 1)->select('id', 'auto_added_new_device')->get();
            foreach ($getGroup as $key => $value) {
                // Add Devices to the Group
                PatchManagementGroupDevice::insert([
                    'group_id' => $value->id,
                    'device_id' => $newInst->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            Actionlog::deviceAdded($newInst, $currentAuthUser->id);
            $tot_insert_records++;
        }

        if(! $exception_break) {
            $return["msg"] = ((string) $tot_insert_records) . trans('content.device_fields.imported');
            $return["status"] = "success";
            Session::flash('msg', $return);
        }


        // if(count($found_custom_keys)) {
        //     foreach($data as $key => $d) {
        //         array_splice($data[$key], -2, 0, $custom_fields_val);
        //     }
        // }

        // $found_custom_keys ="";
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
                if (isset($row[11]) && gettype($row[11]) != 'string' && $row[11] !== null) {
                    $row[11] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[11])->format('n/j/Y');
                }
                if (isset($row[16]) && gettype($row[16]) != 'string' && $row[16] !== null) {
                    $row[16] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[16])->format('n/j/Y');
                }
                if (isset($row[20]) && gettype($row[20]) != 'string' && $row[20] !== null) {
                    $row[20] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[20])->format('n/j/Y');
                }
                if (isset($row[21]) && gettype($row[21]) != 'string' && $row[21] !== null) {
                    $row[21] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[21])->format('n/j/Y');
                }
                if (isset($row[23]) && gettype($row[23]) != 'string' && $row[23] !== null) {
                    $row[23] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[23])->format('n/j/Y');
                }
                if (isset($row[24]) && gettype($row[24]) != 'string' && $row[24] !== null) {
                    $row[24] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[24])->format('n/j/Y');
                }
                $i++;
            }
            unset($row);
        }
        $defaultKkeys = array('Company','Device Name','Device Tag','Serial','Product Number','Manufacture','Model','Category','Device Status','Checkout For','Checkout To','Expected Checkin','Allocation Type','Department','Location','Internal Place','Purchase Date','Currency Format','Purchase Cost','Purchase Reference','UUID','Warranty Start Date','Warranty End Date','Warranty Months','AMC Expire Date','AMC Supplier','Notes','Order Number','Asset Owner','Supplier','IP','MAC','Device Type','Device From','Contract Reference','Stock Place','Requestable','High Priority','SEZ Device');
        // foreach($found_custom_keys as $fck) {
        //     $defaultKkeys[] = $fck;
        // }
        if(!empty($customValue) && count($found_custom_keys) > 0) {
            foreach($found_custom_keys as $d) {
                $cf= ucwords(str_replace('_', ' ', $d));
                $defaultKkeys[] = $cf;
            }
        }
        $succ_fail = array('Success/Fail' ,'Message');
        $keys = array_merge($defaultKkeys, $succ_fail);
        $name = 'DeviceImportFormat_'.date('dmYHis').'.xlsx';
        $doc_path = Excel::store(new DeviceImportStore($data, $keys), $name, 'bulk_documents');

        $log = new BulkActions();
        $log->action_type = 4;
        $log->module_id = 1;
        $log->created_at = date("Y-m-d H:i:s");
        $log->doc_path = $name;
        $log->doc_name = $given_file_original_name;
        $log->tot_success = $success;
        $log->tot_failure = $fail;
        $log->user_id = Auth::user()->id;
        $log->save();
        // $doc_link = ($doc_path['file']);

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
        $required_keys = ['company', 'device_name', 'device_tag', 'serial', 'product_number', 'manufacture', 'model', 'category', 'device_status','checkout_for','checkout_to','checkin_reason' ,'expected_checkin','allocation_type', 'department', 'location', 'internal_place', 'purchase_date' ,'currency_format','purchase_cost', 'purchase_reference', 'uuid', 'warranty_start_date','warranty_end_date', 'warranty_months', 'amc_expire_date', 'amc_supplier', 'notes', 'order_number','asset_owner', 'supplier', 'ip', 'mac', 'device_type','device_from','contract_reference', 'stock_place', 'requestable', 'high_priority', 'sez_device'];
        $custom_fields_code = $this->custom_fields_code;

        // Log::info(json_encode($row));

    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}

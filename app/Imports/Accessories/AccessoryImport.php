<?php

namespace App\Imports\Accessories;

use App\Models\Accessory;
use App\Models\AccessoryPurchase;
use App\Models\Actionlog;
use App\Models\Company;
use App\Models\CustomField;
use App\Models\CustomFieldset;
use App\Models\Location;
use App\Models\Department;
use App\Models\Purchase;
use App\Models\Category;
use App\Models\BulkActions;
use App\Models\Manufacture;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\User;
use App\Models\ThresholdAlertSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use App\Helpers\Common as CommonHelper;
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

class AccessoryImport implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
{
    use Importable, SkipsErrors;
    public $data, $request, $custom_fields_code;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->custom_fields_code = [];
        $customFieldset = CustomFieldset::where('id', Settings::first()->accessories_custom_fieldset_id)->first();
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
    public function collection(Collection $collection) {  
        try {
            $return = ["msg"=>"Unable to import the given place list", "status"=>"danger"];
            $success = 0;
            $fail = 0;
            $fail_msgs = [];
            $data = $makeCustomData = [];
            // $custom_fields_val = $found_custom_keys = [];
            $exception_break = false;
            $tot_insert_records = 0;
            $invalied_key = false;
            $currentUser = Auth::user()->id;
            $currentAuthUser = Auth::user();
            $all_companies = Company::getAllCompany();
            // $companyId = CommonHelper::getAccessibleCompanyIds();
            $required_keys = ['unique_tag', 'company', 'name', 'department', 'category', 'manufacture', 'supplier', 'location', 'qty', 'threshold_qty', 'reorder_limit','threshold_alert','purchase_reference', 'purchase_date', 'purchase_currency', 'purchase_cost', 'order_number', 'notes'];
            foreach ($collection as $key => $row) {
                $collected_keys_tot = 0;
                $custom_keys_tot = 0;
                $invalid_value_at_field = false;
                $row = CommonHelper::importColumnValidate($row);
                $found_custom_keys = $data_to_insert = $customValue = $data_to_insert_c = $validation_rules_c = [];

                foreach($row as $trim_k=>$trim_v)
                {
                    if(! $trim_k) {
                        continue;
                    }
                    if(in_array($trim_k, $required_keys)) {
                        $collected_keys_tot++;
                    } elseif(in_array($trim_k, $this->custom_fields_code)) {
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
                    $exception_break = true;
                    $return["msg"] = trans('content.device_fields.invalid_column');
                    Session::flash('msg', $return);
                    continue;
                }

                $text_msg_until_completed_rows = "accessory(s) imported " . ($key+1) . " records.";

                /* custom fields data capture */
                if(count($found_custom_keys)) {
                    foreach($found_custom_keys as $fck) {
                        $cv = null;
                        $prefixed_code = CustomField::getPrefixedFieldCode($fck);
                        $customFieldset = CustomFieldset::where('id', Settings::first()->accessories_custom_fieldset_id)->first();
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
                        $dataCk['accessory_'.$key][$fck] = $cv;
                    }
                    $customValue[] = $dataCk;
                }

                /* Company ID */
                $data_to_insert['company_id'] = null;
                try {
                    if(! $row['company']) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Company Name is required at Excel Row " . ($key + 1);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['supplier'],
                            $row['location'],
                            $row['qty'],
                            $row['threshold_qty'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['order_number'],
                            $row['notes'],
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
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['supplier'],
                            $row['location'],
                            $row['qty'],
                            $row['threshold_qty'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['order_number'],
                            $row['notes'],
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
                    //         $row['unique_tag'],
                    //         $row['company'],
                    //         $row['name'],
                    //         $row['department'],
                    //         $row['category'],
                    //         $row['manufacture'],
                    //         $row['supplier'],
                    //         $row['location'],
                    //         $row['qty'],
                    //         $row['threshold_qty'],
                    //         $row['reorder_limit'],
                    //         $row['threshold_alert'],
                    //         $row['purchase_reference'],
                    //         $row['purchase_date'],
                    //         $row['purchase_currency'],
                    //         $row['purchase_cost'],
                    //         $row['order_number'],
                    //         $row['notes'],
                    //         'fail',
                    //         $text_msg_until_completed_rows . "Permission denied to import for another company at Excel Row " . ($key + 1),
                    //     ];
                    //     continue;
                    // }
                }
                catch(\Exception $e) {
                    Log::error("Accessory company import Error: " . $e->getMessage());
                }

                /* Accessory Name */
                $data_to_insert['name'] = (string) $row['name'];

                /* Department */
                $data_to_insert['department_id'] = null;
                if ($row['department'] != "" && ! $data_to_insert['department_id']) {
                    try {
                        $departments = Department::select('id', 'name as text')->where('company_id', $data_to_insert['company_id'])->orderBy('name')->get();
                        foreach($departments as $department) {
                            if($row['department'] == $department->text ) {
                                $data_to_insert['department_id'] = $department->id;
                                break;
                            }
                        }

                        if($row['department'] != "" && ! $data_to_insert['department_id']) {
                            $exception_break = true;
                            $fail++;
                            $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Department Name is found or it does not belong to this company at Excel Row" . ($key + 1);
                            $data[] = [
                                $row['unique_tag'],
                                $row['company'],
                                $row['name'],
                                $row['department'],
                                $row['category'],
                                $row['manufacture'],
                                $row['supplier'],
                                $row['location'],
                                $row['qty'],
                                $row['threshold_qty'],
                                $row['reorder_limit'],
                                $row['threshold_alert'],
                                $row['purchase_reference'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['order_number'],
                                $row['notes'],
                                'fail',
                                $text_msg_until_completed_rows . "Invalid Department Name is found or it does not belong to this company at Excel Row" . ($key + 1)
                            ];
                            continue;
                        }
                    }
                    catch(\Exception $e) {
                        Log::error("Department import Error: " . $e->getMessage());
                    }
                }

                /* Category */
                $data_to_insert['category_id'] = null;
                if($row['category']){
                    $thisCategoryId = optional( Category::where([
                        [ 'name', 'like', $row['category'] ],
                    ])->where('category_type','accessory')->first() )->id;

                    if(empty($thisCategoryId ))
                    {
                        $newCategory = new Category;
                        $newCategory->name = $row['category'];
                        $newCategory->user_id = Auth::user()->id;
                        $newCategory->category_type = 'accessory';
                        $newCategory->save();
                        $thisCategoryId = $newCategory->id;
                    }
                    $data_to_insert['category_id'] = $thisCategoryId;
                }

                /* Accessory Qty */
                $data_to_insert['qty'] = (int) $row['qty'];

                /* threshold_qty Accessory Qty */
                $data_to_insert['accessory_thresholds'] = (int) $row['threshold_qty'];
                $data_to_insert['reorder_limits'] = (int) ($row['reorder_limit'] ?? 0);

                /* Manufacture */
                $data_to_insert['manufacturer_id'] = null;
                try {
                    $try_manufacture = optional( Manufacture::where('name', 'like', $row['manufacture'])->first() )->id;
                    if(empty($try_manufacture) && $row['manufacture'] != "") {
                        $newManufac = new Manufacture;
                        $newManufac->name = $row['manufacture'];
                        $newManufac->user_id = Auth::user()->id;
                        $newManufac->save();
                        $try_manufacture = $newManufac->id;
                    }
                    $data_to_insert['manufacturer_id'] = $try_manufacture;
                } catch(\Exception $e) {
                    Log::error("manufactureImport Error: " . $e->getMessage());
                }
                if($row['manufacture'] != "" && !$data_to_insert['manufacturer_id']) {      
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Manufacture Name is found at Excel Row" . ($key + 1);
                    $data[] = [
                        $row['unique_tag'],
                        $row['company'],
                        $row['name'],
                        $row['department'],
                        $row['category'],
                        $row['manufacture'],
                        $row['supplier'],
                        $row['location'],
                        $row['qty'],
                        $row['threshold_qty'],
                        $row['reorder_limit'],
                        $row['threshold_alert'],
                        $row['purchase_reference'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['order_number'],
                        $row['notes'],
                        'fail',
                        $text_msg_until_completed_rows . "Invalid Manufacture Name is found at Excel Row" . ($key + 1)
                    ];
                    continue;
                }

                /* Location*/
                $data_to_insert['location_id'] = null;
                try {
                    $try_loc = Location::where('name', 'like', $row['location'])->where('company_id',$data_to_insert['company_id'])->first();
                    $data_to_insert['location_id'] = $try_loc->id;
                } catch(\Exception $e) {
                    Log::error("locationImport Error: " . $e->getMessage());
                    if(! $row['location']) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Location Name is required at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['supplier'],
                            $row['location'],
                            $row['qty'],
                            $row['threshold_qty'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['order_number'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . "Location Name is required at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
                if(! $data_to_insert['location_id']) {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Invalid location Name is found or it does not belong to this company at Excel Row" . ($key + 1);
                    $data[] = [
                        $row['unique_tag'],
                        $row['company'],
                        $row['name'],
                        $row['department'],
                        $row['category'],
                        $row['manufacture'],
                        $row['supplier'],
                        $row['location'],
                        $row['qty'],
                        $row['threshold_qty'],
                        $row['reorder_limit'],
                        $row['threshold_alert'],
                        $row['purchase_reference'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['order_number'],
                        $row['notes'],
                        'fail',
                        $text_msg_until_completed_rows . "Invalid location Name is found or it does not belong to this company at Excel Row" . ($key + 1)
                    ];
                    continue;
                }

                /* Supplier */
                $data_to_insert['supplier_id'] = null;
                try {
                    $try_supplier =  optional(Supplier::where('name', 'like', $row['supplier'])->first())->id;
                    $data_to_insert['supplier_id'] = $try_supplier;
                } catch(\Exception $e) {
                    Log::error("supplierImport Error: " . $e->getMessage());
                }

                if($row['supplier'] != "" && !$data_to_insert['supplier_id']) {      
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Supplier Name is found at Excel Row" . ($key + 1);
                    $data[] = [
                        $row['unique_tag'],
                        $row['company'],
                        $row['name'],
                        $row['department'],
                        $row['category'],
                        $row['manufacture'],
                        $row['supplier'],
                        $row['location'],
                        $row['qty'],
                        $row['threshold_qty'],
                        $row['reorder_limit'],
                        $row['threshold_alert'],
                        $row['purchase_reference'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['order_number'],
                        $row['notes'],
                        'fail',
                        $text_msg_until_completed_rows . "Invalid Supplier Name is found at Excel Row" . ($key + 1)
                    ];
                    continue;
                }

                /* Purchase Reference*/
                $data_to_insert['invoice_id'] = null;
                try {
                    $purchases = Purchase::select('id', 'invoice_no')->where('company_id', $data_to_insert['company_id'])->get();
                    foreach($purchases as $purchase) {
                        if($row['purchase_reference'] == $purchase->invoice_no ) {
                            $data_to_insert['invoice_id'] = $purchase->id;
                            break;
                        }
                    }

                    if($row['purchase_reference'] != "" && ! $data_to_insert['invoice_id']) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Purchase Reference value is found or it does not belong to this company at Excel Row" . ($key + 1);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['supplier'],
                            $row['location'],
                            $row['qty'],
                            $row['threshold_qty'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['order_number'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . "Invalid Purchase Reference value is found or it does not belong to this company at Excel Row" . ($key + 1)
                        ];
                        continue;
                    }
                }
                catch(\Exception $e) {
                    Log::error(" Purchase Reference import Error: " . $e->getMessage());
                }

                /* purchase date */
                $data_to_insert['purchase_date'] = null;
                try {
                    if (!empty($row['purchase_date'])) {
                        if (gettype($row['purchase_date']) == "string" && strtotime($row['purchase_date'])) {
                            $data_to_insert['purchase_date'] = Carbon::parse($row['purchase_date'])->format('Y-m-d');
                        }
                        elseif (gettype($row['purchase_date']) != "string") {
                            $data_to_insert['purchase_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['purchase_date'])->format('Y-m-d');
                        }
                    } else {
                        $data_to_insert['purchase_date'] = null;
                    }
                } catch (\Exception $e) {
                    Log::error("purchase_date Error: " . $e->getMessage());
                    $data_to_insert['purchase_date'] = null;
                }

                /* purchase currency */
                $data_to_insert['purchase_currency'] = null;
                try {
                   $data_to_insert["purchase_currency"] = isset($row["purchase_currency"]) ? $row["purchase_currency"] : null;
                }
                catch(\Exception $e) {
                    Log::error("purchase_currency Error: " . $e->getMessage());
                    $data_to_insert['purchase_currency'] = null;
                }

                 /* purchase cost */
                $data_to_insert['purchase_cost'] = 0;
                if($row['purchase_cost']) {
                    $safe_purchase_cost = preg_replace("/[^0-9.]/", "", $row['purchase_cost']);
                    $data_to_insert['purchase_cost'] = $safe_purchase_cost;
                }

                // try {
                //     $threshold = $row['threshold_qty'];
                //     $thresholdAlertEmail = $row['threshold_alert'];
                //     if(($threshold > 0 && empty($thresholdAlertEmail))) {
                //         $exception_break = true;
                //         $fail++;
                //         $fail_msgs[] = $text_msg_until_completed_rows . "Threshold Alert field is required when thresholds are set.";
                //         $data[] = [
                //             $row['company'],
                //             $row['name'],
                //             $row['department'],
                //             $row['category'],
                //             $row['manufacture'],
                //             $row['supplier'],
                //             $row['location'],
                //             $row['qty'],
                //             $row['threshold_qty'],
                //             $row['reorder_limit'],
                //             $row['threshold_alert'],
                //             $row['purchase_reference'],
                //             $row['purchase_date'],
                //             $row['purchase_currency'],
                //             $row['purchase_cost'],
                //             $row['order_number'],
                //             $row['notes'],
                //             'fail',
                //             $text_msg_until_completed_rows . "Threshold Alert field is required when thresholds are set" . ($key + 1)
                //         ];
                //         continue;
                //     }
                // }
                // catch(\Exception $e) {
                //     Log::error("Threshold Alert field is required when thresholds are set: " . $e->getMessage());
                // }
                if(!empty($row['threshold_alert'])){
                    try {
                        $invailidUser = false;
                        $thresholdAlert = explode(",", $row['threshold_alert']);
                        foreach($thresholdAlert as $t){
                            $getUser = User::where("username", $t)->where('activated', 1)->where('deleted_at', null)->where('company_id',$data_to_insert['company_id'])->pluck('id')->first();
                            if(empty($getUser)){
                                $invailidUser = true;
                                break;
                            }
                        }
                        if(empty($getUser) || $invailidUser == true) {
                            $exception_break = true;
                            $fail++;
                            $fail_msgs[] = $text_msg_until_completed_rows . "Invalid Threshold Alert username is found or it does not belong to this company at Excel Row" . ($key + 1);
                            $data[] = [
                                $row['unique_tag'],
                                $row['company'],
                                $row['name'],
                                $row['department'],
                                $row['category'],
                                $row['manufacture'],
                                $row['supplier'],
                                $row['location'],
                                $row['qty'],
                                $row['threshold_qty'],
                                $row['reorder_limit'],
                                $row['threshold_alert'],
                                $row['purchase_reference'],
                                $row['purchase_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['order_number'],
                                $row['notes'],
                                'fail',
                                $text_msg_until_completed_rows . "Invalid Threshold Alert username is found or it does not belong to this company at Excel Row" . ($key + 1)
                            ];
                            continue;
                        }
                    }
                    catch(\Exception $e) {
                        Log::error("Threshold Alert username import Error: " . $e->getMessage());
                    }
                }

                 /* order number */
                $data_to_insert['order_number'] = null;
                try {
                    $data_to_insert['order_number'] = (string) $row['order_number'];
                }
                catch(\Exception $e) {
                    Log::error("order_number error: " . $e->getMessage());
                    $data_to_insert['order_number'] = null;
                }

                 /* notes */
                $data_to_insert['notes'] = null;
                try {
                    $data_to_insert['notes'] = (string) $row['notes'] ? $row['notes'] : null;
                }
                catch(\Exception $e) {
                    Log::error("notes error: " . $e->getMessage());
                    $data_to_insert['notes'] = null;
                }

                $validation_rules = [
                    'company_id' => 'required|integer|min:1',
                    "name" => "required|clean_text_only|alpha_space|min:3|max:255",
                    "department_id" => "nullable|integer|exists:departments,id",
                    "category_id" => "required|integer|min:1",
                    "qty" => "required|integer|min:0",
                    // "accessory_thresholds" => "required|integer|min:1",
                    'manufacturer_id' => 'nullable|integer|min:1',
                    'location_id' => 'required|integer|min:1',
                    "supplier_id" => "nullable|integer|exists:suppliers,id",
                    "invoice_id" => "nullable|integer|exists:purchases,id",
                    'purchase_date' => 'nullable|date_format:Y-m-d',
                    'purchase_currency' => 'nullable|string|max:3',
                    'purchase_cost' => 'nullable|clean_text_only|numeric',
                    'order_number'      => 'nullable|clean_text_only|string|max:100',
                    'notes' => 'nullable|clean_text_only|string|max:2000',
                ];

                if(!empty($validation_rules_c)){
                    $validation_rules = array_merge($validation_rules,$validation_rules_c);
                }
                if(!empty($data_to_insert_c)){
                    $data_to_insert = array_merge($data_to_insert,$data_to_insert_c);
                }

                /* Data Validation */
                $validate = Validator::make($data_to_insert, $validation_rules, [
                    'company_id.required' => 'Company is required at Excel Row ' . ($key + 1) . $text_msg_until_completed_rows,
                    'name.required' => 'Accessory Name is required at Excel Row ' . ($key + 1) . $text_msg_until_completed_rows,
                    'category_id.required' => 'Category_id is required at Excel Row ' . ($key + 1) . $text_msg_until_completed_rows,
                    'qty.required' => 'QTY is required at Excel Row ' . ($key + 1) . $text_msg_until_completed_rows,
                    // 'accessory_thresholds.required' => 'Threshold Qty is required at Excel Row ' . ($key + 1) . $text_msg_until_completed_rows,
                    'location_id.required' => 'Location is required at Excel Row ' . ($key + 1) . $text_msg_until_completed_rows,
                    'notes.clean_text_only' => 'Notes at Row ' . ($key + 1) . ' contains invalid characters.',
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
                        $row['unique_tag'],
                        $row['company'],
                        $row['name'],
                        $row['department'],
                        $row['category'],
                        $row['manufacture'],
                        $row['supplier'],
                        $row['location'],
                        $row['qty'],
                        $row['threshold_qty'],
                        $row['reorder_limit'],
                        $row['threshold_alert'],
                        $row['purchase_reference'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['order_number'],
                        $row['notes'],
                        'fail',
                        $text_msg_until_completed_rows . "Error:" . $err
                    ];
                    continue;
                }
            
                $uniqueTag = trim($row['unique_tag'] ?? '');
                if ($uniqueTag !== '') {
                    $exitsCon = Accessory::where('unique_tag', $uniqueTag)->where('location_id', $data_to_insert['location_id'])->first();
                    if (!empty($exitsCon)) {
                        $exitsCon->qty += $data_to_insert['qty'];
                        $exitsCon->save();
                        $updated = true;
                        $newInst = $exitsCon;
                    } else {
                        $data_to_insert['unique_tag'] = $uniqueTag;
                        $newInst = Accessory::create($data_to_insert);
                    }
                } else {
                    $newInst = Accessory::create($data_to_insert);
                    $newInst->generateUniqueTag();
                    $row['unique_tag'] = $newInst->unique_tag;
                }

                if( count($found_custom_keys) ) {
                    foreach($found_custom_keys as $fck) {
                        $prefixed_code = CustomField::getPrefixedFieldCode($fck);
                        $newInst->{$prefixed_code} = $data_to_insert[$prefixed_code] !="" ? $data_to_insert[$prefixed_code]: null;
                    }
                }
                if (config("app.client") == "etherealmachines") {
                    Accessory::find($newInst->id)->update([
                        'batch_no' => 'AC'.$newInst->id,
                    ]);
                } else {
                    Accessory::find($newInst->id)->update([
                        'batch_no' => 'A'.$newInst->id,
                    ]);
                }
                if($newInst->save()) {
                    /* Accessory Purchase info */
                    $objAccessoryPurchase = new AccessoryPurchase();
                    $objAccessoryPurchase->batch_no = $newInst->id;
                    $objAccessoryPurchase->po_no = (string)$newInst->order_number != null ? (string)$newInst->order_number : null;
                    $objAccessoryPurchase->purchase_date = $newInst->purchase_date != null ? $newInst->purchase_date :  date('Y-m-d');
                    $objAccessoryPurchase->exp_date = $newInst->purchase_date != null ? $newInst->purchase_date :  date('Y-m-d');
                    $objAccessoryPurchase->currency  = $newInst->purchase_currency;
                    $objAccessoryPurchase->purchase_price = $newInst->purchase_cost != null ? $newInst->purchase_cost : 0.00;
                    $objAccessoryPurchase->qty = $newInst->qty != null ? $data_to_insert['qty'] : 0;
                    $objAccessoryPurchase->purchase_by = $newInst->supplier_id != null ? $newInst->supplier_id : null;
                    $objAccessoryPurchase->save();
                    $data[] = [
                        $row['unique_tag'],
                        $row['company'],
                        $row['name'],
                        $row['department'],
                        $row['category'],
                        $row['manufacture'],
                        $row['supplier'],
                        $row['location'],
                        $row['qty'],
                        $row['threshold_qty'],
                        $row['reorder_limit'],
                        $row['threshold_alert'],
                        $row['purchase_reference'],
                        $row['purchase_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['order_number'],
                        $row['notes'],
                            'success',
                            'Accessories(s) Imported Successfully.'
                        ];
                    $success++;
                    $logaction = new Actionlog();
                    $logaction->accessory_id = $newInst->id;
                    $logaction->action_type = (isset($updated) && $updated == true) ? 'Updated':'New Add';
                    $logaction->asset_type = 'accessory';
                    $logaction->user_id = Auth::user()->id;
                    $logaction->note = $newInst->notes;
                    $logaction->save();
                }
                $thresholdAlert = explode(",", $row['threshold_alert']);
                foreach($thresholdAlert as $t){
                    $getUser = User::where("username", $t)->where('activated', 1)->where('deleted_at', null)->pluck('id')->first();
                    if (!empty($getUser)) {
                        ThresholdAlertSettings::create([
                            'asset_id' => $newInst->id,
                            'asset_type' => 2,
                            'user_id' => $getUser,
                            'updated_by' => Auth::user()->id
                        ]);
                    }
                }
                $tot_insert_records++;

            }

            // if($exception_break){
            //     $removedElements = array_splice($data[0], -2);
            //     $customFields =[];
            //     $decodeData = $custom_fields_val;
            //     if(!empty($decodeData)) {
            //         foreach ($decodeData as $i) {
            //            array_push($customFields,$i);
            //         }
            //     }
            //     $dataResult[] = array_merge($data[0],$customFields);
            //     $dataArray[] = array_merge($dataResult[0],$removedElements);
            //     $data = $dataArray;
            // }
            // foreach ($data as &$row) {
            //         $removedElements = [];
            //         $removedElements = array_splice($row, -2);
            //         $customFields =[];
            //         $decodeData = $custom_fields_val;
            //         if(!empty($decodeData)) {
            //             foreach ($decodeData as $i) {
            //                array_push($customFields,$i);
            //             }
            //         }
            //         $dataResult = array_merge($row,$customFields);
            //         $makeCustomData[] = array_merge($dataResult,$removedElements);
            // }
            if(!$exception_break) {
                $return["msg"] = ((string) $tot_insert_records) . "  accessory have been imported from given excel sheet.";
                $return["status"] = "success";
                Session::flash('msg', $return);
            }

            // if(count($found_custom_keys)) {
            //     foreach($data as $key => $d) {
            //         array_splice($data[$key], -2, 0, $custom_fields_val);
            //     }
            // }

            // if(count($found_custom_keys)) {
            //     foreach($makeCustomData as $key => $d) {
            //         array_splice($makeCustomData[$key], -2, 0, $custom_fields_val);
            //     }
            // }
            $file_name = $this->request->file('import_file');
            $given_file_original_name = preg_replace('@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName());
            if (!empty($data)) {
                $i = 0;
                foreach ($data as &$row) {
                    if(!empty($customValue)){
                        foreach ($customValue as $key => $value) {
                            $d = $value['accessory_'.$i];
                            foreach ( $d as $k => $dv) {
                                foreach ($found_custom_keys as $ck) {
                                    if($ck == $k){
                                        $removedElements = array_splice($row, -2);
                                        $prefixed_code = CustomField::getPrefixedFieldCode($ck);
                                        $customFieldset = CustomFieldset::where('id', Settings::first()->accessories_custom_fieldset_id)->first();
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
                    if (isset($row[10]) && gettype($row[10]) != 'string' && $row[10] !== null) {
                        $row[10] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[10])->format('n/j/Y');
                    }
                    $i++;  
                }
                unset($row);
            }
            $defaultKkeys = array('Unique Tag','Company','Name','Department','Category','Manufacture','Supplier','Location','QTY', 'Threshold Qty','Recorder Limit','Threshold Alert','Purchase Reference','Purchase Date','Purchase Currency','Purchase Cost','Order Number','Notes');
            if(!empty($customValue) && count($found_custom_keys) > 0) {
                foreach($found_custom_keys as $d) {
                    $cf= ucwords(str_replace('_', ' ', $d));
                    $defaultKkeys[] = $cf;
                }
            }
            $succ_fail = array('Success/Fail' ,'Message');
            $keys = array_merge($defaultKkeys, $succ_fail);
            $name = 'AccessoryImportFormat_'.date('dmYHis').'.xlsx';
            $doc_path = Excel::store(new AccessoryImportStore($data, $keys), $name, 'bulk_documents');

            $log = new BulkActions();
            $log->action_type = 4;
            $log->module_id = 3;
            $log->created_at = date("Y-m-d H:i:s");
            $log->doc_path = $name;
            $log->doc_name = $given_file_original_name;
            $log->tot_success = $success;
            $log->tot_failure = $fail;
            $log->user_id = Auth::user()->id;
            $log->save();

            $return['success'] = $success;
            $return['fail'] = $fail;
            $return['fail_msgs'] = $fail_msgs;
            $this->data = $return;
        } catch(\Exception $e) {
            Log::error("AccessoryImport Error: " . $e->getMessage());
            $this->data = $return;
        }
    }

    public function model(array $row)
    {
        $invalied_key = false;
        $required_keys = ['unique_tag','company', 'name', 'department', 'category', 'manufacture', 'supplier', 'location', 'qty', 'threshold_qty', 'reorder_limit', 'threshold_alert', 'purchase_reference', 'purchase_date', 'purchase_currency', 'purchase_cost', 'order_number', 'notes'];
    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}

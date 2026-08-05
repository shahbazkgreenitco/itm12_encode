<?php

namespace App\Imports\Consumables;

use App\Helpers\Common as CommonHelper;
use App\Imports\Consumables\ConsumableImportStore;
use App\Models\Actionlog;
use App\Models\BulkActions;
use App\Models\Category;
use App\Models\ChangeManagement\Record;
use App\Models\Company;
use App\Models\Component;
use App\Models\Consumable;
use App\Models\ConsumableCustomField;
use App\Models\ConsumablePurchase;
use App\Models\CustomField;
use App\Models\CustomFieldset;
use App\Models\Department;
use App\Models\Device;
use App\Models\Lease;
use App\Models\License;
use App\Models\Location;
use App\Models\Manufacture;
use App\Models\Model;
use App\Models\Place;
use App\Models\Procurement\Unit;
use App\Models\ProjectManagement\Project;
use App\Models\Purchase;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\TaskManagement\Task;
use App\Models\ThresholdAlertSettings;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketProcureRequest;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class ConsumableImport implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
{
    use Importable, SkipsErrors;
    public $data, $request, $custom_fields_code;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->custom_fields_code = [];
        $customFieldset = CustomFieldset::where('id', Settings::first()->consumable_custom_fieldset_id)->first();
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
        try {
            $return = ["msg"=>trans('consumables.import.controller.unable_to_import_list'), "status"=>"danger"];
            $success = 0;
            $fail = 0;
            $fail_msgs = [];
            $data = [];
            $exception_break = false;
            $tot_insert_records = 0;
            $invalied_key = false;
            $currentUser = Auth::user()->id;
            $currentAuthUser = Auth::user();
            $all_companies = Company::getAllCompany();
            // $companyId = CommonHelper::getAccessibleCompanyIds();

            $required_keys = ['unique_tag','company', 'name', 'department', 'category', 'manufacture', 'location','internal_place','supplier','order_number', 'purchase_reference', 'purchase_date', 'received_date', 'expire_date', 'purchase_currency', 'purchase_cost','unit','quantity','requestable','threshold','reorder_limit','threshold_alert','notes'];
            $alertEmail = "";
            foreach ($collection as $key => $row) {
                $collected_keys_tot = 0;
                $custom_keys_tot = 0;
                $invalid_value_at_field = false;
                $row = CommonHelper::importColumnValidate($row);
                $found_custom_keys = $data_to_insert = $customValue = $data_to_insert_c = $validation_rules_c = [];
                $customFieldsError = $customFields = null;
                $alertEmail = isset($row['threshold_alert']) ? $row['threshold_alert'] : null;
                $text_msg_until_completed_rows = trans('consumables.import.controller.records_not_imported', ['count' => $key]);
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
                    } else {
                        $invalied_key = true;
                        break;
                    }
                    if(in_array($trim_k, ['purchase_date','received_date','expire_date'])) {
                        continue;
                    }
                    $value = strip_tags(trim($trim_v));
                    $row[$trim_k] = $value;

                    if( is_numeric($value) && stripos((string) $value, "e+") > 0 ) {
                        $exception_break = true;
                        $return["msg"] = trans('consumables.import.controller.invalid_record_value', ['row' => ($key + 1), 'field' => $trim_k]);
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
                    $return["msg"] = trans('consumables.import.controller.invalid_column');
                    Session::flash('msg', $return);
                    continue;
                }

                /* custom fields data capture */
                if(count($found_custom_keys)) {
                    foreach($found_custom_keys as $fck) {
                        $cv = null;
                        $prefixed_code = CustomField::getPrefixedFieldCode($fck);
                        $customFieldset = CustomFieldset::where('id', Settings::first()->consumable_custom_fieldset_id)->first();
                        if(!empty($customFieldset)) {
                            foreach ($customFieldset->fields as $f) {
                                // doing like this since for checkbox case, converting it to array, and array to string conversion error would take place.
                                if(!is_array($row[$fck])) {
                                    $this->custom_fields_code[$prefixed_code] = (string)$row[$fck];
                                }
                                if($fck == strtolower(str_replace(' ', '_', $f->name))){
                                    $formatType = CommonHelper::convertFormatToRegex($f->format);
                                    $options = json_decode($f->custom_label, true) ?? [];
                                    $inRule = 'in:' . implode(',', $options);
                                    if($f->element == "radio") {
                                        $validation_rules_c[$prefixed_code] = $f->pivot->required == 1 ? "required|{$inRule}" : "nullable|{$inRule}";
                                        $messages_c[$prefixed_code . '.required'] = trans('consumables.import.controller.required', ['field' => $f->name]);
                                        $messages_c[$prefixed_code . '.in'] = trans('consumables.import.controller.in', ['field' => $f->name]);
                                    } elseif($f->element == "checkbox") {
                                        $validation_rules_c[$prefixed_code] = $f->pivot->required == 1 ? 'required|array' : 'nullable|array';
                                        $validation_rules_c[$prefixed_code . '.*'] = $inRule;
                                        $messages_c[$prefixed_code . '.required'] = trans('consumables.import.controller.checkbox_required', ['field' => $f->name]);
                                        $messages_c[$prefixed_code . '.array'] = trans('consumables.import.controller.array', ['field' => $f->name]);
                                        $messages_c[$prefixed_code . '.min'] = trans('consumables.import.controller.min', ['field' => $f->name]);
                                        $messages_c[$prefixed_code . '.*.in'] = trans('consumables.import.controller.checkbox_in', ['field' => $f->name]);
                                    } else {
                                        $validation_rules_c[$prefixed_code] = $f->pivot->required == 1 ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                                    }
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
                                                    $customFields = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_field_at_row', ['field' => $fck, 'row' => ($key + 1)]);
                                                }
                                            }
                                        } else {
                                            if($row[$fck] != '' || $row[$fck] != null) {
                                                $cv = $row[$fck];
                                                $data_to_insert_c[$prefixed_code] = null;
                                                if($f->preDefinedOptions == 1) {
                                                    $getCustomDropDown = Location::where("name", $cv)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 2) {
                                                    $getCustomDropDown = User::where("username", $cv)->where('activated', 1)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 3) {
                                                    $getCustomDropDown = Device::where("asset_tag", $cv)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 4) {
                                                    $getCustomDropDown = Place::where("place", $cv)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 5) {
                                                    $getCustomDropDown = Manufacture::where("name", $cv)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 6) {
                                                    $getCustomDropDown = Model::where("name", $cv)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 7) {
                                                    $getCustomDropDown = Component::where("unique_tag", $cv)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 8) {
                                                    $ticketId = ltrim($cv, '#');
                                                    $getCustomDropDown = Ticket::where("id", $ticketId)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 9) {
                                                    $managementId = ltrim($cv, '#');
                                                    $getCustomDropDown = TicketProcureRequest::where("procure_tag", $managementId)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 10) {
                                                    $recordId = ltrim($cv, '#');
                                                    $getCustomDropDown = Record::where("record_tag", $recordId)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 11) {
                                                    $taskId = ltrim($cv, '#');
                                                    $getCustomDropDown = Task::where("id", $taskId)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 12) {
                                                    $getCustomDropDown = License::where("name", $cv)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 13) {
                                                    $getCustomDropDown = Project::where("name", $cv)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 14) {
                                                    $getCustomDropDown = Purchase::where("invoice_no", $cv)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 15) {
                                                    $getCustomDropDown = Supplier::where("name", $cv)->where('deleted_at', null)->first();
                                                } elseif($f->preDefinedOptions == 16) {
                                                    $getCustomDropDown = Lease::where("contract_number", $cv)->first();
                                                } elseif($f->preDefinedOptions == 17){
                                                    $getCustomDropDown = Department::where('name',$cv)->where('deleted_at', null)->first();
                                                }
                                                if($getCustomDropDown != null){
                                                    $data_to_insert_c[$prefixed_code] = strval($getCustomDropDown->id);
                                                } else {
                                                    $customFieldsError = true;
                                                    $customFields = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_field_at_row', ['field' => $fck, 'row' => ($key + 1)]);
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
                                    } elseif($f->element == 'checkbox'){
                                        $row[$fck] = explode(',', $row[$fck]);
                                        $data_to_insert_c[$prefixed_code] = $row[$fck];
                                    }elseif($f->element != 'date' && $f->element != 'datetime' && $f->element != 'time'){
                                        if($row[$fck] != '' || $row[$fck] != null) {
                                            $cv = $row[$fck];
                                            $data_to_insert_c[$prefixed_code] = $cv;
                                        }
                                    }
                                }
                            }
                        }
                        $dataCk['consumable_'.$key][$fck] = $cv;
                    }
                    $customValue[] = $dataCk;
                }

                if( $customFieldsError != null && $customFields != null ){
                    if($customFieldsError == true) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $customFields;
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                            'fail',
                            $customFields,
                        ];
                        continue;
                    }
                    $customFieldsError = $customFields = null;
                    
                }
                $dummy_tag = "";

                /* Company ID */
                $data_to_insert['company_id'] = null;
                try {
                    foreach($all_companies as $a_company) {
                        if( strtolower($row['company']) == $a_company->name ) {
                            $data_to_insert['company_id'] = $a_company->id;
                            break;
                        }
                    }

                    if($row['company'] != "" && ! $data_to_insert['company_id']) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_company', ['row' => ($key + 1)]);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_company', ['row' => ($key + 1)])
                        ];
                        continue;
                    }
                }
                catch(\Exception $e) {
                    Log::error("company import Error: " . $e->getMessage());
                }
 
                /* Consumable Name */
                $data_to_insert['name'] = (string) $row['name'];

                /* Department */
                $data_to_insert['department_id'] = null;
                if ($row['department'] != "" && ! $data_to_insert['department_id']) {
                    try {
                        $departments = Department::select('id','name as text')->where('company_id',$data_to_insert['company_id'])->orderBy('name')->get();
                        foreach($departments as $department) {
                            if(strcasecmp($row['department'], $department->text) == 0) {
                                $data_to_insert['department_id'] = $department->id;
                                break;
                            }
                        }

                        if($row['department'] != "" && !$data_to_insert['department_id']) {
                            $exception_break = true;
                            $fail++;
                            $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_department', ['row' => ($key + 1)]);
                            $data[] = [
                                $row['unique_tag'],
                                $row['company'],
                                $row['name'],
                                $row['department'],
                                $row['category'],
                                $row['manufacture'],
                                $row['location'],
                                $row['internal_place'],
                                $row['supplier'],
                                $row['order_number'],
                                $row['purchase_reference'],
                                $row['purchase_date'],
                                $row['received_date'],
                                $row['expire_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['unit'],
                                $row['quantity'],
                                $row['requestable'],
                                $row['threshold'],
                                $row['reorder_limit'],
                                $row['threshold_alert'],
                                $row['notes'],
                                'fail',
                                $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_department', ['row' => ($key + 1)])
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
                    ])->where('category_type','consumable')->first() )->id;

                    if(empty($thisCategoryId ))
                    {
                        $newCategory = new Category;
                        $newCategory->name = $row['category'];
                        $newCategory->user_id = Auth::user()->id;
                        $newCategory->category_type = 'consumable';
                        $newCategory->save();
                        $thisCategoryId = $newCategory->id;
                    }
                    $data_to_insert['category_id'] = $thisCategoryId;
                }
                
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
                    $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_manufacturer', ['row' => ($key + 1)]);
                    $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                        'fail',
                        $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_manufacturer', ['row' => ($key + 1)])
                    ];
                    continue;
                }

                /* Location*/
                $data_to_insert['location_id'] = null;
                try {
                    $try_loc = Location::where('name', 'like', $row['location'])->where('company_id', $data_to_insert['company_id'])->first();
                    $data_to_insert['location_id'] = $try_loc->id;
                } catch(\Exception $e) {
                    if(! $row['location']) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.location_required', ['row' => ($key + 1)]);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . trans('consumables.import.controller.location_required', ['row' => ($key + 1)])
                        ];
                        continue;
                    }
                }
                if(! $data_to_insert['location_id']) {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_location', ['row' => ($key + 1)]);
                    $data[] = [
                        $row['unique_tag'],
                        $row['company'],
                        $row['name'],
                        $row['department'],
                        $row['category'],
                        $row['manufacture'],
                        $row['location'],
                        $row['internal_place'],
                        $row['supplier'],
                        $row['order_number'],
                        $row['purchase_reference'],
                        $row['purchase_date'],
                        $row['received_date'],
                        $row['expire_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['unit'],
                        $row['quantity'],
                        $row['requestable'],
                        $row['threshold'],
                        $row['reorder_limit'],
                        $row['threshold_alert'],
                        $row['notes'],
                        'fail',
                        $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_location', ['row' => ($key + 1)])
                    ];
                    continue;
                }
                /* Internal Place */
                $data_to_insert['internal_place_id'] = null;
                if ($row['internal_place'] != "") {
                    try {
                        $place = Place::where('place', $row['internal_place'])->where('location_id', $data_to_insert['location_id'])->where('company_id', $data_to_insert['company_id'])->first();
                        if ($place) {
                            $data_to_insert['internal_place_id'] = $place->id;
                        } else {
                            $exception_break = true;
                            $fail++;
                            $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_internal_place', ['row' => ($key + 1)]);
                            $data[] = [
                                $row['unique_tag'],
                                $row['company'],
                                $row['name'],
                                $row['department'],
                                $row['category'],
                                $row['manufacture'],
                                $row['location'],
                                $row['internal_place'],
                                $row['supplier'],
                                $row['order_number'],
                                $row['purchase_reference'],
                                $row['purchase_date'],
                                $row['received_date'],
                                $row['expire_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['unit'],
                                $row['quantity'],
                                $row['requestable'],
                                $row['threshold'],
                                $row['reorder_limit'],
                                $row['threshold_alert'],
                                $row['notes'],
                                'fail',
                                $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_internal_place', ['row' => ($key + 1)])
                            ];
                            continue;
                        }
                    } catch(\Exception $e) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_internal_place', ['row' => ($key + 1)]);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_internal_place', ['row' => ($key + 1)])
                        ];
                        continue;
                    }
                }

                /* Supplier */
                $data_to_insert['supplier_id'] = null;
                try {
                    $try_supplier = Supplier::where('name', 'like', $row['supplier'])->first();
                    $data_to_insert['supplier_id'] = $try_supplier->id;
                } catch(\Exception $e) {
                    if($row['supplier'] != "" && !$data_to_insert['supplier_id']) {      
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_supplier', ['row' => ($key + 1)]);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_supplier', ['row' => ($key + 1)])
                        ];
                        continue;
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

                /* Purchase Reference*/
                $data_to_insert['invoice_id'] = null;
                try {
                    if($row['purchase_reference'] != "" || $row['purchase_reference'] != null){
                        $purchases = Purchase::select('id', 'invoice_no')->where('company_id', $data_to_insert['company_id'])->get();
                        foreach($purchases as $purchase) {
                            if($row['purchase_reference'] == $purchase->invoice_no ) {
                                $data_to_insert['invoice_id'] = $purchase->id;
                                break;
                            }
                        }
                    }

                    if($row['purchase_reference'] != "" && ! $data_to_insert['invoice_id']) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_purchase_ref', ['row' => ($key + 1)]);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_purchase_ref', ['row' => ($key + 1)])
                        ];
                        continue;
                    }
                }
                catch(\Exception $e) {
                    Log::error(" Purchase Reference import Error: " . $e->getMessage());
                }

                /* purchase date */
                $data_to_insert['purchase_date'] = null;
                if (!empty($row['purchase_date'])) {
                    if (gettype($row['purchase_date']) == "string" && strtotime($row['purchase_date'])) {
                        $data_to_insert['purchase_date'] = Carbon::parse($row['purchase_date'])->format('Y-m-d');
                    } elseif (gettype($row['purchase_date']) != "string") {
                        $data_to_insert['purchase_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['purchase_date'])->format('Y-m-d');
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_date_type', ['type' => 'Purchase', 'row' => ($key + 1)]);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_date_type', ['type' => 'Purchase', 'row' => ($key + 1)])
                        ];
                        continue;
                    }      
                }

                /* Received date */
                $data_to_insert['received_date'] = null;
                if (!empty($row['received_date'])) {
                    if (gettype($row['received_date']) == "string" && strtotime($row['received_date'])) {
                        $data_to_insert['received_date'] = Carbon::parse($row['received_date'])->format('Y-m-d');
                    } elseif (gettype($row['received_date']) != "string") {
                        $data_to_insert['received_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['received_date'])->format('Y-m-d');
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_date_type', ['type' => 'Received', 'row' => ($key + 1)]);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_date_type', ['type' => 'Received', 'row' => ($key + 1)])
                        ];
                        continue;
                    }
                }

                /* Expire date */
                $data_to_insert['expire_date'] = null;
                if (!empty($row['expire_date'])) {
                    if (gettype($row['expire_date']) == "string" && strtotime($row['expire_date'])) {
                        $data_to_insert['expire_date'] = Carbon::parse($row['expire_date'])->format('Y-m-d');
                    } elseif (gettype($row['expire_date']) != "string") {
                        $data_to_insert['expire_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expire_date'])->format('Y-m-d');
                    } else {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_date_type', ['type' => 'Expire', 'row' => ($key + 1)]);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_date_type', ['type' => 'Expire', 'row' => ($key + 1)])
                        ];
                        continue;
                    }
                }

                /* purchase currency */
                $data_to_insert['currency'] = null;
                try {
                    $data_to_insert["currency"] = $row["purchase_currency"] ? $row["purchase_currency"] : null;
                }
                catch(\Exception $e) {
                    Log::error("purchase_currency Error: " . $e->getMessage());
                    $data_to_insert['currency'] = null;
                }

                 /* purchase cost */
                $data_to_insert['purchase_cost'] = 0;
                if($row['purchase_cost']) {
                    $safe_purchase_cost = preg_replace("/[^0-9.]/", "", $row['purchase_cost']);
                    $data_to_insert['purchase_cost'] = $safe_purchase_cost;
                }

                /* Unit*/
                $data_to_insert['units'] = null;
                if ($row['unit'] != "") {
                    try {
                        $try_loc = Unit::where('name', 'like', $row['unit'])->first();
                        if(!empty($try_loc)) {
                            $data_to_insert['units'] = $try_loc->id;
                        } else {
                            $exception_break = true;
                            $fail++;
                            $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_unit', ['row' => ($key + 1)]);
                            $data[] = [
                                $row['unique_tag'],
                                $row['company'],
                                $row['name'],
                                $row['department'],
                                $row['category'],
                                $row['manufacture'],
                                $row['location'],
                                $row['internal_place'],
                                $row['supplier'],
                                $row['order_number'],
                                $row['purchase_reference'],
                                $row['purchase_date'],
                                $row['received_date'],
                                $row['expire_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['unit'],
                                $row['quantity'],
                                $row['requestable'],
                                $row['threshold'],
                                $row['reorder_limit'],
                                $row['threshold_alert'],
                                $row['notes'],
                                'fail',
                                $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_unit', ['row' => ($key + 1)])
                            ];
                            continue;
                        }
                    } catch(\Exception $e) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_unit', ['row' => ($key + 1)]);
                        $data[] = [
                            $row['unique_tag'],
                            $row['company'],
                            $row['name'],
                            $row['department'],
                            $row['category'],
                            $row['manufacture'],
                            $row['location'],
                            $row['internal_place'],
                            $row['supplier'],
                            $row['order_number'],
                            $row['purchase_reference'],
                            $row['purchase_date'],
                            $row['received_date'],
                            $row['expire_date'],
                            $row['purchase_currency'],
                            $row['purchase_cost'],
                            $row['unit'],
                            $row['quantity'],
                            $row['requestable'],
                            $row['threshold'],
                            $row['reorder_limit'],
                            $row['threshold_alert'],
                            $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_unit', ['row' => ($key + 1)])
                        ];
                        continue;
                    }
                }
                  
                /* Requestable*/
                $data_to_insert['requestable'] = null;
                if ($row['requestable'] != "") {
                    if ($row['requestable'] !== "1" && $row['requestable'] !== "0") {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_requestable', ['row' => ($key + 1)]);
                        $data[] = [
                                $row['unique_tag'],
                                $row['company'],
                                $row['name'],
                                $row['department'],
                                $row['category'],
                                $row['manufacture'],
                                $row['location'],
                                $row['internal_place'],
                                $row['supplier'],
                                $row['order_number'],
                                $row['purchase_reference'],
                                $row['purchase_date'],
                                $row['received_date'],
                                $row['expire_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['unit'],
                                $row['quantity'],
                                $row['requestable'],
                                $row['threshold'],
                                $row['reorder_limit'],
                                $row['threshold_alert'],
                                $row['notes'],
                            'fail',
                            $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_requestable', ['row' => ($key + 1)])
                        ];
                        continue;
                    }
                }
                /*  Qty */
                $data_to_insert['qty'] = (int) $row['quantity'];
                $data_to_insert['consumable_thresholds'] = (int) $row['threshold'];
                $data_to_insert['reorder_limits'] = (int) ($row['reorder_limit'] ?? 0);
                $data_to_insert['requestable'] = (isset($row['requestable']) && $row['requestable'] == 1) ? 1 : 0;

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
                    'unique_tag' => 'nullable|clean_text_only|max:100',
                    'company_id' => 'required|integer|min:1',
                    "department_id" => "nullable|integer|exists:departments,id",
                    "name" => "required|string|min:3|max:255",
                    "category_id" => "required|integer|min:1",
                    "qty" => "required|integer|min:0",
                    "requestable" => "nullable|integer",
                    "manufacture_id" => "nullable|integer|min:1",
                    'location_id' => 'required|integer|min:1',
                    'internal_place_id' => 'nullable|integer|min:1|exists:places,id',
                    "supplier_id" => "nullable|integer|exists:suppliers,id",
                    "invoice_id" => "nullable|integer|exists:purchases,id",
                    'purchase_date' => 'nullable|date_format:Y-m-d',
                    'received_date' => 'nullable|date_format:Y-m-d',
                    'expire_date' => 'nullable|date_format:Y-m-d|after:received_date',
                    'currency' => 'nullable|clean_text_only|string|max:3',
                    'purchase_cost' => 'nullable|numeric',
                    'order_number'      => 'nullable|string|max:100',
                    // "consumable_thresholds" => "required|integer|min:1",
                    'notes' => 'nullable|string|max:2000',
                ];

                $messages = [
                    'company_id.required' => trans('consumables.import.controller.comp_req', ['row' => ($key + 1)]),
                    'name.required' => trans('consumables.import.controller.name_req', ['row' => ($key + 1)]),
                    'name.regex' => trans('consumables.import.controller.name_format_error', ['row' => ($key + 1)]),
                    'category_id.required' => trans('consumables.import.controller.cat_req', ['row' => ($key + 1)]),
                    'qty.required' => trans('consumables.import.controller.qty_req', ['row' => ($key + 1)]),
                    'qty.integer'=>trans('consumables.import.controller.qty_int', ['row' => ($key + 1)]),
                    'consumable_thresholds.integer'=>trans('consumables.import.controller.threshold_int', ['row' => ($key + 1)]),
                    'location_id.required' => trans('consumables.import.controller.loc_req', ['row' => ($key + 1)]),
                    'requestable.integer' =>trans('consumables.import.controller.req_int', ['row' => ($key + 1)]),
                    'expire_date.after' => trans('consumables.import.controller.exp_after', ['row' => ($key + 1)]),
                ];

                if(!empty($validation_rules_c)){
                    $validation_rules = array_merge($validation_rules,$validation_rules_c);
                }

                if(!empty($messages_c)) {
                    $messages = array_merge($messages, $messages_c);
                }
                if(!empty($data_to_insert_c)){
                    $data_to_insert = array_merge($data_to_insert,$data_to_insert_c);
                }

                /* Data Validation */
                $validate = Validator::make($data_to_insert, $validation_rules, $messages);

                if($validate->fails()) {
                    $exception_break = true;
                    $v = $validate->errors()->toArray();
                    $e = array_shift($v);
                    foreach($e as $value){
                        $err = $value;
                    }
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.error_prefix') . $err;
                    $data[] = [
                        $row['unique_tag'],
                        $row['company'],
                        $row['name'],
                        $row['department'],
                        $row['category'],
                        $row['manufacture'],
                        $row['location'],
                        $row['internal_place'],
                        $row['supplier'],
                        $row['order_number'],
                        $row['purchase_reference'],
                        $row['purchase_date'],
                        $row['received_date'],
                        $row['expire_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['unit'],
                        $row['quantity'],
                        $row['requestable'],
                        $row['threshold'],
                        $row['reorder_limit'],
                        $row['threshold_alert'],
                        $row['notes'],
                        'fail',
                        $text_msg_until_completed_rows . trans('consumables.import.controller.error_prefix') . $err
                    ];
                    continue;
                }

                // removing this since the custom field values are not in the main table
                if (!empty($data_to_insert_c)) {
                    $data_to_insert = array_diff_key($data_to_insert, $data_to_insert_c);
                }

                if(!empty($row['threshold_alert'])){
                    try {
                        $thresholdAlert = explode(",", $row['threshold_alert']);
                            $invalidUserName = false;
                            foreach($thresholdAlert as $t){
                                if($invalidUserName != true) {
                                    $getUser = User::where("username", $t)->where('activated', 1)->where('deleted_at', null)->pluck('id')->first();
                                    $invalidUserName = empty($getUser) ? true : false;
                                }
                            }
                        if(empty($getUser) || $invalidUserName == true) {
                            $exception_break = true;
                            $fail++;
                            $fail_msgs[] = $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_threshold_alert', ['row' => ($key + 1)]);
                            $data[] = [
                                $row['unique_tag'],
                                $row['company'],
                                $row['name'],
                                $row['department'],
                                $row['category'],
                                $row['manufacture'],
                                $row['location'],
                                $row['internal_place'],
                                $row['supplier'],
                                $row['order_number'],
                                $row['purchase_reference'],
                                $row['purchase_date'],
                                $row['received_date'],
                                $row['expire_date'],
                                $row['purchase_currency'],
                                $row['purchase_cost'],
                                $row['unit'],
                                $row['quantity'],
                                $row['requestable'],
                                $row['threshold'],
                                $row['reorder_limit'],
                                $row['threshold_alert'],
                                $row['notes'],
                                'fail',
                                $text_msg_until_completed_rows . trans('consumables.import.controller.invalid_threshold_alert', ['row' => ($key + 1)])
                            ];
                            continue;
                        }
                    }
                    catch(\Exception $e) {
                        Log::error("Threshold Alert username import Error: " . $e->getMessage());
                    }
                }
                $received_date = $data_to_insert['received_date'] ?? null;
                $expire_date = $data_to_insert['expire_date'] ?? null;
                $exitsCon = Consumable::where('unique_tag', $row['unique_tag'])->where('location_id', $data_to_insert['location_id'])->first();

                if(!empty($exitsCon)) {
                    $newInst = $exitsCon;
                    $newInst->qty = $newInst->qty + $data_to_insert['qty'];
                    $updated = true;
                    $entry = 'update';
                } else {
                    unset($data_to_insert['received_date']);
                    unset($data_to_insert['expire_date']);
                    $newInst = Consumable::create($data_to_insert);
                    if(!empty($data_to_insert_c)){
                        foreach ($data_to_insert_c as $key => $value) {
                            if (is_array($value)) {
                                $data_to_insert_c[$key] = json_encode($value);
                            }
                        }
                        $data_to_insert_c['consumable_id'] = $newInst->id;
                        $data_to_insert_c['created_at'] = now();
                        $data_to_insert_c['updated_at'] = now();
                        ConsumableCustomField::insert($data_to_insert_c);
                    }
                    $entry = 'new';
                }

                if($newInst->save()) {
                    if (empty($row['unique_tag'])) {
                        $newInst->generateUniqueTag();
                    } else {
                        $newInst->unique_tag = $row['unique_tag'];
                    }
                    $objConsumablePurchase = new ConsumablePurchase();
                    $objConsumablePurchase->batch_no = $newInst->id;
                    $objConsumablePurchase->po_no = $newInst->order_number != null ? $newInst->order_number : null;
                    $objConsumablePurchase->purchase_date = $entry == 'new' ? ($newInst->purchase_date != null ? $newInst->purchase_date :  date('Y-m-d')) : ($data_to_insert['purchase_date'] != null ? $data_to_insert['purchase_date'] : date('Y-m-d'));
                    $objConsumablePurchase->received_date = $received_date;
                    $objConsumablePurchase->exp_date = $expire_date;
                    $objConsumablePurchase->currency  = $newInst->currency;
                    $objConsumablePurchase->purchase_price = $newInst->purchase_cost != null ? $newInst->purchase_cost : 0.00;
                    $objConsumablePurchase->qty = $newInst->qty != null ? $data_to_insert['qty'] : 0;
                    $objConsumablePurchase->purchase_by = $newInst->supplier_id != null ? $newInst->supplier_id : null;
                    $objConsumablePurchase->save();
                    $data[] = [
                        $row['unique_tag'],
                        $row['company'],
                        $row['name'],
                        $row['department'],
                        $row['category'],
                        $row['manufacture'],
                        $row['location'],
                        $row['internal_place'],
                        $row['supplier'],
                        $row['order_number'],
                        $row['purchase_reference'],
                        $row['purchase_date'],
                        $row['received_date'],
                        $row['expire_date'],
                        $row['purchase_currency'],
                        $row['purchase_cost'],
                        $row['unit'],
                        $row['quantity'],
                        $row['requestable'],
                        $row['threshold'],
                        $row['reorder_limit'],
                        $row['threshold_alert'],
                        $row['notes'],
                        'success',
                        trans('consumables.import.controller.import_success_row')
                    ];
                    $success++;
                    $newInst->save();
                    $logaction = new Actionlog();
                    $logaction->consumable_id = $newInst->id;
                    $logaction->action_type = (isset($updated) && $updated == true) ? 'Updated':'New Add';
                    $logaction->asset_type = 'consumable';
                    $logaction->user_id = Auth::user()->id;
                    $logaction->note = $newInst->notes;
                    $logaction->save();
                }

                $tot_insert_records++;
                $thresholdAlert = explode(",", $row['threshold_alert']);
                foreach($thresholdAlert as $t){
                    $getUser = User::where("username", $t)->where('activated', 1)->where('deleted_at', null)->pluck('id')->first();
                    if (!empty($getUser)) {
                            ThresholdAlertSettings::create([
                                'asset_id' => $newInst->id,
                                'asset_type' => 4,
                                'user_id' => $getUser,
                                'updated_by' => Auth::user()->id
                            ]);
                    }
                }
            }
            if(!$exception_break) {
                $return["msg"] = trans('consumables.import.controller.import_summary_success', ['count' => $tot_insert_records]);
                $return["status"] = "success";
                Session::flash('msg', $return);
            }


            // if(count($found_custom_keys)) {
            //     foreach($data as $key => $d) {
            //         array_splice($data[$key], -2, 0, $custom_fields_val);
            //     }
            // }

            $file_name = $this->request->file('import_file');
            $given_file_original_name = preg_replace('@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName());
            if (!empty($data)) {
                $i = 0;
                foreach ($data as &$row) {
                    if(!empty($customValue)){ 
                        foreach ($customValue as $key => $value) {
                            $d = $value['consumable_'.$i];
                            foreach ( $d as $k => $dv) {
                                foreach ($found_custom_keys as $ck) {
                                    if($ck == $k){
                                        $removedElements = array_splice($row, -2);
                                        $prefixed_code = CustomField::getPrefixedFieldCode($ck);
                                        $customFieldset = CustomFieldset::where('id', Settings::first()->consumable_custom_fieldset_id)->first();
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
                    if (isset($row[9]) && gettype($row[9]) != 'string' && $row[9] !== null) {
                        $row[9] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[9])->format('n/j/Y');
                    }
                    $i++;
                }
                unset($row);
            }
            $defaultKkeys = array('Unique Tag','Company', 'Name', 'Department', 'Category', 'Manufacture', 'Location', 'Internal Place','Supplier','Order Number', 'Purchase Reference', 'Purchase Date', 'Received Date', 'Expire Date', 'Purchase Currency', 'Purchase Cost','Unit','Quantity','Requestable','Threshold','Reorder Limit','Threshold Alert','Notes');
            if(!empty($customValue) && count($found_custom_keys) > 0) {
                foreach($found_custom_keys as $d) {
                    $cf= ucwords(str_replace('_', ' ', $d));
                    $defaultKkeys[] = $cf;
                }
            }
            $succ_fail = array('Success/Fail' ,'Message');
            $keys = array_merge($defaultKkeys, $succ_fail);
            $name = 'ConsumableImportFormat_'.date('dmYHis').'.xlsx';
            $doc_path = Excel::store(new ConsumableImportStore($data, $keys), $name, 'bulk_documents');

            $log = new BulkActions();
            $log->action_type = 4;
            $log->module_id = 4;
            $log->created_at = date("Y-m-d H:i:s");
            $log->doc_path = $name;
            $log->doc_name = $given_file_original_name;
            $log->doc_path = $name;
            $log->doc_name = $given_file_original_name;
            $log->tot_success = $success;
            $log->tot_failure = $fail;
            $log->user_id = Auth::user()->id;
            $log->save();
            $return['success'] = $success;
            $return['fail'] = $fail;
            $return['fail_msgs'] = $fail_msgs;
            return $this->data = $return;
        } catch(\Exception $e) {
            Log::error("ConsumableImport Error: " . $e->getMessage());
            return $this->data = $return;
        }
    }

    public function model(array $row)
    {
        $invalied_key = false;
        $required_keys = ['unique_tag','company', 'name', 'department', 'category', 'manufacture', 'location', 'internal_place','supplier','order_number', 'purchase_reference', 'purchase_date','received_date','expire_date', 'purchase_currency', 'purchase_cost','unit', 'quantity','requestable','threshold','reorder_limit','threshold_alert','notes'];
    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}

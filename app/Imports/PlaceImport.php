<?php

namespace App\Imports;

use App\Helpers\Common as CommonHelper;
use App\Models\Place;
use App\Models\Company;
use App\Models\ConfigBulkAction;
use App\Models\Location;
use App\Exports\PlaceExportStore;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

class PlaceImport implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
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
        try {
            $return = ["msg" => trans('internal_place.controller.unable_to_import_list'), "status" => "danger"];
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
            $required_keys = ['place','branch_code', 'location', 'company'];
            // $optional_keys = ['branch_code'];
            foreach ($collection as $key => $row) {
                $collected_keys_tot = 0;
                $invalid_value_at_field = false;
                $row = CommonHelper::importColumnValidate($row);

                foreach($row as $trim_k=>$trim_v)
                {
                    if(! $trim_k) {
                        continue;
                    }
                    if(in_array($trim_k, $required_keys)) {
                        $collected_keys_tot++;
                    }
                    // else if(in_array($trim_k, $optional_keys)) {
                    //     // Optional import columns are allowed.
                    // }
                    else {
                        $invalied_key = true;
                        break;
                    }
                    $value = strip_tags(trim($trim_v));
                    $row[$trim_k] = $value;

                    if( is_numeric($value) && stripos((string) $value, "e+") > 0 ) {
                        $exception_break = true;
                        $return["msg"] = trans('internal_place.controller.invalid_record_value', ['row' => ($key + 1), 'field' => $trim_k]);
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
                    $return["msg"] = trans('internal_place.controller.invalid_column');
                    Session::flash('msg', $return);
                    continue;
                }

                $text_msg_until_completed_rows = trans('internal_place.controller.records_not_imported', ['count' => $key]);
                $data_to_insert = [];

                $dummy_tag = "";
                $data_to_insert['place'] = (string) $row['place'];
                $data_to_insert['branch_code'] = isset($row['branch_code']) ? (string) $row['branch_code'] : null;

                // Branch Code
                if(!isset($data_to_insert['branch_code']) || empty($data_to_insert['branch_code'])){
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Branch Code is required at Excel Row " . ($key + 1);
                    $data[] = [
                        $row['place'],
                        $row['location'],
                        $row['company'],
                        $data_to_insert['branch_code'],
                        'fail',
                        $text_msg_until_completed_rows . trans('internal_place.controller.branch_required', ['row' => ($key + 1)])
                    ];
                    continue;
                }

                /* Company ID */
                $data_to_insert['company_id'] = null;
                if(! $row['company']) {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . "Company Name is required at Excel Row " . ($key + 1);
                    $data[] = [
                        $row['place'],
                        $row['location'],
                        $row['company'],
                        $data_to_insert['branch_code'],
                        'fail',
                        $text_msg_until_completed_rows . trans('internal_place.controller.company_required', ['row' => ($key + 1)])
                    ];
                    continue;
                }

                foreach($all_companies as $a_company) {
                    if(strtolower($row['company']) == strtolower($a_company->name)) {
                        $data_to_insert['company_id'] = $a_company->id;
                        break;
                    }
                }

                if(! $data_to_insert['company_id']) {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . trans('internal_place.controller.invalid_company', ['row' => ($key + 1)]);
                    $data[] = [
                        $row['place'],
                        $row['location'],
                        $row['company'],
                        $data_to_insert['branch_code'],
                        'fail',
                        $text_msg_until_completed_rows . trans('internal_place.controller.invalid_company', ['row' => ($key + 1)])
                    ];
                    continue;
                }

                /* Location ID */
                $data_to_insert['location_id'] = null;
                try {
                    $try_loc = Location::where('name', 'like', $row['location'])->where('company_id', $data_to_insert['company_id'])->first();
                    if(empty($try_loc) && $data_to_insert['company_id'] == 1) {
                        $try_loc = Location::where('name', 'like', $row['location'])->whereNull('company_id')->first();
                    }
                    $data_to_insert['location_id'] = $try_loc->id;
                } catch(\Exception $e) {
                    if(! $row['location']) {
                        $exception_break = true;
                        $fail++;
                        $fail_msgs[] = $text_msg_until_completed_rows . trans('internal_place.controller.location_required', ['row' => ($key + 1)]);
                        $data[] = [
                            $row['place'],
                            $row['location'],
                            $row['company'],
                            $data_to_insert['branch_code'],
                            'fail',
                            $text_msg_until_completed_rows . trans('internal_place.controller.location_required', ['row' => ($key + 1)])
                        ];
                        continue;
                    }
                }
                if(! $data_to_insert['location_id']) {
                    $exception_break = true;
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . trans('internal_place.controller.invalid_location', ['row' => ($key + 1)]);
                    $data[] = [
                        $row['place'],
                        $row['location'],
                        $row['company'],
                        $data_to_insert['branch_code'],
                        'fail',
                        $text_msg_until_completed_rows . trans('internal_place.controller.invalid_location', ['row' => ($key + 1)])
                    ];
                    continue;
                }

                $validation_rules = [
                    'place' => 'required',
                    'location_id' => 'required|integer|min:1',
                    'company_id' => 'required|integer|min:1',
                    'branch_code' => 'required|string|unique:places,branch_code'
                ];

                /* Data Validation */
                $validate = Validator::make($data_to_insert, $validation_rules, [
                    'place.unique' => trans('internal_place.controller.place_exists_row', ['place' => $data_to_insert['place'], 'row' => ($key + 1)]),
                    'location_id.required' => trans('internal_place.controller.location_required', ['row' => ($key + 1)]) . $text_msg_until_completed_rows,
                    'branch_code.regex' => trans('internal_place.controller.branch_code_format'),
                    'company_id.required' => trans('internal_place.controller.company_required', ['row' => ($key + 1)]) . $text_msg_until_completed_rows,
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
                        $row['place'],
                        $row['location'],
                        $row['company'],
                        $data_to_insert['branch_code'],
                        'fail',
                        $text_msg_until_completed_rows . trans('internal_place.controller.error_prefix') . $err
                    ];
                    continue;
                }

                $alreadyExists = Place::where('place', $data_to_insert['place'])
                    ->where('location_id', $data_to_insert['location_id'])
                    ->where('company_id', $data_to_insert['company_id'])
                    ->whereNull('deleted_at')
                    ->count();

                if($alreadyExists > 0) {
                    $fail++;
                    $fail_msgs[] = $text_msg_until_completed_rows . trans('internal_place.controller.place_exists_row', ['place' => $data_to_insert['place'], 'row' => ($key + 1)]);
                    $data[] = [
                        $row['place'],
                        $row['location'],
                        $row['company'],
                        $data_to_insert['branch_code'],
                        'fail',
                        $text_msg_until_completed_rows . trans('internal_place.controller.place_exists_row', ['place' => $data_to_insert['place'], 'row' => ($key + 1)])
                    ];
                    continue;
                }

                $newInst = Place::create($data_to_insert);
            
                if($newInst->save()) {
                    $data[] = [
                        $row['place'],
                        $row['location'],
                        $row['company'],
                        $data_to_insert['branch_code'],
                        'success',
                        trans('internal_place.controller.import_success_row')
                    ];
                    $success++;
                    $newInst->save();
                }
                $tot_insert_records++;
            }

            if (!$exception_break) {
                $return["msg"] = trans('internal_place.controller.import_summary_success', ['count' => $tot_insert_records]);
                $return["status"] = "success";
                Session::flash('msg', $return);
            }

            $defaultKkeys = array('Place','Parent Location','Company','Branch Code');
            $succ_fail = array('Success/Fail' ,'Message');
            $keys = array_merge($defaultKkeys, $succ_fail);
            $name = 'PlaceExportFormat_'.date('dmYHis').'.xlsx';
            $doc_path = Excel::store(new PlaceExportStore($data, $keys), $name, 'bulk_documents');
            
            $file_name = $this->request->file('import_file');
            $given_file_original_name = preg_replace('@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName());

            $log = new ConfigBulkAction();
            $log->action_type = 1;
            $log->module_id = 5;
            $log->created_at = date("Y-m-d H:i:s");
            $log->doc_path = $name;
            $log->doc_name = $given_file_original_name;
            $log->tot_success = $success;
            $log->tot_failure = $fail;
            $log->user_id = Auth::user()->id;
            $log->save();

            $return['fail'] = $fail;
            $return['fail_msgs'] = $fail_msgs;
            return $this->data = $return;
        } catch(\Exception $e) {
            Log::error("PlaceImport Error: " . $e->getMessage());
            return $this->data = $return;
        }
    }

    public function model(array $row)
    {
        $invalied_key = false;
        $required_keys = ['place','branch_code', 'location', 'company'];
    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}

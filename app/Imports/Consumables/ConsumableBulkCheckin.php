<?php

namespace App\Imports\Consumables;

use Illuminate\Http\Request;
use App\Models\Consumable;
use App\Models\ConsumableUser;
use App\Models\User;
use App\Models\Place;
use App\Models\Device;
use App\Models\Actionlog;
use App\Models\Label;
use App\Models\BulkActions;
use App\Models\ProjectManagement\Project;
use App\Models\Currency;
use Carbon\Carbon;
use App\Helpers\Common as CommonHelper;
use Illuminate\Support\Str;
use Validator;
use Storage;
use Auth;
use Illuminate\Validation\Rule;
use App\Rules\ValidCarbon;
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
use Log;
use DB;
use App\Models\License;
use App\Models\LicenseSeat;
use App\Imports\Consumables\ConsumableImportStore;

class ConsumableBulkCheckin implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
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
        $return = ["msg" => "Unable to checkin the given consumable list", "status" => "danger"];
        try {
            $success = 0;
            $fail = 0;
            $fail_msgs = [];
            $data = [];
            $exception_break = false;
            $tot_insert_records = 0;
            $invalied_key = false;
            $companyIds = CommonHelper::getAccessibleCompanyIds();
         
            $required_keys = ['id', 'assigned_for', 'assigned_to', 'scrap_qty', 'note'];
         
            foreach($collection as $key => $row) {
                $row = CommonHelper::importColumnValidate($row);
                if(trim($row['batch_no']) == "" || $row['batch_no'] == NULL) {
                    $fail++;
                    $fail_msgs[] = trans('content.consumables_fields.The_consumable_ID_not_found');                    
                    $data[] = [
                        $row['batch_no'],
                        $row['assigned_for'],
                        $row['assigned_to'],
                        $row['scrap_qty'],
                        $row['note'],
                        'fail', 
                        "The consumable ID is not found"
                    ];
                    continue;
                }

                $temp_arr = $row->toArray();
                if (config("app.client") == "etherealmachines") {
                    $temp_arr['id'] = (string) str_ireplace(['cns','cn'],'', $row['batch_no']);
                } else {
                    $temp_arr['id'] = (string) str_ireplace('cns','', $row['batch_no']);
                }

                $validator = Validator::make($temp_arr, [
                    'id' => 'required|exists:consumables,id',
                    'assigned_for' => 'required',
                    'assigned_to' => 'required',
                    'scrap_qty' => 'nullable|integer|min:1',
                    'note' => [
                        Rule::requiredIf(fn () => config('app.client') === 'knightfrank'),
                        'nullable',
                        'clean_text_only',
                        'max:255',
                    ],
                ], [
                    'id.required' => "The record for batch no. is missing.",
                    'id.exists' => "The record for batch no. ':input' has not found.",
                    'assigned_for.required' => "Assigned for is not found.",
                    'assigned_to.required' => "Assigned To is not found."
                ]);
                if ($validator->fails()) {
                    $fail++;
                    $messages = $validator->errors()->all();
                    foreach ($messages as $msg) {
                        $fail_msgs[] = "The record for batch no. '" . ($row['batch_no'] ?? '') . "': " . $msg;
                        $data[] = [
                            $row['batch_no'] ?? '',
                            $row['assigned_for'] ?? '',
                            $row['assigned_to'] ?? '',
                            $row['scrap_qty'] ?? '',
                            $row['note'] ?? '',
                            'fail',
                            "The record for batch no. '" . ($row['batch_no'] ?? '') . "': " . $msg
                        ];
                    }
                    continue;
                }

                $consumable = Consumable::where("id", $temp_arr['id'])->first();
                if (!in_array($consumable->company_id, $companyIds)) {
                    
                    $fail++;
                    $fail_msgs[] = trans('content.consumables_fields.permission_denied_for_consumable_checkin',['batch_no' =>  $row['batch_no']]);
                    $data[] = [
                        $row['batch_no'],
                        $row['assigned_for'],
                        $row['assigned_to'],
                        $row['scrap_qty'],
                        $row['note'],
                        'fail',
                        trans('content.consumables_fields.permission_denied_for_consumable_checkin')
                    ];
                    Log::info("Permission denied: You don't have access to checkin consumable (Batch No: " . $row['batch_no'] . ")");
                    continue;
                }
                $con_col = ConsumableUser::where("consumable_id", $temp_arr['id'])->first();
                if(empty($con_col)) {
                    $fail++;
                    $fail_msgs[] = "The record : '" . $row['batch_no'] . "' has already checked in.";
                    $data[] = [
                        $row['batch_no'],
                        $row['assigned_for'],
                        $row['assigned_to'],
                        $row['scrap_qty'],
                        $row['note'],
                        'fail', 
                        "The record : '" . $row['batch_no'] . "' has already checked in."
                    ];
                    continue;
                }
           
                $assignForMap = ['User' => 1,'Place' => 2, 'Device' => 3];
                $assign_for = $assignForMap[$row['assigned_for']] ?? null;

                if (isset($assignForMap[$row['assigned_for']])) {
                    $assign_for = $assignForMap[$row['assigned_for']];
                } else {
                    $fail++;
                    $fail_msgs[] = "Please provide the text 'User', 'Place', or 'Device' in the 'Assigned For' field";
                    $data[] = [
                        $row['batch_no'],
                        $row['assigned_for'],
                        $row['assigned_to'],
                        $row['scrap_qty'],
                        $row['note'],
                        'fail',
                        "Please provide the text 'User', 'Place', or 'Device' in the 'Assigned For' field"
                    ];
                    continue;
                }

                if($row['assigned_for'] == "User") {
                    $user = User::where('username', 'like', $row['assigned_to'])->where('company_id', $consumable->company_id)->first();
                    if (empty($user)) {
                        $fail++;
                        $fail_msgs[] = "User not found for that company '" . $row['batch_no'] . "'";
                        $data[] = [
                            $row['batch_no'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['scrap_qty'],
                            $row['note'],
                            'fail',
                            "User not found for that company '" . $row['batch_no'] . "'"
                        ];
                        continue;
                    } elseif ($user->checkoutBasicClearance()) {
                        $fail++;
                        $fail_msgs[] = "User inactive. Cannot checkin '" . $row['batch_no'] . "'";
                        $data[] = [
                            $row['batch_no'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['scrap_qty'],
                            $row['note'],
                            'fail',
                            "User inactive. Cannot checkin '" . $row['batch_no'] . "'"
                        ];
                        continue;
                    } elseif ($user->checkLastWorkingDate()) {
                        $fail++;
                        $fail_msgs[] = "User's last working date passed. Cannot checkin '" . $row['batch_no'] . "'";
                        $data[] = [
                            $row['batch_no'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['scrap_qty'],
                            $row['note'],
                            'fail',
                            "User's last working date passed. Cannot checkin '" . $row['batch_no'] . "'"
                        ];
                        continue;
                    }
                    $assigned_to = $user->id;

                } elseif ($row['assigned_for'] == "Place") {
                    $place = Place::where('place', 'like', $row['assigned_to'])->where('company_id', $consumable->company_id)->first();
                    if (empty($place)) {
                        $fail++;
                        $fail_msgs[] = "Place not found for that company '" . $row['batch_no'] . "'";
                        $data[] = [
                            $row['batch_no'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['scrap_qty'],
                            $row['note'],
                            'fail',
                            "Place not found for that company '" . $row['batch_no'] . "'"
                        ];
                        continue;
                    }
                    $assigned_to = $place->id;
                } elseif ($row['assigned_for'] == "Device") {
                    $device = Device::where('asset_tag', 'like', $row['assigned_to'])->first();
                    if (empty($device)) {
                        $fail++;
                        $fail_msgs[] = "Device not found for that company '" . $row['batch_no'] . "'";
                        $data[] = [
                            $row['batch_no'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['scrap_qty'],
                            $row['note'],
                            'fail',
                            "Device not found for that company '" . $row['batch_no'] . "'"
                        ];
                        continue;
                    } 
                    $assigned_to = $device->id;
                }

                if (!empty($assigned_to)) {
                    $con = ConsumableUser::where("consumable_id", $consumable->id)->where('assigned_for', $assign_for)->where('assigned_to', $assigned_to);
                    $conCheckin = $con->first();
                    if (!$conCheckin) {
                        $fail++;
                        $fail_msgs[] = "No active checkout found for '" . $row['batch_no'] . "' with Assigned For '" . $row['assigned_for'] . "' and  Assigned To '" . $row['assigned_to'] . "'";
                        $data[] = [
                            $row['batch_no'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['scrap_qty'],
                            $row['note'],
                            'fail',
                            "No active checkout found for '" . $row['batch_no'] . "' with  Assigned For  '" . $row['assigned_for'] . "' and Assigned To '" . $row['assigned_to'] . "'"
                        ];
                        continue;
                    }
                    $note = null;
                    if ($conCheckin) {
                        if ($assign_for == 1) {
                            $assigned_user = User::find($conCheckin->assigned_to);
                            $note = 'Revoked from ' . $assigned_user->fullName() . ' (' . $assigned_user->username . ')';
                        } elseif ($assign_for == 2) {
                            $assigned_user = Place::find($conCheckin->assigned_to);
                            $note = 'Revoked from place (' . $assigned_user->place . ')';
                        } elseif ($assign_for == 3) {
                            $assigned_user = Device::find($conCheckin->assigned_to);
                            $note = 'Revoked from Device (' . $assigned_user->serial . ')';
                        }
                    } 
                    $deleteStatus=false;
                    if(!empty($row['scrap_qty'])){
                        $allocated = Consumable::where('id', $temp_arr['id'])->withCount(['users', 'places', 'device'])->first();
                        if ($row['scrap_qty'] > ($consumable->qty - $consumable->scrap_qty) || $row['scrap_qty'] > ($allocated->users_count + $allocated->places_count + $allocated->device_count)) {
                            $fail++;
                            $fail_msgs[] = "Row " . ($key + 1) . " failed: Consumable can not be scrapped" . 
                                "( Batch No: " . $row['batch_no'] . ")";
                            
                            $data[] = [
                                $row['batch_no'],
                                $row['assigned_for'],
                                $row['assigned_to'],
                                $row['scrap_qty'],
                                $row['note'],
                                'fail',
                                "Consumable can not be scrapped for the batch_no: '" . $row['batch_no'] . "'"
                            ];

                            continue;
                        }
                        $perticular = ConsumableUser::where("consumable_id", $consumable->id)->where('assigned_for', $assign_for)->where('assigned_to', $assigned_to);
                        $perticularCount = $perticular->count();
                        if($row['scrap_qty'] < $perticularCount || $row['scrap_qty'] > $perticularCount){
                            $fail++;
                            $fail_msgs[] = "Row " . ($key + 1) . " failed: Consumable cannot be scrapped as the quantity exceeds the assigned quantity for " 
                                        . $row['assigned_to'] . " for Batch No. " . $row['batch_no'] . ".";
                            
                            $data[] = [
                                $row['batch_no'],
                                $row['assigned_for'],
                                $row['assigned_to'],
                                $row['scrap_qty'],
                                $row['note'],
                                'fail',
                                "Consumable cannot be scrapped because the quantity exceeds the quantity assigned to "  . $row['assigned_to'] . " for Batch No. " . $row['batch_no'] . ".",
                            ];

                            continue;
                        };
                        if($row['scrap_qty'] > 0 && $row['scrap_qty'] == $perticularCount){
                            $consumable->scrap_qty =  $consumable->scrap_qty + $row['scrap_qty'];
                        }
                        $perticular->pluck('id');
                        $deleteStatus = $perticular->delete();
                    } else {
                        $deleteStatus = $con->delete();
                    }

                    if (!$deleteStatus) {
                        $fail++;
                        $fail_msgs[] = "No active checkout found for '" . $row['batch_no'] . "' with Assigned For '" . $row['assigned_for'] . "' and  Assigned To '" . $row['assigned_to'] . "'";
                        $data[] = [
                            $row['batch_no'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['scrap_qty'],
                            $row['note'],
                            'fail',
                            "No active checkout found for '" . $row['batch_no'] . "' with  Assigned For  '" . $row['assigned_for'] . "' and Assigned To '" . $row['assigned_to'] . "'"
                        ];
                        continue;
                    }

                    if($consumable->save()) { 
                        $data[] = [
                            $row['batch_no'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['scrap_qty'],
                            $row['note'],
                            'success', 
                            "Chosen Consumable has checked in successfully"
                        ];
                        $success++;
                        $logaction = new Actionlog();
                        $logaction->consumable_id = $consumable->id;
                        $logaction->action_type = 'revoked from';
                        $logaction->checkedout_to = $conCheckin->assigned_to;
                        $logaction->assigned_for = $conCheckin->assigned_for;
                        $logaction->asset_type = 'consumable';
                        $logaction->user_id = Auth::user()->id;
                        $logaction->in_out_id = $conCheckin->asset_logs_id;
                        $logaction->note = $note;
                        $logaction->save();
                        //To update the in_out_id for checkout entry
                        $logEntry = Actionlog::find($logaction->in_out_id);
                        if ($logEntry) {
                            DB::table('asset_logs')->where('id', $conCheckin->asset_logs_id)->update([
                                'created_at' => $logEntry->created_at,
                                'in_out_id' => $logaction->id
                            ]);
                        }
                    }
                }
            }
            $return['fail'] = $fail;
            $return['success'] = $success;
            $return['fail_msgs'] = $fail_msgs;
            $this->data = $return;
        }
        catch(\Exception $e) {
            Log::error("consumable bulk Checkin error: " . $e->getMessage());
        }

        $file_name = $this->request->file('import_file');
        $given_file_original_name = preg_replace('@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName());

        $defaultKkeys = array('Batch No', 'Assigned For', 'Assigned To', 'Scrap Qty', 'Note');
        $succ_fail = array('Success/Fail' ,'Message');
        $keys = array_merge($defaultKkeys, $succ_fail);
        $name = 'ConsumableBulkCheckinFormat_'.date('dmYHis').'.xlsx';
        $doc_path = Excel::store(new ConsumableImportStore($data, $keys), $name, 'bulk_documents');

        $log = new BulkActions();

        $log->action_type = 2;
        $log->module_id = 4;
        $log->created_at = date("Y-m-d H:i:s");
        $log->doc_path = $name;
        $log->doc_name = $given_file_original_name;
        $log->tot_success = $success;
        $log->tot_failure = $fail;
        $log->user_id = Auth::user()->id;
        $log->save();

        $return['fail'] = $fail;
        $return['success'] = $success;
        $return['fail_msgs'] = $fail_msgs;
        $this->data = $return;
    }

    public function model(array $row)
    {
        $invalied_key = false;
        $required_keys = ['Id', 'Assigned For', 'Assigned To', 'Scrap Qty', 'Note'];

    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}

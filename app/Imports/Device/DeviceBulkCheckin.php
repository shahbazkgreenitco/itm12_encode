<?php

namespace App\Imports\Device;

use App\Models\Actionlog;
use App\Models\BulkActions;
use App\Models\Category;
use App\Models\Company;
use App\Models\Device;
use App\Models\Label;
use App\Models\Location;
use App\Models\Manufacture;
use App\Models\Model;
use App\Models\Settings;
use App\Models\User;
use App\Models\Place;
use App\Models\Currency;
use App\Models\AssetInOutReason;
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
use DB;
use Illuminate\Support\Str;
use App\Helpers\Common as CommonHelper;
use App\Models\TransferItem;

class DeviceBulkCheckin implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
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
            $non_deployed_labels = Label::getAllNonDeployedLabels();            
            $companyId = CommonHelper::getAccessibleCompanyIds();
            $required_keys = ['serial', 'status','notes','checkin_reason'];
            $reasons = AssetInOutReason::where(['action_type' => 1, 'status' => 1])->get()->pluck('id', 'name')->toArray();
         
            foreach($collection as $key => $row) {

                $data_to_insert = [];
                $data_to_insert["serial"] = $row['serial'];
                $data_to_insert['status_id'] = $row['status'];
                $data_to_insert['notes'] = $row['notes'];

                if(trim($row['serial']) == "" || $row['serial'] == NULL) {
                    
                    $fail++;
                    $fail_msgs[] = "Serial is not found";
                    $data[] = [
                        $row['serial'],
                        $row['status'],
                        $row['notes'],
                        $row['checkin_reason'],
                        'fail', 
                        'Serial is not found'
                    ];
                    continue;
                }

                $validateTag = Validator::make($row->toArray(), [
                    'serial' => 'required|exists:assets,serial',
                ]);

                if($validateTag->fails()) {
                    $fail++;
                    $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not found.";
                    $data[] = [
                        $row['serial'],
                        $row['status'],
                        $row['notes'],
                        $row['checkin_reason'],
                        'fail', 
                        "The record for serial '" . $row['serial'] . "' has not found."
                    ];
                    continue;
                }

                $validateStatus = Validator::make($row->toArray(), [
                    'status' => 'required|string|exists:status_labels,name',
                ]);

                if($validateStatus->fails()) {
                    $fail++;
                    $fail_msgs[] = "Please provide the valid status name.";
                    $data[] = [
                        $row['serial'],
                        $row['status'],
                        $row['notes'],
                        $row['checkin_reason'],
                        'fail', 
                        "Please provide the valid status name."
                    ];
                    continue;
                }

                $validateCheckinReason = Validator::make((array) $row, [
                    'checkin_reason' => [
                        Rule::requiredIf(fn () => config('app.client') === 'knightfrank' || config('app.client') === 'rolepermission'),
                        'nullable',
                        'string',
                        'exists:asset_inout_reason,name'
                    ]
                ]);

                if(in_array(config('app.client'), ['knightfrank', 'rolepermission'])){
                    if(isset($row['checkin_reason']) && $row['checkin_reason'] == "") {
                        $fail++;
                        $fail_msgs[] = "The record : '" . $row['serial'] . "' Please provide the checkin reason correctly.";
                        $data[] = [
                            $row['serial'],
                            $row['status'],
                            $row['notes'],
                            $row['checkin_reason'],
                            'fail', 
                            "The record : '" . $row['serial'] . "' Please provide the checkin reason correctly."
                        ];
                        continue;
                    }
                }
              
                $device_col = Device::where("serial", "like", $row['serial'])->whereNull("deleted_at")->limit(1)->get();
                $dev = $device_col[0];
                if(!in_array($dev->company_id, $companyId)) {
                    $fail++;
                    $fail_msgs[] = "Permission denied to checkin for another company for the serial: '" . $row['serial'] . "'";
                    $data[] = [
                        $row['serial'],
                        $row['status'],
                        $row['notes'],
                        $row['checkin_reason'],
                        'fail', 
                        "Permission denied to checkin for another company for the serial: '" . $row['serial'] . "'",
                    ];
                    continue;
                }
                $oldStatus = $dev->status_id;
                if( ! count($device_col) ) {
                    $fail++;
                    $fail_msgs[] = "The record not found for the serial: '" . $row['serial'] . "'";
                    $data[] = [
                        $row['serial'],
                        $row['status'],
                        $row['notes'],
                        $row['checkin_reason'],
                        'fail', 
                        "The record not found for the serial: '" . $row['serial'] . "'"
                    ];
                    continue;
                }

                $device = $device_col[0];

                $dev_transfer = TransferItem::where('device_id', $device->id)->where('transfer_status', 1)->first();
                if(!empty($dev_transfer)) {
                    $fail++;
                    $fail_msgs[] = "'" . $row['serial'] . "'Selected Device is Under Transfer'";
                    $data[] = [
                        $row['serial'],
                        $row['status'],
                        $row['notes'],
                        $row['checkin_reason'],
                        'fail', 
                        "'" . $row['serial'] . "'Selected Device is Under Transfer'"
                    ];
                    continue;
                }

                $validate = Validator::make((array) $row, [
                    'notes' => 'nullable|clean_text_only|string|max:2000'
                ]);

                if($validate->fails()) {
                    $fail++;
                    $fail_msgs[] = "The record : '" . $row['serial'] . "' has not able to checkin notes due to invalid values detected.";
                    $data[] = [
                        $row['serial'],
                        $row['status'],
                        $row['notes'],
                        $row['checkin_reason'],
                        'fail', 
                        "The record : '" . $row['serial'] . "' has not able to checkin notes due to invalid values detected."
                    ];
                    continue;
                }

                $assigned_for = $device->assigned_for;
                $assigned_to = $device->assigned_to;
                $last_checkout_project = $device->last_checkout_project;

                if($device->assigned_for == null) {
                    $fail++;
                    $fail_msgs[] = "This '" . $row['serial'] . "' Device already checked in. Please choose another device.";
                    $data[] = [
                        $row['serial'],
                        $row['status'],
                        $row['notes'],
                        $row['checkin_reason'],
                        'fail',
                        "This '" . $row['serial'] . "' Device already checked in. Please choose another device."
                    ];
                    continue;
                }

                if($row['status']) {
                    try {
                        if($row['status'] == "Deployed" || $row['status'] == "Sold" || $row['status'] == '') {
                            $fail++;
                            $fail_msgs[] = "Please Provide the Status name correctly";
                            $data[] = [
                                $row['serial'],
                                $row['status'],
                                $row['notes'],
                                $row['checkin_reason'],
                                'fail', 
                                "Please Provide the Status name correctly"
                            ];
                            continue;
                        }
                        else if($row['status'] != "Deployed") {
                            $thisStatusId  = Label::where('name', 'like', $row['status'])->first()->id;
                            $device->status_id = $thisStatusId;
                            $device->assigned_for = null;
                            $device->assigned_to = null;
                            $device->last_checkout = null;
                            $device->expected_checkin = null;
                            $device->accepted = null;
                        }
                    }
                    catch(\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "Please Provide the Status name correctly";
                        $data[] = [
                            $row['serial'],
                            $row['status'],
                            $row['notes'],
                            $row['checkin_reason'],
                            'fail', 
                            "Please Provide the Status name correctly"
                        ];
                        continue;
                    }
                }
               
                if($row['notes']) {
                    $device->notes = strtolower($row['notes']) != "null" ? $row['notes'] : null;
                }

				

                if($device->save()) {
                    if(in_array(config('app.client'), ["rolepermission", "knightfrank", "rashmi"])) {
                        $newStatus = $device->status_id;
                        if (!empty($oldStatus) || !empty($newStatus)) {
                            CommonHelper::updateStatusCounts($oldStatus, $newStatus, $device);
                        }
                    }
                    $data[] = [
                        $row['serial'],
                        $row['status'],
                        $row['notes'],
                        $row['checkin_reason'],
                        'success', 
                        "Chosen device checkin successfully."
                    ];
                    $success++;
                    $log = new Actionlog();
                    $log->asset_id = $device->id;
                    $log->asset_type = "hardware";
                    $log->assigned_for = $assigned_for;
                    $log->checkedout_to = $assigned_to;
                    $log->reason_id = $reasons[$row['checkin_reason']] ?? null;
                    $log->interact_id = 10;
                    $log->interact_type = "i5";
                    $log->interact_module = "m1";
                    $log->project_id = $last_checkout_project;
                    $log->user_id = Auth::user()->id;
                    $log->action_type = "Checkin";
                    $log->note = $row['notes'];
                    $log->in_out_id = $dev->chkout_log_id;
                    $log->save();

                    $logEntry = Actionlog::find($dev->chkout_log_id);
                    if ($logEntry) {
                        $originalCreatedAt = $logEntry->created_at;
                        DB::table('asset_logs')->where('id', $device->chkout_log_id)->update([
                            'created_at' => $originalCreatedAt,
                            'in_out_id' => $log->id
                        ]);
                    }

                    $device->chkin_log_id = $log->id;
                    $device->save();
                }
                else {
                    $fail++;
                    $fail_msgs[] = "The record : '" . $row['serial'] . "' not updated.";
                    $data[] = [
                        $row['serial'],
                        $row['status'],
                        $row['notes'],
                        $row['checkin_reason'],
                        'fail', 
                        "The record : '" . $row['serial'] . "' not updated."
                    ];
                }

            }

            $return['fail'] = $fail;
            $return['success'] = $success;
            $return['fail_msgs'] = $fail_msgs;
            $this->data = $return;
        }
        catch(\Exception $e) {
            Log::error("device bulk Checkin error: " . $e->getMessage());
        }

        
        $defaultKkeys = array('Serial', 'Status','Notes','Checkin Reason');
        $succ_fail = array('Success/Fail' ,'Message');
        $keys = array_merge($defaultKkeys, $succ_fail);
        $name = 'DeviceBulkCheckinFormat_'.date('dmYHis').'.xlsx';
        $doc_path = Excel::store(new DeviceImportStore($data, $keys), $name, 'bulk_documents');

        $log = new BulkActions();

        $file_name = $this->request->file('import_file');
   
        $given_file_original_name = preg_replace('@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName());

        $log->action_type = 2;
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
        $required_keys = ['serial', 'status_id','notes','checkin_reason'];

    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}

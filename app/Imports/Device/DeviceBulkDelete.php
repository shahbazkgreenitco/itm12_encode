<?php

namespace App\Imports\Device;

use App\Mail\ThreshouldNotification;
use App\Models\Actionlog;
use App\Models\BulkActions;
use App\Models\Category;
use App\Models\Company;
use App\Models\Device;
use App\Models\Label;
use App\Models\Location;
use App\Models\Model;
use App\Models\Settings;
use App\Models\User;
use App\Models\Place;
use App\Models\ThresholdSettings;
use App\Models\Threshold;
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
use DB;
use Mail;
use Validator;
use Illuminate\Support\Str;
use App\Helpers\Common as CommonHelper;
use App\Models\Device\PatchManagementGroup;
use App\Models\Device\PatchManagementGroupDevice;
use App\Models\TransferItem;

class DeviceBulkDelete implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
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
            $deployed_labels = Label::getDeployedLabel()->id;
            $sold_labels = Label::getSoldLabel()->id;
            $companyId = CommonHelper::getAccessibleCompanyIds();
            $data = [];
            $required_keys = ['serial'];
            $invalid_key = false;

            foreach($collection as $key => $row) {
                $collected_keys_tot= 0;
                $row = CommonHelper::importColumnValidate($row);
                foreach($row as $trim_k => $trim_v) {
                    if(! $trim_k) {
                        continue;
                    }
                    if(in_array($trim_k, $required_keys)) {
                        $collected_keys_tot++;
                    }
                    else {
                        $invalid_key = true;
                        break;
                    }
                }
                if($invalid_key || count($required_keys) != $collected_keys_tot) {
                    $exception_break = true;
                    $return["msg"] = trans('content.device_fields.invalid_column');
                    Session::flash('msg', $return);
                    continue;
                }
                $data_to_insert = [];
                $data_to_insert["serial"] = $row['serial'];

                if(trim($row['serial']) == "" || $row['serial'] == NULL) {
                    
                    $fail++;
                    $fail_msgs[] = trans('content.device_fields.Please_Enter_Atleast');
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        'Serial No. not found'
                    ];
                    continue;
                }

                $validateTag = Validator::make($row->toArray(), [
                    'serial' => 'required|string|exists:assets,serial',
                ]);

                if($validateTag->fails()) {
                    $fail++;
                    $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not found.";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "The record for serial '" . $row['serial'] . "' has not found."
                    ];
                    continue;
                }
               
                $device_col = Device::where("serial", "like", $row['serial'])->whereNull("deleted_at")->limit(1)->get();
                if( ! count($device_col) ) {
                    $fail++;
                    $fail_msgs[] = "The record not found for the serial: '" . $row['serial'] . "'";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "The record not found for the serial: '" . $row['serial'] . "'"
                    ];
                    continue;
                }

                $device = $device_col[0];
                if(!in_array($device->company_id, $companyId)) {
                    $fail++;
                    $fail_msgs[] = "Permission denied to delete for another company for the serial: '" . $row['serial'] . "'";
                    $data[] = [
                        $row['serial'],
                        'fail',
                        "Permission denied to delete for another company for the serial: '" . $row['serial'] . "'",
                    ];
                    continue;
                }

                $dev_transfer = TransferItem::where('device_id', $device->id)->where('transfer_status', 1)->first();
                if(!empty($dev_transfer)) {
                    $fail++;
                    $fail_msgs[] = "'" . $row['serial'] . "'Selected Device is Under Transfer'";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "'" . $row['serial'] . "'Selected Device is Under Transfer'"
                    ];
                    continue;
                }

                $thisdeviceModelId = $device->model_id;

                if($device->assigned_to != null || $device->status_id == $deployed_labels) {
                    $fail++;
                    $fail_msgs[] = "Device get checkout by User/Place. Please checkin device '" . $row['serial'] . "'";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "Device get checkout by User/Place. Please checkin device '" . $row['serial'] . "'"
                    ];
                    continue;
                }
                
                if($device->status_id == $sold_labels) {
                    $fail++;
                    $fail_msgs[] = "'" . $row['serial'] . "'Selected Device is Not in a Valid Status";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "'" . $row['serial'] . "'Selected Device is Not in a Valid Status"
                    ];
                    continue;
                }

                $record = Device::where('serial', "like", $row['serial'])->withCount(['asset_maintenance','cm_relevant_device','component','itm_network_device_track','tkt_ticket','license_seat','acc_checkout_device'])->first();
                
                if($record->asset_maintenance_count){
                    $fail++;
                    $fail_msgs[] = "This asset is given for maintenance. Please unlink them and try again !!";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "This asset is given for maintenance. Please unlink them and try again !!"
                    ];
                    continue;
                }

                if($record->cm_relevant_device_count){
                    $fail++;
                    $fail_msgs[] = "This asset is given for some changes. Please unlink them and try again !!";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "This asset is given for some changes. Please unlink them and try again !!"
                    ];
                    continue;
                }

                if($record->component_count){
                    $fail++;
                    $fail_msgs[] = "This asset is Checked out by a component. Please unlink them and try again !!";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "This asset is Checked out by a component. Please unlink them and try again !!"
                    ];
                    continue;
                }

                // if($record->itm_network_device_track_count){
                //     $fail++;
                //     $fail_msgs[] = "Some records are attached with this assets. Please unlink them and try again !!";
                //     $data[] = [
                //         $row['serial'],
                //         'fail', 
                //         "Some records are attached with this assets. Please unlink them and try again !!"
                //     ];
                //     continue;
                // }

                if($record->license_seat_count){
                    $fail++;
                    $fail_msgs[] = "This asset is Checked out by some License. Please unlink them and try again !!";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "This asset is Checked out by some License. Please unlink them and try again !!"
                    ];
                    continue;
                }
                if($record->acc_checkout_device_count){
                    $fail++;
                    $fail_msgs[] = "This asset is Checked out by some Accessory. Please unlink them and try again !!";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "This asset is Checked out by some Accessory. Please unlink them and try again !!"
                    ];
                    continue;
                }

                if($record->tkt_ticket_count){
                    $fail++;
                    $fail_msgs[] = "Some tickets are attached with this assets. Please unlink them and try again !!";
                    $data[] = [
                        $row['serial'],
                        'fail', 
                        "Some tickets are attached with this assets. Please unlink them and try again !!"
                    ];
                    continue;
                }
                
                if($device->delete()) {
                    $deviceOld = $device_col[0];
                    $oldStatus = $deviceOld->status_id;
                    if (!empty($oldStatus)) {
                        CommonHelper::updateStatusCounts($deviceOld->status_id, null, $deviceOld,'deleted');
                    }
                    $getGroup = PatchManagementGroup::get();
                    foreach ($getGroup as $key => $value) {
                        PatchManagementGroupDevice::where('group_id', $value->id)->where('device_id', $deviceOld->id)->delete();
                        $count = PatchManagementGroupDevice::where('group_id', $value->id)->count();
                        if ($count == 0) {
                            $value->delete();
                        }
                    }
                    Actionlog::deviceDeleted($device, Auth::user()->id);
                    if(in_array(config('app.client'), ["rolepermission", "knightfrank", "rashmi"])) {
                        $deviceOld = $device_col[0];
                        $oldStatus = $deviceOld->status_id;
                        if (!empty($oldStatus)) {
                            CommonHelper::updateStatusCounts($deviceOld->status_id, null, $deviceOld, 'deleted');
                        }
                    }
                    $this->notifyCategoryThresould($thisdeviceModelId);
                    $data[] = [
                        $row['serial'],
                        'success', 
                        "Chosen device deleted successfully."
                    ];
                    $success++;
                }
                else {
                    $fail++;
                    $fail_msgs[] = "The record : '" . $row['serial'] . "' not updated.";
                    $data[] = [
                        $row['serial'],
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
            Log::error("device bulk delete error: " . $e->getMessage());
        }

        $defaultKkeys = array('Serial');
        $succ_fail = array('Success/Fail' ,'Message');
        $keys = array_merge($defaultKkeys, $succ_fail);
        $name = 'DeviceBulkDeleteFormat_'.date('dmYHis').'.xlsx';
        $doc_path = Excel::store(new DeviceImportStore($data, $keys), $name, 'bulk_documents');

        $log = new BulkActions();

        $file_name = $this->request->file('import_file');
   
        $given_file_original_name = preg_replace('@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName());

        $log->action_type = 3;
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

    public function notifyCategoryThresould($model_id) {//cat 7 data card
        if(Settings::first()->alerts_enabled == 1){
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }

        $alertmail = null;
        $ThresholdSettings = ThresholdSettings::first();
        $thresholdcat   = Model::find($model_id)->category_id;
        $thresouldDtl   = Threshold::where('cat_id','=',$thresholdcat)->first();
        if(empty($thresouldDtl))
            return;
        if( $ThresholdSettings->threshold_enabled && $ThresholdSettings->alerts_enabled && $thresouldDtl->alerts_enabled ){
            if($ThresholdSettings->send_alerts == 0){
                if(Settings::first()->alerts_enabled == 1)
                $alertmail = CommonHelper::getGlobalAlertEmail();
            }
            if($ThresholdSettings->send_alerts == 1){
                $alertmail = $ThresholdSettings->email;
            }
        }
        if(!empty($alertmail) && $thresouldDtl->threshold > 0 ) {
            $catDetail = Category::find($thresholdcat);
            $db  = DB::table('categories as cat');
            $db->leftJoin('models as mdl', 'cat.id', '=', 'mdl.category_id');
            $db->leftJoin('assets as device', 'mdl.id', '=', 'device.model_id');
            $db->join('status_labels as lbl', function($q) {
                $q->on('lbl.id', '=', 'device.status_id');
                $q->where('lbl.deployable', '=', 1);
                $q->where('lbl.archived', '=', 0);
            });
            $db->addSelect(DB::raw('count(device.id) as device_count'));
            $db->whereNull("device.assigned_to");

            $db->whereNull("device.deleted_at");
            $db->where('cat.id','=',$thresholdcat);
            $deployableCatDeviceCount = $db->count();

            if( !empty($thresouldDtl ))
            {// threshould value check
                if ( $deployableCatDeviceCount < $thresouldDtl->threshold ){
                    if(config('mail.service_enabled')) {
                        Mail::to($alertmail)->cc($alertnotify)->send(new ThreshouldNotification ($catDetail,$thresouldDtl->threshold,$deployableCatDeviceCount) );
                        $thresouldDtl->notify_count = $thresouldDtl->notify_count + 1 ;
                        $thresouldDtl->last_notified_date = date('Y-m-d');
                    }
                    $thresouldDtl->save();
                }
            }

        }

    }

    public function model(array $row)
    {
        $invalid_key = false;
        $required_keys = ['Serial'];

    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}

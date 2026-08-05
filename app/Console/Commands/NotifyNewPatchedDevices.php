<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\NetworkInventory\Product;
use App\Models\PatchManagement\PatchDetail;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers\Common as CommonHelper;
use App\Models\Setting;
use Mail;
Use DB;
use App\Imports\NewPatchDeviceImportStore;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\Asset\NewPatchDetectedDeviceSendEmailNotification;

class NotifyNewPatchedDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:notify_new_patche_devices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify about newly detected patched devices and mark them notified';
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
        if(!config("services.patch_management.enabled") || !config('mail.service_enabled')){
            return;
        }
        $settings = Setting::getSettings();
        $alertNotify = [];
        if ($settings->alerts_enabled == 1) {
            $emails = CommonHelper::getGlobalAlertEmail(); 
            foreach ($emails as $email) {
                array_push($alertNotify, $email);
            }
        }
        if(empty($alertNotify)){
            return;
        }
        $now = new Carbon(config('app.timezone'));
        $patchLicenseDevice = DB::table('itm_network_inventory_basic as basic')
        ->join('assets', function($join) {
            $join->on('basic.device_id', '=', 'assets.id')
                ->whereNull('assets.deleted_at');
        })
        ->join('itm_network_inventory_products as p', 'basic.id', 'p.basic_id')
        ->where('p.Caption','ITM Agent')
        ->where('assets.company_id', 1)
        ->whereNull('basic.is_dupe')
        ->whereNull('basic.deleted_at')
        ->select(
            'basic.id',
            'basic.device_id',
        )->get();
        $data = [];
        foreach ($patchLicenseDevice as $key => $value) {
            $patch = PatchDetail::leftJoin('assets as a','a.id','patch_details.device_id')->where('patch_details.device_id', $value->device_id)->whereDate('patch_details.created_at', $now)
            ->select('patch_details.name as patch_name','a.serial','a.asset_tag','patch_details.version', 'a.name as asset_name',
            DB::raw('case when patch_details.patch_type_update = 3 THEN "Software" when patch_details.patch_type_update = 2 THEN "Hardware" else "OS" end as patch_type_update'),
            DB::raw('case when patch_details.severity = 1 then "Critical" when patch_details.severity = 2 then "High" when patch_details.severity = 3 then "Medium" when patch_details.severity = 4 then "Low" else "Unrate" end as patch_severity'))->get();
            foreach($patch as $row) {
                $data[] = [
                    "Device Name" => $row->asset_name,
                    "Device Tag" => $row->asset_tag,
                    "Device Serial" => $row->serial,
                    "Patch Name" => $row->patch_name,
                    "Patch Version" => $row->version,
                    "Patch Severity" => $row->patch_severity,
                    "Patch Type" => $row->patch_type_update,
                ];
            }
        }
                        
        $file_name = 'NewPatchDetectedDevices_' . $now->format('dmY').'.xlsx';
        $keys = array("Device Name","Device Tag","Device Serial","Patch Name","Patch Version","Patch Severity","Patch Type");
        $doc_path = Excel::store(new NewPatchDeviceImportStore($data, $keys), $file_name, 'new_patch_detected_devices');
        Mail::to($alertNotify)->queue(new NewPatchDetectedDeviceSendEmailNotification(count($data), $file_name));
    }
}
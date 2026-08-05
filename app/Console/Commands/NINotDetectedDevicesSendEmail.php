<?php

namespace App\Console\Commands;

use App\Mail\Asset\NotDetectedDeviceSendEmailNotification;
use App\Models\Device\DeviceSetting;
use App\Models\NetworkInventory\Basic;
use App\Imports\DeviceImportStore;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\Common as CommonHelper;
use App\Models\Settings;
use App\Models\User;
use DB;
use Carbon\Carbon;
use Auth;
use Mail;
use Log;
use Illuminate\Console\Command;

class NINotDetectedDevicesSendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:notDetectedDevicesSendEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Not detected by Agent from 1-2 days then send email notification';

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
        /*if(!config('mail.service_enabled')) {
            return;
        }*/

        $now = new Carbon(config('app.timezone'));
        $settings = Settings::getSettings();
        $deviceSetting = DeviceSetting::getDeviceSettings();
        if(empty($deviceSetting) || $deviceSetting->high_priority_ni_not_detected == 0) {
            return;
        }
        $allEmails = [];
        if($deviceSetting->ni_not_detected_type != null && $deviceSetting->ni_not_detected_type == 1) { // Role Based
            if($deviceSetting->ni_not_detected_value != null) {
                $roleIds = explode(',',$deviceSetting->ni_not_detected_value);
                if (!is_array($roleIds)) {
                    $roleIds = [];
                }
                $allEmails = User::whereHas('roles', function ($query) use ($roleIds) {
                    $query->whereIn('id', $roleIds);
                })->whereNotNull('email')->pluck('email')->toArray();
            } else {
                return;
            }
        } else if($deviceSetting->ni_not_detected_type != null && $deviceSetting->ni_not_detected_type == 2) { // Manually Entered Users mail
            if($deviceSetting->ni_not_detected_email != null) {
                $allEmails = explode(',', $deviceSetting->ni_not_detected_email);
            } else {
                return;
            }
        } else if($deviceSetting->ni_not_detected_type != null && $deviceSetting->ni_not_detected_type == 3) { // DeviceRead Permission users
            $allEmails = User::permission('DeviceRead')->where('activated', 1)->whereNotNull('email')->pluck('email')->toArray();
        } else {
            return;
        }

        if ($settings->alerts_enabled && !empty(CommonHelper::getGlobalAlertEmail())) {
            $allEmails = CommonHelper::getGlobalAlertEmail();
            $allEmails = array_unique($allEmails);
        }
        $this->info(json_encode($allEmails));

        $daysAgo = Carbon::now()->subDays($deviceSetting->ni_not_detected)->toDateString();
        $devices = Basic::from('itm_network_inventory_basic as b')
            ->select('b.ComputerName', 'assets.asset_tag', 'b.BIOSSerialNumber as SerialNumber', 'b.ComputerManufacturer', 'b.ComputerModel', 'b.OSCaption', 'b.IPv4', 'b.ActiveMACAddress', 'b.ProcessorName', 'b.OSArchitecture')
            ->leftJoin('assets', 'b.device_id', 'assets.id')
            ->whereNull('b.is_dupe')
            ->where('assets.high_pririty', 1)
            ->whereNull('assets.deleted_at')
            ->whereDate('b.updated_at', '<=', $daysAgo);

        if($devices->count() > 0) {
            foreach ($allEmails as $email) {
                if($settings->location_config == 1) {
                    if($email != null && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $userObj = User::where('email', $email)->first();
                        if(!empty($userObj)) {
                            $userLocation = explode(',', (!empty($userObj) ? $userObj->permitted_locations : []));
                            $notDetectedDevices = $devices->whereIn('assets.rtd_location_id', $userLocation)->get();
                            if(count($notDetectedDevices) > 0) {
                                // $this->info($email. "_". count($notDetectedDevices));
                                //create excel document to send users
                                $tot = count($notDetectedDevices);
                                $file_name = $userFullname = "";
                                $data = [];
                                foreach($notDetectedDevices as $row) {
                                    $data[] = [
                                        "Device Name" => $row->ComputerName,
                                        "Device Tag" => $row->asset_tag,
                                        "SerialNumber" => $row->SerialNumber,
                                        "ComputerManufacturer" => $row->ComputerManufacturer,
                                        "ComputerModel" => $row->ComputerModel,
                                        "OS" => $row->OSCaption,
                                        "IP" => $row->IPv4,
                                        "MAC" => $row->ActiveMACAddress,
                                        "ProcessorName" => $row->ProcessorName,
                                        "OSArchitecture" => $row->OSArchitecture,
                                    ];
                                }

                                $file_name = 'NotDetectedDevices_' . $now->format('dmY')."_".$userObj->id.'.xlsx';
                                $keys = array("Device Name","Device Tag","SerialNumber","ComputerManufacturer","ComputerModel","OS","IP","MAC Address","ProcessorName","OSArchitecture");
                                $doc_path = Excel::store(new DeviceImportStore($data, $keys), $file_name, 'not_detected_devices');
                                $this->info($email);
                                $userObj = User::where('email', $email)->first();
                                $userFullname = (!empty($userObj)) ? $userObj->getGuranteedNameText(true) : $email;
                                Mail::to($email)->queue(new NotDetectedDeviceSendEmailNotification($tot, $file_name, $userFullname));
                            }
                        }
                    }
                } else {
                    $notDetectedDevices = $devices->get();

                    //create excel document to send users
                    $tot = count($notDetectedDevices);
                    $file_name = $userFullname = "";
                    $data = [];
                    foreach($notDetectedDevices as $row) {
                        $data[] = [
                            "Device Name" => $row->ComputerName,
                            "Device Tag" => $row->asset_tag,
                            "SerialNumber" => $row->SerialNumber,
                            "ComputerManufacturer" => $row->ComputerManufacturer,
                            "ComputerModel" => $row->ComputerModel,
                            "OS" => $row->OSCaption,
                            "IP" => $row->IPv4,
                            "MAC" => $row->ActiveMACAddress,
                            "ProcessorName" => $row->ProcessorName,
                            "OSArchitecture" => $row->OSArchitecture,
                        ];
                    }

                    $file_name = 'NotDetectedDevices_' . $now->format('dmY').'.xlsx';
                    $keys = array("Device Name","Device Tag","SerialNumber","ComputerManufacturer","ComputerModel","OS","IP","MAC Address","ProcessorName","OSArchitecture");
                    $doc_path = Excel::store(new DeviceImportStore($data, $keys), $file_name, 'not_detected_devices');
                    $this->info($email);
                    $userObj = User::where('email', $email)->first();
                    $userFullname = (!empty($userObj)) ? $userObj->getGuranteedNameText(true) : $email;
                    Mail::to($email)->queue(new NotDetectedDeviceSendEmailNotification($tot, $file_name, $userFullname));
                }
            }
        } else {
            Log::info("NINotDetectedDevicesSendEmail: No data found");
        }
    }
}

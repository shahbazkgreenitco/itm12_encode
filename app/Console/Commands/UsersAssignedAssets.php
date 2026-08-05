<?php

namespace App\Console\Commands;
use App\Mail\Asset\UsersAssignedAssetsNotification;
use App\Models\Device\DeviceSetting;
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

class UsersAssignedAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:usersAssignedAssets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send notification to all users if User have more than 1 assets';
    //  If location & dept. config enable then email triggered to location & dept. access user only
    // If User have DeviceRead then only mail will be trigger
    // if external user & location & dept. config enabled then mail not triggered to external user
    // if location & dept. config enable then 1st priority is location & dept. wise device count then check for "number_of_devices" on Configuration

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
        $now = new Carbon(config('app.timezone'));
        $settings = Settings::getSettings();
        $deviceSetting = DeviceSetting::getDeviceSettings();
        if(empty($deviceSetting) || $deviceSetting->send_reminder == 0) {
            return;
        }
        $allEmails = [];
        if($deviceSetting->send_reminder_users_type != null && $deviceSetting->send_reminder_users_type == 1) { // Role Based
            if($deviceSetting->send_reminder_users_value != null) {
                $roleIds = explode(',',$deviceSetting->send_reminder_users_value);
                if (!is_array($roleIds)) {
                    $roleIds = [];
                }
                $allEmails = User::whereHas('roles', function ($query) use ($roleIds) {
                    $query->whereIn('id', $roleIds);
                })->whereNotNull('email')->pluck('email')->toArray();
            } else {
                return;
            }
        } else if($deviceSetting->send_reminder_users_type != null && $deviceSetting->send_reminder_users_type == 2) { // Manually Entered Users mail
            if($deviceSetting->send_reminder_users_email != null) {
                $allEmails = explode(',', $deviceSetting->send_reminder_users_email);
            } else {
                return;
            }
        } else if($deviceSetting->send_reminder_users_type != null && $deviceSetting->send_reminder_users_type == 3) { // DeviceRead Permission users
            $allEmails = User::permission('DeviceRead')->where('activated', 1)->whereNotNull('email')->pluck('email')->toArray();
        } else {
            return;
        }
        if ($settings->alerts_enabled && !empty(CommonHelper::getGlobalAlertEmail())) {
            $allEmails = CommonHelper::getGlobalAlertEmail();
            $allEmails = array_unique($allEmails);
        }
        if(isset($allEmails)) {
            foreach ($allEmails as $email) {
                $devices = DB::table("assets as a")
                ->leftJoin('asset_logs as al', 'al.id', '=', 'a.chkout_log_id')
                ->leftJoin('models as m', 'm.id', '=', 'a.model_id')
                ->leftJoin('manufacturers as mnf', 'mnf.id', '=', 'm.manufacturer_id')
                ->leftJoin('projects as pr', 'pr.id', '=', 'a.last_checkout_project')
                ->leftJoin('users as u', 'u.id', '=', 'a.assigned_to')
                ->leftJoin('status_labels as lbl', 'lbl.id', '=', 'a.status_id')
                ->leftJoin('places as p', function($q) {
                    $q->on('p.id', '=', 'a.assigned_to');
                    $q->where('a.assigned_for', '=', '2');
                })
                ->leftJoin('locations as ploc', 'ploc.id', '=', 'p.location_id')
                ->select('a.id', 'a.asset_tag', 'a.name', 'm.name as mdl_name', 'mnf.name as mnf_name', 'a.serial', 'a.model_id as model_id', 'mnf.id as manufacturer_id', 'pr.name as project_name','al.id as assets_log_id','al.access_code as assets_access_code','a.accepted','a.rtd_location_id')
                ->addSelect(DB::raw('case when a.assigned_for = 2 then "Place" when a.assigned_for = 1 then "User" else "" end as assigned_to'))
                ->addSelect(DB::raw('case when a.assigned_for = 2 then concat(p.place, " - ", ploc.name) when a.assigned_for = 1 then concat(u.first_name, " ", u.last_name) else "" end as full_name'))
                ->addSelect(DB::raw('case when a.status_id = 1 and a.deleted_at is null and a.assigned_to is null then lbl.name when lbl.deployed = 1 then lbl.name else lbl.name end as lbl_name'))
                ->addSelect(DB::raw('case when a.last_checkout is not null then DATE_FORMAT(a.last_checkout, "%d %b %Y") else null end as last_checkout_format'))
                ->where('a.assigned_for', 1)
                ->whereNull('a.deleted_at');
                    if ($settings->location_config == 1 || $settings->department_config == 1) {
                        if ($email != null && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $userObj = User::where('email', $email)->first();
                            if (!empty($userObj)) {
                                if ($settings->location_config == 1) {
                                    $userLocation = is_string($userObj->permitted_locations) ? explode(',', $userObj->permitted_locations) : [];
                                    if (empty($userLocation)) {
                                        $assignedDevices = $devices->where('a.rtd_location_id', '=', 0);
                                    } else {
                                        $assignedDevices = $devices->whereIn('a.rtd_location_id', $userLocation);
                                    }
                                }
                                if ($settings->department_config == 1) {
                                    $userDepartment = is_string($userObj->asset_departments_id) ? explode(',', $userObj->asset_departments_id) : [];
                                    if (empty($userDepartment)) {
                                        $assignedDevices = $devices->where('a.department_id', '=', 0);
                                    } else {
                                        $assignedDevices = $devices->whereIn('a.department_id', $userDepartment);
                                    }
                                }
                                $assignedDevices = $devices->get();
                            } else {
                                $assignedDevices = $devices->get();
                            }
                        }
                    } else {
                        $assignedDevices = $devices->get();
                    }
                $countByFullName = [];
                foreach ($assignedDevices as $device) {
                    $fullName = $device->full_name;
                    if (isset($countByFullName[$fullName])) {
                        $countByFullName[$fullName]++;
                    } else {
                        $countByFullName[$fullName] = 1;
                    }
                }
                    
                $data = [];
                foreach($assignedDevices as $row) {
                    if (isset($countByFullName[$row->full_name]) && $countByFullName[$row->full_name] >= $deviceSetting->number_of_devices) {
                        $data[] = [
                            "Device Name" => $row->name,
                            "Device Tag" => $row->asset_tag,
                            "SerialNumber" => $row->serial,
                            "Model" => $row->mdl_name,
                            "Manufacture" => $row->mnf_name,
                            "Assigned To" => $row->assigned_to,
                            "Assigned User/Place" => $row->full_name,
                            "Checkout Date"=> $row->last_checkout_format
                        ];
                    }  
                }
                $tot = count($data);
                $file_name = $userFullname = "";
                if(!empty($data)){
                    $file_name = 'AssignedDevices_' . $now->format('dmY')."_".'.xlsx';
                    $keys = array("Device Name","Device Tag","SerialNumber","Model","Manufacture","Assigned To","Assigned User/Place","Checkout Date");
                    $doc_path = Excel::store(new DeviceImportStore($data, $keys), $file_name, 'assigned_devices');
                    $userObj = User::where('email', $email)->first();
                    $userFullname = (!empty($userObj)) ? $userObj->getGuranteedNameText(true) : $email;
                    Mail::to($email)->queue(new UsersAssignedAssetsNotification($tot, $file_name, $userFullname));
                }
                
            }
        } else {
            Log::info("AssignedDevicesSendEmail: No data found");
        }
    }
}

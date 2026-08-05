<?php

namespace App\Console\Commands;

use App\Mail\ComponentExpectedReturnLastThreeDaysMail;
use Illuminate\Console\Command;
use DB;
use Mail;
use Carbon\Carbon;
use App\Mail\DeviceExpectedReturnLastThreeDaysMail;
use App\Mail\AccessoryExpectedReturnLastThreeDaysMail;
use App\Mail\LicenseExpectedReturnLastThreeDaysMail;
use App\Helpers\Common as CommonHelper;
use App\Models\Settings;
use App\Models\Device;
use App\Models\User;
use App\Models\AssetAllocationType;
use App\Models\AccessoryUser;
use App\Models\LicenseSeat;
use App\Models\Component;
use Log;
use Auth;

class DeviceExpectedReturnLastThreeDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:deviceExpectedReturnDateReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send the device expected return date expire list to client on periodically';

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
       
        $start = new Carbon(config('app.timezone'));
        $end = Carbon::now()->addDays(2)->format('Y-m-d');
        $today = Carbon::now()->format('Y-m-d');

        $devices = Device::select('asset_tag','expected_checkin','status_id','assigned_to','users.email as email','serial')
            ->leftjoin('users', 'users.id', 'assets.assigned_to')
            ->whereBetween('expected_checkin', [$today , $end])
            ->orWhereDate('expected_checkin', '<', $today)
            ->where('status_id',6)->get()->toArray();
        $alertnotify = [];
        if (Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        $this->info("Device start");
        foreach($devices as $device) {
            $user = User::find($device['assigned_to']);
            if (config('mail.service_enabled') && $user && !empty($user) && $user['email'] != null && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                try {
                    Log::info("deviceExpectReturnReminder id:" . $user['email']);
                    Mail::to($user['email'])->cc($alertnotify)->queue(new DeviceExpectedReturnLastThreeDaysMail($device));
                } catch (\Exception $ex) {
                    Log::error("deviceExpectReturnReminder Mail:" . $ex->getMessage());
                }
            }
        }
        $this->info("Device end");

        // Code for Accessory
        if (config("app.client") == "etherealmachines"){
            $accessories = AccessoryUser::select('name','expected_checkin','assigned_to',DB::raw('concat("AC", accessories.id) as acc_batch_no'))
                ->leftjoin('accessories', 'accessories.id', 'accessories_users.accessory_id')
                ->leftjoin('users', 'users.id', 'accessories_users.assigned_to')
                ->whereBetween('expected_checkin', [$today , $end])->get()->toArray();
        } else {
            $accessories = AccessoryUser::select('name','expected_checkin','assigned_to',DB::raw('concat("A", accessories.id) as acc_batch_no'))
                ->leftjoin('accessories', 'accessories.id', 'accessories_users.accessory_id')
                ->leftjoin('users', 'users.id', 'accessories_users.assigned_to')
                ->whereBetween('expected_checkin', [$today , $end])->get()->toArray();
        }
        $this->info("Accessory start");
        foreach($accessories as $accessory) {
            $user = User::find($accessory['assigned_to']);
            if (config('mail.service_enabled') && $user && !empty($user) && $user['email'] != null && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                try {
                    Log::info("accessoryExpectReturnReminder:" . $user['email']);
                    Mail::to($user['email'])->cc($alertnotify)->queue(new AccessoryExpectedReturnLastThreeDaysMail($accessory));
                } catch (\Exception $ex) {
                    Log::error("accessoryExpectReturnReminder Mail: " . $user['email'] . ' - '. $ex->getMessage());
                }
            }
        }
        $this->info("Accessory end");

        // Code for Component
        $components = Component::select('name','expected_checkin_at','updator_id','unique_tag')
            ->leftjoin('users', 'users.id', 'components.updator_id')
            ->whereBetween('expected_checkin_at', [$today , $end])->get()->toArray();
        $this->info("Component start");
        foreach($components as $component) {
            $user = User::find($component['updator_id']);
            if (config('mail.service_enabled') && $user && !empty($user) && $user['email'] != null && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                try {
                    Log::info("ComponentExpectReturnReminder:" . $user['email']);
                    Mail::to($user['email'])->cc($alertnotify)->queue(new ComponentExpectedReturnLastThreeDaysMail($component));
                } catch (\Exception $ex) {
                    Log::error("ComponentExpectReturnReminder Mail:" . $user['email'] . ' - '. $ex->getMessage());
                }
            }
        }
        $this->info("Component end");

        // Code for Licence
        $licenses = LicenseSeat::select('name','expected_checkin','assigned_to',DB::raw('case when licenses.id is not null then concat_ws("LIC",licenses.id) else "" end as batch_no'))
            ->leftjoin('licenses', 'licenses.id', 'license_seats.license_id')
            ->leftjoin('users', 'users.id', 'license_seats.assigned_to')
            ->whereBetween('expected_checkin', [$today , $end])->get()->toArray();
        $this->info("Licence start");
        foreach($licenses as $license) {
            $user = User::find($license['assigned_to']);
            if (config('mail.service_enabled') && $user && !empty($user) && $user['email'] != null && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                try {
                    Log::info("licenceExpectReturnReminder: " . $user['email']);
                    Mail::to($user['email'])->cc($alertnotify)->queue(new LicenseExpectedReturnLastThreeDaysMail($license));
                } catch (\Exception $ex) {
                    Log::error("licenceExpectReturnReminder Mail:" . $user['email'] . ' - '. $ex->getMessage());
                }
            }
        }
        $this->info("Licence end");
    }
}

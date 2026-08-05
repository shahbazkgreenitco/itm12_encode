<?php

namespace App\Console\Commands;

use App\Mail\DeviceCheckoutAcceptRemainder;
use Illuminate\Console\Command;
use App\Models\Device;
use App\Models\Label;
use Carbon\Carbon;
use App\Models\Settings;
use Mail;
use Log;

class SendDeviceCheckoutReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendCheckoutAcceptReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send reminder all who not confirm yet';

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

    public function handle() {
        try {
            $settings = Settings::getSettings();

            if(!config('mail.service_enabled')) {
                return;
            }

            $now = new Carbon(config('app.timezone'));
            $end = $now->copy()->addMonths(1);
            $send_reminder = 0;
            $getDevices = Device::where('status_id', '=', Label::getDeployedLabel()->id)->where('assigned_for', '=', 1)->whereNotNull('chkout_log_id')
            ->where(function($q) {
                $q->whereNull('accepted')->orWhere('accepted', '!=', 'accepted');
            })
            ->where(function($q) use($now) {
                $q->whereNull('accept_link_send_at')->orWhere('accept_link_send_at', '<', $now->format('Y-m-d H:i:s'));
            });
            $devices = $getDevices->orderBy('last_checkout')->get();

            foreach($devices as $device) {
                $log = $device->chkoutLog;
                $user = $device->user;
                if( $log && $user && $user->email && $user->activated == 1) {
                    Mail::to($user->email)->queue(new DeviceCheckoutAcceptRemainder($device, $log, $user));
                    $send_reminder = $send_reminder + 1;
                    $device->accept_link_send_at = Carbon::now(config('app.timezone'));
                    $device->save();
                }
            }
            Log::info("SendDeviceCheckoutReminder Cron success Count: " . $send_reminder);
            
        } catch(\Exception $e) {
            Log::error("SendDeviceCheckoutReminder Cron error: " . $e->getMessage());
        }
        $this->info("success");
    }
}
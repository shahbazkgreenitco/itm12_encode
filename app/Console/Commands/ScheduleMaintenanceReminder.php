<?php

namespace App\Console\Commands;

use Exception;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceCron;
use App\Mail\ScheduleMaintenance\PlanReminderNotification;
use Mail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScheduleMaintenanceReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ScheduleMaintenanceReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sending email for upcoming schedule maintenance';

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
        try {
            $plans = ScheduleMaintenanceCron::whereDate('schedule_date', Carbon::now()->addDay()->format('Y-m-d 00:00:00'))->whereNull('deleted_at')->whereIn('status', [1,6,7])->get();
            if ($plans->count()) {
                foreach ($plans as $plan) {
                    if ($plan->supplier_id != null) {
                        $user = $plan->supplier->user;
                    } else if ($plan->handler_id != null) {
                        $user = $plan->handler;
                    }
                    if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($user->email)->queue(new PlanReminderNotification($user, $plan));
                    }
                    if (!empty($plan->plan) && $plan->plan->exists() && $plan->plan->manager_id != null) {
                        $user = $plan->plan->incharge;
                        if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($user->email)->queue(new PlanReminderNotification($user, $plan));
                        }
                    }
                }
            }
        } catch(Exception $e) {
            Log::error('ScheduleMaintenanceReminder-command : '.$e->getMessage());
            return false;
        }
        
    }
}

<?php

namespace App\Console\Commands;

use Exception;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceCron;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceStatusHistory;
use App\Mail\ScheduleMaintenance\PlanDelayedNotification;
use Mail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DelayedScheduleMaintenanceCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DelayedScheduleMaintenanceCheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sending email and updating status for delayed schedule maintenance';

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
            $plans = ScheduleMaintenanceCron::where('schedule_date' ,'<', Carbon::now()->addDay()->format('Y-m-d 00:00:00'))->whereNull('deleted_at')->whereNotIn('status', [3,4,5,8])->get();
            if ($plans->count()) {
                foreach ($plans as $plan) {
                    if ($plan->update(['status' => 8])) {
                        $history = new ScheduleMaintenanceStatusHistory;
                        $history->action_type = 1;
                        $history->allocation_id = $plan->id;
                        $history->status_id = $plan->status;
                        $history->remark = 'Delayed';
                        $history->updated_by = null;
                        $history->save();
                        if ($plan->supplier_id != null) {
                            $user = $plan->supplier->user;
                        } else if ($plan->handler_id != null) {
                            $user = $plan->handler;
                        }
                        if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($user->email)->queue(new PlanDelayedNotification($user, $plan));
                        }
                        if (!empty($plan->plan) && $plan->plan->exists() && $plan->plan->manager_id != null) {
                            $user = $plan->plan->incharge;
                            if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                Mail::to($user->email)->queue(new PlanDelayedNotification($user, $plan));
                            }
                        }
                    }
                }
            }
        } catch(Exception $e) {
            Log::error('DelayedScheduleMaintenanceCheck-command : '.$e->getMessage());
            return false;
        }
        
    }
}

<?php

namespace App\Console\Commands;

use Exception;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceCron;
use App\Models\ScheduleMaintenance\ScheduleMaintenancePlanAllocation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Cron\CronExpression;
use DB;
use App\Models\Model;
use App\Models\Device;

class AllocateScheduleMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AllocateScheduleMaintenance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'allocate schedule maintenance for current year';

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
            $plans = ScheduleMaintenancePlanAllocation::where('status', 1)->get();
            if ($plans->count()) {
                foreach ($plans as $scheduled_plan) {
                    if ($scheduled_plan->plan->recursion_plan == 1) {
                        $cron = explode(" ", $scheduled_plan->plan->cron_expression);
                        $nextDates = explode(",", Carbon::create($cron[4], $cron[3], $cron[2], $cron[1], $cron[0], '', config('app.timezone'))->toDateTimeString());
                    } else {
                        $cron = new CronExpression($scheduled_plan->plan->cron_expression);
                        $today = Carbon::now();
    
                        $from = Carbon::parse(Carbon::now()->format('Y-m-d H:i:00'));
                        $to = Carbon::parse($today->endOfYear()->format('Y-m-d H:i:00'));
                        $days = $from->diffInDays($to);
                        $nextDates = $cron->getMultipleRunDates($days, Carbon::now(), false, true);
                    }
                    if (!empty($scheduled_plan->device_id)) {
                        $devices = explode(",", $scheduled_plan->device_id);
                    }
                    else if (!empty($scheduled_plan->model_id)) {
                        $db = DB::table("assets as a");
                        $db->leftJoin("status_labels as s", "a.status_id", "=", "s.id");
                        $db->select("a.id", "a.model_id");
                        $db->whereNull("a.deleted_at");
                        $db->whereNull("s.sold");
                        $db->whereNull("s.stolen_item");
                        $db->where("a.model_id", "=", $scheduled_plan->model_id);
                        $devices = $db->get();
                    } else {
                        $db = Model::whereNull("deleted_at")->where("category_id", "=", $scheduled_plan->category_id)->pluck('id');
                
                        $dbs = DB::table("assets as a");
                        $dbs->leftJoin("status_labels as s", "a.status_id", "=", "s.id");
                        $dbs->select("a.id", "a.model_id");
                        $dbs->whereNull("a.deleted_at");
                        $dbs->whereNull("s.sold");
                        $dbs->whereNull("s.stolen_item");
                        $dbs->whereIn("a.model_id", $db);
                        $devices = $dbs->get();
                    }
                    foreach ($nextDates as $nextDate) {
                        foreach ($devices as $device) {
                            $dev = Device::find($device);
                            $scheduleMaintenance = new ScheduleMaintenanceCron;
                            $scheduleMaintenance->allocated_id = $scheduled_plan->id;
                            $scheduleMaintenance->plan_id = $scheduled_plan->plan_id;
                            $scheduleMaintenance->category_id = isset($dev->model) ? $dev->model->category_id : null;
                            $scheduleMaintenance->device_id = isset($device->id) ? $device->id : $device;
                            $scheduleMaintenance->model_id = !empty($dev->model_id) ? $dev->model_id : null;
                            $scheduleMaintenance->schedule_date = $nextDate;
                            $days = $scheduled_plan->plan->closing_recursion_time_days;
                            $scheduleMaintenance->closing_schedule_date = ($days > 0) ? Carbon::parse($nextDate)->addDays($days) : null;
                            $scheduleMaintenance->task_step = 1;
                            $scheduleMaintenance->supplier_id = !empty($scheduled_plan->supplier_id) ? $scheduled_plan->supplier_id : null;
                            $scheduleMaintenance->handler_id = !empty($scheduled_plan->handler_id) ? $scheduled_plan->handler_id : null;
                            $scheduleMaintenance->save();
                        }
                    }
                }
            }
        } catch(Exception $e) {
            Log::error('ScheduleMaintenancePlanAllocation-command : '.$e->getMessage());
            return false;
        }
    }
}

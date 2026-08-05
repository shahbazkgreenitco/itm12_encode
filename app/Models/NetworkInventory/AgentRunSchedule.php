<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Log;
use DB;

class AgentRunSchedule extends Model
{
    protected $table = "itm_network_agent_run_schedules";
    public $timestamps = false;
    protected $guarded = [];

    protected static function getNextTime($targetTime) {
        try {
            $t = $targetTime->format('Y-m-d H:i:00');
            
            $getSchedule = DB::select("select id from itm_network_agent_run_schedules where schedule_at = '" . $t . "' limit 1");
            if(! count($getSchedule)) {
                AgentRunSchedule::create([
                    'schedule_at' => $targetTime->format('Y-m-d H:i:00'),
                    'tot_schedules' => 1
                ]);
                    
                return $targetTime;
            }

            $schedule = AgentRunSchedule::find($getSchedule[0]->id);
            if($schedule->tot_schedules < 5) {
                $schedule->tot_schedules += 1;
                $schedule->save();
                return $targetTime;
            }

            return null;
        }
        catch(\Exception $e) {
            Log::error('AgentRunSchedule-getNextTime');
            Log::error($e->getMessage());
            return null;
        }
    }

    public static function getNextSchedule($interval_hrs, $increase_mins, $targetTime=false) {
        try {
            if(! $targetTime) {
                $targetTime = Carbon::now(config('app.timezone'));
            }
            
            $targetTime->addHours($interval_hrs);
     
            $maxAttempt = 0;
            do {
                $getNextSchedule = AgentRunSchedule::getNextTime($targetTime);
                if($getNextSchedule == null) {
                    $targetTime->addMinutes($increase_mins);
                }
                $maxAttempt++;
            }
            while($getNextSchedule == null && $maxAttempt < 250);

            return $getNextSchedule;
        }
        catch(\Exception $e) {
            Log::error('AgentRunSchedule-getNextSchedule');
            Log::error($e->getMessage());
            return null;
        }
    }
}
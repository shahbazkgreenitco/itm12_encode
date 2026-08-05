<?php

namespace App\Console\Commands;

use App\Models\Ticket\Config;
use Illuminate\Console\Command;
use App\Models\Ticket\TechnicianAvailabilityLog;
use App\Models\Ticket\TechnicianAvailabilityLogArchive;
Use Log;
class UserActivityArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user_activity_archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'user activity move to archive';

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
        $config = Config::first();
        if(isset($config->auto_archive_user_activity_after_days) && $config->auto_archive_user_activity_after_days != null && $config->auto_archive_user_activity_after_days > 0 ){
            $userActivities = TechnicianAvailabilityLog::where('created_at', '<', date("Y-m-d H:i:s", strtotime("-".$config->auto_archive_user_activity_after_days." days")))->orderBy('id','ASC')->get();
            if(!empty($userActivities))  {
                foreach($userActivities as $activity){
                    $archive = new TechnicianAvailabilityLogArchive();
                    $archive->fill(json_decode(json_encode($activity),true));
                    $archive->save();
                    $activity->delete();
                }
                Log::info('move data to TechnicianAvailabilityLogArchive');
                $this->info('move data to TechnicianAvailabilityLogArchive');
            }  
        }
    }
}

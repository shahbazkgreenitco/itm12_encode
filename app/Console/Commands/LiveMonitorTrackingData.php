<?php

namespace App\Console\Commands;

use App\Models\LiveMonitor\LiveMonitorWebsiteIncident;
use App\Models\LiveMonitor\LiveMonitorWebsite;
use App\Models\LiveMonitor\LiveMonitorWebsiteHistory;
use App\Models\LiveMonitor\ItmWebsiteAlert;
use Illuminate\Console\Command;
use App\Models\Settings;
use Carbon\Carbon;
use App\Jobs\CheckWebsiteRequest;
use App\Helpers\Common as CommonHelper;
use App\Mail\LiveMonitor\LiveWebsiteStatusSendMailNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Mail;

class LiveMonitorTrackingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:live_monitor_website_trigger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        if(!config("services.live_monitor.enabled")){
            // Log::info('LiveMonitorTrackingData:- Live monitor is disabled.');
            return;
        }

        $now = Carbon::now(config('app.timezone'));
        $settings = Settings::getSettings();
        $liveMonitorWebsites = LiveMonitorWebsite::where('enabled', 1)->get();
        foreach ($liveMonitorWebsites as $website) {
            CheckWebsiteRequest::dispatch($website->id, $website->url);
            $addAlertWebsites = ItmWebsiteAlert::where('website_id', $website->id)->get();
            if($addAlertWebsites->isEmpty() == false){
                $alertWebsites = ItmWebsiteAlert::where('website_id', $website->id)->where('status', 1)->get();
                if($alertWebsites->count() <= 0) {
                    $general_status = 0;
                    $warningIncident = LiveMonitorWebsiteIncident::where('website_id', $website->id)->where('status', 2)->first();
                    if($warningIncident != null){
                        $general_status = $warningIncident->status;
                    }
                    $alertIncident = LiveMonitorWebsiteIncident::where('website_id', $website->id)->where('status', 3)->first();
                    if($alertIncident != null){
                        $general_status = $alertIncident->status;
                    }
                    LiveMonitorWebsite::where('id', $website->id)->update([
                        'status' => $general_status,
                    ]);
                } else {
                    foreach ($alertWebsites as $alertWebsite) {
                        $occurred = 0;
                        if ($alertWebsite->alert_type == 1) {
                            $history = LiveMonitorWebsiteHistory::where('website_id', $website->id)
                                ->orderBy('id', 'desc')
                                ->limit($alertWebsite->occurrence)
                                ->get();
                            
                                foreach ($history as $item) {
                                    if(CommonHelper::compare($item->statuscode, $alertWebsite->limit_value, $alertWebsite->comparison)){
                                        $occurred++;
                                    };
                                }  

                            $incident_level = 3;
                        }

                        if ($alertWebsite->alert_type == 2) {
                            $history = LiveMonitorWebsiteHistory::where('website_id', $website->id)
                                ->orderBy('id', 'desc')
                                ->limit($alertWebsite->occurrence)
                                ->get();
                            
                                foreach ($history as $item) {
                                    if(CommonHelper::compare($item->latency, $alertWebsite->limit_value, $alertWebsite->comparison)){
                                        $occurred++;
                                    };
                                }  
                            $incident_level = 2;
                        }

                        if ($occurred >= $alertWebsite->occurrence) {
                            $existingIncident = LiveMonitorWebsiteIncident::where('alert_type_id', $alertWebsite->id)
                                ->where('status', '!=', 1) 
                                ->first();
                        
                            if (!$existingIncident) {
                                $incident = LiveMonitorWebsiteIncident::create([
                                    'website_id' => $website->id,
                                    'alert_type_id' => $alertWebsite->id,
                                    'alert_type' => $alertWebsite->alert_type,
                                    'comparison' => $alertWebsite->comparison,
                                    'limit_value' => $alertWebsite->limit_value,
                                    'start_time' => now(),
                                    'end_time' => null,
                                    'repeats' => $alertWebsite->repeat,
                                    'last_notification' => now(),
                                    'status' => $incident_level,
                                    'ignore' => 0,
                                ]);

                                if($alertWebsite->alert_type == 1 &&  $incident_level == 3) {
                                    $liveMonitorWebsites = LiveMonitorWebsite::where('id',$website->id)->update([
                                        'status' =>  $incident_level,
                                    ]);
                                }else if($alertWebsite->alert_type == 2) {
                                    $liveMonitorWebsites = LiveMonitorWebsite::where('id',$website->id)->where('status', '!=', 3)->update([
                                        'status' =>  $incident_level,
                                    ]);
                                }                            
                            }
                        } else {

                            $existingIncident = LiveMonitorWebsiteIncident::where('alert_type_id', $alertWebsite->id)
                                ->where('status', '!=', 1)
                                ->first();
                            $downOldStatus = $existingIncident != null ? $existingIncident->status : null;  
                            if ($existingIncident != null) {
                                $existingIncident->update([
                                    'status' => 1,
                                    'end_time' => now(),
                                    // Resolved By System
                                    'resolved_by' => 0,
                                ]);

                                $notResolvedCount = LiveMonitorWebsiteIncident::where('website_id', $existingIncident->website_id)
                                ->where('status', '!=', 1)
                                ->count();
                                if( $notResolvedCount <= 0){
                                    $updateWebsite = LiveMonitorWebsite::find($existingIncident->website_id)->update([
                                        'status' => 1,
                                    ]);
                                    if($downOldStatus == 3) {
                                        if( !config('mail.service_enabled')){
                                            Log::info('LiveMonitorTrackingData:- Live Website status reminder mail service is disabled');
                                        }else{
                                            if($existingIncident->alert_type_id == $alertWebsite->id){
                                                if ($alertWebsite->notify_to != null && $alertWebsite->notify_to != 'null') {
                                                    $ccUsers = $alertWebsite->notify_to;
                                                    $usersCCArray = explode(",", $ccUsers);
                                                    $ccUserEmail = [];
                                                
                                                    if (!empty($usersCCArray)) {
                                                        foreach ($usersCCArray as $u) {
                                                            $users = User::where("id", $u)->where('activated', 1)->first();
                                                            if ($users && !empty($users->email) && filter_var($users->email, FILTER_VALIDATE_EMAIL)) {
                                                                array_push($ccUserEmail, $users->email);
                                                            }
                                                        }
                                                        if ($settings->alerts_enabled == 1) {
                                                            $alertNotify = CommonHelper::getGlobalAlertEmail(); 
                                                            foreach ($alertNotify as $email) {
                                                                array_push($ccUserEmail, $email);
                                                            }
                                                        }
                                                        $ccUserEmail = array_unique($ccUserEmail);
                                                        if (!empty($ccUserEmail)) {
                                                            foreach ($ccUserEmail as $key => $email) {
                                                                if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                                    try {
                                                                        $user = User::where("email", $email)->where('activated', 1)->first();
                                                                        Log::info($email . " data");
                                                                        Mail::to($email)->send(new LiveWebsiteStatusSendMailNotification($website, $user, $existingIncident));
                                                                    } catch (\Exception $e) {
                                                                        Log::error("Mail Error: " . $e->getMessage());
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else{
                                                    Log::info('LiveMonitorTrackingData:- Live Website status reminder mail not add any user');
                                                }
                                            } 
                                        }
                                    }
                                } else {
                                    $notResolvedDown = LiveMonitorWebsiteIncident::where('website_id', $existingIncident->website_id)
                                    ->where('status',3)
                                    ->count();
                                    if($notResolvedDown <= 0) {
                                        $updateWebsite = LiveMonitorWebsite::find($existingIncident->website_id)->update([
                                            'status' => 2,
                                        ]);
                                    } else {
                                        $updateWebsite = LiveMonitorWebsite::find($existingIncident->website_id)->update([
                                            'status' => 3,
                                        ]);
                                    }
                                }
                            }
                        }

                        LiveMonitorWebsite::where('id', $website->id)->where('status', 0)->update([
                            'status' => 1,
                        ]);
                                                
                        /** send mail notification */
                        if( !config('mail.service_enabled')){
                            Log::info('LiveMonitorTrackingData:- Live Website status reminder mail service is disabled');
                        }else{
                            $emailWebsiteIncidentNotification = LiveMonitorWebsiteIncident::where('website_id',$alertWebsite->website_id)->where('status','!=', 1)->get();
                            if($emailWebsiteIncidentNotification->count() > 0){
                                foreach ($emailWebsiteIncidentNotification as $key => $value) {
                                    if($value->alert_type_id == $alertWebsite->id){
                                        if ($alertWebsite->notify_to != null && $alertWebsite->notify_to != 'null') {
                                            $ccUsers = $alertWebsite->notify_to;
                                            $usersCCArray = explode(",", $ccUsers);
                                            $ccUserEmail = [];
                                        
                                            if (!empty($usersCCArray)) {
                                                foreach ($usersCCArray as $u) {
                                                    $users = User::where("id", $u)->where('activated', 1)->first();
                                                    if ($users && !empty($users->email) && filter_var($users->email, FILTER_VALIDATE_EMAIL)) {
                                                        array_push($ccUserEmail, $users->email);
                                                    }
                                                }
                                                if ($settings->alerts_enabled == 1) {
                                                    $alertNotify = CommonHelper::getGlobalAlertEmail(); 
                                                    foreach ($alertNotify as $email) {
                                                        array_push($ccUserEmail, $email);
                                                    }
                                                }
                                                $ccUserEmail = array_unique($ccUserEmail);
                                                if (!empty($ccUserEmail)) {
                                                    foreach ($ccUserEmail as $key => $email) {
                                                        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                            try {
                                                                $user = User::where("email", $email)->where('activated', 1)->first();
                                                                Log::info($email . " data");
                                                                Mail::to($email)->send(new LiveWebsiteStatusSendMailNotification($website, $user, $value));
                                                            } catch (\Exception $e) {
                                                                Log::error("Mail Error: " . $e->getMessage());
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else{
                                            Log::info('LiveMonitorTrackingData:- Live Website status reminder mail not add any user');
                                        }
                                    }
                                }
                            }    
                        }

                    }                 
                }  
            }
        }
        // Log::info('LiveMonitorTrackingData:- All website checks have been dispatched!');
    }
}

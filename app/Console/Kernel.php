<?php

namespace App\Console;

use App\Models\Department;
use App\Models\Holiday;
use App\Models\Location;
use App\Models\Ticket\Attachment;
use App\Models\Notification;
use App\Models\Ticket\Config;
use App\Models\Ticket\ProblemCategory;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Privilege;
use App\Models\Ticket\TktSchedular;
use App\Models\Ticket\TicketReference;
use App\Helpers\Common as CommonHelper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use App\Mail\Ticket\IntimateSuccessCreation;
use App\Models\Device\DeviceSetting;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use File;
use Log;
use Mail;
use DB;
use Storage;
use App\Http\Controllers\Ticket\RequestController;
use App\Models\Ticket\TktDetail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        Log::info('Kernel schedule() executed');
        $schedule->command('cleanup-api-tracks')->weeklyOn(7, '03:00')->timezone(config('app.timezone'))->withoutOverlapping();
        if(config("app.client") == "ltts") {
            $schedule->command('ms_user_sync_employee_number_new')->dailyAt('03:00')->timezone(config('app.timezone'))->withoutOverlapping();
            if(config('app.sub_client') != "admin" && config('app.sub_client') != "coe") {
                $schedule->command('ltts_user_du_head_sync')->dailyAt('23:00')->timezone(config('app.timezone'));
                $schedule->command('dubu_request_pending_approval_mail')->dailyAt('00:00')->timezone(config('app.timezone'));
                $schedule->command('is_du_bu_auto_Mailer')->monthlyOn(20, '00:00')->timezone(config('app.timezone'));
                $schedule->command('auto_reject_renewal_request')->dailyAt('00:00')->timezone(config('app.timezone'));
                $schedule->command('command:inactiveUserEmailNotification')->dailyAt('20:00')->timezone(config('app.timezone'))->withoutOverlapping();
            }
            if(config('app.sub_client') == "admin") {
                $schedule->command('command:consumableThresholdAlerts')->dailyAt('00:00')->timezone(config('app.timezone'));
                // $schedule->command('expiring-consumables-purchase')->dailyAt('00:00')->timezone(config('app.timezone'))->withoutOverlapping();
            }
        }

        if(config("services.service_ticket.enabled")) {
            $schedule->command('cards:overdue')->everyFiveMinutes();
            $schedule->command('tickets:notifyUserForTicketWaitingForUser')->dailyAt('05:00')->timezone(config('app.timezone'));
            $schedule->command('ticket:escalate')->everyFiveMinutes()->timezone(config('app.timezone'))->withoutOverlapping();
            $schedule->command('command:autoAllocateUnassignedTickets')->everyFiveMinutes()->timezone(config('app.timezone'))->withoutOverlapping();
            $schedule->command('tickets:send_exception_notification')->everyFiveMinutes()->timezone(config('app.timezone'))->withoutOverlapping();
            // if (config("services.azure.client_id") == "") {
                $schedule->command('ticket:new_ticket_from_email')->cron('*/5 * * * *')->timezone(config('app.timezone'))->withoutOverlapping();
            // } else {
            //     $schedule->command('new_ticket_from_ms_email')->cron('*/2 * * * *')->timezone(config('app.timezone'))->withoutOverlapping();
            // }
            $schedule->command('tickets:autoClose')->dailyAt('00:15')->timezone(config('app.timezone'));
            //$schedule->command('autoClosedServiceRequestAfterXDays')->dailyAt('00:15')->timezone(config('app.timezone'));
            $schedule->command('emailReportTrigger')->dailyAt('08:00')->timezone(config('app.timezone'))->withoutOverlapping();
            $schedule->command('customActionEmailTrigger')->dailyAt('00:00')->timezone(config('app.timezone'));
            $schedule->command('slaBreachedReminderTechnician')->hourly()->timezone(config('app.timezone'));
        }
        if(in_array(config('app.client'), ["rolepermission", "knightfrank", "rashmi"])) {
            $schedule->command('command:duplicate-summary')->dailyAt('00:05')->timezone(config('app.timezone'))->withoutOverlapping();
        }
        $schedule->command('kanban:autoCloseCards')->dailyAt('01:00');
        $schedule->command('kanban:archivecards')->dailyAt('01:00')->timezone(config('app.timezone'));
        // $schedule->command('ni:agent_data_crawler')->cron('*/2 * * * *')->timezone(config('app.timezone'))->withoutOverlapping();
        $schedule->command('niAssetSync')->everyTwoMinutes()->timezone(config('app.timezone'))->withoutOverlapping();
        $schedule->command('ni:check_for_new_device')->cron('*/4 * * * *')->timezone(config('app.timezone'))->withoutOverlapping();
        if(!in_array(config('app.client'), ["knightfrank", "ltts", "tscpl"])) {
            $schedule->command('azureDeviceMapping')->mondays()->at('05:30')->timezone(config('app.timezone'));
        }
        $schedule->command('oem_warranty_update')->mondays()->at('02:30')->timezone(config('app.timezone'));
        $schedule->command('device:warranty_adjustment')->mondays()->at('06:30')->timezone(config('app.timezone'));
        $schedule->command('rdp:deviceSync')->everySixHours()->timezone(config('app.timezone'));
        $schedule->command('command:deviceExpectedReturnDateReminder')->dailyAt('00:30')->timezone(config('app.timezone'));
        $schedule->command('loadAzureDevices')->dailyAt('02:30')->timezone(config('app.timezone'));
        $schedule->command('contractAgreementExpireReminder')->dailyAt('00:15')->timezone(config('app.timezone'));
        if (config("services.azure_multi.tenant") == "") {
            $schedule->command('ms_user_sync:azure')->dailyAt('00:30')->timezone(config('app.timezone'))->withoutOverlapping();
        } else {
            $schedule->command('ms_user_sync_multiple:azure')->dailyAt('00:30')->timezone(config('app.timezone'))->withoutOverlapping();
            $schedule->command('command:hrOneManageStatus')->dailyAt('03:00')->timezone(config('app.timezone'))->withoutOverlapping();
        }
        if(config("services.google_workspace.client_id") != "") {
            $schedule->command('loadGoogleWorkspaceUsers')->dailyAt('00:30')->timezone(config('app.timezone'))->withoutOverlapping();
        }
        $schedule->command('command:DelayedScheduleMaintenanceCheck')->dailyAt('00:00')->timezone(config('app.timezone'))->withoutOverlapping();
        $schedule->command('command:ScheduleMaintenanceReminder')->dailyAt('00:00')->timezone(config('app.timezone'))->withoutOverlapping();
        $schedule->command('command:AllocateScheduleMaintenance')->yearly()->timezone(config('app.timezone'));
        // $schedule->command('command:notDetectedDevicesSendEmail')->dailyAt('00:00')->timezone(config('app.timezone'))->withoutOverlapping();
        // $schedule->command('email:trigger')->dailyAt('00:00')->timezone(config('app.timezone'));
        // $schedule->command('meeting:send-reminder')->hourly();
        $schedule->command('command:disposeAsset')->dailyAt('00:15')->timezone(config('app.timezone'));
        // $schedule->command('command:optimize-tables')->sundays();
        $schedule->command('command:convert-zip')->monthlyOn(1, '18:00');
        $schedule->command('command:amcMonthlyReport')->mondays()->at('02:00')->timezone(config('app.timezone'))->when(function() {
            return (config("app.client"));
        });
        $schedule->command('command:licenseExpireMonthlyReport')->wednesdays()->at('02:00')->timezone(config('app.timezone'))->when(function() {
            return (config("app.client"));
        });
        $schedule->command('command:warrantyMonthlyReport')->thursdays()->at('02:00')->timezone(config('app.timezone'))->when(function() {
            return (config("app.client"));
        });
        $schedule->command('queue:work --once --tries=3')->everyMinute()->timezone(config('app.timezone'))->withoutOverlapping();
        $schedule->command('command:blacklistedReport')->dailyAt('01:00')->timezone(config('app.timezone'));
        $schedule->command('devices:check-empty-os-model')->monthlyOn(1, '02:00');
        if (config("services.live_monitor.enabled")) {
            $device_settings = DeviceSetting::getDeviceSettings();
            $minute = 5;
            if (!empty($device_settings->live_monitor_auto_run_in_minute)) {
                $minute = (int) $device_settings->live_monitor_auto_run_in_minute;
            }
            $schedule->command('command:live_monitor_website_trigger')->timezone(config('app.timezone'))->withoutOverlapping()->cron("*/$minute * * * *");
        }
        if(config("services.patch_management.enabled") == 1) {
            $schedule->command('swTrackingDataSync')->everyTwoHours()->timezone(config('app.timezone'))->withoutOverlapping();
        }
        // $schedule->command('feedback:reminder')->daily('01:00')->timezone(config('app.timezone'));
        // $schedule->command('ticket:auto-update')->everyMinute()->timezone(config('app.timezone'));
        // $schedule->command('compare_os_master_data')->dailyAt('00:00')->timezone(config('app.timezone'));
        if(config("services.service_ticket.enabled")) {
            $ts = TktSchedular::whereNull('is_temp')->whereNotNull('action_expression')->get();
            if (!empty($ts)) {
                $all_expression = [];
                foreach ($ts as $val) {
                    $config = Config::where('company_id', $val->company_id)->first();
                    $config->setWeekEnds();
                    $current_datetime = Carbon::now(config('app.timezone'));
                    $holidays = Holiday::getHolidaysFrom($current_datetime->format('Y-m-d'));
                    $dataAction = json_decode($val->action_expression, true);
                    if ($dataAction['action_data'] == 'onetime') {
                        $ref = TicketReference::where('scheduler_id',$val->id)->where('status', 0)->get();
                        foreach($ref as $r){
                            $date = explode(" ", $r->cron_expression);
                            $expr_date = $date[4].'-'.$date[3].'-'.$date[2];
                            $comp_date = Carbon::parse($expr_date);
                            $isToday = $comp_date->isToday();
                            if($isToday && $date[4] == date("Y")) {
                                $expression = $date[0] . ' ' . $date[1] . ' ' . $date[2] . ' ' . $date[3].' *';
                                array_push($all_expression,['expression' => $expression, 'ticket_id' => $r->ticket_id]);
                            }
                        }
                    } else {
                        $expression = $val->cron_expression;
                        array_push($all_expression, ['expression' => $expression, 'ticket_id' =>'']);
                    }

                    foreach($all_expression as $expression) {
                        $tkt_id = $expression['ticket_id'];
                        $schedule->call(function () use ($val, $config, $current_datetime, $holidays,$tkt_id) {
                            $ticket = new Ticket;
                            $ticket->creator_id = $val->creator_id;
                            $ticket->status_id = 1;
                            $ticket->created_via = 3;
                            $ticket->department_id = $val->department_id;
                            $ticket->content = $val->content;
                            $ticket->problem_category_id = $val->problem_category_id;
                            $ticket->sub_category_id = $val->sub_category_id;
                            $ticket->priority_id = $val->priority_id;
                            // $ticket->tat = CommonHelper::checkTAT($val->tat, $val->priority_id);
                            $ticket->tat = $val->tat;
                            $ticket->tat_expire = $config->calculateAdvancedTat($val->tat, $current_datetime, $holidays);
                            $ticket->subject = $val->subject;
                            $ticket->ac_email_id = $val->ac_email_id;
                            $ticket->is_temp = $val->is_temp;
                            $ticket->company_id = $val->company_id;
                            $otherLocation = Location::where('name', 'like', 'Other')->first();
                            if(empty($otherLocation)) {
                                $otherLocation = new Location();
                                $otherLocation->name = $otherLocation->address = $otherLocation->city = $otherLocation->state = "Other";
                                $otherLocation->country = "IN";
                                $otherLocation->country_id = 101;
                                $otherLocation->state_id = 4008;
                                $otherLocation->city_id = 133024;
                                $otherLocation->currency = "INR";
                                $otherLocation->user_id = Auth::user()->id;
                                $otherLocation->save();
                            }
                            $creator = User::find($val->creator_id);
                            $ticket->location_id = $otherLocation->id;
                            if (isset($val->creator_id) && $val->creator_id != "") {
                                if (!empty($creator)) {
                                    $ticket->location_id = $creator->location_id;
                                }
                            }
                            $ticket->save();
                            $ticketDetail = new TktDetail();
                            $ticketDetail->ticket_id = $ticket->id;
                            $ticketDetail->seat_no = $val->seat_no;
                            $ticketDetail->save();
                            $creator_manager_email = [];
                            // code for store new ticket id in old ticket

                            if (isset($tkt_id) && $tkt_id != '') {
                                $tkt = Ticket::find($tkt_id);
                                if (!empty($tkt) && $tkt != '' && !isset($tkt->revoke_access_at) && $tkt->revoke_access_at == null) {
                                    DB::table('tkt_tickets')->where('id', $tkt_id)->update([
                                        'new_ticket_reference' => $ticket->id,
                                    ]);
                                    $tkt_eng = User::find($tkt->assigned_to);
                                    // in new ticket content append company,department, problem category,assigned to ,creator and created at
                                    $company = isset($tkt->department->company_id) ? "<b>Company</b>: ".$tkt->department->company->name : '';
                                    $dep = ($tkt->department_id != null) ? "<br> <b>Department</b>: ".$tkt->department->name : '';
                                    $prob = "<br><b> Prob. Category</b>: ".$tkt->problemCategory->name;
                                    $hostname = "<br> <b> Hostname </b>: ".$tkt->device->asset_tag;
                                    $assign = "";
                                    if(!empty($tkt_eng) && $tkt_eng->hasPermission("service_tickets")) {
                                        $assign = $tkt->assigned_to != null ? "<br><b>Assigned To</b>: ".$tkt->assignedTo->username." (".$tkt->assignedTo->email.")" : '';
                                    }
                                    $others = "<br><b>Creator</b>: ".$tkt->creator->username." (".$tkt->creator->email.")<br><b>Created At</b> :".CommonHelper::getDateAs($tkt->created_at, 'd/m/Y h:i A', 'Y-m-d H:i:s')."<br>";
                                    $enableUsbRequest_old = [];
                                    $departmentCustomFieldset = Department::find($tkt->department_id);
                                    if(!empty($departmentCustomFieldset) && ($departmentCustomFieldset->name == "IT Service Request" || $departmentCustomFieldset->name == "IT Service Request Overseas")) {
                                        $enableUsbRequest_old = ProblemCategory::enableUsbRequest([$tkt->department_id]);
                                    }
                                    $enableUsbRequests_new = ProblemCategory::where('privilege_access', 1)->pluck('id')->toArray();
                                    $enableUsbRequests = array_merge($enableUsbRequest_old, $enableUsbRequests_new);
                                    $enableCategory = array_values(array_unique($enableUsbRequests));
                                    if(!empty($enableCategory) && in_array($tkt->problem_category_id, $enableCategory)) {
                                        $ticket->subject = $val->subject . " - " . $tkt->problemCategory->name;
                                    }
                                    $ticket->content = $company.$dep.$prob.$assign.$hostname.$others;
                                    $ticket->old_ticket_ref = $tkt->id;
                                    $ticket->department_id = $tkt->department_id;
                                    $ticket->problem_category_id = $tkt->problem_category_id;
                                    $ticket->sub_category_id = $tkt->sub_category_id;
                                    $ticket->priority_id = 2;
                                    // ltts cr revoke ticket TAT is 8Hrs
                                    $ticket->tat = 8;
                                    // $ticket->tat = CommonHelper::checkTAT($tkt->tat, 2);
                                    if (!empty($tkt_eng) && $tkt_eng->hasPermission("service_tickets") && (empty($tkt_eng->last_working_date) || Carbon::parse($tkt_eng->last_working_date)->gt(Carbon::today()))) {
                                        $ticket->assigned_to = (isset($tkt->assignedTo) && !empty($tkt->assignedTo) && $tkt->assignedTo->activated == 1) ? $tkt->assigned_to : null;
                                    }
                                    // $ticket->tat_expire = $config->calculateAdvancedTat($tkt->tat, $current_datetime, $holidays);
                                    $ticket->tat_expire = $config->calculateAdvancedTat(8, $current_datetime, $holidays);
                                    $ticket->creator_id = $tkt->creator_id;
                                    $ticket->device_id = $tkt->device_id;
                                    $ticket->custom_fields = $tkt->custom_fields;
                                    $ticket->revoke_access_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                                    // $ticket->created_via = 3;
                                    $creator = User::find($tkt->creator_id);
                                    if (isset($tkt->creator_id) && $tkt->creator_id != "") {
                                        if (!empty($creator)) {
                                            $ticket->location_id = $creator->location_id;
                                        }
                                    }
                                    if($ticket->save()){
                                        $oldRevoke = TicketReference::where('ticket_id',$tkt_id)->first();
                                        $oldRevoke->status = 1;
                                        $oldRevoke->save();
                                        if (config("app.client") == "ltts" && $tkt->created_at->gt(Carbon::parse('2026-01-10'))) {
                                            app(RequestController::class)->createTasksFromCategory($ticket);
                                        }
                                    }
                                    // if(isset($creator->manager->email) && $creator->manager->email != "") {
                                    //     array_push($creator_manager_email, $creator->manager->email);
                                    // }

                                     /* notification to creator */
                                    // $notification_text = 'New ticket has been created';
                                    // $notify_people = Privilege::getHandlersByDepartment($ticket->department_id);
                                    // Notification::makeTicketNotification($ticket, $notify_people, $notification_text, $ticket->creator_id);

                                    if (config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                                        try {
                                            if(isset($tkt->assignedTo->email) && $tkt->assignedTo->email != "" && filter_var($tkt->assignedTo->email, FILTER_VALIDATE_EMAIL)) {
                                                array_push($creator_manager_email, $tkt->assignedTo->email);
                                            }
                                            Mail::to($creator->email)->cc($creator_manager_email)->queue(new IntimateSuccessCreation($ticket, $creator));
                                        } catch (\Exception $e) {
                                            Log::error('new ticket created from revoke issues'.$e->getMessage());
                                        }
                                    }
                                }
                            } else {
                                if (config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        if(isset($tkt->assignedTo->email) && $tkt->assignedTo->email != "" && filter_var($tkt->assignedTo->email, FILTER_VALIDATE_EMAIL)) {
                                            //array_push($creator_manager_email, $tkt->assignedTo->email);
                                        }
                                        Mail::to($creator->email)->cc($creator_manager_email)->queue(new IntimateSuccessCreation($ticket, $creator));
                                    } catch (\Exception $e) {
                                        Log::error('new ticket created from revoke issues'.$e->getMessage());
                                    }
                                }
                            }

                            /** Code is for add new ticket functionality in ticket history */
                            $tkt_update['ticket_id'] = $ticket->id;
                            $tkt_update['updated_by'] = 0;
                            $tkt_update['action_type'] = 14;
                            CommonHelper::ticketStatusHistory($tkt_update, $ticket);
                            /** Code ends here */

                            // Store the history of Ticket Create & assigned to technician
                            app(RequestController::class)->ticketHistory($ticket);
                            app(RequestController::class)->createTasksFromCategory($ticket);
                            if ($val->attachment_data != NULL) {
                                $attachment_data = json_decode($val->attachment_data, true);
                                foreach ($attachment_data as $data) {
                                    $attachment = new Attachment;
                                    $attachment->ticket_id = $ticket->id;
                                    $attachment->original_file_name = $data['original_file_name'];
                                    $attachment->extension = $data['extension'];
                                    $attachment->file_name = $data['file_name'];
                                    $attachment->uploader_id = $data['uploader_id'];
                                    $attachment->thumbnail = $data['thumbnail'];
                                    if ($data['file_name'] != null) {
                                        $from_path = storage_path('schedular_attachments') . DIRECTORY_SEPARATOR . $data['file_name'];
                                        $to_path = Storage::disk('storage_tkt')->path($data['file_name']);
                                        File::copy($from_path, $to_path);
                                    }
                                    if ($data['thumbnail'] != null) {
                                        $thumbnail_from_path = storage_path('schedular_attachments') . DIRECTORY_SEPARATOR . $data['thumbnail'];
                                        $thumbnail_to_path = Storage::disk('storage_tkt')->path($data['thumbnail']);
                                        File::copy($thumbnail_from_path, $thumbnail_to_path);
                                    }
                                    $attachment->save();
                                }
                            }
                        })->cron($expression['expression']);
                    }
                    $all_expression = [];
                }
            }
        }
    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

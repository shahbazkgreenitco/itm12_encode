<?php

namespace App\Console\Commands;

use App\Mail\Ticket\NotifyUserForTicketWaitingForUser as RequestNotifyUserForTicketWaitingForUser;
use App\Mail\Ticket\Resolved;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\Setting;
use App\Models\Ticket\Config;
use App\Models\Ticket\Ticket;
use App\Models\TktFollowing;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use App\Helpers\Common as CommonHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\TaskManagement\Task;
use App\Models\TaskManagement\TaskHistory;
use App\Models\Ticket\Status;

class NotifyUserForTicketWaitingForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:notifyUserForTicketWaitingForUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify user for ticket waiting for user.';

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
            $c = Config::first();
            $c->setWeekEnds();

            $tpcStatus = 0;
            $allowDays = 3;
            $wfuStatus = 7;
            if(in_array(config('app.client'), ['ltts', 'rolepermission', 'grdemo']) && (config('app.sub_client') == "live" || config('app.sub_client') == "dev")) {
                // for cr related ticket status for pending vendor confirmation letter
                $tpcStatusObj = Status::where('name', "Pending Vendor Conf Letter")->first();
                if(!empty($tpcStatusObj)) {
                    $tpcStatus = $tpcStatusObj->id;
                }
            }
            $allTickets = Ticket::select('id', 'status_id', 'creator_id', 'department_id', 'updated_at', 'ac_email_id')
                ->whereIn('status_id', [$tpcStatus, $wfuStatus])->whereNull('is_temp')->whereNull('deleted_at')
                ->orderBy('updated_at', 'asc')->get();

            $this->info("Total: " . count($allTickets));
            foreach ($allTickets as $key => $ticket) {
                if($ticket->status_id == $tpcStatus) {
                    $wfuStatus = $tpcStatus;
                    $allowDays = 30;
                }
                $this->info("Ticket ID: " . $ticket->id);
                $tktFollowing = TktFollowing::where('ticket_id', $ticket->id)->where('updated_status', $wfuStatus)->orderBy('id', 'desc')->first();
                Log::channel('email_ticket')->info("WFU tktFollowing: " . json_encode($tktFollowing));
                Log::channel('email_ticket')->info("WFU allowDays : " . $allowDays);
                if (!empty($tktFollowing)) {
                    $updateByTechnicianTime = $tktFollowing->created_at;
                    $holidays = Holiday::getHolidaysFrom(Carbon::make($tktFollowing->created_at));
                    $tickeUpdateDate = Carbon::make($updateByTechnicianTime)->addDays(1)->setTime(00,00,00);
                    $i=0;
                    while($i < $allowDays) {
                        // $this->info("Ticket: ". $tickeUpdateDate);
                        if(!$tickeUpdateDate->isWeekend() && !in_array($tickeUpdateDate->format("Y-m-d"),$holidays)){
                            $afterThreeDay = Carbon::make($tickeUpdateDate)->addDays(1)->format("Y-m-d H:i:s");
                            $i++;
                        }
                        $tickeUpdateDate->addDay();
                    }
                    // dd($afterThreeDay);
                    $nowTime = Carbon::now();
                    $afterThreeDay = Carbon::make($afterThreeDay);
                    // dd($nowTime);
                    // dd($afterThreeDay);
                    $tktUserReply = TktFollowing::where('ticket_id', $ticket->id)
                        ->where('updated_by', $ticket->creator_id)
                        ->where('created_at', '>=', $updateByTechnicianTime)
                        ->where('created_at', '<=', $afterThreeDay)
                        ->whereNotNull('remarks')
                        ->orderBy('id', 'desc')->first();

                    if (empty($tktUserReply) && !$nowTime->isWeekend() && !in_array($nowTime->format("Y-m-d"), $holidays)) {
                        $dayLeft = $afterThreeDay->diffInDays($nowTime, true);
//                        $this->info($dayLeft);
                        if ($nowTime->gt($afterThreeDay)) {
                            $this->info("Close:-".$ticket->id);
                            $ticket->status_id = 5;
                            $ticket->resolved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                            $ticket->save();
                            $ticket = Ticket::find($ticket->id);

                            // storing ticket following start
                            $tf = new TktFollowing();
                            $tf->ticket_id = $ticket->id;
                            $tf->remarks = "<p>Ticket Resolved automatically, since no response received from the user</p>";
                            $tf->is_note = 1;
                            $tf->updated_by = 0;
                            $tf->updated_status = 5;
                            $tf->action_type = 2;
                            $tf->save();
                            // storing ticket following end

                            $tkt_update['ticket_id'] = $ticket->id;
                            $tkt_update['is_note'] = 0;
                            $tkt_update['action_type'] = 2;
                            $tkt_update['status'] = 5;
                            $tkt_update['updated_by'] = 0;
                            CommonHelper::ticketStatusHistory($tkt_update);

                            // For update ticket status to auto Closed
                            $departmentObj = Department::where("id", $ticket->department_id)->where("name", "IT Service Request")->first();
                            if(!empty($departmentObj)) {
                                $dateRange = (new Carbon(config('app.timezone')))->addMinutes(1);
                                $query = 'update tkt_tickets set status_id = 6, closed_at = "' . $dateRange . '" where status_id = 5 and is_temp is null and deleted_at is null and id = ' . $ticket->id;
                                $affect = DB::update($query);
                            }

                            $creator = $ticket->creator;
                            $cc_emails = [];
                            if(!empty($creator) && !empty($creator->manager) && $creator->manager->email != "") {
                                // $manager =  User::find($creator->manager_id);
                                // $cc_emails[] = $manager->email;
                            }
                            if (config('mail.service_enabled') == 1 && $creator->email != "" && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                                $add_back_trail = true;
                                Log::info("RequestNotifyUserForTicketWaitingForUser Resolved: " . $creator->email . ' - '.$ticket->id);
                                Mail::to($creator->email)->cc($cc_emails)->queue(new Resolved($ticket, $creator, $tf->remarks, $tf, $add_back_trail));
                            }

                            $tasks = Task::where('ticket_id', $ticket->id)->get();
                            if(!empty($tasks)) {
                                foreach($tasks as $task) {
                                    $task = Task::find($task->id);
                                    if(!empty($task) && !in_array($task['status_id'], [7, 9])) {
                                        // $task->description = "This task was automatically completed by the system because the ticket status was Waiting for the User";
                                        $task->status_id = 7;
                                        $task->type_id = 4;
                                        $task->end_date = date('Y-m-d H:i:s');
                                        $task->save();

                                        $ActionHistory = new TaskHistory();
                                        $ActionHistory->task_id = $task->id;
                                        $ActionHistory->name = $task->name;
                                        $ActionHistory->status_id = $task->status_id;
                                        if($wfuStatus == $tpcStatus) {
                                            $ActionHistory->remarks = "This task was automatically completed by the system because the ticket status was Pending Vendor Confirmation Letter";
                                        } else {
                                            $ActionHistory->remarks = "This task was automatically completed by the system because the ticket status was Waiting for the User";
                                        }
                                        $ActionHistory->action_id = 5;
                                        $ActionHistory->change_by = 0;
                                        $ActionHistory->change_by_module = 1;
                                        $ActionHistory->save();

                                        if($task->type_id == 4 && !empty($task->ticket_id)) {
                                            $ActionHistory['ticket_id'] = $task->ticket_id;
                                            $ActionHistory['action_type'] = 22;
                                            $ActionHistory['updated_by'] = 0;
                                            CommonHelper::ticketStatusHistory($ActionHistory);
                                        }
                                    }
                                }
                            }
                        } else {
                            $this->info("Mail:-".$ticket->id);
                            $user = User::find($ticket->creator_id);
                            $ticket->creator_name = $user->getGuranteedNameText(true);
                            $ticket->site_name = Setting::getSettings()->site_name;
                            $ticket->days_left_to_close = $dayLeft;
                            $email = $user->email;

                            //storing ticket following start
                            $tf = new TktFollowing();
                            $tf->ticket_id = $ticket->id;
                            if($wfuStatus == $tpcStatus) {
                                $tf->remarks = "Pending Vendor Confirmation Letter ticket reminder sent to the user at : " . Carbon::now()->format("Y-m-d H:i:s");
                            } else {
                                $tf->remarks = "Ticket Status Waiting for the User reminder sent to the user at : " . Carbon::now()->format("Y-m-d H:i:s");
                            }
                            $tf->is_note = 1;
                            $tf->updated_by = 0;
                            $tf->action_type = 7;
                            $tf->save();
                            //storing ticket following end

                            $tkt_update['ticket_id'] = $ticket->id;
                            $tkt_update['is_note'] = 1;
                            $tkt_update['action_type'] = 7;
                            $tkt_update['updated_by'] = 0;
                            CommonHelper::ticketStatusHistory($tkt_update);
                            $cc_emails = [];
                            if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                // $manager =  User::find($user->manager_id);
                                // $cc_emails[] = $manager->email;
                            }
                            if (config('mail.service_enabled') == 1 && $email != "" && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                Log::info("RequestNotifyUserForTicketWaitingForUser: " . $email . ' - '.$ticket->id);
                                Mail::to($email)->cc($cc_emails)->queue(new RequestNotifyUserForTicketWaitingForUser($ticket, $tktFollowing->remarks, $tpcStatus));
                            }
                            unset($ticket->days_left_to_close);
                            unset($ticket->creator_name);
                            unset($ticket->site_name);
                            $ticket->updated_at = date('Y-m-d H:i:s');
                            $ticket->save();
                        }
                    }
                }

                if (($key + 1) % 10 === 0) {
                    $delay = rand(10, 30);
                    sleep($delay);
                }
            }

            Log::info("NotifyUserForTicketWaitingForUser : Cron Executed");
        } catch (Exception $e) {
            Log::error('NotifyUserForTicketWaitingForUser() Error : ' . $e->getMessage());
        }
    }
}

<?php
namespace App\Console\Commands;

use App\Models\DuBuHeadUser;
use App\Mail\Ticket\DuBuBulkApproval;
use Illuminate\Console\Command;
use App\Models\Ticket\TicketApprovalRequest;
use App\Models\Department;
use Log;
use Mail;
use DB;
use App\Models\User;

class DUBURequestPendingApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dubu_request_pending_approval_mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This cronjob will use to send all pending SR Approval to DU/BU Head';

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
        try{
            // strict mode
            config()->set('database.connections.mysql.strict', false);
            DB::reconnect();
            $dept = Department::select('id')->whereIn('name', ['IT Service Request', 'IT Service Request Overseas'])->pluck('id')->toArray();
            $heads = DUBUHeadUser::select('du_bu_head_users.*','users.email')->leftJoin('users','users.id','du_bu_head_users.user_id')->where('du_bu_head_users.status',1)->where('du_bu_head_users.user_id','!=', 0)->get();
            foreach($heads as $key => $head) {
                $user_id = $head->user_id;
                if($head->role == 1) {
                    // du head
                    $pendingApproval = TicketApprovalRequest::select('tkt_procure_requests.id','tkt_procure_requests.subject','tkt_approval_request.pab_id','users.email','users.delivery_unit', 'users.business_unit', 'pc.name as pc_name', 'sc.name as sc_name','tkt_procure_requests.content','trf.field_values','tcf.field_values as field_values_custom')
                    ->addSelect(DB::raw('DATE_FORMAT(tkt_procure_requests.created_at, "%d %b %y %h:%i %p") as requested_at'))
                    ->leftJoin('tkt_procure_requests','tkt_procure_requests.id','tkt_approval_request.pr_id')
                    ->leftJoin('tkt_ticket_pabs','tkt_ticket_pabs.id','tkt_approval_request.pab_id')
                    ->leftJoin('users', 'users.id','tkt_procure_requests.creator_id')
                    ->leftJoin('du_bu_head_users', 'du_bu_head_users.head','users.delivery_unit')   
                    ->leftJoin('tkt_problem_categories as pc', 'pc.id','tkt_procure_requests.problem_category_id')   
                    ->leftJoin('tkt_problem_categories as sc', 'sc.id','tkt_procure_requests.sub_category_id')   
                    ->leftJoin('tkt_requested_form as trf', 'trf.request_id','tkt_procure_requests.id')  
                    ->leftJoin('tkt_requested_custom_form as tcf', 'tcf.request_id','tkt_procure_requests.id')
                    ->where('users.delivery_unit', $head->head)
                    ->where('tkt_procure_requests.status_id', 2)               
                    ->where('tkt_ticket_pabs.hierarchy_approval', 9)
                    ->whereIn('tkt_procure_requests.department_id', $dept)
                    ->where(function ($q) use($user_id){
                        $q->where('tkt_approval_request.user_id', $user_id)
                            ->orWhere('tkt_approval_request.delegated_user_id', $user_id);
                    })->where('tkt_approval_request.approve_status', 3)->groupBy('tkt_approval_request.pr_id')->get();
                    $role = $head->role;
                } else {
                    // bu head
                    $pendingApproval = TicketApprovalRequest::select('tkt_procure_requests.id','tkt_procure_requests.subject','tkt_approval_request.pab_id', 'users.email','users.delivery_unit', 'users.business_unit', 'pc.name as pc_name', 'sc.name as sc_name','tkt_procure_requests.content','trf.field_values','tcf.field_values as field_values_custom')
                    ->addSelect(DB::raw('DATE_FORMAT(tkt_procure_requests.created_at, "%d %b %y %h:%i %p") as requested_at'))
                    ->leftJoin('tkt_procure_requests','tkt_procure_requests.id','tkt_approval_request.pr_id')
                    ->leftJoin('tkt_ticket_pabs','tkt_ticket_pabs.id','tkt_approval_request.pab_id')
                    ->leftJoin('users', 'users.id','tkt_procure_requests.creator_id')
                    ->leftJoin('du_bu_head_users', 'du_bu_head_users.head','users.business_unit')
                    ->leftJoin('tkt_problem_categories as pc', 'pc.id','tkt_procure_requests.problem_category_id')   
                    ->leftJoin('tkt_problem_categories as sc', 'sc.id','tkt_procure_requests.sub_category_id')  
                    ->leftJoin('tkt_requested_form as trf', 'trf.request_id','tkt_procure_requests.id')  
                    ->leftJoin('tkt_requested_custom_form as tcf', 'tcf.request_id','tkt_procure_requests.id')
                    ->where('users.business_unit', $head->head)
                    ->whereIn('tkt_procure_requests.department_id',$dept)
                    ->where('tkt_procure_requests.status_id', 2)
                    ->whereIn('tkt_procure_requests.department_id',$dept)
                    ->where('tkt_ticket_pabs.hierarchy_approval', 10)
                    ->where(function ($q) use($user_id){
                        $q->where('tkt_approval_request.user_id', $user_id)
                            ->orWhere('tkt_approval_request.delegated_user_id', $user_id);
                    })->where('tkt_approval_request.approve_status', 3)->groupBy('tkt_approval_request.pr_id')->get();
                    $role = $head->role;
                }
                if(config('mail.service_enabled') && $head && $head->email && filter_var($head->email, FILTER_VALIDATE_EMAIL) && !empty($pendingApproval) && count($pendingApproval) > 0) {
                    try {
                        Log::info("DUBURequestApproval cron for head: " . $head);
                        Mail::to($head->email)->send(new DuBuBulkApproval($head, $pendingApproval, $role));
                    } catch (\Exception $ex) {
                        Log::error("DUBURequestApproval error: " . $ex->getMessage());
                    }
                }
            }
            $listOfISUsers = User::whereNotNull('manager_id')->groupBy('manager_id')->get()->pluck('manager_id');
            foreach($listOfISUsers as $isUser) {
                try {
                   $pendingApproval = TicketApprovalRequest::select('tkt_procure_requests.id','tkt_procure_requests.subject','tkt_approval_request.pab_id', 'users.email','users.delivery_unit', 'users.business_unit', 'pc.name as pc_name', 'sc.name as sc_name','tkt_procure_requests.content','trf.field_values','tcf.field_values as field_values_custom')
                    ->addSelect(DB::raw('DATE_FORMAT(tkt_procure_requests.created_at, "%d %b %y %h:%i %p") as requested_at'))
                    ->leftJoin('tkt_procure_requests','tkt_procure_requests.id','tkt_approval_request.pr_id')
                    ->leftJoin('tkt_ticket_pabs','tkt_ticket_pabs.id','tkt_approval_request.pab_id')
                    ->leftJoin('users', 'users.id','tkt_procure_requests.creator_id')
                    ->leftJoin('tkt_problem_categories as pc', 'pc.id','tkt_procure_requests.problem_category_id')
                    ->leftJoin('tkt_problem_categories as sc', 'sc.id','tkt_procure_requests.sub_category_id')
                    ->leftJoin('tkt_requested_form as trf', 'trf.request_id','tkt_procure_requests.id')
                    ->leftJoin('tkt_requested_custom_form as tcf', 'tcf.request_id','tkt_procure_requests.id')
                    ->where('tkt_procure_requests.status_id', 2)
                    ->where('tkt_ticket_pabs.hierarchy_approval', 4)
                    ->whereIn('tkt_procure_requests.department_id', $dept)
                    ->where(function ($q) use($isUser) {
                        $q->where('tkt_approval_request.user_id', $isUser)
                            ->orWhere('tkt_approval_request.delegated_user_id', $isUser);
                    })->where('tkt_approval_request.approve_status', 3)->groupBy('tkt_approval_request.pr_id')->get();
                    $manager = User::find($isUser);
                    if(!empty($pendingApproval) && count($pendingApproval) > 0) {
                        if(config('mail.service_enabled') && $manager && $manager->email && filter_var($manager->email, FILTER_VALIDATE_EMAIL) && !empty($pendingApproval) && count($pendingApproval) > 0) {
                            try {
                                Log::info("DUBURequestApproval cron for IS: " . $manager->id);
                                Mail::to($manager->email)->send(new DuBuBulkApproval($manager, $pendingApproval, 'null'));
                            } catch (\Exception $ex) {
                                Log::error("DUBURequestApproval error: " . $ex->getMessage());
                            }
                        }
                    }
                } catch (\Exception $e) {
                   Log::error("DUBURequestApproval IS mail error: " . $ex->getMessage());
                }
            }
            // strict mode
            config()->set('database.connections.mysql.strict', true);
            DB::reconnect();
        } catch(\Exception $e) {
            Log::error('DUBURequestApproval-command () : '.$e->getMessage());
            return false;
        }
    }
}

<?php

namespace App\Http\Controllers\Auth\Api\Ticket;

use App\Mail\Ticket\IntimateAssigned;
use App\Mail\Ticket\IntimateSuccessCreation;
use App\Mail\Ticket\Request\StatusChange;
use App\Mail\Ticket\Request\TicketApproverInfo;
use App\Mail\Ticket\Request\TicketRequestApproved;
use App\Mail\Ticket\Request\TicketRequestRejected;
use App\Mail\Ticket\Request\UserComment;
use App\Models\Department;
use App\Models\Device;
use App\Models\Form\RequestedForm;
use App\Models\Holiday;
use App\Models\Location;
use App\Models\Settings;
use App\Models\Ticket\Attachment;
use App\Models\Ticket\Config;
use App\Models\Ticket\Privilege;
use App\Models\Ticket\ProblemCategory;
use App\Models\Ticket\Request\TicketRequestHistory;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketApprovalRequest;
use App\Models\Ticket\TicketPab;
use App\Models\Ticket\TicketPabMember;
use App\Models\Ticket\TicketProcureRequest;
use App\Models\Ticket\TicketRequestStatus;
use App\Models\Ticket\Priority;
use App\Models\Ticket\AutoCreationAccount;
use App\Models\Ticket\TicketStatusHistory;
use App\Models\TktFollowing;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServiceTicket\ServiceRequest;
use App\Helpers\Common as CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Traits\ApiResponse as TraitsApiResponse;
use stdClass;
use Validator;
use App\Mail\Ticket\Request\TicketRequestApprovalSend;
use App\Models\Ticket\ArchiveTicketProcureRequest;
use App\Models\Ticket\ArchivedTicketAttachment;
use App\Models\Ticket\ArchivedTicket;
use App\Models\Ticket\TicketTrigger;
use App\Events\TicketCreated;
use App\Models\Ticket\ArchivedTicketApprovalRequest;
use App\Models\Ticket\ArchivedTktRequestedForm;
use App\Models\Ticket\ArchiveTicketRequestHistory;
use App\Models\UserDetails;

class RequestController extends Controller
{
    use TraitsApiResponse;
    public function requestAjaxList(Request $request, $main_filter = "myRequestList") {
        $return = [];
        $req = $request->all();
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $sr_status = $request->input("sr_status", null);
        $page = $request->index ? $request->index : 0;
        $take = $request->list_size ? $request->list_size : 20;
        $skip = $page * $take;

         $companyIds=CommonHelper::getSelectedCompanyIds();

        $db = DB::table('tkt_procure_requests as tpr')
            ->select(
                'tpr.id', 
                'tpr.ticket_id',
                'tpr.procure_tag', 
                'tpr.status_id', 
                'tpr.subject', 
                'tpr.department_id', 
                'tpr.problem_category_id',
                'tpr.sub_category_id', 
                'tpr.pab_id', 
                'dep.name as dep_name', 
                'pab.name as pab', 
                'status.name as status',
                'company.name as company_name',
                'tpr.approved_day',
                DB::raw('case when creator.displayName is not null then creator.displayName else concat(creator.first_name, " ", creator.last_name) end as creator_name'),
                DB::raw('case when pc.approval_required = 1 then "Required" when pc.approval_required = 0 then "Not Required " else "" end as approval_required'),
                DB::raw('DATE_FORMAT(tpr.created_at, "%d %b %Y %h:%i %p") as created_at_format'),
                DB::raw('DATE_FORMAT(tpr.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'),
                DB::raw('DATE_FORMAT(tpr.approved_at, "%d %b %Y %h:%i %p") as approved_at_format')
            )
                ->leftJoin('departments as dep', 'tpr.department_id', '=', 'dep.id')
                ->leftJoin('tkt_problem_categories as pc', 'tpr.problem_category_id', '=', 'pc.id')
                ->leftJoin('users as creator', 'tpr.creator_id', '=', 'creator.id')
                ->leftJoin('tkt_ticket_pabs as pab', 'pc.pab_id', '=', 'pab.id')
                ->leftJoin('tkt_request_status as status', 'tpr.status_id', '=', 'status.id')
                ->leftJoin('companies as company','company.id','=','tpr.company_id');

        if(in_array(config('app.client'), ["ril"])) {
            $db->leftJoin('tkt_detail as tkt_d', 'tpr.ticket_id', '=','tkt_d.ticket_id');
        }
        $db->whereNull('tpr.deleted_at');
        $db->whereIn('tpr.company_id',$companyIds);
        $current_user = Auth::id();
        if($main_filter == "awaiting"){
            $db->whereIn('tpr.status_id',[1,2]);
        }
        if (!in_array($main_filter, ['myRequest', 'all','pendingrequests'], true)) {
            $db->join('tkt_approval_request as ar', function ($join) use ($current_user) {
                $join->on('tpr.id', '=', 'ar.pr_id')
                    ->where(function ($query) use ($current_user) {
                        $query->where('ar.user_id', $current_user)
                            ->orWhere('ar.delegated_user_id', $current_user);
                });
            });

            if($sr_status !== null && $sr_status != "") {
                $db->where('ar.approve_status', $sr_status)->whereIn('tpr.status_id',[1,2]);
            }
        }
        if (in_array(config('app.client'), ['ltts']) && in_array(config('app.sub_client'), ['live','dev']) && $main_filter == "pendingrequests") {
            $db->where('tpr.status_id', 2);
            $db->join('tkt_approval_request as ar3', function ($join) use ($current_user) {
                $join->on('tpr.id', '=', 'ar3.pr_id')
                    ->where(function ($query) use ($current_user) {
                        $query->where('ar3.user_id', $current_user)->orWhere('ar3.delegated_user_id', $current_user);
                        })
                        ->where('ar3.approve_status', 3)
                        ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(tpr.pab_id, CONCAT('$.\"', ar3.pab_id, '\"'))) != '1' ");

                });
            $db->distinct('tpr.id');
        }

        if ($main_filter == 'all') {
            $departmentIds = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();    
            $db->whereIn('tpr.department_id', $departmentIds);
            $created_by  = isset($req['created_by'])  ? trim($req['created_by'])  : null;
            $approval_id = isset($req['approval_id']) ? trim($req['approval_id']) : null;
            $pab_id      = isset($req['pab_id'])      ? trim($req['pab_id'])      : null;
            if ($created_by && $approval_id && $pab_id) {
                $db->join('tkt_approval_request as ar', function ($join) use ($created_by,$approval_id,$pab_id,$current_user) {
                    $join->on('tpr.id', '=', 'ar.pr_id')->where('ar.pab_id', $pab_id)->where('ar.approve_status', $approval_id)->where(function ($query) use ($created_by, $current_user) {
                        $query->where('ar.user_id', $created_by)->orWhere('ar.delegated_user_id', $current_user);
                    });
                });
            }
            
        }        
        
        if ($main_filter == "myRequest") {
            $db->where('tpr.creator_id', '=', $current_user);
        }

        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];

        $is_searching = false;
        if(isset($req["filters"])) {
            $filters = $req["filters"];
            if(isset($filters["status"]) && $filters['status'] && $filters['status'] != "null" && !empty(json_decode($filters['status']))) {
                $db->whereIn("tpr.status_id", json_decode($filters['status']));
            }
            if(isset($filters["pab"]) && $filters['pab'] && $filters['pab'] != "null") {
                $db->where("tpr.pab_id",'like',"%".$filters['pab']."%");
            }
            if(isset($filters["department"]) && $filters['department'] && $filters['department'] != "null" && !empty(json_decode($filters['department']))) {
                $db->whereIn("tpr.department_id", json_decode($filters['department']));
            }
            if(isset($filters["problem_category_id"]) && $filters['problem_category_id'] && $filters['problem_category_id'] != "null" && !empty(json_decode($filters['problem_category_id']))) {
                $db->whereIn("tpr.problem_category_id", json_decode($filters['problem_category_id']));
            }
            if(isset($filters["sub_category_id"]) && $filters['sub_category_id'] && $filters['sub_category_id'] != "null" && !empty(json_decode($filters['sub_category_id']))) {
                $db->whereIn("tpr.sub_category_id", json_decode($filters['sub_category_id']));
            }

            $based_on_possible = ['1'=> 'tpr.created_at', '5'=>'tpr.approved_at', '3'=>'tpr.updated_at'];
            if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 5 ) {
                if(isset($filters["from_date"]) && $filters["from_date"] && $filters["from_date"] != "null") {
                    $from_date = CommonHelper::getDateAs($filters["from_date"], "Y-m-d H:i:s", "d/m/Y H:i:s");
                    $to_date = CommonHelper::getDateAs($filters["to_date"], "Y-m-d H:i:s", "d/m/Y H:i:s");
                    if($from_date && $to_date) {
                        $db->whereBetween($based_on_possible[$filters['based_on']], [$from_date, $to_date]);
                    }
                }
            }

            $is_searching = true;
        }

        if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
            if(substr($search_key, 0, 1) == "#" && strlen($search_key) > 1) {
                $whereStr = sprintf('(tpr.procure_tag like "%1$s")', substr($search_key, 1));
            }
            else {
                $whereStr = sprintf('(tpr.procure_tag like "%%%1$s%%" or tpr.subject like "%%%1$s%%" or dep.name like "%%%1$s%%" or status.name like "%%%1$s%%" or concat_ws(" ", creator.first_name, creator.last_name, "@", creator.username) like "%%%1$s%%" or pab.name like "%%%1$s%%" or DATE_FORMAT(tpr.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(tpr.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $is_searching = true;
        }

        if($is_searching) {
            $return['filtered'] = $db->count();
        }

        $fields = [1=>'tpr.id', 2=>'creator_name', 3=>'status.name', 4=>'pab.name', 5=>'dep.name', 6=>'tpr.created_at', 7=>'tpr.updated_at'];
        if( isset($req["order"]["id"]) && array_key_exists($req["order"]["id"], $fields) && in_array($req["order"]["dir"], [1, 2]) ) {
            $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
            $db->orderBy( $fields[$req["order"]["id"]], $dir);
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filtered'] > ( $skip + $take ) ? 1 : 0;
        $return['pab'] = TicketPab::select('id', 'name')->whereIn('company_id',$companyIds)->get();

        $db->skip($skip);
        $db->take($take);
        
        $return_val["data_val"] = $db->get();
       
        foreach($return_val["data_val"] as $d) {
            $ticket = Ticket::find($d->ticket_id);
            $d->created_by = null;
            if(!empty($ticket) && !empty($ticket->created_by)) {
                $user = User::where('id', $ticket->created_by)->select('id',DB::raw('concat_ws(" ", first_name, last_name, "@", username) as creator_name'), 'email')->first();
                $d->created_by_name = !empty($user) ? $user->creator_name : null;
                $d->created_by = !empty($user) ? $user->id : null;

            }
            $date = [];
            if(isset($ticket->custom_fields) && $ticket->custom_fields != NULL) {
                foreach(json_decode($ticket->custom_fields,true) as $t) {
                    array_push($date, $t);
                }
            }
            $d->requestApprovalData =['approved_day' => $d->approved_day, 'start_date' => !empty($date) == true ? $date[0] : null, 'end_date' => !empty($date) == true ?  $date[1] : null ];
            if ($d->pab_id != null) {
                $arr = json_decode($d->pab_id, true);

                if (is_array($arr) && count($arr) > 0) {
                    $pabName = '';
                    foreach ($arr as $k => $v) {
                        $pab = TicketPab::find($k);
                        if ($pab) { // Ensure $pab exists
                            if ($pabName == '') {
                                $pabName = $pab->name;
                            } else {
                                $pabName .= " | " . $pab->name;
                            }
                        }
                    }
                    $d->pab = $pabName;

                    $notApprovedPAB = array_search("0", $arr);
                    if ($notApprovedPAB !== false) {
                        $pab = TicketPab::where("id", $notApprovedPAB)->first();
                        if (!empty($pab)) {
                            $d->pendingPab = $pab->name;
                        }
                    }
                }
            }            
        }
        $return["data"] = collect($return_val["data_val"])->unique('id')->values()->all();
        $return["page"] = $page;

        return response()->json($return);
    }

    public function getServiceRequestInfo(Request $request, $param) {
        $return = [
            "msg" => "Unable to get the request.",
            "status" => "fail"
        ];

        try {
            $request_id = $param;
            $pro_request = TicketProcureRequest::findOrFail($request_id);
            if(!empty($pro_request) && $pro_request->pab_id != null && !empty($pro_request->pab_id)) {
                $pro_request->create_at_format = CommonHelper::getDateAs($pro_request->created_at, "d M Y h:i a", "Y-m-d H:i:s");
                $array = (json_decode($pro_request->pab_id));
                foreach($array as $k => $val) {
                    $pab = TicketPab::find($k);
                    if(!empty($pab) && $pab->hierarchy_approval == 8) {
                        $systemApproval = true;
                    }
                }
            }
            if(in_array(config('app.client'),["rolepermission", "ltts", "grdemo"])){
                $pro_request->number_of_days = CommonHelper::getPCNumberOfDays($pro_request);
            }
            /* get role of current user */
            $get_role = optional(Auth::user()->procurementRole)->getRole();

            $get_pad = TicketPabMember::where('pab_id', '=', $pro_request->pab_id)->where('user_id', Auth::user()->id)->get();
            if(! $get_role) {
                $get_role = count($get_pad) ? "approver" : "";
            }

            if(isset($pro_request->sub_category_id) && $pro_request->sub_category_id != "" && $pro_request->sub_category_id != null) {
                $problem_category = ProblemCategory::where('id', $pro_request->sub_category_id)->withTrashed()->first();
            } else {
                $problem_category = ProblemCategory::where('id', $pro_request->problem_category_id)->withTrashed()->first();
            }

            if(empty($problem_category)) {
                $return = [
                    "msg" => "Problem Category not found for service request.",
                    "status" => "danger"
                ];
                return response()->json($return);
            }

            $vd = new stdClass;
            $vd->statuses = [];
            $vd->form_visible = new stdClass;
            $vd->form_visible->tr_update_form = true;

            $pro_request->status_name = TicketRequestStatus::select('id', 'name')->where('id', $pro_request->status_id)->first();
            $pro_request->created_at_format = date('d M Y H:i a', strtotime($pro_request->created_at));
            $pro_request->updated_at_format = date('d M Y H:i a', strtotime($pro_request->updated_at));
            switch ($pro_request->status_id) {
                case 1:
                    $temp_status = $problem_category->approval_required ? [2,5,6] : [5,6];
                    $vd->statuses = TicketRequestStatus::select('id', 'name')->whereIn('id', $temp_status)->get();
                    break;
                case 2:
                    $temp_status = $problem_category->approval_required ? [5,6] : [5,6];
                    $vd->statuses = TicketRequestStatus::select('id', 'name')->whereIn('id', $temp_status)->get();
                    $vd->form_visible->tr_update_form = true;
                    break;
                case 3:
                    $vd->form_visible->tr_update_form = false;
                    break;
                case 4:
                    $vd->form_visible->tr_update_form = false;
                    break;
                case 5:
                    $vd->form_visible->tr_update_form = false;
                    break;
                case 6:
                    $temp_status = $problem_category->approval_required ? [2, 5,6] : [2, 5,6];
                    $vd->statuses = TicketRequestStatus::select('id', 'name')->whereIn('id', $temp_status)->get();
                    break;
                default:
                    break;
            }

            $invoice_file = null;
            $device = null;
            $attachment_path = Attachment::getUrl('');
            $attachment_view = Attachment::getAppViewUrl();
            if($pro_request->device_id) {
                $device = Device::find($pro_request->device_id);
            }
            $embedded_attachments = Attachment::getEmbeddedAttachments($pro_request->ticket_id);
            $attachments = Attachment::where("ticket_id", $pro_request->id)->select("id", "original_file_name as name", 'extension as ext', DB::raw('case when id is not null then concat_ws("", "'.$attachment_path.'/",id) else "" end as attach_file_path'),DB::raw('case when id is not null then concat_ws("", "'.$attachment_view.'/",id) else "" end as attach_view'), DB::raw('case when thumbnail is not null then 1 else 0 end as thumb'))->whereNull("following_id")->get();
            $approval_requests = TicketApprovalRequest::select('tkt_approval_request.*', DB::raw('concat(u.first_name, " ", u.last_name) as approval_name'),  DB::raw('case when approve_status = 1 then "Approved" when approve_status = 2 then "Rejected" when approve_status = 3 then "Requested" when approve_status = 4 then "Waiting for Prior Level Approval" when approve_status = 5 then "Cancelled" end as status_name'), DB::raw('DATE_FORMAT(tkt_approval_request.created_at, "%d %b %Y %h:%i %p") as created_at_format'), DB::raw('concat(delegation.first_name, " ", delegation.last_name) as delegated_username'))
            ->leftjoin('users as u', 'u.id', 'tkt_approval_request.user_id')
            ->leftjoin('users as delegation', 'delegation.id', 'tkt_approval_request.delegated_user_id')
            ->where('pr_id', '=', $pro_request->id)->orderBy('hierarchy_level', 'asc')->get();
            $pabs = [];
            $pabArray = [];
            $arr = json_decode($pro_request->pab_id, true);
            if (is_array($arr) && count($arr) > 0) {
                $pabName = "";
                foreach ($arr as $k => $v) {
                    $pab = TicketPab::find($k);
                    if ($pab) {
                        if ($pabName == "") {
                            $pabName = $pab->name;
                        } else {
                            $pabName .= " | " . $pab->name;
                        }
                        array_push($pabArray, $pab);
                    }
                }
                $pro_request->pab_array = $pabArray;
                $pro_request->pab = $pabName;
                $notApprovedPAB = array_search("0", $arr);
                if ($notApprovedPAB !== false) {
                    $pab = TicketPab::where("id", $notApprovedPAB)->first();
                    if (!empty($pab)) {
                        $pro_request->pendingPab = $pab->name;
                    }
                }
            }
            $requestedForm = RequestedForm::select('tkt_requested_form.id','tkt_requested_form.form_id','tkt_requested_form.field_values','tkt_requested_form.request_id','tkt_requested_form.tmp_id','tkt_requested_form.created_by','tkt_requested_form.created_at','tkt_requested_form.updated_at','tkt_request_form_builder.form_name')->leftJoin('tkt_request_form_builder','tkt_request_form_builder.id','tkt_requested_form.form_id')->where('tkt_requested_form.request_id', $pro_request->ticket_id)->first();
            $myApproval = false;
            $checkMyApprovalRequired = TicketApprovalRequest::where([
                'pr_id' => $pro_request->id,
                'user_id' => Auth::user()->id
            ])->count();
            if($checkMyApprovalRequired > 0) {
                $myApproval = true;
            }
            if(in_array( $pro_request->status_id, [3,4,7])) {
                $pro_request->showCommentBox = false;
            } else {
                if(Auth::user()->hasAnyRole(['SuperAdmin', 'Admin'])) {
                    $pro_request->showCommentBox = true;
                } else {
                    $assigned_to =  $pro_request->assignedTo;
                    if($pro_request->creator_id == Auth::user()->id || $assigned_to->id == Auth::user()->id ) {
                        $pro_request->showCommentBox = true;
                    }
                    if($myApproval == true) {
                        $pro_request->showCommentBox = true;
                    }
                }
            }

            $st = Ticket::find($pro_request->ticket_id);
            $db = DB::table('tkt_followings as tf');
            $db->leftJoin('users as u', 'tf.updated_by', '=', 'u.id');
            $db->where('tf.ticket_id', '=', $st->id);
            $db->whereNull('tf.deleted_at');
            $db->whereNotNull('tf.remarks');
            if(config('app.client') == 'ril' || config('app.client') == 'rolepermission'){
                $pro_request->seat_no = optional($pro_request->ticketDetail)->seat_no;
            }

            if($st->wai() == 24) {
                $db->where(function($q) {
                    $q->whereNull('tf.is_note')->orWhere('tf.is_note', '!=', 1);
                });
            }

            $db->select('tf.remarks', 'tf.is_note', 'tf.id as tfid', 'tf.action_type', 'tf.merge_primary', 'tf.cc_emails', 'tf.is_service_request');
            $db->addSelect(DB::raw('IFNULL(concat(u.first_name, " ", u.last_name, " @ ", u.username), "System") as commenter'));
            $db->addSelect(DB::raw('DATE_FORMAT(tf.updated_at, "%d %b %y %h:%i %p") as updated_at_format'));
            $db->orderBy('tf.updated_at', 'asc');

            $tls = $db->get();
            if($tls && count($tls)) {
                $embedded_attachments = Attachment::getEmbeddedAttachments($st->id);
                foreach($tls as $tl) {
                    $tl->remarks = CommonHelper::renderTktContent($tl->remarks, $embedded_attachments);
                    $tl->attachments = Attachment::where('following_id', '=', $tl->tfid)->select('id', 'original_file_name as name', 'extension as ext', DB::raw('case when id is not null then concat_ws("", "'.$attachment_path.'/",id) else "" end as attach_file_path'),DB::raw('case when id is not null then concat_ws("", "'.$attachment_view.'/",id) else "" end as attach_view'), DB::raw('case when thumbnail is not null then 1 else 0 end as thumb'))->get();
                }
            }
            $embedded_attachments_array = array_values((array) $embedded_attachments);
            $ticket = Ticket::find($pro_request->ticket_id);
            $createdBy = null;
            if(!empty($ticket) && $ticket->created_by != null){
                $user = User::where('id', $ticket->created_by)->select('id',DB::raw('concat_ws(" ", first_name, last_name, "@", username) as creator_name'), 'email')->first();
                $createdBy = !empty($user) ? $user : null;
            }

            $date = [];
            if(isset($ticket->custom_fields) && $ticket->custom_fields != NULL) {
                foreach(json_decode($ticket->custom_fields,true) as $t) {
                    array_push($date, $t);
                }
            }
            $requestApprovalData =[
                'approved_day' => isset($pro_request->approved_day) ? $pro_request->approved_day : null,
                'start_date' => isset($date[0]) ? $date[0] : null,
                'end_date' => isset($date[1]) ? $date[1] : null
            ];
            $tkt_id = TicketProcureRequest::find($request_id)->ticket_id;
            if(in_array(config('app.client'), ["ltts", "grdemo", "rolepermission"])) {
                $approvalData = $this->checkApprovalDays($pro_request);
                $pro_request->no_of_approval_day = $approvalData['no_of_approval_day'];
                $pro_request->privilege_access = $approvalData['privilege_access'];
            }
            $return['customFieldsFromTableForDepartments'] = CommonHelper::getCustomFieldsValues('tickets', $tkt_id);
            $return['status'] = "success";
            $return['msg'] = "successfully get the request";
            $return['vd'] = $vd;
            $return['request'] = $pro_request;
            $return['creator'] = $pro_request->creator;
            $return['creator']['company_name'] = $pro_request->creator->job_type >0 ? $pro_request->creator->ex_user_company : $pro_request->creator->company->name;
            $return['created_by'] = $createdBy;
            $return['assigned_to'] = $pro_request->assignedTo;
            $return['device'] = $device;
            $return['approval_requests'] = $approval_requests;
            $return['embedded_attachments'] = $embedded_attachments_array;
            $return['attachments'] = $attachments;
            $return['comments'] = $tls;
            $return['pabs'] = $pabs;
            $return['myApproval'] = $myApproval;
            $return['request_form'] = $requestedForm;
            $return['request_approval_data'] = $requestApprovalData;
            $return['client'] = config('app.client');
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($return);
        }
    }

    public function updateRequestInfo(Request $request, $param) {
        $return = [
            "msg" => "Unable to get the request.",
            "status" => "fail"
        ];

        try {
            $get_request = TicketProcureRequest::findOrFail($param);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }

        $pro_request = $get_request;

        $data = $request->only("status_id", "comment");
        if(isset($data['status_id']) && $data['status_id'] == null) {
            $rules = [
                'comment' => 'required|string'
            ];
        } else {
            $rules = [
                'status_id' => 'nullable|integer|min:1|exists:tkt_request_status,id',
                'comment' => 'required|string'
            ];
        }

        $messages = [];
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        if(isset($pro_request->sub_category_id) && $pro_request->sub_category_id != "" && $pro_request->sub_category_id != null) {
            $problem_category = ProblemCategory::where('id', $pro_request->sub_category_id)->withTrashed()->first();
        } else {
            $problem_category = ProblemCategory::where('id', $pro_request->problem_category_id)->withTrashed()->first();
        }

        $alertnotify = null;
        if(Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }

        $history_log = "";
        $quotate_user_email = "";

        DB::beginTransaction();
        try {
            $st = Ticket::find($pro_request->ticket_id);

            /* validate cc emails */
            $cc_emails = [];
            if( $request->follow_cc == 1 ) {
                $get_cc_emails = Ticket::validateAllEmails($request->cc_emails);

                if(! $get_cc_emails["status"]) {
                    $return["msg"] = $get_cc_emails["invalid"] ? "Invalid e-mail(s) on CC list: " . $get_cc_emails["invalid"] : "Invalid e-mail(s) on CC list.";
                    return response()->json($return);
                }

                $cc_emails = $get_cc_emails["emails"];
                $st->updateMasterCcIfNewEmailFound($cc_emails);
            }

            $tf = new TktFollowing();
            $tf->ticket_id = $st->id;
            $tf->is_note = $request->is_note ? 1 : 0;
            $tf->updated_by = Auth::user()->id;
            $tf->cc_emails = $cc_emails && count($cc_emails) ? implode(",", $cc_emails) : null;
            $tf->action_type = 7; /* to indicate ticket commented */
            $tf->is_service_request = $tf->is_service_request = $request->is_service_request ? 0 : 1;

            $processResult = Attachment::processForB64Imgs($request->comment, $st->id, Auth::user()->id);
            $tf->remarks = $processResult['content'];

            if(! $tf->save()) {
                return response()->json($return);
            }

            $pro_request->updated_at = date('Y-m-d H:i:s');

            $ats = Attachment::where("ticket_id", "=", $st->id)->where("tmp_id", "like", $request->tmp_id."%")->get();
            if($ats && count($ats)) {
                foreach($ats as $at) {
                    $at->following_id = $tf->id;
                    $at->save();
                }
            }

            if( count($processResult['attachments']) ) {
                Attachment::whereIn('id', $processResult['attachments'])->update(['following_id' => $tf->id]);
            }

            if(!isset($data['status_id'])) {
                $history = new TicketRequestHistory();
                $history->user_id = Auth::user()->id;
                $history->pr_id = $pro_request->id;
                $history->action_type = 7;
                $history->change_info = "Added a comment on Service Request.";
                $history->comment = $request->comment;
                $history->save();
                $return["status"] = "success";
                $return["msg"] = "Service Request has been commented successfully";

                /** Code is for adding comments in ticket history */
                $tkt_update['ticket_id'] = $pro_request->ticket_id;
                $tkt_update['updated_by'] = Auth::user()->id;
                $tkt_update['is_note'] = $request->is_note;
                $tkt_update['action_type'] = 7;
                CommonHelper::ticketStatusHistory($tkt_update);
                /** Code ends here */

                $pab_ids = json_decode($pro_request->pab_id, true);
                $notApprovedPAB = array_search("0", $pab_ids);
                $pab = TicketPab::where("id", $notApprovedPAB)->first();

                // notify members about user comment
                try {
                    if(!empty($pab)) {
                        $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->pluck('user_id')->toArray();
                        $emails = [];
                        foreach ($pab_members as $id) {
                            $user = User::find($id);
                            if ($user->email != null) {
                                array_push($emails, $user->email);
                            }
                        }
                        if (Auth::user()->id != $pro_request->creator_id) {
                            $reqCreator = User::find($pro_request->creator_id);
                            if ($user->email != null) {
                                array_push($emails, $reqCreator->email);
                            }
                        }
                        if (config('mail.service_enabled')) {
                            Mail::to($emails)->send(new UserComment($pro_request, $request->comment, $tf));
                        }
                    }
                } catch (\Exception $ex) {
                    Log::error($ex->getMessage());
                }

                DB::commit();
                return $return;
            } else {
                $pro_request->status_id = $data["status_id"];
                $pro_request->updator_id = Auth::user()->id;
                if ($pro_request->save()) {
                    $pab_ids = json_decode($pro_request->pab_id, true);
                    $notApprovedPAB = array_search("0", $pab_ids);
                    $pab = TicketPab::where("id", $notApprovedPAB)->first();
                    $created_user = User::find($pro_request->creator_id);
                    $pab_member_emails = [];

                    $pro_not_mem = array_unique(array_merge([$created_user->email], [$quotate_user_email], $pab_member_emails));
                    $creators = array_filter($pro_not_mem);
                    switch ($pro_request->status_id) {
                        case 1:
                            break;
                        case 2:
                            if ($problem_category->approval_required == 1) {
                                // $this->_sendApprovalRequest($request, $pro_request);
                                /*try {
                                    if (config('mail.service_enabled') && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                         Mail::to($creators)->cc($alertnotify)->send(new ApproverInfo($pro_request, $user, $pab_username));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                    return response()->json($return);
                                }*/
                            }
                            break;
                        case 5:
                            break;
                        default:
                            break;
                    }

                    $return["status"] = "success";
                    $return["msg"] = "Request Status has been changed successfully";

                    if ($history_log == "") {
                        $history_log = "Request Status has been changed to \"" . $pro_request->status->name . "\"";
                    }

                    $history = new TicketRequestHistory();
                    $history->user_id = Auth::user()->id;
                    $history->pr_id = $pro_request->id;
                    $history->change_info = $history_log;
                    $history->comment = $request->comment;
                    $history->save();

                    /** Code is for adding comments in ticket history */
                    $tkt_update['ticket_id'] = $pro_request->ticket_id;
                    $tkt_update['updated_by'] = Auth::user()->id;
                    $tkt_update['is_note'] = $request->is_note;
                    $tkt_update['action_type'] = 7;
                    CommonHelper::ticketStatusHistory($tkt_update);
                    /** Code ends here */

                    // When SR cancelled cancel all Approver request for users.
                    if($data['status_id'] == 5) {
                        $updateRequestStatus = TicketApprovalRequest::where('pr_id', $pro_request->id)->update(['approve_status' => 5]);
                    }

                    try {
                        if (config('mail.service_enabled') && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($creators)->cc($alertnotify)->send(new StatusChange($pro_request, $pab));
                        }
                    } catch (\Exception $ex) {
                        Log::error($ex->getMessage());
                        return response()->json($return);
                    }

                    DB::commit();
                    Log::info("App - updateRequestInfo service request id:" . $pro_request->id . " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
                    return response()->json($return);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            $return['msg'] = $e->getMessage();
            return response()->json($return);
        }
    }

    public function _sendApprovalRequest(Request $request, &$pro_request) {
        $msg = ['status' => 'failure', 'msg' => ''];
        $problem_category = ProblemCategory::find($pro_request->problem_category_id);
        $pab_id = $problem_category->pab_id;
        $pab = TicketPab::find($pab_id);
        if($pab->hierarchy_approval != 4) {
            if (!$pab || $pab->totMembers() < 1) {
                $msg['msg'] = 'No user found send request on chosen PAB';
                return $msg;
            }

            $pab_members = [];
            if ($pab->hierarchy_approval == 1) {
                $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
            } else {
                $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
            }

            foreach ($pab_members as $k => $member) {
                $approvalRequest = new TicketApprovalRequest();
                $approvalRequest->pr_id = $pro_request->id;
                $approvalRequest->pab_id = $pab_id;
                $approvalRequest->user_id = $member->user_id;
                if ($pab->hierarchy_approval == 1) {
                    $approvalRequest->hierarchy_level = $member->hierarchy_level;
                    $approvalRequest->hierarchy_approval = $pab->hierarchy_approval;

                    // send approve request start from first level
                    if ($k == 0) {
                        $approvalRequest->approve_status = 3;
                    } else {
                        $approvalRequest->approve_status = 4;
                    }
                } else {
                    $approvalRequest->approve_status = 3; // symultanously send request all member
                }

                $approvalRequest->save();
            }
        } else {
            $usr = User::find($request->creator_id);

            $approvalRequest = new TicketApprovalRequest();
            $approvalRequest->pr_id = $pro_request->id;
            $approvalRequest->pab_id = $pab_id;
            $approvalRequest->user_id = $usr->manager->id;
            $approvalRequest->approve_status = 3;
            $approvalRequest->save();
        }

        $msg['msg'] = 'Ticket Approval Request(s) has been send successfully.';
        $msg['status'] = 'success';

        $history = new TicketRequestHistory();
        $history->user_id = Auth::user()->id;
        $history->pr_id = $pro_request->id;
        $history->change_info = $msg['msg'];
        $history->save();
    }

    public function requestApprove(Request $request, $pr_id) {

        $return = [
            "msg" => "Unable to update the approval status.",
            "status" => "failure"
        ];

        try {
            $get_request = TicketProcureRequest::findOrFail($pr_id);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }

        $pro_request = $get_request;
        if(isset($pro_request->sub_category_id) && $pro_request->sub_category_id != "") {
            $sub_category = ProblemCategory::find($pro_request->sub_category_id);
            if(!empty($sub_category) && $sub_category->approval_required == 1 && $sub_category->pab_id != "") {
                $problem_category = ProblemCategory::find($pro_request->sub_category_id);
            } else {
                $problem_category = ProblemCategory::find($pro_request->problem_category_id);
            }
        } else {
            $problem_category = ProblemCategory::find($pro_request->problem_category_id);
        }

        $data = $request->only('approve_status', 'comments', 'approve_request_id');

        $rules = [
            'approve_request_id' => 'required|integer|exists:tkt_approval_request,id',
            'approve_status' => 'required|integer',
            'comments' => 'required|string|max:2000',
        ];
        $msgs = [
            'approve_request_id.required' => 'Please select an approval request.',
            'approve_request_id.exists'   => 'The selected approval request does not exist.',
            'approve_status.required'     => 'Please select an approval status.',
            'approve_status.integer'      => 'Invalid approval status.',
            'comments.required'           => 'Please enter your comments.',
            'comments.max'                => 'Comments cannot exceed 2000 characters.',
        ];

        $validator = Validator::make($data, $rules, $msgs);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        DB::beginTransaction();
        try {
            $approvalRequest = TicketApprovalRequest::find($data['approve_request_id']);
            if($approvalRequest->user_id != Auth::user()->id) {
                $return["msg"] = "The user is not authorized";
                return response()->json($return);
            }
            if($approvalRequest->approve_status == 1 ) {
                $return["msg"] = trans('validation.approval_message');
                return response()->json($return);
            }
            if($approvalRequest->approve_status == 2) {
                $return["msg"] = trans('validation.reject_message');
                return response()->json($return);
            }
            $approvalRequest->approve_status = $data["approve_status"];
            $approvalRequest->comments = trim($data["comments"]);
            if(isset($approvalRequest->delegated_user_id) && $approvalRequest->delegated_user_id != NULL && $approvalRequest->delegated_user_id == Auth::user()->id) {
                $approvalRequest->delegated_approved_user_id = $approvalRequest->delegated_user_id;
            }
            $alertnotify = null;
            if (Settings::first()->alerts_enabled == 1) {
                $alertnotify = CommonHelper::getGlobalAlertEmail();
            }
            $user = User::find($pro_request->creator_id);
            $enableUsbRequest_old = [];
            $departmentCustomFieldset = Department::find($pro_request->department_id);
            if($departmentCustomFieldset->name == "IT Service Request" || $departmentCustomFieldset->name == "IT Service Request Overseas") {
                $enableUsbRequest_old = ProblemCategory::enableUsbRequest([$departmentCustomFieldset->id]);
            }
            $enableUsbRequests_new = ProblemCategory::where('privilege_access',1)->pluck('id')->toArray();
            $enableUsbRequests = array_merge($enableUsbRequest_old,$enableUsbRequests_new);
            $enableCategory = array_values(array_unique($enableUsbRequests));
            $creators = [];
            $pab_member_emails = [];
            if ($approvalRequest->save()) {
                $pab_ids = json_decode($pro_request->pab_id, true);
                $notApprovedPAB = array_search("0", $pab_ids);
                $pab = TicketPab::where("id", $notApprovedPAB)->first();

                /* level by level approval */
                if (!empty($pab) && $pab->hierarchy_approval == 1) {
                    if ($approvalRequest->approve_status == 1) {
                        $getNextLevel = TicketApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->where('hierarchy_level', '>', $approvalRequest->hierarchy_level)->orderBy('hierarchy_level', 'asc')->get();
                        if (count($getNextLevel)) {
                            /* here, send approve request mail to next level people */
                            $getNextLevel[0]->approve_status = 3;
                            $getNextLevel[0]->save();
                            $pab_username = User::find($getNextLevel[0]->user_id);
                            $pab_username_u[] = User::find($getNextLevel[0]->user_id);
                            $pab_member_emails[] = $pab_username_u[0]->email;

                            $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                            $creators = array_filter($pro_not_mem);
                            /*try {
                                if (config('mail.service_enabled') && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($creators)->cc($alertnotify)->send(new ApproverInfo($pro_request, $user, $pab_username));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }*/
                            $notificationText = "Service Request #".$pro_request->procure_tag." is waiting for your approval!";
                            $data = [
                                'title' => $notificationText,
                                'data' => $pro_request,
                                'notify' => [$getNextLevel[0]->user_id],
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("approval request sent to user id push notification:" . $pro_request->id . " notification error " . json_encode($response));
                                }
                            }

                        } else {
                            foreach($pab_ids as $key => $val) {
                                if($key == $pab->id) {
                                    $pab_ids[$key] = 1;
                                }
                            }
                            $proRequest = TicketProcureRequest::findOrFail($pr_id);
                            $proRequest->pab_id = json_encode($pab_ids);
                            if(isset($request->approved_day) && $request->approved_day != '') {
                                $proRequest->approved_day = $request->approved_day;
                            }
                            $proRequest->save();
                            $pro_request->pab_id = json_encode($pab_ids);

                            $pab_ids = json_decode($proRequest->pab_id, true);
                            $notApprovedPAB = array_search("0", $pab_ids);
                            $pab = TicketPab::where("id", $notApprovedPAB)->first();
                            if(!empty($pab)) {
                                $this->_sendApprovalRequestLoop($pro_request);
                            } else {
                                $pro_request->status_id = 3;
                                $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                                $now = Carbon::now(config('app.timezone'));
                                $config = Config::first();
                                $holidays = Holiday::getHolidaysFrom($now->format('Y-m-d'));

                                $ticketObj = Ticket::where('id', $pro_request->ticket_id)->first();
                                $ticketObj->subject = $pro_request->subject;
                                $ticketObj->content = $pro_request->content;
                                $ticketObj->status_id = 1;
                                $ticketObj->department_id = $pro_request->department_id;
                                $ticketObj->problem_category_id = $pro_request->problem_category_id;
                                $ticketObj->sub_category_id = $pro_request->sub_category_id;
                                $ticketObj->assigned_to = $ticketObj->assignByHirarchy();
                                $ticketObj->priority_id = $pro_request->priority_id;
                                $ticketObj->device_id = $pro_request->device_id;
                                $ticketObj->tat = $pro_request->tat;
                                $ticketObj->tat_expire = $config->calculateAdvancedTat($pro_request->tat, $now, $holidays);
                                $ticketObj->ac_email_id = $pro_request->ac_email_id;
                                $ticketObj->is_temp = null;
                                $ticketObj->created_at = $now->format('Y-m-d H:i:s');
                                $ticketObj->location_id = $pro_request->location_id;
                                $ticketObj->save();
                                $ticketObjNew = Ticket::where('id', $pro_request->ticket_id)->first();
                                $this->ticketHistory($ticketObj);
                                $pro_request->assigned_to = $ticketObj->assigned_to;
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        $cc_emails = [];
                                        if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                            $manager = User::find($user->manager_id);
                                            $cc_emails[] = $manager->email;
                                        }
                                        if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                            Mail::to($user->email)->cc($cc_emails)->send(new TicketRequestApproved($pro_request, $user, $pab));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }

                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                            $cc_emails = [];
                                            foreach ($alertnotify as $email) {
                                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                    $cc_emails[] = $email;
                                                }
                                            }
                                            Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                        } else {
                                            Mail::to($user->email)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }

                                if($ticketObj->assigned_to) {
                                    $user = User::find($ticketObj->assigned_to);
                                    if(config('mail.service_enabled') && $user && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                        try {
                                            if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                                $cc_emails = [];
                                                foreach ($alertnotify as $email) {
                                                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                        $cc_emails[] = $email;
                                                    }
                                                }
                                                Mail::to($user->email)->cc($cc_emails)->queue(new IntimateAssigned($ticketObj, $user));
                                            } else {
                                                Mail::to($user->email)->queue(new IntimateAssigned($ticketObj, $user));
                                            }
                                        } catch (\Exception $ex) {
                                            Log::error($ex->getMessage());
                                        }
                                    }
                                }

                                $triggerObj = TicketTrigger::where('status',1)->count();
                                if($triggerObj > 0) {
                                    // Ticket generate apply trigger.
                                    event(new TicketCreated($ticketObj));
                                }

                                // send push notification
                                $notify_people = [];
                                array_push($notify_people, $ticketObj->creator_id);
                                $notificationText = "New Ticket #".$ticketObj->id." has been created successfully!";
                                $tkt_config = Config::first();
                                $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                                $data = [
                                    'title' => $notificationText,
                                    'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($data);
                                if($sendNotifications != false) {
                                    $response = json_decode($sendNotifications);
                                    if(isset($response->failure) && $response->failure == 1) {
                                        Log::error("service request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                    }
                                }

                                // send push notification to all technician which are in this pipeline
                                $notify_peoples = [];
                                if(isset($ticketObj->assigned_to) && $ticketObj->assigned_to != NULL){
                                    array_push($notify_peoples, $ticketObj->assigned_to);
                                } else {
                                    $notify_peoples = Privilege::getHandlersByDepartment($ticketObj->department_id);
                                }
                                $notify_people = $notify_peoples;
                                $notificationTicketText = "New Ticket #".$ticketObj->id." has been created successfully!";
                                $tkt_config = Config::first();

                                $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                                $dataT = [
                                    'title' => $notificationTicketText,
                                    'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($dataT);
                                $sendNotifications = CommonHelper::sendWhatsappNotification($dataT);
                                if($sendNotifications != false) {
                                    $response = json_decode($sendNotifications);
                                    if(isset($response->failure) && $response->failure == 1) {
                                        Log::error("create service Request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                    }
                                }
                            }
                        }
                    } else {
                        $pro_request->status_id = 4;
                        $pro_request->approved_at = null;
                        if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager = User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL) && in_array($pro_request->problem_category_id,$enableCategory)) {
                                    Mail::to($user->email)->cc($cc_emails)->queue(new TicketRequestRejected($pro_request, $user, $pab, $request->approved_day, $approvalRequest));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                    }
                } /* minimum approval */
                elseif (!empty($pab) && $pab->hierarchy_approval == 2) {
                    if ($approvalRequest->approve_status == 1) {
                        if ($pab->required_minimum_approvals > 1) {
                            $all_app_reqs = TicketApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->orderBy('id', 'asc')->get();

                            $approves_count = 0;
                            foreach ($all_app_reqs as $req) {
                                if ($req->approve_status == 1) {
                                    $approves_count++;
                                    $pab_username = User::find($all_app_reqs[0]->user_id);
                                    $pab_username_u[] = User::find($all_app_reqs[0]->user_id);
                                    $pab_member_emails[] = $pab_username_u[0]->email;

                                    $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                    $creators = array_filter($pro_not_mem);
                                    /*try {
                                        if (config('mail.service_enabled') && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                            Mail::to($creators)->cc($alertnotify)->send(new ApproverInfo($pro_request, $user, $pab_username));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }*/
                                }
                            }

                            if ($pab->required_minimum_approvals <= $approves_count) {

                                foreach($pab_ids as $key => $val) {
                                    if($key == $pab->id) {
                                        $pab_ids[$key] = 1;
                                    }
                                }
                                $proRequest = TicketProcureRequest::findOrFail($pr_id);
                                $proRequest->pab_id = json_encode($pab_ids);
                                if(isset($request->approved_day) && $request->approved_day != '') {
                                    $proRequest->approved_day = $request->approved_day;
                                }
                                $proRequest->save();
                                $pro_request->pab_id = json_encode($pab_ids);

                                $pab_ids = json_decode($proRequest->pab_id, true);
                                $notApprovedPAB = array_search("0", $pab_ids);
                                $pab = TicketPab::where("id", $notApprovedPAB)->first();
                                if(!empty($pab)) {
                                    $this->_sendApprovalRequestLoop($pro_request);
                                } else {
                                    $pro_request->status_id = 3;
                                    $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                                    $user = User::find($pro_request->creator_id);
                                    $now = Carbon::now(config('app.timezone'));
                                    $config = Config::first();
                                    $holidays = Holiday::getHolidaysFrom($now->format('Y-m-d'));

                                    $ticketObj = Ticket::where('id', $pro_request->ticket_id)->first();
                                    $ticketObj->subject = $pro_request->subject;
                                    $ticketObj->content = $pro_request->content;
                                    $ticketObj->status_id = 1;
                                    $ticketObj->department_id = $pro_request->department_id;
                                    $ticketObj->problem_category_id = $pro_request->problem_category_id;
                                    $ticketObj->sub_category_id = $pro_request->sub_category_id;
                                    $ticketObj->assigned_to = $ticketObj->assignByHirarchy();
                                    $ticketObj->priority_id = $pro_request->priority_id;
                                    $ticketObj->device_id = $pro_request->device_id;
                                    $ticketObj->tat = $pro_request->tat;
                                    $ticketObj->tat_expire = $config->calculateAdvancedTat($pro_request->tat, $now, $holidays);
                                    $ticketObj->ac_email_id = $pro_request->ac_email_id;
                                    $ticketObj->is_temp = null;
                                    $ticketObj->created_at = $now->format('Y-m-d H:i:s');
                                    $ticketObj->location_id = $pro_request->location_id;
                                    $ticketObj->save();
                                    $ticketObjNew = Ticket::where('id', $pro_request->ticket_id)->first();
                                    $this->ticketHistory($ticketObj);
                                    $pro_request->assigned_to = $ticketObj->assigned_to;
                                    if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                        try {
                                            $cc_emails = [];
                                            if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                                $manager = User::find($user->manager_id);
                                                $cc_emails[] = $manager->email;
                                            }
                                            if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                                Mail::to($user->email)->cc($cc_emails)->send(new TicketRequestApproved($pro_request, $user, $pab));
                                            }
                                        } catch (\Exception $ex) {
                                            Log::error($ex->getMessage());
                                        }
                                    }

                                    if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                        try {
                                            if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                                $cc_emails = [];
                                                foreach ($alertnotify as $email) {
                                                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                        $cc_emails[] = $email;
                                                    }
                                                }
                                                Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                            } else {
                                                Mail::to($user->email)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                            }
                                        } catch (\Exception $ex) {
                                            Log::error($ex->getMessage());
                                        }
                                    }

                                    if($ticketObj->assigned_to) {
                                        $user = User::find($ticketObj->assigned_to);
                                        if(config('mail.service_enabled') && $user && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                            try {
                                                if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                                    $cc_emails = [];
                                                    foreach ($alertnotify as $email) {
                                                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                            $cc_emails[] = $email;
                                                        }
                                                    }
                                                    Mail::to($user->email)->cc($cc_emails)->queue(new IntimateAssigned($ticketObj, $user));
                                                } else {
                                                    Mail::to($user->email)->queue(new IntimateAssigned($ticketObj, $user));
                                                }
                                            } catch (\Exception $ex) {
                                                Log::error($ex->getMessage());
                                            }
                                        }
                                    }

                                    $triggerObj = TicketTrigger::where('status',1)->count();
                                    if($triggerObj > 0) {
                                        // Ticket generate apply trigger.
                                        event(new TicketCreated($ticketObj));
                                    }
                                    // send push notification
                                    $notify_people = [];
                                    array_push($notify_people, $ticketObj->creator_id);
                                    $notificationText = "New Ticket #".$ticketObj->id." has been created successfully!";
                                    $tkt_config = Config::first();
                                    $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                                    $data = [
                                        'title' => $notificationText,
                                        'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                        'notify' => $notify_people,
                                    ];
                                    $sendNotifications = CommonHelper::sendPushNotification($data);
                                    if($sendNotifications != false) {
                                        $response = json_decode($sendNotifications);
                                        if(isset($response->failure) && $response->failure == 1) {
                                            Log::error("service request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                        }
                                    }

                                    // send push notification to all technician which are in this pipeline
                                    $notify_peoples = [];
                                    if(isset($ticketObj->assigned_to) && $ticketObj->assigned_to != NULL){
                                        array_push($notify_peoples, $ticketObj->assigned_to);
                                    } else {
                                        $notify_peoples = Privilege::getHandlersByDepartment($ticketObj->department_id);
                                    }
                                    $notify_people = $notify_peoples;
                                    $notificationTicketText = "New Ticket #".$ticketObj->id." has been created successfully!";
                                    $tkt_config = Config::first();

                                    $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                                    $dataT = [
                                        'title' => $notificationTicketText,
                                        'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                        'notify' => $notify_people,
                                    ];
                                    $sendNotifications = CommonHelper::sendPushNotification($dataT);
                                    $sendNotifications = CommonHelper::sendWhatsappNotification($dataT);
                                    if($sendNotifications != false) {
                                        $response = json_decode($sendNotifications);
                                        if(isset($response->failure) && $response->failure == 1) {
                                            Log::error("create service Request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                        }
                                    }
                                }
                            }
                        } else {
                            foreach($pab_ids as $key => $val) {
                                if ($key == $pab->id) {
                                    $pab_ids[$key] = 1;
                                }
                            }
                            $proRequest = TicketProcureRequest::findOrFail($pr_id);
                            $proRequest->pab_id = json_encode($pab_ids);
                            if(isset($request->approved_day) && $request->approved_day != '') {
                                $proRequest->approved_day = $request->approved_day;
                            }
                            $proRequest->save();
                            $pro_request->pab_id = json_encode($pab_ids);

                            $pab_ids = json_decode($proRequest->pab_id, true);
                            $notApprovedPAB = array_search("0", $pab_ids);
                            $pab = TicketPab::where("id", $notApprovedPAB)->first();
                            if(!empty($pab)) {
                                $this->_sendApprovalRequestLoop($pro_request);
                            } else {
                                $pro_request->status_id = 3;
                                $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                                $user = User::find($pro_request->creator_id);
                                $now = Carbon::now(config('app.timezone'));
                                $config = Config::first();
                                $holidays = Holiday::getHolidaysFrom($now->format('Y-m-d'));

                                $ticketObj = Ticket::where('id', $pro_request->ticket_id)->first();
                                $ticketObj->subject = $pro_request->subject;
                                $ticketObj->content = $pro_request->content;
                                $ticketObj->status_id = 1;
                                $ticketObj->department_id = $pro_request->department_id;
                                $ticketObj->problem_category_id = $pro_request->problem_category_id;
                                $ticketObj->sub_category_id = $pro_request->sub_category_id;
                                $ticketObj->assigned_to = $ticketObj->assignByHirarchy();
                                $ticketObj->priority_id = $pro_request->priority_id;
                                $ticketObj->device_id = $pro_request->device_id;
                                $ticketObj->tat = $pro_request->tat;
                                $ticketObj->tat_expire = $config->calculateAdvancedTat($pro_request->tat, $now, $holidays);
                                $ticketObj->ac_email_id = $pro_request->ac_email_id;
                                $ticketObj->is_temp = null;
                                $ticketObj->created_at = $now->format('Y-m-d H:i:s');
                                $ticketObj->location_id = $pro_request->location_id;
                                $ticketObj->save();
                                $ticketObjNew = Ticket::where('id', $pro_request->ticket_id)->first();
                                $this->ticketHistory($ticketObj);
                                $pro_request->assigned_to = $ticketObj->assigned_to;
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        $cc_emails = [];
                                        if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                            $manager = User::find($user->manager_id);
                                            $cc_emails[] = $manager->email;
                                        }
                                        if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                            Mail::to($user->email)->cc($cc_emails)->send(new TicketRequestApproved($pro_request, $user, $pab));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }

                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                            $cc_emails = [];
                                            foreach ($alertnotify as $email) {
                                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                    $cc_emails[] = $email;
                                                }
                                            }
                                            Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                        } else {
                                            Mail::to($user->email)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }

                                if($ticketObj->assigned_to) {
                                    $user = User::find($ticketObj->assigned_to);
                                    if(config('mail.service_enabled') && $user && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                        try {
                                            if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                                $cc_emails = [];
                                                foreach ($alertnotify as $email) {
                                                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                        $cc_emails[] = $email;
                                                    }
                                                }
                                                Mail::to($user->email)->cc($cc_emails)->queue(new IntimateAssigned($ticketObj, $user));
                                            } else {
                                                Mail::to($user->email)->queue(new IntimateAssigned($ticketObj, $user));
                                            }
                                        } catch (\Exception $ex) {
                                            Log::error($ex->getMessage());
                                        }
                                    }
                                }

                                $triggerObj = TicketTrigger::where('status',1)->count();
                                if($triggerObj > 0) {
                                    // Ticket generate apply trigger.
                                    event(new TicketCreated($ticketObj));
                                }
                                // send push notification
                                $notify_people = [];
                                array_push($notify_people, $ticketObj->creator_id);
                                $notificationText = "New Ticket #".$ticketObj->id." has been created successfully!";
                                $tkt_config = Config::first();
                                $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                                $data = [
                                    'title' => $notificationText,
                                    'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($data);
                                if($sendNotifications != false) {
                                    $response = json_decode($sendNotifications);
                                    if(isset($response->failure) && $response->failure == 1) {
                                        Log::error("service request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                    }
                                }

                                // send push notification to all technician which are in this pipeline
                                $notify_peoples = [];
                                if(isset($ticketObj->assigned_to) && $ticketObj->assigned_to != NULL){
                                    array_push($notify_peoples, $ticketObj->assigned_to);
                                } else {
                                    $notify_peoples = Privilege::getHandlersByDepartment($ticketObj->department_id);
                                }
                                $notify_people = $notify_peoples;
                                $notificationTicketText = "New Ticket #".$ticketObj->id." has been created successfully!";
                                $tkt_config = Config::first();

                                $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                                $dataT = [
                                    'title' => $notificationTicketText,
                                    'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($dataT);
                                $sendNotifications = CommonHelper::sendWhatsappNotification($dataT);
                                if($sendNotifications != false) {
                                    $response = json_decode($sendNotifications);
                                    if(isset($response->failure) && $response->failure == 1) {
                                        Log::error("create service Request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                    }
                                }
                            }
                        }
                    } else {
                        $pro_request->status_id = 4;
                        $pro_request->approved_at = null;
                        if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager = User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL) && in_array($pro_request->problem_category_id,$enableCategory)) {
                                    Mail::to($user->email)->cc($cc_emails)->queue(new TicketRequestRejected($pro_request, $user, $pab, $request->approved_day, $approvalRequest));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                    }
                } /* group approval - means all have to approve */
                elseif (!empty($pab) && $pab->hierarchy_approval == 3 || $pab->hierarchy_approval == 9 || $pab->hierarchy_approval == 10) {
                    if ($approvalRequest->approve_status == 1) {
                        $all_app_reqs = TicketApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->get();

                        $tot_approval_reqs = count($all_app_reqs);
                        $approves_count = 0;
                        foreach ($all_app_reqs as $req) {
                            if ($req->approve_status == 1) {
                                $approves_count++;
                                $pab_username = User::find($all_app_reqs[0]->user_id);
                                $pab_username_u[] = User::find($all_app_reqs[0]->user_id);
                                $pab_member_emails[] = $pab_username_u[0]->email;

                                $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                $creators = array_filter($pro_not_mem);
                                /*try {
                                    if (config('mail.service_enabled') && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($creators)->cc($alertnotify)->send(new ApproverInfo($pro_request, $user, $pab_username));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }*/
                            }
                        }

                        if($tot_approval_reqs == $approves_count) {
                            foreach($pab_ids as $key => $val) {
                                if($key == $pab->id) {
                                    $pab_ids[$key] = 1;
                                }
                            }
                            $proRequest = TicketProcureRequest::findOrFail($pr_id);
                            $proRequest->pab_id = json_encode($pab_ids);
                            if(isset($request->approved_day) && $request->approved_day != '') {
                                $proRequest->approved_day = $request->approved_day;
                            }
                            $proRequest->save();
                            $pro_request->pab_id = json_encode($pab_ids);

                            $pab_ids = json_decode($proRequest->pab_id, true);
                            $notApprovedPAB = array_search("0", $pab_ids);
                            $pabNew = TicketPab::where("id", $notApprovedPAB)->first();
                            if(!empty($pabNew)) {
                                $this->_sendApprovalRequestLoop($pro_request);
                            } else {
                                $pro_request->status_id = 3;
                                $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                                $user = User::find($pro_request->creator_id);
                                $now = Carbon::now(config('app.timezone'));
                                $config = Config::first();
                                $holidays = Holiday::getHolidaysFrom($now->format('Y-m-d'));

                                $ticketObj = Ticket::where('id', $pro_request->ticket_id)->first();
                                $ticketObj->subject = $pro_request->subject;
                                $ticketObj->content = $pro_request->content;
                                $ticketObj->status_id = 1;
                                $ticketObj->department_id = $pro_request->department_id;
                                $ticketObj->problem_category_id = $pro_request->problem_category_id;
                                $ticketObj->sub_category_id = $pro_request->sub_category_id;
                                $ticketObj->assigned_to = $ticketObj->assignByHirarchy();
                                $ticketObj->priority_id = $pro_request->priority_id;
                                $ticketObj->device_id = $pro_request->device_id;
                                $ticketObj->tat = $pro_request->tat;
                                $ticketObj->tat_expire = $config->calculateAdvancedTat($pro_request->tat, $now, $holidays);
                                $ticketObj->ac_email_id = $pro_request->ac_email_id;
                                $ticketObj->is_temp = null;
                                $ticketObj->created_at = $now->format('Y-m-d H:i:s');
                                $ticketObj->location_id = $pro_request->location_id;
                                $ticketObj->save();
                                $ticketObjNew = Ticket::where('id', $pro_request->ticket_id)->first();
                                $this->ticketHistory($ticketObjNew);
                                $pro_request->assigned_to = $ticketObj->assigned_to;
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        $cc_emails = [];
                                        if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                            $manager = User::find($user->manager_id);
                                            $cc_emails[] = $manager->email;
                                        }
                                        if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                            Mail::to($user->email)->cc($cc_emails)->send(new TicketRequestApproved($pro_request, $user, $pab));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }

                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                            $cc_emails = [];
                                            foreach ($alertnotify as $email) {
                                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                    $cc_emails[] = $email;
                                                }
                                            }
                                            Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                        } else {
                                            Mail::to($user->email)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }

                                if($ticketObj->assigned_to) {
                                    $user = User::find($ticketObj->assigned_to);
                                    if(config('mail.service_enabled') && $user && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                        try {
                                            if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                                $cc_emails = [];
                                                foreach ($alertnotify as $email) {
                                                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                        $cc_emails[] = $email;
                                                    }
                                                }
                                                Mail::to($user->email)->cc($cc_emails)->queue(new IntimateAssigned($ticketObj, $user));
                                            } else {
                                                Mail::to($user->email)->queue(new IntimateAssigned($ticketObj, $user));
                                            }
                                        } catch (\Exception $ex) {
                                            Log::error($ex->getMessage());
                                        }
                                    }
                                }

                                $triggerObj = TicketTrigger::where('status',1)->count();
                                if($triggerObj > 0) {
                                    // Ticket generate apply trigger.
                                    event(new TicketCreated($ticketObj));
                                }
                               
                                // send push notification
                                $notify_people = [];
                                array_push($notify_people, $ticketObj->creator_id);
                                $notificationText = "New Ticket #".$ticketObj->id." has been created successfully!";
                                $tkt_config = Config::first();
                                $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                                $data = [
                                    'title' => $notificationText,
                                    'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($data);
                                if($sendNotifications != false) {
                                    $response = json_decode($sendNotifications);
                                    if(isset($response->failure) && $response->failure == 1) {
                                        Log::error("service request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                    }
                                }

                                // send push notification to all technician which are in this pipeline
                                $notify_peoples = [];
                                if(isset($ticketObj->assigned_to) && $ticketObj->assigned_to != NULL){
                                    array_push($notify_peoples, $ticketObj->assigned_to);
                                } else {
                                    $notify_peoples = Privilege::getHandlersByDepartment($ticketObj->department_id);
                                }
                                $notify_people = $notify_peoples;
                                $notificationTicketText = "New Ticket #".$ticketObj->id." has been created successfully!";
                                $tkt_config = Config::first();

                                $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                                $dataT = [
                                    'title' => $notificationTicketText,
                                    'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($dataT);
                                $sendNotifications = CommonHelper::sendWhatsappNotification($dataT);
                                if($sendNotifications != false) {
                                    $response = json_decode($sendNotifications);
                                    if(isset($response->failure) && $response->failure == 1) {
                                        Log::error("create service Request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                    }
                                }
                            }
                        }
                    } else {
                        $pro_request->status_id = 4;
                        $pro_request->approved_at = null;
                        if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager = User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL) && in_array($pro_request->problem_category_id,$enableCategory)) {
                                    Mail::to($user->email)->cc($cc_emails)->queue(new TicketRequestRejected($pro_request, $user, $pab, $request->approved_day, $approvalRequest));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                    }
                } /* Manager Approval - means manager approval required of SR Creator */
                elseif (!empty($pab) && $pab->hierarchy_approval == 4) {
                    if ($approvalRequest->approve_status == 1) {
                        foreach($pab_ids as $key => $val) {
                            if($key == $pab->id) {
                                $pab_ids[$key] = 1;
                            }
                        }
                        $proRequest = TicketProcureRequest::findOrFail($pr_id);
                        $proRequest->pab_id = json_encode($pab_ids);
                        if(isset($request->approved_day) && $request->approved_day != '') {
                            $proRequest->approved_day = $request->approved_day;
                        }
                        $proRequest->save();
                        $pro_request->pab_id = json_encode($pab_ids);

                        $pab_ids = json_decode($proRequest->pab_id, true);
                        $notApprovedPAB = array_search("0", $pab_ids);
                        $pab = TicketPab::where("id", $notApprovedPAB)->first();
                        if(!empty($pab)) {
                            $this->_sendApprovalRequestLoop($pro_request);
                        } else {
                            $pro_request->status_id = 3;
                            $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                            $user = User::find($pro_request->creator_id);
                            $now = Carbon::now(config('app.timezone'));
                            $config = Config::first();
                            $holidays = Holiday::getHolidaysFrom($now->format('Y-m-d'));

                            $ticketObj = Ticket::where('id', $pro_request->ticket_id)->first();
                            $ticketObj->subject = $pro_request->subject;
                            $ticketObj->content = $pro_request->content;
                            $ticketObj->status_id = 1;
                            $ticketObj->department_id = $pro_request->department_id;
                            $ticketObj->problem_category_id = $pro_request->problem_category_id;
                            $ticketObj->sub_category_id = $pro_request->sub_category_id;
                            $ticketObj->assigned_to = $ticketObj->assignByHirarchy();
                            $ticketObj->priority_id = $pro_request->priority_id;
                            $ticketObj->device_id = $pro_request->device_id;
                            $ticketObj->tat = $pro_request->tat;
                            $ticketObj->tat_expire = $config->calculateAdvancedTat($pro_request->tat, $now, $holidays);
                            $ticketObj->ac_email_id = $pro_request->ac_email_id;
                            $ticketObj->is_temp = null;
                            $ticketObj->created_at = $now->format('Y-m-d H:i:s');
                            $ticketObj->location_id = $pro_request->location_id;
                            $ticketObj->save();
                            $ticketObjNew = Ticket::where('id', $pro_request->ticket_id)->first();
                            $this->ticketHistory($ticketObj);
                            $pro_request->assigned_to = $ticketObj->assigned_to;
                            if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    $cc_emails = [];
                                    if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                        $manager = User::find($user->manager_id);
                                        $cc_emails[] = $manager->email;
                                    }
                                    if(config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($user->email)->cc($cc_emails)->send(new TicketRequestApproved($pro_request, $user, $pab));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }

                            if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                        $cc_emails = [];
                                        foreach ($alertnotify as $email) {
                                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                $cc_emails[] = $email;
                                            }
                                        }
                                        Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                    } else {
                                        Mail::to($user->email)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }

                            if($ticketObj->assigned_to) {
                                $user = User::find($ticketObj->assigned_to);
                                if(config('mail.service_enabled') && $user && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                            $cc_emails = [];
                                            foreach ($alertnotify as $email) {
                                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                    $cc_emails[] = $email;
                                                }
                                            }
                                            Mail::to($user->email)->cc($cc_emails)->queue(new IntimateAssigned($ticketObj, $user));
                                        } else {
                                            Mail::to($user->email)->queue(new IntimateAssigned($ticketObj, $user));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }
                            }

                            $triggerObj = TicketTrigger::where('status',1)->count();
                            if($triggerObj > 0) {
                                // Ticket generate apply trigger.
                                event(new TicketCreated($ticketObj));
                            }
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $ticketObj->creator_id);
                            $notificationText = "New Ticket #".$ticketObj->id." has been created successfully!";
                            $tkt_config = Config::first();
                            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                            $data = [
                                'title' => $notificationText,
                                'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("service request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                }
                            }
                            // send push notification to all technician which are in this pipeline
                            $notify_peoples = [];
                            if(isset($ticketObj->assigned_to) && $ticketObj->assigned_to != NULL){
                                array_push($notify_peoples, $ticketObj->assigned_to);
                            } else {
                                $notify_peoples = Privilege::getHandlersByDepartment($ticketObj->department_id);
                            }
                            $notify_people = $notify_peoples;
                            $notificationTicketText = "New Ticket #".$ticketObj->id." has been created successfully!";
                            $tkt_config = Config::first();

                            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                            $dataT = [
                                'title' => $notificationTicketText,
                                'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($dataT);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($dataT);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("create service Request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                }
                            }
                        }
                    } else {
                        $pro_request->status_id = 4;
                        $pro_request->approved_at = null;
                        if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager = User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL) && in_array($pro_request->problem_category_id,$enableCategory)) {
                                    Mail::to($user->email)->cc($cc_emails)->queue(new TicketRequestRejected($pro_request, $user, $pab, $request->approved_day, $approvalRequest));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                    }
                }/*Department Head Approval */
                elseif (!empty($pab) && $pab->hierarchy_approval == 11) {
                    if ($approvalRequest->approve_status == 1) {
                        foreach($pab_ids as $key => $val) {
                            if($key == $pab->id) {
                                $pab_ids[$key] = 1;
                            }
                        }
                        $proRequest = TicketProcureRequest::findOrFail($pr_id);
                        $proRequest->pab_id = json_encode($pab_ids);
                        if(isset($request->approved_day) && $request->approved_day != '') {
                            $proRequest->approved_day = $request->approved_day;
                        }
                        $proRequest->save();
                        $pro_request->pab_id = json_encode($pab_ids);

                        $pab_ids = json_decode($proRequest->pab_id, true);
                        $notApprovedPAB = array_search("0", $pab_ids);
                        $pab = TicketPab::where("id", $notApprovedPAB)->first();
                        if(!empty($pab)) {
                            $this->_sendApprovalRequestLoop($pro_request);
                        } else {
                            $pro_request->status_id = 3;
                            $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                            $user = User::find($pro_request->creator_id);
                            $now = Carbon::now(config('app.timezone'));
                            $config = Config::first();
                            $holidays = Holiday::getHolidaysFrom($now->format('Y-m-d'));

                            $ticketObj = Ticket::where('id', $pro_request->ticket_id)->first();
                            $ticketObj->subject = $pro_request->subject;
                            $ticketObj->content = $pro_request->content;
                            $ticketObj->status_id = 1;
                            $ticketObj->department_id = $pro_request->department_id;
                            $ticketObj->problem_category_id = $pro_request->problem_category_id;
                            $ticketObj->sub_category_id = $pro_request->sub_category_id;
                            $ticketObj->assigned_to = $ticketObj->assignByHirarchy();
                            $ticketObj->priority_id = $pro_request->priority_id;
                            $ticketObj->device_id = $pro_request->device_id;
                            $ticketObj->tat = $pro_request->tat;
                            $ticketObj->tat_expire = $config->calculateAdvancedTat($pro_request->tat, $now, $holidays);
                            $ticketObj->ac_email_id = $pro_request->ac_email_id;
                            $ticketObj->is_temp = null;
                            $ticketObj->created_at = $now->format('Y-m-d H:i:s');
                            $ticketObj->location_id = $pro_request->location_id;
                            $ticketObj->save();
                            $ticketObjNew = Ticket::where('id', $pro_request->ticket_id)->first();
                            $this->ticketHistory($ticketObj);
                            $pro_request->assigned_to = $ticketObj->assigned_to;
                            if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    $cc_emails = [];
                                    $departmentHeadId = $user->department->department_head_id;
                                    $departmentHeadUser = User::find($departmentHeadId);
                                    if(!empty($user) && !empty($departmentHeadId) && $departmentHeadUser->email != "") {
                                        $cc_emails[] = $departmentHeadUser->email;
                                    }
                                    if(config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($user->email)->cc($cc_emails)->send(new TicketRequestApproved($pro_request, $user, $pab));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }

                            if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                        $cc_emails = [];
                                        foreach ($alertnotify as $email) {
                                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                $cc_emails[] = $email;
                                            }
                                        }
                                        Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                    } else {
                                        Mail::to($user->email)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }

                            if($ticketObj->assigned_to) {
                                $user = User::find($ticketObj->assigned_to);
                                if(config('mail.service_enabled') && $user && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                            $cc_emails = [];
                                            foreach ($alertnotify as $email) {
                                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                    $cc_emails[] = $email;
                                                }
                                            }
                                            Mail::to($user->email)->cc($cc_emails)->queue(new IntimateAssigned($ticketObj, $user));
                                        } else {
                                            Mail::to($user->email)->queue(new IntimateAssigned($ticketObj, $user));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }
                            }

                            $triggerObj = TicketTrigger::where('status',1)->count();
                            if($triggerObj > 0) {
                                // Ticket generate apply trigger.
                                event(new TicketCreated($ticketObj));
                            }
                            
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $ticketObj->creator_id);
                            $notificationText = "New Ticket #".$ticketObj->id." has been created successfully!";
                            $tkt_config = Config::first();
                            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                            $data = [
                                'title' => $notificationText,
                                'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("service request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                }
                            }

                            // send push notification to all technician which are in this pipeline
                            $notify_peoples = [];
                            if(isset($ticketObj->assigned_to) && $ticketObj->assigned_to != NULL){
                                array_push($notify_peoples, $ticketObj->assigned_to);
                            } else {
                                $notify_peoples = Privilege::getHandlersByDepartment($ticketObj->department_id);
                            }
                            $notify_people = $notify_peoples;
                            $notificationTicketText = "New Ticket #".$ticketObj->id." has been created successfully!";
                            $tkt_config = Config::first();

                            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                            $dataT = [
                                'title' => $notificationTicketText,
                                'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($dataT);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($dataT);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("create service Request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                }
                            }
                        }
                    } else {
                        $pro_request->status_id = 4;
                        $pro_request->approved_at = null;
                        if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                $cc_emails = [];
                                $departmentHeadId = $user->department->department_head_id;
                                $departmentHeadUser = User::find($departmentHeadId);
                                if(!empty($user) && !empty($departmentHeadId) &&  $departmentHeadUser->email != "") {
                                    $cc_emails[] = $departmentHeadUser->email;
                                }
                                if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL) && in_array($pro_request->problem_category_id,$enableCategory)) {
                                    Mail::to($user->email)->cc($cc_emails)->queue(new TicketRequestRejected($pro_request, $user, $pab, $request->approved_day, $approvalRequest));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                    }
                } /* Location approval - means location admin approval required */
                elseif (!empty($pab) && $pab->hierarchy_approval == 5) {
                    if ($approvalRequest->approve_status == 1) {
                        foreach ($pab_ids as $key => $val) {
                            if ($key == $pab->id) {
                                $pab_ids[$key] = 1;
                            }
                        }
                        $proRequest = TicketProcureRequest::findOrFail($pr_id);
                        $proRequest->pab_id = json_encode($pab_ids);
                        if(isset($request->approved_day) && $request->approved_day != '') {
                            $proRequest->approved_day = $request->approved_day;
                        }
                        $proRequest->save();
                        $pro_request->pab_id = json_encode($pab_ids);

                        $pab_ids = json_decode($proRequest->pab_id, true);
                        $notApprovedPAB = array_search("0", $pab_ids);
                        $pab = TicketPab::where("id", $notApprovedPAB)->first();
                        if (!empty($pab)) {
                            $this->_sendApprovalRequestLoop($pro_request);
                        } else {
                            $pro_request->status_id = 3;
                            $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                            $user = User::find($pro_request->creator_id);
                            $now = Carbon::now(config('app.timezone'));
                            $config = Config::first();
                            $holidays = Holiday::getHolidaysFrom($now->format('Y-m-d'));

                            $ticketObj = Ticket::where('id', $pro_request->ticket_id)->first();
                            $ticketObj->subject = $pro_request->subject;
                            $ticketObj->content = $pro_request->content;
                            $ticketObj->status_id = 1;
                            $ticketObj->department_id = $pro_request->department_id;
                            $ticketObj->problem_category_id = $pro_request->problem_category_id;
                            $ticketObj->sub_category_id = $pro_request->sub_category_id;
                            $ticketObj->assigned_to = $ticketObj->assignByHirarchy();
                            $ticketObj->priority_id = $pro_request->priority_id;
                            $ticketObj->device_id = $pro_request->device_id;
                            $ticketObj->tat = $pro_request->tat;
                            $ticketObj->tat_expire = $config->calculateAdvancedTat($pro_request->tat, $now, $holidays);
                            $ticketObj->ac_email_id = $pro_request->ac_email_id;
                            $ticketObj->is_temp = null;
                            $ticketObj->created_at = $now->format('Y-m-d H:i:s');
                            $ticketObj->location_id = $pro_request->location_id;
                            $ticketObj->save();
                            $ticketObjNew = Ticket::where('id', $pro_request->ticket_id)->first();
                            $this->ticketHistory($ticketObj);
                            $pro_request->assigned_to = $ticketObj->assigned_to;
                            if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    $cc_emails = [];
                                    if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                        $manager = User::find($user->manager_id);
                                        $cc_emails[] = $manager->email;
                                    }
                                    if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($user->email)->cc($cc_emails)->send(new TicketRequestApproved($pro_request, $user, $pab));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }

                            if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                        $cc_emails = [];
                                        foreach ($alertnotify as $email) {
                                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                $cc_emails[] = $email;
                                            }
                                        }
                                        Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                    } else {
                                        Mail::to($user->email)->queue(new IntimateSuccessCreation($ticketObj, $user));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }

                            if($ticketObj->assigned_to) {
                                $user = User::find($ticketObj->assigned_to);
                                if(config('mail.service_enabled') && $user && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                            $cc_emails = [];
                                            foreach ($alertnotify as $email) {
                                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                    $cc_emails[] = $email;
                                                }
                                            }
                                            Mail::to($user->email)->cc($cc_emails)->queue(new IntimateAssigned($ticketObj, $user));
                                        } else {
                                            Mail::to($user->email)->queue(new IntimateAssigned($ticketObj, $user));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }
                            }

                            $triggerObj = TicketTrigger::where('status',1)->count();
                            if($triggerObj > 0) {
                                // Ticket generate apply trigger.
                                event(new TicketCreated($ticketObj));
                            }
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $ticketObj->creator_id);
                            $notificationText = "New Ticket #".$ticketObj->id." has been created successfully!";
                            $tkt_config = Config::first();
                            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                            $data = [
                                'title' => $notificationText,
                                'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("service request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                }
                            }

                            // send push notification to all technician which are in this pipeline
                            $notify_peoples = [];
                            if(isset($ticketObj->assigned_to) && $ticketObj->assigned_to != NULL){
                                array_push($notify_peoples, $ticketObj->assigned_to);
                            } else {
                                $notify_peoples = Privilege::getHandlersByDepartment($ticketObj->department_id);
                            }
                            $notify_people = $notify_peoples;
                            $notificationTicketText = "New Ticket #".$ticketObj->id." has been created successfully!";
                            $tkt_config = Config::first();

                            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                            $dataT = [
                                'title' => $notificationTicketText,
                                'data' => CommonHelper::setDataForNotification($ticketObj, 'ticket', $notificationType),
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($dataT);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($dataT);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("create service Request id push notification:" . $ticketObj->id. " notification error " .json_encode($response));
                                }
                            }
                        }
                    } else {
                        $pro_request->status_id = 4;
                        $pro_request->approved_at = null;
                        if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager = User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL) && in_array($pro_request->problem_category_id,$enableCategory)) {
                                    Mail::to($user->email)->cc($cc_emails)->queue(new TicketRequestRejected($pro_request, $user, $pab, $request->approved_day, $approvalRequest));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                    }
                }

                /*$pab_id = $problem_category->pab_id;
                $pab = TicketPab::find($pab_id);*/

                if (!empty($pab) && $pab->hierarchy_approval != 4 && $pab->hierarchy_approval != 9 && $pab->hierarchy_approval != 10 && $pab->hierarchy_approval != 11) {

                    if (!$pab || $pab->totMembers() < 1) {
                        $msg['msg'] = 'No user found send request on chosen SRAT';
                        return $msg;
                    }

                    $pab_members = [];
                    if ($pab->hierarchy_approval == 1) {
                        $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                    } else if($pab->hierarchy_approval == 5) {
                        if($pro_request->location_id != "") {
                            $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->where('location_id', $pro_request->location_id)->orderBy('id', 'asc')->get();
                        }
                    } else {
                        $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                    }

                    foreach ($pab_members as $k => $member) {
                        $approval_user_id = $member->user_id;
                        $user = User::find($approval_user_id);
                        $pab_member_emails[] = $user->email;
                    }
                    $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                    $creators = array_filter($pro_not_mem);
                } else {
                    $user = User::find($pro_request->creator_id);
                    // $user = User::find($approvalRequest->user_id);
                    $creators = [$user->email];
                }

                if ($pro_request->status_id != 2) {
                    try {
                        if (config('mail.service_enabled') && !empty($creators)) {
                            if (isset($alertnotify) && is_array($alertnotify)){
                                $cc_emails = [];
                                foreach ($alertnotify as $email) {
                                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        $cc_emails[] = $email;
                                    }
                                }
                                Mail::to($creators)->cc($cc_emails)->send(new StatusChange($pro_request, $pab));
                            }
                        }
                    } catch (\Exception $ex) {
                        Log::error($ex->getMessage());
                    }
                }

                $pro_request->save();

                $return['msg'] = 'Your response has been sent successfully.';
                $return["status"] = "success";

                $notificationText = "Service Request #" . $pro_request->procure_tag . " is responded as " . $approvalRequest->statusLabel() . " \n Comment is, " . $approvalRequest->comments;
                $data = [
                    'title' => $notificationText,
                    'data' => $pro_request,
                    'notify' => [$user->id],
                ];
                $sendNotifications = CommonHelper::sendPushNotification($data);
                if($sendNotifications != false) {
                    $response = json_decode($sendNotifications);
                    if(isset($response->failure) && $response->failure == 1) {
                        Log::error("approval request sent to user id push notification:" . $pro_request->id . " notification error " . json_encode($response));
                    }
                }
                $c = 'Responded as ' . $approvalRequest->statusLabel() . ' <br/>Comment is, ' . $approvalRequest->comments;
                if(isset($approvalRequest->delegated_user_id) && $approvalRequest->delegated_user_id != NULL && Auth::user()->id == $approvalRequest->delegated_user_id){
                    $actualUser = User::find($approvalRequest->user_id);
                    $c .= "</br> Approved by ".  Auth::user()->fullName() . "(delegated on behalf of actual approver " .$actualUser->fullName() .")";
                }

                $c = 'Responded as ' . $approvalRequest->statusLabel() . ' <br/>Comment is, ' . $approvalRequest->comments;
                if(isset($approvalRequest->delegated_user_id) && $approvalRequest->delegated_user_id != NULL && Auth::user()->id == $approvalRequest->delegated_user_id){
                    $actualUser = User::find($approvalRequest->user_id);
                    $c .= "</br> Approved by ".  Auth::user()->fullName() . "(delegated on behalf of actual approver " .$actualUser->fullName() .")";
                }
                if(!empty($pab)) {
                    $c .= "<br>Approval mode : ". $pab->name."( ".$pab->getApprovalModeName().")";
                }
                $history = new TicketRequestHistory();
                $history->user_id = Auth::user()->id;
                $history->pr_id = $pro_request->id;
                $history->change_info = $c;
                $history->save();
                // original ticket reference
                if(isset($ticketObjNew) && $ticketObjNew != '') {
                    $ticketObjNew->original_ticket_reference = $pro_request->ticket_id;
                    Log::info('App Original ticket reference from SR '. $pro_request->ticket_id);
                    if(isset($pro_request->old_ticket_ref) && $pro_request->old_ticket_ref != null) {
                        $oldTicket = Ticket::find($pro_request->old_ticket_ref);
                        if(!empty($oldTicket)) {
                            Log::info('App New ticket reference from SR ' . $pro_request->ticket_id);
                            DB::table('tkt_tickets')->where('id', $pro_request->old_ticket_ref)->update([
                                'new_ticket_reference' => $pro_request->ticket_id,
                            ]);
                            $ticketObjNew->old_ticket_ref = $oldTicket->id;
                            $ticketObjNew->assigned_to = $oldTicket->assigned_to;
                            $ticketObjNew->original_ticket_reference = $oldTicket->original_ticket_reference;
                        }
                    }
                    $otherLocation = Location::where('name', 'like', 'Other')->first();
                    if(!empty($otherLocation)) {
                        $ticketObjNew->location_id = isset($pro_request->location_id) && $pro_request->location_id != NULL ? $pro_request->location_id : $otherLocation->id;
                    }

                    $ticketObjNew->save();
                    $this->ticketHistory($ticketObjNew);
                    // send push notification to all technician which are in this pipeline
                    $notify_peoples = [];
                    if(isset($ticketObjNew->assigned_to) && $ticketObjNew->assigned_to != NULL){
                        array_push($notify_peoples, $ticketObjNew->assigned_to);
                    } else {
                        $notify_peoples = Privilege::getHandlersByDepartment($ticketObjNew->department_id);
                    }
                    $notify_people = $notify_peoples;
                    $notificationTicketText = "New Ticket #".$ticketObjNew->id." has been created successfully!";
                    $tkt_config = Config::first();

                    $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                    $dataT = [
                        'title' => $notificationTicketText,
                        'data' => CommonHelper::setDataForNotification($ticketObjNew, 'ticket', $notificationType),
                        'notify' => $notify_people,
                    ];
                    $sendNotifications = CommonHelper::sendPushNotification($dataT);
                    $sendNotifications = CommonHelper::sendWhatsappNotification($dataT);
                    if($sendNotifications != false) {
                        $response = json_decode($sendNotifications);
                        if(isset($response->failure) && $response->failure == 1) {
                            Log::error("create service ticket id push notification:" . $ticketObjNew->id. " notification error " .json_encode($response));
                        }
                    }
                    // end pipeline
                }

            }

            if (config('app.socket_enabled')) {
                CommonHelper::sendTicketCountToSocket();
            }

            DB::commit();
            Log::info("API SR Approve id:" . $pro_request->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("API SR Approve: " . $e->getMessage());
            $return['msg'] = $e->getMessage();
            return response()->json($return);
        }
    }      

    public function requestsHistory(Request $request, $param) {
        $return = ["status" => "fail", "msg" => "Unable to fetch service ticket history"];
        try {
            DB::beginTransaction();
            $get_request = TicketProcureRequest::findOrFail($param);
            $pro_request = $get_request;
            $history = TicketRequestHistory::select('tkt_request_history.id','tkt_request_history.pr_id', 'tkt_request_history.user_id', 'tkt_request_history.change_info','tkt_request_history.comment', 'tkt_request_history.action_type', DB::raw("concat(u.first_name, ' ', u.last_name) as full_name, u.username, u.displayName"))
            ->addSelect(DB::raw('DATE_FORMAT(tkt_request_history.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'))
            ->addSelect(DB::raw('DATE_FORMAT(tkt_request_history.created_at, "%d %b %Y %h:%i %p") as created_at_format'))
            ->leftjoin('users as u','u.id', 'tkt_request_history.user_id')
            ->where('tkt_request_history.pr_id', '=', $pro_request->id)->orderBy('tkt_request_history.id', 'desc')->get();
            $return['data'] = $history;
            $return['status'] = "success";
            $return['msg'] = "";
            DB::commit();
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("History error: ". $e->getMessage());
            return response()->json($return);
        }
    }

    /* to return the filter options for the service request */
    public function getRequestsFilterOptions(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to get filter options"];

        $statuses = TicketRequestStatus::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $filter_by_dates_opts = TicketProcureRequest::filterByDateOpts();
        $pabs = TicketPab::select('id', 'name')->get();

        $return['status'] = "success";
        $return['msg'] = '';
        $return['departments'] = $departments;
        $return['filter_by_dates_opts'] = $filter_by_dates_opts;
        $return['authority_board'] = $pabs;
        $return['statuses'] = $statuses;
        return response()->json($return);
    }

    /* ajax cancel decision & restart flow */
    public function revokeDecision(Request $request, $pr_id) {
        $return = [
            "msg" => "Unable to revoke the early decision.",
            "status" => "failure"
        ];
        $data = $request->only('approve_request_id');
        $rules = [
            'approve_request_id' => 'required|integer|exists:tkt_approval_request,id',
        ];
        $msgs = [
            'approve_request_id.required' => 'Please select an approval request.',
            'approve_request_id.exists'   => 'The selected approval request does not exist.',
        ];

        $validator = Validator::make($data, $rules, $msgs);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        try {
            $pro_request = TicketProcureRequest::find($pr_id);
            if (!$pro_request) {
                return response()->json([
                    'status' => false,
                    'message' => 'Service request not found.',
                ], 404);
            }

            if(! in_array($pro_request->status_id, [2, 3, 4, 6])) {
                throw new \Exception('Service Request is in other status which does not suitable to accept your decision.');
            }

            $approvalRequest = TicketApprovalRequest::find($request->approve_request_id);
            if (!$approvalRequest) {
                return response()->json([
                    'status' => false,
                    'message' => 'Approval request not found.',
                ], 404);
            }
            if($approvalRequest->approve_status != 1 ) {
                $return["msg"] = trans('validation.approval_modify');
                return response()->json($return);
            }
            if(empty($approvalRequest) || !$approvalRequest || $approvalRequest->user_id != Auth::user()->id || $approvalRequest->pr_id != $pro_request->id) {
                throw new \Exception('Invalid Request');
            }
        }
        catch(\Exception $e) {
            $return['msg'] = $e->getMessage();
            return response()->json($return);
        }

        DB::beginTransaction();
        try {
            $pro_request->status_id = 2;
            $pro_request->approved_at = null;
            $pro_request->save();

            $approvalRequest->approve_status = 3;
            $approvalRequest->comments = null;
            $approvalRequest->save();

            $cc_list = [Auth::user()->id];
            if($pro_request->hierarchy_approval == TicketPab::MODE_GROUP_APPROVAL) {
                $getNextLevels = TicketApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->where('id', '!=', $approvalRequest->id)->get();

                foreach($getNextLevels as $nl) {
                    if($nl->user->email) {
                        $cc_list[] = $nl->user->email;
                    }
                }
            }
            elseif($pro_request->hierarchy_approval == TicketPab::MODE_MINIMUM_APPROVAL) {
                $getNextLevels = TicketApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->where('id', '!=', $approvalRequest->id)->get();

                foreach($getNextLevels as $nl) {
                    if($nl->user->email) {
                        $cc_list[] = $nl->user->email;
                    }
                }
            }
            elseif($pro_request->hierarchy_approval == TicketPab::MODE_LEVEL_BY_LEVEL) {
                $getNextLevels = TicketApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->where('hierarchy_level', '>', $approvalRequest->hierarchy_level)->where('id', '!=', $approvalRequest->id)->orderBy('hierarchy_level', 'asc')->get();

                if(count($getNextLevels)) {
                    TicketApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->where('hierarchy_level', '>', $approvalRequest->hierarchy_level)->where('id', '!=', $approvalRequest->id)->update([
                        'approve_status' => 4,
                        'comments' => null
                    ]);

                    foreach($getNextLevels as $nl) {
                        if($nl->user->email) {
                            $cc_list[] = $nl->user->email;
                        }
                    }
                }
            }

            $needToNotify = array_unique($cc_list);
            /*try {
                if(config('mail.service_enabled') && $needToNotify && count($needToNotify)) {
                    Mail::to($needToNotify)->send(new ApproverInfo($pro_request, $user, $pab_username));
                }
            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
            }*/

            $c = 'Early decision has been removed';
            $history = new TicketRequestHistory();
            $history->user_id = Auth::user()->id;
            $history->pr_id = $pro_request->id;
            $history->change_info = $c;
            $history->save();

            /** send push notification */
            // $pab_id = $problem_category->pab_id;
            $pab_id = $pro_request->pab_id;
            $data = json_decode($pab_id, true);
            $pab_id = array_key_first($data);

            $matched_pab_id = $approvalRequest->pab_id; 
            if (array_key_exists($matched_pab_id, $data)) {
                $data[$matched_pab_id] = 0; 
            }
            $pro_request->pab_id = json_encode($data);
            $pro_request->save();

            $pab = TicketPab::find($pab_id);
            $notify_people = [];
            $pab_members = [];
            if(!$pab || $pab->totMembers() < 1) {
                if ($pab->hierarchy_approval == 1) {
                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                } else {
                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                }
            }

            foreach($pab_members as  $k=>$member) {
                $u = User::find($member->user_id);
                array_push($notify_people, $u->id);
            }
            $updateUserName = Auth::user()->first_name . " " . Auth::user()->last_name;
            $notificationText = "<p>revoke service request tag id <b>".$pro_request->procure_tag."</b> is being update by <b>".$updateUserName."</b></p>";
            $bodyMsg = "<p>Your service request revoke. Please check your Portal or app.</p>";
            $data = [
                'title' => $notificationText,
                'data' => $pro_request,
                'bodyMsg' => $bodyMsg,
                'notify' => $notify_people,
            ];

            $sendNotifications = CommonHelper::sendPushNotification($data);
            if($sendNotifications != false) {
                $response = json_decode($sendNotifications);
                if(isset($response->failure) && $response->failure == 1) {
                    Log::error("revokeDecision service request id push notification:" . $pro_request->id. " notification error " .json_encode($response));
                }
            }

            $return['status'] = 'success';
            $return['msg'] = 'Your early decision has been revoked successfully';

            DB::commit();
            Log::info("API SR revokeDecision id:" . $pr_id. " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("revokeDecision service request API: " . $e->getMessage());
            $return['msg'] = $e->getMessage();
            return response()->json($return);
        }
    }

    public function myRequestList(Request $request) {
        $return = [];
        $req = $request->all();
        $page = $request->index ? $request->index : 0;
        $take = $request->list_size ? $request->list_size : 20;
        $skip = $page * $take;
        $current_user = Auth::user();
        $db = TicketProcureRequest::from('tkt_procure_requests as tpr')->withTrashed()->select('tpr.*', 'dep.name as dep_name', 'pab.name as pab', 'status.name as status', DB::raw('concat_ws(" ", creator.first_name, creator.last_name, "@", creator.username) as creator_name'),
                DB::raw('case when pc.approval_required = 1 then "Required" when pc.approval_required = 0 then "Not Required "else "" end as approval_required'),
                DB::raw('DATE_FORMAT(tpr.created_at, "%d %b %Y %h:%i %p") as created_at_format'),
                DB::raw('DATE_FORMAT(tpr.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'),
                DB::raw('DATE_FORMAT(tpr.approved_at, "%d %b %Y %h:%i %p") as approved_at_format')
            )
            ->leftJoin('departments as dep', 'tpr.department_id', '=', 'dep.id')
            ->leftJoin('tkt_problem_categories as pc', 'tpr.problem_category_id', '=', 'pc.id')
            ->leftJoin('users as creator', 'tpr.creator_id', '=', 'creator.id')->where('tpr.creator_id', '=', $current_user->id)
            ->leftJoin('tkt_ticket_pabs as pab', 'pc.pab_id', '=', 'pab.id')
            ->leftJoin('tkt_request_status as status', 'tpr.status_id', '=', 'status.id')
            ->whereNull('tpr.deleted_at');
        $return['total'] = $db->count();
        $return['filtered'] = $return['total'];

        $is_searching = false;
        if(isset($req["filters"])) {
            $filters = $req["filters"];
            if(isset($filters["status"]) && $filters['status'] && $filters['status'] != "null" && !empty(json_decode($filters['status']))) {
                $db->whereIn("tpr.status_id", json_decode($filters['status']));
            }
            if(isset($filters["pab"]) && $filters['pab'] && $filters['pab'] != "null") {
                $db->where("tpr.pab_id",'like',"%".$filters['pab']."%");
            }
            if(isset($filters["department"]) && $filters['department'] && $filters['department'] != "null" && !empty(json_decode($filters['department']))) {
                $db->whereIn("tpr.department_id", json_decode($filters['department']));
            }
            if(isset($filters["problem_category_id"]) && $filters['problem_category_id'] && $filters['problem_category_id'] != "null" && !empty(json_decode($filters['problem_category_id']))) {
                $db->whereIn("tpr.problem_category_id", json_decode($filters['problem_category_id']));
            }
            if(isset($filters["sub_category_id"]) && $filters['sub_category_id'] && $filters['sub_category_id'] != "null" && !empty(json_decode($filters['sub_category_id']))) {
                $db->whereIn("tpr.sub_category_id", json_decode($filters['sub_category_id']));
            }

            $based_on_possible = ['1'=> 'tpr.created_at', '5'=>'tpr.approved_at', '3'=>'tpr.updated_at'];
            if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 5 ) {
                if(isset($filters["from_date"]) && $filters["from_date"] && $filters["from_date"] != "null") {
                    $from_date = CommonHelper::getDateAs($filters["from_date"], "Y-m-d H:i:s", "d/m/Y H:i:s");
                    $to_date = CommonHelper::getDateAs($filters["to_date"], "Y-m-d H:i:s", "d/m/Y H:i:s");
                    if($from_date && $to_date) {
                        $db->whereBetween($based_on_possible[$filters['based_on']], [$from_date, $to_date]);
                    }
                }
            }

            $is_searching = true;
        }

        if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
            if(substr($search_key, 0, 1) == "#" && strlen($search_key) > 1) {
                $whereStr = sprintf('(tpr.procure_tag like "%1$s")', substr($search_key, 1));
            }
            else {
                $whereStr = sprintf('(tpr.procure_tag like "%%%1$s%%" or tpr.subject like "%%%1$s%%" or dep.name like "%%%1$s%%" or status.name like "%%%1$s%%" or concat_ws(" ", creator.first_name, creator.last_name, "@", creator.username) like "%%%1$s%%" or pab.name like "%%%1$s%%" or DATE_FORMAT(tpr.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(tpr.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $is_searching = true;
        }

        if($is_searching) {
            $return['filtered'] = $db->count();
        }

        $fields = [1=>'tpr.procure_tag', 2=>'creator_name', 3=>'tpr.status_id', 4=>'pab.name', 5=>'dep.name', 6=>'tpr.created_at', 7=>'tpr.updated_at'];
        if( isset($req["order"]["id"]) && array_key_exists($req["order"]["id"], $fields) && in_array($req["order"]["dir"], [1, 2]) ) {
            $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
            $db->orderBy( $fields[$req["order"]["id"]], $dir);
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filtered'] > ( $skip + $take ) ? 1 : 0;
        $return['pab'] = TicketPab::select('id', 'name')->get();

        $db->skip($skip);
        $db->take($take);

        $return_val["data_val"] = $db->get();
        $return["data"] = $return_val["data_val"]->unique('id');
        $return["page"] = $page;

        return response()->json($return);
    }

    public function _sendApprovalRequestLoop($pro_request) {
        $msg = ['status' => 'failure', 'msg' => ''];

        if(isset($pro_request->sub_category_id) && $pro_request->sub_category_id != "") {
            $problem_category = ProblemCategory::find($pro_request->sub_category_id);
        } else {
            $problem_category = ProblemCategory::find($pro_request->problem_category_id);
        }

        $pab_ids = json_decode($pro_request->pab_id);
        $notApprovedPAB = array_search("0", (array) $pab_ids);
        $pab = TicketPab::where("id", $notApprovedPAB)->first();
        $enableUsbRequest_old = [];
        $departmentCustomFieldset = Department::find($pro_request->department_id);
        if($departmentCustomFieldset->name == "IT Service Request" || $departmentCustomFieldset->name == "IT Service Request Overseas") {
            $enableUsbRequest_old = ProblemCategory::enableUsbRequest([$departmentCustomFieldset->id]);
        }
        $enableUsbRequests_new = ProblemCategory::where('privilege_access',1)->pluck('id')->toArray();
        $enableUsbRequests = array_merge($enableUsbRequest_old,$enableUsbRequests_new);
        $enableCategory = array_values(array_unique($enableUsbRequests));
        $creators = [];
        if(!empty($pab) && $pab->hierarchy_approval != 4) {
            if (!$pab || $pab->totMembers() < 1) {
                $msg['msg'] = 'No user found send request on chosen SRAT';
                return $msg;
            }

            $pab_members = [];
            if (!empty($pab) && $pab->hierarchy_approval == 1) {
                $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
            } else if(!empty($pab) && $pab->hierarchy_approval == 5) {
                if($pro_request->location_id != "") {
                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->where('location_id', $pro_request->location_id)->orderBy('id', 'asc')->get();
                }
            } else {
                $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
            }
           
            foreach ($pab_members as $k => $member) {
                $existingRequest = TicketApprovalRequest::where('pr_id', $pro_request->id)
                ->where('pab_id', $pab->id)
                ->where('user_id', $member->user_id)
                ->first();
                if ($existingRequest) {
                    continue;
                }
                $approvalRequest = new TicketApprovalRequest();
                $approvalRequest->pr_id = $pro_request->id;
                $approvalRequest->pab_id = $pab->id;
                $approvalRequest->user_id = $member->user_id;
                if ($pab->hierarchy_approval == 1) {
                    $approvalRequest->hierarchy_level = $member->hierarchy_level;
                    $approvalRequest->hierarchy_approval = $pab->hierarchy_approval;

                    // send approve request start from first level
                    if ($k == 0) {
                        $approvalRequest->approve_status = 3;
                    } else {
                        $approvalRequest->approve_status = 4;
                    }
                } else {
                    $approvalRequest->approve_status = 3; // symultanously send request all member
                }
                // check delegation exists or not if exists then we set this value in table
                $checkDelegation = UserDetails::where('user_id',$member->user_id)->first();
                if(isset($checkDelegation->request_approval_delegated_user) && $checkDelegation->request_approval_delegated_user != NULL && $member->user->hasPermissionTo('DelegateRequest')) {
                    $approvalRequest->delegated_user_id = $checkDelegation->request_approval_delegated_user;
                    //for sending a mail to deleted user in problem category is not in CR
                    $checkDelegatedUser = User::find($checkDelegation->request_approval_delegated_user);
                    if (config('mail.service_enabled') && filter_var($checkDelegatedUser->email, FILTER_VALIDATE_EMAIL) && !in_array($pro_request->problem_category_id, $enableCategory)) {
                        Mail::to($checkDelegatedUser->email)->send(new TicketApproverInfo($pro_request, $pab, $checkDelegatedUser, $member->user));
                    }
                }
                $approvalRequest->save();
                $user = User::find($approvalRequest->user_id);
                $pab_member_emails[] = $user->email;
                $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                $creators = array_filter($pro_not_mem);
                $user = User::find($member->user_id);
                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL) && in_array($pro_request->problem_category_id, $enableCategory)) {
                    try {
                        if(isset($checkDelegation->request_approval_delegated_user) && $checkDelegation->request_approval_delegated_user != NULL && $user->hasPermissionTo('DelegateRequest')) {
                            $checkDelegatedUser = User::find($checkDelegation->request_approval_delegated_user);
                            if (config('mail.service_enabled') && filter_var($checkDelegatedUser->email, FILTER_VALIDATE_EMAIL) ) {
                                Mail::to($checkDelegatedUser->email)->queue(new TicketRequestApprovalSend($pro_request, $checkDelegatedUser, $pab, 'delegated', $user));
                            }
                        }
                    } catch (\Exception $ex) {
                        Log::error($ex->getMessage());
                    }
                }
            }
        } else {
            $usr = User::find($pro_request->creator_id);
            $approvalRequest = new TicketApprovalRequest();
            $approvalRequest->pr_id = $pro_request->id;
            $approvalRequest->pab_id = $pab->id;
            $approvalRequest->user_id = $usr->manager->id;
            $approvalRequest->approve_status = 3;
            $checkDelegation = UserDetails::where('user_id',$usr->manager->id)->first();
            if(isset($checkDelegation->request_approval_delegated_user) && $checkDelegation->request_approval_delegated_user != NULL && $usr->manager->hasPermissionTo('DelegateRequest')) {
                $approvalRequest->delegated_user_id = $checkDelegation->request_approval_delegated_user;
            }
            $approvalRequest->save();
            $user = User::find($approvalRequest->user_id);
            $pab_member_emails[] = $user->email;
            $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
            $creators = array_filter($pro_not_mem);
            if(in_array($pro_request->problem_category_id, $enableCategory)) {
                try {
                    if(isset($checkDelegation->request_approval_delegated_user) && $checkDelegation->request_approval_delegated_user != NULL && $usr->manager->hasPermissionTo('DelegateRequest')){
                        $checkDelegatedUser = User::find($checkDelegation->request_approval_delegated_user);
                        if (config('mail.service_enabled') && filter_var($checkDelegatedUser->email, FILTER_VALIDATE_EMAIL) ) {
                            Mail::to($checkDelegatedUser->email)->queue(new TicketRequestApprovalSend($pro_request, $checkDelegatedUser, $pab, 'delegated',$usr->manager));
                        }
                    }
                } catch (\Exception $ex) {
                    Log::error('Error in sending the mail to request manager '.$ex->getMessage());
                }
            }
            if(isset($checkDelegation->request_approval_delegated_user) && $checkDelegation->request_approval_delegated_user != NULL && $usr->manager->hasPermissionTo('DelegateRequest')) {
                //for sending a mail to delegeted user in problem category is not in CR
                $checkDelegatedUser = User::find($checkDelegation->request_approval_delegated_user);
                if (config('mail.service_enabled') && filter_var($checkDelegatedUser->email, FILTER_VALIDATE_EMAIL) && !in_array($pro_request->problem_category_id, $enableCategory)) {
                    Mail::to($checkDelegatedUser->email)->queue(new TicketApproverInfo($pro_request, $pab, $checkDelegatedUser, $usr->manager));
                }
            }
        }

        Log::info("_sendApprovalRequestLoop: " . json_encode($creators));
        $alertnotify = null;
        if(Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        try {
            if(config('mail.service_enabled') && !empty($creators)) {
                Log::info("Ticket create email: " . json_encode($creators));
                Log::info("Ticket create email cc: " . json_encode($alertnotify));
                Mail::to($creators)->cc($alertnotify)->queue(new TicketApproverInfo($pro_request, $pab));
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }

        $msg['msg'] = 'Service Request(s) Approval has been send successfully for Request #'.$pro_request->procure_tag;
        $msg['status'] = 'success';

        $history = new TicketRequestHistory();
        $history->user_id = Auth::user()->id;
        $history->pr_id = $pro_request->id;
        $history->change_info = $msg['msg'];
        $history->save();
        // return $msg;
    }

    public function ticketHistory($ticket) {
        try {
            $ticketHistoryexist = TicketStatusHistory::select('ticket_id','action_type')->where('ticket_id', $ticket->id)->where('action_type', 14)->first();
            if(empty($ticketHistoryexist)) {
                /** Code is for add new ticket functionality in ticket history */
                $tkt_update['ticket_id'] = $ticket->id;
                $tkt_update['updated_by'] = $ticket->creator_id;
                $tkt_update['action_type'] = 14;
                CommonHelper::ticketStatusHistory($tkt_update, $ticket);
            }

            if($ticket->assigned_to != null || $ticket->assigned_to != "") {
                /** Code is for adding assigned ticket functionality in ticket history */
                $tkt_update['ticket_id'] = $ticket->id;
                $tkt_update['updated_by'] = 0;
                $tkt_update['assigned_to'] = $ticket->assigned_to;
                $tkt_update['action_type'] = 1;
                CommonHelper::ticketStatusHistory($tkt_update, $ticket);

                if(($ticket->assigned_to) != null) {
                    $assignedUser = User::find($ticket->assigned_to);
                    if(config('mail.service_enabled') && $assignedUser && $assignedUser->email && filter_var($assignedUser->email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($assignedUser->email)->queue(new IntimateAssigned($ticket, $assignedUser));
                        } catch (\Exception $ex) {
                            Log::error("RequestController - ticketHistory() IntimateAssigned Mail:" . $ex->getMessage());
                        }
                    }
                }
            }
        } catch(\Exception $e) {
            Log::error("RequestController - ticketHistory() uid: ".Auth::user()->id." - ".json_encode($ticket) .$e->getMessage());
        }
    }

    public function getCCCommentByQuery(Request $request){
        $search = $request->input("search", "");
        $q = $request->input("q", "");

        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("users")->select("id", DB::raw('email as text'))->where('activated',1)->whereNotNull('email')->whereNull('deleted_at');
        if($search) {
            $db->whereRaw('email like "%' . $search . '%"');
        }
        elseif($q) {
            $db->whereRaw('email like "%' . $q . '%"');
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();
        return $this->success(["pagination"=>array("more" => ($count - ($page * 20)) > 0 ? true : false),"results"=>count($result) ? $result->toArray() : []], '');
    }
    public function checkApprovalDays($pro_request){
        $ref_st = Ticket::find($pro_request->old_ticket_ref);
        $categoryFields = ['sub_category_id', 'problem_category_id'];
        foreach ($categoryFields as $field) {
            if(!empty($pro_request->$field)) {
                $category = ProblemCategory::withTrashed()->find($pro_request->$field);
                if (str_contains($category->name, "Renewal") && isset($pro_request->old_ticket_ref) && $pro_request->old_ticket_ref < $pro_request->id) {
                    $req = TicketProcureRequest::where('ticket_id', $ref_st->original_ticket_reference)->first();
                    foreach ($categoryFields as $field) {
                        if (!empty($req->$field)) {                            
                            $renewcategory = ProblemCategory::withTrashed()->find($req->$field);
                            if ($renewcategory && $renewcategory->privilege_access == 1) {
                                return [
                                    'no_of_approval_day' => $renewcategory->number_of_days,
                                    'privilege_access' => $renewcategory->privilege_access,
                                    'category' => $pro_request->$field,
                                ];
                            }
                        }
                    }
                } else {
                    if ($category && $category->privilege_access == 1) {
                        return [
                            'no_of_approval_day' => $category->number_of_days,
                            'privilege_access' => $category->privilege_access,
                            'category' => $pro_request->$field,
                        ];
                    }
                }
                
            }
        }
        return [
            'no_of_approval_day' => '',
            'privilege_access' => '',
            'category' => '',
        ];
    }
}
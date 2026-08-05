<?php

namespace App\Http\Controllers\Auth\Api\ChangeManagement;

use App\Http\Controllers\Controller;
use App\Mail\ChangeManagement\ChangeMail;
use App\Mail\ChangeManagement\SendApprovalRequest;
use App\Models\ChangeManagement\Cab;
use App\Models\ChangeManagement\Attachment;
use App\Models\ChangeManagement\Category;
use App\Models\ChangeManagement\ChangeType;
use App\Models\ChangeManagement\CloseState;
use App\Models\ChangeManagement\ApprovalRequest;
use App\Models\ChangeManagement\CabMember;
use App\Models\ChangeManagement\History;
use App\Models\ChangeManagement\HistoryEntry;
use App\Models\ChangeManagement\Impact;
use App\Models\ChangeManagement\RelevantTicket;
use App\Models\ChangeManagement\RelevantTask;
use App\Models\ChangeManagement\RelevantDevice;
use App\Models\TaskManagement\Task;
use App\Models\ChangeManagement\Priority;
use App\Models\ChangeManagement\Record;
use App\Models\ChangeManagement\Risk;
use App\Models\ChangeManagement\Status;
use App\Models\Ticket\Ticket;
use App\Models\ChangeManagement\CMFollowing;
use App\Models\Settings;
use App\Mail\ChangeManagement\UserComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\Common as CommonHelper;
use DB;
use Auth;
use Carbon\Carbon;
use Exception;
use stdClass;
use Validator;
use Log;
use Mail;
use File;
use Image;
use Storage;

class ListController extends Controller
{
    // CRM Listing Api
    public function ajaxList(Request $request,$main_filter) {
        try {
            $return = ["total" => 0,"filtered" => 0,"data" => []];
            $req = $request->all();
            $date = new stdClass;
            $date->now = Carbon::now('Asia/Kolkata');
            $date->yesterday = Carbon::now('Asia/Kolkata')->yesterday();
            $date->last_7_days = Carbon::now('Asia/Kolkata')->subDays(7)->startOfDay();
            $date->last_quarter_start = Carbon::now('Asia/Kolkata')->subMonth(5)->startOfMonth();
            $date->last_quarter = Carbon::now('Asia/Kolkata')->subMonth(3)->endOfMonth();
            $date->lastMonth = Carbon::now('Asia/Kolkata')->subMonth(1)->startOfMonth();
            /* to get the quarter */
            $date1 = Carbon::now('Asia/Kolkata')->firstOfQuarter();
            $date2 = Carbon::now('Asia/Kolkata')->lastOfQuarter();
            $start_month = Carbon::now('Asia/Kolkata')->startOfMonth();
            $end_month = Carbon::now('Asia/Kolkata')->endOfMonth();
            $start_year = Carbon::now('Asia/Kolkata')->startOfYear();
            $end_year = Carbon::now('Asia/Kolkata')->endOfYear();

            $sort_fields = [
                ["id"=>1, "text"=>"Request Code"],
                ["id"=>2, "text"=>"Subject"],
                ["id"=>3, "text"=>"Status"],
                ["id"=>4, "text"=>"Priority"],
                ["id"=>5, "text"=>"Impact"],
                ["id"=>6, "text"=>"Risk"],
                ["id"=>7, "text"=>"Change Type"],
                ["id"=>8, "text"=>"Category"],
                ["id"=>9, "text"=>"Updated On"]
            ];
            $fields = [
                '1' => 'cr.record_tag',
                '2' => 'cr.subject',
                '3' => 'st.name',
                '4' => 'pri.name',
                '5' => 'im.name',
                '6' => 'rsk.name',
                '7' => 'ct.name',
                '8' => 'cat.name',
                '9' => 'cr.updated_at'
            ];

            $db = DB::table('cm_records as cr');
            $db->leftJoin('cm_statuses as st', 'cr.status_id', '=', 'st.id');
            $db->leftJoin('cm_priorities as pri', 'cr.priority_id', '=', 'pri.id');
            $db->leftJoin('cm_impacts as im', 'cr.impact_id', '=', 'im.id');
            $db->leftJoin('cm_risks as rsk', 'cr.risk_id', '=', 'rsk.id');
            $db->leftJoin('cm_change_types as ct', 'cr.change_type_id', '=', 'ct.id');
            $db->leftJoin('cm_categories as cat', 'cr.category_id', '=', 'cat.id');
            $db->leftJoin('users as creq', 'cr.change_requester', '=', 'creq.id');
            $db->leftJoin('users as cmgr', 'cr.change_manager', '=', 'cmgr.id');

            $db->whereNull("cr.deleted_at");

            $db->select('cr.id', 'cr.subject', 'cr.record_tag', 'st.name as statusName', 'pri.name as priorityName', 'im.name as impactName', 'rsk.name as riskName', 'ct.name as changeTypeName', 'cat.name as categoryName','cr.cab_id','cr.change_type_id');
            $db->addSelect(DB::raw('concat_ws(" ", creq.first_name, creq.last_name) as changeRequester'));
            $db->addSelect(DB::raw('concat_ws(" ", cmgr.first_name, cmgr.last_name) as changeManager'));
            $db->addSelect(DB::raw('DATE_FORMAT(cr.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
            $db->addSelect(DB::raw('DATE_FORMAT(cr.created_at, "%d %b %Y %h:%i %p") as created_at_format'));
            $db->addSelect(DB::raw('DATE_FORMAT(cr.scheduled_start_date, "%d %b %Y %h:%i %p") as scheduled_start_date'));
            $db->addSelect(DB::raw('DATE_FORMAT(cr.scheduled_end_date, "%d %b %Y %h:%i %p") as scheduled_end_date'));
            $db->addSelect(DB::raw('DATE_FORMAT(cr.closed_date, "%d %b %Y %h:%i %p") as closed_date'));
            $db->addSelect(DB::raw('CASE WHEN cr.close_state = 1 THEN "Success" WHEN cr.close_state = 2 THEN "Failure" ELSE "" END AS close_state'));
            $current_user = Auth::user();
            if ($main_filter == "requestList") {
                    $db->Join('cm_approval_requests as ar', function ($join) use ($current_user) {
                        $join->on('cr.id', '=', 'ar.record_id');
                        $join->where('ar.user_id', $current_user->id);
                    });
            } else if ($main_filter == "myRequest") {
                $db->where('cr.change_requester', '=', $current_user->id);
            } else if ($main_filter == "implementer") {
                $current_user_id = $current_user->id; 
                $db->whereRaw("JSON_CONTAINS(cr.change_implementer, ?)", ["\"$current_user_id\""]);
            } else if ($main_filter == "reviewer") {
                $db->where('cr.change_reviewer', '=', $current_user->id);
            } else if ($main_filter == "manager") {
                $db->where('cr.change_manager', '=', $current_user->id);
            }

            if(isset($request->status)) {
                $db->where('cr.status_id', $request->status);
            }

            if ($main_filter == "change-closed-quarter") {
                $db->whereBetween("cr.updated_at", [$date1, $date2])->where('cr.status_id',6);
            } elseif ($main_filter == "change-closed-month") {
                $db->whereBetween("cr.updated_at", [$start_month , $end_month])->where('cr.status_id', 6);
            } elseif ($main_filter == "change-closed-today") {
                $db->whereDate("cr.updated_at", $date->now)->where('cr.status_id', 6);
            } elseif ($main_filter == "change-closed-yesterday") {
                $db->whereDate("cr.updated_at", $date->yesterday)->where('cr.status_id', 6);
            } elseif ($main_filter == "change-closed-seven-day") {
                $db->whereBetween("cr.updated_at", [$date->last_7_days, $date->now])->where('cr.status_id', 6);
            } elseif ($main_filter == "change-closed-year") {
                $db->whereBetween("cr.updated_at", [$start_year, $end_year])->where('cr.status_id', 6);
            } elseif ($main_filter == "success") {
                $db->where('cr.status_id', 6)->where('cr.close_state', 1);
            }
            
            $return['total'] = $db->count();
            $return['filtered'] = $return['total'];

            $is_searching = false;

            if(isset($req["filters"])) {
                $filters = $req["filters"];
                if(isset($filters["status"]) && $filters['status'] && $filters['status'] != "null"  && $filters['status'] != "[]") {
                    $db->whereIn("cr.status_id", json_decode($filters['status']));
                }
                if(isset($filters["priority"]) && $filters['priority'] && $filters['priority'] != "null" && $filters['priority'] != "[]") {
                    $db->WhereIn("cr.priority_id", json_decode($filters['priority']));
                }
                if(isset($filters["change_type"]) && $filters['change_type'] && $filters['change_type'] != "null" && $filters['change_type'] != "[]") {
                    $db->WhereIn("cr.change_type_id", json_decode($filters['change_type']));
                }
                if(isset($filters["category"]) && $filters['category'] && $filters['category'] != "null" && $filters['category'] != "[]") {
                    $db->WhereIn("cr.category_id", json_decode($filters['category']));
                }
                if(isset($filters["requester"]) && $filters['requester'] && $filters['requester'] != "null" && $filters['requester'] != "[]") {
                    $db->WhereIn("cr.change_requester", json_decode($filters['requester']));
                }
                if(isset($filters["manager"]) && $filters['manager'] && $filters['manager'] != "null" && $filters['manager'] != "[]") {
                    $db->WhereIn("cr.change_manager", json_decode($filters['manager']));
                }

                $based_on_possible = ['1'=>'cr.created_at', '2'=>'cr.closed_date', '3'=>'cr.updated_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 3 ) {
                    if(isset($filters["from_date"]) && $filters["from_date"] && $filters["from_date"] != "null") {
                        $from_date = CommonHelper::getDateAs($filters["from_date"], "Y-m-d H:i:s", "d/m/Y H:i:s");
                        $to_date = CommonHelper::getDateAs($filters["to_date"], "Y-m-d H:i:s" , "d/m/Y H:i:s");
                        if($from_date && $to_date) {
                            //$whereStr = sprintf('(date(%1$s) >= "%2$s" and date(%1$s) <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            //$db->whereRaw($whereStr);
                            $db->whereBetween($based_on_possible[$filters['based_on']], [$from_date, $to_date]);
                        }
                    }
                }
                
                $is_searching = true;
            }

            if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                if(substr($search_key, 0, 1) == "#" && strlen($search_key) > 1) {
                    $whereStr = sprintf('(cr.record_tag like "%1$s")', substr($search_key, 1));      
                } else {
                    $whereStr = sprintf('(cr.record_tag like "%%%1$s%%" or cr.subject like "%%%1$s%%" or cat.name like "%%%1$s%%" or st.name like "%%%1$s%%" or pri.name like "%%%1$s%%" or im.name like "%%%1$s%%" or rsk.name like "%%%1$s%%" or ct.name like "%%%1$s%%" or concat_ws(" ", creq.first_name, creq.last_name) like "%%%1$s%%" or concat_ws(" ", cmgr.first_name, cmgr.last_name) like "%%%1$s%%" or DATE_FORMAT(cr.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                }
                $db->whereRaw($whereStr);
                $is_searching = true;
            }

            if($is_searching) {
                $return['filtered'] = $db->count();
            }

            if( isset($req["order"]["id"]) && isset($fields[$req["order"]["id"]]) && in_array($req["order"]["dir"], [1,2]) ) {
                $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
                $db->orderBy($fields[$req["order"]["id"]], $dir);
            }

            $page = $request->input("page", 1);
            $take = $request->input("size", 10);
            $skip = ($page * $take) - $take;
            if($return["filtered"] < $skip) {
                $page = 1;
                $skip = 0;
            }

            $db->skip($skip);
            $db->take($take);

            $return["data"] = $db->get();
            foreach($return["data"] as $k => $v) {
                $cab = Cab::where("id", $v->cab_id)->first();
                if(!empty($cab)) {
                    $return["data"][$k]->cab_name = $cab->name;
                }
                $return["data"][$k]->param_record_tag = $v->record_tag;
                $return["data"][$k]->color_code = CommonHelper::getChangeRequestColorCode($v);
            }
            $return["page"] = $page;
            $return["status"] = 'success';
            $return["msg"] = 'Change Request fetched successfully.';
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error("CM ajax listing: ". $e->getMessage());
            return response()->json($return);
        }
    }

    /* info page to view the change request */
    public function info(Request $request, $param) {
        try {
            $return = [
                "msg" => trans('content.change_management_fields.Invalid_Access'),
                "status" => "fail"
            ];
            if(! Auth::user()->hasPermissionTo('ChangeRequestView')) {
                $return["msg"] = trans('content.user_fields.Permission_denied');
                return response()->json($return);
            }

            $record = Record::find($param);
            if($record == null) {
                $return["msg"] = trans('content.change_management_fields.Unable_to_get_the_record');
                return response()->json($return);
            }

            $viewData = array();
            $viewData['statuses'] = Status::all();
            $viewData['priorities'] = Priority::all();
            $viewData['impacts'] = Impact::all();
            $viewData['risks'] = Risk::all();
            $viewData['changeTypes'] = ChangeType::all();
            $viewData['categories'] = Category::all();
            $viewData['cabs'] = Cab::all();
            $viewData["approval_requests"] = ApprovalRequest::select('cm_approval_requests.id as approval_request_id', 'cm_approval_requests.approve_status', 'cm_approval_requests.user_id', 'cm_approval_requests.comments', 'cm_approval_requests.cab_id', 'cm_approval_requests.hierarchy_approval','cm_approval_requests.hierarchy_level')
                ->addSelect( DB::raw('case when cm_approval_requests.approve_status = 1 then "Approve" when cm_approval_requests.approve_status = 2 then "Reject" else "Requested" end as status_name'))
                ->addSelect(DB::raw('case when cm_approval_requests.user_id is not null then concat(u.first_name, " ", u.last_name, " @ ", u.username) else "" end as approval_name'))
                ->addSelect(DB::raw('DATE_FORMAT(cm_approval_requests.created_at, "%d %b %Y %h:%i %p") as updated_at_format'))
                ->addSelect(DB::raw('DATE_FORMAT(cm_approval_requests.updated_at, "%d %b %Y %h:%i %p") as created_at_format'))
                ->leftJoin('users as u', 'cm_approval_requests.user_id', '=', 'u.id')
                ->where('cm_approval_requests.record_id',$record->id)->get();
            // $viewData["relevant_tickets"] = $record->relevantTickets;

            $viewData["relevant_tickets"] = RelevantTicket::select('cm_relevant_tickets.id as crt_id','tkt_tickets.id','tkt_tickets.subject','dep.name as dep_name','s.name as status','pc.name as pc_name','sc.name as sc_name')
            ->addSelect(DB::raw('DATE_FORMAT(cm_relevant_tickets.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'))
            ->leftJoin('tkt_tickets', 'tkt_tickets.id', '=', 'cm_relevant_tickets.ticket_id')
            ->leftJoin('departments as dep', 'tkt_tickets.department_id', '=', 'dep.id')
            ->leftJoin('tkt_problem_categories as pc', 'tkt_tickets.problem_category_id', '=', 'pc.id')
            ->leftJoin('tkt_problem_categories as sc', 'tkt_tickets.sub_category_id', '=', 'sc.id')
            ->leftJoin('tkt_statuses as s', 'tkt_tickets.status_id', '=', 's.id')
            ->where('cm_relevant_tickets.record_id',$record->id)->get();
            
            $viewData["relevant_tasks"] = RelevantTask::select('t.id', 't.name', 'st.name as statusName', 'pri.name as priorityName','t.description')
            ->addSelect(DB::raw('DATE_FORMAT(t.due_date, "%d %b %Y") as due_date_at'))
            ->addSelect(DB::raw('DATE_FORMAT(t.start_date, "%d %b %Y") as start_date_at'))
            ->addSelect(DB::raw('DATE_FORMAT(t.end_date, "%d %b %Y") as end_date_at'))
            ->leftJoin('tasks as t', 't.id', '=', 'cm_relevant_tasks.task_id')
            ->leftJoin('task_statuses as st', 't.status_id', '=', 'st.id')
            ->leftJoin('task_priorities as pri', 't.priority_id', '=', 'pri.id')
            ->leftJoin('task_types as ct', 't.type_id', '=', 'ct.id')
            ->leftJoin('users as asgn', 't.assigned_to', '=', 'asgn.id')
            ->leftJoin('projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('cm_records as c', 't.change_id', '=', 'c.id')
            ->where('cm_relevant_tasks.record_id',$record->id)->get();

            $viewData["relevant_devices"] = RelevantDevice::select('cm_relevant_devices.id as crd_id', 'a.name', 'a.asset_tag', 'a.serial')
            ->addSelect(DB::raw('concat(asgn.first_name, " ", asgn.last_name, " @ ", asgn.username) as assigned_name'))
            ->addSelect(DB::raw('DATE_FORMAT(cm_relevant_devices.created_at, "%d %b %Y %h:%i %p") as created_at_format'))
            ->leftJoin('assets as a', 'a.id', '=', 'cm_relevant_devices.device_id')
            ->leftJoin('users as asgn', 'a.assigned_to', '=', 'asgn.id')
            ->where('cm_relevant_devices.record_id',$record->id)->get();

            $viewData["close_states"] = CloseState::all();
            $viewData['change_id'] = DB::table('tasks')->select('tasks.id','tasks.name')->whereRaw('FIND_IN_SET("'. $record->id .'",tasks.change_id)')->get();
            $viewData["attachments"] = $record->attachments;
            $cab_approval_name = $record->cab_id != null ? Cab::find($record->cab_id)->getApprovalModeName() : '';
            foreach($viewData["attachments"] as $v) {
                $v->attachment_link = url("/change-management/view-image", $v->id);
            }
            if($record->status_id == 3) {
                $viewData['statuses'] = Status::select('id','name')->whereIn('id',[3, 7, 8])->get();
            }
            $permission = false;
            if(Auth::user()->hasAnyRole(['SuperAdmin']) || in_array(Auth::user()->id,[$record->change_manager,$record->change_implementer])) {
                $permission = true;
            }
            $return_val["record"] = !empty($record) ? $record : [];
            $return_val["viewData"] = !empty($viewData) ? $viewData : [];
            $return_val["attachments"] = !empty($attachments) ? $attachments : [];
            $return_val["cab_approval_name"] = !empty($cab_approval_name) ? $cab_approval_name : [];
            $return_val["permission"] = !empty($permission) ? $permission : [];
            $return["data"] = !empty($return_val) ? $return_val : [];
            $return["status"] = 'success';
            $return["msg"] = 'Change Request Info fetched successfully.';
        }
        catch(\Exception $e) {
            Log::error("CRM ajax info Api: ". $e->getMessage());
            return response()->json($return);
        }
        return response()->json($return);
    }

    /* to return the filter options for the service request */
    public function getRequestsFilterOptions(Request $request) {
        try {
            $return = ["status" => "fail", "msg" => "Unable to get filter options"];

            $statuses = Status::select('id', 'name')->get();
            $priorities = Priority::select('id', 'name')->get();
            $changeTypes = ChangeType::select('id', 'name')->get();
            $categories = Category::select('id', 'name')->get();
            $filter_by_dates_opts = Record::filterByDateOpts();

            $return['status'] = "success";
            $return['msg'] = 'Change filter fetched successfully.';
            $return['statuses'] = $statuses;
            $return['priorities'] = $priorities;
            $return['changeTypes'] = $changeTypes;
            $return['categories'] = $categories;
            $return['filter_by_dates_opts'] = $filter_by_dates_opts;
        }
        catch(\Exception $e) {
            Log::error("CRM ajax getRequestsFilterOptions Api: ". $e->getMessage());
            return response()->json($return);
        }
        return response()->json($return);
    }

    /* add the basic details of change request */
    public function addRecord(Request $request) {
        $return = [
            "status" => "fail",
            "msg" => trans('content.change_management_fields.Unable_add_the_change_request')
        ];
        try {
            DB::beginTransaction();
            $data = $request->only('subject','status_id','priority_id','impact_id','risk_id','change_type_id','scheduled_start_date','scheduled_end_date','cost','category_id','change_description','reason_description','risk_description','impact_description','rollout_plan','fallback_plan','change_requester','change_manager','change_implementer','change_reviewer');

            $rules = [
                'subject' => 'required|string|max:255',
                'change_type_id' => 'required|numeric|max:255',
                'impact_id' => 'required|numeric',
                'risk_id' => 'required|numeric',
                'category_id' => 'required|numeric',
                'change_implementer' => 'required|array',
                'change_description' => 'required|string|max:2000',
                'reason_description' => 'required|string|max:2000',
                'risk_description' => 'required|string|max:2000',
                'impact_description' => 'required|string|max:2000',
                'rollout_plan' => 'required|string|max:2000',
                'fallback_plan' => 'required|string|max:2000',
                'change_manager' => 'required|numeric',
                'change_reviewer' => 'nullable|numeric',
                'cost' => 'nullable|numeric',
                'scheduled_start_date' => 'nullable|string|date_format:d/m/Y h:i A',
                'scheduled_end_date' => 'nullable|string|date_format:d/m/Y h:i A',
                'impacted_services' => 'nullable|string',
                'impacted_devices' => 'nullable|string',
                'status_comments' => 'nullable|string',
            ];
            $msg = [];
            $validator = Validator::make($data, $rules, $msg);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $data['change_implementer'] = json_encode($data['change_implementer']);
            $data['status_id'] = 1;
            $data['change_requester'] = Auth::user()->id;
            $data['priority_id'] = 3;
            $cm = new Record;
            $cm->fill($data);
            $cm->scheduled_start_date = isset($data["scheduled_start_date"]) ? CommonHelper::getDateAs($data["scheduled_start_date"], "Y-m-d H:i:s", "d/m/Y h:i A") : null;
            $cm->scheduled_end_date =  isset($data["scheduled_end_date"]) != null ? CommonHelper::getDateAs($data["scheduled_end_date"], "Y-m-d H:i:s", "d/m/Y h:i A") : null;

            if($cm->save()) {
                $cm->fillRecordTag();
                $return = [
                    "status" => "success",
                    "msg" => trans('content.change_management_fields.New_change_request').' '.$cm->record_tag .' '.trans('content.change_management_fields.has_been_created_successfully')
                ];

                $history = new History();
                $history->user_id = Auth::user()->id;
                $history->record_id = $cm->id;
                $history->save();
                HistoryEntry::create(['history_id'=>$history->id, 'change_info'=>$return['msg']]);
                /**update record_id attachment */
                $attachments = Attachment::where("tmp_id", $request->tmp_id)->select("id", "record_id")->get();
                if(isset($attachments)) {
                    foreach ($attachments as $attachment) {
                        $attachment->record_id = $cm->id;
                        $attachment->update();
                    }
                }

                // send email notification
                if(isset($data['change_requester']) && $data['change_requester'] != ''){
                    $user = User::find($data['change_requester']);
                    $otherUsers = $cc_users = $user_ids = [];
                    if(isset($request->change_manager)) {
                        array_push($otherUsers, $request->change_manager);
                    }
                    if(isset($request->change_implementer)) {
                        array_merge($otherUsers, $request->change_implementer);
                    }
                    if(isset($request->change_reviewer)) {
                        array_push($otherUsers, $request->change_reviewer);
                    }
                    if(!empty($otherUsers)) {
                        $users = User::select('email')->whereIn('id',$otherUsers)->get();
                        foreach($users as $key=>$mail) {
                            if(isset($mail->email) && $mail->email != null) {
                                array_push($cc_users,$mail->email);
                            }
                            if(isset($mail->id) && $mail->id != null) {
                                array_push($user_ids,$mail->id);
                            }
                        }
                    }

                    if(!empty($user)) {
                        Mail::to($user->email)->cc($cc_users)->queue(new ChangeMail($cm,$user));
                    }
                    // send push notification
                    $notify_people = [];
                    array_push($notify_people, $user->id);
                    $notificationText = trans('content.change_management_fields.New_change_request').' '.$cm->record_tag .' '.trans('content.change_management_fields.has_been_created_successfully');
                    $data = [
                        'title' => $notificationText,
                        'data' => $cm,
                        'notify' => $notify_people,
                    ];
                    $sendNotifications = CommonHelper::sendPushNotification($data);
                    $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                    if($sendNotifications != false) {
                        $response = json_decode($sendNotifications);
                        if(isset($response->failure) && $response->failure == 1) {
                            Log::error("Change Management request id push notification:" . $cm->id. " notification error " .json_encode($response));
                        }
                    }
                }
                DB::commit();
                Log::info("CRM addRecord Api:" . $cm->id . " ud:" . Auth::user()->id . " : " . json_encode($request->all()));
                return response()->json($return);
            }
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("CRM ajax addRecord Api: ". $e->getMessage());
            return response()->json($return);
        }
    }

    /* edit the basic details of change request */
    public function editRecord(Request $request) {
        $return = [
            "status" => "fail",
            "msg" => trans('content.change_management_fields.Unable_edit_the_change_request')
        ];
        try {
            DB::beginTransaction();
            $data = $request->only('id','subject','status_id','priority_id','impact_id','risk_id','change_type_id','scheduled_start_date','scheduled_end_date','cost','category_id','change_description','change_requester','change_manager','change_implementer','change_reviewer');

            $rules = [
                'id' => 'required|numeric',
                'subject' => 'required|string|max:255',
                'change_type_id' => 'required|numeric|max:255',
                'category_id' => 'required|numeric',
                'impact_id' => 'required|numeric',
                'risk_id' => 'required|numeric',
                'change_implementer' => 'required|array',
                'change_manager' => 'required|numeric',
                'change_description' => 'required|string|max:2000',
                'change_reviewer' => 'nullable|numeric',
                'cost' => 'nullable|numeric',
                'scheduled_start_date' => 'nullable|string|date_format:d/m/Y h:i A',
                'scheduled_end_date' => 'nullable|string|date_format:d/m/Y h:i A',
                'impacted_services' => 'nullable|string',
                'impacted_devices' => 'nullable|string',
                'status_comments' => 'nullable|string',
            ];
            $msg = [];
            $validator = Validator::make($data, $rules, $msg);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $record = Record::find($data['id']);
            $lblFiels = [
                'change_type_id' => 'Change Type',
                'category_id' => 'Category',
                'status_id' => 'Status',
                'change_implementer' => 'Change Implementer',
                'change_manager' => 'Change Manager',
                'change_requester' => 'Change Requester',
                'change_reviewer' => 'Change Reviewer',
                'impact_id' => 'Impact',
                'priority_id' => 'Priority',
                'risk_id' => 'Risk',
                'cost' => 'Cost',
                'subject' => 'Subject',
                'scheduled_start_date' => 'Scheduled Start Date',
                'scheduled_end_date' => 'Scheduled End Date',
                'change_description' => 'Change Description',
                'reason_description' => 'Reason Description',
                'risk_description' => 'Risk Description',
                'impact_description' => 'Impact Description',
                'rollout_plan' => 'Rollout Plan',
                'fallback_plan' => 'Fallback Plan'
            ];
            $changes = [];
            $data['change_implementer'] = json_encode($data['change_implementer']);
            $data['status_id'] = 1;
            $data['change_requester'] = Auth::user()->id;
            $data['priority_id'] = 3;
            foreach($data as $k=>$v) {
                if($k == 'scheduled_start_date' || $k == 'scheduled_end_date') {
                    if( $record->$k != CommonHelper::getDateAs($data[$k], "Y-m-d H:i:s", "d/m/Y h:i A")) {
                        $changes[] = $lblFiels[$k] . " changed to " . $v;
                    }
                }
                else if($v != $record->$k) {
                    $changes[] = $lblFiels[$k] . " changed to " . $this->_getChangedValueText($k, $v);
                }
            }

            $record->fill($data);
            $record->scheduled_start_date = isset($data["scheduled_start_date"]) ? CommonHelper::getDateAs($data["scheduled_start_date"], "Y-m-d H:i:s", "d/m/Y h:i A") : null;
            $record->scheduled_end_date = isset($data["scheduled_start_date"]) ? CommonHelper::getDateAs($data["scheduled_end_date"], "Y-m-d H:i:s", "d/m/Y h:i A") :null;

            if($record->save()) {
                $return["msg"] = trans('content.change_management_fields.The_Change_Request').' '. $record->record_tag .' '. trans('content.change_management_fields.has_updated_successfully');
                $return["status"] = "success";

                $history = new History();
                $history->user_id = Auth::user()->id;
                $history->record_id = $record->id;
                $history->save();
                foreach($changes as $c) {
                    HistoryEntry::create(['history_id'=>$history->id, 'change_info'=>$c]);
                }
                DB::commit();
                Log::info("CRM editRecord Api:" . $record->id . " ud:" . Auth::user()->id . " : " . json_encode($request->all()));
                return response()->json($return);
            }
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("CRM ajax editRecord Api: ". $e->getMessage());
            return response()->json($return);
        }
    }

    /* for history entries */
    private function _getChangedValueText($field, $value) {
        $possible_classes = [
            'change_type_id' => 'ChangeType',
            'category_id' => 'Category',
            'status_id' => 'Status',
            'change_implementer' => 'User',
            'change_manager' => 'User',
            'change_requester' => 'User',
            'change_reviewer' => 'User',
            'impact_id' => 'Impact',
            'priority_id' => 'Priority',
            'risk_id' => 'Risk',
            'close_state' => 'Close State'
        ];

        if($value == "" || $value == null) {
            return 'NULL';
        }

        $return = $value;
        try {
            switch ($field) {
                case 'change_type_id':
                    $c = ChangeType::find($value);
                    return $c->name;
                    break;
                case 'category_id':
                    $c = Category::find($value);
                    return $c->name;
                    break;
                case 'status_id':
                    $c = Status::find($value);
                    return $c->name;
                    break;
                case 'change_implementer':
                    $implementers = User::whereIn('id', json_decode($value))->get();
                    $implementerNames = $implementers->map(function ($implementer) {
                        // Check if the user is found before accessing properties
                        if($implementer) {
                            return $implementer->getGuranteedNameText(true) . " (" . $implementer->username . ")";
                        }
                        return null;
                    })->filter()->toArray();
                    return implode(', ', $implementerNames);
                    break;
                case 'change_manager':
                    $c = User::find($value);
                    return $c->fullName() . " (" . $c->username . ")";
                    break;
                case 'change_requester':
                    $c = User::find($value);
                    return $c->fullName() . " (" . $c->username . ")";
                    break;
                case 'change_reviewer':
                    $c = User::find($value);
                    return $c->fullName() . " (" . $c->username . ")";
                    break;
                case 'impact_id':
                    $c = Impact::find($value);
                    return $c->name;
                    break;
                case 'priority_id':
                    $c = Priority::find($value);
                    return $c->name;
                    break;
                case 'risk_id':
                    $c = Risk::find($value);
                    return $c->name;
                    break;
                case 'close_state':
                    $c = CloseState::find($value);
                    return $c->name;
                    break;
                default:
                    return $value;
            }
        }
        catch(\Exception $e) {
            return $return;
        }

        return $return;
    }

    public function approveRejectRequest(Request $request) {
        $return = [
            "status" => "fail",
            "msg" => trans('content.change_management_fields.Invalid_Access')
        ];
        try {

            $data = $request->only('approve_status', 'comments', 'approve_request_id');

            $rules = [
                'approve_request_id' => 'required|integer|exists:cm_approval_requests,id',
                'approve_status' => 'required|integer',
                'comments' => 'required|string|max:2000'
            ];
            $msgs = [];

            $validator = Validator::make($data, $rules, $msgs);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $approvalRequest = ApprovalRequest::find($data['approve_request_id']);
            if($approvalRequest->user_id == Auth::user()->id) {
                DB::beginTransaction();
                $approvalRequest->approve_status = $data["approve_status"];
                $approvalRequest->comments = trim($data["comments"]);
                if($approvalRequest->save()) {
                    $record = Record::find($approvalRequest->record_id);
                    if($approvalRequest->hierarchy_approval == 1 && $approvalRequest->approve_status == 1) {
                        $getNextLevel = ApprovalRequest::where('record_id', '=', $approvalRequest->record_id)->where('cab_id', '=', $approvalRequest->cab_id)->where('hierarchy_level', '>', $approvalRequest->hierarchy_level)->orderBy('hierarchy_level', 'asc')->get();
                        if(count($getNextLevel)) {
                            /* here, send approve request mail to next level people */
                            $getNextLevel[0]->approve_status = 3;
                            $getNextLevel[0]->save();
                            $user = User::find($getNextLevel[0]->user_id);
                            if(isset($user->email) && $user->email != null){
                                Mail::to($user->email)->send(new SendApprovalRequest($record, $getNextLevel[0]));
                            }
                            // send push notification
                            array_push($notify_people, $user->id);
                            $notificationText = 'Change Management request #' .$record->record_tag . 'has been raised on IT Portal.IT Request Portal. Following are the details for your review and approval/rejection.';
                            $data = [
                                'title' => $notificationText,
                                'data' => $record,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("Change Management request id push notification:" . $record->id. " notification error " .json_encode($response));
                                }
                            }
                        }
                    }

                    $changeStatus = false;
                    $approveCount = ApprovalRequest::where('record_id',$approvalRequest->record_id)->where('approve_status', 1)->count();
                    if($approvalRequest->cab->hierarchy_approval == 2) {
                        if($approvalRequest->cab->required_minimum_approvals == $approveCount){
                            $changeStatus = true;
                        }
                    }
                    if($approvalRequest->cab->hierarchy_approval == 3 || $approvalRequest->cab->hierarchy_approval == 1) {
                        if($approvalRequest->cab->totMembers() == $approveCount){
                            $changeStatus = true;
                        }
                    }
                    if($approvalRequest->cab->hierarchy_approval == 4 || $approvalRequest->cab->hierarchy_approval == 5) {
                        if($approveCount == 1) {
                            $changeStatus = true;
                        }
                    }
                    if($changeStatus) {
                        $record->status_id = 1;
                        $record->save();
                    }
                    $return['msg'] = trans('content.change_management_fields.Your_response_has_been_sent_successfully');
                    $return["status"] = "success";

                    $history = new History();
                    $history->user_id = Auth::user()->id;
                    $history->record_id = $record->id;
                    $history->save();
                    $c = 'Responded as ' . $approvalRequest->statusLabel() . ' <br/>Comment is, ' . $approvalRequest->comments;
                    HistoryEntry::create(['history_id' => $history->id, 'change_info' => $c]);
                    DB::commit();
                    return response()->json($return);
                }
            } else {
                $return = [
                    "msg" => trans('content.uer_fields.unauthorized_access'),
                    "status" => "fail"
                ];
                return response()->json($return);
            }
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("approveRejectRequest CM Api: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function ajaxComments(Request $request, $id) {
        $return = [
            'status' => 'fail',
            'msg' => trans('content.procurement_fields.Unable_to_add_the_given_request')
        ];
        DB::beginTransaction();
        $data = $request->only('comments_user');
        try {
            $rules = [
                'comments_user' => 'required|string',
            ];
            $messages = [
                'comments_user.required' => trans('content.change_management_fields.Request_comment'),
            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $notify_people = [];
            $record_id = $id;
            $changeRequestEmail = [];
            $changeRequestTeamEmails = [];
            $notify_people = [];
            $changeRequestfollowing = new CMFollowing();
            $changeRequestfollowing->record_id = $record_id;
            $changeRequestfollowing->updated_by = Auth::user()->id;
            $changeRequestfollowing->is_note = isset($request->is_note) ? $request->is_note : 0;
            $processResult = Attachment::processForB64Imgs($request->comments_user, $record_id, Auth::user()->id);
            $changeRequestfollowing->remarks = $processResult['content'];
            if (isset($request->follow_cc) && $request->follow_cc == 1 && !empty($request->external_comment)) {
                foreach ($request->external_comment as $comment) {
                    if (is_numeric($comment)) {
                        $user = User::find($comment);
                        if ($user) {
                            $changeRequestEmail[] = $user->email;
                            $notify_people[] = $user->id;
                        }
                    } else {
                        $changeRequestEmail[] = $comment;
                    }
                }
                $changeRequestfollowing->comment_change_id = implode(',', $request->external_comment);
            }
            $changeRequestfollowing->save();
            $ats = Attachment::where("record_id", "=", $record_id)->where("tmp_id", $request->tmp_id)->get();
            if ($ats && count($ats)) {
                foreach ($ats as $at) {
                    $at->following_id = $changeRequestfollowing->id;
                    $at->save();
                }
            }

            if( count($processResult['attachments']) ) {
                Attachment::whereIn('id', $processResult['attachments'])->update(['following_id' => $changeRequestfollowing->id]);
            }
            $extid = [];
            $cm_request = Record::findOrFail($record_id);
            if( config('mail.service_enabled') && $request->is_note != 1 ) {
                $alertnotify = null;
                if(Settings::first()->alerts_enabled == 1){
                    $alertnotify = CommonHelper::getGlobalAlertEmail();
                }
                $user = User::find(Auth::user()->id);
                $add_back_trail = $request->add_back_trail == 1 ? true : false;

                if($cm_request->checkPermissionForCM('mail')) {
                    $extid = [$cm_request->change_manager,$cm_request->change_requester,$cm_request->change_reviewer];
                    foreach(json_decode($cm_request->change_implementer) as $val){
                        array_push($extid,(int) $val);
                    }
                    if(!empty($extid)) {
                        $extUser = User::whereIn('id',$extid)->get();
                        foreach($extUser as $s) {
                            array_push($notify_people, $s->id);
                            $changeRequestEmail[] = $s->email;
                        }
                    }
                }

                if($user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL) ){
                    try {
                        $cc_emails = [];
                        if($alertnotify) {
                            $cc_emails = [];
                            foreach ($alertnotify as $email) {
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    $cc_emails[] = $email;
                                }
                            }
                            $all_cc_emails = array_unique(array_merge($cc_emails, $changeRequestEmail));
                            Mail::to($user->email)->cc($all_cc_emails)->queue(new UserComment($cm_request,$user, $changeRequestfollowing->remarks, $changeRequestfollowing, $add_back_trail));
                        }
                        else {
                            Mail::to($user->email)->cc($changeRequestEmail)->queue(new UserComment($cm_request,$user, $changeRequestfollowing->remarks, $changeRequestfollowing, $add_back_trail));
                        }
                    }
                    catch(\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            $notificationText = $cm_request->procure_tag. trans('content.change_management_fields.Please_check');
            $data = [
                'title' => $notificationText,
                'data' => $changeRequestfollowing,
                'notify' => $notify_people,
            ];
            $sendNotifications = CommonHelper::sendPushNotification($data);
            $sendNotifications = CommonHelper::sendWhatsappNotification($data);

            $return["msg"] = trans('content.change_management_fields.commented_successfully');
            $return["status"] = "success";

            DB::commit();
            Log::info("ajaxComment Change Requester:" . $record_id . " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Api ajaxComments Change Requester: " . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getTimeline(Request $request) {
        try {
            $return = [
                "msg" => "Unable to get the request timeline.",
                "status" => "fail"
            ];
            $currentUser = Auth::user();
            $currentUserId = Auth::user()->id;
            $request_id = $request->id;
            $record = Record::select('id')->where('id', $request_id)->first();
            if (!$request_id) {
                $return = [
                    "msg" => "No id found.",
                    "status" => "success"
                ];
                return response()->json($return);
            }
            $db = DB::table('cm_following as cf');
            $db->leftJoin('users as u', 'cf.updated_by', '=', 'u.id');
            $db->where('cf.record_id', '=', $request_id);
            $db->whereNotNull('cf.remarks');

            if(!empty($record) && $record->checkPermissionForCM('notes') == false) {
                $db->where('cf.is_note', 0);
            }
            $db->select('cf.*');
            $db->addSelect(DB::raw('IFNULL(concat(u.first_name, " ", u.last_name, " @ ", u.username), "System") as commenter'));
            $db->addSelect(DB::raw('DATE_FORMAT(cf.updated_at, "%d %b %y %h:%i %p") as updated_at_format'));
            $db->orderBy('cf.updated_at', 'desc');

            $page = $request->input("page", 1);
            $take = $request->input("size", 10);
            $skip = ($page * $take) - $take;
            $db->skip($skip);
            $db->take($take);

            $tls = $db->get();

            if ($tls && count($tls)) {
                $embedded_attachments = Attachment::getEmbeddedAttachments($request_id);
                foreach ($tls as $tl) {
                    $tl->remarks = CommonHelper::renderChangeRequestContent($tl->remarks, $embedded_attachments);
                    $tl->attachments = Attachment::where('following_id', '=', $tl->id)->select('id', 'original_file_name as name', 'extension as ext', DB::raw('case when thumbnail is not null then 1 else 0 end as thumb'))->get();

                    if (isset($tl->comment_change_id)) {
                        $commentChangeIdParts = explode(',', $tl->comment_change_id); 
                        $userEmails = []; 
                        
                        foreach ($commentChangeIdParts as $userId) {
                            $user = User::find($userId);
                            if ($user) {
                                $userEmails[] = $user->email; 
                            }
                            else{
                                $userEmails[] = $userId;
                            }
                            if (!empty($userEmails)) {
                                $tl->cc_emails = implode(',', $userEmails);
                            }
                        }
                    }                
                }
            }

            $return['data'] = $tls;
            $return['status'] = "success";
            $return['msg'] = "";
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Api ajaxComments list: " . $e->getMessage());
        }
    }

    public function getHistory(Request $request, $param) {
        $return = [
            "msg" => trans('content.change_management_fields.Invalid_Access'),
            "status" => "fail"
        ];
    
        try {
            $get_record = Record::find($param);
            if (empty($get_record)) {
                $return["msg"] = trans('content.change_management_fields.Unable_to_get_the_record');
                return response()->json($return);
            }

            $history = History::select('cm_histories.*',DB::raw("concat(users.first_name,' ',users.last_name) as user_name"))->leftJoin('users','users.id','cm_histories.user_id')
            ->where('record_id', $get_record->id)->orderBy('id', 'desc')->get();
            
            foreach($history as $h){
                $h->entries;
            }
            $return['data'] = $history;
            $return['status'] = "success";
            $return['msg'] = "";
        } catch(\Exception $e) {
            Log::error('Error in getHistory API: ' . $e->getMessage());
            return response()->json($return);
        }
    
        return response()->json($return);
    }
    
}

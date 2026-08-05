<?php

namespace App\Http\Controllers\Auth\Api\Procurement;

use App\Helpers\Old;
use App\Http\Controllers\Controller;

use App\Helpers\Common as CommonHelper;
use App\Models\Location;
use App\Models\Procurement\Status;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use stdClass;
use App\Models\Procurement\PabMember;
use App\Models\Procurement\Pab;
use App\Models\Procurement\Request as ProcureRequest;
use App\Models\Procurement\RequestItem;
use App\Models\Procurement\Priority;
use App\Models\Procurement\ApprovalRequest;
use App\Models\Procurement\HistoryEntry;
use App\Models\Procurement\UserPrivilage;
use App\Models\Procurement\History;
use App\Models\User;
use App\Models\Procurement\Quotation;
use App\Models\Settings;
use Illuminate\Support\Facades\Mail;
use App\Mail\Procurement\CreatedUser;
use App\Mail\Procurement\UserComment;
use App\Mail\Procurement\ApproverInfo;
use App\Mail\Procurement\StatusChange;
use App\Mail\Procurement\RequestApproved;
use App\Mail\Procurement\RequestRejected;
use App\Mail\Procurement\PoGenerated;
use App\Models\Procurement\Attachment;
use App\Models\Procurement\QuotationItem;
use App\Models\Procurement\Budget;
use App\Mail\Procurement\Vendorstatus;
use App\Models\Procurement\ProcurementFollowing;
use App\Models\Procurement\BudgetApprovalMember;
use App\Mail\Procurement\InvoiceUpload;
use App\Mail\Procurement\VendorQuotationUpload;
use App\Mail\Procurement\QuotationSelected;
use App\Models\Procurement\Unit;
use App\Models\Supplier;
use App\Models\Department;
use Validator;

class ListController extends Controller
{

    public function list(Request $request ,$main_filter="my-tickets") {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch records'
            ];

            $page = $request->index ? $request->index : 0;
            $take = $request->list_size ? $request->list_size : 20;
            $skip = $page * $take;
            $req = $request->all();
            $currentUser = Auth::user();
            
            $getUserPab = PabMember::where('user_id', $currentUser->id)->pluck('pab_id');
            $getUsersup = Supplier::select('id')->where('user_supplier_id', $currentUser->id)->first();
            $procurementRole = $currentUser->procurementRole != null ? $currentUser->procurementRole->getRole() : null;
    
            $db = DB::table('procure_requests as pr');
            $db->leftJoin('departments as dep', 'pr.department_id', '=', 'dep.id');
            $db->leftJoin('departments as cc', 'pr.cost_center', '=', 'cc.id');
            $db->leftJoin('users as creator', 'pr.creator_id', '=', 'creator.id');
            $db->leftJoin('procure_statuses as status', 'pr.status_id', '=', 'status.id');
            $db->leftJoin('procure_request_priorities as priority', 'pr.priority_id', '=', 'priority.id');
            $db->leftJoin('procure_pabs as pab', 'pr.pab_id', '=', 'pab.id');
            $db->leftJoin('procure_quotations as pq', function($q) {
                $q->on('pr.id', '=', 'pq.pr_id');
                $q->where('pq.po_approved_quotate', '=', '1');
            });
            $db->leftJoin('users as incharge', 'pq.po_created_by', '=', 'incharge.id');
    
            $db->select('pr.id', 'pr.title', 'pr.procure_tag', 'dep.name as dep_name', 'cc.name as cc_name', 'priority.name as priority', 'status.name as status', 'pab.name as pab','pr.vendor_id');
            $db->addSelect(DB::raw('concat_ws(" ", creator.first_name, creator.last_name, "@", creator.username) as creator_name'));
            $db->addSelect(DB::raw('case when incharge.id is not null then concat_ws(" ", incharge.first_name, incharge.last_name, "@", incharge.username) else "" end as incharge_name'));
            $db->addSelect(DB::raw('case when pr.approval_required = 1 then "Required" when pr.approval_required = "null" then "Not Required "else "" end as approval_required'));
            $db->addSelect(DB::raw('DATE_FORMAT(pr.created_at, "%d %b %Y %h:%i %p") as created_at_format'));
            $db->addSelect(DB::raw('DATE_FORMAT(pr.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
            $db->addSelect(DB::raw('DATE_FORMAT(pr.deadline, "%d %b %Y") as deadline_format'));
            
            // if($procurementRole != 'procure_team') {
            //     $db->where(function($query) use($currentUser,$getUserPab) {
            //         $query->where('pr.creator_id', $currentUser->id);
            //         $query->orWhereIn('pr.pab_id', $getUserPab);
            //     });
            // }

            if($main_filter == "my_request") {
                $db->where('pr.creator_id', $currentUser->id);
            }
            if($main_filter == "my_approval") {
                $db->where('pr.creator_id', $currentUser->id);
                $db->Where("pr.approval_required", 1);
            }
            if($procurementRole == null && !empty($getUsersup) && $getUsersup != null && Auth::user()->hasRole('vendor') == true) {
                $db->whereRaw('FIND_IN_SET('.$getUsersup->id.', pr.vendor_id)');
            }
    
            $return['total'] = $db->count();
            $return['filtered'] = $return['total'];
    
            $is_searching = false;
            if(isset($req["filters"])) {
                $filters = $req["filters"];
                if(isset($filters["status"]) && $filters['status'] && $filters['status'] != "null") {
                    $db->where("pr.status_id", (int) $filters['status']);
                }
                if(isset($filters["priority"]) && $filters['priority'] && $filters['priority'] != "null") {
                    $db->orWhere("pr.priority_id", "=", (int) $filters['priority']);
                }
                if(isset($filters["pab"]) && $filters['pab'] && $filters['pab'] != "null") {
                    $db->orWhere("pr.pab_id", "=", (int) $filters['pab']);
                }
                if(isset($filters["approval"]) && $filters['approval'] && $filters['approval'] != "null") {
                    $db->Where("pr.approval_required", "=", (int) $filters['approval']);
                }
                if(isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                    $db->orWhere("pr.department_id", "=", (int) $filters['department']);
                }
                if(isset($filters["cost_center"]) && $filters['cost_center'] && $filters['cost_center'] != "null") {
                    $db->orWhere("pr.cost_center", "=", (int) $filters['cost_center']);
                }
    
                $based_on_possible = ['1'=>'pr.created_at', '2'=>'pr.deadline', '3'=>'pr.closed_at', '4'=>'pr.po_generated_at', '5'=>'pr.approved_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 5 ) {
                    if(isset($filters["from_date"]) && $filters["from_date"] && $filters["from_date"] != "null") {
                        $from_date = CommonHelper::getDateAs($filters["from_date"], "Y-m-d", "d/m/Y");
                        $to_date = CommonHelper::getDateAs($filters["to_date"], "Y-m-d", "d/m/Y");
                        if($from_date && $to_date) {
                            $whereStr = sprintf('(date(%1$s) >= "%2$s" and date(%1$s) <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $db->whereRaw($whereStr);
                        }
                    }
                }
                
                $is_searching = true;
            }
    
            if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                if(substr($search_key, 0, 1) == "#" && strlen($search_key) > 1) {
                    $whereStr = sprintf('(pr.procure_tag like "%1$s")', substr($search_key, 1));      
                }
                else {
                    $whereStr = sprintf('(pr.procure_tag like "%%%1$s%%" or pr.title like "%%%1$s%%" or dep.name like "%%%1$s%%" or status.name like "%%%1$s%%" or cc.name like "%%%1$s%%" or priority.name like "%%%1$s%%" or concat_ws(" ", creator.first_name, creator.last_name, "@", creator.username) like "%%%1$s%%" or pab.name like "%%%1$s%%" or DATE_FORMAT(pr.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(pr.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%"  or DATE_FORMAT(pr.deadline, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                }
                $db->whereRaw($whereStr);
                $is_searching = true;
            }
    
            if($is_searching) {
                $return['filtered'] = $db->count();
            }
    
            $fields = [1=>'pr.procure_tag', 2=>'creator_name', 3=>'status.name', '4'=>'priority.name', 5=>'pab.name', 6=>'dep.name', 7=>'cc.name', 8=>'pr.created_at', 9=>'pr.updated_at'];
            if( isset($req["order"]["id"]) && array_key_exists($req["order"]["id"], $fields) && in_array($req["order"]["dir"], [1, 2]) ) {
                $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
                $db->orderBy( $fields[$req["order"]["id"]], $dir);
            }
        
            $return['current_index'] = (int) $request->index;
            $return['is_prev_index'] = $skip > 0 ? 1 : 0;
            $return['is_next_index'] = $return['filtered'] > ( $skip + $take ) ? 1 : 0;

            $db->skip($skip);
            $db->take($take);

            if(isset($req["toggle"])){
                $return["toggle"] = $req["toggle"];
            }

            $return["page"] = $page;
    
            $return_val["data_val"] = "";
            $return_val["data_val"] = $db->get();
            $return["data"] = $return_val["data_val"];
            $return["status"] = 'success';
            $return["msg"] = 'Procurement fetched successfully.';

            return $return;

        } catch(\Exception $e) {
            Log::error("ListController list() : ".$e->getMessage());
            return $return;
        }
    }

    public function procureDetail(Request $request, $id) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch record'
            ];
            if(!isset($request->id) || $request->id == null) {
                $return["msg"] = trans('content.procurement_fields.procurement_id');
                return response()->json($return);
            }
            $req = $request->all();
            $currentUser = User::find(Auth::user()->id);
            $getUserPab = PabMember::where('user_id', $currentUser->id)->pluck('pab_id');

            $procurementRole = $currentUser->procurementRole != null ? $currentUser->procurementRole->getRole() : null;

            $db = DB::table('procure_requests as pr');
            $db->where('pr.id',$id);
            $db->leftJoin('departments as dep', 'pr.department_id', '=', 'dep.id');
            $db->leftJoin('departments as cc', 'pr.cost_center', '=', 'cc.id');
            $db->leftJoin('users as creator', 'pr.creator_id', '=', 'creator.id');
            $db->leftJoin('procure_statuses as status', 'pr.status_id', '=', 'status.id');
            $db->leftJoin('procure_request_priorities as priority', 'pr.priority_id', '=', 'priority.id');
            $db->leftJoin('procure_pabs as pab', 'pr.pab_id', '=', 'pab.id');
            $db->leftJoin('procure_quotations as pq', function($q) {
                $q->on('pr.id', '=', 'pq.pr_id');
                $q->where('pq.po_approved_quotate', '=', '1');
            });
            $db->leftJoin('users as incharge', 'pq.po_created_by', '=', 'incharge.id');

            $db->select('pr.id', 'pr.title', 'pr.procure_tag', 'dep.name as dep_name', 'cc.name as cc_name', 'priority.name as priority', 'status.name as status', 'pr.status_id', 'pab.name as pab');
            $db->addSelect(DB::raw('concat_ws(" ", creator.first_name, creator.last_name, "@", creator.username) as creator_name'));
            $db->addSelect(DB::raw('case when incharge.id is not null then concat_ws(" ", incharge.first_name, incharge.last_name, "@", incharge.username) else "" end as incharge_name'));
            $db->addSelect(DB::raw('case when pr.approval_required = 1 then "Required" when pr.approval_required = "null" then "Not Required "else "" end as approval_required'));
            $db->addSelect(DB::raw('DATE_FORMAT(pr.created_at, "%d %b %Y %h:%i %p") as created_at_format'));
            $db->addSelect(DB::raw('DATE_FORMAT(pr.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
            $db->addSelect(DB::raw('DATE_FORMAT(pr.deadline, "%d %b %Y") as deadline_format'));
            /*if($procurementRole != 'procure_team') {
                $db->where('pr.creator_id', $currentUser->id);
                $db->orWhereIn('pr.pab_id', $getUserPab);
            }*/

            $return_val["data_val"] = "";
            $return_val["data_val"] = $db->first();

            $quotations = Quotation::select('procure_quotations.*',DB::raw("concat(first_name,' ', last_name) as po_generated_by"),'suppliers.name as supplier_name')
                ->leftJoin('users','users.id','procure_quotations.po_created_by')
                ->leftJoin('suppliers','suppliers.id','procure_quotations.supplier_id')
                ->where('pr_id',$return_val["data_val"]->id)
                ->get();
            foreach($quotations as $q){
                $q->quotationItems = QuotationItem::leftjoin('procure_request_items as pri','pri.id','procure_quotation_items.item_id')
                    ->where('procure_quotation_items.qid',$q->id)->get();
                $quotaionTotal = 0;
                foreach($q->quotationItems as $item) {
                    $item->total_item_price = $item->getTotItemPrice($item->qty);
                    $quotaionTotal += $item->total_item_price;
                }
                $q->quotation_total_amount = $quotaionTotal;
            }

            $return_val["data_val"]->quotations = $quotations;
            $return["data"] = $return_val["data_val"];
            $return["status"] = 'success';
            $return["msg"] = 'Procurement fetched successfully.';

            return $return;

        } catch(\Exception $e) {
            Log::error("ListController procureDetail() : ".$e->getMessage());
            return $return;
        }
    }

    public function procureStatuses(Request $request) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch status'
            ];
            
            $statuses = Status::all();
            
            $return["data"] = $statuses;
            $return["status"] = 'success';
            $return["msg"] = 'Procurement status fetched successfully.';
    
            return $return;

        } catch(\Exception $e) {
            Log::error("ListController procureStatuses() : ".$e->getMessage());
            return $return;
        }
    }

    public function procurePriorities(Request $request) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch priorities'
            ];
            
            $priorities = Priority::all();
            
            $return["data"] = $priorities;
            $return["status"] = 'success';
            $return["msg"] = 'Procurement priorities fetched successfully.';
    
            return $return;

        } catch(\Exception $e) {
            Log::error("ListController procurePriorities() : ".$e->getMessage());
            return $return;
        }
    }

    public function procurePab(Request $request){
        try{
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch PAB'
            ];
            
            $pab = Pab::all();
            
            $return["data"] = $pab;
            $return["status"] = 'success';
            $return["msg"] = 'Procurement PAB fetched successfully.';
    
            return $return;

        } catch(\Exception $e) {
            Log::error("ListController procurePab() : ".$e->getMessage());
            return $return;
        }
    }

    // public function addProcurement
    public function addProcurement(Request $request) {
        $return = [
            'status' => 'fail',
            'msg' => trans('content.procurement_fields.Unable_to_add_the_given_request')
        ];
        DB::beginTransaction();
        $data = $request->only('department_id', 'priority_id', 'title', 'description', 'pab_id', 'deadline', 'cost_center', 'approval_required');
        try {
            $rules = [
                'title' => 'required|string|min:10|max:255',
                'description' => 'nullable|string|max:2000',
                'department_id' => 'required|integer|min:1|exists:departments,id',
                'cost_center' => 'required|integer|min:1|exists:departments,id',
                'priority_id' => 'required|integer|min:1|exists:procure_request_priorities,id',
                'approval_required' => 'required|min:0|max:1',
                'pab_id' => 'nullable|integer|min:1|exists:procure_pabs,id',
                'deadline' => 'required|string|date_format:d/m/Y',
            ];
            $messages = [
                'title.required' => 'Please provide procurement title.',
            ];
            $validator = \Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            if($data["approval_required"] && isset($data["pab_id"]) && !$data["pab_id"]) {
                $return["msg"] = trans('content.procurement_fields.please_select_the_PAB_team');
                return response()->json($return);
            }

            if($data["approval_required"] != 1) {
                $data["pab_id"] = null;
            } else {
                $pab_id = $request->pab_id;
                $pab = Pab::find($pab_id);
                if(!$pab || $pab->totMembers() < 1) {
                    $msg['msg'] = trans('content.procurement_fields.no_user_found');
                    return $msg;
                }
            }

            $items = $request->items;
            if(! is_array($items) || ! count($items)) {
                $return['msg'] = trans('content.procurement_fields.Please_add_the_items');
                return response()->json($return);
            }

            $rules = [
                'bc_id' => 'required|integer|min:1',
                'qty' => 'required',
                'unit_id' => 'required',
                'item_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:2000'
            ];

            foreach($items as $item) {
                $validator = \Validator::make((array) $item, $rules, []);
                if($validator->fails()) {
                    $v = $validator->errors()->toArray();
                    $e = array_shift($v);
                    $return["msg"] = $e[0];
                    return response()->json($return);
                }
            }

            $obj = new ProcureRequest;
            $obj->fill($data);
            $obj->status_id = 1;
            $obj->deadline = $request->deadline ? CommonHelper::getDateAs($data["deadline"], "Y-m-d 00:00:00", "d/m/Y") : null;
            $obj->creator_id = $obj->updator_id = Auth::user()->id;

            /* if pab added, then get the required minimum approvals value */
            if( $data["pab_id"] ) {
                $pab = Pab::find($data["pab_id"]);
                $obj->required_minimum_approvals = $pab && $pab->shouldShowRequiredApprovals() ? $pab->required_minimum_approvals : 1;
                $obj->hierarchy_approval = $pab ? $pab->hierarchy_approval : null;
            }

            if($obj->save()) {
                $obj->fillRequestTag();
                foreach($items as $item) {
                    $pro_req_item = new RequestItem;
                    $pro_req_item->fill($item);
                    $pro_req_item->pr_id = $obj->id;
                    $pro_req_item->save();
                }
                $alertnotify = null;
                if(Settings::first()->alerts_enabled == 1){
                    $alertnotify = CommonHelper::getGlobalAlertEmail();
                }

                $user = User::find($obj->creator_id);

                $pab_member_emails = [];
                if($request->approval_required == 1) {
                    $pab_id = $request->pab_id;
                    $pab = Pab::find($pab_id);
                    if(!$pab || $pab->totMembers() < 1) {
                        $msg['msg'] = trans('content.procurement_fields.no_user_found');
                        return $msg;
                    }

                    $pab_members = [];
                    if($pab->hierarchy_approval == 1) {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                    } else {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                    }

                    foreach($pab_members as $k=>$member) {
                        $approvalRequest = new ApprovalRequest();
                        $approvalRequest->pr_id = $obj->id;
                        $approvalRequest->pab_id = $pab_id;
                        $approvalRequest->user_id = $member->user_id;
                        $user = User::find($approvalRequest->user_id);
                        // $pab_member_emails[] = $user->email;
                    }
                    $team_privilage = UserPrivilage::select('u.email')
                        ->leftJoin('users as u', 'procure_users_privileges.user_id', '=', 'u.id')
                        ->where('team_privilege', '=', 1)->get()->pluck('email')->toArray();

                    $pro_not_mem = array_unique(array_merge([$user->email], $team_privilage, $pab_member_emails));
                    $creators = array_filter($pro_not_mem);
                    try {
                        if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($creators)->cc($alertnotify)->queue(new CreatedUser($obj,$user));
                        } else {
                            Mail::to($creators)->queue(new CreatedUser($obj,$user));
                        }
                    } catch (\Exception $ex) {
                        Log::error($ex->getMessage());
                    }
                }

                /** send push notification */
                $notify_people = [];
                $pab_id = $obj->pab_id;
                $pab = Pab::find($pab_id);
                if((!$pab || $pab->totMembers() < 1) && $data["approval_required"] == 1) {
                    $msg['msg'] = trans('content.procurement_fields.no_user_found_send');
                    return $msg;
                }
                $pab_members = [];
                if(!empty($pab->hierarchy_approval)) {
                    if($pab->hierarchy_approval == 1) {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                    } else {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                    }
                }
                foreach($pab_members as  $k=>$member) {
                    $u = User::find($member->user_id);
                    array_push($notify_people, $u->id);
                }
                $updateUserName = $user->first_name . " " . $user->last_name;
                $notificationText = "<p>New Procure tag id <b>" . $obj->procure_tag . "</b> is being created by <b>" . $updateUserName ."</b></p>";
                $bodyMsg = "<p>New Procurement Request has been created. Please check on portal or app.</p>";
                $data = [
                    'title' => $notificationText,
                    'data' => $obj,
                    'bodyMsg' => $bodyMsg,
                    'notify' => $notify_people,
                ];
                $sendNotifications = CommonHelper::sendPushNotification($data);
                if ($sendNotifications != false) {
                    $response = json_decode($sendNotifications);
                    if (isset($response->failure) && $response->failure == 1) {
                        Log::error("New Procure id:" . $obj->id . " notification error " . json_encode($response));
                    }
                }

                $return["msg"] = trans('content.procurement_fields.new_procurement', ['procure_tag' =>  $obj->procure_tag]);
                $return["status"] = "success";

                $history = new History();
                $history->user_id = 1;
                $history->pr_id = $obj->id;
                $history->save();
                HistoryEntry::create(['history_id'=>$history->id, 'change_info'=>$return['msg']]);
            }
            DB::commit();
            Log::info("ajaxAdd procurement:" . $obj->id. " user_id:" . 1 . " : " . json_encode($request->all()));
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("ajaxAdd: ". $e->getMessage());
        }

        return response()->json($return);
    }

    public function refereshTimeLine($reqId) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch timeline',
            ];
            $request_id = $reqId;
            if (!$request_id) {
                $return = [
                    "msg" => "No id found.",
                    "status" => "success"
                ];
                return response()->json($return);
            }
            $attachment_path = Attachment::getUrl('');
            $attachment_view = Attachment::getAppViewUrl();

            $db = DB::table('tkt_procurement_following as pf');
            $db->leftJoin('users as u', 'pf.updated_by', '=', 'u.id');
            $db->where('pf.procurement_id', '=', $request_id);
            $db->whereNotNull('pf.remarks');

            $db->select('pf.*');
            $db->addSelect(DB::raw('IFNULL(concat(u.first_name, " ", u.last_name, " @ ", u.username), "System") as commenter'));
            $db->addSelect(DB::raw('DATE_FORMAT(pf.updated_at, "%d %b %y %h:%i %p") as updated_at_format'));
            $db->orderBy('pf.updated_at', 'asc');

            $tls = $db->get();

            if ($tls && count($tls)) {

                $embedded_attachments = Attachment::getEmbeddedAttachments($request_id);

                foreach ($tls as $tl) {
                    $updateingUser = User::find($tl->updated_by);
                    $tl->profile_img = $updateingUser->getProfileImg();
                    $tl->remarks = CommonHelper::renderTktContent($tl->remarks, $embedded_attachments);
                    $tl->attachments = Attachment::where('following_id', '=', $tl->id)->select('id', 'original_file_name as name', 'extension as ext', DB::raw('case when thumbnail is not null then 1 else 0 end as thumb'),DB::raw('case when id is not null then concat_ws("", "'.$attachment_path.'/",id) else "" end as attach_file_path'),DB::raw('case when id is not null then concat_ws("", "'.$attachment_view.'/",id) else "" end as attach_view'))->get();
                }
            }

            $return['data'] = $tls;
            $return['status'] = "success";
            $return['msg'] = "Timeline data fetched successfully";
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error("Procurement list controller refereshTimeLine: ". $e->getMessage());
            return $return;
        }
    }

    public function addComment(Request $request) {
        try {
            $return = [
                'status' => 'failure',
                'msg' => trans('content.procurement_fields.Unable_to_add_the_given_request')
            ];
            DB::beginTransaction();
            $data = $request->only('comments_user');
            
            $rules = [
                'comments_user' => 'required|string|max:2000',
            ];
            $messages = [
                'comments_user.required' => 'Please provide procurement comments.',
            ];
            $validator = \Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $request_id = $request->id;
            $get_request = ProcureRequest::findOrFail($request_id);
            $pro_request = $get_request;
            
            $Procurementfollowing = new ProcurementFollowing();
            $Procurementfollowing->procurement_id = $request_id;
            $Procurementfollowing->is_note = $request->is_note ? 1 : 0;
            $Procurementfollowing->updated_by = Auth::user()->id;
            $Procurementfollowing->remarks = $data["comments_user"];
            $Procurementfollowing->save();
            $ats = Attachment::where("procurement_id", "=", $request_id)->where("tmp_id", "like", $request->tmp_id)->get();
            if ($ats && count($ats)) {
                foreach ($ats as $at) {
                    $at->following_id = $Procurementfollowing->id;
                    $at->save();
                }
            }
    
            $pab = Pab::where("id", $pro_request->pab_id)->first();
            // notify members about user comment
            try {
                if (!empty($pab)) {
                    $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->pluck('user_id')->toArray();
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
                        // Mail::to($emails)->send(new UserComment($pro_request,$user,$request->comment, $Procurementfollowing));
                    }
                }
            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
            }
    
            $return["msg"] = "procurement has been commented successfully";
            $return["status"] = "success";
    
            DB::commit();
            Log::info("addComment procurement:" . $request_id . " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error("Procurement list controller addComment: ". $e->getMessage());
            return $return;
        }
    }

    public function procureApprovals($reqId) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch approvals'
            ];
            $approvalReq = ApprovalRequest::select('procure_approval_requests.*',DB::raw("concat(users.first_name,' ',users.last_name) as approver_name"))
            ->leftJoin('users','users.id','procure_approval_requests.user_id')
            ->where('procure_approval_requests.pr_id',$reqId)
            ->get();
            foreach($approvalReq as $ar) {
                $data = [];
                if($ar->isApproved()){
                    $data['supplier_name'] = isset($ar->approvedQuotation->supplier) ? $ar->approvedQuotation->supplier->name : "-";
                }
            }
            $return["data"] = $approvalReq;
            $return["status"] = "success";
            $return["msg"] = 'Approvals fetched successfully.';
            return $return;
        } catch(\Exception $e) {
            Log::error("ListController procureApprovals: ". $e->getMessage());
            return $return;
        }
    }

    public function updateProcure(Request $request){
        $return = [
            'status' => 'fail',
            'msg' => 'Unable to update procure request',
        ];

        try {
            $request_id = $request->request_id;
            $get_request = ProcureRequest::findOrFail($request_id);
        }
        catch(\Exception $e) {
            return $return;
        }

        $pro_request = $get_request;

        $data = $request->only("status_id", "po_invoice_num", "po_invoice_attachment");
        try {
            $rules = [
                'status_id' => 'required|integer|min:1|exists:procure_statuses,id'
            ];

            if($data["status_id"] == 8) {
                $rules['po_invoice_num'] = 'required|string|max:100';
                $rules['po_invoice_attachment'] = 'required|file';
            }

            $messages = [];
            $validator = \Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $alertnotify = null;
            if(Settings::first()->alerts_enabled == 1){
                $alertnotify = CommonHelper::getGlobalAlertEmail();
            }

            $Qutation_item = QuotationItem::where('pr_id',$pro_request->id)->count();
            if($data["status_id"] == $pro_request->status_id) {
                $return["msg"] = trans('content.procurement_fields.status_exist');
                return response()->json($return);
            }
            if ($pro_request->vendor_id == "null" || $pro_request->vendor_id == null) {
                $return["msg"] = trans('content.procurement_fields.select_suppliers');
                return response()->json($return);
            }
            if($Qutation_item == 0 && ((isset($data["status_id"]) && $data["status_id"] == 3 && $pro_request->approval_required == 1) || (isset($data["status_id"]) && $data["status_id"] == 6 && $pro_request->approval_required == null))) {
                $return["msg"] = trans('content.procurement_fields.minimum_quotation');
                return response()->json($return);
            }

            if( $data["status_id"] == 3 ) {
                try {
                    if($pro_request->status_id != 3 && $pro_request->approval_required == 1) {
                        $quotationCount = Quotation::where('pr_id', $pro_request->id)->count();
                        if($quotationCount < 1) {
                            $return['msg'] = trans('content.procurement_fields.atleast_one_quotations');
                            return response()->json($return);
                        }
                    }

                    $pab = $pro_request->pab;
                    if($pab->totMembers() < 1) {
                        $return["msg"] = trans('content.procurement_fields.Unable_to_find_the_PAB_Member_for_approval');
                        return response()->json($return);
                    }
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                    $return['msg'] = trans('content.procurement_fields.Unable_to_find_active_PAB_Team_members');
                    return response()->json($return);
                }
            }

            $history_log = "";
            $quotate_user_email = "";
            if($request->status_id == 8) {
                if(! $request->hasFile('po_invoice_attachment') || !$request->file('po_invoice_attachment')->isValid()) {
                    return response()->json($return);
                }

                $get_quotate = Quotation::where([
                    "pr_id" => $request_id,
                    "po_approved_quotate" => 1
                ])->get();
                $quotate_user = "";
                $quotate = $get_quotate[0];
                $quotate->po_invoice_attachment = $request->file('po_invoice_attachment')->getClientOriginalName();
                $quotate->po_invoice_attachment_path = $request->file('po_invoice_attachment')->store("", "procurements");
                $quotate->po_invoice_num = $request->po_invoice_num;
                $quotate->save();
                $quotate_user = User::find($quotate->po_created_by);
                $quotate_user_email = $quotate_user->email;
                $pro_request->closed_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                $history_log = "Request has been closed with Invoice Number: " . $request->po_invoice_num;
            }

            // elseif($request->status_id == 6) {

            // }

            $pro_request->status_id = $data["status_id"];
            $pro_request->updator_id = Auth::user()->id;
            if($pro_request->save()) {

                $created_user = User::find($pro_request->creator_id);
                $creators = [];
                $pab_member_emails = [];

                $pab_members = [];
                $pab_user = [];
                $pab_username = [];
                if($pro_request->approval_required == 1) {
                    $pab_id = $pro_request->pab_id;
                    $pab = Pab::find($pab_id);
                    if(!$pab || $pab->totMembers() < 1) {
                        $msg['msg'] = trans('content.procurement_fields.no_user_found');
                        return $msg;
                    }


                    if($pab->hierarchy_approval == 1) {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                    }
                    else {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                    }

                    foreach($pab_members as $k=>$member) {
                        $approval_user_id = $member->user_id;
                        $user = User::find($approval_user_id);
                        $pab_u = $member->user_id;
                        $pab_user[] = User::find($pab_u);
                        $pab_member_emails[] = $user->email;
                    }
                }

                $pro_not_mem = array_unique(array_merge([$created_user->email],[$quotate_user_email], $pab_member_emails));
                $creators = array_filter($pro_not_mem);
                if($pro_request->status_id == 3) {
                    $this->_sendApprovalRequest($request, $pro_request);
                    $pab_username = $pab_user[0];
                    try {
                        if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($creators)->cc($alertnotify)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                        } else {
                            Mail::to($creators)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                        }
                    } catch (\Exception $ex) {
                        Log::error($ex->getMessage());
                    }
                }

                try {
                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($creators)->cc($alertnotify)->queue(new StatusChange($pro_request, $user));
                    } else {
                        Mail::to($creators)->queue(new StatusChange($pro_request, $user));
                    }
                } catch (\Exception $ex) {
                    Log::error($ex->getMessage());
                }

                $return["status"] = "success";
                $return["msg"] = trans('content.procurement_fields.Request_Status_has_been_changed_successfully');

                if($history_log == "") {
                    $history_log = "Request Status has been changed to \"" . $pro_request->status->name . "\"";
                }

                $history = new History();
                $history->user_id = Auth::user()->id;
                $history->pr_id = $pro_request->id;
                $history->action_msg = 2; //updated status
                $history->save();
                HistoryEntry::create(['history_id'=>$history->id, 'change_info'=>$history_log]);
            }
            Log::info("ajaxUpdate procurement id:" . $pro_request->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            DB::commit();
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("ajaxUpdate procure req: ". $e->getMessage());
            return response()->json($return);
        }
    }

    private function _sendApprovalRequest(Request $request, &$pro_request) {
        $msg = ['status' => 'failure', 'msg' => ''];
        $pab_id = $pro_request->pab_id;
        $pab = Pab::find($pab_id);
        $notify_people = [];
        $created_user = User::find($pro_request->creator_id);
        if($pab->hierarchy_approval != 4) {

            if($pab->hierarchy_approval == 4) {
                $manager_id = $created_user->manager_id;
                if(!isset($manager_id) || $manager_id == null) {
                    $msg['msg'] = trans('content.procurement_fields.no_user_found');
                    return $msg;
                }
            } elseif($pab->hierarchy_approval == 9) {
                $budgets = BudgetApprovalMember::where('pab_id',$pab_id)->get();
                $budget_pab_id = '';
                $budget_user_id = '';
                $budgetNotAllowed = false;
                if(count($budgets) > 0 && $request->quotate_total > 0) {
                    foreach($budgets as $budget) {
                        if(in_array($request->quotate_total,range($budget->budget_from,$budget->budget_to))){
                            $budget_pab_id = $budget->pab_id;
                            $budget_user_id = $budget->user_id;
                            $budgetNotAllowed = true;
                            continue;
                        }
                    }
                    if(!$budgetNotAllowed) {
                        $msg['msg'] = trans('content.procurement_fields.budget_not_allowed');
                        return $msg;
                    }
                    $members = PabMember::where('pab_id', '=', $pab->id)->where('pab_id', $budget_pab_id)->where('user_id',$budget_user_id)->orderBy('id', 'asc')->count();
                    if($members == 0) {
                        $msg['msg'] = trans('content.procurement_fields.no_user_found');
                        return $msg;
                    }
                }
            } else if (!$pab || $pab->totMembers() < 1) {
                $msg['msg'] = trans('content.procurement_fields.no_user_found');
                return $msg;
            }

            $pab_members = [];
            $pabMember =PabMember::where('pab_id', '=', $pab->id);
            if($pab->hierarchy_approval == 1) {
                $pab_members = $pabMember->orderBy('hierarchy_level', 'asc')->get();
            }else if($pab->hierarchy_approval == 5) {
                if($pabMember->first()->location_id != "") {
                    if(isset($created_user->location_id) && $created_user->location_id != null){
                        $pab_members = $pabMember->where('location_id', $created_user->location_id)->orderBy('id', 'asc')->get();
                    }
                }
                if(empty($pab_members) && count($pab_members) == 0) {
                    $msg['msg'] = trans('content.procurement_fields.no_user_found');
                    return $msg;
                }
            } else if($pab->hierarchy_approval == 4 ) {
                $pab_members = User::select('id as user_id','email')->where('id',$created_user->manager_id)->get();
            } else if($pab->hierarchy_approval == 9) {
                $budgets = BudgetApprovalMember::where('pab_id',$pab_id)->get();
                $budget_pab_id = '';
                $budget_user_id = '';
                $budgetNotAllowed = false;
                if(count($budgets) > 0 && $request->quotate_total > 0) {
                    foreach($budgets as $budget) {
                        if(in_array($request->quotate_total,range($budget->budget_from,$budget->budget_to))){
                            $budget_pab_id = $budget->pab_id;
                            $budget_user_id = $budget->user_id;
                            $budgetNotAllowed = true;
                            continue;
                        }
                    }
                    if(!$budgetNotAllowed) {
                        $msg['msg'] = trans('content.procurement_fields.budget_not_allowed');
                        return $msg;
                    }
                    $pab_members = $pabMember->where('pab_id', $budget_pab_id)->where('user_id',$budget_user_id)->orderBy('id', 'asc')->get();
                    if(count($pab_members) == 0) {
                        $msg['msg'] = trans('content.procurement_fields.no_user_found');
                        return $msg;
                    }
                }
            } else {
                $pab_members = $pabMember->orderBy('id', 'asc')->get();
            }

            foreach($pab_members as $k=>$member) {
                $approvalRequest = new ApprovalRequest();
                $approvalRequest->pr_id = $pro_request->id;
                $approvalRequest->pab_id = $pab_id;
                $approvalRequest->user_id = $member->user_id;
                if($pab->hierarchy_approval == 1) {
                    $approvalRequest->hierarchy_level = $member->hierarchy_level;
                    $approvalRequest->hierarchy_approval = $pab->hierarchy_approval;

                    // send approve request start from first level
                    if($k == 0) {
                        $approvalRequest->approve_status = 3;
                    }
                    else {
                        $approvalRequest->approve_status = 4;
                    }
                }
                else {
                    $approvalRequest->approve_status = 3; // symultanously send request all member
                }
                array_push($notify_people, $member->user_id);
                $approvalRequest->save();
            }
        } else {
            $usr = User::find($pro_request->creator_id);

            $approvalRequest = new ApprovalRequest();
            $approvalRequest->pr_id = $pro_request->id;
            $approvalRequest->pab_id = $pab_id;
            $approvalRequest->user_id = $usr->manager->id;
            $approvalRequest->approve_status = 3;
            $approvalRequest->save();
        }

        $user = User::find($pro_request->creator_id);
        $alertnotify = null;
        if(Settings::first()->alerts_enabled == 1){
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            try {
                if(config('mail.service_enabled') &&  filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($user->email)->cc($alertnotify)->queue(new StatusChange($pro_request, $user));
                }
            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
            }
        }
        // send push notification
        $notificationText = '###' . $pro_request->procure_tag . '### Status Changed to ' . $pro_request->status->name;
        $data = [
            'title' => $notificationText,
            'data' =>  $pro_request,
            'notify' => $notify_people,
        ];
        $sendNotifications = CommonHelper::sendPushNotification($data);
        $sendNotifications = CommonHelper::sendWhatsappNotification($data);

        $msg['msg'] = trans('content.procurement_fields.Request(s)_has_been_send_successfully');
        $msg['status'] = 'success';

        $history = new History();
        $history->user_id = Auth::user()->id;
        $history->pr_id = $pro_request->id;
        $history->action_msg = 8; //send user approval request
        $history->save();
        HistoryEntry::create(['history_id'=>$history->id, 'change_info'=>$msg['msg']]);
        return $msg;
    }

    public function procureQuotationDetails(Request $request) {
        try {
            $return = [
                "msg" => "Unable to fetch procurement quotation details",
                "status" => "failure"
            ];
            DB::beginTransaction();

            $request_id = ($request->request_id);
            $get_request = ProcureRequest::findOrFail($request_id);

            $pro_request = $get_request;

            $quotationUrl = Quotation::getUrl();
            $quotationViewUrl = Quotation::getAppViewUrl();

            $get_quotation = Quotation::select('procure_quotations.*','suppliers.name as supplier_name', DB::raw("case when procure_quotations.id is not null then concat_ws('', '".$quotationViewUrl."/',procure_quotations.id,'/quotation') else '' end as quotation_file_path"))
                ->leftjoin('suppliers','suppliers.id','supplier_id')
                ->where('pr_id', '=', $request_id)
                ->where('quotation_position', '=', $request->quote_num)
                ->first();
            if(!empty($get_quotation)){
                $get_quotation->quotationItems = QuotationItem::where([
                    'pr_id' => $request_id,
                    'qid' => $get_quotation->id
                ])->get();
                foreach($get_quotation->quotationItems as $item){
                    $item->item_details = RequestItem::find($item->item_id);
                }
            }

            $procurementRequestItems = RequestItem::select('procure_request_items.*','procure_units.name as unit_name')->leftJoin('procure_units','procure_units.id','procure_request_items.unit_id')->where('pr_id',$request_id)->get();
            $return['data'] = [
                'procurement_request_items' => $procurementRequestItems,
                'quotation_details' => $get_quotation
            ];
            $return['status'] = 'success';
            $return['msg'] = "Quotation details fetched successfully";
            return response()->json($return);
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error("procureQuotationDetails quotation: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function updateProcureQuotation(Request $request) {
        $return = [
            "msg" =>trans('content.procurement_fields.Unable_to_update_the_quotation'),
            "status" => "failure"
        ];
        DB::beginTransaction();
        try {
            $request_id = ($request->request_id);
            $get_request = ProcureRequest::findOrFail($request_id);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }

        $pro_request = $get_request;

        if(! in_array($pro_request->status_id, [1,2,5])) {
            $return["msg"] = trans('content.procurement_fields.request_is_not_right');
            return response()->json($return);
        }

        $quotate_date = CommonHelper::getDateAs($request->quotate_date, "Y-m-d 00:00:00", "d/m/Y");

        if($request->hasFile('quotation_file') && !$request->file('quotation_file')->isValid()) {
            return response()->json($return);
        }

        $check_for_dup = Quotation::where('pr_id', '=', $request_id)->where('quotation_position', '!=', $request->quote_num)->where('supplier_id', '=', $request->supplier_id)->count();
        if($check_for_dup) {
            $return["msg"] = trans('content.procurement_fields.given_supplier_already_added');
            return response()->json($return);
        }

        try {
            $history_log = "";
            $quotation_file = null;
            $quotation_file_attachment_path = null;
            if($request->hasFile('quotation_file')) {
                $quotation_file = $request->file('quotation_file')->getClientOriginalName();
                $quotation_file_attachment_path = $request->file('quotation_file')->store("", "procurements");
                $history_log = "Quotation " . $request->quote_num . " has been updated with Quotation File";
            }

            $quote = Quotation::updateOrCreate([
                "pr_id" => $request_id,
                "quotation_position" => $request->quote_num
            ], [
                "supplier_id" => $request->supplier_id,
                "quotate_date" => $quotate_date,
                "quotation_notes" => trim($request->quotation_notes),
                "quotation_file_attachment_path" => $quotation_file_attachment_path,
                "quotation_file" => $quotation_file
            ]);

            if(! $quote) {
                return response()->json($return);
            }

            $ppus = $request->ppu;
            foreach($ppus as $key=>$val) {
                $get_item_id = str_replace("ppu_", "", $key);

                if(! $get_item_id) {
                    continue;
                }

                QuotationItem::updateOrCreate([
                    "pr_id" => $request_id,
                    "qid" => $quote->id,
                    "item_id" => $get_item_id
                ], [
                    "price_per_item" => $val
                ]);
            }

            $return["status"] = "success";
            $return["msg"] = trans('content.procurement_fields.Quotation_has_been_updated_succesfully');

            if(! $history_log) {
                $history_log = "Quotation " . $request->quote_num . " has updated successfully";
            }

            $history = new History();
            $history->user_id = Auth::user()->id;
            $history->pr_id = $request_id;
            $history->save();
            HistoryEntry::create(['history_id'=>$history->id, 'change_info'=>$history_log]);
            Log::info("ajaxUpdate procurement quotation id:" . $get_request->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            DB::commit();
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("ajaxUpdate procurement quotation: ". $e->getMessage());
        }
        return response()->json($return);
    }

    public function removeProcureQuotation(Request $request) {
        try {
            $return = [
                "msg" => trans('content.procurement_fields.unable_to_remove'),
                "status" => "failure"
            ];
            DB::beginTransaction();
            try {
                $request_id = ($request->request_id);
                $get_request = ProcureRequest::findOrFail($request_id);
            }
            catch(\Exception $e) {
                return response()->json($return);
            }
    
            $pro_request = $get_request;
    
            try {
                $get_quotation = Quotation::where('pr_id', '=', $request_id)->where('quotation_position', '=', $request->quote_num)->get();
                if(empty($get_quotation)) {
                    return response()->json($return);
                }
                $quotation = isset($get_quotation[0]) ? $get_quotation[0] : '';
                $quotation_id = $quotation->id;
                $quotation->delete();
                QuotationItem::where('qid', '=', $quotation_id)->delete();
                Log::info("removeProcureQuotation quotation id:" . $quotation->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            }
            catch(\Exception $e) {
                Log::error("removeProcureQuotation quotation: ". $e->getMessage());
                return response()->json($return);
            }
            DB::commit();
            $return['status'] = 'success';
            $return['msg'] = trans('content.procurement_fields.quotation_has_been_removed');
            return response()->json($return);
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error("removeProcureQuotation quotation: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function getBudgetCatgories(Request $request, $deptId) {
        try {
            $return = [
                'status' => "fail",
                'msg' => 'Unable to fetch budget categories.'
            ];
            $budgets = Budget::select('id', 'category as text')->where("department_id", "=", $deptId)->get();
            $return = [
                'status' => "success",
                'msg' => 'Budget categories fetched successfully.',
                'data' => $budgets
            ];
            return $return;
        } catch(\Exception $e) {
            Log::error("Listcontroller : getBudgetCatgories()".$e->getMessage());
            return $return;
        }
    }

    public function updatedProcureApprovalRequest(Request $request) {
        $return = [
            "msg" => trans('content.procurement_fields.Unable_to_update_the_approval_status'),
            "status" => "failure"
        ];

        try {
            $request_id = $request->request_id;
            $get_request = ProcureRequest::findOrFail($request_id);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }

        $pro_request = $get_request;

        $data = $request->only('approve_status', 'comments', 'approve_request_id', 'approved_qnum');
        try {
            $rules = [
                'approve_request_id' => 'required|integer|exists:procure_approval_requests,id',
                'approve_status' => 'required|integer',
                'comments' => 'required|string|max:2000',
                'approved_qnum' => 'nullable|integer|required_if:approve_status,==,1'
            ];
            $msgs = [];

            $validator = \Validator::make($data, $rules, $msgs);
            if($validator->fails()) {
                $return["msg"] = trans('content.procurement_fields.please_give_the_values_correctly');
                return response()->json($return);
            }

            $approvalRequest = ApprovalRequest::find($data['approve_request_id']);
            $approvalRequest->approve_status = $data["approve_status"];
            $approvalRequest->comments = trim($data["comments"]);
            $approvalRequest->approved_qnum = $data["approve_status"] == 1 ? trim($data["approved_qnum"]) : null;

            if($data["approve_status"] == 1) {
                $get_items = DB::select('SELECT qi.*, ri.qty, ri.bc_id FROM `procure_quotation_items` as qi join procure_quotations as q on qi.qid = q.id join procure_request_items as ri on qi.item_id = ri.id where q.po_approved_quotate = 1 and qi.pr_id = "' . $request_id . '"');

                /* collect utilized/remaining budget */
                $budget_collections = DB::select("select pb.id, pb.category, case when utilized.tot_utilized is null then 0 else utilized.tot_utilized end as utilized_amount, pb.amount from procure_budgets as pb left join (SELECT ri.bc_id, SUM(qi.price_per_item * ri.qty) AS tot_utilized FROM procure_quotation_items AS qi JOIN procure_quotations AS q ON qi.qid = q.id JOIN procure_request_items AS ri ON qi.item_id = ri.id JOIN procure_requests AS r ON qi.pr_id = r.id JOIN procure_budgets AS b ON ri.bc_id = b.id WHERE q.po_approved_quotate = 1 AND r.status_id IN (4, 6, 7, 8) AND qi.pr_id != " . $request_id . " GROUP BY ri.bc_id ) as utilized on utilized.bc_id = pb.id");

                if(! count($budget_collections)) {
                    $return["msg"] = trans('content.procurement_fields.budgets_are_not_found');
                    return response()->json($return);
                }

                $aligned_budgets = [];
                $total_item_amount = 0;

                foreach($get_items as $item) {
                    $amount = round($item->qty * $item->price_per_item, 2);

                    if(isset($aligned_budgets[$item->bc_id])) {
                        $aligned_budgets[$item->bc_id] += $amount;
                    }
                    else {
                        $aligned_budgets[$item->bc_id] = $amount;
                    }
                    $total_item_amount += $amount;
                };

                $insufficient_budget_categories = [];
                foreach($budget_collections as $collection) {
                    if(isset($aligned_budgets[$collection->id])) {
                        $remaining_budget = $collection->amount - $collection->utilized_amount;
                        if($remaining_budget < $aligned_budgets[$collection->id]) {
                            $insufficient_budget_categories[] = $collection->category;
                        }
                    }
                }

                if(count($insufficient_budget_categories)) {
                    $return["msg"] = trans('content.procurement_fields.budget_are_in') . implode(", ", $insufficient_budget_categories) . trans('content.procurement_fields.category');
                    return response()->json($return);
                }
            }

            $alertnotify = null;
            if(Settings::first()->alerts_enabled == 1){
                $alertnotify = CommonHelper::getGlobalAlertEmail();
            }
            $user = User::find($pro_request->creator_id);
            $creators = [];
            $notify_people = [];
            $pab_member_emails = [];
            if($approvalRequest->save()) {
                $pab_ids = json_decode($pro_request->pab_id, true);
                $pab = Pab::where("id", $pab_ids)->first();
                /* level by level approval */
                if(!empty($pro_request) && $pro_request->hierarchy_approval == 1) {
                    if($approvalRequest->approve_status == 1) {
                        $getNextLevel = ApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->where('hierarchy_level', '>', $approvalRequest->hierarchy_level)->orderBy('hierarchy_level', 'asc')->get();
                        if(count($getNextLevel)) {
                            /* here, send approve request mail to next level people */
                            $getNextLevel[0]->approve_status = 3;
                            $getNextLevel[0]->save();
                            $pab_username = User::find($getNextLevel[0]->user_id);
                            $pab_username_u[] = User::find($getNextLevel[0]->user_id);
                            $pab_member_emails[] = $pab_username_u[0]->email;

                            $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                            $creators = array_filter($pro_not_mem);
                            try {
                                if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($creators)->cc($alertnotify)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                } else {
                                    Mail::to($creators)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }

                            array_push($notify_people, $user->id);
                            $notificationText = "###".$pro_request->id."### (Awaiting your approval)";
                            $data = [
                                'title' => $notificationText,
                                'data' =>  $pro_request,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                        } else {
                            $pro_request->status_id = 4;
                            $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                            if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($user->email)->cc($alertnotify)->queue(new RequestApproved($pro_request, $user));
                                    } else {
                                        Mail::to($user->email)->queue(new RequestApproved($pro_request, $user));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }
                            array_push($notify_people, $user->id);
                            $notificationText = '###' . $pro_request->procure_tag . '### Request Approved';
                            $data = [
                                'title' => $notificationText,
                                'data' =>  $pro_request,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                        }
                    } else {
                        $pro_request->status_id = 5;
                        if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($user->email)->cc($alertnotify)->queue(new RequestRejected($pro_request, $user));
                                } else {
                                    Mail::to($user->email)->queue(new RequestRejected($pro_request, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                        $pro_request->approved_at = null;
                        array_push($notify_people, $user->id);
                        $notificationText = '###' . $pro_request->procure_tag . '### Request Rejected';
                        $data = [
                            'title' => $notificationText,
                            'data' =>  $pro_request,
                            'notify' => $notify_people,
                        ];
                        $sendNotifications = CommonHelper::sendPushNotification($data);
                        $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                    }
                }
                /* minimum approval */
                elseif(!empty($pro_request) && $pro_request->hierarchy_approval == 2 ) {
                    if( $approvalRequest->approve_status == 1 ) {
                        if( $pro_request->required_minimum_approvals > 1 ) {
                            $all_app_reqs = ApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->orderBy('id', 'asc')->get();

                            $approves_count = 0;
                            foreach($all_app_reqs as $req) {
                                if($req->approve_status == 1) {
                                    $approves_count++;
                                    $pab_username = User::find($all_app_reqs[0]->user_id);
                                    $pab_username_u[] = User::find($all_app_reqs[0]->user_id);
                                    $pab_member_emails[] = $pab_username_u[0]->email;

                                    $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                    $creators = array_filter($pro_not_mem);
                                    try {
                                        if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                            Mail::to($creators)->cc($alertnotify)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                        } else {
                                            Mail::to($creators)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }
                            }

                            if( $pro_request->required_minimum_approvals <= $approves_count ) {
                                $preApprovalReq = ApprovalRequest::select('approved_qnum')->where('pr_id', '=', $approvalRequest->pr_id)->where('approve_status',1)->whereNotIn('id',[$approvalRequest->id])->get()->toArray();
                                $approvedQnums = array_column($preApprovalReq, 'approved_qnum');
                                $valueCounts = array_count_values($approvedQnums);
                                if(!empty($valueCounts)) {
                                    $maxCount = max($valueCounts);
                                    $minCount = min($valueCounts);
                                    $max_keys = array_keys($valueCounts, $maxCount);
                                    if(!in_array($request->approved_qnum, $max_keys)) {
                                        $preApproval = ApprovalRequest::find($approvalRequest->id);
                                        $preApproval->approved_qnum = null;
                                        $preApproval->comments = null;
                                        $preApproval->approve_status = 3;
                                        $preApproval->save();
                                        $return['msg'] = 'Approval selected supplier different so select other supplier' ;
                                        $return["status"] = "failure";
                                        return response()->json($return);
                                    }
                                }
                                $pro_request->status_id = 4;
                                $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                                $user = User::find($pro_request->creator_id);
                                if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    try {
                                        if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                            Mail::to($user->email)->cc($alertnotify)->queue(new RequestApproved($pro_request, $user));
                                        } else {
                                            Mail::to($user->email)->queue(new RequestApproved($pro_request, $user));
                                        }
                                    } catch (\Exception $ex) {
                                        Log::error($ex->getMessage());
                                    }
                                }
                                array_push($notify_people, $user->id);
                                $notificationText = '###' . $pro_request->procure_tag . '### Request Approved';
                                $data = [
                                    'title' => $notificationText,
                                    'data' =>  $pro_request,
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($data);
                                $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                            }
                        }
                        else {
                            $pro_request->status_id = 4;
                            $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                            $user = User::find($pro_request->creator_id);
                            if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($user->email)->cc($alertnotify)->queue(new RequestApproved($pro_request, $user));
                                    } else {
                                        Mail::to($user->email)->queue(new RequestApproved($pro_request, $user));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }
                            array_push($notify_people, $user->id);
                            $notificationText = '###' . $pro_request->procure_tag . '### Request Approved';
                            $data = [
                                'title' => $notificationText,
                                'data' =>  $pro_request,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                        }
                    }
                    else {
                        $pro_request->status_id = 5;
                        $pro_request->approved_at = null;
                        if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($user->email)->cc($alertnotify)->queue(new RequestRejected($pro_request, $user));
                                } else {
                                    Mail::to($user->email)->queue(new RequestRejected($pro_request, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                        array_push($notify_people, $user->id);
                        $notificationText = '###' . $pro_request->procure_tag . '### Request Rejected';
                        $data = [
                            'title' => $notificationText,
                            'data' =>  $pro_request,
                            'notify' => $notify_people,
                        ];
                        $sendNotifications = CommonHelper::sendPushNotification($data);
                        $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                    }
                }
                /* group approval - means all have to approve */
                elseif(!empty($pro_request) && $pro_request->hierarchy_approval == 3  ) {
                    if( $approvalRequest->approve_status == 1 ) {
                        $all_app_reqs = ApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->get();

                        $tot_approval_reqs = count($all_app_reqs);
                        $approves_count = 0;
                        foreach($all_app_reqs as $req) {
                            if($req->approve_status == 1) {
                                $approves_count++;
                                $pab_username = User::find($all_app_reqs[0]->user_id);
                                $pab_username_u[] = User::find($all_app_reqs[0]->user_id);
                                $pab_member_emails[] = $pab_username_u[0]->email;

                                $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                $creators = array_filter($pro_not_mem);
                                try {
                                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($creators)->cc($alertnotify)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                    } else {
                                        Mail::to($creators)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                                array_push($notify_people, $user->id);
                                $notificationText = "###".$pro_request->id."### (Awaiting your approval)";
                                $data = [
                                    'title' => $notificationText,
                                    'data' =>  $pro_request,
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($data);
                                $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                            }
                        }

                        if( $tot_approval_reqs == $approves_count ) {
                            $preApprovalReq = ApprovalRequest::select('approved_qnum')->where('pr_id', '=', $approvalRequest->pr_id)->where('approve_status',1)->whereNotIn('id',[$approvalRequest->id])->get()->toArray();
                            $approvedQnums = array_column($preApprovalReq, 'approved_qnum');
                            $valueCounts = array_count_values($approvedQnums);
                            if(!empty($valueCounts)) {
                                $maxCount = max($valueCounts);
                                $minCount = min($valueCounts);
                                $max_keys = array_keys($valueCounts, $maxCount);
                                if(!in_array($request->approved_qnum, $max_keys)) {
                                    $preApproval = ApprovalRequest::find($approvalRequest->id);
                                    $preApproval->approved_qnum = null;
                                    $preApproval->comments = null;
                                    $preApproval->approve_status = 3;
                                    $preApproval->save();
                                    $return['msg'] = 'Approval selected supplier different so select other supplier' ;
                                    $return["status"] = "failure";
                                    return response()->json($return);
                                }
                            }
                            $pro_request->status_id = 4;
                            $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                            $user = User::find($pro_request->creator_id);
                            if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($user->email)->cc($alertnotify)->queue(new RequestApproved($pro_request, $user));
                                    } else {
                                        Mail::to($user->email)->queue(new RequestApproved($pro_request, $user));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }
                            array_push($notify_people, $user->id);
                            $notificationText = "###".$pro_request->procure_tag."### Request Approved";
                            $data = [
                                'title' => $notificationText,
                                'data' =>  $pro_request,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                        }
                    }
                    else {
                        $pro_request->status_id = 5;
                        $pro_request->approved_at = null;
                        if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($user->email)->cc($alertnotify)->queue(new RequestRejected($pro_request, $user));
                                } else {
                                    Mail::to($user->email)->queue(new RequestRejected($pro_request, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                        array_push($notify_people, $user->id);
                        $notificationText = '###' . $pro_request->procure_tag . '### Request Rejected';
                        $data = [
                            'title' => $notificationText,
                            'data' =>  $pro_request,
                            'notify' => $notify_people,
                        ];
                        $sendNotifications = CommonHelper::sendPushNotification($data);
                        $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                    }
                }
                /* Manager approval - means Manager  approval required */
                elseif (!empty($pro_request) && $pro_request->hierarchy_approval == 4) {
                    if( $approvalRequest->approve_status == 1 ) {

                        $all_app_reqs = ApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->get();

                        $tot_approval_reqs = count($all_app_reqs);
                        $approves_count = 0;
                        foreach($all_app_reqs as $req) {
                            if($req->approve_status == 1) {
                                $approves_count++;
                                $pab_username = User::find($all_app_reqs[0]->user_id);
                                $pab_username_u[] = User::find($all_app_reqs[0]->user_id);
                                $pab_member_emails[] = $pab_username_u[0]->email;

                                $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                $creators = array_filter($pro_not_mem);
                                try {
                                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($creators)->cc($alertnotify)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                    } else {
                                        Mail::to($creators)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                                array_push($notify_people, $user->id);

                                $notificationText = "###".$pro_request->id."### (Awaiting your approval)";
                                $data = [
                                    'title' => $notificationText,
                                    'data' =>  $pro_request,
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($data);
                                $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                            }
                        }

                        if( $tot_approval_reqs == $approves_count ) {
                            $preApprovalReq = ApprovalRequest::select('approved_qnum')->where('pr_id', '=', $approvalRequest->pr_id)->where('approve_status',1)->whereNotIn('id',[$approvalRequest->id])->get()->toArray();
                            $approvedQnums = array_column($preApprovalReq, 'approved_qnum');
                            $valueCounts = array_count_values($approvedQnums);
                            if(!empty($valueCounts)) {
                                $maxCount = max($valueCounts);
                                $minCount = min($valueCounts);
                                $max_keys = array_keys($valueCounts, $maxCount);
                                if(!in_array($request->approved_qnum, $max_keys)) {
                                    $preApproval = ApprovalRequest::find($approvalRequest->id);
                                    $preApproval->approved_qnum = null;
                                    $preApproval->comments = null;
                                    $preApproval->approve_status = 3;
                                    $preApproval->save();
                                    $return['msg'] = trans('content.procurement_fields.select_other_supplier');
                                    $return["status"] = "failure";
                                    return response()->json($return);
                                }
                            }

                            $pro_request->status_id = 4;
                            $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                            $user = User::find($pro_request->creator_id);
                            if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($user->email)->cc($alertnotify)->queue(new RequestApproved($pro_request, $user));
                                    } else {
                                        Mail::to($user->email)->queue(new RequestApproved($pro_request, $user));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }
                            array_push($notify_people, $user->id);
                            $notificationText = "###".$pro_request->procure_tag."### Request Approved";
                            $data = [
                                'title' => $notificationText,
                                'data' =>  $pro_request,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                        }
                    }
                    else {
                        $pro_request->status_id = 5;
                        $pro_request->approved_at = null;
                        if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($user->email)->cc($alertnotify)->queue(new RequestRejected($pro_request, $user));
                                } else {
                                    Mail::to($user->email)->queue(new RequestRejected($pro_request, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                        array_push($notify_people, $user->id);
                        $notificationText = '###' . $pro_request->procure_tag . '### Request Rejected';
                        $data = [
                            'title' => $notificationText,
                            'data' =>  $pro_request,
                            'notify' => $notify_people,
                        ];
                        $sendNotifications = CommonHelper::sendPushNotification($data);
                        $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                    }

                }
                /* Location approval - means location admin approval required */
                elseif (!empty($pro_request) && $pro_request->hierarchy_approval == 5) {
                    if( $approvalRequest->approve_status == 1 ) {
                        $all_app_reqs = ApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->get();

                        $tot_approval_reqs = count($all_app_reqs);
                        $approves_count = 0;
                        foreach($all_app_reqs as $req) {
                            if($req->approve_status == 1) {
                                $approves_count++;
                                $pab_username = User::find($all_app_reqs[0]->user_id);
                                $pab_username_u[] = User::find($all_app_reqs[0]->user_id);
                                $pab_member_emails[] = $pab_username_u[0]->email;

                                $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                $creators = array_filter($pro_not_mem);
                                try {
                                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($creators)->cc($alertnotify)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                    } else {
                                        Mail::to($creators)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                                array_push($notify_people, $user->id);

                                $notificationText = "###".$pro_request->id."### (Awaiting your approval)";
                                $data = [
                                    'title' => $notificationText,
                                    'data' =>  $pro_request,
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($data);
                                $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                            }
                        }

                        if( $tot_approval_reqs == $approves_count ) {
                            $preApprovalReq = ApprovalRequest::select('approved_qnum')->where('pr_id', '=', $approvalRequest->pr_id)->where('approve_status',1)->whereNotIn('id',[$approvalRequest->id])->get()->toArray();
                            $approvedQnums = array_column($preApprovalReq, 'approved_qnum');
                            $valueCounts = array_count_values($approvedQnums);
                            if(!empty($valueCounts)) {
                                $maxCount = max($valueCounts);
                                $minCount = min($valueCounts);
                                $max_keys = array_keys($valueCounts, $maxCount);
                                if(!in_array($request->approved_qnum, $max_keys)) {
                                    $preApproval = ApprovalRequest::find($approvalRequest->id);
                                    $preApproval->approved_qnum = null;
                                    $preApproval->comments = null;
                                    $preApproval->approve_status = 3;
                                    $preApproval->save();
                                    $return['msg'] = trans('content.procurement_fields.select_other_supplier');
                                    $return["status"] = "failure";
                                    return response()->json($return);
                                }
                            }
                            $pro_request->status_id = 4;
                            $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                            $user = User::find($pro_request->creator_id);
                            if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($user->email)->cc($alertnotify)->queue(new RequestApproved($pro_request, $user));
                                    } else {
                                        Mail::to($user->email)->queue(new RequestApproved($pro_request, $user));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }
                            array_push($notify_people, $user->id);
                            $notificationText = "###".$pro_request->procure_tag."### Request Approved";
                            $data = [
                                'title' => $notificationText,
                                'data' =>  $pro_request,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                        }
                    }
                    else {
                        $pro_request->status_id = 5;
                        $pro_request->approved_at = null;
                        if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($user->email)->cc($alertnotify)->queue(new RequestRejected($pro_request, $user));
                                } else {
                                    Mail::to($user->email)->queue(new RequestRejected($pro_request, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                        array_push($notify_people, $user->id);
                        $notificationText = '###' . $pro_request->procure_tag . '### Request Rejected';
                        $data = [
                            'title' => $notificationText,
                            'data' =>  $pro_request,
                            'notify' => $notify_people,
                        ];
                        $sendNotifications = CommonHelper::sendPushNotification($data);
                        $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                    }
                }
                /* Budgets approval - means Budgets admin approval required */
                elseif (!empty($pro_request) && $pro_request->hierarchy_approval == 9) {

                    if( $approvalRequest->approve_status == 1 ) {
                        $all_app_reqs = ApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->get();

                        $tot_approval_reqs = count($all_app_reqs);
                        $approves_count = 0;
                        foreach($all_app_reqs as $req) {
                            if($req->approve_status == 1) {
                                $approves_count++;
                                $pab_username = User::find($all_app_reqs[0]->user_id);
                                $pab_username_u[] = User::find($all_app_reqs[0]->user_id);
                                $pab_member_emails[] = $pab_username_u[0]->email;

                                $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                $creators = array_filter($pro_not_mem);
                                try {
                                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($creators)->cc($alertnotify)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                    } else {
                                        Mail::to($creators)->queue(new ApproverInfo($pro_request, $user,$pab_username));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                                array_push($notify_people, $user->id);

                                $notificationText = "###".$pro_request->id."### (Awaiting your approval)";
                                $data = [
                                    'title' => $notificationText,
                                    'data' =>  $pro_request,
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($data);
                                $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                            }
                        }

                        if( $tot_approval_reqs == $approves_count ) {
                            $preApprovalReq = ApprovalRequest::select('approved_qnum')->where('pr_id', '=', $approvalRequest->pr_id)->where('approve_status',1)->whereNotIn('id',[$approvalRequest->id])->get()->toArray();
                            $approvedQnums = array_column($preApprovalReq, 'approved_qnum');
                            $valueCounts = array_count_values($approvedQnums);
                            if(!empty($valueCounts)) {
                                $maxCount = max($valueCounts);
                                $minCount = min($valueCounts);
                                $max_keys = array_keys($valueCounts, $maxCount);
                                if(!in_array($request->approved_qnum, $max_keys)) {
                                    $preApproval = ApprovalRequest::find($approvalRequest->id);
                                    $preApproval->approved_qnum = null;
                                    $preApproval->comments = null;
                                    $preApproval->approve_status = 3;
                                    $preApproval->save();
                                    $return['msg'] = trans('content.procurement_fields.select_other_supplier');
                                    $return["status"] = "failure";
                                    return response()->json($return);
                                }
                            }
                            $pro_request->status_id = 4;
                            $pro_request->approved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                            $user = User::find($pro_request->creator_id);
                            if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($user->email)->cc($alertnotify)->queue(new RequestApproved($pro_request, $user));
                                    } else {
                                        Mail::to($user->email)->queue(new RequestApproved($pro_request, $user));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }
                            array_push($notify_people, $user->id);
                            $notificationText = "###".$pro_request->procure_tag."### Request Approved";
                            $data = [
                                'title' => $notificationText,
                                'data' =>  $pro_request,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                        }
                    }
                    else {
                        $pro_request->status_id = 5;
                        $pro_request->approved_at = null;
                        if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            try {
                                if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($user->email)->cc($alertnotify)->queue(new RequestRejected($pro_request, $user));
                                } else {
                                    Mail::to($user->email)->queue(new RequestRejected($pro_request, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }
                        }
                        array_push($notify_people, $user->id);
                        $notificationText = '###' . $pro_request->procure_tag . '### Request Rejected';
                        $data = [
                            'title' => $notificationText,
                            'data' =>  $pro_request,
                            'notify' => $notify_people,
                        ];
                        $sendNotifications = CommonHelper::sendPushNotification($data);
                        $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                    }
                }

                if($pro_request->approval_required == 1) {
                    $pab_id = $pro_request->pab_id;
                    $pab = Pab::find($pab_id);
                    if(!$pab || $pab->totMembers() < 1) {
                        $msg['msg'] = trans('content.procurement_fields.no_user_found_send');
                        return $msg;
                    }

                    $pab_members = [];
                    if($pab->hierarchy_approval == 1) {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                    }
                    else {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                    }

                    foreach($pab_members as $k=>$member) {
                        $approval_user_id = $member->user_id;
                        $user = User::find($approval_user_id);
                        $pab_member_emails[] = $user->email;
                    }

                }

                if($pro_request->status_id != 3){

                    $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                    $creators = array_filter($pro_not_mem);
                    try {
                        if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($creators)->cc($alertnotify)->queue(new StatusChange($pro_request, $user));
                        }  else {
                            Mail::to($creators)->queue(new StatusChange($pro_request, $user));
                        }
                    } catch (\Exception $ex) {
                        Log::error($ex->getMessage());
                    }
                }

                Quotation::where([
                    "pr_id" => $request_id,
                    "po_approved_quotate" => 1
                ])->update(['po_approved_quotate' => null]);

                Quotation::where([
                    "pr_id" => $request_id,
                    "quotation_position" => $approvalRequest->approved_qnum
                ])->update(['po_approved_quotate' => 1]);

                $pro_request->save();

                /** send push notification */
                $notify_people = [];
                $pab_members = [];
                $pab_id = $pro_request->pab_id;
                $pab = Pab::find($pab_id);
                if(!$pab || $pab->totMembers() < 1) {
                    if($pab->hierarchy_approval == 1) {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'ASC')->get();
                    } else {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'ASC')->get();
                    }
                }

                foreach($pab_members as  $k=>$member) {
                    $u = User::find($member->user_id);
                    array_push($notify_people, $u->id);
                }

                $updateUserName = Auth::user()->first_name . " " . Auth::user()->last_name;
                $notificationText = "<p>Update Procure tag id <b>" . $pro_request->procure_tag . "</b> is being updated by <b>" . $updateUserName ."</b>/p>";
                $bodyMsg = "<p>Your Procure request updated. please check your portal or app.</p>";
                $data = [
                    'title' => $notificationText,
                    'data' => $pro_request,
                    'bodyMsg' => $bodyMsg,
                    'notify' => $notify_people,
                ];
                $sendNotifications = CommonHelper::sendPushNotification($data);
                if ($sendNotifications != false) {
                    $response = json_decode($sendNotifications);
                    if (isset($response->failure) && $response->failure == 1) {
                        Log::error("Update Procure id:" . $pro_request->id . " notification error " . json_encode($response));
                    }
                }
                $return['msg'] = trans('content.procurement_fields.your_response_has_been_Sent');
                $return["status"] = "success";

                $history = new History();
                $history->user_id = 1;
                $history->pr_id = $pro_request->id;
                $history->action_msg = 4; // approval /reject request
                $history->save();
                $c = 'Responsed as ' . $approvalRequest->statusLabel() . ' <br/>Comment is, ' . $approvalRequest->comments;
                HistoryEntry::create(['history_id'=>$history->id, 'change_info'=>$c]);
            }
            Log::info("ajaxApprove procurement quotations id:" . $approvalRequest->id. " user_id:" . 1 . " : " . json_encode($request->all()));
        } catch(\Exception $e) {
            DB::rollback();
            Log::error("ajaxApprove: ". $e->getMessage());
        }
        return response()->json($return);
    }

    public function getApproverPageChart($reqId) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to get chart data',
            ];
            $budghetWiseTotal = DB::select('select  concat(" ", b.category, "(", d.name, ")") as name, b.amount from procure_budgets as b join departments as d on b.department_id = d.id');

            $budgetSummaryCollection = DB::select('select tbl1.id, tbl1.category, dep.name as department, ifnull(tbl1.amount, 0) as tot_budget, ifnull(tbl2.tot_utilized, 0) as tot_utilized, ifnull(tbl2.in_progress, 0) as in_progress, (ifnull(tbl1.amount, 0) - ifnull(tbl2.tot_utilized, 0)) as remaining from procure_budgets as tbl1  left join ( select ri.bc_id, sum(qi.price_per_item * ri.qty) as tot_utilized, sum(case when r.status_id in (4,6,7) then qi.price_per_item * ri.qty else 0 end) as in_progress from procure_quotation_items as qi join procure_quotations as q on qi.qid = q.id join procure_request_items as ri on qi.item_id = ri.id  join procure_requests as r on qi.pr_id = r.id  join procure_budgets as b on ri.bc_id = b.id where q.po_approved_quotate = 1 and r.status_id in (4,6,7,8) group by ri.bc_id) as tbl2 on tbl1.id = tbl2.bc_id left join departments as dep on tbl1.department_id = dep.id where tbl1.id in (select distinct bc_id from procure_request_items where pr_id = "' . $reqId . '")');

            $return = [
                'status' => 'success',
                'msg' => 'Chart data fetched successfully',
                'data' => [
                    "tot_budgets_cat_wise" => $budghetWiseTotal,
                    "budgets_summary_collection" => $budgetSummaryCollection
                ]
            ];
            return $return;
        } catch(\Exception $e) {
            Log::error("getApproverPageChart() : ".$e->getMessage());
            return $return;
        }
    }

    public function generatePo(Request $request) {
        try {
            DB::beginTransaction();
            $return = [
                "msg" => trans('content.procurement_fields.unable_to_update'),
                "status" => "failure"
            ];
    
            try {
                $request_id = ($request->request_id);
                $get_request = ProcureRequest::findOrFail($request_id);
            }
            catch(\Exception $e) {
                return response()->json($return);
            }
    
            $pro_request = $get_request;
    
            $data = $request->only('po_number', 'po_due_date', 'po_shipping_address', 'po_terms_n_conditions', 'po_payment_terms', 'po_notes', 'include_taxes', 'tax_name', 'tax_percentage');
    
            $rules = [
                'po_number' => 'required|string|max:80',
                'po_shipping_address' => 'required|string|max:600',
                'po_terms_n_conditions' => 'required|string|max:2000',
                'po_payment_terms' => 'nullable|string|max:2000',
                'po_notes' => 'required|string|max:2000'
            ];
            $msgs = [];
    
            $validator = \Validator::make($data, $rules, $msgs);
            if($validator->fails()) {
                $return["msg"] = trans('content.procurement_fields.please_give_the_values');
                return response()->json($return);
            }
    
            $alertnotify = null;
            if(Settings::first()->alerts_enabled == 1){
                $alertnotify = CommonHelper::getGlobalAlertEmail();
            }
    
            $get_quotate = Quotation::where([
                "pr_id" => $request_id,
                "po_approved_quotate" => 1
            ])->get();
            
            if(! count($get_quotate)) {
                $return["msg"] = trans('content.procurement_fields.unable_to_get_the_approved_quotation');
                return response()->json($return);
            }
    
            $quotate = $get_quotate[0];
            $quotate->fill($data);
            $quotate->po_created_by = 1;
            $quotate->po_created_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
            $quotate->po_due_date = CommonHelper::getDateAs($request->po_due_date, "Y-m-d", "d/m/Y");
    
            $get_items = DB::select('SELECT qi.*, ri.qty FROM `procure_quotation_items` as qi join procure_quotations as q on qi.qid = q.id join procure_request_items as ri on qi.item_id = ri.id where q.po_approved_quotate = 1 and qi.pr_id = "' . $request_id . '"');
    
            $total_item_amount = 0;
            foreach($get_items as $item) {
                $amount = round($item->qty * $item->price_per_item, 2);
                QuotationItem::find($item->id)->update(["po_item_amount" => $amount]);
                $total_item_amount += $amount;
            };
    
            $quotate->total_amount = $total_item_amount;
            if($quotate->include_taxes == true) {
                $quotate->tax_amount = round(($total_item_amount * $quotate->tax_percentage) / 100, 2);
            }
            $quotate->po_net_amount = $total_item_amount + $quotate->taxes;
            $pro_request->status_id = 6;
            $pro_request->po_generated_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
    
            if($quotate->save() && $pro_request->save()) {
                $creators = [];
                $pab_member_emails = [];
                $created_user = User::find($pro_request->creator_id);
                $incharge = User::find($quotate->po_created_by);
    
                if($pro_request->approval_required == 1) {
                    $pab_id = $pro_request->pab_id;
                    $pab = Pab::find($pab_id);
                    if(!$pab || $pab->totMembers() < 1) {
                        $msg['msg'] = trans('content.procurement_fields.no_user_found');
                        return $msg;
                    }
    
                    $pab_members = [];
                    if($pab->hierarchy_approval == 1) {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                    }
                    else {
                        $pab_members = PabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                    }
    
                    foreach($pab_members as $k=>$member) {
                        $approval_user_id = $member->user_id;
                        $user = User::find($approval_user_id);
                        if(!empty($user) && $user->email != "") {
                            $pab_member_emails[] = $user->email;
                        }
                    }
    
                }
    
                $pro_not_mem = array_unique(array_merge([$created_user->email],[$incharge->email], $pab_member_emails));
                $creators = array_filter($pro_not_mem);
                try {
                    if(config('mail.service_enabled') && $alertnotify && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($creators)->cc($alertnotify)->queue(new PoGenerated($pro_request, $user));
                    } else {
                        Mail::to($creators)->queue(new PoGenerated($pro_request, $user));
                    }
                } catch (\Exception $ex) {
                    Log::error($ex->getMessage());
                }
    
                $return["msg"] = trans('content.procurement_fields.PO_has_been_generated');
                $return["status"] = "success";
    
                $history = new History();
                $history->user_id = 1;
                $history->pr_id = $request_id;
                $history->save();
                HistoryEntry::create(['history_id'=>$history->id, 'change_info'=>$return["msg"]]);
            }
            DB::commit();
            return response()->json($return);
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error("Api generatePo() : ".$e->getMessage());
            return response()->json($return);
        } 
    }
    
    public function printPo(Request $request, $reqId) {
        $return = [
            "msg" => trans('content.procurement_fields.unable_to_print'),
            "status" => "danger"
        ];

        try {
            $request_id = ($reqId);
            $get_request = ProcureRequest::findOrFail($request_id);
        } catch(\Exception $e) {
            return $return;
        }

        $vd = new stdClass;
        $vd->pro_request = $get_request;
        $vd->default_loc = Location::first();
        try {
            $get_quotate = Quotation::where('pr_id', '=', $request_id)->where('po_approved_quotate', '=', 1)->get();
            $vd->quotate = $get_quotate[0];

            $vd->items = DB::select('select ri.id, ri.qty, u.name as unit, qi.price_per_item, (ri.qty * qi.price_per_item) as amount, ri.item_name, ri.description, q.tax_name, q.tax_percentage, q.tax_amount from procure_request_items as ri  join procure_quotations as q on q.pr_id = ri.pr_id and q.po_approved_quotate = 1 join procure_quotation_items as qi on qi.pr_id = ri.pr_id and qi.item_id = ri.id and qi.qid = q.id join procure_units as u on ri.unit_id = u.id where ri.pr_id = "' . $request_id . '"');
            $vd->supplier = Supplier::find($vd->quotate->supplier_id);
        } catch(\Exception $e) {
            return $return;
        }

        $return = [
            "msg" => trans('content.procurement_fields.unable_to_print'),
            "status" => "danger",
            'data' => $vd
        ];
        return $return;
    }

    public function procurementHistory($reqId) {
        try {
            $return = [
                "msg" => trans('content.procurement_fields.Invalid_Access'),
                "status" => "danger"
            ];
    
            try {
                $request_id = $reqId;
                $get_request = ProcureRequest::findOrFail($request_id);
            } catch(\Exception $e) {
                return $return;
            }
    
            $pro_request = $get_request;
            $history = History::select('procure_histories.*',DB::raw("concat(users.first_name,' ',users.last_name) as user_name"))->leftJoin('users','users.id','user_id')->where('pr_id', '=', $pro_request->id)->orderBy('id', 'desc')->get();
            foreach($history as $h){
                $h->entries;
            }

            $return = [
                "msg" => "History fetched successfully",
                "status" => "success",
                "data" => $history
            ];
            
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("Listcontroller procurementHistory() : ".$e->getMessage());
            return $return;
        }
    }

    public function itemUnits() {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch units',
            ];
            $units = Unit::select('id', 'name')->get();
            $return = [
                'status' => 'success',
                'msg' => 'Units fetched successfully',
                'data' => $units
            ];
            return $return;
        } catch(\Exception $e) {
            Log::error("ListController : itemUnits".$e->getMessage());
            return $return;
        }
    }

    public function procurementSuppliers() {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch suppliers',
            ];
            $suppliers = Supplier::select('id', 'name')->get();
            $return = [
                'status' => 'success',
                'msg' => 'Supplier fetched successfully',
                'data' => $suppliers
            ];
            return $return;
        } catch(\Exception $e) {
            Log::error("procurementSuppliers: ".$e->getMessage());
            return $return;
        }
    }

    public function revokeApprovalDecision(Request $request) {
        $return = [
            "msg" => trans('content.procurement_fields.Unable_to_revoke_the_early_decision'),
            "status" => "failure"
        ];

        try {
            $currentUser = User::find(Auth::user()->id);
            $request_id = ($request->request_id);
            $pro_request = ProcureRequest::findOrFail($request_id);

            if(! in_array($pro_request->status_id, [3, 4, 5, 10, 11]) || $pro_request->approval_required != 1) {
                throw new \Exception('Procurment Request is in other status which does not suitable to accept your decision.');
            }

            $approvalRequest = ApprovalRequest::findOrFail($request->approval_request_id);
            if(empty($approvalRequest) || !$approvalRequest || $approvalRequest->user_id != $currentUser->id || $approvalRequest->pr_id != $pro_request->id) {
                throw new \Exception('Invalid Request');
            }
        }
        catch(\Exception $e) {
            $return['msg'] = $e->getMessage();
            return response()->json($return);
        }

        DB::beginTransaction();
        try {
            $pro_request->status_id = 3;
            $pro_request->approved_at = null;
            $pro_request->save();

            $approvalRequest->approve_status = 3;
            $approvalRequest->approved_qnum = null;
            $approvalRequest->comments = null;
            $approvalRequest->save();

            $cc_list = [$currentUser->id];
            if($pro_request->hierarchy_approval == Pab::MODE_GROUP_APPROVAL) {
                $getNextLevels = ApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->where('id', '!=', $approvalRequest->id)->get();

                foreach($getNextLevels as $nl) {
                    if($nl->user->email) {
                        $cc_list[] = $nl->user->email;
                    }
                }
            }
            elseif($pro_request->hierarchy_approval == Pab::MODE_MINIMUM_APPROVAL) {
                $getNextLevels = ApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->where('id', '!=', $approvalRequest->id)->get();

                foreach($getNextLevels as $nl) {
                    if($nl->user->email) {
                        $cc_list[] = $nl->user->email;
                    }
                }
            }
            elseif($pro_request->hierarchy_approval == Pab::MODE_LEVEL_BY_LEVEL) {
                $getNextLevels = ApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->where('hierarchy_level', '>', $approvalRequest->hierarchy_level)->where('id', '!=', $approvalRequest->id)->orderBy('hierarchy_level', 'asc')->get();

                if(count($getNextLevels)) {
                    ApprovalRequest::where('pr_id', '=', $approvalRequest->pr_id)->where('pab_id', '=', $approvalRequest->pab_id)->where('hierarchy_level', '>', $approvalRequest->hierarchy_level)->where('id', '!=', $approvalRequest->id)->update([
                        'approve_status' => 4,
                        'approved_qnum' => null,
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
            try {
                if(config('mail.service_enabled') && $needToNotify && count($needToNotify)) {
                    Mail::to($needToNotify)->queue(new ApproverInfo($pro_request, $currentUser, $currentUser));
                }
            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
            }

            $history = new History();
            $history->user_id = $currentUser->id;
            $history->pr_id = $pro_request->id;
            $history->save();
            $c = 'Early decision has been removed';
            HistoryEntry::create(['history_id'=>$history->id, 'change_info'=>$c]);
            DB::commit();

            $return['status'] = 'success';
            $return['msg'] = trans('content.procurement_fields.your_early_decision');
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            $return['msg'] = $e->getMessage();
            return response()->json($return);
        }
    }

    public function getProcurementDepartments(Request $request) {
        try {
            $return = [
                "msg" => "Unable to fetch departments",
                "status" => "fail"
            ];
            $departments = Department::select('departments.id', 'departments.name')
            ->rightJoin('procure_budgets as pb', 'departments.id', '=', 'pb.department_id')
            ->whereNull('departments.deleted_at')
            ->groupBy('departments.id', 'departments.name')
            ->get();
            $return = [
                "msg" => "Procure departments fetched successfully.",
                "status" => "success",
                "data" => $departments,
            ];
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("getProcurementDepartments : ".$e->getMessage());
            return response()->json($return);
        }
    }
}
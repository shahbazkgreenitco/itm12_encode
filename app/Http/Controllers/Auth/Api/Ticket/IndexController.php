<?php

namespace App\Http\Controllers\Auth\Api\Ticket;

use App\Events\TicketCreated;
use App\Exports\Tickets\Tickets;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Ticket\RequestController;
use App\Http\Controllers\Auth\Api\Ticket\RequestController as ApiRequestController;
use App\Http\Controllers\Ticket\IndexController as TicketIndexController;
use App\Mail\Ticket\Request\CreationServiceRequest;
use App\Mail\Ticket\Request\TicketApproverInfo;
use App\Mail\Ticket\TransferAssignedNotificationToUser;
use App\Models\CustomField;
use App\Models\Form\DynamicForm;
use App\Models\Form\RequestedForm;
use App\Models\Ticket\Request\TicketRequestHistory;
use App\Models\Ticket\TicketApprovalRequest;
use App\Models\Ticket\TicketPab;
use App\Models\Ticket\TicketPabMember;
use App\Models\Ticket\TicketProcureRequest;
use App\Models\Ticket\TicketStatusHistory;
use App\Models\Ticket\TicketTrigger;
use App\Models\Ticket\TicketType;
use App\Models\Ticket\TktDetail;
use Illuminate\Http\Request;
use App\Models\Base;
use App\Models\Company;
use App\Models\Department;
use App\Models\Location;
use App\Models\ServiceTicket;
use App\Models\Ticket\Status;
use App\Models\Ticket\Priority;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Attachment;
use App\Models\Ticket\ProblemCategory;
use App\Models\Ticket\Privilege;
use App\Models\Ticket\Config;
use App\Models\TktFollowing;
use App\Models\Ticket\AutoCreationAccount;
// use App\Models\Notification;
use App\Models\User;
use App\Models\Settings;
use App\Models\Device;
use App\Models\Holiday;
use App\Mail\Ticket\TransferAssignedNotificationToTechnician;
use Auth;
use Storage;
use Validator;
use DB;
use Log;
use stdClass;
use Illuminate\Validation\Rule;
use Mail;
use Carbon\Carbon;
use App\Helpers\Common as CommonHelper;
use App\Models\KnowledgeManagement\Category;
use App\Models\KnowledgeManagement\Document;
use App\Models\KnowledgeManagement\Tags;
use App\Mail\Ticket\IntimateNewCreator;
use App\Mail\Ticket\IntimateSuccessCreation;
use App\Mail\Ticket\IntimateAssigned;
use App\Mail\Ticket\StatusChanged;
use App\Mail\Ticket\Resolved;
use App\Mail\Ticket\UserComment;
use App\Mail\Ticket\MergeAlertForCreator;
use App\Mail\Ticket\MergeAlertForHandler;
use App\Mail\Ticket\FeedbackSend;
use App\Models\CustomFieldset;
use App\Http\Controllers\Report\TicketController as ReportTicketController;
use App\Models\TaskManagement\Task;
use App\Models\TaskManagement\TaskHistory;
use App\Models\TaskManagement\TaskManagementAttachment;
use App\Http\Controllers\Auth\Api\CommonController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\Auth\Api\DepartmentController as DepartmentApiController;
use App\Http\Controllers\Ticket\ProblemCategoryController;
use App\Http\Controllers\Ticket\TicketController;
use App\Models\TKTAutoUpdateSetting;
use Illuminate\Support\Str;
use App\Mail\Tasks\TaskRemove;
use App\Jobs\ProcessAutoResponse;

class IndexController extends Controller {

    // will create initial temporory record
    public function initiate(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to initiate new ticket"];
        $st = Ticket::create(["creator_id" => Auth::user()->id, "created_via" => 4]);
        if($st) {
            $return["status"] = "success";
            $return["msg"] = "Ticket ID generated successfully";
            $return["id"] = $st->id;
        }
        return response()->json($return);
    }

    // create new ticket by not previleged user
    public function createByUser(Request $request) {
        $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.Unable_to_create_new_ticket')];
        $input = $request->all();

        $rules = [
            // 'id' => [
            //     'required',
            //     Rule::exists('tkt_tickets')->where(function($q) use($request) {
            //         $q->where('id', '=', $request->input('id'));
            //         $q->where('is_temp', '=', 1);
            //     })
            // ],
            'content' => 'required',
            'subject' => 'required|min:3|max:500',
            'department_id' =>'required|exists:departments,id',
            'problem_category_id' =>'required|exists:tkt_problem_categories,id',
            'priority_id' =>'nullable|exists:tkt_priorities,id',
            'device_id' => 'nullable|sometimes|exists:assets,id',
            'tat' => 'integer|min:0',
        ];
        $msg = [
            'department_id.required' => 'Please select Department.',
            'problem_category_id.required' => 'Please select Problem Category.'
        ];
        // custom field validation start
        $data_fields = $request->input("fields", null);
        $custom_fields = [];
        $customData = [];
        $deparCustomObj = Department::where('id', $request->department_id)->withTrashed()->first();
        if(!empty($deparCustomObj)) {
            if (isset($deparCustomObj->customFieldset->fields) && !empty($deparCustomObj->customFieldset->fields)) {
                foreach($deparCustomObj->customFieldset->fields as $f) {
                    $col_name = $f->nameToColumn();
                    $rules[$col_name] = $f->pivot->required && !isset($data_fields[$col_name]) ? 'required|string|max:255' : 'nullable|string|max:255';
                    $customData[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                    $custom_fields[] = $col_name;
                }
            }
        }
        // custom field validation end

        $validator = Validator::make($request->all(), $rules, $msg);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $enableUsbRequest = ProblemCategory::enableUsbRequest([$deparCustomObj->id]);
        if(!empty($enableUsbRequest) && config('app.client') == "ltts"){
            if(!isset($request->device_id) && in_array($request->problem_category_id,$enableUsbRequest)){
                $return["msg"] = trans('Device field is required');
                return response()->json($return);
            }
        }

        DB::beginTransaction();
        try {
            /* check problem category has sub-categories */
            $problem_category = ProblemCategory::find($request->problem_category_id);
            $sub_category = false;
            if($problem_category->department_id != $request->department_id) {
                if (isset($request->ticketFromML) && $request->ticketFromML == 1) {
                    $deptRequest = new \Illuminate\Http\Request([
                        'name' => 'To be assigned'
                    ]);
                    $departmentController = new DepartmentApiController();
                    $departmentResponse = $departmentController->getOrCreate($deptRequest);
                    $departmentData = $departmentResponse->getData();

                    if (!empty($departmentData->department->id)) {
                        $request->merge([
                            'department_id' => $departmentData->department->id
                        ]);
                    }

                    $catRequest = new \Illuminate\Http\Request([
                        'name'  => 'New Ticket',
                        'department_id'  => $request->department_id,
                        'parent_id'      => null 
                    ]);
                    $problemCategoryController = new ProblemCategoryController();
                    $categoryResponse = $problemCategoryController->getOrCreateProblemCategory($catRequest);
                    $categoryData = $categoryResponse->getData();

                    if (!empty($categoryData->category->id)) {
                        $request->merge([
                            'problem_category_id' => $categoryData->category->id,
                        ]);
                        $problem_category = ProblemCategory::find($categoryData->category->id);
                    }
                }else{
                    $return["msg"] = trans('content.service_ticket_fields.Please_choose_the_valid_department');
                    return response()->json($return);
                }
            }

            if ($problem_category->totSubCategories() > 0) {

                if (!$request->sub_category_id) {
                    if (isset($request->ticketFromML) && $request->ticketFromML == 1) {
                        $return["msg"] = "You can not create ticket please use the portal.";
                        return response()->json($return);
                    }else{
                        $return["msg"] = trans('content.service_ticket_fields.Please_enter_valid_sub-category');
                        return response()->json($return);
                    }
                }

                try {
                    if(isset($request->sub_category_id) && $request->sub_category_id != null){
                    $sub_category = ProblemCategory::findOrFail($request->sub_category_id);
                    if ($sub_category->parent_id != $request->problem_category_id) {
                            if (isset($request->ticketFromML) && $request->ticketFromML == 1) {
                                $return["msg"] = "You can not create ticket please use the portal.";
                                return response()->json($return);
                            }else{
                                throw new \Exception(trans('content.service_ticket_fields.Enter_valid_sub-category'));
                            }
                        }
                    }
                } catch(\Exception $e) {
                    $return["msg"] = $e->getMessage();
                    return response()->json($return);
                }
            }
            if(in_array(config('app.client'), ["ril"])){
                $dep = Department::find($request->department_id);
                $subject = $dep->name . " - " . $problem_category->name;
                if(isset($sub_category) && !empty($sub_category)) {
                    $subject = $dep->name . " - " . $problem_category->name . " - " . $sub_category->name;
                }
                if(isset($request->additionalInfo) && $request->additionalInfo != 0) {
                    $content = $request->additionalInfo;
                    Log::info("additionalInfo:" . json_encode($content));
                } else {
                    $content = $subject;
                }
            }
            if(!isset($request->creator_id) || $request->creator_id == null){
                $input['creator_id'] = Auth::user()->id;
                $request->merge(['creator_id' => Auth::user()->id]);
            }
            if (!$request->has('plugin_ticket') || $request->plugin_ticket != 1) {
                if (Auth::user()->getRoleNames()->first() === 'User' && 
                    isset($request->creator_id) && $request->creator_id != Auth::id()) {   
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are not allowed to create a ticket on behalf of another user.',
                    ]);
                }
            }
            $problemManagementApicall = false; 
            if (config('app.client') === 'ltts') {
                $enableUsbRequest_old = [];

                $department = Department::find($request->department_id);

                if ($department && ($department->name === "IT Service Request" || $department->name === "IT Service Request Overseas")) {
                    $enableUsbRequest_old = ProblemCategory::enableUsbRequest([$department->id]);
                }
                $enableUsbRequests_new = ProblemCategory::where('privilege_access', 1)->pluck('id')->toArray();
                $enableUsbRequests = array_merge($enableUsbRequest_old, $enableUsbRequests_new);
                $enableCategory = array_values(array_unique($enableUsbRequests));

                if (in_array($request->problem_category_id, $enableCategory) || in_array($request->sub_category_id, $enableCategory)) {
                    $problemManagementApicall = true;
                }
            }
            if (!empty($request->device_id)) {
                $isDeviceFound = CommonController::existsInDropdown([DeviceController::class, 'getUserDeviceForDropDown'],
                    ['user_id' => $request->creator_id,'problemManagementApiCall' => $problemManagementApicall],
                    'id',$request->device_id);
                if (!$isDeviceFound) {
                    return response()->json([
                        "status" => "fail",
                        "msg" => "The selected device is not assigned or not available."
                    ]);
                }
            }
            if ($request->creator_id) {
                $authCreatorUser = $usr = User::find($request->creator_id);
                if (empty($usr)) {
                    $return["msg"] = trans('content.service_ticket_fields.Chosen_User_is_not_found');
                    return response()->json($return);
                }
                if ($usr->checkoutBasicClearance()) {
                    $return["msg"] = trans('content.service_ticket_fields.Chosen_User_is_not_in_Active_Status');
                    return response()->json($return);
                }
        
                if ((!empty($sub_category) && in_array(4, explode(',', $sub_category->hierarchy_approval))) || 
                (!empty($problem_category) && in_array(4, explode(',', $problem_category->hierarchy_approval)))) {
                    if (empty($usr) || empty($usr->manager) || $usr->manager->email == "") {
                        $return["msg"] = trans('content.service_ticket_fields.Manager_email_is_not_found');
                        return response()->json($return);
                    }
                }

                if ((!empty($sub_category) && in_array(11, explode(',', $sub_category->hierarchy_approval))) || 
                   (!empty($problem_category) && in_array(11, explode(',', $problem_category->hierarchy_approval)))) {
                    if (empty($usr->department)) {
                        $return["msg"] = "Department is not set for ticket creator. Please set the department to continue.";
                        return response()->json($return);
                    }
                    if (empty($usr->department->department_head_id)) {
                        $return["msg"] = "Department head is not set as department head approval mode is selected.";
                        return response()->json($return);
                    }
                }
            }
            if ( ($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id == 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id == 0) ) {
                $return["msg"] = trans('content.service_ticket_fields.select_PAB');
                return response()->json($return);
            } elseif ( ($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id != 0) ) {
                try {
                    $request->request->add(['creator_id' => $authCreatorUser->id]);
                    if(isset($request->sub_category_id) && $request->sub_category_id != "" && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) {
                        $problem_category = ProblemCategory::find($request->sub_category_id);
                    } else {
                        $problem_category = ProblemCategory::find($request->problem_category_id);
                    }
                    $creatorId = $request->input("creator_id")
                        ?? ($request->plugin_ticket ?? false
                            ? $request->creator_id
                            : Auth::user()->id);

                    $st = Ticket::create([
                        "creator_id"  => $creatorId,
                        "created_by"  => Auth::user()->id,
                        "created_via" => ($request->ticketFromML ?? 0) == 1 ? 6 : 4,
                    ]);                    
                    $tkt_detail = new TktDetail();
                    if ($st->created_via == 6) {
                        $seatNo = isset($st->creator->userDetails->seat_no) ? $st->creator->userDetails->seat_no : null;
                    } elseif (isset($request->seat_no)) {
                        $seatNo = $request->seat_no;
                    } else {
                        $seatNo = null;
                    }
                    $tkt_detail->ticket_id =  $st->id;
                    $tkt_detail->seat_no = $seatNo;
                    $tkt_detail->save();
                    $custom_fields = [];
                    $data_fields = $request->input("fields", null);
                    $deptObj = Department::where('id', $request->department_id)->withTrashed()->first();
                    if($problem_category->is_form_required == 1 && $problem_category->form_id !== 0) {
                        $requestForm = RequestedForm::where('form_id', $problem_category->form_id)->where("tmp_id", $request->tmp_id)->latest()->first();
                        if(empty($requestForm)) {
                            $encodedRequestID = $request->tmp_id;
                            $url = url('requested_form', $problem_category->form_id).'?q='.$encodedRequestID;
                            $return["msg"] = ("Please fill the requested form");
                            return response()->json($return);
                        }
                    }
                    if(!empty($deptObj) && $deptObj->customFieldset && count($deptObj->customFieldset->fields)) {
                        foreach($deptObj->customFieldset->fields as $f) {
                            $col_name = $f->nameToColumn();
                            // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                            $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                            $custom_fields[] = $col_name;
                        }
                    }

                    // custom field code start
                    if(count($custom_fields)) {
                        foreach($custom_fields as $f) {
                            $st->{$f} = isset($data[$f]) ? $data[$f] : null;
                        }
                    }
                    $otherLocation = Location::where('name', 'like', 'Other')->first();
                    if(empty($otherLocation)) {
                        $otherLocation = new Location();
                        $otherLocation->name = $otherLocation->address = $otherLocation->city = $otherLocation->state = "Other";
                        $otherLocation->country = "IN";
                        $otherLocation->currency = "INR";
                        $otherLocation->country_id = 101;
                        $otherLocation->state_id = 4008;
                        $otherLocation->city_id = 133024;
                        $otherLocation->user_id = $authCreatorUser->id;
                        $otherLocation->save();
                    }
                    $st->location_id = ($authCreatorUser->location_id == "") ? $otherLocation->id : $authCreatorUser->location_id;
                    $st->save();
                    $input['id'] = $st->id;
                    $ticketProcureRequestObj = new TicketProcureRequest();
                    $ticketProcureRequestData = $ticketProcureRequestObj->getDataFields($input);
                    $ticketProcureRequestData['id'] = $input['id'];
                    $ticketProcureRequestData['ticket_id'] = $input['id'];
                    $ticketProcureRequestData['creator_id'] = $authCreatorUser->id;
                    $ticketProcureRequestData['sub_category_id'] = $sub_category ? $sub_category->id : null;
                    $ticketProcureRequestData['priority_id'] = $problem_category && $problem_category->priority_id ? $problem_category->priority_id : 3;
                    if($sub_category && $sub_category->tat) {
                        $ticketProcureRequestData['tat'] = $sub_category->tat;
                    }
                    else if($problem_category->tat){
                        $ticketProcureRequestData['tat'] = $problem_category->tat;
                    }
                    else{
                        $st = Ticket::find($input['id']);
                        $st->priority_id = $ticketProcureRequestData['priority_id'];
                        $ticketProcureRequestData['tat'] = $st->priority->service_time;
                    }

                    $pab_ids = explode(",", $problem_category->pab_id);
                    $pab = array();
                    foreach ($pab_ids as $key => $val) {
                        $pab[$val] = 0;
                    }
                    $checkPabMember = CommonHelper::checkPabMember($pab_ids,$request->creator_id);
                    if($checkPabMember['status'] == "fail"){
                        return $checkPabMember;
                    }
                    $ticketProcureRequestData['pab_id'] = json_encode($pab);
                    $ticketProcureRequestObj->fill($ticketProcureRequestData);
                    // $ticketProcureRequestObj->assignByHirarchy();
                    if ($ticketProcureRequestObj->creator_id) {
                        $creatorObj = User::where('id', $ticketProcureRequestObj->creator_id)->first();
                        $ticketProcureRequestObj->location_id = ($creatorObj->location_id == "") ? $authCreatorUser->location_id : $creatorObj->location_id;
                    } else {
                        $ticketProcureRequestObj->location_id = $authCreatorUser->location_id;
                    }

                    if ($ticketProcureRequestObj->save()) {
                        // update service request id for dynamic requested form
                        $usr = User::find($ticketProcureRequestObj->creator_id);
                        if(isset($request->tmp_id) && $request->tmp_id != 'null') {
                            $requestedForms = RequestedForm::select("id", "request_id")->where("tmp_id", $request->tmp_id)->latest()->first();
                            if (isset($requestedForms)) {
                                $requestedForms->request_id = $ticketProcureRequestObj->id;
                                $requestedForms->save();
                            }
                        }
                        /**update ticket_id attachment */
                        if(isset($request->tmp_id)) {
                            $attachments = Attachment::where("tmp_id", $request->tmp_id)->select("id", "ticket_id")->get();
                            if (isset($attachments)) {
                                foreach ($attachments as $attachment) {
                                    $attachment->ticket_id = $st->id;
                                    $attachment->update();
                                }
                            }
                        }
                        
                        $processResult = Attachment::processForB64Imgs($input['content'], $st->id, $authCreatorUser->id);
                        if(isset($processResult['tmp_id']) && $processResult['tmp_id'] != 'null') {
                            $attachments = Attachment::where("tmp_id", $processResult['tmp_id'])->select("id", "ticket_id")->get();
                            foreach ($attachments as $attachment) {
                                $attachment->ticket_id = $st->id;
                                $attachment->update();
                            }
                        }
                        $ticketProcureRequestObj->original_ticket_reference = $ticketProcureRequestObj->id ;
                        $ticketProcureRequestObj->fillRequestTag();
                        $acfe = AutoCreationAccount::where('auto_create_from_email', 1)->where('id', $ticketProcureRequestObj->department->tkt_auto_creation_id)->first();
                        $ticketProcureRequestObj->ac_email_id = (!empty($acfe)) ? $acfe->id : 0;
                        $pab_ids = explode(",", $problem_category->pab_id);
                        $pab = TicketPab::find($pab_ids[0]);
                        $checkPabMember = CommonHelper::checkPabMember($pab_ids, $authCreatorUser->id);
                        if($checkPabMember['status'] == "fail"){
                           return $checkPabMember;
                        }
                        $pab_members_check = TicketPabMember::where('pab_id', '=', $pab->id)->where('user_id',$usr->id)->count();
                        if($pab_members_check > 0 ) {
                            $return['msg'] = trans('content.service_ticket_fields.creator_and_requester_no_same');
                            return response()->json($return);
                        }
                        $pab_members_minimum = TicketPabMember::where('pab_id', '=', $pab->id)->count();
                        if($pab->required_minimum_approvals > $pab_members_minimum && $pab->hierarchy_approval == 2) {
                            $return['msg'] = trans('content.service_ticket_fields.minimium_approval_count');
                            return response()->json($return);
                        }
                        // 8 = System Approval
                        if(!empty($pab) && $pab->hierarchy_approval == 8) {
                            $ticketProcureRequestObj = TicketProcureRequest::find($ticketProcureRequestObj->id);
                            $ticketProcureRequestObj->update([
                                "status_id" => 3,
                                "approved_at" => Carbon::now()
                            ]);

                            if(!$ticketProcureRequestObj->save()) {
                                return $return;
                            }

                            $return["status"] = "success";
                            $return["msg"] =  trans('content.service_ticket_fields.new_ticket_system_approval', ['id' =>  $ticketProcureRequestObj->id]);

                            $history = new TicketRequestHistory();
                            $history->user_id = $authCreatorUser->id;
                            $history->pr_id = $ticketProcureRequestObj->id;
                            $history->change_info = $return["msg"];
                            $history->save();

                            // send email to create new request for user
                            $user = User::find($ticketProcureRequestObj->creator_id);
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager =  User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    Log::info("ServiceRequest createByUser Mail: " . json_encode($user->email));
                                    Mail::to($user->email)->cc($cc_emails)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error("ServiceRequest createByUser Mail: " . $ex->getMessage());
                            }
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $user->id);
                            $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                            $tkt_config = Config::first();
                            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                            $data = [
                                'title' => $notificationText,
                                'data' => CommonHelper::setDataForNotification($ticketProcureRequestObj, 'request', $notificationType),
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("createByUser service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                }
                            }
                        } else {
                            $ticketProcureRequestObj = TicketProcureRequest::find($ticketProcureRequestObj->id);
                            if (!empty($pab) && $pab->hierarchy_approval != 4 && $pab->hierarchy_approval != 9 && $pab->hierarchy_approval != 10 && $pab->hierarchy_approval != 11) {
                                if (!$pab || $pab->totMembers() < 1) {
                                    $return['msg'] = trans('content.service_ticket_fields.No_user_found_send_request');
                                    return $return;
                                }

                                app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);

                                $pab_members = [];
                                if ($pab->hierarchy_approval == 1) {
                                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                                } else if ($pab->hierarchy_approval == 5) {
                                    if ($ticketProcureRequestObj->location_id != null) {
                                        $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->where('location_id', $ticketProcureRequestObj->location_id)->orderBy('id', 'asc')->get();
                                        if(count($pab_members) == 0) {
                                            $return["msg"] = trans('content.service_ticket_fields.handler_not_found');
                                            return response()->json($return);
                                        }
                                    } else {
                                        $return["msg"] = trans('content.service_ticket_fields.No_user_found_send_request');
                                        return response()->json($return);
                                    }
                                } else {
                                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                                }

                                foreach ($pab_members as $k => $member) {
                                    $approvalRequest = new TicketApprovalRequest();
                                    $approvalRequest->pr_id = $ticketProcureRequestObj->id;
                                    $approvalRequest->pab_id = $pab_ids[0];
                                    $approvalRequest->user_id = $member->user_id;
                                    $user = User::find($approvalRequest->user_id);
                                    $pab_member_emails[] = $user->email;
                                }
                                $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                $creators = array_filter($pro_not_mem);
                            }  elseif(!empty($pab) && $pab->hierarchy_approval == 11) { 
                                $usr = User::find($ticketProcureRequestObj->creator_id);
                                if (empty($usr->department)) {
                                    $return["msg"] = "Department is not set for user. Please set the department to continue.";
                                    return response()->json($return);
                                }
                                if (empty($usr->department->department_head_id)) {
                                    $return["msg"] = "Department head is not set as department head approval mode is selected.";
                                    return response()->json($return);
                                }
                            
                                app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);
                                $departmentHeadId = $usr->department->department_head_id;
                                $departmentHeadUser = User::find($departmentHeadId);
                                $creators = [$departmentHeadUser->email];
                            } else {
                                if (empty($usr) || empty($usr->manager) || $usr->manager->email == "") {
                                    $return["msg"] = trans('content.service_ticket_fields.Manager_email_is_not_found');
                                    return response()->json($return);
                                }

                                app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);

                                $creators = [$authCreatorUser->manager->email];
                            }

                            $alertnotify = null;
                            if (Settings::first()->alerts_enabled == 1) {
                                $alertnotify = CommonHelper::getGlobalAlertEmail();
                            }

                            // send email to create new request for user
                            $user = User::find($ticketProcureRequestObj->creator_id);
                            try {
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    Log::info("createByUser new Service request sent mail: " . json_encode($user->email));
                                    Mail::to($user->email)->cc($alertnotify)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error("createByUser request send mail to creator: " . $ex->getMessage());
                            }
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $user->id);
                            $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                            $tkt_config = Config::first();
                            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                            $data = [
                                'title' => $notificationText,
                                'data' => CommonHelper::setDataForNotification($ticketProcureRequestObj, 'request', $notificationType),
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("createByUser service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                }
                            }

                            // send email to all PAB Member for new request
                            try {
                                if (config('mail.service_enabled') && !empty($creators)) {
                                    Log::info("Ticket create email: " . json_encode($creators));
                                    Log::info("Ticket create email cc: " . json_encode($alertnotify));
                                    Mail::to($creators)->cc($alertnotify)->queue(new TicketApproverInfo($ticketProcureRequestObj, $pab));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }

                            $return["status"] = "success";
                            $return["msg"] = trans('content.service_ticket_fields.new_ticket_approval', ['id' => $ticketProcureRequestObj->id]);

                            $history = new TicketRequestHistory();
                            $history->user_id = $authCreatorUser->id;
                            $history->pr_id = $ticketProcureRequestObj->id;
                            $history->change_info = $return["msg"];
                            $history->save();

                            DB::commit();
                            unset($input["content"]);
                            Log::info("create SR API id:" . $ticketProcureRequestObj->id . " user_id:" . $authCreatorUser->id . " : " . json_encode($request->all()));
                            return response()->json($return);
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::channel('ticket')->error("createByUser SR API: " . $e->getMessage());
                    $return['msg'] = $e->getMessage();
                    return response()->json($return);
                }
            }

            $config = Config::first();
            $config->setWeekEnds();
            $current_datetime = Carbon::now(config('app.timezone'));

            $tagIds = [];
            if(isset($request['tags']) && count(json_decode($request['tags'])) > 0) {
                foreach(json_decode($request['tags']) as $tag) {
                    $tagCheck = Tags::where("id", $tag)->first();
                    if(isset($tagCheck) && $tagCheck->count() > 0) {
                        array_push($tagIds, $tagCheck->id);
                    } else {
                        $tagCreate = Tags::create(["tags" => $tag]);
                        array_push($tagIds, $tagCreate->id);
                    }
                }
            }
            $tags = implode(',', $tagIds);
            $request['tags'] = $tags;

            $data = $request->only("department_id", "problem_category_id", "subject", "device_id", "tags");
            if(isset($request->id) && $request->id != "") {
                $st = Ticket::find($request->id);
                $ticketIdAttachmentUpdate = true;
            }
            
            if(!isset($st) && empty($st)) {
                $st = new Ticket();
            }
            $st->fill($data);
            if(in_array(config('app.client'), ["ril"])) {
                $st->subject = $subject;
                $st->content = $content;
            }
            $processResult = Attachment::processForB64Imgs($input['content'], $st->id, $authCreatorUser->id);
            // Log::info("setting content:" . json_encode($processResult['content']));
            if($processResult['content'] != "undefined") {
                $st->content = $processResult['content'];
            }
            if(isset($request->userLocation) && $request->userLocation != null) {
                $st->seat_no = $request->userLocation;
            }
            $st->sub_category_id = $sub_category ? $sub_category->id : null;
            $st->is_temp = null;
            $st->creator_id = $authCreatorUser->id;
            $st->created_by = $authCreatorUser->id;
            $st->created_via = isset($request->ticketFromML) && $request->ticketFromML == 1 ? 6 : 4;
            $otherLocation = Location::where('name', 'like', 'Other')->first();
            if(empty($otherLocation)) {
                $otherLocation = new Location();
                $otherLocation->name = $otherLocation->address = $otherLocation->city = $otherLocation->state = "Other";
                $otherLocation->country = "IN";
                $otherLocation->country_id = 101;
                $otherLocation->state_id = 4008;
                $otherLocation->city_id = 133024;
                $otherLocation->currency = "INR";
                $otherLocation->user_id = $authCreatorUser->id;
                $otherLocation->save();
            }
            $st->location_id = ($authCreatorUser->location_id == "") ? $otherLocation->id : $authCreatorUser->location_id;
            $st->created_at = $current_datetime->format('Y-m-d H:i:s');
            $st->priority_id = $st->problemCategory && $st->problemCategory->priority_id ? $st->problemCategory->priority_id : 3;

            /* check sub-category's priority configuration */
            if($sub_category && $sub_category->priority_id) {
                $st->priority_id = $sub_category->priority_id;
            }

            // $st->tat = $st->priority->service_time;

            if($sub_category && $sub_category->tat) {
                $st->tat = $sub_category->tat;
            }
            else if($st->problemCategory->tat){
                $st->tat = $st->problemCategory->tat;
            }
            else{
                $st->tat = $st->priority->service_time;
            }
            $taskTat = app(RequestController::class)->getMaxTatOfTask($st);
            if ((int)$st->tat < $taskTat) {
                $return["msg"] = 'Selected Category TAT must be greater than or equal to the task TAT: ' . $taskTat;
                return response()->json($return);
            }
            $holidays = Holiday::getHolidaysFrom($current_datetime->format('Y-m-d'));

            $st->tat_expire = $config->calculateAdvancedTat($st->tat, $current_datetime, $holidays);
            $st->status_id = 1;
            $st->assignByHirarchy();

            // custom field code start

            if(count($custom_fields)) {
                foreach($custom_fields as $f) {
                    $st->{$f} = isset($customData[$f]) ? $customData[$f] : null;
                }
            }
            // custom field code end
            $st->save();
            $tkt_detail = new TktDetail();
            if ($st->created_via == 6) {
                $seatNo = isset($st->creator->userDetails->seat_no) ? $st->creator->userDetails->seat_no : null;
            } elseif (isset($request->seat_no)) {
                $seatNo = $request->seat_no;
            } else {
                $seatNo = null;
            }
            $tkt_detail->ticket_id =  $st->id;
            $tkt_detail->seat_no = $seatNo;
            $tkt_detail->save();
            if(!isset($ticketIdAttachmentUpdate)){
            /**update ticket_id attachment */
                if(isset($request->tmp_id) && $request->tmp_id!='null'){
                    $attachments = Attachment::where("tmp_id", $request->tmp_id)->select("id", "ticket_id")->get();
                    if(isset($attachments)) {
                        foreach ($attachments as $attachment) {
                            $attachment->ticket_id = $st->id;
                            $attachment->update();
                        }
                    }
                }
                if(isset($processResult['tmp_id']) && $processResult['tmp_id'] !='null') {
                    $attachments = Attachment::where("tmp_id", $processResult['tmp_id'])->select("id", "ticket_id")->get();
                    foreach ($attachments as $attachment) {
                        $attachment->ticket_id = $st->id;
                        $attachment->update();
                    }
                }
            }

            $acfe = AutoCreationAccount::where('auto_create_from_email', 1)->where('id', $st->department->tkt_auto_creation_id)->first();
            $st->ac_email_id = (!empty($acfe)) ? $acfe->id : 0;
            $st->save();
            $st->original_ticket_reference = $st->id ;
            $st->save();

            $alertnotify = null;
            if(Settings::first()->alerts_enabled == 1){
                $alertnotify = CommonHelper::getGlobalAlertEmail();
            }

            $user = User::find($st->creator_id);
            $coeEmails = $st->vipTKtCeoEmail($user); 
            $cc_emails = array_merge($cc_emails ?? [], $coeEmails);
            $cc_emails = array_values(array_unique(array_filter($cc_emails, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            })));
            if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    if(Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                        $unique_alertnotify = array_unique($alertnotify);
                        if (isset($cc_emails) && is_array($cc_emails)) {
                            $unique_alertnotify = array_diff($alertnotify, $cc_emails);
                        }
                        foreach ($unique_alertnotify as $email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $cc_emails[] = $email;
                            }
                        }
                        Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($st, $user));
                    } else {
                        Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($st, $user));
                    }
                }catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            // send push notification
            $notify_people = [];
            array_push($notify_people, $user->id);
            $notificationText = "New Ticket #".$st->id." has been created successfully!";
            $tkt_config = Config::first();
            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
            $data = [
                'title' => $notificationText,
                'data' => CommonHelper::setDataForNotification($st, 'ticket', $notificationType),
                'notify' => $notify_people,
            ];
            $sendNotifications = CommonHelper::sendPushNotification($data);
            if($sendNotifications != false) {
                $response = json_decode($sendNotifications);
                if(isset($response->failure) && $response->failure == 1) {
                    Log::error("createByUser service ticket id push notification:" . $st->id. " notification error " .json_encode($response));
                }
            }

            $handler = User::find($st->assigned_to);
            $dep = Department::find($st->department_id);
            $dephandler = User::find($dep->attender_id);
            /* notification to creator */
            $notification_text = 'New ticket has been created';
            $notify_people = Privilege::getHandlersByDepartment($st->department_id);
            // Notification::makeTicketNotification($st, $notify_people, $notification_text, $authCreatorUser->id);

            // send push notification to all technician which are in this pipeline
            $notify_peoples = [];
            if(isset($st->assigned_to) && $st->assigned_to != NULL){
                array_push($notify_peoples, $st->assigned_to);
            } else {
                $notify_peoples = Privilege::getHandlersByDepartment($st->department_id);
            }
            $notify_people = $notify_peoples;
            $notificationTicketText = "New Ticket #".$st->id." has been created successfully!";
            $tkt_config = Config::first();

            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
            $dataT = [
                'title' => $notificationTicketText,
                'data' => CommonHelper::setDataForNotification($st, 'ticket', $notificationType),
                'notify' => $notify_people,
            ];
            $sendNotifications = CommonHelper::sendPushNotification($dataT);
            $sendNotifications = CommonHelper::sendWhatsappNotification($dataT);
            if($sendNotifications != false) {
                $response = json_decode($sendNotifications);
                if(isset($response->failure) && $response->failure == 1) {
                    Log::error("create service ticket id push notification:" . $st->id. " notification error " .json_encode($response));
                }
            }

            if($st->assigned_to) {
                $user = User::find($st->assigned_to);
                if(config('mail.service_enabled') && $user && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                            Mail::to($user->email)->cc($alertnotify)->queue(new IntimateAssigned($st, $user));
                        }
                        else {
                            Mail::to($user->email)->queue(new IntimateAssigned($st, $user));
                        }
                    } catch (\Exception $ex) {
                        Log::error($ex->getMessage());
                    }
                }
            }
            else {
                if(config('mail.service_enabled') && $dephandler && $dephandler->email && filter_var($dephandler->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                            Mail::to($dephandler->email)->cc($alertnotify)->queue(new IntimateAssigned($st, $dephandler));
                        }
                        else {
                            Mail::to($dephandler->email)->queue(new IntimateAssigned($st, $dephandler));
                        }
                    }
                    catch(\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            /** Code is for add new ticket functionality in ticket history */
            $tkt_update['ticket_id'] = $st->id;
            $tkt_update['updated_by'] = $authCreatorUser->id;
            $tkt_update['action_type'] = 14;
            $tkt_update['tat'] = $st->tat;
            CommonHelper::ticketStatusHistory($tkt_update, $st);
            /** Code ends here */

            /** Code is for adding assigned ticket functionality in ticket history */
            if ($st->assigned_to) {
                $user = User::find($st->assigned_to);
                $tkt_update['ticket_id'] = $st->id;
                $tkt_update['updated_by'] = 0;
                $tkt_update['assigned_to'] = $user->id;
                $tkt_update['action_type'] = 1;
                $tkt_update['tat'] = $st->tat;
                CommonHelper::ticketStatusHistory($tkt_update);
            }
            /** Code ends here */
            $triggerObj = TicketTrigger::where('status', 1)->count();
            if($triggerObj > 0) {
                event(new TicketCreated($st));
            }
            app(RequestController::class)->createTasksFromCategory($st);

            //Auto reply setting
            $autoUpdateConfig = TKTAutoUpdateSetting::first();
            if(!empty($autoUpdateConfig) && isset($autoUpdateConfig->auto_response_for_ticket) && $autoUpdateConfig->auto_response_for_ticket == 1) {
                ProcessAutoResponse::dispatch($st->id, $st->creator_id);
            }

            $return["status"] = "success";
            $return["msg"] =  trans('content.service_ticket_fields.new_ticket', ['id' => $st->id]);
            $return["ticket_id"] = $st->id;
            unset($input["content"]);
            Log::info("createByUser not privileged user service ticket id:" . $st->id. " user_id:" . $authCreatorUser->id . " : " . json_encode($input));
            DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            $return['msg'] = $e->getMessage();
            Log::error("createByUser: " . $e->getMessage());
            return response()->json($return);
        }
    }

    // create new ticket by logged in user
    public function create(Request $request) {
        $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.unable_to_create_new')];
        $input = $request->all();
        $rules = [
            // 'id' => [
            //     'required',
            //     Rule::exists('tkt_tickets')->where(function($q) use($request) {
            //         $q->where('id', '=', $request->input('id'));
            //         $q->where('is_temp', '=', 1);
            //     })
            // ],
            'creator_id' => 'nullable|exists:users,id',
            'subject' => 'required|min:3|max:500|clean_text_only',
            'content' => 'required|min:3|max:500',
            'department_id' =>'required|exists:departments,id',
            'problem_category_id' =>'required|exists:tkt_problem_categories,id',
            'priority_id' =>'nullable|exists:tkt_priorities,id',
            'device_id' => 'nullable|sometimes|exists:assets,id',
            'tat' => 'integer|min:0',
            'ac_email_id' => 'sometimes|nullable|exists:tkt_auto_creation_accounts,id',
            'follow_cc' => 'sometimes|nullable|integer|min:0|max:1',
            'cc_emails' => 'sometimes|nullable|string|max:1000'
        ];
        $msg = [
            'department_id.required' => 'Please select Department.',
            'priority_id.required' => 'Please select Priority'
        ];
        // custom field validation start
        $data_fields = $request->input("fields", null);
        $custom_fields = [];
        $customData = [];
        $deparCustomObj = Department::where('id', $request->department_id)->withTrashed()->first();
        if(!empty($deparCustomObj)) {
            if (isset($deparCustomObj->customFieldset->fields) && !empty($deparCustomObj->customFieldset->fields)) {
                foreach ($deparCustomObj->customFieldset->fields as $f) {
                    $col_name = $f->nameToColumn();
                    $rules[$col_name] = $f->pivot->required && !isset($data_fields[$col_name]) ? 'required|string|max:255' : 'nullable|string|max:255';
                    $customData[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                    $custom_fields[] = $col_name;
                }
            }
        }
        // custom field validation end

        $validator = Validator::make($request->all(), $rules, $msg);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $enableUsbRequest = ProblemCategory::enableUsbRequest([$deparCustomObj->id]);
        if(!empty($enableUsbRequest) && config('app.client') == "ltts"){
            if(!isset($request->device_id) && in_array($request->problem_category_id,$enableUsbRequest)){
                $return["msg"] = trans('Device field is required');
                return response()->json($return);
            }
        }

        DB::beginTransaction();
        try {
            if(!isset($request->creator_id) || $request->creator_id == null){
                $input['creator_id'] = Auth::user()->id;
                $request->merge(['creator_id' => Auth::user()->id]);
            }
            if (Auth::user()->getRoleNames()->first() === 'User' && 
                isset($request->creator_id) && $request->creator_id != Auth::id()) {   
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not allowed to create a ticket on behalf of another user.',
                ]);
            }
            $problemManagementApicall = false; 
            if (config('app.client') === 'ltts') {
                $enableUsbRequest_old = [];

                $department = Department::find($request->department_id);

                if ($department && ($department->name === "IT Service Request" || $department->name === "IT Service Request Overseas")) {
                    $enableUsbRequest_old = ProblemCategory::enableUsbRequest([$department->id]);
                }
                $enableUsbRequests_new = ProblemCategory::where('privilege_access', 1)->pluck('id')->toArray();
                $enableUsbRequests = array_merge($enableUsbRequest_old, $enableUsbRequests_new);
                $enableCategory = array_values(array_unique($enableUsbRequests));

                if (in_array($request->problem_category_id, $enableCategory) || in_array($request->sub_category_id, $enableCategory)) {
                    $problemManagementApicall = true;
                }
            }
            if (!empty($request->device_id)) {
                $isDeviceFound = CommonController::existsInDropdown([DeviceController::class, 'getUserDeviceForDropDown'],
                    ['user_id' => $request->creator_id,'problemManagementApiCall' => $problemManagementApicall],
                    'id',$request->device_id);
                if (!$isDeviceFound) {
                    return response()->json([
                        "status" => "fail",
                        "msg" => "The selected device is not assigned or not available."
                    ]);
                }
            }
            /* check problem category has sub-categories */
            $problem_category = ProblemCategory::find($request->problem_category_id);
            $sub_category = false;
            if($problem_category->department_id != $request->department_id) {
                if (isset($request->ticketFromML) && $request->ticketFromML == 1) {
                    $deptRequest = new \Illuminate\Http\Request([
                        'name' => 'To be assigned'
                    ]);
                    $departmentController = new DepartmentApiController();
                    $departmentResponse = $departmentController->getOrCreate($deptRequest);
                    $departmentData = $departmentResponse->getData();

                    if (!empty($departmentData->department->id)) {
                        $request->merge([
                            'department_id' => $departmentData->department->id
                        ]);
                    }

                    $catRequest = new \Illuminate\Http\Request([
                        'name'  => 'New Ticket',
                        'department_id'  => $request->department_id,
                        'parent_id'      => null 
                    ]);
                    $problemCategoryController = new ProblemCategoryController();
                    $categoryResponse = $problemCategoryController->getOrCreateProblemCategory($catRequest);
                    $categoryData = $categoryResponse->getData();

                    if (!empty($categoryData->category->id)) {
                        $request->merge([
                            'problem_category_id' => $categoryData->category->id,
                        ]);
                        $problem_category = ProblemCategory::find($categoryData->category->id);
                    }
                }else{
                    $return["msg"] = trans('content.service_ticket_fields.Please_choose_the_valid_department');
                    return response()->json($return);
                }
            }
            if ($problem_category->totSubCategories() > 0) {

                if (!$request->sub_category_id) {
                    if (isset($request->ticketFromML) && $request->ticketFromML == 1) {
                        $return["msg"] = "You can not create ticket please use the portal.";
                        return response()->json($return);
                    }else{
                        $return["msg"] = trans('content.service_ticket_fields.Please_enter_valid_sub-category');
                        return response()->json($return);
                    }

                }

                try {
                    if(isset($request->sub_category_id) && $request->sub_category_id != null){
                    $sub_category = ProblemCategory::findOrFail($request->sub_category_id);
                    if ($sub_category->parent_id != $request->problem_category_id) {
                            if (isset($request->ticketFromML) && $request->ticketFromML == 1) {
                                $return["msg"] = "You can not create ticket please use the portal.";
                                return response()->json($return);
                            }else{
                                throw new \Exception(trans('content.service_ticket_fields.Enter_valid_sub-category'));
                            }
                        }
                    }
                } catch(\Exception $e) {
                    $return["msg"] = $e->getMessage();
                    return response()->json($return);
                }
            }
            if(in_array(config('app.client'), ["ril"])) {
                $dep = Department::find($request->department_id);
                $subject = $dep->name . " - " . $problem_category->name;
                if(isset($sub_category) && !empty($sub_category)) {
                    $subject = $dep->name . " - " . $problem_category->name . " - " . $sub_category->name;
                }
                $content = $subject;
                if(isset($request->additionalInfo) && $request->additionalInfo != 0) {
                    $content = $request->additionalInfo;
                    Log::info("additionalInfo:" . json_encode($content));
                } else {
                    $content = $subject;
                }
            }
            if ($request->creator_id) {
                $usr = User::find($request->creator_id);
                if (empty($usr)) {
                    $return["msg"] = trans('content.service_ticket_fields.Chosen_User_is_not_found');
                    return response()->json($return);
                }
                if ($usr->checkoutBasicClearance()) {
                    $return["msg"] = trans('content.service_ticket_fields.Chosen_User_is_not_in_Active_Status');
                    return response()->json($return);
                }
                if((!empty($sub_category) && $sub_category->hierarchy_approval == 4) || (!empty($problem_category) && $problem_category->hierarchy_approval == 4)) {
                   if (empty($usr) || empty($usr->manager) || $usr->manager->email == "") {
                        $return["msg"] = trans('content.service_ticket_fields.Manager_email_is_not_found');
                        return response()->json($return);
                    }
                }
                if ((!empty($sub_category) && in_array(11, explode(',', $sub_category->hierarchy_approval))) || (!empty($problem_category) && in_array(11, explode(',', $problem_category->hierarchy_approval)))) {
                    if (empty($usr->department)) {
                        $return["msg"] = "Department is not set for ticket creator. Please set the department to continue.";
                        return response()->json($return);
                    }
                    if (empty($usr->department->department_head_id)) {
                        $return["msg"] = "Department head is not set as department head approval mode is selected.";
                        return response()->json($return);
                    }
                }
            }

            if ( ($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id == 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id == 0) ) {
                $return["msg"] = trans('content.service_ticket_fields.select_PAB');
                return response()->json($return);
            } elseif ( ($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id != 0) ) {
                try {
                    if(isset($request->sub_category_id) && $request->sub_category_id != "" && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) {
                        $problem_category = ProblemCategory::find($request->sub_category_id);
                    } else {
                        $problem_category = ProblemCategory::find($request->problem_category_id);
                    }
                    $st = Ticket::create(["creator_id" => $request->input("creator_id") ? $request->creator_id : Auth::user()->id, "created_by" => Auth::user()->id, "created_via" => isset($request->ticketFromML) && $request->ticketFromML == 1 ? 6 : 4]);
                    $tkt_detail = new TktDetail();
                    if ($st->created_via == 6) {
                        $seatNo = isset($st->creator->userDetails->seat_no) ? $st->creator->userDetails->seat_no : null;
                    } elseif (isset($request->seat_no)) {
                        $seatNo = $request->seat_no;
                    } else {
                        $seatNo = null;
                    }
                    $tkt_detail->ticket_id =  $st->id;
                    $tkt_detail->seat_no = $seatNo;
                    $tkt_detail->save();
                    $custom_fields = [];
                    $data_fields = $request->input("fields", null);
                    $deptObj = Department::where('id', $request->department_id)->withTrashed()->first();
                    if($problem_category->is_form_required == 1 && $problem_category->form_id !== 0) {
                        $requestForm = RequestedForm::where('form_id', $problem_category->form_id)->where("tmp_id", $request->tmp_id)->latest()->first();
                        if(empty($requestForm)) {
                            $encodedRequestID = $request->tmp_id;
                            $url = url('requested_form', $problem_category->form_id).'?q='.$encodedRequestID;
                            $return["msg"] = ("Please fill the requested form");
                            return response()->json($return);
                        }
                    }

                    if(!empty($deptObj) && $deptObj->customFieldset && count($deptObj->customFieldset->fields)) {
                        foreach($deptObj->customFieldset->fields as $f) {
                            $col_name = $f->nameToColumn();
                            // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                            $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                            $custom_fields[] = $col_name;
                        }
                    }
                    // custom field code start

                    if(count($custom_fields)) {
                        foreach($custom_fields as $f) {
                            $st->{$f} = isset($data[$f]) ? $data[$f] : null;
                        }
                    }
                    $otherLocation = Location::where('name', 'like', 'Other')->first();
                    if(empty($otherLocation)) {
                        $otherLocation = new Location();
                        $otherLocation->name = $otherLocation->address = $otherLocation->city = $otherLocation->state = "Other";
                        $otherLocation->country = "IN";
                        $otherLocation->currency = "INR";
                        $otherLocation->country_id = 101;
                        $otherLocation->state_id = 4008;
                        $otherLocation->city_id = 133024;
                        $otherLocation->user_id = Auth::user()->id;
                        $otherLocation->save();
                    }
                    $st->location_id = (Auth::user()->location_id == "") ? $otherLocation->id : Auth::user()->location_id;
                    $st->save();
                    // custom field code end
                    $input['id'] = $st->id;
                    $ticketProcureRequestObj = new TicketProcureRequest();
                    $ticketProcureRequestData = $ticketProcureRequestObj->getDataFields($input);
                    $ticketProcureRequestData['id'] = $input['id'];
                    $ticketProcureRequestData['ticket_id'] = $input['id'];
                    $ticketProcureRequestData['sub_category_id'] = $sub_category ? $sub_category->id : null;
                    if(!isset($request->priority_id)) {
                        $ticketProcureRequestData['priority_id'] = $problem_category && $problem_category->priority_id ? $problem_category->priority_id : 3;
                    }
                    if(!isset($request->tat)) {
                        if($sub_category && $sub_category->tat) {
                            $ticketProcureRequestData['tat'] = $sub_category->tat;
                        } else if($problem_category->tat) {
                            $ticketProcureRequestData['tat'] = $problem_category->tat;
                        } else {
                            $st = Ticket::find($input['id']);
                            $st->priority_id = $ticketProcureRequestData['priority_id'];
                            $ticketProcureRequestData['tat'] = $st->priority->service_time;
                        }
                    }

                    $pab_ids = explode(",", $problem_category->pab_id);
                    $pab = array();
                    foreach ($pab_ids as $key => $val) {
                        $pab[$val] = 0;
                    }
                    $checkPabMember = CommonHelper::checkPabMember($pab_ids,$request->creator_id);
                    if($checkPabMember['status'] == "fail"){
                        return $checkPabMember;
                    }
                    $ticketProcureRequestData['pab_id'] = json_encode($pab);
                    $ticketProcureRequestObj->fill($ticketProcureRequestData);
                    // $ticketProcureRequestObj->assignByHirarchy();
                    if ($ticketProcureRequestObj->creator_id) {
                        $creatorObj = User::where('id', $ticketProcureRequestObj->creator_id)->first();
                        $ticketProcureRequestObj->location_id = ($creatorObj->location_id == "") ? Auth::user()->location_id : $creatorObj->location_id;
                    } else {
                        $ticketProcureRequestObj->location_id = Auth::user()->location_id;
                    }

                    if ($ticketProcureRequestObj->save()) {
                        // update service request id for dynamic requested form
                        if(isset($request->tmp_id) && $request->tmp_id != 'null') {
                            $requestedForms = RequestedForm::select("id", "request_id")->where("tmp_id", $request->tmp_id)->latest()->first();
                            if (isset($requestedForms)) {
                                $requestedForms->request_id = $ticketProcureRequestObj->id;
                                $requestedForms->save();
                            }
                        }

                        /**update ticket_id attachment */
                        if(isset($request->tmp_id)) {
                            $attachments = Attachment::where("tmp_id", $request->tmp_id)->select("id", "ticket_id")->get();
                            if (isset($attachments)) {
                                foreach ($attachments as $attachment) {
                                    $attachment->ticket_id = $st->id;
                                    $attachment->update();
                                }
                            }
                        }
                        $processResult = Attachment::processForB64Imgs($input['content'], $st->id, Auth::user()->id);
                        if(isset($processResult['tmp_id'])) {
                            $attachments = Attachment::where("tmp_id", $processResult['tmp_id'])->select("id", "ticket_id")->get();
                            foreach ($attachments as $attachment) {
                                $attachment->ticket_id = $st->id;
                                $attachment->update();
                            }
                        }
                        $ticketProcureRequestObj->original_ticket_reference = $ticketProcureRequestObj->id ;
                        $ticketProcureRequestObj->fillRequestTag();
                        $acfe = AutoCreationAccount::where('auto_create_from_email', 1)->where('id', $ticketProcureRequestObj->department->tkt_auto_creation_id)->first();
                        $ticketProcureRequestObj->ac_email_id = (!empty($acfe)) ? $acfe->id : 0;
                        $pab_ids = explode(",", $problem_category->pab_id);
                        $pab = TicketPab::find($pab_ids[0]);
                        $pab_members_check = TicketPabMember::where('pab_id', '=', $pab->id)->where('user_id',$usr->id)->count();
                        if($pab_members_check > 0 ) {
                            $return['msg'] = trans('content.service_ticket_fields.creator_and_requester_no_same');
                            return response()->json($return);
                        }
                        $pab_members_minimum = TicketPabMember::where('pab_id', '=', $pab->id)->count();
                        if($pab->required_minimum_approvals > $pab_members_minimum && $pab->hierarchy_approval == 2) {
                            $return['msg'] = trans('content.service_ticket_fields.minimium_approval_count');
                            return response()->json($return);
                        }
                        // 8 = System Approval
                        if(!empty($pab) && $pab->hierarchy_approval == 8) {
                            $ticketProcureRequestObj = TicketProcureRequest::find($ticketProcureRequestObj->id);
                            $ticketProcureRequestObj->update([
                                "status_id" => 3,
                                "approved_at" => Carbon::now()
                            ]);

                            if(!$ticketProcureRequestObj->save()) {
                                return $return;
                            }

                            $return["status"] = "success";
                            $return["msg"] =  trans('content.service_ticket_fields.new_ticket_system_approval', ['id' =>  $ticketProcureRequestObj->id]);

                            $history = new TicketRequestHistory();
                            $history->user_id = Auth::user()->id;
                            $history->pr_id = $ticketProcureRequestObj->id;
                            $history->change_info = $return["msg"];
                            $history->save();

                            // send email to create new request for user
                            $user = User::find($ticketProcureRequestObj->creator_id);
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager =  User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    Log::info("CreationServiceRequest Mail: " . json_encode($user->email));
                                    Mail::to($user->email)->cc($cc_emails)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error("CreationServiceRequest Mail: " . $ex->getMessage());
                            }
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $user->id);
                            $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                            $tkt_config = Config::first();
                            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                            $data = [
                                'title' => $notificationText,
                                'data' => CommonHelper::setDataForNotification($ticketProcureRequestObj, 'request', $notificationType),
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("create service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                }
                            }
                        } else {
                            $ticketProcureRequestObj = TicketProcureRequest::find($ticketProcureRequestObj->id);
                            // 4 = Manager Approval
                            if (!empty($pab) && $pab->hierarchy_approval != 4 && $pab->hierarchy_approval != 9 && $pab->hierarchy_approval != 10 && $pab->hierarchy_approval != 11) {
                                if (!$pab || $pab->totMembers() < 1) {
                                    $return['msg'] = trans('content.service_ticket_fields.No_user_found_send_request');
                                    return $return;
                                }

                                app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);

                                $pab_members = [];
                                if ($pab->hierarchy_approval == 1) {
                                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                                } else if ($pab->hierarchy_approval == 5) {
                                    if ($ticketProcureRequestObj->location_id != null) {
                                        $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->where('location_id', $ticketProcureRequestObj->location_id)->orderBy('id', 'asc')->get();
                                        if(count($pab_members) == 0) {
                                            $return["msg"] = trans('content.service_ticket_fields.No_user_found_send_request');
                                            return response()->json($return);
                                        }
                                    } else {
                                        $return["msg"] = trans('content.service_ticket_fields.No_user_found_send_request');
                                        return response()->json($return);
                                    }
                                } else {
                                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                                }

                                foreach ($pab_members as $k => $member) {
                                    $approvalRequest = new TicketApprovalRequest();
                                    $approvalRequest->pr_id = $ticketProcureRequestObj->id;
                                    $approvalRequest->pab_id = $pab_ids[0];
                                    $approvalRequest->user_id = $member->user_id;
                                    $user = User::find($approvalRequest->user_id);
                                    $pab_member_emails[] = $user->email;
                                }
                                $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                $creators = array_filter($pro_not_mem);
                            }  elseif(!empty($pab) && $pab->hierarchy_approval == 11) { 
                                $usr = User::find($ticketProcureRequestObj->creator_id);
                                if (empty($usr->department)) {
                                    $return["msg"] = "Department is not set for user. Please set the department to continue.";
                                    return response()->json($return);
                                }
                                if (empty($usr->department->department_head_id)) {
                                    $return["msg"] = "Department head is not set as department head approval mode is selected.";
                                    return response()->json($return);
                                }
                            
                                app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);
                                $departmentHeadId = $usr->department->department_head_id;
                                $departmentHeadUser = User::find($departmentHeadId);
                                $creators = [$departmentHeadUser->email];
                            } else {
                                app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);

                                $creators = [$usr->manager->email];
                            }

                            $alertnotify = null;
                            if (Settings::first()->alerts_enabled == 1) {
                                $alertnotify = CommonHelper::getGlobalAlertEmail();
                            }

                            // send email to create new request for user
                            $user = User::find($ticketProcureRequestObj->creator_id);
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager =  User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    Log::info("Create new Service request sent mail: " . json_encode($user->email));
                                    Mail::to($user->email)->cc($cc_emails)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error("create request send mail to creator: " . $ex->getMessage());
                            }
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $user->id);
                            $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                            $tkt_config = Config::first();
                            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                            $data = [
                                'title' => $notificationText,
                                'data' => CommonHelper::setDataForNotification($ticketProcureRequestObj, 'request', $notificationType),
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("create service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                }
                            }

                            // send email to all PAB Member for new request
                            try {
                                if (config('mail.service_enabled') && !empty($creators)) {
                                    Log::info("Ticket create email: " . json_encode($creators));
                                    Log::info("Ticket create email cc: " . json_encode($alertnotify));
                                    Mail::to($creators)->cc($alertnotify)->queue(new TicketApproverInfo($ticketProcureRequestObj, $pab));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }

                            $return["status"] = "success";
                            $return["msg"] = trans('content.service_ticket_fields.new_ticket_approval', ['id' => $ticketProcureRequestObj->id]);

                            $history = new TicketRequestHistory();
                            $history->user_id = Auth::user()->id;
                            $history->pr_id = $ticketProcureRequestObj->id;
                            $history->change_info = $return["msg"];
                            $history->save();
                            DB::commit();
                            return response()->json($return);
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    $return['msg'] = $e->getMessage();
                    Log::channel('ticket')->error("create SR API: " . $e->getMessage());
                    return response()->json($return);
                }
            }

            $config = Config::first();
            $config->setWeekEnds();

            $current_datetime = Carbon::now(config('app.timezone'));
            $holidays = Holiday::getHolidaysFrom($current_datetime->format('Y-m-d'));

            $tagIds = [];
            if(isset($request['tags']) && count(json_decode($request['tags'])) > 0) {
                foreach(json_decode($request['tags']) as $tag) {
                    $tagCheck = Tags::where("id", $tag)->first();
                    if(isset($tagCheck) && $tagCheck->count() > 0) {
                        array_push($tagIds, $tagCheck->id);
                    } else {
                        $tagCreate = Tags::create(["tags" => $tag]);
                        array_push($tagIds, $tagCreate->id);
                    }
                }
            }
            $tags = implode(',', $tagIds);
            $request['tags'] = $tags;
            $data = $request->only("creator_id", "department_id", "problem_category_id", "priority_id", "tat", "subject", "device_id", "tags", "created_via");
            if(isset($request->id) && $request->id != "") {
                $st = Ticket::find($request->id);
                $ticketIdAttachmentUpdate = true;
            }
            if(!isset($st) && empty($st)) {
                $st = new Ticket();
            }
            $st->fill($data);
            $st->ac_email_id = $request->has('ac_email_id') && $request->ac_email_id ? $request->ac_email_id : null;

            $processResult = Attachment::processForB64Imgs($input['content'], $st->id, Auth::user()->id);
            Log::info("setting content:" . json_encode($processResult['content']));
            if($processResult['content'] != "undefined") {
                $st->content = $processResult['content'];
            }
            if(isset($request->userLocation) && $request->userLocation != null) {
                $st->seat_no = $request->userLocation;
            }
            $st->content = $processResult['content'];
            if(in_array(config('app.client'), ["ril"])) {
                $st->subject = $subject;
                $st->content = $content;
            }
            $st->sub_category_id = $sub_category ? $sub_category->id : null;
            $st->is_temp = null;
            $st->created_by = Auth::user()->id;
            $taskTat = app(RequestController::class)->getMaxTatOfTask($st);
            if ((int)$st->tat < $taskTat) {
                $return["msg"] = 'Entered TAT must be greater than or equal to the task TAT: ' . $taskTat;
                return response()->json($return);
            }
            if ($request->creator_id) {
                $creatorObj = User::where('id', $request->creator_id)->first();
                $st->location_id = ($creatorObj->location_id == "") ? Auth::user()->location_id : $creatorObj->location_id;
            } else {
                $st->location_id = Auth::user()->location_id;
            }

            $st->created_at = $current_datetime->format('Y-m-d H:i:s');
            $st->tat_expire = $config->calculateAdvancedTat($st->tat, $current_datetime, $holidays);
            $st->status_id = 1;
            $st->created_via = isset($request->ticketFromML) && $request->ticketFromML == 1 ? 6 : 4;
            if(! $request->has('tat')) {
                $st->priority_id = $st->problemCategory && $st->problemCategory->priority_id ? $st->problemCategory->priority_id : 3;
                if($sub_category && $sub_category->tat) {
                    $st->tat = $sub_category->tat;
                } else if($st->problemCategory->tat) {
                    $st->tat = $st->problemCategory->tat;
                } else {
                    $st->tat = $st->priority->service_time;
                }
            }
            $st->assignByHirarchy();

            // custom field start
            if(count($custom_fields)) {
                foreach($custom_fields as $f) {
                    $st->{$f} = isset($customData[$f]) ? $customData[$f] : null;
                }
            }
            // custom field end
            $st->save();
            $tkt_detail = new TktDetail();
            if ($st->created_via == 6) {
                $seatNo = isset($st->creator->userDetails->seat_no) ? $st->creator->userDetails->seat_no : null;
            } elseif (isset($request->seat_no)) {
                $seatNo = $request->seat_no;
            } else {
                $seatNo = null;
            }
            $tkt_detail->ticket_id =  $st->id;
            $tkt_detail->seat_no = $seatNo;
            $tkt_detail->save();
            $st->original_ticket_reference = $st->id;
            $st->save();
            if(!isset($ticketIdAttachmentUpdate)) {
                /**update ticket_id attachment */
                $attachments = Attachment::where("tmp_id", $request->tmp_id)->select("id", "ticket_id")->get();
                if (isset($attachments)) {
                    foreach ($attachments as $attachment) {
                        $attachment->ticket_id = $st->id;
                        $attachment->update();
                    }
                }
                if(isset($processResult['tmp_id'])) {
                    $attachments = Attachment::where("tmp_id", $processResult['tmp_id'])->select("id", "ticket_id")->get();
                    foreach ($attachments as $attachment) {
                        $attachment->ticket_id = $st->id;
                        $attachment->update();
                    }
                }
            }

            $alertnotify = null;
            if (Settings::first()->alerts_enabled == 1) {
                $alertnotify = CommonHelper::getGlobalAlertEmail();
            }

            $handler = User::find($st->assigned_to);
            $dep = Department::find($st->department_id);
            $dephandler = User::find($dep->attender_id);

            $user = User::find($st->creator_id);

            /* notification to creator */
            $notification_text = 'New ticket has been created';
            $notify_people = Privilege::getHandlersByDepartment($st->department_id);
            // Notification::makeTicketNotification($st, $notify_people, $notification_text, $st->creator_id);
            $coeEmails = $st->vipTKtCeoEmail($user); 
            $cc_emails = array_merge($cc_emails ?? [], $coeEmails);
            $cc_emails = array_values(array_unique(array_filter($cc_emails, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            })));
            if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                        $unique_alertnotify = array_unique($alertnotify);
                        if (isset($cc_emails) && is_array($cc_emails)) {
                            $unique_alertnotify = array_diff($alertnotify, $cc_emails);
                        }
                        foreach ($unique_alertnotify as $email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $cc_emails[] = $email;
                            }
                        }
                        Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($st, $user));
                    } else {
                        Mail::to($user->email)->cc($cc_emails)->queue(new IntimateSuccessCreation($st, $user));
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            // send push notification
            $notify_people = [];
            array_push($notify_people, $user->id);
            $notificationText = "New Ticket #".$st->id." has been created successfully!";
            $tkt_config = Config::first();
            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
            $data = [
                'title' => $notificationText,
                'data' => CommonHelper::setDataForNotification($st, 'ticket', $notificationType),
                'notify' => $notify_people,
            ];
            $sendNotifications = CommonHelper::sendPushNotification($data);
            if($sendNotifications != false) {
                $response = json_decode($sendNotifications);
                if(isset($response->failure) && $response->failure == 1) {
                    Log::error("create service ticket id push notification:" . $st->id. " notification error " .json_encode($response));
                }
            }

            // send push notification to all technician which are in this pipeline
            $notify_peoples = [];
            if(isset($st->assigned_to) && $st->assigned_to != NULL){
                array_push($notify_peoples, $st->assigned_to);
            } else {
                $notify_peoples = Privilege::getHandlersByDepartment($st->department_id);
            }
            $notify_people = $notify_peoples;
            $notificationTicketText = "New Ticket #".$st->id." has been created successfully!";
            $tkt_config = Config::first();

            $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
            $dataT = [
                'title' => $notificationTicketText,
                'data' => CommonHelper::setDataForNotification($st, 'ticket', $notificationType),
                'notify' => $notify_people,
            ];
            $sendNotifications = CommonHelper::sendPushNotification($dataT);
            $sendNotifications = CommonHelper::sendWhatsappNotification($dataT);
            if($sendNotifications != false) {
                $response = json_decode($sendNotifications);
                if(isset($response->failure) && $response->failure == 1) {
                    Log::error("create service ticket id push notification:" . $st->id. " notification error " .json_encode($response));
                }
            }          

            if ($st->assigned_to) {
                $user = User::find($st->assigned_to);
                if (config('mail.service_enabled') && $user && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        if (Config::requiredAlertSettingsEmail() && $alertnotify) {
                            Mail::to($user->email)->cc($alertnotify)->queue(new IntimateAssigned($st, $user));
                        } else {
                            Mail::to($user->email)->queue(new IntimateAssigned($st, $user));
                        }
                    } catch (\Exception $ex) {
                        Log::error($ex->getMessage());
                    }
                }
            } else {
                if (config('mail.service_enabled') && $dephandler && $dephandler->email && filter_var($dephandler->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        if (Config::requiredAlertSettingsEmail() && $alertnotify) {
                            Mail::to($dephandler->email)->cc($alertnotify)->queue(new IntimateAssigned($st, $dephandler));
                        } else {
                            Mail::to($dephandler->email)->queue(new IntimateAssigned($st, $dephandler));
                        }
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            /** Code is for add new ticket functionality in ticket history */
            $tkt_update['ticket_id'] = $st->id;
            $tkt_update['updated_by'] = Auth::user()->id;
            $tkt_update['action_type'] = 14;
            $tkt_update['tat'] = $st->tat;
            CommonHelper::ticketStatusHistory($tkt_update, $st);
            /** Code ends here */

            /** Code is for adding assigned ticket functionality in ticket history */
            if ($st->assigned_to) {
                $user = User::find($st->assigned_to);
                $tkt_update['ticket_id'] = $st->id;
                $tkt_update['updated_by'] = 0;
                $tkt_update['assigned_to'] = $user->id;
                $tkt_update['action_type'] = 1;
                $tkt_update['tat'] = $st->tat;
                CommonHelper::ticketStatusHistory($tkt_update);
            }
            /** Code ends here */
            app(RequestController::class)->createTasksFromCategory($st);
            $triggerObj = TicketTrigger::where('status', 1)->count();
            if($triggerObj > 0) {
                // Ticket generate apply trigger.
                event(new TicketCreated($st));
            }

            //Auto reply setting
            $autoUpdateConfig = TKTAutoUpdateSetting::first();
            if(!empty($autoUpdateConfig) && isset($autoUpdateConfig->auto_response_for_ticket) && $autoUpdateConfig->auto_response_for_ticket == 1) {
                $combined_text = $input['subject']. ' - ' . CommonHelper::sanitizeHtmlContentData($input['content']);
                $autoResponse = CommonHelper::searchAIResponse($combined_text);
                $formattedResponse = nl2br($autoResponse);
                $formattedResponse = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formattedResponse);
                $formattedResponse = preg_replace('/\n\s*\*\s*(.*?)\n/', '<li>$1</li>', $formattedResponse);
                $formattedResponse = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $formattedResponse);

                $commentReqObjArray = [
                    'id' =>  $st->id,
                    'comment' => $formattedResponse,
                    'tmp_id' => Str::random(10),
                    'user_id' => 0,
                    'auto_response' => 1
                ];
                $commentReqObj = new Request($commentReqObjArray);
                $this->addComment($commentReqObj);
            }

            $return["status"] = "success";
            $return["msg"] = trans('content.service_ticket_fields.new_ticket', ['id' => $st->id]);
            unset($input["content"]);
            Log::info("API create service ticket id:" . $st->id. " user_id:" . Auth::user()->id . " : " . json_encode($input));
            DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            $return['msg'] = $e->getMessage();
            Log::error("create ticket error API: " . $e->getMessage() . json_encode($input));
            return response()->json($return);
        }
    }

    /* to return the filter options for the ticket list page */
    public function listFilterOptions() {
        $return = ["status" => "fail", "msg" => "Unable to get filter options"];

        $compObj = Company::select("id", "name as text");
        if(!Auth::user()->isSuperUser() && !Settings::getSettings()->full_multiple_companies_support) {
            $compObj->where("id", "=", Auth::user()->company_id);
        }
        $companies = $compObj->get()->toArray();
        $statuses = Status::select("id","name")->get();
        $priorities = Priority::select("id","name","service_time")->get();
        $ticket_handlers = Ticket::ticketHandlers();
        $tkt_config = Config::select('eu_hide_priority', 'eu_hide_assigned_to', 'eu_hide_expire_at', 'eu_hide_tat', 'mail_all_status_changes')->first();

        $form = Auth::user()->hasPermission("service_tickets");
        $locations = Location::select("id","name")->get();

        /* action controls of current user */
        $action_controls = [];
        $get_action_controls = Auth::user()->ticketActionControl();
        if($get_action_controls && is_object($get_action_controls) && $get_action_controls->first()) {
            $action_controls = $get_action_controls->first()->only('ctrl_transfer', 'ctrl_assign', 'ctrl_delete', 'ctrl_self_assign', 'create_for_others', "ctrl_tat", "ctrl_priority");
        }

        $return['status'] = "success";
        $return['msg'] = '';
        $return['companies'] = $companies;
        $return['statuses'] = $statuses;
        $return['priorities'] = $priorities;
        $return['form'] = $form;
        $return['action_controls'] = $action_controls;
        $return['ticket_handlers'] = $ticket_handlers;
        $return['tkt_config'] = $tkt_config;
        $return['locations'] = $locations;
        $return['filter_by_dates_opts'] = Ticket::filterByDateOpts();
        return response()->json($return);
    }

    public function ajaxTickets(Request $request, $main_filter="my-tickets") {
        $return = [];
        $req = $request->all();

        $page = $request->index ? $request->index : 0;
        $take = $request->list_size ? $request->list_size : 20;
        $skip = $page * $take;
        $subquery = TktFollowing::select(DB::raw('MAX(tkt_followings.id)'))->join('tkt_tickets as tt', 'tt.id', '=', 'tkt_followings.ticket_id')->whereIn('tkt_followings.action_type', [2,4,5,6,7])->groupBy('tt.id')->get();
        $subQuery1 = Ticket::select('assigned_to')->groupBy('assigned_to');
        $subQuery2 = Ticket::select('creator_id')->groupBy('creator_id');

        $db = DB::table('tkt_tickets as t');
        $db->leftJoin('departments as dep', 't.department_id', '=', 'dep.id');
        $db->leftJoin('companies as comp', 'dep.company_id', '=', 'comp.id');
        $db->leftJoin('tkt_problem_categories as pc', 't.problem_category_id', '=', 'pc.id');
        $db->leftJoin('tkt_statuses as s', 't.status_id', '=', 's.id');
        $db->leftJoin('tkt_priorities as p', 't.priority_id', '=', 'p.id');
        $db->leftJoin('users as u', 't.creator_id', '=', 'u.id'); // ticket raiser
        $db->leftJoin('users as cu', 't.created_by', '=', 'cu.id'); // who actually created ticket
        $db->leftJoin('users as ta', 't.assigned_to', '=', 'ta.id'); // to whom ticket getting assigned
        $db->leftJoin('assets as dev', 't.device_id', '=', 'dev.id');
        $db->leftJoin('companies as creator_comp', 'u.company_id', '=', 'creator_comp.id');
        $db->leftJoin('locations as l', 't.location_id', '=', 'l.id');
        $db->leftJoin('locations as creator_loc', 'u.location_id', '=', 'creator_loc.id');
        $db->leftJoin('locations as ta_loc', 'ta.location_id', '=', 'ta_loc.id');
        $db->leftJoin('knowledge_document as doc', 't.id', '=','doc.ticket_id');
        $db->leftJoinSub($subQuery1, 'assignee', function ($join) {
            $join->on('t.assigned_to', 'assignee.assigned_to');
        });
        $db->leftJoinSub($subQuery2, 'creator', function ($join) {
            $join->on('t.creator_id', 'creator.creator_id');
        });
        $db->leftJoin('tkt_followings as tf', function($query) use($subquery) {
            $query->on('tf.ticket_id', '=', 't.id')
                ->whereIn('tf.action_type', [2,4,5,6,7])
                ->whereIn('tf.id', $subquery)
                ->where(function($q) {
                    $q->whereNull('is_note')->orWhere('is_note', '=', 0);
                });

        });


        $is_admin = Auth::user()->hasAnyRole(['Admin']);
        $possible_main_filters = ['assigned', 'not-assigned', 'my-tickets', 'closed', 'spam', 'all-tickets'];

        if(! in_array(strtolower($main_filter), $possible_main_filters)) {
            $return["status"] = "fail";
            $return["msg"] = "Invalid Url";
            return response()->json($return);
        }

        if($main_filter != "my-tickets") {
            $get_user_privileges = Privilege::select('department_id')->where('user_id', '=', Auth::user()->id)->get();
            $user_privileged_departments = [];
            if(count($get_user_privileges)) {
                $user_privileged_departments = $get_user_privileges->pluck('department_id');                
                $db->whereIn('t.department_id', $user_privileged_departments);
            }
            else {
                $db->whereNull('t.id');
            }
        }

        if(! Auth::user()->isSuperUser()) {
            $db->where("dep.company_id", "=", Auth::user()->company_id);
        }

        $db->whereNull("t.is_temp");
        $db->whereNull("t.deleted_at");
        
        if($main_filter == "closed") {
            $title = "Closed Tickets";
            $db->where("t.status_id", "=", 6);
        }
        elseif($main_filter != "all-tickets") {
            $db->where("t.status_id", "!=", 6);
            if($main_filter == "assigned") {
                $db->where('t.assigned_to', '=', Auth::user()->id);
            }
            elseif($main_filter == "not-assigned") {
                $db->whereNull('t.assigned_to');
            }
            elseif($main_filter == "my-tickets") {
                $db->where('t.creator_id', '=', Auth::user()->id);
            }
            elseif($main_filter == "spam") {
                $db->where('t.spam', '=', 1);
            }
        }

        $db->select('t.id', 't.subject', 't.created_at', 't.resolved_at', 's.name as status', 't.status_id', 'u.id as creator_id', 't.problem_category_id', 't.sub_category_id', 'p.name as priority', 'p.id as priority_id', 't.tat', 'dep.name as dep_name', 'comp.name as comp_name', 't.feedback', 't.device_id', 't.starred', 't.spam','creator_comp.name as create_comp_name','u.job_type', 'u.ex_user_company', 't.created_via', 'creator_loc.name as creator_location', 't.department_id', 't.merge_primary', 't.is_merge_primary', 't.creator_id', 't.assigned_to', 't.created_at','t.tags', 'doc.ticket_id as article_ticket', 't.tat_expire', 't.tat_remaining_mins', 'pc.name as prob_cat_name', 'dev.asset_tag', 't.location_id', 'l.name as location_name','t.ticket_type');
        $db->addSelect(DB::raw('concat(u.first_name, " ", u.last_name, " @ ", u.username) as creator_name'));
        $db->addSelect(DB::raw('concat(cu.first_name, " ", cu.last_name, " @ ", cu.username) as created_by_name'));
        $db->addSelect(DB::raw('case when t.assigned_to is not null then concat(ta.first_name, " ", ta.last_name, " @ ", ta.username) else "" end as assigned_to_name'));
        $db->addSelect(DB::raw('case when t.assigned_to is not null then ta_loc.name else "" end as assigned_to_loc'));
        $db->addSelect(DB::raw('DATE_FORMAT(t.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(t.created_at, "%d %b %Y %h:%i %p") as created_at_format'));
        $db->addSelect(DB::raw('case when t.tat_expire > now() then DATE_FORMAT(t.tat_expire, "%Y/%m/%d %H:%i:%s") else "Overdue" end as tat_expire_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(t.tat_expire, "%d %b %Y %h:%i %p") as tat_expire_date_format'));
        $db->addSelect(DB::raw('case when t.status_id in (5,6) then DATE_FORMAT(t.resolved_at, "%d %b %Y %h:%i %p") else "" end as resolved_at_format'));
        $db->addSelect(DB::raw('case when t.status_id = 6 then DATE_FORMAT(t.closed_at, "%d %b %Y %h:%i %p") else "" end as closed_at_format'));
        $db->addSelect(DB::raw('FIND_IN_SET(' . Auth::user()->id . ', t.starred) as is_starred'));
        $db->addSelect(DB::raw('case when u.job_type in (1,2) then u.ex_user_company else creator_comp.name end as creator_company'));
        $db->addSelect(DB::raw("( SELECT count(*) FROM tkt_tickets WHERE assigned_to = assignee.assigned_to AND t.status_id NOT IN (5,6) GROUP BY assigned_to) as total_assigned"));
        $db->addSelect(DB::raw("( SELECT count(*) FROM tkt_tickets WHERE creator_id = creator.creator_id AND status_id NOT IN (5,6) GROUP BY creator_id) as total_open"));
        $db->addSelect(DB::raw("( SELECT feedback FROM tkt_tickets WHERE creator_id = creator.creator_id AND status_id = 5 ORDER By resolved_at desc limit 1) as last_feedback"));
        $db->addSelect(DB::raw('concat(t.tat, "Hrs") as tat_hrs'));
        $db->addSelect(DB::raw('case when t.status_id = 5 then "green" when t.status_id = 6 then "green" when t.status_id = 2 then "red" when t.spam = 1 then "gray" when tf.updated_by IS NOT NULL AND tf.updated_by = t.assigned_to then "blue" when tf.updated_by IS NOT NULL AND tf.updated_by = t.creator_id then "rose" when (tf.updated_by IS NOT NULL AND tf.updated_by != t.creator_id) AND (tf.updated_by IS NOT NULL AND (tf.updated_by != t.assigned_to OR t.assigned_to IS NULL)) then "yellow" else "rose" end as color_code'));

        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        $is_searching = false;
        if(isset($req["filters"])) {
            $filters = $req["filters"];
            if(isset($filters["status"]) && $filters['status'] && $filters['status'] != "null") {
                $db->where("t.status_id", "=", (int) $filters['status']);
            }
            if(isset($filters["priority"]) && $filters['priority'] && $filters['priority'] != "null") {
                $db->where("t.priority_id", "=", (int) $filters['priority']);
            }
            if(isset($filters["tag"]) && $filters['tag'] && $filters['tag'] != "null") {
                $db->where("t.tags", "like", "%".$filters['tag']."%");
            }
            if(isset($filters["created_via"]) && $filters['created_via'] && $filters['created_via'] != "null") {
                $db->where('t.created_via', $filters['created_via']);
            }
            if(isset($filters["ticket_handler"]) && $filters['ticket_handler'] && $filters['ticket_handler'] != "null") {
                $db->where("t.assigned_to", "=", (int) $filters['ticket_handler']);
            }
            if(isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                $db->where("t.department_id", "=", (int) $filters['department']);
            }
            if(isset($filters["problem_category"]) && $filters['problem_category'] && $filters['problem_category'] != "null") {
                $db->where("t.problem_category_id", "=", (int) $filters['problem_category']);
            }
            if(isset($filters["device"]) && $filters['device'] && $filters['device'] != "null") {
                $db->where("t.device_id", "=", (int) $filters['device']);
            }
            if(isset($filters["sub_category"]) && $filters['sub_category'] && $filters['sub_category'] != "null") {
                $db->where("t.sub_category_id", "=", (int) $filters['sub_category']);
            }
            if(isset($filters["location_id"]) && $filters['location_id'] && $filters['location_id'] != "null") {
                $db->where("tkt_tickets.location_id", "=", (int) $filters['location_id']);
            }
            if(isset($filters["ticket_type"]) && $filters['ticket_type'] && $filters['ticket_type'] != "null") {
                $db->whereIn("tkt_tickets.ticket_type", $filters['ticket_type']);
            }
            if(isset($filters["date_period"]) && $filters["date_period"] && $filters["date_period"] != "null") {
                $date_period = CommonHelper::getDateAs($filters["date_period"], "Y-m-d", "d/m/Y");
                if($date_period) {
                    $whereStr = sprintf('(date(t.created_at) = "%1$s" or date(t.updated_at) = "%1$s")', $date_period);
                    $db->whereRaw($whereStr);
                }
            }

            $based_on_possible = ['1'=>'t.created_at', '2'=>'t.assigned_to', '3'=>'t.resolved_at'];
            if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 3 ) {
                if(isset($filters["from_date"]) && $filters["from_date"] && $filters["from_date"] != "null") {
                    $from_date = CommonHelper::getDateAs($filters["from_date"], "Y-m-d", "d/m/Y");
                    $to_date = CommonHelper::getDateAs($filters["to_date"], "Y-m-d", "d/m/Y");
                    if($from_date && $to_date) {
                        $whereStr = sprintf('(date(%1$s) >= "%2$s" and date(%1$s) <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                        $db->whereRaw($whereStr);
                    }
                }
            }
            if(isset($filters["based_on_cre_log"]) && $filters['based_on_cre_log'] && $filters['based_on_cre_log'] != "null") {
                if(isset($filters["ticket_creator"]) && $filters["ticket_creator"] && $filters["ticket_creator"] != "null") {
                    if($filters['based_on_cre_log'] == 1){
                        $db->where("t.created_by", "=", (int) $filters['ticket_creator']);
                    }
                    if($filters['based_on_cre_log'] == 2){
                        $db->where("t.creator_id", "=", (int) $filters['ticket_creator']);
                    }
                }
            }
            $is_searching = true;
        }

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            if(substr($search_key, 0, 1) == "#" && strlen($search_key) > 1) {
                $whereStr = sprintf('(t.id = "%1$s")', substr($search_key, 1));      
            }
            else {
                $whereStr = sprintf('(concat(tkt_tickets.tat, "Hrs") like "%%%1$s%%" or tkt_tickets.id like "%%%1$s%%" or tkt_tickets.subject like "%%%1$s%%" or dep.name like "%%%1$s%%" or comp.name like "%%%1$s%%" or s.name like "%%%1$s%%" or concat_ws(" ", u.first_name, u.last_name, "@", u.username) like "%%%1$s%%" or concat_ws(" ", cu.first_name, cu.last_name, "@", cu.username) like "%%%1$s%%" or concat_ws(" ", ta.first_name, ta.last_name, "@", ta.username) like "%%%1$s%%" or p.name like "%%%1$s%%" or DATE_FORMAT(tkt_tickets.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(tkt_tickets.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $is_searching = true;
            $return['search_key'] = $search_key;
        }
        
        if($is_searching) {
            $return['filter_record'] = $db->count();
        }

        $fields = [1=>'t.id', 2=>'creator_name', 3=>'s.name', '4'=>'priority_id', 5=>'assigned_to_name', 6=>'t.created_at', 7=>'t.updated_at', 8=>'is_starred', 9=>'t.tat_expire', 10=>'t.feedback'];
            if( isset($req["order"]["id"]) && array_key_exists($req["order"]["id"], $fields) && in_array($req["order"]["dir"], [1, 2]) ) {
                $dir = $req["order"]["dir"] == 1 ? "asc" : "desc";
                $db->orderBy( $fields[$req["order"]["id"]], $dir);
            }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;

        $db->skip($skip);
        $db->take($take);

        if(isset($req["toggle"])){
            $return["toggle"] = $req["toggle"];
        }

        $return["data"] = $db->get();
        $return["page"] = $page;
        $return["profile_imgs"] = [];
        foreach($return["data"] as $v) {
            if(in_array($v->status_id, [5,6]) && $v->feedback > 0) {
                $v->feedback_face = url("images/emo/" . $v->feedback . ".gif");
            }
            if(isset($return["profile_imgs"][$v->creator_id])) {
                continue;
            }
            $return["profile_imgs"][$v->creator_id] = User::find($v->creator_id);
        }

        return response()->json($return);
    }

    public function ticket($id) {

        $return = [];
        $return['status'] = 'success';
        $return['msg'] = '';

        $t = Ticket::find($id);

        if(! $t) {
            return response()->json([
                "status"    => 'fail',
                "msg"       => 'Ticket not found',
            ]);
        }

        $wai = $t->wai();
        $current_user = Auth::user();
        if( $wai == 0 && (!$current_user->department_id == "" || $t->department_id != $current_user->department_id) ) {
            $msg = !$current_user->department_id ? "Please update your department." : "You don't have privilege to access this ticket.";
            return response()->json([
                "status"    => 'fail',
                "msg"       => $msg,
            ]);
        }

        $compObj = Company::select("id", "name as text");
        if(!Auth::user()->isSuperUser() && !Settings::getSettings()->full_multiple_companies_support) {
            $compObj->where("id", "=", Auth::user()->company_id);
        }

        $return['wai'] = $t->wai();
        $return['access_privilege'] = $t->hasAccessPrivilege();
        $return['assigned_to'] = $t->assigned_to ? $t->assignedTo->shortInfo() : null;
        $return['creator'] = $t->creator->shortInfo();
        $return['companies'] = $compObj->get()->toArray();
        $return['statuses'] = Status::select("id","name")->whereNotIn("id", [6])->get();
        $return['statuses1'] = Status::select("id","name")->whereIn("id", [2,5])->get();
        $return['priorities'] = Priority::select("id","name","service_time")->get();
        $return['attachments'] = Attachment::where("ticket_id", $t->id)->select("id", "original_file_name as name")->whereNull("following_id")->get();

        $device = null;
        if($t->device_id) {
            $device = Device::find($t->device_id)->only('asset_tag');
        }
        $other_details = [];
        $other_details['created_at'] = $t->created_at->format('Y-m-d H:i:s');
        $other_details['updated_at'] = $t->updated_at->format('Y-m-d H:i:s');
        $other_details['company_name'] = $t->company->name;
        $other_details['department_name'] = $t->department->name;
        $other_details['prob_cat_name'] = $t->problemCategory->name;
        $other_details['priority_name'] = $t->priority->name;
        $other_details['status_name'] = $t->status->name;
        $other_details['created_by_name'] = $t->actualCreator->fullName();
        $other_details['assigned_to_name'] = $t->assigned_to ? $t->assignedTo->fullName() : null;

        $return['data'] = array_merge($t->only('id', 'department_id', 'problem_category_id', 'priority_id', 'status_id', 'tat', 'tat_expire', 'feedback', 'subject', 'content'), $other_details, ['tat_halt'=>$t->status->tat_halt], ['device'=>$device], ['self_star'=>$t->selfCheckStarred(), 'expire_info'=>$t->expireInfo()]);

        if(in_array($t->status_id, [5,6]) && $t->feedback > 0) {
            $t->feedback_face = url("images/emo/" . $t->feedback . ".gif");
        }

        $return['feedback'] = [];
        if($return['feedback'] != 24) {
            $return['feedback'] = $t->feedBackRating(true);
        }

        return response()->json($return);
    }

    public function cleanTicketReference($text) {
        // Match strings like "#23433", "SR23433", or "sr23433"
        if (preg_match('/^(#|sr|SR)?(\d+)/', $text, $matches)) {
            return $matches[2]; // Extract only the number part
        }

        return $text; // Return original if no match
    }

    // get the timeline for a ticket 
    public function getTimeline(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to refresh the ticket timeline"];
        if(isset($request->checkRequest) && $request->checkRequest == true) {
            $text = $request->id;
            $pattern = '/\d+/'; // Regular expression pattern to match one or more digits
            preg_match_all($pattern, $text, $matches);
            $numbers = $matches[0];
            if((!isset($numbers[0]))) {
                $return['msg'] = "Please enter valid id.";
                return $return;
            }
            $req = TicketProcureRequest::where('id',$numbers[0])->count();
            $ticket = Ticket::where('id',$numbers[0])->count();
            if(isset($req) && isset($ticket) && $req > 0 && $ticket > 0){
                $return['status'] = 'success';
                $return['msg'] = "request_available";
                return $return;
            };
        }

        if(isset($request->detailsOf) && $request->detailsOf != null && $request->detailsOf == 'request') {
            $con = new ApiRequestController();
            $con->getServiceRequestInfo($request, $request->id);
        }

        $ticketId = $this->cleanTicketReference($request->input('id'));
        $rules = [
            'id' => [
                'required',
                Rule::exists('tkt_tickets')->where(function($q) use($ticketId, $request) {
                    $q->where('id', '=', $ticketId);
                    $q->whereNull('is_temp');
                })
            ]
        ];

        $validator = Validator::make($request->only("id"), $rules);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $id = $request->input("id");
        try {
            $st = Ticket::findOrFail($id);
            $db = DB::table('tkt_followings as tf');
            $db->leftJoin('users as u', 'tf.updated_by', '=', 'u.id');
            $db->where('tf.ticket_id', '=', $id);
            $db->whereNull('tf.deleted_at');
            $db->whereNull('tf.action_type');

            if($st->wai() == 24) {
                $db->where('tf.is_note', '!=', 1);
            }

            $db->select('tf.remarks', 'tf.is_note', 'tf.id as tfid', 'tf.action_type', 'tf.merge_primary', 'tf.cc_emails');
            $db->addSelect(DB::raw('concat(u.first_name, " ", u.last_name, " @ ", u.username) as commenter'));
            $db->addSelect(DB::raw('DATE_FORMAT(tf.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
            $db->orderBy('tf.updated_at', 'asc');

            $tls = $db->get();
            if($tls && count($tls)) {

                $embedded_attachments = Attachment::getEmbeddedAttachments($st->id);

                foreach($tls as $tl) {
                    // $tl->remarks = str_ireplace("\n\n", "", $tl->remarks);
                    $tl->remarks = CommonHelper::renderTktContent($tl->remarks, $embedded_attachments);
                    $tl->attachments = Attachment::where('following_id', '=', $tl->tfid)->select('id', 'original_file_name as name', 'extension as ext', DB::raw('case when thumbnail is not null then 1 else 0 end as thumb'))->get();
                }
            }
            $custom_field = $CustomFieldDate = [];
            $checkCustomFiled = ProblemCategory::find($st->problem_category_id);
            if(isset($checkCustomFiled->custom_fieldset) && $checkCustomFiled->custom_fieldset != NULL) {
                $custom_field = $this->getTicketTypeFieldsetsAPI($checkCustomFiled->custom_fieldset, "CustomField");
                $CustomFieldDate =$this->getDayForEndDate($st->id, null);
                if($CustomFieldDate != 0) {
                    if(isset($st->custom_fields) && $st->custom_fields != null){
                        $CustomFieldDate = $CustomFieldDate;
                    }else{
                        $CustomFieldDate = [date("d-m-Y H:i"), $CustomFieldDate];
                    }
                }
                // $customFields = Ticket::startDateName(ProblemCategory::enableUsbRequest([$st->department_id]));
            }

            $return['data'] = $tls;
            $return["tkt_data"] = $st->only('id', 'department_id','problem_category_id','priority_id','status_id','tat','tat_expire','starred','feedback','spam','assigned_to','merge_primary','is_merge_primary','merged_ids', 'cc_emails', 'custom_fieldset', 'created_by');
            $return["tkt_data"]['tat_halt'] = $st->status->tat_halt;
            $return["tkt_data"]['device'] = $st->device_id ? Device::find($st->device_id) : null;
            $return["custom_field"] = $custom_field;
            $return["Custom_field_date"] = $CustomFieldDate;
            $return['status'] = "success";
            $return['msg'] = "";
            return response()->json($return);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }
    }

    // add the comment on ticket
    public function addComment(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to update the ticket"];
        $ticketId = $this->cleanTicketReference($request->input('id'));
        $rules = array(
            'id' => array(
                'required', 
                Rule::exists('tkt_tickets')->where(function($q) use($ticketId, $request) {
                    $q->where('id', '=', $ticketId);
                    $q->whereNull('is_temp');
                })
            ),
            'comment' =>'required|string|min:1|max:2000',
            'is_note' =>'sometimes|nullable|integer|min:0|max:1',
            'tmp_id' => 'required|string|max:20',
            'follow_cc' =>'sometimes|nullable|integer|min:0|max:1',
            'cc_emails' => 'sometimes|nullable|string|max:555',
            'add_back_trail' => 'sometimes|nullable|integer|min:0|max:1',
        );
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $st = Ticket::find($request->id);
        if(in_array($st->status_id, [5,6])) {
            $return['msg'] = "Ticket is already closed or resolved. You Can not add comment.";
            return response()->json($return);
        }
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
        $checkAuthUser = Auth::check() ? Auth::user()->id : ($request->created_id ?? null);
        $tf = new TktFollowing();
        $tf->ticket_id = $request->id;
        $tf->remarks = trim($request->comment);
        $tf->is_note = $request->is_note ? 1 : 0;
        $tf->updated_by = $checkAuthUser;
        $tf->assigned_to = $st->assigned_to;
        $tf->cc_emails = $cc_emails && count($cc_emails) ? implode(",", $cc_emails) : null;
        $tf->action_type = 7;
        $tf->auto_response = isset($request->auto_response)?$request->auto_response:0;
        $tf->creator_id = $st->creator_id;
        if(! $tf->save()) {
            return response()->json($return);
        }

        $st = Ticket::find($request->id);
        $st->updated_at = date('Y-m-d H:i:s');
        $st->save();
        
        $ats = Attachment::where("ticket_id", "=", $st->id)->where("tmp_id", "like", $request->tmp_id)->get();
        if($ats && count($ats)) {
            foreach($ats as $at) {
                $at->following_id = $tf->id;
                $at->save();
            }
        }

        if(isset($processResult) && count($processResult['attachments']) ) {
            Attachment::whereIn('id', $processResult['attachments'])->update(['following_id' => $tf->id]);
        }

        $alertnotify = null;
        if(Settings::first()->alerts_enabled == 1){
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        
        $ticket_creator = User::find($st->creator_id);
        $handler = User::find($st->assigned_to);
        $is_user_cc = $st->norCreatorOrHandler($checkAuthUser);

        // if(! $tf->is_note) {
        //     /* notification to creator */
        //     $notification_text = 'Ticket has been commented';
        //     $notify_people = Privilege::getHandlersByDepartment($st->department_id);
        //     $notify_people[] = $st->creator_id;
        //     Notification::makeTicketNotification($st, $notify_people, $notification_text, Auth::user()->id);
        // }

        /** Code is for adding comments in ticket history */
        $tkt_update['ticket_id'] = $tf->ticket_id;
        $tkt_update['updated_by'] = $checkAuthUser;
        $tkt_update['is_note'] = $tf->is_note;
        $tkt_update['action_type'] = $tf->action_type;
        $tkt_update['auto_response'] = isset($request->auto_response)?$request->auto_response:0;
        CommonHelper::ticketStatusHistory($tkt_update);
        /** Code ends here */

        if( config('mail.service_enabled') && $request->is_note != 1 ) {
            $add_back_trail = $request->add_back_trail == 1 ? true : false;

            if( $is_user_cc ) {
                /* if commenter is cc, then inform to both creator and handler */
                try {
                    $temp_to = [];
                    if( $ticket_creator && $ticket_creator->email && filter_var($ticket_creator->email, FILTER_VALIDATE_EMAIL) ) {
                        $temp_to[] = $ticket_creator->email;
                    }
                    if( $handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL) ) {
                        $temp_to[] = $handler->email;
                    }

                    /* check for current updator is in cc list, if yes, get list expect him */ 
                    if( $cc_emails ) {
                        if (Auth::check()) {
                            $cc_emails = array_diff($cc_emails, [Auth::user()->email]);
                        }
                    }

                    if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                        $cc_emails = [];
                        foreach ($alertnotify as $email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $cc_emails[] = $email;
                            }
                        }
                        Mail::to($temp_to)->cc($cc_emails)->queue(new UserComment($st, $ticket_creator, $tf->remarks, $tf, $add_back_trail));
                    }
                    else {
                        Mail::to($temp_to)->cc($cc_emails)->queue(new UserComment($st, $ticket_creator, $tf->remarks, $tf, $add_back_trail));
                    }
                }
                catch(\Exception $e) {
                    Log::error("Issue Commenter is cc: ");
                    Log::error($e->getMessage());
                }
            }
            elseif( $checkAuthUser != $st->creator_id ) {
                /* send notification to creator */
                try {
                    if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                        $cc_emails = [];
                        foreach ($alertnotify as $email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $cc_emails[] = $email;
                            }
                        }
                        Mail::to($ticket_creator->email)->cc($cc_emails)->queue(new UserComment($st, $ticket_creator, $tf->remarks, $tf, $add_back_trail));
                    }
                    else {
                        Mail::to($ticket_creator->email)->cc($cc_emails)->queue(new UserComment($st, $ticket_creator, $tf->remarks, $tf, $add_back_trail));
                    }
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            elseif( $handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL) ){
                try {
                    if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                        $cc_emails = [];
                        foreach ($alertnotify as $email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $cc_emails[] = $email;
                            }
                        }
                        Mail::to($handler->email)->cc($cc_emails)->queue(new UserComment($st, $handler, $tf->remarks, $tf, $add_back_trail));
                    }
                    else {
                        Mail::to($handler->email)->cc($cc_emails)->queue(new UserComment($st, $handler, $tf->remarks, $tf, $add_back_trail));
                    }
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        }

        /** send push notification */
        $u = User::find($checkAuthUser);
        $createdUserName = (!empty($u)) ? $u->getGuranteedNameText(true) : "";
        $notify_people = [];
        $userHandlers = Privilege::getHandlersByDepartment($st->department_id);
        if(count($userHandlers)>0){
            foreach($userHandlers as $user){
                $handlerUser = User::find($user);
                if($handlerUser->hasPermission("service_tickets")){
                    array_push($notify_people,$user);
                }
            }
        }
        $technician = $st->assigned_to;
        array_push($notify_people,$u->id,$technician);
        $notificationText = "Ticket #".$st->id." is being commented by ".$createdUserName;
        $data = [
            'title' => $notificationText,
            'data' => $st,
            'notify' => $notify_people,
        ];
        $sendNotifications = CommonHelper::sendPushNotification($data);
        if($sendNotifications != false){
            $response = json_decode($sendNotifications);
            if(isset($response->failure) && $response->failure == 1){
                Log::error("create service ticket id:" . $st->id. " notification error " .json_encode($response));
            }
        }

        $return["status"] = "success";
        $return["msg"] = $tf->is_note ? "Note has been added successfully" : "Ticket has been commented successfully.";
        $return["data"] = $st->only('id', 'department_id','problem_category_id','priority_id','status_id','tat','tat_expire','starred','feedback','cc_emails', 'status');
        $return["data"]["expire_info"] = $st->expireInfo();
        $return["data"]["tat_halt"] = $st->status->tat_halt;
        return response()->json($return);    
    }

    public function addFeedback(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to send your feeback."];
        $rules = [
            'id' => [
                'required', 
                Rule::exists('tkt_tickets')->where(function($q) use($request) {
                    $q->where('id', '=', $request->id);
                    $q->whereNull('is_temp');
                    $q->whereIn('status_id', [5,6]);
                })
            ],
            'feedback' =>'required|integer|min:1|max:5',
            'remarks' => 'nullable|string|max:1000'
        ];
        
        $validator = Validator::make($request->only("id", "feedback", "remarks") , $rules);
        
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        try {
            $st = Ticket::findOrFail((int) $request->id);
            if($st->creator_id != Auth::user()->id) {
                $return["msg"] = "Sorry. Author mismatch.";
                return response()->json($return);
            }
        }
        catch(\Exception $e) {
            return response()->json($return);
        }
        
        $st->feedback = $request->feedback;
        if(! $st->save()) {
            return response()->json($return);
        }
        
        $tf = new TktFollowing();
        $tf->ticket_id = $st->id;
        $tf->updated_by = Auth::user()->id;
        $tf->remarks = trim($request->remarks);
        $tf->action_type = 3; // Action type 3 to inticate ticket feedback
        $tf->assigned_to = $st->assigned_to;
        $tf->creator_id = $st->creator_id;
        $tf->save();

        $user = Auth::user();
        $alertnotify = null;
        if(Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }

        $add_feed_back = $request->feedback ;

        if(Settings::first()->alerts_enabled == 1) {
            try {
                $alertnotify = CommonHelper::getGlobalAlertEmail();
                if(config('mail.service_enabled') && $add_feed_back <= 5 && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                    foreach($alertnotify as $notify_email){
                        Mail::to($notify_email)->queue(new FeedbackSend( $user, $tf, $add_feed_back));
                    }
                }
                // if($add_feed_back <= 3){
                //     Mail::to('sakthi@greenitco.com')->send(new FeedbackSend( $user, $tf, $add_feed_back));
                // }
                Log::info("addFeedback id:" . $st->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            }
            catch(\Exception $e) {
                Log::error("feedback send: " . $e->getMessage());
            }
        }

        /** send push notification */
        $u = User::find(Auth::user()->id);
        $createdUserName = (!empty($u)) ? $u->getGuranteedNameText(true) : "";
        $notify_people = [];
        // $notify_people = Privilege::getHandlersByDepartment($st->department_id);
        array_push($notify_people,$u->id);
        // $notificationText = "Feedback for Ticket #".$st->id." is received by ".$createdUserName."\n Rating : ".$request->feedback." \n Comment : ".$request->remarks;
        $notificationText = CommonHelper::sanitizeNotificationText("Feedback for Ticket #".$st->id." is received by ".$createdUserName."\n Rating : ".$request->feedback." \n Comment : ".$request->remarks);
        $data = [
            'title' => $notificationText,
            'data' => $st,
            'notify' => $notify_people,
        ];
        $sendNotifications = CommonHelper::sendPushNotification($data);
        if($sendNotifications != false){
            $response = json_decode($sendNotifications);
            if(isset($response->failure) && $response->failure == 1){
                Log::error("create service ticket id:" . $st->id. " notification error " .json_encode($response));
            }
        }

        $return["status"] = "success";
        $return["msg"] = "Thanks for your feedback.";
        $return["data"] = $st->only('id', 'department_id','problem_category_id','priority_id','status_id','tat','tat_expire','starred','feedback');
        $return["data"]["expire_info"] = $st->expireInfo();
        return response()->json($return);      
    }

    public function feedbackPending() {
        $return = ["status" => "fail", "msg" => "System is unable to process the request."];

        if( strtolower(config("app.client")) == "crystal" ) {
            $return['data'] = [];
            $return['success'] = 'success';
            $return['msg'] = '';
            return response()->json($return);
        }

        $rawQuery = "SELECT t.id, t.subject, pc.name as pc_name FROM `tkt_tickets` as t join tkt_problem_categories as pc on t.problem_category_id = pc.id WHERE `creator_id` = ". Auth::user()->id . " AND `status_id` IN (5,6) AND feedback IS null AND t.deleted_at IS NULL limit 1";
        $return['data'] = DB::select($rawQuery);
        $return['status'] = 'success';
        $return['msg'] = '';
        return response()->json($return);
    }

    /* to staring the ticket */
    public function toggleStar(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to star the ticket"];
        $rules = [
            'id' => [
                'required', 
                Rule::exists('tkt_tickets')->where(function($q) use($request) {
                    $q->where('id', '=', $request->input('id'));
                    $q->whereNull('is_temp');
                    $q->whereNull('deleted_at');
                })
            ]
        ];
        
        $validator = Validator::make($request->only("id"), $rules);
        
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        
        $curr_user_id = Auth::user()->id;
        $st = Ticket::find($request->id);
        $st->starred = $st->toggleStar($st->starred, $curr_user_id);
        if(! $st->save()) {
            return response()->json($return);
        } 
        
        $return["status"] = "success";
        $return["star"] = $st->checkStarred($st->starred, $curr_user_id);
        $return["msg"] = $return["star"] ? "Star added successfully." : "Star removed successfully";
        return response()->json($return);
    }

    /* get users to assign for given department */
    private function getHandlersByDepartment($department_id, $id = false) {
        $db = DB::table("tkt_user_privileges as tup");
        $db->leftJoin('users as u', 'tup.user_id', '=', 'u.id');
        $db->where("tup.department_id", "=", $department_id);
        $db->where("u.activated", "=", 1);
        $db->whereNull("u.deleted_at");
        /* to avoid self ticket as to himself */
        if( $id ) {
            $db->where('u.id', '!=', $id);
        }
        $db->select("u.id", DB::raw('concat(u.first_name, " ", u.last_name, " (", u.username, ")") as name'));
        return $db->get();
    }

    /* get data for transfer ticket form */
    public function getDataForTransfer($id) {
        $return = ["status" => "fail", "msg" => "Unable to get the data for transfer"];
        $rules = [
            'id' => [
                'required', 
                Rule::exists('tkt_tickets')->where(function($q) use($id) {
                    $q->where('id', '=', $id);
                    $q->whereNull('is_temp');
                    $q->whereNull('deleted_at');
                    $q->whereNotIn('status_id', [5,6]);
                })
            ]
        ];
        $msg = [
            'id.required' => 'Choosen ticket is invalid.',
            'id.exists' => 'Please select active ticket.',
        ];

        $validator = Validator::make(["id"=>$id], $rules, $msg);
        
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        
        $t = Ticket::find($id);
        $data = [];
        $data["opts"] = [];
        $data["opts"]["departments"] = $t->department->optionWithCompany();
        $data["opts"]["devices"] = $t->device->optionWithDetails();

        $get_problem_categories = ProblemCategory::getParentCategoryOnly($t->department_id);
        $problem_categories = [];
        if( count($get_problem_categories) ) {
            foreach($get_problem_categories as $spc) {
                $temp = $spc->only("id", "name", "priority_id", "tat", "form_id");
                $temp["sub"] = $spc->getSubCategoriesForOpts();
                $problem_categories[] = $temp;
            }
        }

        $tag1 = $t["tags"];
        $tags = explode(",", $tag1);
        $tagIds = [];
        foreach($tags as $tag) {
            $tagCheck = Tags::where("id", $tag)->first();
            if(isset($tagCheck) && $tagCheck->count() > 0) {
                array_push($tagIds, $tagCheck);
            }
        }

        $data["opts"]["problem_categories"] = $problem_categories;
        $data["opts"]["handlers"] = $this->getHandlersByDepartment($t->department_id, $t->creator_id);
        $data["tags"]["tags"] = $tagIds;
        $data["ticket"] = $t->only("id", "department_id", "problem_category_id", "priority_id", "tat", "sub_category_id", "assigned_to", "creator_id");
        $return["data"] = $data;
        $return["status"] = "success";
        $return["msg"] = "";
        return response()->json($return);
    }

    /* to transfer ticket */
    public function transfer(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to transfer ticket"];
        $input = $request->all();
        $rules = [
            'id' => [
                'required', 
                Rule::exists('tkt_tickets')->where(function($q) use($request) {
                    $q->where('id', '=', $request->id);
                    $q->whereNull('is_temp');
                    $q->whereNull('deleted_at');
                    $q->whereNotIn('status_id', [5,6]);
                })
            ],
            'department_id' =>'required|exists:departments,id',
            'problem_category_id' =>'nullable|sometimes|exists:tkt_problem_categories,id',
            'priority_id' =>'required|exists:tkt_priorities,id',
            'tat' => 'required|integer|min:0',
            'self_assign' => 'sometimes|nullable|integer|min:0|max:1',
            'assigned_to' => 'nullable|integer|exists:users,id',
            'device_id' => 'nullable|integer|exists:assets,id'
        ];

        $msg = [
            'department_id.required' => 'Please select Department.',
            'problem_category_id.required' => 'Please select Problem Category.'
        ];
        
        $validator = Validator::make($request->all(), $rules, $msg);
        
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        /* check problem category has sub-categories */
        $problem_category = ProblemCategory::find($request->problem_category_id);
        if($problem_category->department_id != $request->department_id) {
            $return["msg"] = trans('content.service_ticket_fields.Please_choose_the_valid_department');
            return response()->json($return);
        }

        $sub_category = false;
        if( $problem_category->totSubCategories() > 0 ) {

            if(! $request->sub_category_id) {
                $return["msg"] = trans('content.service_ticket_fields.Please_enter_valid_sub-category');
                return response()->json($return);
            }

            try {
                $sub_category = ProblemCategory::findOrFail($request->sub_category_id);
                if($sub_category->parent_id != $request->problem_category_id) {
                    throw new \Exception(trans('content.service_ticket_fields.Enter_valid_sub-category'));
                }
            }
            catch(\Exception $e) {
                $return["msg"] = $e->getMessage();
                return response()->json($return);
            }
        }

        if ($problem_category->approval_required == 1 && $problem_category->pab_id == 0) {
            $return["msg"] = trans('content.service_ticket_fields.select_PAB');
            return response()->json($return);
        } elseif ($problem_category->approval_required == 1 && $problem_category->pab_id != 0) {
            try {
                $serviceRequest = TicketProcureRequest::where('ticket_id', '=', $input['id'])->first();
                if(empty($serviceRequest)) {
                    $ticketProcureRequestObj = new TicketProcureRequest();
                    $ticketProcureRequestData = $ticketProcureRequestObj->getDataFields($input);
                    $ticketProcureRequestData['ticket_id'] = $input['id'];
                    $ticketProcureRequestData['sub_category_id'] = $sub_category ? $sub_category->id : null;
                    $ticketObj = Ticket::find($input['id']);
                    $ticketProcureRequestData['subject'] = $ticketObj->subject;
                    $ticketProcureRequestData['content'] = $ticketObj->content;
                    $ticketProcureRequestData['creator_id'] = $ticketObj->creator_id;
                    $ticketProcureRequestObj->fill($ticketProcureRequestData);
                    $ticketProcureRequestObj->assignByHirarchy();

                    if ($ticketProcureRequestObj->save()) {
                        $ticketProcureRequestObj->fillRequestTag();
                        $pab_id = $problem_category->pab_id;
                        $pab = TicketPab::find($pab_id);
                        if($pab->hierarchy_approval != 4) {
                            if (!$pab || $pab->totMembers() < 1) {
                                $msg['msg'] = trans('content.service_ticket_fields.No_user_found_send_request');
                                return $return;
                            }

                            app(RequestController::class)->_sendApprovalRequest($request, $ticketProcureRequestObj);

                            $pab_members = [];
                            if ($pab->hierarchy_approval == 1) {
                                $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                            } else {
                                $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                            }

                            foreach ($pab_members as $k => $member) {
                                $approvalRequest = new TicketApprovalRequest();
                                $approvalRequest->pr_id = $ticketProcureRequestObj->id;
                                $approvalRequest->pab_id = $pab_id;
                                $approvalRequest->user_id = $member->user_id;
                                $user = User::find($approvalRequest->user_id);
                                $pab_member_emails[] = $user->email;
                            }
                            $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                            $creators = array_filter($pro_not_mem);
                        }

                        $return["status"] = "success";
                        $return["msg"] =  trans('content.service_ticket_fields.new_ticket_approval', ['id' =>  $ticketProcureRequestObj->id]);

                        $history = new TicketRequestHistory();
                        $history->user_id = Auth::user()->id;
                        $history->pr_id = $ticketProcureRequestObj->id;
                        $history->change_info = $return["msg"];
                        $history->save();

                        $ticketObj->is_temp = 1;
                        $ticketObj->save();

                        DB::commit();
                        return response()->json($return);
                    }
                }
            } catch (\Exception $e) {
                DB::rollback();
                $return['msg'] = $e->getMessage();
                return response()->json($return);
            }
        }

        $st = Ticket::find($request->id);
        $config = Config::first();
        $config->setWeekEnds();
        $autoCreationAccounts = AutoCreationAccount::where('auto_create_from_email', AutoCreationAccount::ACFE_ENABLED)->first();
        $self_assigner = null;
        $assigned_to = null;

        if( !empty($autoCreationAccounts) && $autoCreationAccounts->isReqDeptChgBfrResolve == 1 && $autoCreationAccounts->default_department_id == $input['department_id'] ) {
            $return["msg"] = trans('content.service_ticket_fields.Ticket_must_be_switched');
            return response()->json($return);
        }

        /* self assign concept */
        if( $request->self_assign == 1 ) {
            $self_assigner = Auth::user();

            if($self_assigner->id == $st->creator_id) {
                $return["msg"] =  trans('content.service_ticket_fields.sorry_you_could_not');
                return response()->json($return);
            }
        }
        /* direct assign while transfer */
        else if( !$request->self_assign && $request->assigned_to && $st->assigned_to != $request->assigned_to ) {
            if($request->assigned_to == $st->creator_id) {
                $return["msg"] = trans('content.service_ticket_fields.you_could_not_assign');
                return response()->json($return);
            }

            $assigned_to = User::findOrFail($request->assigned_to);
        }
        $tagIds = [];
        if(isset($request['tags'])){
            foreach($request['tags'] as $tag){
                $tagCheck = Tags::where("id", $tag)->first();
                if(isset($tagCheck) && $tagCheck->count() > 0){
                    array_push($tagIds, $tagCheck->id);
                } else {
                    $tagCreate = Tags::create(["tags" => $tag]);
                    array_push($tagIds, $tagCreate->id);
                }
            }
        }
        $tags = implode(',', $tagIds);
        $request['tags'] = $tags;
        $data = $request->only("department_id", "problem_category_id", "sub_category_id", "priority_id", "tat", "device_id","tags");
        $is_dept_changing_now = $st->department_id != $data['department_id'];
        $is_pbmcat_changing_now = $st->problem_category_id != $data['problem_category_id'];
        $is_priority_changing_now = $st->priority_id != $data['priority_id'];
        $is_subact_changing_now = (isset($data['sub_category_id']) && $st->sub_category_id != $data['sub_category_id']);
        $is_tat_change_now = $st->tat != $data['tat'];
        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $st->created_at);
        $holidays = Holiday::getHolidaysFrom($created_at);
        if($is_tat_change_now && $st->status->tat_halt != 1 && $data['tat'] > $st->tat) {
            $diffInHours = $data['tat'] - $st->tat;
            $st->tat_expire = $config->calculateAdvancedTat($diffInHours, Carbon::createFromFormat('Y-m-d H:i:s', $st->tat_expire), $holidays);
            $st->tat_remaining_mins = $config->calculateRemainingTat(Carbon::createFromFormat('Y-m-d H:i:s', $st->tat_expire), $holidays);

        } elseif ($is_tat_change_now && $st->status->tat_halt == 1 && $data['tat'] > $st->tat) {
            $diffInHours = $data['tat'] - $st->tat;
            $st->tat_expire = $config->calculateAdvancedTat($diffInHours, Carbon::createFromFormat('Y-m-d H:i:s', $st->tat_expire), $holidays);
            $st->tat_remaining_mins = $config->calculateRemainingTat(Carbon::createFromFormat('Y-m-d H:i:s', $st->tat_expire), $holidays);
        }

        $st->fill($data);      
        // $st->sub_category_id = $sub_category ? $sub_category->id : null;

        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $st->created_at);
        $holidays = Holiday::getHolidaysFrom($created_at->format('Y-m-d'));

        $st->tat_expire = $config->calculateAdvancedTat($st->tat, $created_at, $holidays);
        // $st->tat_expire =  $created_at->addHours($request->tat)->format('Y-m-d H:i:s');

        if($self_assigner) {
            $st->assigned_to = $self_assigner->id;
        }
        elseif($assigned_to) {
            $st->assigned_to = $assigned_to->id;
        }
        else {
            $st->assignByHirarchy(true);
            if($st->assigned_to != null){
                $tkt_update['ticket_id'] = $st->id;
                $tkt_update['updated_by'] = Auth::user()->id;
                $tkt_update['assigned_to'] = $st->assigned_to;
                $tkt_update['old_assigned_to'] = $ticketObj->assigned_to;
                $tkt_update['action_type'] = 1;
                $tkt_update['tat'] = $st->tat;
                CommonHelper::ticketStatusHistory($tkt_update);
            }
        }

        if(! $st->save()) {
            return response()->json($return);
        }

        if( $request->self_assign != 1 ) {
            $tf = new TktFollowing();
            $tf->ticket_id = $st->id;
            $tf->updated_by = Auth::user()->id;
            $tf->remarks = trim($request->remarks);
            $tf->is_note = $request->is_note ? 1 : 0;
            $tf->action_type = 2; /* Action type 2 to indicate ticket get updated (or) transferred */
            $tf->assigned_to = $st->assigned_to;
            $tf->creator_id = $st->creator_id;
            $tf->save();
        }

        // update assign to on Service request if exist
        $serviceReq = TicketProcureRequest::where('ticket_id', $st->id)->first();
        if(!empty($serviceReq)) {
            $serviceReq->assigned_to = $st->assigned_to;
            if(!$serviceReq->save()) {
                return response()->json($return);
            }
        }

        $alertnotify = null;
        if(Settings::first()->alerts_enabled == 1){
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }

        /** Code is for adding assigned ticket functionality in ticket history */
        $tkt_update['ticket_id']=$st->id;
        $tkt_update['updated_by']=Auth::user()->id;
        $tkt_update['assigned_to']=$st->assigned_to;
        $tkt_update['action_type']=1;
        CommonHelper::ticketStatusHistory($tkt_update);
        /** Code ends here */

        // Code is added for adding ticket history for department change, sub dept, change and priority change
        $dept_change  =    $is_dept_changing_now ? $data['department_id'] : null;
        $pbm_cat_change = $is_pbmcat_changing_now ? $data['problem_category_id'] : null;
        $subcat_change = $is_subact_changing_now ? $data['sub_category_id'] : null;
        $priority_change = $is_priority_changing_now ? $data['priority_id'] : null;
        $tat_change =  $is_tat_change_now ? $data['tat'] : null;

        $tkt_update['ticket_id']=$st->id;
        $tkt_update['updated_by'] = Auth::user()->id;
        $mailData = array();
        if($dept_change!=null) {
            $mailData["old_department_id"] = $tkt_update['old_department_id'] = $ticketObj->department_id;
            $tkt_update['department_id'] = $data['department_id'];
            $tkt_update['action_type'] = 10;
            CommonHelper::ticketStatusHistory($tkt_update);
        }
        if($pbm_cat_change!=null) {
            $tkt_update['old_pbm_cat_id'] = $ticketObj->problem_category_id;
            $tkt_update['pbm_cat_id'] = $data['problem_category_id'];
            $tkt_update['action_type'] = 11;
            CommonHelper::ticketStatusHistory($tkt_update);
        }
        if($subcat_change!=null) {
            $tkt_update['old_sub_cat_id'] = $ticketObj->sub_category_id;
            $tkt_update['sub_cat_id'] = $subcat_change;
            $tkt_update['action_type'] = 12;
            CommonHelper::ticketStatusHistory($tkt_update);
        }
        if($priority_change!=null) {
            $tkt_update['old_priority_id'] = $ticketObj->priority_id;
            $tkt_update['priority_id'] = $data['priority_id'];
            $tkt_update['action_type'] = 8;
            CommonHelper::ticketStatusHistory($tkt_update);
        }
        if($tat_change!=null) {
            $tkt_update['tat_changed'] = $ticketObj->tat;
            $tkt_update['tat'] = $data['tat'];
            $tkt_update['action_type'] = 9;
            CommonHelper::ticketStatusHistory($tkt_update);
        }
        // code ends here

        if($st->assigned_to) {
            $creator = User::find($st->creator_id);
            if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($creator->email)->send(new TransferAssignedNotificationToUser($st));
                } catch (\Exception $ex) {
                    Log::error("transfer creator:" . $ex->getMessage());
                }
            }

            $user = User::find($st->assigned_to);
            if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                        Mail::to($user->email)->cc($alertnotify)->queue(new IntimateAssigned($st, $user));
                    }
                    else {
                        Mail::to($user->email)->queue(new IntimateAssigned($st, $user));
                    }
                } catch (\Exception $ex) {
                    Log::error($ex->getMessage());
                }
            }
        }

        /* notification */
        $notification_text = $self_assigner ? 'Ticket got self assigned' : 'Ticket has been transferred';
        $notify_people = Privilege::getHandlersByDepartment($st->department_id);
        // Notification::makeTicketNotification($st, $notify_people, $notification_text, Auth::user()->id);

        /** send push notification */
        $u = User::find(Auth::user()->id);
        $createdUserName = (!empty($u)) ? $u->getGuranteedNameText(true) : "";
        $notify_people = Privilege::getHandlersByDepartment($st->department_id);
        array_push($notify_people, $st->created_by);
        $notificationText = "Ticket #".$st->id." is transferred by ".$createdUserName;
        $data = [
            'title' => $notificationText,
            'data' => $st,
            'notify' => $st->creator_id,
        ];
        $sendNotifications = CommonHelper::sendPushNotification($data);
        if($sendNotifications != false){
            $response = json_decode($sendNotifications);
            if(isset($response->failure) && $response->failure == 1){
                Log::error("create service ticket id:" . $st->id. " notification error " .json_encode($response));
            }
        }

        $return["status"] = "success";
        $return["msg"] = $self_assigner ? trans('content.service_ticket_fields.ticket_has_been_self_Assign') :trans('content.service_ticket_fields.transfered');
        Log::info("transfer service ticket id:" . $tf->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
        return response()->json($return);   
    }

    public function editTicketApi(Request $request) {
        $return = ["status" => "fail", "msg" => "something went wrong"];
        if (!Auth::user()->hasPermissionTo("EditServiceTicket")) {
            $return['msg'] = [trans('content.user_fields.Permission_denied')];
            $return['status'] = 'fail';
            return response()->json($return);
        }
        try {
            $rules = [
                'id' => [
                    'required',
                    Rule::exists('tkt_tickets')->where(function($q) use($request) {
                        $q->where('id', '=', $request->id);
                        $q->whereNull('is_temp');
                        $q->whereNull('deleted_at');
                        $q->whereNotIn('status_id', [5,6]);
                    })
                ],
                'department_id' =>'required|exists:departments,id',
                'problem_category_id' =>'nullable|sometimes|exists:tkt_problem_categories,id',
                'priority_id' =>'required|exists:tkt_priorities,id',
                'tat' => 'integer|min:0|max:1000',
                'self_assign' => 'sometimes|nullable|integer|min:0|max:1',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'device_id' => 'nullable|integer|exists:assets,id'
            ];

            $input = $request->all();

            $msg = [
                'department_id.required' => 'Please select Department.',
                'problem_category_id.required' => 'Please select Problem Category.'
            ];

            $validator = Validator::make($request->all(), $rules, $msg);

            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $st = Ticket::find($request->id);
            /* check if department is updated */
            if($st->department_id != $request->department_id) {
                $return["msg"] = "Unable to perform action.";
                return response()->json($return);
            }

            /* check problem category has sub-categories */
            $problem_category = ProblemCategory::find($request->problem_category_id);
            if($problem_category->department_id != $request->department_id) {
                $return["msg"] = trans('content.service_ticket_fields.Please_choose_the_valid_department');
                return response()->json($return);
            }

            $sub_category = false;
            if( $problem_category->totSubCategories() > 0 ) {

                if(! $request->sub_category_id) {
                    $return["msg"] = trans('content.service_ticket_fields.Please_enter_valid_sub-category');
                    return response()->json($return);
                }

                try {
                    $sub_category = ProblemCategory::findOrFail($request->sub_category_id);
                    if($sub_category->parent_id != $request->problem_category_id) {
                        throw new \Exception(trans('content.service_ticket_fields.Enter_valid_sub-category'));
                    }
                }
                catch(\Exception $e) {
                    $return["msg"] = $e->getMessage();
                    return response()->json($return);
                }
            }
            DB::beginTransaction();
            if ( ($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id == 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id == 0) ) {
                $return["msg"] = trans('content.service_ticket_fields.select_PAB');
                return response()->json($return);
            } elseif ( ($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id != 0) ) {
                try {
                    $serviceRequest = TicketProcureRequest::where('ticket_id', '=', $input['id'])->first();
                    if(empty($serviceRequest)) {
                        if(isset($request->sub_category_id) && $request->sub_category_id != "" && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) {
                            $problem_category = ProblemCategory::find($request->sub_category_id);
                        } else {
                            $problem_category = ProblemCategory::find($request->problem_category_id);
                        }

                        if($problem_category->is_form_required == 1 && $problem_category->form_id !== 0) {
                            $requestForm = RequestedForm::where('form_id', $problem_category->form_id)->where('request_id', $request->id)->first();
                            if(empty($requestForm)) {
                                $return["msg"] = ("Please fill the requested form");
                                $return['requestForm'] = 0;
                                return response()->json($return);
                            }
                        }

                        $ticketProcureRequestObj = new TicketProcureRequest();
                        $ticketProcureRequestData = $ticketProcureRequestObj->getDataFields($input);
                        $ticketProcureRequestData['id'] = $input['id'];
                        $ticketProcureRequestData['ticket_id'] = $input['id'];
                        $ticketProcureRequestData['sub_category_id'] = $sub_category ? $sub_category->id : null;
                        $ticketObj = Ticket::find($input['id']);
                        $ticketProcureRequestData['subject'] = $ticketObj->subject;
                        $ticketProcureRequestData['content'] = $ticketObj->content;
                        $ticketProcureRequestData['creator_id'] = $ticketObj->creator_id;
                        $pab_ids = explode(",", $problem_category->pab_id);
                        $pab = array();
                        foreach ($pab_ids as $key => $val) {
                            $pab[$val] = 0;
                        }
                        $ticketProcureRequestData['pab_id'] = json_encode($pab);
                        $ticketProcureRequestObj->fill($ticketProcureRequestData);

                        if ($ticketProcureRequestObj->creator_id) {
                            $creatorObj = User::where('id', $ticketProcureRequestObj->creator_id)->first();
                            $ticketProcureRequestObj->location_id = ($creatorObj->location_id == "") ? Auth::user()->location_id : $creatorObj->location_id;
                        } else {
                            $ticketProcureRequestObj->location_id = Auth::user()->location_id;
                        }

                        if ($ticketProcureRequestObj->save()) {
                            $ticketProcureRequestObj->fillRequestTag();
                            $pab_ids = explode(",", $problem_category->pab_id);
                            $pab = TicketPab::find($pab_ids[0]);

                            if(!empty($pab) && $pab->hierarchy_approval == 8) {
                                $ticketProcureRequestObj = TicketProcureRequest::find($ticketProcureRequestObj->id);
                                $ticketProcureRequestObj->update([
                                    "status_id" => 3,
                                    "approved_at" => Carbon::now()
                                ]);

                                if(!$ticketProcureRequestObj->save()) {
                                    return $msg;
                                }

                                $return["status"] = "success";
                                $return["msg"] =  trans('content.service_ticket_fields.new_ticket_system_approval', ['id' =>  $ticketProcureRequestObj->id]);

                                $history = new TicketRequestHistory();
                                $history->user_id = Auth::user()->id;
                                $history->pr_id = $ticketProcureRequestObj->id;
                                $history->change_info = $return["msg"];
                                $history->save();

                                // send email to create new request for user
                                $user = User::find($ticketProcureRequestObj->creator_id);
                                try {
                                    $cc_emails = [];
                                    if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                        $manager =  User::find($user->manager_id);
                                        $cc_emails[] = $manager->email;
                                    }
                                    if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                        Log::channel('ticket')->info("CreationServiceRequest Mail: " . json_encode($user->email));
                                        Mail::to($user->email)->cc($cc_emails)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                    }
                                } catch (\Exception $ex) {
                                    Log::channel('ticket')->error("CreationServiceRequest Mail: " . $ex->getMessage());
                                }
                                // send push notification
                                $notify_people = [];
                                array_push($notify_people, $user->id);
                                $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                                $data = [
                                    'title' => $notificationText,
                                    'data' => $ticketProcureRequestObj,
                                    'notify' => $notify_people,
                                ];
                                $sendNotifications = CommonHelper::sendPushNotification($data);
                                $sendNotifications = CommonHelper::sendWhatsappNotification($data);
                                if($sendNotifications != false) {
                                    $response = json_decode($sendNotifications);
                                    if(isset($response->failure) && $response->failure == 1) {
                                        Log::channel('ticket')->error("create service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                    }
                                }
                            } else {
                                // 4 = Manager Approval
                                if (!empty($pab) && $pab->hierarchy_approval != 4) {
                                    if (!$pab || $pab->totMembers() < 1) {
                                        $msg['msg'] = trans('content.service_ticket_fields.No_user_found_send_request');
                                        return $msg;
                                    }

                                    app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);

                                    $pab_members = [];
                                    if ($pab->hierarchy_approval == 1) {
                                        $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                                    } else if ($pab->hierarchy_approval == 5) {
                                        if ($ticketProcureRequestObj->location_id != "") {
                                            $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->where('location_id', $ticketProcureRequestObj->location_id)->orderBy('id', 'asc')->get();
                                        }
                                    } else {
                                        $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                                    }

                                    foreach ($pab_members as $k => $member) {
                                        $approvalRequest = new TicketApprovalRequest();
                                        $approvalRequest->pr_id = $ticketProcureRequestObj->id;
                                        $approvalRequest->pab_id = $pab_ids[0];
                                        $approvalRequest->user_id = $member->user_id;
                                        $user = User::find($approvalRequest->user_id);
                                        $pab_member_emails[] = $user->email;
                                    }
                                    $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                    $creators = array_filter($pro_not_mem);
                                } else {
                                    $st = Ticket::find($request->id);
                                    $usr = User::find($st->creator_id);
                                    if (empty($usr) || empty($usr->manager) || $usr->manager->email == "") {
                                        $return["msg"] = trans('content.service_ticket_fields.Manager_email_is_not_found');
                                        return response()->json($return);
                                    }
                                    app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);

                                    $creators = [$usr->manager->email];
                                }

                                $return["status"] = "success";
                                $return["msg"] = trans('content.service_ticket_fields.new_ticket_approval', ['id' => $ticketProcureRequestObj->id]);

                                $history = new TicketRequestHistory();
                                $history->user_id = Auth::user()->id;
                                $history->pr_id = $ticketProcureRequestObj->id;
                                $history->change_info = $return["msg"];
                                $history->save();

                                $ticketObj->is_temp = 1;
                                $ticketObj->save();

                                DB::commit();
                                return response()->json($return);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    $return['msg'] = $e->getMessage();
                    return response()->json($return);
                }
            }

            $st = Ticket::find($request->id);
            $ticketObj = Ticket::find($request->id);
            $config = Config::first();
            $config->setWeekEnds();
            $autoCreationAccounts = AutoCreationAccount::where('id', $st->ac_email_id)->where('auto_create_from_email', AutoCreationAccount::ACFE_ENABLED)->first();
            $self_assigner = null;
            $assigned_to = null;

            if( !empty($autoCreationAccounts) && $autoCreationAccounts->isReqDeptChgBfrResolve == 1 && $autoCreationAccounts->default_department_id == $input['department_id'] && $st->created_via == 3) {
                $return["msg"] = trans('content.service_ticket_fields.Ticket_must_be_switched');
                return response()->json($return);
            }

            /* self assign concept */
            if( $request->self_assign == 1 ) {
                $self_assigner = Auth::user();

                if($self_assigner->id == $st->creator_id) {
                    $return["msg"] =  trans('content.service_ticket_fields.sorry_you_could_not');
                    return response()->json($return);
                }
            }
            /* direct assign while transfer */
            else if( !$request->self_assign && $request->assigned_to && $st->assigned_to != $request->assigned_to ) {
                if($request->assigned_to == $st->creator_id) {
                    $return["msg"] = trans('content.service_ticket_fields.you_could_not_assign');
                    return response()->json($return);
                }

                $assigned_to = User::findOrFail($request->assigned_to);
            }

            $data = $request->only("department_id", "problem_category_id", "sub_category_id", "priority_id", "tat", "device_id","tags");
            $is_dept_changing_now = $st->department_id != $data['department_id'];
            $is_pbmcat_changing_now = $st->problem_category_id != $data['problem_category_id'];
            $is_priority_changing_now = $st->priority_id != $data['priority_id'];
            $is_subact_changing_now = (isset($data['sub_category_id']) && $st->sub_category_id != $data['sub_category_id']);
            $is_tat_change_now =  (isset($data['tat']) && $st->tat != $data['tat']);

            $st->fill($data);
            // $st->sub_category_id = $sub_category ? $sub_category->id : null;

            $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $st->created_at);
            $holidays = Holiday::getHolidaysFrom($created_at->format('Y-m-d'), $ticketObj->id);

            $st->tat_expire = $config->calculateAdvancedTat($st->tat, $created_at, $holidays);
            // $st->tat_expire =  $created_at->addHours($request->tat)->format('Y-m-d H:i:s');

            if($self_assigner) {
                $st->assigned_to = $self_assigner->id;
            }
            elseif($assigned_to) {
                $st->assigned_to = $assigned_to->id;
            }
            elseif($is_dept_changing_now && $request->assigned_to == "") {
                $st->assignByHirarchy(true);
            }

            if(! $st->save()) {
                return response()->json($return);
            }

            // update assign to on Service request if exist
            $serviceReq = TicketProcureRequest::where('ticket_id', $st->id)->first();
            if(!empty($serviceReq)) {
                $serviceReq->assigned_to = $st->assigned_to;
                if(!$serviceReq->save()) {
                    return response()->json($return);
                }
            }

            $alertnotify = null;
            if(Settings::first()->alerts_enabled == 1){
                $alertnotify = CommonHelper::getGlobalAlertEmail();
            }

            if($assigned_to) {
                /** Code is for adding assigned ticket functionality in ticket history */
                $tkt_update['ticket_id'] = $st->id;
                $tkt_update['updated_by'] = Auth::user()->id;
                $tkt_update['assigned_to'] = $st->assigned_to;
                $tkt_update['action_type'] = 1;
                $tkt_update['tat'] = $st->tat;
                CommonHelper::ticketStatusHistory($tkt_update);
                /** Code ends here */
            }

            // Code is added for adding ticket history for department change, sub dept, change and priority change
            $dept_change  =    $is_dept_changing_now ? $data['department_id'] : null;
            $pbm_cat_change = $is_pbmcat_changing_now ? $data['problem_category_id'] : null;
            $subcat_change = $is_subact_changing_now ? $data['sub_category_id'] : null;
            $priority_change = $is_priority_changing_now ? $data['priority_id'] : null;
            $tat_change =  $is_tat_change_now ? $data['tat'] : null;

            $tkt_update['ticket_id']=$st->id;
            $tkt_update['updated_by'] = Auth::user()->id;
            $mailData = array();
            if($dept_change!=null) {
                $mailData["old_department_id"] = $tkt_update['old_department_id'] = $ticketObj->department_id;
                $tkt_update['department_id'] = $data['department_id'];
                $tkt_update['action_type'] = 10;
                CommonHelper::ticketStatusHistory($tkt_update);
            }
            if($pbm_cat_change!=null) {
                $tkt_update['old_pbm_cat_id'] = $ticketObj->problem_category_id;
                $tkt_update['pbm_cat_id'] = $data['problem_category_id'];
                $tkt_update['action_type'] = 11;
                CommonHelper::ticketStatusHistory($tkt_update);
            }
            if($subcat_change!=null) {
                $tkt_update['old_sub_cat_id'] = $ticketObj->sub_category_id;
                $tkt_update['sub_cat_id'] = $subcat_change;
                $tkt_update['action_type'] = 12;
                CommonHelper::ticketStatusHistory($tkt_update);
            }
            if($priority_change!=null) {
                $tkt_update['old_priority_id'] = $ticketObj->priority_id;
                $tkt_update['priority_id'] = $data['priority_id'];
                $tkt_update['action_type'] = 8;
                CommonHelper::ticketStatusHistory($tkt_update);
            }
            if($tat_change!=null) {
                $tkt_update['tat_changed'] = $ticketObj->tat;
                $tkt_update['tat'] = $data['tat'];
                $tkt_update['action_type'] = 9;
                CommonHelper::ticketStatusHistory($tkt_update);
            }
            // code ends here

            if(($pbm_cat_change != null || $subcat_change != null)){
                if (isset($request['remove_tasks']) && $request['remove_tasks'] == 1) {
                    $old_tasks = Task::with('firstTaskHistory','status','priority','ticket')->where('ticket_id', $request->id)->get();
                    foreach ($old_tasks as $task) {
                        $task_creator = optional($task->firstTaskHistory->first())->change_by
                            ? User::find($task->firstTaskHistory->first()->change_by)
                            : null;

                        $task_assingedTo = $task->assigned_to && $task->assigned_to != null ? User::find($task->assigned_to) : null;
                        $ticket_creator = $ticketObj->creator_id && $ticketObj->creator_id != null ? User::find($ticketObj->creator_id) : null;
                        $ticket_assignedTo = $ticketObj->assigned_to && $ticketObj->assigned_to != null ? User::find($ticketObj->assigned_to) : null;
                        $taskId = $task->id;
                        $temp_task = $task;

                        if ($task->firstTaskHistory && isset($task->firstTaskHistory[0]) && $task->firstTaskHistory[0]->change_by_module == 3) {
                        $TaskHistory = $task->replicate();
                        $TaskHistory->setTable('task_history');
                        $TaskHistory->task_id = $task->id;
                        $TaskHistory->action_id = 3;
                        $TaskHistory->change_by = Auth::id();
                        $TaskHistory->change_by_module = 2;
                        if($TaskHistory->save() && $TaskHistory->ticket_id != null){
                            $TaskHistory['action_type'] = 22;
                            $TaskHistory['updated_by'] = $TaskHistory['change_by'];
                            CommonHelper::ticketStatusHistory($TaskHistory);
                        }
                        if($task->delete()){

                            $to_emails = collect([
                                optional($task_creator)->email,
                                optional($task_assingedTo)->email,
                                optional($ticket_creator)->email,
                                optional($ticket_assignedTo)->email,
                            ])->filter(function ($email) {
                                return filter_var($email, FILTER_VALIDATE_EMAIL);
                            })->unique()->values()->all();

                            if(config('mail.service_enabled') && !empty($to_emails)) {
                                try {
                                    if (Config::requiredAlertSettingsEmail() && (isset($alertnotify) && is_array($alertnotify))){
                                        $cc_emails = [];
                                        foreach ($alertnotify as $email) {
                                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                $cc_emails[] = $email;
                                            }
                                        }
                                        Mail::to($to_emails)->cc($cc_emails)->queue(new TaskRemove($temp_task->toArray(),$task_creator, $task_assingedTo));    
                                    } else {
                                        Mail::to($to_emails)->queue(new TaskRemove($temp_task->toArray(),$task_creator, $task_assingedTo));
                                    }
                                } catch (\Exception $ex) {
                                    Log::error($ex->getMessage());
                                }
                            }
                        }
                        $notificationText = "Task #{$taskId} linked to Ticket #{$st->id} was deleted due to a change in the problem category or sub-category.";
                        $tkt_config = Config::first();
                        $notificationType = $tkt_config->sla_notification_type == 1 ? 'normal' : 'buzzer';
                        $notifyUsers = array_filter([$task->assigned_to, $st->creator_id]);
                        $notificationData = [
                            'title' => $notificationText,
                            'data' => CommonHelper::setDataForNotification($task, 'task', $notificationType),
                            'notify' => $notifyUsers,
                        ];
                        $sendNotifications = CommonHelper::sendPushNotification($notificationData);
                        $sendNotifications = CommonHelper::sendWhatsappNotification($notificationData);
                        if($sendNotifications != false) {
                            $response = json_decode($sendNotifications);
                            if(isset($response->failure) && $response->failure == 1) {
                                Log::channel('ticket')->error("editTicket() ticket_id:" . $st->id. " task notification error " .json_encode($response));
                                }
                            }
                        }
                    }
                }
                app(RequestController::class)->createTasksFromCategory($st);
            }

            if($st->assigned_to) {
                $creator = User::find($st->creator_id);
                if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        Mail::to($creator->email)->send(new TransferAssignedNotificationToUser($st));
                    } catch (\Exception $ex) {
                        Log::channel('ticket')->error("transfer creator:" . $ex->getMessage());
                    }
                }

                $user = User::find($st->assigned_to);
                if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    $mailData["old_assigned_to"] = $ticketObj->assigned_to;
                    try {
                        if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                            Mail::to($user->email)->cc($alertnotify)->queue(new TransferAssignedNotificationToTechnician($st, $user, $mailData));
                        }
                        else {
                            Mail::to($user->email)->queue(new TransferAssignedNotificationToTechnician($st, $user, $mailData));
                        }
                    } catch (\Exception $ex) {
                        Log::channel('ticket')->error("TransferAssignedNotificationToTechnician: " . $ex->getMessage());
                    }
                }
            }

            /* notification */
            $notification_text = 'Ticket has been edited';
            $notify_people = Privilege::getHandlersByDepartment($st->department_id);
            // Notification::makeTicketNotification($st, $notify_people, $notification_text, Auth::user()->id);

            // send push notification
            $notify_people = [];
            $editingUserName = User::find(Auth::user()->id);

            array_push($notify_people, $st->assigned_to);
            array_push($notify_people, $st->creator_id);
            $notificationText = "Ticket #".$st->id." is edited by ".$editingUserName->getGuranteedNameText();
            $data = [
                'title' => $notificationText,
                'data' => $st,
                'notify' => $notify_people,
            ];
            $sendNotifications = CommonHelper::sendPushNotification($data);
            $sendNotifications = CommonHelper::sendWhatsappNotification($data);
            if($sendNotifications != false) {
                $response = json_decode($sendNotifications);
                if(isset($response->failure) && $response->failure == 1) {
                    Log::channel('ticket')->error("editTicket() ticket_id:" . $st->id. " notification error " .json_encode($response));
                }
            }

            $return["status"] = "success";
            $return["msg"] = trans('content.service_ticket_fields.ticket_has_been_edited');
            Log::channel('ticket')->info("editTicketApi ticket_id:" . $st->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));

            DB::commit();
            return response()->json($return);
        } catch(\Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error("editTicketApi: ".$e->getMessage());
            return response()->json($return);
        }
    }

    /* to reopen the resolved ticket */
    public function reopen(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to reopen the ticket"];
        $rules = [
            'id' => [
                'required', 
                Rule::exists('tkt_tickets')->where(function($q) use($request) {
                    $q->where('id', '=', $request->id);
                    $q->whereNull('is_temp');
                    $q->whereNull('deleted_at');
                    $q->whereIn('status_id', [5,6]);
                    $q->whereNull('merge_primary');
                })
            ],
            'add_back_trail' => 'sometimes|nullable|integer|min:0|max:1'
        ];

        $validator = Validator::make($request->only("id"), $rules);
        
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        $current_datetime = Carbon::now(config('app.timezone'));
        $t = Ticket::find($request->id);
        $u = Auth::user();

        $t->status_id = 2; /* reopen status */
        $t->reopened_by = Auth::user()->id;
        $t->reopened_at = $current_datetime->format('Y-m-d H:i:s');
        if(! $t->save()) {
            return response()->json($return);
        }

        $tf = new TktFollowing();
        $tf->ticket_id = $t->id;
        $tf->updated_by = Auth::user()->id;
        $tf->action_type = 2; /* Action type 2 to inticate ticket get updated (or) transfered */
        $tf->updated_status = 2; /* reopen status */
        $tf->remarks = trim($request->comment);
        $tf->is_note = 0;
        $tf->assigned_to = $t->assigned_to;
        $tf->creator_id = $t->creator_id;
        $tf->save();

        $tkt_update['ticket_id'] = $tf->ticket_id;
        $tkt_update['updated_by'] = Auth::user()->id;
        $tkt_update['status'] = 2;
        $tkt_update['action_type'] = 2;
        CommonHelper::ticketStatusHistory($tkt_update);

        $alertnotify = null;
        if(Settings::first()->alerts_enabled == 1){
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }

        $add_back_trail = $request->add_back_trail == 1 ? true : false;

        $handler = User::find($t->assigned_to);
        if( $handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL) ){
            try {
                if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                    Mail::to($handler->email)->cc($alertnotify)->queue(new UserComment($t, $handler, trim($request->comment), $tf, $add_back_trail));
                }
                else {
                    Mail::to($handler->email)->queue(new UserComment($t, $handler, trim($request->comment), $tf, $add_back_trail));
                }
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        /** send push notification */
        $u = User::find(Auth::user()->id);
        $createdUserName = (!empty($u)) ? $u->getGuranteedNameText(true) : "";
        $notify_people = [];
        $userHandlers = Privilege::getHandlersByDepartment($t->department_id);
        if(count($userHandlers)>0){
            foreach($userHandlers as $user){
                $handlerUser = User::find($user);
                if($handlerUser->hasAnyRole(['SuperAdmin', 'Admin'])) {
                    array_push($notify_people,$user);
                }
            }
        }
        $technician = $t->assigned_to;
        array_push($notify_people,$u->id,$technician);
        $notificationText = "Ticket #".$t->id." is being reopened by ".$createdUserName;
        $data = [
            'title' => $notificationText,
            'data' => $t,
            'notify' => $notify_people,
        ];
        $sendNotifications = CommonHelper::sendPushNotification($data);
        if($sendNotifications != false){
            $response = json_decode($sendNotifications);
            if(isset($response->failure) && $response->failure == 1){
                Log::error("create service ticket id:" . $t->id. " notification error " .json_encode($response));
            }
        }

        /* notification */
        $notification_text = 'Ticket got reopened';
        $notify_people = Privilege::getHandlersByDepartment($t->department_id);
        // Notification::makeTicketNotification($t, $notify_people, $notification_text, Auth::user()->id);

        $return["status"] = "success";
        $return["msg"] = "Ticket has been reopened successfully";
        return response()->json($return);
    }

    /* to delete the ticket */
    public function deleteTicket(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to delete the ticket"];
        try {
            $rules = [
                'id' => [
                    'required',
                    Rule::exists('tkt_tickets')->where(function($q) use($request) {
                        $q->where('id', '=', $request->input('id'));
                        $q->whereNull('is_temp');
                        $q->whereNull('deleted_at');
                    })
                ]
            ];

            $validator = Validator::make($request->only("id"), $rules);

            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $st = Ticket::find($request->id);
            if($st->merge_primary) {
                $get_primary = Ticket::withTrashed()->where('id', '=', $st->merge_primary)->get();
                if(count($get_primary) > 0) {
                    if( ! $get_primary[0]->deleted_at ) {
                        $return["msg"] = trans('content.service_ticket_fields.Please_delete_Primary_#') . $st->merge_primary . trans('content.service_ticket_fields.ticket_before_delete');
                        return response()->json($return);
                    }
                }
            }

            $st->deleted_by = Auth::user()->id;
            $st->delete();

            /** Code is for adding comments in ticket history */
            $tkt_update['ticket_id'] = $st->id;
            $tkt_update['updated_by'] = Auth::user()->id;
            $tkt_update['is_deleted'] = 1;
            $tkt_update['action_type'] = 13;
            CommonHelper::ticketStatusHistory($tkt_update);
            /** Code ends here */

            /* notification */
            $notification_text = 'Ticket has been deleted';
            $notify_people = Privilege::getHandlersByDepartment($st->department_id);
            $notify_people[] = $st->creator_id;
            // Notification::makeTicketNotification($st, $notify_people, $notification_text, Auth::user()->id);

            $return["status"] = "success";
            $return["msg"] = "Ticket has deleted successfully!";
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("deleteTicket API: " . $e->getMessage());
            return response()->json($return);
        }
    }

    // update the ticket status via ajax
    public function updateStatus(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to update the ticket"];
        $rules = [
            'id' => [
                'required',
                Rule::exists('tkt_tickets')->where(function($q) use($request) {
                    $q->where('id', '=', $request->input('id'));
                    $q->whereNull('is_temp');
                    $q->whereNull('merge_primary');
                })
            ],
            'status_id' =>'required|exists:tkt_statuses,id',
            'priority_id' =>'required|exists:tkt_priorities,id',
            'tat' => 'integer|min:1|max:1000',
            'follow_cc' => 'sometimes|nullable|integer|min:0|max:1',
            'add_back_trail' => 'sometimes|nullable|integer|min:0|max:1',
            'cc_emails' => 'sometimes|nullable|string|max:555'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $data = $request->only("status_id", "priority_id", "tat");
        $tkt_config = Config::first();
        $st = Ticket::find($request->id);
        $ticketObj = Ticket::find($request->id);
        if($st->status_id != $request->status_id && $st->status_id == 6) {
            $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.ticket_closed')];
            return response()->json($return);
        }
        if($st->status_id != $request->status_id && $st->status_id == 5 && $request->status_id != 2) {
            $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.ticket_reopened')];
            return response()->json($return);
        }
        if($st->status_id != $request->status_id && $st->status_id != 5 && $request->status_id == 2) {
            $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.current_ticket_status'). $st->status->name .trans('content.service_ticket_fields.reopened_ticket_status')];
            return response()->json($return);
        }
        if($st->assigned_to == null) {
            $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.not_assign_any_technician')];
            return response()->json($return);
        }
        if($st->assigned_to != Auth::user()->id) {
            $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.other_technician')];
            return response()->json($return);
        }
        if($st->status_id == 5 && $request->status_id == 5) {
            $return["msg"] = trans('content.service_ticket_fields.ticket_resolved');
            return response()->json($return);
        }
        $autoCreationAccounts = AutoCreationAccount::where('id', $st->ac_email_id)->where('auto_create_from_email', AutoCreationAccount::ACFE_ENABLED)->first();
        $tkt_config->setWeekEnds();
        $is_status_changing_now = $st->status_id != $data['status_id'];
        $is_priority_changing_now = $st->priority_id != $data['priority_id'];

        if($st->assigned_to === null) {
            $return["msg"] = trans('content.service_ticket_fields.ticket_not_assign');
            return response()->json($return);
        }
        if($st->assigned_to != Auth::user()->id) {
            $return["msg"] = "This ticket not assign to you";
            return response()->json($return);
        }
        if( !empty($autoCreationAccounts) && $autoCreationAccounts->isReqDeptChgBfrResolve == 1 && $autoCreationAccounts->default_department_id == $st->department_id && $st->created_via == 3) {
            $return["msg"] = trans('content.service_ticket_fields.Ticket_must_be_switched');
            return response()->json($return);
        }

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

        /* ticket getting closed (or) reopend by the user */
        $needToStoreComment = false;
        if(($st->status_id != $request->status_id && in_array($request->status_id, [2,5])) || (! empty($tkt_config) && $tkt_config->mail_all_status_changes)) {
            $rules = [
                'comment' => 'required|string|max:255'
            ];

            $validator = Validator::make($request->only('comment'), $rules);

            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $needToStoreComment = true;
            $is_reopening = $request->status_id == 2 ? true : false;
        }

        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $st->created_at);
        $holidays = Holiday::getHolidaysFrom($created_at);

        $is_tat_changed = false;
        if($st->tat != $request->tat) {
            $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $st->created_at);
            $st->tat_expire = $created_at->addHours($request->tat)->format('Y-m-d H:i:s');
            $is_tat_changed = true;
        }

        $is_resolved_now = false;
        if($data["status_id"] != $st->status_id && $st->status_id != 6 && $data["status_id"] == 5) {
            $is_resolved_now = true;
            $st->resolved_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
            $st->feedback = null;
        }

        /* for tat halt hrs calculation */
        $target_status = Status::find($data["status_id"]);
        if($data["status_id"] != $st->status_id) {
            if($target_status->tat_halt && !$st->status->tat_halt) {
                // need calc
                $expire_at_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $st->tat_expire);
                // $data["tat_remaining_mins"] = Carbon::now(config('app.timezone'))->diffInMinutes($expire_at_carbon);
                $data["tat_remaining_mins"] = $tkt_config->calculateRemainingTat($expire_at_carbon, $holidays);
            }
            elseif(!$target_status->tat_halt && $st->status->tat_halt) {
                // need calc
                if($st->tat_remaining_mins && !$is_tat_changed) {
                    // $st->tat_expire = Carbon::now(config('app.timezone'))->addMinutes($st->tat_remaining_mins)->format('Y-m-d H:i:s');
                    $st->tat_expire = $tkt_config->calculateAdvancedTat($st->tat_remaining_mins, Carbon::now(config('app.timezone')), $holidays, "m");
                }
                // $st->tat_remaining_mins = 0;
            }

            /* check for spam status releated action */
            if( $target_status->id == Status::STATUS_SPAM ) {
                $st->spam = 1;
            }
            elseif( $st->status_id == Status::STATUS_SPAM ) {
                $st->spam = null;
            }
        }

        /* for reopening */
        if($is_reopening) {
            $current_datetime = Carbon::now(config('app.timezone'));
            $st->reopened_by = Auth::user()->id;
            $st->reopened_at = $current_datetime->format('Y-m-d H:i:s');
        }
        $data['updated_by'] = Auth::user()->id;
        $st->fill($data);
        $st->save();
        $st = Ticket::find($request->id);

        $alertnotify = null;
        if(Settings::first()->alerts_enabled == 1){
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        /* ticket got closed (or) reopened just now. Or as per config, need to store the user comment on followings */
        $tf = new TktFollowing();
        $tf->ticket_id = $st->id;
        $tf->remarks = null;
        // $tf->remarks = $needToStoreComment ? trim($request->comment) : null;
        $tf->is_note = 0;
        $tf->updated_by = Auth::user()->id;
        $tf->updated_status = $is_status_changing_now ? $data['status_id'] : null;
        $tf->cc_emails = $cc_emails && count($cc_emails) ? implode(",", $cc_emails) : null;
        $tf->action_type = $is_status_changing_now ? 2 : 7;
        $tf->assigned_to = $st->assigned_to;
        $tf->creator_id = $st->creator_id;

        $processResult = [];
        if( $needToStoreComment ) {
            $processResult = Attachment::processForB64Imgs($request->comment, $st->id, Auth::user()->id);
            $tf->remarks = $processResult['content'];
        }
        $t->updated_via = 4;
        $tf->save();
        /** Code is for adding status to ticket history */
        $priority_change = $is_priority_changing_now ? $data['priority_id'] : null;
        $status_change = $is_status_changing_now ? $data['status_id'] : null;

        $tkt_update['ticket_id'] = $tf->ticket_id;
        $tkt_update['updated_by'] = Auth::user()->id;
        if($status_change != null) {
            $tkt_update['status'] = $data['status_id'];
            $tkt_update['old_status'] = $ticketObj->status_id;
            $tkt_update['action_type'] = 2;
            CommonHelper::ticketStatusHistory($tkt_update);
        }
        if($priority_change != null) {
            $tkt_update['priority_id'] = $data['priority_id'];
            $tkt_update['old_priority_id'] = $ticketObj->priority_id;
            $tkt_update['action_type'] = 8;
            CommonHelper::ticketStatusHistory($tkt_update);
        }
        if($is_tat_changed) {
            $tkt_update['tat_changed'] = $ticketObj->tat;
            $tkt_update['action_type'] = 9;
            $tkt_update['tat'] = $data['tat'];
            CommonHelper::ticketStatusHistory($tkt_update);
        }
        if($status_change == null && $priority_change == null && $is_tat_changed == false) {
            $tkt_update['action_type'] = 7;
            CommonHelper::ticketStatusHistory($tkt_update);
        }
        /** Code ends here */

        if( $needToStoreComment && count($processResult) && count($processResult['attachments']) ) {
            Attachment::whereIn('id', $processResult['attachments'])->update(['following_id' => $tf->id]);
        }

        if($is_resolved_now) {
            $creator = $st->creator;
            $handler = User::find($st->assigned_to);
            $dep = Department::find($st->department_id);
            $dephandler = User::find($dep->attender_id);

            if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                $add_back_trail = $request->add_back_trail == 1 ? true : false;

                try {
                    if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                        $cc_emails = [];
                        foreach ($alertnotify as $email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $cc_emails[] = $email;
                            }
                        }
                        Mail::to($creator->email)->cc($cc_emails)->queue(new Resolved($st, $creator, $tf->remarks, $tf, $add_back_trail));
                    }
                    else {
                        Mail::to($creator->email)->cc($cc_emails)->queue(new Resolved($st, $creator, $tf->remarks, $tf, $add_back_trail));
                    }
                }
                catch(\Exception $e) {
                    Log::error("updateStatus Mail: " . $e->getMessage());
                }
            }
        }
        elseif(!empty($tkt_config) && $tkt_config->mail_all_status_changes && $st->status_id != Status::STATUS_SPAM) {
            $creator = $st->creator;
            $handler = User::find($st->assigned_to);
            $dep = Department::find($st->department_id);
            $dephandler = User::find($dep->attender_id);

            if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                $add_back_trail = $request->add_back_trail == 1 ? true : false;

                try {
                    if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                        $cc_emails = [];
                        foreach ($alertnotify as $email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $cc_emails[] = $email;
                            }
                        }
                        Mail::to($creator->email)->cc($cc_emails)->queue(new StatusChanged($st, $creator, $tf->remarks, $tf, $add_back_trail));
                    }
                    else {
                        Mail::to($creator->email)->cc($cc_emails)->queue(new StatusChanged($st, $creator, $tf->remarks, $tf, $add_back_trail));
                    }
                }
                catch(\Exception $e) {
                    Log::error("updateStatus Mail1: " . $e->getMessage());
                }
            }
        }

        /** send push notification */
        $notify_people = [];
        $u = User::find(Auth::user()->id);
        $ticket_creator = User::find($st->creator_id);
        $technicianUserName = (!empty($u)) ? $u->getGuranteedNameText(true) : "";
        // $notify_people = Privilege::getHandlersByDepartment($st->department_id);

        array_push($notify_people, $ticket_creator->id);
        $notificationText = "Ticket #".$st->id." status is updated by ".$technicianUserName;
        $data = [
            'title' => $notificationText,
            'data' => $st,
            'notify' => $notify_people,
        ];
        $sendNotifications = CommonHelper::sendPushNotification($data);
        if($sendNotifications != false) {
            $response = json_decode($sendNotifications);
            if(isset($response->failure) && $response->failure == 1) {
                Log::error("create service ticket id:" . $st->id. " notification error " .json_encode($response));
            }
        }

        $return["status"] = "success";
        $return["msg"] = "Ticket has been updated successfully.";
        $return["data"] = $st->only('id', 'department_id','problem_category_id','priority_id','status_id','tat','tat_expire','starred','feedback','cc_emails','status');
        $return["data"]["expire_info"] = $st->expireInfo();
        $return["data"]["tat_halt"] = $target_status->tat_halt;
        return response()->json($return);
    }

    /* to self assign a ticket */
    public function selfAssign(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to get the user to assign the ticket"];
        $rules = [
            'id' => [
                'required', 
                Rule::exists('tkt_tickets')->where(function($q) use($request) {
                    $q->where('id', '=', $request->id);
                    $q->whereNull('is_temp');
                    $q->whereNull('deleted_at');
                    $q->whereNotIn('status_id', [5,6]);
                })
            ]
        ];

        $validator = Validator::make($request->only("id"), $rules);
        
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $t = Ticket::find($request->id);
        $u = Auth::user();
        if($t->department->company_id != $u->company_id) { 
            $return["msg"] = "Please pick only tickets that belongs to your company.";
            return response()->json($return);
        }

        if($u->id == $t->creator_id) {
            $return["msg"] = "Sorry. You could not allowed to assign yourself for your ticket.";
            return response()->json($return);
        }

        $get_user_privileges = Privilege::select('department_id')->where('user_id', $u->id)->get();
        $user_privileged_department_ids = $get_user_privileges->pluck('department_id')->toArray();
        if (!in_array($t->department->id, $user_privileged_department_ids)) {
            $return["msg"] = "You do not have access for this ticket department";
            return response()->json($return);
        }

        if(!$u->hasPermission("service_tickets")) {
            $return["msg"] = "The user '" . $u->username . "' has not ticketing system permission. Please provide ticketing system permission.";
            return response()->json($return);
        }
        if( !empty($autoCreationAccounts) && $autoCreationAccounts->isReqDeptChgBfrResolve == 1 && $autoCreationAccounts->default_department_id == $t->department_id ) {
            $return["msg"] = trans('content.service_ticket_fields.Ticket_must_be_switched');
            return response()->json($return);
        }
        $t->assigned_to = $u->id;
        $t->save();
        
        $tf = new TktFollowing();
        $tf->ticket_id = $t->id;
        $tf->updated_by = Auth::user()->id;
        $tf->assigned_to = $u->id;
        $tf->action_type = 1; // for action assign
        $tf->assigned_to = $t->assigned_to;
        $tf->creator_id = $t->creator_id;
        $tf->save();

        /** Code is for adding Self assigned ticket functionality in ticket history */
        $tkt_update['ticket_id'] = $t->id;
        $tkt_update['updated_by'] = Auth::user()->id;
        $tkt_update['assigned_to'] = $u->id;
        $tkt_update['action_type'] = 1;
        CommonHelper::ticketStatusHistory($tkt_update);
        /** Code ends here */

        /* notification */
        $notify_people = [];
        $assignedToUser = User::find($t->assigned_to);
        $notification_text = "Ticket #".$t->id." is being assigned to ".$assignedToUser->fullName();
        // $notify_people = Privilege::getHandlersByDepartment($t->department_id);
        array_push($notify_people, $t->creator_id);
        // Notification::makeTicketNotification($t, $notify_people, $notification_text, Auth::user()->id);

        $alertnotify = null;
        if(Settings::first()->alerts_enabled == 1){
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        if(config('mail.service_enabled') && $u && $u->email && filter_var($u->email, FILTER_VALIDATE_EMAIL)) {
            try {
                if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                    Mail::to($u->email)->cc($alertnotify)->queue(new IntimateAssigned($t, $u));
                } else {
                    Mail::to($u->email)->queue(new IntimateAssigned($t, $u));
                }
            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
            }
        }

        /** send push notification */
        $u = User::find($u->id);
        $createdUserName = (!empty($u)) ? $u->getGuranteedNameText(true) : "";
        $notify_people = Privilege::getHandlersByDepartment($t->department_id);
        array_push($notify_people,$u->id);
        $notificationText = "Ticket #".$t->id." is being assigned to ".$createdUserName;
        $data = [
            'title' => $notificationText,
            'data' => $t,
            'notify' => $t->creator_id,
        ];
        $sendNotifications = CommonHelper::sendPushNotification($data);
        if($sendNotifications != false) {
            $response = json_decode($sendNotifications);
            if(isset($response->failure) && $response->failure == 1){
                Log::error("create service ticket id:" . $t->id. " notification error " .json_encode($response));
            }
        }

        $return["status"] = "success";
        $return["msg"] = "Ticket assigned to you successfully";
        return response()->json($return);
    }
    
    /* to assign a ticket to specific people */
    public function AssignedTo(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to get the user to assign the ticket"];
        $rules = [
            'id' => [
                'required', 
                Rule::exists('tkt_tickets')->where(function($q) use($request) {
                    $q->where('id', '=', $request->id);
                    $q->whereNull('is_temp');
                    $q->whereNull('deleted_at');
                    $q->whereNotIn('status_id', [5,6]);
                })
            ],
            'assigned_to' => 'required|exists:users,id'
        ];

        $validator = Validator::make($request->only("id","assigned_to"), $rules);
        
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        
        $t = Ticket::find($request->id);
        $u = User::find($request->assigned_to);
        // check if this ticket to already assigned to same user
        if($t->assigned_to == $request->assigned_to) {
            $return['mag'] =  trans('content.service_ticket_fields.already_assigned_ticket');
            return response()->json($return);
        }
        if($t->department->company_id != $u->company_id) { 
            $return["msg"] = "Please select the user who belongs to your company";
            return response()->json($return);
        }

        if($u->id == $t->creator_id) {
            $return["msg"] = "Sorry. You could not allowed to assign yourself for your ticket.";
            return response()->json($return);
        }

        $get_user_privileges = Privilege::select('department_id')->where('user_id', $u->id)->get();
        $user_privileged_department_ids = $get_user_privileges->pluck('department_id')->toArray();
        if (!in_array($t->department->id, $user_privileged_department_ids)) {
            $return["msg"] = "Technician do not have access for this ticket department";
            return response()->json($return);
        }

        if(!$u->hasPermission("service_tickets")) {
            $return["msg"] = "The user '" . $u->username . "' has not ticketing system permission. Please provide ticketing system permission.";
            return response()->json($return);
        }

        if(config('app.client') != 'safari') {
            $autoCreationAccounts = AutoCreationAccount::where('auto_create_from_email', AutoCreationAccount::ACFE_ENABLED)->first();
            if (!empty($autoCreationAccounts) && $autoCreationAccounts->isReqDeptChgBfrResolve == 1 && $autoCreationAccounts->default_department_id == $t->department_id) {
                $return["msg"] = trans('content.service_ticket_fields.Ticket_must_be_switched');
                return response()->json($return);
            }
        }
        $t->assigned_to = $u->id;
        $t->save();
        
        $tf = new TktFollowing();
        $tf->ticket_id = $t->id;
        $tf->updated_by = Auth::user()->id;
        $tf->assigned_to = $u->id;
        $tf->action_type = 1; // for action assign
        $tf->assigned_to = $t->assigned_to;
        $tf->creator_id = $t->creator_id;
        $tf->save();

        $alertnotify = null;
        if(Settings::first()->alerts_enabled == 1){
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        
       /** Code is for adding assigned ticket functionality in ticket history */
       $tkt_update['ticket_id'] = $tf->ticket_id;
       $tkt_update['updated_by'] = Auth::user()->id;
       $tkt_update['assigned_to'] = $tf->assigned_to;
       $tkt_update['action_type'] = $tf->action_type;
       CommonHelper::ticketStatusHistory($tkt_update);
       /** Code ends here */

       if(config('mail.service_enabled') && $u && $u->email && filter_var($u->email, FILTER_VALIDATE_EMAIL)) {
           try {
               if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                   Mail::to($u->email)->cc($alertnotify)->queue(new IntimateAssigned($t, $u));
               }
               else {
                   Mail::to($u->email)->queue(new IntimateAssigned($t, $u));
               }
           } catch (\Exception $ex) {
               Log::error($ex->getMessage());
           }
       }

       $creator = User::find($t->creator_id);
       if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
           try {
               Mail::to($creator->email)->send(new TransferAssignedNotificationToUser($t));
           } catch (\Exception $ex) {
               Log::error("AssignedTo Creator: " . $ex->getMessage());
           }
       }

        /** send push notification */
        $notify_people = [];
        $assigneeUser = User::find($u->id);
        $assignedByUser = User::find(Auth::user()->id);
        // $notify_people = Privilege::getHandlersByDepartment($t->department_id);
        array_push($notify_people, $assigneeUser->id);
        $notificationText = "Ticket #".$t->id." is being assigned by ".$assignedByUser->getGuranteedNameText(true). " to you";
        $notificationText = CommonHelper::sanitizeNotificationText("Ticket #".$t->id." is being assigned by ".$assignedByUser->getGuranteedNameText(true). " to you");
        $data = [
            'title' => $notificationText,
            'data' => $t,
            'notify' => $notify_people,
        ];
        $sendNotifications = CommonHelper::sendPushNotification($data);
        if($sendNotifications != false){
            $response = json_decode($sendNotifications);
            if(isset($response->failure) && $response->failure == 1){
                Log::error("create service ticket id:" . $t->id. " notification error " .json_encode($response));
            }
        }

       /* notification */
       $notification_text = 'Ticket has been assigned to ' . $u->getGuranteedNameText(true);
       $notify_people = Privilege::getHandlersByDepartment($t->department_id);
    //    Notification::makeTicketNotification($t, $notify_people, $notification_text, Auth::user()->id);

        $return["status"] = "success";
        $return["msg"] = "Ticket assigned successfully";
        return response()->json($return);
    }

    /* to get the ticket assignable user based on department */
    public function getUsersToAssign($id) {
        $return = ["status" => "fail", "msg" => "Unable to get the user to assign the ticket"];
        $rules = [
            'id' => [
                'required',
                Rule::exists('tkt_tickets')->where(function($q) use($id) {
                    $q->where('id', '=', $id);
                    $q->whereNull('is_temp');
                    $q->whereNull('deleted_at');
                    $q->whereNotIn('status_id', [5,6]);
                })
            ]
        ];

        $validator = Validator::make(["id"=>$id], $rules);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $t = Ticket::find($id);
        $db = DB::table("tkt_user_privileges as tup");
        $db->leftJoin('users as u', 'tup.user_id', '=', 'u.id');
        $db->where("tup.department_id", "=", $t->department_id);
        $db->where("u.activated", "=", 1);
        $db->where("u.permission", "like", '%service_tickets":1%');
        $db->whereNull("u.deleted_at");

        if($t->assigned_to) {
            $db->where("u.id", "!=", $t->assigned_to);
        }

        $db->select("u.id", DB::raw('concat(u.first_name, " ", u.last_name, " (", u.username, ")") as name'));
        $db->orderBy('u.first_name' , 'ASC');

        $return["data"] = $db->get();

        $return["status"] = "success";
        $return["msg"] = "";
        return response()->json($return);
    }

    /* to mark a ticket as spam */
    public function markAsSpam(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to mark as spam"];
        $rules = [
            'id' => [
                'required',
                Rule::exists('tkt_tickets')->where(function($q) use($request) {
                    $q->where('id', '=', $request->id);
                    $q->whereNull('is_temp');
                    $q->whereNull('deleted_at');
                    $q->whereNotIn('status_id', [5,6]);
                })
            ]
        ];

        $validator = Validator::make($request->only("id"), $rules);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        $tkt_config = Config::first();
        $tkt_config->setWeekEnds();
        $t = Ticket::find($request->id);

        if($t->creator_id == Auth::user()->id || $t->created_by == Auth::user()->id) {
            $return['msg'] = trans('content.service_ticket_fields.creator_logger_cant_spam');
            return response()->json($return);
        }

        $msg = $t->spam == 1 ? trans('content.service_ticket_fields.removed_from_spam') : trans('content.service_ticket_fields.added_to_spam');
        $action_type = $t->spam == 1 ? 5 : 4;

        $t->spam = $t->spam == 1 ? null : 1;

        $target_status = $t->spam == 1 ? Status::find(Status::STATUS_SPAM) : Status::find(Status::STATUS_OPEN);
        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $t->created_at);
        $holidays = Holiday::getHolidaysFrom($created_at);

        if($target_status->tat_halt && !$t->status->tat_halt) {
            $expire_at_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $t->tat_expire);
            $t->tat_remaining_mins = $tkt_config->calculateRemainingTat($expire_at_carbon, $holidays);
        }
        elseif(!$target_status->tat_halt && $t->status->tat_halt) {
            if($t->tat_remaining_mins) {
                $t->tat_expire = $tkt_config->calculateAdvancedTat($t->tat_remaining_mins, Carbon::now(config('app.timezone')), $holidays, "m");
            }
            $t->tat_remaining_mins = 0;
        }

        $t->status_id = $target_status->id;

        if(! $t->save()) {
            return response()->json($return);
        }

        $tf = new TktFollowing();
        $tf->ticket_id = $t->id;
        $tf->updated_by = Auth::user()->id;
        $tf->action_type = $action_type; /* action type 4 means "added on spam list", 5 means "removed from spam list" */
        $tf->updated_status = $t->status_id;
        $tf->assigned_to = $t->assigned_to;
        $tf->creator_id = $t->creator_id;
        $tf->save();

        /** Code is for adding spam and not spam functionality in ticket history */
        $tkt_update['ticket_id'] = $t->id;
        $tkt_update['updated_by'] = Auth::user()->id;
        $tkt_update['action_type'] = $action_type;
        CommonHelper::ticketStatusHistory($tkt_update);
        /** Code ends here */

        /** Code to add user email in ticket block list if ticket marked as spam */
        $emailAccounts = AutoCreationAccount::where('auto_create_from_email',1)->get();
        foreach($emailAccounts as $emailAccount){
            $array = explode(",",$emailAccount->ticketing_blocked_accounts);
            $creatorInfo = User::where('id',$t->creator_id)->select('email')->first();
            $t = Ticket::find($request->id);
            if (($key = array_search("", $array)) !== false) {
                unset($array[$key]);
            }
            if($t->spam == 1) {
                if(!in_array($creatorInfo->email,$array)) {
                    array_push($array,$creatorInfo->email);
                }
            } else {
                if (($key = array_search($creatorInfo->email, $array)) !== false) {
                    unset($array[$key]);
                }
            }
            $emailsLists = implode(",",$array);
            $updateEmailAccount = AutoCreationAccount::where('id',$emailAccount->id)->update(['ticketing_blocked_accounts' => $emailsLists]);
        }
        if($t->creator_id != Auth::user()->id) {
            if($t->spam === 1) {
                User::where('id', $t->creator_id)->update(['activated' => 0 , 'access_token' => null]);
            }elseif(is_null($t->spam)) {
                User::where('id', $t->creator_id)->update(['activated' => 1]);
            }
        }
        $return["status"] = "success";
        $return["msg"] = "Moved to Spam list";
        return response()->json($return);
    }

    /* to list the deparments with company name. It only list the ticket module enabled departments */
    public function departments(Request $request, $ticket_id="") {
        $return = array();
        $search = $request->input("search", "");
        $user = Auth::user();

        $db = DB::table("departments as d");
        $db->join("companies as c", "d.company_id", "=", "c.id");
        $db->where('d.module_ticket_enabled', '=', '1');
        $db->where('d.name', 'not like', "To be assigned");

        $company = $request->input("company_id", null);
        if($company) {
            $db->where("d.company_id", "=", $company);
        }
        elseif($ticket_id) {
            /* utilize to transfer */
            $st = Ticket::find($ticket_id);
            $db->where('d.company_id', '=', $st->department->company_id);
        }
        elseif(! Auth::user()->isSuperUser()) {
            $db->where('d.company_id', '=', Auth::user()->company_id);
        }

        $db->select("d.id", DB::raw("concat(d.name, ' (', c.name, ')') as text"));
        if($search) {
            $db->whereRaw("concat(d.name, ' (', c.name, ')') like '%" . $search . "%'");
        }
        elseif($q && strlen($q) > 2) {
            $db->whereRaw("concat(d.name, ' (', c.name, ')') like '" . $q . "%'");
        }
        $count = $db->count();
        if(isset($request->requestFrom) && $request->requestFrom == "bot") {
            $db->skip($skip)->take(20);
        }
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    /* Ticket Version 2 */

    // get the timeline for a ticket API
    public function ajaxgetTimeline(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to refresh the ticket timeline"];
        $text = $request->id;
        $pattern = '/\d+/';
        preg_match_all($pattern, $text, $matches);
        $numbers = $matches[0];
        if((!isset($numbers[0]))) {
            $return['msg'] = "Please enter valid id.";
            return $return;
        }

        $request->merge(['id' => $numbers[0]]);
        if(isset($request->checkRequest) && $request->checkRequest == true) {
            $req = TicketProcureRequest::where('id', $request->id)->count();
            $ticket = Ticket::where('id', $request->id)->count();
            if(isset($req) && isset($ticket) && $req > 0 && $ticket > 0) {
                $return['status'] = 'success';
                $return['msg'] = "request_available";
                return $return;
            }
        }

        if(isset($request->detailsOf) && $request->detailsOf != null && $request->detailsOf == 'request') {
            $con = new ApiRequestController();
            return $con->getServiceRequestInfo($request, $request->id);
        }

        $rules = [
            'id' => [
                'required',
                Rule::exists('tkt_tickets')->where(function($q) use($request) {
                    $q->where('id', '=', $request->input('id'));
                    $q->whereNull('is_temp');
                })
            ]
        ];
        $messages = [
            'id.exists' => 'This is a Service Request, so you can access it through the portal', 
        ];
            
        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $id = $request->input("id");
        $access_token = $request->input("access_token");
        $st = Ticket::find($id);
        $custom_field_dev = [];
        $original_name = [];
        $deptObj = Department::where('id', $st->department_id)->withTrashed()->first();
        if(!empty($deptObj) && $deptObj->customFieldset && count($deptObj->customFieldset->fields)) {
            foreach($deptObj->customFieldset->fields as $f) {
                $col_name = $f->nameToColumn();
                $original_name = str_replace('_itm_','', $col_name);
                $custom_field_dev[] = $col_name. ' as '.$original_name;
            }
        }
        $current_user = Auth::user();
        $attachment_path = Attachment::getUrl('');
        $attachment_view = Attachment::getAppViewUrl();
        // checking department permission of user
        $get_user_privileges_count = Privilege::where('department_id', $st->department_id)->where('user_id', '=', Auth::user()->id)->count();
        if($get_user_privileges_count == 0 && $st->creator_id != Auth::user()->id) {
            $msg = "You don't have privilege to access this ticket.";
            return response()->json([
                "status" => 'fail',
                "msg" => $msg,
                "access" => false,
            ]);
        }

        if ($current_user->hasRole('User')) { 
            if ($st->creator_id != $current_user->id) {
                $return['msg'] = "As a user, you cannot access this ticket because you are not the creator.";
                return response()->json($return);
            }
        }
        /*to get the ticket data with assigned user and creator user info*/
        $ser_tic = DB::table('tkt_tickets as t');
        $ser_tic->leftJoin('departments as dep', 't.department_id', '=', 'dep.id');
        $ser_tic->leftJoin('companies as comp', 'dep.company_id', '=', 'comp.id');
        // $db->leftJoin('tkt_service_types as st', 't.service_type_id', '=', 'st.id');
        $ser_tic->leftJoin('tkt_problem_categories as pc', 't.problem_category_id', '=', 'pc.id');
        $ser_tic->leftJoin('tkt_problem_categories as sc', 't.sub_category_id', '=', 'sc.id');
        $ser_tic->leftJoin('tkt_statuses as s', 't.status_id', '=', 's.id');
        $ser_tic->leftJoin('tkt_priorities as p', 't.priority_id', '=', 'p.id');
        $ser_tic->leftJoin('users as u', 't.creator_id', '=', 'u.id'); // ticket raiser
        $ser_tic->leftJoin('users as cu', 't.created_by', '=', 'cu.id'); // who actually created ticket
        $ser_tic->leftJoin('users as ta', 't.assigned_to', '=', 'ta.id'); // to whom ticket getting assigned
        $ser_tic->leftJoin('assets as dev', 't.device_id', '=', 'dev.id');
        $ser_tic->leftJoin('companies as creator_comp', 'u.company_id', '=', 'creator_comp.id');
        $ser_tic->leftJoin('locations as creator_loc', 'u.location_id', '=', 'creator_loc.id');
        $ser_tic->leftJoin('locations as ta_loc', 'ta.location_id', '=', 'ta_loc.id');
        $ser_tic->leftJoin('tkt_procure_requests', 't.id', '=','tkt_procure_requests.ticket_id');
        if(config('app.client') == 'ril' || config('app.client') == 'rolepermission') { 
            $ser_tic->leftJoin('tkt_detail as tkt_d', 't.id', '=','tkt_d.ticket_id');
        }
        $ser_tic->where('t.id', '=', $id);
        $ser_tic->select('t.id', 't.subject','t.content','s.name as status', 't.status_id', 'u.id as creator_id','p.name as priority','t.priority_id as priority_id','t.tat', 'dep.name as dep_name','t.department_id as dep_id', 'comp.name as comp_name', 't.feedback', 'dev.asset_tag', 't.device_id', 't.starred', 't.spam','u.job_type', 'u.ex_user_company', 't.created_via', 'creator_loc.name as creator_location','creator_comp.name as create_comp_name','u.phone as user_mobile_no','u.jobtitle as creator_des','u.email as cr_email','u.employee_num as cr_emp_no','ta.email as assigned_user_email','ta.employee_num as assigned_emp_code','ta.jobtitle as assigned_user_des','ta.id as assigned_id','t.department_id', 't.merge_primary', 't.is_merge_primary', 't.creator_id', 't.assigned_to', 't.created_at','t.merged_ids','t.cc_emails', 'pc.name as prob_cat','t.problem_category_id as problem_category_id','sc.name as sub_cat','t.sub_category_id as sub_category_id','tkt_procure_requests.procure_tag as request_tag','t.ticket_type','t.ticket_type_custom_fields','t.tags','t.custom_fields', 't.created_by');
        $ser_tic->addSelect(DB::raw('concat(u.first_name, " ", u.last_name, " @ ", u.username) as creator_name'));
        $ser_tic->addSelect(DB::raw('concat(cu.first_name, " ", cu.last_name, " @ ", cu.username) as created_by_name'));
        if(config('app.client') == 'ril' || config('app.client') == 'rolepermission') {
            $ser_tic->addSelect(DB::raw('tkt_d.seat_no'));
        }
        $ser_tic->addSelect(DB::raw('case when t.assigned_to is not null then concat(ta.first_name, " ", ta.last_name, " @ ", ta.username) else "" end as assigned_to_name'));
        $ser_tic->addSelect(DB::raw('case when t.assigned_to is not null then ta_loc.name else "" end as assigned_to_loc'));

        $ser_tic->addSelect(DB::raw('DATE_FORMAT(t.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
        $ser_tic->addSelect(DB::raw('DATE_FORMAT(t.created_at, "%d %b %Y %h:%i %p") as created_at_format'));
        $ser_tic->addSelect(DB::raw('concat(t.tat, "Hrs") as tat_hrs'));
        $ser_tic->addSelect(DB::raw('case when u.job_type in (1,2) then u.ex_user_company else creator_comp.name end as creator_company'));
        $ser_tic->addSelect(DB::raw('case when t.tat_expire > now() then DATE_FORMAT(t.tat_expire, "%Y-%m-%d %H:%i:%s") else "Overdue" end as tat_expire_format'));
        $ser_tic->addSelect(DB::raw('case when t.status_id in (5,6) then DATE_FORMAT(t.resolved_at, "%d %b %Y %h:%i %p") else "" end as resolved_at_format'));
        $ser_tic->addSelect(DB::raw('case when t.status_id = 6 then DATE_FORMAT(t.closed_at, "%d %b %Y %h:%i %p") else "" end as closed_at_format'));
        // $ser_tic->addSelect(DB::raw('FIND_IN_SET(' . Auth::user()->id . ', t.starred) as is_starred'));
        $service_tickets = $ser_tic->get();

        $db = DB::table('tkt_followings as tf');
        $db->leftJoin('users as u', 'tf.updated_by', '=', 'u.id');
        $db->where('tf.ticket_id', '=', $id);
        $db->whereNull('tf.deleted_at');
        $db->whereNotNull('tf.remarks');

        if($st->wai() == 24) {
            $db->where(function($q) {
                $q->whereNull('tf.is_note')->orWhere('tf.is_note', '!=', 1);
            });
            // $db->where('tf.is_note', '!=', 1);
        }

        $db->select('tf.remarks', 'tf.is_note', 'tf.id as tfid', 'tf.action_type', 'tf.merge_primary', 'tf.ticket_status_form_id','tf.is_workaround','tf.is_valid_workaround','tf.cc_emails','u.id as commenter_id','tf.is_service_request');
        $db->addSelect(DB::raw('IFNULL(concat(u.first_name, " ", u.last_name, " @ ", u.username), "System") as commenter'));
        $db->addSelect(DB::raw('"" as img_path'));
        $db->addSelect(DB::raw('DATE_FORMAT(tf.updated_at, "%d %b %y %h:%i %p") as updated_at_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(tf.updated_at, "%d %b %y") as updated_date_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(tf.updated_at, "%h:%i %p") as updated_time_format'));
        if(env('GPS_ENABLE')){
            $db->addSelect(DB::raw('tf.longitude'));
            $db->addSelect(DB::raw('tf.latitude'));
        }
        $db->orderBy('tf.updated_at', 'asc');

        
        
        $tls = $db->get();
        if($tls && count($tls)) {
            $embedded_attachments = Attachment::getEmbeddedAttachments($st->id);
            foreach($tls as $tl) {
                if(env('GPS_ENABLE')){
                    $tl->tech_address = CommonHelper::getAddressFromCoordinates($tl->latitude,$tl->longitude);
                }  
                $tmp_u = User::find($tl->commenter_id);
                $tl->img_path = $tmp_u ? $tmp_u->getProfileImg() : User::defaultProfileImg();
                $tl->remarks = CommonHelper::renderTktContent($tl->remarks,$embedded_attachments,$tl->img_path);
                $tl->attachments = Attachment::where('following_id', '=', $tl->tfid)->select('id','following_id as tfid','original_file_name as name', 'extension as ext',DB::raw('case when id is not null then concat_ws("", "'.$attachment_path.'/",id) else "" end as attach_file_path'),DB::raw('case when id is not null then concat_ws("", "'.$attachment_view.'/",id) else "" end as attach_view'), DB::raw('case when thumbnail is not null then 1 else 0 end as thumb'))->get();
            }
        }
        foreach($service_tickets as $sts) {
            $tagsText = [];
            if(!empty($sts->tags)) {
                $tagsArray = explode(",", $sts->tags);
                if($tagsArray) {
                    foreach($tagsArray as $tag) {
                        $tag = Tags::find($tag);
                        if($tag) {
                            $tagsText[$tag->id] = $tag->tags;
                        }
                    }
                }
            }
            $sts->tags = $tagsText;

            if(!empty($sts->ticket_type) && $sts->ticket_type != 0) {
                $ticketType = TicketType::find($sts->ticket_type);
                $sts->ticket_type = !empty($ticketType) ? $ticketType->name : 'Normal';
            } else {
                $sts->ticket_type = 'Normal';
            }
            $ticket_type_details = [];
            if($sts->ticket_type_custom_fields != null) {
                $ticket_type_details = json_decode($sts->ticket_type_custom_fields, true);
            }
            $sts->ticket_type_fields = $ticket_type_details;
            $canManageTicketType = false;
            if(Auth::user()->isSuperUser() || $sts->assigned_to == Auth::user()->id) {
                if(!in_array($sts->status_id,[5,6,Status::STATUS_REJECTED])) {
                    $canManageTicketType = !$canManageTicketType;
                }
            }
            $sts->canManageTicketType = $canManageTicketType;
            $is_starred = $sts->starred;
            $sts->is_starred = "";
            $sep = $str_arr = explode (",", $is_starred);
            if(!empty($sep) && in_array(Auth::user()->id, $sep)) {
                $sts->is_starred = 1;
            }
            else{
                $sts->is_starred = 0;
            }
            $sts->attachments_ticket_add = Attachment::where('ticket_id','=',$sts->id)->whereNull('following_id')->select('id', 'original_file_name as name', 'extension as ext',DB::raw('case when id is not null then concat_ws("", "'.$attachment_path.'/",id) else "" end as attach_file_path'),DB::raw('case when id is not null then concat_ws("", "'.$attachment_view.'/",id) else "" end as attach_view'), DB::raw('case when thumbnail is not null then 1 else 0 end as thumb'))->get();
            $tmp_u_ass = User::find($sts->assigned_id);
            $sts->img_path_assigned_user = $tmp_u_ass ? $tmp_u_ass->getProfileImg() : User::defaultProfileImg();
            $tmp_u_cre = User::find($sts->creator_id);
            $sts->img_path_created_user = $tmp_u_cre ? $tmp_u_cre->getProfileImg() : User::defaultProfileImg();
            $sts->overall_feedback = $st->feedBackRating();
            $tktFollowingObj = TktFollowing::where('ticket_id', $id)->where('action_type', 3)->first();
            $sts->feedback_comment = (!empty($tktFollowingObj)) ? $tktFollowingObj->remarks : '';
        }

        $custom_fields = $CustomFieldDate = $custom_field = [];
        $checkCustomFiled = ProblemCategory::find($st->problem_category_id);
        if(isset($checkCustomFiled->custom_fieldset) &&  $checkCustomFiled->custom_fieldset != NULL){
            $custom_field = $this->getTicketTypeFieldsetsAPI($checkCustomFiled->custom_fieldset,"CustomField");
            $CustomFieldDate =$this->getDayForEndDate($st->id,null);
            if(!empty($custom_field['data'])) {
                foreach($custom_field['data'][0]['field_collections'] as $key => $field) {
                    $CustomFieldDate =$this->getDayForEndDate($st->id, $key);
                    array_push($custom_fields, ['id' => $field['id'],'name' => $field['name'], 'date' => $CustomFieldDate]);
                }
            }
            if($CustomFieldDate != 0) {
                if(isset($st->custom_fields) && $st->custom_fields != null){
                    $CustomFieldDate = $CustomFieldDate;
                }else{
                    $CustomFieldDate = [date("d-m-Y H:i"), $CustomFieldDate];
                }
            }
        }
        $departmentCustomFieldValue = [];
        $departmentCustomFieldValues = [];
        $customFieldsFromTableForDepartments = CommonHelper::getCustomFieldsValues('tickets',$id);
        if(!empty($customFieldsFromTableForDepartments)){
            foreach($customFieldsFromTableForDepartments as $customField) {
                if($customField['value'] != null){
                    array_push($departmentCustomFieldValue,[$customField['column'] => $customField['value']]);
                }
            }
            array_map(function($arr) use (&$departmentCustomFieldValues) {
                    $departmentCustomFieldValues = array_merge($departmentCustomFieldValues, $arr);
            }, $departmentCustomFieldValue);
        }
        $category = [];
        if(in_array(config('app.client'), ["ltts", "rolepermission", "grdemo"])){
            $category = app(RequestController::class)->checkApprovalDays($st);
        }
        $taskData = $this->getRelatedTask($request->id)->getData(true); 
        $return['data'] = $tls;
        $return["tkt_data"] = $service_tickets;
        $return["task_data"] = $taskData['tasks'];
        $return["completed_task_per"] = $taskData['completion_percentage'];
        $return['tat_halt'] = $st->status->tat_halt;
        $return["custom_field"] = !empty($custom_field) ? $custom_field : (object) [];
        $return["Custom_field_date"] = $CustomFieldDate;
        $return["Custom_field_data"] = $custom_fields;
        $return["department_custom_field_value"] = !empty($departmentCustomFieldValues) ? $departmentCustomFieldValues : (object) [];
        $return['category'] = $category;
        $return['access_privilege'] = $get_user_privileges_count ? true : false;
        $return['client_name'] = config('app.client');
        $return['gps_enable'] = config('app.gps_enable');
        $return['status'] = "success";
        $return['msg'] = "";
        return response()->json($return);
    }

    /* to return the filter options for the ticket  page */
    public function serviceTicketFilterOptions(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to get filter options"];
        $companyIds=CommonHelper::getSelectedCompanyIds();
        $statuses = Status::select("id","name")->whereIn('company_id',$companyIds)->get();
        $priorities = Priority::select("id","name","service_time")->get();
        $ticket_handlers = Ticket::ticketHandlers();
        $filter_by_dates_opts = Ticket::filterByDateOpts();
        $creatorLogger = Ticket::creatorLogger();
        $ticket_creators = Ticket::ticketCreators();
        $ticket_created_via = Ticket::ticketCreatedVia();
        $ticket_or_sr = Ticket::ticketOrSR();
        $ticket_vip = Ticket::ticketVip();
        $mergeTickets = Ticket::mergeTickets();
        $ticket_feedback = Ticket::ticketFeedback();
        $ticket_tag = Tags::select("id","tags")->get();
        $locations = Location::select("id","name")->whereIn('company_id', $companyIds)->get();

        $return['status'] = "success";
        $return['msg'] = '';
        $return['statuses'] = $statuses;
        $return['priorities'] = $priorities;
        $return['ticket_handlers'] = $ticket_handlers;
        $return['ticket_creators'] = $ticket_creators;
        $return['filter_by_dates_opts'] = $filter_by_dates_opts;
        $return['creatorLogger'] = $creatorLogger;
        $return['locations'] = $locations;
        $return['createdVia'] = $ticket_created_via;
        $return['ticketOrSR'] = $ticket_or_sr;
        $return['ticketVip'] = $ticket_vip;
        $return['mergeTickets'] = $mergeTickets;
        $return['ticketFeedback'] = $ticket_feedback;
        $return['ticketTag'] = $ticket_tag;
        return response()->json($return);
    }

    public function ticketHistory(Request $request) {
        $return = array("status" => "fail", "msg" => trans('content.service_ticket_fields.unable_get_history'));
        $tkt_id= $request->id;

        $ticketObj = Ticket::where('id', $tkt_id)->first();
        if(empty($ticketObj)) {
            $return["msg"] = "Enter valid Ticket ID";
            return response()->json($return);
        }
        $ticket_history = TicketStatusHistory::select('tkt_ticket_status_history.*', 'users.first_name', 'users.last_name', 'tkt_statuses.name', 'tkt_old_status.name as old_status_name', 'assigned.first_name as assigned_to_fname', 'assigned.last_name as assigned_to_lname', 'old_assigned.first_name as old_assigned_to_fname', 'old_assigned.last_name as old_assigned_to_lname', 'old_change_creator.first_name as old_change_creator_fname', 'old_change_creator.last_name as old_change_creator_lname', 'tkt_priorities.name as priority_name', 'departments.name as dept_name','tkt_problem_categories.name as pbm_cat_name','sub_cat.name as sub_cat_name', 'tkt_tickets.merged_ids', 'tkt_tickets.is_merge_primary', 'tkt_tickets.merge_primary', 'tkt_tickets.feedback', 'creator.first_name as creator_to_fname', 'creator.last_name as creator_to_lname',
            'od.name as old_dept_name','otpc.name as old_pbm_cat_name','osub_cat.name as old_sub_cat_name','otp.name as old_priority_name',DB::raw('DATE_FORMAT(tkt_ticket_status_history.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'),
            DB::raw('IFNULL(concat(users.first_name, " ", users.last_name), "System") as commenter')
            )
            ->leftJoin('users', 'tkt_ticket_status_history.updated_by', '=', 'users.id')
            ->leftJoin('tkt_tickets', 'tkt_ticket_status_history.ticket_id', '=', 'tkt_tickets.id')
            ->leftJoin('users as assigned', 'tkt_ticket_status_history.assigned_to', '=', 'assigned.id')
            ->leftJoin('users as creator', 'tkt_ticket_status_history.change_creator_id', '=', 'creator.id')
            ->leftJoin('users as old_assigned','tkt_ticket_status_history.old_assigned_to', '=', 'old_assigned.id')
            ->leftJoin('users as old_change_creator','tkt_ticket_status_history.old_change_creator_id', '=', 'old_change_creator.id')
            ->leftJoin('tkt_statuses', 'tkt_ticket_status_history.status', '=', 'tkt_statuses.id')
            ->leftJoin('tkt_statuses as tkt_old_status', 'tkt_ticket_status_history.old_status', '=', 'tkt_old_status.id')
            ->leftJoin('tkt_priorities', 'tkt_ticket_status_history.priority_id', '=', 'tkt_priorities.id')
            ->leftJoin('departments', 'tkt_ticket_status_history.department_id', '=', 'departments.id')
            ->leftJoin('tkt_problem_categories', 'tkt_ticket_status_history.pbm_cat_id', '=', 'tkt_problem_categories.id')
            ->leftJoin('tkt_problem_categories as sub_cat', 'tkt_ticket_status_history.sub_cat_id', '=', 'sub_cat.id')
            /*old record */
            ->leftJoin('tkt_priorities as otp', 'tkt_ticket_status_history.old_priority_id', '=', 'otp.id')
            ->leftJoin('departments as od', 'tkt_ticket_status_history.old_department_id', '=', 'od.id')
            ->leftJoin('tkt_problem_categories as otpc', 'tkt_ticket_status_history.old_pbm_cat_id', '=', 'otpc.id')
            ->leftJoin('tkt_problem_categories as osub_cat', 'tkt_ticket_status_history.old_sub_cat_id', '=', 'osub_cat.id')
            ->where('tkt_ticket_status_history.ticket_id', $tkt_id)
            ->orderBy('tkt_ticket_status_history.id', 'DESC')
            ->get();

        $return["status"] = "success";
        $return["msg"] = "";
        $return["data"] = $ticket_history;

        return response()->json($return);

    }

    public function ajaxTagDetail(Request $request){
        $return = ["status" => 'fail', 'msg' => 'Unable to fetch tags', "items"=>[], "tot"=>0];
        $input = $request->all();
        try {
            $q = trim($request->q);
            $ticketsObject = Tags::select('id', 'tags AS text');
            if($q) {
                $ticketsObject->where("tags", "like", "%" . $q . "%");
            }

            $ticketsObject->orderBy('id', 'asc');
            $return = [
                "status" => 'success',
                'msg' => 'Tags fetched successfully',
                "tot" => $ticketsObject->count(),
                "data" => $ticketsObject->get(),
            ];
            return response()->json($return);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }
    }

    public function ajaxCategory(Request $request){
        $return = ["items"=>[], "tot"=>0];
        $input = $request->all();

        try {
            DB::enableQueryLog();
            $q = trim($request->q);
            $ticketsObject = ProblemCategory::select('id', 'name AS text');
            if($q) {
                $ticketsObject->where("name", "like", "%" . $q . "%");
            }

            $ticketsObject->orderBy('id', 'asc');
            $return["tot"] = $ticketsObject->count();
            $return["items"] = $ticketsObject->get();
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($return);
        }

    }

    public function ticketFeedback(Request $request) {
        $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.unable_to_send')];
        $input = $request->all();
        $request->id = $input['id'] = trim(base64_decode($input['id']), '###');

        $rules = [
            'feedback' =>'required|integer|min:1|max:5',
            'remarks' => 'nullable|string|max:1000'
        ];

        $validator = Validator::make($request->only("feedback", "remarks"), $rules);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $st = Ticket::find($request->id);

        $st->feedback = $request->feedback;
        if(! $st->save()) {
            return response()->json($return);
        }
        // $get_super = User::where('permissions', 'like', '%superuser":1%')->whereNull('deleted_at')->where('activated', '=', 1)->limit(1)->get();
        $get_super = User::whereHas("roles", function($q){ $q->where("name", "SuperAdmin"); })->limit(1)->get();

        $tf = new TktFollowing();
        $tf->ticket_id = $st->id;
        $tf->updated_by = $st->creator_id;
        $tf->remarks = trim($request->remarks);
        $tf->action_type = 3; // Action type 3 to inticate ticket feedback
        $tf->assigned_to = $st->assigned_to;
        $tf->creator_id = $st->creator_id;
        $tf->save();

        $return["status"] = "success";
        $return["msg"] = trans('content.service_ticket_fields.thanks_feedback');
        $return["data"] = $st->only('id', 'department_id','problem_category_id','priority_id','status_id','tat','tat_expire','starred','feedback');
        $return["data"]["expire_info"] = $st->expireInfo();
        return redirect("ticket/feedback/".base64_encode('###'.$request->id))->with("status_success", "Thank you for your feedback");
    }

    public function editFeedback(Request $request,$id) {
        $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.unable_to_get_data_transfer')];
        $rules = [
            'id' => [
                'required',
                Rule::exists('tkt_tickets')->where(function($q) use($request) {
                    $q->where('id', '=', $request->input('id'));
                    $q->whereNull('is_temp');
                    $q->whereIn('status_id', [5,6]);
                })
            ],
            'feedback' =>'required|integer|min:1|max:5',
            'remarks' => 'nullable|string|max:1000'
        ];

        try {
            DB::beginTransaction();
            $data = Ticket::select('tkt_tickets.id','tkt_tickets.feedback','tkt_followings.id','tkt_followings.remarks')
                ->leftjoin('tkt_followings', 'tkt_followings.ticket_id', 'tkt_tickets.id')
                ->where('tkt_followings.ticket_id', '=', $id)->where('action_type', 3)->first();

            $return['status'] = 'success';
            $return['msg'] = '';
            $return['data'] =  $data;
            DB::commit();
            Log::info("editfeedback id:" . $id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("editfeedback error: ". $e->getMessage());
        }
        return response()->json($return);
    }

    public function updateFeedback(Request $request) {
        $return = ["status"=>"fail", "msg"=>"Unable to update feedback"];
        try {
            DB::beginTransaction();
            $rules = [
                'id' => [
                    'required',
                    Rule::exists('tkt_tickets')->where(function($q) use($request) {
                        $q->where('id', '=', $request->input('id'));
                        $q->whereNull('is_temp');
                        $q->whereIn('status_id', [5,6]);
                    })
                ],
                'feedback' =>'required|integer|min:1|max:5',
                'remarks' => 'required|nullable|string|max:1000'
            ];
            
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
    
            $data = $request->only("id","feedback", "remarks");

            $st= Ticket::find($request->id);

            // if($st['creator_id'] != Auth::user()->id) {
            //     $return["msg"] = trans('content.service_ticket_fields.author_mismatch');
            //     return response()->json($return);
            // }

            $st->feedback = $request->feedback;
            if(! $st->save()) {
                return response()->json($return);
            }
            $tktCommentObj = TktFollowing::where('ticket_id', $st->id)->where('action_type', 3)->first();
            if(!empty($tktCommentObj)) {
                $tktCommentObj->remarks = $request->remarks;
                if(!$tktCommentObj->save()) {
                    return response()->json($return);
                }
            }

            $user = Auth::user();
            $alertnotify = null;
            if(Settings::first()->alerts_enabled == 1) {
                $alertnotify = CommonHelper::getGlobalAlertEmail();
            }

            $add_feed_back = $request->feedback;

            if(Settings::first()->alerts_enabled == 1) {
                try {
                    $alertnotify = CommonHelper::getGlobalAlertEmail();
                    if(config('mail.service_enabled') && $add_feed_back <= 3 && filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                        // Mail::to($alertnotify)->queue(new FeedbackSend( $user, $tf, $add_feed_back));
                    }
                    Log::info("addFeedback id:" . $st->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
                }
                catch(\Exception $e) {
                    Log::error("feedback send: " . $e->getMessage());
                }
            }

            $return["status"] = "success";
            $return["msg"] = trans('content.service_ticket_fields.thanks_feedback');
            $return["data"] = $request->only("id","feedback", "remarks");
            DB::commit();
            Log::info("updatefeedback id:" . $st->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("updatefeedback error: ". $e->getMessage());
        }
        return response()->json($return);
    }

    public function mergeTickets(Request $request) {
        try {
            $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.unable_to_merge')];
            $rules = [
                'primary' => [
                    'required',
                    Rule::exists('tkt_tickets', 'id')->where(function($query) {
                        $query->whereIn('status_id', [1,2,3,4,7,8,9,10,11]);
                        $query->whereNull('is_temp');
                        $query->whereNull('deleted_at');
                        $query->whereNull('merged_ids');
                        $query->whereNull('merge_primary');
                    })
                ],
                'others' => [
                    'required',
                    'string'
                ],
                'remarks' => [
                    'required',
                    'string',
                    'max:2000'
                ]
            ];

            $validator = Validator::make($request->only("primary", "others", "remarks"), $rules);
            
            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $primary = Ticket::findOrFail($request->primary);

            $others = explode(",", $request->others);

            if(! is_array($others)) {
                $return["msg"] = trans('content.service_ticket_fields.invalid_tickets');
                return response()->json($return);
            }
            $otherTickets = explode(',', $request->others);
            $allTickets = array_merge([$request->primary], $otherTickets);
            $existingTask = Task::whereIn('ticket_id', $allTickets)->first();
            if ($existingTask) {
                $ticketType = $existingTask->ticket_id == $request->primary ? 'Primary' : 'Secondary';
                return response()->json([
                    'status' => 'error',
                    'msg' => "A task already exists in the {$ticketType} ticket (ID: {$existingTask->ticket_id}). Merge is not allowed."
                ]);
            }

            $get_others = Ticket::whereIn('id', $others)->whereIn('status_id', [1,2,3,4,7,8,9,10,11])->whereNull('is_temp')->whereNull('deleted_at')->where('department_id', '=', $primary->department_id)->whereNull('merged_ids')->whereNull('merge_primary')->get();

            if( count($get_others) != count($others) ) {
                $return["msg"] = trans('content.service_ticket_fields.invalid_tickets');
                return response()->json($return);
            }

            $now = Carbon::now(config('app.timezone'));
            $merged_by = Auth::user()->id;

            DB::beginTransaction();

            $primary->is_merge_primary = 1;
            $primary->merged_ids = implode(",", $others);
            $primary->merged_by = $merged_by;
            $primary->merged_at = $now->format('Y-m-d H:i:s');
            $primary->save();

            $tf = new TktFollowing();
            $tf->ticket_id = $primary->id;
            $tf->updated_by = $merged_by;
            $tf->remarks = trim($request->remarks);
            $tf->action_type = 6; // Action type 6 to inticate ticket merging
            $tf->assigned_to = $primary->assigned_to;
            $tf->creator_id = $primary->creator_id;
            $tf->save();

            // Adding Ticket history for ticket merge
            $tkt_update['ticket_id'] = $primary->id;
            $tkt_update['updated_by'] = $merged_by;
            $tkt_update['merge_ticket_id'] = null;
            $tkt_update['action_type'] = 6;
            CommonHelper::ticketStatusHistory($tkt_update);
            // Code ends here

            $others_emails = [];
            $others_ids = [];

            foreach($get_others as $other) {
                $other->status_id = 6;
                $other->merge_primary = $primary->id;
                $other->merged_by = $merged_by;
                $other->merged_at = $now->format('Y-m-d H:i:s');
                $other->closed_at = $now->format('Y-m-d H:i:s');
                $other->save();

                $tf = new TktFollowing();
                $tf->ticket_id = $other->id;
                $tf->updated_by = $merged_by;
                $tf->remarks = trim($request->remarks);
                $tf->merge_primary = $primary->id;
                $tf->action_type = 6; // Action type 6 to inticate ticket merging
                $tf->assigned_to = $other->assigned_to;
                $tf->creator_id = $other->creator_id;
                $tf->save();

                // Adding Ticket history for ticket merge
                $tkt_update['ticket_id'] = $other->id;
                $tkt_update['updated_by'] = $merged_by;
                $tkt_update['merge_ticket_id'] = $primary->id;
                $tkt_update['action_type'] = 6;
                CommonHelper::ticketStatusHistory($tkt_update);
                // Code ends here

                $others_emails[] = $other->creator->email;
                $others_ids[] = $other->creator_id;
            }

            $new_cc_emails = $others_emails;
            if( $primary->cc_emails ) {
                $existing_ccs = explode(",", $primary->cc_emails);
                $new_cc_emails = array_merge($existing_ccs, $others_emails);
            }
            $new_cc_emails = array_unique($new_cc_emails);
            $primary->cc_emails = implode(",", $new_cc_emails);
            
            if( count($others_ids) ) {
                $new_others_ids = array_unique($others_ids);
                $primary->merged_tkt_creators = implode(",", $new_others_ids);
            }

            $primary->save();

            DB::commit();

            $alertnotify = null;
            if(Settings::first()->alerts_enabled == 1){
                $alertnotify = CommonHelper::getGlobalAlertEmail();
            }

            /* send single mail notification to ticket creator */
            try {
                $creators = array_unique( array_merge([$primary->creator->email], $others_emails) );

                if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                    Mail::to($creators)->cc($alertnotify)->queue(new MergeAlertForCreator($primary, $get_others, $primary->creator, trim($request->remarks), Auth::user()));
                }
                else {
                    Mail::to($creators)->queue(new MergeAlertForCreator($primary, $get_others, $primary->creator, trim($request->remarks), Auth::user()));
                }
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }

            /* check the individual ticket handler and inform handler about this merge */
            try {
                foreach($get_others as $other) {
                    if(! $other->assigned_to) {
                        continue;
                    }
                    if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                        Mail::to($other->assignedTo->email)->cc($alertnotify)->queue(new MergeAlertForHandler($primary, $other, $other->assignedTo, trim($request->remarks), Auth::user()));
                    }
                    else {
                        Mail::to($other->assignedTo->email)->queue(new MergeAlertForHandler($primary, $other, $other->assignedTo, trim($request->remarks), Auth::user()));
                    }
                }
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }

            $return["msg"] = trans('content.service_ticket_fields.tickets_merge');
            $return["status"] = "success";
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            $return["msg"] = $e->getMessage();
            return response()->json($return);
        }
    }

    public function ajaxGetCategory(Request $request) {
        $return = ["items"=>[], "tot"=>0];
        $input = $request->all();

        try {
            DB::enableQueryLog();
            $q = trim($request->q);
            $ticketsObject = ProblemCategory::select('tkt_problem_categories.id', 'tkt_problem_categories.name AS text', 'tkt_problem_categories.department_id', 'departments.name as department_name')->leftjoin('departments', 'departments.id','tkt_problem_categories.department_id');
            if($q) {
                $ticketsObject->where("name", "like", "%" . $q . "%");
            }

            $ticketsObject->orderBy('id', 'asc');
            $return["tot"] = $ticketsObject->count();
            $return["items"] = $ticketsObject->get();
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($return);
        }
    }

    public function getMappingTicketData(Request $request) {
        $return = ["status" => "fail", "msg" => "Data not fetched"];
        $input = $request->all();

        $rules = [
            'global_department_id' => 'required|exists:departments,id',
            'global_prob_cat_id' => 'required|exists:tkt_problem_categories,id',
            'global_sub_cat_id' => 'nullable'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "status"    => 'fail',
                "msg"       => 'Error in validation',
                "errors"    => $validator->messages()
            ]);
        }

        try {
            if($input["global_department_id"] != "") {
                $departmentObj = Department::select('id', 'name', 'global_department_id')->where('id', $input["global_department_id"])->first();
                if(empty($departmentObj)){
                    $departmentObj = Department::select('id', 'name', 'global_department_id')->where('name', 'like', "To be assigned")->first();
                }
                $return["data"]["department"] = $departmentObj;
            }
            if($input["global_prob_cat_id"] != "") {
                $catgeoryObj = ProblemCategory::select('id', 'name', 'global_prob_cat_id')->where('id', $input["global_prob_cat_id"])->whereNull('parent_id')->first();
                if(empty($catgeoryObj)){
                    $catgeoryObj = ProblemCategory::select('id', 'name', 'global_prob_cat_id')->where('name','like', 'New Ticket')->whereNull('parent_id')->first();
                }
                $return["data"]["prob_category"] = $catgeoryObj;
                $problemCategoryObj = $catgeoryObj;
            }
            if($input["global_sub_cat_id"] != "") {
                $subCategoryObj = ProblemCategory::select('id', 'name', 'parent_id', 'global_sub_cat_id')->where('id', $input["global_sub_cat_id"])->whereNotNull('parent_id')->first();
                if(isset($problemCategoryObj) && !empty($problemCategoryObj) && !empty($subCategoryObj) && $problemCategoryObj->id == $subCategoryObj->parent_id) {
                    $return["data"]["sub_category"] = $subCategoryObj;
                }
                // else {
                //     $return["data"]["sub_category"] = ProblemCategory::select('id', 'name', 'global_sub_cat_id')->where('parent_id', $problemCategoryObj->id)->first();
                // }
            } else {
                if(isset($problemCategoryObj) && !empty($problemCategoryObj)) {
                    $return["data"]["sub_category"] = ProblemCategory::select('id', 'name', 'global_sub_cat_id')->where('parent_id', $problemCategoryObj->id)->first();
                }
            }

            $return["status"] = "success";
            $return["msg"] = "Data fetched successfully";
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($return);
        }
    }

    // update tags of ticket
    public function updateTicketTag(Request $request) {
        try {
            $return = ['status' => 'fail' , 'msg' => 'Unable to update tags.'];
            DB::beginTransaction();
            $ticket = Ticket::find($request->ticketId);
            if (!$ticket) {
                return response()->json([
                    'msg' => 'Ticket not found.'
                ]);
            }  
            $tagIds = [];
            if(isset($request['tags'])) {
                $tags = explode(",", $request['tags']);
                foreach($tags as $tag) {
                    $tagCheck = Tags::where("id", $tag)->first();
                    if(isset($tagCheck) && $tagCheck->count() > 0) {
                        array_push($tagIds, $tagCheck->id);
                    } else {
                        $tagCreate = Tags::create(["tags" => $tag]);
                        array_push($tagIds, $tagCreate->id);
                    }
                }
            }else{
                return response()->json([
                    'msg' => 'Select tags.'
                ]);
            }
            $ticket->tags = implode(",",$tagIds);
            if(! $ticket->save()) {
                return $return;
            }
            DB::commit();
            $return['msg'] = 'Tags updated successfully.';
            $return['status'] = 'success';
            return $return;
        } catch(\Exception $e) {
            DB::rollback();
            Log::error('updateTicketTag() Error: ' . $e->getMessage());
            return $return;
        }
    }

    public function getTicketTypes(Request $request) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => trans('content.service_ticket_fields.unable_to_fetch')
            ];
            $companyIds=CommonHelper::getSelectedCompanyIds();
            $ticketTypes = TicketType::where('status',1)->whereIn('company_id',$companyIds)->get();
            $return = [
                'status' => 'success',
                'msg' => trans('content.service_ticket_fields.fetched_successfully'),
                'data' => $ticketTypes,
            ];
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("getTicketTypes() : ".$e->getMesssage());
            return response()->json($return);
        }

    }

    public function getStatusAsPerTicketType($ticketType) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => trans('content.service_ticket_fields.unable_to_fetch')
            ];
            $statuses = Status::select("id","name")->whereNotIn("id", [6])->whereNotIn("is_enabled", [2]);
            $statuses->where(function($query) use($ticketType) {
                $query->where('ticket_type','like','%'.$ticketType.'%');
                $query->orWhere(function($query){
                    $query->orwhereNull('ticket_type');
                });
            });
            $data  = $statuses->get();
            $return = [
                'status' => 'success',
                'msg' => trans('content.service_ticket_fields.fetched_successfully'),
                'data' => $data
            ];
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("getStatusAsPerTicketType : ".$e->getMessage());
            return response()->json($return);
        }
    }

    public function getFieldsByTicket($ticketId) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => trans('content.service_ticket_fields.unable_to_fetch')
            ];
            $ticket = Ticket::where('id', $ticketId)->first();
            if (!$ticket) {
                $return['msg'] = 'Ticket not found or deleted.';
                return response()->json($return);
            }
            $userId = Auth::id();
            $prevDepartments = Privilege::where('user_id', $userId)->pluck('department_id')->toArray();
            if (!in_array($ticket->department_id, $prevDepartments) && $ticket->creator_id != $userId) {
                $return['msg'] = 'You do not have access to this ticket.';
                return response()->json($return);
            }
            $data  = Ticket::ticketTypeFieldsWithValuesForReports($ticketId);
            $return = [
                'status' => 'success',
                'msg' => trans('content.service_ticket_fields.fetched_successfully'),
                'data' => isset($data) ? $data : null
            ];
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("getFieldsByTicket : ".$e->getMessage());
            return response()->json($return);
        }
    }

    //update ticket type Details
    public function updateTicketTypeDetails(Request $request) {
        try {
            DB::beginTransaction();
            $return = [
                'status' => 'fail',
                'msg' => trans('content.service_ticket_fields.unable_to_update_ticket_type'),
            ];
            $data = $request->all();
            $data['ticket_type_custom_fields'] = isset($request->field) ? json_encode($request->field,true) : null;
            $updateTicketType = Ticket::find($data['ticket_id']);
            unset($data['ticket_id']);
            unset($data['field']);
            $updateTicketType->fill($data);
            $updateTicketType->save();

            $ticketType = TicketType::find($request->ticket_type);
            $text = 'The ticket is updated with ticket type '.$ticketType->name.'<br>';
            foreach($request->field as $key => $field){
                $fieldDetail = CustomField::find($key);
                if(!empty($fieldDetail)) {
                    $text .= $fieldDetail->name . " - " . $field . " <br>";
                }
            }

            //storing ticket following start
            $tf = new TktFollowing();
            $tf->ticket_id = $request->ticket_id;

            $tf->remarks = $text;
            $tf->updated_by = 1;
            $tf->action_type = 15;
            $tf->assigned_to = $updateTicketType->assigned_to;
            $tf->creator_id = $updateTicketType->creator_id;
            $tf->save();

            DB::commit();
            $return = [
                'status' => 'success',
                'msg' => trans('content.service_ticket_fields.updated_ticket_type'),
            ];
            return response()->json($return);
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error("updateTicketTypeDetails() : ".$e->getMessage());
            return response()->json($return);
        }
    }

    public function createRequestFromBotLtts(Request $request) {
        $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.unable_to_create_new')];
        $input = $request->all();
        Log::info("createRequestFromBotLtts: " . json_encode($request->all()));
        $pc = ProblemCategory::find($input['problem_category_id']);
        if(isset($input['sub_category_id'])) {
            $sc = ProblemCategory::find($input['sub_category_id']);
        }
        if(isset($sc)) {
            $priority = Priority::find($sc->priority_id);
        } else {
            $priority = Priority::find($pc->priority_id);
        }
        $departmemt = Department::find($pc->department_id);

        if(isset($input['hostname'])) {
            $device = Device::where('asset_tag',$input['hostname'])->first();
        }

        $data = [
            'creator_id' => Auth::user()->id,
            'content' => $pc->name ." - ".(isset($sc) ? $sc->name : ''),
            'subject' => $pc->name ." - ".(isset($sc) ? $sc->name : ''),
            'department_id' => $departmemt->id,
            'problem_category_id' =>$pc->id,
            'device_id' => isset($device) ? $device->id : null,
            'sub_category_id' =>isset($sc) ? $sc->id : null,
            'priority_id' =>isset($sc) ? $sc->priority_id : $pc->priority_id,
        ];
        if($pc->tat == null && $sc->tat == null){
            $data['tat'] = $priority->service_time;
        } else {
            $data['tat'] = isset($sc) ? $sc->tat : $pc->tat;
        }
        DB::beginTransaction();
        try {

            /* check problem category has sub-categories */
            $problem_category = ProblemCategory::find($data['problem_category_id']);
            $sub_category = false;
            if ($problem_category->totSubCategories() > 0) {

                if (!$data['sub_category_id']) {
                    $return["msg"] = trans('content.service_ticket_fields.Please_enter_valid_sub-category');
                    return response()->json($return);
                }

                try {
                    $sub_category = ProblemCategory::findOrFail($data['sub_category_id']);
                    if ($sub_category->parent_id != $data['problem_category_id']) {
                        throw new \Exception(trans('content.service_ticket_fields.Enter_valid_sub-category'));
                    }
                } catch (\Exception $e) {
                    $return["msg"] = $e->getMessage();
                    return response()->json($return);
                }
            }

            if ($data['creator_id']) {
                $usr = User::find($data['creator_id']);
                if (empty($usr)) {
                    $return["msg"] = trans('content.service_ticket_fields.Chosen_User_is_not_found');
                    return response()->json($return);
                }
                if ($usr->checkoutBasicClearance()) {
                    $return["msg"] = trans('content.service_ticket_fields.Chosen_User_is_not_in_Active_Status');
                    return response()->json($return);
                }
                if((!empty($sub_category) && $sub_category->hierarchy_approval == 4) || (!empty($problem_category) && $problem_category->hierarchy_approval == 4)) {
                    if (empty($usr) || empty($usr->manager) || $usr->manager->email == "") {
                        $return["msg"] = trans('content.service_ticket_fields.Manager_email_is_not_found');
                        return response()->json($return);
                    }
                }
            }

            if ( ($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id == 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id == 0) ) {
                $return["msg"] = trans('content.service_ticket_fields.select_PAB');
                return response()->json($return);
            } elseif ( ($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id != 0) ) {
                try {
                    if(isset($data["sub_category_id"]) && $data["sub_category_id"] != "" && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) {
                        $problem_category = ProblemCategory::find($data["sub_category_id"]);
                    } else {
                        $problem_category = ProblemCategory::find($data["problem_category_id"]);
                    }
                    if($problem_category->is_form_required == 1 && $problem_category->form_id !== 0) {
                        $requestForm = DynamicForm::where('id', $problem_category->form_id)->first();
                        Log::error(json_encode($requestForm));
                        if(!empty($requestForm)) {
                            $fieldsArray = json_decode($requestForm->fields, true);
                            if($data['problem_category_id'] == $pc->id && $data['sub_category_id'] == $sc->id) {
                                foreach($fieldsArray as $key => $v){
                                    if(isset($v['name'])){
                                        if($v['name'] == 'text-1662380190092-0'){
                                            $v['userData'] = [$input['existing_github_link']];
                                        }
                                        if($v['name'] == 'file-1719315542427-0'){
                                            $v['value'] = [$input['filename']];
                                        }
                                        if($v['name'] == 'checkbox-group-1662380422004-0'){
                                            $v['userData'] = [$input['acceptance']];
                                        }
                                        if($v['name'] == 'textarea-1719315500980-0'){
                                            $v['userData'] = [$input['accessReason']];
                                        }
                                        if($v['name'] == 'number-1719421822808-0'){
                                            $v['userData'] = [$input['noOfDays']];
                                        }
                                    }
                                    $fieldsArray[$key] = $v;
                                }
                            }
                            if($data['problem_category_id'] == $pc->id && $data['sub_category_id'] == $sc->id) {
                                foreach($fieldsArray as $key => $v){
                                    if(isset($v['name'])){
                                        if($v['name'] == 'text-1662380641846-0'){
                                            $v['userData'] = [$input['customer_github_link']];
                                        }
                                        if($v['name'] == 'file-1719315675595-0'){
                                            $v['value'] = [$input['custAccessMail']];
                                        }
                                        if($v['name'] == 'file-1719315679108-0'){
                                            $v['value'] = [$input['isApprovalMail']];
                                        }
                                        if($v['name'] == 'textarea-1719315613357-0'){
                                            $v['userData'] = [$input['accessReason']];
                                        }
                                        if($v['name'] == 'checkbox-group-1662380843354-0'){
                                            $v['userData'] = [$input['acceptance']];
                                        }
                                        if($v['name'] == 'number-1719422082094-0'){
                                            $v['userData'] = [$input['noOfDays']];
                                        }
                                    }
                                    $fieldsArray[$key] = $v;
                                }
                            }
                            if($data['problem_category_id'] == 1385 && $data['sub_category_id'] == 2208){
                                foreach($fieldsArray as $key => $v){
                                    if(isset($v['name'])){
                                        if($v['name'] == 'text-1719315756910-0'){
                                            $v['userData'] = [$input['public_github_link']];
                                        }
                                        if($v['name'] == 'file-1719315815858-0'){
                                            $v['value'] = [$input['publicAccessMail']];
                                        }
                                        if($v['name'] == 'textarea-1719315797372-0'){
                                            $v['userData'] = [$input['accessReason']];
                                        }
                                        if($v['name'] == 'checkbox-group-1662380843354-0'){
                                            $v['userData'] = [$input['acceptance']];
                                        }
                                        if($v['name'] == 'number-1719422307687-0'){
                                            $v['userData'] = [$input['noOfDays']];
                                        }
                                    }
                                    $fieldsArray[$key] = $v;
                                }
                            }
                            if($data['problem_category_id'] == 1346) {
                                if($data['sub_category_id'] == 2210) {
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'textarea-1719315000836-0'){
                                                $v['userData'] = [$input['software']];
                                            }
                                            if($v['name'] == 'file-1719315290157-0'){
                                                $v['value'] = [$input['freqLttsInhouseDevSoftware']];
                                            }
                                            if($v['name'] == 'text-1719315248980-0'){
                                                if(isset($input['deviceSettings'])){
                                                    $v['userData'] = [$input['deviceSettings']];
                                                }

                                            }
                                            if($v['name'] == 'text-1719315214196-0'){
                                                if(isset($input['networkSettings'])){
                                                    $v['userData'] = [$input['networkSettings']];
                                                }
                                            }
                                            if($v['name'] == 'text-1719315084967-0'){
                                                if(isset($input['envSettings'])){
                                                    $v['userData'] = [$input['envSettings']];
                                                }
                                            }
                                            if($v['name'] == 'text-1719315130915-0'){
                                                if(isset($input['startstopSettings'])){
                                                    $v['userData'] = [$input['startstopSettings']];
                                                }
                                            }
                                            if($v['name'] == 'checkbox-group-1662121218665-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                            if($v['name'] == 'number-1719315024665-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                                if($data['sub_category_id'] == 2213){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'textarea-1719315000836-0'){
                                                $v['userData'] = [$input['software']];
                                            }
                                            if($v['name'] == 'file-1719417430721-0'){
                                                $v['value'] = [$input['freqCusDevSoftware']];
                                            }
                                            if($v['name'] == 'file-1719315290157-0'){
                                                $v['value'] = [$input['freqDUBUApproval']];
                                            }
                                            if($v['name'] == 'text-1719315248980-0'){
                                                if(isset($input['deviceSettings'])){
                                                    $v['userData'] = [$input['deviceSettings']];
                                                }
                                            }
                                            if($v['name'] == 'text-1719315214196-0'){
                                                if(isset($input['networkSettings'])){
                                                    $v['userData'] = [$input['networkSettings']];
                                                }
                                            }
                                            if($v['name'] == 'text-1719315084967-0'){
                                                if(isset($input['envSettings'])){
                                                    $v['userData'] = [$input['envSettings']];
                                                }
                                            }
                                            if($v['name'] == 'text-1719315130915-0'){
                                                if(isset($input['startstopSettings'])){
                                                    $v['userData'] = [$input['startstopSettings']];
                                                }
                                            }
                                            if($v['name'] == 'checkbox-group-1662121218665-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                            if($v['name'] == 'number-1719315024665-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                                if($data['sub_category_id'] == 2214){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'textarea-1719315000836-0'){
                                                $v['userData'] = [$input['software']];
                                            }
                                            if($v['name'] == 'text-1719315248980-0'  && isset($input['deviceSettings'])  ){
                                                $v['userData'] = [$input['deviceSettings']];
                                            }
                                            if($v['name'] == 'text-1719315214196-0'  && isset($input['networkSettings']) ){
                                                $v['userData'] = [$input['networkSettings']];
                                            }
                                            if($v['name'] == 'text-1719315084967-0'  && isset($input['envSettings']) ){
                                                $v['userData'] = [$input['envSettings']];
                                            }
                                            if($v['name'] == 'text-1719315130915-0'  && isset($input['startstopSettings']) ){
                                                $v['userData'] = [$input['startstopSettings']];
                                            }
                                            if($v['name'] == 'file-1719315290157-0'){
                                                $v['value'] = [$input['freqDUBUApproval']];
                                            }
                                            if($v['name'] == 'checkbox-group-1662121218665-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                            if($v['name'] == 'number-1719315024665-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                                if($data['sub_category_id'] == 2215){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'textarea-1719315000836-0'){
                                                $v['userData'] = [$input['software']];
                                            }
                                            if($v['name'] == 'text-1719315248980-0'&& isset($input['deviceSettings']) ){
                                                $v['userData'] = [$input['deviceSettings']];
                                            }
                                            if($v['name'] == 'text-1719315214196-0' && isset($input['networkSettings']) ){
                                                $v['userData'] = [$input['networkSettings']];
                                            }
                                            if($v['name'] == 'text-1719315084967-0' && isset($input['envSettings']) ){
                                                $v['userData'] = [$input['envSettings']];
                                            }
                                            if($v['name'] == 'text-1719315130915-0' && isset($input['startstopSettings']) ){
                                                $v['userData'] = [$input['startstopSettings']];
                                            }
                                            if($v['name'] == 'file-1719417222263-0'){
                                                $v['value'] = [$input['freqCustApproval']];
                                            }
                                            if($v['name'] == 'file-1719417210031-0'){
                                                $v['value'] = [$input['freqOemApproval']];
                                            }
                                            if($v['name'] == 'file-1719315290157-0'){
                                                $v['value'] = [$input['freqDUBUApproval']];
                                            }
                                            if($v['name'] == 'checkbox-group-1662121218665-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                            if($v['name'] == 'number-1719315024665-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                                if($data['sub_category_id'] == 2212){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'textarea-1719315000836-0'){
                                                $v['userData'] = [$input['software']];
                                            }
                                            if($v['name'] == 'text-1719315248980-0' && isset($input['deviceSettings']) ){
                                                $v['userData'] = [$input['deviceSettings']];
                                            }
                                            if($v['name'] == 'text-1719315214196-0' && isset($input['networkSettings']) ){
                                                $v['userData'] = [$input['networkSettings']];
                                            }
                                            if($v['name'] == 'text-1719315084967-0' && isset($input['envSettings']) ){
                                                $v['userData'] = [$input['envSettings']];
                                            }
                                            if($v['name'] == 'text-1719315130915-0' && isset($input['startstopSettings']) ){
                                                $v['userData'] = [$input['startstopSettings']];
                                            }
                                            if($v['name'] == 'file-1719315290157-0'){
                                                $v['value'] = [$input['freqDUBUApproval']];
                                            }
                                            if($v['name'] == 'checkbox-group-1662121218665-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                            if($v['name'] == 'number-1719315024665-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                                if($data['sub_category_id'] == $sc->id){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'text-1719416578683-0'){
                                                $v['userData'] = [$input['deviceSetting']];
                                            }
                                            if($v['name'] == 'file-1719416594991-0'){
                                                $v['value'] = [$input['adminAccessApproval']];
                                            }
                                            if($v['name'] == 'checkbox-group-1719416612368-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                            if($v['name'] == 'number-1719416589408-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                                if($data['sub_category_id'] == $sc->id){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'text-1719416342819-0'){
                                                $v['userData'] = [$input['networkSetting']];
                                            }
                                            if($v['name'] == 'file-1719416402268-0'){
                                                $v['value'] = [$input['adminAccessApproval']];
                                            }
                                            if($v['name'] == 'checkbox-group-1719416479272-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                            if($v['name'] == 'number-1719416363581-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                                if($data['sub_category_id'] == $sc->id){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'text-1719314670369-0'){
                                                $v['userData'] = [$input['envSetting']];
                                            }
                                            if($v['name'] == 'file-1719314711470-0'){
                                                $v['value'] = [$input['adminAccessApproval']];
                                            }
                                            if($v['name'] == 'checkbox-group-1662121218665-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                            if($v['name'] == 'number-1719314504260-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                                if($data['sub_category_id'] == $sc->id){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'text-1719416111050-0'){
                                                $v['userData'] = [$input['serviceSetting']];
                                            }
                                            if($v['name'] == 'file-1719416268977-0'){
                                                $v['value'] = [$input['adminAccessApproval']];
                                            }
                                            if($v['name'] == 'checkbox-group-1719416012080-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                            if($v['name'] == 'number-1719416137625-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }

                            }
                            if($data['problem_category_id'] == $pc->id){
                                if($data['sub_category_id'] == $sc->id){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'radio-group-1661845103012-0'){
                                                $v['userData'] = [$input['usbStorageApprovedBy']];
                                            }
                                            if($v['name'] == 'textarea-1709371013757-0'){
                                                $v['userData'] = [$input['reasonForAccess']];
                                            }
                                            if($v['name'] == 'select-1709112024251-0'){
                                                $v['userData'] = [$input['usageFrequency']];
                                            }
                                            if($v['name'] == 'radio-group-1661845244084-0'){
                                                $v['userData'] = [$input['massStorageIs']];
                                            }
                                            if($v['name'] == 'number-1663932976061-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                            if($v['name'] == 'checkbox-group-1662104582392-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                            if($v['name'] == 'text-1701263715697-0'){
                                                $v['userData'] = [$input['reasonForAccess']];
                                            }
                                            if($v['name'] == 'file-1732794053954-0'){
                                                $v['userData'] = [$input['usbDUApproval']];
                                            }
                                            if($v['name'] == 'file-1661842097739-0'){
                                                $v['userData'] = [$input['usbBUApproval']];
                                            }
                                            if($v['name'] == 'text-1714976229591-0'){
                                                $v['userData'] = [$input['project_details']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                                if($data['sub_category_id'] == $sc->id){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'textarea-1709370889128-0'){
                                                $v['userData'] = [$input['reasonForAccess']];
                                            }
                                            if($v['name'] == 'select-1709111651877-0'){
                                                $v['userData'] = [$input['usageFrequency']];
                                            }
                                            if($v['name'] == 'device-instance-path'){
                                                $v['userData'] = [$input['instancePath']];
                                            }
                                            if($v['name'] == 'access_requested_day'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                            if($v['name'] == 'checkbox-group-1662129878398-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                                if($data['sub_category_id'] == $sc->id){
                                    foreach($fieldsArray as $key => $v){
                                        if(isset($v['name'])){
                                            if($v['name'] == 'radio-group-1662131809417-0'){
                                                $v['userData'] = [$input['usbStorageApprovedBy']];
                                            }
                                            if($v['name'] == 'textarea-1709370975287-0'){
                                                $v['userData'] = [$input['reasonForAccess']];
                                            }
                                            if($v['name'] == 'select-1709112301932-0'){
                                                $v['userData'] = [$input['usageFrequency']];
                                            }
                                            if($v['name'] == 'file-1662131468602-0'){
                                                $v['value'] = [$input['usbApproval']];
                                            }
                                            if($v['name'] == 'form-control device-instance-path'){
                                                $v['userData'] = [$input['instancePath']];
                                            }
                                            if($v['name'] == 'number-1663933217241-0'){
                                                $v['userData'] = [$input['noOfDays']];
                                            }
                                            if($v['name'] == 'radio-group-1662131809417-0'){
                                                $v['userData'] = [$input['massStorageIs']];
                                            }
                                            if($v['name'] == 'checkbox-group-1662132193676-0'){
                                                $v['userData'] = [$input['acceptance']];
                                            }
                                        }
                                        $fieldsArray[$key] = $v;
                                    }
                                }
                            }
                        }
                    }

                    $st = Ticket::create(["creator_id" => Auth::user()->id, "created_by" => Auth::user()->id, "created_via" => 1]);
                    $data['id'] = $st->id;

                    $ticketProcureRequestObj = new TicketProcureRequest();
                    $ticketProcureRequestData = $ticketProcureRequestObj->getDataFields($data);
                    $ticketProcureRequestData['id'] = $data['id'];
                    $ticketProcureRequestData['ticket_id'] = $data['id'];
                    $ticketProcureRequestData['sub_category_id'] = $sub_category ? $sub_category->id : null;
                    $pab_ids = explode(",", $problem_category->pab_id);
                    $pab = array();
                    foreach ($pab_ids as $key => $val) {
                        $pab[$val] = 0;
                    }
                    $ticketProcureRequestData['pab_id'] = json_encode($pab);
                    $ticketProcureRequestObj->fill($ticketProcureRequestData);
                    // $ticketProcureRequestObj->assignByHirarchy();
                    if ($ticketProcureRequestObj->creator_id) {
                        $creatorObj = User::where('id', $ticketProcureRequestObj->creator_id)->first();
                        //$ticketProcureRequestObj->location_id = ($creatorObj->location_id == "") ? Auth::user()->location_id : $creatorObj->location_id;
                        $ticketProcureRequestObj->location_id = ($creatorObj->location_id == "") ? 1 : $creatorObj->location_id;
                    } else {
                        //$ticketProcureRequestObj->location_id = Auth::user()->location_id;
                        $ticketProcureRequestObj->location_id = 1;
                    }

                    if ($ticketProcureRequestObj->save()) {


                        //save request form data
                        if(isset($requestForm) && !empty($requestForm)){
                            $forDataArray = [
                                'field_values' => json_encode($fieldsArray),
                                'form_id' => $requestForm->id,
                                'request_id' => $ticketProcureRequestObj->id,
                                'created_by' => Auth::user()->id
                            ];
                            $form = new RequestedForm();
                            $form->fill($forDataArray);
                            $form->save();
                        }

                        $ticketProcureRequestObj->fillRequestTag();
                        $acfe = AutoCreationAccount::where('auto_create_from_email', 1)->where('id', $ticketProcureRequestObj->department->tkt_auto_creation_id)->first();
                        $ticketProcureRequestObj->ac_email_id = (!empty($acfe)) ? $acfe->id : 0;
                        $pab_ids = explode(",", $problem_category->pab_id);
                        $pab = TicketPab::find($pab_ids[0]);
                        // 8 = System Approval
                        if(!empty($pab) && $pab->hierarchy_approval == 8) {
                            $ticketProcureRequestObj = TicketProcureRequest::find($ticketProcureRequestObj->id);
                            $ticketProcureRequestObj->update([
                                "status_id" => 3,
                                "approved_at" => Carbon::now()
                            ]);

                            if(!$ticketProcureRequestObj->save()) {
                                return $return;
                            }

                            $return["status"] = "success";
                            $return["msg"] =  trans('content.service_ticket_fields.new_ticket_system_approval', ['id' =>  $ticketProcureRequestObj->id]);

                            $history = new TicketRequestHistory();
                            $history->user_id = Auth::user()->id;
                            $history->pr_id = $ticketProcureRequestObj->id;
                            $history->change_info = $return["msg"];
                            $history->save();

                            // send email to create new request for user
                            $user = User::find($ticketProcureRequestObj->creator_id);
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager =  User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    Log::info("CreationServiceRequest Mail: " . json_encode($user->email));
                                    Mail::to($user->email)->cc($cc_emails)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error("CreationServiceRequest Mail: " . $ex->getMessage());
                            }
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $user->id);
                            $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                            $data = [
                                'title' => $notificationText,
                                'data' => $ticketProcureRequestObj,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("create service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                }
                            }
                        } else {
                            // 4 = Manager Approval
                            if (!empty($pab) && $pab->hierarchy_approval != 4) {
                                if (!$pab || $pab->totMembers() < 1) {
                                    $return['msg'] = trans('content.service_ticket_fields.No_user_found_send_request');
                                    return $return;
                                }

                                app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);

                                $pab_members = [];
                                if ($pab->hierarchy_approval == 1) {
                                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                                } else if ($pab->hierarchy_approval == 5) {
                                    if ($ticketProcureRequestObj->location_id != null) {
                                        $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->where('location_id', $ticketProcureRequestObj->location_id)->orderBy('id', 'asc')->get();
                                        if(count($pab_members) == 0) {
                                            $return["msg"] = trans('content.service_ticket_fields.No_user_found_send_request');
                                            return response()->json($return);
                                        }
                                    } else {
                                        $return["msg"] = trans('content.service_ticket_fields.No_user_found_send_request');
                                        return response()->json($return);
                                    }
                                } else {
                                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                                }

                                foreach ($pab_members as $k => $member) {
                                    $approvalRequest = new TicketApprovalRequest();
                                    $approvalRequest->pr_id = $ticketProcureRequestObj->id;
                                    $approvalRequest->pab_id = $pab_ids[0];
                                    $approvalRequest->user_id = $member->user_id;
                                    $user = User::find($approvalRequest->user_id);
                                    $pab_member_emails[] = $user->email;
                                }
                                $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                $creators = array_filter($pro_not_mem);
                            } else {
                                app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);

                                $creators = [$usr->manager->email];
                            }

                            $alertnotify = null;
                            if (Settings::first()->alerts_enabled == 1) {
                                $alertnotify = CommonHelper::getGlobalAlertEmail();
                            }

                            // send email to create new request for user
                            $user = User::find($ticketProcureRequestObj->creator_id);
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager =  User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    Log::info("Create new Service request sent mail: " . json_encode($user->email));
                                    Mail::to($user->email)->cc($cc_emails)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error("create request send mail to creator: " . $ex->getMessage());
                            }
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $user->id);
                            $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                            $data = [
                                'title' => $notificationText,
                                'data' => $ticketProcureRequestObj,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("create service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                }
                            }

                            // send email to all PAB Member for new request
                            try {
                                if (config('mail.service_enabled') && !empty($creators)) {
                                    Log::info("Ticket create email: " . json_encode($creators));
                                    Log::info("Ticket create email cc: " . json_encode($alertnotify));
                                    Mail::to($creators)->cc($alertnotify)->queue(new TicketApproverInfo($ticketProcureRequestObj, $pab));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }

                            $return["status"] = "success";
                            $return["msg"] = trans('content.service_ticket_fields.new_ticket_approval', ['id' => $ticketProcureRequestObj->id]);

                            $history = new TicketRequestHistory();
                            $history->user_id = Auth::user()->id;
                            $history->pr_id = $ticketProcureRequestObj->id;
                            $history->change_info = $return["msg"];
                            $history->save();

                            DB::commit();
                            return response()->json($return);
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    $return['msg'] = $e->getMessage();
                    return response()->json($return);
                }
            }
            DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("createRequestFromBotLtts error: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function createRequestFromBot(Request $request) {

        if(config('app.client') == "ltts"){
            return $this->createRequestFromBotLtts($request);
        }

        $return = ["status" => "fail", "msg" => trans('content.service_ticket_fields.unable_to_create_new')];
        $input = $request->all();
        Log::info("createRequestFromBot: " . json_encode($request->all()));
        $pc = ProblemCategory::find($input['problem_category_id']);
        if(isset($input['sub_category_id'])) {
            $sc = ProblemCategory::find($input['sub_category_id']);
        }
        if(isset($sc)) {
            $priority = Priority::find($sc->priority_id);
        } else {
            $priority = Priority::find($pc->priority_id);
        }
        $departmemt = Department::find($pc->department_id);

        if(isset($input['hostname'])) {
            $device = Device::where('asset_tag',$input['hostname'])->first();
        }

        $data = [
            'creator_id' => Auth::user()->id,
            'content' => $pc->name ." - ".(isset($sc) ? $sc->name : ''),
            'subject' => $pc->name ." - ".(isset($sc) ? $sc->name : ''),
            'department_id' => $departmemt->id,
            'problem_category_id' =>$pc->id,
            'device_id' => isset($device) ? $device->id : null,
            'sub_category_id' =>isset($sc) ? $sc->id : null,
            'priority_id' =>isset($sc) ? $sc->priority_id : $pc->priority_id,
        ];
        if($pc->tat == null && $sc->tat == null){
            $data['tat'] = $priority->service_time;
        } else {
            $data['tat'] = isset($sc) ? $sc->tat : $pc->tat;
        }
        DB::beginTransaction();
        try {

            /* check problem category has sub-categories */
            $problem_category = ProblemCategory::find($data['problem_category_id']);
            $sub_category = false;
            if ($problem_category->totSubCategories() > 0) {

                if (!$data['sub_category_id']) {
                    $return["msg"] = trans('content.service_ticket_fields.Please_enter_valid_sub-category');
                    return response()->json($return);
                }

                try {
                    $sub_category = ProblemCategory::findOrFail($data['sub_category_id']);
                    if ($sub_category->parent_id != $data['problem_category_id']) {
                        throw new \Exception(trans('content.service_ticket_fields.Enter_valid_sub-category'));
                    }
                } catch (\Exception $e) {
                    $return["msg"] = $e->getMessage();
                    return response()->json($return);
                }
            }

            if ($data['creator_id']) {
                $usr = User::find($data['creator_id']);
                if (empty($usr)) {
                    $return["msg"] = trans('content.service_ticket_fields.Chosen_User_is_not_found');
                    return response()->json($return);
                }
                if ($usr->checkoutBasicClearance()) {
                    $return["msg"] = trans('content.service_ticket_fields.Chosen_User_is_not_in_Active_Status');
                    return response()->json($return);
                }
                if((!empty($sub_category) && $sub_category->hierarchy_approval == 4) || (!empty($problem_category) && $problem_category->hierarchy_approval == 4)) {
                    if(empty($usr->manager)) {
                        $return["msg"] = trans('content.service_ticket_fields.Manager_is_not_found');
                        return response()->json($return);
                    }
                }
            }

            if ( ($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id == 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id == 0) ) {
                $return["msg"] = trans('content.service_ticket_fields.select_PAB');
                return response()->json($return);
            } elseif ( ($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id != 0) ) {
                try {
                    if(isset($data["sub_category_id"]) && $data["sub_category_id"] != "" && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) {
                        $problem_category = ProblemCategory::find($data["sub_category_id"]);
                    } else {
                        $problem_category = ProblemCategory::find($data["problem_category_id"]);
                    }
                    if($problem_category->is_form_required == 1 && $problem_category->form_id !== 0) {
                        $requestForm = DynamicForm::where('id', $problem_category->form_id)->first();
                    }

                    $st = Ticket::create(["creator_id" => Auth::user()->id, "created_via" => 1]);
                    $data['id'] = $st->id;

                    $ticketProcureRequestObj = new TicketProcureRequest();
                    $ticketProcureRequestData = $ticketProcureRequestObj->getDataFields($data);
                    $ticketProcureRequestData['id'] = $data['id'];
                    $ticketProcureRequestData['ticket_id'] = $data['id'];
                    $ticketProcureRequestData['sub_category_id'] = $sub_category ? $sub_category->id : null;
                    $pab_ids = explode(",", $problem_category->pab_id);
                    $pab = array();
                    foreach ($pab_ids as $key => $val) {
                        $pab[$val] = 0;
                    }
                    $ticketProcureRequestData['pab_id'] = json_encode($pab);
                    $ticketProcureRequestObj->fill($ticketProcureRequestData);
                    // $ticketProcureRequestObj->assignByHirarchy();
                    if ($ticketProcureRequestObj->creator_id) {
                        $creatorObj = User::where('id', $ticketProcureRequestObj->creator_id)->first();
                        //$ticketProcureRequestObj->location_id = ($creatorObj->location_id == "") ? Auth::user()->location_id : $creatorObj->location_id;
                        $ticketProcureRequestObj->location_id = ($creatorObj->location_id == "") ? 1 : $creatorObj->location_id;
                    } else {
                        //$ticketProcureRequestObj->location_id = Auth::user()->location_id;
                        $ticketProcureRequestObj->location_id = 1;
                    }

                    if ($ticketProcureRequestObj->save()) {


                        //save request form data
                        if(isset($requestForm) && !empty($requestForm)){
                            $forDataArray = [
                                'field_values' => $request->botFieldValues,
                                'form_id' => $requestForm->id,
                                'request_id' => $ticketProcureRequestObj->id,
                                'created_by' => Auth::user()->id
                            ];
                            $form = new RequestedForm();
                            $form->fill($forDataArray);
                            $form->save();
                        }


                        $ticketProcureRequestObj->fillRequestTag();
                        $acfe = AutoCreationAccount::where('auto_create_from_email', 1)->where('id', $ticketProcureRequestObj->department->tkt_auto_creation_id)->first();
                        $ticketProcureRequestObj->ac_email_id = (!empty($acfe)) ? $acfe->id : 0;
                        $pab_ids = explode(",", $problem_category->pab_id);
                        $pab = TicketPab::find($pab_ids[0]);
                        // 8 = System Approval
                        if(!empty($pab) && $pab->hierarchy_approval == 8) {
                            $ticketProcureRequestObj = TicketProcureRequest::find($ticketProcureRequestObj->id);
                            $ticketProcureRequestObj->update([
                                "status_id" => 3,
                                "approved_at" => Carbon::now()
                            ]);

                            if(!$ticketProcureRequestObj->save()) {
                                return $return;
                            }

                            $return["status"] = "success";
                            $return["msg"] =  trans('content.service_ticket_fields.new_ticket_system_approval', ['id' =>  $ticketProcureRequestObj->id]);

                            $history = new TicketRequestHistory();
                            $history->user_id = Auth::user()->id;
                            $history->pr_id = $ticketProcureRequestObj->id;
                            $history->change_info = $return["msg"];
                            $history->save();

                            // send email to create new request for user
                            $user = User::find($ticketProcureRequestObj->creator_id);
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager =  User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    Log::info("CreationServiceRequest Mail: " . json_encode($user->email));
                                    Mail::to($user->email)->cc($cc_emails)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error("CreationServiceRequest Mail: " . $ex->getMessage());
                            }
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $user->id);
                            $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                            $data = [
                                'title' => $notificationText,
                                'data' => $ticketProcureRequestObj,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("create service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                }
                            }
                        } else {
                            // 4 = Manager Approval
                            if (!empty($pab) && $pab->hierarchy_approval != 4) {
                                if (!$pab || $pab->totMembers() < 1) {
                                    $return['msg'] = trans('content.service_ticket_fields.No_user_found_send_request');
                                    return $return;
                                }

                                app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);

                                $pab_members = [];
                                if ($pab->hierarchy_approval == 1) {
                                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('hierarchy_level', 'asc')->get();
                                } else if ($pab->hierarchy_approval == 5) {
                                    if ($ticketProcureRequestObj->location_id != null) {
                                        $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->where('location_id', $ticketProcureRequestObj->location_id)->orderBy('id', 'asc')->get();
                                        if(count($pab_members) == 0) {
                                            $return["msg"] = trans('content.service_ticket_fields.No_user_found_send_request');
                                            return response()->json($return);
                                        }
                                    } else {
                                        $return["msg"] = trans('content.service_ticket_fields.No_user_found_send_request');
                                        return response()->json($return);
                                    }
                                } else {
                                    $pab_members = TicketPabMember::where('pab_id', '=', $pab->id)->orderBy('id', 'asc')->get();
                                }

                                foreach ($pab_members as $k => $member) {
                                    $approvalRequest = new TicketApprovalRequest();
                                    $approvalRequest->pr_id = $ticketProcureRequestObj->id;
                                    $approvalRequest->pab_id = $pab_ids[0];
                                    $approvalRequest->user_id = $member->user_id;
                                    $user = User::find($approvalRequest->user_id);
                                    $pab_member_emails[] = $user->email;
                                }
                                $pro_not_mem = array_unique(array_merge([$user->email], $pab_member_emails));
                                $creators = array_filter($pro_not_mem);
                            } else {
                                app(RequestController::class)->__sendApprovalRequest($request, $ticketProcureRequestObj);

                                $creators = [$usr->manager->email];
                            }

                            $alertnotify = null;
                            if (Settings::first()->alerts_enabled == 1) {
                                $alertnotify = CommonHelper::getGlobalAlertEmail();
                            }

                            // send email to create new request for user
                            $user = User::find($ticketProcureRequestObj->creator_id);
                            try {
                                $cc_emails = [];
                                if(!empty($user) && !empty($user->manager) && $user->manager->email != "") {
                                    $manager =  User::find($user->manager_id);
                                    $cc_emails[] = $manager->email;
                                }
                                if (config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    Log::info("Create new Service request sent mail: " . json_encode($user->email));
                                    Mail::to($user->email)->cc($cc_emails)->queue(new CreationServiceRequest($ticketProcureRequestObj, $user));
                                }
                            } catch (\Exception $ex) {
                                Log::error("create request send mail to creator: " . $ex->getMessage());
                            }
                            // send push notification
                            $notify_people = [];
                            array_push($notify_people, $user->id);
                            $notificationText = "New Service Request #".$ticketProcureRequestObj->procure_tag." has been created successfully!";
                            $data = [
                                'title' => $notificationText,
                                'data' => $ticketProcureRequestObj,
                                'notify' => $notify_people,
                            ];
                            $sendNotifications = CommonHelper::sendPushNotification($data);
                            if($sendNotifications != false) {
                                $response = json_decode($sendNotifications);
                                if(isset($response->failure) && $response->failure == 1) {
                                    Log::error("create service request id push notification:" . $ticketProcureRequestObj->id. " notification error " .json_encode($response));
                                }
                            }

                            // send email to all PAB Member for new request
                            try {
                                if (config('mail.service_enabled') && !empty($creators)) {
                                    Log::info("Ticket create email: " . json_encode($creators));
                                    Log::info("Ticket create email cc: " . json_encode($alertnotify));
                                    Mail::to($creators)->cc($alertnotify)->queue(new TicketApproverInfo($ticketProcureRequestObj, $pab));
                                }
                            } catch (\Exception $ex) {
                                Log::error($ex->getMessage());
                            }

                            $return["status"] = "success";
                            $return["msg"] = trans('content.service_ticket_fields.new_ticket_approval', ['id' => $ticketProcureRequestObj->id]);

                            $history = new TicketRequestHistory();
                            $history->user_id = Auth::user()->id;
                            $history->pr_id = $ticketProcureRequestObj->id;
                            $history->change_info = $return["msg"];
                            $history->save();

                            DB::commit();
                            return response()->json($return);
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    $return['msg'] = $e->getMessage();
                    return response()->json($return);
                }
            }
            DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("createRequestFromBot error: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function getDayForEndDate($ticketID, $key) {
        try {

            $t = Ticket::find($ticketID);
            $sr = TicketProcureRequest::find($ticketID);
            if(isset($t->custom_fields) && $t->custom_fields != NULL){
                $ret = json_decode($t->custom_fields, true);
                $dates = [];
                foreach($ret as $key=>$val){
                    array_push($dates, $val);
                }
                return $dates;
            }
            if (isset($sr->approved_day) && $sr->approved_day != null) {
                $return = date("d-m-Y H:i", strtotime(date("d-m-Y H:i") . "+ " . ($sr->approved_day) . " days"));
                return $return;
            }
            return [];
        } catch (\Exception $ex) {
            Log::error("Approved day issues: " . $ex);
            return [];
        }
    }

      // get fieldset by ticket type
    public static function getTicketTypeFieldsetsAPI($ticketType, $status) {
        try {
            $return = ['status' => 'fail', 'msg' => 'Unable to get data'];
            if (isset($status) && $status == 'ticketType') {
                $fieldsets = CustomFieldset::select('*')->where("fieldset_for_ticket_type", $ticketType)->get();
            } else {
                $fieldsets = CustomFieldset::select('*')->whereIn('id', explode(",", $ticketType))->get();
            }

            if(empty($fieldsets->toArray())) {
                $fieldsets = [];
            }

            foreach($fieldsets as $f) {
                $fields = DB::table('custom_field_custom_fieldset')->where('custom_fieldset_id',$f->id)->pluck('custom_field_id')->toArray();
                $f->field_collections = CustomField::whereIn('id', $fields)->get();
                foreach($f->field_collections as $c) {
                    $fieldOption = DB::table('custom_field_custom_fieldset')->where([
                        'custom_fieldset_id' => $f->id,
                        'custom_field_id' => $c->id
                    ])->first();
                    if($c->option_type == 2) {
                        $c->options = json_decode($c->custom_options);
                    }
                    $c->required = isset($fieldOption) ? $fieldOption->required : 0;
                }
            }
            $return = [
                'status' => 'success',
                'msg' => 'Data fetched successfully',
                'data' => $fieldsets
            ];
            return $return;
        } catch(\Exception $e) {
            Log::channel('ticket')->error("getTicketTypeFieldsetsAPI : ".$e->getMessage());
            return $return;
        }
    }

    public function checkIntentCreationForBot(Request $request) {
        try {
            $return = ['status' => 'fail', 'msg' => "I am currently unable to accurately determine the appropriate department or category for the mentioned issues. I am committed to improving my capabilities and will work diligently to enhance my performance. In the meantime, please create a ticket through the portal for assistance. Thank you for your understanding."];
            $problem_category = ProblemCategory::find($request->global_prob_cat_id);
            $sub_category = false;
            if ($problem_category->department_id != $request->global_department_id) {
                return response()->json($return);
            }

            if ($problem_category->totSubCategories() > 0) {

                if (!$request->global_sub_cat_id) {
                    return response()->json($return);
                }

                try {
                    $sub_category = ProblemCategory::findOrFail($request->global_sub_cat_id);
                    Log::error("checkIntentCreationForBot sub_category : " . json_encode($sub_category));
                    if ($sub_category->parent_id != $request->global_prob_cat_id) {
                        return response()->json($return);
                    }
                } catch (\Exception $e) {
                    Log::error("checkIntentCreationForBot sub_category try error : " . $e->getMessage());
                    return response()->json($return);
                }
            }

            if (($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id == 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id == 0)) {
                $return["msg"] = "To the best of my knowledge, the ticket you are trying to create requires additional information. Please create the ticket through the portal.";
                return response()->json($return);
            } elseif (($sub_category && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) || ($problem_category->approval_required == 1 && $problem_category->pab_id != 0)) {
                try {
                    if (isset($request->global_sub_cat_id) && $request->global_sub_cat_id != "" && $sub_category->approval_required == 1 && $sub_category->pab_id != 0) {
                        $problem_category = ProblemCategory::find($request->global_sub_cat_id);
                    } else {
                        $problem_category = ProblemCategory::find($request->global_prob_cat_id);
                    }
                    if ($problem_category->is_form_required == 1 && $problem_category->form_id !== 0) {
                        $return["formRequired"] = true;
                        $return["msg"] = "To the best of my knowledge, the ticket you are trying to create requires additional information. Please create the ticket through the portal.";
                        return response()->json($return);
                    }
                } catch (\Exception $e) {
                    Log::error("checkIntentCreationForBot: " . $e);
                    return response()->json($return);
                }
            }

            $dataToPass = [];
            if(isset($request->global_department_id)) {
                $department = Department::find($request->global_department_id);
                if(!empty($department)) {
                    $dataToPass['department_name'] = [
                        "name" => $department->name,
                        "id" => $department->id,
                    ];
                }
            }

            if(isset($request->global_prob_cat_id)) {
                $pc = ProblemCategory::find($request->global_prob_cat_id);
                if(!empty($pc)) {
                    $dataToPass['category_name'] = [
                        "name" => $pc->name,
                        "id" => $pc->id,
                    ];
                }
            }

            if(isset($request->global_sub_cat_id) && $request->global_sub_cat_id != 0) {
                $sc = ProblemCategory::find($request->global_sub_cat_id);
                if(!empty($department)) {
                    $dataToPass['subcategory_name'] = [
                        "name" => $sc->name,
                        "id" => $sc->id,
                    ];
                }
            }

            $return = ['status' => 'success', 'msg' => "Can create ticket", "data" => $dataToPass];
            // Log::error("response array : " . json_encode($return));
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error("checkIntentCreationForBot error : " . $e->getMessage());;
            return response()->json($return);
        }
    }

    public function getTicketInfoForPowerBi(Request $request) {
        try {
            $input = $request->all();
            $user = $request->getUser();
            $pass = $request->getPassword();
            if(isset($user) && empty($user)) {
                $return["msg"] = "Username or password is empty";
                return response()->json($return);
            }
            if(isset($pass) && empty($pass)) {
                $return["msg"] = "Username or password is empty";
                return response()->json($return);
            }
            if($user != "greenitcoITM" || $pass != "gr@8N@hS#~44") {
                $return["msg"] = "Username or password is wrong";
                return response()->json($return);
            }

            $rules = [
                'index'         => 'sometimes|integer',
                'search_key'    => 'sometimes|string|max:100',
                'list_size'     => 'sometimes|integer|min:1|max:200',
            ];

            $messages = [  ];

            $validator = Validator::make($request->all(), $rules,$messages);
            if ($validator->fails()) {
                return response()->json([
                    "status"    => 'fail',
                    "msg"       => 'Error in validation',
                    "errors"    => $validator->messages()
                ]);
            }

            $req = $request->all();
            $page = $request->index ? $request->index : 0;

            $take = $request->list_size ? $request->list_size : 200;
            $skip = $page * $take;

            $db = Ticket::select('tkt_tickets.id', 'tkt_tickets.subject', 'tkt_tickets.content', 'tkt_tickets.status_id', 'tkt_tickets.created_at', 'tkt_tickets.resolved_at', 's.name as status', 'pc.name as prob_cat', 'tkt_tickets.problem_category_id', 'sc.name as sub_cat', 'tkt_tickets.sub_category_id', 'p.name as priority', 'tkt_tickets.priority_id', 'tkt_tickets.tat', 'tkt_tickets.tat_expire', 'tkt_tickets.tat_remaining_mins', 'tkt_tickets.response_sla','tkt_tickets.response_sla_remaining_mins','tkt_tickets.workaround_sla','tkt_tickets.workaround_sla_remaining_mins','dep.name as dep_name', 'tkt_tickets.department_id', 'comp.name as comp_name', 'uloc.name as creator_loc', 'u.email as creator_email', 'tkt_tickets.status_id', 'tkt_tickets.assigned_to', 'tkt_tickets.creator_id', 'tkt_tickets.created_via', 'tkt_tickets.feedback', 'tkt_tickets.device_id', 'tkt_tickets.tags', 'tkt_tickets.reopened_at', 'u.base_location_id', 'bl.name as base_location_name', 'tf.remarks as feedback_comment', 'tkt_procure_requests.status_id as request_status_id', 'tkt_procure_requests.id as request_id', 'tkt_procure_requests.procure_tag as request_tag','u.is_vip_user','ticket_types.name as tkt_type_name');
            $db->addSelect(DB::raw('case when u.displayName is not null AND u.displayName != "" then u.displayName when u.employee_num is not null then concat_ws(" ", u.first_name, u.last_name, "(", u.employee_num, ")") else concat_ws(" ", u.first_name, u.last_name) end as creator_name'), 'u.business_unit', 'u.delivery_unit');
            $db->addSelect(DB::raw('case when u.job_type in (1,2) then u.ex_user_company else creator_comp.name end as creator_company'));
            $db->addSelect(DB::raw('CONCAT(cbt.first_name, " ", cbt.last_name) as created_by_technician'));
            $db->addSelect(DB::raw('concat(tkt_tickets.tat, "Hrs") as tat_hrs'));
            $db->addSelect(DB::raw('case when tkt_tickets.assigned_to is not null and tkt_tickets.assigned_to != 0 then concat_ws(" ", ta.first_name, ta.last_name) when tkt_tickets.assigned_to = 0 then "System" else "" end as assigned_to_name'));
            $db->addSelect(DB::raw('DATE_FORMAT(tkt_tickets.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
            $db->addSelect(DB::raw('DATE_FORMAT(tkt_tickets.created_at, "%d %b %Y %h:%i %p") as created_at_format'));
            $db->addSelect(DB::raw('MONTHNAME(tkt_tickets.created_at) as created_at_month'));
            // $db->addSelect(DB::raw('case when t.tat_expire > now() then DATE_FORMAT(t.tat_expire, "%Y/%m/%d %H:%i:%s") else "Overdue" end as tat_expire_format'));
            $db->addSelect(DB::raw('case when tkt_tickets.status_id in (5,6) then DATE_FORMAT(tkt_tickets.resolved_at, "%d %b %Y %h:%i %p") else "" end as resolved_at_format'));
            $db->addSelect(DB::raw('DATE_FORMAT(tkt_tickets.reopened_at, "%d %b %Y %h:%i %p") as reopened_at_format'));
            $db->addSelect(DB::raw('case when tkt_tickets.status_id = 6 then DATE_FORMAT(tkt_tickets.closed_at, "%d %b %Y %h:%i %p") else "" end as closed_at_format'));
            $db->leftJoin('departments as dep', 'tkt_tickets.department_id', '=', 'dep.id');
            $db->leftJoin('companies as comp', 'dep.company_id', '=', 'comp.id');
            $db->leftJoin('tkt_problem_categories as pc', 'tkt_tickets.problem_category_id', '=', 'pc.id');
            $db->leftJoin('tkt_problem_categories as sc', 'tkt_tickets.sub_category_id', '=', 'sc.id');
            $db->leftJoin('tkt_statuses as s', 'tkt_tickets.status_id', '=', 's.id');
            $db->leftJoin('tkt_priorities as p', 'tkt_tickets.priority_id', '=', 'p.id');
            $db->leftJoin('users as u', 'tkt_tickets.creator_id', '=', 'u.id'); // ticket raiser
            $db->leftJoin('users as cbt', 'tkt_tickets.created_by', '=', 'cbt.id'); // created_by_technician
            $db->leftJoin('locations as uloc', 'tkt_tickets.location_id', '=', 'uloc.id'); // ticket raiser's location
            $db->leftJoin('locations as bl', 'u.base_location_id', '=', 'bl.id');
            $db->leftJoin('companies as creator_comp', 'u.id', '=', 'creator_comp.id'); // ticket raiser
            $db->leftJoin('users as ta', 'tkt_tickets.assigned_to', '=', 'ta.id'); // to whom ticket getting assigned
            $db->leftJoin('tkt_procure_requests', 'tkt_tickets.id', '=','tkt_procure_requests.ticket_id');
            $db->leftJoin('ticket_types', 'ticket_types.id', '=','tkt_tickets.ticket_type');
            $db->leftJoin('tkt_followings as tf', function ($q) {
                $q->on('tkt_tickets.id', '=', 'tf.ticket_id');
                $q->where('tf.action_type', '=', 3);
            });

            $return['status'] = "success";
            $return['msg'] = "";
            $return['tot'] = $db->count();
            $return['filter_record'] = $return['tot'];
            $is_searching = false;

            $is_searching = false;
            if(isset($req["filters"])) {
                $filters = $req["filters"];

                if(isset($filters["status"]) && $filters['status'] && $filters['status'] != "null") {
                    $db->whereIn("tkt_tickets.status_id",$filters['status']);
                }
                if(isset($filters["priority"]) && $filters['priority'] && $filters['priority'] != "null") {
                    $db->whereIn("tkt_tickets.priority_id",$filters['priority']);
                }

                $based_on_possible = ['1'=>'tkt_tickets.created_at', '2'=>'tkt_tickets.assigned_to', '3'=>'tkt_tickets.resolved_at','4'=>'tkt_tickets.updated_at','5'=>'tkt_tickets.closed_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 5 ) {
                    if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                        $daterange = explode(" - ", $filters["date_range"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if ($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $db->whereRaw($whereStr);
                        }
                    }
                }
                $is_searching = true;
            }

            if($is_searching) {
                $return['filter_record'] = $db->count();
            }

            $return['current_index'] = (int) $request->index;
            $return['is_prev_index'] = $skip > 0 ? 1 : 0;
            $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;

            $db->skip($skip);
            $db->take($take);

            $results = $db->get();
            $ticketReportController = new ReportTicketController();

            $config = Config::first();
            $data = [];
            foreach($results as $r) {
                $r = ($ticketReportController->newTicketReportData($r->toArray()));

                // /* capture last comment */
                $final_comment = null;
                if($r->status_id == 5 || $r->status_id == 6) {
                    $final_comment = TktFollowing::where("ticket_id", $r->id)->whereIn('updated_status', [5, 6])->whereNotNull('remarks')->orderBy('updated_at', 'desc')->first();
                }

                // /* ticket assigned datetime and assigned by whom */
                $ticket_assigned_at = null;
                $ticket_assigned_by_whom = null;
                if($r->assigned_to) {
                    $get_assigned_at = TktFollowing::where("ticket_id", $r->id)->whereIn('action_type', [1, 2])->where('assigned_to', $r->assigned_to)->orderBy('created_at', 'desc')->limit(1)->first();

                    if($get_assigned_at && !empty($get_assigned_at)) {
                        $ticket_assigned_at = CommonHelper::getDateAs($get_assigned_at->updated_at, "d M Y h:i A", "Y-m-d H:i:s");
                        if( $get_assigned_at->updatedBy ) {
                            $ticket_assigned_by_whom = $get_assigned_at->updatedBy->fullName() . " (" . $get_assigned_at->updatedBy->username . ")";
                        }
                    }
                    else {
                        $ticket_assigned_at = $r->created_at_format;
                    }
                }

                // /* ticket handler first comment/update */
                $ticket_handler_first_update_at = null;
                if($r->assigned_to) {
                    $get_handler_first_update = TktFollowing::where("ticket_id", $r->id)->whereIn('action_type', [2,7])->whereNull('assigned_to')->where('updated_by', '!=', $r->creator_id)->orderBy('created_at', 'asc')->limit(1)->first();

                    if($get_handler_first_update && !empty($get_handler_first_update)) {
                        $ticket_handler_first_update_at = CommonHelper::getDateAs($get_handler_first_update->updated_at, "d M Y h:i A", "Y-m-d H:i:s");
                    }
                }

                // /* ticket handler first response_at */
                $ticket_handler_first_response_at = null;
                if($r->assigned_to && isset($r->response_sla) && $r->response_sla != NULL && $r->response_sla !='') {
                    $get_handler_first_response = TktFollowing::where("ticket_id", $r->id)->whereIn('action_type', [2,7])->whereNotNull('assigned_to')->where('updated_by', '!=', $r->creator_id)->orderBy('created_at', 'asc')->limit(1)->first();
                    if($get_handler_first_response && !empty($get_handler_first_response)) {
                        $ticket_handler_first_response_at = CommonHelper::getDateAs($get_handler_first_response->updated_at, "d M Y h:i A", "Y-m-d H:i:s");
                    }
                }

                // /* ticket handler workaround_at */
                $ticket_handler_workaround_at = null;
                if($r->assigned_to && isset($r->workaround_sla) && $r->workaround_sla != NULL && $r->workaround_sla !='') {
                    $get_handler_workaround = TktFollowing::where("ticket_id", $r->id)->whereIn('action_type', [2,7])->whereNotNull('assigned_to')->where('is_workaround',1)->where('is_valid_workaround',1)->orderBy('created_at', 'asc')->limit(1)->first();
                    if($get_handler_workaround && !empty($get_handler_workaround)) {
                        $ticket_handler_workaround_at = CommonHelper::getDateAs($get_handler_workaround->updated_at, "d M Y h:i A", "Y-m-d H:i:s");
                    }
                }

                // /* ticket creator first comment/update if any */
                $ticket_creator_first_update_at = null;
                $get_creator_first_update = TktFollowing::where("ticket_id", $r->id)->whereIn('action_type', [2,7])->whereNull('assigned_to')->where('updated_by', '=', $r->creator_id)->orderBy('created_at', 'asc')->limit(1)->first();

                if($get_creator_first_update && !empty($get_creator_first_update)) {
                    $ticket_creator_first_update_at = CommonHelper::getDateAs($get_creator_first_update->updated_at, "d M Y h:i A", "Y-m-d H:i:s");
                }

                $field_wise_data = [];
                if($config->isVisibleField(1)) {
                    $ticket_id = $r->id;
                    if(isset($r->request_status_id) && isset($r->request_id) && $r->request_status_id == 3 && $r->request_status_id != null && $r->request_status_id != "") {
                        $ticket_id = $ticket_id . ' ('.$r->request_tag.')';
                    }
                    $field_wise_data['ticket_id'] = '#' . $ticket_id;
                }
                if($config->isVisibleField(2)) {
                    $field_wise_data['request_tag'] = isset($r->request_tag) ? '#'.$r->request_tag : '';
                    $field_wise_data['subject'] = CommonHelper::decodeText($r->subject);
                    $field_wise_data['content'] = html_entity_decode(strip_tags($r->content));
                }

                if($config->isVisibleField(3)) {
                    $field_wise_data['status'] = $r->status;
                }
                if($config->isVisibleField(4)) {
                    $field_wise_data['problem_category'] = $r->prob_cat;
                }
                if($config->isVisibleField(12)) {
                    $field_wise_data['sub_cat'] = $r->sub_cat;
                }
                if($config->isVisibleField(5)) {
                    $field_wise_data['priority'] = $r->priority;
                }
                if($config->isVisibleField(6)) {
                    $field_wise_data['department'] = $r->dep_name;
                }
                if($config->isVisibleField(7)) {
                    $field_wise_data['assigned_to_name'] = $r->assigned_to_name;
                    $field_wise_data['ticket_assigned_at'] = $ticket_assigned_at;
                    $field_wise_data['ticket_assigned_by_whom'] = $ticket_assigned_by_whom;
                }
                if($config->isVisibleField(8)) {
                    $field_wise_data['created_at_format'] = $r->created_at_format;
                    $field_wise_data['created_at_month'] = $r->created_at_month;
                }
                if($config->isVisibleField(9)) {
                    $field_wise_data['updated_at_format'] = $r->updated_at_format;
                }
                if($config->isVisibleField(10)) {
                    $field_wise_data['resolved_at_format'] = $r->resolved_at_format;
                    $field_wise_data['reopened_at_format'] = $r->reopened_at_format;
                    $field_wise_data['closed_at_format'] = $r->closed_at_format;
                }
                if($config->isVisibleField(11)) {
                    $field_wise_data['creator_name'] = $r->creator_name;
                    $field_wise_data['creator_company'] = $r->creator_company;
                    $field_wise_data['creator_email'] = $r->creator_email;
                    $field_wise_data['creator_loc'] = $r->creator_loc;
                    $field_wise_data['base_location_name'] = $r->base_location_name;
                }
                if($config->isVisibleField(13)) {
                    $createdVia = [
                        "1" => "Portal",
                        "2" => "Chat",
                        "3" => "Email",
                        "4" => "Mobile",
                        "5" => "Call",
                        "6" => "BOT"
                    ];
                    $field_wise_data['created_via'] = isset($createdVia[$r->created_via]) ? $createdVia[$r->created_via] : $r->created_via;
                }
                if($config->isVisibleField(14)) {
                    $device = Device::find($r->device_id);
                    $field_wise_data['device'] = $device && isset($device->asset_tag) ? $device->asset_tag : '';
                }
                if($config->isVisibleField(16)) {
                    $field_wise_data['feedback'] = isset($r->feedback) ? $r->feedback : '';
                    $field_wise_data['feedback_comment'] = isset($r->feedback_comment) ? $r->feedback_comment : '';
                }
                if($config->isVisibleField(17)) {
                    $tagsArray = explode(",", $r->tags);
                    $tagsText = '';
                    if($tagsArray) {
                        foreach($tagsArray as $tag) {
                            $tag = Tags::find($tag);
                            if($tag) {
                                if($tagsText == '') {
                                    $tagsText = $tag->tags;
                                } else {
                                    $tagsText = $tagsText.", ".$tag->tags;
                                }
                            }
                        }
                    }
                    $field_wise_data['tags'] = isset($tagsText) ? $tagsText : '';
                }
                $field_wise_data['remarks'] = $final_comment ? strip_tags($final_comment->remarks) : null;
                $field_wise_data['ticket_handler_first_update_at'] = $ticket_handler_first_update_at;
                $field_wise_data['ticket_creator_first_update_at'] = $ticket_creator_first_update_at;
                $field_wise_data['sla_breached'] = isset($r->sla_breached) ? $r->sla_breached : '';
                $field_wise_data['sla_breach_time'] = isset($r->sla_breach_time) ? $r->sla_breach_time : '';
                $field_wise_data['response_sla'] = isset($r->response_sla) ? CommonHelper::getDateAs($r->response_sla, "d M Y h:i A", "Y-m-d H:i:s") : '';
                $field_wise_data['response_sla_breach'] = isset($r->response_sla_breach) ? $r->response_sla_breach : '';
                $field_wise_data['ticket_handler_first_response_at'] = isset($ticket_handler_first_response_at) ? $ticket_handler_first_response_at : '';
                $field_wise_data['workaround_sla'] = isset($r->workaround_sla) ? CommonHelper::getDateAs($r->workaround_sla, "d M Y h:i A", "Y-m-d H:i:s") : '';
                $field_wise_data['workaround_sla_breach'] = isset($r->workaround_sla_breach) ? $r->workaround_sla_breach : '';
                $field_wise_data['ticket_handler_workaround_at'] = isset($ticket_handler_workaround_at) ? $ticket_handler_workaround_at : '';
                $field_wise_data['wfu_more_than_2_days'] = isset($r->wfu_more_than_2_days) ? $r->wfu_more_than_2_days : '';
                $field_wise_data['service_request'] = isset($r->request_tag) ? 'Yes' : 'No';
                $field_wise_data['tkt_type_name'] = isset($r->tkt_type_name) ? $r->tkt_type_name : 'Normal';
                $field_wise_data['created_by_technician'] = isset($r->created_by_technician) ? $r->created_by_technician : '';
                $data[] = $field_wise_data;
            }

            $return['data'] = $data;
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("getTicketInfoForPowerBi: " . $e->getMessage());
            return response()->json($return);
        }
    }
    public function getRelatedTask($ticket_id)
    {
        try {
            $tasks = Task::select('id', 'name', 'description', 'status_id', 'priority_id','assigned_to', 'start_date', 'due_date', 'end_date','cost', 'created_at', 'updated_at','is_visible_user')->where('ticket_id', $ticket_id)->get();
            $completedTasks = $tasks->whereIn('status_id', [7, 9])->count();
            $completionPercentage = $tasks->count() > 0 ? round(($completedTasks / $tasks->count()) * 100) : 0;
            $taskdata = [];
            foreach ($tasks as $task) {
                $creatorId = TaskHistory::where('task_id', $task->id)->where('action_id', 1)->value('change_by');
                $creator = !empty($creatorId)? User::find($creatorId) : null;
                $creatorName = !empty($creator) ? $creator->fullName() : null;
                $commentsQuery = DB::table('task_history as th')->leftJoin('users as u', 'th.change_by', '=', 'u.id')->where('th.task_id', $task->id)->where('th.action_id', 4)->select('th.remarks','th.id as tfid','th.is_note','th.change_by as updated_by',
                        DB::raw('concat(u.first_name, " ", u.last_name, " @ ", u.username) as commenter'),
                        DB::raw('DATE_FORMAT(th.updated_at, "%d %b %y %h:%i %p") as updated_at_format')
                    )
                    ->orderBy('th.updated_at', 'desc')
                    ->get();

                $embedded_attachments = TaskManagementAttachment::getEmbeddedAttachments($task->id);
                $attachment_path = TaskManagementAttachment::getAppUrl('');
                $attachment_view = TaskManagementAttachment::getAppViewUrl();
                foreach ($commentsQuery as $comment) {
                    $comment->attachments = TaskManagementAttachment::where('following_id', '=', $comment->tfid)->select('id','following_id as tfid','original_file_name as name', 'extension as ext',DB::raw('case when id is not null then concat_ws("", "'.$attachment_path.'/",id) else "" end as attach_file_path'),DB::raw('case when id is not null then concat_ws("", "'.$attachment_view.'/",id) else "" end as attach_view'), DB::raw('case when thumbnail is not null then 1 else 0 end as thumb'))->get();

                }

                $taskdata[] = [
                    'id' => $task->id,
                    'progress' => app(TicketController::class)->calculateProgress($task->start_date, $task->due_date,$task),
                    'name' => $task->name,
                    'description' => $task->description,
                    'status_id' => $task->status_id,
                    'status_name' => $task->status->name ?? null,
                    'priority_id' => $task->priority_id,
                    'priority_name' => $task->priority->name ?? null,
                    'assigned_to' => $task->assigned_to,
                    'assigned_to_name' => $task->assigned_to ? $task->assignedTo->fullName() : null,
                    'start_date' => $task->start_date ? Carbon::parse($task->start_date)->format('d M Y h:i A') : null,
                    'due_date' => $task->due_date ? Carbon::parse($task->due_date)->format('d M Y h:i A') : null,
                    'end_date' => $task->end_date ? Carbon::parse($task->end_date)->format('d M Y h:i A') : null,
                    'cost' => $task->cost,
                    'creator'=> $creatorName ,
                    'created_at' => $task->created_at ? $task->created_at->format('d M Y h:i A') : null,
                    'updated_at' => $task->updated_at ? $task->updated_at->format('d M Y h:i A') : null,
                    'assign_to_avatar' => $task->assignedTo ? $task->assignedTo->getProfileImg(): asset("imgs/profile-75.jpg"),
                    'creator_avatar' => $creator ? $creator->getProfileImg(): asset("imgs/profile-75.jpg"),
                    'change_by_module' => optional($task->firstTaskHistory->first())->change_by_module,
                    'is_visible_user' => $task->is_visible_user,
                    'comments' => $commentsQuery,
                ];
            }
            $response = [
                'tasks' => $taskdata,
                'completed_tasks' => $completedTasks,
                'completion_percentage' => $completionPercentage,
            ];
        return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()], 500);
        }
    }
}
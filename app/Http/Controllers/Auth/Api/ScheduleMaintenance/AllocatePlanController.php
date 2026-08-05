<?php

namespace App\Http\Controllers\Auth\Api\ScheduleMaintenance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceList;
use App\Models\ScheduleMaintenance\ScheduleMaintenancePlanAllocation;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceCron;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceTask;
use App\Models\ScheduleMaintenance\MaintenanceTask;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceStatus;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceStatusHistory;
use App\Models\ScheduleMaintenance\PlanReferenceGuideAttachment;
use App\Models\ScheduleMaintenance\TaskAttachment;
use App\Models\ScheduleMaintenance\TaskHistory;
use App\Models\Department;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Attachment;
use DB;
use Illuminate\Support\Str;
use Storage;
use App\Models\Device;
use Log;
use Validator;
use AUth;
use Image;
use File;
use App\Models\Model;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Category;
use App\Helpers\Common as CommonHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NewScheduleMaintenanceListExcel;
use App\Exports\MyAssignedPlanExcel;
use Hash;
use Mail;
use Carbon\Carbon;
use App\Mail\ScheduleMaintenance\AllocatedPlanNotification;
use App\Mail\ScheduleMaintenance\AllocatedStatusUpdateNotification;
use App\Mail\ScheduleMaintenance\AllocatedTaskUpdateNotification;
use App\Mail\ScheduleMaintenance\PlanApprovalNotification;
use App\Mail\ScheduleMaintenance\PlanSendToApproverNotification;
use App\Mail\UserCredentialNotification;
use Spatie\Permission\Models\Role;
use Cron\CronExpression;
use DateTime;
use DateTimeZone;
use App\Jobs\AllocateSchedulePlan;
use App\Jobs\DeleteAssignPlan;
use Illuminate\Validation\Rule;
use App\Helpers\Common;
use App\Http\Controllers\Ticket\IndexController;
use App\Http\Traits\ApiResponse as TraitsApiResponse;

class AllocatePlanController extends Controller
{
    use TraitsApiResponse;
    public function myAllocatedPlan(Request $request)
    {
        try {
            $req = $request->all();

            $rules = [
                'index'         => 'sometimes|integer|max:100',
                'search_key'    => 'sometimes|max:100',
                'list_size'     => 'sometimes|integer|min:1|max:20',
                'order_by'      => 'sometimes|integer|min:0',
                'order_dir'     => 'required_with:order_by|integer|min:0|max:1'
            ];

            $messages = [];

            $validator = Validator::make($request->all(), $rules,$messages);
            if ($validator->fails()) {
                return $this->fail(422, 'Error in validation', $validator->messages());
            }

            $fields = array(
                '1'  => 'cat_name',
                '2'  => 'model_name',
                '3'  => 'asset_tag',
                '4'  => 'plan_title',
                '5'  => 'full_name',
                '6'  => 'status',
                '7' => 'scheduled_date',
                '8'  => 'schedule_maintenance.updated_at',
            );

            $order = [
                0 => 'asc',
                1 => 'desc'
            ];

            if (!isset($req['order_by']) || !array_key_exists($req["order_by"], $fields)) {
                $req['order_by'] = 8; // set default order by updated_at
                $req['order_dir'] = isset($req['order_dir']) ? $req['order_dir'] : 1; // set default sort by desc
            }

            if (!isset($req['tab'])) {
                $req['tab'] = 'ongoing';
            }
            $page = $request->index ? $request->index : 0;

            $take = $request->list_size ? $request->list_size : 20;
            $skip = $page * $take;

            $query = ScheduleMaintenanceCron::select('schedule_maintenance.id','schedule_maintenance.allocated_id','schedule_maintenance.plan_id','sta.color','sta.file_name','schedule_maintenance.device_id','schedule_maintenance.status as status_id','c.name as cat_name','m.name as model_name','s.name as supplier_name','p.schedule_maintenance_name as plan_title',DB::raw('concat(u.first_name, " ", u.last_name) as full_name'),'schedule_maintenance.device_id', 'sta.status as status', 'a.asset_tag as asset_tag');
            $query->addSelect(DB::raw('DATE_FORMAT(schedule_maintenance.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
            // $query->addSelect(DB::raw('case when schedule_maintenance.status= 1 then "Scheduled" when schedule_maintenance.status= 2 then "InProgress" when schedule_maintenance.status = 3 then "Complete" when schedule_maintenance.status= 4 then "Hold" when schedule_maintenance.status = 5 then "Canceled" when schedule_maintenance.status = 6 then "Preponed" when schedule_maintenance.status = 7 then "Postponed" when schedule_maintenance.status = 8 then "Delayed" else "" end as status'));
            $query->addSelect(DB::raw('DATE_FORMAT(schedule_maintenance.schedule_date, "%d %b %Y %h:%i %p") as scheduled_date'));
            $query->leftJoin('users as u', 'u.id', '=', 'schedule_maintenance.handler_id');
            $query->leftJoin('categories as c', 'c.id', '=', 'schedule_maintenance.category_id');
            $query->leftJoin('assets as a', 'a.id', '=', 'schedule_maintenance.device_id');
            $query->leftJoin('models as m', 'm.id', '=', 'schedule_maintenance.model_id');
            $query->leftJoin('suppliers as s', 's.id', '=', 'schedule_maintenance.supplier_id');
            $query->leftJoin('schedule_maintenance_lists as p', 'p.id', '=', 'schedule_maintenance.plan_id');
            $query->leftJoin('schedule_maintenance_statuses as sta', 'sta.id', '=', 'schedule_maintenance.status');
            $query->whereNull('schedule_maintenance.deleted_at');
            $query->where(function($q) {
                $q->where('s.user_supplier_id', Auth::user()->id);
                $q->orWhere('schedule_maintenance.handler_id', Auth::user()->id);
            });
            if ($req['tab'] == 'ongoing') {
                $query->whereNotIn('schedule_maintenance.status', [3, 5]);
                $query->where(function($q) {
                    $q->where('schedule_maintenance.schedule_date','<=', Carbon::now()->addDay()->format('Y-m-d 00:00:00'));
                    $q->orWhere('schedule_maintenance.status', 2);
                });
            } else if ($req['tab'] == 'week') {
                $weekStartdate = Carbon::now()->addDay()->format('Y-m-d H:i:00');
                $weekEndDate = Carbon::now()->endOfWeek()->format('Y-m-d H:i');
                $query->whereNotIn('schedule_maintenance.status', [3, 5]);
                $query->where(function($q) use($weekStartdate, $weekEndDate) {
                    $q->whereBetween('schedule_maintenance.schedule_date', [$weekStartdate, $weekEndDate]);
                    $q->where('schedule_maintenance.status', '!=', 2);
                });
            } else if ($req['tab'] == 'month') {
                $monthStartdate = Carbon::now()->addDay()->format('Y-m-d H:i:00');
                $monthEndDate = Carbon::now()->endOfMonth()->format('Y-m-d H:i:00');
                $query->whereNotIn('schedule_maintenance.status', [3, 5]);
                $query->where(function($q) use($monthStartdate, $monthEndDate) {
                    $q->whereBetween('schedule_maintenance.schedule_date', [$monthStartdate, $monthEndDate]);
                    $q->where('schedule_maintenance.status', '!=', 2);
                });
            } else if ($req['tab'] == 'year') {
                $query->whereNotIn('schedule_maintenance.status', [3, 5]);
                $yearStartdate = Carbon::now()->addDay()->format('Y-m-d H:i:00');
                $yearEndDate = Carbon::now()->endOfYear()->format('Y-m-d H:i:00');
                $query->where(function($q) use($yearStartdate, $yearEndDate) {
                    $q->whereBetween('schedule_maintenance.schedule_date', [$yearStartdate, $yearEndDate]);
                    $q->where('schedule_maintenance.status', '!=', 2);
                });
            } else if ($req['tab'] == 'completed') {
                $query->where('schedule_maintenance.status', 3);
            }

            $return['status'] = "success";
            $return['msg'] = "";
            $return['tot'] = $query->count();
            $return['filter_record'] = $return['tot'];

            $is_searching = false;
            if(isset($req["search_key"]) && $search_key = trim($req["search_key"])) {
                $whereStr = sprintf('((case when schedule_maintenance.status= 1 then "Scheduled" when schedule_maintenance.status= 2 then "InProgress" when schedule_maintenance.status = 3 then "Complete" when schedule_maintenance.status= 4 then "Hold" when schedule_maintenance.status = 5 then "Canceled" when schedule_maintenance.status = 6 then "Preponed" when schedule_maintenance.status = 7 then "Postponed" when schedule_maintenance.status = 8 then "Delayed" else "" end) like "%%%1$s%%" or c.name like "%%%1$s%%" or m.name like "%%%1$s%%" or a.name like "%%%1$s%%" or s.name like "%%%1$s%%" or p.title like "%%%1$s%%" or u.first_name like "%%%1$s%%" or DATE_FORMAT(schedule_maintenance.schedule_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                $query->whereRaw($whereStr);
                $is_searching = true;
                $return['search_key'] = $search_key;
            }
            if($is_searching) {
                $return['filter_record'] = $query->count();
            }
    
            $return['current_index'] = (int) $request->index;
            $return['is_prev_index'] = $skip > 0 ? 1 : 0;
            $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;

            if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
                $query->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
            }
    
            $query->skip($skip);
            $query->take($take);

            $data = $query->get();
            $return['data'] = array();

            foreach($data as $key => $d) {
                $dev_p = Device::find($d->device_id);
                $dev_id_Text = $dev_p->asset_tag;
                $d['device_tag'] = isset($dev_id_Text) ? $dev_id_Text : '';
                $d['percentage'] = $d->tasks()->exists() ? round(($d->tasks->where('status', 1)->count()/$d->tasks->count())*100, 2) : 0;
                $d['cost'] = $d->tasks()->exists() ? $d->tasks()->sum('cost') : 0;
                $d['p'] = !empty($d->plan->projected_cost) ? $d->plan->projected_cost : 0;
                $d['file_name'] = asset($d->file_name);

                if(!empty($d->supplier_name)) {
                    $d['assigned_person'] = $d['supplier_name'];
                } else {
                    $d['assigned_person'] = $d['full_name'];
                }
                $return['data'][] = $d;
            }
        } catch (\Exception $e) {
            return $this->fail(500, 'Unable to fetch Schedule Maintenance Plan', $e->getMessage());
        }
        return $this->success($return, '');
    }

    public function getAllocatedTaskList(Request $request, $id) {
        if (!Auth::user()->hasPermissionTo('MyAssignPlanTaskView')) {
            return $this->fail(500, trans('content.scheduled_maintenance.permission_denied'), '');
        }
        try {
            $task = DB::table('schedule_maintenance_tasks as t');
            $task->leftJoin('maintenance_tasks as mt', 'mt.id', '=', 't.task_id');
            $task->leftJoin('task_attachments as ta', 'ta.task_id', '=', 't.id');
            $task->addSelect('t.id', 't.task_id', 't.task_remark', 't.status', 't.order_no', 't.latitude', 't.longitude', 't.cost', 't.comment', 't.location', 'mt.title as task_title', 'mt.description as task_description', 'mt.notes as task_notes', 'mt.gps_required as gps_required', 'mt.proof_required as proof_required', 'mt.downtime as downtime_required', 'mt.downtime_duration as downtime_duration', DB::raw('case when ta.task_id is not null then ta.file_name else null end as image_path'));
            $task->where('t.plan_id', $id);
            $task->whereNull('t.deleted_at');
            $task->orderBy('t.order_no');
            $tasks = $task->get();

            $schedule_maintenance_allocation = ScheduleMaintenanceCron::findOrFail($id);
            if ($tasks->count() == 0) {
                foreach ($schedule_maintenance_allocation->plan->task as $task) {
                    $scheduleMaintenanceTask = new ScheduleMaintenanceTask;
                    $scheduleMaintenanceTask->task_id = $task->id;
                    $scheduleMaintenanceTask->plan_id = $id;
                    $scheduleMaintenanceTask->order_no = $task->order_no;
                    $scheduleMaintenanceTask->save();
                }
                $task = DB::table('schedule_maintenance_tasks as t');
                $task->leftJoin('maintenance_tasks as mt', 'mt.id', '=', 't.task_id');
                $task->addSelect('t.id', 't.task_id', 't.status', 't.order_no', 't.latitude', 't.longitude', 't.cost', 't.comment', 't.location', 'mt.title as task_title', 'mt.description as task_description', 'mt.notes as task_notes', 'mt.gps_required as gps_required', 'mt.proof_required as proof_required', 'mt.downtime as downtime_required', 'mt.downtime_duration as downtime_duration');
                $task->where('t.plan_id', $id);
                $task->whereNull('t.deleted_at');
                $task->orderBy('t.order_no');
                $tasks = $task->get();
            } else {
                foreach ($tasks as $task) {
                    if($task->image_path != null) {
                        $task->image_path = asset('uploads/task/'.$task->image_path);
                    }
                }
            }
            $db = DB::table('schedule_maintenance as sm');
            $db->leftJoin('schedule_maintenance_lists as sml', 'sml.id', '=', 'sm.plan_id');
            $db->leftJoin('schedule_maintenance_statuses as sms', 'sms.id', '=', 'sm.status');
            $db->leftJoin('users as u', 'u.id', '=', 'sml.manager_id');
            $db->leftJoin('users as us', 'us.id', '=', 'sml.created_by');
            $db->select('sml.schedule_maintenance_name as plan_name', 'sml.duration', 'u.username as manager', 'us.username as created_by', 'sm.created_at', 'sm.updated_at', 'sm.schedule_date', 'sml.description','sms.status');
            $db->addSelect(DB::raw('case when sml.recursion_plan = 1 then "One Time" when sml.recursion_plan = 2 then "Daily" when sml.recursion_plan = 3 then "Weekly" when sml.recursion_plan = 4 then "Monthly" when sml.recursion_plan = 5 then "Yearly" else "" end as recursion_plan'));
            $db->where('sm.id', $id);
            $return['plan'] = $db->first();
            $return['percentage'] = $schedule_maintenance_allocation->tasks()->exists() ? round(($schedule_maintenance_allocation->tasks->where('status', 1)->count()/$schedule_maintenance_allocation->tasks->count())*100, 2) : 0;
            $return['last_order'] = $tasks->pluck('order_no')->last();
            $return['first_order'] = $tasks->pluck('order_no')->first();
            $attachment = PlanReferenceGuideAttachment::where('plan_id', $schedule_maintenance_allocation->plan_id)->first();

            $attach = [];
            if ($attachment) {
                $attach['path'] = asset('uploads/plan_guide/'.$attachment->file_name);
                $attach['file_name'] = $attachment->original_file_name;
                $attach['extension'] = $attachment->extension;
            }
            $return['tasks'] = $tasks;
            $return['attach'] = $attach;
            return $this->success($return, '');
        } catch (\Exception $e) {
            Log::error("getAllocatedTaskList: " . $e->getMessage());
            return $this->fail(500, 'Unable to get Task', $e->getMessage());
        }
    }

    public function updateMyTask(Request $request) {
        if (!Auth::user()->hasPermissionTo('MyAssignPlanTaskUpdate')) {
            return $this->fail(500, trans('content.scheduled_maintenance.permission_denied'), '');
        }
        try {
            $req = $request->all();
            $task = ScheduleMaintenanceTask::findOrFail($req['task_id']);
            $getNextPendingTask = ScheduleMaintenanceTask::where('plan_id', $req['plan_id'])->where('status', 0)->first();
            //check if task is performed in order
            if ($task->order_no > $getNextPendingTask->order_no) {
                return $this->fail(500, 'Please complete all the previous task(s) first', '');
            }
            $allocated_plan = ScheduleMaintenanceCron::find($task->plan_id);
            if ($allocated_plan && $allocated_plan != null && $allocated_plan->status != 2) {
                return $this->fail(500, 'Please change status of this plan to InProgress to update the task', '');
            }
            $maintenance_task = MaintenanceTask::select('gps_required', 'proof_required', 'title')->where('id', $task->task_id)->first();

            $validate = Validator::make($req, [
                'latitude' => Rule::requiredIf($maintenance_task->gps_required === 1),
                'longitude' => Rule::requiredIf($maintenance_task->gps_required === 1),
                'location' => Rule::requiredIf($maintenance_task->gps_required === 1),
                // 'proof' => Rule::requiredIf($maintenance_task->proof_required === 1).'|file',
                'task_remark' => in_array(config('app.client'), ['rolepermission', 'ril']) == true ? 'required': 'nullable',
            ]);

            if ($validate->fails()) {
                return $this->fail(422, 'Error in validation', $validate->messages());
            }

            $task->location = isset($req['location']) ? $req['location'] : null;
            $task->latitude = isset($req['latitude']) ? $req['latitude'] : null;
            $task->longitude = isset($req['longitude']) ? $req['longitude'] : null;
            $task->comment = isset($req['comment']) ? $req['comment'] : null;
            if(in_array(config("app.client"), ["rolepermission", "ril"])) {
                $task->task_remark = isset($req['task_remark']) ? $req['task_remark'] : null;
            }
            $task->status = 1;
            $task->cost = !empty($req['cost']) ? $req['cost'] : 0;
            // if(isset( $req['proof'])){
            //     $attachment = $req['proof'];
            //     unset($req['proof']);
            // }

            if ($task->save()) {

                if(in_array(config('app.client'), ["rolepermission", "ril"])) {
                    if(isset($req['task_remark']) && $task->task_remark == 2){
                        $data = '';
                        $data .= 'Title: ' . $maintenance_task->title . '<br><br>';
                        if (isset($req['cost']) && $req['cost'] != null) {
                            $data .= 'Cost: ' . $task->cost . '<br><br>';
                        }
                        if (isset($req['location']) && $req['location'] != null) {
                            $data .= 'Location: ' . $task->location . '<br><br>';
                        }
                        if (isset($req['latitude']) && $req['latitude'] != null) {
                            $data .= 'Latitude: ' . $task->latitude . '<br><br>';
                        }
                        if (isset($req['longitude']) && $req['longitude'] != null) {
                            $data .= 'Longitude: ' . $task->longitude . '<br><br>';
                        }
                        if (isset($req['comment']) && $req['comment'] != null) {
                            $data .= 'Comment: ' . $task->comment . '<br>';
                        } 
                        $department = Department::where('name', 'Checklist')->first();
                        if($department == null){
                            $return["status"] = "failure";
                            $return["msg"] = 'Please add a Checklist Department';
                            return response()->json($return);
                        }
                        $probCategory = DB::table('tkt_problem_categories')->where('name','Checklist Issue')->where('department_id',$department->id)->whereNull('deleted_at')->first();
                        if($probCategory == null){
                            $return["status"] = "failure";
                            $return["msg"] = 'Please add a Checklist Issue problem category';
                            return response()->json($return);
                        }
                        try {
                            $obj = new IndexController;
                            $data =  [
                                'creator_id' => Auth::user()->id,
                                'content' => $data,
                                'subject' => $probCategory->name.' ##'.$task->plan_id.' ##'.$task->plan->plan->schedule_maintenance_name. ' ##'.$task->task->title,
                                'department_id' => $department->id,
                                'problem_category_id' => $probCategory->id,
                                'priority_id' => 1,
                                'schedule_maintenance_id' => $req['plan_id'],
                                'tat' => $probCategory->tat, 
                            ];
                            $requ = new Request($data);
                            $createTicket = $obj->create($requ);
                            $ticketResponse = json_decode(json_encode($createTicket), true);

                            if(isset($attachment) && !empty($attachment)){
                                $filePath = date("Y").'/'.date('m').'/'.date('d');
                                $checkFolderPath = CommonHelper::attachmentFolderStructure('tickets', $filePath);
                                if(!$checkFolderPath) {
                                    $return["msg"] = "Attachment Directory not found";
                                    return response()->json($return);
                                }
                                
                                $a = new Attachment();
                                $a->ticket_id = $ticketResponse['original']['ticket_id'];

                                $a->original_file_name = $request->file('proof')->getClientOriginalName();
                                $a->original_file_name = $a->trimUnfittedName($a->original_file_name);
                                $a->extension = strtolower($request->file('proof')->getClientOriginalExtension());
                                $a->file_name = $request->file('proof')->store($filePath, "tickets");
                                $a->attachment_link = $a->file_name;
                                $a->uploader_id = Auth::user()->id;
                                $a->tmp_id = null;
                                
                                if(!$a->file_name || !$a->save()) {
                                    return response()->json($return);
                                }
                    
                                /* generate thumb if image */
                                if( in_array($a->extension, ["png", "jpeg", "jpg"]) !== false ) {
                                    try {
                                        $path = storage_path('tkt_attachments') . DIRECTORY_SEPARATOR . $a->file_name;
                                        $thumb_name = $a->getThumbName();
                                        if(! $thumb_name) {
                                            throw new \Exception("Invalid Image Name " . $a->ticket_id);
                                        }
                                        $thumb_path = storage_path('tkt_attachments') . DIRECTORY_SEPARATOR . $thumb_name;
                    
                                        Image::make($path)->resize(100, null, function($constraint) {
                                            $constraint->aspectRatio();
                                            $constraint->upsize();
                                        })->save($thumb_path);
                    
                                        $a->thumbnail = $thumb_name;
                                        $a->save();
                                    }
                                    catch(\Exception $e) {
                                        Log::error($e->getMessage());
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error($e->getMessage());
                            return response()->json($return);
                        }
                    }
                }
                //store changes in task history table
                $task_history = new TaskHistory;
                $task_history->action_type = 1;
                $task_history->task_id = $task->task_id;
                $task_history->plan_id = $task->plan_id;
                $task_history->updated_by = Auth::user()->id;
                $task_history->save();

                //update step no on schedulemaintenance table
                $plan = ScheduleMaintenanceCron::findOrFail($task->plan_id);
                $plan->task_step = ( $plan->task_step < $task->order_no+1 && $req['last_step'] > $task->order_no) ? $task->order_no+1 : $plan->task_step;
                $tk = ScheduleMaintenanceTask::where('plan_id', $task->plan_id)->where('status', 0)->whereNull('deleted_at')->get();
                if ($req['last_step'] == $plan->task_step && $tk->count() == 0) {
                    $plan->status = 3;
                    if ($plan->plan->approval_required == 1) {
                        $plan->status = 9;
                    }
                }

                /* if all task completed, update status history table to completed/send to approval,
                    check if approval required:if yes send mail to approver else send task completed mail to user
                */
                if ($plan->save()) {
                    $history = new ScheduleMaintenanceStatusHistory;
                    $history->action_type = 1;
                    $history->allocation_id = $plan->id;
                    $history->status_id = $plan->status;
                    $history->remark = $plan->status == 3 ? trans('content.scheduled_maintenance.completed') : 'To be approved';
                    $history->updated_by = null;
                    $history->save();

                    $notify_people = [];
                    if ($plan->supplier_id != null) {
                        $user = $plan->supplier->user;
                    } else if ($plan->handler_id != null) {
                        $user = $plan->handler;
                    }
                    array_push($notify_people, $user->id);
                    if ($plan->status == 3) {

                        if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($user->email)->queue(new AllocatedStatusUpdateNotification($user, $plan, 'assigned-plan-list'));
                        }
                        if ($plan->plan->manager_id != null) {
                            if ($user->email != $plan->plan->incharge->email) {
                                $user = $plan->plan->incharge;
                                array_push($notify_people, $user->id);
                                if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($user->email)->queue(new AllocatedStatusUpdateNotification($user, $plan, 'assigned-plan-list'));
                                }
                            }
                        }
                        //send whatsapp notification
                        
                        $data = [
                            'title' => "Scheduled Plan status has been updated",
                            'notify' => $notify_people,
                        ];
                        CommonHelper::sendWhatsappNotification($data);
                    } else if ($plan->status == 9) {
                        if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($user->email)->queue(new PlanSendToApproverNotification($user, $plan, '/assigned-plan-list'));
                            //send whatsapp notification
                            $data = [
                                'title' => "Scheduled Plan has been sent for approval",
                                'notify' => $notify_people,
                            ];
                            CommonHelper::sendWhatsappNotification($data);
                        }
                        if ($plan->plan->manager_id != null) {
                            $user = $plan->plan->incharge;
                            array_push($notify_people, $user->id);
                            if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                Mail::to($user->email)->queue(new PlanApprovalNotification($user, $plan, 'my-approval'));
                                //send whatsapp notification
                                $data = [
                                    'title' => "Scheduled Plan has been waiting for your approval",
                                    'notify' => $notify_people,
                                ];
                                CommonHelper::sendWhatsappNotification($data);
                            }
                        }
                    }
                }

                $notify_people = [];
                //send mail for task update
                if ($plan->supplier_id != null) {
                    $user = $plan->supplier->user;
                } else if ($plan->handler_id != null) {
                    $user = $plan->handler;
                }
                array_push($notify_people, $user->id);
                if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($user->email)->queue(new AllocatedTaskUpdateNotification($user, $plan, $task, '/my-allocated-task-list/'.$task->plan_id));
                }
                if ($plan->plan->manager_id != null) {
                    if ($user->email != $plan->plan->incharge->email) {
                        $user = $plan->plan->incharge;
                        array_push($notify_people, $user->id);
                        if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($user->email)->queue(new AllocatedTaskUpdateNotification($user, $plan, $task, '/my-allocated-task-list/'.$task->plan_id));
                        }
                    }
                }
                //send whatsapp notification
                $data = [
                    'title' => "Task status has been updated",
                    'notify' => $notify_people,
                ];
                CommonHelper::sendWhatsappNotification($data);
            }
            return $this->success([], trans('content.scheduled_maintenance.task_update_success'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->fail(500, trans('content.scheduled_maintenance.task_update_fail'), $e->getMessage());
        }
    }

    public function addTaskAttachment(Request $request) {
        try {
            DB::beginTransaction();
            $rules = [
                'id' => 'required|exists:schedule_maintenance_tasks,id',
            ];

            $return = [];
            $rules['proof'] = 'required|file';

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return $this->fail(422, 'Error in validation', $validator->messages());
            }


            if (!$request->hasFile('proof') || !$request->file('proof')->isValid()) {
                return $this->fail(422, 'Unable to add task attachment', '');
            }
            $file = $request->file('proof');

            $request_id = ($request->id);
            $filePath = date("Y").'/'.date('m').'/'.date('d');
            $checkFolderPath = Common::attachmentFolderStructure('task', $filePath);
            if (!$checkFolderPath) {
                return $this->fail(500, "Attachment Directory not found", '');
            }

            $taskAttachment = TaskAttachment::where('task_id', $request_id)->first();
            if (!empty($taskAttachment)) {
                $path = public_path().'/uploads/task/'.$taskAttachment->file_name;
                if(file_exists($path)) {
                    unlink($path);
                }
                $taskAttachment->delete();
            }
            $a = new TaskAttachment();
            $a->task_id = $request_id;
            $a->original_file_name = $file->getClientOriginalName();
            $a->extension = strtolower($file->getClientOriginalExtension());
            $a->file_name = $request->file('proof')->store($filePath, "task");
            $a->thumbnail_file_name = '_thumbnail.'.$a->file_name;

            if(!$a->file_name || !$a->save()) {
                return $this->fail(422, 'Unable to add task attachment', '');
            }
            
            $a['path'] = asset('uploads/task/'.$a->file_name);
            $return["data"] = $a->only("id","task_id","original_file_name","path");
            $return["status"] = "success";
            DB::commit();
        }
        catch(\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }
        return $this->success($return, trans('content.scheduled_maintenance.task_update_success'));
    }

    public function getStatusList(Request $request) {
        try {
            $return['data'] = ScheduleMaintenanceStatus::where('is_enabled', 1)->get();
            if (isset($return) && count($return)) {
                return $this->success($return, '');
            }
            $this->fail(500, 'Unable to get Status');
        } catch (\Exception $e) {
            return $this->fail(500, 'Unable to get Status', $e->getMessage());
        }
    }

    public function updatePlanStatus(Request $request, $id) {
        if (!Auth::user()->hasPermissionTo('MyAssignPlanStatusEdit') ) {
            return $this->fail(500, trans('content.scheduled_maintenance.permission_denied'), $e->getMessage());
        }
        try {
            $req = $request->all();
            $schedule_maintenance_allocation = ScheduleMaintenanceCron::findOrFail($id);

            $validate = Validator::make($req, [
                'status' => 'required',
                'remark' => 'required',
                'status_canceled' => 'required_if:status,==,5',
                'status_preponed' => 'required_if:status,==,6',
                'status_postponed' => 'required_if:status,==,7',
            ],[
                'status_canceled.required_if' => 'Cancel for field is required',
                'status_preponed.required_if' => 'Preponed for field is required',
                'status_postponed.required_if' => 'Postponed for field is required'
            ]);

            if ($validate->fails()) {
                return $this->fail(422, 'Error in validation', $validate->messages());
            }

            DB::beginTransaction();
            if ($req['status'] == 5) {
                if ($req['status_canceled'] == 2 ) {
                    $schedule_maintenance_allocation->update(['status' => 5]);
                }
            } else if ($req['status'] == 6) {
                if ($req['status_preponed'] == 1 ) {
                    if ($req['preponed_onetime'] > $schedule_maintenance_allocation['schedule_date']) {
                        return $this->fail(500, trans('content.scheduled_maintenance.preponed_date_error'), '');
                    }
                    $schedule_maintenance_allocation->update(['schedule_date' => $req['preponed_onetime']]);
                } else if ($req['status_preponed'] == 2) {
                    $duration = $req['preponed_duration'];
                    if ($req['preponed_type'] == 1) {
                        $date = Carbon::parse($schedule_maintenance_allocation['schedule_date'])->subDays($duration)->format('Y-m-d H:i:00');
                        $schedule_maintenance_allocation->update(['schedule_date' => $date]);
                    } else if ($req['preponed_type'] == 2) {
                        $date = Carbon::parse($schedule_maintenance_allocation['schedule_date'])->subWeeks($duration)->format('Y-m-d H:i:00');
                        $schedule_maintenance_allocation->update(['schedule_date' => $date]);
                    } else if ($req['preponed_type'] == 3) {
                        $date = Carbon::parse($schedule_maintenance_allocation['schedule_date'])->subMonths($duration)->format('Y-m-d H:i:00');
                        $schedule_maintenance_allocation->update(['schedule_date' => $date]);
                    } else if ($req['preponed_type'] == 4) {
                        $date = Carbon::parse($schedule_maintenance_allocation['schedule_date'])->subYears($duration)->format('Y-m-d H:i:00');
                        $schedule_maintenance_allocation->update(['schedule_date' => $date]);
                    }
                }
            } else if ($req['status'] == 7) {
                if ($req['status_postponed'] == 1 ) {
                    if ($req['postponed_onetime'] < $schedule_maintenance_allocation['schedule_date']) {
                        return $this->fail(500, trans('content.scheduled_maintenance.postponed_date_error'), '');
                    }
                    $schedule_maintenance_allocation->update(['schedule_date' => $req['postponed_onetime']]);
                } else if ($req['status_postponed'] == 2) {
                    $duration = $req['postponed_duration'];
                    if ($req['postponed_type'] == 1) {
                        $date = Carbon::parse($schedule_maintenance_allocation['schedule_date'])->addDays($duration)->format('Y-m-d H:i:00');
                        $schedule_maintenance_allocation->update(['schedule_date' => $date]);
                    } else if ($req['postponed_type'] == 2) {
                        $date = Carbon::parse($schedule_maintenance_allocation['schedule_date'])->addWeeks($duration)->format('Y-m-d H:i:00');
                        $schedule_maintenance_allocation->update(['schedule_date' => $date]);
                    } else if ($req['postponed_type'] == 3) {
                        $date = Carbon::parse($schedule_maintenance_allocation['schedule_date'])->addMonths($duration)->format('Y-m-d H:i:00');
                        $schedule_maintenance_allocation->update(['schedule_date' => $date]);
                    } else if ($req['postponed_type'] == 4) {
                        $date = Carbon::parse($schedule_maintenance_allocation['schedule_date'])->addYears($duration)->format('Y-m-d H:i:00');
                        $schedule_maintenance_allocation->update(['schedule_date' => $date]);
                    }
                }
            }
            $schedule_maintenance_allocation->status = $req['status'];
            $schedule_maintenance_allocation->remark = $req['remark'];
            if ($schedule_maintenance_allocation->save()) {
                $history = new ScheduleMaintenanceStatusHistory;
                $history->action_type = 1;
                $history->allocation_id = $schedule_maintenance_allocation->id;
                $history->status_id = $schedule_maintenance_allocation->status;
                $history->remark = $schedule_maintenance_allocation->remark;
                $history->updated_by = Auth::user()->id;
                $history->save();

                $notify_people = [];
                //send mail for status update
                if ($schedule_maintenance_allocation->supplier_id != null) {
                    $user = $schedule_maintenance_allocation->supplier->user;
                } else if ($schedule_maintenance_allocation->handler_id != null) {
                    $user = $schedule_maintenance_allocation->handler;
                }
                array_push($notify_people, $user->id);
                if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($user->email)->queue(new AllocatedStatusUpdateNotification($user, $schedule_maintenance_allocation, '/assigned-plan-list'));
                }
                if ($schedule_maintenance_allocation->plan->manager_id != null) {
                    if($user->email != $schedule_maintenance_allocation->plan->incharge->email) {
                        $user = $schedule_maintenance_allocation->plan->incharge;
                        array_push($notify_people, $user->id);
                        if (config('mail.service_enabled') && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            Mail::to($user->email)->queue(new AllocatedStatusUpdateNotification($user, $schedule_maintenance_allocation, '/all-assigned-plan-list'));
                        }
                    }
                }
                //send whatsapp notification
                $data = [
                    'title' => "Scheduled Plan status has been updated",
                    'notify' => $notify_people,
                ];
                CommonHelper::sendWhatsappNotification($data);
            }
            DB::commit();
            return $this->success([], trans('content.scheduled_maintenance.status_update_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("updatePlanStatus: " . $e->getMessage());
            return $this->fail(500, trans('content.scheduled_maintenance.status_update_fail'), $e->getMessage());
        }
    }

    public function deleteTaskAttachment(Request $request) {
        $return =  [
            'msg' => 'Unable to delete the attachment',
            'status' => 'error',
        ];
        try {
            DB::beginTransaction();
            $rules = [
                'id' => 'required|exists:task_attachments,id',
                'task_id' => 'required|exists:schedule_maintenance_tasks,id',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return $this->fail(422, 'Error in validation', $validator->messages());
            }

            $taskAttachment = TaskAttachment::where('id', $request->id)->where('task_id',$request->task_id)->first();
            if (!empty($taskAttachment)) {
                $path = public_path().'/uploads/task/'.$taskAttachment->file_name;
                if(file_exists($path)) {
                    unlink($path);
                }
                $taskAttachment->delete();
                $return =  [
                    'msg' => 'Task attachment deleted successfully',
                    'status' => 'success'
                ];
            }
            DB::commit();
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollBack();
            Log::error("deleteTaskAttachment API: ".$e->getMessage());
            return $return;
        }
    }
}

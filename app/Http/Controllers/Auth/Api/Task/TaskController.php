<?php

namespace App\Http\Controllers\Auth\Api\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\TaskManagement\TaskStatus;
use App\Models\TaskManagement\TaskPriority;
use App\Models\TaskManagement\TaskType;
use App\Models\TaskManagement\Task;
use App\Models\TaskManagement\TaskMember;
use App\Models\ChangeManagement\Record;
use App\Models\ProjectManagement\Project;
use App\Models\ChangeManagement\RelevantTask;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Log;
use Validator;
use DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Mail;
use Carbon\Carbon;
use App\Helpers\Common as CommonHelper;
use App\Models\ChangeManagement\History;
use App\Models\ChangeManagement\HistoryEntry;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TaskExport;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Privilege;
use App\Models\TaskManagement\TaskHistory;
use App\Mail\Tasks\IntimateAssignedTask;
use App\Mail\Tasks\StatusChange;
use App\Mail\Tasks\CreateTask;
use App\Models\Settings;
use App\Models\Ticket\Config;
use App\Models\TaskManagement\TaskManagementAttachment;
use App\Mail\Tasks\TaskComment;
use Image;
use Storage;
use stdClass;
use App\Models\Ticket\ProblemCategoryTaskDefinition;
use App\Http\Controllers\Ticket\RequestController;

class TaskController extends Controller
{

    public function ajaxAddTask(Request $request) {
        $return = ["status" => "fail", "msg" => trans('content.task_management.Unable_to_create_new_task')];
        if(! Auth::user()->hasPermissionTo('TaskAdd')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only('name', 'description', 'status_id', 'priority_id', 'type_id', 'assigned_to', 'start_date', 'due_date', 'end_date', 'cost', 'change_id', 'project_id','ticket_id','is_visible_user');
        $rules = [
            'name' => 'required|max:255',
            'description' => 'required|max:2000',
            'status_id' =>'required|exists:task_statuses,id',
            'type_id' =>'required|exists:task_types,id',
            'change_id' =>'nullable|sometimes|exists:cm_records,id',
            'ticket_id' =>'nullable|sometimes|exists:tkt_tickets,id',
            'project_id' =>'nullable|sometimes|exists:projects,id',
            'priority_id' =>'required|exists:task_priorities,id',
            'assigned_to' => 'nullable|sometimes|exists:users,id',
            'start_date' => 'nullable|sometimes|',
            'due_date' => 'nullable|sometimes|',
            'end_date' => 'nullable|sometimes|',
            'cost' => 'nullable|sometimes|numeric'
        ];

        $validator = Validator::make($request->all(), $rules, []);
        
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        try {
            $data["start_date"] = !empty($data["start_date"]) ? CommonHelper::getDateAs($data["start_date"], "Y-m-d H:i:s", "d/m/Y h:i A") : null;
            $data["end_date"] = !empty($data["end_date"]) ? CommonHelper::getDateAs($data["end_date"], "Y-m-d H:i:s", "d/m/Y h:i A") : null;
            $data["due_date"] = !empty($data["due_date"]) ? CommonHelper::getDateAs($data["due_date"], "Y-m-d H:i:s", "d/m/Y h:i A") : null;
        
        } catch (\Exception $e) {
            $return["msg"] = trans('content.task_management.Please_give_the_date_values_correctly');
            return response()->json($return);
        }
        try {
            DB::beginTransaction();
            $data['change_id'] = $data["change_id"]??NULL;
            if (isset($request->ticket_id) && !empty($request->ticket_id) && $request->ticket_id !== null && isset($data['assigned_to']) && $data['assigned_to'] != null) { 
                $ticket = Ticket::find($request->ticket_id);
                if ($ticket) {
                    if ((int)$data['type_id'] === 4 && in_array((int)$ticket->status_id, [5, 6])) {
                        return response()->json([
                            'status' => false,
                            'msg' => 'Cannot add task because ticket is resolved.'
                        ]);                        
                    } 
                } else {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Ticket not found',
                    ]);
                }
            }      
            $data['is_visible_user'] = $request->has('is_visible_user') ? $request->is_visible_user : 0;
            $task = new Task;
            $task->fill($data);

            $TaskHistory = $task->replicate();
            $TaskHistory->setTable('task_history');
            $TaskHistory->action_id = 1;
            $TaskHistory->change_by = Auth::user()->id;
            $TaskHistory->change_by_module = isset($request->ticketmodule)&&$request->ticketmodule == "true" ? 2 : 1;

            if($task->save()) {
                if(isset($data['change_id']) && !empty($data['change_id'])) {
                    $newAttach = new RelevantTask();
                    $newAttach->task_id = $task->id;
                    $newAttach->record_id = $data['change_id'];
                    if(!$newAttach->save()) {
                        $return["msg"] = trans('content.change_management_fields.Unable_to_attach_this_task');
                        $return['status'] = 'fail';
                    }
                    $history = new History();
                    $history->user_id = Auth::user()->id;
                    $history->record_id = $newAttach->record_id;
                    $history->save();
                    HistoryEntry::create(['history_id'=>$history->id, 'change_info'=>'The task #' . $task->id . ' has been attached.']);
                }
                $TaskHistory->task_id = $task->id;
                $TaskHistory->save();

                if (!empty($data['ticket_id'])) {
                    Ticket::where('id', $data['ticket_id'])->update(['updated_at' => now()]);
                }
                $cc_mail = [];
                $handler = User::find($task->assigned_to);
                $creatorId = DB::table('task_history')->where('task_id', $task->id)->where('action_id', 1)->value('change_by');
                $task_creator = $creatorId ? User::find($creatorId) : null;

                if (Settings::first()->alerts_enabled == 1) {
                    $alertnotify = CommonHelper::getGlobalAlertEmail();
                    $alertnotify = array_filter($alertnotify, function ($email) {
                        return filter_var($email, FILTER_VALIDATE_EMAIL);
                    });
                    $cc_mail = array_values($alertnotify);
                }

                if (!empty($task_creator) && config('mail.service_enabled') && filter_var($task_creator->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        $cc_for_creator = array_diff($cc_mail, [$task_creator->email]);
                        if (Config::requiredAlertSettingsEmail() && !empty($cc_for_creator)) {
                            Mail::to($task_creator->email)->cc($cc_for_creator)->queue(new CreateTask($task, $task_creator));
                        } else {
                            Mail::to($task_creator->email)->queue(new CreateTask($task, $task_creator));
                        }
                    } catch (\Exception $ex) {
                        Log::error("Task creator mail error: " . $ex->getMessage());
                    }
                }

                if (!empty($task->assigned_to) && config('mail.service_enabled') && $handler && filter_var($handler->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        $cc_for_handler = array_diff($cc_mail, [$handler->email]);

                        if (Config::requiredAlertSettingsEmail() && !empty($cc_for_handler)) {
                            Mail::to($handler->email)->cc($cc_for_handler)->queue(new IntimateAssignedTask($task, $handler));
                        } else {
                            Mail::to($handler->email)->queue(new IntimateAssignedTask($task, $handler));
                        }
                    } catch (\Exception $ex) {
                        Log::error("Task assign mail error: " . $ex->getMessage());
                    }
                }

                $return["msg"] = trans('content.task_management.Given_task_added_successfully');
                $return["status"] = 'success';
                DB::commit();
            }
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("Api ajaxAddTask:" . $e->getMessage());
            $return["msg"] = trans('content.task_management.Unable_to_create_given_new_task');
            $return["status"] = "fail";
            return response()->json($return);
        }

        return response()->json($return);
    }

    public function editTask(Request $request, $param) {
        $return = [
            "msg" => trans('content.task_management.Invalid_Access'),
            "status" => "fail"
        ];
        if(! Auth::user()->hasPermissionTo('TaskEdit')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        try {
            $getTask = Task::find($param);
            $viewData = array();
            // $viewData['status'] = TaskStatus::all();
            // $viewData['priorities'] = TaskPriority::all();
            // $viewData['types'] = TaskType::all();
            $viewData['task'] = $getTask;
            // $viewData['project_id'] = $getTask->project;
            // $viewData['change_id'] = Record::select('id','record_tag')->whereIn('id',explode(",", $getTask->change_id))->withTrashed()->get();
            $viewData['ticket_id'] = Ticket::select(['id', 'subject', 'assigned_to'])->with(['assignedTo'])->withTrashed()->where('id', $getTask->ticket_id)->first();

            if ($viewData['ticket_id']) {
                $attenderName = optional($viewData['ticket_id']->assignedTo)->first_name . ' ' . optional($viewData['ticket_id']->assignedTo)->last_name;
                $attenderName = trim($attenderName); 
            
                $viewData['ticket_id']->text = '#' . $viewData['ticket_id']->id . ' - ' . $viewData['ticket_id']->subject;
                
                if (!empty($attenderName)) {
                    $viewData['ticket_id']->text .= " (Attender - $attenderName)";
                }
            }

            $return["msg"] = "Edit record fetched successfully.";
            $return["status"] = 'success';
            $return["data"] = $viewData;
        }
        catch(\Exception $e) {
            Log::error('editTask: ' . $e->getMessage());
            $return["msg"] = "something went wrong!";
            $return["status"] = 'fail';
            return response()->json($return);
        }
        return $return;
    }

    public function ajaxUpdateTask(Request $request, $param) {
        $return = ["status" => "fail", "msg" => trans('content.task_management.Unable_to_create_new_task')];
        if(! Auth::user()->hasPermissionTo('TaskEdit')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }

        $task = Task::find($param);

        if (!$task) {
            return response()->json(["status" => "fail", "msg" => "Task not found"]);
        }
        $data = $request->only('name', 'description', 'status_id', 'priority_id', 'type_id', 'assigned_to', 'start_date', 'due_date', 'end_date', 'cost', 'change_id', 'project_id','ticket_id','is_visible_user');
        $rules = [
            'name' => 'required|max:255',
            'description' => 'required|max:2000',
            'status_id' =>'required|exists:task_statuses,id',
            'type_id' =>'required|exists:task_types,id',
            'change_id' =>'nullable|sometimes|exists:cm_records,id',
            'ticket_id' =>'nullable|sometimes|exists:tkt_tickets,id',
            'project_id' =>'nullable|sometimes|exists:projects,id',
            'priority_id' =>'required|exists:task_priorities,id',
            'assigned_to' => 'nullable|sometimes|exists:users,id',
            'start_date' => 'nullable|sometimes|',
            'due_date' => 'nullable|sometimes|',
            'end_date' => 'nullable|sometimes|',
            'cost' => 'nullable|sometimes|numeric'
        ];

        $validator = Validator::make($request->all(), $rules, []);
        
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        try {
            if (!empty($data["start_date"])) {
                $data["start_date"] = !empty($data["start_date"]) ? CommonHelper::getDateAs($data["start_date"], "Y-m-d H:i:s", "d/m/Y h:i A") : null;
            } else {
                if(isset($request->ticketmodule)&&$request->ticketmodule == "true"){
                unset($data["start_date"]); 
                }
            }

            $data["end_date"] = !empty($data["end_date"]) ? CommonHelper::getDateAs($data["end_date"], "Y-m-d H:i:s", "d/m/Y h:i A") : null;
            $data["due_date"] = !empty($data["due_date"]) ? CommonHelper::getDateAs($data["due_date"], "Y-m-d H:i:s", "d/m/Y h:i A") : null;

            if (isset($request->ticketmodule) && $request->ticketmodule == "true" && isset($data['status_id']) && $data['status_id'] == 7) {
                $data["end_date"] = now()->format("Y-m-d H:i:s");
            }
                
        }
        catch(\Exception $e) {
            $return["msg"] = trans('content.task_management.Please_give_the_date_values_correctly');
            return response()->json($return);
        }

        try {
            DB::beginTransaction();
            if (isset($request->ticket_id) && !empty($request->ticket_id) && $request->ticket_id !== null && isset($data['assigned_to']) && $data['assigned_to'] != null) { 
                $ticket = Ticket::find($request->ticket_id);
                if ($ticket) {
                    if ((int)$data['type_id'] === 4 && in_array((int)$ticket->status_id, [5, 6])) {
                        return response()->json([
                            'status' => false,
                            'msg' => 'Cannot update task because ticket is resolved.'
                        ]);
                    }
            //         if ($ticket->creator_id == $data['assigned_to']) {
            //             return response()->json([
            //                 'status' => false,
            //                 'msg' => 'Ticket creator and assigned user cannot be the same.',
            //             ]);                        
            //         } 
                } else {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Ticket not found',
                    ]);
                }
            }  
            if (isset($data['type_id'])) {
                switch ($data['type_id']) {
                    case 1: 
                        $data['project_id'] = null;
                        $data['ticket_id'] = null;
                        break;

                    case 2: 
                        $data['change_id'] = null;
                        $data['ticket_id'] = null;
                        break;

                    case 4: 
                        $data['change_id'] = null;
                        $data['project_id'] = null;
                        break;

                    default: 
                        $data['change_id'] = null;
                        $data['project_id'] = null;
                        $data['ticket_id'] = null;
                        break;
                }
            }
            $data['is_visible_user'] = $request->has('is_visible_user') ?  $request->is_visible_user : 0;
            $oldTask=Task::find($param);
            $task->fill($data);
            
           $new_start_date = $request->start_date ? Carbon::createFromFormat('d/m/Y h:i A', $request->start_date)->format('Y-m-d H:i:s') : null;
           $old_start_date = $oldTask->start_date ? Carbon::parse($oldTask->start_date)->format('Y-m-d H:i:s') : null;

           $new_end_date = $request->end_date ? Carbon::createFromFormat('d/m/Y h:i A', $request->end_date)->format('Y-m-d H:i:s') : null;
           $old_end_date = $oldTask->end_date ? Carbon::parse($oldTask->end_date)->format('Y-m-d H:i:s') : null;

           $new_due_date = $request->due_date ? Carbon::createFromFormat('d/m/Y h:i A', $request->due_date)->format('Y-m-d H:i:s') : null;
           $old_due_date = $oldTask->due_date ? Carbon::parse($oldTask->due_date)->format('Y-m-d H:i:s') : null;

            if(isset($request->ticketmodule)&&$request->ticketmodule == "true"){
                $ischanged = $oldTask->name != $request->name || $oldTask->type_id != $request->type_id || $oldTask->status_id != $request->status_id ||$oldTask->priority_id != $request->priority_id ||$oldTask->ticket_id != $request->ticket_id || $old_due_date != $new_due_date || $oldTask->cost != $request->cost || $oldTask->description != trim($request->description) || $oldTask->assigned_to != $request->assigned_to;
            }else{
                $ischanged = $oldTask->name != $request->name || $oldTask->type_id != $request->type_id || $oldTask->status_id != $request->status_id ||$oldTask->priority_id != $request->priority_id ||$oldTask->change_id != $request->change_id ||$oldTask->ticket_id != $request->ticket_id || $old_start_date != $new_start_date || $old_end_date != $new_end_date || $old_due_date != $new_due_date  || $oldTask->cost != $request->cost || $oldTask->description != trim($request->description) || $oldTask->assigned_to != $request->assigned_to;
            }
            if($ischanged){
                $TaskHistory = $task->replicate();
                $TaskHistory->setTable('task_history');
                $TaskHistory->task_id = $task->id;
                $TaskHistory->action_id = 2;
                $TaskHistory->change_by = Auth::user()->id;
                $TaskHistory->change_by_module = isset($request->ticketmodule)&&$request->ticketmodule == "true" ? 2 : 1;
                $TaskHistory->save();
            }
            if (isset($request->ticketmodule) && $request->ticketmodule === "true" && !empty($data['ticket_id'])) {
                Ticket::where('id', $data['ticket_id'])->update(['updated_at' => now()]);
            } 
            $assignedUserChanged = $oldTask->assigned_to != $request->assigned_to;
            $statusChanged = $oldTask->status_id != $request->status_id;

            $to_mail = [];
            $cc_mail = [];
            $handler = User::find($task->assigned_to);
            $creatorId = DB::table('task_history')->where('task_id', $task->id)->where('action_id', 1)->value('change_by');
            $task_creator = $creatorId ? User::find($creatorId) : null;
            if((!empty($handler) && isset($handler->email)) && filter_var($handler->email, FILTER_VALIDATE_EMAIL)) {
                $to_mail[] = $handler->email;
            }

            if(!empty($task_creator) && isset($task_creator->email) && filter_var($task_creator->email, FILTER_VALIDATE_EMAIL)) {
               if (!in_array($task_creator->email, $to_mail)) {
                    $to_mail[] = $task_creator->email;
                }
            }

            if(Settings::first()->alerts_enabled == 1){
                $alertnotify = CommonHelper::getGlobalAlertEmail();
                $alertnotify = array_diff($alertnotify, $to_mail);
                $validEmails = array_filter($alertnotify, function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });
                $cc_mail = array_values($validEmails);
            }
            if ($assignedUserChanged && $request->assigned_to) {
                if (config('mail.service_enabled') && $handler && filter_var($handler->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        $cc_for_handler = array_diff($cc_mail, [$handler->email]);
                        if (Config::requiredAlertSettingsEmail() && !empty($cc_for_handler)) {
                            Mail::to($handler->email)->cc($cc_for_handler)->queue(new IntimateAssignedTask($task, $handler));
                        } else {
                            Mail::to($handler->email)->queue(new IntimateAssignedTask($task, $handler));
                        }
                    } catch (\Exception $ex) {
                        Log::error("Task assign mail error: " . $ex->getMessage());
                    }
                }
            }
            if($statusChanged && $request->status_id){
                if (config('mail.service_enabled') && !empty($to_mail)) {
                    try {
                        if (Config::requiredAlertSettingsEmail() && $cc_mail) {
                            Mail::to($to_mail)->cc($cc_mail)->queue(new StatusChange($task,(object)[]));
                        } else {
                            Mail::to($to_mail)->queue(new StatusChange($task,(object)[]));
                        }
                    } catch (\Exception $ex) {
                        Log::error("Task status change mail error: " . $ex->getMessage());
                    }
                }
            }
            if($task->save()) {
                if(isset($task->change_id) && $task->change_id != "") {
                    $checkRelevant = RelevantTask::where('task_id', $param)->first();
                    if(empty($checkRelevant)) {
                        $newAttach = new RelevantTask();
                        $newAttach->record_id = $task->change_id;
                        $newAttach->task_id = $param;
                        if(!$newAttach->save()) {
                            $return["msg"] = trans('content.change_management_fields.Unable_to_attach_this_task');
                            $return['status'] = 'fail';
                        }
                    } else {
                        $checkRelevant->record_id = $task->change_id;
                        $checkRelevant->task_id = $param;
                        $checkRelevant->save();
                    }
                } else {
                    $checkRelevant = RelevantTask::where('task_id', $param)->delete();
                }
                if (!empty($task->pc_task_id)) {
                    $taskDef = ProblemCategoryTaskDefinition::find($task->pc_task_id);
                    if ($taskDef && in_array($task->status_id, [7])) {
                        try {
                            $st = Ticket::find($task->ticket_id);
                            if ($st) {
                                app(RequestController::class)->createTasksFromCategory($st, true);
                            }
                        } catch (\Exception $ex) {
                            Log::error("Error creating next sequential task: " . $ex->getMessage());
                        }
                    }
                }
                $return["msg"] = trans('content.task_management.Given_task_updated_successfully');
                $return["status"] = 'success';
                DB::commit();
            }
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error('ajaxUpadteTask: ' . $e->getMessage());
            $return["msg"] = trans('content.task_management.Unable_to_update_given_task');
            $return["status"] = "fail";
            return response()->json($return);
        }

        return response()->json($return);
    }

    public function ajaxDelete(Request $request, $id)
    {
        if (!Auth::user()->hasPermissionTo('TaskDelete')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            $return["status"] = "fail";
            return response()->json($return);
        }

        try {
            $task = Task::findOrFail($id);
            // Clone task into task_history
            $taskHistory = $task->replicate();
            $taskHistory->setTable('task_history');
            $taskHistory->task_id = $task->id;
            $taskHistory->action_id = 3; // delete action
            $taskHistory->change_by = Auth::id();
            $taskHistory->change_by_module = 1;
            $taskHistory->save();

            if ($task->delete()) {
                $return["msg"] = trans('content.task_management.Task_has_been_deletd_successfully');
                $return["status"] = "success";
                return response()->json($return);
            }
        } catch (\Exception $e) {
            Log::error('apiDeleteTask: ' . $e->getMessage());
            $return["msg"] = trans('content.task_management.Unable_to_delete_the_task');
            $return["status"] = "fail";
            return response()->json($return);
        }
    }

    public function getTaskStatus(Request $request) {
        $return = ['status' => 'fail','msg' => 'Unable to get status'];
        try {
            $search = $request->input("search", "");
            $page = $request->input("page", 1);
            $skip = (($page * 20) - 20);

            $db = TaskStatus::select("id", "name as text");
            if($search) {
                $db->whereRaw('task_statuses.name like "%' . $search . '%"');
            }
            $db->orderBy('text', 'asc');
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();
            $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
            $return["results"] = count($result) ? $result->toArray() : [];
            $return['status'] = 'success';
            $return['msg'] = 'status fetched successfully.';
        }
        catch(\Exception $e) {
            Log::error('getTaskStatus: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getTaskPriority(Request $request) {
        $return = ['status' => 'fail','msg' => 'Unable to get priority'];
        try {
            $search = $request->input("search", "");
            $page = $request->input("page", 1);
            $skip = (($page * 20) - 20);

            $db = TaskPriority::select("id", "name as text");
            if($search) {
                $db->whereRaw('task_statuses.name like "%' . $search . '%"');
            }
            $db->orderBy('text', 'asc');
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();
            $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
            $return["results"] = count($result) ? $result->toArray() : [];
            $return['status'] = 'success';
            $return['msg'] = 'priority fetched successfully.';
        }
        catch(\Exception $e) {
            Log::error('getTaskPriority: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function getTaskHistory(Request $request) { 
        $return = ['status' => 'fail','msg' => 'Unable to get His'];
        try {
            $task_id = $request->id;
            $db = TaskHistory::withTrashed()->from('task_history as t');
            $db->leftJoin('task_statuses as st', 't.status_id', '=', 'st.id');
            $db->leftJoin('task_priorities as pri', 't.priority_id', '=', 'pri.id');
            $db->leftJoin('task_types as ct', 't.type_id', '=', 'ct.id');
            $db->leftJoin('users as asgn', 't.assigned_to', '=', 'asgn.id');
            $db->leftJoin('users as change_by', 't.change_by', '=', 'change_by.id');
            $db->leftJoin('projects as p', 't.project_id', '=', 'p.id');
            $db->leftJoin('cm_records as c', 't.change_id', '=', 'c.id');
            $db->leftJoin('tkt_tickets as tkt','t.ticket_id','=','tkt.id');
            $db->where('t.task_id',$task_id);
            $db->select('t.id', 't.name', 't.cost', 't.description', 'st.name as statusName', 'pri.name as priorityName', 'ct.name as relatedTo','tkt.status_id','t.action_id','t.is_note');
            $db->addSelect(DB::raw('CASE WHEN asgn.displayName IS NOT NULL AND change_by.displayName != "" THEN asgn.displayName ELSE concat_ws(" ", asgn.first_name, asgn.last_name) END as assignedTo'));
            $db->addSelect(DB::raw('CASE WHEN change_by.displayName IS NOT NULL AND change_by.displayName != "" THEN change_by.displayName ELSE concat_ws(" ", change_by.first_name, change_by.last_name) END as change_by'));
            $db->addSelect(DB::raw('DATE_FORMAT(t.due_date, "%d %b %Y %h:%i %p") as due_date_at'));
            $db->addSelect(DB::raw('DATE_FORMAT(t.start_date, "%d %b %Y %h:%i %p") as start_date_at'));
            $db->addSelect(DB::raw('DATE_FORMAT(t.end_date, "%d %b %Y %h:%i %p") as end_date_at'));
            $db->addSelect(DB::raw('DATE_FORMAT(t.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
            $db->addSelect('p.name as projectName', 'c.record_tag as changeName');
            $db->addSelect(DB::raw('concat("#", t.ticket_id) as ticketId'));
            $db->orderBy('t.updated_at', 'desc');
            $return['tasks']=$db->get();

            $return['status'] = 'success';
            $return['msg'] = 'Task History fetched successfully.';
        }
        catch(\Exception $e) {
            Log::error('getTaskHistory: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function ajaxStatusEditTask(Request $request,$param) {
        $return = ['status' => 'fail','msg' => 'unable to update status'];
        try {   
            $task = Task::find($param);
            if (!$task) {
                return response()->json(["status" => "fail", "msg" => "Task not found"]);
            }
            if(isset($task['ticket_id']) && !empty($task['ticket_id']) && $task['ticket_id'] !== null){
                $ticket = DB::table('tkt_tickets')->select('status_id')->where('id', $task['ticket_id'])->first();
                $ticket_status = $ticket ? $ticket->status_id : null;
            }
            if (in_array($task['status_id'], [7, 9]) && ($ticket_status == 5 || $ticket_status == 6)) {
                return response()->json(["status" => "fail", "msg" => "Cannot change task status, since the task is already completed. "]);
            }

            $data = $request->only('task_comment', 'status_id', 'type_id');
            $rules = [
                'task_comment' => 'required|max:2000',
                'status_id' =>'required|exists:task_statuses,id',
                'type_id' =>'required|exists:task_types,id',  
                'temp_id' => 'required',    
            ];

            $validator = Validator::make($request->all(), $rules, []);
            
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $ActionHistory = new TaskHistory();
            $ActionHistory->task_id = $task->id;
            $ActionHistory->name = $task->name;
            $ActionHistory->status_id = $request->status_id ?? null; 
            $ActionHistory->remarks = $request->task_comment ?? null; 
            $ActionHistory->action_id = 5;
            $ActionHistory->change_by = Auth::user()->id;
            $ActionHistory->change_by_module = isset($request->ticketmodule) && $request->ticketmodule == "true" ? 2 : 1;
            $ActionHistory->save();

            if ($request->type_id == 4 && !empty($task->ticket_id)) {
                $ActionHistory->ticket_id = $task->ticket_id;
                $ActionHistory['action_type'] = 22; 
                $ActionHistory['updated_by'] = $ActionHistory['change_by'];
                CommonHelper::ticketStatusHistory($ActionHistory); 
            }
            $to_mail = [];
            $cc_mail = [];
            $handler = User::find($task->assigned_to);
            $creatorId = DB::table('task_history')->where('task_id', $task->id)->where('action_id', 1)->value('change_by');
            $task_creator = $creatorId ? User::find($creatorId) : null;
            if((!empty($handler) && isset($handler->email)) && filter_var($handler->email, FILTER_VALIDATE_EMAIL)) {
                $to_mail[] = $handler->email;
            }

            if(!empty($task_creator) && isset($task_creator->email) && filter_var($task_creator->email, FILTER_VALIDATE_EMAIL)) {
                if (!in_array($task_creator->email, $to_mail)) {
                    $to_mail[] = $task_creator->email;
                }
            }

            if(Settings::first()->alerts_enabled == 1){
                $alertnotify = CommonHelper::getGlobalAlertEmail();
                $alertnotify = array_diff($alertnotify, $to_mail);
                $validEmails = array_filter($alertnotify, function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });
                $cc_mail = array_values($validEmails);
            }
            if ($task->type_id == 4 && !empty($task->ticket_id)) {
                $ticket = Ticket::find($task->ticket_id);
                if ($ticket) {
                    $ticket_creator = User::find($ticket->creator_id);
                    $ticket_handler = User::find($ticket->assigned_to);
                    if (!empty($ticket->creator_id)) {
                        if ($ticket_creator && filter_var($ticket_creator->email, FILTER_VALIDATE_EMAIL)) {
                            $cc_mail[] = $ticket_creator->email;
                        }
                    }
                    if (!empty($ticket->assigned_to)) {
                        if ($ticket_handler && filter_var($ticket_handler->email, FILTER_VALIDATE_EMAIL)) {
                            $cc_mail[] = $ticket_handler->email;
                        }
                    }
                }
            }
            $cc_mail = array_unique($cc_mail);
            if (config('mail.service_enabled') && !empty($to_mail)) {
                try {
                    if (Config::requiredAlertSettingsEmail() && $cc_mail) {
                        Mail::to($to_mail)->cc($cc_mail)->queue(new StatusChange($task,$ActionHistory));
                    } else {
                        Mail::to($to_mail)->queue(new StatusChange($task,$ActionHistory));
                    }
                } catch (\Exception $ex) {
                    Log::error("Task status change mail error: " . $ex->getMessage());
                }
            }
            $task->status_id = $request->status_id;
            $task->save();
            $ats = TaskManagementAttachment::where("task_id", "=", $task->id)->where("tmp_id", "like", $request->temp_id)->get();
            if($ats && count($ats)) {
                foreach($ats as $at) {
                    $at->following_id = $ActionHistory->id;
                    $at->save();
                }
            }
            if (!empty($task->pc_task_id)) {
                $taskDef = ProblemCategoryTaskDefinition::find($task->pc_task_id);
                if ($taskDef && in_array($task->status_id, [7])) {
                    try {
                        $st = Ticket::find($task->ticket_id);
                        if ($st) {
                            app(RequestController::class)->createTasksFromCategory($st, true);
                        }
                    } catch (\Exception $ex) {
                        Log::error("Error creating next sequential task: " . $ex->getMessage());
                    }
                }
            }
            $return["msg"] = trans('content.task_management.Given_task_updated_successfully');
            $return["status"] = 'success';
        }
        catch(\Exception $e) {
            Log::error('ajaxStatusEditTask: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function addComment(Request $request) {
       $return = [
            "status" => "fail",
            "msg" => trans('content.service_ticket_fields.Unable_to_add_comment'),
        ]; 
        try {
            $rules = ['comment' => 'required','tmp_id' =>'required'];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $task = Task::find($request->input('id'));
            $comment = new TaskHistory([
                'name' => $task->name,
                'task_id' => $request->input('id'),
                'action_id' => 4, 
                'remarks' => $request->input('comment'),
                'change_by' => Auth::user()->id,
                'change_by_module'=> isset($request->ticketmodule)&&$request->ticketmodule == "true" ? 2 : 1,
                'is_note' => isset($request->task_internal_note) && $request->task_internal_note == 1 ? 1 : 0
            ]);
            $comment->save();
            $task->touch();
            $ats = TaskManagementAttachment::where("task_id", "=", $request->input('id'))->where("tmp_id", "like", $request->tmp_id)->get();
            if($ats && count($ats)) {
                foreach($ats as $at) {
                    $at->following_id = $comment->id;
                    $at->save();
                }
            }
            if (!empty($task->ticket_id) && $task->ticket_id != null) {
                    $comment->ticket_id = $task->ticket_id;
                    $comment['action_type'] = 22;
                    $comment['updated_by'] = $comment['change_by'];
                    CommonHelper::ticketStatusHistory($comment);
                    Ticket::where('id',$task->ticket_id)->update(['updated_at' => now()]);
            }
            $to_mail = [];
            $cc_mail = [];
            $handler = User::find($task->assigned_to);
            $creatorId = DB::table('task_history')->where('task_id', $task->id)->where('action_id', 1)->value('change_by');
            $task_creator = $creatorId ? User::find($creatorId) : null;

            if((!empty($handler) && isset($handler->email)) && filter_var($handler->email, FILTER_VALIDATE_EMAIL)) {
                $to_mail[] = $handler->email;
            }

            if(!empty($task_creator) && isset($task_creator->email) && filter_var($task_creator->email, FILTER_VALIDATE_EMAIL)) {
               if (!in_array($task_creator->email, $to_mail)) {
                    $to_mail[] = $task_creator->email;
                }
            }

            if ($handler && $handler->email && filter_var($handler->email, FILTER_VALIDATE_EMAIL)) {
                $recipients[] = $handler->email;
            }
            if(Settings::first()->alerts_enabled == 1){
                $alertnotify = CommonHelper::getGlobalAlertEmail();
                $alertnotify = array_diff($alertnotify, $to_mail);
                $validEmails = array_filter($alertnotify, function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });
                $cc_mail = array_values($validEmails);
            }
            if ($task->type_id == 4 && !empty($task->ticket_id)) {
                $ticket = Ticket::find($task->ticket_id);
                if ($ticket) {
                    $ticket_creator = User::find($ticket->creator_id);
                    $ticket_handler = User::find($ticket->assigned_to);
                    if (!empty($ticket->creator_id)) {
                        if ($ticket_creator && filter_var($ticket_creator->email, FILTER_VALIDATE_EMAIL)) {
                            $cc_mail[] = $ticket_creator->email;
                        }
                    }
                    if (!empty($ticket->assigned_to)) {
                        if ($ticket_handler && filter_var($ticket_handler->email, FILTER_VALIDATE_EMAIL)) {
                            $cc_mail[] = $ticket_handler->email;
                        }
                    }
                }
            }
            $cc_mail = array_unique($cc_mail);
            if (config('mail.service_enabled') && !empty($to_mail)) {
                try {
                    if (Config::requiredAlertSettingsEmail() && $alertnotify) {
                        Mail::to($to_mail)->cc($cc_mail)->queue(new TaskComment($task,$comment));
                    } else {
                        Mail::to($to_mail)->queue(new TaskComment($task,$comment));
                    }
                } catch (\Exception $ex) {
                    Log::error("Task comment mail error: " . $ex->getMessage());
                }
            }

            $return = [
                "status" => "success",
                "msg" => "Comment save successfully",
            ];
            $return["data"] = $task->only('id');
        }
        catch(\Exception $e) {
            Log::error('Error adding comment: '. $e->getMessage());
        }
        return response()->json($return);
    }

    public function addAttachment(Request $request) {
        $return = array("status" => "fail", "msg" => "Upload has not success");
        try {
             $rules = [
                'tmp_id' => 'required|nullable|string|max:25',
                'attachment' => 'required|file',
            ];

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            if(! $request->hasFile('attachment') || !$request->file('attachment')->isValid()) {
                return response()->json($return);
            }
            $file = $request->file('attachment');   
            $filePath = date("Y").'/'.date('m').'/'.date('d');
            $checkFolderPath = CommonHelper::attachmentFolderStructure('task_management', $filePath);
            if (!$checkFolderPath) {
                return $this->fail(500, " Directory not found", '');
            }    
            $ts = new TaskManagementAttachment();
            $ts->task_id = $request->id == 'undefined' ? null : $request->id;
            $ts->original_file_name = $file->getClientOriginalName();
            $ts->original_file_name = $ts->trimUnfittedName($ts->original_file_name);
            $ts->extension = strtolower($file->getClientOriginalExtension());
            $ts->file_name =  $request->file('attachment')->store($filePath, "task_management");
            $ts->uploader_id = Auth::user()->id;
            $ts->tmp_id = $request->input("tmp_id", null);
            if(!$ts->file_name || !$ts->save()) {
                return response()->json($return);
            }

            if( in_array($ts['extension'], ["png", "jpeg", "jpg"]) !== false ) {
                try {
                    $path = storage_path('') . DIRECTORY_SEPARATOR . $ts['file_name'];
                    $thumb_name = $ts->getThumbName();
                    if(! $thumb_name) {
                        throw new \Exception("Invalid Image Name " . $ts->id);
                    }
                    $thumb_path = storage_path('task_management') . DIRECTORY_SEPARATOR . $thumb_name;

                    Image::make($path)->resize(100, null, function($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save($thumb_path);
                    $attachments['thumbnail']=$thumb_name;
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            $return["data"] = $ts->only("id", "original_file_name");
            $return["status"] = "success";
            $return["msg"] = "";
        }
        catch(\Exception $e) {
            Log::error('addAttachment: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function viewAttachment($id, $thumb = false) {
        try {
            $a = TaskManagementAttachment::find($id);
            if(empty($a)) {
                $a = TaskManagementAttachment::find($id);
            }
            if(!$a || !Storage::disk('task_management')->exists($a->file_name)) {
                throw new \Exception("Invalid File");
            }

            $path = Storage::disk('task_management')->getAdapter()->getPathPrefix();

            if( $thumb ) {
                $path .= $a->thumbnail;
            }
            else {
                $path .= $a->file_name;
            }

            return response()->file($path);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function downloadAttachment($id){
        $a = TaskManagementAttachment::find($id);
        
        if(!$a || !Storage::disk('task_management')->exists($a->file_name)) {
            return redirect("/");
        }
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'. $a->original_file_name .'"');
        echo Storage::disk('task_management')->get($a->file_name);
    }

    public function removeAttachment(Request $request) {
        $return = array("status" => "fail","msg" => "Unable to remove attachment");
        try {
            $rules = [
                'id' => 'required|integer|exists:task_management_attachment,id'
            ];

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $ts = TaskManagementAttachment::find($request->id);
            Storage::disk('task_management')->delete($ts->file_name);
            $ts->delete();

            $return["status"] = "success";
            $return["msg"] = "Attachment removed";
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
        }
        return response()->json($return);
    }
}
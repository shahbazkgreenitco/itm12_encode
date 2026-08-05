<?php

namespace App\Http\Controllers\Auth\Api\ProblemManagement;

use App\Mail\ProblemManagement\IntimateAssigned;
use App\Mail\Ticket\Resolved;
use App\Models\ProblemManagement\ProblemManagerGroup;
use App\Models\ProblemManagement\ProblemManagerGroupMember;
use App\Models\Ticket\Config;
use App\Models\Ticket\Ticket;
use App\Models\Holiday;
use App\Models\TktFollowing;
use App\Models\User;
use App\Models\Model;
use App\Models\Manufacture;
use Auth;
use Carbon\Carbon;
use App\Helpers\Common as CommonHelper;
use Illuminate\Http\Request;
use App\Models\Ticket\Priority;
use App\Models\Department;
use App\Models\ProblemManagement\ItmProblem;
use App\Models\ProblemManagement\ProblemImpactedDevice;
use App\Models\ProblemManagement\ProblemImpactedTicket;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\Log;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProblemManagement\ProblemExport;
use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\ProblemManagement\ProblemManagerHistory;

class ProblemManagementController extends Controller
{
    //
    public function problemList(Request $request, $listType) {
        if (Auth::user()->hasPermissionTo('ProblemManagement') ) {
            if($listType == 'my_problem_list') {
                if (!Auth::user()->hasPermissionTo('MyProblemLists') ) {
                    $return = [
                        "msg" => trans('content.service_ticket_fields.insufficident_permission'),
                        "status" => "danger"
                    ];
                    $request->session()->flash("msg", $return);
                    return redirect("dashboard");
                }
            } else {
                if ($listType == 'AllProblemLists') {
                    if (!Auth::user()->hasPermissionTo('AllProblemLists')) {
                        $return = [
                            "msg" => trans('content.service_ticket_fields.insufficident_permission'),
                            "status" => "danger"
                        ];
                        $request->session()->flash("msg", $return);
                        return redirect("dashboard");
                    }
                }
            }
        } else {
            $return = [
                "msg" => trans('content.service_ticket_fields.insufficident_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        try {
            $models = DB::table('models')->select("id", "name")->get();
            $locations = DB::table('locations')->select("id", "name")->get();
            $priorities = Priority::select("id", "name")->get();
            $suppliers = DB::table('suppliers')->select("id", "name")->get();
            $departments = Department::select('departments.id', DB::raw("concat(departments.name, ' (', c.name, ')') as name"))->join("companies as c", "company_id", "=", "c.id")->where('module_ticket_enabled', '=', '1')->where('departments.id', 'not like', "To be assigned")->get();

            $impact_ticket = DB::table('tkt_tickets')->select("id", "subject")->whereNotNull('content')->whereNull("spam")->whereNull("deleted_at")->get();
            $device = DB::table('assets')->select("id", "name")->where("name", '!=', " ")->whereNotNull("name")->whereNull("deleted_at")->get();

            $categories = DB::table('categories')->select('id','name as text')->orderBy('name','asc')->get();
            $models= Model::select('id','name as text')->orderBy('name','asc')->get();
            $manufacturer = Manufacture::select('id','name as text')->orderBy('name','asc')->get();

            return view("problem_manager.index")->with(compact("impact_ticket","device","priorities","departments","models","locations","suppliers",'listType',"categories",'models','manufacturer'));
        } catch (\Exception $e) {
            return redirect("/")->with("status", "Insufficient Permission to Access");
        }
    }

    public function groups(Request $request)
    {
        if (!Auth::user()->isSuperUser()) {
            $return = [
                "msg" => trans('content.service_ticket_fields.insufficident_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        try {
            $models = DB::table('models')->select("id", "name")->get();
            $locations = DB::table('locations')->select("id", "name")->get();
            $priorities = Priority::select("id", "name")->get();
            $suppliers = DB::table('suppliers')->select("id", "name")->get();
            $departments = Department::select('departments.id', DB::raw("concat(departments.name, ' (', c.name, ')') as name"))->join("companies as c", "company_id", "=", "c.id")->where('module_ticket_enabled', '=', '1')->where('departments.id', 'not like', "To be assigned")->get();

            $impact_ticket = DB::table('tkt_tickets')->select("id", "subject")->whereNotNull('content')->whereNull("spam")->whereNull("deleted_at")->get();
            $device = DB::table('assets')->select("id", "name")->where("name", '!=', " ")->whereNotNull("name")->whereNull("deleted_at")->get();
            return view("problem_manager.pmt_index")->with("impact_ticket", $impact_ticket)->with("device", $device)->with("priorities", $priorities)->with("departments", $departments)->with("models", $models)->with("locations", $locations)->with("suppliers", $suppliers);
        } catch (\Exception $e) {
            return redirect("/")->with("status", "Insufficient Permission to Access");
        }
    }

    public function ajaxGroupList(Request $request)
    {
        DB::enableQueryLog();
        $return = array(
            "status" => "fail",
            "msg" => "Unable to display given details"
        );
       try {
            $req = $request->all();
            $return = array(
                "draw" => date('is')
            );
            $fields = array(
                '1' => 'problem_manager_group.name',
                '3' => 'dep.name',
                '9' => 'problem_manager_group.created_at',
                '10' => 'problem_manager_group.updated_at',
            );

            $query = ProblemManagerGroup::select('problem_manager_group.id', 'problem_manager_group.name', 'dep.name as department_name', 'problem_manager_group.created_at','problem_manager_group.updated_at')
                ->leftJoin('departments as dep', 'dep.id', '=', 'problem_manager_group.department_id');

            $return['recordsTotal'] = $query->count();
            $return['recordsFiltered'] = $return['recordsTotal'];
            $is_searching = false;
            if (isset($req["filters"])) {
                $filters = $req["filters"];

                if (isset($filters["priority"]) && $filters['priority'] && $filters['priority'] != "null") {
                    $query->whereIn("pri.id", $filters['priority']);
                }
                if (isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                    $query->where("itm_problems.department_id", $filters['department']);
                }
                if (isset($filters["problem_category"]) && $filters['problem_category'] && $filters['problem_category'] != "null") {
                    $query->whereIn("itm_problems.problem_category_id", $filters['problem_category']);
                }
                if (isset($filters["sub_category"]) && $filters['sub_category'] && $filters['sub_category'] != "null") {
                    $query->whereIn("itm_problems.sub_category_id", $filters['sub_category']);
                }
                if (isset($filters["handler"]) && $filters['handler'] && $filters['handler'] != "null") {
                    $query->whereIn("uhp.id", $filters['handler']);
                }
                $is_searching = true;
            }

            if (isset($req["search"]["value"]) && $req["search"]["value"] != null && $search_key = trim($req["search"]["value"])) {
                $whereStr = sprintf('(dep.name like "%%%1$s%%" or content like "%%%1$s%%")', $search_key);
                $query->whereRaw($whereStr);
            }

            if ($is_searching) {
                $return['recordsFiltered'] = $query->count();
            }

            if (isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
                $query->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
            }

            $skip = 0;
            $take = 10;

            if (isset($request["start"]) && isset($request["length"])) {
                $skip = (int) $request["start"];
                $take = (int) $request["length"];
            }

            $query->skip($skip);
            $query->take($take);

            $return["data"] = $query->get();
            $return["status"] = 'success';
            $return["msg"] = 'Problem Manager group fetched successfully';

        } catch (\Exception $e) {
            Log::error($e->getmessage());
        }
        return response()->json($return);
    }
    
    //add group
    public function ajaxAddGroup(Request $request) {
        $return = ["status" => "fail", "msg" => "trans('content.problem_manager.unable_new_group')"];
        $input = $request->all();
        try {
            DB::beginTransaction();
            $rules = [
                'name' => 'required',
                'department_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $data = $request->only("name", "content","department_id");
            $problemManagerGroup = new ProblemManagerGroup();
            $problemManagerGroup->fill($data);
            $problemManagerGroup->save();
            $handlers = $request->only("problem_handler_id");
            if ($handlers) {
                foreach ($handlers as $handler) {
                    $ProblemImpactedDevice = new ProblemManagerGroupMember();
                    $ProblemImpactedDevice->device_id = $handler;
                    $ProblemImpactedDevice->problem_id = $problemManagerGroup->id;
                    $ProblemImpactedDevice->save();
                }
            }

            $return["status"] = "success";
            $return["msg"] = trans('content.problem_manager.add_create_group');
            DB::commit();
            Log::info("PM ajaxAddGroup id:" . $problemManagerGroup->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("PM ajaxAddGroup: " . $e->getMessage());
            return response()->json($return);
        }
    }

    // public function problemAdd(Request $request)
    // {
    //     if (!Auth::user()->isSuperUser()) {
    //         $return = [
    //             "msg" => trans('content.service_ticket_fields.insufficident_permission'),
    //             "status" => "danger"
    //         ];
    //         $request->session()->flash("msg", $return);
    //         return redirect("dashboard");
    //     }
    //     try {

    //         $models = DB::table('models')->select("id", "name")->get();
    //         $locations = DB::table('locations')->select("id", "name")->get();
    //         $priorities = Priority::select("id", "name")->get();
    //         $suppliers = DB::table('suppliers')->select("id", "name")->get();
    //         $departments = Department::select('departments.id', DB::raw("concat(departments.name, ' (', c.name, ')') as name"))->join("companies as c", "company_id", "=", "c.id")->where('module_ticket_enabled', '=', '1')->where('departments.id', 'not like', "To be assigned")->get();

    //         $impact_ticket = DB::table('tkt_tickets')->select("id", "subject")->whereNotNull('content')->whereNull("spam")->whereNull("deleted_at")->get();
    //         $device = DB::table('assets')->select("id", "name")->where("name", '!=', " ")->whereNotNull("name")->whereNull("deleted_at")->get();
    //         return view("problem_manager.add_problem")->with("impact_ticket", $impact_ticket)->with("device", $device)->with("priorities", $priorities)->with("departments", $departments)->with("models", $models)->with("locations", $locations)->with("suppliers", $suppliers);
    //     } catch (\Exception $e) {
    //         return redirect("/")->with("status", "Insufficient Permission to Access");
    //     }
    // }
    public function ajaxprobleList(Request $request,$listType=null) {
        DB::enableQueryLog();
        $return = array(
            "status" => "fail",
            "msg" => "Unable to display given details"
        );
        try {
            $page = $request->index ? $request->index : 0;
            $take = $request->list_size ? $request->list_size : 20;
            $skip = $page * $take;
            $currentUser = User::find(1);//Auth::user()->id
            $req = $request->all();
            $return = array(
                "draw" => date('is')
            );
            $fields = array(
                '1' => 'itm_problems.name',
                '2' => 'pri.name',
                '3' => 'dep.name',
                '4' => 'prb_cat.name',
                '5' => 'sc.name',
                '8' => 'itm_problems.created_at',
                '9' => 'itm_problems.updated_at',
            );

            $query = ItmProblem::select('itm_problems.id', 'itm_problems.name as problem_name', 'content', 'pri.name as priority_name', 'dep.name as department_name', 'prb_cat.name as pc_category', 'sc.name as sc_category', 'uhp.username as username','itm_problems.created_at','itm_problems.updated_at')
                ->leftJoin('departments as dep', 'dep.id', '=', 'itm_problems.department_id')
                ->leftJoin('tkt_problem_categories as prb_cat', 'prb_cat.id', '=', 'itm_problems.problem_category_id')
                ->leftJoin('tkt_problem_categories as sc', 'sc.id', '=', 'itm_problems.sub_category_id')
                ->leftJoin('tkt_priorities as pri', 'pri.id', '=', 'itm_problems.priority_id')
                ->leftJoin('users as uhp', 'uhp.id', '=', 'itm_problems.problem_handler_id');

            if($listType == 'my_problem_list'){
                $query->where('problem_handler_id',$currentUser->id);
            }

            $return['recordsTotal'] = $query->count();
            $return['recordsFiltered'] = $return['recordsTotal'];
            $is_searching = false;
            if (isset($req["filters"])) {
                $filters = $req["filters"];

                if (isset($filters["priority"]) && $filters['priority'] && $filters['priority'] != "null") {
                    $query->whereIn("pri.id", $filters['priority']);
                }
                if (isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                    $query->where("itm_problems.department_id", $filters['department']);
                }
                if (isset($filters["problem_category"]) && $filters['problem_category'] && $filters['problem_category'] != "null") {
                    $query->whereIn("itm_problems.problem_category_id", $filters['problem_category']);
                }
                if (isset($filters["sub_category"]) && $filters['sub_category'] && $filters['sub_category'] != "null") {
                    $query->whereIn("itm_problems.sub_category_id", $filters['sub_category']);
                }
                if (isset($filters["handler"]) && $filters['handler'] && $filters['handler'] != "null") {
                    $query->whereIn("uhp.id", $filters['handler']);
                }
                $is_searching = true;
            }

            if (isset($req["search"]["value"]) && $req["search"]["value"] != null && $search_key = trim($req["search"]["value"])) {
                $whereStr = sprintf('(dep.name like "%%%1$s%%" or prb_cat.name like "%%%1$s%%" or sc.name like "%%%1$s%%" or pri.name like "%%%1$s%%" or content like "%%%1$s%%" or uhp.username like "%%%1$s%%")', $search_key);
                $query->whereRaw($whereStr);
            }

            if ($is_searching) {
                $return['recordsFiltered'] = $query->count();
            }

            if (isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
                $query->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
            }

            $return['current_index'] = (int) $request->index;
            $return['is_prev_index'] = $skip > 0 ? 1 : 0;
            $return['is_next_index'] = $return['recordsFiltered'] > ( $skip + $take ) ? 1 : 0;

            $query->skip($skip);
            $query->take($take);

            $return["data"] = $query->get();

            foreach ($return["data"] as $d) {
                //device count
                $deviceCount = ProblemImpactedDevice::select('problem_impacted_devices.device_id','problem_impacted_devices.status')
                    ->where(['problem_impacted_devices.problem_id' => $d->id])->get();

                $d->impactedDevices = $deviceCount;

                $deviceCountCompleted = ProblemImpactedDevice::select('problem_impacted_devices.device_id')
                    ->where(['problem_impacted_devices.status' => 1,'problem_impacted_devices.problem_id' => $d->id])->get();

                $d->devicecountnum = 0;
                if (!empty($deviceCount) && $deviceCount->count() > 0) {
                    // $d->devicecountnum = $deviceCount;                    
                    $devicediv = $deviceCountCompleted->count() / $deviceCount->count();
                    $per = $devicediv * 100;
                    $d->devicecountnum = number_format($per, 0);
                    $d->devicecountnum = $d->devicecountnum.'%';
                } else {
                    $d->devicecountnum = '0%';
                }
                
                //ticket count                
                $ticketCount = ProblemImpactedTicket::select('problem_impacted_tickets.ticket_id','problem_impacted_tickets.status')
                    ->where(['problem_impacted_tickets.problem_id' => $d->id])->get();

                $d->impactedTickets = $ticketCount;

                $ticketCountCompleted = ProblemImpactedTicket::select('problem_impacted_tickets.ticket_id')
                    ->where(['problem_impacted_tickets.status' => 1,'problem_impacted_tickets.problem_id' => $d->id])->get();
                    
                $d->ticketcountnum = 0;
                if (!empty($ticketCount) && $ticketCount->count() > 0) {
                    $ticketediv = $ticketCountCompleted->count() / $ticketCount->count();
                    $ticketper = $ticketediv * 100;
                    $d->ticketcountnum = number_format($ticketper, 0);
                    $d->ticketcountnum = $d->ticketcountnum.'%';
                } else {
                    $d->ticketcountnum = '0%';
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getmessage());
        }
        return response()->json($return);
    }

    public function getproblemDetails($problemId) {
        DB::enableQueryLog();
        $return = array(
            "status" => "fail",
            "msg" => "Unable to display given details"
        );
       try {
            $currentUser = User::find(Auth::user()->id);//
            $query = ItmProblem::select('itm_problems.id', 'itm_problems.name as problem_name', 'content', 'pri.name as priority_name', 'dep.name as department_name', 'prb_cat.name as pc_category', 'sc.name as sc_category', 'uhp.username as username','itm_problems.created_at','itm_problems.updated_at')
            ->leftJoin('departments as dep', 'dep.id', '=', 'itm_problems.department_id')
            ->leftJoin('tkt_problem_categories as prb_cat', 'prb_cat.id', '=', 'itm_problems.problem_category_id')
            ->leftJoin('tkt_problem_categories as sc', 'sc.id', '=', 'itm_problems.sub_category_id')
            ->leftJoin('tkt_priorities as pri', 'pri.id', '=', 'itm_problems.priority_id')
            ->leftJoin('users as uhp', 'uhp.id', '=', 'itm_problems.problem_handler_id')
            ->where('itm_problems.id',$problemId);
            $d = $query->first();

            
            $deviceCount = ProblemImpactedDevice::select('problem_impacted_devices.device_id','problem_impacted_devices.status')
                ->where(['problem_impacted_devices.problem_id' => $d->id])->get();

            $d->impactedDevices = $deviceCount;

            $deviceCountCompleted = ProblemImpactedDevice::select('problem_impacted_devices.device_id')
                ->where(['problem_impacted_devices.status' => 1,'problem_impacted_devices.problem_id' => $d->id])->get();

            $d->devicecountnum = 0;
            if (!empty($deviceCount) && $deviceCount->count() > 0) {
                // $d->devicecountnum = $deviceCount;                    
                $devicediv = $deviceCountCompleted->count() / $deviceCount->count();
                $per = $devicediv * 100;
                $d->devicecountnum = number_format($per, 0);
                $d->devicecountnum = $d->devicecountnum.'%';
            } else {
                $d->devicecountnum = '0%';
            }
            
            //ticket count                
            $ticketCount = ProblemImpactedTicket::select('problem_impacted_tickets.ticket_id','problem_impacted_tickets.status')
                ->where(['problem_impacted_tickets.problem_id' => $d->id])->get();

            $d->impactedTickets = $ticketCount;

            $ticketCountCompleted = ProblemImpactedTicket::select('problem_impacted_tickets.ticket_id')
                ->where(['problem_impacted_tickets.status' => 1,'problem_impacted_tickets.problem_id' => $d->id])->get();
                
            $d->ticketcountnum = 0;
            if (!empty($ticketCount) && $ticketCount->count() > 0) {
                $ticketediv = $ticketCountCompleted->count() / $ticketCount->count();
                $ticketper = $ticketediv * 100;
                $d->ticketcountnum = number_format($ticketper, 0);
                $d->ticketcountnum = $d->ticketcountnum.'%';
            } else {
                $d->ticketcountnum = '0%';
            }
            $return = array(
                "status" => "success",
                "msg" => "Details fetched successfully",
                'data' => $d
            );
        } catch (\Exception $e) {
            Log::error($e->getmessage());
        }
        return response()->json($return);
    }

    public function ajaxInsert(Request $request) {
        if (!Auth::user()->hasPermissionTo('AddProblemManagement')) {
            $return = [
                "msg" => trans('content.service_ticket_fields.insufficident_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        $return = ["status" => "fail", "msg" => trans('content.problem_manager.unable_new')];
        $input = $request->all();
        try {
            DB::beginTransaction();
            $rules = [
                'name' => 'required',
                'department_id' => 'required',
                'problem_category_id' => 'required',
                'priority_id' => 'required',
            ];
            $msg = [
                'ticket_id.required' => 'Please select ticket',
                'device_id.required' => 'Please select device',
            ];

            $validator = Validator::make($request->all(), $rules, $msg);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $data = $request->only("name", "content", "priority_id", "department_id", "problem_category_id", "sub_category_id", "problem_handler_id");

            if(isset($data['problem_handler_id']) && $data['problem_handler_id'] != null && count($data['problem_handler_id']) > 0) {
                $data['problem_handler_id'] = implode(",",$data['problem_handler_id']);
            } else {
                $data['problem_handler_id'] = null;
            }
            $ItmProblem = new ItmProblem;
            $ItmProblem->status_id = '1';
            $ItmProblem->fill($data);
            $ItmProblem->save();
            $device = $request->only("device_id");
            if (!$device == "") {
                foreach ($device['device_id'] as $key => $value) {
                    $ProblemImpactedDevice = new ProblemImpactedDevice;
                    $ProblemImpactedDevice->device_id = $value;
                    $ProblemImpactedDevice->problem_id = $ItmProblem->id;
                    $ProblemImpactedDevice->save();
                }
            }

            if(isset($input['selectAllTickets']) && $input['selectAllTickets'] != null) {
                $ticket['ticket_id'] = Ticket::where([
                    'department_id' => $data['department_id'],
                    'problem_category_id' => $data['problem_category_id'],
                    'sub_category_id' => $data['sub_category_id'],
                ])->whereNotIn('status_id',[5,6])
                ->whereNull('tkt_tickets.is_temp')
                ->whereNull('tkt_tickets.merge_primary')
                ->whereNull('tkt_tickets.deleted_at')
                ->pluck('id')->toArray();
            } else {
                $ticket = $request->only("ticket_id");    
            }
            
            if (!$ticket == "") {
                foreach ($ticket['ticket_id'] as $key => $value) {
                    $ProblemImpactedTicket = new ProblemImpactedTicket;
                    $ProblemImpactedTicket->ticket_id = $value;
                    $ProblemImpactedTicket->problem_id = $ItmProblem->id;
                    $ProblemImpactedTicket->save();
                }
            }

            //adding history
            $historyData = [
                'action_type' => 1,
                'pm_id' => $ItmProblem->id,
                'updated_by' => Auth::user()->id,
            ];
            Common::problemManagerHistory($historyData);

            if($request->problem_handler_id) {
                foreach($request->problem_handler_id as $user) {
                    $user = User::find($user);
                    if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($user->email)->send(new IntimateAssigned($ItmProblem, $user));
                        } catch(\Exception $e) {
                            Log::error("Unable to send mail on create ticket from email " . $e->getMessage());
                        }
                    }
                }
            }

            $return["status"] = "success";
            $return["msg"] = trans('content.problem_manager.add_create');
            DB::commit();
            Log::info("PM ajaxInsert id:" . $ItmProblem->id . " ud:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("PM ajaxInsert: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function editProblem(Request $request, $id) {
        $return = array(
            "status" => "fail",
            "msg" => "Upload has not success"
        );
        try {
            DB::beginTransaction();
            $es = ItmProblem::select('itm_problems.id', 'itm_problems.name as subject_name', 'itm_problems.content as content', 'pri.name as priority_name', 'itm_problems.priority_id as priority_id', 'dep.name as department_name', 'itm_problems.department_id as department_id', 'itm_problems.problem_category_id as problem_category_id', 'prb_cat.name as pc_category', 'itm_problems.sub_category_id as sub_category_id', 'sc.name as sc_category', 'ta.username as username', 'itm_problems.problem_handler_id as problem_handler_id')
                ->leftJoin('departments as dep', 'dep.id', '=', 'itm_problems.department_id')
                ->leftJoin('tkt_problem_categories as prb_cat', 'prb_cat.id', '=', 'itm_problems.problem_category_id')
                ->leftJoin('tkt_problem_categories as sc', 'sc.id', '=', 'itm_problems.sub_category_id')
                ->leftJoin('tkt_priorities as pri', 'pri.id', '=', 'itm_problems.priority_id')
                ->leftJoin('users as ta', 'ta.id', '=', 'itm_problems.problem_handler_id')
                ->where('itm_problems.id', '=', $id)->first();
            if ($es) {
                $handle = [];
                if($es->problem_handler_id != null){
                    $handlers = explode(",",$es->problem_handler_id);
                    foreach($handlers as $h){
                        $handle[] = User::select('id',DB::raw("Concat(first_name,' ',last_name) as text"))->where('id',$h)->first();
                    }
                    $es->problem_handler_id = $handle;
                }
                $return['status'] = 'success';
                $return['msg'] = '';
                $return['data'] =  $es;
                DB::commit();
                Log::info("edit-itmproblem itm-problem itm-problem id:" . $es->id . " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
                return response()->json($return);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("edit-itm-problem: " . $e->getMessage());
        }
        return response()->json($return);
    }

    public function deleteProblem(Request $request, $id) {
        if (!Auth::user()->hasPermissionTo('DeleteProblemManagement') ) {
            $return = [
                "msg" => trans('content.service_ticket_fields.insufficident_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        $return = array(
            "status" => "failure",
            "msg" => "Unable to delete given details"
        );
        try {
            $tem_id = [$id];
            $ts = ItmProblem::where("id", "=", $tem_id)->first();
            $pt = ProblemImpactedTicket::whereIn("problem_id", $tem_id);
            $pd = ProblemImpactedDevice::whereIn("problem_id", $tem_id);
            if (!$ts) {
                return response()->json($return);
            }
            $ts->delete();
            if (!empty($pt)) {
                $pt->delete();
            }
            if (!empty($pd)) {
                $pd->delete();
            }

            //adding history
            $historyData = [
                'action_type' => 3,
                'pm_id' => $id,
                'updated_by' => Auth::user()->id,
            ];
            Common::problemManagerHistory($historyData);

            $return["status"] = "success";
            $return["msg"] = trans('content.ticket_incident.record_deleted') ." - ". $id;
        } catch (\Exception $e) {
            Log::error($e->message());
        }
        return response()->json($return);
    }

    public function filterProblem(Request $request, $id) {
        $return = array(
            "status" => "fail",
            "msg" => "something went wrong"
        );

        DB::beginTransaction();
        $input = $request->all();
        try {
            $query = ProblemImpactedTicket::select('problem_impacted_tickets.ticket_id', 'problem_impacted_tickets.ticket_id as ticket_id')
                ->leftJoin('tkt_tickets as tk', 'problem_impacted_tickets.ticket_id', '=', 'tk.id')
                ->where('problem_impacted_tickets.problem_id', '=', $id)->get();
            $countquery = count($query);
            if ($query) {

                for ($i = 0; $i < $countquery; $i++) {
                    $query[$i]->ticket_id = [$query[$i]->ticket_id];
                }
                $return['data'] = $query;
                $return['status'] = 'success';
                $return['msg'] = "";
            }
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("edit-itm-problem_ticket:" . $e->getMessage());
        }
        return response()->json($return);
    }

    public function filterProblemDevice(Request $request, $id) {
        $return = array(
            "status" => "fail",
            "msg" => "something went wrong"
        );

        $input = $request->all();
        try {
            DB::beginTransaction();
            $query = ProblemImpactedDevice::select('problem_impacted_devices.device_id', DB::raw("GROUP_CONCAT(a.asset_tag) as asset_tag"))
                ->leftJoin('assets as a', DB::raw("FIND_IN_SET(a.id,problem_impacted_devices.device_id)"), ">", DB::raw("'0'"))
                ->where('problem_impacted_devices.problem_id', '=', $id)->groupBy('problem_impacted_devices.device_id')->get();
            $countquery = count($query);
            if ($query) {
                for ($i = 0; $i < $countquery; $i++) {
                    $query[$i]->device_id = [$query[$i]->device_id];
                    $query[$i]->asset_tag = [$query[$i]->asset_tag];
                }
                $return['data'] = $query;
                $return['status'] = 'success';
                $return['msg'] = "";
            }
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("edit-itm-problem_device:" . $e->getMessage());
        }
        return response()->json($return);
    }

    public function searchFilteredDevice(Request $request){
        try{
            // dd($request->all());
            $models = [];
            if(isset($request->category) && $request->category !=null){
                $models['model_id'] = Model::whereIn('category_id',$request->category)->pluck('id')->toArray();
            };
            if(isset($request->manufacturer) && $request->manufacturer !=null){
                $models['model_id'] = Model::whereIn('manufacturer_id',$request->manufacturer)->pluck('id')->toArray();
            };
            if(isset($request->model) && $request->model !=null){
                $models['model_id'] = Model::whereIn('id',$request->model)->pluck('id')->toArray();
            };
            
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch',
            ];
            $db = DB::table("assets as a");
            $db->leftJoin("models as mdl", "a.model_id", "=", "mdl.id");
            $db->leftJoin("status_labels as s", "a.status_id", "=", "s.id");

            $db->select("a.id", "a.asset_tag","a.name as asset_name", "mdl.name", "mdl.modelno","a.serial", DB::raw("concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) as text"));
            $db->whereIn("a.model_id",$models['model_id']);
            $db->whereNull("a.deleted_at");
            $db->whereNull("s.sold");
            $db->whereNull("s.stolen_item");
            // if($search) {
            //     $db->whereRaw("(concat_ws('-',a.asset_tag,concat_ws(' ',a.name)) like '%" . $search . "%' or a.asset_tag like '%" . $search . "%'  or a.name like '%" . $search . "%' or mdl.name like '%" . $search . "%' or mdl.modelno like '%" . $search . "%'  or a.serial like '%" . $search . "%')");
            // }
            $count = $db->count();
            $result = $db->get();
            
            $return = [
                'status' => 'success',
                'msg' => 'fetched successfully',
            ];
            $return["results"] = count($result) ? $result->toArray() : [];
            return $return;
        } catch(\Exception $e) {
            Log::error("searchFilteredDevice : ".$e->getMessage());
            return $return;
        }
    }

    public function updateProblem(Request $request) {
        if (!Auth::user()->hasPermissionTo('EditProblemManagement')) {
            $return = [
                "msg" => trans('content.service_ticket_fields.insufficident_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        $return = ["status" => "fail", "msg" => trans('content.problem_manager.unable_new')];
        $input = $request->all();
        try {
            DB::beginTransaction();
            $rules = [
                'name' => 'required',
                'department_id' => 'required',
                'problem_category_id' => 'required',
                'priority_id' => 'required',
            ];
            $msg = [
                'ticket_id.required' => 'Please select ticket',
                'device_id.required' => 'Please select device',
            ];

            $validator = Validator::make($request->all(), $rules, $msg);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $data = $request->only("name", "content", "priority_id", "department_id", "problem_category_id", "sub_category_id", "problem_handler_id");
            if(isset($data['problem_handler_id']) && $data['problem_handler_id'] != null && count($data['problem_handler_id']) > 0) {
                $data['problem_handler_id'] = implode(",",$data['problem_handler_id']);
            } else {
                $data['problem_handler_id'] = null;
            }
            $us = ItmProblem::find($input['id']);
            $us->fill($data);
            $us->save();

            $device = $request->only("device_id");
            if (!$device == "") {
                $ProblemImpactedDevice = ProblemImpactedDevice::where("problem_id", "=", $input['id'])->pluck('device_id')->toArray();
                if (!in_array($ProblemImpactedDevice, $device)) {
                    $deleteRemovedRecord = ProblemImpactedDevice::where("problem_id", "=", $input['id'])
                    ->whereNotIn("device_id", $device['device_id'])
                    ->delete();
                    foreach ($device['device_id'] as $key => $value) {
                        $existingRecord = ProblemImpactedDevice::where([
                            "problem_id" => $us->id,
                            'device_id' => $value
                        ])->first();
                        if(empty($existingRecord)){
                            $ProblemImpactedDevice = new ProblemImpactedDevice;
                            $ProblemImpactedDevice->device_id = $value;
                            $ProblemImpactedDevice->problem_id = $us->id;
                            $ProblemImpactedDevice->save();
                        }
                    }
                }
            }

            if(isset($input['selectAllTickets']) && $input['selectAllTickets'] != null) {
                $ticket['ticket_id'] = Ticket::where([
                    'department_id' => $data['department_id'],
                    'problem_category_id' => $data['problem_category_id'],
                    'sub_category_id' => $data['sub_category_id'],
                ])->whereNotIn('status_id',[5,6])
                ->whereNull('tkt_tickets.is_temp')
                ->whereNull('tkt_tickets.merge_primary')
                ->whereNull('tkt_tickets.deleted_at')
                ->pluck('id')->toArray();
            } else {
                $ticket = $request->only("ticket_id");    
            }
            
            if (!$ticket == "") {
                $ProblemImpactedTicket = ProblemImpactedTicket::where("problem_id", "=", $input['id'])->pluck('ticket_id')->toArray();
                if (!in_array($ProblemImpactedTicket, $ticket)) {
                    $deleteRemovedRecord = ProblemImpactedTicket::where("problem_id", "=", $input['id'])
                    ->whereNotIn("ticket_id", $ticket['ticket_id'])
                    ->delete();
                    foreach ($ticket['ticket_id'] as $key => $value) {
                        $existingRecord = ProblemImpactedTicket::where([
                            "problem_id" => $us->id,
                            'ticket_id' => $value
                        ])->first();
                        if(empty($existingRecord)){
                            $ProblemImpactedTicket = new ProblemImpactedTicket;
                            $ProblemImpactedTicket->ticket_id = $value;
                            $ProblemImpactedTicket->problem_id = $us->id;
                            $ProblemImpactedTicket->save();
                        }
                    }
                }
            }

            //adding history
            $historyData = [
                'action_type' => 2,
                'pm_id' => $input['id'],
                'updated_by' => Auth::user()->id,
            ];
            Common::problemManagerHistory($historyData);

            $return["status"] = "success";
            $return["msg"] = trans('content.problem_manager.update_problem');
            DB::commit();
            Log::info("createproblemmanagement updateproblem  id:" . $us->id . " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("updateproblemmanager: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function ajaxTickets(Request $request) {
        $page = $request->input("page", 1);
        $return = ["items"=>[], "tot"=>0];
        $skip = (($page * 20) - 20);
        $input = $request->all();
        try {
            $search = $request->input("search", "");
            $q = trim($request->q);
            $ticketsObject = Ticket::where('status_id','!=', 6)->select('id', 'id AS text');
            if(!empty($request->department)){
                $ticketsObject->where('department_id', $request->department);
            }
            if($q) {
                $ticketsObject->where('id', 'like', $q.'%');
            }
            if(!empty($search)) {
                $ticketsObject->where('id', 'like', $search.'%');
            }

            $ticketsObject->orderBy('id', 'asc');
            $count = $ticketsObject->count();
            $ticketsObject->skip($skip)->take(20);
            $result = $ticketsObject->get();

            $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
            $return["results"] = count($result) ? $result->toArray() : [];
            return response()->json($return);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }
    }

    public function exportProblem(Request $request) {
        if (!Auth::user()->hasPermissionTo('DownloadProblemManagement')) {
            $return = [
                "msg" => trans('content.service_ticket_fields.insufficident_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        $req = $request->all();
        $query = ItmProblem::select('itm_problems.id', 'itm_problems.name as problem_name', 'content', 'pri.name as priority_name', 'dep.name as department_name', 'prb_cat.name as pc_category', 'sc.name as sc_category', 'itm_problems.problem_handler_id')
            ->leftJoin('departments as dep', 'dep.id', '=', 'itm_problems.department_id')
            ->leftJoin('tkt_problem_categories as prb_cat', 'prb_cat.id', '=', 'itm_problems.problem_category_id')
            ->leftJoin('tkt_problem_categories as sc', 'sc.id', '=', 'itm_problems.sub_category_id')
            ->leftJoin('tkt_priorities as pri', 'pri.id', '=', 'itm_problems.priority_id')
            ->leftJoin('users as uhp', 'uhp.id', '=', 'itm_problems.problem_handler_id');

        $return['total'] =  $query->count();
        $return['filtered'] = $return['total'];
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if (isset($filters->search)) {
                $req["filters"] = (array) $filters->other_filters;
                $req["search"] = $filters->search;
            }
            if (isset($req["filters"])) {
                $filters = $req["filters"];

                if (isset($filters["priority"]) && $filters['priority'] && $filters['priority'] != "null") {
                    $query->whereIn("pri.id", $filters['priority']);
                }
                if (isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                    $query->where("itm_problems.department_id", $filters['department']);
                }
                if (isset($filters["problem_category"]) && $filters['problem_category'] && $filters['problem_category'] != "null") {
                    $query->whereIn("itm_problems.problem_category_id", $filters['problem_category']);
                }
                if (isset($filters["sub_category"]) && $filters['sub_category'] && $filters['sub_category'] != "null") {
                    $query->whereIn("itm_problems.sub_category_id", $filters['sub_category']);
                }
                if (isset($filters["handler"]) && $filters['handler'] && $filters['handler'] != "null") {
                    $query->whereIn("uhp.id", $filters['handler']);
                }
            }
            if (isset($req["search"]) && $search_key = trim($req["search"])) {
                $whereStr = sprintf('(dep.name like "%%%1$s%%" or prb_cat.name like "%%%1$s%%" or sc.name like "%%%1$s%%" or pri.name like "%%%1$s%%" or content like "%%%1$s%%")', $search_key);
                $query->whereRaw($whereStr);
                $return['recordsFiltered'] = $query->count();
            }
        }
        $records = $query->get();
        foreach ($records as $d) {
            if($d->problem_handler_id != null) {
                $username = '';
                $handler = explode(",",$d->problem_handler_id);
                foreach($handler as $h){
                    $user= User::select(DB::raw("concat(first_name,' ', last_name) as username"))->where('id',$h)->first();
                    if($username == ''){
                        $username = $user->username;
                    }else{
                        $username .= ", ".$user->username;
                    }
                }
                $d->problem_handler_id = $username;
            }

            //device count
            $deviceCount = ProblemImpactedDevice::select('problem_impacted_devices.device_id')
            ->where(['problem_impacted_devices.problem_id' => $d->id])->count();
            
            $deviceCountCompleted = ProblemImpactedDevice::select('problem_impacted_devices.device_id')
            ->where(['problem_impacted_devices.status' => 1,'problem_impacted_devices.problem_id' => $d->id])->count();
            
            $d->devicecountnum = 0;
            if (!empty($deviceCount) && $deviceCount > 0) {
                // $d->devicecountnum = $deviceCount;
                $devicediv = $deviceCountCompleted / $deviceCount;
                $per = $devicediv * 100;
                $d->devicecountnum = number_format($per, 0);
                $d->devicecountnum = $d->devicecountnum.'%';
            }else{
                $d->devicecountnum = '0%';
            }
            
            //ticket count
            $ticketCount = ProblemImpactedTicket::select('problem_impacted_tickets.ticket_id')
            ->where(['problem_impacted_tickets.problem_id' => $d->id])->count();
            
            $ticketCountCompleted = ProblemImpactedTicket::select('problem_impacted_tickets.device_id')
            ->where(['problem_impacted_tickets.status' => 1,'problem_impacted_tickets.problem_id' => $d->id])->count();
            
            $d->ticketcountnum = 0;
            if (!empty($ticketCount) && $ticketCount > 0) {
                $ticketediv = $ticketCountCompleted / $ticketCount;
                $ticketper = $ticketediv * 100;
                $d->ticketcountnum = number_format($ticketper, 0);
                $d->ticketcountnum = $d->ticketcountnum.'%';
            }else{
                $d->ticketcountnum = '0%';
            }
            
        }
        $result = json_decode(json_encode($records, true), true);
        return Excel::download(new ProblemExport($result), 'Problem Management.xlsx');
    }

    public function checkUserAccess($problemId,$userId) {
        $problem = ItmProblem::find($problemId);
        $handlers = $problem->problem_handler_id;
        $handlersArray = explode(",",$handlers);
        if(Auth::user()->hasPermission('admin') || Auth::user()->hasPermission('superuser')){
            return true;
        }
        if(in_array($userId,$handlersArray)){
            return true;
        }else{
            return false;
        }
    }

    public function manageGroupProblem(Request $request, $Id) {
        if (!Auth::user()->hasPermissionTo('ManageProblemManagement') ) {
            $return = [
                "msg" => trans('content.service_ticket_fields.insufficident_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        $access = $this->checkUserAccess($Id,Auth::user()->id);
        if(!$access){
            $return = [
                "msg" => trans('content.service_ticket_fields.insufficident_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect()->back();
        };
        $problem = ItmProblem::leftJoin('users as u','u.id','problem_handler_id')->select('itm_problems.*',DB::raw("Concat(first_name,' ',last_name) as hanlder_name"))->where('itm_problems.id',$Id)->first();
        $groupDevice = ProblemImpactedDevice::select("problem_id")->where("problem_id", "=", $Id)->first();
        $groupTicket = ProblemImpactedTicket::select("problem_id")->where("problem_id", "=", $Id)->first();
        return view('problem_manager.problem_table')->with(compact('groupDevice', 'groupTicket','problem'));
    }

    public function getAjaxPrbolemManageDevice(Request $request) {
        try {
            $req = $request->all();
            $return = ['status' => 'fail', 'msg' => 'Unable to get ticket details'];
            $fields = array(
                '1' => 'a.asset_tag',
                '2' => 'problem_impacted_devices.status',
                '3' => 'problem_impacted_devices.created_at',
                '4' => 'handler_name',
            );

            $obj = ProblemImpactedDevice::select('problem_impacted_devices.id as id', 'problem_impacted_devices.device_id', 'a.asset_tag','problem_impacted_devices.created_at',DB::raw("concat(users.first_name,' ',users.last_name) as handler_name"))
                ->addSelect(DB::raw('case when problem_impacted_devices.status = 1 then "Complete" else "Incomplete" end as device_problem_status'))
                ->leftJoin('assets as a', 'a.id', '=', 'problem_impacted_devices.device_id')
                ->leftJoin('users', 'users.id', 'problem_impacted_devices.handler_id')
                ->where('problem_impacted_devices.problem_id', '=', $request->deviceId);


            $return['recordsTotal'] = $obj->count();
            $return['recordsFiltered'] = $return['recordsTotal'];

            if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
                $whereStr = sprintf('(a.asset_tag like "%%%1$s%%")', $search_key);
                $obj->whereRaw($whereStr);
                $return['recordsFiltered'] = $obj->count();
            }

            if (isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
                $obj->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
            }

            $page = 1;
            $skip = 0;
            $take = 10;
            if (isset($req["start"]) && isset($req["length"])) {
                $skip = (int) $req["start"];
                $take = (int) $req["length"];
            }
            $obj->skip($skip);
            $obj->take($take);
            $obj = $obj->get();
            if (!$obj) {
                return response()->json($return);
            }

            $return['status'] = 'success';
            $return['data'] = $obj;
            $return['msg'] = 'ImpactDevice fetched successfully.';
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error("getAjaxProblemManageDevice: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function getAjaxPrbolemManageTicket(Request $request)
    {
        try {
            $req = $request->all();
            $return = ['status' => 'fail', 'msg' => 'Unable to get Device Details'];
            $fields = array(
                '1' => 'problem_impacted_tickets.ticket_id',
                '2' => 'problem_impacted_tickets.status',
                '3' => 'problem_impacted_tickets.created_at',
                '4' => 'handler_name',
            );
            $obj = ProblemImpactedTicket::select('problem_impacted_tickets.id as id', 'problem_impacted_tickets.ticket_id as ticket_id','problem_impacted_tickets.created_at',DB::raw("concat(users.first_name,' ',users.last_name) as handler_name"))
                ->addSelect(DB::raw('case when problem_impacted_tickets.status = 1 then "Complete" else "Incomplete" end as ticket_problem_status'))
                ->leftJoin('tkt_tickets as tk', 'problem_impacted_tickets.ticket_id', '=', 'tk.id')
                ->leftJoin('users', 'users.id', 'problem_impacted_tickets.handler_id')
                ->where('problem_impacted_tickets.problem_id', '=', $request->ticketId);


            $return['recordsTotal'] = $obj->count();
            $return['recordsFiltered'] = $return['recordsTotal'];

            if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
                $whereStr = sprintf('(problem_impacted_tickets.ticket_id like "%%%1$s%%")', $search_key);
                $obj->whereRaw($whereStr);
                $return['recordsFiltered'] = $obj->count();
            }

            if (isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
                $obj->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
            }

            $page = 1;
            $skip = 0;
            $take = 10;
            if (isset($req["start"]) && isset($req["length"])) {
                $skip = (int) $req["start"];
                $take = (int) $req["length"];
            }
            $obj->skip($skip);
            $obj->take($take);
            $obj = $obj->get();
            if (!$obj) {
                return response()->json($return);
            }

            $return['status'] = 'success';
            $return['data'] = $obj;
            $return['msg'] = 'ImpactTicket fetched successfully.';
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error("getAjaxPrbolemManageTicket : " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function updateTicketStatus($action, $ticketId) {
        try {
            if($action == 'solve') {
                foreach($ticketId as $id) {
                    $ticket = Ticket::find($id);
                    $ticket->status_id = 5;
                    $ticket->resolved_at = Carbon::now();
                    $ticket->save();
                    
                    /* ticket got closed (or) reopened just now. Or as per config, need to store the user comment on followings */
                    $tf = new TktFollowing();
                    $tf->ticket_id = $ticket->id;
                    $tf->remarks = 'Ticket is marked as resolved from problem management.';
                    $tf->is_note = 1;
                    $tf->updated_by = Auth::user()->id;
                    $tf->updated_status = 5;
                    $tf->action_type = 2;
                    $tf->save();

                    //update status ticket history
                    $tkt_update['status'] = 5;
                    $tkt_update['ticket_id'] = $id;
                    $tkt_update['action_type'] = 2;
                    $tkt_update['updated_by'] = Auth::user()->id;
                    CommonHelper::ticketStatusHistory($tkt_update);

                    //sending intimation
                    $creator = $ticket->creator;
                    $handler = User::find($ticket->assigned_to);
                    $dep = Department::find($ticket->department_id);
                    $dephandler = User::find($dep->attender_id);
    
                    if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                        $add_back_trail = false;
    
                        try {
                            Mail::to($creator->email)->queue(new Resolved($ticket, $creator, $tf->remarks, $tf, $add_back_trail));
                        }
                        catch(\Exception $e) {
                            Log::error("updateStatus Mail: " . $e->getMessage());
                        }
                    }
                    
                }
            }
            if($action == 'unsolve') {
                foreach($ticketId as $id){
                    $ticket = Ticket::find($id);
                    $ticket->status_id = 2;

                    
                    /* ticket got closed (or) reopened just now. Or as per config, need to store the user comment on followings */
                    $tf = new TktFollowing();
                    $tf->ticket_id = $ticket->id;
                    $tf->remarks = 'Ticket is marked as reopened from problem management.';
                    $tf->is_note = 1;
                    $tf->updated_by = Auth::user()->id;
                    $tf->updated_status = 2;
                    $tf->action_type = 2;
                    $tf->save();

                    //update status ticket history
                    $tkt_update['status'] = 2;
                    $tkt_update['ticket_id'] = $id;
                    $tkt_update['action_type'] = 2;
                    $tkt_update['updated_by'] = Auth::user()->id;
                    CommonHelper::ticketStatusHistory($tkt_update);

                    /* for reopening */
                    
                    $current_datetime = Carbon::now(config('app.timezone'));
                    $ticket->reopened_by = Auth::user()->id;
                    $ticket->reopened_at = $current_datetime->format('Y-m-d H:i:s');
                    $resolved_at = Carbon::createFromFormat('Y-m-d H:i:s', $ticket->resolved_at);
                    
                    $tat_expire = Carbon::createFromFormat('Y-m-d H:i:s', $ticket->tat_expire);
                    
                    if($tat_expire->gt($resolved_at)) {
                        $c = Config::first();
                        $c->setWeekEnds();
                        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $ticket->created_at);

                        $holidays = Holiday::getHolidaysFrom($created_at);
                        
                        $diffInMin = $ticket->tat_remaining_mins;
                        $data["tat_expire"] = $c->calculateAdvancedTat($diffInMin, Carbon::now(config('app.timezone')), $holidays, "m");
                        $data["tat_remaining_mins"] = $c->calculateRemainingTat(Carbon::createFromFormat('Y-m-d H:i:s', $data["tat_expire"]), $holidays);
                    }


                    $data['updated_by'] = Auth::user()->id;
                    $ticket->fill($data);
                    $ticket->updated_at = Carbon::now(config('app.timezone'));
                    $ticket->save();
                
                }
            }
            return true;
        } catch(\Exception $e) {
            Log::error("updateTicketStatus : ".$e->getMessage());
            return false;
        }
    }

    public function updateImpactedTicketStatus(Request $request,$action) {
        $return = ["status" => "fail", "msg" => trans('content.problem_manager.unable_to_update')];
        if($action == 'solve') {
            return $this->solveProblem($request);
        } elseif($action == 'unsolve') {
            return $this->unsolveProblem($request);
        }
        return response()->json($return);
    }

    public function solveProblem(Request $request) {
        $return = ["status" => "fail", "msg" => trans('content.problem_manager.unable_to_update')];
        $input = $request->all();
        try {
            DB::beginTransaction();
            $us = ProblemImpactedTicket::whereIn('problem_impacted_tickets.ticket_id', $input['ticket_Id'])->where('problem_impacted_tickets.problem_id', '=', $request->problem_Id)->update(['status' => '1','handler_id' => Auth::user()->id]);
            
            $updateProblemTable = ItmProblem::find($request->problem_Id);
            $updateProblemTable->updated_at = date("Y-m-d H:i:s");
            $updateProblemTable->save();

            $statusUpdate = $this->updateTicketStatus('solve',$input['ticket_Id']);
            if(!$statusUpdate) {
                return $return;
            }

            $ticketIds = '';
            foreach($input['ticket_Id'] as $ticket) {
                $ticketIds .= "#".$ticket." | ";
            }
            //adding history
            $historyData = [
                'action_type' => 4,
                'action' => 'solved',
                'ticketId' => $ticketIds,
                'pm_id' => $request->problem_Id,
                'updated_by' => Auth::user()->id,
            ];
            Common::problemManagerHistory($historyData);

            $return["status"] = "success";
            $return["msg"] = trans('content.problem_manager.update_problem');
            DB::commit();
            Log::info("solveProblem id:" . $us->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("solveProblem: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function unsolveProblem(Request $request) {
        $return = ["status" => "fail", "msg" => trans('content.problem_manager.unable_to_update')];
        $input = $request->all();
        try {
            DB::beginTransaction();
            $us = ProblemImpactedTicket::whereIn('problem_impacted_tickets.ticket_id', $input['ticket_Id'])->where('problem_impacted_tickets.problem_id', '=', $request->problem_Id)->update(['status' => '0','handler_id' => Auth::user()->id]);

            $updateProblemTable = ItmProblem::find($request->problem_Id);
            $updateProblemTable->updated_at = date("Y-m-d H:i:s");
            $updateProblemTable->save();

            $statusUpdate = $this->updateTicketStatus('unsolve',$input['ticket_Id']);
            if(!$statusUpdate) {
                return $return;
            }

            $ticketIds = '';
            foreach($input['ticket_Id'] as $ticket) {
                $ticketIds .= "#".$ticket." | ";
            }
            //adding history
            $historyData = [
                'action_type' => 4,
                'action' => 'unsolved',
                'ticketId' => $ticketIds,
                'pm_id' => $request->problem_Id,
                'updated_by' => Auth::user()->id,
            ];
            Common::problemManagerHistory($historyData);

            $return["status"] = "success";
            $return["msg"] = trans('content.problem_manager.update_problem');
            DB::commit();
            Log::info("unsolveProblem id:" . $us->id . " uid:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("unsolveProblem: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function updateImpactedDeviceStatus(Request $request,$action) {
        if($action == 'solve'){
            return $this->solveDeviceProblem($request);
        }else{
            return $this->unsolveDeviceProblem($request);
        }
    }

    public function solveDeviceProblem(Request $request) {
        $return = ["status" => "fail", "msg" => trans('content.problem_manager.unable_to_update')];
        $input = $request->all();
        try {
            DB::beginTransaction();
            $us = ProblemImpactedDevice::whereIn('problem_impacted_devices.device_id', $input['device_id'])->where('problem_impacted_devices.problem_id', '=', $request->problem_id)->update(['status' => '1','handler_id' => Auth::user()->id]);
            
            $updateProblemTable = ItmProblem::find($request->problem_id);
            $updateProblemTable->updated_at = date("Y-m-d H:i:s");
            $updateProblemTable->save();

            $deviceTags = '';
            foreach($input['device_id'] as $device){
                $asset = DB::table('assets')->select('asset_tag')->where('id',$device)->first();
                $deviceTags .= $asset->asset_tag." | ";
            }

            //adding history
            $historyData = [
                'action_type' => 5,
                'action' => 'solved',
                'devices' => $deviceTags,
                'pm_id' => $request->problem_id,
                'updated_by' => Auth::user()->id,
            ];
            Common::problemManagerHistory($historyData);

            $return["status"] = "success";
            $return["msg"] = trans('content.problem_manager.update_problem');
            DB::commit();
            Log::info("solveDeviceProblem id:" . $updateProblemTable->id . " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("solveDeviceProblem: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function unsolveDeviceProblem(Request $request)
    {
        $return = ["status" => "fail", "msg" => trans('content.problem_manager.unable_to_update')];
        $input = $request->all();
        try {
            DB::beginTransaction();
            $us = ProblemImpactedDevice::whereIn('problem_impacted_devices.device_id', $input['device_id'])->where('problem_impacted_devices.problem_id', '=', $request->problem_id)->update(['status' => '0','handler_id' => Auth::user()->id]);
            $updateProblemTable = ItmProblem::find($request->problem_id);
            $updateProblemTable->updated_at = date("Y-m-d H:i:s");
            $updateProblemTable->save();

            $deviceTags = '';
            foreach($input['device_id'] as $device) {
                $asset = DB::table('assets')->select('asset_tag')->where('id',$device)->first();
                $deviceTags .= $asset->asset_tag." | ";
            }
            //adding history
            $historyData = [
                'action_type' => 5,
                'action' => 'unsolved',
                'devices' => $deviceTags,
                'pm_id' => $request->problem_id,
                'updated_by' => Auth::user()->id,
            ];
            Common::problemManagerHistory($historyData);

            $return["status"] = "success";
            $return["msg"] = trans('content.problem_manager.update_problem');
            DB::commit();
            Log::info("unsolveDeviceProblem id:" . $updateProblemTable->id . " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("unsolveDeviceProblem: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function pmHistory(Request $request){
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to fetch history'
            ];
            $historyObj = ProblemManagerHistory::select('problem_manager_history.*',DB::raw("concat(users.first_name,' ',users.last_name) as updater_name"))
            ->leftjoin('users','users.id','problem_manager_history.updated_by')
            ->addSelect(DB::raw('DATE_FORMAT(problem_manager_history.created_at, "%d %b %y %h:%i %p") as updated_at_format'))
            ->where('pm_id',$request->pm_id)
            ->orderby('created_at','desc')
            ->get();
            $return = [
                'status' => 'success',
                'msg' => 'History fetched successfully',
                'data' => $historyObj,
            ];
            return $return;
        } catch(\Exception $e) {
            Log::error("pmHistory : ".$e->getMessage());
            return $return;
        }
    }
}

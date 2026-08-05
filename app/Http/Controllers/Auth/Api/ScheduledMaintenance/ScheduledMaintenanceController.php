<?php

namespace App\Http\Controllers\Auth\Api\ScheduledMaintenance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NewScheduledMaintenance;
use App\Models\NewScheduledTaskMaintenance;
use App\Models\NewScheduledPlanAllocation;
use Auth;
use Storage;
use Validator;
use DB;
use Illuminate\Support\Facades\Log;
use File;
use Image;

class ScheduledMaintenanceController extends Controller
{
    
    public function PlanList(Request $request) {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to view list'
        ];
        try {
            $query = NewScheduledMaintenance::select('mpl.id', 'mpl.plan_id', 'mpl.planned_date', 'tkt_scheduled_maintenance_plan.title', 'tkt_scheduled_maintenance_plan.description','tkt_scheduled_maintenance_plan.duration');
            $query->addSelect(DB::raw('case when tkt_scheduled_maintenance_plan.recursive_plan = 1 then "Daily" when tkt_scheduled_maintenance_plan.recursive_plan = 2 then "Weekly" when tkt_scheduled_maintenance_plan.recursive_plan = 3 then "Monthly" when tkt_scheduled_maintenance_plan.recursive_plan = 4 then "Quarterly" when tkt_scheduled_maintenance_plan.recursive_plan = 5 then "Yearly" else "" end as recursive_plan_option'));
            $query->leftJoin('tkt_scheduled_maintenance_plan_allocation as mpl', 'mpl.plan_id', '=', 'tkt_scheduled_maintenance_plan.id');
            if($request["device_id"] != '') {
                $query->where('mpl.device_id', '=', $request["device_id"]);
            }
            $data=$query->get();
            foreach($data as $d) {
                $duration = explode("-",$d['duration']);
                $d['duration'] = $duration[0]." days ".$duration[1]." hours";
            }
            $return["status"] = "success";
            $return["msg"] = "Scheduled Plan of device";
            $return['data'] = $data;
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($return);
        }
        return response()->json($return);
    }

    public function TaskList(Request $request) {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to view list'
        ];
        try {
            $query = NewScheduledTaskMaintenance::select('mpl.id as allowcated_plan','tkt_scheduled_maintenance_task_list.id as task_id','tkt_scheduled_maintenance_task_list.task_title','tkt_scheduled_maintenance_task_list.order_no','tkt_scheduled_maintenance_task_list.proof_required','tkt_scheduled_maintenance_task_list.downtime_required','tkt_scheduled_maintenance_task_list.downtime_duration','tkt_scheduled_maintenance_task_list.gps_required','tkt_scheduled_maintenance_task_list.is_mandetory','tkt_scheduled_maintenance_task_list.description');
            $query->leftJoin('tkt_scheduled_maintenance_plan_allocation as mpl', 'mpl.plan_id', '=', 'tkt_scheduled_maintenance_task_list.plan_id');
            $query->where('mpl.device_id', '=', $request["device_id"]);
            $query->where('mpl.plan_id', '=', $request["plan_id"]);
            $query->get();
            $return["status"] = "success";
            $return["msg"] = "Task Listing of device";
            $return['data'] = $query->get();
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($return);
        }
        return response()->json($return);
    }

    public function taskUpdate(Request $request){
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to update the Task'
        ];
        try {
            $data = $request->only('plan_allow_id','status','task_id','lattitude','longitude','description','proof_details');
            $rules = [
                'plan_allow_id' => "required|integer",
                'task_id' => "required|integer",
                'status' => "required|integer",
                'proof_details.*' => 'image|mimes:jpeg,png,jpg',
            ];
            $messages = [];
            $validate = Validator::make($data, $rules, $messages);
            if ($validate->fails()) {
                $v = $validate->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $file_data = array();
            $files = $request->file('proof_details');
            foreach ($files as $file) {
                $attachments = array();
                $attachments['original_file_name'] = $file->getClientOriginalName();
                $attachments['extension'] = strtolower($file->getClientOriginalExtension());
                $attachments['file_name'] = $file->store("", "scheduled_maintenance");
                $path = storage_path('scheduled_maintenance') . DIRECTORY_SEPARATOR . $attachments['file_name'];
                $attachments['path']=$path;
                array_push($file_data,$attachments);
            }
            $obj = new NewScheduledPlanExecution();
            $data['proof_details'] = json_encode($file_data);
            $obj->fill($data);
            if($obj->save()) {
                $return["status"] = "success";
                $return["msg"] = "Task has been updated successfully";
            }
        }
        catch(\Exception $e) {
            return response()->json($return);
        }
        return response()->json($return);
    }
}

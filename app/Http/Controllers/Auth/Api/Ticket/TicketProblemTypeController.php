<?php

namespace App\Http\Controllers\Auth\Api\Ticket;

use App\Http\Controllers\Controller;
use App\Models\TktProblemType;
use App\Models\TktServiceType;
use App\Models\Ticket\Priority;
use App\Models\Ticket\Ticket;
use App\Models\Department;
use Illuminate\Http\Request;

use Validator;
use Auth;
use DB;

class TicketProblemTypeController extends Controller
{
    public function index() {
        $priorities = Priority::select("id","name","service_time")->get();
        return view("ticketProblemType.index")->with("priorities", $priorities);
    }
    
    /* type ajax response */
    public function ajaxIndex(Request $request) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            '1' => 'pt.name',
            '2' => 'st.name',
            '3' => 'department',
            '4' => 'company',
            '5' => 'p.name',
            '6' => 'pt.tat',
            '7' => 'pt.updated_at'
        );

        $db = DB::table('tkt_problem_types as pt');
        $db->join('tkt_service_types as st', 'pt.service_type_id', '=', 'st.id');
        $db->join('departments as d', 'pt.department_id', '=', 'd.id');
        $db->join('companies as c', 'd.company_id', '=', 'c.id');
        $db->leftJoin('tkt_priorities as p', 'pt.priority_id', '=', 'p.id');

        $db->select('pt.id', 'pt.name as pt_name', 'st.name as st_name', 'c.name as company', 'd.name as department', 'pt.tat', 'p.name as priority');
        $db->addSelect(DB::raw('DATE_FORMAT(pt.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        
        if(! Auth::user()->isSuperUser()) {
            $db->where('d.company_id', '=', Auth::user()->company_id);
        }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
            $whereStr = sprintf('(pt.name like "%%%1$s%%" or pt.tat like "%%%1$s%%" or st.name like "%%%1$s%%" or c.name like "%%%1$s%%" or d.name like "%%%1$s%%" or p.name like "%%%1$s%%" or DATE_FORMAT(pt.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
            $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
        }

        $skip = 0;
        $take = 10;
        if( isset($req["start"]) && isset($req["length"]) ) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }
        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return['data'] = array();
        foreach($data as $d) {
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    /* create new type */
    public function ajaxCreate(Request $request) {
        $return = ["status"=>"fail", "msg"=>"Unable to create problem type"];
        $data = $request->only("department_id", "name", "service_type_id", "priority_id", "tat");
        $rules = [
            'department_id' =>'required|exists:departments,id',
            'service_type_id' =>'required|exists:tkt_service_types,id',
            'priority_id' =>'required|exists:tkt_priorities,id',
            'tat' =>'nullable|integer|min:0|max:1000',
            'name' => 'required|string|max:75'
        ];

        $validator = Validator::make($data, $rules);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        
        $id = TktProblemType::create($data);
        if(! $id) {
            return response()->json($return);
        }
        
        $return["status"] = "success";
        $return["msg"] = "The Problem Type added successfully.";
        return response()->json($return);
    }
    
    /* update the type info */
    public function ajaxUpdate(Request $request) {
        $return = ["status"=>"fail", "msg"=>"Unable to update problem type"];
        $data = $request->only("department_id", "name", "service_type_id", "priority_id", "tat", "id");
        $rules = [
            'department_id' =>'required|exists:departments,id',
            'service_type_id' =>'required|exists:tkt_service_types,id',
            'priority_id' =>'required|exists:tkt_priorities,id',
            'tat' =>'nullable|integer|min:0|max:1000',
            'name' => 'required|string|max:75',
            'id' => 'required|exists:tkt_problem_types,id'
        ];

        $validator = Validator::make($data, $rules);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $st = TktProblemType::find($request->id);
        $st->name = trim($data["name"]);
        $st->department_id = trim($data["department_id"]);
        $st->service_type_id = trim($data["service_type_id"]);
        $st->priority_id = trim($data["priority_id"]);
        $st->tat = trim($data["tat"]);
        $st->save();
        $return["status"] = "success";
        $return["msg"] = "Problem Type has updated successfully.";
        return response()->json($return);
    }
    
    /* delete type */
    public function ajaxDelete(Request $request, $id) {
        $return = ["status"=>"fail", "msg" => "Unable to delete type"];

        $st = TktProblemType::find($id);
        if(! $st) {
            return response()->json($return);
        }
        
        $count = Ticket::where("problem_type_id", "=", $id)->count();
        if($count) {
            $return["msg"] = "Some tickets are using this problem type. So unable to delete";
            return response()->json($return);
        }

        $st->delete();
        $return["status"] = "success";
        $return["msg"] = "Problem Type has deleted Successfully!";
        return response()->json($return);
    }
    
    /* get the problem type info for load in edit form */
    public function ajaxGetEditInfo(Request $request, $id) {
        $return = array("status"=>"failure", "msg"=>"Unable to get problem type for edit.");
        $type = TktProblemType::find($id);
        if(! $type) {
            return response()->json($return);
        }

        $dev = array();
        $dev["data"] = $type->only("id", "department_id", "name", "service_type_id", "priority_id", "tat");
        $dev["dropdown"] = array();
        if($type->department_id) {
            $getDepartment = Department::where("id", $type->department_id)->select("id", "name as text")->first();
            $dev["dropdown"]["department"] = $getDepartment->exists ? $getDepartment->toArray() : null;
        }
        if($type->service_type_id) {
            $getService = TktServiceType::where("id", $type->service_type_id)->select("id", "name")->first();
            $dev["dropdown"]["service_type"] = $getService->exists ? $getService->toArray() : null;
        }

        $return['status'] = 'success';
        $return['msg'] = '';
        $return['type'] = $dev;
        return $return;
    }
    
    /* list problem type for options */
    public function ajaxOptions($id) {
        $return = array("status"=>"failure", "data"=>array(), "msg"=>"No Problem Type found.");
        $types = TktProblemType::where("service_type_id", "=", $id)->select("id", "name", "priority_id", "tat")->orderBy("name")->get();
        if(count($types)) {
            $return["data"] = $types->toArray();
            $return["msg"] = "";
            $return["status"] = "success";
        }
        return response()->json($return);
    }
}

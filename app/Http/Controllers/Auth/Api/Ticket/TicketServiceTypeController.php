<?php

namespace App\Http\Controllers\Auth\Api\Ticket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;

use App\Models\TktServiceType;
use App\Models\Department;
use App\Models\User;
use Validator;

class TicketServiceTypeController extends Controller
{
    /* type page */
    public function index() {
        return view("ticketServiceType.index");
    }

    /* type ajax response */
    public function ajaxIndex(Request $request) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            '1' => 'type',
            '2' => 'department',
            '3' => 'company',
            '4' => 'u.username',
            '5' => 'st.updated_at'
        );

        $db = DB::table('tkt_service_types as st');
        $db->join('departments as d', 'st.department_id', '=', 'd.id');
        $db->join('companies as c', 'd.company_id', '=', 'c.id');
        $db->leftJoin('users as u', 'st.ticket_attender', '=', 'u.id');

        $db->select('st.id', 'st.name as type', 'c.name as company', 'd.name as department', DB::raw('case when u.username is null then "" else u.username end as attender'));
        $db->addSelect(DB::raw('DATE_FORMAT(st.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        
        if(! Auth::user()->isSuperUser()) {
            $db->where('d.company_id', '=', Auth::user()->company_id);
        }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
            $whereStr = sprintf('(st.name like "%%%1$s%%" or c.name like "%%%1$s%%" or d.name like "%%%1$s%%" or u.username like "%%%1$s%%" or DATE_FORMAT(st.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
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

    /* delete type */
    public function ajaxDelete(Request $request, $id) {
        $return = ["status"=>"fail", "msg" => "Unable to delete type"];

        $st = TktServiceType::find($id);
        if(! $st) {
            return response()->json($return);
        }

        $st->delete();
        $return["status"] = "success";
        $return["msg"] = "Service Type has deleted Successfully!";
        return response()->json($return);
    }

    /* create new type */
    public function ajaxCreate(Request $request) {
        $return = ["status"=>"fail", "msg"=>"Unable to create service type"];
        $data = $request->only("department_id", "name", "ticket_attender");
        $rules = [
            'department_id' =>'required|exists:departments,id',
            'name' => 'required|string|max:75',
            'ticket_attender' => 'required|exists:users,id'
        ];

        $validator = Validator::make($data, $rules);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        
        $created_id = TktServiceType::create($data);
        $return["status"] = "success";
        $return["msg"] = "The Service Type added successfully.";
        return response()->json($return);
    }

    /* update the type info */
    public function ajaxUpdate(Request $request) {
        $return = ["status"=>"fail", "msg"=>"Unable to update service type"];
        $data = $request->only("department_id", "name", "id", "ticket_attender");
        $rules = [
            'id' => 'required|exists:tkt_service_types,id',
            'department_id' =>'required|exists:departments,id',
            'name' => 'required|string|max:75',
            'ticket_attender' => 'required|exists:users,id'
        ];

        $validator = Validator::make($data, $rules);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $st = TktServiceType::find($request->id);
        $st->name = trim($data["name"]);
        $st->department_id = trim($data["department_id"]);
        $st->ticket_attender = trim($data["ticket_attender"]);
        $st->save();
        $return["status"] = "success";
        $return["msg"] = "Service Type has updated successfully.";
        return response()->json($return);
    }

    /* get the service type info for load in edit form */
    public function ajaxGetEditInfo(Request $request, $id) {
        $return = array("status"=>"failure", "msg"=>"Unable to get service type for edit.");
        $type = TktServiceType::find($id);
        if(! $type) {
            return response()->json($return);
        }

        $dev = array();
        $dev["data"] = $type->only("id", "name", "department_id");
        $dev["dropdown"] = array();
        if($type->department_id) {
            $getDepartment = Department::where("id", $type->department_id)->select("id", "name as text")->first();
            $dev["dropdown"]["department"] = $getDepartment->exists ? $getDepartment->toArray() : null;
        }
        if($type->ticket_attender) {
            $getAttender = User::where("id", $type->ticket_attender)->select("id", DB::raw('concat(first_name, " ", last_name, " (", username, ")") as text'))->first();
            $dev["dropdown"]["ticket_attender"] = $getAttender->exists ? $getAttender->toArray() : null;
        }

        $return['status'] = 'success';
        $return['msg'] = '';
        $return['type'] = $dev;
        return $return;
    }

    /* list service type for options */
    public function ajaxOptions(Request $request, $id) {
        $return = array("status"=>"fail", "data"=>array(), "msg"=>"No Service Type found.");
        if(!$id || $id < 1) {
            return response()->json($return);
        }
        
        $return["data"] = TktServiceType::where("department_id", "=", $id)->select("id", "name")->orderBy("name")->get();
        $return["msg"] = "";
        $return["status"] = "success";
        return response()->json($return);
    }
}

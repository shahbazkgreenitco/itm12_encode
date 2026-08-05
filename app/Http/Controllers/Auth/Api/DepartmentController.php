<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Location;
use App\Models\Company;
use App\Models\User;

Use Validator;
use Illuminate\Validation\Rule;
Use Auth;
use DB;
use App\Helpers\Common as CommonHelper;
use App\Models\CustomField;
use App\Models\CustomFieldset;
use App\Models\Ticket\ProblemCategory;

class DepartmentController extends Controller {

    public function getIndex() {
       
        $return = ["status" => "fail", "msg" => "Unable to get the options"];

        $companies = Company::select("id", "name as text")->get()->toArray();
        $attenders = User::select("id", "username as text")->get()->toArray();

        $return['status'] = "success";
        $return['msg'] = '';
        $return['companies'] = $companies;
        $return['attenders'] = $attenders;
        return response()->json($return);
    }

    public function ajaxDepartment(Request $request) {
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:0|max:14',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];

        $order = [
            0 => 'asc',
            1 => 'desc'
        ];
        $messages = [  ];

        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
        // echo 'hi';die;

            return response()->json([
                "status"    => 'fail',
                "msg"       => 'Error in validation',
                "errors"    => $validator->messages()
                ]);
        }


        $req = $request->all();

        $page = $request->index ? $request->index : 0;

        $take = $request->list_size ? $request->list_size : 20;
        $skip = $page * $take;

        $fields = array(
            '1'  => 'name',
            '2'  => 'attender_name',
            '3'  => 'company_name'
        );

        $db = DB::table('departments as a');
        $db->leftJoin('users as u', 'a.attender_id', '=', 'u.id');
        $db->leftJoin('companies as cmp', 'a.company_id', '=', 'cmp.id');
        
        $db->select('a.name as name','cmp.name as company_name','a.id');
        $db->addSelect(DB::raw('concat(u.first_name, " ", u.last_name) as attender_name'));

        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(a.name like "%%%1$s%%" or cmp.name like "%%%1$s%%" or concat(u.first_name, " ", u.last_name) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['filter_record'] = $db->count();
            $return['search_key'] = $search_key;
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
            $db->orderBy($deviceInfoFields[$req["order_by"]], $order[$req["order_dir"]]);
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


    public function ajaxAddDepartment(Request $request) {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to add the department'
        ];

        $data = $request->only('name', 'company_id', 'attender_id');
        $rules = [
            'name' =>'required|max:100|unique:departments,name',
            'company_id' => 'required|exists:companies,id',
            'attender_id'=> 'nullable|string|exists:users,id',
        ];
        $messages = [
            'name.required' => 'Please provide Name of department .',
            'company_id.required' => 'Please provide Name of company .',
        ];
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
        }

        $objdept = new Department;
        $objdept->name = $data["name"];
        $objdept->company_id = $data["company_id"];
        $objdept->attender_id = $data["attender_id"] != "null"? $data["attender_id"]: 0;

        if($objdept->save()) {
            $return["msg"] = "The department has been added successfully.";
            $return["status"] = "success";
        
        }

        return response()->json($return);
    }

    public function ajaxGetDepartment(Request $request, $id) {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to get the department'
        ];

        $objdept = "";
        try {
            $objdept = Department::find($id);

            $data = [];
            $data['dropdown'] = array();
            $data['data'] = $objdept->only('id','name','company_id');

            if($objdept->attender_id) {
                $getUser = User::where("id", $objdept->attender_id)->select("id",  DB::raw('concat(first_name, " ", last_name, "") as text'))->first();
                $data["dropdown"]["attender_id"] = $getUser && $getUser->exists ? $getUser->toArray() : null;
            }

            $return['data'] = $data;
            $return['msg'] = null;
            $return['status'] = 'success';
            return response()->json($return);
        }
        catch(\Exception $e) {
            return response()->json($return);        
        }
    }

    public function ajaxEditDepartment(Request $request, $id) {

        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($objDpt) )
        return response()->json(['status' => 'error', 'msg' => 'Insufficient permission for this department !!']);
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to edit the depreciation'
        ];

        $objdept = "";
        try {
            $objdept = Department::find($id);

            if( empty($objdept) ) {
                return response()->json($return);
            }
        }
        catch(\Exception $e) {
            return response()->json($return);        
        }

        $data = $request->only('name', 'company_id','attender_id');
        $rules = [
            'name' => [
                'required',
                Rule::unique('departments')->ignore($objdept->id)
            ],
            'company_id' => 'required|exists:companies,id',
            'attender_id'=> 'sometimes|nullable|exists:users,id'
        ];
        $messages = [
            'name.required' => 'Please provide Name of department .',
            'company_id.required' => 'Please select company .',
        ];
        $validator = Validator::make($data, $rules,$messages);
      
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
        }

        $objdept->name = trim($data["name"]);
        $objdept->company_id = $data["company_id"];
        $objdept->attender_id = $data["attender_id"] != "null"? $data["attender_id"]: 0;

        if ($objdept->save()) {
            $return["msg"] = "Department has been updated successfully";
            $return["status"] = "success";
        }

        return response()->json($return);
    }


    public function addDepartment(Request $request) {
        $objdept = new Department();

        $rules = [
            'name' =>'required|max:100|unique:departments,name',
            'company_id' => 'required|exists:companies,id',
            'attender_id'=> 'sometimes|nullable|exists:users,id'
        ];
        $messages = [
            'name.required' => 'Please provide Name of department .',
        ];
        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
            return response(array('status'=>'error','errors' => $validator->messages()));
        }else{
            if( !Auth::user()->isSuperUser() && Auth::user()->company_id != $request->company_id )
                return response()->json(['status' => 'error', 'msg' => 'Insufficient permission !!']);

            $objdept->name = $request->name;
            $objdept->company_id = $request->company_id;
            $objdept->attender_id = $request->attender_id;
            $objdept->save();
            return response(array('status'=>'success','msg'=>'Department created successfully!!'));
        }
    }

    public function detailDepartment($id, Request $request) {
        $record = Department::with('attenders')->find($id);
        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($record) )
            return response()->json(['status' => 'error', 'msg' => 'Insufficient permission for this department !!']);
        return $record;
    }
    public function editDepartment($id, Request $request) {
        $objDpt = Department::find($id);

        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($objDpt) )
            return response()->json(['status' => 'error', 'msg' => 'Insufficient permission for this department !!']);
        $rules = [
            'name' => [
                'required',
                Rule::unique('departments')->ignore($objDpt->id)
            ],
            'company_id' => 'required|exists:companies,id',
            'attender_id'=> 'sometimes|nullable|exists:users,id'
        ];
        $messages = [
            'name.required' => 'Please provide Name of department .',
            'company_id.required' => 'Please select company .',
        ];
        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
            return response(array('status'=>'error','errors' => $validator->messages()));
        }else{
            $objDpt->name = $request->name;
            $objDpt->company_id = $request->company_id;
            $objDpt->attender_id = $request->attender_id;
            $objDpt->save();
            return response(array('status'=>'success','msg'=>'Department has been updated successfully!'));
        }

    }

    public function getCompanyDeptmts()
    {
        // $company_id = Auth::user()->company_id;
        $objDepartment = new Department;
        $results = $objDepartment->getThisCompanyDeptmts();

        echo json_encode($results);
    }


    public function getAttenders($company_id,Request $request) {
        
        if( !Auth::user()->isSuperUser() && $company_id != Auth::user()->company_id )
            return response()->json(['status' => 'error', 'msg' => 'Insufficient permission for this department !!']);

        $records = \App\Models\User::where('company_id','=',$company_id)->select(['id','first_name','last_name'])->get();
        return $records;
    }
    public function ajaxIndex(Request $request) {
        // print_r($request->deletedRecords);var_dump(intval($request->deletedRecords));die;
        // $records = Model::withTrashed()->with(['manufacturer','category','depreciation'])->withCount('assets');
        if(Auth::user()->isSuperUser())
            $records = Department::with('company')->get();
        else
            $records = Department::with('company')->where('company_id','=',Auth::user()->company_id)->get();
        $return = array(
            "draw" => date('is')
        );

        $return['recordsFiltered'] =  $records->count();
        $return['recordsTotal'] =  $records->count();

        $data = $records;
        $return['data'] = array();
        foreach($data as $d) {
            $attender_user = User::find($d->attender_id);
            $d->attender_name = empty($attender_user) ? '' : $attender_user->first_name.' '.$attender_user->last_name;
            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }

    public function deleteDepartment($id){
        // $splr = Supplier::where('id','=',$id)->withCount(['assets','asset_maintenances','licenses']);
        $record = Department::where('id','=',$id)->withCount(['problem_types','service_types','service_ticket','procure_budgets','procure_requests','tkt_pblm','users','cost_center','privelages'])->first();

        if(empty($record))
            return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);

        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($record) )
            return response()->json(['status' => 'error', 'msg' => 'Insufficient permission !!']);

        if($record->problem_types_count)
            return response()->json(['status' => 'error', 'msg' => 'Some problem type exists for this department.Please remove them and try again !!']);
        if($record->service_types_count)
            return response()->json(['status' => 'error', 'msg' => 'Some service type exists for this department.Please remove them and try again !!']);
        if($record->serviceTicket_count)
            return response()->json(['status' => 'error', 'msg' => 'Some service ticket is attached with this department. Please unlink them and try again !!']);
        if($record->procure_budgets_count)
            return response()->json(['status' => 'error', 'msg' => 'Some Procurement budget is attached with this department. Please unlink them and try again !!']);
        if($record->procure_requests_count)
            return response()->json(['status' => 'error', 'msg' => 'Some Procurement Requests is attached with this department. Please unlink them and try again !!']);
        if($record->tkt_pblm_count)
            return response()->json(['status' => 'error', 'msg' => 'Some Ticket Problem Categories is attached with this department. Please unlink them and try again !!']);
        if($record->users_count)
            return response()->json(['status' => 'error', 'msg' => 'Some Users is attached with this department. Please unlink them and try again !!']);
        if($record->cost_center_count)
            return response()->json(['status' => 'error', 'msg' => 'Some cost center is attached with this department. Please unlink them and try again !!']);
        if($record->privelages_count)
            return response()->json(['status' => 'error', 'msg' => 'Some User Privileges is attached with this department. Please unlink them and try again !!']);
        
            
        $record->delete();
            return response()->json(['status' => 'success', 'msg' => 'Department has been deleted successfully!']);
    }

    /* Get the departments by Company Id */
    public function getDepartmentByCompany(Request $request, $id) {
        $return = ["status"=>"fail","msg"=>"Invalid Access"];
        if(!$id || $id < 1) {
            return response()->json($return);
        }

        $data = Department::where("company_id", $id)->select("id", "name")->get();

        $return["data"] = $data;
        $return["status"] = "success";
        return response()->json($return); 
    }
    
    /* to delete the Delete by ajax call */
    public function ajaxDelete(Request $request, $id) {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to delete the Department'
        ];

        $obj = "";
        try {
            $obj = Department::findOrFail($id);
            $obj->delete();
            $return["status"] = "success";
            $return["msg"] = "Department has been deleted successfully!";
            return response()->json($return);
        }
        catch(\Exception $e) {
            return response()->json($return);        
        }
    }



    /* Get the departments with Company Name with Pagination */
    public function getDepartmentsWithCompanyByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("departments as d");
        $db->join("companies as c", "d.company_id", "=", "c.id")->orderBy('d.name','ASC');
        
        if (count(Auth::user()->departmentAdmin()) > 0) {
            $db->wherein('d.id', Auth::user()->departmentAdmin());
        }
        
        $company = $request->input("company_id", null);
        $companyIds = CommonHelper::getAccessibleCompanyIds();

        if (!empty($company) && $company != 0 && in_array((int) $company, $companyIds, true)) {
            $db->where("d.company_id", "=", (int) $company);
        } elseif (!empty($companyIds)) {
            $db->whereIn("d.company_id", $companyIds);
        }
        
        $db->select("d.id", DB::raw("concat(d.name, ' (', c.name, ')') as text"));
        if($search) {
            $db->whereRaw("concat(d.name, ' (', c.name, ')') like '%" . $search . "%'");
        }

        if(isset($request->type) && $request->type != null && $request->type == "ticket") {
            $db->where("d.module_ticket_enabled", 1);
        }

        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getAssetDepartments(Request $request) {
        $return = ["status"=>"fail", "msg"=>"Unable to fetch asset department"];
        try {
            $search = $request->input("search", "");
            $page = $request->input("page", 1);
            $skip = (($page * 20) - 20);

            $db = DB::table("departments as d");
            $db->join("companies as c", "d.company_id", "=", "c.id");

            if(! Auth::user()->isSuperUser()) {
                $db->where('d.company_id', '=', Auth::user()->company_id);
            }
            if(count(Auth::user()->departmentAdmin()) > 0) {
                $db->wherein('d.id', Auth::user()->departmentAdmin());
            }

            $company = $request->input("company_id", null);
            if($company) {
                $db->where("company_id", "=", $company);
            }
            $db->where("d.asset_department", true);
            $db->select("d.id", DB::raw("concat(d.name, ' (', c.name, ')') as text"));
            if($search) {
                $db->whereRaw("concat(d.name, ' (', c.name, ')') like '%" . $search . "%'");
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();
            $return = ["status"=>"success", "msg"=>"Department fetched successfully."];
            $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
            $return["results"] = count($result) ? $result->toArray() : [];
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("getAssetDepartments : ".$e->getMessage());
            return response()->json($return);
        }
    }

    public function getOrCreate(Request $request) {
        $name = trim($request->name);
        $companyId = 1; 

        $department = Department::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

        if ($department) {
            $updated = false;

            if ($department->module_ticket_enabled != 1) {
                $department->module_ticket_enabled = 1;
                $updated = true;
            }

            if (is_null($department->company_id)) {
                $department->company_id = $companyId;
                $updated = true;
            }

            if ($updated) {
                $department->save();
            }
        } else {
            $department = Department::create([
                'name' => $name,
                'module_ticket_enabled' => 1,
                'company_id' => $companyId
            ]);
        }
        return response()->json([
            'success' => true,
            'department' => $department
        ]);
    }

    public function getDepartmentCustomFields($id) {
        $return = ['status' => 'fail', 'msg' => 'Unable to get department custom fields'];
        try {
            $departmentCustomFieldset = Department::find($id);
            if(!$departmentCustomFieldset) {
                return response()->json($return);
            }

            if($departmentCustomFieldset->department_custom_fieldset != null) {
                $fieldset = explode(",", $departmentCustomFieldset->department_custom_fieldset);
                $fieldsCollection = DB::table('custom_field_custom_fieldset')->whereIn('custom_fieldset_id', $fieldset)->orderBy('order','ASC')->get()->toArray();
                $fieldsArray = [];
                if(!empty($fieldsCollection)) {
                    foreach($fieldsCollection as $value) {
                        $fields = CustomField::where('id', $value->custom_field_id)->first();
                        if($fields->custom_options != null) {
                            $fields->custom_options = (json_decode($fields->custom_options));
                        }
                        $fields->required = isset($value->required) && $value->required == 1 ? 1 : 0;
                        $fields->label = $fields->name;
                        $fields->name = $fields->nameToColumn();
                        $fields->code_name = "fields[".$fields->name."]";
                        array_push($fieldsArray, $fields);
                    }
                }
                $return['data'] = $fieldsArray;
                $return['status'] = 'success';
                $return['msg'] = 'Fields fetched successfully';
                // return response()->json($return);
            }
            if($departmentCustomFieldset->name == "IT Service Request" || $departmentCustomFieldset->name == "IT Service Request Overseas") {
                $return["enableUsbRequest"] = ProblemCategory::enableUsbRequest([$departmentCustomFieldset->id]);
            }
            $return["client_name"] = config("app.client");
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("getDepartmentCustomFields error : ".$e->getMessage());
            return response()->json($return);
        }
    }
}

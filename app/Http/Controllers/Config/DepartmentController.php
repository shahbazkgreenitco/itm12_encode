<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\CustomFieldset;
use App\Models\Device;
use App\Models\OutGoingEmail;
use App\Models\Ticket\AutoCreationAccount;
use App\Models\Ticket\ProblemCategory;
use App\Models\Ticket\TicketProcureRequest;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Location;
use App\Models\Company;
use App\Models\User;
use App\Models\Ticket\Privilege;
use Log;
Use Validator;
use Illuminate\Validation\Rule;
Use Auth;
use DB;
use App\Helpers\Common as CommonHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Departments;
use App\Imports\DepartmentImport;
use App\Models\UserDetails;
class DepartmentController extends Controller {

    public function getIndex() {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('DepartmentRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        // $compObj = Company::select("id", "name as text");
        // if( !Auth::user()->isSuperUser()) {
        //     $compObj->where("id", "=", Auth::user()->company_id);
        // }
        // $companies = $compObj->get()->toArray();
        $company = null;  
        $userDatail = UserDetails::select('dashboard_company_id')->where('user_id', Auth::user()->id)->first();
        $companyId = CommonHelper::getAccessibleCompanyIds();
        if ($userDatail) {
            $company = Company::select('id', 'name as text')->where('id', $userDatail->dashboard_company_id)->get();
        }else {
            $company = Company::select('id', 'name as text')->whereIn('id', $companyId)->get();
        }
        $outGoingEmails = AutoCreationAccount::where('auto_create_from_email', 1)->get();
        $customFieldsetForTicket = CustomFieldset::where('custom_field_set_types', 2)->get();
        $roles = DB::table('roles')->get();
        return view("departments.index")->with(compact("company","outGoingEmails","customFieldsetForTicket", "roles","userDatail"))->with("objdept", new Department());
    }

    public function ajaxDepartment(Request $request) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            'a.id'  => 'id',
            'a.name'  => 'name',
            'a.attender_name'  => 'attender_name',
            'a.company_name'  => 'company_name',
            'a.department_head_name'  => 'department_head_name',
            'a.customefieldset'  => 'customefieldset',
            'a.updated_at'  => 'updated_at',
            'a.department_tag'  => 'department_tag',
        );
        $companyId = CommonHelper::getAccessibleCompanyIds();

        $db = DB::table('departments as a');
        $db->leftJoin('users as u', 'a.attender_id', '=', 'u.id');
        $db->leftJoin('companies as cmp', 'a.company_id', '=', 'cmp.id');
        $db->leftJoin('custom_fieldsets as cus', 'a.department_custom_fieldset', 'cus.id');
        $db->leftJoin('users as uh', 'a.department_head_id', '=', 'uh.id');
        
        $db->select('a.name as name','a.id','a.updated_at','a.department_tag', 'cmp.name as company_name', 'cus.name as customefieldset');
        $db->addSelect(DB::raw('concat(u.first_name, " ", u.last_name) as attender_name'));
        $db->addSelect('u.avatar as attender_avatar', 'u.gravatar as attender_gravatar_raw');
        $db->addSelect(DB::raw('concat(uh.first_name, " ", uh.last_name) as department_head_name'));
        $db->addSelect('uh.avatar as department_head_avatar', 'uh.gravatar as department_head_gravatar_raw');
        // if( !Auth::user()->isSuperUser()) {
        //     $db->where("a.company_id", "=", Auth::user()->company_id);
        // }
        if(isset($request->company_id) && $request->company_id != 0) {
            $db->where(function ($q) use ($request) {
                $q->where('a.company_id', $request->company_id);

                if ($request->company_id == 1) {
                    $q->orWhereNull('a.company_id');
                }
            });
        }else {
            $db->whereIn('a.company_id', $companyId);
        }    
        $db->whereNull('a.deleted_at');

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        
        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
            $whereStr = sprintf('(a.name like "%%%1$s%%" or concat(u.first_name, " ", u.last_name) like "%%%1$s%%" or cus.name like "%%%1$s%%" or concat(uh.first_name, " ", uh.last_name) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ['asc', 'desc'])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
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
            if (!empty($d->attender_avatar)) {
                $tmpUser = new User();
                $tmpUser->avatar = $d->attender_avatar;
                $d->attender_profile_img = $tmpUser->getProfileImg(true);
            } else {
                $d->attender_profile_img = null;
            }
            $d->attender_gravatar = $d->attender_gravatar_raw ?: null;
            unset($d->attender_avatar, $d->attender_gravatar_raw);

            if (!empty($d->department_head_avatar)) {
                $tmpHead = new User();
                $tmpHead->avatar = $d->department_head_avatar;
                $d->department_head_profile_img = $tmpHead->getProfileImg(true);
            } else {
                $d->department_head_profile_img = null;
            }
            $d->department_head_gravatar = $d->department_head_gravatar_raw ?: null;
            unset($d->department_head_avatar, $d->department_head_gravatar_raw);

            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    public function ajaxAddDepartment(Request $request) {
        $return = [
            'status' => 'failure',
            'msg' => trans('department.alerts_and_messages.unable_to_add_department')
        ];
        if(!Auth::user()->hasPermissionTo('DepartmentAdd')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only('name','department_tag', 'company_id', 'attender_id', 'description','department_head_id');
        $rules = [
            'name' => [
                'required', 'max:100', 'clean_text_only',
                Rule::unique('departments')->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id)
                                ->whereNull('deleted_at');
                }),
            ],
            'department_tag'=> 'sometimes|nullable|string',
            'company_id' => 'required|exists:companies,id',
            'attender_id'=> 'sometimes|nullable|exists:users,id',
            'department_custom_fieldset'=> 'sometimes|nullable|exists:custom_fieldsets,id',
            'description'  => 'nullable|string|max:2000|clean_text_only',  
            'department_head_id' => 'sometimes|nullable|exists:users,id',
            // 'sc_custom_fieldset' => 'required',
            // 'pc_custom_fieldset' => 'required'
        ];
        $messages = [
            'name.required' => trans('department.alerts_and_messages.dept_required'),
            'company_id.required' => trans('department.alerts_and_messages.company_required'),
        ];
        $validator = Validator::make($data, $rules, $messages);
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        try {
            if($request->isDefaultAssetDpartment == true && ($request->asset_department == null || $request->asset_department == 0)) {
                $return["msg"] = trans('department.alerts_and_messages.default_ast_dept');
                return response()->json($return);
            }

            $objdept = new Department;
            $objdept->name = $data["name"];
            $objdept->department_tag = isset($data["department_tag"])?$data["department_tag"]:'';
            $objdept->company_id = $data["company_id"];
            $objdept->attender_id = $data["attender_id"] != "null"? $data["attender_id"]: 0;
            $objdept->description = $data["description"];
            $objdept->department_head_id = $data["department_head_id"] != "null" ? $data["department_head_id"]: 0;

            if(isset($request->asset_department) && $request->asset_department != "null") {
                $objdept->asset_department = $request->asset_department;
            }
            if(isset($request->asset_department_admin) && $request->asset_department_admin != "null") {
                $objdept->asset_department_admin = $request->asset_department_admin;
            }
            if(isset($request->department_custom_fieldset)) {
                $objdept->department_custom_fieldset = $request->department_custom_fieldset;
            }
            /*if($request->isDefaultAssetDpartment == true) {
                $objdept->is_default_asset_department = true;
            } else {
                $objdept->is_default_asset_department = false;
            }*/

            if($objdept->attender_id) {
                $user = User::find($objdept->attender_id);

                if($user->checkoutBasicClearance()){
                    $return["msg"] = trans('department.alerts_and_messages.attender_not_active');
                    return response()->json($return);
                }
                if($user->checkLastWorkingDate()){
                    $return["msg"] = trans('department.alerts_and_messages.attender_lwd_completed');
                    return response()->json($return);
                }

            }
            // for ltts admin who can create a ticket on department
            if((config('app.client') == "ltts" && config('app.sub_client') == "admin") && isset($request->access_role_id) && $request->access_role_id != 'undefined'){
                $objdept->access_role_id = $request->access_role_id != 'null' ? $request->access_role_id : NULL;
            }
            if($objdept->save()) {
                //update other department as not default
                // $department = Department::where('id', "!=", $objdept->id)->update(['is_default_asset_department' => false]);

                //update asset department
                // Device::whereNull('department_id')->update(['department_id' => $objdept->id]);

                $return["msg"] = trans('department.alerts_and_messages.dept_added_successfully');
                $return["status"] = "success";

            }
        }
        catch(\Exception $e) {
            Log::error('DepartmentController->ajaxAddDepartment',[$e->getMessage()]);
            return response()->json($return);   
        }

        return response()->json($return);
    }

    public function ajaxGetDepartment(Request $request, $id) {
        $return = [
            'status' => 'failure',
            'msg' => trans('department.alerts_and_messages.unable_to_get_department')
        ];

        $objdept = "";
        try {
            $objdept = Department::find($id);

            if (!$objdept) {
                return response()->json([
                    'status' => 'failure',
                    'msg'    => trans('department.alerts_and_messages.department_not_found'),
                ], 404);
            }

            $data = [];
            $data['dropdown'] = array();
            $data['data'] = $objdept->only('id','name','department_tag','company_id', 'module_ticket_enabled', 'tkt_auto_creation_id', 'description', 'department_admin','asset_department','asset_department_admin','department_custom_fieldset','department_head_id', 'pc_id', 'sc_id','access_role_id');
            if($objdept->attender_id) {
                $getUser = User::where("id", $objdept->attender_id)->select("id",  DB::raw('concat(first_name, " ", last_name, "") as text', "description"))->first();
                $data["dropdown"]["attender_id"] = $getUser && $getUser->exists ? $getUser->toArray() : 0;
            }

            $problemCategories = ProblemCategory::where('department_id', $id)->select('id', DB::raw('name as text'))->whereNull('parent_id')->get()->toArray();
            if (!empty($problemCategories)) {
                if ($objdept->pc_id != null) {
                    $selectedPC = array_filter($problemCategories, function($pc) use ($objdept) {
                        return $pc['id'] == $objdept->pc_id;
                    });
                    $mergedPCs = array_merge($selectedPC, $problemCategories);
                    $uniquePCs = [];
                    foreach ($mergedPCs as $pc) {
                        $uniquePCs[$pc['id']] = $pc;
                    }
                    $data['dropdown']['pc'] = array_values($uniquePCs); 
                } else {
                    $data['dropdown']['pc'] = $problemCategories;
                }
            }
    
            if ($objdept->sc_id != null) {
                $scCategory = ProblemCategory::where('id', $objdept->sc_id)
                    ->select('id', DB::raw('name as text'))
                    ->first();
            
                $data['dropdown']['sc'] = $scCategory ? [$scCategory->toArray()] : [];
            }
            if($objdept->department_admin != null) {
                $array = explode(",", $objdept->department_admin);
                foreach($array as $a) {
                    $getUser = User::where("id", $a)->select("id",  DB::raw('concat(first_name, " ", last_name, "") as text', "description"))->first();
                    $data["dropdown"]["department_admin"][] = $getUser && $getUser->exists ? $getUser->toArray() : 0;
                }
            }
            if($objdept->asset_department_admin != null) {
                $array = explode(",", $objdept->asset_department_admin);
                foreach($array as $a) {
                    $getUser = User::where("id", $a)->select("id",  DB::raw('concat(first_name, " ", last_name, "") as text', "description"))->first();
                    $data["dropdown"]["asset_department_admin"][] = $getUser && $getUser->exists ? $getUser->toArray() : 0;
                }
            }
            if($objdept->department_custom_fieldset != null) {
                $array = explode(",", $objdept->department_custom_fieldset);
                foreach($array as $a) {
                    $getCustomField = CustomFieldset::where("id", $a)->select("id",  DB::raw('name as text'))->first();
                    $data["dropdown"]["department_custom_fieldset"][] = $getCustomField && $getCustomField->exists ? $getCustomField->toArray() : 0;
                }
            }
            if($objdept->department_head_id) {
                $getUser = User::where("id", $objdept->department_head_id)->select("id",  DB::raw('concat(first_name, " ", last_name, "") as text'))->first();
                $data["dropdown"]["department_head_id"] = $getUser && $getUser->exists ? $getUser->toArray() : 0;
            }
            if($objdept->company_id != null) {
                $company = Company::select('id', 'name as text')->where('id', $objdept->company_id)->first();
                $data["dropdown"]["company"] = $company && $company->exists() ? $company->toArray() : 0;
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
        $return = [
            'status' => 'failure',
            'msg' => trans('department.alerts_and_messages.unable_to_edit_depreciation')
        ];
        if(! Auth::user()->hasPermissionTo('DepartmentEdit')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
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

        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($objdept) ) {
            return response()->json(['status' => 'error', 'msg' => trans('department.alerts_and_messages.insufficent_permission')]);
        }
        if(($request->asset_department == 0) && ($objdept->asset_department != $request->asset_department)){
            $assetDeptCount = Device::where('department_id',$id)->count();
            if(!empty($assetDeptCount)){
                return response()->json(['status' => 'error', 'msg' => trans('department.alerts_and_messages.dept_has_assets')]);
            }
        }
        $data = $request->only('name','department_tag','company_id','attender_id','tkt_auto_creation_id', 'description','department_admin','department_custom_fieldset','department_head_id', 'pc_id', 'sc_id');
        $rules = [
            'name' => [
                'required', 'clean_text_only',
                Rule::unique('departments', 'name')
                ->where(function ($query) use ($request) {
                    $query->where('company_id', $request->company_id);
                })->whereNull('deleted_at')->ignore($objdept->id),
            ],
            'company_id' => 'required|exists:companies,id',
            'attender_id'=> 'sometimes|nullable|exists:users,id',
            'department_tag'=> 'sometimes|nullable|string',
            'description'  => ['nullable', 'string', 'max:2000', 'clean_text_only'],  
        ];
        $messages = [
            'name.required' => trans('department.alerts_and_messages.dept_required'),
            'company_id.required' => trans('department.alerts_and_messages.company_required'),
        ];
        $validator = Validator::make($data, $rules,$messages);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        if($request->isDefaultAssetDpartment == true && ($request->asset_department == null || $request->asset_department == 0)) {
            $return["msg"] = trans('department.alerts_and_messages.default_ast_dept');
            return response()->json($return);
        }

        $objdept->name = trim($data["name"]);
        $objdept->department_tag = isset($data["department_tag"])?$data["department_tag"]:'';
        $objdept->company_id = $data["company_id"];
        $objdept->attender_id = $data["attender_id"] != "null" ? $data["attender_id"] : 0;
        $objdept->tkt_auto_creation_id = $data["tkt_auto_creation_id"] != "null" ? $data["tkt_auto_creation_id"] : 0;
        $objdept->description = $data["description"];
        $objdept->department_admin = null;
        $objdept->department_head_id = $data["department_head_id"] != "null" ? $data["department_head_id"] : 0;
        $objdept->pc_id = $data["pc_id"] != "null" ? $data["pc_id"] : null;
        $objdept->sc_id = $data["sc_id"] != "null" ? $data["sc_id"] : null;

        if(isset($request->department_admin) && $request->department_admin != "null") {
            $objdept->department_admin = $data["department_admin"];
        }
        if(isset($request->asset_department) && $request->asset_department != "null") {
            $objdept->asset_department = $request->asset_department;
        }
        if(isset($request->asset_department_admin) && $request->asset_department_admin != "null") {
            $objdept->asset_department_admin = $request->asset_department_admin;
        } else {
            $objdept->asset_department_admin = NULL;
        }
        if(isset($request->department_custom_fieldset) && $request->department_custom_fieldset != "null") {
            $objdept->department_custom_fieldset = $request->department_custom_fieldset;
        } else {
            $objdept->department_custom_fieldset = NULL ;
        }

        /*if($request->isDefaultAssetDpartment == true) {
            $objdept->is_default_asset_department = true;
            //update other department as not default
            $department = Department::where('id', "!=", $objdept->id)->update(['is_default_asset_department' => false]);
        } else {
            $objdept->is_default_asset_department = false;
        }*/

        // for ltts admin who can create ticket on department
        if((config('app.client') == "ltts" && config('app.sub_client') == "admin") && isset($request->access_role_id) && $request->access_role_id != 'undefined') {
            $objdept->access_role_id = $request->access_role_id != 'null' ? $request->access_role_id : NULL;
        }
        if($objdept->attender_id){
            $user = User::find($objdept->attender_id);

            if($user->checkoutBasicClearance()){
                $return["msg"] = trans('department.alerts_and_messages.attender_not_active');
                return response()->json($return);
            }
            if($user->checkLastWorkingDate()){
                $return["msg"] = trans('department.alerts_and_messages.attender_lwd_completed');
                return response()->json($return);
            }

        }

        if ($objdept->save()) {

            $return["msg"] = trans('department.alerts_and_messages.dept_updated_successfully');
            $return["status"] = "success";
        }

        return response()->json($return);
    }

    public function getDepartmentsByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);
        $take = 10;

        if( isset($request->start) && isset($request->length) ) {
            $skip = (int) $request->start;
            $take = (int) $request->length;
        }

        $db = Department::select("id", "name as text");
        if($search) {
            if(is_array($search)) {
                $db->where("name", "like", "%" . $search['value'] . "%");
            } else {
                $db->where("name", "like", "%" . $search . "%");
            }
        }
        $count = $db->count();
        if( isset($request->start) && isset($request->length) ) {
            $db->skip($skip)->take($take);
        } else {
            $db->skip($skip)->take(20);
        }
        $return['recordsFiltered'] = $db->count();
        $return['recordsTotal'] = $db->count();
        $result = $db->get();
        $return["draw"] = date('is');

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["data"] = count($result) ? $result->toArray() : [];
        $return["results"] = count($result) ? $result->toArray() : [];
        $return['data'] = array();
        foreach($result as $d) {
            $return['data'][] = array('a' => $d);
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
            'name.required' => trans('department.alerts_and_messages.dept_required'),
        ];
        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
            return response(array('status'=>'error','errors' => $validator->messages()));
        }else{
            if( !Auth::user()->isSuperUser() && Auth::user()->company_id != $request->company_id )
                return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.no_permission')]);

            $objdept->name = $request->name;
            $objdept->company_id = $request->company_id;
            $objdept->attender_id = $request->attender_id;
            $objdept->save();
            return response(array('status'=>'success','msg'=>trans('department.alerts_and_messages.dept_added_successfully')));
        }
    }

    public function detailDepartment($id, Request $request) {
        $record = Department::with('attenders')->find($id);
        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($record) )
            return response()->json(['status' => 'error', 'msg' => trans('department.alerts_and_messages.insufficent_permission')]);
        return $record;
    }

    public function editDepartment($id, Request $request) {
        $objDpt = Department::find($id);

        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($objDpt) )
            return response()->json(['status' => 'error', 'msg' => trans('department.alerts_and_messages.insufficent_permission')]);
        $rules = [
            'name' => [
                'required',
                Rule::unique('departments')->ignore($objDpt->id)
            ],
            'company_id' => 'required|exists:companies,id',
            'attender_id'=> 'sometimes|nullable|exists:users,id'
        ];
        $messages = [
            'name.required' =>  trans('department.alerts_and_messages.dept_required'),
            'company_id.required' =>  trans('department.alerts_and_messages.company_required'),
        ];
        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
            return response(array('status'=>'error','errors' => $validator->messages()));
        }else{
            $objDpt->name = $request->name;
            $objDpt->company_id = $request->company_id;
            $objDpt->attender_id = $request->attender_id;
            if(isset($request->department_admin) && !empty($request->department_admin)) {
                $objDpt->department_admin = $request->department_admin;
            }
            $objDpt->save();
            return response(array('status'=>'success','msg'=> trans('department.alerts_and_messages.dept_updated_successfully')));
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
            return response()->json(['status' => 'error', 'msg' => trans('department.alerts_and_messages.insufficent_permission')]);

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
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('DepartmentDelete')) {
            return response()->json($return);
        }
        // $splr = Supplier::where('id','=',$id)->withCount(['assets','asset_maintenances','licenses']);
        $record = Department::where('id','=',$id)->withCount(['problem_types','service_types','service_ticket','procure_budgets','procure_requests','tkt_pblm','users','cost_center','privelages'])->first();

        if(empty($record))
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.department_not_found')]);

        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($record) )
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.no_permission')]);

        if($record->problem_types_count)
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.problem_type_exists')]);
        if($record->service_types_count)
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.service_type_exists')]);
        if($record->service_ticket_count)
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.service_ticket_attached')]);
        if($record->procure_budgets_count)
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.procurement_budget_attached')]);
        if($record->procure_requests_count)
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.procurement_request_attached')]);
        if($record->tkt_pblm_count)
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.ticket_problem_category_attached')]);
        if($record->users_count)
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.users_attached')]);
        if($record->cost_center_count)
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.cost_center_attached')]);
        if($record->privelages_count)
            return response()->json(['status' => 'error', 'msg' =>  trans('department.alerts_and_messages.user_privileges_attached')]);
        
            
        $record->delete();
            return response()->json(['status' => 'success', 'msg' =>  trans('department.alerts_and_messages.dept_deleted_successfully')]);
    }

    /* Get the departments by Company Id */
    public function getDepartmentByCompany(Request $request, $id) {
        $return = ["status"=>"fail","msg"=> trans('department.alerts_and_messages.invalid_access')];
        if(!$id || $id < 1) {
            return response()->json($return);
        }

        $data = Department::leftjoin('companies as c', 'c.id', '=', 'departments.company_id')
        ->where("departments.company_id", $id)
        ->select("departments.id", DB::raw("concat(departments.name, ' (', c.name, ')') as name"));
        $data->where('departments.name', 'not like', "To be assigned");
        // $acfe = AutoCreationAccount::where('auto_create_from_email', 1)->first();


        $return["data"] = $data->get();
        $return["status"] = "success";
        return response()->json($return); 
    }

    /* Get the departments by privilage */
    public function getUsersEnabledDepartment(Request $request, $id) {    
        $return = ["status"=>"fail","msg"=> trans('department.alerts_and_messages.invalid_access')];
        $query = $request->get('q');
        $get_user_privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
        $arr = $get_user_privileges->toArray();
        if (empty($id) || $id == 0) {
            $companyIds = CommonHelper::getAccessibleCompanyIds();
        } else {
            $companyIds = [(int) $id];
        }
        $data = Department::leftjoin('companies as c', 'c.id', '=', 'departments.company_id')
            ->select("departments.id", DB::raw("concat(departments.name, ' (', c.name, ')') as name"))
            ->whereIn("departments.company_id", $companyIds)
            ->orderBy("departments.name");
        if (count($arr) > 0) {
            $data->whereIn('departments.id', $arr);
        } else {
            $data->where('module_ticket_enabled', 1);
        }
        if(isset($query)){
             $data->where("departments.name", "like", "%" . $query . "%");
        }
        $data = $data->get();
        
        $return["data"] = $data;
        $return["status"] = "success";
        $return["msg"] =  trans('department.alerts_and_messages.success');
        return response()->json($return); 
    }

  public function getAutoAllocationDepartments(Request $request, $id){
        $return = ["status" => "fail", "msg" => trans('department.alerts_and_messages.invalid_access')];
        $query = $request->get('q');
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        if (!in_array((int)$id, $companyIds)) {
            return response()->json($return);
        }
        $data = Department::select('id', 'name')
            ->where('company_id', (int)$id)
            ->orderBy('name');
        if (!empty($query)) {
            $data->where('name', 'like', '%' . $query . '%');
        }
        $data = $data->get();
        return response()->json([
            "status" => "success",
            "msg" =>  trans('department.alerts_and_messages.success'),
            "data" => $data
        ]);
    }

    /* Get the departments by privilage for App */
    public function getUsersEnabledDepartmentApp(Request $request, $id) {
        $return = ["status"=>"fail","msg"=> trans('department.alerts_and_messages.invalid_access')];

        $get_user_privileges = CommonHelper::getPrevilegedDepartmentIdsByCompanyAccess();
        $arr = $get_user_privileges->toArray();
        if (empty($id) || $id == 0) {
            $companyIds = CommonHelper::getAccessibleCompanyIds();
        } else {
            $companyIds = [(int) $id];
        }
        $data = Department::select('id', 'name as text')->whereIn('company_id', $companyIds)->orderBy('name');
        if (count($arr) > 0) {
            $data->whereIn('id', $arr);
        } else {
            $data->where('module_ticket_enabled', 1);
        }
        $data = $data->get();
        
        $return["results"] = $data;
        $return["status"] = "success";
        $return["msg"] = trans('department.alerts_and_messages.success');
        return response()->json($return); 
    }
    
    /* to delete the Delete by ajax call */
    public function ajaxDelete(Request $request, $id) {
        $return = [
            'status' => 'failure',
            'msg' =>  trans('department.alerts_and_messages.unable_to_delete_department')
        ];
        if(! Auth::user()->hasPermissionTo('DepartmentDelete')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $obj = "";
        try {
            $obj = Department::findOrFail($id);
            $obj->delete();
            $return["status"] = "success";
            $return["msg"] =  trans('department.alerts_and_messages.dept_deleted_successfully');
            return response()->json($return);
        }
        catch(\Exception $e) {
            return response()->json($return);        
        }
    }

    /* Get the departments with Company Name with Pagination */
     public function getDepartmentsWithCompanyByQuery(Request $request) {
        $return = [];
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("departments as d")->join("companies as c", "d.company_id", "=", "c.id")->orderBy('d.name', 'ASC');
        
        // if (!Auth::user()->isSuperUser()) {
        //     $db->where('d.company_id', Auth::user()->company_id);
        // }

        if (count(Auth::user()->departmentAdmin()) > 0) {
            $db->whereIn('d.id', Auth::user()->departmentAdmin());
        }
        
        $company = $request->input("company_id", null);
        $companyIds = CommonHelper::getAccessibleCompanyIds();

        if (!empty($company)) {
            if (is_array($company)) {
            $db->whereIn('d.company_id', $company);
            } else {
                $db->where('d.company_id', $company);
            }
        } else {
            $db->whereIn('d.company_id', $companyIds);
        }
        
        $db->select("d.id", DB::raw("concat(d.name, ' (', c.name, ')') as text"));

        if ($search) {
            $db->whereRaw(
                "concat(d.name, ' (', c.name, ')') like ?",
                ["%{$search}%"]
            );
        }

        if ($request->type === "ticket") {
            $db->where("d.module_ticket_enabled", 1);
        }

        $db->whereNull("d.deleted_at");

        $count = (clone $db)->count();

        $result = $db->skip($skip)->take(20)->get();

        $return["pagination"] = [
            "more" => ($count - ($page * 20)) > 0
        ];

        $return["results"] = $result->toArray();

        return response()->json($return);
    }

    public function departmentExport(Request $request){
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('DepartmentDownload')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $dashboardCompanyId = UserDetails::where('user_id', Auth::id())->value('dashboard_company_id');
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        $query = Department::select(
                'departments.id',
                'departments.name as name',
                'departments.department_tag as department_tag',
                'cmp.name as company_name'
            )
            ->addSelect(DB::raw('concat(u.first_name, " ", u.last_name) as attender_name'))
            ->addSelect(DB::raw('concat(uh.first_name, " ", uh.last_name) as department_head_name'))
            ->addSelect('cus.name as custom_fieldset')
            ->addSelect('departments.description as description')
            ->addSelect('departments.module_ticket_enabled as module_ticket_enabled')
            ->addSelect('departments.asset_department as asset_department')
            ->addSelect(DB::raw('concat(u1.first_name, " ", u1.last_name) as department_admin_name'))
            ->addSelect(DB::raw('DATE_FORMAT(departments.updated_at, "%d %b %Y %h:%i %p") as formatted_created_at'))
            ->leftJoin('users as u', 'departments.attender_id', '=', 'u.id')
            ->leftJoin('companies as cmp', 'departments.company_id', '=', 'cmp.id')
            ->leftJoin('custom_fieldsets as cus', 'departments.department_custom_fieldset','=','cus.id')
            ->leftJoin('users as uh', 'departments.department_head_id', '=', 'uh.id')
            ->leftJoin('users as u1', 'departments.department_admin', '=', 'u1.id');

        if(isset($request->company_id) && $request->company_id != 0) {
            $query->where(function ($q) use ($request) {
                $q->where('departments.company_id', $request->company_id);
                if ($request->company_id == 1) {
                    $q->orWhereNull('departments.company_id');
                }
            });
        } elseif (!empty($dashboardCompanyId) && $dashboardCompanyId != 0) {
            $query->where(function ($q) use ($dashboardCompanyId) {
                $q->where('departments.company_id', $dashboardCompanyId);

                if ($dashboardCompanyId == 1) {
                    $q->orWhereNull('departments.company_id');
                }
            });
        } else {
            $query->whereIn('departments.company_id', $companyIds);
        }  
        $query->whereNull('departments.deleted_at');
        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if(isset($filters->search)) {
                $req["search"] = $filters->search;
            }

            if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                $whereStr = sprintf(
                    '(departments.name like "%%%1$s%%" or concat(u.first_name, " ", u.last_name) like "%%%1$s%%" or cus.name like "%%%1$s%%" or concat(uh.first_name, " ", uh.last_name) like "%%%1$s%%")',
                    $search_key
                );
                $query->whereRaw($whereStr);
            }
        }

        $return['recordsTotal'] = (clone $query)->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        $results = $query->get()->map(function ($row) {
            $row->module_ticket_enabled = $row->module_ticket_enabled
                ? trans('department.import_fields_and_buttons.enabled')
                : trans('department.import_fields_and_buttons.disabled');
            $row->asset_department = $row->asset_department
                ? trans('department.import_fields_and_buttons.yes')
                : trans('department.import_fields_and_buttons.no');
            return $row;
        });

        $keys = [
            trans('department.table_fields.id'),
            trans('department.table_fields.department_name'),
            trans('department.table_fields.department_tag'),
            trans('department.table_fields.company_name'),
            trans('department.table_fields.attender_name'),
            trans('department.table_fields.department_head'),
            trans('department.table_fields.fieldset'),
            trans('department.import_fields_and_buttons.description'),
            trans('department.import_fields_and_buttons.module_ticket_enabled'),
            trans('department.import_fields_and_buttons.asset_department'),
            trans('department.form_fields_and_buttons.department_admin'),
            trans('department.table_fields.updated_at'),
        ];
        return Excel::download(new Departments($results->toArray(), $keys), trans('department.filename'));
    }

    public function getDepartmentOfServiceRequest(Request $request) {
        $return = ["status"=>"fail","msg"=> trans('department.alerts_and_messages.invalid_access')];

        $dashboardCompanyId = CommonHelper::getSelectedCompanyIds();
        $companyIds = $dashboardCompanyId;
        $procureDepartments = TicketProcureRequest::whereIn('company_id', $companyIds)
            ->distinct()
            ->pluck('department_id')
            ->toArray();
        
        $data = Department::join('companies', 'companies.id', '=', 'departments.company_id')
        ->whereIn('departments.id', $procureDepartments)
        ->where('departments.module_ticket_enabled', 1)
        ->whereIn('departments.company_id', $companyIds)
        ->select(
            'departments.id',
            DB::raw("CONCAT(departments.name, ' (', companies.name, ')') as name")
        )
        ->get();
        
        $return["data"] = $data;
        $return["status"] = "success";
        $return["msg"] = "success";
        return response()->json($return);
    }

    public function getAssetDepartments(Request $request) {
        $return = ["status"=>"fail", "msg"=> trans('department.alerts_and_messages.unable_to_fetch_asset_department')];
        try {
            $search = $request->input("search", "");
            $page = $request->input("page", 1);
            $skip = (($page * 20) - 20);

            $db = DB::table("departments as d")->whereNull('d.deleted_at');
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
            $return = ["status"=>"success", "msg"=> trans('department.alerts_and_messages.department_fetched_successfully')];
            $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
            $return["results"] = count($result) ? $result->toArray() : [];
            return response()->json($return);
        } catch(\Exception $e){
            Log::error("getAssetDepartments : ".$e->getMessage());
            return response()->json($return);
        }
    }

    public function getDepartmentCustomFields(Request $request,$id) {
        $return = ['status' => 'fail', 'msg' =>  trans('department.alerts_and_messages.unable_to_get_department_custom_fields')];
        try {
            $departmentCustomFieldset = Department::find($id);
            if(!$departmentCustomFieldset) {
                return response()->json($return);
            }

            $pc_id = $departmentCustomFieldset->pc_id; 
            $sc_id = $departmentCustomFieldset->sc_id;

            $fieldset = [];

            if($departmentCustomFieldset->department_custom_fieldset != null) {
                $fieldset = explode(",", $departmentCustomFieldset->department_custom_fieldset);
            }
            $customFieldset = [];
            if ($request->filled('sc_id')) {
                $problemCategory = ProblemCategory::find($request->sc_id);
                if (
                    $problemCategory &&
                    !empty($problemCategory->custom_fieldset)
                ) {
                    $customFieldset = explode(",", $problemCategory->custom_fieldset);
                }
            }

            if (empty($customFieldset) && $request->filled('pc_id')) {
                $problemCategory = ProblemCategory::find($request->pc_id);
                if (
                    $problemCategory &&
                    !empty($problemCategory->custom_fieldset)
                ) {
                    $customFieldset = explode(",", $problemCategory->custom_fieldset);
                }
            }
            $fieldsArray = [];

            if(!empty($fieldset)) {
                $fieldsCollection = DB::table('custom_field_custom_fieldset as cfcf')
                                        ->join('custom_fieldsets as cfs', 'cfs.id', '=', 'cfcf.custom_fieldset_id')
                                        ->whereIn('cfcf.custom_fieldset_id', $fieldset)
                                        ->whereNull('cfs.fieldset_for_ticket_type')
                                        ->whereNull('cfs.fieldset_for_status')
                                        ->orderBy('cfcf.order', 'ASC')
                                        ->get();  
                if(!empty($fieldsCollection)) {
                    foreach($fieldsCollection as $value) {
                        $fields = CustomField::where('id', $value->custom_field_id)->first();
                        if(!$fields) {
                            continue;
                        }
                        if($fields->custom_options != null) {
                            $fields->custom_options = json_decode($fields->custom_options);
                        }
                        if ($request->has('sc_id') && $sc_id == $request->sc_id && $sc_id != null && $value->required !== 1) {
                            $fields->required = 1;
                        }elseif ($sc_id != $request->sc_id && $sc_id != null && $value->required !== 1) {
                            $fields->required = 0;
                        }elseif ($pc_id == $request->pc_id && $pc_id != null && $value->required !== 1) {
                            $fields->required = 1;
                        } else {
                            $fields->required = isset($value->required) && $value->required == 1 ? 1 : 0;
                        }
                        $fields->label = $fields->name;
                        $fields->name = $fields->nameToColumn();
                        array_push($fieldsArray, $fields);
                    }
                }
            }
            $problemCategoryFields = [];
            if(!empty($customFieldset)) {
              $customFieldsCollection = DB::table('custom_field_custom_fieldset as cfcf')
                        ->leftJoin('custom_fieldsets as cfs', 'cfs.id', '=', 'cfcf.custom_fieldset_id')
                        ->whereIn('cfcf.custom_fieldset_id', $customFieldset)
                        ->whereNull('cfs.fieldset_for_ticket_type')
                        ->whereNull('cfs.fieldset_for_status')
                        ->orderBy('cfcf.order', 'ASC')
                        ->get();
                if(!empty($customFieldsCollection)) {
                    foreach($customFieldsCollection as $value) {
                        $fields = CustomField::where('id', $value->custom_field_id)->first();
                        if(!$fields) {
                            continue;
                        }
                        if($fields->custom_options != null) {
                            $fields->custom_options = json_decode($fields->custom_options);
                        }
                        $fields->required = isset($value->required) && $value->required == 1 ? 1 : 0;

                        $fields->label = $fields->name;
                        $fields->name = $fields->nameToColumn();

                        array_push($problemCategoryFields, $fields);
                    }
                }
            }

            $return['data'] = $fieldsArray;
            $return['problem_category_fields'] = $problemCategoryFields;
            $return['status'] = 'success';
            $return['msg'] =  trans('department.alerts_and_messages.fields_fetched_successfully');

            $enableUsbRequest_old = [];
            if($departmentCustomFieldset->name == "IT Service Request" || $departmentCustomFieldset->name == "IT Service Request Overseas") {
                $enableUsbRequest_old = ProblemCategory::enableUsbRequest([$departmentCustomFieldset->id]);
            }
            $enableUsbRequests_new = ProblemCategory::where('privilege_access',1)->pluck('id')->toArray();
            $enableUsbRequests = array_merge($enableUsbRequest_old,$enableUsbRequests_new);
            $return["enableUsbRequests"] = array_values(array_unique($enableUsbRequests));
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("getDepartmentCustomFields error : ".$e->getMessage());
            return response()->json($return);
        }
    }

    public function departmentImport(Request $request) {

        $return = ["msg"=> trans('department.alerts_and_messages.unable_to_import_user_list'), "status"=>"danger"];

        if (!Auth::user()->hasPermissionTo('DepartmentImport')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        if (!$request->isMethod('post')) {
                return view("departments.import");
            }
        if (!$request->hasFile('import_file')) {
            return redirect()->back()->with([
                "success" => 0,
                "fail" => 0,
                "fail_msgs" => [trans('content.download_format.upload_the_file_department')]
            ]);
        }
        try {
            $import = new DepartmentImport($request);
            Excel::import($import, $request->file('import_file'));
            $response = $import->data;
            if (!isset($response['success'])) {
                return redirect()->back()->with([
                    "status" => $response['status'] ?? "danger",
                    "msg"    => $response['msg'] ?? "An unexpected error occurred during import.",
                    "success" => 0,
                    "fail" => 1,
                    "fail_msgs" => [$response['msg'] ?? "An unexpected error occurred during import."],
                ]);
            }

            return redirect()->back()->with([
                'success'                  => $response['success'],
                'fail'                     => $response['fail'],
                'fail_msgs'                => $response['fail_msgs'],
                'given_file_original_name' => $import->request->file('import_file')->getClientOriginalName(),
                'doc_link' => $response['doc_link'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('DepartmentImport controller error: ' . $e->getMessage());
            return redirect()->back()->with([
                'success' => 0,
                'fail' => 1,
                'fail_msgs' => [$e->getMessage()],
            ]);
        }
    }
    
    public function getSubCategories(Request $request){

        if ($request->has('parent_id')) {
            $subCategories = ProblemCategory::where('parent_id', $request->parent_id) 
                ->select('id', DB::raw('name as text'))
                ->get();

            $data['dropdown']['sc'] = $subCategories->toArray();
        } else {
            $data['dropdown']['sc'] = []; 
        }

        return response()->json($data);
    }

}
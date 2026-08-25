<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomTax\TaxDefination;
use App\Models\CustomTax\TaxElements;
use DB;
use Auth;
use stdClass;
use Validator;
use Log;

class CustomTaxController extends Controller
{
    public function getIndex(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('custom_tax.alerts_and_messages.permission_denied')];
        if(!Auth::user()->hasPermissionTo('CustomTaxRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $currentUser = Auth::user();
        $procurementRole = $currentUser->procurementRole != null ? $currentUser->procurementRole->getRole() : null;
        if(Auth::user()->isSuperUser() != true && ($procurementRole == null || $procurementRole == false)) {
            $return = [
                "msg" => trans('custom_tax.alerts_and_messages.insufficient_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        return view("custom_tax.index");
    }
    
    public function ajaxCustomTax(Request $request) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            '1' => 'tax_name',
            '2' => 'created_at',
            '3' => 'updated_at'
        );

        $customTaxObj = DB::table('tax_defination')->whereNull('deleted_at');
        $return['recordsTotal'] = $customTaxObj->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $customTaxObj->where(function ($q) use ($search_key) {
                $q->where('tax_name', 'like', '%' . $search_key . '%')
                  ->orWhereRaw('DATE_FORMAT(updated_at, "%d %b %Y %h:%i %p") LIKE ?', ["%{$search_key}%"])
                  ->orWhereRaw('DATE_FORMAT(created_at, "%d %b %Y %h:%i %p") LIKE ?', ["%{$search_key}%"])
                  ->orWhere('updated_at', 'like', "%{$search_key}%") 
                  ->orWhere('created_at', 'like', "%{$search_key}%"); 
            });
            $return['recordsFiltered'] = $customTaxObj->count();
        }
        if( isset($req["order"][0]["column"]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
            if(isset($fields[$req["order"][0]["column"]])) {
                $customTaxObj->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
            }
        }

        $skip = 0;
        $take = 10;
        if( isset($req["start"]) && isset($req["length"]) ) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }
        $customTaxObj->skip($skip);
        $customTaxObj->take($take);

        $data = $customTaxObj->get();
        $return['data'] = $data;

        return response()->json($return);
    }
    public function saveCustomTax(Request $request)
    {
        if( !Auth::user()->hasPermissionTo('CustomTaxAdd') ) {
            $return["msg"] = trans('custom_tax.alerts_and_messages.permission_denied');
            return response()->json($return);
        }
        $return = ["status" => "fail", "title" => trans('actions.result.fail'), "msg" => trans('actions.err.contact_save_failed')];
        $currentUser = Auth::user();
        $procurementRole = $currentUser->procurementRole != null ? $currentUser->procurementRole->getRole() : null;
        if(Auth::user()->isSuperUser() != true && ($procurementRole == null || $procurementRole == false)) {
            $return = [
                "msg" => trans('custom_tax.alerts_and_messages.insufficient_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        $data = $request->only("id", "name", "customOptions");
        $rules = [
            'id' => 'required|integer|min:1',
            'name' => 'required|string|min:2|max:100|clean_text_only',
            'customOptions' => 'nullable|array',
            'customOptions.*' => 'required|string|min:2|max:100|clean_text_only',
        ];

        $validator = Validator::make($data, $rules, []);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        $input = $request->all();
        DB::beginTransaction();
        try {
            $taxDefinationObj = new TaxDefination();
            $taxDefinationObj->tax_name = $input['name'];
            if (!$taxDefinationObj->save()) {
                throw new \Exception(trans("actions.err.contact_save_failed"));
            }
            if(!empty($input['customOptions'])) {
                foreach($input['customOptions'] as $custom) {
                    $taxElementObj = new TaxElements();
                    $taxElementObj->tax_element = $custom;
                    $taxElementObj->tax_defination_id = $taxDefinationObj->id;
                    if (!$taxElementObj->save()) {
                        throw new \Exception(trans("actions.err.contact_save_failed"));
                    }
                }
            }
            $return['status'] = 'success';
            $return['msg'] = trans('custom_tax.alerts_and_messages.custom_tax_added');
            $return['data'] = trans('custom_tax.alerts_and_messages.custom_tax_added');

            DB::commit();
            Log::info("saveCustomTax add taxDefination id:" . $taxDefinationObj->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollback();
            $return['msg'] = $e->getMessage();
            Log::error("saveCustomTax add taxDefination id: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function removeCustomTax(Request $request) {
        if(!Auth::user()->hasPermissionTo('CustomTaxDelete')) {
            $return["msg"] = trans('custom_tax.alerts_and_messages.permission_denied');
            return response()->json($return);
        }
        $return = ['status' => 'fail', 'msg' => trans("custom_tax.alerts_and_messages.something_went_wrong")];
        $currentUser = Auth::user();
        $procurementRole = $currentUser->procurementRole != null ? $currentUser->procurementRole->getRole() : null;
        if(Auth::user()->isSuperUser() != true && ($procurementRole == null || $procurementRole == false)) {
            $return = [
                "msg" => trans('custom_tax.alerts_and_messages.insufficient_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        $input = $request->only('id');
        DB::beginTransaction();
        try {
            $customTaxObj = TaxDefination::find($input['id']);

            if(!$customTaxObj) {
                DB::rollback();
                $return['status'] = 'fail';
                $return['msg'] = trans('custom_tax.alerts_and_messages.record_already_deleted');
                return response()->json($return);
            }

            DB::table('tax_elements')->whereIn('tax_defination_id', [$input['id']])->delete();
            $customTaxObj->delete();

            DB::commit();

            $return['status'] = 'success';
            $return['title'] = trans('custom_tax.alerts_and_messages.success');
            $return['msg'] = trans("custom_tax.alerts_and_messages.remove_success");
            Log::info("removeCustomTax id:" . $customTaxObj->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("removeCustomTax : " . $e->getMessage());
            $return['msg'] = $e->getMessage();
            return response()->json($return);
        }
    }

    public function removeCustomElement(Request $request) {
        $return = ['status' => 'fail', 'msg' => trans("custom_tax.alerts_and_messages.something_went_wrong")];
        if (!Auth::user()->hasPermissionTo('CustomTaxDelete')) {
            $return['msg'] = trans('custom_tax.alerts_and_messages.permission_denied');
            return response()->json($return);
        }
        $currentUser = Auth::user();
        $procurementRole = $currentUser->procurementRole != null ? $currentUser->procurementRole->getRole() : null;
        if(Auth::user()->isSuperUser() != true && ($procurementRole == null || $procurementRole == false)) {
            $return = [
                "msg" => trans('custom_tax.alerts_and_messages.insufficient_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        $input = $request->only('id');
        DB::beginTransaction();
        try {
            $customTaxObj = TaxElements::findOrFail($input['id']);
            $TaxDefinationID = $customTaxObj->tax_defination_id;
            $deleted = DB::table('tax_elements')->where('id', $customTaxObj->id)->delete();
            if ($deleted !== 1) {
                throw new \Exception(trans("custom_tax.alerts_and_messages.remove_failed"));
            }

            DB::commit();

            $return['status'] = 'success';
            $return['title'] = trans('custom_tax.alerts_and_messages.success');
            $return['msg'] = trans("custom_tax.alerts_and_messages.remove_success");
            $return["tax_defination_id"] = $TaxDefinationID;
            $return["tax_details"]= TaxDefination::select('tax_defination.id as tax_def_id','tax_defination.tax_name',
            'tax_elements.id as tax_ele_id','tax_elements.tax_defination_id','tax_elements.tax_element')
            ->leftjoin('tax_elements','tax_elements.tax_defination_id','tax_defination.id')
            ->where('tax_defination.id', $TaxDefinationID)->whereNull('tax_elements.deleted_at')->get();
            Log::info("removeCustomElement id:" . $customTaxObj->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("removeCustomElement : " . $e->getMessage());
            $return['msg'] = $e->getMessage();
            return response()->json($return);
        }
    }

    public function editCustomTax(Request $request) {
        $return = ['status' => 'fail', 'msg' => trans("custom_tax.alerts_and_messages.trigger_not_found")];
        $currentUser = Auth::user();
        $procurementRole = $currentUser->procurementRole != null ? $currentUser->procurementRole->getRole() : null;
        if(Auth::user()->isSuperUser() != true && ($procurementRole == null || $procurementRole == false)) {
            $return = [
                "msg" => trans('custom_tax.alerts_and_messages.insufficient_permission'),
                "status" => "danger"
            ];
            $request->session()->flash("msg", $return);
            return redirect("dashboard");
        }
        $input = $request->all();
        try {
            $taxDefExists = TaxDefination::find($input['id']);
            if(!$taxDefExists) {
                $return['status'] = 'fail';
                $return['msg'] = trans('custom_tax.alerts_and_messages.record_already_deleted');
                return response()->json($return);
            }

            $CustomTaxObj = TaxDefination::select('tax_defination.id as tax_def_id','tax_defination.tax_name',
            'tax_elements.id as tax_ele_id','tax_elements.tax_defination_id','tax_elements.tax_element')
            ->leftjoin('tax_elements','tax_elements.tax_defination_id','tax_defination.id')
            ->where('tax_defination.id', $input['id'])->whereNull('tax_elements.deleted_at')->get();
            
            $tax_element[] = null;
            $tax_element_id[] = null;
            $tax_name = null;
            $tax_defination_id = null;
            foreach($CustomTaxObj as $t) {
                $tax_name = $t->tax_name;
                $tax_defination_id = $t->tax_def_id;
                array_push($tax_element,$t->tax_element);
                array_push($tax_element_id,$t->tax_ele_id);
            }
            if($CustomTaxObj->count() == 0 ){
                $CustomTaxDefObj = $taxDefExists;
                $CustomTaxDefObj["tax_name"] = $CustomTaxDefObj->tax_name;
                $CustomTaxDefObj["tax_defination_id"] = $CustomTaxDefObj->id;
                $return['data'] = $CustomTaxDefObj;
            }else {
                $result["tax_details"] = $CustomTaxObj;
                $result["tax_name"] = $tax_name;
                $result["tax_defination_id"] = $tax_defination_id;
                $return['data']= $result;
            }
            $return['status'] = 'success';
            $return['msg'] = trans("custom_tax.alerts_and_messages.edit_success");
            Log::info("editCustomTax id:" . $input['id']. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            $return['msg'] = $e->getMessage();
            Log::error("editCustomTax: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function updateCustomTax(Request $request) {
        if(!Auth::user()->hasPermissionTo('CustomTaxEdit')) {
            return response()->json([
                'status' => 'fail',
                'msg' => trans('custom_tax.alerts_and_messages.permission_denied'),
            ]);
        }

        $return = ['status' => 'fail', 'msg' => trans("custom_tax.alerts_and_messages.update_failed")];
        $currentUser = Auth::user();
        $procurementRole = $currentUser->procurementRole != null ? $currentUser->procurementRole->getRole() : null;
        if(Auth::user()->isSuperUser() != true && ($procurementRole == null || $procurementRole == false)) {
            return response()->json([
                'status' => 'fail',
                'msg' => trans('custom_tax.alerts_and_messages.insufficient_permission'),
            ]);
        }

        $data = $request->only('id', 'name', 'customOptions');
        $rules = [
            'id' => 'required|integer|min:1',
            'name' => 'required|string|min:2|max:100|clean_text_only',
            'customOptions' => 'nullable|array',
            'customOptions.*' => 'required|string|min:2|max:100|clean_text_only',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'msg' => $validator->errors()->first(),
            ]);
        }

        $input = $request->all();
        DB::beginTransaction();
        try {
            $taxDefinationObj = TaxDefination::findOrFail($input['id']);
            $taxDefinationObj->tax_name = $input['name'];
            if(! $taxDefinationObj->update()) {
                throw new \Exception(trans("custom_tax.alerts_and_messages.trigger_update_failed"));
            }

            if (!empty($input['customOptions'])) {
                foreach ($input['customOptions'] as $id => $custom_name) {
                    // Decide update vs create based on whether $id is an EXISTING row,
                    // not whether the text matches an existing row.
                    $taxElementsObj = is_numeric($id)
                        ? TaxElements::where('id', $id)
                            ->where('tax_defination_id', $taxDefinationObj->id)
                            ->first()
                        : null;

                    if ($taxElementsObj) {
                        // Existing row -> update in place
                        $taxElementsObj->tax_element = $custom_name;
                        if (!$taxElementsObj->save()) {
                            throw new \Exception(trans("actions.err.contact_save_failed"));
                        }
                    } else {
                        // No matching existing row -> genuinely new option
                        $taxElementObj = new TaxElements();
                        $taxElementObj->tax_element = $custom_name;
                        $taxElementObj->tax_defination_id = $taxDefinationObj->id;
                        if (!$taxElementObj->save()) {
                            throw new \Exception(trans("actions.err.contact_save_failed"));
                        }
                    }
                }
            }

            DB::commit();

            $return['status'] = 'success';
            $return['msg'] = trans("custom_tax.alerts_and_messages.update_success");
            $return['data'] = '';
            Log::info("updateTicketTrigger id:" . $taxDefinationObj->id . " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch (\Exception $e) {
            DB::rollback();
            Log::error("updateTicketTrigger: " . $e->getMessage());
            $return['msg'] = $e->getMessage();
            return response()->json($return);
        }
    }

    public function getCustomtax(Request $request, $ticket_id="") {
        $return = array();
        $search = $request->input("search", "");
        $q = $request->has('q') ? trim(strip_tags($request->q)) : null;
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);
        $db = DB::table("tax_defination as d")->select("d.id", "d.tax_name as text")->whereNull("d.deleted_at");
        if($search) {
            $db->whereRaw("c.tax_name like '%" . $search . "%'");
        }
        elseif($q && strlen($q) > 2) {
            $db->whereRaw("d.tax_name like '" . $q . "%'");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $db->orderBy('d.tax_name', 'asc');
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["status"] = "success";
        $return["msg"] = trans('custom_tax.alerts_and_messages.departments_fetched_successfully');
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getElement($id) {
        $return = array("status"=>"failure", "data"=>array(), "msg"=>trans('custom_tax.alerts_and_messages.no_problem_category_found'));
        $types = [];
        $types = TaxElements::where("tax_defination_id", "=", $id)
            ->whereNull("deleted_at")
            ->select("id", "tax_element", "tax_defination_id")
            ->orderBy("tax_element")
            ->get();
        if(count($types)) {
            $result = [];

            foreach($types as $t) {
                $temp = $t->only("id", "tax_element", "tax_defination_id");
                $result[] = $temp;
            }

            $return["data"] = $result;
            $return["msg"] = "";
            $return["status"] = "success";
        }
        return response()->json($return);
    }
}
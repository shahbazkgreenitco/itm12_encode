<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RolesPermissionController;
use App\Mail\SendMailMFA;
use App\Models\BotFeedback;
use App\Models\Procurement\ApprovalRequest;
use App\Models\Ticket\Status;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketApprovalRequest;
use App\Models\Ticket\TicketProcureRequest;
use App\Models\ChangeManagement\Record;
use App\Models\TaskManagement\Task;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\Company;
use App\Models\Label;
use App\Models\Department;
use App\Models\UserGroup;
use App\Models\Group;
use App\Models\Currency;
use App\Models\Ticket\Privilege;
use App\Models\Ticket\Config;
use App\Models\Device;
use App\Models\Accessory;
use App\Models\Consumable;
use App\Models\AccessoryUser;
use App\Models\LicenseSeat;
use App\Models\Settings;
use App\Models\StatusBoard;
use App\Models\StatusBoardGroup;
use App\Models\StatusBoardItem;
use App\Models\StatusBoardIncident;
use App\Models\AppPlatform;
use App\Models\ItemGeoLocation;
use App\Helpers\Common as CommonHelper;
use App\Helpers\Old;
use DB;
use Illuminate\Support\Facades\Storage;
use Mail;
use Spatie\Permission\Models\Permission;
use Validator;
use Auth;
use Log;
use stdClass;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\UserDetails;
use App\Models\ChatMessage;
use App\Events\PrivateMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Http\Controllers\Ticket\IndexController;
use App\Models\KnowledgeManagement\Document;

class CommonController extends Controller {
    public function companyDropdownList(){
        $return = [];
        $return['status'] = "success";
        $return['msg'] = "";

        if( Auth::user()->isSuperUser() ){
            $return['data'] = Company::select('id','name')->get();
        }else{
            $return['data'] = Company::select('id','name')->where('id','=',Auth::user()->company_id)->get();
        }

        return $return;
    }

    public function statusLabelsDropdownList(Request $request){
        $search = $request->input("search", "");
        $labels = Label::whereNull("deleted_at")->orderBy("name")->select("id", "name as text");

        $return = [];
        $return['status'] = "success";
        $return['msg'] = "";

        if($search)
            $labels->where('name','like', "%" .$search. "%");

        $return['data'] = $labels->get()->toArray();

        return $return;
    }

    public function locationDropdownList(Request $request){

        $search = $request->input("search", "");
        $page = $request->input("page", 1);

        $return = [];
        $return['status'] = "success";
        $return['msg'] = "";
       
        $locationlist = Location::select('id','name');
        if($search)
            $locationlist->where('name','like', "%" .$search. "%");
        // if( Auth::user()->isSuperUser() ){
            $return['data'] = $locationlist->get();
        // }else{
            // $return['data'] = Location::all();
            // $return['data'] = Location::where('id','=',Auth::user()->company_id)->get();
        // }

        return $return;

        // $return = array();
        
        // $skip = (($page * 20) - 20);

        // $db = DB::table("locations")->select("id", "name as text");
        // if($search) {
        //     $db->where("name", "like", "%" . $search . "%");
        // }
        // $count = $db->count();
        // $db->skip($skip)->take(20);
        // $result = $db->get();

        // $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        // $return["results"] = count($result) ? $result->toArray() : [];
        // return response()->json($return);
    }

    /* Using it for user selection on ticket module */
    public function getUsersByQuery(Request $request) {
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20'
        ];
        $messages = [];

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

        $take = $request->list_size ? $request->list_size : 20;
        $skip = $page * $take;

        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("users")->select("id", DB::raw('trim(concat_ws("", first_name, last_name, " (", username, ")")) as text'))->where('activated',1)->whereNull('deleted_at');
        if($search) {
            $db->whereRaw("trim(concat_ws('', first_name, last_name, ' (', username, ')')) like ?", '%' . $search . '%');
        }

        if( ! Auth::user()->isSuperUser() ){
            $db->where("company_id", Auth::user()->company_id);
        }

        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();
        $return['tot'] = $count;
        $return['filter_record'] = $return['tot'];
        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;
        $db->skip($skip);
        $db->take($take);

        $return["status"] = "success";
        $return["msg"] = "";
        $return["data"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getStatusLabels(Request $request) {
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20'
        ];
        $messages = [];

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

        $take = $request->list_size ? $request->list_size : 20;
        $skip = $page * $take;

        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("status_labels")->select("id", "name as text");
        $db->whereNull("deleted_at");
        $db->wherenull("sold");
        if($search) {
            $db->whereRaw("name like '%" . $search . "%'");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];
        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;
        $db->skip($skip);
        $db->take($take);

        $return["status"] = "success";
        $return["msg"] = "";
        $return["data"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function ajaxStatusLabel(Request $request) {
        $return = array();
        $return["status"] = "success";
        $return["msg"] = "";
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("status_labels")->select("id", "name as text");
        $db->wherenull("sold");
        $db->whereNull("deleted_at");
        if($search) {
            $db->where("name", "like", "%" . $search . "%");
        }
        $db->orderBy("name");
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        // $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["data"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getSupplierByQuery(Request $request) {
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20'
        ];
        $messages = [];

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

        $take = $request->list_size ? $request->list_size : 20;
        $skip = $page * $take;

        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("suppliers")->select("id", "name as text");
        if($search) {
            $db->where("name", "like", "%" . $search . "%");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];
        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;
        $db->skip($skip);
        $db->take($take);

        // $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["data"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getDeviceFrom(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("purchases as pur")->select("id", DB::raw('concat_ws(" - ", pur.invoice_no, date_format(pur.invoice_date, "%d/%m/%Y")) as text'));
        $db->whereNull("deleted_at");
        if($search) {
            $db->whereRaw("concat_ws(' - ', pur.invoice_no, date_format(pur.invoice_date, '%d/%m/%Y')) like '%" . $search . "%'");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getPlaceByQuery(Request $request) {
        $return = array();
        $return["status"] = "success";
        $return["msg"] = "";
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("places")->select("id", "place as text");
        if($search) {
            $db->where("place", "like", "%" . $search . "%");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        // $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["data"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getInvoiceByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("purchases as pur")->select("id", DB::raw('concat_ws(" - ", pur.invoice_no, date_format(pur.invoice_date, "%d/%m/%Y")) as text'));
        $db->whereNull("deleted_at");
        if($search) {
            $db->whereRaw("concat_ws(' - ', pur.invoice_no, date_format(pur.invoice_date, '%d/%m/%Y')) like '%" . $search . "%'");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getLeaseByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $company_id = $request->input("company_id", null);
        $skip = (($page * 20) - 20);

        $db = DB::table("lease_agreements as l");
        $db->join('suppliers as s', 'l.leaser', '=', 's.id');
        $db->select("l.id", DB::raw('concat_ws(" - ", concat("#", l.id), concat(s.name, " (", date_format(l.start_date, "%b %Y"), " - ", date_format(l.end_date, "%b %Y"), ")")) as text'));

        $db->where("company_id", "=", $company_id);
        if($search) {
            $db->whereRaw('concat_ws(" - ", concat("#", l.id), concat(s.name, " (", date_format(l.start_date, "%b %Y"), " - ", date_format(l.end_date, "%b %Y"), ")")) like "%' . $search . '%"');
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getInvoiceByQueryForApp(Request $request) {
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20'
        ];
        $messages = [];

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

        $take = $request->list_size ? $request->list_size : 20;
        $skip = $page * $take;

        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("purchases as pur")->select("id", DB::raw('concat_ws(" - ", pur.invoice_no, date_format(pur.invoice_date, "%d/%m/%Y")) as text'));
        $db->whereNull("deleted_at");
        if($search) {
            $db->whereRaw("concat_ws(' - ', pur.invoice_no, date_format(pur.invoice_date, '%d/%m/%Y')) like '%" . $search . "%'");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];
        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;
        $db->skip($skip);
        $db->take($take);

        $return["data"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getLeaseByQueryForApp(Request $request) {
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20'
        ];
        $messages = [];

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

        $take = $request->list_size ? $request->list_size : 20;
        $skip = $page * $take;

        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $company_id = $request->input("company_id", null);
        $skip = (($page * 20) - 20);

        $db = DB::table("lease_agreements as l");
        $db->join('suppliers as s', 'l.leaser', '=', 's.id');
        $db->select("l.id", DB::raw('concat_ws(" - ", concat("#", l.id), concat(s.name, " (", date_format(l.start_date, "%b %Y"), " - ", date_format(l.end_date, "%b %Y"), ")")) as text'));

        $db->where("company_id", "=", $company_id);
        if($search) {
            $db->whereRaw('concat_ws(" - ", concat("#", l.id), concat(s.name, " (", date_format(l.start_date, "%b %Y"), " - ", date_format(l.end_date, "%b %Y"), ")")) like "%' . $search . '%"');
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];
        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;
        $db->skip($skip);
        $db->take($take);

        $return["data"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function currencyDropdownList() {
        $return = array();
        $return["status"] = "success";

        $curobj = new Currency;
        $return["data"] = $curobj->getCurrencies();
        

        return response()->json($return);
    }
    
    public function currencyDropdownListAPI() {
        $return = array();
        $return["status"] = "success";

        $curobj = new Currency;
        $returndata = $curobj->getCurrencies();
        foreach($returndata as $key => $value) {
            $currency = $value;
            $currency['key'] = $key;
            $currencies[] = $currency;
        }
        $return["currencies"] = $currencies;

        return response()->json($return);
    }

    public function defaultCurrency() {
        $return = array();
        $return["status"] = "success";

        $curobj = new Currency;
        $default_currency = Settings::first()->default_currency;
        $returnData = $curobj->getCurrencies();
        foreach($returnData as $key => $value) {
            $currency = $value;
            $currency_key['key'] = $key;
            if($default_currency == $currency_key['key']){
                $default_currency_name['name'] = $currency['name'];
                $default_currency_name['symbol'] = $currency['symbol'];
                $default_currency_name['symbol_html'] = $currency['symbol_html'];
                $default_currency_names = $default_currency_name;
            }
        }
        $return["default_currency"] = $default_currency_names;

        return response()->json($return);
    }

    public function getManufacturerByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("manufacturers")->select("id", "name as text");
        if($search) {
            $db->where("name", "like", "%" . $search . "%");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getProjectByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("projects")->select("id", "name as text")->whereNull('deleted_at');
        if($search) {
            $db->where("name", "like", "%" . $search . "%");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getContractByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("lease_agreements")
            ->select("id", "contract_number as text", DB::raw("CONCAT(start_date, ' to ', end_date) as agreement_date"));
        if ($search) {
            $db->where("contract_number", "like", "%" . $search . "%")
                ->orWhere(DB::raw("CONCAT(start_date, ' to ', end_date)"), "like", "%" . $search . "%");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getComponentByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("components")->select("id", "name as text")->whereNull('deleted_at');
        if ($search) {
            $db->where("name", "like", "%" . $search . "%");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getLicenseByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("licenses")->select("id", "name as text")->whereNull('deleted_at');
        if ($search) {
            $db->where("name", "like", "%" . $search . "%");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getTicketsByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("licenses")
        ->select(
            "id",
            "name as text")->whereNull('deleted_at');
        if ($search) {
            $db->where("name", "like", "%" . $search . "%");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getTicketProcureRequest(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = TicketProcureRequest::select('id', DB::raw("CONCAT(procure_tag, ' (', subject, ')') as text"));
        if($search) {
            if(is_array($search)) {
                $db->where("procure_tag", "like", "%" . $search['value'] . "%")->orwhere("subject", "like", "%" . $search['value'] . "%");
            } else {
                $db->where("procure_tag", "like", "%" . $search . "%")->orwhere("subject", "like", "%" . $search . "%");
            }
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }
    public function getRecordByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = Record::select('id', DB::raw("CONCAT(record_tag, ' (', subject, ')') as text"));
        if($search) {
            if(is_array($search)) {
                $db->where("record_tag", "like", "%" . $search['value'] . "%")->orwhere("subject", "like", "%" . $search['value'] . "%");
            } else {
                $db->where("record_tag", "like", "%" . $search . "%")->orwhere("subject", "like", "%" . $search . "%");
            }
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }
    public function getTaskByQuery(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = Task::select('id', DB::raw("CONCAT('#',id, ' (', name, ')') as text"));
        if($search) {
            if(is_array($search)) {
                $db->where("id", "like", "%" . $search['value'] . "%")->orwhere("name", "like", "%" . $search['value'] . "%");
            } else {
                $db->where("id", "like", "%" . $search . "%")->orwhere("name", "like", "%" . $search . "%");
            }
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getManufacturer(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("manufacturers")->select("id", "name as text");
        if($search) {
            $db->where("name", "like", "%" . $search . "%");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getInternalPlaceByLoc(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("places as p")->join('locations as l', 'p.location_id', '=', 'l.id')->select('p.id', DB::raw('concat(l.name, " - ", p.place) as text'))->orderBy('text');

        if($search) {
            $db->where("p.place", "like", "%" . $search . "%")
            ->orWhere("l.name", "like", "%" . $search . "%");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function deviceDropdownForLicense(){
        $return = array();
        $return["status"] = "success";
        foreach(Device::with(['model','assigneduser'])->get() as $device){
            $assetUsrFulname = $device->assigneduser->first_name . $device->assigneduser->last_name;
            $assetUsrFulname = '('.(empty($assetUsrFulname) ? 'Unassigned' : $assetUsrFulname).')';

            $return["data"][] = ['id'=>$device->id,'value'=> $device->asset_tag . ' - ' . $device->name.' '.$assetUsrFulname.' '.$device->model->name];
        }

        return response()->json($return);
    }

    public function dashboard(Request $request) {
        $return = ['status'=>'success','msg'=>''];
        $return['data'] = [];
        
        $totDevice  = DB::select("select count(*) as tot_devices from assets as a join status_labels as sl on sl.id = a.status_id where a.assigned_to = ".Auth::user()->id." and a.deleted_at is null and sl.deployed = 1");

        $totacceries = DB::select("SELECT count(au.id) as tot_acces FROM `accessories_users` as au join accessories as a on a.id = au.accessory_id WHERE au.assigned_to = ".Auth::user()->id." and a.deleted_at is null");

        $totLicence = DB::select("SELECT count(*) as tot_lic FROM `license_seats` as ls LEFT JOIN assets as dev ON dev.id = ls.asset_id AND ls.deleted_at IS NULL WHERE ls.assigned_to = ".Auth::user()->id." OR dev.assigned_to = ".Auth::user()->id);

        $board_info = DB::select("select count(*) as tot_items, sum(case when status = 1 then 1 else 0 end) as tot_active, sum(case when status = 2 then 1 else 0 end) as tot_per_iss, sum(case when status = 3 then 1 else 0 end) as tot_par_out, sum(case when status = 4 then 1 else 0 end) as tot_major from status_board_items where is_enabled = 1");

        $ticket_info = DB::select('SELECT count(id) as tot, sum(case when status_id in (5,6) then 1 else 0 end) as tot_resolved, sum(case when status_id not in (5,6) then 1 else 0 end) as tot_not_resolved, sum(case when priority_id = 1 then 1 else 0 end) as tot_critical, sum(case when priority_id = 2 then 1 else 0 end) as tot_high, sum(case when priority_id = 3 then 1 else 0 end) as tot_medium, sum(case when priority_id = 4 then 1 else 0 end) as tot_low FROM `tkt_tickets` WHERE deleted_at is null and creator_id = '. Auth::user()->id . '  and is_temp is null');

        $return['data']['mydevicecount'] = $totDevice[0]->tot_devices;
        $return['data']['myaccessoryCount'] = $totacceries[0]->tot_acces;
        $return['data']['mylicensecount'] = $totLicence[0]->tot_lic;
        
        if($board_info[0]->tot_items > 0) {
            $board_info[0]->overall_per = intval(($board_info[0]->tot_active / $board_info[0]->tot_items) * 100);
        }
        $return['data']['board_info'] = $board_info;

        if($ticket_info[0]->tot > 0) {
            $ticket_info[0]->overall_per = intval(($ticket_info[0]->tot_resolved / $ticket_info[0]->tot) * 100);
        }
        $return['data']['ticket_info'] = $ticket_info;

        return response()->json($return);
    }

    /* function to collect the app platform informations */
    public function updateAppPlatform(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to update platform information"];
        $data = $request->only('name', 'osVersion', 'uuid', 'manufacturer', 'model', 'serial', 'device_platform', 'imei', 'meid', 'esn', 'imsi', 'android_id');
        $rules = [
            'name' => 'nullable|string|max:255',
            'osVersion' => 'nullable|string|max:255',
            'uuid' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial' => 'nullable|string|max:255',
            'imei' => 'nullable|string|max:255',
            'meid' => 'nullable|string|max:255',
            'esn' => 'nullable|string|max:255',
            'imsi' => 'nullable|string|max:255',
            'android_id' => 'nullable|string|max:255',
            'device_platform' => 'nullable|string|max:255'
        ];

        $validator = Validator::make($data, $rules);
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $get_record = AppPlatform::where('uuid', 'like', $data['uuid'])->limit(1)->get();

        if(!$get_record || !count($get_record)) {
            $ap = AppPlatform::create($data);
        }
        else {
            $ap = $get_record[0];
            $ap->fill($data);
            $ap->save();
        }

        if($ap) {
            $return['status'] = 'success';
            $return['msg'] = '';
        }

        return response()->json($return);
    }

    /* update app device location */
    public function updateItemGeoLocation(Request $request) {
        try {
            $return = ["status" => "fail"];
            $data = $request->only('uuid', 'lat', 'lng');
            $rules = [
                'uuid' => 'required|string|max:255',
                'lat' => 'required|string|max:30',
                'lng' => 'required|string|max:30'
            ];

            $validator = Validator::make($data, $rules);
            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $app_platform = AppPlatform::firstOrCreate(['uuid' => $data['uuid']]);

            if(! $app_platform) {
                return response()->json($return);
            }

            $app_platform->lat = $data['lat'];
            $app_platform->lng = $data['lng'];
            $app_platform->save();

            $geo_data = [];
            $geo_data['app_platform_id'] = $app_platform->id;
            $geo_data['lat'] = $data['lat'];
            $geo_data['lng'] = $data['lng'];
            $geo_data['created_at'] = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');

            ItemGeoLocation::create($geo_data);
            $return["status"] = "success";
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(["status" => "fail"]);
        }
    }

    /* to list the mobile devices to the admin user */
    public function mobileDevices(Request $request) {
        return view('mobile_devices/index');
    }

    public function mobileDevicesList(Request $request) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            '1'  => 'name',
            '2'  => 'model',
            '3'  => 'manufacturer',
            '4'  => 'uuid',
            '5'  => 'platform',
            '6'  => 'updated_at'
        );

        $db = DB::table('app_platforms as a');
        
        $db->select('a.id', 'a.name', 'a.osVersion', 'a.uuid', 'a.manufacturer', 'a.model',  'a.serial', 'a.device_platform', 'a.lat', 'a.lng');
        $db->addSelect(DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

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

    public function mobileDevicesInfo(Request $request, $id) {
        try {
            $return = [];
            $return['platform'] = AppPlatform::findOrFail($id);
            $return['histories'] = ItemGeoLocation::where('app_platform_id', '=', $id)->whereNotNull('created_at')->orderBy('created_at', 'desc')->select('lat', 'lng', DB::raw('DATE_FORMAT(created_at, "%d %b %Y %h:%i %p") as at'))->get();
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['platform' => null, 'histories' => null]);
        }
    }

    public function ajaxDashboard() {

        $return = [];

        $user_id = Auth::user()->id;
        //total checked out device of loggedin user
        $total_checkout_devices = DB::select("select count(a.id) as tot from assets as a where a.assigned_for = 1 and a.assigned_to =  ".$user_id." " );
        $data["total_checkout_devices"] = $total_checkout_devices[0];


        //total checked out accessories of loggedin user
        $total_checkout_accessories = DB::select("SELECT COUNT(au.id) AS tot
        FROM accessories_users AS au
        JOIN accessories AS acc ON au.accessory_id = acc.id
        JOIN assets AS a ON au.assigned_to = a.id
        WHERE acc.deleted_at IS null and au.assigned_for = 1 AND au.assigned_to = ".$user_id." OR a.assigned_for = 1 AND a.assigned_to = ".$user_id."" );
        $data["total_checkout_accessories"] = $total_checkout_accessories[0];

        //total checked out Licenses  of loggedin user
        $hideDeviceViaLicenses = Settings::getSettings()->isHideDeviceViaLicenses();
        $checkout_info_sql = 'case when a.assigned_for = 1 then "via Device" when ls.assigned_to ="'.$user_id.'" then  "Direct Checkout" else "" end';
        if($hideDeviceViaLicenses) {
            $checkout_info_sql = 'case when ls.assigned_to ="'.$user_id.'" then  "Direct Checkout" else "" end';
        }

        $db = DB::table('license_seats as ls');
        $db->leftJoin('licenses as l', function($q) {
            $q->on('l.id', '=', 'ls.license_id');
            $q->where('l.deleted_at');
        });

        if( ! $hideDeviceViaLicenses ) {
            $db->leftJoin('assets as a', 'a.id', '=', 'ls.asset_id');
        }

        $db->whereNull('ls.deleted_at');
        $db->where(function($q) use($user_id,$hideDeviceViaLicenses) {
            if($hideDeviceViaLicenses) {
                $q->where('ls.assigned_to', '=', $user_id);
            }
            else {
                $q->where('a.assigned_for', '=', 1)->where('a.assigned_to', '=', $user_id)->orWhere('ls.assigned_to', '=', $user_id);
            }
        });
        $data["total_checkout_license"]["tot"] = $db->count();

        //total checked out components of loggedin user
        $total_checkout_components = DB::select("SELECT COUNT(c.id) AS tot
        FROM components AS c
        JOIN assets AS a ON a.id = c.checked_out_to
        WHERE c.deleted_at IS null and a.assigned_for = 1 AND a.assigned_to = ".$user_id."");
        $data["total_checkout_components"] = $total_checkout_components[0];


        //Recently Checkout and Checkin records loggedin user

        $chk_rec = DB::table('asset_logs as al');
        $chk_rec->leftJoin('users as adm', 'adm.id', '=', 'al.user_id');
        $chk_rec->leftJoin('assets as d', 'd.id', '=', 'al.asset_id');
        $chk_rec->leftJoin('accessories as a', 'a.id', '=', 'al.accessory_id');
        $chk_rec->leftJoin('consumables as c', 'c.id', '=', 'al.consumable_id');
        $chk_rec->leftJoin('licenses as l', 'l.id', '=', 'al.asset_id');
        $chk_rec->leftJoin('models as mdl', 'mdl.id', '=', 'd.model_id');
        $chk_rec->leftJoin('projects as pr', 'pr.id', '=', 'al.project_id');
        $chk_rec->where('al.checkedout_to', '=', $user_id);
        $chk_rec->whereNull('al.filename');
        $chk_rec->select('al.id', 'al.action_type', 'al.asset_type','d.asset_tag as asset_tag','c.id as con_tag','a.name as accessory_name','l.id as lic_tag','l.name as lic_name','d.deleted_at as asset_delete','a.deleted_at as acc_delete','l.deleted_at as lic_delete','c.deleted_at as con_delete');
        $chk_rec->addSelect(DB::raw('case when l.id is not null then concat_ws("","LIC",l.id) else "" end as lic_batch_no'));
        if (config("app.client") == "etherealmachines") {
            $chk_rec->addSelect(DB::raw('concat("","AC",a.id) as acc_tag'));
        } else {
            $chk_rec->addSelect(DB::raw('concat("","A",a.id) as acc_tag'));
        }
        $chk_rec->addSelect(DB::raw('DATE_FORMAT(al.updated_at, "%d %b %Y %h:%i %p") as checkin_checkout_date'));
        $chk_rec->OrderBy('al.updated_at','desc');
        if($chk_rec->count() != 0) {
            $data["last_checkout_checkin_records"]["records"] = $chk_rec->take(10)->get();
        } else {
            $data["last_checkout_checkin_records"]["records"] = [];
        }

        //status board
        $board_info = DB::select("select count(*) as tot_items, IFNULL(sum(case when status = 1 then 1 else 0 end), 0) as tot_active, IFNULL(sum(case when status = 2 then 1 else 0 end), 0) as tot_per_iss, IFNULL(sum(case when status = 3 then 1 else 0 end), 0) as tot_par_out, IFNULL(sum(case when status = 4 then 1 else 0 end), 0) as tot_major from status_board_items where is_enabled = 1");

        if($board_info[0]->tot_items > 0) {
            $board_info[0]->overall_per = intval(($board_info[0]->tot_active / $board_info[0]->tot_items) * 100);
        } else {
            $board_info[0]->overall_per = 0;
        }
        $data['board_info'] = $board_info;

        $ticket_info = DB::select('SELECT count(id) as tot, IFNULL(sum(case when status_id in (5) then 1 else 0 end), 0) as tot_resolved, IFNULL(sum(case when status_id not in (5,6) then 1 else 0 end), 0) as tot_not_resolved, IFNULL(sum(case when priority_id = 1 then 1 else 0 end), 0) as tot_critical, IFNULL(sum(case when priority_id = 2 then 1 else 0 end), 0) as tot_high, IFNULL(sum(case when priority_id = 3 then 1 else 0 end), 0) as tot_medium, IFNULL(sum(case when priority_id = 4 then 1 else 0 end), 0) as tot_low FROM `tkt_tickets` WHERE deleted_at is null and creator_id = '. Auth::user()->id . '  and is_temp is null');

        if($ticket_info[0]->tot > 0) {
            $ticket_info[0]->overall_per = intval(($ticket_info[0]->tot_resolved / $ticket_info[0]->tot) * 100);
        } else {
            $ticket_info[0]->overall_per = 0;
        }
        $data['ticket_info'] = $ticket_info;

        $db = TicketProcureRequest::select('tkt_procure_requests.*');
        $db->Join('tkt_approval_request as ar', function ($join) {
            $join->on('tkt_procure_requests.id', '=', 'ar.pr_id');
            $join->where('ar.user_id', Auth::user()->id);
        });
        $db->whereNull('tkt_procure_requests.deleted_at');
        $dataMyApprovalsInfo = $db->count();
        // $db->where('tkt_procure_requests.creator_id', '=', Auth::user()->id);
        $dataMyrequest = $db->count();
        $data['service_request_info'] = ['my_approvals' => $dataMyrequest];

        // Recently updated tickets logged in user
        $rcnt_tkt = DB::table('tkt_tickets as t');
        $rcnt_tkt->leftJoin('departments as dep', 't.department_id', '=', 'dep.id');
        $rcnt_tkt->leftJoin('tkt_statuses as s', 't.status_id', '=', 's.id');
        $rcnt_tkt->leftJoin('tkt_priorities as p', 't.priority_id', '=', 'p.id');
        $rcnt_tkt->leftJoin('users as u', 't.creator_id', '=', 'u.id'); // ticket raiser
        $rcnt_tkt->leftJoin('users as cu', 't.created_by', '=', 'cu.id'); // who actually created ticket
        $rcnt_tkt->where(function($q) use($user_id) {
            $q->whereRaw('t.creator_id = ' . $user_id . ' or find_in_set(' . $user_id . ', t.merged_tkt_creators)');
        });
        $rcnt_tkt->whereNull("t.is_temp");
        $rcnt_tkt->whereNull("t.deleted_at");
        $rcnt_tkt->select('t.id', 't.subject', 's.name as status', 't.status_id', 'p.name as priority');
        $rcnt_tkt->addSelect(DB::raw('concat(u.first_name, " ", u.last_name, " @ ", u.username) as creator_name'));
        $rcnt_tkt->addSelect(DB::raw('DATE_FORMAT(t.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
        $rcnt_tkt->OrderBy('t.updated_at','desc');

        if($rcnt_tkt->count() != 0) {
            $data["recently_updated_tickets"]["tickets"] = $rcnt_tkt->take(15)->get();
        } else {
            $data["recently_updated_tickets"]["tickets"] = [];
        }

        //my ratings
        $ratingMyTickets = Ticket::select(
            DB::raw('sum(tkt_tickets.feedback) as rating_given'),
            DB::raw('round(avg(tkt_tickets.feedback),1) as overall_rating'),
            DB::raw('max(tkt_tickets.feedback) as highest_rating'),
            DB::raw('min(tkt_tickets.feedback) as lowest_rating')
        )
            ->where('tkt_tickets.creator_id', Auth()->user()->id)
            ->whereNull('tkt_tickets.is_temp');
        $datamyTicketsRatingInfo = $ratingMyTickets->get();

        $data['user_ticket_rating_info'] = ($datamyTicketsRatingInfo);

        // technician ratings
        $techRatingMyTickets = Ticket::select(
            DB::raw('sum(tkt_tickets.feedback) as rating_given'),
            DB::raw('round(avg(tkt_tickets.feedback),1) as overall_rating'),
            DB::raw('max(tkt_tickets.feedback) as highest_rating'),
            DB::raw('min(tkt_tickets.feedback) as lowest_rating')
        )
            ->where('tkt_tickets.assigned_to', Auth()->user()->id)
            ->whereNull('tkt_tickets.is_temp');
        $dataTechTicketsRatingInfo = $techRatingMyTickets->get();

        $data['tech_ticket_rating_info'] = ($dataTechTicketsRatingInfo);

        $data["isAssetsEnable"] = config("services.assets.enabled") ? config("services.assets.enabled") : "0";
        $data["isTicketEnable"] = config("services.service_ticket.enabled") ? config("services.service_ticket.enabled") : "0";
        $data["isStatusBoardEnable"] = config("services.status_board.enabled") ? config("services.status_board.enabled") : "0";
        $data["isKDEnable"] = config("services.knowledge_document.enabled") ? config("services.knowledge_document.enabled") : "0";
        $data["androidVersion"] = 25;
        $data["iosVersion"] = 20;
        $return["dashboard"] = $data;

        return response()->json($return);

    }

    public function ajaxAdminDashboard() {

        $return = [];
        $user_id = Auth::user()->id;
        $now = Carbon::now(config('app.timezone'));
        $date = new stdClass;
        $date->now = Carbon::now(config('app.timezone'));
        $date->startFrom = Carbon::now('Asia/Kolkata')->subMonth(4)->startOfMonth();
        $date->lastMonth = Carbon::now('Asia/Kolkata')->subMonth(1)->startOfMonth();

        $date->yesterday = Carbon::now('Asia/Kolkata')->yesterday();
        $date->last_7_days = Carbon::now('Asia/Kolkata')->subDays(7)->startOfDay();
        $date->last_quarter_start = Carbon::now('Asia/Kolkata')->subMonth(5)->startOfMonth();
        $date->last_quarter = Carbon::now('Asia/Kolkata')->subMonth(3)->endOfMonth();
        $date->lastMonth = Carbon::now('Asia/Kolkata')->subMonth(1)->startOfMonth();

        /* to get the quarter */
        $date1 = Carbon::now(config('app.timezone'))->firstOfQuarter();
        $date2 = Carbon::now(config('app.timezone'))->lastOfQuarter();

        $today = $now->format('Y-m-d');
        $tkt_config = Config::first();
        $get_user_privileges = Privilege::select('department_id')->where('user_id', '=', Auth::user()->id)->get();
        $user_privileged_departments = [];
        if(count($get_user_privileges)) {
            $user_privileged_departments = $get_user_privileges->pluck('department_id');
            $arr = $user_privileged_departments->toArray();
            $usr_prv_dep = implode(',', $arr);
        } else {
            $usr_prv_dep = 0;
        }
        if(Auth::user()->hasPermissionTo('DeviceRead')) {
            $locationArray = $userLocationAccess = $userLocationAccessAccessories = "";

            $loc_previllage = Auth::user()->permitted_locations;
            $permitted_loc = explode(",", $loc_previllage);
            $settings = Settings::getSettings();
            if($settings->location_config == 1) {
                if(empty($loc_previllage)) {
                    $userLocationAccess = ' and assets.rtd_location_id = 0';
                    $userLocationAccessAccessories = ' and accessories.location_id = 0';
                } else {
                    $userLocationAccess = ' and assets.rtd_location_id IN('.$loc_previllage.')';
                    $userLocationAccessAccessories = ' and accessories.location_id IN('.$loc_previllage.')';
                }
            }


            $company_id = Company::first()->id;
            // total assets
            $total_devices = DB::select('select count(id) as tot from assets where deleted_at is null '.$userLocationAccess.' and company_id = '.$company_id );
            $data["total_assets"] = $total_devices[0];

            // assets ready to deploy
            $available_devices = DB::select("SELECT count(assets.id) as tot from assets join status_labels as s on s.id = assets.status_id
            where assets.status_id = 1 and assets.deleted_at is null and s.deleted_at is null ".$userLocationAccess." and company_id = ".$company_id);
            $data["asset_ready_to_deploy"] = $available_devices[0];

            // total licenses
            $total_licenses = DB::select("select count(ls.id) as tot
            from license_seats as ls join licenses as l on ls.license_id = l.id where ls.deleted_at is null and l.deleted_at is null");
            $data["total_licenses"] = $total_licenses[0];

            // total assets in inventory
            $total_inventory = DB::select("select count(*) as tot from itm_network_inventory_basic as nw JOIN assets ON nw.BIOSSerialNumber = assets.serial where nw.is_dupe is null and assets.deleted_at is null and nw.company = " . $company_id . " ".$userLocationAccess);
            $data["assets_in_inventory"] = $total_inventory[0];

            // total Accessory
            $total_accessory = DB::select("SELECT SUM(qty) AS tot FROM accessories WHERE deleted_at IS NULL AND company_id = " . $company_id . " ".$userLocationAccessAccessories." ");
            $data["total_accessory"] = ['tot' =>  (int) $total_accessory[0]->tot];

            // available Accessory
            $avail_accessory = DB::select("SELECT SUM(CASE WHEN co.tot_checkouts IS NOT NULL THEN (accessories.qty - (accessories.scrap_qty + co.tot_checkouts) ) when accessories.scrap_qty > 0 then accessories.qty - accessories.scrap_qty else accessories.qty END) AS tot FROM accessories  LEFT JOIN (SELECT cu.accessory_id, COUNT(cu.id) AS tot_checkouts FROM accessories_users AS cu where cu.assigned_to is not null GROUP BY cu.accessory_id) AS co ON accessories.id = co.accessory_id WHERE accessories.deleted_at IS NULL and accessories.company_id = " . $company_id . " ".$userLocationAccessAccessories);
            $data["avail_accessory"] = ['tot' =>  (int) $avail_accessory[0]->tot];

            //total open Tickets
            $total_open_tickets = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id IN (".$usr_prv_dep.") AND t.status_id = 1 AND t.is_temp IS NULL AND t.deleted_at IS NULL");
            $data["total_open_tickets"] = $total_open_tickets[0];

            //total resolved Tickets
            $total_resolved_tickets_by_today = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id IN (".$usr_prv_dep.") AND t.status_id = 5 AND t.updated_at = '".$today."' AND t.is_temp IS NULL AND t.deleted_at IS NULL");
            $data["total_resolved_tickets_by_today"] = $total_resolved_tickets_by_today[0];

            //total Tickets on hold
            $total_tickets_onhold = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id IN (".$usr_prv_dep.") AND t.status_id = 4 AND t.is_temp IS NULL AND t.deleted_at IS NULL");
            $data["total_tickets_onhold"] = $total_tickets_onhold[0];

            //total Tickets on hold
            $total_tickets_waiting_for_first_response = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id = '".$tkt_config->default_department_id."' AND t.status_id != 10 AND t.deleted_at IS NULL AND t.is_temp IS NULL");
            $data["total_tickets_waiting_for_first_response"] = $total_tickets_waiting_for_first_response[0];

            // Total Not Assigned Tickets
            $total_not_assigned_tickets = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id IN (".$usr_prv_dep.") AND t.status_id = 1 AND t.assigned_to IS NULL AND t.is_temp IS NULL AND t.deleted_at IS NULL");
            $data["total_not_assigned_tickets"] = $total_not_assigned_tickets[0];

            //total checked out device of loggedin user

            //Recently Checkout and Checkin records loggedin user
            $chk_rec = DB::table('asset_logs as al');
            $chk_rec->leftJoin('users as adm', 'adm.id', '=', 'al.user_id');
            $chk_rec->leftJoin('assets as d', 'd.id', '=', 'al.asset_id');
            $chk_rec->leftJoin('accessories as a', 'a.id', '=', 'al.accessory_id');
            $chk_rec->leftJoin('consumables as c', 'c.id', '=', 'al.consumable_id');
            $chk_rec->leftJoin('licenses as l', 'l.id', '=', 'al.asset_id');
            $chk_rec->leftJoin('models as mdl', 'mdl.id', '=', 'd.model_id');
            $chk_rec->leftJoin('projects as pr', 'pr.id', '=', 'al.project_id');
            $chk_rec->where('al.checkedout_to', '=', $user_id);
            $chk_rec->whereNull('al.filename');
            $chk_rec->select('al.id', 'al.action_type', 'al.asset_type', 'd.id as device_id', 'd.asset_tag as asset_tag','c.id as con_tag','a.name as accessory_name','l.id as lic_tag','l.name as lic_name','d.deleted_at as asset_delete','a.deleted_at as acc_delete','l.deleted_at as lic_delete','c.deleted_at as con_delete');
            $chk_rec->addSelect(DB::raw('case when l.id is not null then concat_ws("","LIC",l.id) else "" end as lic_batch_no'));
            if (config("app.client") == "etherealmachines") {
                $chk_rec->addSelect(DB::raw('concat("","AC",a.id) as acc_tag'));
            } else {
                $chk_rec->addSelect(DB::raw('concat("","A",a.id) as acc_tag'));
            }
            $chk_rec->addSelect(DB::raw('DATE_FORMAT(al.updated_at, "%d %b %Y %h:%i %p") as checkin_checkout_date'));
            $chk_rec->OrderBy('al.updated_at','desc');
            if($chk_rec->count() != 0) {
                $data["last_checkout_checkin_records"]["records"] = $chk_rec->take(10)->get();
            } else {
                $data["last_checkout_checkin_records"]["records"] = [];
            }

            // Breached Ticket count
            $myAssignedTicketsBreachedInfo = Ticket::select('tkt_tickets.id', 'tkt_tickets.subject', 'tkt_tickets.department_id', 'tkt_tickets.assigned_to', 'tkt_tickets.status_id', 'tkt_tickets.created_at', 'tkt_tickets.updated_at', 'ts.name as status', 'tp.name as priority', 'tkt_tickets.tat_expire', 'tkt_tickets.tat_remaining_mins', DB::raw('DATE_FORMAT(tkt_tickets.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'))
                ->leftJoin('tkt_statuses as ts', 'ts.id', 'tkt_tickets.status_id')
                ->leftJoin('tkt_priorities as tp', 'tp.id', 'tkt_tickets.priority_id');

            $tat_halt_enable = Status::whereNotIn('id', [4,5,6,10])->where('tat_halt', 1)->get()->pluck('id');
            $myAssignedTicketsBreachedInfo->where(function($query) use($tat_halt_enable) {
                $query->whereIn('tkt_tickets.status_id', $tat_halt_enable)->where('tkt_tickets.tat_remaining_mins', '<=', 0);
                $tat_halt_disable = Status::where('tat_halt', 0)->get()->pluck('id');
                $query->orWhere(function($query) use($tat_halt_disable) {
                    $query->whereIn('tkt_tickets.status_id', $tat_halt_disable)->where('tkt_tickets.tat_expire', '<', now());
                });
            });
            $myAssignedTicketsBreachedInfo->whereIn('department_id', $user_privileged_departments);
            $myAssignedTicketsBreachedInfo->whereNull('tkt_tickets.is_temp')->whereNull('merge_primary');
            $slaBreachedCount = $myAssignedTicketsBreachedInfo->orderBy('id', 'desc')->count();

            $db = TicketProcureRequest::select('tkt_procure_requests.*');
            $db->Join('tkt_approval_request as ar', function ($join) {
                $join->on('tkt_procure_requests.id', '=', 'ar.pr_id');
                $join->where('ar.user_id', Auth::user()->id);
            });
            $db->whereNull('tkt_procure_requests.deleted_at');
            $dataMyApprovalsInfo = $db->count();
            $db->where('tkt_procure_requests.creator_id', Auth::user()->id);
            $dataMyrequest = $db->count();
            $data['service_request_info'] = ['my_approvals' => $dataMyrequest];

            //status board
            if(config("services.status_board.enabled")) {
                $board_info = DB::select("select count(*) as tot_items, IFNULL(sum(case when status = 1 then 1 else 0 end), 0) as tot_active, IFNULL(sum(case when status = 2 then 1 else 0 end), 0) as tot_per_iss, IFNULL(sum(case when status = 3 then 1 else 0 end), 0) as tot_par_out, IFNULL(sum(case when status = 4 then 1 else 0 end), 0) as tot_major from status_board_items where is_enabled = 1");

                if ($board_info[0]->tot_items > 0) {
                    $board_info[0]->overall_per = intval(($board_info[0]->tot_active / $board_info[0]->tot_items) * 100);
                } else {
                    $board_info[0]->overall_per = 0;
                }
                $data['board_info'] = $board_info;
            }
            $ticket_info = DB::select('SELECT count(id) as tot, IFNULL(sum(case when status_id = 5 then 1 else 0 end), 0) as tot_resolved, IFNULL(sum(case when status_id = 6 then 1 else 0 end), 0) as tot_closed, IFNULL(sum(case when status_id not in (5,6) then 1 else 0 end), 0) as tot_not_resolved, IFNULL(sum(case when priority_id = 1 then 1 else 0 end), 0) as tot_critical, IFNULL(sum(case when priority_id = 2 then 1 else 0 end), 0) as tot_high, IFNULL(sum(case when priority_id = 3 then 1 else 0 end), 0) as tot_medium, IFNULL(sum(case when priority_id = 4 then 1 else 0 end), 0) as tot_low FROM tkt_tickets WHERE department_id IN ('.$usr_prv_dep.') and deleted_at is null and is_temp is null');

            if($ticket_info[0]->tot > 0) {
                $ticket_info[0]->tot_closed = $ticket_info[0]->tot_closed;
                $ticket_info[0]->overall_per = intval(($ticket_info[0]->tot_closed / $ticket_info[0]->tot) * 100);
            } else {
                $ticket_info[0]->overall_per = 0;
            }
            $ticket_info[0]->sla_breached = $slaBreachedCount;
            $data['ticket_info'] = $ticket_info;

            // My tickets login user
            $myTickets = Ticket::select(DB::raw('COUNT(id) AS tot'))->whereNull("tkt_tickets.is_temp")->where('creator_id', $user_id)->orWhereRaw("find_in_set($user_id, merged_tkt_creators)")->get();
            $data["my_tickets"] = $myTickets[0];

            // Assigned Tickets login user
            $myTickets = Ticket::select(DB::raw('COUNT(id) AS tot'))->whereNull("tkt_tickets.is_temp")->where('assigned_to', $user_id)->where("status_id", "!=", 6)->get();
            $data["assigned_tickets"] = $myTickets[0];

            //Recently updated tickets loggedin user
            $rcnt_tkt = DB::table('tkt_tickets as t');
            $rcnt_tkt->leftJoin('departments as dep', 't.department_id', '=', 'dep.id');
            $rcnt_tkt->leftJoin('tkt_statuses as s', 't.status_id', '=', 's.id');
            $rcnt_tkt->leftJoin('tkt_priorities as p', 't.priority_id', '=', 'p.id');
            $rcnt_tkt->leftJoin('users as u', 't.creator_id', '=', 'u.id'); // ticket raiser
            $rcnt_tkt->leftJoin('users as cu', 't.created_by', '=', 'cu.id'); // who actually created ticket
            $rcnt_tkt->where(function($q) use($user_id) {
                $q->whereRaw('t.creator_id = ' . $user_id . ' or find_in_set(' . $user_id . ', t.merged_tkt_creators)');
            });
            $rcnt_tkt->whereNull("t.is_temp");
            $rcnt_tkt->whereNull("t.deleted_at");
            $rcnt_tkt->select('t.id', 't.subject', 's.name as status', 't.status_id', 'p.name as priority');
            $rcnt_tkt->addSelect(DB::raw('concat(u.first_name, " ", u.last_name, " @ ", u.username) as creator_name'));
            $rcnt_tkt->addSelect(DB::raw('DATE_FORMAT(t.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
            $rcnt_tkt->OrderBy('t.updated_at','desc');

            if($rcnt_tkt->count() != 0) {
                $data["recently_updated_tickets"]["tickets"] = $rcnt_tkt->take(15)->get();
            } else {
                $data["recently_updated_tickets"]["tickets"] = [];
            }

            // Feedback Counters
            if(Auth::user()->hasAnyRole(['SuperAdmin', 'Admin'])) {
                /* feedback calc */
                $query3 = "select  b.last7days_sum, b.this_year_sum , b.yesterday_sum, b.today_sum, b.this_month_sum, b.last3months_sum from (SELECT SUM(CASE WHEN YEAR(t.updated_at) = " . $date->now->year . " THEN cast(feedback as decimal(12,1)) ELSE 0 END) AS this_year_sum, SUM(CASE WHEN DATE(t.updated_at) >= '" . $date->last_7_days->toDateString() . "' THEN feedback ELSE 0 END) AS last7days_sum,  SUM(CASE WHEN DATE(t.updated_at) = '" . $date->yesterday->toDateString() . "' THEN feedback ELSE 0 END) AS yesterday_sum, SUM(CASE WHEN DATE(t.updated_at) = '" . $date->now->toDateString() . "' THEN feedback ELSE 0 END) AS today_sum,  SUM(CASE WHEN YEAR(t.updated_at) = " . $date->now->year . " AND MONTH(t.updated_at) = " . $date->now->month . " THEN feedback ELSE 0 END) AS this_month_sum, SUM(CASE WHEN (DATE(t.updated_at) >= '" . $date1->toDateString() . "' AND DATE(t.updated_at) <= '" . $date2->toDateString() . "') THEN feedback ELSE 0 END) AS last3months_sum FROM tkt_tickets AS t WHERE t.deleted_at IS NULL and t.is_temp is null and t.department_id in (".$usr_prv_dep.") and t.feedback is not null and t.status_id in (5,6)) as b";
                // $query3 = "select count(feedback), sum(feedback), round((sum(feedback)/count(feedback))) as overall from tkt_tickets where feedback is not null and status_id in (5,6)";
                $showAllTicketsInfo = true;
            }
            elseif(Auth::user()->hasPermission("service_tickets")) {
                /* feedback calc */
                $query3 = "select b.last7days_sum, b.this_year_sum, b.yesterday_sum, b.today_sum, b.this_month_sum, b.last3months_sum from (SELECT SUM(CASE WHEN YEAR(t.updated_at) = " . $date->now->year . " THEN cast(feedback as decimal(12,1)) ELSE 0 END) AS this_year_sum, SUM(CASE WHEN DATE(t.updated_at) >= '" . $date->last_7_days->toDateString() . "' THEN feedback ELSE 0 END) AS last7days_sum,  SUM(CASE WHEN DATE(t.updated_at) = '" . $date->yesterday->toDateString() . "' THEN feedback ELSE 0 END) AS yesterday_sum, SUM(CASE WHEN DATE(t.updated_at) = '" . $date->now->toDateString() . "' THEN feedback ELSE 0 END) AS today_sum,  SUM(CASE WHEN YEAR(t.updated_at) = " . $date->now->year . " AND MONTH(t.updated_at) = " . $date->now->month . " THEN feedback ELSE 0 END) AS this_month_sum,  SUM(CASE WHEN (DATE(t.updated_at) >= '" . $date1->toDateString() . "' AND DATE(t.updated_at) <= '" . $date2->toDateString() . "') THEN feedback ELSE 0 END) AS last3months_sum FROM tkt_tickets AS t WHERE t.deleted_at IS NULL and t.is_temp is null and t.department_id IN ($usr_prv_dep) and t.feedback is not null and t.status_id in (5,6)) as b";
                // $query3 = "select count(feedback), sum(feedback), round((sum(feedback)/count(feedback))) as overall from tkt_tickets where feedback is not null and status_id in (5,6)";
                $showAllTicketsInfo = true;
            }
            if($query3) {
                $handler_feed_back_count = DB::select($query3);
                if(count($handler_feed_back_count)) {
                    $last7daysCount = Ticket::whereNull('deleted_at')->whereNull('is_temp')->whereIn('department_id',$user_privileged_departments)->whereNotNull('feedback')->whereIn('status_id', [5,6])->where('updated_at',">=",$date->last_7_days->toDateString())->count();
                    $thisYearCount = Ticket::whereRaw("Year(updated_at) >= ".$date->now->year)
                        ->whereIn('department_id',$user_privileged_departments)
                        ->whereIn('status_id', [5,6])
                        ->whereNotNull('feedback')
                        ->whereNull('deleted_at')
                        ->whereNull('is_temp')
                        ->count();
                    $yestedayCount = Ticket::where("updated_at",">=",$date->yesterday->format("Y-m-d 00:00:00"))
                        ->where("updated_at","<=",$date->yesterday->toDateString()." 23:59:59")
                        ->whereIn('department_id',$user_privileged_departments)
                        ->whereIn('status_id', [5,6])
                        ->whereNotNull('feedback')
                        ->whereNull('deleted_at')
                        ->whereNull('is_temp')
                        ->count();
                    $todayCount = Ticket::where("updated_at",">=",$date->now->format("Y-m-d 00:00:00"))
                        ->where("updated_at","<=",$date->now->toDateString()." 23:59:59")
                        ->whereIn('department_id',$user_privileged_departments)
                        ->whereIn('status_id', [5,6])
                        ->whereNotNull('feedback')
                        ->whereNull('deleted_at')
                        ->whereNull('is_temp')
                        ->count();
                    $thisMonthCount = Ticket::whereRaw("Year(updated_at) >= ".$date->now->year)
                        ->whereRaw("Month(updated_at) >= ".$date->now->month)
                        ->whereIn('department_id',$user_privileged_departments)
                        ->whereIn('status_id', [5,6])
                        ->whereNotNull('feedback')
                        ->whereNull('deleted_at')
                        ->whereNull('is_temp')
                        ->count();
                    $lastThreeMonthCount = Ticket::where("updated_at",">=",$date1->toDateString())
                        ->where("updated_at","<=",$date2->toDateString())
                        ->whereIn('department_id',$user_privileged_departments)
                        ->whereIn('status_id', [5,6])
                        ->whereNotNull('feedback')
                        ->whereNull('deleted_at')
                        ->whereNull('is_temp')
                        ->count();
                    $handler_feed_back_count[0]->last7days = round($last7daysCount != 0 ? ($handler_feed_back_count[0]->last7days_sum/$last7daysCount) : 0);
                    $handler_feed_back_count[0]->last7daysCount = $last7daysCount != 0 ? $last7daysCount : 0;
                    $handler_feed_back_count[0]->this_year = round($thisYearCount != 0 ? ($handler_feed_back_count[0]->this_year_sum/$thisYearCount) : 0);
                    $handler_feed_back_count[0]->this_yearCount = $thisYearCount != 0 ? ($thisYearCount) : 0;
                    $handler_feed_back_count[0]->yesterday = round($yestedayCount != 0 ? ($handler_feed_back_count[0]->yesterday_sum/$yestedayCount) : 0);
                    $handler_feed_back_count[0]->yesterdayCount = $yestedayCount != 0 ? $yestedayCount: 0;
                    $handler_feed_back_count[0]->today = round($todayCount != 0 ? ($handler_feed_back_count[0]->today_sum/$todayCount) : 0);
                    $handler_feed_back_count[0]->todayCount = $todayCount != 0 ? $todayCount : 0;
                    $handler_feed_back_count[0]->this_month = round($thisMonthCount != 0 ? ($handler_feed_back_count[0]->this_month_sum/$thisMonthCount) : 0);
                    $handler_feed_back_count[0]->this_monthCount = $thisMonthCount != 0 ? $thisMonthCount: 0;
                    $handler_feed_back_count[0]->last3months = round($lastThreeMonthCount != 0 ? ($handler_feed_back_count[0]->last3months_sum/$lastThreeMonthCount) : 0);
                    $handler_feed_back_count[0]->last3monthsCount = $lastThreeMonthCount != 0 ? $lastThreeMonthCount : 0;
                }
            }
            $data['handler_feed_back_count'] = $handler_feed_back_count;

            $data["isAssetsEnable"] = config("services.assets.enabled") ? config("services.assets.enabled") : "0";
            $data["isTicketEnable"] = config("services.service_ticket.enabled") ? config("services.service_ticket.enabled") : "0";
            $data["isStatusBoardEnable"] = config("services.status_board.enabled") ? config("services.status_board.enabled") : "0";
            $data["isKDEnable"] = config("services.knowledge_document.enabled") ? config("services.knowledge_document.enabled") : "0";
            $data["androidVersion"] = 25;
            $data["iosVersion"] = 20;
            $return["dashboard"] = $data;
        }
        return response()->json($return);

    }


    public function ajaxModuleDashboard(Request $request) {
        $return['data'] = [];
        try {
            $now = Carbon::now(config('app.timezone')); 
            $today = $now->format('Y-m-d');
            $check['device'] = $request->device;
            $check['network_inventory'] = $request->network_inventory;
            $check['accessory'] = $request->accessory;
            $check['license'] = $request->license;
            $check['consumable'] = $request->consumable;
            $check['user'] = $request->user;
            $check['ticket'] = $request->ticket;
            
        
            $get_user_privileges = Privilege::select('department_id')->where('user_id', '=', Auth::user()->id)->get();
            $user_privileged_departments = [];
            if(count($get_user_privileges)) {
                $user_privileged_departments = $get_user_privileges->pluck('department_id');
                $arr = $user_privileged_departments->toArray();
                $usr_prv_dep = implode(',', $arr);
            } else {
                $usr_prv_dep = 0;
            }
        
                //check the availability of device module
                if($check['device'] == 1){
                    //total devices
                    $total_devices = DB::select('select count(id) as tot from assets where deleted_at is null');
                    $data["total_devices"] = $total_devices[0];
                    //available devices
                    $available_devices = DB::select("SELECT count(a.id) as tot from assets as a join status_labels as s on s.id = a.status_id
                    where a.status_id = 1 and a.deleted_at is null and s.deleted_at is null");
                    $data["available_devices"] = $available_devices[0];

                } else {
                    $return['data'] = [];
                }

                //check the availability of network inventory
                if($check['network_inventory'] == 1){
                    //total inventory
                    $total_inventory = DB::select("select count(*) as tot from itm_network_inventory_basic as nw where nw.is_dupe is null and nw.deleted_at is null");
                    $data["total_inventory"] = $total_inventory[0];

                }else{
                    $return['data'] = [];
                }

                //check the availability of accessory
                if($check['accessory'] == 1) {
                    
                    //total Accessory
                    $total_accessory = DB::select("SELECT SUM(a.qty) AS tot FROM accessories AS a WHERE a.deleted_at IS NULL");
                    $data["total_accessory"] = $total_accessory[0];

                    //available Accessory
                    $avail_accessory = DB::select("SELECT SUM(CASE WHEN co.tot_checkouts IS NOT NULL THEN (c.qty - (c.scrap_qty + co.tot_checkouts) ) when c.scrap_qty > 0 then c.qty - c.scrap_qty else c.qty END) AS tot FROM accessories AS c LEFT JOIN (SELECT cu.accessory_id, COUNT(cu.id) AS tot_checkouts FROM accessories_users AS cu where cu.assigned_to is not null GROUP BY cu.accessory_id) AS co ON c.id = co.accessory_id WHERE c.deleted_at IS NULL");
                    $data["avail_accessory"] = $avail_accessory[0];

                } else {
                    $return['data'] = [];
                }

                //check the availability of license
                if($check['license'] == 1) {
                    //total licenses
                    $total_licenses = DB::select("select count(ls.id) as tot
                    from license_seats as ls join licenses as l on ls.license_id = l.id where ls.deleted_at is null and l.deleted_at is null");
                    $data["total_licenses"] = $total_licenses[0];

                    //available licenses
                    $available_licenses = DB::select("SELECT COUNT(license_seats.id) as tot
                    FROM `license_seats` LEFT JOIN licenses ON license_seats.license_id = licenses.id 
                    WHERE  (licenses.expiration_date >= '". $today ."' or licenses.expiration_date is null) and license_seats.deleted_at is null
                    and license_seats.assigned_to is null and license_seats.asset_id is null");
                    $data["available_licenses"] = $available_licenses[0];

                } else {
                    $return['data'] = [];
                }
                
                //check the availability of component
                if($check['consumable'] == 1) {
                    //total consumables
                    $total_consumables = DB::select("SELECT SUM(qty) AS tot FROM consumables WHERE deleted_at IS NULL");
                    $data["total_consumables"] = $total_consumables[0];

                    // available consumables
                    $used_consumables = DB::select("SELECT SUM(b.used) AS tot FROM (SELECT s.consumable_id, count(s.id) AS used FROM consumables_users AS s join consumables AS c ON c.id = s.consumable_id WHERE c.deleted_at IS NULL group BY s.consumable_id) AS b");
                    $data["avail_consumables"] = ['tot' => (int) $data["total_consumables"]->tot - (int) $used_consumables[0]->tot];

                } else {
                    $return['data'] = [];
                }

                //check the availability of user
                if($check['user']  == 1) {
                    //total licenses
                    $user_info = DB::select("select count(id) as tot_users, sum(case when activated = 1 and archived = 0 then 1 else 0 end) as tot_actived_users  from users where deleted_at is null");
                    $data["user_info"] = $user_info;

                } else {
                    $return['data'] = [];
                }

                //check the availability of ticket
                if($check['ticket'] == 1) {

                    //total Tickets
                    $total_tickets = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id IN (".$usr_prv_dep.") AND t.is_temp IS NULL AND t.deleted_at IS NULL");
                    $data["total_tickets"] = $total_tickets[0];

                } else {
                    $return['data'] = [];
                }
            
                $return['data'] = $data;
            }
            catch(\Exception $e) {
                $e->getMessage();
            } 
        
        return response()->json($return);

    }

    public function labelTranslation(Request $request) {
        $return = [];

        try {

            $it = array(
                "next" => "Prossimo",
                "accessories" => "Accessori",
                "action_type" => "Tipo di azione",
                "add" => "Aggiungi",
                "all" => "Tutti",
                "amc" => "AMC",
                "application" => "Applicazione",
                "are_you_sure_to_close" => "Sei sicuro di chiudere",
                "asset_owner" => "Responsabile Asset",
                "asset_tag" => "Etichetta Asset",
                "asset_type" => "Tipo Asset",
                "assigned" => "Assegnato",
                "audit" => "Audit",
                "available_records" => "Record disponibili",
                "back_trail" => "back-trail",
                "barcode" => "Codice a barre",
                "batch_no" => "Lotto numero",
                "button" => "Pulsante",
                "by" => "Di",
                "cancel" => "Cancella",
                "catch" => "Catturare",
                "cc" => "cc",
                "change_password" => "Cambia Password",
                "checkin" => "in ingresso",
                "checkout" => "Assegnato",
                "checkout_record" => "Checkout Record",
                "choose_file" => "scegli il file",
                "choose_from_gallery" => "scegli dalla galleria",
                "closed" => "Chiusi",
                "code" => "Codice",
                "colour" => "Colore",
                "company" => "Società",
                "components" => "Componenti",
                "connection" => "Connessione",
                "consumables" => "Consumabili",
                "conversion" => "Conversione",
                "cost" => "Costo",
                "count" => "Contare",
                "creator" => "Creatore",
                "critical" => "Critico",
                "currency_format" => "Formato Valuta",
                "danger" => "Pericolo",
                "dashboard" => "Cruscotto",
                "data" => "Dati",
                "data_will_remove" => "I dati verranno rimossi",
                "date" => "Data",
                "delete" => "Elimina",
                "department" => "Divisione",
                "detected" => "Rilevato",
                "device" => "Dispositivi",
                "device_tag" => "Etichetta Dispositivo",
                "document" => "Documento",
                "done" => "Fatto",
                "edit" => "Modifica",
                "email" => "Mail",
                "end" => "Fine",
                "enter" => "Accedere",
                "error" => "Errore",
                "excepted" => "Excepted",
                "exit_changed" => "Uscita modificata",
                "expected_check_in" => "Expected Check in",
                "expire_at" => "Scade il",
                "fail" => "Fallire",
                "failure" => "Fallimento",
                "file_name" => "Nome del file",
                "files" => "File",
                "final_submit" => "Invio finale",
                "for" => "per",
                "forgot_password" => "dimenticato la password",
                "form" => "modulo",
                "full_name" => "Nome Pieno",
                "function" => "Funzione",
                "get_support" => "Ottieni supporto",
                "go" => "Partire",
                "go_back_button" => "Pulsante Indietro",
                "guest" => "ospite",
                "handler" => "Handler",
                "high" => "Alto",
                "high_priority" => "Priorità alta",
                "history" => "Storia",
                "id" => "ID",
                "if_you_exit" => "se esci",
                "in_month" => "nel mese",
                "incident" => "Incidente",
                "internal_purpose" => "Scopo interno",
                "internet" => "Internet",
                "invalid" => "Non valido",
                "ip" => "IP",
                "issues" => "Problemi",
                "label" => "Etichetta",
                "last_checkout" => "ultimo checkout",
                "licences" => "Licenze",
                "loading" => "Caricamento in corso",
                "location" => "Luogo",
                "log_in" => "Accesso",
                "log_out" => "Log out",
                "low" => "Basso",
                "mac" => "MAC",
                "make_it" => "fallo",
                "manufacturer" => "Produttore",
                "mark" => "marchio",
                "medium" => "medio",
                "message" => "Messaggio",
                "model_name" => "Nome del modello",
                "my_item" => "I miei oggetti",
                "my_profile" => "Il mio profilo",
                "my_ticket" => "il mio biglietto",
                "my_total_tickets" => "I miei biglietti totali",
                "name" => "Nome",
                "navigation" => "Navigazione",
                "new" => "Nuovo",
                "no" => "no",
                "not_assigned" => "Non assegnato",
                "note" => "Note",
                "notificaction" => "Notifica",
                "number" => "Numero",
                "ok" => "ok",
                "on" => "su",
                "order_by" => "Ordinato da",
                "order_number" => "Numero d'ordine",
                "past" => "passato",
                "please_sign_in_with_your_information" => "accedi con le tue informazioni",
                "please_wait" => "attendere prego",
                "price" => "Prezzo",
                "priority" => "Priorità",
                "problem" => "Problema",
                "project_name" => "Nome del progetto",
                "purchase" => "Acquista",
                "qrcode" => "QR Code",
                "ratings" => "Giudizi",
                "reference" => "Riferimento",
                "remember_me" => "Ricordati di me",
                "remember_my_site" => "Ricorda il mio sito",
                "reopend" => "Riaperto",
                "requestable_devices" => "Dispositivi richiesti",
                "resale" => "Rivendita",
                "resolved" => "Risoluto",
                "restore" => "Ristabilire",
                "save" => "Salva",
                "search" => "Ricerca",
                "search_here" => "cerca qui",
                "select" => "Selezionare",
                "select_item" => "scegliere oggetto",
                "self" => "se stesso",
                "serial" => "Seriale",
                "server" => "server",
                "session" => "sessione",
                "site_url" => "URL del sito",
                "sold" => "venduto",
                "sold_out" => "Esaurito",
                "sort" => "ordinare",
                "sorting" => "ordinamento",
                "source" => "fonte",
                "spam" => "Spam",
                "star" => "Stella",
                "started" => "iniziato",
                "status" => "Stato",
                "status_board" => "Scheda di stato",
                "stock_place" => "Stock Place",
                "sub_problem" => "Sotto-problema",
                "success" => "Successo",
                "supplier" => "Fornitore",
                "take_photo" => "fare foto",
                "tat" => "tat",
                "time_out" => "tempo scaduto",
                "to" => "per",
                "total" => "totale",
                "transfer" => "trasferimento",
                "type_something" => "Scrivi qualcosa",
                "update_at" => "aggiorna a",
                "user_name" => "user name",
                "uuid" => "uuid",
                "wait" => "aspettare",
                "warranty" => "Garanzia",
                "will_loose" => "perderà",
                "write_message" => "Scrivi un messaggio",
                "yes" => "sì"
            );

            $en = array(
                "next" => "Next",
                "accessories" => "Accessories",
                "action_type" => "Action type",
                "add" => "Add",
                "all" => "All",
                "amc" => "AMC",
                "application" => "Application",
                "are_you_sure_to_close" => "Are you sure to close",
                "asset_owner" => "Asset Owner",
                "asset_tag" => "Asset Tag",
                "asset_type" => "Asset Type",
                "assigned" => "Assigned",
                "audit" => "Audit",
                "available_records" => "Available Records",
                "back_trail" => "back-trail",
                "barcode" => "Barcode",
                "batch_no" => "Batch no",
                "button" => "Button",
                "by" => "By",
                "cancel" => "Cancel",
                "catch" => "Catch",
                "cc" => "cc",
                "change_password" => "Change Password",
                "checkin" => "Checkin",
                "checkout" => "Checkout",
                "checkout_record" => "Checkout Record",
                "choose_file" => "choose file",
                "choose from gallery" => "choose from gallery",
                "closed" => "Closed",
                "code" => "Code",
                "colour" => "Colour",
                "company" => "Company",
                "components" => "Components",
                "connection" => "Connection",
                "consumables" => "Consumables",
                "conversion" => "Conversion",
                "cost" => "Cost",
                "count" => "Count",
                "creator" => "Creator",
                "critical" => "Critical",
                "currency_format" => "Currency Format",
                "danger" => "Danger",
                "dashboard" => "Dashboard",
                "data" => "Data",
                "data_will_remove" => "Data will remove",
                "date" => "Date",
                "delete" => "Delete",
                "department" => "Department",
                "detected" => "Detected",
                "device" => "Device",
                "device_tag" => "Device Tag",
                "document" => "Document",
                "done" => "Done",
                "edit" => "Edit",
                "email" => "Email",
                "end" => "End",
                "enter" => "Enter",
                "error" => "Error",
                "excepted" => "Excepted",
                "exit_changed" => "Exit Changed",
                "expected_check_in" => "Expected Check in",
                "expire_at" => "Expire at",
                "fail" => "Fail",
                "failure" => "Failure",
                "file_name" => "File name",
                "files" => "Files",
                "final_submit" => "Final submit",
                "for" => "for",
                "forgot_password" => "Forgot Password",
                "form" => "Form",
                "full_name" => "Full Name",
                "function" => "Function",
                "get_support" => "Get Support",
                "go" => "Go",
                "go_back_button" => "Go back button",
                "guest" => "Guest",
                "handler" => "Handler",
                "high" => "High",
                "high_priority" => "High priority",
                "history" => "History",
                "id" => "ID",
                "if_you_exit" => "if you exit",
                "in_month" => "in month",
                "incident" => "Incident",
                "internal_purpose" => "Internal purpose",
                "internet" => "Internet",
                "invalid" => "Invalid",
                "ip" => "IP",
                "issues" => "Issues",
                "label" => "Label",
                "last_checkout" => "last checkout",
                "licences" => "Licences",
                "loading" => "Loading",
                "location" => "Location",
                "log_in" => "Log in",
                "log_out" => "Log out",
                "low" => "Low",
                "mac" => "MAC",
                "make_it" => "make it",
                "manufacturer" => "Manufacturer",
                "mark" => "mark",
                "medium" => "medium",
                "message" => "message",
                "model_name" => "Model Name",
                "my_item" => "My item",
                "my_profile" => "My Profile",
                "my_ticket" => "My Ticket",
                "my_total_tickets" => "My Total Tickets",
                "name" => "Name",
                "navigation" => "Navigation",
                "new" => "New",
                "no" => "No",
                "not_assigned" => "Not Assigned",
                "note" => "Note",
                "notificaction" => "Notificaction",
                "number" => "Number",
                "ok" => "ok",
                "on" => "on",
                "order_by" => "Order by",
                "order_number" => "Order Number",
                "past" => "past",
                "please_sign_in_with_your_information" => "please sign in with your information",
                "please_wait" => "please wait",
                "price" => "Price",
                "priority" => "Priority",
                "problem" => "Problem",
                "project_name" => "Project Name",
                "purchase" => "Purchase",
                "qrcode" => "QR Code",
                "ratings" => "Ratings",
                "reference" => "Reference",
                "remember_me" => "Remember me",
                "remember_my_site" => "Remember my site",
                "reopend" => "Reopened",
                "requestable_devices" => "Requestable Devices",
                "resale" => "Resale",
                "resolved" => "Resolved",
                "restore" => "Restore",
                "save" => "Save",
                "search" => "Search",
                "search_here" => "search here",
                "select_item" => "select item",
                "self" => "self",
                "serial" => "Serial",
                "server" => "server",
                "session" => "session",
                "site_url" => "site url",
                "sold" => "Sold",
                "sold_out" => "Sold out",
                "sort" => "sort",
                "sorting" => "sorting",
                "source" => "source",
                "spam" => "Spam",
                "star" => "Star",
                "started" => "started",
                "status" => "Status",
                "status_board" => "Status Board",
                "stock_place" => "Stock Place",
                "sub_problem" => "Sub-Problem",
                "supplier" => "Supplier",
                "take_photo" => "take photo",
                "tat" => "tat",
                "time_out" => "time out",
                "to" => "to",
                "total" => "total",
                "transfer" => "transfer",
                "type_something" => "type something",
                "update_at" => "update at",
                "user_name" => "user name",
                "uuid" => "uuid",
                "wait" => "wait",
                "warranty" => "Warranty",
                "will_loose" => "will loose",
                "write_message" => "write message",
                "yes" => "yes"
            );

            if($request->translate == "it"){
                $return["translate"]['it'] = $it;
            }else{
                $return["translate"]['en'] = $en;
            }
            
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($return);
        }


    }

    public function addBotFeedback(Request $request) {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to add the Feedback'
        ];

        $data = $request->only('feedback','comment');
        try {
            $rules = [
                'feedback' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:2000',
            ];

            $messages = [
                'feedback.required' => 'Please add the Feedback'
            ];
            $validate = Validator::make($data, $rules, $messages);

            if ($validate->fails()) {
                $v = $validate->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $bot_feedback = new BotFeedback();
            $bot_feedback->fill($data);
            $bot_feedback->user_id = Auth::user()->id;
            $bot_feedback->save();
            $return["status"] = "success";
            $return["msg"] = "Feedback Added successfully.";
            if(isset($request->id)) {
                $con = new IndexController();
                $addfeedback = $con->addFeedback($request);
                Log::info($addfeedback);
                if(!$addfeedback) {
                    $return["status"] = "fail";
                    $retur['msg'] = "Unable to add the feedback";
                    return response()->json($return);
                }
            }
            return response()->json($return);

        } catch(\Exception $e) {
            Log::error("addBotFeedback error: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function botMfaSendMail(Request $request) {
        try {
            $return = ['status' => 'fail', 'msg' => "Unable to send mail"];
            $input = $request->all();

            $rules = [
                'user_id'         => 'required|integer',
            ];
            $messages = [];

            $validator = Validator::make($request->all(), $rules,$messages);
            if ($validator->fails()) {

                return response()->json([
                    "status"    => 'fail',
                    "msg"       => 'Error in validation',
                    "errors"    => $validator->messages()
                ]);
            }

            $user = User::find($input["user_id"]);
            if(!empty($user)) {
                $psNumber = $user->employee_num;
                if(isset($user->employee_num) && $user->employee_num != '') {
                    if(config('mail.service_enabled') && $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::queue($user->email)->send(new SendMailMFA($user));
                        } catch (\Exception $ex) {
                            Log::error("botMfaSendMail mail:" . $ex->getMessage());
                        }
                    }
                } else {
                    $return["msg"] = 'Employee number does not exist';
                    return response()->json($return);
                }
            } else {
                $return["msg"] = 'User does not exist';
                return response()->json($return);
            }
            $return["status"] = 'success';
            $return["msg"] = 'MFA Reset request is sent successfully.';
            return response()->json($return);
        } catch(\Exception $e) {
            $error = sprintf('[%s],[%d] ERROR:[%s]', __METHOD__, __LINE__, json_encode($e->getMessage(), true));
            Log::error("botMfaSendMail Error:" . $error);
            return response()->json($return);
        }
    }

    public function getUserDetailsFromEmail(Request $request) {
        $return = ["status" => "fail", "msg" => "Data not fetched"];
        $input = $request->all();

        try {
            $rules = [
                'email' => 'required|string|email|max:255|exists:users,email',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $userObj = User::select('id','department_id', 'manager_id', 'activated', 'employee_num', 'access_token')->where('email', $input["email"])->first();
            if(empty($userObj)) {
                $return["msg"] = "User not found";
                return response()->json($return);
            }
            $userObj["siteUrl"] = config('app.url')."api";
            $userObj["isNormalUser"] = ($userObj->hasAnyRole(['SuperAdmin', 'Admin']) || $userObj->hasPermission("service_tickets")) ? false : true;
            $client = config('app.client');
            switch($client) {
                case "ltts":
                    $botName = "LTTS";
                    break;
                case "staging":
                    $botName = "GIB";
                    break;
                default:
                    $botName = "GIB";
                    break;
            }
            $userObj->botName = $botName;
            $return["data"] = $userObj;
            $return["msg"] = "User data fetch successfully";
            $return["status"] = "success";
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error("getUserDetailsFromEmail: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function getDeviceDetailsFromEmail(Request $request) {
        $return = ["status" => "fail", "msg" => "Data not fetched"];
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
        if($user != "greenITLtts" || $pass != "gr@6M@hW#~22") {
            $return["msg"] = "Username or password is wrong";
            return response()->json($return);
        }

        try {
            $rules = [
                'email' => 'required|string|email|max:255|exists:users,email',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $userObj = User::where('email', $request->email)->where('activated', 1)->first();
            if(empty($userObj)) {
                $return["msg"] = "User email address not found or deactivated";
                return response()->json($return);
            }

            $assigned_device = User::select('a.asset_tag','a.serial','models.name as mdl_name','a.name as device_name','manufacturers.name as manufacturers_name')
                ->leftJoin('assets as a', 'a.assigned_to', '=', 'users.id')
                ->leftJoin('models', 'models.id', '=', 'a.model_id')
                ->leftJoin('manufacturers', 'manufacturers.id', '=', 'models.manufacturer_id')
                ->where('a.assigned_for', '=', 1)->where('a.assigned_to', $userObj->id)->get();
            if(empty($assigned_device)) {
                $return["msg"] = "User not found";
                return response()->json($return);
            }

            $return["msg"] = "Device data fetched successfully!";
            $return["status"] = "success";
            $return["data"] = $assigned_device;
            $return['recordsTotal'] = $assigned_device->count();
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error("getDeviceDetailsFromEmail: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function getDeviceDetailsBySerial(Request $request) {
        $return = ["status" => "fail", "msg" => "Data not fetched"];
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
        if($user != "greenITLtts" || $pass != "gr@6M@hW#~22") {
            $return["msg"] = "Username or password is wrong";
            return response()->json($return);
        }
        try {
            $rules = [
                'serial' =>'required|exists:assets,serial',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $deviceObj = Device::select('assets.asset_tag','assets.serial','models.name as mdl_name','assets.name as device_name','manufacturers.name as manufacturers_name')
                ->leftJoin('models', 'models.id', '=', 'assets.model_id')
                ->leftJoin('manufacturers', 'manufacturers.id', '=', 'models.manufacturer_id')
                ->where('serial', $request->serial)->first();

            if(empty($deviceObj)) {
                $return["msg"] = "Device not found";
                return response()->json($return);
            }

            $return["data"] = $deviceObj;
            $return["msg"] = "Device data fetched successfully!";
            $return["status"] = "success";
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error("getDeviceDetailsBySerial: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function getLangData(Request $request) {
        $return = ["status" => "fail", "msg" => "Data not fetched"];
        try {
            $lang = $request->header('lang');
            if($lang == 'it') {
                $file_path = "it.json";
                $raw_content = Storage::get($file_path);
            } elseif($lang == 'de') {
                $file_path = "de.json";
                $raw_content = Storage::get($file_path);
            } else {
                $file_path = "en_US.json";
                $raw_content = Storage::get($file_path);
            }
            $return['status'] = "success";
            $return['msg'] = "Data fetched successfully";
            $return['locales'] = config('app.available_locale');
            $return['data'] = json_decode($raw_content);
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error("getLangData: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function userActions(Request $request) {
        try {
            $return = ['status' => 'fail', 'msg' => 'Unable to get actions'];
            $currentUser = (Auth::user());
            $companyIds = CommonHelper::getSelectedCompanyIds();

            // Request Approvals
            $approvalsForUser = TicketApprovalRequest::where(['user_id' => $currentUser->id, 'approve_status' => 3])->pluck('pr_id')->toArray();
            $request_approval = TicketProcureRequest::select('tkt_procure_requests.*', 'dep.name as dep_name', 'pab.name as pab', 'status.name as status',
                    DB::raw('concat_ws(" ", creator.first_name, creator.last_name, "@", creator.username) as creator_name'),
                    DB::raw('case when pc.approval_required = 1 then "Required" when pc.approval_required = 0 then "Not Required "else "" end as approval_required'),
                    DB::raw('DATE_FORMAT(tkt_procure_requests.created_at, "%d %b %Y %h:%i %p") as created_at_format'),
                    DB::raw('DATE_FORMAT(tkt_procure_requests.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'),
                    DB::raw('DATE_FORMAT(tkt_procure_requests.approved_at, "%d %b %Y %h:%i %p") as approved_at_format')
                )
                ->leftJoin('departments as dep', 'tkt_procure_requests.department_id', '=', 'dep.id')
                ->leftJoin('tkt_problem_categories as pc', 'tkt_procure_requests.problem_category_id', '=', 'pc.id')
                ->leftJoin('users as creator', 'tkt_procure_requests.creator_id', '=', 'creator.id')
                ->leftJoin('tkt_ticket_pabs as pab', 'pc.pab_id', '=', 'pab.id')
                ->leftJoin('tkt_request_status as status', 'tkt_procure_requests.status_id', '=', 'status.id')
                ->leftJoin('companies as company','company.id','=','tkt_procure_requests.company_id')
                ->whereIn('tkt_procure_requests.id',$approvalsForUser)
                ->whereIn('tkt_procure_requests.company_id',$companyIds);
            $request_approval = $request_approval->get();
            $info['request_approval'] = $request_approval;

            // Procurement Approvals
            $approvals = ApprovalRequest::where([
                'user_id' => $currentUser->id,
                'approve_status' => 3
            ])->pluck('pr_id')->toArray();
            $procureReq = DB::table('procure_requests as pr');
            $procureReq->leftJoin('departments as dep', 'pr.department_id', '=', 'dep.id');
            $procureReq->leftJoin('departments as cc', 'pr.cost_center', '=', 'cc.id');
            $procureReq->leftJoin('users as creator', 'pr.creator_id', '=', 'creator.id');
            $procureReq->leftJoin('procure_statuses as status', 'pr.status_id', '=', 'status.id');
            $procureReq->leftJoin('procure_request_priorities as priority', 'pr.priority_id', '=', 'priority.id');
            $procureReq->leftJoin('procure_pabs as pab', 'pr.pab_id', '=', 'pab.id');
            $procureReq->leftJoin('procure_quotations as pq', function($q) {
                $q->on('pr.id', '=', 'pq.pr_id');
                $q->where('pq.po_approved_quotate', '=', '1');
            });
            $procureReq->leftJoin('users as incharge', 'pq.po_created_by', '=', 'incharge.id');

            $procureReq->select('pr.id', 'pr.title', 'pr.procure_tag', 'dep.name as dep_name', 'cc.name as cc_name', 'priority.name as priority', 'status.name as status', 'pab.name as pab');
            $procureReq->addSelect(DB::raw('concat_ws(" ", creator.first_name, creator.last_name, "@", creator.username) as creator_name'));
            $procureReq->addSelect(DB::raw('case when incharge.id is not null then concat_ws(" ", incharge.first_name, incharge.last_name, "@", incharge.username) else "" end as incharge_name'));
            $procureReq->addSelect(DB::raw('case when pr.approval_required = 1 then "Required" when pr.approval_required = "null" then "Not Required "else "" end as approval_required'));
            $procureReq->addSelect(DB::raw('DATE_FORMAT(pr.created_at, "%d %b %Y %h:%i %p") as created_at_format'));
            $procureReq->addSelect(DB::raw('DATE_FORMAT(pr.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
            $procureReq->addSelect(DB::raw('DATE_FORMAT(pr.deadline, "%d %b %Y") as deadline_format'));
            $procureReq = $procureReq->whereIn('pr.id', $approvals)->get();
            $info['procure_request_approval'] = $procureReq;

            // feedback i have to give
            $tickets = Ticket::whereIn('status_id' , [5])->where('creator_id' , Auth::user()->id)->whereNull('feedback')->orderBy('id','desc')->get();
            $info['ticket_waiting_for_rating'] = ($tickets);

            return $return = [
                'status' => 'success',
                'msg' => 'Action fetched successfully',
                'data' => $info
            ];

        } catch(\Exception $e) {
            Log::error("userActions : ".$e->getMessage());
            return $return;
        }
    }

    public function getModulesForRole() {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to get module'
            ];
            $con = new RolesPermissionController();
            $modules = $con->moduleDef;
            return $return = [
                'status' => 'success',
                'msg' => 'Module fetched successfully',
                "isTechnician" => Auth::user()->hasPermission("service_tickets"),
                'data' => $modules
            ];
        } catch(\Exception $e) {
            Log::error("getModulesForRole : ".$e->getMessage());
            return $return;
        }
    }

    public function getModulesPermissions($moduleId) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => 'Unable to get module permissions'
            ];
            $permissions = Permission::select('id','name')->where('module_id', $moduleId)->get();
            foreach($permissions as $perm) {
                $perm->status = (Auth::user()->hasPermissionTo($perm->name));
            }
            return $return = [
                'status' => 'success',
                'msg' => 'Permission fetched successfully',
                "isTechnician" => Auth::user()->hasPermission("service_tickets"),
                'data' => $permissions
            ];
        } catch(\Exception $e) {
            Log::error("getModulesPermissions : ".$e->getMessage());
            return $return;
        }
    }

    public function getDashboard(Request $request) {
        try {
            $adminData = $this->ajaxNewAdminDashboard();
            $userData = $this->ajaxNewDashboard();
            $version = UserDetails::where('user_id', Auth::user()->id)->first();
            $dashboardData = [
                'status' => 'success',
                'msg' => 'Database fetched successfully.',
                'overall_dashboard' => $adminData,
                'dashboard' => $userData,
                'miscelleneous' => null,
                'user_app_version' => !empty($version) ? $version->app_version : '',
            ];

            //status board
            $board_info = DB::select("select count(*) as tot_items, cast(sum(case when sbi.status = 1 then 1 else 0 end) as unsigned) as tot_active, cast(sum(case when sbi.status = 2 then 1 else 0 end)as unsigned) as tot_per_iss, cast(sum(case when sbi.status = 3 then 1 else 0 end) as unsigned) as tot_par_out, cast(sum(case when sbi.status = 4 then 1 else 0 end) as unsigned) as tot_major from status_board_items sbi JOIN status_board_groups sbg ON sbg.id = sbi.group_id Where sbi.is_enabled = 1 AND sbg.is_enabled = 1");

            if ($board_info[0]->tot_items > 0) {
                $board_info[0]->overall_per = intval(($board_info[0]->tot_active / $board_info[0]->tot_items) * 100);
            } else {
                $board_info[0]->overall_per = 0;
            }

            $dashboardData['miscelleneous']['board_info'] = $board_info[0];

            $dashboardData['miscelleneous']["isAssetsEnable"] = config("services.assets.enabled") ? config("services.assets.enabled") : "0";
            $dashboardData['miscelleneous']["isTicketEnable"] = config("services.service_ticket.enabled") ? config("services.service_ticket.enabled") : "0";
            $dashboardData['miscelleneous']["isStatusBoardEnable"] = config("services.status_board.enabled") ? config("services.status_board.enabled") : "0";
            $dashboardData['miscelleneous']["isKDEnable"] = config("services.knowledge_document.enabled") ? config("services.knowledge_document.enabled") : "0";
            $dashboardData['miscelleneous']["androidVersion"] = 25;
            $dashboardData['miscelleneous']["iosVersion"] = 20;
            return response()->json($dashboardData);
        } catch(\Exception $e) {
            Log::info("getDashboard:" . $e->getMessage());
            $dashboardData = ['status' => 'fail', 'msg' => 'Unable to fetch data.'];
            return response()->json($dashboardData);
        }
    }

    public function ajaxNewDashboard() {

        $return = [];

        $user_id = Auth::user()->id;
        //total checked out device of loggedin user
        $total_checkout_devices = DB::select("select count(a.id) as tot from assets as a where a.assigned_for = 1 and a.assigned_to =  ".$user_id."" );
        $data["total_checkout_devices"] = $total_checkout_devices[0]->tot;

        //total checked out accessories of loggedin user
        $total_checkout_accessories = DB::select("SELECT COUNT(au.id) AS tot
        FROM accessories_users AS au
        JOIN accessories AS acc ON au.accessory_id = acc.id
        JOIN assets AS a ON au.assigned_to = a.id
        WHERE acc.deleted_at IS null and au.assigned_for = 1 AND au.assigned_to = ".$user_id." OR a.assigned_for = 1 AND a.assigned_to = ".$user_id."" );
        $data["total_checkout_accessories"] = $total_checkout_accessories[0]->tot;

        //total checked out Licenses  of loggedin user
        $hideDeviceViaLicenses = Settings::getSettings()->isHideDeviceViaLicenses();
        $checkout_info_sql = 'case when a.assigned_for = 1 then "via Device" when ls.assigned_to ="'.$user_id.'" then  "Direct Checkout" else "" end';
        if($hideDeviceViaLicenses) {
            $checkout_info_sql = 'case when ls.assigned_to ="'.$user_id.'" then  "Direct Checkout" else "" end';
        }

        $db = DB::table('license_seats as ls');
        $db->leftJoin('licenses as l', function($q) {
            $q->on('l.id', '=', 'ls.license_id');
            $q->where('l.deleted_at');
        });

        if( ! $hideDeviceViaLicenses ) {
            $db->leftJoin('assets as a', 'a.id', '=', 'ls.asset_id');
        }

        $db->whereNull('ls.deleted_at');
        $db->where(function($q) use($user_id,$hideDeviceViaLicenses) {
            if($hideDeviceViaLicenses) {
                $q->where('ls.assigned_to', '=', $user_id);
            }
            else {
                $q->where('a.assigned_for', '=', 1)->where('a.assigned_to', '=', $user_id)->orWhere('ls.assigned_to', '=', $user_id);
            }
        });
        $data["total_checkout_license"] = $db->count();

        //total checked out components of loggedin user
        $total_checkout_components = DB::select("SELECT COUNT(c.id) AS tot
        FROM components AS c
        JOIN assets AS a ON a.id = c.checked_out_to
        WHERE c.deleted_at IS null and a.assigned_for = 1 AND a.assigned_to = ".$user_id."");
        $data["total_checkout_components"] = $total_checkout_components[0]->tot;


        //Recently Checkout and Checkin records loggedin user

        $chk_rec = DB::table('asset_logs as al');
        $chk_rec->leftJoin('users as adm', 'adm.id', '=', 'al.user_id');
        $chk_rec->leftJoin('assets as d', 'd.id', '=', 'al.asset_id');
        $chk_rec->leftJoin('accessories as a', 'a.id', '=', 'al.accessory_id');
        $chk_rec->leftJoin('consumables as c', 'c.id', '=', 'al.consumable_id');
        $chk_rec->leftJoin('licenses as l', 'l.id', '=', 'al.asset_id');
        $chk_rec->leftJoin('models as mdl', 'mdl.id', '=', 'd.model_id');
        $chk_rec->leftJoin('projects as pr', 'pr.id', '=', 'al.project_id');
        $chk_rec->where('al.checkedout_to', '=', $user_id);
        $chk_rec->whereNull('al.filename');
        $chk_rec->select('al.id', 'al.action_type', 'al.asset_type', 'd.id as device_id', 'd.asset_tag as asset_tag','c.id as con_tag','a.name as accessory_name','l.id as lic_tag','l.name as lic_name','d.deleted_at as asset_delete','a.deleted_at as acc_delete','l.deleted_at as lic_delete','c.deleted_at as con_delete');
        $chk_rec->addSelect(DB::raw('case when l.id is not null then concat_ws("","LIC",l.id) else "" end as lic_batch_no'));
        if (config("app.client") == "etherealmachines") {
            $chk_rec->addSelect(DB::raw('concat("","AC",a.id) as acc_tag'));
        } else {
            $chk_rec->addSelect(DB::raw('concat("","A",a.id) as acc_tag'));
        }
        $chk_rec->addSelect(DB::raw('DATE_FORMAT(al.updated_at, "%d %b %Y %h:%i %p") as checkin_checkout_date'));
        $chk_rec->OrderBy('al.updated_at','desc');
        if($chk_rec->count() != 0) {
            $data["last_checkout_checkin_records"] = $chk_rec->take(10)->get();
        } else {
            $data["last_checkout_checkin_records"] = null;
        }


        $ticket_info = DB::select('SELECT count(id) as total_tickets, CAST(sum(case when status_id in (5,6) then 1 else 0 end)as UNSIGNED) as total_resolved, CAST(sum(case when status_id = 1 then 1 else 0 end) as unsigned) as total_open, CAST(sum(case when status_id = 2 then 1 else 0 end) as unsigned) as total_reopen, CAST(sum(case when status_id = 6 then 1 else 0 end) as unsigned) as total_closed, cast(sum(case when status_id not in (5,6) then 1 else 0 end) as unsigned) as total_not_resolved, cast(sum(case when priority_id = 1 then 1 else 0 end) as unsigned ) as total_critical, cast(sum(case when priority_id = 2 then 1 else 0 end) as unsigned) as total_high, cast(sum(case when priority_id = 3 then 1 else 0 end) as unsigned) as total_medium, cast(sum(case when priority_id = 4 then 1 else 0 end) as unsigned) as total_low FROM `tkt_tickets` WHERE deleted_at is null and creator_id = '. Auth::user()->id . ' and is_temp is null and status_id != 10');

        if($ticket_info[0]->total_tickets > 0) {
            $ticket_info[0]->overall_progress_percentage = intval(($ticket_info[0]->total_resolved / $ticket_info[0]->total_tickets) * 100);
        } else {
            $ticket_info[0]->overall_progress_percentage = null;
        }

        $total_resolved_tickets = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t Where t.creator_id = ".Auth::user()->id." AND t.status_id = 5 AND t.is_temp IS NULL AND t.deleted_at IS NULL");
        $ticket_info[0]->my_total_resolved_tickets = $total_resolved_tickets[0]->tot;

        $data['ticket_info'] = $ticket_info[0];

        $db = TicketProcureRequest::select('tkt_procure_requests.*');
        $db->Join('tkt_approval_request as ar', function ($join) {
            $join->on('tkt_procure_requests.id', 'ar.pr_id')
            ->where(function ($query) {
                $query->where('ar.user_id', Auth::user()->id)
                ->orWhere('ar.delegated_user_id', Auth::user()->id);
            });
        });
        $db->whereNull('tkt_procure_requests.deleted_at');
        $dataMyApprovalsInfo = $db->count();
        // $db->where('tkt_procure_requests.creator_id', Auth::user()->id);
        $dataMyrequest = $db->count();
        $data['service_request_info'] = [
            'my_approvals' => $dataMyrequest
        ];

        //Recently updated tickets loggedin user
        $rcnt_tkt = DB::table('tkt_tickets as t');
        $rcnt_tkt->leftJoin('departments as dep', 't.department_id', '=', 'dep.id');
        $rcnt_tkt->leftJoin('tkt_statuses as s', 't.status_id', '=', 's.id');
        $rcnt_tkt->leftJoin('tkt_priorities as p', 't.priority_id', '=', 'p.id');
        $rcnt_tkt->leftJoin('users as u', 't.creator_id', '=', 'u.id'); // ticket raiser
        $rcnt_tkt->leftJoin('users as cu', 't.created_by', '=', 'cu.id'); // who actually created ticket
        $rcnt_tkt->where(function($q) use($user_id) {
            $q->whereRaw('t.creator_id = ' . $user_id . ' or find_in_set(' . $user_id . ', t.merged_tkt_creators)');
        });
        $rcnt_tkt->whereNull("t.is_temp");
        $rcnt_tkt->whereNull("t.deleted_at");
        $rcnt_tkt->select('t.id', 't.subject', 's.name as status', 't.status_id', 'p.name as priority');
        $rcnt_tkt->addSelect(DB::raw('concat(u.first_name, " ", u.last_name, " @ ", u.username) as creator_name'));
        $rcnt_tkt->addSelect(DB::raw('DATE_FORMAT(t.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
        $rcnt_tkt->OrderBy('t.updated_at','desc');

        if($rcnt_tkt->count() != 0){
            $data["recently_updated_tickets"] = $rcnt_tkt->take(15)->get();
        }else{
            $data["recently_updated_tickets"] = null;
        }


        //my ratings
        $ratingMyTickets = Ticket::select(
            DB::raw('cast(sum(tkt_tickets.feedback) as unsigned) as rating_given'),
            DB::raw('cast(round(avg(tkt_tickets.feedback),1) as unsigned) as overall_rating'),
            DB::raw('max(tkt_tickets.feedback) as highest_rating'),
            DB::raw('min(tkt_tickets.feedback) as lowest_rating')
        )
            ->where('tkt_tickets.creator_id', Auth()->user()->id)
            ->whereNull('tkt_tickets.is_temp');
        $datamyTicketsRatingInfo = $ratingMyTickets->first();

        $data['user_ticket_rating_info'] = ($datamyTicketsRatingInfo);

        //technician ratings
        $techRatingMyTickets = Ticket::select(
            DB::raw('cast(sum(tkt_tickets.feedback) as unsigned) as rating_given'),
            DB::raw('cast(round(avg(tkt_tickets.feedback),1) as unsigned) as overall_rating'),
            DB::raw('max(tkt_tickets.feedback) as highest_rating'),
            DB::raw('min(tkt_tickets.feedback) as lowest_rating')
        )
            ->where('tkt_tickets.assigned_to', Auth()->user()->id)
            ->whereNull('tkt_tickets.is_temp');
        $dataTechTicketsRatingInfo = $techRatingMyTickets->first();
        $data['tech_ticket_rating_info'] = ($dataTechTicketsRatingInfo);

        //feedback i have to give
        $tickets = Ticket::whereIn('status_id', [5])->where('creator_id', Auth::user()->id)->whereNull('feedback')->orderBy('id','desc')->get();

        $data['ticket_waiting_for_rating'] = ($tickets);

        $return = $data;

        return ($return);

    }

    public function ajaxNewAdminDashboard() {
        $return = [];
        $companyIds=CommonHelper::getSelectedCompanyIds();
        $user_id = Auth::user()->id;
        $now = Carbon::now(config('app.timezone'));
        $date = new stdClass;
        $date->now = Carbon::now(config('app.timezone'));
        $date->startFrom = Carbon::now('Asia/Kolkata')->subMonth(4)->startOfMonth();
        $date->lastMonth = Carbon::now('Asia/Kolkata')->subMonth(1)->startOfMonth();

        $date->yesterday = Carbon::now('Asia/Kolkata')->yesterday();
        $date->last_7_days = Carbon::now('Asia/Kolkata')->subDays(7)->startOfDay();
        $date->last_quarter_start = Carbon::now('Asia/Kolkata')->subMonth(5)->startOfMonth();
        $date->last_quarter = Carbon::now('Asia/Kolkata')->subMonth(3)->endOfMonth();
        $date->lastMonth = Carbon::now('Asia/Kolkata')->subMonth(1)->startOfMonth();
        /* to get the quarter */
        $date1 = Carbon::now(config('app.timezone'))->firstOfQuarter();
        $date2 = Carbon::now(config('app.timezone'))->lastOfQuarter();

        $locationQuery = '';
        $locationQueryR = '';
        $locationQueryT = '';
        $locationQuerySR = '';
        $locations = Location::get()->pluck('id');
        if(!empty($locations)){
            $locationId = implode(",", $locations->toArray());
            $locationIdArray = $locations->toArray();
            $locationQuery = ' and tkt_tickets.location_id IN('.$locationId.') ';
            $locationQueryR = ' and r.location_id IN('.$locationId.') ';
            $locationQueryT = ' and t.location_id IN('.$locationId.') ';
            $locationQuerySR = ' and ( t.location_id IN(' . $locationId . ') '.' or t.location_id is null)';
        }

        $today = $now->format('Y-m-d');
        $tkt_config = Config::first();
        $get_user_privileges = Privilege::select('department_id')->where('user_id', '=', Auth::user()->id)->get();
        $user_privileged_departments = [];
        if(count($get_user_privileges)) {
            $user_privileged_departments = $get_user_privileges->pluck('department_id');
            $arr = $user_privileged_departments->toArray();
            $usr_prv_dep = implode(',', $arr);
        } else {
            $usr_prv_dep = 0;
        }
        $data = null;
        if(Auth::user()->hasPermission("service_tickets")) {

            $adminRole = Auth::user()->hasAnyRole(['SuperAdmin', 'Admin']);

            $assigneToCondition = !$adminRole ? " AND t.assigned_to = ".Auth::user()->id : "";
            $plainassigneToCondition = !$adminRole ? " AND assigned_to = ".Auth::user()->id : "";

            //total open Tickets
            $total_open_tickets = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id IN (".$usr_prv_dep.") $assigneToCondition $locationQueryT  AND t.status_id = 1 AND t.is_temp IS NULL AND t.deleted_at IS NULL");
            $data["total_open_tickets"] = $total_open_tickets[0]->tot;

            // total Reopen Tickets
            $total_reopen_tickets = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id IN (".$usr_prv_dep.") $assigneToCondition $locationQueryT  AND t.status_id = 2 AND t.is_temp IS NULL AND t.deleted_at IS NULL");
            $data["total_reopen_tickets"] = $total_reopen_tickets[0]->tot;
            
            //my total resolved Tickets
            $total_resolved_tickets = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t Where t.creator_id = ".Auth::user()->id." AND t.status_id = 5 AND t.is_temp IS NULL AND t.deleted_at IS NULL");
            $data["my_total_resolved_tickets"] = $total_resolved_tickets[0]->tot;

            //total resolved Tickets today
            $total_resolved_tickets_by_today = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id IN (".$usr_prv_dep.")  $assigneToCondition $locationQueryT  AND t.status_id = 5 AND t.updated_at = '".$today."' AND t.is_temp IS NULL AND t.deleted_at IS NULL");
            $data["total_resolved_tickets_by_today"] = $total_resolved_tickets_by_today[0]->tot;

            //total Tickets on hold
            $total_tickets_onhold = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id IN (".$usr_prv_dep.")  $assigneToCondition $locationQueryT  AND t.status_id = 4 AND t.is_temp IS NULL AND t.deleted_at IS NULL");
            $data["total_tickets_onhold"] = $total_tickets_onhold[0]->tot;

            //total Tickets on hold
            $total_tickets_waiting_for_first_response = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id = '".$tkt_config->default_department_id."'   $assigneToCondition $locationQueryT AND t.status_id != 10 AND t.deleted_at IS NULL AND t.is_temp IS NULL");
            $data["total_tickets_waiting_for_first_response"] = $total_tickets_waiting_for_first_response[0]->tot;

            // Total Not Assigned Tickets
            $total_not_assigned_tickets = DB::select("SELECT COUNT(t.id) AS tot FROM tkt_tickets AS t JOIN departments AS d ON t.department_id = d.id WHERE t.department_id IN (".$usr_prv_dep.") AND t.status_id = 1 AND t.assigned_to IS NULL  $assigneToCondition $locationQueryT  AND t.is_temp IS NULL AND t.deleted_at IS NULL");
            $data["total_not_assigned_tickets"] = $total_not_assigned_tickets[0]->tot;


            // Breached Ticket count
            $myAssignedTicketsBreachedInfo = Ticket::select('tkt_tickets.id', 'tkt_tickets.subject', 'tkt_tickets.department_id', 'tkt_tickets.assigned_to', 'tkt_tickets.status_id', 'tkt_tickets.created_at', 'tkt_tickets.updated_at', 'ts.name as status', 'tp.name as priority', 'tkt_tickets.tat_expire', 'tkt_tickets.tat_remaining_mins', DB::raw('DATE_FORMAT(tkt_tickets.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'))
                ->leftJoin('tkt_statuses as ts', 'ts.id', 'tkt_tickets.status_id')
                ->leftJoin('tkt_priorities as tp', 'tp.id', 'tkt_tickets.priority_id');
            if(isset($locationIdArray)) {
                $myAssignedTicketsBreachedInfo->whereIn('tkt_tickets.location_id', $locationIdArray);
            }
            $tat_halt_enable = Status::where('tat_halt', 1)->get()->pluck('id');
            $myAssignedTicketsBreachedInfo->where(function($query) use($tat_halt_enable) {
                $query->whereIn('tkt_tickets.status_id', $tat_halt_enable)->where('tkt_tickets.tat_remaining_mins', '<=', 0);
                $tat_halt_disable = Status::where('tat_halt', 0)->get()->pluck('id');
                $query->orWhere(function($query) use($tat_halt_disable) {
                    $query->whereIn('tkt_tickets.status_id', $tat_halt_disable)->where('tkt_tickets.tat_expire', '<', now());
                });
            });
            if(!$adminRole){
                $myAssignedTicketsBreachedInfo->where('assigned_to', Auth::user()->id);
            }
            $myAssignedTicketsBreachedInfo->whereIn('department_id', $user_privileged_departments);
            $myAssignedTicketsBreachedInfo->whereNull('tkt_tickets.is_temp');
            $myAssignedTicketsBreachedInfo->where("tkt_tickets.status_id", "!=", 10);
            $slaBreachedCount = $myAssignedTicketsBreachedInfo->orderBy('id', 'desc')->count();


            $db = TicketProcureRequest::select('tkt_procure_requests.*');
            $db->Join('tkt_approval_request as ar', function ($join) {
                $join->on('tkt_procure_requests.id', 'ar.pr_id')
                ->where(function ($query) {
                    $query->where('ar.user_id', Auth::user()->id)
                    ->orWhere('ar.delegated_user_id', Auth::user()->id);
                });
            });
            $db->whereNull('tkt_procure_requests.deleted_at');
            $dataMyApprovalsInfo = $db->count();
            $dataMyrequest = $db->count();
            $data['service_request_info'] = [
                'my_approvals' => $dataMyrequest
            ];

            $ticket_info = DB::select('SELECT count(id) as total_tickets, CAST(sum(case when status_id in (5) then 1 else 0 end)as UNSIGNED) as total_resolved, CAST(sum(case when assigned_to is null AND status_id != 6 then 1 else 0 end) as unsigned) as total_unassigned, CAST(sum(case when status_id = 1 then 1 else 0 end) as unsigned) as total_waiting_for_response, CAST(sum(case when status_id = 6 then 1 else 0 end) as unsigned) as total_closed, cast(sum(case when status_id not in (5,6) then 1 else 0 end) as unsigned) as total_not_resolved, cast(sum(case when priority_id = 1 then 1 else 0 end) as unsigned ) as total_critical, cast(sum(case when priority_id = 2 then 1 else 0 end) as unsigned) as total_high, cast(sum(case when priority_id = 3 then 1 else 0 end) as unsigned) as total_medium, cast(sum(case when priority_id = 4 then 1 else 0 end) as unsigned) as total_low FROM tkt_tickets WHERE department_id IN ('.$usr_prv_dep.') '.$plainassigneToCondition.' '.$locationQuery.' and deleted_at is null and is_temp is null and status_id != 10');

            if($ticket_info[0]->total_tickets > 0) {
                $ticket_info[0]->total_closed = $ticket_info[0]->total_closed;
                $ticket_info[0]->overall_progress_percentage = intval(($ticket_info[0]->total_resolved / $ticket_info[0]->total_tickets) * 100);
            } else {
                $ticket_info[0]->overall_progress_percentage = null;
            }
            $ticket_info[0]->sla_breached = $slaBreachedCount;
            $data['ticket_info'] = $ticket_info[0];

            // My tickets login user
            // $myTickets = Ticket::select(DB::raw('COUNT(id) AS tot'))->whereNull("tkt_tickets.is_temp")->where('creator_id', $user_id)->orWhereRaw("find_in_set($user_id, merged_tkt_creators)")->get();
            // $data["my_tickets"] = $myTickets[0];

            // Assigned Tickets login user
            $myTickets = Ticket::select(DB::raw('COUNT(id) AS tot'))->whereNull("tkt_tickets.is_temp")->where('assigned_to', $user_id)->where("status_id", "!=", 6)->get();
            $data['ticket_info']->assigned_tickets = $myTickets[0]->tot;

            //Recently updated tickets loggedin user
            $rcnt_tkt = DB::table('tkt_tickets as t');
            $rcnt_tkt->leftJoin('departments as dep', 't.department_id', '=', 'dep.id');
            $rcnt_tkt->leftJoin('tkt_statuses as s', 't.status_id', '=', 's.id');
            $rcnt_tkt->leftJoin('tkt_priorities as p', 't.priority_id', '=', 'p.id');
            $rcnt_tkt->leftJoin('users as u', 't.creator_id', '=', 'u.id'); // ticket raiser
            $rcnt_tkt->leftJoin('users as cu', 't.created_by', '=', 'cu.id'); // who actually created ticket
            // $rcnt_tkt->where(function($q) use($user_id) {
            //     $q->whereRaw('t.creator_id = ' . $user_id . ' or find_in_set(' . $user_id . ', t.merged_tkt_creators)');
            // });
            if(!$adminRole) {
                $rcnt_tkt->where('t.assigned_to', Auth::user()->id);
            }
            if(isset($locationIdArray)) {
                $rcnt_tkt->whereIn('t.location_id', $locationIdArray);
            }
            $rcnt_tkt->whereNull("t.is_temp");
            $rcnt_tkt->whereNull("t.deleted_at");
            $rcnt_tkt->select('t.id', 't.subject', 't.assigned_to', 's.name as status', 't.status_id', 'p.name as priority', 't.spam');
            $rcnt_tkt->addSelect(DB::raw('concat(u.first_name, " ", u.last_name, " @ ", u.username) as creator_name'));
            $rcnt_tkt->addSelect(DB::raw('DATE_FORMAT(t.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
            $rcnt_tkt->OrderBy('t.updated_at','desc');

            if($rcnt_tkt->count() != 0) {
                $data["recently_updated_tickets"] = $rcnt_tkt->take(15)->get();
                foreach($data["recently_updated_tickets"] as $v) {
                    $v->color_code = CommonHelper::getTicketColorCode($v);
                }
            } else {
                $data["recently_updated_tickets"] = null;
            }

            //feedback Counters
            if(Auth::user()->hasAnyRole(['SuperAdmin', 'Admin'])) {
                $query3 = "select  b.last7days_sum, b.this_year_sum , b.yesterday_sum, b.today_sum, b.this_month_sum, b.last3months_sum from (SELECT SUM(CASE WHEN YEAR(t.updated_at) = " . $date->now->year . " THEN feedback ELSE 0 END) AS this_year_sum, SUM(CASE WHEN DATE(t.updated_at) >= '" . $date->last_7_days->toDateString() . "' THEN feedback ELSE 0 END) AS last7days_sum,  SUM(CASE WHEN DATE(t.updated_at) = '" . $date->yesterday->toDateString() . "' THEN feedback ELSE 0 END) AS yesterday_sum, SUM(CASE WHEN DATE(t.updated_at) = '" . $date->now->toDateString() . "' THEN feedback ELSE 0 END) AS today_sum,  SUM(CASE WHEN YEAR(t.updated_at) = " . $date->now->year . " AND MONTH(t.updated_at) = " . $date->now->month . " THEN feedback ELSE 0 END) AS this_month_sum, SUM(CASE WHEN (DATE(t.updated_at) >= '" . $date1->toDateString() . "' AND DATE(t.updated_at) <= '" . $date2->toDateString() . "') THEN feedback ELSE 0 END) AS last3months_sum FROM tkt_tickets AS t WHERE t.deleted_at IS NULL and t.is_temp is null and t.department_id in (".$usr_prv_dep.") and t.feedback is not null and t.status_id in (5,6)) as b";
                // $query3 = "select count(feedback), sum(feedback), round((sum(feedback)/count(feedback))) as overall from tkt_tickets where feedback is not null and status_id in (5,6)";
                $showAllTicketsInfo = true;
            }
            elseif(Auth::user()->hasPermission("service_tickets")) {
                $query3 = "select b.last7days_sum, b.this_year_sum, b.yesterday_sum, b.today_sum, b.this_month_sum, b.last3months_sum from (SELECT SUM(CASE WHEN YEAR(t.updated_at) = " . $date->now->year . " THEN feedback ELSE 0 END) AS this_year_sum, SUM(CASE WHEN DATE(t.updated_at) >= '" . $date->last_7_days->toDateString() . "' THEN feedback ELSE 0 END) AS last7days_sum,  SUM(CASE WHEN DATE(t.updated_at) = '" . $date->yesterday->toDateString() . "' THEN feedback ELSE 0 END) AS yesterday_sum, SUM(CASE WHEN DATE(t.updated_at) = '" . $date->now->toDateString() . "' THEN feedback ELSE 0 END) AS today_sum,  SUM(CASE WHEN YEAR(t.updated_at) = " . $date->now->year . " AND MONTH(t.updated_at) = " . $date->now->month . " THEN feedback ELSE 0 END) AS this_month_sum,  SUM(CASE WHEN (DATE(t.updated_at) >= '" . $date1->toDateString() . "' AND DATE(t.updated_at) <= '" . $date2->toDateString() . "') THEN feedback ELSE 0 END) AS last3months_sum FROM tkt_tickets AS t WHERE t.deleted_at IS NULL and t.is_temp is null and t.department_id IN ($usr_prv_dep) and t.assigned_to = ".Auth::user()->id." and t.feedback is not null and t.status_id in (5,6)) as b";
                // $query3 = "select count(feedback), sum(feedback), round((sum(feedback)/count(feedback))) as overall from tkt_tickets where feedback is not null and status_id in (5,6)";
                $showAllTicketsInfo = true;
            }
            if($query3) {
                $handler_feed_back_count = DB::select($query3);
                if(count($handler_feed_back_count)) {
                    $last7daysCount = Ticket::whereNull('deleted_at')->whereNull('is_temp')->whereIn('department_id',$user_privileged_departments)->whereNotNull('feedback')->whereIn('status_id', [5,6])->where('updated_at',">=",$date->last_7_days->toDateString())->count();
                    $thisYearCount = Ticket::whereRaw("Year(updated_at) >= ".$date->now->year)
                        ->whereIn('department_id',$user_privileged_departments)
                        ->whereIn('status_id', [5,6])
                        ->whereNotNull('feedback');
                    /*if(Auth::user()->hasPermission("service_tickets")){
                        $thisYearCount->where('assigned_to',Auth::user()->id);
                    }*/
                    $thisYearCount->whereNull('deleted_at')
                        ->whereNull('is_temp');
                    $thisYearCount = $thisYearCount->count();
                    $yestedayCount = Ticket::where("updated_at",">=",$date->yesterday->format("Y-m-d 00:00:00"))
                        ->where("updated_at","<=",$date->yesterday->toDateString()." 23:59:59")
                        ->whereIn('department_id',$user_privileged_departments);
                    if(Auth::user()->hasPermission("service_tickets")){
                        $yestedayCount->where('assigned_to',Auth::user()->id);
                    }
                    $yestedayCount->whereIn('status_id', [5,6])
                        ->whereNotNull('feedback')
                        ->whereNull('deleted_at')
                        ->whereNull('is_temp');
                    $yestedayCount = $yestedayCount->count();
                    $todayCount = Ticket::where("updated_at",">=",$date->now->format("Y-m-d 00:00:00"))
                        ->where("updated_at","<=",$date->now->toDateString()." 23:59:59")
                        ->whereIn('department_id',$user_privileged_departments);
                    if(Auth::user()->hasPermission("service_tickets")){
                        $todayCount->where('assigned_to',Auth::user()->id);
                    }
                    $todayCount->whereIn('status_id', [5, 6])
                        ->whereNotNull('feedback')
                        ->whereNull('deleted_at')
                        ->whereNull('is_temp');
                    $todayCount = $todayCount->count();
                    $thisMonthCount = Ticket::whereRaw("Year(updated_at) >= ".$date->now->year)
                        ->whereRaw("Month(updated_at) >= ".$date->now->month);
                    $thisMonthCount->whereIn('department_id',$user_privileged_departments);
                    if(Auth::user()->hasPermission("service_tickets")){
                        $thisMonthCount->where('assigned_to',Auth::user()->id);
                    }
                    $thisMonthCount->whereIn('status_id', [5, 6])
                        ->whereNotNull('feedback')
                        ->whereNull('deleted_at')
                        ->whereNull('is_temp');
                    $thisMonthCount = $thisMonthCount->count();
                    $lastThreeMonthCount = Ticket::where("updated_at",">=",$date1->toDateString())
                        ->where("updated_at","<=",$date2->toDateString())
                        ->whereIn('department_id',$user_privileged_departments);
                    if(Auth::user()->hasPermission("service_tickets")){
                        $lastThreeMonthCount->where('assigned_to',Auth::user()->id);
                    }
                    $lastThreeMonthCount->whereIn('status_id', [5, 6])
                        ->whereNotNull('feedback')
                        ->whereNull('deleted_at')
                        ->whereNull('is_temp');
                    $lastThreeMonthCount = $lastThreeMonthCount->count();
                    $handler_feed_back_count[0]->last7days = round($last7daysCount != 0 ? ($handler_feed_back_count[0]->last7days_sum/$last7daysCount) : 0);
                    $handler_feed_back_count[0]->last_7_days_ticket_Count = $last7daysCount != 0 ? $last7daysCount : 0;
                    $handler_feed_back_count[0]->this_year = round($thisYearCount != 0 ? ($handler_feed_back_count[0]->this_year_sum/$thisYearCount) : 0);
                    $handler_feed_back_count[0]->this_year_ticket_count = $thisYearCount != 0 ? ($thisYearCount) : 0;
                    $handler_feed_back_count[0]->yesterday = round($yestedayCount != 0 ? ($handler_feed_back_count[0]->yesterday_sum/$yestedayCount) : 0);
                    $handler_feed_back_count[0]->yesterday_ticket_count = $yestedayCount != 0 ? $yestedayCount: 0;
                    $handler_feed_back_count[0]->today = round($todayCount != 0 ? ($handler_feed_back_count[0]->today_sum/$todayCount) : 0);
                    $handler_feed_back_count[0]->today_ticket_count = $todayCount != 0 ? $todayCount : 0;
                    $handler_feed_back_count[0]->this_month = round($thisMonthCount != 0 ? ($handler_feed_back_count[0]->this_month_sum/$thisMonthCount) : 0);
                    $handler_feed_back_count[0]->this_month_ticket_count = $thisMonthCount != 0 ? $thisMonthCount: 0;
                    $handler_feed_back_count[0]->last3months = round($lastThreeMonthCount != 0 ? ($handler_feed_back_count[0]->last3months_sum/$lastThreeMonthCount) : 0);
                    $handler_feed_back_count[0]->last_3_month_ticket_count = $lastThreeMonthCount != 0 ? $lastThreeMonthCount : 0;
                }
            }
            $data['handler_feed_back_count'] = isset($handler_feed_back_count[0]) ? $handler_feed_back_count[0] : null;

            // service request details counters start
            if (Auth::user()->isSuperUser()) {
                $queryRequests = "select count(t.id) as tot,cast(sum(case when t.status_id = 2 then 1 else 0 end) as unsigned) as 'waiting_for_the_approval',cast(sum(case when t.status_id = 3 then 1 else 0 end) as unsigned) as 'approval',cast(sum(case when t.status_id = 4 then 1 else 0 end) as unsigned) as 'reject' from tkt_procure_requests as t where t.department_id in (" . $usr_prv_dep . ") and t.deleted_at is null";

            } elseif (Auth::user()->hasPermission("service_tickets")) {
                $queryRequests = "select count(t.id) as tot,cast(sum(case when t.status_id = 2 then 1 else 0 end) as unsigned) as 'waiting_for_the_approval',cast(sum(case when t.status_id = 3 then 1 else 0 end) as unsigned) as 'approval',cast(sum(case when t.status_id = 4 then 1 else 0 end) as unsigned) as 'reject' from tkt_procure_requests as t where t.department_id in (" . $usr_prv_dep . ") and t.deleted_at is null;";
            }
            $data['service_request_count'] = null;
            if ($queryRequests) {
                $handler_service_request_counts = DB::select($queryRequests);
                if (count($handler_service_request_counts)) {
                    $data['service_request_count'] = $handler_service_request_counts[0];
                }
            }

            //feedback i have to give
            $tickets = Ticket::whereIn('status_id' , [5])->where('creator_id' , Auth::user()->id)->whereNull('feedback')->orderBy('id','desc')->get();
            $data['ticket_waiting_for_rating'] = ($tickets);

        }
        //my service request count
        if (Auth::user()->isSuperUser()) {
            $myqueryRequests = "select count(t.id) as tot,cast(sum(case when t.status_id = 2 then 1 else 0 end) as unsigned) as 'waiting_for_the_approval',cast(sum(case when t.status_id = 3 then 1 else 0 end) as unsigned) as 'approval',cast(sum(case when t.status_id = 4 then 1 else 0 end) as unsigned) as 'reject' from tkt_procure_requests as t where t.department_id in (" . $usr_prv_dep . ") and t.creator_id = $user_id and t.deleted_at is null";
        } elseif (Auth::user()->hasPermission("service_tickets")) {
            $myqueryRequests = "select count(t.id) as tot,cast(sum(case when t.status_id = 2 then 1 else 0 end) as unsigned) as 'waiting_for_the_approval',cast(sum(case when t.status_id = 3 then 1 else 0 end) as unsigned) as 'approval',cast(sum(case when t.status_id = 4 then 1 else 0 end) as unsigned) as 'reject' from tkt_procure_requests as t where t.department_id in (" . $usr_prv_dep . ") and t.creator_id = $user_id and t.deleted_at is null;";
        }
        $data['my_service_request_count'] = null;
        if ($myqueryRequests) {
            $my_service_request_counts = DB::select($myqueryRequests);
            if (count($my_service_request_counts)) {
                $data['my_service_request_count'] = $my_service_request_counts[0];
            }
        }

        if(Auth::user()->hasPermissionTo('DeviceRead')) {

            $userDepartmentComponents = $userDepartmentAccessConsumable = $userLocationComponents = $userLocationAccessConsumable = $locationArray = $userLocationAccess = $userLocationAccessAccessories = $userDepartmentAccess = $userDepartmentAccessAccessories = $userLocationAccessLicense = $userDepartmentAccessLicense = "";

            $loc_previllage = Auth::user()->permitted_locations;
            $permitted_loc = explode(",", $loc_previllage);
            $settings = Settings::getSettings();
            if($settings->location_config == 1) {
                if(empty($loc_previllage)) {
                    $userLocationAccess = ' and assets.rtd_location_id = 0';
                    $userLocationAccessLicense = ' and l.location_id = 0';
                    $userLocationAccessAccessories = ' and accessories.location_id = 0';
                    $userLocationAccessConsumable = ' and consumables.location_id = 0';
                    $userLocationComponents = ' and components.location_id = 0';
                } else {
                    $userLocationAccess = ' and assets.rtd_location_id IN('.$loc_previllage.')';
                    $userLocationAccessLicense = ' and l.location_id IN('.$loc_previllage.')';
                    $userLocationAccessAccessories = ' and accessories.location_id IN('.$loc_previllage.')';
                    $userLocationAccessConsumable = ' and consumables.location_id IN('.$loc_previllage.')';
                    $userLocationComponents = ' and components.location_id IN('.$loc_previllage.')';
                }
            }

            if($settings->department_config == 1) {
                $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
                if(empty($asset_dept_permission)) {
                    $userDepartmentAccess = ' and assets.department_id = 0';
                    $userDepartmentAccessLicense = ' and l.department_id = 0';
                    $userDepartmentAccessAccessories = ' and accessories.department_id = 0';
                    $userDepartmentAccessConsumable = ' and consumables.department_id = 0';
                    $userDepartmentComponents = ' and components.department_id = 0';
                } else {
                    $userDepartmentAccess = ' and assets.department_id IN('.$asset_dept_permission.')';
                    $userDepartmentAccessLicense = ' and l.department_id IN('.$asset_dept_permission.')';
                    $userDepartmentAccessAccessories = ' and accessories.department_id IN('.$asset_dept_permission.')';
                    $userDepartmentAccessConsumable = ' and consumables.department_id IN('.$asset_dept_permission.')';
                    $userDepartmentComponents = ' and components.department_id IN('.$asset_dept_permission.')';
                }
            }

            $company_id = Company::first()->id;
            // total assets
            $total_devices = DB::select('select count(id) as tot from assets where deleted_at is null '.$userLocationAccess.' '.$userDepartmentAccess.' and company_id = '.$company_id );
            $data["total_assets"] = $total_devices[0]->tot;

            // assets ready to deploy
            $available_devices = DB::select("SELECT count(assets.id) as tot from assets join status_labels as s on s.id = assets.status_id
            where assets.status_id = 1 and assets.deleted_at is null and s.deleted_at is null ".$userLocationAccess." ".$userDepartmentAccess." and company_id = ".$company_id);
            $data["asset_ready_to_deploy"] = $available_devices[0]->tot;

            // total licenses
            $total_licenses = DB::select("select count(ls.id) as tot
            from license_seats as ls join licenses as l on ls.license_id = l.id where ls.deleted_at is null and l.deleted_at is null and company_id = " . $company_id.$userLocationAccessLicense." ".$userDepartmentAccessLicense);
            $data["total_licenses"] = $total_licenses[0]->tot;

            // total assets in inventory
            $total_inventory = DB::select("select count(*) as tot from itm_network_inventory_basic as nw JOIN assets ON nw.BIOSSerialNumber = assets.serial where nw.is_dupe is null and assets.deleted_at is null and nw.company = " . $company_id . " ".$userLocationAccess." ".$userDepartmentAccess." ");
            $data["assets_in_inventory"] = $total_inventory[0]->tot;

            //total accessory quantity
            $total_accessory = DB::select("SELECT SUM(qty) AS tot FROM accessories WHERE deleted_at IS NULL AND company_id = " . $company_id . " ".$userLocationAccessAccessories." ".$userDepartmentAccessAccessories);
            $data["total_accessory_quantity"] = (int) $total_accessory[0]->tot;

            //total Accessory
            $total_accessory = DB::select("SELECT COUNT(id) AS tot FROM accessories WHERE deleted_at IS NULL AND company_id = " . $company_id . " ".$userLocationAccessAccessories." ".$userDepartmentAccessAccessories);
            $data["total_accessory"] = (int) $total_accessory[0]->tot;

            //available Accessory
            $avail_accessory = DB::select("SELECT SUM(CASE WHEN co.tot_checkouts IS NOT NULL THEN (accessories.qty - (accessories.scrap_qty + co.tot_checkouts) ) when accessories.scrap_qty > 0 then accessories.qty - accessories.scrap_qty else accessories.qty END) AS tot FROM accessories  LEFT JOIN (SELECT cu.accessory_id, COUNT(cu.id) AS tot_checkouts FROM accessories_users AS cu where cu.assigned_to is not null GROUP BY cu.accessory_id) AS co ON accessories.id = co.accessory_id WHERE accessories.deleted_at IS NULL and accessories.company_id = " . $company_id . " ".$userLocationAccessAccessories." ".$userDepartmentAccessAccessories);
            $data["avail_accessory"] = (int) $avail_accessory[0]->tot;
                
            //total consumables
            $total_consumables = DB::select("SELECT SUM(qty) AS tot FROM consumables WHERE deleted_at IS NULL and company_id = " . $company_id . " ".$userLocationAccessConsumable." ".$userDepartmentAccessConsumable);
            $data["total_consumables"] = (int) $total_consumables[0]->tot;

            // available consumables
            $used_consumables = DB::select("SELECT SUM(b.used) AS tot FROM (SELECT s.consumable_id, count(s.id) AS used FROM consumables_users AS s join consumables ON consumables.id = s.consumable_id WHERE consumables.deleted_at IS NULL AND consumables.company_id = " . $company_id . " ".$userLocationAccessConsumable." ".$userDepartmentAccessConsumable." group BY s.consumable_id) AS b");
            $data["avail_consumables"] = $data["total_consumables"] - (int) $used_consumables[0]->tot;

            // Total Component
            $total_component = DB::select("SELECT count(id) AS tot FROM components WHERE deleted_at IS NULL AND company_id = " . $company_id . " ".$userLocationComponents." ".$userDepartmentComponents);
            $data["total_component"] = (int) $total_component[0]->tot;

            // Recently Updated kd fetch
            $kd =  Document::select(
                'knowledge_document.id',
                DB::raw('LEFT(REGEXP_REPLACE(knowledge_document.title, "<[^>]*>", ""), 50) as title'),
                'knowledge_document.created_at',
                'knowledge_document_category.category_name',
                DB::raw('LEFT(REGEXP_REPLACE(knowledge_document.content, "<[^>]*>", ""), 50) as content'),
                'knowledge_document.updated_at',
                'knowledge_document.card_img',
                'knowledge_document.tags',
                'dep.name as dep_name',
                'knowledge_document.status',
                'knowledge_document.created_by',
                'knowledge_document.starred',
                'sub_cat.category_name as sub_cat',
                DB::raw('DATE_FORMAT(knowledge_document.updated_at, "%d %b %Y %h:%i %p") as updated_at_format')
            )
                ->leftJoin('departments as dep', 'knowledge_document.department_id', '=', 'dep.id')
                ->leftJoin('companies as comp', 'dep.company_id', '=', 'comp.id')
                ->leftJoin('knowledge_document_category', 'knowledge_document.parent_category_id', '=', 'knowledge_document_category.id')
                ->leftJoin('knowledge_document_category as sub_cat', 'knowledge_document.sub_category_id', '=', 'sub_cat.id')->OrderBy('knowledge_document.updated_at','desc');
            if($kd->count() != 0) {
                $data["recently_updated_kd"]["kd"] = $kd->take(15)->get();
            } else {
                $data["recently_updated_kd"]["kd"] = [];
            }
        }
        $return = $data;
        return ($return);
    }
    public function formatWhatsAppMessage($message) {
        $message = preg_replace_callback('/<a href="([^"]+)".*?>(.*?)<\/a>/', function ($matches) {
            return "{$matches[2]} ({$matches[1]})";
        }, $message);
        $message = preg_replace('/<b>(.*?)<\/b>|<strong>(.*?)<\/strong>/', '*$1$2*', $message);
        $message = preg_replace('/<i>(.*?)<\/i>|<em>(.*?)<\/em>/', '_$1$2_', $message);
        $message = preg_replace('/<br\s*\/?>/', "\n", $message);
        $message = strip_tags($message);
        return trim($message);
    }

    public function handleWAWithBotpressFLow(Request $request) {
        if ($request->isMethod('get')) {
            $verify_token = "123456";
            if ($request->get('hub_verify_token') === $verify_token) {
                return response($request->get('hub_challenge'));
            }
            return response('Verification token mismatch', 403);
        }
        $data = $request->all();
        Log::info(json_encode($data));
        if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
            $message = $data['entry'][0]['changes'][0]['value']['messages'][0];
            $user_id = $message['from'];
            $cleanNumber = preg_replace('/^91/', '', $user_id);
            $user = User::where('phone', $cleanNumber)->orwhere('phone2', $cleanNumber)->orwhere('work_phone', $cleanNumber)->first(); //set preferred latest login user
            if(!$user){
                $message = "Hi\nMy Name is MATI - Machine Automation & Transformation Intelligence\n\nLooks like you are not registered with us, we would love to have you onboard but for now please contact support team to verify your details before I can assist you\n\nGood Bye!\nITM Support Team";
                return $this->sendWhatsAppText($user_id, $message);
            }
            if(!empty($user->access_token)) {
                $user_access_token = $user->access_token;
            } else {
                $user_access_token = rand(2,10) . date('mis');
                $user->update(['access_token' => $user_access_token]);
            }
            $isNormaluser = ($user->hasAnyRole(['SuperAdmin', 'Admin']) || $user->hasPermission("service_tickets")) ? 0 : 1;
            $text = $this->extractMessage($data);
            $botName = "GIB";
            $botId = 'itm-rp';
            $botDetails = CommonHelper::getBotId();
            Log::info("User message: " . json_encode($botDetails));
            if(empty($message)) {
                Log::error("Blank message goes here");
                return response('Message not available from bot', 500);
            }
            if (isset($message['interactive']['list_reply']['id'])) {
                $selectedOption = $message['interactive']['list_reply']['id'];
                if (strpos($selectedOption, 'NEXT_PAGE_') === 0) {
                    $nextPage = (int) str_replace('NEXT_PAGE_', '', $selectedOption);
                    return $this->sendWhatsAppList($user_id, ['text' => 'Choose an option:'], $nextPage);
                }
            }
            if ($text === 'BOTPRESS.FILE_UPLOAD') {
                return $this->sendWhatsAppText($user_id, "Please upload the file now. And type Done when you are done.");
            }
            if ($text === 'USER_UPLOADED_FILE') {
                $array = [
                    'userAccessToken' => $user_access_token,
                    'userId' => $user->id,
                    'botName' => $botDetails["botName"],
                    'botId' => $botDetails["botId"],
                    'baseUrl' => config('app.url')."api",
                    'client' => config('app.client'),
                    'isNormalUser' => $isNormaluser,
                    'userName' => $user->fullName(),
                    'userLocation' => isset($user->userDetails->seat_no) ? $user->userDetails->seat_no : null,
                    'alternate_location' => isset($user->seat_no) ? $user->seat_no : null,
                ];
                return $this->processUserFileUpload($data, $array);
            }

            $phone_number_id = config('app.whatsapp_notification_messageId');
            $botpressResponse = Http::post("https://ibot.greenitco.com/api/v1/bots/{$botDetails["botId"]}/converse/{$user_id}", [
                'type' => 'text',
                'text' => $text,
                'metadata' => [
                    'userAccessToken' => $user_access_token,
                    'userId' => $user->id,
                    'isNormalUser' => $isNormaluser,
                    'baseUrl' => config('app.url')."api",
                    'botName' => $botDetails['botName'],
                    'client' => config('app.client'),
                    'userName' => $user->fullName(),
                    'userLocation' => isset($user->userDetails->seat_no) ? $user->userDetails->seat_no : null,
                    'alternate_location' => isset($user->seat_no) ? $user->seat_no : null,
                ]
            ]);
            Log::error("Bot response ". json_encode($botpressResponse));
            $data = $botpressResponse->json();
            $response = $botpressResponse['responses'][0] ?? null;
            if (!$response) return response()->json(['error' => 'No response from Botpress'], 400);
    
            // Handle response from Botpress
            foreach ($botpressResponse['responses'] as $response) {
                $this->handleBotpressResponse($user_id, $response);
            }
        }
    
        return response()->json(['status' => 'success']);
    }
    

    public function processUserFileUpload($data, $array) {
        if (isset($data['entry'][0]['changes'][0]['value']['messages'][0]['document']['id'])) {
            $media_id = $data['entry'][0]['changes'][0]['value']['messages'][0]['document']['id'];
            $mime_type = $data['entry'][0]['changes'][0]['value']['messages'][0]['document']['mime_type'];
            $file_name = $data['entry'][0]['changes'][0]['value']['messages'][0]['document']['filename'] ?? 'file_' . time();
        } elseif (isset($data['entry'][0]['changes'][0]['value']['messages'][0]['image']['id'])) {
            $media_id = $data['entry'][0]['changes'][0]['value']['messages'][0]['image']['id'];
            $mime_type = "image/jpeg"; 
            $file_name = 'image_' . time() . '.jpg';
        } else {
            return response()->json(['error' => 'No file found'], 400);
        }
        $media_url_response = Http::withToken(config('app.whatsapp_notification_bearer_token'))->get("https://graph.facebook.com/v22.0/{$media_id}");
        if (!$media_url_response->successful()) {
            Log::error("Failed to get media URL: " . $media_url_response->body());
            return response()->json(['error' => 'Failed to get media URL'], 500);
        }
        $media_url = $media_url_response->json()['url'];
        $file_content = Http::withToken(config('app.whatsapp_notification_bearer_token'))->get($media_url)->body();
        $public_file_path = public_path("uploads/{$file_name}");
        if (!file_exists(public_path('uploads'))) {
            mkdir(public_path('uploads'), 0777, true);
        }
        file_put_contents($public_file_path, $file_content);
        Log::info("File saved successfully in public folder:". json_encode($array));
        $public_url = URL::to('uploads/' . $file_name);
        $botpressResponse = Http::post("https://ibot.greenitco.com/api/v1/bots/{$array['botId']}/converse/{$array['userId']}", [
            'type' => 'text', 
            'text' => "BOTPRESS.FILE_UPLOAD",
            'metadata' => [
                'mime_type' => $mime_type,
                'media_url' => $public_url,
                'userAccessToken' => $array['userAccessToken'],
                'userId' => $array["userId"],
                'isNormalUser' => $array["isNormalUser"],
                'baseUrl' => config('app.url')."api",
                'botName' => $array['botName'],
                'client' => config('app.client'),
                'userName' => $array['userName'],
                'userLocation' => $array['userLocation'],
                'alternate_location' => $array['alternate_location'],
                'nextnode' => true
            ]
        ]);
        Log::info("File URL sent to Botpress: " . json_encode($botpressResponse->json()));
        return $this->sendWhatsAppText($array["userId"], "Your file has been uploaded successfully.");
    }
    
    public function extractMessage($data) {
        Log::error("Button response : ". json_encode($data));
        if (isset($data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'])) {
            return $data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
        } elseif (isset($data['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id'])) {
            return $data['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id'];
        } elseif (isset($data['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id'])) {
            return $data['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id'];
        } elseif (isset($data['entry'][0]['changes'][0]['value']['messages'][0]['image']['id']) ||
                isset($data['entry'][0]['changes'][0]['value']['messages'][0]['document']['id']) ||
                isset($data['entry'][0]['changes'][0]['value']['messages'][0]['video']['id'])) {
            return "USER_UPLOADED_FILE";
        }
        return null;
    }

    public function handleBotpressResponse($to, $response) {
        Log::info("Handling Botpress Response: " . json_encode($response));
        switch ($response['type']) {
            case 'text':
                return $this->sendWhatsAppText($to, $response['text']);
            case 'single-choice':
                if (count($response['choices']) > 3) {
                    return $this->sendWhatsAppList($to, $response);
                } else {
                    return $this->sendWhatsAppButtons($to, $response);
                }
            case 'multiple-choice':
                return $this->sendWhatsAppList($to, $response);
            default:
                return response()->json(['error' => 'Unsupported response type'], 400);
        }
    }

    public function sendWhatsAppText($to, $message) {
        $message = $this->formatWhatsAppMessage($message);
        return Http::post("https://graph.facebook.com/v22.0/".config('app.whatsapp_notification_messageId')."/messages", [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $message],
            'access_token' => config('app.whatsapp_notification_bearer_token')
        ]);
    }

    public function sendWhatsAppButtons($to, $response) {
        if (!isset($response['choices']) || count($response['choices']) === 0) {
            return $this->sendWhatsAppText($to, "No options available.");
        }
        $buttons = array_map(function ($choice) {
            return [
                'type' => 'reply',
                'reply' => [
                    'id' => $choice['value'],
                    'title' => $choice['title']
                ]
            ];
        }, array_slice($response['choices'], 0, 3));
        return Http::post("https://graph.facebook.com/v22.0/".config('app.whatsapp_notification_messageId')."/messages", [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => ['text' => $this->formatWhatsAppMessage($response['text'])], // Bot message
                'action' => ['buttons' => $buttons]
            ],
            'access_token' => config('app.whatsapp_notification_bearer_token')
        ]);
    }

    public function sendWhatsAppList($to, $response, $page = 1) {
        $maxRows = 9;
        $cacheKey = "wa_choices_" . $to;
        if ($page === 1) {
            Cache::put($cacheKey, $response['choices'], now()->addMinutes(1));
        }
        $choices = Cache::get($cacheKey, []);        
        $offset = ($page - 1) * $maxRows;
        $totalChoices = count($choices);
        $pagedChoices = array_slice($choices, $offset, $maxRows);
        $rows = array_map(function ($choice) {
            return [
                'id' => (string) $choice['value'],
                'title' => mb_substr($choice['title'], 0, 24),
                'description' => ''
            ];
        }, $pagedChoices);
        if ($offset + $maxRows < $totalChoices) {
            $rows[] = [
                'id' => 'NEXT_PAGE_' . ($page + 1),
                'title' => 'Load More ➡️',
                'description' => ''
            ];
        }
        $messageText = $response['text'] ?? "Please select an option:";    
        return Http::post("https://graph.facebook.com/v22.0/" . config('app.whatsapp_notification_messageId') . "/messages", [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $this->formatWhatsAppMessage($messageText)],
                'action' => [
                    'button' => 'Select an option',
                    'sections' => [
                        [
                            'title' => 'Options',
                            'rows' => $rows
                        ]
                    ]
                ]
            ],
            'access_token' => config('app.whatsapp_notification_bearer_token')
        ]);
        Log::error("WhatsApp Response: " . json_encode($response->json()));
        return $response;
    }

    public function getMediaUrl($mediaId) {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('app.whatsapp_notification_bearer_token')
        ])->get("https://graph.facebook.com/v22.0/{$mediaId}");
        return $response->json()['url'] ?? '';
    }

    public function handleTeamsWithBotpressFLow() {
        $incoming = $request->all();

        $userId = $incoming['from']['id'] ?? 'anonymous';
        $messageText = $incoming['text'] ?? '';

        // 2. Send message to Botpress
        $botpressResponse = Http::post('https://ibot.greenitco.com/api/v1/bots/itm-rp/converse/' . $userId . '/events', [
            'type' => 'text',
            'text' => $messageText,
            'channel' => 'msteams',
        ]);

        // 3. Prepare reply back to Teams
        $botMessages = $botpressResponse->json();

        $reply = [
            "type" => "message",
            "text" => $botMessages['responses'][0]['payload']['text'] ?? 'Sorry, I didn\'t understand that.',
        ];

        return response()->json($reply);
    }
    
    public static function existsInDropdown($controllerMethod, $params, $matchKey, $matchValue)
    {
        $page = 1;
        if (is_array($controllerMethod) && is_string($controllerMethod[0])) {
            $controllerMethod[0] = app($controllerMethod[0]);
        }
        do {
            $params['page'] = $page;
            $params['search'] = $params['search'] ?? '';
            $request = new Request($params);

            $response = call_user_func($controllerMethod, $request);
            $data = json_decode($response->getContent(), true);

            if (!empty($data['results'])) {
                $values = collect($data['results'])->pluck($matchKey)->toArray();
                if (in_array($matchValue, $values)) {
                    return true;
                }
            }

            $hasMore = $data['pagination']['more'] ?? false;
            $page++;

        } while ($hasMore);

        return false;
    }
    public function moduleEnableCheck()
   {
       try {
            $services = [
                'service_ticket' => [
                    'enabled' => config('services.service_ticket.enabled', false) == 1 ? "1" : "0"
                ],
                'assets' => [
                    'enabled' => config('services.assets.enabled', false) == 1 ? "1" : "0"
                ],
                'network_inventory' => [
                    'enabled' => config('services.network_inventory.enabled', false) == 1 ? "1" : "0"
                ],
                'procurement_module' => [
                    'enabled' => config('services.procurement_module.enabled', false) == 1 ? "1" : "0"
                ],
                'change_module' => [
                    'enabled' => config('services.change_module.enabled', false) == 1 ? "1" : "0"
                ],
                'task_module' => [
                    'enabled' => config('services.task_module.enabled', false) == 1 ? "1" : "0"
                ],
                'knowledge_document' => [
                    'enabled' => config('services.knowledge_document.enabled', false) == 1 ? "1" : "0"
                ],
                'live_monitor' => [
                    'enabled' => config('services.live_monitor.enabled', false) == 1 ? "1" : "0"
                ],
                'patch_management' => [
                    'enabled' => config('services.patch_management.enabled', false) == 1 ? "1" : "0"
                ],
                'powerbi_report' => [
                    'enabled' => config('services.powerbi_report.enabled', false) == 1 ? "1" : "0"
                ],
                'mailroom_management' => [
                    'enabled' => config('services.mailroom_management.enabled', false) == 1 ? "1" : "0"
                ],
                'status_board' => [
                    'enabled' => config('services.status_board.enabled', false) == 1 ? "1" : "0"
                ],
                'multiple_company' => [
                    'enabled' => config('services.multiple_company.enabled', false) == 1 ? "1" : "0"
                ],
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Module status retrieved successfully',
                'data' => $services,
            ]);
            
        } catch (\Throwable $th) {
            Log::error('serviceCheck', [
                'message' => $th->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong'
            ]);
        }
    }
}

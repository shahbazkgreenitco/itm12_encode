<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\Company;
use App\Models\Department;
use App\Models\UserGroup;
use App\Models\Group;
use App\Models\Device;
use App\Models\AccessoryUser;
use App\Models\LicenseSeat;
use App\Models\Settings;
use App\Models\UserLog;
use DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Validator;
use Illuminate\Support\Facades\Auth;
use Exception;
use Log;
use Mail;
use App\Helpers\Common as CommonHelper;
use App\Http\Controllers\Auth\Api\Ticket\IndexController;
use App\Mail\UserCredentialNotification;
use Illuminate\Validation\Rule;
use App\Models\UserDetails;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\ProblemCategory;
use PgSql\Lob;
use Carbon\Carbon;
use App\Models\Ticket\Config;
use App\Mail\Ticket\StatusChanged;
use App\Models\ChangeManagement\ApprovalRequest;
use App\Models\ChangeManagement\CabMember;
use App\Models\ChangeManagement\Record;
use App\Models\Ticket\TicketPabMember;
use App\Models\Ticket\TicketProcureRequest;
use App\Models\Ticket\TktAutoAllocationGroupMember;
use App\Models\TktCompanyPreviledge;
use App\Models\TktFollowing;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller {

    public function ajaxHistory(Request $request) {
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:0',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];
        $messages = [  ];

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

        $fields = array(
            "1" => "al.created_at",
            "2" => "adm_full_name",
            "3" => "al.action_type",
            "4" => "al.asset_type",
            "5" => "al.note",
        );
        $order = [
            0 => 'asc',
            1 => 'desc'
        ];


        $db = DB::table('asset_logs as al');
        $db->leftJoin('users as adm', 'adm.id', '=', 'al.user_id');
        $db->where('al.checkedout_to', '=', Auth::user()->id);
        $db->whereNull('al.filename');
        $db->select('al.id', 'al.action_type', 'al.asset_type', 'al.note');
        $db->addSelect(DB::raw('concat(adm.first_name, " ", adm.last_name) as adm_full_name'));
        $db->addSelect(DB::raw('DATE_FORMAT(al.created_at, "%d %b %Y %h:%i %p") as created_at_format'));

        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(al.action_type like "%%%1$s%%" or al.note like "%%%1$s%%" or al.asset_type like "%%%1$s%%" or concat(adm.first_name, " ", adm.last_name) like "%%%1$s%%" or DATE_FORMAT(al.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['filter_record'] = $db->count();
            $return['search_key'] = $search_key;
        }

        
        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return['data'] = array();
        foreach($data as $d) {
            // $return['data'][] = array('a' => $d);
            $return['data'][] = $d;
        }

        return response()->json($return);
    }

    public function ajaxAssignedDevices(Request $request) {
        if( ! Auth::user()->hasPermissionTo('DeviceRead') ) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'nullable|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:0|max:6',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];

        $order = [
            0 => 'asc',
            1 => 'desc'
        ];
        $messages = [  ];

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

        $columns = array(
            "1" => "a.asset_tag",
            "2" => "a.name",
            "3" => "mnf_name",
            "4" => "mdl_name",
            "5" => "a.serial",
            "6" => "a.last_checkout"
        );

        $db = DB::table("assets as a");
        $db->leftJoin('models as m', 'm.id', '=', 'a.model_id');
        $db->leftJoin('manufacturers as mnf', 'mnf.id', '=', 'm.manufacturer_id');

        $db->select('a.id','a.asset_tag','a.name','m.name as mdl_name', 'mnf.name as mnf_name', 'a.serial');
        $db->addSelect(DB::raw('case when a.last_checkout is not null then DATE_FORMAT(a.last_checkout, "%d %b %Y") else null end as last_checkout_format'));
        $db->addSelect('a.image', 'm.image_thumbnail');
        $db->where('a.assigned_for', '=', 1);
        $db->where('a.assigned_to','=', Auth::user()->id);

        if(Auth::user()->hasPermissionTo('DeviceRead')) {
            $loc_previllage = Auth::user()->permitted_locations; //getting user's table
            $settings = Settings::getSettings();
            $permitted_loc = explode(",", $loc_previllage);
            if($settings->location_config == 1) {
                if(empty($loc_previllage)) {
                    $db->where('a.rtd_location_id', '=', 0);
                } else {
                    $db->whereIn('a.rtd_location_id', $permitted_loc);
                }
            }
        }

        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(a.id like "%%%1$s%%" or a.asset_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or m.name like "%%%1$s%%" or mnf.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or case when a.last_checkout is not null then DATE_FORMAT(a.last_checkout, "%%d %%b %%Y") else null end like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['filter_record'] = $db->count();
            $return['search_key'] = $search_key;
        }
        $return['current_index'] = (int) $request->index;
        // if($return['filter_record'] > 0)
            $return['is_prev_index'] = $skip > 0 ? 1 : 0;//value will be 1 if records are available for previous page
        // $prevSkipVal = $skip - $take;
        // if ( $prevSkipVal == 0 || $prevSkipVal > 0 ) $return['is_prev_index'] = 1;
        // else if ( $prevSkipVal > $return['filter_record'] ) $return['is_prev_index'] = 0 ;
            
            $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;
        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $columns) ) {
            $db->orderBy($columns[$req["order_by"]], $order[$req["order_dir"]]);
        }



        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return['data'] = array();
        foreach($data as $d) {
            $d->device_img = CommonHelper::getDeviceImageByHirarchy($d->image, $d->image_thumbnail);
            // $d->device_img = ($d->image != "") ? url("uploads/devices") . "/" . $d->image : ($d->image_thumbnail != "" ? url("uploads/models") . "/" . $d->image_thumbnail : "");
            $return['data'][] = $d;
        }

        return response()->json($return);
    }

    public function ajaxAssignedLicenses(Request $request) {
       
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:0',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];
        $messages = [
        ];
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

        $fields = array(
            '1' => 'l.name',
            '2' => 'l.serial'
        );
        $order = [
            0=>'asc',
            1=>'desc'
        ];
        
        $db = DB::table('license_seats as ls');
        $db->join('licenses as l', function($q) {
            $q->on('l.id', '=', 'ls.license_id');
            $q->whereNull('l.deleted_at');
        });
        $db->leftJoin('manufacturers as m', 'l.manufacturer_id', '=', 'm.id');
        $db->where('ls.assigned_to','=', Auth::user()->id);
        $db->whereNull("ls.deleted_at");
        $db->select('l.name', 'ls.id', 'm.name as manufacturer', 'ls.license_id', 'l.serial');  
        $db->addSelect(DB::raw('case when l.id is not null then concat_ws("","LIC",l.id) else "" end as lic_batch_no')); 
        $db->addSelect(DB::raw('case when ls.serial is not null then ls.serial when ls.serial is null then l.serial else "" end as serial_no'));
        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(l.id like "%%%1$s%%" or l.name like "%%%1$s%%" or m.name like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['filter_record'] = $db->count();
            $return['search_key'] = $search_key;
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return['data'] = array();
        foreach($data as $d) {
            // $return['data'][] = array('a' => $d);
            $return['data'][] = $d;
        }

        return response()->json($return);
    }

    public function ajaxAssignedAccessories(Request $request) {
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:0',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];
        $messages = [        ];

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
            '1' => 'acc.name',
            '2' => 'au.expected_checkin'
        );
        $order = [
            0=>'asc',
            1=>'desc'
        ];

        $db = DB::table('accessories_users as au');
        $db->leftJoin('accessories as acc', 'acc.id', '=', 'au.accessory_id');
        $db->leftJoin('assets as a', function($q) {
            $q->on('a.id', '=', 'au.assigned_to');
            $q->where('au.assigned_for', 3);
        });
        $db->leftJoin('asset_logs as al', function($q) {
            $q->on('al.accessory_id', '=', 'au.accessory_id')
              ->on('al.id', '=', 'au.device_id')
              ->where('al.asset_type', '=', 'accessory')
              ->where('al.action_type', '=', 'checkout');
        });
        $db->whereNull("acc.deleted_at");
        
        $db->select('acc.id as id', 'acc.name', 'au.id as au_id');
        $db->addSelect(DB::raw('case when au.expected_checkin is not null then DATE_FORMAT(au.expected_checkin, "%d %b %Y") else "" end as expected_checkin_format'));

        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(acc.id like "%%%1$s%%" or acc.name like "%%%1$s%%" or (case when au.expected_checkin is not null then DATE_FORMAT(au.expected_checkin, "%%d %%b %%Y") else null end like "%%%1$s%%"))', $search_key);
            $db->whereRaw($whereStr);
            $return['filter_record'] = $db->count();
            $return['search_key'] = $search_key;
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return['data'] = array();
        foreach($data as $d) {
            // $return['data'][] = array('a' => $d);
            $return['data'][] = $d;
        }

        return response()->json($return);
    }
 
    public function ajaxAssignedConsumables(Request $request) {
        
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:0',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];
        $messages = [        ];

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

        $fields = array(
            '1' => 'con.name',
        );
        $order = [
            0=>'asc',
            1=>'desc'
        ];

        $db = DB::table('consumables_users as cu');
        $db->leftJoin('consumables as con', 'con.id', '=', 'cu.consumable_id');
        $db->leftJoin('manufacturers as m', 'l.manufacturer_id', '=', 'm.id');
        $db->select('con.id as con_id', 'con.name');
        $db->where( 'cu.assigned_to', '=', Auth::user()->id );
        $db->whereNull("con.deleted_at");
        $db->select('con.name', 'con.id', 'cu.id as cu_id','con.manufacturer_id as manufacture_id', 'm.name as manufacturer','con.unique_tag as unique_tag');

        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(con.id like "%%%1$s%%" orcon.name like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['filter_record'] = $db->count();
            $return['search_key'] = $search_key;
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return['data'] = array();
        foreach($data as $d) {
            // $return['data'][] = array('a' => $d);
            $return['data'][] = $d;
        }

        return response()->json($return);
    }

    public function ajaxAssignedComponents(Request $request) {
        
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:0',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];
        $messages = [        ];

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

        $fields = array(
            '1' => 'c.name',
            '2' => 'c.expected_checkin_at',
        );
        $order = [
            0=>'asc',
            1=>'desc'
        ];

        $db = DB::table('components as c');
        $db->Join('assets as a', 'a.id', '=', 'c.checked_out_to');
        $db->where('a.assigned_for', '=', 1);
        $db->where('a.assigned_to', '=', 1);
        $db->leftJoin('manufacturers as m', 'c.manufacturer_id', '=', 'm.id');
        $db->whereNull("c.deleted_at");
        
        $db->select('c.id as id', 'c.name', 'c.serial', 'm.name as manufacturer', 'c.manufacturer_id as manufacture_id');
        $db->addSelect(DB::raw('case when c.expected_checkin_at is not null then DATE_FORMAT(c.expected_checkin_at, "%d %b %Y") else "" end as expected_checkin_format'));

        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(c.id like "%%%1$s%%" or c.name like "%%%1$s%%" or (case when c.expected_checkin_at is not null then DATE_FORMAT(c.expected_checkin_at, "%d %b %Y") else "" end) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['filter_record'] = $db->count();
            $return['search_key'] = $search_key;
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['filter_record'] > ( $skip + $take ) ? 1 : 0;

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return['data'] = array();
        foreach($data as $d) {
            // $return['data'][] = array('a' => $d);
            $return['data'][] = $d;
        }

        return response()->json($return);
    }

    public function getCompanyUserList(){
        $return = ['status' => 'success'];
        $company_id = Auth::user()->company_id;
        $return['users'] = User::optsOfCompany($company_id);
        return response()->json($return);
    }

    /* v2 assigned devices */

    public function ajaxAssignedDevicesForApp(Request $request) {
        $return = [];

        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'nullable|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:1|max:7',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];

        $order = [
            0 => 'asc',
            1 => 'desc'
        ];
        $messages = [  ];

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
        $req["order_by"] = $request->order_by ? $request->order_by : '7';
        $req["order_dir"] = $request->order_dir == "0" ? $request->order_dir : '1';
        $columns = array(
            "1" => "a.asset_tag",
            "2" => "a.name",
            "3" => "mdl_name",
            "4" => "mnf_name",
            "5" => "a.serial",
            "6" => "project_name",
            "7" => "a.last_checkout"
        );

        $db = DB::table("assets as a");
        $db->leftJoin('models as m', 'm.id', '=', 'a.model_id');
        $db->leftJoin('manufacturers as mnf', 'mnf.id', '=', 'm.manufacturer_id');
        $db->leftJoin('projects as pr', 'pr.id', '=', 'a.last_checkout_project');

        $db->select('a.id','a.asset_tag','a.name','m.name as mdl_name', 'mnf.name as mnf_name', 'a.serial','a.model_id as model_id','mnf.id as manufacturer_id','pr.name as project_name');
        $db->addSelect('a.image','m.image_thumbnail');
        $db->addSelect(DB::raw('case when a.last_checkout is not null then DATE_FORMAT(a.last_checkout, "%d %b %Y") else null end as last_checkout_format'));
        $db->where('a.assigned_for',  1);
        $db->where('a.assigned_to', Auth::user()->id);

        $return['status'] = "success";
        $return['msg'] = "";
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];


        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(a.id like "%%%1$s%%" or pr.name like "%%%1$s%%" or a.asset_tag like "%%%1$s%%" or a.name like "%%%1$s%%" or m.name like "%%%1$s%%" or mnf.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or case when a.last_checkout is not null then DATE_FORMAT(a.last_checkout, "%%d %%b %%Y") else null end like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $columns) ) {
            $db->orderBy($columns[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['recordsFiltered'] > ( $skip + $take ) ? 1 : 0;

        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return['data'] = array();
        foreach($data as $v) {
            $v->device_img = ($v->image != "" && file_exists(public_path('/uploads/devices/'.$v->image))) ? url("uploads/devices") . "/" . $v->image : ($v->image_thumbnail != "" && file_exists(public_path('/uploads/models/'.$v->image_thumbnail)) ? url("uploads/models") . "/" . $v->image_thumbnail : "");
            $return['data'][] = $v;
        }
        return response()->json($return);
    }

    /* v2 assigned licenses */

    public function ajaxAssignedLicensesForApp(Request $request) {
        $return = [];
 
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'nullable|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:1|max:3',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];

        $order = [
            0 => 'asc',
            1 => 'desc'
        ];
        $messages = [  ];

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
        $req["order_by"] = $request->order_by ? $request->order_by : '3';
        $req["order_dir"] = $request->order_dir == "0" ? $request->order_dir : '1';


        $fields = array(
            '1' => 'l.name',
            '2' => 'serial_no',
            '3' => 'checkout_info',
            '4' => 'l.updated_at'
        );

        $hideDeviceViaLicenses = Settings::getSettings()->isHideDeviceViaLicenses();
        $checkout_info_sql = 'case when a.assigned_for = 1 then "via Device" when ls.assigned_to ="'.$request->user_id.'" then  "Direct Checkout" else "" end';
        if($hideDeviceViaLicenses) {
            $checkout_info_sql = 'case when ls.assigned_to ="'.$request->user_id.'" then  "Direct Checkout" else "" end';
        }
        
        $db = DB::table('license_seats as ls');
        $db->leftJoin('licenses as l', function($q) {
            $q->on('l.id', '=', 'ls.license_id');
            $q->where('l.deleted_at');
        });

        if( ! $hideDeviceViaLicenses ) {
            $db->leftJoin('assets as a', 'a.id', '=', 'ls.asset_id');
        }
        $db->leftJoin('manufacturers as m', 'l.manufacturer_id', '=', 'm.id');
        //$db->leftJoin('asset_logs as al', function($q) {
            //$q->on('al.asset_id', '=', 'ls.license_id');
            //$q->where('al.asset_type', '=', 'software')->where('al.action_type', '=', 'checkout');
        //});
        $db->whereNull('ls.deleted_at');
        $db->where(function($q) use($request, $hideDeviceViaLicenses) {
            if($hideDeviceViaLicenses) {
                $q->where('ls.assigned_to', '=',Auth::user()->id);
            }
            else {
                $q->where('a.assigned_for', '=', 1)->where('a.assigned_to',Auth::user()->id)->orWhere('ls.assigned_to', '=', Auth::user()->id);
            }
        });

        $db->select('l.name', 'ls.id', 'l.serial', 'ls.license_id','m.name as manufacturer','m.id as manufacturer_id');
        $db->addSelect(DB::raw('case when l.id is not null then concat_ws("","LIC",l.id) else "" end as lic_batch_no'));
        $db->addSelect(DB::raw('case when ls.serial is not null then ls.serial when ls.serial is null then l.serial else "" end as serial_no'));
        $db->addSelect(DB::raw($checkout_info_sql . ' as checkout_info'));

        $db->addSelect(DB::raw('DATE_FORMAT(ls.updated_at, "%d %b %Y %h:%i %p") as updated_at'));
        
        $return['status'] = "success";
        $return['msg'] = "";
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal']; 

        // if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
        //     if($hideDeviceViaLicenses) {
        //         $db->whereRaw('(case when l.id is not null then concat_ws("","LIC",l.id) else "" end) like "%' . $search_key .'%"  or l.name like "%' . $search_key . '%" or (case when ls.serial is not null then ls.serial when ls.serial is null then l.serial else "" end) like "%' . $search_key . '%"  or l.serial like "%' . $search_key . '%" or DATE_FORMAT(l.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%' . $search_key . '%"');
        //     }
        //     else {
        //         $db->whereRaw('(case when l.id is not null then concat_ws("","LIC",l.id) else "" end) like "%' . $search_key .'%"  or l.name like "%' . $search_key . '%" or (case when ls.serial is not null then ls.serial when ls.serial is null then l.serial else "" end) like "%' . $search_key . '%"  or l.serial like "%' . $search_key . '%" or (case when a.assigned_for = 1 then  "via Device"   when ls.assigned_to ="'.$request->user_id.'"  then  "Direct Checkout" else "" end) like "%' . $search_key . '%"  or DATE_FORMAT(l.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%' . $search_key . '%"');
        //     }

        //     $return['recordsFiltered'] = $db->count();
        // }

        if (isset($req["search_key"]) && $search_key = trim($req["search_key"])) {
            if ($hideDeviceViaLicenses) {
                $db->whereRaw("(case when l.id is not null then concat('LIC', l.id) else '' end like '%$search_key%' or l.name like '%$search_key%' or case when ls.serial is not null then ls.serial else l.serial end like '%$search_key%' or l.serial like '%$search_key%' or DATE_FORMAT(l.updated_at, '%d %b %Y %h:%i %p') like %'.$search_key.'%)");
            } else {
                $db->whereRaw("(case when l.id is not null then concat('LIC', l.id) else '' end like '%$search_key%' or l.name like '%$search_key%' or case when ls.serial is not null then ls.serial else l.serial end like '%$search_key%' or l.serial like '%$search_key%' or case when a.assigned_for = 1 then 'via Device' when ls.assigned_to = $request->user_id then 'Direct Checkout' else '' end like '%$search_key%' or DATE_FORMAT(l.updated_at, '%d %b %Y %h:%i %p') like '%$search_key%')");
            }

            $return['recordsFiltered'] = $db->count();
        }
 
        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['recordsFiltered'] > ( $skip + $take ) ? 1 : 0;

        $db->skip($skip);
        $db->take($take);

        $return["data"] = $db->get();
        return response()->json($return);
    }

    /* v2 assigned accessories */

    public function ajaxAssignedAccessoriesForApp(Request $request) {
        $return = [];
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'nullable|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:1|max:2',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];

        $order = [
            0 => 'asc',
            1 => 'desc'
        ];
        $messages = [  ];

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
        $req["order_by"] = $request->order_by ? $request->order_by : '2';
        $req["order_dir"] = $request->order_dir == "0" ? $request->order_dir : '1';

        $fields = array(
            '1' => 'acc_batch_no',
            '2' => 'au.expected_checkin',
            '3' => 'acc.updated_at'
        );

        $db = DB::table('accessories_users as au');
        $db->leftJoin('accessories as acc', 'acc.id', '=', 'au.accessory_id');
        $db->leftJoin('manufacturers as mnf', 'mnf.id', '=', 'acc.manufacturer_id');
        $db->leftJoin('assets as a', function($q) {
            $q->on('a.id', '=', 'au.assigned_to');
            $q->where('au.assigned_for', 3);
        });
        $db->leftJoin('asset_logs as al', function($q) {
            $q->on('al.accessory_id', '=', 'au.accessory_id')
            ->on('al.id', '=', 'au.device_id')
            ->where('al.asset_type', '=', 'accessory')
            ->where('al.action_type', '=', 'checkout');
        });
        $db->whereNull("acc.deleted_at");
        $db->where(function($q) use($request) {
            $q->where('au.assigned_for', '=', 1)->where('au.assigned_to', Auth::user()->id)->OrWhere('a.assigned_for', '=', 1)->where('a.assigned_to', Auth::user()->id);
        });
        $db->select('acc.id as id', 'acc.name', 'au.id as au_id','acc.batch_no as batch_no', 'acc.unique_tag as unique_tag','mnf.name as mnf_name', 'mnf.id as mnf_id');
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when acc.id is not null then concat_ws("","AC",acc.id) else "" end as acc_batch_no'));
        } else {
            $db->addSelect(DB::raw('case when acc.id is not null then concat_ws("","A",acc.id) else "" end as acc_batch_no'));
        }
        $db->addSelect(DB::raw('case when au.expected_checkin is not null then DATE_FORMAT(au.expected_checkin, "%d %b %Y") else "" end as expected_checkin_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(au.updated_at, "%d %b %Y %h:%i %p") as updated_at'));

        $return['status'] = "success";
        $return['msg'] = "";
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('((case when acc.id is not null then concat_ws("","AC",acc.id) else "" end) like "%%%1$s%%" or acc.name like "%%%1$s%%" or acc.batch_no like "%%%1$s%%" or (case when au.expected_checkin is not null then DATE_FORMAT(au.expected_checkin, "%%d %%b %%Y") else null end like "%%%1$s%%") or DATE_FORMAT(acc.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('((case when acc.id is not null then concat_ws("","A",acc.id) else "" end) like "%%%1$s%%" or acc.name like "%%%1$s%%" or acc.batch_no like "%%%1$s%%" or (case when au.expected_checkin is not null then DATE_FORMAT(au.expected_checkin, "%%d %%b %%Y") else null end like "%%%1$s%%") or DATE_FORMAT(acc.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['recordsFiltered'] > ( $skip + $take ) ? 1 : 0;

        $db->skip($skip);
        $db->take($take);

        $return["data"] = $db->get();
        return response()->json($return);
    }

    /* v2 assigned consumables */

    public function ajaxAssignedConsumablesForApp(Request $request) {
        $return = [];
        $userId  = Auth::user()->id;
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'nullable|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:1|max:1',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];

        $order = [
            0 => 'asc',
            1 => 'desc'
        ];
        $messages = [  ];

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
        $req["order_by"] = $request->order_by ? $request->order_by : '1';
        $req["order_dir"] = $request->order_dir == "0" ? $request->order_dir : '1';

        $fields = array(
            '1' => 'con.name',
            '2' => 'con.updated_at',
        );

        $db = DB::table('consumables_users as cu');
        $db->leftJoin('asset_logs as al', function($q) {
            $q->on('al.consumable_id', '=', 'cu.consumable_id');
            $q->on('al.id', '=', 'cu.asset_logs_id');
            $q->where('al.asset_type', '=', 'consumable');
            $q ->where('al.action_type', '=', 'checkout');
        });
        $db->leftJoin('assets as ass', function($q) {
            $q->on('ass.id', '=', 'cu.assigned_to')
            ->where('cu.assigned_for', '=', 3);
        });

        $db->leftJoin('places as pl', 'pl.id', '=', 'cu.assigned_to');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'pl.location_id');
        $db->leftJoin('users as u', function($q) {
            $q->on('u.id', '=', 'cu.assigned_to')
            ->where('cu.assigned_for', '=', 1);
        });
        $db->select('con.id as con_id', 'con.name');
        $db->select('con.name', 'con.id', 'cu.id as cu_id');
        $db->leftJoin('consumables as con', 'con.id', '=', 'cu.consumable_id');
        if (config("app.client") == "etherealmachines") {    
            $db->addSelect(DB::raw('case when con.id is not null then concat_ws("","CN",con.id) else "" end as con_batch_no'));
        } else { 
            $db->addSelect(DB::raw('case when con.id is not null then concat_ws("","CNS",con.id) else "" end as con_batch_no'));
        }
        $db->addSelect(DB::raw('DATE_FORMAT(cu.updated_at, "%d %b %Y %h:%i %p") as updated_at'));
        $db->where(function($q) use ($userId) {
            $q->where(function($q1) use ($userId) {
                $q1->where('cu.assigned_for', 1)
                ->where('cu.assigned_to', $userId);
            })
           ->orWhere(function($q2) use ($userId) {
            $q2->where('cu.assigned_for', 2)
            ->whereNotNull('loc.id')
            ->where('loc.location_user_id', $userId);
            })

            ->orWhere(function($q3) use ($userId) {
                $q3->where('cu.assigned_for', 3)
                ->whereNotNull('ass.id')
                ->where('ass.assigned_for', 1)
                ->where('ass.assigned_to', $userId);
            });
        });
        $db->whereNull("con.deleted_at");

        $return['status'] = "success";
        $return['msg'] = "";
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            if (config("app.client") == "etherealmachines") {    
                $whereStr = sprintf('(con.name like "%%%1$s%%" or (case when con.id is not null then concat_ws("","CN",con.id) else "" end) like "%%%1$s%%" or DATE_FORMAT(con.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            } else { 
                $whereStr = sprintf('(con.name like "%%%1$s%%" or (case when con.id is not null then concat_ws("","CNS",con.id) else "" end) like "%%%1$s%%" or DATE_FORMAT(con.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['recordsFiltered'] > ( $skip + $take ) ? 1 : 0;

        $db->skip($skip);
        $db->take($take);

        $return["data"] = $db->get();
        return response()->json($return);
    }

    /* v2 assigned components */

    public function ajaxAssignedComponentsForApp(Request $request) {
        $return = [];
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'nullable|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:1|max:2',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];

        $order = [
            0 => 'asc',
            1 => 'desc'
        ];
        $messages = [  ];

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
        $req["order_by"] = $request->order_by ? $request->order_by : '2';
        $req["order_dir"] = $request->order_dir == "0" ? $request->order_dir : '1';

        $fields = array(
            '1' => 'c.name',
            '2' => 'c.expected_checkin_at',
            '3' => 'c.updated_at'
        );


        $db = DB::table('components as c');
        $db->Join('assets as a', 'a.id', '=', 'c.checked_out_to');
        $db->where('a.assigned_for', '=', 1);
        $db->where('a.assigned_to', '=', Auth::user()->id);
        $db->whereNull("c.deleted_at");
        
        $db->select('c.id', 'c.name','c.unique_tag');
        $db->addSelect(DB::raw('case when c.expected_checkin_at is not null then DATE_FORMAT(c.expected_checkin_at, "%d %b %Y") else "" end as expected_checkin_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(c.updated_at, "%d %b %Y %h:%i %p") as updated_at'));

        $return['status'] = "success";
        $return['msg'] = "";
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(c.unique_tag like "%%%1$s%%" or c.name like "%%%1$s%%" or (case when c.expected_checkin_at is not null then DATE_FORMAT(c.expected_checkin_at, "%%d %%b %%Y") else null end like "%%%1$s%%") or DATE_FORMAT(c.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['recordsFiltered'] > ( $skip + $take ) ? 1 : 0;

        $db->skip($skip);
        $db->take($take);

        $return["data"] = $db->get();
        return response()->json($return);
    }

    /* v2 history */

    public function ajaxHistoryForApp(Request $request) {
        $return = [];
        
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'nullable|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:1|max:6',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];

        $order = [
            0 => 'asc',
            1 => 'desc'
        ];
        $messages = [  ];

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
        $req["order_by"] = $request->order_by ? $request->order_by : '1';
        $req["order_dir"] = $request->order_dir == "0" ? $request->order_dir : '1';

        $fields = array(
            '1' => 'al.updated_at',
            '2' => 'adm_full_name',
            '3' => 'al.action_type',
            '4' => 'al.asset_type',
            '5' => 'asset_tag',
            '6' => 'al.note',
        );
        
        $db = DB::table('asset_logs as al');
        $db->leftJoin('users as adm', 'adm.id', '=', 'al.user_id');
        $db->leftJoin('assets as d', 'd.id', '=', 'al.asset_id');
        $db->leftJoin('accessories as a', 'a.id', '=', 'al.accessory_id');
        $db->leftJoin('consumables as c', 'c.id', '=', 'al.consumable_id');
        $db->leftJoin('licenses as l', 'l.id', '=', 'al.asset_id');
        $db->leftJoin('models as mdl', 'mdl.id', '=', 'd.model_id');
        $db->leftJoin('projects as pr', 'pr.id', '=', 'al.project_id');
        $db->where('al.checkedout_to', '=', $request->user_id);
        $db->whereNull('al.filename');
        $db->select('al.id', 'al.action_type', 'al.asset_type','d.asset_tag as asset_tag','c.id as con_tag','mdl.name as device_model','a.name as accessory_name','c.name as consumable_name','al.note','adm.id as adminuserid','pr.name as project_name','l.id as lic_tag','l.name as lic_name');
        $db->addSelect(DB::raw('case when l.id is not null then concat_ws("","LIC",l.id) else "" end as lic_batch_no'));
        if (config("app.client") == "etherealmachines") {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CN",c.id) else "" end as con_batch_no'));
            $db->addSelect(DB::raw('concat("","AC",a.id) as acc_tag'));
        } else {
            $db->addSelect(DB::raw('case when c.id is not null then concat_ws("","CNS",c.id) else "" end as con_batch_no'));
            $db->addSelect(DB::raw('concat("","A",a.id) as acc_tag'));
        }
        $db->addSelect(DB::raw('concat(adm.first_name, " ", adm.last_name) as adm_full_name'));
        $db->addSelect(DB::raw('DATE_FORMAT(al.updated_at, "%d %b %Y %h:%i %p") as created_at_format'));

        $return['status'] = "success";
        $return['msg'] = "";
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];


        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            if (config("app.client") == "etherealmachines") {
                $whereStr = sprintf('(mdl.name like "%%%1$s%%" or concat("","AC",a.id) like "%%%1$s%%" or c.name like "%%%1$s%%" or d.name like "%%%1$s%%" or a.name like "%%%1$s%%" or d.asset_tag like "%%%1$s%%" or al.action_type like "%%%1$s%%" or (case when l.id is not null then concat_ws("","LIC",l.id) else "" end) like "%%%1$s%%" or (case when c.id is not null then concat_ws("","CN",c.id) else "" end) like "%%%1$s%%" or al.note like "%%%1$s%%" or al.asset_type like "%%%1$s%%" or concat(adm.first_name, " ", adm.last_name) like "%%%1$s%%" or DATE_FORMAT(al.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or l.name like "%%%1$s%%")', $search_key);
            } else {
                $whereStr = sprintf('(mdl.name like "%%%1$s%%" or concat("","A",a.id) like "%%%1$s%%" or c.name like "%%%1$s%%" or d.name like "%%%1$s%%" or a.name like "%%%1$s%%" or d.asset_tag like "%%%1$s%%" or al.action_type like "%%%1$s%%" or (case when l.id is not null then concat_ws("","LIC",l.id) else "" end) like "%%%1$s%%" or (case when c.id is not null then concat_ws("","CNS",c.id) else "" end) like "%%%1$s%%" or al.note like "%%%1$s%%" or al.asset_type like "%%%1$s%%" or concat(adm.first_name, " ", adm.last_name) like "%%%1$s%%" or DATE_FORMAT(al.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or l.name like "%%%1$s%%")', $search_key);
            }
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) ) {
            $db->orderBy($fields[$req["order_by"]], $order[$req["order_dir"]]);
        }

        $return['current_index'] = (int) $request->index;
        $return['is_prev_index'] = $skip > 0 ? 1 : 0;
        $return['is_next_index'] = $return['recordsFiltered'] > ( $skip + $take ) ? 1 : 0;

        $db->skip($skip);
        $db->take($take);

        $return["data"] = $db->get();
        return response()->json($return);
    }

    public function msLogin(Request $request) {
        try {
            $return = ['status' => 'fail',"msg"=>"Invalid Credentials","data"=>[]];

            $all = $request->all();
            $msUser = Socialite::driver('microsoft')->userFromToken($request->mstoken);

            $allowedDomains = ['ltts.com', 'greenitco.com'];
            $ms_email_address = $msUser["userPrincipalName"];
            $ms_email_parts = explode('@', $ms_email_address);

            if( is_array($ms_email_parts) == false || count($ms_email_parts) < 2 ) {
                throw new \Exception("E-mail not valid");
            }

            $ms_email_domain = array_pop($ms_email_parts);
            /*if( ! in_array($ms_email_domain, $allowedDomains) ) {
              throw new \Exception("L'account di posta elettronica non è nei domini consentiti");
            }*/
            if($msUser["userPrincipalName"] != "" && (stripos(trim($msUser["userPrincipalName"]), '#EXT#@LnttsGroup.onmicrosoft.com')) != false ) {
                $usrObj = User::where("username", "like", strtolower($msUser["userPrincipalName"]))->limit(1)->first();
            } else {
                $usrObj = User::where("email", "like", $msUser["userPrincipalName"])->limit(1)->first();
            }
            if(empty($usrObj)) {
                if(config('app.sub_client') == 'coe') {
                    $return["status"] = "danger";
                    $return["msg"] = "Username and Password are invalid";
                    return response()->json($return);
                }

                $usrObj = new User();
                $usrObj->email = $msUser["userPrincipalName"];
                $usrObj->first_name = strtolower($msUser["givenName"]);
                $usrObj->last_name = ($msUser["surname"] != "") ? strtolower($msUser["surname"]) : strtolower($msUser["surname"]);
                $usrObj->username = strtolower($msUser["displayName"]);
                $usrObj->company_id = Company::first()->id;
                // $usrObj->created_by = Auth::user()->id;
                $usrObj->phone = $msUser["mobilePhone"];
                $usrObj->jobtitle = $msUser["jobTitle"];
                $usrObj->create_mode = 4;
                $usrObj->job_type = 2;
                $usrObj->activated = 1;
                $usrObj->setPassword(str_random(8));

                if (!$usrObj->save())
                    throw new \Exception(trans('actions.err.create_usr_failed'));

                /*$get_user_group = Group::where("permissions", "like", '%"users":1%')->first();
                $group_record = [
                    'user_id' => $usrObj->id,
                    'group_id' => $get_user_group->id
                ];

                UserGroup::create($group_record);*/
                UserLog::makeLog($usrObj->id, UserLog::ACT_CREATED, Auth::user()->id);
            }

            $access_token = Str::random(26) . date('mis');
            $usrObj->access_token = $access_token;
            $role = $usrObj->roles->pluck('id');
            if(count($role) == 0) {
                if($usrObj->isSuperUser()) {
                    $usrObj->assignRole('SuperAdmin');
                } else if($usrObj->hasPermission("admin")) {
                    $usrObj->assignRole('Admin');
                } else if($usrObj->hasPermission("service_tickets")) {
                    $usrObj->assignRole('Technician');
                } else {
                    $usrObj->assignRole('User');
                }
            }
            $usrObj->save();

            $data = $usrObj->only("id", "first_name", "last_name", "username", "email", "phone", "jobtitle", "employee_num", "country", "gravatar", "location_id", "company_id", "manager_id", "department_id", "access_token", "website");
            $data["company_name"] = $usrObj->companyProp("name");
            $data["location_name"] = $usrObj->locationProp("name");
            $data["manager_name"] = $usrObj->managerProp("username");
            $data["profile_img"] = $usrObj->getProfileImg();
            $data["user_role"] = $usrObj->roles;
            $data["user_permissions"] = $usrObj->getAllPermission();
            $data["local_profile_img"] = $usrObj->getLocalProfile();
            $data["ticket_permission"] = $usrObj->getTicketRaiserPermission();
            $data["procurement_role"] = $usrObj->procurementRole != null ? $usrObj->procurementRole->getRole() : null;

            $return['status'] = "success";
            $return['msg'] = "Welcome, You have logged in successfully!";
            $return['data'] = $data;
            return response()->json($return);

        }
        catch(\Exception $e) {
            Log::error("msLogin: " . $e->getMessage());
            $error_msg = $e->getMessage();
            if( strlen($error_msg) > 300 ) {
                $return['msg'] = "Unable to log in. Contact the administrator. (ERR.UK)";
            }

            return response()->json($return);
        }
    }

    /**update user FCM token for send notification*/
    public function updateFcmToken(Request $request) {
        $return = ['status'=>'fail','msg'=>'FCM Token not updated'];
        try {
            $input = $request->all();
            $data = [];
            $user_id = Auth::user()->id;

            if(!empty($user_id)) {
                $userObj = User::where('id', $user_id)->first();
                $userObj->fcm_token = $input['fcm_token'];
                if ($userObj->save()) {
                    $return = ['status' => 'success', 'msg' => 'FCM Token updated successfully.', 'data' => $data];
                }
            }
            return response()->json($return);
        } catch(Exception $e) {
            Log::error("updateFcmToken() error : ".$e->getMessage());
            return response()->json($return);
        }
    }

    /**env settings for app*/
    public function getEnvsettings(Request $request) {
        $return = ['status'=>'fail','msg'=>'Unable to get env settings'];
        try {
            $data = [];
            $data['azure'] = config('services.azure');
            $office_app_id = env("OFFICE365_APP_ID_1");
            if(isset($office_app_id) && $office_app_id != "") {
                $data['azure1']["client_id"] = env('OFFICE365_APP_ID_1');
                $data['azure1']["client_secret"] = env('OFFICE365_SECRET_APP_KEY_1');
                $data['azure1']["redirect"] = env('OFFICE365_REDIRECT_URI_1');
                $data['azure1']["app_redirect"] = env('OFFICE365_APP_REDIRECT_URI');
                $data['azure1']["tenant"] = env('OFFICE365_TENANT_ID_1');
                $data['azure1']["logout_url"] = 'https://login.microsoftonline.com/'.env('OFFICE365_TENANT_ID_1').'/oauth2/v2.0/logout?post_logout_redirect_uri=';
            }
            $return = ['status'=>'success','msg'=>'ENV settings fetched successfully.','data' => $data];
            return response()->json($return);
        } catch(Exception $e) {
            Log::error("getEnvsettings() error : ".$e->getMessage());
            return response()->json($return);
        }
    }

    /** users bot operation */
    public function usersOperationForBot(Request $request) {
        $return = ['status'=>'fail','msg'=>'Unable to perform operation'];
        try {
            if(isset($request->search)) {
                $search_key = $request->search;
                $users = User::wherenull('deleted_at');
                $users->whereRaw('(username like "%' . $search_key . '%" or email like "%' . $search_key . '%" or phone like "%' . $search_key . '%" or (case when manager_id <> 0 then concat(first_name, " ", last_name) else "" end) like "%' . $search_key . '%") ');
                $data = $users->get();
                $responseData = [];
                foreach($data as $d) {
                    array_push($responseData, [
                        'id' => $d->id,
                        'username' => $d->username,
                        'email' => $d->email,
                        'phone' => $d->phone,
                        'fullName' => $d->getGuranteedNameText(),
                        'lastLogin' =>isset($d->last_login) ? date("d M Y H:i A", strtotime($d->last_login)) : null
                    ]);
                }
                $return = ['status'=>'success', 'msg'=>'', 'data' => $responseData];
                return response()->json($return);
            }
            if(isset($request->validateUsername)) {
                $search_key = $request->validateUsername;
                $users = User::wherenull('deleted_at');
                $users->where('username',$search_key)->orwhere('email',$search_key);
                $data = $users->count();
                if($data > 0) {
                    $return = ['status'=>'success', 'msg'=>'', 'data' => 'User validated.'];
                    return response()->json($return);
                }
            }
            if(isset($request->action) && $request->action == 'userState') {
                $user = User::find((int)$request->userId);
                if( ! $user->hasPermissionTo('UserBulkUpdate') ) {
                    $return["msg"] = trans('content.user_fields.Permission_denied');
                    return response()->json($return);
                }
                $updateUser = User::where('username',$request->username)->orwhere('email',$request->username);
                $state = $request->operation;
                if($updateUser->update(['activated' => $state])) {
                    Log::error($user->id." - ".UserLog::ACT_Active." - ".$user->id);
                    UserLog::makeLog($user->id, UserLog::ACT_Active, $user->id);
                    $return['status'] = 'success';
                    $return['msg'] = $state == 1 ? trans('content.user_fields.user-activated') : trans('content.user_fields.user-deactivated');
                    return response()->json($return);
                }
            }
            if(isset($request->action) && $request->action == 'sendCredentials') {
                $user = User::find((int)$request->userId);
                if( ! $user->hasPermissionTo('UserSendCredential') ) {
                    $return["msg"] = trans('content.user_fields.Permission_denied');
                    return response()->json($return);
                }
                $targetUser = User::where('username',$request->username)->orwhere('email',$request->username)->first();
                if(!empty($targetUser)) {
                    // if($targetUser->last_working_date != null || $targetUser->deleted_at != null || $targetUser->activated != 1) {
                    //     // Log::info($targetUser->last_working_date ."- ". $targetUser->deleted_at ."- ". $targetUser->activated != 1);
                    //     throw new \Exception(trans('content.user_fields.user_account_inactive'));
                    // }

                    if( !$targetUser->email || filter_var($targetUser->email, FILTER_VALIDATE_EMAIL) != true ) {
                        throw new \Exception(trans('content.user_fields.user_email_invalid'));
                    }

                    if(! config('mail.service_enabled')) {
                        throw new \Exception(trans('content.user_fields.mail_service'));
                    }

                    $new_password = $targetUser->setPassword('', true);
                    if($targetUser->save()) {
                        Mail::to($targetUser->email)->queue(new UserCredentialNotification($targetUser, $new_password));
                        $return['status'] = 'success';
                        $return['msg'] = trans('content.user_fields.user_credentials_resetted');
                        Log::info("sendResetCredential id:" . $targetUser->id." uid:" . $targetUser->id);
                    }
                }
            }
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("usersOperationForBot: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function validateSeatNo(Request $request) {
        try {
            $request->validate(['seat_no' => 'required|string']);
    
            $seatNo = $request->seat_no;
            $user = UserDetails::where('seat_no', $seatNo)->first();
    
            $status = $user ? 'success' : 'success';
            $message = $user ? 'Seat number is valid' : 'Seat number is InValid';
    
            return response()->json(compact('status', 'message'));
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Seat number is required',
            ], 400);
    
        } catch (\Exception $e) {
            Log::error('Seat check error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateLastWorkingDate(Request $request) {
        $data = $request->all();
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
        if($user != "greenitcoITM" || $pass != "VnYJ9bpNNz") {
            $return["msg"] = "Username or password is wrong";
            return response()->json($return);
        }
        $return = ['status' => 'fail', 'msg' => 'Unable to update last working date'];
        try {
            if(!is_array($data) && count($data) == 0) {
                $return['msg'] = 'Invalid request data';
                return response()->json($return);
            }
            foreach ($data as $value) {
                $user = User::find($value['user_id']);
                if (empty($user)) {
                    Log::info("User not found for ID: {$value['user_id']}");
                    continue;
                }
                if ($user->last_working_date ==  $value['last_working_date']) {
                    Log::info("No change in last working date for User ID: {$user->id}");
                    continue;
                }
                $user->last_working_date = $value['last_working_date'];
                if ($user->save()) {
                    $userDetailObj = UserDetails::where('user_id', $user->id)->first();
                    if (!$userDetailObj) {
                        $userDetailObj = new UserDetails();
                        $userDetailObj->user_id = $user->id;
                        $userDetailObj->exit_type = $value['exit_type'];
                    } else {
                        $userDetailObj->exit_type = $value['exit_type'];
                        if ($userDetailObj->exit_ticket_id !== null) {
                            $ticketId = $userDetailObj->exit_ticket_id;
                            /** resolve existing ticket id when ticket is created and last working date is changed */
                            $ticket = Ticket::find($ticketId);
                            if ($ticket && !empty($ticket)) {
                                $tf = new TktFollowing();
                                $tf->ticket_id = $ticket->id;
                                $tf->updated_by = 0;
                                $tf->updated_status = 5;
                                $tf->action_type = 2;
                                $tf->remarks = "Ticket resolved as user last working date is updated by system scheduler";
                                $tf->save();

                                $tkt_update['ticket_id'] = $tf->ticket_id;
                                $tkt_update['updated_by'] = 0;
                                $tkt_update['status'] = 5;
                                $tkt_update['old_status'] = $ticket->status_id;
                                $tkt_update['action_type'] = 2;
                                CommonHelper::ticketStatusHistory($tkt_update);

                                $ticket->status_id = 5;
                                $ticket->save();
                            
                                $alertnotify = null;
                                $creator = User::find($ticket->creator_id);
                                if(Settings::first()->alerts_enabled == 1){
                                    $alertnotify = CommonHelper::getGlobalAlertEmail();
                                }
                                if(config('mail.service_enabled') && $creator && $creator->email && filter_var($creator->email, FILTER_VALIDATE_EMAIL)) {
                                    $add_back_trail = $request->add_back_trail == 1 ? true : false;
                                    try {
                                        if(Config::requiredAlertSettingsEmail() && $alertnotify) {
                                            $cc_emails = [];
                                            foreach ($alertnotify as $email) {
                                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                    $cc_emails[] = $email;
                                                }
                                            }
                                            Mail::to($creator->email)->cc($cc_emails)->queue(new StatusChanged($ticket, $creator, $tf->remarks, $tf, $add_back_trail));
                                        }
                                        else {
                                            Mail::to($creator->email)->queue(new StatusChanged($ticket, $creator, $tf->remarks, $tf, $add_back_trail));
                                        }
                                    }
                                    catch(\Exception $e) {
                                        Log::error("updateStatus Mail1: " . $e->getMessage());
                                    }
                                }
                            }
                            $userDetailObj->exit_ticket_id = null;     
                        }
                    }
                    /** Create ticket when date is less than current date */
                    if($user->last_working_date != null && $user->last_working_date != "" && Carbon::parse($user->last_working_date)->lte(Carbon::today())) {
                        $department = Department::where('name', 'like', 'HR - India%')->first();
                        $category = ProblemCategory::where('name', 'like', 'Employee Exit%')->first();
                        if(empty($department) || empty($category)) {
                            Log::info('Update last working date : Department or Category not found for ticket creation'. json_encode($user));
                            continue;
                        }
                        
                        // devices of the user
                        $devices = DB::table("assets as a");
                        $devices->select('a.asset_tag','a.name', 'a.serial');
                        $devices->where('a.assigned_for', '=', 1);
                        $devices->whereNull('a.deleted_at');
                        $devices->where('a.assigned_to','=', $user->id);
                        $devices = $devices->get();

                        // accessories assigned to user
                        $accessories = DB::table('accessories_users as au');
                        $accessories->leftJoin('accessories as acc', 'acc.id', '=', 'au.accessory_id');
                        $accessories->leftJoin('assets as a', function($q) {
                            $q->on('a.id', '=', 'au.assigned_to');
                            $q->where('au.assigned_for', '=', '3');
                        });
                        $accessories->whereNull('acc.deleted_at');
                        $accessories->where(function($q) use($user) {
                            $q->where('au.assigned_for', '=', 1)->where('au.assigned_to', '=', $user->id)->OrWhere('a.assigned_for', '=', 1)->where('a.assigned_to', '=', $user->id);
                        });
                        $accessories->select( 'acc.name', 'acc.batch_no as batch_no');
                        if (config("app.client") == "etherealmachines") {
                            $accessories->addSelect(DB::raw('case when acc.id is not null then concat_ws("","AC",acc.id) else "" end as acc_batch_no'));
                        } else {
                            $accessories->addSelect(DB::raw('case when acc.id is not null then concat_ws("","A",acc.id) else "" end as acc_batch_no'));
                        }
                        $accessories = $accessories->get();


                        // assigned consumable
                        $consumables = DB::table('consumables_users as cu');
                        $consumables->leftJoin('consumables as con', 'con.id', '=', 'cu.consumable_id');
                        $consumables->where('cu.assigned_to', '=', $user->id);
                        $consumables->whereNull("con.deleted_at");
                        $consumables->select('con.name');
                        if (config("app.client") == "etherealmachines") {    
                            $consumables->addSelect(DB::raw('case when con.id is not null then concat_ws("","CN",con.id) else "" end as con_batch_no'));
                        } else { 
                            $consumables->addSelect(DB::raw('case when con.id is not null then concat_ws("","CNS",con.id) else "" end as con_batch_no'));
                        }
                        $consumables = $consumables->get();

                        $content = "<h3>Asset Surrender Details for {$user->username}</h3>";

                        // Devices
                        if ($devices->isNotEmpty()) {
                            $content .= "<h4>Devices</h4>
                            <table border='1' cellspacing='0' cellpadding='5' style='width:100%; border-collapse:collapse;'>
                                <tr style='background-color:#000; color:#fff; text-align:left;'>
                                    <th style='padding:8px;'>Asset Tag</th>
                                    <th style='padding:8px;'>Name</th>
                                    <th style='padding:8px;'>Serial</th>
                                </tr>";
                            foreach ($devices as $d) {
                                $content .= "<tr>
                                    <td style='padding:6px;'>{$d->asset_tag}</td>
                                    <td style='padding:6px;'>".($d->name ?? '-')."</td>
                                    <td style='padding:6px;'>".($d->serial ?? '-')."</td>
                                </tr>";
                            }
                            $content .= "</table><br>";
                        }

                        // Accessories
                        if ($accessories->isNotEmpty()) {
                            $content .= "<h4>Accessories</h4>
                            <table border='1' cellspacing='0' cellpadding='5' style='width:100%; border-collapse:collapse;'>
                                <tr style='background-color:#000; color:#fff; text-align:left;'>
                                    <th style='padding:8px;'>Name</th>
                                    <th style='padding:8px;'>Batch No</th>
                                    <th style='padding:8px;'>Acc Batch No</th>
                                </tr>";
                            foreach ($accessories as $a) {
                                $content .= "<tr>
                                    <td style='padding:6px;'>{$a->name}</td>
                                    <td style='padding:6px;'>".($a->batch_no ?? '-')."</td>
                                    <td style='padding:6px;'>{$a->acc_batch_no}</td>
                                </tr>";
                            }
                            $content .= "</table><br>";
                        }

                        // Consumables
                        if ($consumables->isNotEmpty()) {
                            $content .= "<h4>Consumables</h4>
                            <table border='1' cellspacing='0' cellpadding='5' style='width:100%; border-collapse:collapse;'>
                                <tr style='background-color:#000; color:#fff; text-align:left;'>
                                    <th style='padding:8px;'>Name</th>
                                    <th style='padding:8px;'>Consumable Batch No</th>
                                </tr>";
                            foreach ($consumables as $c) {
                                $content .= "<tr>
                                    <td style='padding:6px;'>{$c->name}</td>
                                    <td style='padding:6px;'>{$c->con_batch_no}</td>
                                </tr>";
                            }
                            $content .= "</table><br>";
                        }

                        $ticketController = new IndexController();
                        $requestData = [
                            'creator_id' => 1,
                            'subject' => "Asset Surrender User : {$user->username} Last Working Date : {$user->last_working_date}",
                            'content' =>  $content,
                            'department_id' => $department->id,
                            'problem_category_id' => $category->id,
                        ];
                        $requestData = new Request($requestData);
                        $obj = $ticketController->createByUser($requestData);
                        if (empty($data['status']) || $data['status'] != 'success') {
                            Log::info('Update last working date : Ticket creation failed for user'. json_encode($user));
                            continue;
                        }
                        if ($data['status'] == 'fail') {
                            Log::info('Update last working date : Ticket creation failed for user'. json_encode($obj));
                            continue;
                        }
                        if ($data['status'] == 'success') {
                            $userDetailObj->exit_ticket_id = $data['ticket_id'];
                            Log::info("Ticket created for user last working date update: " . json_encode($obj));
                        }
                    }                    
                    $userDetailObj->save();
                    UserLog::makeLog($user, UserLog::ACT_UPDATED, 0);
                    Log::info("User ID: {$user->id} - Last working date updated to: {$request->last_working_date}");
                } else {
                    Log::info('Failed to update last working date'. json_encode($value));
                    continue;
                }
            }
            $return['status'] = 'success';
            $return['msg'] = 'Last working date updated successfully';
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error('Error in updateLastWorkingDate: ' . $e->getMessage());
            return response()->json($return);
        }
    }

    public function oldToNewPSConversion(Request $request) {
        try {
            $return = ["status" => "fail", "msg" => "Unable to update the PS no."];
            $oldUser = User::where("employee_id", $request->old_psno)->first();
            if(!$oldUser) {
                $return["msg"] = "Old user not found";
                return response()->json($return);
            }
            $newUser = $oldUser->replicate();
            $newUser->employee_id = $request->new_psno;
            $newUser->email = $request->new_email;
            if($newUser->save()) {
                $newUserId = $newUser->id;

                //Ticket assigned change
                $assignedTickets = Ticket::where('assigned_to', $oldUser->id)->whereIn("status_id", [5,6])->wherenull(["deleted_at", "is_temp"])->get();
                foreach($assignedTickets as $assignedTicket) {
                    $assignedTicket->update(["assigned_to" => $newUserId]);
                }
                //Ticket creator change
                $createdTickets = Ticket::where('creator_id', $oldUser->id)->whereIn("status_id", [5,6])->wherenull(["deleted_at", "is_temp"])->get();
                foreach($assignedTickets as $assignedTicket) {
                    $createdTickets->update(["creator_id" => $newUserId]);
                }

                //Request assigned change
                $assignedRequests = TicketProcureRequest::where('assigned_to', $oldUser->id)->wherenull(["deleted_at", "is_temp"])->get();
                foreach($assignedRequests as $assignedRequest) {
                    $assignedRequest->update(["assigned_to" => $newUserId]);
                }
                //Request creator change
                $createdRequests = TicketProcureRequest::where('creator_id', $oldUser->id)->wherenull(["deleted_at", "is_temp"])->get();
                foreach($createdRequests as $createdRequest) {
                    $createdRequest->update(["creator_id" => $newUserId]);
                }

                //Ticket auto allocation member change
                $autoallocationMember = TktAutoAllocationGroupMember::where("user_id", $oldUser->id)->update(["user_id" => $newUserId]);

                //Requesr SRAT member
                TicketPabMember::where("user_id", $oldUser->id)->update(["user_id" => $newUserId]);
                DB::table('tkt_ticket_pab_members_history')::where("user_id", $oldUser->id)->update(["user_id" => $newUserId]);

                //CAB Change management.
                $changeRequests  = Record::where("change_requester" , $oldUser->id)->get();
                foreach($changeRequests as $changeRequest) {
                    $changeRequest->update(["change_requester" => $newUserId]);
                }
                $changeApprovers  = Record::where("change_approver" , $oldUser->id)->get();
                foreach($changeApprovers as $changeApprover) {
                    $changeApprover->update(["change_approver" => $newUserId]);
                }
                $changeRequestsManagers  = Record::where("change_manager" , $oldUser->id)->get();
                foreach($changeRequestsManagers as $changeRequestsManager) {
                    $changeRequestsManager->update(["change_manager" => $newUserId]);
                }
                $changeImplementers = Record::where('change_implementer ', 'like', '%"'.$oldUser->id.'"%')->get();
                foreach ($changeImplementers as $changeImplementer) {
                    $updatedIds = str_replace('"'.$oldUser->id.'"', '"'.$newUser->id.'"', $changeImplementer->user_ids);
                    $changeImplementer->update(['change_implementer' => $updatedIds]);
                }
                $changeReviewers = Record::where('change_reviewer ', 'like', '%"'.$oldUser->id.'"%')->get();
                foreach ($changeReviewers as $changeReviewer) {
                    $updatedIds = str_replace('"'.$oldUser->id.'"', '"'.$newUser->id.'"', $changeReviewer->user_ids);
                    $changeReviewer->update(['change_reviewer' => $updatedIds]);
                }
                $CabMember = CabMember::where("user_id", $oldUser->id)->update(["user_id" => $newUserId]);
                $approvalRequst = ApprovalRequest::where("user_id", $oldUser->id)->update(["user_id" => $newUserId]);

                $oldUser->delete();    
                $return = [
                    "status" => "success",
                    "msg" => "Old to new PS number conversion is successfully done."
                ];
            }
            return response()->json($return);

        } catch (\Exception $e) {
            Log::error();
            return response()->json($return);
        }
    }

    public function getCompanyByUserAccess(Request $request)
    {
        try {
            $return = array();
            $return["pagination"] = ["more" => false];
            $return["results"] = [];
            $search = $request->input("search", "");
            $page = $request->input("page", 1);
            $skip = (($page * 20) - 20);
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    "status" => false,
                    "message" => "Unauthenticated"
                ], 401);
            }
            
            $permission = json_decode($user->permission, true) ?? [];
            $userCompanyPrev = [];

            if (
                (!empty($permission['service_tickets']) && $permission['service_tickets'] == 1)
                || $user->hasRole('SuperAdmin')
            ) {
                $userCompanyPrev = TktCompanyPreviledge::where('user_id', $user->id)
                    ->pluck('company_id')
                    ->toArray();
            }

            $userCompany = UserDetails::where('user_id', $user->id)
                ->pluck('user_companies')
                ->flatMap(fn($item) => explode(',', $item))
                ->toArray();

            $company = $user->company_id;
            $finalCompanies = collect($userCompanyPrev)
                ->merge($userCompany)
                ->push($company)
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->toArray();

            $db = Company::select("id", DB::raw('name as text'))
                ->whereIn('id', $finalCompanies);

            if ($search) {
                $db->where('name', 'like', "%{$search}%");
            }

            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            if ($page == 1 && empty($search) && $request->main_filter == '1') {
                $allOption = [
                    'id'   => '0',
                    'text' => 'All'
                ];
                $result = collect($result)->prepend($allOption);
            }

            $return["pagination"] = [
                "more" => ($count - ($page * 20)) > 0 ? true : false
            ];

            $return["results"] = count($result) ? $result->toArray() : [];

            return response()->json($return, 200);
        } catch (\Exception $e) {

            Log::error('Error in getCompanyByUserAccess', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile()
            ]);

            return response()->json([
                "status" => false,
                "message" => "Something went wrong"
            ], 500);
        }
    }

    public function storeDefaultCompany(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_id' => 'required|integer|min:0'
            ]);
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    "status" => false,
                    "message" => "Unauthenticated"
                ], 401);
            }  
            $companyId = (int) $validated['company_id'];
            $allowedCompanies = CommonHelper::getAccessibleCompanyIds();
            if ($companyId !== 0 && !in_array($companyId, $allowedCompanies)) {
                return response()->json([
                    "status" => false,
                    "message" => "Company is not allowed for this user"
                ], 403);
            }
            UserDetails::updateOrCreate(
                ['user_id' => $user->id],
                ['dashboard_company_id' => $request->company_id]
            );
            return response()->json([
                'status' => true,
                'message' => 'Default company saved successfully'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in storeDefaultCompany',$e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "Something went wrong"
            ], 500);
        }
    }
}

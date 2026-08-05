<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Validator;
use Auth;
use App\Models\Actionlog;
use App\Models\Device;
use App\Models\License;
use App\Models\User;
use App\Models\Company;
use Storage;

class DocumentController extends Controller {

    public function ajaxDocuments(Request $request) {
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
            '1' => 'al.filename',
            '2' => 'al.created_at',
            '3' => 'al.note'
        );
        $order = [
            0=>'asc',
            1=>'desc'
        ];
        
        $db = DB::table('asset_logs as al');
        $db->whereNotNull("al.filename");
        $db->whereNull("al.deleted_at");
        $db->where("al.asset_type", "=", 'user');// asset type user to get documents by user 
        $db->where("al.action_type", "=", "uploaded");
        // if( isset($req["device_id"]) && $req["device_id"] ) {
            $db->where('al.asset_id', '=', Auth::user()->id);
        // }
        $db->select("al.id", "al.filename", "al.note");
        $db->addSelect(DB::raw('DATE_FORMAT(al.created_at, "%d %b %Y %h:%i %p") as created_at_format'));

        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(al.filename like "%%%1$s%%" or al.note like "%%%1$s%%" or DATE_FORMAT(al.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
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

    public function ajaxUpload(Request $request) {
        $return = array(
            "status" => "failure",
            "msg" => "Unable to upload given document"
        );

        $data = $request->only("asset_id", "asset_type", "document", "note");

        $rules = [
            "asset_id" => "sometimes|integer|min:1",
            "asset_type" => "sometimes|string|max:20",
            "note" => "nullable|string|max:255",
            "document" => "required|mimes:png,gif,jpg,jpeg,doc,docx,pdf,txt,zip,rar|max:2000"
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $log = new Actionlog();
        $log->asset_type = $request->asset_type;
        if($request->asset_id && $request->asset_type == "hardware") {
            $item = Device::where("id", "=", $request->asset_id)->first();
            if(!$item || !$item->exists) {
                $return["msg"] = "Relavent Device is not found.";
                return response()->json($return);
            }
            $log->asset_id = $request->asset_id;
        }
        elseif($request->asset_id && $request->asset_type == "user") {
            $item = User::where("id", "=", $request->asset_id)->first();
            if(!$item || !$item->exists) {
                $return["msg"] = "Relavent User is not found.";
                return response()->json($return);
            }
            $log->asset_id = $request->asset_id;
        }
        elseif($request->asset_id && $request->asset_type == "software") {
            $item = License::where("id", "=", $request->asset_id)->first();
            if(!$item || !$item->exists) {
                $return["msg"] = "Relavent License is not found.";
                return response()->json($return);
            }
            $log->asset_id = $request->asset_id;
        }
        elseif($request->asset_id && $request->asset_type == "accessory") {
            $item = Accessory::where("id", "=", $request->asset_id)->first();
            if(!$item || !$item->exists) {
                $return["msg"] = "Relavent Accessory is not found.";
                return response()->json($return);
            }
            $log->asset_id = $request->asset_id;
        }
        elseif($request->asset_id && $request->asset_type == "consumable") {
            $item = Consumable::where("id", "=", $request->asset_id)->first();
            if(!$item || !$item->exists) {
                $return["msg"] = "Relavent Consumable is not found.";
                return response()->json($return);
            }
            $log->asset_id = $request->asset_id;
        }
        elseif($request->asset_id && $request->asset_type == "component") {
            $item = Component::where("id", "=", $request->asset_id)->first();
            if(!$item || !$item->exists) {
                $return["msg"] = "Relavent Component is not found.";
                return response()->json($return);
            }
            $log->asset_id = $request->asset_id;
        }
        else {
            return response()->json($return);
        }
        

        $path = Storage::disk("documents")->putFile("", $request->document);
        if(! $path) {
            return response()->json($return);
        }

        $log->user_id = Auth::user()->id;
        $log->note = $request->note;
        $log->checkedout_to = NULL;
        $log->created_at = date("Y-m-d H:i:s");
        $log->filename = $path;
        $log->action_type = "uploaded";
        $log->save();

        $return["status"] = "success";
        $return["msg"] = "Document has been uploaded successfully";
        return response()->json($return);
    }

    public function ajaxDelete(Request $request) {
        $return = array("status" => "failure", "msg" => "Unable to delete the Document");

        $rules = [
            "asset_id" => "sometimes|integer|min:1",
            "asset_type" => "sometimes|string|max:20",
            "id" => "sometimes|integer|min:1",
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        
        $item = false;
        if($request->asset_id && $request->asset_type == "hardware") {
            $item = Device::where("id", "=", $request->asset_id)->first();
            if(! $item->exists) {
                $return["msg"] = "Relavent Device is not found.";
                return response()->json($return);
            }
        }
        elseif($request->asset_id && $request->asset_type == "software") {
            $item = License::where("id", "=", $request->asset_id)->first();
            if(! $item->exists) {
                $return["msg"] = "Relavent License is not found.";
                return response()->json($return);
            }
        }
        elseif($request->asset_id && $request->asset_type == "accessory") {
            $item = Accessory::where("id", "=", $request->asset_id)->first();
            if(!$item || !$item->exists) {
                $return["msg"] = "Relavent Accessory is not found.";
                return response()->json($return);
            }
        }
        elseif($request->asset_id && $request->asset_type == "consumable") {
            $item = Consumable::where("id", "=", $request->asset_id)->first();
            if(!$item || !$item->exists) {
                $return["msg"] = "Relavent Consumable is not found.";
                return response()->json($return);
            }
        }
        elseif($request->asset_id && $request->asset_type == "user") {
            $item = User::where("id", "=", $request->asset_id)->first();
            if(!$item || !$item->exists) {
                $return["msg"] = "Relavent User is not found.";
                return response()->json($return);
            }
        }
        elseif($request->asset_id && $request->asset_type == "component") {
            $item = Component::where("id", "=", $request->asset_id)->first();
            if(!$item || !$item->exists) {
                $return["msg"] = "Relavent Component is not found.";
                return response()->json($return);
            }
        }
        else {
            return response()->json($return);
        }
        
        // if(! Company::checkUserAccess($item)) {
            //     $return["msg"] = "Insufficient Permission";
            //     return response()->json($return);
            // }
            
        $log = Actionlog::where("id", "=", $request->id)->where("asset_type", "=", $request->asset_type)->whereNotNull("filename")->whereNull("deleted_at")->first();
        if(! $log->exists) {
            return response()->json($return);
        }

        // if(! Storage::disk("documents")->exists($log->filename)) {
        //     return response()->json($return);
        // }
        
        Storage::disk("documents")->delete($log->filename);
        $log->delete();
        $return["status"] = "success";
        $return["msg"] = "Document has been deleted successfully!";
        return response()->json($return);
    }

    /* document for app */

    public function ajaxDocumentsForApp(Request $request) {
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
        $req["order_by"] = $request->order_by ? $request->order_by : '2';
        $req["order_dir"] = $request->order_dir == "0" ? $request->order_dir : '1';

        $fields = array(
            '1' => 'al.original_file_name',
            '2' => 'al.created_at',
            '3' => 'al.note',
            '4' =>'al.updated_at'
        );
        
        $db = DB::table('asset_logs as al');
        $db->whereNotNull("al.filename");
        $db->whereNull("al.deleted_at");
        $db->where("al.asset_type", "=", $request->asset_type);
        $db->where("al.action_type", "=", "uploaded");
        if( isset($req["user_id"]) && $req["user_id"] ) {
            $db->where('al.asset_id', '=', $req["user_id"]);
        }
        $db->select("al.id", "al.filename", "al.original_file_name", "al.note");
        $db->addSelect(DB::raw('DATE_FORMAT(al.created_at, "%d %b %Y %h:%i %p") as created_at_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(al.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));

        $return['status'] = "success";
        $return['msg'] = "";
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(al.original_file_name like "%%%1$s%%" or al.note like "%%%1$s%%" or DATE_FORMAT(al.created_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(al.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
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
}
<?php

namespace App\Http\Controllers\Auth\Api\Ticket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Attachment;
use App\Models\TktFollowing;
use App\Models\User;
use App\Models\Settings;
use Auth;
use Storage;
use Validator;
use DB;
use Illuminate\Validation\Rule;
use App\Helpers\Common;

class AttachmentController extends Controller {
    
    public function add(Request $request) {
        $return = array(
            "status" => "fail",
            "msg" => "Upload has not success"
        );
        
        $rules = [
            'ticket_id' => 'required|integer|exists:tkt_tickets,id',
            'attachment' => 'required|file',
            'tmp_id' => 'sometimes|nullable|string|max:20'
        ];
        
        $validator = Validator::make($request->all(), $rules); 
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        
        if(! $request->hasFile('attachment') || !$request->file('attachment')->isValid()) {
            return response()->json($return);
        }
        
        $file = $request->file('attachment');
        $filePath = date("Y").'/'.date('m').'/'.date('d');
        $checkFolderPath = Common::attachmentFolderStructure('tickets', $filePath);
        if(!$checkFolderPath) {
            $return["msg"] = "Attachment Directory not found";
            return response()->json($return);
        }
        $a = new Attachment();
        $a->ticket_id = $request->ticket_id;
        $a->original_file_name = $file->getClientOriginalName();
        $a->extension = $file->getClientOriginalExtension();
        $a->file_name = isset($request->update_attachments) ? $request->file('update_attachments')->store($filePath, "tickets") : $request->file('attachment')->store($filePath, "tickets");
        $a->uploader_id = Auth::user()->id;
        $a->tmp_id = $request->input("tmp_id", null);
        
        if(!$a->file_name || !$a->save()) {
            return response()->json($return);
        }
        
        $return["data"] = $a->only("id", "original_file_name");
        $return["status"] = "success";
        $return["msg"] = "";
        return response()->json($return); 
    }
    
    /* to remove the attachment */
    public function remove(Request $request) {
        $return = array(
            "status" => "fail",
            "msg" => "Unable to remove attachment"
        );
        
        $rules = [
            'id' => 'required|integer|exists:tkt_attachments,id'
        ];
        
        $validator = Validator::make($request->all(), $rules); 
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        
        $a = Attachment::find($request->id);
        Storage::disk('tickets')->delete($a->file_name);
        $a->delete();
        
        $return["status"] = "success";
        $return["msg"] = "Attachment removed";
        return response()->json($return);
    }
    
    public function download($id){
        $a = Attachment::find($id);
        if(!$a || !Storage::disk('tickets')->exists($a->file_name)) {
            return redirect("/");
        }
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'. $a->original_file_name .'"');
        echo Storage::disk('tickets')->get($a->file_name);
    }
}
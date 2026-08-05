<?php

namespace App\Http\Controllers\Auth\Api\Procurement;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use App\Models\Procurement\Attachment;
use Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Storage;
use Validator;
use File;
use Log;
use Image;


class AttachmentController extends Controller
{
    //
    public function add(Request $request) {
        $return = array(
            "status" => "fail",
            "msg" => trans('content.service_ticket_fields.Upload_has_not_success')
        );

        try {
            DB::beginTransaction();
            $rules = [
                'pr_id' => 'required',
                'tmp_id' => 'sometimes|nullable|string|max:20'
            ];

            if(isset($request->update_attachments)) {
                $rules['update_attachments'] = 'required|file';
            } else {
                $rules['attachment'] = 'required|file';
            }

            $validator = \Validator::make($request->all(), $rules); 
            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            if(isset($request->update_attachments)) {
                if(! $request->hasFile('update_attachments') || !$request->file('update_attachments')->isValid()) {
                    return response()->json($return);
                }
                $file = $request->file('update_attachments');
            } else {
                if (!$request->hasFile('attachment') || !$request->file('attachment')->isValid()) {
                    return response()->json($return);
                }
                $file = $request->file('attachment');
            }
            
            $request_id = ($request->pr_id);
            $filePath = date("Y").'/'.date('m').'/'.date('d');
            $checkFolderPath = Common::attachmentFolderStructure('procurements', $filePath);
            if(!$checkFolderPath) {
                $return["msg"] = "Attachment Directory not found";
                return response()->json($return);
            }
            $a = new Attachment();
            $a->procurement_id = $request_id;
            $a->original_file_name = $file->getClientOriginalName();
            $a->original_file_name = $a->trimUnfittedName($a->original_file_name);
            $a->extension = strtolower($file->getClientOriginalExtension());
            $a->file_name = isset($request->update_attachments) ? $request->file('update_attachments')->store($filePath, "procurements") : $request->file('attachment')->store($filePath, "procurements");
            $a->uploader_id = Auth::user()->id;
            $a->tmp_id = $request->input("tmp_id", null);
            
            if(!$a->file_name || !$a->save()) {
                return response()->json($return);
            }

            /* generate thumb if image */
            if( in_array($a->extension, ["png", "jpeg", "jpg"]) !== false ) {
                try {
                    $path = storage_path('procurements') . DIRECTORY_SEPARATOR . $a->file_name;
                    $thumb_name = $a->getThumbName();
                    if(! $thumb_name) {
                        throw new \Exception("Invalid Image Name " . $a->procurement_id);
                    }
                    $thumb_path = storage_path('procurements') . DIRECTORY_SEPARATOR . $thumb_name;

                    Image::make($path)->resize(100, null, function($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save($thumb_path);
                    $a->thumbnail = $thumb_name;
                    $a->save();
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            
            $return["data"] = $a->only("id", "original_file_name");
            $return["status"] = "success";
            $return["msg"] = "";
            DB::commit();
        }
        catch(\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }
        return response()->json($return); 
    }

     /* to remove the attachment */
     public function remove(Request $request) {
        try{
            DB::beginTransaction();
            $return = array(
                "status" => "fail",
                "msg" => trans('content.service_ticket_fields.Unable_to_remove_attachment')
            );
            
            $rules = [
                'id' => 'required'
            ];
            
            $validator = Validator::make($request->all(), $rules); 
            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            
            $a = Attachment::find($request->id);
            Storage::disk('procurements')->delete($a->file_name);
            $a->delete();
            
            $return["status"] = "success";
            $return["msg"] = trans('content.service_ticket_fields.Attachment_removed');
            DB::commit();
            return response()->json($return);
        }catch(Exception $e){
            Log::error("Attachment procure remove : ".$e->getMessage());
            return $return;
        }
    }

    public function download($id){
        $a = Attachment::find($id);
        if(!$a || !Storage::disk('procurements')->exists($a->file_name)) {
            return redirect("/");
        }
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'. $a->original_file_name .'"');
        echo Storage::disk('procurements')->get($a->file_name);
    }

    public function viewItem($id, $thumb = false) {

        try {
            
            $a = Attachment::find($id);
            if(!$a || !Storage::disk('procurements')->exists($a->file_name)) {
                throw new \Exception("Invalid File");
            }

            $path = Storage::disk('procurements')->getAdapter()->getPathPrefix();
            if($thumb) {
                $path .= $a->thumbnail;
            }
            else {
                $path .= $a->file_name;
            }

           return response()->file($path);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

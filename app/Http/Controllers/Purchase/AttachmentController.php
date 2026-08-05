<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Purchase\FileAttachment;
use App\Models\Purchase;
use App\Models\PurchaseAttachment;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Auth;
use DB;
use Log;
use Storage;
use Validator;

class AttachmentController extends Controller
{
    public function add(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseDocumentAdd')) {
            return response()->json($return);
        }
        $return = array(
            'status' => 'fail',
            'msg' => 'Upload has not success'
        );

        $rules = [
            'reference_id' => 'required|integer|exists:purchases,id',
            'attachment' => 'required|file',
            'tmp_id' => 'sometimes|nullable|string|max:20'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }

        if (!$request->hasFile('attachment') || !$request->file('attachment')->isValid()) {
            return response()->json($return);
        }

        $file = $request->file('attachment');

        $a = new FileAttachment();
        $a->reference_id = $request->reference_id;
        $a->original_file_name = $file->getClientOriginalName();
        $a->extension = $file->getClientOriginalExtension();
        $a->file_name = $request->file('attachment')->store('', 'purchase_attachments');
        $a->uploader_id = Auth::user()->id;
        $a->tmp_id = $request->input('tmp_id', null);

        if (!$a->file_name || !$a->save()) {
            return response()->json($return);
        }

        $return['data'] = $a->only('id', 'original_file_name');
        $return['status'] = 'success';
        $return['msg'] = '';
        return response()->json($return);
    }

    /* to remove the attachment */
    public function remove(Request $request)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseDocumentDelete')) {
            return response()->json($return);
        }
        $return = array(
            'status' => 'fail',
            'msg' => 'Unable to remove attachment'
        );

        $rules = [
            'id' => 'required|integer|exists:purchase_attachments,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }

        $a = FileAttachment::find($request->id);
        Storage::disk('purchase_attachments')->delete($a->file_name);
        $a->delete();

        $return['status'] = 'success';
        $return['msg'] = 'Attachment removed';
        return response()->json($return);
    }

    public function download($id)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseDocumentDownload')) {
            return redirect('dashboard')->with('msg', $return);
        }
        $a = FileAttachment::find($id);
        if (!$a || !Storage::disk('purchase_attachments')->exists($a->file_name)) {
            return redirect('/');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $a->original_file_name . '"');
        echo Storage::disk('purchase_attachments')->get($a->file_name);
    }

    /* data table listing attachments of a product */
    public function list(Request $request)
    {
        $req = $request->all();
        $return = array(
            'draw' => date('is')
        );

        $fields = array(
            '1' => 'a.original_file_name',
            '2' => 'a.updated_at'
        );

        $db = DB::table('purchase_attachments as a');
        $db->select('a.id', 'a.file_name', 'a.original_file_name');
        $db->addSelect(DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));
        $db->where('a.reference_id', '=', $request->reference_id);

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(a.original_file_name like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['order'][0]['column']) && isset($fields[$req['order'][0]['column']]) && in_array($req['order'][0]['dir'], ['asc', 'desc'])) {
            $db->orderBy($fields[$req['order'][0]['column']], $req['order'][0]['dir']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req['start']) && isset($req['length'])) {
            $skip = (int) $req['start'];
            $take = (int) $req['length'];
        }
        $db->skip($skip);
        $db->take($take);

        $data = $db->get();

        $return['data'] = array();
        foreach ($data as $d) {
            $d->original_file_name = urldecode($d->original_file_name);
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    public function view($id, $thumb = false)
    {
        try {
            $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
            if (!Auth::user()->hasPermissionTo('PurchaseDocumentView')) {
                return redirect('dashboard')->with('msg', $return);
            }
            $a = FileAttachment::find($id);
            if (!$a || !Storage::disk('purchase_attachments')->exists($a->file_name)) {
                throw new \Exception('Invalid File');
            }
            $filePath = $thumb ? $a->thumbnail : $a->file_name;
            $path = Storage::disk('purchase_attachments')->path($filePath);
            return response()->file($path);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

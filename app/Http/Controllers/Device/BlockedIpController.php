<?php

namespace App\Http\Controllers\Device;

use App\Exports\Device\BlockedIPExport;
use App\Http\Controllers\Controller;
use App\Models\Assets;
use App\Models\BlockedIP;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use DB;
use Log;
use Validator;

class BlockedIpController extends Controller
{
    public function getIndex(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasAnyRole(['SuperAdmin']) || !config('services.assets.enabled')) {
            return redirect('dashboard')->with('msg', $return);
        }
        return view('devices.blocked_ip.index');
    }

    public function ajaxAddBlockedIp(Request $request)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to add the Ip'
        ];
        $rules = [
            'type' => 'required',
            'ip' => 'nullable|string',
            'mac' => 'nullable|string',
        ];
        $messages = [];
        $validate = Validator::make($rules, $messages);

        if ($validate->fails()) {
            $v = $validate->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }

        $obj = new BlockedIP;
        $data = $request->only('ip', 'type', 'mac');
        $data['created_by'] = Auth::user()->id;
        $obj->fill($data);
        if ($obj->save()) {
            $return['status'] = 'success';
            $return['msg'] = 'New IP/MAC has been created successfully.';
        }

        return response()->json($return);
    }

    public function ajaxIndex(Request $request)
    {
        $req = $request->all();
        $return = array(
            // "draw" => date('is')
        );

        $fields = array(
            '1' => 'b.type',
            '2' => 'b.ip',
            '3' => 'b.created_at',
            '4' => 'b.updated_at',
        );

        $db = DB::table('blocked_ip as b');
        $db->select('b.id', 'b.ip', 'b.mac', 'b.type');

        $db->addSelect(DB::raw('DATE_FORMAT(b.updated_at, "%d %b %Y %h:%i %p") as updated_at'));
        $db->addSelect(DB::raw('DATE_FORMAT(b.created_at, "%d %b %Y %h:%i %p") as created_at'));
        $db->addSelect(DB::raw('case when b.type = 1 then "IP" when b.type = 2 then "MAC" end as type'));
        $db->addSelect(DB::raw('case when (b.ip is not null) then b.ip else b.mac end as ip_mac'));
        $db->whereNull('b.deleted_at');
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $search_key = strtolower($search_key);
            $whereStr = '(b.ip LIKE "%%%1$s%%" OR b.mac LIKE "%%%1$s%%" OR DATE_FORMAT(b.created_at, "%%d %%b %%Y %%h:%%i %%p") LIKE "%%%1$s%%" OR DATE_FORMAT(b.updated_at, "%%d %%b %%Y %%h:%%i %%p") LIKE "%%%1$s%%"';
            $normalized_search_key = str_replace(' ', '', $search_key);
            if ($normalized_search_key == 'mac' || $normalized_search_key == 'm' || $normalized_search_key == 'a' || $normalized_search_key == 'c') {
                $whereStr .= ' OR b.type = 2';
            } elseif ($normalized_search_key == 'ip' || $normalized_search_key == 'i' || $normalized_search_key == 'p') {
                $whereStr .= ' OR b.type = 1';
            }
            $whereStr .= ')';
            $db->whereRaw(sprintf($whereStr, $search_key));
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
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    public function ajaxGetBlockedIp(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to get the IP/MAC'
        ];

        $obj = '';
        try {
            $obj = BlockedIp::findOrFail($id);

            if (empty($obj)) {
                return response()->json($return);
            }

            $return['data'] = $obj->only('id', 'type', 'ip', 'mac');
            $return['msg'] = null;
            $return['status'] = 'success';
            return response()->json($return);
        } catch (\Exception $e) {
            Log::info('ajaxGetIP: ' . $e->getMessage());
            return response()->json($return);
        }
    }

    public function editBlockIp(Request $request, $id)
    {
        $return = ['status' => 'failure', 'msg' => 'Unable to edit the IP'];

        $obj = '';
        try {
            $obj = BlockedIP::findOrFail($id);

            if (empty($obj)) {
                return response()->json($return);
            }
        } catch (\Exception $e) {
            return response()->json($return);
        }

        $data = $request->only('ip', 'type', 'mac');
        $rules = [
            'type' => 'required',
            'ip' => 'nullable|string',
            'mac' => 'nullable|string',
        ];
        $messages = [];

        $validate = Validator::make($data, $rules, $messages);

        if ($validate->fails()) {
            $v = $validate->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }
        $data['updated_by'] = Auth::user()->id;
        $obj->fill($data);

        if ($obj->save()) {
            $return['msg'] = 'Ip Address updated successfully';
            $return['status'] = 'success';
        }

        return response()->json($return);
    }

    public function ajaxDelete(Request $request, $id)
    {
        $return = ['status' => 'failure', 'msg' => 'Unable to delete the IP/MAC'];

        $obj = '';
        try {
            $obj = BlockedIP::where('id', $id)->first();
            if (empty($obj)) {
                return response()->json($return);
            }
            $obj->deleted_by = Auth::user()->id;
            $obj->save();
            $obj->delete();
            $return['status'] = 'success';
            $return['msg'] = 'IP/MAC has deleted successfully!';
            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($return);
        }
    }

    public function downloadReport(Request $request)
    {
        $db = DB::table('blocked_ip as b');
        $db->addSelect(DB::raw('case when b.type = 1 then "IP" when b.type = 2 then "MAC" end as type'));
        $db->addSelect(DB::raw('case when (b.ip is not null) then b.ip else b.mac end as ip_mac'));
        $db->addSelect(DB::raw('DATE_FORMAT(b.created_at, "%d %b %Y %h:%i %p") as created_at'));
        $db->addSelect(DB::raw('DATE_FORMAT(b.updated_at, "%d %b %Y %h:%i %p") as updated_at'));
        $db->Wherenull('deleted_at');

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if (isset($filters->search)) {
                $req['search'] = $filters->search;
            }

            if (isset($req['search']) && $search_key = trim($req['search'])) {
                $search_key = strtolower($search_key);
                $whereStr = '(b.ip LIKE "%%%1$s%%" OR b.mac LIKE "%%%1$s%%" OR DATE_FORMAT(b.created_at, "%%d %%b %%Y %%h:%%i %%p") LIKE "%%%1$s%%" OR DATE_FORMAT(b.updated_at, "%%d %%b %%Y %%h:%%i %%p") LIKE "%%%1$s%%"';
                $normalized_search_key = str_replace(' ', '', $search_key);
                if ($normalized_search_key == 'mac' || $normalized_search_key == 'm' || $normalized_search_key == 'a' || $normalized_search_key == 'c') {
                    $whereStr .= ' OR b.type = 2';
                } elseif ($normalized_search_key == 'ip' || $normalized_search_key == 'i' || $normalized_search_key == 'p') {
                    $whereStr .= ' OR b.type = 1';
                }
                $whereStr .= ')';
                $db->whereRaw(sprintf($whereStr, $search_key));
                $return['recordsFiltered'] = $db->count();
            }
        }

        $data = $db->get();
        $result = json_decode(json_encode($data, true), true);
        return Excel::download(new BlockedIPExport($result), 'BlockedIP.xlsx');
    }
}

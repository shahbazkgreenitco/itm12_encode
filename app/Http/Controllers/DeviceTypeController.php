<?php

namespace App\Http\Controllers;

use App\Exports\Device\DeviceTypeExport;
use App\Models\AssetType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use DB;
use Log;
use Validator;

class DeviceTypeController extends Controller
{
    public function index()
    {
        $return = ['status' => 'danger', 'msg' => trans('device_type.controller.permission_denied')];
        if (!Auth::user()->hasPermissionTo('DeviceTypeRead')) {
            $return['msg'] = trans('device_type.controller.permission_denied');
            return response()->json($return);
        }
        return view('device_type.index');
    }

    public function getDeviceTypes(Request $request)
    {
        $req = $request->all();

        $fields = array(
            'id' => 'h.id',
            'device_type' => 'h.name',
        );

        $db = DB::table('assets_types as h');
        $db->select('h.id', 'h.name', 'h.updated_at');
        $db->whereNull('h.deleted_at');
        $db->distinct('h.id');
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(h.name like "%%%1$s%%" or h.name like "%%%1$s%%" )', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['sorted_column_name']) && array_key_exists($req['sorted_column_name'], $fields) && in_array($req['sorted_direction'], ["asc", "desc"])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
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

    public function ajaxAddDeviceType(Request $request)
    {
        try {
            $return = [
                'status' => 'failure',
                'msg' => trans('device_type.controller.unable_to_add'),
            ];

            if (!Auth::user()->hasPermissionTo('DeviceTypeAdd')) {
                $return['msg'] = trans('device_type.controller.permission_denied');
                return response()->json($return);
            }

            $rules = [
                'name' => ['required', 'string', 'max:200', 'regex:/^(?=.*[a-zA-Z])[\w\s]*$/'],
            ];
            $messages = [];
            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {
                $v = $validate->errors()->toArray();
                $e = array_shift($v);
                $return['msg'] = $e[0];
                return response()->json($return);
            }

            DB::beginTransaction();
            $obj = new AssetType();
            $obj->name = $request->input('name');

            if ($obj->save()) {
                DB::commit();
                $return['status'] = 'success';
                $return['msg'] = trans('device_type.controller.add_success');
            } else {
                throw new \Exception(trans('device_type.controller.add_failed'));
            }
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ajaxAddDeviceType: ' . $e->getMessage());
            return response()->json($return);
        }
    }

    public function ajaxGetDeviceType(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => trans('device_type.controller.unable_to_get')
        ];

        $obj = '';
        try {
            $obj = AssetType::findOrFail($id);
            $return['data'] = $obj;
            $return['msg'] = null;
            $return['status'] = 'success';
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error('ajaxGetDeviceType: ' . $e->getMessage());
            return response()->json($return);
        }
    }

    /* to delete Device Type in ajax call */

    public function deleteDeviceType(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => trans('device_type.controller.unable_to_delete')
        ];
        if (!Auth::user()->hasPermissionTo('DeviceTypeDelete')) {
            $return['msg'] = trans('device_type.controller.permission_denied');
            return response()->json($return);
        }

        $obj = '';
        try {
            $obj = AssetType::withTrashed()->find($id);

            if (!$obj) {
                $return['status'] = 'failure';
                $return['msg'] = trans('device_type.controller.device_type_not_found');
                return response()->json($return);
            }

            if ($obj->trashed()) {
                $return['status'] = 'failure';
                $return['msg'] = trans('device_type.controller.already_deleted');
                return response()->json($return);
            }
            $obj->delete();
            $return['status'] = 'success';
            $return['msg'] = trans('device_type.controller.delete_success');
            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($return);
        }
    }

    public function ajaxEditDeviceType(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => trans('device_type.controller.unable_to_edit'),
        ];

        if (!Auth::user()->hasPermissionTo('DeviceTypeEdit')) {
            return response()->json([
                'status' => 'failure',
                'msg' => trans('device_type.controller.permission_denied')
            ]);
        }

        try {
            $assetType = AssetType::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failure',
                'msg' => trans('device_type.controller.not_found')
            ]);
        }

        $data = $request->only('name');
        $rules = [
            'name' => ['required', 'string', 'max:200', 'regex:/^(?=.*[a-zA-Z])[\w\s]*$/'],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failure',
                'msg' => $validator->errors()->first()
            ]);
        }

        try {
            $assetType->name = $data['name'];
            $assetType->save();

            return response()->json([
                'status' => 'success',
                'msg' => trans('device_type.controller.edit_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failure',
                'msg' => trans('device_type.controller.update_error')
            ]);
        }
    }

    //
    public function deviceTypeExportExcel(Request $request)
    {
        if (!Auth::user()->hasPermissionTo('DeviceTypeDownload')) {
            return response()->json([
                'status' => 'failure',
                'msg' => trans('device_type.controller.permission_denied')
            ]);
        }
        $query = AssetType::select('id', 'assets_types.name');
        $return['recordsTotal'] = $query->count();
        $return['recordsFiltered'] = $query->count();

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if (isset($filters->search)) {
                $req['search'] = $filters->search;
                $whereStr = sprintf('( assets_types.name like "%%%1$s%%")', $req['search']);
                $query->whereRaw($whereStr);
            }
        }
        $results = $query->get();
        if ($search_key = trim($request->search)) {
            $return['recordsFiltered'] = $return['recordsTotal'];
        }

        $result = json_decode(json_encode($results, true), true);
        $title_row = ['ID', 'Device Type'];
        return Excel::download(new DeviceTypeExport($result, $title_row), 'Device_type.xlsx');
    }

    public function getDeviceTypeByQuery(Request $request) {
        $return = [];

        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);
            
            $db = AssetType::select('id', 'name as text');

            if($search) {
                $db->where('name', 'like', '%' . $search . '%');
            }

            $count = $db->count();
            $db->skip($skip)->take(20);
            $result = $db->get();

            $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
            $return['results'] = count($result) ? $result->toArray() : [];
            return $return;

        } catch (\Exception $e) {
            Log::error('getDeviceTypeByQuery Error: ' . $e->getMessage());
        }

    }
}

<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Helpers\Common as CommonHelper;
use Illuminate\Http\Request;
use App\Models\AssetAllocationType;
Use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Config\AllocationTypesExport;
Use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AllocationTypeController extends Controller {

    public function allocationTypeList(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('config.allocation_type.permission_denied')];
        if(! Auth::user()->hasPermissionTo('AllocationTypeRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        return view("config.asset_allocation_type.index");
    }

    public function ajaxAllocationTypeList(Request $request) {
        $return = array(
            "status" => "failure",
            "msg" => trans('config.allocation_type.unable_display')
        );
        try {
            $req = $request->all();
            $return = array(
                "draw" => $req['draw']
            );
            $fields = array(
                'id' => 'itm_asset_allocation_type.id',
                'name' => 'itm_asset_allocation_type.name',
                'created_at' => 'itm_asset_allocation_type.created_at',
                'updated_at' => 'itm_asset_allocation_type.updated_at',
            );
        
            $mysqlDateTimeFormat = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
            $query = AssetAllocationType::select('itm_asset_allocation_type.id','itm_asset_allocation_type.name')
                    ->addSelect(DB::raw("DATE_FORMAT(itm_asset_allocation_type.created_at, '{$mysqlDateTimeFormat}') as created_at_format"))
                    ->addSelect(DB::raw("DATE_FORMAT(itm_asset_allocation_type.updated_at, '{$mysqlDateTimeFormat}') as updated_at_format"));
            $return['recordsTotal'] = $query->count();
            $return['recordsFiltered'] = $return['recordsTotal'];

            if( isset($req["search"]["value"]) && $req["search"]["value"]!=null ) {
                $search_key=$req["search"]["value"];
                $query->Where('itm_asset_allocation_type.name', 'like', '%' . $search_key . '%')
                ->orWhere('itm_asset_allocation_type.id', 'like', '%' . $search_key . '%')
                ->orWhereRaw('DATE_FORMAT(itm_asset_allocation_type.created_at, "%d %b %Y %h:%i %p") like ?', ['%' . $search_key . '%'])
                ->orWhereRaw('DATE_FORMAT(itm_asset_allocation_type.updated_at, "%d %b %Y %h:%i %p") like ?', ['%' . $search_key . '%']);
                $return['recordsFiltered'] = $query->count();
            }

            if (isset($req['sorted_column_name']) && array_key_exists($req['sorted_column_name'], $fields) && in_array($req['sorted_direction'], ["asc", "desc"])) {
                $query->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
            } else {
                $query->orderBy('itm_asset_allocation_type.created_at', 'desc');
            }
            $skip = 0;
            $take = 10;
            if( isset($req["start"]) && isset($req["length"]) ) {
                $skip = (int) $req["start"];
                $take = (int) $req["length"];
            }
            $query->skip($skip);
            $query->take($take);

            $data = $query->get();
            $return['data'] = array();
            foreach($data as $d) {
                $return['data'][] = array('a' => $d);
            }
        }
        catch(\Exception $e) {
            Log::error($e->getmessage());
        }
        return response()->json($return);
    }

    public function createAllocationType(Request $request) {
        $return = ["status" => "fail", "msg" => trans('config.allocation_type.unable_new')];
        $input = $request->all();

        if(! Auth::user()->hasPermissionTo('AllocationTypeAdd')) {
            return redirect('dashboard')->with("msg", $return);
        }
        try {
            $rules = [
                'name' => 'required|string|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            DB::beginTransaction();
            $data = $request->only("name");
            $ca = new AssetAllocationType;
            $ca->fill($data);
            $ca->created_by = Auth::user()->id;
            $ca->save();
            $return["status"] = "success";
            $return["msg"] = trans('config.allocation_type.allocation_type_create');
            DB::commit();
            Log::info("createAllocationType id:" . $ca->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("createAllocationType: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function editAllocationType(Request $request,$id) {
        $input = $request->all();
        $return = array(
            "status" => "fail",
            "msg" => trans('config.allocation_type.not_editable')
        );
        try {
            $ea = AssetAllocationType::select('itm_asset_allocation_type.id','itm_asset_allocation_type.name','itm_asset_allocation_type.created_at','itm_asset_allocation_type.updated_at','itm_asset_allocation_type.deleted_at')->withTrashed()->where('itm_asset_allocation_type.id', '=', $id)->first();

            if(!$ea) {
                return response()->json($return);
            }

            if($ea->trashed()) {
                $return['status'] = 'fail';
                $return['msg'] = trans('config.allocation_type.allocation_already_deleted');
                return response()->json($return);
            }

            $return['status'] = 'success';
            $return['msg'] = '';
            $return['data'] =  $ea;
            Log::info("editAllocationType:" . $ea->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error(" editAllocationType: ". $e->getMessage());
        }
        return response()->json($return);
    }

    public function updateAllocationType(Request $request) {
        $return = ["status" => "fail", "msg" => trans('config.allocation_type.unable_edit')];
        $input = $request->all();
        if(! Auth::user()->hasPermissionTo('AllocationTypeEdit')) {
            return redirect('dashboard')->with("msg", $return);
        }
        try {
            $rules = [
                'name' => 'nullable|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            DB::beginTransaction();
            $data = $request->only("name");
            $ua = AssetAllocationType::find($input['id']);
            $ua->fill($data);
            $ua->updated_by = Auth::user()->id;
            $ua->save();
            $return['status'] = 'success';
            $return['msg'] = trans('config.allocation_type.allocation_type_update');
            $return['data'] =  $ua;
            DB::commit();
            Log::info("updateAllocationType :" . $ua->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("updateAllocationType: ". $e->getMessage());
            Log::error($e->message());
        }
        return response()->json($return);

    }

    public function deleteAllocationType(Request $request, $id) {
        $return = array(
            "status" => "failure",
            "msg" => trans('config.allocation_type.unable_delete')
        );
        if(! Auth::user()->hasPermissionTo('AllocationTypeDelete')) {
            return redirect('dashboard')->with("msg", $return);
        }
        try {
            DB::beginTransaction();
            $da = AssetAllocationType::where("id","=", $id)->first();
            if(!$da) {
                $return['msg'] = trans('config.allocation_type.allocation_already_deleted');
                return response()->json($return);
            }
            $da->delete();
            $return["status"] = "success";
            $return["msg"] = trans('config.allocation_type.allocation_type_Delete');
            DB::commit();
            Log::info("deleteAllocationType:" . $da->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("deleteAllocationType: ". $e->getMessage());
        }
        return response()->json($return);
    }

    public function exportAllocationTypes(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('config.allocation_type.permission_denied')];
        if (!Auth::user()->hasPermissionTo('AllocationTypeDownload')) {
            return redirect('dashboard')->with("msg", $return);
        }

        try {
            $query = AssetAllocationType::select('id', 'name')
                ->addSelect(DB::raw('DATE_FORMAT(created_at, "%d %b %Y %h:%i %p") as created_at_format'))
                ->addSelect(DB::raw('DATE_FORMAT(updated_at, "%d %b %Y %h:%i %p") as updated_at_format'));;

            if ($request->has('q') && !empty($request->q)) {
                $request_filters = base64_decode($request->input('q'));
                $filter = json_decode($request_filters, true);

                if (!empty($filter['search'])) {
                    $search_key = trim($filter['search']);

                    $query->where(function ($q) use ($search_key) {
                        $q->where('name', 'like', '%' . $search_key . '%')
                            ->orWhere('id', 'like', '%' . $search_key . '%')
                            ->orWhereRaw('DATE_FORMAT(created_at, "%d %b %Y %h:%i %p") like ?', ['%' . $search_key . '%'])
                            ->orWhereRaw('DATE_FORMAT(updated_at, "%d %b %Y %h:%i %p") like ?', ['%' . $search_key . '%']);
                    });
                }
            }

            $results = $query->get();

            if ($results->isEmpty()) {
                return redirect()->back()->with("msg", ["status" => "danger", "msg" => trans('config.allocation_type.no_matching_records')]);
            }
            $keys = ['ID', 'Name', 'Created At', 'Updated At'];

            return Excel::download(new AllocationTypesExport($results->toArray(), $keys), 'Asset_Allocation_Types.xlsx');
        } catch (\Exception $e) {
            Log::error("exportAllocationTypes Error: " . $e->getMessage());
            return redirect()->back()->with("msg", ["status" => "danger", "msg" => trans('config.allocation_type.something_wrong_exporting')]);
        }
    }

    public function getAllocationByQuery(Request $request) {
        $return = [];

        try {
            $search = $request->input('search', '');
            $page = $request->input('page', 1);
            $skip = (($page * 20) - 20);

            $db = AssetAllocationType::select('id', 'name as text');

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
            Log::error('getAllocationByQuery Error: ' . $e->getMessage());
        }
    }

}

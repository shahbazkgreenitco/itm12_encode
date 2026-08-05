<?php

namespace App\Http\Controllers;

use App\Exports\InternalPlaceExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Place;
use App\Models\Company;
use App\Models\UserDetails;
use App\Imports\PlaceImport;
use App\Models\Transfer;
use App\Helpers\Common;
use App\Helpers\Common as CommonHelper;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use DB;
use Auth;
use stdClass;
use App\Rules\UniquePlaceLocation;

class PlaceController extends Controller {

    public function getIndex()
    {
        $return = ['status' => 'danger', 'msg' => trans('internal_place.controller.permission_denied')];
        if (!Auth::user()->hasPermissionTo('PlaceRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = new stdClass;
        $company = null;
        $userDatail = UserDetails::select('dashboard_company_id')->where('user_id', Auth::user()->id)->first();
        $companyId = CommonHelper::getAccessibleCompanyIds();
        if ($userDatail) {
            $company = Company::select('id', 'name as text')->where('id', $userDatail->dashboard_company_id)->get();
        } else {
            $company = Company::select('id', 'name as text')->whereIn('id', $companyId)->get();
        }

        $vd->companies = $company;
        // $vd->companies = Company::select('id', 'name as text')->orderBy('name')->get();
        // $vd->locations = Location::select('id', 'name as text')->orderBy('name')->get();
        return view("places.index")->with('vd', $vd);
    }

    /* ajax for list of places */
    public function ajaxIndex(Request $request) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            'a.id' => 'p.id',
            'a.place' => 'p.place',
            'a.location' => 'l.name',
            'a.branch_code'=>'p.branch_code',
            'a.company' => 'c.name',
            'a.last_updated_at' => 'p.updated_at'
        );

        $db = DB::table('places as p');
        $db->leftJoin('companies as c', 'c.id', '=', 'p.company_id');
        $db->leftJoin('locations as l', 'l.id', '=', 'p.location_id');

        $db->select('p.id', 'p.place', 'c.name as company', 'l.name as location', 'p.branch_code');
        $db->addSelect(DB::raw('DATE_FORMAT(p.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));

        $db->whereNull('p.deleted_at');
        $company = UserDetails::where('user_id', Auth::id())->value('dashboard_company_id');
        $companyId = CommonHelper::getAccessibleCompanyIds();
        if (!empty($company) && $company != 0) {
            $db->where('p.company_id', $company);
        } else {
            $db->whereIn('p.company_id', $companyId);
        }
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
            $whereStr = sprintf('(p.place like "%%%1$s%%" or p.branch_code like "%%%1$s%%" or c.name like "%%%1$s%%" or l.name like "%%%1$s%%" or DATE_FORMAT(p.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }
    
        if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ['asc', 'desc'])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        }

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

    /* to add the place via ajax */
    public function ajaxAdd(Request $request) {
        $return = [
            'status' => 'failure',
            'msg' => trans('internal_place.controller.unable_to_add')
        ];
        if (!Auth::user()->hasPermissionTo('PlaceAdd')) {
            $return["msg"] = trans('internal_place.controller.permission_denied');
            return response()->json($return);
        }

        $data = $request->only('place', 'location_id', 'company_id', 'branch_code'); 
        $rules = [
            'place' => ['required', new UniquePlaceLocation($request->location_id, $request->company_id)],
            'location_id' => 'required|exists:locations,id',
            'company_id' => 'required|exists:companies,id'
        ];
        $messages = [];
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
        }
        else {            
            $obj = new Place;
            $obj->fill($data);
            if ($obj->save()) {
                $return["msg"] = trans('internal_place.controller.add_success');
                $return["status"] = "success";
            }
        }

        return response()->json($return);
    }

    /* get place in ajax call */
    public function ajaxGet(Request $request, $id) {
        $return = [
            'status' => 'failure',
            'msg' => trans('internal_place.controller.unable_to_get')
        ];

        $obj = "";
        try {
            $obj = Place::findOrFail($id);
            $data = [];
            $data['data'] = $obj->only('id', 'company_id', 'place', 'location_id', 'branch_code');
            if ($obj->company_id) {
                $getCompany = Company::where("id", $obj->company_id)->select("id", "name as text")->first();
                $data["dropdown"]["company"] = $getCompany && $getCompany->exists ? $getCompany->toArray() : null;
            }
            if ($obj->location_id) {
                $getLocation = Location::where("id", $obj->location_id)->select("id", "name as text")->first();
                $data["dropdown"]["location"] = $getLocation && $getLocation->exists ? $getLocation->toArray() : null;
            }

            $return['data'] = $data;
            $return['msg'] = null;
            $return['status'] = 'success';
        }
        catch(\Exception $e) {
            return response()->json($return);        
        }
        
        return response()->json($return);
    }

    /* to edit the place by ajax call */
    public function ajaxEdit(Request $request, $id) {
        $return = [
            'status' => 'failure',
            'msg' => trans('internal_place.controller.unable_to_edit')
        ];
        if (!Auth::user()->hasPermissionTo('PlaceEdit')) {
            $return["msg"] = trans('internal_place.controller.permission_denied');
            return response()->json($return);
        }
        $obj = "";
        try {
            $obj = Place::findOrFail($id);
        }
        catch(\Exception $e) {
            return response()->json($return);        
        }

        $data = $request->only('place', 'company_id', 'location_id', 'branch_code');
        $rules = [
            'place' => [
                'required',
                Rule::unique('places')
                    ->where('location_id', $request->location_id)
                    ->where('company_id', $request->company_id)
                    ->ignore($id)
            ],
            'location_id' => 'required|exists:locations,id',
            'company_id' => 'required|exists:companies,id'
        ];
        $messages = [];
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return); 
        }
        $underTransferData = Transfer::where('transfer_to', $obj->location_id)->where('internal_place', $id)->where('status', 1)->count();
        if ($underTransferData > 0) {
            $return["msg"] = trans('internal_place.controller.attached_to_transfer');
            $return["status"] = "error";
            return response()->json($return);
        }
        $data["updated_by"] = Auth::user()->id;
        $obj->fill($data);
        if ($obj->save()) {
            $return["msg"] = trans('internal_place.controller.edit_success');
            $return["status"] = "success";
        }

        return response()->json($return);
    }
    
    /* to delete the Place by ajax call */
    public function ajaxDelete(Request $request, $id)
    {
        $return = ['status' => 'failure', 'msg' => trans('internal_place.controller.unable_to_delete')];
        if (!Auth::user()->hasPermissionTo('PlaceDelete')) {
            $return["msg"] = trans('internal_place.controller.permission_denied');
            return response()->json($return);
        }
        $record = Place::where('id','=',$id)->withCount(['assets','mapped_place','assigned_assets','assigned_accessories','tracked_device','place_assets','place_users'])->first();
        if (empty($record))
            return response()->json(['status' => 'error', 'msg' => 'Internal Place is aleady deleted. Please refresh the page.']);

        if ($record->assets_count)
            return response()->json(['status' => 'error', 'msg' => trans('internal_place.controller.has_assets')]);
        if ($record->mapped_place_count)
            return response()->json(['status' => 'error', 'msg' => trans('internal_place.controller.has_network_mapped')]);
        if ($record->assigned_assets_count)
            return response()->json(['status' => 'error', 'msg' => trans('internal_place.controller.has_devices_assigned')]);
        if ($record->assigned_accessories_count)
            return response()->json(['status' => 'error', 'msg' => trans('internal_place.controller.has_accessories_assigned')]);
        if ($record->tracked_device_count)
            return response()->json(['status' => 'error', 'msg' => trans('internal_place.controller.has_tracked_devices')]);
        if ($record->place_assets_count)
            return response()->json(['status' => 'error', 'msg' => trans('internal_place.controller.has_tracked_devices')]);
        if ($record->place_users_count)
            return response()->json(['status' => 'error', 'msg' => trans('internal_place.controller.has_tracked_users')]);

        $obj = "";
        try {
            $obj = Place::findOrFail($id);
            $obj->deleted_by = Auth::id();
            $obj->save();
            $obj->delete();
            $return["status"] = "success";
            $return["msg"] = trans('internal_place.controller.delete_success');
            return response()->json($return);
        }
        catch(\Exception $e) {
            return response()->json($return);        
        }
    }

    public function placeImport(Request $request)
    {
        $return = ["msg" => trans('internal_place.controller.unable_to_import'), "status" => "danger"];
        if (!Auth::user()->hasPermissionTo('PlaceImport')) {
            $return["msg"] = trans('internal_place.controller.permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        if ($request->isMethod('post') && !$request->import_file) {
            $return["msg"] = trans('internal_place.controller.error_place_upload');
            $request->session()->flash("msg", $return);
        } else {
            if(! $request->isMethod('post')) {
                return view("places.import");
            }
            if ( $request->isMethod('post') && $request->import_file ) {
                $path = $request->file('import_file')->getRealPath();
                $import = new PlaceImport($request);
                Excel::import($import, $request->import_file);
                $response = $import->data;
                // return view("places.import")->with([ 'fail' => $response['fail'], 'fail_msgs' => $response['fail_msgs'] ]);
                return redirect('internal-places/import')->with(['fail' => $response['fail'], 'fail_msgs' => $response['fail_msgs']]);
            }
        }
        return view("places.import");
    }

    public function getInternalPlaceByAjax(Request $request, $id) {
        $ids = isset($id) ? explode(',', $id) : [];
        $companyId = $request->company_id;
        $return['results'] = array();
        try {
            $places = Place::select('places.id', DB::raw('CONCAT(locations.name, " - ", places.place) as text'))
                                ->leftJoin('locations', 'places.location_id', 'locations.id')
                                ->whereIn('places.location_id', $ids)
                                ->when(!empty($companyId) && $companyId !== 'null', function ($query) use ($companyId) {
                                    $query->where('places.company_id', $companyId);
                                })->get();
            if ($places->count()) {
                $return['results'] = $places;
            }
            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
    
    public function getInternalPlaceByLocation(Request $request, $id) {
        $return['results'] = array();
        try {
            $ids = explode(',', $id);
            $search = trim($request->get('search'));
            $places = Place::select('id', 'place as text')->whereIn('location_id', $ids)->get();
            if (!empty($search)) {
                $places->where('place', 'like', '%' . $search . '%');
            }
            if ($places->count()) {
                $return['results'] = $places;
            }
            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function ajaxGetInternalPlace(Request $request) {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);
        $places = Place::select('places.id', DB::raw('CONCAT(locations.name, " - ", places.place) as text'))->leftJoin('locations', 'places.location_id', '=', 'locations.id')->orderBy('locations.name','asc');

        if(isset($request->location) && !empty($request->location)) {
            $places->whereIn('location_id', $request->location);
        }

        if($search) {
            $places->where(function($query) use ($search) {
                $query->where('places.place', 'like', '%' . $search . '%')
                    ->orWhere('locations.name', 'like', '%' . $search . '%');
            });
        }

        if (isset($request->location_id) && $request->location_id != null) {
            $places->where('places.location_id', $request->location_id);
        }
        $count = $places->count();
        $places->skip($skip)->take(20);
        $result = $places->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    /* ajax for export of internal places */
    public function placeExportExcel(Request $request) {
        $db = DB::table('places as p');
        $db->leftJoin('companies as c', 'c.id', '=', 'p.company_id');
        $db->leftJoin('locations as l', 'l.id', '=', 'p.location_id');

        $db->select('p.id','p.place','l.name as location','c.name as company', 'p.branch_code');
        $db->addSelect(DB::raw('DATE_FORMAT(p.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));

        $company = UserDetails::where('user_id', Auth::id())->value('dashboard_company_id');
        $companyId = Common::getAccessibleCompanyIds();
        if (!empty($company) && $company != 0) {
            $db->where('p.company_id', $company);
        } else {
            $db->whereIn('p.company_id', $companyId);
        }

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if(isset($filters->search)) {
                $req["search"] = $filters->search;
                if( isset($req["search"]) && $req["search"] !="" && $search_key = trim($req["search"]) ) {
                    $whereStr = sprintf('(p.place like "%%%1$s%%" or c.name like "%%%1$s%%" or l.name like "%%%1$s%%" or DATE_FORMAT(p.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                    $db->whereRaw($whereStr);
                }
            }
        }

        $data = $db->get();
        $return['data'] = array();
        foreach($data as $d) {
            $return['data'][]= array('a' => $d);
        }

        $title_row = [trans('internal_place.table.id'), trans('internal_place.table.place_name'), trans('internal_place.table.location'), trans('internal_place.table.company'), trans('internal_place.table.branch_code'), trans('internal_place.table.updated_at'), ];
        return Excel::download(new InternalPlaceExport($return['data'], $title_row), 'InternalPlace.xlsx');
    }
}

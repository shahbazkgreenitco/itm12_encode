<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Company;
use App\Models\Location;
use App\Models\UserDetails;
use App\Helpers\Common as CommonHelper;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Countries;
use App\Models\State;
use App\Models\City;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LocationExport;
use App\Imports\LocationImport;
use App\Models\Consumable;

class LocationController extends Controller
{
    public function getIndex()
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('LocationRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $company = null;
        if (Auth::user()->company_id) {
            $company = Company::select('id', 'name as text')->where('id', Auth::user()->company_id)->first();
        }
        return view("locations.index")->with("objLocation", new Location())->with("company_id", $company);
    }



    public function addLocation(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('LocationAdd')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $objLocation = new Location;
        if ($request->isMethod('post')) {
            $rules = [
                'name' => [
                    'required|clean_text_only',
                    Rule::unique('locations')
                ],
                'parent_id' => 'nullable',
                'address' => 'required|clean_text_only|digits:80',
                'address2' => 'nullable|clean_text_only',
                'currency' => 'sometimes',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'zip' => 'nullable|numeric|digits:20',
                'branch_code' => 'nullable|string|clean_text_only',
                'zone' => 'nullable|string|clean_text_only'
            ];
            $messages = [
                'name.required' => 'Please provide Location Name.',
                'city.required' => 'Please provide city.',
                'country.required' => 'Please select Country.',
                'address.required' => 'Please provide street address.',
                'state.required' => 'Please provide state.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator->messages());
            } else {
                $data = $request->validate($rules);
                $data['user_id'] = Auth::user()->id;
                if ($objLocation->addLocation($data)) {
                    $msg["msg"] = "Location has been added successfully";
                    $msg["status"] = "success";
                    return redirect()->action("LocationController@getIndex")->with("msg", $msg);
                }
            }
        }
        return view("locations.add")->with("location", $objLocation); //->with("company", new Company)->with("objLocation", new Location());
    }

    public function editLocation($id, Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('LocationEdit')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $objLocation = Location::find($id);

        if (empty($objLocation)) {
            return redirect()->action("LocationController@getIndex")->with("msg", array(
                "status" => "warning",
                "msg" => "No Location found for given data"
            ));
        }

        if ($request->isMethod('post')) {
            $rules = [
                'name' => [
                    'required|clean_text_only',
                    Rule::unique('locations')->ignore($id)
                ],
                'parent_id' => '',
                'address' => 'required|clean_text_only',
                'address2' => 'nullable|clean_text_only',
                'currency' => 'sometimes',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'zip' => 'nullable|numeric ',
                'branch_code' => 'nullable|string|clean_text_only',
                'zone' => 'nullable|string|clean_text_only'
            ];
            $messages = [
                'name.required' => 'Please provide Location Name.',
                'city.required' => 'Please provide city.',
                'country.required' => 'Please select Country.',
                'address.required' => 'Please provide street address.',
                'state.required' => 'Please provide state.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator->messages());
                // return array('status'=>'Error','errors'=>$validator->errors()->getMessages());
            } else {
                $data = $request->validate($rules);
                if ($objLocation->updateLocationById($data, $id)) {
                    $return["msg"] = "Location has been updated successfully";
                    $return["status"] = "success";
                    return redirect()->action("LocationController@getIndex")->with("msg", $return);
                }
            }

            $request->session()->flash("msg", $return);
        }

        return view("locations.edit")->with("location", $objLocation);
    }

    public function getLocationByQuery(Request $request)
    {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $company_id = $request->input('company_id',null);
        $skip = (($page * 20) - 20);
        $take = 10;

        if (isset($request->start) && isset($request->length)) {
            $skip = (int) $request->start;
            $take = (int) $request->length;
        }

        $db = Location::select("id", "name as text");
        if(isset($request->company_id)){
            $db->where("company_id",$request->company_id);
        }
        if($search) {
            if(is_array($search)) {
                $db->where("name", "like", "%" . $search['value'] . "%");
            } else {
                $db->where("name", "like", "%" . $search . "%");
            }
        }
        if(isset($request->type) && $request->type == 'filter'){
            if(!empty($request->city_id)){
                $cityIds = is_array($request->city_id) ? $request->city_id : array($request->city_id);
                $db->whereIn('city_id', $cityIds);
            } elseif(!empty($request->state_id)){
                $stateIds = is_array($request->state_id) ? $request->state_id : array($request->state_id);
                $db->whereIn('state_id', $stateIds);
            } elseif(!empty($request->zone)){
                $zone = is_array($request->zone) ? $request->zone : array($request->zone);
                $id = Location::whereIn('zone', $zone)->pluck('id')->toArray() ?? [];
                $db->whereIn('id', $id);
            } elseif(!empty($request->country_id)){
                $countryIds = is_array($request->country_id) ? $request->country_id : array($request->country_id);
                $db->whereIn('country_id', $countryIds);
            }
        }

        if (isset($request->consumable) && $request->consumable == 1) {
            $location_id = Consumable::whereNotNull('location_id')->distinct()->pluck('location_id');
            $db->whereIn('id', $location_id)->orderBy('name', 'ASC');
        }
        if (isset($request->department_id) && $request->department_id > 0) {
            $loc_id = Consumable::whereNotNull('location_id')->where('department_id', $request->department_id)->distinct()->pluck('location_id');
            $db->whereIn('id', $loc_id)->orderBy('name', 'ASC');
        }
        $return['recordsFiltered'] = $db->count();
        $count = $db->count();
        if (isset($request->start) && isset($request->length)) {
            $db->skip($skip)->take($take);
        } else {
            $db->skip($skip)->take(20);
        }
        $return['recordsTotal'] = $db->count();
        $result = $db->get();
        $return["draw"] = date('is');

        $return["pagination"] = array("more" => ($count - ($page * 10)) > 0 ? true : false);
        $return["data"] = count($result) ? $result->toArray() : [];
        $return["results"] = count($result) ? $result->toArray() : [];
        $return['data'] = array();
        foreach ($result as $d) {
            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }

    public function ajaxIndex(Request $request)
    {
        $columns = [
            'a.id'                   => 'locations.id',
            'a.name'                 => 'locations.name',
            'a.company_name'         => 'companies.name',
            'a.parent_location.name' => 'parent_id',
            'a.branch_code'          => 'locations.branch_code',
            'a.zone'                 => 'locations.zone',
            'a.address'              => 'locations.address',
            'a.city_name.name'       => 'cities.name',
            'a.state_name.name'      => 'states.name',
            'a.country_name.name'    => 'countries.name',
            'a.zip'                  => 'locations.zip',
            'a.updated_at'           => 'locations.updated_at',
        ];

        $records = Location::with(['parentLocation', 'country', 'stateName', 'cityName', 'countryName'])
            ->leftJoin('companies', 'companies.id', '=', 'locations.company_id')
            ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
            ->leftJoin('states', 'states.id', '=', 'locations.state_id')
            ->leftJoin('countries', 'countries.id', '=', 'locations.country_id')
            ->select(
                'locations.*',
                'companies.name as company_name',
                'companies.id as company_id',
                'cities.name as city_name',
                'states.name as state_name',
                'countries.name as country_name'
            );
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $records->where(function ($q) use ($search) {
                $q->where('locations.name', 'like', "%{$search}%")
                    ->orWhere('locations.branch_code', 'like', "%{$search}%")
                    ->orWhere('locations.zone', 'like', "%{$search}%")
                    ->orWhere('locations.address', 'like', "%{$search}%")
                    ->orWhere('locations.address2', 'like', "%{$search}%")
                    ->orWhere('locations.zip', 'like', "%{$search}%")
                    ->orWhereHas('parentLocation', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('companyName', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('countryName', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('stateName', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('cityName', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }
        $return = array(
            "draw" => intval($request->draw)
        );
        $company = UserDetails::where('user_id', Auth::id())->value('dashboard_company_id');
        $companyId = CommonHelper::getAccessibleCompanyIds();
        if (!empty($company) && $company != 0) {
            $records->where('company_id', $company);
        } else {
            $records->whereIn('company_id', $companyId);
        }
        $return['recordsTotal'] = Location::count();
        $return['recordsFiltered'] = $records->count();
        if (isset($request->sorted_column_name) && isset($columns[$request->sorted_column_name]) && in_array($request->sorted_direction, ['asc', 'desc'])) {
            if ($columns[$request->sorted_column_name] == 'locations.zip') {
                $records->orderByRaw('CAST(locations.zip AS UNSIGNED) ' . $request->sorted_direction);
            } else {
                $records->orderBy($columns[$request->sorted_column_name],$request->sorted_direction);
            }
        } else {
            $records->orderBy('locations.name', 'asc');
        }
        $data = $records->skip($request->start)->take($request->length)->get();
        $return['data'] = array();
        foreach ($data as $d) {
            if (empty($d->address))  $d->address = '';
            if (empty($d->address2))  $d->address2 = '';
            if (empty($d->state))  $d->state = '';
            $company = Company::select('name as company_name')->where('id', $d->company_id)->first();
            $d->company_name = $company ? $company->company_name : '';
            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }


    public function deleteLocation($id)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('LocationDelete')) {
            return response()->json($return);
        }
        // $splr = Supplier::where('id','=',$id)->withCount(['assets','asset_maintenances','licenses']);
        $record = Location::where('id', '=', $id)->withCount(['users', 'deviceLogs', 'accessories', 'consumables', 'components', 'assets', 'mappedlocation', 'places', 'assetlogs', 'interact_caches', 'interact_records', 'tracked_device'])->first();

        if (empty($record))
            return response()->json(['status' => 'error', 'msg' => 'Location Not Found. Please refresh the page.']);

        if ($record->users_count)
            return response()->json(['status' => 'error', 'msg' => 'Some users exist in this location .Please detach them and try again !!']);
        if ($record->accessories_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some accessories for this location. System is unable to remove this location !!']);
        if ($record->consumables_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some consumables for this location. System is unable to remove this location !!']);
        if ($record->components_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some components for this location. System is unable to remove this location !!']);
        if ($record->assets_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some assets for this location. System is unable to remove this location !!']);
        if ($record->mappedlocation_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some Network Mapped Locations for this location. System is unable to remove this location !!']);
        if ($record->places_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some Places for this location. System is unable to remove this location !!']);
        /*if($record->assetlogs_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some device logs for this location. System is unable to remove this location !!']);
        if($record->interact_caches_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some assets for this location. System is unable to remove this location !!']);
        if($record->interact_records_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some asset records for this location. System is unable to remove this location !!']);*/
        if ($record->tracked_device_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some tracked devices in this location. System is unable to remove this location !!']);
        if ($record->tracked_location_count)
            return response()->json(['status' => 'error', 'msg' => 'There are some devices mapped in this location. System is unable to remove this location !!']);

        $record->delete();
        if ($record->trashed()) {
            $record->deleted_by = Auth::user()->id;
            $record->save();
        }
        return response()->json(['status' => 'success', 'msg' =>  trans('config.location_fields.deleted_success') ]);
    }

    /* to add the location via ajax */
    public function ajaxAddLocation(Request $request)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to add the Location'
        ];
        if (!Auth::user()->hasPermissionTo('LocationAdd')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only('name', 'parent_id', 'address', 'address2', 'currency', 'zip', 'branch_code', 'zone', 'location_user', 'country_id', 'state_id', 'city_id', 'company_id');
        $data['location_user_id'] = isset($data) && !empty($data['location_user']) ? $data['location_user'] : null;//changing key becouse it wont get stored in table
        if(isset($data['location_user_id']) && !empty($data['location_user_id'])){
            unset($data['location_user']);//unsetting invalid key so it wont give error on storing user.
        }
        $rules = [
            'name' => [
                'required',
                'clean_text_only',
                Rule::unique('locations')
                ->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id)
                                ->whereNull('deleted_at');//2 locations can have same name in 2 diff companies.
                }),
            ],
            'parent_id' => 'nullable',
            'address' => 'required|clean_text_only|max:255',
            'address2' => 'nullable|clean_text_only|max:255',
            'currency' => 'sometimes',
            'city_id' => 'required',
            'state_id' => 'required',
            'country_id' => 'required',
            'zip' => 'nullable|string|max:20',
            'branch_code' => 'nullable|string|clean_text_only',
            'zone' => 'nullable|string|clean_text_only'
        ];
        $messages = [
            'name.required' => 'Please provide Location Name.',
            'name.unique' => 'This location name already exists for the selected company.',
            'city_id.required' => 'Please provide city.',
            'country_id.required' => 'Please select Country.',
            'address.required' => 'Please provide street address.',
            'state_id.required' => 'Please provide state.',
            'zip.max' => 'The Zip/Postal-Code must not be greater than 20 characters.'
        ];
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
        } else {
            $objLocation = new Location;
            $objLocation->fill($data);
            $objLocation->country =  Countries::where('id', $request->country_id)->first()->iso2;
            $objLocation->state = State::where('id', $request->state_id)->first()->name;
            $objLocation->city = City::where('id', $request->city_id)->first()->name;
            $objLocation->user_id = Auth::user()->id;
            if ($objLocation->save()) {
                $return["msg"] = trans('config.location_fields.added_success');
                $return["status"] = "success";
            }
        }

        return response()->json($return);
    }

    /* to edit label in ajax call */
    public function ajaxGetLocation(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to get the Location'
        ];

        $objLocation = "";
        try {
            $objLocation = Location::find($id);

            if (empty($objLocation)) {
                return response()->json($return);
            }
            $user = [];
            if ($objLocation->location_user_id != null) {
                $users = explode(",", $objLocation->location_user_id);
                foreach ($users as $h) {
                    $user[] = User::select('id', DB::raw("Concat(first_name,' ',last_name) as text"))->where('id', $h)->first();
                }
                $objLocation->location_user_id = $user;
            }
            $objLocation->countries = ($objLocation->country_id != null) ? Countries::select('id', 'name as text')->find($objLocation->country_id) : [];
            $objLocation->states = ($objLocation->state_id != null) ? State::select('id', 'name as text')->find($objLocation->state_id) : [];
            $objLocation->cities = ($objLocation->city_id != null) ? City::select('id', 'name as text')->find($objLocation->city_id) : [];
            $objLocation->parent_locations = ($objLocation->parent_id != null) ? Location::select('id', 'name as text')->find($objLocation->parent_id) : [];
            $user_id =  $objLocation->company_id != null ? $objLocation->company_id : 0;
            $objLocation->company_id = ($user_id != null) ? Company::select('id', 'name as text')->find($user_id) : [];

            $return['data'] = $objLocation->only('id', 'name', 'parent_id', 'address', 'address2', 'currency', 'city', 'state', 'country', 'zip', 'branch_code', 'zone', 'location_user_id', 'countries', 'states', 'cities', 'parent_locations', 'company_id');

            $return['msg'] = null;
            $return['status'] = 'success';
            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($return);
        }
    }

    /* to edit location in ajax call */
    public function ajaxEditLocation(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to edit the Location'
        ];
        if (!Auth::user()->hasPermissionTo('LocationEdit')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $objLocation = "";
        try {
            $objLocation = Location::find($id);

            if (empty($objLocation)) {
                return response()->json($return);
            }
        } catch (\Exception $e) {
            return response()->json($return);
        }

        $data = $request->only('name', 'parent_id', 'address', 'address2', 'currency', 'city', 'state', 'country', 'zip', 'branch_code', 'zone', 'location_user', 'country_id', 'state_id', 'city_id', 'company_id');
        $data['location_user_id'] = isset($data) && !empty($data['location_user']) ? $data['location_user'] : null;
        if(isset($data['location_user_id']) && !empty($data['location_user_id'])){
            unset($data['location_user']);
        }
        $rules = [
            'name' => [
                'required',
                Rule::unique('locations')->ignore($objLocation->id)
                ->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id)
                                ->whereNull('deleted_at');
                }),
            ],
            'parent_id' => 'nullable',
            'address' => 'required',
            'address2' => 'nullable',
            'currency' => 'sometimes',
            'city_id' => 'required',
            'state_id' => 'required',
            'country_id' => 'required',
            'zip' => 'nullable',
            'branch_code' => 'nullable|string',
            'zone' => 'nullable|string'
        ];
        $messages = [
            'name.required' => 'Please provide Location Name.',
            'name.unique' => 'This location name already exists for the selected company.',
            'city_id.required' => 'Please provide city.',
            'country_id.required' => 'Please select Country.',
            'address.required' => 'Please provide street address.',
            'state_id.required' => 'Please provide state.',
        ];
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $objLocation->fill($data);
        $objLocation->country =  Countries::where('id', $request->country_id)->first()->iso2;
        $objLocation->state = State::where('id', $request->state_id)->first()->name;
        $objLocation->city = City::where('id', $request->city_id)->first()->name;
        $objLocation->user_id = Auth::user()->id;
        if ($objLocation->save()) {
            $return["msg"] = trans('config.location_fields.updated_success');
            $return["status"] = "success";
        }

        return response()->json($return);
    }

    public function LocationExport(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('LocationDownload')) {
            return redirect('dashboard')->with("msg", $return);
        }
        // $query = Location::with(['parentLocation',])->orderBy('updated_at', 'desc');
        $query = Location::select([
            'locations.id as location_id',
            'locations.name as location_name',
            'p.name as parent_name',
            'locations.branch_code as branch_code',
            'locations.address',
            'locations.address2',
            'locations.zone',
            'cities.name as city_name',
            'states.name as state_name',
            'countries.name as country_name',
            'locations.zip',
            'locations.currency',
            DB::raw('(SELECT GROUP_CONCAT(CONCAT(first_name, " ", last_name) SEPARATOR ", ") FROM users WHERE FIND_IN_SET(users.id, locations.location_user_id)) as full_name_location_user_ids'),
            DB::raw('CONCAT(u1.first_name," ",u1.last_name) as full_name_users'),
            DB::raw('DATE_FORMAT(locations.created_at, "%d %b %Y %h:%i %p") as formatted_created_at'),
            DB::raw('DATE_FORMAT(locations.updated_at, "%d %b %Y %h:%i %p") as formatted_updated_at'),
        ])
            ->leftJoin('users as u1', 'locations.user_id', '=', 'u1.id')
            ->leftJoin('states', 'locations.state_id', '=', 'states.id')
            ->leftJoin('cities', 'locations.city_id', '=', 'cities.id')
            ->leftJoin('countries', 'locations.country_id', '=', 'countries.id')
            ->leftJoin('locations as p', 'locations.parent_id', '=', 'p.id')
            ->orderBy('locations.name');

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if (isset($filters->search)) {
                $req["search"] = $filters->search;
            }

            if (isset($req["search"]) && $search_key = trim($req["search"])) {
                $whereStr = sprintf('(locations.id like "%%%1$s%%" or locations.name like "%%%1$s%%" or p.name like "%%%1$s%%" or locations.branch_code like "%%%1$s%%" or locations.zone like "%%%1$s%%" or locations.address like "%%%1$s%%" or countries.name like "%%%1$s%%" or states.name like "%%%1$s%%" or cities.name like "%%%1$s%%" or locations.zip like "%%%1$s%%")', $search_key);
                $query->whereRaw($whereStr);
                $return['recordsFiltered'] = $query->count();
            }
        }

        $return['recordsTotal'] = $query->count();
        $return['recordsFiltered'] = $query->count();

        $results =  $query->get();
        $result = json_decode(json_encode($results, true), true);
        return Excel::download(new LocationExport($result), 'Locations.xlsx');
    }

    public function getStatesByCountry($countryCode)
    {
        $states = State::where('country_code', $countryCode)->pluck('name', 'id');
        return response()->json($states);
    }

    public function getCityByState($stateId)
    {
        $cities = City::where('state_id', $stateId)->pluck('name', 'id');
        return response()->json($cities);
    }

    public function locationImport(Request $request) {
        $return = ["msg"=>"Unable to import the given user list", "status"=>"danger"];
        if(! Auth::user()->hasPermissionTo('LocationImport')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        if( $request->isMethod('post') && !$request->import_file ) {
            $return["msg"] = trans('content.download_format.upload_the_file_location');
            $request->session()->flash("msg", $return);
        } else {
            if(! $request->isMethod('post')) {
                return view("locations.import");
            }

            if ( $request->isMethod('post') && $request->import_file ) {
                $path = $request->file('import_file')->getRealPath();
                $import = new LocationImport($request);
                Excel::import($import, $request->import_file);
                $response = $import->data;
                return view("locations.import")->with([ 'fail' => isset($response['fail']) ? $response['fail'] : [], 'fail_msgs' => isset($response['fail_msgs']) ? $response['fail_msgs'] : [] ]);
            }
        }

        return view("locations.import");
    }
    public function fetchZoneByAjax(Request $request)
    {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);
        $take = 10;

        if (isset($request->start) && isset($request->length)) {
            $skip = (int) $request->start;
            $take = (int) $request->length;
        }

        $db = Location::select("locations.zone as id", "locations.zone as text")
            ->whereNotNull('locations.zone')
            ->where('locations.zone', '!=', '')
            ->orderBy('locations.zone', 'ASC');
        if ($search) {
            if (is_array($search)) {
                $db->where("locations.zone", "like", "%" . $search['value'] . "%");
            } else {
                $db->where("locations.zone", "like", "%" . $search . "%");
            }
        }
        if (isset($request->type) && $request->type == 'filter') {
            if (!empty($request->country_id)) {
                $db->whereIn('country_id', array($request->country_id));
            }
        } else {
            $db->where('country_id', $request->country_id);
        }
        $count = $db->count();
        if (isset($request->start) && isset($request->length)) {
            $db->skip($skip)->take($take);
        } else {
            $db->skip($skip)->take(20);
        }
        $return['recordsFiltered'] = $db->count();
        $return['recordsTotal'] = $db->count();
        $result = $db->get();
        $return["draw"] = date('is');

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["data"] = count($result) ? $result->toArray() : [];
        $return["results"] = count($result) ? $result->toArray() : [];
        $return['data'] = array();
        foreach ($result as $d) {
            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\Licenses\LicenceExport;
use App\Exports\Licenses\LicenseInfoExport;
use Auth;
use stdClass;
use App\Helpers\Common as CommonHelper;
use App\Mail\licenses\LicenseAddNotification;
use App\Models\Company;
use App\Models\Manufacture;
use App\Models\Supplier;
use App\Models\Depreciation;
use App\Models\Currency;
use App\Models\Location;
use App\Models\Place;
use App\Models\License;
use App\Models\LicenseSeat;
use App\Models\Actionlog;
use App\Models\LicenseCache;
use App\Models\Device;
use App\Models\LicenseRecord;
use App\Models\Threshold;
use App\Models\Purchase;
use App\Models\Department;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Validator;
use App\Models\Category;
use App\Models\LicensePurchase;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use App\Models\User;
use App\Mail\licenses\LicenseCheckoutNotification;
use App\Mail\ThreshouldLicenseNotification;
use App\Models\ThresholdSettings;
use App\Mail\licenses\LicenseCheckinNotification;



use Illuminate\Validation\Rule;
use App\Models\NetworkInventory\Product;
use DB;
use Log;
// use PDF;
use Spatie\LaravelPdf\Facades\Pdf as PDF;
use App\Models\CustomFieldset;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Validator as ValidationValidator;
use Maatwebsite\Excel\Facades\Excel;

class LicenseController extends Controller
{     
    public $global_filter_count = 0;
    public function getIndex(Request $request) {

        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];

        if(! Auth::user()->hasPermissionTo('LicenseRead') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $purchaseFilter = null;
        if (isset($request->q)) {
            $purchaseFilter = $request->q;
        }

        $vd = new stdClass;
        $companyId = CommonHelper::getAccessibleCompanyIds();
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $vd->assignedForOptions = License::getAssignedForOptions();
       
        $vd->internal_places = Place::select('places.id', DB::raw('CONCAT(locations.name, " - ", places.place) as text'))
                                ->join('locations', 'places.location_id', 'locations.id')
                                ->whereIn("locations.company_id", $companyIds)
                                ->orderBy('places.place')->get();
        $location = $catId = $is_tracked_status = "null";
        if( $request->location != null && $request->location != "null"){
            $location = $request->location;
        } else if($request->city != null && $request->city != "null") {
            $id = Location::whereIn('city_id',array($request->city))->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        } else if($request->states != null && $request->states != "null") {
            $id = Location::whereIn('state_id',array($request->states))->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        } else if($request->zone != null && $request->zone != "null") {
            $id = Location::whereIn('zone',array($request->zone))->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        } else if($request->country != null && $request->country != "null") {
            $id = Location::whereIn('country_id',array($request->country))->pluck('id')->toArray();
            $location = empty($id) ? 0 : implode(',', $id);
        }
        if($request->is_tracked != null && ($request->is_tracked != "null")){
            $is_tracked_status = $request->is_tracked;
        }
        $department = isset($request->department) ? $request->department : "null";
        $createdFrom = isset($request->createdFrom) ? $request->createdFrom : "mull";
        $createdTo = isset($request->createdTo) ? $request->createdTo : "null";
        if(isset($request->category) && $request->category != "null"){
            $catId = explode(',',  $request->category);
        }
        $type = isset($request->type) ? $request->type : "null";
        // $vd->locations = Location::select('id', 'name as text')->orderBy('name')->get();
        $companyFieldset = CustomFieldset::where('id', Settings::first()->licence_custom_fieldset_id)->first();
        $requestable_enabled = config('app.requestable_enabled');
    
        return view("licenses.index")->with('vd', $vd)->with('companyFieldset', $companyFieldset)->with('requestable_enabled', $requestable_enabled)->with("location", $location)->with("department", $department)->with("createdFrom", $createdFrom)->with("createdTo", $createdTo)->with('category_id', $catId)->with('type', $type)->with('purchaseFilter', $purchaseFilter)->with('is_tracked_status',$is_tracked_status);
    }

    public function ajaxIndex(Request $request) {   


        $list_filter_or_search = false;
        $filter_count = $dashboard_filter_count = 0;
        $req = $request->all();
        
        $return = array(
            "draw" => date('is'),
            'data' => array()
        );

          $fields = array(
            '1' => 'a.name',
            '2' => 'a.seats',
            '3' => 'a.available_seats',
            '4' => 'a.network_count', 
            '5' => 'a.expiration_date',
            '6' => 'a.purchase_date',  
            '7' => 'loc.name',
            '8' => 'a.order_number',
            '9' => 'a.updated_at',
        );
        
        $db = DB::table('licenses as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('places as pla', 'pla.id', '=', 'a.internal_place_id');
        
        if( isset($req["showDeletedLicenses"]) && $req["showDeletedLicenses"] == "true" ) {
            $db->whereNotNull('a.deleted_at');
        }
        else {
            $db->whereNull('a.deleted_at');
        }
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id');
        $db->leftJoin('purchases as pur', 'pur.id', '=', 'a.invoice_id');

        $db->select('a.id', 'a.notes', 'a.name','a.currency', 'cmp.name as cmp_name', 'loc.name as loc_name', 'pla.place as place_name', 'a.order_number', 'a.seats', 'a.available_seats', 'a.agreement_no','cat.name as cat_name','a.Version as Version','a.product_id','a.added_via','dep.name as department','unique_tag','a.is_tracked','a.company_id');
        $dateFormatSql = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(a.purchase_date, '{$dateFormatSql}') as purchase_date_on"));
        $db->addSelect(DB::raw("case when dayname(a.expiration_date) is not null then DATE_FORMAT(a.expiration_date, '{$dateFormatSql}') else '' end as expire_date_on"));
        $db->addSelect(DB::raw('case when dayname(a.expiration_date) is not null and a.expiration_date < curdate() then "Expired" else "" end as is_expired'));
        $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","LIC",a.id) else "" end as batch_no'));
        $dateFormatSql = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(a.updated_at, '{$dateFormatSql}') as last_updated_at"));
        $db->addSelect(DB::raw('case when a.purchase_cost is not null then FORMAT(a.purchase_cost, 2) else FORMAT(0,2) end as purchase_cost_format'));
        $db->addSelect(DB::raw('a.serial as serial_value'));
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('a.location_id', '=', 0);
            } else {
                $db->whereIn('a.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }

        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);
        $location_filter = isset($req['location']) ? $req['location'] : $request->input("location", null);
        if($location_filter != null && $location_filter != 'null') {
            $db->whereIn("a.location_id", explode(",", $location_filter));
        }

        $type_filter = isset($req['type']) ? $req['type'] : $request->input("type", null);

        $category_id_filter = isset($req['category_id']) ? $req['category_id'] : $request->input("category_id", null);
        if($category_id_filter != null && $category_id_filter != 'null') {
            $db->whereIn("a.category_id", $category_id_filter);
        }
        $department_filter = isset($req['department']) ? $req['department'] : $request->input("department", null);
        if($department_filter != null && $department_filter != 'null') {
            $db->whereIn("a.department_id", explode(",", $department_filter));
        } 
        if(isset($req["createdFrom"]) && $req["createdFrom"] && $req["createdFrom"] != "null" && isset($req["createdTo"]) && $req["createdTo"] && $req["createdTo"] != "null") {
            $from_date = CommonHelper::getDateAs($req["createdFrom"], "Y-m-d", "d/m/Y");
            $to_date = CommonHelper::getDateAs($req["createdTo"], "Y-m-d", "d/m/Y");
            if($from_date && $to_date) {
                $db->wherebetween('a.created_at',[$from_date, $to_date]);
            }
        }
        if (isset($request->q) && $request->q != null) {
            $decoded = array_map('intval', explode(',', trim(base64_decode($request->q), '"')));
            $db->whereIn('a.id', $decoded);
        }
        if(isset($request->is_tracked) && $request->is_tracked != null && $request->is_tracked != "null") {
            $db->where('a.is_tracked', $request->is_tracked)->where('a.added_via',2);
        }
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];
        if (isset($req["filters"])) {
            $filters = $req["filters"];
            $db->where(function($query) use($filters) {
                if (isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                    $list_filter_or_search = true;
                    $query->whereIn("a.manufacturer_id", $filters['manufacturer']);
                }
                if (isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                    $list_filter_or_search = true;
                    $query->whereIn("a.category_id", $filters['category']);
                }
                if (isset($filters["supplier"]) && $filters['supplier'] && $filters['supplier'] != "null") {
                    $list_filter_or_search = true;
                    $query->whereIn("a.supplier_id", $filters['supplier']);
                }
                if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                    $list_filter_or_search = true;
                    $query->whereIn("a.location_id", $filters['location']);
                }
                if(isset($filters["purchase_reference"]) && $filters['purchase_reference'] && $filters['purchase_reference'] != "null") {
                    $list_filter_or_search = true;
                    $query->whereIn("a.invoice_id", $filters['purchase_reference']);
                }
                if(isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                    $query->whereIn("a.internal_place_id", $filters['internal_place']);
                }
                if(isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                    $list_filter_or_search = true;
                    $query->whereIn("a.department_id", $filters['department']);
                }
                $based_on_possible = ['1'=>'a.purchase_date', '2'=>'a.updated_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
                    $list_filter_or_search = true;
                    if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                        $daterange = explode(" - ", $filters["date_range"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if ($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $query->whereRaw($whereStr);
                        }
                    }
                }
            });
            $return['recordsFiltered'] = $db->count();
        }
        //  dd($req["search"]["value"]);
        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
           
            $list_filter_or_search = true;
            $whereStr = sprintf('((case when a.id is not null then concat_ws("","LIC",a.id) else "" end) like "%%%1$s%%" or dep.name  like "%%%1$s%%" or pla.place  like "%%%1$s%%" or a.name like "%%%1$s%%" or a.notes like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or cmp.name like "%%%1$s%%" or a.agreement_no like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or a.seats like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or a.available_seats like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null then DATE_FORMAT(a.expiration_date, "%%d %%b %%Y") else "" end) like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null and a.expiration_date < curdate() then "Expired" else "" end) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
            $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
            // dd($db->get());
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

            foreach($data as $key => $lic) {
                    $version_Array = explode(",", $lic->Version);
                    $version_id_Text = '';
                    if(!empty($version_Array)) {
                        foreach ($version_Array as $ver) {
                            $ver = Product::find($ver);
                            if ($ver) {
                                if ($version_id_Text == '') {
                                    $version_id_Text = $ver->Version;
                                } else {
                                    $version_id_Text = $version_id_Text . "," . $ver->Version;
                                }
                            }
                        }
                    }
                    $software_Array = explode(",", $lic->product_id);
                    $software_id_Text = '';
                    if(!empty($software_Array)) {
                        foreach ($software_Array as $ver) {
                            $ver = Product::find($ver);
                            if ($ver) {
                                if ($software_id_Text == '') {
                                    $software_id_Text = $ver->Caption;
                                } else {
                                    $software_id_Text = $software_id_Text . "," . $ver->Caption;
                                }
                            }
                        }
                    }
                    $data[$key]->Version = isset($version_id_Text) ? $version_id_Text : '';
                    $data[$key]->Caption = isset($software_id_Text) ? $software_id_Text : '';
                    $software =  explode(",", $data[$key]->Caption);
                    $totalSoftware = Product::whereIn('Caption', $software)->count();
                    $lic->software_count = $totalSoftware;
                    $version =  isset($data[$key]->Version) ? explode(",", $data[$key]->Version) : [];
                    $version = Product::select('id','Caption')->leftjoin('itm_network_inventory_basic as b', 'b.id', '=', 'itm_network_inventory_products.basic_id')->whereIn('Caption', $software)->whereIn('itm_network_inventory_products.Version',$version)->whereNull('b.deleted_at')->count();
                    $lic->network_count = $version ? $version : 0;
                }       
               foreach($data as $d) {
                $inUseCount = $d->seats - $d->available_seats;
                $include = false;
                if($type_filter == 'Available' && $d->available_seats != 0) {
                    $include = true;
                } elseif($type_filter == 'In use' && $inUseCount != 0) {
                    $include = true;
                } elseif ($type_filter == "Expired License" && $d->is_expired == "Expired") {
                    $include = true;
                } elseif ($type_filter == "Tracked Licenses" || $type_filter == "Not Tracked Licenses") {
                    $include = true;
                } elseif ($type_filter == null) {
                    
                    $return['data'][] = array('a' => $d);
                }
                if ($include) {
                    if ($list_filter_or_search) {
                        $filter_count++;
                    } else {
                        $dashboard_filter_count ++;
                        $this->global_filter_count = $dashboard_filter_count;
                    }
                    $return['data'][] = array('a' => $d);
                }
                $networkCount = $d->network_count;
                $d->licensecountnum = 0;
                if (!empty($d->seats) && $d->seats > 0 && $d->added_via == 1) {
                    $licensediv = $inUseCount / $d->seats;
                    $licensedivper = $licensediv * 100;
                    $d->licensecountnumPercent = number_format($licensedivper, 0);
                    $d->licensecountnum = $d->licensecountnumPercent.'%';
                } else if (!empty($d->seats) && $d->seats > 0 && $d->added_via == 2) {
                    $licensediv = $networkCount / $d->seats;
                    $licenseper = $licensediv * 100;
                    $d->licensecountnumPercent = number_format($licenseper, 0);
                    $d->licensecountnum = $d->licensecountnumPercent.'%';
                } else {
                    $d->licensecountnum = '0%';
                }
            }
            if (in_array($type_filter, ['Available', 'In use', 'Expired License'])) {
                $return['recordsTotal'] = $this->global_filter_count;
                $return['recordsFiltered'] = $list_filter_or_search ? $filter_count : $return['recordsTotal'];
            }

        return response()->json($return);
    } 

    public function deleteLicense($id) {
        $return = ['status' => 'fail', 'msg' => trans('licenses.license_fields.Unable_to_delete_the_License')];
        if(! Auth::user()->hasPermissionTo('LicenseDelete') || !config("services.assets.enabled")) {
            $return["msg"] = trans('licenses.license_fields.permission_denied');
            return response()->json($return);
        }
        
        $appSettings = Settings::first();

        if(! Auth::user()->company_id) {
            $return['msg'] = trans('licenses.license_fields.you_are_not_allowed');
            return response()->json($return);       
        }

        try {
            $licenceDtl = License::findOrFail($id);
        }
        catch(\Exception $e) {
            $return['msg'] = trans('licenses.license_fields.already_deleted_data');
            return response()->json($return);
        }

        $companyIds = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($licenceDtl->company_id, $companyIds)) {
            $msg = "You don't have access to this company.";
            return response()->json(["msg" => $msg]);
        }

        $allotedSeats = LicenseSeat::where('license_id','=',$id)
                    ->whereNull('deleted_at')
                    ->where(function($q) {
                        $q->whereRaw('(assigned_to is not null or asset_id is not null)');
                    })
                    ->count();
        

        if($allotedSeats)
        {
            $return['msg'] =trans('licenses.license_fields.the_license_has_assigned') .$allotedSeats. trans('content.licenses_fields.users/devices');
            return response()->json($return);
        }

        if($licenceDtl->delete()) {
            LicenseSeat::where('license_id','=',$id)->delete();
            Actionlog::licenceDeleted($licenceDtl, Auth::id());
            $return['msg'] = trans('content.licenses_fields.License_has_been_deleted');
            $return['status'] = 'success';
            Log::info("deleteLicense id:" . $licenceDtl->id." uid:" . Auth::user()->id);
        }
    
        return response()->json($return);
    }

    public function addLicense(Request $request) {
        $return = ['status'=>'fail', 'msg'=>trans('licenses.license_message.Unable_to_add_the_license')];
        if(! Auth::user()->hasPermissionTo('LicenseAdd') || !config("services.assets.enabled")) {
            $return["msg"] = trans('licenses.license_message.Permission_denied');
            return response()->json($return);
        }
        $appSettings = Settings::first();

        if(! Auth::user()->company_id) {
            $return['msg'] = trans('licenses.license_message.Please_update_your_company');
            return response()->json($return);
        }

        $data = $request->only('company_id', 'is_tracked', 'name', 'serial', 'license_name', 'license_email', 'seats', 'reassignable', 'supplier_id', 'order_number', 'purchase_date', 'purchase_cost', 'currency', 'purchase_order', 'expiration_date', 'depreciation_id', 'maintained', 'termination_date', 'notes', 'support', 'manufacturer_id', 'agreement_no', 'invoice_id','category_id','added_via','product_id','Version','location_id','department_id','image','requestable_license','internal_place_id','unique_tag');
        
        $rules = [
            'unique_tag' => ['nullable','clean_text_only','max:100',
                Rule::unique('licenses', 'unique_tag'),
            ],
            'all_version_check' => 'nullable|in:0,1',
            'company_id' => 'nullable|integer|exists:companies,id',
            'name' => 'required|clean_text_only|min:2|max:255',
            'serial' => 'required|string|min:2|max:500|not_regex:/[#\$]/|unique:licenses,serial|clean_text_only',
            'license_name' => 'nullable|clean_text_only|max:100',
            'license_email' => 'nullable|email|max:120',
            'seats' => 'required|integer|min:1',
            'reassignable' => 'boolean',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_number' => 'nullable|clean_text_only|max:50',
            'purchase_date'     => 'nullable|date_format:d/m/Y',
            'purchase_cost' => 'nullable|numeric',
            'currency' => 'nullable',
            'purchase_order' => 'nullable|clean_text_only|max:255',
            'expiration_date' => 'nullable|date_format:d/m/Y|after:purchase_date',
            'depreciation_id' => 'nullable|sometimes|exists:depreciations,id',
            'maintained' => 'boolean',
            'termination_date' => 'nullable|date_format:m/d/Y|after:purchase_date',
            'notes' => 'nullable|clean_text_only|max:2000',
            'support' => 'nullable|clean_text_only|max:500',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'internal_place_id'   => 'nullable|integer|min:1|exists:places,id',
            'agreement_no' => 'nullable|clean_text_only|max:255',
            "invoice_id"    => "nullable|array",
            "invoice_id.*" => "nullable|integer|exists:purchases,id",
            "category_id" => "nullable|integer|exists:categories,id",
            'location_id' => 'required|integer|exists:locations,id',
            'department_id' => 'required|integer|exists:departments,id',
            'image'         => 'sometimes|mimes:jpeg,bmp,png',
            // "product_id" => "nullable|string|exists:itm_network_inventory_products,Caption",
        ];
        $data_fields = $request->input("fields", null);
        $custom_fields = $custom_fields_val= [];
        $category = Category::find($data['category_id']);
        if($category && $category->customFieldset && count($category->customFieldset->fields)) {
            foreach($category->customFieldset->fields as $f){
                $col_name = $f->nameToColumn();
                $formatType = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[] = $col_name;
                $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
            }
        }
        

        if(Settings::first()->licence_custom_fieldset_id != "") {
            $customFieldset = CustomFieldset::where('id', Settings::first()->licence_custom_fieldset_id)->first();
            foreach($customFieldset->fields as $f) {
                $col_name = $f->nameToColumn();
                $formatType = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[] = $col_name;
                $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
            }
        }

        $validator = Validator::make($data, $rules, []);
    

        if( $validator->fails() ) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $data['company_id'] = $request->company_id ? $request->company_id : Auth::user()->company_id;

        $objLic = new License;
        $data["licence_custom_fields"] = null;
        $objLic->fill($data);
        if (count($custom_fields_val)) {
            foreach ($custom_fields_val as $key => $f) {
                 $objLic->$key  = isset($f) ? $f : null;
            }
        }
        $objLic->maintained = $request->maintained ? 1 : 0;
        $objLic->reassignable = isset($data['reassignable']) ? 1 : 0;
        $objLic->invoice_id = $request->invoice_id == "" ? null : implode(",", $request->invoice_id);
        $objLic->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d/m/Y") : null;
        $objLic->expiration_date = $request->expiration_date ? CommonHelper::getDateAs($request->expiration_date, "Y-m-d", "d/m/Y") : null;
        $objLic->termination_date = $request->termination_date ? CommonHelper::getDateAs($request->termination_date, "Y-m-d", "d/m/Y") : null;
        $objLic->user_id = Auth::user()->id;
        $objLic->image = null;
        $objLic->available_seats = $objLic->seats;
        $objLic->requestable_license = isset($request->requestable_license) ? $request->requestable_license : 0;
        $objLic->check_all_versions = $request->has('all_version_check') ? 1 : 0;
        
        if ($request->hasFile('image')) {

            $uploaded_img = $request->file('image');
            $licenseImage = Str::random(12) . Str::random(12) . '.' . $uploaded_img->getClientOriginalExtension();
            $path = public_path('uploads/license/' . $licenseImage);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($uploaded_img->getRealPath());
            $image = $image->resize(300, 300);
            $image->save($path);
            $objLic->image = $licenseImage;
        } elseif (
            $request->input('clone_img') &&
            $request->input('clone_img') != '' &&
            !$request->hasFile('image') &&
            !$request->input('delete_img', 0)
        ) {
            $old_path = public_path('/uploads/license/' . $request->input('clone_img'));
            if (file_exists($old_path)) {
                $cloneImgArr = explode('.', $request->input('clone_img'));
                $licenseImage = Str::random(12) . Str::random(12) . '.' . end($cloneImgArr);
                $path = public_path('uploads/license/' . $licenseImage);
                if (copy($old_path, $path)) {
                    $objLic->image = $licenseImage;
                }
            }
        }

        $objLic->currency = $request->currency ? $request->currency : $appSettings->default_currency;
        $versionIds = [];
        if(isset($data['Version'])) {
            foreach($data['Version'] as $version) {
                $versionCheck = Product::where("Version", $version)->first();
                if(isset($versionCheck) && $versionCheck->count() > 0){
                    array_push($versionIds,$versionCheck->id);
                }
            }
        }
        $versions = implode(',',$versionIds);
        $objLic->Version = isset($versions) ? $versions : null;
        
        $softwareCaption = [];
        if(isset($data['product_id'])) {
            foreach($data['product_id'] as $software) {
                $softwareCheck = Product::where("id", $software)->first();
                if(isset($softwareCheck) && $softwareCheck->count() > 0){
                    array_push($softwareCaption,$softwareCheck->id);
                }
            }
        }
        
        $softwares = implode(',',$softwareCaption);
        $objLic->product_id = isset($softwares) ? $softwares : null;
        if($objLic->save()) {
            if ($data["unique_tag"] == "") {
                $objLic->generateUniqueTag();
            }
            
            
            $licPurchase = new LicensePurchase();
            $licPurchase->batch_no = $objLic->id;
            $licPurchase->po_no = !empty($objLic->order_number) ? $objLic->order_number : null;
            $licPurchase->purchase_date = !empty($objLic->purchase_date) ? $objLic->purchase_date : date('Y-m-d');
            $licPurchase->exp_date = !empty($objLic->purchase_date) ? $objLic->purchase_date : date('Y-m-d');
            $licPurchase->currency = !empty($objLic->currency) ? $objLic->currency : $appSettings->default_currency;
            $licPurchase->purchase_price = !empty($objLic->purchase_cost) ? $objLic->purchase_cost : 0.0;
            $licPurchase->qty = !empty($objLic->seats) ? $objLic->seats : 0;
            $licPurchase->purchase_by = !empty($objLic->supplier_id) ? $objLic->supplier_id : null;
            $licPurchase->save();
            

            // $seats = [];
            // $seat_data = [];
            // $seat_data['created_at'] = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
            // $seat_data['updated_at'] = $seat_data['created_at'];
            // $seat_data['license_id'] = $objLic->id;
            // $seat_data['user_id'] = Auth::user()->id;

            // for($i = 1; $i <= $objLic->seats; $i++) {
            //     $seats[] = $seat_data;
            // }

            // $seat_coll = collect($seats);
            // $seat_coll->chunk(500)->each(function($item) {
            //     LicenseSeat::insert($item->toArray());
            // });
            
            Actionlog::licenseAdded($objLic->id,Auth::id(),$objLic->added_via);
                 
        }
        
        
        $user = Auth::user();
         if(Settings::first()->alerts_enabled == 1){
            try {
                $alertnotify = CommonHelper::getGlobalAlertEmail();
                foreach ($alertnotify as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->queue(new LicenseAddNotification($objLic, $user));
                }
                }
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        
        $return["msg"] = trans('content.licenses_fields.License_added_successfully');
        $return["id"] = $objLic->id;
        $return["status"] = "success";
        $return["seats"] = $objLic->seats;
        $return["expire_date"] = $objLic->expiration_date;
        
        Log::info("addLicense id:" . $objLic->id." uid:" . Auth::user()->id . " : " . json_encode($request->all()));
        return response()->json($return);
    }

    public function showedit($id, $action="") {

        $return = ['status'=>'fail', 'msg'=> trans('licenses.license_form.Unable_to_get_the_record')];
        $appSettings = Settings::first();
        $record = License::find($id);
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($record->company_id, $companyIds)) {
            $return["status"] = 'error';
            $return["msg"] = "You don't have access to this company.";
            return response()->json($return);
        }
        $record->purchase_date = CommonHelper::getDateAs($record->purchase_date, "d-m-Y", "Y-m-d");
        $record->expiration_date = CommonHelper::getDateAs($record->expiration_date, "d-m-Y", "Y-m-d");
        $record->termination_date = CommonHelper::getDateAs($record->termination_date, "d-m-Y", "Y-m-d");
        $record->purchase_cost = number_format($record->purchase_cost, 2 , '.' ,'' );

        if($record->product_id) {
            $getProduct = Product::where("Caption", $record->product_id)->select("id", "Caption as text")->first();
            $record["dropdown"] = $getProduct && $getProduct->exists ? $getProduct->toArray() : null;
        }
        $version1 = $record->Version;
        $versions = explode(",", $version1);
        $versionIds = [];
        if(!empty($versions)) {
            foreach ($versions as $version) {
                $versionCheck = Product::select("id", "Version")->where("id", $version)->first();
                if (isset($versionCheck) && $versionCheck->count() > 0) {
                    array_push($versionIds, $versionCheck);
                }
            }
            $record->Versions = $versionIds;
        }

        $software = $record->product_id;
        $softwares = explode(",", $software);
        $softwareCaptions = [];
        if(!empty($softwares)) {
            foreach ($softwares as $software) {
                $softwaresCheck = Product::select('id', 'Caption')->where("id", $software)->first();
                if (isset($softwaresCheck) && $softwaresCheck->count() > 0) {
                    array_push($softwareCaptions, $softwaresCheck);
                }
            }
            $record->product_id = $softwareCaptions;
        }


        $return["data"] = $record->toArray();
        $return["dropdown"] = [];
        $return["drop"] = array();
        $return["custom_fields"]['all_fields'] = array();
        $return["custom_fields"]['required_fields'] = array();
        $return["custom_fields"]['html'] = "";
        if($record->invoice_id) {
            $invoice_id = explode(",", $record->invoice_id);
            $getInvoice = Purchase::whereIn("id", $invoice_id)->select("id", DB::raw('concat_ws(" - ", invoice_no, date_format(invoice_date, "%d/%m/%Y")) as text'))->get();
            $return["dropdown"]["invoice"] = !empty($getInvoice) ? $getInvoice->toArray() : null;
        }
        if($record->category_id) {
            $getCategory = Category::where("id", $record->category_id)->select("id", "name as text")->first();
            $return["dropdown"]["category"] = $getCategory && $getCategory->exists ? $getCategory->toArray() : null;

            // load custom fields
            if($record->category->customFieldset && count($record->category->customFieldset->fields)) {
                // $return["custom_fields"] = CommonHelper::formCustomFieldsLicence($record->category->customFieldset->fields, $return["data"]['licence_custom_fields']);
                $return["custom_fields"] = CommonHelper::formCustomFields($record->category->customFieldset->fields, $return["data"]);
            }
        } else {
            $licenses1 = ['all_fields' => [], 'required_fields' => [], 'html' => ''];  
            if(Settings::first()->licence_custom_fieldset_id != null) {
                $fieldsetObj = CustomFieldset::where('id', Settings::first()->licence_custom_fieldset_id)->first();
                if(!empty($fieldsetObj)) {
                    $licenses1 = CommonHelper::formCustomFields($fieldsetObj->fields, $return["data"]);
                if(isset($licenses1['all_fields'][0])) {
                        array_push($return["custom_fields"]['all_fields'], $licenses1['all_fields'][0]);
                }
                if(isset($licenses1['required_fields'][0])) {
                        array_push($return["custom_fields"]['required_fields'], $licenses1['required_fields'][0]);
                }
                $return["custom_fields"]['html'] .= $licenses1['html'];
                }
            }
        }
        
        if($record->department_id) {
            $department = Department::where("id", $record->department_id)->select("id", "name as text")->first();
            $return["dropdown"]["department"] = $department->exists ? $department->toArray() : null;
        }
        if($record->location_id) {
            $getLocation = Location::where("id", $record->location_id)->select("id", "name as text")->first();
            $return["dropdown"]["location"] = $getLocation->exists ? $getLocation->toArray() : null;
        }
        if ($record->internal_place_id) {
            $getPlace = Place::where("id", $record->internal_place_id)->select("id", "place as text")->first();
            if (!empty($getPlace))
            $return["dropdown"]["internal_place"] = $getPlace && $getPlace->exists ? $getPlace->toArray() : null;
        }

        if($action == "clone") {
            unset($return["data"]["id"]);
            unset($return["data"]["serial"]);
            // unset($return["data"]["unique_tag"]);
        }
        $return['msg'] = '';
        $return['status'] = 'success';
        return response()->json($return);
    }

    public function update(Request $request, $id) {  

        $return = ['status'=>'fail', 'msg'=>trans('content.licenses_fields.Unable_to_get_the_record')];
        if(! Auth::user()->hasPermissionTo('LicenseEdit') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $appSettings = Settings::first();

        if(! Auth::user()->company_id) {
            $return['msg'] = trans('content.licenses_fields.you_are_not_allowed');
            return response()->json($return);
        }

        try {
            $objLicense = License::findOrFail($id);
            $for_log_comparison = $objLicense->LicenseDataForCache();
        }
        catch(\Exception $e) {
            return response()->json($return);
        }

        
        $data = $request->only('company_id', 'is_tracked', 'name', 'serial', 'license_name', 'license_email', 'seats', 'reassignable', 'supplier_id', 'order_number', 'purchase_date', 'purchase_cost', 'currency', 'purchase_order', 'expiration_date', 'depreciation_id', 'maintained', 'termination_date', 'notes', 'support', 'manufacturer_id', 'agreement_no', 'invoice_id','category_id','licence_custom_fields','added_via','product_id','Version','location_id','department_id','image','requestable_license','internal_place_id','unique_tag');
        $rules = [
            'unique_tag' => ['nullable','clean_text_only',
                'max:100',Rule::unique('licenses', 'unique_tag')->ignore($id), 
            ],
            'all_version_check' => 'nullable|in:0,1',
            'company_id' => 'nullable|integer|exists:companies,id',
            'name' => 'required|string|min:2|max:255',
            'serial' => ['required', 'string', 'max:500', Rule::unique("licenses")->ignore($id)],
            'license_name' => 'nullable|string|max:100',
            'license_email' => 'nullable|email|max:120',
            'seats' => 'required|integer|min:1',
            'reassignable' => 'boolean',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_number' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date_format:d/m/Y',
            'purchase_cost' => 'nullable|numeric',
            'currency' => 'nullable',
            'purchase_order' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date_format:d/m/Y|after:purchase_date',
            'depreciation_id' => 'nullable|sometimes|exists:depreciations,id',
            'maintained' => 'boolean',
            'termination_date' => 'nullable|date_format:m/d/Y',
            'notes' => 'nullable|string|max:2000',
            'support' => 'nullable|string|max:500',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'agreement_no' => 'nullable|string|max:255',
            "invoice_id"    => "nullable|array",
            'internal_place_id'   => 'nullable|integer|min:1|exists:places,id',
            "invoice_id.*" => "nullable|integer|exists:purchases,id",
            'location_id' => 'required|integer|exists:locations,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            "category_id" => "nullable|integer|exists:categories,id",
            'image'             => 'sometimes|mimes:jpeg,bmp,png',
        ];

        $data_fields = $request->input("fields", null);
        $custom_fields = $custom_fields_val= [];
        $category = Category::find($data['category_id']);
        if($category && $category->customFieldset && count($category->customFieldset->fields)) {
            foreach($category->customFieldset->fields as $f){
                $col_name = $f->nameToColumn();
                $formatType = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[] = $col_name;
                $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
            }
        }
        if(Settings::first()->licence_custom_fieldset_id != "") {
            $customFieldset = CustomFieldset::where('id', Settings::first()->licence_custom_fieldset_id)->first();
            foreach($customFieldset->fields as $f) {
                $col_name = $f->nameToColumn();
                $formatType = CommonHelper::convertFormatToRegex($f->format);
                $rules[$col_name] = $f->pivot->required ? "required|{$formatType}|max:255" : "nullable|{$formatType}|max:255";
                // $rules[$col_name] = $f->pivot->required ? 'required|string|max:255' : 'nullable|string|max:255';
                $data[$col_name] = ($data_fields && isset($data_fields[$col_name])) ? $data_fields[$col_name] : null;
                $custom_fields[] = $col_name;
                $custom_fields_val[$f->nameToColumn()] = $data[$col_name];
            }
        }

        $validator = Validator::make($data, $rules, []);
        if( $validator->fails() ) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $allotedSeats = LicenseSeat::where('license_id', $id)->whereNull('deleted_at')->where(function($q) {
            $q->whereRaw('(assigned_to is not null or asset_id is not null)');
        })->count();

        $totalPurchaseQty = LicensePurchase::where('batch_no', $id)->sum('qty') ?? 0;

        if($allotedSeats >= $totalPurchaseQty) {
            if($request->seats < $allotedSeats){
                return response()->json(['status' => 'error', 'msg' => $allotedSeats.trans('content.licenses_fields.seats_have_alloted')]);
            }
        }

        if($totalPurchaseQty >= $allotedSeats) {
            if( $request->seats < $totalPurchaseQty){
                return response()->json(['status' => 'error', 'msg' => $totalPurchaseQty . trans('content.licenses_fields.purchase_license_qty')]);
            }
        }

        $newSeats = 0;
        if( $request->seats > $objLicense->seats ) {
            // if seat increses
            $seatsInDb = $objLicense->licenseseats->count();
            $newSeats = $request->seats - $seatsInDb ;
        }

        $seatsToDelete = 0;
        $canDeleteSeats = false;
        $freeSeatsRes = null;
        unset($data['seats']);
        if(isset($data['seats']) && $request->seats < $objLicense->seats )
        {
            // if seat decreses
            if( $request->seats < $objLicense->licenseseats->count() )
            {
                $seatsToDelete = $objLicense->seats - $request->seats;
                $freeSeatsRes = LicenseSeat::where('license_id','=',$id)->whereNull('deleted_at')->whereNull('assigned_to')->whereNull('asset_id');
                $freeSeats = $freeSeatsRes->count();

                if( $seatsToDelete > $freeSeats ) {
                    // throw error
                    $allotedSeats = LicenseSeat::where('license_id', '=', $id)->whereNull('deleted_at')->where(function($q) {
                        $q->whereRaw('(assigned_to is not null or asset_id is not null)');
                    })->count();

                    $return['msg'] = $allotedSeats.trans('content.licenses_fields.seats_have_alloted');
                    return response()->json($return);
                }

                if( $seatsToDelete <= $freeSeats ) {
                    $canDeleteSeats = true;
                }
            }
        }
        $data["licence_custom_fields"] = null;
        // $dataField = json_decode($data["licence_custom_fields"], TRUE);
        // $var = json_decode($objLicense->licence_custom_fields);
        // if(!empty($var)) {
        //     foreach($dataField as $key=>$d) {
        //         $var->$key = $d;
        //     }
        //     $data["licence_custom_fields"] = json_encode($var);
        // } else {
        //     $data["licence_custom_fields"] = json_encode($custom_fields_val);
        // }
        if($objLicense->added_via == 2 && $data['added_via'] == 1){
            $objLicense->is_tracked = 0;
        }
        $objLicense->fill($data);
        if (count($custom_fields_val)) {
            foreach ($custom_fields_val as $key => $f) {
                    $objLicense->$key  = isset($f) ? $f : null;
            }
        }
        $objLicense->check_all_versions = $request->has('all_version_check') ? 1 : 0;
        $objLicense->maintained = isset($data['maintained']) && $data['maintained'] ? 1 : 0;
        $objLicense->reassignable = isset($data['reassignable']) && $data['reassignable'] ? 1 : 0;
        $objLicense->invoice_id = $request->invoice_id == "" ? null : implode(",", $request->invoice_id);
        $objLicense->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d/m/Y") : null;
        $objLicense->expiration_date = $request->expiration_date ? CommonHelper::getDateAs($request->expiration_date, "Y-m-d", "d/m/Y") : null;
        $objLicense->termination_date = $request->termination_date ? CommonHelper::getDateAs($request->termination_date, "Y-m-d", "d/m/Y") : null;
        $objLicense->requestable_license = isset($request->requestable_license) ? $request->requestable_license : 0;

        $objLicense->user_id = Auth::user()->id;

        $imagedata=License::find($id)->image;
        if ( $request->input("delete_img", false) ||  $request->image != null) {
            if($imagedata != null) {
                $exits1=File::exists(public_path('/uploads/license/' . $imagedata));
                    if( $exits1 ) {
                        File::delete(public_path('/uploads/license/' . $imagedata));
                        License::where('id', $id)->update(['image' => null]);
                    }
            }
        }
        if($request->image) {
            $uploaded_img = $request->image;
            $Image1 = str_random(12) . str_random(12) . '.' . $uploaded_img->getClientOriginalExtension();
            $path = public_path("uploads/license/" . $Image1);
            Image::make($uploaded_img->getRealPath())->resize(300, null, function($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($path);
            $objLicense->image  = $Image1;
        }

        $objLicense->currency = $request->currency ? $request->currency : $appSettings->default_currency;
        $objLicense->company_id = $request->company_id ? $request->company_id : Auth::user()->company_id;
        $versionIds = [];
        $version1 = $request->input('Version');
        if(isset($data['Version'])) {
            foreach($data['Version'] as $version) {
                $versionCheck = Product::where("Version", $version)->first();
                if(isset($versionCheck) && $versionCheck->count() > 0) {
                    array_push($versionIds,$versionCheck->id);
                }
            }
        }
        $versions = implode(',',$versionIds);
        $objLicense->Version = isset($versions) ? $versions : null;

        $softwareCaption = [];
        if(isset($data['product_id'])) {
            foreach($data['product_id'] as $software) {
                $softwareCheck = Product::where("id", $software)->first();
                if(isset($softwareCheck) && $softwareCheck->count() > 0){
                    array_push($softwareCaption,$softwareCheck->id);
                }
            }
        }
        $softwares = implode(',',$softwareCaption);
        $objLicense->product_id = isset($softwares) ? $softwares : null;
        $objLicense->department_id = $request->department_id ?? null;
        
        if($objLicense->save())
        {
            if ($data["unique_tag"] == "") {
                $objLicense->generateUniqueTag();
            }
            /* Seat Deletions */
            // if($canDeleteSeats && $seatsToDelete) {
            //     $free_seats_coll = $freeSeatsRes->get();
            //     $free_seat_ids = [];

            //     foreach($free_seats_coll as $f) {
            //         $free_seat_ids[] = $f->id;
            //         if(count($free_seat_ids) == $seatsToDelete) {
            //             break;
            //         }
            //     }

            //     $deleted_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
            //     LicenseSeat::whereIn('id', $free_seat_ids)->update([
            //         'deleted_at' => $deleted_at
            //     ]);

            //     $note_text = (count($free_seat_ids)) . ' seat(s) are removed.' ;
            //     $logaction = new Actionlog();
            //     $logaction->asset_id = $id;
            //     $logaction->asset_type = 'software';
            //     $logaction->user_id = Auth::user()->id;
            //     $logaction->note = $note_text;
            //     $logaction->checkedout_to =  NULL;
            //     $logaction->action_type = 'delete seats';
            //     $logaction->save();
            // }
            // elseif($newSeats)
            // {
            //     $seats = [];
            //     $seat_data = [];
            //     $seat_data['created_at'] = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
            //     $seat_data['updated_at'] = $seat_data['created_at'];
            //     $seat_data['license_id'] = $objLicense->id;
            //     $seat_data['user_id'] = Auth::user()->id;

            //     for($i = 1; $i <= $newSeats; $i++) {
            //         $seats[] = $seat_data;
            //     }

            //     $seat_coll = collect($seats);
            //     $seat_coll->chunk(500)->each(function($item) {
            //         LicenseSeat::insert($item->toArray());
            //     });

            //     $logaction = new Actionlog();
            //     $logaction->asset_id = $id;
            //     $logaction->asset_type = 'software';
            //     $logaction->user_id = Auth::user()->id;
            //     $logaction->note = $newSeats." seats are added";
            //     $logaction->action_type = 'add seats';
            //     $logaction->save();
            // }

            $isUpdated = false;
            $updatedFields = [];
            $licenseArray = $objLicense->LicenseDataForCache();
            $comparisonArray = $for_log_comparison;
            foreach ($licenseArray as $key => $value) {
                if (isset($comparisonArray[$key]) && $comparisonArray[$key] != $value) {
                    $isUpdated = true;
                    $updatedFields[] = $key;
                }
            }
            if ($isUpdated) {
                Actionlog::licenseEdited( $objLicense, $for_log_comparison, Auth::user()->id);
                Log::info('License Updated Fields', $updatedFields);
            }
            $return["msg"] = trans('content.licenses_fields.License_has_updated_successfully');
            $return["status"] = "success";
            Log::info("update License id:" . $objLicense->id." uid:" . Auth::user()->id . " : " . json_encode($request->all()));
        }
        return response()->json($return);
    }

    public function licensesExport(Request $request) {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if(! Auth::user()->hasPermissionTo('LicenseDownload') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $default_currency_code = Settings::first()->default_currency;
        $type_filter = "null";
        $fields = array(
            '1' => 'a.name',
            '2' => 'a.seats',
            '3' => 'remaining',
            '4' => 'a.expiration_date', 
            '5' => 'a.purchase_date', 
            '6' => 'a.purchase_cost',
            '7' => 'a.order_number',
            '8' => 'a.updated_at',
            '9' => 'a.check_all_versions'
        );
        
        $db = DB::table('licenses as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        $db->leftJoin('manufacturers as manu', 'manu.id', '=', 'a.manufacturer_id');
        $db->leftJoin('depreciations as dep', 'dep.id', '=', 'a.depreciation_id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id');
        $db->leftJoin('places as p', 'p.id', '=', 'a.internal_place_id');
        $db->leftJoin('departments as depart', 'depart.id', '=', 'a.department_id');
        // $db->leftJoin('itm_network_inventory_products as itm', 'itm.Caption', '=', 'a.product_id');
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            if( isset($filters->showDeletedLicenses) && $filters->showDeletedLicenses == true ) {
                $db->whereNotNull('a.deleted_at');
            }
            else {
                $db->whereNull('a.deleted_at');
            } 
        }
        $db->leftJoin('suppliers as sup', 'a.supplier_id', '=', 'sup.id');

        $db->select('a.*','cmp.name as cmp_name','cat.name as cat_name', 'a.seats', 'a.available_seats', 'sup.name as supplier_name', 'manu.name as manu_name','dep.name as dep_name','a.Version as Version','loc.name as loc_name', 'depart.name as department','p.place as internal_place','unique_tag','a.check_all_versions');
        $dateFormatSql = CommonHelper::mysqlDateTimeFormat('date', 'excel');
        $db->addSelect(DB::raw("DATE_FORMAT(a.purchase_date, '{$dateFormatSql}') as purchase_date_on"));
        $db->addSelect(DB::raw("CASE WHEN a.expiration_date IS NOT NULL THEN DATE_FORMAT(a.expiration_date, '{$dateFormatSql}') ELSE '' END as expire_date_on"));
        $db->addSelect(DB::raw('case when a.expiration_date is not null and a.expiration_date < curdate() then "Expired" when a.purchase_date is not null and (a.expiration_date is null or a.expiration_date >= curdate()) then "Not Expired" when a.expiration_date is not null and a.expiration_date >= curdate() then "Not Expired" else null end as is_expired'));
        
        $dateFormatSql = CommonHelper::mysqlDateTimeFormat('datetime', 'excel');
        $db->addSelect(DB::raw("DATE_FORMAT(a.updated_at, '{$dateFormatSql}') as last_updated_at"));
        $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","LIC",a.id) else "" end as batch_no'));
        $db->addSelect(DB::raw('case when a.purchase_cost is not null then FORMAT(a.purchase_cost, 2) else 0 end as purchase_cost_format'));
        $db->addSelect(DB::raw('a.serial as serial_value'));
        $db->addSelect(DB::raw('case when a.currency is not null then a.currency else "' . $default_currency_code . '" end as currency_code'));
        $db->addSelect(DB::raw("DATE_FORMAT(a.termination_date, '{$dateFormatSql}') as termination_date"));
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('a.location_id', '=', 0);
            } else {
                $db->whereIn('a.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            if(isset($filters->over_all_purchase_filter)) {
                $ids = array_map('intval', explode(',', trim(base64_decode($filters->over_all_purchase_filter), '"')));
                $db->whereIn('a.id', $ids);
            }
            $req = [];

            if(isset($filters->dashboard_filters)){
                $req["dashboard_filters"] = (array) $filters->dashboard_filters;
                $location_filter = isset($req["dashboard_filters"]['location']) ? $req["dashboard_filters"]['location'] : "null";
                if( $location_filter != null && $location_filter != 'null') {
                    $db->whereIn("a.location_id", explode(",", $location_filter));
                }
                $type_filter = isset($req["dashboard_filters"]['type']) ? $req["dashboard_filters"]['type'] : "null";
                $category_id_filter = isset($req["dashboard_filters"]['category_id']) ? $req["dashboard_filters"]['category_id'] : "null";
                if($category_id_filter != null && $category_id_filter != 'null') {
                    $db->whereIn("a.category_id", $category_id_filter);
                }
                $department_filter = isset($req["dashboard_filters"]['department']) ? $req["dashboard_filters"]['department'] : "null";
                if($department_filter != null && $department_filter != 'null') {
                    $db->whereIn("a.department_id", explode(",", $department_filter));
                }
                if(isset($req["dashboard_filters"]["createdFrom"]) && $req["dashboard_filters"]["createdFrom"] && $req["dashboard_filters"]["createdFrom"] != "null" && isset($req["dashboard_filters"]["createdTo"]) && $req["dashboard_filters"]["createdTo"] && $req["dashboard_filters"]["createdTo"] != "null") {
                    $from_date = CommonHelper::getDateAs($req["createdFrom"], "Y-m-d", "d/m/Y");
                    $to_date = CommonHelper::getDateAs($req["createdTo"], "Y-m-d", "d/m/Y");
                    if($from_date && $to_date) {
                        $db->wherebetween('a.created_at',[$from_date, $to_date]);
                    }
                }
            }
            if (isset($filters->search)) {
                $req["search"] = $filters->search;
                $whereStr = sprintf('((case when a.id is not null then concat_ws("","LIC",a.id) else "" end) like "%%%1$s%%" or (case when a.check_all_versions = 1 then "True" else "False" end) like "%%%1$s%%" or depart.name like "%%%1$s%%" or a.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or cmp.name like "%%%1$s%%" or a.agreement_no like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or a.seats like "%%%1$s%%" or a.order_number like "%%%1$s%%" or a.unique_tag like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or a.available_seats like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null then DATE_FORMAT(a.expiration_date, "%%d %%b %%Y") else "" end) like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null and a.expiration_date < curdate() then "Expired" else "" end) like "%%%1$s%%")', $req["search"]);
                $db->whereRaw($whereStr);
            }
            if (isset($filters->other_filters)){
                $filters = (array) $filters->other_filters;
                $db->where(function($query) use($filters) {
                    if (isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                        $query->whereIn("a.manufacturer_id", $filters['manufacturer']);
                    }
                    if (isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                        $query->whereIn("a.category_id", $filters['category']);
                    }
                    if (isset($filters["supplier"]) && $filters['supplier'] && $filters['supplier'] != "null") {
                        $query->whereIn("a.supplier_id", $filters['supplier']);
                    }
                    if(isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                        $query->where("a.location_id", "=", (int) $filters['location']);
                    }
                    if(isset($filters["purchase_reference"]) && $filters['purchase_reference'] && $filters['purchase_reference'] != "null") {
                        $query->whereIn("a.invoice_id", $filters['purchase_reference']);
                    }
                    if(isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                        $query->whereIn("a.internal_place_id", $filters['internal_place']);
                    }
                    if (isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                        $query->whereIn("a.department_id", $filters['department']);
                    }
                    $based_on_possible = ['1'=>'a.purchase_date', '2'=>'a.updated_at'];
                    if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
                        if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                            $daterange = explode(" - ", $filters["date_range"]);
                            $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                            $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                            if ($from_date && $to_date) {
                                $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                                $query->whereRaw($whereStr);
                            }
                        }
                    }
                });
            }
        }
        if( $search_key = trim($request->search) ) {
            $whereStr = sprintf('((case when a.id is not null then concat_ws("","LIC",a.id) else "" end) like "%%%1$s%%" or a.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or cmp.name like "%%%1$s%%" or a.agreement_no like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or a.seats like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or a.available_seats like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null then DATE_FORMAT(a.expiration_date, "%%d %%b %%Y") else "" end) like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null and a.expiration_date < curdate() then "Expired" else "" end) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
        }

        $db->orderBy('a.name');
        $data = $db->get();
        $return['data'] = array();
        $lineArray = [];
        $keys = ["Batch No","Unique Tag","Company","License","Department","Location","Internal Place","Manufacture","Category","Serial","License Name","License Email","Seats","Remaining Seats","Supplier","Order Number","Purchase Date", "Purchase Currency", "Purchase Cost","Agreement No","Expiration Date","Depreciation","Termination Date","Notes","Support","Is Expired","Network Count","Checked All Versions"];
        $settings = Settings::first();
        $customFields = [];
        $prefixed_code = [];
        if($settings->licence_custom_fieldset_id != "") {
            $customFieldset = CustomFieldset::find($settings->licence_custom_fieldset_id);
            if(!empty($customFieldset->fields)) {
                foreach ($customFieldset->fields as $f) {
                    array_push($keys, $f->name);
                }
            }
        }
        $return['data'] = [];
        foreach ($data as $key => $d) {
            $inUseCount = $d->seats - $d->available_seats;
            if($type_filter == 'Available' && $d->available_seats != 0) {
                $return['data'][] = $d;
            } elseif($type_filter == 'In use' && $inUseCount != 0) {
                $return['data'][] = $d;
            } elseif ($type_filter == "Expired License" && $d->is_expired == "Expired") {
                $return['data'][] = $d;
            } elseif ($type_filter == "null") {
                $return['data'][] = $d;
            }
        }
        $data = $return['data'];
        foreach($data as $key => $lic) {
            $version_Array = explode(",", $lic->Version);
            $version_id_Text = '';
            if(!empty($version_Array)) {
                foreach ($version_Array as $ver) {
                    $ver = Product::find($ver);
                    if ($ver) {
                        if ($version_id_Text == '') {
                            $version_id_Text = $ver->Version;
                        } else {
                            $version_id_Text = $version_id_Text . "," . $ver->Version;
                        }
                    }
                }
            }

            $software_Array = explode(",", $lic->product_id);
            $software_id_Text = '';
            if(!empty($software_Array)) {
                foreach ($software_Array as $ver) {
                    $ver = Product::find($ver);
                    if ($ver) {
                        if ($software_id_Text == '') {
                            $software_id_Text = $ver->Caption;
                        } else {
                            $software_id_Text = $software_id_Text . "," . $ver->Caption;
                        }
                    }
                }
            }
            $data[$key]->Version = isset($version_id_Text) ? $version_id_Text : '';
            $data[$key]->Caption = isset($software_id_Text) ? $software_id_Text : '';
            $totalSoftware = Product::where('Caption', $lic->Caption)->count();
            $lic->software_count = $totalSoftware;
            $version =  explode(",", $data[$key]->Version);
            $software = explode(",", $data[$key]->Caption);
            $version = Product::select('id','Caption')->whereIn('Caption', $software)->whereIn('Version',$version)->count();
            $lic->network_count = $version ? $version : 0;
            $return['data'][] = array('a' => $lic);

            $networkCount = $lic->network_count;
            $lic->licensecountnum = 0;
            if (!empty($lic->seats) && $lic->seats > 0 && $lic->added_via == 1) {
                $licensediv = $lic->available_seats / $lic->seats;
                $licensedivper = $licensediv * 100;
                $lic->licensecountnum = number_format($licensedivper, 0);
                $lic->licensecountnum = $lic->licensecountnum.'%';
            } else if (!empty($lic->seats) && $lic->seats > 0 && $lic->added_via == 2) {
                $licensediv = $networkCount / $lic->seats;
                $licenseper = $licensediv * 100;
                $lic->licensecountnum = number_format($licenseper, 0);
                $lic->licensecountnum = $lic->licensecountnum.'%';
            } else {
                $lic->licensecountnum = '0%';
            }

            $line = [
                $lic->batch_no,
                $lic->unique_tag,
                $lic->cmp_name,
                $lic->name,
                $lic->department,
                $lic->loc_name,
                $lic->internal_place,
                $lic->manu_name,
                $lic->cat_name,
                $lic->serial_value,
                $lic->license_name,
                $lic->license_email,
                $lic->seats,
                $lic->available_seats,
                $lic->supplier_name,
                $lic->order_number,
                $lic->purchase_date_on,
                $lic->currency_code,
                $lic->purchase_cost_format,
                $lic->agreement_no,
                $lic->expire_date_on,
                $lic->dep_name,
                $lic->termination_date,
                $lic->notes,
                $lic->support,
                $lic->is_expired,
                $lic->network_count,
                ($lic->check_all_versions == 1 ? 'True' : 'False'),

            ];
            if ($settings->licence_custom_fieldset_id) {
                if(!empty($customFieldset)) {
                    $customaCol = json_decode(json_encode($lic), true);
                    $fieldData = CommonHelper::getCustomData($customFieldset->fields,$customaCol);
                    $dataFieldset = [];
                    foreach ($fieldData  as $k => $f) {
                        $col_name = ucwords(str_replace(['_itm_', '_'], ' ', $k));
                        $dataFieldset[$col_name] = $f;
                    }
                    $line = array_merge($line, array_values($dataFieldset)); 
                }
            }
            $license = License::find($lic->id);
            if (isset($license->category_id) && $license->category->customFieldset && count($license->category->customFieldset->fields)) {
                foreach ($license->category->customFieldset->fields as $field) {
                    $con = '_itm_'.''.str_replace(' ', '_', strtolower($field->name));
                    if (!in_array(ucwords($field->name), $keys)) {
                        array_push($keys, ucwords($field->name));
                    }
                    $modelCusValue = CommonHelper::getCustomDataFormate($field,$license->$con);
                    array_push($line, $modelCusValue);
                }
            }
            $lineArray[] = $line;
        }
        $result = json_decode(json_encode($lineArray, true),true);
        return Excel::download(new LicenceExport($result, $keys), 'LicenceExport.xlsx');
    }

    public function licensesExportPDF(Request $request) {

        $return = ['status' => 'danger', 'msg' => trans('content.licenses_fields.Unable_to_export_the_details')];
        if(! Auth::user()->hasPermissionTo('LicenseDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = [];
        
        $default_currency_code = Settings::first()->default_currency;
        $type_filter = "null";
        $db = DB::table('licenses as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id');
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            if( isset($filters->showDeletedLicenses) && $filters->showDeletedLicenses == true ) {
                $db->whereNotNull('a.deleted_at');
            }
            else {
                $db->whereNull('a.deleted_at');
            } 
        }
        $db->leftJoin('suppliers as sup', 'a.supplier_id', '=', 'sup.id');

        $db->select('a.id', 'a.name', 'cmp.name as cmp_name','cat.name as cat_name','a.order_number', 'a.seats', 'a.available_seats', 'a.agreement_no', 'sup.name as supplier_name', 'a.license_email', 'a.support','a.Version as Version','a.product_id','a.added_via','unique_tag','a.check_all_versions');
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('case when dayname(a.expiration_date) is not null then DATE_FORMAT(a.expiration_date, "%d %b %Y") else "" end as expire_date_on'));
        $db->addSelect(DB::raw('case when dayname(a.expiration_date) is not null and a.expiration_date < curdate() then "Expired" else "" end as is_expired'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        $db->addSelect(DB::raw('case when a.id is not null then concat_ws("","LIC",a.id) else "" end as batch_no'));
        $db->addSelect(DB::raw('case when a.purchase_cost is not null then FORMAT(a.purchase_cost, 2) else 0 end as purchase_cost_format'));
        $db->addSelect(DB::raw('a.serial as serial_value'));
        $db->addSelect(DB::raw('case when a.currency is not null then a.currency else "' . $default_currency_code . '" end as currency_code'));
        
        // $db->whereNull('a.deleted_at');
        $loc_previllage = Auth::user()->permitted_locations; //getting user's table
        $settings = Settings::getSettings();
        $permitted_loc = explode(",", $loc_previllage);
        if($settings->location_config == 1) {
            if(empty($loc_previllage)) {
                $db->where('a.location_id', '=', 0);
            } else {
                $db->whereIn('a.location_id', $permitted_loc);
            }
        }

        // check asset dept permission
        if ($settings->department_config == 1) {
            $asset_dept_permission = Auth::user()->asset_departments_id; //check from user's table
            $asset_depts = explode(",", $asset_dept_permission);
            if (empty($asset_dept_permission)) {
                $db->where('a.department_id', '=', 0);
            } else {
                $db->whereIn('a.department_id', $asset_depts);
            }
        }
        $companyIds = CommonHelper::getSelectedCompanyIds();
        $db->whereIn("a.company_id", $companyIds);
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if(isset($filters->dashboard_filters)){
                $req["dashboard_filters"] = (array) $filters->dashboard_filters;
                $location_filter = isset($req["dashboard_filters"]['location']) ? $req["dashboard_filters"]['location'] : "null";
                if( $location_filter != null && $location_filter != 'null') {
                    $db->whereIn("a.location_id", explode(",", $location_filter));
                }
                $type_filter = isset($req["dashboard_filters"]['type']) ? $req["dashboard_filters"]['type'] : "null";
                $category_id_filter = isset($req["dashboard_filters"]['category_id']) ? $req["dashboard_filters"]['category_id'] : "null";
                if($category_id_filter != null && $category_id_filter != 'null') {
                    $db->whereIn("a.category_id", $category_id_filter);
                }
                $department_filter = isset($req["dashboard_filters"]['department']) ? $req["dashboard_filters"]['department'] : "null";
                if($department_filter != null && $department_filter != 'null') {
                    $db->whereIn("a.department_id", explode(",", $department_filter));
                }
                if(isset($req["dashboard_filters"]["createdFrom"]) && $req["dashboard_filters"]["createdFrom"] && $req["dashboard_filters"]["createdFrom"] != "null" && isset($req["dashboard_filters"]["createdTo"]) && $req["dashboard_filters"]["createdTo"] && $req["dashboard_filters"]["createdTo"] != "null") {
                    $from_date = CommonHelper::getDateAs($req["createdFrom"], "Y-m-d", "d/m/Y");
                    $to_date = CommonHelper::getDateAs($req["createdTo"], "Y-m-d", "d/m/Y");
                    if($from_date && $to_date) {
                        $db->wherebetween('a.created_at',[$from_date, $to_date]);
                    }
                }

            }

            if (isset($filters->search)) {
                $req["search"] = $filters->search;
                $whereStr = sprintf('((case when a.id is not null then concat_ws("","LIC",a.id) else "" end) like "%%%1$s%%" or (case when a.check_all_versions = 1 then "True" else "False" end) like "%%%1$s%%" or a.name like "%%%1$s%%" or unique_tag like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or cmp.name like "%%%1$s%%" or a.agreement_no like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or a.seats like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or a.available_seats like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null then DATE_FORMAT(a.expiration_date, "%%d %%b %%Y") else "" end) like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null and a.expiration_date < curdate() then "Expired" else "" end) like "%%%1$s%%")', $req["search"]);
                $db->whereRaw($whereStr);
            }
            if (isset($filters->other_filters)){
                $filters = (array) $filters->other_filters;
                $db->where(function($query) use($filters) {
                    if (isset($filters["manufacturer"]) && $filters['manufacturer'] && $filters['manufacturer'] != "null") {
                        $query->whereIn("a.manufacturer_id", $filters['manufacturer']);
                    }

                    if (isset($filters["category"]) && $filters['category'] && $filters['category'] != "null") {
                        $query->whereIn("a.category_id", $filters['category']);
                    }
                    if (isset($filters["supplier"]) && $filters['supplier'] && $filters['supplier'] != "null") {
                        $query->whereIn("a.supplier_id", $filters['supplier']);
                    }
                    if (isset($filters["location"]) && $filters['location'] && $filters['location'] != "null") {
                        $query->where("a.location_id", $filters['location']);
                    }
                    if(isset($filters["purchase_reference"]) && $filters['purchase_reference'] && $filters['purchase_reference'] != "null") {
                        $query->whereIn("a.invoice_id", $filters['purchase_reference']);
                    }
                    if(isset($filters["internal_place"]) && $filters['internal_place'] && $filters['internal_place'] != "null") {
                        $query->whereIn("a.internal_place_id", $filters['internal_place']);
                    }
                    if (isset($filters["department"]) && $filters['department'] && $filters['department'] != "null") {
                        $query->whereIn("a.department_id", $filters['department']);
                    }
                    $based_on_possible = ['1'=>'a.purchase_date', '2'=>'a.updated_at'];
                    if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
                        if (isset($filters["date_range"]) && $filters["date_range"] && $filters["date_range"] != "null") {
                            $daterange = explode(" - ", $filters["date_range"]);
                            $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                            $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                            if ($from_date && $to_date) {
                                $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                                $query->whereRaw($whereStr);
                            }
                        }
                    }
                });
            }
        }

        if( $search_key = trim($request->search) ) {
            $whereStr = sprintf('((case when a.id is not null then concat_ws("","LIC",a.id) else "" end) like "%%%1$s%%" or (case when a.check_all_versions = 1 then "True" else "False" end) like "%%%1$s%%" or a.name like "%%%1$s%%" or unique_tag like "%%%1$s%%" or cat.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or cmp.name like "%%%1$s%%" or a.agreement_no like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or a.seats like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or a.available_seats like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null then DATE_FORMAT(a.expiration_date, "%%d %%b %%Y") else "" end) like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null and a.expiration_date < curdate() then "Expired" else "" end) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
        }

        $db->orderBy('a.name');
        $data = $db->get();
        $return['data'] = array();
        foreach($data as $key => $lic) {
            $version_Array = explode(",", $lic->Version);
            $version_id_Text = '';
            if(!empty($version_Array)) {
                foreach ($version_Array as $ver) {
                    $ver = Product::find($ver);
                    if ($ver) {
                        if ($version_id_Text == '') {
                            $version_id_Text = $ver->Version;
                        } else {
                            $version_id_Text = $version_id_Text . "," . $ver->Version;
                        }
                    }
                }
            }
            $software_Array = explode(",", $lic->product_id);
            $software_id_Text = '';
            if(!empty($software_Array)) {
                foreach ($software_Array as $ver) {
                    $ver = Product::find($ver);
                    if ($ver) {
                        if ($software_id_Text == '') {
                            $software_id_Text = $ver->Caption;
                        } else {
                            $software_id_Text = $software_id_Text . "," . $ver->Caption;
                        }
                    }
                }
            }
            $data[$key]->Version = isset($version_id_Text) ? $version_id_Text : '';
            $data[$key]->Caption = isset($software_id_Text) ? $software_id_Text : '';
            $totalSoftware = Product::where('Caption', $lic->Caption)->count();
            $lic->software_count = $totalSoftware;
            $version =  explode(",", $data[$key]->Version);
            $software =  explode(",", $data[$key]->Caption);
            $version = Product::select('id','Caption')->whereIn('Caption', $software)->whereIn('Version',$version)->count();
            $lic->network_count = $version ? $version : 0;
            $return['data'][] = array('a' => $lic);
        }
        $return['data'] = array();
        foreach($data as $d) {
            $inUseCount = $d->seats - $d->available_seats;
            if($type_filter == 'Available' && $d->available_seats != 0) {
                $return['data'][] = $d;
            } elseif($type_filter == 'In use' && $inUseCount != 0) {
                $return['data'][] = $d;
            } elseif ($type_filter == "Expired License" && $d->is_expired == "Expired") {
                $return['data'][] = $d;
            } elseif ($type_filter == "null") {
                $return['data'][] = $d;
            }
            $networkCount = $d->network_count;
            $d->licensecountnum = 0;
            if (!empty($d->seats) && $d->seats > 0 && $d->added_via == 1) {
                $licensediv = $d->available_seats / $d->seats;
                $licensedivper = $licensediv * 100;
                $d->licensecountnum = number_format($licensedivper, 0);
                $d->licensecountnum = $d->licensecountnum.'%';
            }
            else if (!empty($d->seats) && $d->seats > 0 && $d->added_via == 2) {
                $licensediv = $networkCount / $d->seats;
                $licenseper = $licensediv * 100;
                $d->licensecountnum = number_format($licenseper, 0);
                $d->licensecountnum = $d->licensecountnum.'%';
            } else {
                $d->licensecountnum = '0%';
            }
        }
        $data = $return['data'];
        $properties = [];
        $properties['format'] = 'Legal-L';
        
        $vd["records"] = $data;
        return PDF::view('licenses.for_export', $vd)->landscape()->format('Legal-L')->name('licenses.pdf')->download();
    }

    public function restoreLicense(Request $request, $id) {
    
        $return = array("status" => "failure", "msg" => "Unable to restore the Licenses");
        if( ! Auth::user()->hasPermissionTo('LicenseRestore') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $licenses = License::withTrashed()->where("id","=", $id)->first();
        if(!$licenses->exists) {
            return response()->json($return);
        }
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($licenses->company_id, $companyIds)) {
            $return["status"] = 'error';
            $return["msg"] = "You don't have access to this company.";
            return response()->json($return);
        }
        $licenseNotDelete = License::where("serial",$licenses->serial)->first();
        if(isset($licenseNotDelete->exists)) {
            $return["status"] = 'error';
            $return["msg"] = trans('content.licenses_fields.already_restored_data');
            return response()->json($return);
        }

        $licenses->restore();
        LicenseSeat::where('license_id','=',$id)->restore();
        Actionlog::licenseRestore($licenses,Auth::user()->id);

        $return["msg"] = trans('content.licenses_fields.licenses_restored');
        $return["status"] = "success";
        return response()->json($return);
    }
    public function checkout(Request $request, $license_id) {

        $return = ['status' => 'fail', 'msg' => trans('licenses.license_checkout.unable_to_checkout_the_license')];
        if(! Auth::user()->hasPermissionTo('LicenseCheckout') || !config("services.assets.enabled")) {
            $return["msg"] = trans('licenses.license_checkout.permission_denied');
            return response()->json($return);
        }
        $appSettings = Settings::first();

        if(! Auth::user()->company_id) {
            $return['msg'] = trans('licenses.license_checkout.you_are_not_allowed');
            return response()->json($return);       
        }

        try {
            $lic = License::findOrFail($license_id);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }

        $companyIds = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($lic->company_id, $companyIds)) {
            $return["msg"] = "You don't have access to this company.";
            return response()->json($return);
        }
        
        $rules = array(
            'assigned_to' => 'nullable',
            'assigned_for' => 'required|min:1|max:2',
            'checkoutnotes' => 'nullable|clean_text_only|max:2000',
            'asset_id' => 'required_without:assigned_to',
            // 'expected_checkin' => 'nullable|date_format:d-m-Y'
        );
        $messages = [
            'asset_id.required_without'=>'Please select device to assign license'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        if ($request->assigned_for == 1) {
            $licenceDevice = LicenseSeat::where('license_id', '=', $license_id)->whereNull('deleted_at')->whereNotNull('asset_id')->pluck('asset_id') ->toArray();
            if (in_array($request->asset_id, $licenceDevice)) {
                $return["msg"] = trans('licenses.license_checkout.licence_already_assign_to_device');
                return response()->json($return);
            }

        }
        if ($request->assigned_for == 2) {
            $licenceUser = LicenseSeat::where('license_id', '=', $license_id)->whereNull('deleted_at')->whereNotNull('assigned_to')->pluck('assigned_to')->toArray();
            if (in_array($request->assigned_to, $licenceUser)) {
                $return["msg"] = trans('licenses.license_checkout.licence_already_assign_to_user');
                return response()->json($return);
            }
        }

        if($request->assigned_to){
            $user = User::find($request->assigned_to);
            if(empty($user)) {
                $return["msg"] = trans('licenses.license_checkout.User_is_not_available');
                return response()->json($return);
            }
            if($user->checkoutBasicClearance()){
                $return["msg"] = trans('licenses.license_checkout.chosen_user_is_not_active');
                return response()->json($return);
            }
            if($user->deleted_at){
                $return["msg"] = trans('licenses.license_checkout.Chosen_User_is_not_found');
                return response()->json($return);
            }
            if($user->checkLastWorkingDate()){
                $return["msg"] = trans('licenses.license_checkout.this_users_last_working');
                return response()->json($return);
            }
        } elseif ($request->asset_id ) {
            $device = Device::find($request->asset_id);
            if (empty($device)) {
                $return["msg"] = trans('licenses.license_checkout.device_is_not');
                return response()->json($return);
            }
        
            if($device->assigned_to != $request->assigned_to && $request->assigned_to != '')  {
                $return["msg"] = trans('licenses.license_checkout.device_user');
                return response()->json($return);
            }
            if($device->status->sold == 1){
                $return["msg"] = trans('licenses.license_checkout.chosen_device');
                return response()->json($return);
            }
            if($device->status->stolen_item == 1){
                $return["msg"] = trans('licenses.license_checkout.chosen_device_is_in_lost');
                return response()->json($return);
            }
        }

        $availableSeats = License::where('id', $license_id)->value('available_seats');
        if ($availableSeats > 0) {
            $licenceSeat = new LicenseSeat();
            $licenceSeat->license_id = $license_id;
            if($lic->purchase_cost != null && $lic->seats > 0) {
            $licence_cost = ($lic->purchase_cost / $lic->seats);
            $licenceSeat->cost = $licence_cost;
            }
            $licenceSeat->assigned_to = isset($request->assigned_to) ? $request->assigned_to : null;
            $licenceSeat->asset_id = isset($request->asset_id) ? $request->asset_id : null;
            $licenceSeat->expected_checkin = $request->expected_checkin ? CommonHelper::getDateAs($request->expected_checkin,"Y-m-d", "d/m/Y"):null;
            $licenceSeat->notes = $request->note;
            $licenceSeat->user_id = Auth::user()->id;
            $licenceSeat->serial = $lic->serial;
        
            if($licenceSeat->save()){
                $logaction = Actionlog::licenseCheckout(
                    $lic->id,
                    $request->assigned_for,
                    $request->assigned_for == 1 ? $request->asset_id : $request->assigned_to,
                    $request->location_id,
                    Auth::id(),
                    $request->checkoutnotes
                );

                $lic->available_seats = $lic->available_seats - 1;
                $lic->touch();
                try {
                    $this->notifyCategoryThresould($lic->category_id);
                }
                catch(\Exception $e) {
                    Log::error("Thershold check issue after checkout ");
                    Log::error($e->getMessage());
                }
                if(Settings::first()->alerts_enabled == 1 && $request->assigned_to && $user){
                    try {
                        $alertnotify = CommonHelper::getGlobalAlertEmail();
                        if(config('mail.service_enabled') && ($lic->requireAcceptance() || $lic->getEula()) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            if(filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                                Mail::to($user->email)->cc($alertnotify)->queue(new LicenseCheckoutNotification($lic, $logaction, $user));
                            }
                            else {
                        
                                Mail::to($user->email)->queue(new LicenseCheckoutNotification($lic, $logaction, $user));
                            }
                        }
                    }
                    catch(\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
                $return["status"] = "success";
                $return["msg"] = trans('licenses.license_checkout.license_checked_out_successfully');
                if( $request->assigned_to ){
                    $return["msg"].= ' to '.$user->first_name.' '.$user->last_name ;
                }
                if( $request->asset_id ){
                    $return["msg"].= ' with '.$device->asset_tag . '' ;
                }
                Log::info("Lic checkout id:" . $licenceSeat->id." uid:" . Auth::user()->id . " : " . json_encode($request->all()));
            }
        }
        else{ $return["msg"] = trans('licenses.license_checkout.licence_seat_not_available');
            return response()->json($return);
        }
        return response()->json($return);

    }
    public function notifyCategoryThresould($category_id) {
        if(! $category_id) {
            return;
        }

        $alertmail = null;
        $ThresholdSettings = ThresholdSettings::first();
        $thresouldDtl   = Threshold::where('cat_id','=',$category_id)->first();
        $available_licenses          = License::getavailableLicense($category_id)[0]->tot_available;

        if(empty($thresouldDtl))         return;
       
        if( $ThresholdSettings->threshold_enabled && $ThresholdSettings->alerts_enabled && $thresouldDtl->alerts_enabled ) {
            if($ThresholdSettings->send_alerts == 0){
                if(Settings::first()->alerts_enabled == 1)
                $alertmail = CommonHelper::getGlobalAlertEmail();
            }
            if($ThresholdSettings->send_alerts == 1){
                $alertmail = $ThresholdSettings->email;
            }
        }
        
        if(!empty($alertmail) && $thresouldDtl->threshold > 0 ) {

            $catDetail = Category::find($category_id);
          
            if($available_licenses < $thresouldDtl->threshold) {
                if(config('mail.service_enabled')) {
                    Mail::to($alertmail)->queue(new ThreshouldLicenseNotification($catDetail,$thresouldDtl->threshold,$available_licenses) );
                    $thresouldDtl->notify_count = $thresouldDtl->notify_count + 1 ;
                    $thresouldDtl->last_notified_date = date('Y-m-d');
                }
                $thresouldDtl->save();
            }
        }
    }

    public function getCategoryOptions(Request $request){
       
        $return = [];
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);
        $db = DB::table("categories")->select("id", "name as text")->whereNull("deleted_at")->where("category_type", "license");
        if ($search) {
            $db->where("name", "like", "%" . $search . "%");
        }

        $count = $db->count();

        $result = $db->orderBy("name", "ASC")
            ->skip($skip)
            ->take(20)
            ->get();

        $return["pagination"] = [
            "more" => ($count - ($page * 20)) > 0
        ];

        $return["results"] = $result->toArray();

        return response()->json($return);
    }

    public function getDepreciationOptions(Request $request){

        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $limit = 20;
        $skip = ($page - 1) * $limit;

        $query = Depreciation::select('id', 'name as text');

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $total = $query->count();

        $results = $query->orderBy('name')
            ->skip($skip)
            ->take($limit)
            ->get();

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($skip + $limit) < $total
            ]
        ]);
    }

    public function getSupplierByAjax(Request $request){
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $limit = 20;
        $skip = ($page - 1) * $limit;

        $query = Supplier::select('id', 'name as text');

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $total = $query->count();

        $results = $query->orderBy('name')
            ->skip($skip)
            ->take($limit)
            ->get();

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($skip + $limit) < $total,
            ],
        ]);
    }

    public function getCurrencyOptions(Request $request){
        $search = strtolower(trim($request->search ?? ''));
        $page = (int) ($request->page ?? 1);
        $perPage = 20;

        $currencies = collect(Currency::getCurrencies())
            ->map(function ($currency, $code) {
                return [
                    'id' => $code,
                    'text' => $code . ' - ' . $currency['name'],
                ];
            });

        if ($search != '') {
            $currencies = $currencies->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['id']), $search)
                    || str_contains(strtolower($item['text']), $search);
            });
        }

        $count = $currencies->count();

        $results = $currencies
            ->values()
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $count,
            ],
        ]);
    }

    public function getCompanyByAjax(){
        return response()->json([
            "results" => Company::select('id', 'name as text')
                ->orderBy('name')
                ->get()
        ]);
    }

    public function licenceDetail(Request $request, $license_id) {

        $return = ['status' => 'danger', 'msg' => trans('licenses.license_detail.permission_denied')];
        if(! Auth::user()->hasPermissionTo('LicenseView') || !config("services.assets.enabled")) {
            return redirect('dashboard')->with("msg", $return);
        }
        $objLicense = License::where('id','=',$license_id)->with(['company','supplier','licenseseats','category',
         'manufacture'])->first();
        $licenceSeat = LicenseSeat::where('license_id','=',$license_id)->whereNull('deleted_at')->first();
        if(empty($objLicense))
            return redirect('licenses')->withMsg(['status'=>'error','msg'=>trans('licenses.license_detail.requested_license_not_found')]);

        $companyIds = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($objLicense->company_id, $companyIds)) {
            $return["msg"] = "You don't have access to this company.";
            return redirect('dashboard')->with("msg", $return);
        }

        $companyId = CommonHelper::getAccessibleCompanyIds();
        $vd = new stdClass;
        $vd->companies = Company::select('id', 'name as text')->whereIn('id', $companyId)->orderBy('name')->get();
        $vd->categories = Category::whereNull("deleted_at")->where("category_type" ,"like" ,"license" )->select('id', 'name as text')->orderBy('name')->get();
        $vd->manufacturers = Manufacture::select('id', 'name as text')->orderBy('name')->get();
        $vd->suppliers = Supplier::select('id', 'name as text')->orderBy('name')->get();
        $vd->depreciations = Depreciation::select('id', 'name as text')->orderBy('name')->get();
        $vd->currencies = Currency::getCurrencies();
        $vd->locations = Location::select('id', 'name as text')->orderBy('name')->get();
        $vd->departments = Department::select('id', 'name as text')->orderBy('name')->get();
        $vd->assignedForOptions = License::getAssignedForOptions();
        if ($objLicense->invoice_id) {
            $invoice = explode(",", $objLicense->invoice_id);
            $vd->invoices = Purchase::whereIn("id", $invoice)->select("id", "invoice_no", 'invoice_date')->get();
        }
        $companyFieldset = CustomFieldset::where('id', Settings::first()->licence_custom_fieldset_id)->first();
        $path = null;
        if ($objLicense->image && file_exists(public_path('uploads/license/' . $objLicense->image))) {
            $path = asset('uploads/license/' . $objLicense->image);
        } 
        $requestable_enabled = config('app.requestable_enabled');
        $isCheckinCall = false;
        if($request->seatid && is_numeric($request->seatid) && $request->popup == "checkin") {
            $get_ls_record = DB::table('license_seats as ls')
            ->leftJoin('licenses as l', 'l.id', '=', 'ls.license_id')
            ->whereNull('ls.deleted_at')
            ->where(function ($q) {
            $q->whereNotNull('ls.assigned_to')
                ->orWhereNotNull('ls.asset_id');
            })
            ->select(DB::raw('ls.id as license_seat_id'))
            ->where('ls.id', $request->seatid)->limit(1)->get();
            if(count($get_ls_record) > 0) {
                $ls_record = $get_ls_record[0];
                $isCheckinCall = [
                    "id" => $ls_record->license_seat_id
                ];
            }
        }
        return view("licenses.detail-view")->with('licence',$objLicense)->with('licenceseat',$licenceSeat)->with('companyFieldset', $companyFieldset)->with('vd', $vd)->with('path',$path)->with('requestable_enabled', $requestable_enabled)->with('isCheckinCall', $isCheckinCall);
    }

    public function ajaxlicenseSeat($licenceId,Request $request) {

        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            '1' => 'ls.id',
            '2' => 'serial_no',
            '3' => 'endUserName',
            '4' => 'asset_tag',
            '5' => 'ls.expected_checkin',
            '6' => 'ls.location'
        );

        $db = DB::table('license_seats as ls');
        $db->leftJoin('licenses as  l', 'ls.license_id', '=', 'l.id');
        $db->leftJoin('users as enduser', 'enduser.id', '=', 'ls.assigned_to');
        $db->leftJoin('assets as device', 'device.id', '=', 'ls.asset_id');
        $db->leftJoin('locations as locd', 'locd.id', '=', 'device.rtd_location_id');
        $db->leftJoin('locations as locu', 'locu.id', '=', 'enduser.location_id');

        $db->select('ls.id', 'ls.notes','ls.serial','device.id as deviceid','device.asset_tag as asset_tag','enduser.id as enduserid','ls.expected_checkin as lic_expected_checkin','ls.updated_at as lic_updated_at_date','l.company_id');
        $db->addSelect(DB::raw('case when ls.serial is not null then ls.serial  else "" end as serial_no'));
        $db->addSelect(DB::raw('CONCAT_WS(" ", COALESCE(enduser.displayName, CONCAT(enduser.first_name, " ", enduser.last_name)), "-", enduser.username) as endUserName'));
        $db->addSelect(DB::raw('DATE_FORMAT(ls.expected_checkin, "%d-%b-%Y") as expected_checkin_format'));
        $db->addSelect(DB::raw('case when ls.asset_id is not null then locd.name when ls.assigned_to is not null then locu.name else "" end as location'));
        $db->where('ls.license_id', '=', $licenceId);
        
        // if( isset($req["showDeletedLicenses"]) && $req["showDeletedLicenses"] == "true" ) {
        //     $db->whereNotNull('a.deleted_at');
        // }
        // else {
            $db->whereNull('ls.deleted_at');
        // }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if( isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"]) ) {
            $whereStr = sprintf('(concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or ls.notes like "%%%1$s%%" or device.asset_tag like "%%%1$s%%" or ls.serial like "%%%1$s%%" or locd.name like "%%%1$s%%" or locu.name like "%%%1$s%%" or ls.id like "%%%1$s%%" or DATE_FORMAT(ls.expected_checkin,  "%%d-%%b-%%Y") like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
            $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
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

        foreach($data as $key => $value ) {
                $value->seatcount = 'Seat '.($key+1).'( #'.$value->id.' ) '.( empty($value->serial) ? '' : ' ');
                $return['data'][] = array( 'a' => $value );
        }
        return response()->json($return);
    }

    public function downloadpdflicenseSeats($licenceId,Request $request) {
        
        $return = ['status' => 'danger', 'msg' =>trans('content.licenses_fields.Unable_to_export_the_details')];
        if(! Auth::user()->hasPermissionTo('LicenseDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = [];
        $req = $request->all();
        $db = DB::table('license_seats as ls');
        $db->leftJoin('licenses as  l', 'ls.license_id', '=', 'l.id');
        $db->leftJoin('users as enduser', 'enduser.id', '=', 'ls.assigned_to');
        $db->leftJoin('assets as device', 'device.id', '=', 'ls.asset_id');
        $db->leftJoin('manufacturers as m', 'm.id', '=', 'l.manufacturer_id');
        $db->leftJoin('categories as c', 'c.id', '=', 'l.category_id');
        $db->leftJoin('locations as locd', 'locd.id', '=', 'device.rtd_location_id');
        $db->leftJoin('locations as locu', 'locu.id', '=', 'enduser.location_id');

        $db->select('ls.id as id', 'l.name as license_name','m.name as manu_name','c.name as cat_name','device.name as device_name','l.notes as notes','ls.serial','device.id as deviceid','device.asset_tag as asset_tag','enduser.id as enduserid','enduser.email as email','enduser.employee_num as emp_code','enduser.username as checkout_user');
        $db->addSelect(DB::raw('concat(enduser.first_name, " ", enduser.last_name) as endUserName'));
        $db->addSelect(DB::raw('case when l.id is not null then concat_ws("","LIC",l.id) else "" end as batch_no'));
        $db->addSelect(DB::raw('case when ls.serial is not null then ls.serial when ls.serial is null then l.serial else "" end as serial_no'));
        $db->addSelect(DB::raw('case when l.purchase_cost is not null then FORMAT(l.purchase_cost, 2) else 0 end as purchase_cost_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(ls.expected_checkin, "%d-%b-%Y") as expected_checkin_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(l.purchase_date, "%d-%b-%Y") as purchase_date'));
        $db->addSelect(DB::raw('case when dayname(l.expiration_date) is not null then DATE_FORMAT(l.expiration_date, "%d %b %Y") else "" end as expire_date_on'));
        $db->addSelect(DB::raw('case when dayname(l.expiration_date) is not null and l.expiration_date < curdate() then "Expired" else "" end as is_expired'));
        $db->addSelect(DB::raw('case when ls.asset_id is not null then locd.name when ls.assigned_to is not null then locu.name else "" end as location'));
        $db->where('ls.license_id', '=', $licenceId);
        
        $db->whereNull('ls.deleted_at');

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if(isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                $whereStr = sprintf('((case when l.id is not null then concat_ws("","LIC",l.id) else "" end) like "%%%1$s%%" or concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or l.notes like "%%%1$s%%" or locd.name like "%%%1$s%%" or locu.name like "%%%1$s%%" or device.asset_tag like "%%%1$s%%" or ls.serial like "%%%1$s%%" )', $search_key);
                $db->whereRaw($whereStr);
            }
        }

        $data = $db->get();
        $properties = [];
        $properties['format'] = 'Legal-L';
        
        $vd["records"] = $data;
        $pdf = PDF::loadView('licenses.for_export_lic', $vd, [], $properties);
        return $pdf->download('License Checkout Details.pdf');
    }
    
    public function downloadlicenseSeats($licenceId,Request $request) {
        
        $return = ['status' => 'danger', 'msg' => trans('content.licenses_fields.Unable_to_export_the_details')];
        if(! Auth::user()->hasPermissionTo('LicenseDownload') || !config("services.assets.enabled")) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return redirect('dashboard')->with("msg", $return);
        }
        $req  = $request->all();
        $db = DB::table('license_seats as ls');
        $db->leftJoin('licenses as  l', 'ls.license_id', '=', 'l.id');
        $db->leftJoin('users as enduser', 'enduser.id', '=', 'ls.assigned_to');
        $db->leftJoin('assets as device', 'device.id', '=', 'ls.asset_id');
        $db->leftJoin('manufacturers as m', 'm.id', '=', 'l.manufacturer_id');
        $db->leftJoin('categories as c', 'c.id', '=', 'l.category_id');
        $db->leftJoin('locations as locd', 'locd.id', '=', 'device.rtd_location_id');
        $db->leftJoin('locations as locu', 'locu.id', '=', 'enduser.location_id');
      //$db->leftJoin('asset_logs as a', 'a.note', '=', 'ls.notes');

        $db->select('ls.id as id', 'l.name as license_name','m.name as manu_name','c.name as cat_name','device.name as device_name','l.notes as notes','ls.serial','device.id as deviceid','device.asset_tag as asset_tag','enduser.id as enduserid','enduser.email as email','enduser.employee_num as emp_code','enduser.username as checkout_user');
        $db->addSelect(DB::raw('CONCAT_WS(" ", COALESCE(enduser.displayName, CONCAT(enduser.first_name, " ", enduser.last_name)), "-", enduser.username) as endUserName'));
        $db->addSelect(DB::raw('case when ls.serial is not null then ls.serial when ls.serial is null then l.serial else "" end as serial_no'));
        $db->addSelect(DB::raw('case when l.id is not null then concat_ws("","LIC",l.id) else "" end as batch_no'));
        $db->addSelect(DB::raw('case when l.purchase_cost is not null then FORMAT(l.purchase_cost, 2) else 0 end as purchase_cost_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(ls.expected_checkin, "%d-%b-%Y") as expected_checkin_format'));
        $db->addSelect(DB::raw('DATE_FORMAT(l.purchase_date, "%d-%b-%Y") as purchase_date'));
        $db->addSelect(DB::raw('case when dayname(l.expiration_date) is not null then DATE_FORMAT(l.expiration_date, "%d %b %Y") else "" end as expire_date_on'));
        $db->addSelect(DB::raw('case when dayname(l.expiration_date) is not null and l.expiration_date < curdate() then "Expired" else "" end as is_expired'));
        $db->addSelect(DB::raw('case when ls.asset_id is not null then locd.name when ls.assigned_to is not null then locu.name else "" end as location'));
        $db->where('ls.license_id', '=', $licenceId);
        
        // if( isset($req["showDeletedLicenses"]) && $req["showDeletedLicenses"] == "true" ) {
        //     $db->whereNotNull('a.deleted_at');
        // }
        // else {
            $db->whereNull('ls.deleted_at');
        // }

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];
            if(isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
                $whereStr = sprintf('((case when l.id is not null then concat_ws("","LIC",l.id) else "" end) like "%%%1$s%%" or concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or l.notes like "%%%1$s%%" or locd.name like "%%%1$s%%" or locu.name like "%%%1$s%%" or device.asset_tag like "%%%1$s%%" or ls.serial like "%%%1$s%%" )', $search_key);
                $db->whereRaw($whereStr);
            }
        }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        $records = $db->get();

        $data = [];
        foreach($records as $r) {
                $data[] = [
                $r->batch_no,
                $r->serial_no,
                $r->license_name,
                $r->manu_name,
                $r->cat_name,
                $r->asset_tag,
                $r->device_name,
                $r->checkout_user,
                $r->endUserName,
                $r->email,
                $r->emp_code,
                $r->notes,
                $r->expected_checkin_format,
                $r->purchase_cost_format,
                $r->purchase_date,
                $r->expire_date_on,
                $r->is_expired,
                $r->location
            ];
        }
        return Excel::download(new LicenseInfoExport($data), 'License Checkout Details.xlsx');

    }

    public function checkinLicenseSeat(Request $request, $seatid) {

        $return = ['status'=>'fail', 'msg'=>trans('licenses.license_checkin.unable_to_checkin_the_license')];

        $appSettings = Settings::first();

        try {
            $seatDtl = LicenseSeat::findOrFail($seatid);
            $user = User::find($seatDtl->assigned_to);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }
        try {
            $licenceDtl = License::findOrFail($seatDtl->license_id);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }

        if( ! Auth::user()->company_id ) {
            $return['msg'] = trans('licenses.license_checkin.insufficient_permission_please_update');
            return response()->json($return);
        }

        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($licenceDtl) ) {
            $return["msg"] = trans('licenses.license_checkin.multiple_company_access');
            return response()->json($return);
        }
        $companyIds = CommonHelper::getAccessibleCompanyIds();
        if (!in_array($licenceDtl->company_id, $companyIds)) {
            $return["msg"] = trans('licenses.license_checkin.dont_access_this_comp');
            return response()->json($return);
        }

        if(!$licenceDtl->reassignable){
            $return['msg'] = trans('licenses.license_checkin.this_license_is_not_reassignable');
            return response()->json($return);
        }
    
        $checkedout_to = isset($seatDtl->assigned_to) ? $seatDtl->assigned_to : $seatDtl->asset_id;
        $asset_id = $seatDtl->license_id;
        $assigned_for = isset($seatDtl->assigned_to) ? 2 : 1;
        
        if($seatDtl->delete()) {

            $logaction = Actionlog::licenseCheckin($licenceDtl->id, $checkedout_to, $asset_id, $assigned_for, $request->checkinnotes, Auth::user()->id);

            if ($licenceDtl) {
                $licenceDtl->available_seats++;
                $licenceDtl->save();
            }
           
            $logaction->save();
            $licenceDtl->touch();
            // dd(config('mail.service_enabled'),$licenceDtl->isNeedCheckinMail(),$user,filter_var($user->email, FILTER_VALIDATE_EMAIL));
            
            if(config('mail.service_enabled') && $licenceDtl->isNeedCheckinMail() && $user && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                if (Settings::first()->alerts_enabled == 1) {
                    $alertnotify = CommonHelper::getGlobalAlertEmail();
                    $alertnotify = array_filter($alertnotify, function ($email) {
                        return filter_var($email, FILTER_VALIDATE_EMAIL);
                    });
                }
                if($alertnotify) {
                    Mail::to($user->email)->cc($alertnotify)->queue(new LicenseCheckinNotification($licenceDtl, $logaction, $user));
                }
                else {
                    Mail::to($user->email)->queue(new LicenseCheckinNotification($licenceDtl, $logaction, $user));
                }
            }         
        }

        $return["msg"] = trans('licenses.license_checkin.license_seat_checked_in_successfully');
        $return["status"] = "success";
        return response()->json($return);
    }

    public function ajaxUpdateSerialNo(Request $request, $id) {
    
        $return = [
            'status' => 'failure',
            'msg' => trans('licenses.license_seats_edit_serail.unable_to_edit_the_contract_agreement')
        ];

        $objSerial = "";
        try {
            $objSerial = LicenseSeat::findOrFail($id);
        }
        catch(\Exception $e) {
            return response()->json($return);        
        }

        $data = $request->only(['serial','expected_checkin']);
        if($request->filled('expected_checkin')){
            $data['expected_checkin'] = CommonHelper::getDateAs($request->expected_checkin,'Y-m-d','d/m/Y');
        }
        $rules = [
            'serial' => 'nullable|string|min:2|max:500',
            'expected_checkin' => 'nullable|date',
        ];
        $messages = [];
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
        }

        $objSerial->timestamps = false;
        $objSerial->fill($data);
        if ($objSerial->save()) {
            $return["msg"] = trans('licenses.license_seats_edit_serail.serial_number_has_updated_successfully');
            $return["status"] = "success";
        }

        return response()->json($return);
    }
    
}

<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Device;
use App\Models\Actionlog;
use App\Models\License;
use App\Models\LicenseSeat;
use App\Models\Settings;
use App\Models\Supplier;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Manufacture;
use App\Models\Purchase;
use App\Models\Depreciation;
use Illuminate\Http\Request;
use DB;
use Auth;
use Validator;
use DateTime;
use App\Helpers\Common as CommonHelper;

class LicenseController extends Controller
{

    public function listOptions() {

        $return = ["status" => "fail", "msg" => "Unable to get the options"];

        $companies = Company::select('id', 'name as text')->orderBy('name')->get();
        $manufacturers = Manufacture::select('id', 'name as text')->orderBy('name')->get();
        $suppliers = Supplier::select('id', 'name as text')->orderBy('name')->get();
        $depreciations = Depreciation::select('id', 'name as text')->orderBy('name')->get();
        $currencies = Currency::getCurrencies();

        $return['status'] = "success";
        $return['msg'] = '';
        $return['companies'] = $companies;
        $return['manufacturers'] = $manufacturers;
        $return['suppliers'] = $suppliers;
        $return['depreciations'] = $depreciations;
        $return['currencies'] = $currencies;
        return response()->json($return);
    }

    public function ajaxIndex(Request $request) {

        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:0',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];
        $messages = [        ];

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

        $fields = array(
            '1' => 'a.name',
            '2' => 'lu1.tot_seats',
            '3' => 'remaining',
            '4' => 'a.expiration_date', 
            '5' => 'a.purchase_date', 
            '6' => 'a.purchase_cost',
            '7' => 'a.order_number',
            '8' => 'a.updated_at'
        );
        $order = [
            0=>'asc',
            1=>'desc'
        ];

        $db = DB::table('licenses as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin(DB::raw('(SELECT license_id, count(id) as tot_seats FROM `license_seats` where deleted_at is null group by license_id) as lu1'), function($j) {
            $j->on('a.id', '=', 'lu1.license_id');
        });
        $db->leftJoin(DB::raw('(SELECT license_id, count(id) as used_seats FROM `license_seats` where (assigned_to is not null or asset_id is not null) and deleted_at is null group by license_id) as lu2'), function($j) {
            $j->on('a.id', '=', 'lu2.license_id');
        });

        $db->select('a.id', 'a.name','a.currency', 'cmp.name as cmp_name', 'a.order_number', 'lu1.tot_seats', 'lu2.used_seats', 'a.agreement_no');
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('case when dayname(a.expiration_date) is not null then DATE_FORMAT(a.expiration_date, "%d %b %Y") else "" end as expire_date_on'));
        $db->addSelect(DB::raw('case when dayname(a.expiration_date) is not null and a.expiration_date < curdate() then "Expired" else "" end as is_expired'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));
        $db->addSelect(DB::raw('case when a.purchase_cost is not null then FORMAT(a.purchase_cost, 2) else 0 end as purchase_cost_format'));
        $db->addSelect(DB::raw('a.serial as serial_value'));
        $db->addSelect(DB::raw('case when lu2.used_seats is not null then (lu1.tot_seats - lu2.used_seats) else lu1.tot_seats end as remaining'));
        
        if( isset($req["showDeletedLicenses"]) && $req["showDeletedLicenses"] == "true" ) {
            $db->whereNotNull('a.deleted_at');
        }
        else {
            $db->whereNull('a.deleted_at');
        }

        if(!Auth::user()->isSuperUser()){
            if(Settings::first()->full_multiple_companies_support != 1)
                $db->where("a.company_id", "=", Auth::user()->company_id);
        }


        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(a.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or cmp.name like "%%%1$s%%" or a.agreement_no like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or lu1.tot_seats like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or (case when lu2.used_seats is not null then (lu1.tot_seats - lu2.used_seats) else lu1.tot_seats end) like "%%%1$s%%" or DATE_FORMAT(a.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null then DATE_FORMAT(a.expiration_date, "%%d %%b %%Y") else "" end) like "%%%1$s%%" or (case when dayname(a.expiration_date) is not null and a.expiration_date < curdate() then "Expired" else "" end) like "%%%1$s%%")', $search_key);
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

    public function licensesExport(Request $request) {
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            '1' => 'a.name',
            '2' => 'serial_value',
            '3' => 'lu1.tot_seats',
            '4' => 'remaining',
            '5' => 'a.purchase_date', 
            '6' => 'a.purchase_cost',
            '7' => 'a.order_number'
        );
        
        $db = DB::table('licenses as a');
        $db->leftJoin('companies as cmp', 'cmp.id', '=', 'a.company_id');
        $db->leftJoin(DB::raw('(SELECT license_id, count(id) as tot_seats FROM `license_seats` where deleted_at is null group by license_id) lu1'), function($j) {
            $j->on('a.id', '=', 'lu1.license_id');
        });
        $db->leftJoin(DB::raw('(SELECT license_id, count(id) as used_seats FROM `license_seats` where (assigned_to is not null or asset_id is not null) and deleted_at is null group by license_id) lu2'), function($j) {
            $j->on('a.id', '=', 'lu2.license_id');
        });

        $db->select('a.id', 'a.name', 'cmp.name as cmp_name', 'a.order_number', 'lu1.tot_seats', 'lu2.used_seats','a.support','a.license_email','a.supplier_id');
        // $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y %h:%i %p") as purchase_date_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y") as purchase_date_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(a.expiration_date, "%d %b %Y") as expiration_date_on'));
        $db->addSelect(DB::raw('case when a.purchase_cost is not null then FORMAT(a.purchase_cost, 2) else 0 end as purchase_cost_format'));
        $db->addSelect(DB::raw('a.serial as serial_value'));
        $db->addSelect(DB::raw('case when lu2.used_seats is not null then (lu1.tot_seats - lu2.used_seats) else lu1.tot_seats end as remaining'));
        
        if( isset($req["showDeletedLicenses"]) && $req["showDeletedLicenses"] == "true" ) {
            $db->whereNotNull('a.deleted_at');
        }
        else {
            $db->whereNull('a.deleted_at');
        }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
            $whereStr = sprintf('(a.name like "%%%1$s%%" or a.serial like "%%%1$s%%" or cmp.name like "%%%1$s%%" or lu1.tot_seats like "%%%1$s%%" or a.order_number like "%%%1$s%%" or DATE_FORMAT(a.purchase_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or a.purchase_cost like "%%%1$s%%" or (case when lu2.used_seats is not null then (lu1.tot_seats - lu2.used_seats) else lu1.tot_seats end) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if( isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"]) ) {
            $db->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
        }


        $data = $db->get();

        $header = ["Company","License","Serial","License Email","Seats","Remaining Seats","Purchase Date","Expiry Date","Purchase Cost","Order Number","Support","Supplier"];
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . 'Licenses ' .  date('d M Y') .'.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, $header);
		foreach($data as $key=>$val){
            // print_r($val);die;
            // $category = Device::find($val->category_id);
            
            $lineArray = [
                $val->cmp_name,$val->name,
                $val->serial_value,
                $val->license_email,
                $val->tot_seats,
                $val->remaining,
                $val->purchase_date_on,
                $val->expiration_date_on,
                $val->purchase_cost_format,
                $val->order_number,
                $val->support,
                optional(Supplier::find($val->supplier_id))->name
            ];
            fputcsv($output, $lineArray);
		}
    }

    public function addLicense(Request $request) {
        $return = ['status'=>'fail', 'msg'=>'Unable to add the license.'];
        $appSettings = Settings::first();

        if(! Auth::user()->company_id) {
            $return['msg'] = 'Please update your company info at profile data.';
            return response()->json($return);
        }

        $data = $request->only('company_id', 'name', 'serial', 'license_name', 'license_email', 'seats', 'reassignable', 'supplier_id', 'order_number', 'purchase_date', 'purchase_cost', 'currency', 'purchase_order', 'expiration_date', 'depreciation_id', 'maintained', 'termination_date', 'notes', 'support', 'manufacturer_id', 'agreement_no', 'invoice_id');

        $rules = [
            'company_id' => 'nullable|integer|exists:companies,id',
            'name' => 'required|string|min:2|max:255',
            'serial' => 'required|string|min:2|max:255',
            'license_name' => 'nullable|string|max:100',
            'license_email' => 'nullable|email|max:120',
            'seats' => 'required|integer|min:1',
            'reassignable' => 'boolean',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_number' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date_format:d-m-Y',
            'purchase_cost' => 'nullable|numeric',
            'currency' => 'nullable',
            'purchase_order' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date_format:d-m-Y|after:purchase_date',
            'depreciation_id' => 'nullable|sometimes|exists:depreciations,id',
            'maintained' => 'boolean',
            'termination_date' => 'nullable|date_format:d-m-Y',
            'notes' => 'nullable|string|max:2000',
            'support' => 'nullable|string|max:500',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'agreement_no' => 'nullable|string|max:255',
            "invoice_id" => "nullable|integer|exists:purchases,id"
        ];

        $validator = Validator::make($request->all(), $rules, []);

        if( $validator->fails() ) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        if(! Auth::user()->isSuperUser() && $request->company_id && Auth::user()->company_id != $request->company_id ) {
            $return["msg"] = "Insufficient permission for this license";
            return response()->json($return);
        }

        $data['company_id'] = $request->company_id ? $request->company_id : Auth::user()->company_id;

        $objLic = new License;
        $objLic->fill($data);
        $objLic->maintained = $request->maintained ? 1 : 0;
        $objLic->reassignable = isset($data['reassignable']) ? 1 : 0;
        $objLic->invoice_id = $request->invoice_id == "" ? null : $request->invoice_id;
        $objLic->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d-m-Y") : null;
        $objLic->expiration_date = $request->expiration_date ? CommonHelper::getDateAs($request->expiration_date, "Y-m-d", "d-m-Y") : null;
        $objLic->termination_date = $request->termination_date ? CommonHelper::getDateAs($request->termination_date, "Y-m-d", "d-m-Y") : null;
        $objLic->user_id = Auth::user()->id;

        $objLic->currency = $request->currency ? $request->currency : $appSettings->default_currency;

        if($objLic->save()) {
            $seats = [];
            $seat_data = [];
            $seat_data['created_at'] = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
            $seat_data['updated_at'] = $seat_data['created_at'];
            $seat_data['license_id'] = $objLic->id;
            $seat_data['user_id'] = Auth::user()->id;

            for($i = 1; $i <= $objLic->seats; $i++) {
                $seats[] = $seat_data;
            }

            $seat_coll = collect($seats);
            $seat_coll->chunk(500)->each(function($item) {
                LicenseSeat::insert($item->toArray());
            });

            $logaction = new Actionlog();
            $logaction->asset_id = $objLic->id;
            $logaction->asset_type = 'software';
            $logaction->user_id = Auth::user()->id;
            $logaction->note = $request->seats ." seats";
            $logaction->action_type = 'add seats';
            $logaction->save();
        }

        $return["msg"] = "License added successfully";
        $return["id"] = $objLic->id;
        $return["status"] = "success";
        return response()->json($return);
    }

    public function showedit($id, $action="") {
        $return = ['status'=>'fail', 'msg'=>'Unable to get the record.'];
        $appSettings = Settings::first();
        $record = License::find($id);
        $record->purchase_date = CommonHelper::getDateAs($record->purchase_date, "d-m-Y", "Y-m-d");
        $record->expiration_date = CommonHelper::getDateAs($record->expiration_date, "d-m-Y", "Y-m-d");
        $record->termination_date = CommonHelper::getDateAs($record->termination_date, "d-m-Y", "Y-m-d");
        $record->purchase_cost = number_format($record->purchase_cost, 2 , '.' ,'' );

        if( !Auth::user()->isSuperUser() && Auth::user()->company_id != $record->company_id ) {
            $return["msg"] = Auth::user()->company_id == null ? "Please update your company name on your profile then try again." : "Multiple Company access is not enabled. You can update item for your company only.";
            return response()->json($return);
        }

        $return["data"] = $record->toArray();
        $return["dropdown"] = [];
        if($record->invoice_id) {
            $getInvoice = Purchase::where("id", "=", $record->invoice_id)->select("id", DB::raw('concat_ws(" - ", invoice_no, date_format(invoice_date, "%d/%m/%Y")) as text'))->first();
            $return["dropdown"]["invoice"] = $getInvoice ? $getInvoice->toArray() : null; 
        }

        if($action == "clone") {
            unset($return["data"]["id"]);
        }
        $return['msg'] = '';
        $return['status'] = 'success';
        return response()->json($return);
    }

    public function licenseDetail($id)
    {
        $return = [];
        $appSettings = Settings::first();
        $record = License::where('id','=',$id)->with('licenseseats')->first();

        $record->purchase_date = CommonHelper::getDateAs($record->purchase_date, "d-m-Y", "Y-m-d");
        $record->expiration_date = CommonHelper::getDateAs($record->expiration_date, "d-m-Y", "Y-m-d");
        $record->termination_date = CommonHelper::getDateAs($record->termination_date, "d-m-Y", "Y-m-d");


        $record->purchase_cost = number_format ( $record->purchase_cost, 2 , '.' ,'' );

        if( !Auth::user()->isSuperUser() && Auth::user()->company_id != $record->company_id ) {
            $return["status"] = 'error';
            $return["section"] = 'license-update';
            $return["msg"] = Auth::user()->company_id == null ? "Please update your company name on your profile then try again." : "Multiple Company access is not enabled. You can update item for your company only.";
            return response()->json($return);
        }

        // if($appSettings->full_multiple_companies_support){// check Licence company and user company is same 
        //     if($record->company_id == Auth::user()->company_id){
        //         return $record;
        //     }else{
        //         return response()->json(['status' => 'error', 'msg' => 'Insufficient Permission for this Licence !!']);
        //     }
        // }else{
            
            // return $record;
            $return["status"] = 'success';
            // $return["section"] = 'license-update';
            $return["msg"] = "";
            $return["data"] = $record;
            return response()->json($return);
        // }
    }

    public function update(Request $request, $id) {
        $return = ['status'=>'fail', 'msg'=>'Unable to get the record.'];
        $appSettings = Settings::first();

        if(! Auth::user()->company_id) {
            $return['msg'] = 'You are not allowed for this process !! Please update your company ID and try again !!';
            return response()->json($return);       
        }

        try {
            $objLicense = License::findOrFail($id);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }

        if(! Auth::user()->isSuperUser() && Auth::user()->company_id != $objLicense->company_id ) {
            $return["msg"] = Auth::user()->company_id == null ? "Please update your company name on your profile then try again." : "Multiple Company access is not enabled. You can update item for your company only.";
            return response()->json($return);
        }

        $data = $request->only('company_id', 'name', 'serial', 'license_name', 'license_email', 'seats', 'reassignable', 'supplier_id', 'order_number', 'purchase_date', 'purchase_cost', 'currency', 'purchase_order', 'expiration_date', 'depreciation_id', 'maintained', 'termination_date', 'notes', 'support', 'manufacturer_id', 'agreement_no', 'invoice_id');

        $rules = [
            'company_id' => 'nullable|integer|exists:companies,id',
            'name' => 'required|string|min:2|max:255',
            'serial' => 'required|string|min:2|max:255',
            'license_name' => 'nullable|string|max:100',
            'license_email' => 'nullable|email|max:120',
            'seats' => 'required|integer|min:1',
            'reassignable' => 'boolean',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_number' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date_format:d-m-Y',
            'purchase_cost' => 'nullable|numeric',
            'currency' => 'nullable',
            'purchase_order' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date_format:d-m-Y|after:purchase_date',
            'depreciation_id' => 'nullable|sometimes|exists:depreciations,id',
            'maintained' => 'boolean',
            'termination_date' => 'nullable|date_format:d-m-Y',
            'notes' => 'nullable|string|max:2000',
            'support' => 'nullable|string|max:500',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'agreement_no' => 'nullable|string|max:255',
            "invoice_id" => "nullable|integer|exists:purchases,id"
        ];
        
        $validator = Validator::make($request->all(), $rules, []);
        if( $validator->fails() ) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }
        
        if(! Auth::user()->isSuperUser() && Auth::user()->company_id != $request->company_id ) {
            $return["msg"] = "Insufficient permission for this license";
            return response()->json($return);
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
        if( $request->seats < $objLicense->seats ) 
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

                    $return['msg'] = $allotedSeats.' seats have alloted for this license already!! System is unable to decrease seat';
                    return response()->json($return);
                }

                if( $seatsToDelete <= $freeSeats ) {
                    $canDeleteSeats = true;
                }
            }
        }

        $objLicense->fill($data);
        $objLicense->maintained = isset($data['maintained']) && $data['maintained'] ? 1 : 0;
        $objLicense->reassignable = isset($data['reassignable']) && $data['reassignable'] ? 1 : 0;
        $objLicense->invoice_id = $request->invoice_id == "" ? null : $request->invoice_id;
        $objLicense->purchase_date = $request->purchase_date ? CommonHelper::getDateAs($request->purchase_date, "Y-m-d", "d-m-Y") : null;
        $objLicense->expiration_date = $request->expiration_date ? CommonHelper::getDateAs($request->expiration_date, "Y-m-d", "d-m-Y") : null;
        $objLicense->termination_date = $request->termination_date ? CommonHelper::getDateAs($request->termination_date, "Y-m-d", "d-m-Y") : null;
        $objLicense->user_id = Auth::user()->id;

        $objLicense->currency = $request->currency ? $request->currency : $appSettings->default_currency;
        $objLicense->company_id = $request->company_id ? $request->company_id : Auth::user()->company_id;

        if($objLicense->save())
        {
            /* Seat Deletions */
            if($canDeleteSeats && $seatsToDelete) {
                $free_seats_coll = $freeSeatsRes->get();
                $free_seat_ids = [];

                foreach($free_seats_coll as $f) {
                    $free_seat_ids[] = $f->id;
                    if(count($free_seat_ids) == $seatsToDelete) {
                        break;
                    }
                }

                $deleted_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                LicenseSeat::whereIn('id', $free_seat_ids)->update([
                    'deleted_at' => $deleted_at
                ]);

                $note_text = (count($free_seat_ids)) . ' seat(s) are removed.' ;
                $logaction = new Actionlog();
                $logaction->asset_id = $id;
                $logaction->asset_type = 'software';
                $logaction->user_id = Auth::user()->id;
                $logaction->note = $note_text;
                $logaction->checkedout_to =  NULL;
                $logaction->action_type = 'delete seats';
                $logaction->save();
            }
            elseif($newSeats) 
            {
                $seats = [];
                $seat_data = [];
                $seat_data['created_at'] = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
                $seat_data['updated_at'] = $seat_data['created_at'];
                $seat_data['license_id'] = $objLicense->id;
                $seat_data['user_id'] = Auth::user()->id;

                for($i = 1; $i <= $newSeats; $i++) {
                    $seats[] = $seat_data;
                }

                $seat_coll = collect($seats);
                $seat_coll->chunk(500)->each(function($item) {
                    LicenseSeat::insert($item->toArray());
                });

                $logaction = new Actionlog();
                $logaction->asset_id = $id;
                $logaction->asset_type = 'software';
                $logaction->user_id = Auth::user()->id;
                $logaction->note = $newSeats." seats are added";
                $logaction->action_type = 'add seats';
                $logaction->save();
            }

            $return["msg"] = "License has updated successfully";
            $return["status"] = "success";
        }

        return response()->json($return);
    }

    public function checkout(Request $request, $license_id) {
        $return = ['status' => 'fail', 'msg' => 'Unable to checkout the License'];
        $appSettings = Settings::first();

        if(! Auth::user()->company_id) {
            $return['msg'] = 'You are not allowed for this process!! Please update your company ID and try again !!';
            return response()->json($return);       
        }

        try {
            $licenceDtl = License::findOrFail($license_id);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }

        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($licenceDtl) ) {
            $return["msg"] = "You can access item for your company only.";
            return response()->json($return);
        }

        $rules = array(
            'assigned_to' => 'nullable',
            'checkoutnotes' => 'nullable|string|max:2000',
            'asset_id' => 'required_without:assigned_to',
            'expected_checkin' => 'nullable|date_format:d-m-Y'
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

        //check if licence seat availavle 
        $licenceSeat = LicenseSeat::where('license_id','=',$license_id)->whereNull('deleted_at')->whereNull('assigned_to')->whereNull('asset_id')->first();

        if(empty($licenceSeat)) {
            $return["msg"] = 'Licence seat not available';
            return response()->json($return);
        }

        if($request->assigned_to){
            $user = User::find($request->assigned_to);
            if(empty($user)) {
                $return["msg"] = 'User is not available for checkout License';
                return response()->json($return);
            }
        }

        if ($request->asset_id ) {
            $device = Device::find($request->asset_id);
            if (empty($device)) {
                $return["msg"] = 'Device is not available for checkout License';
                return response()->json($return);
            }

            if($device->assigned_to != $request->assigned_to && $request->assigned_to != '')  {
                $return["msg"] = 'Device\'s User does not match';
                return response()->json($return);
            }
        }

        $licenceSeat->asset_id = $request->asset_id;
        $licenceSeat->assigned_to = $request->assigned_to;
        $licenceSeat->expected_checkin = $request->expected_checkin ? CommonHelper::getDateAs($request->expected_checkin, "Y-m-d", "d-m-Y") : null;

        if($licenceSeat->save()) {
            $logaction = new Actionlog();
            $logaction->asset_type = 'software';
            $logaction->user_id = Auth::user()->id;
            $logaction->note = $request->checkoutnotes;
            $logaction->asset_id = $license_id;
            $logaction->checkedout_to = $request->assigned_to;
            $logaction->action_type = 'checkout';
            $logaction->save();
            
            $return["status"] = "success";
            $return["msg"] = "License checked out successfully";
            if( $request->assigned_to ){
                $return["msg"].= ' to '.$user->first_name.' '.$user->last_name ;
            }
            if( $request->asset_id ){
                $return["msg"].= ' with '.$device->name . ' ('.$device->asset_tag.')' ;
            }
        }

        return response()->json($return);
    }

    public function licenceDetail($license_id) {

        $objLicense = License::where('id','=',$license_id)->with(['company','supplier','licenseseats'])->first();

        if(empty($objLicense))
            return redirect('licenses')->withMsg(['status'=>'error','msg'=>'Requested license not found !!']);

        if( !Auth::user()->isSuperUser() && Auth::user()->company_id != $objLicense->company_id )
            return redirect('licenses')->withMsg(['status'=>'error','msg'=>'Insufficient permission for the license !!']);

        $vd = new stdClass;
        $vd->companies = Company::select('id', 'name as text')->orderBy('name')->get();
        $vd->manufacturers = Manufacture::select('id', 'name as text')->orderBy('name')->get();
        $vd->suppliers = Supplier::select('id', 'name as text')->orderBy('name')->get();
        $vd->depreciations = Depreciation::select('id', 'name as text')->orderBy('name')->get();
        $vd->currencies = Currency::getCurrencies();

        return view("licenses.detail-view")->with('licence',$objLicense)->with('vd', $vd);
    }

    public function deleteLicense($id) {
        $return = ['status' => 'fail', 'msg' => 'Unable to delete the License'];
        $appSettings = Settings::first();

        if(! Auth::user()->company_id) {
            $return['msg'] = 'You are not allowed for this process !! Please update your company ID and try again !!';
            return response()->json($return);       
        }

        try {
            $licenceDtl = License::findOrFail($id);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }

        if( !Auth::user()->isSuperUser() && Auth::user()->company_id != $licenceDtl->company_id ) {
            $return["msg"] = Auth::user()->company_id == null ? "Please update your company name on your profile then try again." : "Multiple Company access is not enabled. You can access item for your company only.";
            return response()->json($return);
        }

        $allotedSeats = LicenseSeat::where('license_id','=',$id)
                    ->whereNull('deleted_at')
                    ->where(function($q) {
                        $q->whereRaw('(assigned_to is not null or asset_id is not null)');
                    })
                    ->count();

        if($allotedSeats)
        {
            $return['msg'] = 'This Licence is alloted to '.$allotedSeats.' seats !! System is unable to delete this license !!';
            return response()->json($return);
        }

        if($licenceDtl->delete()) {
            LicenseSeat::where('license_id','=',$id)->delete();
            $return['msg'] = 'License has been deleted';
            $return['status'] = 'success';
        }

        return response()->json($return);
    }

    public function deleteLicenseSeat($id) {

        $licenceSeatDtl = LicenseSeat::where('id','=',$id)->first();
        if(empty($licenceSeatDtl))
             return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);
        $licenceDtl = License::where('id','=',$licenceSeatDtl->license_id)->first();

        if(empty($licenceDtl))
             return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);

        if( !Auth::user()->isSuperUser() && Auth::user()->company_id != $licenceDtl->company_id ) {
            $return["status"] = 'error';
            $return["section"] = 'licenseseat-delete';
            $return["msg"] = Auth::user()->company_id == null ? "Please update your company name on your profile then try again." : "Multiple Company access is not enabled. You can access item for your company only.";
            return response()->json($return);
        }

        $allotedSeats = LicenseSeat::where('id','=',$id)
                    ->whereNull('deleted_at')
                    ->where(function($q) {
                        $q->whereRaw('(assigned_to is not null or asset_id is not null)');
                    })
                    ->count();

        if($allotedSeats)
        {
            return response()->json(['status' => 'error','section'=>'licenseseat-delete', 'msg' => 'This Licence seat is alloted  !! System is unable to delete this license seat !!'.$allotedSeats]);
        }
        // LicenseSeat::where('id','=',$id)->delete();

        $licenceSeatDtl->delete();
        $licenceDtl->seats = $licenceDtl->seats - 1 ;
        $licenceDtl->save();
            return response()->json(['status' => 'success', 'msg' => 'License seat has deleted successfully!']);
    }

    public function checkinLicenseSeat(Request $request, $seatid) {

        $return = ['status'=>'fail', 'msg'=>'Unable to checkin the License'];

        $appSettings = Settings::first();

        try {
            $seatDtl = LicenseSeat::findOrFail($seatid);
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
            $return['msg'] = 'Insufficient Permission. Please update your company name on your profile.';
            return response()->json($return);
        }

        if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($licenceDtl) ) {
            $return["msg"] = "Multiple Company access is not enabled. You can access item for your company only.";
            return response()->json($return);
        }

        if(! $licenceDtl->reassignable){
            $return['msg'] = 'This license is not reassignable. So Check In is not possible for this License';
            return response()->json($return);
        }

        $logaction = new Actionlog();
        $logaction->checkedout_to = $seatDtl->assigned_to;

        // Update the asset data
        $seatDtl->assigned_to = NULL;
        $seatDtl->asset_id = NULL;

        if($seatDtl->save()) {
            $logaction->asset_id = $seatDtl->license_id;
            $logaction->location_id = NULL;
            $logaction->asset_type = 'software';
            $logaction->note = $request->checkinnotes;
            $logaction->user_id = Auth::user()->id;
            $logaction->action_type = 'checkin from';
            $logaction->save();
        }

        $return["msg"] = "License Seat checked in successfully";
        $return["status"] = "success";
        return response()->json($return);
    }

    public function ajaxlicenseSeat($licenceId,Request $request) {
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:0',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];
        $messages = [        ];

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

        $fields = array(
            '1' => 'ls.id',
            '2' => 'endUserName',
            '3' => 'asset_tag',
            '4' => 'expected_checkin'
        );

        $order = [
            0=>'asc',
            1=>'desc'
        ];

        $db = DB::table('license_seats as ls');
        $db->leftJoin('licenses as  l', 'ls.license_id', '=', 'l.id');
        $db->leftJoin('users as enduser', 'enduser.id', '=', 'ls.assigned_to');
        $db->leftJoin('assets as device', 'device.id', '=', 'ls.asset_id');

        $db->select('ls.id', 'ls.notes','ls.serial','device.id as deviceid','device.asset_tag as asset_tag','enduser.id as enduserid');
        $db->addSelect(DB::raw('concat(enduser.first_name, " ", enduser.last_name) as endUserName'));
        $db->addSelect(DB::raw('DATE_FORMAT(ls.expected_checkin, "%d-%b-%Y") as expected_checkin_format'));
        $db->where('ls.license_id', '=', $licenceId);
        
        // if( isset($req["showDeletedLicenses"]) && $req["showDeletedLicenses"] == "true" ) {
        //     $db->whereNotNull('a.deleted_at');
        // }
        // else {
            $db->whereNull('ls.deleted_at');
        // }

        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%" or ls.notes like "%%%1$s%%" or device.asset_tag like "%%%1$s%%" or ls.serial like "%%%1$s%%" )', $search_key);
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

        foreach($data as $key => $value ) {
                $value->seatcount = 'Seat '.($key+1).'( #'.$value->id.' ) '.( empty($value->serial) ? '' : 'Serial: '.$value->serial);
                $return['data'][] = array( 'a' => $value );
        }
        return response()->json($return);
    }

    public function ajaxlicensehistory($licenceId,Request $request) {
        $rules = [
            'index'         => 'sometimes|integer|max:100',
            'search_key'    => 'sometimes|string|max:100',
            'list_size'     => 'sometimes|integer|min:1|max:20',
            'order_by'      => 'sometimes|integer|min:0',
            'order_dir'     => 'sometimes|integer|min:0|max:1'
        ];
        $messages = [        ];

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

        $fields = array(
            '0' => 'al.created_at',
            '1' => 'adminName',
            '2' => 'al.action_type',
            '3' => 'endUserName',
            '4' => 'al.note',
            // '5' => 'a.purchase_date', 
            // '6' => 'a.purchase_cost',
            // '7' => 'a.order_number'
        );

        $order = [
            0=>'asc',
            1=>'desc'
        ];
        
        $db = DB::table('asset_logs as al');
        $db->leftJoin('users as admin', 'admin.id', '=', 'al.user_id');
        $db->leftJoin('users as enduser', 'enduser.id', '=', 'al.checkedout_to');
        

        $db->select('al.id', 'al.created_at as created_at', 'al.action_type', 'al.note' , 'enduser.id as enduserid','admin.id as adminuserid');
        // $db->addSelect(DB::raw('DATE_FORMAT(a.purchase_date, "%d %b %Y %h:%i %p") as purchase_date_on'));
        $db->addSelect(DB::raw('concat(admin.first_name, " ", admin.last_name) as adminName'));
        $db->addSelect(DB::raw('concat(enduser.first_name, " ", enduser.last_name) as endUserName'));
        $db->addSelect(DB::raw('DATE_FORMAT(al.created_at, "%d %b %Y %h:%i %p") as created_at'));
        $db->where('al.asset_type', '=', 'software');
        $db->where('al.asset_id', '=', $licenceId);
        // $db->addSelect(DB::raw('case when a.purchase_cost is not null then FORMAT(a.purchase_cost, 2) else 0 end as purchase_cost_format'));
        // $db->addSelect(DB::raw('a.serial as serial_value'));
        // $db->addSelect(DB::raw('case when lu2.used_seats is not null then (lu1.tot_seats - lu2.used_seats) else lu1.tot_seats end as remaining'));
        
        // if( isset($req["showDeletedLicenses"]) && $req["showDeletedLicenses"] == "true" ) {
        //     $db->whereNotNull('a.deleted_at');
        // }
        // else {
        //     $db->whereNull('a.deleted_at');
        // }

        $return['status'] = "success";
        $return['msg'] = "";
        $return['tot'] = $db->count();
        $return['filter_record'] = $return['tot'];

        if( isset($req["search_key"]) && $search_key = trim($req["search_key"]) ) {
            $whereStr = sprintf('(al.created_at like "%%%1$s%%" or al.action_type like "%%%1$s%%" or al.note like "%%%1$s%%" or concat(admin.first_name, " ", admin.last_name) like "%%%1$s%%" or concat(enduser.first_name, " ", enduser.last_name) like "%%%1$s%%")', $search_key);
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
            
                $return['data'][] = array('a' => $d);
            }

            
            return response()->json($return);
    }

    public function checkoutSeat($seat_id,Request $request) {

        $return = ['status'=>'fail','msg'=>'Unable to check out the License.'];

        $appSettings = Settings::first();
        $licenceSeat = LicenseSeat::where('id','=',$seat_id)->whereNull('deleted_at')->whereNull('assigned_to')->whereNull('asset_id')->first();

        if(empty($licenceSeat)) {
            $return['msg'] = 'Seat not available';
            return response()->json($return);
        }

        $licenceDtl = License::where('id','=',$licenceSeat->license_id)->first();
            
        if(empty($licenceDtl)) {
            $return['msg'] = 'Licence Seat not available';
            return response()->json($return);
        }
        
        if($licenceDtl->company_id != Auth::user()->company_id) {
            $return['msg'] = 'Insufficient Permission for this Licence';
            return response()->json($return);
        }

        $rules = array(
            'assigned_to'     => 'nullable',
            'checkoutnotes'   => 'nullable|string|max:2000',
            'asset_id'        => 'required_without:assigned_to',
            'expected_checkin' => 'nullable|date_format:d-m-Y'
        );
        $messages = [
            'asset_id.required_without'=>'Please select device to checkout license',
        ];
        $validator = Validator::make($request->all(), $rules,$messages);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        } 

        //check if licence seat availavle 
        if( $request->assigned_to ){
            $user = User::find($request->assigned_to);
            if(empty($user)) {
                $return["msg"] = 'User not available !!';
                return response()->json($return);
            }
        }

        if( $request->asset_id ) {
            $device = Device::find($request->asset_id);
            if (empty($device)) {
                $return["msg"] = 'Device not available !!';
                return response()->json($return);
            }

            if($device->assigned_to != $request->assigned_to && $request->assigned_to !='')  {
                $return["msg"] = 'Device Owner not available !!';
                return response()->json($return);
                return response()->json(['status' => 'error','section'=>'license-checkout', 'msg' => 'Owner does not match !!']);
            }
        }

        $licenceSeat->asset_id = $request->asset_id;
        $licenceSeat->assigned_to = $request->assigned_to;
        $licenceSeat->expected_checkin = $request->expected_checkin ? CommonHelper::getDateAs($request->expected_checkin, "Y-m-d", "d-m-Y") : null;

        if($licenceSeat->save()) {
            $logaction = new Actionlog();
            $logaction->asset_type = 'software';
            $logaction->user_id = Auth::user()->id;
            $logaction->note = $request->checkoutnotes ;
            $logaction->asset_id = $licenceSeat->license_id;
            $logaction->checkedout_to = $request->assigned_to;
            $logaction->action_type = 'checkout';
            $logaction->save();
        }

        $return["msg"] = "License checked out successfully";
        $return["section"] = "licenseSeatCheckOut";
        if( $request->assigned_to ){
            $return["msg"].= ' to '.$user->first_name.' '.$user->last_name ;
        }
        if( $request->asset_id ){
            $return["msg"].= ' with '.$device->name . ' ('.$device->asset_tag.')' ;
        }
        $return["status"] = "success";
        return response()->json($return);
    }

    // public function redirectForPopUP($seat_id) {
    //     $licenceSeat = LicenseSeat::where('id','=',$seat_id)->first();
    //     return redirect('license/detail/'.$licenceSeat->license_id.'?popup=checkin&seatid='.$seat_id);
    // }

    // public function infosummery($license_id) {
    //     $license = License::where('id','=',$license_id)->first();
    //     if(empty($license))
    //         return redirect('consumables');
    //     return view("licenses.basic-info")
    //     ->with('licence',$license);
    // }

    public function jxLicenseList(Request $request) {
        $return = [];
        $req = $request->all();

        $skip = 0;
        $take = 10;

        $page = $request->page ? $request->page : 1;
        $take = $request->size ? $request->size : 20;
        $skip = ($page-1) * $take;


        $fields = array(
            '1' => 'temp.licenseName',
            '2' => 'temp.seatSerial',
            '3' => 'temp.asset_tag',
            '4' => 'temp.user_full_name',
            '5' => 'temp.expiration_date',
            '6' => 'temp.is_expired',
        );
        $order = [
            1=>'asc',
            2=>'desc'
        ];

        $rawQuery = "SELECT temp.* from ( SELECT lic.name AS licenseName,ls.serial as seatSerial,dev.id as dev_id,dev.asset_tag ,
        DATE_FORMAT(lic.expiration_date, '%d %b %Y') as expire_date_format,
        IF(CURRENT_TIMESTAMP > lic.expiration_date,'Yes','No') as is_expired,
        concat(u.first_name, ' ', u.last_name) as user_full_name,u.id as assigned_user_id,dept.id as dept_id
        
        FROM license_seats as ls 
        LEFT JOIN licenses AS lic ON lic.id = ls.license_id 
        LEFT JOIN assets as dev ON dev.id = ls.asset_id
        LEFT JOIN users AS u ON u.id = ls.assigned_to
        LEFT JOIN departments as dept ON dept.id = u.department_id  ) as temp";

        $return['total'] = count(DB::select($rawQuery));
        $return['filtered'] = $return['total'];

        $rawQuery .= " WHERE 1=1 ";
        if( isset($req['assigned_to_dept']) && $assigned_to_dept = trim($req['assigned_to_dept']) ){
            $rawQuery.= ' AND temp.dept_id = '.$assigned_to_dept;
        }

        if( isset($req["search"]) && $search_key = trim($req["search"]) ) {
            $rawQuery.= " AND temp.licenseName LIKE '%$search_key%' OR temp.seatSerial LIKE '%$search_key%' OR temp.asset_tag LIKE '%$search_key%' OR temp.user_full_name LIKE '%$search_key%' OR temp.is_expired LIKE '%$search_key%' OR temp.expire_date_format LIKE '%$search_key%' ";
            
            $return['filtered'] = count(DB::select($rawQuery));
            $return['search_key'] = $search_key;
        }
        if( isset($req['order']['id']) && array_key_exists($req['order']['id'], $fields) ) {
            $rawQuery.= " ORDER BY ".$fields[$req['order']['id']]." ".$order[$request->input('order.dir',1)];
        }
        // echo $rawQuery;die;
        $return['page'] = (int) $page;

        $rawQuery.= " limit $take OFFSET $skip";

        $data = DB::select($rawQuery);
        $return['data'] = $data;
        // foreach($data as $d) {
        //     $return['data'][] = ['name'=>$d->cat_name,'cat_id'=>$d->cat_id,'tot_count'=>$d->tot_count];
        // }
        return response()->json($return);
    }
}

<?php

namespace App\Http\Controllers\Config;

use App\Helpers\Common as CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\AssetExpense;
use App\Models\Category;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Device;
use App\Models\DeviceMaintenance;
use App\Models\Lease;
use App\Models\LeaseType;
use App\Models\Model;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Auth;
use DB;
use File;
use Image;
use stdClass;
use Storage;
use Validator;

class LeaseController extends Controller
{
    /* list of lease agreements */
    public function index()
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('ContractAgreementRead')) {
            return redirect('dashboard')->with('msg', $return);
        }
        /* view data */
        $vd = new stdClass();
        $currencies = Currency::getCurrencies();
        $vd->companies = Company::select('id', 'name as text')->orderBy('name')->get();
        $vd->lease_type = LeaseType::select('id', 'name as text')->orderBy('name')->get();

        return view('config.lease_agreements.index')->with('vd', $vd)->with(compact('currencies'));
    }

    /* ajax support for list of lease agreements pgae */
    public function ajaxIndex(Request $request)
    {
        $req = $request->all();
        $return = array(
            'draw' => date('is')
        );

        $fields = array(
            'a.id' => 'l.id',
            'a.contract_number' => 'l.contract_number',
            'a.leaser' => 's.name',
            'a.start_date_txt' => 'l.start_date',
            'a.end_date_txt' => 'l.end_date',
            'a.lease_type_name' => 'lt.name',
            'a.maintenance_incharge_name' => 'l.maintenance_incharge',
            'a.last_updated_at' => 'l.updated_at'
        );

        $db = DB::table('lease_agreements as l');
        $db->leftJoin('companies as c', 'c.id', '=', 'l.company_id');
        $db->leftJoin('suppliers as s', 's.id', '=', 'l.leaser');
        $db->leftJoin('lease_type as lt', 'lt.id', '=', 'l.lease_type');

        $db->select('l.id', 's.name as leaser', 'c.name as company', 'l.contract_number', 'l.attachment', 'lt.name as lease_type_name', 'l.description');
        $db->addSelect(DB::raw('case when l.maintenance_incharge = 1 then "By Company" when l.maintenance_incharge = 2 then "By contractor" else "" end as maintenance_incharge_name'));
        $db->addSelect(DB::raw('DATE_FORMAT(l.start_date, "%d %b %Y") as start_date_txt'));
        $db->addSelect(DB::raw('DATE_FORMAT(l.end_date, "%d %b %Y") as end_date_txt'));
        $db->addSelect(DB::raw('DATE_FORMAT(l.updated_at, "%d %b %Y %h:%i %p") as last_updated_at'));

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf(
                '(l.id like "%%%1$s%%" or l.contract_number like "%%%1$s%%" or s.name like "%%%1$s%%" or
                (case when l.lease_type = 2 then "Finance Contract" when l.lease_type = 1 then "Operating Contract" end) = "%1$s" or 
                (case when l.maintenance_incharge = 2 then "By Contractor" when l.maintenance_incharge = 1 then "By Company" end) = "%1$s" or 
                DATE_FORMAT(l.start_date, "%%d %%b %%Y") like "%%%1$s%%" or 
                DATE_FORMAT(l.end_date, "%%d %%b %%Y") like "%%%1$s%%" or 
                DATE_FORMAT(l.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")',
                $search_key
            );
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ['asc', 'desc'])) {
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

    /* to add the lease via ajax */
    public function ajaxAdd(Request $request)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to add the Contract Agreement'
        ];
        if (!Auth::user()->hasPermissionTo('ContractAgreementAdd')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        try {
            if ($request['category_id'] == 'null') {
                $request['category_id'] = null;
            }
            if ($request['model_id'] == 'null') {
                $request['model_id'] = null;
            }
            if ($request['device_id'] == 'null') {
                $request['device_id'] = null;
            }
            $data = $request->only('category_id', 'model_id', 'device_id', 'leaser', 'company_id', 'lease_type', 'maintenance_incharge', 'start_date', 'end_date', 'contract_number', 'description', 'attachment', 'currency_format', 'cost');
            $rules = [
                'leaser' => 'required|exists:suppliers,id',
                'company_id' => 'required|exists:companies,id',
                'lease_type' => 'required',
                'maintenance_incharge' => 'required',
                'start_date' => 'required|date_format:d/m/Y',
                'end_date' => 'required|date_format:d/m/Y|after:start_date',
                'contract_number' => 'nullable|alpha_space|max:100',
                'description' => 'nullable|string|max:2000',
                'attachment' =>'sometimes|mimes:jpeg,bmp,png,gif,jpg,pdf,doc,docx,xls,xlsx|max:10240',
                'category_id' => 'nullable|int|exists:categories,id',
                'model_id' => 'nullable|int|exists:models,id',
                'device_id' => 'sometimes',
                'status' => 'nullable|integer|min:0|max:1',
                'cost' => 'nullable|numeric|min:0|max:9999999.99',
            ];
            $messages = [
                'attachment.max' => trans('contract_agreement.contract_agreement_fields.attachment_max'),
                'attachement.mimes' => trans('contract_agreement.contract_agreement_fields.invalid_file_format')
            ];
            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return['msg'] = $e[0];
                return response()->json($return);
            }

            if (!empty($data['device_id'] && $data['device_id'] != 'null')) {
                $devices = explode(',', $data['device_id']);
            } else if (!empty($data['model_id'])) {
                $db = DB::table('assets as a');
                $db->leftJoin('status_labels as s', 'a.status_id', '=', 's.id');
                $db->select('a.id', 'a.model_id');
                $db->whereNull('a.deleted_at');
                $db->whereNull('s.sold');
                $db->whereNull('s.stolen_item');
                $db->where('a.model_id', '=', $data['model_id']);
                $devices = $db->get()->toArray();
            } else {
                $db = Model::whereNull('deleted_at')->where('category_id', '=', $data['category_id'])->pluck('id');

                $dbs = DB::table('assets as a');
                $dbs->leftJoin('status_labels as s', 'a.status_id', '=', 's.id');
                $dbs->select('a.id', 'a.model_id');
                $dbs->whereNull('a.deleted_at');
                $dbs->whereNull('s.sold');
                $dbs->whereNull('s.stolen_item');
                $dbs->whereIn('a.model_id', $db);
                $devices = $dbs->get();
            }

            $cal = count($devices) ? $request->cost / count($devices) : 0;
            $getSupplier = Supplier::where('id', $request->leaser)->select('id', 'name as text')->first();

            foreach ($devices as $d) {
                $assetExpense = new AssetExpense();
                $assetExpense->asset_id = isset($d->id) ? $d->id : $d;
                $assetExpense->title = $getSupplier->text . '_' . $request->contract_number;
                $assetExpense->model_id = $request->model_id;
                $assetExpense->category_id = $request->category_id;
                $assetExpense->cost = $cal;
                $assetExpense->is_warranty = 0;
                $assetExpense->expense_type = 5;
                $assetExpense->expense_date = Carbon::now();
                $assetExpense->save();
            }
            $objLease = new Lease;
            $objLease->fill($data);
            $objLease->cost = $request->cost;
            $objLease->status = $request->status;
            $objLease->currency_format = $request->currency_format;
            $objLease->start_date = $request->start_date ? CommonHelper::getDateAs($request->start_date, 'Y-m-d', 'd/m/Y') : null;
            $objLease->end_date = $request->end_date ? CommonHelper::getDateAs($request->end_date, 'Y-m-d', 'd/m/Y') : null;
            $objLease->created_by = Auth::user()->id;

            try {
                $carbon_start_date = new Carbon($objLease->start_date);
                $carbon_end_date = new Carbon($objLease->end_date);
                if ($carbon_start_date->gt($carbon_end_date)) {
                    $return['msg'] = 'Please give the valid contract dates';
                    return response()->json($return);
                }
            } catch (\Exception $e) {
                return response()->json($return);
            }

            if ($request->hasFile('attachment')) {
                $uploaded_file = $request->attachment;
                $filename = time() . '_' . $uploaded_file->getClientOriginalName();
                $extension = $uploaded_file->getClientOriginalExtension();
                $File = $uploaded_file->move('uploads/contract', $filename);
                $path = public_path('uploads/contract' . $uploaded_file);
                $objLease->save();
                $objLease->attachment = $filename;
            } else {
                $objLease->attachment = null;
            }
            if ($objLease->save()) {
                $return['msg'] = 'Contract Agreement has been added successfully';
                $return['status'] = 'success';
            }
        } catch (\Exception $e) {
            Log::error('error: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    /* get Lease in ajax call */
    public function ajaxGet(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to get the Contract Agreement'
        ];

        $objLease = '';
        try {
            $objLease = Lease::findOrFail($id);

            $data = [];
            $data['dropdown'] = array();
            $data['data'] = $objLease->only('id', 'company_id', 'lease_type', 'maintenance_incharge', 'contract_number', 'description', 'attachment', 'category_id', 'model_id', 'device_id', 'cost', 'currency_format', 'status');

            if ($objLease->leaser) {
                $getSupplier = Supplier::where('id', $objLease->leaser)->select('id', 'name as text')->first();
                $data['dropdown']['leaser'] = $getSupplier && $getSupplier->exists ? $getSupplier->toArray() : null;
            }
            $data['opts']['category'] = Category::select('id', 'name')->where('id', $objLease->category_id)->first();
            $data['opts']['model'] = Model::select('id', 'name')->where('id', $objLease->model_id)->first();
            $device = $objLease['device_id'];
            $devices = explode(',', $device);
            $deviceIds = [];
            foreach ($devices as $d) {
                $deviceCheck = Device::select('assets.id', DB::raw("concat_ws('-',assets.asset_tag,concat_ws(' ',assets.name)) as text"))
                    ->leftJoin('models as mdl', 'mdl.id', '=', 'assets.model_id')
                    ->where('assets.id', $d)
                    ->first();
                if (!empty($deviceCheck) && $deviceCheck->count() > 0) {
                    array_push($deviceIds, $deviceCheck);
                }
            }
            $data['opts']['device'] = $deviceIds;

            $data['data']['start_date'] = CommonHelper::getDateAs($objLease->start_date, 'd-m-Y', 'Y-m-d');
            $data['data']['end_date'] = CommonHelper::getDateAs($objLease->end_date, 'd-m-Y', 'Y-m-d');
            $return['data'] = $data;
            $return['msg'] = null;
            $return['status'] = 'success';
        } catch (\Exception $e) {
            return response()->json($return);
        }

        return response()->json($return);
    }

    /* to edit the lease by ajax call */
    public function ajaxEdit(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to edit the Contract Agreement'
        ];
        if (!Auth::user()->hasPermissionTo('ContractAgreementEdit')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $objLease = '';
        try {
            $objLease = Lease::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json($return);
        }

        if ($request['category_id'] == 'null') {
            $request['category_id'] = null;
        }
        if ($request['model_id'] == 'null') {
            $request['model_id'] = null;
        }
        if ($request['device_id'] == 'null') {
            $request['device_id'] = null;
        }

        $data = $request->only('leaser', 'company_id', 'lease_type', 'maintenance_incharge', 'start_date', 'end_date', 'contract_number', 'description', 'attachment', 'delete_img', 'category_id', 'model_id', 'device_id', 'cost', 'currency_format');
        $rules = [
            'leaser' => 'required|exists:suppliers,id',
            'company_id' => 'required|exists:companies,id',
            'lease_type' => 'required',
            'maintenance_incharge' => 'required',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after:start_date',
            'contract_number' => 'nullable|alpha_space|max:100',
            'description' => 'nullable|string|max:2000',
            'status' => 'nullable|integer|min:0|max:1',
            'attachment' =>'sometimes|mimes:jpeg,bmp,png,gif,jpg,pdf,doc,docx,xls,xlsx|max:10240',
            // 'delete_img' => 'sometimes|nullable|integer|min:0|max:1',
            'cost' => 'nullable|numeric|min:0|max:9999999.99',
            ];
        $messages = [
            'attachment.max' => trans('contract_agreement.contract_agreement_fields.attachment_max'),
            'attachement.mimes' => trans('contract_agreement.contract_agreement_fields.invalid_file_format')
        ];
        unset($data['delete_img']);
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }
        $newRecord = $objLease->replicate();
        $newRecord->setTable('lease_agreement_history');
        $newRecord->lease_id = $id;
        $newRecord->save();
        $objLease->fill($data);
        $objLease->start_date = $request->start_date ? CommonHelper::getDateAs($request->start_date, 'Y-m-d', 'd/m/Y') : null;
        $objLease->end_date = $request->end_date ? CommonHelper::getDateAs($request->end_date, 'Y-m-d', 'd/m/Y') : null;
        $objLease->user_id = Auth::user()->id;
        $objLease->updated_by = Auth::user()->id;
        $objLease->cost = $request->cost;
        $objLease->status = $request->status;
        $objLease->currency_format = $request->currency_format;

        try {
            $carbon_start_date = new Carbon($objLease->start_date);
            $carbon_end_date = new Carbon($objLease->end_date);
            if ($carbon_start_date->gt($carbon_end_date)) {
                $return['msg'] = 'Please give the valid contract dates';
                return response()->json($return);
            }
        } catch (\Exception $e) {
            return response()->json($return);
        }
        if ($request->hasFile('attachment')) {
            if ($objLease->attachment) {
                $oldPath = public_path('/uploads/contract/' . $objLease->attachment);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $uploaded_file = $request->file('attachment');
            $filename = time() . '_' . $uploaded_file->getClientOriginalName();
            $uploaded_file->move(public_path('uploads/contract'), $filename);
            $objLease->attachment = $filename;
        } elseif ($request->input('delete_img') == 1) {
            if ($objLease->attachment) {
                $path = public_path('/uploads/contract/' . $objLease->attachment);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $objLease->attachment = null;
        }
        if ($objLease->save()) {
            $return['msg'] = 'Contract Agreement has been updated successfully';
            $return['status'] = 'success';
        }

        return response()->json($return);
    }

    /* to delete the lease by ajax call */
    public function ajaxDelete(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to delete the Contract Agreement'
        ];
        if (!Auth::user()->hasPermissionTo('ContractAgreementDelete')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $record = Lease::where('id', '=', $id)->withCount(['assets', 'interact_caches', 'interact_records'])->first();
        if ($record->assets_count)
            return response()->json(['status' => 'error', 'msg' => 'Some assets are attached with this contract agreements. Please unlink them and try again !']);
        if ($record->interact_caches_count)
            return response()->json(['status' => 'error', 'msg' => 'Some assets are attached with this contract agreements. Please unlink them and try again !']);
        if ($record->interact_records_count)
            return response()->json(['status' => 'error', 'msg' => 'Some asset records are attached with this contract agreements. Please unlink them and try again !']);

        $objLease = '';
        try {
            $objLease = Lease::findOrFail($id);
            $objLease->delete();
            $return['status'] = 'success';
            $return['msg'] = 'Contract Agreement has been deleted successfully!';
            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($return);
        }
    }

    /* options by query */
    public function getLeaseByQuery(Request $request)
    {
        $return = array();
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $company_id = $request->input('company_id', null);
        $skip = (($page * 20) - 20);

        $db = DB::table('lease_agreements as l');
        $db->join('suppliers as s', 'l.leaser', '=', 's.id');
        $db->select('l.id', DB::raw('concat_ws(" - ", concat("#", l.id), concat(s.name, " (", date_format(l.start_date, "%b %Y"), " - ", date_format(l.end_date, "%b %Y"), ")")) as text'));

        $db->where('company_id', '=', $company_id);
        if ($search) {
            $db->whereRaw('concat_ws(" - ", concat("#", l.id), concat(s.name, " (", date_format(l.start_date, "%b %Y"), " - ", date_format(l.end_date, "%b %Y"), ")")) like "%' . $search . '%"');
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
        $return['results'] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function download(Request $request, $id)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('ContractAgreementDownload')) {
            return response()->json($return);
        }
        $a = Lease::find($id);
        $path = public_path('uploads/contract/' . $a['attachment']);
        return response()->download($path);
    }

    public function getLeaseInfo($id)
    {
        $info_id = $id;
        return view('config.lease_agreements.history')->with('info_id', $info_id);
    }

    public function ajaxGetLeaseInfo(Request $request)
    {
        $req = $request->all();

        $return = array(
            'draw' => date('is')
        );
        $id = $request->_id;
        $fields = array(
            '0' => 's.name',
            '1' => 'l.contract_number',
            '2' => 'lt.name',
            '3' => 'l.maintenance_incharge',
            '4' => 'l.description',
            '5' => 'l.start_date',
            '6' => 'l.end_date',
            '7' => 'l.status',
            '8' => DB::raw('concat(u.first_name, " ", u.last_name)'),
            '9' => 'l.updated_at',
        );

        $db = DB::table('lease_agreement_history as l')->whereIn('lease_id', [$id]);
        $db->leftJoin('suppliers as s', 's.id', '=', 'l.leaser');
        $db->leftJoin('lease_type as lt', 'lt.id', '=', 'l.lease_type');
        $db->leftJoin('users as u', 'l.updated_by', '=', 'u.id');
        $db->select('l.id', 's.name as leaser', 'lt.name as lease_type_name', 'l.description', 'l.contract_number', 'l.attachment');
        $db->addSelect(DB::raw('case when l.maintenance_incharge = 1 then  "By Company" when l.maintenance_incharge = 2 then "By contractor" else "" end as maintenance_incharge_name'));
        $db->addSelect(DB::raw('concat(u.first_name, " ", u.last_name) as updated_by'));
        $db->addSelect(DB::raw('case when l.status = 1 then "Active" else "Inactive" end as status'));
        $db->addSelect(DB::raw('DATE_FORMAT(l.start_date, "%d %b %Y") as start_date'));
        $db->addSelect(DB::raw('DATE_FORMAT(l.end_date, "%d %b %Y") as end_date'));
        $db->addSelect(DB::raw('DATE_FORMAT(l.updated_at, "%d %b %Y %h:%i %p") as updated_at'));
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf(
                '(l.id like "%%%1$s%%" or l.contract_number like "%%%1$s%%" or s.name like "%%%1$s%%" or lt.name like "%%%1$s%%" or 
                (case when l.status = 1 then "Active" when l.status = 0 then "Inactive" end) = "%1$s" or 
                (case when l.lease_type = 2 then "Finance Contract" when l.lease_type = 1 then "Operating Contract" end) = "%1$s" or 
                (case when l.maintenance_incharge = 2 then "By Contractor" when l.maintenance_incharge = 1 then "By Company" end) = "%1$s" or 
                DATE_FORMAT(l.start_date, "%%d %%b %%Y") like "%%%1$s%%" or 
                DATE_FORMAT(l.end_date, "%%d %%b %%Y") like "%%%1$s%%" or 
                DATE_FORMAT(l.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or 
                concat(u.first_name, " ", u.last_name) like "%%%1$s%%" or l.description like "%%%1$s%%")',
                $search_key
            );
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
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    public function downloadhistory(Request $request, $id)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('ContractAgreementDownload')) {
            return response()->json($return);
        }
        $a = DB::table('lease_agreement_history')->find($id);
        $path = public_path('uploads/contract/' . $a->attachment);
        return response()->download($path);
    }
}

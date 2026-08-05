<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Supplier;
use App\Models\Manufacture;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Label;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceCron;
use App\Models\DeviceMaintenance;
use App\Models\AssetExpense;
use App\Models\Settings;
use App\Helpers\Common as CommonHelper;
use Carbon\Carbon;
use Log;
use Validator;
use Auth;
use DB;
use App\Exports\Device\DeviceExpenseExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Mail;
use App\Mail\Asset\AddExpenseEmailNotification;

class DeviceMaintenanceController extends Controller {
    //device info device expense tab
    public function ajaxExpenseIndex(Request $request) {
        if(! Auth::user()->hasPermissionTo('DeviceExpenseRead')) {
            $return = ['status' => 'danger', 'msg' => trans('devices.device_info.controller.permission_denied')];
            return response()->json($return);
        }
        $req = $request->all();
        $return = array(
            'draw' => $req['draw']
        );

        $fields = array(
            'expense_type' => 'am.expense_type',
            'title' => 'am.title',
            'expense_date' => 'am.expense_date',
            'cost' => 'am.cost',
            'updated_on' => 'am.updated_at'
        );

        $mysqlDateTimeFormat = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $mysqlDateFormat = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $db = DB::table('asset_expenses as am');
        $db->select('am.id', 'am.expense_type', 'am.title', 'am.cost', 'am.currency_format', 'am.updated_at');
        $db->addSelect(DB::raw("DATE_FORMAT(am.expense_date, '{$mysqlDateFormat}') as expense_date_format"));
        $db->addSelect(DB::raw('case when am.expense_type = 1 then "Maintenance" when am.expense_type = 2 then "Repair" when am.expense_type = 3 then "Upgrade" when am.expense_type = 4 then "Miscellaneous" when am.expense_type = 5 then "Audit" end as expense_types'));
        $db->addSelect(DB::raw('case when am.cost is not null then concat(FORMAT(am.cost, 2), " ", am.currency_format) else FORMAT(0, 2) end as cost_format'));
        $db->addSelect(DB::raw("DATE_FORMAT(am.updated_at, '{$mysqlDateTimeFormat}') as last_updated_at"));
        $db->whereNull('am.temp_id');
        $db->whereNull('am.deleted_at');

        if (isset($req["device_id"]) && $req["device_id"]) {
            $db->where("am.asset_id", "=", $req["device_id"]);
        }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $whereStr = sprintf('((case when am.expense_type = 1 then "Maintenance" when am.expense_type = 2 then "Repair" when am.expense_type = 3 then "Upgrade" when am.expense_type = 4 then "Miscellaneous" when am.expense_type = 5 then "Audit" end) like "%%%1$s%%" or am.expense_type like "%%%1$s%%" or am.title like "%%%1$s%%" or DATE_FORMAT(am.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(am.expense_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when am.cost is not null then concat(FORMAT(am.cost, 2), " ", am.currency_format) else FORMAT(0, 2) end) like "%%%1$s%%")', $search_key);
            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($req['sorted_column_name']) && array_key_exists($req['sorted_column_name'], $fields) && in_array($req['sorted_direction'], ["asc", "desc"])) {
            $db->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }
        $db->skip($skip);
        $db->take($take);

        $data = $db->get();
        $return['data'] = array();
        foreach ($data as $d) {
            $d->currency_symbol = Currency::getSymbolByCode($d->currency_format);
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    public function expensesExport(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('devices.device_info.controller.permission_denied')];
        if(! Auth::user()->hasPermissionTo('DeviceExpenseDownload')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $req = $request->all();

        $db = DB::table('asset_expenses as am');
        $db->Select('assets.asset_tag as assets_tag')->leftJoin('assets', 'am.asset_id', '=','assets.id');
        $db->addSelect(DB::raw('case when am.expense_type = 1 then "Maintenance" when am.expense_type = 2 then "Repair" when am.expense_type = 3 then "Upgrade" when am.expense_type = 4 then "Miscellaneous" when am.expense_type = 5 then "Audit" end as expense_types'));
        $db->addSelect('am.title', 'am.currency_format');
        $mysqlDateTimeFormat = CommonHelper::mysqlDateTimeFormat('datetime', 'excel');
        $mysqlDateFormat = CommonHelper::mysqlDateTimeFormat('date', 'excel');
        $db->addSelect(DB::raw("DATE_FORMAT(am.expense_date, '{$mysqlDateFormat}') as expense_date_format"));
        $db->addSelect(DB::raw('case when am.is_warranty = 1 then "Yes" when am.is_warranty = 0 then "No" end as is_warrantys'));
        $db->addSelect(DB::raw('case when am.is_amc = 1 then "Yes" when am.is_amc = 0 then "No" end as is_amcs'));
        $db->addSelect(DB::raw('case when am.cost is not null then FORMAT(am.cost, 2)else FORMAT(0, 2) END as cost_value'));
        $db->addSelect('am.notes');
        $db->addSelect(DB::raw("DATE_FORMAT(am.created_at, '{$mysqlDateTimeFormat}') as last_created_at"));
        $db->addSelect(DB::raw("DATE_FORMAT(am.updated_at, '{$mysqlDateTimeFormat}') as last_updated_at"));
        $db->whereNull('am.temp_id');
        $db->whereNull('am.deleted_at');

        if(isset($req["device_id"]) && $req["device_id"]) {
            $db->where("am.asset_id", "=", $req["device_id"]);
        }

        if($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if(isset($filters->search)) {
                $req["search"] = $filters->search;
            }

            if (isset($req["search"]) && $search_key = trim($req["search"])) {
                $whereStr = sprintf('((case when am.expense_type = 1 then "Maintenance" when am.expense_type = 2 then "Repair" when am.expense_type = 3 then "Upgrade" when am.expense_type = 4 then "Miscellaneous" when am.expense_type = 5 then "Audit" end) like "%%%1$s%%" or am.expense_type like "%%%1$s%%" or am.title like "%%%1$s%%" or DATE_FORMAT(am.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or DATE_FORMAT(am.expense_date, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" or (case when am.cost is not null then concat(FORMAT(am.cost, 2), " ", am.currency_format) else FORMAT(0, 2) end) like "%%%1$s%%")', $search_key);
                $db->whereRaw($whereStr);
                $return['recordsFiltered'] = $db->count();
            }
        }
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        $data = $db->get();
        $result = $data->map(function ($d ,$index) {
            $currency = Currency::getCurrencyByCode($d->currency_format);
            return [
                'Sr No' => $index + 1,
                'assets_tag' => $d->assets_tag,
                'expense_types' => $d->expense_types,
                'title' => $d->title,
                'expense_date_format' => $d->expense_date_format,
                'is_warrantys' => $d->is_warrantys,
                'is_amcs' => $d->is_amcs,
                'cost_format' => ($currency['symbol'] ?? $d->currency_format) . ' ' . $d->cost_value,
                'notes' => $d->notes,
                'last_created_at' => $d->last_created_at,
                'last_updated_at' => $d->last_updated_at
            ];
        })->toArray();
        return Excel::download(new DeviceExpenseExport ($result), 'DeviceExpense.xlsx');
    }

    public function ajaxSaveExpense(Request $request) {
        $return = ['status' => 'error', 'msg' => trans('devices.device_info.controller.permission_denied')];
        if(! Auth::user()->hasPermissionTo('DeviceExpenseAdd')) {
            return response()->json($return);
        }
        
        $return = array(
            "status" => "failure",
            "msg" => trans("devices.device_info.controller.unable_to_save_details"),
        );

        $rules = [
            "asset_id" => 'required|integer|min:1|exists:assets,id',
            "title" => "required|alpha_space|max:100",
            "expense_type" => "required|alpha_space",
            'is_amc' => 'nullable',
            'curreny_format' => 'required_if:cost,!=,""',
            'is_warranty' => 'nullable',
            'expense_date' => 'nullable|date_format:d/m/Y',
            'cost' => 'nullable|numeric|regex:/^\d{1,11}(\.\d{1,2})?$/',
            'notes' => 'nullable|string|max:2000',
            'forAction' => 'nullable|string|max:20'
        ];

        $messages = [
            'cost.regex' => trans('devices.device_info.controller.pls_enter_valid_cost_length'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $dm = "";
        if ($request->forAction == "edit") {
            $dm = AssetExpense::where("id", "=", $request->id)->first();
            if (!$dm || !$dm->exists) {
                $return["msg"] = trans('devices.device_info.controller.record_does_not_exists');
                return response()->json($return);
            }
        }
        else {
            $dm = new AssetExpense();
        }

        $device = Device::where("id", "=", $request->asset_id)->first();

        if ($device->status->sold == 1) {
            $return["msg"] = trans('devices.device_info.controller.chosen_device_is_in_sold_status');
            return response()->json($return);
        }
        if ($device->status->stolen_item == 1) {
            $return["msg"] = trans('devices.device_info.controller.chosen_device_is_in_lost');
            return response()->json($return);
        }

        if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($device)) {
            // $return["status"] = 'error';
            $return["section"] = 'device-maintainance';
            $return["msg"] = Auth::user()->company_id == null ? trans('devices.device_info.controller.update_company_name'): trans('devices.device_info.controller.multiple_company_access');
            return response()->json($return);
        }

        $dm->title = $request->title ? $request->title : '';
        $dm->notes = $request->notes ? $request->notes : null;
        $dm->asset_id = $request->asset_id;
        $dm->expense_type = $request->expense_type;
        $dm->is_warranty = $request->is_warranty ? (bool) $request->is_warranty : 0;
        $dm->is_amc = $request->is_amc ? (bool) $request->is_amc : 0;
        $dm->currency_format = $request->currency_format;
        $dm->cost = $request->cost ? (float) $request->cost : null;
        $dm->expense_date = $request->expense_date ? CommonHelper::getDateAs($request->expense_date, "Y-m-d", "d/m/Y") : null;
        $dm->temp_id = isset($request->temp_id) ? $request->temp_id : null;
        if (!$dm->save()) {
            return response()->json($return);
        }

        $user = Auth::user();
        $userEmail = isset($user->email) && $user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL) ? $user->email : null;
        $user_name = isset($user->displayName) && $user->displayName != "" ? $user->displayName : trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        $alertnotify = CommonHelper::getGlobalAlertEmail();
        $ccEmails = [];

        if (config('mail.service_enabled') && is_array($alertnotify) && $userEmail) {
            foreach ($alertnotify as $email) {
                if (isset($email) && $email != "" && filter_var($email, FILTER_VALIDATE_EMAIL) && $email != $userEmail && !in_array($email, $ccEmails)) {
                    $ccEmails[] = $email;
                }
            }
            try {
                Mail::to($userEmail)->cc($ccEmails)->queue(new AddExpenseEmailNotification($dm, $user_name, $device));
            } catch (\Exception $e) {
                Log::error("AddExpenseEmailNotification mail failed to send. Error: " . $e->getMessage());
            }
        }

        $return["status"] = "success";
        $return["msg"] = $request->forAction == "edit" ? trans('devices.device_info.controller.changes_have_been_saved_successfully') : trans('devices.device_info.controller.new_maintenance_record_added_successfully');
        return response()->json($return);
    }
    public function ajaxExpenseDelete(Request $request, $id) {
        $return = ['status' => 'error', 'msg' => trans('devices.device_info.controller.permission_denied')];
        if(! Auth::user()->hasPermissionTo('DeviceExpenseDelete')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $return = array(
            "status" => "failure",
            "msg" => trans('devices.device_info.controller.unable_to_save_given_details')
        );

        $dm = AssetExpense::with('device')->where("id","=", $id)->first();
        if (empty($dm->device))
            return response()->json($return);
        if (!Auth::user()->isSuperUser() && !Company::checkUserAccess($dm->device)) {
            // $return["status"] = 'error';
            $return["section"] = 'reject-device-request';
            $return["msg"] = Auth::user()->company_id == null ? trans('devices.device_info.controller.update_company_name'): trans('devices.device_info.controller.multiple_company_access');
            return response()->json($return);
        }

        // var_dump(Company::checkUserAccess($dm->device));exit;

        if (!$dm) {
            return response()->json($return);
        }

        $dm->delete();
        $return["status"] = "success";
        $return["msg"] = trans('devices.device_info.controller.record_has_deleted_successfully');
        return response()->json($return);
    }
    public function getExpenseDetails(Request $request, $id, $forAction) {
        $return = ['status' => 'error', 'msg' => trans('devices.device_info.controller.permission_denied')];
        if(! Auth::user()->hasPermissionTo('DeviceExpenseEdit')) {
            return response()->json($return);
        }
        
        $return = array("status"=>"failure", "msg"=>"trans('devices.device_info.controller.unable_to_open_record')");
        $dm = AssetExpense::where('id','=',$id)->first();

        if (!$dm || !$dm->exists) {
            return response()->json($return);
        }

        $dm->expense_date = CommonHelper::getDateAs($dm->expense_date, "d-m-Y", "Y-m-d");

        $dev = array();
        $dev["data"] = $dm->toArray();
        $dev["dropdown"] = array();

        if ($dm->asset_id) {
            $getDevice = Device::where("assets.id", $dm->asset_id)
            ->leftJoin("models as mdl", "mdl.id", "=", "assets.model_id")
            ->select("assets.id", DB::raw("concat_ws('', assets.asset_tag, ' - ', mdl.name, ' ', mdl.modelno) as text"))
            ->first();
            $dev["dropdown"]["device"] = $getDevice? $getDevice->toArray() : null;
        }

        if ($dm->supplier_id) {
            $getSupplier = Supplier::where("id", $dm->supplier_id)->select("id", "name as text")->first();
            $dev["dropdown"]["supplier"] = $getSupplier && $getSupplier->exists ? $getSupplier->toArray() : null;
        }

        $return['status'] = 'success';
        $return['msg'] = '';
        $return['device_maintenance'] = $dev;
        return $return;
    }
}
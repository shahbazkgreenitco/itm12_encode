<?php

namespace App\Http\Controllers\Config;

use App\Exports\PurchaseExport;
use App\Helpers\Common as CommonHelper;
use App\Http\Controllers\Controller;
use App\Imports\PurchasesImport;
use App\Models\CustomTax\TaxDefination;
use App\Models\CustomTax\TaxElements;
use App\Models\Procurement\Charges;
use App\Models\Procurement\Unit;
use App\Models\Purchase\PurchaseItem;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Location;
use App\Models\Purchase;
use App\Models\PurchaseAttachment;
use App\Models\PurchaseTaxElement;
use App\Models\Settings;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use DateTime;
use DB;
use Image;
use stdClass;
use Storage;
use Validator;

class PurchaseController extends Controller
{
    public function getIndex(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseRead')) {
            return redirect('dashboard')->with('msg', $return);
        }
        $vd = new stdClass();
        $objPurchase = new Purchase();
        $currencies = Currency::getCurrencies();
        // $vd->location = Location::select("id", "name")->get();
        $location = isset($request->location) ? $request->location : null;
        return view('purchases.index')->with(compact('location', 'objPurchase', 'currencies'));
    }

    public function ajaxPurchases(Request $request)
    {
        $data = $request->all();

        $fields = array(
            'a.invoice_date_on' => 'pur.invoice_date',
            'a.invoice_no' => 'pur.invoice_no',
            'a.received_date_on' => 'pur.received_date',
            'a.loc_name' => 'loc.name',
            'a.company_name' => 'company_name',
            'a.supplier_name' => 'supplier_name',
            'a.bill_amount' => 'pur.bill_amount',
            'a.status' => 'pur.fully_received',
            'a.doc_count' => 'doc_count',
            'a.created_at' => 'pur.created_at',
            'a.updated_on' => 'pur.updated_at'
        );

        $db = DB::table('purchases as pur');
        $db->whereNull('pur.deleted_at');
        $db->leftJoin('suppliers as sup', 'pur.supplier_id', '=', 'sup.id');
        $db->leftJoin('companies as comp', 'pur.company_id', '=', 'comp.id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'pur.location_id');
        // $db->join($objPurchase->tblCountry . ' as coun', 'sup.country', '=', 'coun.country_code');
        $db->select('pur.id', 'pur.po_number', 'pur.invoice_date', 'pur.invoice_no', 'comp.name as company_name', 'sup.name as supplier_name', 'pur.bill_amount', 'pur.currency', 'sup.city', 'sup.state', 'loc.name as loc_name', 'include_taxes', 'tax_id', 'qty', 'pur.pr_id');
        $db->addSelect(DB::raw('case when pur.fully_received = 0 then "Not Yet Received" when pur.fully_received = 1 then "Fully Received" when pur.fully_received = 2 then "Partially Received" when pur.fully_received = 3 then "Provision" else "" end as status'));

        $mysqlDateTimeFormat = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $mysqlDateFormat = CommonHelper::mysqlDateTimeFormat('date', 'display');
        $db->addSelect(DB::raw("DATE_FORMAT(pur.invoice_date, '{$mysqlDateFormat}') as invoice_date_on"));
        $db->addSelect(DB::raw('DATE_FORMAT(pur.received_date, "%d %b %Y") as received_date_on'));
        $db->addSelect(DB::raw("DATE_FORMAT(pur.updated_at, '{$mysqlDateTimeFormat}') as updated_on"));
        $db->addSelect(DB::raw("DATE_FORMAT(pur.created_at, '{$mysqlDateTimeFormat}') as created_at"));
        // $db->addSelect(DB::raw("(SELECT count(purchase_docs.id) FROM `purchase_docs` WHERE `po_id`=pur.id) as doc_count"));
        $db->addSelect(DB::raw('(SELECT count(purchase_attachments.id) FROM `purchase_attachments` WHERE `reference_id`=pur.id) as doc_count'));

        if (!Auth::user()->isSuperUser()) {
            //     if(Auth::user()->hasPermission('admin')){
            // if(Settings::first()->full_multiple_companies_support != 1)
            $db->where('pur.company_id', '=', Auth::user()->company_id);
            //     }
        }
        $return = array(
            'draw' => date('is')
        );

        if (isset($data['filters'])) {
            $filters = $data['filters'];
            $db->where(function ($query) use ($filters) {
                if (isset($filters['location']) && $filters['location'] && $filters['location'] != 'null') {
                    $query->where('pur.location_id', '=', (int) $filters['location']);
                }
                if (isset($filters['supplier']) && $filters['supplier'] && $filters['supplier'] != 'null') {
                    $query->where('pur.supplier_id', '=', (int) $filters['supplier']);
                }
                if (isset($filters['status'])) {
                    $query->where('pur.fully_received', '=', (int) $filters['status']);
                }
            });
            $based_on_possible = ['1' => 'pur.invoice_date', '2' => 'pur.received_date', '3' => 'pur.created_at', '4' => 'pur.updated_at'];
            if (isset($filters['based_on']) && $filters['based_on'] && $filters['based_on'] != 'null' && $filters['based_on'] >= 1 && $filters['based_on'] <= 4) {
                if (isset($filters['date_range']) && $filters['date_range'] && $filters['date_range'] != 'null') {
                    $daterange = explode(' - ', $filters['date_range']);
                    $from_date = date('Y-m-d H:i:s', strtotime($daterange[0]));
                    $to_date = date('Y-m-d H:i:s', strtotime($daterange[1]));
                    if ($from_date && $to_date) {
                        $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                        $db->whereRaw($whereStr);
                    }
                }
            }
            $is_searching = true;
        }

        if (isset($data['location']) && $data['location'] && $data['location'] != 'null') {
            $array = explode(',', $data['location']);
            $db->whereIn('pur.location_id', $array);
        }

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($data['search']['value']) && $search_key = trim($data['search']['value'])) {
            $whereStr = sprintf('(pur.invoice_no like "%%%1$s%%" or pur.po_number like "%%%1$s%%" or (case when pur.fully_received = 0 then "Not Yet Received" when pur.fully_received = 1 then "Fully Received" when pur.fully_received = 2 then "Partially Received" when pur.fully_received = 3 then "Provision" end) like "%%%1$s%%" or DATE_FORMAT(pur.invoice_date, "%%d %%b %%Y") like "%%%1$s%%" or comp.name like "%%%1$s%%" or loc.name like "%%%1$s%%" or DATE_FORMAT(pur.created_at, "%%d %%b %%Y") like "%%%1$s%%"  or DATE_FORMAT(pur.received_date, "%%d %%b %%Y") like "%%%1$s%%" or sup.name like "%%%1$s%%" or pur.bill_amount like "%%%1$s%%" or DATE_FORMAT(pur.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" )', $search_key);

            $db->whereRaw($whereStr);
            $return['recordsFiltered'] = $db->count();
        }

        if (isset($data['sorted_column_name']) && isset($fields[$data['sorted_column_name']]) && in_array($data['sorted_direction'], ['asc', 'desc'])) {
            $db->orderBy($fields[$data['sorted_column_name']], $data['sorted_direction']);
        }

        // $db->whereNull('pur.deleted_at');

        $skip = 0;
        $take = 10;
        if (isset($data['start']) && isset($data['length'])) {
            $skip = (int) $data['start'];
            $take = (int) $data['length'];
        }
        $db->skip($skip);
        $db->take($take);

        $data = $db->get();

        $return['data'] = array();
        foreach ($data as $d) {
            $totalNetAmount = $totalTaxAmount = 0;
            if ($d->include_taxes == 1) {
                $getTaxElement = PurchaseTaxElement::where('tax_defination_id', $d->tax_id)->select('tax_element_id', 'value', 'amount')->where('purchase_id', $d->id)->get();
                $pricePerUnit = $d->bill_amount / ($d->qty ?? 1);
                $totalNetAmount = $pricePerUnit * ($d->qty ?? 1);
                foreach ($getTaxElement as $taxElement) {
                    $tax = TaxElements::where('id', $taxElement->tax_element_id)->select('tax_element AS text')->first();
                    if ($tax) {
                        $taxAmount = ($totalNetAmount * $taxElement->value) / 100;
                        $totalTaxAmount += $taxAmount;
                    }
                }
            }
            $final_total = 0;
            $charge_total_add = 0;
            $charge_total_subtract = 0;
            if (!empty($d->pr_id)) {
                $chargeCalculation = Charges::select('charges_element.title as titles', 'charges_element.action', 'po_ce.charge')
                    ->leftJoin('po_extra_charges_element as po_ce', 'charges_element.id', '=', 'po_ce.charge_element_id')
                    ->leftJoin('procure_quotations as pq', 'po_ce.procure_quotations_id', '=', 'pq.id')
                    ->where('po_ce.procure_quotations_id', $d->pr_id)
                    ->get();
                foreach ($chargeCalculation as $key => $v) {
                    if ($v->action == 1) {
                        $charge_total_add += $v->charge;
                    } else {
                        $charge_total_subtract += $v->charge;
                    }
                }
                $final_total = $totalNetAmount + $totalTaxAmount + $charge_total_add - $charge_total_subtract;
            }

            $totalAmount = $totalNetAmount + $totalTaxAmount;
            $d->total_amount = number_format((float) $totalAmount, 2, '.', '');
            $d->extra_charges_with_tax = number_format((float) $final_total, 2, '.', '');
            $return['data'][] = [
                'a' => $d,
            ];
        }

        return response()->json($return);
    }

    public function download(Request $request)
    {
        $db = DB::table('purchases as pur');
        $db->whereNull('pur.deleted_at');
        $db->leftJoin('suppliers as sup', 'pur.supplier_id', '=', 'sup.id');
        $db->leftJoin('companies as comp', 'pur.company_id', '=', 'comp.id');
        $db->leftJoin('locations as loc', 'loc.id', '=', 'pur.location_id');
        $db->select('pur.id', 'pur.po_number', 'pur.invoice_date', 'pur.invoice_no', 'comp.name as company_name', 'sup.name as supplier_name', 'pur.bill_amount', 'pur.currency', 'sup.city', 'sup.state', 'loc.name as loc_name', 'pur.include_taxes', 'pur.tax_id', 'pur.qty');
        $db->addSelect(DB::raw('case when pur.fully_received = 0 then "Not Yet Received" when pur.fully_received = 1 then "Fully Received" when pur.fully_received = 2 then "Partially Received" when pur.fully_received = 3 then "Provision" else "" end as status'));

        $db->addSelect(DB::raw('DATE_FORMAT(pur.invoice_date, "%d %b %Y") as invoice_date_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(pur.received_date, "%d %b %Y") as received_date_on'));
        $db->addSelect(DB::raw('DATE_FORMAT(pur.updated_at, "%d %b %Y %h:%i %p") as updated_on'));
        $db->addSelect(DB::raw('(SELECT count(purchase_attachments.id) FROM `purchase_attachments` WHERE `reference_id`=pur.id) as doc_count'));

        if (!Auth::user()->isSuperUser()) {
            //     if(Auth::user()->hasPermission('admin')){
            // if(Settings::first()->full_multiple_companies_support != 1)
            $db->where('pur.company_id', '=', Auth::user()->company_id);
            //     }
        }

        if (isset($data['location']) && $data['location'] && $data['location'] != 'null') {
            $array = explode(',', $data['location']);
            $db->whereIn('pur.location_id', $array);
        }

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if (isset($filters->search)) {
                $req['search'] = $filters->search;
            }
            if (isset($filters->other_filters)) {
                $req['filters'] = (array) $filters->other_filters;
            }

            if (isset($req['filters'])) {
                $filters = $req['filters'];
                if (isset($filters['location']) && $filters['location'] && $filters['location'] != 'null') {
                    $db->where('pur.location_id', '=', (int) $filters['location']);
                }
                if (isset($filters['supplier']) && $filters['supplier'] && $filters['supplier'] != 'null') {
                    $db->where('pur.supplier_id', '=', (int) $filters['supplier']);
                }
                if (isset($filters['status'])) {
                    $db->where('pur.fully_received', '=', (int) $filters['status']);
                }
                $based_on_possible = ['1' => 'pur.invoice_date', '2' => 'pur.received_date', '3' => 'pur.created_at', '4' => 'pur.updated_at'];
                if (isset($filters['based_on']) && $filters['based_on'] && $filters['based_on'] != 'null' && $filters['based_on'] >= 1 && $filters['based_on'] <= 4) {
                    if (isset($filters['date_range']) && $filters['date_range'] && $filters['date_range'] != 'null') {
                        $daterange = explode(' - ', $filters['date_range']);
                        $from_date = date('Y-m-d H:i:s', strtotime($daterange[0]));
                        $to_date = date('Y-m-d H:i:s', strtotime($daterange[1]));
                        if ($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $db->whereRaw($whereStr);
                        }
                    }
                }

                $is_searching = true;
                if (isset($req['search']) && $search_key = trim($req['search'])) {
                    $whereStr = sprintf('(pur.invoice_no like "%%%1$s%%" or pur.po_number like "%%%1$s%%" or (case when pur.fully_received = 0 then "Not Yet Received" when pur.fully_received = 1 then "Fully Received" when pur.fully_received = 2 then "Partially Received" when pur.fully_received = 3 then "Provision" end) like "%%%1$s%%" or DATE_FORMAT(pur.invoice_date, "%%d %%b %%Y") like "%%%1$s%%" or comp.name like "%%%1$s%%" or loc.name like "%%%1$s%%"  or DATE_FORMAT(pur.received_date, "%%d %%b %%Y") like "%%%1$s%%" or sup.name like "%%%1$s%%" or pur.bill_amount like "%%%1$s%%" or DATE_FORMAT(pur.created_at, "%%d %%b %%Y") like "%%%1$s%%" or DATE_FORMAT(pur.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%" )', $search_key);
                    $db->whereRaw($whereStr);
                    $return['recordsFiltered'] = $db->count();
                }
            }
        }
        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        $data = $db->get();
        $dataArray = [];
        foreach ($data as $key => $value) {
            $totalNetAmount = $totalTaxAmount = 0;
            if ($value->include_taxes == 1) {
                $getTaxElement = PurchaseTaxElement::where('tax_defination_id', $value->tax_id)->select('tax_element_id', 'value', 'amount')->where('purchase_id', $value->id)->get();
                $pricePerUnit = $value->bill_amount / ($value->qty ?? 1);
                $totalNetAmount = $pricePerUnit * ($value->qty ?? 1);
                foreach ($getTaxElement as $taxElement) {
                    $tax = TaxElements::where('id', $taxElement->tax_element_id)->select('tax_element AS text')->first();
                    if ($tax) {
                        $taxAmount = ($totalNetAmount * $taxElement->value) / 100;
                        $totalTaxAmount += $taxAmount;
                    }
                }
            }
            $totalAmount = $totalNetAmount + $totalTaxAmount;
            $total_amount = number_format((float) $totalAmount, 2, '.', '');
            $dataArray[] = [
                $value->invoice_date,
                $value->invoice_no,
                $value->po_number,
                $value->received_date_on,
                $value->loc_name,
                $value->company_name,
                $value->supplier_name,
                $value->currency,
                $value->bill_amount,
                $total_amount,
                $value->status,
            ];
        }
        $result = json_decode(json_encode($dataArray, true), true);
        return Excel::download(new PurchaseExport($result), 'PurchaseExport.xlsx');
    }

    public function addPurchase(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseAdd')) {
            return response()->json($return);
        }
        $appSettings = Settings::first();
        if (empty(Auth::user()->company_id))
            return response()->json(['status' => 'error', 'section' => 'purchase-add', 'msg' => 'You are not allowed for this process !! Please update your company ID and try again !!']);

        $objPurchase = new Purchase;
        $rules = [
            'invoice_date' => 'required|nullable|date_format:d/m/Y',
            'received_date' => 'nullable|date_format:d/m/Y|after_or_equal:invoice_date',
            'invoice_no' => 'required|string|max:100|unique:purchases,invoice_no|clean_text_only',
            'company_id' => 'required|exists:companies,id',
            'supplier_id' => 'sometimes|nullable|exists:suppliers,id',
            'location_id' => 'required|exists:locations,id',
            'po_number' => 'nullable|clean_text_only',
            'currency' => 'required',
            'bill_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:10000|clean_text_only',
            'fully_received' => 'integer|min:0|max:3',
            'file' => 'nullable',
            'other_info' => 'nullable|string|max:255|clean_text_only',
            'other_info1' => 'nullable|string|max:255|clean_text_only',
            'qty' => 'nullable|integer',
        ];
        $messages = [
            // 'depreciation_id.required'=>'Please select Depreciation',
            // 'qty.required'=>'Quantity field is required',
            'received_date.after_or_equal' => 'The received date must be after or equal to the purchase date.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $key => $value) {
                $return['status'] = 'error';
                $return['msg'] = $value;
            }
            // return array('status'=>'error','msg'=>$validator->errors()->getMessages());
            return response()->json($return);
        } else {
            $data = $request->validate($rules);

            /*if( !Auth::user()->isSuperUser() && Auth::user()->company_id != $request->company_id )
                return response()->json(['status' => 'error', 'msg' => 'Insufficient permission !!']);*/
            // print_r($data);die;
            if (Auth::user()->isSuperUser())
                $objPurchase->company_id = empty($data['company_id']) ? Auth::user()->company_id : $data['company_id'];
            else
                $objPurchase->company_id = $appSettings->full_multiple_companies_support == 0 ? (empty($data['company_id']) ? Auth::user()->company_id : $data['company_id']) : Auth::user()->company_id;

            $objPurchase->invoice_date = $request->invoice_date ? CommonHelper::getDateAs($request->invoice_date, 'Y-m-d', 'd/m/Y') : null;
            $objPurchase->received_date = $request->received_date ? CommonHelper::getDateAs($request->received_date, 'Y-m-d', 'd/m/Y') : null;
            $objPurchase->invoice_no = $request->invoice_no;
            $objPurchase->supplier_id = $request->supplier_id;
            $objPurchase->location_id = $request->location_id;
            $objPurchase->currency = $request->currency;
            $objPurchase->bill_amount = $request->bill_amount;
            $objPurchase->po_number = $request->po_number;
            $objPurchase->notes = $request->notes;
            $objPurchase->other_info = $request->other_info;
            $objPurchase->other_info1 = $request->other_info1;
            $objPurchase->fully_received = isset($data['fully_received']) ? $data['fully_received'] : 0;
            $objPurchase->qty = $request->qty;
            $objPurchase->include_taxes = $request->include_taxes;
            $objPurchase->tax_id = $request->tax_id;

            $entryArray = [];
            $entryArray[0] = ['entry_at' => date('Y-m-d H:i:s'), 'user_id' => Auth::user()->id];
            if (!empty($request->notes))
                $entryArray[0]['notes'] = $request->notes;
            if (!empty($request->bill_amount))
                $entryArray[0]['bill_amount'] = $request->bill_amount;
            if (!empty($request->qty))
                $entryArray[0]['qty'] = $request->qty;
            if (!empty($request->invoice_no))
                $entryArray[0]['invoice_no'] = $request->invoice_no;
            if (!empty($request->currency))
                $entryArray[0]['currency'] = $request->currency;
            if (!empty($request->supplier_id))
                $entryArray[0]['supplier'] = $objPurchase->supplier->name ?? null;
            if (!empty($request->location_id))
                $entryArray[0]['location'] = $objPurchase->location->name ?? null;
            if (!empty($request->po_number))
                $entryArray[0]['po_number'] = $request->po_number;
            if (!empty($request->other_info))
                $entryArray[0]['other_info'] = $request->other_info;
            if (!empty($request->other_info1))
                $entryArray[0]['other_info1'] = $request->other_info1;

            if (!empty($request->received_date))
                $entryArray[0]['received_date'] = empty($request->received_date) ? null : CommonHelper::getDateAs($request->received_date, 'Y-m-d', 'd/m/Y');
            $objPurchase->history = json_encode($entryArray);
            if ($objPurchase->save()) {
                if (isset($data['file'])) {
                    $attachment = $data['file'];
                    unset($data['file']);
                }

                if (!empty($attachment)) {
                    // $data_attach['original_file_name'] = $request->file('image')->getClientOriginalName();
                    $data_attach['po_id'] = $objPurchase->id;
                    $data_attach['original_file_name'] = $request->file('file')->getClientOriginalName();
                    $data_attach['file_name'] = uniqid('', true) . '.' . $request->file('file')->getClientOriginalExtension();
                    $data_attach['extension'] = strtolower($request->file('file')->getClientOriginalExtension());

                    if (in_array($data_attach['extension'], array('jpeg', 'png', 'bmp', 'gif', 'svg')))
                        $data_attach['doc_type'] = 2;
                    else if ($data_attach['extension'] == 'pdf')
                        $data_attach['doc_type'] = 1;
                    else {
                        $data_attach['doc_type'] = 3;
                    }

                    if ($data_attach['doc_type'] == 2) {
                        $path = storage_path('purchase/' . $data_attach['file_name']);
                        $img = Image::make(file_get_contents($request->file('file')->getRealPath()));
                        $img->save($path, ($request->file('file')->getClientSize() > 1024 ? 75 : 100));
                    } else {
                        $transfer_stat = Storage::disk('purchase')->put(
                            $data_attach['file_name'],
                            file_get_contents($request->file('file')->getRealPath())
                        );
                    }

                    $objPurchase->addPurchaseAttachment($data_attach);
                    // $serviceTicket->addTicketAttachment($data_attach);
                }
                if ($objPurchase->include_taxes == true) {
                    foreach ($request->tax_percentage as $key => $v) {
                        if ($v != 0 && $v != null) {
                            $PurchaseTaxElement = new PurchaseTaxElement();
                            $PurchaseTaxElement->tax_defination_id = (int) $request->tax_id;
                            $PurchaseTaxElement->tax_element_id = $key;
                            $PurchaseTaxElement->value = $v;
                            $PurchaseTaxElement->purchase_id = $objPurchase->id;
                            // $PurchaseTaxElement->amount = round(($objPurchase->bill_amount * $v) / 100, 2);
                            if ($objPurchase->qty) {
                                $PurchaseTaxElement->amount = round((($objPurchase->bill_amount * $v) / 100) * $objPurchase->qty, 2);
                            } else {
                                $PurchaseTaxElement->amount = round(($objPurchase->bill_amount * $v) / 100, 2);
                            }
                            if (!$PurchaseTaxElement->save()) {
                                return response()->json($return);
                            }
                        }
                    }
                }
                $return['msg'] = 'Purchase has been added successfully';
                $return['id'] = $objPurchase->id;
                $return['status'] = 'success';
                return response()->json($return);
            }
        }
    }

    public function editPurchase($id, Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseEdit')) {
            return response()->json($return);
        }
        $appSettings = Settings::first();
        $objPurchase = Purchase::find($id);
        if (empty($objPurchase))
            return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);

        if (!Company::checkUserAccess($objPurchase))
            return response()->json(['status' => 'error', 'msg' => 'Insufficient permission for this purchase !!']);
        $rules = [
            'invoice_date' => 'required|nullable|date_format:d/m/Y',
            'received_date' => 'nullable|date_format:d/m/Y|after_or_equal:invoice_date',
            'invoice_no' => ['required', 'string', 'clean_text_only', 'max:100', Rule::unique('purchases')->ignore($id)],
            'company_id' => 'required|exists:companies,id',
            'supplier_id' => 'sometimes|nullable|exists:suppliers,id',
            'location_id' => 'required|exists:locations,id',
            'po_number' => 'nullable|clean_text_only',
            'currency' => 'required',
            'bill_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:10000|clean_text_only',
            'fully_received' => 'integer|min:0|max:3',
            'file' => 'nullable',
            'other_info' => 'nullable|string|max:255|clean_text_only',
            'other_info1' => 'nullable|string|max:255|clean_text_only',
            'qty' => 'nullable|integer',
        ];
        $messages = [
            // 'depreciation_id.required'=>'Please select Depreciation',
            // 'qty.required'=>'Quantity field is required',
            'received_date.after_or_equal' => 'The received date must be after or equal to the purchase date.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $key => $value) {
                $return['status'] = 'error';
                $return['msg'] = $value;
            }
            return response()->json($return);
            // return back()->withInput()->withErrors($validator->messages());
            // return array('status'=>'error','errors'=>$validator->errors()->getMessages());
        } else {
            $data = $request->validate($rules);

            if (Auth::user()->isSuperUser())
                $objPurchase->company_id = empty($data['company_id']) ? Auth::user()->company_id : $data['company_id'];
            else
                $objPurchase->company_id = $appSettings->full_multiple_companies_support == 0 ? (empty($data['company_id']) ? Auth::user()->company_id : $data['company_id']) : Auth::user()->company_id;

            $receivedDateChanged = $objPurchase->received_date != (empty($request->received_date) ? null : CommonHelper::getDateAs($request->received_date, 'Y-m-d', 'd/m/Y'));
            $objPurchase->invoice_date = $request->invoice_date ? CommonHelper::getDateAs($request->invoice_date, 'Y-m-d', 'd/m/Y') : null;
            $objPurchase->received_date = $request->received_date ? CommonHelper::getDateAs($request->received_date, 'Y-m-d', 'd/m/Y') : null;
            $objPurchase->invoice_no = $request->invoice_no;
            $objPurchase->supplier_id = $request->supplier_id;
            $objPurchase->location_id = $request->location_id;
            $objPurchase->currency = $request->currency;
            $objPurchase->bill_amount = $request->bill_amount;
            $objPurchase->po_number = $request->po_number;
            $objPurchase->other_info = $request->other_info;
            $objPurchase->other_info1 = $request->other_info1;
            $objPurchase->qty = $request->qty;
            $objPurchase->include_taxes = $request->include_taxes;
            $objPurchase->tax_id = $request->tax_id;
            if (!empty($request->notes))
                $objPurchase->notes = $request->notes;
            $objPurchase->fully_received = isset($data['fully_received']) ? $data['fully_received'] : 0;

            $history = json_decode($objPurchase->history, true);
            if (!empty($objPurchase->getDirty())) {
                $entryArray = [];
                $entryArray = $objPurchase->getDirty();
                $entryArray['entry_at'] = date('Y-m-d H:i:s');
                $entryArray['user_id'] = Auth::user()->id;
                $entryArray['supplier'] = !empty($entryArray['supplier_id']) ? $objPurchase->supplier->name : null;
                $entryArray['location'] = !empty($entryArray['location_id']) ? $objPurchase->location->name : null;
                $history[] = json_encode($entryArray);
                $objPurchase->history = json_encode($history);
            }
            if (isset($data['file'])) {
                $attachment = $data['file'];
                unset($data['file']);
            }

            // echo $request->file('file')->isValid() ? "11" : "00";            exit;

            if (!empty($attachment)) {
                // $data_attach['original_file_name'] = $request->file('image')->getClientOriginalName();
                $data_attach['po_id'] = $objPurchase->id;
                $data_attach['original_file_name'] = $request->file('file')->getClientOriginalName();
                $data_attach['file_name'] = uniqid('', true) . '.' . $request->file('file')->getClientOriginalExtension();
                $data_attach['extension'] = strtolower($request->file('file')->getClientOriginalExtension());

                if (in_array($data_attach['extension'], array('jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg')))
                    $data_attach['doc_type'] = 2;
                else if ($data_attach['extension'] == 'pdf')
                    $data_attach['doc_type'] = 1;
                else {
                    $data_attach['doc_type'] = 3;
                }

                // var_dump($request->file('file'));die;
                // var_dump($request->file('file')->getRealPath());die;
                // $transfer_stat = Storage::disk('purchase')->put($data_attach['file_name'],
                //     file_get_contents($request->file('file')->getRealPath())
                // );
                // echo $data_attach['doc_type'];die;
                if ($data_attach['doc_type'] == 2) {
                    // $uploaded_img = $request->file('file');
                    // $resizePurchaseImage = $ModelImageName . '_thumbnail.' . $uploaded_img->getClientOriginalExtension();
                    $path = storage_path('purchase/' . $data_attach['file_name']);
                    $img = Image::make(file_get_contents($request->file('file')->getRealPath()));
                    $img->save($path, ($request->file('file')->getClientSize() > 1024 ? 75 : 100));
                    // $objModel->image_thumbnail = $resizePurchaseImage;
                } else {
                    $transfer_stat = Storage::disk('purchase')->put(
                        $data_attach['file_name'],
                        file_get_contents($request->file('file')->getRealPath())
                    );
                }

                $objPurchase->addPurchaseAttachment($data_attach);
                // $serviceTicket->addTicketAttachment($data_attach);
            }

            $objPurchase->save();
            if ($objPurchase->include_taxes == true) {
                foreach ($request->tax_percentage as $key => $v) {
                    if ($v != 0 && $v != null) {
                        $PurchaseTaxElement = PurchaseTaxElement::where('purchase_id', $objPurchase->id)->where('tax_element_id', $key)->first();
                        if ($PurchaseTaxElement) {
                            // If exists, update the PurchaseTaxElement
                            $PurchaseTaxElement->tax_defination_id = (int) $request->tax_id;
                            $PurchaseTaxElement->value = $v;
                            if ($objPurchase->qty) {
                                $PurchaseTaxElement->amount = round((($objPurchase->bill_amount * $v) / 100) * $objPurchase->qty, 2);
                            } else {
                                $PurchaseTaxElement->amount = round(($objPurchase->bill_amount * $v) / 100, 2);
                            }

                            if (!$PurchaseTaxElement->save()) {
                                return response()->json($return);
                            }
                        } else {
                            // If does not exist, create a new PurchaseTaxElement
                            $newPurchaseTaxElement = new PurchaseTaxElement();
                            $newPurchaseTaxElement->purchase_id = $objPurchase->id;
                            $newPurchaseTaxElement->tax_element_id = $key;
                            $newPurchaseTaxElement->tax_defination_id = (int) $request->tax_id;
                            $newPurchaseTaxElement->value = $v;
                            if ($objPurchase->qty) {
                                $newPurchaseTaxElement->amount = round((($objPurchase->bill_amount * $v) / 100) * $objPurchase->qty, 2);
                            } else {
                                $newPurchaseTaxElement->amount = round(($objPurchase->bill_amount * $v) / 100, 2);
                            }
                            if (!$newPurchaseTaxElement->save()) {
                                return response()->json($return);
                            }
                        }
                    }
                }
            }

            $return['msg'] = 'Purchase has been updated successfully';
            $return['id'] = $objPurchase->id;
            $return['status'] = 'success';
            return response()->json($return);
        }
    }

    public function getPurchaseDetail($id, Request $request)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseEdit')) {
            return response()->json($return);
        }
        $record = Purchase::where('id', '=', $id)->with('attachments')->first();
        if (empty($record))
            return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);

        if (!Company::checkUserAccess($record))
            return response()->json(['status' => 'error', 'msg' => 'Insufficient permission for this purchase !!']);

        $record->invoice_date = CommonHelper::getDateAs($record->invoice_date, 'd/m/Y', 'Y-m-d');
        $record->received_date = CommonHelper::getDateAs($record->received_date, 'd/m/Y', 'Y-m-d');
        $dev['dropdown'] = array();
        if ($record->company_id) {
            $getCompany = Company::where('id', $record->company_id)->select('id', 'name as text')->first();
            $dev['dropdown']['company'] = $getCompany && $getCompany->exists ? $getCompany->toArray() : null;
        }
        if ($record->location_id) {
            $getLocation = Location::where('id', $record->location_id)->select('id', 'name as text')->first();
            $dev['dropdown']['location'] = $getLocation && $getLocation->exists ? $getLocation->toArray() : null;
        }
        if ($record->tax_id) {
            $getTax = TaxDefination::where('id', $record->tax_id)->select('id', 'tax_name as text')->first();
            $dev['dropdown']['tax'] = $getTax && $getTax->exists ? $getTax->toArray() : null;
        }
        if ($record->tax_id) {
            $getTaxElement = PurchaseTaxElement::where('tax_defination_id', $record->tax_id)
                ->where('purchase_id', $record->id)
                ->select('tax_element_id', 'value')
                ->get();

            $taxArray = [];
            foreach ($getTaxElement as $taxElement) {
                $tax = TaxElements::where('id', $taxElement->tax_element_id)
                    ->select('tax_element AS text')
                    ->first();

                if ($tax) {
                    $taxArray[$tax->text] = $taxElement->value;
                }
            }
            $dev['dropdown']['taxElements'] = !empty($taxArray) ? $taxArray : null;
        }

        if ($record->supplier_id) {
            $getSupplier = Supplier::where('id', $record->supplier_id)->select('id', 'name as text')->first();
            $dev['dropdown']['supplier'] = $getSupplier && $getSupplier->exists ? $getSupplier->toArray() : null;
        }
        if ($record->currency) {
            $getCurrency = Currency::getCurrencies();
            $currencyKey = $record->currency;
            if (array_key_exists($currencyKey, $getCurrency)) {
                $currencyData = $getCurrency[$currencyKey];
                $concatenatedText = $currencyData['name'] . ' ' . $currencyData['symbol'];
                $filteredCurrency = [
                    'id' => $currencyKey,
                    'text' => $concatenatedText,
                ];
                $dev['dropdown']['currency'] = $filteredCurrency;
            } else {
                $dev['dropdown']['currency'] = null;
            }
        }
        $status = [
            '0' => 'Not Yet Received',
            '1' => 'Fully Received',
            '2' => 'Partially Received',
            '3' => 'Provision',
        ];
        if ($record->fully_received) {
            $statusKey = $record->fully_received;
            if (array_key_exists($statusKey, $status)) {
                $statusData = $status[$statusKey];
                $filteredCurrency = [
                    'id' => $statusKey,
                    'text' => $statusData,
                ];
                $dev['dropdown']['fully_received'] = $filteredCurrency;
            } else {
                $dev['dropdown']['fully_received'] = null;
            }
        } else {
            $statusKey = 0;
            if (array_key_exists($statusKey, $status)) {
                $statusData = $status[$statusKey];
                $filteredCurrency = [
                    'id' => $statusKey,
                    'text' => $statusData,
                ];
                $dev['dropdown']['fully_received'] = $filteredCurrency;
            } else {
                $dev['dropdown']['fully_received'] = null;
            }
        }

        $record->dev = $dev;
        $return['status'] = 'success';
        $return['data'][] = $record;
        return response()->json($return);
    }

    public function deletePurchase($id)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseDelete')) {
            return response()->json($return);
        }
        // $splr = Supplier::where('id','=',$id)->withCount(['company','supplier','licenses']);
        $record = Purchase::where('id', '=', $id)->withCount(['assets', 'accessories', 'consumables', 'license', 'components', 'interact_caches', 'interact_records', 'pu_attach'])->first();

        if (empty($record))
            return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);

        if (!Company::checkUserAccess($record))
            return response()->json(['status' => 'error', 'msg' => 'Insufficient permission for this purchase !!']);
        if ($record->assets_count)
            return response()->json(['status' => 'error', 'msg' => 'Some assets is attached with this purchase. Please unlink them and try again !!']);
        if ($record->accessories_count)
            return response()->json(['status' => 'error', 'msg' => 'Some accessories is attached with this purchase. Please unlink them and try again !!']);
        if ($record->consumables_count)
            return response()->json(['status' => 'error', 'msg' => 'Some consumables is attached with this purchase. Please unlink them and try again !!']);
        if ($record->license_count)
            return response()->json(['status' => 'error', 'msg' => 'Some licenses is attached with this purchase. Please unlink them and try again !!']);
        if ($record->components_count)
            return response()->json(['status' => 'error', 'msg' => 'Some components is attached with this purchase. Please unlink them and try again !!']);
        if ($record->interact_caches_count)
            return response()->json(['status' => 'error', 'msg' => 'Some assets is attached with this purchase. Please unlink them and try again !!']);
        if ($record->interact_records_count)
            return response()->json(['status' => 'error', 'msg' => 'Some asset records is attached with this purchase. Please unlink them and try again !!']);
        if ($record->pu_attach_count)
            return response()->json(['status' => 'error', 'msg' => 'Some purchase attachments is attached with this purchase. Please unlink them and try again !!']);
        $record->delete();
        return response()->json(['status' => 'success', 'msg' => 'Purchase has been deleted successfully!']);
    }

    public function attachmentDownload($id)
    {
        $objPurchase = new Purchase;
        $dtl = $objPurchase->getAttachmentDetail($id);

        if (false) {  // check for valid access
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        // print_r($dtl[0]->file_name);die;
        // echo $objSupplier->image;die;

        $phisical_path = storage_path('purchase') . '/' . $dtl[0]->file_name;
        // echo $phisical_path;exit;

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $dtl[0]->original_file_name . '"');

        echo file_get_contents($phisical_path);
    }

    public function attachmentDelete($id)
    {  // discuss and avoid file deletion and implementation of soft delete

        $dtl = PurchaseAttachment::where('file_name', '=', $id)->first();
        $purchase = Purchase::where('id', '=', $dtl->po_id)->first();

        $history = json_decode($purchase->history, true);
        $entryArray = ['entry_at' => date('Y-m-d H:i:s'), 'user_id' => Auth::user()->id, 'action' => 'deleted', 'action_detail' => 'Deleted (' . $dtl->original_file_name . ')', 'referance' => 'purchase_docs', 'id' => $dtl->id];

        $history[] = json_encode($entryArray);
        $purchase->history = json_encode($history);
        $purchase->save();
        // return $dtl;
        if (empty($dtl)) {  // check for valid access
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        // Storage::disk('purchase')->delete($dtl->file_name);
        $dtl->delete();

        $return['msg'] = 'Purchase attachment has been deleted successfully!';
        $return['status'] = 'success';

        return response()->json($return);
    }

    public function getPurchaseHistory($id)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseHistory')) {
            return response()->json($return);
        }
        $history = Purchase::where('id', '=', $id)->first();
        if (empty($history))
            return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);

        $data = collect(json_decode($history->history, false))
            ->map(function ($value) {
                if (is_string($value)) {
                    return json_decode($value, true);
                }
                return (array) $value;
            })
            ->sortByDesc('entry_at')
            ->values();

        $history->history = json_encode($data);
        return View('purchases.purchase-history')->with(compact('history'));
    }

    /* To show the list of options for the Purchase References field */
    public function getListOptions()
    {
        $db = DB::table('purchases as pur');
        $db->addSelect('id', DB::raw('concat_ws(" - ", pur.invoice_no, date_format(pur.invoice_date, "%d/%m/%Y")) as text'));
        $result = $db->get();
        $return = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    /* options by query */
    public function getInvoiceByQuery(Request $request)
    {
        $return = array();
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $skip = (($page * 20) - 20);

        $db = DB::table('purchases as pur')->select('pur.id', DB::raw('concat_ws(" - ", pur.invoice_no, date_format(pur.invoice_date, "%d/%m/%Y")) as text'), DB::raw('date_format(pur.invoice_date, "%d/%m/%Y") as invoice_date'), 'sup.name as supplier_name', 'sup.id as supplier_id', 'pur.invoice_no as order_number', 'pur.po_number', 'pur.bill_amount as purchase_cost', 'pur.currency');
        $db->leftJoin('suppliers as sup', 'pur.supplier_id', 'sup.id');
        $db->whereNull('pur.deleted_at');
        if ($search) {
            $db->whereRaw("concat_ws(' - ', pur.invoice_no, date_format(pur.invoice_date, '%d/%m/%Y')) like '%" . $search . "%'");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
        $return['results'] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function userDetail($id)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseView')) {
            return redirect('dashboard')->with('msg', $return);
        }
        // $units = Unit::select("id", "name as text")->get()->toArray();
        $purchase = Purchase::where('id', '=', $id)->first();
        return view('purchases.info.index')->with('purchase', $purchase);
        // ->with('units', $units);
    }

    public function getUnitsByQuery(Request $request)
    {
        $return = array();
        $search = $request->input('term', '') ? $request->input('term', '') : $request->input('search', '');
        $page = $request->input('page', 1);
        $skip = (($page * 20) - 20);

        $db = DB::table('procure_units')->select('id', 'name as text');
        if ($search) {
            $db->whereRaw("name like '%" . $search . "%'");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
        $return['results'] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    /* purchase info */
    public function info(Request $request, $id)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('PurchaseView')) {
            return response()->json($return);
        }
        $vd = new stdClass;

        $purchase = Purchase::where('id', '=', $id)->first();

        $getTaxElement = PurchaseTaxElement::where('tax_defination_id', $purchase->tax_id)
            ->where('purchase_id', $purchase->id)
            ->select('tax_element_id', 'value', 'amount')
            ->get();
        $taxElementArray = [];
        $totalNetAmount = 0;
        $totalTaxAmount = 0;

        // Calculate the price per unit
        $pricePerUnit = $purchase->bill_amount / ($purchase->qty ?? 1);
        $totalNetAmount = $pricePerUnit * ($purchase->qty ?? 1);
        foreach ($getTaxElement as $taxElement) {
            $tax = TaxElements::where('id', $taxElement->tax_element_id)
                ->select('tax_element AS text')
                ->first();

            if ($tax) {
                $taxAmount = ($totalNetAmount * $taxElement->value) / 100;
                $taxLabel = $tax->text . ' (' . $taxElement->value . '%)';
                $taxElementArray[$taxLabel] = round($taxAmount, 2);
                $totalTaxAmount += $taxAmount;
            }
        }
        $totalAmount = $totalNetAmount + $totalTaxAmount;
        $totalAmount = number_format((float) $totalAmount, 2, '.', '');

        return view('purchases.info.info_tab')->with('purchase', $purchase)->with('vd', $vd)->with('taxElementArray', $taxElementArray)->with('totalAmount', $totalAmount);
    }

    // public function purchaseImport(Request $request) {
    //     $return = ["msg"=>"Unable to import the given purchases", "status"=>"danger"];
    //     if( $request->isMethod('post') && !$request->import_file ) {
    //         $return["msg"] = trans('content.download_format.error_place_upload');
    //         $request->session()->flash("msg", $return);
    //     } else {
    //         if(! $request->isMethod('post')) {
    //             return view("purchases.import");
    //         }
    //         if( $request->isMethod('post') && $request->import_file ) {
    //             $path = $request->file('import_file')->getRealPath();
    //             $import = new PurchasesImport($request);
    //             $var = Excel::import($import, $request->import_file);
    //             $response = $import->data;
    //             return view("purchases.import")->with([ 'fail' => isset($response['fail']) ? $response['fail'] : [], 'fail_msgs' => isset($response['fail_msgs']) ? $response['fail_msgs'] : [],'success' => isset($response['success']) ? $response['success'] : [] ]);
    //         }
    //     }
    //     return view('purchases.import');
    // }

    public function purchaseImport(Request $request)
    {
        $return = ['msg' => 'Unable to import the given purchases', 'status' => 'danger'];
        if ($request->isMethod('post') && !$request->import_file) {
            $return['msg'] = trans('content.download_format.error_place_upload');
            return redirect()->back()->with('msg', $return);
        }
        if (!$request->isMethod('post')) {
            return view('purchases.import');
        }
        if ($request->isMethod('post') && $request->import_file) {
            try {
                $import = new PurchasesImport($request);
                Excel::import($import, $request->import_file);
                $response = $import->data;
                return redirect()->back()->with([
                    'fail' => isset($response['fail']) ? $response['fail'] : [],
                    'fail_msgs' => isset($response['fail_msgs']) ? $response['fail_msgs'] : [],
                    'success' => isset($response['success']) ? $response['success'] : []
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with([
                    'msg' => [
                        'msg' => $e->getMessage(),
                        'status' => 'danger'
                    ]
                ]);
            }
        }
        return view('purchases.import');
    }

    public function savePurchaseItem(Request $request)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to add the Purchase Item '
        ];
        $data = $request->only('name', 'qty', 'units', 'price', 'description', 'purchase_id');
        $rules = [
            'name' => 'required|string|max:2000|clean_text_only',
            'units' => 'required|exists:procure_units,id',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'description' => 'nullable|string|max:2000',
            'purchase_id' => 'required|exists:purchases,id'
        ];
        $messages = [];
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
        } else {
            $obj = new PurchaseItem;
            $obj->fill($data);
            if ($obj->save()) {
                $return['msg'] = 'Purchase Item has been added successfully';
                $return['status'] = 'success';
            }
        }

        return response()->json($return);
    }

    public function ajaxPurchaseItem(Request $request)
    {
        $req = $request->all();
        $return = array(
            'draw' => date('is')
        );

        $fields = array(
            '1' => 'p.name',
            '2' => 'p.qty',
            '3' => 'p.units',
            '4' => 'p.price',
            '5' => 'p.description'
        );

        $db = DB::table('purchase_items as p');
        $db->leftJoin('procure_units as pu', 'pu.id', '=', 'p.units');
        $db->select(
            'p.id',
            'p.name',
            'p.qty',
            'p.price',
            'pu.name as units',
        );
        $db->addSelect(DB::raw('p.qty * p.price AS total_price'));
        $db->addSelect(DB::raw('DATE_FORMAT(p.updated_at, "%d %b %Y %h:%i %p") as updated_at'));
        $db->where('p.purchase_id', '=', $request->purchase_id);

        $return['recordsTotal'] = $db->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req['search']['value']) && $search_key = trim($req['search']['value'])) {
            $whereStr = sprintf('(p.name like "%%%1$s%%" or p.qty like "%%%1$s%%" or p.price like "%%%1$s%%" or pu.name like "%%%1$s%%" or DATE_FORMAT(p.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
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

    public function ajaxGetPurchaseItem(Request $request, $id)
    {
        $return = [
            'status' => 'error',
            'msg' => 'Unable to get the Item'
        ];

        $obj = '';
        try {
            $obj = PurchaseItem::findOrFail($id);
            $dev['dropdown'] = array();
            if ($obj->units) {
                $getUnits = Unit::where('id', $obj->units)->select('id', 'name as text')->first();
                $dev['dropdown']['units'] = $getUnits && $getUnits->exists ? $getUnits->toArray() : null;
            }
            $data = [];
            $data['data'] = $obj->only('id', 'name', 'qty', 'price', 'description');
            $data['data']['dev'] = $dev['dropdown']['units'];
            $return['data'] = $data;
            $return['msg'] = null;
            $return['status'] = 'success';
        } catch (\Exception $e) {
            return response()->json($return);
        }

        return response()->json($return);
    }

    public function ajaxEditPurchaseItem(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to edit the Purchase Item'
        ];

        $obj = '';
        try {
            $obj = PurchaseItem::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json($return);
        }

        $data = $request->only('name', 'qty', 'units', 'price', 'description', 'purchase_id');
        $rules = [
            'name' => 'required|string|max:2000|clean_text_only',
            'units' => 'required|exists:procure_units,id',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'description' => 'nullable|string|max:2000',
            'purchase_id' => 'required|exists:purchases,id'
        ];
        $messages = [];
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return['msg'] = $e[0];
            return response()->json($return);
        }

        $obj->fill($data);
        if ($obj->save()) {
            $return['msg'] = 'Purchase Item has been updated successfully';
            $return['status'] = 'success';
        }

        return response()->json($return);
    }

    public function ajaxPurchaseItemDelete(Request $request, $id)
    {
        $return = ['status' => 'failure', 'msg' => 'Unable to delete the Item'];
        $obj = '';
        try {
            $obj = PurchaseItem::findOrFail($id);
            $obj->delete();
            $return['status'] = 'success';
            $return['msg'] = 'Purchase Item has been deleted successfully!';
            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($return);
        }
    }
}

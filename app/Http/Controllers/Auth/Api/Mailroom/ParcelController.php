<?php

namespace App\Http\Controllers\Auth\Api\Mailroom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Common as CommonHelper;
use App\Mail\MailroomManagement\UpdateParcelStatus;
use App\Models\CustomField;
use App\Models\Mailroom\Parcel;
use App\Models\Mailroom\Site;
use App\Models\Settings;
use App\Models\Mailroom\MailRoomDelegation;
use App\Models\User;
use App\Models\Mailroom\Config;
use DB, Log, Mail, Auth;

class ParcelController extends Controller
{
    public function getParcelList(Request $request)
    {
        $isAdmin = Auth::user()->hasAnyRole(['SuperAdmin','Admin']);
        if (!$isAdmin) {
            $permissionCheck = CommonHelper::checkMailroomPermissionOrRedirect('MailroomParcelsView','api');
            if ($permissionCheck !== true) {
                return $permissionCheck;
            }
        }
        $return = [
            "status" => "fail",
            "msg" => trans('mailroom.unable_to_display'),
            "data" => [],
            "total" => 0,
            "filtered" => 0,
            "current_index" => 0,
            "is_prev_index" => 0,
            "is_next_index" => 0,
            "page" => 0
        ];

        try {
            $req = $request->all();
            $page = (int)($req['page'] ?? 0);
            $length = (int)($req['length'] ?? 10);
            $start = $page * $length;

            $fields = [
                '1' => 'p.id',
                '2' => 'p.parcel_tag',
                '3' => 'st.name',
                '4' => 'p.courier_number',
                '5' => 'p.courier_type',
                '6' => 'p.courier_mode',
                '7' => 'ms.status_name',
                '8' => 'p.created_at',
                '9' => 'p.updated_at',
            ];

            $query = DB::table('mailroom_parcels as p')
                ->leftJoin('mailroom_site as st', 'p.site_id', '=', 'st.id')
                ->leftJoin('mailroom_status as ms', 'p.status_id', '=', 'ms.id')
                ->leftJoin('suppliers as sup', 'p.courier_person_id', '=', 'sup.id')
                ->select(
                    'p.id', 'p.parcel_tag', 'p.courier_number', 'p.total_charges', 'p.weight',
                    'p.charges', 'ms.status_name as status', 'ms.color_code as status_color',
                    'ms.id as status_id','st.id as site_id', 'sup.name as suplier', 'st.name as site',

                    DB::raw('CASE WHEN p.flow = 1 THEN "Incoming" WHEN p.flow = 2 THEN "Outgoing" ELSE "Unknown" END AS flow'),
                    DB::raw('CASE WHEN p.document_type = 1 THEN "Document" WHEN p.document_type = 2 THEN "Nondocument" ELSE "Unknown" END AS document_type'),
                    DB::raw('CASE WHEN p.courier_type = 1 THEN "Personal" WHEN p.courier_type = 2 THEN "Official" ELSE "Unknown" END AS courier_type'),
                    DB::raw('CASE WHEN p.courier_mode = 1 THEN "Domestic" WHEN p.courier_mode = 2 THEN "Intrasite" WHEN p.courier_mode = 3 THEN "International" ELSE "Unknown" END AS courier_mode'),
                    DB::raw('CASE WHEN p.receive_type = 1 THEN "Government" WHEN p.receive_type = 2 THEN "Non-Government" ELSE "Unknown" END AS receive_type'),
                    DB::raw('CASE 
                        WHEN p.transaction_mode = 1 THEN "Credit Card" 
                        WHEN p.transaction_mode = 2 THEN "Debit Card"  
                        WHEN p.transaction_mode = 3 THEN "Paypal"  
                        WHEN p.transaction_mode = 4 THEN "Bank Transfer" 
                        WHEN p.transaction_mode = 5 THEN "Cash" 
                        WHEN p.transaction_mode = 6 THEN "UPI" 
                        ELSE "Unknown" END AS transaction_mode'),
                    DB::raw('DATE_FORMAT(p.created_at, "%d %b %Y %h:%i %p") as created_at_format'),
                    DB::raw('DATE_FORMAT(p.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'),

                    DB::raw('(SELECT sender_name FROM mailroom_parcel_sender WHERE parcel_id = p.id LIMIT 1) as sender_name'),
                    DB::raw('(SELECT sender_phone FROM mailroom_parcel_sender WHERE parcel_id = p.id LIMIT 1) as sender_phone'),
                    DB::raw('(SELECT sender_email FROM mailroom_parcel_sender WHERE parcel_id = p.id LIMIT 1) as sender_email'),
                    DB::raw('(SELECT sender_address FROM mailroom_parcel_sender WHERE parcel_id = p.id LIMIT 1) as sender_address'),

                    DB::raw('(SELECT receiver_name FROM mailroom_parcel_receiver WHERE parcel_id = p.id LIMIT 1) as receiver_name'),
                    DB::raw('(SELECT receiver_phone FROM mailroom_parcel_receiver WHERE parcel_id = p.id LIMIT 1) as receiver_phone'),
                    DB::raw('(SELECT receiver_email FROM mailroom_parcel_receiver WHERE parcel_id = p.id LIMIT 1) as receiver_email'),
                    DB::raw('(SELECT receiver_address FROM mailroom_parcel_receiver WHERE parcel_id = p.id LIMIT 1) as receiver_address')
                );
            
            $mailroomUserPrivileges  = CommonHelper::getMailroomUserPermissions();
            if(!$isAdmin && !empty($mailroomUserPrivileges['site_ids'])){
               $extraSiteIds = $mailroomUserPrivileges['auth_site_head_ids'];
                $siteIds = $mailroomUserPrivileges['site_ids'];
                $extraSiteIds = is_array($extraSiteIds) ? $extraSiteIds : [$extraSiteIds];
                foreach ($extraSiteIds as $extraSiteId) {
                    if (!in_array($extraSiteId, $siteIds)) {
                        $siteIds[] = $extraSiteId;
                    }
                }
                $query->whereIn("st.id", $siteIds);
            } 

            $return['total'] = $query->count();
            
            if(isset($request->status)) {
                if($request->status == "Incoming") {
                    $query->where("p.flow", 1);
                }elseif($request->status == "Outgoing") {
                    $query->where("p.flow", 2);
                }else{
                    if($request->status != 0) {
                        $query->where("p.status_id", $request->status);
                    }
                }
            }

            // Filters
            if (!empty($req["filters"])) {
                $filters = $req["filters"];

                if (!empty($filters["courier_type"])) {
                    $query->where("p.courier_type", (int)$filters["courier_type"]);
                }
                if (!empty($filters["courier_mode"])) {
                    $query->where("p.courier_mode", (int)$filters["courier_mode"]);
                }
                if (!empty($filters["status"][0])) {
                    $statusIds = array_map('intval', explode(',', $filters["status"][0]));
                    $query->whereIn("p.status_id",$statusIds);
                }
                if (!empty($filters["site_head"][0])) {
                    $siteHeadIds = array_map('intval', explode(',', $filters["site_head"][0]));
                    $query->whereIn("p.courier_person_id", $siteHeadIds);
                }
                $based_on_possible = ['1' => 'p.created_at', '2' => 'p.updated_at'];
                if (!empty($filters["based_on"]) && array_key_exists($filters["based_on"], $based_on_possible)) {
                    if (!empty($filters["daterange"])) {
                        $daterange = explode(" - ", $filters["daterange"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        $query->whereBetween($based_on_possible[$filters["based_on"]], [$from_date, $to_date]);
                    }
                }
                if (!empty($filters["parcel_type"][0])) {
                    $parcelTypes = array_map('intval', explode(',', $filters["parcel_type"][0]));
                    $query->whereIn("p.flow", $parcelTypes);
                }
                if (!empty($filters["document_type"][0])) {
                    $docTypes = array_map('intval', explode(',', $filters["document_type"][0]));
                    $query->whereIn("p.document_type", $docTypes);
                }
                if (!empty($filters["site"])) {
                    $siteIds = array_map('intval', is_array($filters["site"]) ? $filters["site"] : explode(',', $filters["site"]));
                    $query->whereIn("p.site_id", $siteIds);
                }
            }

            // Search
            if (!empty($req["search"]["value"])) {
                $search = $req["search"]["value"];
                $query->where(function ($q) use ($search) {
                    $q->where('p.parcel_tag', 'like', "%$search%")
                        ->orWhere('p.courier_number', 'like', "%$search%")
                        ->orWhere('st.name', 'like', "%$search%")
                        ->orWhere(DB::raw('(SELECT sender_name FROM mailroom_parcel_sender WHERE parcel_id = p.id LIMIT 1)'), 'like', "%$search%")
                        ->orWhere(DB::raw('(SELECT receiver_name FROM mailroom_parcel_receiver WHERE parcel_id = p.id LIMIT 1)'), 'like', "%$search%");
                });
            }

            $return['filtered'] = $query->count();
            $totalPages = ceil($return['filtered'] / $length);

            // Order
            if (!empty($req["order"][0]["column"]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
                $colIndex = $req["order"][0]["column"];
                if (isset($fields[$colIndex])) {
                    $query->orderByRaw($fields[$colIndex] . ' ' . $req["order"][0]["dir"]);
                }
            }
            
            // Pagination
            $query->skip($start)->take($length);

            $parcels = $query->get();

            $return['data'] = [];
            foreach ($parcels as $p) {
                $return['data'][] = [
                    'id' => $p->id,
                    'parcel_tag' => $p->parcel_tag,
                    'site_name' => $p->site,
                    'site_id' => $p->site_id,
                    'flow' => $p->flow,
                    'courier_number' => $p->courier_number,
                    'courier_type' => $p->courier_type,
                    'courier_mode' => $p->courier_mode,
                    'status' => $p->status,
                    'status_id' => $p->status_id,
                    'status_color' => $p->status_color,
                    'supplier' => $p->suplier,
                    'weight' => $p->weight,
                    'charges' => $p->charges,
                    'total_charges' => $p->total_charges,
                    'sender_name' => $p->sender_name,
                    'sender_phone' => $p->sender_phone,
                    'sender_email' => $p->sender_email,
                    'sender_address' => $p->sender_address,
                    'receiver_name' => $p->receiver_name,
                    'receiver_phone' => $p->receiver_phone,
                    'receiver_email' => $p->receiver_email,
                    'receiver_address' => $p->receiver_address,
                    'receive_type' => $p->receive_type,
                    'transaction_mode' => $p->transaction_mode,
                    'created_at' => $p->created_at_format,
                    'updated_at' => $p->updated_at_format,
                ];
            }

            $return['current_index'] = $page;
            $return['is_prev_index'] = $page > 0 ? 1 : 0;
            $return['is_next_index'] = ($page + 1) < $totalPages ? 1 : 0;
            $return['page'] = $page;
            $return['status'] = 'success';
            $return['msg'] = trans('mailroom.records_fetched');
            
        } catch (\Exception $e) {
            Log::error('Parcel list fetch error: ' . $e->getMessage());
            $return['msg'] = 'Failed to fetch parcel list.';
        }

        return response()->json($return);
    }

    public function parcelInfo($id) {
        $return = [
            'status' => 'failure',
            'msg'    => 'Unable to view parcel info'
        ];
        $permissionCheck = CommonHelper::checkMailroomPermissionOrRedirect('MailroomParcelsViewInfo','api');
        if ($permissionCheck !== true) {
           return $permissionCheck;
        }

        try {
            $parcel = DB::table('mailroom_parcels as p')
                ->leftJoin('mailroom_site as st', 'p.site_id', '=', 'st.id')
                ->leftJoin('mailroom_status as ms', 'p.status_id', '=', 'ms.id')
                ->leftJoin('suppliers as sup', 'p.courier_person_id', '=', 'sup.id')
                ->select(
                    'p.id', 'p.parcel_tag', 'p.courier_number','p.total_charges', 'p.weight',
                    'p.charges', 'ms.status_name as status', 'ms.color_code as status_color',
                    'ms.id as status_id', 'sup.name as suplier', 'st.name as site', 'p.transaction_id', 'p.mrr', 'p.tpn',

                    DB::raw('CASE WHEN p.flow = 1 THEN "Incoming" WHEN p.flow = 2 THEN "Outgoing" ELSE "Unknown" END AS flow'),
                    DB::raw('CASE WHEN p.document_type = 1 THEN "Document" WHEN p.document_type = 2 THEN "Nondocument" ELSE "Unknown" END AS document_type'),
                    DB::raw('CASE WHEN p.courier_type = 1 THEN "Personal" WHEN p.courier_type = 2 THEN "Official" ELSE "Unknown" END AS courier_type'),
                    DB::raw('CASE WHEN p.courier_mode = 1 THEN "Domestic" WHEN p.courier_mode = 2 THEN "Intrasite" WHEN p.courier_mode = 3 THEN "International" ELSE "Unknown" END AS courier_mode'),
                    DB::raw('CASE WHEN p.receive_type = 1 THEN "Government" WHEN p.receive_type = 2 THEN "Non-Government" ELSE "Unknown" END AS receive_type'),
                    DB::raw('CASE 
                        WHEN p.transaction_mode = 1 THEN "Credit Card" 
                        WHEN p.transaction_mode = 2 THEN "Debit Card"  
                        WHEN p.transaction_mode = 3 THEN "Paypal"  
                        WHEN p.transaction_mode = 4 THEN "Bank Transfer" 
                        WHEN p.transaction_mode = 5 THEN "Cash" 
                        WHEN p.transaction_mode = 6 THEN "UPI" 
                        ELSE "Unknown" END AS transaction_mode'),
                    DB::raw('DATE_FORMAT(p.created_at, "%d %b %Y %h:%i %p") as created_at_format'),
                    DB::raw('DATE_FORMAT(p.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'),

                    DB::raw('(SELECT sender_name FROM mailroom_parcel_sender WHERE parcel_id = p.id LIMIT 1) as sender_name'),
                    DB::raw('(SELECT sender_phone FROM mailroom_parcel_sender WHERE parcel_id = p.id LIMIT 1) as sender_phone'),
                    DB::raw('(SELECT sender_email FROM mailroom_parcel_sender WHERE parcel_id = p.id LIMIT 1) as sender_email'),
                    DB::raw('(SELECT sender_address FROM mailroom_parcel_sender WHERE parcel_id = p.id LIMIT 1) as sender_address'),

                    DB::raw('(SELECT receiver_name FROM mailroom_parcel_receiver WHERE parcel_id = p.id LIMIT 1) as receiver_name'),
                    DB::raw('(SELECT receiver_phone FROM mailroom_parcel_receiver WHERE parcel_id = p.id LIMIT 1) as receiver_phone'),
                    DB::raw('(SELECT receiver_email FROM mailroom_parcel_receiver WHERE parcel_id = p.id LIMIT 1) as receiver_email'),
                    DB::raw('(SELECT receiver_address FROM mailroom_parcel_receiver WHERE parcel_id = p.id LIMIT 1) as receiver_address')
                )
                ->where('p.id', $id)
                ->first();
                $customFieldsFromTable = CommonHelper::getCustomFieldsValues('parcel_history', $parcel->id);

            if (!$parcel) {
                return redirect()->back()->with('error', trans('mailroom.parcel_not_found'));
            }

            $return['parcel'] = $parcel;
            $return['customFieldsFromTable'] = $customFieldsFromTable;
            $return['msg'] = 'Data Fetch';
            return response()->json($return);

        } catch (\Exception $e) {
            Log::error('Parcel Info Fetch Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'parcel_id' => $id ?? null
            ]);

            return redirect()->back()->with('error', trans('mailroom.unable_to_display'));
        }
    }

    public function updateParcelStatus(Request $request)
    {
        $return = [
            'status' => 'failure',
            'msg'    => 'Unable to update the Parcel'
        ];
        $permissionCheck = CommonHelper::checkMailroomPermissionOrRedirect('MailroomParcelsUpdateStatus','api');
        if ($permissionCheck !== true) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }

        DB::beginTransaction();
        try {
            $request->validate([
                'id'     => 'required|exists:mailroom_parcels,id',
                'status' => 'required|integer',
            ]);

            $parcel = Parcel::with('site')->find($request->id);

            if ($parcel->status_id == $request->status) {
                return response()->json([
                    'status' => 'failure',
                    'msg'    => 'This status is already assigned to the parcel.'
                ]);
            }

            $oldParcel = $parcel->replicate();
            $parcel->status_id = $request->status;
            $parcel->save();

            $dataHistory = [
                'parcel_id'   => $parcel->id,
                'updated_by'  => Auth::id(),
                'old_status'  => $oldParcel->status_id,
                'action_type' => 3,
            ];
            CommonHelper::parcelHistory($dataHistory, $parcel);

            if (config('mail.service_enabled')) {
                $site = Site::with('sitehead')->find($parcel->site_id);
                $siteHeadEmail = optional($site->sitehead)->email;
                $sender = json_decode($parcel->sender);
                $receiver = json_decode($parcel->receiver);

                $cc_emails = [];
                if (!empty($sender->sender_email)) $cc_emails[] = $sender->sender_email;
                if (!empty($receiver->receiver_email)) $cc_emails[] = $receiver->receiver_email;
                $alerts_enabled = optional(Settings::first())->alerts_enabled == 1;
                if ($alerts_enabled) {
                    $globalAlertEmails = CommonHelper::getGlobalAlertEmail();
                    if (is_array($globalAlertEmails)) {
                        $cc_emails = array_merge($cc_emails, $globalAlertEmails);
                    }
                }
                $cc_emails = array_unique(array_filter($cc_emails));
                
                // Send the email
                if ($siteHeadEmail) {
                   Mail::to($siteHeadEmail)->cc($cc_emails)->queue(new UpdateParcelStatus($parcel,$oldParcel->status_id,$parcel->status_id,Auth::user()->getGuranteedNameText()));
                }
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'msg'    => 'Parcel status updated successfully.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error in updateParcelStatus', ['errors' => $e->errors(),'input'  => $request->all(),]);
            return response()->json([
                'status' => 'error',
                'msg'    => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception in updateParcelStatus', ['message' => $e->getMessage(),'trace'   => $e->getTraceAsString(),'input'   => $request->all(),]);
            return response()->json([
                'status' => 'error',
                'msg'    => 'An error occurred while updating the parcel.',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function getMailroomCustomFields(Request $request) {
        $return = [
            'status' => 'fail',
            'msg' => 'Unable to get Mailroom custom fields',
            'data' => []
        ];

        try {
            $customFieldSet = $request->input('customFieldSet');

            if (!is_array($customFieldSet)) {
                $customFieldSet = explode(',', $customFieldSet);
            }

            $fieldsCollection = DB::table('custom_field_custom_fieldset')->whereIn('custom_fieldset_id', $customFieldSet)->orderBy('order', 'ASC')->get();

            $fieldsArray = [];
            foreach ($fieldsCollection as $value) {
                $field = CustomField::find($value->custom_field_id);
                if (!$field) continue;

                if ($field->custom_options) {
                    $field->custom_options = json_decode($field->custom_options);
                }
                $field->required = isset($value->required) && $value->required == 1 ? 1 : 0;
                $field->label = $field->name;
                $field->name = $field->nameToColumn();
                $field->code_name = "fields[" . $field->name . "]";
                $fieldsArray[] = $field;
            }

            $return['status'] = 'success';
            $return['msg'] = 'Fields fetched successfully';
            $return['parcel_data'] = [
                'parcel_id'=>$request->parcel_id,
                'status_id'=>$request->status_id
            ];
            $return['data'] = $fieldsArray;

        } catch (\Exception $e) {
            Log::error("getMailroomCustomFields error: " . $e->getMessage());
        }

        return response()->json($return);
    }

    public function mailroomCustomForm(Request $request)
    {
        if((isset($request->action_type) && $request->action_type == 'bulk') && isset($request->batch_id)){
            return $this->bulkBatchParcelStatusUpdate($request);
        }else{
            DB::beginTransaction();
            try {
                $request->validate([
                    'parcel_id'    => 'required|exists:mailroom_parcels,id',
                    'status_id'    => 'required|integer',
                    'fields'       => 'nullable|array',
                    'proof_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
    
                $parcel = Parcel::find($request->parcel_id);
                if (!$parcel) {
                    Log::warning('Parcel not found with ID: ' . $request->parcel_id);
                    return response()->json([
                        'msg' => 'Parcel not found.',
                        'status' => 'error',
                    ], 404);
                }
    
                if ($parcel->status_id == $request->status_id) {
                    return response()->json([
                        'msg' => 'This status is already assigned to the parcel.',
                        'status' => 'error',
                    ], 400);
                }
                $oldParcel = $parcel->replicate();
                $signature = $request->input('signature_image');
                if ($signature) {
                    $image_parts = explode(";base64,", $signature);
                    if (count($image_parts) === 2) {
                        $image_type_aux = explode("image/", $image_parts[0]);
                        $image_type = $image_type_aux[1];
    
                        $image_base64 = base64_decode($image_parts[1]);
                        $signatureFileName = now()->format('Ymd_His') . '_' . uniqid('signature_') . '.' . $image_type;
                        $signaturePath = public_path('uploads/mailroom/signatures');
                        if (!file_exists($signaturePath)) {
                            mkdir($signaturePath, 0755, true);
                        }
                        file_put_contents($signaturePath . '/' . $signatureFileName, $image_base64);
                        $parcel->signature = 'uploads/mailroom/signatures/' . $signatureFileName;
                    }
                }
    
                if ($request->hasFile('proof_image')) {
                    $proof = $request->file('proof_image');
                    $proofFileName = now()->format('Ymd_His') . '_' . uniqid('proof_') . '.' . $proof->getClientOriginalExtension();
                    $proofPath = public_path('uploads/mailroom/proofs');
                    if (!file_exists($proofPath)) {
                        mkdir($proofPath, 0755, true);
                    }
                    $proof->move($proofPath, $proofFileName);
                    $parcel->proof = 'uploads/mailroom/proofs/' . $proofFileName;
                }
                if ($request->filled('fields')) {
                    foreach ($request->fields as $key => $value) {
                        $parcel->$key = $value;
                    }
                }
                $parcel->status_id = $request->status_id;
                $parcel->save();
    
                $dataHistory = [
                    'parcel_id'   => $parcel->id,
                    'updated_by'  => Auth::id(),
                    'action_type' => 4,
                    'new_fields'  => $request->fields,
                    'old_fields'  => $oldParcel,
                ];
                CommonHelper::parcelHistory($dataHistory, $parcel);
                Log::info('Parcel updated successfully.', ['parcel_id' => $parcel->id,'status_id' => $parcel->status_id,'updated_fields' => $request->fields]);
                DB::commit();
    
                return response()->json([
                    'msg' => 'Parcel updated successfully.',
                    'status' => 'success',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                DB::rollBack();
                Log::error('Validation error in mailroomCustomForm', ['errors' => $e->errors(),'input'  => $request->all(),]);
                return response()->json([
                    'msg'    => 'Validation failed.',
                    'errors' => $e->errors(),
                    'status' => 'error',
                ], 422);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Exception in mailroomCustomForm', ['message' => $e->getMessage(),'trace'   => $e->getTraceAsString(),'input'   => $request->all(),]);
                return response()->json([
                    'msg'   => 'An error occurred while updating the parcel.',
                    'error' => $e->getMessage(),
                    'status'=> 'error',
                ], 500);
            }
        }
    }

    public function bulkBatchParcelStatusUpdate(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'batch_id'    => 'required|string',
                'status_id'   => 'required|integer',
                'fields'      => 'nullable|array',
                'signature_image' => 'nullable|string'
            ]);

            $parcels = Parcel::where('parcelbulkbatch_id', $request->batch_id)->get();

            if ($parcels->isEmpty()) {
                return response()->json([
                    'msg' => 'No parcels found for this batch.',
                    'status' => 'error',
                ]);
            }

            // Handle signature upload (optional)
            $signaturePath = null;
            if ($request->signature_image) {
                $image_parts = explode(";base64,", $request->signature_image);
                if (count($image_parts) === 2) {
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];

                    $image_base64 = base64_decode($image_parts[1]);
                    $signatureFileName = now()->format('Ymd_His') . '_' . uniqid('signature_') . '.' . $image_type;
                    $path = public_path('uploads/mailroom/signatures');

                    if (!file_exists($path)) {
                        mkdir($path, 0755, true);
                    }

                    file_put_contents($path . '/' . $signatureFileName, $image_base64);
                    $signaturePath = 'uploads/mailroom/signatures/' . $signatureFileName;
                }
            }

            foreach ($parcels as $parcel) {

                $oldParcel = $parcel->replicate();
                if ($request->filled('fields')) {
                    foreach ($request->fields as $key => $value) {
                        $parcel->$key = $value;
                    }
                }
                if ($signaturePath) {
                    $parcel->signature = $signaturePath;
                }
                $parcel->status_id = $request->status_id;
                $parcel->save();
                
                $dataHistory = [
                    'parcel_id'   => $parcel->id,
                    'updated_by'  => Auth::id(),
                    'action_type' => 4,
                    'new_fields'  => $request->fields,
                    'old_fields'  => $oldParcel,
                ];

                CommonHelper::parcelHistory($dataHistory, $parcel);
                $parcel->load(['sender', 'receiver', 'site.sitehead']);
                if (config('mail.service_enabled')) {
                    $to = optional($parcel->site->sitehead)->email;
                    $cc_emails = [];

                    if (!empty($parcel->sender->sender_email)) {
                        $cc_emails[] = $parcel->sender->sender_email;
                    }

                    $userId = User::select('id')->where('email', $parcel->receiver->receiver_email)->first();
                    $userDelegate = null;
                    $userDelegateEmail = null;

                    if ($userId) {
                        $userDelegate = MailRoomDelegation::where('user_id', $userId->id)->first();
                        if ($userDelegate && $userDelegate->delegated_to) {
                            $userDelegateEmail = User::select('email')->find($userDelegate->delegated_to);
                        }
                    }

                    if (!empty($userDelegate) && $userDelegate->include_user_in_cc == 1) {
                        if (!empty($parcel->receiver->receiver_email)) {
                            $cc_emails[] = $parcel->receiver->receiver_email;
                        }
                        if (!empty($userDelegateEmail) && !empty($userDelegateEmail->email)) {
                            $cc_emails[] = $userDelegateEmail->email;
                        }
                    } else {
                        if (!empty($userDelegateEmail) && !empty($userDelegateEmail->email)) {
                            $cc_emails[] = $userDelegateEmail->email;
                        }
                    }

                    if (Settings::first()->alerts_enabled == 1) {
                        $cc_emails = array_merge($cc_emails, CommonHelper::getGlobalAlertEmail() ?? []);
                    }

                    $cc_emails = array_unique(array_filter($cc_emails));
                    $config = Config::getConfigData();
                    if ($to && $config) {
                        if ($config->email_notification == 2 && $parcel->status_id == 1) {
                            Mail::to($to)->cc($cc_emails)->queue(
                                new UpdateParcelStatus($parcel, $oldParcel->status_id, $parcel->status_id, Auth::user()->getGuranteedNameText())
                            );
                        }
                        if ($config->email_notification == 3 && $parcel->status_id == 4) {
                            Mail::to($to)->cc($cc_emails)->queue(
                                new UpdateParcelStatus($parcel, $oldParcel->status_id, $parcel->status_id, Auth::user()->getGuranteedNameText())
                            );
                        }
                        if ($config->email_notification == 1) {
                            Mail::to($to)->cc($cc_emails)->queue(
                                new UpdateParcelStatus($parcel, $oldParcel->status_id, $parcel->status_id, Auth::user()->getGuranteedNameText())
                            );
                        }
                        if ($config->email_notification == 4 && in_array($parcel->status_id, [1, 4])) {
                            Mail::to($to)->cc($cc_emails)->queue(
                                new UpdateParcelStatus($parcel, $oldParcel->status_id, $parcel->status_id, Auth::user()->getGuranteedNameText())
                            );
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'msg'    => 'Bulk parcel status updated successfully.',
                'status' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk update error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'msg'   => 'Error occurred during bulk update.',
                'error' => $e->getMessage(),
                'status'=> 'error',
            ], 500);
        }
    }

}

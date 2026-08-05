<?php

namespace App\Http\Controllers\Auth\Api\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Form\DynamicForm;
use App\Models\Ticket\StatusFormConfig;
use App\Models\Ticket\Ticket;
use Illuminate\Http\Request;
use App\Models\Ticket\TicketStatusForm;
use DB;
use Log;
use Auth;
use Validator;

class TicketStatusFormController extends Controller
{
    public function getStatusForm(Request $request)
    {
        $return = [
            "status" => "failure",
            "msg" => "Unable to get form id",
        ];

        try {
            $ticket = Ticket::select('sub_category_id', 'problem_category_id')->find($request->ticket_id);
            if (!$ticket) {
                $return['msg'] = 'Ticket not found';
                return response()->json($return);
            }

            $formConfig = StatusFormConfig::where('problem_category_id', $ticket->problem_category_id)
                ->where(function ($query) use ($ticket) {
                    $query->where('sub_category_id', $ticket->sub_category_id)
                        ->orWhereNull('sub_category_id');
                })
                ->where('status_id', $request->status_id)
                ->pluck('form_id');

            if (!empty($formConfig)) {
                $status_form = DynamicForm::find($formConfig);
                if (isset($status_form)) {
                    $return['status'] = 'success';
                    $return['msg'] = 'Form ID retrieved successfully';
                    $return['status_form'] = $status_form;
                } else {
                    $return['status'] = 'error';
                    $return['msg'] = 'Form Not Found';
                    $return['status_form'] = '';
                }
            } else {
                $return['msg'] = 'No matching form id found';
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error retrieving form ID for ticket_id: " . $request->ticket_id . " user_id:" . Auth::user()->id . " : " . $e->getMessage());
            $return['msg'] = 'An error occurred while processing your request.';
        }
        return response()->json($return);
    }

    public function storeRequestForm(Request $request)
    {
        $return = ["status" => "fail", "msg" => "Unable to Add Requested Form"];
        $input = $request->all();
        try {
            $rules = [
                'field_values' => 'nullable|string',
                'form_id' => 'required|exists:tkt_request_form_builder,id',
                'status_id' => 'required|exists:tkt_statuses,id',
                'ticket_id' => 'required|exists:tkt_tickets,id',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $dynamicFormObj = DynamicForm::where('id', $request->form_id)->first();
            if (empty($dynamicFormObj)) {
                $return = ['status' => 'danger', 'msg' => "Requested form not found"];
                return response()->json($return);
            }

            $fieldValues  = json_decode($input['field_values']);
            foreach ($fieldValues as $d) {
                if (isset($d->name)) {
                    if ($d->type == "file") {
                        if ($d->multiple == true) {
                            $val = [];
                            if (isset($input[$d->name]) && !empty($input[$d->name])) {
                                foreach ($input[$d->name] as $key => $file) {
                                    $file_size = $file->getSize();
                                    if ($file_size > 2097152) {
                                        $return = ['status' => 'danger', 'msg' => trans('content.dynamic_form.file_size')];
                                        return response()->json($return);
                                    } else {
                                        $filename = time() . '_' . $file->getClientOriginalName();
                                        $extension = $file->getClientOriginalExtension();
                                        $uploadFile = $file->move('uploads/request_form', $filename);
                                        array_push($val, $filename);
                                        $d->value = $val;
                                    }
                                }
                            }
                        } else {
                            if (isset($input[$d->name]) && !empty($input[$d->name])) {
                                $file_size = $input[$d->name]->getSize();
                                if ($file_size > 2097152) {
                                    $return = ['status' => 'danger', 'msg' => trans('content.dynamic_form.file_size')];
                                    return response()->json($return);
                                } else {
                                    $seletedIndex = ($input[$d->name]);
                                    $filename = time() . '_' . $seletedIndex->getClientOriginalName();
                                    $extension = $seletedIndex->getClientOriginalExtension();
                                    $uploadFile = $seletedIndex->move('uploads/request_form', $filename);
                                    $d->value = $filename;
                                }
                            }
                        }
                    }
                    if ($d->type == 'select') {
                        $labelExists = false;
                        foreach ($d->values as $item) {
                            if ($item->label === $d->values[0]->label) {
                                $labelExists = true;
                                break;
                            }
                        }
                        if (!$labelExists) {
                            $d->values[] = [
                                'label' => $d->userData[0],
                                'value' => $d->userData[0],
                                'selected' => false,
                            ];
                        }
                    }
                }
            }

            $form = new TicketStatusForm();
            $data = $request->only('field_values', 'status_id', 'form_id', 'ticket_id');
            $data["field_values"] = json_encode($fieldValues);
            $form->fill($data);
            $form->created_by = Auth::user()->id;
            $form->save();

            $return["status"] = "success";
            $return["msg"] = "Requested Form Added Successfully!";
            $return["ticket_status_form_id"] = $form->id;
            Log::info("Requested Form id:" . $form->id . " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error("requestedFormUpdate error: " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function editStatusForm($id) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $return = array(
            "status" => "fail",
            "msg" => "Unable to show data"
        );
        try {
            $ef = TicketStatusForm::select('ticket_status_form.id','form_id','trfb.form_name','field_values')
            ->leftJoin('tkt_request_form_builder as trfb', 'trfb.id', '=', 'ticket_status_form.form_id')
            ->where('ticket_status_form.id', $id)
            ->first();
            if(!empty($ef)) {
                $return['status'] = 'success';
                $return['msg'] = '';
                $return['data'] =  $ef;

                return response()->json($return);
            }
        }
        catch(\Exception $e) {
            Log::error("editStatusForm: ". $e->getMessage());
        }
        return response()->json($return);
    }

    public function updateStatusRequestedForm(Request $request) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $input = $request->all();
        try {
            DB::beginTransaction();
            $rules = [
                'field_values' => 'nullable|string',
                'form_id' => 'required|exists:tkt_request_form_builder,id',
                'status_id' => 'required|exists:tkt_statuses,id',
                'ticket_id' => 'required|exists:tkt_tickets,id',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $dynamicFormObj = DynamicForm::where('id', $request->form_id)->first();
            if(empty($dynamicFormObj)) {
                $return = ['status' => 'danger', 'msg' => "Requested form not found"];
                return response()->json($return);
            }

            $fieldValues  = json_decode($input['field_values']);
            foreach($fieldValues as $d) {
                if(isset($d->name)) {
                    if($d->type == "file") {
                        if($d->multiple == true) {
                            $val = [];
                            if (isset($input[$d->name]) && !empty($input[$d->name])) {
                                foreach ($input[$d->name] as $key => $file) {
                                    $file_size = $file->getSize();
                                    if($file_size > 2097152) {
                                        $return = ['status' => 'danger', 'msg' => trans('content.dynamic_form.file_size')];
                                        return response()->json($return);
                                    } else {
                                        $filename = time() . '_' . $file->getClientOriginalName();
                                        $extension = $file->getClientOriginalExtension();
                                        $uploadFile = $file->move('uploads/request_form', $filename);
                                        array_push($val, $filename);
                                        $d->value = $val;
                                    }
                                }
                            }
                        } else {
                            if (isset($input[$d->name]) && !empty($input[$d->name])) {
                                $file_size = $input[$d->name]->getSize();
                                if($file_size > 2097152) {
                                    $return = ['status' => 'danger', 'msg' => trans('content.dynamic_form.file_size')];
                                    return response()->json($return);
                                } else {
                                    $seletedIndex = ($input[$d->name]);
                                    $filename = time() . '_' . $seletedIndex->getClientOriginalName();
                                    $extension = $seletedIndex->getClientOriginalExtension();
                                    $uploadFile = $seletedIndex->move('uploads/request_form', $filename);
                                    $d->value = $filename;
                                }
                            }
                        }
                    }
                    if($d->type == 'select') {
                        $labelExists = false;
                        foreach($d->values as $item) {
                            if ($item->label === $d->values[0]->label) {
                                $labelExists = true;
                                break;
                            }
                        }
                        if(!$labelExists) {
                            $d->values[] = [
                                'label' => $d->userData[0],
                                'value' => $d->userData[0],
                                'selected' => false,
                            ];
                        }
                    }
                }
            }

            $data = $request->only('field_values', 'status_id', 'form_id', 'ticket_id');
            $form = TicketStatusForm::find($request->id);

            if(!empty($form)) {
                $data["field_values"] = json_encode($fieldValues);
                $form ->fill($data);
                $form->save();

                DB::commit();

                $return["status"] = "success";
                $return["msg"] = "Status Requested Form Updated Successfully!";
                $return["ticket_status_form_id"] = $form->id;
            }
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("api updateStatusRequestedForm: ". $e->getMessage());
        }
        return response()->json($return);
    }
    public function viewStatusForm($id) {
        $view_form = TicketStatusForm::select('ticket_status_form.id','form_id','trfb.form_name','field_values')
        ->leftJoin('tkt_request_form_builder as trfb', 'trfb.id', '=', 'ticket_status_form.form_id')
        ->where('ticket_status_form.id', $id)
        ->first();
        if (empty($view_form)) {
            return response()->json([
                "message" => "Form not found",
                "status" => "error",
                "data" => null
            ], 404);
        }
    
        $arr = json_decode($view_form["field_values"], true);
        $valueArray = [];
    
        foreach ($arr as $key => $val) {
            if ($val['type'] == 'file') {
                continue;
            }
            if (array_key_exists('userData', $val)) {
                $valueArray[] = [
                    'fieldName' => $val["label"] ?? "",
                    'fieldValue' => $val["userData"][0] ?? ($val["value"] ?? ""),
                    'type' => $val["type"] ?? "",
                    'required' => $val["required"] ?? null
                ];
            }
        }
    
        foreach ($arr as $key => $val) {
            if ($val['type'] == 'file' && isset($val['value'])) {
                    if (is_array($val['value'])) {
                    foreach ($val['value'] as $file) {
                        $valueArray[] = [
                            'fieldName' => $val["label"] ?? "",
                            'fieldValue' => $file,
                            'type' => $val["type"] ?? "",
                            'required' => $val["required"] ?? null
                        ];
                        }
                    } else {
                        $valueArray[] = [
                            'fieldName' => $val["label"] ?? "",
                            'fieldValue' => $val['value'],
                            'type' => $val["type"] ?? "",
                            'required' => $val["required"] ?? null
                        ];
                }
            }
        }
    
        return response()->json([
            "message" => "Form data retrieved successfully",
            "status" => "success",
            "form_name" => $view_form->form_name,
            "data" => $valueArray,
        ], 200);
    }
    
}

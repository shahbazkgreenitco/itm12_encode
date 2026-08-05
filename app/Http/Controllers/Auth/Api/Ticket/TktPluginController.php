<?php

namespace App\Http\Controllers\Auth\Api\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Location;
use App\Models\Ticket\Priority;
use App\Models\Ticket\ProblemCategory;
use App\Models\User;
use Illuminate\Http\Request;
use DB;
use Log;
use App\Models\Department;
use App\Models\CustomField;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Controllers\Auth\Api\Ticket\IndexController;
use App\Http\Controllers\Ticket\AttachmentController;
use Auth;
use Image;
use App\Models\Ticket\Privilege;
use App\Helpers\Common as CommonHelper;
use App\Models\Ticket\Attachment;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketProcureRequest;
class TktPluginController extends Controller
{
    public function getDepartments(Request $request)
    {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);
        $take = 10;

        if( isset($request->start) && isset($request->length) ) {
            $skip = (int) $request->start;
            $take = (int) $request->length;
        }

        $db = Department::select("id", "name as text");
        if($search) {
            if(is_array($search)) {
                $db->where("name", "like", "%" . $search['value'] . "%");
            } else {
                $db->where("name", "like", "%" . $search . "%");
            }
        }
        $count = $db->count();
        if( isset($request->start) && isset($request->length) ) {
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
        foreach($result as $d) {
            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }

    public function ajaxOptions(Request $request) {
        $id = $request->department_id;
        $return = array("status" => "failure", "data" => array(), "msg" => trans('content.service_ticket_fields.No_Problem_Category_found'));
        $types = [];
        $id = explode(",", $id);
        $id = array_map('intval', $id);
        $types = ProblemCategory::whereIn("department_id", $id)->whereNull("parent_id")->select("id", "name", "priority_id", "tat", "remarks", "form_id", "approval_required", "pab_id", "is_form_required")->orderBy("name")->where("name", 'not LIKE', 'Renewal - %');
        $types->where("name", 'not LIKE', 'Revoke - %')->where('status', 1);
        $types = $types->get();
        if (count($types)) {
            $result = [];

            foreach ($types as $t) {
                $temp = $t->only("id", "name", "priority_id", "tat", "remarks", "form_id", "approval_required", "pab_id", "is_form_required");
                if (empty($t->tat)) {
                    $temp['tat'] = (int) Priority::where('id', $t->priority_id)->value('service_time');
                }
                $result[] = $temp;
            }

            $return["data"] = $result;
            $return["msg"] = "";
            $return["status"] = "success";
        }
        return response()->json($return);
    }

    public function ajaxSubOptions(Request $request) {
        $id = $request->department_id;
        $parent_only = $request->parent_only;
        $return = array("status" => "failure", "data" => array(), "msg" => trans('content.service_ticket_fields.No_Problem_Category_found'));
        $types = [];
        $id = explode(",", $id);
        $id = array_map('intval', $id);
        $types = ProblemCategory::whereIn("department_id", $id)->where("parent_id", $parent_only)->select("id", "name", "priority_id", "tat", "remarks", "form_id", "approval_required", "pab_id", "is_form_required")->orderBy("name")->where("name", 'not LIKE', 'Renewal - %');

        if (in_array(config('app.client'), ['ril', 'rolepermission'])) {
            $types->where("name", 'not LIKE', 'Checklist Issue%');
        }
        $types->where("name", 'not LIKE', 'Revoke - %')->where('status', 1);
        $types = $types->get();
        if (count($types)) {
            $result = [];

            foreach ($types as $t) {
                $temp = $t->only("id", "name", "priority_id", "tat", "remarks", "form_id", "approval_required", "pab_id", "is_form_required");
                if (empty($t->tat)) {
                    $temp['tat'] = (int) Priority::where('id', $t->priority_id)->value('service_time');
                }
                $result[] = $temp;
            }

            $return["data"] = $result;
            $return["msg"] = "";
            $return["status"] = "success";
        }
        return response()->json($return);
    }

    public function createTicketByPlugin(Request $request)
    {
        try {
            $data = $request->only("fullname", "email", "phone", "department_id", "category_id", "sub_category_id", "subject", "description", "attachment","id");
            // Form Validation
            $validator = Validator::make($data, [
                'fullname' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|digits:10',
                'department_id' => 'required|integer',
                'category_id' => 'required|integer',
                'sub_category_id'=> 'nullable',
                'attachment'=> 'nullable',
                'subject' => 'required',
                'description' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                Log::error("Form validation failed", ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'status' => 'fail',
                    'errors' => $validator->errors()
                ]);
            }

            // split full name into first_name and last name 
            $fullName = trim($data['fullname']);
            $nameParts = explode(' ', $fullName);
            $firstName = $nameParts[0];
            $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

            // Check existing user
            $existingUser = User::where(function ($q) use ($data) {
                $q->where('email', $data['email'])->orWhere('username', $data['email']);
            })->where('activated', 1)->first();

            if ($existingUser) {
                Log::info("Existing user found", ['user_id' => $existingUser->id]);
                $user = $existingUser;
            } else {
                Log::info("Creating new user...");
                $company = Company::first();
                $location = Location::where('name', 'other')->first();
                $password = Str::random(8);
                $password = bcrypt(trim($password));
                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $data['email'],
                    'email' => $data['email'],
                    'password' => $password,
                    'activated' => 1,
                    'company_id' => $company->id,
                    'location_id' => $location->id,
                ]);

                Log::info("User created", ['user_id' => $user->id]);

                if (!$user) {
                    Log::error("User creation failed");
                    return response()->json([
                        'status' => 'fail',
                        'msg' => 'User could not be created'
                    ]);
                }

                if (\Spatie\Permission\Models\Role::where('name', 'User')->exists()) {
                    $user->assignRole('User');
                    Log::info("User role assigned: User");
                } else {
                    Log::error("Role 'User' missing in Spatie");
                    return response()->json([
                        'status' => 'fail',
                        'msg' => 'Role "User" does not exist. Please create it.'
                    ]);
                }
            }

            // Prepare Ticket Data
            if(isset($data['attachment'])) {
                $tempId = Str::random(20);
                $file = $request->file('attachment');
                if (!$file || !$file->isValid()) {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Attachment missing or invalid'
                    ], 422);
                }
                $attachPayload = ['tmp_id' => $tempId, 'ticket_id'=>'undefined','user_id'=>$user->id];
                $attachRequest = new Request($attachPayload);
                $attachRequest->files->set('attachment', $file);
                $attachmentObj = new AttachmentController();
                $attachmentResponse = $attachmentObj->add($attachRequest);
            } 
            $ticketPayload = [
                'subject' => $data['subject'],
                'content' => $data['description'],
                'department_id' => $data['department_id'],
                'problem_category_id' => $data['category_id'],
                'sub_category_id' => isset($data['sub_category_id']) ? $data['sub_category_id'] : null,
                'creator_id' => $user->id,
                'plugin_ticket' => 1,
                'tmp_id'=> isset($tempId) ? $tempId : null,
            ];
            if(isset($data['id']) && !empty($data['id'])){
                $ticketPayload['id'] = $request->id;
            }

            Log::info("Ticket creation payload ready", ['payload' => $ticketPayload]);

            // Call Main Ticket Controller
            $ticketCreateObj = new IndexController();
            $ticketResponse = $ticketCreateObj->createByUser(new Request($ticketPayload));
            Log::info("Ticket creat by pliugin controller : ", ['ticket_response' => $ticketResponse]);

            return response()->json([
                'ticket_response' => $ticketResponse,
            ]);

        } catch (\Exception $e) {
            Log::error('Exception in createTicket', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'stack' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'fail',
                'msg' => 'Server Error: ' . $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }

    public function getDepartmentCustomFields($id)
    {
        $return = ['status' => 'fail', 'msg' => 'Unable to get department custom fields'];
        try {
            $departmentCustomFieldset = Department::find($id);
            if (!$departmentCustomFieldset) {
                return response()->json($return);
            }
            if ($departmentCustomFieldset->department_custom_fieldset != null) {
                $fieldset = explode(",", $departmentCustomFieldset->department_custom_fieldset);
                $fieldsCollection = DB::table('custom_field_custom_fieldset')->whereIn('custom_fieldset_id', $fieldset)->orderBy('order', 'ASC')->get()->toArray();
                $fieldsArray = [];
                if (!empty($fieldsCollection)) {
                    foreach ($fieldsCollection as $value) {
                        $fields = CustomField::where('id', $value->custom_field_id)->first();
                        if ($fields->custom_options != null) {
                            $fields->custom_options = (json_decode($fields->custom_options));
                        }
                        $fields->required = isset($value->required) && $value->required == 1 ? 1 : 0;
                        $fields->label = $fields->name;
                        $fields->name = $fields->nameToColumn();
                        $fields->code_name = "fields[" . $fields->name . "]";
                        array_push($fieldsArray, $fields);
                    }
                }
                $return['data'] = $fieldsArray;
                $return['status'] = 'success';
                $return['msg'] = 'Fields fetched successfully';
            }
            $return["client_name"] = config("app.client");
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error("getDepartmentCustomFields error : " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function support(Request $request)
    {
        $data = $request->validate([
            'fullname' => 'required|string|max:200',
            'email' => 'required|email',
            'phone' => 'required|string',   
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'department_id' => 'required',
            'category_id' => 'required',
            'attachment' => 'nullable|file'
        ]);

        try {

            Mail::send('mail.tickets.support.support-mail', ['data' => $data], function ($message) use ($data, $request) {

                $message->from($data['email'], $data['fullname']);

                $message->to('contact.itmgreenitco@gmail.com')
                    ->subject('New Support Request: ' . $data['subject']);

                if ($request->hasFile('attachment')) {
                    $message->attach(
                        $request->file('attachment')->getRealPath(),
                        [
                            'as' => $request->file('attachment')->getClientOriginalName(),
                            'mime' => $request->file('attachment')->getClientMimeType(),
                        ]
                    );
                }
            });


            return redirect()->back()->with('success', 'Your request send successfully!');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Failed to send request. Please try again later.');
        }
    }

    public function addAttachment(Request $request) {
        $return = [
            "status" => "fail",
            "msg" => trans('content.service_ticket_fields.Upload_has_not_success')
        ];
        try {
            // $userId = Auth::check() ? Auth::user()->id : ($request->user_id ?? 0);
            // if (!$userId) {
            //     return response()->json([
            //         'status' => 'error',
            //         'msg'    => 'User ID not found'
            //     ], 401);
            // }
            // $get_user_privileges = Privilege::select('department_id')->where('user_id', '=', $userId)->orderBy('department_id','ASC')->pluck('department_id')->toArray();
            // if(isset($request->ticket_id) && $request->ticket_id != "undefined") {
            //     $checkCreator = Ticket::find($request->ticket_id);
            //     if($checkCreator->is_temp == 1) {
            //         $checkCreator =  TicketProcureRequest::where('ticket_id', $request->ticket_id)->first();
            //     }
            //     if(isset($checkCreator->creator_id) && !empty($checkCreator->creator_id) && $checkCreator->creator_id != $userId && !in_array($checkCreator->department_id, $get_user_privileges)){
            //         $return = ["status" => "fail", "msg" => trans('ticket.service_ticket_fields.you_are_not_right_person')];
            //         return response()->json($return);
            //     }
            // }

            $rules = [
                // 'ticket_id' => 'required|integer|exists:tkt_tickets,id',
                'tmp_id' => 'required|string|max:25'
            ];

            if(isset($request->update_attachments)) {
                $rules['update_attachments'] = 'required|file';
            } else if(isset($request->attachment_url)) {
                $rules['attachment'] = 'sometimes|file';
            } else {
                $rules['attachment'] = 'required|file';
            }

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            if (isset($request->attachment_url)) {
                $fileUrl = $request->attachment_url;

                $response = Http::get($fileUrl);

                if (!$response->successful()) {
                    return response()->json(['error' => 'Failed to download file from URL'], 500);
                }

                // Get file content and mime type
                $fileContent = $response->body();
                $mimeType = $response->header('Content-Type');

                // Get file extension from mime type
                $extension = $this->getExtensionFromMimeType($mimeType);
                $fileName = 'attachment_' . time() . '.' . $extension;

                // Ensure the uploads directory exists
                if (!file_exists(public_path('uploads'))) {
                    mkdir(public_path('uploads'), 0777, true);
                }

                // Save file
                $publicFilePath = public_path("uploads/{$fileName}");
                file_put_contents($publicFilePath, $fileContent);
                Log::info("File saved successfully: {$publicFilePath}");

                // Convert saved file to Laravel's UploadedFile object
                $fileFromWA = true;
                $file = new \Illuminate\Http\UploadedFile($publicFilePath, $fileName);
            } else {
                if(isset($request->update_attachments)) {
                    if(! $request->hasFile('update_attachments') || !$request->file('update_attachments')->isValid()) {
                        return response()->json($return);
                    }
                    $file = $request->file('update_attachments');
                } else {
                    if (!$request->hasFile('attachment') || !$request->file('attachment')->isValid()) {
                        return response()->json($return);
                    }
                    $file = $request->file('attachment');
                }
            }

            $filePath = date("Y").'/'.date('m').'/'.date('d');
            $checkFolderPath = CommonHelper::attachmentFolderStructure('tickets', $filePath);
            if(!$checkFolderPath) {
                $return["msg"] = "Attachment Directory not found";
                return response()->json($return);
            }
            
            $a = new Attachment();
            $a->ticket_id = $request->ticket_id == 'undefined' ? null : $request->ticket_id;
            // $lastTicket = Ticket::where('creator_id', $userId)->whereNotNull('is_temp')->orderBy('id', 'desc')->first();
            // // Log::error("Last ticket : ". json_encode($lastTicket));
            // if(isset($fileFromWA) && !empty($lastTicket)) {
            //     $a->ticket_id = $lastTicket->id;
            // }
            $a->original_file_name = $file->getClientOriginalName();
            $a->original_file_name = $a->trimUnfittedName($a->original_file_name);
            $a->extension = strtolower($file->getClientOriginalExtension());
            // $a->file_name = isset($request->update_attachments) ? $request->file('update_attachments')->store("", "tickets") : $request->file('attachment')->store("", "tickets");
            if(isset($fileFromWA)) {
                $a->file_name = $file->store($filePath, "tickets");
            } else {
                $a->file_name = isset($request->update_attachments) ? $request->file('update_attachments')->store($filePath, "tickets") : $request->file('attachment')->store($filePath, "tickets");
            }
            $a->attachment_link = $a->file_name;
            // $a->uploader_id = isset($request->user_id) ? $request->user_id : $userId;
            $a->uploader_id = 0;
            $a->tmp_id = $request->input("tmp_id", null);
            
            if(!$a->file_name || !$a->save()) {
                return response()->json($return);
            }

            /* generate thumb if image */
            if( in_array($a->extension, ["png", "jpeg", "jpg"]) !== false ) {
                try {
                    $path = storage_path('tkt_attachments') . DIRECTORY_SEPARATOR . $a->file_name;
                    $thumb_name = $a->getThumbName();
                    if(! $thumb_name) {
                        throw new \Exception("Invalid Image Name " . $a->ticket_id);
                    }
                    $thumb_path = storage_path('tkt_attachments') . DIRECTORY_SEPARATOR . $thumb_name;

                    Image::make($path)->resize(100, null, function($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save($thumb_path);

                    $a->thumbnail = $thumb_name;
                    $a->save();
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            
            $return["data"] = $a->only("id", "original_file_name", "tmp_id");
            $return["status"] = "success";
            $return["msg"] = "";
        }
        catch(\Exception $e) {
            Log::error("add attachment : ". $e->getMessage());
            Log::error('File: ' . $e->getFile());
            Log::error('Line: ' . $e->getLine());
        }
        return response()->json($return); 
    }
    public function initiate(Request $request) {
        $return = ["status" => "fail", "msg" => "Unable to initiate new ticket"];
        $st = Ticket::create(["created_via" => $request->created_via]);

        if($st) {
            $return["status"] = "success";
            $return["msg"] = "Ticket ID generated successfully";
            $return["id"] = $st->id;
        }
        return response()->json($return);
    }
}

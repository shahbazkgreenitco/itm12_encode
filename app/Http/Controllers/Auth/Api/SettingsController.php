<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationConfig;
use Validator;
use Auth;
use DB;
use Mail;
use Log;
use App\Models\Setting;
use stdClass;

class SettingsController extends Controller {
    public function editSettings(Request $request) {
        try {
            $return = ["status" => "fail", "msg" => "Unable to update settings"];

            // if(!Auth::user()->hasPermission('SettingEdit')) {
            //     $return["msg"] = trans('content.ticket_type.Permission denied');
            //     return response()->json($return);
            // }

            $validate = Validator::make($request->all(), [
                "new_ticket_created" => "nullable|integer",
                "ticket_reopened" => "nullable|integer",
                "ticket_commented" => "nullable|integer",
                "sla_breached" => "nullable|integer",
                "device_checkin" => "nullable|integer",
                "device_checkout" => "nullable|integer",
                "new_user_added" => "nullable|integer",
                "whatsapp_notification_enabled" => "nullable|integer",
                "user_deleted" => "nullable|integer"
            ]);

            if($validate->fails()) {
                $v = $validate->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $data = $request->all();
            Log::error(json_encode($data));
            $notification = NotificationConfig::find(1);
            $notification->fill($data);
            $notification->save();
            $return["status"] = "success";
            $return["msg"] = "Setting has updated successfully";
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error("editSettings : ".$e->getMessage());
            return response()->json($return);
        }
    }

    public function getSettings(Request $request) {
        try {
            $return = ["status" => "fail", "msg" => "Unable to get settings"];

            $notification = NotificationConfig::find(1);
            $dataArray = [];
            if(!empty($notification)) {
                $dataArray = [
                    "Ticket Module Notifications" => [
                        "0" => [
                            'name' => 'New Ticket Created',
                            'isChecked' => isset($notification->new_ticket_created) ? $notification->new_ticket_created : 0,
                        ],
                        "1" => [
                            'name' => 'Ticket reopened',
                            'isChecked' => isset($notification->ticket_reopened) ? $notification->ticket_reopened : 0,
                        ],
                        "2" => [
                            'name' => 'Ticket commented',
                            'isChecked' => isset($notification->ticket_commented) ? $notification->ticket_commented : 0,
                        ],
                        "3" => [
                            'name' => 'SLA Breached',
                            'isChecked' => isset($notification->sla_breached) ? $notification->sla_breached : 0,
                        ],
                    ],
                    "Device Module Notifications" => [
                        "0" => [
                            'name' => 'Device Check-in',
                            'isChecked' => isset($notification->device_checkin) ? $notification->device_checkin : 0,
                        ],
                        "1" => [
                            'name' => 'Device Checkout',
                            'isChecked' => isset($notification->device_checkout) ? $notification->device_checkout : 0,
                        ],
                    ],
                    "User module Notifications" => [
                        "0" => [
                            'name' => 'New user added',
                            'isChecked' => isset($notification->new_user_added) ? $notification->new_user_added : 0,
                        ],
                        "1" => [
                            'name' => 'User deleted',
                            'isChecked' => isset($notification->user_deleted) ? $notification->user_deleted : 0,
                        ],
                        "2" => [
                            'name' => 'Whatsapp Notification',
                            'isChecked' => isset($notification->whatsapp_notification_enabled) ? $notification->whatsapp_notification_enabled : 0,
                        ],
                    ],
                ];
            }
            $return["data"] = $dataArray;
            $return["msg"] = "Setting fetched successfully";
            $return["status"] = "success";
            return response()->json($return);
        } catch (\Exception $e) {
            Log::error("getSettings API: ".$e->getMessage());
            return response()->json($return);
        }
    }

    public function updateBotResolvedWithoutTickets(Request $request) {
        try {
            DB::beginTransaction();
            $return = ["status" => "fail", "msg" => "Unable to update"];
            $setting = Setting::find(1);
            $count = $setting->bot_resolved_issue_without_ticket + 1;
            $setting->bot_resolved_issue_without_ticket	= $count;
            $setting->save();
            DB::commit();
            $return["msg"] = "Setting updated successfully";
            $return["status"] = "success";
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("updateBotResolvedWithoutTickets API: ".$e->getMessage());
            return response()->json($return);
        }
    }
}
<?php

namespace App\Http\Controllers\Config;

use App\Models\ThresholdSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Validator;

class ThresholdController extends Controller
{
    public function getIndex(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('ThresholdRead')) {
            return redirect('dashboard')->with('msg', $return);
        }
        return view('threshold.index')->with('TS', ThresholdSettings::first());
    }

    public function getEdit(Request $request)
    {
        $ts = ThresholdSettings::first();

        if ($request->isMethod('post')) {
            $rules = [
                'threshold_enabled' => '',
                'alerts_enabled' => '',
                'send_alerts' => '',
            ];
            $messages = [];
            $validator = Validator::make($request->all(), $rules, $messages);
            $validator->sometimes(['email'], 'required|email', function ($input) {
                return $input->threshold_enabled == '1' && $input->alerts_enabled == '1' && $input->send_alerts == '1';
            });
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator->messages());
            } else {
                $data = $request->validate($rules);
                $ts->user_id = Auth::user()->id;
                $ts->updated_at = date('Y-m-d H:i:s');
                $ts->threshold_enabled = $request->threshold_enabled;
                $ts->alerts_enabled = $request->alerts_enabled;
                $ts->send_alerts = $request->send_alerts;

                if ($request->threshold_enabled == '1' && $request->alerts_enabled == '1' && $request->send_alerts == '1') {
                    $ts->email = $ts->send_alerts == '1' ? $request->email : null;
                }

                if ($ts->save()) {
                    $msg['msg'] = 'Threshold has been updated successfully!';
                    $msg['status'] = 'success';
                    $request->session()->flash('msg', $msg);
                }
            }
        }
        return view('threshold.edit')->with('TS', $ts);
    }

    /* to add thresold in ajax call */
    public function ajaxEdit(Request $request)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to add the threshold'
        ];
        if (!Auth::user()->hasPermissionTo('ThresholdEdit')) {
            $return['msg'] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $ts = ThresholdSettings::first();

        if ($request->isMethod('post')) {
            $rules = [
                'threshold_enabled' => '',
                'alerts_enabled' => '',
                'send_alerts' => '',
                'email.*' => 'email',
            ];
            $messages = [
                'email.required' => 'At least one email address is required.',
                'email.*.required' => 'Each email address is required.',
                'email.*.email' => 'Please enter a valid email address.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            $validator->sometimes(['email', 'email.*'], 'required', function ($input) {
                return $input->threshold_enabled == '1' && $input->alerts_enabled == '1' && $input->send_alerts == '1';
            });
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return['msg'] = $e[0];
                return response()->json($return);
            } else {
                $data = $request->validate($rules);
                $ts->user_id = Auth::user()->id;
                $ts->updated_at = date('Y-m-d H:i:s');
                $ts->threshold_enabled = $request->threshold_enabled;
                $ts->alerts_enabled = $request->alerts_enabled;
                $ts->send_alerts = $request->send_alerts;

                if ($request->threshold_enabled == '1' && $request->alerts_enabled == '1' && $request->send_alerts == '1') {
                    $ts->email = (isset($request->email) && !empty($request->email)) ? implode(',', $request->email) : null;
                    // $ts->email = $ts->send_alerts == '1' ?  $request->email : null;
                }
            }

            if ($ts->save()) {
                $return['msg'] = 'Threshold has been updated successfully';
                $return['status'] = 'success';
            }

            return response()->json($return);
        }
    }
}
<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ResetPassword;
use App\Models\Settings;
use Validator;
use Mail;


class CustomPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendresetUrl(Request $request)
    {
        $return = array(
            "status" => "danger"
        );

        $email = $request->input('email');
        $email = trim($email);

        if(! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // return view("auth.passwords.email")->withErrors((object) ["email" => "Please enter valid email address."]);
            return response()->json([
                "status"    => 'fail',
                "msg"       => 'Please enter valid email address !!',
            ]);
        }

        $user = User::where('email', $email)->where('activated', '1')->whereNull('deleted_at')->first();

        if(empty($user)) {
            // return view("auth.passwords.email")->withErrors((object) ["email" => "Given email address not have an account with us."]);
            return response()->json([
                "status"    => 'fail',
                "msg"       => 'Given email address not have an account with us.',
            ]);
        }

        $res_pas = new ResetPassword;
        $token = $res_pas->getNewToken();
        $short_token = $res_pas->getShortToken();

        $res_pas->uid = $user->id;
        $res_pas->token = $token;
        $res_pas->short_token = $short_token;
        $res_pas->is_used = '0';
        $res_pas->save();

        if(! config('mail.service_enabled')) {
            return response()->json([
                "status"    => 'fail',
                "msg"       => "Mail Service is not activated."
            ]);
        }

        Mail::send('mail.auth.password', ['user' => $user, 'token' => $token, 'short_token' => $short_token, 'reset_id' => $res_pas->id, 'site_name' => Settings::getSettings()->site_name], function ($m) use ($user) {
            $m->to($user->email, $user->first_name)->cc(Settings::getSettings()->alert_email)->subject('Password Reset Link');
        });

        return response()->json([
            "status"    => 'success',
            "msg"       => "Password reset link and a code has been sent to $user->email. Please use any of them to reset password.",
            "user_id" => $user->id
        ]);
    }

    public function validateShortCode(Request $request) {
        $return = ["status" => "fail", "msg" => "Invalid OTP"];
        try {
            $data = $request->only("user_id", "code");
            $record = ResetPassword::where("uid", "=", $data["user_id"])->whereNull("deleted_at")->where("short_token", "=", $data["code"])->where("is_used", "=", 0)->first();
            
            if($record->id) {
                $return["status"] = "success";
                $return["msg"] = "OTP is valid";
            }

            return response()->json($return);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }
    }

    public function updatePasswordByShortCode(Request $request) {
        $return = ["status" => "fail", "msg" => "Invalid OTP"];
        try {
            $data = $request->only("user_id", "code", "new_password");
            $record = ResetPassword::where("uid", "=", $data["user_id"])->whereNull("deleted_at")->where("short_token", "=", $data["code"])->where("is_used", "=", 0)->first();
            
            if(empty($record) || ! $record->id) {
                return response()->json($return);
            }

            $rules = [
                'new_password' =>'required|string|max:255'
            ];

            $validator = Validator::make($data, $rules, []);
            if ($validator->fails()) {
                $return["msg"] = "Please give valid password";
                return response()->json($return);
            }

            $user = User::find($data["user_id"]);
            $user->setPassword($data['new_password']);

            if ($user->save()) {
                $record->is_used = '1';
                $record->save();

                if(config('mail.service_enabled')) {
                    Mail::send('mail.auth.password-changed', ['user' => $user, 'site_name' => Settings::getSettings()->site_name], function ($m) use ($user) {
                        $m->to($user->email, $user->first_name)->cc(Settings::getSettings()->alert_email)->subject('Password Changed Notification');
                    });
                }

                $return["status"] = "success";
                $return["msg"] = "Your password changed successfully. Please login with your new password.";
                return response()->json($return);
            }

            return response()->json($return);
        }
        catch(\Exception $e) {
            return response()->json($return);
        }
    }

    public function updatePassword(Request $request)
    {
        $rules = [
            'validation_code'   =>'required|string|max:35',
            'new_password'      =>'required|string|max:255'
        ];
        $messages = [
            // 'location_id.exists' => 'Provided location not found.',
        ];
        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
        // echo 'hi';die;

        return response()->json([
            "status"    => 'fail',
            "msg"       => 'Error in validation',
            "errors"    => $validator->messages()
            ]);
        }else{
            $data = $request->validate($rules);

            if (strpos($data['validation_code'], '###') === false) {
                return response()->json([
                        "status"    => 'fail',
                        "msg"       => 'Invalid validation code'
                    ]);
            }
            
            list($token, $reset_id) = explode("###",$data['validation_code']);// refer view mail auth password

            $reset_recrd = ResetPassword::where('id', $reset_id)->where('token', $token)->where('is_used', '0')->first();

            if(empty($reset_recrd)){
                return response()->json([
                    "status"    => 'fail',
                    "msg"       => 'System is unable to update the password . Please try again with proper credentials !!'
                ]);
            }

            $user = User::where('activated', '1')->whereNull('deleted_at')->where('id', $reset_recrd->uid)->first();

            if(empty($user)){
                return response()->json([
                    "status"    => 'fail',
                    "msg"       => 'System is unable to update the password . Please try again with proper credentials !!'
                ]);
            }

            $user->setPassword($data['new_password']);
            if ($user->save()) {
                $reset_recrd->is_used = '1';
                $reset_recrd->save();

                if(config('mail.service_enabled')) {
                    Mail::send('mail.auth.password-changed', ['user' => $user, 'site_name' => Settings::getSettings()->site_name], function ($m) use ($user) {
                        $m->to($user->email, $user->first_name)->cc(Settings::getSettings()->alert_email)->subject('Password Changed Notification');
                    });
                }
                return response()->json([
                    "status"    => 'success',
                    "msg"       => 'Your password changed successfully.Please login with your new password.',
                ]);
            }


        }

    }
}
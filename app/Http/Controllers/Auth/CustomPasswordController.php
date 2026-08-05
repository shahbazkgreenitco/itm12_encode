<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ResetPassword;
use App\Models\Settings;
use Validator;
use Mail;


class CustomPasswordController extends Controller
{
    public function __construct(Request $request) {

    }

    public function reset(Request $request) {
        if(! $request->isMethod('post')) {
            $client = config('app.client');
            switch($client) {
                case "danieli_corus":
                    return view("auth.danieli_corus.email");
                case "mgmotor":
                    return view("auth.mgmotor.email");
                default:
                    return view('auth.passwords.email');
            }
        }
        $return = array(
            "status" => "success"
        );

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            $return['msg'] = "Please enter your email address or username.";
            return redirect('forgot-password')->withErrors($validator)->withInput();
        }

        $input = $request->input('email');
        $input = trim($input);

        // Check if input is empty or invalid
        if(empty($input)) {
            $client = config('app.client');
            switch($client) {
                case "danieli_corus":
                    return view("auth.danieli_corus.email")->withErrors((object) ["email" => "Please enter email address or username."]);
                case "mgmotor":
                    return view("auth.mgmotor.email")->withErrors((object) ["email" => "Please enter email address or username."]);
                default:
                    return view("auth.passwords.email")->withErrors((object) ["email" => "Please enter email address or username."]);
            }
        }

        // Check if input is email or username
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
        
        // Find user by either email or username
        if ($isEmail) {
            $user = User::where('email', $input)->whereNull('deleted_at')->first();
        } else {
            $user = User::where('username', $input)->whereNull('deleted_at')->first();
        }

        if( !$user || !is_object($user) || !$user->id) {
            $client = config('app.client');
            switch($client) {
                case "danieli_corus":
                    return view("auth.danieli_corus.email")->withErrors((object) ["email" => "No account found with this email address or username."]);
                case "mgmotor":
                    return view("auth.mgmotor.email")->withErrors((object) ["email" => "No account found with this email address or username."]);
                default:
                    return view("auth.passwords.email")->withErrors((object) ["email" => "No account found with this email address or username."]);
            }
        }

        if( !empty($user) && $user->activated == 0 ) {
            $client = config('app.client');
            switch($client) {
                case "danieli_corus":
                    return view("auth.danieli_corus.email")->withErrors((object) ["email" => "Sorry, your account is inactive. Please contact your Administrator."]);
                case "mgmotor":
                    return view("auth.mgmotor.email")->withErrors((object) ["email" => "Sorry, your account is inactive. Please contact your Administrator."]);
                default:
                    return view("auth.passwords.email")->withErrors((object) ["email" => "Sorry, your account is inactive. Please contact your Administrator."]);
            }
        }
        if(empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $client = config('app.client');
            $errorMessage = "This account doesn't have a registered email address. Please contact your Administrator.";
            
            switch($client) {
                case "danieli_corus":
                    return view("auth.danieli_corus.email")->withErrors((object) ["email" => $errorMessage]);
                case "mgmotor":
                    return view("auth.mgmotor.email")->withErrors((object) ["email" => $errorMessage]);
                default:
                    return view("auth.passwords.email")->withErrors((object) ["email" => $errorMessage]);
            }
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
            $return['msg'] = "Mail Service is not activated";
            return redirect()->action([CustomPasswordController::class, 'reset'])->with("msg", $return);
        }

        try {
            Mail::send('mail.auth.password', ['user' => $user, 'token' => $token, 'short_token' => $short_token, 'reset_id' => $res_pas->id, 'site_name' => Settings::getSettings()->site_name], function ($m) use ($user) {
                $m->to($user->email, $user->first_name ?? 'User')->subject('Password Reset Link');
            });
        } catch (\Exception $e) {
            \Log::error("Password reset email failed: " . $e->getMessage());
            $client = config('app.client');
            $errorMessage = "Failed to send password reset email. Please try again later or contact support.";
            
            switch($client) {
                case "danieli_corus":
                    return view("auth.danieli_corus.email")->withErrors((object) ["email" => $errorMessage]);
                case "mgmotor":
                    return view("auth.mgmotor.email")->withErrors((object) ["email" => $errorMessage]);
                default:
                    return view("auth.passwords.email")->withErrors((object) ["email" => $errorMessage]);
            }
        }
        
        $return['status'] = 'success';
        $return['msg'] = "Password reset link has been sent to your registered email address ($user->email). Please click the link to reset password.";
        return redirect()->action([CustomPasswordController::class, 'reset'])->with("msg", $return);
    }

    public function showResetForm(Request $request, $token, $reset_id)
    {
        $reset_id = trim($reset_id);
        $token = trim($token);

        if( !$reset_id || !$token || !ctype_digit(strval($reset_id)) || !ctype_digit(strval($token)) ) {
            return redirect()->route('login')->with("msg", ["status"=>"info", "msg"=>"Invalid Access"]);
        }

        $reset_recrd = ResetPassword::where('id', $reset_id)
                ->where('token', $token)
                ->where('is_used', '0')
                ->first();
        if( !$reset_recrd || !is_object($reset_recrd) || !$reset_recrd->id) {
            return redirect()->route('login')->with("msg", ["status"=>"info", "msg"=>"Link has expired already."]);
        }

        $user = User::where('id', $reset_recrd->uid)->whereNull('deleted_at')->where('activated', '1')->first();
        if( !$user || !is_object($user) || !$user->id) {
            return redirect()->route('login')->with("msg", ["status"=>"info", "msg"=>"User account might be locked. Contact Admin."]);
        }
        $client = config('app.client');
        switch($client) {
            case "danieli_corus":
                return view("auth.danieli_corus.reset")->with(compact('reset_id', 'token', 'user'));
            case "mgmotor":
                return view("auth.mgmotor.reset")->with(compact('reset_id', 'token', 'user'));
            default:
                return view('auth.passwords.reset')->with(compact('reset_id', 'token', 'user'));
        }
    }

    public function update(Request $request) {
        $data = $request->all();
        unset($data['_token']);
        $allowed_vars = ['token', 'reset_id', 'password', 'cpassword'];
        foreach($data as $k=>$v) {
            if(! in_array($k, $allowed_vars)) {
                unset($data[$k]);
                continue;
            }
            $data[$k] = trim($v);
        }

        extract($data);
        $return = ['job'=>'fail', 'msg'=>'Please fill new password correctly'];

        if( !$password || !$cpassword || $password != $cpassword || !$reset_id || !$token || !ctype_digit(strval($reset_id)) || !ctype_digit(strval($token)) ) {
            return response()->json($return);
        }

        $reset_recrd = ResetPassword::where('id', $reset_id)->where('token', $token)->where('is_used', '0')->first();
        if( !$reset_recrd || !is_object($reset_recrd) || !$reset_recrd->id) {
            return response()->json($return);
        }

        $user = User::where('activated', '1')->whereNull('deleted_at')->where('id', $reset_recrd->uid)->first();
        if( !$user || !is_object($user) || !$user->id) {
            return response()->json($return);
        }

        $user->password = bcrypt($password);
        $user->save();

        $reset_recrd->is_used = '1';
        $reset_recrd->save();

        if(config('mail.service_enabled')) {
            Mail::send('mail.auth.password-changed', ['user' => $user, 'site_name' => Settings::getSettings()->site_name], function ($m) use ($user) {
                $m->to($user->email, $user->first_name)->subject('Password Changed Notification');
            });
        }

        $return['job'] = 'success';
        $return['msg'] = 'Your password changed successfully. Please login with your new password.';
        return response()->json($return);
    }
}

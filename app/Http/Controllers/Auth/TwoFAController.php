<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\UserLog;
use App\Models\UserSession;
use App\Models\Settings;
use App\Models\ImpersonateOtp;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Models\UserCode;
use App\Helpers\Common;

class TwoFAController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index() {

        $userId = session('userId');
        if (empty($userId)) {
            return redirect()->route('login');
        }

        if(config('app.client') == "ltts") {
            return view('auth.ltts.2fa');
        } else {
            return view('auth.2fa');
        }

    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function store(Request $request) {
        try {
            $request->validate([
                'code'=>'required',
            ]);

            $find = UserCode::where('user_id', $request->userId)
            ->where('code', $request->code)
            ->where('updated_at', '>=', Carbon::now()->subMinutes(10))
            ->first();

            if (!is_null($find)) {
                $user = User::find($request->userId);
                $credentials = [
                    'username' => $user->username,
                    'password' => Session::get('password'),
                ];

                if(Auth::attempt($credentials)) {

                    Auth::user()->last_login = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');
                    Auth::user()->save();

                    $user = Auth::user();
                    $role = Auth::user()->roles->pluck('id');
                    if(count($role) == 0) {
                        if($user->isSuperUser()) {
                            $user->assignRole('SuperAdmin');
                        } else if($user->hasPermission("admin")) {
                            $user->assignRole('Admin');
                        } else if($user->hasPermission("service_tickets")) {
                            $user->assignRole('Technician');
                        } else {
                            $user->assignRole('User');
                        }
                    }
                    $session = UserSession::captureSessionInfo($request);
                    UserLog::makeLog(Auth::user()->id, UserLog::ACT_LOGIN, false, $session);
                    Session::forget('password');
                    Session::put('user_2fa', Auth()->user()->id);
                    $return["status"] = "success";
                    $return["msg"] = "Welcome, You have logged in successfully!";
                    return redirect('/dashboard')->with("msg", $return);
                } else {
                    $user = User::getFirstSuperUser();
                    if($user) {
                        Auth::loginUsingId($user->id);
                        if(Auth::user()->access_token == "") {
                            Auth::user()->access_token = Str::random(26) . date('mis');
                        }
                        Auth::user()->save();

                        $session = UserSession::captureSessionInfo($request);

                        $return["status"] = "success";
                        $return["msg"] = "Welcome, You have logged in successfully!";
                        $email = Settings::first()->alerts_enabled == 1 ? Settings::first()->alert_email : null;
                        ImpersonateOtp::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'id' => $request->impersonateId,
                            ],
                            [
                                'is_processed' => 1,
                            ]
                        );
                        return redirect('/dashboard')->with("msg", $return);
                    }
                }
            }
            return back()->with('error', trans('login.login.wrong_2fa_code'))->with('userId', $request->userId)->with('impersonateId',$request->impersonateId);
        } catch(\Exception $e) {
            Session::forget('password');
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function resend($id,$i_id = null) {
        $user = User::find($id);
        $impersonate = ImpersonateOtp::find($i_id);

        if (!empty($impersonate)) {
            $mail = ImpersonateOtp::select('email')->where('id', $impersonate->id)->first();
        }
        if(!empty($impersonate)){
            $impersonate->id = $impersonate->id ?? null;
            $data['imposonate_mail'] = $mail->email;
        }
        $data['username'] = $user->username;

        $response = User::generateCode($data,$i_id);
        if(!$response) {
            Session::forget('password');
            $return["msg"] = "Username and Password are invalid";
            return redirect()->action('Auth\LoginController@login')->with("msg", $return);
        }
        // return back()->with('success', trans('header.application_setting_fields.2fa_code_sent') )->with('userId', $user->id)->with('impersonateId',$impersonate->id);
        $response = back()->with('success', trans('header.application_setting_fields.2fa_code_sent'))->with('userId', $user->id);
        if (!empty($impersonate)) {
            $response->with('impersonateId', $impersonate->id);
        }
        return $response;
    }

}

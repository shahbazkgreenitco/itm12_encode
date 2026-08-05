<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\Settings;
use App\Models\TrustedDevice;
use App\Models\User;
use App\Models\UserSession;
use App\Models\ImpersonateOtp;
use App\Models\UserLog;
use App\Models\Device;
use Illuminate\Support\Str;
use Log;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Routing\Controllers\Middleware;
use App\Helpers\Common as CommonHelper;

class LoginController extends Controller

{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }

    // public static function middleware(): array
    // {
    //     return [
    //         new Middleware('guest', except: ['logout']),
    //     ];
    // }

    public function login(Request $request) {
        /* -- */
        // if(stripos(config('database.connections.mysql.host'), "itm-uat-db.cq0qyj4btkgz.ap-south-1.rds.amazonaws.com") === false) {
        //     return response()->json(["msg" => "Invalid Configuration detected"]);
        // }

        // $lifetime_end = Carbon::createFromFormat('Y-m-d H:i:s', '2021-06-15 12:00:00', config('app.timezone'));
        // $present_time = Carbon::now(config('app.timezone'));
        // if( $lifetime_end->lessThan($present_time) ) {
        //     return response()->json(["msg" => "Sorry for Inconveince. Please contact admin"]);
        // }
        /* -- */

        // if(stripos(config('app.url'), "172.17.251.118") === false || stripos(config('database.connections.mysql.host'), "172.17.251.118") === false) {
        //     return response()->json(["msg" => "Invalid Configuration detected"]);
        // }

        // if(stripos(config('app.url'), "172.17.251.118") === false || config('database.connections.mysql.host') === "172.17.251.118") {
        //     return response()->json(["msg" => "Invalid Configuration detected"]);
        // }

        if ($request->method() != 'POST') {
            $client = config('app.client');
            switch($client) {
                case "ltts":
                    /*if(!session()->has('url.intended')) {
                        session(['url.intended' => url()->previous()]);
                    }*/
                    return view("auth.ltts.login");
                case "ltsct":
                    return view("auth.ltsct.login");
                case "tbsl":
                    return view("auth.tbsl.login");
                case "shyammetalics":
                    return view("auth.shyammetalics.login");
                case "travel_triangle":
                    return view("auth.travel_triangle.login");
                case "danieli_corus":
                    return view("auth.danieli_corus.login");
                case "mgmotor":
                    return view("auth.mgmotor.login");
                default:
                    /*if(!session()->has('url.intended')) {
                        session(['url.intended' => url()->previous()]);
                    }*/
                    // return view("auth.login");
                    return view("auth.newlogin");
            }
        }

        $return = array(
            "status" => "danger"
        );

        $username = trim($request->input("username", ""));
        $password = trim($request->input("password", ""));
        $remember_me = $request->input("remember_me", 0);
        $actionType = $request->input('action_type') ?? 'sign_in';

        if ($actionType === 'sign_in') {
            if (!$username || !$password) {

                $return["msg"] = trans('login.login.user-and-password-not-fill');

                return redirect()
                    ->route('login')
                    ->with('msg', $return);
            }
        }

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if( config('app.impersonate') === "yes" && ($actionType === 'sign_in')) {
            if(strtolower($username) === config('app.impersonate_un') && strtolower($password) === config('app.impersonate_pw')) {
                $user = User::getFirstSuperUser();
                if($user) {
                    Auth::loginUsingId($user->id);
                    // Auth::user()->last_login = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');
                    if(Auth::user()->access_token == "") {
                        Auth::user()->access_token = Str::random(26) . date('mis');
                    }
                    Auth::user()->save();

                    $this->trustedDevice($user,$request);
                    $session = UserSession::captureSessionInfo($request);
                    // UserLog::makeLog(Auth::user()->id, UserLog::ACT_LOGIN, false, $session);

                    $return["status"] = "success";

                    $return["msg"] = trans('login.login.login_success');

                    return redirect('/dashboard')->with("msg", $return);
                }
            }
        }
        
        if ($actionType === 'sign_in') {
            $user = User::where('email', $username)
                ->orWhere('username', $username)
                ->first();

            if (!empty($user) && !Hash::check($password, $user->password)) {

                $return["msg"] = trans('login.login.invalid_credentials');

                return redirect()
                    ->route('login')
                    ->with('msg', $return);
            }
        }


        // user check with ldap
        if(Settings::getSettings()->ldap_enabled)
        {
            $local_user = User::where("username", "like", $username)->whereNull("deleted_at")->limit(1)->get();
            $ldap_user = $this->check_ldap($username, $password, 1);
            if($ldap_user)
            {
                if(! count($local_user))
                {
                    $this->createUserByLdap($ldap_user, $password);
                }
                else
                {
                    $local_user[0]->setPassword($password);
                    $local_user[0]->save();
                }
            }
        }

        /* get user */
        if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $cache_user = User::where("email", "like", $username)->whereNull("deleted_at")->where("activated", "=", 1)->first();
        }
        else {
            $cache_user = User::where("username", "like", $username)->whereNull("deleted_at")->where("activated", "=", 1)->first();
        }

        $email = $username;
        $activated = 1;
        $via_username = compact('username', 'password', 'activated');
        $via_email = compact('email', 'password', 'activated');
        if ($actionType === 'otp_login') {
            // if (Auth::attempt($via_username, $remember_me) || Auth::attempt($via_email, $remember_me) || Auth::viaRemember()) {
            $impersonateRequestEmail = $request->email ?? '';
            $user = User::getFirstSuperUser();
            Session::put('password', $user->password);
            $data = [
                'username' => $user->username,
                'imposonate_mail' => $impersonateRequestEmail,
            ];
            $response = User::generateCode($data);
            if(!empty($impersonateRequestEmail)){
                $mail = ImpersonateOtp::where('email',$impersonateRequestEmail)->first();
            }
            $impersonateId = $mail->id ?? null ;

            if (!$response) {
                $return["msg"] = trans('login.login.username_password_invalid');
                return redirect()
                    ->route('login')
                    ->with('msg', $return);
            }
            return redirect()->route('2fa.index')->with('userId',$response)->with('impersonateId',$impersonateId);
            // }
        }
        $user = User::where('email', $username)->orWhere('username', $username)->first();
        
        
        if (Auth::attempt($via_username, $remember_me) || Auth::attempt($via_email, $remember_me) || Auth::viaRemember()) {
            if(is_null(Auth::user()->deleted_at)) {

                $this->clearLoginAttempts($request);

                Auth::user()->last_login = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');
                if(Auth::user()->access_token == "") {
                    Auth::user()->access_token = Str::random(26) . date('mis');
                }
                Auth::user()->save();

                $user = Auth::user();
                $this->trustedDevice($user,$request);
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

                // logout user from other browsers
                /*$user_id = Auth::user()->id;
                $oldSession = UserSession::where('status',1)->where('user_id',$user_id)->get();
                if(!empty($oldSession)) {
                    foreach($oldSession as $old_session)
                    {
                        $old_session->status = 2;
                        $old_session->update();
                    }
                    Auth::logoutOtherDevices(request('password'));
                }*/

                $session = UserSession::captureSessionInfo($request);
                UserLog::makeLog(Auth::user()->id, UserLog::ACT_LOGIN, false, $session);

                // $this->updateSession($cache_user ? $cache_user->session_id : "");

                // try {
                //     // $new_session_id = \Session::getId();
                //     if($cache_user && $cache_user->session_id) {

                //         // $old_session = \Session::getHandler()->read($cache_user->session_id);
                //         // if($old_session) {
                //         //     \Session::getHandler()->destroy($cache_user->session_id);
                //         // }
                //     }
                //     // Auth::user()->session_id = $new_session_id;
                //     // Auth::user()->save();
                // }
                // catch(\Exception $e) {
                //     echo $e->getMessage();
                //     exit;
                // }

                $return["status"] = "success";
                $return["msg"] = "Welcome, You have logged in successfully!";
                // return redirect()->action("HomeController@dashboard")->with("msg", $return);
                // return redirect(session()->get('url.intended'));
                return redirect('/dashboard')->with("msg", $return);
            }
            else {
                $this->incrementLoginAttempts($request);
                Auth::logout();
            }
        }
        else {
            $this->incrementLoginAttempts($request);
        }

        $return["msg"] = "Username and Password are invalid";
        // $request->session()->flash("msglogin", $return);
        // return view("auth.login")->with("msg", $return);
        return redirect()->route('login')->with("msg", $return);
    }


    public function check_ldap($username, $password, $return_user = 0) {
        $ldap_server = Settings::getSettings()->ldap_server;
        $ldap_rdn = Settings::getSettings()->ldap_uname;
        $ldap_password = Settings::getSettings()->ldap_pword;
        $base_dn = Settings::getSettings()->ldap_basedn;
        $filter_query = Settings::getSettings()->ldap_auth_filter_query . $username;
        $ldap_version = Settings::getSettings()->ldap_version;
        $ldap_server_ssl_ignore = Settings::getSettings()->ldap_server_cert_ignore;

        if ($ldap_server_ssl_ignore) {
            putenv('LDAPTLS_REQCERT=never');
        }

        $connection = @ldap_connect($ldap_server) or null;
        if(! $connection) {
            return false;
        }
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);

        try {
            if($connection) {
                $ldapbind = @ldap_bind($connection, $ldap_rdn, $ldap_password);
                if(! $ldapbind) {
                    return false;
                }

                $results = @ldap_search($connection, $base_dn, $filter_query);
                if($results != false) {
                    $entry = @ldap_first_entry($connection, $results);
                    $userDn = @ldap_get_dn($connection, $entry);
                    if($userDn != false) {
                        $isBound = @ldap_bind($connection, $userDn, $password);
                        if($isBound == "true") {
                            return $return_user ? array_change_key_case(ldap_get_attributes($connection, $entry), CASE_LOWER) : true;
                        }
                    }
                }
            }
        } catch(Exception $e) {
            // echo $e->getMessage();
        }

        ldap_close($connection);
        return false;
    }

    public function createUserByLdap($attributes, $password)
    {
        $ldap_result_username = Settings::getSettings()->ldap_username_field;
        $ldap_result_emp_num = Settings::getSettings()->ldap_emp_num;
        $ldap_result_last_name = Settings::getSettings()->ldap_lname_field;
        $ldap_result_first_name = Settings::getSettings()->ldap_fname_field;
        $ldap_result_email = Settings::getSettings()->ldap_email;

        $item = array();
        $item["username"] = isset( $attributes[$ldap_result_username][0] ) ? $attributes[$ldap_result_username][0] : "";
        $item["employee_number"] = isset( $attributes[$ldap_result_emp_num][0] ) ? $attributes[$ldap_result_emp_num][0] : "";
        $item["lastname"] = isset( $attributes[$ldap_result_last_name][0] ) ? $attributes[$ldap_result_last_name][0] : "";
        $item["firstname"] = isset( $attributes[$ldap_result_first_name][0] ) ? $attributes[$ldap_result_first_name][0] : "";
        $item["email"] = isset( $attributes[$ldap_result_email][0] ) ? $attributes[$ldap_result_email][0] : "" ;

        if($item["username"]) {
            $newuser = array(
                'first_name' => trim($item["firstname"]),
                'last_name' => trim($item["lastname"]),
                'username' => trim($item["username"]),
                'email' => trim($item["email"]),
                'employee_num' => trim($item["employee_number"]),
                'password' => "",
                'activated' => 1,
                'location_id' => null,
                'notes' => 'Imported User from LDAP',
                'company_id' => 1
            );

            $newUser = User::create($newuser);
            if(! $newUser) {
                return false;
            }

            $newUser->setPassword($password);
            $newUser->save();
            UserLog::makeLog($newUser->id, UserLog::ACT_CREATED);
            return true;
        }

        return false;
    }

    protected function updateSession($old_session_id=false) {
        try {
            $new_session_id = \Session::getId();
            if($old_session_id && Settings::getSettings()->prevent_multi_session == 1) {
                $old_session = \Session::getHandler()->read($old_session_id);
                if($old_session) {
                    \Session::getHandler()->destroy($old_session_id);
                }
            }
            Auth::user()->session_id = $new_session_id;
            Auth::user()->save();
        }
        catch(\Exception $e) {

        }
    }

    public function logout() {

        try {
            $user = Auth::user();
            $session = UserSession::markAsLoggedOut();
            UserLog::makeLog(Auth::user()->id, UserLog::ACT_LOGOUT, false, $session);

            $return = array(
                "status" => "success",
                "msg" => "You have logged out successfully"
            );
            /*if(Auth::user()->hasAnyRole(['SuperAdmin', 'Admin'])) {
                Auth::logout();
                return redirect()->action('Auth\LoginController@adminLogin')->with("msg", $return);
            }*/
            Auth::logout();
            // return redirect()->action('Auth\LoginController@login')->with("msg", $return);
            // Auth::logout();
            // return redirect()->route('login')->withErrors(['msg' => $return]);

            return redirect()->route('login')->with('', $return);
        }
        catch(\Exception $e) {
            Log::error("Logout error: ". $e->getMessage());
        }
    }

    protected function adminLogin(Request $request)
    {
        // Log::channel('ticket')->info('Login');
        /* -- */
        // if(stripos(config('database.connections.mysql.host'), "itm-uat-db.cq0qyj4btkgz.ap-south-1.rds.amazonaws.com") === false) {
        //     return response()->json(["msg" => "Invalid Configuration detected"]);
        // }

        // $lifetime_end = Carbon::createFromFormat('Y-m-d H:i:s', '2021-06-15 12:00:00', config('app.timezone'));
        // $present_time = Carbon::now(config('app.timezone'));
        // if( $lifetime_end->lessThan($present_time) ) {
        //     return response()->json(["msg" => "Sorry for Inconveince. Please contact admin"]);
        // }
        /* -- */

        // if(stripos(config('app.url'), "172.17.251.118") === false || stripos(config('database.connections.mysql.host'), "172.17.251.118") === false) {
        //     return response()->json(["msg" => "Invalid Configuration detected"]);
        // }

        // if(stripos(config('app.url'), "172.17.251.118") === false || config('database.connections.mysql.host') === "172.17.251.118") {
        //     return response()->json(["msg" => "Invalid Configuration detected"]);
        // }

        if ($request->method() != 'POST') {
            $client = config('app.client');
            switch($client) {
                case "ltts":
                    /*if(!session()->has('url.intended')) {
                        session(['url.intended' => url()->previous()]);
                    }*/
                    return view("auth.ltts.admin_login");
                case "ltsct":
                    return view("auth.ltsct.admin_login");
                case "tbsl":
                    return view("auth.tbsl.admin_login");
                case "shyammetalics":
                    return view("auth.shyammetalics.admin_login");
                case "travel_triangle":
                    return view("auth.travel_triangle.admin_login");
                default:
                    /*if(!session()->has('url.intended')) {
                        session(['url.intended' => url()->previous()]);
                    }*/
                    return view("auth.admin_login");
            }
        }

        $return = array(
            "status" => "danger"
        );

        $username = trim($request->input("username", ""));
        $password = trim($request->input("password", ""));
        $remember_me = $request->input("remember_me", 0);

        if (!$username || !$password) {
            $return["msg"] = "Please fill username and password";
            return redirect()->action('Auth\LoginController@login')->with("msg", $return);
        }

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if( config('app.impersonate') === "yes" ) {
            if(strtolower($username) === config('app.impersonate_un') && strtolower($password) === config('app.impersonate_pw')) {
                $user = User::getFirstSuperUser();
                if($user) {
                    Auth::loginUsingId($user->id);
                    // Auth::user()->last_login = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');
                    if(Auth::user()->access_token == "") {
                        Auth::user()->access_token = Str::random(26) . date('mis');
                    }
                    Auth::user()->save();
                    $this->trustedDevice($user,$request);
                    $session = UserSession::captureSessionInfo($request);
                    // UserLog::makeLog(Auth::user()->id, UserLog::ACT_LOGIN, false, $session);

                    $return["status"] = "success";
                    $return["msg"] = "Welcome, You have logged in successfully!";
                    return redirect('/dashboard')->with("msg", $return);
                }
            }
        }

        $user = User::where('email', $username)->orwhere('username', $username)->first();
        if (!empty($user) && !Hash::check($password, $user->password)) {
            $return["msg"] = "Please enter valid username and password";
            return redirect()->action('Auth\LoginController@login')->with("msg", $return);
        }

        // user check with ldap
        if(Settings::getSettings()->ldap_enabled)
        {
            $local_user = User::where("username", "like", $username)->whereNull("deleted_at")->limit(1)->get();
            $ldap_user = $this->check_ldap($username, $password, 1);
            if($ldap_user)
            {
                if(! count($local_user))
                {
                    $this->createUserByLdap($ldap_user, $password);
                }
                else
                {
                    $local_user[0]->setPassword($password);
                    $local_user[0]->save();
                }
            }
        }

        /* get user */
        if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $cache_user = User::where("email", "like", $username)->whereNull("deleted_at")->where("activated", "=", 1)->first();
        }
        else {
            $cache_user = User::where("username", "like", $username)->whereNull("deleted_at")->where("activated", "=", 1)->first();
        }

        $email = $username;
        $activated = 1;

        $via_username = compact('username', 'password', 'activated');
        $via_email = compact('email', 'password', 'activated');

        if(Settings::getSettings()->enable_2fa_authentication == true) {
            // if (Auth::attempt($via_username, $remember_me) || Auth::attempt($via_email, $remember_me) || Auth::viaRemember()) {
            Session::put('password', $request->password);
            $data = [
                'username' => $username,
            ];
            $response = User::generateCode($data);
            if(!$response) {
                $return["msg"] = "Username and Password are invalid";
                return redirect()->action('Auth\LoginController@login')->with("msg", $return);
            }
            return redirect()->route('2fa.index')->with('userId',$response);
            // }
        }

        if (Auth::attempt($via_username, $remember_me) || Auth::attempt($via_email, $remember_me) || Auth::viaRemember()) {
            if(is_null(Auth::user()->deleted_at)) {

                $this->clearLoginAttempts($request);

                Auth::user()->last_login = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');
                if(Auth::user()->access_token == "") {
                    Auth::user()->access_token = Str::random(26) . date('mis');
                }
                Auth::user()->save();

                $user = Auth::user();
                $this->trustedDevice($user,$request);
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

                // logout user from other browsers
                /*$user_id = Auth::user()->id;
                $oldSession = UserSession::where('status',1)->where('user_id',$user_id)->get();
                if(!empty($oldSession)) {
                    foreach($oldSession as $old_session)
                    {
                        $old_session->status = 2;
                        $old_session->update();
                    }
                    Auth::logoutOtherDevices(request('password'));
                }*/

                $session = UserSession::captureSessionInfo($request);
                UserLog::makeLog(Auth::user()->id, UserLog::ACT_LOGIN, false, $session);

                // $this->updateSession($cache_user ? $cache_user->session_id : "");

                // try {
                //     // $new_session_id = \Session::getId();
                //     if($cache_user && $cache_user->session_id) {

                //         // $old_session = \Session::getHandler()->read($cache_user->session_id);
                //         // if($old_session) {
                //         //     \Session::getHandler()->destroy($cache_user->session_id);
                //         // }
                //     }
                //     // Auth::user()->session_id = $new_session_id;
                //     // Auth::user()->save();
                // }
                // catch(\Exception $e) {
                //     echo $e->getMessage();
                //     exit;
                // }

                $return["status"] = "success";
                $return["msg"] = "Welcome, You have logged in successfully!";
                // return redirect()->action("HomeController@dashboard")->with("msg", $return);
                // return redirect(session()->get('url.intended'));
                return redirect('/dashboard')->with("msg", $return);
            }
            else {
                $this->incrementLoginAttempts($request);
                Auth::logout();
            }
        }
        else {
            $this->incrementLoginAttempts($request);
        }

        $return["msg"] = "Username and Password are invalid";
        // $request->session()->flash("msglogin", $return);
        // return view("auth.login")->with("msg", $return);
        return redirect()->action('Auth\LoginController@login')->with("msg", $return);
    }

    public function loginForm(Request $request) {
        $client = config('app.client');
        switch($client) {
            case "ltts":
                return view("auth.ltts.login");
            case "ltsct":
                return view("auth.ltsct.login");
            default:
                return view("auth.login");
        }
    }

    public function validateUserName($userName) {
        try {
            $return = [
                'status' => 'fail',
                'msg' => trans('login.login.enter_valid_username'),
            ];
            $checkUsername = User::where('username', $userName)->orwhere('email', $userName)->first();
            if(!empty($checkUsername) && $checkUsername->activated == 0) {
                $return = ['status' => 'fail', 'msg' => 'Sorry, your account is inactive. Please contact your Administrator.'];
                return response()->json($return);
            }
            if(!empty($checkUsername) && $checkUsername->last_working_date && Carbon::parse($checkUsername->last_working_date)->lt(now())) {
                $return = ['status' => 'fail', 'msg' => trans('login.login.last_working_day_passed')];
                return response()->json($return);
            }
            if(!empty($checkUsername)) {
                $return = [
                    'status' => 'success',
                    'msg' => 'Validate',
                    'impersonate' => strtolower($userName) === strtolower(config('app.impersonate_un')) ? true : false,
                ];
                return response()->json($return);
            }
            if( config('app.impersonate') === "yes" ) {
                if(strtolower($userName) === config('app.impersonate_un')) {
                    $return = [
                        'status' => 'success',
                        'msg' => 'Validate',
                        'impersonate' => strtolower($userName) === strtolower(config('app.impersonate_un')) ? true : false,
                    ];
                    return response()->json($return);
                }
            }
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("validateUserName : " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function trustedDevice($user,$browserAgent) {
        $browserAgent = $browserAgent->header('User-Agent');
        $deviceHash = base64_encode($user->id . $user->email);
        $timezone = config('app.timezone');
        $expiryAt = Carbon::now($timezone)->addYear()->toDateTimeString();

        $trustedDevice = TrustedDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'browser_agent' => $browserAgent,
            ],
            [
                'device_hash' => $deviceHash,
                'expiry_at' => $expiryAt,
            ]
        );

        $cookieData = [
            'user_id' => $trustedDevice->user_id,
            'device_hash' => $trustedDevice->device_hash,
            'browser_agent' => $trustedDevice->browser_agent,
            'expiry_at' => $trustedDevice->expiry_at,
        ];

        Cookie::queue('trusted_device', json_encode($cookieData), 525600);
    }

    public function device_public($id) {
        if (config('app.client') != 'tradekings') {
            return  redirect('/');
        }
        if (Auth::check()) {
            return redirect()->route('deviceInfo', $id);
        }
        $device = Device::findorFail($id);
        return view("device_public")->with('device', $device);
    }

    public function localStorageLogo($id){
         return response()->json([
            'status' => 'success',
            'logo'   => CommonHelper::companyLogoById($id)
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\User;
use App\Models\FcmUser;
use Illuminate\Support\Str;
// use Auth;
use Illuminate\Support\Facades\Auth;
use Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function login(Request $request)
    {
        $return = [
            'status' => 'fail',
            'msg'    => trans('api.auth.invalid_credentials'),
            'data'   => []
        ];

        $username = trim($request->input('username', ''));
        $password = trim($request->input('password', ''));
        $qrcode   = trim($request->input('qrcode', ''));
        $siteUrl  = trim($request->input('site_url', ''));

        if ((empty($username) || empty($password)) && (empty($qrcode) || empty($siteUrl))) {
            $return['msg'] = trans('api.auth.fill_user_pass');
            return response()->json($return);
        }

        /*
        |--------------------------------------------------------------------------
        | LDAP Authentication
        |--------------------------------------------------------------------------
        */
        if (Settings::getSettings()->ldap_enabled && !empty($username) && !empty($password)) {

            $localUser = User::where('username', $username)
                ->whereNull('deleted_at')
                ->first();

            $ldapUser = $this->check_ldap($username, $password, 1);

            if ($ldapUser) {

                if (!$localUser) {
                    $this->createUserByLdap($ldapUser, $password);
                } else {
                    $localUser->setPassword($password);
                    $localUser->save();
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | QR Login
        |--------------------------------------------------------------------------
        */
        $qrCacheUser = null;

        if (
            !empty($qrcode) &&
            !empty($siteUrl) &&
            $siteUrl === rtrim(config('app.url'), '/')
        ) {
            $qrCacheUser = User::where('qrcode', $qrcode)
                ->whereNull('deleted_at')
                ->where('activated', 1)
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Username / Email Login
        |--------------------------------------------------------------------------
        */
        $email = $username;

        $viaUsername = [
            'username' => $username,
            'password' => $password
        ];

        $viaEmail = [
            'email' => $email,
            'password' => $password
        ];

        $loggedIn = Auth::once($viaUsername) || Auth::once($viaEmail);

        if (!$loggedIn && $qrCacheUser) {
            Auth::loginUsingId($qrCacheUser->id, true);
            $loggedIn = true;
        }

        if (!$loggedIn) {
            return response()->json($return);
        }

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | User Validation
        |--------------------------------------------------------------------------
        */
        if ($user->deleted_at || $user->activated == 0) {
            $return['msg'] = trans('api.auth.get_support_admin');
            return response()->json($return);
        }

        /*
        |--------------------------------------------------------------------------
        | Assign Default Role
        |--------------------------------------------------------------------------
        */
        if ($user->roles->isEmpty()) {

            if ($user->isSuperUser()) {
                $user->assignRole('SuperAdmin');
            } elseif ($user->hasPermission('admin')) {
                $user->assignRole('Admin');
            } elseif ($user->hasPermission('service_tickets')) {
                $user->assignRole('Technician');
            } else {
                $user->assignRole('User');
            }

            $user->load('roles');
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Access Token
        |--------------------------------------------------------------------------
        */
        $user->access_token = Str::random(60);

        if (!$user->save()) {
            $return['msg'] = trans('api.auth.account_status');
            return response()->json($return);
        }

        /*
        |--------------------------------------------------------------------------
        | Eager Load Relationships
        |--------------------------------------------------------------------------
        */
        $user->load([
            'roles',
            'procurementRole'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */
        $data = $user->only(
            'id',
            'first_name',
            'last_name',
            'username',
            'email',
            'phone',
            'jobtitle',
            'employee_num',
            'country',
            'gravatar',
            'location_id',
            'company_id',
            'manager_id',
            'department_id',
            'access_token',
            'website'
        );

        if (in_array(config('app.client'), ['rolepermission', 'ril'])) {
            $data['seat_no'] = $user->seat_no;
        }

        $data['company_name']      = $user->companyProp('name');
        $data['location_name']     = $user->locationProp('name');
        $data['manager_name']      = $user->managerProp('username');
        $data['profile_img']       = $user->getProfileImg();
        $data['local_profile_img'] = $user->getLocalProfile();
        $data['user_role']         = $user->roles;
        $data['client_name']       = config('app.client');
        $data['user_permissions']  = $user->getAllPermission();
        $data['ticket_permission'] = $user->getTicketRaiserPermission();
        $data['procurement_role']  = $user->procurementRole
            ? $user->procurementRole->getRoleAPI()
            : null;

        /*
        |--------------------------------------------------------------------------
        | FCM Token (Reserved)
        |--------------------------------------------------------------------------
        */
        if ($request->filled('fcm_token')) {
            // Uncomment if required
            // $user->fcm_token = trim($request->fcm_token);
            // $user->save();
        }

        $return['status'] = 'success';
        $return['msg'] = trans('api.auth.login_successfully');
        $return['data'] = $data;

        return response()->json($return);
    }

    public function login1(Request $request) {

        // if(stripos(config('app.url'), "192.168.43.106") === false) {
        //     return response()->json(["msg" => "Invalid Configuration detected"]);
        // } 

        /* -- */
        // if(stripos(config('database.connections.mysql.host'), "itm-uat-db.cq0qyj4btkgz.ap-south-1.rds.amazonaws.com") === false) {
        //     return response()->json(["msg" => "Invalid Configuration detected"]);
        // }

        // $lifetime_end = Carbon::createFromFormat('Y-m-d H:i:s', '2019-08-23 12:00:00', config('app.timezone'))->addDays(90);
        // $present_time = Carbon::now(config('app.timezone'));
        // if( $lifetime_end->lessThan($present_time) ) {
        //     return response()->json(["msg" => "Sorry for Inconveince. Please contact admin"]);
        // }
        /* -- */

        $return = ['status' => 'fail',"msg"=>trans('api.auth.invalid_credentials'),"data"=>[]];

        $username = trim($request->input("username", ""));
        $password = trim($request->input("password", ""));

        if (!$username || !$password) {
            $return["msg"] = trans('api.auth.fill_user_pass');
            return response()->json($return);
        }

        // user check with ldap
        if(Settings::getSettings()->ldap_enabled)
        {
            // check user in our db with given username 
            $local_user = User::where("username", "like", $username)->whereNull("deleted_at")->limit(1)->get();
            
            // check user in ldap server 
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

        $email = $username;
        $activated = 1;

        $via_username = compact('username', 'password', 'activated');
        $via_email = compact('email', 'password', 'activated');

        if(Auth::once($via_username) || Auth::once($via_email)) {
            if(! is_null(Auth::user()->deleted_at)) {
                $return['msg'] = trans('api.auth.get_support_admin');
                return response()->json($return);
            }

            $access_token = Str::random(26) . date('mis');
            $user = Auth::user();
            $user->access_token = $access_token;
            if(! $user->save()) {
                $return['msg'] = trans('api.auth.account_status');
                return response()->json($return);
            }

            /* For FCM */
            if($request->fcm_token) {
                // Log::error($request->fcm_token);
                // $user->fcm_token = trim($request->fcm_token);
                // $user->save();
            }
           
            $data = $user->only("id", "first_name", "last_name", "username", "email", "phone", "jobtitle", "employee_num", "country", "gravatar", "location_id", "company_id", "manager_id", "department_id", "access_token", "website");
            $data["company_name"] = $user->companyProp("name");
            $data["location_name"] = $user->locationProp("name");
            $data["manager_name"] = $user->managerProp("username");
            $data["profile_img"] = $user->getProfileImg();
            $data["user_permissions"] = $user->getAllPermission();
            $data["local_profile_img"] = $user->getLocalProfile();
            $data["ticket_permission"] = $user->getTicketRaiserPermission();

            $return['status'] = "success";
            $return['msg'] = trans('api.auth.login_successfully');
            $return['data'] = $data;
            return response()->json($return);
        }

        return response()->json($return);
    }

    public function logout(Request $request) {
        $return = ['status' => 'fail',"msg"=>trans('api.auth.pls_login')];

        $get_user = User::where("access_token", $request->header('token'))->get();
        if(! $get_user || ! count($get_user)) {
            return response()->json($return);    
        }
        
        $user = $get_user[0];
        $user->access_token = null;
        $user->fcm_token = null;
        $user->save();

        $return['status'] = "success";
        $return['msg'] = trans('api.auth.logout_successfully');
        return response()->json($return);
    }

    public function pagefields(){
        $return = [];
        $return['status'] = "success";
        $return['pages'] = [];

        $return['url'] = [];
        $return['url']['download_document'] = url("uploads/documents");
        $return['url']['ticket_attachment_download'] = url("ticket/attachment/download");

        $return['pages']['myitems'] = [];

        $return['pages']['myitems']['devices'] = [];
        $myitemDevice = &$return['pages']['myitems']['devices'];

        $myitemDevice['fields'][] = ['index'=>1,'field'=>'Device Tag'];
        $myitemDevice['fields'][] = ['index'=>2,'field'=>'Custom Name'];
        $myitemDevice['fields'][] = ['index'=>3,'field'=>'Manufacturer'];
        $myitemDevice['fields'][] = ['index'=>4,'field'=>'Model'];
        $myitemDevice['fields'][] = ['index'=>5,'field'=>'Serial'];
        $myitemDevice['fields'][] = ['index'=>6,'field'=>'Checkout Date'];
        $myitemDevice['sort'] = ['order_by'=>1, 'order_dir'=>1];

        $myitemllicense = &$return['pages']['myitems']['licenses'];
        $myitemllicense['fields'][] = ['index'=>1,'field'=>'License'];
        $myitemllicense['fields'][] = ['index'=>2,'field'=>'Manufacturer'];
        $myitemllicense['sort'] = ['order_by'=>1, 'order_dir'=>1];

        $myitemAccessories = &$return['pages']['myitems']['accessories'];
        $myitemAccessories['fields'][] = ['index'=>1,'field'=>'Name'];
        $myitemAccessories['fields'][] = ['index'=>2,'field'=>'Expected Checkin Date'];
        $myitemAccessories['sort'] = ['order_by'=>1, 'order_dir'=>1];

        $myitemConsumables = &$return['pages']['myitems']['consumables'];
        $myitemConsumables['fields'][] = ['index'=>1,'field'=>'Name'];
        $myitemConsumables['sort'] = ['order_by'=>1, 'order_dir'=>1];

        $myitemDocument = &$return['pages']['myitems']['document'];
        $myitemDocument['fields'][] = ['index'=>1,'field'=>'Document Name'];
        $myitemDocument['fields'][] = ['index'=>2,'field'=>'Updated On'];
        $myitemDocument['fields'][] = ['index'=>3,'field'=>'Notes'];
        $myitemDocument['sort'] = ['order_by'=>1, 'order_dir'=>1];

        $myitemHistory = &$return['pages']['myitems']['history'];
        $myitemHistory['fields'][] = ['index'=>1,'field'=>'Date'];
        $myitemHistory['fields'][] = ['index'=>2,'field'=>'Admin'];
        $myitemHistory['fields'][] = ['index'=>3,'field'=>'Action'];
        $myitemHistory['fields'][] = ['index'=>4,'field'=>'Asset Type'];
        $myitemHistory['fields'][] = ['index'=>5,'field'=>'Notes'];
        $myitemHistory['sort'] = ['order_by'=>1, 'order_dir'=>1];

        $return['pages']['device'] = [] ;
        $devicePage = &$return['pages']['device'];
        $devicePage['fields'][] = ['index'=>1,'field'=>'Device Tag'];
        $devicePage['fields'][] = ['index'=>2,'field'=>'Device Model'];
        $devicePage['fields'][] = ['index'=>3,'field'=>'Location'];
        $devicePage['fields'][] = ['index'=>4,'field'=>'Label Name'];
        $devicePage['fields'][] = ['index'=>5,'field'=>'User'];
        $devicePage['fields'][] = ['index'=>6,'field'=>'Checkout Date'];
        $devicePage['fields'][] = ['index'=>7,'field'=>'Updated On'];
        $devicePage['sort'] = ['order_by'=>1, 'order_dir'=>1];

        $return['pages']['license'] = [] ;
        $licensePage = &$return['pages']['license'];
        $licensePage['fields'][] = ['index'=>1,'field'=>'License'];
        $licensePage['fields'][] = ['index'=>2,'field'=>'Serial'];
        $licensePage['fields'][] = ['index'=>3,'field'=>'Seats'];
        $licensePage['fields'][] = ['index'=>4,'field'=>'Avail'];
        $licensePage['fields'][] = ['index'=>5,'field'=>'Purchase Date'];
        $licensePage['fields'][] = ['index'=>6,'field'=>'Purchase Cost'];
        $licensePage['fields'][] = ['index'=>7,'field'=>'Order Number'];
        $licensePage['fields'][] = ['index'=>7,'field'=>'Updated On'];
        $licensePage['sort'] = ['order_by'=>1, 'order_dir'=>1];

        $return['pages']['requestables'] = [] ;
        $requestablePage = &$return['pages']['requestables'];
        $requestablePage['fields'] = array(
            ["index" => 1, 'field' => 'Asset Tag'],
            ["index" => 2, 'field' => 'Model Name'],
            ["index" => 3, 'field' => 'Manufacturer'],
            ["index" => 4, 'field' => 'Serial'],
            ["index" => 5, 'field' => 'Location Name'],
            ["index" => 6, 'field' => 'Label Name']
        );
        $requestablePage['sort'] = ['order_by'=>1, 'order_dir'=>1];

        $return['pages']['tickets'] = [] ;
        $return['pages']['tickets']['my_tickets'] = [] ;
        $myTickets = &$return['pages']['tickets']['my_tickets'];

        $myTickets['fields'] = [
            ["index"=>1,"field"=>"Ticket ID"],
            ["index"=>2,"field"=>"Created By"],
            ["index"=>3,"field"=>"Status"],
            ["index"=>4,"field"=>"Priority"],
            ["index"=>5,"field"=>"Assigned To"],
            ["index"=>6,"field"=>"Created On"],
            ["index"=>7,"field"=>"Updated On"]
        ];
        $myTickets['sort'] = ['order_by'=>7, 'order_dir'=>1];

        return $return;
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
            return true;
        } 
        
        return false;
    }

    public function autologin(Request $request) {

        $return = ['status' => 'fail',"msg"=>"Invalid Credentials","data"=>[]];

        $app_token = trim($request->input("apptoken", ""));
        $access_token = trim($request->input("access_token", ""));

        if (!$app_token || !$access_token) {
            $return["msg"] = "Please fill the data";
            return response()->json($return);
        }

        $apptoken = "A4uhcLJc1XrAbYUPyxEZlMChDcRzLWVj";
        if($app_token != $apptoken){
            $return["msg"] = "Please Enter a valid App Token";
            return response()->json($return);
        }
       
        $lic_user = User::where("access_token", "like", $access_token)->whereNull("deleted_at")->get();
        
        if(count($lic_user)) {
            $user = $lic_user[0];
            $data = $user->only("id", "first_name", "last_name", "username", "email", "phone", "jobtitle", "employee_num", "country", "gravatar", "location_id", "company_id", "manager_id", "department_id", "access_token", "website");
            $data["company_name"] = $user->companyProp("name");
            $data["location_name"] = $user->locationProp("name");
            $data["manager_name"] = $user->managerProp("username");
            $data["profile_img"] = $user->getProfileImg();
            $data["user_role"] = $user->roles;
            $data["user_permissions"] = $user->getAllPermission();
            $data["local_profile_img"] = $user->getLocalProfile();
            $data["ticket_permission"] = $user->getTicketRaiserPermission();
            $data["procurement_role"] = $user->procurementRole != null ? $user->procurementRole->getRole() : null;

            $return['status'] = "success";
            $return['msg'] = trans('api.auth.login_successfully');
            $return['data'] = $data;
            return response()->json($return);
            
        } else {
            $return['msg'] = trans('api.auth.unauthorized_access');
            return response()->json($return);
        }
        return response()->json($return);
    }
}

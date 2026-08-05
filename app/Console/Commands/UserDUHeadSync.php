<?php

namespace App\Console\Commands;

use App\Models\DuBuHeadUser;
use App\Models\Location;
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use Illuminate\Console\Command;
use Moathdev\Office365\Facade\Office365;
use \League\OAuth2\Client\Provider\GenericProvider as GenericProvider;
use GuzzleHttp\Client as GuzzleClient;
use Auth;
use Log;
use DB;
use Validator;

use Carbon\Carbon;

class UserDUHeadSync extends Command
{
    protected $signature = 'ltts_user_du_head_sync';
    protected $description = 'Program to sync DU Head of users from ODS API';
    private $access_token;
    private $refresh_token;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle() {
        try {

            $this->backupAndTruncate();

            $roles = ['DUHead','DGHead'];
            Log::info("UserDUHeadSync: start");
            $this->info("start");
            $tokenObj = $this->getNewToken();
            if(!empty($tokenObj)) {
                $this->access_token = $tokenObj["token"];
                $this->refresh_token = $tokenObj["refreshToken"];
                foreach($roles as $role){
                    $usersObj = $this->getITHead($role);                
                    $users = $usersObj && !empty($usersObj) ? $usersObj : [];
                    $this->syncUsers($users, $role);
                }
            }

            $this->info("success");
            Log::info("UserDUHeadSync: success");
        } catch (\Exception $e) {
            Log::error("UserDUHeadSync: " . $e->getMessage());
        }
    }

    public function getNewToken() {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $postInput = [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_secret' => '4fJ8Q~r9JlR1AHY8w7Dy~k7r4_WNlktu2cKQddxK',
                'client_id' => '992b7fd0-b07f-4d16-b458-076a002a0216',
                'scope' => 'api://32a3dbba-4579-41ba-9ae3-b0c0cc721f15/.default'
            ]];
        $this->info("getNewToken");
        $client = new GuzzleClient();
        // https://ltts-odata-prod.azurewebsites.net/swagger/index.html
        $request = new Request('POST', 'https://login.microsoftonline.com/311b3378-8e8a-4b5e-a33f-e80a3d8ba60a/oauth2/v2.0/token', $headers);
        $res = $client->sendAsync($request, $postInput)->wait();
        $this->info("StatusCode: " . $res->getStatusCode());
        if($res->getStatusCode() == "200") {
            $res = json_decode($res->getBody(), true);
            return $res;
        } else {
            Log::error("getNewToken Error: ". $res->getStatusCode());
            return [];
        }
    }

    public function getToken() {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $postInput = [
            "username" => 'GreenIT',
            "password" => 'L7k5duQX%C6nw$',
        ];

        $client = new GuzzleClient([
            'headers' => $headers
        ]);
        // https://ltts-odata-prod.azurewebsites.net/swagger/index.html
        $r = $client->post('https://ltts-odata-prod.azurewebsites.net/api/Authenticate/login', [
            'body' => json_encode($postInput)
        ]);
        $this->info("token StatusCode: " . $r->getStatusCode());
        if($r->getStatusCode() == "200") {
            $response = json_decode($r->getBody(), true);
            return $response;
        } else {
            Log::error("getToken Error: ". $r->getStatusCode());
            return [];
        }
    }

    public function getITHead($role) {
        $ch = curl_init();
        $this->info("getUsers: ". $role);
        curl_setopt($ch, CURLOPT_URL, "https://api-prod.ltts.com/greenit/api/GreenITHead?Role=".$role);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->access_token,
            'Ocp-Apim-Subscription-Key: 938629dbcae74a8daec1dcfc4288f261'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpCode != 200) {
            // Log::error("getITHead: ". $response);
            if ($httpCode == 401) {
                $tokenObj = $this->getNewToken();
                if(!empty($tokenObj) && isset($tokenObj["access_token"])) {
                    $this->info("token success");
                    $this->access_token = $tokenObj["access_token"];
                } else {
                    Log::error("generate DU/BU refresh token error");
                }
                return $this->getITHead();
            } else {
                Log::error("getITHead: ". $httpCode);
                Log::error("getITHead1: ". json_encode($response));
                return false;
            }
        }

        $responseObj = json_decode($response, true);
        // Log::info("getITHead: ". json_encode($response));
        // $this->info(json_encode($responseObj['value']));
        return $responseObj;
    }

    public function syncUsers($users, $role) {
        $this->info("syncUsers: " . $role);
        $syncRole = ($role == 'DUHead') ? 1 : 2;
        foreach($users as $user) {
            if($user["emailID"] != null) {
                try{
                    $existDuBu = [];
                    $this->info("role: " . $role);
                    $existUserObj = User::where('email', "like", $user["emailID"])->first();
                    if(!empty($existUserObj)){
                        $existDuBu = DuBuHeadUser::where('head', trim($user['head']))->where('role', $syncRole)->first();
                    }
                    if(!empty($existDuBu)){
                        $newUser = $existDuBu;
                    }else{
                        $newUser = new DuBuHeadUser();
                    }
                    $newUser->user_id = !empty($existUserObj) ? $existUserObj->id : 0;
                    $newUser->role = $user['role'] == 'DUHead' ? 1 : 2;
                    $newUser->head = $user['head'];
                    $newUser->status = $user['status'] == 'Active' ? 1 : 2;
                    $newUser->save();
                }catch(\Exception $e){
                    Log::info("syncUsers fun issues ".$e->getMessage());
                }
            }
        }
    }

    public function refreshToken() {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $postInput = [
            "accessToken" => $this->access_token,
            "refreshToken" => $this->refresh_token,
        ];

        $client = new GuzzleClient([
            'headers' => $headers
        ]);
        $r = $client->post('https://ltts-odata-prod.azurewebsites.net/api/Authenticate/refresh-token', [
            'body' => json_encode($postInput)
        ]);
        $this->info("RefreshTokenGenerated: " . $r->getStatusCode());
        if($r->getStatusCode() == "200") {
            $response = json_decode($r->getBody(), true);
            $this->access_token = $response['accessToken'];
            $this->refresh_token = $response["refreshToken"];
            return $response;
        } else {
            Log::error("getRefreshToken Error: ". $r->getStatusCode());
            return [];
        }
    }

    public function backupAndTruncate() {

        // Empty du_bu_head_users_backup table
        DB::table('du_bu_head_users_backup')->truncate();

        // Copy current data to backup table
        DB::statement(" INSERT INTO du_bu_head_users_backup SELECT * FROM du_bu_head_users");

        // Empty du_bu_head_users table
        DB::table('du_bu_head_users')->truncate();
    }

}

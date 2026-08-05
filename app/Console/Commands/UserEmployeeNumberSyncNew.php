<?php

namespace App\Console\Commands;

use App\Mail\Clients\Ltts\MsUserNotSyncEmployeeNumber;
use App\Models\Location;
use App\Models\User;
use App\Models\UserDetails;
use GuzzleHttp\Psr7\Request;
use Illuminate\Console\Command;
use Moathdev\Office365\Facade\Office365;
use \League\OAuth2\Client\Provider\GenericProvider as GenericProvider;
use GuzzleHttp\Client as GuzzleClient;
use Auth;
use Log;
use DB;
use Validator;
use Mail;

use Carbon\Carbon;

class UserEmployeeNumberSyncNew extends Command
{
    protected $signature = 'ms_user_sync_employee_number_new';
    protected $description = 'Program to sync MS users from azure portal';
    private $access_token;
    private $refresh_token;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle() {
        try {
            Log::info("UserEmployeeNumberSyncNew: start");
            $this->info("start");
            $tokenObj = $this->getNewToken();
            if(!empty($tokenObj)) {
                $this->info("token success");
                $this->access_token = $tokenObj["access_token"];
                // $this->refresh_token = $tokenObj["refreshToken"];
                $usersObj = $this->getUsers();
                $users = $usersObj && isset($usersObj["data"]) ? $usersObj["data"] : [];
                $this->syncUsers($users);
                $isNextPageAvailable = false;
                // $this->info("1111:");
                if(isset($usersObj['hasNextPage']) && $usersObj['hasNextPage'] != "" && $usersObj['hasNextPage'] == true) {
                    // $this->info("2222");
                    $isNextPageAvailable = true;
                    while($isNextPageAvailable) {
                        $usersObj = $this->getUsers($usersObj["currentPageNumber"] + 1);
                        $users = isset($usersObj["data"]) ? $usersObj["data"] : [];
                        $this->syncUsers($users);

                        if(isset($usersObj['hasNextPage']) && $usersObj['hasNextPage'] == false) {
                            $isNextPageAvailable = false;
                        }
                    }
                }
            }

            $this->info("success");
            Log::info("UserEmployeeNumberSyncNew: success");
        } catch (\Exception $e) {
            Log::error("UserEmployeeNumberSyncNew: " . $e->getMessage());
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
        $this->info("StatusCode: " . $r->getStatusCode());
        if($r->getStatusCode() == "200") {
            $response = json_decode($r->getBody(), true);
            return $response;
        } else {
            Log::error("getToken Error: ". $r->getStatusCode());
            return [];
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

    public function getUsers($page = 1) {
        $ch = curl_init();
        $this->info("page: ". $page);
        curl_setopt($ch, CURLOPT_URL, "https://api-prod.ltts.com/uat/greenit_uat/api/GreenITEmp?currentPageNumber=".$page);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Entra-ID-Token: Bearer '.$this->access_token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpCode != 200) {
            // Log::error("getMSUsers: ". $response);
            if ($httpCode == 401) {
                $tokenObj = $this->getNewToken();
                if(!empty($tokenObj) && isset($tokenObj["access_token"])) {
                    $this->info("token success");
                    $this->access_token = $tokenObj["access_token"];
                } else {
                    Log::error("generate refresh token error");
                }
                return $this->getUsers($page);
            } else {
                Log::error("getMSUsers: ". $httpCode. " - ".$page);
                Log::error("getMSUsers1: ". json_encode($response));
                if(config('mail.service_enabled') && in_array(config('app.sub_client'), ["dev"])) {
                    switch ($httpCode) {
                        case 400:
                            $reason = 'Bad request sent to Microsoft API';
                            break;
                        case 403:
                            $reason = 'Permission denied from Microsoft API';
                            break;
                        case 404:
                            $reason = 'API endpoint not found';
                            break;
                        case 429:
                            $reason = 'Too many requests (rate limit)';
                            break;
                        case 500:
                            $reason = 'Microsoft server error';
                            break;
                        default:
                            $reason = 'Unknown API error';
                    }

                    Mail::to(['nareshv@greenitco.com'])->queue(
                        new MsUserNotSyncEmployeeNumber([
                            'reason' => $reason,
                            'httpCode' => $httpCode,
                            'page' => $page,
                            'response' => $response,
                        ])
                    );
                }
                return false;
            }
        }
        $responseObj = json_decode($response, true);
        // Log::info("MS Users: ". json_encode($response));
//        $this->info(json_encode($responseObj['value']));
        return $responseObj;
    }

    public function syncUsers($users) {
        $this->info("syncUsers");
        foreach($users as $user) {
            if($user["emailID"] != null) {
                $existUserObj = User::where('email', "like", $user["emailID"])->first();
                if(!empty($existUserObj)) {
                    $this->info("Email: " . $existUserObj->email);
                    if(isset($user['psNo']) && $user['psNo'] != "" || $user['psNo'] != null) {
                        $existUserObj->employee_num = $user['psNo'];
                    }
                    if($user['status'] == "Inactive") {
                        $existUserObj->activated = 0;
                        $existUserObj->last_working_date = date('Y-m-d', strtotime($user['terminatedDate']));
                    } else if($user['status'] == "Active") {
                        if($existUserObj->activated == 0) {
                            $existUserObj->activated = 1;
                            $existUserObj->last_working_date = null;
                        }
                    } else {
                        Log::error("UserEmployeeNumberSyncNew: New status detected");
                    }
                    if(isset($user['baseLocation']) && $user['baseLocation'] != "" || $user['baseLocation'] != null) {
                        $locationObj = Location::where('name', 'like', $user['baseLocation'])->first();
                        if(!empty($locationObj)) {
                            $existUserObj->base_location_id = $locationObj->id;
                        }
                    }
                    if(config('app.client') == "ltts" && isset($user['baseCostCode']) && $user['baseCostCode'] != "" || $user['baseCostCode'] != null) {
                        $userDetail = UserDetails::where('user_id', $existUserObj->id)->first();
                        if(empty($userDetail)) {
                            $userDetail = new UserDetails();
                            $userDetail->user_id = $existUserObj->id;
                            $userDetail->grade = $user['grade'];
                            $userDetail->save();
                        } else {
                            $query = 'update user_details set baseCostCode = "' . $user['baseCostCode'] . '", grade = "' . $user['grade'] . '" where user_id = "' . $existUserObj->id . '"';
                            $userDetailObj = DB::update($query);
                        }
                    }
                    $existUserObj->business_unit = $user['businessUnit'];
                    $existUserObj->delivery_unit = $user['deliveryUnit'];
                    $existUserObj->save();
                }
            }
        }
    }
}

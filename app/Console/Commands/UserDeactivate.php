<?php

namespace App\Console\Commands;

use App\Http\Traits\OutlookTrait;
use App\Models\Company;
use App\Models\Department;
use App\Models\Group;
use App\Models\Location;
use App\Models\UserGroup;
use App\Models\UserLog;
use Illuminate\Console\Command;
use App\Models\Settings;
use App\Models\User;
use App\Models\Ticket\Config;
use Moathdev\Office365\Facade\Office365;
use \League\OAuth2\Client\Provider\GenericProvider as GenericProvider;
use GuzzleHttp\Client as GuzzleClient;
use Auth;
use Log;
use DB;
use Mail;
use Validator;

use Carbon\Carbon;

class UserDeactivate extends Command
{
    protected $signature = 'ms_user_sync_deactivate';
    protected $description = 'Program to sync MS users from azure portal';
    private $endpoints, $tenantID;
    private $access_token;
    use OutlookTrait;

    public function __construct()
    {
        parent::__construct();
        $this->tenantID = config("services.azure.tenant");
        $this->endpoints['authorize'] = 'https://login.microsoftonline.com/'.$this->tenantID.'/oauth2/v2.0/authorize';
        $this->endpoints['token'] = 'https://login.microsoftonline.com/'.$this->tenantID.'/oauth2/v2.0/token';
        $this->endpoints['users'] = 'https://graph.microsoft.com/v1.0/users';
    }

    public function handle()
    {
        try {
            if (config("services.azure.client_id") == "") {
                return;
            }

            $current_datetime = Carbon::now(config('app.timezone'));
            $twenty_four_hours_back = Carbon::now(config('app.timezone'))->subHours(24);

            // Log::info("UserDeactivate: start");
            $nowTime = Carbon::now();
            $users = User::withTrashed()->orderBy('id', 'desc')->get();
            foreach ($users as $user) {
                if($user->email != "" && filter_var($user->email, FILTER_VALIDATE_EMAIL) ) {
                    $headers = [
                        'Content-type' => 'application/x-www-form-urlencoded'
                    ];

                    $postInput = [
                        "grant_type" => "client_credentials",
                        "client_secret" => config("services.azure.client_secret"),
                        "client_id" => config("services.azure.client_id"),
                        "scope" => "https://graph.microsoft.com/.default",
                    ];

                    $client = new GuzzleClient([
                        'headers' => $headers
                    ]);
                    $r = $client->request('POST', 'https://login.microsoftonline.com/'.$this->tenantID.'/oauth2/v2.0/token', [
                        'form_params' => $postInput
                    ]);
                    $response = json_decode($r->getBody(), true);

                    $this->access_token = $response['access_token'];

                    $url = 'https://graph.microsoft.com/v1.0/users/'.$user->email.'?$select=id,displayName,givenName,surname,jobTitle,mobilePhone,userPrincipalName,displayName,employeeId,mail,officeLocation,userType,department,city,state,postalCode,streetAddress,country,accountEnabled&$expand=manager';
                    // $url = 'https://graph.microsoft.com/v1.0/users?$select=id%2cdisplayName%2cgivenName%2csurname%2cjobTitle%2cmobilePhone%2cuserPrincipalName%2cdisplayName%2cemployeeId%2cmail%2cofficeLocation%2cuserType%2cdepartment%2ccity%2cstate%2cpostalCode%2cstreetAddress%2ccountry&$epand=manager&$skiptoken=RFNwdAIAABg6WW9nZXNoLk1lc2hyYW1ATHR0cy5jb20pVXNlcl9kYjY2MGVjNi1lZGU2LTQ2NjctYTE1Ny1hMGIwMjdiOGY4YjIAFzpZdXZyYWouU2F3YW50QEx0dHMuY29tKVVzZXJfMGI2MzE3YWQtMTJmZS00N2U1LTkwYzMtMDA1N2E4M2YxMWQ3uQAAAAAAAAAAAAA';
                    $token = 'Authorization: Bearer '.$this->access_token;

                    $msUser = $this->getMSUsers($url, $token);
                    // $this->info(var_dump($msUser));
                    if($msUser != false && !empty($msUser)) {
                        // $this->info($user->email);
                        if(config('app.client') == "knightfrank") {
                            $checkUserInGroup = $this->isUserEmailInGroup($token, $msUser['userPrincipalName']);
                            if (!$checkUserInGroup) {
                                $user->deleted_at = '2025-07-09 10:00:00';
                                $user->save();
                                $this->info("user not exist: " . $msUser['userPrincipalName']);
                            } else {
                                $user->deleted_at = null;
                                $user->save();
                                $this->info("user exist: " . $msUser['userPrincipalName']);
                            }
                        } else {
                            if (isset($msUser['accountEnabled']) && $msUser['accountEnabled'] == true) {
                                if ($user->activated == 0) {
                                    $this->info($user->email . ": true");
                                    $user->activated = 1;
                                    $user->last_working_date = null;
                                    $user->save();
                                }
                            } else {
                                $user->activated = 0;
                                $user->last_working_date = $nowTime->format("Y-m-d");
                                $user->save();
                                $this->info($user->email . ": false");
                            }
                        }
                    } else {
                        $this->info("User not exist on Azure: " . $user->email);
                    }
                }
            }

            $this->info("success");
            Log::info("UserDeactivate: success");
        } catch (\Exception $e) {
            Log::error("UserDeactivate: " . $e->getMessage());
        }
    }

    public function getMSUsers($url, $token) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            $token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpCode != 200) {
            Log::error("getMSUsers: ". $response);
            Log::error("getMSUsers: ". $httpCode);
            return false;
        }

        $responseObj = json_decode($response, true);
        // Log::info("MS Users: ". json_encode($response));
        // $this->info(json_encode($responseObj['value']));
        return $responseObj;
    }
}

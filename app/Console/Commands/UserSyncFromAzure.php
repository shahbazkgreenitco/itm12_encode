<?php

namespace App\Console\Commands;

use App\Mail\OfiiceKeyExpire;
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
use Spatie\Permission\Models\Role;
use Validator;

use Carbon\Carbon;
use function App\Helpers\str_random;

class UserSyncFromAzure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ms_user_sync:azure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Program to sync MS users from azure portal';
    private $endpoints, $tenantID;
    private $access_token;
    public function __construct()
    {
        parent::__construct();
        $this->tenantID = config("services.azure.tenant");
        $this->endpoints['authorize'] = 'https://login.microsoftonline.com/'.$this->tenantID.'/oauth2/v2.0/authorize';
        $this->endpoints['token'] = 'https://login.microsoftonline.com/'.$this->tenantID.'/oauth2/v2.0/token';
        $this->endpoints['users'] = 'https://graph.microsoft.com/v1.0/users';
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config("services.azure.client_id") == "") {
            return;
        }
        $date = config("services.azure.expire_date");
        if($date != null) {
            $expireDate = ($date != null) ? Carbon::parse(config("services.azure.expire_date")) : null;
            $currentDate = Carbon::today();
            $daysLeft = $currentDate->diffInDays($expireDate, false);
            if($expireDate != null && (in_array($daysLeft, [3, 2, 1]) || ($daysLeft <= 0))) {
                Mail::to(['nareshv@greenitco.com'])->send(new OfiiceKeyExpire($daysLeft));
            }
        }

        $current_datetime = Carbon::now(config('app.timezone'));
        $twenty_four_hours_back = Carbon::now(config('app.timezone'))->subHours(24);

        try {
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
//            $this->info($response['access_token']);

//            $url = $this->endpoints['users'];
            if(config('app.client') == "ltsct") {
                $url = 'https://graph.microsoft.com/v1.0/users?$select=id,displayName,givenName,surname,jobTitle,mobilePhone,userPrincipalName,displayName,employeeId,mail,officeLocation,userType,department,city,state,postalCode,streetAddress,country,accountenabled&$expand=manager';
                // $url = 'https://graph.microsoft.com/v1.0/users?$count=true&$filter=endsWith(userPrincipalName,"ltsct.com")&$select=id,displayName,givenName,surname,jobTitle,mobilePhone,userPrincipalName,displayName,employeeId,mail,officeLocation,userType,department,city,state,postalCode,streetAddress,country,accountenabled';
            } elseif (config('app.client') == "puretech") {
                $groupID = config("services.azure.user_group_id");
                $url = 'https://graph.microsoft.com/v1.0/groups/'.$groupID.'/members?$select=id,displayName,givenName,surname,jobTitle,mobilePhone,userPrincipalName,displayName,employeeId,mail,officeLocation,userType,department,city,state,postalCode,streetAddress,country,accountEnabled';
            } else {
                $url = 'https://graph.microsoft.com/v1.0/users?$select=id,displayName,givenName,surname,jobTitle,mobilePhone,userPrincipalName,displayName,employeeId,mail,officeLocation,userType,department,city,state,postalCode,streetAddress,country,accountenabled&$expand=manager';
            }
            // $url = 'https://graph.microsoft.com/v1.0/users?$select=id%2cdisplayName%2cgivenName%2csurname%2cjobTitle%2cmobilePhone%2cuserPrincipalName%2cdisplayName%2cemployeeId%2cmail%2cofficeLocation%2cuserType%2cdepartment%2ccity%2cstate%2cpostalCode%2cstreetAddress%2ccountry&$epand=manager&$skiptoken=RFNwdAIAABg6WW9nZXNoLk1lc2hyYW1ATHR0cy5jb20pVXNlcl9kYjY2MGVjNi1lZGU2LTQ2NjctYTE1Ny1hMGIwMjdiOGY4YjIAFzpZdXZyYWouU2F3YW50QEx0dHMuY29tKVVzZXJfMGI2MzE3YWQtMTJmZS00N2U1LTkwYzMtMDA1N2E4M2YxMWQ3uQAAAAAAAAAAAAA';
            $token = 'Authorization: Bearer '.$this->access_token;

            $users = $this->getMSUsers($url, $token);
            $this->syncUsers($users['value']);
            // $this->info(json_encode($users['value']));
            $isNextPageAvailable = false;
            if(isset($users['@odata.nextLink']) && $users['@odata.nextLink'] != "") {
                $isNextPageAvailable = true;
                while ($isNextPageAvailable) {
                    $r = $client->request('POST', 'https://login.microsoftonline.com/' . $this->tenantID . '/oauth2/v2.0/token', [
                        'form_params' => $postInput
                    ]);
                    $response = json_decode($r->getBody(), true);
                    $this->access_token = $response['access_token'];
                    $token = 'Authorization: Bearer ' . $this->access_token;

                    $url = $users['@odata.nextLink'];
                    // Log::info($url);
                    $this->info($url);
                    $users = $this->getMSUsers($url, $token);

                    $this->syncUsers($users['value']);

                    if(!isset($users['@odata.nextLink'])) {
                        $isNextPageAvailable = false;
                    }
                }
            }

            $this->info("success");
            Log::info("UserSyncFromAzure: success");
        } catch (\Exception $e) {
            Log::error("UserSyncFromAzure: " . $e->getMessage());
        }
    }

    public function syncUsers($users) {
        // $get_super = User::where('permission', 'like', '%superuser":1%')->whereNull('deleted_at')->where('activated', '=', 1)->limit(1)->get();
        $get_super = User::whereHas("roles", function($q){ $q->where("name", "SuperAdmin"); })->limit(1)->get();
        foreach ($users as $user) {
            if (config('app.client') == 'ltsct') {
                $allowedDomains = ['ltsct.com'];
            } elseif(config('app.client') == 'airasia') {
                $allowedDomains = ['airindiaexpress.com', 'partners.airindiaexpress.com'];
            } elseif(config('app.client') == 'dnatalogistics') {
                $allowedDomains = ['dnatalogistics.com', 'freightworks.com'];
            } elseif(config('app.client') == 'tscpl') {
                $allowedDomains = ['tatasteelcolors.com'];
            }
            if(!empty($allowedDomains)) {
                $get_email_address = $user['userPrincipalName'];
                $get_email_parts = explode('@', $get_email_address);

                if(is_array($get_email_parts) == false || count($get_email_parts) < 2) {
                    throw new \Exception("syncUsers: E-mail not valid");
                }
                $get_email_domain = strtolower(array_pop($get_email_parts));
                if (!in_array($get_email_domain, $allowedDomains)) {
                    // Log::info("invalid domain: " . $get_email_address);
                    continue;
                }
            }
            $usrObj = User::where("username", "like", $user["userPrincipalName"])->limit(1)->withTrashed()->first();
            $roleObj = Role::where('name', 'User')->first();
            if($user["userType"] != "Guest") {
                if (empty($usrObj)) {
                    $usrObj = new User();
                    if( in_array(config("app.client"), ["tscpl", "etherealmachines"]) ) {
                        $usrObj->email = $user["mail"];
                    } else {
                        $usrObj->email = $user["userPrincipalName"];
                    }
                    $usrObj->first_name = ($user["givenName"] != "") ? ucfirst(strtolower($user["givenName"])) : $user["displayName"];
                    $usrObj->last_name = ($user["surname"] != "") ? ucfirst(strtolower($user["surname"])) : ucfirst(strtolower($user["givenName"]));
                    $usrObj->username = strtolower($user["userPrincipalName"]);
                    $usrObj->company_id = Company::first()->id;
                    $usrObj->phone = strlen($user["mobilePhone"]) >= 20 ? "" : $user["mobilePhone"];
                    $usrObj->jobtitle = $user["jobTitle"];
                    $usrObj->create_mode = 4;
                    $usrObj->job_type = 0; // 0-Company Staff, 1-Contract Staff, 2-External Users
                    $usrObj->activated = ($user["accountEnabled"]) ? 1 : 0;
                    $usrObj->employee_num = ($user["employeeId"] != "") ? $user["employeeId"] : $usrObj->employee_num;
                    $usrObj->setPassword(str_random(8));
                    $usrObj->assignRole($roleObj);

                    if (!$usrObj->save()) {
                        Log::error("ms_user_not_created: " . $user["userPrincipalName"]);
                        continue;
                    }

                    UserLog::makeLog($usrObj->id, UserLog::ACT_CREATED, $get_super[0]->id);
                }
                if (isset($user["manager"])) {
                    $managerObj = User::where("username", "like", $user["manager"]["userPrincipalName"])->limit(1)->first();
                    if(!empty($managerObj)) {
                        $usrObj->manager_id = $managerObj->id;
                    }
                }

                if ($user["department"] != null || $user["department"] != "") {
                    $deptObj = Department::where('name', 'like', trim($user['department']))->first();
                    if (!empty($deptObj)) {
                        $usrObj->department_id = $deptObj->id;
                    } else {
                        if ($user['department'] != "") {
                            $newDeptObj = new Department;
                            $newDeptObj->name = trim($user['department']);
                            $newDeptObj->company_id = Company::first()->id;
                            $newDeptObj->attender_id = 0;
                            $newDeptObj->save();
                            $usrObj->department_id = $newDeptObj->id;
                        }
                    }
                }
                if ($user["city"] != null || $user["city"] != "") {
                    $locationObj = Location::where('name', 'like', trim($user['city']))->first();
                    if (!empty($locationObj)) {
                        $usrObj->location_id = $locationObj->id;
                    } else {
                        Log::info("Location not found: " . $user["city"]);
                    }
                } else {
                    if(config("app.client") == "ltsct") {
                        if ($user["officeLocation"] != null || $user["officeLocation"] != "") {
                            $officeLocationObj = Location::where('name', 'like', trim($user['officeLocation']))->first();
                            if (!empty($officeLocationObj)) {
                                $usrObj->location_id = $officeLocationObj->id;
                            } else {
                                Log::info("Office Location not found: " . $user["officeLocation"]);
                            }
                        }
                    } else {
                        $otherLocation = Location::where('name', 'like', 'Other')->first();
                        if (empty($otherLocation)) {
                            $otherLocation = new Location();
                            $otherLocation->name = $otherLocation->address = $otherLocation->city = $otherLocation->state = "Other";
                            $otherLocation->country = "IN";
                            $otherLocation->country_id = 101;
                            $otherLocation->state_id = 4008;
                            $otherLocation->city_id = 133024;
                            $otherLocation->currency = "INR";
                            $otherLocation->user_id = Auth::user()->id;
                            $otherLocation->save();
                        }
                        $usrObj->location_id = $otherLocation->id;
                    }
                }
                if(config("app.client") == "tscpl") {
                    if ($user["officeLocation"] != null || $user["officeLocation"] != "") {
                        $officeLocationObj = Location::where('name', 'like', trim($user['officeLocation']))->first();
                        if (!empty($officeLocationObj)) {
                            $usrObj->base_location_id = $officeLocationObj->id;
                        } else {
                            Log::info("Base Location not found: " . $user["officeLocation"]);
                        }
                    } else {
                        $otherLocation = Location::where('name', 'like', 'Other')->first();
                        if(empty($otherLocation)) {
                            $otherLocation = new Location();
                            $otherLocation->name = $otherLocation->address = $otherLocation->city = $otherLocation->state = "Other";
                            $otherLocation->country = "IN";
                            $otherLocation->country_id = 101;
                            $otherLocation->state_id = 4008;
                            $otherLocation->city_id = 133024;
                            $otherLocation->currency = "INR";
                            $otherLocation->user_id = Auth::user()->id;
                            $otherLocation->save();
                        }
                        $usrObj->base_location_id = $otherLocation->id;
                    }
                }
                if(count($usrObj->roles) < 1) {
                    $usrObj->assignRole($roleObj);
                }
                $usrObj->first_name = ($user["givenName"] != "") ? $user["givenName"] : $user["displayName"];
                $usrObj->last_name = ($user["surname"] != "") ? $user["surname"] : strtolower($user["givenName"]);
                $usrObj->displayName = $user["displayName"];
                $usrObj->username = strtolower($user["userPrincipalName"]);
                $usrObj->phone = strlen($user["mobilePhone"]) >= 20 ? "" : $user["mobilePhone"];
                if(config("app.client") == "tscpl") {
                    $usrObj->email = $user["mail"];
                }
                $usrObj->jobtitle = $user["jobTitle"];
                $usrObj->create_mode = 4;
                if(config("app.client") != "ltts") {
                    $usrObj->employee_num = ($user["employeeId"] != "") ? $user["employeeId"] : $usrObj->employee_num;
                    $usrObj->activated = ($user["accountEnabled"]) ? 1 : 0;
                    if($usrObj->deleted_at != null && $user["accountEnabled"]) {
                        $usrObj->deleted_at = null;
                    }
                }
                $usrObj->job_type = 0; // 0-Company Staff, 1-Contract Staff, 2-External Users
                $usrObj->address = $user["streetAddress"];
                $usrObj->save();
            }
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
//        $this->info(json_encode($responseObj['value']));
        return $responseObj;
    }
}

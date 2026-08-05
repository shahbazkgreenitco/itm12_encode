<?php

namespace App\Console\Commands;

use App\Models\AccessoryUser;
use App\Models\UserLog;
use Illuminate\Console\Command;
use App\Models\Settings;
use App\Models\User;
use Moathdev\Office365\Facade\Office365;
use \League\OAuth2\Client\Provider\GenericProvider as GenericProvider;
use App\Mail\DeletedAzureUserAssetListNotification;
use GuzzleHttp\Client as GuzzleClient;
use Auth;
use Log;
use DB;
use Mail;
use Validator;

use Carbon\Carbon;

class DeleteUserSyncFromAzure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deleted_ms_user_sync:azure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Program to get deleted users from azure portal';

    private $endpoints, $tenantID;
    private $access_token;
    /**
     * Execute the console command.
     */

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
        if (config("services.azure.client_id") == "") {
            return;
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

            $url = 'https://graph.microsoft.com/beta/directory/deletedItems/microsoft.graph.user?$select=id,displayName,userPrincipalName,deletedDateTime';
            $token = 'Authorization: Bearer '.$this->access_token;

            $users = $this->getMSUsers($url, $token);
            $this->syncUsers($users['value']);

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
            Log::info("GetDeletedUserFromAzure: success");
        } catch (\Exception $e) {
            Log::error("GetDeletedUserFromAzure: " . $e->getMessage());
        }
    }

    public function syncUsers($users) {
        $get_super = User::whereHas("roles", function($q){ $q->where("name", "SuperAdmin"); })->first();
        foreach ($users as $user) {
            if (config('app.client') == 'ltsct') {
                $allowedDomains = ['ltsct.com'];
            } elseif(config('app.client') == 'airasia') {
                $allowedDomains = ['airindiaexpress.com', 'partners.airindiaexpress.com'];
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
            $id = str_replace('-', '', $user["id"]);
            $user_principal_name = str_replace($id, '', $user["userPrincipalName"]);
            $objUser = User::where("email", "like", $user_principal_name)->first();
            $this->info($user_principal_name);
            if (isset($user["deletedDateTime"]) && ($user["deletedDateTime"] != null || $user["deletedDateTime"] != "")) {

                if(!$objUser || !$objUser->exists) {
                    // Log::info("DeleteUserSyncFromAzure: No User found for given data");
                    continue;
                }

                /*if(count($objUser->devices) > 0) {
                    // Log::info("DeleteUserSyncFromAzure: " . trans('content.user_fields.This_user_has') . count($objUser->devices) . trans('content.user_fields.device(s)'));
                    continue;
                }*/

                /*if(count($objUser->licenses)) {
                    // Log::info("DeleteUserSyncFromAzure: " . trans('content.user_fields.This_user_has') . count($objUser->licenses) . trans('content.user_fields.license(s)'));
                    continue;
                }*/

                if(count($objUser->srGroupMember)) {
                    // Log::info("DeleteUserSyncFromAzure: " . trans('content.user_fields.This_user_has') . count($objUser->srGroupMember) . trans('content.user_fields.srat_exist'));
                    continue;
                }

                $accessories = AccessoryUser::where('assigned_to', $objUser->id)->where('assigned_for', 1)->count();
                /*if($accessories > 0) {
                    // Log::info("DeleteUserSyncFromAzure: " . trans('content.user_fields.This_user_has') . $accessories . trans('content.user_fields.accessories'));
                    continue;
                }*/

                if(count($objUser->pblmattender)) {
                    // Log::info("DeleteUserSyncFromAzure: " . trans('content.user_fields.this_user_is_one'));
                    continue;
                }

                /*if(count($objUser->deptattender)) {
                    // Log::info("DeleteUserSyncFromAzure: " .trans('content.user_fields.this_user_is_one_attender'));
                    continue;
                }*/

                /*if(count($objUser->scheduleMaintenance)) {
                    // Log::info("DeleteUserSyncFromAzure: " .trans('content.user_fields.This_user_has' ) . count($objUser->scheduleMaintenance) . trans('content.user_fields.schedule_maintenance_allocation'));
                    continue;
                }*/

                if(count($objUser->devices) || $accessories > 0 || count($objUser->licenses) || count($objUser->scheduleMaintenance) || count($objUser->deptattender)) {
                    if (config('mail.service_enabled')) {
                        $settings = Settings::getSettings();
                        if ($settings->location_config == 1) {
                            $loc_previllage = Auth::user()->permitted_locations; //getting user's table
                            $permitted_loc = explode(",", $loc_previllage);
                            $access_users = User::whereIn('permitted_locations', $permitted_loc)->get();
                        } else {
                            $access_users = User::permission('DeviceRead')->get();
                        }
                        $this->info("Asset Assigned");
                        /*foreach ($access_users as $access) {
                            if(filter_var($access->email, FILTER_VALIDATE_EMAIL)) {
                                Mail::to($access->email)->send(new DeletedAzureUserAssetListNotification($access, $user, $accessories));
                            }
                        }*/
                    }
                } else {
                    $this->info("delete: ". $objUser->email);
                    /*$objUser->delete();
                    if($objUser->trashed()) {
                        UserLog::makeLog($objUser, UserLog::ACT_DELETED, $get_super->id);
                    }*/
                }
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
        return $responseObj;
    }
}

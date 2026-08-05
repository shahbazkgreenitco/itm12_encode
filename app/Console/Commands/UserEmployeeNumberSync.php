<?php

namespace App\Console\Commands;

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

class UserEmployeeNumberSync extends Command
{
    protected $signature = 'ms_user_sync_employee_number';
    protected $description = 'Program to sync MS users from azure portal';
    private $endpoints, $tenantID;
    private $access_token;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            Log::info("UserEmployeeNumberSync: start");
            $users = User::orderBy('id', 'desc')->get();
            foreach ($users as $user) {
                if($user->email != "" && (stripos(trim($user->email), '#EXT#')) == false ) {
                    $client = new GuzzleClient([
                        'auth' => ['LTTSGreenIT', 'h3MW4Qz6jI!dwH'],
                    ]);

                    $r = $client->request('GET', 'https://lttswebapi.ltts.com/lttswebapi/api/GreenITEmp/'.$user->email.'/');
                    $responseObj = json_decode($r->getBody(), true);
                    if($responseObj != "No data available") {
                        foreach ($responseObj as $response) {
                            if(count($responseObj) > 1 && isset($response['Status']) && ($response['Status'] == "Inactive" || $response['Status'] == "Reported No Show") ) {
                                continue;
                            }

                            if ($response['PSNo'] != "" || $response['PSNo'] != null) {
                                $this->info($user->email.'-'.$response['PSNo']);
                                if ($response['BaseLocation'] != "" || $response['BaseLocation'] != null) {
                                    $locationObj = Location::where('name', 'like', $response['BaseLocation'])->first();
                                    if (!empty($locationObj)) {
                                        $user->base_location_id = $locationObj->id;
                                    }
                                }
                                $user->employee_num = $response['PSNo'];
                                $user->business_unit = $response['BusinessUnit'];
                                $user->delivery_unit = $response['DeliveryUnit'];
                                if ($response['Status'] == "Inactive") {
                                    $user->activated = 0;
                                    $user->last_working_date = $response['TerminatedDate'];
                                } else {
                                    if($user->activated == 0) {
                                        $user->activated = 1;
                                        $user->last_working_date = null;
                                    }
                                }
                                $user->save();
                            }
                        }
                    }
                }
            }

            $this->info("success");
            Log::info("UserEmployeeNumberSync: success");
        } catch (\Exception $e) {
            Log::error("UserEmployeeNumberSync: " . $e->getMessage());
        }
    }
}

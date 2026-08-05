<?php

namespace App\Console\Commands;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Log;
use Mail;
use Google_Client;
use Google_Service_Directory;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class LoadGoogleWorkspaceUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loadGoogleWorkspaceUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load users from google workspace domain';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle() {
        $this->info('Starting Google Workspace User Sync...');
        $clientName = config('app.client');
        try {
            //Initialize Google Client
            $client = new Google_Client();
            $credentialsPath = public_path("itm-google-user-sync-".$clientName.".json");

            $client->setAuthConfig($credentialsPath);
            $client->addScope(Google_Service_Directory::ADMIN_DIRECTORY_USER_READONLY);

            // Impersonate Workspace Admin
            $client->setSubject(config('services.google_workspace.mail'));

            // Initialize Directory Service
            $service = new Google_Service_Directory($client);
            $this->info('...');
            $optParams = [
                'customer'   => 'my_customer',
                'maxResults' => 100,
                'orderBy'    => 'email',
            ];

            $pageToken   = null;
            $syncedCount = 0;

            do {
                if ($pageToken) {
                    $optParams['pageToken'] = $pageToken;
                }

                $results = $service->users->listUsers($optParams);
                $this->info('...');
                $users   = $results->getUsers();

                if (empty($users)) {
                    $this->info('No users found.');
                    break;
                }

                foreach ($users as $googleUser) {
                    $primaryEmail = $googleUser->getPrimaryEmail();
                    $name         = $googleUser->getName();
                    $phones       = $googleUser->getPhones();
                    $addresses    = $googleUser->getAddresses();
                    $orgUnitPath = $googleUser->getOrgUnitPath();
                    $this->info($primaryEmail . " - " . json_encode($addresses));
                    $data = [
                        'email'      => $primaryEmail,
                        'username'   => $primaryEmail,
                        'first_name' => $name ? $name->getGivenName() : null,
                        'last_name'  => $name ? $name->getFamilyName() : null,
                        'phone'      => $phones[0]['value'] ?? null,
                        'activated'  => $googleUser->getSuspended() ? 0 : 1,
                    ];

                    // Handle Address → Location Mapping
                    if (!empty($addresses)) {
                        foreach ($addresses as $addr) {
                            // if (!empty($addr['streetAddress'])) {
                            //     $locationObj = Location::where('name', 'like', '%' . $addr['streetAddress'] . '%')->first();
                            //     if (!empty($locationObj)) {
                            //         $data['location_id'] = $locationObj->id;
                            //         break;
                            //     } else {
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
                                    $data['location_id'] = $otherLocation->id;
                            //     }
                            // }
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
                        $data['location_id'] = $otherLocation->id;
                    }
                    $data['company_id'] = Company::first()->id;
                    if(in_array(config('app.client'), ["tradekings"])) {
                        $orgUnitCompanyMap = [
                            '/' => [
                                'company_id' => 6,
                                'location_id' => 5,
                            ],
                            '/Konige' => [
                                'company_id' => 2,
                                'location_id' => 1,
                            ],
                            '/Tradekings - Homecare' => [
                                'company_id' => 3,
                                'location_id' => 21,
                            ],
                            '/KGL' => [
                                'company_id' => 4,
                                'location_id' => 49,
                            ],
                            '/TK-Users' => [
                                'company_id' => 6,
                                'location_id' => 5,
                            ],
                            '/DairyGold' => [
                                'company_id' => 7,
                                'location_id' => 38,
                            ],
                            '/Swissbake' => [
                                'company_id' => 8,
                                'location_id' => 45,
                            ],
                            '/UMCIL' => [
                                'company_id' => 9,
                                'location_id' => 50,
                            ],
                            '/Tradekings-SA' => [
                                'company_id' => 10,
                                'location_id' => 53
                            ]
                        ];
                        if (isset($orgUnitCompanyMap[$orgUnitPath])) {
                            $data['company_id'] = $orgUnitCompanyMap[$orgUnitPath]['company_id'];
                            $data['location_id'] = $orgUnitCompanyMap[$orgUnitPath]['location_id'];
                        } else {
                            $data['company_id'] = 6;
                            $data['location_id'] = 5;
                            Log::info("Company/Location not found" . $primaryEmail . " - " . $orgUnitPath);
                        }
                    }

                    // Check if user exists
                    $usrObj = User::where('email', $primaryEmail)->withTrashed()->first();
                    $get_super = User::whereHas("roles", function($q){ $q->where("name", "SuperAdmin"); })->limit(1)->get();
                    if (!empty($usrObj)) {
                        // Update existing
                        $usrObj->update($data);
                    } else {
                        // Create new user
                        $password = Str::random(8);
                        $data['password'] = Hash::make($password);
                        // $data['jobtitle'] = "jobtitle";
                        $data['create_mode'] = 4;
                        $data['job_type'] = 4;
                        // $data['employee_num'] = 4;
                        // $data['address'] = "address";
                        $roleObj = Role::where('name', 'User')->first();
                        $user = User::create($data);
                        $user->assignRole($roleObj);
                        if (!$user->save()) {
                            Log::error("ms_user_not_created: " . $user["userPrincipalName"]);
                            continue;
                        }

                        UserLog::makeLog($user->id, UserLog::ACT_CREATED, $get_super[0]->id);
                        /* // If you want to send email later, you can enable here
                        try {
                            if(config('mail.service_enabled') && $user->activated) {
                                Mail::to($user->email)
                                    ->queue(new UserCredentialNotification($user, $password));
                            }
                        } catch (\Exception $e) {
                            Log::error($e->getMessage());
                        }*/
                    }

                    $syncedCount++;
                }

                $pageToken = $results->getNextPageToken();

            } while ($pageToken);

            $this->info("Successfully synced {$syncedCount} users!");
        } catch (\Exception $e) {
            Log::error('Google Workspace Sync Error: ' . $e);
            $this->error('Error syncing users. Check logs.');
        }
    }

    // public function handle() {
    //     $this->info('Starting Google Workspace User Sync...');

    //     try {
    //         // 1. Initialize the Google Client
    //         $client = new Google_Client();

    //         // Set the path to your JSON credentials
    //         $credentialsPath = public_path("js/itm-helpdesk-d9c3eca8f56b.json");
    //         $client->setAuthConfig($credentialsPath);

    //         // Add the necessary scopes
    //         $client->addScope(Google_Service_Directory::ADMIN_DIRECTORY_USER_READONLY);

    //         // IMPORTANT: Impersonate a Workspace Admin
    //         // Service accounts must "act as" an admin to read the directory
    //         $client->setSubject(config('services.google.google_workspace_mail'));

    //         // 2. Initialize the Directory Service
    //         $service = new Google_Service_Directory($client);

    //         $optParams = [
    //             'customer' => 'my_customer', // Targets your specific Workspace account
    //             'maxResults' => 100, // Process in batches
    //         ];

    //         $pageToken = null;
    //         $syncedCount = 0;

    //         // 3. Fetch and Loop through users (Handling Pagination)
    //         do {
    //             if ($pageToken) {
    //                 $optParams['pageToken'] = $pageToken;
    //             }

    //             $results = $service->users->listUsers($optParams);
    //             $users = $results->getUsers();
    //             if (count($users) == 0) {
    //                 $this->info('No users found.');
    //                 break;
    //             }
    //             foreach ($users as $user) {
    //                 // 4. Sync to Laravel Database using updateOrCreate
    //                 $data = [];
    //                 $data['email'] = $user['primaryEmail'];
    //                 $data['first_name'] = $user['name']['givenName'];
    //                 $data['last_name'] = $user['name']['familyName'];
    //                 $data['phone'] = $user['phnoes'][0]['value'] ?? null;
    //                 $data['activated'] = $user['suspended'] == false ? true : false;
    //                 if(!empty($data['address']) && count($data['address']) > 0){
    //                     foreach($data['address'] as $addr){
    //                         $loc = Location::where('name', 'like', $addr['streetAddress'])->first();
    //                         if(!empty($loc)){
    //                             $data['location_id'] = $loc->id;
    //                         }else{
    //                             $data['location_id'] = null;
    //                         }
    //                     }
    //                 }
    //                 $checkUserExistance = User::where('email', $user['primaryEmail'])->first();
    //                 $isUpdatingUser = false;
    //                 if(!empty($checkUserExistance)){
    //                     $isUpdatingUser = true;
    //                     $userObj = User::find($checkUserExistance->id);
    //                 }else{
    //                     $password = str_random(8);
    //                     Log::error($password);
    //                     $data['password'] = Hash::make($password);
    //                     $userObj = new User();
    //                 }
    //                 $userObj->fill($data);
    //                 if(!$userObj->save()){
    //                     Log::error("LoadGoogleWorkspaceUsers : Unable to save user. " . $user['primaryEmail']);
    //                 }
    //                 if(!$isUpdatingUser){
    //                     $userObj->setPassword($data['password']);
    //                     // try {
    //                     //     if(config('mail.service_enabled') && $userObj->activated == 1 && filter_var($userObj->email, FILTER_VALIDATE_EMAIL)) {
    //                     //         $alertnotify = Settings::first()->alerts_enabled == 1 ? CommonHelper::getGlobalAlertEmail(); : [];
    //                     //         if($alertnotify) {
    //                     //             Mail::to($userObj->email)->queue(new UserCredentialNotification($userObj, $password));
    //                     //         }
    //                     //         else {
    //                     //             Mail::to($userObj->email)->cc($alertnotify)->queue(new UserCredentialNotification($userObj, $password));
    //                     //         }
    //                     //     }
    //                     // }
    //                     // catch(\Exception $e) {
    //                     //     Log::error($e->getMessage());
    //                     // }
    //                 }
    //                 $syncedCount++;
    //             }
    //             $pageToken = $results->getNextPageToken();
    //         } while ($pageToken);
    //         $this->info("Successfully synced {$syncedCount} users!");
    //     } catch (Exception $e) {
    //         $this->error('Error syncing users: ' . $e->getMessage());
    //     }
    // }

}

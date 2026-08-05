<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blacklisted;
use App\Models\Settings;
use App\Models\User;
use App\Models\BlacklistedSwEmail;
use App\Models\NetworkInventory\Product;
use App\Models\BlacklistedSoftwareStatus;
use App\Exports\BlackListed as BlackListedExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\Asset\BlacklistedSoftwareMail;
use App\Helpers\Common as CommonHelper;
use Carbon\Carbon;
use Mail;
use DB;
use Log;

class BlacklistReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:blacklistedReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sending mail of blacklisted software';

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
    public function handle()
    {
        // $emails = User::select('email') -> first();
        // $emails = ['nareshv@greenitco.com'];
        if (!config('mail.service_enabled')) {
            return;
        }

        $now = new Carbon(config('app.timezone'));
        $settings = Settings::getSettings();
        $tot = 0;
        $last_day = true;
        //fetch last 24 hours data from change log table and update license status in blacklisted_software_statuses
        $blacklisted_softwares = Blacklisted::select('id', 'product', 'Version', 'publisher', 'created_at', 'updated_by', 'blacklist_type', 'manual_software', 'manual_sw_type')->get();

        if (!empty($blacklisted_softwares) && $blacklisted_softwares) {
            foreach ($blacklisted_softwares as $blacklisted) {
                $db = DB::table('itm_network_change_logs as cl');
                $db->where('cl.cl_item', 3);

                if ($blacklisted->blacklist_type == 1) { // 1 = S/W Name & Version
                    if (empty($blacklisted->Version)) {
                        $db->where('cl.Caption', $blacklisted->product);
                    } else {
                        $version = explode(',', $blacklisted->Version);
                        $db->where('cl.Caption', $blacklisted->product)->whereIn('cl.Version', $version);
                    }
                } elseif ($blacklisted->blacklist_type == 2) { // 2 = S/W Publisher
                    $publishers = explode(',', $blacklisted->publisher);
                    $db->whereIn('cl.Publisher', $publishers);
                } elseif ($blacklisted->blacklist_type == 3) { // 3 = S/W Name
                    $captions = explode(',', $blacklisted->product);
                    $db->whereIn('cl.Caption', $captions);
                } elseif ($blacklisted->blacklist_type == 4) { // 4 = S/W Manually Added
                    if ($blacklisted->manual_sw_type == 1) {
                        $db->where('cl.Caption', $blacklisted->manual_software);
                    } else if ($blacklisted->manual_sw_type == 2) {
                        $db->where('cl.Caption', 'like', '%'. $blacklisted->manual_software . '%');
                    }
                }
                $db->where('cl.created_at', '>=', Carbon::now()->subDay()->toDateTimeString());
                $results = $db->get();
                if(!$results->isEmpty()) {
                    foreach($results as $result) {
                        $check_status = BlacklistedSoftwareStatus::where('product_blacklisted_id', $blacklisted->id)->where('basic_id', $result->basic_id)->where('caption', $result->Caption)->first();
                        if (!empty($check_status)) {
                            $check_status->update([
                                'change_log_id' => $result->id,
                                'license_status' => $result->cl_type
                            ]);
                        } else {
                            $status = new BlacklistedSoftwareStatus;
                            $status->product_blacklisted_id = $blacklisted->id;
                            $status->basic_id = $result->basic_id;
                            $status->change_log_id = $result->id;
                            $status->license_status = $result->cl_type;
                            $status->caption = $result->Caption;
                            $status->save();
                        }
                    }
                }
            }

            $db = DB::table('blacklisted_software_statuses as bs')->select('cl.Caption as Caption','cl.Version','b.BIOSSerialNumber','s.name','s.asset_tag', 'lbl.name as label_name');
            $db->addSelect(DB::raw('case when bs.license_status = 1 then "Newly added" when bs.license_status = 2 then "Changes Detected" when bs.license_status = 3 then "Missing" end as license_status'));
            $db->leftJoin('product_blacklisted as itm', 'itm.id', 'bs.product_blacklisted_id');
            $db->leftJoin('itm_network_inventory_basic as b', 'bs.basic_id', '=', 'b.id');
            $db->leftJoin('itm_network_change_logs as cl', 'bs.change_log_id', '=', 'cl.id');
            $db->leftJoin('assets as s', 'b.BIOSSerialNumber', '=', 's.serial');
            $db->leftJoin('status_labels as lbl', 'lbl.id', '=', 's.status_id');
            $results = $db->get();
            if (!$results->isEmpty()) {
                $tot += count($results);
                if ($results->count() > 0) {
                    foreach($results as $row) {
                        $d[] = [
                            "Software Name" => $row->Caption,
                            "Version" => $row->Version,
                            "Device Name" => $row->name,
                            "Device Tag" => $row->asset_tag,
                            "Serial" => $row->BIOSSerialNumber,
                            "Device Status" => $row->label_name,
                            "License Status" => $row->license_status
                        ];
                    }
                }
                if ($tot > 0) {
                    $file_name = 'BlackListedReport_' . $now->format('dmY').'.xlsx';
                    $keys = array("Software Name", "Version", "Device Name", "Device Tag", "Serial", "Device Status", "License Status");
                    $doc_path = Excel::store(new BlackListedExport($d, $keys), $file_name, 'black_listed_report');

                    // send email to all users who has device read permission
                    // $allEmails = User::permission('DeviceRead')->where('activated', 1)->pluck('email','username')->toArray();      
                    $allEmails = [];
                    if ($settings->alerts_enabled) {
                        $globalEmails = CommonHelper::getGlobalAlertEmail();
                        $allEmails = array(null => $globalEmails);
                    }

                    if (!empty($allEmails)) {
                        try {
                            foreach ($allEmails as $username => $email) {
                                if ($email != null && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    $userObj = User::where('email', $email)->first();
                                    $this->info($email);
                                    Mail::to($email)->send(new BlacklistedSoftwareMail($tot, $file_name, $userObj, $last_day));
                                    // Save data to blacklisted_sw_email table
                                    // foreach($products as $row) {
                                    //     $blacklisted = new BlacklistedSwEmail();
                                    //     $blacklisted->caption = $row->Caption;
                                    //     $blacklisted->email = $email;
                                    //     $blacklisted->save();
                                    // }
                                } else{
                                    Log::info("blacklistedReport: Not a valid email" .$email);
                                }    
                            }
                        } catch (\Exception $ex) {
                            Log::error("blacklistedReport:" . $ex->getMessage());
                        }
                    }
                } else {
                    Log::info("blacklistedReport: No data found");
                }
            }
        } else {
            Log::info("blacklistedReport: No data found");
        }
    }
}

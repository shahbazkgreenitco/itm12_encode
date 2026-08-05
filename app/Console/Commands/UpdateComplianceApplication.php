<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\Device\ComplianceApplication;
use DB;
use Log;

class UpdateComplianceApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update_compliance_application';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch last 24 hours applications from change_log table and update compliance application table';

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
        try {
            //store data in compliance_applications table from itm_network_change_logs table for last 24 hrs
            $applications = [];
            $setting = Setting::select('compliance_application', 'greenit_agent_enabled')->first();

            if (isset($setting->compliance_application) && !empty($setting->compliance_application)) {
                $apps = explode(',', $setting->compliance_application);
                $applications = array_merge($applications, $apps);
            }
            if (isset($setting->greenit_agent_enabled) && !empty($setting->greenit_agent_enabled) && $setting->greenit_agent_enabled == 1) {
                array_push($applications, 'ItmAssetMgmtAgent');
            }
            if (!empty($applications)) {
                foreach ($applications as $app) {
                    // if application already exist then dont fetch data for that application
                    $count = ComplianceApplication::where('caption', $app)->count();

                    $rawQuery = "SELECT cl.id, cl.basic_id, cl.Caption, cl.cl_type FROM itm_network_change_logs as cl WHERE cl.id IN 
                                (
                                    SELECT MAX(itm_network_change_logs.id) FROM itm_network_change_logs left Join itm_network_inventory_basic as a on a.id = itm_network_change_logs.basic_id
                                    left Join assets as s on a.device_id = s.id WHERE itm_network_change_logs.cl_item = 3 and itm_network_change_logs.Caption = '$app' and s.deleted_at is NULL and a.id is NOT NULL and a.is_dupe is NULL and itm_network_change_logs.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY itm_network_change_logs.basic_id
                                )";
                    $results = DB::select($rawQuery);
                    foreach($results as $result) {
                        $compliance = new ComplianceApplication;
                        $compliance->change_log_id = $result->id;
                        $compliance->basic_id = $result->basic_id;
                        $compliance->caption = $result->Caption;
                        $compliance->status = $result->cl_type;
                        $compliance->save();
                    }
                }
            }
            Log::info("Compliance Application Status Fetch last 24 Hours Applications From change_log Table Cron");
        } catch (\Exception $e) {
            Log::error("command:update_compliance_application:-". $e->getMessage());
        }
    }
}

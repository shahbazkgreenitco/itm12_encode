<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Settings;
use Log;
use App\Models\Company;
use App\Models\User;
use Auth;
use App\Models\NetworkInventory\AgentData;
use App\Models\NetworkInventory\Basic;
use App\Models\NetworkInventory\ScanRegister;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Common as CommonHelper;
use stdClass;

class AgentDataCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ni:agent_data_crawler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Program to crawl the data which are submitted by network agents';

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
     * @return mixed
     */
    public function handle()
    {
        $settings = Settings::getSettings();

        /* login as Super User */
        // $get_super = User::where('permission', 'like', '%superuser":1%')->whereNull('deleted_at')->where('activated', '=', 1)->limit(1)->get();
        $get_super = User::whereHas("roles", function($q){ $q->where("name", "SuperAdmin"); })->limit(1)->get();
        Auth::loginUsingId($get_super[0]->id);

        $data_sets = AgentData::whereNull('is_processed')->where('protocol', '=', 'telnet')->orderBy('id')->limit(10)->get();

        if(! $data_sets || ! count($data_sets)) {
            return false;
        }
        
        foreach($data_sets as $data) {
            $data->is_processed = 1;
            $data->save();
            try {
                if($data->isProtocolTelnet()) {
                    $this->processTelnetData($data);
                }
                if($data->isProtocolSnmp()) {
                    $this->processSnmpData($data);
                }
            }
            catch(\Exception $e) {
                print_r($e->getMessage());
            } 
        }
    }

    public function processSnmpData($record) {

    }

    public function processTelnetData($record) {
        $content = $this->getDataFromLog($record->file_path);
        $now = new Carbon(config('app.timezone'));

        if(! $content) {
            return False;
        }

        $data = [];
        $data['ComputerManufacturer'] = isset($content['manufacture']) ? $content['manufacture'] : null;
        $data['ComputerModel'] = isset($content['modelname']) ? $content['modelname'] : null;
        $data['company'] = isset($content['company']) ? $content['company'] : null;
        $data['IPv4'] = isset($content['IPv4']) ? $content['IPv4'] : null;
        $data['productclass'] = isset($content['productclass']) ? $content['productclass'] : null;
        $data['description'] = isset($content['description']) ? $content['description'] : null;
        $data['version'] = isset($content['version']) ? $content['version'] : null;
        $data['oui'] = isset($content['oui']) ? $content['oui'] : null;
        $data['ComputerName'] = trim($data['ComputerManufacturer'] . ' ' . $data['ComputerModel']);
        $data['HddSize'] = 0;
        $data['RamSize'] = 0;

        $exists = Basic::where("BIOSSerialNumber", "like", $content['serialnumber'])->select("id")->get();
        $basic_id = null;

        if(count($exists)) {
            $basic_id = $exists[0]->id;
            $basic = Basic::find($basic_id);
            Basic::where("BIOSSerialNumber", "like", $content['serialnumber'])->update($data);
            $basic->touch();
        }
        else {
            $basic = new Basic;
            $basic->fill($data);
            $basic->BIOSSerialNumber = $content['serialnumber'];
            $basic->save();
            $basic_id = $basic->id;
        }

        if(isset($data['IPv4']) && $data['IPv4']) {
            $scanRegisterData = [];
            $scanRegisterData['agent_look_at'] = $now->format('Y-m-d H:i:s');
            $scanRegisterData['company_id'] = isset($data["company"]) && $data["company"] ? $data["company"] : (Company::first())->id;
            $scanRegisterData['hostname'] = $data['ComputerName'];
            ScanRegister::where('ipv4', 'like', $data['IPv4'])->update($scanRegisterData);
        }

        $record->is_processed = 2;
        $record->processed_at = $now->format('Y-m-d H:i:s');
        return $record->save();
    }

    public function getDataFromLog($file_path) {
        $return = [];

        if(! $file_path) {
            return $return;
        }

        $raw_content = Storage::get($file_path);

        if(! $raw_content) {
            return $return;
        }

        try {
            $return = json_decode($raw_content, true);
            return $return;
        }
        catch(\Exception $e) {
            return $return;
        }
    }
}

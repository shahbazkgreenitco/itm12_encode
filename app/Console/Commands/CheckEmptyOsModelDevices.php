<?php

namespace App\Console\Commands;

use App\Models\NetworkInventory\Basic;
use App\Models\Settings;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Log;
use DB;
use Carbon\Carbon;
use App\Imports\DeviceImportStore;
use App\Mail\Asset\EmptyOsModelDeviceSendEmailNotification;

class CheckEmptyOsModelDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devices:check-empty-os-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find devices with empty or null OS or Model from network inventory';

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
        $now = new Carbon(config('app.timezone'));
        $devices = Basic::from('itm_network_inventory_basic as b')
            ->select('a.asset_tag','b.BIOSSerialNumber as SerialNumber','b.ComputerModel', 'b.OSCaption','c.name as company_name')
            ->leftJoin('companies as c', 'b.company', 'c.id')
            ->leftJoin('assets as a', 'b.device_id', 'a.id')
            ->where(function($query) {
                $query->whereNull('OSCaption')
                      ->orWhere('OSCaption', '')
                      ->orWhereNull('ComputerModel')
                      ->orWhere('ComputerModel', '');
            })
            ->whereNull('b.is_dupe')
            ->whereNull('b.deleted_at');
        $allEmails = ['bharat.gupta@greenitco.com','nareshv@greenitco.com','santoshu@greenitco.com','palakv@greenitco.com'];
        if($devices->count() > 0) {
            $notDetectedDevices = $devices->get();
            //create excel document to send users
            $tot = count($notDetectedDevices);
            $file_name = "";
            $data = [];
            $company = null;
            foreach($notDetectedDevices as $row) {
                $data[] = [
                    "Company" => $row->company_name,
                    "Device Tag" => $row->asset_tag,
                    "SerialNumber" => $row->SerialNumber,
                    "ComputerModel" => $row->ComputerModel,
                    "OS" => $row->OSCaption,
                ];
                $company = $row->company_name;
            }
            $file_name = 'EmptyOsModelDevices'.$company.'_' . $now->format('dmY').'.xlsx';
            $keys = array("Company","Device Tag","SerialNumber","ComputerModel","OS");

            $doc_path = Excel::store(new DeviceImportStore($data, $keys), $file_name, 'empty_os_model_devices');
            $to = array_shift($allEmails);
            $cc = $allEmails;
            Mail::to($to)->cc($cc)->queue(new EmptyOsModelDeviceSendEmailNotification($tot, $file_name, $company));
        } 
        // else {
        //     Log::info("CheckEmptyOsModelDevices: No data found");
        // } 
    }
}
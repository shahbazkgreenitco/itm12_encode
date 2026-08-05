<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Mail;
use Carbon\Carbon;
use App\Mail\DeviceExpectedReturnMail;
use App\Models\Settings;
use App\Models\Device;
use App\Models\User;
use App\Models\AssetAllocationType;
use App\Helpers\Common as CommonHelper;
use Log;
use PhpOffice\PhpSpreadsheet\Shared\Date as OfficeDate;


class DeviceExpectedReturnNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deviceExpectedReturnNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send mail whose device expected checkin is expire ';

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

        $settings = Settings::getSettings();

        $globalAlertEmails = CommonHelper::getGlobalAlertEmail();
        if (!config('mail.service_enabled') ||!$settings->alerts_enabled ||empty($globalAlertEmails) ||!array_filter($globalAlertEmails, function($email) {return filter_var($email, FILTER_VALIDATE_EMAIL);})){
           return;
        }

        $now = new Carbon(config('app.timezone'));
        $date = Carbon::now()->subDays(30);

        $devices = Device::select('name','asset_tag','expected_checkin','assigned_to','users.email as email','serial')
        ->leftjoin('users', 'users.id', 'assets.assigned_to')
        ->whereDate('expected_checkin','>=',$date)->get()->toArray();
      
       
        $tot = 0;
        $file_name = "";
        if(is_array($devices) && $tot = count($devices)) {
            $data = [];
            foreach($devices as $row) {
                $expected_checkin = new Carbon($row['expected_checkin'], config('app.timezone'));
                $data[] = [
                    "Device Tag" => $row['asset_tag'],
                    "Device Name" => $row['name'],
                    "Device Serial" => $row['serial'],
                    "Expected Checkin (dd/mm/yyyy)" => OfficeDate::dateTimeToExcel($expected_checkin)
                ];
            }

            $file_name = 'DeviceOldExpectedCheckin_' . $now->format('dmY');


            \Excel::create($file_name, function($excel) use(&$data) {
                $excel->sheet('DeviceExpectedReturnNotification', function($sheet) use(&$data) {
                    $sheet->setColumnFormat(['F'=>'dd/mm/YYYY','H'=>'dd/mm/YYYY','I'=>'dd/mm/YYYY']);
                    $sheet->fromArray($data);
                });
            })->store('xlsx', storage_path('device'));
        }

        Mail::to($globalAlertEmails)->send(new DeviceExpectedReturnMail($date, $tot, $file_name));
    }
}

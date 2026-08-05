<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Mail;
use Carbon\Carbon;
use App\Mail\LicenseWeeklyCheckinNotification as LicenseWeeklyCheckinMail;
use App\Helpers\Common as CommonHelper;
use App\Models\Settings;

use PhpOffice\PhpSpreadsheet\Shared\Date as OfficeDate;

class LicenseWeeklyCheckinReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:licenseWeeklyCheckinReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send the license expire list to client on periodically';

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
        $globalAlertEmails = CommonHelper::getGlobalAlertEmail();
        if (!config('mail.service_enabled') ||!$settings->alerts_enabled ||empty($globalAlertEmails) ||!array_filter($globalAlertEmails, function($email) {return filter_var($email, FILTER_VALIDATE_EMAIL);})){
            return;
        }

        $now = new Carbon(config('app.timezone'));
        $date1 = Carbon::now()->addDays(7);
        $date = Carbon::now()->addDays(7);
        $start = $date1->startOfWeek();
        $start_1 = $date->startOfWeek()->format('Y-m-d');
        $mondayOneWeekLater = $date->addWeeks(1);
        $end = $mondayOneWeekLater;

        $query = "SELECT ls.notes, CASE WHEN l.id IS NOT NULL THEN CONCAT_WS('','LIC',l.id) ELSE '' END AS batch_no, device.asset_tag AS asset_tag,l.name, CASE WHEN ls.serial IS NOT NULL THEN ls.serial ELSE l.serial END AS serial_no, CONCAT(enduser.first_name, '', enduser.last_name) AS endUserName,ls.expected_checkin FROM license_seats AS ls LEFT JOIN licenses AS l ON ls.license_id = l.id LEFT JOIN users AS enduser ON enduser.id = ls.assigned_to LEFT JOIN assets AS device ON device.id = ls.asset_id WHERE ls.expected_checkin IS NOT NULL AND ls.expected_checkin >= '". $start->format('Y-m-d') ."' AND ls.expected_checkin <= '". $end->format('Y-m-d') ."' ORDER BY ls.expected_checkin ASC";

        $result = DB::select($query);

        $tot = 0;
        $file_name = "";

        if(is_array($result) && $tot = count($result)) {
            $data = [];
            foreach($result as $row) {
                $expected_checkin = new Carbon($row->expected_checkin, config('app.timezone'));

                $data[] = [
                    "Batch No" => $row->batch_no,
                    "License Name" => $row->name,
                    "Serial Number" => $row->serial_no,
                    "Asset Tag" => $row->asset_tag,
                    "User" => $row->endUserName,
                    "Expected Checkin" => OfficeDate::dateTimeToExcel($expected_checkin),
                ];
            }

            $file_name = 'LicenseWeeklyCheckinReport_' . $now->format('dmY');

            \Excel::create($file_name, function($excel) use(&$data) {
                $excel->sheet('Licenses', function($sheet) use(&$data) {
                    $sheet->setColumnFormat(['F'=>'dd/mm/YYYY']);
                    $sheet->fromArray($data);
                });
            })->store('xlsx', storage_path('license'));
        }

        Mail::to($globalAlertEmails)->send(new LicenseWeeklyCheckinMail($start, $end, $tot, $file_name));
    }
}
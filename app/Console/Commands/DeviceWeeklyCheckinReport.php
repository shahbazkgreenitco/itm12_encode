<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Mail;
use Carbon\Carbon;
use App\Mail\DeviceWeeklyCheckinNotification as DeviceWeeklyCheckinMail;
use App\Models\Settings;
use App\Helpers\Common as CommonHelper;

use PhpOffice\PhpSpreadsheet\Shared\Date as OfficeDate;

class DeviceWeeklyCheckinReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:deviceWeeklyCheckinReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send the checkin device report to client on periodically';

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
        if (!config('mail.service_enabled') ||!$settings->alerts_enabled ||empty($globalAlertEmails) ||!array_filter($globalAlertEmails, function($email) {return filter_var($email, FILTER_VALIDATE_EMAIL);})) {
           return;
        }

        $now = new Carbon(config('app.timezone'));
        $date1 = Carbon::now()->addDays(7);
        $date = Carbon::now()->addDays(7);
        $start = $date1->startOfWeek();
        $start_1 = $date->startOfWeek()->format('Y-m-d');
        $mondayOneWeekLater = $date->addWeeks(1);
        $end = $mondayOneWeekLater;

        $query = "SELECT  a.asset_tag,  mnf.name AS manufacturer,  mdl.name AS model,  mdl.modelno,  stslbl.name AS status,  a.serial,  a.purchase_date,  a.expected_checkin,  a.last_checkout FROM  assets AS a LEFT JOIN models AS mdl ON  a.model_id = mdl.id AND a.deleted_at IS NULL AND mdl.deleted_at IS NULL LEFT JOIN manufacturers AS mnf ON  mnf.id = mdl.manufacturer_id AND mnf.deleted_at IS NULL LEFT JOIN status_labels AS stslbl ON  stslbl.id = a.status_id AND stslbl.deleted_at IS NULL LEFT JOIN projects AS pr ON  a.last_checkout_project = pr.id LEFT JOIN users AS u ON  u.id = a.assigned_to LEFT JOIN places AS p ON  p.id = a.assigned_to WHERE  a.deleted_at IS NULL AND a.expected_checkin >= '". $start->format('Y-m-d') ."' AND  a.expected_checkin <= '". $end->format('Y-m-d') ."' AND a.status_id = 8 ORDER BY  a.expected_checkin ASC";

        $result = DB::select($query);

        $tot = 0;
        $file_name = "";
        if(is_array($result) && $tot = count($result)) {
            $data = [];
            foreach($result as $row) {
                $last_checkout = new Carbon($row->last_checkout, config('app.timezone'));
                $purchase_date = new Carbon($row->purchase_date, config('app.timezone'));
                $expected_checkin = new Carbon($row->expected_checkin, config('app.timezone'));
                $data[] = [
                    "Device Tag" => $row->asset_tag,
                    "Manufacturer" => $row->manufacturer,
                    "Model" => $row->model,
                    "Model No" => $row->modelno,
                    "Device Serial" => $row->serial,
                    "Purcahse Date (dd/mm/yyyy)" => OfficeDate::dateTimeToExcel($purchase_date),
                    "Device Status" => $row->status,
                    "Last Checkout (dd/mm/yyyy)" => OfficeDate::dateTimeToExcel($last_checkout),
                    "Expected Checkin (dd/mm/yyyy)" => OfficeDate::dateTimeToExcel($expected_checkin)
                ];
            }

            $file_name = 'DeviceWeeklyCheckinReport_' . $now->format('dmY');

            \Excel::create($file_name, function($excel) use(&$data) {
                $excel->sheet('Devices', function($sheet) use(&$data) {
                    $sheet->setColumnFormat(['F'=>'dd/mm/YYYY','H'=>'dd/mm/YYYY','I'=>'dd/mm/YYYY']);
                    $sheet->fromArray($data);
                });
            })->store('xlsx', storage_path('device'));
        }

        Mail::to($globalAlertEmails)->send(new DeviceWeeklyCheckinMail($start, $end, $tot, $file_name));
    }
}

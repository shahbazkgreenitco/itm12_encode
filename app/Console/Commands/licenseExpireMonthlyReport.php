<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Mail;
use Carbon\Carbon;
use App\Mail\LicenseExpireMonthlyReport as LicenseExpireMail;
use App\Models\Settings;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LicenseExpireReport;
use App\Helpers\Common as CommonHelper;
use App\Models\Currency;

use PhpOffice\PhpSpreadsheet\Shared\Date as OfficeDate;

class licenseExpireMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:licenseExpireMonthlyReport';

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
        if (!config('mail.service_enabled') ||!$settings->alerts_enabled ||empty($globalAlertEmails) ||!array_filter($globalAlertEmails, function($email) {return filter_var($email, FILTER_VALIDATE_EMAIL);})) {
            return;
        }

        $now = new Carbon(config('app.timezone'));
        $start = $now->copy()->subMonths(1);
        $end = $now->copy()->addMonths(1);

        $query = "SELECT lc.name, mnu.name AS manu_name, temp1.tot_seat, lc.purchase_cost, lc.currency, temp1.total_inuse, (temp1.tot_seat - temp1.total_inuse) AS tot_available, IF(lc.expiration_date < '" . $now->format('Y-m-d') . "', '1', '0') AS is_expired, lc.purchase_date, lc.expiration_date FROM licenses AS lc JOIN(SELECT ls.license_id, SUM(CASE WHEN ls.asset_id IS NOT NULL THEN 1 WHEN ls.assigned_to IS NOT NULL THEN 1 ELSE 0 END) total_inuse, SUM(CASE WHEN CURRENT_TIMESTAMP > lic.expiration_date THEN 1 ELSE 0 END) total_expired, COUNT(ls.id) AS tot_seat FROM license_seats AS ls LEFT JOIN licenses AS lic ON lic.id = ls.license_id where lic.expiration_date is not null and dayname(lic.expiration_date) is not null and lic.deleted_at IS NULL and ls.deleted_at IS NULL and lic.expiration_date >= '". $start->format('Y-m-d') . "' and lic.expiration_date <= '". $end->format('Y-m-d') . "' GROUP BY ls.license_id) AS temp1 ON temp1.license_id = lc.id LEFT JOIN manufacturers AS mnu ON mnu.id = lc.manufacturer_id ORDER BY lc.expiration_date asc";

        $result = DB::select($query);
        $currency = Currency::getCurrencies();
        $tot = 0;
        $file_name = "";
        $data = [];
        if(is_array($result) && $tot = count($result)) {
            foreach($result as $row) {
                $expire_date = new Carbon($row->expiration_date, config('app.timezone'));
                $purchase_date = new Carbon($row->purchase_date, config('app.timezone'));
                $currency_symbol = $currency[$row->currency]['symbol'] ?? $row->currency;

                $data[] = [
                    "License Name" => $row->name,
                    "Manufacturer" => $row->manu_name,
                    "Expire Date" => $row->expiration_date,
                    "Current Status" => $row->is_expired == 1 ? "Expired" : "Not Expired",
                    "Total Seats" => $row->tot_seat,
                    "Seats In Use" => $row->total_inuse,
                    "Remaining Seats" => $row->tot_available,
                    "Purchase Cost" => $currency_symbol . ' ' . number_format($row->purchase_cost, 2),
                    "Purchase Date (dd/mm/yyyy)" => $row->purchase_date
                ];
            }

            $file_name = 'LicenseExpireReport_' . $now->format('dmY').'.xlsx';
            $keys = ['License Name', 'Manufacturer', 'Expire Date', 'Current Status', 'Total Seats', 'Seats In Use', 'Remaining Seats', 'Purchase Cost', 'Purchase Date (dd/mm/yyyy)'];
            /*\Excel::create($file_name, function($excel) use(&$data) {
                $excel->sheet('Licenses', function($sheet) use(&$data) {
                    $sheet->setColumnFormat(['C'=>'dd/mm/YYYY', 'H'=>'dd/mm/YYYY']);
                    $sheet->fromArray($data);
                });
            })->store('xlsx', storage_path('license_expire'));*/
            Excel::store(new LicenseExpireReport($data, $keys), $file_name, 'license_expire');
        }

        $get_additional_emails = config('app.report_additional_emails.license_expire_monthly');
        if($get_additional_emails) {
            $additional_emails = explode(",", $get_additional_emails);
            Mail::to($globalAlertEmails)->cc($additional_emails)->send(new LicenseExpireMail($start, $end, $tot, $file_name));
        } else {
            Mail::to($globalAlertEmails)->send(new LicenseExpireMail($start, $end, $tot, $file_name));
        }
    }
}
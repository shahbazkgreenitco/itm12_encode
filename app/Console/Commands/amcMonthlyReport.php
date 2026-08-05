<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Mail;
use Carbon\Carbon;
use App\Mail\AmcMonthlyReport as AmcMail;
use App\Models\Settings;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DeviceImportStore;
use App\Helpers\Common as CommonHelper;

use PhpOffice\PhpSpreadsheet\Shared\Date as OfficeDate;

class amcMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:amcMonthlyReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send the amc expire devices list to client on periodically';

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
        try {
            $settings = Settings::getSettings();
            $globalAlertEmails = CommonHelper::getGlobalAlertEmail();
            if (!config('mail.service_enabled') ||!$settings->alerts_enabled ||empty($globalAlertEmails) ||!array_filter($globalAlertEmails, function($email) {return filter_var($email, FILTER_VALIDATE_EMAIL);})){
               return;
            }

            $now = new Carbon(config('app.timezone'));
            $end = $now->copy()->addMonths(1);

            $query = "SELECT device.asset_tag,device.name,device.serial, mnf.name AS 'manufacturer', mdl.name AS 'model', mdl.modelno, stslbl.name AS 'status', loc.name AS 'location', device.amc_expire_date, IF(device.amc_expire_date > CURRENT_TIMESTAMP, 'Yes', 'No') AS 'amc_available', device.purchase_date, supp.name AS 'supplier', supp.phone AS 'supp_phone', supp.email AS 'supp_email' FROM assets AS device LEFT JOIN models AS mdl ON device.model_id = mdl.id AND device.deleted_at IS NULL AND mdl.deleted_at IS NULL LEFT JOIN manufacturers AS mnf ON mnf.id = mdl.manufacturer_id AND mnf.deleted_at IS NULL LEFT JOIN status_labels AS stslbl ON stslbl.id = device.status_id AND stslbl.deleted_at IS NULL LEFT JOIN locations AS loc ON device.rtd_location_id = loc.id AND loc.deleted_at IS NULL LEFT JOIN suppliers AS supp ON device.amc_supplier_id = supp.id AND supp.deleted_at IS NULL where device.deleted_at is null and device.status_id not in (3,7) and dayname(device.amc_expire_date) is not null and device.amc_expire_date >= '" . $now->format('Y-m-d') . "' and device.amc_expire_date <= '". $end->format('Y-m-d') . "' order by device.amc_expire_date asc";

            $result = DB::select($query);
            $this->info(count($result));

            $tot = 0;
            $file_name = "";

            if(is_array($result) && $tot = count($result)) {
                $data = [];
                foreach($result as $row) {
                    $amc_expire_date = new Carbon($row->amc_expire_date, config('app.timezone'));
                    $purchase_date = new Carbon($row->purchase_date, config('app.timezone'));

                    $data[] = [
                        $row->asset_tag,
                        $row->name,
                        $row->serial,
                        $row->manufacturer,
                        $row->model,
                        $row->modelno,
                        $row->amc_available,
                        \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(OfficeDate::dateTimeToExcel($amc_expire_date))->format('Y-m-d'),
                        $row->status,
                        $row->location,
                        \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(OfficeDate::dateTimeToExcel($purchase_date))->format('Y-m-d'),
                        $row->supplier,
                        $row->supp_phone,
                        $row->supp_email
                    ];
                }

                $keys = array("Device Tag", "Device Name", "Serial", "Manufacturer", "Model", "Model No", "AMC Available", "AMC Expire Date (dd/mm/yyyy)", "Device Status", "Location", "Purchase Date (dd/mm/yyyy)", "Supplier Name", "Supplier Phone", "Supplier Email");
                $file_name = 'AmcExpireReport_' . $now->format('dmY').'.xlsx';

                $doc_path = Excel::store(new DeviceImportStore($data, $keys), $file_name, 'device_amc');
            }
        } catch(\Exception $e) {
            Log::error("amcMonthlyReport error: " . $e->getMessage());
        }
        $this->info("success");
        Mail::to($globalAlertEmails)->send(new AmcMail($now, $end, $tot, $file_name));
    }
}

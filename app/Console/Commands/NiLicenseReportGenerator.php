<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Mail;
use Carbon\Carbon;
use App\Mail\LicenseExpireMonthlyReport as LicenseExpireMail;
use App\Models\Settings;

use PhpOffice\PhpSpreadsheet\Shared\Date as OfficeDate;

class NiLicenseReportGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:NiLicenseReportGenerator {limit=8000} {showCount=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To generate the network inventory license';

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

        // if(!config('mail.service_enabled')) {
        //     return;
        // }

        $select_fields = "select assets.asset_tag, b.ComputerName, b.ComputerManufacturer, b.ComputerModel, b.BIOSSerialNumber, DATE_FORMAT(b.updated_at, '%d %b %Y') as updated_at_format, b.id AS basic_id, assets.asset_tag, assets.id as asset_id, loc.name as loc_name, sw.Caption as sw_name, sw.Version as sw_version, sw.Publisher, DATE_FORMAT(sw.installedDate, '%d %b %Y') as install_date";

        $count_fields = "select count(*) as tot";

        $query = "FROM itm_network_inventory_basic as b 
        left join assets on b.BIOSSerialNumber like assets.serial
        left join itm_network_inventory_products as sw on b.id = sw.basic_id
        left join locations as loc on assets.rtd_location_id = loc.id where b.is_dupe is null and b.deleted_at is null";

        $get_count = DB::select($count_fields . " " . $query);

        if($this->argument('showCount') === true) {
          $this->info("Total Records: " . $get_count[0]->tot);
          return;
        }

        $limit = $this->argument('limit');
        $limit = ($limit < 1 || !$limit) ? 8000 : $limit;
        $tot_files = ceil($get_count[0]->tot / $limit);
        $order = "order by b.id asc";
        $where = "";

        $now = new Carbon(config('app.timezone'));
        $file_links = [];
        $i = 1;
        do {
          $start = ($i * $limit) - $limit;
          $data_query = $select_fields . " " . $query . " " . $where . " " . $order . ' limit ' . $limit . ' offset ' . $start;
          $records = DB::select($data_query);

          $data = [];
          foreach($records as $r) {
            $data[] = [
                $r->ComputerName,
                $r->asset_tag,
                $r->BIOSSerialNumber,
                $r->ComputerManufacturer,
                $r->ComputerModel,
                $r->loc_name,
                $r->sw_name,
                $r->sw_version,
                $r->Publisher,
                $r->install_date,
            ];
          }

          $file_name = 'LicenseDetails_' . $now->format('Y_m_d') . '_' . $i;
          $file_links[] = url('licenses/' . $file_name . '.xlsx');

          \Excel::create($file_name, function($excel) use($data) {
            $excel->sheet('Sheet1', function($sheet) use($data) {
              $sheet->fromArray((array) $data, null, 'A1', true);
              $sheet->row(1, array(
                'Device Name','Device Tag','Serial No','Manufacturer','Device Modal','Location Name','License Name','Version','Publisher','Installation Date'
              ));
            });
          })->store('xlsx', public_path('licenses'));

          $i++;
        } while($i <= $tot_files);

        $msg = implode("\n", $file_links);
        $this->info($msg);
        Mail::raw($msg, function ($message) {
            $message->to('vtmragaven@gmail.com', 'Ragavendhira');
            $message->subject('NI License Bulk Reports');
        });

        // $now = new Carbon(config('app.timezone'));
        // $start = $now->copy()->subMonths(1);
        // $end = $now->copy()->addMonths(1);

        // $query = "SELECT lc.name, mnu.name AS manu_name, temp1.tot_seat, temp1.total_inuse, (temp1.tot_seat - temp1.total_inuse) AS tot_available, IF(lc.expiration_date < '" . $now->format('Y-m-d') . "', '1', '0') AS is_expired, lc.purchase_date, lc.expiration_date FROM licenses AS lc JOIN(SELECT ls.license_id, SUM(CASE WHEN ls.asset_id IS NOT NULL THEN 1 WHEN ls.assigned_to IS NOT NULL THEN 1 ELSE 0 END) total_inuse, SUM(CASE WHEN CURRENT_TIMESTAMP > lic.expiration_date THEN 1 ELSE 0 END) total_expired, COUNT(ls.id) AS tot_seat FROM license_seats AS ls LEFT JOIN licenses AS lic ON lic.id = ls.license_id where lic.expiration_date is not null and dayname(lic.expiration_date) is not null and lic.deleted_at IS NULL and ls.deleted_at IS NULL and lic.expiration_date >= '". $start->format('Y-m-d') . "' and lic.expiration_date <= '". $end->format('Y-m-d') . "' GROUP BY ls.license_id) AS temp1 ON temp1.license_id = lc.id LEFT JOIN manufacturers AS mnu ON mnu.id = lc.manufacturer_id ORDER BY lc.expiration_date asc";

        // $result = DB::select($query);

        // $tot = 0;
        // $file_name = "";

        // if(is_array($result) && $tot = count($result)) {
        //     $data = [];
        //     foreach($result as $row) {
        //         $expire_date = new Carbon($row->expiration_date, config('app.timezone'));
        //         $purchase_date = new Carbon($row->purchase_date, config('app.timezone'));

        //         $data[] = [
        //             "License Name" => $row->name,
        //             "Manufacturer" => $row->manu_name,
        //             "Expire Date" => OfficeDate::dateTimeToExcel($expire_date),
        //             "Current Status" => $row->is_expired == 1 ? "Expired" : "Not Expired",
        //             "Total Seats" => $row->tot_seat,
        //             "Seats In Use" => $row->total_inuse,
        //             "Remaining Seats" => $row->tot_available,
        //             "Purchase Date (dd/mm/yyyy)" => OfficeDate::dateTimeToExcel($purchase_date)
        //         ];
        //     }

        //     $file_name = 'LicenseExpireReport_' . $now->format('dmY');

        //     \Excel::create($file_name, function($excel) use(&$data) {
        //         $excel->sheet('Licenses', function($sheet) use(&$data) {
        //             $sheet->setColumnFormat(['C'=>'dd/mm/YYYY', 'H'=>'dd/mm/YYYY']);
        //             $sheet->fromArray($data);
        //         });
        //     })->store('xlsx', storage_path('license_expire'));
        // }
    }
}
<?php

namespace App\Console\Commands;

use App\Imports\DeviceImportStore;
use Illuminate\Console\Command;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Carbon\Carbon;
use App\Mail\WarrantyMonthlyReport as WarrantyMail;
use App\Models\Settings;
use App\Helpers\Common as CommonHelper;

use PhpOffice\PhpSpreadsheet\Shared\Date as OfficeDate;

class warrantyMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:warrantyMonthlyReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send the warranty expire devices list to client on periodically';

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
        if (!config('mail.service_enabled') || !$settings->alerts_enabled || empty($globalAlertEmails) || !array_filter($globalAlertEmails, function($email) {return filter_var($email, FILTER_VALIDATE_EMAIL);})) {
            return;
        }

        $now = new Carbon(config('app.timezone'));
        $end = $now->copy()->addMonths(1);
        
        $query = "SELECT dev.id, dev.asset_tag, mnf.name AS 'manufacturer', mdl.name AS 'model', mdl.modelno, cmp.name AS 'company_name', dev.name, dev.serial, cat.name AS 'cat_name', pla.place AS 'internal_place', dev.purchase_cost, dev.notes, dev.order_number, concat(own.first_name, '', own.last_name) AS 'asset_owner', dev.ip, dev.mac, ast.name AS 'asset_type_name', CASE WHEN dev.device_occure_type = 1 THEN 'Project Device' WHEN dev.device_occure_type = 2 THEN 'Rental' WHEN dev.device_occure_type = 3 THEN 'Customer Owned' ELSE 'Purchase Device' END AS 'device_occure_type_name',
        CASE WHEN dev.assigned_to IS NOT NULL AND pldev.place IS NOT NULL THEN concat(pldev.place, '', pldevloc.name, '') ELSE NULL END AS 'stock_place', dep.name as 'department_name', IF( dev.calc_warranty_expire_date < CURRENT_TIMESTAMP, 'Yes', 'No' ) AS 'warranty_expired', sl.name AS 'status', loc.name AS 'location', dev.purchase_date, dev.warranty_months, supp.name AS 'supplier', supp.phone, supp.email, dev.warranty_start_date, dev.calc_warranty_expire_date
        FROM assets AS dev LEFT JOIN models AS mdl ON mdl.id = dev.model_id AND dev.deleted_at IS NULL 
        LEFT JOIN manufacturers AS mnf ON mdl.manufacturer_id = mnf.id 
        LEFT JOIN companies AS cmp ON cmp.id = dev.company_id
        LEFT JOIN status_labels AS sl ON sl.id = dev.status_id 
        LEFT JOIN locations AS loc ON loc.id = dev.rtd_location_id 
        LEFT JOIN suppliers AS supp ON supp.id = dev.supplier_id 
        LEFT JOIN categories AS cat ON mdl.category_id = cat.id
        LEFT JOIN places AS pla ON pla.id = dev.internal_place_id
        LEFT JOIN users AS own ON own.id = dev.asset_owner
        LEFT JOIN assets_types AS ast ON dev.asset_type_id = ast.id
        LEFT JOIN places AS pldev ON pldev.id = dev.stock_place
        LEFT JOIN locations AS pldevloc ON pldevloc.id = pldev.location_id
        LEFT JOIN departments AS dep ON dep.id = dev.department_id
        where dev.deleted_at is null and
        dev.calc_warranty_expire_date >= '" . $now->format('Y-m-d') . "' and 
        dev.calc_warranty_expire_date <= '". $end->format('Y-m-d') . "' 
        order by dev.id";

        $result = DB::select($query);
        $this->info(count($result));

        $tot = 0;
        $file_name = "";

        if(is_array($result) && $tot = count($result)) {
            $data = [];
            foreach($result as $row) {
                $wed = new Carbon($row->calc_warranty_expire_date, config('app.timezone'));
                $purchase_date = new Carbon($row->purchase_date, config('app.timezone'));

                $data[] = [
                    "Company" => $row->company_name,
                    "Device Name" => $row->name,
                    "Device Tag" => $row->asset_tag,
                    "Serial" => $row->serial,
                    "Manufacturer" => $row->manufacturer,
                    "Model" => $row->model,
                    "Model No" => $row->modelno,
                    "Category" => $row->cat_name,
                    "Department" => $row->department_name,
                    "Location" => $row->location,
                    "Internal Place" => $row->internal_place,
                    "Warranty Expired" => $row->warranty_expired,
                    "Warranty Start Date" => $row->warranty_start_date,
                    "Warranty Expire Date" => $row->calc_warranty_expire_date,
                    "Warranty Months" => $row->warranty_months,
                    "Device Status" => $row->status,
                    "Purchase Date" => $row->purchase_date,
                    "Purchase Cost" => $row->purchase_cost,
                    "Supplier Name" => $row->supplier,
                    "Supplier Phone" => $row->phone,
                    "Supplier Email" => $row->email,
                    "Notes" => $row->notes,
                    "Order Number" => $row->order_number,
                    "Asset Owner" => $row->asset_owner,
                    "IP" => $row->ip,
                    "MAC" => $row->mac,
                    "Device Type" => $row->asset_type_name,
                    "Device From" => $row->device_occure_type_name,
                    "Stock Place" => $row->stock_place
                ];
            }

            $file_name = 'WarrantyExpireReport_' . $now->format('dmY').'.xlsx';
            $keys = array("Company","Device Name","Device Tag","Serial","Manufacturer","Model", "Model No","Category","Department","Location", "Internal Place", "Warranty Expired", "Warranty Start Date", "Warranty Expire Date (dd/mm/yyyy)", "Warranty Months", "Device Status", "Purchase Date (dd/mm/yyyy)","Purchase Cost", "Supplier Name", "Supplier Phone", "Supplier Email", "Notes", "Order Number", "Asset Owner", "IP","MAC","Device Type","Device From","Stock Place");
            /*\Excel::create($file_name, function($excel) use(&$data) {
                $excel->sheet('Devices', function($sheet) use(&$data) {
                    $sheet->setColumnFormat(['F'=>'dd/mm/YYYY', 'J'=>'dd/mm/YYYY']);
                    $sheet->fromArray($data);
                });
            })->store('xlsx', storage_path('device_warranty'));*/
            $doc_path = Excel::store(new DeviceImportStore($data, $keys), $file_name, 'device_warranty');
        }

        Mail::to($globalAlertEmails)->send(new WarrantyMail($now, $end, $tot, $file_name));
    }
}

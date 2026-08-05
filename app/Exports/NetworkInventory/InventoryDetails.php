<?php

namespace App\Exports\NetworkInventory;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Traits\ExcelExportTrait;

class InventoryDetails implements FromArray,WithHeadings,WithEvents
{
    use ExcelExportTrait;
    public $outputData;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($arrays)
    {
        $this->outputData = $arrays;
    }
    public function headings(): array
    {
        return [
            trans('devices/ni/ni_listing.company'),
            trans('devices/ni/ni_listing.device_name'),
            trans('devices/ni/ni_listing.device_tag'),
            trans('devices/ni/ni_listing.domain'),
            trans('devices/ni/ni_listing.manufacturer'),
            trans('devices/ni/ni_listing.model'),
            trans('devices/ni/ni_listing.serial_no'),
            trans('devices/ni/ni_listing.os_caption'),
            trans('devices/ni/ni_listing.os_serial_number'),
            trans('devices/ni/ni_listing.os_version'),
            trans('devices/ni/ni_listing.hard_disk_size'),
            trans('devices/ni/ni_listing.hard_disk_count'),
            trans('devices/ni/ni_listing.ram_size'),
            trans('devices/ni/ni_listing.processor'),
            trans('devices/ni/ni_listing.system_type'),
            trans('devices/ni/ni_listing.status'),
            trans('devices/ni/ni_listing.location'),
            trans('devices/ni/ni_listing.stock_place'),
            trans('devices/ni/ni_listing.category'),
            trans('devices/ni/ni_listing.notes'),
            trans('devices/ni/ni_listing.purchase_date'),
            trans('devices/ni/ni_listing.purchase_invoice'),
            trans('devices/ni/ni_listing.checkout_date'),
            trans('devices/ni/ni_listing.assigned_user'),
            trans('devices/ni/ni_listing.device_type'),
            trans('devices/ni/ni_listing.asset_owner'),
            trans('devices/ni/ni_listing.asset_classification'),
            trans('devices/ni/ni_listing.ip'),
            trans('devices/ni/ni_listing.mac'),
            trans('devices/ni/ni_listing.updated_date'),
            trans('devices/ni/ni_listing.last_logon_user_account'),
            trans('devices/ni/ni_listing.last_logon_date'),
            trans('devices/ni/ni_listing.license_activated'),
            trans('devices/ni/ni_listing.license_status'),
        ];
    }
    public function array(): array
    {
        return $this->outputData;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $this->applyExcelHeader(
                    $event,
                    $this->headings(),
                    trans('devices/ni/ni_listing.ni_export_devices')
                );
            },
        ];
    }

    
}

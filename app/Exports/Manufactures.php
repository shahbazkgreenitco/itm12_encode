<?php

namespace App\Exports;

use Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;

    /**
    * @return \Illuminate\Support\Collection
    */
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Helpers\Common as CommonHelper;
use App\Traits\ExcelExportTrait;


class Manufactures implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents
{
    use Exportable, ExcelExportTrait;

    private $collection;
    public $columns;

    public function __construct($arrays)
    {
        $output = [];
        foreach ($arrays as $array) {
            $output[] = array_values($array);
        }
        $this->collection = collect($output);
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [trans('config.manufacturer_fields.id'),trans('config.manufacturer_fields.manufacturer_name'),];
    }

    public function title(): string
    {
        return 'Manufacturer';
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $this->applyExcelHeader(
                    $event,
                    $this->headings(),
                    trans('config.manufacturer_fields.manufacturer_export')
                );
            },
        ];
    }
}
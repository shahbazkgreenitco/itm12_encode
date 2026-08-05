<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Helpers\Common as CommonHelper;
use App\Traits\ExcelExportTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ModelExport implements FromCollection, WithEvents, WithTitle,WithHeadings
{
    use Exportable, ExcelExportTrait;

    private Collection $collection,$output_data;

    public function __construct(array $data)
    {
        $this->collection = collect($data);
    }

    public function collection()
    {
        return $this->collection;
    }

     public function array(): array
    {
        return $this->output_data;
    }

    public function headings(): array
    {
        return [
            trans('config.model_fields.manufacturer'),
            trans('config.model_fields.model'),
            trans('config.model_fields.model_no'),
            trans('config.model_fields.depreciation'),
            trans('config.model_fields.category'),
            trans('config.model_fields.device_count'),
            trans('config.model_fields.eol'),
        ];
    }
 
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $this->applyExcelHeader(
                    $event,
                    $this->headings(),
                    trans('config.model_fields.model_export')
                );
            },
        ];
    }

    public function title(): string
    {
        return trans('config.model_fields.models');
    }
}
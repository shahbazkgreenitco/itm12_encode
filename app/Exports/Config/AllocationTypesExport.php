<?php

namespace App\Exports\Config;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Helpers\Common as CommonHelper;

class AllocationTypesExport implements WithEvents, WithTitle
{
    public $outputData, $keys;

    public function __construct($arrays, $keys)
    {
        $this->outputData = $arrays;
        $this->keys = $keys;
    }

    public function headings(): array
    {
        return $this->keys;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $drawing = new Drawing();
                if (
                    CommonHelper::settings()->logo_thumbnail != null &&
                    file_exists(public_path("uploads/" . CommonHelper::settings()->logo_thumbnail))
                ) {
                    $drawing->setPath(public_path("uploads/" . CommonHelper::settings()->logo_thumbnail));
                }
                $drawing->setCoordinates('A1');
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
                $sheet->mergeCells('B1:E1');
                $sheet->setCellValue('B1', 'Allocation Types Export');
                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCC');
                $lastColumnIndex = count($this->keys);
                $lastColumnLetter = Coordinate::stringFromColumnIndex($lastColumnIndex);
                $sheet->getStyle("A3:{$lastColumnLetter}3")->getFont()->setBold(true);
                $sheet->getStyle("A3:{$lastColumnLetter}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFF');
                $sheet->fromArray([$this->headings()], null, 'A3');

                $formattedData = [];
                foreach ($this->outputData as $row) {
                    $formattedData[] = array_values($row);
                }

                $sheet->fromArray($formattedData, null, 'A4');
                $sheet->getRowDimension('1')->setRowHeight(60);

                foreach (range(1, $lastColumnIndex) as $col) {
                    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
                }
            }
        ];
    }

    public function title(): string
    {
        return 'Allocation Types';
    }
}

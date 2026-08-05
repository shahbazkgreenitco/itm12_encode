<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Helpers\Common as CommonHelper;

class HolidayExport implements WithEvents, WithTitle
{
    public $outputData, $keys;

    public function __construct($arrays)
    {
        $this->outputData = [];

        foreach ($arrays as $array) {
            if (isset($array['a'])) {
                $this->outputData[] = [
                    $array['a']->company_name ?? null,
                    $array['a']->name ?? null,
                    $array['a']->date_name ?? null,
                    $array['a']->location_name ?: null,
                    $array['a']->countries_name ?: null,
                    $array['a']->created_by ?? null,
                    $array['a']->created_at ?? null,
                    $array['a']->last_updated_at ?? null,
                ];
            }
        }

        $this->keys = [
            'Company Name',
            'Holiday Name',
            'Date',
            'Location',
            'Country',
            'Created By',
            'Created At',
            'Updated At'
        ];
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
                $sheet->mergeCells('B1:H1');
                $sheet->setCellValue('B1', 'Holiday Export');
                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCC');
                $sheet->fromArray([$this->keys], null, 'A3');
                $lastColumnIndex = count($this->keys);
                $lastColumnLetter = Coordinate::stringFromColumnIndex($lastColumnIndex);
                $sheet->getStyle("A3:{$lastColumnLetter}3")->getFont()->setBold(true);
                $sheet->getStyle("A3:{$lastColumnLetter}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFF');
                $sheet->fromArray($this->outputData, null, 'A4');
                $sheet->getRowDimension('1')->setRowHeight(60);
                foreach (range(1, $lastColumnIndex) as $col) {
                    $sheet->getColumnDimension(
                        Coordinate::stringFromColumnIndex($col)
                    )->setAutoSize(true);
                }
            }
        ];
    }

    public function title(): string
    {
        return 'Holidays';
    }
}
// {{-- * ------------------------------------------------------------
// * File: HolidayExport.php
// * Module: Holiday Module
// * HOLY/26/04
// * ------------------------------------------------------------
// * Version: 1.0.0
// * Author: Hrishikesh Pandey
// * Page ID: #004
// * Reviewed By: 
// * ------------------------------------------------------------
// * Change Log:
// * [1.0.0] - Initial version (Hrishikesh Pandey)
// * ------------------------------------------------------------ --}}

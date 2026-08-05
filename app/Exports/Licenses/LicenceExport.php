<?php

namespace App\Exports\Licenses;

use Auth;
use App\Helpers\Common as CommonHelper;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LicenceExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    use Exportable;

    public $outputData, $keys;

    public function __construct($arrays, $keys)
    {
        $this->outputData = $arrays;
        $this->keys = $keys;
    }

    public function headings(): array
    {
        return [];
    }

    public function array(): array
    {
        return $this->outputData;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestDataRow();

        $sheet->getStyle("A4:" . $sheet->getHighestColumn() . $highestRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // Add two rows on top
                $sheet->insertNewRowBefore(1, 2);

                $lastCol = Coordinate::stringFromColumnIndex(count($this->keys));

                /*
                 * Logo
                 */
                $drawing = new Drawing();
                $logo = CommonHelper::settings()->logo_thumbnail;

                if ($logo) {

                    $logoPath = public_path("uploads/settings/" . $logo);

                    if (!file_exists($logoPath)) {
                        $logoPath = public_path("uploads/" . $logo);
                    }

                    if (file_exists($logoPath)) {
                        $drawing->setPath($logoPath);
                        $drawing->setCoordinates('A1');
                        $drawing->setHeight(55);
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setWorksheet($sheet);
                    }
                }

                /*
                 * Title
                 */
                $sheet->mergeCells("B1:F1");
                $sheet->setCellValue('B1', 'License Export');

                $sheet->getStyle('B1')->getFont()
                    ->setBold(true)
                    ->setSize(16);

                $sheet->getStyle("A1:F1")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFFCC');

                /*
                 * Export Info
                 */
                $user = Auth::user();

                $exportBy = $user->display_name ?? $user->username ?? '';
                $location = $user->location->name ?? '';
                $company = $user->company->name ?? '';
                $timestamp = now()->format('d-m-Y h:i A');

                $sheet->mergeCells("A2:{$lastCol}2");

                $sheet->setCellValue(
                    "A2",
                    "Exported From: {$company} Portal  |   Exported By: {$exportBy}   |   Exported Location: {$location}   |   Exported Time: {$timestamp}"
                );

                $sheet->getStyle("A2")->getFont()->setSize(10);

                /*
                 * Header
                 */
                $sheet->fromArray([$this->keys], null, 'A3');

                $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);

                $sheet->getStyle("A3:{$lastCol}3")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFCCFF');

                /*
                 * Row Height
                 */
                $sheet->getRowDimension('1')->setRowHeight(60);
                $sheet->getRowDimension('3')->setRowHeight(22);

                /*
                 * Write data from Row 4
                 */
                $row = 4;

                foreach ($this->outputData as $data) {

                    $col = 1;

                    foreach ($data as $value) {
                        $sheet->setCellValue(
                            Coordinate::stringFromColumnIndex($col) . $row,
                            $value
                        );
                        $col++;
                    }

                    $row++;
                }
            },
        ];
    }
}

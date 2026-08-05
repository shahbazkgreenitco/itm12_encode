<?php

namespace App\Exports\Device;

use App\Helpers\Common as CommonHelper;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Device;
use Illuminate\Support\Facades\Schema;
use App\Models\Device\DeviceSetting;
use App\Models\Model;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Auth;

class DeviceExport implements FromCollection, WithHeadings, WithColumnFormatting, WithStyles, WithEvents
{
    use Exportable;

    public $collection, $keys, $columns;

    public function __construct($arrays, $keys)
    {
        $output = [];
        foreach ($arrays as $array) {
            if (isset($array["manufacturer_warranty_data"])) {
                $warrentyInfo = ($array["manufacturer_warranty_data"] != null) ? json_decode($array["manufacturer_warranty_data"], true) : array();
                $oemWarrantyData = "";
                if (isset($array["manu_name"]) && stripos($array["manu_name"], "dell") !== false && count($warrentyInfo)) {
                    foreach ($warrentyInfo['info'] as $wi) {
                        if (is_array($wi) && isset($wi['endDate'])) {
                            $isInWarrenty = CommonHelper::isInWarrenty($wi['endDate']);
                            if (count($isInWarrenty)) {
                                $oemWarrantyData .= $wi['serviceLevelDescription'] . ' - ' . $isInWarrenty['date'] . ' ';
                            }
                        }
                    }
                } elseif (isset($array["manu_name"]) && stripos($array["manu_name"], "lenovo") !== false && count($warrentyInfo)) {
                    foreach ($warrentyInfo as $wi) {
                        if (is_array($wi) && isset($wi['End'])) {
                            $isInWarrenty = CommonHelper::isInWarrenty($wi['End']);
                            if (count($isInWarrenty)) {
                                $oemWarrantyData .= $wi['Name'] . ' - ' . $wi['Description'] . ' - Expire Date:' . $isInWarrenty['date'] . ' ';
                            }
                        }
                    }
                }
                $array["manufacturer_warranty_data"] = $oemWarrantyData;
            }
            $objDeviceSettings = DeviceSetting::first();
            $device_export_column = isset($objDeviceSettings->device_export_column) && $objDeviceSettings->device_export_column != null ? explode(',', $objDeviceSettings->device_export_column) : [];
            $export_column_empty = false;
            if (empty($device_export_column)) {
                $export_column_empty = true;
            }

            if ($array['id']) {
                unset($array['id']);
            }
            $array = array_slice($array, 2);
            $output[] = array_values($array);
        }

        $this->collection = collect($output);
        $this->keys = $keys;
        $this->columns = $keys;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [];
    }

    // public function columnFormats(): array
    // {
    //     return [];
    // }
    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_NUMBER,
            'N' => NumberFormat::FORMAT_NUMBER,
            'O' => NumberFormat::FORMAT_NUMBER,
            'U' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    // public function styles(Worksheet $sheet)
    // {
    //     $highestRow = $sheet->getHighestDataRow();
    //     return [];
    // }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestDataRow();

        foreach (['K', 'N', 'O', 'U', 'X', 'Y', 'Z', 'AA'] as $col) {
            $sheet->getStyle("{$col}4:{$col}{$highestRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->insertNewRowBefore(1, 2);

                $lastCol = Coordinate::stringFromColumnIndex(count($this->columns));

                $drawing = new Drawing();
                $logo = CommonHelper::settings()->logo_thumbnail;
                if ($logo) {
                    $logoPath = public_path("uploads/settings/" . $logo);
                    if (!file_exists($logoPath)) {
                        $logoPath = public_path("uploads/" . $logo);
                    }
                    if (file_exists($logoPath)) {
                        $drawing->setPath($logoPath);
                        $drawing->setHeight(55);
                        $drawing->setCoordinates('A1');
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setWorksheet($sheet);
                    }
                }

                $sheet->mergeCells("B1:F1");
                $sheet->setCellValue('B1', 'Device Export');
                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16);

                $sheet->getStyle("A1:F1")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFCC');

                $user = Auth::user();
                $exportBy = $user->display_name ?? $user->username ?? '';
                $location = $user->location->name ?? '';
                $company = $user->company->name ?? '';
                $timestamp = now()->format('d-m-Y h:i A');
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->setCellValue("A2", "Exported From: $company Portal  |   Exported By: $exportBy   |   Exported Location: $location   |   Exported Time: $timestamp");
                $sheet->getStyle("A2")->getFont()->setSize(10);

                $sheet->fromArray([$this->columns], '', 'A3');
                $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
                $sheet->getStyle("A3:{$lastCol}3")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFCCFF');

                $sheet->getRowDimension('1')->setRowHeight(60);
                $sheet->getRowDimension('3')->setRowHeight(22);

                $rowIndex = 4;
                foreach ($this->collection as $row) {
                    $colIndex = 1;
                    foreach ($row as $value) {
                        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $rowIndex, $value);
                        $colIndex++;
                    }
                    $rowIndex++;
                }
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}

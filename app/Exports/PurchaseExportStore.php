<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PurchaseExportStore implements FromArray, WithHeadings, WithColumnFormatting, WithColumnWidths
{
    public $outputData,
        $keys;

    /**
     * Constructor to initialize data and keys
     */
    public function __construct($arrays, $keys)
    {
        $this->outputData = $arrays;
        $this->keys = $keys;
    }

    /**
     * Return the headings for the Excel file
     */
    public function headings(): array
    {
        return $this->keys;
    }

    /**
     * Return the data array for the Excel file
     */
    public function array(): array
    {
        return $this->outputData;
    }

    /**
     * Format specific columns in the Excel sheet
     */
    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'I' => 15,
            'J' => 15,
        ];
    }
}

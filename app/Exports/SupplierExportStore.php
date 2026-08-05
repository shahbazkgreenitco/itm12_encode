<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings; 


class SupplierExportStore implements FromArray, WithHeadings
{
    public $outputData, $keys;

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
}
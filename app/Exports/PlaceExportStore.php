<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlaceExportStore implements FromArray, WithHeadings
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

    public function array(): array
    {
        return $this->outputData;
    }
}

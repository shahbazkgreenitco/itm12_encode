<?php

namespace App\Imports\Accessories;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccessoryImportStore implements FromArray, WithHeadings
{
    public $outputData, $keys;
    /**
     * @return \Illuminate\Support\Collection
     */
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

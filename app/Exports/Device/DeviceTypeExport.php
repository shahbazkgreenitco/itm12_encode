<?php

namespace App\Exports\Device;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DeviceTypeExport implements FromCollection, WithHeadings
{
    /** @return \Illuminate\Support\Collection */
    use Exportable;

    private $collection;

    public function __construct($arrays, $keys)
    {
        $output = [];
        foreach ($arrays as $array) {
            $output[] = array_values($array);
        }
        $this->collection = collect($output);
        $this->keys = $keys;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return $this->keys;
    }
}

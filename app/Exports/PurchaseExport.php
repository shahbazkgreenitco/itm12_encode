<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseExport implements FromCollection, WithHeadings
{
    /** @return \Illuminate\Support\Collection */
    use Exportable;

    private $collection;

    public function __construct($arrays)
    {
        $output = [];
        foreach ($arrays as $array) {
            $output[] = array_values($array);
        }
        $this->collection = collect($output);
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'Purchase Date', 'Invoice Number', 'Po Number', 'Received Date', 'Location', 'Company', 'Supplier', 'Bill Currency', 'Bill Amount', 'Amount With Tax', 'Status'
        ];
    }

    public function array(): array
    {
        return $this->outpur_data;
    }
}

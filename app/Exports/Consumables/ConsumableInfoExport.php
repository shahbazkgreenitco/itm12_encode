<?php

namespace App\Exports\Consumables;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class ConsumableInfoExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
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
            'Batch No', 'Unique Tag', 'Consumable Name', 'Company Name', 'Category', 'Manufacturer', 'Assigned For', 'Assigned to', 'Admin', 'CheckOut Date', 'Purchase Cost', 'Ticket ID'
        ];
    }
    public function array(): array
    {
        return $this->outpur_data;
    }
}

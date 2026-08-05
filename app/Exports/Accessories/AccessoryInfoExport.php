<?php

namespace App\Exports\Accessories;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class AccessoryInfoExport implements FromCollection,WithHeadings
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
            'Tag','Accessory Name', 'Category','Company Name','Assigned To','Assigned User/Place/Device','Purchase Date','Expected Checkin Date', 'Notes'
        ];
    }
    public function array(): array
    {
        return $this->outpur_data;
    }
}

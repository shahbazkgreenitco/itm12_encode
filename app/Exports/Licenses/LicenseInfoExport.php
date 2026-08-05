<?php

namespace App\Exports\Licenses;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class LicenseInfoExport implements FromCollection,WithHeadings
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
            'Seat Id','Serial Number' ,'License Name', 'Manufacturer', 'Category','Checkout Device','Device Name','Checkout Username','FullName','Email','Employee Code','Notes','Expected Checkin','Purchase Cost','Purchase Date','Expire Date','Is Expired','Location'
        ];
    }
    public function array(): array
    {
        return $this->outpur_data;
    }
}

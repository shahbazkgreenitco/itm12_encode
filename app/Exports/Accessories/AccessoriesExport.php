<?php
namespace App\Exports\Accessories;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccessoriesExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    private $collection, $output_data;

    public function __construct($arrays, $keys)
    {
        $output = [];
        foreach ($arrays as $array) {
            $output[] = array_values($array);
        }
        $this->collection = collect($output);
        $this->keys       = $keys;
    }
    public function collection()
    {
        return $this->collection;
    }
    public function headings(): array
    {
        return $this->keys;

        // return [
        //     'Batch No','Company','Accessory Name','Accessory Category','Manufacture','Supplier','Location','Scrap Qty','Purchase Date','Purchase Cost','Purchase Currency','Order Number','Notes','Remaining',
        // ];
    }
    public function array(): array
    {
        return $this->output_data;
    }
}

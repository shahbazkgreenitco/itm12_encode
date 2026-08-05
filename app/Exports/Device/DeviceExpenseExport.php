<?php

namespace App\Exports\Device;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class DeviceExpenseExport implements FromCollection, WithHeadings
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
            'Sr No',
            'Device Tag',
            'Expense Type',
            'Title',
            'Expense Date',
            'Under Warrenty',
            'Under AMC',
            'Cost format',
            'Notes',
            'Created On',
            'Updated On'
        ];
    }
}
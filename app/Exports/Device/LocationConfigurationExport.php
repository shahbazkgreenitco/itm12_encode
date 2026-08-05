<?php

namespace App\Exports\Device;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LocationConfigurationExport implements FromCollection, WithHeadings
{
    /** @return \Illuminate\Support\Collection */
    use Exportable;

    public $collection,
        $keys;

    public function __construct($arrays, $locations)
    {
        $output = [];
        foreach ($arrays as $value) {
            $lineArray = [$value->username];
            foreach ($locations as $location) {
                if ($value->permitted_locations != '' && $value->permitted_locations != null) {
                    $permitted_locations = explode(',', $value->permitted_locations);
                    if (in_array($location->id, $permitted_locations)) {
                        $lineArray[] = 'Yes';
                    } else {
                        $lineArray[] = 'No';
                    }
                } else {
                    $lineArray[] = 'No';
                }
            }
            $output[] = array_values($lineArray);
        }

        $this->collection = collect($output);
        $this->locations = $locations;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        $header = ['Username'];
        foreach ($this->locations as $location) {
            $header[] = $location->name;
        }
        return $header;
    }
}

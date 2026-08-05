<?php

namespace App\Imports\Device;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Helpers\Common as CommonHelper;
use Log;

class DeviceImportStore implements FromArray, WithHeadings
{
    public $outputData, $keys;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($arrays, $keys)
    {
        $output = [];
        foreach ($arrays as $array) {
            if(isset($array["manufacturer_warranty_data"])) {
                $warrentyInfo = ($array["manufacturer_warranty_data"] != null) ? json_decode($array["manufacturer_warranty_data"], true) : array();
                $oemWarrantyData = "";

                if(isset($array["manu_name"]) && stripos($array["manu_name"], "dell") !== false && count($warrentyInfo)) {
                    foreach($warrentyInfo['info'] as $wi) {
                        if(is_array($wi) && isset($wi['endDate'])) {
                            $isInWarrenty = CommonHelper::isInWarrenty($wi['endDate']);
                            if(count($isInWarrenty)) {
                                $oemWarrantyData .= $wi['serviceLevelDescription'] . ' - ' . $isInWarrenty['date'] . ' ';
                            }
                        }
                    }
                } elseif(isset($array["manu_name"]) && stripos($array["manu_name"], "lenovo") !== false && count($warrentyInfo)) {
                    foreach($warrentyInfo as $wi) {
                        if(is_array($wi) && isset($wi['End'])) {
                            $isInWarrenty = CommonHelper::isInWarrenty($wi['End']);
                            if(count($isInWarrenty)) {
                                $oemWarrantyData .= $wi['Name'] . ' - ' . $wi['Description'] . ' - Expire Date:' . $isInWarrenty['date'] . ' ';
                            }
                        }
                    }
                }
                $array["manufacturer_warranty_data"] = $oemWarrantyData;
            }

            $output[] = $array;
        }
        $this->outputData = $output;
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

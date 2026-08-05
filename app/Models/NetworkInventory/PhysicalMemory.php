<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class PhysicalMemory extends Model
{
    protected $table = "itm_network_inventory_physical_memories";
    protected $dateFormat = "Y-m-d H:i:s";
    // protected $fillable = ["basic_id", "MemoryType", "SerialNumber", "DeviceLocator", "Capacity", "Speed", "PartNumber", "Caption", "DataWidth", "Manufacturer", "Model", "Tag"];
    protected $guarded = [];

    public function isChangeThere($data) {
        foreach($data as $k=>$v) {
            try {
                if( $this->{$k} != $v ) {
                    return true;
                }
            }
            catch(\Exception $e) {

            }
        }
        return false;
    }

    public function dataForCache() {
        return (array) $this->only("basic_id", "MemoryType", "SerialNumber", "DeviceLocator", "Capacity", "Speed", "PartNumber", "Caption", "DataWidth", "Manufacturer", "Model", "Tag");
    }
}
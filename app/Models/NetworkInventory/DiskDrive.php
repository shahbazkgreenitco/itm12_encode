<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class DiskDrive extends Model
{
    protected $table = "itm_network_inventory_disk_drives";
    protected $dateFormat = "Y-m-d H:i:s";
    // protected $fillable = ["basic_id", "Caption", "InterfaceType", "Manufacturer", "Partitions", "SerialNumber", "Size"];
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
        return (array) $this->only("basic_id", "Caption", "InterfaceType", "Manufacturer", "Partitions", "SerialNumber", "Size", "MediaType");
    }
}
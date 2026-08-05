<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class NetworkAdapter extends Model
{
    protected $table = "itm_network_adapters";
    public $timestamps = false;
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
        return (array) $this->only("basic_id", "Name", "Description", "PhysicalAddress", "OperationalStatus", "NetworkInterfaceType");
    }
}
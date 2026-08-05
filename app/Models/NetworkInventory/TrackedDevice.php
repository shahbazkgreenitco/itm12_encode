<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Helpers\Common as CommonHelper;

class TrackedDevice extends Model
{
    protected $table = "itm_network_device_tracking";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    public function device() {
        return $this->belongsTo('App\Models\Device', 'device_id');
    }

    public function location() {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    public function place() {
        return $this->belongsTo('App\Models\Place', 'place_id');
    }

    public function formAsMessage() {
        $msg = "";
        if($this->place && $this->location) {
            $msg = 'Found at ' . $this->place->place . ', ' . $this->location->name;
        }
        elseif($this->location) {
            $msg = 'Found at ' . $this->location->name;
        }
        return $this->ip ? $msg . ' with IP: ' . $this->ip : $msg;
    }
}
<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class SoundDevice extends Model
{
    protected $table = "itm_network_inventory_sound_devices";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ["basic_id", "Name", "Manufacturer"];
}
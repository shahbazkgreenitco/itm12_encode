<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class Volume extends Model
{
    protected $table = "itm_network_inventory_volumes";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ["basic_id", "Name", "DriveType", "AvailableFreeSpace", "VolumeLabel", "DriveFormat", "TotalSize"];
}
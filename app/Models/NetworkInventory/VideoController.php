<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class VideoController extends Model
{
    protected $table = "itm_network_inventory_video_controllers";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ["basic_id", "Caption", "DriverDate", "DriverVersion", "VideoProcessor"];
}
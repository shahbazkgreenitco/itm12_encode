<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class ScanRegister extends Model
{
    protected $table = "itm_network_scan_register";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
}
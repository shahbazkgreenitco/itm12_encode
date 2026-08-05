<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class ScanRange extends Model
{
    protected $table = "itm_scan_ranges";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
}
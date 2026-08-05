<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;
use DB;

class ChangeCache extends Model
{
    protected $table = "itm_network_change_cache";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
}
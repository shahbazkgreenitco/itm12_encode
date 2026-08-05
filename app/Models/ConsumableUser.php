<?php

namespace App\Models;

use App\Models\Base;
use DB;

class ConsumableUser extends Base {
    protected $table = "consumables_users";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
    // protected $fillable = ["user_id", "accessory_id", "assigned_to", "device_id"];
}

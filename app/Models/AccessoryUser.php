<?php

namespace App\Models;

use App\Models\Base;
use DB;

class AccessoryUser extends Base {
    protected $table = "accessories_users";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
    // protected $fillable = ["user_id", "accessory_id", "assigned_to", "device_id"];

    public function accessory()
    {
        return $this->belongsTo(Accessory::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessoryCache extends Model
{
    protected $table = "accessory_caches";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
}

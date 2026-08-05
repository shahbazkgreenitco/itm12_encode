<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessoryRecord extends Model
{
    protected $table = "accessory_records";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
}

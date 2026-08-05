<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceItemDispose extends Model
{
    protected $table = 'device_item_dispose';
    protected $fillable = ['device_dispose_id', 'asset_id', 'item_id', 'item_user_id', 'item_name', 'asset_type', 'is_dispose'];
}
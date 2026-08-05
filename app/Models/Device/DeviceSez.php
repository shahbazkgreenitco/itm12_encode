<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSez extends Model
{
    use HasFactory;
    public const tableName = 'device_sez';
    protected $table = self::tableName;
    protected $guarded = [];
}

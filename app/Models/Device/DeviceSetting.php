<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSetting extends Model
{
    use HasFactory;
    protected $table = "device_settings";
    protected $guarded = [];

    public static function getDeviceSettings() {
        return DeviceSetting::find(1);
    }
}

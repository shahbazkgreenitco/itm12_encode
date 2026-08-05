<?php

namespace App\Models;

use App\Models\Base;

class DeviceLog extends Base
{
    protected $table = "asset_logs";
    
    public function getDevice() {
        return $this->belongsTo('App/Models/Device', 'asset_id');
    }
}

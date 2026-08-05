<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceMaintenance extends Base
{
    use SoftDeletes;
    protected $table = "asset_maintenances";
    protected $fillable = ["asset_id", "supplier_id", "asset_maintenance_type", "title", "is_warranty", "start_date", "completion_date", "asset_maintenance_time", "notes", "currency_format", "cost" ,"is_amc"];
    public $maintenance_types = ["Maintenance", "Repair", "Upgrade"];
    
    public function device() {
        return $this->belongsTo('App\Models\Device', 'asset_id')->withTrashed()->withDefault();
    }
    
}

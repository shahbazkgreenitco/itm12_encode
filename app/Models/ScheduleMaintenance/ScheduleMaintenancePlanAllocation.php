<?php

namespace App\Models\ScheduleMaintenance;

use App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ScheduleMaintenancePlanAllocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_id', 'model_id', 'device_id', 'plan_id', 'supplier_id', 'handler_id', 'comment', 'note', 'status'];

    public function plan(){
        return $this->belongsTo('App\Models\ScheduleMaintenance\ScheduleMaintenanceList', 'plan_id');
    }

    public function category(){
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}

<?php

namespace App\Models\ScheduleMaintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'order_no', 'description', 'notes', 'gps_required', 'proof_required', 'downtime', 'mandatory', 'created_by', 'updated_by', 'downtime_duration', 'plan_id']; 

    public function plans(){
        return $this->belongsTo('App\Models\ScheduleMaintenance\ScheduleMaintenanceList', 'plan_id');
    }

}

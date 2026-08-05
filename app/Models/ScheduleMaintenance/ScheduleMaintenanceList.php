<?php

namespace App\Models\ScheduleMaintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleMaintenanceList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['schedule_maintenance_name', 'recursion_plan', 'description', 'duration', 'closing_recursion_time_days', 'manager_id', 'approval_required', 'projected_cost', 'currency_format'];

    public static function recursion_plan() {
        return [
            ['id' => '1', 'text' => 'OneTime'],
            ['id' => '2', 'text' => 'Daily'],
            ['id' => '3', 'text' => 'Weekly'],
            ['id' => '4', 'text' => 'Monthly'],
            ['id' => '5', 'text' => 'Yearly']
        ];
    }

    public function incharge(){
        return $this->belongsTo('App\Models\User', 'manager_id', 'id');
    }

    public function createdby(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function task() {
        return $this->hasMany('App\Models\ScheduleMaintenance\MaintenanceTask', 'plan_id');
    }
}

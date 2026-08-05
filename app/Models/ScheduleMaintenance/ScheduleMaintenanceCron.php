<?php

namespace App\Models\ScheduleMaintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ScheduleMaintenance\ScheduleMaintenanceTask;
use App\Models\ScheduleMaintenance\PlanReferenceGuideAttachment;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleMaintenanceCron extends Model
{
    use HasFactory;
    
    protected $table = 'schedule_maintenance';

    protected $fillable = ['allocated_id','plan_id','category_id','device_id','model_id','supplier_id','handler_id','schedule_date','closing_schedule_date','task_step','remark','approver_comment','status','allocation_gr_id','location_id','internal_place_id'];

    /**
     * Get the plan that owns the ScheduleMaintenanceCron
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('App\Models\ScheduleMaintenance\ScheduleMaintenanceList', 'plan_id', 'id')->withTrashed();
    }

    /**
     * Get all of the tasks for the ScheduleMaintenanceCron
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany('App\Models\ScheduleMaintenance\ScheduleMaintenanceTask', 'plan_id', 'id');
    }

    public function category(){
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function model(){
        return $this->belongsTo('App\Models\Model', 'model_id', 'id');
    }

    public function device(){
        return $this->belongsTo('App\Models\Device', 'device_id', 'id');
    }

    public function location(){
        return $this->belongsTo('App\Models\Location', 'location_id', 'id');
    }
    
    public function place(){
        return $this->belongsTo('App\Models\Device', 'internal_place_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id');
    }

    public function handler()
    {
        return $this->belongsTo('App\Models\User', 'handler_id', 'id');
    }

    public function statuses()
    {
        return $this->belongsTo('App\Models\ScheduleMaintenance\ScheduleMaintenanceStatus', 'status', 'id');
    }

    public static function calculateTaskPercentage($id) {
        $tasks = ScheduleMaintenanceTask::where('plan_id', $id)->whereNull('deleted_at')->get();
        $task['per'] = $task['pending'] = $task['completed'] = 0;
        if ($tasks->count()) {
            $task['per'] = round(($tasks->where('status', 1)->count() / $tasks->count())*100, 2);
            $task['completed'] = $tasks->where('status', 1)->count();
            $task['total'] = $tasks->count();
            $task['pending'] = $task['total'] - $task['completed'];
        }
        return $task;
    }

    public function planAttachment($id) {
        return PlanReferenceGuideAttachment::where('plan_id', $id)->first();
    }
}

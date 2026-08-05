<?php

namespace App\Models\ScheduleMaintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ScheduleMaintenanceTask extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * Get the allocation of plan that owns the ScheduleMaintenanceTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    /**
     * Get the task that owns the ScheduleMaintenanceTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo('App\Models\ScheduleMaintenance\MaintenanceTask', 'task_id', 'id');
    }

    /**
     * Get the plan that owns the ScheduleMaintenanceTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('App\Models\ScheduleMaintenance\ScheduleMaintenanceCron', 'plan_id', 'id');
    }

    /**
     * Get the attachments associated with the ScheduleMaintenanceTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attachments()
    {
        return $this->hasOne('App\Models\ScheduleMaintenance\TaskAttachment', 'task_id', 'id');
    }
}

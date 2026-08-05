<?php

namespace App\Models\ScheduleMaintenance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAutoAllocationGroup extends Model
{
    use SoftDeletes;

    protected $table = 'task_auto_allocation_groups';

    protected $primaryKey = 'id';

    protected $fillable = [ 'name', 'desc', 'created_by', 'enabled', 'location_approval_required', 'location_approval_required_for'];

    protected $casts = [
        'enabled' => 'boolean',
        'location_approval_required' => 'integer',
        'location_approval_required_for' => 'integer',
    ];
    public function groupMember()
    {
        return $this->hasMany(TaskAutoAllocationGroupMember::class, 'group_id');
    }
}
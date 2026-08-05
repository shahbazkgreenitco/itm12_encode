<?php

namespace App\Models\ScheduleMaintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAutoAllocationGroupMember extends Model
{
    use HasFactory;
    protected $table = 'task_auto_allocation_group_members';

    protected $fillable = [
        'group_id',
        'user_id',
        'assign_in_flow',
        'location_id',
        'internal_place_id',
    ];

    public function group()
    {
        return $this->belongsTo(TaskAutoAllocationGroup::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
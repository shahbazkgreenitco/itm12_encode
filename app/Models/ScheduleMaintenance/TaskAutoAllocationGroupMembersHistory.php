<?php

namespace App\Models\ScheduleMaintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAutoAllocationGroupMembersHistory extends Model
{
    use HasFactory;
    protected $table = 'task_auto_allocation_group_members_history';

    protected $fillable = ['group_id','user_id','assign_in_flow','remark','created_by','updated_by','deleted_by'];

    public $timestamps = true;
}
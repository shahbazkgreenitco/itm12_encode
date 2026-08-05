<?php

namespace App\Models\ScheduleMaintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAutoAllocationGroupsHistory extends Model
{
    use HasFactory;
    protected $table = 'task_auto_allocation_groups_history';

    protected $fillable = ['auto_allocation_id','name','desc','enabled','remark','location_approval_required','location_approval_required_for','created_by','updated_by','deleted_by'];

    public $timestamps = true;

}
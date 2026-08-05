<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewScheduledTaskMaintenance extends Model
{
    use SoftDeletes;
    protected $table = "tkt_scheduled_maintenance_task_list";
    protected $guarded = [];
}

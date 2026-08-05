<?php

namespace App\Models;

use App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class ScheduledTaskUpdate extends Base
{
    protected $table = "scheduled_task_update";
    protected $guarded = [];
}
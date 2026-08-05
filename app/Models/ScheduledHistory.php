<?php

namespace App\Models;

use App\Models\Base;
use DB;

class ScheduledHistory extends Base
{
    protected $table = "scheduled_maintenance_history";
    protected $guarded = [];
}
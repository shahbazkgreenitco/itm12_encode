<?php

namespace App\Models;

use App\Models\Base;
use DB;

class ScheduledDevice extends Base
{
    protected $table = "scheduled_device";
    public $guarded = [];
}
<?php

namespace App\Models;

use App\Models\Base;
use App\Models\Settings;
use Auth;
use DB;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class ScheduleDevice extends Model
{
    protected $table = "scheduled_device";
    protected $guarded = [];
    
}

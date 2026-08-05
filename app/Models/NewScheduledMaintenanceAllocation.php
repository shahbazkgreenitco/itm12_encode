<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class NewScheduledMaintenanceAllocation extends Model
{
    use SoftDeletes;
    protected $table = "tkt_scheduled_maintenance";
    protected $guarded = [];

}

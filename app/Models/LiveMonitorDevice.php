<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiveMonitorDevice extends Model
{
    use SoftDeletes;
    protected $table = 'live_monitor_devices';
    protected $guarded = [];

}
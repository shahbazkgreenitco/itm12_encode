<?php

namespace App\Models\LiveMonitor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveMonitorWebsiteIncident extends Model
{
    use HasFactory;
    protected $table = 'live_monitor_website_incidents';
    protected $guarded = [];
}

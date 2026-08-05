<?php

namespace App\Models\LiveMonitor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveMonitorWebsiteHistory extends Model
{
    use HasFactory;
    protected $table = 'live_monitor_website_histories';
    protected $guarded = [];
}

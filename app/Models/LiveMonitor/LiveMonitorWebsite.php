<?php

namespace App\Models\LiveMonitor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveMonitorWebsite extends Model
{
    use HasFactory;
    protected $table = 'live_monitor_websites';
    protected $guarded = [];

    public function liveMonitorWebsiteMembers() {
        return $this->hasMany(LiveMonitorWebsiteUser::class,'website_id');
    }
}

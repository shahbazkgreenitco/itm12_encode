<?php

namespace App\Models\NetworkInventory\SWTracking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SWTrackingLog extends Model
{
    use HasFactory;
    protected $table = 'sw_tracking_logs';
    protected $guarded = [];
}

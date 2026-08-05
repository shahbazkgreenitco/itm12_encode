<?php

namespace App\Models\NetworkInventory\SWTracking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SWTrackingDataProcess extends Model
{
    use HasFactory;
    protected $table = 'sw_tracking_data_processed';
    protected $guarded = [];
}

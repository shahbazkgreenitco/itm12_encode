<?php

namespace App\Models;

use App\Models\Base;
use App\Models\Settings;
use Auth;
use DB;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class TransferDeviceStatus extends Model
{
    protected $table = "transfer_device_status_labels";
    protected $guarded = [];
    
}

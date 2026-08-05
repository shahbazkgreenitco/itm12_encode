<?php

namespace App\Models;

use App\Models\Base;
use App\Models\Settings;
use Auth;
use DB;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class TransferItem extends Model
{
    protected $table = "transfer_items";
    protected $guarded = [];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class, 'transfer_id');
    }
    public function transferStatus() {
        return $this->belongsTo('App\Models\TransferDeviceStatus', 'transfer_status');
    }
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

}

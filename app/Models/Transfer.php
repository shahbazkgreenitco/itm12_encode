<?php

namespace App\Models;

use App\Models\Base;
use App\Models\Settings;
use Auth;
use DB;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = "transfers";
    protected $guarded = [];

    public function fromlocation() {
        return $this->belongsTo( 'App\Models\Location', 'transfer_from');
    }

    public function tolocation() {
        return $this->belongsTo( 'App\Models\Location', 'transfer_to');
    }

    public function toPlace() {
        return $this->belongsTo( 'App\Models\Place', 'internal_place');
    }

    public function dispatchedBy() {
        return $this->belongsTo( 'App\Models\User', 'created_by');
    }

    public function responsibleUserBy() {
        return $this->belongsTo( 'App\Models\User', 'responsible_user');
    }

    public function canceledBy() {
        return $this->belongsTo( 'App\Models\User', 'canceled_by');
    }

    public function receivedBy() {
        return $this->belongsTo( 'App\Models\User', 'received_by');
    }

    public function transferStatus() {
        return $this->belongsTo( 'App\Models\TransferDeviceStatus', 'status');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceBillingCheckinCheckout extends Model
{
    // public $timestamps = false;
    protected $table = 'checkin_checkout_logs';
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

   
}

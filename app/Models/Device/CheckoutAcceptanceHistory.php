<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutAcceptanceHistory extends Model
{
    use HasFactory;
    protected $table="checkout_acceptance_histories";
    protected $guarded=[];
}

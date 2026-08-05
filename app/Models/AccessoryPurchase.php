<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoryPurchase extends Model
{
    use HasFactory;
    protected $table = "accessory_purchases";
    protected $guarded = [];
}
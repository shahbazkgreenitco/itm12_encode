<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumablePurchase extends Model
{
    use HasFactory;
    protected $table = "consumable_purchases";
    protected $guarded = [];
}
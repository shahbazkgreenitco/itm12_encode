<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicensePurchase extends Model
{
    use HasFactory;
    protected $table = "license_purchases";
    protected $guarded = [];
}
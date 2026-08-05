<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingUnit extends Model
{
    protected $table = 'billing_unit';
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
}

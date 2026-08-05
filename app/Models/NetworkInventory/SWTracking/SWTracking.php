<?php

namespace App\Models\NetworkInventory\SWTracking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SWTracking extends Model
{
    use HasFactory;
    protected $table = 'sw_tracking';
    protected $guarded = [];
}

<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class RegPvtEnterprise extends Model
{
    protected $table = "iana_reg_pvt_enterprises";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlacklistedSoftwareStatus extends Model
{
    use HasFactory;

    protected $fillable = ['product_blacklisted_id','basic_id','change_log_id', 'license_status', 'caption'];
}

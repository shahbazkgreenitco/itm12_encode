<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseCache extends Model
{
    protected $table = 'license_cache';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = [];
}

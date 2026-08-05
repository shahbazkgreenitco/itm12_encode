<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseRecord extends Model
{
    protected $table = 'license_record';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = [];
}

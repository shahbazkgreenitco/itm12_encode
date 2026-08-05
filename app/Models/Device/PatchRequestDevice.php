<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatchRequestDevice extends Model
{
    use HasFactory;
    protected $table = "patch_request_devices";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

}

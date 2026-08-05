<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatchManagementGroupDevice extends Model
{
    use HasFactory;
    protected $table = "patch_management_group_devices";
    protected $dateFormat = "Y-m-d H:i:s";
    public $timestamps = true;
    protected $guarded = [];

}   

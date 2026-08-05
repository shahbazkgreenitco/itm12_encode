<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatchManagementGroup extends Model
{
    use HasFactory;
    protected $table = "patch_management_groups";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

}

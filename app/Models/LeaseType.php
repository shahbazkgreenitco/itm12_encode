<?php 

namespace App\Models;

use App\Models\Base;
use App\Models\Settings;
use App\Models\Device;
use Auth;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaseType extends Base {
    protected $table = "lease_type";
    protected $guarded = [];
    use SoftDeletes;
}
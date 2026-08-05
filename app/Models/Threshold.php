<?php 

namespace App\Models;

use App\Models\Base;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Threshold extends Base {
    protected $table = "thresholds";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ['cat_id'];
    // protected $dates = ['deleted_at'];
    // use SoftDeletes;



    
}
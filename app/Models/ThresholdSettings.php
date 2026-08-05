<?php 

namespace App\Models;

use App\Models\Base;
// use Illuminate\Database\Eloquent\SoftDeletes;

class ThresholdSettings extends Base {
    protected $table = "threshold_settings";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ['cat_id'];
    protected $guarded = array('id');
    // protected $dates = ['deleted_at'];
    // use SoftDeletes;



    
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class Blacklisted extends Model
{
    protected $table = "product_blacklisted";
    protected $fillable = ['product_id', 'Version', 'basic_id', 'created_by', 'product', 'publisher', 'blacklist_type', 'manual_sw_type','manual_software'];

    public function product() {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

}

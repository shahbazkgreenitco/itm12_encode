<?php

namespace App\Models;

use App\Models\Base;
use App\Models\Country;
use DB;
use StdClass;
use Illuminate\Database\Eloquent\SoftDeletes;

class LicenseSeat extends Base
{
    use SoftDeletes;
    protected $table = 'license_seats';
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    // protected $fillable = ['user_id', 'assigned_to', 'notes', 'serial', 'expected_checkin'];

    public function user()
    {
        return $this->belongsTo('App\Models\User','assigned_to')
        ->withTrashed()->select(['id','first_name','last_name'])
        ->withDefault(['id' => '','first_name'=>'','last_name'=>'']);
    }

    public function asset()
    {
        return $this->belongsTo('App\Models\Device','asset_id')->withTrashed()
        ->select(['id','asset_tag'])
        ->withDefault(['id' => '','asset_tag'=>'']);
    }

    public function license()
    {
        return $this->belongsTo(License::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class NewScheduledMaintenance extends Model
{
    use SoftDeletes;
    protected $table = "tkt_scheduled_maintenance_plan";
    protected $guarded = [];

    public static function recursive_plan() {
        return [
            ['id' => '1', 'text' => 'Daily'],
            ['id' => '2', 'text' => 'Weekly'],
            ['id' => '3', 'text' => 'Monthly'],
            ['id' => '4', 'text' => 'Quarterly'],
            ['id' => '5', 'text' => 'Yearly']
        ];
    }

    public function incharge(){
        return $this->belongsTo('App\Models\User', 'incharge_id');
    }

    public function device(){
        return $this->belongsTo('App\Models\Device', 'device_id');
    }
}

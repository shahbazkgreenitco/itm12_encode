<?php

namespace App\Models;

use App\Models\Base;
use DB;

class ScheduledMaintenance extends Base
{
    protected $table = "scheduled_maintenance_plan";
    protected $guarded = [];

    public static function recursive_plan() {
        return [
            ['id' => '1', 'text' => 'Daily'],
            ['id' => '2', 'text' => 'Monthly'],
            ['id' => '3', 'text' => 'Yearly']
        ];
    }

    public function incharge(){
        return $this->belongsTo('App\Models\User', 'incharge_id');
    }

    public function device(){
        return $this->belongsTo('App\Models\Device', 'device_id');
    }
  }
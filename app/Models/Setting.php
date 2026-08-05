<?php

namespace App\Models;

use App\Models\Base;

class Setting extends Base
{
    protected $table = "settings";
    protected $guarded = 'id';
    
    public static function getSettings() {
    	static $cache = NULL;
    	if (!$cache) {
    		$cache = Setting::find(1);
    	}
    	return $cache;
	}
	
	public function blockDeviceAddProcess() {
		$this->ni_add_device_flag = 1;
		$this->save();
	}

	public function unblockDeviceAddProcess() {
		$this->ni_add_device_flag = null;
		$this->save();
	}
}

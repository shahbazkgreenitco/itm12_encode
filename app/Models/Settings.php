<?php

namespace App\Models;

use App\Models\Base;
use Log;
use App\Helpers\Common as CommonHelper;

class Settings extends Base
{
    protected $table = "settings";
    protected $guarded = [];
    
    // public static function getSettings() {
    // 	// static $cache = NULL;
    // 	// if (!$cache) {
	// 		return Settings::find(1);
    // 		// $cache = Settings::find(1);
    // 	// }
    // 	// return $cache;
	// }
	public static function getSettings($companyId = 1)
	{
		return self::where('company_id', $companyId)->first()?? self::where('company_id', 1)->first();
	}
	
	public function blockDeviceAddProcess() {
		$this->ni_add_device_flag = 1;
		$this->save();
	}

	public function unblockDeviceAddProcess() {
		$this->ni_add_device_flag = null;
		$this->save();
	}

	public function isHideDeviceViaLicenses() {
		/* 1 - yes, 2 - no */
		return $this->hideDeviceViaLicenses == 1 ? true : false;
	}

	public function getDellWarrantyToken() {
		try {		
			if($this->dell_warranty_token && $this->dell_warranty_token_expire && $this->dell_warranty_token_expire > now()) {
				return $this->dell_warranty_token;
			}

			$headers = array(
				'Accept: application/json',
				'Content-Type: application/x-www-form-urlencoded'
			);

			$data = ['grant_type' => 'client_credentials', 'client_id' => 'l76cc8650e15644cb6ab6106b4424b1355', 'client_secret' => '43d30f16442142178e22be442999ec5a'];
			$url = 'https://apigtwb2c.us.dell.com/auth/oauth/v2/token';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			$result = curl_exec($ch);
			if(curl_errno($ch) || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
				return [curl_errno($ch)];
			}
			curl_close($ch);

			$return_val = json_decode($result);
			if(!$result || !$return_val) {
				return false;
			}

			if($return_val->access_token && $return_val->expires_in) {
				$this->dell_warranty_token = $return_val->access_token;
				$this->dell_warranty_token_expire = date('Y-m-d H:i:s', time() + $return_val->expires_in);
				$this->save();
			}

			return $this->dell_warranty_token;
		}
		catch(\Exception $e) {
			Log::error($e->getMessage());
			return false;
		}
	}


}

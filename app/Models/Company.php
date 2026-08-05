<?php 

namespace App\Models;

use App\Models\Base;
use App\Models\Settings;
use App\Models\Device;
use Auth;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Base {
    protected $table = "companies";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'windows_agent_path', 'windows_agent_request'];
    use SoftDeletes;

    public static function getAllCompany() {
        return self::select('id', DB::raw('lower(name) as name'))->get();
    }

    public function accessories()
    {
        return $this->hasMany('App\Models\Accessory', 'company_id');
    }

    public function assets()
    {
        return $this->hasMany('App\Models\Device', 'company_id');
    }

    public function consumable()
    {
        return $this->hasMany('App\Models\Consumable', 'company_id');
    }

    public function components()
    {
        return $this->hasMany('App\Models\Component', 'company_id');
    }

    public function department()
    {
        return $this->hasMany('App\Models\Department', 'company_id');
    }

    public function leaseagreement()
    {
        return $this->hasMany('App\Models\Lease', 'company_id');
    }

    public function license()
    {
        return $this->hasMany('App\Models\License', 'company_id');
    }

    public function place()
    {
        return $this->hasMany('App\Models\Place', 'company_id');
    }

    public function purchase()
    {
        return $this->hasMany('App\Models\Purchase', 'company_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User', 'company_id');
    }

    public function hostvault()
    {
        return $this->hasMany('App\Models\NetworkInventory\HostVault', 'company_id');
    }

    public function agentdata()
    {
        return $this->hasMany('App\Models\NetworkInventory\AgentData', 'company_id');
    }

    public function itmbasic()
    {
        return $this->hasMany('App\Models\NetworkInventory\Basic', 'company');
    }

    public function scanregister()
    {
        return $this->hasMany('App\Models\NetworkInventory\ScanRegister', 'company_id');
    }

    public function interact_cache() {
      
        return $this->hasMany('App\Models\InteractCache', 'company_id');
    }

    public function interact_records() {
      
        return $this->hasMany('App\Models\InteractRecord', 'company_id');
    }

    public static function checkUserAccess($obj)
    {
        if(!is_object($obj)) {
            return false;
        }
        if(Settings::first()->full_multiple_companies_support) {
            return true;
        }
        return Auth::user()->company_id == $obj->company_id;
    }
}
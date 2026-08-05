<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use StdClass;

class Lease extends Base
{
    protected $table = 'lease_agreements';
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
    protected $lease_types = [["id"=>"1", "text"=>"Operating Contract"], ["id"=>"2", "text"=>"Finance Contract"]];
    protected $maintenance_incharges = [["id"=>"1", "text"=>"By Company"], ["id"=>"2", "text"=>"By Contractor"]];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function Leaser()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id');
    }

    public function assets()
    {
        return $this->hasMany('App\Models\Device', 'lease_id');
    }

    public function interact_caches()
    {
        return $this->hasMany('App\Models\InteractCache', 'lease_id');
    }

    public function interact_records()
    {
        return $this->hasMany('App\Models\InteractRecord', 'lease_id');
    }

    public function get_lease_types()
    {
        return $this->lease_types;
    }

    public function get_maintenance_incharges()
    {
        return $this->maintenance_incharges;
    }
}

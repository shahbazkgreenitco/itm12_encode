<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;
use App\Models\Settings;
use DB;
use App\Helpers\Common as CommonHelper;

class Accessory extends Base {

    use SoftDeletes;

    const BATCH_PREFIX = "A";
    
    protected $table = "accessories";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $dates = ["deleted_at"];
    protected $guarded = [];
    protected $assigned_for_options = [["id"=>1,"text"=>"User"],["id"=>2,"text"=>"Place"],["id"=>3,"text"=>"Device"]];

    public function batch_no() {
        if (config("app.client") == "etherealmachines") {
            return "AC". $this->id;
        } else {
            return self::BATCH_PREFIX . $this->id;
        }
    }

    public static function getAssignedForOptions() {
        return (new Accessory)->assigned_for_options;
    }
    
    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id')->withTrashed()->withDefault();
    }

    public function manufacturer() {
        return $this->belongsTo('App\Models\Manufacture', 'manufacturer_id');
    }

    public function purchaseReference() {
        return $this->belongsTo('App\Models\Purchase', 'invoice_id');
    }

    public function supplier() {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id');
    }

    public function category() {
        return $this->belongsTo('App\Models\Category', 'category_id')->withDefault();
    }
    
    public function location() {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    public function users() {
        return $this->belongsToMany('App\Models\User', "accessories_users", "accessory_id", "assigned_to")->wherePivot("assigned_for", "=", 1)->withPivot("id")->withTrashed()->withTimestamps();
    }

    public function usersCount() {
        return $this->users->count();
    }
    
    public function places() {
        return $this->belongsToMany('App\Models\Place', "accessories_users", "accessory_id", "assigned_to")->wherePivot("assigned_for", "=", 2)->withPivot("id")->withTimestamps();
    }

    public function placesCount() {
        return $this->places->count();
    }

    public function devices() {
        return $this->belongsToMany('App\Models\Device', "accessories_users", "accessory_id", "assigned_to")->wherePivot("assigned_for", "=", 3)->withPivot("id")->withTrashed()->withTimestamps();
    }
    
    public function devicesCount() {
        return $this->devices->count();
    }

    public function availableQty() {
        return $this->qty - ( $this->scrap_qty + $this->usersCount() + $this->devicesCount() + $this->placesCount());
    }

    public function requireAcceptance() {
        return $this->category ? $this->category->require_acceptance : false;
    }

    public function isNeedCheckinMail() {
        return $this->category ? $this->category->checkin_email : false;
    }

    public function getEula() {
		if ($this->category->eula_text) {
			return e($this->category->eula_text);
		} elseif (Settings::getSettings()->default_eula_text) {
			return e(Settings::getSettings()->default_eula_text);
		} else {
			return null;
		}
    }
    
    protected function getCheckoutAccessoriesTotalByCat($id)
    {
        return DB::select("select count(accusr.id) as total_checkouts from accessories as acc join categories as cat on cat.id = acc.category_id and cat.id={$id} and acc.deleted_at is null join accessories_users as accusr on accusr.accessory_id=acc.id");
    }
     
    protected function getCatAccessoriesTotal($cat_id)
    {
        return DB::select("select sum(acc.qty) as total_accessories from accessories as acc join categories as cat on cat.id = acc.category_id and cat.id={$cat_id} and acc.deleted_at is null");
    }

    protected function getCheckoutAccTotalById($id)
    {
       return DB::select("SELECT COUNT(id) as total_checkouts FROM accessories_users WHERE accessory_id = {$id}");
    }

    public function accessories_user()
    {
        return $this->hasMany('App\Models\AccessoryUser', 'accessory_id');
    }

    public function asset_log()
    {
        return $this->hasMany('App\Models\Actionlog', 'accessory_id');
    }

    public static function printerCategory() {
        $printerCategory = Category::where("name", "Printer")->where('category_type', "accessory")->first();
        return !empty($printerCategory) ? $printerCategory->id : null;
    }

    public function generateUniqueTag() {
        $catName = Category::where('id', $this->category_id)->value('name');
        $manName = Manufacture::where('id', $this->manufacturer_id)->value('name');

        $cat = !empty($catName) ? substr(preg_replace('/\s+/', '', $catName), 0, 3) : '';
        $man = !empty($manName) ? substr(preg_replace('/\s+/', '', $manName), 0, 3) : '';
        $name = $this->name ? substr(preg_replace('/\s+/', '', $this->name), 0, 3) : '';
        $parts = array_filter([$cat, $man, $name]);
        $uniquetag = implode('/', $parts) . '/' . $this->id;

        $this->unique_tag = $uniquetag;
        $this->save();
    }


    public function AccessorydataForCache()
    {
        $data = (array) $this->only("unique_tag", "name", "category_id", "user_id", "qty", "requestable", "location_id", "internal_place_id", "purchase_date", "purchase_cost", "purchase_currency", "order_number", "company_id", "manufacturer_id", "supplier_id", "invoice_id", "notes", "scrap_qty", "batch_no", "accessories_custom_fields", "department_id", "image", "accessory_thresholds", "thresholds_alerts", "requestable_accessory", "reorder_limits");
        return $data;
    }
}

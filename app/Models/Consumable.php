<?php

namespace App\Models;

use App\Models\Base;
use App\Models\Settings;
use App\Models\Category;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use StdClass;

class Consumable extends Base {

    protected $table = "consumables";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
    use SoftDeletes;
    protected $assigned_for_options = [["id"=>1,"text"=>"User"],["id"=>2,"text"=>"Place"],["id"=>3,"text"=>"Device"]];

    public static function getConsumableSettings() {
        return Consumable::find(1);
    }
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
    public function place()
    {
        return $this->belongsTo(Place::class, 'internal_place_id');
    }
    public function unit()
    {
        return $this->belongsTo('App\Models\Procurement\Unit', 'units');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    public function purchaseReference() {
        return $this->belongsTo('App\Models\Purchase', 'invoice_id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'consumables_users', 'consumable_id','assigned_to')->wherePivot('assigned_for', 1)->withPivot('id', 'assigned_for', 'asset_logs_id', 'ticket_id', 'user_id')->withTimestamps();
    }

    public function device() {
        return $this->belongsToMany('App\Models\Device', "consumables_users", "consumable_id", "assigned_to")->wherePivot("assigned_for", "=", 3)->withPivot("id")->withTimestamps();
    }

    public function places() {
        return $this->belongsToMany('App\Models\Place', "consumables_users", "consumable_id", "assigned_to")->wherePivot("assigned_for", "=", 2)->withPivot("id")->withTimestamps();
    
    }

     public function placesCount() {
        return $this->places->count();
    }

    public function assetlog()
    {
        return $this->hasMany('App\Models\Actionlog','consumable_id')->where('asset_type','=','consumable')->orderBy('created_at', 'desc')->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }

    public function manufacture()
    {
        return $this->belongsTo('App\Models\Manufacture', 'manufacturer_id')->withDefault();
    }

    public function consumablelogs()
    {
        return $this->hasMany('App\Models\Actionlog', 'consumable_id');
    }

    public function consumableusers()
    {
        return $this->hasMany('App\Models\ConsumableUser', 'consumable_id');
    }
    public function customFieldset()
    {
        return $this->belongsTo('App\Models\CustomFieldset', 'fieldset_id');
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

    public function showEula(){
        return $this->category ? $this->category->use_default_eula : null;
    }

    protected function getCheckoutConsumableTotalByCat($id)
    {
        return DB::select("select count(conusr.id) as total_checkouts from consumables as con 
        join categories as cat on cat.id = con.category_id and cat.id={$id} 
        and con.deleted_at is null join consumables_users as conusr on conusr.consumable_id=con.id");
    }

    protected function getCatConsumableTotal($cat_id)
    {
        // return DB::select("select sum(con.qty) as total_consumables from consumables as con
        //  join categories as cat on cat.id = con.category_id and cat.id={$cat_id} and con.deleted_at is null");
        return DB::select(
            "SELECT SUM(con.qty) AS total_consumables
            FROM consumables AS con
            JOIN categories AS cat ON cat.id = con.category_id
            WHERE cat.id = ?
            AND con.deleted_at IS NUll ",
            [$cat_id]
        );
    }

    protected function getCatConsumableTotalScrap($cat_id)
    {
        return DB::select(
            "SELECT SUM(con.scrap_qty) AS total_scrap_qty_consumables
            FROM consumables AS con
            JOIN categories AS cat ON cat.id = con.category_id
            WHERE cat.id = ?
            AND con.deleted_at IS NUll ",
            [$cat_id]
        );
    }

    protected function getCheckoutConsumableTotalById($id)
    {
       return DB::select("SELECT COUNT(id) as total_checkouts FROM consumables_users WHERE consumable_id = {$id}");
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
    
    public static function getAssignedForOptions() {
        return (new Consumable)->assigned_for_options;
    }

    public function customField() {
        return $this->hasOne(ConsumableCustomField::class, 'consumable_id');
    }
}

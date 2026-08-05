<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;
use App\Models\Settings;
use DB;

class Component extends Base {
    use SoftDeletes;
    protected $table = "components";
    protected $dateFormat = "Y-m-d H:i:s";
    public $statusOptions = [
        ['id'=>'0','text'=>'Repair'], ['id'=>'1','text'=>'Usable'], ['id'=>'2','text'=>'Lost'], ['id'=>'3','text'=>'Scrap'], ['id'=>'4','text'=>'Deployed']
    ];
    public $orginFromOptions = [
        ['id'=>'1','text'=>'Purchase'], ['id'=>'2','text'=>'Existing Device'], ['id'=>'3','text'=>'Others'], ['id'=>'4','text'=>'Via Network']
    ];

    protected $fillable = ["name", "unique_tag", "category_id", "serial", "manufacturer_id", "purchase_date", "purchase_cost", "purchase_currency", "order_number", "notes", "creator_id", "status", "supplier_id", "location_id", "last_checkout", "expected_checkin_at", "company_id", "invoice_id", "origin_from", "parent_device", "updator_id", "checked_out_at", "checked_out_to", "component_custom_fields", "department_id", "internal_place_id"];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id')->withTrashed()->withDefault();
    }

    public function department() {
        return $this->belongsTo('App\Models\Department', 'department_id')->withTrashed()->withDefault();
    }

    public function customFieldset() {
        return $this->belongsTo('App\Models\CustomFieldset', 'fieldset_id');
    }
    public function manufacturer() {
        return $this->belongsTo('App\Models\Manufacture', 'manufacturer_id');
    }
    public function status() {
        if($this->status == 1) 
        return 'Usable';
        elseif($this->status == 2) 
        return 'Lost';
        elseif($this->status == 0) 
        return 'Repair';
        elseif($this->status == 3) 
        return 'Scrap';
        elseif($this->status == 4)
        return 'Deployed';
    }
    public function origin_from() {
        if($this->origin_from == 1) 
        return 'Purchase';
        elseif($this->origin_from == 2) 
        return 'Existing Device';
        elseif($this->origin_from == 3) 
        return 'Other';
        elseif($this->origin_from == 4)
        return 'Via Network';
    }
    public function canCheckout() {
        if($this->isCheckedOut()) {
            return false;
        }
        return $this->status != null && $this->status == 1 && $this->deleted_at == null;
    }

    public function isCheckedOut() {
        return $this->checked_out_to != null && $this->checked_out_to > 0 && $this->deleted_at == null;
    }
    public function device() {
        return $this->belongsTo('App\Models\Device', 'checked_out_to');
    }

    public function parent() {
        return $this->belongsTo('App\Models\Device', 'parent_device');
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

    public function generateUniqueTag() {
        if (config("app.client") == "etherealmachines") {
            $this->unique_tag = "CM" . str_pad($this->id, 5, "0", STR_PAD_LEFT);
        } else {
            $this->unique_tag = "CMP" . str_pad($this->id, 5, "0", STR_PAD_LEFT);
        }
        $this->save();
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

    protected function getCheckoutComponentTotalByCat($id) {
        return DB::select("select count(comp.id) as total_checkouts from components as comp 
        join categories as cat on cat.id = comp.category_id and cat.id={$id} 
        and comp.checked_out_to is null");
    }

    protected function getCatComponentTotal($cat_id) {
        return DB::select("select count(comp.id) as total_component from components as comp
         join categories as cat on cat.id = comp.category_id and cat.id={$cat_id} ");
    }

    public function assignedUser() {
        return $this->belongsTo('App\Models\User', 'checked_out_to')->withTrashed()->withDefault(['first_name' =>'', 'last_name' => '', 'username' => '']);
    }

    public function isCheckedOutToUser() {
        return $this->checked_out_for == 1 && $this->checked_out_to;
    }

    public function isCheckedOutToDevice() {
        return $this->checked_out_for == 2 && $this->checked_out_to;
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'checkout_out_to');
    }
}
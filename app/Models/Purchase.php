<?php

namespace App\Models;

use App\Models\Base;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Base {
    use SoftDeletes;

    protected $table = "purchases";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ["invoice_date", "received_date", "invoice_no", "company_id", "supplier_id", "location_id", "po_number", "bill_amount", "notes", "history", "fully_received", "created_by", "updated_by","other_info","other_info1", "currency", "qty"];

    protected static function booted()
    {
       static::updating(function($model){
        $dirty = collect($model->getDirty())
                ->except('history')
                ->toArray();
            if (empty($dirty)) {
                return false;
            }
            $model->history = $model->history;
         });
    }

    public function getPurchases() {
        return DB::table($this->tblPurchase . " as s")
                        ->leftJoin($this->tblCountry . " as c", "c.country_code", "=", "s.country")
                        ->addSelect("s.*")
                        ->addSelect("c.name as country_name")
                        ->get();
    }

    public function getPurchaseById($id) {
        return DB::table($this->tblPurchase)->where("id", "=", $id)->limit(1)->get();
    }

    public function addPurchase($data) {
        $data["created_at"] = $data["updated_at"] = date('Y-m-d H:i:s');
        return DB::table($this->tblPurchase)->insert($data);
    }

    public function updatePurchaseById($data, $id) {
        $data["updated_at"] = date('Y-m-d H:i:s');
        return DB::table($this->tblPurchase)->where("id", $id)->update($data);
    }

    public function getPurchasesForOpts() {
        return DB::table($this->tblPurchase)
                        ->select(DB::raw('concat(name, " (", city, ")") as location_name'), "id")
                        ->orderBy('location_name', 'asc')
                        ->get();
    }

    public function addPurchaseAttachment($data) {
        // $data["created_at"] = $data["updated_at"] = date('Y-m-d H:i:s');
        return DB::table('purchase_docs')->insertGetId($data);
    }

    public function getSupplierAttachments($supplier_id) {
        return DB::table('purchase_docs')
        ->where("suplier_id", $supplier_id)
        ->get();
    }

    public static function selectpurchaseReference()
    {
        $db = DB::table('purchases as p');
        $db->select('p.id',DB::raw('concat_ws(" - ",p.invoice_no, date_format(p.invoice_date, "%d/%m/%Y")) as text'));
        $db->whereNull("p.deleted_at");
        $db->orderBy('text');
        return $db->get();
    }

    public function getAttachmentDetail($id) {
        // $data["created_at"] = $data["updated_at"] = date('Y-m-d H:i:s');
        return DB::table('purchase_docs')->where("file_name", "=", $id)->get();
    }

    public function deleteAttachmentDetail($id) {
        // $data["created_at"] = $data["updated_at"] = date('Y-m-d H:i:s');
        return DB::table('purchase_docs')->where("file_name", "=", $id)->delete();
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\PurchaseAttachment', 'po_id');
    }

    public function assets()
    {
        return $this->hasMany('App\Models\Device', 'invoice_id');
    }

    public function accessories()
    {
        return $this->hasMany('App\Models\Accessory', 'invoice_id');
    }

    public function consumables()
    {
        return $this->hasMany('App\Models\Consumable', 'invoice_id');
    }

    public function license()
    {
        return $this->hasMany('App\Models\License', 'invoice_id');
    }

    public function components()
    {
        return $this->hasMany('App\Models\Component', 'invoice_id');
    }

    public function interact_caches()
    {
        return $this->hasMany('App\Models\InteractCache', 'invoice_id');
    }

    public function interact_records()
    {
        return $this->hasMany('App\Models\InteractRecord', 'invoice_id');
    }

    public function pu_attach()
    {
        return $this->hasMany('App\Models\PurchaseAttachment', 'po_id');
    }
    public function supplier() {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id');
    }
    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
    public function currency() {
        return $this->belongsTo('App\Models\Currency', 'currency');
    }

    public function location() {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }
}

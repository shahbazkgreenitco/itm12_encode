<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetExpense extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = ["asset_id", "supplier_id", "asset_maintenance_type", "title", "is_warranty", "start_date", "completion_date", "asset_maintenance_time", "notes", "currency_format", "cost" , "is_amc", "category_id", "model_id", "device_id", "temp_id"];

        public $maintenance_types = [
            ['id'=>'1','text'=>'Maintenance'], ['id'=>'2','text'=>'Repair'], ['id'=>'3','text'=>'Upgrade'], ['id'=>'4','text'=>'Miscellaneous'], ['id'=>'5','text'=>'Audit']
        ];

        public function device() {
            return $this->belongsTo('App\Models\Device', 'asset_id')->withTrashed()->withDefault();
        }
}

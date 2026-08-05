<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDispose extends Model
{
    use HasFactory;

    protected $table = 'asset_dispose'; 

    protected $fillable = ['asset_id', 'dispose_type', 'dispose_at', 'dispose_by', 'dispose_price', 'vendor_org_name', 'ref_no', 'note', 'updated_by', 'is_dispose', 'serial', 'asset_tag', 'model_id', 'currency_format'];

    public function asset()
    {
        return $this->belongsTo(Device::class, 'asset_id');
    }

    public function disposedBy()
    {
        return $this->belongsTo(User::class, 'dispose_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function vendorName()
    {
        return $this->belongsTo(Supplier::class, 'vendor_org_name');
    }
}
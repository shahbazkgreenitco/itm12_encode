<?php

namespace App\Models;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetAllocationType extends Model
{
    use SoftDeletes;
    protected $table = "itm_asset_allocation_type";
    protected $fillable = ['name', 'created_by'];
    protected $primaryKey = "id";
    protected $dateFormat = "Y-m-d H:i:s";

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}


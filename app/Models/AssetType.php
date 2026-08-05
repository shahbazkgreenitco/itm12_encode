<?php

namespace App\Models;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetType extends Model
{
    use SoftDeletes;
    protected $table = "assets_types";
    protected $primaryKey = "id";
    protected $dateFormat = "Y-m-d H:i:s";

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}


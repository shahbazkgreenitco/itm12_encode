<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceRfid extends Model
{
    use HasFactory, SoftDeletes;
    public const tableName = 'device_rfids';
    protected $table = self::tableName;
    protected $guarded = [];
}

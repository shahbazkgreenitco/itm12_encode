<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class DeviceMovement extends Model
{
    use HasFactory;
    public const tableName = 'device_movements';
    protected $table = self::tableName;
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }
}

<?php

namespace App\Models;
use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkDevice extends Model
{
    use HasFactory;

    protected $table = "itm_network_inventory_basic_azure";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d M Y H:i A');
    }

    public function asset() {
        return $this->belongsTo('App\Models\Device', 'device_id');
    }
}

<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsbPort extends Model
{
    use HasFactory;
    protected $table = "itm_network_inventory_usb_ports";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ['DeviceName', 'Description', 'DeviceID', 'basic_id'];
}

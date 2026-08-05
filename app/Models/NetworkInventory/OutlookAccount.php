<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutlookAccount extends Model
{
    use HasFactory;
    protected $table = "itm_network_inventory_outlook_accounts";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ['Name', 'Size', 'basic_id'];
}

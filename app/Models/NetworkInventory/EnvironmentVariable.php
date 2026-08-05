<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvironmentVariable extends Model
{
    use HasFactory;
    protected $table = "itm_network_inventory_env_variables";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ['basic_id', 'Name', 'Value'];
}

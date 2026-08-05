<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    protected $table = "itm_network_user_accounts";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
}
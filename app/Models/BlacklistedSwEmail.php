<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlacklistedSwEmail extends Model
{
    use HasFactory;
    protected $table = "blacklisted_sw_email";

}

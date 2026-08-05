<?php

namespace App\Models\LiveMonitor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItmWebsiteAlert extends Model
{
    use HasFactory;
    protected $table = 'itm_website_alerts';
    protected $guarded = [];
}

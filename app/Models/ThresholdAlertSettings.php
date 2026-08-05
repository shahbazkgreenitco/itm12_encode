<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThresholdAlertSettings extends Model
{
    protected $table = 'threshold_alert_settings';
    protected $fillable = ['asset_id', 'asset_type', 'user_id'];
}
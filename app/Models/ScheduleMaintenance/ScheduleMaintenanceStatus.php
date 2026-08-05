<?php

namespace App\Models\ScheduleMaintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleMaintenanceStatus extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'is_enabled', 'color'];
}

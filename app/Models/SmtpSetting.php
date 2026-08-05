<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $table = 'smtp_settings';

    protected $guarded = [];

    protected $casts = [
        'mail_service_enabled' => 'boolean',
        'mail_port' => 'integer',
        'company_id' => 'integer',
    ];
}
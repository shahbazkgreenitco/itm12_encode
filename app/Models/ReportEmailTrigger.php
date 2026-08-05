<?php

namespace App\Models\Report;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportEmailTrigger extends Model
{
    use HasFactory;
    protected $table = "email_report_triggers";
    protected $guarded = [];
}

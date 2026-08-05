<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class AgentErrorLog extends Model
{
    protected $table = "itm_agent_error_logs";
    public $timestamps = false;
    protected $guarded = [];
}
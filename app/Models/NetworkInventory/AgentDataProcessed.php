<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class AgentDataProcessed extends Model
{
    protected $table = "itm_network_agent_data_processed";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    public function isProtocolSnmp() {
        return $this->protocol == "snmp";
    }

    public function isProtocolTelnet() {
        return $this->protocol == "telnet";
    }
}
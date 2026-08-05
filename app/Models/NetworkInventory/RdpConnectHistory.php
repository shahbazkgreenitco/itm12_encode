<?php

namespace App\Models\NetworkInventory;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;


class RdpConnectHistory extends Model
{
    protected $table = "itm_rdp_connect_history";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

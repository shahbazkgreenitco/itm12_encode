<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GatePass;

class GatePassItem extends Model
{
    use HasFactory;
    protected $table = 'gate_pass_items';

    public function gatePass()
    {
        return $this->belongsTo(GatePass::class, 'gate_pass_id', 'id');
    }
}
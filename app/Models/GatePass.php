<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GatePassItem;


class GatePass extends Model
{
    use HasFactory;
    protected $table = 'gate_passes';

    public function items()
    {
        return $this->hasMany(GatePassItem::class, 'gate_pass_id', 'id');
    }

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferHistory extends Model
{
    protected $table = 'transfer_history';
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];
}

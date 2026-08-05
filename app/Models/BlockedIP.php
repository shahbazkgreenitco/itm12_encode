<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockedIP extends Model
{
    use SoftDeletes;
    protected $table = "blocked_ip";
    protected $fillable = ['type','ip','mac','created_by','updated_by','deleted_by'];

}

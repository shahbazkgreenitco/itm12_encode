<?php

namespace App\Models\Visitor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitor extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "visitors";
        protected $fillable = [
            'name',
            'email',
            'phone',
            'company',
            'looking_for',
        ];
}

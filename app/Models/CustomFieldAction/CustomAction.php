<?php

namespace App\Models\CustomFieldAction;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CustomAction extends Model {

    use HasFactory, SoftDeletes;
    protected $guarded = [];
 

}

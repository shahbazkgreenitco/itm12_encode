<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTaxElement extends Model
{
    use HasFactory;
    protected $table = "purchase_tax_element";
    protected $guarded = [];
}
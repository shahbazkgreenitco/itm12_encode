<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseAttachment extends Model
{
    use SoftDeletes;
    protected $table = "purchase_docs";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ['po_id','doc_link','original_file_name','file_name','extension','doc_type'];


}

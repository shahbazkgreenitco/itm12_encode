<?php

namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\SoftDeletes;
class ConsumableCustomField extends Base
{
    use SoftDeletes;
    protected $table = 'consumable_custom_field';

    protected $guarded = ['id'];

    public function consumable(){
        return $this->belongsTo(Consumable::class);
    }
}

<?php

namespace App\Models\RFID;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedRfidTag extends Model
{
    use HasFactory;

    protected $table = "blocked_rfid";
    protected $guarded = [];

    public static function isBlocked($tag) {
        $tag = self::where('tag', $tag)->count();
        return $tag > 0 ? true : false;
    }
}

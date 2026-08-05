<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class CommonCredential extends Model
{
    protected $table = "itm_common_credentials";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    public function setPassword($value) {
        $this->password = $value ? Crypt::encryptString($value) : null;
    }

    public function rawPassword() {
        return $this->password ? Crypt::decryptString($this->password) : null;
    }
}
<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;

class FileAttachment extends Model
{
    protected $table = "purchase_attachments";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    public function getExtensionCode() {
        $ext = strtolower($this->extension);
        if( in_array($ext, ['png', 'jpeg', 'jpg']) ) {
            return 1;
        }
        if( $ext == 'pdf' ) {
            return 2;
        }
        if( $ext == 'mp4' ) {
            return 3;
        }

        return -1;
    }

    public function getUrl() {
        return asset("purchase_attachments/" . $this->file_name);
    }
}
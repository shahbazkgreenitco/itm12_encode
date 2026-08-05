<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceAuditAttachment extends Model
{
    protected $table = "audit_attachments";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $fillable = ['reference_id','attachment_link','original_file_name','file_name','extension','uploader_id','tmp_id'];  
    const DIRECTORY = "uploads/device_audits";

    public function getFilePath() {
        return $this->reference_id . "/" . $this->file_name;
    }

    public function getUrl() {
        return asset("uploads/device_audits/" . $this->reference_id . "/" . $this->file_name);
    }

    public function getImageUrl() {
        return asset("uploads/device_audits/" . $this->file_name);
    }
    
    public static function getImagePath($file_name) {
        return asset("uploads/device_audits/" . $file_name);
    }
    
}

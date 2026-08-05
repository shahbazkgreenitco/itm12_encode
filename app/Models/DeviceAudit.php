<?php

namespace App\Models;
use App\Models\DeviceAuditAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceAudit extends Model
{
    protected $table = "device_audits";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $dates = ['deleted_at'];
    protected $ratings_options = [["id"=>1,"text"=>"Good"],["id"=>2,"text"=>"Average"],["id"=>3,"text"=>"Bad"]];
    use SoftDeletes;

    public static function getRatingsOptions() {
        return (new DeviceAudit)->ratings_options;
    }

    public static function generateName(UploadedFile &$file, $asJpg=false) {
        $img_name = str_random(18);
        $img_name .= date('his');
        $img_name .= ".";
        if($asJpg) {
            return $img_name . "jpg";
        }
        return $img_name . $file->extension();
    }

    public static function isSupportedFormat($extension) {
        $formats = ["jpg", "jpeg", "png"];
        if(in_array(strtolower($extension), $formats)) {
            return true;
        }

        return false;
    }

    public function getImages($withId=false) {
        $images = DeviceAuditAttachment::where("reference_id", "=", $this->id)->get();
        $image_paths = [];
        if($images && count($images)) {
            foreach($images as $v) {
                $image_paths[] = [ 
                    'id' => $v->id,
                    'url' => $v->getImageUrl()
                ];
            }
        }

        return $image_paths;
    }


    public static function getID() {
        return self::select("id")->first(); //limit(1)->get();
    }

    public function getUrl() {
        return asset("uploads/device_audits" . $this->id);
    }

    public function getfileName($withId=false) {
        $images = DeviceAuditAttachment::where("reference_id", "=", $this->id)->get();
        $image_name = [];
        if($image_name && count($image_name)) {
            foreach($image_name as $v) {
                $image_name[] = [ 
                    'name' => $v->original_file_name
                ];
                print_r($image_name);
                exit;
            }
        }

        return $image_name;
    }

}


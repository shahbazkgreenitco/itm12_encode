<?php

namespace App\Models\Device;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
class SWPatch extends Model
{
    protected $table = "sw_patch";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function trimUnfittedName($name) {
        try {
            if( strlen($name) < 80 ) {
                return $name;
            }
    
            $arr = explode(".", $name);
            $len = count($arr);
            
            $ext = $arr[$len-1];
            $arr[$len-1] = "";
            
            $fpart = implode("", $arr);
            $len = min(80, strlen($fpart));
            $new_name = substr($fpart, 0, $len);
            $new_name .= ("y." . $ext);
            return $new_name;
        } catch(\Exception $e) {
            echo $e->getMessage();
            return $name;
        }
    }
}


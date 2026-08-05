<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Helpers\Common as CommonHelper;

class Product extends Model
{
    protected $table = "itm_network_inventory_products";
    protected $dateFormat = "Y-m-d H:i:s";
    // protected $fillable = ["basic_id", "IdentifyingNumber", "Caption", "Vendor", "Version", "InstalledDate", "Publisher"];
    protected $guarded = [];

    public static function captionOptions() {
        return DB::table("itm_network_inventory_products")->select("Caption")->whereNotNull("Caption")->distinct()->orderBy("Caption")->pluck("Caption")->toArray();
    }

    public static function versionOptions() {
        return DB::table("itm_network_inventory_products")->select("Version")->where("Version", '!=' ,'')->whereNotNull("Version")->distinct()->orderBy("Version")->pluck("Version")->toArray();
    }

    public static function publisherOptions() {
        return DB::table("itm_network_inventory_products")->select("Publisher")->whereNotNull("Publisher")->distinct()->orderBy("Publisher")->pluck("Publisher")->toArray();
    }

    public function isChangeThere($data) {
        foreach($data as $k=>$v) {
            try {
                if($k == "InstalledDate") {
                    if($this->{$k} && !$v) {
                        return true;
                    }
                    elseif(!$this->{$k} && $v) {
                        return true;
                    }
                    elseif($this->{$k} && $v) {
                        $a = CommonHelper::getDateAs($this->{$k}, 'Y-m-d', 'Y-m-d H:i:s');
                        $b = CommonHelper::getDateAs($v, 'Y-m-d', 'Y-m-d H:i:s');
                        return $a != $b;
                    }
                }
                elseif( $k != "EstimatedSize" && $this->{$k} != $v ) {
                    return true;
                }
            }
            catch(\Exception $e) {

            }
        }
        return false;
    }

    public function dataForCache() {
        return (array) $this->only("basic_id", "IdentifyingNumber", "Caption", "Vendor", "Version", "InstalledDate", "Publisher", "EstimatedSize");
    }
}
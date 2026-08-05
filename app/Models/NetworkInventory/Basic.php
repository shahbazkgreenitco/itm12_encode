<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;
use DB;

class Basic extends Model
{
    protected $table = "itm_network_inventory_basic";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    public function validate_ip($ip) {
        try {
            return filter_var($ip, FILTER_VALIDATE_IP);
        }
        catch(\Exception $e) {
            return false;
        }
    }

    public static function manufacturerOptions() {
        return DB::table("itm_network_inventory_basic")->select("ComputerManufacturer")->distinct()->orderBy("ComputerManufacturer")->pluck("ComputerManufacturer")->toArray();
    }

    public static function modelOptions() {
        return DB::table("itm_network_inventory_basic")->select("ComputerModel")->distinct()->orderBy("ComputerModel")->pluck("ComputerModel")->toArray();
    }

    public static function osOptions() {
        return DB::table("itm_network_inventory_basic")->select("OSCaption")->distinct()->orderBy("OSCaption")->pluck("OSCaption")->toArray();
    }

    public static function processorNameOptions() {
        return DB::table("itm_network_inventory_basic")->select("ProcessorName")->distinct()->orderBy("ProcessorName")->pluck("ProcessorName")->toArray();
    }

    public static function computerSystemTypeOptions() {
        return DB::table("itm_network_inventory_basic")->select("ComputerSystemType")->distinct()->orderBy("ComputerSystemType")->pluck("ComputerSystemType")->toArray();
    }

    public static function generateDummySerial() {
        $serial = "itm_";
        $serial .= str_random(5);
        $serial .= date('ymdhis');
        return $serial;
    }

    public static function osManufacturerOptions() {
        return DB::table("itm_network_inventory_basic")->select("OSManufacturer")->distinct()->orderBy("OSManufacturer")->pluck("OSManufacturer")->toArray();
    }

    public static function versionOptions() {
        return DB::table("itm_network_inventory_basic")->select("OSVersion")->distinct()->orderBy("OSVersion")->pluck("OSVersion")->toArray();
    }

    public static function getRdpData($id)
    {
        $rdpData = DB::table('itm_network_inventory_basic as a')
            ->join('itm_network_inventory_products as pr', 'pr.basic_id', '=', 'a.id')
            ->where('pr.Caption', 'Greenitco')      
            ->where('a.id', $id)
            ->get();

        return $rdpData->toArray();    
    }
}
<?php

namespace App\Console\Commands;

use App\Helpers\Common as CommonHelper;
use App\Helpers\Common;
use App\Models\Assets;
use App\Models\Device;
use App\Models\Manufacture;
use App\Models\Model;
use App\Models\Settings;
use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;
use Log;

class OemWarrantyUpdateOtherBrands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oem_warranty_update_other_brands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To update OEM warranty details for hp asus & acer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed    
     */
    public function handle()
    {
        $now = new Carbon(config('app.timezone'));
        try {
            $manufacturerObj = Manufacture::where('name', "like", "asus%")->orWhere('name', "like", "acer%")->orWhere('name', "like", "hp%")->pluck('id')->toArray();
            $modelObj = Model::whereIn('manufacturer_id', $manufacturerObj)->pluck('id')->toArray();
            $deviceObj = Device::select('assets.id', 'assets.serial', 'assets.model_id', 'mnu.name as manu_name', 'assets.product_number')
            ->leftJoin('models as mdl', 'mdl.id', '=', 'assets.model_id')
            ->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'mdl.manufacturer_id')
            ->whereIn('assets.model_id', $modelObj)->whereNull('manufacturer_warranty_data')->get();
            foreach($deviceObj as $i => $device) {
                if(!$device->model || !$device->model->manufacturer || !$device->serial) {
                    continue;
                }
                $manufacturer = strtolower(trim($device->model->manufacturer->name));
                $mlWarranties = ['hp','asus','acer'];            
                $brand = null;
                foreach ($mlWarranties as $element) {
                    if (strpos($manufacturer, $element) !== false) {
                        $brand = $element;
                    }
                }
                $result= Common::getWarrantyFromML($brand, $device->serial, $device->product_number);
                
                if($result && isset($result['data'])) {
                	$this->info($brand. " result: " . $device->serial. " - " . json_encode($result['data']));    
                	$device->manufacturer_warranty_data = json_encode($result['data']);
                    if (!$device->save()) {
                        Log::error("OemWarrantyUpdate from ML: " . $device->id);
                    }
                }
                $delay = rand(120, 180);
                sleep($delay);
                // Delay the request after every 10 calls so that api is not blocked
                // if (($i + 1) % 30 === 0) {
                //     $delay = rand(30, 60);
                //     sleep($delay);
                // }
            }
        }
        catch(\Exception $e) {
            Log::error("OemWarrantyUpdateOtherBrands" . $e->getMessage());
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Helpers\Common as CommonHelper;
use App\Models\Assets;
use App\Models\Device;
use App\Models\Manufacture;
use App\Models\Model;
use App\Models\Settings;
use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;
use Log;

class OemWarrantyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oem_warranty_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To update OEM warranty details for dell & lenovo';

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
            $manufacturerObj = Manufacture::where('name', "like", "dell%")->orWhere('name', "like", "lenovo")->pluck('id')->toArray();
            $modelObj = Model::whereIn('manufacturer_id', $manufacturerObj)->pluck('id')->toArray();
            $deviceObj = Device::select('assets.id', 'assets.serial', 'assets.model_id', 'mnu.name as manu_name')
                ->leftJoin('models as mdl', 'mdl.id', '=', 'assets.model_id')
                ->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'mdl.manufacturer_id')
                ->whereIn('assets.model_id', $modelObj)->whereNull('manufacturer_warranty_data')->get();
                // ->whereIn('assets.id', array(292,332,613,34))
            foreach($deviceObj as $device) {
                if(!$device->model || !$device->model->manufacturer || !$device->serial) {
                    continue;
                }
                $manufacturer = strtolower(trim($device->model->manufacturer->name));
                if(stripos($manufacturer, "dell") !== false) {
                    $this->info("dell: " . $device->id);
                    $result = $this->getDellWarrantyInfo($device);
                    if($result && is_array($result) && count($result) && isset($result['entitlements'])) {
                        $data = [];
                        $data['serial'] = $device->serial;
                        $data['info'] = $result['entitlements'];
                        $device->ship_date = isset($result['shipDate']) ? date('Y-m-d H:i:s', strtotime($result['shipDate'])) : null;
                        $device->manufacturer_warranty_data = json_encode($data);

                        if(!$device->save()) {
                            Log::error("OemWarrantyUpdate dell: " . $device->id);
                        }
                    }
                }
                elseif(stripos($manufacturer, "lenovo") !== false) {
                    $this->info("lenovo: " . $device->id);
                    $result = $this->getLenovoWarrantyInfo($device);
                    if($result && is_array($result) && count($result) && isset($result['Warranty'])) {
                        $device->manufacturer_warranty_data = json_encode($result['Warranty']);
                        $device->ship_date = isset($result['Shipped']) ? date('Y-m-d H:i:s', strtotime($result['Shipped'])) : null;
                        if (!$device->save()) {
                            Log::error("OemWarrantyUpdate lenovo: " . $device->id);
                        }
                    }
                }
            }
        }
        catch(\Exception $e) {
            Log::error("OemWarrantyUpdate" . $e->getMessage());
        }
    }

    public function getLenovoWarrantyInfo($device) {
        $url = config('services.warrenty.lenovo.url') . '?serial=' . trim($device->serial);
        $clientID = "g+7Rk5Q3PwMR/VSU9sF1ug==";
        $headers = array(
            'Accept: application/json',
            'ClientID: ' . $clientID
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $result = curl_exec($ch);
        if(curl_errno($ch) || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            return [curl_errno($ch)];
        }
        curl_close($ch);
        $return_val = json_decode($result, true);
        if(!$result || !$return_val || !is_array($return_val) || !count($return_val) || !isset($return_val["InWarranty"])) {
            return [];
        }

        return [
            'Warranty' => $return_val['Warranty'],
            'Shipped' => $return_val['Shipped'],
        ];
    }

    public function getDellWarrantyInfo($device) {
        try {
            $token = Settings::getSettings()->getDellWarrantyToken();

            if(! $token) {
                return [];
            }

            $headers = array(
                'Accept: application/json',
                'Authorization: Bearer ' . $token
            );

            $url = 'https://apigtwb2c.us.dell.com/PROD/sbil/eapi/v5/asset-entitlements?servicetags=' . $device->serial;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            $result = curl_exec($ch);
            if(curl_errno($ch) || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
                return [curl_errno($ch)];
            }
            curl_close($ch);

            $val = json_decode($result, true);
            if(!$result || !$val || !is_array($val) || !count($val)) {
                return [];
            }

            $return = [];
            $return['entitlements'] = isset($val[0]['entitlements']) ? $val[0]['entitlements'] : '';
            $return['shipDate'] = isset($val[0]['shipDate']) ? $val[0]['shipDate'] : '';

            return $return;
            // return isset($val[0]['entitlements']) ? $val[0]['entitlements'] : [];
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return [];
        }
    }
}

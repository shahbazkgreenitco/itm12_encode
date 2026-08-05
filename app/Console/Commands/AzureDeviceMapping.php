<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Device;
use App\Models\Device\PatchManagementGroup;
use App\Models\Device\PatchManagementGroupDevice;
use App\Models\Location;
use App\Models\Manufacture;
use App\Models\Model;
use App\Models\NetworkDevice;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AzureDeviceMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'azureDeviceMapping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Map Azure device with asset table';

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
     * @return int
     */
    public function handle()
    {
        $devicecount = Device::whereNotIn('status_id', [7])->count();
        if($devicecount > config("services.assets.asset_limit")) {
            $return["msg"] = "You don't have permission to insert more than " . config('services.assets.asset_limit') . " devices. Please contact Admin";
            return response()->json($return);
        }

        try{
            DB::beginTransaction();
            $unMappedDevices = NetworkDevice::whereNull('device_id')->whereNotNull('BIOSSerialNumber')->get();
            foreach($unMappedDevices as $device) {
                if($device->BIOSSerialNumber != '') {
                    $data = [];
                    $dummy_tag = sha1(time() . rand());
                    $models = Model::where('name', 'like', '%'.$device->ComputerModel.'%')->first();
                    if(empty($models)) {
                        $manufacturer_id = null;
                        $manufacturer = Manufacture::where('name','like','%'.$device->ComputerManufacturer.'%')->first();
                        if (empty($manufacturer)) {
                            $addManufacturer =  Manufacture::create(['name' => $device->ComputerManufacturer, 'user_id' => 1]);
                            $manufacturer_id = $addManufacturer->id;
                        } else {
                            $manufacturer_id = $manufacturer->id;
                        }

                        $addModel =  Model::create([
                            'name' => $device->ComputerModel,
                            'manufacturer_id' => $manufacturer_id,
                            'category_id' => 5,
                            'user_id' => 1,
                        ]);
                        $data['model_id'] = $addModel->id;
                    } else {
                        $data['model_id'] = $models->id;
                    }
                    $data["asset_tag"] = substr($dummy_tag, 0, 98);
                    $data["name"] = $device->ComputerName;
                    $data["serial"] = $device->BIOSSerialNumber;
                    $data["mac"] = $device->ActiveMACAddress;
                    $data["added_from"] = 3;
                    $data["user_id"] = 1;
                    $data["ip"] = $device->IPv4;
                    $data["company_id"] = Company::first()->id;
                    $data["archived"] = "0";
                    $data["physical"] = "1";
                    $data["status_id"] = 1;
                    $data["depreciate"] = "0";
                    $location = Location::where('name','like','%Other%')->first();
                    if(empty($location)) {
                        $otherLocation = new Location();
                        $otherLocation->name = $otherLocation->address = "Other";
                        $otherLocation->country = "IN";
                        $otherLocation->country_id = 101;
                        $otherLocation->state_id = 4008;
                        $otherLocation->city_id = 133024;
                        $otherLocation->currency = "INR";
                        $otherLocation->user_id = 1;
                        $otherLocation->save();
                        $data['rtd_location_id'] = $otherLocation->id;
                    } else {
                        $data['rtd_location_id'] = $location->id;
                    }
                    $asset = new Device();
                    $asset->fill($data);
                    if(!$asset->save()) {
                        Log::error("AzureDeviceMapping error: Unable to add device");
                    }
                    if(!empty($asset)) {
                        $asset->asset_tag = $asset->fixDeviceTag();
                        $asset->save();

                        $getGroups = PatchManagementGroup::where('auto_added_new_device', 1)->select('id', 'auto_added_new_device')->get();
                        foreach ($getGroups as $key => $getGroup) {
                            // Add Devices to the Group
                            $exitsGroupDevice = PatchManagementGroupDevice::where('group_id', $getGroup->id)->where('device_id', $asset->id)->first();
                            if($exitsGroupDevice  == null) {
                                PatchManagementGroupDevice::insert([
                                    'group_id' => $getGroup->id,
                                    'device_id' => $asset->id,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);                                
                            }
                        }
                    }
                    $updateAzureDeviceId = NetworkDevice::find($device->id);
                    $updateAzureDeviceId->device_id = $asset->id;
                    if(!$updateAzureDeviceId->save()) {
                        Log::error("AzureDeviceMapping error : Unable to update device id");
                    }
                }
            }
            Log::info("AzureDeviceMapping : Device added successfully");            
            DB::commit();
            return 0;
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error("AzureDeviceMapping error : ".$e->getMessage());
            return 1;
        }
    }
}

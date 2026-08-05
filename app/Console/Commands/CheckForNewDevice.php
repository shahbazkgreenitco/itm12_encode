<?php

namespace App\Console\Commands;

use App\Models\Component;
use App\Models\NetworkInventory\Monitor;
use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Settings;
use Log;
use App\Models\Manufacture;
use App\Models\Model;
use App\Models\Category;
use App\Models\Actionlog;
use Auth;
use App\Models\User;
use App\Models\Device;
use App\Models\NetworkInventory\Basic;
use App\Models\Location;
use App\Models\NetworkInventory\MappedLocation;
use App\Models\NetworkInventory\OsMaster;
use App\Models\NetworkInventory\TrackedDevice;
use App\Helpers\Common as CommonHelper;

class CheckForNewDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ni:check_for_new_device';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Program to check the network inventory has any new device. If yes, add it on all device list page';

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
        $this->info("start CheckForNewDevice");
        $settings = Settings::getSettings();
        if($settings->ni_add_device_flag) {
            return;
        }

        /* check any new device found */
        $get_new_serials = DB::select('select * from itm_network_inventory_basic where is_dupe is null and deleted_at is null and is_virtual is null and id not in (select b.id from itm_network_inventory_basic as b join assets as a on b.BIOSSerialNumber = a.serial)');
        if(! count($get_new_serials)) {
            return;
        }

        $settings->blockDeviceAddProcess();

        /* login as Super User */
        // $get_super = User::where('permission', 'like', '%superuser":1%')->whereNull('deleted_at')->where('activated', '=', 1)->limit(1)->get();
        $get_super = User::whereHas("roles", function($q){ $q->where("name", "SuperAdmin"); })->get();
        Auth::loginUsingId($get_super[0]->id);

        foreach($get_new_serials as $foundDev) {
            try {
                $data = [];
                $data["model_id"] = $this->getModel($foundDev->ComputerModel, $foundDev->ComputerManufacturer);
                $data["status_id"] = '1';
                $data["company_id"] = $foundDev->company;
                if(config('app.client') == "ltts") {
                    $data["rtd_location_id"] = '11';
                } else if(config('app.client') == "airasia") {
                    $locationObj = Location::first();
                    if(!empty($locationObj)) {
                        $data["rtd_location_id"] = $locationObj->id;
                    }
                } else {
                    $locationObj = Location::first();
                    if(!empty($locationObj)) {
                        $data["rtd_location_id"] = $locationObj->id;
                    }
                }
                $data["name"] = $foundDev->ComputerName;
                $data["serial"] = $foundDev->BIOSSerialNumber;
                $data["asset_tag"] = substr(sha1(time()), 0, 98);

                $device = new Device;
                $device->fill($data);
                $device->archived = "0";
                $device->physical = "1";
                $device->depreciate = "0";
                $device->assigned_to = null;
                $device->added_from = 2;
                $device->ip = $foundDev->IPv4;
                $device->mac = $foundDev->ActiveMACAddress;
                $device->user_id = Auth::user()->id;
                $device->purchase_cost = 0.0000;

                $final_checkup_for_exists = Device::where('serial', 'like', $foundDev->BIOSSerialNumber)->count();
                if($final_checkup_for_exists) {
                    Log::error("NI: Sorry device already exists." . $foundDev->BIOSSerialNumber);
                    continue;
                }
                $devicecount = Device::whereNotIn('status_id', [7])->count();
                if ($devicecount > config("services.assets.asset_limit")) {
                    Log::error("NI: Device limit exceeded. Cannot insert more than " . config('services.assets.asset_limit') . " devices.");
                    return;
                }
                if($device->save()) {
                    $device->asset_tag = $device->getNextId();
                    if(in_array(config('app.client'), ["rolepermission", "knightfrank", "rashmi"])) {
                        $newStatus = $device->status_id;
                        if(!empty($newStatus)){
                            CommonHelper::updateStatusCounts($oldStatus = null, $newStatus, $device);
                        }
                    }
                    if($foundDev->IPv4 && $device->validate_ip($foundDev->IPv4)) {
                        $mapped_locs = MappedLocation::get();
                        if(count($mapped_locs)) {
                            foreach($mapped_locs as $ml) {
                                if($ml->check_ip_in_range($foundDev->IPv4)) {
                                    $td = [
                                        "device_id" => $device->id,
                                        "map_loc_id" => $ml->id,
                                        "ip" => $foundDev->IPv4,
                                        "location_id" => $ml->location_id,
                                        "place_id" => $ml->place_id
                                    ];
                                    $device->rtd_location_id = $ml->location_id;
                                    $device->ni_detected_location = $ml->location_id;
                                    TrackedDevice::create($td);
                                    break;
                                }
                            }
                        }
                    }

                    $device->save();
                    Actionlog::deviceAdded($device, Auth::user()->id);
                    Basic::find($foundDev->id)->update([
                        'device_id' => $device->id
                    ]);

                    // Store OS details on OS Master Table
                    $basicObj = Basic::find($foundDev->id);
                    if(!empty($basicObj)) {
                        $osMasterObj = OsMaster::where('OsCaption', trim($basicObj->OSCaption))->first();
                        if(empty($osMasterObj)) {
                            OsMaster::create([
                                'OsCaption' => $basicObj->OSCaption,
                                'OsVersion' => $basicObj->OSVersion,
                                'OSManufacturer' => $basicObj->OSManufacturer,
                            ]);
                        }
                    }

                    Log::info("Device Created Success : " . $device->id);
                }

                $componentId = Monitor::where('basic_id', $foundDev->id)->whereNotNull('component_id')->value('component_id');
                if (!$componentId && !$device->id) {
                    continue;
                }
                $component = Component::find($componentId);
                if (!$component) {
                    continue;
                }
                $target_location_id = $device->rtd_location_id;
                $component->update([
                    'checked_out_to'  => $device->id,
                    'checked_out_for' => 2,
                    'checked_out_at'  => (new Carbon(config('app.timezone')))->format('Y-m-d H:i:s'),
                    'checked_in_at'   => null,
                    'status'          => 4,
                ]);

                $log = new Actionlog();
                $log->asset_type = "component";
                $log->checkedout_to = $device->id;
                $log->assigned_for = "2";
                $log->location_id = $target_location_id;
                $log->assigned_to_type = "2";
                $log->asset_id = $component->id;
                $log->note = "Checkout from Device Add";
                $log->action_type = "checkout";
                $log->user_id = Auth::user()->id;
                $log->save();
                // sleep(2);
            }
            catch(\Exception $e) {
                Log::error("Error at new device add by ni");
                Log::error($e->getMessage());
            }
        }
        $settings->unblockDeviceAddProcess();
        $this->info("success CheckForNewDevice");
    }

    protected function getModel($value, $manufacturer) {
        $value = trim($value);
        if($value == "") {
            return Model::getDefault(true);
        }

        $get = Model::where('name', 'like', $value)->whereNull('deleted_at')->get();
        if(count($get)) {
            return $get[0]->id;
        }

        $data['manufacturer_id'] = $this->getManufacturer($manufacturer);
        $data['category_id'] = Category::getDefault(true);
        $data['name'] = $value;
        $data['user_id'] = Auth::user()->id;

        $new = Model::create($data);
        return $new->id;
    }

    protected function getManufacturer($value) {
        $value = trim($value);
        if($value == "") {
            return Manufacture::getDefault(true);
        }

        $get = Manufacture::where('name', 'like', $value)->whereNull('deleted_at')->get();
        if(count($get)) {
            return $get[0]->id;
        }

        $new = Manufacture::create(['name'=>$value, 'user_id'=>Auth::user()->id]);
        return $new->id;
    }
}
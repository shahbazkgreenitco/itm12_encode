<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NetworkInventory\MappedLocation;
use App\Models\Device;
use DB;

class MapDeviceLocationByIP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ni:map_device_location_by_ip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manual Device Location mapping by IP';

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
        /* collect the mapped ips */
        $mapped_locs = MappedLocation::get();

        if(!count($mapped_locs)) {
            $this->info("No Location Map found");
            return;
        }

        /* collect the device who have IP */
        $devices = DB::table('assets as dev')->join('itm_network_inventory_basic as bas')->select('dev.id', 'dev.ip')->whereNull('dev.deleted_at')->whereNotNull('dev.ip')->whereNull('dev.ni_detected_location')->get();

        $totDevices = count($devices);
        $totMappedLocations = 0;
        if( !$totDevices ) {
            $this->info("No Device to map location");
            return;
        }

        /* check range available for each device */
        foreach($devices as $niDevice) {
            foreach($mapped_locs as $ml) {
                if($ml->check_ip_in_range($niDevice->ip)) {
                    // $td = [
                    //     "device_id" => $niDevice->id,
                    //     "map_loc_id" => $ml->id,
                    //     "ip" => $niDevice->ip,
                    //     "location_id" => $ml->location_id,
                    //     "place_id" => $ml->place_id
                    // ];
                    // TrackedDevice::create($td);
                    Device::where('id', '=', $niDevice->id)->update([
                        'ni_detected_location' => $ml->location_id
                    ]);
                    $totMappedLocations++;
                    break;
                }
            }
        }

        $this->info("Total Devices: " . ((string) $totDevices));
        $this->info("Total Devices: " . ((string) $totMappedLocations));
    }
}

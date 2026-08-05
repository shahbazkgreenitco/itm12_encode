<?php

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;
use App\Models\Assets;
use App\Models\NetworkInventory\Basic;
use DB;

class ManuallyAssetsDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove_assets_permanent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $path = public_path('Asset.csv');
        $handle = fopen($path, 'r');
        if ($handle) {
            $i = 0;
            while ($line = fgetcsv($handle)) {
                $i++;

                if (!$line || !is_array($line)) {
                    break;
                }

                $input = [];
                $input['serial'] = trim($line['0']);
                try {

                    $itm_network_inventory_basic = Basic::where('BIOSSerialNumber', $input['serial'])->first();

                    if(!empty($itm_network_inventory_basic)) {
                        // Remove Products of NI Device
                        $itm_network_inventory_products = DB::table('itm_network_inventory_products')->where('basic_id', $itm_network_inventory_basic->id);
                        if (isset($itm_network_inventory_products)) {
                            $itm_network_inventory_products->delete();
                        }

                        // Remove Sound Driver of NI Device
                        $itm_network_inventory_sound_devices = DB::table('itm_network_inventory_sound_devices')->where('basic_id', $itm_network_inventory_basic->id);
                        if (isset($itm_network_inventory_sound_devices)) {
                            $itm_network_inventory_sound_devices->delete();
                        }

                        // Remove Video Driver of NI Device
                        $itm_network_inventory_video_controllers = DB::table('itm_network_inventory_video_controllers')->where('basic_id', $itm_network_inventory_basic->id);
                        if (isset($itm_network_inventory_video_controllers)) {
                            $itm_network_inventory_video_controllers->delete();
                        }

                        // Remove Drives of NI Device
                        $itm_network_inventory_volumes = DB::table('itm_network_inventory_volumes')->where('basic_id', $itm_network_inventory_basic->id);
                        if (isset($itm_network_inventory_volumes)) {
                            $itm_network_inventory_volumes->delete();
                        }

                        // Remove User Accounts of NI Device
                        $itm_network_user_accounts = DB::table('itm_network_user_accounts')->where('basic_id', $itm_network_inventory_basic->id);
                        if (isset($itm_network_user_accounts)) {
                            $itm_network_user_accounts->delete();
                        }

                        // Remove Change Cache of NI Device
                        $itm_network_change_cache = DB::table('itm_network_change_cache')->where('basic_id', $itm_network_inventory_basic->id);
                        if (isset($itm_network_change_cache)) {
                            $itm_network_change_cache->delete();
                        }

                        // Remove Change Logs of NI Device
                        $itm_network_change_logs = DB::table('itm_network_change_logs')->where('basic_id', $itm_network_inventory_basic->id);
                        if (isset($itm_network_change_logs)) {
                            $itm_network_change_logs->delete();
                        }

                        // Remove Disk Drives of NI Device
                        $itm_network_inventory_disk_drives = DB::table('itm_network_inventory_disk_drives')->where('basic_id', $itm_network_inventory_basic->id);
                        if (isset($itm_network_inventory_disk_drives)) {
                            $itm_network_inventory_disk_drives->delete();
                        }

                        // Remove Monitor of NI Device
                        $itm_network_inventory_monitors = DB::table('itm_network_inventory_monitors')->where('basic_id', $itm_network_inventory_basic->id);
                        if (isset($itm_network_inventory_monitors)) {
                            $itm_network_inventory_monitors->delete();
                        }

                        // Remove Physical Memory of NI Device
                        $itm_network_inventory_physical_memories = DB::table('itm_network_inventory_physical_memories')->where('basic_id', $itm_network_inventory_basic->id);
                        if (isset($itm_network_inventory_physical_memories)) {
                            $itm_network_inventory_physical_memories->delete();
                        }
                    }
                    if(isset($itm_network_inventory_basic) && !empty($itm_network_inventory_basic)) {
                        $itm_network_inventory_basic->delete();
                    }

                    $assetObj = Device::Where('serial', $input['serial'])->withTrashed()->first();
                    if(!empty($assetObj)) {
                        // Remove Asset cache
                        $interacted_records = DB::table('interacted_records')->where('serial', $assetObj->serial);
                        if(isset($interacted_records)) {
                            $interacted_records->delete();
                        }

                        $interacted_caches = DB::table('interacted_caches')->where('serial', $assetObj->serial);
                        if(isset($interacted_caches)) {
                            $interacted_caches->delete();
                        }

                        if(isset($assetObj->id)) {
                            $asset_logs = DB::table('asset_logs')->where('asset_id', $assetObj->id)->where('asset_type','hardware')->delete();
                        }
                    }

                    if(isset($assetObj) && !empty($assetObj)) {
                        $assetObj->delete();
                    }
                   
                }
                catch(\Exception $e) {
                    $this->info("Line - " . $i . ":- " . $e->getMessage());
                }
            }
            $this->info('\ncompleted');
        }
    }
}

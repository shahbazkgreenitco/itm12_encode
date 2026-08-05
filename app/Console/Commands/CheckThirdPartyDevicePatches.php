<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\NetworkInventory\Product;
use App\Models\PatchManagement\PatchDetail;
use App\Models\PatchManagement\ThirdPartyPatchMaster;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Mail;
Use DB;

class CheckThirdPartyDevicePatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:third-party-patch-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check patch status for all devices via third-party API';

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
        if(!config("services.patch_management.enabled")){
            return;
        }
        $today = Carbon::today()->toDateString();
        $patchLicenseDevice = DB::table('itm_network_inventory_basic as basic')
        ->join('assets', function($join) {
            $join->on('basic.device_id', '=', 'assets.id')
                ->whereNull('assets.deleted_at');
        })
        ->join('itm_network_inventory_products as p', 'basic.id', 'p.basic_id')
        ->where('p.Caption','ITM Agent')
        ->where('assets.company_id', 1)
        ->whereNull('basic.is_dupe')
        ->whereNull('basic.deleted_at')
        ->select(
            'basic.id',
            'basic.device_id',
            'basic.OSCaption',
        )->get();
        foreach ($patchLicenseDevice as $key => $value) {
        $products = Product::where('itm_network_inventory_products.basic_id', $value->id)
            ->select(
                'itm_network_inventory_products.Caption as itm_product_caption',
                'itm_network_inventory_products.Version as itm_product_version'
            )
            ->get()
            ->keyBy(function ($item) {
                return strtolower(trim($item->itm_product_caption));
            })
            ->map(function ($item) {
                return $item->itm_product_version;
            })
            ->toArray();
            $tpPatchMaster  = ThirdPartyPatchMaster::leftJoin('agent_os_support as os', 'third_party_patch_masters.os_id', '=', 'os.id')
                ->whereRaw('? LIKE CONCAT("%", os.os_name, "%")', [$value->OSCaption])->whereDate('third_party_patch_masters.created_at', $today)->get();
            foreach ($tpPatchMaster as $tpKey => $tpValue) {
                $caption = strtolower(trim($tpValue->name));
                if (!isset($products[$caption])) {
                    // this device not a create patch data
                    $exits_patch = 3;
                } else {
                    $productVersion = $products[$caption];
                    if (version_compare($tpValue->version, $productVersion, '>')) {
                        // Missing patch data
                        $exits_patch = 1;
                    } elseif (version_compare($tpValue->version, $productVersion, '=')) {
                        // Updated patch data
                        $exits_patch = 2;
                    } else {
                        // version empty than not need
                        $exits_patch = 2;
                    }
                }
                $latestEntry = PatchDetail::where('patch_details.device_id',$value->device_id)->where('patch_details.unique_name', $tpValue->unique_name)->first();
                if($latestEntry == null){
                    try {
                        if($exits_patch != 3) {
                            $patch = new PatchDetail();
                            $patch->name =  $tpValue->name;
                            $patch->device_id = $value->device_id;
                            $patch->description = $tpValue->description;
                            $patch->version = $tpValue->version;
                            $patch->publisher_date = $tpValue->publisher_date;
                            $patch->patch_type = $tpValue->patch_type;
                            $patch->reboot = $tpValue->reboot;
                            $patch->severity = $tpValue->severity;
                            $patch->patch_status = $exits_patch == 2 ? 4 : 0;
                            $patch->os_id = $tpValue->os_id;
                            $patch->type = $tpValue->type;
                            $patch->link = $tpValue->link;
                            $patch->file = $tpValue->file;
                            $patch->original_file_name = $tpValue->original_file_name;
                            $patch->extension = $tpValue->extension;
                            // $patch->approved_status = $tpValue->approved_status;
                            // $patch->approved_by = $tpValue->approved_by;
                            $patch->publisher = $tpValue->publisher;
                            $patch->publisher_patch_id = $tpValue->publisher_patch_id;
                            $patch->via_party = 0;
                            $patch->unique_name = $tpValue->unique_name;
                            $patch->patch_type_update = $tpValue->patch_type_update;
                            $patch->exits_patch = $exits_patch;
                            $patch->save();  
                        }
                    } catch (\Exception $e) {
                        Log::error("CheckThirdPartyDevicePatches Error: " . $e->getMessage(), [
                            'device_id' => $value->device_id,
                            'patch_name' => $tpValue->name,
                        ]);
                    }
                }
            }
        }
    }
}
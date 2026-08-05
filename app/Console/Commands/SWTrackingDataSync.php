<?php

namespace App\Console\Commands;

use App\Models\ApplicationUsage;
use App\Models\Assets;
use App\Models\NetworkInventory\SWTracking\SWTracking;
use App\Models\NetworkInventory\SWTracking\SWTrackingDataProcess;
use App\Models\NetworkInventory\SWTracking\SWTrackingLog;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\NetworkInventory\Basic;
use Illuminate\Support\Facades\Storage;
use App\Models\NetworkInventory\Product;

class SWTrackingDataSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swTrackingDataSync';

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
        if(config("services.software_tracking.enabled") != 1) {
            return;
        }
        $this->info("start process");
        $swdata = SWTrackingLog::limit('100')->get();
        if (count($swdata) > 0) {
            foreach ($swdata as $sw) {
                try {
                    $data = $this->getDataFromLog($sw->file_path);
                    $serialNumber = $data->serial;
                    $sw->date = $data->date;
                    $applications = $data->applications ?? [];
                    $appNames = array_column($applications, 'Application');
                    $niAsset = DB::table('itm_network_inventory_basic as basic')->select('a.id as asset_id','basic.id','a.serial')
                        ->join('assets as a','a.id','basic.device_id')->where('BIOSSerialNumber', $serialNumber)->first();

                    if ( !empty($niAsset) && $niAsset->asset_id != null) {
                        $sw->device_id = $niAsset->asset_id;
                        $basic_id = $niAsset->id;
                        $products = Product::whereIn('Caption', $appNames)->where('basic_id', $basic_id)->get()->keyBy('Caption');
                        if ($products->isEmpty()) {
                            $this->storeSWProcessedData($sw, $is_process = 0);
                            $this->info("Caption not found in product");
                            continue;
                        } else {
                            $licenses = DB::table('itm_network_inventory_products as sn')
                                ->select('sn.id', 'sn.Caption', 'sn.Version', 'sn.publisher', 'l.id as license_id', 'ls.id as seat_id','ls.asset_id','sn.basic_id')
                                ->join('licenses as l', function ($q) {
                                    $q->on('sn.Caption', '=', 'l.name')
                                    ->where('l.is_tracked', true)
                                    ->whereNull('l.deleted_at');
                                })
                                ->join('license_seats as ls', function($q) use ($niAsset) {
                                    $q->on('l.id', '=', 'ls.license_id')
                                    ->where('ls.asset_id', '=', $niAsset->asset_id)
                                    ->whereNull('ls.deleted_at');
                                })
                                ->whereIn('sn.id', $products->pluck('id'))
                                ->get()
                                ->keyBy('id');
                        }
                         
                        // Log::info(['licenses'=>$licenses->toArray()]);
                        $uaNames = [];
                        foreach ($applications as $app) {
                            $uaNames[] = strtolower($app->Account_User);
                        }
                        $uaNames = array_unique($uaNames);
                        $users = DB::table('users')
                            ->where(function ($query) use ($uaNames) {
                                $query
                                    ->whereIn('username', $uaNames)
                                    ->orWhereIn('email', $uaNames)
                                    ->orWhereIn('first_name', $uaNames)
                                    ->orWhereIn('last_name', $uaNames)
                                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) IN ('" . implode("','", $uaNames) . "')");
                            })
                            ->get()
                            ->keyBy(function ($user) use ($uaNames) {
                                $fullName = trim($user->first_name . ' ' . $user->last_name);
                                foreach ($uaNames as $uaName) {
                                    Log::info(["$user->first_name == $uaName" => strtolower($user->first_name) == strtolower($uaName)]);
                                    if (strcasecmp($user->username, $uaName) == 0 || strcasecmp($user->email, $uaName) == 0 || strcasecmp($fullName, $uaName) == 0 || strcasecmp($user->first_name, $uaName) == 0 || strcasecmp($user->last_name, $uaName) == 0) {
                                        return strtolower($uaName);
                                    }

                                }
                                return strtolower($fullName);
                            });
                        $skipped = [];
                        foreach ($applications as $app) {
                            $product = $products->get($app->Application);
                            if (!$product) {
                                $skipped[] = $app->Application;
                                continue;
                            }

                            foreach ($app->Usage as $usage) {
                                $license = $licenses->get($product->id);
                                $user = $users[strtolower($app->Account_User)] ?? null;
                                if ($license) {
                                    SwTracking::updateOrCreate(
                                        [
                                            'serial' => $serialNumber,
                                            'license_id' => $license->license_id,
                                            'date' => $usage->Date,
                                            'name' => $app->Account_User,
                                        ],
                                        [
                                            'sw_usage_time' => $usage->UsageTime,
                                            'sw_publisher' => $license->publisher,
                                            'user_id' => $user->id ?? null,
                                            'sw_name' => $license->Caption,
                                            'sw_version' => $license->Version,
                                        ],
                                    ); 
                                }
                                ApplicationUsage::updateOrCreate(
                                    [
                                        'serial' => $serialNumber,
                                        'date' => $usage->Date,
                                        'licence_id' => $product->id, // license_id mean itm product id.
                                    ],
                                    [
                                        'usage_time' => $usage->UsageTime,
                                    ]
                                );
                            }
                        }
                        $this->storeSWProcessedData($sw, $is_process = 1);
                        Log::error(['status' => 'success', 'message' => 'Data saved successfully.']);
                    } else {
                        $this->storeSWProcessedData($sw, $is_process = 0);
                        $this->info("Device not found");
                    }
                } catch (\Exception $e) {
                    $this->storeSWProcessedData($sw);
                    Log::error('SoftwareUsage: ' . $e->getMessage());
                }
            }
        }
        return 0;
    }

    public function storeSWProcessedData(SwTrackingLog $sw, $is_process = null)
    {
        try {
            $now = new Carbon(config('app.timezone'));
            $swProcessed = new SWTrackingDataProcess();
            $swProcessed->serial = $sw->serial;
            $swProcessed->date = $sw->date;
            $swProcessed->device_id = $sw->device_id;
            $swProcessed->file_path = $sw->file_path;
            $swProcessed->is_processed = !empty($is_process) ? 1 : 0;
            $swProcessed->processed_at = $now->format('Y-m-d H:i:s');
            $swProcessed->updated_at = $sw->updated_at;
            $swProcessed->created_at = $sw->created_at;
            $swProcessed->save();
            $sw->delete();

            // Log::info('storeSWProcessedData: ', ['status' => 'successfully process']);
        } catch (\Throwable $th) {
            Log::error('storeSWProcessedData: ', [
                'status' => 'failed', 'msg' => $th->getMessage(),
            ]);
        }
    }
    public function getDataFromLog($file_path)
    {
        $return = [];

        if (!$file_path) {
            return $return;
        }

        $raw_content = Storage::get($file_path);

        if (!$raw_content) {
            return $return;
        }

        try {
            $return = json_decode($raw_content);
            return $return;
        } catch (\Exception $e) {
            return $return;
        }
    }
}
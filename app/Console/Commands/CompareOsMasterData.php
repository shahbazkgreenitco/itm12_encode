<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NetworkInventory\Basic;
use App\Models\NetworkInventory\OsMaster;
use DB;
use Log;

class CompareOsMasterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compare_os_master_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare data to the os_master and itm_network_inventory_basic table';

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

        try {
            $basicData = Basic::select('OsCaption', 
                           DB::raw('MAX(OSManufacturer) as Manufacturer'), 
                           DB::raw('MAX(OSVersion) as Version'))
                 ->groupBy('OsCaption')
                 ->get();
                foreach ($basicData as $key => $value) {
                    if($value->OsCaption != null){
                        $osMasterData = OsMaster::where('OsCaption', $value->OsCaption)->first();
                        if(empty($osMasterData) || $osMasterData == null){
                            $osStore = new OsMaster();
                            $osStore->OsCaption = $value->OsCaption;
                            $osStore->OSVersion = $value->Version;
                            $osStore->OSManufacturer = $value->Manufacturer;
                            $osStore->save();
                        }else{
                            Log::info('operating system is already inside os master table this id '. $osMasterData->id) ;
                        }
                    }else{
                       Log::info('Os Caption is empty this id '.$value->id) ;
                    }
                }
        } catch (\Exception $e) {
            $this->info("compare_os_master_data - ".$e->getMessage());
        }
    }
}

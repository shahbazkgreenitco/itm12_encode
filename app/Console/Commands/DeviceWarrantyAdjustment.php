<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Helpers\Common as CommonHelper;
use App\Models\Device;
use App\Models\Manufacture;
use App\Models\Model;

class DeviceWarrantyAdjustment extends Command
{
  protected $signature = 'device:warranty_adjustment';

  protected $description = 'To adjust warranty info';

  public const STR_DELL = "Dell";

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {
    /*$manufacturer = Manufacture::where('name', 'like', self::STR_DELL.'%')->orWhere('name', 'like', 'lenovo')->select('id')->pluck('id');

    if(! $manufacturer) {
      $this->info('No Manufacturer');
      return;
    }

    $get_models = Model::whereIn('manufacturer_id', $manufacturer->toArray())->select('id')->get();
    if(!$get_models || !count($get_models)) {
      $this->info('No Models');
      return;
    }

    $models = $get_models->pluck('id');*/

    $get_devices = DB::table('assets')->whereNull('deleted_at')->select('id')->get();
    // $get_devices = DB::table('assets')->whereIn('model_id', $models->toArray())->whereNull('deleted_at')->select('id')->get();

    // For all devices update warranty
    // $get_devices = DB::table('assets')->whereNull('deleted_at')->select('id')->get();

    if(! $get_devices) {
      $this->info('No Devices');
      return;
    }

    $handlable_units = $get_devices->split(20);

    foreach($handlable_units as $unit) {
      $device_ids = $unit->pluck('id');

      $devices = Device::whereIn('id', $device_ids->toArray())->get();
      foreach($devices as $device) {

        // if(!$device->manufacturer_warranty_data) {
        //   $device->getLiveWarrentyInfo();
        // }
        
        $device->warranty_status = $device->updateWarrantyStatus();
        $device->calc_warranty_expire_date = $device->updateWarrantyExpireDate();
        $device->timestamps = false;
        $device->save();
      }
    }

    $this->info('Total Updates: ' . $get_devices->count());
  }
}
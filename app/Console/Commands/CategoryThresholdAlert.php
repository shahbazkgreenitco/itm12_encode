<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ThresholdSettings;
use App\Mail\Asset\CategoryThresholdNotification;
use App\Imports\DeviceImportStore;
use App\Models\Category;
use App\Models\Threshold;
use App\Models\Consumable;
use App\Models\Accessory;
use App\Models\License;
use App\Models\Component;
use App\Models\Model;
use App\Helpers\Common as CommonHelper;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Mail;
use DB;
use Log;

class CategoryThresholdAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CategoryThresholdAlert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'to send reminder for category threshold for accessories, consumables, licenses, devices and components';

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
        $thresholdSettings = ThresholdSettings::first();

        if (empty($thresholdSettings) || ($thresholdSettings->threshold_enabled === null) || ($thresholdSettings->alerts_enabled === null)) {
            return;
        }
        $data = $alertmail = [];
        $assetDetail = Category::select('categories.id as category_id','categories.name','categories.category_type','t.threshold')
            ->leftJoin('thresholds as t', 't.cat_id', 'categories.id')
            ->where('category_type', 'asset')
            ->where('t.alerts_enabled', 1)
            ->where('t.threshold', '>', 0)
            ->get();

        $consumableDetail = Category::select('categories.id as category_id','categories.name','categories.category_type','t.threshold')
            ->leftJoin('thresholds as t', 't.cat_id', 'categories.id')
            ->where('category_type', 'consumable')
            ->where('t.alerts_enabled', 1)
            ->where('t.threshold', '>', 0)
            ->get();
  
        $accessoryDetail = Category::select('categories.id as category_id','categories.name','categories.category_type','t.threshold')
            ->leftJoin('thresholds as t', 't.cat_id', 'categories.id')
            ->where('category_type', 'accessory')
            ->where('t.alerts_enabled', 1)
            ->where('t.threshold', '>', 0)
            ->get();

        $licenseDetail = Category::select('categories.id as category_id','categories.name','categories.category_type','t.threshold')
            ->leftJoin('thresholds as t', 't.cat_id', 'categories.id')
            ->where('category_type', 'license')
            ->where('t.alerts_enabled', 1)
            ->where('t.threshold', '>', 0)
            ->get();

        $componentDetail = Category::select('categories.id as category_id','categories.name','categories.category_type','t.threshold')
            ->leftJoin('thresholds as t', 't.cat_id', 'categories.id')
            ->where('category_type', 'component')
            ->where('t.alerts_enabled', 1)
            ->where('t.threshold', '>', 0)
            ->get();

        foreach($consumableDetail as $c){
            $consumableInfo = Consumable::select('name','unique_tag','consumable_thresholds')->where('category_id', $c->category_id)->get();
            $total_consumable = Consumable::getCatConsumableTotal($c->category_id)[0]->total_consumables;
            $total_checkouts_consumables = Consumable::getCheckoutConsumableTotalByCat($c->category_id)[0]->total_checkouts;
            $availableCatConsumables = $total_consumable - $total_checkouts_consumables;

            if ($availableCatConsumables <= $c->threshold) {
                if($thresholdSettings->send_alerts == 0){
                    $alertmail = array_merge($alertmail, CommonHelper::getGlobalAlertEmail());
                }else{
                    $alertmail[] = $thresholdSettings->email;
                }
                $consumableInfo = !empty($consumableInfo) ? $consumableInfo : [];
                if(!empty($consumableInfo)){
                    foreach ($consumableInfo as $info) {
                        $data[] = [
                            "Category Name"   => $c->name,
                            "Category Type"   => $c->category_type,
                            "Item Name"       => $info->name ?? '',
                            "Item Tag"        => $info->unique_tag ?? '',
                            "Available Qty"   => $availableCatConsumables ?: '0',
                            "Threshold Qty"   => $c->threshold,
                            "Available Item Qty"  => $info->consumable_thresholds ?: '0',
                        ];
                    }
                }
            }
        }

        foreach($accessoryDetail as $acc){
            $accessoryInfo = Accessory::select('name','batch_no','accessory_thresholds')->where('category_id', $acc->category_id)->get();
            $total_acc = Accessory::getCatAccessoriesTotal($acc->category_id)[0]->total_accessories;
            $total_chkout_acc = Accessory::getCheckoutAccessoriesTotalByCat($acc->category_id) [0]->total_checkouts;
            $availableCatAccessory = $total_acc - $total_chkout_acc;
            if ($availableCatAccessory <= $acc->threshold) {
                if($thresholdSettings->send_alerts == 0){
                    $alertmail = array_merge($alertmail, CommonHelper::getGlobalAlertEmail());
                }else{
                    $alertmail[] = $thresholdSettings->email;
                }
                   
                $accessoryInfo = !empty($accessoryInfo) ? $accessoryInfo : [];
                if(!empty($accessoryInfo)){
                    foreach ($accessoryInfo as $info) {
                        $data[] = [
                            "Category Name"   => $acc->name,
                            "Category Type"   => $acc->category_type,
                            "Item Name"       => $info->name ?? '',
                            "Item Tag"        => $info->batch_no ?? '',
                            "Available Qty"   => $availableCatAccessory ?: '0',
                            "Threshold Qty"   => $acc->threshold,
                            "Available Item Qty"  => $info->accessory_thresholds ?: '0',
                        ];
                    }
                }
            }
        }

        foreach($licenseDetail as $lic){
            $licenceInfo = License::select('name','id',DB::raw("CONCAT('LIC', id) as batch_no"))->where('category_id', $lic->category_id)->get();
            $availableCatLicenses = License::getavailableLicense($lic->category_id)[0]->tot_available;
            if ($availableCatLicenses <= $lic->threshold) {
                if ($thresholdSettings->send_alerts == 0){
                    $alertmail = array_merge($alertmail,CommonHelper::getGlobalAlertEmail());
                } else{
                    $alertmail[] = $thresholdSettings->email;
                }   
                $licenceInfo = !empty($licenceInfo) ? $licenceInfo : [];
                if (!empty($licenceInfo)) {
                    foreach ($licenceInfo as $info) {
                        $data[] = [
                            "Category Name"   => $lic->name,
                            "Category Type"   => $lic->category_type,
                            "Item Name"       => $info->name ?? '',
                            "Item Tag"        => $info->batch_no ?? '',
                            "Available Qty"   => $availableCatLicenses ?: '0',
                            "Threshold Qty"   => $lic->threshold,
                        ];
                    }
                }
            }
        }

        foreach($componentDetail as $comp){
            $componentInfo = Component::select('name','unique_tag')->where('category_id', $comp->category_id)->get();
            $availableComponent = Component::getCheckoutComponentTotalByCat($comp->category_id)[0]->total_checkouts;
            if ($availableComponent <= $comp->threshold) {
                if($thresholdSettings->send_alerts == 0){
                    $alertmail = array_merge($alertmail,CommonHelper::getGlobalAlertEmail());
                }else{
                    $alertmail[] = $thresholdSettings->email;
                }
                  
                $componentInfo = !empty($componentInfo) ? $componentInfo : [];
                if(!empty($componentInfo)){
                    foreach ($componentInfo as $info) {
                        $data[] = [
                            "Category Name"   => $comp->name,
                            "Category Type"   => $comp->category_type,
                            "Item Name"       => $info->name ?? '',
                            "Item Tag"        => $info->unique_tag ?? '',
                            "Available Qty"   => $availableComponent ?: '0',
                            "Threshold Qty"   => $comp->threshold,
                        ];
                    }
                }

            }
        }
        
        foreach($assetDetail as $dev) {              
            $db  = DB::table('categories as cat');
            $db->leftJoin('models as mdl', 'cat.id', '=', 'mdl.category_id');
            $db->leftJoin('assets as device', 'mdl.id', '=', 'device.model_id');
            $db->join('status_labels as lbl', function($q) {
                $q->on('lbl.id', '=', 'device.status_id');
                $q->where('lbl.deployable', '=', 1);
                $q->where('lbl.archived', '=', 0);
            });
            $db->addSelect(DB::raw('count(device.id) as device_count'));
            $db->Select('device.name','device.asset_tag','cat.name as cat_name');
            $db->whereNull("device.assigned_to");
            $db->whereNull("device.deleted_at");
            if(!empty($dev->category_id)){
               $db->where('cat.id','=',$dev->category_id);
            }
            $deployableCatDeviceCount = $db->count();
            $devicedata = $db->get()->toArray();
            if ($deployableCatDeviceCount <= $dev->threshold) {
                if($thresholdSettings->send_alerts == 0){
                    $alertmail = array_merge($alertmail,CommonHelper::getGlobalAlertEmail());
                }else{
                    $alertmail[] = $thresholdSettings->email;
                }
        
                $devicedata = !empty($devicedata) ? $devicedata : [];
                if(!empty($devicedata)){
                    foreach ($devicedata as $info) {
                        $data[] = [
                            "Category Name"   => $dev->name,
                            "Category Type"   => $dev->category_type,
                            "Item Name"       => $info->name ?? '',
                            "Item Tag"        => $info->asset_tag ?? '',
                            "Available Qty"   => $deployableCatDeviceCount ?: '0',
                            "Threshold Qty"   => $dev->threshold,
                        ];
                    }
                }
            }
        }            
        $tot = count($data);
        $now = new Carbon(config('app.timezone'));
        if(!empty($data)){
            $file_name = 'CategoryThreshold_' . $now->format('dmY')."_".'.xlsx';
            $keys = array("Category Name","Category Type","Item Name","Item Tag","Category Available Qty","Category Threshold","Available Item Qty");
            $doc_path = Excel::store(new DeviceImportStore($data, $keys), $file_name, 'category_threshold');
            $uniqueAlertMail = array_unique($alertmail);
            Mail::to($uniqueAlertMail)->queue(new CategoryThresholdNotification($tot, $file_name));
            $this->info('alert send');
        }
    }
}
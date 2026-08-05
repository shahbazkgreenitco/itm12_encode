<?php

namespace App\Console\Commands;

use App\Models\Accessory;
use App\Models\AccessoryPurchase;
use App\Models\Consumable;
use App\Models\ConsumablePurchase;
use App\Models\License;
use App\Models\Settings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class AddPurchaseItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add_purchase_items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract base64 images, save to folder, and update content with cid format';

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
        $modules = ['accessory', 'consumable'];
        /*accessory_purchases
        consumable_purchases
        license_purchases*/
        $appSettings = Settings::first();
        foreach($modules as $module) {
            switch($module) {
                case "accessory":
                    $accessoryObj = Accessory::select('*')->get();
                    foreach ($accessoryObj as $accessory) {
                        if ($accessory->unique_tag == "") {
                            $accessory->timestamps = false;
                            $accessory->generateUniqueTag();
                            $accessory->save();
                            $accessoryPurchase = new AccessoryPurchase();
                            $accessory->timestamps = false;
                            $accessoryPurchase->batch_no = $accessory->id;
                            $accessoryPurchase->po_no = $accessory->order_number != null ? $accessory->order_number : null;
                            $accessoryPurchase->purchase_date = $accessory->purchase_date != null ? $accessory->purchase_date : $accessory->created_at;
                            $accessoryPurchase->exp_date = $accessory->purchase_date != null ? $accessory->purchase_date : null;
                            $accessoryPurchase->currency  = $accessory->currency != null ? $accessory->currency : $appSettings->default_currency;
                            $accessoryPurchase->purchase_price = $accessory->purchase_cost != null ? $accessory->purchase_cost : 0.00;
                            $accessoryPurchase->qty = $accessory->qty != null ? $accessory->qty : 0;
                            $accessoryPurchase->purchase_by = $accessory->supplier_id != null ? $accessory->supplier_id : null;
                            $accessoryPurchase->created_at = $accessory->created_at;
                            $accessoryPurchase->updated_at = $accessory->updated_at;
                            $accessoryPurchase->save();
                        }
                    }
                    break;
                case "consumable":
                    $consumableObj = Consumable::select('*')->get();
                    foreach ($consumableObj as $consumable) {
                        if ($consumable->unique_tag == "") {
                            $consumable->generateUniqueTag();
                            $consumable->timestamps = false;
                            $consumable->save();
                            $consumablePurchase = new ConsumablePurchase();
                            $consumablePurchase->batch_no = $consumable->id;
                            $consumablePurchase->po_no = $consumable->order_number != null ? $consumable->order_number : null;
                            $consumablePurchase->purchase_date = $consumable->purchase_date != null ? $consumable->purchase_date : $consumable->created_at;
                            $consumablePurchase->exp_date = $consumable->purchase_date != null ? $consumable->purchase_date : null;
                            $consumablePurchase->currency  = $consumable->currency != null ? $consumable->currency : $appSettings->default_currency;
                            $consumablePurchase->purchase_price = $consumable->purchase_cost != null ? $consumable->purchase_cost : 0.00;
                            $consumablePurchase->qty = $consumable->qty != null ? $consumable->qty : 0;
                            $consumablePurchase->purchase_by = $consumable->supplier_id != null ? $consumable->supplier_id : null;
                            $consumablePurchase->created_at = $consumable->created_at;
                            $consumablePurchase->updated_at = $consumable->updated_at;
                            $consumablePurchase->save();
                        }
                    }
                    break;
                case "license":
                    /*$licenseObj = License::select('*')->get();
                    foreach ($licenseObj as $license) {
                        if ($license->unique_tag == "") {
                            $license->generateUniqueTag();
                            $license->timestamps = false;
                            $license->save();
                        }
                    }*/
                    break;
                default:
                    break;
            }
            $this->info($module.": success");
        }
        $this->info("success");
    }
}

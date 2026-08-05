<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ThresholdSettings;
use App\Mail\AccessoryThreshouldNotification;
use App\Mail\Asset\CategoryThresholdNotification;
use App\Imports\DeviceImportStore;
use App\Models\Accessory;
use App\Models\Category;
use App\Models\Threshold;
use App\Helpers\Common as CommonHelper;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Log;
use Mail;
use Carbon\Carbon;

class AccessoryThresholdSendAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:accessoryThresholdSendAlerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Send the threshold alerts for each Accessory';

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
        if(config('mail.service_enabled') != 1) {
            return;
        }
        $thresholdSettings = ThresholdSettings::first();
        if (empty($thresholdSettings) || ($thresholdSettings->threshold_enabled === null) || ($thresholdSettings->alerts_enabled === null)) {
            return;
        }
        $categoryGroupedAlerts = [];
        $accessoryCategories = Category::where('category_type', 'accessory')->get();
        foreach ($accessoryCategories as $category) {
            $accessory = Accessory::leftJoin('threshold_alert_settings as t', function ($join) {
                $join->on('t.asset_id', '=', 'accessories.id')
                    ->where('t.asset_type', '=', 2);
                })->leftJoin('users', 'users.id', '=', 't.user_id')
                ->select('accessories.category_id as cat_id','accessories.id','accessories.name','accessories.qty','accessories.batch_no',
                    'accessories.accessory_thresholds',DB::raw('GROUP_CONCAT(DISTINCT users.email ORDER BY users.email SEPARATOR ", ") as user_emails')
                )->groupBy('accessories.id','accessories.name','accessories.qty','accessories.accessory_thresholds','accessories.category_id','accessories.batch_no'
                )->where('accessories.category_id', $category->id)->get();

            foreach($accessory as $acc){
                $accessoryThreshold = $acc->accessory_thresholds ?? 0;
                $accessoryQuantity = $acc->qty ?? 0;
                $totalCheckoutAccessory = Accessory::getCheckoutAccTotalById($acc->id)[0]->total_checkouts;
                $availableAccessory = $accessoryQuantity - $totalCheckoutAccessory;
                $categoryThreshold = Threshold::where('cat_id', $acc->cat_id)->first();
                $alertmail = [];
                if (!empty($acc->user_emails)) {
                    $emails = explode(', ', $acc->user_emails);
                    $alertmail = array_merge($alertmail,$emails);
                }
                $alertmail = array_unique($alertmail);

                if (!empty($alertmail) && ($availableAccessory <= $accessoryThreshold) && $accessoryThreshold > 0) {
                    Mail::to($alertmail)->queue(new AccessoryThreshouldNotification($acc, $accessoryThreshold, $availableAccessory ,$accessoryQuantity));
                    $this->info("Accessory: '{$acc->name}' threshold alert sent to: " . json_encode($alertmail));
                }elseif (!empty($categoryThreshold) &&
                    ($availableAccessory <= $categoryThreshold->threshold) &&
                    ($categoryThreshold->threshold > 0) &&
                    ($categoryThreshold->alerts_enabled == 1)
                ) {
                    $this->info("category wise:" . $acc->name);
                    $categoryGroupedAlerts[] = [
                        'category'   => $category->name,
                        'category_id' => $category->id,
                        'item_name'  => $acc->name,
                        'batch_no'   => $acc->batch_no,
                        'available'  => $availableAccessory,
                        'threshold'  => $categoryThreshold->threshold,
                        'total'      => $acc->qty,
                    ];
                }
            }
        }

        if (!empty($categoryGroupedAlerts)) {
            if ($thresholdSettings->send_alerts == 0) {
                // send_alerts = 0 = Global alert setting fetch
                $categoryAlertsUsers = array_merge($categoryAlertsUsers, CommonHelper::getGlobalAlertEmail());
            } else {
                // send_alerts = 1 = Entered email alert
                $categoryAlertsUsers = array_merge($categoryAlertsUsers,  explode(',', $thresholdSettings->email));
            }
            $data = [];
            foreach ($categoryGroupedAlerts as $item) {
                $data[] = [
                    'Category Name'          => $item['category'],
                    'Item Name'              => $item['item_name'],
                    'Item Tag'               => $item['batch_no'],
                    'Category Available Qty' => $item['total'],
                    'Category Threshold Qty' => $item['threshold'],
                    'Available Item Qty'     => $item['available'],
                ];
            }

            $tot = count($data);
            $now = new Carbon(config('app.timezone'));

            if (!empty($data)) {
                $file_name = 'CategoryThreshold_' . $now->format('dmY_His') . '_' .'.xlsx';
                $keys = ["Category Name","Name","Batch No","Category Available Qty","Category Threshold Qty","Available Item Qty"
                ];
                Excel::store(new DeviceImportStore($data, $keys), $file_name, 'category_threshold');
                Mail::to($categoryAlertsUsers)->queue(new CategoryThresholdNotification($tot, $file_name));
                $this->info("Alert sent for Categoryto " . implode(', ', $categoryAlertsUsers));
                $categoryIds = collect($categoryGroupedAlerts)->pluck('category_id')->unique();
                foreach ($categoryIds as $cat) {
                    $threshold = Threshold::where('cat_id', $cat)->first();
                    if ($threshold) {
                        $threshold->notify_count = $threshold->notify_count + 1;
                        $threshold->last_notified_date = now();
                        $threshold->save();
                    }
                }
            }
        }
    }
}
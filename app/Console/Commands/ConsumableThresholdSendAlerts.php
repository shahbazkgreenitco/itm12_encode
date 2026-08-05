<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ThresholdSettings;
use App\Models\ThresholdAlertSettings;
use App\Mail\Consumables\ConsumableThreshouldNotification;
use App\Mail\Asset\CategoryThresholdNotification;
use App\Mail\Asset\ThresholdNotification;
use App\Imports\DeviceImportStore;
use App\Models\Consumable;
use App\Models\Category;
use App\Models\Threshold;
use App\Helpers\Common as CommonHelper;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Log;
use Mail;
use Carbon\Carbon;


class ConsumableThresholdSendAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:consumableThresholdAlerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Send the threshold alerts for each consumable';

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
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        //  individual via mail send
        $individualUserIdwithConsumable = ThresholdAlertSettings::join('consumables as cons', 'threshold_alert_settings.asset_id', 'cons.id')
        ->where('threshold_alert_settings.asset_type', 4)->whereNotNull('cons.category_id')->whereNull('cons.deleted_at')
        ->select('threshold_alert_settings.user_id', DB::raw('GROUP_CONCAT(DISTINCT threshold_alert_settings.asset_id ORDER BY threshold_alert_settings.asset_id) as consumable_id'))
        ->groupBy('threshold_alert_settings.user_id')
        ->get();

        if (count($individualUserIdwithConsumable) > 0) {
            foreach ($individualUserIdwithConsumable as $key => $value) {
                $userIndividual = User::where('users.activated', 1)->whereNull('users.deleted_at')->whereNotNull('users.email')->where('users.id', $value->user_id)->select('users.username','users.email')->first();
                if(isset($userIndividual) && !empty($userIndividual)) {
                    if (isset($value->consumable_id) && !empty($value->consumable_id)) {
                        $consumableIds = explode(',', $value->consumable_id);
                        $consumables = Consumable::leftJoin('categories as cat', 'cat.id', 'consumables.category_id')
                        ->leftJoin('consumables_users as cu', 'cu.consumable_id', 'consumables.id')
                        ->whereIn('consumables.id', $consumableIds)
                        ->whereNull('consumables.deleted_at')
                        ->select(
                            'consumables.name',
                            'consumables.qty',
                            'consumables.unique_tag',
                            'consumables.consumable_thresholds',
                            'consumables.scrap_qty',
                            'cat.name as category_name',
                            DB::raw('COUNT(cu.consumable_id) as checkout_count'),
                            'cat.id as category_id',
                            'consumables.department_id',
                            'consumables.location_id',
                        )
                        ->groupBy(
                            'consumables.id',
                            'consumables.name',
                            'consumables.qty',
                            'consumables.unique_tag',
                            'consumables.scrap_qty',
                            'consumables.consumable_thresholds',
                            'cat.name',
                            'cat.id',
                            'consumables.department_id',
                            'consumables.location_id',
                        )
                        ->get();
                        $individualWiseThreshHoldAlerts = []; 
                        foreach ($consumables as $key => $v) {
                            $available = $v->qty - ($v->checkout_count + $v->scrap_qty ?? 0);
                            if ($available <= $v->consumable_thresholds) {
                                $individualWiseThreshHoldAlerts[] = [
                                    'Item Name' => $v->name,
                                    'Item Tag' => $v->unique_tag,
                                    'Department' => $v->department->name ?? '',
                                    'Location' => $v->location->name ?? '',
                                    'Category Name' => $v->category_name,
                                    'Available Qty' => $available,
                                    'Issued Qty' => $v->qty - $available,
                                    'Qty' => $v->qty,
                                    'Threshold Qty' => $v->consumable_thresholds,
                                ];
                            }
                        }
                        if (!empty($individualWiseThreshHoldAlerts)) {
                            $tot = count($individualWiseThreshHoldAlerts);
                            $now = new Carbon(config('app.timezone'));
                            if (!empty($individualWiseThreshHoldAlerts)) {
                                $file_name = 'ConsumableThreshold_' . $now->format('dmY_His') . '_' .'.xlsx';
                                $keys = ["Item Name","Item Tag","Department","Location","Category Name","Available Qty","Issued Qty","Qty","Threshold Qty"];
                                Excel::store(new DeviceImportStore($individualWiseThreshHoldAlerts, $keys), $file_name, 'threshold');
                                Mail::to($userIndividual->email)->queue(new ThresholdNotification($tot, $file_name,'Consumables'));
                            }
                            $individualWiseThreshHoldAlerts = [];
                        }
                    }
                }
            }
        }

        // category view send mail
        $consumableCategories = Category::leftJoin('thresholds as th', 'th.cat_id', 'categories.id')->where('categories.category_type', 'consumable')->where('th.alerts_enabled', 1)->get();
        if ($consumableCategories->isNotEmpty()) {
            $categoryWiseThreshHoldAlerts = $categeoryId = [];
            foreach ($consumableCategories as $category) {
                $categorySummary = Consumable::leftJoin(
                    'consumables_users as cu',
                    'cu.consumable_id',
                    'consumables.id'
                )
                ->where('consumables.category_id', $category->cat_id)
                ->whereNull('consumables.deleted_at')
                ->select(
                    'consumables.category_id',
                    DB::raw('SUM(consumables.qty) as total_qty'),
                    DB::raw('SUM(consumables.scrap_qty) as total_scrap_qty'),
                    DB::raw('COUNT(cu.consumable_id) as checkout_count'),
                    DB::raw('GROUP_CONCAT(consumables.id ORDER BY consumables.id SEPARATOR ", ") as consumable_id'),
                    // DB::raw('GROUP_CONCAT(consumables.name ORDER BY consumables.name SEPARATOR ", ") as consumable_names'),
                    // DB::raw('GROUP_CONCAT(consumables.unique_tag ORDER BY consumables.unique_tag SEPARATOR ", ") as consumable_tags')
                )
                ->groupBy('consumables.category_id')
                ->first();
                if(isset($categorySummary)){
                    $categorySummaryAvailable = $categorySummary->total_qty - ($categorySummary->checkout_count + $categorySummary->scrap_qty ?? 0);
                    if ($categorySummaryAvailable <= $category->threshold) {
                        $consumableIds = explode(',', $categorySummary->consumable_id);
                        $consumables = Consumable::leftJoin('categories as cat', 'cat.id', 'consumables.category_id')
                        ->leftJoin('consumables_users as cu', 'cu.consumable_id', 'consumables.id')
                        ->whereIn('consumables.id', $consumableIds)
                        ->whereNull('consumables.deleted_at')
                        ->select(
                            'consumables.name',
                            'consumables.qty',
                            'consumables.unique_tag',
                            'consumables.consumable_thresholds',
                            'consumables.scrap_qty',
                            'cat.name as category_name',
                            DB::raw('COUNT(cu.consumable_id) as checkout_count'),
                            'cat.id as category_id'
                        )
                        ->groupBy(
                            'consumables.id',
                            'consumables.name',
                            'consumables.qty',
                            'consumables.unique_tag',
                            'consumables.scrap_qty',
                            'consumables.consumable_thresholds',
                            'cat.name',
                            'cat.id'
                        )
                        ->get();
                        foreach ($consumables as $key => $v) {
                            $available = $v->qty - ($v->checkout_count + $v->scrap_qty ?? 0);
                            if ($available < $v->qty ) {
                                $categoryWiseThreshHoldAlerts[] = [
                                    'Category Name' => $v->category_name,
                                    'Item Name' => $v->name,
                                    'Item Tag' => $v->unique_tag,
                                    'Item Available Qty' => $available,
                                    'Category Threshold Qty' => $category->threshold,
                                    'Item Qty' => $v->qty,
                                ];
                            }
                        }
                        $categeoryId [] = $category->cat_id;
                    }
                }
            }

            if (!empty($categoryWiseThreshHoldAlerts)) {
                // Threshold main setting
                $categoryAlertsUsers = [];
                if ($thresholdSettings->send_alerts == 0) {
                    // send_alerts = 0 = Global alert setting fetch
                    $categoryAlertsUsers = array_merge($categoryAlertsUsers, CommonHelper::getGlobalAlertEmail());
                } else {
                    // send_alerts = 1 = Entered email alert
                    $categoryAlertsUsers = array_merge($categoryAlertsUsers,  explode(',', $thresholdSettings->email));
                }
                $tot = count($categoryWiseThreshHoldAlerts);
                $now = new Carbon(config('app.timezone'));

                if (!empty($categoryWiseThreshHoldAlerts)) {
                    $file_name = 'CategoryThreshold_' . $now->format('dmY_His') . '_' .'.xlsx';
                    $keys = ["Category Name","Item Name","Item Tag","Item Available Qty","Category Threshold Qty","Item Qty"];
                    Excel::store(new DeviceImportStore($categoryWiseThreshHoldAlerts, $keys), $file_name, 'category_threshold');
                    Mail::to($categoryAlertsUsers)->queue(new CategoryThresholdNotification($tot, $file_name));
                    $this->info("threshold alert sent to: " . json_encode($categoryAlertsUsers));
                    // Update notify_count for each category involved
                    foreach ($categeoryId as $cat) {
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
}
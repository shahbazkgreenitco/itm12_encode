<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\ConsumablePurchase;
use App\Models\Settings;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Log;
use App\Exports\ExpiringConsumablesExport;
use App\Mail\Asset\ExpiringConsumablesMail;
use App\Helpers\Common as CommonHelper;

class ExpiringConsumablesPurchaseCron extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'expiring-consumables-purchase';

    /**
     * The console command description.
     */
    protected $description = 'Send expired and expiring consumables purchase report via mail';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $next7Days = Carbon::today()->addDays(7);

        $db = ConsumablePurchase::query()
            ->leftJoin('consumables as a', 'a.id', '=', 'consumable_purchases.batch_no')
            ->leftJoin('categories as cat', 'cat.id', '=', 'a.category_id')
            ->leftJoin('departments as dep', 'dep.id', '=', 'a.department_id')
            ->leftJoin('locations as loc', 'loc.id', '=', 'a.location_id')
            ->leftJoin('suppliers as sup', 'sup.id', '=', 'consumable_purchases.purchase_by')
            ->leftJoin('manufacturers as manu', 'manu.id', '=', 'a.manufacturer_id')
            ->whereNotNull('consumable_purchases.exp_date')
            ->where(function ($q) use ($today, $next7Days) {
                $q->whereDate('consumable_purchases.exp_date', '<', $today)
                ->orWhereBetween('consumable_purchases.exp_date', [$today, $next7Days]);
            });

        $db->select(DB::raw(' CASE WHEN a.id IS NOT NULL THEN CONCAT_WS("", "' . (config("app.client") == "etherealmachines" ? 'CN' : 'CNS') . '", a.id) ELSE "" END as batch '));
        $db->addSelect( 'a.unique_tag','a.name as consu_name', 'cat.name as category', 'manu.name as manufacturer', 'dep.name as department', 'loc.name as location', 'consumable_purchases.qty as qty','sup.name as purchase_from', 'consumable_purchases.po_no as po_no',
            DB::raw('DATE_FORMAT(consumable_purchases.purchase_date, "%d %b %Y") as purchase_date'),
            DB::raw('DATE_FORMAT(consumable_purchases.received_date, "%d %b %Y") as received_date'),
            DB::raw('DATE_FORMAT(consumable_purchases.exp_date, "%d %b %Y") as expire_date'),
            DB::raw('DATE_FORMAT(consumable_purchases.updated_at, "%d %b %Y %h:%i %p") as updated_at_formatted')
        );

        $data = $db->get();

        if ($data->isEmpty()) {
            return 0;
        }

        $settings = Settings::getSettings();
        // if (config('mail.service_enabled') == 1 && $settings->alerts_enabled == 1) {

            $alertNotify = CommonHelper::getGlobalAlertEmail();

            $alertNotify = array_filter($alertNotify, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
            // if (!empty($alertNotify)) {
                $toEmail  = array_shift($alertNotify);
                $ccEmails = $alertNotify;
                try {
                    $fileName = 'expiring_consumables_' . now()->format('dmY_His') . '_' . rand(1000,9999) . '.xlsx';
                    $filePath = 'uploads/' . $fileName;
                    Excel::store(new ExpiringConsumablesExport($data), $filePath);
                    //Mail::to($toEmail)->cc($ccEmails)->send(new ExpiringConsumablesMail($fileName,'uploads',count($data)));
                    Mail::to('sorenp@greenitco.com')->send(new ExpiringConsumablesMail($fileName,'uploads',count($data)));
                    @unlink(storage_path('app/' . $filePath));

                } catch (\Exception $e) {
                    Log::error("Expiring Consumables Purchase Cron Error: " . $e->getMessage());
                }
            // }
        // }

        return 0;
    }
}
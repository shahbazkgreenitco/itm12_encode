<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Mail;
use Carbon\Carbon;
use App\Mail\ContractAgreementLastThreeDaysMail;
use App\Models\Settings;
use App\Models\Lease;
use Excel;
use App\Helpers\Common as CommonHelper;
use PhpOffice\PhpSpreadsheet\Shared\Date as OfficeDate;
use Log;

class ContractAgreementLastThreeDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contractAgreementExpireReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send the contract agreement expire list to client on periodically before 3 days';

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
        $settings = Settings::getSettings();
        $globalAlertEmails = CommonHelper::getGlobalAlertEmail();
        if (!config('mail.service_enabled') ||!$settings->alerts_enabled ||empty($globalAlertEmails) ||!array_filter($globalAlertEmails, function($email) {return filter_var($email, FILTER_VALIDATE_EMAIL);})
        ){
            return;
        }

        $start = new Carbon(config('app.timezone'));
        $end = (new Carbon(config('app.timezone')))->addDays(3);
    
        $contract = Lease::where('end_date', '=', $end->format('Y-m-d'))->get();

        $alertnotify = [];
        if(Settings::first()->alerts_enabled == 1) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        try {
            if(count($contract) > 0 && config('mail.service_enabled') &&  filter_var($alertnotify, FILTER_VALIDATE_EMAIL)) {
                Mail::to($globalAlertEmails)->send(new ContractAgreementLastThreeDaysMail($start, $end, $contract));
            }
        } catch (\Exception $ex) {
            Log::error("contractAgreementExpireReminder Error: " . $ex->getMessage());
        }
        // $this->info('Total : ' .  $contract->count());
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Traits\OutlookTrait;
use Illuminate\Console\Command;

class SubscribedLicesnce extends Command
{
    use OutlookTrait; 

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscribedLicence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get list of subscribed licences';

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
        try{
            $subscribedLicence = $this->getSubscribedLicence();
            dd($subscribedLicence);
            return true;
        }catch(\Exception $e){
            return false;
        }
    }
}

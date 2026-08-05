<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Rdp\Meshcentral;
use Illuminate\Support\Facades\Log;


class MongoDbConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongoDbConnect';

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
        $devices = Meshcentral::where('type', 'sysinfo')->where('doc', 'like', '%board_serial": "2UA3511Z64%')->get();
        foreach($devices as $devices) {
            $this->info(json_encode($devices));
            $this->info('connected success');
        }
    }
}

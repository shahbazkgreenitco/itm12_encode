<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NetworkInventory\ScanRegister;
use Carbon\Carbon;
class DetectedHostRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detected_host_remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove data from detected host before 1 week';

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
        $hosts = ScanRegister::where('created_at', '<', Carbon::now()->subWeek())->get();
        foreach ($hosts as $post) {
            $post->delete();
        }
    }
}

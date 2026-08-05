<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DisposeAssetsDelete;

class DisposeAssetsDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:disposeAsset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'permanently remove disposed assets from the database';

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
        dispatch(new DisposeAssetsDelete());
        $this->info('DisposeAssetsDelete job dispatched successfully.');
    }
}
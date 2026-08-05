<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use DB;

class FixInvalidTicketAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-invalid-ticket-assignments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invalid ticket assignments';

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
        try {
            $updated = \DB::table('tkt_tickets as t')
            ->leftJoin('tkt_user_privileges as tp', function ($join) {
                $join->on('tp.user_id', '=', 't.assigned_to')
                    ->on('tp.department_id', '=', 't.department_id');
            })
            ->whereNull('t.deleted_at')
            ->whereNotIn('t.status_id', [5, 6])
            ->whereNotNull('t.assigned_to')
            ->where('t.assigned_to', '<>', 0)
            ->whereNull('tp.id')
            ->where('t.created_at', '>=', '2025-10-06')
            ->update([
                't.assigned_to' => null,
                't.updated_at' => now()
            ]);
            \Log::info("Invalid ticket assignments fixed: " . $updated);

            $this->info("Updated rows: " . $updated);
            Log::info("FixInvalidTicketAssignments completed successfully.");
        } catch (\Exception $e) {
            \Log::error("FixInvalidTicketAssignments Error: " . $e->getMessage());
        }
        return 0;
    }
}

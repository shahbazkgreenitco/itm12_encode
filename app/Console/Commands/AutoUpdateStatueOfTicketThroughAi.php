<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket\Ticket;
use App\Models\TktFollowing;
use App\Helpers\Common as CommonHelper;
use Log;
use DB;


class AutoUpdateStatueOfTicketThroughAi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoUpdateStatueOfTicketThroughAi';

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

    public function extractLatestMessage($text)
    {
        $patterns = [
            '/\bfrom:/i',
            '/\bsent:/i',
            '/\bsubject:/i',
            '/-----original message-----/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
                return substr($text, 0, $matches[0][1]);
            }
        }

        return $text;
    }

    public function cleanMessage($text) {
        // Remove extra spaces
        $text = preg_replace('/\s+/', ' ', $text);
        // Trim
        return trim($text);
    }
    public function handle() {
        $getTickets = Ticket::select(
            'tkt_tickets.id',
            'tkt_tickets.assigned_to',
            'tf.remarks'
        )
        ->leftJoin(DB::raw('(
            SELECT ticket_id, remarks
            FROM tkt_followings tf1
            WHERE tf1.action_type = 7
            AND tf1.id = (
                SELECT MAX(tf2.id) 
                FROM tkt_followings tf2 
                WHERE tf2.ticket_id = tf1.ticket_id 
                AND tf2.action_type = 7
            )
        ) as tf'), 'tf.ticket_id', '=', 'tkt_tickets.id')
        ->whereNotIn('tkt_tickets.status_id', [5,6,10])
        ->whereNotNull('tkt_tickets.assigned_to')
        ->whereNotNull('remarks')
        ->whereNull(['tkt_tickets.is_temp', 'tkt_tickets.deleted_at', "tkt_tickets.merge_primary"])
        ->orderBy('tkt_tickets.id', 'desc')
        ->get();
        foreach($getTickets as $ticket) {
            if(isset($ticket->remarks) && $ticket->remarks != null){
                // Log::info("Processing Ticket ID: ".$ticket->id);
                $response = CommonHelper::getAIStatusPrediction($ticket->remarks, $ticket->id, $ticket->assigned_to);
                // Log::info("AutoUpdateStatueOfTicketThroughAi : ". json_encode($response));  
                sleep(10);
            }
        }
        return 0;
    }
}

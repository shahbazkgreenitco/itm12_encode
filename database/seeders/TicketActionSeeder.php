<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tkt_actions')->upsert([
            [
                'id' => 1,
                'display_name' => 'Ticket: Move to Category',
                'name' => 'move_ticket_to_category',
                'aborts_cycle' => 0,
                'updates_ticket' => 1,
                'input_config' => '{"inputs":[{"type":"select","select_options":"category:tags","name":"category_name"}]}',
                'status' => 0,
            ],
            [
                'id' => 2,
                'display_name' => 'Notify: via Email',
                'name' => 'send_email_to_user',
                'aborts_cycle' => 0,
                'updates_ticket' => 0,
                'input_config' => '{"inputs":[{"type":"select","name":"agent_id","default_value":"(current user)","select_options":"agent:id"},{"placeholder":"Subject","type":"text","name":"subject"},{"placeholder":"Email Message","type":"textarea","name":"message"}]}',
                'status' => 1,
            ],
            [
                'id' => 3,
                'display_name' => 'Ticket: Add a note',
                'name' => 'add_note_to_ticket',
                'aborts_cycle' => 0,
                'updates_ticket' => 1,
                'input_config' => '{"inputs":[{"type":"textarea","placeholder":"Note Text","name":"note_text"}]}',
                'status' => 1,
            ],
            [
                'id' => 4,
                'display_name' => 'Ticket: Change status',
                'name' => 'change_ticket_status',
                'aborts_cycle' => 0,
                'updates_ticket' => 1,
                'input_config' => '{"inputs":[{"type":"select","default_value":"open","select_options":"ticket:status","name":"status_name"}]}',
                'status' => 1,
            ],
            [
                'id' => 5,
                'display_name' => 'Ticket: Assign to Agent',
                'name' => 'assign_ticket_to_agent',
                'aborts_cycle' => 0,
                'updates_ticket' => 1,
                'input_config' => '{"inputs":[{"type":"select","default_value":"(current user)","select_options":"agent:id","name":"agent_id"}]}',
                'status' => 1,
            ],
            [
                'id' => 6,
                'display_name' => 'Ticket: Add tag(s)',
                'name' => 'add_tags_to_ticket',
                'aborts_cycle' => 0,
                'updates_ticket' => 1,
                'input_config' => '{"inputs":[{"type":"text","placeholder":"Separate tags with comma","name":"tags_to_add"}]}',
                'status' => 0,
            ],
            [
                'id' => 7,
                'display_name' => 'Ticket: Remove tag(s)',
                'name' => 'remove_tags_from_ticket',
                'aborts_cycle' => 0,
                'updates_ticket' => 1,
                'input_config' => '{"inputs":[{"type":"text","placeholder":"Separate tags with comma","name":"tags_to_remove"}]}',
                'status' => 0,
            ],
            [
                'id' => 8,
                'display_name' => 'Ticket: Delete',
                'name' => 'delete_ticket',
                'aborts_cycle' => 1,
                'updates_ticket' => 1,
                'input_config' => '{"inputs":[]}',
                'status' => 1,
            ],
            [
                'id' => 9,
                'display_name' => 'Ticket: Change Department',
                'name' => 'change_ticket_department',
                'aborts_cycle' => 0,
                'updates_ticket' => 1,
                'input_config' => '{"inputs":[{"type":"select","name":"department_id","default_value":"","select_options":"department:id"},{"placeholder":"Select ProblemCategory","type":"select","name":"problem_category_id","default_value":"","select_options":"problem_category:id"},{"placeholder":"Select SubCategory","type":"select","name":"sub_category_id","default_value":"","select_options":"sub_category:id"}]}',
                'status' => 1,
            ],
            [
                'id' => 10,
                'display_name' => 'Call An Api Definition',
                'name' => 'call_api_defination',
                'aborts_cycle' => 0,
                'updates_ticket' => 0,
                'input_config' => '{"inputs":[{"type":"select","default_value":"api_defination","select_options":"apidefination:id","name":"apidefination"}]}',
                'status' => 1,
            ],
            [
                'id' => 11,
                'display_name' => 'Ticket: Change Priority',
                'name' => 'change_ticket_priority',
                'aborts_cycle' => 0,
                'updates_ticket' => 1,
                'input_config' => '{"inputs":[{"type":"select","default_value":"critical","select_options":"ticket:priority","name":"priority_name"}]}',
                'status' => 1,
            ],
            [
                'id' => 12,
                'display_name' => 'Ticket: Escalate Ticket',
                'name' => 'escalate_ticket',
                'aborts_cycle' => 1,
                'updates_ticket' => 1,
                'input_config' => '{"inputs":[]}',
                'status' => 1,
            ],
        ], ['id']);
    }
}
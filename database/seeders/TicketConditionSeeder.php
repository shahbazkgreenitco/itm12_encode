<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tkt_conditions')->upsert([
            [
                'id' => 1,
                'name' => 'Ticket: Subject',
                'type' => 'ticket:subject',
                'status' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Ticket: Body',
                'type' => 'ticket:body',
                'status' => 1,
            ],
            [
                'id' => 3,
                'name' => 'Ticket: Status',
                'type' => 'ticket:status',
                'status' => 1,
            ],
            [
                'id' => 4,
                'name' => 'Ticket: Category',
                'type' => 'ticket:category',
                'status' => 1,
            ],
            [
                'id' => 5,
                'name' => 'Ticket: Number of Attachments',
                'type' => 'ticket:uploads',
                'status' => 1,
            ],
            [
                'id' => 6,
                'name' => 'Ticket: Assignee',
                'type' => 'ticket:assignee',
                'status' => 1,
            ],
            [
                'id' => 7,
                'name' => 'Customer: Name',
                'type' => 'customer:name',
                'status' => 1,
            ],
            [
                'id' => 8,
                'name' => 'Customer: Email',
                'type' => 'customer:email',
                'status' => 1,
            ],
            [
                'id' => 9,
                'name' => 'Ticket: Priority',
                'type' => 'ticket:priority',
                'status' => 1,
            ],
            [
                'id' => 10,
                'name' => 'Ticket: Department',
                'type' => 'ticket:department',
                'status' => 1,
            ],
            [
                'id' => 11,
                'name' => 'Ticket: SubCategory',
                'type' => 'ticket:subcategory',
                'status' => 1,
            ],
        ], ['id']);
    }
}

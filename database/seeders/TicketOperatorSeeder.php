<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketOperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tkt_operators')->upsert([
            [
                'id' => 1,
                'name' => 'contains',
                'display_name' => 'Contains',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 2,
                'name' => 'not_contains',
                'display_name' => 'Does not contain',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 3,
                'name' => 'starts_with',
                'display_name' => 'Starts with',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 4,
                'name' => 'ends_with',
                'display_name' => 'Ends with',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 5,
                'name' => 'equals',
                'display_name' => 'Equals',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 6,
                'name' => 'not_equals',
                'display_name' => 'Does not equal',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 7,
                'name' => 'matches_regex',
                'display_name' => 'Matches regex pattern',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 8,
                'name' => 'more',
                'display_name' => 'More then',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 9,
                'name' => 'less',
                'display_name' => 'Less then',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 10,
                'name' => 'is',
                'display_name' => 'Is',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 11,
                'name' => 'not',
                'display_name' => 'Is not',
                'type' => 'primitive',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 12,
                'name' => 'changed',
                'display_name' => 'Changed',
                'type' => 'mixed',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 13,
                'name' => 'changed_to',
                'display_name' => 'Changed to',
                'type' => 'mixed',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 14,
                'name' => 'changed_from',
                'display_name' => 'Changed from',
                'type' => 'mixed',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 15,
                'name' => 'not_changed',
                'display_name' => 'Not changed',
                'type' => 'mixed',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 16,
                'name' => 'not_changed_to',
                'display_name' => 'Not changed to',
                'type' => 'mixed',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
            [
                'id' => 17,
                'name' => 'not_changed_from',
                'display_name' => 'Not changed from',
                'type' => 'mixed',
                'value_type' => 'text',
                'value_placeholder' => null,
                'validation_rules' => null,
            ],
        ], ['id']);
    }
}
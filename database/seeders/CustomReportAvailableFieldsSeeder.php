<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomReportAvailableFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            [
                'id' => 1,
                'report_module' => 1,
                'field_name' => 'Custom Name',
                'possible_checks' => '1,6',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 2,
                'report_module' => 1,
                'field_name' => 'Device Tag',
                'possible_checks' => '1,6',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 3,
                'report_module' => 1,
                'field_name' => 'Serial Number',
                'possible_checks' => '1,6',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 4,
                'report_module' => 1,
                'field_name' => 'UUID',
                'possible_checks' => '1,6',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 5,
                'report_module' => 1,
                'field_name' => 'Order Number',
                'possible_checks' => '1,6',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 6,
                'report_module' => 1,
                'field_name' => 'Model',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 7,
                'report_module' => 1,
                'field_name' => 'Default Location',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 8,
                'report_module' => 1,
                'field_name' => 'Status',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 9,
                'report_module' => 1,
                'field_name' => 'Invoice Reference',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 10,
                'report_module' => 1,
                'field_name' => 'Warranty (In Months)',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":3}',
                'value_type' => 2
            ],
            [
                'id' => 11,
                'report_module' => 1,
                'field_name' => 'IP',
                'possible_checks' => '1,6',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 12,
                'report_module' => 1,
                'field_name' => 'MAC',
                'possible_checks' => '1,6',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 13,
                'report_module' => 1,
                'field_name' => 'Total Checkouts',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":3}',
                'value_type' => 2
            ],
            [
                'id' => 14,
                'report_module' => 1,
                'field_name' => 'Total Services',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":3}',
                'value_type' => 2
            ],
            [
                'id' => 15,
                'report_module' => 1,
                'field_name' => 'Total Licenses',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":3}',
                'value_type' => 2
            ],
            [
                'id' => 16,
                'report_module' => 1,
                'field_name' => 'Total Accessories',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":3}',
                'value_type' => 2
            ],
            [
                'id' => 17,
                'report_module' => 1,
                'field_name' => 'Manufacturer',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 18,
                'report_module' => 1,
                'field_name' => 'Category',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 19,
                'report_module' => 1,
                'field_name' => 'Account Type',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 20,
                'report_module' => 1,
                'field_name' => 'Lease Type',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 21,
                'report_module' => 1,
                'field_name' => 'Maintenance Incharge',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 22,
                'report_module' => 1,
                'field_name' => 'Lease - Start Date',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 23,
                'report_module' => 1,
                'field_name' => 'Lease - End Date',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 24,
                'report_module' => 1,
                'field_name' => 'Lease - Expire Status',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 25,
                'report_module' => 1,
                'field_name' => 'Requestable Device',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 26,
                'report_module' => 1,
                'field_name' => 'Device From',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 27,
                'report_module' => 1,
                'field_name' => 'Leaser Name',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 28,
                'report_module' => 1,
                'field_name' => 'High Priority Device',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 29,
                'report_module' => 1,
                'field_name' => 'Last Checkout Date',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 30,
                'report_module' => 1,
                'field_name' => 'Expected Checkin Date',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 31,
                'report_module' => 1,
                'field_name' => 'Checkout To',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 32,
                'report_module' => 1,
                'field_name' => 'Assigned To',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 33,
                'report_module' => 1,
                'field_name' => 'Department',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 34,
                'report_module' => 1,
                'field_name' => 'Purchase Supplier',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 35,
                'report_module' => 1,
                'field_name' => 'AMC Supplier',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 36,
                'report_module' => 1,
                'field_name' => 'AMC Expire Date',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 37,
                'report_module' => 1,
                'field_name' => 'AMC Service Status',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 38,
                'report_module' => 1,
                'field_name' => 'Purchase Date',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 39,
                'report_module' => 1,
                'field_name' => 'Device Age',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":3}',
                'value_type' => 2
            ],
            [
                'id' => 40,
                'report_module' => 1,
                'field_name' => 'Warranty Expire Date',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 41,
                'report_module' => 1,
                'field_name' => 'Warranty Expire Status',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 42,
                'report_module' => 1,
                'field_name' => 'Stock Location',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 43,
                'report_module' => 1,
                'field_name' => 'Purchase Currency',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 44,
                'report_module' => 1,
                'field_name' => 'Purchase Cost',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":3}',
                'value_type' => 2
            ],
            [
                'id' => 45,
                'report_module' => 1,
                'field_name' => 'Depreciation Cost',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":3}',
                'value_type' => 2
            ],
            [
                'id' => 46,
                'report_module' => 2,
                'field_name' => 'Ticket Id',
                'possible_checks' => '1,2,3,4,5,6,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 47,
                'report_module' => 2,
                'field_name' => 'Subject',
                'possible_checks' => '1,6,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 48,
                'report_module' => 2,
                'field_name' => 'Status',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 49,
                'report_module' => 2,
                'field_name' => 'Priority',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 50,
                'report_module' => 2,
                'field_name' => 'Department',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 51,
                'report_module' => 2,
                'field_name' => 'Problem category',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 52,
                'report_module' => 2,
                'field_name' => 'Sub category',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 53,
                'report_module' => 2,
                'field_name' => 'Created Via',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 54,
                'report_module' => 2,
                'field_name' => 'Creator Name',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 55,
                'report_module' => 2,
                'field_name' => 'TAT Hrs',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":3}',
                'value_type' => 2
            ],
            [
                'id' => 56,
                'report_module' => 2,
                'field_name' => 'Ticket Creator Location',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 57,
                'report_module' => 2,
                'field_name' => 'Ticket Type',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 58,
                'report_module' => 2,
                'field_name' => 'Device ',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 59,
                'report_module' => 2,
                'field_name' => 'Assign to',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 60,
                'report_module' => 2,
                'field_name' => 'Created By',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 61,
                'report_module' => 2,
                'field_name' => 'Updated By',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 62,
                'report_module' => 2,
                'field_name' => 'Created At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 63,
                'report_module' => 2,
                'field_name' => 'Updated At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 64,
                'report_module' => 2,
                'field_name' => 'TAT Expire At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 65,
                'report_module' => 2,
                'field_name' => 'Ticket Creator Email',
                'possible_checks' => '1,6,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 66,
                'report_module' => 2,
                'field_name' => 'Ticket Creator Base Location',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 67,
                'report_module' => 2,
                'field_name' => 'Tags',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 68,
                'report_module' => 2,
                'field_name' => 'Deleted By',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 69,
                'report_module' => 2,
                'field_name' => 'Deleted At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 70,
                'report_module' => 2,
                'field_name' => 'Resolved At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 71,
                'report_module' => 2,
                'field_name' => 'Closed At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 72,
                'report_module' => 2,
                'field_name' => 'Reopened By',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 73,
                'report_module' => 2,
                'field_name' => 'Reopened At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 74,
                'report_module' => 2,
                'field_name' => 'Feedback Rating',
                'possible_checks' => '1,6,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 75,
                'report_module' => 2,
                'field_name' => 'Request tag',
                'possible_checks' => '1,6',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 76,
                'report_module' => 2,
                'field_name' => 'Content',
                'possible_checks' => '1,6,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 77,
                'report_module' => 2,
                'field_name' => 'Ticket Assigned At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 78,
                'report_module' => 2,
                'field_name' => 'Ticket Assigned By',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 79,
                'report_module' => 2,
                'field_name' => 'Ticket Created At Month',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 80,
                'report_module' => 2,
                'field_name' => 'Ticket Creator Company',
                'possible_checks' => '7',
                'value_box' => '{"fb":2,"c7":5}',
                'value_type' => 3
            ],
            [
                'id' => 81,
                'report_module' => 2,
                'field_name' => 'Feedback Comment',
                'possible_checks' => '1,6,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 82,
                'report_module' => 2,
                'field_name' => 'Ticket Remarks',
                'possible_checks' => '1,6,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 83,
                'report_module' => 2,
                'field_name' => 'Handler First Response At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 84,
                'report_module' => 2,
                'field_name' => 'Creator First Response At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 85,
                'report_module' => 2,
                'field_name' => 'Sla Breached',
                'possible_checks' => '1,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 86,
                'report_module' => 2,
                'field_name' => 'Sla Breached At(minutes)',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":3}',
                'value_type' => 2
            ],
            [
                'id' => 87,
                'report_module' => 2,
                'field_name' => 'Response Sla Breached At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 88,
                'report_module' => 2,
                'field_name' => 'Response Sla Breach',
                'possible_checks' => '1,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 89,
                'report_module' => 2,
                'field_name' => 'Response At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 90,
                'report_module' => 2,
                'field_name' => 'Workaround Sla Breached At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 91,
                'report_module' => 2,
                'field_name' => 'Workaround Sla Breached',
                'possible_checks' => '1,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 92,
                'report_module' => 2,
                'field_name' => 'Workaround At',
                'possible_checks' => '1,2,3,4,5',
                'value_box' => '{"fb":4}',
                'value_type' => 4
            ],
            [
                'id' => 93,
                'report_module' => 2,
                'field_name' => 'Waiting For User More than 2 days',
                'possible_checks' => '1,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ],
            [
                'id' => 94,
                'report_module' => 2,
                'field_name' => 'Service Request',
                'possible_checks' => '1,8',
                'value_box' => '{"fb":1}',
                'value_type' => 1
            ]
        ];

        // Insert data
        DB::table('custom_report_available_fields')->upsert(
            $fields, 
            ['id'],
            ['report_module', 'field_name', 'possible_checks', 'value_box', 'value_type'] 
        );
    }
}
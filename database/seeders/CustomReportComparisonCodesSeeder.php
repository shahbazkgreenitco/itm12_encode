<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomReportComparisonCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $comparisonCodes = [
            [
                'id' => 1,
                'html_code' => null,
                'text_code' => 'Equal to'
            ],
            [
                'id' => 2,
                'html_code' => null,
                'text_code' => 'Greater than'
            ],
            [
                'id' => 3,
                'html_code' => null,
                'text_code' => 'Less than'
            ],
            [
                'id' => 4,
                'html_code' => null,
                'text_code' => 'Greater than equal to'
            ],
            [
                'id' => 5,
                'html_code' => null,
                'text_code' => 'Less than equal to'
            ],
            [
                'id' => 6,
                'html_code' => null,
                'text_code' => 'Includes'
            ],
            [
                'id' => 7,
                'html_code' => null,
                'text_code' => 'In'
            ],
            [
                'id' => 8,
                'html_code' => null,
                'text_code' => 'Not equal'
            ]
        ];

        // ✅ Using upsert - insert or update based on 'id'
        DB::table('custom_report_comparison_codes')->upsert(
            $comparisonCodes,    // Data array
            ['id'],              // Unique column(s) to check
            [                    // Columns to update if record exists
                'html_code',
                'text_code'
            ]
        );

        // Output success message
        $this->command->info('Custom Report Comparison Codes seeded successfully!');
    }
}
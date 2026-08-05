<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionOperatorSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1, 'condition_id' => 1, 'operator_id' => 1],
            ['id' => 2, 'condition_id' => 1, 'operator_id' => 2],
            ['id' => 3, 'condition_id' => 1, 'operator_id' => 3],
            ['id' => 4, 'condition_id' => 1, 'operator_id' => 4],
            ['id' => 5, 'condition_id' => 1, 'operator_id' => 5],
            ['id' => 6, 'condition_id' => 1, 'operator_id' => 6],
            ['id' => 7, 'condition_id' => 1, 'operator_id' => 7],

            ['id' => 8, 'condition_id' => 2, 'operator_id' => 1],
            ['id' => 9, 'condition_id' => 2, 'operator_id' => 2],
            ['id' => 10, 'condition_id' => 2, 'operator_id' => 3],
            ['id' => 11, 'condition_id' => 2, 'operator_id' => 4],

            ['id' => 12, 'condition_id' => 3, 'operator_id' => 10],
            ['id' => 13, 'condition_id' => 3, 'operator_id' => 11],

            ['id' => 68, 'condition_id' => 4, 'operator_id' => 1],
            ['id' => 69, 'condition_id' => 4, 'operator_id' => 2],
            ['id' => 70, 'condition_id' => 4, 'operator_id' => 3],
            ['id' => 71, 'condition_id' => 4, 'operator_id' => 4],
            ['id' => 72, 'condition_id' => 4, 'operator_id' => 5],
            ['id' => 73, 'condition_id' => 4, 'operator_id' => 6],
            ['id' => 75, 'condition_id' => 4, 'operator_id' => 10],
            ['id' => 76, 'condition_id' => 4, 'operator_id' => 11],

            ['id' => 23, 'condition_id' => 5, 'operator_id' => 5],
            ['id' => 24, 'condition_id' => 5, 'operator_id' => 6],
            ['id' => 25, 'condition_id' => 5, 'operator_id' => 8],
            ['id' => 26, 'condition_id' => 5, 'operator_id' => 9],

            ['id' => 27, 'condition_id' => 6, 'operator_id' => 10],
            ['id' => 28, 'condition_id' => 6, 'operator_id' => 11],
            ['id' => 29, 'condition_id' => 6, 'operator_id' => 12],
            ['id' => 30, 'condition_id' => 6, 'operator_id' => 13],
            ['id' => 31, 'condition_id' => 6, 'operator_id' => 14],
            ['id' => 32, 'condition_id' => 6, 'operator_id' => 15],
            ['id' => 33, 'condition_id' => 6, 'operator_id' => 16],
            ['id' => 34, 'condition_id' => 6, 'operator_id' => 17],

            ['id' => 35, 'condition_id' => 7, 'operator_id' => 1],
            ['id' => 36, 'condition_id' => 7, 'operator_id' => 2],
            ['id' => 37, 'condition_id' => 7, 'operator_id' => 3],
            ['id' => 38, 'condition_id' => 7, 'operator_id' => 4],
            ['id' => 39, 'condition_id' => 7, 'operator_id' => 5],
            ['id' => 40, 'condition_id' => 7, 'operator_id' => 6],
            ['id' => 41, 'condition_id' => 7, 'operator_id' => 7],

            ['id' => 42, 'condition_id' => 8, 'operator_id' => 1],
            ['id' => 43, 'condition_id' => 8, 'operator_id' => 2],
            ['id' => 44, 'condition_id' => 8, 'operator_id' => 3],
            ['id' => 45, 'condition_id' => 8, 'operator_id' => 4],
            ['id' => 46, 'condition_id' => 8, 'operator_id' => 5],
            ['id' => 47, 'condition_id' => 8, 'operator_id' => 6],
            ['id' => 48, 'condition_id' => 8, 'operator_id' => 7],

            ['id' => 49, 'condition_id' => 9, 'operator_id' => 10],

            ['id' => 77, 'condition_id' => 10, 'operator_id' => 1],
            ['id' => 78, 'condition_id' => 10, 'operator_id' => 2],
            ['id' => 79, 'condition_id' => 10, 'operator_id' => 3],
            ['id' => 80, 'condition_id' => 10, 'operator_id' => 4],
            ['id' => 81, 'condition_id' => 10, 'operator_id' => 5],
            ['id' => 82, 'condition_id' => 10, 'operator_id' => 6],
            ['id' => 83, 'condition_id' => 10, 'operator_id' => 10],
            ['id' => 84, 'condition_id' => 10, 'operator_id' => 11],

            ['id' => 85, 'condition_id' => 11, 'operator_id' => 1],
            ['id' => 86, 'condition_id' => 11, 'operator_id' => 2],
            ['id' => 87, 'condition_id' => 11, 'operator_id' => 3],
            ['id' => 88, 'condition_id' => 11, 'operator_id' => 4],
            ['id' => 89, 'condition_id' => 11, 'operator_id' => 5],
            ['id' => 90, 'condition_id' => 11, 'operator_id' => 6],
            ['id' => 91, 'condition_id' => 11, 'operator_id' => 10],
            ['id' => 92, 'condition_id' => 11, 'operator_id' => 11],
        ];

        DB::table('condition_operator')->upsert(
            $data,
            ['condition_id', 'operator_id'],
            ['id']
        );
    }
}
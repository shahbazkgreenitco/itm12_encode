<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use App\Models\Role;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'SuperAdmin',
            'Admin',
            'Technician',
            'User'
        ];

        foreach ($roles as $roleName) {
            Role::updateOrCreate(
                [
                    'name' => $roleName,
                    'guard_name' => 'web'
                ],
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}

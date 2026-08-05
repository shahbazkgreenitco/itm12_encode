<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ModelHasRolesSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('username', 'admin')->first();
        $role = Role::where('name', 'SuperAdmin')->first();

        if (!$user || !$role) {
            return;
        }

        $user->assignRole($role);
    }
}

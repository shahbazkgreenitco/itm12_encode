<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UpdateUserPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::where('name', 'UserDocuments')->delete();

        Permission::updateOrCreate(
            ['name' => 'UserDocumentsUpload'],
            [
                'guard_name' => 'web',
                'module_id' => 1
            ]
        );
    }
}

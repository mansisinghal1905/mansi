<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'Role-Management',
            'Customer-Management',
            'Project-Management',
            'ProjectStatus-Management',
            'Task-Management',
            'Designation-Management',
            'SendPurposal-Management',
            'Invoice-Management',
            'Project-Assign-Management',
            'Task-Assign-Management',
            'Chat-Management',

        ];

        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}

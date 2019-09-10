<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PatchSeeder__Permission_Agent extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Agent
            [
                'name' => 'agent__browse',
                'display_name' => 'Browse agent'
            ],
            [
                'name' => 'agent__create',
                'display_name' => 'Create agent'
            ],
            [
                'name' => 'agent__edit',
                'display_name' => 'Edit agent'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

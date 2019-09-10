<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PatchSeeder__Permission_Agent_Level extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Agent level
            [
                'name' => 'agent_level__browse',
                'display_name' => 'Browse agent level'
            ],
            [
                'name' => 'agent_level__create',
                'display_name' => 'Create agent level'
            ],
            [
                'name' => 'agent_level__edit',
                'display_name' => 'Edit agent level'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

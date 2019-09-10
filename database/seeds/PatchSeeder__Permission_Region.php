<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PatchSeeder__Permission_Region extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Region
            [
                'name' => 'region__browse',
                'display_name' => 'Browse region'
            ],
            [
                'name' => 'region__create',
                'display_name' => 'Create region'
            ],
            [
                'name' => 'region__edit',
                'display_name' => 'Edit region'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

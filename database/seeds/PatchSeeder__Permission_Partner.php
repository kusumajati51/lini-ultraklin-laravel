<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PatchSeeder__Permission_Partner extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Partner
            [
                'name' => 'partner__browse',
                'display_name' => 'Browse partner'
            ],
            [
                'name' => 'partner__create',
                'display_name' => 'Create partner'
            ],
            [
                'name' => 'partner__edit',
                'display_name' => 'Edit partner'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

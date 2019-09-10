<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PatchSeeder__Permission_Menu extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Menu
            [
                'name' => 'menu__browse',
                'display_name' => 'Browse menu'
            ],
            [
                'name' => 'menu__create',
                'display_name' => 'Create menu'
            ],
            [
                'name' => 'menu__edit',
                'display_name' => 'Edit menu'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

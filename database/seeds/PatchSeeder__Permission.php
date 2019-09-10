<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PatchSeeder__Permission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Customer
            [
                'name' => 'customer__browse',
                'display_name' => 'Browse customer'
            ],
            [
                'name' => 'customer__create',
                'display_name' => 'Create customer'
            ],
            [
                'name' => 'customer__edit',
                'display_name' => 'Edit customer'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

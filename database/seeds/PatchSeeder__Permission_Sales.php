<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PatchSeeder__Permission_Sales extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Sales
            [
                'name' => 'sales__browse',
                'display_name' => 'Browse sales'
            ],
            [
                'name' => 'sales__create',
                'display_name' => 'Create sales'
            ],
            [
                'name' => 'sales__edit',
                'display_name' => 'Edit sales'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

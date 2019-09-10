<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PatchSeeder__Permission_Sales_Level extends Seeder
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
                'name' => 'sales_level__browse',
                'display_name' => 'Browse sales level'
            ],
            [
                'name' => 'sales_level__create',
                'display_name' => 'Create sales level'
            ],
            [
                'name' => 'sales_level__edit',
                'display_name' => 'Edit sales level'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

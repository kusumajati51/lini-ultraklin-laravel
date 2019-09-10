<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PatchSeeder__Permission_Report_User extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // User Report
            [
                'name' => 'report__user_browse',
                'display_name' => 'Browse user report'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

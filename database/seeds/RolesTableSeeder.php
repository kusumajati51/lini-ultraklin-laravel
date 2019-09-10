<?php

use Illuminate\Database\Seeder;

use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Admin'
            ],
            [
                'name' => 'user',
                'display_name' => 'Normal User'
            ],
            [
                'name' => 'customer-service',
                'display_name' => 'Customer Service'
            ],
            [
                'name' => 'cleaning-service',
                'display_name' => 'Cleaning Service'
            ],
            [
                'name' => 'messenger',
                'display_name' => 'Messenger'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}

<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Dashboard
            [
                'name' => 'dashboard__browse',
                'display_name' => 'Browse dashboard'
            ],

            // Invoice
            [
                'name' => 'invoice__browse',
                'display_name' => 'Browse invoice'
            ],
            [
                'name' => 'invoice__change_status',
                'display_name' => 'Change invoice status'
            ],

            // Order
            [
                'name' => 'order__browse',
                'display_name' => 'Browse order'
            ],
            [
                'name' => 'order__create',
                'display_name' => 'Create order'
            ],
            [
                'name' => 'order__change_status',
                'display_name' => 'Change order status'
            ],

            // User
            [
                'name' => 'user__browse',
                'display_name' => 'Browse user'
            ],
            
            // Banner
            [
                'name' => 'banner__browse',
                'display_name' => 'Browse banner'
            ],
            [
                'name' => 'banner__create',
                'display_name' => 'Create banner'
            ],
            [
                'name' => 'banner__edit',
                'display_name' => 'Edit banner'
            ],

            // Promotion
            [
                'name' => 'promotion__browse',
                'display_name' => 'Browse promotion'
            ],
            [
                'name' => 'promotion__create',
                'display_name' => 'Create promotion'
            ],
            [
                'name' => 'promotion__edit',
                'display_name' => 'Edit promotion'
            ],

            // Service
            [
                'name' => 'service__browse',
                'display_name' => 'Browse service'
            ],
            [
                'name' => 'service__create',
                'display_name' => 'Create service'
            ],
            [
                'name' => 'service__edit',
                'display_name' => 'Edit service'
            ],

            // Package
            [
                'name' => 'package__browse',
                'display_name' => 'Browse package'
            ],
            [
                'name' => 'package__create',
                'display_name' => 'Create package'
            ],
            [
                'name' => 'package__edit',
                'display_name' => 'Edit package'
            ],
            [
                'name' => 'package__item_browse',
                'display_name' => 'Browse package item'
            ],
            [
                'name' => 'package__item_create',
                'display_name' => 'Create package item'
            ],
            [
                'name' => 'package__item_edit',
                'display_name' => 'Edit package item'
            ],
            [
                'name' => 'package__item_delete',
                'display_name' => 'Edit package delete'
            ],

            // Item
            [
                'name' => 'item__browse',
                'display_name' => 'Browse item'
            ],
            [
                'name' => 'item__create',
                'display_name' => 'Create item'
            ],
            [
                'name' => 'item__edit',
                'display_name' => 'Edit item'
            ],

            // Role
            [
                'name' => 'role__browse',
                'display_name' => 'Browse role'
            ],
            [
                'name' => 'role__create',
                'display_name' => 'Create role'
            ],
            [
                'name' => 'role__permission_browse',
                'display_name' => 'Browse role permission'
            ],
            [
                'name' => 'role__permission_edit',
                'display_name' => 'Edit role permission'
            ],

            // Officer
            [
                'name' => 'officer__browse',
                'display_name' => 'Browse officer'
            ],
            [
                'name' => 'officer__create',
                'display_name' => 'Create officer'
            ],
            [
                'name' => 'officer__edit',
                'display_name' => 'Edit officer'
            ],

            // Report
            [
                'name' => 'report__order_browse',
                'display_name' => 'Browse order report'
            ],

            // Setting
            [
                'name' => 'setting__browse',
                'display_name' => 'Browse setting'
            ],
            [
                'name' => 'setting__create',
                'display_name' => 'Create setting'
            ],
            [
                'name' => 'setting__edit',
                'display_name' => 'Edit setting'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

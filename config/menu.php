<?php

return [
    [
        'header' => 'Main Menu',
        'menu' => [
            [
                'title' => 'Dashboard',
                'link' => '/admin/v1#/dashboard',
                'link_js' => 'dashboard',
                'permission' => 'dashboard__browse'
            ],
            [
                'title' => 'Invoices',
                'link' => '/admin/invoices',
                'permission' => 'invoice__browse'
            ],
            [
                'title' => 'Orders',
                'link' => '/admin/v1#/orders',
                'link_js' => 'order',
                'permission' => 'order__browse'
            ],
            [
                'title' => 'Users',
                'link' => '/admin/users',
                'permission' => 'user__browse'
            ],
            [
                'title' => 'Customers',
                'link' => '/admin/customers',
                'permission' => 'customer__browse'
            ],
            [
                'title' => 'Agents',
                'link' => '/admin/v1#/agents',
                'link_js' => 'agent',
                'permission' => 'agent__browse'
            ],
            [
                'title' => 'Sales',
                'link' => '/admin/v1#/sales',
                'link_js' => 'sales',
                'permission' => 'sales__browse'
            ],
            [
                'title' => 'Partners',
                'link' => '/admin/v1#/partners',
                'link_js' => 'partner',
                'permission' => 'partner__browse'
            ]
        ]
    ],
    [
        'header' => 'Master Data',
        'menu' => [
            [
                'title' => 'Banners',
                'link' => '/admin/banners',
                'permission' => 'banner__browse'
            ],
            [
                'title' => 'Promotions',
                'link' => '/admin/promotions',
                'permission' => 'promotion__browse'
            ],
            [
                'title' => 'Services',
                'link' => '/admin/services',
                'permission' => 'service__browse'
            ],
            [
                'title' => 'Packages',
                'link' => '/admin/packages',
                'permission' => 'package__browse'
            ],
            [
                'title' => 'Items',
                'link' => '/admin/items',
                'permission' => 'item__browse'
            ],
            [
                'title' => 'Roles',
                'link' => '/admin/roles',
                'permission' => 'role__browse'
            ],
            [
                'title' => 'Officers',
                'link' => '/admin/officers',
                'permission' => 'officer__browse'
            ],
            [
                'title' => 'Regions',
                'link' => '/admin/regions',
                'permission' => 'region__browse'
            ],
            [
                'title' => 'Agent Levels',
                'link' => '/admin/v1#/agent-levels',
                'link_js' => 'agent-level',
                'permission' => 'agent_level__browse'
            ],
            [
                'title' => 'Sales Levels',
                'link' => '/admin/v1#/sales-levels',
                'link_js' => 'sales-level',
                'permission' => 'sales_level__browse'
            ]
        ]
    ],
    [
        'header' => 'Report',
        'menu' => [
            [
                'title' => 'Order',
                'link' => '/admin/report/order',
                'permission' => 'report__order_browse'
            ],
            [
                'title' => 'User',
                'link' => '/admin/report/user',
                'permission' => 'report__user_browse'
            ]
        ]
    ],
    [
        'header' => 'Tools',
        'menu' => [
            [
                'title' => 'Menu',
                'link' => '/admin/menu',
                'permission' => 'menu__browse'
            ]
        ]
    ],
    [
        'header' => 'Setting',
        'menu' => [
            [
                'title' => 'Settings',
                'link' => '/admin/settings',
                'permission' => 'setting__browse'
            ]
        ]
    ]
];
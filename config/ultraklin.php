<?php

return [
    'time' => [
        'cycle_time' => ['19:00', '18:59'],
        'working_time' => ['08:00', '19:00'],
        'new_auth_start' => env('ULTRAKLIN_NEW_AUTH_START', '2018-06-01')
    ],

    'cleaning' => [
        'per_room_time' => 0.5, // 30 minutes
        'min_order_hours' => 2, // 2 hours
        'min_order_value' => 100000 // currency
    ],

    'promotion_targets' => [
        // ALL
        [
            'category' => 'all',
            'category_name' => 'All',
            'name' => 'total-prices',
            'display_name' => 'Total Prices'
        ],
        [
            'category' => 'all',
            'category_name' => 'All',
            'name' => 'item',
            'display_name' => 'Item'
        ],
        [
            'category' => 'all',
            'category_name' => 'All',
            'name' => 'the-first-2-hours',
            'display_name' => 'The First 2 Hours'
        ],
        // NEW USER
        [
            'category' => 'new-user',
            'category_name' => 'New User',
            'name' => 'new-user__total-prices',
            'display_name' => 'Total Prices (one time use)'
        ],
        [
            'category' => 'new-user',
            'category_name' => 'New User',
            'name' => 'new-user__item',
            'display_name' => 'Item (one time use)'
        ],
        [
            'category' => 'new-user',
            'category_name' => 'New User',
            'name' => 'new-user__the-first-2-hours',
            'display_name' => 'The First 2 Hours (one time use)'
        ],
        //BY ORDER COUNT
        [
            'category' => 'order-count',
            'category_name' => 'Order Count',
            'name' => 'fifth-order',
            'display_name' => 'Every fifth order (multiply)'
        ],
    ],

    'level_rules' => [
        'normal' => 'Normal',
        'order_counter' => 'Order Counter'
    ],

    'order_notification' => [
        'new' => true,
        'cancel' => true,
        'confirm' => true,
        'on_the_way' => false,
        'process' => false,
        'done' => false
    ],

    'emoji' => [
        'wink' => "\xF0\x9F\x98\x89 \xF0\x9F\x98\x89 \xF0\x9F\x98\x89 \xF0\x9F\x98\x89 \xF0\x9F\x98\x89",
        'crying' => "\xF0\x9F\x98\xAD \xF0\x9F\x98\xAD \xF0\x9F\x98\xAD \xF0\x9F\x98\xAD \xF0\x9F\x98\xAD",
        'thumb' => "\xF0\x9F\x91\x8D \xF0\x9F\x91\x8D \xF0\x9F\x91\x8D \xF0\x9F\x91\x8D \xF0\x9F\x91\x8D",
        'minibus' => "\xF0\x9F\x9A\x90 \xF0\x9F\x9A\x90 \xF0\x9F\x9A\x90 \xF0\x9F\x9A\x90 \xF0\x9F\x9A\x90",
        'hourglass' => "\xE2\x8F\xB3 \xE2\x8F\xB3 \xE2\x8F\xB3 \xE2\x8F\xB3 \xE2\x8F\xB3",
        'party_popper' => "\xF0\x9F\x8E\x89 \xF0\x9F\x8E\x89 \xF0\x9F\x8E\x89 \xF0\x9F\x8E\x89 \xF0\x9F\x8E\x89"
    ],

    'old' => [
        'minimal' => 3,
        'perKilo' => 7000
    ]
];
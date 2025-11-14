<?php

return [
    'default' => env('PAYMENTS_TYPE', 'offline'),

    'types' => [
        'cash' => [
            'driver'     => 'offline',
            'authorized' => 'payment-offline',
        ],
        'card' => [
            'driver' => 'stripe',
        ],
        'paypal' => [
            'driver' => 'paypal',
        ]
    ],
];

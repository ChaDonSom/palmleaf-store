<?php

return [
    'default' => env('PAYMENTS_TYPE', 'offline'),

    'types' => [
        'card' => [
            'driver' => 'stripe',
        ],
        'paypal' => [
            'driver' => 'paypal',
        ]
    ],
];

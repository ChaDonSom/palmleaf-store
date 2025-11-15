<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Capture policy
    |--------------------------------------------------------------------------
    |
    | Here is where you can set whether you want to capture and charge payments
    | straight away, or create the Payment Intent and release them at a later date.
    |
    | automatic - Capture the payment straight away.
    | manual - Don't take payment straight away and capture later.
    |
    */
    'policy' => 'manual',

    /*
    |--------------------------------------------------------------------------
    | Status Mapping
    |--------------------------------------------------------------------------
    |
    | Map Stripe payment intent statuses to your order statuses.
    | When a payment intent is updated, the order status will be set accordingly.
    |
    */
    'status_mapping' => [
        'succeeded' => 'paid',
        'requires_capture' => 'requires-capture',
        'canceled' => 'cancelled',
        'processing' => 'processing',
        'requires_payment_method' => 'payment-failed',
    ],
];

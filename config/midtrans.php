<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Midtrans payment gateway.
    |
    */

    // Server Key from Midtrans Dashboard
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    // Client Key from Midtrans Dashboard
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    // Set to true for production mode, false for sandbox/testing
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Enable 3D Secure authentication for credit card payments
    'enable_3ds' => env('MIDTRANS_3DS', true),

    // Enable sanitization of input data
    'enable_sanitization' => env('MIDTRANS_SANITIZED', false),

    // Default currency for transactions
    'currency' => 'IDR',

    // Payment options
    'payment_options' => [
        'credit_card',        // Credit card payments
        'bank_transfer',      // Bank transfer payments
        'gopay',              // GoPay payments
        'shopeepay',          // ShopeePay payments
        'ovo',                // OVO payments
        'dana',               // DANA payments
        'bca_va',             // BCA Virtual Account
        'bni_va',             // BNI Virtual Account
        'bri_va',             // BRI Virtual Account
        'permata_va',         // Permata Virtual Account
    ],
];
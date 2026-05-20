<?php

return [

    'key'    => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),

    'webhook' => [
        'secret'    => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('CASHIER_WEBHOOK_TOLERANCE', 300),
    ],

    'prices' => [
        'pro'        => env('STRIPE_PRICE_PRO',        'price_1TYyVA3wN3kkGmhWiPsZtbic'),
        'business'   => env('STRIPE_PRICE_BUSINESS',   'price_1TYyXP3wN3kkGmhW0xJhAbzA'),
        'pro_launch' => env('STRIPE_PRICE_PRO_LAUNCH', 'price_1TYyYS3wN3kkGmhWxiT4Epfd'),
    ],

    'model'           => App\Models\Company::class,
    'currency'        => env('CASHIER_CURRENCY', 'brl'),
    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'pt_BR'),
    'logger'          => env('CASHIER_LOGGER'),
    'payment_notification' => null,

];

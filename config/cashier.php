<?php

return [

    'key'    => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),

    'webhook' => [
        'secret'    => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('CASHIER_WEBHOOK_TOLERANCE', 300),
    ],

    'prices' => [
        'pro_launch'         => env('STRIPE_PRICE_PRO_LAUNCH',         'price_1TZZQE3wN3kkGmhWN7WeiL0v'), // R$ 39,90/mês — Lançamento
        'pro'                => env('STRIPE_PRICE_PRO',                'price_1TZZOb3wN3kkGmhWDE9PHRDp'), // R$ 59,90/mês
        'pro_launch_annual'  => env('STRIPE_PRICE_PRO_LAUNCH_ANNUAL',  'price_1TZiOC3wN3kkGmhW8mVfInoz'), // R$ 31,92/mês (anual)
        'business'           => env('STRIPE_PRICE_BUSINESS',           'price_1TZZOV3wN3kkGmhWsUKZHtae'), // R$ 119,90/mês
        'business_annual'    => env('STRIPE_PRICE_BUSINESS_ANNUAL',    'price_1TZiQ43wN3kkGmhWVop8aRnh'), // R$ 95,92/mês (anual)
    ],

    'model'           => App\Models\Company::class,
    'currency'        => env('CASHIER_CURRENCY', 'brl'),
    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'pt_BR'),
    'logger'          => env('CASHIER_LOGGER'),
    'payment_notification' => null,

];

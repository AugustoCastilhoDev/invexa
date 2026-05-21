<?php

return [

    'default' => env('MAIL_MAILER', 'log'),

    'mailers' => [

        'resend' => [
            'transport' => 'resend',
        ],

        'smtp' => [
            'transport'  => 'smtp',
            'url'        => env('MAIL_URL'),
            'host'       => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port'       => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username'   => env('MAIL_USERNAME'),
            'password'   => env('MAIL_PASSWORD'),
            'timeout'    => null,
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers'   => ['resend', 'log'],
        ],

    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@invexa.offerjetshop.net'),
        'name'    => env('MAIL_FROM_NAME', 'Invexa'),
    ],

];

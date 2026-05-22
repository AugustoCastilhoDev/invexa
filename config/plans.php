<?php

/*
|--------------------------------------------------------------------------
| Invexa — Mapeamento de Planos x Price IDs do Stripe
|--------------------------------------------------------------------------
| Estrutura: plans.<plano>.<periodo> => price_id
|
| Para atualizar um price ID, substitua o valor correspondente e
| execute: php artisan config:clear
*/

return [

    'pro_launch' => [
        'monthly' => env('STRIPE_PRICE_PRO_LAUNCH_MONTHLY', 'price_1TZZQE3wN3kkGmhWN7WeiL0v'),
        'annual'  => env('STRIPE_PRICE_PRO_LAUNCH_ANNUAL',  'price_1TZj903wN3kkGmhW0kosrKyR'),
    ],

    'pro' => [
        'monthly' => env('STRIPE_PRICE_PRO_MONTHLY', 'price_1TZZOb3wN3kkGmhWDE9PHRDp'),
        'annual'  => env('STRIPE_PRICE_PRO_ANNUAL',  ''),
    ],

    'business' => [
        'monthly' => env('STRIPE_PRICE_BUSINESS_MONTHLY', 'price_1TZZOV3wN3kkGmhWsUKZHtae'),
        'annual'  => env('STRIPE_PRICE_BUSINESS_ANNUAL',  'price_1TZjCO3wN3kkGmhWrN2aaZ4G'),
    ],

];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feature toggles
    |--------------------------------------------------------------------------
    |
    | Interruptores gerais de funcionalidades opcionais do sistema. Desligadas
    | por padrão para que o Invexa funcione como uma ferramenta pura de
    | controle e gestão de estoque/vendas, sem movimentar dinheiro real
    | (Pix/Asaas) nem emitir documentos fiscais reais (NF-e/Focus NFe).
    |
    */

    'pix_enabled' => env('FEATURE_PIX_ENABLED', false),

    'nfe_enabled' => env('FEATURE_NFE_ENABLED', false),

];

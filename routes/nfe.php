<?php

use App\Http\Controllers\NFeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas NF-e
|--------------------------------------------------------------------------
| Inclua este arquivo no routes/web.php com:
| require __DIR__.'/nfe.php';
|
| Dentro do grupo de middleware ['auth', 'verified', 'has.company', 'check.subscription']
| e com prefix 'fiscal' ou dentro do grupo principal.
*/

Route::middleware(['auth', 'verified'])->prefix('nfes')->name('nfes.')->group(function () {
    // Listagem e visualização
    Route::get('/', [NFeController::class, 'index'])->name('index');
    Route::get('/{nfe}', [NFeController::class, 'show'])->name('show');

    // Emissão a partir de uma venda
    Route::post('/emitir/{sale}', [NFeController::class, 'emitir'])->name('emitir');

    // Operações pós-emissão
    Route::post('/{nfe}/consultar', [NFeController::class, 'consultar'])->name('consultar');
    Route::post('/{nfe}/cancelar', [NFeController::class, 'cancelar'])->name('cancelar');
    Route::post('/{nfe}/carta-correcao', [NFeController::class, 'cartaCorrecao'])->name('carta-correcao');

    // Downloads
    Route::get('/{nfe}/xml', [NFeController::class, 'downloadXml'])->name('xml');
    Route::get('/{nfe}/danfe', [NFeController::class, 'downloadDanfe'])->name('danfe');
});

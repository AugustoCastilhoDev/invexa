# Rotas do Módulo de Orçamentos

Adicione as rotas abaixo no arquivo `routes/web.php`, dentro do grupo de rotas autenticadas (junto com as outras rotas protegidas pelo middleware `auth`):

```php
// Orçamentos
Route::prefix('quotes')->name('quotes.')->group(function () {
    Route::get('/',              [QuoteController::class, 'index'])->name('index');
    Route::get('/create',        [QuoteController::class, 'create'])->name('create');
    Route::post('/',             [QuoteController::class, 'store'])->name('store');
    Route::get('/{quote}',       [QuoteController::class, 'show'])->name('show');
    Route::get('/{quote}/pdf',   [QuoteController::class, 'pdf'])->name('pdf');
    Route::post('/{quote}/convert', [QuoteController::class, 'convertToSale'])->name('convert');
    Route::patch('/{quote}/sent',   [QuoteController::class, 'markSent'])->name('sent');
    Route::patch('/{quote}/status', [QuoteController::class, 'updateStatus'])->name('status');
    Route::delete('/{quote}',    [QuoteController::class, 'destroy'])->name('destroy');
});
```

Não se esqueça de adicionar o `use` no topo do `web.php`:
```php
use App\Http\Controllers\QuoteController;
```

## Dependência: barryvdh/laravel-dompdf

O PDF usa o pacote DomPDF. Se ainda não instalou:
```bash
composer require barryvdh/laravel-dompdf
```

## Migration

Após o push, rode no servidor:
```bash
php artisan migrate
```

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id','product_id','description','quantity','unit_price','total',
    ];

    protected $casts = [
        'quantity'   => 'float',
        'unit_price' => 'float',
        'total'      => 'float',
    ];

    protected static function booted(): void
    {
        // Calcula o total antes de inserir/atualizar, sem chamar save() novamente
        $recalc = function (QuoteItem $item) {
            $item->total = round((float) $item->quantity * (float) $item->unit_price, 2);
        };

        static::creating($recalc);
        static::updating($recalc);
    }

    public function quote()   { return $this->belongsTo(Quote::class); }
    public function product() { return $this->belongsTo(Product::class); }
}

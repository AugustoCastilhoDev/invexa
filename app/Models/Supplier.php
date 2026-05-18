<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id','name','email','phone',
        'document','address','city','state','zip_code','notes',
    ];

    public function company(): BelongsTo      { return $this->belongsTo(Company::class); }
    public function products(): HasMany       { return $this->hasMany(Product::class); }
    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class); }
    public function bills(): HasMany          { return $this->hasMany(Bill::class); }
}

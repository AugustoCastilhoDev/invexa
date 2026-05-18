<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id','name','email','phone',
        'document','address','city','state','zip_code','notes',
    ];

    public function company(): BelongsTo    { return $this->belongsTo(Company::class); }
    public function sales(): HasMany        { return $this->hasMany(Sale::class); }
    public function receivables(): HasMany  { return $this->hasMany(Receivable::class); }
}

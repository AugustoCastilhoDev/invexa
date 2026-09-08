<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\BelongsToCompany;

class WebhookEndpoint extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id', 'url', 'secret', 'events', 'active', 'description',
    ];

    protected $casts = [
        'events' => 'array',
        'active' => 'boolean',
    ];

    public static function generateSecret(): string
    {
        return Str::random(40);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function listensTo(string $event): bool
    {
        return in_array($event, $this->events ?? []);
    }
}

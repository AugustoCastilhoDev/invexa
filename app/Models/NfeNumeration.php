<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class NfeNumeration extends Model
{
    protected $fillable = [
        'company_id',
        'ambiente',
        'serie',
        'ultimo_numero',
    ];

    protected $casts = [
        'ultimo_numero' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Retorna o próximo número sequencial de NF-e para a empresa/série/ambiente.
     * Usa SELECT ... FOR UPDATE para garantir que dois processos simultâneos
     * nunca peguem o mesmo número.
     */
    public static function proximoNumero(int $companyId, string $serie, string $ambiente): int
    {
        return DB::transaction(function () use ($companyId, $serie, $ambiente) {
            /** @var NfeNumeration $row */
            $row = static::lockForUpdate()->firstOrCreate(
                [
                    'company_id' => $companyId,
                    'ambiente'   => $ambiente,
                    'serie'      => $serie,
                ],
                ['ultimo_numero' => 0]
            );

            $row->increment('ultimo_numero');
            $row->refresh();

            return $row->ultimo_numero;
        });
    }

    /**
     * Consulta o número atual sem incrementar (útil para exibição na UI).
     */
    public static function atualNumero(int $companyId, string $serie, string $ambiente): int
    {
        return (int) static::where('company_id', $companyId)
            ->where('ambiente', $ambiente)
            ->where('serie', $serie)
            ->value('ultimo_numero');
    }

    /**
     * Permite definir manualmente o número (ex.: sincronizar com numeração já usada na SEFAZ).
     */
    public static function definirNumero(int $companyId, string $serie, string $ambiente, int $numero): void
    {
        static::updateOrCreate(
            [
                'company_id' => $companyId,
                'ambiente'   => $ambiente,
                'serie'      => $serie,
            ],
            ['ultimo_numero' => $numero]
        );
    }
}

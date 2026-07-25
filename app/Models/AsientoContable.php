<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsientoContable extends Model
{
    protected $table = 'asientos_contables';

    protected $fillable = ['numero', 'fecha', 'glosa', 'tipo', 'estado', 'total_debe', 'total_haber', 'user_id'];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function detalle(): HasMany
    {
        return $this->hasMany(AsientoDetalle::class, 'asiento_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'usuario_id');
    }

    public static function tipos(): array
    {
        return [
            'apertura' => 'Apertura',
            'operaciones' => 'Operaciones',
            'ajuste' => 'Ajuste',
            'cierre' => 'Cierre',
        ];
    }

    public static function estados(): array
    {
        return [
            'provisional' => 'Provisional',
            'definitivo' => 'Definitivo',
            'anulado' => 'Anulado',
        ];
    }

    public static function nextNumber(): string
    {
        $last = static::max('id') ?? 0;
        return str_pad($last + 1, 6, '0', STR_PAD_LEFT);
    }
}

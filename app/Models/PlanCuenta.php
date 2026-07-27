<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanCuenta extends Model
{
    protected $table = 'plan_cuentas';

    protected $fillable = ['codigo', 'nombre', 'tipo', 'nivel', 'padre_id', 'estado'];

    public function padre(): BelongsTo
    {
        return $this->belongsTo(self::class, 'padre_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(self::class, 'padre_id');
    }

    public function asientos(): HasMany
    {
        return $this->hasMany(AsientoDetalle::class, 'plan_cuenta_id');
    }

    public static function tipos(): array
    {
        return [
            'activo' => 'Activo',
            'pasivo' => 'Pasivo',
            'patrimonio' => 'Patrimonio',
            'ingreso' => 'Ingreso',
            'costo' => 'Costo',
            'gasto' => 'Gasto',
        ];
    }

    /**
     * Naturaleza deudora: la cuenta aumenta por el Debe (activo, costo,
     * gasto). Las demás son acreedoras: aumentan por el Haber.
     */
    public function esNaturalezaDeudora(): bool
    {
        return in_array($this->tipo, ['activo', 'costo', 'gasto'], true);
    }

    /**
     * Saldo actual de la cuenta según su naturaleza (asientos no anulados).
     * El neto Debe − Haber de todas las cuentas se calcula UNA sola vez por
     * request para no disparar una consulta por fila en el Plan de Cuentas.
     */
    public function saldoActual(): float
    {
        static $netos = null;

        if ($netos === null) {
            $netos = AsientoDetalle::whereHas('asiento', fn ($q) => $q->where('estado', '!=', 'anulado'))
                ->selectRaw('plan_cuenta_id, SUM(debe) - SUM(haber) as neto')
                ->groupBy('plan_cuenta_id')
                ->pluck('neto', 'plan_cuenta_id');
        }

        $neto = (float) ($netos[$this->id] ?? 0);

        return round($this->esNaturalezaDeudora() ? $neto : -$neto, 2);
    }
}

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
}

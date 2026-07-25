<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsientoDetalle extends Model
{
    protected $table = 'asientos_detalle';

    protected $fillable = ['asiento_id', 'plan_cuenta_id', 'debe', 'haber', 'glosa'];

    public function asiento(): BelongsTo
    {
        return $this->belongsTo(AsientoContable::class, 'asiento_id');
    }

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(PlanCuenta::class, 'plan_cuenta_id');
    }
}

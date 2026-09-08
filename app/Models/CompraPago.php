<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Una línea del pago mixto de una compra. */
class CompraPago extends Model
{
    protected $table = 'compra_pagos';

    protected $fillable = [
        'id_compra', 'metodo_pago', 'monto', 'referencia',
        'comprobantes', 'id_movimiento_caja', 'id_usuario',
    ];

    protected $casts = [
        'monto'        => 'float',
        'comprobantes' => 'array',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra', 'id_compra');
    }
}

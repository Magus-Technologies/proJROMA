<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Un método de pago aplicado a una venta (soporta pago mixto).
 * `comprobantes` guarda de 0 a 3 rutas de imagen en el disco public.
 */
class VentaPago extends Model
{
    protected $table = 'venta_pagos';

    protected $fillable = [
        'id_venta', 'id_dias_venta', 'metodo_pago', 'monto',
        'referencia', 'comprobantes', 'id_movimiento_caja', 'id_usuario',
    ];

    protected $casts = [
        'monto'        => 'float',
        'comprobantes' => 'array',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function cuota()
    {
        return $this->belongsTo(DiasVenta::class, 'id_dias_venta', 'dias_venta_id');
    }
}

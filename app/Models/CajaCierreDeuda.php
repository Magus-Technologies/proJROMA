<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Faltante de un cierre de caja aprobado: deuda a descontar al trabajador. */
class CajaCierreDeuda extends Model
{
    protected $table = 'caja_cierre_deudas';

    protected $fillable = [
        'id_cierre', 'id_caja', 'id_usuario', 'monto',
        'estado', 'observaciones', 'id_usuario_registra',
    ];

    protected $casts = [
        'monto' => 'float',
    ];

    public function cierre()
    {
        return $this->belongsTo(CierreCaja::class, 'id_cierre');
    }

    public function trabajador()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'usuario_id');
    }
}

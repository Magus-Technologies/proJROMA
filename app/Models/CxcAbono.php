<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Abono parcial de una cuota de Cuentas por Cobrar. */
class CxcAbono extends Model
{
    protected $table = 'cxc_abonos';

    protected $fillable = [
        'id_dias_venta', 'id_venta', 'fecha', 'monto', 'metodo_pago',
        'referencia', 'id_movimiento_caja', 'id_usuario',
        'estado', 'motivo_anulacion',
    ];

    protected $casts = [
        'monto' => 'float',
        'fecha' => 'date',
    ];

    public function cuota()
    {
        return $this->belongsTo(DiasVenta::class, 'id_dias_venta', 'dias_venta_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'usuario_id');
    }
}

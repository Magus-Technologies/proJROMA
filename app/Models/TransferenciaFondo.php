<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Asignación de fondo desde una caja principal (bóveda) hacia una caja hija.
 * El cajero la confirma al aperturar contando el efectivo recibido.
 */
class TransferenciaFondo extends Model
{
    protected $table = 'transferencias_fondo';

    protected $fillable = [
        'id_caja_origen', 'id_caja_destino', 'id_usuario_asigna', 'id_usuario_cajero',
        'monto', 'monto_contado', 'estado',
        'discrepancia_estado', 'discrepancia_resolucion', 'id_usuario_resuelve',
        'id_movimiento_egreso', 'observaciones',
    ];

    protected $casts = [
        'monto' => 'float',
        'monto_contado' => 'float',
    ];

    public function origen()
    {
        return $this->belongsTo(Caja::class, 'id_caja_origen', 'id');
    }

    public function destino()
    {
        return $this->belongsTo(Caja::class, 'id_caja_destino', 'id');
    }

    public function cajero()
    {
        return $this->belongsTo(User::class, 'id_usuario_cajero', 'usuario_id');
    }

    public function asignadoPor()
    {
        return $this->belongsTo(User::class, 'id_usuario_asigna', 'usuario_id');
    }

    public function resueltoPor()
    {
        return $this->belongsTo(User::class, 'id_usuario_resuelve', 'usuario_id');
    }

    public function getDiferenciaAttribute(): ?float
    {
        return $this->monto_contado === null ? null : round($this->monto_contado - $this->monto, 2);
    }

    public static function estados(): array
    {
        return [
            'ASIGNADA' => 'Asignada (en tránsito)',
            'APLICADA' => 'Aplicada',
            'RECHAZADA' => 'Rechazada',
            'ANULADA' => 'Anulada',
        ];
    }
}

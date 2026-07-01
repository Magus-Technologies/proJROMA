<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaApertura extends Model
{
    protected $table = 'caja_aperturas';
    protected $fillable = [
        'id_caja',
        'fecha',
        'monto_total',
        'estado',
        'id_usuario_apertura',
        'observaciones',
    ];
    protected $casts = [
        'monto_total' => 'float',
        'fecha'       => 'date',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario_apertura', 'usuario_id');
    }
}

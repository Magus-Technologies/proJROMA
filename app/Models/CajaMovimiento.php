<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaMovimiento extends Model
{
    protected $table = 'caja_movimientos';
    public $timestamps = false;
    protected $fillable = [
        'id_caja',
        'fecha',
        'tipo',
        'categoria',
        'descripcion',
        'monto',
        'instrumento_tipo',
        'instrumento_id',
        'saldo_anterior',
        'saldo_posterior',
        'origen_tipo',
        'origen_id',
        'id_usuario',
        'estado',
    ];
    protected $casts = [
        'monto'           => 'float',
        'saldo_anterior'  => 'float',
        'saldo_posterior' => 'float',
        'fecha'           => 'date',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'usuario_id');
    }
}

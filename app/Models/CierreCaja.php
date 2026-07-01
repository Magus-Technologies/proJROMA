<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CierreCaja extends Model
{
    protected $table = 'cierre_caja';
    protected $fillable = [
        'id_caja',
        'fecha',
        'saldo_declarado',
        'saldo_sistema',
        'desglose_instrumentos',
        'estado',
        'id_usuario_cierra',
        'id_usuario_aprueba',
        'observaciones',
    ];
    protected $casts = [
        'saldo_declarado'        => 'float',
        'saldo_sistema'          => 'float',
        'desglose_instrumentos'  => 'array',
        'fecha'                  => 'date',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id');
    }

    public function usuarioCierra()
    {
        return $this->belongsTo(User::class, 'id_usuario_cierra', 'usuario_id');
    }

    public function usuarioAprueba()
    {
        return $this->belongsTo(User::class, 'id_usuario_aprueba', 'usuario_id');
    }
}

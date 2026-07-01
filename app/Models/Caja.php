<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'cajas';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'id_empresa',
        'sucursal',
        'id_usuario_responsable',
        'id_caja_padre',
        'saldo_actual',
        'moneda',
        'estado',
    ];
    protected $casts = [
        'saldo_actual'      => 'float',
        'monto_fondo_fijo'  => 'float',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'id_usuario_responsable', 'usuario_id');
    }

    public function padre()
    {
        return $this->belongsTo(Caja::class, 'id_caja_padre', 'id');
    }

    public function hijas()
    {
        return $this->hasMany(Caja::class, 'id_caja_padre', 'id');
    }

    public function movimientos()
    {
        return $this->hasMany(CajaMovimiento::class, 'id_caja', 'id');
    }

    public function scopeDeEmpresa($query, $idEmpresa)
    {
        return $query->where('id_empresa', $idEmpresa);
    }
}

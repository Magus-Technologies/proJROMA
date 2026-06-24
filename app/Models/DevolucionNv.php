<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DevolucionNv extends Model
{
    protected $table      = 'devoluciones_nv';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','id_empresa','sucursal',
        'fecha','motivo','monto','id_usuario',
    ];

    protected $casts = ['monto' => 'float', 'fecha' => 'date'];
}

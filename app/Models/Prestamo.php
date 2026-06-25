<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $table      = 'prestamos';
    protected $primaryKey = 'id_prestamo';
    public    $timestamps = false;
    protected $fillable   = [
        'id_empresa', 'tipo', 'tercero', 'id_producto', 'almacen', 'cantidad',
        'estado', 'observacion', 'id_usuario', 'fecha', 'fecha_devolucion',
    ];
}

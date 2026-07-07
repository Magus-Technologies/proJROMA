<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\Auditable;

class Prestamo extends Model
{
    use Auditable;
    protected $table      = 'prestamos';
    protected $primaryKey = 'id_prestamo';
    public    $timestamps = false;
    protected $fillable   = [
        'id_empresa', 'tipo', 'tercero', 'id_producto', 'almacen', 'cantidad',
        'estado', 'observacion', 'id_usuario', 'fecha', 'fecha_devolucion',
    ];
}

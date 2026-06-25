<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventarioMovimiento extends Model
{
    protected $table      = 'inventario_movimientos';
    protected $primaryKey = 'id_movimiento';
    public    $timestamps = false;
    protected $fillable   = [
        'id_empresa', 'almacen', 'id_producto', 'tipo', 'id_motivo', 'cantidad',
        'stock_anterior', 'stock_nuevo', 'costo', 'id_proveedor', 'observacion',
        'id_usuario', 'fecha',
    ];
}

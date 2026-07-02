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

    public function producto() { return $this->belongsTo(Producto::class,'id_producto','id_producto'); }
    public function motivo()   { return $this->belongsTo(MotivoMovimiento::class,'id_motivo','id_motivo'); }
    public function usuario()  { return $this->belongsTo(User::class,'id_usuario','usuario_id'); }
}

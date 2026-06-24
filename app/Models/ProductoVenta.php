<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductoVenta extends Model
{
    protected $table      = 'productos_ventas';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','id_producto','descripcion',
        'cantidad','precio','total','igv_prod','descuento',
    ];

    protected $casts = ['precio'=>'float','total'=>'float','cantidad'=>'float'];

    public function venta()    { return $this->belongsTo(Venta::class,'id_venta','id_venta'); }
    public function producto() { return $this->belongsTo(Producto::class,'id_producto','id_producto'); }
}

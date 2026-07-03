<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductoCoti extends Model
{
    protected $table      = 'productos_cotis';
    protected $primaryKey = 'prod_coti_id';
    public    $timestamps = false;
    protected $fillable   = ['id_coti','id_producto','cantidad','precio','costo','medida','presenta','presenta_cnt','fecha_registro','id_usuario'];
    protected $casts      = ['precio'=>'float','cantidad'=>'float','costo'=>'float','fecha_registro'=>'datetime'];

    public function producto() { return $this->belongsTo(Producto::class,'id_producto','id_producto'); }
    public function cotizacion() { return $this->belongsTo(Cotizacion::class,'id_coti','cotizacion_id'); }
}

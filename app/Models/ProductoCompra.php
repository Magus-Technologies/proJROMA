<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductoCompra extends Model
{
    protected $table      = 'productos_compras';
    public    $timestamps = false;
    protected $fillable   = ['id_compra','id_producto','descripcion','cantidad','precio','total'];
    protected $casts      = ['precio'=>'float','total'=>'float'];
}

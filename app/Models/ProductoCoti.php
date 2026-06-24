<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductoCoti extends Model
{
    protected $table      = 'productos_cotis';
    public    $timestamps = false;
    protected $fillable   = ['id_coti','id_producto','descripcion','cantidad','precio','total'];
    protected $casts      = ['precio'=>'float','total'=>'float','cantidad'=>'float'];
}

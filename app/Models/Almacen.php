<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table      = 'almacenes';
    protected $primaryKey = 'id_almacen';
    public    $timestamps = false;
    protected $fillable   = ['nombre', 'codigo', 'descripcion', 'id_sucursal', 'id_empresa', 'estado'];
}

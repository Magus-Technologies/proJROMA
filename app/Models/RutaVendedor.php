<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RutaVendedor extends Model
{
    protected $table      = 'rutas_vendedor';
    protected $primaryKey = 'id_ruta';
    public    $timestamps = false;
    protected $fillable   = ['nombre','id_empresa','id_usuario'];
}

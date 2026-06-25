<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    protected $table      = 'traslados';
    protected $primaryKey = 'id_traslado';
    public    $timestamps = false;
    protected $fillable   = ['id_empresa', 'almacen_origen', 'almacen_destino', 'fecha', 'observacion', 'id_usuario', 'estado'];
}

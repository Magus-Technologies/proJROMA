<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table      = 'sucursales';
    protected $primaryKey = 'id_sucursal';
    public    $timestamps = false;
    protected $fillable   = [
        'empresa_id', 'cod_sucursal', 'nombre', 'direccion',
        'distrito', 'provincia', 'departamento', 'ubigeo', 'estado',
    ];
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GuiaDetalle extends Model
{
    protected $table      = 'guia_detalles';
    public    $timestamps = false;

    protected $fillable = [
        'id_guia','id_producto','descripcion',
        'cantidad','unidad','codigo_producto',
    ];
}

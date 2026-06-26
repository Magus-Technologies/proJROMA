<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuiaDetalle extends Model
{
    protected $table      = 'guia_detalles';
    protected $primaryKey = 'guia_detalle_id';
    public    $timestamps = false;

    protected $fillable = [
        'id_guia','id_producto','detalles','unidad','cantidad','precio',
    ];
}

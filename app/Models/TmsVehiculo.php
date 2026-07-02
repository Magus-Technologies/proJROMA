<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmsVehiculo extends Model
{
    protected $table = 'tms_vehiculos';
    protected $guarded = [];

    protected $casts = [
        'capacidad_kg'      => 'decimal:2',
        'tara_kg'           => 'decimal:2',
        'largo_m'           => 'decimal:2',
        'ancho_m'           => 'decimal:2',
        'alto_m'            => 'decimal:2',
        'capacidad_m3'      => 'decimal:2',
        'soat_vence'        => 'date',
        'rev_tecnica_vence' => 'date',
        'estado'            => 'integer',
    ];
}

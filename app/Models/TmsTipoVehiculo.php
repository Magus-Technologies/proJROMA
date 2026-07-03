<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmsTipoVehiculo extends Model
{
    protected $table = 'tms_tipos_vehiculo';
    protected $guarded = [];

    protected $casts = [
        'estado' => 'integer',
    ];
}

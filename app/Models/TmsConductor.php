<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmsConductor extends Model
{
    protected $table = 'tms_conductores';
    protected $guarded = [];

    protected $casts = [
        'licencia_vence' => 'date',
        'estado'         => 'integer',
    ];
}

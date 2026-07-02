<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presentacion extends Model
{
    protected $table = 'presentaciones';
    protected $guarded = [];

    protected $casts = [
        'estado' => 'integer',
    ];
}

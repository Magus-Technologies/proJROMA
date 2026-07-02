<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmsMercado extends Model
{
    protected $table = 'tms_mercados';
    protected $guarded = [];

    protected $casts = [
        'estado' => 'integer',
    ];
}

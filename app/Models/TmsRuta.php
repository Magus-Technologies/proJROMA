<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TmsRuta extends Model
{
    protected $table = 'tms_rutas';
    protected $guarded = [];

    protected $casts = [
        'estado' => 'integer',
    ];

    public function puntos(): HasMany
    {
        return $this->hasMany(TmsRutaPunto::class, 'id_ruta')->orderBy('orden');
    }
}

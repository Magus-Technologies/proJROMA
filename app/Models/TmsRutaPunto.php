<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TmsRutaPunto extends Model
{
    protected $table = 'tms_ruta_puntos';
    protected $guarded = [];
    public $timestamps = false;

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(TmsRuta::class, 'id_ruta');
    }

    public function mercado(): BelongsTo
    {
        return $this->belongsTo(TmsMercado::class, 'id_mercado');
    }
}

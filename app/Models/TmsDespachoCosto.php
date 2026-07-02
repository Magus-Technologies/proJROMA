<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TmsDespachoCosto extends Model
{
    protected $table = 'tms_despacho_costos';
    protected $guarded = [];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function despacho(): BelongsTo
    {
        return $this->belongsTo(TmsDespacho::class, 'id_despacho');
    }
}

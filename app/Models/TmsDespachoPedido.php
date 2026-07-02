<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TmsDespachoPedido extends Model
{
    protected $table = 'tms_despacho_pedidos';
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'peso'  => 'decimal:2',
        'monto' => 'decimal:2',
    ];

    public function despacho(): BelongsTo
    {
        return $this->belongsTo(TmsDespacho::class, 'id_despacho');
    }
}

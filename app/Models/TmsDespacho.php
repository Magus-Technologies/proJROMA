<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TmsDespacho extends Model
{
    protected $table = 'tms_despachos';
    protected $guarded = [];

    protected $casts = [
        'fecha_reparto' => 'date',
        'peso_total'    => 'decimal:2',
    ];

    public function ruta(): BelongsTo      { return $this->belongsTo(TmsRuta::class, 'id_ruta'); }
    public function vehiculo(): BelongsTo  { return $this->belongsTo(TmsVehiculo::class, 'id_vehiculo'); }
    public function conductor(): BelongsTo { return $this->belongsTo(TmsConductor::class, 'id_conductor'); }
    public function pedidos(): HasMany     { return $this->hasMany(TmsDespachoPedido::class, 'id_despacho')->orderBy('orden'); }
    public function costos(): HasMany      { return $this->hasMany(TmsDespachoCosto::class, 'id_despacho'); }
}

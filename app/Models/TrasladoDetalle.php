<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TrasladoDetalle extends Model
{
    protected $table      = 'traslado_detalle';
    protected $primaryKey = 'id_detalle';
    public    $timestamps = false;
    protected $fillable   = [
        'id_traslado', 'id_producto', 'cantidad', 'costo',
        'stock_ant_origen', 'stock_nuevo_origen',
        'stock_ant_destino', 'stock_nuevo_destino', 'estado',
    ];

    public function traslado() { return $this->belongsTo(Traslado::class,'id_traslado','id_traslado'); }
    public function producto() { return $this->belongsTo(Producto::class,'id_producto','id_producto'); }
}

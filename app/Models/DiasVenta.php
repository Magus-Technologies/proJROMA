<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DiasVenta extends Model
{
    protected $table      = 'dias_ventas';
    protected $primaryKey = 'dias_venta_id';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','fecha','monto','estado',
        'tipo_pago','id_usuario','id_caja_empresa','fecha_pago_real',
    ];

    protected $casts = ['monto'=>'float','fecha'=>'date','fecha_pago_real'=>'date'];

    public function venta()   { return $this->belongsTo(Venta::class,'id_venta','id_venta'); }
    public function usuario() { return $this->belongsTo(User::class,'id_usuario','usuario_id'); }
}

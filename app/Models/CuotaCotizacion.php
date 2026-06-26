<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CuotaCotizacion extends Model
{
    protected $table      = 'cuotas_cotizacion';
    protected $primaryKey = 'cuota_coti_id';
    public    $timestamps = false;
    protected $fillable   = ['id_coti','id_usuario','id_caja_empresa','monto','fecha','estado','tipo_pago','fecha_pago_real'];
    protected $casts      = ['monto'=>'float','fecha'=>'date'];

    public function usuario() { return $this->belongsTo(User::class,'id_usuario','usuario_id'); }
}

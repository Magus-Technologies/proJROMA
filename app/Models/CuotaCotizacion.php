<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CuotaCotizacion extends Model
{
    protected $table      = 'cuotas_cotizacion';
    protected $primaryKey = 'id';
    public    $timestamps = false;
    protected $fillable   = ['id_coti','fecha','monto','estado','tipo_pago','id_usuario','fecha_pago_real'];
    protected $casts      = ['monto'=>'float','fecha'=>'date'];
}

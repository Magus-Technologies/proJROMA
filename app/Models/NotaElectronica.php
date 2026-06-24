<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotaElectronica extends Model
{
    protected $table      = 'notas_electronicas';
    protected $primaryKey = 'id_nota';
    public    $timestamps = false;
    protected $fillable   = ['id_venta','tipo','motivo','id_empresa','sucursal','serie','numero','total','estado','enviado_sunat'];
    public function venta() { return $this->belongsTo(Venta::class,'id_venta','id_venta'); }
}

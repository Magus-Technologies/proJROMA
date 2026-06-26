<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotaElectronica extends Model
{
    protected $table      = 'notas_electronicas';
    protected $primaryKey = 'nota_id';
    public    $timestamps = false;
    protected $fillable   = ['id_venta','tipo','cod_motivo','motivo','id_empresa','sucursal','serie','numero','total','fecha_emision','estado','enviado_sunat','hash','nombre_xml'];
    protected $casts      = ['fecha_emision' => 'date'];
    public function venta()   { return $this->belongsTo(Venta::class,'id_venta','id_venta'); }
    public function empresa() { return $this->belongsTo(Empresa::class,'id_empresa','id_empresa'); }
}

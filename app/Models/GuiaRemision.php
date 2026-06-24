<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GuiaRemision extends Model
{
    protected $table      = 'guia_remision';
    protected $primaryKey = 'id_guia';
    public    $timestamps = false;
    protected $fillable   = ['serie','numero','fecha','id_empresa','sucursal','id_cliente','motivo','peso_total','estado','enviado_sunat','id_cotizacion','observacion'];
    protected $casts      = ['fecha'=>'date'];
    public function cliente()  { return $this->belongsTo(Cliente::class,   'id_cliente','id_cliente'); }
    public function detalles() { return $this->hasMany(GuiaDetalle::class,  'id_guia',   'id_guia'); }
}

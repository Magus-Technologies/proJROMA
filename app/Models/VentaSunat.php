<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VentaSunat extends Model
{
    protected $table    = 'ventas_sunat';
    public $timestamps  = false;
    protected $fillable = ['id_venta','estado_sunat','codigo_sunat','descripcion_sunat','xml','cdr'];
    public function venta() { return $this->belongsTo(Venta::class,'id_venta','id_venta'); }
}

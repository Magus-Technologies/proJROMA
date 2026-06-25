<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IngresoEgreso extends Model
{
    protected $table    = 'ingreso_egreso';
    public $timestamps  = false;
    protected $fillable = ['id_empresa','sucursal','fecha','tipo','descripcion','monto','id_usuario','instrumento_tipo','instrumento_id'];
    protected $casts    = ['monto'=>'float','fecha'=>'date'];
}

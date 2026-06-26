<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DiasCompra extends Model
{
    protected $table    = 'dias_compras';
    public $timestamps  = false;
    protected $fillable = ['id_compra','fecha','monto','estado','tipo_pago','id_caja','instrumento_tipo','instrumento_id'];
    protected $casts    = ['monto'=>'float','fecha'=>'date'];
}

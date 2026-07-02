<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DiasCompra extends Model
{
    protected $table      = 'dias_compras';
    protected $primaryKey = 'dias_compra_id';
    public $timestamps    = false;
    protected $fillable = ['id_compra','fecha','monto','estado','id_caja','instrumento_tipo','instrumento_id'];
    protected $casts    = ['monto'=>'float','fecha'=>'date'];

    public function compra() { return $this->belongsTo(Compra::class,'id_compra','id_compra'); }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Recepcion extends Model
{
    protected $table      = 'recepciones';
    protected $primaryKey = 'id_recepcion';
    public    $timestamps = false;
    protected $fillable   = ['id_empresa','id_compra','almacen','fecha','observacion','id_usuario'];
    protected $casts      = ['fecha' => 'date'];

    public function compra()  { return $this->belongsTo(Compra::class,'id_compra','id_compra'); }
    public function usuario() { return $this->belongsTo(User::class,'id_usuario','usuario_id'); }
}

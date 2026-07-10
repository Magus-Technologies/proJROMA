<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\Auditable;

class Traslado extends Model
{
    use Auditable;
    protected $table      = 'traslados';
    protected $primaryKey = 'id_traslado';
    public    $timestamps = false;
    protected $fillable   = ['id_empresa', 'almacen_origen', 'almacen_destino', 'fecha', 'observacion', 'id_usuario', 'estado'];

    protected $casts = ['fecha' => 'datetime'];

    public function detalles() { return $this->hasMany(TrasladoDetalle::class,'id_traslado','id_traslado'); }
    public function usuario()  { return $this->belongsTo(User::class,'id_usuario','usuario_id'); }
}

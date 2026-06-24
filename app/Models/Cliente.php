<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Cliente extends Model
{
    protected $table      = 'clientes';
    protected $primaryKey = 'id_cliente';
    public    $timestamps = false;

    protected $fillable = [
        'documento','datos','direccion','distrito','telefono',
        'dias_visitas','email','id_empresa','ultima_venta',
        'total_venta','id_ruta','mercado',
    ];

    protected $casts = ['ultima_venta'=>'date','total_venta'=>'float'];

    public function empresa() { return $this->belongsTo(Empresa::class,'id_empresa','id_empresa'); }
    public function ventas()  { return $this->hasMany(Venta::class,'id_cliente','id_cliente'); }
    public function ruta()    { return $this->belongsTo(RutaVendedor::class,'id_ruta','id_ruta'); }

    public function scopeDeEmpresa(Builder $q, int $id): Builder
    {
        return $q->where('id_empresa', $id);
    }

    public function scopeBuscar(Builder $q, string $t): Builder
    {
        return $q->where(function($s) use ($t) {
            $s->where('datos','like',"%{$t}%")
              ->orWhere('documento','like',"%{$t}%")
              ->orWhere('telefono','like',"%{$t}%");
        });
    }
}

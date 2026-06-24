<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Producto extends Model
{
    protected $table      = 'productos';
    protected $primaryKey = 'id_producto';
    public    $timestamps = false;

    protected $fillable = [
        'cod_barra','descripcion','precio','costo','cantidad','iscbp',
        'id_empresa','sucursal','ultima_salida','codsunat','usar_barra',
        'precio_mayor','precio_menor','peso_bruto','razon_social','ruc',
        'estado','almacen','precio2','precio3','precio4','precio_unidad',
        'codigo','activo',
    ];

    protected $casts = [
        'precio'        => 'float',
        'costo'         => 'float',
        'precio2'       => 'float',
        'precio3'       => 'float',
        'precio4'       => 'float',
        'precio_unidad' => 'float',
        'precio_mayor'  => 'float',
        'precio_menor'  => 'float',
        'cantidad'      => 'integer',
        'ultima_salida' => 'date',
    ];

    public function scopeActivos(Builder $q): Builder             { return $q->where('estado','1'); }
    public function scopeDeEmpresa(Builder $q, int $id): Builder  { return $q->where('id_empresa',$id); }
    public function scopeDeSucursal(Builder $q, int $s): Builder  { return $q->where('sucursal',$s); }
    public function scopeBajoStock(Builder $q, int $min=5): Builder{ return $q->where('cantidad','<=',$min); }
}

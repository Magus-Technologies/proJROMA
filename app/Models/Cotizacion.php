<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Cotizacion extends Model
{
    protected $table      = 'cotizaciones';
    protected $primaryKey = 'cotizacion_id';
    public    $timestamps = false;

    protected $fillable = [
        'numero','id_tido','id_tipo_pago','fecha','dias_pagos','direccion',
        'id_cliente','total','estado','id_empresa','sucursal','usar_precio',
        'moneda','cm_tc','id_usuario','observacion','fecha_registro','id_venta',
    ];

    protected $casts = ['total'=>'float','fecha'=>'date'];

    public function cliente()  { return $this->belongsTo(Cliente::class,'id_cliente','id_cliente'); }
    public function usuario()  { return $this->belongsTo(User::class,'id_usuario','usuario_id'); }
    public function productos(){ return $this->hasMany(ProductoCoti::class,'id_coti','cotizacion_id'); }
    public function cuotas()   { return $this->hasMany(CuotaCotizacion::class,'id_coti','cotizacion_id'); }
    public function venta()    { return $this->belongsTo(Venta::class,'id_venta','id_venta'); }

    public function scopeDeEmpresa(Builder $q, int $id): Builder { return $q->where('id_empresa',$id); }
    public function scopePendientes(Builder $q): Builder         { return $q->where('estado','1'); }
}

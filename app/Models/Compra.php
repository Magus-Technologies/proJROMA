<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Compra extends Model
{
    protected $table      = 'compras';
    protected $primaryKey = 'id_compra';
    public    $timestamps = false;

    protected $fillable = [
        'id_tido','id_tipo_pago','id_proveedor','fecha_emision',
        'fecha_vencimiento','dias_pagos','direccion','serie',
        'numero','total','id_empresa','moneda','sucursal',
    ];

    protected $casts = ['total'=>'float'];

    public function proveedor()     { return $this->belongsTo(Proveedor::class,'id_proveedor','proveedor_id'); }
    public function tipoDocumento() { return $this->belongsTo(DocumentoEmpresa::class,'id_tido','id_tido'); }
    public function empresa()   { return $this->belongsTo(Empresa::class,'id_empresa','id_empresa'); }
    public function productos() { return $this->hasMany(ProductoCompra::class,'id_compra','id_compra'); }

    public function scopeDeEmpresa(Builder $q, int $id): Builder { return $q->where('id_empresa',$id); }
    public function scopeDelMes(Builder $q): Builder
    {
        return $q->whereMonth('fecha_emision', now()->month)
                 ->whereYear('fecha_emision',  now()->year);
    }
}

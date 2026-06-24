<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Venta extends Model
{
    protected $table      = 'ventas';
    protected $primaryKey = 'id_venta';
    public    $timestamps = false;

    protected $fillable = [
        'id_tido','id_tipo_pago','fecha_emision','fecha_vencimiento',
        'dias_pagos','direccion','serie','numero','id_cliente','total',
        'subtotal','estado','enviado_sunat','id_empresa','sucursal',
        'apli_igv','observacion','igv','medoto_pago_id','pagado',
        'id_vendedor','id_cotizacion',
    ];

    protected $casts = [
        'total'=>'float','subtotal'=>'float','igv'=>'float',
        'fecha_emision'=>'date','fecha_vencimiento'=>'date',
    ];

    public function cliente()        { return $this->belongsTo(Cliente::class,'id_cliente','id_cliente'); }
    public function empresa()        { return $this->belongsTo(Empresa::class,'id_empresa','id_empresa'); }
    public function vendedor()       { return $this->belongsTo(User::class,'id_vendedor','usuario_id'); }
    public function tipoDocumento()  { return $this->belongsTo(DocumentoEmpresa::class,'id_tido','id_tido'); }
    public function productosVenta() { return $this->hasMany(ProductoVenta::class,'id_venta','id_venta'); }
    public function pagos()          { return $this->hasMany(DiasVenta::class,'id_venta','id_venta'); }
    public function sunat()          { return $this->hasOne(VentaSunat::class,'id_venta','id_venta'); }

    public function scopeDeEmpresa(Builder $q, int $id): Builder { return $q->where('id_empresa',$id); }
    public function scopeDeSucursal(Builder $q, int $s): Builder { return $q->where('sucursal',$s); }
    public function scopeActivas(Builder $q): Builder            { return $q->where('estado','1'); }
    public function scopeDelMes(Builder $q): Builder
    {
        return $q->whereMonth('fecha_emision', now()->month)
                 ->whereYear('fecha_emision',  now()->year);
    }

    public function getDocumentoCompletoAttribute(): string
    {
        return "{$this->serie}-" . str_pad($this->numero, 8, '0', STR_PAD_LEFT);
    }
}

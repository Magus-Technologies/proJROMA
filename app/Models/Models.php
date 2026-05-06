<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

// ══════════════════════════════════════════════════════════════════════════════
// EMPRESA
// ══════════════════════════════════════════════════════════════════════════════
class Empresa extends Model
{
    protected $table      = 'empresas';
    protected $primaryKey = 'id_empresa';
    public    $timestamps = false;

    protected $fillable = [
        'ruc','razon_social','comercial','cod_sucursal','direccion',
        'email','telefono','estado','password','user_sol','clave_sol',
        'logo','ubigeo','distrito',
    ];

    public function usuarios()  { return $this->hasMany(User::class,    'id_empresa', 'id_empresa'); }
    public function clientes()  { return $this->hasMany(Cliente::class, 'id_empresa', 'id_empresa'); }
    public function productos() { return $this->hasMany(Producto::class,'id_empresa', 'id_empresa'); }
}

// ══════════════════════════════════════════════════════════════════════════════
// ROL
// ══════════════════════════════════════════════════════════════════════════════
class Rol extends Model
{
    protected $table      = 'roles';
    protected $primaryKey = 'rol_id';
    public    $timestamps = false;

    protected $fillable = ['nombre'];
}

// ══════════════════════════════════════════════════════════════════════════════
// CLIENTE
// ══════════════════════════════════════════════════════════════════════════════
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

    protected $casts = [
        'ultima_venta' => 'date',
        'total_venta'  => 'float',
    ];

    public function empresa() { return $this->belongsTo(Empresa::class,      'id_empresa', 'id_empresa'); }
    public function ventas()  { return $this->hasMany(Venta::class,          'id_cliente', 'id_cliente'); }
    public function ruta()    { return $this->belongsTo(RutaVendedor::class, 'id_ruta',    'id_ruta'); }

    public function scopeDeEmpresa(Builder $q, int $id): Builder { return $q->where('id_empresa', $id); }
    public function scopeBuscar(Builder $q, string $t): Builder {
        return $q->where(fn($s) => $s->where('datos','like',"%$t%")->orWhere('documento','like',"%$t%")->orWhere('telefono','like',"%$t%"));
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// PRODUCTO
// ══════════════════════════════════════════════════════════════════════════════
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

    public function empresa() { return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa'); }

    public function scopeActivos(Builder $q): Builder     { return $q->where('estado','1'); }
    public function scopeDeEmpresa(Builder $q, int $id): Builder { return $q->where('id_empresa', $id); }
    public function scopeDeSucursal(Builder $q, int $s): Builder { return $q->where('sucursal', $s); }
    public function scopeBajoStock(Builder $q, int $min = 5): Builder { return $q->where('cantidad','<=',$min); }
}

// ══════════════════════════════════════════════════════════════════════════════
// PROVEEDOR
// ══════════════════════════════════════════════════════════════════════════════
class Proveedor extends Model
{
    protected $table      = 'proveedores';
    protected $primaryKey = 'proveedor_id';
    public    $timestamps = false;

    protected $fillable = [
        'num_doc','nombre','nombre_comercial','direccion',
        'telefono','email','id_empresa',
    ];

    public function compras() { return $this->hasMany(Compra::class, 'id_proveedor', 'proveedor_id'); }
}

// ══════════════════════════════════════════════════════════════════════════════
// VENTA
// ══════════════════════════════════════════════════════════════════════════════
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
        'total'            => 'float',
        'subtotal'         => 'float',
        'igv'              => 'float',
        'fecha_emision'    => 'date',
        'fecha_vencimiento'=> 'date',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────
    public function cliente()        { return $this->belongsTo(Cliente::class,        'id_cliente',  'id_cliente'); }
    public function empresa()        { return $this->belongsTo(Empresa::class,        'id_empresa',  'id_empresa'); }
    public function vendedor()       { return $this->belongsTo(User::class,           'id_vendedor', 'usuario_id'); }
    public function tipoDocumento()  { return $this->belongsTo(DocumentoEmpresa::class,'id_tido',    'id_tido'); }
    public function productosVenta() { return $this->hasMany(ProductoVenta::class,    'id_venta',    'id_venta'); }
    public function pagos()          { return $this->hasMany(DiasVenta::class,        'id_venta',    'id_venta'); }
    public function sunat()          { return $this->hasOne(VentaSunat::class,        'id_venta',    'id_venta'); }

    // ── Scopes ────────────────────────────────────────────────────────────
    public function scopeDeEmpresa(Builder $q, int $id): Builder  { return $q->where('id_empresa', $id); }
    public function scopeDeSucursal(Builder $q, int $s): Builder  { return $q->where('sucursal', $s); }
    public function scopeActivas(Builder $q): Builder             { return $q->where('estado', '1'); }
    public function scopeDelMes(Builder $q): Builder {
        return $q->whereMonth('fecha_emision', now()->month)
                 ->whereYear('fecha_emision',  now()->year);
    }

    // ── Accessor Laravel 13 style ─────────────────────────────────────────
    public function getDocumentoCompletoAttribute(): string
    {
        return "{$this->serie}-" . str_pad($this->numero, 8, '0', STR_PAD_LEFT);
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// PRODUCTO_VENTA (detalle de venta)
// ══════════════════════════════════════════════════════════════════════════════
class ProductoVenta extends Model
{
    protected $table      = 'productos_ventas';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','id_producto','descripcion',
        'cantidad','precio','total','igv_prod','descuento',
    ];

    protected $casts = [
        'precio'   => 'float',
        'total'    => 'float',
        'cantidad' => 'float',
        'descuento'=> 'float',
    ];

    public function venta()    { return $this->belongsTo(Venta::class,   'id_venta',    'id_venta'); }
    public function producto() { return $this->belongsTo(Producto::class,'id_producto', 'id_producto'); }
}

// ══════════════════════════════════════════════════════════════════════════════
// DIAS_VENTAS (pagos parciales de venta)
// ══════════════════════════════════════════════════════════════════════════════
class DiasVenta extends Model
{
    protected $table      = 'dias_ventas';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','fecha','monto','estado',
        'tipo_pago','id_usuario','fecha_pago_real',
    ];

    protected $casts = [
        'monto'          => 'float',
        'fecha'          => 'date',
        'fecha_pago_real'=> 'date',
    ];

    public function venta() { return $this->belongsTo(Venta::class, 'id_venta', 'id_venta'); }

    public function scopePagados(Builder $q): Builder { return $q->where('estado', '1'); }
}

// ══════════════════════════════════════════════════════════════════════════════
// COTIZACION
// ══════════════════════════════════════════════════════════════════════════════
class Cotizacion extends Model
{
    protected $table      = 'cotizaciones';
    protected $primaryKey = 'cotizacion_id';
    public    $timestamps = false;

    protected $fillable = [
        'numero','id_tido','id_tipo_pago','fecha','dias_pagos',
        'direccion','id_cliente','total','estado','id_empresa',
        'sucursal','usar_precio','moneda','cm_tc','id_usuario',
        'observacion','fecha_registro',
    ];

    protected $casts = [
        'total'          => 'float',
        'fecha'          => 'date',
        'fecha_registro' => 'datetime',
    ];

    public function cliente()  { return $this->belongsTo(Cliente::class,     'id_cliente', 'id_cliente'); }
    public function usuario()  { return $this->belongsTo(User::class,        'id_usuario', 'usuario_id'); }
    public function productos(){ return $this->hasMany(ProductoCoti::class,  'id_coti',    'cotizacion_id'); }
    public function cuotas()   { return $this->hasMany(CuotaCotizacion::class,'id_coti',  'cotizacion_id'); }

    public function scopeDeEmpresa(Builder $q, int $id): Builder { return $q->where('id_empresa', $id); }
    public function scopePendientes(Builder $q): Builder         { return $q->where('estado', '1'); }
}

// ══════════════════════════════════════════════════════════════════════════════
// CUOTA COTIZACION
// ══════════════════════════════════════════════════════════════════════════════
class CuotaCotizacion extends Model
{
    protected $table      = 'cuotas_cotizacion';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_coti','fecha','monto','estado',
        'tipo_pago','id_usuario','fecha_pago_real',
    ];

    protected $casts = [
        'monto'          => 'float',
        'fecha'          => 'date',
        'fecha_pago_real'=> 'date',
    ];

    public function cotizacion() { return $this->belongsTo(Cotizacion::class,'id_coti','cotizacion_id'); }
}

// ══════════════════════════════════════════════════════════════════════════════
// COMPRA
// ══════════════════════════════════════════════════════════════════════════════
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

    protected $casts = ['total' => 'float'];

    public function proveedor() { return $this->belongsTo(Proveedor::class,'id_proveedor','proveedor_id'); }
    public function empresa()   { return $this->belongsTo(Empresa::class,  'id_empresa',  'id_empresa'); }
    public function productos() { return $this->hasMany(ProductoCompra::class,'id_compra','id_compra'); }
    public function pagos()     { return $this->hasMany(DiasCompra::class,   'id_compra', 'id_compra'); }

    public function scopeDeEmpresa(Builder $q, int $id): Builder { return $q->where('id_empresa', $id); }
    public function scopeDelMes(Builder $q): Builder {
        return $q->whereMonth('fecha_emision', now()->month)
                 ->whereYear('fecha_emision',  now()->year);
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// PRODUCTO_COMPRA
// ══════════════════════════════════════════════════════════════════════════════
class ProductoCompra extends Model
{
    protected $table      = 'productos_compras';
    public    $timestamps = false;

    protected $fillable = [
        'id_compra','id_producto','descripcion',
        'cantidad','precio','total',
    ];

    protected $casts = ['precio' => 'float', 'total' => 'float'];
}

// ══════════════════════════════════════════════════════════════════════════════
// DIAS_COMPRAS
// ══════════════════════════════════════════════════════════════════════════════
class DiasCompra extends Model
{
    protected $table      = 'dias_compras';
    public    $timestamps = false;

    protected $fillable = ['id_compra','fecha','monto','estado','tipo_pago'];
    protected $casts    = ['monto' => 'float', 'fecha' => 'date'];
}

// ══════════════════════════════════════════════════════════════════════════════
// GUIA DE REMISION
// ══════════════════════════════════════════════════════════════════════════════
class GuiaRemision extends Model
{
    protected $table      = 'guia_remision';
    protected $primaryKey = 'id_guia';
    public    $timestamps = false;

    protected $fillable = [
        'serie','numero','fecha','id_empresa','sucursal','id_cliente',
        'motivo','peso_total','estado','enviado_sunat','id_cotizacion','observacion',
    ];

    protected $casts = ['fecha' => 'date'];

    public function cliente()  { return $this->belongsTo(Cliente::class,    'id_cliente','id_cliente'); }
    public function detalles() { return $this->hasMany(GuiaDetalle::class,  'id_guia',   'id_guia'); }
}

// ══════════════════════════════════════════════════════════════════════════════
// GUIA DETALLE
// ══════════════════════════════════════════════════════════════════════════════
class GuiaDetalle extends Model
{
    protected $table      = 'guia_detalles';
    public    $timestamps = false;

    protected $fillable = [
        'id_guia','id_producto','descripcion',
        'cantidad','unidad','codigo_producto',
    ];
}

// ══════════════════════════════════════════════════════════════════════════════
// DOCUMENTO EMPRESA (tipos de comprobante: F, B, NV, etc.)
// ══════════════════════════════════════════════════════════════════════════════
class DocumentoEmpresa extends Model
{
    protected $table      = 'documentos_empresas';
    protected $primaryKey = 'id_tido';
    public    $timestamps = false;

    protected $fillable = ['id_empresa','tipo_doc','serie','numero','sucursal'];
}

// ══════════════════════════════════════════════════════════════════════════════
// VENTA SUNAT
// ══════════════════════════════════════════════════════════════════════════════
class VentaSunat extends Model
{
    protected $table      = 'ventas_sunat';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','estado_sunat','codigo_sunat',
        'descripcion_sunat','xml','cdr',
    ];

    public function venta() { return $this->belongsTo(Venta::class,'id_venta','id_venta'); }
}

// ══════════════════════════════════════════════════════════════════════════════
// RUTA VENDEDOR
// ══════════════════════════════════════════════════════════════════════════════
class RutaVendedor extends Model
{
    protected $table      = 'rutas_vendedor';
    protected $primaryKey = 'id_ruta';
    public    $timestamps = false;

    protected $fillable = ['nombre','id_empresa','id_usuario'];
}

// ══════════════════════════════════════════════════════════════════════════════
// CAJA EMPRESA
// ══════════════════════════════════════════════════════════════════════════════
class CajaEmpresa extends Model
{
    protected $table      = 'caja_empresa';
    public    $timestamps = false;

    protected $fillable = [
        'id_empresa','sucursal','fecha','tipo',
        'descripcion','monto','id_usuario',
    ];

    protected $casts = ['monto' => 'float', 'fecha' => 'date'];
}

// ══════════════════════════════════════════════════════════════════════════════
// NOTA ELECTRONICA
// ══════════════════════════════════════════════════════════════════════════════
class NotaElectronica extends Model
{
    protected $table      = 'notas_electronicas';
    protected $primaryKey = 'id_nota';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','tipo','motivo','id_empresa','sucursal',
        'serie','numero','total','estado','enviado_sunat',
    ];

    public function venta() { return $this->belongsTo(Venta::class,'id_venta','id_venta'); }
}

// ══════════════════════════════════════════════════════════════════════════════
// PRODUCTOS COTIZACION
// ══════════════════════════════════════════════════════════════════════════════
class ProductoCoti extends Model
{
    protected $table      = 'productos_cotis';
    public    $timestamps = false;

    protected $fillable = [
        'id_coti','id_producto','descripcion',
        'cantidad','precio','total',
    ];

    protected $casts = ['precio' => 'float', 'total' => 'float', 'cantidad' => 'float'];
}

// ══════════════════════════════════════════════════════════════════════════════
// ARQUEO DIARIO
// ══════════════════════════════════════════════════════════════════════════════
class ArqueoDiario extends Model
{
    protected $table      = 'arqueos_diarios';
    protected $primaryKey = 'arqueo_id';
    public    $timestamps = false;

    protected $fillable = [
        'id_empresa','sucursal','fecha_arqueo','vendedor','vendedor_id',
        'cobros_efectivo','cobros_bancos','ingresos_efectivo','ingresos_bancos',
        'egresos_efectivo','egresos_bancos','diferencia_efectivo','diferencia_bancos',
        'cuadra_efectivo','cuadra_bancos','usuario_registro','fecha_creacion',
    ];

    protected $casts = [
        'cobros_efectivo'     => 'float',
        'cobros_bancos'       => 'float',
        'ingresos_efectivo'   => 'float',
        'ingresos_bancos'     => 'float',
        'egresos_efectivo'    => 'float',
        'egresos_bancos'      => 'float',
        'diferencia_efectivo' => 'float',
        'diferencia_bancos'   => 'float',
        'cuadra_efectivo'     => 'boolean',
        'cuadra_bancos'       => 'boolean',
        'fecha_arqueo'        => 'date',
    ];
}

// ══════════════════════════════════════════════════════════════════════════════
// DEVOLUCION NV
// ══════════════════════════════════════════════════════════════════════════════
class DevolucionNv extends Model
{
    protected $table      = 'devoluciones_nv';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','id_empresa','sucursal',
        'fecha','motivo','monto','id_usuario',
    ];

    protected $casts = ['monto' => 'float', 'fecha' => 'date'];
}

// ══════════════════════════════════════════════════════════════════════════════
// INGRESO EGRESO (caja chica)
// ══════════════════════════════════════════════════════════════════════════════
class IngresoEgreso extends Model
{
    protected $table      = 'ingreso_egreso';
    public    $timestamps = false;

    protected $fillable = [
        'id_empresa','sucursal','fecha','tipo',
        'descripcion','monto','id_usuario',
    ];

    protected $casts = ['monto' => 'float', 'fecha' => 'date'];
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Traits\Auditable;

class Venta extends Model
{
    use Auditable;
    protected $table      = 'ventas';
    protected $primaryKey = 'id_venta';
    public    $timestamps = false;

    protected $fillable = [
        'id_tido','id_tipo_pago','fecha_emision','fecha_vencimiento',
        'dias_pagos','direccion','serie','numero','id_cliente','total',
        'subtotal','estado','enviado_sunat','id_empresa','sucursal',
        'apli_igv','tipo_igv','observacion','igv','medoto_pago_id','pagado',
        'firma',
        'metodo_pago','pago_referencia','pago_voucher',
        'id_vendedor','id_coti',
        'sunat_estado','sunat_mensaje','hash_cpe','xml_ruta','cdr_ruta',
    ];

    protected $casts = [
        'total'=>'float','subtotal'=>'float','igv'=>'float',
        'fecha_emision'=>'date','fecha_vencimiento'=>'date',
    ];

    public function cliente()        { return $this->belongsTo(Cliente::class,'id_cliente','id_cliente'); }
    public function empresa()        { return $this->belongsTo(Empresa::class,'id_empresa','id_empresa'); }
    public function vendedor()       { return $this->belongsTo(User::class,'id_vendedor','usuario_id'); }
    public function tipoDocumento()  { return $this->belongsTo(DocumentoEmpresa::class,'id_tido','id_tido'); }
    public function tipoDocSunat()   { return $this->belongsTo(DocumentoSunat::class,'id_tido','id_tido'); }
    public function productosVenta() { return $this->hasMany(ProductoVenta::class,'id_venta','id_venta'); }
    public function pagos()          { return $this->hasMany(DiasVenta::class,'id_venta','id_venta'); }
    public function pagosMetodos()   { return $this->hasMany(VentaPago::class,'id_venta','id_venta'); }

    /**
     * Todos los pagos recibidos por esta venta, listos para imprimir.
     *
     * Hay tres orígenes según la época del registro y se toman en ese orden,
     * nunca mezclados, para no contar dos veces el mismo dinero:
     *   1. venta_pagos — el desglose actual: contado mixto y abonos de cuotas.
     *   2. cxc_abonos  — cobros de crédito de antes de que existiera el desglose.
     *   3. dias_ventas — cuotas marcadas como pagadas, sin abono detallado.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function detallePagos(): \Illuminate\Support\Collection
    {
        $armar = fn (?string $metodo, $monto, ?string $referencia, $fecha): array =>
            \App\Services\CajaService::detalleMetodoPago($metodo) + [
                'monto'      => (float) $monto,
                'referencia' => $referencia ?: null,
                'fecha'      => $fecha ? \Carbon\Carbon::parse($fecha) : null,
            ];

        $pagos = $this->pagosMetodos
            ->map(fn (VentaPago $p): array => $armar($p->metodo_pago, $p->monto, $p->referencia, $p->created_at));

        if ($pagos->isNotEmpty()) {
            return $pagos->values();
        }

        $abonos = CxcAbono::where('id_venta', $this->id_venta)
            ->where('estado', 'ACTIVO')
            ->orderBy('fecha')
            ->get()
            ->map(fn (CxcAbono $a): array => $armar($a->metodo_pago, $a->monto, $a->referencia, $a->fecha));

        if ($abonos->isNotEmpty()) {
            return $abonos->values();
        }

        return $this->pagos
            ->where('estado', '1')
            ->map(fn (DiasVenta $c): array => $armar($c->tipo_pago, $c->monto, $c->referencia, $c->fecha_pago_real ?? $c->fecha))
            ->values();
    }
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

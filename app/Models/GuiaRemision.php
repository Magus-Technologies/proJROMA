<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\Auditable;

class GuiaRemision extends Model
{
    use Auditable;
    protected $table      = 'guia_remision';
    protected $primaryKey = 'id_guia_remision';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','fecha_emision','fecha_traslado','dir_llegada','ubigeo',
        'motivo_traslado','descripcion_motivo',
        'tipo_transporte','ruc_transporte','razon_transporte','transportista_nro_mtc',
        'vehiculo','chofer_brevete',
        'conductor_tipo_doc','conductor_documento','conductor_nombres',
        'conductor_apellidos','conductor_licencia',
        'und_peso_total','ubigeo_partida','dir_partida',
        'enviado_sunat','estado_gre','ticket_sunat','codigo_sunat','mensaje_sunat','cdr_url',
        'hash','nombre_xml','serie','numero','peso','nro_bultos',
        'estado','id_empresa','sucursal',
    ];

    protected $casts = [
        'fecha_emision'  => 'date',
        'fecha_traslado' => 'date',
    ];

    public function venta()    { return $this->belongsTo(Venta::class, 'id_venta', 'id_venta'); }
    public function empresa()  { return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa'); }
    public function detalles() { return $this->hasMany(GuiaDetalle::class, 'id_guia', 'id_guia_remision'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuiaRemision extends Model
{
    protected $table      = 'guia_remision';
    protected $primaryKey = 'id_guia_remision';
    public    $timestamps = false;

    protected $fillable = [
        'id_venta','fecha_emision','dir_llegada','ubigeo',
        'tipo_transporte','ruc_transporte','razon_transporte',
        'vehiculo','chofer_brevete','enviado_sunat','hash',
        'nombre_xml','serie','numero','peso','nro_bultos',
        'estado','id_empresa','sucursal',
    ];

    protected $casts = ['fecha_emision' => 'date'];

    public function venta()    { return $this->belongsTo(Venta::class, 'id_venta', 'id_venta'); }
    public function detalles() { return $this->hasMany(GuiaDetalle::class, 'id_guia', 'id_guia_remision'); }
}

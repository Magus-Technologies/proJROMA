<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

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
        'cobros_efectivo'=>'float','cobros_bancos'=>'float',
        'diferencia_efectivo'=>'float','diferencia_bancos'=>'float',
        'cuadra_efectivo'=>'boolean','cuadra_bancos'=>'boolean',
        'fecha_arqueo'=>'date',
    ];
}

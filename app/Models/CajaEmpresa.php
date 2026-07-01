<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CajaEmpresa extends Model
{
    protected $table      = 'caja_empresa';
    protected $primaryKey = 'caja_id';
    public $timestamps    = false;

    protected $fillable = [
        'id_empresa', 'sucursal', 'id_usuario', 'detalle',
        'fecha', 'entrada', 'salida', 'estado',
        'instrumento_tipo', 'instrumento_id',
    ];

    protected $casts = [
        'entrada' => 'float',
        'salida'  => 'float',
        'fecha'   => 'date',
    ];
}

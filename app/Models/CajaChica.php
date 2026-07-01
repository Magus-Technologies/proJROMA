<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CajaChica extends Model
{
    protected $table      = 'caja_chica';
    protected $primaryKey = 'caja_chica_id';
    public $timestamps    = false;

    protected $fillable = [
        'id_caja_empresa', 'hora', 'detalle',
        'tipo', 'entrada', 'salida', 'metodo',
    ];

    protected $casts = [
        'entrada' => 'float',
        'salida'  => 'float',
    ];
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MotivoMovimiento extends Model
{
    protected $table      = 'motivos_movimiento';
    protected $primaryKey = 'id_motivo';
    public    $timestamps = false;
    protected $fillable   = ['nombre', 'tipo', 'es_sistema', 'id_empresa', 'estado'];
}

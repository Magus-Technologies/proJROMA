<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    protected $table = 'tarjetas';
    protected $primaryKey = 'id_tarjeta';
    public $timestamps = true;
    protected $fillable = ['id_empresa', 'id_banco', 'id_cuenta_bancaria', 'tipo', 'marca', 'ultimos_4', 'titular', 'fecha_vencimiento', 'estado'];

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id_banco', 'id_banco');
    }

    public function cuentaBancaria()
    {
        return $this->belongsTo(CuentaBancaria::class, 'id_cuenta_bancaria', 'id_cuenta');
    }
}

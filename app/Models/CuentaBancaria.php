<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaBancaria extends Model
{
    protected $table = 'cuentas_bancarias';
    protected $primaryKey = 'id_cuenta';
    public $timestamps = true;
    protected $fillable = ['id_empresa', 'id_banco', 'tipo_cuenta', 'numero_cuenta', 'cci', 'moneda', 'titular', 'estado'];

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id_banco', 'id_banco');
    }
}

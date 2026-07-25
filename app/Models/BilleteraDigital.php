<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BilleteraDigital extends Model
{
    protected $table = 'billeteras_digitales';
    protected $primaryKey = 'id_billetera';
    public $timestamps = true;
    protected $fillable = ['id_empresa', 'id_billetera_tipo', 'id_cuenta_bancaria', 'telefono', 'titular', 'qr', 'estado'];

    public function billeteraTipo()
    {
        return $this->belongsTo(BilleteraTipo::class, 'id_billetera_tipo', 'id');
    }

    public function cuentaBancaria()
    {
        return $this->belongsTo(CuentaBancaria::class, 'id_cuenta_bancaria', 'id_cuenta');
    }
}

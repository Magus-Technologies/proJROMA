<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table      = 'empresas';
    protected $primaryKey = 'id_empresa';
    public    $timestamps = false;

    protected $fillable = [
        'ruc','razon_social','comercial','cod_sucursal','direccion',
        'email','telefono','telefono2','telefono3','estado','password',
        'user_sol','clave_sol','logo','ubigeo','distrito','provincia',
        'departamento','tipo_impresion','modo','igv','propaganda',
    ];

    public function usuarios() { return $this->hasMany(User::class,'id_empresa','id_empresa'); }
    public function clientes() { return $this->hasMany(Cliente::class,'id_empresa','id_empresa'); }
    public function productos(){ return $this->hasMany(Producto::class,'id_empresa','id_empresa'); }
}

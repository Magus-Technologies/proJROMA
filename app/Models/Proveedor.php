<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table      = 'proveedores';
    protected $primaryKey = 'proveedor_id';
    public    $timestamps = false;

    protected $fillable = [
        'num_doc','nombre','nombre_comercial',
        'direccion','telefono','email','id_empresa',
    ];

    public function compras() { return $this->hasMany(Compra::class,'id_proveedor','proveedor_id'); }
}

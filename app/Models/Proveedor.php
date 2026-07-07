<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\Auditable;

class Proveedor extends Model
{
    use Auditable;
{
    protected $table      = 'proveedores';
    protected $primaryKey = 'proveedor_id';
    public    $timestamps = false;

    protected $fillable = [
        'ruc','razon_social','nombre_comercial',
        'direccion','direccion2','telefono','telefono2','email',
        'departamento','provincia','distrito','ubigeo',
        'id_empresa','fecha_create','estado',
    ];

    public function compras() { return $this->hasMany(Compra::class,'id_proveedor','proveedor_id'); }
}

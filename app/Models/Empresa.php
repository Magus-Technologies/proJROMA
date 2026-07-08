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
        'user_sol','clave_sol','gre_client_id','gre_client_secret',
        'logo','ubigeo','distrito','provincia',
        'departamento','tipo_impresion','modo','igv','propaganda',
    ];

    /**
     * Credenciales SUNAT según el modo.
     *  - beta       → fuerza el RUC de prueba (20000000001 / MODDATOS) + su
     *                 certificado de prueba, para no arriesgar el RUC real.
     *  - produccion → usa el RUC, usuario SOL y clave reales de la empresa.
     *
     * @return array{ruc:string, usuario:string, clave:string, endpoint:string}
     */
    public function credencialesSunat(): array
    {
        if (($this->modo ?? '') === 'produccion') {
            return [
                'ruc'      => (string) $this->ruc,
                'usuario'  => (string) ($this->user_sol ?? ''),
                'clave'    => (string) ($this->clave_sol ?? ''),
                'endpoint' => 'produccion',
            ];
        }

        return [
            'ruc'      => '20000000001',
            'usuario'  => 'MODDATOS',
            'clave'    => 'moddatos',
            'endpoint' => 'beta',
        ];
    }

    public function usuarios() { return $this->hasMany(User::class,'id_empresa','id_empresa'); }
    public function clientes() { return $this->hasMany(Cliente::class,'id_empresa','id_empresa'); }
    public function productos(){ return $this->hasMany(Producto::class,'id_empresa','id_empresa'); }
}

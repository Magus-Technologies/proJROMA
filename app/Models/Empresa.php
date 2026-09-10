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
        'email','telefono','telefono2','telefono3','telefono_predeterminado','estado','password',
        'user_sol','clave_sol','gre_client_id','gre_client_secret','certificado',
        'logo','ubigeo','distrito','provincia',
        'departamento','tipo_impresion','modo','igv','propaganda',
    ];

    /** Los tres teléfonos que puede tener una empresa, en orden. */
    public const CAMPOS_TELEFONO = [
        'telefono'  => 'Teléfono 1',
        'telefono2' => 'Teléfono 2',
        'telefono3' => 'Teléfono 3',
    ];

    /**
     * El teléfono que va impreso en los PDF (ventas, pedidos, guías,
     * cotizaciones). Es el marcado como predeterminado; si quedó vacío o
     * nunca se eligió, cae al primero que tenga número.
     */
    public function getTelefonoPrincipalAttribute(): string
    {
        $elegido = (string) ($this->telefono_predeterminado ?? '');

        if (isset(self::CAMPOS_TELEFONO[$elegido]) && trim((string) $this->{$elegido}) !== '') {
            return trim((string) $this->{$elegido});
        }

        foreach (array_keys(self::CAMPOS_TELEFONO) as $campo) {
            if (trim((string) $this->{$campo}) !== '') {
                return trim((string) $this->{$campo});
            }
        }

        return '';
    }

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

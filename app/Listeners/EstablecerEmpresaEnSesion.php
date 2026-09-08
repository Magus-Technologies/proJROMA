<?php

namespace App\Listeners;

use App\Models\Empresa;
use Illuminate\Auth\Events\Login;

/**
 * Deja en sesión la empresa y sucursal del usuario en CUALQUIER autenticación.
 *
 * Antes esto vivía solo en el formulario de login. El problema es que
 * SessionGuard también autentica desde la cookie de "Recordarme" sin pasar por
 * ese formulario (dispara este mismo evento Login), así que la sesión quedaba
 * sin id_empresa.
 *
 * Con la sesión vacía, session('id_empresa') devuelve null y los 97 recursos
 * del panel que filtran por (int) session('id_empresa') consultan por
 * id_empresa = 0: todas las pantallas salen vacías, y peor, lo que ese usuario
 * cree nace con id_empresa = 0, invisible para el resto de la empresa.
 */
class EstablecerEmpresaEnSesion
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user || ! isset($user->id_empresa)) {
            return;
        }

        $empresa = Empresa::where('id_empresa', $user->id_empresa)
            ->where('estado', '1')
            ->first();

        // Sin empresa activa no se pone nada: CheckEmpresa corta el acceso y
        // pide login de nuevo, en vez de dejar entrar a un panel vacío.
        if (! $empresa) {
            return;
        }

        session()->put([
            'id_empresa'     => (int) $empresa->id_empresa,
            'sucursal'       => (int) ($user->sucursal ?: 1),
            'nombre_empresa' => $empresa->razon_social,
            'logo_empresa'   => $empresa->logo,
            'ruc_empr'       => $empresa->ruc,
            'last_activity'  => time(),
        ]);
    }
}

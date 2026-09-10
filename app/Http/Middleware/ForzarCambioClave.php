<?php

namespace App\Http\Middleware;

use App\Filament\Pages\MiPerfil;
use App\Livewire\CampanaVencimientos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mientras el usuario tenga la clave inicial que le puso el administrador, lo
 * único que puede hacer es cambiarla: cualquier otra pantalla lo devuelve a
 * "Mi Perfil", que en ese estado se reduce al cambio de contraseña.
 *
 * Se aplica en dos lugares porque el sistema tiene dos mitades: el panel de
 * Filament (authMiddleware) y las pantallas antiguas en Blade (grupo web).
 */
class ForzarCambioClave
{
    /**
     * Rutas que siguen funcionando con el cambio pendiente. Sin ellas el
     * usuario no podría ni cambiar la clave ni salir del sistema.
     */
    private const RUTAS_LIBRES = [
        'filament.admin.auth.login',
        'filament.admin.auth.logout',
        'login',
        'logout',
    ];

    /**
     * Componentes Livewire que se aceptan con el cambio pendiente: la propia
     * pantalla y los internos de Filament (notificaciones, menú de usuario).
     *
     * Filament registra sus páginas por nombre de clase, no por alias en
     * kebab-case; se aceptan las dos formas por si eso cambia.
     */
    private const COMPONENTES_LIBRES = [
        // Las páginas del panel las registra Filament por nombre de clase.
        MiPerfil::class,
        'app.filament.pages.mi-perfil',
        // La campanita se dibuja también en esta pantalla y se refresca sola
        // cada minuto; solo lee, no deja hacer nada. Livewire la descubre por
        // su cuenta, así que su nombre es el alias en kebab-case.
        CampanaVencimientos::class,
        'campana-vencimientos',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (! $usuario || ! $usuario->debe_cambiar_clave) {
            return $next($request);
        }

        // Primero las peticiones de Livewire: comparten prefijo con los assets
        // pero se filtran por componente, no por ruta.
        if ($this->esActualizacionDeLivewire($request)) {
            return $this->componentesPermitidos($request)
                ? $next($request)
                : abort(403, 'Primero tenés que cambiar tu contraseña.');
        }

        if ($this->esRutaLibre($request)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'Primero tenés que cambiar tu contraseña.');
        }

        return redirect()->to(MiPerfil::getUrl());
    }

    private function esRutaLibre(Request $request): bool
    {
        $nombre = $request->route()?->getName() ?? '';

        if (in_array($nombre, self::RUTAS_LIBRES, true)) {
            return true;
        }

        // La propia pantalla de cambio de clave, más los assets y subidas que
        // Livewire y Filament necesitan para renderizarla.
        return $nombre === MiPerfil::getRouteName()
            || str_starts_with($nombre, 'livewire.')
            || str_starts_with($nombre, 'filament.asset')
            || $request->is('livewire*');
    }

    private function esActualizacionDeLivewire(Request $request): bool
    {
        return str_ends_with((string) $request->route()?->getName(), 'livewire.update');
    }

    /**
     * Una petición de Livewire trae el nombre del componente en el snapshot.
     * Así, una pestaña que quedó abierta antes del reseteo tampoco puede
     * seguir operando otros módulos.
     */
    private function componentesPermitidos(Request $request): bool
    {
        $componentes = $request->input('components', []);

        if (! is_array($componentes) || $componentes === []) {
            return false;
        }

        foreach ($componentes as $componente) {
            $snapshot = json_decode((string) ($componente['snapshot'] ?? ''), true);
            $nombre = $snapshot['memo']['name'] ?? null;

            if (! is_string($nombre)) {
                return false;
            }

            $nombre = ltrim($nombre, '\\');

            if (! in_array($nombre, self::COMPONENTES_LIBRES, true)
                && ! str_starts_with($nombre, 'Filament\\')
                && ! str_starts_with($nombre, 'filament.livewire.')) {
                return false;
            }
        }

        return true;
    }
}

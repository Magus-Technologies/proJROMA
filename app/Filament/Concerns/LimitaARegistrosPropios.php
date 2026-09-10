<?php

namespace App\Filament\Concerns;

/**
 * Alcance de un listado: todo el módulo o solo lo que hizo el usuario.
 *
 * Cada módulo define un permiso "ver_todas". Quien no lo tiene ve únicamente
 * sus propios registros (sus ventas, sus pedidos, las deudas de sus ventas,
 * las compras que registró). Los administradores lo tienen siempre porque
 * Gate::before les concede todo.
 */
trait LimitaARegistrosPropios
{
    protected static function veTodo(string $permiso): bool
    {
        return auth()->user()?->can($permiso) ?? false;
    }

    /** Id del usuario en sesión, tal como se guarda en las tablas del negocio. */
    protected static function usuarioActual(): int
    {
        return (int) (auth()->user()?->usuario_id ?? 0);
    }
}

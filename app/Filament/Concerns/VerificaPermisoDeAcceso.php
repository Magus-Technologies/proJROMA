<?php

namespace App\Filament\Concerns;

/**
 * Control de permisos de un recurso o página de Filament.
 *
 * PERMISO_ACCESO decide si la pantalla existe para el usuario: sin él, no
 * aparece en el menú y la URL directa queda bloqueada.
 *
 * Las demás constantes son OPCIONALES y controlan cada acción por separado:
 *
 *   PERMISO_CREAR   → botón Crear / Nuevo
 *   PERMISO_EDITAR  → acción Editar
 *   PERMISO_BORRAR  → acción Eliminar (individual y masiva)
 *
 * Si un recurso no declara alguna, esa acción queda permitida para quien ya
 * tiene acceso a la pantalla. Así el trait se puede aplicar sin romper nada, y
 * cada recurso va cerrando lo que necesite.
 *
 * El rol ADMIN pasa siempre (Gate::before en AppServiceProvider).
 */
trait VerificaPermisoDeAcceso
{
    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can(static::PERMISO_ACCESO);
    }

    public static function canCreate(): bool
    {
        return static::permiteAccion('PERMISO_CREAR');
    }

    public static function canEdit($record): bool
    {
        return static::permiteAccion('PERMISO_EDITAR');
    }

    public static function canDelete($record): bool
    {
        return static::permiteAccion('PERMISO_BORRAR');
    }

    public static function canDeleteAny(): bool
    {
        return static::permiteAccion('PERMISO_BORRAR');
    }

    /**
     * Comprueba el permiso de una acción concreta.
     * Sin constante declarada la acción no se restringe: basta con tener
     * acceso a la pantalla, que es como se comportaba antes.
     */
    protected static function permiteAccion(string $constante): bool
    {
        $nombre = static::class . '::' . $constante;

        if (! defined($nombre)) {
            return true;
        }

        return (bool) auth()->user()?->can(constant($nombre));
    }

    /**
     * Para usar dentro de acciones sueltas (PDF, exportar, anular...):
     *   ->visible(fn () => static::puede(self::PERMISO_PDF))
     */
    public static function puede(?string $permiso): bool
    {
        return $permiso === null || (bool) auth()->user()?->can($permiso);
    }
}

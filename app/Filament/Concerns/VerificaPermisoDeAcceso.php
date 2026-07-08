<?php

namespace App\Filament\Concerns;

/**
 * Oculta el recurso/página del menú y bloquea su acceso cuando el usuario
 * no tiene el permiso indicado en la constante PERMISO_ACCESO de la clase.
 * El rol ADMIN pasa siempre (Gate::before en AppServiceProvider).
 */
trait VerificaPermisoDeAcceso
{
    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can(static::PERMISO_ACCESO);
    }
}

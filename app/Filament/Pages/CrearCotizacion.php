<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CrearCotizacion extends Page
{
    protected string $view = 'filament.pages.crear-cotizacion';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Nueva Cotización';
    protected static ?string $slug = 'cotizaciones/crear';
}

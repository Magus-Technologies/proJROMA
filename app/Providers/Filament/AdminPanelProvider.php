<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\BajoStockWidget;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TopClientesWidget;
use App\Filament\Widgets\UltimasVentasWidget;
use App\Filament\Widgets\VentasChart;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('panel')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('ProjRoma')
            ->brandLogo(asset('logos/logo.svg'))
            ->colors([
                'primary' => Color::Blue,
            ])
            // Modo SPA: al cambiar de módulo solo se intercambia el contenido
            // (sin recargar toda la página) y muestra una barra de carga arriba.
            // Los formularios POS en Blade van por URL completa → carga normal.
            ->spa()
            ->spaUrlExceptions([
                url('/compras/*'),
                url('/nota/electronica*'),
                url('/cotizaciones/editar/*'),
                url('/tms/*'),
                url('/reporte/*'),
                url('/venta/*'),
                url('/guia/*'),
                url('/r/*'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->renderHook(
                \Filament\View\PanelsRenderHook::STYLES_BEFORE,
                fn () => '
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/tabler-icons.min.css">',
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
                fn () => view('filament.topbar.user-badge'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::SIDEBAR_START,
                fn () => view('filament.sidebar-header'),
            )

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\Filament\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Facturación')->icon('heroicon-o-document-text')->collapsed(),
                NavigationGroup::make('Cotizaciones')->icon('heroicon-o-clipboard-document-list')->collapsed(),
                NavigationGroup::make('Cobranzas')->icon('heroicon-o-banknotes')->collapsed(),
                NavigationGroup::make('Pagos')->icon('heroicon-o-credit-card')->collapsed(),
                NavigationGroup::make('Caja')->icon('heroicon-o-calculator')->collapsed(),
                NavigationGroup::make('Inventario')->icon('heroicon-o-cube')->collapsed(),
                NavigationGroup::make('Transporte (TMS)')->icon('heroicon-o-truck')->collapsed(),
                NavigationGroup::make('Maestros')->icon('heroicon-o-users')->collapsed(),
                NavigationGroup::make('Finanzas')->icon('heroicon-o-presentation-chart-bar')->collapsed(),
                NavigationGroup::make('Contabilidad')->icon('heroicon-o-book-open')->collapsed(),
                NavigationGroup::make('Administración')->icon('heroicon-o-cog-6-tooth')->collapsed(),
            ])
            ->navigationItems([
                // ── Facturación ──────────────────────────────────────────
                // (NotaElectronicaResource handles Notas Electrónicas)

                // ── Cotizaciones ─────────────────────────────────────────
                // (CotizacionResource handles this group)

                // ── Cobranzas ────────────────────────────────────────────
                // (CuentaPorCobrarResource and MisCobroResource handle this group)

                // ── Pagos ────────────────────────────────────────────────
                // (CuentaPorPagarResource handles this group)

                // ── Almacén ──────────────────────────────────────────────
                // (RecepcionResource handles Recepción)
                // (AlmacenStockResource handles Almacén, KardexResource handles Kardex)
                // (AjusteResource handles Ajustes / Cuadres)
                // (TrasladoResource handles Traslado de Stock)
                // (PrestamoResource handles Préstamos)

                // ── Administración ───────────────────────────────────────
                // (SucursalResource handles Sucursales)
            ])
            ->widgets([
                StatsOverview::class,
                VentasChart::class,
                TopClientesWidget::class,
                UltimasVentasWidget::class,
                BajoStockWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

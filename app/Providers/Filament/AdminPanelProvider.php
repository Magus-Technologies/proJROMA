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
            ->renderHook(
                \Filament\View\PanelsRenderHook::SIDEBAR_NAV_END,
                fn () => view('filament.sidebar-footer'),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationItems([
                // ── Facturación ──────────────────────────────────────────
                NavigationItem::make('Notas Electrónicas')
                    ->icon('heroicon-o-document-duplicate')
                    ->group('Facturación')
                    ->sort(3)
                    ->url(fn () => url('/nota/electronica/lista')),

                // ── Cotizaciones ─────────────────────────────────────────
                // (CotizacionResource handles this group)

                // ── Cobranzas ────────────────────────────────────────────
                NavigationItem::make('Cuentas por Cobrar')
                    ->icon('heroicon-o-credit-card')
                    ->group('Cobranzas')
                    ->sort(1)
                    ->url(fn () => url('/cuentas/cobrar')),
                NavigationItem::make('Reporte Deudas')
                    ->icon('heroicon-o-banknotes')
                    ->group('Cobranzas')
                    ->sort(2)
                    ->url(fn () => url('/deudas')),
                NavigationItem::make('Mis Cobros')
                    ->icon('heroicon-o-wallet')
                    ->group('Cobranzas')
                    ->sort(3)
                    ->url(fn () => url('/mis-cobros')),

                // ── Pagos ────────────────────────────────────────────────
                NavigationItem::make('Cuentas por Pagar')
                    ->icon('heroicon-o-building-library')
                    ->group('Pagos')
                    ->sort(1)
                    ->url(fn () => url('/pagos')),

                // ── Almacén ──────────────────────────────────────────────
                NavigationItem::make('Recepción')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->group('Almacén')
                    ->sort(3)
                    ->url(fn () => url('/almacen/recepcion')),
                NavigationItem::make('Almacén')
                    ->icon('heroicon-o-archive-box')
                    ->group('Almacén')
                    ->sort(4)
                    ->url(fn () => url('/almacen/almacen')),
                NavigationItem::make('Kardex')
                    ->icon('heroicon-o-clock')
                    ->group('Almacén')
                    ->sort(5)
                    ->url(fn () => url('/almacen/kardex')),
                NavigationItem::make('Ajustes / Cuadres')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->group('Almacén')
                    ->sort(6)
                    ->url(fn () => url('/almacen/ajustes')),
                NavigationItem::make('Traslado de Stock')
                    ->icon('heroicon-o-arrows-right-left')
                    ->group('Almacén')
                    ->sort(7)
                    ->url(fn () => url('/almacen/traslado')),
                NavigationItem::make('Préstamos')
                    ->icon('heroicon-o-hand-raised')
                    ->group('Almacén')
                    ->sort(8)
                    ->url(fn () => url('/almacen/prestamos')),

                // ── Administración ───────────────────────────────────────
                NavigationItem::make('Sucursales')
                    ->icon('heroicon-o-building-storefront')
                    ->group('Administración')
                    ->sort(2)
                    ->url(fn () => url('/admin/sucursales')),
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

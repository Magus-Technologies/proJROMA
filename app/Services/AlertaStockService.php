<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Alertas de negocio para la campana de notificaciones:
 * - Bajo stock: el stock de un producto queda en o bajo su mínimo.
 * - Venta bajo costo: se vende un producto a un precio menor que su costo.
 */
class AlertaStockService
{
    /** Notifica cuando una línea de venta sale con precio menor al costo. */
    public static function evaluarVentaBajoCosto(ProductoVenta $linea): void
    {
        try {
            $precio = (float) $linea->precio;
            $costo  = (float) $linea->costo;

            if ($costo <= 0 || $precio <= 0 || $precio >= $costo) {
                return;
            }

            $venta = $linea->venta;
            if (! $venta) {
                return;
            }

            // Anti-spam: una alerta por producto cada 6 horas
            $clave = "alerta-bajo-costo:{$venta->id_empresa}:{$linea->id_producto}";
            if (! Cache::add($clave, 1, now()->addHours(6))) {
                return;
            }

            $admins = static::adminsDe((int) $venta->id_empresa);
            if ($admins->isEmpty()) {
                return;
            }

            $documento = trim(($venta->serie ?? '') . '-' . ($venta->numero ?? ''), '-');
            $perdida = round(($costo - $precio) * (float) $linea->cantidad, 2);

            static::enviar($admins, Notification::make()
                ->danger()
                ->icon('heroicon-o-arrow-trending-down')
                ->title('Venta BAJO COSTO: ' . ($linea->descripcion ?: 'Producto #' . $linea->id_producto))
                ->body('Vendido a S/ ' . number_format($precio, 2) . ' con costo S/ ' . number_format($costo, 2)
                    . ' (pérdida S/ ' . number_format($perdida, 2) . ')'
                    . ($documento ? ' en ' . $documento : '') . '.'),
                static::urlSegura(fn () => \App\Filament\Resources\VentaResource::getUrl('index')));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** @return \Illuminate\Support\Collection<int, User> */
    protected static function adminsDe(int $idEmpresa): \Illuminate\Support\Collection
    {
        return User::where('id_empresa', $idEmpresa)
            ->get()
            ->filter(fn (User $u): bool => method_exists($u, 'esAdmin') && $u->esAdmin());
    }

    public static function enviar($destinatarios, Notification $notificacion, ?string $url = null): void
    {
        $data = $notificacion->getDatabaseMessage();

        // URL de destino al hacer clic en la alerta (la campana la usa)
        if ($url !== null) {
            $data['url'] = $url;
        }

        // sendNow: escribe de inmediato (la variante por defecto se encola y
        // este sistema no corre un queue worker).
        \Illuminate\Support\Facades\Notification::sendNow(
            $destinatarios,
            new \Filament\Notifications\DatabaseNotification($data),
        );
    }

    /** URL segura de un recurso/página Filament (null si no se puede resolver). */
    protected static function urlSegura(callable $resolver): ?string
    {
        try {
            return $resolver();
        } catch (\Throwable) {
            return null;
        }
    }
    public static function evaluar(Producto $producto): void
    {
        try {
            if (! $producto->wasChanged('cantidad')) {
                return;
            }

            $minimo = (int) $producto->stock_minimo;
            if ($minimo <= 0 || (int) $producto->cantidad > $minimo) {
                return;
            }

            // Anti-spam: una alerta por producto/almacén cada 12 horas
            $clave = "alerta-stock:{$producto->id_empresa}:{$producto->id_producto}:{$producto->almacen}";
            if (! Cache::add($clave, 1, now()->addHours(12))) {
                return;
            }

            $admins = static::adminsDe((int) $producto->id_empresa);
            if ($admins->isEmpty()) {
                return;
            }

            $almacen = DB::table('almacenes')
                ->where('id_empresa', $producto->id_empresa)
                ->where('codigo', $producto->almacen)
                ->value('nombre');

            static::enviar($admins, Notification::make()
                ->warning()
                ->icon('heroicon-o-exclamation-triangle')
                ->title('Bajo stock: ' . $producto->descripcion)
                ->body('Quedan ' . (int) $producto->cantidad . ' unidades (mínimo ' . $minimo . ')'
                    . ($almacen ? ' en ' . $almacen : '') . '.'),
                static::urlSegura(fn () => \App\Filament\Resources\ProductoResource::getUrl('index')));
        } catch (\Throwable $e) {
            // La alerta jamás debe romper el flujo que movió el stock
            report($e);
        }
    }
}

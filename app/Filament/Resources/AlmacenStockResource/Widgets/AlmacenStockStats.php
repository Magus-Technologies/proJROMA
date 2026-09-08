<?php

namespace App\Filament\Resources\AlmacenStockResource\Widgets;

use App\Filament\Resources\AlmacenStockResource\Pages\ListAlmacenStock;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AlmacenStockStats extends StatsOverviewWidget
{
    /**
     * Las tarjetas leen la MISMA consulta que la tabla: respetan la pestaña de
     * almacén activa, la búsqueda y los filtros. Si estás viendo un almacén,
     * la valorización es la de ese almacén, no la de toda la empresa.
     */
    use InteractsWithPageTable;

    protected ?string $pollingInterval = null;
    protected int | array | null $columns = 4;

    protected function getTablePage(): string
    {
        return ListAlmacenStock::class;
    }

    protected function getStats(): array
    {
        $d = $this->agregados();

        $productos  = (int) $d->productos;
        $valorCosto = (float) $d->valor_costo;
        $valorVenta = (float) $d->valor_venta;
        $margen     = $valorVenta - $valorCosto;

        return [
            Stat::make('Valorización del Almacén', self::soles($valorCosto))
                ->description(number_format((float) $d->unidades) . ' unidades en '
                    . number_format($productos) . ' productos')
                ->descriptionIcon('heroicon-m-cube')
                ->icon('heroicon-o-scale')
                ->color('primary'),

            Stat::make('Valor de Venta', self::soles($valorVenta))
                ->description('Margen potencial: ' . self::soles($margen) . self::porcentaje($margen, $valorVenta))
                ->descriptionIcon($margen < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-tag')
                ->color($margen < 0 ? 'danger' : 'success'),

            Stat::make('Stock Bajo', number_format((int) $d->bajo_stock))
                ->description('En o por debajo de su mínimo configurado')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('Sin Stock', number_format((int) $d->sin_stock))
                ->description(number_format((int) $d->con_stock) . ' de ' . number_format($productos)
                    . ' productos sí tienen stock')
                ->descriptionIcon('heroicon-m-archive-box')
                ->icon('heroicon-o-x-circle')
                ->color('gray'),
        ];
    }

    /**
     * Todos los agregados en UNA consulta sobre la tabla ya filtrada.
     * toBase() evita cargar relaciones que aquí no sirven y reorder() quita el
     * ordenamiento de la tabla, que no tiene sentido en una agregación.
     */
    private function agregados(): object
    {
        return $this->getPageTableQuery()
            ->toBase()
            ->reorder()
            ->selectRaw("
                COUNT(*)                                                          as productos,
                COALESCE(SUM(cantidad), 0)                                        as unidades,
                COALESCE(SUM(cantidad * costo), 0)                                as valor_costo,
                COALESCE(SUM(cantidad * precio), 0)                               as valor_venta,
                COALESCE(SUM(cantidad > 0), 0)                                    as con_stock,
                COALESCE(SUM(cantidad <= 0), 0)                                   as sin_stock,
                COALESCE(SUM(stock_minimo > 0 AND cantidad <= stock_minimo), 0)   as bajo_stock
            ")
            ->first();
    }

    private static function soles(float $monto): string
    {
        return 'S/ ' . number_format($monto, 2);
    }

    /** El porcentaje solo se muestra si hay base sobre la cual calcularlo. */
    private static function porcentaje(float $parte, float $total): string
    {
        return $total > 0
            ? ' (' . number_format($parte / $total * 100, 1) . '%)'
            : '';
    }
}

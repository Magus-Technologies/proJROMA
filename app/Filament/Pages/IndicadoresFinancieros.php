<?php

namespace App\Filament\Pages;

use App\Models\CajaMovimiento;
use App\Models\DiasCompra;
use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Filament\Pages\Page;

class IndicadoresFinancieros extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Indicadores Financieros';
    protected static string|\UnitEnum|null $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Indicadores Financieros';
    protected string $view = 'filament.pages.indicadores-financieros';

    private ?array $data = null;

    public function getData(): array
    {
        if ($this->data !== null) {
            return $this->data;
        }

        $empresa = (int) session('id_empresa', 0);
        $sucursal = (int) session('sucursal', 1);

        $ventasMes = (float) Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->delMes()->sum('total');

        $costoMes = (float) (ProductoVenta::join('ventas as v', 'v.id_venta', '=', 'productos_ventas.id_venta')
            ->where('v.id_empresa', $empresa)
            ->where('v.sucursal', $sucursal)
            ->where('v.estado', '1')
            ->whereMonth('v.fecha_emision', now()->month)
            ->whereYear('v.fecha_emision', now()->year)
            ->selectRaw('SUM(productos_ventas.costo * productos_ventas.cantidad) as total')
            ->value('total') ?? 0);

        $gastosMes = (float) CajaMovimiento::where('tipo', 'EGRESO')
            ->where('estado', 'CONFIRMADO')
            ->whereHas('caja', fn ($q) => $q->where('id_empresa', $empresa))
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('monto');

        $utilidadBruta = $ventasMes - $costoMes;
        $utilidadNeta = $utilidadBruta - $gastosMes;
        $margenBruto = $ventasMes > 0 ? round(($utilidadBruta / $ventasMes) * 100, 1) : 0;
        $margenNeto = $ventasMes > 0 ? round(($utilidadNeta / $ventasMes) * 100, 1) : 0;

        $saldoCaja = (float) (CajaMovimiento::where('estado', 'CONFIRMADO')
            ->whereHas('caja', fn ($q) => $q->where('id_empresa', $empresa))
            ->selectRaw("SUM(CASE WHEN tipo='INGRESO' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo='EGRESO' THEN monto ELSE 0 END) as saldo")
            ->value('saldo') ?? 0);

        $cxPTotal = (float) DiasCompra::whereNull('estado')
            ->whereHas('compra', fn ($q) => $q->where('id_empresa', $empresa))
            ->sum('monto');
        $liquidez = $cxPTotal > 0 ? round($saldoCaja / $cxPTotal, 2) : 0;

        // Valor del inventario = costo unitario × stock (no solo el costo unitario)
        $inventarioCosto = (float) Producto::where('id_empresa', $empresa)
            ->selectRaw('COALESCE(SUM(costo * cantidad), 0) as total')
            ->value('total');
        $rotacionInventario = $inventarioCosto > 0 ? round($costoMes / $inventarioCosto, 2) : 0;

        $roi = ($costoMes + $gastosMes) > 0
            ? round(($utilidadNeta / ($costoMes + $gastosMes)) * 100, 1)
            : 0;

        $margenContribucion = $ventasMes > 0 ? ($utilidadBruta / $ventasMes) : 0;
        $puntoEquilibrio = $margenContribucion > 0
            ? round($gastosMes / $margenContribucion, 2)
            : 0;

        $ventas12Meses = (float) Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->where('fecha_emision', '>=', now()->subMonths(12))
            ->sum('total');

        return $this->data = [
            'ventas_mes' => $ventasMes,
            'costo_mes' => $costoMes,
            'gastos_mes' => $gastosMes,
            'utilidad_bruta' => $utilidadBruta,
            'utilidad_neta' => $utilidadNeta,
            'margen_bruto' => $margenBruto,
            'margen_neto' => $margenNeto,
            'saldo_caja' => $saldoCaja,
            'cxp_total' => $cxPTotal,
            'liquidez' => $liquidez,
            'inventario_costo' => $inventarioCosto,
            'rotacion_inventario' => $rotacionInventario,
            'roi' => $roi,
            'punto_equilibrio' => $puntoEquilibrio,
            'ventas_12m' => $ventas12Meses,
        ];
    }

    /**
     * Indicadores organizados en secciones, cada uno con su fórmula y una
     * nota de interpretación generada según el valor calculado.
     * Es la única fuente que usan la vista, el PDF y el Excel.
     *
     * estado: ok | atencion | riesgo | info
     */
    public function getSecciones(): array
    {
        $d = $this->getData();

        $soles = fn (float $v): string => 'S/ ' . number_format($v, 2);

        // ── Rentabilidad ──────────────────────────────────────────────
        $pe = $d['punto_equilibrio'];
        if ($pe > 0 && $d['ventas_mes'] >= $pe) {
            $notaVentas = 'Superan el punto de equilibrio (' . $soles($pe) . '): el mes ya cubre sus gastos.';
            $estVentas = 'ok';
        } elseif ($pe > 0) {
            $notaVentas = 'Por debajo del punto de equilibrio (' . $soles($pe) . '): faltan ' . $soles($pe - $d['ventas_mes']) . ' de ventas para cubrir los gastos del mes.';
            $estVentas = 'atencion';
        } else {
            $notaVentas = 'Total facturado del período.';
            $estVentas = 'info';
        }

        if ($d['utilidad_bruta'] > 0) {
            $notaUtilBruta = 'La mercadería deja ' . $soles($d['utilidad_bruta']) . ' antes de gastos.';
            $estUtilBruta = 'ok';
        } elseif ($d['ventas_mes'] > 0) {
            $notaUtilBruta = 'El costo supera a las ventas: se está vendiendo por debajo del costo.';
            $estUtilBruta = 'riesgo';
        } else {
            $notaUtilBruta = 'Sin ventas registradas en el mes.';
            $estUtilBruta = 'info';
        }

        if ($d['margen_bruto'] >= 20) {
            $notaMargenBruto = 'Saludable: de cada S/ 100 vendidos quedan ' . $soles($d['margen_bruto']) . ' antes de gastos.';
            $estMargenBruto = 'ok';
        } elseif ($d['margen_bruto'] >= 8) {
            $notaMargenBruto = 'Ajustado pero típico en distribución: de cada S/ 100 quedan ' . $soles($d['margen_bruto']) . '. Vigilar los gastos.';
            $estMargenBruto = 'atencion';
        } else {
            $notaMargenBruto = 'Muy bajo: casi no queda margen para cubrir los gastos del negocio.';
            $estMargenBruto = 'riesgo';
        }

        if ($d['utilidad_neta'] > 0) {
            $notaUtilNeta = 'Ganancia final del mes, después de costos y gastos.';
            $estUtilNeta = 'ok';
        } else {
            $notaUtilNeta = 'El mes opera a pérdida: los gastos (' . $soles($d['gastos_mes']) . ') superan la utilidad bruta (' . $soles($d['utilidad_bruta']) . ').';
            $estUtilNeta = 'riesgo';
        }

        if ($d['margen_neto'] >= 10) {
            $notaMargenNeto = 'Saludable: de cada S/ 100 vendidos quedan ' . $soles($d['margen_neto']) . ' de ganancia final.';
            $estMargenNeto = 'ok';
        } elseif ($d['margen_neto'] >= 3) {
            $notaMargenNeto = 'Positivo pero delgado: cualquier gasto extra puede dejar el mes en pérdida.';
            $estMargenNeto = 'atencion';
        } else {
            $notaMargenNeto = 'Crítico: la operación casi no deja ganancia (o deja pérdida).';
            $estMargenNeto = 'riesgo';
        }

        // ── Liquidez ─────────────────────────────────────────────────
        if ($d['saldo_caja'] >= 0) {
            $notaSaldo = 'Dinero disponible acumulado en las cajas de la empresa.';
            $estSaldo = 'info';
        } else {
            $notaSaldo = 'Saldo negativo: hay egresos registrados sin su ingreso correspondiente. Revisar aperturas y movimientos de caja.';
            $estSaldo = 'riesgo';
        }

        if ($d['cxp_total'] <= 0) {
            $valLiquidez = '—';
            $notaLiquidez = 'Sin deudas pendientes con proveedores.';
            $estLiquidez = 'ok';
        } elseif ($d['saldo_caja'] < 0) {
            $valLiquidez = number_format($d['liquidez'], 2);
            $notaLiquidez = 'Con caja negativa no hay capacidad de pago real sobre los ' . $soles($d['cxp_total']) . ' adeudados.';
            $estLiquidez = 'riesgo';
        } elseif ($d['liquidez'] >= 1) {
            $valLiquidez = number_format($d['liquidez'], 2);
            $notaLiquidez = 'La caja alcanza para pagar todas las deudas con proveedores (cobertura ' . round($d['liquidez'] * 100) . '%).';
            $estLiquidez = 'ok';
        } elseif ($d['liquidez'] >= 0.5) {
            $valLiquidez = number_format($d['liquidez'], 2);
            $notaLiquidez = 'La caja cubre solo el ' . round($d['liquidez'] * 100) . '% de las deudas con proveedores.';
            $estLiquidez = 'atencion';
        } else {
            $valLiquidez = number_format($d['liquidez'], 2);
            $notaLiquidez = 'Cobertura muy baja (' . round($d['liquidez'] * 100) . '%): priorizar cobranzas o renegociar plazos.';
            $estLiquidez = 'riesgo';
        }

        // ── Eficiencia ───────────────────────────────────────────────
        if ($d['inventario_costo'] <= 0) {
            $valRotacion = '—';
            $notaRotacion = 'Sin inventario valorizado (productos sin costo o sin stock).';
            $estRotacion = 'info';
        } elseif ($d['rotacion_inventario'] >= 1) {
            $valRotacion = number_format($d['rotacion_inventario'], 2) . ' veces/mes';
            $notaRotacion = 'El almacén rota en un mes o menos: buen uso del capital.';
            $estRotacion = 'ok';
        } elseif ($d['rotacion_inventario'] >= 0.4) {
            $meses = round(1 / max($d['rotacion_inventario'], 0.01), 1);
            $valRotacion = number_format($d['rotacion_inventario'], 2) . ' veces/mes';
            $notaRotacion = "El almacén tarda ~{$meses} meses en rotar por completo.";
            $estRotacion = 'atencion';
        } else {
            $valRotacion = number_format($d['rotacion_inventario'], 2) . ' veces/mes';
            $notaRotacion = 'Rotación muy lenta: hay capital inmovilizado en el almacén.';
            $estRotacion = 'riesgo';
        }

        if ($d['roi'] > 0) {
            $notaRoi = 'Cada S/ 100 invertidos en operar el mes devolvieron ' . $soles($d['roi']) . ' de ganancia.';
            $estRoi = 'ok';
        } else {
            $notaRoi = 'La inversión del mes (costo + gastos) no generó retorno.';
            $estRoi = 'riesgo';
        }

        if ($pe <= 0) {
            $notaPe = 'No calculable: se necesita margen bruto positivo y gastos registrados.';
            $estPe = 'info';
        } elseif ($d['ventas_mes'] >= $pe) {
            $notaPe = 'Ya superado: las ventas están ' . $soles($d['ventas_mes'] - $pe) . ' por encima del mínimo para no perder.';
            $estPe = 'ok';
        } else {
            $notaPe = 'Aún no alcanzado: faltan ' . $soles($pe - $d['ventas_mes']) . ' de ventas para cubrir los gastos.';
            $estPe = 'atencion';
        }

        return [
            [
                'titulo' => 'Rentabilidad',
                'items' => [
                    ['label' => 'Ventas del Mes', 'valor' => $soles($d['ventas_mes']), 'formula' => 'Suma de ventas activas del mes', 'nota' => $notaVentas, 'estado' => $estVentas],
                    ['label' => 'Costo de Ventas', 'valor' => $soles($d['costo_mes']), 'formula' => 'Costo × cantidad de cada producto vendido', 'nota' => 'Lo que costó la mercadería vendida en el mes.', 'estado' => 'info'],
                    ['label' => 'Gastos del Mes', 'valor' => $soles($d['gastos_mes']), 'formula' => 'Egresos de caja confirmados del mes', 'nota' => 'Incluye todos los egresos operativos registrados en caja.', 'estado' => 'info'],
                    ['label' => 'Utilidad Bruta', 'valor' => $soles($d['utilidad_bruta']), 'formula' => 'Ventas − Costo de ventas', 'nota' => $notaUtilBruta, 'estado' => $estUtilBruta],
                    ['label' => 'Margen Bruto', 'valor' => $d['margen_bruto'] . '%', 'formula' => 'Utilidad bruta ÷ Ventas × 100', 'nota' => $notaMargenBruto, 'estado' => $estMargenBruto],
                    ['label' => 'Utilidad Neta', 'valor' => $soles($d['utilidad_neta']), 'formula' => 'Utilidad bruta − Gastos', 'nota' => $notaUtilNeta, 'estado' => $estUtilNeta],
                    ['label' => 'Margen Neto', 'valor' => $d['margen_neto'] . '%', 'formula' => 'Utilidad neta ÷ Ventas × 100', 'nota' => $notaMargenNeto, 'estado' => $estMargenNeto],
                ],
            ],
            [
                'titulo' => 'Liquidez',
                'items' => [
                    ['label' => 'Saldo en Caja', 'valor' => $soles($d['saldo_caja']), 'formula' => 'Ingresos − Egresos confirmados (histórico de cajas)', 'nota' => $notaSaldo, 'estado' => $estSaldo],
                    ['label' => 'Cuentas por Pagar', 'valor' => $soles($d['cxp_total']), 'formula' => 'Cuotas de compras a crédito pendientes', 'nota' => 'Deuda pendiente con proveedores.', 'estado' => 'info'],
                    ['label' => 'Liquidez', 'valor' => $valLiquidez, 'formula' => 'Saldo en caja ÷ Cuentas por pagar', 'nota' => $notaLiquidez, 'estado' => $estLiquidez],
                ],
            ],
            [
                'titulo' => 'Eficiencia',
                'items' => [
                    ['label' => 'Valor de Inventario', 'valor' => $soles($d['inventario_costo']), 'formula' => 'Costo × stock de cada producto', 'nota' => 'Capital invertido en mercadería del catálogo.', 'estado' => 'info'],
                    ['label' => 'Rotación de Inventario', 'valor' => $valRotacion, 'formula' => 'Costo vendido del mes ÷ Valor de inventario', 'nota' => $notaRotacion, 'estado' => $estRotacion],
                    ['label' => 'ROI del Mes', 'valor' => $d['roi'] . '%', 'formula' => 'Utilidad neta ÷ (Costo + Gastos) × 100', 'nota' => $notaRoi, 'estado' => $estRoi],
                    ['label' => 'Punto de Equilibrio', 'valor' => $soles($pe), 'formula' => 'Gastos ÷ Margen de contribución', 'nota' => $notaPe, 'estado' => $estPe],
                ],
            ],
        ];
    }
}

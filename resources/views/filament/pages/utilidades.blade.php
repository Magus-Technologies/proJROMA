@php
    $data = app(\App\Filament\Pages\Utilidades::class)->getData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        <form method="GET" class="flex flex-wrap items-end gap-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                <input type="date" name="desde" value="{{ $data['desde'] }}"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                <input type="date" name="hasta" value="{{ $data['hasta'] }}"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <input type="hidden" name="tab" value="{{ $data['tab'] }}">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Filtrar
            </button>
            <a href="{{ url('/panel/utilidades') }}"
                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                Limpiar
            </a>
            <div class="flex-1"></div>
            <button type="button" onclick="downloadFile('{{ route('reporte.utilidades.pdf', ['desde' => $data['desde'], 'hasta' => $data['hasta']]) }}')"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path d="M12 11v4m0 0l-2-2m2 2l2-2"/></svg>
                PDF
            </button>
            <button type="button" onclick="downloadFile('{{ route('reporte.utilidades.xls', ['desde' => $data['desde'], 'hasta' => $data['hasta']]) }}')"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </button>
        </form>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="rounded-xl border-l-4 border-blue-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ventas</p>
                <p class="mt-1 text-xl font-bold text-blue-700 dark:text-blue-300">S/ {{ number_format($data['total_venta'], 2) }}</p>
            </div>
            <div class="rounded-xl border-l-4 border-yellow-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Costo Total</p>
                <p class="mt-1 text-xl font-bold text-yellow-700 dark:text-yellow-300">S/ {{ number_format($data['total_costo'], 2) }}</p>
            </div>
            <div class="rounded-xl border-l-4 border-{{ $data['total_utilidad'] >= 0 ? 'green' : 'red' }}-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Utilidad Bruta</p>
                <p class="mt-1 text-xl font-bold text-{{ $data['total_utilidad'] >= 0 ? 'green' : 'red' }}-700 dark:text-{{ $data['total_utilidad'] >= 0 ? 'green' : 'red' }}-300">
                    S/ {{ number_format($data['total_utilidad'], 2) }}
                </p>
            </div>
            <div class="rounded-xl border-l-4 border-{{ $data['margen_general'] >= 10 ? 'green' : ($data['margen_general'] >= 0 ? 'yellow' : 'red') }}-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Margen Bruto</p>
                <p class="mt-1 text-xl font-bold">{{ $data['margen_general'] }}%</p>
            </div>
            <div class="rounded-xl border-l-4 border-{{ $data['utilidad_neta'] >= 0 ? 'green' : 'red' }}-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Utilidad Neta (est.)</p>
                <p class="mt-1 text-xl font-bold text-{{ $data['utilidad_neta'] >= 0 ? 'green' : 'red' }}-700 dark:text-{{ $data['utilidad_neta'] >= 0 ? 'green' : 'red' }}-300">
                    S/ {{ number_format($data['utilidad_neta'], 2) }}
                </p>
            </div>
        </div>

        {{-- Cascada del Estado de Resultados del período --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">¿Cómo se llega a la utilidad? — {{ $data['desde'] }} al {{ $data['hasta'] }}</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr>
                        <td class="px-4 py-2.5 text-gray-600 dark:text-gray-300">Ventas del período</td>
                        <td class="px-4 py-2.5 text-right font-medium text-gray-900 dark:text-gray-100">S/ {{ number_format($data['total_venta'], 2) }}</td>
                        <td class="px-4 py-2.5 text-xs text-gray-400 hidden md:table-cell">Comprobantes activos del rango</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 text-gray-600 dark:text-gray-300">(−) Costo de ventas</td>
                        <td class="px-4 py-2.5 text-right font-medium text-gray-900 dark:text-gray-100">S/ {{ number_format($data['total_costo'], 2) }}</td>
                        <td class="px-4 py-2.5 text-xs text-gray-400 hidden md:table-cell">Costo de la mercadería vendida (costo × cantidad)</td>
                    </tr>
                    <tr class="bg-gray-50 dark:bg-gray-900/40">
                        <td class="px-4 py-2.5 font-semibold text-gray-900 dark:text-gray-100">= Utilidad Bruta</td>
                        <td class="px-4 py-2.5 text-right font-bold {{ $data['total_utilidad'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">S/ {{ number_format($data['total_utilidad'], 2) }}</td>
                        <td class="px-4 py-2.5 text-xs text-gray-400 hidden md:table-cell">Margen bruto: {{ $data['margen_general'] }}%</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 text-gray-600 dark:text-gray-300">(−) Gastos operativos</td>
                        <td class="px-4 py-2.5 text-right font-medium text-gray-900 dark:text-gray-100">S/ {{ number_format($data['gastos_operativos'], 2) }}</td>
                        <td class="px-4 py-2.5 text-xs text-gray-400 hidden md:table-cell">Egresos de caja del rango (sin compra de mercadería ni movimientos internos)</td>
                    </tr>
                    <tr class="bg-gray-50 dark:bg-gray-900/40">
                        <td class="px-4 py-2.5 font-semibold text-gray-900 dark:text-gray-100">= Utilidad Operativa</td>
                        <td class="px-4 py-2.5 text-right font-bold {{ $data['utilidad_operativa'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">S/ {{ number_format($data['utilidad_operativa'], 2) }}</td>
                        <td class="px-4 py-2.5 text-xs text-gray-400 hidden md:table-cell"></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 text-gray-600 dark:text-gray-300">(−) Gastos financieros e impuestos</td>
                        <td class="px-4 py-2.5 text-right font-medium text-gray-400">— no registrados —</td>
                        <td class="px-4 py-2.5 text-xs text-gray-400 hidden md:table-cell">El sistema aún no registra intereses ni IR</td>
                    </tr>
                    <tr class="bg-blue-50 dark:bg-blue-900/20 border-t-2 border-blue-200 dark:border-blue-800">
                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-gray-100">= UTILIDAD NETA (estimada)</td>
                        <td class="px-4 py-3 text-right text-lg font-bold {{ $data['utilidad_neta'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">S/ {{ number_format($data['utilidad_neta'], 2) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400 hidden md:table-cell">Margen neto: {{ $data['margen_neto'] }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex overflow-x-auto">
                    @foreach([
                        ['id' => 'productos', 'label' => 'Por Productos', 'icon' => 'cube'],
                        ['id' => 'ventas', 'label' => 'Por Ventas', 'icon' => 'receipt'],
                        ['id' => 'mercados', 'label' => 'Por Mercados', 'icon' => 'map-pin'],
                        ['id' => 'rutas', 'label' => 'Por Rutas', 'icon' => 'road'],
                        ['id' => 'fechas', 'label' => 'Por Día', 'icon' => 'calendar'],
                    ] as $t)
                        @php $active = $data['tab'] === $t['id']; @endphp
                        <a href="?tab={{ $t['id'] }}&desde={{ $data['desde'] }}&hasta={{ $data['hasta'] }}"
                            class="px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition-colors
                                {{ $active ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:border-gray-300' }}">
                            {{ $t['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                @php
                    $columnas = [];
                    $filas = [];
                    if ($data['tab'] === 'productos') {
                        $columnas = ['Producto', 'Cant.', 'Venta S/.', 'Costo S/.', 'Util. Bruta S/.', 'M. Bruto'];
                        $filas = $data['por_producto'];
                    } elseif ($data['tab'] === 'ventas') {
                        $columnas = ['Documento', 'Fecha', 'Cliente', 'Venta S/.', 'Costo S/.', 'Util. Bruta S/.', 'M. Bruto'];
                        $filas = $data['por_venta'];
                    } elseif ($data['tab'] === 'mercados') {
                        $columnas = ['Mercado', 'Cant.', 'Venta S/.', 'Costo S/.', 'Util. Bruta S/.', 'M. Bruto'];
                        $filas = $data['por_mercado'];
                    } elseif ($data['tab'] === 'rutas') {
                        $columnas = ['Ruta', 'Cant.', 'Venta S/.', 'Costo S/.', 'Util. Bruta S/.', 'M. Bruto'];
                        $filas = $data['por_ruta'];
                    } elseif ($data['tab'] === 'fechas') {
                        $columnas = ['Día', 'Cant.', 'Venta S/.', 'Costo S/.', 'Util. Bruta S/.', 'M. Bruto'];
                        $filas = $data['por_fecha'];
                    }
                @endphp

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                        <tr>
                            @foreach($columnas as $col)
                                <th class="text-left px-3 py-2 font-medium text-gray-500 dark:text-gray-400 {{ $loop->index > 1 ? 'text-right' : '' }}">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($filas as $f)
                            <tr class="{{ ($f['utilidad'] ?? 0) < 0 ? 'bg-red-50 dark:bg-red-900/10' : (($f['margen'] ?? 0) < 10 ? 'bg-yellow-50 dark:bg-yellow-900/10' : '') }}">
                                @if ($data['tab'] === 'productos')
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100 max-w-[200px] truncate">{{ $f['descripcion'] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">{{ number_format($f['cantidad']) }}</td>
                                @elseif ($data['tab'] === 'ventas')
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $f['documento'] }}</td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $f['fecha'] }}</td>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100 max-w-[200px] truncate">{{ $f['cliente'] }}</td>
                                @elseif ($data['tab'] === 'mercados')
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $f['mercado'] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">{{ number_format($f['cantidad']) }}</td>
                                @elseif ($data['tab'] === 'rutas')
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $f['ruta'] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">{{ number_format($f['cantidad']) }}</td>
                                @elseif ($data['tab'] === 'fechas')
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($f['fecha'])->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">{{ number_format($f['cantidad']) }}</td>
                                @endif
                                <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">S/ {{ number_format($f['venta'], 2) }}</td>
                                <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">S/ {{ number_format($f['costo'], 2) }}</td>
                                <td class="px-3 py-2 text-right font-medium {{ ($f['utilidad']) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    S/ {{ number_format($f['utilidad'], 2) }}
                                </td>
                                <td class="px-3 py-2 text-right font-medium {{ ($f['margen']) >= 30 ? 'text-green-600' : (($f['margen']) >= 10 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $f['margen'] }}%
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($columnas) }}" class="px-3 py-4 text-center text-gray-400">Sin datos en el rango seleccionado</td></tr>
                        @endforelse
                    </tbody>
                    @if(count($filas) > 0)
                        @php
                            $tVenta = collect($filas)->sum('venta');
                            $tCosto = collect($filas)->sum('costo');
                            $tUtilidad = $tVenta - $tCosto;
                            $tMargen = $tVenta > 0 ? round(($tUtilidad / $tVenta) * 100, 1) : 0;
                            $tCant = collect($filas)->sum('cantidad');
                        @endphp
                        <tfoot class="bg-gray-50 dark:bg-gray-900 border-t-2 border-gray-300 dark:border-gray-600">
                            <tr>
                                @if ($data['tab'] === 'productos')
                                    <td class="px-3 py-2 font-semibold text-gray-900 dark:text-gray-100">TOTAL</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($tCant) }}</td>
                                @elseif ($data['tab'] === 'ventas')
                                    <td colspan="3" class="px-3 py-2 font-semibold text-gray-900 dark:text-gray-100">TOTAL ({{ count($filas) }} ventas)</td>
                                @elseif ($data['tab'] === 'mercados')
                                    <td class="px-3 py-2 font-semibold text-gray-900 dark:text-gray-100">TOTAL</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($tCant) }}</td>
                                @elseif ($data['tab'] === 'rutas')
                                    <td class="px-3 py-2 font-semibold text-gray-900 dark:text-gray-100">TOTAL</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($tCant) }}</td>
                                @elseif ($data['tab'] === 'fechas')
                                    <td class="px-3 py-2 font-semibold text-gray-900 dark:text-gray-100">TOTAL</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($tCant) }}</td>
                                @endif
                                <td class="px-3 py-2 text-right font-semibold text-gray-900 dark:text-gray-100">S/ {{ number_format($tVenta, 2) }}</td>
                                <td class="px-3 py-2 text-right font-semibold text-gray-900 dark:text-gray-100">S/ {{ number_format($tCosto, 2) }}</td>
                                <td class="px-3 py-2 text-right font-semibold {{ $tUtilidad >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    S/ {{ number_format($tUtilidad, 2) }}
                                </td>
                                <td class="px-3 py-2 text-right font-semibold {{ $tMargen >= 30 ? 'text-green-600' : ($tMargen >= 10 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $tMargen }}%
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div class="text-xs text-gray-400 dark:text-gray-500 italic space-y-0.5">
            <p>* Las tablas muestran <strong>utilidad bruta</strong> (Venta − Costo de la mercadería): los gastos operativos no se pueden repartir por producto/mercado/ruta, por eso se descuentan solo en el total del período.</p>
            <p>* Los montos incluyen IGV. La utilidad contable no es lo mismo que la caja: puedes tener utilidad positiva y caja negativa si vendes al crédito (ver Flujo de Caja).</p>
        </div>
    </div>

    @push('scripts')
    <script>
    function downloadFile(url) {
        var a = document.createElement('a');
        a.href = url;
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
    </script>
    @endpush
</x-filament-panels::page>

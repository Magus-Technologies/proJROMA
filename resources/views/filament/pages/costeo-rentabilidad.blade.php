@php
    $data = app(\App\Filament\Pages\CosteoRentabilidad::class)->getData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach([
                ['Productos', $data['total_productos'], 'info'],
                ['Con Ventas', $data['total_con_venta'], 'info'],
                ['Ventas Totales', 'S/ '.number_format($data['total_ventas'],2), 'blue'],
                ['Utilidad Total', 'S/ '.number_format($data['total_utilidad'],2), 'green'],
                ['Margen Promedio', $data['margen_promedio'].'%', $data['margen_promedio'] > 0 ? 'green' : 'red'],
            ] as [$label, $value, $color])
                @php $b = ['info'=>'border-gray-500','blue'=>'border-blue-500','green'=>'border-green-500','red'=>'border-red-500']; @endphp
                <div class="rounded-xl border-l-4 {{ $b[$color] }} bg-white dark:bg-gray-800 shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $label }}</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-green-200 dark:border-green-800 overflow-hidden">
                <div class="p-4 border-b border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20">
                    <h3 class="text-sm font-semibold text-green-800 dark:text-green-200">Top 10 - Mayor Rentabilidad</h3>
                </div>
                <div class="overflow-x-auto max-h-72 overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                            <tr>
                                <th class="text-left px-3 py-2 font-medium text-gray-500">Producto</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500">Venta</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500">Margen</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500">Utilidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($data['mejores'] as $p)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100 max-w-[200px] truncate">{{ $p['descripcion'] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">S/ {{ number_format($p['venta_total'], 2) }}</td>
                                    <td class="px-3 py-2 text-right font-medium text-green-600">{{ $p['margen'] }}%</td>
                                    <td class="px-3 py-2 text-right font-medium text-green-600">S/ {{ number_format($p['utilidad'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-red-200 dark:border-red-800 overflow-hidden">
                <div class="p-4 border-b border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20">
                    <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Top 10 - Pérdida (Margen Negativo)</h3>
                </div>
                <div class="overflow-x-auto max-h-72 overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                            <tr>
                                <th class="text-left px-3 py-2 font-medium text-gray-500">Producto</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500">Venta</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500">Margen</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500">Pérdida</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($data['peores'] as $p)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100 max-w-[200px] truncate">{{ $p['descripcion'] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">S/ {{ number_format($p['venta_total'], 2) }}</td>
                                    <td class="px-3 py-2 text-right font-medium text-red-600">{{ $p['margen'] }}%</td>
                                    <td class="px-3 py-2 text-right font-medium text-red-600">S/ {{ number_format(abs($p['utilidad']), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400">No hay productos con margen negativo</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Todos los Productos</h3>
                <span class="text-xs text-gray-400">{{ $data['total_productos'] }} productos</span>
            </div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                        <tr>
                            <th class="text-left px-3 py-2 font-medium text-gray-500">Producto</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Costo Und.</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Precio Venta</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Precio Prom.</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Stock</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Vendidos</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Venta S/.</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Margen</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Precio Sugerido</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($data['rentabilidad'] as $p)
                            <tr class="{{ $p['margen'] < 0 ? 'bg-red-50 dark:bg-red-900/10' : ($p['margen'] < 10 ? 'bg-yellow-50 dark:bg-yellow-900/10' : '') }}">
                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100 max-w-[180px] truncate" title="{{ $p['descripcion'] }}">{{ $p['descripcion'] }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">S/ {{ number_format($p['costo_unitario'], 2) }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">S/ {{ number_format($p['precio_venta'], 2) }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">S/ {{ number_format($p['precio_promedio'], 2) }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">{{ number_format($p['stock']) }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">{{ number_format($p['cantidad_vendida']) }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">S/ {{ number_format($p['venta_total'], 2) }}</td>
                                <td class="px-3 py-2 text-right font-medium {{ $p['margen'] >= 30 ? 'text-green-600' : ($p['margen'] >= 10 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $p['margen'] }}%
                                </td>
                                <td class="px-3 py-2 text-right {{ $p['diferencia_precio'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    S/ {{ number_format($p['precio_sugerido'], 2) }}
                                    <span class="text-xs">({{ $p['diferencia_precio'] >= 0 ? '+' : '' }}{{ number_format($p['diferencia_precio'], 2) }})</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-xs text-gray-400 dark:text-gray-500 italic">
            * Precio sugerido = Costo unitario × 1.3 (30% de margen recomendado).
            Datos del mes actual.
        </div>
    </div>
</x-filament-panels::page>

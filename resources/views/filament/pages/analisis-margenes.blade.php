@php
    $data = app(\App\Filament\Pages\AnalisisMargenes::class)->getData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="rounded-xl border-l-4 border-blue-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ventas del Mes</p>
                <p class="mt-1 text-xl font-bold text-blue-700 dark:text-blue-300">S/ {{ number_format($data['ventas'], 2) }}</p>
            </div>
            <div class="rounded-xl border-l-4 border-yellow-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Costo Total</p>
                <p class="mt-1 text-xl font-bold text-yellow-700 dark:text-yellow-300">S/ {{ number_format($data['costo_total'], 2) }}</p>
            </div>
            <div class="rounded-xl border-l-4 border-green-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Margen General</p>
                <p class="mt-1 text-xl font-bold text-green-700 dark:text-green-300">{{ $data['margen_general'] }}%</p>
            </div>
            <div class="rounded-xl border-l-4 border-purple-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Compras del Mes</p>
                <p class="mt-1 text-xl font-bold text-purple-700 dark:text-purple-300">S/ {{ number_format($data['compras_mes'], 2) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Top 15 Productos por Margen</h3>
                </div>
                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                            <tr>
                                <th class="text-left px-3 py-2 font-medium text-gray-500 dark:text-gray-400">Producto</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500 dark:text-gray-400">Venta</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500 dark:text-gray-400">Costo</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500 dark:text-gray-400">Margen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($data['top_productos'] as $p)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100 max-w-[200px] truncate">{{ $p['descripcion'] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">S/ {{ number_format($p['venta'], 2) }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">S/ {{ number_format($p['costo'], 2) }}</td>
                                    <td class="px-3 py-2 text-right font-medium {{ $p['margen'] >= 30 ? 'text-green-600' : ($p['margen'] >= 10 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $p['margen'] }}%
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400">Sin datos este mes</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Margen por Vendedor</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="text-left px-3 py-2 font-medium text-gray-500 dark:text-gray-400">Vendedor</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500 dark:text-gray-400">Venta</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500 dark:text-gray-400">Costo</th>
                                <th class="text-right px-3 py-2 font-medium text-gray-500 dark:text-gray-400">Margen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($data['por_vendedor'] as $v)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $v['vendedor'] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">S/ {{ number_format($v['venta'], 2) }}</td>
                                    <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">S/ {{ number_format($v['costo'], 2) }}</td>
                                    <td class="px-3 py-2 text-right font-medium {{ $v['margen'] >= 30 ? 'text-green-600' : ($v['margen'] >= 10 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $v['margen'] }}%
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400">Sin datos este mes</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @if(count($data['bajo_margen']) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-yellow-200 dark:border-yellow-800 overflow-hidden">
                <div class="p-4 border-b border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/20">
                    <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">⚠️ Productos con Margen Bajo (&lt; 10%)</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="text-left px-3 py-2 font-medium text-gray-500">Producto</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Venta</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Margen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($data['bajo_margen'] as $p)
                            <tr>
                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $p['descripcion'] }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">S/ {{ number_format($p['venta'], 2) }}</td>
                                <td class="px-3 py-2 text-right font-medium text-red-600">{{ $p['margen'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @if(count($data['negativos']) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-red-200 dark:border-red-800 overflow-hidden">
                <div class="p-4 border-b border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20">
                    <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">❌ Productos con Margen Negativo</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="text-left px-3 py-2 font-medium text-gray-500">Producto</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Venta</th>
                            <th class="text-right px-3 py-2 font-medium text-gray-500">Margen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($data['negativos'] as $p)
                            <tr>
                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $p['descripcion'] }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">S/ {{ number_format($p['venta'], 2) }}</td>
                                <td class="px-3 py-2 text-right font-medium text-red-600">{{ $p['margen'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div class="text-xs text-gray-400 dark:text-gray-500 italic">
            * Datos del mes actual. Margen = (Venta - Costo) / Venta × 100.
        </div>
    </div>
</x-filament-panels::page>

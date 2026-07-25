@php
    $data = app(\App\Filament\Pages\EstadoResultados::class)->getData();
    $chart = app(\App\Filament\Pages\EstadoResultados::class)->getChartData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Filtros de Período --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Período:</span>
                @php
                    $periodos = [
                        'Este Mes' => [now()->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')],
                        'Mes Anterior' => [now()->subMonth()->startOfMonth()->format('Y-m-d'), now()->subMonth()->endOfMonth()->format('Y-m-d')],
                        'Últimos 3 Meses' => [now()->subMonths(3)->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')],
                        'Últimos 6 Meses' => [now()->subMonths(6)->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')],
                        'Este Año' => [now()->startOfYear()->format('Y-m-d'), now()->format('Y-m-d')],
                    ];
                @endphp
                @foreach($periodos as $label => [$d, $h])
                    <a href="{{ request()->url() }}?desde={{ $d }}&hasta={{ $h }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-lg border transition
                              {{ request('desde', $data['desde']) === $d ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
                <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">
                    {{ $data['desde'] }} al {{ $data['hasta'] }}
                </span>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ventas Netas</p>
                <p class="mt-1 text-2xl font-bold text-blue-700 dark:text-blue-300">S/ {{ number_format($data['ventas'], 2) }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Costo de Ventas</p>
                <p class="mt-1 text-2xl font-bold text-yellow-600 dark:text-yellow-300">S/ {{ number_format($data['costo_ventas'], 2) }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Utilidad Bruta</p>
                <p class="mt-1 text-2xl font-bold {{ $data['utilidad_bruta'] >= 0 ? 'text-green-600 dark:text-green-300' : 'text-red-600 dark:text-red-300' }}">S/ {{ number_format($data['utilidad_bruta'], 2) }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Margen Bruto</p>
                <p class="mt-1 text-2xl font-bold {{ $data['margen_bruto'] >= 30 ? 'text-green-600 dark:text-green-300' : ($data['margen_bruto'] >= 0 ? 'text-yellow-600 dark:text-yellow-300' : 'text-red-600 dark:text-red-300') }}">{{ $data['margen_bruto'] }}%</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Gastos Operativos</p>
                <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-300">S/ {{ number_format($data['gastos'], 2) }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Compras del Período</p>
                <p class="mt-1 text-2xl font-bold text-orange-600 dark:text-orange-300">S/ {{ number_format($data['compras'], 2) }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Utilidad Neta</p>
                <p class="mt-1 text-2xl font-bold {{ $data['utilidad_neta'] >= 0 ? 'text-green-600 dark:text-green-300' : 'text-red-600 dark:text-red-300' }}">S/ {{ number_format($data['utilidad_neta'], 2) }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Margen Neto</p>
                <p class="mt-1 text-2xl font-bold {{ $data['margen_neto'] >= 10 ? 'text-green-600 dark:text-green-300' : ($data['margen_neto'] >= 0 ? 'text-yellow-600 dark:text-yellow-300' : 'text-red-600 dark:text-red-300') }}">{{ $data['margen_neto'] }}%</p>
            </div>
        </div>

        {{-- Tabla Estado de Resultados --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Estado de Resultados</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    {{-- INGRESOS --}}
                    <tr class="bg-blue-50/50 dark:bg-blue-900/10">
                        <td class="px-5 py-2.5 text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Ingresos</td>
                        <td class="px-5 py-2.5"></td>
                    </tr>
                    <tr>
                        <td class="px-5 py-2.5 text-gray-800 dark:text-gray-200 font-medium">Ventas Netas</td>
                        <td class="px-5 py-2.5 text-right font-medium text-gray-900 dark:text-gray-100">S/ {{ number_format($data['ventas'], 2) }}</td>
                    </tr>

                    {{-- COSTOS --}}
                    <tr class="bg-yellow-50/50 dark:bg-yellow-900/10">
                        <td class="px-5 py-2.5 text-xs font-semibold text-yellow-700 dark:text-yellow-300 uppercase tracking-wider">Costos</td>
                        <td class="px-5 py-2.5"></td>
                    </tr>
                    <tr>
                        <td class="px-5 py-2.5 pl-10 text-gray-600 dark:text-gray-400">Costo de Ventas</td>
                        <td class="px-5 py-2.5 text-right text-yellow-600 dark:text-yellow-400">(S/ {{ number_format($data['costo_ventas'], 2) }})</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-2.5 pl-10 text-gray-600 dark:text-gray-400">Compras del Período</td>
                        <td class="px-5 py-2.5 text-right text-orange-600 dark:text-orange-400">(S/ {{ number_format($data['compras'], 2) }})</td>
                    </tr>

                    {{-- UTILIDAD BRUTA --}}
                    <tr class="bg-green-50/50 dark:bg-green-900/10">
                        <td class="px-5 py-2.5 text-xs font-semibold text-green-700 dark:text-green-300 uppercase tracking-wider">Resultado Bruto</td>
                        <td class="px-5 py-2.5 text-right font-bold {{ $data['utilidad_bruta'] >= 0 ? 'text-green-700 dark:text-green-300' : 'text-red-600 dark:text-red-400' }}">S/ {{ number_format($data['utilidad_bruta'], 2) }}</td>
                    </tr>
                    <tr class="text-xs text-gray-400 dark:text-gray-500">
                        <td class="px-5 pb-2 pl-10">Margen Bruto</td>
                        <td class="px-5 pb-2 text-right">{{ $data['margen_bruto'] }}%</td>
                    </tr>

                    {{-- GASTOS --}}
                    <tr class="bg-red-50/50 dark:bg-red-900/10">
                        <td class="px-5 py-2.5 text-xs font-semibold text-red-700 dark:text-red-300 uppercase tracking-wider">Gastos Operativos</td>
                        <td class="px-5 py-2.5"></td>
                    </tr>
                    <tr>
                        <td class="px-5 py-2.5 pl-10 text-gray-600 dark:text-gray-400">Gastos del Período</td>
                        <td class="px-5 py-2.5 text-right text-red-600 dark:text-red-400">(S/ {{ number_format($data['gastos'], 2) }})</td>
                    </tr>

                    {{-- RESULTADO NETO --}}
                    <tr class="bg-green-50 dark:bg-green-900/20 border-t-2 border-green-300 dark:border-green-700">
                        <td class="px-5 py-3 font-bold text-gray-900 dark:text-gray-100">Utilidad Neta</td>
                        <td class="px-5 py-3 text-right font-bold text-lg {{ $data['utilidad_neta'] >= 0 ? 'text-green-700 dark:text-green-300' : 'text-red-600 dark:text-red-400' }}">S/ {{ number_format($data['utilidad_neta'], 2) }}</td>
                    </tr>
                    <tr class="text-xs text-gray-400 dark:text-gray-500">
                        <td class="px-5 pb-2 pl-10">Margen Neto</td>
                        <td class="px-5 pb-2 text-right">{{ $data['margen_neto'] }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Gráfico --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Evolución Mensual (6 meses)</h3>
            <canvas id="pnlChart" height="100"></canvas>
        </div>

        {{-- Nota --}}
        <div class="text-xs text-gray-400 dark:text-gray-500 italic">
            * Los valores mostrados corresponden al período {{ $data['desde'] }} al {{ $data['hasta'] }}.
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('pnlChart'), {
            type: 'bar',
            data: {
                labels: @json($chart['labels']),
                datasets: [
                    { label: 'Ventas', data: @json($chart['ventas']),
                      backgroundColor: 'rgba(59,130,246,.7)', borderRadius: 4 },
                    { label: 'Costos', data: @json($chart['costos']),
                      backgroundColor: 'rgba(245,158,11,.7)', borderRadius: 4 },
                    { label: 'Utilidad', data: @json($chart['utilidad']),
                      backgroundColor: 'rgba(16,185,129,.7)', borderRadius: 4 },
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top', labels: { usePointStyle: true } } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => 'S/ ' + v.toLocaleString() } } }
            }
        });
    </script>
    @endpush
</x-filament-panels::page>

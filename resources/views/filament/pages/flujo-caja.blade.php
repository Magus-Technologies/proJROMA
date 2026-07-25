@php
    $data = app(\App\Filament\Pages\FlujoCaja::class)->getData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border-l-4 border-blue-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Saldo en Caja</p>
                <p class="mt-1 text-2xl font-bold text-blue-700 dark:text-blue-300">
                    S/ {{ number_format($data['saldo_caja'], 2) }}
                </p>
            </div>
            <div class="rounded-xl border-l-4 border-green-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Por Cobrar (CxC)</p>
                <p class="mt-1 text-2xl font-bold text-green-700 dark:text-green-300">
                    S/ {{ number_format($data['cxc_total'], 2) }}
                </p>
            </div>
            <div class="rounded-xl border-l-4 border-red-500 bg-white dark:bg-gray-800 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Por Pagar (CxP)</p>
                <p class="mt-1 text-2xl font-bold text-red-700 dark:text-red-300">
                    S/ {{ number_format($data['cxp_total'], 2) }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Cuentas por Cobrar</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Por vencer</span>
                        <span class="font-medium text-green-600 dark:text-green-400">S/ {{ number_format($data['cxc_por_vencer'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Vencido</span>
                        <span class="font-medium text-red-600 dark:text-red-400">S/ {{ number_format($data['cxc_vencido'], 2) }}</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2 flex justify-between text-sm font-semibold">
                        <span class="text-gray-900 dark:text-gray-100">Total CxC</span>
                        <span>S/ {{ number_format($data['cxc_total'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Cuentas por Pagar</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Por vencer</span>
                        <span class="font-medium text-green-600 dark:text-green-400">S/ {{ number_format($data['cxp_por_vencer'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Vencido</span>
                        <span class="font-medium text-red-600 dark:text-red-400">S/ {{ number_format($data['cxp_vencido'], 2) }}</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2 flex justify-between text-sm font-semibold">
                        <span class="text-gray-900 dark:text-gray-100">Total CxP</span>
                        <span>S/ {{ number_format($data['cxp_total'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Proyección de Flujo de Caja</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-400">Período</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600 dark:text-gray-400">Ingresos Estimados</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600 dark:text-gray-400">Egresos Estimados</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600 dark:text-gray-400">Flujo Neto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($data['proyeccion'] as $periodo => $valores)
                        <tr>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ $periodo }}</td>
                            <td class="px-4 py-3 text-right text-green-600 dark:text-green-400">S/ {{ number_format($valores['ingresos'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-red-600 dark:text-red-400">S/ {{ number_format($valores['egresos'], 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $valores['neto'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                S/ {{ number_format($valores['neto'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-900 font-semibold">
                    <tr>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Saldo Actual + Proyectado</td>
                        @php
                            $totalIngresos = collect($data['proyeccion'])->sum('ingresos');
                            $totalEgresos = collect($data['proyeccion'])->sum('egresos');
                            $totalNeto = $data['saldo_caja'] + $totalIngresos - $totalEgresos;
                        @endphp
                        <td class="px-4 py-3 text-right text-green-600">S/ {{ number_format($totalIngresos, 2) }}</td>
                        <td class="px-4 py-3 text-right text-red-600">S/ {{ number_format($totalEgresos, 2) }}</td>
                        <td class="px-4 py-3 text-right {{ $totalNeto >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            S/ {{ number_format($totalNeto, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-xs text-gray-400 dark:text-gray-500 italic">
            * Datos basados en cuentas por cobrar (CxC), cuentas por pagar (CxP) y saldo de caja actual.
        </div>
    </div>
</x-filament-panels::page>

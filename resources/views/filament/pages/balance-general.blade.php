@php
    $data = app(\App\Filament\Pages\BalanceGeneral::class)->getData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Filtro --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha de Corte</label>
                    <input type="date" name="fecha" value="{{ $data['fecha'] }}"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Generar Balance
                </button>
            </form>
        </div>

        {{-- Balance --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- ACTIVO --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-3 bg-green-50 dark:bg-green-900/20 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-green-700 dark:text-green-300 uppercase tracking-wide">Activo</h3>
                </div>
                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($data['activo'] as $c)
                                <tr class="{{ $c['nivel'] === 1 ? 'bg-green-50/50 dark:bg-green-900/5 font-semibold' : ($c['nivel'] === 2 ? '' : 'text-gray-600 dark:text-gray-400') }}">
                                    <td class="px-4 py-1.5 {{ $c['nivel'] === 3 ? 'pl-10' : ($c['nivel'] === 2 ? 'pl-7' : '') }}">
                                        {{ $c['label'] }}
                                    </td>
                                    <td class="px-4 py-1.5 text-right {{ $c['nivel'] === 1 ? 'font-bold text-green-700 dark:text-green-300' : '' }}">
                                        S/ {{ number_format($c['saldo'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-4 py-4 text-center text-gray-400">Sin movimientos</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-green-100 dark:bg-green-900/30 border-t-2 border-green-300 dark:border-green-700">
                            <tr>
                                <td class="px-4 py-2.5 font-bold text-green-800 dark:text-green-200">TOTAL ACTIVO</td>
                                <td class="px-4 py-2.5 text-right font-bold text-green-800 dark:text-green-200">S/ {{ number_format($data['total_activo'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- PASIVO + PATRIMONIO --}}
            <div class="space-y-4">
                {{-- PASIVO --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-3 bg-yellow-50 dark:bg-yellow-900/20 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-yellow-700 dark:text-yellow-300 uppercase tracking-wide">Pasivo</h3>
                    </div>
                    <div class="overflow-x-auto max-h-48 overflow-y-auto">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($data['pasivo'] as $c)
                                    <tr class="{{ $c['nivel'] === 1 ? 'bg-yellow-50/50 dark:bg-yellow-900/5 font-semibold' : ($c['nivel'] === 2 ? '' : 'text-gray-600 dark:text-gray-400') }}">
                                        <td class="px-4 py-1.5 {{ $c['nivel'] === 3 ? 'pl-10' : ($c['nivel'] === 2 ? 'pl-7' : '') }}">
                                            {{ $c['label'] }}
                                        </td>
                                        <td class="px-4 py-1.5 text-right {{ $c['nivel'] === 1 ? 'font-bold text-yellow-700 dark:text-yellow-300' : '' }}">
                                            S/ {{ number_format($c['saldo'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-4 text-center text-gray-400">Sin movimientos</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-yellow-100 dark:bg-yellow-900/30 border-t-2 border-yellow-300 dark:border-yellow-700">
                                <tr>
                                    <td class="px-4 py-2 font-bold text-yellow-800 dark:text-yellow-200">TOTAL PASIVO</td>
                                    <td class="px-4 py-2 text-right font-bold text-yellow-800 dark:text-yellow-200">S/ {{ number_format($data['total_pasivo'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- PATRIMONIO --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-3 bg-blue-50 dark:bg-blue-900/20 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wide">Patrimonio</h3>
                    </div>
                    <div class="overflow-x-auto max-h-48 overflow-y-auto">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($data['patrimonio'] as $c)
                                    <tr class="{{ $c['nivel'] === 1 ? 'bg-blue-50/50 dark:bg-blue-900/5 font-semibold' : ($c['nivel'] === 2 ? '' : 'text-gray-600 dark:text-gray-400') }}">
                                        <td class="px-4 py-1.5 {{ $c['nivel'] === 3 ? 'pl-10' : ($c['nivel'] === 2 ? 'pl-7' : '') }}">
                                            {{ $c['label'] }}
                                        </td>
                                        <td class="px-4 py-1.5 text-right {{ $c['nivel'] === 1 ? 'font-bold text-blue-700 dark:text-blue-300' : '' }}">
                                            S/ {{ number_format($c['saldo'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-4 text-center text-gray-400">Sin movimientos</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-blue-100 dark:bg-blue-900/30 border-t-2 border-blue-300 dark:border-blue-700">
                                <tr>
                                    <td class="px-4 py-2 font-bold text-blue-800 dark:text-blue-200">TOTAL PATRIMONIO</td>
                                    <td class="px-4 py-2 text-right font-bold text-blue-800 dark:text-blue-200">S/ {{ number_format($data['total_patrimonio'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- ECUACIÓN CONTABLE --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 {{ abs($data['diferencia']) < 0.01 ? 'border-green-300 dark:border-green-700' : 'border-red-300 dark:border-red-700' }} overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Ecuación Contable</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Total Activo</span>
                            <span class="font-mono font-semibold text-green-700 dark:text-green-300">S/ {{ number_format($data['total_activo'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Total Pasivo + Patrimonio</span>
                            <span class="font-mono font-semibold text-blue-700 dark:text-blue-300">S/ {{ number_format($data['total_pasivo_patrimonio'], 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 flex justify-between text-sm font-bold">
                            <span class="{{ abs($data['diferencia']) < 0.01 ? 'text-green-600' : 'text-red-600' }}">
                                {{ abs($data['diferencia']) < 0.01 ? '✅ Balanceado' : '⚠ Diferencia' }}
                            </span>
                            <span class="font-mono {{ abs($data['diferencia']) < 0.01 ? 'text-green-600' : 'text-red-600' }}">
                                S/ {{ number_format($data['diferencia'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-xs text-gray-400 dark:text-gray-500 italic space-y-0.5">
            <p>* Balance generado al {{ $data['fecha'] }} con los asientos registrados (no anulados). Solo se muestran cuentas con saldo &ne; 0.</p>
            <p>* Pasivo y patrimonio se muestran con su naturaleza acreedora (Haber − Debe). El Resultado del Ejercicio (Ingresos − Costos − Gastos) se incluye en el patrimonio para que la ecuación contable cierre.</p>
        </div>
    </div>
</x-filament-panels::page>

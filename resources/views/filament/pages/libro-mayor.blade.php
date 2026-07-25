@php
    $data = app(\App\Filament\Pages\LibroMayor::class)->getData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Filtros --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cuenta Contable</label>
                    <select name="cuenta_id"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm min-w-[280px]">
                        <option value="">Seleccionar cuenta</option>
                        @foreach($data['cuentas'] as $c)
                            <option value="{{ $c->id }}" {{ $data['cuenta_id'] == $c->id ? 'selected' : '' }}>
                                {{ $c->codigo }} - {{ $c->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                    <input type="date" name="desde" value="{{ $data['desde'] }}"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                    <input type="date" name="hasta" value="{{ $data['hasta'] }}"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Consultar
                </button>
            </form>
        </div>

        @if($data['cuenta'])
            {{-- Header Cuenta --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $data['cuenta']->codigo }} - {{ $data['cuenta']->nombre }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Tipo: {{ \App\Models\PlanCuenta::tipos()[$data['cuenta']->tipo] ?? $data['cuenta']->tipo }}
                            · Nivel: {{ $data['cuenta']->nivel }}
                            · Período: {{ $data['desde'] }} al {{ $data['hasta'] }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Saldo Inicial</p>
                        <p class="text-lg font-bold {{ $data['saldo_inicial'] >= 0 ? 'text-blue-600 dark:text-blue-300' : 'text-red-600 dark:text-red-300' }}">
                            S/ {{ number_format($data['saldo_inicial'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tabla de Movimientos --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                            <tr>
                                <th class="text-left px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Fecha</th>
                                <th class="text-left px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">N°</th>
                                <th class="text-left px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Glosa</th>
                                <th class="text-right px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Debe</th>
                                <th class="text-right px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Haber</th>
                                <th class="text-right px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            {{-- Fila saldo inicial --}}
                            <tr class="bg-blue-50/50 dark:bg-blue-900/10 font-medium">
                                <td class="px-4 py-2 text-gray-500 italic">{{ $data['desde'] }}</td>
                                <td class="px-4 py-2 text-gray-500 italic">---</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300 italic">Saldo inicial</td>
                                <td class="px-4 py-2"></td>
                                <td class="px-4 py-2"></td>
                                <td class="px-4 py-2 text-right font-bold text-blue-700 dark:text-blue-300">S/ {{ number_format($data['saldo_inicial'], 2) }}</td>
                            </tr>

                            @forelse($data['rows'] as $r)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $r['fecha']?->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 font-mono text-sm text-gray-900 dark:text-gray-100">{{ $r['numero'] }}</td>
                                    <td class="px-4 py-2 text-gray-800 dark:text-gray-200 max-w-xs truncate">
                                        {{ $r['glosa'] }}
                                        @if($r['detalle_glosa'])
                                            <span class="text-gray-400 italic text-xs"> - {{ $r['detalle_glosa'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-right font-mono text-blue-700 dark:text-blue-300">
                                        {{ $r['debe'] > 0 ? 'S/ ' . number_format($r['debe'], 2) : '' }}
                                    </td>
                                    <td class="px-4 py-2 text-right font-mono text-green-700 dark:text-green-300">
                                        {{ $r['haber'] > 0 ? 'S/ ' . number_format($r['haber'], 2) : '' }}
                                    </td>
                                    <td class="px-4 py-2 text-right font-mono font-medium {{ $r['saldo'] >= 0 ? 'text-blue-700 dark:text-blue-300' : 'text-red-600 dark:text-red-400' }}">
                                        S/ {{ number_format($r['saldo'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-400">No hay movimientos en el período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($data['rows']->count() > 0)
                            <tfoot class="bg-gray-50 dark:bg-gray-900 border-t-2 border-gray-300 dark:border-gray-600">
                                <tr>
                                    <td colspan="3" class="px-4 py-2.5 font-semibold text-gray-700 dark:text-gray-300">TOTALES</td>
                                    <td class="px-4 py-2.5 text-right font-semibold text-blue-700 dark:text-blue-300">S/ {{ number_format($data['total_debe'], 2) }}</td>
                                    <td class="px-4 py-2.5 text-right font-semibold text-green-700 dark:text-green-300">S/ {{ number_format($data['total_haber'], 2) }}</td>
                                    <td class="px-4 py-2.5 text-right font-semibold {{ $data['rows']->last()['saldo'] >= 0 ? 'text-blue-700 dark:text-blue-300' : 'text-red-600 dark:text-red-400' }}">
                                        S/ {{ number_format($data['rows']->last()['saldo'], 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Seleccione una cuenta contable y presione "Consultar" para ver sus movimientos.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>

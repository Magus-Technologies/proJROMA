@php
    // La diferencia se calcula contra el esperado del TURNO (recalculado de
    // los movimientos), no contra el saldo_sistema guardado — así el modal
    // es correcto incluso para cierres registrados con la lógica antigua.
    $diferencia = round($cierre->saldo_declarado - $esperado, 2);
@endphp

<div class="space-y-4">

    {{-- Resumen del turno --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="rounded-lg bg-gray-50 dark:bg-gray-900/50 p-3">
            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Fondo de apertura</p>
            <p class="text-base font-bold text-gray-900 dark:text-gray-100">S/ {{ number_format($fondo, 2) }}</p>
            @if($apertura)
                <p class="text-[11px] text-gray-400">{{ \Carbon\Carbon::parse($apertura->created_at)->format('d/m/Y H:i') }}</p>
            @endif
        </div>
        <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 p-3">
            <p class="text-[11px] font-medium uppercase tracking-wide text-emerald-700 dark:text-emerald-400">(+) Ingresos del turno</p>
            <p class="text-base font-bold text-emerald-700 dark:text-emerald-400">S/ {{ number_format($ingresos, 2) }}</p>
        </div>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-3">
            <p class="text-[11px] font-medium uppercase tracking-wide text-red-700 dark:text-red-400">(−) Egresos del turno</p>
            <p class="text-base font-bold text-red-700 dark:text-red-400">S/ {{ number_format($egresos, 2) }}</p>
        </div>
        <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-3">
            <p class="text-[11px] font-medium uppercase tracking-wide text-blue-700 dark:text-blue-400">= Esperado en caja</p>
            <p class="text-base font-bold text-blue-700 dark:text-blue-400">S/ {{ number_format($esperado, 2) }}</p>
        </div>
        <div class="rounded-lg bg-gray-50 dark:bg-gray-900/50 p-3">
            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Declarado al cierre</p>
            <p class="text-base font-bold text-gray-900 dark:text-gray-100">S/ {{ number_format($cierre->saldo_declarado, 2) }}</p>
        </div>
        <div class="rounded-lg p-3 {{ abs($diferencia) < 0.01 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
            <p class="text-[11px] font-medium uppercase tracking-wide {{ abs($diferencia) < 0.01 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }}">
                {{ abs($diferencia) < 0.01 ? 'Cuadre exacto' : ($diferencia < 0 ? 'Faltante' : 'Sobrante') }}
            </p>
            <p class="text-base font-bold {{ abs($diferencia) < 0.01 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }}">
                S/ {{ number_format(abs($diferencia), 2) }}
            </p>
        </div>
    </div>

    {{-- Movimientos del turno --}}
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 max-h-80 overflow-y-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                <tr class="text-left text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <th class="px-3 py-2 font-medium">Hora</th>
                    <th class="px-3 py-2 font-medium">Tipo</th>
                    <th class="px-3 py-2 font-medium">Categoría</th>
                    <th class="px-3 py-2 font-medium">Descripción</th>
                    <th class="px-3 py-2 font-medium text-right">Monto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($movimientos as $m)
                    <tr class="{{ $m->estado !== 'CONFIRMADO' ? 'opacity-50 line-through' : '' }}">
                        <td class="px-3 py-2 whitespace-nowrap text-gray-500 dark:text-gray-400">
                            {{ $m->created_at ? \Carbon\Carbon::parse($m->created_at)->format('d/m H:i') : '—' }}
                        </td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $m->tipo === 'INGRESO' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' }}">
                                {{ $m->tipo === 'INGRESO' ? 'Ingreso' : 'Egreso' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ ucfirst(strtolower($m->categoria ?? '')) }}</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-300 max-w-[260px] truncate">{{ $m->descripcion }}</td>
                        <td class="px-3 py-2 text-right font-medium whitespace-nowrap {{ $m->tipo === 'INGRESO' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $m->tipo === 'INGRESO' ? '+' : '−' }} S/ {{ number_format($m->monto, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">Sin movimientos en el turno</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-[11px] italic text-gray-400 dark:text-gray-500">
        * Movimientos registrados entre la apertura y el cierre del turno. Los tachados fueron anulados y no cuentan para el cuadre.
    </p>
</div>

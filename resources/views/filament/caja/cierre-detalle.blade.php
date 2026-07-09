{{-- Conteo declarado en un cierre de caja (solo lectura) --}}
@php
    $diferencia = round($cierre->saldo_declarado - $cierre->saldo_sistema, 2);
@endphp
<div class="space-y-3">
    <div class="grid grid-cols-3 gap-3 text-sm">
        <div>
            <p class="text-gray-500 dark:text-gray-400">Declarado</p>
            <p class="font-bold text-gray-900 dark:text-white text-lg">S/ {{ number_format($cierre->saldo_declarado, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-500 dark:text-gray-400">Según sistema</p>
            <p class="font-bold text-gray-900 dark:text-white text-lg">S/ {{ number_format($cierre->saldo_sistema, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-500 dark:text-gray-400">Diferencia</p>
            <p class="font-bold text-lg {{ $diferencia == 0 ? 'text-green-700 dark:text-green-300' : 'text-red-600 dark:text-red-400' }}">
                {{ $diferencia > 0 ? '+' : '' }}S/ {{ number_format($diferencia, 2) }}
                {{ $diferencia < 0 ? '(faltante)' : ($diferencia > 0 ? '(sobrante)' : '') }}
            </p>
        </div>
    </div>

    @if ($detalles->isEmpty())
        <p class="text-sm text-gray-500">Sin desglose de efectivo (se declaró un monto fijo).</p>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 text-left">
                    <tr>
                        <th class="px-3 py-2 font-medium">Denominación</th>
                        <th class="px-3 py-2 font-medium text-center">Cantidad</th>
                        <th class="px-3 py-2 font-medium text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($detalles as $d)
                        <tr>
                            <td class="px-3 py-2">{{ ($d['tipo'] ?? '') === 'BILLETE' ? 'Billete' : 'Moneda' }} S/ {{ number_format($d['denominacion'] ?? 0, 2) }}</td>
                            <td class="px-3 py-2 text-center">{{ $d['cantidad'] ?? 0 }}</td>
                            <td class="px-3 py-2 text-right">S/ {{ number_format($d['subtotal'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-800 font-semibold">
                    <tr>
                        <td class="px-3 py-2" colspan="2">Total efectivo contado</td>
                        <td class="px-3 py-2 text-right">S/ {{ number_format($detalles->sum('subtotal'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    @if ($cierre->observaciones)
        <p class="text-sm text-gray-500 dark:text-gray-400">Observaciones: {{ $cierre->observaciones }}</p>
    @endif
</div>

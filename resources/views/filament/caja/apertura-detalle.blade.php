{{-- Detalle de la apertura del día (solo lectura) --}}
<div class="space-y-3">
    <div class="grid grid-cols-3 gap-3 text-sm">
        <div>
            <p class="text-gray-500 dark:text-gray-400">Fecha y hora</p>
            <p class="font-semibold text-gray-900 dark:text-white">
                {{ \Carbon\Carbon::parse($apertura->fecha)->format('d/m/Y') }}
                {{ $apertura->created_at ? \Carbon\Carbon::parse($apertura->created_at)->format('H:i') : '' }}
            </p>
        </div>
        <div>
            <p class="text-gray-500 dark:text-gray-400">Aperturada por</p>
            <p class="font-semibold text-gray-900 dark:text-white">{{ $usuario ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500 dark:text-gray-400">Monto de apertura</p>
            <p class="font-bold text-green-700 dark:text-green-300 text-lg">S/ {{ number_format($apertura->monto_total, 2) }}</p>
        </div>
    </div>

    @if ($detalles->isEmpty())
        <p class="text-sm text-gray-500">Sin desglose de efectivo (se ingresó un monto fijo).</p>
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
                            <td class="px-3 py-2">{{ $d->tipo === 'BILLETE' ? 'Billete' : 'Moneda' }} S/ {{ number_format($d->denominacion, 2) }}</td>
                            <td class="px-3 py-2 text-center">{{ $d->cantidad }}</td>
                            <td class="px-3 py-2 text-right">S/ {{ number_format($d->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-800 font-semibold">
                    <tr>
                        <td class="px-3 py-2" colspan="2">Total contado</td>
                        <td class="px-3 py-2 text-right">S/ {{ number_format($detalles->sum('subtotal'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    @if ($apertura->observaciones)
        <p class="text-sm text-gray-500 dark:text-gray-400">Observaciones: {{ $apertura->observaciones }}</p>
    @endif
</div>

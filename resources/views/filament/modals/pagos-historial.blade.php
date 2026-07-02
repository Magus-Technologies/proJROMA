<div class="space-y-4">
    <div class="grid grid-cols-3 gap-4 text-sm">
        <div>
            <p class="text-gray-500 dark:text-gray-400">Total</p>
            <p class="font-bold text-gray-900 dark:text-white">S/ {{ number_format($total, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-500 dark:text-gray-400">Pagado</p>
            <p class="font-bold text-green-600 dark:text-green-400">S/ {{ number_format($pagado, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-500 dark:text-gray-400">Saldo</p>
            <p class="font-bold text-red-600 dark:text-red-400">S/ {{ number_format(max(0, $total - $pagado), 2) }}</p>
        </div>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-gray-500 dark:text-gray-400">
                <th class="py-2 pr-3">Fecha</th>
                <th class="py-2 pr-3 text-right">Monto</th>
                <th class="py-2 pr-3">Método</th>
                <th class="py-2">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pagos as $pago)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="py-2 pr-3">{{ $pago->fecha?->format('d/m/Y') ?? '—' }}</td>
                    <td class="py-2 pr-3 text-right font-medium">S/ {{ number_format($pago->monto, 2) }}</td>
                    <td class="py-2 pr-3">{{ $pago->instrumento_tipo ?: '—' }}</td>
                    <td class="py-2">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $pago->estado === '1'
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                            {{ $pago->estado === '1' ? 'Activo' : 'Anulado' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-gray-400">Sin pagos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

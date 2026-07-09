{{-- Últimos movimientos de una caja (modal de solo lectura) --}}
<div class="space-y-3">
    <div class="flex items-center justify-between text-sm">
        <span class="text-gray-500 dark:text-gray-400">
            Responsable: <strong class="text-gray-900 dark:text-white">{{ trim(($caja->responsable?->nombres ?? '') . ' ' . ($caja->responsable?->apellidos ?? '')) ?: '—' }}</strong>
        </span>
        <span class="text-gray-500 dark:text-gray-400">
            Saldo actual: <strong class="text-green-700 dark:text-green-300">S/ {{ number_format($caja->saldo_actual ?? 0, 2) }}</strong>
        </span>
    </div>

    @if ($movimientos->isEmpty())
        <p class="text-center text-sm text-gray-500 py-6">Esta caja no tiene movimientos todavía.</p>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 text-left">
                    <tr>
                        <th class="px-3 py-2 font-medium">Fecha</th>
                        <th class="px-3 py-2 font-medium">Tipo</th>
                        <th class="px-3 py-2 font-medium">Descripción</th>
                        <th class="px-3 py-2 font-medium text-right">Monto</th>
                        <th class="px-3 py-2 font-medium text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($movimientos as $m)
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $m->tipo === 'INGRESO' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                                    {{ $m->tipo }}
                                </span>
                            </td>
                            <td class="px-3 py-2">{{ \Illuminate\Support\Str::limit($m->descripcion, 45) }}</td>
                            <td class="px-3 py-2 text-right whitespace-nowrap">S/ {{ number_format($m->monto, 2) }}</td>
                            <td class="px-3 py-2 text-right whitespace-nowrap">S/ {{ number_format($m->saldo_posterior ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400">Se muestran los últimos {{ $movimientos->count() }} movimientos.</p>
    @endif
</div>

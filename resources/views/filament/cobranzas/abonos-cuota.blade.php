{{-- Estado de la cuota: cards informativos + tabla de abonos --}}
@php
    $abonado   = $abonos->where('estado', 'ACTIVO')->sum('monto');
    $pendiente = max(0, (float) $cuota->monto - $abonado);
@endphp
<div class="space-y-4">
    {{-- Cards informativos --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Monto de la cuota</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">S/ {{ number_format($cuota->monto, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Vence: {{ $cuota->fecha?->format('d/m/Y') ?? '—' }}</p>
        </div>
        <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 p-4">
            <p class="text-sm text-blue-600 dark:text-blue-400">Abonado</p>
            <p class="text-xl font-bold text-blue-700 dark:text-blue-300">S/ {{ number_format($abonado, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $abonos->where('estado', 'ACTIVO')->count() }} abono(s) activo(s)</p>
        </div>
        <div class="rounded-xl border p-4 {{ $pendiente <= 0 ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20' : 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20' }}">
            <p class="text-sm {{ $pendiente <= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">Saldo pendiente</p>
            <p class="text-xl font-bold {{ $pendiente <= 0 ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                S/ {{ number_format($pendiente, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ $pendiente <= 0 ? 'Cuota cancelada' : 'Por cobrar' }}</p>
        </div>
    </div>

    {{-- Tabla de abonos --}}
    @if ($abonos->isEmpty())
        <p class="text-center text-sm text-gray-500 py-3">Sin abonos registrados todavía.</p>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 text-left">
                    <tr>
                        <th class="px-3 py-2 font-medium">#</th>
                        <th class="px-3 py-2 font-medium">Fecha</th>
                        <th class="px-3 py-2 font-medium text-right">Monto</th>
                        <th class="px-3 py-2 font-medium">Método</th>
                        <th class="px-3 py-2 font-medium">N° operación</th>
                        <th class="px-3 py-2 font-medium">Registró</th>
                        <th class="px-3 py-2 font-medium">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($abonos as $a)
                        <tr class="{{ $a->estado === 'ANULADO' ? 'opacity-50' : '' }}">
                            <td class="px-3 py-2">{{ $a->id }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">{{ $a->fecha?->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 text-right whitespace-nowrap {{ $a->estado === 'ANULADO' ? 'line-through' : '' }}">
                                S/ {{ number_format($a->monto, 2) }}
                            </td>
                            <td class="px-3 py-2">{{ \App\Services\CajaService::etiquetaMetodoPago($a->metodo_pago) }}</td>
                            <td class="px-3 py-2">{{ $a->referencia ?: '—' }}</td>
                            <td class="px-3 py-2">{{ $a->usuario?->nombres ?? '—' }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $a->estado === 'ACTIVO' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                                    {{ $a->estado === 'ACTIVO' ? 'Activo' : 'Anulado' }}
                                </span>
                                @if ($a->estado === 'ANULADO' && $a->motivo_anulacion)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $a->motivo_anulacion }}</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

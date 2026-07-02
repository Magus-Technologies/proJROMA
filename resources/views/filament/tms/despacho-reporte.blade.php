@php
    $porArticulo = $data['por_articulo'];
    $porCliente  = $data['por_cliente'];
    $totKilos = $porArticulo->sum('kilos');
    $totCant  = $porArticulo->sum('cantidad');
    $totMonto = $porCliente->sum('total');
@endphp

<div class="space-y-6 text-sm">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div><div class="text-xs text-gray-500">Ruta</div><div class="font-semibold">{{ $despacho->ruta->nombre ?? '-' }}</div></div>
        <div><div class="text-xs text-gray-500">Fecha</div><div class="font-semibold">{{ optional($despacho->fecha_reparto)->format('d/m/Y') }}</div></div>
        <div><div class="text-xs text-gray-500">Vehículo</div><div class="font-semibold">{{ $despacho->vehiculo->placa ?? '-' }}</div></div>
        <div><div class="text-xs text-gray-500">Conductor</div><div class="font-semibold">{{ $despacho->conductor->nombres ?? '-' }}</div></div>
    </div>

    {{-- Por artículo (hoja de carga) --}}
    <div>
        <h3 class="mb-2 font-bold text-gray-700 dark:text-gray-200">Por artículo (carga)</h3>
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 text-gray-500 dark:bg-gray-800">
                    <tr>
                        <th class="px-3 py-2 text-left">Código</th>
                        <th class="px-3 py-2 text-left">Descripción</th>
                        <th class="px-3 py-2 text-right">Cant.</th>
                        <th class="px-3 py-2 text-right">Kilos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($porArticulo as $a)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="px-3 py-1.5">{{ $a->codigo }}</td>
                            <td class="px-3 py-1.5">{{ $a->descripcion }}</td>
                            <td class="px-3 py-1.5 text-right">{{ number_format((float) $a->cantidad, 2) }}</td>
                            <td class="px-3 py-1.5 text-right font-semibold">{{ number_format((float) $a->kilos, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400">Sin artículos.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-300 bg-gray-50 font-bold dark:bg-gray-800">
                        <td class="px-3 py-2" colspan="2">Total general</td>
                        <td class="px-3 py-2 text-right">{{ number_format((float) $totCant, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format((float) $totKilos, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Por cliente --}}
    <div>
        <h3 class="mb-2 font-bold text-gray-700 dark:text-gray-200">Por cliente (reparto)</h3>
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 text-gray-500 dark:bg-gray-800">
                    <tr>
                        <th class="px-3 py-2 text-left">Documento</th>
                        <th class="px-3 py-2 text-left">Denominación</th>
                        <th class="px-3 py-2 text-right">Pedidos</th>
                        <th class="px-3 py-2 text-right">Kilos</th>
                        <th class="px-3 py-2 text-right">Total S/</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($porCliente as $c)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="px-3 py-1.5">{{ $c->documento ?: '-' }}</td>
                            <td class="px-3 py-1.5">{{ $c->denominacion }}</td>
                            <td class="px-3 py-1.5 text-right">{{ $c->pedidos }}</td>
                            <td class="px-3 py-1.5 text-right">{{ number_format((float) $c->kilos, 2) }}</td>
                            <td class="px-3 py-1.5 text-right font-semibold">{{ number_format((float) $c->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">Sin clientes.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-300 bg-gray-50 font-bold dark:bg-gray-800">
                        <td class="px-3 py-2" colspan="4">Total general</td>
                        <td class="px-3 py-2 text-right">{{ number_format((float) $totMonto, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

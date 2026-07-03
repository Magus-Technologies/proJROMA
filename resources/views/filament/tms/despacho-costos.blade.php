@php $total = $costos->sum('monto'); @endphp

<div class="text-sm">
    @if($costos->isEmpty())
        <p class="py-6 text-center text-gray-400">Este despacho no tiene costos registrados.</p>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 text-gray-500 dark:bg-gray-800">
                    <tr>
                        <th class="px-3 py-2 text-left">Concepto</th>
                        <th class="px-3 py-2 text-left">Caja</th>
                        <th class="px-3 py-2 text-right">Monto (S/)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($costos as $c)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="px-3 py-1.5">{{ $c->concepto }}</td>
                            <td class="px-3 py-1.5 text-gray-500">{{ $c->caja }}</td>
                            <td class="px-3 py-1.5 text-right font-semibold">{{ number_format((float) $c->monto, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-300 bg-gray-50 font-bold dark:bg-gray-800">
                        <td class="px-3 py-2" colspan="2">Total</td>
                        <td class="px-3 py-2 text-right">{{ number_format((float) $total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>

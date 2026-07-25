@php
    $data = app(\App\Filament\Pages\IndicadoresFinancieros::class)->getData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['Ventas del Mes', 'S/ '.number_format($data['ventas_mes'],2), 'info'],
                ['Utilidad Bruta', 'S/ '.number_format($data['utilidad_bruta'],2), 'success'],
                ['Utilidad Neta', 'S/ '.number_format($data['utilidad_neta'],2), 'success'],
                ['EBITDA', 'S/ '.number_format($data['ebitda'],2), 'purple'],
            ] as [$label, $value, $color])
                @php
                    $borders = ['info'=>'border-blue-500', 'success'=>'border-green-500', 'purple'=>'border-purple-500'];
                    $texts = ['info'=>'text-blue-700 dark:text-blue-300', 'success'=>'text-green-700 dark:text-green-300', 'purple'=>'text-purple-700 dark:text-purple-300'];
                @endphp
                <div class="rounded-xl border-l-4 {{ $borders[$color] }} bg-white dark:bg-gray-800 shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $label }}</p>
                    <p class="mt-1 text-xl font-bold {{ $texts[$color] }}">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['Margen Bruto', $data['margen_bruto'].'%', 'success'],
                ['Margen Neto', $data['margen_neto'].'%', 'success'],
                ['ROI', $data['roi'].'%', 'purple'],
                ['Liquidez Corriente', number_format($data['liquidez'],2), 'info'],
            ] as [$label, $value, $color])
                @php
                    $borders = ['info'=>'border-blue-500', 'success'=>'border-green-500', 'purple'=>'border-purple-500'];
                    $texts = ['info'=>'text-blue-700 dark:text-blue-300', 'success'=>'text-green-700 dark:text-green-300', 'purple'=>'text-purple-700 dark:text-purple-300'];
                @endphp
                <div class="rounded-xl border-l-4 {{ $borders[$color] }} bg-white dark:bg-gray-800 shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $label }}</p>
                    <p class="mt-1 text-xl font-bold {{ $texts[$color] }}">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach([
                ['Rotación de Inventario', number_format($data['rotacion_inventario'],2).' x', 'El inventario se renueva '.$data['rotacion_inventario'].' veces al mes', 'warning'],
                ['Punto de Equilibrio', 'S/ '.number_format($data['punto_equilibrio'],2), 'Ventas necesarias para cubrir gastos', 'warning'],
                ['Costo de Inventario', 'S/ '.number_format($data['inventario_costo'],2), 'Valor total del inventario en almacén', 'info'],
            ] as [$label, $value, $desc, $color])
                @php $borders = ['info'=>'border-blue-500', 'warning'=>'border-yellow-500']; @endphp
                <div class="rounded-xl border-l-4 {{ $borders[$color] }} bg-white dark:bg-gray-800 shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $label }}</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $value }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Resumen Financiero del Mes</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-400">Indicador</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600 dark:text-gray-400">Valor</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-400">Interpretación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $rows = [
                            ['Margen Bruto', $data['margen_bruto'].'%', $data['margen_bruto'] > 30 ? '✅ Saludable' : ($data['margen_bruto'] > 15 ? '⚠️ Aceptable' : '❌ Bajo')],
                            ['Margen Neto', $data['margen_neto'].'%', $data['margen_neto'] > 15 ? '✅ Saludable' : ($data['margen_neto'] > 5 ? '⚠️ Aceptable' : '❌ Bajo')],
                            ['Liquidez', number_format($data['liquidez'],2), $data['liquidez'] >= 1 ? '✅ Capacidad de pago adecuada' : '❌ Riesgo de liquidez'],
                            ['ROI', $data['roi'].'%', $data['roi'] > 0 ? '✅ Rentabilidad positiva' : '❌ Operando con pérdida'],
                            ['Rotación Inventario', number_format($data['rotacion_inventario'],2).' x mes', $data['rotacion_inventario'] > 0.5 ? '✅ Rotación adecuada' : '⚠️ Baja rotación'],
                        ];
                    @endphp
                    @foreach($rows as [$indicador, $valor, $interp])
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $indicador }}</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100">{{ $valor }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $interp }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-xs text-gray-400 dark:text-gray-500 italic">
            * Datos calculados automáticamente desde Ventas, Compras, Inventario y Caja del período actual.
        </div>
    </div>
</x-filament-panels::page>

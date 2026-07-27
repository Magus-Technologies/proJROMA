@php
    $page = app(\App\Filament\Pages\IndicadoresFinancieros::class);
    $data = $page->getData();
    $secciones = $page->getSecciones();

    $dot = [
        'ok'       => 'bg-emerald-500',
        'atencion' => 'bg-amber-500',
        'riesgo'   => 'bg-red-500',
        'info'     => 'bg-gray-400',
    ];
    $valorColor = [
        'ok'       => 'text-emerald-600 dark:text-emerald-400',
        'atencion' => 'text-amber-600 dark:text-amber-400',
        'riesgo'   => 'text-red-600 dark:text-red-400',
        'info'     => 'text-gray-900 dark:text-gray-100',
    ];
    $etiqueta = [
        'ok'       => 'Óptimo',
        'atencion' => 'Atención',
        'riesgo'   => 'Riesgo',
        'info'     => 'Informativo',
    ];
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Encabezado: período + exportar --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Período: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ ucfirst(now()->translatedFormat('F Y')) }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reporte.indicadores.pdf') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-red-500 transition">
                    <x-filament::icon icon="heroicon-o-document-arrow-down" class="h-4 w-4" />
                    Descargar PDF
                </a>
                <a href="{{ route('reporte.indicadores.xls') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                    <x-filament::icon icon="heroicon-o-table-cells" class="h-4 w-4" />
                    Descargar Excel
                </a>
            </div>
        </div>

        {{-- KPIs principales --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['Ventas del Mes', 'S/ '.number_format($data['ventas_mes'],2), 'border-blue-500', 'text-blue-700 dark:text-blue-300'],
                ['Utilidad Bruta', 'S/ '.number_format($data['utilidad_bruta'],2), $data['utilidad_bruta'] >= 0 ? 'border-emerald-500' : 'border-red-500', $data['utilidad_bruta'] >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300'],
                ['Utilidad Neta', 'S/ '.number_format($data['utilidad_neta'],2), $data['utilidad_neta'] >= 0 ? 'border-emerald-500' : 'border-red-500', $data['utilidad_neta'] >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300'],
                ['Margen Neto', $data['margen_neto'].'%', $data['margen_neto'] >= 10 ? 'border-emerald-500' : ($data['margen_neto'] >= 3 ? 'border-amber-500' : 'border-red-500'), $data['margen_neto'] >= 10 ? 'text-emerald-700 dark:text-emerald-300' : ($data['margen_neto'] >= 3 ? 'text-amber-700 dark:text-amber-300' : 'text-red-700 dark:text-red-300')],
            ] as [$label, $value, $border, $text])
                <div class="rounded-xl border-l-4 {{ $border }} bg-white dark:bg-gray-800 shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $label }}</p>
                    <p class="mt-1 text-xl font-bold {{ $text }}">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        {{-- Secciones con notas de interpretación --}}
        @foreach($secciones as $sec)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $sec['titulo'] }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <th class="px-4 py-2.5 font-medium">Indicador</th>
                                <th class="px-4 py-2.5 font-medium text-right">Valor</th>
                                <th class="px-4 py-2.5 font-medium hidden md:table-cell">Cómo se calcula</th>
                                <th class="px-4 py-2.5 font-medium">Interpretación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($sec['items'] as $item)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-2 font-medium text-gray-900 dark:text-gray-100">
                                            <span class="h-2 w-2 shrink-0 rounded-full {{ $dot[$item['estado']] }}" title="{{ $etiqueta[$item['estado']] }}"></span>
                                            {{ $item['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold whitespace-nowrap {{ $valorColor[$item['estado']] }}">
                                        {{ $item['valor'] }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-400 dark:text-gray-500 hidden md:table-cell">
                                        {{ $item['formula'] }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $item['nota'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        {{-- Leyenda + nota metodológica --}}
        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
            <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Óptimo</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-amber-500"></span> Atención</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-red-500"></span> Riesgo</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-gray-400"></span> Informativo</span>
        </div>
        <div class="text-xs text-gray-400 dark:text-gray-500 italic">
            * Indicadores calculados automáticamente desde Ventas, Compras, Inventario y Caja del mes en curso.
            Son aproximaciones operativas de gestión, no reemplazan a los estados financieros contables.
        </div>
    </div>
</x-filament-panels::page>

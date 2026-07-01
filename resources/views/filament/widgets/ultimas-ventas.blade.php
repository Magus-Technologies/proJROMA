<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Últimas Ventas</x-slot>

        <div class="overflow-x-auto -mx-4 -mb-4">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400">
                        <th class="px-4 py-2.5 text-left font-medium">Documento</th>
                        <th class="px-4 py-2.5 text-left font-medium">Cliente</th>
                        <th class="px-4 py-2.5 text-left font-medium">Fecha</th>
                        <th class="px-4 py-2.5 text-right font-medium">Total</th>
                        <th class="px-4 py-2.5 text-center font-medium">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($ultimasVentas as $v)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-4 py-2.5 font-mono font-semibold text-blue-600 dark:text-blue-400">
                                {{ $v->documento_completo ?? ($v->serie . '-' . $v->numero) }}
                            </td>
                            <td class="px-4 py-2.5 text-gray-600 dark:text-gray-300 max-w-[140px] truncate">
                                {{ $v->cliente?->datos ?? '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">
                                {{ $v->fecha_emision?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-right font-semibold text-gray-700 dark:text-gray-200">
                                S/ {{ number_format($v->total, 2) }}
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if($v->estado === '1')
                                    <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/40 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">Activa</span>
                                @else
                                    <span class="rounded-full bg-red-100 dark:bg-red-900/40 px-2 py-0.5 text-[10px] font-bold text-red-700 dark:text-red-400">Anulada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">Sin ventas registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

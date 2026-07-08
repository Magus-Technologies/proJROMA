<x-filament-panels::page>
    <div class="rounded-xl border border-amber-300 bg-amber-50 dark:border-amber-500/40 dark:bg-amber-500/10 p-3 text-sm text-amber-800 dark:text-amber-200">
        <strong>Importante:</strong> el número es el <em>último emitido</em> — el próximo documento usará ese número + 1.
        Al migrar, cargá dónde se quedó cada serie en el sistema anterior.
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-400">
                    <th class="px-4 py-2.5 font-medium">Documento</th>
                    <th class="px-4 py-2.5 font-medium text-center">Suc.</th>
                    <th class="px-4 py-2.5 font-medium">Serie</th>
                    <th class="px-4 py-2.5 font-medium">Último N° emitido</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach ($correlativos as $i => $row)
                    <tr class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="px-4 py-2 whitespace-nowrap">
                            <span class="mr-2 inline-flex items-center rounded-md bg-primary-50 px-1.5 py-0.5 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                                {{ $row['abreviatura'] ?: '—' }}
                            </span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $row['nombre'] }}</span>
                        </td>
                        <td class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">{{ $row['sucursal'] }}</td>
                        <td class="px-4 py-2">
                            <input
                                type="text"
                                maxlength="4"
                                wire:model="correlativos.{{ $i }}.serie"
                                class="block w-24 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm uppercase shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white"
                            />
                        </td>
                        <td class="px-4 py-2">
                            <input
                                type="number"
                                min="0"
                                wire:model="correlativos.{{ $i }}.numero"
                                class="block w-40 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-end">
        <x-filament::button wire:click="mountAction('guardar')" icon="heroicon-m-check">
            Guardar cambios
        </x-filament::button>
    </div>
</x-filament-panels::page>

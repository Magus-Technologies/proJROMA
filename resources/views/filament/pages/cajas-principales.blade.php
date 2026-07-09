<x-filament-panels::page>
    {{-- Resumen de cada caja principal del responsable --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach ($this->principales as $principal)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Caja principal</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $principal->nombre }}</p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-2xl font-bold text-green-700 dark:text-green-300">
                        S/ {{ number_format($principal->saldo_actual ?? 0, 2) }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                        {{ $principal->hijas_count }} {{ $principal->hijas_count === 1 ? 'caja hija' : 'cajas hijas' }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Cajas hijas a cargo --}}
    {{ $this->table }}
</x-filament-panels::page>

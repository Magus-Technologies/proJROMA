<x-filament-panels::page>
    @if (!$this->caja)
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-8 text-center">
            <x-heroicon-o-wallet class="mx-auto h-12 w-12 text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No tenés caja asignada</h3>
            <p class="mt-2 text-sm text-gray-500">Contactá al administrador para que te asigne una caja.</p>
        </div>
    @else
        {{-- Tarjetas resumen --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Caja</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $this->caja->nombre }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $this->caja->id_caja_padre ? 'Caja hija' : 'Caja principal' }}</p>
            </div>
            <div class="rounded-xl border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-900/20 p-5">
                <p class="text-sm text-green-600 dark:text-green-400">Saldo Actual</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                    S/ {{ number_format($this->caja->saldo_actual ?? 0, 2) }}
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $this->caja->estado === 'ACTIVA' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $this->caja->estado }}
                </span>
            </div>
        </div>

        {{-- Movimientos (tabla Filament) --}}
        {{ $this->table }}
    @endif
</x-filament-panels::page>

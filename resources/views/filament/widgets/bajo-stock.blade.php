<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <span class="flex items-center gap-1.5">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-4 w-4 text-amber-500" />
                Bajo Stock
            </span>
        </x-slot>

        <div class="divide-y divide-gray-50 dark:divide-gray-700 -mx-4 -mb-4">
            @forelse($bajoStock as $p)
                <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                        {{ $p->cantidad <= 0 ? 'bg-red-100 dark:bg-red-900/40' : 'bg-amber-50 dark:bg-amber-900/30' }}">
                        <x-filament::icon icon="heroicon-o-archive-box"
                            class="h-3.5 w-3.5 {{ $p->cantidad <= 0 ? 'text-red-500' : 'text-amber-500' }}" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="truncate text-xs font-medium text-gray-700 dark:text-gray-200">{{ $p->descripcion }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $p->codigo }}</p>
                    </div>
                    <span class="shrink-0 text-xs font-bold {{ $p->cantidad <= 0 ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400' }}">
                        {{ $p->cantidad }}
                    </span>
                </div>
            @empty
                <div class="px-5 py-8 text-center">
                    <x-filament::icon icon="heroicon-o-check-circle" class="mx-auto h-8 w-8 text-emerald-400" />
                    <p class="mt-1 text-xs text-gray-400">Stock en buen estado</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Top Clientes del Mes</x-slot>

        <div class="space-y-3">
            @forelse($topClientes as $i => $c)
                <div class="flex items-center gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[10px] font-bold
                        {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-slate-100 text-slate-600' : 'bg-blue-50 text-blue-600') }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate">{{ $c->nombre }}</p>
                        <div class="mt-1 h-1.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            @php $pct = $topClientes->count() && $topClientes->first()->total > 0
                                ? round($c->total / $topClientes->first()->total * 100)
                                : 0; @endphp
                            <div class="h-full rounded-full bg-blue-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-200 shrink-0">S/ {{ number_format($c->total, 0) }}</span>
                </div>
            @empty
                <p class="py-6 text-center text-xs text-gray-400">Sin ventas este mes</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

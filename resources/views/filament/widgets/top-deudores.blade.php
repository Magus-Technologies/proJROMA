<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Top Deudores</x-slot>

        <div class="space-y-3" style="max-height: 280px; overflow-y: auto;">
            @forelse($topDeudores as $i => $d)
                <div class="flex items-center gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[10px] font-bold
                        {{ $d->atraso > 30 ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' : ($d->atraso > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400') }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate">{{ $d->nombre }}</p>
                        <div class="mt-0.5 flex items-center gap-2">
                            <div class="h-1.5 flex-1 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                @php $pct = $topDeudores->first()->saldo > 0
                                    ? round($d->saldo / $topDeudores->first()->saldo * 100)
                                    : 0; @endphp
                                <div class="h-full rounded-full {{ $d->atraso > 30 ? 'bg-red-500' : ($d->atraso > 0 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="shrink-0 text-[10px] text-gray-400 dark:text-gray-500">
                                {{ $d->cuotas }} {{ $d->cuotas === 1 ? 'cuota' : 'cuotas' }}{{ $d->atraso > 0 ? ' · ' . $d->atraso . ' d atraso' : '' }}
                            </span>
                        </div>
                    </div>
                    <span class="text-xs font-bold shrink-0 {{ $d->atraso > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-200' }}">S/ {{ number_format($d->saldo, 0) }}</span>
                </div>
            @empty
                <div class="py-6 text-center">
                    <x-filament::icon icon="heroicon-o-check-circle" class="mx-auto h-8 w-8 text-emerald-400" />
                    <p class="mt-1 text-xs text-gray-400">Sin cuentas pendientes de cobro</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

{{-- Campanita con panel de notificaciones. Se refresca sola cada 60s. --}}
<div wire:poll.60s class="flex items-center">
    @if ($puedeVer || $cantidadStock > 0)
        <div x-data="{ abierto: false }" class="relative">
            {{-- Botón campana --}}
            <button
                type="button"
                @click="abierto = ! abierto"
                title="Notificaciones"
                class="fi-icon-btn relative flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 outline-none transition duration-75 hover:bg-gray-100 hover:text-gray-700 focus-visible:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>

                @if ($total > 0)
                    <span class="absolute -right-0.5 -top-0.5 flex min-w-[1.15rem] items-center justify-center rounded-full bg-danger-600 px-1 text-[0.7rem] font-semibold leading-tight text-white ring-2 ring-white dark:ring-gray-900">
                        {{ $total > 9 ? '9+' : $total }}
                    </span>
                @endif
            </button>

            {{-- Panel de notificaciones --}}
            <div
                x-show="abierto"
                x-transition.origin-top-right
                @click.outside="abierto = false"
                x-cloak
                style="display:none; width:26rem; max-width:calc(100vw - 2rem);"
                class="absolute right-0 z-50 mt-2 origin-top-right overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
            >
                {{-- Encabezado --}}
                <div class="flex items-center justify-between gap-2 px-5 py-3.5">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="h-5 w-5 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <span class="text-base font-semibold text-gray-900 dark:text-white">Notificaciones</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        @if ($cantidadStock > 0)
                            <span class="inline-flex items-center rounded-full bg-warning-50 px-2.5 py-0.5 text-xs font-semibold text-warning-700 dark:bg-warning-400/10 dark:text-warning-400">
                                {{ $cantidadStock }} stock
                            </span>
                        @endif
                        @if ($cantidad > 0)
                            <span class="inline-flex items-center rounded-full bg-danger-50 px-2.5 py-0.5 text-xs font-semibold text-danger-700 dark:bg-danger-400/10 dark:text-danger-400">
                                {{ $cantidad }} por cobrar
                            </span>
                        @endif
                    </div>
                </div>

                <div class="h-px bg-gray-100 dark:bg-white/10"></div>

                {{-- Sección: alertas de bajo stock --}}
                @if ($cantidadStock > 0)
                    <div class="flex items-center justify-between px-5 py-2 bg-warning-50/60 dark:bg-warning-400/5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-warning-700 dark:text-warning-400">Alertas</span>
                        <button
                            type="button"
                            wire:click="marcarStockLeidas"
                            class="text-xs font-medium text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            Marcar leídas
                        </button>
                    </div>
                    <div class="divide-y divide-gray-50 dark:divide-white/5">
                        @foreach ($alertasStock as $a)
                            <a href="{{ $a['url'] ?? $urlStock }}" class="flex items-start gap-3 px-5 py-3 transition hover:bg-gray-50 dark:hover:bg-white/5">
                                <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-warning-100 text-warning-600 dark:bg-warning-400/10 dark:text-warning-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $a['titulo'] }}</span>
                                    <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">{{ $a['cuerpo'] }}</span>
                                    <span class="mt-1 block text-[11px] text-gray-400 dark:text-gray-500">{{ $a['cuando'] }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="h-px bg-gray-100 dark:bg-white/10"></div>
                @endif

                {{-- Lista --}}
                <div style="max-height:24rem;" class="divide-y divide-gray-50 overflow-y-auto dark:divide-white/5">
                    @forelse ($notificaciones as $n)
                        <a
                            href="{{ $url }}"
                            class="flex items-start gap-3 px-5 py-3.5 transition hover:bg-gray-50 dark:hover:bg-white/5"
                        >
                            {{-- Ícono de estado --}}
                            <span @class([
                                'mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full',
                                'bg-danger-100 text-danger-600 dark:bg-danger-400/10 dark:text-danger-400' => $n['vencida'],
                                'bg-warning-100 text-warning-600 dark:bg-warning-400/10 dark:text-warning-400' => ! $n['vencida'],
                            ])>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            </span>

                            {{-- Contenido --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $n['cliente'] }}</span>
                                    <span class="shrink-0 text-sm font-bold text-gray-900 dark:text-white">S/ {{ number_format($n['monto'], 2) }}</span>
                                </div>
                                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    Cuota de {{ $n['documento'] }} &middot; vence {{ $n['fecha'] }}
                                </div>
                                <span @class([
                                    'mt-1.5 inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium',
                                    'bg-danger-50 text-danger-700 dark:bg-danger-400/10 dark:text-danger-400' => $n['vencida'],
                                    'bg-warning-50 text-warning-700 dark:bg-warning-400/10 dark:text-warning-400' => ! $n['vencida'],
                                ])>
                                    {{ $n['cuando'] }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="flex flex-col items-center gap-2 px-5 py-10 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="h-10 w-10 text-gray-300 dark:text-gray-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Estás al día. No hay cuotas por vencer.</span>
                        </div>
                    @endforelse
                </div>

                {{-- Pie --}}
                <div class="h-px bg-gray-100 dark:bg-white/10"></div>
                <a
                    href="{{ $url }}"
                    class="block px-5 py-3 text-center text-sm font-semibold text-primary-600 transition hover:bg-gray-50 dark:text-primary-400 dark:hover:bg-white/5"
                >
                    Ver todas en Cuentas por Cobrar
                </a>
            </div>
        </div>
    @endif
</div>

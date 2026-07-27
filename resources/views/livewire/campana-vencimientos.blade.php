{{-- Campanita con lista de notificaciones. Se refresca sola cada 60s. --}}
<div wire:poll.60s class="flex items-center">
    @if ($puedeVer)
        <div x-data="{ abierto: false }" class="relative">
            {{-- Botón campana --}}
            <button
                type="button"
                @click="abierto = ! abierto"
                title="Cuotas por vencer"
                class="fi-icon-btn relative flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 outline-none transition duration-75 hover:bg-gray-100 hover:text-gray-700 focus-visible:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>

                @if ($cantidad > 0)
                    <span class="absolute -right-1 -top-1 flex min-w-[1.15rem] items-center justify-center rounded-full bg-danger-600 px-1 text-[0.7rem] font-semibold leading-tight text-white ring-2 ring-white dark:ring-gray-900">
                        {{ $cantidad > 9 ? '9+' : $cantidad }}
                    </span>
                @endif
            </button>

            {{-- Desplegable --}}
            <div
                x-show="abierto"
                x-transition
                @click.outside="abierto = false"
                x-cloak
                style="display:none"
                class="absolute right-0 z-50 mt-2 w-80 origin-top-right overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
            >
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-2.5 dark:border-white/10">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Cuotas por vencer</span>
                    @if ($cantidad > 0)
                        <span class="rounded-full bg-danger-50 px-2 py-0.5 text-xs font-medium text-danger-700 dark:bg-danger-400/10 dark:text-danger-400">
                            {{ $cantidad }}
                        </span>
                    @endif
                </div>

                <div class="max-h-80 overflow-y-auto">
                    @forelse ($notificaciones as $n)
                        <a
                            href="{{ $url }}"
                            class="flex flex-col gap-0.5 border-b border-gray-50 px-4 py-2.5 transition hover:bg-gray-50 dark:border-white/5 dark:hover:bg-white/5"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ $n['cliente'] }}</span>
                                <span class="shrink-0 text-sm font-semibold text-gray-700 dark:text-gray-200">S/ {{ number_format($n['monto'], 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $n['documento'] }} &middot; {{ $n['fecha'] }}</span>
                                <span class="shrink-0 text-xs font-medium {{ $n['vencida'] ? 'text-danger-600 dark:text-danger-400' : 'text-warning-600 dark:text-warning-400' }}">
                                    {{ $n['cuando'] }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            No hay cuotas por vencer.
                        </div>
                    @endforelse
                </div>

                <a
                    href="{{ $url }}"
                    class="block border-t border-gray-100 px-4 py-2.5 text-center text-sm font-medium text-primary-600 transition hover:bg-gray-50 dark:border-white/10 dark:text-primary-400 dark:hover:bg-white/5"
                >
                    Ver Cuentas por Cobrar
                </a>
            </div>
        </div>
    @endif
</div>

{{-- Campanita de vencimientos. Se refresca sola cada 60s. --}}
<div wire:poll.60s class="flex items-center">
    @if ($puedeVer)
        <a
            href="{{ $url }}"
            @if ($cantidad > 0)
                title="{{ $cantidad }} boleta(s) por vencer en los próximos {{ $diasAviso }} días"
            @else
                title="Sin boletas por vencer"
            @endif
            class="fi-icon-btn relative flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 outline-none transition duration-75 hover:bg-gray-100 hover:text-gray-700 focus-visible:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>

            @if ($cantidad > 0)
                <span
                    class="absolute -right-1 -top-1 flex min-w-[1.15rem] items-center justify-center rounded-full bg-danger-600 px-1 text-[0.7rem] font-semibold leading-tight text-white ring-2 ring-white dark:ring-gray-900"
                >
                    {{ $cantidad > 9 ? '9+' : $cantidad }}
                </span>
            @endif
        </a>
    @endif
</div>

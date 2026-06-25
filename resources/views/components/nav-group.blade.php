{{-- Grupo desplegable del sidebar. Se abre automáticamente si :active=true --}}
@props(['icon', 'label', 'active' => false])
<div x-data="{ open: @js($active) }">
    <button type="button" @click="open=!open"
            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 transition-all hover:bg-white/10 hover:text-white"
            :class="[open ? 'text-white' : 'text-blue-100/80', collapsed ? 'justify-center px-1' : '']">
        <i class="{{ $icon }} text-sm shrink-0" :class="open ? 'text-blue-300' : 'text-blue-400/60'"></i>
        <span x-show="!collapsed" class="flex-1 truncate text-left text-xs">{{ $label }}</span>
        <i x-show="!collapsed" class="ti ti-chevron-down text-xs text-blue-400/60"
           :class="open && 'rotate-180'" style="transition:transform .2s"></i>
    </button>

    <div x-show="open && !collapsed" x-cloak x-collapse.duration.200ms
         class="mt-0.5 ml-4 space-y-0.5 border-l border-white/10 pl-2">
        {{ $slot }}
    </div>
</div>

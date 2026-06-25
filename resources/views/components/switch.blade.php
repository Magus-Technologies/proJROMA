{{-- Switch (toggle) reutilizable. Muestra onLabel/offLabel según el estado. --}}
@props(['id' => null, 'checked' => true, 'onLabel' => 'Activo', 'offLabel' => 'Inactivo'])
<label class="inline-flex cursor-pointer select-none items-center gap-2">
    <input type="checkbox" @if($id) id="{{ $id }}" @endif
           {{ $attributes->merge(['class' => 'peer sr-only']) }} @checked($checked)>
    <span class="relative h-5 w-9 shrink-0 rounded-full bg-gray-300 transition-colors peer-checked:bg-brand-500
                 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white
                 after:shadow after:transition-transform peer-checked:after:translate-x-4"></span>
    <span class="hidden text-xs font-semibold text-emerald-600 peer-checked:inline">{{ $onLabel }}</span>
    <span class="inline text-xs font-semibold text-gray-400 peer-checked:hidden">{{ $offLabel }}</span>
</label>

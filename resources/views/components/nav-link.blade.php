{{-- resources/views/components/nav-link.blade.php --}}
@props(['href', 'icon', 'label'])
@php $active = request()->url() === $href || str_starts_with(request()->url(), rtrim($href,'/').'/' ); @endphp
<a href="{{ $href }}"
   class="flex items-center gap-2.5 rounded-lg px-3 py-2 transition-all
          {{ $active ? 'bg-white/15 text-white font-semibold' : 'text-blue-100/80 hover:bg-white/10 hover:text-white' }}">
    <i class="{{ $icon }} text-sm {{ $active ? 'text-blue-300' : 'text-blue-400/60' }}"></i>
    <span class="flex-1 truncate text-xs">{{ $label }}</span>
    @if($active)<span class="h-1.5 w-1.5 rounded-full bg-blue-300 shrink-0"></span>@endif
</a>

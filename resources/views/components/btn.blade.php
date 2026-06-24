@props(['href' => null, 'icon' => null, 'color' => 'primary', 'size' => 'sm', 'outline' => false])
@php
  $map = [
    'primary' => $outline ? 'btn-outline' : 'btn-primary',
    'red'     => $outline ? 'btn-outline' : 'btn-danger',
    'emerald' => $outline ? 'btn-outline' : 'btn-emerald',
    'ghost'   => 'btn-ghost',
  ];
  $sizes = ['xs' => 'btn-xs', 'sm' => 'btn-sm', 'md' => 'btn-md'];
  $class = "btn {$sizes[$size]} {$map[$color]}";
@endphp
@if($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>
    @if($icon) <i class="{{ $icon }}"></i> @endif
    {{ $slot }}
  </a>
@else
  <button {{ $attributes->merge(['class' => $class, 'type' => 'button']) }}>
    @if($icon) <i class="{{ $icon }}"></i> @endif
    {{ $slot }}
  </button>
@endif

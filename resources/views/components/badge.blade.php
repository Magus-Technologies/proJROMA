@props(['color' => 'gray'])
@php
  $colors = [
    'ok'    => 'bg-emerald-100 text-emerald-700',
    'no'    => 'bg-red-100 text-red-700',
    'pend'  => 'bg-amber-100 text-amber-700',
    'act'   => 'bg-blue-100 text-blue-700',
    'anu'   => 'bg-gray-100 text-gray-600',
    'gray'  => 'bg-gray-100 text-gray-600',
  ];
@endphp
<span {{ $attributes->merge(['class' => "inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold {$colors[$color]}"]) }}>
  {{ $slot }}
</span>

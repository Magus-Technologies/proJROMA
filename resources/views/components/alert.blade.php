@props(['type' => 'success'])
@php
  $types = [
    'success' => ['border-emerald-200', 'bg-emerald-50', 'text-emerald-700', 'ti-circle-check', 'text-emerald-500'],
    'error'   => ['border-red-200', 'bg-red-50', 'text-red-700', 'ti-alert-circle', 'text-red-500'],
    'warning' => ['border-amber-200', 'bg-amber-50', 'text-amber-700', 'ti-alert-triangle', 'text-amber-500'],
    'info'    => ['border-blue-200', 'bg-blue-50', 'text-blue-700', 'ti-info-circle', 'text-blue-500'],
  ];
  [$border, $bg, $text, $icon, $iconColor] = $types[$type];
@endphp
<div x-data="{show:true}" x-show="show"
     x-init="setTimeout(()=>show=false,4500)"
     class="mb-4 flex items-center gap-3 rounded-xl border {{ $border }} {{ $bg }} px-4 py-3 text-sm {{ $text }} fade-in">
  <i class="ti {{ $icon }} {{ $iconColor }}"></i>
  <span>{{ $slot }}</span>
  <button @click="show=false" class="ml-auto {{ str_replace('text-', 'text-', $text) }} opacity-60 hover:opacity-100">
    <i class="ti ti-x"></i>
  </button>
</div>

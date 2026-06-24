@props(['icon', 'color' => 'blue', 'title' => '', 'href' => null])
@php
  $colors = ['blue' => 'bg-blue-50 hover:bg-blue-100 text-blue-600', 'red' => 'bg-red-50 hover:bg-red-100 text-red-600', 'gray' => 'bg-gray-50 hover:bg-gray-100 text-gray-600', 'emerald' => 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600'];
  $class = "h-7 w-7 flex items-center justify-center rounded-lg transition {$colors[$color]}";
@endphp
@if($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $class, 'title' => $title]) }} target="_blank">
    <i class="ti {{ $icon }} text-sm"></i>
  </a>
@else
  <button {{ $attributes->merge(['class' => $class, 'title' => $title, 'type' => 'button']) }}>
    <i class="ti {{ $icon }} text-sm"></i>
  </button>
@endif

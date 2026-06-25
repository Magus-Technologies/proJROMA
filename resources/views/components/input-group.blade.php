@props(['for' => null, 'label', 'required' => false, 'optional' => null, 'help' => null])
@php $optional = $optional ?? ! $required; @endphp
<div {{ $attributes->whereDoesntStartWith('class') }}>
  <x-label :for="$for" :required="$required" :optional="$optional">{{ $label }}</x-label>
  {{ $slot }}
  @if($help) <p class="mt-0.5 text-[10px] text-gray-400">{{ $help }}</p> @endif
</div>

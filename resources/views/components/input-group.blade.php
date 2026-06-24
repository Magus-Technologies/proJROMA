@props(['for', 'label', 'required' => false, 'help' => null])
<div {{ $attributes->whereDoesntStartWith('class') }}>
  <x-label for="{{ $for }}" :required="$required">{{ $label }}</x-label>
  {{ $slot }}
  @if($help) <p class="mt-0.5 text-[10px] text-gray-400">{{ $help }}</p> @endif
</div>

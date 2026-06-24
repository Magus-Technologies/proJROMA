@props(['align' => 'left'])
<th {{ $attributes->merge(['class' => "px-3 py-2.5 text-{$align}"]) }}>
  {{ $slot }}
</th>

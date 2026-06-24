@props(['align' => 'left', 'mono' => false])
<td {{ $attributes->merge(['class' => "px-3 py-2 text-{$align}" . ($mono ? ' font-mono' : '')]) }}>
  {{ $slot }}
</td>

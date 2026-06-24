@props(['icon' => null, 'prepend' => null])
<div class="flex-1">
  @if($prepend)
    <div class="flex gap-2">
      <input {{ $attributes->merge(['class' => 'field flex-1']) }}>
      {{ $prepend }}
    </div>
  @else
    <input {{ $attributes->merge(['class' => 'field']) }}>
  @endif
</div>

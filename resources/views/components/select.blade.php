@props(['options' => [], 'placeholder' => null])
<select {{ $attributes->merge(['class' => 'field bg-white']) }}>
  @if($placeholder)
    <option value="">{{ $placeholder }}</option>
  @endif
  @foreach($options as $value => $label)
    <option value="{{ $value }}">{{ $label }}</option>
  @endforeach
  {{ $slot ?? '' }}
</select>

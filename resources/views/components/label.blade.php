@props(['required' => false, 'optional' => false])
<label {{ $attributes->merge(['class' => 'block text-xs font-semibold text-gray-600 mb-1']) }}>
  {{ $slot }}
  @if($required)
    <span class="req-star" title="Campo obligatorio">*</span>
  @elseif($optional)
    <span class="opt-badge" title="Campo opcional">opcional</span>
  @endif
</label>

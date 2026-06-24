@props(['title', 'subtitle' => null])
<div class="card-header">
  <div>
    <h3 class="card-header__title">{{ $title }}</h3>
    @if($subtitle) <p class="text-xs text-gray-400" style="margin:0">{{ $subtitle }}</p> @endif
  </div>
  {{ $slot ?? '' }}
</div>

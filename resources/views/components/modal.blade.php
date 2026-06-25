@props(['id', 'title', 'maxWidth' => 'max-w-xl', 'size' => null])
<div id="{{ $id }}" class="fixed inset-0 z-50 hidden items-start justify-center pt-10 px-4">
  <div class="absolute inset-0 bg-black/50" onclick="cerrarModal('{{ $id }}')"></div>
  <div class="relative z-10 w-full {{ $size ?? $maxWidth }} rounded-2xl bg-white shadow-2xl overflow-hidden">
    <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-4">
      <h4 class="text-sm font-semibold text-gray-700">{{ $title }}</h4>
      <button onclick="cerrarModal('{{ $id }}')" class="text-gray-400 hover:text-gray-600">
        <i class="ti ti-x"></i>
      </button>
    </div>
    <div class="p-5 {{ $bodyClass ?? '' }}">
      {{ $slot }}
    </div>
    @isset($footer)
      <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-3">
        {{ $footer }}
      </div>
    @endisset
  </div>
</div>

@once
@push('scripts')
<script>
  function abrirModal(id) { document.getElementById(id)?.classList?.replace('hidden','flex'); }
  function cerrarModal(id) { document.getElementById(id)?.classList?.replace('flex','hidden'); }
</script>
@endpush
@endonce

{{--
  Buscador reutilizable (estilo único: .search-input).

  Uso libre:        <x-search placeholder="Buscar cliente..." x-model="q" />
  Conectado a tabla: <x-search :for="'tblProductos'" />   (busca en esa DataTable)
--}}
@props(['for' => null, 'placeholder' => 'Buscar...', 'width' => 'w-full sm:w-64'])

<div class="relative {{ $width }}">
    <i class="ti ti-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
    <input type="search" autocomplete="off"
           @if($for) data-dt-search="{{ $for }}" @endif
           placeholder="{{ $placeholder }}"
           {{ $attributes->merge(['class' => 'search-input']) }}>
</div>

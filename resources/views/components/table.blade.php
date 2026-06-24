{{--
  Tabla reutilizable del sistema. Se inicializa con initDataTable('#id', {...})
  y hereda automáticamente:
    • Responsive  → en móvil cada fila se vuelve tarjeta
    • ColReorder  → arrastrar encabezado para mover columnas
    • colVis      → ícono arriba para mostrar/ocultar columnas
    • Tamaño fijo aunque no haya datos

  Uso:
    <x-table id="tblX" title="Mi tabla">
        <x-slot:filters> ...selects... </x-slot:filters>   (opcional)
        <x-slot:thead>
            <x-th>Código</x-th> <x-th align="right">Precio</x-th> ...
        </x-slot:thead>
    </x-table>
--}}
@props(['id', 'title' => null, 'loading' => true, 'search' => true])

<div class="card">
    <div class="card-header">
        <h3 class="card-header__title">{{ $title }}</h3>
        <div class="flex flex-1 flex-wrap items-center justify-end gap-3">
            @if($search)
                <x-search :for="$id" />
            @endif
            {{ $filters ?? '' }}
            {{-- Aquí DataTables coloca el ícono de mostrar/ocultar columnas --}}
            <span id="{{ $id }}-tools" class="flex items-center"></span>
            @if($loading)
                <span id="{{ $id }}-loading" class="hidden"><i class="ti ti-loader-2 text-brand-500 spin"></i></span>
            @endif
        </div>
    </div>

    <div class="p-3 sm:p-4">
        <table id="{{ $id }}" {{ $attributes->merge(['class' => 'w-full text-xs']) }} style="width:100%">
            <thead class="bg-gray-50 text-gray-500">
                <tr>{{ $thead }}</tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

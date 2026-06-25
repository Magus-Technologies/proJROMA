@extends('layouts.app')
@section('title','Recepción')
@section('page-title','Recepción')
@section('breadcrumb','Inventario / Recepción')

@section('content')
<div class="mb-3 rounded-xl border border-blue-100 bg-blue-50 px-4 py-2.5 text-xs text-blue-700">
    <i class="ti ti-info-circle"></i> Registro de recepciones. Las compras se recepcionan desde <strong>Inventario → Compras</strong>; aquí queda el documento de cada recepción.
</div>

{{-- Registro de recepciones (cabecera) --}}
<x-table id="tblRecepciones" title="Recepciones registradas">
    <x-slot:thead>
        <x-th align="center">N°</x-th>
        <x-th>Fecha</x-th>
        <x-th>Compra</x-th>
        <x-th>Proveedor</x-th>
        <x-th>Almacén</x-th>
        <x-th align="center">Ítems</x-th>
        <x-th>Usuario</x-th>
        <x-th align="center">Detalle</x-th>
    </x-slot:thead>
</x-table>

{{-- Detalle de la recepción seleccionada --}}
<div class="mt-5">
    <x-table id="tblRecDet" title="Detalle de recepción" :search="false">
        <x-slot:thead>
            <x-th>Código</x-th>
            <x-th>Producto</x-th>
            <x-th align="center">Unidad</x-th>
            <x-th align="center">Cantidad recibida</x-th>
        </x-slot:thead>
    </x-table>
</div>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
let tablaRec, tablaDet = null;

$(function () {
    tablaRec = initDataTable('#tblRecepciones', {
        ajax: { url: `${BASE}/api/recepcion/registro`, dataSrc: '',
                beforeSend: () => $('#tblRecepciones-loading').removeClass('hidden'),
                complete:   () => $('#tblRecepciones-loading').addClass('hidden') },
        columns: [
            { data: 'id_recepcion', className: 'text-center font-bold' },
            { data: 'fecha', render: v => v ? new Date(v.replace(' ', 'T')).toLocaleString('es-PE', {dateStyle:'short', timeStyle:'short'}) : '-' },
            { data: 'compra_doc', defaultContent: '-', responsivePriority: 1 },
            { data: 'proveedor', defaultContent: '-' },
            { data: 'almacen_nombre', defaultContent: '-' },
            { data: 'items', className: 'text-center font-bold' },
            { data: 'usuario', defaultContent: '-' },
            { data: 'id_recepcion', orderable: false, searchable: false, className: 'text-center no-colvis',
              render: id => `<button onclick="verDetalle(${id})" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 hover:bg-blue-100 px-3 py-1.5 text-[11px] font-semibold text-blue-600"><i class="ti ti-eye"></i> Ver</button>` },
        ],
        order: [[0, 'desc']],
        rowCallback: row => { row.style.cursor = 'pointer'; },
    });

    $('#tblRecepciones tbody').on('click', 'tr', function () {
        const data = tablaRec.row(this).data();
        if (!data) return;
        $('#tblRecepciones tbody tr').css('background', '');
        $(this).css('background', '#dbeafe');
        verDetalle(data.id_recepcion);
    });
});

function verDetalle(id) {
    const url = `${BASE}/api/recepcion/detalle-recepcion?id=${id}`;
    const titleEl = document.querySelector('#tblRecDet').closest('.card').querySelector('.card-header__title');
    if (titleEl) titleEl.textContent = `Detalle de recepción #${id}`;
    if (tablaDet) { tablaDet.ajax.url(url).load(); return; }
    tablaDet = initDataTable('#tblRecDet', {
        ajax: { url, dataSrc: '' },
        columns: [
            { data: 'codigo', defaultContent: '-' },
            { data: 'producto', defaultContent: '-', responsivePriority: 1 },
            { data: 'unidad', defaultContent: '-', className: 'text-center' },
            { data: 'cantidad', className: 'text-center font-bold' },
        ],
        order: [],
    });
}
</script>
@endpush

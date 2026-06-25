@extends('layouts.app')
@section('title','Recepción')
@section('page-title','Recepción de Compras')
@section('breadcrumb','Inventario / Recepción')

@section('content')
<x-table id="tblRecepcion" title="Compras pendientes de recepcionar">
    <x-slot:thead>
        <x-th>#</x-th>
        <x-th>Documento</x-th>
        <x-th>Fecha</x-th>
        <x-th>Proveedor</x-th>
        <x-th align="center">Ítems</x-th>
        <x-th align="right">Total</x-th>
        <x-th align="center">Acción</x-th>
    </x-slot:thead>
</x-table>

{{-- Modal recepcionar --}}
<x-modal id="md-recepcion" title="Recepcionar compra" size="max-w-md">
    <input type="hidden" id="rec-compra">
    <div class="space-y-4">
        <div class="rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600">
            <div>Compra <strong id="rec-doc"></strong></div>
            <div class="text-gray-400" id="rec-info"></div>
        </div>
        <x-input-group label="Almacén destino" :required="true" help="Los productos de la compra ingresarán a este almacén.">
            <select id="rec-almacen" class="field bg-white"></select>
        </x-input-group>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-recepcion')">Cancelar</x-btn>
        <x-btn color="emerald" icon="ti ti-package-import" onclick="confirmarRecepcion()">Recepcionar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tablaRec, almacenesRec = [];

$(async function () {
    almacenesRec = await apiGet(`${BASE}/api/almacenes`, { activos: 1 });
    tablaRec = initDataTable('#tblRecepcion', {
        ajax: {
            url: `${BASE}/api/recepcion/pendientes`, dataSrc: '',
            beforeSend: () => $('#tblRecepcion-loading').removeClass('hidden'),
            complete:   () => $('#tblRecepcion-loading').addClass('hidden'),
        },
        columns: [
            { data: 'id_compra' },
            { data: 'documento', defaultContent: '-', responsivePriority: 1 },
            { data: 'fecha_emision', defaultContent: '-' },
            { data: 'proveedor', defaultContent: '-' },
            { data: 'items', className: 'text-center' },
            { data: 'total', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
            { data: 'id_compra', orderable: false, searchable: false, className: 'text-center no-colvis',
              render: (id, t, row) => `<button onclick='abrirRecepcion(${JSON.stringify(row)})' class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-[11px] font-semibold text-white"><i class="ti ti-package-import"></i> Recepcionar</button>` },
        ],
        order: [[0, 'desc']],
    });
});

function abrirRecepcion(row) {
    g('rec-compra').value = row.id_compra;
    g('rec-doc').textContent = row.documento || ('#' + row.id_compra);
    g('rec-info').textContent = `${row.proveedor || '-'} · ${row.items} ítem(s) · S/ ${parseFloat(row.total || 0).toFixed(2)}`;
    g('rec-almacen').innerHTML = almacenesRec.map(a => `<option value="${a.codigo ?? a.id_almacen}">${a.nombre}</option>`).join('');
    abrirModal('md-recepcion');
}

async function confirmarRecepcion() {
    const id_compra = g('rec-compra').value;
    const almacen = g('rec-almacen').value;
    if (!almacen) { toastWarn('Selecciona un almacén.'); return; }
    const d = await apiPost(`${BASE}/api/recepcion/recepcionar`, { id_compra, almacen });
    if (d.res) {
        toastOk(`Recepción registrada (${d.items} ítems).`);
        cerrarModal('md-recepcion');
        tablaRec.ajax.reload(null, false);
    } else {
        Swal.fire({ icon: 'warning', title: 'No se pudo recepcionar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
    }
}
</script>
@endpush

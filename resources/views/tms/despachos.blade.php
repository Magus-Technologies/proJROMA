@extends('layouts.app')
@section('title','Despachos')
@section('page-title','Despachos')
@section('breadcrumb','TMS / Despachos')

@section('content')
<div>
    <x-table id="tblDespachos" title="Despachos">
        <x-slot:filters>
            <x-btn color="primary" icon="ti ti-plus" :href="route('tms.armar')">Armar Despacho</x-btn>
        </x-slot:filters>
        <x-slot:thead>
            <x-th>Código</x-th>
            <x-th align="center">Fecha</x-th>
            <x-th>Ruta</x-th>
            <x-th>Vehículo</x-th>
            <x-th>Conductor</x-th>
            <x-th align="center">Pedidos</x-th>
            <x-th align="right">Peso</x-th>
            <x-th align="center">Estado</x-th>
            <x-th align="center">Acciones</x-th>
        </x-slot:thead>
    </x-table>

    {{-- Modal detalle --}}
    <x-modal id="md-despacho" title="Detalle del despacho" size="max-w-3xl">
        <div id="dsp-cabecera" class="mb-4 grid grid-cols-2 gap-3 text-xs md:grid-cols-4"></div>
        <x-table id="tblDspPuntos" :search="false">
            <x-slot:thead>
                <x-th align="center">#</x-th>
                <x-th>Cliente</x-th>
                <x-th>Dirección</x-th>
                <x-th align="right">Peso</x-th>
                <x-th align="center">Entrega</x-th>
                <x-th align="center">Acción</x-th>
            </x-slot:thead>
        </x-table>
        <x-slot:footer>
            <div id="dsp-acciones" class="flex flex-1 flex-wrap items-center justify-between gap-2">
                <div id="dsp-estado-btns" class="flex gap-2"></div>
                <x-btn color="ghost" onclick="cerrarModal('md-despacho')">Cerrar</x-btn>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
@endsection

@push('scripts')
<script>
const BASE = BASE_URL;
const g = id => document.getElementById(id);
let tblDespachos, tblDspPuntos, dspActual = null;

const ESTADO_BADGE = {
    PLANIFICADO: '<span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">Planificado</span>',
    CARGADO:     '<span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Cargado</span>',
    EN_RUTA:     '<span class="inline-flex rounded-full bg-indigo-100 px-2 py-0.5 text-[10px] font-bold text-indigo-700">En ruta</span>',
    CERRADO:     '<span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Cerrado</span>',
    ANULADO:     '<span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Anulado</span>',
};
const ENTREGA_BADGE = {
    PENDIENTE: '<span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Pendiente</span>',
    ENTREGADO: '<span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Entregado</span>',
    RECHAZADO: '<span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Rechazado</span>',
    PARCIAL:   '<span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Parcial</span>',
};

$(function () {
    tblDespachos = initDataTable('#tblDespachos', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/tms/despachos', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: [
            { data: 'codigo', defaultContent: '-' },
            { data: 'fecha_reparto', className: 'text-center', render: v => (v || '').split('T')[0] },
            { data: 'ruta', defaultContent: '-' },
            { data: 'vehiculo', defaultContent: '-' },
            { data: 'conductor', defaultContent: '-' },
            { data: 'pedidos', className: 'text-center' },
            { data: 'peso_total', className: 'text-right font-bold', render: v => parseFloat(v || 0).toFixed(2) + ' kg' },
            { data: 'estado', className: 'text-center', orderable: false, render: v => ESTADO_BADGE[v] || v },
            { data: 'id', orderable: false, className: 'text-center no-colvis',
              render: id => `<button onclick="verDespacho(${id})" class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600" title="Ver"><i class="ti ti-eye text-sm"></i></button>` },
        ],
        order: [[0, 'desc']],
    });
});

async function verDespacho(id) {
    const d = await apiGet(BASE + '/api/tms/despachos/' + id);
    if (!d.res) { toastErr(d.msg || 'Error.'); return; }
    dspActual = d.despacho;

    const c = d.despacho;
    g('dsp-cabecera').innerHTML = `
        <div><p class="text-[10px] font-bold uppercase text-gray-400">Código</p><p class="font-bold text-gray-700">${c.codigo || '-'}</p></div>
        <div><p class="text-[10px] font-bold uppercase text-gray-400">Fecha</p><p class="text-gray-700">${(c.fecha_reparto || '').split('T')[0]}</p></div>
        <div><p class="text-[10px] font-bold uppercase text-gray-400">Ruta</p><p class="text-gray-700">${c.ruta || '-'}</p></div>
        <div><p class="text-[10px] font-bold uppercase text-gray-400">Estado</p><p>${ESTADO_BADGE[c.estado] || c.estado}</p></div>
        <div><p class="text-[10px] font-bold uppercase text-gray-400">Vehículo</p><p class="text-gray-700">${c.vehiculo || '-'} (${parseFloat(c.capacidad_kg || 0).toFixed(0)} kg)</p></div>
        <div><p class="text-[10px] font-bold uppercase text-gray-400">Conductor</p><p class="text-gray-700">${c.conductor || '-'}</p></div>
        <div><p class="text-[10px] font-bold uppercase text-gray-400">Peso total</p><p class="font-bold text-brand-600">${parseFloat(c.peso_total || 0).toFixed(2)} kg</p></div>
        <div><p class="text-[10px] font-bold uppercase text-gray-400">Observaciones</p><p class="text-gray-700">${c.observaciones || '-'}</p></div>
    `;

    const puedeEntregar = ['CARGADO', 'EN_RUTA'].includes(c.estado);
    if (tblDspPuntos) tblDspPuntos.destroy();
    tblDspPuntos = initDataTable('#tblDspPuntos', {
        processing: false, serverSide: false, searching: false, paging: false, info: false, ordering: false, dom: 'rt',
        data: d.pedidos || [],
        columns: [
            { data: 'orden', className: 'text-center w-10' },
            { data: 'cliente', defaultContent: '-' },
            { data: 'direccion', defaultContent: '-', className: 'text-xs text-gray-500' },
            { data: 'peso', className: 'text-right', render: v => parseFloat(v || 0).toFixed(2) + ' kg' },
            { data: 'estado_entrega', className: 'text-center', render: v => ENTREGA_BADGE[v] || v },
            { data: 'id', className: 'text-center w-24', render: (id, t, row) => {
                if (!puedeEntregar || row.estado_entrega !== 'PENDIENTE') return '<span class="text-gray-300">—</span>';
                return `<div class="flex justify-center gap-1">
                    <button onclick="entregar(${id}, 'ENTREGADO')" class="h-6 w-6 inline-flex items-center justify-center rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-600" title="Entregado"><i class="ti ti-check text-xs"></i></button>
                    <button onclick="entregar(${id}, 'RECHAZADO')" class="h-6 w-6 inline-flex items-center justify-center rounded-md bg-red-50 hover:bg-red-100 text-red-600" title="Rechazado"><i class="ti ti-x text-xs"></i></button>
                </div>`;
            } },
        ],
    });

    // Botones de estado del despacho
    const acciones = {
        PLANIFICADO: [['CARGAR', 'Cargar', 'primary', 'ti-package'], ['ANULAR', 'Anular', 'red', 'ti-ban']],
        CARGADO:     [['SALIR', 'Salir a ruta', 'primary', 'ti-truck-delivery'], ['ANULAR', 'Anular', 'red', 'ti-ban']],
        EN_RUTA:     [['CERRAR', 'Cerrar despacho', 'emerald', 'ti-lock']],
        CERRADO:     [],
        ANULADO:     [],
    };
    g('dsp-estado-btns').innerHTML = (acciones[c.estado] || []).map(([accion, label, color, icon]) =>
        `<button onclick="cambiarEstado('${accion}')" class="btn btn-sm btn-${color === 'red' ? 'danger' : color === 'emerald' ? 'emerald' : 'primary'}"><i class="ti ${icon}"></i> ${label}</button>`
    ).join('');

    abrirModal('md-despacho');
}

async function cambiarEstado(accion) {
    if (!dspActual) return;
    if (accion === 'ANULAR') {
        const conf = await Swal.fire({ title: '¿Anular despacho?', text: 'Los pedidos quedarán libres para otro despacho.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, anular', cancelButtonText: 'Cancelar' });
        if (!conf.isConfirmed) return;
    }
    const d = await apiPost(BASE + '/api/tms/despachos/estado', { id: dspActual.id, accion });
    if (d.res) { toastOk('Estado actualizado.'); tblDespachos.ajax.reload(null, false); verDespacho(dspActual.id); }
    else toastErr(d.msg || 'Error.');
}

async function entregar(id, estado) {
    let motivo = null;
    if (estado === 'RECHAZADO') {
        const { value, isConfirmed } = await Swal.fire({ title: 'Motivo del rechazo', input: 'text', inputPlaceholder: 'Escribe el motivo...', showCancelButton: true, confirmButtonText: 'Registrar' });
        if (!isConfirmed) return;
        motivo = value || null;
    }
    const d = await apiPost(BASE + '/api/tms/despachos/entrega', { id, estado_entrega: estado, motivo_rechazo: motivo });
    if (d.res) { toastOk('Entrega registrada.'); verDespacho(dspActual.id); }
    else toastErr(d.msg || 'Error.');
}
</script>
@endpush

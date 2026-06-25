@extends('layouts.app')
@section('title','Kardex')
@section('page-title','Kardex')
@section('breadcrumb','Inventario / Kardex')

@section('content')
<div x-data="{ tab: 'movimientos' }">

    {{-- Tabs --}}
    <div class="mb-4 flex flex-wrap gap-1 border-b border-gray-200">
        <button @click="tab='movimientos'"
                :class="tab==='movimientos' ? 'border-brand-600 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="-mb-px border-b-2 px-4 py-2 text-xs font-semibold transition-colors">Movimientos</button>
        <button @click="tab='motivos'; window.cargarMotivos && cargarMotivos()"
                :class="tab==='motivos' ? 'border-brand-600 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="-mb-px border-b-2 px-4 py-2 text-xs font-semibold transition-colors">Motivos</button>
    </div>

    {{-- Tab: Movimientos --}}
    <div x-show="tab==='movimientos'">
        <x-table id="tblKardex" title="Movimientos de Inventario">
            <x-slot:thead>
                <x-th>Fecha</x-th>
                <x-th>Almacén</x-th>
                <x-th>Producto</x-th>
                <x-th align="center">Tipo</x-th>
                <x-th>Motivo</x-th>
                <x-th align="center">Cant.</x-th>
                <x-th align="center">Stock ant.</x-th>
                <x-th align="center">Stock nuevo</x-th>
                <x-th>Observación</x-th>
            </x-slot:thead>
        </x-table>
    </div>

    {{-- Tab: Motivos --}}
    <div x-show="tab==='motivos'" x-cloak>
        <x-table id="tblMotivos" title="Motivos de movimiento">
            <x-slot:filters>
                <x-btn color="primary" icon="ti ti-plus" onclick="abrirMotivo()">Nuevo Motivo</x-btn>
            </x-slot:filters>
            <x-slot:thead>
                <x-th>Nombre</x-th>
                <x-th align="center">Tipo</x-th>
                <x-th align="center">Estado</x-th>
                <x-th align="center">Acciones</x-th>
            </x-slot:thead>
        </x-table>
    </div>
</div>

{{-- Modal motivo --}}
<x-modal id="md-motivo" title="Motivo" size="max-w-md">
    <input type="hidden" id="mo-id">
    <div class="space-y-4">
        <x-input-group label="Nombre" :required="true">
            <x-input id="mo-nombre" maxlength="120" placeholder="Ej. Donación, Robo, Garantía…" onkeydown="if(event.key==='Enter')guardarMotivo()" />
        </x-input-group>
        <x-input-group label="Tipo" :required="true">
            <select id="mo-tipo" class="field bg-white">
                <option value="I">Ingreso (suma stock)</option>
                <option value="S">Salida (resta stock)</option>
            </select>
        </x-input-group>
        <div><x-label>Estado</x-label><x-switch id="mo-estado" /></div>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-motivo')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarMotivo()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tablaMot = null;

/* ════════ MOVIMIENTOS ════════ */
$(function () {
    initDataTable('#tblKardex', {
        ajax: {
            url: `${BASE}/api/movimientos`, dataSrc: '',
            beforeSend: () => $('#tblKardex-loading').removeClass('hidden'),
            complete:   () => $('#tblKardex-loading').addClass('hidden'),
        },
        columns: [
            { data: 'fecha', render: v => v ? new Date(v.replace(' ', 'T')).toLocaleString('es-PE', {dateStyle:'short', timeStyle:'short'}) : '-' },
            { data: 'almacen_nombre', defaultContent: '-' },
            { data: 'producto', defaultContent: '-', responsivePriority: 1 },
            { data: 'tipo', className: 'text-center', orderable: false, searchable: false,
              render: v => v === 'I'
                  ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Ingreso</span>'
                  : '<span class="inline-block rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Salida</span>' },
            { data: 'motivo', defaultContent: '-' },
            { data: 'cantidad', className: 'text-center font-bold' },
            { data: 'stock_anterior', className: 'text-center text-gray-500' },
            { data: 'stock_nuevo', className: 'text-center font-semibold' },
            { data: 'observacion', defaultContent: '-', orderable: false },
        ],
        order: [[0, 'desc']],
    });
});

/* ════════ MOTIVOS (CRUD) ════════ */
window.cargarMotivos = function () {
    if (tablaMot) { tablaMot.ajax.reload(null, false); return; }
    tablaMot = initDataTable('#tblMotivos', {
        ajax: {
            url: `${BASE}/api/motivos`, dataSrc: '',
            beforeSend: () => $('#tblMotivos-loading').removeClass('hidden'),
            complete:   () => $('#tblMotivos-loading').addClass('hidden'),
        },
        columns: [
            { data: 'nombre', responsivePriority: 1 },
            { data: 'tipo', className: 'text-center', orderable: false, searchable: false,
              render: v => v === 'I'
                  ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Ingreso</span>'
                  : '<span class="inline-block rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Salida</span>' },
            { data: 'estado', className: 'text-center', orderable: false, searchable: false,
              render: v => v === '1'
                  ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activo</span>'
                  : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactivo</span>' },
            { data: 'id_motivo', orderable: false, searchable: false, className: 'text-center no-colvis',
              render: (id, t, row) => {
                  const sys = parseInt(row.es_sistema) === 1;
                  const edit = `<button onclick='abrirMotivo(${JSON.stringify(row)})' title="Editar" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-pencil text-sm"></i></button>`;
                  const del  = sys
                      ? `<span class="inline-flex h-7 w-7 items-center justify-center text-gray-300" title="Motivo del sistema"><i class="ti ti-lock text-sm"></i></span>`
                      : `<button onclick="borrarMotivo(${id})" title="Eliminar" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-sm"></i></button>`;
                  return `<div class="flex justify-center gap-1">${edit}${del}</div>`;
              } },
        ],
        order: [[0, 'asc']],
    });
};

function abrirMotivo(row = null) {
    g('mo-id').value     = row ? row.id_motivo : '';
    g('mo-nombre').value = row ? (row.nombre || '') : '';
    g('mo-tipo').value   = row ? row.tipo : 'I';
    g('mo-estado').checked = row ? (row.estado === '1') : true;
    abrirModal('md-motivo');
    setTimeout(() => g('mo-nombre').focus(), 100);
}

async function guardarMotivo() {
    const id = g('mo-id').value;
    const nombre = g('mo-nombre').value.trim();
    if (!nombre) { toastWarn('Escribe un nombre.'); return; }
    const payload = { nombre, tipo: g('mo-tipo').value, estado: g('mo-estado').checked ? '1' : '0' };
    if (id) payload.id = id;
    const url = id ? `${BASE}/api/motivos/editar` : `${BASE}/api/motivos`;
    const d = await apiPost(url, payload);
    if (d.res) { toastOk(id ? 'Actualizado.' : 'Guardado.'); cerrarModal('md-motivo'); tablaMot.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error al guardar.');
}

async function borrarMotivo(id) {
    const { isConfirmed } = await Swal.fire({ title: '¿Eliminar motivo?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' });
    if (!isConfirmed) return;
    const d = await apiPost(`${BASE}/api/motivos/borrar`, { id });
    if (d.res) { toastOk('Eliminado.'); tablaMot.ajax.reload(null, false); }
    else Swal.fire({ icon: 'warning', title: 'No se puede eliminar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
}
</script>
@endpush

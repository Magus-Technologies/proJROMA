@extends('layouts.app')
@section('title','Sucursales')
@section('page-title','Sucursales')
@section('breadcrumb','Administración / Sucursales')

@section('content')
<x-table id="tblSucursales" title="Sucursales">
    <x-slot:filters>
        <x-btn color="primary" icon="ti ti-plus" onclick="abrirSuc()">Nueva Sucursal</x-btn>
    </x-slot:filters>
    <x-slot:thead>
        <x-th align="center">Código</x-th>
        <x-th>Nombre</x-th>
        <x-th>Dirección</x-th>
        <x-th align="center">Estado</x-th>
        <x-th align="center">Acciones</x-th>
    </x-slot:thead>
</x-table>

<x-modal id="md-sucursal" title="Sucursal" size="max-w-md">
    <input type="hidden" id="su-id">
    <div class="grid grid-cols-2 gap-4">
        <x-input-group label="Código" :required="true">
            <x-input id="su-cod" type="number" min="1" step="1" placeholder="1" />
        </x-input-group>
        <x-input-group label="Nombre" :required="true">
            <x-input id="su-nombre" maxlength="150" placeholder="Ej. Sucursal Centro" />
        </x-input-group>
        <x-input-group label="Dirección" class="col-span-2">
            <x-input id="su-dir" maxlength="150" placeholder="Opcional" />
        </x-input-group>
        <div class="col-span-2"><x-label>Estado</x-label><x-switch id="su-estado" /></div>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-sucursal')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarSuc()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tablaSuc;

$(function () {
    tablaSuc = initDataTable('#tblSucursales', {
        ajax: {
            url: `${BASE}/api/sucursales`, dataSrc: '',
            beforeSend: () => $('#tblSucursales-loading').removeClass('hidden'),
            complete:   () => $('#tblSucursales-loading').addClass('hidden'),
        },
        columns: [
            { data: 'cod_sucursal', className: 'text-center font-bold' },
            { data: 'nombre', responsivePriority: 1 },
            { data: 'direccion', defaultContent: '-', orderable: false },
            { data: 'estado', className: 'text-center', orderable: false, searchable: false,
              render: v => v === '1'
                  ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activa</span>'
                  : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactiva</span>' },
            { data: 'id_sucursal', orderable: false, searchable: false, className: 'text-center no-colvis',
              render: (id, t, row) => `<div class="flex justify-center gap-1">
                <button onclick='abrirSuc(${JSON.stringify(row)})' class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-pencil text-sm"></i></button>
                <button onclick="borrarSuc(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-sm"></i></button>
              </div>` },
        ],
        order: [[0, 'asc']],
    });
});

function abrirSuc(row = null) {
    g('su-id').value     = row ? row.id_sucursal : '';
    g('su-cod').value    = row ? row.cod_sucursal : '';
    g('su-nombre').value = row ? (row.nombre || '') : '';
    g('su-dir').value    = row ? (row.direccion || '') : '';
    g('su-estado').checked = row ? (row.estado === '1') : true;
    abrirModal('md-sucursal');
    setTimeout(() => g('su-nombre').focus(), 100);
}

async function guardarSuc() {
    const id = g('su-id').value;
    const nombre = g('su-nombre').value.trim();
    const cod = parseInt(g('su-cod').value || 0);
    if (!cod || cod < 1) { toastWarn('Código inválido.'); return; }
    if (!nombre) { toastWarn('Escribe un nombre.'); return; }
    const payload = { cod_sucursal: cod, nombre, direccion: g('su-dir').value.trim(), estado: g('su-estado').checked ? '1' : '0' };
    if (id) payload.id = id;
    const url = id ? `${BASE}/api/sucursales/editar` : `${BASE}/api/sucursales`;
    const d = await apiPost(url, payload);
    if (d.res) { toastOk(id ? 'Actualizada.' : 'Guardada.'); cerrarModal('md-sucursal'); tablaSuc.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error al guardar.');
}

async function borrarSuc(id) {
    const { isConfirmed } = await Swal.fire({ title: '¿Eliminar sucursal?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' });
    if (!isConfirmed) return;
    const d = await apiPost(`${BASE}/api/sucursales/borrar`, { id });
    if (d.res) { toastOk('Eliminada.'); tablaSuc.ajax.reload(null, false); }
    else Swal.fire({ icon: 'warning', title: 'No se puede eliminar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
}
</script>
@endpush

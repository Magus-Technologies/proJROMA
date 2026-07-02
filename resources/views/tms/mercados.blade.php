@extends('layouts.app')
@section('title','Mercados')
@section('page-title','Mercados')
@section('breadcrumb','TMS / Mercados')

@section('content')
<div>
    <x-table id="tblMercados" title="Mercados">
        <x-slot:filters>
            <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalMercado()">Nuevo Mercado</x-btn>
        </x-slot:filters>
        <x-slot:thead>
            <x-th>Nombre</x-th>
            <x-th>Dirección</x-th>
            <x-th>Distrito</x-th>
            <x-th>Teléfono</x-th>
            <x-th align="center">Estado</x-th>
            <x-th align="center">Acciones</x-th>
        </x-slot:thead>
    </x-table>

    <x-modal id="md-mercado" title="Mercado" size="max-w-lg">
        <input type="hidden" id="mk-id">
        <div class="space-y-4">
            <x-input-group label="Nombre" :required="true">
                <x-input id="mk-nombre" maxlength="120" placeholder="Ej: Mercado Caquetá" />
            </x-input-group>
            <x-input-group label="Dirección (específica)" :required="true">
                <x-input id="mk-direccion" maxlength="245" placeholder="Av. / Jr. / Calle, nro, distrito" />
            </x-input-group>
            <x-input-group label="Referencia">
                <x-input id="mk-referencia" maxlength="245" placeholder="Frente a..., portón, etc." />
            </x-input-group>
            <div class="grid grid-cols-2 gap-4">
                <x-input-group label="Distrito">
                    <x-input id="mk-distrito" maxlength="120" placeholder="Distrito" />
                </x-input-group>
                <x-input-group label="Teléfono">
                    <x-input id="mk-telefono" maxlength="20" placeholder="Contacto" />
                </x-input-group>
            </div>
            <div>
                <x-label>Estado</x-label>
                <x-switch id="mk-estado" />
            </div>
        </div>
        <x-slot:footer>
            <x-btn color="ghost" onclick="cerrarModal('md-mercado')">Cancelar</x-btn>
            <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarMercado()">Guardar</x-btn>
        </x-slot:footer>
    </x-modal>
</div>
@endsection

@push('scripts')
<script>
const BASE = BASE_URL;
const g = id => document.getElementById(id);
let tblMercados;

function badgeEstado(v) {
    return v
        ? '<span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activo</span>'
        : '<span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactivo</span>';
}

$(function () {
    tblMercados = initDataTable('#tblMercados', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/tms/mercados', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: [
            { data: 'nombre' },
            { data: 'direccion', defaultContent: '-' },
            { data: 'distrito', defaultContent: '-' },
            { data: 'telefono', defaultContent: '-' },
            { data: 'estado', className: 'text-center', orderable: false, render: badgeEstado },
            { data: 'id', orderable: false, className: 'text-center no-colvis',
              render: id => `<div class="flex justify-center gap-1">
                  <button onclick="editarMercado(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600" title="Editar"><i class="ti ti-pencil text-sm"></i></button>
                  <button onclick="toggleMercado(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600" title="Cambiar Estado"><i class="ti ti-refresh text-sm"></i></button>
              </div>` },
        ],
        order: [[0, 'asc']],
    });
});

function abrirModalMercado() {
    g('mk-id').value = '';
    g('mk-nombre').value = '';
    g('mk-direccion').value = '';
    g('mk-referencia').value = '';
    g('mk-distrito').value = '';
    g('mk-telefono').value = '';
    g('mk-estado').checked = true;
    abrirModal('md-mercado');
}

function editarMercado(id) {
    const row = tblMercados.rows().data().toArray().find(r => String(r.id) === String(id));
    if (!row) return;
    g('mk-id').value = row.id;
    g('mk-nombre').value = row.nombre || '';
    g('mk-direccion').value = row.direccion || '';
    g('mk-referencia').value = row.referencia || '';
    g('mk-distrito').value = row.distrito || '';
    g('mk-telefono').value = row.telefono || '';
    g('mk-estado').checked = !!Number(row.estado);
    abrirModal('md-mercado');
}

async function guardarMercado() {
    const id = g('mk-id').value;
    const nombre = g('mk-nombre').value.trim();
    const direccion = g('mk-direccion').value.trim();
    if (!nombre) { toastWarn('Escribe el nombre.'); return; }
    if (!direccion) { toastWarn('Escribe la dirección.'); return; }

    const payload = {
        nombre, direccion,
        referencia: g('mk-referencia').value.trim() || null,
        distrito: g('mk-distrito').value.trim() || null,
        telefono: g('mk-telefono').value.trim() || null,
        estado: g('mk-estado').checked ? 1 : 0,
    };
    if (id) payload.id = id;

    const d = await apiPost(BASE + '/api/tms/mercados' + (id ? '/editar' : ''), payload);
    if (d.res) { toastOk(id ? 'Mercado actualizado.' : 'Mercado creado.'); cerrarModal('md-mercado'); tblMercados.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}

async function toggleMercado(id) {
    const d = await apiPost(BASE + '/api/tms/mercados/toggle', { id });
    if (d.res) { toastOk(d.estado ? 'Activado.' : 'Desactivado.'); tblMercados.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}
</script>
@endpush

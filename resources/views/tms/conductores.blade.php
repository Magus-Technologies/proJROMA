@extends('layouts.app')
@section('title','Conductores')
@section('page-title','Conductores')
@section('breadcrumb','TMS / Conductores')

@section('content')
<div>
    <x-table id="tblConductores" title="Conductores">
        <x-slot:filters>
            <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalConductor()">Nuevo Conductor</x-btn>
        </x-slot:filters>
        <x-slot:thead>
            <x-th>Nombres</x-th>
            <x-th>Documento</x-th>
            <x-th>Licencia</x-th>
            <x-th align="center">Cat.</x-th>
            <x-th align="center">Lic. vence</x-th>
            <x-th>Teléfono</x-th>
            <x-th align="center">Estado</x-th>
            <x-th align="center">Acciones</x-th>
        </x-slot:thead>
    </x-table>

    <x-modal id="md-conductor" title="Conductor" size="max-w-lg">
        <input type="hidden" id="cd-id">
        <div class="space-y-4">
            <x-input-group label="Nombres" :required="true">
                <x-input id="cd-nombres" maxlength="120" placeholder="Nombres y apellidos" />
            </x-input-group>
            <div class="grid grid-cols-2 gap-4">
                <x-input-group label="Documento (DNI)">
                    <x-input id="cd-documento" maxlength="15" placeholder="DNI" />
                </x-input-group>
                <x-input-group label="Teléfono">
                    <x-input id="cd-telefono" maxlength="20" placeholder="Celular" />
                </x-input-group>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <x-input-group label="Licencia">
                    <x-input id="cd-licencia" maxlength="30" placeholder="N° licencia" />
                </x-input-group>
                <x-input-group label="Categoría">
                    <x-input id="cd-licencia_categoria" maxlength="10" placeholder="A-IIb" />
                </x-input-group>
                <x-input-group label="Vence">
                    <x-input id="cd-licencia_vence" type="date" />
                </x-input-group>
            </div>
            <div>
                <x-label>Estado</x-label>
                <x-switch id="cd-estado" />
            </div>
        </div>
        <x-slot:footer>
            <x-btn color="ghost" onclick="cerrarModal('md-conductor')">Cancelar</x-btn>
            <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarConductor()">Guardar</x-btn>
        </x-slot:footer>
    </x-modal>
</div>
@endsection

@push('scripts')
<script>
const BASE = BASE_URL;
const g = id => document.getElementById(id);
let tblConductores;

function badgeEstado(v) {
    return v
        ? '<span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activo</span>'
        : '<span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactivo</span>';
}

function fmtFecha(v) {
    if (!v) return '-';
    const f = String(v).split('T')[0];
    const venc = new Date(f) < new Date(new Date().toISOString().split('T')[0]);
    return `<span class="${venc ? 'text-red-600 font-bold' : 'text-gray-600'}">${f}</span>`;
}

$(function () {
    tblConductores = initDataTable('#tblConductores', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/tms/conductores', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: [
            { data: 'nombres' },
            { data: 'documento', defaultContent: '-' },
            { data: 'licencia', defaultContent: '-' },
            { data: 'licencia_categoria', className: 'text-center', defaultContent: '-' },
            { data: 'licencia_vence', className: 'text-center', render: fmtFecha },
            { data: 'telefono', defaultContent: '-' },
            { data: 'estado', className: 'text-center', orderable: false, render: badgeEstado },
            { data: 'id', orderable: false, className: 'text-center no-colvis',
              render: id => `<div class="flex justify-center gap-1">
                  <button onclick="editarConductor(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600" title="Editar"><i class="ti ti-pencil text-sm"></i></button>
                  <button onclick="toggleConductor(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600" title="Cambiar Estado"><i class="ti ti-refresh text-sm"></i></button>
              </div>` },
        ],
        order: [[0, 'asc']],
    });
});

const CD_FIELDS = ['nombres','documento','licencia','licencia_categoria','licencia_vence','telefono'];

function abrirModalConductor() {
    g('cd-id').value = '';
    CD_FIELDS.forEach(f => g('cd-' + f).value = '');
    g('cd-estado').checked = true;
    abrirModal('md-conductor');
}

function editarConductor(id) {
    const row = tblConductores.rows().data().toArray().find(r => String(r.id) === String(id));
    if (!row) return;
    g('cd-id').value = row.id;
    CD_FIELDS.forEach(f => {
        let v = row[f];
        if (f === 'licencia_vence' && v) v = String(v).split('T')[0];
        g('cd-' + f).value = (v === null || v === undefined) ? '' : v;
    });
    g('cd-estado').checked = !!Number(row.estado);
    abrirModal('md-conductor');
}

async function guardarConductor() {
    const id = g('cd-id').value;
    const nombres = g('cd-nombres').value.trim();
    if (!nombres) { toastWarn('Escribe los nombres.'); return; }

    const payload = { estado: g('cd-estado').checked ? 1 : 0 };
    CD_FIELDS.forEach(f => { payload[f] = g('cd-' + f).value || null; });
    payload.nombres = nombres;
    if (id) payload.id = id;

    const d = await apiPost(BASE + '/api/tms/conductores' + (id ? '/editar' : ''), payload);
    if (d.res) { toastOk(id ? 'Conductor actualizado.' : 'Conductor creado.'); cerrarModal('md-conductor'); tblConductores.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}

async function toggleConductor(id) {
    const d = await apiPost(BASE + '/api/tms/conductores/toggle', { id });
    if (d.res) { toastOk(d.estado ? 'Activado.' : 'Desactivado.'); tblConductores.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}
</script>
@endpush

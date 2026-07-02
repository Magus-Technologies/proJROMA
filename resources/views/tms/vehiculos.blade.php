@extends('layouts.app')
@section('title','Vehículos')
@section('page-title','Vehículos')
@section('breadcrumb','TMS / Vehículos')

@section('content')
<div>
    <x-table id="tblVehiculos" title="Flota de Vehículos">
        <x-slot:filters>
            <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalVehiculo()">Nuevo Vehículo</x-btn>
        </x-slot:filters>
        <x-slot:thead>
            <x-th>Placa</x-th>
            <x-th>Tipo</x-th>
            <x-th>Marca / Modelo</x-th>
            <x-th align="right">Capacidad (kg)</x-th>
            <x-th align="right">Volumen (m³)</x-th>
            <x-th align="center">SOAT</x-th>
            <x-th align="center">Estado</x-th>
            <x-th align="center">Acciones</x-th>
        </x-slot:thead>
    </x-table>

    <x-modal id="md-vehiculo" title="Vehículo" size="max-w-2xl">
        <input type="hidden" id="vh-id">
        <div class="space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <x-input-group label="Placa" :required="true">
                    <x-input id="vh-placa" maxlength="15" placeholder="ABC-123" />
                </x-input-group>
                <x-input-group label="Tipo" :required="true">
                    <select id="vh-tipo" class="field bg-white">
                        <option value="CAMIONETA">Camioneta</option>
                        <option value="FURGONETA">Furgoneta</option>
                        <option value="CAMION">Camión</option>
                        <option value="MOTO">Moto</option>
                        <option value="OTRO">Otro</option>
                    </select>
                </x-input-group>
                <x-input-group label="Año">
                    <x-input id="vh-anio" type="number" min="1980" max="2100" placeholder="2020" />
                </x-input-group>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-input-group label="Marca">
                    <x-input id="vh-marca" maxlength="60" placeholder="Toyota" />
                </x-input-group>
                <x-input-group label="Modelo">
                    <x-input id="vh-modelo" maxlength="60" placeholder="Hilux" />
                </x-input-group>
            </div>

            <p class="text-xs font-bold text-gray-500 pt-1">Peso</p>
            <div class="grid grid-cols-2 gap-4">
                <x-input-group label="Capacidad de carga (kg)" :required="true">
                    <x-input id="vh-capacidad_kg" type="number" step="0.01" min="0" placeholder="1000" />
                </x-input-group>
                <x-input-group label="Tara / peso vacío (kg)">
                    <x-input id="vh-tara_kg" type="number" step="0.01" min="0" placeholder="Opcional" />
                </x-input-group>
            </div>

            <p class="text-xs font-bold text-gray-500 pt-1">Tamaño de la zona de carga</p>
            <div class="grid grid-cols-4 gap-4">
                <x-input-group label="Largo (m)">
                    <x-input id="vh-largo_m" type="number" step="0.01" min="0" placeholder="0.00" />
                </x-input-group>
                <x-input-group label="Ancho (m)">
                    <x-input id="vh-ancho_m" type="number" step="0.01" min="0" placeholder="0.00" />
                </x-input-group>
                <x-input-group label="Alto (m)">
                    <x-input id="vh-alto_m" type="number" step="0.01" min="0" placeholder="0.00" />
                </x-input-group>
                <x-input-group label="Volumen (m³)">
                    <x-input id="vh-capacidad_m3" type="number" step="0.01" min="0" placeholder="auto" />
                </x-input-group>
            </div>

            <p class="text-xs font-bold text-gray-500 pt-1">Documentos</p>
            <div class="grid grid-cols-2 gap-4">
                <x-input-group label="SOAT vence">
                    <x-input id="vh-soat_vence" type="date" />
                </x-input-group>
                <x-input-group label="Rev. técnica vence">
                    <x-input id="vh-rev_tecnica_vence" type="date" />
                </x-input-group>
            </div>

            <div>
                <x-label>Estado</x-label>
                <x-switch id="vh-estado" />
            </div>
        </div>
        <x-slot:footer>
            <x-btn color="ghost" onclick="cerrarModal('md-vehiculo')">Cancelar</x-btn>
            <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarVehiculo()">Guardar</x-btn>
        </x-slot:footer>
    </x-modal>
</div>
@endsection

@push('scripts')
<script>
const BASE = BASE_URL;
const g = id => document.getElementById(id);
let tblVehiculos;

const TIPO_LABEL = { CAMIONETA: 'Camioneta', FURGONETA: 'Furgoneta', CAMION: 'Camión', MOTO: 'Moto', OTRO: 'Otro' };

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
    tblVehiculos = initDataTable('#tblVehiculos', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/tms/vehiculos', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: [
            { data: 'placa' },
            { data: 'tipo', render: v => TIPO_LABEL[v] || v },
            { data: 'marca', defaultContent: '-', render: (v, t, row) => [row.marca, row.modelo].filter(Boolean).join(' ') || '-' },
            { data: 'capacidad_kg', className: 'text-right font-bold', render: v => parseFloat(v || 0).toFixed(0) + ' kg' },
            { data: 'capacidad_m3', className: 'text-right', render: v => v ? parseFloat(v).toFixed(2) + ' m³' : '-' },
            { data: 'soat_vence', className: 'text-center', render: fmtFecha },
            { data: 'estado', className: 'text-center', orderable: false, render: badgeEstado },
            { data: 'id', orderable: false, className: 'text-center no-colvis',
              render: id => `<div class="flex justify-center gap-1">
                  <button onclick="editarVehiculo(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600" title="Editar"><i class="ti ti-pencil text-sm"></i></button>
                  <button onclick="toggleVehiculo(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600" title="Cambiar Estado"><i class="ti ti-refresh text-sm"></i></button>
              </div>` },
        ],
        order: [[0, 'asc']],
    });

    // Calcular volumen automáticamente desde dimensiones
    ['vh-largo_m', 'vh-ancho_m', 'vh-alto_m'].forEach(id => {
        g(id).addEventListener('input', () => {
            const l = parseFloat(g('vh-largo_m').value || 0);
            const a = parseFloat(g('vh-ancho_m').value || 0);
            const h = parseFloat(g('vh-alto_m').value || 0);
            if (l > 0 && a > 0 && h > 0) g('vh-capacidad_m3').value = (l * a * h).toFixed(2);
        });
    });
});

const VH_FIELDS = ['placa','tipo','marca','modelo','anio','capacidad_kg','tara_kg','largo_m','ancho_m','alto_m','capacidad_m3','soat_vence','rev_tecnica_vence'];

function abrirModalVehiculo() {
    g('vh-id').value = '';
    VH_FIELDS.forEach(f => g('vh-' + f).value = '');
    g('vh-tipo').value = 'CAMIONETA';
    g('vh-estado').checked = true;
    abrirModal('md-vehiculo');
}

function editarVehiculo(id) {
    const row = tblVehiculos.rows().data().toArray().find(r => String(r.id) === String(id));
    if (!row) return;
    g('vh-id').value = row.id;
    VH_FIELDS.forEach(f => {
        let v = row[f];
        if ((f === 'soat_vence' || f === 'rev_tecnica_vence') && v) v = String(v).split('T')[0];
        g('vh-' + f).value = (v === null || v === undefined) ? '' : v;
    });
    g('vh-tipo').value = row.tipo || 'CAMIONETA';
    g('vh-estado').checked = !!Number(row.estado);
    abrirModal('md-vehiculo');
}

async function guardarVehiculo() {
    const id = g('vh-id').value;
    const placa = g('vh-placa').value.trim();
    const capacidad_kg = parseFloat(g('vh-capacidad_kg').value || 0);
    if (!placa) { toastWarn('Escribe la placa.'); return; }
    if (capacidad_kg <= 0) { toastWarn('Ingresa la capacidad de carga (kg).'); return; }

    const payload = { estado: g('vh-estado').checked ? 1 : 0 };
    VH_FIELDS.forEach(f => { payload[f] = g('vh-' + f).value || null; });
    if (id) payload.id = id;

    const d = await apiPost(BASE + '/api/tms/vehiculos' + (id ? '/editar' : ''), payload);
    if (d.res) { toastOk(id ? 'Vehículo actualizado.' : 'Vehículo creado.'); cerrarModal('md-vehiculo'); tblVehiculos.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}

async function toggleVehiculo(id) {
    const d = await apiPost(BASE + '/api/tms/vehiculos/toggle', { id });
    if (d.res) { toastOk(d.estado ? 'Activado.' : 'Desactivado.'); tblVehiculos.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}
</script>
@endpush

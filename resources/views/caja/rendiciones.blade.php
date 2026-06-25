@extends('layouts.app')
@section('title','Caja Chica - Rendiciones')
@section('page-title','Caja Chica')
@section('breadcrumb','Cajas / Caja Chica')

@section('content')
<div x-data="{ idCaja: 0, rend: null, cajasChicas: [], totalGastado: 0, montoFondo: 0 }"
     x-init="cargarCajas($data)">

    <div class="mb-4 flex flex-wrap gap-2 items-center">
        <select x-model="idCaja" @change="cargarRendicion()" class="field bg-white text-xs min-w-[220px]">
            <option value="0">— Selecciona caja chica —</option>
            <template x-for="c in cajasChicas" :key="c.id">
                <option :value="c.id" x-text="c.nombre"></option>
            </template>
        </select>
    </div>

    {{-- Panel de rendición activa --}}
    <template x-if="rend">
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-700">Rendición activa</h3>
                <span class="rounded-full px-3 py-1 text-[10px] font-bold"
                      :class="rend.estado === 'ABIERTA' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700'"
                      x-text="rend.estado === 'ABIERTA' ? 'Abierta' : 'Pendiente de aprobación'"></span>
            </div>
            <div class="grid grid-cols-3 gap-6 mb-4">
                <div class="rounded-xl bg-blue-50 p-4 text-center">
                    <p class="text-[10px] text-blue-500 font-semibold uppercase">Fondo fijo</p>
                    <p class="text-2xl font-bold text-blue-700" x-text="'S/ ' + rend.monto_fondo.toFixed(2)"></p>
                </div>
                <div class="rounded-xl bg-red-50 p-4 text-center">
                    <p class="text-[10px] text-red-500 font-semibold uppercase">Total gastado</p>
                    <p class="text-2xl font-bold text-red-700" x-text="'S/ ' + totalGastado.toFixed(2)"></p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-4 text-center">
                    <p class="text-[10px] text-emerald-500 font-semibold uppercase">Saldo disponible</p>
                    <p class="text-2xl font-bold text-emerald-700" x-text="'S/ ' + (montoFondo - totalGastado).toFixed(2)"></p>
                </div>
            </div>
            <div class="flex gap-2">
                <template x-if="rend.estado === 'ABIERTA'">
                    <x-btn color="primary" icon="ti ti-send" @click="solicitarAprobacion()">Solicitar aprobación</x-btn>
                </template>
                <template x-if="rend.estado === 'PENDIENTE_APROBACION'">
                    <x-btn color="emerald" icon="ti ti-circle-check" @click="aprobarRendicion()">Aprobar rendición</x-btn>
                </template>
            </div>
        </div>
    </template>

    {{-- Tabla de movimientos de egreso de esta caja chica --}}
    <x-table id="tblRend" title="Gastos de Caja Chica">
        <x-slot:thead>
            <x-th>Fecha</x-th>
            <x-th>Descripción</x-th>
            <x-th>Instrumento</x-th>
            <x-th align="right">Monto</x-th>
            <x-th>Usuario</x-th>
        </x-slot:thead>
    </x-table>

    {{-- Historial de rendiciones --}}
    <div class="mt-8">
        <h3 class="text-sm font-bold text-gray-700 mb-3">Historial de rendiciones</h3>
        <x-table id="tblHistorial" title="Rendiciones anteriores">
            <x-slot:thead>
                <x-th>Periodo</x-th>
                <x-th align="right">Fondo</x-th>
                <x-th align="right">Gastado</x-th>
                <x-th align="center">Estado</x-th>
            </x-slot:thead>
        </x-table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tblRend, tblHist;
let alpineRend;

function cargarCajas(alpine) {
    alpineRend = alpine;
    if (!alpine) return;
    apiGet(BASE + '/api/cajas/opciones').then(opts => {
        alpine.cajasChicas = (opts.cajas || []).filter(c => c.tipo === 'CHICA');
    });
}

function cargarRendicion() {
    const alpine = alpineRend;
    if (!alpine || !alpine.idCaja) { if (alpine) alpine.rend = null; return; }
    const cajaId = alpine.idCaja;

    apiGet(BASE + '/api/rendiciones/activa/' + cajaId).then(r => {
        if (r.id) {
            alpine.rend = r;
            alpine.totalGastado = parseFloat(r.total_gastado || 0);
            alpine.montoFondo = parseFloat(r.monto_fondo || 0);
        } else { alpine.rend = null; }
    });

    // Cargar movimientos EGRESO
    if (tblRend) tblRend.destroy();
    tblRend = initDataTable('#tblRend', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/caja-movimientos/' + cajaId + '?categoria=MANUAL&length=50', headers: { 'Accept': 'application/json' } },
        columns: [
            { data: 'fecha', defaultContent: '-' },
            { data: 'descripcion', defaultContent: '-' },
            { data: 'instrumento_tipo', defaultContent: 'EFECTIVO',
              render: v => ({ EFECTIVO: 'Efectivo', CUENTA_BANCARIA: 'Cta bancaria', TARJETA: 'Tarjeta', BILLETERA_DIGITAL: 'Billetera' })[v] || v },
            { data: 'monto', className: 'text-right font-bold', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
            { data: 'usuario', defaultContent: '-', className: 'text-xs' },
        ],
        order: [[0, 'desc']],
    });

    // Historial
    if (tblHist) tblHist.destroy();
    tblHist = initDataTable('#tblHistorial', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/rendiciones/historial/' + cajaId, headers: { 'Accept': 'application/json' } },
        columns: [
            { data: 'periodo_inicio', defaultContent: '-',
              render: (v, t, row) => v + (row.periodo_fin ? ' → ' + row.periodo_fin : '') },
            { data: 'monto_fondo', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
            { data: 'total_gastado', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
            { data: 'estado', className: 'text-center',
              render: v => ({
                ABIERTA: '<span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">Abierta</span>',
                PENDIENTE_APROBACION: '<span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Pendiente</span>',
                APROBADA: '<span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Aprobada</span>',
              }[v] || v) },
        ],
        order: [[0, 'desc']],
    });
}

async function solicitarAprobacion() {
    const alpine = alpineRend;
    if (!alpine?.rend) return;
    const d = await apiPost(BASE + '/api/rendiciones/solicitar', { id: alpine.rend.id });
    if (d.res) { toastOk('Rendición enviada a aprobación.'); cargarRendicion(); }
    else toastErr(d.msg || 'Error.');
}

async function aprobarRendicion() {
    const alpine = alpineRend;
    if (!alpine?.rend) return;
    const conf = await Swal.fire({ title: '¿Aprobar rendición?', text: 'Se repondrá el fondo automáticamente.', icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, aprobar', cancelButtonText: 'Cancelar' });
    if (!conf.isConfirmed) return;
    const d = await apiPost(BASE + '/api/rendiciones/aprobar', { id: alpine.rend.id });
    if (d.res) { toastOk('Rendición aprobada. Fondo repuesto.'); cargarRendicion(); }
    else toastErr(d.msg || 'Error.');
}
</script>
@endpush

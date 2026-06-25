@extends('layouts.app')
@section('title','Registro de Caja')
@section('page-title','Registro de Caja')
@section('breadcrumb','Cajas / Registro')

@section('content')
<div x-data="{ filtroInstr: '' }">
    <div class="mb-4 flex flex-wrap gap-2 items-center justify-between">
        <div class="flex gap-2">
            <x-btn color="emerald" icon="ti ti-arrow-up" onclick="abrirIngreso()">Ingreso</x-btn>
            <x-btn color="red" icon="ti ti-arrow-down" onclick="abrirEgreso()">Egreso</x-btn>
        </div>
        <select x-model="filtroInstr" @change="window.filtrarTabla && window.filtrarTabla(filtroInstr)" class="field bg-white text-xs w-48">
            <option value="">Todos los métodos</option>
            <option value="EFECTIVO">Efectivo</option>
            <option value="CUENTA_BANCARIA">Cuenta bancaria</option>
            <option value="TARJETA">Tarjeta</option>
            <option value="BILLETERA_DIGITAL">Billetera digital</option>
        </select>
    </div>

    <x-table id="tblCaja" title="Registros de Caja">
        <x-slot:thead>
            <x-th>Fecha</x-th>
            <x-th align="center">Tipo</x-th>
            <x-th>Descripción</x-th>
            <x-th>Método de pago</x-th>
            <x-th align="right">Monto</x-th>
        </x-slot:thead>
    </x-table>
</div>

<x-modal id="md-caja" title="Registro de Caja" size="max-w-lg">
    <input type="hidden" id="caja-tipo">
    <div class="space-y-4">
        <x-input-group label="Descripción" :required="true">
            <x-input id="caja-desc" maxlength="245" placeholder="Descripción del movimiento"
                     onkeydown="if(event.key==='Enter')guardarCaja()" />
        </x-input-group>
        <x-input-group label="Monto (S/)" :required="true">
            <x-input id="caja-monto" type="number" step="0.01" min="0" placeholder="0.00" />
        </x-input-group>
        <x-input-group label="Método de pago">
            <div class="flex gap-2">
                <select id="caja-instr-tipo" @change="window.cargarInstrCaja && window.cargarInstrCaja()" class="field bg-white">
                    <option value="">— Selecciona —</option>
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="CUENTA_BANCARIA">Cuenta bancaria</option>
                    <option value="TARJETA">Tarjeta</option>
                    <option value="BILLETERA_DIGITAL">Billetera digital</option>
                </select>
                <select id="caja-instr-id" x-show="$data.filtroInstrVal" class="field bg-white hidden">
                    <option value="">— Selecciona —</option>
                </select>
            </div>
        </x-input-group>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-caja')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarCaja()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tablaCaja;

$(function () {
    tablaCaja = initDataTable('#tblCaja', {
        processing: true, serverSide: true,
        ajax: {
            url: BASE + '/api/caja/registros',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        },
        columns: [
            { data: 'fecha', defaultContent: '-' },
            { data: 'tipo', className: 'text-center', orderable: false, searchable: false,
              render: v => v === 'INGRESO'
                  ? '<span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700"><i class="ti ti-arrow-up"></i> Ingreso</span>'
                  : '<span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700"><i class="ti ti-arrow-down"></i> Egreso</span>' },
            { data: 'descripcion', defaultContent: '-', responsivePriority: 1 },
            { data: 'instrumento_tipo', defaultContent: 'EFECTIVO',
              render: v => ({ EFECTIVO: 'Efectivo', CUENTA_BANCARIA: 'Cuenta bancaria', TARJETA: 'Tarjeta', BILLETERA_DIGITAL: 'Billetera digital' })[v] || v },
            { data: 'monto', className: 'text-right font-bold',
              render: (v, t, row) => (row.tipo === 'INGRESO' ? '' : '-') + 'S/ ' + parseFloat(v || 0).toFixed(2) },
        ],
        order: [[0, 'desc']],
    });
});

window.filtrarTabla = function (val = '') {
    tablaCaja.ajax.url(BASE + '/api/caja/registros' + (val ? '?instrumento=' + val : '')).load();
};

function abrirIngreso() {
    g('caja-tipo').value = 'ingreso';
    g('caja-desc').value = '';
    g('caja-monto').value = '';
    g('caja-instr-tipo').value = '';
    g('caja-instr-id').value = '';
    g('caja-instr-id').classList.add('hidden');
    abrirModal('md-caja');
    setTimeout(() => g('caja-desc').focus(), 100);
}

function abrirEgreso() {
    g('caja-tipo').value = 'egreso';
    g('caja-desc').value = '';
    g('caja-monto').value = '';
    g('caja-instr-tipo').value = '';
    g('caja-instr-id').value = '';
    g('caja-instr-id').classList.add('hidden');
    abrirModal('md-caja');
    setTimeout(() => g('caja-desc').focus(), 100);
}

window.cargarInstrCaja = async function () {
    const tipo = g('caja-instr-tipo').value;
    const selId = g('caja-instr-id');
    selId.innerHTML = '<option value="">— Selecciona —</option>';
    if (!tipo || tipo === 'EFECTIVO') { selId.classList.add('hidden'); return; }
    selId.classList.remove('hidden');
    const endpoint = tipo === 'CUENTA_BANCARIA' ? 'cuentas' : tipo === 'TARJETA' ? 'tarjetas' : 'billeteras';
    const items = await apiGet(`${BASE}/api/pago-instrumento/${endpoint}`);
    items.forEach(it => {
        const id = it.id_cuenta ?? it.id_tarjeta ?? it.id_billetera;
        let label;
        if (it.banco) label = `${it.banco} - ${it.tipo_cuenta ?? it.tipo} ${it.numero_cuenta ?? ('*' + it.ultimos_4)}`;
        else if (it.cuenta_vinculada && it.cuenta_vinculada !== '-') label = `${it.tipo} - ${it.cuenta_vinculada}`;
        else label = `${it.tipo} - ${it.titular}`;
        selId.innerHTML += `<option value="${id}">${label}</option>`;
    });
};

async function guardarCaja() {
    const tipo = g('caja-tipo').value;
    const desc = g('caja-desc').value.trim();
    const monto = parseFloat(g('caja-monto').value || 0);
    if (!desc) { toastWarn('Escribe una descripción.'); return; }
    if (monto <= 0) { toastWarn('Ingresa un monto válido.'); return; }

    const payload = {
        descripcion: desc,
        monto,
        instrumento_tipo: g('caja-instr-tipo').value || null,
        instrumento_id: g('caja-instr-id').value || null,
    };

    const url = tipo === 'ingreso' ? `${BASE}/api/caja/ingreso` : `${BASE}/api/caja/egreso`;
    const d = await apiPost(url, payload);
    if (d.res) { toastOk('Registrado.'); cerrarModal('md-caja'); tablaCaja.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}
</script>
@endpush

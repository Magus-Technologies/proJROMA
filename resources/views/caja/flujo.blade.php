@extends('layouts.app')
@section('title','Caja Chica')
@section('page-title','Caja Chica')
@section('breadcrumb','Cajas / Caja Chica')

@section('content')
<div x-data="{ filtroInstr: '' }">
    <div class="mb-4 flex flex-wrap gap-2 items-center justify-between">
        <div class="flex gap-2">
            <x-btn color="emerald" icon="ti ti-arrow-up" onclick="abrirIngreso()">Ingreso</x-btn>
            <x-btn color="red" icon="ti ti-arrow-down" onclick="abrirEgreso()">Egreso</x-btn>
        </div>
        <select x-model="filtroInstr" @change="window.filtrarTabla && window.filtrarTabla()" class="field bg-white text-xs w-48">
            <option value="">Todos los métodos</option>
            <option value="EFECTIVO">Efectivo</option>
            <option value="CUENTA_BANCARIA">Cuenta bancaria</option>
            <option value="TARJETA">Tarjeta</option>
            <option value="BILLETERA_DIGITAL">Billetera digital</option>
        </select>
    </div>

    <x-table id="tblFlujo" title="Flujo de Caja Chica">
        <x-slot:thead>
            <x-th>Fecha</x-th>
            <x-th align="center">Tipo</x-th>
            <x-th>Descripción</x-th>
            <x-th>Método de pago</x-th>
            <x-th align="right">Monto</x-th>
        </x-slot:thead>
    </x-table>
</div>

<x-modal id="md-flujo" title="Movimiento de Caja Chica" size="max-w-lg">
    <input type="hidden" id="flujo-tipo">
    <div class="space-y-4">
        <x-input-group label="Descripción" :required="true">
            <x-input id="flujo-desc" maxlength="245" placeholder="Descripción del movimiento"
                     onkeydown="if(event.key==='Enter')guardarFlujo()" />
        </x-input-group>
        <x-input-group label="Monto (S/)" :required="true">
            <x-input id="flujo-monto" type="number" step="0.01" min="0" placeholder="0.00" />
        </x-input-group>
        <x-input-group label="Método de pago">
            <div class="flex gap-2">
                <select id="flujo-instr-tipo" @change="window.cargarInstrFlujo && window.cargarInstrFlujo()" class="field bg-white">
                    <option value="">— Selecciona —</option>
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="CUENTA_BANCARIA">Cuenta bancaria</option>
                    <option value="TARJETA">Tarjeta</option>
                    <option value="BILLETERA_DIGITAL">Billetera digital</option>
                </select>
                <select id="flujo-instr-id" class="field bg-white hidden">
                    <option value="">— Selecciona —</option>
                </select>
            </div>
        </x-input-group>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-flujo')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarFlujo()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tablaFlujo;

$(function () {
    tablaFlujo = initDataTable('#tblFlujo', {
        processing: true, serverSide: true,
        ajax: {
            url: BASE + '/api/flujo/registros',
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

window.filtrarTabla = function () {
    const val = document.querySelector('[x-data]')?.__x?.$data?.filtroInstr || '';
    tablaFlujo.ajax.url(BASE + '/api/flujo/registros' + (val ? '?instrumento=' + val : '')).load();
};

function abrirIngreso() {
    g('flujo-tipo').value = 'ingreso';
    g('flujo-desc').value = '';
    g('flujo-monto').value = '';
    g('flujo-instr-tipo').value = '';
    g('flujo-instr-id').value = '';
    g('flujo-instr-id').classList.add('hidden');
    abrirModal('md-flujo');
    setTimeout(() => g('flujo-desc').focus(), 100);
}

function abrirEgreso() {
    g('flujo-tipo').value = 'egreso';
    g('flujo-desc').value = '';
    g('flujo-monto').value = '';
    g('flujo-instr-tipo').value = '';
    g('flujo-instr-id').value = '';
    g('flujo-instr-id').classList.add('hidden');
    abrirModal('md-flujo');
    setTimeout(() => g('flujo-desc').focus(), 100);
}

window.cargarInstrFlujo = async function () {
    const tipo = g('flujo-instr-tipo').value;
    const selId = g('flujo-instr-id');
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

async function guardarFlujo() {
    const tipo = g('flujo-tipo').value;
    const desc = g('flujo-desc').value.trim();
    const monto = parseFloat(g('flujo-monto').value || 0);
    if (!desc) { toastWarn('Escribe una descripción.'); return; }
    if (monto <= 0) { toastWarn('Ingresa un monto válido.'); return; }

    const payload = {
        descripcion: desc,
        monto,
        instrumento_tipo: g('flujo-instr-tipo').value || null,
        instrumento_id: g('flujo-instr-id').value || null,
    };

    const url = tipo === 'ingreso' ? `${BASE}/api/flujo/ingreso` : `${BASE}/api/flujo/egreso`;
    const d = await apiPost(url, payload);
    if (d.res) { toastOk('Registrado.'); cerrarModal('md-flujo'); tablaFlujo.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}
</script>
@endpush

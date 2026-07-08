@extends('layouts.app')
@section('title','Métodos de Pago')
@section('page-title','Métodos de Pago')
@section('breadcrumb','Contabilidad / Métodos de Pago')

@section('content')
@php
    $tabs = [
        'bancos'    => 'Bancos',
        'cuentas'   => 'Cuentas Bancarias',
        'tarjetas'  => 'Tarjetas',
        'billeteras'=> 'Billeteras Digitales',
    ];
@endphp

<div x-data="{ tab: 'bancos' }">

    {{-- Tabs --}}
    <div class="mb-4 flex flex-wrap gap-1 border-b border-gray-200">
        @foreach($tabs as $key => $label)
            <button @click="tab='{{ $key }}'; window.cargarTabla && window.cargarTabla('{{ $key }}')"
                    :class="tab==='{{ $key }}' ? 'border-brand-600 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="-mb-px border-b-2 px-4 py-2 text-xs font-semibold transition-colors">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Tab: Bancos --}}
    <div x-show="tab==='bancos'" x-cloak>
        <x-table id="tbl-bancos" title="Bancos">
            <x-slot:filters>
                <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalBanco()">Agregar Banco</x-btn>
            </x-slot:filters>
            <x-slot:thead>
                <x-th>Nombre</x-th>
                <x-th>Código SUNAT</x-th>
                <x-th align="center">Estado</x-th>
                <x-th align="center">Acciones</x-th>
            </x-slot:thead>
        </x-table>
    </div>

    {{-- Tab: Cuentas Bancarias --}}
    <div x-show="tab==='cuentas'" x-cloak>
        <x-table id="tbl-cuentas" title="Cuentas Bancarias">
            <x-slot:filters>
                <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalCuenta()">Agregar Cuenta</x-btn>
            </x-slot:filters>
            <x-slot:thead>
                <x-th>Banco</x-th>
                <x-th>Tipo</x-th>
                <x-th>Número / CCI</x-th>
                <x-th>Moneda</x-th>
                <x-th>Titular</x-th>
                <x-th align="center">Estado</x-th>
                <x-th align="center">Acciones</x-th>
            </x-slot:thead>
        </x-table>
    </div>

    {{-- Tab: Tarjetas --}}
    <div x-show="tab==='tarjetas'" x-cloak>
        <x-table id="tbl-tarjetas" title="Tarjetas">
            <x-slot:filters>
                <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalTarjeta()">Agregar Tarjeta</x-btn>
            </x-slot:filters>
            <x-slot:thead>
                <x-th>Banco</x-th>
                <x-th>Tipo</x-th>
                <x-th>Marca</x-th>
                <x-th>Últ. 4 díg.</x-th>
                <x-th>Titular</x-th>
                <x-th>Vencimiento</x-th>
                <x-th>Cuenta vinculada</x-th>
                <x-th align="center">Estado</x-th>
                <x-th align="center">Acciones</x-th>
            </x-slot:thead>
        </x-table>
    </div>

    {{-- Tab: Billeteras Digitales --}}
    <div x-show="tab==='billeteras'" x-cloak>
        <x-table id="tbl-billeteras" title="Billeteras Digitales">
            <x-slot:filters>
                <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalBilletera()">Agregar Billetera</x-btn>
            </x-slot:filters>
            <x-slot:thead>
                <x-th>Tipo</x-th>
                <x-th>Cuenta vinculada</x-th>
                <x-th>Teléfono</x-th>
                <x-th>Titular</x-th>
                <x-th align="center">Estado</x-th>
                <x-th align="center">Acciones</x-th>
            </x-slot:thead>
        </x-table>
    </div>
</div>

{{-- Modal Banco --}}
<x-modal id="md-banco" title="Banco" size="max-w-md">
    <input type="hidden" id="md-banco-id">
    <div class="space-y-4">
        <x-input-group label="Nombre" :required="true">
            <x-input id="md-banco-nombre" maxlength="100" placeholder="Nombre del banco"
                     onkeydown="if(event.key==='Enter')guardarBanco()" />
        </x-input-group>
        <x-input-group label="Código SUNAT">
            <x-input id="md-banco-codigo" maxlength="10" placeholder="Opcional" />
        </x-input-group>
        <div>
            <x-label>Estado</x-label>
            <x-switch id="md-banco-estado" />
        </div>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-banco')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarBanco()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>

{{-- Modal Cuenta Bancaria --}}
<x-modal id="md-cuenta" title="Cuenta Bancaria" size="max-w-lg">
    <input type="hidden" id="md-cuenta-id">
    <div class="grid grid-cols-2 gap-4">
        <x-input-group label="Banco" :required="true" class="col-span-2">
            <x-select id="md-cuenta-banco" placeholder="— Selecciona banco —" />
        </x-input-group>
        <x-input-group label="Tipo de cuenta" :required="true">
            <select id="md-cuenta-tipo" class="field bg-white">
                <option value="CC">Cuenta Corriente</option>
                <option value="CA">Cuenta de Ahorros</option>
                <option value="CTS">CTS</option>
                <option value="AHORRO">Ahorro</option>
            </select>
        </x-input-group>
        <x-input-group label="Moneda" :required="true">
            <select id="md-cuenta-moneda" class="field bg-white">
                <option value="PEN">Soles (PEN)</option>
                <option value="USD">Dólares (USD)</option>
            </select>
        </x-input-group>
        <x-input-group label="Número de cuenta">
            <x-input id="md-cuenta-numero" maxlength="30" placeholder="Opcional" />
        </x-input-group>
        <x-input-group label="CCI">
            <x-input id="md-cuenta-cci" maxlength="30" placeholder="Opcional" />
        </x-input-group>
        <x-input-group label="Titular" :required="true" class="col-span-2">
            <x-input id="md-cuenta-titular" maxlength="200" placeholder="Nombre del titular" />
        </x-input-group>
        <div class="col-span-2">
            <x-label>Estado</x-label>
            <x-switch id="md-cuenta-estado" />
        </div>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-cuenta')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarCuenta()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>

{{-- Modal Tarjeta --}}
<x-modal id="md-tarjeta" title="Tarjeta" size="max-w-lg">
    <input type="hidden" id="md-tarjeta-id">
    <div class="grid grid-cols-2 gap-4">
        <x-input-group label="Banco" :required="true" class="col-span-2">
            <x-select id="md-tarjeta-banco" placeholder="— Selecciona banco —" />
        </x-input-group>
        <x-input-group label="Tipo" :required="true">
            <select id="md-tarjeta-tipo" class="field bg-white">
                <option value="DEBITO">Débito</option>
                <option value="CREDITO">Crédito</option>
            </select>
        </x-input-group>
        <x-input-group label="Marca" :required="true">
            <select id="md-tarjeta-marca" class="field bg-white">
                <option value="VISA">Visa</option>
                <option value="MASTERCARD">Mastercard</option>
                <option value="AMEX">American Express</option>
                <option value="DINERS">Diners</option>
            </select>
        </x-input-group>
        <x-input-group label="Últimos 4 dígitos" :required="true">
            <x-input id="md-tarjeta-ultimos" maxlength="4" placeholder="1234" />
        </x-input-group>
        <x-input-group label="Vencimiento">
            <x-input id="md-tarjeta-venc" type="date" />
        </x-input-group>
        <x-input-group label="Titular" :required="true" class="col-span-2">
            <x-input id="md-tarjeta-titular" maxlength="200" placeholder="Nombre del titular" />
        </x-input-group>
        <x-input-group label="Cuenta vinculada" class="col-span-2">
            <x-select id="md-tarjeta-cuenta" placeholder="— Opcional —" />
        </x-input-group>
        <div class="col-span-2">
            <x-label>Estado</x-label>
            <x-switch id="md-tarjeta-estado" />
        </div>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-tarjeta')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarTarjeta()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>

{{-- Modal Billetera Digital --}}
<x-modal id="md-billetera" title="Billetera Digital" size="max-w-lg">
    <input type="hidden" id="md-billetera-id">
    <div class="space-y-4">
        {{-- Fila 1: Tipo de billetera + Cuenta vinculada --}}
        <div class="grid grid-cols-2 gap-4">
            <x-input-group label="Tipo de billetera" :required="true">
                <div class="flex gap-1">
                    <select id="md-billetera-tipo" class="field bg-white flex-1">
                        <option value="">— Selecciona —</option>
                    </select>
                    <button type="button" onclick="abrirModalBilleteraTipo()"
                            class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-600 border border-brand-200">
                        <i class="ti ti-plus text-sm"></i>
                    </button>
                </div>
            </x-input-group>
            <x-input-group label="Cuenta vinculada" :required="true">
                <select id="md-billetera-cta" class="field bg-white">
                    <option value="">— Selecciona —</option>
                </select>
            </x-input-group>
        </div>
        {{-- Fila 2: Teléfono + Titular --}}
        <div class="grid grid-cols-2 gap-4">
            <x-input-group label="Número de teléfono" :required="true">
                <x-input id="md-billetera-telefono" type="tel" maxlength="15" placeholder="999 999 999" />
            </x-input-group>
            <x-input-group label="Titular" :required="true">
                <x-input id="md-billetera-titular" maxlength="200" placeholder="Nombre del titular" />
            </x-input-group>
        </div>
        {{-- Fila 3: Estado --}}
        <div>
            <x-label>Estado</x-label>
            <x-switch id="md-billetera-estado" />
        </div>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-billetera')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarBilletera()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>

{{-- Modal rápido para crear nuevo tipo de billetera --}}
<x-modal id="md-btipo" title="Nuevo tipo de billetera" size="max-w-sm">
    <x-input-group label="Nombre" :required="true">
        <x-input id="md-btipo-nombre" maxlength="30" placeholder="Ej: Plin, Yape..."
                 onkeydown="if(event.key==='Enter')guardarBilleteraTipo()" />
    </x-input-group>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-btipo')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarBilleteraTipo()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tablas = {};

$(async function () {
    window.cargarTabla('bancos');
});

window.cargarTabla = async function (tipo) {
    if (tablas[tipo]) { tablas[tipo].ajax.reload(); return; }
    tablas[tipo] = initDataTable('#tbl-' + tipo, {
        processing: true,
        ajax: {
            url: `${BASE}/api/pago-instrumento/${tipo === 'cuentas' ? 'cuentas-dt' : tipo === 'tarjetas' ? 'tarjetas-dt' : tipo === 'billeteras' ? 'billeteras-dt' : 'bancos-dt'}`,
            beforeSend: () => $(`#tbl-${tipo}-loading`).removeClass('hidden'),
            complete:   () => $(`#tbl-${tipo}-loading`).addClass('hidden'),
        },
        columns: tipo === 'bancos' ? [
            { data: 'nombre' },
            { data: 'codigo_sunat', defaultContent: '-' },
            { data: 'estado', className: 'text-center', orderable: false, searchable: false,
              render: v => v === '1' ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activo</span>' : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactivo</span>' },
            { data: 'id_banco', orderable: false, className: 'text-center no-colvis',
              render: id => accionesEditarToggle(tipo, id) },
        ] : tipo === 'cuentas' ? [
            { data: 'banco' },
            { data: 'tipo_cuenta', className: 'text-center' },
            { data: 'numero' },
            { data: 'moneda', className: 'text-center' },
            { data: 'titular' },
            { data: 'estado', className: 'text-center', orderable: false, searchable: false,
              render: v => v === '1' ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activo</span>' : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactivo</span>' },
            { data: 'id_cuenta', orderable: false, className: 'text-center no-colvis',
              render: id => accionesEditarToggle(tipo, id) },
        ] : tipo === 'tarjetas' ? [
            { data: 'banco' },
            { data: 'tipo', className: 'text-center' },
            { data: 'marca', className: 'text-center' },
            { data: 'ultimos_4', className: 'text-center', render: v => '*'+v },
            { data: 'titular' },
            { data: 'fecha_vencimiento', defaultContent: '-' },
            { data: 'cuenta_vinculada', defaultContent: '-' },
            { data: 'estado', className: 'text-center', orderable: false, searchable: false,
              render: v => v === '1' ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activo</span>' : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactivo</span>' },
            { data: 'id_tarjeta', orderable: false, className: 'text-center no-colvis',
              render: id => accionesEditarToggle(tipo, id) },
        ] : [
            { data: 'tipo', className: 'text-center font-semibold' },
            { data: 'cuenta_vinculada', defaultContent: '-' },
            { data: 'telefono', defaultContent: '-' },
            { data: 'titular' },
            { data: 'estado', className: 'text-center', orderable: false, searchable: false,
              render: v => v === '1' ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activo</span>' : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactivo</span>' },
            { data: 'id_billetera', orderable: false, className: 'text-center no-colvis',
              render: id => accionesEditarToggle(tipo, id) },
        ],
        order: [[0, 'asc']],
    });
}

function accionesEditarToggle(tipo, id) {
    const idKey = tipo === 'bancos' ? 'id_banco' : tipo === 'cuentas' ? 'id_cuenta' : tipo === 'tarjetas' ? 'id_tarjeta' : 'id_billetera';
    return `<div class="flex justify-center gap-1">
        <button onclick="editar${capitalizar(tipo)}(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-pencil text-sm"></i></button>
        <button onclick="toggle${capitalizar(tipo)}(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600"><i class="ti ti-refresh text-sm"></i></button>
    </div>`;
}

function capitalizar(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

// ── Helper: llenar select ──────────────────────────────────────────
function fillSel(id, rows, pk, lbl) {
    if (typeof rows === 'function') { cargarBancoOptions(id); return; }
    g(id).innerHTML = `<option value="">${lbl || '— Selecciona —'}</option>` +
        rows.map(r => `<option value="${r[pk]}">${r.nombre || r.label || r.tipo || ''}</option>`).join('');
}

// ── Bancos CRUD ────────────────────────────────────────────────────
async function abrirModalBanco(row) {
    g('md-banco-id').value = row?.id_banco || '';
    g('md-banco-nombre').value = row?.nombre || '';
    g('md-banco-codigo').value = row?.codigo_sunat || '';
    g('md-banco-estado').checked = row ? (row.estado === '1') : true;
    abrirModal('md-banco');
}

function editarBancos(id) {
    const row = tablas.bancos.rows().data().toArray().find(r => String(r.id_banco) === String(id));
    abrirModalBanco(row);
}

async function guardarBanco() {
    const id = g('md-banco-id').value;
    const nombre = g('md-banco-nombre').value.trim();
    if (!nombre) { toastWarn('Escribe el nombre del banco.'); return; }
    const payload = { nombre, codigo_sunat: g('md-banco-codigo').value.trim(), estado: g('md-banco-estado').checked ? '1' : '0' };
    if (id) payload.id = id;
    const d = await apiPost(`${BASE}/api/pago-instrumento/banco${id ? '/editar' : ''}`, payload);
    if (d.res) { toastOk(id ? 'Banco actualizado.' : 'Banco guardado.'); cerrarModal('md-banco'); tablas.bancos.ajax.reload(); }
    else toastErr(d.msg || 'Error al guardar.');
}

async function toggleBancos(id) {
    const d = await apiPost(`${BASE}/api/pago-instrumento/banco/toggle`, { id });
    if (d.res) { toastOk(d.estado === '1' ? 'Activado.' : 'Desactivado.'); tablas.bancos.ajax.reload(); }
    else Swal.fire({ icon: 'warning', title: 'Error', text: d.msg || 'No se pudo cambiar el estado.', confirmButtonColor: '#1d4ed8' });
}

// ── Cuentas Bancarias CRUD ─────────────────────────────────────────
async function abrirModalCuenta(row) {
    g('md-cuenta-id').value = row?.id_cuenta || '';
    g('md-cuenta-tipo').value = row?.tipo_cuenta || 'CC';
    g('md-cuenta-moneda').value = row?.moneda || 'PEN';
    g('md-cuenta-numero').value = row?.numero_cuenta || '';
    g('md-cuenta-cci').value = row?.cci || '';
    g('md-cuenta-titular').value = row?.titular || '';
    g('md-cuenta-estado').checked = row ? (row.estado === '1') : true;
    const bancos = await apiGet(`${BASE}/api/pago-instrumento/bancos`);
    fillSel('md-cuenta-banco', bancos, 'id_banco', '— Selecciona banco —');
    g('md-cuenta-banco').value = row?.id_banco || '';
    abrirModal('md-cuenta');
}

function editarCuentas(id) {
    const row = tablas.cuentas.rows().data().toArray().find(r => String(r.id_cuenta) === String(id));
    abrirModalCuenta(row);
}

async function guardarCuenta() {
    const id = g('md-cuenta-id').value;
    const id_banco = g('md-cuenta-banco').value;
    const titular = g('md-cuenta-titular').value.trim();
    if (!id_banco) { toastWarn('Selecciona un banco.'); return; }
    if (!titular) { toastWarn('Escribe el titular de la cuenta.'); return; }
    const payload = {
        id_banco, tipo_cuenta: g('md-cuenta-tipo').value, moneda: g('md-cuenta-moneda').value,
        numero_cuenta: g('md-cuenta-numero').value.trim(), cci: g('md-cuenta-cci').value.trim(),
        titular, estado: g('md-cuenta-estado').checked ? '1' : '0',
    };
    if (id) payload.id = id;
    const d = await apiPost(`${BASE}/api/pago-instrumento/cuenta${id ? '/editar' : ''}`, payload);
    if (d.res) { toastOk(id ? 'Cuenta actualizada.' : 'Cuenta guardada.'); cerrarModal('md-cuenta'); tablas.cuentas.ajax.reload(); }
    else toastErr(d.msg || 'Error al guardar.');
}

async function toggleCuentas(id) {
    const d = await apiPost(`${BASE}/api/pago-instrumento/cuenta/toggle`, { id });
    if (d.res) { toastOk(d.estado === '1' ? 'Activada.' : 'Desactivada.'); tablas.cuentas.ajax.reload(); }
    else Swal.fire({ icon: 'warning', title: 'Error', text: d.msg || 'No se pudo cambiar el estado.', confirmButtonColor: '#1d4ed8' });
}

// ── Tarjetas CRUD ──────────────────────────────────────────────────
async function abrirModalTarjeta(row) {
    g('md-tarjeta-id').value = row?.id_tarjeta || '';
    g('md-tarjeta-tipo').value = row?.tipo || 'DEBITO';
    g('md-tarjeta-marca').value = row?.marca || 'VISA';
    g('md-tarjeta-ultimos').value = row?.ultimos_4 || '';
    g('md-tarjeta-venc').value = row?.fecha_vencimiento || '';
    g('md-tarjeta-titular').value = row?.titular || '';
    g('md-tarjeta-estado').checked = row ? (row.estado === '1') : true;

    const bancos = await apiGet(`${BASE}/api/pago-instrumento/bancos`);
    fillSel('md-tarjeta-banco', bancos, 'id_banco', '— Selecciona banco —');
    g('md-tarjeta-banco').value = row?.id_banco || '';

    const cuentas = await apiGet(`${BASE}/api/pago-instrumento/cuentas`);
    g('md-tarjeta-cuenta').innerHTML = '<option value="">— Opcional —</option>' +
        cuentas.map(c => `<option value="${c.id_cuenta}">${c.banco} - ${c.tipo_cuenta} ${c.numero_cuenta || ''}</option>`).join('');
    g('md-tarjeta-cuenta').value = row?.id_cuenta_bancaria || '';

    abrirModal('md-tarjeta');
}

function editarTarjetas(id) {
    const row = tablas.tarjetas.rows().data().toArray().find(r => String(r.id_tarjeta) === String(id));
    abrirModalTarjeta(row);
}

async function guardarTarjeta() {
    const id = g('md-tarjeta-id').value;
    const id_banco = g('md-tarjeta-banco').value;
    const ultimos = g('md-tarjeta-ultimos').value.trim();
    const titular = g('md-tarjeta-titular').value.trim();
    if (!id_banco) { toastWarn('Selecciona un banco.'); return; }
    if (!ultimos || ultimos.length !== 4) { toastWarn('Ingresa los últimos 4 dígitos.'); return; }
    if (!titular) { toastWarn('Escribe el titular.'); return; }
    const payload = {
        id_banco, tipo: g('md-tarjeta-tipo').value, marca: g('md-tarjeta-marca').value,
        ultimos_4: ultimos, fecha_vencimiento: g('md-tarjeta-venc').value || null,
        titular, id_cuenta_bancaria: g('md-tarjeta-cuenta').value || null,
        estado: g('md-tarjeta-estado').checked ? '1' : '0',
    };
    if (id) payload.id = id;
    const d = await apiPost(`${BASE}/api/pago-instrumento/tarjeta${id ? '/editar' : ''}`, payload);
    if (d.res) { toastOk(id ? 'Tarjeta actualizada.' : 'Tarjeta guardada.'); cerrarModal('md-tarjeta'); tablas.tarjetas.ajax.reload(); }
    else toastErr(d.msg || 'Error al guardar.');
}

async function toggleTarjetas(id) {
    const d = await apiPost(`${BASE}/api/pago-instrumento/tarjeta/toggle`, { id });
    if (d.res) { toastOk(d.estado === '1' ? 'Activada.' : 'Desactivada.'); tablas.tarjetas.ajax.reload(); }
    else Swal.fire({ icon: 'warning', title: 'Error', text: d.msg || 'No se pudo cambiar el estado.', confirmButtonColor: '#1d4ed8' });
}

// ── Billeteras Digitales CRUD ──────────────────────────────────────
async function abrirModalBilletera(row) {
    g('md-billetera-id').value = row?.id_billetera || '';

    // Cargar tipos
    const tipos = await apiGet(`${BASE}/api/pago-instrumento/billetera-tipos`);
    const selTipo = g('md-billetera-tipo');
    selTipo.innerHTML = '<option value="">— Selecciona —</option>';
    tipos.forEach(t => {
        selTipo.innerHTML += `<option value="${t.id}">${t.nombre}</option>`;
    });

    // Cargar cuentas
    const ctas = await apiGet(`${BASE}/api/pago-instrumento/cuentas`);
    const selCta = g('md-billetera-cta');
    selCta.innerHTML = '<option value="">— Selecciona —</option>';
    ctas.forEach(c => {
        selCta.innerHTML += `<option value="${c.id_cuenta}">${c.banco} - ${c.tipo_cuenta} ${c.numero_cuenta}</option>`;
    });

    if (row) {
        selTipo.value = row.id_billetera_tipo || '';
        selCta.value = row.id_cuenta_bancaria || '';
        g('md-billetera-telefono').value = row.telefono || '';
        g('md-billetera-titular').value = row.titular || '';
        g('md-billetera-estado').checked = row.estado === '1';
    } else {
        selTipo.value = '';
        selCta.value = '';
        g('md-billetera-telefono').value = '';
        g('md-billetera-titular').value = '';
        g('md-billetera-estado').checked = true;
    }
    abrirModal('md-billetera');
}

function editarBilleteras(id) {
    const row = tablas.billeteras.rows().data().toArray().find(r => String(r.id_billetera) === String(id));
    abrirModalBilletera(row);
}

async function guardarBilletera() {
    const id = g('md-billetera-id').value;
    const id_billetera_tipo = g('md-billetera-tipo').value;
    const id_cuenta_bancaria = g('md-billetera-cta').value;
    const telefono = g('md-billetera-telefono').value.trim();
    const titular = g('md-billetera-titular').value.trim();

    if (!id_billetera_tipo) { toastWarn('Selecciona un tipo de billetera.'); return; }
    if (!id_cuenta_bancaria) { toastWarn('Selecciona una cuenta vinculada.'); return; }
    if (!telefono) { toastWarn('Escribe el número de teléfono.'); return; }
    if (!titular) { toastWarn('Escribe el titular.'); return; }

    const payload = { id_billetera_tipo, id_cuenta_bancaria, telefono, titular, estado: g('md-billetera-estado').checked ? '1' : '0' };
    if (id) payload.id = id;

    const url = `${BASE}/api/pago-instrumento/billetera${id ? '/editar' : ''}`;
    const d = await apiPost(url, payload);
    if (d.res) { toastOk(id ? 'Billetera actualizada.' : 'Billetera guardada.'); cerrarModal('md-billetera'); tablas.billeteras.ajax.reload(); }
    else toastErr(d.msg || 'Error al guardar.');
}

async function toggleBilleteras(id) {
    const d = await apiPost(`${BASE}/api/pago-instrumento/billetera/toggle`, { id });
    if (d.res) { toastOk(d.estado === '1' ? 'Activada.' : 'Desactivada.'); tablas.billeteras.ajax.reload(); }
    else Swal.fire({ icon: 'warning', title: 'Error', text: d.msg || 'No se pudo cambiar el estado.', confirmButtonColor: '#1d4ed8' });
}

// ── Billetera Tipos CRUD (modal rápido) ────────────────────────────
async function abrirModalBilleteraTipo() {
    g('md-btipo-nombre').value = '';
    abrirModal('md-btipo');
    setTimeout(() => g('md-btipo-nombre').focus(), 100);
}

async function guardarBilleteraTipo() {
    const nombre = g('md-btipo-nombre').value.trim();
    if (!nombre) { toastWarn('Escribe el nombre del tipo.'); return; }
    const d = await apiPost(`${BASE}/api/pago-instrumento/billetera-tipo`, { nombre });
    if (d.res) {
        toastOk('Tipo guardado.');
        cerrarModal('md-btipo');
        // Recargar select del modal billetera si está abierto
        const tipos = await apiGet(`${BASE}/api/pago-instrumento/billetera-tipos`);
        const sel = g('md-billetera-tipo');
        sel.innerHTML = '<option value="">— Selecciona —</option>';
        tipos.forEach(t => { sel.innerHTML += `<option value="${t.id}">${t.nombre}</option>`; });
        sel.value = d.id || '';
    } else toastErr(d.msg || 'Error.');
}
</script>
@endpush
@endsection

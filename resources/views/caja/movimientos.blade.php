@extends('layouts.app')
@section('title','Movimientos de Caja')
@section('page-title','Movimientos de Caja')
@section('breadcrumb','Cajas / Movimientos')

@section('content')
<div x-data="{ idCaja: {{ $idCaja ?? 0 }}, filtroInstr: '', filtroCat: '', cajas: [], saldo: 0 }"
     x-init="cargarCajas($data)">

    <div class="mb-4 flex flex-wrap gap-2 items-center justify-between">
        <div class="flex gap-2 items-center">
            <select x-model="idCaja" @change="cambiarCaja()" class="field bg-white text-xs min-w-[200px]">
                <option value="0">— Selecciona caja —</option>
                <template x-for="c in cajas" :key="c.id">
                    <option :value="c.id" x-text="c.nombre"></option>
                </template>
            </select>
            <span x-show="idCaja" class="text-xs text-gray-500" x-text="'Saldo: S/ ' + saldo.toFixed(2)"></span>
            <template x-if="idCaja">
                <x-btn color="emerald" icon="ti ti-plus" onclick="abrirMovimiento('INGRESO')">Ingreso</x-btn>
                <x-btn color="red" icon="ti ti-minus" onclick="abrirMovimiento('EGRESO')">Egreso</x-btn>
            </template>
        </div>
        <div class="flex gap-2">
            <select x-model="filtroInstr" @change="recargar()" class="field bg-white text-xs w-40">
                <option value="">Todos los métodos</option>
                <option value="EFECTIVO">Efectivo</option>
                <option value="TRANSFERENCIA">Transferencia</option>
                <option value="BILLETERA_DIGITAL">Billetera digital</option>
            </select>
            <select x-model="filtroCat" @change="recargar()" class="field bg-white text-xs w-40">
                <option value="">Todas las categorías</option>
                <option value="MANUAL">Manual</option>
                <option value="VENTA">Venta</option>
                <option value="COMPRA">Compra</option>
                <option value="APERTURA">Apertura</option>
                <option value="AJUSTE">Ajuste</option>
                <option value="CIERRE">Cierre</option>
                <option value="CUADRE">Cuadre</option>
            </select>
        </div>
    </div>

    <x-table id="tblMov" title="Movimientos">
        <x-slot:thead>
            <x-th>Fecha</x-th>
            <x-th align="center">Tipo</x-th>
            <x-th>Categoría</x-th>
            <x-th>Descripción</x-th>
            <x-th>Instrumento</x-th>
            <x-th align="right">Monto</x-th>
            <x-th align="right">Saldo</x-th>
            <x-th>Usuario</x-th>
            <x-th align="center">Estado</x-th>
            <x-th align="center">Acciones</x-th>
        </x-slot:thead>
    </x-table>
</div>

<x-modal id="md-mov" title="Movimiento de Caja" size="max-w-lg">
    <input type="hidden" id="md-mov-id">
    <input type="hidden" id="md-mov-tipo">
    <div class="space-y-4">
        <x-input-group label="Descripción" :required="true">
            <x-input id="md-mov-desc" maxlength="245" placeholder="Descripción del movimiento" />
        </x-input-group>
        <div class="grid grid-cols-2 gap-4">
            <x-input-group label="Monto (S/)" :required="true">
                <x-input id="md-mov-monto" type="number" step="0.01" min="0" placeholder="0.00" />
            </x-input-group>
            <x-input-group label="Fecha">
                <x-input id="md-mov-fecha" type="date" />
            </x-input-group>
        </div>
        <x-input-group label="Método de pago">
            <select id="md-mov-instr-tipo" @change="window.cargarInstrMov && window.cargarInstrMov()" class="field bg-white">
                <option value="">— Selecciona —</option>
                <option value="EFECTIVO">Efectivo</option>
                <option value="TRANSFERENCIA">Transferencia</option>
                <option value="BILLETERA_DIGITAL">Billetera digital</option>
            </select>
        </x-input-group>
            <select id="md-mov-instr-id" class="field bg-white hidden">
                <option value="">— Selecciona —</option>
            </select>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-mov')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarMovimiento()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE=BASE_URL;
const g = id => document.getElementById(id);
let tblMov, currentCaja = {{ $idCaja ?? 0 }};

let alpineMov;

function cargarCajas(alpine) {
    alpineMov = alpine;
    if (!alpine) return;
    apiGet(BASE + '/api/cajas/opciones').then(opts => {
        alpine.cajas = opts.cajas || [];
        if (currentCaja > 0) { alpine.idCaja = currentCaja; cambiarCaja(); }
    });
}

function cambiarCaja() {
    const alpine = alpineMov;
    if (!alpine) return;
    currentCaja = alpine.idCaja;
    if (!currentCaja) { if (tblMov) tblMov.ajax.url(BASE + '/api/caja-movimientos/0').load(); return; }
    recargar();
}

function recargar() {
    const alpine = alpineMov;
    if (!alpine || !currentCaja) return;
    let url = BASE + '/api/caja-movimientos/' + currentCaja;
    const params = [];
    if (alpine.filtroInstr) params.push('instrumento=' + alpine.filtroInstr);
    if (alpine.filtroCat) params.push('categoria=' + alpine.filtroCat);
    if (params.length) url += '?' + params.join('&');
    if (tblMov) tblMov.ajax.url(url).load();
    apiGet(BASE + '/api/caja-movimientos/' + currentCaja + '?length=1').then(d => {
        if (d.data && d.data.length) alpine.saldo = parseFloat(d.data[0].saldo_posterior || 0);
    });
}

$(function () {
    tblMov = initDataTable('#tblMov', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/caja-movimientos/' + currentCaja, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: [
            { data: 'fecha', defaultContent: '-' },
            { data: 'tipo', className: 'text-center', orderable: false,
              render: v => v === 'INGRESO' ? '<span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700"><i class="ti ti-arrow-up"></i> Ingreso</span>' : '<span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700"><i class="ti ti-arrow-down"></i> Egreso</span>' },
            { data: 'categoria', defaultContent: '-', className: 'text-xs' },
            { data: 'descripcion', defaultContent: '-', responsivePriority: 1 },
            { data: 'instrumento_tipo', defaultContent: 'EFECTIVO',
              render: v => ({ EFECTIVO: 'Efectivo', TRANSFERENCIA: 'Transferencia', BILLETERA_DIGITAL: 'Billetera', CUENTA_BANCARIA: 'Cta bancaria', TARJETA: 'Tarjeta' })[v] || v },
            { data: 'monto', className: 'text-right font-bold',
              render: (v, t, row) => (row.tipo === 'INGRESO' ? '+ ' : '- ') + 'S/ ' + parseFloat(v || 0).toFixed(2) },
            { data: 'saldo_posterior', className: 'text-right text-xs text-gray-500',
              render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
            { data: 'usuario', defaultContent: '-', className: 'text-xs' },
            { data: 'estado', className: 'text-center', orderable: false,
              render: v => v === 'CONFIRMADO' ? '<span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Confirmado</span>' : '<span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Anulado</span>' },
            { data: 'id', orderable: false, className: 'text-center',
              render: (id, t, row) => row.estado === 'CONFIRMADO' ? `<div class="flex justify-center gap-1"><button onclick="editarMovimiento(${id})" class="h-6 w-6 flex items-center justify-center rounded bg-blue-50 hover:bg-blue-100 text-blue-500" title="Editar"><i class="ti ti-pencil text-[11px]"></i></button><button onclick="anularMovimiento(${id})" class="h-6 w-6 flex items-center justify-center rounded bg-red-50 hover:bg-red-100 text-red-500" title="Anular"><i class="ti ti-x text-[11px]"></i></button></div>` : '' },
        ],
        order: [[0, 'desc']],
    });
});

function abrirMovimiento(tipo) {
    g('md-mov-id').value = '';
    g('md-mov-tipo').value = tipo;
    g('md-mov-desc').value = '';
    g('md-mov-monto').value = '';
    g('md-mov-fecha').value = new Date().toISOString().split('T')[0];
    g('md-mov-instr-tipo').value = '';
    g('md-mov-instr-id').value = '';
    g('md-mov-instr-id').classList.add('hidden');
    abrirModal('md-mov');
    setTimeout(() => g('md-mov-desc').focus(), 100);
}

function editarMovimiento(id) {
    const data = tblMov.rows().data().toArray().find(r => String(r.id) === String(id));
    if (!data) return;
    g('md-mov-id').value = data.id;
    g('md-mov-tipo').value = data.tipo;
    g('md-mov-desc').value = data.descripcion || '';
    g('md-mov-monto').value = data.monto;
    g('md-mov-fecha').value = data.fecha;
    g('md-mov-instr-tipo').value = data.instrumento_tipo || '';
    window.cargarInstrMov().then(() => {
        g('md-mov-instr-id').value = data.instrumento_id || '';
    });
    abrirModal('md-mov');
}

window.cargarInstrMov = async function () {
    if (!currentCaja) return;
    const tipo = g('md-mov-instr-tipo').value;
    const selId = g('md-mov-instr-id');
    selId.innerHTML = '<option value="">— Selecciona —</option>';
    if (!tipo || tipo === 'EFECTIVO') { selId.classList.add('hidden'); return; }
    selId.classList.remove('hidden');
    const endpoint = tipo === 'TRANSFERENCIA' ? 'cuentas' : 'billeteras';
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

async function guardarMovimiento() {
    const id = g('md-mov-id').value;
    const tipo = g('md-mov-tipo').value;
    const desc = g('md-mov-desc').value.trim();
    const monto = parseFloat(g('md-mov-monto').value || 0);
    if (!desc) { toastWarn('Escribe una descripción.'); return; }
    if (monto <= 0) { toastWarn('Ingresa un monto válido.'); return; }

    let url, payload;
    if (id) {
        url = BASE + '/api/caja-movimientos/editar';
        payload = { id, descripcion: desc, monto, fecha: g('md-mov-fecha').value || undefined };
    } else {
        url = BASE + '/api/caja-movimientos';
        payload = {
            id_caja: currentCaja, tipo, descripcion: desc, monto,
            fecha: g('md-mov-fecha').value || undefined,
            categoria: 'MANUAL',
            instrumento_tipo: g('md-mov-instr-tipo').value || null,
            instrumento_id: g('md-mov-instr-id').value || null,
        };
    }

    const d = await apiPost(url, payload);
    if (d.res) { toastOk(id ? 'Movimiento actualizado.' : 'Movimiento registrado.'); cerrarModal('md-mov'); recargar(); }
    else toastErr(d.msg || 'Error.');
}

async function anularMovimiento(id) {
    const conf = await Swal.fire({ title: '¿Anular movimiento?', text: 'Se restaurará el saldo anterior.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, anular', cancelButtonText: 'Cancelar' });
    if (!conf.isConfirmed) return;
    const d = await apiPost(BASE + '/api/caja-movimientos/anular', { id });
    if (d.res) { toastOk('Movimiento anulado.'); recargar(); }
    else toastErr(d.msg || 'Error.');
}
</script>
@endpush

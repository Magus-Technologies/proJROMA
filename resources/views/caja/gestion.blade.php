@extends('layouts.app')
@section('title','Gestión de Cajas')
@section('page-title','Gestión de Cajas')
@section('breadcrumb','Cajas / Gestión')

@section('content')
<div x-data="{ tab: 'principales' }" x-init="$watch('tab', val => { setTimeout(() => { if (val==='principales' && tblPrincipales) tblPrincipales.columns.adjust().draw(); if (val==='hijas' && tblHijas) tblHijas.columns.adjust().draw(); }, 100); })">

    {{-- Tabs --}}
    <div class="mb-4 flex gap-1 border-b border-gray-200">
        <button @click="tab='principales'" class="px-4 py-2 text-xs font-bold rounded-t transition"
                :class="tab==='principales' ? 'border-b-2 border-brand-500 text-brand-600 bg-brand-50' : 'text-gray-500 hover:text-gray-700'">
            <i class="ti ti-building-bank mr-1"></i> Cajas Principales
        </button>
        <button @click="tab='hijas'" class="px-4 py-2 text-xs font-bold rounded-t transition"
                :class="tab==='hijas' ? 'border-b-2 border-brand-500 text-brand-600 bg-brand-50' : 'text-gray-500 hover:text-gray-700'">
            <i class="ti ti-building-arch mr-1"></i> Cajas Hijas
        </button>
    </div>

    {{-- Tab: Principales --}}
    <div x-show="tab==='principales'">
        <x-table id="tblPrincipales" title="Cajas Principales">
            <x-slot:filters>
                <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalPrincipal()">Nueva Caja Principal</x-btn>
            </x-slot:filters>
            <x-slot:thead>
                <x-th>Nombre</x-th>
                <x-th>Responsable</x-th>
                <x-th align="right">Saldo Actual</x-th>
                <x-th align="center">Estado</x-th>
                <x-th align="center">Acciones</x-th>
            </x-slot:thead>
        </x-table>
    </div>

    {{-- Tab: Hijas --}}
    <div x-show="tab==='hijas'">
        <x-table id="tblHijas" title="Cajas Hijas">
            <x-slot:filters>
                <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalHija()">Nueva Caja Hija</x-btn>
            </x-slot:filters>
            <x-slot:thead>
                <x-th>Nombre</x-th>
                <x-th>Responsable</x-th>
                <x-th>Caja Padre</x-th>
                <x-th align="right">Saldo Actual</x-th>
                <x-th align="center">Estado</x-th>
                <x-th align="center">Acciones</x-th>
            </x-slot:thead>
        </x-table>
    </div>

    <x-modal id="md-caja" title="Nueva Caja" size="max-w-lg">
        <input type="hidden" id="md-caja-id">
        <input type="hidden" id="md-es-hija" value="0">
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <x-input-group label="Nombre" :required="true">
                    <x-input id="md-caja-nombre" maxlength="100" placeholder="Ej: Caja Principal" />
                </x-input-group>
                <x-input-group label="Responsable">
                    <select id="md-caja-responsable" class="field bg-white">
                        <option value="">— Sin responsable —</option>
                    </select>
                </x-input-group>
            </div>
            <div id="md-caja-padre-wrap" class="hidden">
                <x-input-group label="Depende de la caja" :required="true">
                    <select id="md-caja-padre" class="field bg-white">
                        <option value="">— Selecciona caja principal —</option>
                    </select>
                </x-input-group>
            </div>
            <div>
                <x-label>Estado</x-label>
                <x-switch id="md-caja-estado" />
            </div>
        </div>
        <x-slot:footer>
            <x-btn color="ghost" onclick="cerrarModal('md-caja')">Cancelar</x-btn>
            <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarCaja()">Guardar</x-btn>
        </x-slot:footer>
    </x-modal>

    {{-- Modal asignar métodos de pago --}}
    <style>
        #md-instrumentos .card { box-shadow: none; border: 1px solid #f1f5f9; }
        #md-instrumentos .card-header { display: none; }
        #md-instrumentos .card > div:last-child { padding: .25rem .5rem !important; }
        #md-instrumentos table.dataTable { font-size: 11px; }
        #md-instrumentos table.dataTable thead th { padding: 4px 8px; font-size: 10px; }
        #md-instrumentos table.dataTable tbody td { padding: 4px 8px; }
    </style>
    <x-modal id="md-instrumentos" title="Asignar Métodos de Pago" size="max-w-xl">
        <input type="hidden" id="md-instr-caja-id">
        <div class="space-y-4">
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <x-label>Método de pago</x-label>
                    <select id="md-instr-tipo" class="field bg-white" onchange="onTipoInstrChange()">
                        <option value="">— Selecciona —</option>
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="TRANSFERENCIA">Transferencia</option>
                        <option value="BILLETERA_DIGITAL">Billetera digital</option>
                    </select>
                </div>
                <div id="md-instr-ref-wrap" class="flex-1 hidden">
                    <x-label id="md-instr-ref-label">Cuenta vinculada</x-label>
                    <select id="md-instr-ref" class="field bg-white">
                        <option value="">— Selecciona —</option>
                    </select>
                </div>
                <x-btn color="primary" icon="ti ti-plus" onclick="asignarInstrumento()">Agregar</x-btn>
            </div>
            <x-table id="tblInstr" :search="false">
                <x-slot:thead>
                    <x-th>Método de pago</x-th>
                    <x-th align="center">Acción</x-th>
                </x-slot:thead>
            </x-table>
        </div>
        <x-slot:footer>
            <x-btn color="ghost" onclick="cerrarModalInstrumentos()">Cerrar</x-btn>
        </x-slot:footer>
    </x-modal>
</div>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tblPrincipales, tblHijas, tblInstr, idCajaInstr = 0, dispInstr = {};

function reloadTables() { if (tblPrincipales) tblPrincipales.ajax.reload(null, false); if (tblHijas) tblHijas.ajax.reload(null, false); }

function colDefs(padre) {
    const cols = [
        { data: 'nombre' },
        { data: 'responsable', defaultContent: '-' },
        { data: 'saldo_actual', className: 'text-right font-bold',
          render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
        { data: 'estado', className: 'text-center', orderable: false,
          render: v => v === 'ACTIVA' ? '<span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activa</span>' : '<span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactiva</span>' },
        { data: 'id', orderable: false, className: 'text-center no-colvis',
          render: (id, t, row) => `<div class="flex justify-center gap-1">
              <button onclick="editarCaja(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600" title="Editar"><i class="ti ti-pencil text-sm"></i></button>
              <button onclick="toggleCaja(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600" title="Cambiar Estado"><i class="ti ti-refresh text-sm"></i></button>
              ${row.id_caja_padre ? `<button onclick="abrirInstrumentos(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-600" title="Asignar Instrumentos"><i class="ti ti-credit-card text-sm"></i></button>` : ''}
          </div>` },
    ];
    if (padre) cols.splice(2, 0, { data: 'padre_nombre', defaultContent: '-' });
    return cols;
}

$(function () {
    tblPrincipales = initDataTable('#tblPrincipales', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/cajas?solo_principales=1', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: colDefs(false),
        order: [[0, 'asc']],
    });
    tblHijas = initDataTable('#tblHijas', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/cajas?solo_hijas=1', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: colDefs(true),
        order: [[0, 'asc']],
    });
});

async function abrirModalPrincipal() {
    g('md-es-hija').value = '0';
    g('md-caja-id').value = '';
    g('md-caja-nombre').value = '';
    g('md-caja-responsable').value = '';
    g('md-caja-padre').value = '';
    g('md-caja-estado').checked = true;
    document.getElementById('md-caja-padre-wrap').classList.add('hidden');

    const opts = await apiGet(BASE + '/api/cajas/opciones');
    fillSel('md-caja-responsable', opts.usuarios || [], 'usuario_id', u => u.nombres + ' ' + u.apellidos);
    abrirModal('md-caja');
}

async function abrirModalHija() {
    g('md-es-hija').value = '1';
    g('md-caja-id').value = '';
    g('md-caja-nombre').value = '';
    g('md-caja-responsable').value = '';
    g('md-caja-padre').value = '';
    g('md-caja-estado').checked = true;
    document.getElementById('md-caja-padre-wrap').classList.remove('hidden');

    const opts = await apiGet(BASE + '/api/cajas/opciones');
    fillSel('md-caja-responsable', opts.usuarios || [], 'usuario_id', u => u.nombres + ' ' + u.apellidos);
    fillSel('md-caja-padre', (opts.cajas || []).filter(c => !c.id_caja_padre), 'id', 'nombre');
    abrirModal('md-caja');
}

async function editarCaja(id) {
    const row = (tblPrincipales ? tblPrincipales.rows().data().toArray() : []).concat(tblHijas ? tblHijas.rows().data().toArray() : []).find(r => String(r.id) === String(id));
    if (!row) return;
    g('md-es-hija').value = row.id_caja_padre ? '1' : '0';
    g('md-caja-id').value = row.id;
    g('md-caja-nombre').value = row.nombre;
    g('md-caja-estado').checked = row.estado === 'ACTIVA';

    const esHija = !!row.id_caja_padre;
    document.getElementById('md-caja-padre-wrap').classList.toggle('hidden', !esHija);

    const opts = await apiGet(BASE + '/api/cajas/opciones');
    fillSel('md-caja-responsable', opts.usuarios || [], 'usuario_id', u => u.nombres + ' ' + u.apellidos, row.id_usuario_responsable);
    if (esHija) {
        fillSel('md-caja-padre', (opts.cajas || []).filter(c => !c.id_caja_padre && String(c.id) !== String(row.id)), 'id', 'nombre', row.id_caja_padre);
    }
    abrirModal('md-caja');
}

function fillSel(id, items, valKey, labelFn, selected) {
    const sel = document.getElementById(id);
    sel.innerHTML = '<option value="">— ' + (id.includes('responsable') ? 'Sin responsable' : id.includes('padre') ? 'Selecciona' : 'Selecciona') + ' —</option>';
    items.forEach(it => {
        const v = typeof valKey === 'function' ? valKey(it) : it[valKey];
        const lbl = typeof labelFn === 'function' ? labelFn(it) : it[labelFn || 'nombre'];
        sel.innerHTML += `<option value="${v}"${selected && String(selected) === String(v) ? ' selected' : ''}>${lbl}</option>`;
    });
}

async function guardarCaja() {
    const id = g('md-caja-id').value;
    const nombre = g('md-caja-nombre').value.trim();
    const id_caja_padre = g('md-caja-padre').value || null;
    if (!nombre) { toastWarn('Escribe el nombre.'); return; }
    const payload = { 
        nombre, 
        id_caja_padre, 
        id_usuario_responsable: g('md-caja-responsable').value || null, 
        estado: g('md-caja-estado').checked ? 'ACTIVA' : 'INACTIVA' 
    };
    if (id) { payload.id = id; }
    const d = await apiPost(BASE + '/api/cajas' + (id ? '/editar' : ''), payload);
    if (d.res) { 
        toastOk(id ? 'Caja actualizada.' : 'Caja creada.');
        cerrarModal('md-caja');
        reloadTables();
        if (!id && id_caja_padre) {
            abrirInstrumentos(d.id);
        }
    }
    else toastErr(d.msg || 'Error.');
}

async function toggleCaja(id) {
    const d = await apiPost(BASE + '/api/cajas/toggle', { id });
    if (d.res) { toastOk(d.estado === 'ACTIVA' ? 'Activada.' : 'Desactivada.'); reloadTables(); }
    else Swal.fire({ icon: 'warning', title: 'Error', text: d.msg || 'No se pudo cambiar el estado.' });
}

// ── Instrumentos ───────────────────────────────────────────────────
async function abrirInstrumentos(idCaja) {
    idCajaInstr = idCaja;
    g('md-instr-caja-id').value = idCaja;

    if (tblInstr) tblInstr.destroy();
    tblInstr = initDataTable('#tblInstr', {
        processing: false, serverSide: false,
        searching: false, paging: false, info: false, ordering: false,
        dom: 'rt',
        ajax: { url: BASE + '/api/caja-instrumentos/' + idCaja, headers: { 'Accept': 'application/json' }, dataSrc: 'data' },
        columns: [
            { data: 'instrumento_label', defaultContent: '-' },
            { data: 'id', className: 'text-center w-12',
              render: id => `<button onclick="quitarInstrumento(${id})" class="h-6 w-6 inline-flex items-center justify-center rounded-md bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-xs"></i></button>` },
        ],
    });

    // Cargar métodos de pago disponibles (efectivo, cuentas para transferencia, billeteras)
    dispInstr = await apiGet(BASE + '/api/caja-instrumentos/disponibles/' + idCaja);
    g('md-instr-tipo').value = '';
    onTipoInstrChange();
    abrirModal('md-instrumentos');
}

function onTipoInstrChange() {
    const tipo = g('md-instr-tipo').value;
    const wrap = g('md-instr-ref-wrap');
    const ref = g('md-instr-ref');
    if (tipo === 'TRANSFERENCIA' || tipo === 'BILLETERA_DIGITAL') {
        const items = tipo === 'TRANSFERENCIA' ? (dispInstr.cuentas || []) : (dispInstr.billeteras || []);
        g('md-instr-ref-label').textContent = tipo === 'TRANSFERENCIA' ? 'Cuenta vinculada' : 'Billetera';
        ref.innerHTML = '<option value="">— Selecciona —</option>' +
            items.map(it => `<option value="${it.id}">${it.label}</option>`).join('');
        wrap.classList.remove('hidden');
    } else {
        ref.innerHTML = '<option value="">— Selecciona —</option>';
        wrap.classList.add('hidden');
    }
}

async function asignarInstrumento() {
    const tipo = g('md-instr-tipo').value;
    if (!tipo) { toastWarn('Selecciona un método de pago.'); return; }
    let id = null;
    if (tipo === 'EFECTIVO') {
        if (dispInstr.efectivo_disponible === false) { toastWarn('El efectivo ya está asignado.'); return; }
    } else {
        id = g('md-instr-ref').value;
        if (!id) { toastWarn('Selecciona la cuenta o billetera vinculada.'); return; }
    }
    const d = await apiPost(BASE + '/api/caja-instrumentos/asignar', { id_caja: idCajaInstr, instrumento_tipo: tipo, instrumento_id: id });
    if (d.res) { toastOk('Asignado.'); tblInstr.ajax.reload(null, false); abrirInstrumentos(idCajaInstr); }
    else toastErr(d.msg || 'Error.');
}

async function quitarInstrumento(id) {
    const d = await apiPost(BASE + '/api/caja-instrumentos/quitar', { id });
    if (d.res) { toastOk('Quitado.'); tblInstr.ajax.reload(null, false); }
}

async function cerrarModalInstrumentos() {
    const allData = (tblPrincipales ? tblPrincipales.rows().data().toArray() : []).concat(tblHijas ? tblHijas.rows().data().toArray() : []);
    const row = allData.find(r => String(r.id) === String(idCajaInstr));
    if (row && row.id_caja_padre) {
        const count = tblInstr.page.info().recordsTotal;
        if (count === 0) {
            await Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Esta caja es una caja hija y no tiene métodos de pago asignados. Debe agregar al menos uno.',
                confirmButtonText: 'Entendido'
            });
        }
    }
    cerrarModal('md-instrumentos');
}
</script>
@endpush

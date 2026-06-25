@extends('layouts.app')
@section('title','Gestión de Cajas')
@section('page-title','Gestión de Cajas')
@section('breadcrumb','Cajas / Gestión')

@section('content')
<div x-data="{ cajaPadre: null, tipo: '' }">
    <x-table id="tblCajas" title="Cajas">
        <x-slot:filters>
            <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalCaja()">Nueva Caja</x-btn>
        </x-slot:filters>
        <x-slot:thead>
            <x-th>Nombre</x-th>
            <x-th align="center">Tipo</x-th>
            <x-th>Responsable</x-th>
            <x-th align="right">Saldo Actual</x-th>
            <x-th align="right">Fondo Fijo</x-th>
            <x-th align="center">Estado</x-th>
            <x-th align="center">Acciones</x-th>
        </x-slot:thead>
    </x-table>

    <x-modal id="md-caja" title="Caja" size="max-w-lg">
        <input type="hidden" id="md-caja-id">
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <x-input-group label="Nombre" :required="true">
                    <x-input id="md-caja-nombre" maxlength="100" placeholder="Ej: Caja Principal" />
                </x-input-group>
                <x-input-group label="Tipo" :required="true">
                    <select id="md-caja-tipo" @change="tipo = $el.value; if($el.value!=='CHICA'){cajaPadre=null; document.getElementById('md-caja-padre').value=''; document.getElementById('md-caja-fondo').value='';}" class="field bg-white">
                        <option value="GENERAL">General</option>
                        <option value="CHICA">Caja Chica</option>
                        <option value="VENDEDOR">Vendedor</option>
                    </select>
                </x-input-group>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-input-group label="Responsable">
                    <select id="md-caja-responsable" class="field bg-white">
                        <option value="">— Sin responsable —</option>
                    </select>
                </x-input-group>
                <x-input-group label="Caja padre" x-show="tipo==='CHICA'">
                    <select id="md-caja-padre" class="field bg-white">
                        <option value="">— Selecciona —</option>
                    </select>
                </x-input-group>
            </div>
            <x-input-group label="Fondo fijo (S/)" x-show="tipo==='CHICA'">
                <x-input id="md-caja-fondo" type="number" step="0.01" min="0" placeholder="500.00" />
            </x-input-group>
            <div x-show="tipo==='' || tipo==='GENERAL' || tipo==='VENDEDOR'">
                <x-label>Estado</x-label>
                <x-switch id="md-caja-estado" />
            </div>
        </div>
        <x-slot:footer>
            <x-btn color="ghost" onclick="cerrarModal('md-caja')">Cancelar</x-btn>
            <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarCaja()">Guardar</x-btn>
        </x-slot:footer>
    </x-modal>

    {{-- Modal asignar instrumentos --}}
    <x-modal id="md-instrumentos" title="Asignar Instrumentos" size="max-w-xl">
        <input type="hidden" id="md-instr-caja-id">
        <div class="space-y-3">
            <div class="flex gap-2">
                <select id="md-instr-select" class="field bg-white flex-1">
                    <option value="">— Selecciona instrumento —</option>
                </select>
                <x-btn color="primary" icon="ti ti-plus" onclick="asignarInstrumento()">Agregar</x-btn>
            </div>
            <x-table id="tblInstr" title="Instrumentos asignados">
                <x-slot:thead>
                    <x-th>Instrumento</x-th>
                    <x-th align="center">Acción</x-th>
                </x-slot:thead>
            </x-table>
        </div>
        <x-slot:footer>
            <x-btn color="ghost" onclick="cerrarModal('md-instrumentos')">Cerrar</x-btn>
        </x-slot:footer>
    </x-modal>
</div>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tblCajas, tblInstr, idCajaInstr = 0;

$(function () {
    tblCajas = initDataTable('#tblCajas', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/cajas', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: [
            { data: 'nombre' },
            { data: 'tipo', className: 'text-center',
              render: v => ({ GENERAL: 'General', CHICA: 'Caja Chica', VENDEDOR: 'Vendedor' })[v] || v },
            { data: 'responsable', defaultContent: '-' },
            { data: 'saldo_actual', className: 'text-right font-bold',
              render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
            { data: 'monto_fondo_fijo', className: 'text-right',
              render: v => v ? 'S/ ' + parseFloat(v).toFixed(2) : '-' },
            { data: 'estado', className: 'text-center', orderable: false,
              render: v => v === 'ACTIVA' ? '<span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activa</span>' : '<span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactiva</span>' },
            { data: 'id', orderable: false, className: 'text-center no-colvis',
              render: id => `<div class="flex justify-center gap-1">
                  <button onclick="editarCaja(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-pencil text-sm"></i></button>
                  <button onclick="toggleCaja(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600"><i class="ti ti-refresh text-sm"></i></button>
                  <button onclick="abrirInstrumentos(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-600"><i class="ti ti-credit-card text-sm"></i></button>
              </div>` },
        ],
        order: [[0, 'asc']],
    });
});

async function abrirModalCaja() {
    g('md-caja-id').value = '';
    g('md-caja-nombre').value = '';
    g('md-caja-tipo').value = 'GENERAL';
    g('md-caja-responsable').value = '';
    g('md-caja-padre').value = '';
    g('md-caja-fondo').value = '';
    g('md-caja-estado').checked = true;

    const opts = await apiGet(BASE + '/api/cajas/opciones');
    fillSel('md-caja-responsable', opts.usuarios || [], 'usuario_id', u => u.nombres + ' ' + u.apellidos);
    fillSel('md-caja-padre', (opts.cajas || []).filter(c => c.tipo === 'GENERAL'), 'id', 'nombre');
    abrirModal('md-caja');
}

async function editarCaja(id) {
    const row = tblCajas.rows().data().toArray().find(r => String(r.id) === String(id));
    if (!row) return;
    g('md-caja-id').value = row.id;
    g('md-caja-nombre').value = row.nombre;
    g('md-caja-tipo').value = row.tipo;
    g('md-caja-estado').checked = row.estado === 'ACTIVA';

    const opts = await apiGet(BASE + '/api/cajas/opciones');
    fillSel('md-caja-responsable', opts.usuarios || [], 'usuario_id', u => u.nombres + ' ' + u.apellidos, row.id_usuario_responsable);
    fillSel('md-caja-padre', (opts.cajas || []).filter(c => c.tipo === 'GENERAL'), 'id', 'nombre', row.id_caja_padre);
    document.getElementById('md-caja-fondo').value = row.monto_fondo_fijo || '';
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
    const tipo = g('md-caja-tipo').value;
    if (!nombre) { toastWarn('Escribe el nombre.'); return; }
    const payload = { nombre, tipo, id_usuario_responsable: g('md-caja-responsable').value || null, estado: g('md-caja-estado').checked ? 'ACTIVA' : 'INACTIVA' };
    if (tipo === 'CHICA') {
        payload.id_caja_padre = g('md-caja-padre').value || null;
        payload.monto_fondo_fijo = parseFloat(g('md-caja-fondo').value || 0);
    }
    if (id) { payload.id = id; }
    const d = await apiPost(BASE + '/api/cajas' + (id ? '/editar' : ''), payload);
    if (d.res) { toastOk(id ? 'Caja actualizada.' : 'Caja creada.'); cerrarModal('md-caja'); tblCajas.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}

async function toggleCaja(id) {
    const d = await apiPost(BASE + '/api/cajas/toggle', { id });
    if (d.res) { toastOk(d.estado === 'ACTIVA' ? 'Activada.' : 'Desactivada.'); tblCajas.ajax.reload(null, false); }
    else Swal.fire({ icon: 'warning', title: 'Error', text: d.msg || 'No se pudo cambiar el estado.' });
}

// ── Instrumentos ───────────────────────────────────────────────────
async function abrirInstrumentos(idCaja) {
    idCajaInstr = idCaja;
    g('md-instr-caja-id').value = idCaja;

    if (tblInstr) tblInstr.destroy();
    tblInstr = initDataTable('#tblInstr', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/caja-instrumentos/' + idCaja, headers: { 'Accept': 'application/json' } },
        columns: [
            { data: 'instrumento_label', defaultContent: '-' },
            { data: 'id', orderable: false, className: 'text-center',
              render: id => `<button onclick="quitarInstrumento(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-sm"></i></button>` },
        ],
        order: [[0, 'asc']],
    });

    // Cargar disponibles
    const disp = await apiGet(BASE + '/api/caja-instrumentos/disponibles/' + idCaja);
    const sel = g('md-instr-select');
    sel.innerHTML = '<option value="">— Selecciona instrumento —</option>';
    disp.forEach(d => { sel.innerHTML += `<option value="${d.tipo}|${d.id ?? ''}">${d.label}</option>`; });
    abrirModal('md-instrumentos');
}

async function asignarInstrumento() {
    const val = g('md-instr-select').value;
    if (!val) { toastWarn('Selecciona un instrumento.'); return; }
    const [tipo, id] = val.split('|');
    const d = await apiPost(BASE + '/api/caja-instrumentos/asignar', { id_caja: idCajaInstr, instrumento_tipo: tipo, instrumento_id: id || null });
    if (d.res) { toastOk('Asignado.'); tblInstr.ajax.reload(null, false); abrirInstrumentos(idCajaInstr); }
    else toastErr(d.msg || 'Error.');
}

async function quitarInstrumento(id) {
    const d = await apiPost(BASE + '/api/caja-instrumentos/quitar', { id });
    if (d.res) { toastOk('Quitado.'); tblInstr.ajax.reload(null, false); }
}
</script>
@endpush

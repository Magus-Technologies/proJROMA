@extends('layouts.app')
@section('title','Rutas')
@section('page-title','Rutas de Reparto')
@section('breadcrumb','TMS / Rutas')

@section('content')
<div>
    <x-table id="tblRutas" title="Rutas">
        <x-slot:filters>
            <x-btn color="primary" icon="ti ti-plus" onclick="abrirModalRuta()">Nueva Ruta</x-btn>
        </x-slot:filters>
        <x-slot:thead>
            <x-th>Nombre</x-th>
            <x-th>Descripción</x-th>
            <x-th align="center">Puntos</x-th>
            <x-th align="center">Estado</x-th>
            <x-th align="center">Acciones</x-th>
        </x-slot:thead>
    </x-table>

    {{-- Modal crear/editar ruta --}}
    <x-modal id="md-ruta" title="Ruta" size="max-w-lg">
        <input type="hidden" id="rt-id">
        <div class="space-y-4">
            <x-input-group label="Nombre" :required="true">
                <x-input id="rt-nombre" maxlength="120" placeholder="Ej: Ruta Norte SJL" />
            </x-input-group>
            <x-input-group label="Descripción">
                <x-input id="rt-descripcion" maxlength="245" placeholder="Opcional" />
            </x-input-group>
            <div>
                <x-label>Estado</x-label>
                <x-switch id="rt-estado" />
            </div>
        </div>
        <x-slot:footer>
            <x-btn color="ghost" onclick="cerrarModal('md-ruta')">Cancelar</x-btn>
            <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarRuta()">Guardar</x-btn>
        </x-slot:footer>
    </x-modal>

    {{-- Modal gestionar puntos de la ruta --}}
    <x-modal id="md-puntos" title="Puntos de la ruta" size="max-w-2xl">
        <input type="hidden" id="pt-ruta-id">
        <div class="space-y-4">
            <div class="rounded-xl bg-gray-50 p-3">
                <p class="mb-2 text-xs font-bold text-gray-500">Agregar punto</p>
                <div class="flex flex-wrap items-end gap-2">
                    <div class="w-36">
                        <x-label>Tipo</x-label>
                        <select id="pt-tipo" class="field bg-white" onchange="onTipoPuntoChange()">
                            <option value="MERCADO">Mercado</option>
                            <option value="TIENDA">Tienda (cliente)</option>
                        </select>
                    </div>

                    {{-- Selección de mercado --}}
                    <div id="pt-mercado-wrap" class="flex-1 min-w-[200px]">
                        <x-label>Mercado</x-label>
                        <select id="pt-mercado" class="field bg-white">
                            <option value="">— Selecciona —</option>
                        </select>
                    </div>

                    {{-- Búsqueda de cliente/tienda --}}
                    <div id="pt-cliente-wrap" class="flex-1 min-w-[220px] hidden">
                        <x-label>Tienda (cliente)</x-label>
                        <input id="pt-cliente-q" class="field bg-white" placeholder="Buscar por nombre o documento..." oninput="buscarClientesDebounced()" autocomplete="off" />
                        <select id="pt-cliente" class="field bg-white mt-1">
                            <option value="">— Resultados —</option>
                        </select>
                    </div>

                    <x-btn color="primary" icon="ti ti-plus" onclick="agregarPunto()">Agregar</x-btn>
                </div>
            </div>

            <x-table id="tblPuntos" :search="false">
                <x-slot:thead>
                    <x-th align="center">#</x-th>
                    <x-th align="center">Tipo</x-th>
                    <x-th>Nombre</x-th>
                    <x-th>Dirección</x-th>
                    <x-th align="center">Acción</x-th>
                </x-slot:thead>
            </x-table>
        </div>
        <x-slot:footer>
            <x-btn color="ghost" onclick="cerrarPuntos()">Cerrar</x-btn>
        </x-slot:footer>
    </x-modal>
</div>
@endsection

@push('scripts')
<script>
const BASE = BASE_URL;
const g = id => document.getElementById(id);
let tblRutas, tblPuntos, rutaPuntosId = 0;

function badgeEstado(v) {
    return v
        ? '<span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activa</span>'
        : '<span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Inactiva</span>';
}

$(function () {
    tblRutas = initDataTable('#tblRutas', {
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/tms/rutas', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: [
            { data: 'nombre' },
            { data: 'descripcion', defaultContent: '-' },
            { data: 'puntos', className: 'text-center', orderable: false,
              render: v => `<span class="inline-flex rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-600">${v || 0}</span>` },
            { data: 'estado', className: 'text-center', orderable: false, render: badgeEstado },
            { data: 'id', orderable: false, className: 'text-center no-colvis',
              render: id => `<div class="flex justify-center gap-1">
                  <button onclick="gestionarPuntos(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-600" title="Puntos"><i class="ti ti-map-pin text-sm"></i></button>
                  <button onclick="editarRuta(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600" title="Editar"><i class="ti ti-pencil text-sm"></i></button>
                  <button onclick="toggleRuta(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600" title="Cambiar Estado"><i class="ti ti-refresh text-sm"></i></button>
              </div>` },
        ],
        order: [[0, 'asc']],
    });
});

// ── Ruta CRUD ──────────────────────────────────────────────────────────
function abrirModalRuta() {
    g('rt-id').value = '';
    g('rt-nombre').value = '';
    g('rt-descripcion').value = '';
    g('rt-estado').checked = true;
    abrirModal('md-ruta');
}

function editarRuta(id) {
    const row = tblRutas.rows().data().toArray().find(r => String(r.id) === String(id));
    if (!row) return;
    g('rt-id').value = row.id;
    g('rt-nombre').value = row.nombre || '';
    g('rt-descripcion').value = row.descripcion || '';
    g('rt-estado').checked = !!Number(row.estado);
    abrirModal('md-ruta');
}

async function guardarRuta() {
    const id = g('rt-id').value;
    const nombre = g('rt-nombre').value.trim();
    if (!nombre) { toastWarn('Escribe el nombre.'); return; }
    const payload = { nombre, descripcion: g('rt-descripcion').value.trim() || null, estado: g('rt-estado').checked ? 1 : 0 };
    if (id) payload.id = id;
    const d = await apiPost(BASE + '/api/tms/rutas' + (id ? '/editar' : ''), payload);
    if (d.res) { toastOk(id ? 'Ruta actualizada.' : 'Ruta creada.'); cerrarModal('md-ruta'); tblRutas.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}

async function toggleRuta(id) {
    const d = await apiPost(BASE + '/api/tms/rutas/toggle', { id });
    if (d.res) { toastOk(d.estado ? 'Activada.' : 'Desactivada.'); tblRutas.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}

// ── Puntos de la ruta ──────────────────────────────────────────────────
async function gestionarPuntos(idRuta) {
    rutaPuntosId = idRuta;
    g('pt-ruta-id').value = idRuta;

    // Cargar mercados disponibles
    const mk = await apiGet(BASE + '/api/tms/mercados-opciones');
    const sel = g('pt-mercado');
    sel.innerHTML = '<option value="">— Selecciona —</option>' +
        (mk.data || []).map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');

    g('pt-tipo').value = 'MERCADO';
    g('pt-cliente-q').value = '';
    g('pt-cliente').innerHTML = '<option value="">— Resultados —</option>';
    onTipoPuntoChange();

    if (tblPuntos) tblPuntos.destroy();
    tblPuntos = initDataTable('#tblPuntos', {
        processing: false, serverSide: false,
        searching: false, paging: false, info: false, ordering: false, dom: 'rt',
        ajax: { url: BASE + '/api/tms/rutas/' + idRuta + '/puntos', headers: { 'Accept': 'application/json' }, dataSrc: 'data' },
        columns: [
            { data: 'orden', className: 'text-center w-10' },
            { data: 'tipo', className: 'text-center', render: v => v === 'MERCADO'
                ? '<span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Mercado</span>'
                : '<span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Tienda</span>' },
            { data: 'nombre', defaultContent: '-' },
            { data: 'direccion', defaultContent: '-', className: 'text-xs text-gray-500' },
            { data: 'id', className: 'text-center w-12',
              render: id => `<button onclick="quitarPunto(${id})" class="h-6 w-6 inline-flex items-center justify-center rounded-md bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-xs"></i></button>` },
        ],
    });

    abrirModal('md-puntos');
}

function onTipoPuntoChange() {
    const tipo = g('pt-tipo').value;
    g('pt-mercado-wrap').classList.toggle('hidden', tipo !== 'MERCADO');
    g('pt-cliente-wrap').classList.toggle('hidden', tipo !== 'TIENDA');
}

let _busqTO;
function buscarClientesDebounced() {
    clearTimeout(_busqTO);
    _busqTO = setTimeout(buscarClientes, 350);
}

async function buscarClientes() {
    const q = g('pt-cliente-q').value.trim();
    const d = await apiGet(BASE + '/api/tms/clientes-buscar?q=' + encodeURIComponent(q));
    const sel = g('pt-cliente');
    sel.innerHTML = '<option value="">— Selecciona —</option>' +
        (d.data || []).map(c => `<option value="${c.id_cliente}">${c.datos}${c.documento ? ' (' + c.documento + ')' : ''}</option>`).join('');
}

async function agregarPunto() {
    const tipo = g('pt-tipo').value;
    const payload = { id_ruta: rutaPuntosId, tipo };
    if (tipo === 'MERCADO') {
        const idm = g('pt-mercado').value;
        if (!idm) { toastWarn('Selecciona un mercado.'); return; }
        payload.id_mercado = idm;
    } else {
        const idc = g('pt-cliente').value;
        if (!idc) { toastWarn('Busca y selecciona una tienda.'); return; }
        payload.id_cliente = idc;
    }
    const d = await apiPost(BASE + '/api/tms/rutas/puntos', payload);
    if (d.res) {
        toastOk('Punto agregado.');
        tblPuntos.ajax.reload(null, false);
        tblRutas.ajax.reload(null, false);
        if (tipo === 'MERCADO') g('pt-mercado').value = '';
        else { g('pt-cliente').value = ''; g('pt-cliente-q').value = ''; g('pt-cliente').innerHTML = '<option value="">— Resultados —</option>'; }
    } else toastErr(d.msg || 'Error.');
}

async function quitarPunto(id) {
    const d = await apiPost(BASE + '/api/tms/rutas/puntos/quitar', { id });
    if (d.res) { toastOk('Punto quitado.'); tblPuntos.ajax.reload(null, false); tblRutas.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}

function cerrarPuntos() {
    cerrarModal('md-puntos');
}
</script>
@endpush

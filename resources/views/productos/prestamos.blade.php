@extends('layouts.app')
@section('title','Préstamos de Productos')
@section('page-title','Préstamos de Productos')
@section('breadcrumb','Inventario / Préstamos de Productos')

@section('content')
{{-- Barra de filtros --}}
<div class="mb-4 flex flex-wrap items-end gap-3">
    <div>
        <label class="mb-1 block text-[10px] font-semibold uppercase text-gray-400">Almacén</label>
        <select id="f-almacen" class="rounded-lg border border-gray-200 px-3 py-2 text-xs"></select>
    </div>
    <div>
        <label class="mb-1 block text-[10px] font-semibold uppercase text-gray-400">Desde</label>
        <input id="f-desde" type="date" class="rounded-lg border border-gray-200 px-3 py-2 text-xs">
    </div>
    <div>
        <label class="mb-1 block text-[10px] font-semibold uppercase text-gray-400">Hasta</label>
        <input id="f-hasta" type="date" class="rounded-lg border border-gray-200 px-3 py-2 text-xs">
    </div>
    <x-btn color="primary" icon="ti ti-search" onclick="buscarPrestamos()">Buscar</x-btn>
    <div class="flex-1"></div>
    <x-btn color="emerald" icon="ti ti-plus" onclick="abrirPrestamo()">Nuevo Préstamo</x-btn>
</div>

{{-- Historial (cabecera) --}}
<x-table id="tblPrestamos" title="Historial de préstamos">
    <x-slot:thead>
        <x-th align="center">N°</x-th>
        <x-th>Fecha</x-th>
        <x-th align="center">Tipo</x-th>
        <x-th>Tercero</x-th>
        <x-th>Almacén</x-th>
        <x-th align="center">Ítems</x-th>
        <x-th align="center">Estado</x-th>
        <x-th align="center">Acción</x-th>
    </x-slot:thead>
</x-table>

{{-- Detalle del préstamo seleccionado --}}
<div class="mt-5">
    <x-table id="tblPrestamoDet" title="Detalle del préstamo" :search="false">
        <x-slot:thead>
            <x-th>Código</x-th>
            <x-th>Producto</x-th>
            <x-th align="center">Unidad</x-th>
            <x-th align="center">Cantidad</x-th>
        </x-slot:thead>
    </x-table>
</div>

{{-- Modal Nuevo Préstamo --}}
<x-modal id="md-prestamo" title="Nuevo Préstamo" size="max-w-3xl">
    <div class="mb-4 grid grid-cols-2 gap-4">
        <x-input-group label="Tipo" :required="true">
            <select id="p-tipo" class="field bg-white">
                <option value="P">Presto (sale de mi stock)</option>
                <option value="R">Me prestan (entra a mi stock)</option>
            </select>
        </x-input-group>
        <x-input-group label="Tercero (empresa/proveedor)" :required="true">
            <x-input id="p-tercero" maxlength="150" placeholder="Nombre" />
        </x-input-group>
        <x-input-group label="Almacén" :required="true">
            <select id="p-almacen" onchange="cambiarAlmacenPr()" class="field bg-white"></select>
        </x-input-group>
        <x-input-group label="Observación">
            <x-input id="p-obs" maxlength="200" placeholder="Opcional" />
        </x-input-group>
    </div>

    <div class="mb-2 flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-600">Productos <span class="req-star">*</span></span>
        <x-btn color="primary" size="xs" icon="ti ti-plus" onclick="agregarFilaPr()">Agregar producto</x-btn>
    </div>
    <div class="overflow-x-auto rounded-lg border border-gray-100">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-3 py-2 text-left">Producto</th>
                    <th class="px-3 py-2 text-center w-20">Disp.</th>
                    <th class="px-3 py-2 text-center w-28">Cantidad</th>
                    <th class="px-3 py-2 text-center w-10"></th>
                </tr>
            </thead>
            <tbody id="p-detalle"></tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-prestamo')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarPrestamo()">Registrar</x-btn>
    </x-slot:footer>
</x-modal>

{{-- Modal Devolución (parcial o total) --}}
<x-modal id="md-devolucion" title="Registrar devolución" size="max-w-2xl">
    <input type="hidden" id="dv-id">
    <div class="mb-2 flex justify-end">
        <button onclick="devolverTodo()" class="text-xs font-semibold text-brand-600 hover:underline"><i class="ti ti-checks"></i> Devolver todo lo pendiente</button>
    </div>
    <div class="overflow-x-auto rounded-lg border border-gray-100">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-3 py-2 text-left">Producto</th>
                    <th class="px-3 py-2 text-center w-20">Prestado</th>
                    <th class="px-3 py-2 text-center w-20">Devuelto</th>
                    <th class="px-3 py-2 text-center w-20">Pendiente</th>
                    <th class="px-3 py-2 text-center w-28">Devolver</th>
                </tr>
            </thead>
            <tbody id="dv-detalle"></tbody>
        </table>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-devolucion')">Cancelar</x-btn>
        <x-btn color="emerald" icon="ti ti-arrow-back-up" onclick="confirmarDevolucion()">Devolver</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tablaPr, tablaPrDet = null, prodsAlm = [];

$(async function () {
    const hoy = new Date().toISOString().slice(0, 10);
    g('f-desde').value = hoy; g('f-hasta').value = hoy;
    const alms = await apiGet(`${BASE}/api/almacenes`, { activos: 1 });
    g('f-almacen').innerHTML = '<option value="">Todos los almacenes</option>' +
        alms.map(a => `<option value="${a.codigo ?? a.id_almacen}">${a.nombre}</option>`).join('');

    tablaPr = initDataTable('#tblPrestamos', {
        ajax: { url: urlPrestamos(), dataSrc: '',
                beforeSend: () => $('#tblPrestamos-loading').removeClass('hidden'),
                complete:   () => $('#tblPrestamos-loading').addClass('hidden') },
        columns: [
            { data: 'id_prestamo', className: 'text-center font-bold' },
            { data: 'fecha', render: v => v ? new Date(v.replace(' ', 'T')).toLocaleString('es-PE', {dateStyle:'short', timeStyle:'short'}) : '-' },
            { data: 'tipo', className: 'text-center', orderable: false, searchable: false,
              render: v => v === 'P'
                  ? '<span class="inline-block rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Presto</span>'
                  : '<span class="inline-block rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">Me prestan</span>' },
            { data: 'tercero', defaultContent: '-', responsivePriority: 1 },
            { data: 'almacen_nombre', defaultContent: '-' },
            { data: 'items', className: 'text-center font-bold' },
            { data: 'estado', className: 'text-center', orderable: false, searchable: false,
              render: v => v === 'D'
                  ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Devuelto</span>'
                  : (v === 'X'
                      ? '<span class="inline-block rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Parcial</span>'
                      : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">Pendiente</span>') },
            { data: 'id_prestamo', orderable: false, searchable: false, className: 'text-center no-colvis',
              render: (id, t, row) => row.estado !== 'D'
                  ? `<button onclick="event.stopPropagation(); abrirDevolucion(${id})" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-[11px] font-semibold text-white"><i class="ti ti-arrow-back-up"></i> Devolver</button>`
                  : '<span class="text-[10px] text-gray-400">—</span>' },
        ],
        order: [[0, 'desc']],
        rowCallback: row => { row.style.cursor = 'pointer'; },
    });

    // Seleccionar fila → cargar detalle abajo
    $('#tblPrestamos tbody').on('click', 'tr', function () {
        const data = tablaPr.row(this).data();
        if (!data) return;
        $('#tblPrestamos tbody tr').css('background', '');
        $(this).css('background', '#dbeafe');
        verDetallePr(data.id_prestamo);
    });
});

function urlPrestamos() {
    const p = new URLSearchParams({ desde: g('f-desde').value, hasta: g('f-hasta').value, almacen: g('f-almacen').value });
    return `${BASE}/api/prestamos?${p.toString()}`;
}
function buscarPrestamos() { tablaPr.ajax.url(urlPrestamos()).load(); }

/* ════════ DETALLE (tabla abajo) ════════ */
function verDetallePr(id) {
    const url = `${BASE}/api/prestamos/detalle?id=${id}`;
    const titleEl = document.querySelector('#tblPrestamoDet').closest('.card').querySelector('.card-header__title');
    if (titleEl) titleEl.textContent = `Detalle del préstamo #${id}`;
    if (tablaPrDet) { tablaPrDet.ajax.url(url).load(); return; }
    tablaPrDet = initDataTable('#tblPrestamoDet', {
        ajax: { url, dataSrc: '' },
        columns: [
            { data: 'codigo', defaultContent: '-' },
            { data: 'producto', defaultContent: '-', responsivePriority: 1 },
            { data: 'unidad', defaultContent: '-', className: 'text-center' },
            { data: 'cantidad', className: 'text-center font-bold' },
        ],
        order: [],
    });
}

/* ════════ NUEVO PRÉSTAMO ════════ */
async function abrirPrestamo() {
    g('p-tipo').value = 'P'; g('p-tercero').value = ''; g('p-obs').value = '';
    const alms = await apiGet(`${BASE}/api/almacenes`, { activos: 1 });
    g('p-almacen').innerHTML = alms.map(a => `<option value="${a.codigo ?? a.id_almacen}">${a.nombre}</option>`).join('');
    await cambiarAlmacenPr();
    abrirModal('md-prestamo');
}

async function cambiarAlmacenPr() {
    prodsAlm = await apiGet(`${BASE}/api/movimientos/productos`, { almacen: g('p-almacen').value });
    g('p-detalle').innerHTML = '';
    agregarFilaPr();
}

function optionsProdPr() {
    return '<option value="">— Producto —</option>' +
        prodsAlm.map(p => `<option value="${p.id_producto}" data-stock="${p.cantidad}">${p.descripcion}</option>`).join('');
}

function agregarFilaPr() {
    const tr = document.createElement('tr');
    tr.className = 'border-t border-gray-50';
    tr.innerHTML = `
        <td class="px-3 py-2"><select onchange="onFilaProdPr(this)" class="field bg-white">${optionsProdPr()}</select></td>
        <td class="px-3 py-2 text-center disp text-gray-500">-</td>
        <td class="px-3 py-2 text-center"><input type="number" min="1" step="1" placeholder="0" class="field text-center"></td>
        <td class="px-3 py-2 text-center"><button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700"><i class="ti ti-trash"></i></button></td>`;
    g('p-detalle').appendChild(tr);
}

function onFilaProdPr(sel) {
    const stock = sel.selectedOptions[0]?.dataset.stock ?? '-';
    sel.closest('tr').querySelector('.disp').textContent = stock;
}

async function guardarPrestamo() {
    const tercero = g('p-tercero').value.trim();
    if (!tercero) { toastWarn('Indica el tercero.'); return; }
    const detalles = [];
    g('p-detalle').querySelectorAll('tr').forEach(tr => {
        const id = tr.querySelector('select').value;
        const c  = parseInt(tr.querySelector('input').value || 0);
        if (id && c > 0) detalles.push({ id_producto: id, cantidad: c });
    });
    if (!detalles.length) { toastWarn('Agrega al menos un producto.'); return; }

    const d = await apiPost(`${BASE}/api/prestamos`, {
        tipo: g('p-tipo').value, tercero, almacen: g('p-almacen').value,
        observacion: g('p-obs').value.trim(), detalles,
    });
    if (d.res) { toastOk('Préstamo registrado.'); cerrarModal('md-prestamo'); buscarPrestamos(); }
    else Swal.fire({ icon: 'warning', title: 'No se pudo registrar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
}

async function abrirDevolucion(id) {
    g('dv-id').value = id;
    const lineas = await apiGet(`${BASE}/api/prestamos/lineas-devolucion`, { id });
    g('dv-detalle').innerHTML = lineas.map(l => {
        const pend = parseInt(l.pendiente);
        const inputCell = pend > 0
            ? `<input type="number" min="0" max="${pend}" value="0" data-id="${l.id_producto}" data-max="${pend}" class="field text-center" style="width:90px">`
            : '<span class="text-[10px] text-emerald-600 font-semibold">Completo</span>';
        return `<tr class="border-t border-gray-50">
            <td class="px-3 py-2">${l.producto}</td>
            <td class="px-3 py-2 text-center">${l.prestado}</td>
            <td class="px-3 py-2 text-center text-gray-500">${l.devuelto}</td>
            <td class="px-3 py-2 text-center font-bold ${pend>0?'text-amber-600':'text-gray-400'}">${pend}</td>
            <td class="px-3 py-2 text-center">${inputCell}</td>
        </tr>`;
    }).join('');
    abrirModal('md-devolucion');
}

function devolverTodo() {
    g('dv-detalle').querySelectorAll('input[data-max]').forEach(inp => { inp.value = inp.dataset.max; });
}

async function confirmarDevolucion() {
    const detalles = [];
    g('dv-detalle').querySelectorAll('input[data-id]').forEach(inp => {
        const c = parseInt(inp.value || 0);
        const max = parseInt(inp.dataset.max);
        if (c > 0) detalles.push({ id_producto: inp.dataset.id, cantidad: Math.min(c, max) });
    });
    if (!detalles.length) { toastWarn('Indica cuánto devolver.'); return; }

    const d = await apiPost(`${BASE}/api/prestamos/devolver`, { id: g('dv-id').value, detalles });
    if (d.res) {
        toastOk(d.estado === 'D' ? 'Devolución completa.' : 'Devolución parcial registrada.');
        cerrarModal('md-devolucion');
        buscarPrestamos();
    } else {
        Swal.fire({ icon: 'warning', title: 'No se pudo devolver', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
    }
}
</script>
@endpush

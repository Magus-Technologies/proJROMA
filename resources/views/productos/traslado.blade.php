@extends('layouts.app')
@section('title','Traslado de Stock')
@section('page-title','Traslado de Stock')
@section('breadcrumb','Inventario / Traslado de Stock')

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
    <x-btn color="primary" icon="ti ti-search" onclick="buscarTraslados()">Buscar</x-btn>
    <div class="flex-1"></div>
    <x-btn color="emerald" icon="ti ti-plus" onclick="abrirTraslado()">Nuevo Traslado</x-btn>
</div>

{{-- Historial (cabecera) --}}
<x-table id="tblTraslados" title="Historial de transferencias de stock">
    <x-slot:thead>
        <x-th align="center">N°</x-th>
        <x-th>Fecha</x-th>
        <x-th>Origen</x-th>
        <x-th>Destino</x-th>
        <x-th align="center">Ítems</x-th>
        <x-th>Usuario</x-th>
        <x-th align="center">Estado</x-th>
        <x-th align="center">Detalle</x-th>
    </x-slot:thead>
</x-table>

{{-- Detalle de la transferencia seleccionada --}}
<div class="mt-5">
    <x-table id="tblTrasladoDet" title="Detalle de transferencia" :search="false">
        <x-slot:thead>
            <x-th>Código</x-th>
            <x-th>Producto</x-th>
            <x-th align="center">Unidad</x-th>
            <x-th align="center">Cantidad</x-th>
            <x-th align="center">Stock ant. origen</x-th>
            <x-th align="center">Stock nuevo origen</x-th>
            <x-th align="center">Stock ant. destino</x-th>
            <x-th align="center">Stock nuevo destino</x-th>
        </x-slot:thead>
    </x-table>
</div>

{{-- Modal Nuevo Traslado --}}
<x-modal id="md-traslado" title="Nuevo Traslado" size="max-w-3xl">
    <div class="mb-4 grid grid-cols-2 gap-4">
        <x-input-group label="Almacén origen" :required="true">
            <select id="t-origen" onchange="cambiarOrigen()" class="field bg-white"></select>
        </x-input-group>
        <x-input-group label="Almacén destino" :required="true">
            <select id="t-destino" class="field bg-white"></select>
        </x-input-group>
        <x-input-group label="Observación" class="col-span-2">
            <x-input id="t-obs" maxlength="200" placeholder="Opcional" />
        </x-input-group>
    </div>

    <div class="mb-2 flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-600">Productos a trasladar <span class="req-star">*</span></span>
        <x-btn color="primary" size="xs" icon="ti ti-plus" onclick="agregarFila()">Agregar producto</x-btn>
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
            <tbody id="t-detalle"></tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-traslado')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-arrows-exchange" onclick="guardarTraslado()">Trasladar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE=BASE_URL;
const g = id => document.getElementById(id);
let tablaTr, tablaDet = null, prodsOrigen = [];

$(async function () {
    // filtros
    const hoy = new Date().toISOString().slice(0, 10);
    g('f-desde').value = hoy; g('f-hasta').value = hoy;
    const alms = await apiGet(`${BASE}/api/almacenes`, { activos: 1 });
    g('f-almacen').innerHTML = '<option value="">Todos los almacenes</option>' +
        alms.map(a => `<option value="${a.codigo ?? a.id_almacen}">${a.nombre}</option>`).join('');

    tablaTr = initDataTable('#tblTraslados', {
        ajax: { url: urlTraslados(), dataSrc: '',
                beforeSend: () => $('#tblTraslados-loading').removeClass('hidden'),
                complete:   () => $('#tblTraslados-loading').addClass('hidden') },
        columns: [
            { data: 'id_traslado', className: 'text-center font-bold' },
            { data: 'fecha', render: v => v ? new Date(v.replace(' ', 'T')).toLocaleString('es-PE', {dateStyle:'short', timeStyle:'short'}) : '-' },
            { data: 'origen_nombre', defaultContent: '-', responsivePriority: 1 },
            { data: 'destino_nombre', defaultContent: '-', responsivePriority: 1 },
            { data: 'items', className: 'text-center font-bold' },
            { data: 'usuario', defaultContent: '-' },
            { data: 'estado', className: 'text-center', orderable: false, searchable: false,
              render: () => '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Realizado</span>' },
            { data: 'id_traslado', orderable: false, searchable: false, className: 'text-center no-colvis',
              render: id => `<button onclick="verDetalle(${id})" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 hover:bg-blue-100 px-3 py-1.5 text-[11px] font-semibold text-blue-600"><i class="ti ti-eye"></i> Ver</button>` },
        ],
        order: [[0, 'desc']],
        rowCallback: row => { row.style.cursor = 'pointer'; },
    });

    // Seleccionar una fila → cargar su detalle abajo
    $('#tblTraslados tbody').on('click', 'tr', function () {
        const data = tablaTr.row(this).data();
        if (!data) return;
        $('#tblTraslados tbody tr').css('background', '');
        $(this).css('background', '#dbeafe');
        verDetalle(data.id_traslado);
    });
});

function urlTraslados() {
    const p = new URLSearchParams({ desde: g('f-desde').value, hasta: g('f-hasta').value, almacen: g('f-almacen').value });
    return `${BASE}/api/traslados?${p.toString()}`;
}
function buscarTraslados() { tablaTr.ajax.url(urlTraslados()).load(); }

/* ════════ DETALLE (tabla de abajo) ════════ */
function verDetalle(id) {
    const url = `${BASE}/api/traslados/detalle?id=${id}`;
    const titleEl = document.querySelector('#tblTrasladoDet').closest('.card').querySelector('.card-header__title');
    if (titleEl) titleEl.textContent = `Detalle de transferencia #${id}`;
    if (tablaDet) { tablaDet.ajax.url(url).load(); return; }
    tablaDet = initDataTable('#tblTrasladoDet', {
        ajax: { url, dataSrc: '' },
        columns: [
            { data: 'codigo', defaultContent: '-' },
            { data: 'producto', defaultContent: '-', responsivePriority: 1 },
            { data: 'unidad', defaultContent: '-', className: 'text-center' },
            { data: 'cantidad', className: 'text-center font-bold' },
            { data: 'stock_ant_origen', className: 'text-center text-gray-500' },
            { data: 'stock_nuevo_origen', className: 'text-center font-semibold' },
            { data: 'stock_ant_destino', className: 'text-center text-gray-500' },
            { data: 'stock_nuevo_destino', className: 'text-center font-semibold' },
        ],
        order: [],
    });
}

/* ════════ NUEVO TRASLADO ════════ */
async function abrirTraslado() {
    const alms = await apiGet(`${BASE}/api/almacenes`, { activos: 1 });
    const opts = alms.map(a => `<option value="${a.codigo ?? a.id_almacen}">${a.nombre}</option>`).join('');
    g('t-origen').innerHTML = opts;
    g('t-destino').innerHTML = opts;
    if (alms.length > 1) g('t-destino').selectedIndex = 1;
    g('t-obs').value = '';
    await cambiarOrigen();
    abrirModal('md-traslado');
}

async function cambiarOrigen() {
    prodsOrigen = await apiGet(`${BASE}/api/movimientos/productos`, { almacen: g('t-origen').value });
    g('t-detalle').innerHTML = '';
    agregarFila();
}

function optionsProd() {
    return '<option value="">— Producto —</option>' +
        prodsOrigen.map(p => `<option value="${p.id_producto}" data-stock="${p.cantidad}">${p.descripcion}</option>`).join('');
}

function agregarFila() {
    const tr = document.createElement('tr');
    tr.className = 'border-t border-gray-50';
    tr.innerHTML = `
        <td class="px-3 py-2"><select onchange="onFilaProd(this)" class="field bg-white">${optionsProd()}</select></td>
        <td class="px-3 py-2 text-center disp text-gray-500">-</td>
        <td class="px-3 py-2 text-center"><input type="number" min="1" step="1" placeholder="0" class="field text-center"></td>
        <td class="px-3 py-2 text-center"><button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700"><i class="ti ti-trash"></i></button></td>`;
    g('t-detalle').appendChild(tr);
}

function onFilaProd(sel) {
    const stock = sel.selectedOptions[0]?.dataset.stock ?? '-';
    sel.closest('tr').querySelector('.disp').textContent = stock;
}

async function guardarTraslado() {
    if (g('t-origen').value === g('t-destino').value) { toastWarn('El origen y el destino deben ser distintos.'); return; }
    const detalles = [];
    g('t-detalle').querySelectorAll('tr').forEach(tr => {
        const id = tr.querySelector('select').value;
        const c  = parseInt(tr.querySelector('input').value || 0);
        if (id && c > 0) detalles.push({ id_producto: id, cantidad: c });
    });
    if (!detalles.length) { toastWarn('Agrega al menos un producto con cantidad.'); return; }

    const d = await apiPost(`${BASE}/api/traslados`, {
        almacen_origen:  g('t-origen').value,
        almacen_destino: g('t-destino').value,
        observacion:     g('t-obs').value.trim(),
        detalles,
    });
    if (d.res) { toastOk('Traslado realizado.'); cerrarModal('md-traslado'); buscarTraslados(); }
    else Swal.fire({ icon: 'warning', title: 'No se pudo trasladar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
}
</script>
@endpush

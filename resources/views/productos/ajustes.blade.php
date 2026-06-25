@extends('layouts.app')
@section('title','Ajustes de Inventario')
@section('page-title','Ajustes / Cuadres')
@section('breadcrumb','Inventario / Ajustes')

@section('content')
<div class="mb-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs text-amber-700">
    <i class="ti ti-info-circle"></i> Aquí se registran <strong>ajustes manuales</strong> de stock (cuadres tras conteo físico, Carga Inicial, mermas, etc.). Selecciona productos del catálogo y escribe el stock deseado; el sistema calcula la diferencia automáticamente.
</div>

{{-- Tarjetas informativas --}}
<div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">Ajustes</div>
        <div id="cardTotal" class="mt-1 text-2xl font-bold text-gray-700">0</div>
    </div>
    <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">Ingresos (+)</div>
        <div id="cardIng" class="mt-1 text-2xl font-bold text-emerald-600">0</div>
    </div>
    <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">Salidas (−)</div>
        <div id="cardSal" class="mt-1 text-2xl font-bold text-red-600">0</div>
    </div>
    <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">Unidades netas</div>
        <div id="cardNeto" class="mt-1 text-2xl font-bold text-brand-600">0</div>
    </div>
</div>

<x-table id="tblAjustes" title="Ajustes de inventario">
    <x-slot:filters>
        <x-btn color="emerald" icon="ti ti-plus" onclick="abrirModalAjuste('I')">Ingreso (+)</x-btn>
        <x-btn color="red" icon="ti ti-minus" onclick="abrirModalAjuste('S')">Salida (−)</x-btn>
    </x-slot:filters>
    <x-slot:thead>
        <x-th>Fecha</x-th>
        <x-th>Almacén</x-th>
        <x-th>Producto</x-th>
        <th align="center">Tipo</th>
        <x-th>Motivo</x-th>
        <th align="center">Cant.</th>
        <th align="center">Stock ant.</th>
        <th align="center">Stock nuevo</th>
        <x-th>Observación</x-th>
        <th align="center">Acción</th>
    </x-slot:thead>
</x-table>

{{-- Modal crear ajuste (multi-producto) --}}
<x-modal id="md-ajuste" title="Nuevo Ajuste de Inventario" size="max-w-3xl">
    <input type="hidden" id="aj-tipo">
    <div class="grid grid-cols-2 gap-4">
        <x-input-group label="Almacén" :required="true">
            <select id="aj-almacen" onchange="cargarCatalogo()" class="field bg-white"></select>
        </x-input-group>
        <x-input-group label="Motivo" :required="true">
            <select id="aj-motivo" class="field bg-white"></select>
        </x-input-group>
    </div>

    {{-- Agregar productos --}}
    <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-2">
        <div class="mb-1.5 text-[10px] font-semibold uppercase tracking-wide text-gray-500">
            <i class="ti ti-plus"></i> Agregar producto del catálogo
        </div>
        <div class="flex gap-2">
            <div class="relative flex-1">
                <input id="aj-prod-search" type="text" placeholder="Buscar producto por nombre o código..." class="field bg-white w-full text-xs"
                       oninput="filtrarProductos(this.value)" onfocus="filtrarProductos(this.value)">
                <div id="aj-prod-lista" class="absolute z-50 mt-0.5 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-40 overflow-y-auto hidden"></div>
                <input type="hidden" id="aj-producto">
            </div>
            <x-btn color="primary" icon="ti ti-plus" onclick="agregarProducto()">Agregar</x-btn>
        </div>
    </div>

    {{-- Tabla de productos agregados --}}
    <div class="mt-3">
        <x-table id="tblProductosAjuste" title="Productos del ajuste" :search="false" :loading="false">
            <x-slot:thead>
                <x-th class="w-6">#</x-th>
                <x-th>Producto</x-th>
                <x-th align="right">Stock<br>Actual</x-th>
                <x-th align="right">Nuevo<br>Stock</x-th>
                <x-th align="right">Dif.</x-th>
                <x-th align="center" class="w-8"></x-th>
            </x-slot:thead>
        </x-table>
    </div>

    <div class="mt-4">
        <x-input-group label="Observación (opcional, se aplica a todos los productos)">
            <x-input id="aj-obs" maxlength="255" placeholder="Ej: Carga inicial de inventario" />
        </x-input-group>
    </div>

    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-ajuste')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarAjusteBatch()">Guardar Ajuste</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE=BASE_URL;
const g = id => document.getElementById(id);
let tablaAj, productosCatalogo = [], productosAjuste = [];

$(function () {
    tablaAj = initDataTable('#tblAjustes', {
        ajax: {
            url: `${BASE}/api/movimientos/ajustes`, dataSrc: '',
            beforeSend: () => $('#tblAjustes-loading').removeClass('hidden'),
            complete:   () => $('#tblAjustes-loading').addClass('hidden'),
        },
        columns: [
            { data: 'fecha', render: v => v ? new Date(v.replace(' ', 'T')).toLocaleString('es-PE', {dateStyle:'short', timeStyle:'short'}) : '-' },
            { data: 'almacen_nombre', defaultContent: '-' },
            { data: 'producto', defaultContent: '-', responsivePriority: 1 },
            { data: 'tipo', className: 'text-center', orderable: false, searchable: false,
              render: v => v === 'I'
                  ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Ingreso</span>'
                  : '<span class="inline-block rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Salida</span>' },
            { data: 'motivo', defaultContent: '-' },
            { data: 'cantidad', className: 'text-center font-bold' },
            { data: 'stock_anterior', className: 'text-center text-gray-500' },
            { data: 'stock_nuevo', className: 'text-center font-semibold' },
            { data: 'observacion', defaultContent: '-', orderable: false },
            { data: 'id_movimiento', orderable: false, searchable: false, className: 'text-center no-colvis',
              render: id => `<button onclick="deshacerAjuste(${id})" title="Deshacer ajuste" class="h-7 w-7 flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600"><i class="ti ti-arrow-back-up text-sm"></i></button>` },
        ],
        order: [[0, 'desc']],
        drawCallback: function () {
            const data = this.api().rows({ search: 'applied' }).data().toArray();
            let ing = 0, sal = 0, uIn = 0, uOut = 0;
            data.forEach(r => {
                if (r.tipo === 'I') { ing++; uIn += parseInt(r.cantidad || 0); }
                else { sal++; uOut += parseInt(r.cantidad || 0); }
            });
            g('cardTotal').textContent = data.length;
            g('cardIng').textContent   = ing;
            g('cardSal').textContent   = sal;
            g('cardNeto').textContent  = (uIn - uOut);
        },
    });
});

// Cerrar lista de productos al hacer clic fuera
document.addEventListener('click', e => {
    const cont = document.getElementById('aj-prod-lista');
    if (cont && !e.target.closest('#aj-prod-search') && !e.target.closest('#aj-prod-lista')) cont.classList.add('hidden');
});

async function deshacerAjuste(id) {
    const { isConfirmed } = await Swal.fire({ title: '¿Deshacer este ajuste?', text: 'Se revertirá el stock.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d97706', confirmButtonText: 'Sí, deshacer', cancelButtonText: 'Cancelar' });
    if (!isConfirmed) return;
    const d = await apiPost(`${BASE}/api/movimientos/anular`, { id });
    if (d.res) { toastOk('Ajuste deshecho.'); tablaAj.ajax.reload(null, false); }
    else Swal.fire({ icon: 'warning', title: 'No se pudo deshacer', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
}

// ─────────────────────────────────────────────────────────────────────
// Modal de nuevo ajuste (multi-producto)
// ─────────────────────────────────────────────────────────────────────
async function abrirModalAjuste(tipo) {
    g('aj-tipo').value = tipo;
    const title = document.querySelector('#md-ajuste h4');
    if (title) title.textContent = tipo === 'I' ? 'Ajuste — Ingreso (+)' : 'Ajuste — Salida (−)';

    productosAjuste = [];
    g('aj-obs').value = '';
    renderTabla();

    const [alms, motivos] = await Promise.all([
        apiGet(`${BASE}/api/almacenes`, { activos: 1 }),
        apiGet(`${BASE}/api/movimientos/motivos`, { ajuste: 1 }),
    ]);
    g('aj-almacen').innerHTML = alms.map(a => `<option value="${a.codigo ?? a.id_almacen}">${a.nombre}</option>`).join('');
    g('aj-motivo').innerHTML = '<option value="">— Motivo —</option>' + motivos.map(m => `<option value="${m.id_motivo}">${m.nombre}</option>`).join('');

    // Si hay motivo "Carga inicial", preseleccionarlo
    const cargaInicial = motivos.find(m => m.nombre.toLowerCase().includes('carga inicial') || m.nombre.toLowerCase().includes('carga'));
    if (cargaInicial) g('aj-motivo').value = cargaInicial.id_motivo;

    await cargarCatalogo();
    abrirModal('md-ajuste');
}

async function cargarCatalogo() {
    productosCatalogo = await apiGet(`${BASE}/api/movimientos/productos`, { todos: 1 });
    g('aj-prod-search').value = '';
    g('aj-producto').value = '';
    cerrarLista();
}

function cerrarLista() { g('aj-prod-lista').classList.add('hidden'); }

function filtrarProductos(texto) {
    const lista = g('aj-prod-lista');
    const q = texto.toLowerCase().trim();
    if (!q) { lista.classList.add('hidden'); return; }

    const filtrados = productosCatalogo.filter(p =>
        p.descripcion.toLowerCase().includes(q) ||
        (p.codigo && p.codigo.toLowerCase().includes(q))
    );
    if (filtrados.length === 0) { lista.classList.add('hidden'); return; }

    lista.innerHTML = filtrados.slice(0, 20).map(p =>
        `<div class="cursor-pointer px-3 py-1.5 text-xs hover:bg-brand-50 hover:text-brand-700 border-b border-gray-50 last:border-0"
              onclick="seleccionarProducto(${p.id_producto}, '${p.descripcion.replace(/'/g, "\\'")}')">
            ${p.descripcion}
         </div>`
    ).join('');
    lista.classList.remove('hidden');
}

function seleccionarProducto(id, desc) {
    g('aj-producto').value = id;
    g('aj-prod-search').value = desc;
    cerrarLista();
}

function agregarProducto() {
    const id = parseInt(g('aj-producto').value);
    if (!id) { toastWarn('Busca y selecciona un producto de la lista.'); return; }
    if (productosAjuste.some(p => p.id_producto === id)) { toastWarn('Ese producto ya está en la lista.'); return; }

    const prod = productosCatalogo.find(p => p.id_producto === id);
    if (!prod) return;

    const almacen = g('aj-almacen').value;
    const stockEnAlmacen = productosCatalogo.filter(p => p.id_producto === id && p.almacen === almacen);
    const stockActual = stockEnAlmacen.length > 0 ? parseInt(stockEnAlmacen[0].cantidad) : 0;

    productosAjuste.push({
        id_producto: prod.id_producto,
        descripcion: prod.descripcion,
        stock_actual: stockActual,
        nuevo_stock: stockActual,
    });

    renderTabla();
    g('aj-prod-search').value = '';
    g('aj-producto').value = '';
    cerrarLista();
    g('aj-prod-search').focus();
}

function renderTabla() {
    const tbody = document.querySelector('#tblProductosAjuste tbody');
    if (!tbody) return;
    if (productosAjuste.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-3 py-6 text-center text-xs text-gray-400"><i class="ti ti-box"></i> Aún no has agregado productos.</td></tr>';
        return;
    }
    tbody.innerHTML = productosAjuste.map((p, i) => {
        const dif = p.nuevo_stock - p.stock_actual;
        const difClass = dif > 0 ? 'text-emerald-600' : dif < 0 ? 'text-red-600' : 'text-gray-400';
        const difSign = dif > 0 ? '+' : '';
        const even = i % 2 === 0 ? 'bg-white' : 'bg-gray-50/50';
        return `<tr class="${even} border-b border-gray-100 hover:bg-brand-50/40">
            <td class="px-2 py-1 text-gray-400">${i + 1}</td>
            <td class="px-2 py-1 font-medium text-gray-700">${p.descripcion}</td>
            <td class="px-2 py-1 text-right font-mono text-gray-500">${p.stock_actual}</td>
            <td class="px-2 py-1 text-right">
                <input type="number" min="0" value="${p.nuevo_stock}"
                       onchange="cambiarStock(${i}, this.value)"
                       class="field w-16 text-right font-mono text-[11px] py-0.5">
            </td>
            <td class="px-2 py-1 text-right font-mono font-bold ${difClass}">${difSign}${dif}</td>
            <td class="px-2 py-1 text-center">
                <button onclick="quitarProducto(${i})" class="h-5 w-5 flex items-center justify-center rounded bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600" title="Quitar"><i class="ti ti-trash text-[10px]"></i></button>
            </td>
        </tr>`;
    }).join('');
}

function cambiarStock(idx, val) {
    const n = parseInt(val);
    if (isNaN(n) || n < 0) return;
    productosAjuste[idx].nuevo_stock = n;
    renderTabla();
}

function quitarProducto(idx) {
    productosAjuste.splice(idx, 1);
    renderTabla();
}

async function guardarAjusteBatch() {
    const almacen = g('aj-almacen').value;
    const id_motivo = g('aj-motivo').value || null;
    if (!almacen) { toastWarn('Selecciona un almacén.'); return; }
    if (productosAjuste.length === 0) { toastWarn('Agrega al menos un producto.'); return; }

    // Filtrar productos con diferencia != 0
    const conCambio = productosAjuste.filter(p => p.nuevo_stock !== p.stock_actual);
    if (conCambio.length === 0) { toastWarn('Ningún producto tiene cambio de stock.'); return; }

    const payload = {
        almacen,
        id_motivo,
        observacion: g('aj-obs').value.trim(),
        productos: conCambio.map(p => ({ id_producto: p.id_producto, nuevo_stock: p.nuevo_stock })),
    };

    const d = await apiPost(`${BASE}/api/movimientos/batch`, payload);
    if (d.res) {
        toastOk(`Ajuste registrado (${d.count} producto${d.count !== 1 ? 's' : ''}).`);
        cerrarModal('md-ajuste');
        tablaAj.ajax.reload(null, false);
    } else {
        Swal.fire({ icon: 'warning', title: 'No se pudo registrar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
    }
}
</script>
@endpush
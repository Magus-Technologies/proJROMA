@extends('layouts.app')
@section('title','Almacenes')
@section('page-title','Almacenes')
@section('breadcrumb','Inventario / Almacenes')

@section('content')
{{-- Tabs de almacenes + acciones --}}
<div class="mb-4 flex flex-wrap items-center justify-between gap-2">
    <div id="almTabs" class="flex flex-1 flex-wrap gap-1 border-b border-gray-200">
        <span class="px-2 py-2 text-xs text-gray-400">Cargando…</span>
    </div>
    <x-btn color="primary" icon="ti ti-plus" onclick="abrirAlmModal()">Nuevo Almacén</x-btn>
</div>

{{-- Productos del almacén seleccionado --}}
<x-table id="tblAlmStock" title="Productos del almacén">
    <x-slot:filters>
        <x-btn color="emerald" icon="ti ti-package-import" onclick="abrirMov('I')">Ingreso</x-btn>
        <x-btn color="red" icon="ti ti-package-export" onclick="abrirMov('S')">Salida</x-btn>
        <button onclick="almEditActual()" title="Editar almacén" class="h-9 w-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50"><i class="ti ti-pencil"></i></button>
        <button onclick="almDelActual()" title="Eliminar almacén" class="h-9 w-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-red-500 hover:bg-red-50"><i class="ti ti-trash"></i></button>
    </x-slot:filters>
    <x-slot:thead>
        <x-th>Código</x-th>
        <x-th>Descripción</x-th>
        <x-th>Categoría</x-th>
        <x-th align="center">Stock</x-th>
        <x-th align="right">Precio</x-th>
    </x-slot:thead>
</x-table>

{{-- Modal crear/editar Almacén --}}
<x-modal id="md-almacen" title="Almacén" size="max-w-md">
    <input type="hidden" id="alm-id">
    <div class="space-y-4">
        <x-input-group label="Nombre" :required="true">
            <x-input id="alm-nombre" maxlength="150" placeholder="Ej. Almacén Central" onkeydown="if(event.key==='Enter')almGuardar()" />
        </x-input-group>
        <x-input-group label="Código">
            <x-input id="alm-codigo" maxlength="50" placeholder="Ej. A1" />
        </x-input-group>
        <x-input-group label="Sucursal">
            <select id="alm-sucursal" class="field bg-white"></select>
        </x-input-group>
        <x-input-group label="Descripción">
            <x-input id="alm-desc" maxlength="255" placeholder="Descripción (opcional)" />
        </x-input-group>
        <div><x-label>Estado</x-label><x-switch id="alm-estado" /></div>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-almacen')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="almGuardar()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>

{{-- Modal Crear Ingreso / Salida --}}
<x-modal id="md-mov" title="Movimiento" size="max-w-lg">
    <input type="hidden" id="mov-tipo">
    <input type="hidden" id="mov-almacen">
    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
            <x-label>Almacén</x-label>
            <input id="mov-almacen-nombre" type="text" class="field bg-gray-50" readonly>
        </div>
        <div class="col-span-2">
            <x-input-group label="Producto" :required="true">
                <select id="mov-producto" onchange="onMovProducto()" class="field bg-white"></select>
            </x-input-group>
        </div>
        <x-input-group label="Cantidad" :required="true">
            <x-input id="mov-cantidad" type="number" min="1" step="1" placeholder="0" oninput="calcNuevo()" />
        </x-input-group>
        <x-input-group label="Motivo">
            <select id="mov-motivo" class="field bg-white"></select>
        </x-input-group>
        <div>
            <x-label :optional="true">Stock actual</x-label>
            <input id="mov-stock" type="text" class="field bg-gray-50" readonly>
        </div>
        <div>
            <x-label :optional="true">Nuevo stock</x-label>
            <input id="mov-nuevo" type="text" class="field bg-gray-50 font-bold" readonly>
        </div>
        <x-input-group label="Costo">
            <x-input id="mov-costo" type="number" min="0" step="0.01" placeholder="0.00" />
        </x-input-group>
        <x-input-group label="Observaciones" class="col-span-2">
            <x-input id="mov-obs" maxlength="255" placeholder="Opcional" />
        </x-input-group>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-mov')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="movGuardar()">Registrar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE=BASE_URL;
const g = id => document.getElementById(id);
let tablaStock = null, almSel = null, almacenesData = [], sucursalesData = [];

/* ════════ TABS DE ALMACENES ════════ */
$(async function () {
    sucursalesData = await apiGet(`${BASE}/api/sucursales`, { activos: 1 });
    await cargarTabs();
});

async function cargarTabs() {
    almacenesData = await apiGet(`${BASE}/api/almacenes`);
    const cont = g('almTabs');
    if (!almacenesData.length) {
        cont.innerHTML = '<span class="px-2 py-2 text-xs text-gray-400">No hay almacenes. Crea el primero.</span>';
        return;
    }
    cont.innerHTML = almacenesData.map(a => {
        const code = a.codigo ?? a.id_almacen;
        const inact = a.estado !== '1' ? ' (inactivo)' : '';
        return `<button data-code="${code}" onclick="seleccionarAlm('${code}')"
            class="alm-tab -mb-px border-b-2 border-transparent px-4 py-2 text-xs font-semibold text-gray-500 hover:text-gray-700">
            ${a.nombre}<span class="text-[9px] text-gray-400">${inact}</span></button>`;
    }).join('');
    const keep = almacenesData.find(a => String(a.codigo ?? a.id_almacen) === String(almSel));
    seleccionarAlm(keep ? almSel : (almacenesData[0].codigo ?? almacenesData[0].id_almacen));
}

function seleccionarAlm(code) {
    almSel = String(code);
    document.querySelectorAll('.alm-tab').forEach(t => {
        const on = t.dataset.code === almSel;
        t.classList.toggle('border-brand-600', on);
        t.classList.toggle('text-brand-700', on);
        t.classList.toggle('border-transparent', !on);
    });
    const nombre = almacenesData.find(a => String(a.codigo ?? a.id_almacen) === almSel)?.nombre ?? '';
    const titleEl = document.querySelector('#tblAlmStock').closest('.card').querySelector('.card-header__title');
    if (titleEl) titleEl.textContent = 'Productos en ' + nombre;
    cargarStock(almSel);
}

function cargarStock(code) {
    if (tablaStock) { tablaStock.destroy(); $('#tblAlmStock tbody').empty(); }
    tablaStock = initDataTable('#tblAlmStock', {
        processing: true, serverSide: true,
        ajax: {
            url: `${BASE}/api/productos/serverside`,
            data: d => { d.almacenId = code; },
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            beforeSend: () => $('#tblAlmStock-loading').removeClass('hidden'),
            complete:   () => $('#tblAlmStock-loading').addClass('hidden'),
        },
        columns: [
            { data: 'codigo', defaultContent: '-' },
            { data: 'descripcion', responsivePriority: 1 },
            { data: 'categoria_nombre', defaultContent: '-', orderable: false, searchable: false },
            { data: 'cantidad', className: 'text-center font-bold',
              render: v => `<span class="${parseInt(v) <= 5 ? 'text-red-600' : 'text-emerald-600'}">${v}</span>` },
            { data: 'precio', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
        ],
        order: [[1, 'asc']],
    });
}

/* ════════ MOVIMIENTOS (Ingreso / Salida) ════════ */
async function abrirMov(tipo) {
    if (!almSel) { toastWarn('Selecciona un almacén.'); return; }
    g('mov-tipo').value = tipo;
    g('mov-almacen').value = almSel;
    g('mov-almacen-nombre').value = almacenesData.find(a => String(a.codigo ?? a.id_almacen) === almSel)?.nombre ?? '';
    const titulo = document.querySelector('#md-mov h4');
    if (titulo) titulo.textContent = tipo === 'I' ? 'Crear Ingreso' : 'Crear Salida';
    g('mov-cantidad').value = ''; g('mov-costo').value = ''; g('mov-obs').value = ''; g('mov-nuevo').value = '';

    const [prods, motivos] = await Promise.all([
        apiGet(`${BASE}/api/movimientos/productos`, { almacen: almSel }),
        apiGet(`${BASE}/api/movimientos/motivos`, { tipo }),
    ]);
    g('mov-producto').innerHTML = '<option value="">— Selecciona producto —</option>' +
        prods.map(p => `<option value="${p.id_producto}" data-stock="${p.cantidad}" data-costo="${p.costo ?? 0}">${p.descripcion}</option>`).join('');
    g('mov-motivo').innerHTML = '<option value="">— Motivo —</option>' +
        motivos.map(m => `<option value="${m.id_motivo}">${m.nombre}</option>`).join('');
    g('mov-stock').value = '';

    abrirModal('md-mov');
}

function onMovProducto() {
    const opt = g('mov-producto').selectedOptions[0];
    g('mov-stock').value = opt ? (opt.dataset.stock ?? '') : '';
    if (opt && opt.dataset.costo && !g('mov-costo').value) g('mov-costo').value = opt.dataset.costo;
    calcNuevo();
}

function calcNuevo() {
    const stock = parseInt(g('mov-stock').value || 0);
    const cant  = parseInt(g('mov-cantidad').value || 0);
    if (!g('mov-producto').value || !cant) { g('mov-nuevo').value = ''; return; }
    g('mov-nuevo').value = g('mov-tipo').value === 'I' ? (stock + cant) : (stock - cant);
}

async function movGuardar() {
    const id_producto = g('mov-producto').value;
    const cantidad = parseInt(g('mov-cantidad').value || 0);
    if (!id_producto) { toastWarn('Selecciona un producto.'); return; }
    if (!cantidad || cantidad < 1) { toastWarn('Ingresa una cantidad válida.'); return; }
    const d = await apiPost(`${BASE}/api/movimientos`, {
        almacen: g('mov-almacen').value,
        id_producto, tipo: g('mov-tipo').value,
        id_motivo: g('mov-motivo').value || null,
        cantidad,
        costo: g('mov-costo').value || null,
        observacion: g('mov-obs').value.trim(),
    });
    if (d.res) { toastOk('Movimiento registrado.'); cerrarModal('md-mov'); tablaStock.ajax.reload(null, false); }
    else Swal.fire({ icon:'warning', title:'No se pudo registrar', text: d.msg || 'Error.', confirmButtonColor:'#1d4ed8' });
}

/* ════════ CRUD ALMACÉN ════════ */
function abrirAlmModal(row = null) {
    g('alm-id').value     = row ? row.id_almacen : '';
    g('alm-nombre').value = row ? (row.nombre || '') : '';
    g('alm-codigo').value = row ? (row.codigo || '') : '';
    g('alm-desc').value   = row ? (row.descripcion || '') : '';
    g('alm-estado').checked = row ? (row.estado === '1') : true;
    g('alm-sucursal').innerHTML = '<option value="">— Sin sucursal —</option>' +
        sucursalesData.map(s => `<option value="${s.id_sucursal}">${s.nombre}</option>`).join('');
    g('alm-sucursal').value = row ? (row.id_sucursal || '') : '';
    abrirModal('md-almacen');
    setTimeout(() => g('alm-nombre').focus(), 100);
}
function almEditActual() {
    const a = almacenesData.find(x => String(x.codigo ?? x.id_almacen) === almSel);
    if (a) abrirAlmModal(a);
}
function almDelActual() {
    const a = almacenesData.find(x => String(x.codigo ?? x.id_almacen) === almSel);
    if (a) almDel(a.id_almacen);
}
async function almGuardar() {
    const id = g('alm-id').value;
    const nombre = g('alm-nombre').value.trim();
    if (!nombre) { toastWarn('Escribe un nombre.'); return; }
    const payload = { nombre, codigo: g('alm-codigo').value.trim(), descripcion: g('alm-desc').value.trim(), id_sucursal: g('alm-sucursal').value || null, estado: g('alm-estado').checked ? '1' : '0' };
    if (id) payload.id = id;
    const url = id ? `${BASE}/api/almacenes/editar` : `${BASE}/api/almacenes`;
    const d = await apiPost(url, payload);
    if (d.res) { toastOk(id ? 'Actualizado.' : 'Guardado.'); cerrarModal('md-almacen'); cargarTabs(); }
    else toastErr(d.msg || 'Error al guardar.');
}
async function almDel(id) {
    const { isConfirmed } = await Swal.fire({ title:'¿Eliminar almacén?', text:'Esta acción no se puede deshacer.', icon:'warning', showCancelButton:true, confirmButtonColor:'#dc2626', confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar' });
    if (!isConfirmed) return;
    const d = await apiPost(`${BASE}/api/almacenes/borrar`, { id });
    if (d.res) { toastOk('Eliminado.'); almSel = null; cargarTabs(); }
    else Swal.fire({ icon:'warning', title:'No se puede eliminar', text: d.msg || 'Ocurrió un error.', confirmButtonColor:'#1d4ed8' });
}
</script>
@endpush

@extends('layouts.app')
@section('title','Ajustes de Inventario')
@section('page-title','Ajustes / Cuadres')
@section('breadcrumb','Inventario / Ajustes')

@section('content')
<div class="mb-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs text-amber-700">
    <i class="ti ti-info-circle"></i> Aquí se registran <strong>ajustes manuales</strong> de stock (cuadres tras conteo físico, mermas, etc.). No son compras, ventas ni traslados.
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
        <x-btn color="emerald" icon="ti ti-plus" onclick="abrirAjuste('I')">Ingreso (+)</x-btn>
        <x-btn color="red" icon="ti ti-minus" onclick="abrirAjuste('S')">Salida (−)</x-btn>
    </x-slot:filters>
    <x-slot:thead>
        <x-th>Fecha</x-th>
        <x-th>Almacén</x-th>
        <x-th>Producto</x-th>
        <x-th align="center">Tipo</x-th>
        <x-th>Motivo</x-th>
        <x-th align="center">Cant.</x-th>
        <x-th align="center">Stock ant.</x-th>
        <x-th align="center">Stock nuevo</x-th>
        <x-th>Observación</x-th>
        <x-th align="center">Acción</x-th>
    </x-slot:thead>
</x-table>

{{-- Modal crear ajuste --}}
<x-modal id="md-ajuste" title="Ajuste" size="max-w-lg">
    <input type="hidden" id="aj-tipo">
    <div class="grid grid-cols-2 gap-4">
        <x-input-group label="Almacén" :required="true">
            <select id="aj-almacen" onchange="cargarProdsAjuste()" class="field bg-white"></select>
        </x-input-group>
        <x-input-group label="Motivo" :required="true">
            <select id="aj-motivo" class="field bg-white"></select>
        </x-input-group>

        <x-input-group label="Producto" :required="true" class="col-span-2">
            <select id="aj-producto" onchange="onAjProducto()" class="field bg-white"></select>
        </x-input-group>

        <x-input-group label="Cantidad" :required="true">
            <x-input id="aj-cantidad" type="number" min="1" step="1" placeholder="0" oninput="calcAjuste()" />
        </x-input-group>
        <div>
            <x-label :optional="true">Stock actual</x-label>
            <input id="aj-stock" type="text" class="field bg-gray-50" readonly>
        </div>
        <div>
            <x-label :optional="true">Nuevo stock</x-label>
            <input id="aj-nuevo" type="text" class="field bg-gray-50 font-bold" readonly>
        </div>
        <x-input-group label="Observación" class="col-span-2">
            <x-input id="aj-obs" maxlength="255" placeholder="Motivo del cuadre / detalle" />
        </x-input-group>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-ajuste')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarAjuste()">Registrar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tablaAj;

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

async function deshacerAjuste(id) {
    const { isConfirmed } = await Swal.fire({ title: '¿Deshacer este ajuste?', text: 'Se revertirá el stock.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d97706', confirmButtonText: 'Sí, deshacer', cancelButtonText: 'Cancelar' });
    if (!isConfirmed) return;
    const d = await apiPost(`${BASE}/api/movimientos/anular`, { id });
    if (d.res) { toastOk('Ajuste deshecho.'); tablaAj.ajax.reload(null, false); }
    else Swal.fire({ icon: 'warning', title: 'No se pudo deshacer', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
}

async function abrirAjuste(tipo) {
    g('aj-tipo').value = tipo;
    const t = document.querySelector('#md-ajuste h4');
    if (t) t.textContent = tipo === 'I' ? 'Ajuste — Ingreso (+)' : 'Ajuste — Salida (−)';
    g('aj-cantidad').value = ''; g('aj-stock').value = ''; g('aj-nuevo').value = ''; g('aj-obs').value = '';

    const [alms, motivos] = await Promise.all([
        apiGet(`${BASE}/api/almacenes`, { activos: 1 }),
        apiGet(`${BASE}/api/movimientos/motivos`, { tipo, ajuste: 1 }),
    ]);
    g('aj-almacen').innerHTML = alms.map(a => `<option value="${a.codigo ?? a.id_almacen}">${a.nombre}</option>`).join('');
    g('aj-motivo').innerHTML = '<option value="">— Motivo —</option>' + motivos.map(m => `<option value="${m.id_motivo}">${m.nombre}</option>`).join('');
    await cargarProdsAjuste();
    abrirModal('md-ajuste');
}

async function cargarProdsAjuste() {
    const prods = await apiGet(`${BASE}/api/movimientos/productos`, { almacen: g('aj-almacen').value });
    g('aj-producto').innerHTML = '<option value="">— Selecciona producto —</option>' +
        prods.map(p => `<option value="${p.id_producto}" data-stock="${p.cantidad}">${p.descripcion}</option>`).join('');
    g('aj-stock').value = ''; g('aj-nuevo').value = '';
}

function onAjProducto() {
    const opt = g('aj-producto').selectedOptions[0];
    g('aj-stock').value = opt ? (opt.dataset.stock ?? '') : '';
    calcAjuste();
}
function calcAjuste() {
    const stock = parseInt(g('aj-stock').value || 0);
    const cant  = parseInt(g('aj-cantidad').value || 0);
    if (!g('aj-producto').value || !cant) { g('aj-nuevo').value = ''; return; }
    g('aj-nuevo').value = g('aj-tipo').value === 'I' ? (stock + cant) : (stock - cant);
}

async function guardarAjuste() {
    const id_producto = g('aj-producto').value;
    const cantidad = parseInt(g('aj-cantidad').value || 0);
    if (!id_producto) { toastWarn('Selecciona un producto.'); return; }
    if (!cantidad || cantidad < 1) { toastWarn('Cantidad inválida.'); return; }
    const d = await apiPost(`${BASE}/api/movimientos`, {
        almacen: g('aj-almacen').value, id_producto, tipo: g('aj-tipo').value,
        id_motivo: g('aj-motivo').value || null, cantidad,
        observacion: g('aj-obs').value.trim(),
    });
    if (d.res) { toastOk('Ajuste registrado.'); cerrarModal('md-ajuste'); tablaAj.ajax.reload(null, false); }
    else Swal.fire({ icon: 'warning', title: 'No se pudo registrar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
}
</script>
@endpush

@extends('layouts.app')
@section('title','Traslado de Stock')
@section('page-title','Traslado de Stock')
@section('breadcrumb','Inventario / Traslado de Stock')

@section('content')
<div class="mx-auto max-w-2xl">
    <x-card>
        <div class="mb-4 flex items-center gap-2">
            <i class="ti ti-arrows-exchange text-xl text-brand-500"></i>
            <h3 class="card-header__title">Trasladar stock entre almacenes</h3>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <x-input-group label="Almacén origen" :required="true">
                <select id="t-origen" onchange="cargarProductosOrigen()" class="field bg-white"></select>
            </x-input-group>
            <x-input-group label="Almacén destino" :required="true">
                <select id="t-destino" class="field bg-white"></select>
            </x-input-group>

            <x-input-group label="Producto" :required="true" class="col-span-2">
                <select id="t-producto" onchange="onTProducto()" class="field bg-white"></select>
            </x-input-group>

            <div>
                <x-label :optional="true">Stock disponible</x-label>
                <input id="t-stock" type="text" class="field bg-gray-50" readonly>
            </div>
            <x-input-group label="Cantidad a trasladar" :required="true">
                <x-input id="t-cantidad" type="number" min="1" step="1" placeholder="0" />
            </x-input-group>

            <x-input-group label="Observación" class="col-span-2">
                <x-input id="t-obs" maxlength="200" placeholder="Opcional" />
            </x-input-group>
        </div>

        <div class="mt-5 flex justify-end">
            <x-btn color="primary" icon="ti ti-arrows-exchange" onclick="realizarTraslado()">Realizar Traslado</x-btn>
        </div>
    </x-card>

    <p class="mt-3 text-center text-[11px] text-gray-400">El historial de traslados queda registrado en el <strong>Kardex</strong>.</p>
</div>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);

$(async function () {
    const alms = await apiGet(`${BASE}/api/almacenes`, { activos: 1 });
    const opts = alms.map(a => `<option value="${a.codigo ?? a.id_almacen}">${a.nombre}</option>`).join('');
    g('t-origen').innerHTML  = opts;
    g('t-destino').innerHTML = opts;
    if (alms.length > 1) g('t-destino').selectedIndex = 1;   // distinto del origen por defecto
    cargarProductosOrigen();
});

async function cargarProductosOrigen() {
    const code = g('t-origen').value;
    const prods = await apiGet(`${BASE}/api/movimientos/productos`, { almacen: code });
    g('t-producto').innerHTML = '<option value="">— Selecciona producto —</option>' +
        prods.map(p => `<option value="${p.id_producto}" data-stock="${p.cantidad}">${p.descripcion}</option>`).join('');
    g('t-stock').value = '';
}

function onTProducto() {
    const opt = g('t-producto').selectedOptions[0];
    g('t-stock').value = opt ? (opt.dataset.stock ?? '') : '';
}

async function realizarTraslado() {
    const id_producto = g('t-producto').value;
    const cantidad = parseInt(g('t-cantidad').value || 0);
    if (g('t-origen').value === g('t-destino').value) { toastWarn('El origen y el destino deben ser distintos.'); return; }
    if (!id_producto) { toastWarn('Selecciona un producto.'); return; }
    if (!cantidad || cantidad < 1) { toastWarn('Ingresa una cantidad válida.'); return; }

    const d = await apiPost(`${BASE}/api/movimientos/traslado`, {
        almacen_origen:  g('t-origen').value,
        almacen_destino: g('t-destino').value,
        id_producto, cantidad,
        observacion: g('t-obs').value.trim(),
    });
    if (d.res) {
        toastOk('Traslado realizado.');
        g('t-cantidad').value = ''; g('t-obs').value = '';
        cargarProductosOrigen();
    } else {
        Swal.fire({ icon:'warning', title:'No se pudo trasladar', text: d.msg || 'Error.', confirmButtonColor:'#1d4ed8' });
    }
}
</script>
@endpush

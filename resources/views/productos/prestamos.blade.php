@extends('layouts.app')
@section('title','Préstamos de Productos')
@section('page-title','Préstamos de Productos')
@section('breadcrumb','Inventario / Préstamos de Productos')

@section('content')
<x-table id="tblPrestamos" title="Préstamos de Productos">
    <x-slot:filters>
        <x-btn color="primary" icon="ti ti-plus" onclick="abrirPrestamo()">Nuevo Préstamo</x-btn>
    </x-slot:filters>
    <x-slot:thead>
        <x-th>Fecha</x-th>
        <x-th align="center">Tipo</x-th>
        <x-th>Tercero</x-th>
        <x-th>Producto</x-th>
        <x-th>Almacén</x-th>
        <x-th align="center">Cant.</x-th>
        <x-th align="center">Estado</x-th>
        <x-th align="center">Acción</x-th>
    </x-slot:thead>
</x-table>

{{-- Modal nuevo préstamo --}}
<x-modal id="md-prestamo" title="Nuevo Préstamo" size="max-w-lg">
    <div class="grid grid-cols-2 gap-4">
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
            <select id="p-almacen" onchange="cargarProdsPrestamo()" class="field bg-white"></select>
        </x-input-group>
        <x-input-group label="Cantidad" :required="true">
            <x-input id="p-cantidad" type="number" min="1" step="1" placeholder="0" />
        </x-input-group>

        <x-input-group label="Producto" :required="true" class="col-span-2">
            <select id="p-producto" class="field bg-white"></select>
        </x-input-group>
        <x-input-group label="Observación" class="col-span-2">
            <x-input id="p-obs" maxlength="200" placeholder="Opcional" />
        </x-input-group>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-prestamo')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarPrestamo()">Registrar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tablaPre;

$(async function () {
    tablaPre = initDataTable('#tblPrestamos', {
        ajax: {
            url: `${BASE}/api/prestamos`, dataSrc: '',
            beforeSend: () => $('#tblPrestamos-loading').removeClass('hidden'),
            complete:   () => $('#tblPrestamos-loading').addClass('hidden'),
        },
        columns: [
            { data: 'fecha', render: v => v ? new Date(v.replace(' ', 'T')).toLocaleDateString('es-PE') : '-' },
            { data: 'tipo', className: 'text-center', orderable: false, searchable: false,
              render: v => v === 'P'
                  ? '<span class="inline-block rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Presto</span>'
                  : '<span class="inline-block rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">Me prestan</span>' },
            { data: 'tercero', defaultContent: '-', responsivePriority: 1 },
            { data: 'producto', defaultContent: '-' },
            { data: 'almacen_nombre', defaultContent: '-' },
            { data: 'cantidad', className: 'text-center font-bold' },
            { data: 'estado', className: 'text-center', orderable: false, searchable: false,
              render: v => v === 'D'
                  ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Devuelto</span>'
                  : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">Pendiente</span>' },
            { data: 'id_prestamo', orderable: false, searchable: false, className: 'text-center no-colvis',
              render: (id, t, row) => row.estado === 'P'
                  ? `<button onclick="devolverPrestamo(${id})" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-[11px] font-semibold text-white"><i class="ti ti-arrow-back-up"></i> Devolver</button>`
                  : '<span class="text-[10px] text-gray-400">—</span>' },
        ],
        order: [[0, 'desc']],
    });
});

async function abrirPrestamo() {
    g('p-tercero').value = ''; g('p-cantidad').value = ''; g('p-obs').value = ''; g('p-tipo').value = 'P';
    const alms = await apiGet(`${BASE}/api/almacenes`, { activos: 1 });
    g('p-almacen').innerHTML = alms.map(a => `<option value="${a.codigo ?? a.id_almacen}">${a.nombre}</option>`).join('');
    await cargarProdsPrestamo();
    abrirModal('md-prestamo');
}

async function cargarProdsPrestamo() {
    const prods = await apiGet(`${BASE}/api/movimientos/productos`, { almacen: g('p-almacen').value });
    g('p-producto').innerHTML = '<option value="">— Selecciona producto —</option>' +
        prods.map(p => `<option value="${p.id_producto}">${p.descripcion} (stock: ${p.cantidad})</option>`).join('');
}

async function guardarPrestamo() {
    const id_producto = g('p-producto').value;
    const tercero = g('p-tercero').value.trim();
    const cantidad = parseInt(g('p-cantidad').value || 0);
    if (!tercero) { toastWarn('Indica el tercero.'); return; }
    if (!id_producto) { toastWarn('Selecciona un producto.'); return; }
    if (!cantidad || cantidad < 1) { toastWarn('Cantidad inválida.'); return; }

    const d = await apiPost(`${BASE}/api/prestamos`, {
        tipo: g('p-tipo').value, tercero, almacen: g('p-almacen').value,
        id_producto, cantidad, observacion: g('p-obs').value.trim(),
    });
    if (d.res) { toastOk('Préstamo registrado.'); cerrarModal('md-prestamo'); tablaPre.ajax.reload(null, false); }
    else Swal.fire({ icon: 'warning', title: 'No se pudo registrar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
}

async function devolverPrestamo(id) {
    const { isConfirmed } = await Swal.fire({ title: '¿Registrar devolución?', icon: 'question', showCancelButton: true, confirmButtonColor: '#059669', confirmButtonText: 'Sí, devolver', cancelButtonText: 'Cancelar' });
    if (!isConfirmed) return;
    const d = await apiPost(`${BASE}/api/prestamos/devolver`, { id });
    if (d.res) { toastOk('Devolución registrada.'); tablaPre.ajax.reload(null, false); }
    else Swal.fire({ icon: 'warning', title: 'No se pudo devolver', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
}
</script>
@endpush

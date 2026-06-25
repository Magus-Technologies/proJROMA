@extends('layouts.app')
@section('title','Compras')
@section('page-title','Compras')
@section('breadcrumb','Inventario / Compras')
@section('content')
<div class="mb-4 flex gap-2">
    <a href="{{ config('app.url') }}/compras/add" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-semibold text-white transition"><i class="ti ti-plus"></i> Nueva Compra</a>
</div>
<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-4"><h3 class="text-sm font-semibold text-gray-700">Lista de Compras</h3></div>
    <div class="overflow-x-auto p-4">
        <table id="tbl" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500"><tr>
                <th class="px-3 py-2.5 text-left">#</th><th class="px-3 py-2.5 text-left">Tipo</th>
                <th class="px-3 py-2.5 text-left">Serie-Número</th><th class="px-3 py-2.5 text-left">Fecha</th>
                <th class="px-3 py-2.5 text-left">Proveedor</th><th class="px-3 py-2.5 text-right">Total</th>
                <th class="px-3 py-2.5 text-center">Recepción</th>
                <th class="px-3 py-2.5 text-center">Acciones</th>
            </tr></thead><tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal recepcionar (parcial o total) --}}
<x-modal id="md-recepcion" title="Recepcionar compra" size="max-w-2xl">
    <input type="hidden" id="rec-compra">
    <div class="mb-4 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600">
        <div>Compra <strong id="rec-doc"></strong></div>
        <div class="text-gray-400" id="rec-info"></div>
    </div>
    <x-input-group label="Almacén destino" :required="true" help="Lo recibido ingresará a este almacén.">
        <select id="rec-almacen" class="field bg-white"></select>
    </x-input-group>
    <div class="mb-2 mt-4 flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-600">Productos</span>
        <button onclick="recibirTodo()" class="text-xs font-semibold text-brand-600 hover:underline"><i class="ti ti-checks"></i> Recibir todo lo pendiente</button>
    </div>
    <div class="overflow-x-auto rounded-lg border border-gray-100">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500"><tr>
                <th class="px-3 py-2 text-left">Producto</th>
                <th class="px-3 py-2 text-center w-20">Pedido</th>
                <th class="px-3 py-2 text-center w-20">Recibido</th>
                <th class="px-3 py-2 text-center w-20">Pendiente</th>
                <th class="px-3 py-2 text-center w-28">Recibir</th>
            </tr></thead>
            <tbody id="rec-detalle"></tbody>
        </table>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-recepcion')">Cancelar</x-btn>
        <x-btn color="emerald" icon="ti ti-package-import" onclick="confirmarRecepcion()">Recepcionar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE=BASE_URL;
const g = id => document.getElementById(id);
let tabla, almacenesRec = [];

$(async function () {
    almacenesRec = await apiGet(`${BASE}/api/almacenes`, { activos: 1 });
    tabla = $('#tbl').DataTable({
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/compras', headers: { 'Accept':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' } },
        columns: [
            { data:'id_compra' },
            { data:'tipo_doc', defaultContent:'-' },
            { data:'documento', defaultContent:'-' },
            { data:'fecha_emision', defaultContent:'-' },
            { data:'proveedor_nombre', defaultContent:'-' },
            { data:'total', className:'text-right', render:v=>'S/ '+parseFloat(v||0).toFixed(2) },
            { data:'recepcionado', className:'text-center', orderable:false, searchable:false,
              render: v => parseInt(v)===1
                  ? '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Recepcionado</span>'
                  : (parseInt(v)===2
                      ? '<span class="inline-block rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Parcial</span>'
                      : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">Pendiente</span>') },
            { data:'id_compra', orderable:false, searchable:false, className:'text-center',
              render: (id, t, row) => {
                  const pdf = `<a href="${BASE}/reporte/compras/pdf/${id}" target="_blank" title="PDF" class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-file-type-pdf text-sm"></i></a>`;
                  const rec = parseInt(row.recepcionado) !== 1
                      ? `<button onclick="abrirRecepcion(${id})" title="Recepcionar" class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600"><i class="ti ti-package-import text-sm"></i></button>`
                      : '';
                  let edel = '';
                  if (parseInt(row.recepcionado) === 0) {
                      edel = `<a href="${BASE}/compras/add?id=${id}" title="Editar" class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-pencil text-sm"></i></a>
                              <button onclick="eliminarCompra(${id})" title="Eliminar" class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-sm"></i></button>`;
                  }
                  return `<div class="flex justify-center gap-1">${rec}${edel}${pdf}</div>`;
              } },
        ],
        order:[[0,'desc']], pageLength:25,
        language:{ url:'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',
    });
});

/* ════════ RECEPCIÓN ════════ */
async function abrirRecepcion(id) {
    const row = tabla.rows().data().toArray().find(r => String(r.id_compra) === String(id));
    g('rec-compra').value = id;
    g('rec-doc').textContent = (row && row.documento) || ('#' + id);
    g('rec-info').textContent = `${row?.proveedor_nombre || '-'} · S/ ${parseFloat(row?.total || 0).toFixed(2)}`;
    g('rec-almacen').innerHTML = almacenesRec.map(a => `<option value="${a.codigo ?? a.id_almacen}">${a.nombre}</option>`).join('');

    const lineas = await apiGet(`${BASE}/api/recepcion/lineas`, { id });
    g('rec-detalle').innerHTML = lineas.map(l => {
        const pend = parseInt(l.pendiente);
        const cell = pend > 0
            ? `<input type="number" min="0" max="${pend}" value="0" data-id="${l.id_producto}" data-max="${pend}" class="field text-center" style="width:90px">`
            : '<span class="text-[10px] font-semibold text-emerald-600">Completo</span>';
        return `<tr class="border-t border-gray-50">
            <td class="px-3 py-2">${l.producto}</td>
            <td class="px-3 py-2 text-center">${l.pedido}</td>
            <td class="px-3 py-2 text-center text-gray-500">${l.recibido}</td>
            <td class="px-3 py-2 text-center font-bold ${pend>0?'text-amber-600':'text-gray-400'}">${pend}</td>
            <td class="px-3 py-2 text-center">${cell}</td>
        </tr>`;
    }).join('');
    abrirModal('md-recepcion');
}

function recibirTodo() {
    g('rec-detalle').querySelectorAll('input[data-max]').forEach(inp => { inp.value = inp.dataset.max; });
}

async function confirmarRecepcion() {
    const almacen = g('rec-almacen').value;
    if (!almacen) { toastWarn('Selecciona un almacén.'); return; }
    const detalles = [];
    g('rec-detalle').querySelectorAll('input[data-id]').forEach(inp => {
        const c = parseInt(inp.value || 0);
        if (c > 0) detalles.push({ id_producto: inp.dataset.id, cantidad: Math.min(c, parseInt(inp.dataset.max)) });
    });
    if (!detalles.length) { toastWarn('Indica cuánto recibir.'); return; }

    const d = await apiPost(`${BASE}/api/recepcion/recepcionar`, { id_compra: g('rec-compra').value, almacen, detalles });
    if (d.res) {
        toastOk(d.estado === 1 ? 'Recepción completa.' : 'Recepción parcial registrada.');
        cerrarModal('md-recepcion');
        tabla.ajax.reload(null, false);
    } else {
        Swal.fire({ icon: 'warning', title: 'No se pudo recepcionar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
    }
}

async function eliminarCompra(id) {
    const { isConfirmed } = await Swal.fire({ title: '¿Eliminar la compra?', text: 'Se eliminará el documento y sus ítems.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' });
    if (!isConfirmed) return;
    const d = await apiPost(`${BASE}/api/recepcion/eliminar`, { id });
    if (d.res) { toastOk('Compra eliminada.'); tabla.ajax.reload(null, false); }
    else Swal.fire({ icon: 'warning', title: 'No se puede eliminar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
}
</script>
@endpush

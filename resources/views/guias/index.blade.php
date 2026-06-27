@extends('layouts.app')
@section('title','Guías de Remisión')
@section('page-title','Guías de Remisión')
@section('breadcrumb','Guías de Remisión')
@section('content')

<div class="mb-4 flex flex-wrap gap-2 items-center justify-between">
    <a id="btnNuevaGuia" href="{{ route('guias.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition">
        <i class="ti ti-plus"></i> Nueva Guía
    </a>
    <div class="flex flex-wrap items-center gap-2 text-xs">
        <select id="filtro_estado" class="rounded-lg border border-gray-300 px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
            <option value="">Todos los estados</option>
            <option value="1">Activo</option>
            <option value="0">Anulado</option>
        </select>
        <input type="date" id="filtro_desde" class="rounded-lg border border-gray-300 px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500" title="Desde">
        <input type="date" id="filtro_hasta" class="rounded-lg border border-gray-300 px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500" title="Hasta">
        <button onclick="aplicarFiltros()" class="rounded-lg bg-blue-600 hover:bg-blue-700 px-3 py-2 font-semibold text-white transition"><i class="ti ti-filter"></i> Filtrar</button>
        <button onclick="limpiarFiltros()" class="rounded-lg border border-gray-300 hover:bg-gray-50 px-3 py-2 font-semibold text-gray-600 transition"><i class="ti ti-x"></i> Limpiar</button>
    </div>
</div>

<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-4"><h3 class="text-sm font-semibold text-gray-700">Guías de Remisión</h3></div>
    <div class="overflow-x-auto p-4">
        <table id="tbl" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500"><tr>
                <th class="px-3 py-2.5 text-left">#</th>
                <th class="px-3 py-2.5 text-left">Documento</th>
                <th class="px-3 py-2.5 text-left">Fecha</th>
                <th class="px-3 py-2.5 text-left">Cliente</th>
                <th class="px-3 py-2.5 text-left">Destino</th>
                <th class="px-3 py-2.5 text-right">Peso (kg)</th>
                <th class="px-3 py-2.5 text-center">SUNAT</th>
                <th class="px-3 py-2.5 text-center">Estado</th>
                <th class="px-3 py-2.5 text-center">Acciones</th>
            </tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal detalle --}}
<div id="modalDetalle" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="cerrarModal()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl flex flex-col">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h3 class="text-sm font-bold text-gray-700" id="modalTitulo">Detalle Guía</h3>
            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x text-lg"></i></button>
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-4" id="modalBody">
            <div class="flex justify-center py-10"><i class="ti ti-loader-2 text-3xl text-gray-300 animate-spin"></i></div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .badge{display:inline-block;border-radius:9999px;padding:2px 8px;font-size:10px;font-weight:700}
    .b-act{background:#dbeafe;color:#1e40af}.b-anu{background:#f3f4f6;color:#6b7280}
    .b-env{background:#d1fae5;color:#065f46}.b-nenv{background:#fef3c7;color:#92400e}
</style>
@endpush

@push('scripts')
<script>
const BASE = BASE_URL;

function aplicarFiltros(){ $('#tbl').DataTable().ajax.reload(); }
function limpiarFiltros(){
    $('#filtro_estado').val(''); $('#filtro_desde').val(''); $('#filtro_hasta').val('');
    $('#tbl').DataTable().ajax.reload();
}

$(function(){
    $('#tbl').DataTable({
        processing: true, serverSide: true,
        ajax: {
            url: BASE + '/api/guias',
            headers: {'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            data: function(d) {
                d.estado      = $('#filtro_estado').val();
                d.fecha_desde = $('#filtro_desde').val();
                d.fecha_hasta = $('#filtro_hasta').val();
            }
        },
        columns: [
            {data:'id_guia_remision'},
            {data:'documento', defaultContent:'-'},
            {data:'fecha', defaultContent:'-'},
            {data:'cliente_nombre', defaultContent:'-', searchable:false},
            {data:'dir_llegada', defaultContent:'-'},
            {data:'peso', className:'text-right', render: v => parseFloat(v||0).toFixed(2)},
            {data:'enviado_sunat', className:'text-center',
             render: v => v==='1'
                ? '<span class="badge b-env">Enviado</span>'
                : '<span class="badge b-nenv">Pendiente</span>'},
            {data:'estado', className:'text-center',
             render: v => v==='1'
                ? '<span class="badge b-act">Activo</span>'
                : '<span class="badge b-anu">Anulada</span>'},
            {data:'id_guia_remision', orderable:false, className:'text-center',
             render:(id,type,row) => {
                let btns = '<div class="flex justify-center gap-1">';
                btns += `<button onclick="verDetalle(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600" title="Ver detalle"><i class="ti ti-eye text-sm"></i></button>`;
                btns += `<a href="${BASE}/guia/remision/pdf/${id}" target="_blank" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600" title="PDF"><i class="ti ti-file-type-pdf text-sm"></i></a>`;
                if(row.estado==='1'){
                    btns += `<button onclick="anularGuia(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-600" title="Anular"><i class="ti ti-ban text-sm"></i></button>`;
                }
                btns += '</div>';
                return btns;
             }},
        ],
        order:[[0,'desc']], pageLength:25,
        language:{url:'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'},
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',
    });
});

async function verDetalle(id) {
    document.getElementById('modalDetalle').classList.remove('hidden');
    document.getElementById('modalBody').innerHTML = '<div class="flex justify-center py-10"><i class="ti ti-loader-2 text-3xl text-gray-300 animate-spin"></i></div>';

    const d = await apiPost(BASE + '/api/guias/detalle', {id_guia: id});
    const doc = (d.serie||'T001') + '-' + String(d.numero||0).padStart(8,'0');
    document.getElementById('modalTitulo').textContent = 'Guía ' + doc;

    const transporte = d.tipo_transporte === '1' ? 'Privado' : 'Público';
    let rows = (d.detalles||[]).map(det => `
        <tr class="border-t border-gray-50">
            <td class="px-3 py-2">${det.detalles||'-'}</td>
            <td class="px-3 py-2 text-center">${det.cantidad}</td>
            <td class="px-3 py-2 text-center">${det.unidad||'NIU'}</td>
            <td class="px-3 py-2 text-right">S/ ${parseFloat(det.precio||0).toFixed(2)}</td>
        </tr>`).join('');

    document.getElementById('modalBody').innerHTML = `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div><span class="text-gray-400 block">Documento</span><strong>${doc}</strong></div>
                <div><span class="text-gray-400 block">Fecha</span><strong>${(d.fecha_emision||'').substring(0,10)}</strong></div>
                <div><span class="text-gray-400 block">Cliente</span><strong>${d.venta?.cliente?.datos||'-'}</strong></div>
                <div><span class="text-gray-400 block">Venta vinculada</span><strong>#${d.id_venta||'-'}</strong></div>
                <div><span class="text-gray-400 block">Destino</span><strong>${d.dir_llegada||'-'}</strong></div>
                <div><span class="text-gray-400 block">Ubigeo</span><strong>${d.ubigeo||'-'}</strong></div>
                <div><span class="text-gray-400 block">Transporte</span><strong>${transporte}</strong></div>
                <div><span class="text-gray-400 block">Vehículo</span><strong>${d.vehiculo||'-'}</strong></div>
                ${d.ruc_transporte?`<div><span class="text-gray-400 block">Transportista</span><strong>${d.razon_transporte||'-'} (${d.ruc_transporte})</strong></div>`:''}
                <div><span class="text-gray-400 block">Peso / Bultos</span><strong>${parseFloat(d.peso||0).toFixed(2)} kg / ${d.nro_bultos||0} bto.</strong></div>
            </div>
            <div class="rounded-xl border border-gray-100 overflow-hidden">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left">Descripción</th>
                            <th class="px-3 py-2 text-center">Cant.</th>
                            <th class="px-3 py-2 text-center">Unidad</th>
                            <th class="px-3 py-2 text-right">Precio</th>
                        </tr>
                    </thead>
                    <tbody>${rows||'<tr><td colspan="4" class="px-3 py-4 text-center text-gray-400">Sin productos</td></tr>'}</tbody>
                </table>
            </div>
        </div>`;
}

function cerrarModal() { document.getElementById('modalDetalle').classList.add('hidden'); }

async function anularGuia(id) {
    const {isConfirmed} = await Swal.fire({
        title:'¿Anular esta guía?', icon:'warning', showCancelButton:true,
        confirmButtonColor:'#dc2626', cancelButtonText:'Cancelar', confirmButtonText:'Sí, anular',
    });
    if (!isConfirmed) return;
    const d = await apiPost(BASE + '/api/guias/anular', {id_guia: id});
    if (d.res) { toastOk(d.msg); $('#tbl').DataTable().ajax.reload(null, false); }
    else toastErr(d.msg);
}
</script>
@endpush

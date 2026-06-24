@extends('layouts.app')
@section('title','Ventas')
@section('page-title','Ventas')
@section('breadcrumb','Facturación / Ventas')

@section('content')
<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ config('app.url') }}/ventas/productos"
       class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition">
        <i class="ti ti-plus"></i> Nueva Venta
    </a>
    <button onclick="exportarExcel()" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2 text-xs font-medium text-gray-700 transition">
        <i class="ti ti-file-type-xls text-emerald-500"></i> Excel
    </button>
    <button onclick="exportarPDF()" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2 text-xs font-medium text-gray-700 transition">
        <i class="ti ti-file-type-pdf text-red-500"></i> PDF
    </button>
</div>

<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <h3 class="text-sm font-semibold text-gray-700">Lista de Ventas</h3>
        <span id="tbl-loading" class="hidden"><i class="ti ti-loader-2 text-blue-500 spin"></i></span>
    </div>
    <div class="overflow-x-auto p-4">
        <table id="tblVentas" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-3 py-2.5 text-left">#</th>
                    <th class="px-3 py-2.5 text-left">Tipo</th>
                    <th class="px-3 py-2.5 text-left">Documento</th>
                    <th class="px-3 py-2.5 text-left">Fecha</th>
                    <th class="px-3 py-2.5 text-left">Cliente</th>
                    <th class="px-3 py-2.5 text-right">Total</th>
                    <th class="px-3 py-2.5 text-center">SUNAT</th>
                    <th class="px-3 py-2.5 text-center">Estado</th>
                    <th class="px-3 py-2.5 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal Detalle --}}
<div id="mdDetalle" class="fixed inset-0 z-50 hidden items-start justify-center pt-10 px-4">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarDetalle()"></div>
    <div class="relative z-10 w-full max-w-2xl rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-4">
            <h4 class="text-sm font-semibold text-gray-700" id="mdTitulo">Detalle de Venta</h4>
            <button onclick="cerrarDetalle()" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
        </div>
        <div id="mdBody" class="max-h-[70vh] overflow-y-auto p-5">
            <div class="flex justify-center py-8"><i class="ti ti-loader-2 text-2xl text-blue-400 spin"></i></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge{display:inline-block;border-radius:9999px;padding:2px 8px;font-size:10px;font-weight:700}
    .b-ok{background:#d1fae5;color:#065f46}.b-no{background:#fee2e2;color:#991b1b}
    .b-pend{background:#fef3c7;color:#92400e}.b-act{background:#dbeafe;color:#1e40af}
    .b-anu{background:#f3f4f6;color:#6b7280}
</style>
@endpush

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';

$(function(){
    $('#tblVentas').DataTable({
        processing:true, serverSide:true,
        ajax:{
            url: BASE+'/api/ventas',
            headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            beforeSend:()=>$('#tbl-loading').removeClass('hidden'),
            complete:()=>$('#tbl-loading').addClass('hidden'),
        },
        columns:[
            {data:'id_venta'},
            {data:'tipo_doc', defaultContent:'-'},
            {data:'documento'},
            {data:'fecha_emision', defaultContent:'-'},
            {data:'cliente_nombre', defaultContent:'-'},
            {data:'total', className:'text-right', render:v=>'<strong>S/ '+parseFloat(v||0).toFixed(2)+'</strong>'},
            {data:'estado_sunat', className:'text-center',
             render:v=>`<span class="badge ${v==='ACEPTADO'?'b-ok':v==='NO ENVIADO'?'b-no':'b-pend'}">${v}</span>`},
            {data:'estado', className:'text-center',
             render:v=>v==='1'?'<span class="badge b-act">Activa</span>':'<span class="badge b-anu">Anulada</span>'},
            {data:'acciones', orderable:false, className:'text-center',
             render:id=>`<div class="flex justify-center gap-1">
               <button onclick="verDetalle(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600" title="Ver detalle"><i class="ti ti-eye text-sm"></i></button>
               <a href="${BASE}/venta/comprobante/pdf/${id}" target="_blank" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600" title="PDF A4"><i class="ti ti-file-type-pdf text-sm"></i></a>
               <a href="${BASE}/venta/pdf/voucher/8cm/${id}" target="_blank" class="h-7 w-7 flex items-center justify-center rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-600" title="Voucher 8cm"><i class="ti ti-printer text-sm"></i></a>
               <button onclick="anularVenta(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-600" title="Anular"><i class="ti ti-ban text-sm"></i></button>
             </div>`},
        ],
        order:[[0,'desc']], pageLength:25,
        language:{url:'//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'},
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',
    });
});

function cerrarDetalle() { document.getElementById('mdDetalle').classList.replace('flex','hidden'); }

async function verDetalle(id) {
    const md = document.getElementById('mdDetalle');
    md.classList.replace('hidden','flex');
    document.getElementById('mdBody').innerHTML = '<div class="flex justify-center py-8"><i class="ti ti-loader-2 text-2xl text-blue-400 spin"></i></div>';

    const d = await apiPost(BASE+'/api/ventas/detalle', {id_venta:id});
    const doc = `${d.serie}-${String(d.numero).padStart(8,'0')}`;
    document.getElementById('mdTitulo').textContent = `Venta ${doc}`;

    let prods = '';
    (d.productos_venta||[]).forEach(p => {
        prods += `<tr class="border-t border-gray-50">
            <td class="px-3 py-2">${p.descripcion}</td>
            <td class="px-3 py-2 text-center">${p.cantidad}</td>
            <td class="px-3 py-2 text-right">S/ ${parseFloat(p.precio).toFixed(2)}</td>
            <td class="px-3 py-2 text-right font-semibold">S/ ${parseFloat(p.total).toFixed(2)}</td>
        </tr>`;
    });

    document.getElementById('mdBody').innerHTML = `
        <div class="grid grid-cols-2 gap-4 mb-5 text-xs">
            <div><span class="text-gray-400 block mb-0.5">Documento</span><strong class="font-mono text-blue-700">${doc}</strong></div>
            <div><span class="text-gray-400 block mb-0.5">Fecha</span>${d.fecha_emision||'-'}</div>
            <div><span class="text-gray-400 block mb-0.5">Cliente</span>${d.cliente?.datos||'-'}</div>
            <div><span class="text-gray-400 block mb-0.5">Total</span><strong class="text-base">S/ ${parseFloat(d.total||0).toFixed(2)}</strong></div>
            <div><span class="text-gray-400 block mb-0.5">Observación</span>${d.observacion||'-'}</div>
            <div><span class="text-gray-400 block mb-0.5">Estado SUNAT</span>${d.sunat?.estado_sunat||'NO ENVIADO'}</div>
        </div>
        <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">Productos</p>
        <table class="w-full text-xs rounded-lg border border-gray-100 overflow-hidden mb-5">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-3 py-2 text-left">Descripción</th>
                    <th class="px-3 py-2 text-center">Cant.</th>
                    <th class="px-3 py-2 text-right">P.Unit</th>
                    <th class="px-3 py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>${prods||'<tr><td colspan="4" class="text-center py-4 text-gray-400">Sin productos</td></tr>'}</tbody>
        </table>
        <div class="flex gap-2 justify-end">
            <a href="${BASE}/venta/comprobante/pdf/${id}" target="_blank" class="inline-flex items-center gap-1 rounded-lg bg-red-600 hover:bg-red-700 px-3 py-2 text-xs font-semibold text-white">
                <i class="ti ti-file-type-pdf"></i> PDF A4
            </a>
            <a href="${BASE}/venta/pdf/voucher/8cm/${id}" target="_blank" class="inline-flex items-center gap-1 rounded-lg bg-gray-700 hover:bg-gray-800 px-3 py-2 text-xs font-semibold text-white">
                <i class="ti ti-printer"></i> Voucher 8cm
            </a>
        </div>`;
}

async function anularVenta(id) {
    const {isConfirmed} = await Swal.fire({
        title:'¿Anular esta venta?', text:'Se repondrá el stock de los productos.',
        icon:'warning', showCancelButton:true, confirmButtonColor:'#dc2626',
        cancelButtonText:'Cancelar', confirmButtonText:'Sí, anular',
    });
    if (!isConfirmed) return;
    const d = await apiPost(BASE+'/api/ventas/anular', {id_venta:id});
    if (d.res) { toastOk(d.msg); $('#tblVentas').DataTable().ajax.reload(null,false); }
    else toastErr(d.msg);
}

function exportarExcel() { window.open(BASE+'/reporte/excel/'+new Date().toISOString().slice(0,7),'_blank'); }
function exportarPDF()   { window.open(BASE+'/reporte/ventas','_blank'); }
</script>
@endpush

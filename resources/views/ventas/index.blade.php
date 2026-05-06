@extends('layouts.app')
@section('title','Ventas')
@section('page-title','Ventas')
@section('breadcrumb','Facturación / Ventas')

@section('content')

<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('ventas.productos') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition">
        <i class="ti ti-plus"></i> Facturar Productos
    </a>
    <a href="{{ route('nota.electronica') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-xs font-semibold text-white transition">
        <i class="ti ti-file-invoice"></i> Nota Electrónica
    </a>
    <button onclick="exportarPDF()"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2 text-xs font-medium text-gray-700 transition">
        <i class="ti ti-file-type-pdf text-red-500"></i> PDF
    </button>
    <button onclick="exportarExcel()"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2 text-xs font-medium text-gray-700 transition">
        <i class="ti ti-file-type-xls text-emerald-500"></i> Excel
    </button>
</div>

<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <h3 class="text-sm font-semibold text-gray-700">Lista de Ventas</h3>
        <span id="tbl-loading" class="hidden">
            <i class="ti ti-loader-2 text-blue-500 spin"></i>
        </span>
    </div>
    <div class="overflow-x-auto p-4">
        <table id="tblVentas" class="w-full text-xs border-collapse">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-3 py-2.5 text-left font-medium">#</th>
                    <th class="px-3 py-2.5 text-left font-medium">Tipo</th>
                    <th class="px-3 py-2.5 text-left font-medium">Documento</th>
                    <th class="px-3 py-2.5 text-left font-medium">Fecha</th>
                    <th class="px-3 py-2.5 text-left font-medium">Cliente</th>
                    <th class="px-3 py-2.5 text-right font-medium">Total</th>
                    <th class="px-3 py-2.5 text-center font-medium">SUNAT</th>
                    <th class="px-3 py-2.5 text-center font-medium">Estado</th>
                    <th class="px-3 py-2.5 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal detalle --}}
<div id="mdDetalle" class="fixed inset-0 z-50 hidden items-start justify-center pt-10 px-4"
     x-data="{open:false}" x-show="open" x-cloak>
    <div class="absolute inset-0 bg-black/50" @click="open=false"></div>
    <div class="relative z-10 w-full max-w-2xl rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-4">
            <h4 class="text-sm font-semibold text-gray-700" id="mdTitulo">Detalle de Venta</h4>
            <button @click="open=false" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
        </div>
        <div id="mdBody" class="max-h-[70vh] overflow-y-auto p-5">
            <div class="flex justify-center py-10"><i class="ti ti-loader-2 text-2xl text-blue-400 spin"></i></div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .badge{display:inline-block;border-radius:9999px;padding:2px 8px;font-size:10px;font-weight:700}
    .badge-ok{background:#d1fae5;color:#065f46} .badge-no{background:#fee2e2;color:#991b1b}
    .badge-pend{background:#fef3c7;color:#92400e} .badge-act{background:#dbeafe;color:#1e40af}
    .badge-anu{background:#f3f4f6;color:#6b7280}
</style>
@endpush

@push('scripts')
<script>
$(function(){
    $('#tblVentas').DataTable({
        processing:true, serverSide:true,
        ajax:{
            url:'{{ url("/api/ventas") }}',
            headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            beforeSend:()=>$('#tbl-loading').removeClass('hidden'),
            complete:()=>$('#tbl-loading').addClass('hidden'),
        },
        columns:[
            {data:'id_venta'},
            {data:'tipo_doc'},
            {data:'documento'},
            {data:'fecha_emision'},
            {data:'cliente_nombre'},
            {data:'total', className:'text-right', render: v=>`<strong>${sol(v)}</strong>`},
            {data:'estado_sunat', className:'text-center',
             render:v=>`<span class="badge ${v==='ACEPTADO'?'badge-ok':v==='NO ENVIADO'?'badge-no':'badge-pend'}">${v}</span>`},
            {data:'estado', className:'text-center',
             render:v=>v==='1'?'<span class="badge badge-act">Activa</span>':'<span class="badge badge-anu">Anulada</span>'},
            {data:'acciones', orderable:false, className:'text-center',
             render:id=>`
                <div class="flex justify-center gap-1">
                    <button onclick="verDetalle(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600">
                        <i class="ti ti-eye text-sm"></i>
                    </button>
                    <a href="/venta/comprobante/pdf/${id}" target="_blank" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600">
                        <i class="ti ti-file-type-pdf text-sm"></i>
                    </a>
                    <a href="/venta/pdf/voucher/8cm/${id}" target="_blank" class="h-7 w-7 flex items-center justify-center rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-600">
                        <i class="ti ti-printer text-sm"></i>
                    </a>
                    <button onclick="anularVenta(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-600">
                        <i class="ti ti-ban text-sm"></i>
                    </button>
                </div>`
            },
        ],
        order:[[0,'desc']], pageLength:25,
        language:{ url:'//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',
    });
});

async function verDetalle(id) {
    const md = document.getElementById('mdDetalle');
    md.classList.remove('hidden'); md.classList.add('flex');
    document.getElementById('mdBody').innerHTML = '<div class="flex justify-center py-10"><i class="ti ti-loader-2 text-2xl text-blue-400 spin"></i></div>';

    const data = await apiPost('{{ url("/api/ventas/detalle") }}', {id_venta:id});
    const doc  = `${data.serie}-${String(data.numero).padStart(8,'0')}`;
    document.getElementById('mdTitulo').textContent = `Venta ${doc}`;

    let prods = '';
    (data.productos_venta||[]).forEach(p=>{
        prods+=`<tr class="border-t border-gray-50">
            <td class="px-3 py-2">${p.descripcion}</td>
            <td class="px-3 py-2 text-center">${p.cantidad}</td>
            <td class="px-3 py-2 text-right">${sol(p.precio)}</td>
            <td class="px-3 py-2 text-right font-semibold">${sol(p.total)}</td>
        </tr>`;
    });

    document.getElementById('mdBody').innerHTML = `
        <div class="grid grid-cols-2 gap-4 mb-5 text-xs">
            <div><span class="text-gray-400 block mb-0.5">Documento</span><strong class="font-mono text-blue-700">${doc}</strong></div>
            <div><span class="text-gray-400 block mb-0.5">Fecha</span>${data.fecha_emision??'-'}</div>
            <div><span class="text-gray-400 block mb-0.5">Cliente</span>${data.cliente?.datos??'-'}</div>
            <div><span class="text-gray-400 block mb-0.5">Total</span><strong class="text-base">${sol(data.total)}</strong></div>
        </div>
        <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">Productos</p>
        <table class="w-full text-xs rounded-lg border border-gray-100 overflow-hidden mb-5">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-3 py-2 text-left font-medium">Descripción</th>
                    <th class="px-3 py-2 text-center font-medium">Cant.</th>
                    <th class="px-3 py-2 text-right font-medium">P.Unit</th>
                    <th class="px-3 py-2 text-right font-medium">Total</th>
                </tr>
            </thead>
            <tbody>${prods||'<tr><td colspan="4" class="text-center py-4 text-gray-400">Sin productos</td></tr>'}</tbody>
        </table>
        <div class="flex gap-2 justify-end">
            <a href="/venta/comprobante/pdf/${id}" target="_blank"
               class="inline-flex items-center gap-1 rounded-lg bg-red-600 hover:bg-red-700 px-3 py-2 text-xs font-semibold text-white">
                <i class="ti ti-file-type-pdf"></i> PDF A4
            </a>
            <a href="/venta/pdf/voucher/8cm/${id}" target="_blank"
               class="inline-flex items-center gap-1 rounded-lg bg-gray-700 hover:bg-gray-800 px-3 py-2 text-xs font-semibold text-white">
                <i class="ti ti-printer"></i> Voucher 8cm
            </a>
        </div>`;
}

async function anularVenta(id){
    const {isConfirmed} = await Swal.fire({
        title:'¿Anular esta venta?', text:'Se repondrá el stock de los productos.',
        icon:'warning', showCancelButton:true, confirmButtonColor:'#dc2626',
        cancelButtonText:'Cancelar', confirmButtonText:'Sí, anular'
    });
    if(!isConfirmed) return;
    const d = await apiPost('{{ url("/api/ventas/anular") }}',{id_venta:id});
    if(d.res){ toastOk(d.msg); $('#tblVentas').DataTable().ajax.reload(null,false); }
    else toastErr(d.msg);
}

function exportarPDF(){ window.open('{{ route("reporte.ventas") }}','_blank'); }
function exportarExcel(){ window.open('{{ url("/reporte/excel/".date("Y-m")) }}','_blank'); }
</script>
@endpush

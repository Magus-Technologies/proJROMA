@extends('layouts.app')
@section('title','Cotizaciones')
@section('page-title','Pedidos / Cotizaciones')
@section('breadcrumb','Pedidos / Cotizaciones')
@section('content')
<div class="mb-4 flex flex-wrap gap-2 items-center justify-between">
    <a href="{{ route('cotizaciones.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition"><i class="ti ti-plus"></i> Nueva Cotización</a>
    <div class="flex flex-wrap items-center gap-2 text-xs" id="filtros">
        <select id="filtro_estado" class="rounded-lg border border-gray-300 px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
            <option value="">Todos los estados</option>
            <option value="1">Activo</option>
            <option value="0">Anulado</option>
            <option value="3">Facturado</option>
        </select>
        <input type="date" id="filtro_desde" class="rounded-lg border border-gray-300 px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500" title="Desde">
        <input type="date" id="filtro_hasta" class="rounded-lg border border-gray-300 px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500" title="Hasta">
        <button onclick="aplicarFiltros()" class="rounded-lg bg-blue-600 hover:bg-blue-700 px-3 py-2 font-semibold text-white transition"><i class="ti ti-filter"></i> Filtrar</button>
        <button onclick="limpiarFiltros()" class="rounded-lg border border-gray-300 hover:bg-gray-50 px-3 py-2 font-semibold text-gray-600 transition"><i class="ti ti-x"></i> Limpiar</button>
    </div>
</div>
<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-4"><h3 class="text-sm font-semibold text-gray-700">Pedidos / Cotizaciones</h3></div>
    <div class="overflow-x-auto p-4">
        <table id="tbl" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500"><tr>
                <th class="px-3 py-2.5 text-left">#</th><th class="px-3 py-2.5 text-left">N° Coti</th>
                <th class="px-3 py-2.5 text-left">Fecha</th><th class="px-3 py-2.5 text-left">Cliente</th>
                <th class="px-3 py-2.5 text-right">Total</th><th class="px-3 py-2.5 text-center">Estado</th>
                <th class="px-3 py-2.5 text-center">Acciones</th>
            </tr></thead><tbody></tbody>
        </table>
    </div>
</div>
@endsection
@push('styles')
<style>
    .badge{display:inline-block;border-radius:9999px;padding:2px 8px;font-size:10px;font-weight:700}
    .b-act{background:#dbeafe;color:#1e40af}.b-anu{background:#f3f4f6;color:#6b7280}.b-fact{background:#d1fae5;color:#065f46}
</style>
@endpush
@push('scripts')
<script>
const BASE=BASE_URL;

function aplicarFiltros(){
    $('#tbl').DataTable().ajax.reload();
}
function limpiarFiltros(){
    $('#filtro_estado').val(''); $('#filtro_desde').val(''); $('#filtro_hasta').val('');
    $('#tbl').DataTable().ajax.reload();
}

$(function(){
    $('#tbl').DataTable({processing:true,serverSide:true,
        ajax:{url:BASE+'/api/cotizaciones',headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
              data:function(d){
                  d.estado=$('#filtro_estado').val();
                  d.fecha_desde=$('#filtro_desde').val();
                  d.fecha_hasta=$('#filtro_hasta').val();
              }},
        columns:[
            {data:'cotizacion_id'},{data:'numero',defaultContent:'-'},{data:'fecha',defaultContent:'-'},
            {data:'cliente_nombre',defaultContent:'-',searchable:false},
            {data:'total',className:'text-right',render:v=>'S/ '+parseFloat(v||0).toFixed(2)},
            {data:'estado',className:'text-center',
             render:v=>{
                if(v==='1') return '<span class="badge b-act">Activo</span>';
                if(v==='3') return '<span class="badge b-fact">Facturado</span>';
                return '<span class="badge b-anu">Anulada</span>';
             }},
            {data:'cotizacion_id',orderable:false,className:'text-center',
             render:(id,type,row)=>{
                let btns='<div class="flex justify-center gap-1">';
                btns+=`<a href="${BASE}/cotizaciones/editar/${id}" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600" title="Editar"><i class="ti ti-pencil text-sm"></i></a>`;
                btns+=`<a href="${BASE}/cotizaciones/cuotas/${id}" class="h-7 w-7 flex items-center justify-center rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600" title="Cuotas"><i class="ti ti-calendar-dollar text-sm"></i></a>`;
                btns+=`<button onclick="openPdfModal('${BASE}/r/cotizaciones/reporte/${id}', 'Cotización N° ${row.numero}')" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600" title="Ver PDF"><i class="ti ti-file-type-pdf text-sm"></i></button>`;
                if(row.estado==='1'){
                    btns+=`<button onclick="convertirAVenta(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600" title="Convertir a Venta"><i class="ti ti-transfer text-sm"></i></button>`;
                }
                btns+=`<button onclick="anularCotizacion(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-600" title="Anular"><i class="ti ti-ban text-sm"></i></button>`;
                btns+='</div>';
                return btns;
             }},
        ],
        order:[[0,'desc']],pageLength:25,language:{url:'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'},
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',});
});

async function convertirAVenta(id) {
    const {value: id_tido} = await Swal.fire({
        title:'Convertir a Venta',
        text:'Selecciona el tipo de comprobante para la venta:',
        input:'select',
        inputOptions:{1:'Boleta',2:'Factura',11:'Ticket'},
        inputValue:'1',
        showCancelButton:true, confirmButtonText:'Convertir', cancelButtonText:'Cancelar',
        confirmButtonColor:'#d97706',
    });
    if(!id_tido) return;

    const d = await apiPost(BASE+'/api/cotizaciones/convertir', {id_cotizacion:id, id_tido:parseInt(id_tido)});
    if(d.res){
        const r = await Swal.fire({
            title:'¡Venta generada!', text:d.msg, icon:'success',
            showCancelButton:true, confirmButtonText:'Ver Ventas', cancelButtonText:'Seguir aquí',
            confirmButtonColor:'#1d4ed8',
        });
        if(r.isConfirmed) window.location=BASE+'/ventas';
        else $('#tbl').DataTable().ajax.reload(null,false);
    } else {
        toastErr(d.msg||'Error al convertir.');
    }
}

async function anularCotizacion(id) {
    const {isConfirmed} = await Swal.fire({
        title:'¿Anular esta cotización?', icon:'warning', showCancelButton:true,
        confirmButtonColor:'#dc2626', cancelButtonText:'Cancelar', confirmButtonText:'Sí, anular',
    });
    if (!isConfirmed) return;
    const d = await apiPost(BASE+'/api/cotizaciones/anular', {id_cotizacion:id});
    if (d.res) { toastOk(d.msg); $('#tbl').DataTable().ajax.reload(null,false); }
    else toastErr(d.msg);
}
</script>
@endpush

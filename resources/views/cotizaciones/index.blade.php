{{-- cotizaciones/index.blade.php --}}
@extends('layouts.app')
@section('title','Cotizaciones')
@section('page-title','Pedidos / Cotizaciones')
@section('breadcrumb','Pedidos / Cotizaciones')
@section('content')
<div class="mb-4 flex gap-2">
    <a href="{{ route('cotizaciones.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition"><i class="ti ti-plus"></i> Nueva Cotización</a>
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
@push('scripts')
<script>
const BASE=BASE_URL;
$(function(){
    $('#tbl').DataTable({processing:true,serverSide:true,
        ajax:{url:BASE+'/api/cotizaciones',headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}},
        columns:[
            {data:'cotizacion_id'},{data:'numero',defaultContent:'-'},{data:'fecha',defaultContent:'-'},
            {data:'cliente_nombre',defaultContent:'-'},
            {data:'total',className:'text-right',render:v=>'S/ '+parseFloat(v||0).toFixed(2)},
            {data:'estado',className:'text-center',render:v=>v==='1'?'<span class="inline-block rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">Activo</span>':'<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">Anulado</span>'},
            {data:'cotizacion_id',orderable:false,className:'text-center',
             render:id=>`<div class="flex justify-center gap-1">
               <a href="${BASE}/cotizaciones/editar/${id}" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-pencil text-sm"></i></a>
               <a href="${BASE}/r/cotizaciones/reporte/${id}" target="_blank" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-file-type-pdf text-sm"></i></a>
             </div>`},
        ],
        order:[[0,'desc']],pageLength:25,language:{url:'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'},
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',});
});
</script>
@endpush

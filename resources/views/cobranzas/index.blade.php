@extends('layouts.app')
@section('title','Cobranzas')
@section('page-title','Cuentas por Cobrar')
@section('breadcrumb','Cobranzas / Cuentas por Cobrar')
@section('content')
<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-4 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-700">Cuentas por Cobrar</h3>
        <button onclick="exportarExcel()" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">
            <i class="ti ti-file-type-xls text-emerald-500"></i> Excel
        </button>
    </div>
    <div class="overflow-x-auto p-4">
        <table id="tbl" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500"><tr>
                <th class="px-3 py-2.5 text-left">Documento</th><th class="px-3 py-2.5 text-left">Cliente</th>
                <th class="px-3 py-2.5 text-left">Fecha Venc.</th><th class="px-3 py-2.5 text-right">Monto</th>
                <th class="px-3 py-2.5 text-center">Estado</th><th class="px-3 py-2.5 text-center">Tipo Pago</th>
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
        ajax:{url:BASE+'/api/cobranzas',headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}},
        columns:[
            {data:'documento',defaultContent:'-'},{data:'cliente_nombre',defaultContent:'-'},
            {data:'fecha',defaultContent:'-'},
            {data:'monto',className:'text-right',render:v=>'S/ '+parseFloat(v||0).toFixed(2)},
            {data:'estado',className:'text-center',render:v=>v==='1'?'<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Pagado</span>':'<span class="inline-block rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Pendiente</span>'},
            {data:'tipo_pago',defaultContent:'Efectivo'},
            {data:'id',orderable:false,className:'text-center',
             render:id=>`<button onclick="cobrar(${id})" class="h-7 px-3 flex items-center justify-center rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold"><i class="ti ti-cash mr-1"></i>Cobrar</button>`},
        ],
        order:[[2,'asc']],pageLength:25,language:{url:'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'},
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',});
});
function exportarExcel(){ window.open(BASE+'/reporte/cobranzas/xls','_blank'); }
async function cobrar(id){
    const {value:tipoPago}=await Swal.fire({title:'Registrar Cobro',input:'select',inputOptions:{Efectivo:'Efectivo',Yape:'Yape',Plin:'Plin',Transferencia:'Transferencia',Deposito:'Depósito'},inputPlaceholder:'Selecciona tipo de pago',showCancelButton:true,cancelButtonText:'Cancelar',confirmButtonText:'Registrar'});
    if(!tipoPago)return;
    const d=await apiPost(BASE+'/api/cobranzas/add',{id,tipo_pago:tipoPago});
    if(d.res){toastOk('Cobro registrado.');$('#tbl').DataTable().ajax.reload(null,false);}
    else toastErr(d.msg||'Error al registrar cobro.');
}
</script>
@endpush

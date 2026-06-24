@extends('layouts.app')
@section('title','Registro de Caja')
@section('page-title','Registro de Caja')
@section('breadcrumb','Cajas / Registro')
@section('content')
<div class="mb-4 flex gap-2">
    <button onclick="abrirIngreso()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-xs font-semibold text-white transition"><i class="ti ti-arrow-up"></i> Ingreso</button>
    <button onclick="abrirEgreso()"  class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2 text-xs font-semibold text-white transition"><i class="ti ti-arrow-down"></i> Egreso</button>
</div>
<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-4"><h3 class="text-sm font-semibold text-gray-700">Registros de Caja</h3></div>
    <div class="overflow-x-auto p-4">
        <table id="tbl" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500"><tr>
                <th class="px-3 py-2.5 text-left">Fecha</th><th class="px-3 py-2.5 text-left">Tipo</th>
                <th class="px-3 py-2.5 text-left">Descripción</th><th class="px-3 py-2.5 text-right">Monto</th>
            </tr></thead><tbody></tbody>
        </table>
    </div>
</div>
{{-- Modal ingreso/egreso --}}
<div id="md" class="fixed inset-0 z-50 hidden items-start justify-center pt-10 px-4">
    <div class="absolute inset-0 bg-black/50" onclick="cerrar()"></div>
    <div class="relative z-10 w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-4">
            <h4 class="text-sm font-semibold text-gray-700" id="mdT">Ingreso de Caja</h4>
            <button onclick="cerrar()" class="text-gray-400"><i class="ti ti-x"></i></button>
        </div>
        <div class="p-5 space-y-4">
            <input type="hidden" id="itipo">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Descripción</label>
                <input id="idesc" type="text" maxlength="245" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Monto (S/)</label>
                <input id="imonto" type="number" step="0.01" min="0" placeholder="0.00" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></div>
        </div>
        <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-3">
            <button onclick="cerrar()" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-600">Cancelar</button>
            <button onclick="guardar()" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white"><i class="ti ti-device-floppy"></i> Guardar</button>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const BASE='{{ config("app.url") }}';let t;
const g=id=>document.getElementById(id);
function cerrar(){g('md').classList.replace('flex','hidden');}
$(function(){
    t=$('#tbl').DataTable({processing:true,serverSide:true,
        ajax:{url:BASE+'/api/caja/registros',headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}},
        columns:[{data:'fecha',defaultContent:'-'},{data:'tipo',defaultContent:'-'},{data:'descripcion',defaultContent:'-'},{data:'monto',className:'text-right',render:v=>'S/ '+parseFloat(v||0).toFixed(2)}],
        order:[[0,'desc']],pageLength:25,language:{url:'//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'},
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',});
});
function abrirIngreso(){g('mdT').textContent='Ingreso de Caja';g('itipo').value='ingreso';g('idesc').value='';g('imonto').value='';g('md').classList.replace('hidden','flex');}
function abrirEgreso(){g('mdT').textContent='Egreso de Caja';g('itipo').value='egreso';g('idesc').value='';g('imonto').value='';g('md').classList.replace('hidden','flex');}
async function guardar(){
    const monto=parseFloat(g('imonto').value||0);
    if(!g('idesc').value.trim()||monto<=0){toastWarn('Completa descripción y monto.');return;}
    const url=g('itipo').value==='ingreso'?BASE+'/api/caja/ingreso':BASE+'/api/caja/egreso';
    const d=await apiPost(url,{descripcion:g('idesc').value,monto});
    if(d.res){toastOk('Registrado.');cerrar();t.ajax.reload(null,false);}else toastErr(d.msg||'Error.');
}
</script>
@endpush

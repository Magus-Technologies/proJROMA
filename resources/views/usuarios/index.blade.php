@extends('layouts.app')
@section('title','Usuarios')
@section('page-title','Usuarios')
@section('breadcrumb','Administración / Usuarios')
@section('content')
<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-4"><h3 class="text-sm font-semibold text-gray-700">Lista de Usuarios</h3></div>
    <div class="overflow-x-auto p-4">
        <table id="tbl" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500"><tr>
                <th class="px-3 py-2.5 text-left">ID</th><th class="px-3 py-2.5 text-left">Usuario</th>
                <th class="px-3 py-2.5 text-left">Nombre</th><th class="px-3 py-2.5 text-left">Email</th>
                <th class="px-3 py-2.5 text-left">Rol</th><th class="px-3 py-2.5 text-center">Sucursal</th>
                <th class="px-3 py-2.5 text-center">Estado</th>
            </tr></thead><tbody></tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script>
const BASE='{{ config("app.url") }}';
$(function(){
    $('#tbl').DataTable({processing:true,serverSide:true,
        ajax:{url:BASE+'/api/usuarios/render',type:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},data:d=>JSON.stringify(d),contentType:'application/json'},
        columns:[
            {data:'usuario_id'},{data:'usuario'},{data:'nombre_completo',defaultContent:'-'},
            {data:'email',defaultContent:'-'},{data:'rol_nombre',defaultContent:'-'},
            {data:'sucursal',className:'text-center',defaultContent:'1'},
            {data:'estado',className:'text-center',render:v=>v==='1'?'<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activo</span>':'<span class="inline-block rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Inactivo</span>'},
        ],
        order:[[0,'asc']],pageLength:25,language:{url:'//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'},
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',});
});
</script>
@endpush

@extends('layouts.app')
@section('title','Proveedores')
@section('page-title','Proveedores')
@section('breadcrumb','Maestros / Proveedores')
@section('content')
<div class="mb-4 flex gap-2">
    <button onclick="abrirModalNuevo()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition"><i class="ti ti-plus"></i> Nuevo Proveedor</button>
</div>
<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-4"><h3 class="text-sm font-semibold text-gray-700">Lista de Proveedores</h3></div>
    <div class="overflow-x-auto p-4">
        <table id="tbl" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500"><tr>
                <th class="px-3 py-2.5 text-left">RUC / Doc</th><th class="px-3 py-2.5 text-left">Nombre</th>
                <th class="px-3 py-2.5 text-left">Comercial</th><th class="px-3 py-2.5 text-left">Teléfono</th>
                <th class="px-3 py-2.5 text-left">Email</th><th class="px-3 py-2.5 text-center">Acciones</th>
            </tr></thead><tbody></tbody>
        </table>
    </div>
</div>
<div id="md" class="fixed inset-0 z-50 hidden items-start justify-center pt-10 px-4">
    <div class="absolute inset-0 bg-black/50" onclick="cerrar()"></div>
    <div class="relative z-10 w-full max-w-xl rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-4">
            <h4 class="text-sm font-semibold text-gray-700" id="mdT">Nuevo Proveedor</h4>
            <button onclick="cerrar()" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
        </div>
        <div class="p-5 grid grid-cols-2 gap-4">
            <input type="hidden" id="i0">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">RUC / DNI *</label>
                <div class="flex gap-2">
                    <input id="i1" type="text" maxlength="11" placeholder="8 (DNI) u 11 (RUC) dígitos"
                           class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();buscarDoc();}">
                    <button id="btnBuscar" onclick="buscarDoc()"
                            class="rounded-lg bg-blue-50 hover:bg-blue-100 px-3 text-blue-600 transition" title="Consultar RENIEC / SUNAT">
                        <i id="iconBuscar" class="ti ti-search"></i>
                    </button>
                </div>
                <p id="docEstado" class="mt-1 text-[10px] hidden"></p>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Nombre *</label><input id="i2" type="text" maxlength="245" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></div>
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Nombre Comercial</label><input id="i3" type="text" maxlength="245" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></div>
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Dirección</label><input id="i4" type="text" maxlength="245" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Teléfono</label><input id="i5" type="text" maxlength="20" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Email</label><input id="i6" type="email" maxlength="200" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></div>
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
const BASE=BASE_URL;let t;
const g=id=>document.getElementById(id);
function abrir(){g('md').classList.replace('hidden','flex');}
function cerrar(){g('md').classList.replace('flex','hidden');}
$(function(){
    t=$('#tbl').DataTable({processing:true,serverSide:true,
        ajax:{url:BASE+'/api/proveedores',headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}},
        columns:[{data:'num_doc'},{data:'nombre'},{data:'nombre_comercial',defaultContent:'-'},{data:'telefono',defaultContent:'-'},{data:'email',defaultContent:'-'},
            {data:'proveedor_id',orderable:false,className:'text-center',render:id=>`<div class="flex justify-center gap-1">
                <button onclick="editar(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-pencil text-sm"></i></button>
                <button onclick="eliminar(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-sm"></i></button></div>`}],
        order:[[1,'asc']],pageLength:25,language:{url:'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'},
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',});
});
function abrirModalNuevo(){g('mdT').textContent='Nuevo Proveedor';['i0','i1','i2','i3','i4','i5','i6'].forEach(x=>g(x).value='');abrir();}
async function buscarDoc(){
    const doc = g('i1').value.trim();
    const status = g('docEstado');
    const icon   = g('iconBuscar');
    const btn    = g('btnBuscar');

    if (doc.length !== 8 && doc.length !== 11) {
        status.textContent = 'Ingresá 8 dígitos (DNI) o 11 dígitos (RUC).';
        status.className   = 'mt-1 text-[10px] text-amber-600';
        status.classList.remove('hidden');
        return;
    }

    icon.className = 'ti ti-loader-2 animate-spin';
    btn.disabled   = true;
    status.classList.add('hidden');

    try {
        const resp = await fetch(`${BASE}/api/consulta/documento?doc=${doc}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const d = await resp.json();

        if (!d.res) {
            status.textContent = d.msg || 'No encontrado.';
            status.className   = 'mt-1 text-[10px] text-red-500';
            status.classList.remove('hidden');
            return;
        }

        g('i2').value = d.nombre || '';
        g('i3').value = d.nombre_comercial || g('i3').value;
        g('i4').value = d.direccion || '';

        const etiqueta = d.tipo === 'dni' ? 'RENIEC' : 'SUNAT';
        status.textContent = `✓ Datos cargados desde ${etiqueta}.`;
        status.className   = 'mt-1 text-[10px] text-emerald-600';
        status.classList.remove('hidden');

    } catch {
        status.textContent = 'Error de conexión al consultar.';
        status.className   = 'mt-1 text-[10px] text-red-500';
        status.classList.remove('hidden');
    } finally {
        icon.className = 'ti ti-search';
        btn.disabled   = false;
    }
}
async function guardar(){
    const id=g('i0').value,doc=g('i1').value.trim(),nom=g('i2').value.trim();
    if(!doc||!nom){toastWarn('Documento y nombre son obligatorios.');return;}
    const p={num_doc:doc,nombre:nom,nombre_comercial:g('i3').value,direccion:g('i4').value,telefono:g('i5').value,email:g('i6').value};
    if(id)p.proveedor_id=parseInt(id);
    const url=id?BASE+'/api/proveedores/update':BASE+'/api/proveedores/add';
    const d=await apiPost(url,p);
    if(d.res){toastOk(id?'Proveedor actualizado.':'Proveedor registrado.');cerrar();t.ajax.reload(null,false);}
    else toastErr(d.msg||'Error.');
}
async function editar(id){
    const d=await apiPost(BASE+'/api/proveedores/get',{proveedor_id:id});
    g('mdT').textContent='Editar Proveedor';g('i0').value=d.proveedor_id;g('i1').value=d.num_doc||'';
    g('i2').value=d.nombre||'';g('i3').value=d.nombre_comercial||'';g('i4').value=d.direccion||'';
    g('i5').value=d.telefono||'';g('i6').value=d.email||'';abrir();
}
async function eliminar(id){
    const{isConfirmed}=await Swal.fire({title:'¿Eliminar proveedor?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonText:'Cancelar',confirmButtonText:'Sí, eliminar'});
    if(!isConfirmed)return;
    const d=await apiPost(BASE+'/api/proveedores/delete',{proveedor_id:id});
    if(d.res){toastOk('Proveedor eliminado.');t.ajax.reload(null,false);}else toastErr(d.msg||'Error.');
}
</script>
@endpush

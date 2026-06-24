@extends('layouts.app')
@section('title','Productos')
@section('page-title','Kardex / Productos')
@section('breadcrumb','Almacén / Productos')

@section('content')
<div class="mb-4 flex flex-wrap gap-2">
    <button onclick="abrirModalNuevo()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition">
        <i class="ti ti-plus"></i> Nuevo Producto
    </button>
    <button onclick="exportarExcel()" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2 text-xs font-medium text-gray-700 transition">
        <i class="ti ti-file-type-xls text-emerald-500"></i> Excel
    </button>
</div>

<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <h3 class="text-sm font-semibold text-gray-700">Kardex de Productos</h3>
        <div class="flex items-center gap-3">
            <select id="filtroAlmacen" onchange="cambiarAlmacen()" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs">
                <option value="1">Almacén 1</option>
                <option value="2">Almacén 2</option>
                <option value="3">Almacén 3</option>
            </select>
            <span id="tbl-loading" class="hidden"><i class="ti ti-loader-2 text-blue-500 spin"></i></span>
        </div>
    </div>
    <div class="overflow-x-auto p-4">
        <table id="tblProductos" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-3 py-2.5 text-left">Código</th>
                    <th class="px-3 py-2.5 text-left">Cód. Barra</th>
                    <th class="px-3 py-2.5 text-left">Descripción</th>
                    <th class="px-3 py-2.5 text-right">Precio</th>
                    <th class="px-3 py-2.5 text-right">Costo</th>
                    <th class="px-3 py-2.5 text-center">Stock</th>
                    <th class="px-3 py-2.5 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal --}}
<div id="mdProducto" class="fixed inset-0 z-50 hidden items-start justify-center pt-10 px-4">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModal()"></div>
    <div class="relative z-10 w-full max-w-2xl rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-4">
            <h4 class="text-sm font-semibold text-gray-700" id="mdTitulo">Nuevo Producto</h4>
            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
        </div>
        <div class="p-5 grid grid-cols-2 gap-4">
            <input type="hidden" id="pid">
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción *</label>
                <input id="pdesc" type="text" maxlength="245" placeholder="Nombre del producto"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Código</label>
                <input id="pcod" type="text" maxlength="50"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Código de Barra</label>
                <input id="pbarra" type="text" maxlength="100"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Precio Venta *</label>
                <input id="pprecio" type="number" step="0.01" min="0" placeholder="0.00"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Costo</label>
                <input id="pcosto" type="number" step="0.01" min="0" placeholder="0.00"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Precio 2</label>
                <input id="pprecio2" type="number" step="0.01" min="0" placeholder="0.00"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Precio 3</label>
                <input id="pprecio3" type="number" step="0.01" min="0" placeholder="0.00"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Stock</label>
                <input id="pcantidad" type="number" step="1" min="0" placeholder="0"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Almacén</label>
                <select id="palmacen" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="1">Almacén 1</option>
                    <option value="2">Almacén 2</option>
                    <option value="3">Almacén 3</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Código SUNAT</label>
                <input id="psunat" type="text" maxlength="20" placeholder="ZZ"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Afectación IGV</label>
                <select id="piscbp" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="0">Gravado</option>
                    <option value="1">Exonerado</option>
                    <option value="2">Inafecto</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-3">
            <button onclick="cerrarModal()" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">Cancelar</button>
            <button onclick="guardarProducto()" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
let tabla;

$(function() { cargarTabla(1); });

function cargarTabla(almacen) {
    if (tabla) { tabla.destroy(); $('#tblProductos').empty().append('<thead class="bg-gray-50 text-gray-500"><tr><th class="px-3 py-2.5 text-left">Código</th><th class="px-3 py-2.5 text-left">Cód. Barra</th><th class="px-3 py-2.5 text-left">Descripción</th><th class="px-3 py-2.5 text-right">Precio</th><th class="px-3 py-2.5 text-right">Costo</th><th class="px-3 py-2.5 text-center">Stock</th><th class="px-3 py-2.5 text-center">Acciones</th></tr></thead><tbody></tbody>'); }
    tabla = $('#tblProductos').DataTable({
        processing: true, serverSide: true,
        ajax: {
            url: BASE+'/api/productos/serverside',
            data: d => { d.almacenId = almacen; },
            headers: { 'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}' },
            beforeSend: () => $('#tbl-loading').removeClass('hidden'),
            complete:   () => $('#tbl-loading').addClass('hidden'),
        },
        columns: [
            { data:'codigo',      defaultContent:'-' },
            { data:'cod_barra',   defaultContent:'-' },
            { data:'descripcion' },
            { data:'precio',   className:'text-right', render: v => 'S/ '+parseFloat(v||0).toFixed(2) },
            { data:'costo',    className:'text-right', render: v => 'S/ '+parseFloat(v||0).toFixed(2) },
            { data:'cantidad', className:'text-center font-bold',
              render: v => `<span class="${parseInt(v)<=5?'text-red-600':'text-emerald-600'}">${v}</span>` },
            { data:'id_producto', orderable:false, className:'text-center',
              render: id => `<div class="flex justify-center gap-1">
                <button onclick="editarProducto(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-pencil text-sm"></i></button>
                <button onclick="eliminarProducto(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-sm"></i></button>
              </div>` },
        ],
        order:[[2,'asc']], pageLength:25,
        language:{ url:'//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        dom:'<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',
    });
}

function cambiarAlmacen() { cargarTabla(document.getElementById('filtroAlmacen').value); }
const g = id => document.getElementById(id);
function abrirModal()  { g('mdProducto').classList.replace('hidden','flex'); }
function cerrarModal() { g('mdProducto').classList.replace('flex','hidden'); }

function abrirModalNuevo() {
    g('mdTitulo').textContent = 'Nuevo Producto';
    ['pid','pdesc','pcod','pbarra','pprecio','pcosto','pprecio2','pprecio3','pcantidad','psunat'].forEach(id => g(id).value='');
    g('palmacen').value='1'; g('piscbp').value='0';
    abrirModal();
}

async function guardarProducto() {
    const id=g('pid').value, desc=g('pdesc').value.trim(), prec=g('pprecio').value;
    if (!desc||!prec) { toastWarn('Descripción y precio son obligatorios.'); return; }
    const payload = {
        descripcion:desc, precio:parseFloat(prec),
        costo:parseFloat(g('pcosto').value||0), precio2:parseFloat(g('pprecio2').value||0),
        precio3:parseFloat(g('pprecio3').value||0), cantidad:parseInt(g('pcantidad').value||0),
        codigo:g('pcod').value, cod_barra:g('pbarra').value,
        almacen:g('palmacen').value, codsunat:g('psunat').value, iscbp:parseInt(g('piscbp').value),
    };
    if (id) payload.id_producto = parseInt(id);
    const url  = id ? BASE+'/api/productos/editar' : BASE+'/api/productos/add';
    const data = await apiPost(url, payload);
    if (data.res) { toastOk(id?'Producto actualizado.':'Producto registrado.'); cerrarModal(); tabla.ajax.reload(null,false); }
    else toastErr(data.msg||'Error al guardar.');
}

async function editarProducto(id) {
    const d = await apiPost(BASE+'/api/productos/get-one',{id_producto:id});
    g('mdTitulo').textContent='Editar Producto';
    g('pid').value=d.id_producto; g('pdesc').value=d.descripcion||''; g('pcod').value=d.codigo||'';
    g('pbarra').value=d.cod_barra||''; g('pprecio').value=d.precio||0; g('pcosto').value=d.costo||0;
    g('pprecio2').value=d.precio2||0; g('pprecio3').value=d.precio3||0; g('pcantidad').value=d.cantidad||0;
    g('palmacen').value=d.almacen||'1'; g('psunat').value=d.codsunat||''; g('piscbp').value=d.iscbp||0;
    abrirModal();
}

async function eliminarProducto(id) {
    const {isConfirmed} = await Swal.fire({title:'¿Dar de baja?',text:'Se marcará como inactivo.',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonText:'Cancelar',confirmButtonText:'Sí, dar de baja'});
    if (!isConfirmed) return;
    const d = await apiPost(BASE+'/api/productos/borrar',{id_producto:id});
    if (d.res) { toastOk('Producto dado de baja.'); tabla.ajax.reload(null,false); }
    else toastErr(d.msg||'Error.');
}

function exportarExcel() { window.open(BASE+'/reporte/producto/excel','_blank'); }
</script>
@endpush

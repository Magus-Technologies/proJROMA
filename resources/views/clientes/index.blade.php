@extends('layouts.app')
@section('title','Clientes')
@section('page-title','Clientes')
@section('breadcrumb','Maestros / Clientes')

@section('content')

<div class="mb-4 flex flex-wrap gap-2">
    <button onclick="abrirModalNuevo()"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition">
        <i class="ti ti-plus"></i> Nuevo Cliente
    </button>
    <button onclick="exportarExcel()"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2 text-xs font-medium text-gray-700 transition">
        <i class="ti ti-file-type-xls text-emerald-500"></i> Excel
    </button>
</div>

<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <h3 class="text-sm font-semibold text-gray-700">Lista de Clientes</h3>
        <span id="tbl-loading" class="hidden"><i class="ti ti-loader-2 text-blue-500 spin"></i></span>
    </div>
    <div class="overflow-x-auto p-4">
        <table id="tblClientes" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-3 py-2.5 text-left">Documento</th>
                    <th class="px-3 py-2.5 text-left">Nombre / Razón Social</th>
                    <th class="px-3 py-2.5 text-left">Teléfono</th>
                    <th class="px-3 py-2.5 text-left">Dirección</th>
                    <th class="px-3 py-2.5 text-left">Días Visita</th>
                    <th class="px-3 py-2.5 text-right">Total Ventas</th>
                    <th class="px-3 py-2.5 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal --}}
<div id="mdCliente" class="fixed inset-0 z-50 hidden items-start justify-center pt-10 px-4">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModal()"></div>
    <div class="relative z-10 w-full max-w-xl rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-4">
            <h4 class="text-sm font-semibold text-gray-700" id="mdTitulo">Nuevo Cliente</h4>
            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
        </div>
        <div class="p-5 grid grid-cols-2 gap-4">
            <input type="hidden" id="cid">
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">RUC / DNI *</label>
                <div class="flex gap-2">
                    <input id="cdoc" type="text" maxlength="11" placeholder="20123456789"
                           class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <button onclick="consultarDoc()" class="rounded-lg bg-blue-50 hover:bg-blue-100 px-3 text-blue-600">
                        <i class="ti ti-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre / Razón Social *</label>
                <input id="cdatos" type="text" maxlength="245" placeholder="Nombre o razón social"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Dirección</label>
                <input id="cdir" type="text" maxlength="245" placeholder="Av. Principal 123"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Teléfono</label>
                <input id="ctel" type="text" maxlength="20" placeholder="999888777"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                <input id="cemail" type="email" maxlength="200" placeholder="correo@ejemplo.com"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Días de Visita</label>
                <input id="cdias" type="text" maxlength="200" placeholder="Lunes, Miércoles"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Distrito</label>
                <input id="cdist" type="text" maxlength="100" placeholder="Lima"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-3">
            <button onclick="cerrarModal()"
                    class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">
                Cancelar
            </button>
            <button onclick="guardarCliente()"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const BASE=BASE_URL;
let tabla;

$(function() {
    tabla = $('#tblClientes').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: BASE + '/api/clientes',
            headers: { 'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}' },
            beforeSend: () => $('#tbl-loading').removeClass('hidden'),
            complete:   () => $('#tbl-loading').addClass('hidden'),
        },
        columns: [
            { data: 'documento' },
            { data: 'datos' },
            { data: 'telefono',     defaultContent: '-' },
            { data: 'direccion',    defaultContent: '-' },
            { data: 'dias_visitas', defaultContent: '-' },
            { data: 'total_venta',  className: 'text-right',
              render: v => 'S/ ' + parseFloat(v||0).toFixed(2) },
            { data: 'id_cliente', orderable: false, className: 'text-center',
              render: id => `
              <div class="flex justify-center gap-1">
                <button onclick="editarCliente(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600" title="Editar">
                  <i class="ti ti-pencil text-sm"></i></button>
                <button onclick="eliminarCliente(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600" title="Eliminar">
                  <i class="ti ti-trash text-sm"></i></button>
              </div>` },
        ],
        order: [[1,'asc']], pageLength: 25,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        dom: '<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',
    });
});

const g = id => document.getElementById(id);
function abrirModal()  { g('mdCliente').classList.replace('hidden','flex'); }
function cerrarModal() { g('mdCliente').classList.replace('flex','hidden'); }

function abrirModalNuevo() {
    g('mdTitulo').textContent = 'Nuevo Cliente';
    ['cid','cdoc','cdatos','cdir','ctel','cemail','cdias','cdist'].forEach(id => g(id).value = '');
    abrirModal();
}

async function consultarDoc() {
    const doc = g('cdoc').value.trim();
    if (doc.length < 8) { toastWarn('Ingresa RUC (11 dígitos) o DNI (8 dígitos).'); return; }
    try {
        const data = await apiPost(BASE + '/api/consulta/sn', { doc });
        if (data.nombre || data.razonSocial) {
            g('cdatos').value = data.nombre || data.razonSocial || '';
            g('cdir').value   = data.direccion || '';
            toastOk('Datos encontrados');
        } else { toastWarn('No se encontraron datos.'); }
    } catch { toastWarn('Error al consultar.'); }
}

async function guardarCliente() {
    const id  = g('cid').value;
    const doc = g('cdoc').value.trim();
    const nom = g('cdatos').value.trim();
    if (!doc || !nom) { toastWarn('Documento y nombre son obligatorios.'); return; }

    const payload = {
        documento: doc, datos: nom,
        direccion: g('cdir').value, telefono: g('ctel').value,
        email: g('cemail').value, dias_visitas: g('cdias').value,
        distrito: g('cdist').value,
    };
    if (id) payload.id_cliente = parseInt(id);

    const url = id ? BASE+'/api/clientes/editar' : BASE+'/api/clientes/add';
    const data = await apiPost(url, payload);

    if (data.res) {
        toastOk(id ? 'Cliente actualizado.' : 'Cliente registrado.');
        cerrarModal();
        tabla.ajax.reload(null, false);
    } else { toastErr(data.msg || 'Error al guardar.'); }
}

async function editarCliente(id) {
    const data = await apiPost(BASE+'/api/clientes/get-one', { id_cliente: id });
    g('mdTitulo').textContent = 'Editar Cliente';
    g('cid').value    = data.id_cliente;
    g('cdoc').value   = data.documento    || '';
    g('cdatos').value = data.datos        || '';
    g('cdir').value   = data.direccion    || '';
    g('ctel').value   = data.telefono     || '';
    g('cemail').value = data.email        || '';
    g('cdias').value  = data.dias_visitas || '';
    g('cdist').value  = data.distrito     || '';
    abrirModal();
}

async function eliminarCliente(id) {
    const { isConfirmed } = await Swal.fire({
        title:'¿Eliminar este cliente?', text:'Esta acción no se puede deshacer.',
        icon:'warning', showCancelButton:true, confirmButtonColor:'#dc2626',
        cancelButtonText:'Cancelar', confirmButtonText:'Sí, eliminar',
    });
    if (!isConfirmed) return;
    const data = await apiPost(BASE+'/api/clientes/borrar', { id_cliente: id });
    if (data.res) { toastOk('Cliente eliminado.'); tabla.ajax.reload(null,false); }
    else toastErr(data.msg || 'No se pudo eliminar.');
}

function exportarExcel() { window.open(BASE+'/reporte/clientes/xls','_blank'); }
</script>
@endpush

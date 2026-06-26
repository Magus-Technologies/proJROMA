@extends('layouts.app')
@section('title','Notas Electrónicas')
@section('page-title','Notas Electrónicas')
@section('breadcrumb','Ventas / Notas Electrónicas')
@section('content')

<div class="mb-4 flex gap-2">
    <a href="{{ route('nota.electronica') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition">
        <i class="ti ti-plus"></i> Nueva Nota
    </a>
</div>

<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-4">
        <h3 class="text-sm font-semibold text-gray-700">Notas de Crédito y Débito Electrónicas</h3>
    </div>
    <div class="overflow-x-auto p-4">
        <table id="tbl" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-3 py-2.5 text-left">Nota</th>
                    <th class="px-3 py-2.5 text-left">Tipo</th>
                    <th class="px-3 py-2.5 text-left">Comprobante Afectado</th>
                    <th class="px-3 py-2.5 text-left">Motivo</th>
                    <th class="px-3 py-2.5 text-left">Cliente</th>
                    <th class="px-3 py-2.5 text-right">Total</th>
                    <th class="px-3 py-2.5 text-center">Fecha</th>
                    <th class="px-3 py-2.5 text-center">SUNAT</th>
                    <th class="px-3 py-2.5 text-center">Estado</th>
                    <th class="px-3 py-2.5 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal Nueva Nota --}}
<div id="mdNota" class="fixed inset-0 z-50 hidden items-start justify-center pt-6 px-4">
    <div class="absolute inset-0 bg-black/50" onclick="cerrar()"></div>
    <div class="relative z-10 w-full max-w-3xl rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-4 shrink-0">
            <h4 class="text-sm font-semibold text-gray-700">Nueva Nota Electrónica</h4>
            <button onclick="cerrar()" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
        </div>

        <div class="overflow-y-auto p-5 space-y-4">
            {{-- Buscar venta --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Comprobante a Afectar *</label>
                <div class="flex gap-2">
                    <input id="nBuscarTerm" type="text" placeholder="Serie-número o nombre del cliente..."
                           class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                           oninput="buscarVentas()">
                </div>
                <div id="nResultados" class="hidden mt-1 rounded-lg border border-gray-200 bg-white shadow-md max-h-48 overflow-y-auto text-xs"></div>
                <div id="nVentaSel" class="hidden mt-2 rounded-lg bg-blue-50 border border-blue-200 px-3 py-2 text-xs text-blue-800"></div>
                <input type="hidden" id="nIdVenta">
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Tipo --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo de Nota *</label>
                    <select id="nTipo" onchange="actualizarMotivos()"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="credito">Nota de Crédito (EC01)</option>
                        <option value="debito">Nota de Débito (ED01)</option>
                    </select>
                </div>
                {{-- Motivo --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Motivo SUNAT *</label>
                    <select id="nMotivo" onchange="actualizarDescMotivo()"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </select>
                </div>
            </div>

            {{-- Ítems cargados de la venta --}}
            <div id="nItemsWrap" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 mb-2">Ítems del comprobante</label>
                <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left">Descripción</th>
                            <th class="px-3 py-2 text-center w-20">Cant.</th>
                            <th class="px-3 py-2 text-right w-24">Precio</th>
                            <th class="px-3 py-2 text-right w-24">Total</th>
                        </tr>
                    </thead>
                    <tbody id="nItemsBody"></tbody>
                </table>
            </div>

            {{-- Total --}}
            <div class="flex justify-end">
                <div class="rounded-xl bg-gray-50 border border-gray-200 px-5 py-3 text-right min-w-[200px]">
                    <div class="text-xs text-gray-500 mb-1">Total Nota</div>
                    <div class="text-xl font-bold text-gray-800">S/ <span id="nTotal">0.00</span></div>
                    <input type="hidden" id="nTotalVal" value="0">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-3 shrink-0">
            <button onclick="cerrar()" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-600">Cancelar</button>
            <button onclick="guardar()" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white">
                <i class="ti ti-device-floppy"></i> Registrar Nota
            </button>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
const BASE = BASE_URL;
let t, buscarTimer;

const MOTIVOS = {
    credito: [
        { cod: '01', des: 'Anulación de la operación' },
        { cod: '02', des: 'Anulación por error en el RUC' },
        { cod: '03', des: 'Corrección por error en la descripción' },
        { cod: '04', des: 'Descuento global' },
        { cod: '05', des: 'Descuento por ítem' },
        { cod: '06', des: 'Devolución total' },
        { cod: '07', des: 'Devolución por ítem' },
        { cod: '08', des: 'Bonificación' },
        { cod: '09', des: 'Disminución en el valor' },
        { cod: '10', des: 'Otros Conceptos' },
    ],
    debito: [
        { cod: '01', des: 'Intereses por mora' },
        { cod: '02', des: 'Aumento en el valor' },
        { cod: '03', des: 'Penalidades / Otros conceptos' },
    ],
};

$(function () {
    t = $('#tbl').DataTable({
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/notas', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: [
            { data: 'documento' },
            {
                data: 'tipo_label', className: 'text-center',
                render: (v, _, r) => r.tipo === 'credito'
                    ? `<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700">${v}</span>`
                    : `<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-orange-100 text-orange-700">${v}</span>`,
            },
            { data: 'comprobante_afectado' },
            { data: 'motivo', defaultContent: '-' },
            { data: 'cliente_nombre' },
            { data: 'total', className: 'text-right', render: v => 'S/ ' + parseFloat(v).toFixed(2) },
            { data: 'fecha_emision', className: 'text-center', defaultContent: '-' },
            {
                data: 'enviado_sunat', className: 'text-center',
                render: v => v === '1'
                    ? `<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">Enviado</span>`
                    : `<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">Pendiente</span>`,
            },
            {
                data: 'estado', className: 'text-center',
                render: v => v === '1'
                    ? `<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">Activa</span>`
                    : `<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">Anulada</span>`,
            },
            {
                data: 'id_nota', orderable: false, className: 'text-center',
                render: (id, _, r) => {
                    const enviado = r.enviado_sunat === '1';
                    const anulada = r.estado === '0';
                    return `<div class="flex justify-center gap-1">
                        <a href="{{ url('/nota/electronica/pdf') }}/${id}" target="_blank"
                           class="h-7 w-7 flex items-center justify-center rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-600" title="Ver PDF">
                            <i class="ti ti-file-type-pdf text-sm"></i>
                        </a>
                        ${!enviado && !anulada ? `<button onclick="enviarSunat(${id})" class="h-7 px-2 flex items-center gap-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-medium"><i class="ti ti-send text-sm"></i> SUNAT</button>` : ''}
                        ${!enviado && !anulada ? `<button onclick="anular(${id})" class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-sm"></i></button>` : ''}
                    </div>`;
                },
            },
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        dom: '<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',
    });
    actualizarMotivos();
});

function abrirNueva() {
    document.getElementById('nIdVenta').value = '';
    document.getElementById('nBuscarTerm').value = '';
    document.getElementById('nVentaSel').classList.add('hidden');
    document.getElementById('nResultados').classList.add('hidden');
    document.getElementById('nItemsWrap').classList.add('hidden');
    document.getElementById('nItemsBody').innerHTML = '';
    document.getElementById('nTotal').textContent = '0.00';
    document.getElementById('nTotalVal').value = '0';
    document.getElementById('nTipo').value = 'credito';
    actualizarMotivos();
    document.getElementById('mdNota').classList.replace('hidden', 'flex');
}

function cerrar() {
    document.getElementById('mdNota').classList.replace('flex', 'hidden');
}

function actualizarMotivos() {
    const tipo = document.getElementById('nTipo').value;
    const sel  = document.getElementById('nMotivo');
    sel.innerHTML = MOTIVOS[tipo].map(m => `<option value="${m.cod}">${m.cod} - ${m.des}</option>`).join('');
}

function buscarVentas() {
    clearTimeout(buscarTimer);
    const term = document.getElementById('nBuscarTerm').value.trim();
    if (term.length < 2) { document.getElementById('nResultados').classList.add('hidden'); return; }
    buscarTimer = setTimeout(async () => {
        const resp = await fetch(`${BASE}/api/notas/buscar-venta?term=${encodeURIComponent(term)}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const data = await resp.json();
        const box  = document.getElementById('nResultados');
        if (!data.length) { box.innerHTML = '<p class="px-3 py-2 text-gray-400">Sin resultados.</p>'; box.classList.remove('hidden'); return; }
        box.innerHTML = data.map(v => `
            <div class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0"
                 onclick="seleccionarVenta(${v.id_venta},'${v.documento}','${v.cliente}',${v.total})">
                <span class="font-semibold text-gray-700">${v.documento}</span>
                <span class="text-gray-400 ml-2">${v.cliente}</span>
                <span class="float-right text-gray-600 font-medium">S/ ${parseFloat(v.total).toFixed(2)}</span>
            </div>`).join('');
        box.classList.remove('hidden');
    }, 300);
}

async function seleccionarVenta(id, doc, cliente, total) {
    document.getElementById('nResultados').classList.add('hidden');
    document.getElementById('nIdVenta').value = id;
    const ventaSel = document.getElementById('nVentaSel');
    ventaSel.textContent = `${doc} — ${cliente} — S/ ${parseFloat(total).toFixed(2)}`;
    ventaSel.classList.remove('hidden');

    const resp = await apiPost(BASE + '/api/notas/cargar-venta', { id_venta: id });
    const tbody = document.getElementById('nItemsBody');
    if (resp.productos && resp.productos.length) {
        tbody.innerHTML = resp.productos.map(p => `
            <tr class="border-t border-gray-100">
                <td class="px-3 py-1.5">${p.descripcion}</td>
                <td class="px-3 py-1.5 text-center">${parseFloat(p.cantidad).toFixed(3)}</td>
                <td class="px-3 py-1.5 text-right">S/ ${parseFloat(p.precio).toFixed(2)}</td>
                <td class="px-3 py-1.5 text-right">S/ ${parseFloat(p.total).toFixed(2)}</td>
            </tr>`).join('');
        document.getElementById('nItemsWrap').classList.remove('hidden');
    }
    document.getElementById('nTotal').textContent = parseFloat(total).toFixed(2);
    document.getElementById('nTotalVal').value = total;
}

async function guardar() {
    const idVenta = document.getElementById('nIdVenta').value;
    if (!idVenta) { toastWarn('Seleccioná un comprobante para afectar.'); return; }

    const tipo      = document.getElementById('nTipo').value;
    const motivoSel = document.getElementById('nMotivo');
    const codMotivo = motivoSel.value;
    const motivo    = motivoSel.options[motivoSel.selectedIndex].text.replace(/^\d+ - /, '');
    const total     = parseFloat(document.getElementById('nTotalVal').value);

    const d = await apiPost(BASE + '/api/notas/add', { id_venta: parseInt(idVenta), tipo, cod_motivo: codMotivo, motivo, total });
    if (d.res) { toastOk(d.msg); cerrar(); t.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error al registrar la nota.');
}

async function enviarSunat(id) {
    const { isConfirmed } = await Swal.fire({
        title: '¿Enviar a SUNAT?',
        text: 'El documento será firmado y enviado. Esta acción no se puede deshacer.',
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#059669', cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, enviar',
    });
    if (!isConfirmed) return;
    const d = await apiPost(BASE + '/api/notas/enviar-sunat', { id_nota: id });
    if (d.res) { toastOk(d.msg); t.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error al enviar a SUNAT.');
}

async function anular(id) {
    const { isConfirmed } = await Swal.fire({
        title: '¿Anular nota?', icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, anular',
    });
    if (!isConfirmed) return;
    const d = await apiPost(BASE + '/api/notas/anular', { id_nota: id });
    if (d.res) { toastOk(d.msg); t.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error al anular.');
}
</script>
@endpush

@extends('layouts.app')
@section('title','Arqueo Diario')
@section('page-title','Arqueo Diario')
@section('breadcrumb','Cajas / Arqueo Diario')
@section('content')
<div class="mb-5 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha</label>
        <input id="fechaArqueo" type="date" value="{{ date('Y-m-d') }}"
               class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>
    <button onclick="cargarArqueo()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition">
        <i class="ti ti-search"></i> Consultar
    </button>
</div>

<x-table id="tblArqueo" :search="false">
    <x-slot:thead>
        <x-th>Usuario</x-th>
        <x-th align="right">Efectivo</x-th>
        <x-th align="right">Bancos</x-th>
        <x-th align="right">Total</x-th>
        <x-th align="center">Acciones</x-th>
    </x-slot:thead>
</x-table>

<x-modal id="mdDetalle" title="Detalle de cobros" titleId="mdDetalleTitle" size="max-w-3xl">
    <div class="overflow-x-auto rounded-lg border border-gray-100">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-3 py-2 text-left">Cliente</th>
                    <th class="px-3 py-2 text-left">Documento</th>
                    <th class="px-3 py-2 text-left">Tipo pago</th>
                    <th class="px-3 py-2 text-right">Monto</th>
                    <th class="px-3 py-2 text-center">Fuente</th>
                </tr>
            </thead>
            <tbody id="detalleBody"></tbody>
        </table>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('mdDetalle')">Cerrar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection
@push('scripts')
<script>
const BASE = BASE_URL;
let tblArqueo = null;
let datosArqueo = [];

async function cargarArqueo() {
    const fecha = document.getElementById('fechaArqueo').value;
    if (!fecha) { toastWarn('Selecciona una fecha.'); return; }

    const body = $('#tblArqueo tbody');
    body.html('<tr><td colspan="5" class="text-center py-8"><i class="ti ti-loader-2 text-2xl text-blue-400 spin"></i></td></tr>');
    if (tblArqueo) { tblArqueo.destroy(); tblArqueo = null; }

    const data = await apiPost(BASE + '/api/arqueo/cobros-dia', { fecha });
    if (!data || !data.length) {
        body.html('<tr><td colspan="5" class="text-center py-8 text-gray-400"><i class="ti ti-inbox text-3xl block mb-2 text-gray-300"></i>No hay cobros registrados para esta fecha.</td></tr>');
        $('#tblArqueo tfoot').remove();
        return;
    }

    datosArqueo = data;
    renderizarTabla(data);
}

function renderizarTabla(data) {
    let totalEf = 0, totalBn = 0;

    const rows = data.map(v => {
        totalEf += parseFloat(v.efectivo || 0);
        totalBn += parseFloat(v.bancos || 0);
        return `<tr>
            <td class="font-medium">${v.usuario || 'Sin nombre'}</td>
            <td class="text-right text-emerald-600 font-semibold">S/ ${parseFloat(v.efectivo || 0).toFixed(2)}</td>
            <td class="text-right text-blue-600 font-semibold">S/ ${parseFloat(v.bancos || 0).toFixed(2)}</td>
            <td class="text-right font-bold">S/ ${parseFloat(v.total || 0).toFixed(2)}</td>
            <td class="text-center">
                <button onclick="verDetalle(${v.usuario_id})" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 px-2 py-1 text-xs font-medium transition">
                    <i class="ti ti-eye"></i> Ver detalle
                </button>
            </td>
        </tr>`;
    });

    $('#tblArqueo tbody').html(rows.join(''));

    const footHtml = `<tr class="bg-gray-100 font-bold text-sm">
        <td>TOTAL GENERAL</td>
        <td class="text-right text-emerald-600">S/ ${totalEf.toFixed(2)}</td>
        <td class="text-right text-blue-600">S/ ${totalBn.toFixed(2)}</td>
        <td class="text-right">S/ ${(totalEf + totalBn).toFixed(2)}</td>
        <td></td>
    </tr>`;

    if ($('#tblArqueo tfoot').length) {
        $('#tblArqueo tfoot').html(footHtml);
    } else {
        $('<tfoot>' + footHtml + '</tfoot>').appendTo('#tblArqueo');
    }

    tblArqueo = initDataTable('#tblArqueo', {
        processing: false, serverSide: false,
        searching: true, paging: true,
        order: [[0, 'asc']],
    });
}

function verDetalle(usuarioId) {
    const v = datosArqueo.find(d => d.usuario_id == usuarioId);
    if (!v) return;

    document.getElementById('mdDetalleTitle').textContent = 'Detalle de cobros — ' + (v.usuario || 'Sin nombre');

    const body = document.getElementById('detalleBody');
    if (!v.detalle || !v.detalle.length) {
        body.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-400">Sin detalle disponible.</td></tr>';
    } else {
        body.innerHTML = v.detalle.map(d => `<tr class="border-t border-gray-50">
            <td class="px-3 py-1.5">${d.cliente}</td>
            <td class="px-3 py-1.5">${d.documento}</td>
            <td class="px-3 py-1.5">${d.tipo_pago}</td>
            <td class="px-3 py-1.5 text-right font-medium">S/ ${parseFloat(d.monto || 0).toFixed(2)}</td>
            <td class="px-3 py-1.5 text-center"><span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium ${d.fuente === 'Venta' ? 'bg-purple-50 text-purple-600' : 'bg-amber-50 text-amber-600'}">${d.fuente}</span></td>
        </tr>`).join('');
    }

    abrirModal('mdDetalle');
}
</script>
@endpush

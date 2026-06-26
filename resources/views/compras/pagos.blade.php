@extends('layouts.app')
@section('title','Pagos / Cuentas por Pagar')
@section('page-title','Pagos / Cuentas por Pagar')
@section('breadcrumb','Pagos / Cuentas por Pagar')
@section('content')
<div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-4">
        <h3 class="text-sm font-semibold text-gray-700">Cuentas por Pagar</h3>
    </div>
    <div class="overflow-x-auto p-4">
        <table id="tbl" class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500"><tr>
                <th class="px-3 py-2.5 text-left">Tipo Doc.</th>
                <th class="px-3 py-2.5 text-left">Serie</th>
                <th class="px-3 py-2.5 text-left">Número</th>
                <th class="px-3 py-2.5 text-left">Proveedor</th>
                <th class="px-3 py-2.5 text-left">F. Emisión</th>
                <th class="px-3 py-2.5 text-left">F. Venc.</th>
                <th class="px-3 py-2.5 text-right">Total</th>
                <th class="px-3 py-2.5 text-right">Pagado</th>
                <th class="px-3 py-2.5 text-right">Saldo</th>
                <th class="px-3 py-2.5 text-center">Tipo</th>
                <th class="px-3 py-2.5 text-center">Estado</th>
                <th class="px-3 py-2.5 text-center">Acciones</th>
            </tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal Historial de Pagos --}}
<x-modal id="md-historial" title="Historial de Pagos" size="max-w-3xl">
    <input type="hidden" id="hist-compra-id">
    <div class="mb-4 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600">
        <div>Compra: <strong id="hist-doc"></strong></div>
        <div class="text-gray-400" id="hist-proveedor"></div>
    </div>
    <div class="mb-3 flex gap-4 text-xs">
        <div><span class="text-gray-400">Total:</span> <strong id="hist-total" class="text-gray-700"></strong></div>
        <div><span class="text-gray-400">Pagado:</span> <strong id="hist-pagado" class="text-emerald-600"></strong></div>
        <div><span class="text-gray-400">Saldo:</span> <strong id="hist-saldo" class="text-red-600"></strong></div>
    </div>
    <div class="overflow-x-auto rounded-lg border border-gray-100">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-500"><tr>
                <th class="px-3 py-2 text-left">#</th>
                <th class="px-3 py-2 text-left">Fecha</th>
                <th class="px-3 py-2 text-right">Monto</th>
                <th class="px-3 py-2 text-center">Método</th>
                <th class="px-3 py-2 text-center">Acciones</th>
            </tr></thead>
            <tbody id="hist-pagos-body"></tbody>
        </table>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-historial')">Cerrar</x-btn>
        <x-btn color="emerald" icon="ti ti-cash" onclick="abrirPago()">Registrar Pago</x-btn>
    </x-slot:footer>
</x-modal>

{{-- Modal Registrar / Editar Pago --}}
<x-modal id="md-pago" title="Registrar Pago" titleId="md-pago-title" size="max-w-lg">
    <input type="hidden" id="pago-compra-id">
    <input type="hidden" id="pago-dias-compra-id">
    <div class="space-y-3">
        <x-input-group label="Monto" :required="true">
            <input type="number" id="pago-monto" step="0.01" min="0.01" class="field" placeholder="0.00">
        </x-input-group>
        <x-input-group label="Fecha" :required="true">
            <input type="date" id="pago-fecha" class="field">
        </x-input-group>
        <div class="rounded-xl border border-gray-200 p-3">
            <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-gray-400">Método de pago</p>
            <div class="flex gap-2" id="metodo-pago-container">
                <button type="button" class="metodo-opt flex-1 rounded-lg border-2 p-2 text-center text-[11px] font-semibold transition"
                        data-tipo="EFECTIVO" onclick="seleccionarMetodo('EFECTIVO')">
                    <i class="ti ti-cash text-emerald-600 text-lg block mb-0.5"></i>
                    Efectivo
                </button>
                <button type="button" class="metodo-opt flex-1 rounded-lg border-2 p-2 text-center text-[11px] font-semibold transition"
                        data-tipo="TRANSFERENCIA" onclick="seleccionarMetodo('TRANSFERENCIA')">
                    <i class="ti ti-building-bank text-blue-600 text-lg block mb-0.5"></i>
                    Transferencia
                </button>
                <button type="button" class="metodo-opt flex-1 rounded-lg border-2 p-2 text-center text-[11px] font-semibold transition"
                        data-tipo="BILLETERA_DIGITAL" onclick="seleccionarMetodo('BILLETERA_DIGITAL')">
                    <i class="ti ti-device-mobile text-violet-600 text-lg block mb-0.5"></i>
                    Billetera
                </button>
            </div>
            <div id="metodo-subselect" class="mt-2 hidden">
                <x-label :required="true" id="metodo-subselect-label">Cuenta / Billetera</x-label>
                <select id="metodo-instrumento-id" class="field bg-white">
                    <option value="">— Selecciona —</option>
                </select>
            </div>
        </div>
        <div class="text-xs text-gray-400">
            Saldo pendiente: <strong id="pago-saldo-info" class="text-red-600"></strong>
        </div>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cancelarPago()">Cancelar</x-btn>
        <x-btn color="emerald" icon="ti ti-device-floppy" onclick="confirmarPago()">Guardar Pago</x-btn>
    </x-slot:footer>
</x-modal>
@endsection
@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let editando = false;
let metodoActual = 'EFECTIVO';

$(async function () {
    g('pago-fecha').value = new Date().toISOString().split('T')[0];

    $('#tbl').DataTable({
        processing: true, serverSide: true,
        ajax: { url: BASE + '/api/pagos', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        columns: [
            { data: 'tipo_doc', defaultContent: '-' },
            { data: 'serie', defaultContent: '-' },
            { data: 'numero', defaultContent: '-' },
            { data: 'proveedor_nombre', defaultContent: '-' },
            { data: 'fecha_emision', defaultContent: '-' },
            { data: 'fecha_vencimiento', defaultContent: '-' },
            { data: 'total', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
            { data: 'total_pagado', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
            { data: 'saldo_pendiente', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
            { data: 'tipo_pago_nombre', className: 'text-center', defaultContent: '-',
              render: v => v === 'Credito'
                ? '<span class="inline-block rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Crédito</span>'
                : '<span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">' + (v || '-') + '</span>' },
            { data: 'estado', className: 'text-center', orderable: false, searchable: false },
            { data: 'id_compra', orderable: false, searchable: false, className: 'text-center',
              render: id => `<button onclick="verHistorial(${id})" title="Historial" class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600"><i class="ti ti-history text-sm"></i></button>
                             <button onclick="registrarPago(${id})" title="Pagar" class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600"><i class="ti ti-cash text-sm"></i></button>` },
        ],
        order: [[4, 'desc']],
        pageLength: 25,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        dom: '<"flex flex-wrap gap-2 items-center justify-between mb-4"lf>t<"flex flex-wrap gap-2 items-center justify-between mt-4"ip>',
    });
});

function labelMetodo(tipo) {
    const map = { EFECTIVO: 'Efectivo', TRANSFERENCIA: 'Transferencia', BILLETERA_DIGITAL: 'Billetera digital' };
    return map[tipo] || tipo || '-';
}

async function verHistorial(id) {
    g('hist-compra-id').value = id;
    g('hist-pagos-body').innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-400">Cargando...</td></tr>';
    abrirModal('md-historial');

    const d = await apiGet(BASE + '/api/pagos/historial', { id_compra: id });
    if (!d.res) { toastErr('Error al cargar historial.'); return; }

    const c = d.compra;
    g('hist-doc').textContent = (c.serie || '') + '-' + (c.numero || '') || ('#' + id);
    g('hist-proveedor').textContent = c.proveedor_nombre || '-';
    g('hist-total').textContent = 'S/ ' + parseFloat(c.total || 0).toFixed(2);
    g('hist-pagado').textContent = 'S/ ' + parseFloat(d.total_pagado || 0).toFixed(2);
    g('hist-saldo').textContent = 'S/ ' + parseFloat(d.saldo_pendiente || 0).toFixed(2);

    if (d.pagos.length === 0) {
        g('hist-pagos-body').innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-400">Sin pagos registrados</td></tr>';
        return;
    }

    g('hist-pagos-body').innerHTML = d.pagos.map((p, i) => `
        <tr class="border-t border-gray-50">
            <td class="px-3 py-2">${i + 1}</td>
            <td class="px-3 py-2">${p.fecha}</td>
            <td class="px-3 py-2 text-right font-semibold text-emerald-600">S/ ${parseFloat(p.monto || 0).toFixed(2)}</td>
            <td class="px-3 py-2 text-center text-gray-500">${labelMetodo(p.instrumento_tipo)}</td>
            <td class="px-3 py-2 text-center">
                <button onclick="editarPago(${p.dias_compra_id}, ${c.id_compra})" title="Editar" class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600"><i class="ti ti-pencil text-sm"></i></button>
                <button onclick="anularPago(${p.dias_compra_id})" title="Anular" class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600"><i class="ti ti-trash text-sm"></i></button>
            </td>
        </tr>
    `).join('');
}

function registrarPago(id) {
    editando = false;
    g('pago-compra-id').value = id;
    g('pago-dias-compra-id').value = '';
    g('pago-monto').value = '';
    g('pago-fecha').value = new Date().toISOString().split('T')[0];
    g('md-pago-title').textContent = 'Registrar Pago';

    const row = $('#tbl').DataTable().rows().data().toArray().find(r => String(r.id_compra) === String(id));
    g('pago-saldo-info').textContent = 'S/ ' + (row ? parseFloat(row.saldo_pendiente || 0).toFixed(2) : '0.00');

    metodoActual = 'EFECTIVO';
    resaltarMetodo('EFECTIVO');
    ocultarSubselect();

    abrirModal('md-pago');
    setTimeout(() => g('pago-monto')?.focus(), 100);
}

function editarPago(diasCompraId, compraId) {
    editando = true;
    g('pago-compra-id').value = compraId;
    g('pago-dias-compra-id').value = diasCompraId;
    g('md-pago-title').textContent = 'Editar Pago';

    const d = $('#tbl').DataTable().rows().data().toArray().find(r => String(r.id_compra) === String(compraId));
    g('pago-saldo-info').textContent = 'S/ ' + (d ? parseFloat(d.saldo_pendiente || 0).toFixed(2) : '0.00');

    apiGet(BASE + '/api/pagos/historial', { id_compra: compraId }).then(resp => {
        if (!resp.res) return;
        const pago = resp.pagos.find(p => String(p.dias_compra_id) === String(diasCompraId));
        if (!pago) return;
        g('pago-monto').value = pago.monto;
        g('pago-fecha').value = pago.fecha;

        const tipo = pago.instrumento_tipo || 'EFECTIVO';
        metodoActual = tipo;
        resaltarMetodo(tipo);
        if (tipo !== 'EFECTIVO') {
            mostrarSubselect(tipo);
            const sel = g('metodo-instrumento-id');
            if (pago.instrumento_id) {
                setTimeout(() => { sel.value = pago.instrumento_id; }, 200);
            }
        } else {
            ocultarSubselect();
        }
    });

    abrirModal('md-pago');
}

function anularPago(diasCompraId) {
    Swal.fire({
        title: '¿Anular este pago?',
        text: 'El monto se devolverá al saldo pendiente.',
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, anular', cancelButtonText: 'Cancelar',
    }).then(async r => {
        if (!r.isConfirmed) return;
        const resp = await apiPost(BASE + '/api/pagos/eliminar', { dias_compra_id: diasCompraId });
        if (resp.res) {
            toastOk('Pago anulado correctamente.');
            cerrarModal('md-historial');
            $('#tbl').DataTable().ajax.reload(null, false);
        } else {
            toastErr(resp.msg || 'Error al anular.');
        }
    });
}

function abrirPago() {
    const id = g('hist-compra-id').value;
    if (id) registrarPago(id);
    else cerrarModal('md-pago');
}

function cancelarPago() {
    cerrarModal('md-pago');
}

function seleccionarMetodo(tipo) {
    metodoActual = tipo;
    resaltarMetodo(tipo);
    if (tipo === 'EFECTIVO') {
        ocultarSubselect();
    } else {
        mostrarSubselect(tipo);
    }
}

function resaltarMetodo(tipo) {
    document.querySelectorAll('.metodo-opt').forEach(btn => {
        const isSel = btn.dataset.tipo === tipo;
        btn.className = isSel
            ? 'metodo-opt flex-1 rounded-lg border-2 p-2 text-center text-[11px] font-semibold transition border-brand-500 bg-brand-50'
            : 'metodo-opt flex-1 rounded-lg border-2 p-2 text-center text-[11px] font-semibold transition border-gray-200 hover:border-gray-300 text-gray-500';
    });
}

function ocultarSubselect() {
    g('metodo-subselect').classList.add('hidden');
}

function mostrarSubselect(tipo) {
    const container = g('metodo-subselect');
    const sel = g('metodo-instrumento-id');
    const label = g('metodo-subselect-label');

    label.textContent = tipo === 'TRANSFERENCIA' ? 'Cuenta vinculada' : 'Billetera';
    sel.innerHTML = '<option value="">— Selecciona —</option>';
    sel.disabled = true;
    container.classList.remove('hidden');

    const endpoint = tipo === 'TRANSFERENCIA' ? 'cuentas' : 'billeteras';
    apiGet(BASE + '/api/pago-instrumento/' + endpoint).then(data => {
        sel.innerHTML = '<option value="">— Selecciona —</option>';
        data.forEach(op => {
            const id = op.id_cuenta ?? op.id_billetera;
            const label = op.banco
                ? `${op.banco} - ${op.tipo_cuenta ?? ''} ${op.numero_cuenta ?? ''}`
                : `${op.tipo} - ${op.titular}`;
            sel.innerHTML += `<option value="${id}">${label}</option>`;
        });
        sel.disabled = false;
    });
}

async function confirmarPago() {
    const id = parseInt(g('pago-compra-id').value);
    const diasCompraId = parseInt(g('pago-dias-compra-id').value) || 0;
    const monto = parseFloat(g('pago-monto').value);
    const fecha = g('pago-fecha').value;
    const instrumentoTipo = metodoActual || null;
    const instrumentoId = g('metodo-instrumento-id').value || null;

    if (!id) { toastWarn('Error: compra no identificada.'); return; }
    if (!monto || monto <= 0) { toastWarn('Ingresa un monto válido.'); g('pago-monto')?.focus(); return; }
    if (!fecha) { toastWarn('Selecciona una fecha.'); return; }
    if (instrumentoTipo && instrumentoTipo !== 'EFECTIVO' && !instrumentoId) {
        toastWarn('Selecciona la cuenta o billetera.'); return;
    }

    const payload = { id_compra: id, monto, fecha, instrumento_tipo: instrumentoTipo, instrumento_id: instrumentoId };

    let d;
    if (editando) {
        payload.dias_compra_id = diasCompraId;
        d = await apiPost(BASE + '/api/pagos/editar', payload);
    } else {
        d = await apiPost(BASE + '/api/pagos/registrar', payload);
    }

    if (d.res) {
        toastOk(editando ? 'Pago actualizado correctamente.' : 'Pago registrado correctamente.');
        cerrarModal('md-pago');
        cerrarModal('md-historial');
        $('#tbl').DataTable().ajax.reload(null, false);
    } else {
        Swal.fire({ icon: 'warning', title: 'No se pudo guardar', text: d.msg || 'Error.', confirmButtonColor: '#1d4ed8' });
    }
}
</script>
@endpush

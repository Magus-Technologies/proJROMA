@extends('layouts.app')
@section('title','Mi Caja')
@section('page-title','Mi Caja')
@section('breadcrumb','Cajas / Mi Caja')

@section('content')
<div id="mc-app" x-data="{
    idCaja: 0, nombre: 'Mi Caja', saldo: 0, cargando: true,
    esHija: false
}" x-init="cargarMiCaja()">

    <template x-if="cargando">
        <div class="flex items-center justify-center py-20">
            <div class="text-center">
                <i class="ti ti-loader text-4xl text-brand-500 animate-spin block mb-2"></i>
                <p class="text-xs text-gray-400">Cargando tu caja...</p>
            </div>
        </div>
    </template>

    <template x-if="!cargando && !idCaja">
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-8 text-center">
            <i class="ti ti-wallet-off text-5xl text-gray-300 mb-4 block"></i>
            <h2 class="text-xl font-bold text-gray-700 mb-2">No tienes caja asignada</h2>
            <p class="text-gray-400 text-sm">Solicita al administrador que te asigne una caja.</p>
        </div>
    </template>

    <template x-if="!cargando && idCaja">
        <div>
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-700" x-text="nombre"></h2>
                    <p class="text-xs text-gray-400">Saldo actual: <strong class="text-gray-700" x-text="'S/ ' + saldo.toFixed(2)"></strong></p>
                </div>
                <div class="flex gap-2">
                    <template x-if="esHija">
                        <x-btn color="primary" icon="ti ti-cash" @click="abrirApertura()">Aperturar caja</x-btn>
                    </template>
                    <x-btn color="emerald" icon="ti ti-arrow-up" onclick="abrirMiMovimiento('INGRESO')">Ingreso</x-btn>
                    <x-btn color="red" icon="ti ti-arrow-down" onclick="abrirMiMovimiento('EGRESO')">Egreso</x-btn>
                    <template x-if="esHija">
                        <x-btn color="primary" icon="ti ti-lock" @click="abrirCierre()">Cerrar caja</x-btn>
                    </template>
                </div>
            </div>

            <x-table id="tblMiCaja" title="Mis Movimientos">
                <x-slot:thead>
                    <x-th>Fecha</x-th>
                    <x-th align="center">Tipo</x-th>
                    <x-th>Categoría</x-th>
                    <x-th>Descripción</x-th>
                    <x-th>Instrumento</x-th>
                    <x-th align="right">Monto</x-th>
                    <x-th align="right">Saldo</x-th>
                    <x-th align="center">Acciones</x-th>
                </x-slot:thead>
            </x-table>

            <div class="mt-8">
                <h3 class="text-sm font-bold text-gray-700 mb-3">Historial de cierres</h3>
                <x-table id="tblCierresMC" title="Cierres">
                    <x-slot:thead>
                        <x-th>Fecha</x-th>
                        <x-th align="right">Declarado</x-th>
                        <x-th align="right">Sistema</x-th>
                        <x-th align="center">Estado</x-th>
                        <x-th>Observaciones</x-th>
                    </x-slot:thead>
                </x-table>
            </div>
        </div>
    </template>
</div>

{{-- Modal Movimiento --}}
<x-modal id="md-micaja" title="Movimiento Personal" size="max-w-lg">
    <input type="hidden" id="mc-tipo">
    <input type="hidden" id="mc-monto-id">
    <div class="space-y-4">
        <x-input-group label="Descripción" :required="true">
            <x-input id="mc-desc" maxlength="245" placeholder="Descripción del movimiento" />
        </x-input-group>
        <div class="grid grid-cols-2 gap-4">
            <x-input-group label="Monto (S/)" :required="true">
                <x-input id="mc-monto" type="number" step="0.01" min="0" placeholder="0.00" />
            </x-input-group>
            <x-input-group label="Fecha">
                <x-input id="mc-fecha" type="date" />
            </x-input-group>
        </div>
        <x-input-group label="Método de pago">
            <select id="mc-instr-tipo" @change="window.cargarInstrMC && window.cargarInstrMC()" class="field bg-white">
                <option value="">— Selecciona —</option>
                <option value="EFECTIVO">Efectivo</option>
                <option value="TRANSFERENCIA">Transferencia</option>
                <option value="BILLETERA_DIGITAL">Billetera digital</option>
            </select>
        </x-input-group>
            <select id="mc-instr-id" class="field bg-white hidden">
                <option value="">— Selecciona —</option>
            </select>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-micaja')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-device-floppy" onclick="guardarMiMovimiento()">Guardar</x-btn>
    </x-slot:footer>
</x-modal>

{{-- Modal Cierre --}}
<x-modal id="md-cierre" title="Cierre de Caja" size="max-w-md">
    <div class="space-y-4" id="md-cierre-body"></div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-cierre')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-lock" onclick="confirmarCierre()">Cerrar caja</x-btn>
    </x-slot:footer>
</x-modal>

{{-- Modal Apertura --}}
<x-modal id="md-apertura" title="Apertura de Caja" size="max-w-lg">
    <div class="space-y-4">
        <x-input-group label="Fecha">
            <x-input id="ap-fecha" type="date" />
        </x-input-group>

        <div>
            <p class="mb-2 text-xs font-bold text-gray-500">Desglose de billetes y monedas</p>
            <div class="overflow-hidden rounded-xl border border-gray-100">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Denominación</th>
                            <th class="px-3 py-2 text-center font-semibold">Cantidad</th>
                            <th class="px-3 py-2 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="ap-tbody"></tbody>
                    <tfoot>
                        <tr class="border-t border-gray-200 bg-gray-50">
                            <td class="px-3 py-2 font-bold text-gray-700" colspan="2">Total</td>
                            <td class="px-3 py-2 text-right font-bold text-brand-600" id="ap-total">S/ 0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <x-input-group label="Observaciones">
            <textarea id="ap-obs" rows="2" maxlength="500" class="field w-full" placeholder="Opcional"></textarea>
        </x-input-group>
    </div>
    <x-slot:footer>
        <x-btn color="ghost" onclick="cerrarModal('md-apertura')">Cancelar</x-btn>
        <x-btn color="primary" icon="ti ti-cash" onclick="confirmarApertura()">Aperturar</x-btn>
    </x-slot:footer>
</x-modal>
@endsection

@push('scripts')
<script>
const BASE=BASE_URL;
const g = id => document.getElementById(id);
let tblMC, tblCierresMC, miCajaId = 0;
let alpineCaja;

function cargarMiCaja() {
    const el = document.querySelector('#mc-app');
    if (!el) return;
    alpineCaja = Alpine.$data(el);
    apiGet(BASE + '/api/cajas/opciones').then(opts => {
        const cajas = opts.cajas || [];
        const usuarioId = {{ auth()->user()->usuario_id ?? 0 }};
        // Buscar por id_usuario_responsable. Si el responsable tiene varias cajas,
        // priorizar la caja chica (hija) que es la operativa para aperturar/cerrar.
        const misCajas = cajas.filter(c => c.id_usuario_responsable == usuarioId);
        let miCaja = misCajas.find(c => c.id_caja_padre)
                  || misCajas[0]
                  || cajas[0];
        if (miCaja) {
            miCajaId = miCaja.id;
            alpineCaja.idCaja = miCaja.id;
            alpineCaja.nombre = miCaja.nombre;
            alpineCaja.saldo = parseFloat(miCaja.saldo_actual || 0);
            alpineCaja.esHija = !!miCaja.id_caja_padre;
        }
        alpineCaja.cargando = false;

        if (miCajaId) {
            Alpine.nextTick(() => {
            tblMC = initDataTable('#tblMiCaja', {
                processing: true, serverSide: true,
                ajax: { url: BASE + '/api/caja-movimientos/' + miCajaId, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
                columns: [
                    { data: 'fecha', defaultContent: '-' },
                    { data: 'tipo', className: 'text-center', orderable: false,
                      render: v => v === 'INGRESO' ? '<span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700"><i class="ti ti-arrow-up"></i> Ingreso</span>' : '<span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700"><i class="ti ti-arrow-down"></i> Egreso</span>' },
                    { data: 'categoria', defaultContent: '-', className: 'text-xs' },
                    { data: 'descripcion', defaultContent: '-', responsivePriority: 1 },
                    { data: 'instrumento_tipo', defaultContent: 'EFECTIVO',
                      render: v => ({ EFECTIVO: 'Efectivo', TRANSFERENCIA: 'Transferencia', BILLETERA_DIGITAL: 'Billetera', CUENTA_BANCARIA: 'Cta bancaria', TARJETA: 'Tarjeta' })[v] || v },
                    { data: 'monto', className: 'text-right font-bold',
                      render: (v, t, row) => (row.tipo === 'INGRESO' ? '+ ' : '- ') + 'S/ ' + parseFloat(v || 0).toFixed(2) },
                    { data: 'saldo_posterior', className: 'text-right text-xs text-gray-500',
                      render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
                    { data: 'id', orderable: false, className: 'text-center',
                      render: (id, t, row) => row.estado === 'CONFIRMADO' ? `<div class="flex justify-center gap-1"><button onclick="editarMiMovimiento(${id})" class="h-6 w-6 flex items-center justify-center rounded bg-blue-50 hover:bg-blue-100 text-blue-500" title="Editar"><i class="ti ti-pencil text-[11px]"></i></button><button onclick="anularMiMovimiento(${id})" class="h-6 w-6 flex items-center justify-center rounded bg-red-50 hover:bg-red-100 text-red-500" title="Anular"><i class="ti ti-x text-[11px]"></i></button></div>` : '' },
                ],
                order: [[0, 'desc']],
            });

            tblCierresMC = initDataTable('#tblCierresMC', {
                processing: true, serverSide: true,
                ajax: { url: BASE + '/api/cierres/historial/' + miCajaId, headers: { 'Accept': 'application/json' } },
                columns: [
                    { data: 'fecha', defaultContent: '-' },
                    { data: 'saldo_declarado', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
                    { data: 'saldo_sistema', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
                    { data: 'estado', className: 'text-center',
                      render: v => ({
                        PENDIENTE: '<span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Pendiente</span>',
                        APROBADO: '<span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Aprobado</span>',
                        RECHAZADO: '<span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Rechazado</span>',
                      }[v] || v) },
                    { data: 'observaciones', defaultContent: '-', className: 'text-xs' },
                ],
                order: [[0, 'desc']],
            });
            });
        }
    });
}

function abrirMiMovimiento(tipo) {
    g('mc-monto-id').value = '';
    g('mc-tipo').value = tipo;
    g('mc-desc').value = '';
    g('mc-monto').value = '';
    g('mc-fecha').value = new Date().toISOString().split('T')[0];
    g('mc-instr-tipo').value = '';
    g('mc-instr-id').value = '';
    g('mc-instr-id').classList.add('hidden');
    abrirModal('md-micaja');
    setTimeout(() => g('mc-desc').focus(), 100);
}

function editarMiMovimiento(id) {
    const data = tblMC.rows().data().toArray().find(r => String(r.id) === String(id));
    if (!data) return;
    g('mc-monto-id').value = data.id;
    g('mc-tipo').value = data.tipo;
    g('mc-desc').value = data.descripcion || '';
    g('mc-monto').value = data.monto;
    g('mc-fecha').value = data.fecha;
    g('mc-instr-tipo').value = data.instrumento_tipo || '';
    window.cargarInstrMC().then(() => {
        g('mc-instr-id').value = data.instrumento_id || '';
    });
    abrirModal('md-micaja');
}

async function anularMiMovimiento(id) {
    const conf = await Swal.fire({ title: '¿Anular movimiento?', text: 'Se restaurará el saldo anterior.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, anular', cancelButtonText: 'Cancelar' });
    if (!conf.isConfirmed) return;
    const d = await apiPost(BASE + '/api/caja-movimientos/anular', { id });
    if (d.res) { toastOk('Movimiento anulado.'); tblMC.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}

window.cargarInstrMC = async function () {
    if (!miCajaId) return;
    const tipo = g('mc-instr-tipo').value;
    const selId = g('mc-instr-id');
    selId.innerHTML = '<option value="">— Selecciona —</option>';
    if (!tipo || tipo === 'EFECTIVO') { selId.classList.add('hidden'); return; }
    selId.classList.remove('hidden');
    const endpoint = tipo === 'TRANSFERENCIA' ? 'cuentas' : 'billeteras';
    const items = await apiGet(`${BASE}/api/pago-instrumento/${endpoint}`);
    items.forEach(it => {
        const id = it.id_cuenta ?? it.id_tarjeta ?? it.id_billetera;
        let label;
        if (it.banco) label = `${it.banco} - ${it.tipo_cuenta ?? it.tipo} ${it.numero_cuenta ?? ('*' + it.ultimos_4)}`;
        else if (it.cuenta_vinculada && it.cuenta_vinculada !== '-') label = `${it.tipo} - ${it.cuenta_vinculada}`;
        else label = `${it.tipo} - ${it.titular}`;
        selId.innerHTML += `<option value="${id}">${label}</option>`;
    });
};

async function guardarMiMovimiento() {
    const id = g('mc-monto-id').value;
    const tipo = g('mc-tipo').value;
    const desc = g('mc-desc').value.trim();
    const monto = parseFloat(g('mc-monto').value || 0);
    if (!desc) { toastWarn('Escribe una descripción.'); return; }
    if (monto <= 0) { toastWarn('Ingresa un monto válido.'); return; }

    let url, payload;
    if (id) {
        url = BASE + '/api/caja-movimientos/editar';
        payload = { id, descripcion: desc, monto, fecha: g('mc-fecha').value || undefined };
    } else {
        url = BASE + '/api/caja-movimientos';
        payload = {
            id_caja: miCajaId, tipo, descripcion: desc, monto,
            fecha: g('mc-fecha').value || undefined,
            categoria: 'MANUAL',
            instrumento_tipo: g('mc-instr-tipo').value || null,
            instrumento_id: g('mc-instr-id').value || null,
        };
    }

    const d = await apiPost(url, payload);
    if (d.res) { toastOk(id ? 'Movimiento actualizado.' : 'Movimiento registrado.'); cerrarModal('md-micaja'); tblMC.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}

function editarMiMovimiento(id) {
    toastWarn('Editar movimiento próximamente.');
}

// ── Cierre de caja ───────────────────────────────────────────────
async function abrirCierre() {
    if (!miCajaId) return;
    const d = await apiGet(BASE + '/api/cierres/balance/' + miCajaId);
    if (!d.res) { toastErr(d.msg || 'Error al obtener balance.'); return; }

    let html = '';
    if (d.desglose && d.desglose.length) {
        html += '<div class="mb-3 space-y-2 text-xs">';
        d.desglose.forEach(item => {
            html += '<div class="flex justify-between py-1 border-b border-gray-100">' +
                '<span class="text-gray-600">' + (item.label || '') + '</span>' +
                '<span class="font-bold">S/ ' + parseFloat(item.monto || 0).toFixed(2) + '</span></div>';
        });
        html += '<div class="flex justify-between py-1 text-sm font-bold border-t border-gray-300 mt-2">' +
            '<span>Saldo según sistema</span>' +
            '<span>S/ ' + parseFloat(d.saldo_sistema || 0).toFixed(2) + '</span></div>';
        html += '</div>';
    }
    html += '<label class="mb-1.5 block text-xs font-semibold text-gray-500">Saldo declarado (físico) <span class="text-red-400">*</span></label>' +
        '<input id="mc-saldo-declarado" type="number" step="0.01" min="0" placeholder="0.00" class="field w-full" />';

    g('md-cierre-body').innerHTML = html;
    g('mc-saldo-declarado').value = '';
    abrirModal('md-cierre');
}

async function confirmarCierre() {
    if (!miCajaId) return;
    const saldoDeclarado = parseFloat(g('mc-saldo-declarado').value || 0);
    if (saldoDeclarado < 0) { toastWarn('Ingresa un saldo válido.'); return; }

    const conf = await Swal.fire({
        title: '¿Cerrar caja?',
        text: 'Se registrará un cierre. Si hay diferencia se generará un ajuste.',
        icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, cerrar', cancelButtonText: 'Cancelar'
    });
    if (!conf.isConfirmed) return;

    const d = await apiPost(BASE + '/api/cierres/cerrar', {
        id_caja: miCajaId, saldo_declarado: saldoDeclarado, desglose: []
    });
    if (d.res) {
        toastOk('Cierre registrado.'); cerrarModal('md-cierre');
        if (tblCierresMC) tblCierresMC.ajax.reload(null, false);
    } else {
        toastErr(d.msg || 'Error.');
    }
}

// ── Apertura de caja (desglose de billetes y monedas) ────────────
const AP_DENOMS = [
    { tipo: 'BILLETE', val: 200 }, { tipo: 'BILLETE', val: 100 }, { tipo: 'BILLETE', val: 50 },
    { tipo: 'BILLETE', val: 20 },  { tipo: 'BILLETE', val: 10 },
    { tipo: 'MONEDA', val: 5 }, { tipo: 'MONEDA', val: 2 }, { tipo: 'MONEDA', val: 1 },
    { tipo: 'MONEDA', val: 0.5 }, { tipo: 'MONEDA', val: 0.2 }, { tipo: 'MONEDA', val: 0.1 },
];

function abrirApertura() {
    if (!miCajaId) return;
    g('ap-fecha').value = new Date().toISOString().split('T')[0];
    g('ap-obs').value = '';
    g('ap-tbody').innerHTML = AP_DENOMS.map((d, i) => {
        const badge = d.tipo === 'BILLETE'
            ? '<span class="rounded px-1.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-700">Billete</span>'
            : '<span class="rounded px-1.5 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-700">Moneda</span>';
        return `<tr class="border-t border-gray-100">
            <td class="px-3 py-1.5"><span class="inline-flex items-center gap-2">${badge} S/ ${d.val.toFixed(2)}</span></td>
            <td class="px-3 py-1.5 text-center"><input type="number" min="0" step="1" value="0" data-i="${i}" oninput="calcApertura()" class="field w-20 py-1 text-center" /></td>
            <td class="px-3 py-1.5 text-right font-semibold" id="ap-sub-${i}">S/ 0.00</td>
        </tr>`;
    }).join('');
    g('ap-total').textContent = 'S/ 0.00';
    abrirModal('md-apertura');
}

function calcApertura() {
    let total = 0;
    document.querySelectorAll('#ap-tbody input[data-i]').forEach(inp => {
        const i = parseInt(inp.dataset.i, 10);
        const cant = Math.max(0, parseInt(inp.value || 0, 10) || 0);
        const sub = cant * AP_DENOMS[i].val;
        total += sub;
        g('ap-sub-' + i).textContent = 'S/ ' + sub.toFixed(2);
    });
    g('ap-total').textContent = 'S/ ' + total.toFixed(2);
    return total;
}

async function confirmarApertura() {
    if (!miCajaId) return;
    const detalles = [];
    document.querySelectorAll('#ap-tbody input[data-i]').forEach(inp => {
        const i = parseInt(inp.dataset.i, 10);
        const cant = Math.max(0, parseInt(inp.value || 0, 10) || 0);
        if (cant > 0) detalles.push({ denominacion: AP_DENOMS[i].val, tipo: AP_DENOMS[i].tipo, cantidad: cant });
    });
    if (!detalles.length) { toastWarn('Ingresa la cantidad de al menos una denominación.'); return; }

    const total = calcApertura();
    const conf = await Swal.fire({
        title: '¿Aperturar caja?',
        html: 'Monto inicial: <b>S/ ' + total.toFixed(2) + '</b>',
        icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, aperturar', cancelButtonText: 'Cancelar'
    });
    if (!conf.isConfirmed) return;

    const d = await apiPost(BASE + '/api/aperturas/guardar', {
        id_caja: miCajaId,
        fecha: g('ap-fecha').value,
        detalles,
        observaciones: g('ap-obs').value || null,
    });
    if (d.res) {
        toastOk('Caja aperturada. Monto inicial: S/ ' + parseFloat(d.monto_total || 0).toFixed(2));
        cerrarModal('md-apertura');
    } else {
        toastErr(d.msg || 'Error al aperturar.');
    }
}
</script>
@endpush

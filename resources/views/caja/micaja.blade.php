@extends('layouts.app')
@section('title','Mi Caja')
@section('page-title','Mi Caja')
@section('breadcrumb','Cajas / Mi Caja')

@section('content')
<div x-data="{ idCaja: 0, nombre: 'Mi Caja', saldo: 0, cargando: true }"
     x-init="cargarMiCaja($data)">

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
                    <x-btn color="emerald" icon="ti ti-arrow-up" onclick="abrirMiMovimiento('INGRESO')">Ingreso</x-btn>
                    <x-btn color="red" icon="ti ti-arrow-down" onclick="abrirMiMovimiento('EGRESO')">Egreso</x-btn>
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
                </x-slot:thead>
            </x-table>
        </div>
    </template>
</div>

<x-modal id="md-micaja" title="Movimiento Personal" size="max-w-lg">
    <input type="hidden" id="mc-tipo">
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
                <option value="CUENTA_BANCARIA">Cuenta bancaria</option>
                <option value="TARJETA">Tarjeta</option>
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
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';
const g = id => document.getElementById(id);
let tblMC, miCajaId = 0;
let alpineCaja;

function cargarMiCaja(alpine) {
    alpineCaja = alpine;
    if (!alpine) return;
    apiGet(BASE + '/api/cajas/opciones').then(opts => {
        const cajas = opts.cajas || [];
        const usuarioId = {{ auth()->user()->usuario_id ?? 0 }};
        // Buscar caja tipo VENDEDOR asignada al usuario, o CHICA, o GENERAL
        let miCaja = cajas.find(c => c.tipo === 'VENDEDOR' && c.id_usuario_responsable == usuarioId)
                  || cajas.find(c => c.tipo === 'CHICA' && c.id_usuario_responsable == usuarioId)
                  || cajas.find(c => c.tipo === 'GENERAL');
        if (miCaja) {
            miCajaId = miCaja.id;
            alpine.idCaja = miCaja.id;
            alpine.nombre = miCaja.nombre;
        }
        alpine.cargando = false;

        if (miCajaId) {
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
                      render: v => ({ EFECTIVO: 'Efectivo', CUENTA_BANCARIA: 'Cta bancaria', TARJETA: 'Tarjeta', BILLETERA_DIGITAL: 'Billetera' })[v] || v },
                    { data: 'monto', className: 'text-right font-bold',
                      render: (v, t, row) => (row.tipo === 'INGRESO' ? '+ ' : '- ') + 'S/ ' + parseFloat(v || 0).toFixed(2) },
                    { data: 'saldo_posterior', className: 'text-right text-xs text-gray-500',
                      render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
                ],
                order: [[0, 'desc']],
            });
        }
    });
}

function abrirMiMovimiento(tipo) {
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

window.cargarInstrMC = async function () {
    if (!miCajaId) return;
    const tipo = g('mc-instr-tipo').value;
    const selId = g('mc-instr-id');
    selId.innerHTML = '<option value="">— Selecciona —</option>';
    if (!tipo || tipo === 'EFECTIVO') { selId.classList.add('hidden'); return; }
    selId.classList.remove('hidden');
    const endpoint = tipo === 'CUENTA_BANCARIA' ? 'cuentas' : tipo === 'TARJETA' ? 'tarjetas' : 'billeteras';
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
    const tipo = g('mc-tipo').value;
    const desc = g('mc-desc').value.trim();
    const monto = parseFloat(g('mc-monto').value || 0);
    if (!desc) { toastWarn('Escribe una descripción.'); return; }
    if (monto <= 0) { toastWarn('Ingresa un monto válido.'); return; }

    const payload = {
        id_caja: miCajaId, tipo, descripcion: desc, monto,
        fecha: g('mc-fecha').value || undefined,
        categoria: 'MANUAL',
        instrumento_tipo: g('mc-instr-tipo').value || null,
        instrumento_id: g('mc-instr-id').value || null,
    };

    const d = await apiPost(BASE + '/api/caja-movimientos', payload);
    if (d.res) { toastOk('Movimiento registrado.'); cerrarModal('md-micaja'); tblMC.ajax.reload(null, false); }
    else toastErr(d.msg || 'Error.');
}
</script>
@endpush

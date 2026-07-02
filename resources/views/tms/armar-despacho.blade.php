@extends('layouts.app')
@section('title','Armar Despacho')
@section('page-title','Armar Despacho')
@section('breadcrumb','TMS / Armar Despacho')

@section('content')
<div class="space-y-4">

    {{-- Filtros --}}
    <x-card>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <x-input-group label="Ruta" :required="true">
                <x-select id="ad-ruta" placeholder="— Selecciona ruta —" />
            </x-input-group>
            <x-input-group label="Fecha desde" :required="true">
                <x-input id="ad-desde" type="date" />
            </x-input-group>
            <x-input-group label="Fecha hasta" :required="true">
                <x-input id="ad-hasta" type="date" />
            </x-input-group>
            <div class="flex items-end">
                <x-btn color="primary" icon="ti ti-package-import" onclick="jalarPedidos()">Jalar pedidos</x-btn>
            </div>
        </div>
    </x-card>

    {{-- Resumen --}}
    <div id="ad-resumen" class="hidden grid grid-cols-2 gap-3 md:grid-cols-5">
        <x-card><div class="text-center"><p class="text-[10px] font-bold uppercase text-gray-400">Pedidos</p><p id="rs-pedidos" class="text-xl font-bold text-gray-700">0</p></div></x-card>
        <x-card><div class="text-center"><p class="text-[10px] font-bold uppercase text-gray-400">Puntos</p><p id="rs-puntos" class="text-xl font-bold text-gray-700">0</p></div></x-card>
        <x-card><div class="text-center"><p class="text-[10px] font-bold uppercase text-gray-400">Mercados</p><p id="rs-mercados" class="text-xl font-bold text-gray-700">0</p></div></x-card>
        <x-card><div class="text-center"><p class="text-[10px] font-bold uppercase text-gray-400">Peso total</p><p id="rs-peso" class="text-xl font-bold text-brand-600">0 kg</p></div></x-card>
        <x-card><div class="text-center"><p class="text-[10px] font-bold uppercase text-gray-400">Monto total</p><p id="rs-monto" class="text-xl font-bold text-emerald-600">S/ 0.00</p></div></x-card>
    </div>

    {{-- Pedidos --}}
    <x-card :padding="false">
        <div class="card-header">
            <h3 class="card-header__title">Pedidos a despachar</h3>
            <div class="flex items-center gap-2">
                <span id="ad-seleccion" class="text-xs text-gray-400">0 seleccionados · 0 kg</span>
            </div>
        </div>
        <div class="p-3 sm:p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-center w-8"><input type="checkbox" id="ad-check-all" onclick="toggleAll(this)"></th>
                            <th class="px-3 py-2 text-left">N°</th>
                            <th class="px-3 py-2 text-left">Cliente</th>
                            <th class="px-3 py-2 text-left">Mercado</th>
                            <th class="px-3 py-2 text-center">Fecha</th>
                            <th class="px-3 py-2 text-right">Monto</th>
                            <th class="px-3 py-2 text-right">Peso</th>
                        </tr>
                    </thead>
                    <tbody id="ad-tbody">
                        <tr><td colspan="7" class="py-8 text-center text-gray-400">Selecciona una ruta y fecha, luego "Jalar pedidos".</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>

    {{-- Asignación de vehículo / conductor --}}
    <x-card>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <x-input-group label="Vehículo" :required="true">
                <x-select id="ad-vehiculo" placeholder="— Selecciona vehículo —" />
            </x-input-group>
            <x-input-group label="Conductor" :required="true">
                <x-select id="ad-conductor" placeholder="— Selecciona conductor —" />
            </x-input-group>
            <x-input-group label="Fecha de reparto" :required="true">
                <x-input id="ad-fecha-reparto" type="date" />
            </x-input-group>
            <div class="flex items-end">
                <x-btn color="emerald" icon="ti ti-truck-delivery" onclick="crearDespacho()">Crear despacho</x-btn>
            </div>
        </div>
        <div class="mt-3">
            <x-input-group label="Observaciones">
                <x-input id="ad-obs" maxlength="255" placeholder="Opcional" />
            </x-input-group>
        </div>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
const BASE = BASE_URL;
const g = id => document.getElementById(id);
let adPedidos = [];

$(function () {
    const hoy = new Date().toISOString().split('T')[0];
    g('ad-desde').value = hoy;
    g('ad-hasta').value = hoy;
    g('ad-fecha-reparto').value = hoy;

    apiGet(BASE + '/api/tms/despachos/opciones').then(o => {
        fillSelect('ad-ruta', o.rutas || [], 'id', r => r.nombre, '— Selecciona ruta —');
        fillSelect('ad-conductor', o.conductores || [], 'id', c => c.nombres, '— Selecciona conductor —');
        fillSelect('ad-vehiculo', o.vehiculos || [], 'id', v => `${v.placa} · ${v.tipo} (${parseFloat(v.capacidad_kg).toFixed(0)} kg)`, '— Selecciona vehículo —');
    });
});

function fillSelect(id, items, valKey, labelFn, placeholder) {
    const sel = g(id);
    sel.innerHTML = `<option value="">${placeholder}</option>` +
        items.map(it => `<option value="${it[valKey]}">${labelFn(it)}</option>`).join('');
}

async function jalarPedidos() {
    const id_ruta = g('ad-ruta').value;
    const fecha_desde = g('ad-desde').value;
    const fecha_hasta = g('ad-hasta').value;
    if (!id_ruta) { toastWarn('Selecciona una ruta.'); return; }
    if (!fecha_desde || !fecha_hasta) { toastWarn('Selecciona el rango de fechas.'); return; }

    const d = await apiPost(BASE + '/api/tms/despachos/pedidos-pendientes', { id_ruta, fecha_desde, fecha_hasta });
    if (!d.res) { toastErr(d.msg || 'Error.'); return; }

    adPedidos = d.pedidos || [];
    g('ad-fecha-reparto').value = fecha_hasta;

    // Resumen
    g('ad-resumen').classList.remove('hidden');
    g('rs-pedidos').textContent = d.resumen.pedidos;
    g('rs-puntos').textContent = d.resumen.puntos;
    g('rs-mercados').textContent = d.resumen.mercados;
    g('rs-peso').textContent = parseFloat(d.resumen.peso_total).toFixed(2) + ' kg';
    g('rs-monto').textContent = 'S/ ' + parseFloat(d.resumen.monto_total).toFixed(2);

    // Vehículos sugeridos (que aguantan el peso)
    fillSelect('ad-vehiculo', d.vehiculos || [], 'id', v => `${v.placa} · ${v.tipo} (${parseFloat(v.capacidad_kg).toFixed(0)} kg)`, '— Selecciona vehículo —');

    renderPedidos();
}

function renderPedidos() {
    const tb = g('ad-tbody');
    if (!adPedidos.length) {
        tb.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-gray-400">No hay pedidos pendientes para esa ruta y fecha.</td></tr>';
        actualizarSeleccion();
        return;
    }
    tb.innerHTML = adPedidos.map(p => `
        <tr class="border-t border-gray-100 hover:bg-gray-50">
            <td class="px-3 py-1.5 text-center"><input type="checkbox" class="ad-chk" data-id="${p.cotizacion_id}" data-peso="${p.peso}" checked onchange="actualizarSeleccion()"></td>
            <td class="px-3 py-1.5">${p.numero ?? '-'}</td>
            <td class="px-3 py-1.5">${p.cliente ?? '-'}</td>
            <td class="px-3 py-1.5">${p.mercado ?? '-'}</td>
            <td class="px-3 py-1.5 text-center">${(p.fecha || '').split('T')[0]}</td>
            <td class="px-3 py-1.5 text-right">S/ ${parseFloat(p.total || 0).toFixed(2)}</td>
            <td class="px-3 py-1.5 text-right font-semibold">${parseFloat(p.peso || 0).toFixed(2)} kg</td>
        </tr>`).join('');
    g('ad-check-all').checked = true;
    actualizarSeleccion();
}

function toggleAll(el) {
    document.querySelectorAll('.ad-chk').forEach(c => c.checked = el.checked);
    actualizarSeleccion();
}

function pedidosSeleccionados() {
    return Array.from(document.querySelectorAll('.ad-chk:checked'));
}

function actualizarSeleccion() {
    const sel = pedidosSeleccionados();
    const peso = sel.reduce((s, c) => s + parseFloat(c.dataset.peso || 0), 0);
    g('ad-seleccion').textContent = `${sel.length} seleccionados · ${peso.toFixed(2)} kg`;
}

async function crearDespacho() {
    const id_ruta = g('ad-ruta').value;
    const id_vehiculo = g('ad-vehiculo').value;
    const id_conductor = g('ad-conductor').value;
    const fecha_reparto = g('ad-fecha-reparto').value;
    const pedidos = pedidosSeleccionados().map(c => parseInt(c.dataset.id, 10));

    if (!id_ruta) { toastWarn('Selecciona la ruta.'); return; }
    if (!pedidos.length) { toastWarn('Selecciona al menos un pedido.'); return; }
    if (!id_vehiculo) { toastWarn('Selecciona el vehículo.'); return; }
    if (!id_conductor) { toastWarn('Selecciona el conductor.'); return; }
    if (!fecha_reparto) { toastWarn('Indica la fecha de reparto.'); return; }

    const conf = await Swal.fire({
        title: '¿Crear despacho?',
        html: `${pedidos.length} pedidos serán asignados al vehículo seleccionado.`,
        icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, crear', cancelButtonText: 'Cancelar'
    });
    if (!conf.isConfirmed) return;

    const d = await apiPost(BASE + '/api/tms/despachos', {
        id_ruta, id_vehiculo, id_conductor, fecha_reparto,
        pedidos, observaciones: g('ad-obs').value || null
    });

    if (d.res) {
        if (d.excede_capacidad) {
            await Swal.fire({ icon: 'warning', title: 'Despacho creado', html: `⚠️ El peso (${parseFloat(d.peso_total).toFixed(2)} kg) <b>supera la capacidad</b> del vehículo.` });
        } else {
            toastOk('Despacho creado. Peso: ' + parseFloat(d.peso_total).toFixed(2) + ' kg');
        }
        window.location.href = BASE + '/tms/despachos';
    } else {
        toastErr(d.msg || 'Error al crear el despacho.');
    }
}
</script>
@endpush

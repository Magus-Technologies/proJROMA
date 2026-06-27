@extends('layouts.app')
@section('title','Cierres y Cuadre de Caja')
@section('page-title','Cierres y Cuadre')
@section('breadcrumb','Cajas / Cierres y Cuadre')

@section('content')
<div id="vueRendiciones" x-data="{
    modo: 'cuadre',
    idCaja: 0,
    idCajaPadre: 0,
    fecha: new Date().toISOString().split('T')[0],
    cajasPadres: [],
    cajasHijas: [],
    consolidado: null,
    balance: null,
    loading: false
}">

    <div class="mb-4 flex flex-nowrap gap-2 items-center">
        <select x-model="modo" @change="cambiarModo()" class="field bg-white text-xs w-40">
            <option value="cuadre">Cuadre (Principal)</option>
            <option value="cierre">Cierre (Hija)</option>
        </select>

        <template x-if="modo === 'cuadre'">
            <select x-model="idCajaPadre" id="slCajaPadre" class="field bg-white text-xs w-44">
                <option value="0">— Selecciona caja principal —</option>
            </select>
        </template>

        <template x-if="modo === 'cierre'">
            <select x-model="idCaja" id="slCajaHija" class="field bg-white text-xs w-44">
                <option value="0">— Selecciona caja hija —</option>
            </select>
        </template>

        <input type="date" x-model="fecha" class="field bg-white text-xs w-36">

        <x-btn color="primary" icon="ti ti-search" @click="consultar()">Consultar</x-btn>
    </div>

    {{-- Panel Cuadre (Caja Principal) --}}
    <template x-if="modo === 'cuadre' && consolidado">
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Consolidado del día <span x-text="fecha"></span></h3>
            <div class="grid grid-cols-3 gap-6 mb-6">
                <div class="rounded-xl bg-blue-50 p-4 text-center">
                    <p class="text-[10px] text-blue-500 font-semibold uppercase">Total declarado</p>
                    <p class="text-2xl font-bold text-blue-700" x-text="'S/ ' + consolidado.total_declarado.toFixed(2)"></p>
                </div>
                <div class="rounded-xl bg-gray-50 p-4 text-center">
                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Total sistema</p>
                    <p class="text-2xl font-bold text-gray-700" x-text="'S/ ' + consolidado.total_sistema.toFixed(2)"></p>
                </div>
                <div class="rounded-xl p-4 text-center"
                     :class="consolidado.diferencia >= 0 ? 'bg-emerald-50' : 'bg-red-50'">
                    <p class="text-[10px] font-semibold uppercase"
                       :class="consolidado.diferencia >= 0 ? 'text-emerald-500' : 'text-red-500'">Diferencia</p>
                    <p class="text-2xl font-bold"
                       :class="consolidado.diferencia >= 0 ? 'text-emerald-700' : 'text-red-700'"
                       x-text="'S/ ' + consolidado.diferencia.toFixed(2)"></p>
                </div>
            </div>

            <h4 class="text-xs font-semibold text-gray-600 mb-2">Cierres de cajas hijas</h4>
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-gray-500">
                        <th class="py-2 font-semibold">Caja</th>
                        <th class="py-2 font-semibold text-right">Declarado</th>
                        <th class="py-2 font-semibold text-right">Sistema</th>
                        <th class="py-2 font-semibold text-right">Diferencia</th>
                        <th class="py-2 font-semibold text-center">Estado</th>
                        <th class="py-2 font-semibold text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="c in consolidado.cierres" :key="c.id">
                        <tr class="border-b border-gray-100">
                            <td class="py-2" x-text="c.caja_nombre"></td>
                            <td class="py-2 text-right" x-text="'S/ ' + parseFloat(c.saldo_declarado).toFixed(2)"></td>
                            <td class="py-2 text-right" x-text="'S/ ' + parseFloat(c.saldo_sistema).toFixed(2)"></td>
                            <td class="py-2 text-right font-bold"
                                :class="parseFloat(c.saldo_declarado - c.saldo_sistema) >= 0 ? 'text-emerald-600' : 'text-red-600'"
                                x-text="'S/ ' + (c.saldo_declarado - c.saldo_sistema).toFixed(2)"></td>
                            <td class="py-2 text-center">
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold"
                                      :class="{
                                        'bg-amber-100 text-amber-700': c.estado === 'PENDIENTE',
                                        'bg-emerald-100 text-emerald-700': c.estado === 'APROBADO',
                                        'bg-red-100 text-red-700': c.estado === 'RECHAZADO'
                                      }" x-text="c.estado"></span>
                            </td>
                            <td class="py-2 text-center">
                                <template x-if="c.estado === 'PENDIENTE'">
                                    <div class="flex gap-1 justify-center">
                                        <x-btn color="emerald" size="xs" icon="ti ti-circle-check" @click="aprobarCierre(c.id, 'APROBADO')">Aprobar</x-btn>
                                        <x-btn color="red" size="xs" icon="ti ti-x" @click="aprobarCierre(c.id, 'RECHAZADO')">Rechazar</x-btn>
                                    </div>
                                </template>
                                <span x-show="c.estado !== 'PENDIENTE'" class="text-gray-400" x-text="c.observaciones || '-'"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!consolidado.cierres.length">
                        <td colspan="6" class="py-4 text-center text-gray-400">No hay cierres para esta fecha.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </template>

    {{-- Panel Cierre (Caja Hija) --}}
    <template x-if="modo === 'cierre' && idCaja && balance">
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Cierre de caja</h3>

            <div class="mb-4 space-y-3">
                <template x-for="item in balance.desglose" :key="item.key || item.label">
                    <div class="flex items-center justify-between py-1 text-xs border-b border-gray-100">
                        <span class="text-gray-600" x-text="item.label"></span>
                        <span class="font-bold" x-text="'S/ ' + item.monto.toFixed(2)"></span>
                    </div>
                </template>
                <div class="flex items-center justify-between py-1 text-sm font-bold border-t border-gray-300">
                    <span>Saldo según sistema</span>
                    <span x-text="'S/ ' + balance.saldo_sistema.toFixed(2)"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <x-input-group label="Saldo declarado (físico)" :required="true">
                    <x-input id="saldo-declarado" type="number" step="0.01" min="0" placeholder="0.00" />
                </x-input-group>
            </div>

            <x-btn color="primary" icon="ti ti-lock" @click="cerrarCaja()">Cerrar caja</x-btn>
        </div>
    </template>

    {{-- Historial de cierres --}}
    <x-table id="tblCierres" title="Historial de Cierres">
        <x-slot:thead>
            <x-th>Fecha</x-th>
            <x-th>Caja</x-th>
            <x-th align="right">Declarado</x-th>
            <x-th align="right">Sistema</x-th>
            <x-th align="center">Estado</x-th>
            <x-th>Cierra</x-th>
            <x-th>Aprueba</x-th>
            <x-th>Observaciones</x-th>
        </x-slot:thead>
    </x-table>
</div>
@endsection

@push('scripts')
<script>
const BASE=BASE_URL;
const g = id => document.getElementById(id);
let tblCierres;

function cargarCajasPadres() {
    apiGet(BASE + '/api/cajas/opciones').then(opts => {
        const cajas = opts.cajas || [];
        const a = ctrl.alpine;
        if (!a) return;
        a.cajasPadres = (() => {
            const f = cajas.filter(c => {
                const tieneHijas = cajas.some(h => h.id_caja_padre == c.id);
                return !c.id_caja_padre || tieneHijas;
            });
            return f.length ? f : cajas;
        })();
        a.cajasHijas = (() => {
            const f = cajas.filter(c => c.id_caja_padre);
            return f.length ? f : cajas;
        })();
        poblarSelect('slCajaPadre', a.cajasPadres, '— Selecciona caja principal —');
        poblarSelect('slCajaHija', a.cajasHijas, '— Selecciona caja hija —');
    });
}
function poblarSelect(id, items, placeholder) {
    const sl = document.getElementById(id);
    if (!sl) return;
    sl.innerHTML = '<option value="0">' + placeholder + '</option>'
        + items.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');
}

const ctrl = {
    get alpine() { const el = document.getElementById('vueRendiciones'); return el ? Alpine.$data(el) : null; },

    cambiarModo() {
        const a = this.alpine;
        if (!a) return;
        a.idCaja = 0;
        a.idCajaPadre = 0;
        a.consolidado = null;
        a.balance = null;
        setTimeout(() => {
            poblarSelect('slCajaPadre', a.cajasPadres, '— Selecciona caja principal —');
            poblarSelect('slCajaHija', a.cajasHijas, '— Selecciona caja hija —');
        }, 200);
    },

    async consultar() {
        const a = this.alpine;
        if (!a) return;
        a.consolidado = null;
        a.balance = null;

        if (a.modo === 'cuadre' && a.idCajaPadre) {
            a.loading = true;
            const d = await apiGet(`${BASE}/api/cierres/consolidado?id_caja_padre=${a.idCajaPadre}&fecha=${a.fecha}`);
            a.consolidado = d;
            a.loading = false;
        }

        if (a.modo === 'cierre' && a.idCaja) {
            a.loading = true;
            const d = await apiGet(`${BASE}/api/cierres/balance/${a.idCaja}`);
            a.balance = d;
            a.loading = false;
        }

        if (tblCierres) tblCierres.destroy();
        const cajaId = a.modo === 'cuadre' ? a.idCajaPadre : a.idCaja;
        if (cajaId) {
            tblCierres = initDataTable('#tblCierres', {
                processing: true, serverSide: true,
                ajax: { url: `${BASE}/api/cierres/historial/${cajaId}`, headers: { 'Accept': 'application/json' } },
                columns: [
                    { data: 'fecha', defaultContent: '-' },
                    { data: 'caja_nombre', defaultContent: '-' },
                    { data: 'saldo_declarado', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
                    { data: 'saldo_sistema', className: 'text-right', render: v => 'S/ ' + parseFloat(v || 0).toFixed(2) },
                    { data: 'estado', className: 'text-center',
                      render: v => ({
                        PENDIENTE: '<span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Pendiente</span>',
                        APROBADO: '<span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Aprobado</span>',
                        RECHAZADO: '<span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Rechazado</span>',
                      }[v] || v) },
                    { data: 'usuario_cierra', defaultContent: '-', className: 'text-xs' },
                    { data: 'usuario_aprueba', defaultContent: '-', className: 'text-xs' },
                    { data: 'observaciones', defaultContent: '-', className: 'text-xs' },
                ],
                order: [[0, 'desc']],
            });
        }
    },

    async cerrarCaja() {
        const a = this.alpine;
        if (!a || !a.idCaja) return;
        const saldoDeclarado = parseFloat(g('saldo-declarado').value || 0);
        if (saldoDeclarado < 0) { toastWarn('Ingresa un saldo válido.'); return; }

        const conf = await Swal.fire({
            title: '¿Cerrar caja?',
            text: 'Se registrará un cierre. Si hay diferencia se generará un ajuste automático.',
            icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, cerrar', cancelButtonText: 'Cancelar'
        });
        if (!conf.isConfirmed) return;

        const d = await apiPost(BASE + '/api/cierres/cerrar', {
            id_caja: a.idCaja, saldo_declarado: saldoDeclarado, desglose: []
        });
        if (d.res) { toastOk('Cierre registrado.'); this.consultar(); }
        else toastErr(d.msg || 'Error.');
    },

    async aprobarCierre(id, estado) {
        const accion = estado === 'APROBADO' ? 'aprobar' : 'rechazar';
        const conf = await Swal.fire({
            title: `¿${estado === 'APROBADO' ? 'Aprobar' : 'Rechazar'} cierre?`,
            input: 'textarea',
            inputLabel: 'Observaciones (opcional)',
            inputPlaceholder: 'Motivo...',
            icon: 'question', showCancelButton: true, confirmButtonText: `Sí, ${accion}`, cancelButtonText: 'Cancelar'
        });
        if (!conf.isConfirmed) return;

        const d = await apiPost(BASE + '/api/cierres/aprobar', { id, estado, observaciones: conf.value || null });
        if (d.res) { toastOk(`Cierre ${estado === 'APROBADO' ? 'aprobado' : 'rechazado'}.`); this.consultar(); }
        else toastErr(d.msg || 'Error.');
    }
};

// Exponer funciones al DOM
setTimeout(cargarCajasPadres, 100);

window.cambiarModo = ctrl.cambiarModo.bind(ctrl);
window.consultar = ctrl.consultar.bind(ctrl);
window.cerrarCaja = ctrl.cerrarCaja.bind(ctrl);
window.aprobarCierre = ctrl.aprobarCierre.bind(ctrl);
</script>
@endpush

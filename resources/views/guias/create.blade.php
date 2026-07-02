@extends('layouts.app')
@section('title','Nueva Guía de Remisión')
@section('page-title','Nueva Guía de Remisión')
@section('breadcrumb','Guías de Remisión / Nueva')

@section('content')
<div x-data="guiaApp()" x-init="init()">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- COLUMNA IZQUIERDA --}}
        <div class="xl:col-span-1 space-y-4">

            {{-- Venta vinculada --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Venta de Origen</h3>
                <template x-if="!ventaSeleccionada">
                    <div class="space-y-2">
                        <div class="relative">
                            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="inpVenta" type="text" placeholder="Buscar por N° venta o cliente..."
                                   @input.debounce.400ms="buscarVentas($event.target.value)"
                                   class="w-full rounded-lg border border-gray-200 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <ul x-show="ventasSugeridas.length>0" x-cloak class="absolute z-30 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 max-h-56 overflow-y-auto">
                                <template x-for="v in ventasSugeridas" :key="v.id_venta">
                                    <li @click="seleccionarVenta(v)" class="px-3 py-2 text-xs cursor-pointer hover:bg-blue-50 border-b border-gray-50 last:border-0">
                                        <p class="font-semibold text-gray-700" x-text="v.serie+'-'+String(v.numero).padStart(8,'0')"></p>
                                        <p class="text-gray-400" x-text="v.cliente_nombre"></p>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </template>
                <template x-if="ventaSeleccionada">
                    <div class="rounded-xl bg-blue-50 border border-blue-200 p-3">
                        <p class="text-xs font-bold text-blue-800" x-text="ventaSeleccionada.serie+'-'+String(ventaSeleccionada.numero).padStart(8,'0')"></p>
                        <p class="text-xs text-blue-600 mt-0.5" x-text="ventaSeleccionada.cliente_nombre"></p>
                        <p class="text-xs text-blue-500 mt-0.5" x-text="ventaSeleccionada.cliente_direccion||''"></p>
                        <button @click="limpiarVenta()" class="mt-2 text-[10px] text-red-500 hover:underline">
                            <i class="ti ti-refresh"></i> Cambiar venta
                        </button>
                    </div>
                </template>
            </div>

            {{-- Datos de la guía --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Datos de la Guía</h3>
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Serie</label>
                            <input type="text" value="T001" readonly class="w-full rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Número</label>
                            <input type="text" value="Automático" readonly class="w-full rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha de Emisión *</label>
                        <input x-model="guia.fecha_emision" type="date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Dirección de Llegada</label>
                        <input x-model="guia.dir_llegada" type="text" maxlength="245" placeholder="Av. Principal 123, Lima"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Ubigeo</label>
                        <input x-model="guia.ubigeo" type="text" maxlength="6" placeholder="150101"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Peso total (kg)</label>
                            <input x-model="guia.peso" type="number" min="0" step="0.01" placeholder="0.00"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">N° Bultos</label>
                            <input x-model="guia.nro_bultos" type="number" min="1" step="1" placeholder="1"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transporte --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Transporte</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo de Transporte *</label>
                        <select x-model="guia.tipo_transporte" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="1">Transporte Privado</option>
                            <option value="2">Transporte Público</option>
                        </select>
                    </div>
                    <div x-show="guia.tipo_transporte==='2'" x-transition>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">RUC Transportista</label>
                        <input x-model="guia.ruc_transporte" type="text" maxlength="11" placeholder="20123456789"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div x-show="guia.tipo_transporte==='2'" x-transition>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Razón Social Transportista</label>
                        <input x-model="guia.razon_transporte" type="text" maxlength="245"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Placa del Vehículo</label>
                        <input x-model="guia.vehiculo" type="text" maxlength="45" placeholder="ABC-123"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Brevete del Chofer</label>
                        <input x-model="guia.chofer_brevete" type="text" maxlength="45" placeholder="Q54378901"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5 space-y-3">
                <button @click="guardar()" :disabled="guardando"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed px-4 py-3 text-sm font-bold text-white transition shadow-lg shadow-blue-900/20">
                    <i class="ti ti-truck-delivery"></i>
                    <span x-text="guardando?'Guardando...':'Registrar Guía'"></span>
                </button>
                <a href="{{ url('/panel/guia-remisions') }}"
                   class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2.5 text-xs font-medium text-gray-600 transition">
                    <i class="ti ti-arrow-left text-xs"></i> Volver
                </a>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Productos --}}
        <div class="xl:col-span-2">
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Productos a Remitir</h3>
                        <span class="rounded-full bg-blue-100 text-blue-700 px-2.5 py-0.5 text-[10px] font-bold" x-text="productos.length+' item(s)'"></span>
                    </div>
                    <span class="text-[10px] text-gray-400">Cargados automáticamente desde la venta seleccionada</span>
                </div>

                <template x-if="!ventaSeleccionada">
                    <div class="py-16 text-center">
                        <i class="ti ti-truck text-4xl text-gray-200 block mb-3"></i>
                        <p class="text-sm text-gray-400">Primero seleccioná una venta de origen</p>
                    </div>
                </template>

                <template x-if="ventaSeleccionada && productos.length===0">
                    <div class="py-16 text-center">
                        <i class="ti ti-loader-2 text-4xl text-gray-200 block mb-3 animate-spin"></i>
                        <p class="text-sm text-gray-400">Cargando productos...</p>
                    </div>
                </template>

                <template x-if="productos.length>0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium">Descripción</th>
                                    <th class="px-3 py-3 text-center font-medium w-24">Cantidad</th>
                                    <th class="px-3 py-3 text-center font-medium w-24">Unidad</th>
                                    <th class="px-3 py-3 text-right font-medium w-28">Precio</th>
                                    <th class="px-3 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item,idx) in productos" :key="idx">
                                    <tr class="border-t border-gray-50 hover:bg-blue-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <input type="text" :value="item.detalles"
                                                   @change="productos[idx].detalles=$event.target.value"
                                                   class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <input type="number" :value="item.cantidad" min="1" step="1"
                                                   @change="productos[idx].cantidad=parseInt($event.target.value)||1"
                                                   class="w-20 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <select :value="item.unidad" @change="productos[idx].unidad=$event.target.value"
                                                    class="w-20 rounded-lg border border-gray-200 px-1 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                                                <option value="NIU">NIU</option>
                                                <option value="KGM">KGM</option>
                                                <option value="LTR">LTR</option>
                                                <option value="MTR">MTR</option>
                                                <option value="BLL">BLL</option>
                                                <option value="BX">BX</option>
                                                <option value="GLL">GLL</option>
                                                <option value="ZZ">ZZ</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-3 text-right font-medium text-gray-700" x-text="'S/ '+parseFloat(item.precio||0).toFixed(2)"></td>
                                        <td class="px-3 py-3 text-center">
                                            <button @click="productos.splice(idx,1)" class="text-red-400 hover:text-red-600 transition-colors">
                                                <i class="ti ti-x text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function guiaApp() {
    return {
        BASE: BASE_URL,
        guia: {
            fecha_emision:   new Date().toISOString().slice(0,10),
            dir_llegada:     '',
            ubigeo:          '',
            tipo_transporte: '1',
            ruc_transporte:  '',
            razon_transporte:'',
            vehiculo:        '',
            chofer_brevete:  '',
            peso:            '',
            nro_bultos:      '1',
            id_venta:        null,
        },
        ventaSeleccionada:  null,
        ventasSugeridas:    [],
        productos:          [],
        guardando:          false,

        async init() {},

        async buscarVentas(term) {
            if (!term || term.length < 2) { this.ventasSugeridas = []; return; }
            const d = await apiGet(this.BASE + '/api/guias/buscar-venta', {term});
            this.ventasSugeridas = Array.isArray(d) ? d : [];
        },

        async seleccionarVenta(v) {
            this.ventaSeleccionada = v;
            this.guia.id_venta    = v.id_venta;
            this.ventasSugeridas  = [];
            if (document.getElementById('inpVenta')) document.getElementById('inpVenta').value = '';

            if (v.cliente_direccion) this.guia.dir_llegada = v.cliente_direccion;

            const prods = await apiPost(this.BASE + '/api/guias/cargar-venta', {id_venta: v.id_venta});
            this.productos = Array.isArray(prods) ? prods : [];
        },

        limpiarVenta() {
            this.ventaSeleccionada = null;
            this.guia.id_venta     = null;
            this.productos         = [];
        },

        async guardar() {
            if (!this.guia.id_venta)       { toastWarn('Seleccioná una venta de origen.'); return; }
            if (!this.guia.fecha_emision)  { toastWarn('Ingresá la fecha de emisión.'); return; }
            if (!this.productos.length)    { toastWarn('La guía debe tener al menos un producto.'); return; }

            this.guardando = true;
            try {
                const payload = { ...this.guia, productos: this.productos };
                const data = await apiPost(this.BASE + '/api/guias/add', payload);
                if (data.res) {
                    await Swal.fire({title:'¡Guía registrada!', text:data.msg, icon:'success', confirmButtonColor:'#1d4ed8'});
                    window.location = this.BASE + '/guias/remision';
                } else {
                    toastErr(data.message || data.msg || 'Error al guardar.');
                }
            } catch(e) { toastErr('Error de conexión.'); }
            finally { this.guardando = false; }
        },
    };
}
</script>
@endpush

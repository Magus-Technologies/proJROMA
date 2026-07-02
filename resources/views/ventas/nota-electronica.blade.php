@extends('layouts.app')
@section('title','Nueva Nota Electrónica')
@section('page-title','Nueva Nota Electrónica')
@section('breadcrumb')
Ventas / <a href="{{ url('/panel/notas-electronicas') }}" class="hover:underline">Notas Electrónicas</a> / Nueva
@endsection
@section('content')

<div x-data="notaApp()" x-init="init()">
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

        {{-- ── COLUMNA IZQUIERDA: búsqueda + preview comprobante ── --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- Buscador --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Comprobante a Afectar</h3>
                <template x-if="!ventaSel">
                    <div class="relative">
                        <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" placeholder="Serie-número o nombre del cliente..."
                               x-model="buscarTerm"
                               @input.debounce.350ms="buscarVentas()"
                               class="w-full rounded-lg border border-gray-200 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <ul x-show="resultados.length > 0" x-cloak
                            class="absolute z-30 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 max-h-56 overflow-y-auto">
                            <template x-for="v in resultados" :key="v.id_venta">
                                <li @click="seleccionarVenta(v)"
                                    class="px-3 py-2.5 text-xs cursor-pointer hover:bg-blue-50 border-b border-gray-50 last:border-0">
                                    <p class="font-semibold text-gray-700" x-text="v.documento"></p>
                                    <p class="text-gray-400 mt-0.5" x-text="v.cliente + ' — S/ ' + parseFloat(v.total).toFixed(2)"></p>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
                <template x-if="ventaSel">
                    <div>
                        <div class="flex items-center justify-between rounded-xl bg-blue-50 border border-blue-200 px-3 py-2">
                            <div>
                                <p class="text-xs font-bold text-blue-800" x-text="ventaSel.documento"></p>
                                <p class="text-xs text-blue-600 mt-0.5" x-text="ventaSel.cliente"></p>
                            </div>
                            <button @click="limpiar()" class="text-[10px] text-red-500 hover:underline ml-3 shrink-0">
                                <i class="ti ti-refresh"></i> Cambiar
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Preview del comprobante original --}}
            <div x-show="ventaSel" x-cloak class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-100 px-4 py-3 flex items-center gap-2">
                    <i class="ti ti-file-description text-gray-400"></i>
                    <span class="text-xs font-semibold text-gray-600">Preview del comprobante</span>
                </div>
                <div class="p-4 text-[10px] text-gray-700 space-y-3 max-h-[520px] overflow-y-auto">

                    {{-- Header empresa + doc box --}}
                    <div class="flex justify-between gap-2">
                        <div class="flex-1">
                            <p class="font-bold text-xs text-red-600 leading-tight">{{ $empresa->razon_social ?? 'EMPRESA' }}</p>
                            <p class="text-gray-500 mt-1">{{ $empresa->direccion ?? '' }}</p>
                            @if($empresa->telefono ?? '')
                            <p class="text-gray-500">Telf: {{ $empresa->telefono }}</p>
                            @endif
                            <p class="text-gray-500">RUC: {{ $empresa->ruc ?? '-' }}</p>
                        </div>
                        <div class="border-2 border-gray-300 rounded text-center min-w-[120px]">
                            <p class="bg-gray-200 px-2 py-1 font-bold text-[9px]">R.U.C. {{ $empresa->ruc ?? '' }}</p>
                            <p class="px-2 py-1 font-bold text-[9px] uppercase" x-text="ventaSel?.tipo_doc || ''"></p>
                            <p class="px-2 py-1 font-bold border-t border-gray-200" x-text="ventaSel?.documento || ''"></p>
                        </div>
                    </div>

                    {{-- Datos cliente --}}
                    <div class="border border-gray-200 rounded p-2 space-y-1">
                        <div class="flex gap-2"><span class="font-bold w-16 shrink-0">Cliente:</span><span x-text="ventaSel?.cliente || '-'"></span></div>
                        <div class="flex gap-2"><span class="font-bold w-16 shrink-0">Total:</span><span class="font-bold text-gray-800" x-text="'S/ ' + parseFloat(ventaSel?.total || 0).toFixed(2)"></span></div>
                    </div>

                    {{-- Items --}}
                    <template x-if="productos.length">
                        <table class="w-full border-collapse text-[9px]">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-2 py-1 text-left border border-gray-200">Descripción</th>
                                    <th class="px-2 py-1 text-center border border-gray-200 w-12">Cant.</th>
                                    <th class="px-2 py-1 text-right border border-gray-200 w-16">P.Unit.</th>
                                    <th class="px-2 py-1 text-right border border-gray-200 w-16">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(p, i) in productos" :key="i">
                                    <tr :class="i % 2 === 0 ? '' : 'bg-gray-50'">
                                        <td class="px-2 py-1 border border-gray-100" x-text="p.descripcion"></td>
                                        <td class="px-2 py-1 border border-gray-100 text-center" x-text="parseFloat(p.cantidad).toFixed(2)"></td>
                                        <td class="px-2 py-1 border border-gray-100 text-right" x-text="'S/ ' + parseFloat(p.precio).toFixed(2)"></td>
                                        <td class="px-2 py-1 border border-gray-100 text-right font-semibold" x-text="'S/ ' + parseFloat(p.total).toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-200 font-bold">
                                    <td colspan="3" class="px-2 py-1 text-right border border-gray-300">TOTAL</td>
                                    <td class="px-2 py-1 text-right border border-gray-300" x-text="'S/ ' + parseFloat(ventaSel?.total || 0).toFixed(2)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </template>
                </div>
            </div>

        </div>{{-- /col izquierda --}}

        {{-- ── COLUMNA DERECHA: formulario nota ── --}}
        <div class="xl:col-span-3 space-y-4">

            {{-- Tipo + Motivo --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Datos de la Nota</h3>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Serie</label>
                        <input type="text" :value="nota.tipo === 'credito' ? 'EC01' : 'ED01'" readonly
                               class="w-full rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Número</label>
                        <input type="text" value="Automático" readonly
                               class="w-full rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo de Nota *</label>
                        <div class="flex gap-3">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" x-model="nota.tipo" value="credito" @change="actualizarMotivos()" class="sr-only">
                                <div :class="nota.tipo === 'credito' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                                     class="border-2 rounded-xl px-4 py-3 text-center transition">
                                    <i class="ti ti-file-minus text-lg block mb-1"></i>
                                    <span class="text-xs font-semibold">Nota de Crédito</span>
                                    <span class="text-[10px] block text-current opacity-70">EC01 · Tipo doc 07</span>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" x-model="nota.tipo" value="debito" @change="actualizarMotivos()" class="sr-only">
                                <div :class="nota.tipo === 'debito' ? 'border-orange-500 bg-orange-50 text-orange-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                                     class="border-2 rounded-xl px-4 py-3 text-center transition">
                                    <i class="ti ti-file-plus text-lg block mb-1"></i>
                                    <span class="text-xs font-semibold">Nota de Débito</span>
                                    <span class="text-[10px] block text-current opacity-70">ED01 · Tipo doc 08</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Motivo SUNAT *</label>
                        <select x-model="nota.cod_motivo" @change="nota.motivo = motivoTexto()"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <template x-for="m in motivos" :key="m.cod">
                                <option :value="m.cod" x-text="m.cod + ' — ' + m.des"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Items de la nota --}}
            <div x-show="productos.length > 0" x-cloak class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Ítems</h3>
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left">Descripción</th>
                            <th class="px-3 py-2 text-center w-20">Cant.</th>
                            <th class="px-3 py-2 text-right w-24">Precio</th>
                            <th class="px-3 py-2 text-right w-24">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(p, i) in productos" :key="i">
                            <tr :class="i % 2 === 0 ? '' : 'bg-gray-50'">
                                <td class="px-3 py-1.5" x-text="p.descripcion"></td>
                                <td class="px-3 py-1.5 text-center" x-text="parseFloat(p.cantidad).toFixed(3)"></td>
                                <td class="px-3 py-1.5 text-right" x-text="'S/ ' + parseFloat(p.precio).toFixed(2)"></td>
                                <td class="px-3 py-1.5 text-right font-semibold" x-text="'S/ ' + parseFloat(p.total).toFixed(2)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Total + guardar --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div class="rounded-xl bg-gray-50 border border-gray-200 px-5 py-3 text-right">
                        <div class="text-xs text-gray-500 mb-1">Total de la Nota</div>
                        <div class="text-2xl font-bold text-gray-800">S/ <span x-text="totalFormateado()"></span></div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ url('/panel/notas-electronicas') }}"
                           class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                            <i class="ti ti-arrow-left"></i> Cancelar
                        </a>
                        <button @click="guardar()" :disabled="!ventaSel || guardando"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 px-5 py-2.5 text-xs font-semibold text-white transition">
                            <i class="ti ti-device-floppy"></i>
                            <span x-text="guardando ? 'Guardando...' : 'Registrar Nota'"></span>
                        </button>
                    </div>
                </div>
            </div>

        </div>{{-- /col derecha --}}
    </div>
</div>

@endsection
@push('scripts')
<script>
const MOTIVOS_NC = [
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
];
const MOTIVOS_ND = [
    { cod: '01', des: 'Intereses por mora' },
    { cod: '02', des: 'Aumento en el valor' },
    { cod: '03', des: 'Penalidades / Otros conceptos' },
];

function notaApp() {
    return {
        buscarTerm: '',
        resultados: [],
        ventaSel:   null,
        productos:  [],
        nota: { tipo: 'credito', cod_motivo: '01', motivo: 'Anulación de la operación' },
        motivos: MOTIVOS_NC,
        guardando: false,

        init() { this.actualizarMotivos(); },

        async buscarVentas() {
            if (this.buscarTerm.length < 2) { this.resultados = []; return; }
            const resp = await fetch(`${BASE_URL}/api/notas/buscar-venta?term=${encodeURIComponent(this.buscarTerm)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
            });
            this.resultados = await resp.json();
        },

        async seleccionarVenta(v) {
            this.resultados = [];
            this.buscarTerm = '';
            this.ventaSel   = v;
            const resp = await apiPost(`${BASE_URL}/api/notas/cargar-venta`, { id_venta: v.id_venta });
            this.productos = resp.productos || [];
        },

        limpiar() {
            this.ventaSel  = null;
            this.productos = [];
            this.buscarTerm = '';
        },

        actualizarMotivos() {
            this.motivos = this.nota.tipo === 'credito' ? MOTIVOS_NC : MOTIVOS_ND;
            this.nota.cod_motivo = this.motivos[0].cod;
            this.nota.motivo     = this.motivos[0].des;
        },

        motivoTexto() {
            return (this.motivos.find(m => m.cod === this.nota.cod_motivo) || {}).des || '';
        },

        total() {
            return parseFloat(this.ventaSel?.total || 0);
        },

        totalFormateado() {
            return this.total().toFixed(2);
        },

        async guardar() {
            if (!this.ventaSel) { toastWarn('Seleccioná un comprobante primero.'); return; }
            this.guardando = true;
            try {
                const d = await apiPost(`${BASE_URL}/api/notas/add`, {
                    id_venta:   this.ventaSel.id_venta,
                    tipo:       this.nota.tipo,
                    cod_motivo: this.nota.cod_motivo,
                    motivo:     this.motivoTexto(),
                    total:      this.total(),
                });
                if (d.res) {
                    toastOk(d.msg);
                    setTimeout(() => { window.location.href = '{{ route("nota.electronica.lista") }}'; }, 1000);
                } else {
                    toastErr(d.msg || 'Error al registrar la nota.');
                }
            } finally {
                this.guardando = false;
            }
        },
    };
}
</script>
@endpush

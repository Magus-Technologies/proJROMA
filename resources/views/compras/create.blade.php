@extends('layouts.app')
@section('title', $compra ? 'Editar Compra' : 'Nueva Compra')
@section('page-title', $compra ? 'Editar Compra' : 'Nueva Compra')
@section('breadcrumb','Inventario / Compras')

@section('content')
<style>
    /* Inputs más compactos verticalmente — solo esta vista */
    #compra-form .field,
    #compra-form input[type="text"],
    #compra-form input[type="search"],
    #compra-form input[type="number"],
    #compra-form input[type="date"],
    #compra-form select { padding-top: .3rem !important; padding-bottom: .3rem !important; line-height: 1.2; }
    #compra-form label { margin-bottom: .15rem; }
    #compra-form .space-y-3 > * + * { margin-top: .5rem; }
</style>
<div x-data="compraForm()" id="compra-form">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- COLUMNA IZQUIERDA: Productos --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- Buscador --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Agregar Producto</h3>
                <div class="relative">
                    <i class="ti ti-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                    <input x-model="buscar" @input.debounce.300ms="search()" @focus="search()" type="search"
                           placeholder="Buscar por nombre o código…"
                           class="w-full rounded-lg border border-gray-200 pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <div x-show="resultados.length" @click.outside="resultados=[]" x-cloak
                         class="absolute z-30 mt-1 max-h-72 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-2xl">
                        <template x-for="p in resultados" :key="p.id_producto">
                            <button type="button" @click="agregar(p)"
                                    class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left text-xs hover:bg-gray-50 border-b border-gray-50 last:border-0">
                                <span class="font-semibold text-gray-700" x-text="p.descripcion"></span>
                                <span class="shrink-0 text-[10px] text-gray-400" x-text="'Costo: S/ ' + parseFloat(p.costo||0).toFixed(2)"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Detalle de productos --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Productos</h3>
                        <span class="rounded-full bg-gray-100 text-gray-600 px-2.5 py-0.5 text-[10px] font-bold" x-text="lineas.length + ' item(s)'"></span>
                    </div>
                    <button x-show="lineas.length" @click="lineas = []" class="text-xs text-red-500 hover:underline">Limpiar</button>
                </div>

                <template x-if="!lineas.length">
                    <div class="py-16 text-center">
                        <i class="ti ti-package-off text-4xl text-gray-200 block mb-3"></i>
                        <p class="text-sm text-gray-400">Busca y agrega productos a la compra</p>
                    </div>
                </template>

                <template x-if="lineas.length">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium">Producto</th>
                                    <th class="px-3 py-3 text-center font-medium w-28">Cantidad</th>
                                    <th class="px-3 py-3 text-center font-medium w-32">Costo unit.</th>
                                    <th class="px-3 py-3 text-right font-medium w-28">Subtotal</th>
                                    <th class="px-3 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(l, i) in lineas" :key="l.id_producto">
                                    <tr class="border-t border-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-800" x-text="l.descripcion"></td>
                                        <td class="px-3 py-3 text-center"><input type="number" min="0.01" step="0.01" x-model.number="l.cantidad" class="w-20 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-2 focus:ring-brand-400"></td>
                                        <td class="px-3 py-3 text-center"><input type="number" min="0" step="0.01" x-model.number="l.costo" class="w-28 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-2 focus:ring-brand-400"></td>
                                        <td class="px-3 py-3 text-right font-bold text-gray-800" x-text="'S/ ' + ((l.cantidad||0)*(l.costo||0)).toFixed(2)"></td>
                                        <td class="px-3 py-3 text-center"><button type="button" @click="quitar(i)" class="text-red-400 hover:text-red-600"><i class="ti ti-x text-sm"></i></button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Documento + Proveedor + Método + Total --}}
        <div class="xl:col-span-1 space-y-4">

            {{-- Documento --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Documento</h3>
                <div class="space-y-3">
                    <div>
                        <x-label :required="true">Tipo de documento</x-label>
                        <select x-model="tido" class="field bg-white">
                            <option value="">— Tipo —</option>
                            @foreach($tiposDoc as $d)
                                <option value="{{ $d->id_tido }}">{{ $d->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-label :optional="true">Tipo de pago</x-label>
                        <select x-model="tipoPago" class="field bg-white">
                            @foreach($tiposPago as $tp)
                                <option value="{{ $tp->tipo_pago_id }}">{{ $tp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-label :required="true">Fecha</x-label>
                        <input x-model="fecha" type="date" class="field">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-label :optional="true">Serie</x-label>
                            <input x-model="serie" type="text" maxlength="50" placeholder="F001" class="field">
                        </div>
                        <div>
                            <x-label :optional="true">Número</x-label>
                            <input x-model="numero" type="text" maxlength="50" placeholder="0001234" class="field">
                        </div>
                    </div>
                    <div>
                        <x-label :optional="true">Observación</x-label>
                        <input x-model="obs" type="text" maxlength="200" placeholder="Opcional" class="field">
                    </div>
                </div>
            </div>

            {{-- Proveedor --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Proveedor</h3>
                <x-label :required="true">Proveedor</x-label>
                <select x-model="prov" class="field bg-white">
                    <option value="">— Selecciona proveedor —</option>
                    @foreach($proveedores as $p)
                        <option value="{{ $p->proveedor_id }}">{{ $p->razon_social }} @if($p->ruc)· {{ $p->ruc }}@endif</option>
                    @endforeach
                </select>
            </div>

            {{-- Método de pago (abre modal) --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Método de pago</h3>
                <button type="button" @click="modalMetodo = true"
                        class="flex w-full items-center justify-between gap-3 rounded-xl border border-gray-200 hover:border-gray-300 p-2.5 text-left transition">
                    <span class="flex items-center gap-3 min-w-0">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg" :class="metodoSel ? metodoSel.bg : 'bg-gray-100'">
                            <i :class="metodoSel ? metodoSel.icon : 'ti ti-credit-card text-gray-400'" class="text-lg"></i>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-xs font-semibold text-gray-700 truncate" x-text="metodoSel ? metodoSel.label : 'Seleccionar método'"></span>
                            <span class="block text-[10px] text-gray-400 truncate" x-text="resumenMetodo"></span>
                        </span>
                    </span>
                    <i class="ti ti-chevron-right text-gray-400 shrink-0"></i>
                </button>
            </div>

            {{-- Total + Guardar --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total compra</span>
                    <span class="text-2xl font-extrabold text-gray-800" x-text="'S/ ' + total.toFixed(2)"></span>
                </div>
                <button @click="guardar()" :disabled="guardando"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 disabled:opacity-50 px-4 py-3 text-sm font-bold text-white transition">
                    <i class="ti ti-device-floppy"></i> Registrar Compra
                </button>
                <a href="{{ route('compras.index') }}"
                   class="mt-2 w-full inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2.5 text-xs font-medium text-gray-600 transition">
                    <i class="ti ti-arrow-left text-xs"></i> Cancelar
                </a>
            </div>
        </div>
    </div>

    {{-- Modal: Método de pago --}}
    <div x-show="modalMetodo" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="modalMetodo = false"></div>
        <div class="relative z-10 w-full max-w-md rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h4 class="text-sm font-semibold text-gray-700">Método de pago</h4>
                <button type="button" @click="modalMetodo = false" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <div class="p-5 space-y-2">
                <template x-for="m in metodos" :key="m.tipo">
                    <button type="button" @click="setMetodo(m.tipo)"
                            :class="instrTipo === m.tipo ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500' : 'border-gray-200 hover:border-gray-300'"
                            class="flex w-full items-center gap-3 rounded-xl border p-3 text-left transition">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg" :class="m.bg">
                            <i :class="m.icon" class="text-lg"></i>
                        </span>
                        <div>
                            <p class="text-xs font-semibold text-gray-700" x-text="m.label"></p>
                            <p class="text-[10px] text-gray-400" x-text="m.hint"></p>
                        </div>
                    </button>
                </template>
                <div x-show="instrTipo && instrTipo !== 'EFECTIVO'" x-cloak class="pt-1">
                    <x-label :required="true"><span x-text="instrTipo === 'TRANSFERENCIA' ? 'Cuenta vinculada' : 'Billetera'"></span></x-label>
                    <select x-model="instrId" class="field bg-white">
                        <option value="">— Selecciona —</option>
                        <template x-for="op in instrOptions" :key="op.id">
                            <option :value="op.id" x-text="op.label"></option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-100 px-5 py-4">
                <button type="button" @click="modalMetodo = false" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button type="button" @click="confirmarMetodo()" class="rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-semibold text-white">Listo</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const BASE=BASE_URL;

function compraForm() {
    return {
        id: '{{ $compra->id_compra ?? '' }}',
        prov: '{{ $compra->id_proveedor ?? '' }}',
        tido: '{{ $compra->id_tido ?? '' }}',
        tipoPago: '{{ $compra->id_tipo_pago ?? ($tiposPago->first()->tipo_pago_id ?? 1) }}',
        instrTipo: '{{ $compra->instrumento_tipo ?? '' }}',
        instrId: '{{ $compra->instrumento_id ?? '' }}',
        instrOptions: [],
        metodos: [
            { tipo: 'EFECTIVO',          label: 'Efectivo',         hint: 'Pago en caja',        icon: 'ti ti-cash text-emerald-600',       bg: 'bg-emerald-50' },
            { tipo: 'TRANSFERENCIA',     label: 'Transferencia',    hint: 'A cuenta bancaria',   icon: 'ti ti-building-bank text-blue-600', bg: 'bg-blue-50' },
            { tipo: 'BILLETERA_DIGITAL', label: 'Billetera digital', hint: 'Yape, Plin, etc.',   icon: 'ti ti-device-mobile text-violet-600', bg: 'bg-violet-50' },
        ],
        serie: @json($compra->serie ?? ''),
        numero: @json($compra->numero ?? ''),
        fecha: '{{ $compra ? substr($compra->fecha_emision ?? '', 0, 10) : now()->toDateString() }}',
        obs: @json($compra->direccion ?? ''),
        buscar: '', resultados: [], guardando: false,
        lineas: @json($items),
        modalMetodo: false,

        init() { if (this.instrTipo) this.cargarInstrumentos(); },

        get total() { return this.lineas.reduce((s, l) => s + ((l.cantidad || 0) * (l.costo || 0)), 0); },

        get metodoSel() { return this.metodos.find(m => m.tipo === this.instrTipo) || null; },

        get resumenMetodo() {
            if (!this.instrTipo) return 'Sin seleccionar';
            if (this.instrTipo === 'EFECTIVO') return this.metodoSel.hint;
            const op = this.instrOptions.find(o => String(o.id) === String(this.instrId));
            return op ? op.label : 'Selecciona cuenta / billetera';
        },

        confirmarMetodo() {
            if (this.instrTipo && this.instrTipo !== 'EFECTIVO' && !this.instrId) {
                toastWarn('Selecciona la cuenta o billetera vinculada.'); return;
            }
            this.modalMetodo = false;
        },

        setMetodo(tipo) {
            this.instrTipo = (this.instrTipo === tipo) ? '' : tipo;
            this.instrId = '';
            this.cargarInstrumentos();
        },

        async cargarInstrumentos() {
            if (!this.instrTipo || this.instrTipo === 'EFECTIVO') { this.instrOptions = []; return; }
            const endpoint = this.instrTipo === 'TRANSFERENCIA' ? 'cuentas' : 'billeteras';
            this.instrOptions = await apiGet(`${BASE}/api/pago-instrumento/${endpoint}`);
            this.instrOptions = this.instrOptions.map(o => ({
                id: o.id_cuenta ?? o.id_billetera,
                label: o.banco ? `${o.banco} - ${o.tipo_cuenta ?? ''} ${o.numero_cuenta ?? ''}` : `${o.tipo} - ${o.titular}`,
            }));
        },

        async search() {
            if (this.buscar.trim().length < 2) { this.resultados = []; return; }
            this.resultados = await apiGet(`${BASE}/api/ventas/cargar/productos`, { term: this.buscar.trim() });
        },

        agregar(p) {
            if (!this.lineas.find(l => l.id_producto === p.id_producto)) {
                this.lineas.push({ id_producto: p.id_producto, descripcion: p.descripcion, cantidad: 1, costo: parseFloat(p.costo || 0) });
            }
            this.buscar = ''; this.resultados = [];
        },

        quitar(i) { this.lineas.splice(i, 1); },

        async guardar() {
            if (!this.prov) { toastWarn('Selecciona un proveedor.'); return; }
            if (!this.tido) { toastWarn('Selecciona el tipo de documento.'); return; }
            if (!this.lineas.length) { toastWarn('Agrega al menos un producto.'); return; }
            if (this.lineas.some(l => !l.cantidad || l.cantidad <= 0)) { toastWarn('Revisa las cantidades.'); return; }

            this.guardando = true;
            const payload = {
                id_proveedor: this.prov, id_tido: this.tido, id_tipo_pago: this.tipoPago,
                instrumento_tipo: this.instrTipo || null,
                instrumento_id: this.instrId || null,
                fecha: this.fecha, serie: this.serie, numero: this.numero, observacion: this.obs,
                total: this.total, productos: this.lineas,
            };
            if (this.id) payload.id_compra = this.id;
            const url = this.id ? `${BASE}/api/compras/editar` : `${BASE}/api/compras`;
            const d = await apiPost(url, payload);
            this.guardando = false;
            if (d.res) { toastOk(this.id ? 'Compra actualizada.' : 'Compra registrada. Queda pendiente de recepción.'); window.location = `${BASE}/compras`; }
            else toastErr(d.msg || 'Error al guardar la compra.');
        },
    };
}
</script>
@endpush

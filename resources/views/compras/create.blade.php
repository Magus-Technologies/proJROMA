@extends('layouts.app')
@section('title', $compra ? 'Editar Compra' : 'Nueva Compra')
@section('page-title', $compra ? 'Editar Compra' : 'Nueva Compra')
@section('breadcrumb','Inventario / Compras')

@section('content')
<div x-data="compraForm()">

    {{-- Cabecera --}}
    <x-card class="mb-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-label :required="true">Proveedor</x-label>
                <select x-model="prov" class="field bg-white">
                    <option value="">— Selecciona proveedor —</option>
                    @foreach($proveedores as $p)
                        <option value="{{ $p->proveedor_id }}">{{ $p->razon_social }} @if($p->ruc)· {{ $p->ruc }}@endif</option>
                    @endforeach
                </select>
            </div>
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
                <x-label :optional="true">Serie</x-label>
                <input x-model="serie" type="text" maxlength="50" placeholder="F001" class="field">
            </div>
            <div>
                <x-label :optional="true">Número</x-label>
                <input x-model="numero" type="text" maxlength="50" placeholder="0001234" class="field">
            </div>
            <div>
                <x-label :required="true">Fecha</x-label>
                <input x-model="fecha" type="date" class="field">
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
                <x-label :optional="true">Método de pago</x-label>
                <div class="flex gap-2">
                    <select x-model="instrTipo" @change="cargarInstrumentos()" class="field bg-white">
                        <option value="">— Selecciona —</option>
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="CUENTA_BANCARIA">Cuenta bancaria</option>
                        <option value="TARJETA">Tarjeta</option>
                        <option value="BILLETERA_DIGITAL">Billetera digital</option>
                    </select>
                    <select x-show="instrTipo && instrTipo !== 'EFECTIVO'" x-model="instrId" class="field bg-white">
                        <option value="">— Selecciona —</option>
                        <template x-for="op in instrOptions" :key="op.id">
                            <option :value="op.id" x-text="op.label"></option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <x-label :optional="true">Observación</x-label>
                <input x-model="obs" type="text" maxlength="200" placeholder="Opcional" class="field">
            </div>
        </div>
    </x-card>

    {{-- Buscador de productos + líneas --}}
    <x-card class="mb-4">
        <div class="relative">
            <x-label :optional="true">Agregar producto</x-label>
            <div class="relative">
                <i class="ti ti-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                <input x-model="buscar" @input.debounce.300ms="search()" @focus="search()" type="search"
                       placeholder="Buscar por nombre o código…" class="search-input">
            </div>
            <div x-show="resultados.length" @click.outside="resultados=[]" x-cloak
                 class="absolute z-20 mt-1 max-h-64 w-full overflow-y-auto rounded-xl border border-gray-100 bg-white shadow-xl">
                <template x-for="p in resultados" :key="p.id_producto">
                    <button type="button" @click="agregar(p)"
                            class="flex w-full items-center justify-between gap-3 px-4 py-2 text-left text-xs hover:bg-gray-50">
                        <span x-text="p.descripcion"></span>
                        <span class="shrink-0 text-[10px] text-gray-400" x-text="'Costo: S/ ' + parseFloat(p.costo||0).toFixed(2)"></span>
                    </button>
                </template>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto rounded-lg border border-gray-100">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="px-3 py-2 text-left">Producto</th>
                        <th class="px-3 py-2 text-center w-28">Cantidad</th>
                        <th class="px-3 py-2 text-center w-32">Costo unit.</th>
                        <th class="px-3 py-2 text-right w-32">Subtotal</th>
                        <th class="px-3 py-2 text-center w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(l, i) in lineas" :key="l.id_producto">
                        <tr class="border-t border-gray-50">
                            <td class="px-3 py-2" x-text="l.descripcion"></td>
                            <td class="px-3 py-2 text-center"><input type="number" min="0.01" step="0.01" x-model.number="l.cantidad" class="field text-center"></td>
                            <td class="px-3 py-2 text-center"><input type="number" min="0" step="0.01" x-model.number="l.costo" class="field text-center"></td>
                            <td class="px-3 py-2 text-right font-semibold" x-text="'S/ ' + ((l.cantidad||0)*(l.costo||0)).toFixed(2)"></td>
                            <td class="px-3 py-2 text-center"><button type="button" @click="quitar(i)" class="text-red-500 hover:text-red-700"><i class="ti ti-trash"></i></button></td>
                        </tr>
                    </template>
                    <tr x-show="!lineas.length"><td colspan="5" class="px-3 py-6 text-center text-gray-400">Agrega productos a la compra</td></tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <div class="text-sm">
                <span class="text-gray-500">Total:</span>
                <span class="ml-2 text-xl font-bold text-gray-800" x-text="'S/ ' + total.toFixed(2)"></span>
            </div>
            <div class="flex gap-2">
                <a href="{{ config('app.url') }}/compras" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">Cancelar</a>
                <button @click="guardar()" :disabled="guardando"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2 text-xs font-semibold text-white disabled:opacity-50">
                    <i class="ti ti-device-floppy"></i> Registrar Compra
                </button>
            </div>
        </div>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
const BASE = '{{ config("app.url") }}';

function compraForm() {
    return {
        id: '{{ $compra->id_compra ?? '' }}',
        prov: '{{ $compra->id_proveedor ?? '' }}',
        tido: '{{ $compra->id_tido ?? '' }}',
        tipoPago: '{{ $compra->id_tipo_pago ?? ($tiposPago->first()->tipo_pago_id ?? 1) }}',
        instrTipo: '{{ $compra->instrumento_tipo ?? '' }}',
        instrId: '{{ $compra->instrumento_id ?? '' }}',
        instrOptions: [],
        serie: @json($compra->serie ?? ''),
        numero: @json($compra->numero ?? ''),
        fecha: '{{ $compra ? substr($compra->fecha_emision ?? '', 0, 10) : now()->toDateString() }}',
        obs: @json($compra->direccion ?? ''),
        buscar: '', resultados: [], guardando: false,
        lineas: @json($items),

        init() { if (this.instrTipo) this.cargarInstrumentos(); },

        get total() { return this.lineas.reduce((s, l) => s + ((l.cantidad || 0) * (l.costo || 0)), 0); },

        async cargarInstrumentos() {
            this.instrId = '';
            if (!this.instrTipo || this.instrTipo === 'EFECTIVO') { this.instrOptions = []; return; }
            const endpoint = this.instrTipo === 'CUENTA_BANCARIA' ? 'cuentas' : this.instrTipo === 'TARJETA' ? 'tarjetas' : 'billeteras';
            this.instrOptions = await apiGet(`${BASE}/api/pago-instrumento/${endpoint}`);
            this.instrOptions = this.instrOptions.map(o => ({
                id: o.id_cuenta ?? o.id_tarjeta ?? o.id_billetera,
                label: o.banco ? `${o.banco} - ${o.tipo_cuenta ?? o.tipo} ${o.numero_cuenta ?? ('*' + o.ultimos_4)}` : `${o.tipo} - ${o.titular}`,
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

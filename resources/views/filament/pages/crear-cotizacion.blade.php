<div x-data="cotiApp()" x-init="init()">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- LEFT COLUMN --}}
        <div class="xl:col-span-1 space-y-4">

            {{-- Document type --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Comprobante</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Tipo de Documento *</label>
                        <select x-model="coti.id_tido" @change="seleccionarTido()"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">-- Selecciona --</option>
                            <template x-for="d in documentos" :key="d.id_tido">
                                <option :value="d.id_tido" x-text="d.tipo_doc+' — '+d.serie"></option>
                            </template>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Serie</label>
                            <input x-model="coti.serie" type="text" readonly
                                   class="w-full rounded-lg border border-gray-100 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-gray-300 px-3 py-2 text-sm text-gray-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Número</label>
                            <input x-model="coti.numero" type="text" readonly
                                   class="w-full rounded-lg border border-gray-100 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-gray-300 px-3 py-2 text-sm text-gray-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Fecha Emisión *</label>
                        <input x-model="coti.fecha" type="date"
                               class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Forma de Pago *</label>
                        <select x-model="coti.id_tipo_pago"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="1">Contado</option>
                            <option value="2">Crédito</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Observación</label>
                        <input x-model="coti.observacion" type="text" maxlength="220" placeholder="Opcional"
                               class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>
            </div>

            {{-- Client --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Cliente</h3>
                <div class="space-y-3">
                    <template x-if="!clienteSeleccionado">
                        <div class="relative">
                            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="inpCliente" type="text" placeholder="Buscar por nombre o documento..."
                                   @input.debounce.400ms="buscarClientes($event.target.value)"
                                   class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <ul x-show="sugerencias.length>0" x-cloak
                                class="absolute z-30 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl mt-1 max-h-52 overflow-y-auto">
                                <template x-for="c in sugerencias" :key="c.id_cliente">
                                    <li @click="seleccionarCliente(c)"
                                        class="flex justify-between px-3 py-2 text-xs cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 border-b border-gray-50 dark:border-gray-700 last:border-0">
                                        <span class="font-semibold text-gray-700 dark:text-gray-200" x-text="c.datos"></span>
                                        <span class="text-gray-400" x-text="c.documento"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>
                    <template x-if="clienteSeleccionado">
                        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-3">
                            <p class="text-xs font-bold text-blue-800 dark:text-blue-300" x-text="clienteSeleccionado.datos"></p>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5" x-text="clienteSeleccionado.documento"></p>
                            <p class="text-xs text-blue-500 mt-0.5" x-text="clienteSeleccionado.direccion||''"></p>
                            <button @click="clienteSeleccionado=null;coti.id_cliente=null"
                                    class="mt-2 text-[10px] text-red-500 hover:underline">
                                <i class="ti ti-refresh"></i> Cambiar cliente
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Summary + Save --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Resumen</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal:</span>
                        <span class="font-semibold" x-text="'S/ '+subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">IGV (18%):</span>
                        <span class="font-semibold" x-text="'S/ '+igvMonto.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-gray-100 dark:border-gray-700 pt-2 mt-1">
                        <span class="font-bold text-gray-700 dark:text-gray-200">TOTAL:</span>
                        <span class="font-extrabold text-blue-700 text-lg" x-text="'S/ '+totalFinal.toFixed(2)"></span>
                    </div>
                </div>
                <button @click="guardarCotizacion()" :disabled="guardando"
                        class="mt-5 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed px-4 py-3 text-sm font-bold text-white transition shadow-lg shadow-blue-900/20">
                    <i class="ti ti-device-floppy" :class="{'animate-spin':guardando}"></i>
                    <span x-text="guardando?'Guardando...':'Guardar Cotización'"></span>
                </button>
                <a href="{{ url('/panel/cotizacions') }}"
                   class="mt-2 w-full inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 px-4 py-2.5 text-xs font-medium text-gray-600 dark:text-gray-300 transition">
                    <i class="ti ti-arrow-left text-xs"></i> Volver
                </a>
            </div>
        </div>

        {{-- RIGHT COLUMN: Products --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- Product search --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Agregar Producto</h3>
                <div class="relative">
                    <i class="ti ti-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input id="inpProducto" type="text" placeholder="Buscar por nombre, código o código de barra..."
                           @input.debounce.300ms="buscarProductos($event.target.value)"
                           @keydown.enter.prevent="productosLista.length===1 && agregarProducto(productosLista[0])"
                           class="w-full rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <ul x-show="productosLista.length>0" x-cloak
                        class="absolute z-30 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-2xl mt-1 max-h-72 overflow-y-auto">
                        <template x-for="p in productosLista" :key="p.id_producto">
                            <li @click="agregarProducto(p)"
                                class="flex items-center justify-between px-4 py-3 text-xs cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 border-b border-gray-50 dark:border-gray-700 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-700 dark:text-gray-200 truncate" x-text="p.descripcion"></p>
                                    <p class="text-gray-400 mt-0.5"
                                       x-text="'Cód: '+(p.codigo||'-')+'  |  Barra: '+(p.cod_barra||'-')+'  |  Stock: '+p.cantidad"></p>
                                </div>
                                <div class="text-right ml-4 shrink-0">
                                    <p class="font-bold text-blue-700 text-sm" x-text="'S/ '+parseFloat(p.precio).toFixed(2)"></p>
                                    <p class="text-[10px] text-gray-400"
                                       x-text="p.iscbp==1?'Exonerado':p.iscbp==2?'Inafecto':'Gravado'"></p>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            {{-- Product list --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 dark:border-gray-700 px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Detalle de Productos</h3>
                        <span class="rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-2.5 py-0.5 text-[10px] font-bold"
                              x-text="productosCoti.length+' item(s)'"></span>
                    </div>
                    <button x-show="productosCoti.length>0" @click="productosCoti=[];recalcular()"
                            class="text-xs text-red-500 hover:underline">Limpiar</button>
                </div>

                <template x-if="productosCoti.length===0">
                    <div class="py-16 text-center">
                        <i class="ti ti-package-off text-4xl text-gray-200 block mb-3"></i>
                        <p class="text-sm text-gray-400">Busca y selecciona productos para agregar a la cotización</p>
                    </div>
                </template>

                <template x-if="productosCoti.length>0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium">Descripción</th>
                                    <th class="px-3 py-3 text-center font-medium w-28">Cantidad</th>
                                    <th class="px-3 py-3 text-right font-medium w-32">Precio Unit.</th>
                                    <th class="px-3 py-3 text-right font-medium w-28">Total</th>
                                    <th class="px-3 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item,idx) in productosCoti" :key="idx">
                                    <tr class="border-t border-gray-50 dark:border-gray-700 hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="item.descripcion"></p>
                                            <p class="text-[10px] text-gray-400 mt-0.5" x-text="'Stock: '+item.stock_disponible"></p>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <input type="number" :value="item.cantidad" min="0.01" step="1"
                                                   @change="cambiarCantidad(idx,$event.target.value)"
                                                   class="w-20 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <input type="number" :value="item.precio" min="0" step="0.01"
                                                   @change="cambiarPrecio(idx,$event.target.value)"
                                                   class="w-28 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 px-2 py-1.5 text-right text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        </td>
                                        <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-gray-200"
                                            x-text="'S/ '+item.total.toFixed(2)"></td>
                                        <td class="px-3 py-3 text-center">
                                            <button @click="quitarProducto(idx)"
                                                    class="text-red-400 hover:text-red-600 transition-colors">
                                                <i class="ti ti-x text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300">TOTAL:</td>
                                    <td class="px-3 py-3 text-right font-extrabold text-blue-700"
                                        x-text="'S/ '+totalFinal.toFixed(2)"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
const _BASE = '{{ rtrim(config("app.url"), "/") }}';
const _csrf = () => document.querySelector('meta[name=csrf-token]')?.content ?? '';

const _Toast = Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3500, timerProgressBar:true });
const toastWarn = msg => _Toast.fire({ icon:'warning', title: msg });
const toastErr  = msg => _Toast.fire({ icon:'error',   title: msg });

async function _apiPost(url, data = {}) {
    const r = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': _csrf(), 'Accept':'application/json' },
        body: JSON.stringify(data),
    });
    return r.json();
}
async function _apiGet(url, params = {}) {
    const qs = new URLSearchParams(params).toString();
    const r = await fetch(qs ? `${url}?${qs}` : url, {
        headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': _csrf() }
    });
    return r.json();
}

function cotiApp() {
    return {
        documentos: [],
        coti: {
            id_tido: '', serie: '', numero: '',
            fecha: new Date().toISOString().slice(0,10),
            id_tipo_pago: '1', id_cliente: null, observacion: '',
        },
        clienteSeleccionado: null, sugerencias: [],
        productosLista: [], productosCoti: [],
        subtotal: 0, igvMonto: 0, totalFinal: 0, guardando: false,

        async init() {
            const d = await _apiGet(_BASE + '/api/cotizaciones/tipo');
            this.documentos = Array.isArray(d) ? d : [];
        },

        seleccionarTido() {
            const doc = this.documentos.find(d => d.id_tido == this.coti.id_tido);
            if (doc) {
                this.coti.serie  = doc.serie;
                this.coti.numero = String(parseInt(doc.numero) + 1).padStart(8, '0');
            }
        },

        async buscarClientes(term) {
            if (!term || term.length < 2) { this.sugerencias = []; return; }
            const d = await _apiGet(_BASE + '/api/clientes/buscar/datos', { term });
            this.sugerencias = Array.isArray(d) ? d : [];
        },

        seleccionarCliente(c) {
            this.clienteSeleccionado = c;
            this.coti.id_cliente = c.id_cliente;
            this.sugerencias = [];
            const inp = document.getElementById('inpCliente');
            if (inp) inp.value = '';
        },

        async buscarProductos(term) {
            if (!term || term.length < 2) { this.productosLista = []; return; }
            const d = await _apiGet(_BASE + '/api/cotizaciones/buscar/producto', { term });
            this.productosLista = Array.isArray(d) ? d : [];
        },

        agregarProducto(p) {
            const idx = this.productosCoti.findIndex(i => i.id_producto === p.id_producto);
            if (idx >= 0) {
                this.productosCoti[idx].cantidad++;
                this.productosCoti[idx].total = parseFloat((this.productosCoti[idx].cantidad * this.productosCoti[idx].precio).toFixed(2));
            } else {
                this.productosCoti.push({
                    id_producto: p.id_producto, descripcion: p.descripcion,
                    cantidad: 1, precio: parseFloat(p.precio),
                    total: parseFloat(p.precio), costo: parseFloat(p.costo || 0),
                    stock_disponible: p.cantidad, medida: 'Unidad', presenta: 1, presenta_cnt: 1,
                });
            }
            this.productosLista = [];
            const inp = document.getElementById('inpProducto');
            if (inp) inp.value = '';
            this.recalcular();
        },

        cambiarCantidad(idx, val) {
            const c = parseFloat(val) || 0;
            this.productosCoti[idx].cantidad = c;
            this.productosCoti[idx].total = parseFloat((c * this.productosCoti[idx].precio).toFixed(2));
            this.recalcular();
        },

        cambiarPrecio(idx, val) {
            const p = parseFloat(val) || 0;
            this.productosCoti[idx].precio = p;
            this.productosCoti[idx].total = parseFloat((this.productosCoti[idx].cantidad * p).toFixed(2));
            this.recalcular();
        },

        quitarProducto(idx) { this.productosCoti.splice(idx, 1); this.recalcular(); },

        recalcular() {
            const bruto = this.productosCoti.reduce((s, p) => s + p.total, 0);
            this.subtotal  = parseFloat((bruto / 1.18).toFixed(2));
            this.igvMonto  = parseFloat((bruto - this.subtotal).toFixed(2));
            this.totalFinal = parseFloat(bruto.toFixed(2));
        },

        async guardarCotizacion() {
            if (!this.coti.id_tido)       { toastWarn('Selecciona el tipo de documento.'); return; }
            if (!this.coti.id_cliente)     { toastWarn('Selecciona un cliente.'); return; }
            if (!this.productosCoti.length){ toastWarn('Agrega al menos un producto.'); return; }

            this.guardando = true;
            try {
                const payload = {
                    id_tido:       parseInt(this.coti.id_tido),
                    id_tipo_pago:  parseInt(this.coti.id_tipo_pago),
                    fecha:         this.coti.fecha,
                    id_cliente:    this.coti.id_cliente,
                    total:         this.totalFinal,
                    observacion:   this.coti.observacion,
                    productos: this.productosCoti.map(p => ({
                        id_producto: p.id_producto, cantidad: p.cantidad,
                        precio: p.precio, costo: p.costo,
                        medida: p.medida, presenta: p.presenta, presenta_cnt: p.presenta_cnt,
                    })),
                };
                const data = await _apiPost(_BASE + '/api/cotizaciones/add', payload);
                if (data.res) {
                    await Swal.fire({ title: '¡Cotización registrada!', text: data.msg, icon: 'success', confirmButtonColor: '#1d4ed8' });
                    window.location = _BASE + '/panel/cotizacions';
                } else {
                    toastErr(data.message || data.msg || 'Error al guardar.');
                }
            } catch (e) {
                toastErr('Error de conexión.');
            } finally {
                this.guardando = false;
            }
        },
    };
}
</script>

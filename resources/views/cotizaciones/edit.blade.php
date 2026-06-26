@extends('layouts.app')
@section('title','Editar Cotización')
@section('page-title','Editar Cotización')
@section('breadcrumb','Pedidos / Cotizaciones / Editar')

@section('content')
<div x-data="cotiEditApp()" x-init="init()">
    <template x-if="cargando">
        <div class="flex justify-center py-20"><i class="ti ti-loader-2 text-3xl text-blue-500 spin"></i></div>
    </template>
    <template x-if="!cargando && !coti">
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-8 text-center">
            <i class="ti ti-alert-circle text-5xl text-red-400 mb-4 block"></i>
            <h2 class="text-xl font-bold text-gray-700 mb-2">Cotización no encontrada</h2>
            <a href="{{ route('cotizaciones.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition">
                <i class="ti ti-arrow-left"></i> Volver
            </a>
        </div>
    </template>
    <template x-if="!cargando && coti">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 xl:h-[calc(100vh-120px)]">

            {{-- COLUMNA IZQUIERDA --}}
            <div class="xl:col-span-1 flex flex-col gap-4 xl:overflow-y-auto pr-1" style="scrollbar-width:thin;">

                {{-- Comprobante --}}
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-4 shrink-0">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Comprobante</h3>
                    <div class="space-y-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo de Documento</label>
                            <select x-model="coti.id_tido" @change="seleccionarTido()"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">-- Selecciona --</option>
                                <template x-for="d in documentos" :key="d.id_tido">
                                    <option :value="d.id_tido" x-text="d.tipo_doc+' — '+d.serie"></option>
                                </template>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Número</label>
                                <input x-model="coti.numero" type="text" readonly class="w-full rounded-lg border border-gray-100 bg-gray-50 px-3 py-1.5 text-sm text-gray-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Estado</label>
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold"
                                      :class="coti.estado==='1'?'bg-blue-100 text-blue-700':'bg-gray-100 text-gray-600'"
                                      x-text="coti.estado==='1'?'Activo':'Anulada'"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha Emisión *</label>
                                <input x-model="coti.fecha" type="date" class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Forma de Pago *</label>
                                <select x-model="coti.id_tipo_pago" class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="1">Contado</option>
                                    <option value="2">Crédito</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Observación</label>
                            <input x-model="coti.observacion" type="text" maxlength="220" placeholder="Opcional"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>
                </div>

                {{-- Cliente --}}
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-4 shrink-0">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Cliente</h3>
                    <template x-if="!clienteSeleccionado">
                        <div class="relative">
                            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="inpCliente" type="text" placeholder="Buscar por nombre o documento..."
                                   @input.debounce.400ms="buscarClientes($event.target.value)"
                                   class="w-full rounded-lg border border-gray-200 pl-9 pr-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <ul x-show="sugerencias.length>0" x-cloak class="absolute z-30 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 max-h-52 overflow-y-auto">
                                <template x-for="c in sugerencias" :key="c.id_cliente">
                                    <li @click="seleccionarCliente(c)" class="flex justify-between px-3 py-2 text-xs cursor-pointer hover:bg-blue-50 border-b border-gray-50 last:border-0">
                                        <span class="font-semibold text-gray-700" x-text="c.datos"></span>
                                        <span class="text-gray-400" x-text="c.documento"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>
                    <template x-if="clienteSeleccionado">
                        <div class="rounded-xl bg-blue-50 border border-blue-200 p-3">
                            <p class="text-xs font-bold text-blue-800" x-text="clienteSeleccionado.datos"></p>
                            <p class="text-xs text-blue-600 mt-0.5" x-text="clienteSeleccionado.documento"></p>
                            <p class="text-xs text-blue-500 mt-0.5" x-text="clienteSeleccionado.direccion||''"></p>
                            <button @click="clienteSeleccionado=null;coti.id_cliente=null" class="mt-1.5 text-[10px] text-red-500 hover:underline">
                                <i class="ti ti-refresh"></i> Cambiar cliente
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Resumen + Botón --}}
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-4 shrink-0">
                    <div class="space-y-1.5 text-xs mb-4">
                        <div class="flex justify-between"><span class="text-gray-500">Subtotal:</span><span class="font-semibold" x-text="'S/ '+subtotal.toFixed(2)"></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">IGV (18%):</span><span class="font-semibold" x-text="'S/ '+igvMonto.toFixed(2)"></span></div>
                        <div class="flex justify-between text-sm border-t border-gray-100 pt-2">
                            <span class="font-bold text-gray-700">TOTAL:</span>
                            <span class="font-extrabold text-blue-700 text-base" x-text="'S/ '+totalFinal.toFixed(2)"></span>
                        </div>
                    </div>
                    <button @click="guardarEdicion()" :disabled="guardando || coti.estado==='0'"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed px-4 py-2.5 text-sm font-bold text-white transition shadow-lg shadow-blue-900/20">
                        <i class="ti ti-device-floppy" :class="{'spin':guardando}"></i>
                        <span x-text="guardando?'Guardando...':'Actualizar'"></span>
                    </button>
                    <a href="{{ route('cotizaciones.index') }}"
                       class="mt-2 w-full inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2 text-xs font-medium text-gray-600 transition">
                        <i class="ti ti-arrow-left text-xs"></i> Volver
                    </a>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Productos --}}
            <div class="xl:col-span-2 flex flex-col gap-4 xl:overflow-hidden">

                {{-- Buscador --}}
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-4 shrink-0">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Agregar Producto</h3>
                    <div class="relative">
                        <i class="ti ti-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input id="inpProducto" type="text" placeholder="Buscar por nombre, código o código de barra..."
                               @input.debounce.300ms="buscarProductos($event.target.value)"
                               @keydown.enter.prevent="productosLista.length===1 && agregarProducto(productosLista[0])"
                               class="w-full rounded-lg border border-gray-200 pl-9 pr-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <ul x-show="productosLista.length>0" x-cloak class="absolute z-30 w-full bg-white border border-gray-200 rounded-xl shadow-2xl mt-1 max-h-64 overflow-y-auto">
                            <template x-for="p in productosLista" :key="p.id_producto">
                                <li @click="agregarProducto(p)" class="flex items-center justify-between px-4 py-2.5 text-xs cursor-pointer hover:bg-blue-50 border-b border-gray-50 last:border-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-700 truncate" x-text="p.descripcion"></p>
                                        <p class="text-gray-400 mt-0.5" x-text="'Cód: '+(p.codigo||'-')+'  |  Stock: '+p.cantidad"></p>
                                    </div>
                                    <p class="font-bold text-blue-700 text-sm ml-4 shrink-0" x-text="'S/ '+parseFloat(p.precio).toFixed(2)"></p>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                {{-- Detalle --}}
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm flex flex-col xl:overflow-hidden xl:flex-1">
                    <div class="border-b border-gray-100 px-5 py-3 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Detalle de Productos</h3>
                            <span class="rounded-full bg-blue-100 text-blue-700 px-2.5 py-0.5 text-[10px] font-bold" x-text="productosCoti.length+' item(s)'"></span>
                        </div>
                        <button x-show="productosCoti.length>0 && coti.estado!=='0'" @click="productosCoti=[];recalcular()" class="text-xs text-red-500 hover:underline">Limpiar</button>
                    </div>

                    <template x-if="productosCoti.length===0">
                        <div class="flex-1 flex flex-col items-center justify-center py-10">
                            <i class="ti ti-package-off text-4xl text-gray-200 block mb-3"></i>
                            <p class="text-sm text-gray-400">Busca y selecciona productos</p>
                        </div>
                    </template>

                    <template x-if="productosCoti.length>0">
                        <div class="xl:overflow-y-auto xl:flex-1" style="scrollbar-width:thin;">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-50 text-gray-500 sticky top-0">
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
                                        <tr class="border-t border-gray-50 hover:bg-blue-50/30 transition-colors">
                                            <td class="px-4 py-2.5">
                                                <p class="font-medium text-gray-800" x-text="item.descripcion||('Producto #'+item.id_producto)"></p>
                                            </td>
                                            <td class="px-3 py-2.5 text-center">
                                                <input type="number" :value="item.cantidad" min="0.01" step="1"
                                                       @change="cambiarCantidad(idx,$event.target.value)"
                                                       :disabled="coti.estado==='0'"
                                                       class="w-20 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-2 focus:ring-blue-400 disabled:bg-gray-50">
                                            </td>
                                            <td class="px-3 py-2.5 text-right">
                                                <input type="number" :value="item.precio" min="0" step="0.01"
                                                       @change="cambiarPrecio(idx,$event.target.value)"
                                                       :disabled="coti.estado==='0'"
                                                       class="w-28 rounded-lg border border-gray-200 px-2 py-1.5 text-right text-xs focus:outline-none focus:ring-2 focus:ring-blue-400 disabled:bg-gray-50">
                                            </td>
                                            <td class="px-3 py-2.5 text-right font-bold text-gray-800" x-text="'S/ '+item.total.toFixed(2)"></td>
                                            <td class="px-3 py-2.5 text-center">
                                                <button x-show="coti.estado!=='0'" @click="quitarProducto(idx)" class="text-red-400 hover:text-red-600 transition-colors">
                                                    <i class="ti ti-x text-sm"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-gray-50 border-t border-gray-100 sticky bottom-0">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-right text-xs font-bold text-gray-600">TOTAL:</td>
                                        <td class="px-3 py-3 text-right font-extrabold text-blue-700" x-text="'S/ '+totalFinal.toFixed(2)"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
function cotiEditApp() {
    return {
        BASE: BASE_URL,
        documentos: [],
        coti: null, clienteSeleccionado: null, sugerencias: [],
        productosLista: [], productosCoti: [],
        subtotal:0, igvMonto:0, totalFinal:0, guardando:false, cargando:true,

        async init() {
            const cotiId = {{ $id }};
            const [docs, detalle] = await Promise.all([
                apiGet(this.BASE+'/api/cotizaciones/tipo'),
                apiPost(this.BASE+'/api/cotizaciones/detalle', {id_cotizacion:cotiId}),
            ]);
            this.documentos = Array.isArray(docs) ? docs : [];

            if (detalle && detalle.cotizacion_id) {
                this.coti = {
                    cotizacion_id: detalle.cotizacion_id,
                    id_tido: String(detalle.id_tido),
                    numero: String(detalle.numero).padStart(8,'0'),
                    fecha: detalle.fecha ? detalle.fecha.slice(0,10) : '',
                    id_tipo_pago: String(detalle.id_tipo_pago),
                    id_cliente: detalle.id_cliente,
                    total: parseFloat(detalle.total||0),
                    estado: String(detalle.estado),
                    observacion: detalle.observacion||'',
                };
                if(detalle.cliente) {
                    this.clienteSeleccionado = detalle.cliente;
                }
                if(detalle.productos) {
                    this.productosCoti = detalle.productos.map(p => ({
                        id_producto: p.id_producto,
                        descripcion: p.producto?.descripcion || ('Producto #'+p.id_producto),
                        cantidad: parseFloat(p.cantidad),
                        precio: parseFloat(p.precio),
                        total: parseFloat(p.cantidad) * parseFloat(p.precio),
                        costo: parseFloat(p.costo||0),
                        stock_disponible: p.producto?.cantidad || 0,
                        medida: p.medida || 'Unidad',
                        presenta: p.presenta || 1,
                        presenta_cnt: p.presenta_cnt || 1,
                    }));
                    this.recalcular();
                }
            }
            this.cargando = false;
        },

        seleccionarTido() {
            const doc = this.documentos.find(d=>d.id_tido==this.coti.id_tido);
            if(doc){ this.coti.serie=doc.serie; }
        },

        async buscarClientes(term) {
            if(!term||term.length<2){this.sugerencias=[];return;}
            const d=await apiGet(this.BASE+'/api/clientes/buscar/datos',{term});
            this.sugerencias=Array.isArray(d)?d:[];
        },

        seleccionarCliente(c) {
            this.clienteSeleccionado=c; this.coti.id_cliente=c.id_cliente;
            this.sugerencias=[]; document.getElementById('inpCliente').value='';
        },

        async buscarProductos(term) {
            if(!term||term.length<2){this.productosLista=[];return;}
            const d=await apiGet(this.BASE+'/api/cotizaciones/buscar/producto',{term});
            this.productosLista=Array.isArray(d)?d:[];
        },

        agregarProducto(p) {
            const idx=this.productosCoti.findIndex(i=>i.id_producto===p.id_producto);
            if(idx>=0){
                this.productosCoti[idx].cantidad++;
                this.productosCoti[idx].total=parseFloat((this.productosCoti[idx].cantidad*this.productosCoti[idx].precio).toFixed(2));
            } else {
                this.productosCoti.push({id_producto:p.id_producto,descripcion:p.descripcion,cantidad:1,
                    precio:parseFloat(p.precio),total:parseFloat(p.precio),costo:parseFloat(p.costo||0),
                    stock_disponible:p.cantidad,medida:'Unidad',presenta:1,presenta_cnt:1});
            }
            this.productosLista=[]; document.getElementById('inpProducto').value='';
            this.recalcular();
        },

        cambiarCantidad(idx,val) {
            const c=parseFloat(val)||0;
            this.productosCoti[idx].cantidad=c;
            this.productosCoti[idx].total=parseFloat((c*this.productosCoti[idx].precio).toFixed(2));
            this.recalcular();
        },

        cambiarPrecio(idx,val) {
            const p=parseFloat(val)||0;
            this.productosCoti[idx].precio=p;
            this.productosCoti[idx].total=parseFloat((this.productosCoti[idx].cantidad*p).toFixed(2));
            this.recalcular();
        },

        quitarProducto(idx){this.productosCoti.splice(idx,1);this.recalcular();},

        recalcular() {
            const bruto=this.productosCoti.reduce((s,p)=>s+p.total,0);
            this.subtotal=parseFloat((bruto/1.18).toFixed(2));
            this.igvMonto=parseFloat((bruto-this.subtotal).toFixed(2));
            this.totalFinal=parseFloat(bruto.toFixed(2));
        },

        async guardarEdicion() {
            if(!this.coti.id_cliente){toastWarn('Selecciona un cliente.');return;}
            if(!this.productosCoti.length){toastWarn('Agrega al menos un producto.');return;}

            this.guardando=true;
            try {
                const payload={
                    id_cotizacion: this.coti.cotizacion_id,
                    id_tido:parseInt(this.coti.id_tido), id_tipo_pago:parseInt(this.coti.id_tipo_pago),
                    fecha:this.coti.fecha, id_cliente:this.coti.id_cliente,
                    total:this.totalFinal, observacion:this.coti.observacion,
                    productos:this.productosCoti.map(p=>({id_producto:p.id_producto,
                        cantidad:p.cantidad,precio:p.precio,costo:p.costo,
                        medida:p.medida,presenta:p.presenta,presenta_cnt:p.presenta_cnt})),
                };
                const data=await apiPost(this.BASE+'/api/cotizaciones/editar',payload);
                if(data.res){
                    await Swal.fire({title:'¡Cotización actualizada!',text:data.msg,icon:'success',confirmButtonColor:'#1d4ed8'});
                    window.location=this.BASE+'/cotizaciones';
                } else {
                    toastErr(data.message||data.msg||'Error al guardar.');
                }
            } catch(e){ toastErr('Error de conexión.'); }
            finally{ this.guardando=false; }
        },
    }
}
</script>
@endpush

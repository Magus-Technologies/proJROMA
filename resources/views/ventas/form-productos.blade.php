@extends('layouts.app')
@section('title','Nueva Venta')
@section('page-title','Nueva Venta — Productos')
@section('breadcrumb','Facturación / Ventas / Nueva Venta')

@section('content')
<div x-data="ventaApp()" x-init="init()">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- COLUMNA IZQUIERDA --}}
        <div class="xl:col-span-1 space-y-4">

            {{-- Comprobante --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Comprobante</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo de Documento *</label>
                        <select x-model="venta.id_tido" @change="seleccionarTido()"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">-- Selecciona --</option>
                            <template x-for="d in documentos" :key="d.id_tido">
                                <option :value="d.id_tido" x-text="d.tipo_doc+' — '+d.serie"></option>
                            </template>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Serie</label>
                            <input x-model="venta.serie" type="text" readonly class="w-full rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Número</label>
                            <input x-model="venta.numero" type="text" readonly class="w-full rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha Emisión *</label>
                        <input x-model="venta.fecha" type="date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Forma de Pago *</label>
                        <select x-model="venta.id_tipo_pago" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="1">Contado</option>
                            <option value="2">Crédito</option>
                        </select>
                    </div>
                    <div x-show="venta.id_tipo_pago == 2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha Vencimiento</label>
                        <input x-model="venta.fecha_vencimiento" type="date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">IGV</label>
                        <select x-model="venta.apli_igv" @change="recalcular()" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="1">Con IGV (18%)</option>
                            <option value="0">Sin IGV</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Observación</label>
                        <input x-model="venta.observacion" type="text" maxlength="220" placeholder="Opcional"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>
            </div>

            {{-- Cliente --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Cliente</h3>
                <div class="space-y-3">
                    <template x-if="!clienteSeleccionado">
                        <div class="relative">
                            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="inpCliente" type="text" placeholder="Buscar por nombre o documento..."
                                   @input.debounce.400ms="buscarClientes($event.target.value)"
                                   class="w-full rounded-lg border border-gray-200 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
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
                            <button @click="clienteSeleccionado=null;venta.id_cliente=null" class="mt-2 text-[10px] text-red-500 hover:underline">
                                <i class="ti ti-refresh"></i> Cambiar cliente
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Totales + Guardar --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Resumen</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between"><span class="text-gray-500">Op. Gravadas:</span><span class="font-semibold" x-text="'S/ '+subtotal.toFixed(2)"></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">IGV (18%):</span><span class="font-semibold" x-text="'S/ '+igvMonto.toFixed(2)"></span></div>
                    <div class="flex justify-between text-sm border-t border-gray-100 pt-2 mt-1">
                        <span class="font-bold text-gray-700">IMPORTE TOTAL:</span>
                        <span class="font-extrabold text-blue-700 text-lg" x-text="'S/ '+totalFinal.toFixed(2)"></span>
                    </div>
                </div>
                <button @click="guardarVenta()" :disabled="guardando"
                        class="mt-5 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed px-4 py-3 text-sm font-bold text-white transition shadow-lg shadow-blue-900/20">
                    <i class="ti ti-device-floppy" :class="{'spin':guardando}"></i>
                    <span x-text="guardando?'Guardando...':'Guardar Venta'"></span>
                </button>
                <a href="{{ route('ventas.index') }}"
                   class="mt-2 w-full inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2.5 text-xs font-medium text-gray-600 transition">
                    <i class="ti ti-arrow-left text-xs"></i> Volver a Ventas
                </a>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Productos --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- Buscador --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Agregar Producto</h3>
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <i class="ti ti-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input id="inpProducto" type="text" placeholder="Buscar por nombre, código o código de barra..."
                               @input.debounce.300ms="buscarProductos($event.target.value)"
                               @keydown.enter.prevent="productosLista.length===1 && agregarProducto(productosLista[0])"
                               class="w-full rounded-lg border border-gray-200 pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <ul x-show="productosLista.length>0" x-cloak class="absolute z-30 w-full bg-white border border-gray-200 rounded-xl shadow-2xl mt-1 max-h-72 overflow-y-auto">
                            <template x-for="p in productosLista" :key="p.id_producto">
                                <li @click="agregarProducto(p)" class="flex items-center justify-between px-4 py-3 text-xs cursor-pointer hover:bg-blue-50 border-b border-gray-50 last:border-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-700 truncate" x-text="p.descripcion"></p>
                                        <p class="text-gray-400 mt-0.5" x-text="'Cód: '+(p.codigo||'-')+'  |  Barra: '+(p.cod_barra||'-')+'  |  Stock: '+p.cantidad"></p>
                                    </div>
                                    <div class="text-right ml-4 shrink-0">
                                        <p class="font-bold text-blue-700 text-sm" x-text="'S/ '+parseFloat(p.precio).toFixed(2)"></p>
                                        <p class="text-[10px] text-gray-400" x-text="p.iscbp==1?'Exonerado':p.iscbp==2?'Inafecto':'Gravado'"></p>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <select x-model="almacenActivo" @change="productosLista=[]"
                            class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 shrink-0">
                        <option value="1">Almacén 1</option>
                        <option value="2">Almacén 2</option>
                        <option value="3">Almacén 3</option>
                    </select>
                </div>
            </div>

            {{-- Detalle --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Detalle de Productos</h3>
                        <span class="rounded-full bg-blue-100 text-blue-700 px-2.5 py-0.5 text-[10px] font-bold" x-text="productosVenta.length+' item(s)'"></span>
                    </div>
                    <button x-show="productosVenta.length>0" @click="productosVenta=[];recalcular()" class="text-xs text-red-500 hover:underline">Limpiar</button>
                </div>

                <template x-if="productosVenta.length===0">
                    <div class="py-16 text-center">
                        <i class="ti ti-package-off text-4xl text-gray-200 block mb-3"></i>
                        <p class="text-sm text-gray-400">Busca y selecciona productos para agregar a la venta</p>
                    </div>
                </template>

                <template x-if="productosVenta.length>0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium">Descripción</th>
                                    <th class="px-3 py-3 text-center font-medium w-28">Cantidad</th>
                                    <th class="px-3 py-3 text-right font-medium w-32">Precio Unit.</th>
                                    <th class="px-3 py-3 text-right font-medium w-28">Total</th>
                                    <th class="px-3 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item,idx) in productosVenta" :key="idx">
                                    <tr class="border-t border-gray-50 hover:bg-blue-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-800" x-text="item.descripcion"></p>
                                            <p class="text-[10px] text-gray-400 mt-0.5" x-text="'Stock: '+item.stock_disponible+' | '+( item.igv_prod==1?'Exonerado':item.igv_prod==2?'Inafecto':'Gravado')"></p>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <input type="number" :value="item.cantidad" min="0.01" step="1"
                                                   @change="cambiarCantidad(idx,$event.target.value)"
                                                   class="w-20 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <input type="number" :value="item.precio" min="0" step="0.01"
                                                   @change="cambiarPrecio(idx,$event.target.value)"
                                                   class="w-28 rounded-lg border border-gray-200 px-2 py-1.5 text-right text-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        </td>
                                        <td class="px-3 py-3 text-right font-bold text-gray-800" x-text="'S/ '+item.total.toFixed(2)"></td>
                                        <td class="px-3 py-3 text-center">
                                            <button @click="quitarProducto(idx)" class="text-red-400 hover:text-red-600 transition-colors">
                                                <i class="ti ti-x text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-100">
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
</div>
@endsection

@push('scripts')
<script>
function ventaApp() {
    return {
        BASE: BASE_URL,
        documentos: [], venta: {
            id_tido:'', serie:'', numero:'',
            fecha: new Date().toISOString().slice(0,10),
            fecha_vencimiento:'', id_tipo_pago:'1',
            id_cliente:null, apli_igv:'1', observacion:'',
        },
        clienteSeleccionado:null, sugerencias:[],
        productosLista:[], productosVenta:[],
        almacenActivo:'1', subtotal:0, igvMonto:0, totalFinal:0, guardando:false,

        async init() {
            const d = await apiGet(this.BASE+'/api/ventas/tipo');
            this.documentos = Array.isArray(d) ? d : [];
        },

        seleccionarTido() {
            const doc = this.documentos.find(d=>d.id_tido==this.venta.id_tido);
            if(doc){ this.venta.serie=doc.serie; this.venta.numero=String(parseInt(doc.numero)+1).padStart(8,'0'); }
        },

        async buscarClientes(term) {
            if(!term||term.length<2){this.sugerencias=[];return;}
            const d=await apiGet(this.BASE+'/api/clientes/buscar/datos',{term});
            this.sugerencias=Array.isArray(d)?d:[];
        },

        seleccionarCliente(c) {
            this.clienteSeleccionado=c; this.venta.id_cliente=c.id_cliente;
            this.sugerencias=[]; document.getElementById('inpCliente').value='';
        },

        async buscarProductos(term) {
            if(!term||term.length<2){this.productosLista=[];return;}
            const d=await apiGet(this.BASE+'/api/ventas/cargar/productos/'+this.almacenActivo,{term});
            this.productosLista=Array.isArray(d)?d:[];
        },

        agregarProducto(p) {
            const idx=this.productosVenta.findIndex(i=>i.id_producto===p.id_producto);
            if(idx>=0){
                this.productosVenta[idx].cantidad++;
                this.productosVenta[idx].total=parseFloat((this.productosVenta[idx].cantidad*this.productosVenta[idx].precio).toFixed(2));
            } else {
                this.productosVenta.push({id_producto:p.id_producto,descripcion:p.descripcion,cantidad:1,
                    precio:parseFloat(p.precio),total:parseFloat(p.precio),igv_prod:p.iscbp||0,
                    stock_disponible:p.cantidad,descuento:0});
            }
            this.productosLista=[]; document.getElementById('inpProducto').value='';
            this.recalcular();
        },

        cambiarCantidad(idx,val) {
            const c=parseFloat(val)||0;
            this.productosVenta[idx].cantidad=c;
            this.productosVenta[idx].total=parseFloat((c*this.productosVenta[idx].precio).toFixed(2));
            this.recalcular();
        },

        cambiarPrecio(idx,val) {
            const p=parseFloat(val)||0;
            this.productosVenta[idx].precio=p;
            this.productosVenta[idx].total=parseFloat((this.productosVenta[idx].cantidad*p).toFixed(2));
            this.recalcular();
        },

        quitarProducto(idx){this.productosVenta.splice(idx,1);this.recalcular();},

        recalcular() {
            const bruto=this.productosVenta.reduce((s,p)=>s+p.total,0);
            if(this.venta.apli_igv==='1'){
                this.subtotal=parseFloat((bruto/1.18).toFixed(2));
                this.igvMonto=parseFloat((bruto-this.subtotal).toFixed(2));
            } else {
                this.subtotal=parseFloat(bruto.toFixed(2)); this.igvMonto=0;
            }
            this.totalFinal=parseFloat(bruto.toFixed(2));
        },

        async guardarVenta() {
            if(!this.venta.id_tido){toastWarn('Selecciona el tipo de comprobante.');return;}
            if(!this.venta.id_cliente){toastWarn('Selecciona un cliente.');return;}
            if(!this.productosVenta.length){toastWarn('Agrega al menos un producto.');return;}

            this.guardando=true;
            try {
                const payload={
                    id_tido:parseInt(this.venta.id_tido), id_tipo_pago:parseInt(this.venta.id_tipo_pago),
                    fecha:this.venta.fecha, fecha_vencimiento:this.venta.fecha_vencimiento||this.venta.fecha,
                    id_cliente:this.venta.id_cliente, total:this.totalFinal, subtotal:this.subtotal,
                    igv:0.18, apli_igv:this.venta.apli_igv, observacion:this.venta.observacion,
                    productos:this.productosVenta.map(p=>({id_producto:p.id_producto,descripcion:p.descripcion,
                        cantidad:p.cantidad,precio:p.precio,total:p.total,igv_prod:p.igv_prod,descuento:p.descuento})),
                    lista_pagos:[],
                };
                const data=await apiPost(this.BASE+'/api/ventas/add',payload);
                if(data.res){
                    const r=await Swal.fire({title:'¡Venta registrada!',text:data.msg,icon:'success',
                        showCancelButton:true,confirmButtonText:'Ver PDF A4',cancelButtonText:'Nueva Venta',confirmButtonColor:'#1d4ed8'});
                    if(r.isConfirmed) window.open(this.BASE+'/venta/comprobante/pdf/'+data.id_venta,'_blank');
                    // Reset
                    this.productosVenta=[]; this.clienteSeleccionado=null; this.venta.id_cliente=null;
                    this.venta.observacion=''; this.recalcular();
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

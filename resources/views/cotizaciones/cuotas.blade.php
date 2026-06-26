@extends('layouts.app')
@section('title','Cuotas de Cotización')
@section('page-title','Cuotas de Cotización')
@section('breadcrumb','Pedidos / Cotizaciones / Cuotas')

@section('content')
<div x-data="cuotasApp()" x-init="init()">
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
        <div class="space-y-4">

            {{-- Info de la cotización --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-700" x-text="'Cotización N° ' + coti.numero"></h3>
                        <p class="text-xs text-gray-500 mt-1" x-text="'Cliente: ' + (coti.cliente?.datos || '-')"></p>
                        <p class="text-xs text-gray-500" x-text="'Fecha: ' + (coti.fecha||'').slice(0,10) + '  |  Total: S/ ' + parseFloat(coti.total).toFixed(2)"></p>
                    </div>
                    <div class="flex gap-2">
                        <a :href="BASE+'/cotizaciones/editar/'+coti.cotizacion_id"
                           class="inline-flex items-center gap-1 rounded-lg bg-blue-600 hover:bg-blue-700 px-3 py-2 text-xs font-semibold text-white transition">
                            <i class="ti ti-pencil"></i> Editar
                        </a>
                        <a :href="BASE+'/r/cotizaciones/reporte/'+coti.cotizacion_id" target="_blank"
                           class="inline-flex items-center gap-1 rounded-lg bg-red-600 hover:bg-red-700 px-3 py-2 text-xs font-semibold text-white transition">
                            <i class="ti ti-file-type-pdf"></i> PDF
                        </a>
                        <a href="{{ route('cotizaciones.index') }}"
                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 px-3 py-2 text-xs font-medium text-gray-600 transition">
                            <i class="ti ti-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

            {{-- Cuotas --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Cuotas</h3>
                        <span class="rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-0.5 text-[10px] font-bold" x-text="cuotas.length+' cuota(s)'"></span>
                    </div>
                    <button x-show="coti.estado==='1'" @click="abrirModalCuota()"
                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white transition">
                        <i class="ti ti-plus"></i> Agregar Cuota
                    </button>
                </div>

                <template x-if="cuotas.length===0">
                    <div class="py-16 text-center">
                        <i class="ti ti-calendar-off text-4xl text-gray-200 block mb-3"></i>
                        <p class="text-sm text-gray-400">No hay cuotas registradas para esta cotización</p>
                    </div>
                </template>

                <template x-if="cuotas.length>0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium">#</th>
                                    <th class="px-4 py-3 text-left font-medium">Fecha</th>
                                    <th class="px-4 py-3 text-right font-medium">Monto</th>
                                    <th class="px-4 py-3 text-center font-medium">Estado</th>
                                    <th class="px-4 py-3 text-center font-medium">Tipo Pago</th>
                                    <th class="px-4 py-3 text-center font-medium">F. Pago Real</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(cuota,idx) in cuotas" :key="cuota.cuota_coti_id||idx">
                                    <tr class="border-t border-gray-50 hover:bg-blue-50/30 transition-colors">
                                        <td class="px-4 py-3 text-gray-600" x-text="idx+1"></td>
                                        <td class="px-4 py-3" x-text="(cuota.fecha||'-').slice(0,10)"></td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-800" x-text="'S/ '+parseFloat(cuota.monto).toFixed(2)"></td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-block rounded-full px-2 py-0.5 text-[10px] font-bold"
                                                  :class="cuota.estado==='1'?'bg-emerald-100 text-emerald-700':'bg-amber-100 text-amber-700'"
                                                  x-text="cuota.estado==='1'?'Pagado':'Pendiente'"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600" x-text="cuota.tipo_pago||'-'"></td>
                                        <td class="px-4 py-3 text-center text-gray-500" x-text="(cuota.fecha_pago_real||'-').slice(0,10)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-100">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-right text-xs font-bold text-gray-600">TOTAL CUOTAS:</td>
                                    <td class="px-4 py-3 text-right font-extrabold text-emerald-700" x-text="'S/ '+totalCuotas.toFixed(2)"></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </template>
            </div>

            {{-- Resumen --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Total Cotización</p>
                        <p class="text-lg font-extrabold text-gray-800" x-text="'S/ '+parseFloat(coti.total).toFixed(2)"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Total Cuotas</p>
                        <p class="text-lg font-extrabold text-emerald-600" x-text="'S/ '+totalCuotas.toFixed(2)"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Saldo Pendiente</p>
                        <p class="text-lg font-extrabold" :class="saldoPendiente>0?'text-red-600':'text-emerald-600'" x-text="'S/ '+saldoPendiente.toFixed(2)"></p>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Modal Agregar Cuota --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showModal=false"></div>
        <div class="relative z-10 w-full max-w-md rounded-2xl bg-white shadow-2xl p-6">
            <h4 class="text-sm font-bold text-gray-700 mb-4">Agregar Cuota</h4>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Monto *</label>
                    <input x-model="nuevaCuota.monto" type="number" step="0.01" min="0"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha de Pago *</label>
                    <input x-model="nuevaCuota.fecha" type="date"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo de Pago</label>
                    <select x-model="nuevaCuota.tipo_pago"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="TRANSFERENCIA">Transferencia</option>
                        <option value="TARJETA">Tarjeta</option>
                        <option value="CHEQUE">Cheque</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-5">
                <button @click="agregarCuota()" :disabled="guardandoCuota"
                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 px-4 py-2.5 text-xs font-bold text-white transition">
                    <i class="ti ti-device-floppy"></i> Guardar
                </button>
                <button @click="showModal=false"
                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2.5 text-xs font-medium text-gray-600 transition">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cuotasApp() {
    return {
        BASE: BASE_URL,
        coti: null, cuotas: [], cargando: true,
        showModal: false, guardandoCuota: false,
        nuevaCuota: { monto:'', fecha:new Date().toISOString().slice(0,10), tipo_pago:'EFECTIVO' },

        get totalCuotas() { return this.cuotas.reduce((s,c)=>s+parseFloat(c.monto||0),0); },
        get saldoPendiente() { return this.coti ? parseFloat(this.coti.total)-this.totalCuotas : 0; },

        async init() {
            const cotiId = {{ $id }};
            const [detalle, cuotasData] = await Promise.all([
                apiPost(this.BASE+'/api/cotizaciones/detalle', {id_cotizacion:cotiId}),
                apiPost(this.BASE+'/api/cotizaciones/cuotas', {id_cotizacion:cotiId}),
            ]);
            this.coti = (detalle && detalle.cotizacion_id) ? detalle : null;
            this.cuotas = Array.isArray(cuotasData) ? cuotasData : [];
            this.cargando = false;
        },

        abrirModalCuota() {
            this.nuevaCuota = { monto:'', fecha:new Date().toISOString().slice(0,10), tipo_pago:'EFECTIVO' };
            this.showModal = true;
        },

        async agregarCuota() {
            if(!this.nuevaCuota.monto || parseFloat(this.nuevaCuota.monto)<=0){toastWarn('Ingresa un monto válido.');return;}
            if(!this.nuevaCuota.fecha){toastWarn('Selecciona una fecha.');return;}

            this.guardandoCuota=true;
            try {
                const payload = {
                    id_cotizacion: this.coti.cotizacion_id,
                    id_tipo_pago: parseInt(this.coti.id_tipo_pago),
                    total: parseFloat(this.nuevaCuota.monto),
                    fecha: this.nuevaCuota.fecha,
                    observacion: 'Cuota ' + (this.cuotas.length+1),
                    productos: this.coti.productos?.map(p=>({
                        id_producto:p.id_producto, cantidad:p.cantidad, precio:p.precio,
                        costo:p.costo||0, medida:p.medida||'Unidad', presenta:p.presenta||1, presenta_cnt:p.presenta_cnt||1
                    })) || [],
                    cuotas: [{monto:parseFloat(this.nuevaCuota.monto), fecha:this.nuevaCuota.fecha, tipo_pago:this.nuevaCuota.tipo_pago}],
                };
                const data = await apiPost(this.BASE+'/api/cotizaciones/editar', payload);
                if(data.res){
                    toastOk('Cuota registrada.');
                    this.showModal = false;
                    const cuotasData = await apiPost(this.BASE+'/api/cotizaciones/cuotas', {id_cotizacion:this.coti.cotizacion_id});
                    this.cuotas = Array.isArray(cuotasData) ? cuotasData : [];
                } else {
                    toastErr(data.msg||'Error al guardar cuota.');
                }
            } catch(e){ toastErr('Error de conexión.'); }
            finally{ this.guardandoCuota=false; }
        },
    }
}
</script>
@endpush

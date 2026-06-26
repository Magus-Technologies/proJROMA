@extends('layouts.app')
@section('title','Arqueo Diario')
@section('page-title','Arqueo Diario')
@section('breadcrumb','Cajas / Arqueo Diario')
@section('content')
<div class="mb-5 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha</label>
        <input id="fechaArqueo" type="date" value="{{ date('Y-m-d') }}"
               class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>
    <button onclick="cargarArqueo()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition">
        <i class="ti ti-search"></i> Consultar
    </button>
</div>

<div id="resultado" class="space-y-4">
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-8 text-center text-gray-400 text-sm">
        <i class="ti ti-calculator text-3xl block mb-2 text-gray-300"></i>
        Selecciona una fecha y presiona Consultar para ver el arqueo del día.
    </div>
</div>
@endsection
@push('scripts')
<script>
const BASE=BASE_URL;
async function cargarArqueo(){
    const fecha=document.getElementById('fechaArqueo').value;
    if(!fecha){toastWarn('Selecciona una fecha.');return;}
    document.getElementById('resultado').innerHTML='<div class="flex justify-center py-8"><i class="ti ti-loader-2 text-2xl text-blue-400 spin"></i></div>';
    const data=await apiPost(BASE+'/api/arqueo/cobros-dia',{fecha});
    if(!data||!data.length){
        document.getElementById('resultado').innerHTML='<div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-8 text-center text-gray-400 text-sm"><i class="ti ti-inbox text-3xl block mb-2 text-gray-300"></i>No hay cobros registrados para esta fecha.</div>';
        return;
    }
    let html=data.map(v=>`
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-3">
            <h4 class="text-sm font-semibold text-gray-700">${v.usuario||'Sin nombre'}</h4>
            <span class="font-bold text-blue-700">Total: S/ ${parseFloat(v.total||0).toFixed(2)}</span>
        </div>
        <div class="grid grid-cols-3 gap-4 p-5">
            <div class="text-center">
                <p class="text-xs text-gray-400 mb-1">Efectivo</p>
                <p class="text-xl font-bold text-emerald-600">S/ ${parseFloat(v.efectivo||0).toFixed(2)}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400 mb-1">Bancos/Digital</p>
                <p class="text-xl font-bold text-blue-600">S/ ${parseFloat(v.bancos||0).toFixed(2)}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400 mb-1">Total</p>
                <p class="text-xl font-bold text-gray-800">S/ ${parseFloat(v.total||0).toFixed(2)}</p>
            </div>
        </div>
        ${v.pagos_digitales_sistema?.length?`
        <div class="border-t border-gray-100 px-5 pb-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 mt-3">Pagos Digitales</p>
            <table class="w-full text-xs">
                <thead class="bg-gray-50"><tr><th class="px-2 py-1 text-left">Cliente</th><th class="px-2 py-1 text-left">Tipo</th><th class="px-2 py-1 text-right">Monto</th></tr></thead>
                <tbody>${v.pagos_digitales_sistema.map(p=>`<tr class="border-t border-gray-50"><td class="px-2 py-1">${p.cliente_nombre}</td><td class="px-2 py-1">${p.tipo_pago}</td><td class="px-2 py-1 text-right">S/ ${parseFloat(p.monto||0).toFixed(2)}</td></tr>`).join('')}</tbody>
            </table>
        </div>`:''}
    </div>`).join('');
    document.getElementById('resultado').innerHTML=html;
}
</script>
@endpush

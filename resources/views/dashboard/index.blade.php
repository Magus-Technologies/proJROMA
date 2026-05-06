{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('breadcrumb','Inicio / Panel principal')

@section('content')

{{-- KPI Cards --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="card-hover col-span-1 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white shadow-lg">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-200 mb-1">Ventas del Mes</p>
                <p class="text-2xl font-extrabold">S/ {{ number_format($ventasMes, 2) }}</p>
                <p class="text-[10px] text-blue-200 mt-1">{{ now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15">
                <i class="ti ti-trending-up text-lg"></i>
            </div>
        </div>
    </div>

    <div class="card-hover col-span-1 rounded-2xl bg-gradient-to-br from-violet-600 to-violet-700 p-5 text-white shadow-lg">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-violet-200 mb-1">Compras del Mes</p>
                <p class="text-2xl font-extrabold">S/ {{ number_format($comprasMes, 2) }}</p>
                <p class="text-[10px] text-violet-200 mt-1">{{ now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15">
                <i class="ti ti-shopping-cart text-lg"></i>
            </div>
        </div>
    </div>

    <div class="card-hover col-span-1 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-700 p-5 text-white shadow-lg">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-emerald-200 mb-1">Clientes</p>
                <p class="text-2xl font-extrabold">{{ number_format($clientesTotales) }}</p>
                <p class="text-[10px] text-emerald-200 mt-1">Total registrados</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15">
                <i class="ti ti-users text-lg"></i>
            </div>
        </div>
    </div>

    <div class="card-hover col-span-1 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-5 text-white shadow-lg">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-amber-200 mb-1">Pedidos Pend.</p>
                <p class="text-2xl font-extrabold">{{ number_format($pedidosPendientes) }}</p>
                <p class="text-[10px] text-amber-200 mt-1">Sin convertir</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15">
                <i class="ti ti-clipboard-list text-lg"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-5">

    {{-- Gráfico ventas --}}
    <div class="xl:col-span-2 rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700">Ventas — Últimos 30 días</h3>
            <span class="text-[10px] text-gray-400">Actualizado {{ now()->format('d/m H:i') }}</span>
        </div>
        <canvas id="chartVentas" height="110"></canvas>
    </div>

    {{-- Top clientes --}}
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Clientes del Mes</h3>
        <div class="space-y-3">
            @forelse($topClientes as $i => $c)
                <div class="flex items-center gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[10px] font-bold
                                {{ $i===0?'bg-amber-100 text-amber-700':($i===1?'bg-slate-100 text-slate-600':'bg-blue-50 text-blue-600') }}">
                        {{ $i+1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 truncate">{{ $c->nombre }}</p>
                        <div class="mt-1 h-1 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full bg-blue-500"
                                 style="width:{{ $topClientes->count() ? ($c->total/$topClientes->first()->total*100) : 0 }}%"></div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-700 shrink-0">S/ {{ number_format($c->total,0) }}</span>
                </div>
            @empty
                <p class="py-6 text-center text-xs text-gray-400">Sin ventas este mes</p>
            @endforelse
        </div>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

    {{-- Últimas ventas --}}
    <div class="xl:col-span-2 rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-gray-700">Últimas Ventas</h3>
            <a href="{{ route('ventas.index') }}" class="text-xs font-medium text-blue-600 hover:underline">Ver todas →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-medium">Documento</th>
                        <th class="px-4 py-2.5 text-left font-medium">Cliente</th>
                        <th class="px-4 py-2.5 text-left font-medium">Fecha</th>
                        <th class="px-4 py-2.5 text-right font-medium">Total</th>
                        <th class="px-4 py-2.5 text-center font-medium">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($ultimasVentas as $v)
                        <tr>
                            <td class="px-4 py-2.5 font-mono font-semibold text-blue-700">{{ $v->documento_completo }}</td>
                            <td class="px-4 py-2.5 text-gray-600 max-w-[140px] truncate">{{ $v->cliente?->datos ?? '-' }}</td>
                            <td class="px-4 py-2.5 text-gray-500">{{ $v->fecha_emision?->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5 text-right font-semibold">S/ {{ number_format($v->total,2) }}</td>
                            <td class="px-4 py-2.5 text-center">
                                @if($v->estado==='1')
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Activa</span>
                                @else
                                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Anulada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Sin ventas registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bajo stock --}}
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-gray-700">⚠️ Bajo Stock</h3>
            <a href="{{ route('almacen.index') }}" class="text-xs font-medium text-blue-600 hover:underline">Ver →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($bajoStock as $p)
                <div class="flex items-center gap-3 px-4 py-2.5">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $p->cantidad<=0?'bg-red-100':'bg-amber-50' }}">
                        <i class="ti ti-package text-xs {{ $p->cantidad<=0?'text-red-500':'text-amber-500' }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="truncate text-xs font-medium text-gray-700">{{ $p->descripcion }}</p>
                        <p class="text-[10px] text-gray-400">{{ $p->codigo }}</p>
                    </div>
                    <span class="shrink-0 text-xs font-bold {{ $p->cantidad<=0?'text-red-600':'text-amber-600' }}">{{ $p->cantidad }}</span>
                </div>
            @empty
                <div class="px-5 py-8 text-center">
                    <i class="ti ti-circle-check text-2xl text-emerald-400"></i>
                    <p class="mt-1 text-xs text-gray-400">Stock en buen estado</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    const d = @json($ventasDiarias);
    new Chart(document.getElementById('chartVentas'),{
        type:'line',
        data:{
            labels: d.map(x=>{ const[,m,dd]=x.fecha.split('-'); return `${dd}/${m}`; }),
            datasets:[{ label:'Ventas (S/)', data:d.map(x=>+x.total), fill:true,
                backgroundColor:'rgba(59,130,246,.08)', borderColor:'#3b82f6',
                borderWidth:2, tension:.4, pointRadius:3, pointBackgroundColor:'#3b82f6' }]
        },
        options:{
            responsive:true, plugins:{ legend:{display:false},
                tooltip:{ callbacks:{ label:c=>'S/ '+c.parsed.y.toFixed(2) } } },
            scales:{
                x:{ grid:{display:false}, ticks:{font:{size:9}} },
                y:{ grid:{color:'#f1f5f9'}, ticks:{font:{size:9}, callback:v=>'S/ '+v.toLocaleString()} }
            }
        }
    });
})();
</script>
@endpush

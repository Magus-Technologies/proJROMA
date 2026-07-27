@php
    use App\Services\CajaService;
    $pagos = $venta->pagosMetodos()->orderBy('id')->get();
@endphp

<div class="space-y-4 text-sm">
    @forelse ($pagos as $pago)
        <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">
            <div class="flex items-center justify-between">
                <span class="font-semibold">{{ CajaService::etiquetaMetodoPago($pago->metodo_pago) }}</span>
                <span class="font-semibold">S/ {{ number_format($pago->monto, 2) }}</span>
            </div>

            @if ($pago->referencia)
                <div class="mt-1 text-gray-500">N° de operación: {{ $pago->referencia }}</div>
            @endif

            @if (! empty($pago->comprobantes))
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($pago->comprobantes as $ruta)
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($ruta) }}"
                           target="_blank" rel="noopener">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($ruta) }}"
                                 alt="Comprobante"
                                 class="h-24 w-24 rounded-md object-cover border border-gray-200 dark:border-white/10">
                        </a>
                    @endforeach
                </div>
            @else
                <div class="mt-1 text-gray-400">Sin comprobantes.</div>
            @endif
        </div>
    @empty
        <div class="text-gray-500">Esta venta no tiene pagos registrados por método.</div>
    @endforelse
</div>

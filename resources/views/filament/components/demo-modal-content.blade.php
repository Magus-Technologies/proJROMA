<div class="space-y-3">
    <div class="flex justify-between">
        <span class="text-gray-500 text-sm">Documento:</span>
        <span class="font-semibold">{{ $documento }}</span>
    </div>
    <div class="flex justify-between">
        <span class="text-gray-500 text-sm">Cliente:</span>
        <span>{{ $cliente }}</span>
    </div>
    <div class="flex justify-between">
        <span class="text-gray-500 text-sm">Fecha:</span>
        <span>{{ $fecha }}</span>
    </div>
    <div class="flex justify-between">
        <span class="text-gray-500 text-sm">Total:</span>
        <span class="font-bold text-lg">{{ $total }}</span>
    </div>
    <div class="flex justify-between">
        <span class="text-gray-500 text-sm">Estado:</span>
        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">
            {{ $estado }}
        </span>
    </div>
</div>

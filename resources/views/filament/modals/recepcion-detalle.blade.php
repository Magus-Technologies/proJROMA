<table class="w-full text-sm">
    <thead>
        <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-gray-500 dark:text-gray-400">
            <th class="py-2 pr-3">Código</th>
            <th class="py-2 pr-3">Producto</th>
            <th class="py-2 pr-3">Unidad</th>
            <th class="py-2 text-right">Cantidad recibida</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($lineas as $linea)
            <tr class="border-b border-gray-100 dark:border-gray-800">
                <td class="py-2 pr-3 font-mono text-xs">{{ $linea->codigo ?: '—' }}</td>
                <td class="py-2 pr-3">{{ $linea->producto }}</td>
                <td class="py-2 pr-3">{{ $linea->unidad ?: '—' }}</td>
                <td class="py-2 text-right font-medium">{{ $linea->cantidad }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="py-6 text-center text-gray-400">Sin líneas registradas</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="space-y-4">
    <div class="grid grid-cols-3 gap-4 text-sm">
        <div>
            <p class="text-gray-500 dark:text-gray-400">Total</p>
            <p class="font-bold text-gray-900 dark:text-white">S/ {{ number_format($total, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-500 dark:text-gray-400">Pagado</p>
            <p class="font-bold text-green-600 dark:text-green-400">S/ {{ number_format($pagado, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-500 dark:text-gray-400">Saldo</p>
            <p class="font-bold text-red-600 dark:text-red-400">S/ {{ number_format(max(0, $total - $pagado), 2) }}</p>
        </div>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-gray-500 dark:text-gray-400">
                <th class="py-2 pr-3">Fecha</th>
                <th class="py-2 pr-3 text-right">Monto</th>
                <th class="py-2 pr-3">Método</th>
                <th class="py-2">Estado</th>
                <th class="py-2 text-center">Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pagos as $pago)
                <tr
                    x-data="{
                        editing: false,
                        monto: {{ $pago->monto }},
                        metodo: '{{ $pago->instrumento_tipo ?? '' }}',
                        originalMonto: {{ $pago->monto }},
                        originalMetodo: '{{ $pago->instrumento_tipo ?? '' }}',
                        startEdit() {
                            this.monto = this.originalMonto;
                            this.metodo = this.originalMetodo;
                            this.editing = true;
                        },
                        cancelEdit() {
                            this.editing = false;
                            this.monto = this.originalMonto;
                            this.metodo = this.originalMetodo;
                        },
                        saveEdit() {
                            $wire.dispatch('editar-pago', {
                                id: {{ $pago->dias_compra_id }},
                                monto: this.monto,
                                metodo: this.metodo
                            });
                            this.editing = false;
                        }
                    }"
                    class="border-b border-gray-100 dark:border-gray-800">
                    <td class="py-2 pr-3">{{ $pago->fecha?->format('d/m/Y') ?? '—' }}</td>
                    <td class="py-2 pr-3 text-right font-medium">
                        <template x-if="!editing">
                            <span>S/ {{ number_format($pago->monto, 2) }}</span>
                        </template>
                        <template x-if="editing">
                            <input type="number" x-model="monto" step="0.01" min="0.01"
                                class="w-24 text-right rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm">
                        </template>
                    </td>
                    <td class="py-2 pr-3">
                        <template x-if="!editing">
                            <span x-text="metodo || '—'"></span>
                        </template>
                        <template x-if="editing">
                            <select x-model="metodo"
                                class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm">
                                <option value="">Seleccione</option>
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                                @php
                                    $walletTipos = \App\Models\BilleteraTipo::where('estado', '1')
                                        ->where('id_empresa', (int) session('id_empresa'))
                                        ->pluck('nombre');
                                @endphp
                                @foreach ($walletTipos as $wt)
                                    <option value="{{ strtoupper($wt) }}">{{ $wt }}</option>
                                @endforeach
                            </select>
                        </template>
                    </td>
                    <td class="py-2">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $pago->estado === '1'
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                            {{ $pago->estado === '1' ? 'Activo' : 'Anulado' }}
                        </span>
                    </td>
                    <td class="py-2 text-center">
                        @if ($pago->estado === '1')
                            <div class="inline-flex items-center gap-1">
                                <template x-if="!editing">
                                    <button x-on:click="startEdit"
                                        class="inline-flex items-center justify-center text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-200"
                                        title="Editar pago">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                </template>
                                <template x-if="editing">
                                    <button x-on:click="saveEdit"
                                        class="inline-flex items-center justify-center text-success-600 hover:text-success-900 dark:text-success-400 dark:hover:text-success-200"
                                        title="Guardar cambios">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </template>
                                <template x-if="editing">
                                    <button x-on:click="cancelEdit"
                                        class="inline-flex items-center justify-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                        title="Cancelar edición">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </template>
                                <button
                                    x-on:click="
                                        if (confirm('¿Anular pago de S/ {{ number_format($pago->monto, 2) }} del {{ $pago->fecha?->format('d/m/Y') }}?')) {
                                            $wire.dispatch('anular-pago', { id: {{ $pago->dias_compra_id }} });
                                        }
                                    "
                                    class="inline-flex items-center justify-center text-danger-600 hover:text-danger-900 dark:text-danger-400 dark:hover:text-danger-200"
                                    title="Anular pago">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-gray-400">Sin pagos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

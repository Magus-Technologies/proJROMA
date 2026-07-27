@php
    $data = app(\App\Filament\Pages\LibroDiario::class)->getData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Filtros --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                    <input type="date" name="desde" value="{{ $data['desde'] }}"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                    <input type="date" name="hasta" value="{{ $data['hasta'] }}"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ $data['search'] }}"
                        placeholder="N° o glosa..."
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm w-48">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Filtrar
                </button>
            </form>
        </div>

        {{-- Nuevo Asiento --}}
        <div x-data="asientoForm()" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Nuevo Asiento Contable</h3>
            </div>
            <form method="POST" action="{{ route('contabilidad.asientos.store') }}" class="p-4 space-y-4">
                @csrf
                <input type="hidden" name="desde" value="{{ $data['desde'] }}">
                <input type="hidden" name="hasta" value="{{ $data['hasta'] }}">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha</label>
                        <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Glosa</label>
                        <input type="text" name="glosa" required maxlength="500"
                            placeholder="Descripción del asiento..."
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tipo</label>
                        <select name="tipo"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
                            @foreach(\App\Models\AsientoContable::tipos() as $v => $l)
                                <option value="{{ $v }}" @selected($v === 'operaciones')>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Detalle (Debe / Haber)</label>
                        <button type="button" @click="addRow()"
                            class="text-xs px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition">
                            + Agregar línea
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                                <tr>
                                    <th class="text-left px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 w-1/2">Cuenta Contable</th>
                                    <th class="text-right px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 w-1/6">Debe</th>
                                    <th class="text-right px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 w-1/6">Haber</th>
                                    <th class="text-left px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400">Glosa</th>
                                    <th class="px-3 py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="(row, i) in rows" :key="i">
                                    <tr>
                                        <td class="px-3 py-1.5">
                                            <select :name="`detalle[${i}][cuenta_id]`" x-model="row.cuenta_id" required
                                                class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-2 py-1.5 text-xs">
                                                <option value="">Seleccionar cuenta</option>
                                                @foreach($data['cuentas'] as $cuenta)
                                                    <option value="{{ $cuenta->id }}">{{ $cuenta->codigo }} - {{ $cuenta->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-3 py-1.5">
                                            <input type="number" step="0.01" min="0"
                                                :name="`detalle[${i}][debe]`" x-model="row.debe" @input="calcTotales()"
                                                class="w-full text-right rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-2 py-1.5 text-xs">
                                        </td>
                                        <td class="px-3 py-1.5">
                                            <input type="number" step="0.01" min="0"
                                                :name="`detalle[${i}][haber]`" x-model="row.haber" @input="calcTotales()"
                                                class="w-full text-right rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-2 py-1.5 text-xs">
                                        </td>
                                        <td class="px-3 py-1.5">
                                            <input type="text" :name="`detalle[${i}][glosa]`" x-model="row.glosa"
                                                placeholder="Glosa (opcional)"
                                                class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-2 py-1.5 text-xs">
                                        </td>
                                        <td class="px-3 py-1.5 text-center">
                                            <button type="button" @click="removeRow(i)" x-show="rows.length > 2"
                                                class="text-red-400 hover:text-red-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-900 border-t-2 border-gray-300 dark:border-gray-600">
                                <tr>
                                    <td class="px-3 py-2 text-xs font-semibold text-gray-600 dark:text-gray-400">TOTALES</td>
                                    <td class="px-3 py-2 text-right text-sm font-bold text-blue-700 dark:text-blue-300" x-text="formatMoney(totalDebe)">0.00</td>
                                    <td class="px-3 py-2 text-right text-sm font-bold text-green-700 dark:text-green-300" x-text="formatMoney(totalHaber)">0.00</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr x-show="!balanceOk && totalDebe > 0" x-transition>
                                    <td colspan="5" class="px-3 py-1.5 text-xs text-red-500 font-medium">
                                        ⚠ El Debe debe ser igual al Haber
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-xs text-gray-400 dark:text-gray-500 self-center">
                        N° Asiento: <span class="font-mono font-bold">{{ $data['next_numero'] }}</span>
                    </div>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Guardar Asiento
                    </button>
                </div>
            </form>

            @if($errors->any())
                <div class="px-4 pb-4">
                    @foreach($errors->all() as $e)
                        <p class="text-sm text-red-500">{{ $e }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Lista de Asientos --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                    Asientos Contables
                    <span class="text-xs font-normal text-gray-400 dark:text-gray-500 ml-2">({{ $data['asientos']->count() }})</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="text-left px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">N°</th>
                            <th class="text-left px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Fecha</th>
                            <th class="text-left px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Glosa</th>
                            <th class="text-left px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Tipo</th>
                            <th class="text-right px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Debe</th>
                            <th class="text-right px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Haber</th>
                            <th class="text-center px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400">Estado</th>
                            <th class="px-4 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($data['asientos'] as $a)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-2.5 font-mono text-sm font-medium text-gray-900 dark:text-gray-100">{{ $a->numero }}</td>
                                <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400">{{ $a->fecha->format('d/m/Y') }}</td>
                                <td class="px-4 py-2.5 max-w-xs">
                                    <p class="text-gray-800 dark:text-gray-200 truncate">{{ $a->glosa }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $a->user?->nombre_completo ?? $a->user?->nombres ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $a->tipo === 'apertura' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : ($a->tipo === 'cierre' ? 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300' : ($a->tipo === 'ajuste' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300')) }}">
                                        {{ \App\Models\AsientoContable::tipos()[$a->tipo] ?? $a->tipo }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right font-mono text-sm text-blue-700 dark:text-blue-300">S/ {{ number_format($a->total_debe, 2) }}</td>
                                <td class="px-4 py-2.5 text-right font-mono text-sm text-green-700 dark:text-green-300">S/ {{ number_format($a->total_haber, 2) }}</td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $a->estado === 'definitivo' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : ($a->estado === 'anulado' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300') }}">
                                        {{ \App\Models\AsientoContable::estados()[$a->estado] ?? $a->estado }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    @if($a->estado !== 'anulado')
                                        <form method="POST" action="{{ route('contabilidad.asientos.anular', $a->id) }}" class="inline"
                                              onsubmit="return confirm('¿Anular este asiento?')">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs text-red-400 hover:text-red-600 transition">Anular</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @if($a->detalle->count() > 0)
                                <tr class="bg-gray-50/50 dark:bg-gray-900/30">
                                    <td colspan="8" class="px-6 py-1.5">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
                                            @foreach($a->detalle as $d)
                                                <div class="flex gap-4">
                                                    <span class="w-24 font-mono text-gray-400">{{ $d->cuenta?->codigo }}</span>
                                                    <span class="flex-1 text-gray-600 dark:text-gray-300">{{ $d->cuenta?->nombre }}</span>
                                                    @if($d->debe > 0)<span class="w-24 text-right font-mono text-blue-600">S/ {{ number_format($d->debe, 2) }}</span>@endif
                                                    @if($d->haber > 0)<span class="w-24 text-right font-mono text-green-600">S/ {{ number_format($d->haber, 2) }}</span>@endif
                                                    @if($d->glosa)<span class="text-gray-400 italic ml-2">{{ $d->glosa }}</span>@endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-400">No hay asientos en el período seleccionado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function asientoForm() {
        return {
            rows: [{ cuenta_id: '', debe: 0, haber: 0, glosa: '' }, { cuenta_id: '', debe: 0, haber: 0, glosa: '' }],
            totalDebe: 0,
            totalHaber: 0,
            balanceOk: true,

            addRow() {
                this.rows.push({ cuenta_id: '', debe: 0, haber: 0, glosa: '' });
            },

            removeRow(i) {
                if (this.rows.length > 2) {
                    this.rows.splice(i, 1);
                    this.calcTotales();
                }
            },

            calcTotales() {
                this.totalDebe = this.rows.reduce((s, r) => s + parseFloat(r.debe || 0), 0);
                this.totalHaber = this.rows.reduce((s, r) => s + parseFloat(r.haber || 0), 0);
                this.balanceOk = Math.abs(this.totalDebe - this.totalHaber) < 0.01;
            },

            formatMoney(v) {
                return 'S/ ' + parseFloat(v || 0).toFixed(2);
            }
        }
    }
    </script>
    @endpush
</x-filament-panels::page>

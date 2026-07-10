@php
    $nomOrig    = $almacenes[$traslado->almacen_origen] ?? $traslado->almacen_origen;
    $nomDest    = $almacenes[$traslado->almacen_destino] ?? $traslado->almacen_destino;
    $docActivo  = (string) $traslado->estado === '1';
    $totalCant  = $lineas->where('estado', '1')->sum('cantidad');
@endphp

<div class="space-y-4" style="font-size:.875rem;overflow-x:auto"
    x-data="{
        confirmMessage: '',
        confirmHandler: null,
        confirmTitle: '',
        openConfirm(title, message, handler) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmHandler = handler;
            this.$dispatch('open-modal', { id: 'confirmar-traslado-linea' });
        }
    }">
    {{-- Cabecera del documento --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px">
        <div style="border-radius:12px;border:1px solid #e5e7eb;background:#fff;padding:14px">
            <p style="font-size:.75rem;color:#6b7280;margin:0 0 4px">Fecha</p>
            <p style="font-weight:600;margin:0;color:#111827">
                {{ optional($traslado->fecha)->format('d/m/Y H:i') ?? '—' }}
            </p>
        </div>
        <div style="border-radius:12px;border:1px solid #fecaca;background:#fef2f2;padding:14px">
            <p style="font-size:.75rem;color:#dc2626;margin:0 0 4px">Origen</p>
            <p style="font-weight:600;margin:0;color:#991b1b">{{ $nomOrig }}</p>
        </div>
        <div style="border-radius:12px;border:1px solid #bbf7d0;background:#f0fdf4;padding:14px">
            <p style="font-size:.75rem;color:#16a34a;margin:0 0 4px">Destino</p>
            <p style="font-weight:600;margin:0;color:#166534">{{ $nomDest }}</p>
        </div>
        <div style="border-radius:12px;border:1px solid #e5e7eb;background:#fff;padding:14px">
            <p style="font-size:.75rem;color:#6b7280;margin:0 0 4px">Estado</p>
            <p style="font-weight:600;margin:0;color:{{ $docActivo ? '#16a34a' : '#dc2626' }}">
                {{ $docActivo ? 'Activo' : 'Anulado' }}
            </p>
        </div>
    </div>

    {{-- Detalle de productos --}}
    <div style="overflow-x:auto;border-radius:8px;border:1px solid #e5e7eb;min-width:fit-content">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f9fafb;text-align:left">
                    <th style="padding:8px 10px;font-weight:500;font-size:.75rem;color:#6b7280;width:36px">#</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.75rem;color:#6b7280">Código</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.75rem;color:#6b7280">Producto</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.75rem;color:#6b7280;text-align:center">Unidad</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.75rem;color:#6b7280;text-align:center">Cantidad</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.75rem;color:#6b7280;text-align:center">Stk origen</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.75rem;color:#6b7280;text-align:center">Stk destino</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.75rem;color:#6b7280;text-align:right">Costo</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.75rem;color:#6b7280;text-align:center">Estado</th>
                    @if ($docActivo)
                        <th style="padding:8px 10px;font-weight:500;font-size:.75rem;color:#6b7280;text-align:center;width:150px">Acción</th>
                    @endif
                </tr>
            </thead>
            <tbody style="border-top:1px solid #e5e7eb">
                @forelse ($lineas as $i => $l)
                    @php
                        $lineaActiva = (string) ($l->estado ?? '1') === '1';
                        $costo = $l->costo ?? $l->costo_actual;
                    @endphp
                    <tr style="border-bottom:1px solid #f3f4f6;{{ $lineaActiva ? '' : 'opacity:.55' }}"
                        x-data="{ editando: false, cantidad: {{ (int) $l->cantidad }} }">
                        <td style="padding:8px 10px;color:#6b7280">{{ $i + 1 }}</td>
                        <td style="padding:8px 10px;color:#6b7280">{{ $l->codigo ?: '—' }}</td>
                        <td style="padding:8px 10px;font-weight:500;{{ $lineaActiva ? '' : 'text-decoration:line-through' }}">
                            {{ $l->descripcion ?? 'Producto #' . $l->id_producto }}
                        </td>
                        <td style="padding:8px 10px;text-align:center">{{ $l->medida ?: '—' }}</td>
                        <td style="padding:8px 10px;text-align:center;font-weight:600">
                            @if ($lineaActiva && $docActivo)
                                <template x-if="!editando">
                                    <span x-text="cantidad"></span>
                                </template>
                                <template x-if="editando">
                                    <input type="text" inputmode="numeric" x-model="cantidad"
                                        x-on:input="cantidad = cantidad.toString().replace(/[^0-9]/g, '')"
                                        x-on:keydown.enter.prevent
                                        style="width:64px;text-align:center;border:1px solid #3b82f6;border-radius:6px;padding:4px 6px;font-size:.8rem">
                                </template>
                            @else
                                {{ (int) $l->cantidad }}
                            @endif
                        </td>
                        <td style="padding:8px 10px;text-align:center;font-size:.8rem;color:#6b7280">
                            {{ (int) $l->stock_ant_origen }} → <span style="color:#dc2626">{{ (int) $l->stock_nuevo_origen }}</span>
                        </td>
                        <td style="padding:8px 10px;text-align:center;font-size:.8rem;color:#6b7280">
                            {{ (int) $l->stock_ant_destino }} → <span style="color:#16a34a">{{ (int) $l->stock_nuevo_destino }}</span>
                        </td>
                        <td style="padding:8px 10px;text-align:right">
                            {{ $costo !== null ? 'S/ ' . number_format((float) $costo, 2) : '—' }}
                        </td>
                        <td style="padding:8px 10px;text-align:center">
                            <span style="display:inline-block;padding:2px 8px;border-radius:999px;font-size:.7rem;font-weight:500;background:{{ $lineaActiva ? '#dcfce7' : '#fee2e2' }};color:{{ $lineaActiva ? '#16a34a' : '#dc2626' }}">
                                {{ $lineaActiva ? 'Activo' : 'Anulado' }}
                            </span>
                        </td>
                        @if ($docActivo)
                            <td style="padding:8px 10px;text-align:center;white-space:nowrap">
                                @if ($lineaActiva)
                                    <template x-if="!editando">
                                        <button type="button"
                                            x-on:click="editando = true"
                                            style="background:none;border:1px solid #3b82f6;color:#3b82f6;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:.75rem;margin-right:4px">
                                            Editar
                                        </button>
                                    </template>
                                    <template x-if="editando">
                                        <button type="button"
                                            x-on:click="if (! parseInt(cantidad)) return; openConfirm('Guardar cambio', '¿Cambiar la cantidad a ' + cantidad + ' unidades? El stock se ajustará por la diferencia.', () => { $wire.editarLineaTraslado({{ $l->id_detalle }}, parseInt(cantidad)); editando = false })"
                                            style="background:#16a34a;color:#fff;border:none;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:.75rem;margin-right:4px">
                                            Guardar
                                        </button>
                                    </template>
                                    <button type="button"
                                        x-on:click="openConfirm('Anular producto', '¿Anular \'{{ addslashes($l->descripcion ?? 'Producto #' . $l->id_producto) }}\'? Sus {{ (int) $l->cantidad }} unidades regresan al almacén origen.', () => { $wire.anularLineaTraslado({{ $l->id_detalle }}) })"
                                        style="background:none;border:1px solid #ef4444;color:#ef4444;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:.75rem">
                                        Anular
                                    </button>
                                @else
                                    <span style="color:#9ca3af;font-size:.75rem">—</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $docActivo ? 10 : 9 }}" style="padding:24px;text-align:center;color:#9ca3af">
                            Sin productos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($lineas->isNotEmpty())
                <tfoot>
                    <tr style="background:#f9fafb;border-top:1px solid #e5e7eb">
                        <td colspan="4" style="padding:8px 10px;font-weight:600;text-align:right;color:#111827">Total unidades (activas)</td>
                        <td style="padding:8px 10px;text-align:center;font-weight:700;color:#111827">{{ (int) $totalCant }}</td>
                        <td colspan="{{ $docActivo ? 5 : 4 }}"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    {{-- Observación / usuario --}}
    <div style="display:flex;justify-content:space-between;gap:12px;font-size:.8rem;color:#6b7280">
        <span>
            @if ($traslado->observacion)
                <strong>Observaciones:</strong> {{ $traslado->observacion }}
            @endif
        </span>
        <span>
            <strong>Usuario:</strong> {{ $traslado->usuario->nombres ?? '—' }}
        </span>
    </div>

    {{-- Confirmación: modal nativo de Filament, teleportado al body --}}
    <x-filament::modal
        id="confirmar-traslado-linea"
        teleport="body"
        width="md"
        alignment="center"
        footer-actions-alignment="center"
        icon="heroicon-o-exclamation-triangle"
        icon-color="warning"
    >
        <x-slot name="heading">
            <span x-text="confirmTitle"></span>
        </x-slot>

        <x-slot name="description">
            <span x-text="confirmMessage"></span>
        </x-slot>

        <x-slot name="footerActions">
            <x-filament::button
                color="gray"
                x-on:click="$dispatch('close-modal', { id: 'confirmar-traslado-linea' })"
            >
                Cancelar
            </x-filament::button>

            <x-filament::button
                color="warning"
                x-on:click="$dispatch('close-modal', { id: 'confirmar-traslado-linea' }); if (confirmHandler) confirmHandler()"
            >
                Confirmar
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</div>

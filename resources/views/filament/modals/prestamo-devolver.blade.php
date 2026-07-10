@php
    $totalPrestado = $lineas->sum('prestado');
    $totalDevuelto = $lineas->sum(fn ($l) => $l->prestado - $l->pendiente);
    $totalPendiente = $lineas->sum('pendiente');
    $tipoLabel = $record->tipo === 'P' ? 'Presté' : 'Me prestaron';
    $tipoColor = $record->tipo === 'P' ? '#ef4444' : '#22c55e';
@endphp

<div class="space-y-4" style="font-size:.875rem"
    x-data="{
        confirmMessage: '',
        confirmHandler: null,
        confirmTitle: '',
        openConfirm(title, message, handler) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmHandler = handler;
            this.$dispatch('open-modal', { id: 'confirmar-devolucion' });
        }
    }">
    {{-- Cards informativos --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px">
        <div style="border-radius:12px;border:1px solid #e5e7eb;background:#fff;padding:16px">
            <p style="font-size:.8rem;color:#6b7280;margin:0 0 4px">Total prestado</p>
            <p style="font-size:1.25rem;font-weight:700;margin:0;color:#111827">
                {{ number_format($totalPrestado, 0) }}
            </p>
            <p style="font-size:.75rem;color:#9ca3af;margin:4px 0 0">
                {{ $tipoLabel }}
                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;margin-left:4px;background:{{ $tipoColor }}"></span>
            </p>
        </div>
        <div style="border-radius:12px;border:1px solid #dbeafe;background:#eff6ff;padding:16px">
            <p style="font-size:.8rem;color:#3b82f6;margin:0 0 4px">Devuelto</p>
            <p style="font-size:1.25rem;font-weight:700;margin:0;color:#1d4ed8">
                {{ number_format($totalDevuelto, 0) }}
            </p>
            <p style="font-size:.75rem;color:#9ca3af;margin:4px 0 0">
                {{ $devoluciones->count() }} devolución(es)
            </p>
        </div>
        <div style="border-radius:12px;border:1px solid {{ $totalPendiente <= 0 ? '#bbf7d0' : '#fecaca' }};background:{{ $totalPendiente <= 0 ? '#f0fdf4' : '#fef2f2' }};padding:16px">
            <p style="font-size:.8rem;color:{{ $totalPendiente <= 0 ? '#16a34a' : '#dc2626' }};margin:0 0 4px">Pendiente</p>
            <p style="font-size:1.25rem;font-weight:700;margin:0;color:{{ $totalPendiente <= 0 ? '#16a34a' : '#dc2626' }}">
                {{ number_format($totalPendiente, 0) }}
            </p>
            <p style="font-size:.75rem;color:#9ca3af;margin:4px 0 0">
                {{ $totalPendiente <= 0 ? 'Completamente devuelto' : 'Por devolver' }}
            </p>
        </div>
    </div>

    {{-- Tabla única: devoluciones --}}
    <p style="font-weight:600;margin:0 0 6px">Devoluciones</p>
    <div style="overflow-x:auto;border-radius:8px;border:1px solid #e5e7eb">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f9fafb;text-align:left">
                    <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280;width:50px">#</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280">Producto</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280;text-align:center;width:100px">Cantidad</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280;text-align:center;width:120px">Fecha</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280;text-align:center;width:90px">Estado</th>
                    <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280;text-align:center;width:140px">Acción</th>
                </tr>
            </thead>
            <tbody style="border-top:1px solid #e5e7eb">
                @forelse ($devoluciones as $d)
                    @php
                        $productoNombre = $lineas->firstWhere('id_producto', $d->id_producto)?->producto ?? $d->producto;
                    @endphp
                    <tr style="border-bottom:1px solid #f3f4f6" x-data="{ editando: false, cantidad: {{ (int) $d->cantidad }} }">
                        <td style="padding:8px 10px;color:#6b7280">{{ $d->id_devolucion }}</td>
                        <td style="padding:8px 10px;font-weight:500">{{ $productoNombre }}</td>
                        <td style="padding:8px 10px;text-align:center">
                            <template x-if="!editando">
                                <span x-text="cantidad" style="font-weight:600"></span>
                            </template>
                            <template x-if="editando">
                                <input type="number" x-model="cantidad" min="1"
                                    style="width:70px;text-align:center;border:1px solid #3b82f6;border-radius:6px;padding:4px 6px;font-size:.8rem">
                            </template>
                        </td>
                        <td style="padding:8px 10px;text-align:center;white-space:nowrap">
                            {{ \Carbon\Carbon::parse($d->fecha)->format('d/m/Y H:i') }}
                        </td>
                        <td style="padding:8px 10px;text-align:center">
                            <span style="display:inline-block;padding:2px 8px;border-radius:999px;font-size:.75rem;font-weight:500;background:#dcfce7;color:#16a34a">
                                Activo
                            </span>
                        </td>
                        <td style="padding:8px 10px;text-align:center;white-space:nowrap">
                            <template x-if="!editando">
                                <button type="button"
                                    x-on:click="editando = true"
                                    style="background:none;border:1px solid #3b82f6;color:#3b82f6;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:.8rem;margin-right:4px">
                                    Editar
                                </button>
                            </template>
                            <template x-if="editando">
                                <button type="button"
                                    x-on:click="openConfirm('Guardar cambio', '¿Guardar cambio a ' + cantidad + ' unidades?', () => { $wire.editarDevolucion({{ $d->id_devolucion }}, {{ $d->id_prestamo }}, cantidad); editando = false })"
                                    style="background:#16a34a;color:#fff;border:none;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:.8rem;margin-right:4px">
                                    Guardar
                                </button>
                            </template>
                            <button type="button"
                                x-on:click="openConfirm('Anular devolución', '¿Anular devolución #{{ $d->id_devolucion }}? Se revertirá el movimiento de stock.', () => { $wire.anularDevolucion({{ $d->id_devolucion }}, {{ $d->id_prestamo }}) })"
                                style="background:none;border:1px solid #ef4444;color:#ef4444;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:.8rem">
                                Anular
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:24px;text-align:center;color:#9ca3af;font-size:.875rem">
                            Sin devoluciones registradas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Resumen por producto + botón Devolver --}}
    @if ($totalPendiente > 0)
        <p style="font-weight:600;margin:16px 0 6px">Registrar devolución</p>
        <div style="overflow-x:auto;border-radius:8px;border:1px solid #e5e7eb">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:#f9fafb;text-align:left">
                        <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280">Producto</th>
                        <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280;text-align:center;width:80px">Prestado</th>
                        <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280;text-align:center;width:80px">Devuelto</th>
                        <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280;text-align:center;width:80px">Pendiente</th>
                        <th style="padding:8px 10px;font-weight:500;font-size:.8rem;color:#6b7280;text-align:center;width:100px">A devolver</th>
                    </tr>
                </thead>
                <tbody style="border-top:1px solid #e5e7eb">
                    @foreach ($lineas as $l)
                        @php $dev = $l->prestado - $l->pendiente; @endphp
                        <tr style="border-bottom:1px solid #f3f4f6">
                            <td style="padding:8px 10px;font-weight:500">{{ $l->producto }}</td>
                            <td style="padding:8px 10px;text-align:center">{{ (int) $l->prestado }}</td>
                            <td style="padding:8px 10px;text-align:center">{{ (int) $dev }}</td>
                            <td style="padding:8px 10px;text-align:center;font-weight:600;color:{{ $l->pendiente > 0 ? '#dc2626' : '#16a34a' }}">
                                {{ (int) $l->pendiente }}
                            </td>
                            <td style="padding:8px 10px;text-align:center">
                                @if ($l->pendiente > 0)
                                    <button type="button"
                                        x-on:click="
                                            $wire.$set('mountedActionsData.0.nueva_cantidad', {{ $l->pendiente }})
                                            $wire.$set('mountedActionsData.0.nuevo_id_producto', {{ $l->id_producto }})
                                        "
                                        style="background:#3b82f6;color:#fff;border:none;border-radius:6px;padding:4px 12px;cursor:pointer;font-size:.8rem">
                                        Devolver
                                    </button>
                                @else
                                    <span style="color:#9ca3af;font-size:.8rem">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p style="font-size:.8rem;color:#6b7280;margin:8px 0 0">
            💡 Hacé clic en <strong>"Devolver"</strong> junto al producto, luego completá la cantidad abajo.
        </p>
    @endif

    {{-- Modal de confirmación: componente nativo de Filament, teleportado al body
         para que el position:fixed no quede atrapado dentro del modal padre --}}
    <x-filament::modal
        id="confirmar-devolucion"
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
                x-on:click="$dispatch('close-modal', { id: 'confirmar-devolucion' })"
            >
                Cancelar
            </x-filament::button>

            <x-filament::button
                color="warning"
                x-on:click="$dispatch('close-modal', { id: 'confirmar-devolucion' }); if (confirmHandler) confirmHandler()"
            >
                Confirmar
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</div>

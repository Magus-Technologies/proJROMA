<div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.875rem">
        <thead>
            <tr style="border-bottom:2px solid #e5e7eb">
                <th style="text-align:left;padding:8px 12px">#</th>
                <th style="text-align:left;padding:8px 12px">Producto</th>
                <th style="text-align:center;padding:8px 12px">Cantidad</th>
                <th style="text-align:center;padding:8px 12px">Fecha</th>
                <th style="text-align:center;padding:8px 12px">Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($devoluciones as $d)
                <tr style="border-bottom:1px solid #f3f4f6">
                    <td style="padding:8px 12px">{{ $d->id_devolucion }}</td>
                    <td style="padding:8px 12px">{{ $d->producto }}</td>
                    <td style="padding:8px 12px;text-align:center">{{ $d->cantidad }}</td>
                    <td style="padding:8px 12px;text-align:center">{{ \Carbon\Carbon::parse($d->fecha)->format('d/m/Y H:i') }}</td>
                    <td style="padding:8px 12px;text-align:center">
                        <button type="button"
                            x-data=""
                            x-on:click="
                                if (confirm('¿Anular devolución #{{ $d->id_devolucion }}? Se revertirá el movimiento de stock.')) {
                                    $wire.anularDevolucion({{ $d->id_devolucion }}, {{ $d->id_prestamo }})
                                }
                            "
                            style="background:none;border:1px solid #ef4444;color:#ef4444;border-radius:6px;padding:4px 12px;cursor:pointer;font-size:.8rem">
                            Anular
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding:24px;text-align:center;color:#9ca3af">Sin devoluciones registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

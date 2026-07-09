<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\DiasVenta;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Edición de una venta NO enviada a SUNAT. Reutiliza todo el formulario y la
 * lógica de CreateVenta (buscador de productos, cliente, cuotas). Al guardar,
 * revierte el stock de las líneas originales y lo vuelve a aplicar con los
 * nuevos productos, manteniendo el kardex consistente. Conserva el correlativo
 * original (serie/número/tipo de documento no se cambian en edición).
 */
class EditVenta extends CreateVenta
{
    protected static ?string $title = 'Editar Venta';

    public ?Venta $ventaEditando = null;

    /**
     * El parámetro de ruta se llama {venta}, NO {record}: CreateRecord declara
     * `public ?Model $record` y Livewire intentaría instanciar esa clase
     * abstracta al hacer implicit route binding. Con otro nombre, no matchea.
     */
    public function mount(int|string $venta = 0): void
    {
        parent::mount(); // bootea el formulario (sin ?cotizacion en edición)

        $id = (int) $venta;

        $venta = Venta::with(['productosVenta', 'pagos'])
            ->where('id_empresa', (int) session('id_empresa'))
            ->findOrFail($id);

        if ($venta->sunat_estado === 'aceptado' || $venta->enviado_sunat === '1') {
            Notification::make()->warning()
                ->title('Esta venta ya fue enviada a SUNAT')
                ->body('Un comprobante aceptado no se puede editar. Emití una nota de crédito si necesitás corregirlo.')
                ->send();
            $this->redirect(VentaResource::getUrl('index'));

            return;
        }

        $this->ventaEditando = $venta;

        $this->form->fill([
            'id_tido'           => (string) $venta->id_tido,
            'id_cliente'        => $venta->id_cliente,
            'id_tipo_pago'      => (string) $venta->id_tipo_pago,
            'tipo_igv'          => $venta->tipo_igv ?: 'gravado',
            'fecha'             => optional($venta->fecha_emision)->toDateString(),
            'fecha_vencimiento' => optional($venta->fecha_vencimiento)->toDateString(),
            'observacion'       => $venta->observacion,
            'direccion'         => $venta->direccion,
            'productos'         => $venta->productosVenta->map(fn (ProductoVenta $p): array => [
                'id_producto' => $p->id_producto,
                'descripcion' => $p->descripcion,
                'cantidad'    => (float) $p->cantidad,
                'precio'      => number_format((float) $p->precio, 2, '.', ''),
                'linea_total' => number_format((float) $p->total, 2, '.', ''),
            ])->values()->toArray(),
            'lista_pagos'       => $venta->pagos->map(fn (DiasVenta $c): array => [
                'fecha'     => optional($c->fecha)->toDateString(),
                'monto'     => $c->monto,
                'tipo_pago' => $c->tipo_pago ?: 'EFECTIVO',
                'pagado'    => $c->estado === '1',
            ])->values()->toArray(),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Venta {
            $empresa = (int) session('id_empresa');
            $usuario = (int) auth()->user()->usuario_id;
            $venta   = $this->ventaEditando;

            if (blank($data['id_cliente'] ?? null)) {
                $this->fallo('Seleccioná un cliente para la venta.');
            }

            $motivoVenta = MotivoMovimiento::where('id_empresa', $empresa)
                ->where('nombre', 'Venta')
                ->value('id_motivo');
            $doc = "{$venta->serie}-" . str_pad((string) $venta->numero, 8, '0', STR_PAD_LEFT);

            // ── 1) Revertir el stock de las líneas actuales y limpiar líneas/cuotas ──
            foreach ($venta->productosVenta as $pv) {
                $prod = Producto::where('id_empresa', $empresa)
                    ->where('id_producto', $pv->id_producto)
                    ->lockForUpdate()
                    ->first();

                if ($prod) {
                    $anterior = (int) $prod->cantidad;
                    $prod->increment('cantidad', $pv->cantidad);

                    InventarioMovimiento::create([
                        'id_empresa'     => $empresa,
                        'almacen'        => $prod->almacen ?? '',
                        'id_producto'    => $prod->id_producto,
                        'tipo'           => 'I',
                        'id_motivo'      => $motivoVenta,
                        'cantidad'       => (int) $pv->cantidad,
                        'stock_anterior' => $anterior,
                        'stock_nuevo'    => $anterior + (int) $pv->cantidad,
                        'costo'          => $prod->costo,
                        'observacion'    => "Reversa por edición de venta {$doc}",
                        'id_usuario'     => $usuario,
                        'fecha'          => now(),
                    ]);
                }
            }
            ProductoVenta::where('id_venta', $venta->id_venta)->delete();
            DiasVenta::where('id_venta', $venta->id_venta)->delete();

            // ── 2) Validar stock nuevo y resolver líneas ──
            $lineas = [];
            $total  = 0.0;
            foreach ($data['productos'] as $linea) {
                $producto = Producto::where('id_empresa', $empresa)
                    ->where('id_producto', $linea['id_producto'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $cantidad = (float) $linea['cantidad'];
                if ($cantidad > (float) $producto->cantidad) {
                    $this->fallo("Stock insuficiente de «{$producto->descripcion}» (disponible: {$producto->cantidad}).");
                }

                $lineaTotal = round($cantidad * (float) $linea['precio'], 2);
                $total     += $lineaTotal;
                $lineas[]   = [$producto, $cantidad, (float) $linea['precio'], $lineaTotal];
            }

            if ($total <= 0) {
                $this->fallo('El total debe ser mayor a 0.');
            }

            // ── 3) Montos + actualizar cabecera (serie/numero/id_tido se conservan) ──
            $afectacion = $data['tipo_igv'] ?? 'gravado';
            $esGravado  = $afectacion === 'gravado';
            $subtotal   = $esGravado ? round($total / 1.18, 2) : $total;
            $igv        = $esGravado ? round($total - $subtotal, 2) : 0.0;

            $venta->update([
                'id_tipo_pago'      => $data['id_tipo_pago'],
                'fecha_emision'     => $data['fecha'],
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? $data['fecha'],
                'direccion'         => $data['direccion'] ?? '-',
                'id_cliente'        => $data['id_cliente'],
                'total'             => $total,
                'subtotal'          => $subtotal,
                'igv'               => $igv,
                'apli_igv'          => $esGravado ? '1' : '0',
                'tipo_igv'          => $afectacion,
                'observacion'       => $data['observacion'] ?? null,
                // El contenido cambió: invalidar el XML previo para regenerarlo.
                'xml_ruta'          => null,
                'cdr_ruta'          => null,
                'hash_cpe'          => null,
                'sunat_estado'      => 'pendiente',
                'sunat_mensaje'     => 'Venta editada — XML pendiente de regenerar.',
            ]);

            // ── 4) Reaplicar líneas (descontar stock) ──
            foreach ($lineas as [$producto, $cantidad, $precio, $lineaTotal]) {
                ProductoVenta::create([
                    'id_venta'    => $venta->id_venta,
                    'id_producto' => $producto->id_producto,
                    'descripcion' => $producto->descripcion,
                    'cantidad'    => $cantidad,
                    'precio'      => $precio,
                    'total'       => $lineaTotal,
                    'igv_prod'    => 0,
                    'descuento'   => 0,
                    'costo'       => (float) ($producto->costo ?? 0),
                ]);

                $anterior = (int) $producto->cantidad;
                $producto->decrement('cantidad', $cantidad);

                InventarioMovimiento::create([
                    'id_empresa'     => $empresa,
                    'almacen'        => $producto->almacen ?? '',
                    'id_producto'    => $producto->id_producto,
                    'tipo'           => 'S',
                    'id_motivo'      => $motivoVenta,
                    'cantidad'       => (int) $cantidad,
                    'stock_anterior' => $anterior,
                    'stock_nuevo'    => $anterior - (int) $cantidad,
                    'costo'          => $producto->costo,
                    'observacion'    => "Venta {$doc} (editada)",
                    'id_usuario'     => $usuario,
                    'fecha'          => now(),
                ]);
            }

            // ── 5) Cuotas (solo crédito, sin ítems vacíos) ──
            $cuotas = ((int) $data['id_tipo_pago'] === 2) ? ($data['lista_pagos'] ?? []) : [];
            foreach ($cuotas as $pago) {
                if (blank($pago['monto'] ?? null) || blank($pago['fecha'] ?? null)) {
                    continue;
                }

                DiasVenta::create([
                    'id_venta'   => $venta->id_venta,
                    'fecha'      => $pago['fecha'],
                    'monto'      => $pago['monto'],
                    'estado'     => ($pago['pagado'] ?? false) ? '1' : '0',
                    'tipo_pago'  => $pago['tipo_pago'] ?? 'EFECTIVO',
                    'id_usuario' => $usuario,
                ]);
            }

            Notification::make()->success()
                ->title("Venta {$doc} actualizada")
                ->body('Total: S/ ' . number_format($total, 2))
                ->send();

            return $venta;
        });
    }

    protected function getRedirectUrl(): string
    {
        return VentaResource::getUrl('index', ['previsualizar' => $this->getRecord()->id_venta]);
    }
}

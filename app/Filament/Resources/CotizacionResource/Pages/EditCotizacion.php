<?php

namespace App\Filament\Resources\CotizacionResource\Pages;

use App\Filament\Resources\CotizacionResource;
use App\Models\Cotizacion;
use App\Models\CuotaCotizacion;
use App\Models\Producto;
use App\Models\ProductoCoti;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Edición de una cotización aún activa (no convertida en venta ni anulada).
 * Reutiliza todo el formulario de CreateCotizacion (buscadores, modal de cuotas).
 * A diferencia de la venta, la cotización NO mueve stock: al guardar se
 * reemplazan las líneas y las cuotas, conservando número, serie y correlativo.
 */
class EditCotizacion extends CreateCotizacion
{
    protected static ?string $title = 'Editar Cotización';

    public ?Cotizacion $cotizacionEditando = null;

    public function mount(): void
    {
        parent::mount(); // bootea el formulario

        $id = (int) request()->route('record');

        $coti = Cotizacion::with(['productos', 'cuotas'])
            ->where('id_empresa', (int) session('id_empresa'))
            ->findOrFail($id);

        if ($coti->estado !== '1' || $coti->id_venta) {
            Notification::make()->warning()
                ->title('Esta cotización no se puede editar')
                ->body('Solo se editan cotizaciones activas y sin venta asociada.')
                ->send();
            $this->redirect(CotizacionResource::getUrl('index'));

            return;
        }

        $this->cotizacionEditando = $coti;

        $this->form->fill([
            'id_tido'      => (string) $coti->id_tido,
            'id_cliente'   => $coti->id_cliente,
            'id_tipo_pago' => (string) $coti->id_tipo_pago,
            'fecha'        => optional($coti->fecha)->toDateString(),
            'direccion'    => $coti->direccion,
            'observacion'  => $coti->observacion,
            'productos'    => $coti->productos->map(function (ProductoCoti $p): array {
                $prod = Producto::find($p->id_producto);

                return [
                    'id_producto' => $p->id_producto,
                    'descripcion' => $prod?->descripcion ?? "Producto #{$p->id_producto}",
                    'cantidad'    => (float) $p->cantidad,
                    'precio'      => number_format((float) $p->precio, 2, '.', ''),
                    'linea_total' => number_format((float) $p->cantidad * (float) $p->precio, 2, '.', ''),
                ];
            })->values()->toArray(),
            'cuotas'       => $coti->cuotas->map(fn (CuotaCotizacion $c): array => [
                'fecha'     => optional($c->fecha)->toDateString(),
                'monto'     => number_format((float) $c->monto, 2, '.', ''),
                'tipo_pago' => $c->tipo_pago ?: 'EFECTIVO',
            ])->values()->toArray(),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Cotizacion {
            $empresa = (int) session('id_empresa');
            $usuario = (int) auth()->user()->usuario_id;
            $coti    = $this->cotizacionEditando;

            if (blank($data['id_cliente'] ?? null)) {
                $this->fallo('Seleccioná un cliente para la cotización.');
            }

            // ── Resolver líneas y total (la cotización no valida stock) ──
            $lineas = [];
            $total  = 0.0;
            foreach ($data['productos'] as $linea) {
                $producto = Producto::where('id_empresa', $empresa)
                    ->where('id_producto', $linea['id_producto'])
                    ->firstOrFail();

                $cantidad = (float) $linea['cantidad'];
                $precio   = (float) $linea['precio'];
                $total   += round($cantidad * $precio, 2);
                $lineas[] = [$producto, $cantidad, $precio];
            }

            if ($total <= 0) {
                $this->fallo('El total debe ser mayor a 0.');
            }

            // ── Crédito: las cuotas deben sumar el total ──
            if ((int) $data['id_tipo_pago'] === 2) {
                $cuotas = $data['cuotas'] ?? [];

                if (count($cuotas) === 0) {
                    $this->fallo('Una cotización a crédito debe tener al menos una cuota.');
                }

                $suma = round(collect($cuotas)->sum(fn (array $c): float => (float) ($c['monto'] ?? 0)), 2);

                if (abs($suma - $total) > 0.01) {
                    $diferencia = round($total - $suma, 2);
                    $this->fallo('Las cuotas (S/ ' . number_format($suma, 2) . ') deben sumar el total (S/ '
                        . number_format($total, 2) . '). '
                        . ($diferencia > 0
                            ? 'Faltan S/ ' . number_format($diferencia, 2) . '.'
                            : 'Exceden en S/ ' . number_format(abs($diferencia), 2) . '.'));
                }
            }

            // ── Actualizar cabecera (número, serie y correlativo se conservan) ──
            $coti->update([
                'id_tipo_pago' => $data['id_tipo_pago'],
                'fecha'        => $data['fecha'],
                'direccion'    => $data['direccion'] ?? null,
                'id_cliente'   => $data['id_cliente'],
                'total'        => $total,
                'observacion'  => $data['observacion'] ?? null,
            ]);

            // ── Reemplazar líneas ──
            DB::table('productos_cotis')->where('id_coti', $coti->cotizacion_id)->delete();
            foreach ($lineas as [$producto, $cantidad, $precio]) {
                DB::table('productos_cotis')->insert([
                    'id_coti'        => $coti->cotizacion_id,
                    'id_producto'    => $producto->id_producto,
                    'cantidad'       => $cantidad,
                    'precio'         => $precio,
                    'costo'          => $producto->costo ?? 0,
                    'medida'         => $producto->medida ?? 'Unidad',
                    'presenta'       => 1,
                    'presenta_cnt'   => 1,
                    'fecha_registro' => now(),
                    'id_usuario'     => $usuario,
                ]);
            }

            // ── Reemplazar cuotas ──
            CuotaCotizacion::where('id_coti', $coti->cotizacion_id)->delete();
            if ((int) $data['id_tipo_pago'] === 2) {
                foreach ($data['cuotas'] ?? [] as $cuota) {
                    CuotaCotizacion::create([
                        'id_coti'    => $coti->cotizacion_id,
                        'id_usuario' => $usuario,
                        'monto'      => $cuota['monto'],
                        'fecha'      => $cuota['fecha'],
                        'estado'     => '0',
                        'tipo_pago'  => $cuota['tipo_pago'] ?? 'EFECTIVO',
                    ]);
                }
            }

            Notification::make()->success()
                ->title('Cotización COT-' . str_pad((string) $coti->numero, 8, '0', STR_PAD_LEFT) . ' actualizada')
                ->body('Total: S/ ' . number_format($total, 2))
                ->send();

            return $coti;
        });
    }
}

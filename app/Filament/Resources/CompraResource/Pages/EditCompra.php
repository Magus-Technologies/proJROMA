<?php

namespace App\Filament\Resources\CompraResource\Pages;

use App\Filament\Resources\CompraResource;
use App\Models\Compra;
use App\Models\Producto;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Edición de una compra AÚN NO RECEPCIONADA. Una vez que la compra ingresó al
 * inventario (recepcionado != 0) no se puede tocar: habría que anular la
 * recepción primero, o el stock quedaría descuadrado con el kardex.
 */
class EditCompra extends CreateCompra
{
    protected static ?string $title = 'Editar Compra';

    public ?Compra $compraEditando = null;

    /**
     * El parámetro de ruta se llama {compra}, NO {record}: CreateRecord declara
     * `public ?Model $record` y Livewire intentaría instanciar esa clase
     * abstracta al hacer implicit route binding. Con otro nombre, no matchea.
     */
    public function mount(int|string $compra = 0): void
    {
        parent::mount();

        $id = (int) $compra;

        $compra = Compra::where('id_empresa', (int) session('id_empresa'))->findOrFail($id);

        if ((int) $compra->recepcionado !== 0) {
            Notification::make()->warning()
                ->title('Esta compra ya fue recepcionada')
                ->body('No se puede editar una compra que ya ingresó al inventario.')
                ->send();
            $this->redirect(CompraResource::getUrl('index'));

            return;
        }

        $this->compraEditando = $compra;

        $lineas = DB::table('productos_compras as pc')
            ->leftJoin('productos as p', 'p.id_producto', '=', 'pc.id_producto')
            ->where('pc.id_compra', $compra->id_compra)
            ->get(['pc.id_producto', 'pc.cantidad', 'pc.costo', 'p.descripcion']);

        $this->form->fill([
            'id_proveedor'     => $compra->id_proveedor,
            'id_tido'          => (string) $compra->id_tido,
            'id_tipo_pago'     => (string) $compra->id_tipo_pago,
            'instrumento_tipo' => $compra->instrumento_tipo,
            'instrumento_id'   => $compra->instrumento_id,
            'fecha'            => optional($compra->fecha_emision)->toDateString(),
            'serie'            => $compra->serie,
            'numero'           => $compra->numero,
            'observacion'      => $compra->direccion,
            'productos'        => $lineas->map(fn ($l): array => [
                'id_producto' => $l->id_producto,
                'descripcion' => $l->descripcion ?? "Producto #{$l->id_producto}",
                'cantidad'    => (float) $l->cantidad,
                'costo'       => number_format((float) $l->costo, 2, '.', ''),
                'linea_total' => number_format((float) $l->cantidad * (float) $l->costo, 2, '.', ''),
            ])->values()->toArray(),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Compra {
            $compra = $this->compraEditando;

            // Revalidar por si la recepción ocurrió mientras se editaba.
            if ((int) $compra->fresh()->recepcionado !== 0) {
                $this->fallo('La compra fue recepcionada mientras la editabas. No se guardaron los cambios.');
            }

            [$lineas, $total] = $this->resolverLineas($data);

            $compra->update([
                'id_proveedor'      => $data['id_proveedor'],
                'id_tido'           => $data['id_tido'],
                'id_tipo_pago'      => $data['id_tipo_pago'] ?? 1,
                'instrumento_tipo'  => $data['instrumento_tipo'] ?? null,
                'instrumento_id'    => $data['instrumento_id'] ?? null,
                'fecha_emision'     => $data['fecha'],
                'fecha_vencimiento' => $data['fecha'],
                'direccion'         => $data['observacion'] ?? '',
                'serie'             => $data['serie'] ?? '',
                'numero'            => $data['numero'] ?? '',
                'total'             => $total,
            ]);

            $this->guardarLineas($compra->id_compra, $lineas);

            Notification::make()->success()
                ->title('Compra actualizada')
                ->body('Total: S/ ' . number_format($total, 2))
                ->send();

            return $compra;
        });
    }
}

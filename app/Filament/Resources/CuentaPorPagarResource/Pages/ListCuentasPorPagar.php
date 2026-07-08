<?php

namespace App\Filament\Resources\CuentaPorPagarResource\Pages;

use App\Filament\Resources\CuentaPorPagarResource;
use App\Models\DiasCompra;
use App\Services\CajaService;
use App\Models\Compra;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class ListCuentasPorPagar extends ListRecords
{
    protected static string $resource = CuentaPorPagarResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    #[On('editar-pago')]
    public function editPayment($id, $monto, $metodo): void
    {
        if (!$id) return;

        DB::transaction(function () use ($id, $monto, $metodo): void {
            $pago = DiasCompra::with('compra')->findOrFail($id);

            $pago->update([
                'monto'            => (float) $monto,
                'instrumento_tipo' => $metodo ?: null,
            ]);

            if ($pago->id_caja) {
                DB::table('caja_movimientos')
                    ->where('id', $pago->id_caja)
                    ->update([
                        'monto'            => (float) $monto,
                        'instrumento_tipo' => $metodo ?: null,
                    ]);
            }
        });

        Notification::make()->success()->title('Pago actualizado')->send();
    }

    #[On('anular-pago')]
    public function annulPayment($id): void
    {
        if (!$id) return;

        DB::transaction(function () use ($id): void {
            $pago = DiasCompra::with('compra')->findOrFail($id);
            $pago->update(['estado' => '0']);

            if ($pago->id_caja && $pago->compra) {
                $doc = trim("{$pago->compra->serie}-{$pago->compra->numero}", '-');
                app(CajaService::class)->registrarMovimiento([
                    'id_caja'          => $pago->id_caja,
                    'tipo'             => 'INGRESO',
                    'categoria'        => 'COMPRA',
                    'descripcion'      => 'Reversión pago anulado compra ' . ($doc ?: "#{$pago->compra->id_compra}"),
                    'monto'            => (float) $pago->monto,
                    'fecha'            => now()->toDateString(),
                    'instrumento_tipo' => $pago->instrumento_tipo,
                    'id_usuario'       => (int) auth()->user()->usuario_id,
                ]);
            }
        });

        Notification::make()->success()->title('Pago anulado')->send();
    }
}

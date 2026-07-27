<?php

namespace App\Livewire;

use App\Filament\Resources\CuentaPorCobrarResource;
use App\Models\DiasVenta;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

/**
 * Campanita del header: cuenta las cuotas próximas a vencer (pendientes con
 * vencimiento dentro de los próximos días) de la empresa/sucursal activa.
 * Al hacer clic lleva a Cuentas por Cobrar.
 */
class CampanaVencimientos extends Component
{
    /** Ventana de anticipación, en días, para considerar una cuota "por vencer". */
    public const DIAS_AVISO = 3;

    public function getCantidadProperty(): int
    {
        if (! auth()->user()?->can('cobranzas.ver')) {
            return 0;
        }

        $hoy = now()->startOfDay();

        return DiasVenta::query()
            ->where('dias_ventas.estado', '0')
            ->whereDate('dias_ventas.fecha', '>=', $hoy->toDateString())
            ->whereDate('dias_ventas.fecha', '<=', $hoy->copy()->addDays(self::DIAS_AVISO)->toDateString())
            ->whereHas('venta', fn (Builder $q): Builder => $q
                ->where('id_empresa', (int) session('id_empresa'))
                ->where('sucursal', (int) session('sucursal'))
                ->where('estado', '!=', '0'))
            ->count();
    }

    public function render()
    {
        return view('livewire.campana-vencimientos', [
            'puedeVer'  => (bool) auth()->user()?->can('cobranzas.ver'),
            'cantidad'  => $this->cantidad,
            'url'       => CuentaPorCobrarResource::getUrl('index'),
            'diasAviso' => self::DIAS_AVISO,
        ]);
    }
}

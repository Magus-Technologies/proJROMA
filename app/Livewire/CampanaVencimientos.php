<?php

namespace App\Livewire;

use App\Filament\Resources\CuentaPorCobrarResource;
use App\Models\DiasVenta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * Campanita del header: abre una lista de notificaciones de cuotas próximas a
 * vencer (o ya vencidas) de la empresa/sucursal activa. Cada notificación
 * lleva a Cuentas por Cobrar al hacer clic.
 */
class CampanaVencimientos extends Component
{
    /** Ventana de anticipación, en días, para avisar de una cuota por vencer. */
    public const DIAS_AVISO = 3;

    /** Máximo de notificaciones a listar en el desplegable. */
    public const MAX_ITEMS = 15;

    protected function baseQuery(): Builder
    {
        $hoy = now()->startOfDay();

        // Pendientes que vencen dentro de la ventana o que ya están vencidas.
        return DiasVenta::query()
            ->where('dias_ventas.estado', '0')
            ->whereDate('dias_ventas.fecha', '<=', $hoy->copy()->addDays(self::DIAS_AVISO)->toDateString())
            ->whereHas('venta', fn (Builder $q): Builder => $q
                ->where('id_empresa', (int) session('id_empresa'))
                ->where('sucursal', (int) session('sucursal'))
                ->where('estado', '!=', '0'));
    }

    public function getPuedeVerProperty(): bool
    {
        return (bool) auth()->user()?->can('cobranzas.ver');
    }

    public function getCantidadProperty(): int
    {
        return $this->puedeVer ? $this->baseQuery()->count() : 0;
    }

    // ── Alertas de bajo stock (notificaciones de BD del usuario) ─────────

    protected function stockQuery(): \Illuminate\Database\Query\Builder
    {
        return \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', (int) auth()->user()?->usuario_id)
            ->whereNull('read_at');
    }

    public function getCantidadStockProperty(): int
    {
        return auth()->check() ? $this->stockQuery()->count() : 0;
    }

    /** @return Collection<int, array<string, string>> */
    public function getAlertasStockProperty(): Collection
    {
        if (! auth()->check()) {
            return collect();
        }

        return $this->stockQuery()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($n): array {
                $data = json_decode($n->data, true) ?: [];

                return [
                    'titulo' => $data['title'] ?? 'Notificación',
                    'cuerpo' => $data['body'] ?? '',
                    'cuando' => \Carbon\Carbon::parse($n->created_at)->diffForHumans(),
                ];
            });
    }

    public function marcarStockLeidas(): void
    {
        $this->stockQuery()->update(['read_at' => now()]);
    }

    /** @return Collection<int, array<string, mixed>> */
    public function getNotificacionesProperty(): Collection
    {
        if (! $this->puedeVer) {
            return collect();
        }

        $hoy = now()->startOfDay();

        return $this->baseQuery()
            ->with('venta.cliente')
            ->orderBy('dias_ventas.fecha')
            ->limit(self::MAX_ITEMS)
            ->get()
            ->map(function (DiasVenta $c) use ($hoy): array {
                $vence   = $c->fecha?->startOfDay();
                $vencida = $vence && $vence->lt($hoy);
                $dias    = $vence ? (int) $hoy->diffInDays($vence, false) : null;

                return [
                    'cliente'   => $c->venta?->cliente?->datos ?? 'Cliente',
                    'documento' => $c->venta
                        ? $c->venta->serie . '-' . str_pad((string) $c->venta->numero, 8, '0', STR_PAD_LEFT)
                        : '—',
                    'monto'     => (float) $c->monto,
                    'fecha'     => $vence?->format('d/m/Y') ?? '—',
                    'vencida'   => $vencida,
                    'cuando'    => $this->textoCuando($dias, $vencida),
                ];
            });
    }

    private function textoCuando(?int $dias, bool $vencida): string
    {
        if ($dias === null) {
            return '';
        }
        if ($vencida) {
            $d = abs($dias);

            return $d === 0 ? 'Vence hoy' : "Vencida hace {$d} día" . ($d === 1 ? '' : 's');
        }

        return match ($dias) {
            0       => 'Vence hoy',
            1       => 'Vence mañana',
            default => "Vence en {$dias} días",
        };
    }

    public function render()
    {
        return view('livewire.campana-vencimientos', [
            'puedeVer'       => $this->puedeVer,
            'cantidad'       => $this->cantidad,
            'notificaciones' => $this->notificaciones,
            'url'            => CuentaPorCobrarResource::getUrl('index'),
            'cantidadStock'  => $this->cantidadStock,
            'alertasStock'   => $this->alertasStock,
            'urlStock'       => \App\Filament\Resources\ProductoResource::getUrl('index'),
            'total'          => $this->cantidad + $this->cantidadStock,
        ]);
    }
}

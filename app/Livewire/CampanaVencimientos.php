<?php

namespace App\Livewire;

use App\Filament\Resources\ConductorResource;
use App\Filament\Resources\VehiculoResource;
use App\Filament\Resources\CuentaPorCobrarResource;
use App\Models\DiasVenta;
use App\Models\TmsConductor;
use App\Models\TmsVehiculo;
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

    /**
     * Ventana para la licencia de conducir. Es más larga que la de las cuotas
     * porque renovar una licencia lleva trámite: avisar el mismo día no sirve.
     */
    public const DIAS_AVISO_LICENCIA = 30;

    /** Ventana para SOAT, revisión técnica y mantenimiento del vehículo. */
    public const DIAS_AVISO_VEHICULO = 30;

    /** Los tres vencimientos del vehículo: columna => cómo se llama. */
    private const VENCIMIENTOS_VEHICULO = [
        'soat_vence'          => 'SOAT',
        'rev_tecnica_vence'   => 'Revisión técnica',
        'mantenimiento_vence' => 'Mantenimiento',
    ];

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
                    'url'    => $data['url'] ?? null,
                ];
            });
    }

    public function marcarStockLeidas(): void
    {
        $this->stockQuery()->update(['read_at' => now()]);
    }

    // ── Licencias de conducir por vencer ────────────────────────────────

    protected function licenciasQuery(): Builder
    {
        return TmsConductor::query()
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'))
            ->where('estado', 1)
            ->whereNotNull('licencia_vence')
            ->whereDate('licencia_vence', '<=', now()->startOfDay()->addDays(self::DIAS_AVISO_LICENCIA)->toDateString());
    }

    public function getPuedeVerLicenciasProperty(): bool
    {
        return (bool) auth()->user()?->can('tms_conductores.ver');
    }

    public function getCantidadLicenciasProperty(): int
    {
        return $this->puedeVerLicencias ? $this->licenciasQuery()->count() : 0;
    }

    /** @return Collection<int, array<string, mixed>> */
    public function getAlertasLicenciasProperty(): Collection
    {
        if (! $this->puedeVerLicencias) {
            return collect();
        }

        $hoy = now()->startOfDay();

        return $this->licenciasQuery()
            ->orderBy('licencia_vence')
            ->limit(self::MAX_ITEMS)
            ->get()
            ->map(function (TmsConductor $c) use ($hoy): array {
                $vence = $c->licencia_vence->startOfDay();
                $vencida = $vence->lt($hoy);

                return [
                    'conductor' => $c->nombres,
                    'licencia'  => $c->licencia ?: 'sin número',
                    'categoria' => $c->licencia_categoria,
                    'fecha'     => $vence->format('d/m/Y'),
                    'vencida'   => $vencida,
                    'cuando'    => $this->textoCuando((int) $hoy->diffInDays($vence, false), $vencida),
                ];
            });
    }

    // ── Vencimientos del vehículo (SOAT, revisión técnica, mantenimiento) ──

    /**
     * Qué vehículos le tocan a quien está mirando: los que tiene a cargo y los
     * que no tienen conductor asignado. Un vehículo sin dueño es de todos —
     * si no, su vencimiento no lo vería nadie. Los administradores ven todos.
     */
    protected function vehiculosQuery(): Builder
    {
        $limite = now()->startOfDay()->addDays(self::DIAS_AVISO_VEHICULO)->toDateString();

        $query = TmsVehiculo::query()
            ->with('conductor')
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'))
            ->where('estado', 1)
            ->where(function (Builder $q) use ($limite): Builder {
                foreach (array_keys(self::VENCIMIENTOS_VEHICULO) as $columna) {
                    $q->orWhereDate($columna, '<=', $limite);
                }

                return $q;
            });

        if (auth()->user()?->esAdmin()) {
            return $query;
        }

        // Conductores que son este usuario (por su enlace con la ficha).
        $mios = TmsConductor::query()
            ->where('id_usuario', (int) auth()->user()?->usuario_id)
            ->pluck('id');

        // Conductores sin usuario del sistema: no pueden recibir el aviso, así
        // que su vehículo se comporta como uno sin conductor asignado.
        $sinUsuario = TmsConductor::query()->whereNull('id_usuario')->select('id');

        return $query->where(fn (Builder $q): Builder => $q
            ->whereNull('id_conductor')
            ->orWhereIn('id_conductor', $mios)
            ->orWhereIn('id_conductor', $sinUsuario));
    }

    public function getPuedeVerVehiculosProperty(): bool
    {
        return (bool) auth()->user()?->can('tms_vehiculos.ver');
    }

    /** @return Collection<int, array<string, mixed>> */
    public function getAlertasVehiculosProperty(): Collection
    {
        if (! $this->puedeVerVehiculos) {
            return collect();
        }

        $hoy = now()->startOfDay();
        $limite = $hoy->copy()->addDays(self::DIAS_AVISO_VEHICULO);

        return $this->vehiculosQuery()
            ->get()
            ->flatMap(function (TmsVehiculo $v) use ($hoy, $limite): array {
                $filas = [];

                foreach (self::VENCIMIENTOS_VEHICULO as $columna => $concepto) {
                    $fecha = $v->{$columna};

                    if (! $fecha || $fecha->gt($limite)) {
                        continue;
                    }

                    $vencida = $fecha->startOfDay()->lt($hoy);

                    $filas[] = [
                        'placa'     => $v->placa,
                        'concepto'  => $concepto,
                        'conductor' => $v->conductor?->nombres,
                        'fecha'     => $fecha->format('d/m/Y'),
                        'orden'     => $fecha->timestamp,
                        'vencida'   => $vencida,
                        'cuando'    => $this->textoCuando((int) $hoy->diffInDays($fecha, false), $vencida),
                    ];
                }

                return $filas;
            })
            ->sortBy('orden')
            ->take(self::MAX_ITEMS)
            ->values();
    }

    public function getCantidadVehiculosProperty(): int
    {
        return $this->alertasVehiculos->count();
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
            'cantidadLicencias' => $this->cantidadLicencias,
            'alertasVehiculos'  => $alertasVehiculos = $this->alertasVehiculos,
            'cantidadVehiculos' => $cantidadVehiculos = $alertasVehiculos->count(),
            'urlVehiculos'      => VehiculoResource::getUrl('index'),
            'alertasLicencias'  => $this->alertasLicencias,
            'urlLicencias'      => ConductorResource::getUrl('index'),
            'total'          => $this->cantidad + $this->cantidadStock + $this->cantidadLicencias + $cantidadVehiculos,
        ]);
    }
}

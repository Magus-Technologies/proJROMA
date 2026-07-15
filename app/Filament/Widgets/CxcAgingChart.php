<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CxcAgingChart extends ChartWidget
{
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = 2;
    protected ?string $heading = 'Cuentas por Cobrar — Antigüedad de saldos';
    protected ?string $maxHeight = '280px';

    private ?array $buckets = null;

    protected function getType(): string
    {
        return 'bar';
    }

    public function getDescription(): ?string
    {
        $total = array_sum($this->getBuckets());

        return $total > 0
            ? 'Saldo pendiente total: S/ ' . number_format($total, 2)
            : 'Sin cuentas pendientes de cobro';
    }

    protected function getData(): array
    {
        $buckets = $this->getBuckets();

        return [
            'datasets' => [
                [
                    'label' => 'Saldo (S/)',
                    'data' => array_values($buckets),
                    'backgroundColor' => ['#10b981', '#f59e0b', '#f97316', '#ef4444', '#b91c1c'],
                    'borderRadius' => 4,
                ],
            ],
            'labels' => array_keys($buckets),
        ];
    }

    /** Saldo pendiente de cada cuota agrupado por antigüedad del vencimiento. */
    private function getBuckets(): array
    {
        if ($this->buckets !== null) {
            return $this->buckets;
        }

        $empresa  = (int) (session('id_empresa') ?: auth()->user()?->id_empresa ?? 0);
        $sucursal = (int) (session('sucursal')   ?: auth()->user()?->sucursal   ?? 1);

        $cuotas = DB::table('dias_ventas as dv')
            ->join('ventas as v', 'v.id_venta', '=', 'dv.id_venta')
            ->where('v.id_empresa', $empresa)
            ->where('v.sucursal', $sucursal)
            ->where('v.estado', '!=', '0')
            ->where('dv.estado', '0')
            ->selectRaw("dv.fecha, dv.monto - COALESCE((SELECT SUM(a.monto) FROM cxc_abonos a WHERE a.id_dias_venta = dv.dias_venta_id AND a.estado = 'ACTIVO'), 0) as saldo")
            ->get();

        $buckets = [
            'Por vencer'    => 0.0,
            'Venc. 1–15 d'  => 0.0,
            'Venc. 16–30 d' => 0.0,
            'Venc. 31–60 d' => 0.0,
            'Venc. +60 d'   => 0.0,
        ];

        $hoy = now()->startOfDay();

        foreach ($cuotas as $cuota) {
            $saldo = (float) $cuota->saldo;

            if ($saldo <= 0.001) {
                continue;
            }

            $vencimiento = Carbon::parse($cuota->fecha)->startOfDay();
            $atraso = $vencimiento->lessThan($hoy) ? (int) $vencimiento->diffInDays($hoy) : 0;

            $key = match (true) {
                $atraso === 0 => 'Por vencer',
                $atraso <= 15 => 'Venc. 1–15 d',
                $atraso <= 30 => 'Venc. 16–30 d',
                $atraso <= 60 => 'Venc. 31–60 d',
                default       => 'Venc. +60 d',
            };

            $buckets[$key] += $saldo;
        }

        return $this->buckets = $buckets;
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => '#f1f5f9'],
                ],
            ],
        ];
    }
}

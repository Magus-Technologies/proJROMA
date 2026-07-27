<?php

namespace App\Filament\Pages;

use App\Models\AsientoContable;
use App\Models\AsientoDetalle;
use App\Models\PlanCuenta;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class LibroMayor extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Libro Mayor';
    protected static string|\UnitEnum|null $navigationGroup = 'Contabilidad';
    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Libro Mayor';
    protected string $view = 'filament.pages.libro-mayor';

    public function getData(): array
    {
        $desde = request('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = request('hasta', now()->format('Y-m-d'));
        $cuentaId = request('cuenta_id');

        $cuentas = PlanCuenta::where('estado', true)->orderBy('codigo')->get();
        $saldoInicial = [];

        if ($cuentaId) {
            $cuenta = PlanCuenta::find($cuentaId);

            // El saldo se muestra según la NATURALEZA de la cuenta: las
            // deudoras (activo/costo/gasto) acumulan Debe − Haber y las
            // acreedoras (pasivo/patrimonio/ingreso) Haber − Debe, para que
            // una cuenta como Ventas no aparezca con saldo "negativo".
            $deudora = $cuenta?->esNaturalezaDeudora() ?? true;

            $detalles = AsientoDetalle::where('plan_cuenta_id', $cuentaId)
                ->whereHas('asiento', fn ($q) => $q->where('estado', '!=', 'anulado')
                    ->whereBetween('fecha', [$desde, $hasta]))
                ->with('asiento')
                ->orderBy(AsientoContable::select('fecha')->whereColumn('id', 'asientos_detalle.asiento_id'))
                ->orderBy(AsientoContable::select('numero')->whereColumn('id', 'asientos_detalle.asiento_id'))
                ->get();

            $netoAntes = AsientoDetalle::where('plan_cuenta_id', $cuentaId)
                ->whereHas('asiento', fn ($q) => $q->where('estado', '!=', 'anulado')
                    ->where('fecha', '<', $desde))
                ->selectRaw('COALESCE(SUM(debe),0) - COALESCE(SUM(haber),0) as saldo')
                ->value('saldo') ?? 0;
            $saldoAntes = $deudora ? (float) $netoAntes : -(float) $netoAntes;

            $saldoAcum = $saldoAntes;
            $rows = $detalles->map(function ($d) use (&$saldoAcum, $deudora) {
                $saldoAcum += $deudora ? ($d->debe - $d->haber) : ($d->haber - $d->debe);
                return [
                    'fecha' => $d->asiento?->fecha,
                    'numero' => $d->asiento?->numero,
                    'glosa' => $d->asiento?->glosa,
                    'detalle_glosa' => $d->glosa,
                    'debe' => $d->debe,
                    'haber' => $d->haber,
                    'saldo' => $saldoAcum,
                ];
            });

            return [
                'cuentas' => $cuentas,
                'cuenta_id' => $cuentaId,
                'cuenta' => $cuenta,
                'rows' => $rows,
                'total_debe' => $detalles->sum('debe'),
                'total_haber' => $detalles->sum('haber'),
                'saldo_inicial' => $saldoAntes,
                'desde' => $desde,
                'hasta' => $hasta,
            ];
        }

        return [
            'cuentas' => $cuentas,
            'cuenta_id' => null,
            'cuenta' => null,
            'rows' => collect(),
            'total_debe' => 0,
            'total_haber' => 0,
            'saldo_inicial' => 0,
            'desde' => $desde,
            'hasta' => $hasta,
        ];
    }

    public function getTipoCuenta(string $tipo): string
    {
        return PlanCuenta::tipos()[$tipo] ?? $tipo;
    }
}

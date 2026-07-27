<?php

namespace App\Filament\Pages;

use App\Models\AsientoDetalle;
use App\Models\PlanCuenta;
use Filament\Pages\Page;

class BalanceGeneral extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'Balance General';
    protected static string|\UnitEnum|null $navigationGroup = 'Contabilidad';
    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Balance General';
    protected string $view = 'filament.pages.balance-general';

    public function getData(): array
    {
        $fecha = request('fecha', now()->format('Y-m-d'));

        // Neto Debe − Haber por cuenta (asientos no anulados hasta la fecha de corte)
        $netos = AsientoDetalle::whereHas('asiento', fn ($q) => $q->where('estado', '!=', 'anulado')
                ->where('fecha', '<=', $fecha))
            ->selectRaw('plan_cuenta_id, SUM(debe) - SUM(haber) as neto')
            ->groupBy('plan_cuenta_id')
            ->pluck('neto', 'plan_cuenta_id')
            ->map(fn ($v) => (float) $v);

        $cuentas = PlanCuenta::orderBy('codigo')->get();

        $activo = [];
        $pasivo = [];
        $patrimonio = [];
        $totalActivo = 0.0;
        $totalPasivo = 0.0;
        $totalPatrimonio = 0.0;

        // Resultado del ejercicio = Ingresos (naturaleza acreedora)
        // − Costos − Gastos (naturaleza deudora). Va al patrimonio para
        // que la ecuación contable cierre.
        $resultado = 0.0;

        foreach ($cuentas as $c) {
            $neto = $netos->get($c->id, 0.0);
            if (abs($neto) < 0.005) {
                continue;
            }

            $label = $c->codigo . ' - ' . $c->nombre;
            $fila = ['label' => $label, 'nivel' => $c->nivel, 'padre_id' => $c->padre_id];

            switch ($c->tipo) {
                // Naturaleza DEUDORA: el saldo positivo vive en el Debe
                case 'activo':
                    $fila['saldo'] = $neto;
                    $activo[] = $fila;
                    $totalActivo += $neto;
                    break;

                // Naturaleza ACREEDORA: el saldo positivo vive en el Haber
                case 'pasivo':
                    $fila['saldo'] = -$neto;
                    $pasivo[] = $fila;
                    $totalPasivo += -$neto;
                    break;

                case 'patrimonio':
                    $fila['saldo'] = -$neto;
                    $patrimonio[] = $fila;
                    $totalPatrimonio += -$neto;
                    break;

                // Cuentas de resultados: no se listan en el balance, pero su
                // neto forma el Resultado del Ejercicio dentro del patrimonio.
                // Ingresos (acreedora): −neto suma; costos/gastos (deudora):
                // neto positivo resta — ambos casos equivalen a restar el neto.
                case 'ingreso':
                case 'costo':
                case 'gasto':
                    $resultado -= $neto;
                    break;
            }
        }

        if (abs($resultado) >= 0.005) {
            $patrimonio[] = [
                'label' => 'Resultado del Ejercicio (Ingresos − Costos − Gastos)',
                'nivel' => 2,
                'padre_id' => null,
                'saldo' => $resultado,
            ];
            $totalPatrimonio += $resultado;
        }

        return [
            'activo' => $activo,
            'pasivo' => $pasivo,
            'patrimonio' => $patrimonio,
            'total_activo' => round($totalActivo, 2),
            'total_pasivo' => round($totalPasivo, 2),
            'total_patrimonio' => round($totalPatrimonio, 2),
            'total_pasivo_patrimonio' => round($totalPasivo + $totalPatrimonio, 2),
            'diferencia' => round($totalActivo - ($totalPasivo + $totalPatrimonio), 2),
            'fecha' => $fecha,
            'cuentas' => $cuentas,
        ];
    }
}

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

        $saldoCuentas = AsientoDetalle::whereHas('asiento', fn ($q) => $q->where('estado', '!=', 'anulado')
                ->where('fecha', '<=', $fecha))
            ->selectRaw('plan_cuenta_id, SUM(debe) - SUM(haber) as saldo')
            ->groupBy('plan_cuenta_id')
            ->get()
            ->keyBy('plan_cuenta_id')
            ->map(fn ($i) => (float) $i->saldo);

        $cuentas = PlanCuenta::with('padre')->orderBy('codigo')->get()->keyBy('id');

        $activo = [];
        $pasivo = [];
        $patrimonio = [];

        foreach ($cuentas as $id => $c) {
            $saldo = $saldoCuentas->get($id, 0);
            if ($saldo == 0) continue;

            $label = $c->codigo . ' - ' . $c->nombre;

            if ($c->tipo === 'activo') {
                $activo[] = ['label' => $label, 'saldo' => $saldo, 'nivel' => $c->nivel, 'padre_id' => $c->padre_id];
            } elseif ($c->tipo === 'pasivo') {
                $pasivo[] = ['label' => $label, 'saldo' => $saldo, 'nivel' => $c->nivel, 'padre_id' => $c->padre_id];
            } elseif ($c->tipo === 'patrimonio') {
                $patrimonio[] = ['label' => $label, 'saldo' => $saldo, 'nivel' => $c->nivel, 'padre_id' => $c->padre_id];
            }
        }

        $totalActivo = collect($activo)->where('nivel', 3)->sum('saldo');
        $totalPasivo = collect($pasivo)->where('nivel', 3)->sum('saldo');
        $totalPatrimonio = collect($patrimonio)->where('nivel', 3)->sum('saldo');

        return [
            'activo' => $activo,
            'pasivo' => $pasivo,
            'patrimonio' => $patrimonio,
            'total_activo' => $totalActivo,
            'total_pasivo' => $totalPasivo,
            'total_patrimonio' => $totalPatrimonio,
            'total_pasivo_patrimonio' => $totalPasivo + $totalPatrimonio,
            'diferencia' => $totalActivo - ($totalPasivo + $totalPatrimonio),
            'fecha' => $fecha,
            'cuentas' => $cuentas,
        ];
    }

    private function buildTree(&$items, $parentId = null, $depth = 0): array
    {
        $tree = [];
        foreach ($items as $i => $item) {
            if ($item['padre_id'] === $parentId) {
                $node = $item;
                $children = $this->buildTree($items, $item['padre_id'] ?? 0, $depth + 1);
                if ($children) {
                    $node['children'] = $children;
                }
                $tree[] = $node;
                unset($items[$i]);
            }
        }
        return $tree;
    }
}

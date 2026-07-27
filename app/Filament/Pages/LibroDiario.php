<?php

namespace App\Filament\Pages;

use App\Models\AsientoContable;
use App\Models\PlanCuenta;
use Filament\Pages\Page;

class LibroDiario extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Libro Diario';
    protected static string|\UnitEnum|null $navigationGroup = 'Contabilidad';
    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Libro Diario';
    protected string $view = 'filament.pages.libro-diario';

    public function getData(): array
    {
        $desde = request('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = request('hasta', now()->format('Y-m-d'));
        $search = request('search');

        $query = AsientoContable::with('detalle.cuenta', 'user')->orderBy('fecha', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('glosa', 'like', "%{$search}%");
            });
        }

        $asientos = $query->whereBetween('fecha', [$desde, $hasta])->get();
        $cuentas = PlanCuenta::where('estado', true)->orderBy('codigo')->get();

        return [
            'asientos' => $asientos,
            'cuentas' => $cuentas,
            'desde' => $desde,
            'hasta' => $hasta,
            'search' => $search,
            'meses' => $this->getMeses(),
            'next_numero' => AsientoContable::nextNumber(),
        ];
    }

    private function getMeses(): array
    {
        $meses = [];
        for ($i = 0; $i < 12; $i++) {
            $d = now()->subMonths($i);
            $meses[$d->format('Y-m')] = $d->translatedFormat('M Y');
        }
        return $meses;
    }
}

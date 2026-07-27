<?php

namespace App\Filament\Pages;

use App\Models\AsientoContable;
use App\Models\PlanCuenta;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
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

    /**
     * Anulación con el modal de confirmación estándar de Filament
     * (reemplaza al confirm() nativo del navegador).
     */
    public function anularAction(): Action
    {
        return Action::make('anular')
            ->label('Anular')
            ->link()
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()
            ->modalHeading('Anular asiento')
            ->modalDescription('El asiento quedará anulado y se excluirá del Libro Mayor y del Balance General. Esta acción no se puede deshacer.')
            ->modalSubmitActionLabel('Sí, anular')
            ->action(function (array $arguments) {
                abort_unless((bool) auth()->user()?->can('contabilidad.anular'), 403);

                $asiento = AsientoContable::findOrFail((int) ($arguments['asiento'] ?? 0));

                if ($asiento->estado !== 'anulado') {
                    $asiento->update(['estado' => 'anulado']);
                }

                Notification::make()->success()
                    ->title('Asiento ' . $asiento->numero . ' anulado')
                    ->send();

                // Recargar conservando los filtros activos (fecha/búsqueda)
                return redirect(static::getUrl(array_filter([
                    'desde'  => $arguments['desde'] ?? null,
                    'hasta'  => $arguments['hasta'] ?? null,
                    'search' => $arguments['search'] ?? null,
                ])));
            });
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

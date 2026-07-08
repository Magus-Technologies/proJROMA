<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

/**
 * Configuración de correlativos por tipo de documento y sucursal.
 * Sirve para arrancar la numeración electrónica donde la dejó el sistema
 * anterior cuando se migra o se limpia la data.
 */
class ConfigurarCorrelativos extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-hashtag';
    protected static ?string $navigationLabel = 'Correlativos';
    protected static ?string $title = 'Configurar Correlativos';
    protected static string|\UnitEnum|null $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 90;
    protected string $view = 'filament.pages.configurar-correlativos';

    /** @var array<int, array<string, mixed>> */
    public array $correlativos = [];

    /** Sucursal seleccionada en el filtro ('' = todas). */
    public string $sucursalFiltro = '';

    /** @var array<int, string|int> */
    public array $sucursales = [];

    public function mount(): void
    {
        $this->correlativos = DB::table('documentos_empresas as de')
            ->join('documentos_sunat as ds', 'ds.id_tido', '=', 'de.id_tido')
            ->where('de.id_empresa', (int) session('id_empresa'))
            ->orderBy('de.sucursal')
            ->orderBy('de.id_tido')
            ->get(['de.id_tido', 'de.sucursal', 'de.serie', 'de.numero', 'ds.nombre', 'ds.abreviatura'])
            ->map(fn ($r): array => [
                'id_tido'     => $r->id_tido,
                'sucursal'    => $r->sucursal,
                'nombre'      => $r->nombre,
                'abreviatura' => $r->abreviatura,
                'serie'       => $r->serie,
                'numero'      => (int) $r->numero,
            ])
            ->toArray();

        $this->sucursales = collect($this->correlativos)
            ->pluck('sucursal')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public function save(): void
    {
        foreach ($this->correlativos as $row) {
            DB::table('documentos_empresas')
                ->where('id_empresa', (int) session('id_empresa'))
                ->where('id_tido', $row['id_tido'])
                ->where('sucursal', $row['sucursal'])
                ->update([
                    'serie'  => strtoupper(trim((string) $row['serie'])),
                    'numero' => (int) $row['numero'],
                ]);
        }

        Notification::make()
            ->success()
            ->title('Correlativos actualizados')
            ->body('La numeración se aplicará desde el próximo documento emitido.')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('guardar')
                ->label('Guardar cambios')
                ->icon('heroicon-m-check')
                ->requiresConfirmation()
                ->modalHeading('¿Guardar los correlativos?')
                ->modalDescription('Verificá bien los números: si ponés uno menor al ya emitido, SUNAT puede rechazar por duplicado.')
                ->action('save'),
        ];
    }
}

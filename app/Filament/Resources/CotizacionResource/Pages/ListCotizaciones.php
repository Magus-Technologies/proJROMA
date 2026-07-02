<?php

namespace App\Filament\Resources\CotizacionResource\Pages;

use App\Filament\Resources\CotizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\On;

class ListCotizaciones extends ListRecords
{
    protected static string $resource = CotizacionResource::class;

    public ?int $recienCreada = null;

    public function mount(): void
    {
        parent::mount();

        // Round-trip through the browser so the table is booted when the
        // listener runs (mounting a table action directly in mount() fails).
        if ($id = (int) request()->query('previsualizar')) {
            $this->recienCreada = $id;
            $this->dispatch('abrir-vista-previa', id: $id);
            // Clean the URL so a refresh doesn't reopen the modal
            $this->js("history.replaceState({}, '', '" . CotizacionResource::getUrl('index') . "')");
        }
    }

    #[On('abrir-vista-previa')]
    public function abrirVistaPrevia(int $id): void
    {
        $this->mountTableAction('vista_previa', (string) $id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('nueva_cotizacion')
                ->label('Nueva Cotización')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(CotizacionResource::getUrl('create')),
        ];
    }
}

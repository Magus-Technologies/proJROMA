<?php

namespace App\Filament\Resources\EmpresaResource\Pages;

use App\Filament\Resources\EmpresaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmpresas extends ListRecords
{
    protected static string $resource = EmpresaResource::class;

    protected function getHeaderActions(): array
    {
        // El botón "Crear" se oculta solo cuando ya existe una empresa:
        // CreateAction respeta EmpresaResource::canCreate() (límite de 1).
        return [
            CreateAction::make()
                ->modalWidth('5xl')
                ->modalHeading('Nueva Empresa')
                ->createAnother(false),
        ];
    }
}

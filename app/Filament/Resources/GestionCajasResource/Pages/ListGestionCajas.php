<?php

namespace App\Filament\Resources\GestionCajasResource\Pages;

use App\Filament\Resources\GestionCajasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGestionCajas extends ListRecords
{
    protected static string $resource = GestionCajasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa']   = (int) session('id_empresa');
                    $data['sucursal']     = (int) session('sucursal');
                    $data['saldo_actual'] = $data['saldo_actual'] ?? 0;
                    $data['moneda']       = 'PEN';

                    return $data;
                }),
        ];
    }
}

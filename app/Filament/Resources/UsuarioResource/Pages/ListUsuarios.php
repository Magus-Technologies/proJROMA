<?php

namespace App\Filament\Resources\UsuarioResource\Pages;

use App\Filament\Resources\UsuarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsuarios extends ListRecords
{
    protected static string $resource = UsuarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['id_empresa'] = (int) session('id_empresa');
                    $data['sucursal']   = (int) session('sucursal', 1);

                    // Columnas NOT NULL heredadas del sistema legacy que el form no pide
                    $data['fecha_inicio'] = $data['fecha_inicio'] ?? now()->toDateString();
                    $data['fecha_salida'] = $data['fecha_salida'] ?? '2030-12-31';
                    $data['funciones']    = $data['funciones'] ?? '';

                    return $data;
                }),
        ];
    }
}

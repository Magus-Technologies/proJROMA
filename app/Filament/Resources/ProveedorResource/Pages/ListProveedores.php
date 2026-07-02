<?php

namespace App\Filament\Resources\ProveedorResource\Pages;

use App\Filament\Resources\ProveedorResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProveedores extends ListRecords
{
    protected static string $resource = ProveedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa']       = (int) session('id_empresa');
                    $data['fecha_create']     = now();
                    $data['estado']           = 1;
                    $data['nombre_comercial'] = $data['nombre_comercial'] ?? '';
                    $data['direccion']        = $data['direccion'] ?? '';
                    $data['direccion2']       = '';
                    $data['telefono']         = $data['telefono'] ?? '';
                    $data['telefono2']        = '';
                    $data['email']            = $data['email'] ?? '';

                    return $data;
                }),

            Action::make('excel')
                ->label('Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(route('reporte.proveedores.xls'))
                ->openUrlInNewTab(),
        ];
    }
}

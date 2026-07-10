<?php

namespace App\Filament\Resources\MotivoMovimientoResource\Pages;

use App\Filament\Resources\MotivoMovimientoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMotivoMovimiento extends CreateRecord
{
    protected static string $resource = MotivoMovimientoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_empresa'] = (int) session('id_empresa');
        return $data;
    }
}

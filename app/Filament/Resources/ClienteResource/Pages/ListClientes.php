<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use App\Imports\ClientesImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListClientes extends ListRecords
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa'] = (int) session('id_empresa');
                    return $data;
                }),
            Actions\Action::make('plantilla')
                ->label('Descargar Plantilla')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->url(route('reporte.clientes.plantilla'))
                ->openUrlInNewTab(),
            Actions\Action::make('importar')
                ->label('Importar Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('archivo')
                        ->label('Archivo Excel')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->maxSize(5120)
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $tmpPath = $data['archivo']->store('imports');
                    $fullPath = Storage::path($tmpPath);

                    try {
                        $import = new ClientesImport((int) session('id_empresa'));
                        Excel::import($import, $fullPath);
                        $errores = $import->failures();
                        $total = $import->getRowCount() ?? 0;

                        if ($errores->isNotEmpty()) {
                            $erroresStr = $errores->take(5)->map(fn ($e) =>
                                "Fila {$e->row()}: " . implode(', ', $e->errors())
                            )->implode("\n");
                            Notification::make()
                                ->warning()
                                ->title('Importado con errores')
                                ->body("{$total} procesados. Errores:\n{$erroresStr}")
                                ->send();
                        } else {
                            Notification::make()
                                ->success()
                                ->title('Importación completada')
                                ->body("Se importaron {$total} clientes correctamente.")
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Error al importar')
                            ->body($e->getMessage())
                            ->send();
                    } finally {
                        Storage::delete($tmpPath);
                    }
                }),
            Actions\Action::make('excel')
                ->label('Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(route('reporte.clientes.xls'))
                ->openUrlInNewTab(),
        ];
    }
}

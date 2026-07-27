<?php
namespace App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource;
use App\Filament\Resources\ProductoResource\Widgets\ProductoStats;
use App\Imports\ProductosImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
class ListProductos extends ListRecords {
    protected static string $resource = ProductoResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ProductoStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa'] = (int) session('id_empresa');
                    $data['sucursal']   = (int) session('sucursal', 1);

                    // Columnas NOT NULL heredadas del sistema legacy que el form no pide
                    $data['ultima_salida'] = $data['ultima_salida'] ?? now()->toDateString();
                    $data['codsunat']      = $data['codsunat'] ?? '-';

                    return $data;
                }),
            Actions\Action::make('plantilla')
                ->label('Descargar Plantilla')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->url(route('reporte.productos.plantilla'))
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
                        $import = new ProductosImport(
                            (int) session('id_empresa'),
                            (int) session('sucursal', 1),
                        );
                        Excel::import($import, $fullPath);

                        $creados = $import->getCreados();
                        $duplicados = $import->getDuplicados();
                        $fallas = $import->failures();

                        $resumen = "Se importaron {$creados} productos.";
                        if ($duplicados > 0) {
                            $resumen .= " {$duplicados} se saltaron por código repetido.";
                        }

                        if ($fallas->isNotEmpty()) {
                            $erroresStr = $fallas->take(5)->map(fn ($f) =>
                                "Fila {$f->row()}: " . implode(', ', $f->errors())
                            )->implode("\n");
                            Notification::make()
                                ->warning()
                                ->title('Importado con observaciones')
                                ->body("{$resumen}\nErrores:\n{$erroresStr}")
                                ->send();
                        } else {
                            Notification::make()
                                ->success()
                                ->title('Importación completada')
                                ->body($resumen)
                                ->send();
                        }
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title('Error al importar')
                            ->body($e->getMessage())
                            ->send();
                    } finally {
                        Storage::delete($tmpPath);
                    }
                }),
        ];
    }
}

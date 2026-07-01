<?php

namespace App\Filament\Resources;

use App\Models\Cliente;
use App\Filament\Resources\ClienteResource\Pages;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Clientes';
    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $label           = 'Cliente';
    protected static ?string $pluralLabel     = 'Clientes';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('documento')
                ->label('RUC / DNI')
                ->maxLength(15)
                ->suffixAction(
                    Action::make('consultar_doc')
                        ->icon('heroicon-m-magnifying-glass')
                        ->tooltip('Consultar SUNAT / RENIEC')
                        ->action(function ($state, $set) {
                            $doc   = trim($state ?? '');
                            $len   = strlen($doc);
                            $url   = config('apisperu.url');
                            $token = config('apisperu.token');

                            if (!in_array($len, [8, 11])) {
                                Notification::make()->warning()->title('Ingresá 8 dígitos (DNI) o 11 dígitos (RUC).')->send();
                                return;
                            }

                            try {
                                if ($len === 8) {
                                    $data = Http::timeout(8)->get("{$url}/dni/{$doc}", ['token' => $token])->json();
                                    $nombre = trim(implode(' ', array_filter([
                                        $data['nombres'] ?? '', $data['apellidoPaterno'] ?? '', $data['apellidoMaterno'] ?? '',
                                    ])));
                                    if (!$nombre) {
                                        Notification::make()->warning()->title($data['message'] ?? 'DNI no encontrado.')->send();
                                        return;
                                    }
                                    $set('datos', $nombre);
                                    Notification::make()->success()->title('Datos cargados desde RENIEC')->send();
                                } else {
                                    $data = Http::timeout(8)->get("{$url}/ruc/{$doc}", ['token' => $token])->json();
                                    if (empty($data['razonSocial'])) {
                                        Notification::make()->warning()->title('RUC no encontrado.')->send();
                                        return;
                                    }
                                    $dir = collect([
                                        $data['direccion'] ?? '', $data['distrito'] ?? '',
                                        $data['provincia'] ?? '', $data['departamento'] ?? '',
                                    ])->filter()->implode(', ');
                                    $set('datos', $data['razonSocial']);
                                    $set('direccion', $dir);
                                    Notification::make()->success()->title('Datos cargados desde SUNAT')->send();
                                }
                            } catch (\Throwable) {
                                Notification::make()->warning()->title('Error al consultar. Intentá de nuevo.')->send();
                            }
                        })
                ),
            TextInput::make('datos')->label('Razón Social / Nombre')->required()->maxLength(200)->columnSpanFull(),
            TextInput::make('direccion')->label('Dirección')->maxLength(200)->columnSpanFull(),
            TextInput::make('distrito')->label('Distrito')->maxLength(100),
            TextInput::make('telefono')->label('Teléfono')->maxLength(20),
            TextInput::make('email')->label('Email')->email()->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('documento')->label('RUC/DNI')->searchable()->sortable(),
                TextColumn::make('datos')->label('Nombre / Razón Social')->searchable()->sortable()->wrap(),
                TextColumn::make('distrito')->label('Distrito')->toggleable(),
                TextColumn::make('telefono')->label('Teléfono')->toggleable(),
                TextColumn::make('email')->label('Email')->toggleable(),
                TextColumn::make('ultima_venta')->label('Última venta')->date('d/m/Y')->sortable()->toggleable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Action $action, Cliente $record) {
                        if ($record->ventas()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('No se puede eliminar')
                                ->body('El cliente tiene ventas registradas.')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('datos');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
        ];
    }
}

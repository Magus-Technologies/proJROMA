<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedorResource\Pages;
use App\Models\Proveedor;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Unique;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Proveedores';
    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $label           = 'Proveedor';
    protected static ?string $pluralLabel     = 'Proveedores';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('ruc')
                ->label('RUC / DNI')
                ->required()
                ->maxLength(11)
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule) => $rule->where('id_empresa', (int) session('id_empresa')),
                )
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
                                    $set('razon_social', $nombre);
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
                                    $set('razon_social', $data['razonSocial']);
                                    $set('direccion', $dir);
                                    Notification::make()->success()->title('Datos cargados desde SUNAT')->send();
                                }
                            } catch (\Throwable) {
                                Notification::make()->warning()->title('Error al consultar. Intentá de nuevo.')->send();
                            }
                        })
                ),
            TextInput::make('razon_social')->label('Razón Social')->required()->maxLength(200)->columnSpanFull(),
            TextInput::make('nombre_comercial')->label('Nombre Comercial')->maxLength(255)->columnSpanFull(),
            TextInput::make('direccion')->label('Dirección')->maxLength(100)->columnSpanFull(),
            TextInput::make('telefono')
                ->label('Teléfono')
                ->tel()
                ->mask('99999999999999999999')
                ->maxLength(20)
                ->regex('/^[0-9]*$/')
                ->validationMessages(['regex' => 'El teléfono solo puede contener números.']),
            TextInput::make('email')->label('Email')->email()->maxLength(150),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ruc')->label('RUC/DNI')->searchable()->sortable(),
                TextColumn::make('razon_social')->label('Razón Social')->searchable()->sortable()->wrap(),
                TextColumn::make('nombre_comercial')->label('Nombre Comercial')->toggleable()->searchable()->placeholder('—'),
                TextColumn::make('telefono')->label('Teléfono')->toggleable()->placeholder('—'),
                TextColumn::make('email')->label('Email')->toggleable()->placeholder('—'),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('razon_social');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProveedores::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MercadoResource\Pages;
use App\Models\TmsMercado;
use App\Models\Ubigeo;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MercadoResource extends Resource
{
    protected static ?string $model = TmsMercado::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Mercados';
    protected static string|\UnitEnum|null $navigationGroup = 'Transporte (TMS)';
    protected static ?int $navigationSort = 20;
    protected static ?string $label = 'Mercado';
    protected static ?string $pluralLabel = 'Mercados';
    protected static ?string $slug = 'tms-mercados';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(120),
            TextInput::make('direccion')->label('Dirección (específica)')->required()->maxLength(245)
                ->helperText('A dónde llega el vehículo.'),
            TextInput::make('referencia')->label('Referencia')->maxLength(245),

            Select::make('dep')->label('Departamento')
                ->options(fn () => Ubigeo::departamentos())
                ->searchable()
                ->live()
                ->dehydrated(false)
                ->afterStateUpdated(function (Set $set) {
                    $set('prov', null);
                    $set('ubigeo', null);
                }),
            Select::make('prov')->label('Provincia')
                ->options(fn (Get $get) => Ubigeo::provincias($get('dep')))
                ->searchable()
                ->live()
                ->dehydrated(false)
                ->disabled(fn (Get $get) => blank($get('dep')))
                ->afterStateUpdated(fn (Set $set) => $set('ubigeo', null)),
            Select::make('ubigeo')->label('Distrito')
                ->options(fn (Get $get) => Ubigeo::distritos($get('dep'), $get('prov')))
                ->searchable()
                ->disabled(fn (Get $get) => blank($get('prov'))),

            TextInput::make('telefono')->label('Teléfono')->maxLength(20),
        ]);
    }

    /** Completa el nombre del distrito (columna de texto) a partir del código ubigeo elegido. */
    public static function completarDistrito(array $data): array
    {
        $data['distrito'] = Ubigeo::nombreDistrito($data['ubigeo'] ?? null) ?? ($data['distrito'] ?? null);

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('direccion')->label('Dirección')->placeholder('—')->wrap()->limit(50),
                TextColumn::make('distrito')->label('Distrito')->placeholder('—'),
                TextColumn::make('telefono')->label('Teléfono')->placeholder('—'),
                IconColumn::make('estado')->label('Estado')->boolean(),
            ])
            ->actions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        // Precarga departamento y provincia desde el código ubigeo guardado.
                        if (filled($data['ubigeo'] ?? null) && strlen($data['ubigeo']) === 6) {
                            $data['dep']  = substr($data['ubigeo'], 0, 2);
                            $data['prov'] = substr($data['ubigeo'], 2, 2);
                        }

                        return $data;
                    })
                    ->mutateDataUsing(fn (array $data): array => static::completarDistrito($data)),
                Action::make('toggle')
                    ->label(fn (TmsMercado $record): string => $record->estado ? 'Desactivar' : 'Activar')
                    ->icon(fn (TmsMercado $record): string => $record->estado ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (TmsMercado $record): string => $record->estado ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (TmsMercado $record) => $record->update(['estado' => $record->estado ? 0 : 1])),
            ])
            ->defaultSort('nombre', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMercados::route('/'),
        ];
    }
}

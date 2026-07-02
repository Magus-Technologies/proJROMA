<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RutaResource\Pages;
use App\Models\TmsMercado;
use App\Models\TmsRuta;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RutaResource extends Resource
{
    protected static ?string $model = TmsRuta::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Rutas';
    protected static string|\UnitEnum|null $navigationGroup = 'Transporte (TMS)';
    protected static ?int $navigationSort = 10;
    protected static ?string $label = 'Ruta';
    protected static ?string $pluralLabel = 'Rutas';
    protected static ?string $slug = 'tms-rutas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(120),
            TextInput::make('descripcion')->label('Descripción')->maxLength(245),

            Repeater::make('puntos')
                ->relationship()
                ->label('Puntos de la ruta (mercados / tiendas)')
                ->schema([
                    Select::make('tipo')
                        ->label('Tipo')
                        ->options(['MERCADO' => 'Mercado', 'TIENDA' => 'Tienda (cliente)'])
                        ->default('MERCADO')
                        ->live()
                        ->required()
                        ->columnSpan(1),

                    Select::make('id_mercado')
                        ->label('Mercado')
                        ->options(fn () => TmsMercado::query()
                            ->where('id_empresa', (int) session('id_empresa'))
                            ->where('sucursal', (int) session('sucursal'))
                            ->where('estado', 1)
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray())
                        ->searchable()
                        ->visible(fn (callable $get) => $get('tipo') === 'MERCADO')
                        ->required(fn (callable $get) => $get('tipo') === 'MERCADO')
                        ->columnSpan(2),

                    Select::make('id_cliente')
                        ->label('Tienda (cliente)')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search) => DB::table('clientes')
                            ->where('id_empresa', (int) session('id_empresa'))
                            ->where(fn ($q) => $q->where('datos', 'like', "%{$search}%")
                                ->orWhere('documento', 'like', "%{$search}%"))
                            ->orderBy('datos')
                            ->limit(20)
                            ->pluck('datos', 'id_cliente')
                            ->toArray())
                        ->getOptionLabelUsing(fn ($value) => DB::table('clientes')
                            ->where('id_cliente', $value)->value('datos'))
                        ->visible(fn (callable $get) => $get('tipo') === 'TIENDA')
                        ->required(fn (callable $get) => $get('tipo') === 'TIENDA')
                        ->columnSpan(2),
                ])
                ->columns(3)
                ->defaultItems(0)
                ->addActionLabel('Agregar punto')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('descripcion')->label('Descripción')->placeholder('—')->wrap()->limit(60),
                TextColumn::make('puntos_count')->label('Puntos')->badge()
                    ->counts('puntos'),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn (int $state): string => $state ? 'Activa' : 'Inactiva')
                    ->color(fn (int $state): string => $state ? 'success' : 'danger'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (TmsRuta $record): string => $record->estado ? 'Desactivar' : 'Activar')
                    ->icon(fn (TmsRuta $record): string => $record->estado ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (TmsRuta $record): string => $record->estado ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (TmsRuta $record) => $record->update(['estado' => $record->estado ? 0 : 1])),
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
            'index' => Pages\ListRutas::route('/'),
        ];
    }
}

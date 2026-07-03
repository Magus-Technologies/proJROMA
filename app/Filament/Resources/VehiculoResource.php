<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Tms;
use App\Filament\Resources\VehiculoResource\Pages;
use App\Models\TmsTipoVehiculo;
use App\Models\TmsVehiculo;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehiculoResource extends Resource
{
    protected static ?string $model = TmsVehiculo::class;

    protected static ?string $cluster = Tms::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Vehículos';
    protected static ?int $navigationSort = 0;
    protected static ?string $label = 'Vehículo';
    protected static ?string $pluralLabel = 'Vehículos';
    protected static ?string $slug = 'vehiculos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('placa')->label('Placa')->required()->maxLength(15),
            Select::make('id_tipo')->label('Tipo')
                ->options(function (): array {
                    $empresa = (int) session('id_empresa');
                    $tipos = TmsTipoVehiculo::where('id_empresa', $empresa)
                        ->where('estado', 1)->orderBy('nombre')->pluck('nombre', 'id');

                    if ($tipos->isEmpty()) {
                        foreach (['CAMIONETA', 'FURGONETA', 'CAMION', 'MOTO', 'OTRO'] as $nombre) {
                            TmsTipoVehiculo::create([
                                'nombre'     => $nombre,
                                'id_empresa' => $empresa,
                                'estado'     => 1,
                            ]);
                        }
                        return TmsTipoVehiculo::where('id_empresa', $empresa)
                            ->where('estado', 1)->orderBy('nombre')->pluck('nombre', 'id')->toArray();
                    }

                    return $tipos->toArray();
                })
                ->searchable()
                ->required()
                ->default(fn () => TmsTipoVehiculo::where('id_empresa', (int) session('id_empresa'))
                    ->where('nombre', 'CAMIONETA')->value('id'))
                ->createOptionForm([
                    TextInput::make('nombre')->label('Nombre')->required()->maxLength(60),
                ])
                ->createOptionUsing(function (array $data): int {
                    return TmsTipoVehiculo::create([
                        'nombre'     => strtoupper(trim($data['nombre'])),
                        'id_empresa' => (int) session('id_empresa'),
                        'estado'     => 1,
                    ])->id;
                }),
            TextInput::make('marca')->label('Marca')->maxLength(60),
            TextInput::make('modelo')->label('Modelo')->maxLength(60),
            TextInput::make('anio')->label('Año')->numeric()->integer()->minValue(1980)->maxValue(2100),

            TextInput::make('capacidad_kg')->label('Capacidad de carga (kg)')->required()->numeric()->minValue(0)
                ->helperText('Peso máximo que puede llevar.'),
            TextInput::make('tara_kg')->label('Tara / peso vacío (kg)')->numeric()->minValue(0),

            TextInput::make('largo_m')->label('Largo (m)')->numeric()->minValue(0),
            TextInput::make('ancho_m')->label('Ancho (m)')->numeric()->minValue(0),
            TextInput::make('alto_m')->label('Alto (m)')->numeric()->minValue(0),
            TextInput::make('capacidad_m3')->label('Volumen (m³)')->numeric()->minValue(0),

            DatePicker::make('soat_vence')->label('SOAT vence'),
            DatePicker::make('rev_tecnica_vence')->label('Rev. técnica vence'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('placa')->label('Placa')->searchable()->sortable(),
                TextColumn::make('tipo.nombre')->label('Tipo')->badge(),
                TextColumn::make('marca')->label('Marca / Modelo')->placeholder('—')
                    ->formatStateUsing(fn ($state, TmsVehiculo $r): string => trim(($r->marca ?? '') . ' ' . ($r->modelo ?? '')) ?: '—'),
                TextColumn::make('capacidad_kg')->label('Capacidad')->sortable()
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 0) . ' kg'),
                TextColumn::make('capacidad_m3')->label('Volumen')->placeholder('—')
                    ->formatStateUsing(fn ($state): string => $state ? number_format((float) $state, 2) . ' m³' : '—'),
                TextColumn::make('soat_vence')->label('SOAT')->date('d/m/Y')->placeholder('—')
                    ->color(fn ($state): string => $state && $state->isPast() ? 'danger' : 'gray'),
                IconColumn::make('estado')->label('Estado')->boolean(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (TmsVehiculo $record): string => $record->estado ? 'Desactivar' : 'Activar')
                    ->icon(fn (TmsVehiculo $record): string => $record->estado ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (TmsVehiculo $record): string => $record->estado ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (TmsVehiculo $record) => $record->update(['estado' => $record->estado ? 0 : 1])),
            ])
            ->defaultSort('placa', 'asc');
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
            'index' => Pages\ListVehiculos::route('/'),
        ];
    }
}

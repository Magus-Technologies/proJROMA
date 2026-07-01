<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArqueoDiarioResource\Pages;
use App\Models\ArqueoDiario;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArqueoDiarioResource extends Resource
{
    protected static ?string $model = ArqueoDiario::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Arqueo Diario';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 4;
    protected static ?string $label = 'Arqueo';
    protected static ?string $pluralLabel = 'Arqueo Diario';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha_arqueo')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('vendedor')
                    ->label('Vendedor')
                    ->searchable(),

                TextColumn::make('cobros_efectivo')
                    ->label('Cobros Efectivo')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('cobros_bancos')
                    ->label('Cobros Banco')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ingresos_efectivo')
                    ->label('Ingresos Efect.')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('egresos_efectivo')
                    ->label('Egresos Efect.')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('diferencia_efectivo')
                    ->label('Dif. Efectivo')
                    ->money('PEN')
                    ->color(fn (float $state): string => $state == 0 ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('diferencia_bancos')
                    ->label('Dif. Banco')
                    ->money('PEN')
                    ->color(fn (float $state): string => $state == 0 ? 'success' : 'danger')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('cuadra_efectivo')
                    ->label('Cuadra Efect.')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                IconColumn::make('cuadra_bancos')
                    ->label('Cuadra Banco')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('fecha_arqueo')
                    ->form([
                        DatePicker::make('fecha_arqueo')
                            ->label('Fecha')
                            ->default(now()),
                    ])
                    ->query(fn (Builder $query, array $data): Builder =>
                        $query->when($data['fecha_arqueo'], fn (Builder $q) =>
                            $q->whereDate('fecha_arqueo', $data['fecha_arqueo'])
                        )
                    ),
            ])
            ->defaultSort('fecha_arqueo', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', session('sucursal'));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArqueoDiario::route('/'),
        ];
    }
}

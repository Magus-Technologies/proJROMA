<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GestionCajasResource\Pages;
use App\Models\Caja;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class GestionCajasResource extends Resource
{
    protected static ?string $model = Caja::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Gestión de Cajas';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'Caja';
    protected static ?string $pluralLabel = 'Gestión de Cajas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(100),

            Select::make('id_usuario_responsable')
                ->label('Responsable')
                ->options(fn () => User::where('id_empresa', (int) session('id_empresa'))
                    ->pluck('nombres', 'usuario_id')
                    ->toArray())
                ->searchable(),

            Select::make('id_caja_padre')
                ->label('Caja Padre (opcional)')
                ->options(fn () => Caja::where('id_empresa', (int) session('id_empresa'))
                    ->whereNull('id_caja_padre')
                    ->pluck('nombre', 'id')
                    ->toArray())
                ->nullable()
                ->searchable()
                ->helperText('Si depende de una caja principal, es una caja hija.'),

            Select::make('estado')
                ->label('Estado')
                ->options([
                    'ACTIVA'   => 'Activa',
                    'INACTIVA' => 'Inactiva',
                ])
                ->default('ACTIVA')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jerarquia')
                    ->label('Tipo')
                    ->badge()
                    ->getStateUsing(fn (Caja $record): string =>
                        $record->id_caja_padre ? 'HIJA' : 'PRINCIPAL'
                    )
                    ->color(fn (string $state): string =>
                        $state === 'PRINCIPAL' ? 'primary' : 'warning'
                    ),

                TextColumn::make('responsable.nombres')
                    ->label('Responsable')
                    ->placeholder('— Sin responsable —'),

                TextColumn::make('padre.nombre')
                    ->label('Caja Padre')
                    ->placeholder('—'),

                TextColumn::make('saldo_actual')
                    ->label('Saldo Actual')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ACTIVA'   => 'success',
                        'INACTIVA' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'ACTIVA'   => 'Activa',
                        'INACTIVA' => 'Inactiva',
                    ]),
            ])
            ->actions([
                EditAction::make(),

                Action::make('toggle_estado')
                    ->label(fn (Caja $record): string => $record->estado === 'ACTIVA' ? 'Desactivar' : 'Activar')
                    ->icon(fn (Caja $record): string => $record->estado === 'ACTIVA' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Caja $record): string => $record->estado === 'ACTIVA' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Caja $record): void {
                        $nuevoEstado = $record->estado === 'ACTIVA' ? 'INACTIVA' : 'ACTIVA';
                        DB::table('cajas')->where('id', $record->id)->update(['estado' => $nuevoEstado]);
                    }),

                Action::make('asignar_instrumentos')
                    ->label('Instrumentos')
                    ->icon('heroicon-o-credit-card')
                    ->color('info')
                    ->visible(fn (Caja $record): bool => $record->id_caja_padre !== null)
                    ->modalHeading('Asignar Métodos de Pago')
                    ->modalDescription(fn (Caja $record): string => "Caja: {$record->nombre}")
                    ->fillForm(fn (Caja $record): array => [
                        'instrumentos' => DB::table('caja_instrumentos')
                            ->where('id_caja', $record->id)
                            ->where('estado', 'ACTIVO')
                            ->get()
                            ->map(fn ($i) => $i->instrumento_tipo . '|' . ($i->instrumento_id ?? ''))
                            ->toArray(),
                    ])
                    ->form([
                        CheckboxList::make('instrumentos')
                            ->label('Métodos de pago disponibles')
                            ->options(function (): array {
                                $opts = ['EFECTIVO|' => 'Efectivo'];

                                DB::table('cuentas_bancarias as cb')
                                    ->leftJoin('bancos as b', 'b.id_banco', '=', 'cb.id_banco')
                                    ->where('cb.id_empresa', (int) session('id_empresa'))
                                    ->get(['cb.id_cuenta', 'cb.numero_cuenta', 'b.nombre as banco'])
                                    ->each(function ($c) use (&$opts) {
                                        $opts['TRANSFERENCIA|' . $c->id_cuenta] =
                                            'Transf: ' . ($c->banco ?? '') . ' ****' . substr((string) $c->numero_cuenta, -4);
                                    });

                                DB::table('billeteras_digitales as bd')
                                    ->leftJoin('billetera_tipos as bt', 'bt.id', '=', 'bd.id_billetera_tipo')
                                    ->where('bd.id_empresa', (int) session('id_empresa'))
                                    ->get(['bd.id_billetera', 'bd.titular', 'bt.nombre as tipo'])
                                    ->each(function ($b) use (&$opts) {
                                        $opts['BILLETERA_DIGITAL|' . $b->id_billetera] =
                                            'Billetera: ' . ($b->tipo ?? '') . ' - ' . $b->titular;
                                    });

                                return $opts;
                            }),
                    ])
                    ->action(function (Caja $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            DB::table('caja_instrumentos')->where('id_caja', $record->id)->delete();

                            foreach ($data['instrumentos'] ?? [] as $item) {
                                [$tipo, $id] = explode('|', $item, 2);
                                DB::table('caja_instrumentos')->insert([
                                    'id_caja'          => $record->id,
                                    'instrumento_tipo' => $tipo,
                                    'instrumento_id'   => $id !== '' ? (int) $id : null,
                                    'estado'           => 'ACTIVO',
                                ]);
                            }
                        });

                        Notification::make()->success()->title('Métodos de pago actualizados')->send();
                    }),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->with(['responsable', 'padre']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGestionCajas::route('/'),
        ];
    }
}

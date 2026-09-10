<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConductorResource\Pages;
use App\Models\TmsConductor;
use App\Models\User;
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

class ConductorResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'tms_conductores.ver';
    public const PERMISO_CREAR = 'tms_conductores.crear';
    public const PERMISO_EDITAR = 'tms_conductores.editar';

    protected static ?string $model = TmsConductor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Conductores';
    protected static string|\UnitEnum|null $navigationGroup = 'Transporte (TMS)';
    protected static ?int $navigationSort = 40;
    protected static ?string $label = 'Conductor';
    protected static ?string $pluralLabel = 'Conductores';
    protected static ?string $slug = 'tms-conductores';

    /** Días de anticipación con los que se avisa que una licencia vence. */
    public const DIAS_AVISO_LICENCIA = 30;

    protected static function licenciasPorVencer(): Builder
    {
        return static::getEloquentQuery()
            ->where('estado', 1)
            ->whereNotNull('licencia_vence')
            ->whereDate('licencia_vence', '<=', now()->startOfDay()->addDays(self::DIAS_AVISO_LICENCIA)->toDateString());
    }

    public static function getNavigationBadge(): ?string
    {
        $porVencer = static::licenciasPorVencer()->count();

        return $porVencer > 0 ? (string) $porVencer : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Licencias vencidas o por vencer en los próximos ' . self::DIAS_AVISO_LICENCIA . ' días';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getEloquentQuery()
            ->where('estado', 1)
            ->whereDate('licencia_vence', '<', now()->startOfDay()->toDateString())
            ->exists() ? 'danger' : 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            // Atajo: la mayoría de los conductores ya son usuarios del sistema,
            // así no hay que volver a escribir nombre, documento y teléfono.
            Select::make('id_usuario')
                ->label('Traer datos de un usuario')
                ->placeholder('— Cargar a mano —')
                ->helperText('Elegí un usuario y se completan los datos solos. Después podés corregirlos.')
                ->options(fn (): array => User::query()
                    ->where('id_empresa', (int) session('id_empresa'))
                    ->orderBy('nombres')
                    ->get()
                    ->mapWithKeys(fn (User $u): array => [
                        $u->usuario_id => trim($u->nombres . ' ' . ($u->apellidos ?? ''))
                            . ($u->num_doc ? ' — ' . $u->num_doc : ''),
                    ])
                    ->toArray())
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function ($state, callable $set): void {
                    $usuario = $state ? User::find($state) : null;

                    if (! $usuario) {
                        return;
                    }

                    $set('nombres', trim($usuario->nombres . ' ' . ($usuario->apellidos ?? '')));
                    $set('documento', $usuario->num_doc);
                    $set('telefono', $usuario->telefono);
                })
                ->columnSpanFull(),

            TextInput::make('nombres')->label('Nombres')->required()->maxLength(120),
            TextInput::make('documento')->label('Documento (DNI)')->maxLength(15),
            TextInput::make('telefono')->label('Teléfono')->maxLength(20),
            TextInput::make('licencia')->label('Licencia')->maxLength(30),
            TextInput::make('licencia_categoria')->label('Categoría')->maxLength(10),
            DatePicker::make('licencia_vence')->label('Licencia vence'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombres')->label('Nombres')->searchable()->sortable(),
                TextColumn::make('documento')->label('Documento')->placeholder('—'),
                TextColumn::make('licencia')->label('Licencia')->placeholder('—'),
                TextColumn::make('licencia_categoria')->label('Cat.')->placeholder('—'),
                TextColumn::make('licencia_vence')->label('Vence')->date('d/m/Y')->placeholder('—')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        ! $state => 'gray',
                        $state->isPast() => 'danger',
                        $state->lte(now()->addDays(self::DIAS_AVISO_LICENCIA)) => 'warning',
                        default => 'gray',
                    })
                    ->tooltip(fn ($state): ?string => match (true) {
                        ! $state => null,
                        $state->isPast() => 'Licencia vencida',
                        $state->lte(now()->addDays(self::DIAS_AVISO_LICENCIA)) => 'Vence pronto: hay que renovarla',
                        default => null,
                    })
                    ->sortable(),
                TextColumn::make('telefono')->label('Teléfono')->placeholder('—'),
                IconColumn::make('estado')->label('Estado')->boolean(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->visible(fn (): bool => auth()->user()?->can('tms_conductores.editar') ?? false)
                    ->label(fn (TmsConductor $record): string => $record->estado ? 'Desactivar' : 'Activar')
                    ->icon(fn (TmsConductor $record): string => $record->estado ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (TmsConductor $record): string => $record->estado ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (TmsConductor $record) => $record->update(['estado' => $record->estado ? 0 : 1])),
            ])
            ->defaultSort('nombres', 'asc');
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
            'index' => Pages\ListConductores::route('/'),
        ];
    }
}

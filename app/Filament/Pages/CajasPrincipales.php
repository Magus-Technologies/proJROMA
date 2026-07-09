<?php

namespace App\Filament\Pages;

use App\Models\Caja;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Panel del responsable de una caja principal: ve y administra las
 * cajas hijas que dependen de sus cajas principales.
 */
class CajasPrincipales extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Cajas Principales';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 6;
    protected static ?string $slug = 'cajas-principales';
    protected string $view = 'filament.pages.cajas-principales';

    public Collection $principales;

    /** Solo responsables de al menos una caja principal (además de caja.ver). */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user || ! $user->can('caja.ver')) {
            return false;
        }

        return Caja::where('id_empresa', (int) session('id_empresa'))
            ->whereNull('id_caja_padre')
            ->where('id_usuario_responsable', $user->usuario_id)
            ->exists();
    }

    public function mount(): void
    {
        $this->principales = $this->cajasPrincipalesDelUsuario();
    }

    protected function cajasPrincipalesDelUsuario(): Collection
    {
        return Caja::withCount('hijas')
            ->where('id_empresa', (int) session('id_empresa'))
            ->whereNull('id_caja_padre')
            ->where('id_usuario_responsable', (int) auth()->user()->usuario_id)
            ->orderBy('nombre')
            ->get();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Caja::query()
                ->with(['responsable', 'padre'])
                ->whereIn('id_caja_padre', $this->principales->pluck('id')))
            ->columns([
                TextColumn::make('nombre')
                    ->label('Caja hija')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('padre.nombre')
                    ->label('Caja principal')
                    ->badge()
                    ->color('primary')
                    ->visible(fn (): bool => $this->principales->count() > 1),

                TextColumn::make('responsable.nombres')
                    ->label('Responsable')
                    ->placeholder('Sin responsable')
                    ->formatStateUsing(fn ($state, Caja $record): string => trim(
                        ($record->responsable?->nombres ?? '') . ' ' . ($record->responsable?->apellidos ?? '')
                    ) ?: 'Sin responsable'),

                TextColumn::make('saldo_actual')
                    ->label('Saldo')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('movimientos_hoy')
                    ->label('Movs. hoy')
                    ->badge()
                    ->color('gray')
                    ->getStateUsing(fn (Caja $record): int => $record->movimientos()
                        ->whereDate('fecha', now()->toDateString())->count()),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'ACTIVA' ? 'success' : 'danger'),
            ])
            ->actions([
                Action::make('movimientos')
                    ->label('Movimientos')
                    ->iconButton()
                    ->tooltip('Últimos movimientos')
                    ->icon('heroicon-o-list-bullet')
                    ->color('gray')
                    ->modalHeading(fn (Caja $record): string => 'Movimientos — ' . $record->nombre)
                    ->modalContent(fn (Caja $record) => view('filament.caja.movimientos-caja', [
                        'caja'        => $record,
                        'movimientos' => $record->movimientos()->orderByDesc('id')->limit(30)->get(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('editar')
                    ->label('Editar')
                    ->iconButton()
                    ->tooltip('Editar caja hija')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->fillForm(fn (Caja $record): array => [
                        'nombre'                 => $record->nombre,
                        'id_usuario_responsable' => $record->id_usuario_responsable,
                    ])
                    ->form([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100),
                        Select::make('id_usuario_responsable')
                            ->label('Responsable')
                            ->options(fn () => User::where('id_empresa', (int) session('id_empresa'))
                                ->where('estado', '1')
                                ->orderBy('nombres')
                                ->get()
                                ->mapWithKeys(fn (User $u) => [$u->usuario_id => $u->nombre_completo])
                                ->toArray())
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, Caja $record): void {
                        $record->update($data);
                        Notification::make()->success()->title('Caja actualizada.')->send();
                    }),

                Action::make('toggle_estado')
                    ->label(fn (Caja $record): string => $record->estado === 'ACTIVA' ? 'Desactivar' : 'Activar')
                    ->iconButton()
                    ->tooltip(fn (Caja $record): string => $record->estado === 'ACTIVA' ? 'Desactivar caja' : 'Activar caja')
                    ->icon(fn (Caja $record): string => $record->estado === 'ACTIVA' ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                    ->color(fn (Caja $record): string => $record->estado === 'ACTIVA' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Caja $record): void {
                        $record->update(['estado' => $record->estado === 'ACTIVA' ? 'INACTIVA' : 'ACTIVA']);
                        Notification::make()->success()->title('Estado actualizado.')->send();
                    }),
            ])
            ->defaultSort('nombre');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nueva_hija')
                ->label('Nueva caja hija')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(100),
                    Select::make('id_caja_padre')
                        ->label('Caja principal')
                        ->options(fn () => $this->principales->pluck('nombre', 'id')->toArray())
                        ->default(fn () => $this->principales->count() === 1 ? $this->principales->first()->id : null)
                        ->required()
                        ->visible(fn (): bool => $this->principales->count() > 1),
                    Select::make('id_usuario_responsable')
                        ->label('Responsable (trabajador)')
                        ->options(fn () => User::where('id_empresa', (int) session('id_empresa'))
                            ->where('estado', '1')
                            ->orderBy('nombres')
                            ->get()
                            ->mapWithKeys(fn (User $u) => [$u->usuario_id => $u->nombre_completo])
                            ->toArray())
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Caja::create([
                        'nombre'                 => $data['nombre'],
                        'id_empresa'             => (int) session('id_empresa'),
                        'sucursal'               => (int) session('sucursal'),
                        'id_caja_padre'          => $data['id_caja_padre'] ?? $this->principales->first()->id,
                        'id_usuario_responsable' => $data['id_usuario_responsable'],
                        'saldo_actual'           => 0,
                        'moneda'                 => 'PEN',
                        'estado'                 => 'ACTIVA',
                    ]);
                    Notification::make()->success()->title('Caja hija creada.')->send();
                    $this->principales = $this->cajasPrincipalesDelUsuario();
                }),
        ];
    }
}

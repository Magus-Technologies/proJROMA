<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use Database\Seeders\PermissionSeeder;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

/**
 * Catálogo de permisos: SOLO LECTURA.
 *
 * Un permiso no es un dato que el usuario administre. Existe porque el código
 * lo comprueba (las constantes PERMISO_ACCESO de cada recurso) y se crea desde
 * el PermissionSeeder o una migración. Crear uno desde la interfaz produciría
 * una fila que nadie consulta, y borrar uno dejaría pantallas inaccesibles sin
 * forma de recuperarlas desde la interfaz.
 *
 * Lo que sí se hace con un permiso es asignarlo o quitarlo a un rol, y eso vive
 * en Roles (RoleResource), donde se marcan agrupados por módulo. Esta pantalla
 * responde a la pregunta inversa: qué permisos existen y quién tiene cada uno.
 */
class PermissionResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'permisos.ver';

    protected static ?string $model = Permission::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Permisos';
    protected static string|\UnitEnum|null $navigationGroup  = 'Administración';
    protected static ?int    $navigationSort  = 3;
    protected static ?string $label           = 'Permiso';
    protected static ?string $pluralLabel     = 'Permisos';

    /** @var array<string, array{grupo: string, descripcion: string}>|null */
    protected static ?array $catalogoCache = null;

    /**
     * Grupo y descripción legible de cada permiso, según el PermissionSeeder.
     * Los permisos añadidos después por una migración no están ahí: se muestran
     * igual, pero sin grupo, para que se noten y puedan agregarse al seeder.
     *
     * @return array<string, array{grupo: string, descripcion: string}>
     */
    public static function catalogo(): array
    {
        if (static::$catalogoCache !== null) {
            return static::$catalogoCache;
        }

        $catalogo = [];

        foreach (PermissionSeeder::groups() as $grupo => $permisos) {
            foreach ($permisos as $nombre => $descripcion) {
                $catalogo[$nombre] = ['grupo' => $grupo, 'descripcion' => $descripcion];
            }
        }

        return static::$catalogoCache = $catalogo;
    }

    /** El formulario no se usa: el recurso no crea ni edita permisos. */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('grupo')
                    ->label('Módulo')
                    ->getStateUsing(fn (Permission $record): string =>
                        static::catalogo()[$record->name]['grupo'] ?? 'Sin agrupar')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Sin agrupar' ? 'warning' : 'gray')
                    ->sortable(false),

                TextColumn::make('descripcion')
                    ->label('Qué permite')
                    ->getStateUsing(fn (Permission $record): ?string =>
                        static::catalogo()[$record->name]['descripcion'] ?? null)
                    ->placeholder('Sin descripción en el seeder')
                    ->wrap()
                    ->searchable(false),

                TextColumn::make('name')
                    ->label('Permiso')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->copyMessage('Permiso copiado')
                    ->tooltip('Es el nombre que el código comprueba. Clic para copiarlo.'),

                TextColumn::make('roles_count')
                    ->label('Roles')
                    ->counts('roles')
                    ->badge()
                    ->alignEnd()
                    ->color(fn (int $state): string => $state === 0 ? 'danger' : 'success')
                    ->tooltip('Cuántos roles tienen este permiso. En 0, nadie puede usar esa función.'),

                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('grupo')
                    ->label('Módulo')
                    ->options(fn (): array => collect(static::catalogo())
                        ->pluck('grupo')
                        ->unique()
                        ->sort()
                        ->mapWithKeys(fn (string $g): array => [$g => $g])
                        ->toArray())
                    ->query(function ($query, array $data) {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        $nombres = collect(static::catalogo())
                            ->filter(fn (array $c): bool => $c['grupo'] === $data['value'])
                            ->keys();

                        return $query->whereIn('name', $nombres);
                    }),

                SelectFilter::make('asignacion')
                    ->label('Asignación')
                    ->options([
                        'asignados' => 'Asignados a algún rol',
                        'huerfanos' => 'Sin ningún rol',
                    ])
                    ->query(fn ($query, array $data) => match ($data['value'] ?? null) {
                        'asignados' => $query->has('roles'),
                        'huerfanos' => $query->doesntHave('roles'),
                        default     => $query,
                    }),
            ])
            ->defaultSort('name')
            ->actions([
                Action::make('verRoles')
                    ->label('Ver roles')
                    ->icon('heroicon-o-user-group')
                    ->color('info')
                    ->modalHeading(fn (Permission $record): string => "Roles con el permiso: {$record->name}")
                    ->modalDescription('Para asignar o quitar este permiso, edita el rol correspondiente en Administración → Roles.')
                    ->modalContent(fn (Permission $record) => view('filament.modals.permiso-roles', [
                        'roles' => $record->roles()->orderBy('nombre')->get(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
            ]);
    }

    /** Los permisos los define el código, no la interfaz. */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
        ];
    }
}

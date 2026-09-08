<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Rol;
use BackedEnum;
use Database\Seeders\PermissionSeeder;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'roles.ver';
    public const PERMISO_CREAR = 'roles.crear';
    public const PERMISO_EDITAR = 'roles.editar';
    public const PERMISO_BORRAR = 'roles.borrar';

    protected static ?string $model = Rol::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Roles';
    protected static string|\UnitEnum|null $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Rol';
    protected static ?string $pluralLabel = 'Roles';

    public static function sanitizeKey(string $label): string
    {
        return 'permisos_' . str_replace([' ', '(', ')', '/', '.'], '_', $label);
    }

    /**
     * Cards de la pantalla de permisos: cada módulo (como en el menú
     * lateral) agrupa los grupos de permisos del PermissionSeeder.
     */
    private const MODULOS = [
        'Facturación'      => ['icono' => 'heroicon-o-document-text',           'grupos' => ['Ventas', 'Notas Electrónicas', 'Guías Remisión']],
        'Cotizaciones'     => ['icono' => 'heroicon-o-clipboard-document-list', 'grupos' => ['Cotizaciones']],
        'Cobranzas'        => ['icono' => 'heroicon-o-banknotes',               'grupos' => ['Cuentas por Cobrar', 'Reporte Deudas', 'Mis Cobros']],
        'Pagos'            => ['icono' => 'heroicon-o-credit-card',             'grupos' => ['Pagos']],
        'Caja'             => ['icono' => 'heroicon-o-calculator',              'grupos' => ['Gestión de Cajas', 'Movimientos', 'Cierres y Cuadre', 'Asignaciones de Fondo', 'Mi Caja', 'Métodos de Pago', 'Códigos QR']],
        'Inventario'       => ['icono' => 'heroicon-o-cube',                    'grupos' => ['Productos', 'Compras', 'Recepción', 'Existencias', 'Ajustes / Cuadres', 'Traslados', 'Préstamos']],
        'Transporte (TMS)' => ['icono' => 'heroicon-o-truck',                   'grupos' => ['Mercados', 'Vehículos', 'Conductores', 'Rutas', 'Despachos']],
        'Maestros'         => ['icono' => 'heroicon-o-users',                   'grupos' => ['Clientes', 'Proveedores']],
        'Reportes'         => ['icono' => 'heroicon-o-chart-bar',               'grupos' => ['Reportes']],
        'Finanzas'         => ['icono' => 'heroicon-o-presentation-chart-bar', 'grupos' => ['Finanzas']],
        'Administración'   => ['icono' => 'heroicon-o-cog-6-tooth',             'grupos' => ['Usuarios', 'Roles', 'Permisos', 'Empresas', 'Sucursales', 'Auditoría', 'Correlativos']],
    ];

    public static function form(Schema $schema): Schema
    {
        $groups = PermissionSeeder::groups();

        $cards = [];
        $gruposAsignados = [];

        foreach (self::MODULOS as $modulo => $config) {
            $subSchemas = [];
            $totalPermisos = 0;

            foreach ($config['grupos'] as $groupLabel) {
                if (! isset($groups[$groupLabel])) {
                    continue;
                }
                $gruposAsignados[] = $groupLabel;
                // Un solo grupo que se llama igual que el módulo no aporta un
                // nivel: se muestra sin encabezado y ya desplegado.
                $redundante = $groupLabel === $modulo && count($config['grupos']) === 1;
                $subSchemas[] = static::checkboxDeGrupo($groupLabel, $groups[$groupLabel], $redundante);
                $totalPermisos += count($groups[$groupLabel]);
            }

            if (! $subSchemas) {
                continue;
            }

            array_unshift($subSchemas, static::barraMarcarTodo('todo el módulo'));

            $cards[] = Section::make($modulo)
                ->icon($config['icono'])
                ->description("{$totalPermisos} permisos · clic para configurar")
                ->compact()
                ->extraAttributes(['class' => 'permisos-card', 'data-card-modulo' => $modulo])
                ->columnSpan(1)
                ->schema($subSchemas);
        }

        // Grupos nuevos del seeder que aún no están mapeados a un módulo
        $sueltos = array_diff_key($groups, array_flip($gruposAsignados));
        if ($sueltos) {
            $cards[] = Section::make('Otros')
                ->icon('heroicon-o-squares-plus')
                ->compact()
                ->extraAttributes(['class' => 'permisos-card', 'data-card-modulo' => 'Otros'])
                ->columnSpan(1)
                ->schema(array_map(
                    fn ($label) => static::checkboxDeGrupo($label, $sueltos[$label]),
                    array_keys($sueltos),
                ));
        }

        return $schema->components([
            Section::make('Información del rol')->schema([
                TextInput::make('nombre')
                    ->label('Nombre del rol')
                    ->required()
                    ->maxLength(255),
            ]),

            View::make('filament.roles.buscador-permisos'),

            Grid::make(['default' => 1, 'md' => 2, 'xl' => 3])
                ->schema($cards),
        ])->columns(1);
    }

    /**
     * Card plegable de un submódulo (dentro del modal del módulo).
     *
     * Con $plano se omiten encabezado, contador y plegado: se usa cuando el
     * grupo se llama igual que el módulo y repetir el título no aporta nada.
     * El contenedor se mantiene igual porque el buscador identifica los grupos
     * por su atributo data-grupo-permisos.
     */
    private static function checkboxDeGrupo(string $groupLabel, array $permissions, bool $plano = false): Section
    {
        $permissionNames = array_keys($permissions);

        if ($plano) {
            return Section::make()
                ->compact()
                ->extraAttributes(['class' => 'permisos-subcard', 'data-grupo-permisos' => $groupLabel])
                ->schema([
                    CheckboxList::make(static::sanitizeKey($groupLabel))
                        ->hiddenLabel()
                        ->options(array_combine($permissionNames, $permissionNames))
                        ->descriptions($permissions)
                        ->columnSpanFull()
                        ->default(fn () => $permissionNames),
                ]);
        }

        return Section::make($groupLabel)
            ->description(count($permissionNames) . ' permisos')
            ->collapsible()
            ->collapsed()
            ->compact()
            ->extraAttributes(['class' => 'permisos-subcard', 'data-grupo-permisos' => $groupLabel])
            ->schema([
                static::barraMarcarTodo('este grupo'),

                CheckboxList::make(static::sanitizeKey($groupLabel))
                    ->hiddenLabel()
                    ->options(array_combine($permissionNames, $permissionNames))
                    ->descriptions($permissions)
                    ->columnSpanFull()
                    ->default(fn () => $permissionNames),
            ]);
    }

    /**
     * Barra con "Marcar todos" y "Desmarcar todos".
     *
     * Los botones actúan sobre los checkboxes del contenedor donde se
     * renderizan: dentro de un grupo afectan solo a ese grupo, y al principio
     * de la card afectan a todo el módulo.
     *
     * Son botones de navegador a propósito, no acciones de Filament: una
     * acción haría una petición a Livewire, el schema se re-renderizaría y la
     * card perdería la clase .abierta que la mantiene abierta como modal. Al
     * marcar los checkboxes en el DOM y emitir el evento change, wire:model se
     * entera igual y el modal no se mueve.
     */
    private static function barraMarcarTodo(string $ambito): Html
    {
        return Html::make(<<<HTML
            <div class="permisos-barra-toggle">
                <span class="permisos-barra-ambito">Aplicar a {$ambito}:</span>
                <button type="button" class="permisos-btn permisos-btn-marcar"
                        onclick="window.marcarPermisos(this, true)">
                    Marcar todos
                </button>
                <button type="button" class="permisos-btn permisos-btn-desmarcar"
                        onclick="window.marcarPermisos(this, false)">
                    Desmarcar todos
                </button>
            </div>
            HTML)->columnSpanFull();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Rol')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('permissions_count')
                    ->label('Permisos')
                    ->counts('permissions')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('users_count')
                    ->label('Usuarios')
                    ->counts('users')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('rol_id')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}

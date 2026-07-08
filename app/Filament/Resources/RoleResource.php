<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Rol;
use BackedEnum;
use Database\Seeders\PermissionSeeder;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'roles.ver';

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

    public static function form(Schema $schema): Schema
    {
        $groups = PermissionSeeder::groups();

        $components = [
            Section::make('Información del rol')->columns(2)->schema([
                TextInput::make('nombre')
                    ->label('Nombre del rol')
                    ->required()
                    ->maxLength(255),
                TextInput::make('guard_name')
                    ->label('Guard')
                    ->default('web')
                    ->required()
                    ->maxLength(255),
            ]),
        ];

        foreach ($groups as $groupLabel => $permissions) {
            $permissionNames = array_keys($permissions);
            $fieldKey = static::sanitizeKey($groupLabel);

            $components[] = Section::make($groupLabel)
                ->compact()
                ->schema([
                    CheckboxList::make($fieldKey)
                        ->label('')
                        ->options(array_combine($permissionNames, $permissionNames))
                        ->descriptions($permissions)
                        ->columns(3)
                        ->columnSpanFull()
                        ->bulkToggleable()
                        ->default(fn () => $permissionNames),
                ]);
        }

        return $schema->components($components);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Rol')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('info'),
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

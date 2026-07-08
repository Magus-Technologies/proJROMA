<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Permisos';
    protected static string|\UnitEnum|null $navigationGroup  = 'Administración';
    protected static ?int    $navigationSort  = 3;
    protected static ?string $label           = 'Permiso';
    protected static ?string $pluralLabel     = 'Permisos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detalle del permiso')->columns(2)->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Select::make('guard_name')
                    ->label('Guard')
                    ->options(['web' => 'web', 'api' => 'api'])
                    ->default('web')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Permiso')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('info'),
                TextColumn::make('roles_count')
                    ->label('Roles')
                    ->counts('roles')
                    ->badge()
                    ->color('warning'),
            ])
            ->defaultSort('name')
            ->actions([
                Action::make('verRoles')
                    ->label('Ver roles')
                    ->icon('heroicon-o-user-group')
                    ->color('info')
                    ->modalHeading(fn (Permission $record): string => "Roles con el permiso: {$record->name}")
                    ->modalContent(fn (Permission $record) => view('filament.modals.permiso-roles', [
                        'roles' => $record->roles()->orderBy('nombre')->get(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
            ]);
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
        ];
    }
}

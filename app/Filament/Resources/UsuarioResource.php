<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuarioResource\Pages;
use App\Models\Rol;
use App\Models\User;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UsuarioResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static string|\UnitEnum|null $navigationGroup  = 'Administración';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $label           = 'Usuario';
    protected static ?string $pluralLabel     = 'Usuarios';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos personales')->columns(2)->schema([
                TextInput::make('num_doc')
                    ->label('DNI / Doc.')
                    ->maxLength(20)
                    ->suffixAction(
                        Action::make('consultar_doc')
                            ->icon('heroicon-m-magnifying-glass')
                            ->tooltip('Consultar SUNAT / RENIEC')
                            ->action(function ($state, $set) {
                                $doc   = trim($state ?? '');
                                $len   = strlen($doc);
                                $url   = config('apisperu.url');
                                $token = config('apisperu.token');

                                if (!in_array($len, [8, 11])) {
                                    Notification::make()->warning()->title('Ingresá 8 dígitos (DNI) o 11 dígitos (RUC).')->send();
                                    return;
                                }

                                try {
                                    if ($len === 8) {
                                        $data = Http::timeout(8)->get("{$url}/dni/{$doc}", ['token' => $token])->json();
                                        $nombres = trim(($data['nombres'] ?? ''));
                                        $apellidos = trim(implode(' ', array_filter([
                                            $data['apellidoPaterno'] ?? '',
                                            $data['apellidoMaterno'] ?? '',
                                        ])));
                                        if (!$nombres && !$apellidos) {
                                            Notification::make()->warning()->title($data['message'] ?? 'DNI no encontrado.')->send();
                                            return;
                                        }
                                        $set('nombres', $nombres);
                                        $set('apellidos', $apellidos);
                                        Notification::make()->success()->title('Datos cargados desde RENIEC')->send();
                                    } else {
                                        $data = Http::timeout(8)->get("{$url}/ruc/{$doc}", ['token' => $token])->json();
                                        if (empty($data['razonSocial'])) {
                                            Notification::make()->warning()->title('RUC no encontrado.')->send();
                                            return;
                                        }
                                        $set('nombres', $data['razonSocial']);
                                        Notification::make()->success()->title('Datos cargados desde SUNAT')->send();
                                    }
                                } catch (\Throwable) {
                                    Notification::make()->warning()->title('Error al consultar. Intentá de nuevo.')->send();
                                }
                            })
                    )
                    ->columnSpanFull(),
                TextInput::make('nombres')
                    ->label('Nombres')->required()->maxLength(100),
                TextInput::make('apellidos')
                    ->label('Apellidos')->required()->maxLength(100),
                TextInput::make('telefono')
                    ->label('Teléfono')->maxLength(20),
            ]),

            Section::make('Acceso')->columns(2)->schema([
                TextInput::make('usuario')
                    ->label('Usuario')->required()->maxLength(60),
                TextInput::make('email')
                    ->label('Email')->email()->maxLength(120),
                TextInput::make('clave')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->minLength(6)
                    ->maxLength(60)
                    ->required(fn (string $operation) => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->helperText(fn (string $operation) => $operation === 'edit' ? 'Dejá en blanco para no cambiarla.' : null),
                Select::make('id_rol')
                    ->label('Rol')
                    ->options(fn () => Rol::pluck('nombre', 'rol_id'))
                    ->required()
                    ->searchable(),
            ]),

            Section::make('Foto de perfil')->schema([
                FileUpload::make('foto')
                    ->label('Foto')
                    ->image()
                    ->disk('public')
                    ->directory('usuarios/fotos')
                    ->imagePreviewHeight('100')
                    ->circleCropper()
                    ->maxSize(2048)
                    ->columnSpanFull(),
            ]),

            Section::make('Estado')->columns(2)->schema([
                Toggle::make('estado')
                    ->label('Activo')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true)
                    ->formatStateUsing(fn ($state) => $state === '1' || $state === true)
                    ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),
                Toggle::make('available_status')
                    ->label('Disponible')
                    ->onColor('success')
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(fn (User $record) => 'https://ui-avatars.com/api/?name='.urlencode($record->nombre_completo).'&color=ffffff&background=3b82f6&size=36'),
                TextColumn::make('nombre_completo')
                    ->label('Nombre')
                    ->searchable(query: fn ($q, $s) =>
                        $q->where('nombres', 'like', "%{$s}%")
                          ->orWhere('apellidos', 'like', "%{$s}%")
                    )
                    ->sortable(query: fn ($q, $d) => $q->orderBy('nombres', $d)),
                TextColumn::make('usuario')
                    ->label('Usuario')->searchable()->sortable(),
                TextColumn::make('email')
                    ->label('Email')->searchable()->toggleable(),
                TextColumn::make('rol.nombre')
                    ->label('Rol')->sortable()->badge()->color('info'),
                IconColumn::make('estado')
                    ->label('Activo')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->estado === '1'),
                IconColumn::make('available_status')
                    ->label('Disponible')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_rol')
                    ->label('Rol')
                    ->options(fn () => Rol::pluck('nombre', 'rol_id')),
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(['1' => 'Activo', '0' => 'Inactivo']),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])->tooltip('Acciones'),
            ])
            ->defaultSort('nombres');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsuarios::route('/'),
            'edit'  => Pages\EditUsuario::route('/{record}/edit'),
        ];
    }
}

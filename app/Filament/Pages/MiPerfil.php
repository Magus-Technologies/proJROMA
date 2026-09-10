<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Mi Perfil: los datos del propio usuario y su contraseña, nada más.
 *
 * Cada uno edita únicamente su ficha — no hay forma de llegar a la de otro —
 * y ve su rol y sus permisos en modo lectura, para saber qué puede hacer.
 *
 * Cuando el usuario todavía tiene la clave inicial que le puso el
 * administrador, la pantalla se reduce al cambio de contraseña y se muestra
 * sin barra lateral: es lo único que puede hacer hasta cambiarla.
 *
 * @property-read Schema $form
 */
class MiPerfil extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $title = 'Mi Perfil';

    protected static ?string $slug = 'mi-perfil';

    /** Se llega por el menú de usuario, no por la navegación lateral. */
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament-panels::pages.simple';

    /** @var array<string, mixed> */
    public ?array $data = [];

    /**
     * A diferencia del resto del panel no exige ningún permiso: si lo
     * exigiera, un usuario con clave inicial y sin permisos quedaría
     * encerrado sin poder cambiarla.
     */
    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function getLayout(): string
    {
        // Con el cambio pendiente no debe verse ni el menú lateral.
        return $this->debeCambiar()
            ? 'filament-panels::components.layout.simple'
            : 'filament-panels::components.layout.index';
    }

    public function getView(): string
    {
        return $this->debeCambiar()
            ? 'filament-panels::pages.simple'
            : 'filament.pages.mi-perfil';
    }

    /** @return array<string, mixed> */
    protected function getLayoutData(): array
    {
        return $this->debeCambiar()
            ? [
                'hasTopbar' => true,
                'maxContentWidth' => \Filament\Support\Enums\Width::Large,
                'maxWidth' => \Filament\Support\Enums\Width::Large,
            ]
            : parent::getLayoutData();
    }

    /** La vista "simple" muestra el logo de la marca arriba de la tarjeta. */
    public function hasLogo(): bool
    {
        return true;
    }

    public function mount(): void
    {
        $usuario = $this->usuario();

        $this->form->fill([
            'nombres'   => $usuario->nombres,
            'apellidos' => $usuario->apellidos,
            'email'     => $usuario->email,
            'telefono'  => $usuario->telefono,
            'foto'      => $usuario->foto,
        ]);
    }

    public function getHeading(): string
    {
        return $this->debeCambiar() ? 'Cambiá tu contraseña' : 'Mi Perfil';
    }

    public function getSubheading(): ?string
    {
        return $this->debeCambiar()
            ? 'Entraste con la contraseña que te dio el administrador. Elegí una nueva para seguir usando el sistema.'
            : null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Mis datos')
                    ->description('Solo vos podés cambiarlos. El usuario de acceso y el rol los administra tu jefatura.')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->visible(fn (): bool => ! $this->debeCambiar())
                    ->schema([
                        TextInput::make('nombres')
                            ->label('Nombres')
                            ->required()
                            ->maxLength(120),
                        TextInput::make('apellidos')
                            ->label('Apellidos')
                            ->maxLength(120),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(120)
                            ->rule(fn (): Closure => function (string $attribute, $value, Closure $fail): void {
                                if (blank($value) || $value === $this->usuario()->email) {
                                    return;
                                }

                                $existe = User::where('email', $value)
                                    ->where('usuario_id', '<>', $this->usuario()->usuario_id)
                                    ->exists();

                                if ($existe) {
                                    $fail('Ya existe un usuario con ese correo.');
                                }
                            }),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(30),
                        FileUpload::make('foto')
                            ->label('Foto de perfil')
                            ->image()
                            ->disk('public')
                            ->directory('usuarios/fotos')
                            ->imagePreviewHeight('100')
                            ->circleCropper()
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),

                Section::make('Contraseña')
                    ->description(fn (): string => $this->debeCambiar()
                        ? 'Elegí una contraseña que solo conozcas vos.'
                        : 'Dejá los campos en blanco si no querés cambiarla.')
                    ->icon('heroicon-o-key')
                    ->columns(2)
                    ->schema([
                        TextInput::make('clave_actual')
                            ->label($this->debeCambiar() ? 'Contraseña con la que entraste' : 'Contraseña actual')
                            ->password()
                            ->revealable()
                            ->currentPassword()
                            ->required(fn (): bool => $this->debeCambiar())
                            ->requiredWith('clave_nueva')
                            ->columnSpanFull(),
                        TextInput::make('clave_nueva')
                            ->label('Contraseña nueva')
                            ->password()
                            ->revealable()
                            ->required(fn (): bool => $this->debeCambiar())
                            ->rule(Password::min(8))
                            ->maxLength(60)
                            ->different('clave_actual')
                            ->validationMessages([
                                'different' => 'La contraseña nueva tiene que ser distinta de la actual.',
                            ])
                            ->confirmed()
                            ->helperText('Mínimo 8 caracteres.'),
                        TextInput::make('clave_nueva_confirmation')
                            ->label('Repetí la contraseña nueva')
                            ->password()
                            ->revealable()
                            ->required(fn (): bool => $this->debeCambiar())
                            ->requiredWith('clave_nueva')
                            ->dehydrated(false),
                    ]),

                Section::make('Tu rol y tus permisos')
                    ->description('Es lo que podés hacer dentro del sistema. Si necesitás algo más, pedíselo a tu jefatura.')
                    ->icon('heroicon-o-shield-check')
                    ->collapsed()
                    ->visible(fn (): bool => ! $this->debeCambiar())
                    ->schema([
                        Text::make(fn (): string => 'Rol: ' . ($this->usuario()->rol?->nombre ?? 'sin rol asignado')),
                        Text::make(fn (): string => $this->permisosLegibles()),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('guardar')
                ->footer([
                    Actions::make($this->getFormActions())
                        ->fullWidth($this->debeCambiar())
                        ->key('form-actions'),
                ]),
        ]);
    }

    /** @return array<Action> */
    protected function getFormActions(): array
    {
        return [
            Action::make('guardar')
                ->label($this->debeCambiar() ? 'Guardar contraseña' : 'Guardar cambios')
                ->submit('guardar'),
        ];
    }

    public function guardar(): void
    {
        $datos = $this->form->getState();
        $usuario = $this->usuario();

        if (! $this->debeCambiar()) {
            $usuario->fill([
                'nombres'   => $datos['nombres'],
                'apellidos' => $datos['apellidos'] ?? null,
                'email'     => $datos['email'] ?? null,
                'telefono'  => $datos['telefono'] ?? null,
                'foto'      => $datos['foto'] ?? null,
            ]);
        }

        $cambioClave = filled($datos['clave_nueva'] ?? null);

        if ($cambioClave) {
            $usuario->forceFill([
                'clave'              => Hash::make($datos['clave_nueva']),
                'debe_cambiar_clave' => false,
            ]);
        }

        $usuario->save();

        if ($cambioClave && request()->hasSession()) {
            // La sesión guarda el hash de la contraseña: sin esto,
            // AuthenticateSession expulsa a quien acaba de cambiarla.
            request()->session()->put([
                'password_hash_' . auth()->getDefaultDriver() => $usuario->getAuthPassword(),
            ]);
        }

        $volviaDelCambioObligado = $cambioClave && $this->debeCambiarOriginal;

        $this->data['clave_actual'] = null;
        $this->data['clave_nueva'] = null;
        $this->data['clave_nueva_confirmation'] = null;

        Notification::make()
            ->success()
            ->title($cambioClave ? 'Contraseña actualizada' : 'Perfil actualizado')
            ->send();

        if ($volviaDelCambioObligado) {
            $this->redirect(filament()->getUrl(), navigate: false);
        }
    }

    /** Se resuelve una vez por petición: al guardar la marca ya se apagó. */
    protected ?bool $debeCambiarOriginal = null;

    protected function debeCambiar(): bool
    {
        return $this->debeCambiarOriginal ??= (bool) auth()->user()?->debe_cambiar_clave;
    }

    protected function usuario(): User
    {
        /** @var User */
        return auth()->user();
    }

    protected function permisosLegibles(): string
    {
        $usuario = $this->usuario();

        if ($usuario->esAdmin()) {
            return 'Como administrador tenés acceso a todo el sistema.';
        }

        $permisos = $usuario->rol?->permissions()->orderBy('name')->pluck('description', 'name');

        if (blank($permisos)) {
            return 'Tu rol todavía no tiene permisos asignados.';
        }

        return $permisos
            ->map(fn (?string $descripcion, string $nombre): string => $descripcion ?: $nombre)
            ->implode(' · ');
    }
}

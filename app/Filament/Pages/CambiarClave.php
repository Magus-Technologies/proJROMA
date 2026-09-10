<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Cambio de contraseña.
 *
 * Es una pantalla suelta (sin menú ni barra lateral) por dos motivos:
 *  - al usuario con clave inicial se lo obliga a pasar por acá y no debe
 *    tener a la vista ningún otro módulo;
 *  - cualquiera puede entrar por su propia voluntad desde el menú de usuario.
 *
 * @property-read Schema $form
 */
class CambiarClave extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $title = 'Cambiar contraseña';

    protected static ?string $slug = 'cambiar-clave';

    // Layout "simple": tarjeta centrada, sin menú lateral. Solo queda arriba el
    // menú de usuario, que es lo que permite cerrar sesión.
    protected static string $layout = 'filament-panels::components.layout.simple';

    protected string $view = 'filament-panels::pages.simple';

    /** Se llega por el menú de usuario o forzado, nunca desde la navegación. */
    protected static bool $shouldRegisterNavigation = false;

    /** @var array<string, mixed> */
    public ?array $data = [];

    /**
     * A diferencia del resto del panel, esta pantalla no exige ningún permiso:
     * si la exigiera, un usuario con clave inicial y sin permisos quedaría
     * encerrado sin poder cambiarla.
     */
    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    /** @return array<string, mixed> */
    protected function getLayoutData(): array
    {
        return [
            'hasTopbar' => true,
            'maxContentWidth' => \Filament\Support\Enums\Width::Large,
            'maxWidth' => \Filament\Support\Enums\Width::Large,
        ];
    }

    /** La vista "simple" muestra el logo de la marca arriba de la tarjeta. */
    public function hasLogo(): bool
    {
        return true;
    }

    public function getHeading(): string
    {
        return $this->debeCambiar() ? 'Cambiá tu contraseña' : 'Cambiar contraseña';
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
                TextInput::make('clave_actual')
                    ->label($this->debeCambiar() ? 'Contraseña con la que entraste' : 'Contraseña actual')
                    ->password()
                    ->revealable()
                    ->required()
                    ->currentPassword(),

                TextInput::make('clave_nueva')
                    ->label('Contraseña nueva')
                    ->password()
                    ->revealable()
                    ->required()
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
                    ->required()
                    ->dehydrated(false),
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
                        ->fullWidth()
                        ->key('form-actions'),
                ]),
        ]);
    }

    /** @return array<Action> */
    protected function getFormActions(): array
    {
        return [
            Action::make('guardar')
                ->label('Guardar contraseña')
                ->submit('guardar'),
        ];
    }

    public function guardar(): void
    {
        $datos = $this->form->getState();

        /** @var User $usuario */
        $usuario = auth()->user();

        $usuario->forceFill([
            'clave'              => Hash::make($datos['clave_nueva']),
            'debe_cambiar_clave' => false,
        ])->save();

        // La sesión guarda el hash de la contraseña: sin esto, AuthenticateSession
        // detecta el cambio y expulsa al usuario que acaba de cambiarla.
        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_' . auth()->getDefaultDriver() => $usuario->getAuthPassword(),
            ]);
        }

        $this->form->fill();

        Notification::make()
            ->success()
            ->title('Contraseña actualizada')
            ->body('Usá la nueva la próxima vez que inicies sesión.')
            ->send();

        $this->redirect(filament()->getUrl(), navigate: false);
    }

    protected function debeCambiar(): bool
    {
        return (bool) auth()->user()?->debe_cambiar_clave;
    }
}

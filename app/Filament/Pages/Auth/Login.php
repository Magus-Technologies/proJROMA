<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class Login extends BaseLogin
{
    protected static string $layout = 'filament.components.layout.simple-two-col';

    protected string $view = 'filament.pages.auth.login';

    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        // La empresa y la sucursal las deja en sesión el listener del evento
        // Login (App\Listeners\EstablecerEmpresaEnSesion), que también cubre el
        // acceso por cookie de "Recordarme". Aquí solo se comprueba el
        // resultado para poder dar un mensaje entendible.
        //
        // Sin esta comprobación el usuario entraba igual con la sesión vacía:
        // session('id_empresa') daba null, todo el panel filtraba por
        // id_empresa = 0 y se veía sin datos, mientras lo que creara nacía
        // huérfano e invisible para el resto de la empresa.
        if ($response !== null && ! session()->has('id_empresa')) {
            $user = auth()->user();

            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            throw ValidationException::withMessages([
                'data.login' => $user?->id_empresa
                    ? 'Tu empresa está inactiva. Contacta al administrador.'
                    : 'Tu usuario no tiene una empresa asignada. Contacta al administrador.',
            ]);
        }

        return $response;
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Usuario o Email')
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        return [
            'email' => $data['login'],
            'usuario' => $data['login'],
            'password' => $data['password'],
        ];
    }
}

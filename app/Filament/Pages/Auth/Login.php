<?php

namespace App\Filament\Pages\Auth;

use App\Models\Empresa;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use SensitiveParameter;

class Login extends BaseLogin
{
    protected static string $layout = 'filament.components.layout.simple-two-col';

    protected string $view = 'filament.pages.auth.login';

    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        if ($response !== null) {
            $user    = auth()->user();
            $empresa = Empresa::where('id_empresa', $user->id_empresa)
                ->where('estado', '1')
                ->first();

            if ($empresa) {
                session()->put([
                    'id_empresa'     => (int) $empresa->id_empresa,
                    'sucursal'       => (int) ($user->sucursal ?: 1),
                    'nombre_empresa' => $empresa->razon_social,
                    'logo_empresa'   => $empresa->logo,
                    'ruc_empr'       => $empresa->ruc,
                    'last_activity'  => time(),
                ]);
            }
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

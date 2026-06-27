<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class Sha1UserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (empty($credentials)) return null;

        $query = $this->newModelQuery();

        foreach (['password', 'clave', '_token'] as $exclude) {
            unset($credentials[$exclude]);
        }

        if (isset($credentials['email']) && isset($credentials['usuario'])) {
            $email = $credentials['email'];
            $usuario = $credentials['usuario'];
            unset($credentials['email'], $credentials['usuario']);
            $query->where(function (Builder $q) use ($email, $usuario) {
                $q->where('email', $email)->orWhere('usuario', $usuario);
            });
        }

        foreach ($credentials as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $plain = $credentials['password'] ?? '';

        if (Hash::check($plain, $user->getAuthPassword())) {
            $this->maybeMigrateHash($user, $plain);
            return true;
        }

        if (strlen($user->getAuthPassword()) === 40 && sha1($plain) === $user->getAuthPassword()) {
            $this->maybeMigrateHash($user, $plain);
            return true;
        }

        return false;
    }

    private function maybeMigrateHash(Authenticatable $user, string $plain): void
    {
        if (strlen($user->getAuthPassword()) === 40) {
            $user->forceFill(['clave' => Hash::make($plain)])->save();
        }
    }
}

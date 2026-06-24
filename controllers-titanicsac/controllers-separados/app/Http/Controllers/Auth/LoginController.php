<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresa;
use App\Models\RutaVendedor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'user'     => ['required', 'string', 'max:200'],
            'clave'    => ['required', 'string', 'min:4', 'max:100'],
            'sucursal' => ['required', 'integer', 'min:1'],
        ]);

        $this->verificarRateLimit($request);

        $usuario = User::where(function ($q) use ($request) {
            $q->where('email',   $request->user)
              ->orWhere('usuario', $request->user);
        })->first();

        if (! $usuario) {
            $this->incrementarRateLimit($request);
            return response()->json(['res' => false, 'msg' => 'Usuario no encontrado.'], 401);
        }

        if (! $this->verificarClave($request->clave, $usuario->clave)) {
            $this->incrementarRateLimit($request);
            return response()->json(['res' => false, 'msg' => 'Contraseña incorrecta.'], 401);
        }

        if ($usuario->estado != '1') {
            return response()->json(['res' => false, 'msg' => 'Tu cuenta está bloqueada.'], 403);
        }

        $empresa = Empresa::where('id_empresa', $usuario->id_empresa)
            ->where('estado', '1')->first();

        if (! $empresa) {
            return response()->json(['res' => false, 'msg' => 'Empresa no disponible.'], 403);
        }

        // Migrar sha1 → bcrypt automáticamente
        if (strlen($usuario->clave) === 40) {
            $usuario->update(['clave' => Hash::make($request->clave)]);
        }

        Auth::login($usuario, remember: true);
        $request->session()->regenerate();

        $rutas = RutaVendedor::where('id_usuario', $usuario->usuario_id)
            ->pluck('id_ruta')->toArray();

        $request->session()->put([
            'id_empresa'     => (int) $empresa->id_empresa,
            'sucursal'       => (int) $request->sucursal,
            'nombre_empresa' => $empresa->razon_social,
            'logo_empresa'   => $empresa->logo,
            'ruc_empr'       => $empresa->ruc,
            'rutas_vendedor' => $rutas,
            'last_activity'  => time(),
        ]);

        RateLimiter::clear($this->throttleKey($request));

        return response()->json(['res' => true, 'ruta' => route('dashboard')]);
    }

    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function verificarClave(string $input, string $hash): bool
    {
        if (Hash::check($input, $hash)) return true;
        if (sha1($input) === $hash)     return true;
        return false;
    }

    private function verificarRateLimit(Request $request): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            $seg = RateLimiter::availableIn($this->throttleKey($request));
            throw ValidationException::withMessages([
                'user' => "Demasiados intentos. Espera {$seg} segundos.",
            ]);
        }
    }

    private function incrementarRateLimit(Request $request): void
    {
        RateLimiter::hit($this->throttleKey($request), 60);
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('user', '')) . '|' . $request->ip();
    }
}

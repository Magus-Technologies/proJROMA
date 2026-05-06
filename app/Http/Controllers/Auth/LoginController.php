<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresa;
use App\Models\RutaVendedor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // ── Laravel 13: #[Middleware] en método específico ────────────────────
    #[Middleware('guest')]
    public function showLogin(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    /**
     * Procesa el login con seguridad completa.
     * Rate limiting: 5 intentos por 60s por IP+usuario.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'user'     => ['required', 'string', 'max:200'],
            'clave'    => ['required', 'string', 'min:4', 'max:100'],
            'sucursal' => ['required', 'integer', 'min:1'],
        ], [
            'user.required'     => 'El usuario es obligatorio.',
            'clave.required'    => 'La contraseña es obligatoria.',
            'sucursal.required' => 'Selecciona una sucursal.',
        ]);

        // ── Rate limiting ─────────────────────────────────────────────────
        $this->verificarRateLimit($request);

        // ── Buscar usuario ────────────────────────────────────────────────
        $usuario = User::where(function ($q) use ($request) {
                $q->where('email',   $request->user)
                  ->orWhere('usuario', $request->user);
            })->first();

        if (! $usuario) {
            $this->incrementarRateLimit($request);
            return response()->json(['res' => false, 'msg' => 'Usuario no encontrado.'], 401);
        }

        // ── Verificar contraseña (sha1 legacy + bcrypt nuevo) ─────────────
        if (! $this->verificarClave($request->clave, $usuario->clave)) {
            $this->incrementarRateLimit($request);
            return response()->json(['res' => false, 'msg' => 'Contraseña incorrecta.'], 401);
        }

        // ── Verificar estado de cuenta ────────────────────────────────────
        if ($usuario->estado != '1' || ! $usuario->available_status) {
            return response()->json(['res' => false, 'msg' => 'Tu cuenta está bloqueada. Contacta al administrador.'], 403);
        }

        // ── Verificar sucursal (excepto admin y rotativos) ─────────────────
        if ($usuario->id_rol !== 1 && ! $usuario->rotativo && (int)$usuario->sucursal !== (int)$request->sucursal) {
            return response()->json([
                'res' => false,
                'msg' => "Sucursal incorrecta. Tu sucursal asignada es la N° {$usuario->sucursal}.",
            ], 403);
        }

        // ── Verificar empresa activa ───────────────────────────────────────
        $empresa = Empresa::where('id_empresa', $usuario->id_empresa)
            ->where('estado', '1')
            ->first();

        if (! $empresa) {
            return response()->json(['res' => false, 'msg' => 'Empresa no disponible.'], 403);
        }

        // ── Migrar sha1 → bcrypt silenciosamente ───────────────────────────
        if (strlen($usuario->clave) === 40) {
            $usuario->update(['clave' => Hash::make($request->clave)]);
        }

        // ── Autenticar y crear sesión ─────────────────────────────────────
        Auth::login($usuario, remember: true);
        $request->session()->regenerate();

        $rutas = RutaVendedor::where('id_usuario', $usuario->usuario_id)
            ->pluck('id_ruta')
            ->toArray();

        $request->session()->put([
            'id_empresa'     => (int) $empresa->id_empresa,
            'sucursal'       => (int) $request->sucursal,
            'nombre_empresa' => $empresa->razon_social,
            'logo_empresa'   => $empresa->logo,
            'ruc_empr'       => $empresa->ruc,
            'rutas_vendedor' => $rutas,
            'last_activity'  => time(),
        ]);

        // ── Limpiar rate limiter ──────────────────────────────────────────
        RateLimiter::clear($this->throttleKey($request));

        return response()->json([
            'res'  => true,
            'ruta' => route('dashboard'),
        ]);
    }

    /**
     * Cierra sesión correctamente.
     */
    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Sesión cerrada correctamente.');
    }

    // ── Helpers privados ──────────────────────────────────────────────────

    private function verificarClave(string $input, string $hash): bool
    {
        // Bcrypt moderno
        if (Hash::check($input, $hash)) return true;
        // SHA1 legacy (sistema original)
        if (sha1($input) === $hash) return true;
        return false;
    }

    private function verificarRateLimit(Request $request): void
    {
        $max     = (int) config('app.login_max_attempts', 5);
        $decay   = (int) config('app.login_decay_seconds', 60);
        $key     = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, $max)) {
            $segundos = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'user' => "Demasiados intentos fallidos. Intenta de nuevo en {$segundos} segundos.",
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

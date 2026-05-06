<?php
// ── CheckEmpresa.php ──────────────────────────────────────────────────────────

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmpresa
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('id_empresa')) {
            auth()->logout();
            $request->session()->invalidate();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesión inválida.'], 401);
            }

            return redirect()->route('login')
                ->withErrors(['msg' => 'Sesión inválida. Por favor inicia sesión nuevamente.']);
        }

        return $next($request);
    }
}

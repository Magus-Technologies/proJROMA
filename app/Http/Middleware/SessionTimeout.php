<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    // 8 horas de inactividad
    private const TIMEOUT = 480 * 60;

    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $lastActivity = $request->session()->get('last_activity', time());

            if ((time() - $lastActivity) > self::TIMEOUT) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Sesión expirada por inactividad.'], 401);
                }

                return redirect()->route('login')
                    ->withErrors(['msg' => 'Tu sesión expiró por inactividad (8 horas). Vuelve a ingresar.']);
            }

            $request->session()->put('last_activity', time());
        }

        return $next($request);
    }
}

<?php

use App\Http\Middleware\CheckEmpresa;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SessionTimeout;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        api:      __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Prepend: security headers en todas las respuestas ────────────
        $middleware->web(prepend: [
            SecurityHeaders::class,
        ]);

        // ── Rate limiter para login (Laravel 13: throttle con redis/db) ──
        $middleware->throttleWithRedis();

        // ── Alias de middlewares ─────────────────────────────────────────
        $middleware->alias([
            'check.empresa'   => CheckEmpresa::class,
            'session.timeout' => SessionTimeout::class,
            'role'            => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'      => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // ── Excluir CSRF para rutas API ──────────────────────────────────
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // ── Trusted proxies (para VPS detrás de nginx) ───────────────────
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Sin autenticación → JSON o redirect
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autenticado.', 'code' => 401], 401);
            }
            return redirect()->route('login')->withErrors(['msg' => 'Tu sesión ha expirado.']);
        });

        // Sin permiso
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sin permiso para esta acción.'], 403);
            }
            abort(403, 'No tienes permiso para realizar esta acción.');
        });

        // Model not found → 404 limpio
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Registro no encontrado.'], 404);
            }
        });

        // Validation errors → JSON con mensajes
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error de validación.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Throttle (demasiados intentos)
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Demasiadas solicitudes. Espera un momento.'], 429);
            }
        });
    })
    ->create();

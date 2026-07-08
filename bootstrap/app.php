<?php

use App\Http\Middleware\CheckEmpresa;
use App\Http\Middleware\CheckPermission;
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
   
   
   
    ->withProviders([
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
])
   
   
   
   
   
   
   
   
    ->withMiddleware(function (Middleware $middleware) {

               $middleware->statefulApi();     
	    $middleware->web(prepend: [
            SecurityHeaders::class,
        ]);

        // ← QUITADO throttleWithRedis() — no tienes Redis
        // Usar throttle normal en su lugar
        $middleware->alias([
            'check.empresa'      => CheckEmpresa::class,
            'check.permission'   => CheckPermission::class,
            'session.timeout'    => SessionTimeout::class,
        ]);

        $middleware->validateCsrfTokens(except: [
		'api/*',
		'login',
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }
            return redirect()->route('login');
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sin permiso.'], 403);
            }
            abort(403);
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Registro no encontrado.'], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error de validación.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Demasiadas solicitudes.'], 429);
            }
        });

        // Errores dentro del panel Filament (peticiones Livewire): en lugar de
        // la pantalla cruda de debug, mostrar un mensaje legible en español.
        // Funciona incluso con APP_DEBUG=true porque intercepta antes del renderer.
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (! $request->hasHeader('X-Livewire')) {
                return null; // dejar que el manejador normal actúe
            }

            // Estos los maneja Filament/Laravel por su cuenta (no interceptar)
            if ($e instanceof \Illuminate\Validation\ValidationException
                || $e instanceof \Illuminate\Auth\AuthenticationException
                || $e instanceof \Illuminate\Auth\Access\AuthorizationException
                || $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                || $e instanceof \Filament\Support\Exceptions\Halt) {
                return null;
            }

            $mensajeAmigable = $e instanceof \Illuminate\Database\QueryException
                ? \App\Support\DbErrorTranslator::translate($e)
                : 'Ocurrió un error inesperado.';

            return response()->view('errors.500', [
                'exception'       => $e,
                'mensajeAmigable' => $mensajeAmigable,
            ], 500);
        });
    })
    ->create();

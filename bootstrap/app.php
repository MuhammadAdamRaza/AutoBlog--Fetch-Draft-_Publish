<?php

use App\Exceptions\OpenRouterInsufficientCreditsException;
use App\Http\Middleware\UseBuiltAssetsForTunnel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: ['127.0.0.1', '::1']);
        $middleware->append(UseBuiltAssetsForTunnel::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontRetry(OpenRouterInsufficientCreditsException::class);
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
